@extends('backEnd.layouts.master')

@section('title', 'Expense Logs / Reports')

@section('content')
<div class="container-fluid">

    {{-- PAGE HEADER --}}
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h4 class="mb-1">
                <i data-feather="file-text" class="me-1"></i>
                Expense Logs / Reports
            </h4>
            <small class="text-muted">
                সম্পাদিত এবং মুছে ফেলা Expense-এর বিস্তারিত রিপোর্ট
            </small>
        </div>

        <div>
            <a href="{{ route('admin.expenses.index') }}" class="btn btn-sm btn-outline-secondary">
                <i data-feather="arrow-left" class="me-1"></i> Back to Expenses
            </a>
        </div>
    </div>

    {{-- FILTER FORM --}}
    <div class="card mb-3">
        <div class="card-body">
            <form method="GET" action="{{ route('admin.expenses.logs') }}" class="row g-3">
                <div class="col-md-3">
                    <label class="form-label">Action</label>
                    <select name="action" class="form-select">
                        <option value="">All Actions</option>
                        <option value="edit" {{ request('action') == 'edit' ? 'selected' : '' }}>Edit</option>
                        <option value="delete" {{ request('action') == 'delete' ? 'selected' : '' }}>Delete</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">From Date</label>
                    <input type="date" name="from_date" class="form-control" value="{{ request('from_date') }}">
                </div>
                <div class="col-md-3">
                    <label class="form-label">To Date</label>
                    <input type="date" name="to_date" class="form-control" value="{{ request('to_date') }}">
                </div>
                <div class="col-md-3 d-flex align-items-end gap-2">
                    <button type="submit" class="btn btn-primary">Filter</button>
                    <a href="{{ route('admin.expenses.logs') }}" class="btn btn-outline-secondary">Clear</a>
                </div>
            </form>
        </div>
    </div>

    {{-- SUMMARY CARDS --}}
    <div class="row mb-4">
        <div class="col-md-6">
            <div class="card bg-primary text-white">
                <div class="card-body">
                    <h5 class="mb-1" style="color:#fff !important;">Total Edits</h5>
                    <h3 class="mb-0" style="color:#fff !important;">{{ $total_edits }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card bg-danger text-white">
                <div class="card-body">
                    <h5 class="mb-1" style="color:#fff !important;">Total Deletes</h5>
                    <h3 class="mb-0" style="color:#fff !important;">{{ $total_deletes }}</h3>
                </div>
            </div>
        </div>
    </div>

    {{-- LOGS TABLE --}}
    <div class="card">
        <div class="card-header bg-light">
            <strong>Expense Logs / Reports</strong>
        </div>
        <div class="card-body table-responsive">
            <table class="table table-bordered table-striped align-middle">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Action</th>
                        <th>Expense ID</th>
                        <th>Old Values</th>
                        <th>New Values</th>
                        <th>Fund Balance Change</th>
                        <th>Description</th>
                        <th>Performed By</th>
                        <th>Date & Time</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($logs as $log)
                        <tr>
                            <td>{{ $log->id }}</td>
                            <td>
                                @if($log->action == 'edit')
                                    <span class="badge bg-warning">Edit</span>
                                @else
                                    <span class="badge bg-danger">Delete</span>
                                @endif
                            </td>
                            <td>
                                @if($log->expense)
                                    #{{ $log->expense_id }}
                                @else
                                    <span class="text-muted">#{{ $log->expense_id }} (Deleted)</span>
                                @endif
                            </td>
                            <td>
                                @if($log->old_title)
                                    <div><strong>Title:</strong> {{ $log->old_title }}</div>
                                    <div><strong>Amount:</strong> {{ number_format($log->old_amount, 2) }} ৳</div>
                                    <div><strong>Date:</strong> {{ $log->old_expense_date ? \Carbon\Carbon::parse($log->old_expense_date)->format('d M Y') : '-' }}</div>
                                    @if($log->old_category)
                                        <div><strong>Category:</strong> {{ $log->old_category }}</div>
                                    @endif
                                    @if($log->old_note)
                                        <div><strong>Note:</strong> {{ Str::limit($log->old_note, 30) }}</div>
                                    @endif
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td>
                                @if($log->new_title)
                                    <div><strong>Title:</strong> {{ $log->new_title }}</div>
                                    <div><strong>Amount:</strong> {{ number_format($log->new_amount, 2) }} ৳</div>
                                    <div><strong>Date:</strong> {{ $log->new_expense_date ? \Carbon\Carbon::parse($log->new_expense_date)->format('d M Y') : '-' }}</div>
                                    @if($log->new_category)
                                        <div><strong>Category:</strong> {{ $log->new_category }}</div>
                                    @endif
                                    @if($log->new_note)
                                        <div><strong>Note:</strong> {{ Str::limit($log->new_note, 30) }}</div>
                                    @endif
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td>
                                <div><strong>Before:</strong> {{ number_format($log->fund_balance_before, 2) }} ৳</div>
                                <div><strong>After:</strong> {{ number_format($log->fund_balance_after, 2) }} ৳</div>
                                @php
                                    $balance_diff = $log->fund_balance_after - $log->fund_balance_before;
                                @endphp
                                <div>
                                    <strong>Change:</strong> 
                                    <span class="{{ $balance_diff >= 0 ? 'text-success' : 'text-danger' }}">
                                        {{ $balance_diff >= 0 ? '+' : '' }}{{ number_format($balance_diff, 2) }} ৳
                                    </span>
                                </div>
                            </td>
                            <td>
                                <small>{{ $log->description }}</small>
                            </td>
                            <td>
                                @if($log->performedBy)
                                    {{ $log->performedBy->name }}
                                @else
                                    <span class="text-muted">User #{{ $log->performed_by }}</span>
                                @endif
                            </td>
                            <td>{{ $log->created_at->format('d M Y, h:i A') }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="text-center text-muted">
                                No expense logs found.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>

            {{-- Pagination --}}
            <div class="mt-3">
                {{ $logs->links('pagination::bootstrap-4') }}
            </div>
        </div>
    </div>

</div>
@endsection
