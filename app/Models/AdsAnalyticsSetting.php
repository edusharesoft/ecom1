<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class AdsAnalyticsSetting extends Model
{
    protected $table = 'ads_analytics_settings';

    protected $fillable = [
        'platform',
        'is_active',
        'access_token',
        'ad_account_id',
        'app_id',
        'app_secret',
        'refresh_token',
        'client_id',
        'client_secret',
        'extra_config',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'extra_config' => 'array',
    ];

    public static function getByPlatform(string $platform): ?self
    {
        $result = Cache::remember("ads_analytics_{$platform}", 300, function () use ($platform) {
            return static::where('platform', $platform)->where('is_active', true)->first();
        });
        return $result instanceof self ? $result : null;
    }

    public static function forgetCache(): void
    {
        foreach (['facebook', 'google', 'tiktok'] as $p) {
            Cache::forget("ads_analytics_{$p}");
        }
    }
}
