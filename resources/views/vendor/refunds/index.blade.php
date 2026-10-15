@extends('vendor.layouts.app')

@section('title', 'Refund Requests')
@section('page-title', 'Refund Management')

@push('styles')
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
<style>
    :root {
        --primary: #4f46e5;
        --secondary: #64748b;
        --success: #10b981;
        --warning: #f59e0b;
        --danger: #ef4444;
        --info: #0ea5e9;
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

    .form-control, .form-select {
        border-radius: 8px;
        border: 1px solid var(--border);
        padding: 10px 15px;
        font-size: 0.95rem;
        background-color: #fff;
    }
    .form-control:focus, .form-select:focus {
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

    .custom-table td {
        padding: 16px 24px;
        vertical-align: middle;
        border-bottom: 1px solid var(--border);
        font-size: 0.9rem;
        color: var(--dark);
    }
    .custom-table tbody tr:last-child td { border-bottom: none; }
    .custom-table tbody tr:hover { background-color: #f8fafc; }

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
    .badge-soft-warning { background-color: #fef3c7; color: #92400e; }
    .badge-soft-danger  { background-color: #fee2e2; color: #991b1b; }
    .badge-soft-info    { background-color: #dbeafe; color: #1e40af; }

    /* Action Button */
    .btn-icon {
        width: 32px;
        height: 32px;
        border-radius: 8px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        color: var(--secondary);
        border: 1px solid var(--border);
        background: #fff;
        transition: 0.2s;
    }
    .btn-icon:hover {
        background-color: var(--primary);
        color: #fff;
        border-color: var(--primary);
    }

    .order-link {
        color: var(--primary);
        font-weight: 600;
        text-decoration: none;
    }
    .order-link:hover { text-decoration: underline; }

</style>
@endpush

@section('content')
<div class="container-fluid px-0">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold mb-1 text-dark">Refund Requests</h4>
            <p class="text-secondary small mb-0">Manage customer refund requests efficiently.</p>
        </div>
        <div>
            <span class="badge bg-white text-secondary border py-2 px-3 rounded-pill shadow-sm">
                Total Requests: <strong>{{ $refunds->total() }}</strong>
            </span>
        </div>
    </div>

    <div class="filter-card">
        <form method="GET" action="{{ route('vendor.refunds.index') }}">
            <div class="row g-3 align-items-end">
                <div class="col-md-4">
                    <label class="form-label small fw-bold text-secondary">Search Order</label>
                    <div class="position-relative">
                        <i class="fas fa-search position-absolute text-secondary" style="top: 12px; left: 15px;"></i>
                        <input type="text" name="order_invoice" class="form-control ps-5" placeholder="Order Invoice ID..." value="{{ request('order_invoice') }}">
                    </div>
                </div>
                <div class="col-md-3">
                    <label class="form-label small fw-bold text-secondary">Status</label>
                    <select name="status" class="form-select">
                        <option value="">All Status</option>
                        @foreach($statuses as $status)
                            <option value="{{ $status }}" {{ request('status') == $status ? 'selected' : '' }}>
                                {{ ucfirst($status) }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3 d-flex gap-2">
                    <button type="submit" class="btn btn-primary w-100 fw-medium">
                        <i class="fas fa-filter me-1"></i> Filter
                    </button>
                    <a href="{{ route('vendor.refunds.index') }}" class="btn btn-light border w-100 fw-medium text-secondary">
                        Reset
                    </a>
                </div>
            </div>
        </form>
    </div>

    <div class="table-card">
        <div class="table-responsive">
            <table class="custom-table">
                <thead>
                    <tr>
                        <th>Refund ID</th>
                        <th>Order Invoice</th>
                        <th>Customer Info</th>
                        <th>Refund Amount</th>
                        <th>Status</th>
                        <th>Request Date</th>
                        <th class="text-end">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($refunds as $refund)
                        <tr>
                            <td>
                                <span class="fw-bold text-dark">#{{ $refund->refund_id }}</span>
                            </td>

                            <td>
                                <a href="#" class="order-link">
                                    #{{ $refund->order->invoice_id }}
                                </a>
                            </td>

                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="bg-light rounded-circle p-2 me-2 text-primary">
                                        <i class="fas fa-user"></i>
                                    </div>
                                    <div>
                                        <h6 class="mb-0 fw-semibold text-dark small">{{ $refund->customer->name ?? 'Guest' }}</h6>
                                        <small class="text-secondary" style="font-size: 11px;">{{ $refund->customer->phone ?? 'N/A' }}</small>
                                    </div>
                                </div>
                            </td>

                            <td>
                                <h6 class="mb-0 fw-bold text-dark">৳{{ number_format($refund->amount, 2) }}</h6>
                                @if($refund->shipping_charge > 0)
                                    <small class="text-secondary" style="font-size: 11px;">
                                        + Shipping: ৳{{ number_format($refund->shipping_charge, 2) }}
                                    </small>
                                @endif
                            </td>

                            <td>
                                @php
                                    $status = $refund->status;
                                    $badgeClass = 'badge-soft-secondary';
                                    $icon = 'fa-circle';

                                    if($status == 'pending') {
                                        $badgeClass = 'badge-soft-warning'; $icon = 'fa-clock';
                                    } elseif($status == 'approved') {
                                        $badgeClass = 'badge-soft-info'; $icon = 'fa-thumbs-up';
                                    } elseif($status == 'rejected') {
                                        $badgeClass = 'badge-soft-danger'; $icon = 'fa-times-circle';
                                    } elseif($status == 'processed') {
                                        $badgeClass = 'badge-soft-success'; $icon = 'fa-check-circle';
                                    }
                                @endphp
                                <span class="badge-soft {{ $badgeClass }}">
                                    <i class="fas {{ $icon }} me-1"></i> {{ ucfirst($status) }}
                                </span>
                            </td>

                            <td>
                                <span class="d-block text-dark small fw-medium">{{ $refund->created_at->format('d M, Y') }}</span>
                                <small class="text-muted" style="font-size: 11px;">{{ $refund->created_at->format('h:i A') }}</small>
                            </td>

                            <td class="text-end">
                                <a href="{{ route('vendor.refunds.show', $refund->id) }}" class="btn-icon" title="View Details">
                                    <i class="fas fa-eye"></i>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-5">
                                <div class="d-flex flex-column align-items-center">
                                    <div class="mb-3 p-3 bg-light rounded-circle">
                                        <i class="fas fa-undo-alt fa-2x text-muted opacity-50"></i>
                                    </div>
                                    <h6 class="text-secondary fw-bold">No Refund Requests</h6>
                                    <p class="text-muted small mb-0">You don't have any refund requests at the moment.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

@if($refunds->hasPages())
<div class="d-flex flex-column flex-md-row justify-content-between align-items-center p-4 border-top bg-white rounded-bottom">
    
    <div class="text-muted small fw-medium mb-3 mb-md-0">
        Showing <span class="fw-bold text-dark">{{ $refunds->firstItem() }}</span> 
        to <span class="fw-bold text-dark">{{ $refunds->lastItem() }}</span> 
        of <span class="fw-bold text-dark">{{ $refunds->total() }}</span> results
    </div>

    <nav aria-label="Page navigation">
        <ul class="premium-pagination mb-0">
            
            {{-- Previous Page Link --}}
            @if ($refunds->onFirstPage())
                <li class="page-item disabled">
                    <span class="page-link icon-box"><i class="fas fa-chevron-left"></i></span>
                </li>
            @else
                <li class="page-item">
                    <a class="page-link icon-box" href="{{ $refunds->previousPageUrl() }}" rel="prev">
                        <i class="fas fa-chevron-left"></i>
                    </a>
                </li>
            @endif

            {{-- Page Numbers Logic --}}
            @php
                $start = max($refunds->currentPage() - 2, 1);
                $end = min($start + 4, $refunds->lastPage());
                if($end - $start < 4) {
                    $start = max($end - 4, 1);
                }
            @endphp

            @if($start > 1)
                <li class="page-item">
                    <a class="page-link" href="{{ $refunds->url(1) }}">1</a>
                </li>
                @if($start > 2)
                    <li class="page-item disabled"><span class="page-link dots">...</span></li>
                @endif
            @endif

            @for ($i = $start; $i <= $end; $i++)
                @if ($i == $refunds->currentPage())
                    <li class="page-item active">
                        <span class="page-link">{{ $i }}</span>
                    </li>
                @else
                    <li class="page-item">
                        <a class="page-link" href="{{ $refunds->url($i) }}">{{ $i }}</a>
                    </li>
                @endif
            @endfor

            @if($end < $refunds->lastPage())
                @if($end < $refunds->lastPage() - 1)
                    <li class="page-item disabled"><span class="page-link dots">...</span></li>
                @endif
                <li class="page-item">
                    <a class="page-link" href="{{ $refunds->url($refunds->lastPage()) }}">{{ $refunds->lastPage() }}</a>
                </li>
            @endif

            {{-- Next Page Link --}}
            @if ($refunds->hasMorePages())
                <li class="page-item">
                    <a class="page-link icon-box" href="{{ $refunds->nextPageUrl() }}" rel="next">
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
    /* Premium Pagination CSS */
    .premium-pagination {
        display: flex;
        padding-left: 0;
        list-style: none;
        gap: 5px;
        align-items: center;
    }
    
    .premium-pagination .page-link {
        border: none;
        width: 34px;
        height: 34px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 8px;
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