<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

use App\Http\Controllers\Frontend\FrontendController;
use App\Http\Controllers\Frontend\ShoppingController;
use App\Http\Controllers\Frontend\CustomerController;
use App\Http\Controllers\Frontend\BkashController;
use App\Http\Controllers\Frontend\ShurjopayControllers;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\OrderController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\PermissionController;
use App\Http\Controllers\Admin\EmailSettingController;
use App\Http\Controllers\Admin\DashboardController;
use Illuminate\Support\Facades\Response;

use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\SubcategoryController;
use App\Http\Controllers\Admin\ChildcategoryController;
use App\Http\Controllers\Admin\OrderStatusController;
use App\Http\Controllers\Admin\PixelsController;
use App\Http\Controllers\Admin\TiktokPixelsController;
use App\Http\Controllers\Admin\BrandController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\ApiIntegrationController;
use App\Http\Controllers\Admin\ManualPaymentGatewayController;
use App\Http\Controllers\Admin\CronJobController;
use App\Http\Controllers\Admin\GeneralSettingController;
use App\Http\Controllers\Admin\SocialMediaController;
use App\Http\Controllers\Admin\ContactController;
use App\Http\Controllers\Admin\BannerCategoryController;
use App\Http\Controllers\Admin\BannerController;
use App\Http\Controllers\Admin\CreatePageController;
use App\Http\Controllers\Admin\CampaignController;
use App\Http\Controllers\Admin\ErrorLogController;
use App\Http\Controllers\Admin\ReviewController;
use App\Http\Controllers\Admin\CustomerManageController;
use App\Http\Controllers\Admin\VendorController;
use App\Http\Controllers\Admin\ShippingChargeController;
use App\Http\Controllers\Admin\DeliveryDivisionController;
use App\Http\Controllers\Admin\DeliveryDistrictController;
use App\Http\Controllers\Admin\DeliveryUpazilaController;
use App\Http\Controllers\Frontend\DeliveryAjaxController;
use App\Http\Controllers\Admin\ColorController;
use App\Http\Controllers\Admin\SizeController;
use App\Http\Controllers\Admin\TagManagerController;
use App\Http\Controllers\Admin\IncompleteOrderController;
use App\Http\Controllers\Frontend\UddoktaPayController;
use App\Http\Controllers\Frontend\AamarPayController;
use Illuminate\Support\Facades\Artisan;
use Brian2694\Toastr\Facades\Toastr; // যদি না থাকে তাহলে যোগ করো
use App\Http\Controllers\Admin\SitemapController;
use App\Http\Controllers\Admin\CouponController;
use App\Http\Controllers\Admin\FraudSettingController;
use App\Http\Controllers\Admin\FundController;
use App\Http\Controllers\Admin\ExpenseController;
use App\Http\Controllers\Admin\PurchaseController;
use App\Http\Controllers\Admin\SupplierController;
use App\Http\Controllers\Admin\ReportController;
use App\Http\Controllers\DigitalDownloadController;
use App\Http\Controllers\Frontend\ComplaintController;
use App\Http\Controllers\Admin\AdminComplaintController;
use App\Http\Controllers\Admin\ContactMessageController;
use App\Http\Controllers\Admin\FacebookCapiSettingController;
use App\Http\Controllers\Admin\AdsAnalyticsController;
use App\Http\Controllers\Admin\FacebookPageController;
use App\Http\Controllers\Frontend\ContactMessageController as FrontendContactMessageController;
use App\Http\Controllers\Frontend\BlogController;
use App\Http\Controllers\Admin\BlogController as AdminBlogController;
use App\Http\Controllers\Admin\PopupController;
use App\Http\Controllers\Vendor\DashboardController as VendorDashboardController;
use App\Http\Controllers\Vendor\ProductController as VendorProductController;
use App\Http\Controllers\Vendor\SettingsController as VendorSettingsController;
use App\Http\Controllers\Reseller\ResellerDashboardController;
use App\Http\Controllers\Admin\DeliveryBoyController;
use App\Http\Controllers\DeliveryBoy\AuthController as DeliveryBoyAuthController;
use App\Http\Controllers\DeliveryBoy\DashboardController as DeliveryBoyDashboardController;
use App\Http\Controllers\DeliveryBoy\OrderController as DeliveryBoyOrderController;
use App\Http\Controllers\DeliveryBoy\ProfileController as DeliveryBoyProfileController;
use App\Http\Controllers\DeliveryBoy\WalletController as DeliveryBoyWalletController;
use App\Http\Controllers\Frontend\ShatajPayController;

Route::get('admin/clear-cache', function () {
    Artisan::call('optimize:clear');
    return redirect()->back()->with('success', '✅ Cache cleared successfully!');
})->middleware(['auth:admin', 'admin', 'demo_mode'])->name('admin.clear.cache');

// Admin root route - redirect to login if not authenticated, otherwise to dashboard
Route::get('admin', function () {
    if (Auth::guard('admin')->check()) {
        return redirect()->route('admin.dashboard');
    }
    return redirect()->route('login');
})->name('admin');

Auth::routes();

// Admin Forgot Password Routes
Route::get('admin/forgot-password', [App\Http\Controllers\Admin\Auth\ForgotPasswordController::class, 'showLinkRequestForm'])->name('admin.password.request');
Route::post('admin/forgot-password', [App\Http\Controllers\Admin\Auth\ForgotPasswordController::class, 'sendResetLinkEmail'])->name('admin.password.email');

// Admin Reset Password Routes
Route::get('admin/reset-password/{token}', [App\Http\Controllers\Admin\Auth\ResetPasswordController::class, 'showResetForm'])
    ->name('admin.password.reset');
Route::post('admin/reset-password', [App\Http\Controllers\Admin\Auth\ResetPasswordController::class, 'reset'])
    ->name('admin.password.update');

Route::post('/admin/fraud-check', [App\Http\Controllers\Admin\OrderController::class, 'fraudCheck'])
    ->middleware(['auth:admin', 'admin', 'demo_mode'])
    ->name('admin.fraud.check');
Route::post('/admin/ai-order-risk-assessment', [App\Http\Controllers\Admin\OrderController::class, 'aiOrderRiskAssessment'])
    ->middleware(['auth:admin', 'admin', 'demo_mode'])
    ->name('admin.order.ai_risk_assessment');
Route::get('/cc', function() {
    Artisan::call('config:clear');
    Artisan::call('cache:clear');
    Artisan::call('route:clear');
    Artisan::call('view:clear');
    return "Cleared!";
});


Route::group(['prefix' => 'admin', 'as' => 'admin.', 'middleware' => ['demo_mode']], function () {
    
    // Popup Routes
    Route::get('/popup', [PopupController::class, 'index'])->name('popup.index');
    Route::post('/popup/store', [PopupController::class, 'store'])->name('popup.store');
    
    // Edit & Update Routes (নতুন যোগ করা হয়েছে)
    Route::get('/popup/edit/{id}', [PopupController::class, 'edit'])->name('popup.edit');
    Route::post('/popup/update', [PopupController::class, 'update'])->name('popup.update');

    // Status & Delete
    Route::post('/popup/status/{id}', [PopupController::class, 'status'])->name('popup.status');
    Route::post('/popup/delete/{id}', [PopupController::class, 'destroy'])->name('popup.destroy');

});


Route::prefix('admin')
    ->middleware(['auth:admin', 'admin', 'lock', 'check_refer', 'demo_mode'])
    ->name('admin.')
    ->group(function () {
        Route::get('/fraud-settings', [FraudSettingController::class, 'index'])->name('fraud.index');
        Route::post('/fraud-settings/update', [FraudSettingController::class, 'update'])->name('fraud.update');
        
        // Order Restriction Settings
        Route::get('/order-restriction-settings', [App\Http\Controllers\Admin\OrderRestrictionSettingController::class, 'index'])->name('order.restriction.setting.index');
        Route::post('/order-restriction-settings/update', [App\Http\Controllers\Admin\OrderRestrictionSettingController::class, 'update'])->name('order.restriction.setting.update');
    });


Route::prefix('admin')->middleware(['auth:admin', 'admin', 'demo_mode'])->group(function () {
    Route::get('/sitemap', [SitemapController::class, 'index'])->name('admin.sitemap.index');
    Route::post('/sitemap/generate', [SitemapController::class, 'generate'])->name('admin.sitemap.generate');
});

Route::post('/incomplete-order/store', [\App\Http\Controllers\Frontend\FrontendController::class, 'storeIncompleteOrder'])
    ->name('incomplete.order.store');

// RedX Webhook (CSRF excluded)
Route::post('/api/redx/webhook', [\App\Http\Controllers\Admin\RedXWebhookController::class, 'handleWebhook'])
    ->name('redx.webhook');

// Steadfast Webhook (CSRF excluded) — delivery_status | tracking_update
Route::post('/api/steadfast/webhook', [\App\Http\Controllers\Admin\SteadfastWebhookController::class, 'handle'])
    ->name('steadfast.webhook');

	
Route::get('/style.css', function () {
    $css = view('frontEnd.assets.style')->render();   // Blade থেকে CSS রেন্ডার
    return Response::make($css, 200, [
        'Content-Type'  => 'text/css',
        'Cache-Control' => 'public, max-age=3600', // ১ ঘন্টা ব্রাউজারে ক্যাশ হবে
    ]);
});

Route::get('/responsive.css', function () {
    $css = view('frontEnd.assets.responsive')->render();
    return Response::make($css, 200, [
        'Content-Type'  => 'text/css',
        'Cache-Control' => 'public, max-age=3600',
    ]);
});
Route::get('/dynamic-theme.css', function () {
    return response()
        ->view('frontEnd.assets.theme')
        ->header('Content-Type', 'text/css');
});

Route::get('/digital-download/{token}', [DigitalDownloadController::class, 'download'])
    ->name('digital.download');
/* Blog Frontend */
Route::get('/blogs', [BlogController::class, 'index'])->name('blogs');
Route::get('/blog/{slug}', [BlogController::class, 'details'])->name('blog.details');


// Vendor protected routes
Route::prefix('vendor')
    ->middleware(['vendor', 'demo_mode'])
    ->name('vendor.')
    ->group(function () {
        Route::get('/dashboard', [VendorDashboardController::class, 'index'])->name('dashboard');
        
        // Product routes
        Route::get('/products', [VendorProductController::class, 'index'])->name('products.index');
        Route::get('/products/create', [VendorProductController::class, 'create'])->name('products.create');
        Route::post('/products/store', [VendorProductController::class, 'store'])->name('products.store');
        Route::get('/products/{id}/edit', [VendorProductController::class, 'edit'])->name('products.edit');
        Route::post('/products/update', [VendorProductController::class, 'update'])->name('products.update');
        Route::post('/products/destroy', [VendorProductController::class, 'destroy'])->name('products.destroy');
        Route::post('/products/image/destroy', [VendorProductController::class, 'imgdestroy'])->name('products.image.destroy');
        
        Route::get('/orders', [VendorDashboardController::class, 'orders'])->name('orders');
        Route::get('/orders/export', [VendorDashboardController::class, 'exportOrders'])->name('orders.export');
        Route::get('/analytics', [VendorDashboardController::class, 'analytics'])->name('analytics');
        Route::get('/customers', [VendorDashboardController::class, 'customers'])->name('customers');
        
        // Settings routes
        Route::get('/settings', [VendorSettingsController::class, 'index'])->name('settings');
        Route::post('/settings/shop-info', [VendorSettingsController::class, 'updateShopInfo'])->name('settings.shop-info');
        Route::post('/settings/profile', [VendorSettingsController::class, 'updateProfile'])->name('settings.profile');
        Route::post('/settings/password', [VendorSettingsController::class, 'updatePassword'])->name('settings.password');

        // Withdrawals
        Route::get('/withdrawals', [\App\Http\Controllers\Vendor\WithdrawalController::class, 'index'])->name('withdrawals.index');
        Route::post('/withdrawals', [\App\Http\Controllers\Vendor\WithdrawalController::class, 'store'])->name('withdrawals.store');
        
        // Refunds
        Route::get('/refunds', [\App\Http\Controllers\Vendor\RefundController::class, 'index'])->name('refunds.index');
        Route::get('/refunds/{id}', [\App\Http\Controllers\Vendor\RefundController::class, 'show'])->name('refunds.show');
        
        // Wholesale Products
        Route::get('/wholesale-products', [\App\Http\Controllers\Vendor\WholesaleProductController::class, 'index'])->name('wholesale_products.index');
        Route::get('/wholesale-products/create', [\App\Http\Controllers\Vendor\WholesaleProductController::class, 'create'])->name('wholesale_products.create');
        Route::post('/wholesale-products', [\App\Http\Controllers\Vendor\WholesaleProductController::class, 'store'])->name('wholesale_products.store');
        Route::get('/wholesale-products/{id}', [\App\Http\Controllers\Vendor\WholesaleProductController::class, 'show'])->name('wholesale_products.show');
        Route::get('/wholesale-products/{id}/edit', [\App\Http\Controllers\Vendor\WholesaleProductController::class, 'edit'])->name('wholesale_products.edit');
        Route::post('/wholesale-products/{id}', [\App\Http\Controllers\Vendor\WholesaleProductController::class, 'update'])->name('wholesale_products.update');
        Route::delete('/wholesale-products/{id}', [\App\Http\Controllers\Vendor\WholesaleProductController::class, 'destroy'])->name('wholesale_products.destroy');
        Route::get('/ajax-wholesale-subcategory', [\App\Http\Controllers\Vendor\WholesaleProductController::class, 'getSubcategory'])->name('ajax.wholesale.subcategory');
        Route::get('/ajax-wholesale-childcategory', [\App\Http\Controllers\Vendor\WholesaleProductController::class, 'getChildcategory'])->name('ajax.wholesale.childcategory');
        
        // Verification
        Route::get('/verification', [\App\Http\Controllers\Vendor\VerificationController::class, 'index'])->name('verification.index');
        Route::post('/verification', [\App\Http\Controllers\Vendor\VerificationController::class, 'store'])->name('verification.store');
        
        // Logout route
        Route::post('/logout', [\App\Http\Controllers\Vendor\AuthController::class, 'logout'])->name('logout');
    });

