@extends('vendor.layouts.app')

@section('title', 'Dashboard')
@section('page-title', 'Dashboard')

@push('styles')
<style>
    .avatar-sm { width: 34px; height: 34px; border-radius: 50%; object-fit: cover; }
    .chart-container { position: relative; height: 300px; width: 100%; }
    @media (max-width: 991px) { .chart-container { height: 250px; } }
    .pp-row { display: flex; align-items: center; gap: 12px; padding: 9px 0; border-bottom: 1px dashed #eef0f4; }
    .pp-row:last-child { border-bottom: 0; }
</style>
@endpush

@push('header-search')
    <!-- Search bar will be shown in header -->
@endpush

@push('header-notifications')
    @if(isset($pendingOrders) && $pendingOrders > 0)
    <span class="position-absolute top-0 start-100 translate-middle p-1 bg-danger border border-light rounded-circle"></span>
    @endif
@endpush

@push('notification-items')
    <!-- Pending Orders Notification (Dashboard specific) -->
    @if(isset($pendingOrders) && $pendingOrders > 0)
    <li><hr class="dropdown-divider"></li>
    <li>
        <div class="px-3 py-2 border-bottom">
            <h6 class="mb-1 fw-bold text-dark">
                <i class="fas fa-shopping-cart text-info me-2"></i>
                নতুন অর্ডার
            </h6>
        </div>
    </li>
    <li>
        <a class="dropdown-item py-3" href="{{ route('vendor.orders') }}">
            <div class="d-flex align-items-center">
                <div class="flex-shrink-0">
                    <div class="rounded-circle bg-info bg-opacity-10 d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                        <i class="fas fa-bell text-info"></i>
                    </div>
                </div>
                <div class="flex-grow-1 ms-3">
                    <p class="mb-0 fw-semibold">নতুন অর্ডার</p>
                    <small class="text-muted">{{ $pendingOrders }} টি পেন্ডিং অর্ডার</small>
                </div>
                <div class="flex-shrink-0">
                    <span class="badge bg-danger rounded-pill">{{ $pendingOrders }}</span>
                </div>
            </div>
        </a>
    </li>
    @endif
    
    <!-- Empty State -->
    @if($vendor->verification_status == 'approved' && (!isset($pendingOrders) || $pendingOrders == 0))
    <li>
        <div class="px-3 py-4 text-center text-muted">
            <i class="fas fa-check-circle fa-2x mb-2 opacity-25"></i>
            <p class="mb-0 small">কোন নোটিফিকেশন নেই</p>
        </div>
    </li>
    @endif
@endpush

@section('content')
@php
    $growth = $salesGrowth ?? 0;
    $vCards = [
        ['fa-sack-dollar', 'মোট বিক্রি (মাসিক)', '৳ '.number_format($monthlySales ?? 0, 0), $growth != 0 ? (($growth > 0 ? '▲ ' : '▼ ').number_format(abs($growth), 1).'% '.($growth > 0 ? 'বৃদ্ধি' : 'হ্রাস')) : 'গত মাস: ৳'.number_format($lastMonthSales ?? 0, 0), '#bfd7fd', '#1d4ed8', route('vendor.analytics')],
        ['fa-basket-shopping', 'নতুন অর্ডার', ($newOrders ?? 0).' টি', ($pendingOrders ?? 0).' পেন্ডিং', '#c0ead7', '#047857', route('vendor.orders')],
        ['fa-rotate-left', 'রিটার্ন এসেছে', ($returnOrders ?? 0).' টি', ($returnOrders ?? 0) > 0 ? 'অ্যাকশন প্রয়োজন' : 'কোন রিটার্ন নেই', '#fbd0d0', '#b91c1c', route('vendor.refunds.index')],
        ['fa-hourglass-half', 'পেন্ডিং রিকোয়েস্ট', ($pendingOrders ?? 0).' টি', 'অ্যাকশন প্রয়োজন', '#fde6c2', '#c2410c', route('vendor.orders')],
    ];
@endphp

    <div class="row g-4 mb-4">
        @foreach ($vCards as [$icon, $label, $value, $sub, $bg, $color, $link])
            <div class="col-6 col-xl-3">
                <a href="{{ $link }}" class="st-card" style="background:{{ $bg }}">
                    <span class="st-l">{{ $label }}</span>
                    <span class="st-ic" style="color:{{ $color }}"><i class="fa-solid {{ $icon }}"></i></span>
                    <span class="st-v" style="color:{{ $color }}">{{ $value }}</span>
                    <span class="st-s">{{ $sub }}</span>
                </a>
            </div>
        @endforeach
    </div>

    <div class="row g-4 mb-4">
        <div class="col-lg-8">
            <div class="dp purple">
                <div class="dp-head">
                    <h6><i class="fa-solid fa-chart-line me-1" style="color:#7c3aed"></i> বিক্রয় পরিসংখ্যান</h6>
                    <select class="form-select form-select-sm" id="chartPeriod">
                        <option value="week">এই সপ্তাহ</option>
                        <option value="month">এই মাস</option>
                    </select>
                </div>
                <div class="dp-body"><div class="chart-container"><canvas id="salesChart"></canvas></div></div>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="dp green">
                <div class="dp-head"><h6><i class="fa-solid fa-fire me-1 text-success"></i> জনপ্রিয় পণ্য</h6></div>
                <div class="dp-body">
                @forelse($popularProducts ?? [] as $item)
                <div class="pp-row">
                    <div class="bg-light p-2 rounded">
                        @if($item->product && $item->product->image && $item->product->image->image)
                            <img src="{{ asset($item->product->image->image) }}" alt="{{ $item->product->name }}" style="width: 30px; height: 30px; object-fit: cover; border-radius: 4px;">
                        @else
                            <i class="fas fa-box fa-lg text-secondary"></i>
                        @endif
                    </div>
                    <div class="flex-grow-1">
                        <h6 class="mb-0 fw-semibold">{{ $item->product->name ?? 'N/A' }}</h6>
                        <small class="text-muted">{{ $item->total_sold ?? 0 }} টি বিক্রি</small>
                    </div>
                    <span class="fw-bold text-success">৳{{ number_format($item->total_revenue ?? 0, 0) }}</span>
                </div>
                @empty
                <div class="text-center text-muted py-4">
                    <i class="fas fa-box-open fa-3x mb-3 opacity-25"></i>
                    <p>কোন জনপ্রিয় পণ্য নেই</p>
                </div>
                @endforelse
                </div>
            </div>
        </div>
    </div>

    <div class="dp yellow">
        <div class="dp-head">
            <h6><i class="fa-solid fa-bag-shopping me-1 text-warning"></i> সাম্প্রতিক অর্ডার</h6>
            <a href="{{ route('vendor.orders') }}" class="small">সব দেখুন <i class="fa-solid fa-arrow-right"></i></a>
        </div>
        <div class="dp-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead>
                        <tr><th>আইডি</th><th>কাস্টমার</th><th>তারিখ</th><th>অ্যামাউন্ট</th><th>পেমেন্ট</th><th>স্ট্যাটাস</th><th>অ্যাকশন</th></tr>
                    </thead>
                    <tbody>
                    @forelse($recentOrders ?? [] as $order)
                    <tr>
                        <td class="fw-bold">#{{ $order->invoice_id ?? $order->id }}</td>
                        <td>
                            <div class="d-flex align-items-center">
                                @if($order->customer && $order->customer->image)
                                    <img src="{{ asset($order->customer->image) }}" class="avatar-sm me-2" alt="">
                                @else
                                    <div class="avatar-sm bg-light d-flex align-items-center justify-content-center me-2">
                                        <i class="fas fa-user text-secondary"></i>
                                    </div>
                                @endif
                                {{ $order->customer->name ?? 'N/A' }}
                            </div>
                        </td>
                        <td>{{ $order->created_at ? $order->created_at->format('d M, Y') : 'N/A' }}</td>
                        <td>৳{{ number_format($order->amount ?? 0, 2) }}</td>
                        <td>
                            @if($order->payment)
                                @php
                                    $paymentMethod = strtolower($order->payment->payment_method ?? '');
                                @endphp
                                @if(str_contains($paymentMethod, 'card') || str_contains($paymentMethod, 'visa'))
                                    <span class="badge bg-light text-dark border"><i class="fab fa-cc-visa text-primary"></i> কার্ড</span>
                                @elseif(str_contains($paymentMethod, 'mastercard'))
                                    <span class="badge bg-light text-dark border"><i class="fab fa-cc-mastercard text-danger"></i> মাস্টারকার্ড</span>
                                @else
                                    <span class="badge bg-light text-dark border"><i class="fas fa-money-bill text-success"></i> {{ ucfirst($paymentMethod) }}</span>
                                @endif
                            @else
                                <span class="badge bg-light text-dark border">N/A</span>
                            @endif
                        </td>
                        <td>
                            @php
                                $status = (string)($order->order_status ?? '');
                                $statusClass = '';
                                $statusText = '';
                                
                                if($status == '6') {
                                    $statusClass = 'bg-success bg-opacity-10 text-success';
                                    $statusText = 'ডেলিভারড';
                                } elseif($status == '11') {
                                    $statusClass = 'bg-danger bg-opacity-10 text-danger';
                                    $statusText = 'বাতিল';
                                } elseif(in_array($status, ['1', '2', '3'])) {
                                    $statusClass = 'bg-warning bg-opacity-10 text-warning';
                                    $statusText = 'পেন্ডিং';
                                } elseif($status == '4') {
                                    $statusClass = 'bg-info bg-opacity-10 text-info';
                                    $statusText = 'শিপিং';
                                } else {
                                    $statusClass = 'bg-secondary bg-opacity-10 text-secondary';
                                    $statusText = 'অন্যান্য';
                                }
                            @endphp
                            <span class="badge {{ $statusClass }} px-3">{{ $statusText }}</span>
                        </td>
                        <td>
                            <a href="{{ route('vendor.orders') }}?search={{ $order->invoice_id ?? $order->id }}" class="text-muted">
                                <i class="fas fa-eye"></i>
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center text-muted py-4">
                            <i class="fas fa-shopping-cart fa-3x mb-3 opacity-25"></i>
                            <p>কোন অর্ডার নেই</p>
                        </td>
                    </tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // Toggle Sidebar
    function toggleSidebar() {
        const sidebar = document.getElementById('sidebar');
        const overlay = document.getElementById('sidebarOverlay');
        if (sidebar) sidebar.classList.toggle('show');
        if (overlay) overlay.classList.toggle('show');
    }

    // Chart Data
    const weeklyData = {
        labels: {!! json_encode($weeklyLabels ?? []) !!},
        data: {!! json_encode($weeklySales ?? []) !!}
    };
    
    const monthlyData = {
        labels: {!! json_encode($monthlyLabels ?? []) !!},
        data: {!! json_encode($monthlySalesData ?? []) !!}
    };

    // Initialize Sales Chart
    const ctx = document.getElementById('salesChart');
    let salesChart = null;
    
    function updateChart(period) {
        const chartData = period === 'month' ? monthlyData : weeklyData;
        
        if (salesChart) {
            salesChart.destroy();
        }
        
        if (ctx) {
            salesChart = new Chart(ctx.getContext('2d'), {
                type: 'line',
                data: {
                    labels: chartData.labels,
                    datasets: [{
                        label: 'বিক্রয় (টাকা)',
                        data: chartData.data,
                        backgroundColor: 'rgba(45, 45, 232, 0.08)',
                        borderColor: '#2d2de8',
                        borderWidth: 2,
                        pointBackgroundColor: '#fff',
                        pointBorderColor: '#2d2de8',
                        pointRadius: 4,
                        pointHoverRadius: 6,
                        fill: true,
                        tension: 0.4
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: false
                        },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    return 'বিক্রয়: ৳' + context.parsed.y.toLocaleString('bn-BD');
                                }
                            }
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            grid: {
                                borderDash: [2, 4],
                                color: '#eaecf4',
                                drawBorder: false
                            },
                            ticks: {
                                callback: function(value) {
                                    return '৳' + value.toLocaleString('bn-BD');
                                }
                            }
                        },
                        x: {
                            grid: {
                                display: false
                            },
                            ticks: {
                                maxRotation: 45,
                                minRotation: 45
                            }
                        }
                    }
                }
            });
        }
    }

    // Initialize with weekly data
    if (ctx) {
        updateChart('week');
    }

    // Handle period change
    const chartPeriod = document.getElementById('chartPeriod');
    if (chartPeriod) {
        chartPeriod.addEventListener('change', function() {
            updateChart(this.value);
        });
    }
</script>
@endpush
