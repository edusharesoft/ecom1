@extends('backEnd.layouts.master')
@section('title', 'Vendor Withdrawals')

@section('css')
<style>
    /* --- Card & Table Styles --- */
    .card-modern {
        border: none;
        border-radius: 12px;
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.03);
        background: #fff;
    }
    
    .table-modern th {
        background-color: #fff;
        color: #64748b;
        font-size: 0.75rem;
        font-weight: 700;
        text-transform: uppercase;
        padding: 1rem;
        border-bottom: 2px solid #f1f5f9;
        white-space: nowrap;
    }
    .table-modern td {
        padding: 1rem;
        vertical-align: middle;
        font-size: 0.875rem;
        color: #334155;
        border-bottom: 1px solid #f1f5f9;
    }
    .table-modern tr:hover td { background-color: #f8fafc; }

    /* --- Status Badges --- */
    .badge-soft {
        padding: 5px 10px; border-radius: 6px; font-size: 0.75rem; font-weight: 600;
        display: inline-flex; align-items: center; gap: 5px;
    }
    .badge-approved { background: #dcfce7; color: #166534; }
    .badge-rejected { background: #fee2e2; color: #991b1b; }
    .badge-pending { background: #fef3c7; color: #92400e; }
    .status-dot { width: 6px; height: 6px; border-radius: 50%; background: currentColor; }

    /* --- Method Badges --- */
    .method-badge {
        font-size: 0.75rem; padding: 4px 8px; border-radius: 4px;
        background: #f1f5f9; color: #475569; border: 1px solid #e2e8f0;
        text-transform: capitalize;
    }

    /* --- Action Buttons --- */
    .btn-icon {
        width: 32px; height: 32px;
        display: inline-flex; align-items: center; justify-content: center;
        border-radius: 8px; transition: all 0.2s; border: none;
    }
    .btn-icon:hover { transform: translateY(-2px); }
    .btn-approve { background: #dcfce7; color: #166534; }
    .btn-reject { background: #fee2e2; color: #991b1b; }
</style>
@endsection

@section('content')
<div class="container-fluid py-4">

    {{-- PAGE HEADER --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="mb-1 fw-bold text-dark">
                <i data-feather="dollar-sign" class="text-primary me-2"></i> Withdrawals
            </h4>
            <p class="text-muted small mb-0">Manage vendor payout requests.</p>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success border-0 shadow-sm mb-4"><i data-feather="check-circle" class="me-2" style="width:16px;"></i> {{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger border-0 shadow-sm mb-4"><i data-feather="alert-circle" class="me-2" style="width:16px;"></i> {{ session('error') }}</div>
    @endif

    <div class="card card-modern">
        <div class="table-responsive">
            <table class="table table-modern mb-0">
                <thead>
                    <tr>
                        <th width="5%">#</th>
                        <th width="20%">Vendor Details</th>
                        <th width="15%">Amount</th>
                        <th width="10%">Method</th>
                        <th width="20%">Account Info</th>
                        <th width="15%">Request Date</th>
                        <th width="10%">Status</th>
                        <th width="5%" class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($data as $key => $row)
                        <tr>
                            <td class="text-muted">{{ $loop->iteration }}</td>
                            
                            {{-- Vendor --}}
                            <td>
                                <div class="d-flex flex-column">
                                    <span class="fw-bold text-dark">{{ $row->vendor->shop_name ?? 'Unknown Shop' }}</span>
                                    <span class="small text-muted">{{ $row->vendor->owner_name ?? '' }}</span>
                                </div>
                            </td>

                            {{-- Amount --}}
                            <td>
                                <span class="fw-bold text-dark fs-6">৳{{ number_format($row->amount, 2) }}</span>
                            </td>

                            {{-- Method --}}
                            <td>
                                <span class="method-badge">
                                    @if($row->payout_method == 'bank') <i class="fas fa-university me-1"></i>
                                    @elseif(in_array($row->payout_method, ['bkash', 'nagad', 'rocket'])) <i class="fas fa-mobile-alt me-1"></i>
                                    @else <i class="fas fa-money-bill me-1"></i> @endif
                                    {{ ucfirst($row->payout_method) }}
                                </span>
                            </td>

                            {{-- Account Info --}}
                            <td>
                                <div class="small">
                                    @if($row->account_name)
                                        <div class="fw-medium text-dark">{{ $row->account_name }}</div>
                                    @endif
                                    @if($row->account_number)
                                        <div class="text-muted font-monospace">{{ $row->account_number }}</div>
                                    @endif
                                    @if($row->note)
                                        <div class="text-muted fst-italic mt-1" style="font-size: 11px;">"{{ Str::limit($row->note, 20) }}"</div>
                                    @endif
                                </div>
                            </td>

                            {{-- Date --}}
                            <td class="text-muted small">
                                <div>{{ $row->created_at->format('d M, Y') }}</div>
                                <div>{{ $row->created_at->format('h:i A') }}</div>
                            </td>

                            {{-- Status --}}
                            <td>
                                @if($row->status === 'approved')
                                    <span class="badge-soft badge-approved"><span class="status-dot"></span> Approved</span>
                                    @if($row->processed_at)
                                        <div class="small text-muted mt-1" style="font-size: 10px;">{{ $row->processed_at->format('d M, Y') }}</div>
                                    @endif
                                @elseif($row->status === 'rejected')
                                    <span class="badge-soft badge-rejected"><span class="status-dot"></span> Rejected</span>
                                @else
                                    <span class="badge-soft badge-pending"><span class="status-dot"></span> Pending</span>
                                @endif
                            </td>

                            {{-- Actions --}}
                            <td class="text-end">
                                @if($row->status === 'pending')
                                    <div class="d-flex justify-content-end gap-1">
                                        <button type="button" class="btn-icon btn-approve" data-bs-toggle="modal" data-bs-target="#approveModal{{ $row->id }}" title="Approve">
                                            <i data-feather="check" style="width:14px;"></i>
                                        </button>
                                        <button type="button" class="btn-icon btn-reject" data-bs-toggle="modal" data-bs-target="#rejectModal{{ $row->id }}" title="Reject">
                                            <i data-feather="x" style="width:14px;"></i>
                                        </button>
                                    </div>
                                @else
                                    <div class="text-center text-muted small">-</div>
                                @endif

                                {{-- Approve Modal --}}
                                <div class="modal fade" id="approveModal{{ $row->id }}" tabindex="-1" aria-hidden="true">
                                    <div class="modal-dialog modal-dialog-centered">
                                        <div class="modal-content border-0 shadow-lg">
                                            <div class="modal-header border-bottom-0 pb-0">
                                                <h5 class="modal-title fw-bold text-success">Approve Withdrawal</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                            </div>
                                            <form action="{{ route('admin.vendor.withdrawals.approve', $row->id) }}" method="POST">
                                                @csrf
                                                <div class="modal-body text-start">
                                                    <div class="alert alert-soft-success border-0 mb-3">
                                                        <i data-feather="check-circle" class="me-1" style="width:14px;"></i>
                                                        Balance will be deducted from admin fund.
                                                    </div>
                                                    <div class="mb-3 p-3 bg-light rounded border">
                                                        <div class="d-flex justify-content-between mb-1">
                                                            <span class="text-muted small">Amount:</span>
                                                            <span class="fw-bold">৳{{ number_format($row->amount, 2) }}</span>
                                                        </div>
                                                        <div class="d-flex justify-content-between">
                                                            <span class="text-muted small">To:</span>
                                                            <span class="fw-bold">{{ $row->vendor->shop_name }}</span>
                                                        </div>
                                                    </div>
                                                    <div class="mb-3">
                                                        <label class="form-label small fw-bold text-muted">Admin Note (Optional)</label>
                                                        <textarea name="admin_note" class="form-control" rows="2" placeholder="Transaction ID or remarks..."></textarea>
                                                    </div>
                                                </div>
                                                <div class="modal-footer border-top-0 pt-0">
                                                    <button type="button" class="btn btn-light btn-sm" data-bs-dismiss="modal">Cancel</button>
                                                    <button type="submit" class="btn btn-success btn-sm px-4">Confirm Approve</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>

                                {{-- Reject Modal --}}
                                <div class="modal fade" id="rejectModal{{ $row->id }}" tabindex="-1" aria-hidden="true">
                                    <div class="modal-dialog modal-dialog-centered">
                                        <div class="modal-content border-0 shadow-lg">
                                            <div class="modal-header border-bottom-0 pb-0">
                                                <h5 class="modal-title fw-bold text-danger">Reject Withdrawal</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                            </div>
                                            <form action="{{ route('admin.vendor.withdrawals.reject', $row->id) }}" method="POST">
                                                @csrf
                                                <div class="modal-body text-start">
                                                    <div class="alert alert-soft-warning border-0 mb-3">
                                                        <i data-feather="alert-triangle" class="me-1" style="width:14px;"></i>
                                                        Amount will be refunded to vendor's wallet.
                                                    </div>
                                                    <div class="mb-3">
                                                        <label class="form-label small fw-bold text-muted">Rejection Reason <span class="text-danger">*</span></label>
                                                        <textarea name="admin_note" class="form-control" rows="3" required placeholder="e.g. Invalid bank details..."></textarea>
                                                    </div>
                                                </div>
                                                <div class="modal-footer border-top-0 pt-0">
                                                    <button type="button" class="btn btn-light btn-sm" data-bs-dismiss="modal">Cancel</button>
                                                    <button type="submit" class="btn btn-danger btn-sm px-4">Confirm Reject</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>

                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        <div class="p-4 border-top d-flex justify-content-between align-items-center bg-white rounded-bottom">
            <small class="text-muted">
                Showing <strong>{{ $data->firstItem() }}</strong> to <strong>{{ $data->lastItem() }}</strong> of <strong>{{ $data->total() }}</strong> requests
            </small>
            <div>
                {{ $data->links('pagination::bootstrap-4') }}
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
    {{-- FontAwesome for specific icons if needed --}}
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
@endpush