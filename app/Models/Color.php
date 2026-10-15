<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Color extends Model
{
    use HasFactory;

    protected $fillable = [
        'colorName',
        'color', // এটি হবে HEX code field, যেমন #FF0000
        'status',
    ];

    /**
     * Display name - works whether column is colorName or color_name
     */
    public function getNameAttribute()
    {
        return $this->attributes['name'] ?? $this->attributes['colorName'] ?? $this->attributes['color_name'] ?? null;
    }

    /**
     * Get display name for order/invoice (same as name)
     */
    public function getDisplayName(): ?string
    {
        return $this->name ?? $this->colorName ?? $this->color_name ?? null;
    }

    /**
     * Relationship: A Color can be used in many products.
     */
    public function productColors()
    {
        return $this->hasMany(Productcolor::class, 'color_id', 'id');
    }
}
