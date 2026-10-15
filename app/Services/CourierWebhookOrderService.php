<?php

namespace App\Services;

use App\Models\FundTransaction;
use App\Models\GeneralSetting;
use App\Models\Order;
use App\Models\OrderDetails;
use App\Models\OrderStatus;
use App\Models\Payment;
use App\Models\SmsGateway;
use App\Models\User;
use App\Support\SteadfastWebhookStatus;
use App\Models\VendorWallet;
use App\Models\VendorWalletTransaction;
use Illuminate\Support\Facades\Log;

/**
 * কুরিয়ার ওয়েবহুক থেকে অর্ডার স্ট্যাটাস আপডেট — RedX / Steadfast ইত্যাদি।
 */
class CourierWebhookOrderService
{
    public function applyStatusChange(Order $order, int $newOrderStatus, string $sourceLabel = 'Courier'): bool
    {
        $oldStatus = (int) $order->order_status;
        $newOrderStatus = (int) $newOrderStatus;

        if ($oldStatus === $newOrderStatus) {
            return false;
        }

        $order->order_status = $newOrderStatus;

        if (SteadfastWebhookStatus::isCompleted($newOrderStatus)) {
            $order->payment_status = 'paid';
        } elseif (SteadfastWebhookStatus::isCancelled($newOrderStatus)) {
            $order->payment_status = 'cancelled';
        }

        $order->save();

        $this->syncPaymentRecordStatus($order, $newOrderStatus);

        $this->handleStockChange($order, $oldStatus, $newOrderStatus);

        if (SteadfastWebhookStatus::isCancelled($newOrderStatus)) {
            \App\Helpers\ResellerOrderHelper::deductDeliveryChargeOnCancel($order);
        }

        if (SteadfastWebhookStatus::isCompleted($newOrderStatus) && $oldStatus !== $newOrderStatus) {
            FundTransaction::create([
                'direction'  => 'in',
                'source'     => 'sale',
                'source_id'  => $order->id,
                'amount'     => $order->amount,
                'note'       => "Order complete via {$sourceLabel} webhook (#{$order->invoice_id})",
                'created_by' => 1,
            ]);

            $this->distributeVendorEarnings($order, $sourceLabel);
            $this->creditResellerWallet($order, $sourceLabel);
        }

        $this->sendStatusUpdateSMS($order, $newOrderStatus, $sourceLabel);

        return true;
    }

    private function syncPaymentRecordStatus(Order $order, int $orderStatusId): void
    {
        $payment = Payment::where('order_id', $order->id)->orderByDesc('id')->first();
        if (! $payment) {
            return;
        }

        if (SteadfastWebhookStatus::isCompleted($orderStatusId)) {
            $payment->payment_status = 'paid';
            if ((float) $payment->amount <= 0) {
                $payment->amount = (float) $order->amount;
            }
        } elseif (SteadfastWebhookStatus::isCancelled($orderStatusId)) {
            $payment->payment_status = 'cancelled';
        } else {
            return;
        }

        $payment->save();
    }

    public function appendCourierNote(Order $order, string $message, string $prefix = 'Steadfast'): void
    {
        $line = '['.$prefix.' '.now()->format('Y-m-d H:i').'] '.trim($message);
        $existing = trim((string) ($order->admin_note ?? ''));
        $order->admin_note = $existing !== '' ? $existing."\n".$line : $line;
        $order->save();
    }

    private function handleStockChange(Order $order, int $oldStatus, int $newStatus): void
    {
        $activeStatuses = [1, 2, 3, 5, 6, 8];

        if (in_array($newStatus, $activeStatuses, true) && ! in_array($oldStatus, $activeStatuses, true)) {
            $details = OrderDetails::where('order_id', $order->id)->with('product:id,stock')->get();
            foreach ($details as $row) {
                if ($row->product) {
                    $row->product->stock = max(0, $row->product->stock - $row->qty);
                    $row->product->save();
                }
            }
        }

        if ($newStatus === 11 && in_array($oldStatus, $activeStatuses, true)) {
            $details = OrderDetails::where('order_id', $order->id)->with('product:id,stock')->get();
            foreach ($details as $row) {
                if ($row->product) {
                    $row->product->stock = $row->product->stock + $row->qty;
                    $row->product->save();
                }
            }
        }
    }

