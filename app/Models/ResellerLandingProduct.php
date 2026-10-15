<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ResellerLandingProduct extends Model
{
    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'custom_price' => 'decimal:2',
        ];
    }

    public function resellerLandingPage(): BelongsTo
    {
        return $this->belongsTo(ResellerLandingPage::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
}