// Vendor AJAX routes (no middleware needed for AJAX)
Route::prefix('vendor')->name('vendor.')->group(function () {
    Route::get('/ajax-product-subcategory', [VendorProductController::class, 'getSubcategory'])->name('ajax.subcategory');
    Route::get('/ajax-product-childcategory', [VendorProductController::class, 'getChildcategory'])->name('ajax.childcategory');
});

// Reseller Public Landing Page (no auth)
Route::get('/r/{slug}', [\App\Http\Controllers\Reseller\PublicLandingController::class, 'show'])->name('reseller.landing.public');
Route::get('/r/{slug}/category/{categorySlug}', [\App\Http\Controllers\Reseller\PublicLandingController::class, 'category'])->name('reseller.landing.category');
Route::get('/r/{slug}/subcategory/{subcategorySlug}', [\App\Http\Controllers\Reseller\PublicLandingController::class, 'subcategory'])->name('reseller.landing.subcategory');
Route::get('/r/{slug}/product/{productSlug}', [\App\Http\Controllers\Reseller\PublicLandingController::class, 'product'])->name('reseller.landing.product');

Route::post('/r/{slug}/cart/add', [\App\Http\Controllers\Reseller\LandingOrderController::class, 'addToCart'])->name('reseller.landing.cart.add');
Route::get('/r/{slug}/cart/remove/{productId}', [\App\Http\Controllers\Reseller\LandingOrderController::class, 'removeFromCart'])->name('reseller.landing.cart.remove');
Route::get('/r/{slug}/order', [\App\Http\Controllers\Reseller\LandingOrderController::class, 'orderForm'])->name('reseller.landing.order');
Route::post('/r/{slug}/order', [\App\Http\Controllers\Reseller\LandingOrderController::class, 'storeOrder'])->name('reseller.landing.order.store');
Route::post('/r/{slug}/order/resend-otp', [\App\Http\Controllers\Reseller\LandingOrderController::class, 'resendCheckoutOtp'])->name('reseller.landing.order.resend_otp');
Route::get('/r/{slug}/order/success/{orderId}', [\App\Http\Controllers\Reseller\LandingOrderController::class, 'orderSuccess'])->name('reseller.landing.order.success');
Route::get('/r/{slug}/order-track', [\App\Http\Controllers\Reseller\LandingOrderController::class, 'orderTrack'])->name('reseller.landing.order-track');
Route::get('/r/{slug}/order-track/result', [\App\Http\Controllers\Reseller\LandingOrderController::class, 'orderTrackResult'])->name('reseller.landing.order-track.result');
Route::get('/r/{slug}/contact', [\App\Http\Controllers\Reseller\LandingContactController::class, 'show'])->name('reseller.landing.contact');
Route::post('/r/{slug}/contact', [\App\Http\Controllers\Reseller\LandingContactController::class, 'store'])->name('reseller.landing.contact.store');
Route::post('/r/{slug}/newsletter/subscribe', [\App\Http\Controllers\Reseller\LandingNewsletterController::class, 'subscribe'])->name('reseller.landing.newsletter.subscribe');

Route::get('/gateway/payment/callback', [ShatajPayController::class , 'paymentCallbackUrl'])->name('payment.callback.management');

// Reseller protected routes
Route::prefix('reseller')
    ->middleware(['auth:admin', 'reseller', 'demo_mode'])
    ->name('reseller.')
    ->group(function () {
        Route::get('/dashboard', [ResellerDashboardController::class, 'index'])->name('dashboard');
        
        // Product Catalog
        Route::get('/products', [\App\Http\Controllers\Reseller\ProductCatalogController::class, 'index'])->name('products.index');
        Route::get('/products/{slug}', [\App\Http\Controllers\Reseller\ProductCatalogController::class, 'show'])->name('products.show');
        
        // Cart
        Route::post('/cart/add', [\App\Http\Controllers\Reseller\ResellerCartController::class, 'addToCart'])->name('cart.add');
        Route::post('/cart/add-ajax', [\App\Http\Controllers\Reseller\ResellerCartController::class, 'addToCartAjax'])->name('cart.add.ajax');
        
        // Checkout
        Route::get('/checkout', [\App\Http\Controllers\Reseller\ResellerCheckoutController::class, 'index'])->name('checkout');
        Route::post('/checkout', [\App\Http\Controllers\Reseller\ResellerCheckoutController::class, 'store'])->name('checkout.store');
        Route::post('/checkout/resend-otp', [\App\Http\Controllers\Reseller\ResellerCheckoutController::class, 'checkout_resend_otp'])->name('checkout.resend_otp');
        Route::post('/checkout/cancel-otp', [\App\Http\Controllers\Reseller\ResellerCheckoutController::class, 'checkout_cancel_otp'])->name('checkout.cancel_otp');
        
        // Order Success
        Route::get('/order-success/{id}', [\App\Http\Controllers\Reseller\ResellerOrderSuccessController::class, 'show'])->name('order.success');
        
        Route::get('/orders', [ResellerDashboardController::class, 'orders'])->name('orders');
        Route::get('/customers', [ResellerDashboardController::class, 'customers'])->name('customers');
        Route::get('/wallet', [ResellerDashboardController::class, 'wallet'])->name('wallet');
        Route::get('/deposit', [\App\Http\Controllers\Reseller\ResellerDepositController::class, 'index'])->name('deposit');
        Route::post('/deposit', [\App\Http\Controllers\Reseller\ResellerDepositController::class, 'store'])->name('deposit.store');
        Route::get('/withdrawals', [\App\Http\Controllers\Reseller\WithdrawalController::class, 'index'])->name('withdrawals.index');
        Route::post('/withdrawals', [\App\Http\Controllers\Reseller\WithdrawalController::class, 'store'])->name('withdrawals.store');
        Route::get('/verification', [\App\Http\Controllers\Reseller\VerificationController::class, 'index'])->name('verification.index');
        Route::post('/verification', [\App\Http\Controllers\Reseller\VerificationController::class, 'store'])->name('verification.store');
        Route::get('/settings', [\App\Http\Controllers\Reseller\SettingsController::class, 'index'])->name('settings');
        Route::get('/landing', [\App\Http\Controllers\Reseller\LandingPageController::class, 'index'])->name('landing.index');
        Route::post('/landing', [\App\Http\Controllers\Reseller\LandingPageController::class, 'store'])->name('landing.store');
        Route::get('/landing/products', [\App\Http\Controllers\Reseller\LandingProductController::class, 'index'])->name('landing.products');
        Route::get('/landing/products/add', [\App\Http\Controllers\Reseller\LandingProductController::class, 'addForm'])->name('landing.products.add');
        Route::post('/landing/products/add', [\App\Http\Controllers\Reseller\LandingProductController::class, 'add'])->name('landing.products.add.store');
        Route::post('/landing/products/update-price', [\App\Http\Controllers\Reseller\LandingProductController::class, 'updatePrice'])->name('landing.products.update-price');
        Route::post('/landing/products/remove/{productId}', [\App\Http\Controllers\Reseller\LandingProductController::class, 'remove'])->name('landing.products.remove');
        
        // Fraud Check
        Route::get('/fraud-check', [\App\Http\Controllers\Reseller\ResellerFraudController::class, 'manualFraudCheckPage'])->name('fraud.page');
        Route::post('/fraud-check', [\App\Http\Controllers\Reseller\ResellerFraudController::class, 'manualFraudCheck'])->name('fraud.check');
        Route::post('/settings/profile', [\App\Http\Controllers\Reseller\SettingsController::class, 'updateProfile'])->name('settings.profile');
        Route::post('/settings/password', [\App\Http\Controllers\Reseller\SettingsController::class, 'updatePassword'])->name('settings.password');
        Route::post('/logout', [\App\Http\Controllers\Reseller\AuthController::class, 'logout'])->name('logout');
    });

Route::prefix('admin')
    ->middleware(['auth', 'demo_mode'])
    ->name('admin.')
    ->group(function () {

        // Blog Management
        Route::get('/blogs', [AdminBlogController::class, 'index'])
            ->name('blog.index');

        Route::get('/blog/create', [AdminBlogController::class, 'create'])
            ->name('blog.create');

        Route::post('/blog/store', [AdminBlogController::class, 'store'])
            ->name('blog.store');

        Route::get('/blog/edit/{id}', [AdminBlogController::class, 'edit'])
            ->name('blog.edit');

        Route::post('/blog/update/{id}', [AdminBlogController::class, 'update'])
            ->name('blog.update');

        Route::get('/blog/delete/{id}', [AdminBlogController::class, 'delete'])
            ->name('blog.delete');
    });

	
Route::get('/complaint', function () {
    $contact = \App\Models\Contact::where('status',1)->first();
    $cmnmenu = \App\Models\CreatePage::where('status',1)->get();
    return view('frontEnd.layouts.pages.complaint', compact('contact','cmnmenu'));
})->name('complaint');

Route::post('/complaint-store', [ComplaintController::class, 'store'])
    ->name('complaint.store');

Route::post('/gemini-chat/send', [\App\Http\Controllers\Frontend\GeminiCustomerChatController::class, 'send'])
    ->middleware('throttle:30,1')
    ->name('customer.gemini_chat.send');
Route::post('/gemini-chat/clear', [\App\Http\Controllers\Frontend\GeminiCustomerChatController::class, 'clear'])
    ->name('customer.gemini_chat.clear');
Route::post('/gemini-chat/complaint', [\App\Http\Controllers\Frontend\GeminiCustomerChatController::class, 'complaint'])
    ->middleware('throttle:10,1')
    ->name('customer.gemini_chat.complaint');
// Admin complaints
Route::get('/admin/complaints', [AdminComplaintController::class, 'index'])
    ->middleware(['auth:admin', 'admin'])->name('backEnd.complaints.index');

Route::post('/admin/complaints/{id}/status', [AdminComplaintController::class, 'updateStatus'])
    ->middleware(['auth:admin', 'admin', 'demo_mode'])->name('backEnd.complaints.status');

Route::delete('/admin/complaints/{id}', [AdminComplaintController::class, 'destroy'])
    ->middleware(['auth:admin', 'admin', 'demo_mode'])->name('backEnd.complaints.destroy');


