<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ResellerWalletTransaction extends Model
{
    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
            'balance_after' => 'decimal:2',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public static function log(int $userId, string $type, float $amount, ?string $refType = null, ?int $refId = null, ?string $description = null): self
    {
        $user = User::find($userId);
        $balanceAfter = $user ? (float) ($user->wallet_balance ?? 0) : 0;

        return self::create([
            'user_id' => $userId,
            'type' => $type,
            'amount' => $amount,
            'balance_after' => $balanceAfter,
            'reference_type' => $refType,
            'reference_id' => $refId,
            'description' => $description,
        ]);
    }

    public function getTypeLabelAttribute(): string
    {
        return match($this->type) {
            'deposit' => 'ডিপোজিট',
            'order_profit' => 'অর্ডার প্রফিট',
            'delivery_charge_deduct' => 'ডেলিভারি চার্জ (ক্যান্সেল)',
            'withdrawal' => 'উইথড্র',
            'withdrawal_reversed' => 'উইথড্র ফেরত',
            default => $this->type,
        };
    }
}
