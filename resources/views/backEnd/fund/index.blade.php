@extends('backEnd.layouts.master')

@section('title', 'Fund Management')

@section('content')
<div class="container-fluid">

    <div class="d-flex justify-content-between align-items-center mb-3">
        <h3 class="mb-0">💰 Fund Management</h3>
        <a href="{{ route('admin.fund.logs') }}" class="btn btn-info btn-sm">
            <i data-feather="file-text" class="me-1" style="width:14px;height:14px;"></i> 
            View Edit/Delete Logs & Reports
        </a>
    </div>

    {{-- Top summary cards --}}
    <div class="row mb-4">

       {{-- Total Balance --}}
<div class="col-md-4 mb-3">
    <div class="card" style="background:#198754; color:#fff;">
        <div class="card-body">
            <h5 class="mb-1" style="color:#fff !important;">Available Balance</h5>
            <h2 class="mb-0" style="color:#fff !important;">{{ number_format($balance, 2) }} ৳</h2>
            <small style="color:#fff !important; opacity:0.85;">In – Out এর পার্থক্য</small>
        </div>
    </div>
</div>

{{-- This Year --}}
<div class="col-md-4 mb-3">
    <div class="card" style="background:#0d6efd; color:#fff;">
        <div class="card-body">
            <h5 class="mb-1" style="color:#fff !important;">This Year ({{ $currentYear }})</h5>
            <h3 class="mb-0" style="color:#fff !important;">{{ number_format($yearlyAdded, 2) }} ৳</h3>
            <small style="color:#fff !important; opacity:0.85;">এই বছরে মোট ফান্ড যোগ হয়েছে</small>
        </div>
    </div>
</div>

{{-- This Month --}}
<div class="col-md-4 mb-3">
    <div class="card" style="background:#222275; color:#fff;">
        <div class="card-body">
            <h5 class="mb-1" style="color:#fff !important;">This Month ({{ \Carbon\Carbon::create()->month($currentMonth)->format('F') }})</h5>
            <h3 class="mb-0" style="color:#fff !important;">{{ number_format($monthlyAdded, 2) }} ৳</h3>
            <small style="color:#fff !important; opacity:0.85;">এই মাসে মোট ফান্ড যোগ হয়েছে</small>
        </div>
    </div>
