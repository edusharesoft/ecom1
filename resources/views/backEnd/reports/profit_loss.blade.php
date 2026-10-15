@extends('backEnd.layouts.master')
@section('title', 'Profit & Loss Report')

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
        border-color: #6366f1; box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.1);
    }

    /* --- Stat Cards --- */
    .stat-card {
        padding: 20px; text-align: center;
        background: #fff; border-radius: 12px; border: 1px solid #f1f5f9;
        box-shadow: 0 2px 6px rgba(0,0,0,0.02);
        position: relative; overflow: hidden;
    }
    .stat-title { font-size: 0.85rem; color: #64748b; font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px; }
    .stat-amount { font-size: 1.75rem; font-weight: 700; margin: 10px 0 0; }
    
    .bg-soft-primary { background: #e0e7ff; color: #4338ca; }
    .bg-soft-warning { background: #fef3c7; color: #b45309; }
    .bg-soft-secondary { background: #f1f5f9; color: #475569; }
    .bg-soft-success { background: #dcfce7; color: #166534; }
    .bg-soft-danger { background: #fee2e2; color: #991b1b; }

    /* --- Summary Table --- */
    .summary-table th { background-color: #f8fafc; font-weight: 600; color: #475569; padding: 15px; }
    .summary-table td { padding: 15px; font-size: 1rem; font-weight: 500; color: #1e293b; }
    .net-profit-row { background-color: #f0fdf4; border-top: 2px solid #bbf7d0; }
    .net-loss-row { background-color: #fef2f2; border-top: 2px solid #fecaca; }

    /* --- Utilities --- */
    .btn-custom-primary { background: #4f46e5; color: #fff; border: none; padding: 10px 20px; border-radius: 8px; font-weight: 600; }
    .btn-custom-primary:hover { background: #4338ca; color: #fff; }
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
                <i data-feather="pie-chart" class="text-primary me-2"></i> Profit & Loss Report
            </h4>
            <p class="text-muted small mb-0">Financial overview for: <strong>{{ $label ?? 'Today' }}</strong></p>
        </div>
    </div>

    {{-- FILTER FORM --}}
    <div class="filter-card">
        <form method="GET" action="{{ route('admin.reports.profit_loss') }}" id="profitLossFilterForm">
            <div class="row g-3 align-items-end">
                @php $type = $type ?? request('type','today'); @endphp

                <div class="col-md-3">
                    <label class="form-label-custom">Filter By</label>
                    <div class="input-group">
                        <span class="input-group-text bg-white border-end-0"><i data-feather="filter" style="width:16px;"></i></span>
                        <select name="type" class="form-select form-select-custom border-start-0" id="report-type">
                            <option value="today" {{ $type=='today'?'selected':'' }}>Today</option>
                            <option value="month" {{ $type=='month'?'selected':'' }}>Monthly</option>
                            <option value="year"  {{ $type=='year'?'selected':'' }}>Yearly</option>
                            <option value="range" {{ $type=='range'?'selected':'' }}>Custom Range</option>
                        </select>
                    </div>
                </div>

                <div class="col-md-2 type-month type-year" style="display:none;">
                    <label class="form-label-custom">Year</label>
                    <input type="number" name="year" class="form-control form-control-custom" value="{{ request('year', now()->year) }}">
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

                <div class="col-md-auto ms-auto d-flex gap-2">
                    <button class="btn btn-custom-primary" type="submit">
                        <i data-feather="refresh-cw" class="me-1" style="width:16px;"></i> Generate
                    </button>
                    <button class="btn btn-custom-outline" type="submit" name="export" value="csv">
                        <i data-feather="download" class="me-1" style="width:16px;"></i> CSV
                    </button>
                </div>
            </div>
        </form>
    </div>

    {{-- RESULT AREA --}}
    <div id="profitLossResult">
        
        {{-- Stat Cards --}}
        <div class="row g-4 mb-4">
            <div class="col-md-3">
                <div class="stat-card bg-soft-primary">
                    <div class="stat-title">Total Sales</div>
                    <div class="stat-amount">৳{{ number_format($salesAmount ?? 0, 2) }}</div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stat-card bg-soft-warning">
                    <div class="stat-title">COGS (Cost)</div>
                    <div class="stat-amount">৳{{ number_format($cogs ?? 0, 2) }}</div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stat-card bg-soft-secondary">
                    <div class="stat-title">Total Expenses</div>
                    <div class="stat-amount">৳{{ number_format($totalExpense ?? 0, 2) }}</div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stat-card {{ ($netProfit ?? 0) >= 0 ? 'bg-soft-success' : 'bg-soft-danger' }}">
                    <div class="stat-title">Net Profit/Loss</div>
                    <div class="stat-amount">৳{{ number_format($netProfit ?? 0, 2) }}</div>
                </div>
            </div>
        </div>

        {{-- Detailed Summary Table --}}
        <div class="card card-modern">
            <div class="card-header border-bottom bg-white py-3">
                <h5 class="mb-0 fw-bold text-dark">Financial Statement</h5>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-bordered mb-0 summary-table">
                        <tbody>
                            <tr>
                                <th width="40%">Total Revenue (Sales)</th>
                                <td class="text-end text-primary">৳{{ number_format($salesAmount ?? 0, 2) }}</td>
                            </tr>
                            <tr>
                                <th>Cost of Goods Sold (COGS)</th>
                                <td class="text-end text-danger">- ৳{{ number_format($cogs ?? 0, 2) }}</td>
                            </tr>
                            <tr class="bg-light">
                                <th><strong>Gross Profit</strong></th>
                                <td class="text-end fw-bold">৳{{ number_format($grossProfit ?? 0, 2) }}</td>
                            </tr>
                            <tr>
                                <th>Operating Expenses</th>
                                <td class="text-end text-danger">- ৳{{ number_format($totalExpense ?? 0, 2) }}</td>
                            </tr>
                            <tr class="{{ ($netProfit ?? 0) >= 0 ? 'net-profit-row' : 'net-loss-row' }}">
                                <th class="fs-5"><strong>Net Profit / (Loss)</strong></th>
                                <td class="text-end fs-4 fw-bold {{ ($netProfit ?? 0) >= 0 ? 'text-success' : 'text-danger' }}">
                                    ৳{{ number_format($netProfit ?? 0, 2) }}
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

    </div>
</div>
@endsection

@section('script')
<script>
    function toggleReportFields(){
        let type = $('#report-type').val();
        $('.type-month,.type-year,.type-range').hide();

        if(type==='month'){
            $('.type-month,.type-year').show();
        }else if(type==='year'){
            $('.type-year').show();
        }else if(type==='range'){
            $('.type-range').show();
        }
    }
    toggleReportFields();
    $('#report-type').on('change', toggleReportFields);

    /* ========== AJAX FILTER ========== */
    $('#profitLossFilterForm').on('submit', function(e){
        // Allow normal submit for CSV export
        if(document.activeElement.name === 'export') return;

        e.preventDefault();
        
        // Add loading indicator
        let btn = $(this).find('button[type="submit"]:first');
        let originalText = btn.html();
        btn.html('<i class="spinner-border spinner-border-sm"></i> Generating...');
        btn.prop('disabled', true);

        $.get($(this).attr('action'), $(this).serialize(), function(res){
            $('#profitLossResult').html(
                $(res).find('#profitLossResult').html()
            );
            btn.html(originalText);
            btn.prop('disabled', false);
        }).fail(function() {
            alert('Something went wrong. Please try again.');
            btn.html(originalText);
            btn.prop('disabled', false);
        });
    });
</script>
@endsection