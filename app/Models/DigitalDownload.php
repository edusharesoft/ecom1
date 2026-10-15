<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DigitalDownload extends Model
{
    protected $table = 'digital_downloads';

    protected $fillable = [
        'order_id',
        'product_id',
        'customer_id',
        'token',
        'file_path',
        'remaining_downloads',
        'expires_at',
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }
}
