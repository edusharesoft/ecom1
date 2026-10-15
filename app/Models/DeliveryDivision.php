<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class DeliveryDivision extends Model
{
    protected $table = 'divisions';

    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'sort_order' => 'integer',
            'status'     => 'integer',
        ];
    }

    public function districts(): HasMany
    {
        return $this->hasMany(DeliveryDistrict::class, 'division_id')->orderBy('sort_order')->orderBy('name');
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
