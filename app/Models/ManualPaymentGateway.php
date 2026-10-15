<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ManualPaymentGateway extends Model
{
    protected $fillable = [
        'title',
        'logo',
        'instructions',
        'sort_order',
        'status',
    ];

    protected $casts = [
        'status'     => 'integer',
        'sort_order' => 'integer',
    ];

    /** প্রজেক্টে প্রায় সর্বদা asset('public/...') ব্যবহার করা হয়। */
    public function getLogoAssetUrlAttribute(): ?string
    {
        $logo = $this->attributes['logo'] ?? null;
        if ($logo === null || $logo === '') {
            return null;
        }

        $path = trim(str_replace('\\', '/', (string) $logo), '/');

        return str_starts_with($path, 'public/')
            ? asset($path)
            : asset('public/'.$path);
    }

    public function scopeEnabled($query)
    {
        return $query->where('status', 1);
    }

    /** bKash/Shurjo ইত্যাদি — সার্ভার কলব্যাকে এমাউন্ট সেট হয় */
    public static function usesHostedRedirect(?string $paymentMethod): bool
    {
        return in_array($paymentMethod, ['bkash', 'shurjopay', 'uddoktapay', 'aamarpay'], true);
    }

    public static function isManualPaymentMethod(?string $paymentMethod): bool
    {
        return is_string($paymentMethod) && str_starts_with($paymentMethod, 'manual_');
    }

    public static function manualIdFromPaymentMethod(?string $paymentMethod): ?int
    {
        if (! self::isManualPaymentMethod($paymentMethod)) {
            return null;
        }
        $raw = substr($paymentMethod, strlen('manual_'));

        return ctype_digit($raw) ? (int) $raw : null;
    }
}
