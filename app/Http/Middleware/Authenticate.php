<?php

namespace App\Http\Middleware;

use Illuminate\Auth\Middleware\Authenticate as Middleware;

class Authenticate extends Middleware
{
    
    
    protected function redirectTo($request)
    {
        if (! $request->expectsJson()) {
            if ($request->is('delivery') || $request->is('delivery/*')) {
                return route('delivery.login');
            }

            return route('login');
        }
    }
}
