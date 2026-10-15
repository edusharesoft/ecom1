<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductWholesalePrice extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'min_quantity',
        'max_quantity',
        'wholesale_price',
        'stock',
    ];

    protected function casts(): array
    {
        return [
            'min_quantity' => 'integer',
            'max_quantity' => 'integer',
            'wholesale_price' => 'decimal:2',
            'stock' => 'integer',
        ];
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
