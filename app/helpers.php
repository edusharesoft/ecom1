<?php

use App\Models\ResellerLandingPage;

if (!function_exists('landing_url')) {
    /**
     * Generate URL for reseller landing - uses custom domain when applicable.
     */
    function landing_url(string $slug, string $path = ''): string
    {
        $landing = ResellerLandingPage::where('slug', $slug)->first();
        $host = strtolower(request()->getHost());

        if ($landing && $landing->custom_domain && strtolower($landing->custom_domain) === $host) {
            return $path === '' ? url('/') : url($path);
        }

        $base = '/r/' . $slug;
        return $path === '' ? url($base) : url(rtrim($base . '/' . ltrim($path, '/'), '/'));
    }
}
