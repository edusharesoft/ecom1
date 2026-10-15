<aside class="sidebar" id="sidebar">
    <div class="brand-logo">
        <div class="brand-logo-wrapper">
            @php
                $logo = null;
                if(isset($generalsetting) && $generalsetting && !empty($generalsetting->dark_logo)) {
                    $logo = $generalsetting->dark_logo;
                } else {
                    // Fallback: Direct fetch if variable not available
                    $gs = \App\Models\GeneralSetting::where('status', 1)->first();
                    if($gs && !empty($gs->dark_logo)) {
                        $logo = $gs->dark_logo;
                    }
                }
            @endphp
            
            @if($logo)
                <a href="{{ route('reseller.dashboard') }}" class="brand-logo-link">
                    <img src="{{ asset($logo) }}" alt="Logo" class="brand-logo-img">
                </a>
            @else
                <a href="{{ route('reseller.dashboard') }}" class="text-decoration-none" style="color: inherit;">
                    <i class="fas fa-layer-group"></i> Reseller<span style="color: #0f172a;">Pro</span>
                </a>
            @endif
        </div>
        
        <div class="dropdown brand-menu-dropdown">
            <button class="btn brand-menu-btn" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                <i class="fas fa-ellipsis-v"></i>
            </button>
            <ul class="dropdown-menu dropdown-menu-end brand-menu-dropdown-menu">
                <li>
                    <h6 class="dropdown-header">
                        <i class="fas fa-bolt me-2 text-primary"></i>Quick Access
                    </h6>
                </li>
                <li>
                    <a class="dropdown-item" href="{{ route('reseller.dashboard') }}">
                        <i class="fas fa-chart-pie me-2"></i> ড্যাশবোর্ড
                    </a>
                </li>
                <li>
                    <a class="dropdown-item" href="{{ route('reseller.products.index') }}">
                        <i class="fas fa-store me-2"></i> প্রোডাক্ট ক্যাটালগ
                    </a>
                </li>
                <li>
                    <a class="dropdown-item" href="{{ route('reseller.orders') }}">
                        <i class="fas fa-shopping-bag me-2"></i> আমার অর্ডার
                    </a>
                </li>
                <li>
                    <a class="dropdown-item" href="{{ route('reseller.customers') }}">
                        <i class="fas fa-users me-2"></i> কাস্টমার লিস্ট
                    </a>
                </li>
                <li>
                    <a class="dropdown-item" href="{{ route('reseller.fraud.page') }}">
                        <i class="fas fa-shield-alt me-2"></i> ফ্রড চেক
                    </a>
                </li>
                <li><hr class="dropdown-divider"></li>
                <li>
                    <a class="dropdown-item" href="{{ route('reseller.wallet') }}">
                        <i class="fas fa-wallet me-2"></i> ওয়ালেট ও আয়
                    </a>
                </li>
                <li>
                    <a class="dropdown-item" href="{{ route('reseller.withdrawals.index') }}">
                        <i class="fas fa-university me-2"></i> পেমেন্ট উইথড্র
                    </a>
                </li>
                <li><hr class="dropdown-divider"></li>
                <li>
                    <a class="dropdown-item" href="{{ route('reseller.landing.index') }}">
                        <i class="fas fa-globe me-2"></i> ল্যান্ডিং পেজ
                    </a>
                </li>
                <li>
                    <a class="dropdown-item" href="{{ route('reseller.settings') }}">
                        <i class="fas fa-user-cog me-2"></i> প্রোফাইল সেটিংস
                    </a>
                </li>
                <li>
                    <a class="dropdown-item" href="{{ route('home') }}" target="_blank">
                        <i class="fas fa-home me-2"></i> ওয়েবসাইট
                    </a>
                </li>
                <li><hr class="dropdown-divider"></li>
                <li>
                    <form action="{{ route('reseller.logout') }}" method="POST" class="d-inline w-100 demo-allow-logout">
                        @csrf
                        <button type="submit" class="dropdown-item text-danger">
                            <i class="fas fa-sign-out-alt me-2"></i> লগ আউট
                        </button>
                    </form>
                </li>
            </ul>
        </div>
    </div>

    <nav class="mt-2">
        <div class="menu-label">Main Menu</div>
        <div class="nav-item">
            <a href="{{ route('reseller.dashboard') }}" class="nav-link {{ request()->routeIs('reseller.dashboard') ? 'active' : '' }}">
                <i class="fas fa-chart-pie"></i> ড্যাশবোর্ড
            </a>
        </div>
        <div class="nav-item">
            <a href="{{ route('reseller.products.index') }}" class="nav-link {{ request()->routeIs('reseller.products.*') ? 'active' : '' }}">
                <i class="fas fa-store"></i> প্রোডাক্ট ক্যাটালগ
            </a>
        </div>
        <div class="nav-item">
            <a href="{{ route('reseller.checkout') }}" class="nav-link {{ request()->routeIs('reseller.checkout*') ? 'active' : '' }}">
                <i class="fas fa-shopping-cart"></i> চেকআউট 
                @if(Cart::instance('shopping')->count() > 0)
                    <span id="sidebar-cart-badge" class="badge bg-danger ms-2">{{ Cart::instance('shopping')->count() }}</span>
                @else
                    <span id="sidebar-cart-badge" class="badge bg-danger ms-2" style="display: none;">0</span>
                @endif
            </a>
        </div>
        <div class="nav-item">
            <a href="{{ route('reseller.orders') }}" class="nav-link {{ request()->routeIs('reseller.orders') ? 'active' : '' }}">
                <i class="fas fa-shopping-bag"></i> আমার অর্ডার
            </a>
        </div>
        <div class="nav-item">
            <a href="{{ route('reseller.customers') }}" class="nav-link {{ request()->routeIs('reseller.customers') ? 'active' : '' }}">
                <i class="fas fa-users"></i> কাস্টমার লিস্ট
            </a>
        </div>

        <div class="menu-label">Finance</div>
        <div class="nav-item">
            <a href="{{ route('reseller.wallet') }}" class="nav-link {{ request()->routeIs('reseller.wallet') ? 'active' : '' }}">
                <i class="fas fa-wallet"></i> ওয়ালেট ও আয়
            </a>
        </div>
        <div class="nav-item">
            <a href="{{ route('reseller.deposit') }}" class="nav-link {{ request()->routeIs('reseller.deposit') ? 'active' : '' }}">
                <i class="fas fa-plus-circle"></i> ডিপোজিট
            </a>
        </div>
        <div class="nav-item">
            <a href="{{ route('reseller.withdrawals.index') }}" class="nav-link {{ request()->routeIs('reseller.withdrawals.*') ? 'active' : '' }}">
                <i class="fas fa-university"></i> পেমেন্ট উইথড্র
            </a>
        </div>

        <div class="menu-label">Tools</div>
        <div class="nav-item">
            <a href="{{ route('reseller.fraud.page') }}" class="nav-link {{ request()->routeIs('reseller.fraud.*') ? 'active' : '' }}">
                <i class="fas fa-shield-alt"></i> ফ্রড চেক
            </a>
        </div>

        <div class="menu-label">Settings</div>
        <div class="nav-item">
            <a href="{{ route('reseller.landing.index') }}" class="nav-link {{ request()->routeIs('reseller.landing.index') ? 'active' : '' }}">
                <i class="fas fa-globe"></i> ল্যান্ডিং পেজ
            </a>
        </div>
        <div class="nav-item">
            <a href="{{ route('reseller.landing.products') }}" class="nav-link {{ request()->routeIs('reseller.landing.products*') ? 'active' : '' }}">
                <i class="fas fa-box-open"></i> ল্যান্ডিং প্রোডাক্ট
            </a>
        </div>
        <div class="nav-item">
            <a href="{{ route('reseller.settings') }}" class="nav-link {{ request()->routeIs('reseller.settings*') ? 'active' : '' }}">
                <i class="fas fa-user-cog"></i> প্রোফাইল
            </a>
        </div>
        <div class="nav-item mt-3">
            <form action="{{ route('reseller.logout') }}" method="POST" class="d-inline w-100 demo-allow-logout">
                @csrf
                <button type="submit" class="nav-link text-danger w-100 text-start border-0" style="background-color: rgba(220, 53, 69, 0.1);">
                    <i class="fas fa-sign-out-alt"></i> লগ আউট
                </button>
            </form>
        </div>
    </nav>
</aside>
