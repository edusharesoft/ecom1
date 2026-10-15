<?php

namespace App\Support;

use Illuminate\Http\Request;

class TrafficSourceDetector
{
    public const ALLOWED = [
        'facebook', 'google', 'tiktok', 'whatsapp', 'instagram',
        'youtube', 'bing', 'yahoo', 'twitter', 'direct', 'other',
    ];

    /**
     * @return array{source: string, referrer: string}
     */
    public static function detect(Request $request): array
    {
        $referrer = (string) $request->headers->get('referer', '');
        $host     = strtolower(preg_replace('/^www\./', '', $request->getHost()));

        $utm = strtolower(trim((string) $request->query('utm_source', '')));

        // Referrer আগে — fbclid URL-এ থাকলেও YouTube/Twitter রিফারার প্রাধান্য পাবে
        if ($referrer !== '') {
            $fromRef = self::detectFromReferrerUrl($referrer, $host);
            if ($fromRef !== null) {
                return $fromRef;
            }
        }

        if ($request->filled('fbclid') || $utm === 'fb' || $utm === 'facebook' || str_contains($utm, 'facebook')) {
            return self::result('facebook', $referrer, 'https://www.facebook.com/');
        }
        if ($utm === 'ig' || $utm === 'instagram' || str_contains($utm, 'instagram')) {
            return self::result('instagram', $referrer, 'https://www.instagram.com/');
        }
        if ($request->filled('gclid') || str_contains($utm, 'google')) {
            return self::result('google', $referrer, 'https://www.google.com/');
        }
        if ($request->filled('ttclid') || str_contains($utm, 'tiktok')) {
            return self::result('tiktok', $referrer, 'https://www.tiktok.com/');
        }
        if ($request->filled('twclid') || $utm === 'x' || $utm === 'twitter' || str_contains($utm, 'twitter')) {
            return self::result('twitter', $referrer, 'https://twitter.com/');
        }
        if (str_contains($utm, 'whatsapp') || $utm === 'wa') {
            return self::result('whatsapp', $referrer, 'https://www.whatsapp.com/');
        }
        if (str_contains($utm, 'youtube') || $utm === 'yt') {
            return self::result('youtube', $referrer, 'https://www.youtube.com/');
        }
        if (str_contains($utm, 'bing')) {
            return self::result('bing', $referrer, 'https://www.bing.com/');
        }
        if (str_contains($utm, 'yahoo')) {
            return self::result('yahoo', $referrer, 'https://www.yahoo.com/');
        }
        if ($utm !== '' && in_array($utm, self::ALLOWED, true)) {
            return self::result($utm, $referrer, '');
        }
        if ($utm !== '') {
            return self::result('other', $referrer, '');
        }

        return ['source' => 'direct', 'referrer' => ''];
    }

    /**
     * @return array{source: string, referrer: string}|null
     */
    private static function detectFromReferrerUrl(string $referrer, string $siteHost): ?array
    {
        $r = strtolower($referrer);

        $map = [
            'facebook'  => ['facebook.com', 'fb.com', 'fb.me', 'm.facebook.com', 'l.facebook.com', 'lm.facebook.com', 'web.facebook.com'],
            'instagram' => ['instagram.com', 'l.instagram.com'],
            'google'    => ['google.com', 'google.com.bd', 'google.co.in'],
            'tiktok'    => ['tiktok.com'],
            'twitter'   => ['twitter.com', 't.co', 'x.com'],
            'youtube'   => ['youtube.com', 'youtu.be', 'm.youtube.com'],
            'bing'      => ['bing.com'],
            'yahoo'     => ['yahoo.com'],
            'whatsapp'  => ['whatsapp.com', 'wa.me', 'api.whatsapp.com', 'web.whatsapp.com'],
        ];

        foreach ($map as $source => $needles) {
            foreach ($needles as $needle) {
                if (str_contains($r, $needle)) {
                    return self::result($source, $referrer, '');
                }
            }
        }

        $refHost = parse_url($referrer, PHP_URL_HOST);
        if (is_string($refHost) && $refHost !== '') {
            $refHost = strtolower(preg_replace('/^www\./', '', $refHost));
            if ($refHost !== $siteHost) {
                return self::result('other', $referrer, '');
            }
        }

        return null;
    }

    /**
     * @return array{source: string, referrer: string}
     */
    private static function result(string $source, string $referrer, string $fallbackReferrer): array
    {
        $ref = trim($referrer);
        if ($ref === '' && $fallbackReferrer !== '') {
            $ref = $fallbackReferrer;
        }

        return [
            'source'   => $source,
            'referrer' => self::clip($ref),
        ];
    }

    public static function normalize(?string $source): string
    {
        $s = strtolower(trim((string) $source));

        return in_array($s, self::ALLOWED, true) ? $s : 'other';
    }

    public static function clip(string $value): string
    {
        return substr($value, 0, 490);
    }
}