    private function distributeVendorEarnings(Order $order, string $sourceLabel): void
    {
        $details = $order->orderdetails()
            ->with(['product:id,vendor_id,name', 'product.vendor:id,commission_rate'])
            ->get();

        foreach ($details as $item) {
            $product = $item->product;
            if (! $product || ! $product->vendor_id || $item->vendor_paid_at) {
                continue;
            }

            $vendorId = $product->vendor_id;
            $vendor   = $product->vendor;
            if (! $vendor) {
                continue;
            }

            $commissionRate  = $vendor->commission_rate ?? config('app.vendor_commission', 10);
            $lineTotal       = (float) ($item->sale_price ?? 0) * (float) ($item->qty ?? 0);
            $adminCommission = round($lineTotal * ($commissionRate / 100), 2);
            $vendorEarning   = max(0, round($lineTotal - $adminCommission, 2));

            $item->update([
                'vendor_id'        => $vendorId,
                'commission_rate'  => $commissionRate,
                'admin_commission' => $adminCommission,
                'vendor_earning'   => $vendorEarning,
                'vendor_paid_at'   => now(),
            ]);

            $wallet = VendorWallet::firstOrCreate(['vendor_id' => $vendorId]);
            $wallet->balance += $vendorEarning;
            $wallet->total_earned += $vendorEarning;
            $wallet->save();

            VendorWalletTransaction::create([
                'vendor_id'   => $vendorId,
                'type'        => 'earning',
                'status'      => 'completed',
                'amount'      => $vendorEarning,
                'source_type' => 'order',
                'source_id'   => $item->id,
                'note'        => 'Order #'.$order->invoice_id.' item earning ('.$sourceLabel.')',
            ]);

            if ($adminCommission > 0) {
                FundTransaction::create([
                    'direction'  => 'in',
                    'source'     => 'vendor_commission',
                    'source_id'  => $order->id,
                    'amount'     => $adminCommission,
                    'note'       => 'Vendor commission Order #'.$order->invoice_id.' ('.$sourceLabel.')',
                    'created_by' => 1,
                ]);
            }
        }
    }

    private function creditResellerWallet(Order $order, string $sourceLabel): void
    {
        if (! $order->reseller_profit || $order->reseller_profit <= 0 || $order->reseller_wallet_credited) {
            return;
        }

        $resellerUser = null;
        if ($order->user_id) {
            $u = User::find($order->user_id);
            if ($u && ($u->hasRole('reseller') || (isset($u->role) && strtolower($u->role) === 'reseller'))) {
                $resellerUser = $u;
            }
        }

        if (! $resellerUser && $order->customer?->email) {
            $resellerUser = User::where('email', $order->customer->email)
                ->where(function ($q) {
                    $q->where('role', 'reseller')->orWhereHas('roles', fn ($r) => $r->where('name', 'reseller'));
                })
                ->first();
        }

        if (! $resellerUser) {
            return;
        }

        $resellerUser->wallet_balance = ($resellerUser->wallet_balance ?? 0) + $order->reseller_profit;
        $resellerUser->save();

        \App\Models\ResellerWalletTransaction::log(
            $resellerUser->id,
            'order_profit',
            (float) $order->reseller_profit,
            'Order',
            $order->id,
            'অর্ডার #'.($order->invoice_id ?? $order->id).' প্রফিট'
        );

        $order->reseller_wallet_credited = true;
        $order->save();

        Log::info("Reseller wallet credited via {$sourceLabel} webhook", [
            'order_id'    => $order->id,
            'reseller_id' => $resellerUser->id,
        ]);
    }

    private function sendStatusUpdateSMS(Order $order, int $newStatus, string $sourceLabel): void
    {
        try {
            $sms_gateway  = SmsGateway::where('status', 1)->first();
            $site_setting = GeneralSetting::first();
            $orderStatus  = OrderStatus::find($newStatus);

            if (! $sms_gateway || ! $order->customer || ! $orderStatus) {
                return;
            }

            $data = [
                'api_key'  => $sms_gateway->api_key,
                'number'   => $order->customer->phone,
                'type'     => 'text',
                'senderid' => $sms_gateway->serderid,
                'message'  => "Dear {$order->customer->name},\r\n"
                    ."Your order (Order ID: {$order->invoice_id}) status: {$orderStatus->name} via {$sourceLabel}.\r\n"
                    ."Thank you — {$site_setting->name}!",
            ];

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $sms_gateway->url);
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_exec($ch);
            curl_close($ch);
        } catch (\Throwable $e) {
            Log::error("{$sourceLabel} webhook SMS failed", ['order_id' => $order->id, 'error' => $e->getMessage()]);
        }
    }
}
