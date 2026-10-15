<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\IpBlock;
use Illuminate\Support\Facades\Cache;

class IpFilter
{
    public function handle(Request $request, Closure $next)
    {
        $clientIp = $request->getClientIp();
        $ipblock = Cache::remember("ip_block_{$clientIp}", 60, function () use ($clientIp) {
            return IpBlock::where('ip_no', $clientIp)->first();
        });
        if ($ipblock) {
            abort(403, "You are restricted to access the site. Because " . $ipblock->reason);
        }
        return $next($request);
    }
}
