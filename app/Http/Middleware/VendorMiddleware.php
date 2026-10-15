<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\GeneralSetting;

class VendorMiddleware
{
    /**
     * Ensure the user is authenticated with admin guard and has vendor role.
     * 
     * Laravel 12: Explicitly use admin guard for consistency with reseller/admin middleware
     */
    public function handle(Request $request, Closure $next)
    {
        // Check if vendor system is enabled
        $generalSetting = GeneralSetting::orderBy('id', 'desc')->first();
        if (!$generalSetting || ($generalSetting->vendor_enabled ?? 1) != 1) {
            return redirect()->route('login')->with('error', 'Vendor system is currently disabled.');
        }

        // Check if user is authenticated with admin guard (vendors use admin guard)
        if (!Auth::guard('admin')->check()) {
            return redirect()->route('login')->with('error', 'Please login to access vendor panel.');
        }

        $user = Auth::guard('admin')->user();

        // Check if user has vendor role
        if (!$user->hasRole('vendor')) {
            Auth::guard('admin')->logout();
            return redirect()->route('login')->with('error', 'You do not have permission to access vendor panel.');
        }

        return $next($request);
    }
}
