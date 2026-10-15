<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Spatie\Permission\Traits\HasRoles;
use Laravel\Sanctum\HasApiTokens;

class Customer extends Authenticatable
{
    use HasFactory, HasRoles, HasApiTokens;

    protected $guard = 'customer';
    
    // Set guard name for Spatie Permission
    protected $guard_name = 'customer';

    // 🔥 এখানে সব প্রয়োজনীয় ফিল্ডগুলো রাখলাম
    protected $fillable = [
        'name',
        'slug',
        'phone',
        'email',
        'password',
        'verify',
        'status',
        'forgot',
        'address',
        'district',
        'area',
        'image',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    public function cust_area()
    {
        return $this->belongsTo(District::class, 'area');
    }

    public function orders()
    {
        return $this->hasMany(Order::class, 'customer_id');
    }

    /**
     * Get the customer's profile image URL with default fallback
     * 
     * @return string
     */
    public function getProfileImageUrlAttribute()
    {
        $image = $this->attributes['image'] ?? null;
        $defaultImage = 'public/uploads/default/user.svg';
        $legacyDefault = 'public/uploads/default/user.png';
        $fallbackImage = 'public/uploads/default/no-image.png';

        // If image is empty or doesn't exist, use default
        if (empty($image) || !file_exists(public_path($image))) {
            if (file_exists(public_path($defaultImage))) {
                return $defaultImage;
            }
            if (file_exists(public_path($legacyDefault))) {
                return $legacyDefault;
            }
            if (file_exists(public_path($fallbackImage))) {
                return $fallbackImage;
            }
            return $defaultImage;
        }
        
        return $image;
    }
}
