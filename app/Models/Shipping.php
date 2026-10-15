<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Shipping extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'order_id',
        'customer_id',
        'name',
        'phone',
        'address',
        'area',
        'division_id',
        'district_id',
        'upazila_id',
    ];
    
    public function division()
    {
        return $this->belongsTo(DeliveryDivision::class, 'division_id');
    }

    public function district()
    {
        return $this->belongsTo(DeliveryDistrict::class, 'district_id');
    }

    public function upazila()
    {
        return $this->belongsTo(DeliveryUpazila::class, 'upazila_id');
    }
}
