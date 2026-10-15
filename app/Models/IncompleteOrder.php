<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class IncompleteOrder extends Model
{
    use HasFactory;

    /**
     * যদি তোমার টেবিলের নাম 'incomplete_orders' হয়
     * তাহলে নিচের লাইন দরকার নেই, কারণ Laravel নিজেই ধরে ফেলবে।
     * কিন্তু ক্লিয়ার করার জন্য রেখে দিতে চাইলে আনকমেন্ট করে ব্যবহার করতে পারো।
     */
    // protected $table = 'incomplete_orders';

    /**
     * কোন কোন ফিল্ড mass assignment দিয়ে fill করা যাবে
     */
    protected $fillable = [
        'name',
        'phone',
        'address',
        'items',
        'product_image',
        'product_link',
        'total_amount',
    ];

    /**
     * Laravel 12: Use casts() method instead of $casts property
     * items কলামকে স্বয়ংক্রিয়ভাবে array <-> json convert করার জন্য
     */
    protected function casts(): array
    {
        return [
            'items'        => 'array',
            'total_amount' => 'float',
        ];
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    public function getLineItemsAttribute(): array
    {
        return \App\Support\IncompleteOrderPayload::lineItems($this->items);
    }

    /**
     * @return array<string, mixed>
     */
    public function getCheckoutMetaAttribute(): array
    {
        return \App\Support\IncompleteOrderPayload::meta($this->items);
    }

    /**
     * যদি তোমার টেবিলে created_at / updated_at না থাকে,
     * তাহলে timestamps = false করে দাও।
     * নাহলে এটাকে বাদ দিতে পারো।
     */
    // public $timestamps = false;
}
