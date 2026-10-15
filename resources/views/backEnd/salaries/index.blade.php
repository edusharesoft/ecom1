@extends('backEnd.layouts.master')
@section('title', 'Salary Management')

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
    .badge-calculated { background: #e0f2fe; color: #075985; }
    .badge-pending { background: #fef3c7; color: #92400e; }
    .badge-days { background: #f1f5f9; color: #475569; padding: 4px 8px; border-radius: 4px; }
    
    /* --- Action Buttons --- */
    .btn-icon {
        width: 32px; height: 32px;
        display: inline-flex; align-items: center; justify-content: center;
        border-radius: 8px; transition: all 0.2s; border: none;
    }
    .btn-icon:hover { transform: translateY(-2px); }
    .btn-view { background: #e0e7ff; color: #4338ca; }
    .btn-pay { background: #dcfce7; color: #166534; }
</style>
@endsection

@section('content')
<div class="container-fluid py-4">

    {{-- PAGE HEADER --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="mb-1 fw-bold text-dark">
                <i data-feather="dollar-sign" class="text-primary me-2"></i> Salary Management
            </h4>
            <p class="text-muted small mb-0">Calculate and manage employee monthly salaries.</p>
        </div>
        <div class="d-flex gap-2">
            <button type="button" class="btn btn-white border shadow-sm rounded-pill px-3 text-dark" data-bs-toggle="modal" data-bs-target="#calculateModal">
                <i data-feather="plus" class="me-1" style="width: 16px;"></i> Calculate Single
            </button>
            <button type="button" class="btn btn-primary px-4 rounded-pill shadow-sm" data-bs-toggle="modal" data-bs-target="#bulkCalculateModal">
                <i data-feather="zap" class="me-1" style="width: 16px;"></i> Bulk Calculate
            </button>
        </div>
    </div>

    <div class="card card-modern">
        
        {{-- FILTERS --}}
        <div class="filter-container">
            <form method="GET" action="{{ route('admin.salaries.index') }}">
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
                            <option value="calculated" {{ request('status') == 'calculated' ? 'selected' : '' }}>Calculated</option>
                            <option value="paid" {{ request('status') == 'paid' ? 'selected' : '' }}>Paid</option>
                        </select>
                    </div>
                    <div class="col-md-3 d-flex gap-2">
                        <button type="submit" class="btn btn-dark w-100 fw-bold">Filter</button>
                        <a href="{{ route('admin.salaries.index') }}" class="btn btn-light border px-3" title="Reset">
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
                        <th width="20%">Employee Details</th>
                        <th width="10%">Month</th>
                        <th width="20%">Attendance Summary</th>
                        <th width="15%">Gross Salary</th>
                        <th width="15%">Net Payable</th>
                        <th width="10%">Status</th>
                        <th width="5%" class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($salaries as $salary)
                        <tr>
                            <td class="text-muted">{{ $loop->iteration + ($salaries->currentPage()-1)*$salaries->perPage() }}</td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="bg-light text-primary fw-bold rounded-circle d-flex align-items-center justify-content-center me-2" 
                                         style="width: 32px; height: 32px; font-size: 12px; border: 1px solid #e0e7ff;">
                                        {{ substr($salary->employee->name, 0, 1) }}
                                    </div>
                                    <div>
                                        <div class="fw-bold text-dark">{{ $salary->employee->name }}</div>
                                        <div class="small text-muted">ID: {{ $salary->employee->employee_id }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="fw-medium text-dark">{{ \Carbon\Carbon::parse($salary->salary_month)->format('M Y') }}</td>
                            <td>
                                <div class="d-flex gap-2 text-xs">
                                    <span class="badge-days" title="Working Days">W: {{ $salary->working_days }}</span>
                                    <span class="badge-days text-success" title="Present">P: {{ $salary->present_days }}</span>
                                    <span class="badge-days text-danger" title="Absent">A: {{ $salary->absent_days }}</span>
                                </div>
                            </td>
                            <td class="text-muted">৳{{ number_format($salary->gross_salary, 2) }}</td>
                            <td class="fw-bold text-dark fs-6">৳{{ number_format($salary->net_salary, 2) }}</td>
                            <td>
                                @if($salary->status == 'paid')
                                    <span class="badge-soft badge-paid">Paid</span>
                                @elseif($salary->status == 'calculated')
                                    <span class="badge-soft badge-calculated">Calculated</span>
                                @else
                                    <span class="badge-soft badge-pending">Pending</span>
                                @endif
                            </td>
                            <td class="text-end">
                                <div class="d-flex justify-content-end gap-1">
                                    <a href="{{ route('admin.salaries.show', $salary->id) }}" class="btn-icon btn-view" title="View Details">
                                        <i data-feather="eye" style="width:14px;"></i>
                                    </a>
                                    @if($salary->status == 'calculated')
                                        <a href="{{ route('admin.salary_payments.create', ['employee_id' => $salary->employee_id, 'salary_id' => $salary->id]) }}" class="btn-icon btn-pay" title="Pay Now">
                                            <i data-feather="credit-card" style="width:14px;"></i>
                                        </a>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center py-5">
                                <img src="https://cdn-icons-png.flaticon.com/512/7486/7486744.png" width="60" class="mb-3 opacity-25">
                                <p class="text-muted fw-bold mb-0">No salary records found</p>
                                <small class="text-muted">Try calculating salary for a month.</small>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- PAGINATION --}}
        <div class="p-4 border-top d-flex justify-content-between align-items-center bg-white rounded-bottom">
            <small class="text-muted">
                Showing <strong>{{ $salaries->firstItem() }}</strong> to <strong>{{ $salaries->lastItem() }}</strong> of <strong>{{ $salaries->total() }}</strong> records
            </small>
            <div>
                {{ $salaries->links('pagination::bootstrap-4') }}
            </div>
        </div>

    </div>
</div>

<div class="modal fade" id="calculateModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header border-bottom-0 pb-0">
                <h5 class="modal-title fw-bold">Calculate Salary</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('admin.salaries.calculate') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-muted">Select Employee <span class="text-danger">*</span></label>
                        <select name="employee_id" class="form-control select2" required style="width: 100%;">
                            <option value="">Choose Employee</option>
                            @foreach($employees as $emp)
                                <option value="{{ $emp->id }}">{{ $emp->name }} ({{ $emp->employee_id }})</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-muted">Salary Month <span class="text-danger">*</span></label>
                        <input type="month" name="salary_month" class="form-control" value="{{ date('Y-m') }}" required>
                    </div>
                </div>
                <div class="modal-footer border-top-0 pt-0">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary px-4">Calculate</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="bulkCalculateModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header border-bottom-0 pb-0">
                <h5 class="modal-title fw-bold">Bulk Salary Calculation</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('admin.salaries.bulk_calculate') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="alert alert-soft-info d-flex align-items-center mb-3 border-0 bg-light p-3 rounded">
                        <i data-feather="info" class="me-2 text-primary"></i>
                        <small class="text-muted">This will calculate salary for <strong>ALL active employees</strong> for the selected month.</small>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-muted">Select Month <span class="text-danger">*</span></label>
                        <input type="month" name="salary_month" class="form-control" value="{{ date('Y-m') }}" required>
                    </div>
                </div>
                <div class="modal-footer border-top-0 pt-0">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary px-4">Start Calculation</button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
    $(document).ready(function() {
        if ($.fn.select2) {
            $('.select2').select2({
                width: '100%',
                dropdownParent: $('#calculateModal') // Fix for modal select2 issue
            });
        }
    });
</script>
@endpush