<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\OrderDetails;
use App\Models\OrderStatus;
use App\Models\Courierapi;
use App\Models\FundTransaction;
use App\Models\VendorWallet;
use App\Models\VendorWalletTransaction;
use App\Models\SmsGateway;
use App\Models\GeneralSetting;
use App\Models\User;
use App\Services\RedXService;
use Illuminate\Support\Facades\Log;

class RedXWebhookController extends Controller
{
    /**
     * Handle RedX webhook callbacks
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function handleWebhook(Request $request)
    {
        try {
            // Log incoming webhook
            Log::info('RedX Webhook Received', [
                'payload' => $request->all(),
                'headers' => $request->headers->all()
            ]);

            // Validate required fields
            $trackingNumber = $request->input('tracking_number');
            $status = $request->input('status');
            $invoiceNumber = $request->input('invoice_number');

            if (!$trackingNumber || !$status) {
                return response()->json([
                    'success' => false,
                    'message' => 'Missing required fields: tracking_number or status'
                ], 400);
            }

            // Find order by tracking_id or invoice_id
            $order = Order::where('courier_tracking_id', $trackingNumber)
                ->orWhere('invoice_id', $invoiceNumber)
                ->first();

            if (!$order) {
                Log::warning('RedX Webhook: Order not found', [
                    'tracking_number' => $trackingNumber,
                    'invoice_number' => $invoiceNumber
                ]);
                
                return response()->json([
                    'success' => false,
                    'message' => 'Order not found'
                ], 404);
            }

            // Map RedX status to order status
            $redxService = new RedXService();
            $newOrderStatus = $redxService->mapStatusToOrderStatus($status);

            if ($newOrderStatus !== null) {
                $oldStatus = (int) $order->order_status;
                $newOrderStatus = (int) $newOrderStatus;
                
                // Update order status
                $order->order_status = $newOrderStatus;
                $order->save();

                // Handle stock change (same logic as OrderController)
                $this->handleStockChange($order, $oldStatus, $newOrderStatus);

                if ($newOrderStatus == 11) {
                    \App\Helpers\ResellerOrderHelper::deductDeliveryChargeOnCancel($order);
                }

                // If order is delivered/completed (status = 6)
                if ($newOrderStatus == 6 && $oldStatus != 6) {
                    // Add money to fund
                    FundTransaction::create([
                        'direction'  => 'in',
                        'source'     => 'sale',
                        'source_id'  => $order->id,
                        'amount'     => $order->amount,
                        'note'       => 'Order complete via RedX webhook (#' . $order->invoice_id . ')',
                        'created_by' => 1, // System user
                    ]);

                    // Credit vendors for their items
                    $this->distributeVendorEarnings($order);
                    
                    // Credit reseller wallet if this is a reseller order
                    $this->creditResellerWallet($order);
                }

                // Send SMS notification if configured
                $this->sendStatusUpdateSMS($order, $newOrderStatus);

                Log::info('RedX Webhook: Order status updated successfully', [
                    'order_id' => $order->id,
                    'invoice_id' => $order->invoice_id,
                    'tracking_id' => $trackingNumber,
                    'old_status' => $oldStatus,
                    'new_status' => $newOrderStatus,
                    'redx_status' => $status
                ]);
            } else {
                Log::warning('RedX Webhook: Status mapping not found', [
                    'order_id' => $order->id,
                    'redx_status' => $status
                ]);
            }

            return response()->json([
                'success' => true,
                'message' => 'Webhook processed successfully'
            ], 200);

        } catch (\Exception $e) {
            Log::error('RedX Webhook Error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'payload' => $request->all()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Internal server error'
            ], 500);
        }
    }

    /**
     * Handle stock change when order status changes
     * Same logic as OrderController
     */
    private function handleStockChange(Order $order, int $oldStatus, int $newStatus)
    {
        $activeStatuses = [1, 2, 3, 5, 6, 8];

        // 1) প্রথমবার active status এ ঢুকলে স্টক কমবে
        if (in_array($newStatus, $activeStatuses) && !in_array($oldStatus, $activeStatuses)) {
            $details = OrderDetails::where('order_id', $order->id)
                ->with('product:id,stock')
                ->get();

            foreach ($details as $row) {
                if ($row->product) {
                    $row->product->stock = max(0, $row->product->stock - $row->qty);
                    $row->product->save();
                }
            }
        }

        // 2) cancel (11) হলে, যদি আগেরটা active group এ থাকে -> স্টক রিস্টোর
        if ($newStatus == 11 && in_array($oldStatus, $activeStatuses)) {
            $details = OrderDetails::where('order_id', $order->id)
                ->with('product:id,stock')
                ->get();

            foreach ($details as $row) {
                if ($row->product) {
                    $row->product->stock = $row->product->stock + $row->qty;
                    $row->product->save();
                }
            }
        }
    }

