<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\ResellerLandingPage;
use App\Http\Controllers\Reseller\PublicLandingController;
use App\Http\Controllers\Reseller\LandingOrderController;
use Symfony\Component\HttpFoundation\Response;

class ResellerCustomDomain
{
    public function handle(Request $request, Closure $next): Response
    {
        $host = strtolower($request->getHost());

        // Skip if this is main app domain (e.g. from APP_URL)
        $appUrl = parse_url(config('app.url', ''), PHP_URL_HOST);
        if ($appUrl && strtolower($appUrl) === $host) {
            return $next($request);
        }

        $landing = ResellerLandingPage::where('custom_domain', $host)
            ->where('is_active', 1)
            ->first();

        if (!$landing) {
            return $next($request);
        }

        $path = trim($request->path(), '/');
        $slug = $landing->slug;

        // Map custom domain paths to controllers (without /r/slug prefix)
        if ($path === '' || $path === 'r' || $path === 'r/' . $slug) {
            return app(PublicLandingController::class)->show($slug);
        }

        if (preg_match('#^category/([^/]+)$#', $path, $m)) {
            return app(PublicLandingController::class)->category($slug, $m[1]);
        }
        if (preg_match('#^subcategory/([^/]+)$#', $path, $m)) {
            return app(PublicLandingController::class)->subcategory($slug, $m[1]);
        }
        if (preg_match('#^product/([^/]+)$#', $path, $m)) {
            return app(PublicLandingController::class)->product($slug, $m[1]);
        }

        if ($path === 'order' && $request->isMethod('GET')) {
            return app(LandingOrderController::class)->orderForm($slug);
        }
        if ($path === 'order' && $request->isMethod('POST')) {
            return app(LandingOrderController::class)->storeOrder($request, $slug);
        }
        if (preg_match('#^order/success/(\d+)$#', $path, $m) && $request->isMethod('GET')) {
            return app(LandingOrderController::class)->orderSuccess($slug, (int) $m[1]);
        }
        if (preg_match('#^cart/remove/(\d+)$#', $path, $m) && $request->isMethod('GET')) {
            return app(LandingOrderController::class)->removeFromCart($slug, (int) $m[1]);
        }
        if ($path === 'cart/add' && $request->isMethod('POST')) {
            return app(LandingOrderController::class)->addToCart($request, $slug);
        }

        // Fallback: show main landing
        return app(PublicLandingController::class)->show($slug);
    }
}
