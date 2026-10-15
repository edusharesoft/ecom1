<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class DemoModeMiddleware
{
    /**
     * ডেমো মুড চালু থাকলে অ্যাডমিন প্যানেল থেকে কোন ডাটা পরিবর্তন বন্ধ রাখে।
     * .env এ DEMO_MODE=true সেট করলেই চালু হবে।
     * ফ্রন্টএন্ড (কাস্টমার অর্ডার, ট্রাকিং ইত্যাদি) স্বাভাবিক কাজ করবে।
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!filter_var(env('DEMO_MODE', false), FILTER_VALIDATE_BOOLEAN)) {
            return $next($request);
        }

        $path = $request->path();
        $allowedPostPaths = ['vendor/logout', 'reseller/logout'];
        foreach ($allowedPostPaths as $p) {
            if (str_contains($path, $p)) {
                return $next($request);
            }
        }

        if (in_array($request->method(), ['GET', 'HEAD'], true)) {
            $destructiveGets = [
                'admin/blog/delete', 'admin/products/image/destroy', 'admin/products/price/destroy',
                'admin/campaign/image/destroy', 'admin/order-bulk-destroy', 'admin/clear-cache',
            ];
            foreach ($destructiveGets as $p) {
                if (str_starts_with($path, $p) || str_contains($path, $p)) {
                    return $this->blockResponse($request, 'ডেমো মুড চালু আছে। অ্যাডমিন প্যানেল থেকে কোন পরিবর্তন করা যাবে না।');
                }
            }
            return $next($request);
        }

        $message = 'ডেমো মুড চালু আছে। অ্যাডমিন প্যানেল থেকে কোন পরিবর্তন করা যাবে না।';

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => false,
                'message' => $message,
                'demo_mode' => true,
            ], 403);
        }

        return redirect()->back()
            ->with('demo_mode_blocked', true);
    }

    protected function blockResponse(Request $request, string $message): Response
    {
        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => false,
                'message' => $message,
                'demo_mode' => true,
            ], 403);
        }
        return redirect()->back()
            ->with('demo_mode_blocked', true);
    }
}
