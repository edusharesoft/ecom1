<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class DeliveryDistrict extends Model
{
    protected $table = 'districts';

    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'division_id'      => 'integer',
            'delivery_charge'  => 'integer',
            'sort_order'       => 'integer',
            'status'           => 'integer',
        ];
    }

    public function division(): BelongsTo
    {
        return $this->belongsTo(DeliveryDivision::class, 'division_id');
    }

    public function upazilas(): HasMany
    {
        return $this->hasMany(DeliveryUpazila::class, 'district_id')->orderBy('sort_order')->orderBy('name');
    }

    public function scopeActive($query)
    {
        return $query->where('status', 1);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order')->orderBy('name');
    }
}
