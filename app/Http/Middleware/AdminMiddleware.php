<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminMiddleware
{
    /**
     * Ensure the user is authenticated with admin guard and has admin role (not vendor).
     */
    public function handle(Request $request, Closure $next)
    {
        if (!Auth::guard('admin')->check()) {
            return redirect()->route('login')->with('error', 'Please login to access admin panel.');
        }

        $user = Auth::guard('admin')->user();

        if ($user->id == 1) {
            return $next($request);
        }

        $spatieRoles = $user->getRoleNames()->map(function ($role) {
            return strtolower($role);
        })->toArray();

        if (in_array('admin', $spatieRoles)) {
            return $next($request);
        }

        if (in_array('reseller', $spatieRoles)) {
            return redirect()->route('reseller.dashboard')->with('error', 'You do not have permission to access admin panel.');
        }

        if (in_array('vendor', $spatieRoles)) {
            return redirect()->route('vendor.dashboard')->with('error', 'You do not have permission to access admin panel.');
        }

        if (count($spatieRoles) > 0) {
            return $next($request);
        }

        $roleColumn = isset($user->role) ? strtolower($user->role) : null;
        $blockedRoleColumns = ['vendor', 'reseller'];

        if ($roleColumn && in_array($roleColumn, $blockedRoleColumns)) {
            Auth::guard('admin')->logout();

            return redirect()->route('login')->with('error', 'You do not have permission to access admin panel.');
        }

        return $next($request);
    }
}