Route::post('cart/apply-coupon', [ShoppingController::class, 'applyCoupon'])->name('coupon.apply');
Route::get('cart/remove-coupon', [ShoppingController::class, 'removeCoupon'])->name('coupon.remove');
Route::prefix('admin')->middleware(['auth:admin', 'admin', 'demo_mode'])->group(function () {
    // Fund Routes
    Route::get('/fund', [FundController::class, 'index'])->name('admin.fund.index');
    Route::post('/fund/add', [FundController::class, 'add'])->name('admin.fund.add');
    Route::post('/fund/withdraw', [FundController::class, 'withdraw'])->name('admin.fund.withdraw');
    Route::get('/fund/export', [FundController::class, 'export'])->name('admin.fund.export');
    Route::get('/fund/logs', [FundController::class, 'logs'])->name('admin.fund.logs');
    Route::get('/fund/{id}/edit', [FundController::class, 'edit'])->name('admin.fund.edit');
    Route::post('/fund/{id}/update', [FundController::class, 'update'])->name('admin.fund.update');
    Route::delete('/fund/{id}', [FundController::class, 'destroy'])->name('admin.fund.destroy');

    // Vendor withdrawals
    Route::get('/vendor-withdrawals', [\App\Http\Controllers\Admin\VendorWithdrawalController::class, 'index'])->name('admin.vendor.withdrawals.index');
    Route::post('/vendor-withdrawals/{id}/approve', [\App\Http\Controllers\Admin\VendorWithdrawalController::class, 'approve'])->name('admin.vendor.withdrawals.approve');
    Route::post('/vendor-withdrawals/{id}/reject', [\App\Http\Controllers\Admin\VendorWithdrawalController::class, 'reject'])->name('admin.vendor.withdrawals.reject');

    // Expense Routes
    Route::get('/expenses', [ExpenseController::class,'index'])->name('admin.expenses.index');
    Route::post('/expenses/store', [ExpenseController::class,'store'])->name('admin.expenses.store');
    Route::get('/expenses/logs', [ExpenseController::class,'logs'])->name('admin.expenses.logs');
    Route::get('/expenses/{id}/edit', [ExpenseController::class,'edit'])->name('admin.expenses.edit');
    Route::post('/expenses/{id}/update', [ExpenseController::class,'update'])->name('admin.expenses.update');
    Route::delete('/expenses/{id}', [ExpenseController::class,'destroy'])->name('admin.expenses.destroy');
    Route::get('/expenses/export', [ExpenseController::class,'export'])->name('admin.expenses.export');
});

	

// ✅ উদ্যোক্তা পে (UddoktaPay) রাউট
Route::get('/uddoktapay/checkout', [UddoktaPayController::class, 'checkout'])->name('uddoktapay.checkout');
Route::get('/uddoktapay/deposit/checkout/{deposit_id}', [UddoktaPayController::class, 'depositCheckout'])->name('uddoktapay.deposit.checkout');
Route::get('/uddoktapay/verify', [UddoktaPayController::class, 'verify'])->name('uddoktapay.verify');
Route::get('/uddoktapay/cancel', [UddoktaPayController::class, 'cancel'])->name('uddoktapay.cancel');
Route::post('/uddoktapay/ipn', [UddoktaPayController::class, 'ipn'])->name('uddoktapay.ipn');

// ✅ aamarPay রাউট (GET এবং POST দুটোই support করে)
Route::match(['get', 'post'], '/aamarpay/checkout', [AamarPayController::class, 'checkout'])->name('aamarpay.checkout');
Route::match(['get', 'post'], '/aamarpay/success', [AamarPayController::class, 'success'])->name('aamarpay.success');
Route::match(['get', 'post'], '/aamarpay/fail', [AamarPayController::class, 'fail'])->name('aamarpay.fail');
Route::get('/aamarpay/cancel', [AamarPayController::class, 'cancel'])->name('aamarpay.cancel');

// ✅ অর্ডার সফলতার রাউট (CustomerController থেকে)
Route::get('customer/order-success/{id}', [CustomerController::class, 'order_success'])
     ->name('customer.order_success');
	 
	 // ✅ Manual Payment Status Change
Route::post('admin/order/update-payment-status', [App\Http\Controllers\Admin\OrderController::class, 'updatePaymentStatus'])
     ->middleware(['auth:admin', 'admin', 'demo_mode'])->name('admin.order.updatePaymentStatus');

// ✅ Manual Order Status Change (from invoice page)
Route::post('admin/order/update-single-status', [App\Http\Controllers\Admin\OrderController::class, 'updateSingleStatus'])
     ->middleware(['auth:admin', 'admin', 'demo_mode'])->name('admin.order.updateSingleStatus');

Route::post('admin/order/update-note', [\App\Http\Controllers\Admin\OrderController::class, 'updateNote'])
    ->middleware(['auth:admin', 'admin', 'demo_mode'])->name('admin.order.update_note');


// Admin Routes
Route::prefix('admin')->middleware(['auth:admin', 'admin', 'demo_mode'])->group(function(){
    // ইনকমপ্লিট অর্ডার লিস্ট
    Route::get('/incomplete-orders', [IncompleteOrderController::class, 'index'])
        ->name('admin.incomplete-orders.index');

    // ✅ ইনকমপ্লিট অর্ডার থেকে Accept করে অর্ডারে নিয়ে যাও
    Route::post('/incomplete-orders/{id}/accept', [IncompleteOrderController::class, 'accept'])
        ->name('admin.incomplete-orders.accept');

    // ইনকমপ্লিট অর্ডার ডিলিট
    Route::delete('/incomplete-orders/{id}', [IncompleteOrderController::class, 'destroy'])
        ->name('admin.incomplete-orders.destroy');

    // Reseller Orders Management
    Route::get('/reseller-orders', [\App\Http\Controllers\Admin\ResellerOrderController::class, 'index'])
        ->name('admin.reseller-orders.index');
    Route::post('/reseller-orders/update-status', [\App\Http\Controllers\Admin\ResellerOrderController::class, 'updateStatus'])
        ->name('admin.reseller-orders.update-status');
    Route::post('/reseller-orders/bulk-update-status', [\App\Http\Controllers\Admin\ResellerOrderController::class, 'bulkUpdateStatus'])
        ->name('admin.reseller-orders.bulk-update-status');
});

// Manual Fraud Check Routes
Route::get('admin/manual-fraud-check', [App\Http\Controllers\Admin\OrderController::class, 'manualFraudCheckPage'])->middleware(['auth:admin', 'admin'])->name('manualFraud.page');
Route::post('admin/manual-fraud-check', [App\Http\Controllers\Admin\OrderController::class, 'manualFraudCheck'])->middleware(['auth:admin', 'admin', 'demo_mode'])->name('manualFraud.check');

// Duplicate Order Check Routes
Route::post('/admin/duplicate-order-check', [App\Http\Controllers\Admin\OrderController::class, 'duplicateOrderCheck'])
    ->middleware(['auth:admin', 'admin', 'demo_mode'])->name('admin.duplicate.order.check');
Route::get('admin/manual-duplicate-order-check', [App\Http\Controllers\Admin\OrderController::class, 'manualDuplicateOrderCheckPage'])->middleware(['auth:admin', 'admin'])->name('manualDuplicateOrder.page');
Route::post('admin/manual-duplicate-order-check', [App\Http\Controllers\Admin\OrderController::class, 'manualDuplicateOrderCheck'])->middleware(['auth:admin', 'admin', 'demo_mode'])->name('manualDuplicateOrder.check');


Route::get('/ajax/delivery/districts/{division}', [DeliveryAjaxController::class, 'districts'])->name('ajax.delivery.districts');
Route::get('/ajax/delivery/upazilas/{district}', [DeliveryAjaxController::class, 'upazilas'])->name('ajax.delivery.upazilas');

Route::get('/admin/dashboard', [App\Http\Controllers\Admin\DashboardController::class, 'index'])->name('dashboard');

Route::get('/controller', function() {
    Artisan::call('make:controller Admin/TagManagerController');
    return "Controller Done!";
});

// Admin custom SMS
Route::get('/admin/sms/custom-send', [App\Http\Controllers\Admin\ApiIntegrationController::class, 'sms_custom_send_page'])->middleware(['auth:admin', 'admin'])->name('admin.sms.custom.page');
Route::post('/admin/sms/custom-send', [App\Http\Controllers\Admin\ApiIntegrationController::class, 'sms_custom_send'])->middleware(['auth:admin', 'admin', 'demo_mode'])->name('admin.sms.custom.send');


Route::group(['namespace'=>'Frontend', 'middleware' => ['ipcheck','check_refer']], function() {
    Route::get('/', [FrontendController::class, 'index'])->name('home');
	
	    Route::get('brand/{slug}', [FrontendController::class, 'brand'])
        ->name('brand.products');
    Route::get('shop/{slug}', [FrontendController::class, 'vendorShop'])->name('vendor.shop');
    Route::get('category/{category}', [FrontendController::class, 'category'])->name('category');

    Route::get('subcategory/{subcategory}', [FrontendController::class, 'subcategory'])->name('subcategory');

    Route::get('products/{slug}', [FrontendController::class, 'products'])->name('products');
    Route::get('wholesale-products', [FrontendController::class, 'wholesaleProducts'])->name('wholesale.products');

    Route::get('hot-deals', [FrontendController::class, 'hotdeals'])->name('hotdeals');
    Route::get('flash-sales', [FrontendController::class, 'flashsales'])->name('flashsales');
    Route::get('sellers', [FrontendController::class, 'sellers'])->name('sellers');
    Route::get('shop', [FrontendController::class, 'shop'])->name('shop');
    Route::get('all-products', [FrontendController::class, 'shop'])->name('all.products');
    Route::get('livesearch', [FrontendController::class, 'livesearch'])->name('livesearch');
    Route::get('search', [FrontendController::class, 'search'])->name('search');
    Route::get('product/{id}', [FrontendController::class, 'details'])->name('product');
    Route::get('product-reviews/load', [FrontendController::class, 'loadProductReviews'])->name('product.reviews.load');
    Route::get('sales-notifications', [FrontendController::class, 'salesNotifications'])->name('sales.notifications');
    Route::get('quick-view', [FrontendController::class, 'quickview'])->name('quickview');
    Route::get('/shipping-charge', [FrontendController::class, 'shipping_charge'])->name('shipping.charge');
    Route::get('/page/{slug}', [FrontendController::class, 'page'])->name('page');
    Route::get('districts', [FrontendController::class, 'districts'])->name('districts');
    Route::get('/campaign/{slug}', [FrontendController::class, 'campaign'])->name('campaign');
    Route::get('/offer', [FrontendController::class, 'offers'])->name('offers');
     Route::get('/payment-success', [FrontEndController::class, 'payment_success'])->name('payment_success');
    Route::get('/payment-cancel', [FrontEndController::class, 'payment_cancel'])->name('payment_cancel');




Route::post('/cart/store', [FrontendController::class, 'cartStore'])->name('cart.store');


    Route::get('/add-to-cart/{id}/{qty}', [ShoppingController::class, 'addTocartGet']);

    Route::get('shop/cart', [ShoppingController::class, 'cart_show'])->name('cart.show');
    Route::get('cart/remove', [ShoppingController::class, 'cart_remove'])->name('cart.remove');
    Route::get('cart/count', [ShoppingController::class, 'cart_count'])->name('cart.count');
    Route::get('mobilecart/count', [ShoppingController::class, 'mobilecart_qty'])->name('mobile.cart.count');
    Route::get('cart/decrement', [ShoppingController::class, 'cart_decrement'])->name('cart.decrement');

    Route::get('cart/increment', [ShoppingController::class, 'cart_increment'])->name('cart.increment');
    Route::get('cart/sidebar', [ShoppingController::class, 'sidebarCart'])->name('cart.sidebar');
    Route::get('/cart/change-product', [ShoppingController::class, 'changeProduct'])->name('cart.changeProduct');
    Route::get('cart/update', [ShoppingController::class, 'cart_update'])->name('cart.update');


});

