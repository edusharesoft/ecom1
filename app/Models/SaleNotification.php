<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SaleNotification extends Model
{
    protected $table    = 'sale_notifications';
    protected $guarded  = [];
    protected $casts    = [
        'is_real'   => 'boolean',
        'is_active' => 'boolean',
    ];
}
