@php
    $logo = null;
    if (isset($generalsetting) && $generalsetting && !empty($generalsetting->dark_logo)) {
        $logo = $generalsetting->dark_logo;
    } else {
        $gs = \App\Models\GeneralSetting::where('status', 1)->first();
        if ($gs && !empty($gs->dark_logo)) {
            $logo = $gs->dark_logo;
        }
    }
    $is = fn (...$r) => request()->routeIs(...$r);
    $cartCount = Cart::instance('shopping')->count();
    $rMenu = [
        ['sec' => 'Main Menu'],
        ['label' => 'ড্যাশবোর্ড', 'icon' => 'fa-solid fa-gauge-high', 'color' => '#6366f1', 'url' => route('reseller.dashboard'), 'active' => $is('reseller.dashboard')],
        ['label' => 'প্রোডাক্ট ক্যাটালগ', 'icon' => 'fa-solid fa-store', 'color' => '#ec4899', 'url' => route('reseller.products.index'), 'active' => $is('reseller.products.*')],
        ['cart' => true],
        ['label' => 'আমার অর্ডার', 'icon' => 'fa-solid fa-bag-shopping', 'color' => '#f97316', 'url' => route('reseller.orders'), 'active' => $is('reseller.orders')],
        ['label' => 'কাস্টমার লিস্ট', 'icon' => 'fa-solid fa-users', 'color' => '#0ea5e9', 'url' => route('reseller.customers'), 'active' => $is('reseller.customers')],
        ['sec' => 'Finance'],
        ['label' => 'ওয়ালেট ও আয়', 'icon' => 'fa-solid fa-wallet', 'color' => '#16a34a', 'url' => route('reseller.wallet'), 'active' => $is('reseller.wallet')],
        ['label' => 'ডিপোজিট', 'icon' => 'fa-solid fa-circle-plus', 'color' => '#14b8a6', 'url' => route('reseller.deposit'), 'active' => $is('reseller.deposit')],
        ['label' => 'পেমেন্ট উইথড্র', 'icon' => 'fa-solid fa-building-columns', 'color' => '#8b5cf6', 'url' => route('reseller.withdrawals.index'), 'active' => $is('reseller.withdrawals.*')],
        ['sec' => 'Tools'],
        ['label' => 'ফ্রড চেক', 'icon' => 'fa-solid fa-shield-halved', 'color' => '#ef4444', 'url' => route('reseller.fraud.page'), 'active' => $is('reseller.fraud.*')],
        ['sec' => 'Settings'],
        ['label' => 'ল্যান্ডিং পেজ', 'icon' => 'fa-solid fa-globe', 'color' => '#0284c7', 'url' => route('reseller.landing.index'), 'active' => $is('reseller.landing.index')],
        ['label' => 'ল্যান্ডিং প্রোডাক্ট', 'icon' => 'fa-solid fa-box-open', 'color' => '#a855f7', 'url' => route('reseller.landing.products'), 'active' => $is('reseller.landing.products*')],
        ['label' => 'প্রোফাইল', 'icon' => 'fa-solid fa-user-gear', 'color' => '#64748b', 'url' => route('reseller.settings'), 'active' => $is('reseller.settings*')],
        ['label' => 'ওয়েবসাইট দেখুন', 'icon' => 'fa-solid fa-arrow-up-right-from-square', 'color' => '#0ea5e9', 'url' => route('home'), 'active' => false, 'blank' => true],
    ];
@endphp
<aside class="mg-sidebar" id="sidebar">
    <div class="brand">
        @if($logo)
            <img src="{{ asset($logo) }}" alt="" class="logo-img">
        @else
            <span class="logo-ic"><i class="fa-solid fa-layer-group"></i></span>
        @endif
        <span class="t"><b>{{ $user->shop_name ?? $user->name ?? 'Reseller' }}</b><small>Reseller Panel</small></span>
    </div>
    <nav>
        @foreach ($rMenu as $item)
            @if (isset($item['cart']))
                <a href="{{ route('reseller.checkout') }}" @class(['pm-item', 'active' => $is('reseller.checkout*')]) title="চেকআউট">
                    <span class="pm-ic" style="background:#f59e0b1f;color:#f59e0b"><i class="fa-solid fa-cart-shopping"></i></span>
                    <span class="pm-label">চেকআউট</span>
                    <span id="sidebar-cart-badge" class="badge bg-danger" @if($cartCount < 1) style="display: none;" @endif>{{ $cartCount }}</span>
                </a>
            @else
                @include('partials.mb.panel-menu', ['items' => [$item]])
            @endif
        @endforeach
        <form action="{{ route('reseller.logout') }}" method="POST" class="demo-allow-logout">@csrf
            <button type="submit" class="pm-item" title="লগ আউট"><span class="pm-ic" style="background:#e11d481f;color:#e11d48"><i class="fa-solid fa-right-from-bracket"></i></span><span class="pm-label">লগ আউট</span></button>
        </form>
    </nav>
</aside>
