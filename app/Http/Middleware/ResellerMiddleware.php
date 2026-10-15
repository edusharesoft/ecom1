<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\GeneralSetting;

class ResellerMiddleware
{
    /**
     * Ensure the user is authenticated with admin guard and has reseller role.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        // Check if reseller system is enabled
        $generalSetting = GeneralSetting::orderBy('id', 'desc')->first();
        if (!$generalSetting || ($generalSetting->reseller_enabled ?? 1) != 1) {
            return redirect()->route('login')->with('error', 'Reseller system is currently disabled.');
        }

        // Check if user is authenticated with admin guard
        if (!Auth::guard('admin')->check()) {
            return redirect()->route('login')->with('error', 'Please login to access reseller panel.');
        }

        $user = Auth::guard('admin')->user();

        // Check if user has reseller role (check both Spatie role and role column)
        $hasResellerRole = $user->hasRole('reseller') || 
                           (isset($user->role) && strtolower($user->role) === 'reseller') ||
                           $user->getRoleNames()->contains('reseller');
        
        if (!$hasResellerRole) {
            // User doesn't have reseller role
            Auth::guard('admin')->logout();
            return redirect()->route('login')->with('error', 'You do not have permission to access reseller panel.');
        }

        return $next($request);
    }
}
