<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    // সব ফিল্ড mass assign করতে পারবে
    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'delivery_assigned_at' => 'datetime',
            'rider_delivered_at'   => 'datetime',
            'delivery_otp_sent_at' => 'datetime',
            'delivery_otp_expires_at' => 'datetime',
            'delivery_otp_verified_at' => 'datetime',
        ];
    }

    // ============================
    // 🌟 RELATIONSHIPS
    // ============================

    // অর্ডারের সব প্রোডাক্ট আইটেম (order_details টেবিল)
    public function orderdetails()
    {
        return $this->hasMany(OrderDetails::class, 'order_id');
    }

    // alias: items()
    public function items()
    {
        return $this->hasMany(OrderDetails::class, 'order_id');
    }

    // 🔥 অর্ডার থেকে সরাসরি Products আনতে (hasManyThrough)
    public function products()
    {
        return $this->hasManyThrough(
            Product::class,      // শেষ মডেল (যেটা চাও)
            OrderDetails::class, // মধ্যবর্তী মডেল
            'order_id',          // order_details টেবিলের foreign key (order_id)
            'id',                // products টেবিলের primary key
            'id',                // orders টেবিলের local key
            'product_id'         // order_details টেবিলের foreign key (product_id)
        );
    }

    // পুরোনো কোডে যদি with('product') / $order->product থাকে,
    // সেটা ব্রেক না করার জন্য product() নামেও একই relation দিলাম।
    public function product()
    {
        return $this->products();
    }

    // পেমেন্ট স্ট্যাটাস / অর্ডার স্ট্যাটাস
    public function status()
    {
        return $this->belongsTo(OrderStatus::class, 'order_status');
    }

    // শিপিং তথ্য
    public function shipping()
    {
        return $this->hasOne(Shipping::class, 'order_id', 'id');
    }

    // পেমেন্ট ডাটা
    public function payment()
    {
        return $this->hasOne(Payment::class, 'order_id', 'id');
    }

    // কাস্টমার (frontend user)
    public function customer()
    {
        return $this->belongsTo(Customer::class, 'customer_id');
    }

    // অ্যাডমিন ইউজার (order created by)
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    // ============================
    // 🌟 DIGITAL DOWNLOAD SUPPORT
    // ============================

    // অর্ডার থেকে সব ডিজিটাল ডাউনলোড লিঙ্ক
    public function digitalDownloads()
    {
        return $this->hasMany(DigitalDownload::class, 'order_id');
    }

    // ============================
    // 🌟 REFUND SUPPORT
    // ============================

    // অর্ডারের সব রিফান্ড রিকোয়েস্ট
    public function refunds()
    {
        return $this->hasMany(Refund::class, 'order_id');
    }

    public function deliveryBoy()
    {
        return $this->belongsTo(DeliveryBoy::class, 'delivery_boy_id');
    }

    // অর্ডারের active/pending refund আছে কিনা
    public function hasPendingRefund()
    {
        return $this->refunds()->whereIn('status', ['pending', 'approved'])->exists();
    }
}
