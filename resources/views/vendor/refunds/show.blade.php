@extends('vendor.layouts.app')

@section('title', 'Refund Details')
@section('page-title', 'Refund Details')

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

    /* Card Styling */
    .detail-card {
        background: #fff;
        border-radius: 12px;
        border: 1px solid var(--border);
        box-shadow: 0 1px 3px rgba(0,0,0,0.02);
        margin-bottom: 24px;
        overflow: hidden;
    }

    .card-header-custom {
        padding: 16px 20px;
        border-bottom: 1px solid var(--border);
        background: #fcfcfc;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .card-title-custom {
        font-size: 0.95rem;
        font-weight: 600;
        color: var(--dark);
        margin: 0;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .info-row {
        display: flex;
        justify-content: space-between;
        margin-bottom: 12px;
        font-size: 0.9rem;
    }
    .info-label { color: var(--secondary); }
    .info-value { font-weight: 500; color: var(--dark); text-align: right; }

    /* Product Table */
    .product-table th {
        font-size: 0.75rem;
        text-transform: uppercase;
        color: var(--secondary);
        background: var(--light);
        padding: 10px 15px;
    }
    .product-table td {
        padding: 12px 15px;
        font-size: 0.9rem;
        vertical-align: middle;
    }

    /* Status Banner */
    .status-banner {
        padding: 16px 20px;
        border-radius: 12px;
        margin-bottom: 24px;
        display: flex;
        align-items: center;
        gap: 15px;
        border: 1px solid transparent;
    }
    .status-pending { background: #fffbeb; border-color: #fcd34d; color: #92400e; }
    .status-approved { background: #eff6ff; border-color: #93c5fd; color: #1e40af; }
    .status-processed { background: #ecfdf5; border-color: #6ee7b7; color: #065f46; }
    .status-rejected { background: #fef2f2; border-color: #fca5a5; color: #991b1b; }

</style>
@endpush

@section('content')
<div class="container-fluid px-0">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold mb-1 text-dark">Refund Request #{{ $refund->refund_id }}</h4>
            <p class="text-secondary small mb-0">Requested on {{ $refund->created_at->format('d M, Y h:i A') }}</p>
        </div>
        <a href="{{ route('vendor.refunds.index') }}" class="btn btn-white border shadow-sm fw-medium">
            <i class="fas fa-arrow-left me-2"></i>Back to List
        </a>
    </div>

    @if($refund->status == 'pending')
        <div class="status-banner status-pending">
            <i class="fas fa-clock fa-lg"></i>
            <div>
                <h6 class="fw-bold mb-0">Request Pending</h6>
                <small>This refund request is currently under review.</small>
            </div>
        </div>
    @elseif($refund->status == 'approved')
        <div class="status-banner status-approved">
            <i class="fas fa-check-circle fa-lg"></i>
            <div>
                <h6 class="fw-bold mb-0">Request Approved</h6>
                <small>The refund has been approved and is processing.</small>
            </div>
        </div>
    @elseif($refund->status == 'processed')
        <div class="status-banner status-processed">
            <i class="fas fa-check-double fa-lg"></i>
            <div>
                <h6 class="fw-bold mb-0">Refund Completed</h6>
                <small>The refund amount has been successfully processed.</small>
            </div>
        </div>
    @elseif($refund->status == 'rejected')
        <div class="status-banner status-rejected">
            <i class="fas fa-times-circle fa-lg"></i>
            <div>
                <h6 class="fw-bold mb-0">Request Rejected</h6>
                <small>This refund request was rejected by admin.</small>
            </div>
        </div>
    @endif

    <div class="row g-4">
        <div class="col-lg-8">
            
            <div class="detail-card">
                <div class="card-header-custom">
                    <h6 class="card-title-custom"><i class="fas fa-undo text-primary"></i> Refund Information</h6>
                </div>
                <div class="p-4">
                    <div class="row g-4">
                        <div class="col-md-6 border-end">
                            <div class="info-row">
                                <span class="info-label">Refund Amount</span>
                                <span class="info-value text-dark fw-bold">৳{{ number_format($refund->amount, 2) }}</span>
                            </div>
                            <div class="info-row">
                                <span class="info-label">Shipping Refund</span>
                                <span class="info-value">৳{{ number_format($refund->shipping_charge, 2) }}</span>
                            </div>
                            <div class="info-row pt-2 border-top">
                                <span class="info-label fw-bold text-dark">Total Refund</span>
                                <span class="info-value text-primary fs-5 fw-bold">৳{{ number_format($refund->amount + $refund->shipping_charge, 2) }}</span>
                            </div>
                        </div>
                        <div class="col-md-6 ps-md-4">
                            <div class="info-row">
                                <span class="info-label">Refund Method</span>
                                <span class="badge bg-light text-dark border">
                                    {{ ucfirst(str_replace('_', ' ', $refund->refund_method ?? 'Unknown')) }}
                                </span>
                            </div>
                            @if($refund->refund_account)
                            <div class="info-row">
                                <span class="info-label">Account No</span>
                                <span class="info-value">{{ $refund->refund_account }}</span>
                            </div>
                            @endif
                            @if($refund->transaction_id)
                            <div class="info-row">
                                <span class="info-label">Transaction ID</span>
                                <span class="info-value font-monospace bg-light px-2 py-1 rounded small">{{ $refund->transaction_id }}</span>
                            </div>
                            @endif
                        </div>
                    </div>

                    <div class="mt-4 pt-3 border-top">
                        <h6 class="fw-bold text-dark small mb-2">Customer Reason</h6>
                        <div class="p-3 bg-light rounded border text-secondary small">
                            {{ $refund->reason ?? 'No reason provided.' }}
                        </div>
                    </div>
                </div>
            </div>

            <div class="detail-card">
                <div class="card-header-custom">
                    <h6 class="card-title-custom"><i class="fas fa-shopping-bag text-info"></i> Order Details</h6>
                    <a href="#" class="text-primary small text-decoration-none">View Order <i class="fas fa-external-link-alt ms-1"></i></a>
                </div>
                <div class="p-0">
                    <div class="p-3 bg-light border-bottom d-flex justify-content-between align-items-center">
                        <div>
                            <span class="text-secondary small">Order Invoice:</span>
                            <span class="fw-bold text-dark ms-1">#{{ $refund->order->invoice_id }}</span>
                        </div>
                        <div>
                            <span class="text-secondary small">Order Date:</span>
                            <span class="fw-bold text-dark ms-1">{{ $refund->order->created_at->format('d M, Y') }}</span>
                        </div>
                    </div>
                    
                    <div class="table-responsive">
                        <table class="table product-table mb-0">
                            <thead>
                                <tr>
                                    <th>Product</th>
                                    <th class="text-center">Qty</th>
                                    <th class="text-end">Unit Price</th>
                                    <th class="text-end">Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($refund->order->orderdetails as $item)
                                <tr>
                                    <td>
                                        <div class="fw-medium text-dark">{{ $item->product_name }}</div>
                                        @if($item->color || $item->size)
                                            <div class="small text-muted">{{ $item->color ?? '' }} {{ $item->size ? '| '.$item->size : '' }}</div>
                                        @endif
                                    </td>
                                    <td class="text-center">{{ $item->qty }}</td>
                                    <td class="text-end">৳{{ number_format($item->sale_price, 2) }}</td>
                                    <td class="text-end fw-bold">৳{{ number_format($item->sale_price * $item->qty, 2) }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

        </div>

        <div class="col-lg-4">
            
            <div class="detail-card">
                <div class="card-header-custom">
                    <h6 class="card-title-custom"><i class="fas fa-user-circle text-secondary"></i> Customer Profile</h6>
                </div>
                <div class="p-4 text-center border-bottom">
                    <div class="mx-auto bg-light rounded-circle d-flex align-items-center justify-content-center mb-3" style="width: 60px; height: 60px;">
                        <span class="fs-4 fw-bold text-primary">{{ strtoupper(substr($refund->customer->name ?? 'G', 0, 1)) }}</span>
                    </div>
                    <h6 class="fw-bold text-dark mb-1">{{ $refund->customer->name ?? 'Guest User' }}</h6>
                    <span class="badge bg-success bg-opacity-10 text-success">Verified Customer</span>
                </div>
                <div class="p-4">
                    <div class="info-row">
                        <span class="info-label"><i class="fas fa-phone me-2"></i>Phone</span>
                        <span class="info-value">{{ $refund->customer->phone ?? 'N/A' }}</span>
                    </div>
                    <div class="info-row">
                        <span class="info-label"><i class="fas fa-envelope me-2"></i>Email</span>
                        <span class="info-value">{{ $refund->customer->email ?? 'N/A' }}</span>
                    </div>
                    <div class="info-row">
                        <span class="info-label"><i class="fas fa-map-marker-alt me-2"></i>Location</span>
                        <span class="info-value">{{ $refund->customer->district ?? 'N/A' }}</span>
                    </div>
                </div>
            </div>

            @if($refund->admin_note)
            <div class="detail-card bg-warning bg-opacity-10 border-warning">
                <div class="p-3">
                    <h6 class="fw-bold text-warning mb-2"><i class="fas fa-sticky-note me-2"></i>Admin Note</h6>
                    <p class="small text-dark mb-0">{{ $refund->admin_note }}</p>
                </div>
            </div>
            @endif

        </div>
    </div>

</div>
@endsection