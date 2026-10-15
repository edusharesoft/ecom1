<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Complaint extends Model
{
    protected $fillable = [
        'customer_id',
        'order_id',
        'name',
        'phone',
        'image',
        'description',
        'status'
    ];
}
