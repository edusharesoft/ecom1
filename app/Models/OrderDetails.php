<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderDetails extends Model
{
    use HasFactory;

    protected $table = 'order_details';

    protected $fillable = [
        'order_id',
        'product_id',
        'product_name',
        'purchase_price',
        'sale_price',
        'product_discount',
        'product_size',
        'variant_price_id',
        'product_color',
        'qty',
        'vendor_id',
        'commission_rate',
        'admin_commission',
        'vendor_earning',
        'vendor_paid_at',
    ];

    // ✅ অর্ডারের সাথে রিলেশন
    public function order()
    {
        return $this->belongsTo(Order::class, 'order_id');
    }

    // ✅ প্রোডাক্ট রিলেশন (ডিজিটাল চেক করার জন্য খুব জরুরি)
    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id');
        // চাইলে select ব্যবহার করতে পারো:
        // ->select('id', 'name', 'slug', 'is_digital', 'digital_file', 'download_limit', 'download_expire_days');
    }

    // ✅ কালার রিলেশন (product_color = Color ID)
    public function color()
    {
        return $this->belongsTo(Color::class, 'product_color', 'id');
    }

    // ✅ সাইজ রিলেশন (product_size = Size ID)
    public function size()
    {
        return $this->belongsTo(Size::class, 'product_size', 'id');
    }

    // ✅ ইমেজ (প্রোডাক্ট আইডি দিয়ে)
    public function image()
    {
        return $this->belongsTo(Productimage::class, 'product_id', 'product_id')
                    ->select('id', 'product_id', 'image');
    }

    // ✅ ভেন্ডর রিলেশন
    public function vendor()
    {
        return $this->belongsTo(Vendor::class, 'vendor_id')
                    ->select('id', 'shop_name', 'owner_name');
    }
}
