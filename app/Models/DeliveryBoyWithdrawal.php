<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DeliveryBoyWithdrawal extends Model
{
    protected $table = 'delivery_boy_withdrawals';

    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'amount'           => 'decimal:2',
            'processed_at'     => 'datetime',
        ];
    }

    public function deliveryBoy(): BelongsTo
    {
        return $this->belongsTo(DeliveryBoy::class, 'delivery_boy_id');
    }
}
