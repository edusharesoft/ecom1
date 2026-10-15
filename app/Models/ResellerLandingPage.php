<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Str;

class ResellerLandingPage extends Model
{
    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'slider_images' => 'array',
            'is_active' => 'boolean',
            'show_newsletter_footer' => 'boolean',
            'show_social_footer' => 'boolean',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function landingProducts(): BelongsToMany
    {
        return $this->belongsToMany(Product::class, 'reseller_landing_products', 'reseller_landing_page_id', 'product_id')
            ->withPivot('custom_price')
            ->withTimestamps();
    }

    public static function generateSlug(string $base, ?int $excludeUserId = null): string
    {
        $slug = Str::slug($base);
        if (empty($slug)) {
            $slug = 'store-' . Str::random(6);
        }
        $original = $slug;
        $i = 1;
        while (true) {
            $q = self::where('slug', $slug);
            if ($excludeUserId) {
                $q->where('user_id', '!=', $excludeUserId);
            }
            if (!$q->exists()) {
                return $slug;
            }
            $slug = $original . '-' . $i;
            $i++;
        }
    }
}