Route::group(['prefix'=>'customer','namespace'=>'Frontend', 'middleware' => ['ipcheck','check_refer']], function() {
    
	
	Route::get('/login', [CustomerController::class, 'login'])->name('customer.login');
    Route::post('/signin', [CustomerController::class, 'signin'])->name('customer.signin');
    Route::get('/register', [CustomerController::class, 'register'])->name('customer.register');
    Route::post('/store', [CustomerController::class, 'store'])->name('customer.store');
    Route::get('/verify', [CustomerController::class, 'verify'])->name('customer.verify');
    Route::post('/verify-account', [CustomerController::class, 'account_verify'])->name('customer.account.verify');
    Route::post('/resend-otp', [CustomerController::class, 'resendotp'])->name('customer.resendotp');
    Route::post('/logout', [CustomerController::class, 'logout'])->name('customer.logout');
    Route::post('/post/review', [CustomerController::class, 'review'])->name('customer.review');
    Route::get('/forgot-password', [CustomerController::class, 'forgot_password'])->name('customer.forgot.password');
    Route::post('/forgot-verify', [CustomerController::class, 'forgot_verify'])->name('customer.forgot.verify');
    Route::get('/forgot-password/reset', [CustomerController::class, 'forgot_reset'])->name('customer.forgot.reset');
    Route::post('/forgot-password/store', [CustomerController::class, 'forgot_store'])->name('customer.forgot.store');
    Route::post('/forgot-password/resendotp', [CustomerController::class, 'forgot_resend'])->name('customer.forgot.resendotp');
    Route::get('/checkout', [CustomerController::class, 'checkout'])->name('customer.checkout');
    Route::post('/order-save', [CustomerController::class, 'order_save'])->name('customer.ordersave');
    Route::post('/checkout-resend-otp', [CustomerController::class, 'checkout_resend_otp'])->name('customer.checkout.resend_otp');
    Route::post('/checkout-cancel-otp', [CustomerController::class, 'checkout_cancel_otp'])->name('customer.checkout.cancel_otp');
    Route::get('/order-success/{id}', [CustomerController::class, 'order_success'])->name('customer.order_success');

   Route::get('/order-track', [CustomerController::class, 'order_track'])->name('customer.order_track');
    Route::get('/order-track/result', [CustomerController::class, 'order_track_result'])->name('customer.order_track_result');
    

});
// customer auth
Route::group(['prefix'=>'customer','namespace'=>'Frontend','middleware' => ['customer','ipcheck','check_refer']], function() {
    
    Route::get('/account', [CustomerController::class, 'account'])->name('customer.account');
    
    Route::get('/orders', [CustomerController::class, 'orders'])->name('customer.orders');
    Route::get('/invoice', [CustomerController::class, 'invoice'])->name('customer.invoice');
    Route::get('/invoice/order-note', [CustomerController::class, 'order_note'])->name('customer.order_note');
    Route::get('/profile-edit', [CustomerController::class, 'profile_edit'])->name('customer.profile_edit');
    Route::post('/profile-update', [CustomerController::class, 'profile_update'])->name('customer.profile_update');
    Route::get('/change-password', [CustomerController::class, 'change_pass'])->name('customer.change_pass');
    Route::post('/password-update', [CustomerController::class, 'password_update'])->name('customer.password_update');
    
    // Refund Routes
    Route::get('/refunds', [\App\Http\Controllers\Frontend\RefundController::class, 'index'])->name('customer.refunds');
    Route::get('/refunds/request/{order_id}', [\App\Http\Controllers\Frontend\RefundController::class, 'create'])->name('customer.refunds.create');
    Route::post('/refunds/request', [\App\Http\Controllers\Frontend\RefundController::class, 'store'])->name('customer.refunds.store');
    Route::get('/refunds/{id}', [\App\Http\Controllers\Frontend\RefundController::class, 'show'])->name('customer.refunds.show');
    Route::delete('/refunds/{id}/cancel', [\App\Http\Controllers\Frontend\RefundController::class, 'cancel'])->name('customer.refunds.cancel');
    
});

Route::group(['namespace'=>'Frontend', 'middleware' => ['ipcheck','check_refer']], function() {
        // Contact page
    Route::get('site/contact-us', [FrontendController::class, 'contact'])
        ->name('contact');

    // Contact form submit (✅ correct place)
Route::post('contact/store',
    [FrontendContactMessageController::class, 'store']
)->name('frontend.contact.store');

    // Newsletter subscribe (footer)
Route::post('newsletter/subscribe',
    [\App\Http\Controllers\Frontend\NewsletterController::class, 'store']
)->name('frontend.newsletter.subscribe');
    Route::get('bkash/checkout-url/pay',[BkashController::class,'pay'])->name('url-pay');
Route::any('bkash/checkout-url/create',[BkashController::class,'create'])->name('url-create');
Route::get('bkash/checkout-url/callback',[BkashController::class,'callback'])->name('url-callback');
    Route::get('/payment-success', [ShurjopayControllers::class, 'payment_success'])->name('payment_success');
    Route::get('/payment-cancel', [ShurjopayControllers::class, 'payment_cancel'])->name('payment_cancel');

});

// unathenticate admin route
Route::group(['namespace'=>'Admin','prefix'=>'admin','middleware' => ['customer','ipcheck','check_refer']], function() {
    Route::get('locked', [DashboardController::class, 'locked'])->name('locked');
    Route::post('unlocked', [DashboardController::class, 'unlocked'])->name('unlocked');
});

// ajax route
Route::get('/ajax-product-subcategory', [ProductController::class, 'getSubcategory']);
Route::get('/ajax-product-childcategory', [ProductController::class, 'getChildcategory']);

