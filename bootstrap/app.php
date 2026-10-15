<?php

require_once __DIR__.'/../app/helpers.php';

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        // =================================================================
        // 🛡️ Global Middleware
        // =================================================================
        $middleware->use([
            \App\Http\Middleware\TrustProxies::class,
            \Illuminate\Http\Middleware\HandleCors::class,
            \App\Http\Middleware\PreventRequestsDuringMaintenance::class,
            \Illuminate\Foundation\Http\Middleware\ValidatePostSize::class,
            \App\Http\Middleware\TrimStrings::class,
            \Illuminate\Foundation\Http\Middleware\ConvertEmptyStringsToNull::class,
            \App\Http\Middleware\userVerify::class,
        ]);

        // =================================================================
        // 🌐 Web Middleware Group
        // =================================================================
        $middleware->web(prepend: [
            \App\Http\Middleware\ResellerCustomDomain::class,
        ]);
        $middleware->web(append: [
            \App\Http\Middleware\EncryptCookies::class,
            \Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse::class,
            \Illuminate\Session\Middleware\StartSession::class,
            \Illuminate\View\Middleware\ShareErrorsFromSession::class,
            \App\Http\Middleware\VerifyCsrfToken::class,
            \Illuminate\Routing\Middleware\SubstituteBindings::class,
            // প্রোডাক্ট লিঙ্ক শেয়ার থেকে ট্রাফিক সোর্স (referrer / fbclid — utm লাগে না)
            \App\Http\Middleware\TrackTrafficSource::class,
        ]);

        // =================================================================
        // 🔌 API Middleware Group
        // =================================================================
        $middleware->api(prepend: [
            // \Laravel\Sanctum\Http\Middleware\EnsureFrontendRequestsAreStateful::class,
        ]);
        
        // API throttle is handled by default in Laravel 12

        // =================================================================
        // 🏷️ Middleware Aliases
        // =================================================================
        $middleware->alias([
            'auth' => \App\Http\Middleware\Authenticate::class,
            'auth.basic' => \Illuminate\Auth\Middleware\AuthenticateWithBasicAuth::class,
            'auth.session' => \Illuminate\Session\Middleware\AuthenticateSession::class,
            'cache.headers' => \Illuminate\Http\Middleware\SetCacheHeaders::class,
            'can' => \Illuminate\Auth\Middleware\Authorize::class,
            'guest' => \App\Http\Middleware\RedirectIfAuthenticated::class,
            'password.confirm' => \Illuminate\Auth\Middleware\RequirePassword::class,
            'signed' => \App\Http\Middleware\ValidateSignature::class,
            'throttle' => \Illuminate\Routing\Middleware\ThrottleRequests::class,
            'verified' => \Illuminate\Auth\Middleware\EnsureEmailIsVerified::class,
            
            // Spatie Permission Middleware
            'role' => \Spatie\Permission\Middleware\RoleMiddleware::class,
            'permission' => \Spatie\Permission\Middleware\PermissionMiddleware::class,
            'role_or_permission' => \Spatie\Permission\Middleware\RoleOrPermissionMiddleware::class,
            
            // Custom Middleware
            'lock' => \App\Http\Middleware\LockAccount::class,
            'customer' => \App\Http\Middleware\Customer::class,
            'vendor' => \App\Http\Middleware\VendorMiddleware::class,
            'reseller' => \App\Http\Middleware\ResellerMiddleware::class,
            'admin' => \App\Http\Middleware\AdminMiddleware::class,
            'ipcheck' => \App\Http\Middleware\IpFilter::class,
            'check_refer' => \App\Http\Middleware\CheckReffer::class,
            'demo_mode' => \App\Http\Middleware\DemoModeMiddleware::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();