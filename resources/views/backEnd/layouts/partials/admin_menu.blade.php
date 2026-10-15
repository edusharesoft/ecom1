{{--
    Admin sidebar menu (multibranch style), grouped for easier use.
    Every item keeps the same permission check it had in the old menu.
    Item:  ['label', 'icon' (font awesome), 'url', 'can' => [permissions] | true, 'active' => bool, 'badge' => int, 'confirm' => text]
    Group: ['label', 'icon', 'children' => [items]]   (hidden when no child is visible)
--}}
@php
    $gate = app(\Illuminate\Contracts\Auth\Access\Gate::class);
    $ok = fn ($can) => $can === true || (is_array($can) && count($can) && $gate->any($can));
    $is = fn (...$r) => request()->routeIs(...$r);
    $gs = $generalsetting ?? null;
    $vendorOn = (int) ($gs->vendor_enabled ?? 1) === 1;
    $resellerOn = (int) ($gs->reseller_enabled ?? 1) === 1;
    $cnt = function ($cb) { try { return (int) $cb(); } catch (\Throwable $e) { return 0; } };
    $pendingReviews = $cnt(fn () => \App\Models\Review::where('status', 'pending')->count());
    $vendorVerify = $vendorOn ? $cnt(fn () => \App\Models\Vendor::where('verification_status', 'pending')->count()) : 0;
    $resellerVerify = $resellerOn ? $cnt(fn () => \App\Models\User::where('role', 'reseller')->where('verification_status', 'pending')->count()) : 0;
    $resellerWithdraw = $resellerOn ? $cnt(fn () => \App\Models\ResellerWithdrawal::where('status', 'pending')->count()) : 0;
    $resellerDeposit = $resellerOn ? $cnt(fn () => \App\Models\ResellerDeposit::where('status', 'pending')->count()) : 0;

    $orderItems = [
        ['label' => 'All Order', 'icon' => 'fa-list-ul', 'url' => route('admin.orders', ['slug' => 'all']), 'can' => ['order-list']],
        ['label' => 'Reseller Orders', 'icon' => 'fa-user-tie', 'url' => route('admin.reseller-orders.index'), 'can' => ['order-list']],
        ['label' => 'Incomplete Orders', 'icon' => 'fa-hourglass-half', 'url' => route('admin.incomplete-orders.index'), 'can' => ['order-list']],
    ];
    foreach (($orderstatus ?? []) as $st) {
        $orderItems[] = ['label' => $st->name, 'icon' => 'fa-circle-dot', 'url' => route('admin.orders', ['slug' => $st->slug]), 'can' => ['order-list']];
    }
    $orderItems[] = ['label' => 'Order Status', 'icon' => 'fa-sliders', 'url' => route('orderstatus.index'), 'can' => ['order-edit']];
    $orderItems[] = ['label' => 'IP Block', 'icon' => 'fa-ban', 'url' => route('customers.ip_block'), 'can' => ['order-manage']];

    $menu = [
        ['label' => 'Dashboard', 'icon' => 'fa-gauge-high', 'url' => url('admin/dashboard'), 'can' => ['dashboard-view']],
        ['label' => 'AI Assistant', 'icon' => 'fa-robot', 'url' => route('admin.gemini_chat.index'), 'can' => ['dashboard-view'], 'active' => $is('admin.gemini_chat.*')],
        ['label' => 'POS System', 'icon' => 'fa-cash-register', 'url' => route('admin.order.create'), 'can' => ['order-create']],

        ['label' => 'Orders', 'icon' => 'fa-cart-shopping', 'children' => $orderItems],

        ['label' => 'Products', 'icon' => 'fa-boxes-stacked', 'children' => [
            ['label' => 'All Inhouse Products', 'icon' => 'fa-box', 'url' => route('inhouse.products.index'), 'can' => ['product-list']],
            ['label' => 'All Vendor Products', 'icon' => 'fa-bag-shopping', 'url' => route('products.index'), 'can' => ['product-list']],
            ['label' => 'Pending Products', 'icon' => 'fa-clock', 'url' => route('products.pending'), 'can' => ['product-list']],
            ['label' => 'Wholesale Products', 'icon' => 'fa-layer-group', 'url' => route('admin.products.wholesale'), 'can' => ['product-list']],
            ['label' => 'Add Product', 'icon' => 'fa-circle-plus', 'url' => route('products.create'), 'can' => ['product-create']],
            ['label' => 'Categories', 'icon' => 'fa-sitemap', 'url' => route('categories.index'), 'can' => ['category-list']],
            ['label' => 'Sub Categories', 'icon' => 'fa-diagram-project', 'url' => route('subcategories.index'), 'can' => ['subcategory-list']],
            ['label' => 'Child Categories', 'icon' => 'fa-code-branch', 'url' => route('childcategories.index'), 'can' => ['childcategory-list']],
            ['label' => 'Brands', 'icon' => 'fa-copyright', 'url' => route('brands.index'), 'can' => ['brand-list', 'brand-create', 'brand-edit']],
            ['label' => 'Colors', 'icon' => 'fa-palette', 'url' => route('colors.index'), 'can' => ['color-list', 'color-create', 'color-edit']],
            ['label' => 'Sizes', 'icon' => 'fa-ruler', 'url' => route('sizes.index'), 'can' => ['size-list', 'size-create', 'size-edit']],
        ]],

        ['label' => 'Refunds', 'icon' => 'fa-rotate-left', 'children' => [
            ['label' => 'All Refunds', 'icon' => 'fa-list-ul', 'url' => route('admin.refunds.index'), 'can' => ['order-list', 'order-edit']],
            ['label' => 'Pending Refunds', 'icon' => 'fa-clock', 'url' => route('admin.refunds.index', ['status' => 'pending']), 'can' => ['order-list', 'order-edit']],
            ['label' => 'Approved Refunds', 'icon' => 'fa-circle-check', 'url' => route('admin.refunds.index', ['status' => 'approved']), 'can' => ['order-list', 'order-edit']],
            ['label' => 'Processed Refunds', 'icon' => 'fa-check-double', 'url' => route('admin.refunds.index', ['status' => 'processed']), 'can' => ['order-list', 'order-edit']],
        ]],

        ['label' => 'Promotion', 'icon' => 'fa-bullhorn', 'children' => [
            ['label' => 'Newsletter Subscribers', 'icon' => 'fa-envelope-open-text', 'url' => route('admin.newsletter.subscribers'), 'can' => ['newsletter-list']],
            ['label' => 'Send Custom SMS', 'icon' => 'fa-paper-plane', 'url' => route('admin.sms.custom.page'), 'can' => ['sms-send']],
            ['label' => 'Popup Offer', 'icon' => 'fa-comment-dots', 'url' => route('admin.popup.index'), 'can' => ['popup-list', 'popup-manage']],
            ['label' => 'Facebook Page Post', 'icon' => 'fa-share-nodes', 'url' => route('admin.facebook_page.settings'), 'can' => ['pixel-manage']],
            ['label' => 'Banner & Sliders', 'icon' => 'fa-images', 'url' => route('banners.index'), 'can' => ['banner-list']],
            ['label' => 'Sales Notification Popup', 'icon' => 'fa-bell', 'url' => route('admin.sale-notification.index'), 'can' => true, 'active' => $is('admin.sale-notification.*')],
            ['label' => 'Live Ads Overview', 'icon' => 'fa-chart-line', 'url' => route('admin.ads_analytics.dashboard'), 'can' => ['pixel-manage']],
            ['label' => 'Facebook Ads', 'icon' => 'fa-bullseye', 'url' => route('admin.ads_analytics.facebook'), 'can' => ['pixel-manage']],
            ['label' => 'Google Ads', 'icon' => 'fa-globe', 'url' => route('admin.ads_analytics.google'), 'can' => ['pixel-manage']],
            ['label' => 'TikTok Ads', 'icon' => 'fa-video', 'url' => route('admin.ads_analytics.tiktok'), 'can' => ['pixel-manage']],
            ['label' => 'Tag Manager', 'icon' => 'fa-tags', 'url' => route('tagmanagers.index'), 'can' => ['pixel-manage']],
            ['label' => 'Pixel Manage', 'icon' => 'fa-code', 'url' => route('pixels.index'), 'can' => ['pixel-manage']],
            ['label' => 'TikTok Pixel', 'icon' => 'fa-film', 'url' => route('tiktok.pixels.index'), 'can' => ['pixel-manage']],
            ['label' => 'All Coupons', 'icon' => 'fa-ticket', 'url' => route('admin.coupons.index'), 'can' => ['coupon-list']],
            ['label' => 'Add New Coupons', 'icon' => 'fa-circle-plus', 'url' => route('admin.coupons.create'), 'can' => ['coupon-create']],
        ]],

        ['label' => 'Users', 'icon' => 'fa-users', 'children' => [
            ['label' => 'User', 'icon' => 'fa-user', 'url' => route('users.index'), 'can' => ['user-list']],
            ['label' => 'Roles', 'icon' => 'fa-user-shield', 'url' => route('roles.index'), 'can' => ['role-list']],
            ['label' => 'Permissions', 'icon' => 'fa-key', 'url' => route('permissions.index'), 'can' => ['permission-list']],
            ['label' => 'Customers', 'icon' => 'fa-user-group', 'url' => route('customers.index'), 'can' => ['customer-list', 'customer-create', 'customer-edit']],
            ['label' => 'Vendors', 'icon' => 'fa-store', 'url' => $vendorOn ? route('admin.vendors.index') : '#', 'can' => $vendorOn ? ['vendor-list'] : []],
            ['label' => 'Vendor Verifications', 'icon' => 'fa-shield-halved', 'url' => $vendorOn ? route('admin.vendor.verification.index') : '#', 'can' => $vendorOn ? ['vendor-verification'] : [], 'badge' => $vendorVerify],
            ['label' => 'Vendor Withdrawals', 'icon' => 'fa-money-bill-transfer', 'url' => $vendorOn ? route('admin.vendor.withdrawals.index') : '#', 'can' => $vendorOn ? ['vendor-withdrawal'] : []],
            ['label' => 'Resellers', 'icon' => 'fa-user-tie', 'url' => $resellerOn ? route('admin.resellers.index') : '#', 'can' => $resellerOn ? ['reseller-list'] : []],
            ['label' => 'Reseller Deposits', 'icon' => 'fa-credit-card', 'url' => $resellerOn ? route('admin.reseller-deposits.index') : '#', 'can' => $resellerOn ? ['reseller-withdrawal'] : [], 'badge' => $resellerDeposit],
            ['label' => 'Reseller Verifications', 'icon' => 'fa-shield-halved', 'url' => $resellerOn ? route('admin.reseller.verification.index') : '#', 'can' => $resellerOn ? ['reseller-verification'] : [], 'badge' => $resellerVerify],
            ['label' => 'Reseller Withdrawals', 'icon' => 'fa-sack-dollar', 'url' => $resellerOn ? route('admin.reseller.withdrawals.index') : '#', 'can' => $resellerOn ? ['reseller-withdrawal'] : [], 'badge' => $resellerWithdraw],
            ['label' => 'Manage Fraud API', 'icon' => 'fa-user-lock', 'url' => route('admin.fraud.index'), 'can' => ['fraud-setting-list']],
            ['label' => 'Manual Fraud Check', 'icon' => 'fa-magnifying-glass', 'url' => route('manualFraud.page'), 'can' => ['fraud-check']],
        ]],

        ['label' => 'CRM / HR', 'icon' => 'fa-id-card', 'children' => [
            ['label' => 'Employees', 'icon' => 'fa-user', 'url' => route('admin.employees.index'), 'can' => ['employee-list']],
            ['label' => 'Attendance', 'icon' => 'fa-circle-check', 'url' => route('admin.attendances.index'), 'can' => ['attendance-list']],
            ['label' => 'Leaves', 'icon' => 'fa-calendar-days', 'url' => route('admin.leaves.index'), 'can' => ['leave-list']],
            ['label' => 'Salaries', 'icon' => 'fa-sack-dollar', 'url' => route('admin.salaries.index'), 'can' => ['salary-list']],
            ['label' => 'Bonuses', 'icon' => 'fa-gift', 'url' => route('admin.bonuses.index'), 'can' => ['bonus-list']],
            ['label' => 'Salary Payments', 'icon' => 'fa-credit-card', 'url' => route('admin.salary_payments.index'), 'can' => ['salary-payment-list']],
        ]],

        ['label' => 'Setting', 'icon' => 'fa-gear', 'children' => [
            ['label' => 'General Setting', 'icon' => 'fa-sliders', 'url' => route('settings.index'), 'can' => ['setting-list']],
            ['label' => 'Login & Registration Page', 'icon' => 'fa-right-to-bracket', 'url' => route('settings.auth_theme'), 'can' => ['setting-list', 'setting-edit'], 'active' => $is('settings.auth_theme*')],
            ['label' => 'Sales Notification Popup', 'icon' => 'fa-bell', 'url' => route('admin.sale-notification.index'), 'can' => ['setting-list']],
            ['label' => 'Delivery persons', 'icon' => 'fa-person-biking', 'url' => route('admin.delivery-boys.index'), 'can' => ['delivery-boy-list']],
            ['label' => 'Rider withdrawals', 'icon' => 'fa-sack-dollar', 'url' => route('admin.delivery-boys.withdrawals'), 'can' => ['delivery-withdrawal-list']],
            ['label' => 'Delivery locations', 'icon' => 'fa-location-dot', 'url' => route('admin.delivery.divisions.index'), 'can' => ['delivery-location-list']],
            ['label' => 'Social Media', 'icon' => 'fa-share-nodes', 'url' => route('socialmedias.index'), 'can' => ['social-list']],
            ['label' => 'Contact', 'icon' => 'fa-address-book', 'url' => route('contact.index'), 'can' => ['contact-list']],
            ['label' => 'Create Page', 'icon' => 'fa-file-lines', 'url' => route('pages.index'), 'can' => ['page-list', 'page-create', 'page-edit']],
            ['label' => 'Shipping Charge', 'icon' => 'fa-truck', 'url' => route('shippingcharges.index'), 'can' => ['shipping-list', 'shipping-create', 'shipping-edit']],
            ['label' => 'SEO Settings', 'icon' => 'fa-magnifying-glass-chart', 'url' => route('admin.seo_settings.index'), 'can' => ['seo-manage']],
            ['label' => 'Email Settings', 'icon' => 'fa-envelope', 'url' => route('email_setting'), 'can' => ['email-setting-list']],
            ['label' => 'Sitemap Settings', 'icon' => 'fa-map', 'url' => route('admin.sitemap.index'), 'can' => ['sitemap-manage']],
            ['label' => 'Cron Job', 'icon' => 'fa-clock', 'url' => route('admin.cron.index'), 'can' => ['api-manage']],
            ['label' => 'Payment Gateway', 'icon' => 'fa-building-columns', 'url' => route('paymentgeteway.manage'), 'can' => ['api-manage']],
            ['label' => 'Manual Payment', 'icon' => 'fa-money-bill-wave', 'url' => route('manual-payment-gateway.manage'), 'can' => ['api-manage']],
            ['label' => 'SMS Gateway', 'icon' => 'fa-comment-sms', 'url' => route('smsgeteway.manage'), 'can' => ['api-manage']],
            ['label' => 'Courier API', 'icon' => 'fa-truck-fast', 'url' => route('courierapi.manage'), 'can' => ['api-manage']],
            ['label' => 'Facebook CAPI', 'icon' => 'fa-bullseye', 'url' => route('admin.facebook_capi.edit'), 'can' => ['api-manage']],
            ['label' => 'Gemini AI', 'icon' => 'fa-wand-magic-sparkles', 'url' => route('admin.gemini_ai.edit'), 'can' => ['api-manage']],
            ['label' => 'Gemini Assistant', 'icon' => 'fa-robot', 'url' => route('admin.gemini_chat.index'), 'can' => ['api-manage']],
        ]],

        ['label' => 'Reports', 'icon' => 'fa-chart-pie', 'children' => [
            ['label' => 'Order Report', 'icon' => 'fa-file-invoice', 'url' => route('admin.reports.orders'), 'can' => ['order-report', 'report-view']],
            ['label' => 'Purchase Report', 'icon' => 'fa-bag-shopping', 'url' => route('admin.reports.purchases'), 'can' => ['purchase-report', 'report-view']],
            ['label' => 'Expense Report', 'icon' => 'fa-arrow-trend-down', 'url' => route('admin.reports.expenses'), 'can' => ['expense-report', 'report-view']],
            ['label' => 'Stock Report', 'icon' => 'fa-box-archive', 'url' => route('admin.reports.stock'), 'can' => ['stock-report', 'report-view']],
            ['label' => 'Profit & Loss', 'icon' => 'fa-scale-balanced', 'url' => route('admin.reports.profit_loss'), 'can' => ['profit-loss-report', 'report-view']],
        ]],

        ['label' => 'Purchases', 'icon' => 'fa-file-invoice-dollar', 'url' => route('purchases.index'), 'can' => ['purchase-list', 'purchase-create', 'purchase-edit']],
        ['label' => 'Suppliers', 'icon' => 'fa-truck-ramp-box', 'url' => route('admin.suppliers.index'), 'can' => ['supplier-list', 'supplier-create', 'supplier-edit']],
        ['label' => 'Expenses', 'icon' => 'fa-receipt', 'url' => route('admin.expenses.index'), 'can' => ['expense-list', 'expense-create', 'expense-edit'], 'active' => $is('admin.expenses.*')],
        ['label' => 'Fund / Account', 'icon' => 'fa-wallet', 'url' => route('admin.fund.index'), 'can' => ['fund-list', 'fund-create', 'fund-edit']],

        ['label' => 'Reviews', 'icon' => 'fa-star', 'children' => [
            ['label' => 'Pending Reviews ('.$pendingReviews.')', 'icon' => 'fa-clock', 'url' => route('reviews.pending'), 'can' => ['review-list']],
            ['label' => 'All Reviews', 'icon' => 'fa-list-ul', 'url' => route('reviews.index'), 'can' => ['review-list']],
            ['label' => 'Create', 'icon' => 'fa-circle-plus', 'url' => route('reviews.create'), 'can' => $gate->check('review-list') ? ['review-create'] : []],
        ]],
        ['label' => 'Landing Page', 'icon' => 'fa-display', 'children' => [
            ['label' => 'Campaign', 'icon' => 'fa-bullhorn', 'url' => route('campaign.index'), 'can' => ['campaign-list']],
            ['label' => 'Create', 'icon' => 'fa-circle-plus', 'url' => route('campaign.create'), 'can' => ['campaign-create']],
        ]],
        ['label' => 'Blog', 'icon' => 'fa-pen-to-square', 'children' => [
            ['label' => 'All Blogs', 'icon' => 'fa-list-ul', 'url' => route('admin.blog.index'), 'can' => ['blog-list']],
            ['label' => 'Add New Blog', 'icon' => 'fa-circle-plus', 'url' => route('admin.blog.create'), 'can' => ['blog-create']],
        ]],

        ['label' => 'Complaints', 'icon' => 'fa-triangle-exclamation', 'url' => route('backEnd.complaints.index'), 'can' => ['complaint-list', 'complaint-create', 'complaint-edit'], 'active' => $is('backEnd.complaints.*')],
        ['label' => 'Contact Messages', 'icon' => 'fa-envelope', 'url' => route('admin.contact.messages'), 'can' => ['contact-list'], 'active' => $is('admin.contact.messages*')],
        ['label' => 'Order Restriction', 'icon' => 'fa-clock', 'url' => route('admin.order.restriction.setting.index'), 'can' => ['setting-list', 'setting-edit']],
        ['label' => 'Clear Cache', 'icon' => 'fa-rotate', 'url' => route('admin.clear.cache'), 'can' => ['cache-clear'], 'confirm' => 'Are you sure you want to clear all cache?'],
        ['label' => 'Error Log', 'icon' => 'fa-bug', 'url' => route('error-log.index'), 'can' => ['error-log-view']],
    ];
