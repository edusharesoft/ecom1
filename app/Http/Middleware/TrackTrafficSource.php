<?php

namespace App\Http\Middleware;

use App\Support\TrafficSourceDetector;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * শেয়ার করা সাধারণ প্রোডাক্ট লিঙ্ক থেকে ট্রাফিক ধরা — referrer / fbclid / gclid (utm ছাড়াই)।
 */
class TrackTrafficSource
{
    public function handle(Request $request, Closure $next): Response
    {
        if ($this->shouldSkip($request)) {
            return $next($request);
        }

        $detected = TrafficSourceDetector::detect($request);
        $current  = session('order_traffic_source');

        // নতুন নন-ডাইরেক্ট সিগন্যাল (YouTube, Twitter, fbclid…) — আগের Facebook ওভাররাইট করবে
        if ($detected['source'] !== 'direct') {
            session([
                'order_traffic_source'   => $detected['source'],
                'order_traffic_referrer' => $detected['referrer'],
            ]);
        } elseif ($current === null || $current === '' || $current === 'direct') {
            // ইন্টারনাল পেজ ব্রাউজ — আগের facebook/youtube মুছবে না
            session([
                'order_traffic_source'   => 'direct',
                'order_traffic_referrer' => $detected['referrer'],
            ]);
        }

        return $next($request);
    }

    private function shouldSkip(Request $request): bool
    {
        if ($request->is('admin/*', 'super/*', 'delivery/*', 'api/*')) {
            return true;
        }

        $path = ltrim($request->path(), '/');

        return in_array($path, ['up'], true);
    }
}
