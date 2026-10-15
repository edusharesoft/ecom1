<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Auth;
class Customer
{
    public function handle($request, Closure $next)
    {
        if (Auth::guard('customer')->user()){
            return $next($request);
        }
        return redirect()->route('customer.login');
    }
}
