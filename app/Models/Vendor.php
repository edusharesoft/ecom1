<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Vendor extends Model
{
    use HasFactory;

    protected $guarded = [];

    /**
     * A vendor can have many products.
     */
    public function products()
    {
        return $this->hasMany(Product::class, 'vendor_id');
    }

    /**
     * Wallet relationship.
     */
    public function wallet()
    {
        return $this->hasOne(VendorWallet::class);
    }

    /**
     * Withdrawal requests.
     */
    public function withdrawals()
    {
        return $this->hasMany(VendorWithdrawal::class);
    }

    /**
     * Laravel 12: Use casts() method instead of $casts property
     */
    protected function casts(): array
    {
        return [
            'verified_at' => 'datetime',
        ];
    }
}
