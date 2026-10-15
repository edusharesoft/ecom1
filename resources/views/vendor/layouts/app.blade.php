<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Vendor Dashboard') - {{ $vendor->shop_name }}</title>
    @if(!empty($generalsetting->favicon))
    <link rel="shortcut icon" href="{{ asset($generalsetting->favicon) }}" type="image/x-icon">
    <link rel="icon" href="{{ asset($generalsetting->favicon) }}" type="image/x-icon">
    @endif
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Hind+Siliguri:wght@400;500;600;700&family=Poppins:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    @include('partials.mb.panel-css')
    <style>
        :root { --primary-bg: #f3f4f8; --card-dark: #232338; --text-muted: #8c90b5; }
    </style>
    @stack('styles')
</head>
<body class="panel-ui">

    @include('vendor.partials.sidebar')
    <div class="sb-backdrop" id="sidebarOverlay" onclick="document.getElementById('sidebar').classList.remove('show')"></div>

    <div class="mg-main">
        <div class="mg-topbar">
            <div class="d-flex align-items-center gap-2">
                <button type="button" class="burger" id="sbToggle" aria-label="Menu"><i class="fa-solid fa-bars"></i></button>
                <span class="page-title d-none d-sm-inline">@yield('page-title', 'Dashboard')</span>
                @hasSection('header-search')
                    <div class="search-bar d-none d-lg-block ms-3">
                        <input type="text" placeholder="অর্ডার বা প্রোডাক্ট খুঁজুন..." class="form-control" style="border-radius: 20px; border: 1px solid #e5e7eb; padding: 8px 18px; background: #f8f9fc; width: 280px;">
                    </div>
                @endif
            </div>
            <div class="tb-right">
                @if(isset($demoMode) && $demoMode)
                <div class="d-none d-md-flex"><span class="badge bg-warning text-dark px-2 py-1" title="ডেমো মুড সক্রিয়"><i class="fas fa-eye me-1"></i>ডেমো</span></div>
                @endif
                <div class="dropdown">
                        @php
                            $user = Auth::guard('admin')->user();
                            $vendorId = $user->vendor_id ?? null;
                            $notificationCount = 0;
                            if ($vendorId) {
                                $orderIds = \App\Models\OrderDetails::whereIn('product_id', function($query) use ($vendorId) {
                                        $query->select('id')->from('products')->where('vendor_id', $vendorId);
                                    })->distinct()->pluck('order_id')->toArray();
                                
                                $newOrders = \App\Models\Order::whereIn('id', $orderIds)
                                    ->whereIn('order_status', ['1', '2', '3'])
                                    ->where('created_at', '>=', \Carbon\Carbon::now()->subDay())
                                    ->count();
                                
                                $pendingWithdrawals = \App\Models\VendorWithdrawal::where('vendor_id', $vendorId)
                                    ->where('status', 'pending')->count();
                                
                                $pendingRefunds = \App\Models\Refund::where('vendor_id', $vendorId)
                                    ->where('status', 'pending')->count();
                                
                                $notificationCount = $newOrders + $pendingWithdrawals + $pendingRefunds;
                                
                                if ($vendor->verification_status != 'approved') {
                                    $notificationCount++;
                                }
                            }
                        @endphp
                    <a href="#" class="tb-icon" id="notificationDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="fa-regular fa-bell"></i>
                        @if($notificationCount > 0)
                            <span class="badge rounded-pill bg-danger">{{ $notificationCount > 9 ? '9+' : $notificationCount }}</span>
                        @endif
                    </a>
                    @stack('header-notifications')
                    <ul class="dropdown-menu dropdown-menu-end shadow-lg border-0" style="min-width: 350px; max-height: 500px; overflow-y: auto;" aria-labelledby="notificationDropdown">
                        @stack('notification-items')
                        @include('vendor.partials.notifications')
                    </ul>
                </div>
                @php
                    $user = Auth::guard('admin')->user();
                    $tbName = $user->name ?? $vendor->owner_name;
                @endphp
                <a href="{{ route('vendor.settings') }}" class="tb-user">
                    @if($user && $user->image)
                        <img src="{{ asset($user->image) }}" class="avatar" alt="">
                    @else
                        <span class="avatar">{{ mb_strtoupper(mb_substr((string) $tbName, 0, 1)) }}</span>
                    @endif
                    <span class="nm">{{ $tbName }}<small>Vendor</small></span>
                </a>
                <form action="{{ route('vendor.logout') }}" method="POST" class="p-0 demo-allow-logout">@csrf<button type="submit" class="tb-logout" title="লগ আউট"><i class="fa-solid fa-right-from-bracket"></i></button></form>
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
    </div>

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
    <script>
        window.toggleSidebar = function () { document.getElementById('sidebar').classList.toggle('show'); };
    </script>
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