    /**
     * Distribute vendor earnings when order is completed
     * Same logic as OrderController
     */
    private function distributeVendorEarnings(Order $order): void
    {
        $details = $order->orderdetails()
            ->with([
                'product:id,vendor_id,name',
                'product.vendor:id,commission_rate'
            ])
            ->get();

        foreach ($details as $item) {
            $product = $item->product;
            if (!$product || !$product->vendor_id) {
                continue;
            }

            // Skip if already processed
            if ($item->vendor_paid_at) {
                continue;
            }

            $vendorId = $product->vendor_id;
            $vendor   = $product->vendor;

            if (!$vendor) {
                Log::warning('Vendor not loaded for product: ' . $product->id);
                continue;
            }

            $commissionRate = $vendor->commission_rate ?? config('app.vendor_commission', 10);
            $lineTotal      = (float) ($item->sale_price ?? 0) * (float) ($item->qty ?? 0);

            $adminCommission = round($lineTotal * ($commissionRate / 100), 2);
            $vendorEarning   = max(0, round($lineTotal - $adminCommission, 2));

            // Update order detail record
            $item->update([
                'vendor_id'        => $vendorId,
                'commission_rate'  => $commissionRate,
                'admin_commission' => $adminCommission,
                'vendor_earning'   => $vendorEarning,
                'vendor_paid_at'   => now(),
            ]);

            // Update wallet
            $wallet = VendorWallet::firstOrCreate(['vendor_id' => $vendorId]);
            $wallet->balance       += $vendorEarning;
            $wallet->total_earned  += $vendorEarning;
            $wallet->save();

            VendorWalletTransaction::create([
                'vendor_id'   => $vendorId,
                'type'        => 'earning',
                'status'      => 'completed',
                'amount'      => $vendorEarning,
                'source_type' => 'order',
                'source_id'   => $item->id,
                'note'        => 'Order #' . $order->invoice_id . ' item earning (RedX)',
            ]);

            // Add admin commission to fund transaction
            if ($adminCommission > 0) {
                FundTransaction::create([
                    'direction'  => 'in',
                    'source'     => 'vendor_commission',
                    'source_id'  => $order->id,
                    'amount'     => $adminCommission,
                    'note'       => 'Vendor commission from Order #' . $order->invoice_id . ' - Product: ' . $item->product_name . ' (RedX)',
                    'created_by' => 1, // System user
                ]);
            }
        }
    }

    /**
     * Credit reseller wallet when order is delivered
     * Same logic as OrderController
     */
    private function creditResellerWallet(Order $order): void
    {
        // Check if this is a reseller order
        if (!$order->reseller_profit || $order->reseller_profit <= 0) {
            return;
        }

        // Check if already credited
        if ($order->reseller_wallet_credited) {
            return;
        }

        // Get reseller user
        $resellerUser = null;
        if ($order->user_id) {
            $resellerUser = User::find($order->user_id);
            if ($resellerUser && 
                ($resellerUser->hasRole('reseller') || 
                 (isset($resellerUser->role) && strtolower($resellerUser->role) === 'reseller'))) {
                // Reseller found
            } else {
                $resellerUser = null;
            }
        }

        // Fallback: Check customer email (for old orders)
        if (!$resellerUser && $order->customer && $order->customer->email) {
            $resellerUser = User::where('email', $order->customer->email)
                ->where(function($query) {
                    $query->where('role', 'reseller')
                          ->orWhereHas('roles', function($q) {
                              $q->where('name', 'reseller');
                          });
                })
                ->first();
        }

        if (!$resellerUser) {
            return;
        }

        // Credit reseller wallet
        $resellerUser->wallet_balance = ($resellerUser->wallet_balance ?? 0) + $order->reseller_profit;
        $resellerUser->save();

        \App\Models\ResellerWalletTransaction::log(
            $resellerUser->id, 'order_profit', (float) $order->reseller_profit,
            'Order', $order->id,
            'অর্ডার #' . ($order->invoice_id ?? $order->id) . ' প্রফিট'
        );

        // Mark as credited
        $order->reseller_wallet_credited = true;
        $order->save();

        Log::info('Reseller wallet credited via RedX webhook', [
            'order_id' => $order->id,
            'reseller_id' => $resellerUser->id,
            'amount' => $order->reseller_profit
        ]);
    }

    /**
     * Send SMS notification when order status changes
     */
    private function sendStatusUpdateSMS(Order $order, int $newStatus)
    {
        try {
            $sms_gateway = SmsGateway::where('status', 1)->first();
            $site_setting = GeneralSetting::first();
            $orderStatus = OrderStatus::find($newStatus);

            if ($sms_gateway && $order->customer && $orderStatus) {
                $url  = $sms_gateway->url;
                $data = [
                    "api_key"  => $sms_gateway->api_key,
                    "number"   => $order->customer->phone,
                    "type"     => 'text',
                    "senderid" => $sms_gateway->serderid,
                    "message"  => "Dear {$order->customer->name},\r\n"
                        . "Your order (Order ID: {$order->invoice_id}) status has been updated to: "
                        . "{$orderStatus->name} via RedX Courier.\r\n"
                        . "Thank you for using {$site_setting->name}!",
                ];

                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL, $url);
                curl_setopt($ch, CURLOPT_POST, 1);
                curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
                curl_exec($ch);
                curl_close($ch);
            }
        } catch (\Exception $e) {
            Log::error('RedX Webhook SMS sending failed', [
                'order_id' => $order->id,
                'error' => $e->getMessage()
            ]);
        }
    }
}
