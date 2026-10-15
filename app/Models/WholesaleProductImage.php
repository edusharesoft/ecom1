<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WholesaleProductImage extends Model
{
    use HasFactory;

    protected $fillable = [
        'wholesale_product_id',
        'image',
        'sort_order',
    ];

    public function wholesaleProduct()
    {
        return $this->belongsTo(WholesaleProduct::class, 'wholesale_product_id');
    }
}
