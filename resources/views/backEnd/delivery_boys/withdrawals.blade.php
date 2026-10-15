@extends('backEnd.layouts.master')
@section('title','Rider withdrawals')

@section('css')
<style>
    .card-modern {
        border: none;
        border-radius: 12px;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05);
        background: #fff;
    }
    .table-modern th {
        background-color: #fff;
        color: #64748b;
        font-size: 0.75rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.02em;
        padding: 1rem;
        border-bottom: 2px solid #f1f5f9;
        white-space: nowrap;
    }
    .table-modern td {
        vertical-align: middle;
        padding: 1rem;
        font-size: 0.875rem;
        color: #334155;
        border-bottom: 1px solid #f1f5f9;
    }
    .table-modern tr:hover td { background-color: #f8fafc; }
    .rider-avatar {
        width: 38px; height: 38px;
        background: linear-gradient(135deg, #fef3c7 0%, #fde68a 100%);
        color: #b45309;
        border-radius: 10px;
        display: flex; align-items: center; justify-content: center;
        font-weight: 700; font-size: 0.9rem;
        margin-right: 12px;
        flex-shrink: 0;
    }
    .badge-soft {
        padding: 5px 10px; border-radius: 6px; font-size: 0.75rem; font-weight: 600;
        display: inline-flex; align-items: center; gap: 5px;
    }
    .badge-pending { background: #fef9c3; color: #854d0e; }
    .badge-approved { background: #dcfce7; color: #166534; }
    .badge-rejected { background: #fee2e2; color: #991b1b; }
    .status-dot { width: 6px; height: 6px; border-radius: 50%; background: currentColor; }
    .amount-highlight {
        font-variant-numeric: tabular-nums;
        font-weight: 700;
        font-size: 1rem;
        color: #0f172a;
    }
    .method-pill {
        display: inline-block;
        padding: 0.25rem 0.6rem;
        background: #f1f5f9;
        border-radius: 6px;
        font-size: 0.8rem;
        font-weight: 600;
        color: #475569;
        text-transform: capitalize;
    }
    .payout-num { font-family: ui-monospace, monospace; font-size: 0.85rem; color: #334155; }
</style>
@endsection

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
        <div>
            <h4 class="mb-1 fw-bold text-dark d-flex align-items-center">
                <i data-feather="credit-card" class="text-primary me-2" style="width:26px;height:26px;"></i>
                Rider withdrawals
            </h4>
            <p class="text-muted small mb-0">Review payout requests from delivery riders.</p>
        </div>
        <a href="{{ route('admin.delivery-boys.index') }}" class="btn btn-outline-secondary rounded-pill px-3">
            <i data-feather="arrow-left" class="me-1" style="width:16px;height:16px;"></i> Back to riders
        </a>
    </div>

    <div class="card card-modern">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-modern mb-0">
                    <thead>
                        <tr>
                            <th width="6%">#</th>
                            <th>Rider</th>
                            <th>Amount</th>
                            <th>Method</th>
                            <th>Payout number</th>
                            <th>Status</th>
                            <th class="text-end" width="18%">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($rows as $w)
                            @php
                                $status = strtolower($w->status ?? '');
                            @endphp
                            <tr>
                                <td class="text-muted small">{{ $w->id }}</td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="rider-avatar">
                                            {{ strtoupper(substr($w->deliveryBoy->name ?? '?', 0, 1)) }}
                                        </div>
                                        <span class="fw-semibold text-dark">{{ $w->deliveryBoy->name ?? '—' }}</span>
                                    </div>
                                </td>
                                <td><span class="amount-highlight">৳{{ number_format($w->amount, 2) }}</span></td>
                                <td><span class="method-pill">{{ $w->payout_method }}</span></td>
                                <td class="payout-num">{{ $w->payout_number }}</td>
                                <td>
                                    @if($status === 'pending')
                                        <span class="badge-soft badge-pending"><span class="status-dot"></span> Pending</span>
                                    @elseif($status === 'approved')
                                        <span class="badge-soft badge-approved"><span class="status-dot"></span> Approved</span>
                                    @elseif($status === 'rejected')
                                        <span class="badge-soft badge-rejected"><span class="status-dot"></span> Rejected</span>
                                    @else
                                        <span class="badge bg-secondary">{{ $w->status }}</span>
                                    @endif
                                </td>
                                <td class="text-end">
                                    @if($status === 'pending')
                                        <div class="d-inline-flex flex-wrap gap-1 justify-content-end">
                                            <form action="{{ route('admin.delivery-boys.withdrawals.approve') }}" method="post" class="d-inline">
                                                @csrf
                                                <input type="hidden" name="id" value="{{ $w->id }}">
                                                <button type="submit" class="btn btn-sm btn-success rounded-pill px-3">Approve</button>
                                            </form>
                                            <form action="{{ route('admin.delivery-boys.withdrawals.reject') }}" method="post" class="d-inline">
                                                @csrf
                                                <input type="hidden" name="id" value="{{ $w->id }}">
                                                <button type="submit" class="btn btn-sm btn-outline-danger rounded-pill px-3">Reject</button>
                                            </form>
                                        </div>
                                    @else
                                        <span class="text-muted small">—</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-5 text-muted">
                                    <i data-feather="inbox" class="mb-2 opacity-50" style="width:40px;height:40px;"></i>
                                    <p class="mb-0">No withdrawal requests yet.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($rows->hasPages())
                <div class="card-body border-top">{{ $rows->links() }}</div>
            @endif
        </div>
    </div>
</div>
@endsection
