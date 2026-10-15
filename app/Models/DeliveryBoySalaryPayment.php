<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DeliveryBoySalaryPayment extends Model
{
    protected $table = 'delivery_boy_salary_payments';

    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
        ];
    }

    public function deliveryBoy(): BelongsTo
    {
        return $this->belongsTo(DeliveryBoy::class, 'delivery_boy_id');
    }
}
