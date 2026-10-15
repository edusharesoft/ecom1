@extends('vendor.layouts.app')

@section('title', 'Withdrawal Management')
@section('page-title', 'My Wallet')

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

    /* Stats Card */
    .stat-card {
        background: #fff;
        border-radius: 16px;
        padding: 24px;
        border: 1px solid var(--border);
        box-shadow: 0 1px 3px rgba(0,0,0,0.05);
        transition: transform 0.2s;
        height: 100%;
        display: flex;
        align-items: center;
        justify-content: space-between;
    }
    .stat-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
    }
    .stat-icon {
        width: 48px;
        height: 48px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 24px;
    }
    .icon-primary { background: #eef2ff; color: var(--primary); }
    .icon-success { background: #ecfdf5; color: var(--success); }
    .icon-warning { background: #fffbeb; color: var(--warning); }

    /* Forms */
    .form-card {
        background: #fff;
        border-radius: 16px;
        border: 1px solid var(--border);
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
        overflow: hidden;
    }
    .form-header {
        padding: 20px 24px;
        border-bottom: 1px solid var(--border);
        background: #fff;
    }
    .form-control, .form-select {
        border-radius: 8px;
        border: 1px solid var(--border);
        padding: 12px 15px;
        font-size: 0.95rem;
        background-color: #f8fafc;
    }
    .form-control:focus, .form-select:focus {
        background-color: #fff;
        border-color: var(--primary);
        box-shadow: 0 0 0 3px rgba(79, 70, 229, 0.1);
    }

    /* Table */
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
    .badge-soft-secondary { background-color: #f1f5f9; color: #475569; }

</style>
@endpush

@section('content')
<div class="container-fluid px-0">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold mb-1 text-dark">Withdrawal</h4>
            <p class="text-secondary small mb-0">Manage your earnings and payouts.</p>
        </div>
        <div class="d-none d-md-block">
            <span class="text-secondary small">Current Date:</span>
            <span class="fw-bold text-dark ms-1">{{ date('d M, Y') }}</span>
        </div>
    </div>

    <div class="row g-4 mb-4">
        <div class="col-md-4">
            <div class="stat-card">
                <div>
                    <p class="text-secondary text-uppercase small fw-bold mb-1">Available Balance</p>
                    <h3 class="fw-bold text-dark mb-0">৳{{ number_format($wallet->balance, 2) }}</h3>
                </div>
                <div class="stat-icon icon-primary">
                    <i class="fas fa-wallet"></i>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="stat-card">
                <div>
                    <p class="text-secondary text-uppercase small fw-bold mb-1">Total Earned</p>
                    <h3 class="fw-bold text-dark mb-0">৳{{ number_format($wallet->total_earned, 2) }}</h3>
                </div>
                <div class="stat-icon icon-success">
                    <i class="fas fa-chart-line"></i>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="stat-card">
                <div>
                    <p class="text-secondary text-uppercase small fw-bold mb-1">Total Withdrawn</p>
                    <h3 class="fw-bold text-dark mb-0">৳{{ number_format($wallet->total_withdrawn, 2) }}</h3>
                </div>
                <div class="stat-icon icon-warning">
                    <i class="fas fa-history"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-lg-4">
            <div class="form-card">
                <div class="form-header">
                    <h6 class="fw-bold m-0 text-dark"><i class="fas fa-paper-plane me-2 text-primary"></i>Request Payout</h6>
                </div>
                <div class="p-4">
                    <form action="{{ route('vendor.withdrawals.store') }}" method="POST">
                        @csrf
                        
                        <div class="mb-3">
                            <label class="form-label small fw-bold text-secondary">Withdrawal Amount <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border text-secondary fw-bold">৳</span>
                                <input type="number" step="0.01" min="0" name="amount" class="form-control" placeholder="0.00" required>
                            </div>
                            @error('amount') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label small fw-bold text-secondary">Payment Method <span class="text-danger">*</span></label>
                            <select name="payout_method" class="form-select" required>
                                <option value="" selected disabled>Select Method</option>
                                <option value="bkash">bKash</option>
                                <option value="nagad">Nagad</option>
                                <option value="bank">Bank Transfer</option>
                                <option value="manual">Manual / Cash</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label small fw-bold text-secondary">Account Name</label>
                            <input type="text" name="account_name" class="form-control" placeholder="e.g. Karim Uddin">
                        </div>

                        <div class="mb-3">
                            <label class="form-label small fw-bold text-secondary">Account / Phone Number</label>
                            <input type="text" name="account_number" class="form-control" placeholder="e.g. 017xxxxxxxx">
                        </div>

                        <div class="mb-4">
                            <label class="form-label small fw-bold text-secondary">Additional Note</label>
                            <textarea name="note" rows="2" class="form-control" placeholder="Any special instructions..."></textarea>
                        </div>

                        <button type="submit" class="btn btn-primary w-100 py-2 fw-bold shadow-sm">
                            Submit Request <i class="fas fa-arrow-right ms-2"></i>
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-lg-8">
            <div class="form-card h-100">
                <div class="form-header d-flex justify-content-between align-items-center">
                    <h6 class="fw-bold m-0 text-dark"><i class="fas fa-list-alt me-2 text-primary"></i>Withdrawal History</h6>
                    </div>
                
                <div class="table-responsive">
                    <table class="custom-table">
                        <thead>
                            <tr>
                                <th>Date Request</th>
                                <th>Method</th>
                                <th>Amount</th>
                                <th>Status</th>
                                <th>Note</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($withdrawals as $row)
                                <tr>
                                    <td>
                                        <div class="d-flex flex-column">
                                            <span class="fw-medium text-dark">{{ $row->created_at->format('d M, Y') }}</span>
                                            <small class="text-secondary">{{ $row->created_at->format('h:i A') }}</small>
                                        </div>
                                    </td>

                                    <td>
                                        <span class="badge bg-light text-dark border px-2 py-1">
                                            @if($row->payout_method == 'bkash') <img src="https://blogger.googleusercontent.com/img/b/R29vZ2xl/AVvXsEjAzc1VDWM3b5K39L6xmk3TtI8uXlR8-gXfVcedgMV2N6vG7exqwFQD5HcGkkiFC0J7V_h7y08jb_Zyto7IV-xs4fMEprQ9UDjqIvHyf4zF0corbkhIFAVURKj8nuudzDbZLO2hf8z66WDVFmax4YQW-p-66uxh7lDqMf7u9sY9DNET11KDKT1iZJ07JgBO/s320/bkash.png" height="15" class="me-1"> @endif
                                            {{ ucfirst($row->payout_method) }}
                                        </span>
                                        <div class="small text-muted mt-1">{{ $row->account_number ?? 'N/A' }}</div>
                                    </td>

                                    <td>
                                        <span class="fw-bold text-dark fs-6">৳{{ number_format($row->amount, 2) }}</span>
                                    </td>

                                    <td>
                                        @php
                                            $status = $row->status;
                                            $badgeClass = 'badge-soft-secondary';
                                            $icon = 'fa-question';

                                            if($status === 'approved') {
                                                $badgeClass = 'badge-soft-success'; $icon = 'fa-check';
                                            } elseif($status === 'rejected') {
                                                $badgeClass = 'badge-soft-danger'; $icon = 'fa-times';
                                            } elseif($status === 'pending') {
                                                $badgeClass = 'badge-soft-warning'; $icon = 'fa-clock';
                                            }
                                        @endphp
                                        <span class="badge-soft {{ $badgeClass }}">
                                            <i class="fas {{ $icon }} me-1"></i> {{ ucfirst($status) }}
                                        </span>
                                    </td>

                                    <td>
                                        <span class="text-secondary small d-inline-block text-truncate" style="max-width: 150px;">
                                            {{ $row->note ?? '-' }}
                                        </span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center py-5">
                                        <div class="d-flex flex-column align-items-center">
                                            <div class="mb-3 p-3 bg-light rounded-circle">
                                                <i class="fas fa-inbox fa-2x text-muted opacity-50"></i>
                                            </div>
                                            <h6 class="text-secondary fw-bold">No Records Found</h6>
                                            <p class="text-muted small mb-0">You haven't made any withdrawal requests yet.</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

               @if($withdrawals->hasPages())
<div class="d-flex flex-column flex-md-row justify-content-between align-items-center p-4 border-top bg-white rounded-bottom">
    
    <div class="text-muted small fw-medium mb-3 mb-md-0">
        Showing <span class="fw-bold text-dark">{{ $withdrawals->firstItem() }}</span> 
        to <span class="fw-bold text-dark">{{ $withdrawals->lastItem() }}</span> 
        of <span class="fw-bold text-dark">{{ $withdrawals->total() }}</span> entries
    </div>

    <nav aria-label="Page navigation">
        <ul class="premium-pagination mb-0">
            
            {{-- Previous Page Link --}}
            @if ($withdrawals->onFirstPage())
                <li class="page-item disabled">
                    <span class="page-link icon-box"><i class="fas fa-chevron-left"></i></span>
                </li>
            @else
                <li class="page-item">
                    <a class="page-link icon-box" href="{{ $withdrawals->previousPageUrl() }}" rel="prev">
                        <i class="fas fa-chevron-left"></i>
                    </a>
                </li>
            @endif

            {{-- Page Numbers Logic --}}
            @php
                $start = max($withdrawals->currentPage() - 2, 1);
                $end = min($start + 4, $withdrawals->lastPage());
                if($end - $start < 4) {
                    $start = max($end - 4, 1);
                }
            @endphp

            @if($start > 1)
                <li class="page-item">
                    <a class="page-link" href="{{ $withdrawals->url(1) }}">1</a>
                </li>
                @if($start > 2)
                    <li class="page-item disabled"><span class="page-link dots">...</span></li>
                @endif
            @endif

            @for ($i = $start; $i <= $end; $i++)
                @if ($i == $withdrawals->currentPage())
                    <li class="page-item active">
                        <span class="page-link">{{ $i }}</span>
                    </li>
                @else
                    <li class="page-item">
                        <a class="page-link" href="{{ $withdrawals->url($i) }}">{{ $i }}</a>
                    </li>
                @endif
            @endfor

            @if($end < $withdrawals->lastPage())
                @if($end < $withdrawals->lastPage() - 1)
                    <li class="page-item disabled"><span class="page-link dots">...</span></li>
                @endif
                <li class="page-item">
                    <a class="page-link" href="{{ $withdrawals->url($withdrawals->lastPage()) }}">{{ $withdrawals->lastPage() }}</a>
                </li>
            @endif

            {{-- Next Page Link --}}
            @if ($withdrawals->hasMorePages())
                <li class="page-item">
                    <a class="page-link icon-box" href="{{ $withdrawals->nextPageUrl() }}" rel="next">
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
    </div>

</div>
@endsection