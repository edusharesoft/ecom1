<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VendorWithdrawal extends Model
{
    use HasFactory;

    protected $fillable = [
        'vendor_id',
        'amount',
        'charge',
        'payout_method',
        'account_name',
        'account_number',
        'note',
        'status',
        'admin_note',
        'processed_at',
    ];

    // Laravel 12: Use casts() method instead of $casts property
    protected function casts(): array
    {
        return [
            'processed_at' => 'datetime',
        ];
    }

    public function vendor()
    {
        return $this->belongsTo(Vendor::class);
    }
}
