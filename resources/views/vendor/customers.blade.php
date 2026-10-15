@extends('vendor.layouts.app')

@section('title', 'Customers')
@section('page-title', 'Customer List')

@push('styles')
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
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

    /* Filter Card */
    .filter-card {
        background: #fff;
        border: 1px solid var(--border);
        border-radius: 12px;
        padding: 20px;
        margin-bottom: 24px;
        box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
    }

    .form-control {
        border-radius: 8px;
        border: 1px solid var(--border);
        padding: 10px 15px;
        font-size: 0.9rem;
        background-color: #fff;
    }
    .form-control:focus {
        border-color: var(--primary);
        box-shadow: 0 0 0 3px rgba(79, 70, 229, 0.1);
    }

    /* Table Card */
    .table-card {
        background: #fff;
        border: 1px solid var(--border);
        border-radius: 12px;
        overflow: hidden;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
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

    /* Avatar */
    .avatar-initial {
        width: 42px;
        height: 42px;
        border-radius: 50%;
        background-color: #eef2ff;
        color: var(--primary);
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 700;
        font-size: 16px;
        border: 1px solid rgba(79, 70, 229, 0.1);
    }

    /* Soft Badges */
    .badge-soft {
        padding: 6px 12px;
        border-radius: 20px;
        font-weight: 600;
        font-size: 0.75rem;
        display: inline-flex;
        align-items: center;
    }
    .badge-soft-success { background-color: #d1fae5; color: #065f46; }
    .badge-soft-secondary { background-color: #f1f5f9; color: #475569; }

    /* Icons */
    .icon-box {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        color: var(--secondary);
        font-size: 0.85rem;
    }

</style>
@endpush

@section('content')
<div class="container-fluid px-0">

    <div class="d-flex flex-column flex-md-row justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold mb-1 text-dark">Customer List</h4>
            <p class="text-secondary small mb-0">Manage and view your store's customer details.</p>
        </div>
        <div class="mt-3 mt-md-0">
            <span class="badge bg-white text-dark border py-2 px-3 rounded-pill shadow-sm fw-medium">
                Total Customers: <strong>{{ $customers->total() }}</strong>
            </span>
        </div>
    </div>

    <div class="filter-card">
        <form action="" method="GET">
            <div class="row g-3">
                <div class="col-md-10">
                    <div class="position-relative">
                        <i class="fas fa-search position-absolute text-secondary" style="top: 13px; left: 15px;"></i>
                        <input type="text" name="keyword" class="form-control ps-5" placeholder="Search by name, phone, or email..." value="{{ request('keyword') }}">
                    </div>
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-primary w-100 fw-medium">
                        <i class="fas fa-search me-1"></i> Search
                    </button>
                </div>
            </div>
        </form>
    </div>

    <div class="table-card">
        <div class="table-responsive">
            <table class="custom-table">
                <thead>
                    <tr>
                        <th>Customer Profile</th>
                        <th>Contact Info</th>
                        <th>Orders</th>
                        <th>Total Spent</th>
                        <th>Last Activity</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($customers as $customer)
                    <tr>
                        <td>
                            <div class="d-flex align-items-center">
                                <div class="avatar-initial me-3">
                                    {{ strtoupper(substr($customer->name ?? 'G', 0, 1)) }}
                                </div>
                                <div>
                                    <h6 class="mb-0 fw-semibold text-dark">{{ $customer->name ?? 'Guest User' }}</h6>
                                    <small class="text-secondary">{{ $customer->email ?? 'No email provided' }}</small>
                                </div>
                            </div>
                        </td>

                        <td>
                            <div class="d-flex flex-column gap-1">
                                <div class="icon-box">
                                    <i class="fas fa-phone-alt fa-xs"></i>
                                    <span class="text-dark fw-medium">{{ $customer->phone ?? 'N/A' }}</span>
                                </div>
                                @if($customer->address)
                                <div class="icon-box">
                                    <i class="fas fa-map-marker-alt fa-xs"></i>
                                    <span class="text-secondary small" title="{{ $customer->address }}">{{ Str::limit($customer->address, 25) }}</span>
                                </div>
                                @endif
                            </div>
                        </td>

                        <td>
                            <span class="badge bg-light text-dark border px-3 py-2 rounded-pill">
                                {{ $customer->orders_count ?? 0 }} Orders
                            </span>
                        </td>

                        <td>
                            <h6 class="mb-0 fw-bold text-dark">৳{{ number_format($customer->total_spent ?? 0, 2) }}</h6>
                        </td>

                        <td>
                            @php
                                // Optimized logic moved inside view for compatibility
                                $lastOrder = \App\Models\Order::whereIn('id', function($query) use ($vendor) {
                                        $query->select('order_id')
                                              ->from('order_details')
                                              ->whereIn('product_id', function($q) use ($vendor) {
                                                  $q->select('id')->from('products')->where('vendor_id', $vendor->id);
                                              });
                                    })
                                    ->where('customer_id', $customer->id)
                                    ->latest()
                                    ->first();
                            @endphp

                            @if($lastOrder)
                                <div class="d-flex flex-column">
                                    <span class="fw-medium text-dark small">{{ $lastOrder->created_at->format('d M, Y') }}</span>
                                    <small class="text-muted" style="font-size: 11px;">{{ $lastOrder->created_at->format('h:i A') }}</small>
                                </div>
                            @else
                                <span class="text-muted small">Never</span>
                            @endif
                        </td>

                        <td>
                            @if($customer->status == 1)
                                <span class="badge-soft badge-soft-success">
                                    <span class="me-1" style="width: 6px; height: 6px; border-radius: 50%; background-color: currentColor;"></span> Active
                                </span>
                            @else
                                <span class="badge-soft badge-soft-secondary">Inactive</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center py-5">
                            <div class="d-flex flex-column align-items-center">
                                <div class="mb-3 p-3 bg-light rounded-circle">
                                    <i class="fas fa-users-slash fa-2x text-secondary opacity-50"></i>
                                </div>
                                <h6 class="text-secondary fw-bold">No Customers Found</h6>
                                <p class="text-muted small mb-0">It looks like you don't have any customers yet.</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

  @if($customers->hasPages())
<div class="d-flex flex-column flex-md-row justify-content-between align-items-center p-4 border-top bg-white rounded-bottom">
    
    <div class="text-muted small fw-medium mb-3 mb-md-0">
        Showing <span class="fw-bold text-dark">{{ $customers->firstItem() }}</span> 
        to <span class="fw-bold text-dark">{{ $customers->lastItem() }}</span> 
        of <span class="fw-bold text-dark">{{ $customers->total() }}</span> entries
    </div>

    <nav aria-label="Page navigation">
        <ul class="premium-pagination mb-0">
            
            {{-- Previous Page Link --}}
            @if ($customers->onFirstPage())
                <li class="page-item disabled">
                    <span class="page-link icon-box"><i class="fas fa-chevron-left"></i></span>
                </li>
            @else
                <li class="page-item">
                    <a class="page-link icon-box" href="{{ $customers->previousPageUrl() }}" rel="prev">
                        <i class="fas fa-chevron-left"></i>
                    </a>
                </li>
            @endif

            {{-- Page Numbers Logic --}}
            @php
                $start = max($customers->currentPage() - 2, 1);
                $end = min($start + 4, $customers->lastPage());
                if($end - $start < 4) {
                    $start = max($end - 4, 1);
                }
            @endphp

            @if($start > 1)
                <li class="page-item">
                    <a class="page-link" href="{{ $customers->url(1) }}">1</a>
                </li>
                @if($start > 2)
                    <li class="page-item disabled"><span class="page-link dots">...</span></li>
                @endif
            @endif

            @for ($i = $start; $i <= $end; $i++)
                @if ($i == $customers->currentPage())
                    <li class="page-item active">
                        <span class="page-link">{{ $i }}</span>
                    </li>
                @else
                    <li class="page-item">
                        <a class="page-link" href="{{ $customers->url($i) }}">{{ $i }}</a>
                    </li>
                @endif
            @endfor

            @if($end < $customers->lastPage())
                @if($end < $customers->lastPage() - 1)
                    <li class="page-item disabled"><span class="page-link dots">...</span></li>
                @endif
                <li class="page-item">
                    <a class="page-link" href="{{ $customers->url($customers->lastPage()) }}">{{ $customers->lastPage() }}</a>
                </li>
            @endif

            {{-- Next Page Link --}}
            @if ($customers->hasMorePages())
                <li class="page-item">
                    <a class="page-link icon-box" href="{{ $customers->nextPageUrl() }}" rel="next">
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

<style>
    /* Premium Pagination Styles */
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
        border-radius: 10px; /* Modern Rounded Corners */
        font-weight: 700;
        color: #64748b;
        background: #f8fafc;
        transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
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
    }
    .premium-pagination .dots:hover { 
        background: transparent; 
        transform: none; 
    }
</style>
@endif
    </div>

</div>
@endsection