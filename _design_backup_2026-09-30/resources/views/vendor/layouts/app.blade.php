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
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    
    <style>
        :root {
            --sidebar-width: 260px;
            --primary-bg: #f5f6fa;
            --card-dark: #232338;
            --text-muted: #8c90b5;
        }

        body {
            background-color: var(--primary-bg);
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            overflow-x: hidden;
        }

        .sidebar {
            width: var(--sidebar-width);
            height: 100vh;
            position: fixed;
            top: 0;
            left: 0;
            background: #fff;
            box-shadow: 0 0 15px rgba(0,0,0,0.05);
            z-index: 1050;
            overflow-y: auto;
            transition: margin-left 0.3s ease-in-out;
            display: flex;
            flex-direction: column;
        }

        .brand-section {
            padding: 30px 20px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            color: #4e73df;
            font-weight: 700;
            font-size: 1.4rem;
            border-bottom: 1px solid #eaecf4;
        }
        
        .nav-link {
            color: #555;
            padding: 12px 20px;
            font-size: 0.95rem;
            display: flex;
            align-items: center;
            border-left: 4px solid transparent;
            text-decoration: none;
        }
        
        .nav-link:hover, .nav-link.active {
            background-color: #f8f9fa;
            color: #4e73df;
            border-left: 4px solid #4e73df;
        }
        
        .nav-link i {
            width: 25px;
            margin-right: 10px;
            text-align: center;
        }

        .main-content {
            margin-left: var(--sidebar-width);
            transition: margin-left 0.3s ease-in-out;
            padding: 20px;
            min-height: 100vh;
        }

        @media (max-width: 1199px) { 
            .sidebar {
                margin-left: calc(var(--sidebar-width) * -1);
            }
            .sidebar.show {
                margin-left: 0;
            }
            .main-content {
                margin-left: 0;
            }
            .sidebar-overlay {
                display: none;
                position: fixed;
                top: 0; left: 0; right: 0; bottom: 0;
                background: rgba(0,0,0,0.5);
                z-index: 1040;
            }
            .sidebar-overlay.show {
                display: block;
            }
        }
    </style>
    @stack('styles')
</head>
<body>

    <div class="sidebar-overlay" id="sidebarOverlay"></div>

    @include('vendor.partials.sidebar')

    <div class="main-content">
        <header class="d-flex justify-content-between align-items-center mb-4 bg-white p-3 rounded shadow-sm">
            <div class="d-flex align-items-center">
                <button class="btn btn-light me-3 d-xl-none" id="sidebarToggle"><i class="fas fa-bars"></i></button>
                <h5 class="m-0 fw-bold text-dark d-none d-md-block">@yield('page-title', 'Dashboard')</h5>
                @hasSection('header-search')
                    <div class="search-bar d-none d-md-block ms-3">
                        <input type="text" placeholder="অর্ডার বা প্রোডাক্ট খুঁজুন..." class="form-control" style="border-radius: 20px; border: none; padding: 10px 20px; background: #f8f9fc; width: 300px;">
                    </div>
                @endif
            </div>
            
                <div class="d-flex align-items-center gap-3">
                    @if(isset($demoMode) && $demoMode)
                    <span class="badge bg-warning text-dark px-2 py-1" title="ডেমো মুড সক্রিয়"><i class="fas fa-eye me-1"></i>ডেমো</span>
                    @endif
                    <div class="position-relative dropdown">
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
                        <i class="fas fa-bell fa-lg text-secondary cursor-pointer" id="notificationDropdown" data-bs-toggle="dropdown" aria-expanded="false" style="cursor: pointer;"></i>
                        @if($notificationCount > 0)
                            <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger" style="font-size: 10px; padding: 2px 5px;">
                                {{ $notificationCount > 9 ? '9+' : $notificationCount }}
                            </span>
                        @endif
                        @stack('header-notifications')
                        <ul class="dropdown-menu dropdown-menu-end shadow-lg border-0" style="min-width: 350px; max-height: 500px; overflow-y: auto; margin-top: 10px;" aria-labelledby="notificationDropdown">
                            @stack('notification-items')
                            @include('vendor.partials.notifications')
                        </ul>
                    </div>
                @php
                    $user = Auth::guard('admin')->user();
                @endphp
                @if($user && $user->image)
                    <img src="{{ asset($user->image) }}" class="rounded-circle border shadow-sm" alt="Profile" style="width: 40px; height: 40px; object-fit: cover;">
                @else
                    <div class="rounded-circle border bg-primary bg-opacity-10 d-flex align-items-center justify-content-center shadow-sm" style="width: 40px; height: 40px;">
                        <i class="fas fa-user text-primary"></i>
                    </div>
                @endif
                <div class="d-none d-md-block ms-2">
                    <small class="text-muted d-block" style="line-height:1.2; font-size: 0.75rem; color: #858796;">Admin</small>
                    <span class="fw-bold d-block" style="font-size: 0.95rem; color: #5a5c69; line-height:1.2;">{{ $user->name ?? $vendor->owner_name }}</span>
                </div>
            </div>
        </header>

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

    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
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
    <script>
        const sidebar = document.getElementById('sidebar');
        const overlay = document.getElementById('sidebarOverlay');
        const toggleBtn = document.getElementById('sidebarToggle');
        const closeBtn = document.getElementById('closeSidebar');

        function toggleSidebar() {
            sidebar.classList.toggle('show');
            overlay.classList.toggle('show');
        }

        if (toggleBtn) toggleBtn.addEventListener('click', toggleSidebar);
        if (closeBtn) closeBtn.addEventListener('click', toggleSidebar);
        if (overlay) overlay.addEventListener('click', toggleSidebar);
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
