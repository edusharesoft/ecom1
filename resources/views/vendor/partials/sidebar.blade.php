@php
    $is = fn (...$r) => request()->routeIs(...$r);
    $vMenu = [
        ['label' => 'ড্যাশবোর্ড', 'icon' => 'fa-solid fa-gauge-high', 'color' => '#6366f1', 'url' => route('vendor.dashboard'), 'active' => $is('vendor.dashboard')],
        ['sec' => 'শপ'],
        ['label' => 'প্রোডাক্ট ম্যানেজ', 'icon' => 'fa-solid fa-box-open', 'color' => '#ec4899', 'url' => route('vendor.products.index'), 'active' => $is('vendor.products*')],
        ['label' => 'অর্ডার সমূহ', 'icon' => 'fa-solid fa-cart-shopping', 'color' => '#f59e0b', 'url' => route('vendor.orders'), 'active' => $is('vendor.orders*'), 'badge' => (isset($pendingOrders) && $pendingOrders > 0) ? $pendingOrders : null],
        ['label' => 'রিফান্ড', 'icon' => 'fa-solid fa-rotate-left', 'color' => '#ef4444', 'url' => route('vendor.refunds.index'), 'active' => $is('vendor.refunds*')],
        ['label' => 'কাস্টমার', 'icon' => 'fa-solid fa-users', 'color' => '#0ea5e9', 'url' => route('vendor.customers'), 'active' => $is('vendor.customers*')],
        ['label' => 'অ্যানালিটিক্স', 'icon' => 'fa-solid fa-chart-pie', 'color' => '#f97316', 'url' => route('vendor.analytics'), 'active' => $is('vendor.analytics*')],
        ['label' => 'ওয়ালেট', 'icon' => 'fa-solid fa-wallet', 'color' => '#16a34a', 'url' => route('vendor.withdrawals.index'), 'active' => $is('vendor.withdrawals*')],
        ['sec' => 'একাউন্ট'],
        ['label' => 'সেটিংস', 'icon' => 'fa-solid fa-gear', 'color' => '#64748b', 'url' => route('vendor.settings'), 'active' => $is('vendor.settings*')],
        ['label' => 'ওয়েবসাইট দেখুন', 'icon' => 'fa-solid fa-arrow-up-right-from-square', 'color' => '#0ea5e9', 'url' => url('/'), 'active' => false, 'blank' => true],
    ];
@endphp
<aside class="mg-sidebar" id="sidebar">
    <div class="brand">
        @if($vendor->logo)
            <img src="{{ asset($vendor->logo) }}" alt="" class="logo-img">
        @else
            <span class="logo-ic" style="background:linear-gradient(135deg,#0ea5e9,#6366f1)"><i class="fa-solid fa-store"></i></span>
        @endif
        <span class="t"><b>{{ $vendor->shop_name }}</b><small>Vendor Panel</small></span>
    </div>
    <nav>
        @include('partials.mb.panel-menu', ['items' => $vMenu])
        <form action="{{ route('vendor.logout') }}" method="POST" class="demo-allow-logout">@csrf
            <button type="submit" class="pm-item" title="লগ আউট"><span class="pm-ic" style="background:#e11d481f;color:#e11d48"><i class="fa-solid fa-right-from-bracket"></i></span><span class="pm-label">লগ আউট</span></button>
        </form>
    </nav>
</aside>
