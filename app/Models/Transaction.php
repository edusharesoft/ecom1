<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Transaction extends Model
{
    protected $fillable = [
        'name',
        'email',
        'phone',
        'method',
        'amount',
        'currency',
        'status',
        'transaction_id',
        'order_id',
        'main_order_id',
        'event_name',
        'event_data',
        'address',
    ];

    protected $casts = [
        'event_data' => 'array',
        'amount' => 'decimal:2',
    ];
}