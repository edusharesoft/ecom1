@extends('backEnd.layouts.master')
@section('title', 'Stock Report')

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
    
    /* --- Stat Cards --- */
    .stat-card {
        padding: 20px;
        background: #fff; border-radius: 12px; border: 1px solid #f1f5f9;
        box-shadow: 0 2px 6px rgba(0,0,0,0.02);
        display: flex; align-items: center; justify-content: space-between;
    }
    .stat-icon-box {
        width: 50px; height: 50px; border-radius: 10px;
        display: flex; align-items: center; justify-content: center; font-size: 24px;
    }
    
    .bg-light-primary { background: #e0e7ff; color: #4338ca; }
    .bg-light-info { background: #e0f2fe; color: #0369a1; }
    .bg-light-success { background: #dcfce7; color: #166534; }

    .stat-title { font-size: 0.85rem; color: #64748b; font-weight: 600; text-transform: uppercase; margin-bottom: 5px; }
    .stat-value { font-size: 1.5rem; font-weight: 700; color: #1e293b; margin: 0; }

    /* --- Table Styles --- */
    .table-modern th {
        background-color: #f8fafc; color: #475569; font-size: 0.75rem;
        font-weight: 700; text-transform: uppercase; padding: 1rem; border-bottom: 1px solid #e2e8f0;
    }
    .table-modern td {
        padding: 1rem; vertical-align: middle; font-size: 0.875rem; color: #334155;
        border-bottom: 1px solid #f1f5f9;
    }
    .table-modern tr:hover td { background-color: #f8fafc; }

    /* --- Utilities --- */
    .btn-custom-outline { 
        background: #fff; border: 1px solid #10b981; color: #10b981; 
        padding: 8px 20px; border-radius: 8px; font-weight: 600; transition: all 0.2s;
    }
    .btn-custom-outline:hover { background: #10b981; color: #fff; transform: translateY(-2px); }
</style>
@endsection

@section('content')
<div class="container-fluid py-4">

    {{-- HEADER --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="mb-1 fw-bold text-dark">
                <i data-feather="box" class="text-primary me-2"></i> Stock Report
            </h4>
            <p class="text-muted small mb-0">Live inventory status and valuation.</p>
        </div>
        <div>
            <a href="{{ route('admin.reports.stock',['export'=>'csv']) }}" class="btn btn-custom-outline shadow-sm">
                <i data-feather="download" class="me-1" style="width:16px;"></i> Export CSV
            </a>
        </div>
    </div>

    {{-- STATS OVERVIEW --}}
    <div class="row g-4 mb-4">
        {{-- Total Products --}}
        <div class="col-md-4">
            <div class="stat-card">
                <div>
                    <div class="stat-title">Total Items</div>
                    <h3 class="stat-value">{{ $products->total() }}</h3> {{-- Use total() for pagination count --}}
                </div>
                <div class="stat-icon-box bg-light-primary">
                    <i data-feather="layers"></i>
                </div>
            </div>
        </div>

        {{-- Total Stock Qty --}}
        <div class="col-md-4">
            <div class="stat-card">
                <div>
                    <div class="stat-title">Stock Quantity</div>
                    <h3 class="stat-value">{{ number_format($totalStockQty) }}</h3>
                </div>
                <div class="stat-icon-box bg-light-info">
                    <i data-feather="package"></i>
                </div>
            </div>
        </div>

        {{-- Total Stock Value --}}
        <div class="col-md-4">
            <div class="stat-card">
                <div>
                    <div class="stat-title">Inventory Value</div>
                    <h3 class="stat-value">৳{{ number_format($totalStockValue, 2) }}</h3>
                </div>
                <div class="stat-icon-box bg-light-success">
                    <span class="fw-bold">৳</span>
                </div>
            </div>
        </div>
    </div>

    {{-- STOCK TABLE --}}
    <div id="stock-table-wrapper">
        <div class="card card-modern">
            <div class="card-header border-bottom bg-white py-3">
                <h5 class="mb-0 fw-bold text-dark">Current Inventory List</h5>
            </div>
            
            <div class="table-responsive">
                <table class="table table-modern mb-0">
                    <thead>
                        <tr>
                            <th width="5%">#</th>
                            <th width="35%">Product Name</th>
                            <th width="15%" class="text-end">In Stock</th>
                            <th width="15%" class="text-end">Purchase Cost</th>
                            <th width="15%" class="text-end">Selling Price</th>
                            <th width="15%" class="text-end">Total Value</th>
                        </tr>
                    </thead>
                    <tbody>
                    @forelse($products as $p)
                        @php
                            $purchasePrice = $p->purchase_price ?? 0;
                            $salePrice     = $p->new_price ?? $p->old_price ?? 0;
                            $stock         = $p->stock ?? 0;
                            $stockValue    = $purchasePrice * $stock;
                        @endphp
                        <tr>
                            <td class="text-muted">{{ $loop->iteration + ($products->currentPage() - 1) * $products->perPage() }}</td>
                            <td>
                                <span class="fw-bold text-dark">{{ $p->name }}</span>
                            </td>
                            <td class="text-end">
                                <span class="badge {{ $stock > 10 ? 'bg-success' : ($stock > 0 ? 'bg-warning' : 'bg-danger') }} bg-opacity-10 text-dark border px-2">
                                    {{ $stock }}
                                </span>
                            </td>
                            <td class="text-end text-muted">৳{{ number_format($purchasePrice, 2) }}</td>
                            <td class="text-end text-muted">৳{{ number_format($salePrice, 2) }}</td>
                            <td class="text-end fw-bold text-dark">৳{{ number_format($stockValue, 2) }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-5">
                                <div class="d-flex flex-column align-items-center">
                                    <img src="https://cdn-icons-png.flaticon.com/512/7486/7486744.png" width="50" class="opacity-25 mb-2">
                                    <p class="text-muted fw-bold mb-0">No products found</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Pagination --}}
            <div class="p-4 border-top d-flex justify-content-between align-items-center">
                <small class="text-muted">Showing {{ $products->firstItem() }} to {{ $products->lastItem() }} of {{ $products->total() }} entries</small>
                <div>{{ $products->links('pagination::bootstrap-4') }}</div>
            </div>
        </div>
    </div>

</div>
@endsection

@section('script')
<script>
    /* ===============================
       AJAX Pagination for Stock Report
       =============================== */
    document.addEventListener('click', function(e){
        let link = e.target.closest('.pagination a');
        if (!link) return;

        e.preventDefault();
        let url = link.getAttribute('href');

        // Add loading state
        let tableWrapper = document.getElementById('stock-table-wrapper');
        tableWrapper.style.opacity = '0.5';

        fetch(url, {
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        })
        .then(res => res.text())
        .then(html => {
            let temp = document.createElement('div');
            temp.innerHTML = html;
            let newContent = temp.querySelector('#stock-table-wrapper');
            
            if (newContent) {
                tableWrapper.innerHTML = newContent.innerHTML;
                tableWrapper.style.opacity = '1';
                // Re-initialize feather icons if needed
                if(typeof feather !== 'undefined') feather.replace();
            }
        })
        .catch(err => {
            console.error(err);
            tableWrapper.style.opacity = '1';
        });
    });
</script>
@endsection