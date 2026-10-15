<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DeliveryUpazila extends Model
{
    protected $table = 'upazilas';

    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'district_id' => 'integer',
            'sort_order'  => 'integer',
            'status'      => 'integer',
        ];
    }

    public function district(): BelongsTo
    {
        return $this->belongsTo(DeliveryDistrict::class, 'district_id');
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
