@extends('backEnd.layouts.master')
@section('title','Pending Products')

@section('css')
<style>
    /* Professional Card */
    .card {
        border: none;
        box-shadow: 0 0 20px rgba(18, 38, 63, 0.03);
        border-radius: 12px;
        overflow: hidden;
    }
    .card-header {
        background-color: #fff;
        border-bottom: 1px solid #f1f5f7;
        padding: 20px 25px;
    }

    /* Table Styles */
    .table thead th {
        font-weight: 600;
        text-transform: uppercase;
        font-size: 11px;
        letter-spacing: 0.5px;
        color: #8391a2;
        background: #f9fbfd;
        border-bottom: 1px solid #eef2f7;
        padding: 12px 15px;
    }
    .table tbody td {
        vertical-align: middle;
        padding: 15px;
        border-bottom: 1px solid #f1f5f7;
        color: #313b5e;
    }
    .table-hover tbody tr:hover {
        background-color: #fafbfd;
    }

    /* Product & Vendor Identity */
    .product-box {
        display: flex;
        align-items: center;
        gap: 15px;
    }
    .product-img {
        width: 48px;
        height: 48px;
        border-radius: 8px;
        object-fit: cover;
        box-shadow: 0 2px 5px rgba(0,0,0,0.05);
        border: 1px solid #f1f5f7;
    }
    .product-info h6 {
        margin: 0;
        font-size: 14px;
        font-weight: 600;
        color: #343a40;
    }
    .product-info small {
        color: #98a6ad;
        font-size: 12px;
    }

    /* Soft Badges */
    .badge-soft-primary { background-color: rgba(114, 124, 245, 0.18); color: #727cf5; }
    .badge-soft-success { background-color: rgba(10, 207, 151, 0.18); color: #0acf97; }
    .badge-soft-warning { background-color: rgba(255, 188, 0, 0.18); color: #ffbc00; }
    .badge-soft-info { background-color: rgba(57, 175, 209, 0.18); color: #39afd1; }
    .badge-soft-secondary { background-color: rgba(108, 117, 125, 0.18); color: #6c757d; }
    .badge-pill { padding: 5px 10px; border-radius: 50rem; font-weight: 500; font-size: 11px; }

    /* Action Buttons */
    .btn-action-group {
        display: flex;
        gap: 5px;
    }
    .btn-approve {
        background-color: #e8f5e9;
        color: #2e7d32;
        border: none;
        padding: 5px 10px;
        border-radius: 4px;
        font-size: 12px;
        font-weight: 600;
        transition: 0.2s;
    }
    .btn-approve:hover { background-color: #2e7d32; color: #fff; }
    
    .btn-reject {
        background-color: #ffebee;
        color: #c62828;
        border: none;
        padding: 5px 10px;
        border-radius: 4px;
        font-size: 12px;
        font-weight: 600;
        transition: 0.2s;
    }
    .btn-reject:hover { background-color: #c62828; color: #fff; }

    .btn-icon {
        width: 30px; height: 30px;
        display: inline-flex; align-items: center; justify-content: center;
        border-radius: 50%; color: #6c757d; transition: all 0.2s;
        border: 1px solid transparent; background: transparent;
    }
    .btn-icon:hover { background-color: #eef2f7; color: #727cf5; }

    /* Search Input */
    .form-control-sm { border-radius: 6px; padding: 8px 12px; font-size: 13px; }
</style>
@endsection

@section('content')
<meta name="csrf-token" content="{{ csrf_token() }}">
<div class="container-fluid">
    
    <div class="row mb-3 mt-3">
        <div class="col-12 d-flex justify-content-between align-items-center">
            <h4 class="page-title mb-0" style="font-weight: 700; color: #2d3436;">Pending Approvals</h4>
            <a href="{{route('products.index')}}" class="btn btn-secondary rounded-pill shadow-sm px-4">
                <i class="fe-arrow-left me-1"></i> Back to Products
            </a>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                
                <div class="card-header bg-white d-flex justify-content-between align-items-center">
                    <h5 class="text-muted mb-0 text-uppercase font-size-13">Waiting for Approval</h5>
                    
                    <form class="d-flex" method="GET" action="{{ route('products.pending') }}">
                        <div class="input-group input-group-sm" style="width: 250px;">
                            <span class="input-group-text bg-light border-end-0"><i class="fe-search"></i></span>
                            <input type="text" name="keyword" class="form-control border-start-0 ps-0" placeholder="Search pending..." value="{{ request('keyword') }}">
                        </div>
                    </form>
                </div>

                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead>
                            <tr>
                                <th style="width: 50px;">SL</th>
                                <th style="width: 30%;">Product Details</th>
                                <th>Vendor Info</th>
                                <th>Category</th>
                                <th>Price</th>
                                <th>Stock</th>
                                <th>Status</th>
                                <th class="text-end" style="width: 200px;">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($data as $key=>$value)
                            <tr>
                                <td>{{ $data->firstItem() + $key }}</td>
                                
                                <td>
                                    <div class="product-box">
                                        <img src="{{ asset($value->image ? $value->image->image : 'storage/uploads/placeholder.png') }}" class="product-img" alt="Product">
                                        <div class="product-info">
                                            <h6 class="text-truncate" style="max-width: 250px;" title="{{$value->name}}">{{$value->name}}</h6>
                                            @php
                                                $isDigital = isset($value->is_digital) ? (bool)$value->is_digital : ($value->product_type === 'digital');
                                            @endphp
                                            <small class="text-muted">Type: 
                                                <span class="{{ $isDigital ? 'text-primary' : 'text-info' }}">
                                                    {{ $isDigital ? 'Digital' : 'Physical' }}
                                                </span>
                                            </small>
                                        </div>
                                    </div>
                                </td>

                                <td>
                                    @if($value->vendor)
                                        <div class="d-flex align-items-center">
                                            <div class="avatar-xs me-2">
                                                <span class="avatar-title rounded-circle bg-soft-info text-info font-size-12">
                                                    {{ substr($value->vendor->shop_name, 0, 1) }}
                                                </span>
                                            </div>
                                            <span class="font-size-13 fw-medium">{{ $value->vendor->shop_name }}</span>
                                        </div>
                                    @else
                                        <span class="badge badge-soft-secondary">Admin Product</span>
                                    @endif
                                </td>

                                <td>{{$value->category ? $value->category->name : 'N/A'}}</td>

                                <td class="fw-bold text-dark">৳{{ number_format($value->new_price, 2) }}</td>

                                <td>
                                    @if($value->stock > 0)
                                        <span class="badge badge-soft-success">{{$value->stock}}</span>
                                    @else
                                        <span class="badge badge-soft-danger">Out of Stock</span>
                                    @endif
                                </td>

                                <td>
                                    <span class="badge badge-pill badge-soft-warning">
                                        <i class="fe-clock me-1"></i> Pending
                                    </span>
                                </td>

                                <td class="text-end">
                                    <div class="d-flex justify-content-end align-items-center gap-2">
                                        {{-- Edit Link --}}
                                        <a href="{{route('products.edit',$value->id)}}" class="btn-icon" title="View Details">
                                            <i class="fe-eye"></i>
                                        </a>

                                        {{-- Approve Button --}}
                                        <form method="POST" action="{{ route('products.approve') }}" class="d-inline">
                                            @csrf
                                            <input type="hidden" name="id" value="{{ $value->id }}">
                                            <button type="submit" class="btn-approve" onclick="return confirm('Are you sure you want to approve this product?')">
                                                <i class="fe-check me-1"></i> Approve
                                            </button>
                                        </form>

                                        {{-- Reject Button (Trigger Modal) --}}
                                        <button type="button" class="btn-reject" data-bs-toggle="modal" data-bs-target="#rejectModal{{ $value->id }}">
                                            <i class="fe-x me-1"></i> Reject
                                        </button>
                                    </div>

                                    <div class="modal fade" id="rejectModal{{ $value->id }}" tabindex="-1" aria-hidden="true">
                                        <div class="modal-dialog modal-dialog-centered">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title text-danger"><i class="fe-alert-triangle me-2"></i>Reject Product</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                </div>
                                                <form method="POST" action="{{ route('products.reject') }}">
                                                    @csrf
                                                    <div class="modal-body text-start">
                                                        <input type="hidden" name="id" value="{{ $value->id }}">
                                                        <p class="mb-2">Are you sure you want to reject <strong>{{ $value->name }}</strong>?</p>
                                                        
                                                        <div class="form-group mt-3">
                                                            <label class="form-label small fw-bold">Rejection Reason (Optional)</label>
                                                            <textarea name="rejection_reason" class="form-control" rows="3" placeholder="Explain why the product is rejected..."></textarea>
                                                        </div>
                                                    </div>
                                                    <div class="modal-footer bg-light">
                                                        <button type="button" class="btn btn-sm btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                                        <button type="submit" class="btn btn-sm btn-danger">Confirm Rejection</button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                    </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="8" class="text-center py-5">
                                    <div class="text-center">
                                        <img src="{{ asset('public/backEnd/assets/images/no-data.png') }}" style="height: 80px; opacity: 0.6; margin-bottom: 15px;" alt="">
                                        <h5 class="text-muted">No pending approvals!</h5>
                                        <p class="text-muted mb-0">All products have been processed.</p>
                                    </div>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="card-footer bg-white border-top-0 py-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="text-muted small">
                            Showing {{ $data->firstItem() }} to {{ $data->lastItem() }} of {{ $data->total() }} results
                        </div>
                        <div class="custom-paginate">
                            {{$data->links('pagination::bootstrap-4')}}
                        </div>
                    </div>
                </div>

            </div> </div></div>
</div>
@endsection