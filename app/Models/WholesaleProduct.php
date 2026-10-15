<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class WholesaleProduct extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'category_id',
        'subcategory_id',
        'childcategory_id',
        'brand_id',
        'product_code',
        'purchase_price',
        'wholesale_price',
        'retail_price',
        'min_quantity',
        'stock',
        'status',
        'approval_status',
        'vendor_id',
        'created_by',
        'meta_title',
        'meta_description',
        'meta_keywords',
        'meta_image',
        'feature_product',
        'unit',
    ];

    protected function casts(): array
    {
        return [
            'purchase_price' => 'decimal:2',
            'wholesale_price' => 'decimal:2',
            'retail_price' => 'decimal:2',
            'min_quantity' => 'integer',
            'stock' => 'integer',
            'status' => 'integer',
            'feature_product' => 'integer',
        ];
    }

    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($product) {
            if (empty($product->slug)) {
                $product->slug = Str::slug($product->name . '-' . time());
            }
            if (empty($product->product_code)) {
                $lastId = self::max('id') ?? 0;
                $product->product_code = 'WP' . str_pad($lastId + 1, 5, '0', STR_PAD_LEFT);
            }
        });
    }

    // Relationships
    public function category()
    {
        return $this->belongsTo(Category::class, 'category_id');
    }

    public function subcategory()
    {
        return $this->belongsTo(Subcategory::class, 'subcategory_id');
    }

    public function childcategory()
    {
        return $this->belongsTo(Childcategory::class, 'childcategory_id');
    }

    public function brand()
    {
        return $this->belongsTo(Brand::class, 'brand_id');
    }

    public function vendor()
    {
        return $this->belongsTo(Vendor::class, 'vendor_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function images()
    {
        return $this->hasMany(WholesaleProductImage::class, 'wholesale_product_id')->orderBy('sort_order');
    }

    public function image()
    {
        return $this->hasOne(WholesaleProductImage::class, 'wholesale_product_id')->orderBy('sort_order');
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('status', 1)->where('approval_status', 'approved');
    }

    public function scopeApproved($query)
    {
        return $query->where('approval_status', 'approved');
    }

    public function scopePending($query)
    {
        return $query->where('approval_status', 'pending');
    }

    public function scopeByVendor($query, $vendorId)
    {
        return $query->where('vendor_id', $vendorId);
    }

    // Helper methods
    public function isActive()
    {
        return $this->status == 1 && $this->approval_status == 'approved';
    }

    public function isApproved()
    {
        return $this->approval_status == 'approved';
    }

    public function isPending()
    {
        return $this->approval_status == 'pending';
    }
}