// auth route
// admin route group
Route::group(['middleware' => ['auth:admin','admin','lock','check_refer','demo_mode'], 'prefix' => 'admin'], function () {
	// 🟢 Coupon Management
Route::get('coupon/manage', [CouponController::class, 'index'])->name('admin.coupons.index');
Route::get('coupon/create', [CouponController::class, 'create'])->name('admin.coupons.create');
Route::post('coupon/save', [CouponController::class, 'store'])->name('admin.coupons.store');
Route::get('coupon/{id}/edit', [CouponController::class, 'edit'])->name('admin.coupons.edit');
Route::match(['put', 'post'], 'coupon/update/{id}', [CouponController::class, 'update'])->name('admin.coupons.update');

Route::delete('coupon/destroy/{id}', [CouponController::class, 'destroy'])
     ->name('admin.coupons.destroy');

Route::get('contact-messages',
        [ContactMessageController::class, 'index']
    )->name('admin.contact.messages');

Route::post('contact-messages/status/{id}',
        [ContactMessageController::class, 'status']
    )->name('admin.contact.messages.status');

Route::delete('contact-messages/delete/{id}',
        [ContactMessageController::class, 'destroy']
    )->name('admin.contact.messages.delete');

// Newsletter Subscribers
Route::get('newsletter-subscribers',
    [\App\Http\Controllers\Admin\NewsletterSubscriberController::class, 'index']
)->name('admin.newsletter.subscribers');

Route::delete('newsletter-subscribers/delete/{id}',
    [\App\Http\Controllers\Admin\NewsletterSubscriberController::class, 'destroy']
)->name('admin.newsletter.subscribers.delete');
	 
	 
// Purchase Routes
Route::get('purchases/manage', [PurchaseController::class, 'index'])->name('purchases.index');
Route::post('purchases/store', [PurchaseController::class, 'store'])->name('purchases.store');
Route::get('purchases/logs', [PurchaseController::class, 'logs'])->name('purchases.logs');
Route::get('purchases/{id}/edit', [PurchaseController::class, 'edit'])->name('purchases.edit');
Route::post('purchases/{id}/update', [PurchaseController::class, 'update'])->name('purchases.update');
Route::delete('purchases/{id}', [PurchaseController::class, 'destroy'])->name('purchases.destroy');
Route::post('purchases/{id}/pay-due', [PurchaseController::class, 'payDue'])->name('purchases.pay_due');
Route::post('purchase-item/{id}/return', [PurchaseController::class, 'returnItem'])->name('purchases.item_return');
Route::get('purchases/{id}/invoice', [PurchaseController::class, 'invoice'])->name('purchases.invoice');
Route::get('purchases/export', [PurchaseController::class, 'export'])->name('purchases.export');
// ✅ Purchases AJAX Pagination
Route::get('purchases/ajax', [PurchaseController::class, 'ajaxIndex'])
    ->name('purchases.ajax');


// ==== REPORT ROUTES ==== //
Route::get('reports/orders',        [ReportController::class, 'orders'])->name('admin.reports.orders');
Route::get('reports/purchases',     [ReportController::class, 'purchases'])->name('admin.reports.purchases');
Route::get('reports/expenses',      [ReportController::class, 'expenses'])->name('admin.reports.expenses');
Route::get('reports/stock',         [ReportController::class, 'stock'])->name('admin.reports.stock');
Route::get('reports/profit-loss',   [ReportController::class, 'profitLoss'])->name('admin.reports.profit_loss');

    // Supplier Routes
    Route::get('suppliers/manage', [SupplierController::class, 'index'])->name('admin.suppliers.index');
    Route::post('suppliers/store', [SupplierController::class, 'store'])->name('admin.suppliers.store');
    Route::get('suppliers/{id}/edit', [SupplierController::class, 'edit'])->name('admin.suppliers.edit');
    Route::post('suppliers/{id}/update', [SupplierController::class, 'update'])->name('admin.suppliers.update');
    Route::delete('suppliers/{id}', [SupplierController::class, 'destroy'])->name('admin.suppliers.destroy');

    // CRM - Employee Management Routes
    Route::get('employees', [\App\Http\Controllers\Admin\EmployeeController::class, 'index'])->name('admin.employees.index');
    Route::get('employees/create', [\App\Http\Controllers\Admin\EmployeeController::class, 'create'])->name('admin.employees.create');
    Route::post('employees/store', [\App\Http\Controllers\Admin\EmployeeController::class, 'store'])->name('admin.employees.store');
    Route::post('employees/import-user', [\App\Http\Controllers\Admin\EmployeeController::class, 'importFromUser'])->name('admin.employees.import');
    Route::get('employees/{id}', [\App\Http\Controllers\Admin\EmployeeController::class, 'show'])->name('admin.employees.show');
    Route::get('employees/{id}/edit', [\App\Http\Controllers\Admin\EmployeeController::class, 'edit'])->name('admin.employees.edit');
    Route::post('employees/{id}/update', [\App\Http\Controllers\Admin\EmployeeController::class, 'update'])->name('admin.employees.update');
    Route::delete('employees/{id}', [\App\Http\Controllers\Admin\EmployeeController::class, 'destroy'])->name('admin.employees.destroy');

    // CRM - Attendance Routes
    Route::get('attendances', [\App\Http\Controllers\Admin\AttendanceController::class, 'index'])->name('admin.attendances.index');
    Route::get('attendances/create', [\App\Http\Controllers\Admin\AttendanceController::class, 'create'])->name('admin.attendances.create');
    Route::post('attendances/store', [\App\Http\Controllers\Admin\AttendanceController::class, 'store'])->name('admin.attendances.store');
    Route::post('attendances/bulk-mark', [\App\Http\Controllers\Admin\AttendanceController::class, 'bulkMark'])->name('admin.attendances.bulk');
    Route::get('attendances/{id}/edit', [\App\Http\Controllers\Admin\AttendanceController::class, 'edit'])->name('admin.attendances.edit');
    Route::post('attendances/{id}/update', [\App\Http\Controllers\Admin\AttendanceController::class, 'update'])->name('admin.attendances.update');
    Route::delete('attendances/{id}', [\App\Http\Controllers\Admin\AttendanceController::class, 'destroy'])->name('admin.attendances.destroy');

    // CRM - Leave Routes
    Route::get('leaves', [\App\Http\Controllers\Admin\LeaveController::class, 'index'])->name('admin.leaves.index');
    Route::get('leaves/create', [\App\Http\Controllers\Admin\LeaveController::class, 'create'])->name('admin.leaves.create');
    Route::post('leaves/store', [\App\Http\Controllers\Admin\LeaveController::class, 'store'])->name('admin.leaves.store');
    Route::post('leaves/{id}/approve', [\App\Http\Controllers\Admin\LeaveController::class, 'approve'])->name('admin.leaves.approve');
    Route::post('leaves/{id}/reject', [\App\Http\Controllers\Admin\LeaveController::class, 'reject'])->name('admin.leaves.reject');
    Route::get('leaves/{id}/edit', [\App\Http\Controllers\Admin\LeaveController::class, 'edit'])->name('admin.leaves.edit');
    Route::post('leaves/{id}/update', [\App\Http\Controllers\Admin\LeaveController::class, 'update'])->name('admin.leaves.update');
    Route::delete('leaves/{id}', [\App\Http\Controllers\Admin\LeaveController::class, 'destroy'])->name('admin.leaves.destroy');

    // CRM - Salary Routes
    Route::get('salaries', [\App\Http\Controllers\Admin\SalaryController::class, 'index'])->name('admin.salaries.index');
    Route::post('salaries/calculate', [\App\Http\Controllers\Admin\SalaryController::class, 'calculate'])->name('admin.salaries.calculate');
    Route::post('salaries/bulk-calculate', [\App\Http\Controllers\Admin\SalaryController::class, 'bulkCalculate'])->name('admin.salaries.bulk_calculate');
    Route::get('salaries/{id}', [\App\Http\Controllers\Admin\SalaryController::class, 'show'])->name('admin.salaries.show');

    // CRM - Bonus Routes
    Route::get('bonuses', [\App\Http\Controllers\Admin\BonusController::class, 'index'])->name('admin.bonuses.index');
    Route::get('bonuses/create', [\App\Http\Controllers\Admin\BonusController::class, 'create'])->name('admin.bonuses.create');
    Route::post('bonuses/store', [\App\Http\Controllers\Admin\BonusController::class, 'store'])->name('admin.bonuses.store');
    Route::post('bonuses/{id}/approve', [\App\Http\Controllers\Admin\BonusController::class, 'approve'])->name('admin.bonuses.approve');
    Route::post('bonuses/{id}/pay', [\App\Http\Controllers\Admin\BonusController::class, 'pay'])->name('admin.bonuses.pay');
    Route::post('bonuses/{id}/reject', [\App\Http\Controllers\Admin\BonusController::class, 'reject'])->name('admin.bonuses.reject');
    Route::get('bonuses/{id}/edit', [\App\Http\Controllers\Admin\BonusController::class, 'edit'])->name('admin.bonuses.edit');
    Route::post('bonuses/{id}/update', [\App\Http\Controllers\Admin\BonusController::class, 'update'])->name('admin.bonuses.update');
    Route::delete('bonuses/{id}', [\App\Http\Controllers\Admin\BonusController::class, 'destroy'])->name('admin.bonuses.destroy');

    // CRM - Salary Payment Routes
    Route::get('salary-payments', [\App\Http\Controllers\Admin\SalaryPaymentController::class, 'index'])->name('admin.salary_payments.index');
    Route::get('salary-payments/create', [\App\Http\Controllers\Admin\SalaryPaymentController::class, 'create'])->name('admin.salary_payments.create');
    Route::post('salary-payments/store', [\App\Http\Controllers\Admin\SalaryPaymentController::class, 'store'])->name('admin.salary_payments.store');
    Route::post('salary-payments/pay-from-salary/{salaryId}', [\App\Http\Controllers\Admin\SalaryPaymentController::class, 'payFromSalary'])->name('admin.salary_payments.pay_from_salary');
    Route::get('salary-payments/{id}', [\App\Http\Controllers\Admin\SalaryPaymentController::class, 'show'])->name('admin.salary_payments.show');


    Route::get('email-setting', [EmailSettingController::class, 'index'])->name('email_setting');
    Route::post('email-setting', [EmailSettingController::class, 'update'])->name('email_setting.update');
	Route::get('seo-settings', [App\Http\Controllers\Admin\SeoSettingController::class, 'index'])
        ->name('admin.seo_settings.index');

    Route::post('seo-settings', [App\Http\Controllers\Admin\SeoSettingController::class, 'update'])
        ->name('admin.seo_settings.update');

Route::get('dashboard', [DashboardController::class, 'dashboard'])->name('admin.dashboard');
    Route::get('dashboard/new-orders-popup', [DashboardController::class, 'newOrdersForPopup'])->name('admin.dashboard.new_orders_popup');
    Route::post('dashboard/dismiss-new-orders-popup', [DashboardController::class, 'dismissNewOrdersPopup'])->name('admin.dashboard.dismiss_new_orders_popup');
    Route::get('orders/poll-new', [DashboardController::class, 'pollNewOrders'])->name('admin.orders.poll_new');

    Route::get('change-password', [DashboardController::class, 'changepassword'])->name('change_password');
    Route::post('new-password', [DashboardController::class, 'newpassword'])->name('new_password');

    // users route 
    Route::get('users/manage', [UserController::class,'index'])->name('users.index');
    Route::get('users/create', [UserController::class,'create'])->name('users.create');
    Route::post('users/save', [UserController::class,'store'])->name('users.store');
    Route::get('users/{id}/edit', [UserController::class,'edit'])->name('users.edit');
    Route::post('users/update', [UserController::class,'update'])->name('users.update');
    Route::post('users/inactive', [UserController::class,'inactive'])->name('users.inactive');
    Route::post('users/active', [UserController::class,'active'])->name('users.active');
    Route::post('users/destroy', [UserController::class,'destroy'])->name('users.destroy');
    
    // roles
    Route::get('roles/manage', [RoleController::class,'index'])->name('roles.index');
    Route::get('roles/{id}/show', [RoleController::class,'show'])->name('roles.show');
    Route::get('roles/create', [RoleController::class,'create'])->name('roles.create');
    Route::post('roles/save', [RoleController::class,'store'])->name('roles.store');
    Route::get('roles/{id}/edit', [RoleController::class,'edit'])->name('roles.edit');
    Route::post('roles/update', [RoleController::class,'update'])->name('roles.update');
    Route::post('roles/destroy', [RoleController::class,'destroy'])->name('roles.destroy');

    // permissions
    Route::get('permissions/manage', [PermissionController::class,'index'])->name('permissions.index');
    Route::get('permissions/{id}/show', [PermissionController::class,'show'])->name('permissions.show');
    Route::get('permissions/create', [PermissionController::class,'create'])->name('permissions.create');
    Route::post('permissions/save', [PermissionController::class,'store'])->name('permissions.store');
    Route::get('permissions/{id}/edit', [PermissionController::class,'edit'])->name('permissions.edit');
    Route::post('permissions/update', [PermissionController::class,'update'])->name('permissions.update');
    Route::post('permissions/destroy', [PermissionController::class,'destroy'])->name('permissions.destroy');

    // categories
    Route::get('categories/manage', [CategoryController::class,'index'])->name('categories.index');
    Route::get('categories/{id}/show', [CategoryController::class,'show'])->name('categories.show');
    Route::get('categories/create', [CategoryController::class,'create'])->name('categories.create');
    Route::post('categories/save', [CategoryController::class,'store'])->name('categories.store');
    Route::get('categories/{id}/edit', [CategoryController::class,'edit'])->name('categories.edit');
    Route::post('categories/update', [CategoryController::class,'update'])->name('categories.update');
    Route::post('categories/inactive', [CategoryController::class,'inactive'])->name('categories.inactive');
    Route::post('categories/active', [CategoryController::class,'active'])->name('categories.active');
    Route::post('categories/destroy', [CategoryController::class,'destroy'])->name('categories.destroy');

    // Subcategories
    Route::get('subcategories/manage', [SubcategoryController::class,'index'])->name('subcategories.index');
    Route::get('subcategories/{id}/show', [SubcategoryController::class,'show'])->name('subcategories.show');
    Route::get('subcategories/create', [SubcategoryController::class,'create'])->name('subcategories.create');
    Route::post('subcategories/save', [SubcategoryController::class,'store'])->name('subcategories.store');
    Route::get('subcategories/{id}/edit', [SubcategoryController::class,'edit'])->name('subcategories.edit');
    Route::post('subcategories/update', [SubcategoryController::class,'update'])->name('subcategories.update');
    Route::post('subcategories/inactive', [SubcategoryController::class,'inactive'])->name('subcategories.inactive');
    Route::post('subcategories/active', [SubcategoryController::class,'active'])->name('subcategories.active');
    Route::post('subcategories/destroy', [SubcategoryController::class,'destroy'])->name('subcategories.destroy');

    // Childcategories
    Route::get('childcategories/manage', [ChildcategoryController::class,'index'])->name('childcategories.index');
    Route::get('childcategories/{id}/show', [ChildcategoryController::class,'show'])->name('childcategories.show');
    Route::get('childcategories/create', [ChildcategoryController::class,'create'])->name('childcategories.create');
    Route::post('childcategories/save', [ChildcategoryController::class,'store'])->name('childcategories.store');
    Route::get('childcategories/{id}/edit', [ChildcategoryController::class,'edit'])->name('childcategories.edit');
    Route::post('childcategories/update', [ChildcategoryController::class,'update'])->name('childcategories.update');
    Route::post('childcategories/inactive', [ChildcategoryController::class,'inactive'])->name('childcategories.inactive');
    Route::post('childcategories/active', [ChildcategoryController::class,'active'])->name('childcategories.active');
    Route::post('childcategories/destroy', [ChildcategoryController::class,'destroy'])->name('childcategories.destroy');
    
     // paymentgeteway
    Route::get('paymentgeteway/manage', [ApiIntegrationController::class,'pay_manage'])->name('paymentgeteway.manage');
    Route::post('paymentgeteway/save', [ApiIntegrationController::class,'pay_update'])->name('paymentgeteway.update');
    Route::get('paymentgeteway/manual-manage', [ManualPaymentGatewayController::class, 'index'])->name('manual-payment-gateway.manage');
    Route::post('paymentgeteway/manual-save', [ManualPaymentGatewayController::class, 'store'])->name('manual-payment-gateway.store');
    Route::post('paymentgeteway/manual-update', [ManualPaymentGatewayController::class, 'update'])->name('manual-payment-gateway.update');
    Route::post('paymentgeteway/manual-destroy', [ManualPaymentGatewayController::class, 'destroy'])->name('manual-payment-gateway.destroy');
    
     // smsgeteway
    Route::get('smsgeteway/manage', [ApiIntegrationController::class,'sms_manage'])->name('smsgeteway.manage');
    Route::post('smsgeteway/save', [ApiIntegrationController::class,'sms_update'])->name('smsgeteway.update');
    Route::post('smsgeteway/toggle-field', [ApiIntegrationController::class,'sms_toggle_field'])->name('smsgeteway.toggle');
    Route::get('smsgeteway/balance', [ApiIntegrationController::class,'sms_balance'])->name('smsgeteway.balance');
    Route::get('bdcourier/my-plan', [ApiIntegrationController::class,'bdcourier_my_plan'])->name('bdcourier.myplan');
    Route::get('steadfast/dashboard-widget', [ApiIntegrationController::class,'steadfast_dashboard_widget'])->name('steadfast.dashboard.widget');
    
    // courierapi
    Route::get('courierapi/manage', [ApiIntegrationController::class,'courier_manage'])->name('courierapi.manage');

    // Cron Job Management
    Route::get('cron-jobs',                    [CronJobController::class, 'index']          )->name('admin.cron.index');
    Route::post('cron-jobs/{id}/toggle',       [CronJobController::class, 'toggle']         )->name('admin.cron.toggle');
    Route::post('cron-jobs/{id}/settings',     [CronJobController::class, 'update_settings'])->name('admin.cron.settings');
    Route::post('cron-jobs/{id}/run-now',      [CronJobController::class, 'run_now']        )->name('admin.cron.run_now');
    Route::get('cron-jobs/{id}/status',        [CronJobController::class, 'status']         )->name('admin.cron.status');
    Route::post('courierapi/save', [ApiIntegrationController::class,'courier_update'])->name('courierapi.update');
    Route::post('courierapi/pathao-generate-token', [ApiIntegrationController::class,'pathao_generate_token'])->name('admin.courierapi.pathao.generate_token');
    
    // RedX Areas AJAX
    Route::get('redx/areas', [OrderController::class, 'redxAreas'])->name('admin.redx.areas');
    Route::get('redx/pickup-stores', [OrderController::class, 'redxPickupStores'])->name('admin.redx.pickup-stores');

    // attribute
    Route::get('orderstatus/manage', [OrderStatusController::class,'index'])->name('orderstatus.index');
    Route::get('orderstatus/{id}/show', [OrderStatusController::class,'show'])->name('orderstatus.show');
    Route::get('orderstatus/create', [OrderStatusController::class,'create'])->name('orderstatus.create');
    Route::post('orderstatus/save', [OrderStatusController::class,'store'])->name('orderstatus.store');
    Route::get('orderstatus/{id}/edit', [OrderStatusController::class,'edit'])->name('orderstatus.edit');
    Route::post('orderstatus/update', [OrderStatusController::class,'update'])->name('orderstatus.update');
    Route::post('orderstatus/inactive', [OrderStatusController::class,'inactive'])->name('orderstatus.inactive');
    Route::post('orderstatus/active', [OrderStatusController::class,'active'])->name('orderstatus.active');
    Route::post('orderstatus/destroy', [OrderStatusController::class,'destroy'])->name('orderstatus.destroy');
    
    // pixels
    Route::get('pixels/manage', [PixelsController::class,'index'])->name('pixels.index');
    Route::get('pixels/{id}/show', [PixelsController::class,'show'])->name('pixels.show');
    Route::get('pixels/create', [PixelsController::class,'create'])->name('pixels.create');
    Route::post('pixels/save', [PixelsController::class,'store'])->name('pixels.store');
    Route::get('pixels/{id}/edit', [PixelsController::class,'edit'])->name('pixels.edit');
    Route::post('pixels/update', [PixelsController::class,'update'])->name('pixels.update');
    Route::post('pixels/inactive', [PixelsController::class,'inactive'])->name('pixels.inactive');
    Route::post('pixels/active', [PixelsController::class,'active'])->name('pixels.active');
    Route::post('pixels/destroy', [PixelsController::class,'destroy'])->name('pixels.destroy');
    
    // TikTok Pixel
    Route::get('tiktok-pixels/manage', [TiktokPixelsController::class, 'index'])->name('tiktok.pixels.index');
    Route::get('tiktok-pixels/create', [TiktokPixelsController::class, 'create'])->name('tiktok.pixels.create');
    Route::post('tiktok-pixels/save', [TiktokPixelsController::class, 'store'])->name('tiktok.pixels.store');
    Route::get('tiktok-pixels/{id}/edit', [TiktokPixelsController::class, 'edit'])->name('tiktok.pixels.edit');
    Route::post('tiktok-pixels/update', [TiktokPixelsController::class, 'update'])->name('tiktok.pixels.update');
    Route::post('tiktok-pixels/inactive', [TiktokPixelsController::class, 'inactive'])->name('tiktok.pixels.inactive');
    Route::post('tiktok-pixels/active', [TiktokPixelsController::class, 'active'])->name('tiktok.pixels.active');
    Route::post('tiktok-pixels/destroy', [TiktokPixelsController::class, 'destroy'])->name('tiktok.pixels.destroy');

    // Facebook Conversion API settings
    Route::get('facebook-capi/settings', [FacebookCapiSettingController::class, 'edit'])->name('admin.facebook_capi.edit');
    Route::post('facebook-capi/settings', [FacebookCapiSettingController::class, 'update'])->name('admin.facebook_capi.update');
    Route::get('gemini-ai/settings', [App\Http\Controllers\Admin\GeminiAiSettingController::class, 'edit'])->name('admin.gemini_ai.edit');
    Route::post('gemini-ai/settings', [App\Http\Controllers\Admin\GeminiAiSettingController::class, 'update'])->name('admin.gemini_ai.update');
    Route::post('gemini-ai/test', [App\Http\Controllers\Admin\GeminiAiSettingController::class, 'test'])->name('admin.gemini_ai.test');
    Route::get('gemini-chat', [App\Http\Controllers\Admin\GeminiAdminChatController::class, 'index'])->name('admin.gemini_chat.index');
    Route::post('gemini-chat/send', [App\Http\Controllers\Admin\GeminiAdminChatController::class, 'send'])->name('admin.gemini_chat.send');
    Route::post('gemini-chat/clear', [App\Http\Controllers\Admin\GeminiAdminChatController::class, 'clear'])->name('admin.gemini_chat.clear');
    Route::post('gemini-chat/refresh-context', [App\Http\Controllers\Admin\GeminiAdminChatController::class, 'refreshContext'])->name('admin.gemini_chat.refresh_context');

    // Ads Analytics - separate pages for each platform
    Route::get('ads-analytics', [AdsAnalyticsController::class, 'dashboard'])->name('admin.ads_analytics.dashboard');
    Route::get('ads-analytics/facebook', [AdsAnalyticsController::class, 'facebook'])->name('admin.ads_analytics.facebook');
    Route::get('ads-analytics/google', [AdsAnalyticsController::class, 'google'])->name('admin.ads_analytics.google');
    Route::get('ads-analytics/tiktok', [AdsAnalyticsController::class, 'tiktok'])->name('admin.ads_analytics.tiktok');
    Route::get('ads-analytics/live', [AdsAnalyticsController::class, 'liveData'])->name('admin.ads_analytics.live_data');
    Route::get('ads-analytics/settings', [AdsAnalyticsController::class, 'settings'])->name('admin.ads_analytics.settings');
    Route::post('ads-analytics/settings', [AdsAnalyticsController::class, 'saveSettings'])->name('admin.ads_analytics.save_settings');

    // Facebook Page - Auto post products
    Route::get('facebook-page/settings', [FacebookPageController::class, 'settings'])->name('admin.facebook_page.settings');
    Route::post('facebook-page/settings', [FacebookPageController::class, 'saveSettings'])->name('admin.facebook_page.save_settings');
    Route::post('facebook-page/post-product/{product}', [FacebookPageController::class, 'postProduct'])->name('admin.facebook_page.post_product');
    
     // tag manager
    Route::get('tag-manager/manage', [TagManagerController::class,'index'])->name('tagmanagers.index');
    Route::get('tag-manager/{id}/show', [TagManagerController::class,'show'])->name('tagmanagers.show');
    Route::get('tag-manager/create', [TagManagerController::class,'create'])->name('tagmanagers.create');
    Route::post('tag-manager/save', [TagManagerController::class,'store'])->name('tagmanagers.store');
    Route::get('tag-manager/{id}/edit', [TagManagerController::class,'edit'])->name('tagmanagers.edit');
    Route::post('tag-manager/update', [TagManagerController::class,'update'])->name('tagmanagers.update');
    Route::post('tag-manager/inactive', [TagManagerController::class,'inactive'])->name('tagmanagers.inactive');
    Route::post('tag-manager/active', [TagManagerController::class,'active'])->name('tagmanagers.active');
    Route::post('tag-manager/destroy', [TagManagerController::class,'destroy'])->name('tagmanagers.destroy');
    
    // attribute
    Route::get('brands/manage', [BrandController::class,'index'])->name('brands.index');
    Route::get('brands/{id}/show', [BrandController::class,'show'])->name('brands.show');
    Route::get('brands/create', [BrandController::class,'create'])->name('brands.create');
    Route::post('brands/save', [BrandController::class,'store'])->name('brands.store');
    Route::get('brands/{id}/edit', [BrandController::class,'edit'])->name('brands.edit');
    Route::post('brands/update', [BrandController::class,'update'])->name('brands.update');
    Route::post('brands/inactive', [BrandController::class,'inactive'])->name('brands.inactive');
    Route::post('brands/active', [BrandController::class,'active'])->name('brands.active');
    Route::post('brands/destroy', [BrandController::class,'destroy'])->name('brands.destroy');

     // color
    Route::get('color/manage', [ColorController::class,'index'])->name('colors.index');
    Route::get('color/{id}/show', [ColorController::class,'show'])->name('colors.show');
    Route::get('color/create', [ColorController::class,'create'])->name('colors.create');
    Route::post('color/save', [ColorController::class,'store'])->name('colors.store');
    Route::get('color/{id}/edit', [ColorController::class,'edit'])->name('colors.edit');
    Route::post('color/update', [ColorController::class,'update'])->name('colors.update');
    Route::post('color/inactive', [ColorController::class,'inactive'])->name('colors.inactive');
    Route::post('color/active', [ColorController::class,'active'])->name('colors.active');
    Route::post('color/destroy', [ColorController::class,'destroy'])->name('colors.destroy');
    
    // size
    Route::get('size/manage', [SizeController::class,'index'])->name('sizes.index');
    Route::get('size/{id}/show', [SizeController::class,'show'])->name('sizes.show');
    Route::get('size/create', [SizeController::class,'create'])->name('sizes.create');
    Route::post('size/save', [SizeController::class,'store'])->name('sizes.store');
    Route::get('size/{id}/edit', [SizeController::class,'edit'])->name('sizes.edit');
    Route::post('size/update', [SizeController::class,'update'])->name('sizes.update');
    Route::post('size/inactive', [SizeController::class,'inactive'])->name('sizes.inactive');
    Route::post('size/active', [SizeController::class,'active'])->name('sizes.active');
    Route::post('size/destroy', [SizeController::class,'destroy'])->name('sizes.destroy');
   
   
    // product
    Route::get('products/manage', [ProductController::class,'index'])->name('products.index');
    Route::get('products/wholesale', [ProductController::class,'wholesale'])->name('admin.products.wholesale');
    Route::get('products/{id}/show', [ProductController::class,'show'])->name('products.show');
    Route::get('products/create', [ProductController::class,'create'])->name('products.create');
    
    // Inhouse Products
    Route::get('inhouse-products/manage', [App\Http\Controllers\Admin\InhouseProductController::class,'index'])->name('inhouse.products.index');
    Route::get('inhouse-products/{id}/show', [App\Http\Controllers\Admin\InhouseProductController::class,'show'])->name('inhouse.products.show');
    
    // Wholesale Products
    Route::get('wholesale-products', [\App\Http\Controllers\Admin\WholesaleProductController::class, 'index'])->name('admin.wholesale_products.index');
    Route::get('wholesale-products/create', [\App\Http\Controllers\Admin\WholesaleProductController::class, 'create'])->name('admin.wholesale_products.create');
    Route::post('wholesale-products', [\App\Http\Controllers\Admin\WholesaleProductController::class, 'store'])->name('admin.wholesale_products.store');
    Route::get('wholesale-products/{id}', [\App\Http\Controllers\Admin\WholesaleProductController::class, 'show'])->name('admin.wholesale_products.show');
    Route::get('wholesale-products/{id}/edit', [\App\Http\Controllers\Admin\WholesaleProductController::class, 'edit'])->name('admin.wholesale_products.edit');
    Route::post('wholesale-products/{id}', [\App\Http\Controllers\Admin\WholesaleProductController::class, 'update'])->name('admin.wholesale_products.update');
    Route::delete('wholesale-products/{id}', [\App\Http\Controllers\Admin\WholesaleProductController::class, 'destroy'])->name('admin.wholesale_products.destroy');
    Route::post('wholesale-products/{id}/approve', [\App\Http\Controllers\Admin\WholesaleProductController::class, 'approve'])->name('admin.wholesale_products.approve');
    Route::post('wholesale-products/{id}/reject', [\App\Http\Controllers\Admin\WholesaleProductController::class, 'reject'])->name('admin.wholesale_products.reject');
    Route::get('ajax-wholesale-subcategory', [\App\Http\Controllers\Admin\WholesaleProductController::class, 'getSubcategory'])->name('admin.ajax.wholesale.subcategory');
    Route::get('ajax-wholesale-childcategory', [\App\Http\Controllers\Admin\WholesaleProductController::class, 'getChildcategory'])->name('admin.ajax.wholesale.childcategory');
    Route::post('products/save', [ProductController::class,'store'])->name('products.store');
    Route::post('products/generate-ai-description', [ProductController::class, 'generateAIDescription'])->name('products.generate_ai_description');
    Route::get('products/{id}/edit', [ProductController::class,'edit'])->name('products.edit');
    Route::post('products/update', [ProductController::class,'update'])->name('products.update');
    Route::post('products/inactive', [ProductController::class,'inactive'])->name('products.inactive');
    Route::post('products/active', [ProductController::class,'active'])->name('products.active');
    Route::post('products/destroy', [ProductController::class,'destroy'])->name('products.destroy');
    Route::get('products/image/destroy', [ProductController::class,'imgdestroy'])->name('products.image.destroy');
    Route::get('products/price/destroy', [ProductController::class,'pricedestroy'])->name('products.price.destroy');
    Route::post('products/update-deals', [ProductController::class,'update_deals'])->name('products.update_deals');
    Route::get('products/update-feature', [ProductController::class,'update_feature'])->name('products.update_feature');
    Route::post('products/update-status', [ProductController::class,'update_status'])->name('products.update_status');
    Route::get('products/price-edit', [ProductController::class,'price_edit'])->name('products.price_edit');
    Route::post('products/price-update', [ProductController::class,'price_update'])->name('products.price_update');
    
    // Product Approval Routes
    Route::get('products/pending', [ProductController::class,'pending'])->name('products.pending');
    Route::post('products/approve', [ProductController::class,'approve'])->name('products.approve');
    Route::post('products/reject', [ProductController::class,'reject'])->name('products.reject');
    
    // campaign
    Route::get('campaign/manage', [CampaignController::class,'index'])->name('campaign.index');
    Route::get('campaign/{id}/show', [CampaignController::class,'show'])->name('campaign.show');
    Route::get('campaign/create', [CampaignController::class,'create'])->name('campaign.create');
    Route::post('campaign/save', [CampaignController::class,'store'])->name('campaign.store');
    Route::get('campaign/{id}/edit', [CampaignController::class,'edit'])->name('campaign.edit');
    Route::post('campaign/update', [CampaignController::class,'update'])->name('campaign.update');
    Route::post('campaign/inactive', [CampaignController::class,'inactive'])->name('campaign.inactive');
    Route::post('campaign/active', [CampaignController::class,'active'])->name('campaign.active');
    Route::post('campaign/destroy', [CampaignController::class,'destroy'])->name('campaign.destroy');
    Route::get('campaign/image/destroy', [CampaignController::class,'imgdestroy'])->name('campaign.image.destroy');
    Route::post('campaign/builder/upload-image', [CampaignController::class,'uploadBuilderImage'])->name('campaign.builder.upload');

    // Error Log (Laravel log viewer)
    Route::get('error-log', [ErrorLogController::class,'index'])->name('error-log.index');
    Route::post('error-log/create', [ErrorLogController::class,'create'])->name('error-log.create');
    Route::post('error-log/test', [ErrorLogController::class,'testLog'])->name('error-log.test');

    // settings route 
    Route::get('settings/manage', [GeneralSettingController::class,'index'])->name('settings.index');
    Route::get('settings/create', [GeneralSettingController::class,'create'])->name('settings.create');
    Route::post('settings/save', [GeneralSettingController::class,'store'])->name('settings.store');
    Route::get('settings/{id}/edit', [GeneralSettingController::class,'edit'])->name('settings.edit');
    Route::post('settings/update', [GeneralSettingController::class,'update'])->name('settings.update');
    Route::post('settings/inactive', [GeneralSettingController::class,'inactive'])->name('settings.inactive');
    Route::post('settings/active', [GeneralSettingController::class,'active'])->name('settings.active');
    Route::post('settings/destroy', [GeneralSettingController::class,'destroy'])->name('settings.destroy');
    // Login & registration page background + YouTube video (multibranch style)
    Route::get('settings/auth-page', [\App\Http\Controllers\Admin\AuthThemeController::class,'edit'])->name('settings.auth_theme');
    Route::post('settings/auth-page', [\App\Http\Controllers\Admin\AuthThemeController::class,'update'])->name('settings.auth_theme.update');

     // settings route 
    Route::get('social-media/manage', [SocialMediaController::class,'index'])->name('socialmedias.index');
    Route::get('social-media/create', [SocialMediaController::class,'create'])->name('socialmedias.create');
    Route::post('social-media/save', [SocialMediaController::class,'store'])->name('socialmedias.store');
    Route::get('social-media/{id}/edit', [SocialMediaController::class,'edit'])->name('socialmedias.edit');
    Route::post('social-media/update', [SocialMediaController::class,'update'])->name('socialmedias.update');
    Route::post('social-media/inactive', [SocialMediaController::class,'inactive'])->name('socialmedias.inactive');
    Route::post('social-media/active', [SocialMediaController::class,'active'])->name('socialmedias.active');
    Route::post('social-media/destroy', [SocialMediaController::class,'destroy'])->name('socialmedias.destroy');

     // contact route 
    Route::get('contact/manage', [ContactController::class,'index'])->name('contact.index');
    Route::get('contact/create', [ContactController::class,'create'])->name('contact.create');

    Route::get('contact/{id}/edit', [ContactController::class,'edit'])->name('contact.edit');
    Route::post('contact/update', [ContactController::class,'update'])->name('contact.update');
    Route::post('contact/inactive', [ContactController::class,'inactive'])->name('contact.inactive');
    Route::post('contact/active', [ContactController::class,'active'])->name('contact.active');
    Route::post('contact/destroy', [ContactController::class,'destroy'])->name('contact.destroy');

     // banner category route 
    Route::get('banner-category/manage', [BannerCategoryController::class,'index'])->name('banner_category.index');
    Route::get('banner-category/create', [BannerCategoryController::class,'create'])->name('banner_category.create');
    Route::post('banner-category/save', [BannerCategoryController::class,'store'])->name('banner_category.store');
    Route::get('banner-category/{id}/edit', [BannerCategoryController::class,'edit'])->name('banner_category.edit');
    Route::post('banner-category/update', [BannerCategoryController::class,'update'])->name('banner_category.update');
    Route::post('banner-category/inactive', [BannerCategoryController::class,'inactive'])->name('banner_category.inactive');
    Route::post('banner-category/active', [BannerCategoryController::class,'active'])->name('banner_category.active');
    Route::post('banner-category/destroy', [BannerCategoryController::class,'destroy'])->name('banner_category.destroy');

    // banner  route 
    Route::get('banner/manage', [BannerController::class,'index'])->name('banners.index');
    Route::get('banner/create', [BannerController::class,'create'])->name('banners.create');
    Route::post('banner/save', [BannerController::class,'store'])->name('banners.store');
    Route::get('banner/{id}/edit', [BannerController::class,'edit'])->name('banners.edit');
    Route::post('banner/update', [BannerController::class,'update'])->name('banners.update');
    Route::post('banner/inactive', [BannerController::class,'inactive'])->name('banners.inactive');
    Route::post('banner/active', [BannerController::class,'active'])->name('banners.active');
    Route::post('banner/destroy', [BannerController::class,'destroy'])->name('banners.destroy');

    // Sales notification popup
    Route::get('sale-notification', [App\Http\Controllers\Admin\SaleNotificationController::class, 'index'])->name('admin.sale-notification.index');
    Route::post('sale-notification/settings', [App\Http\Controllers\Admin\SaleNotificationController::class, 'saveSettings'])->name('admin.sale-notification.settings');
    Route::post('sale-notification/store', [App\Http\Controllers\Admin\SaleNotificationController::class, 'store'])->name('admin.sale-notification.store');
    Route::post('sale-notification/bulk', [App\Http\Controllers\Admin\SaleNotificationController::class, 'bulkStore'])->name('admin.sale-notification.bulk');
    Route::post('sale-notification/reorder', [App\Http\Controllers\Admin\SaleNotificationController::class, 'reorder'])->name('admin.sale-notification.reorder');
    Route::post('sale-notification/sync-real', [App\Http\Controllers\Admin\SaleNotificationController::class, 'syncRealOrders'])->name('admin.sale-notification.sync-real');
    Route::post('sale-notification/{id}/toggle', [App\Http\Controllers\Admin\SaleNotificationController::class, 'toggle'])->name('admin.sale-notification.toggle');
    Route::delete('sale-notification/{id}', [App\Http\Controllers\Admin\SaleNotificationController::class, 'destroy'])->name('admin.sale-notification.destroy');
    Route::delete('sale-notification-all', [App\Http\Controllers\Admin\SaleNotificationController::class, 'destroyAll'])->name('admin.sale-notification.destroy-all');
    Route::delete('sale-notification-all-real', [App\Http\Controllers\Admin\SaleNotificationController::class, 'destroyAllReal'])->name('admin.sale-notification.destroy-all-real');
    
    // contact route 
    Route::get('page/manage', [CreatePageController::class,'index'])->name('pages.index');
    Route::get('page/create', [CreatePageController::class,'create'])->name('pages.create');
    Route::post('page/save', [CreatePageController::class,'store'])->name('pages.store');
    Route::get('page/{id}/edit', [CreatePageController::class,'edit'])->name('pages.edit');
    Route::post('page/update', [CreatePageController::class,'update'])->name('pages.update');
    Route::post('page/inactive', [CreatePageController::class,'inactive'])->name('pages.inactive');
    Route::post('page/active', [CreatePageController::class,'active'])->name('pages.active');
    Route::post('page/destroy', [CreatePageController::class,'destroy'])->name('pages.destroy');

    // Pos route
    Route::get('order/create', [OrderController::class,'order_create'])->name('admin.order.create');
    Route::post('order/store', [OrderController::class,'order_store'])->name('admin.order.store');
    Route::get('order/cart-add', [OrderController::class,'cart_add'])->name('admin.order.cart_add');
    Route::get('order/cart-content', [OrderController::class,'cart_content'])->name('admin.order.cart_content');
    Route::get('order/cart-increment', [OrderController::class,'cart_increment'])->name('admin.order.cart_increment');
    Route::get('order/cart-decrement', [OrderController::class,'cart_decrement'])->name('admin.order.cart_decrement');
    Route::get('order/cart-remove', [OrderController::class,'cart_remove'])->name('admin.order.cart_remove');
    Route::get('order/cart-product-discount', [OrderController::class,'product_discount'])->name('admin.order.product_discount');
    Route::get('order/cart-details', [OrderController::class,'cart_details'])->name('admin.order.cart_details');
    Route::get('order/cart-shipping', [OrderController::class,'cart_shipping'])->name('admin.order.cart_shipping');
    Route::get('order/cart-clear', [OrderController::class,'cart_clear'])->name('admin.order.cart_clear');
    Route::get('order/cart/update', [OrderController::class, 'cart_update'])->name('admin.order.cart.update');
    Route::post('order/pos/apply-coupon', [OrderController::class, 'posApplyCoupon'])->name('admin.order.pos.apply_coupon');
    Route::get('order/pos/remove-coupon', [OrderController::class, 'posRemoveCoupon'])->name('admin.order.pos.remove_coupon');

    // Order route 
	Route::get('order/{slug}/ajax', [OrderController::class, 'ajaxIndex'])->name('admin.orders.ajax');

    Route::get('order/{slug}', [OrderController::class,'index'])->name('admin.orders');
    Route::get('order/edit/{invoice_id}', [OrderController::class,'order_edit'])->name('admin.order.edit');
    Route::post('order/update', [OrderController::class,'order_update'])->name('admin.order.update');
    Route::get('order/quick-view/{id}', [OrderController::class, 'orderQuickView'])->name('admin.order.quick_view');
    Route::get('order/invoice/{invoice_id}', [OrderController::class,'invoice'])->name('admin.order.invoice');
    Route::get('order/process/{invoice_id}', [OrderController::class,'process'])->name('admin.order.process');
    Route::post('order/change', [OrderController::class,'order_process'])->name('admin.order_change');
    Route::post('order/destroy', [OrderController::class,'destroy'])->name('admin.order.destroy');
    Route::get('order-assign', [OrderController::class,'order_assign'])->name('admin.order.assign');
    Route::get('order-status', [OrderController::class,'order_status'])->name('admin.order.status');
    Route::get('order-bulk-destroy', [OrderController::class,'bulk_destroy'])->name('admin.order.bulk_destroy');
    Route::get('order-print', [OrderController::class,'order_print'])->name('admin.order.order_print');
    Route::get('bulk-courier/{slug}', [OrderController::class,'bulk_courier'])->name('admin.bulk_courier');
    Route::get('stock-report', [OrderController::class,'stock_report'])->name('admin.stock_report');
    Route::get('order-report', [OrderController::class,'order_report'])->name('admin.order_report');
    Route::post('order-pathao', [OrderController::class,'order_pathao'])->name('admin.order.pathao');
    Route::get('/pathao-city', [OrderController::class, 'pathaocity'])->name('pathaocity');
    Route::get('/pathao-zone', [OrderController::class, 'pathaozone'])->name('pathaozone');

    // Order route 
    Route::get('reviews', [ReviewController::class,'index'])->name('reviews.index');
    Route::get('review/pending', [ReviewController::class,'pending'])->name('reviews.pending');
     Route::post('review/inactive', [ReviewController::class,'inactive'])->name('reviews.inactive');
    Route::post('review/active', [ReviewController::class,'active'])->name('reviews.active');
     Route::get('review/create', [ReviewController::class,'create'])->name('reviews.create');
    Route::post('review/save', [ReviewController::class,'store'])->name('reviews.store');
    Route::get('review/{id}/edit', [ReviewController::class,'edit'])->name('reviews.edit');
    Route::post('review/update', [ReviewController::class,'update'])->name('reviews.update');
    Route::post('review/destroy', [ReviewController::class,'destroy'])->name('reviews.destroy');

    // flavor  route 
    Route::get('shipping-charge/manage', [ShippingChargeController::class,'index'])->name('shippingcharges.index');
    Route::get('shipping-charge/create', [ShippingChargeController::class,'create'])->name('shippingcharges.create');
    Route::post('shipping-charge/save', [ShippingChargeController::class,'store'])->name('shippingcharges.store');
    Route::get('shipping-charge/{id}/edit', [ShippingChargeController::class,'edit'])->name('shippingcharges.edit');
    Route::post('shipping-charge/update', [ShippingChargeController::class,'update'])->name('shippingcharges.update');
    Route::post('shipping-charge/inactive', [ShippingChargeController::class,'inactive'])->name('shippingcharges.inactive');
    Route::post('shipping-charge/active', [ShippingChargeController::class,'active'])->name('shippingcharges.active');
    Route::post('shipping-charge/destroy', [ShippingChargeController::class,'destroy'])->name('shippingcharges.destroy');

    Route::get('delivery/divisions', [DeliveryDivisionController::class, 'index'])->name('admin.delivery.divisions.index');
    Route::get('delivery/divisions/create', [DeliveryDivisionController::class, 'create'])->name('admin.delivery.divisions.create');
    Route::post('delivery/divisions/save', [DeliveryDivisionController::class, 'store'])->name('admin.delivery.divisions.store');
    Route::get('delivery/divisions/{id}/edit', [DeliveryDivisionController::class, 'edit'])->name('admin.delivery.divisions.edit');
    Route::post('delivery/divisions/update', [DeliveryDivisionController::class, 'update'])->name('admin.delivery.divisions.update');
    Route::post('delivery/divisions/destroy', [DeliveryDivisionController::class, 'destroy'])->name('admin.delivery.divisions.destroy');

    Route::get('delivery/division/{division}/districts', [DeliveryDistrictController::class, 'index'])->name('admin.delivery.districts.index');
    Route::get('delivery/division/{division}/districts/create', [DeliveryDistrictController::class, 'create'])->name('admin.delivery.districts.create');
    Route::post('delivery/district/save', [DeliveryDistrictController::class, 'store'])->name('admin.delivery.districts.store');
    Route::get('delivery/district/{id}/edit', [DeliveryDistrictController::class, 'edit'])->name('admin.delivery.districts.edit');
    Route::post('delivery/district/update', [DeliveryDistrictController::class, 'update'])->name('admin.delivery.districts.update');
    Route::post('delivery/district/destroy', [DeliveryDistrictController::class, 'destroy'])->name('admin.delivery.districts.destroy');

    Route::get('delivery/district/{district}/upazilas', [DeliveryUpazilaController::class, 'index'])->name('admin.delivery.upazilas.index');
    Route::get('delivery/district/{district}/upazilas/create', [DeliveryUpazilaController::class, 'create'])->name('admin.delivery.upazilas.create');
    Route::post('delivery/upazila/save', [DeliveryUpazilaController::class, 'store'])->name('admin.delivery.upazilas.store');
    Route::get('delivery/upazila/{id}/edit', [DeliveryUpazilaController::class, 'edit'])->name('admin.delivery.upazilas.edit');
    Route::post('delivery/upazila/update', [DeliveryUpazilaController::class, 'update'])->name('admin.delivery.upazilas.update');
    Route::post('delivery/upazila/destroy', [DeliveryUpazilaController::class, 'destroy'])->name('admin.delivery.upazilas.destroy');
    
    // backend customer route 
    Route::get('customer', [CustomerManageController::class,'index'])->name('customers.index');
    Route::get('customer/manage', [CustomerManageController::class,'index'])->name('customers.manage');
    Route::get('customer/{id}/edit', [CustomerManageController::class,'edit'])->name('customers.edit');
    Route::post('customer/update', [CustomerManageController::class,'update'])->name('customers.update');
    Route::post('customer/inactive', [CustomerManageController::class,'inactive'])->name('customers.inactive');
    Route::post('customer/active', [CustomerManageController::class,'active'])->name('customers.active');
    Route::get('customer/profile', [CustomerManageController::class,'profile'])->name('customers.profile');
    Route::post('customer/adminlog', [CustomerManageController::class,'adminlog'])->name('customers.adminlog');
    Route::get('customer/ip-block', [CustomerManageController::class,'ip_block'])->name('customers.ip_block');
    Route::post('customer/ip-store', [CustomerManageController::class,'ipblock_store'])->name('customers.ipblock.store');
    Route::post('customer/ip-update', [CustomerManageController::class,'ipblock_update'])->name('customers.ipblock.update');
    Route::post('customer/ip-destroy', [CustomerManageController::class,'ipblock_destroy'])->name('customers.ipblock.destroy');
    Route::post('customer/ip-quick-block', [CustomerManageController::class,'ipblock_quick_store'])->name('customers.ipblock.quick');
    Route::delete('customer/{id}', [CustomerManageController::class,'destroy'])->name('customers.destroy');
    Route::post('customer/delete-all', [CustomerManageController::class,'destroyAll'])->name('customers.destroy-all');

    // Vendor Management Routes
    Route::get('vendors', [VendorController::class,'index'])->name('admin.vendors.index');
    Route::get('vendors/manage', [VendorController::class,'index'])->name('admin.vendors.manage');
    Route::get('vendors/{id}/edit', [VendorController::class,'edit'])->name('admin.vendors.edit');
    Route::post('vendors/update', [VendorController::class,'update'])->name('admin.vendors.update');
    Route::post('vendors/{id}/toggle-status', [VendorController::class,'toggleStatus'])->name('admin.vendors.toggle-status');
    Route::post('vendors/{id}/approve-verification', [VendorController::class,'approveVerification'])->name('admin.vendors.approve-verification');
    Route::post('vendors/{id}/reject-verification', [VendorController::class,'rejectVerification'])->name('admin.vendors.reject-verification');
    Route::delete('vendors/{id}', [VendorController::class,'destroy'])->name('admin.vendors.destroy');
    
    // Vendor Verification Management
    Route::get('vendor-verifications', [\App\Http\Controllers\Admin\VendorVerificationController::class,'index'])->name('admin.vendor.verification.index');
    Route::get('vendor-verifications/{id}', [\App\Http\Controllers\Admin\VendorVerificationController::class,'show'])->name('admin.vendor.verification.show');
    Route::post('vendor-verifications/{id}/approve', [\App\Http\Controllers\Admin\VendorVerificationController::class,'approve'])->name('admin.vendor.verification.approve');
    Route::post('vendor-verifications/{id}/reject', [\App\Http\Controllers\Admin\VendorVerificationController::class,'reject'])->name('admin.vendor.verification.reject');

    // Reseller Management
    Route::get('resellers', [\App\Http\Controllers\Admin\ResellerController::class,'index'])->name('admin.resellers.index');
    Route::get('resellers/{id}/edit', [\App\Http\Controllers\Admin\ResellerController::class,'edit'])->name('admin.resellers.edit');
    Route::post('resellers/update', [\App\Http\Controllers\Admin\ResellerController::class,'update'])->name('admin.resellers.update');
    Route::post('resellers/{id}/toggle-status', [\App\Http\Controllers\Admin\ResellerController::class,'toggleStatus'])->name('admin.resellers.toggle-status');
    Route::delete('resellers/{id}', [\App\Http\Controllers\Admin\ResellerController::class,'destroy'])->name('admin.resellers.destroy');
    
    // Reseller Verification Management
    Route::get('reseller-verifications', [\App\Http\Controllers\Admin\ResellerVerificationController::class,'index'])->name('admin.reseller.verification.index');
    Route::get('reseller-verifications/{id}', [\App\Http\Controllers\Admin\ResellerVerificationController::class,'show'])->name('admin.reseller.verification.show');
    Route::post('reseller-verifications/{id}/approve', [\App\Http\Controllers\Admin\ResellerVerificationController::class,'approve'])->name('admin.reseller.verification.approve');
    Route::post('reseller-verifications/{id}/reject', [\App\Http\Controllers\Admin\ResellerVerificationController::class,'reject'])->name('admin.reseller.verification.reject');

    // Reseller Withdrawal Management
    Route::get('reseller-withdrawals', [\App\Http\Controllers\Admin\ResellerWithdrawalController::class,'index'])->name('admin.reseller.withdrawals.index');
    Route::post('reseller-withdrawals/{id}/approve', [\App\Http\Controllers\Admin\ResellerWithdrawalController::class,'approve'])->name('admin.reseller.withdrawals.approve');
    Route::post('reseller-withdrawals/{id}/reject', [\App\Http\Controllers\Admin\ResellerWithdrawalController::class,'reject'])->name('admin.reseller.withdrawals.reject');

    // Reseller Deposit Management
    Route::get('reseller-deposits', [\App\Http\Controllers\Admin\ResellerDepositController::class,'index'])->name('admin.reseller-deposits.index');
    Route::post('reseller-deposits/{id}/mark-paid', [\App\Http\Controllers\Admin\ResellerDepositController::class,'markAsPaid'])->name('admin.reseller-deposits.mark-paid');

    // Refund Management Routes
    Route::get('refunds', [\App\Http\Controllers\Admin\RefundController::class, 'index'])->name('admin.refunds.index');
    Route::get('refunds/{id}', [\App\Http\Controllers\Admin\RefundController::class, 'show'])->name('admin.refunds.show');
    Route::post('refunds/{id}/approve', [\App\Http\Controllers\Admin\RefundController::class, 'approve'])->name('admin.refunds.approve');
    Route::post('refunds/{id}/reject', [\App\Http\Controllers\Admin\RefundController::class, 'reject'])->name('admin.refunds.reject');
    Route::post('refunds/{id}/process', [\App\Http\Controllers\Admin\RefundController::class, 'process'])->name('admin.refunds.process');
    Route::delete('refunds/{id}', [\App\Http\Controllers\Admin\RefundController::class, 'destroy'])->name('admin.refunds.destroy');

    Route::get('delivery-boys', [DeliveryBoyController::class, 'index'])->name('admin.delivery-boys.index');
    Route::get('delivery-boys/create', [DeliveryBoyController::class, 'create'])->name('admin.delivery-boys.create');
    Route::post('delivery-boys/save', [DeliveryBoyController::class, 'store'])->name('admin.delivery-boys.store');
    Route::get('delivery-boys/{id}/edit', [DeliveryBoyController::class, 'edit'])->name('admin.delivery-boys.edit');
    Route::post('delivery-boys/update/{id}', [DeliveryBoyController::class, 'update'])->name('admin.delivery-boys.update');
    Route::get('delivery-boys/{id}/wallet', [DeliveryBoyController::class, 'wallet'])->name('admin.delivery-boys.wallet');
    Route::post('delivery-boys/salary', [DeliveryBoyController::class, 'paySalary'])->name('admin.delivery-boys.salary');
    Route::get('delivery-withdrawals', [DeliveryBoyController::class, 'withdrawals'])->name('admin.delivery-boys.withdrawals');
    Route::post('delivery-withdrawals/approve', [DeliveryBoyController::class, 'approveWithdrawal'])->name('admin.delivery-boys.withdrawals.approve');
    Route::post('delivery-withdrawals/reject', [DeliveryBoyController::class, 'rejectWithdrawal'])->name('admin.delivery-boys.withdrawals.reject');

});

