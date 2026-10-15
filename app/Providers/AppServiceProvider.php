<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Models\{GeneralSetting, Category, Brand, SocialMedia, Contact, CreatePage, OrderStatus, EcomPixel, GoogleTagManager, Order, PaymentGateway, Review, Vendor, ResellerWithdrawal, TiktokPixel};
use Illuminate\Support\Facades\{Config, Gate, Cache, Auth, DB};

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        // পেমেন্ট ক্যালব্যাক ৪১৯ এড়াতে CSRF exclude
        \App\Http\Middleware\VerifyCsrfToken::except([
            'aamarpay/success', 'aamarpay/fail', 'aamarpay/cancel', 'aamarpay/checkout',
            'uddoktapay/verify', 'uddoktapay/ipn', 'uddoktapay/cancel',
            'payment-success', 'payment-cancel',
            'bkash/checkout-url/callback',
        ]);

        // ================== [ Super Admin Gate Override ] ==================
        Gate::before(function ($user, $ability) {
            if (!Auth::guard('admin')->check()) {
                return null;
            }

            $adminUser = Auth::guard('admin')->user();

            if ($adminUser->id == 1) {
                return true;
            }

            $spatieRoles = $adminUser->getRoleNames()->map(fn ($role) => strtolower($role))->toArray();
            if (in_array('admin', $spatieRoles)) {
                return true;
            }

            try {
                $roleIds = DB::table('model_has_roles')
                    ->where('model_type', get_class($adminUser))
                    ->where('model_id', $adminUser->id)
                    ->pluck('role_id')
                    ->toArray();

                if (empty($roleIds)) {
                    return null;
                }

                $hasPermission = DB::table('role_has_permissions')
                    ->join('permissions', 'role_has_permissions.permission_id', '=', 'permissions.id')
                    ->whereIn('role_has_permissions.role_id', $roleIds)
                    ->where('permissions.name', $ability)
                    ->exists();

                if ($hasPermission) {
                    return true;
                }

                $hasDirectPermission = DB::table('model_has_permissions')
                    ->join('permissions', 'model_has_permissions.permission_id', '=', 'permissions.id')
                    ->where('model_has_permissions.model_type', get_class($adminUser))
                    ->where('model_has_permissions.model_id', $adminUser->id)
                    ->where('permissions.name', $ability)
                    ->exists();

                if ($hasDirectPermission) {
                    return true;
                }

                return null;
            } catch (\Exception $e) {
                return null;
            }
        });

        // ================== [ Shurjopay Dynamic Config (30 min cache) ] ==================
        try {
            $shurjopay = Cache::remember('shurjopay_gateway_config', 1800, function () {
                return PaymentGateway::where(['status' => 1, 'type' => 'shurjopay'])->first();
            });
            if ($shurjopay) {
                Config::set([
                    'shurjopay.apiCredentials.username'   => $shurjopay->username,
                    'shurjopay.apiCredentials.password'   => $shurjopay->password,
                    'shurjopay.apiCredentials.prefix'     => $shurjopay->prefix,
                    'shurjopay.apiCredentials.return_url' => $shurjopay->success_url,
                    'shurjopay.apiCredentials.cancel_url' => $shurjopay->return_url,
                    'shurjopay.apiCredentials.base_url'   => $shurjopay->base_url,
                ]);
            }
        } catch (\Exception $e) {}

        // ================== [ Global View Share (Cache optimized) ] ==================
        try {
            $generalsetting = Cache::remember('general_setting', 1800, function () {
                return GeneralSetting::where('status', 1)->first();
            });
            view()->share('generalsetting', $generalsetting);
            view()->share('demoMode', filter_var(env('DEMO_MODE', false), FILTER_VALIDATE_BOOLEAN));

            $seo = Cache::remember('seo_settings', 1800, function () {
                return DB::table('seo_settings')->first();
            });
            view()->share('seo', $seo);

            $menucategories = Cache::remember('menu_categories_v4', 1800, function () {
                return Category::where('status', 1)
                    ->where('parent_id', 0)
                    ->select('id', 'name', 'slug', 'status', 'image', 'icon')
                    ->with(['subcategories' => function ($query) {
                        $query->where('status', 1)
                            ->select('id', 'slug', 'subcategoryName', 'category_id')
                            ->orderBy('subcategoryName')
                            ->with(['childcategories' => function ($q) {
                                $q->where('status', 1)
                                    ->select('id', 'slug', 'childcategoryName', 'subcategory_id')
                                    ->orderBy('childcategoryName');
                            }]);
                    }])
                    ->orderBy('id', 'ASC')
                    ->get();
            });
            view()->share('menucategories', $menucategories);

            $contact = Cache::remember('contact_info', 1800, function () {
                return Contact::where('status', 1)->first();
            });
            view()->share('contact', $contact);

            $socialicons = Cache::remember('social_icons', 1800, function () {
                return SocialMedia::where('status', 1)->get();
            });
            view()->share('socialicons', $socialicons);

            $pages = Cache::remember('pages_top', 1800, function () {
                return CreatePage::where('status', 1)->limit(3)->get();
            });
            view()->share('pages', $pages);

            $pagesright = Cache::remember('pages_right', 1800, function () {
                return CreatePage::where('status', 1)->skip(1)->limit(5)->get();
            });
            view()->share('pagesright', $pagesright);

            $cmnmenu = Cache::remember('common_menu', 1800, function () {
                return CreatePage::where('status', 1)->get();
            });
            view()->share('cmnmenu', $cmnmenu);

            $brands = Cache::remember('brands_list', 1800, function () {
                return Brand::where('status', 1)->select('id', 'name', 'slug', 'image')->orderBy('id', 'ASC')->limit(20)->get();
            });
            view()->share('brands', $brands);

            if (request()->is('admin') || request()->is('admin/*')) {
                $pending_reviews = Cache::remember('pending_reviews_count', 300, function () {
                    return Review::where('status', 'pending')->count();
                });
                view()->share('pending_reviews', $pending_reviews);

                $neworder = Cache::remember('new_order_count', 120, function () {
                    return Order::where('order_status', 1)->count();
                });
                view()->share('neworder', $neworder);

                $pendingorder = Cache::remember('pending_orders_list', 120, function () {
                    return Order::where('order_status', 1)->latest()->limit(9)->get();
                });
                view()->share('pendingorder', $pendingorder);

                $orderstatus = Cache::remember('order_status_list', 1800, function () {
                    return OrderStatus::get();
                });
                view()->share('orderstatus', $orderstatus);
            }

            $pixels = Cache::remember('pixels_list', 1800, function () {
                return EcomPixel::where('status', 1)->get();
            });
            view()->share('pixels', $pixels);

            $gtm_code = Cache::remember('gtm_code_list', 1800, function () {
                return GoogleTagManager::where('status', 1)->get();
            });
            view()->share('gtm_code', $gtm_code);

            $tiktok_pixels = Cache::remember('tiktok_pixels_list', 1800, function () {
                return TiktokPixel::where('status', 1)->get();
            });
            view()->share('tiktok_pixels', $tiktok_pixels);

            $trackingUser = null;
            if (\Illuminate\Support\Facades\Auth::guard('customer')->check()) {
                $trackingUser = \App\Support\EcommerceTrackingUser::fromCustomer(
                    \Illuminate\Support\Facades\Auth::guard('customer')->user()
                );
            }
            view()->share('trackingUser', $trackingUser);

            if (Auth::guard('admin')->check() && Auth::guard('admin')->user()->vendor_id) {
                $vendor = Vendor::find(Auth::guard('admin')->user()->vendor_id);
                view()->share('vendor', $vendor);
            }

            if (Auth::guard('admin')->check()) {
                $resellerUser = Auth::guard('admin')->user();
                $isReseller = $resellerUser->hasRole('reseller') ||
                              (isset($resellerUser->role) && strtolower($resellerUser->role) === 'reseller') ||
                              $resellerUser->getRoleNames()->contains('reseller');

                if ($isReseller) {
                    $resellerData = Cache::remember('reseller_notifications_' . $resellerUser->id, 120, function () use ($resellerUser) {
                        $pendingOrdersCount = Order::whereNotNull('reseller_profit')
                            ->where(function ($q) use ($resellerUser) {
                                $q->where('user_id', $resellerUser->id)
                                  ->orWhereHas('customer', fn ($c) => $c->where('email', $resellerUser->email));
                            })
                            ->where(fn ($q) => $q->where('order_status', '!=', '6')->where('order_status', '!=', '11'))
                            ->count();

                        $pendingWithdrawalsCount = ResellerWithdrawal::where('user_id', $resellerUser->id)
                            ->where('status', 'pending')->count();

                        $recentOrders = Order::whereNotNull('reseller_profit')
                            ->where(function ($q) use ($resellerUser) {
                                $q->where('user_id', $resellerUser->id)
                                  ->orWhereHas('customer', fn ($c) => $c->where('email', $resellerUser->email));
                            })
                            ->with(['orderdetails.product.image', 'customer', 'status'])
                            ->latest()->limit(5)->get();

                        $recentWithdrawals = ResellerWithdrawal::where('user_id', $resellerUser->id)
                            ->latest()->limit(3)->get();

                        $verificationStatus = $resellerUser->verification_status ?? 'pending';
                        $totalNotifications = $pendingOrdersCount + $pendingWithdrawalsCount;
                        if ($verificationStatus !== 'approved') {
                            $totalNotifications += 1;
                        }

                        return compact('pendingOrdersCount', 'pendingWithdrawalsCount', 'recentOrders', 'recentWithdrawals', 'verificationStatus', 'totalNotifications');
                    });

                    view()->share('resellerPendingOrders', $resellerData['pendingOrdersCount']);
                    view()->share('resellerPendingWithdrawals', $resellerData['pendingWithdrawalsCount']);
                    view()->share('resellerRecentOrders', $resellerData['recentOrders']);
                    view()->share('resellerRecentWithdrawals', $resellerData['recentWithdrawals']);
                    view()->share('resellerVerificationStatus', $resellerData['verificationStatus']);
                    view()->share('resellerTotalNotifications', $resellerData['totalNotifications']);
                }
            }
        } catch (\Exception $e) {}
    }
}