</div>


    </div>

    {{-- Add / Withdraw + Export --}}
    <div class="row mb-4">
        {{-- Add Fund --}}
        <div class="col-md-4 mb-3">
            <div class="card">
                <div class="card-header bg-light">
                    <strong>➕ Add Fund</strong>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.fund.add') }}" method="POST">
                        @csrf
                        <div class="mb-2">
                            <label class="form-label">Amount (৳)</label>
                            <input type="number" name="amount" class="form-control @error('amount') is-invalid @enderror"
                                   placeholder="Amount" step="0.01" min="1" required>
                            @error('amount')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>
                        <div class="mb-2">
                            <label class="form-label">Note</label>
                            <input type="text" name="note" class="form-control" placeholder="Note (optional)">
                        </div>
                        <button class="btn btn-primary w-100">Add Fund</button>
                    </form>
                </div>
            </div>
        </div>

        {{-- Withdraw --}}
        <div class="col-md-4 mb-3">
            <div class="card">
                <div class="card-header bg-light">
                    <strong>➖ Withdraw</strong>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.fund.withdraw') }}" method="POST">
                        @csrf
                        <div class="mb-2">
                            <label class="form-label">Amount (৳)</label>
                            <input type="number" name="amount" class="form-control"
                                   placeholder="Amount" step="0.01" min="1" required>
                        </div>
                        <div class="mb-2">
                            <label class="form-label">Note</label>
                            <input type="text" name="note" class="form-control" placeholder="Note (optional)">
                        </div>
                        <button class="btn btn-danger w-100">Withdraw</button>
                    </form>
                </div>
            </div>
        </div>

        {{-- Export Report --}}
        <div class="col-md-4 mb-3">
            <div class="card">
                <div class="card-header bg-light">
                    <strong>📤 Export Report</strong>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.fund.export') }}" method="GET" id="fundExportForm">
                        <div class="mb-2">
                            <label class="form-label">Filter Type</label>
                            <select name="filter" id="filter_type" class="form-select">
                                <option value="year" selected>Yearly</option>
                                <option value="month">Monthly</option>
                                <option value="custom">Custom Date</option>
                            </select>
                        </div>

                        {{-- Year field --}}
                        <div class="mb-2" id="year_field">
                            <label class="form-label">Year</label>
                            <input type="number" name="year" class="form-control"
                                   value="{{ $currentYear }}" min="2000" max="2100">
                        </div>

                        {{-- Month field --}}
                        <div class="mb-2 d-none" id="month_field">
                            <label class="form-label">Month</label>
                            <select name="month" class="form-select">
                                @for($m=1;$m<=12;$m++)
                                    <option value="{{ $m }}" {{ $m == $currentMonth ? 'selected' : '' }}>
                                        {{ \Carbon\Carbon::create()->month($m)->format('F') }}
                                    </option>
                                @endfor
                            </select>
                        </div>

                        {{-- Custom date range --}}
                        <div class="mb-2 d-none" id="custom_date_fields">
                            <label class="form-label">From Date</label>
                            <input type="date" name="from_date" class="form-control mb-2">
                            <label class="form-label">To Date</label>
                            <input type="date" name="to_date" class="form-control">
                        </div>

                        <button class="btn btn-outline-secondary w-100 mt-2">
                            <i class="fe-download"></i> Download CSV
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    {{-- Fund History --}}
    <div class="card">
        <div class="card-header bg-light d-flex justify-content-between align-items-center">
            <strong>🧾 Fund History</strong>
            <div>
                <a href="{{ route('admin.fund.logs') }}" class="btn btn-sm btn-outline-info">
                    <i data-feather="file-text" class="me-1" style="width:14px;height:14px;"></i> View Logs / Reports
                </a>
                <small class="text-muted ms-2">সর্বশেষ ট্রান্স্যাকশন লিস্ট</small>
            </div>
        </div>
        <div class="card-body table-responsive">
            <table class="table table-bordered table-striped align-middle">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Type</th>
                        <th>Source</th>
                        <th>Amount</th>
                        <th>Note</th>
                        <th>Date & Time</th>
                        @php
                            // Check if current user is Admin (Super Admin or has Admin role)
                            $isAdmin = false;
                            $user = Auth::guard('admin')->user();
                            if ($user) {
                                if ($user->id == 1) {
                                    $isAdmin = true;
                                } else {
                                    $spatieRoles = $user->getRoleNames()->map(function($role) {
                                        return strtolower($role);
                                    })->toArray();
                                    $isAdmin = in_array('admin', $spatieRoles);
                                }
                            }
                        @endphp
                        @if($isAdmin)
                        <th>Actions</th>
                        @endif
                    </tr>
                </thead>
                <tbody>
                    @forelse($transactions as $t)
                        <tr>
                            <td>{{ $t->id }}</td>
                            <td>
                                @if($t->direction == 'in')
                                    <span class="badge bg-success">IN (+)</span>
                                @else
                                    <span class="badge bg-danger">OUT (-)</span>
                                @endif
                            </td>
                            <td>
                                {{ $t->source ?? '-' }}
                                @if($t->hasBeenEdited())
                                    <span class="badge bg-warning ms-1" title="This transaction has been edited">
                                        <i class="fe-edit" style="width:12px;height:12px;"></i> Edited
                                    </span>
                                @endif
                            </td>
                            <td>{{ number_format($t->amount, 2) }} ৳</td>
                            <td>
                                {{ $t->note ?? '-' }}
                                @if($t->updated_by)
                                    <br>
                                    <small class="text-muted">
                                        <i class="fe-edit-2" style="width:12px;height:12px;"></i> Last updated: {{ $t->updated_at ? $t->updated_at->format('d M Y, h:i A') : 'N/A' }}
                                    </small>
                                @endif
                            </td>
                            <td>{{ $t->created_at->format('d M Y, h:i A') }}</td>
                            @if($isAdmin)
                            <td>
                                <div class="d-flex gap-2">
                                    <a href="{{ route('admin.fund.edit', $t->id) }}" class="btn btn-sm btn-outline-primary" title="Edit">
                                        <i class="fe-edit"></i>
                                    </a>
                                    <form method="POST" action="{{ route('admin.fund.destroy', $t->id) }}" class="d-inline delete-form">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger delete-confirm" title="Delete" onclick="return confirm('Are you sure you want to delete this transaction?');">
                                            <i class="fe-trash-2"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                            @endif
                        </tr>
                    @empty
                        <tr>
                            <td colspan="{{ $isAdmin ? 7 : 6 }}" class="text-center text-muted">
                                No fund transactions found.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>

            {{-- Pagination --}}
            <div class="mt-2">
                {{ $transactions->links('pagination::bootstrap-4') }}
            </div>
        </div>
    </div>

</div>
@endsection

@section('script')
<script>
    // Export form filter UI show/hide
    (function () {
        const filterSelect      = document.getElementById('filter_type');
        const yearField         = document.getElementById('year_field');
        const monthField        = document.getElementById('month_field');
        const customDateFields  = document.getElementById('custom_date_fields');

        function updateFilterFields() {
            const val = filterSelect.value;

            yearField.classList.add('d-none');
            monthField.classList.add('d-none');
            customDateFields.classList.add('d-none');

            if (val === 'year') {
                yearField.classList.remove('d-none');
            } else if (val === 'month') {
                yearField.classList.remove('d-none');
                monthField.classList.remove('d-none');
            } else if (val === 'custom') {
                customDateFields.classList.remove('d-none');
            }
        }

        filterSelect.addEventListener('change', updateFilterFields);
        updateFilterFields(); // on page load
    })();
</script>
@endsection