Route::prefix('delivery')->name('delivery.')->group(function () {
    Route::middleware('guest:delivery_boy')->group(function () {
        Route::get('login', [DeliveryBoyAuthController::class, 'showLogin'])->name('login');
        Route::post('login', [DeliveryBoyAuthController::class, 'login'])->name('login.post');
        Route::get('forgot-password', [DeliveryBoyAuthController::class, 'showForgotPasswordForm'])->name('password.request');
        Route::post('forgot-password', [DeliveryBoyAuthController::class, 'sendResetOtp'])->middleware('throttle:5,1')->name('password.email');
        Route::get('verify-otp', [DeliveryBoyAuthController::class, 'showVerifyOtpForm'])->name('password.verify');
        Route::post('verify-otp', [DeliveryBoyAuthController::class, 'verifyOtp'])->middleware('throttle:10,1')->name('password.verify.post');
        Route::post('resend-otp', [DeliveryBoyAuthController::class, 'resendOtp'])->middleware('throttle:3,1')->name('password.resend');
        Route::get('reset-password', [DeliveryBoyAuthController::class, 'showResetForm'])->name('password.reset');
        Route::post('reset-password', [DeliveryBoyAuthController::class, 'resetPassword'])->middleware('throttle:10,1')->name('password.store');
    });
    Route::middleware('auth:delivery_boy')->group(function () {
        Route::get('/', [DeliveryBoyDashboardController::class, 'index'])->name('dashboard');
        Route::post('logout', [DeliveryBoyAuthController::class, 'logout'])->name('logout');
        Route::get('orders', [DeliveryBoyOrderController::class, 'index'])->name('orders.index');
        Route::get('orders/history', [DeliveryBoyOrderController::class, 'history'])->name('orders.history');
        Route::get('orders/{id}', [DeliveryBoyOrderController::class, 'show'])->name('orders.show');
        Route::post('orders/{id}/deliver', [DeliveryBoyOrderController::class, 'deliver'])->name('orders.deliver');
        Route::post('orders/{id}/cancel', [DeliveryBoyOrderController::class, 'cancel'])->name('orders.cancel');
        Route::get('wallet', [DeliveryBoyWalletController::class, 'index'])->name('wallet');
        Route::post('wallet/withdraw', [DeliveryBoyWalletController::class, 'withdraw'])->name('wallet.withdraw');
        Route::get('profile', [DeliveryBoyProfileController::class, 'edit'])->name('profile.edit');
        Route::post('profile', [DeliveryBoyProfileController::class, 'update'])->name('profile.update');
    });
});