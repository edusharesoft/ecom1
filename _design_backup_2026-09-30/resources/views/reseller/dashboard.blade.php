@extends('reseller.layouts.app')

@section('title', 'প্রো রিসেলার ড্যাশবোর্ড')
@section('page-title', 'Dashboard')

@push('styles')
<style>
    /* --- Cards --- */
    .dashboard-card {
        background: #fff;
        border-radius: 16px;
        padding: 24px;
        border: 1px solid #e2e8f0;
        box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.05), 0 4px 6px -2px rgba(0, 0, 0, 0.025);
        height: 100%;
        transition: transform 0.2s;
    }
    .dashboard-card:hover { transform: translateY(-3px); }

    .stat-icon-box {
        width: 48px;
        height: 48px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.25rem;
    }

    /* --- Product Card --- */
    .product-card {
        background: #fff;
        border-radius: 16px;
        overflow: hidden;
        border: 1px solid #e2e8f0;
        transition: all 0.3s;
        position: relative;
    }
    .product-card:hover { box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04); }

    .product-badge {
        position: absolute;
        top: 10px;
        left: 10px;
        background: #ffffff;
        padding: 4px 10px;
        border-radius: 6px;
        font-size: 0.75rem;
        font-weight: 700;
        color: #4f46e5;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }

    .product-img {
        width: 100%;
        height: 200px;
        object-fit: cover;
        background: #f8fafc;
    }

    .profit-chip {
        background: #ecfdf5;
        color: #059669;
        font-size: 0.75rem;
        font-weight: 700;
        padding: 4px 8px;
        border-radius: 4px;
        display: inline-block;
    }

    /* Toast Notification Customization */
    .custom-toast {
        background: white;
        border-radius: 12px;
        box-shadow: 0 10px 30px rgba(0,0,0,0.15);
        border-left: 5px solid #10b981;
    }
</style>
@endpush

@section('content')

    <!-- Verification Alert -->
    @if($user->verification_status != 'approved')
    <div class="alert alert-warning alert-dismissible fade show mb-4" role="alert">
        <div class="d-flex align-items-center">
            <i class="fas fa-exclamation-triangle me-3 fa-2x"></i>
            <div class="flex-grow-1">
                <h6 class="mb-1">
                    @if($user->verification_status == 'pending')
                        Account Verification Pending
                    @elseif($user->verification_status == 'rejected')
                        Account Verification Rejected
                    @else
                        Please Verify Your Account
                    @endif
                </h6>
                <p class="mb-2 small">
                    @if($user->verification_status == 'pending')
                        আপনার verification documents admin review করছে। অনুগ্রহ করে অপেক্ষা করুন।
                    @elseif($user->verification_status == 'rejected')
                        আপনার verification reject হয়েছে। নতুন documents upload করুন।
                        @if($user->verification_note)
                            <br><strong>কারণ:</strong> {{ $user->verification_note }}
                        @endif
                    @else
                        অর্ডার করার জন্য আপনার একাউন্ট verify করা আবশ্যক।
                    @endif
                </p>
                <a href="{{ route('reseller.verification.index') }}" class="btn btn-warning btn-sm">
                    <i class="fas fa-upload me-1"></i> Upload Documents
                </a>
            </div>
        </div>
    </div>
    @endif

    <div class="row g-4 mb-5">
        <div class="col-xl-3 col-md-6 col-12">
            <div class="dashboard-card d-flex align-items-center justify-content-between">
                <div>
                    <p class="text-secondary text-uppercase small fw-bold mb-1">মোট আয়</p>
                    <h3 class="fw-bold text-dark mb-0">৳ {{ number_format($totalProfit, 0) }}</h3>
                    @if($salesGrowth > 0)
                    <small class="text-success fw-bold"><i class="fas fa-arrow-up"></i> +{{ number_format($salesGrowth, 1) }}%</small>
                    @elseif($salesGrowth < 0)
                    <small class="text-danger fw-bold"><i class="fas fa-arrow-down"></i> {{ number_format(abs($salesGrowth), 1) }}%</small>
                    @else
                    <small class="text-muted">এই মাসে</small>
                    @endif
                </div>
                <div class="stat-icon-box bg-primary bg-opacity-10 text-primary">
                    <i class="fas fa-coins"></i>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6 col-12">
            <div class="dashboard-card d-flex align-items-center justify-content-between">
                <div>
                    <p class="text-secondary text-uppercase small fw-bold mb-1">মোট অর্ডার</p>
                    <h3 class="fw-bold text-dark mb-0">{{ $totalOrders }} টি</h3>
                    <small class="text-muted">সব সময়</small>
                </div>
                <div class="stat-icon-box bg-success bg-opacity-10 text-success">
                    <i class="fas fa-shopping-cart"></i>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6 col-12">
            <div class="dashboard-card d-flex align-items-center justify-content-between">
                <div>
                    <p class="text-secondary text-uppercase small fw-bold mb-1">পেন্ডিং ব্যালেন্স</p>
                    <h3 class="fw-bold text-dark mb-0">৳ {{ number_format($pendingBalance, 0) }}</h3>
                    <small class="text-warning fw-bold">প্রসেসিং হচ্ছে</small>
                </div>
                <div class="stat-icon-box bg-warning bg-opacity-10 text-warning">
                    <i class="fas fa-history"></i>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6 col-12">
            <div class="dashboard-card d-flex align-items-center justify-content-between">
                <div>
                    <p class="text-secondary text-uppercase small fw-bold mb-1">কাস্টমার</p>
                    <h3 class="fw-bold text-dark mb-0">{{ $totalCustomers }} জন</h3>
                    <small class="text-success fw-bold">সক্রিয়</small>
                </div>
                <div class="stat-icon-box bg-info bg-opacity-10 text-info">
                    <i class="fas fa-users"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h5 class="fw-bold m-0">🔥 জনপ্রিয় পণ্য (Hot Deals)</h5>
        <a href="{{ route('reseller.products.index') }}" class="btn btn-outline-primary btn-sm rounded-pill px-3">সব দেখুন</a>
    </div>

    <div class="row g-4 mb-5">
        @forelse($popularProducts as $product)
        <div class="col-xl-3 col-lg-4 col-sm-6">
            <div class="product-card h-100 d-flex flex-column">
                <div class="position-relative">
                    @if($loop->first)
                    <span class="product-badge">নতুন</span>
                    @elseif($loop->index < 3)
                    <span class="product-badge text-danger">হট</span>
                    @endif
                    @if($product['image'])
                        <img src="{{ asset($product['image']) }}" class="product-img" alt="{{ $product['name'] }}">
                    @else
                        <div class="product-img d-flex align-items-center justify-content-center bg-light">
                            <i class="fas fa-box fa-3x text-muted"></i>
                        </div>
                    @endif
                </div>
                <div class="p-3 flex-grow-1 d-flex flex-column">
                    <h6 class="fw-bold text-dark mb-1 text-truncate">{{ $product['name'] }}</h6>
                    <div class="d-flex justify-content-between align-items-center mt-2 mb-3">
                        <span class="profit-chip">লাভ: ৳{{ number_format($product['profit'], 0) }}</span>
                        <span class="text-muted small">স্টক: {{ $product['stock'] }}+</span>
                    </div>
                    <div class="mt-auto">
                        <div class="d-flex justify-content-between align-items-end">
                            <div>
                                <small class="text-secondary d-block" style="font-size: 11px;">রিসেলার প্রাইস</small>
                                <span class="fw-bold fs-5 text-primary">৳ {{ number_format($product['reseller_price'], 0) }}</span>
                            </div>
                            <div class="d-flex gap-2">
                                <button class="btn btn-light text-primary btn-sm rounded-circle border" title="Copy Link" onclick="copyProductLink('{{ route('product', $product['slug']) }}')">
                                    <i class="fas fa-link"></i>
                                </button>
                                {{-- Always navigate to product details page when clicking order --}}
                                <a href="{{ route('reseller.products.show', $product['slug']) }}" class="btn btn-primary btn-sm rounded-pill px-3" style="text-decoration: none;">
                                    <i class="fas fa-shopping-cart me-1"></i> অর্ডার
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @empty
        <div class="col-12">
            <div class="text-center py-5">
                <i class="fas fa-box-open fa-3x text-muted mb-3"></i>
                <p class="text-muted">কোন পণ্য পাওয়া যায়নি</p>
            </div>
        </div>
        @endforelse
    </div>

    <div class="row g-4">
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
                <div class="card-header bg-white py-3 border-bottom d-flex justify-content-between align-items-center">
                    <h6 class="fw-bold m-0">সাম্প্রতিক অর্ডার</h6>
                    <a href="{{ route('reseller.orders') }}" class="btn btn-sm btn-light border">সব দেখুন</a>
                </div>
                <div class="table-responsive">
                    <table class="table align-middle mb-0 table-hover">
                        <thead class="bg-light">
                            <tr>
                                <th class="ps-4">Invoice</th>
                                <th>Product</th>
                                <th>Customer</th>
                                <th>Profit</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($orders->take(10) as $order)
                            <tr>
                                <td class="ps-4 fw-bold text-primary">#{{ $order->invoice_id ?? $order->id }}</td>
                                <td>
                                    @if($order->orderdetails->first() && $order->orderdetails->first()->product)
                                        <div class="d-flex align-items-center">
                                            @if($order->orderdetails->first()->product->image)
                                                <img src="{{ asset($order->orderdetails->first()->product->image->image) }}" class="rounded" width="30" height="30" style="object-fit:cover;">
                                            @else
                                                <div class="rounded bg-light d-flex align-items-center justify-content-center" style="width:30px;height:30px;">
                                                    <i class="fas fa-box text-muted"></i>
                                                </div>
                                            @endif
                                            <span class="ms-2 small fw-bold">{{ Str::limit($order->orderdetails->first()->product->name ?? 'N/A', 20) }}</span>
                                        </div>
                                    @else
                                        <span class="small">N/A</span>
                                    @endif
                                </td>
                                <td class="small">{{ $order->customer->name ?? 'Guest' }}</td>
                                <td class="text-success fw-bold small">+ ৳{{ number_format($order->reseller_profit ?? 0, 0) }}</td>
                                <td>
                                    @php
                                        $status = (string)($order->order_status ?? '');
                                        $statusClass = '';
                                        $statusText = '';
                                        
                                        if($status == '6') {
                                            $statusClass = 'bg-success bg-opacity-10 text-success';
                                            $statusText = 'Delivered';
                                        } elseif($status == '11') {
                                            $statusClass = 'bg-danger bg-opacity-10 text-danger';
                                            $statusText = 'Cancelled';
                                        } elseif($status == '4') {
                                            $statusClass = 'bg-info bg-opacity-10 text-info';
                                            $statusText = 'Shipping';
                                        } else {
                                            $statusClass = 'bg-warning bg-opacity-10 text-warning';
                                            $statusText = 'Pending';
                                        }
                                    @endphp
                                    <span class="badge {{ $statusClass }} px-2">{{ $statusText }}</span>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="text-center text-muted py-4">
                                    <i class="fas fa-shopping-cart fa-2x mb-2"></i>
                                    <p>কোন অর্ডার নেই</p>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card border-0 shadow-sm rounded-4 h-100">
                <div class="card-header bg-white py-3 border-bottom">
                    <h6 class="fw-bold m-0">সাপ্তাহিক আয় (গ্রাফ)</h6>
                </div>
                <div class="card-body">
                    <canvas id="profitChart" height="200"></canvas>
                </div>
            </div>
        </div>
    </div>

@endsection

@push('scripts')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    // Add to Cart from Dashboard
    function addToCartDashboard(event, productId) {
        event.preventDefault();
        
        var form = event.target;
        var formData = new FormData(form);
        
        // Show loading
        var btn = form.querySelector('button[type="submit"]');
        var originalText = btn.innerHTML;
        btn.disabled = true;
        btn.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i> যোগ করা হচ্ছে...';
        
        fetch('{{ route("reseller.cart.add.ajax") }}', {
            method: 'POST',
            body: formData,
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Show success toast
                showToast('সফল!', 'প্রোডাক্ট কার্টে যোগ করা হয়েছে', 'success');
                
                // Update cart count in sidebar
                updateCartCount(data.cart_count);
                
                // Reload page after 1.5 second to show updated cart
                setTimeout(function() {
                    window.location.reload();
                }, 1500);
            } else {
                showToast('ত্রুটি!', data.message || 'কার্টে যোগ করতে সমস্যা হয়েছে', 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showToast('ত্রুটি!', 'কার্টে যোগ করতে সমস্যা হয়েছে', 'error');
        })
        .finally(() => {
            btn.disabled = false;
            btn.innerHTML = originalText;
        });
        
        return false;
    }
    
    // Update Cart Count in Sidebar
    function updateCartCount(count) {
        // Update sidebar cart badge
        var sidebarBadge = document.getElementById('sidebar-cart-badge');
        if (sidebarBadge) {
            if (count > 0) {
                sidebarBadge.textContent = count;
                sidebarBadge.style.display = 'inline-block';
            } else {
                sidebarBadge.style.display = 'none';
            }
        }
        
        // Also try to find by selector as fallback
        var fallbackBadge = document.querySelector('.sidebar .nav-link[href*="checkout"] .badge');
        if (fallbackBadge && !sidebarBadge) {
            if (count > 0) {
                fallbackBadge.textContent = count;
                fallbackBadge.style.display = 'inline-block';
            } else {
                fallbackBadge.style.display = 'none';
            }
        }
        
        // Update any other cart count elements
        var cartCountElements = document.querySelectorAll('.cart-count, #cart-count, [data-cart-count]');
        cartCountElements.forEach(function(el) {
            el.textContent = count;
        });
    }
    
    // Show Toast Notification
    function showToast(title, message, type) {
        let container = document.getElementById('toast-container');
        if (!container) {
            container = document.createElement('div');
            container.id = 'toast-container';
            container.className = 'position-fixed bottom-0 end-0 p-3';
            container.style.zIndex = '9999';
            document.body.appendChild(container);
        }

        const toastId = 'toast-' + Date.now();
        const bgColor = type === 'success' ? '#10b981' : '#ef4444';
        const icon = type === 'success' ? 'fa-check-circle' : 'fa-exclamation-circle';
        
        const toastHtml = `
            <div id="${toastId}" class="toast custom-toast align-items-center border-0" role="alert" aria-live="assertive" aria-atomic="true">
                <div class="d-flex">
                    <div class="toast-body d-flex align-items-center py-3">
                        <i class="fas ${icon} fa-lg me-3" style="color: ${bgColor};"></i>
                        <div>
                            <h6 class="fw-bold mb-0">${title}</h6>
                            <small class="text-muted">${message}</small>
                        </div>
                    </div>
                    <button type="button" class="btn-close me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
                </div>
            </div>
        `;
        
        container.insertAdjacentHTML('beforeend', toastHtml);
        const toastElement = document.getElementById(toastId);
        const bsToast = new bootstrap.Toast(toastElement);
        bsToast.show();

        toastElement.addEventListener('hidden.bs.toast', function () {
            toastElement.remove();
        });
    }

    // Copy Product Link
    function copyProductLink(url) {
        navigator.clipboard.writeText(url).then(function() {
            showToast('সফল!', 'লিংক কপি হয়েছে', 'success');
        });
    }

    // Profit Chart
    const ctx = document.getElementById('profitChart');
    if (ctx) {
        new Chart(ctx.getContext('2d'), {
            type: 'bar',
            data: {
                labels: {!! json_encode($weeklyLabels ?? []) !!},
                datasets: [{
                    label: 'Profit (৳)',
                    data: {!! json_encode($weeklyProfit ?? []) !!},
                    backgroundColor: '#4f46e5',
                    borderRadius: 4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { 
                    legend: { display: false },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                return 'আয়: ৳' + context.parsed.y.toLocaleString('bn-BD');
                            }
                        }
                    }
                },
                scales: {
                    y: { 
                        beginAtZero: true, 
                        grid: { display: false },
                        ticks: {
                            callback: function(value) {
                                return '৳' + value.toLocaleString('bn-BD');
                            }
                        }
                    },
                    x: { grid: { display: false } }
                }
            }
        });
    }
</script>
@endpush
