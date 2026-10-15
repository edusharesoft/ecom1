<?php

namespace App\Http\Middleware;

use App\Providers\RouteServiceProvider;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RedirectIfAuthenticated
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string|null  ...$guards
     * @return mixed
     */
    public function handle(Request $request, Closure $next, ...$guards)
    {
        $guards = empty($guards) ? [null] : $guards;

        foreach ($guards as $guard) {
            if (Auth::guard($guard)->check()) {
                
                // ১. যদি অ্যাডমিন লগইন করা থাকে, তাকে অ্যাডমিন ড্যাশবোর্ডে পাঠাবে
                if ($guard === 'admin') {
                    return redirect()->route('admin.dashboard');
                }

                if ($guard === 'delivery_boy') {
                    return redirect()->route('delivery.dashboard');
                }

                // ২. যদি সাধারণ ইউজার লগইন করা থাকে, তাকে ইউজার ড্যাশবোর্ডে পাঠাবে
                // (সাধারণত 'web' হলো ডিফল্ট ইউজার গার্ড)
                if ($guard === 'web' || $guard === null) {
                    return redirect()->route('user.dashboard');
                }
            }
        }

        return $next($request);
    }
}