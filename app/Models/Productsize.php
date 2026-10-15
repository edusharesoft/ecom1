<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Productsize extends Model
{
    use HasFactory;

    // ✅ আসল টেবিল নাম
    protected $table = 'productsizes';

    protected $fillable = ['product_id', 'size_id'];

    public function size()
    {
        return $this->belongsTo(Size::class, 'size_id', 'id');
    }

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id', 'id');
    }
}
