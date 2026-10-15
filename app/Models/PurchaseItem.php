<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PurchaseItem extends Model
{
    protected $fillable = [
        'purchase_id','product_id','variant_price_id',
        'qty','unit_cost','line_total','returned_qty',
    ];

    public function purchase()
    {
        return $this->belongsTo(Purchase::class);
    }

    public function product()
    {
        return $this->belongsTo(\App\Models\Product::class);
    }

    public function variant()
    {
        return $this->belongsTo(\App\Models\ProductVariantPrice::class, 'variant_price_id');
    }
}
