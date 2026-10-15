<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Popup extends Model
{
    use HasFactory;

    // ডাটাবেসের যে কলামগুলো পূরণ করা যাবে তার লিস্ট (Mass Assignment Safe)
    protected $fillable = [
        'title',
        'description',
        'offer_end_text',
        'btn_text',
        'link',
        'image',
        'status',
    ];

    // অপশনাল: স্ট্যাটাস চেক করার জন্য স্কোপ (সহজ কুয়েরির জন্য)
    public function scopeActive($query)
    {
        return $query->where('status', 1);
    }
}