@endphp
@foreach ($menu as $n => $item)
    @if (!empty($item['children']))
        @php
            $kids = array_values(array_filter($item['children'], fn ($k) => $ok($k['can'] ?? [])));
            $open = collect($kids)->contains(fn ($k) => !empty($k['active']));
            $gid = 'sb-grp-'.$n;
            $sum = collect($kids)->sum(fn ($k) => (int) ($k['badge'] ?? 0));
        @endphp
        @if (count($kids))
            <li @class(['menuitem-active' => $open])>
                <a href="#{{ $gid }}" data-bs-toggle="collapse" aria-expanded="{{ $open ? 'true' : 'false' }}">
                    <i class="fa-solid {{ $item['icon'] }}"></i>
                    <span> {{ $item['label'] }} </span>
                    @if ($sum)<span class="badge bg-danger rounded-pill">{{ $sum }}</span>@endif
                    <span class="menu-arrow"></span>
                </a>
                <div @class(['collapse', 'show' => $open]) id="{{ $gid }}">
                    <ul class="nav-second-level">
                        @foreach ($kids as $k)
                            <li @class(['active' => !empty($k['active'])])>
                                <a href="{{ $k['url'] }}" @class(['active' => !empty($k['active'])])>
                                    <i class="fa-solid {{ $k['icon'] }}"></i> {{ $k['label'] }}
                                    @if (!empty($k['badge']))<span class="badge bg-danger rounded-pill ms-auto">{{ $k['badge'] }}</span>@endif
                                </a>
                            </li>
                        @endforeach
                    </ul>
                </div>
            </li>
        @endif
    @elseif ($ok($item['can'] ?? []))
        <li @class(['active' => !empty($item['active'])])>
            <a href="{{ $item['url'] }}" @class(['active' => !empty($item['active'])]) @if (!empty($item['confirm'])) onclick="return confirm({{ json_encode($item['confirm']) }})" @endif>
                <i class="fa-solid {{ $item['icon'] }}"></i>
                <span> {{ $item['label'] }} </span>
            </a>
        </li>
    @endif
@endforeach
<li>
  <a href="{{ route('home') }}" target="_blank">
    <i class="fa-solid fa-arrow-up-right-from-square"></i>
    <span> View Website </span>
  </a>
</li>
<li>
  <a href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
    <i class="fa-solid fa-right-from-bracket" style="--c:#e11d48;color:#e11d48;background:#e11d481f"></i>
    <span> Logout </span>
  </a>
</li>
