@extends('vendor.layouts.app')

@section('title', 'Analytics')
@section('page-title', 'Analytics & Reports')

@push('styles')
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600&display=swap" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<style>
    :root {
        --primary: #4f46e5;
        --secondary: #64748b;
        --success: #10b981;
        --warning: #f59e0b;
        --danger: #ef4444;
        --dark: #1e293b;
        --light: #f8fafc;
        --border: #e2e8f0;
    }

    body {
        font-family: 'Inter', sans-serif;
        background-color: #f1f5f9;
        color: var(--dark);
    }

    .analytics-card {
        background: #fff;
        border: 1px solid var(--border);
        border-radius: 12px;
        padding: 24px;
        box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
        height: 100%;
    }

    .stat-card {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        border-radius: 12px;
        padding: 24px;
        height: 100%;
    }

    .stat-card.success {
        background: linear-gradient(135deg, #10b981 0%, #059669 100%);
    }

    .stat-card.warning {
        background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
    }

    .stat-card.danger {
        background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
    }

    .chart-container {
        position: relative;
        height: 300px;
        margin-top: 20px;
    }

    .product-item {
        padding: 12px;
        border-bottom: 1px solid var(--border);
        transition: background 0.2s;
    }

    .product-item:hover {
        background: var(--light);
    }

    .product-item:last-child {
        border-bottom: none;
    }
</style>
@endpush

@section('content')
<div class="container-fluid px-0">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold mb-1 text-dark">Analytics & Reports</h4>
            <p class="text-secondary small mb-0">Detailed insights into your business performance</p>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row g-4 mb-4">
        <div class="col-md-3">
            <div class="stat-card">
                <p class="mb-2 small opacity-90">Total Sales</p>
                <h2 class="fw-bold mb-0">৳{{ number_format($totalSales ?? 0, 0) }}</h2>
                <small class="opacity-75">All time revenue</small>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card success">
                <p class="mb-2 small opacity-90">Total Orders</p>
                <h2 class="fw-bold mb-0">{{ $totalOrders ?? 0 }}</h2>
                <small class="opacity-75">{{ $deliveredOrders ?? 0 }} delivered</small>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card warning">
                <p class="mb-2 small opacity-90">Pending Orders</p>
                <h2 class="fw-bold mb-0">{{ $pendingOrders ?? 0 }}</h2>
                <small class="opacity-75">Awaiting processing</small>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card danger">
                <p class="mb-2 small opacity-90">Avg Order Value</p>
                <h2 class="fw-bold mb-0">৳{{ number_format($avgOrderValue ?? 0, 0) }}</h2>
                <small class="opacity-75">Per order average</small>
            </div>
        </div>
    </div>

    <!-- Charts Row -->
    <div class="row g-4 mb-4">
        <!-- Monthly Sales Chart -->
        <div class="col-lg-8">
            <div class="analytics-card">
                <h5 class="fw-bold mb-4 text-primary">Monthly Sales (Last 12 Months)</h5>
                <div class="chart-container">
                    <canvas id="monthlyChart"></canvas>
                </div>
            </div>
        </div>

        <!-- Order Status Distribution -->
        <div class="col-lg-4">
            <div class="analytics-card">
                <h5 class="fw-bold mb-4 text-primary">Order Status</h5>
                <div class="chart-container">
                    <canvas id="statusChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Daily Sales & Top Products -->
    <div class="row g-4 mb-4">
        <!-- Daily Sales Chart -->
        <div class="col-lg-8">
            <div class="analytics-card">
                <h5 class="fw-bold mb-4 text-primary">Daily Sales (Last 30 Days)</h5>
                <div class="chart-container">
                    <canvas id="dailyChart"></canvas>
                </div>
            </div>
        </div>

        <!-- Top Products -->
        <div class="col-lg-4">
            <div class="analytics-card">
                <h5 class="fw-bold mb-4 text-primary">Top Selling Products</h5>
                <div style="max-height: 300px; overflow-y: auto;">
                    @forelse($topProducts ?? [] as $product)
                        <div class="product-item d-flex align-items-center">
                            <div class="flex-shrink-0 me-3">
                                @if($product->product && $product->product->image)
                                    <img src="{{ asset($product->product->image->image) }}" 
                                         alt="{{ $product->product->name }}" 
                                         style="width: 50px; height: 50px; object-fit: cover; border-radius: 8px;">
                                @else
                                    <div class="bg-light d-flex align-items-center justify-content-center" 
                                         style="width: 50px; height: 50px; border-radius: 8px;">
                                        <i class="fas fa-box text-muted"></i>
                                    </div>
                                @endif
                            </div>
                            <div class="flex-grow-1">
                                <h6 class="mb-0 fw-semibold">{{ $product->product->name ?? 'N/A' }}</h6>
                                <small class="text-muted">Sold: {{ $product->total_sold ?? 0 }} units</small>
                            </div>
                            <div class="text-end">
                                <p class="mb-0 fw-bold text-success">৳{{ number_format($product->total_revenue ?? 0, 0) }}</p>
                            </div>
                        </div>
                    @empty
                        <p class="text-muted text-center py-3">No products found</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    <!-- Current Year Revenue -->
    <div class="row g-4">
        <div class="col-12">
            <div class="analytics-card">
                <h5 class="fw-bold mb-4 text-primary">Revenue by Month ({{ date('Y') }})</h5>
                <div class="chart-container">
                    <canvas id="yearlyChart"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Monthly Sales Chart
    const monthlyCtx = document.getElementById('monthlyChart');
    if (monthlyCtx) {
        new Chart(monthlyCtx.getContext('2d'), {
            type: 'line',
            data: {
                labels: {!! json_encode($monthlyLabels ?? []) !!},
                datasets: [{
                    label: 'Sales (৳)',
                    data: {!! json_encode($monthlySales ?? []) !!},
                    borderColor: '#4f46e5',
                    backgroundColor: 'rgba(79, 70, 229, 0.1)',
                    borderWidth: 2,
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
                                return 'Sales: ৳' + context.parsed.y.toLocaleString('bn-BD');
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(value) {
                                return '৳' + value.toLocaleString('bn-BD');
                            }
                        }
                    }
                }
            }
        });
    }

    // Order Status Chart
    const statusCtx = document.getElementById('statusChart');
    if (statusCtx) {
        const statusData = {!! json_encode($orderStatusData ?? []) !!};
        new Chart(statusCtx.getContext('2d'), {
            type: 'doughnut',
            data: {
                labels: statusData.map(item => item.status),
                datasets: [{
                    data: statusData.map(item => item.count),
                    backgroundColor: [
                        '#f59e0b',
                        '#3b82f6',
                        '#8b5cf6',
                        '#10b981',
                        '#ef4444'
                    ]
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom'
                    }
                }
            }
        });
    }

    // Daily Sales Chart
    const dailyCtx = document.getElementById('dailyChart');
    if (dailyCtx) {
        new Chart(dailyCtx.getContext('2d'), {
            type: 'bar',
            data: {
                labels: {!! json_encode($dailyLabels ?? []) !!},
                datasets: [{
                    label: 'Daily Sales (৳)',
                    data: {!! json_encode($dailySales ?? []) !!},
                    backgroundColor: 'rgba(16, 185, 129, 0.8)',
                    borderColor: '#10b981',
                    borderWidth: 1
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
                                return 'Sales: ৳' + context.parsed.y.toLocaleString('bn-BD');
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(value) {
                                return '৳' + value.toLocaleString('bn-BD');
                            }
                        }
                    }
                }
            }
        });
    }

    // Yearly Revenue Chart
    const yearlyCtx = document.getElementById('yearlyChart');
    if (yearlyCtx) {
        new Chart(yearlyCtx.getContext('2d'), {
            type: 'bar',
            data: {
                labels: {!! json_encode($currentYearLabels ?? []) !!},
                datasets: [{
                    label: 'Revenue (৳)',
                    data: {!! json_encode($currentYearRevenue ?? []) !!},
                    backgroundColor: 'rgba(79, 70, 229, 0.8)',
                    borderColor: '#4f46e5',
                    borderWidth: 1
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
                                return 'Revenue: ৳' + context.parsed.y.toLocaleString('bn-BD');
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(value) {
                                return '৳' + value.toLocaleString('bn-BD');
                            }
                        }
                    }
                }
            }
        });
    }
</script>
@endpush
