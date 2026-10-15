@extends('backEnd.layouts.master')
@section('title','Delivery persons')

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
        background: linear-gradient(135deg, #e0e7ff 0%, #dbeafe 100%);
        color: #4338ca;
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
    .badge-active { background: #dcfce7; color: #166534; }
    .badge-inactive { background: #f1f5f9; color: #64748b; }
    .status-dot { width: 6px; height: 6px; border-radius: 50%; background: currentColor; }
    .amount-cell { font-variant-numeric: tabular-nums; font-weight: 600; color: #0f172a; }
</style>
@endsection

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
        <div>
            <h4 class="mb-1 fw-bold text-dark d-flex align-items-center">
                <i data-feather="truck" class="text-primary me-2" style="width:26px;height:26px;"></i>
                Delivery persons
            </h4>
            <p class="text-muted small mb-0">Riders, commission, wallet balance, and quick actions.</p>
        </div>
        <div class="d-flex flex-wrap gap-2">
            <a href="{{ route('admin.delivery-boys.withdrawals') }}" class="btn btn-outline-secondary rounded-pill px-3">
                <i data-feather="dollar-sign" class="me-1" style="width:16px;height:16px;"></i> Withdrawals
            </a>
            <a href="{{ route('admin.delivery-boys.create') }}" class="btn btn-primary rounded-pill px-4 shadow-sm">
                <i data-feather="plus" class="me-1" style="width:16px;height:16px;"></i> Add rider
            </a>
        </div>
    </div>

    <div class="card card-modern">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-modern mb-0">
                    <thead>
                        <tr>
                            <th>Rider</th>
                            <th>Phone</th>
                            <th>Commission / delivery</th>
                            <th>Wallet</th>
                            <th>Status</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($rows as $r)
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="rider-avatar">{{ strtoupper(substr($r->name, 0, 1)) }}</div>
                                        <span class="fw-semibold text-dark">{{ $r->name }}</span>
                                    </div>
                                </td>
                                <td class="font-monospace text-muted">{{ $r->phone }}</td>
                                <td class="amount-cell">৳{{ number_format($r->commission_per_delivery, 2) }}</td>
                                <td class="amount-cell">৳{{ number_format($r->wallet_balance, 2) }}</td>
                                <td>
                                    @if($r->status)
                                        <span class="badge-soft badge-active"><span class="status-dot"></span> Active</span>
                                    @else
                                        <span class="badge-soft badge-inactive"><span class="status-dot"></span> Inactive</span>
                                    @endif
                                </td>
                                <td class="text-end">
                                    <a href="{{ route('admin.delivery-boys.wallet', $r->id) }}" class="btn btn-sm btn-soft-info me-1">Wallet</a>
                                    <a href="{{ route('admin.delivery-boys.edit', $r->id) }}" class="btn btn-sm btn-primary">Edit</a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-5 text-muted">
                                    <i data-feather="inbox" class="mb-2 opacity-50" style="width:40px;height:40px;"></i>
                                    <p class="mb-0">No delivery persons yet. Add your first rider to get started.</p>
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
