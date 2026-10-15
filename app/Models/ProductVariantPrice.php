<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductVariantPrice extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'color_id',
        'size_id',
        'price',
        'stock',
        'sku',
    ];

    public $timestamps = false; // 🟢 timestamps বন্ধ

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function color()
    {
        return $this->belongsTo(Color::class);
    }

    public function size()
    {
        return $this->belongsTo(Size::class);
    }
}
