<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DeliveryBoyWalletTransaction extends Model
{
    protected $table = 'delivery_boy_wallet_transactions';

    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'amount'        => 'decimal:2',
            'balance_after' => 'decimal:2',
        ];
    }

    public function deliveryBoy(): BelongsTo
    {
        return $this->belongsTo(DeliveryBoy::class, 'delivery_boy_id');
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class, 'order_id');
    }
}
