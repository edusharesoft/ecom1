@extends('vendor.layouts.app')

@section('title', 'Order Management')
@section('page-title', 'Order List')

@push('styles')
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600&display=swap" rel="stylesheet">
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

    /* Filters Section */
    .filter-card {
        background: #fff;
        border: 1px solid var(--border);
        border-radius: 12px;
        padding: 20px;
        margin-bottom: 24px;
        box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
    }

    .form-control, .form-select {
        border-radius: 8px;
        border: 1px solid var(--border);
        padding: 10px 15px;
        font-size: 0.9rem;
    }
    .form-control:focus, .form-select:focus {
        border-color: var(--primary);
        box-shadow: 0 0 0 3px rgba(79, 70, 229, 0.1);
    }

    /* Table Styling */
    .table-card {
        background: #fff;
        border: 1px solid var(--border);
        border-radius: 12px;
        overflow: hidden;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
    }

    .custom-table {
        width: 100%;
        border-collapse: separate;
        border-spacing: 0;
    }

    .custom-table thead th {
        background-color: #f8fafc;
        color: var(--secondary);
        font-weight: 600;
        font-size: 0.75rem;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        padding: 16px 24px;
        border-bottom: 1px solid var(--border);
    }

    .custom-table tbody tr {
        transition: all 0.2s;
    }
    .custom-table tbody tr:hover {
        background-color: #f8fafc;
    }

    .custom-table td {
        padding: 16px 24px;
        vertical-align: middle;
        border-bottom: 1px solid var(--border);
        font-size: 0.9rem;
        color: var(--dark);
    }

    .custom-table tbody tr:last-child td {
        border-bottom: none;
    }

    /* Product Images Stack */
    .product-stack {
        display: flex;
        align-items: center;
    }
    .product-thumb {
        width: 40px;
        height: 40px;
        border-radius: 8px;
        object-fit: cover;
        border: 2px solid #fff;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        margin-right: -10px; /* Overlap effect */
        background-color: #eee;
        position: relative;
        z-index: 1;
        transition: z-index 0.2s;
    }
    .product-thumb:hover {
        z-index: 10;
        transform: scale(1.1);
    }
    .product-count-badge {
        width: 40px;
        height: 40px;
        border-radius: 8px;
        background-color: #f1f5f9;
        color: var(--secondary);
        font-size: 12px;
        font-weight: 600;
        display: flex;
        align-items: center;
        justify-content: center;
        border: 2px solid #fff;
        margin-left: 5px; /* Adjust spacing */
        z-index: 0;
    }

    /* Avatar */
    .avatar-initial {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        background-color: #e0e7ff;
        color: var(--primary);
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 600;
        font-size: 14px;
    }

    /* Status Badges */
    .badge-soft {
        padding: 6px 12px;
        border-radius: 20px;
        font-weight: 500;
        font-size: 0.75rem;
        display: inline-flex;
        align-items: center;
    }
    .badge-soft-success { background-color: #d1fae5; color: #065f46; }
    .badge-soft-warning { background-color: #fef3c7; color: #92400e; }
    .badge-soft-danger  { background-color: #fee2e2; color: #991b1b; }
    .badge-soft-info    { background-color: #dbeafe; color: #1e40af; }
    .badge-soft-secondary { background-color: #f1f5f9; color: #475569; }

    /* Action Buttons */
    .btn-icon {
        width: 32px;
        height: 32px;
        border-radius: 6px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        color: var(--secondary);
        transition: 0.2s;
    }
    .btn-icon:hover {
        background-color: #f1f5f9;
        color: var(--primary);
    }

    .order-id-link {
        color: var(--primary);
        font-weight: 600;
        text-decoration: none;
    }
    .order-id-link:hover {
        text-decoration: underline;
    }

    /* Product Details Modal */
    .product-detail-item {
        padding: 12px;
        border-bottom: 1px solid #f0f0f0;
    }
    .product-detail-item:last-child {
        border-bottom: none;
    }
    .product-detail-img {
        width: 50px;
        height: 50px;
        object-fit: cover;
        border-radius: 8px;
    }

</style>
@endpush

@section('content')
<div class="container-fluid px-0">

    <div class="d-flex flex-column flex-md-row justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold mb-1 text-dark">Orders Overview</h4>
            <p class="text-secondary small mb-0">Manage orders and view product details.</p>
        </div>
        <div class="mt-3 mt-md-0 d-flex gap-2">
            <a href="{{ route('vendor.orders.export', request()->all()) }}" class="btn btn-white border bg-white shadow-sm fw-medium">
                <i class="fas fa-download me-2"></i>Export CSV
            </a>
        </div>
    </div>

    <div class="row g-4 mb-4">
        <div class="col-md-3">
            <div class="card border-0 shadow-sm p-3">
                <p class="text-secondary small mb-1 text-uppercase fw-bold">Total Orders</p>
                <h3 class="fw-bold mb-0 text-dark">{{ $orders->total() }}</h3>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm p-3">
                <p class="text-secondary small mb-1 text-uppercase fw-bold">Total Revenue</p>
                <h3 class="fw-bold mb-0 text-success">৳{{ number_format($totalRevenue ?? 0, 0) }}</h3>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm p-3">
                <p class="text-secondary small mb-1 text-uppercase fw-bold">Pending Orders</p>
                <h3 class="fw-bold mb-0 text-warning">{{ $orders->whereIn('order_status', ['1', '2', '3'])->count() }}</h3>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm p-3">
                <p class="text-secondary small mb-1 text-uppercase fw-bold">Delivered</p>
                <h3 class="fw-bold mb-0 text-success">{{ $orders->where('order_status', '6')->count() }}</h3>
            </div>
        </div>
    </div>

    <div class="filter-card">
        <form action="" method="GET">
            <div class="row g-3">
                <div class="col-md-4">
                    <div class="position-relative">
                        <i class="fas fa-search position-absolute text-secondary" style="top: 13px; left: 15px;"></i>
                        <input type="text" name="keyword" class="form-control ps-5" placeholder="Search by Order ID, Customer name..." value="{{ request('keyword') }}">
                    </div>
                </div>
                <div class="col-md-3">
                    <select name="status" class="form-select">
                        <option value="">All Status</option>
                        <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="delivered" {{ request('status') == 'delivered' ? 'selected' : '' }}>Delivered</option>
                        <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <input type="date" name="date" class="form-control" value="{{ request('date') }}">
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-primary w-100 fw-medium">Filter</button>
                </div>
            </div>
        </form>
    </div>

    <div class="table-card">
        <div class="table-responsive">
            <table class="custom-table">
                <thead>
                    <tr>
                        <th>Order ID</th>
                        <th>Products</th> <th>Customer</th>
                        <th>Date</th>
                        <th>Amount</th>
                        <th>Status</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($orders as $order)
                    <tr>
                        <td>
                            <a href="#" class="order-id-link">#{{ $order->invoice_id ?? $order->id }}</a>
                        </td>

                        <td>
                            <div class="product-stack" data-bs-toggle="modal" data-bs-target="#orderModal{{ $order->id }}" style="cursor: pointer;">
                                @php
                                    $maxDisplay = 3;
                                    $count = $order->orderdetails->count();
                                @endphp

                                @foreach($order->orderdetails->take($maxDisplay) as $detail)
                                    @if($detail->product && $detail->product->image)
                                        <img src="{{ asset($detail->product->image->image) }}" 
                                             class="product-thumb" 
                                             alt="{{ $detail->product->name }}"
                                             title="{{ $detail->product->name }} (Qty: {{ $detail->qty }})">
                                    @else
                                        <div class="product-thumb d-flex align-items-center justify-content-center bg-light text-muted small">
                                            <i class="fas fa-box"></i>
                                        </div>
                                    @endif
                                @endforeach

                                @if($count > $maxDisplay)
                                    <div class="product-count-badge">
                                        +{{ $count - $maxDisplay }}
                                    </div>
                                @endif
                            </div>
                            
                            <!-- Product Details Modal -->
                            <div class="modal fade" id="orderModal{{ $order->id }}" tabindex="-1" aria-hidden="true">
                                <div class="modal-dialog modal-lg">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title fw-bold">Order #{{ $order->invoice_id ?? $order->id }} - Products</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                        </div>
                                        <div class="modal-body">
                                            @if($order->orderdetails && $order->orderdetails->count() > 0)
                                                @foreach($order->orderdetails as $detail)
                                                    <div class="product-detail-item d-flex align-items-center">
                                                        <div class="flex-shrink-0 me-3">
                                                            @if($detail->product && $detail->product->image)
                                                                <img src="{{ asset($detail->product->image->image) }}" 
                                                                     class="product-detail-img" 
                                                                     alt="{{ $detail->product->name }}">
                                                            @else
                                                                <div class="product-detail-img bg-light d-flex align-items-center justify-content-center">
                                                                    <i class="fas fa-image text-muted"></i>
                                                                </div>
                                                            @endif
                                                        </div>
                                                        <div class="flex-grow-1">
                                                            <h6 class="mb-1 fw-bold">{{ $detail->product->name ?? 'Product' }}</h6>
                                                            <div class="d-flex flex-wrap gap-3 small text-muted">
                                                                <span><i class="fas fa-box me-1"></i>Quantity: {{ $detail->qty ?? 0 }}</span>
                                                                @if($detail->product && $detail->product->pro_unit)
                                                                    <span><i class="fas fa-ruler me-1"></i>Unit: {{ $detail->product->pro_unit }}</span>
                                                                @endif
                                                                @if($detail->product_color || $detail->product_size)
                                                                    <span>
                                                                        @if($detail->product_color && $detail->color)
                                                                            <i class="fas fa-palette me-1"></i>Color: {{ $detail->color->name ?? 'N/A' }}
                                                                        @endif
                                                                        @if($detail->product_size && $detail->size)
                                                                            @if($detail->product_color) | @endif
                                                                            <i class="fas fa-ruler-combined me-1"></i>Size: {{ $detail->size->name ?? 'N/A' }}
                                                                        @endif
                                                                    </span>
                                                                @endif
                                                            </div>
                                                        </div>
                                                        <div class="flex-shrink-0 text-end">
                                                            <p class="mb-0 fw-bold text-primary">৳{{ number_format($detail->sale_price ?? 0, 2) }}</p>
                                                            <small class="text-muted">Total: ৳{{ number_format(($detail->sale_price ?? 0) * ($detail->qty ?? 0), 2) }}</small>
                                                        </div>
                                                    </div>
                                                @endforeach
                                            @else
                                                <p class="text-muted text-center py-3">No products found</p>
                                            @endif
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </td>

                        <td>
                            <div class="d-flex align-items-center">
                                <div class="avatar-initial me-3">
                                    {{ strtoupper(substr($order->customer->name ?? 'G', 0, 1)) }}
                                </div>
                                <div>
                                    <h6 class="mb-0 fw-semibold text-dark">{{ $order->customer->name ?? 'Guest User' }}</h6>
                                    <small class="text-secondary">{{ $order->customer->phone ?? 'No Phone' }}</small>
                                </div>
                            </div>
                        </td>

                        <td>
                            <div class="d-flex flex-column">
                                <span class="fw-medium">{{ $order->created_at->format('M d, Y') }}</span>
                                <small class="text-secondary">{{ $order->created_at->format('h:i A') }}</small>
                            </div>
                        </td>

                        <td>
                            <span class="fw-bold text-dark">৳{{ number_format($order->amount, 0) }}</span>
                            <div class="small text-secondary">{{ $order->payment->payment_method ?? 'COD' }}</div>
                        </td>

                        <td>
                            @php
                                $status = (string)($order->order_status ?? '');
                                $badgeClass = 'badge-soft-secondary';
                                $statusName = 'Unknown';

                                if ($status == '1' || $status == 1) { 
                                    $badgeClass = 'badge-soft-warning'; $statusName = 'Pending'; 
                                } elseif ($status == '2' || $status == 2) { 
                                    $badgeClass = 'badge-soft-info'; $statusName = 'Processing'; 
                                } elseif ($status == '3' || $status == 3) { 
                                    $badgeClass = 'badge-soft-info'; $statusName = 'Shipping'; 
                                } elseif ($status == '6' || $status == 6) { 
                                    $badgeClass = 'badge-soft-success'; $statusName = 'Delivered'; 
                                } elseif ($status == '8' || $status == 8 || $status == '11' || $status == 11) { 
                                    $badgeClass = 'badge-soft-danger'; $statusName = 'Cancelled'; 
                                }
                            @endphp
                            <span class="badge-soft {{ $badgeClass }}">
                                <span class="me-1" style="width: 6px; height: 6px; border-radius: 50%; background-color: currentColor; display: inline-block;"></span>
                                {{ $order->status->name ?? $statusName }}
                            </span>
                        </td>

                        <td class="text-end">
                            <div class="dropdown">
                                <button class="btn btn-icon" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                    <i class="fas fa-ellipsis-v"></i>
                                </button>
                                <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0">
                                    <li>
                                        <a class="dropdown-item py-2" href="#" data-bs-toggle="modal" data-bs-target="#orderModal{{ $order->id }}">
                                            <i class="fas fa-eye me-2 text-primary"></i> View Details
                                        </a>
                                    </li>
                                    <li>
                                        <a class="dropdown-item py-2" href="{{ route('customer.invoice') }}?id={{ $order->id }}" target="_blank">
                                            <i class="fas fa-file-invoice me-2 text-secondary"></i> Invoice
                                        </a>
                                    </li>
                                    <li><hr class="dropdown-divider"></li>
                                    @if($order->order_status != '6' && $order->order_status != '11')
                                    <li>
                                        <a class="dropdown-item py-2 text-danger" href="#" onclick="return confirm('Are you sure you want to cancel this order?')">
                                            <i class="fas fa-ban me-2"></i> Cancel Order
                                        </a>
                                    </li>
                                    @endif
                                </ul>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center py-5">
                            <div class="d-flex flex-column align-items-center justify-content-center">
                                <div class="bg-light rounded-circle p-4 mb-3">
                                    <i class="fas fa-box-open fa-3x text-secondary opacity-50"></i>
                                </div>
                                <h6 class="text-secondary fw-bold">No orders found</h6>
                                <p class="text-muted small">Try adjusting your filters or create a new order.</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
@if($orders->hasPages())
<div class="card-footer bg-white border-top p-4">
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-center gap-3">
        
        <div class="text-muted small fw-medium">
            Showing <span class="fw-bold text-dark">{{ $orders->firstItem() }}</span> 
            to <span class="fw-bold text-dark">{{ $orders->lastItem() }}</span> 
            of <span class="fw-bold text-dark">{{ $orders->total() }}</span> entries
        </div>

        <nav aria-label="Page navigation">
            <ul class="premium-pagination mb-0">
                
                {{-- Previous Page Link --}}
                @if ($orders->onFirstPage())
                    <li class="page-item disabled">
                        <span class="page-link icon-box"><i class="fas fa-chevron-left"></i></span>
                    </li>
                @else
                    <li class="page-item">
                        <a class="page-link icon-box" href="{{ $orders->previousPageUrl() }}" rel="prev">
                            <i class="fas fa-chevron-left"></i>
                        </a>
                    </li>
                @endif

                {{-- Page Numbers (Simplified Logic for Inline) --}}
                {{-- দেখাবে: প্রথম পেজ ... বর্তমানের আশেপাশে ২ পেজ ... শেষ পেজ --}}
                @php
                    $start = max($orders->currentPage() - 2, 1);
                    $end = min($start + 4, $orders->lastPage());
                    if($end - $start < 4) {
                        $start = max($end - 4, 1);
                    }
                @endphp

                @if($start > 1)
                    <li class="page-item">
                        <a class="page-link" href="{{ $orders->url(1) }}">1</a>
                    </li>
                    @if($start > 2)
                        <li class="page-item disabled"><span class="page-link dots">...</span></li>
                    @endif
                @endif

                @for ($i = $start; $i <= $end; $i++)
                    @if ($i == $orders->currentPage())
                        <li class="page-item active">
                            <span class="page-link">{{ $i }}</span>
                        </li>
                    @else
                        <li class="page-item">
                            <a class="page-link" href="{{ $orders->url($i) }}">{{ $i }}</a>
                        </li>
                    @endif
                @endfor

                @if($end < $orders->lastPage())
                    @if($end < $orders->lastPage() - 1)
                        <li class="page-item disabled"><span class="page-link dots">...</span></li>
                    @endif
                    <li class="page-item">
                        <a class="page-link" href="{{ $orders->url($orders->lastPage()) }}">{{ $orders->lastPage() }}</a>
                    </li>
                @endif

                {{-- Next Page Link --}}
                @if ($orders->hasMorePages())
                    <li class="page-item">
                        <a class="page-link icon-box" href="{{ $orders->nextPageUrl() }}" rel="next">
                            <i class="fas fa-chevron-right"></i>
                        </a>
                    </li>
                @else
                    <li class="page-item disabled">
                        <span class="page-link icon-box"><i class="fas fa-chevron-right"></i></span>
                    </li>
                @endif
            </ul>
        </nav>
    </div>
</div>

<style>
    /* Pagination Styles */
    .premium-pagination {
        display: flex;
        padding-left: 0;
        list-style: none;
        gap: 5px;
        align-items: center;
    }
    
    .premium-pagination .page-link {
        border: none;
        width: 36px;
        height: 36px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 10px; /* Rounded Box */
        font-weight: 700;
        color: #64748b;
        background: #f8fafc;
        transition: all 0.2s ease;
        text-decoration: none;
        font-size: 0.85rem;
        cursor: pointer;
    }

    .premium-pagination .page-link:hover {
        background: #eef2ff;
        color: #4f46e5;
        transform: translateY(-2px);
    }

    .premium-pagination .page-item.active .page-link {
        background: linear-gradient(135deg, #4f46e5 0%, #3730a3 100%);
        color: white;
        box-shadow: 0 4px 10px rgba(79, 70, 229, 0.3);
    }

    .premium-pagination .page-item.disabled .page-link {
        background: #fff;
        color: #cbd5e1;
        cursor: not-allowed;
    }
    
    .premium-pagination .dots { 
        background: transparent; 
        cursor: default; 
        letter-spacing: 2px;
    }
    .premium-pagination .dots:hover { 
        background: transparent; 
        transform: none; 
    }
    
    /* Responsive Adjustments */
    @media (max-width: 576px) {
        .card-footer > div {
            flex-direction: column;
            text-align: center;
        }
    }
</style>
@endif
    </div>
</div>
@endsection