@extends('vendor.layouts.app')

@section('title', 'My Products')
@section('page-title', 'My Products')

@push('styles')
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">
<style>
    :root {
        --primary-color: #4e73df;
        --secondary-color: #858796;
        --success-color: #1cc88a;
        --info-color: #36b9cc;
        --warning-color: #f6c23e;
        --danger-color: #e74a3b;
        --text-color: #5a5c69;
    }

    body {
        font-family: 'Poppins', sans-serif;
        color: var(--text-color);
    }

    /* Card Styling */
    .dashboard-card {
        background: #fff;
        border-radius: 15px;
        border: none;
        box-shadow: 0 0.15rem 1.75rem 0 rgba(58,59,69,0.05);
    }

    /* Product Image */
    .product-img-container {
        width: 50px;
        height: 50px;
        border-radius: 10px;
        overflow: hidden;
        background: #f8f9fc;
        border: 1px solid #eaecf4;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    .backend-image {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    /* Table Styling */
    .table thead th {
        background-color: transparent;
        border-bottom: 2px solid #eaecf4;
        color: var(--primary-color);
        font-weight: 600;
        text-transform: uppercase;
        font-size: 0.80rem;
        letter-spacing: 0.5px;
        padding: 15px 10px;
    }
    
    .table td {
        vertical-align: middle;
        padding: 15px 10px;
        font-size: 0.9rem;
        border-bottom: 1px solid #f8f9fc;
    }

    /* Soft Badges */
    .badge-soft-success { background-color: rgba(28, 200, 138, 0.1); color: var(--success-color); }
    .badge-soft-danger { background-color: rgba(231, 74, 59, 0.1); color: var(--danger-color); }
    .badge-soft-warning { background-color: rgba(246, 194, 62, 0.1); color: var(--warning-color); }
    .badge-soft-info { background-color: rgba(54, 185, 204, 0.1); color: var(--info-color); }
    
    .badge {
        padding: 6px 12px;
        border-radius: 30px;
        font-weight: 500;
        font-size: 0.75rem;
    }

    /* Search Input */
    .search-input {
        border-radius: 50px;
        padding: 10px 20px;
        border: 1px solid #eaecf4;
        background-color: #f8f9fc;
    }
    .search-input:focus {
        background-color: #fff;
        box-shadow: 0 0 0 0.2rem rgba(78, 115, 223, 0.1);
        border-color: var(--primary-color);
    }

    /* Buttons */
    .btn-primary-soft {
        background-color: rgba(78, 115, 223, 0.1);
        color: var(--primary-color);
        border: none;
        font-weight: 500;
    }
    .btn-primary-soft:hover {
        background-color: var(--primary-color);
        color: #fff;
    }
    
    .action-btn {
        width: 32px;
        height: 32px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border-radius: 50%;
        transition: all 0.2s;
    }
    .btn-edit { background: rgba(78, 115, 223, 0.1); color: var(--primary-color); }
    .btn-edit:hover { background: var(--primary-color); color: #fff; }
    
    .btn-delete { background: rgba(231, 74, 59, 0.1); color: var(--danger-color); border: none; }
    .btn-delete:hover { background: var(--danger-color); color: #fff; }

</style>
@endpush

@section('content')

<div class="d-flex flex-column flex-md-row justify-content-between align-items-center mb-4">
    <div>
        <h4 class="fw-bold text-dark mb-1">Product Management</h4>
        <p class="text-secondary small mb-0">Manage your product inventory and stock</p>
    </div>
    <div class="mt-3 mt-md-0">
        @if($vendor->verification_status == 'approved')
            <a href="{{ route('vendor.products.create') }}" class="btn btn-primary rounded-pill px-4 shadow-sm">
                <i class="fas fa-plus me-2"></i>Add New Product
            </a>
        @else
            <button disabled class="btn btn-secondary rounded-pill px-4" title="Please verify your account first">
                <i class="fas fa-lock me-2"></i>Add Product
            </button>
        @endif
    </div>
</div>

@if($vendor->verification_status != 'approved')
<div class="dashboard-card p-4 mb-4 border-start border-4 {{ $vendor->verification_status == 'rejected' ? 'border-danger' : 'border-warning' }}">
    <div class="d-flex align-items-center">
        <div class="me-3">
            <div class="avatar-md bg-light-warning rounded-circle d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
                <i class="fas fa-exclamation-triangle fa-lg {{ $vendor->verification_status == 'rejected' ? 'text-danger' : 'text-warning' }}"></i>
            </div>
        </div>
        <div class="flex-grow-1">
            <h6 class="fw-bold mb-1">
                @if($vendor->verification_status == 'pending')
                    Account Verification Pending
                @elseif($vendor->verification_status == 'rejected')
                    Account Verification Rejected
                @else
                    Action Required: Verify Account
                @endif
            </h6>
            <p class="mb-0 text-muted small">
                @if($vendor->verification_status == 'pending')
                    Your documents are under review. Product upload is restricted.
                @elseif($vendor->verification_status == 'rejected')
                    Verification rejected. Please re-upload valid documents.
                @else
                    Upload your ID card and photo to unlock product uploading features.
                @endif
            </p>
        </div>
        <a href="{{ route('vendor.verification.index') }}" class="btn btn-sm btn-outline-dark rounded-pill ms-3">
            Verify Now
        </a>
    </div>
</div>
@endif

<div class="dashboard-card p-4 mb-4">
    <form method="GET" action="{{ route('vendor.products.index') }}">
        <div class="row g-3 align-items-center">
            <div class="col-md-8">
                <div class="position-relative">
                    <i class="fas fa-search position-absolute text-muted" style="left: 15px; top: 50%; transform: translateY(-50%);"></i>
                    <input type="text" name="keyword" class="form-control search-input ps-5" placeholder="Search by product name, category..." value="{{ request('keyword') }}">
                </div>
            </div>
            <div class="col-md-4 text-end">
                <button type="submit" class="btn btn-primary rounded-pill px-4">Search</button>
                @if(request('keyword'))
                    <a href="{{ route('vendor.products.index') }}" class="btn btn-light rounded-pill px-4 ms-2 text-danger">
                        <i class="fas fa-times"></i>
                    </a>
                @endif
            </div>
        </div>
    </form>
</div>

<div class="dashboard-card p-0 overflow-hidden">
    <div class="p-4 border-bottom border-light">
        <h6 class="fw-bold m-0 text-primary">All Products List</h6>
    </div>
    
    <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead class="bg-light">
                <tr>
                    <th class="ps-4">Product</th>
                    <th>Category</th>
                    <th>Type</th>
                    <th>Price</th>
                    <th>Stock</th>
                    <th>Status</th>
                    <th>Approval</th>
                    <th class="text-end pe-4">Action</th>
                </tr>
            </thead>
            <tbody>
                @forelse($data as $key => $value)
                <tr>
                    <td class="ps-4">
                        <div class="d-flex align-items-center">
                            <div class="product-img-container me-3">
                                <img src="{{ asset($value->image ? $value->image->image : 'storage/uploads/placeholder.png') }}" 
                                     class="backend-image" alt="Product">
                            </div>
                            <div>
                                <h6 class="mb-0 fw-semibold text-dark" style="font-size: 0.9rem;">{{ Str::limit($value->name, 30) }}</h6>
                                <small class="text-muted">ID: #{{ $value->id }}</small>
                            </div>
                        </div>
                    </td>

                    <td>{{ $value->category ? $value->category->name : '-' }}</td>

                    <td>
                        @php $isDigital = isset($value->is_digital) ? (bool) $value->is_digital : false; @endphp
                        @if($isDigital)
                            <span class="badge badge-soft-info"><i class="fas fa-cloud-download-alt me-1"></i> Digital</span>
                        @else
                            <span class="badge badge-soft-success"><i class="fas fa-box me-1"></i> Physical</span>
                        @endif
                    </td>

                    <td class="fw-bold text-dark">৳{{ number_format($value->new_price, 2) }}</td>

                    <td>
                        @if($value->stock > 0)
                            <span class="text-dark fw-medium">{{ $value->stock }}</span>
                        @else
                            <span class="text-danger fw-bold">Out of Stock</span>
                        @endif
                    </td>

                    <td>
                        @if($value->status == 1)
                            <span class="badge badge-soft-success">Active</span>
                        @else
                            <span class="badge badge-soft-danger">Inactive</span>
                        @endif
                    </td>

                    <td>
                        @if($value->approval_status == 'approved')
                            <span class="badge badge-soft-success">Approved</span>
                        @elseif($value->approval_status == 'pending')
                            <span class="badge badge-soft-warning">Pending</span>
                        @elseif($value->approval_status == 'rejected')
                            <span class="badge badge-soft-danger">Rejected</span>
                        @endif
                    </td>

                    <td class="text-end pe-4">
                        <div class="d-inline-flex gap-2">
                            <a href="{{ route('vendor.products.edit', $value->id) }}" class="action-btn btn-edit" title="Edit">
                                <i class="fas fa-pen fa-xs"></i>
                            </a>
                            
                            <form method="post" action="{{ route('vendor.products.destroy') }}" class="d-inline delete-form">
                                @csrf
                                <input type="hidden" value="{{ $value->id }}" name="hidden_id">
                                <button type="submit" class="action-btn btn-delete" title="Delete">
                                    <i class="fas fa-trash-alt fa-xs"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" class="text-center py-5">
                        <div class="d-flex flex-column align-items-center">
                            <div class="mb-3 p-3 bg-light rounded-circle">
                                <i class="fas fa-box-open fa-2x text-muted"></i>
                            </div>
                            <h6 class="text-muted">No products found</h6>
                            <a href="{{ route('vendor.products.create') }}" class="btn btn-sm btn-primary-soft mt-2">Create New Product</a>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    
@if($data->hasPages())
<div class="pagination-wrapper p-4 border-top border-light d-flex justify-content-center bg-white rounded-bottom">
    <ul class="premium-pagination mb-0">
        
        {{-- Previous Page Link --}}
        @if ($data->onFirstPage())
            <li class="page-item disabled">
                <span class="page-link icon-box"><i class="fas fa-chevron-left"></i></span>
            </li>
        @else
            <li class="page-item">
                <a class="page-link icon-box" href="{{ $data->previousPageUrl() }}" rel="prev">
                    <i class="fas fa-chevron-left"></i>
                </a>
            </li>
        @endif

        {{-- Pagination Elements --}}
        @foreach ($data->links()->elements as $element)
            {{-- "Three Dots" Separator --}}
            @if (is_string($element))
                <li class="page-item disabled"><span class="page-link dots">{{ $element }}</span></li>
            @endif

            {{-- Array Of Links --}}
            @if (is_array($element))
                @foreach ($element as $page => $url)
                    @if ($page == $data->currentPage())
                        <li class="page-item active"><span class="page-link">{{ $page }}</span></li>
                    @else
                        <li class="page-item"><a class="page-link" href="{{ $url }}">{{ $page }}</a></li>
                    @endif
                @endforeach
            @endif
        @endforeach

        {{-- Next Page Link --}}
        @if ($data->hasMorePages())
            <li class="page-item">
                <a class="page-link icon-box" href="{{ $data->nextPageUrl() }}" rel="next">
                    <i class="fas fa-chevron-right"></i>
                </a>
            </li>
        @else
            <li class="page-item disabled">
                <span class="page-link icon-box"><i class="fas fa-chevron-right"></i></span>
            </li>
        @endif
    </ul>
</div>

<style>
    .premium-pagination {
        display: flex;
        padding-left: 0;
        list-style: none;
        gap: 6px;
        align-items: center;
    }
    
    .premium-pagination .page-link {
        border: none;
        width: 36px;
        height: 36px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 10px;
        font-weight: 700;
        color: #64748b;
        background: #f8fafc;
        transition: all 0.2s ease;
        text-decoration: none;
        font-size: 0.9rem;
        cursor: pointer;
    }

    .premium-pagination .page-link:hover {
        background: #eef2ff;
        color: #4f46e5;
        transform: translateY(-2px);
    }

    .premium-pagination .page-item.active .page-link {
        background: linear-gradient(135deg, #4f46e5 0%, #3730a3 100%);
        color: white;
        box-shadow: 0 4px 10px rgba(79, 70, 229, 0.3);
    }

    .premium-pagination .page-item.disabled .page-link {
        background: #fff;
        color: #cbd5e1;
        cursor: not-allowed;
    }
    
    .premium-pagination .dots { background: transparent; cursor: default; }
</style>
@endif
</div>
@endsection

@push('scripts')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    $(document).ready(function() {
        // Confirmation for delete
        $('.delete-form').on('submit', function(e) {
            if (!confirm('Are you sure you want to delete this product?')) {
                e.preventDefault();
            }
        });
    });
</script>
@endpush