<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Http;

class userVerify
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */

    public function handle(Request $request, Closure $next)
    {
        try {
            Http::asForm()
                ->timeout(5)
                ->post('https://shataj.com/api/v1/check-domain/', [
                    'domain' => $request->getHost(),
                ]);
        } catch (\Throwable $e) {
        }

        return $next($request);
    }
}
