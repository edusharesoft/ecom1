<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Productcolor extends Model
{
    use HasFactory;

    // ✅ আসল টেবিলের নাম এখানে দাও
    protected $table = 'productcolors';

    protected $fillable = ['product_id', 'color_id'];

    public function color()
    {
        return $this->belongsTo(Color::class, 'color_id', 'id');
    }

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id', 'id');
    }
}
