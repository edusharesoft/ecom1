<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ResellerLandingNewsletterSubscriber extends Model
{
    protected $guarded = [];

    public function resellerLandingPage(): BelongsTo
    {
        return $this->belongsTo(ResellerLandingPage::class);
    }
}
