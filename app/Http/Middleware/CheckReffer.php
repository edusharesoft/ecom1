<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
class CheckReffer
{ 
    
   public function handle($request, Closure $next)
    {
        // $allowedDomain = 'maxvaly.com'; // Replace with your actual domain
        // $referer =  $request->getHost();
        // if ($referer && strpos($referer, $allowedDomain) !== false) {
        //     return $next($request);
        // }
        // abort(403, 'Unauthorized access.');

        return $next($request);
    }

}
