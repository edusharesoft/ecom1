<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VendorWalletTransaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'vendor_id',
        'type',
        'status',
        'amount',
        'source_type',
        'source_id',
        'note',
    ];

    public function vendor()
    {
        return $this->belongsTo(Vendor::class);
    }
}
