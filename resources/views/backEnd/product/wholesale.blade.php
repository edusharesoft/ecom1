@extends('backEnd.layouts.master')
@section('title','Wholesale Products')
@section('content')
<meta name="csrf-token" content="{{ csrf_token() }}">

<style>
    /* কাস্টম আধুনিক স্টাইল */
    .card { border: none; box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075); border-radius: 0.75rem; }
    .table thead { background-color: #f8f9fa; }
    .table thead th { border-top: none; text-transform: uppercase; font-size: 0.75rem; letter-spacing: 0.05em; color: #6c757d; font-weight: 700; padding: 12px 15px; }
    .table tbody td { vertical-align: middle; padding: 12px 15px; border-color: #f1f3f5; }
    
    /* ইমেজ স্টাইল */
    .product-img { border-radius: 8px; object-fit: cover; border: 1px solid #ebedf2; transition: transform 0.2s ease; }
    .product-img:hover { transform: scale(1.1); }

    /* বাটন ও ব্যাজ স্টাইল */
    .btn-action { width: 32px; height: 32px; display: inline-flex; align-items: center; justify-content: center; border-radius: 6px; transition: 0.3s; border: none; }
    .btn-edit { background: #e3f2fd; color: #2196f3; }
    .btn-edit:hover { background: #2196f3; color: #fff; }
    .btn-delete { background: #ffebee; color: #f44336; }
    .btn-delete:hover { background: #f44336; color: #fff; }
    .btn-status-toggle { background: #f1f3f5; color: #495057; }
    .btn-status-toggle:hover { background: #dee2e6; }
    .btn-status-active { background: #e8f5e9; color: #2e7d32; }
    .btn-status-active:hover { background: #2e7d32; color: #fff; }

    /* সফট ব্যাজ কালার */
    .badge-soft-primary { background-color: #e1f5fe; color: #039be5; }
    .badge-soft-success { background-color: #e8f5e9; color: #2e7d32; }
    .badge-soft-warning { background-color: #fff3e0; color: #ef6c00; }
    .badge-soft-danger { background-color: #ffebee; color: #c62828; }
    .badge-soft-info { background-color: #e0f7fa; color: #00838f; }
    .badge-soft-secondary { background-color: #f1f3f5; color: #495057; }

    .action2-btn { list-style: none; padding: 0; margin: 0; display: flex; gap: 8px; flex-wrap: wrap; }
    
    /* নাম এবং টেক্সট স্টাইল */
    .product-title { font-size: 14px; font-weight: 600; color: #343a40; margin: 0; }
    .text-small { font-size: 11px; }
</style>

<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-flex align-items-center justify-content-between py-3">
                <h4 class="page-title mb-0">Wholesale Products</h4>
                <div class="page-title-right">
                    <a href="{{route('products.create')}}" class="btn btn-danger rounded-pill shadow-sm">
                        <i class="fe-plus me-1"></i> Add New Product
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    
                    <div class="row mb-3 align-items-center">
                        <div class="col-lg-8 col-md-7">
                            <ul class="action2-btn">
                                <li>
                                    <button data-url="{{ route('products.update_deals') }}" data-status="1" class="btn btn-sm btn-outline-success rounded-pill hotdeal_update">
                                        <i class="fe-thumbs-up me-1"></i> Set Deal
                                    </button>
                                </li>
                                <li>
                                    <button data-url="{{ route('products.update_deals') }}" data-status="0" class="btn btn-sm btn-outline-danger rounded-pill hotdeal_update">
                                        <i class="fe-thumbs-down me-1"></i> Remove Deal
                                    </button>
                                </li>
                                <div class="vr mx-1 d-none d-lg-block"></div>
                                <li>
                                    <button data-url="{{ route('products.update_status') }}" data-status="1" class="btn btn-sm btn-primary rounded-pill update_status">
                                        <i class="fe-check me-1"></i> Active Selected
                                    </button>
                                </li>
                                <li>
                                    <button data-url="{{ route('products.update_status') }}" data-status="0" class="btn btn-sm btn-light border rounded-pill update_status">
                                        <i class="fe-x me-1"></i> Inactive Selected
                                    </button>
                                </li>
                            </ul>
                        </div>

                        <div class="col-lg-4 col-md-5 mt-2 mt-md-0">
                            <form method="GET" action="{{ route('admin.products.wholesale') }}">
                                <div class="row g-2">
                                    <div class="col-12">
                                        <div class="input-group">
                                            <input type="text" name="keyword" class="form-control form-control-sm border-end-0" placeholder="Search by name..." value="{{ request('keyword') }}">
                                            <button class="btn btn-sm btn-info border-start-0 px-3" type="submit">
                                                <i class="fe-search"></i>
                                            </button>
                                        </div>
                                    </div>
                                    @if($categories && $categories->count() > 0)
                                    <div class="col-12">
                                        <select name="category_id" class="form-control form-control-sm" onchange="this.form.submit()">
                                            <option value="">All Categories</option>
                                            @foreach($categories as $category)
                                                <option value="{{ $category->id }}" {{ request('category_id') == $category->id ? 'selected' : '' }}>
                                                    {{ $category->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    @endif
                                    <div class="col-12">
                                        <select name="status" class="form-control form-control-sm" onchange="this.form.submit()">
                                            <option value="">All Status</option>
                                            <option value="1" {{ request('status') == '1' ? 'selected' : '' }}>Active</option>
                                            <option value="0" {{ request('status') == '0' ? 'selected' : '' }}>Inactive</option>
                                        </select>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead>
                                <tr>
                                    <th style="width: 40px;">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" id="checkAll">
                                        </div>
                                    </th>
                                    <th>Image</th>
                                    <th>Product Name</th>
                                    <th>Category</th>
                                    <th>Vendor</th>
                                    <th>Wholesale Tiers</th>
                                    <th>Price</th>
                                    <th>Stock</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($data as $product)
                                <tr>
                                    <td>
                                        <div class="form-check">
                                            <input class="form-check-input product-checkbox" type="checkbox" value="{{ $product->id }}" name="product_ids[]">
                                        </div>
                                    </td>
                                    <td>
                                        <img src="{{ asset($product->image ? $product->image->image : 'public/uploads/default/no-image.png') }}" 
                                             alt="{{ $product->name }}" 
                                             class="product-img" 
                                             style="width: 50px; height: 50px;">
                                    </td>
                                    <td>
                                        <p class="product-title mb-1">{{ Str::limit($product->name, 40) }}</p>
                                        <small class="text-muted text-small">SKU: {{ $product->sku ?? 'N/A' }}</small>
                                    </td>
                                    <td>
                                        <span class="badge badge-soft-info">{{ $product->category->name ?? 'N/A' }}</span>
                                    </td>
                                    <td>
                                        @if($product->vendor)
                                            <span class="badge badge-soft-primary">{{ $product->vendor->shop_name ?? 'Vendor #' . $product->vendor_id }}</span>
                                        @else
                                            <span class="badge badge-soft-secondary">Inhouse</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($product->wholesalePrices && $product->wholesalePrices->count() > 0)
                                            <span class="badge badge-soft-success">{{ $product->wholesalePrices->count() }} Tier(s)</span>
                                        @else
                                            <span class="badge badge-soft-warning">No Tiers</span>
                                        @endif
                                    </td>
                                    <td>
                                        <strong class="text-primary">৳{{ number_format($product->new_price, 2) }}</strong>
                                        @if($product->old_price && $product->old_price > $product->new_price)
                                            <br><small class="text-muted text-decoration-line-through">৳{{ number_format($product->old_price, 2) }}</small>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="badge {{ $product->stock > 0 ? 'badge-soft-success' : 'badge-soft-danger' }}">
                                            {{ $product->stock ?? 0 }}
                                        </span>
                                    </td>
                                    <td>
                                        <span class="badge {{ $product->status == 1 ? 'badge-soft-success' : 'badge-soft-danger' }}">
                                            {{ $product->status == 1 ? 'Active' : 'Inactive' }}
                                        </span>
                                    </td>
                                    <td>
                                        <div class="d-flex gap-1">
                                            <a href="{{ route('products.show', $product->id) }}" class="btn btn-sm btn-action btn-edit" title="View">
                                                <i class="fe-eye"></i>
                                            </a>
                                            <a href="{{ route('products.edit', $product->id) }}" class="btn btn-sm btn-action btn-edit" title="Edit">
                                                <i class="fe-edit"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="10" class="text-center py-4">
                                        <div class="text-muted">
                                            <i class="fe-package" style="font-size: 48px; opacity: 0.3;"></i>
                                            <p class="mt-2 mb-0">No wholesale products found</p>
                                        </div>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    @if($data->hasPages())
                    <div class="d-flex justify-content-center mt-3">
                        {{ $data->links() }}
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    // Check All functionality
    document.getElementById('checkAll')?.addEventListener('change', function() {
        const checkboxes = document.querySelectorAll('.product-checkbox');
        checkboxes.forEach(checkbox => {
            checkbox.checked = this.checked;
        });
    });

    // Update Status functionality
    document.querySelectorAll('.update_status').forEach(button => {
        button.addEventListener('click', function() {
            const selectedIds = Array.from(document.querySelectorAll('.product-checkbox:checked')).map(cb => cb.value);
            if (selectedIds.length === 0) {
                alert('Please select at least one product');
                return;
            }

            const url = this.getAttribute('data-url');
            const status = this.getAttribute('data-status');

            if (confirm(`Are you sure you want to ${status == 1 ? 'activate' : 'deactivate'} selected products?`)) {
                fetch(url, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({
                        product_ids: selectedIds,
                        status: status
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        location.reload();
                    } else {
                        alert(data.message || 'Something went wrong');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Something went wrong');
                });
            }
        });
    });

    // Hot Deal Update functionality
    document.querySelectorAll('.hotdeal_update').forEach(button => {
        button.addEventListener('click', function() {
            const selectedIds = Array.from(document.querySelectorAll('.product-checkbox:checked')).map(cb => cb.value);
            if (selectedIds.length === 0) {
                alert('Please select at least one product');
                return;
            }

            const url = this.getAttribute('data-url');
            const status = this.getAttribute('data-status');

            if (confirm(`Are you sure you want to ${status == 1 ? 'set' : 'remove'} hot deal for selected products?`)) {
                fetch(url, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({
                        product_ids: selectedIds,
                        status: status
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        location.reload();
                    } else {
                        alert(data.message || 'Something went wrong');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Something went wrong');
                });
            }
        });
    });
</script>

@endsection
