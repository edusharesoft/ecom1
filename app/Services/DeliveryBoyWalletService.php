<?php

namespace App\Services;

use App\Models\DeliveryBoy;
use App\Models\DeliveryBoyWalletTransaction;
use App\Models\DeliveryBoyWithdrawal;
use App\Models\Order;
use Illuminate\Support\Facades\DB;

class DeliveryBoyWalletService
{
    public function credit(DeliveryBoy $boy, string $type, float $amount, ?int $orderId, ?string $note, ?int $withdrawalId = null): DeliveryBoyWalletTransaction
    {
        if ($amount <= 0) {
            throw new \InvalidArgumentException('Amount must be positive');
        }

        return DB::transaction(function () use ($boy, $type, $amount, $orderId, $note, $withdrawalId) {
            $locked = DeliveryBoy::whereKey($boy->id)->lockForUpdate()->first();
            $newBal = (float) $locked->wallet_balance + $amount;
            $locked->wallet_balance = $newBal;
            $locked->save();

            return DeliveryBoyWalletTransaction::create([
                'delivery_boy_id' => $locked->id,
                'type'            => $type,
                'amount'          => $amount,
                'direction'       => 'credit',
                'balance_after'   => $newBal,
                'order_id'        => $orderId,
                'withdrawal_id'   => $withdrawalId,
                'note'            => $note,
            ]);
        });
    }

    public function debit(DeliveryBoy $boy, string $type, float $amount, ?string $note, ?int $withdrawalId = null, ?int $orderId = null): DeliveryBoyWalletTransaction
    {
        if ($amount <= 0) {
            throw new \InvalidArgumentException('Amount must be positive');
        }

        return DB::transaction(function () use ($boy, $type, $amount, $note, $withdrawalId, $orderId) {
            $locked = DeliveryBoy::whereKey($boy->id)->lockForUpdate()->first();
            if ((float) $locked->wallet_balance < $amount) {
                throw new \RuntimeException('Insufficient wallet balance');
            }
            $newBal = (float) $locked->wallet_balance - $amount;
            $locked->wallet_balance = $newBal;
            $locked->save();

            return DeliveryBoyWalletTransaction::create([
                'delivery_boy_id' => $locked->id,
                'type'            => $type,
                'amount'          => $amount,
                'direction'       => 'debit',
                'balance_after'   => $newBal,
                'order_id'        => $orderId,
                'withdrawal_id'   => $withdrawalId,
                'note'            => $note,
            ]);
        });
    }

    public function creditCommission(DeliveryBoy $boy, Order $order): ?DeliveryBoyWalletTransaction
    {
        $exists = DeliveryBoyWalletTransaction::where('delivery_boy_id', $boy->id)
            ->where('order_id', $order->id)
            ->where('type', 'commission')
            ->exists();
        if ($exists) {
            return null;
        }

        $amt = (float) $boy->commission_per_delivery;
        if ($amt <= 0) {
            return null;
        }

        return $this->credit(
            $boy,
            'commission',
            $amt,
            $order->id,
            'Commission for invoice '.$order->invoice_id
        );
    }

    public function approveWithdrawal(DeliveryBoyWithdrawal $w): void
    {
        DB::transaction(function () use ($w) {
            $w->refresh();
            if ($w->status !== 'pending') {
                return;
            }
            $boy = DeliveryBoy::whereKey($w->delivery_boy_id)->lockForUpdate()->firstOrFail();
            $this->debit(
                $boy,
                'withdrawal',
                (float) $w->amount,
                'Withdrawal #'.$w->id,
                $w->id,
                null
            );
            $w->status = 'approved';
            $w->processed_at = now();
            $w->processed_by = auth()->id();
            $w->save();
        });
    }
}
