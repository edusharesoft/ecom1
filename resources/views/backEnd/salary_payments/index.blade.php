@extends('backEnd.layouts.master')
@section('title', 'Salary Payments')

@section('css')
<style>
    /* --- Card & Filter Styles --- */
    .card-modern {
        border: none;
        border-radius: 12px;
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.03);
        background: #fff;
    }
    .filter-container {
        background: #f8fafc;
        border-bottom: 1px solid #e2e8f0;
        padding: 1.25rem;
        border-radius: 12px 12px 0 0;
    }
    
    /* --- Form Elements --- */
    .form-control-modern, .form-select-modern {
        border: 1px solid #e2e8f0;
        border-radius: 8px;
        padding: 0.6rem 1rem;
        font-size: 0.875rem;
        background-color: #fff;
    }
    .form-control-modern:focus, .form-select-modern:focus {
        border-color: #6366f1;
        box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.1);
    }

    /* --- Table Styles --- */
    .table-modern th {
        background-color: #fff;
        color: #64748b;
        font-size: 0.75rem;
        font-weight: 700;
        text-transform: uppercase;
        padding: 1rem;
        border-bottom: 2px solid #f1f5f9;
    }
    .table-modern td {
        padding: 1rem;
        vertical-align: middle;
        font-size: 0.875rem;
        color: #334155;
        border-bottom: 1px solid #f1f5f9;
    }
    .table-modern tr:last-child td { border-bottom: none; }
    .table-modern tr:hover td { background-color: #f8fafc; }

    /* --- Badges --- */
    .badge-soft {
        padding: 5px 10px;
        border-radius: 6px;
        font-size: 0.75rem;
        font-weight: 600;
        display: inline-flex;
        align-items: center;
        gap: 5px;
    }
    .badge-paid { background: #dcfce7; color: #166534; }
    .badge-failed { background: #fee2e2; color: #991b1b; }
    .badge-pending { background: #fef3c7; color: #92400e; }
    .status-dot { width: 6px; height: 6px; border-radius: 50%; background: currentColor; }

    /* --- Action Buttons --- */
    .btn-icon {
        width: 32px; height: 32px;
        display: inline-flex; align-items: center; justify-content: center;
        border-radius: 8px; transition: all 0.2s; border: none;
    }
    .btn-icon:hover { transform: translateY(-2px); }
    .btn-view { background: #e0f2fe; color: #0284c7; }
</style>
@endsection

@section('content')
<div class="container-fluid py-4">

    {{-- PAGE HEADER --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="mb-1 fw-bold text-dark">
                <i data-feather="credit-card" class="text-primary me-2"></i> Salary Payments
            </h4>
            <p class="text-muted small mb-0">History of all salary transactions.</p>
        </div>
        <a href="{{ route('admin.salary_payments.create') }}" class="btn btn-success px-4 py-2 rounded-pill shadow-sm">
            <i data-feather="plus" class="me-1" style="width: 16px;"></i> Make Payment
        </a>
    </div>

    <div class="card card-modern">
        
        {{-- FILTERS --}}
        <div class="filter-container">
            <form method="GET" action="{{ route('admin.salary_payments.index') }}">
                <div class="row g-3 align-items-end">
                    <div class="col-md-3">
                        <label class="form-label small fw-bold text-muted text-uppercase mb-1">Employee</label>
                        <select name="employee_id" class="form-control select2 form-select-modern">
                            <option value="">All Employees</option>
                            @foreach($employees as $emp)
                                <option value="{{ $emp->id }}" {{ request('employee_id') == $emp->id ? 'selected' : '' }}>
                                    {{ $emp->name }} ({{ $emp->employee_id }})
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label small fw-bold text-muted text-uppercase mb-1">Month</label>
                        <input type="month" name="month" class="form-control form-control-modern" value="{{ request('month') }}">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label small fw-bold text-muted text-uppercase mb-1">Status</label>
                        <select name="status" class="form-select form-select-modern">
                            <option value="">All Status</option>
                            <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                            <option value="paid" {{ request('status') == 'paid' ? 'selected' : '' }}>Paid</option>
                            <option value="failed" {{ request('status') == 'failed' ? 'selected' : '' }}>Failed</option>
                        </select>
                    </div>
                    <div class="col-md-3 d-flex gap-2">
                        <button type="submit" class="btn btn-dark w-100 fw-bold">Filter</button>
                        <a href="{{ route('admin.salary_payments.index') }}" class="btn btn-light border px-3" title="Reset">
                            <i data-feather="refresh-cw" style="width:16px;"></i>
                        </a>
                    </div>
                </div>
            </form>
        </div>

        {{-- TABLE --}}
        <div class="table-responsive">
            <table class="table table-modern mb-0">
                <thead>
                    <tr>
                        <th width="5%">#</th>
                        <th width="15%">Txn ID</th>
                        <th width="20%">Employee Details</th>
                        <th width="15%">Month</th>
                        <th width="15%">Amount</th>
                        <th width="15%">Method</th>
                        <th width="10%">Status</th>
                        <th width="5%" class="text-end">View</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($payments as $payment)
                        <tr>
                            <td class="text-muted">{{ $loop->iteration + ($payments->currentPage()-1)*$payments->perPage() }}</td>
                            <td>
                                <span class="font-monospace text-dark small bg-light px-2 py-1 rounded border">
                                    {{ $payment->payment_id }}
                                </span>
                            </td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="bg-light text-primary fw-bold rounded-circle d-flex align-items-center justify-content-center me-2" 
                                         style="width: 32px; height: 32px; font-size: 12px; border: 1px solid #e0e7ff;">
                                        {{ substr($payment->employee->name, 0, 1) }}
                                    </div>
                                    <div>
                                        <div class="fw-bold text-dark">{{ $payment->employee->name }}</div>
                                        <div class="small text-muted">{{ $payment->employee->employee_id }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="text-muted">{{ $payment->payment_month }}</td>
                            <td class="fw-bold text-dark fs-6">৳{{ number_format($payment->amount, 2) }}</td>
                            <td>
                                <div class="d-flex align-items-center text-muted small">
                                    @if($payment->payment_method == 'bank') <i data-feather="briefcase" class="me-1" style="width:12px;"></i>
                                    @elseif($payment->payment_method == 'cash') <i data-feather="dollar-sign" class="me-1" style="width:12px;"></i>
                                    @else <i data-feather="smartphone" class="me-1" style="width:12px;"></i> @endif
                                    
                                    {{ ucfirst(str_replace('_', ' ', $payment->payment_method)) }}
                                </div>
                                <div class="small text-muted" style="font-size: 10px;">{{ $payment->payment_date->format('d M, Y') }}</div>
                            </td>
                            <td>
                                @if($payment->status == 'paid')
                                    <span class="badge-soft badge-paid"><span class="status-dot"></span> Paid</span>
                                @elseif($payment->status == 'failed')
                                    <span class="badge-soft badge-failed"><span class="status-dot"></span> Failed</span>
                                @else
                                    <span class="badge-soft badge-pending"><span class="status-dot"></span> Pending</span>
                                @endif
                            </td>
                            <td class="text-end">
                                <a href="{{ route('admin.salary_payments.show', $payment->id) }}" class="btn-icon btn-view" title="View Details">
                                    <i data-feather="eye" style="width:14px;"></i>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center py-5">
                                <img src="https://cdn-icons-png.flaticon.com/512/7486/7486744.png" width="60" class="mb-3 opacity-25">
                                <p class="text-muted fw-bold mb-0">No payment history found</p>
                                <small class="text-muted">Adjust filters or create a new payment.</small>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- PAGINATION --}}
        <div class="p-4 border-top d-flex justify-content-between align-items-center bg-white rounded-bottom">
            <small class="text-muted">
                Showing <strong>{{ $payments->firstItem() }}</strong> to <strong>{{ $payments->lastItem() }}</strong> of <strong>{{ $payments->total() }}</strong> transactions
            </small>
            <div>
                {{ $payments->links('pagination::bootstrap-4') }}
            </div>
        </div>

    </div>
</div>
@endsection

@push('scripts')
<script>
    $(document).ready(function() {
        if ($.fn.select2) {
            $('.select2').select2({ width: '100%' });
        }
    });
</script>
@endpush