@extends('backEnd.layouts.master')
@section('title','Purchases')

@php
    use Illuminate\Support\Facades\Auth;
@endphp

@section('css')
<style>
    /* --- Modern Card Style --- */
    .card {
        border: none;
        border-radius: 10px;
        box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.1);
        transition: all 0.3s ease;
    }
    .card-header {
        background-color: #fff;
        border-bottom: 1px solid #e3e6f0;
        padding: 1rem 1.35rem;
        font-weight: 700;
        color: #4e73df;
        border-radius: 10px 10px 0 0 !important;
    }
    
    /* --- Stats Widgets --- */
    .stats-card {
        display: flex;
        align-items: center;
        padding: 1.5rem;
    }
    .stats-icon {
        width: 50px;
        height: 50px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 24px;
        margin-right: 1rem;
        flex-shrink: 0;
    }
    .bg-soft-primary { background-color: rgba(78, 115, 223, 0.1); color: #4e73df; }
    .bg-soft-success { background-color: rgba(28, 200, 138, 0.1); color: #1cc88a; }
    .bg-soft-info    { background-color: rgba(54, 185, 204, 0.1); color: #36b9cc; }
    .bg-soft-danger  { background-color: rgba(231, 74, 59, 0.1); color: #e74a3b; }

    .stats-label { font-size: 0.8rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 0.25rem; }
    .stats-value { font-size: 1.5rem; font-weight: 700; color: #5a5c69; margin-bottom: 0; }
    .stats-sub   { font-size: 0.75rem; color: #858796; }

    /* --- Form Elements --- */
    .form-label { font-weight: 600; font-size: 0.85rem; color: #5a5c69; margin-bottom: 0.3rem; }
    .form-control, .form-select {
        border-radius: 6px;
        border: 1px solid #d1d3e2;
        padding: 0.5rem 0.75rem;
    }
    .form-control:focus { border-color: #bac8f3; box-shadow: 0 0 0 0.2rem rgba(78, 115, 223, 0.25); }

    /* --- Table Styles --- */
    .table thead th {
        background-color: #f8f9fc;
        color: #4e73df;
        font-size: 0.85rem;
        font-weight: 700;
        text-transform: uppercase;
        border-bottom: 2px solid #e3e6f0;
        padding: 1rem 0.75rem;
    }
    .table tbody td {
        font-size: 0.9rem;
        vertical-align: middle;
        padding: 0.75rem;
        color: #5a5c69;
    }
    .table-hover tbody tr:hover { background-color: #f8f9fc; }

    /* --- Action Buttons --- */
    .btn-action {
        width: 32px; height: 32px;
        display: inline-flex; align-items: center; justify-content: center;
        border-radius: 6px; transition: all 0.2s;
    }
    .pay-input { width: 80px; font-size: 0.8rem; border-radius: 4px; border: 1px solid #d1d3e2; padding: 4px; }
    .pay-btn { padding: 4px 10px; font-size: 0.8rem; border-radius: 4px; font-weight: 600; }
</style>
@endsection

@section('content')
<div class="container-fluid mb-5">

    <div class="d-sm-flex align-items-center justify-content-between mb-4 mt-2">
        <h1 class="h3 mb-0 text-gray-800 fw-bold">🛒 Purchase Management</h1>
        <a href="{{ route('purchases.logs') }}" class="d-none d-sm-inline-block btn btn-sm btn-info shadow-sm">
            <i class="fe-file-text me-1"></i> View Reports / Logs
        </a>
    </div>

    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card h-100 py-2 border-start border-4 border-success">
                <div class="card-body stats-card">
                    <div class="stats-icon bg-soft-success"><i class="fe-calendar"></i></div>
                    <div>
                        <div class="stats-label text-success">This Year ({{ $currentYear }})</div>
                        <div class="stats-value">{{ number_format($yearlyTotal, 2) }} ৳</div>
                        <div class="stats-sub">Total Purchase</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card h-100 py-2 border-start border-4 border-info">
                <div class="card-body stats-card">
                    <div class="stats-icon bg-soft-info"><i class="fe-bar-chart-2"></i></div>
                    <div>
                        <div class="stats-label text-info">
                            {{ \Carbon\Carbon::createFromDate(now()->year, $currentMonth, 1)->format('F') }}
                        </div>
                        <div class="stats-value">{{ number_format($monthlyTotal, 2) }} ৳</div>
                        <div class="stats-sub">Monthly Purchase</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card h-100 py-2 border-start border-4 border-primary">
                <div class="card-body stats-card">
                    <div class="stats-icon bg-soft-primary"><i class="fe-shopping-bag"></i></div>
                    <div>
                        <div class="stats-label text-primary">Today ({{ now()->format('d M') }})</div>
                        <div class="stats-value">{{ number_format($todayTotal, 2) }} ৳</div>
                        <div class="stats-sub">Daily Purchase</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card h-100 py-2 border-start border-4 border-danger">
                <div class="card-body stats-card">
                    <div class="stats-icon bg-soft-danger"><i class="fe-alert-circle"></i></div>
                    <div>
                        <div class="stats-label text-danger">Supplier Due</div>
                        <div class="stats-value">{{ number_format($totalDue, 2) }} ৳</div>
                        <div class="stats-sub">Total Liability</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-8 mb-4">
            <div class="card mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary"><i class="fe-plus-circle me-1"></i> New Purchase Entry</h6>
                </div>
                <div class="card-body">
                    <form action="{{ route('purchases.store') }}" method="POST">
                        @csrf
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Supplier *</label>
                                <select name="supplier_id" class="form-control form-select" required>
                                    <option value="">-- Select Supplier --</option>
                                    @foreach($suppliers as $s)
                                        <option value="{{ $s->id }}">{{ $s->name }} ({{ $s->phone }})</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Invoice No *</label>
                                <input type="text" name="invoice_no" class="form-control" value="{{ 'PUR-'.time() }}" required readonly style="background-color: #f8f9fc;">
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Date *</label>
                                <input type="date" name="purchase_date" class="form-control" value="{{ now()->format('Y-m-d') }}" required>
                            </div>
                        </div>

                        <hr class="sidebar-divider my-3">
                        <h6 class="text-xs font-weight-bold text-uppercase text-gray-500 mb-3">Product Details</h6>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Product *</label>
                                <select name="product_id" class="form-control form-select" required>
                                    <option value="">-- Select Product --</option>
                                    @foreach($products as $p)
                                        <option value="{{ $p->id }}">{{ $p->name }} (Stock: {{ $p->stock }})</option>
                                    @endforeach
                                </select>
                            </div>
                            
                            <input type="hidden" name="variant_price_id" value="">

                            <div class="col-md-3 mb-3">
                                <label class="form-label">Quantity *</label>
                                <input type="number" name="qty" class="form-control" min="1" value="1" required>
                            </div>
                            <div class="col-md-3 mb-3">
                                <label class="form-label">Unit Cost (৳) *</label>
                                <input type="number" step="0.01" name="unit_cost" class="form-control" placeholder="0.00" required>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-3 mb-3">
                                <label class="form-label">Discount</label>
                                <input type="number" step="0.01" name="discount" class="form-control" value="0">
                            </div>
                            <div class="col-md-3 mb-3">
                                <label class="form-label">Shipping Cost</label>
                                <input type="number" step="0.01" name="shipping_cost" class="form-control" value="0">
                            </div>
                            <div class="col-md-3 mb-3">
                                <label class="form-label text-success fw-bold">Paid Amount</label>
                                <input type="number" step="0.01" name="paid_amount" class="form-control border-success" value="0">
                                <small class="text-muted" style="font-size:10px;">Deducted from fund</small>
                            </div>
                            <div class="col-md-3 mb-3">
                                <label class="form-label">Note</label>
                                <input type="text" name="note" class="form-control" placeholder="Optional">
                            </div>
                        </div>

                        <div class="mt-3 text-end">
                            <button type="submit" class="btn btn-primary px-4">
                                <i class="fe-save me-1"></i> Save Purchase
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-lg-4 mb-4">
            <div class="card h-100">
                <div class="card-header py-3 bg-white">
                    <h6 class="m-0 font-weight-bold text-dark"><i class="fe-download me-1"></i> Export Report</h6>
                </div>
                <div class="card-body">
                    <form action="{{ route('purchases.export') }}" method="GET" target="_blank">
                        <div class="mb-3">
                            <label class="form-label">Filter by Month/Year</label>
                            <div class="input-group">
                                <input type="number" name="month" class="form-control" placeholder="Month (1-12)" value="{{ request('month') }}">
                                <input type="number" name="year" class="form-control" placeholder="Year" value="{{ request('year') }}">
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Date Range</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-end-0">From</span>
                                <input type="date" name="from_date" class="form-control" value="{{ request('from_date') }}">
                            </div>
                            <div class="input-group mt-2">
                                <span class="input-group-text bg-light border-end-0" style="width: 58px;">To</span>
                                <input type="date" name="to_date" class="form-control" value="{{ request('to_date') }}">
                            </div>
                        </div>
                        <div class="d-grid">
                            <button type="submit" class="btn btn-outline-dark">
                                <i class="fe-download-cloud me-2"></i> Download CSV
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <h6 class="m-0 font-weight-bold text-primary"><i class="fe-list me-1"></i> Recent Purchase History</h6>
        </div>
        <div class="card-body p-0">
            <div id="purchase-table-wrapper" class="table-responsive">
                <table class="table table-bordered table-hover mb-0" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th class="text-center" width="5%">#</th>
                            <th width="12%">Date</th>
                            <th width="15%">Invoice</th>
                            <th width="15%">Supplier</th>
                            <th class="text-end" width="10%">Total</th>
                            <th class="text-end" width="10%">Paid</th>
                            <th class="text-end" width="10%">Due</th>
                            <th class="text-center" width="23%">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                    @forelse($purchases as $p)
                        @php
                            $user = Auth::guard('admin')->user();
                            $isAdmin = false;
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
                        <tr>
                            <td class="text-center">{{ $loop->iteration + ($purchases->currentPage()-1)*$purchases->perPage() }}</td>
                            <td>
                                {{ \Carbon\Carbon::parse($p->purchase_date)->format('d M, Y') }}
                            </td>
                            <td>
                                <span class="fw-bold text-dark">{{ $p->invoice_no }}</span>
                                @if($p->updated_by)
                                    <i class="fe-edit-2 text-warning ms-1" title="Edited" style="font-size: 10px;"></i>
                                @endif
                            </td>
                            <td>
                                @if($p->supplier)
                                    <div class="fw-bold text-secondary">{{ $p->supplier->name }}</div>
                                    <small class="text-muted">{{ $p->supplier->phone }}</small>
                                @else
                                    <span class="text-danger">Deleted</span>
                                @endif
                            </td>
                            <td class="text-end fw-bold">{{ number_format($p->grand_total,2) }}</td>
                            <td class="text-end text-success">{{ number_format($p->paid_amount,2) }}</td>
                            <td class="text-end text-danger">{{ number_format($p->due_amount,2) }}</td>
                            <td class="text-center">
                                <div class="d-flex justify-content-center align-items-center gap-1">
                                    <a href="{{ route('purchases.invoice',$p->id) }}" class="btn btn-action btn-outline-secondary" target="_blank" title="Invoice">
                                        <i class="fe-file-text"></i>
                                    </a>

                                    @if($p->due_amount > 0)
                                        <form action="{{ route('purchases.pay_due',$p->id) }}" method="POST" class="d-flex align-items-center bg-light rounded p-1 border">
                                            @csrf
                                            <input type="number" step="0.01" name="amount" class="pay-input me-1" placeholder="Pay Due" required>
                                            <input type="hidden" name="payment_date" value="{{ now()->format('Y-m-d') }}">
                                            <button class="btn btn-success pay-btn text-white" title="Pay Now">
                                                <i class="fe-check"></i>
                                            </button>
                                        </form>
                                    @else
                                        <span class="badge bg-success bg-soft-success text-success border border-success px-2">Paid</span>
                                    @endif

                                    @if($isAdmin)
                                        <a href="{{ route('purchases.edit', $p->id) }}" class="btn btn-action btn-outline-primary ms-1" title="Edit">
                                            <i class="fe-edit"></i>
                                        </a>
                                        <form method="POST" action="{{ route('purchases.destroy', $p->id) }}" class="d-inline delete-form">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-action btn-outline-danger delete-confirm ms-1" title="Delete" onclick="return confirm('Confirm delete? This affects stock & fund.');">
                                                <i class="fe-trash-2"></i>
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center py-5">
                                <img src="https://cdn-icons-png.flaticon.com/512/4076/4076432.png" width="60" class="mb-3 opacity-50">
                                <p class="text-muted mb-0">No purchase records found.</p>
                            </td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
                
                <div id="purchase-pagination" class="p-3 d-flex justify-content-end">
                    {{ $purchases->links('pagination::bootstrap-4') }}
                </div>
            </div>
        </div>
    </div>

</div>
@endsection

@push('scripts')
<script>
    // AJAX Pagination Script
    $(document).on('click', '#purchase-pagination a', function(e){
        e.preventDefault();
        let url = $(this).attr('href');
        // Add loading state opacity
        $('#purchase-table-wrapper').css('opacity', '0.5');
        
        $.get(url, function(response){
            let html = $(response).find('#purchase-table-wrapper').html();
            $('#purchase-table-wrapper').html(html);
            $('#purchase-table-wrapper').css('opacity', '1');
        });
    });
</script>
@endpush