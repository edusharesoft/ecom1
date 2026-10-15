<?php

namespace App\Helpers;

use App\Models\Order;
use App\Models\User;
use App\Models\ResellerWalletTransaction;
use Illuminate\Support\Facades\Log;

class ResellerOrderHelper
{
    /**
     * When reseller order is cancelled (admin/courier/Pathao/RedX/Steadfast),
     * deduct delivery charge from reseller wallet.
     */
    public static function deductDeliveryChargeOnCancel(Order $order): void
    {
        if (!$order->user_id || !$order->reseller_profit) {
            return; // Not a reseller order
        }
        if ($order->delivery_charge_deducted ?? false) {
            return; // Already deducted
        }

        $charge = (float) ($order->shipping_charge ?? 0);
        if ($charge <= 0) {
            $order->delivery_charge_deducted = true;
            $order->save();
            return;
        }

        $user = User::find($order->user_id);
        if (!$user) {
            return;
        }

        $wallet = (float) ($user->wallet_balance ?? 0);
        $deduct = min($charge, $wallet);
        $user->wallet_balance = max(0, $wallet - $deduct);
        $user->save();

        ResellerWalletTransaction::log(
            $user->id,
            'delivery_charge_deduct',
            -$deduct,
            'Order',
            $order->id,
            'অর্ডার #' . ($order->invoice_id ?? $order->id) . ' ক্যান্সেল - ডেলিভারি চার্জ ৳' . number_format($deduct, 2)
        );

        $order->delivery_charge_deducted = true;
        $order->save();

        Log::info("Reseller order #{$order->id} cancelled: deducted ৳{$deduct} delivery charge from user #{$user->id} wallet.");
    }
}
