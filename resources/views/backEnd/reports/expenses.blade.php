@extends('backEnd.layouts.master')
@section('title', 'Expense Report')

@section('css')
<style>
    /* --- Modern Card --- */
    .card-modern {
        border: none;
        border-radius: 12px;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05);
        background: #fff;
        margin-bottom: 24px;
        transition: transform 0.2s;
    }
    
    /* --- Filter Section --- */
    .filter-card {
        background: #fff;
        border-radius: 12px;
        padding: 20px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.02);
        border: 1px solid #e2e8f0;
        margin-bottom: 24px;
    }
    .form-label-custom {
        font-size: 0.8rem; font-weight: 600; text-transform: uppercase; color: #64748b; margin-bottom: 5px;
    }
    .form-control-custom, .form-select-custom {
        border: 1px solid #e2e8f0; border-radius: 8px; padding: 10px 15px; font-size: 0.9rem;
    }
    .form-control-custom:focus, .form-select-custom:focus {
        border-color: #ef4444; box-shadow: 0 0 0 3px rgba(239, 68, 68, 0.1);
    }

    /* --- Stat Card (Expense Specific) --- */
    .expense-card {
        padding: 25px;
        background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
        border-radius: 12px;
        color: #fff;
        box-shadow: 0 4px 15px rgba(239, 68, 68, 0.3);
        display: flex; align-items: center; justify-content: space-between;
    }
    .expense-icon {
        font-size: 32px; opacity: 0.8;
    }
    .expense-label { font-size: 0.9rem; opacity: 0.9; margin-bottom: 5px; text-transform: uppercase; letter-spacing: 0.5px; }
    .expense-amount { font-size: 2rem; font-weight: 700; margin: 0; line-height: 1.2; }

    /* --- Table Styles --- */
    .table-modern th {
        background-color: #f8fafc; color: #475569; font-size: 0.75rem;
        font-weight: 700; text-transform: uppercase; padding: 1rem; border-bottom: 1px solid #e2e8f0;
    }
    .table-modern td {
        padding: 1rem; vertical-align: middle; font-size: 0.875rem; color: #334155;
        border-bottom: 1px solid #f1f5f9;
    }
    .table-modern tr:hover td { background-color: #fff1f2; }

    /* --- Utilities --- */
    .btn-custom-danger { background: #ef4444; color: #fff; border: none; padding: 10px 20px; border-radius: 8px; font-weight: 600; }
    .btn-custom-danger:hover { background: #dc2626; color: #fff; }
    .btn-custom-outline { background: transparent; border: 1px solid #10b981; color: #10b981; padding: 10px 20px; border-radius: 8px; font-weight: 600; }
    .btn-custom-outline:hover { background: #10b981; color: #fff; }
</style>
@endsection

@section('content')
<div class="container-fluid py-4">

    {{-- HEADER --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="mb-1 fw-bold text-dark">
                <i data-feather="credit-card" class="text-danger me-2"></i> Expense Reports
            </h4>
            <p class="text-muted small mb-0">Track company spending for: <strong>{{ $label ?? 'Today' }}</strong></p>
        </div>
    </div>

    {{-- FILTER FORM --}}
    <div class="filter-card">
        <form method="GET" action="{{ route('admin.reports.expenses') }}" id="expense-filter-form">
            <div class="row g-3 align-items-end">
                
                {{-- Report Type --}}
                <div class="col-md-3">
                    <label class="form-label-custom">Filter By</label>
                    <div class="input-group">
                        <span class="input-group-text bg-white border-end-0"><i data-feather="filter" style="width:16px;"></i></span>
                        <select name="type" class="form-select form-select-custom border-start-0" id="report-type">
                            <option value="today" {{ ($type ?? '')=='today' ? 'selected' : '' }}>Today</option>
                            <option value="month" {{ ($type ?? '')=='month' ? 'selected' : '' }}>Monthly</option>
                            <option value="year"  {{ ($type ?? '')=='year'  ? 'selected' : '' }}>Yearly</option>
                            <option value="range" {{ ($type ?? '')=='range' ? 'selected' : '' }}>Custom Date Range</option>
                        </select>
                    </div>
                </div>

                {{-- Dynamic Inputs --}}
                <div class="col-md-2 type-month type-year" style="display:none;">
                    <label class="form-label-custom">Year</label>
                    <input type="number" name="year" class="form-control form-control-custom" value="{{ request('year', now()->year) }}" placeholder="YYYY">
                </div>

                <div class="col-md-2 type-month" style="display:none;">
                    <label class="form-label-custom">Month</label>
                    <select name="month" class="form-select form-select-custom">
                        @for($m=1; $m<=12; $m++)
                            <option value="{{ $m }}" {{ request('month', now()->month) == $m ? 'selected' : '' }}>
                                {{ date('F', mktime(0, 0, 0, $m, 1)) }}
                            </option>
                        @endfor
                    </select>
                </div>

                <div class="col-md-2 type-range" style="display:none;">
                    <label class="form-label-custom">Start Date</label>
                    <input type="date" name="from_date" class="form-control form-control-custom" value="{{ request('from_date') }}">
                </div>

                <div class="col-md-2 type-range" style="display:none;">
                    <label class="form-label-custom">End Date</label>
                    <input type="date" name="to_date" class="form-control form-control-custom" value="{{ request('to_date') }}">
                </div>

                {{-- Actions --}}
                <div class="col-md-auto ms-auto d-flex gap-2">
                    <button class="btn btn-custom-danger" type="submit">
                        <i data-feather="search" class="me-1" style="width:16px;"></i> Find
                    </button>
                    <button class="btn btn-custom-outline" type="submit" name="export" value="csv" id="export-csv-btn">
                        <i data-feather="download" class="me-1" style="width:16px;"></i> CSV
                    </button>
                </div>
            </div>
        </form>
    </div>

    {{-- SUMMARY CARD --}}
    <div class="row mb-4">
        <div class="col-md-4">
            <div class="expense-card">
                <div>
                    <div class="expense-label">Total Expenses</div>
                    <h2 class="expense-amount">৳{{ number_format($totalExpense ?? 0, 2) }}</h2>
                </div>
                <div class="expense-icon">
                    <i data-feather="trending-down"></i>
                </div>
            </div>
        </div>
    </div>

    {{-- EXPENSE TABLE --}}
    <div id="expense-table-wrapper">
        <div class="card card-modern">
            <div class="card-header border-bottom bg-white py-3">
                <h5 class="mb-0 fw-bold text-dark">Expense List</h5>
            </div>
            
            <div class="table-responsive">
                <table class="table table-modern mb-0">
                    <thead>
                        <tr>
                            <th width="5%">#</th>
                            <th width="15%">Date</th>
                            <th width="20%">Title</th>
                            <th width="15%">Category</th>
                            <th width="15%" class="text-end">Amount</th>
                            <th width="30%">Note</th>
                        </tr>
                    </thead>
                    <tbody>
                    @forelse($expenses as $e)
                        @php
                            $rawDate = $e->expense_date ?? $e->created_at;
                            $formattedDate = $rawDate ? \Carbon\Carbon::parse($rawDate)->format('d M, Y') : '-';
                        @endphp
                        <tr>
                            <td class="text-muted">{{ $loop->iteration }}</td>
                            <td>{{ $formattedDate }}</td>
                            <td class="fw-bold text-dark">{{ $e->title }}</td>
                            <td><span class="badge bg-light text-dark border">{{ $e->category ?? 'General' }}</span></td>
                            <td class="text-end fw-bold text-danger">৳{{ number_format($e->amount ?? 0, 2) }}</td>
                            <td class="text-muted small fst-italic">{{ Str::limit($e->note, 50) ?? '-' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-5">
                                <div class="d-flex flex-column align-items-center">
                                    <img src="https://cdn-icons-png.flaticon.com/512/7486/7486744.png" width="50" class="opacity-25 mb-2">
                                    <p class="text-muted fw-bold mb-0">No expenses found</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Pagination --}}
            <div class="p-4 border-top d-flex justify-content-between align-items-center">
                <small class="text-muted">Showing {{ $expenses->firstItem() }} to {{ $expenses->lastItem() }} of {{ $expenses->total() }} entries</small>
                <div>{{ $expenses->links('pagination::bootstrap-4') }}</div>
            </div>
        </div>
    </div>

</div>
@endsection

@section('script')
<script>
    /* -------- Toggle fields -------- */
    function toggleReportFields() {
        let type = document.getElementById('report-type').value;

        document.querySelectorAll('.type-month,.type-year,.type-range')
            .forEach(el => el.style.display = 'none');

        if (type === 'month') {
            document.querySelectorAll('.type-month,.type-year')
                .forEach(el => el.style.display = 'block');
        } else if (type === 'year') {
            document.querySelectorAll('.type-year')
                .forEach(el => el.style.display = 'block');
        } else if (type === 'range') {
            document.querySelectorAll('.type-range')
                .forEach(el => el.style.display = 'block');
        }
    }

    document.getElementById('report-type')
        .addEventListener('change', toggleReportFields);
    toggleReportFields();

    /* -------- AJAX Filter + Pagination -------- */
    const form = document.getElementById('expense-filter-form');

    form.addEventListener('submit', function(e){
        // CSV হলে normal submit
        if (e.submitter && e.submitter.id === 'export-csv-btn') {
            return true;
        }

        e.preventDefault();
        loadExpenses(new URLSearchParams(new FormData(form)).toString());
    });

    document.addEventListener('click', function(e){
        let link = e.target.closest('.pagination a');
        if (!link) return;

        e.preventDefault();
        let query = link.getAttribute('href').split('?')[1] || '';
        loadExpenses(query);
    });

    function loadExpenses(query) {
        fetch("{{ route('admin.reports.expenses') }}?" + query, {
            headers: {'X-Requested-With':'XMLHttpRequest'}
        })
        .then(res => res.text())
        .then(html => {
            let temp = document.createElement('div');
            temp.innerHTML = html;

            document.getElementById('expense-table-wrapper').innerHTML =
                temp.querySelector('#expense-table-wrapper').innerHTML;
        });
    }
</script>
@endsection