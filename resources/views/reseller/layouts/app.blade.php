<!DOCTYPE html>
<html lang="bn">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'প্রো রিসেলার ড্যাশবোর্ড') - {{ $user->shop_name ?? $user->name }}</title>
    
    <!-- App favicon -->
    @if(isset($generalsetting) && !empty($generalsetting->favicon))
    <link rel="shortcut icon" href="{{ asset($generalsetting->favicon) }}" type="image/x-icon">
    <link rel="icon" href="{{ asset($generalsetting->favicon) }}" type="image/x-icon">
    @else
    <link rel="shortcut icon" href="{{ asset('public/backEnd/assets/images/favicon.ico') }}" type="image/x-icon">
    <link rel="icon" href="{{ asset('public/backEnd/assets/images/favicon.ico') }}" type="image/x-icon">
    @endif
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Hind+Siliguri:wght@400;500;600;700&family=Poppins:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    @include('partials.mb.panel-css')
    <style>
        :root {
            --primary: #4f46e5; --primary-dark: #4338ca; --secondary: #64748b; --success: #10b981; --bg-body: #f3f4f8;
            --card-shadow: 0 6px 16px rgba(15, 23, 42, .10);
        }
        .wallet-badge { background: #f0fdf4; color: #15803d; border: 1px solid #bbf7d0; padding: 6px 14px; border-radius: 30px; font-weight: 600; display: flex; align-items: center; gap: 8px; font-size: 14px; }
        .cursor-pointer { cursor: pointer; }
    </style>
    @stack('styles')
</head>
<body class="panel-ui">

    @include('reseller.partials.sidebar')
    <div class="sb-backdrop" id="sidebarOverlay" onclick="document.getElementById('sidebar').classList.remove('show')"></div>

    <main class="mg-main">
        <div class="mg-topbar">
            <div class="d-flex align-items-center gap-2">
                <button type="button" class="burger" id="sbToggle" aria-label="Menu"><i class="fa-solid fa-bars"></i></button>
                <div class="d-none d-md-block lh-sm">
                    <span class="page-title">@yield('page-title', 'Dashboard')</span>
                    <small class="text-muted d-block">Welcome back, {{ $user->shop_name ?? $user->name }}!</small>
                </div>
            </div>
            <div class="tb-right">
                @if(isset($demoMode) && $demoMode)
                <div class="d-none d-md-flex"><span class="badge bg-warning text-dark px-2 py-1" title="ডেমো মুড সক্রিয়"><i class="fas fa-eye me-1"></i>ডেমো</span></div>
                @endif
                <div class="d-none d-sm-flex">
                    <a href="{{ route('reseller.wallet') }}" class="wallet-badge"><i class="fas fa-wallet"></i> ৳ {{ number_format($user->wallet_balance ?? 0, 0) }}</a>
                </div>
                <div class="dropdown">
                    <a href="#" class="tb-icon" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="fa-regular fa-bell"></i>
                        @if(isset($resellerTotalNotifications) && $resellerTotalNotifications > 0)
                            <span class="badge rounded-pill bg-danger">{{ $resellerTotalNotifications > 9 ? '9+' : $resellerTotalNotifications }}</span>
                        @endif
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end shadow-lg border-0" style="width: 350px; max-height: 500px; overflow-y: auto;">
                        <li class="px-3 py-2 border-bottom bg-light">
                            <h6 class="mb-0 fw-bold text-dark">
                                <i class="fas fa-bell text-primary me-2"></i>নোটিফিকেশন
                                @if(isset($resellerTotalNotifications) && $resellerTotalNotifications > 0)
                                <span class="badge bg-danger ms-2">{{ $resellerTotalNotifications }}</span>
                                @endif
                            </h6>
                        </li>
                        
                        <!-- Account Verification Notification -->
                        @if(isset($resellerVerificationStatus) && $resellerVerificationStatus != 'approved')
                        <li>
                            <a class="dropdown-item py-3 border-bottom" href="{{ route('reseller.verification.index') }}">
                                <div class="d-flex align-items-center">
                                    <div class="flex-shrink-0">
                                        <div class="rounded-circle bg-warning bg-opacity-10 d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                                            <i class="fas fa-exclamation-triangle text-warning"></i>
                                        </div>
                                    </div>
                                    <div class="flex-grow-1 ms-3">
                                        <p class="mb-0 fw-semibold">
                                            @if($resellerVerificationStatus == 'pending')
                                                একাউন্ট ভেরিফিকেশন পেন্ডিং
                                            @elseif($resellerVerificationStatus == 'rejected')
                                                একাউন্ট ভেরিফিকেশন রিজেক্ট
                                            @else
                                                একাউন্ট ভেরিফাই করুন
                                            @endif
                                        </p>
                                        <small class="text-muted">আপনার একাউন্ট verify করা আবশ্যক</small>
                                    </div>
                                </div>
                            </a>
                        </li>
                        @endif
                        
                        <!-- Pending Orders Notification -->
                        @if(isset($resellerPendingOrders) && $resellerPendingOrders > 0)
                        <li>
                            <a class="dropdown-item py-3 border-bottom" href="{{ route('reseller.orders') }}">
                                <div class="d-flex align-items-center">
                                    <div class="flex-shrink-0">
                                        <div class="rounded-circle bg-info bg-opacity-10 d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                                            <i class="fas fa-shopping-cart text-info"></i>
                                        </div>
                                    </div>
                                    <div class="flex-grow-1 ms-3">
                                        <p class="mb-0 fw-semibold">নতুন অর্ডার</p>
                                        <small class="text-muted">{{ $resellerPendingOrders }} টি পেন্ডিং অর্ডার</small>
                                    </div>
                                    <div class="flex-shrink-0">
                                        <span class="badge bg-danger rounded-pill">{{ $resellerPendingOrders }}</span>
                                    </div>
                                </div>
                            </a>
                        </li>
                        @endif
                        
                        <!-- Recent Orders -->
                        @if(isset($resellerRecentOrders) && $resellerRecentOrders->count() > 0)
                        <li><hr class="dropdown-divider"></li>
                        <li>
                            <div class="px-3 py-2 border-bottom">
                                <h6 class="mb-1 fw-bold text-dark small">সাম্প্রতিক অর্ডার</h6>
                            </div>
                        </li>
                        @foreach($resellerRecentOrders->take(3) as $order)
                        <li>
                            <a class="dropdown-item py-2" href="{{ route('reseller.orders') }}">
                                <div class="d-flex align-items-center">
                                    <div class="flex-shrink-0">
                                        @if($order->orderdetails->first() && $order->orderdetails->first()->product && $order->orderdetails->first()->product->image)
                                            <img src="{{ asset($order->orderdetails->first()->product->image->image) }}" class="rounded" width="35" height="35" style="object-fit:cover;">
                @else
                                            <div class="rounded bg-light d-flex align-items-center justify-content-center" style="width:35px;height:35px;">
                                                <i class="fas fa-box text-muted"></i>
                    </div>
                @endif
                                    </div>
                                    <div class="flex-grow-1 ms-2">
                                        <p class="mb-0 small fw-semibold">#{{ $order->invoice_id ?? $order->id }}</p>
                                        <small class="text-muted">{{ $order->customer->name ?? 'Guest' }}</small>
                                    </div>
                                    <div class="flex-shrink-0 text-end">
                                        <span class="badge bg-success bg-opacity-10 text-success small">+৳{{ number_format($order->reseller_profit ?? 0, 0) }}</span>
                                    </div>
                                </div>
                            </a>
                        </li>
                        @endforeach
                        @endif
                        
                        <!-- Pending Withdrawals Notification -->
                        @if(isset($resellerPendingWithdrawals) && $resellerPendingWithdrawals > 0)
                        <li><hr class="dropdown-divider"></li>
                        <li>
                            <a class="dropdown-item py-3 border-bottom" href="{{ route('reseller.withdrawals.index') }}">
                                <div class="d-flex align-items-center">
                                    <div class="flex-shrink-0">
                                        <div class="rounded-circle bg-warning bg-opacity-10 d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                                            <i class="fas fa-money-bill-wave text-warning"></i>
                                        </div>
                                    </div>
                                    <div class="flex-grow-1 ms-3">
                                        <p class="mb-0 fw-semibold">পেন্ডিং উইথড্র</p>
                                        <small class="text-muted">{{ $resellerPendingWithdrawals }} টি রিকোয়েস্ট প্রসেসিং</small>
                                    </div>
                                    <div class="flex-shrink-0">
                                        <span class="badge bg-warning rounded-pill">{{ $resellerPendingWithdrawals }}</span>
                                    </div>
                                </div>
                            </a>
                        </li>
                        @endif
                        
                        <!-- Recent Withdrawals -->
                        @if(isset($resellerRecentWithdrawals) && $resellerRecentWithdrawals->count() > 0)
                        <li><hr class="dropdown-divider"></li>
                        <li>
                            <div class="px-3 py-2 border-bottom">
                                <h6 class="mb-1 fw-bold text-dark small">সাম্প্রতিক উইথড্র</h6>
                            </div>
                        </li>
                        @foreach($resellerRecentWithdrawals as $withdrawal)
                        <li>
                            <a class="dropdown-item py-2" href="{{ route('reseller.withdrawals.index') }}">
                                <div class="d-flex align-items-center">
                                    <div class="flex-shrink-0">
                                        <div class="rounded-circle bg-primary bg-opacity-10 d-flex align-items-center justify-content-center" style="width: 35px; height: 35px;">
                                            <i class="fas fa-university text-primary"></i>
                                        </div>
                                    </div>
                                    <div class="flex-grow-1 ms-2">
                                        <p class="mb-0 small fw-semibold">৳{{ number_format($withdrawal->amount, 0) }}</p>
                                        <small class="text-muted">
                                            @if($withdrawal->status == 'pending')
                                                <span class="text-warning">পেন্ডিং</span>
                                            @elseif($withdrawal->status == 'approved')
                                                <span class="text-success">অনুমোদিত</span>
                                            @else
                                                <span class="text-danger">রিজেক্ট</span>
                                            @endif
                                        </small>
                                    </div>
                                </div>
                            </a>
                        </li>
                        @endforeach
                        @endif
                        
                        <!-- Empty State -->
                        @if((!isset($resellerPendingOrders) || $resellerPendingOrders == 0) && 
                            (!isset($resellerPendingWithdrawals) || $resellerPendingWithdrawals == 0) && 
                            (isset($resellerVerificationStatus) && $resellerVerificationStatus == 'approved'))
                        <li>
                            <div class="px-3 py-4 text-center text-muted">
                                <i class="fas fa-check-circle fa-2x mb-2 opacity-25"></i>
                                <p class="mb-0 small">কোন নোটিফিকেশন নেই</p>
                            </div>
                        </li>
                        @endif
                        
                        <li><hr class="dropdown-divider"></li>
                        <li>
                            <a class="dropdown-item text-center text-primary fw-semibold" href="{{ route('reseller.orders') }}">
                                সব দেখুন <i class="fas fa-arrow-right ms-1"></i>
                            </a>
                        </li>
                    </ul>
                </div>
                <div class="dropdown">
                    <a href="#" class="tb-user" data-bs-toggle="dropdown" aria-expanded="false">
                        @if($user->image)
                            <img src="{{ asset($user->image) }}" class="avatar" alt="">
                        @else
                            <span class="avatar">{{ mb_strtoupper(mb_substr((string) ($user->name ?? 'R'), 0, 1)) }}</span>
                        @endif
                        <span class="nm">{{ $user->name }}<small>Reseller</small></span>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end p-2">
                        <li><a class="dropdown-item rounded" href="{{ route('reseller.settings') }}"><i class="fa-solid fa-user-gear me-2 text-secondary"></i>প্রোফাইল</a></li>
                        <li><a class="dropdown-item rounded" href="{{ route('reseller.wallet') }}"><i class="fa-solid fa-wallet me-2 text-success"></i>ব্যালেন্স দেখুন</a></li>
                    </ul>
                </div>
                <form action="{{ route('reseller.logout') }}" method="POST" class="p-0 demo-allow-logout">@csrf<button type="submit" class="tb-logout" title="লগ আউট"><i class="fa-solid fa-right-from-bracket"></i></button></form>
            </div>
        </div>

        <div class="mg-content">
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif
        @if(session('error') && !session('demo_mode_blocked'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @yield('content')
    </div>
    </main>

    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    @if(Session::has('demo_mode_blocked'))
    <script>
    if (typeof Swal !== 'undefined') {
        Swal.fire({
            icon: 'info',
            title: '<strong style="font-size:1.4rem;color:#2c3e50;">ডেমো মুড সক্রিয়</strong>',
            html: '<div style="text-align:center;padding:10px 0;"><div style="width:70px;height:70px;margin:0 auto 15px;background:linear-gradient(135deg,#667eea 0%,#764ba2 100%);border-radius:50%;display:flex;align-items:center;justify-content:center;"><i class="fas fa-eye" style="font-size:32px;color:#fff;"></i></div><p style="font-size:1rem;color:#5a6c7d;margin-bottom:8px;line-height:1.6;">ডেমো মুড চালু আছে। কোন ডাটা পরিবর্তন বা সংযোজন করা যাবে না।</p><p style="font-size:0.9rem;color:#95a5a6;margin:0;">কাস্টমার সাইটে অর্ডার, ট্রাকিং ও অন্যান্য সেবা স্বাভাবিকভাবে কাজ করবে।</p></div>',
            confirmButtonText: 'বুঝেছি',
            confirmButtonColor: '#667eea',
            customClass: { popup: 'demo-mode-popup', confirmButton: 'demo-mode-btn' },
            width: '420px',
            backdrop: 'rgba(0,0,0,0.5)'
        });
    }
    </script>
    @endif
    <style>.demo-mode-popup{border-radius:12px;box-shadow:0 10px 40px rgba(0,0,0,0.2);}.demo-mode-btn{padding:10px 28px;font-weight:600;border-radius:8px;}</style>
    @include('partials.mb.panel-toggle')
    @if(isset($demoMode) && $demoMode)
    <script>
    function showDemoModeAlert(msg) {
        if (typeof Swal !== 'undefined') {
            Swal.fire({
                icon: 'info',
                title: '<strong style="font-size:1.4rem;color:#2c3e50;">ডেমো মুড সক্রিয়</strong>',
                html: '<div style="text-align:center;padding:10px 0;"><div style="width:70px;height:70px;margin:0 auto 15px;background:linear-gradient(135deg,#667eea 0%,#764ba2 100%);border-radius:50%;display:flex;align-items:center;justify-content:center;"><i class="fas fa-eye" style="font-size:32px;color:#fff;"></i></div><p style="font-size:1rem;color:#5a6c7d;margin-bottom:8px;line-height:1.6;">' + (msg || 'ডেমো মুড চালু আছে। কোন ডাটা পরিবর্তন বা সংযোজন করা যাবে না।') + '</p><p style="font-size:0.9rem;color:#95a5a6;margin:0;">কাস্টমার সাইটে অর্ডার, ট্রাকিং ও অন্যান্য সেবা স্বাভাবিকভাবে কাজ করবে।</p></div>',
                confirmButtonText: 'বুঝেছি',
                confirmButtonColor: '#667eea',
                customClass: { popup: 'demo-mode-popup', confirmButton: 'demo-mode-btn' },
                width: '420px',
                backdrop: 'rgba(0,0,0,0.5)'
            });
        }
    }
    $(document).ajaxComplete(function(event, xhr) {
        if (xhr.status === 403) {
            try {
                var data = typeof xhr.responseJSON !== 'undefined' ? xhr.responseJSON : JSON.parse(xhr.responseText || '{}');
                if (data.demo_mode && typeof Swal !== 'undefined') showDemoModeAlert(data.message || '');
            } catch (e) {}
        }
    });
    $(document).on('submit', 'form', function(e) {
        if ($(this).hasClass('demo-allow-logout')) return;
        var action = (this.action || '').toLowerCase();
        if (action.indexOf('logout') !== -1) return;
        var method = ($(this).find('input[name="_method"]').val() || $(this).attr('method') || 'get').toLowerCase();
        if (method === 'get') return;
        e.preventDefault();
        showDemoModeAlert();
        return false;
    });
    $(document).on('click', '.delete-confirm, .change-confirm', function(e) {
        e.preventDefault();
        showDemoModeAlert();
        return false;
    });
    document.addEventListener('click', function(e) {
        var el = e.target.closest ? e.target.closest('a[href*="destroy"], a[href*="delete"]') : null;
        if (el && el.href && el.href.indexOf('#') !== 0) {
            e.preventDefault();
            e.stopPropagation();
            showDemoModeAlert();
            return false;
        }
    }, true);
    </script>
    @endif
    @stack('scripts')
</body>
</html>
