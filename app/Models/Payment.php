<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'order_id',
        'customer_id',
        'payment_method',
        'amount',
        'manual_payable_snapshot',
        'trx_id',
        'sender_number',
        'payment_status',
    ];
}
