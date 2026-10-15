@extends('backEnd.layouts.master')
@section('title','Product Details')

@section('css')
<style>
    /* Professional Card & Layout */
    .card {
        border: none;
        box-shadow: 0 0 20px rgba(18, 38, 63, 0.03);
        border-radius: 10px;
        overflow: hidden;
    }
    .card-header {
        background: #fff;
        border-bottom: 1px solid #f1f5f7;
        padding: 15px 20px;
        font-weight: 700;
        color: #495057;
    }

    /* Product Image Section */
    .pro-img-details {
        border-radius: 10px;
        overflow: hidden;
        border: 1px solid #f1f5f7;
        background: #fff;
        text-align: center;
        padding: 20px;
        margin-bottom: 15px;
    }
    .pro-img-details img {
        max-width: 100%;
        height: auto;
        max-height: 400px;
    }
    .pro-thumb-list {
        display: flex;
        flex-wrap: wrap;
        gap: 10px;
    }
    .pro-thumb-img {
        width: 70px;
        height: 70px;
        border-radius: 8px;
        border: 1px solid #dee2e6;
        padding: 3px;
        cursor: pointer;
        object-fit: cover;
    }
    .pro-thumb-img:hover {
        border-color: #727cf5;
    }

    /* Typography & Badges */
    .pro-title {
        color: #343a40;
        font-weight: 700;
        font-size: 22px;
        margin-bottom: 10px;
    }
    .price-tag {
        font-size: 24px;
        font-weight: 700;
        color: #727cf5;
    }
    .old-price {
        text-decoration: line-through;
        color: #98a6ad;
        font-size: 16px;
        margin-left: 10px;
    }
    
    /* Soft Badges */
    .badge-soft-primary { background-color: rgba(114, 124, 245, 0.1); color: #727cf5; }
    .badge-soft-success { background-color: rgba(10, 207, 151, 0.1); color: #0acf97; }
    .badge-soft-danger { background-color: rgba(250, 92, 124, 0.1); color: #fa5c7c; }
    .badge-soft-warning { background-color: rgba(255, 188, 0, 0.1); color: #ffbc00; }
    .badge-soft-info { background-color: rgba(57, 175, 209, 0.1); color: #39afd1; }
    
    /* Table Styling */
    .table-nowrap td, .table-nowrap th {
        vertical-align: middle;
        padding: 12px 15px;
    }
    .table-nowrap th {
        color: #6c757d;
        font-weight: 600;
        width: 30%;
        background-color: #f9fbfd;
    }
    
    /* Stock Progress */
    .stock-box {
        background: #f8f9fa;
        padding: 15px;
        border-radius: 8px;
        border: 1px solid #eff2f7;
    }
</style>
@endsection

@section('content')
<div class="container-fluid">
    
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-flex align-items-center justify-content-between py-3">
                <h4 class="page-title mb-0">Product Details</h4>
                <div class="page-title-right gap-2 d-flex">
                    <form action="{{ route('admin.facebook_page.post_product', $product->id) }}" method="POST" class="d-inline">
                        @csrf
                        <button type="submit" class="btn btn-primary rounded-pill shadow-sm" title="Post to Facebook Page">
                            <i class="fe-facebook me-1"></i> Post to Facebook
                        </button>
                    </form>
                    <a href="{{route('products.edit', $product->id)}}" class="btn btn-info rounded-pill shadow-sm">
                        <i class="fe-edit me-1"></i> Edit Product
                    </a>
                    <a href="{{route('inhouse.products.index')}}" class="btn btn-secondary rounded-pill shadow-sm">
                        <i class="fe-arrow-left me-1"></i> Back to List
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-5">
            <div class="card">
                <div class="card-body">
                    <div class="pro-img-details shadow-sm">
                        <img src="{{ asset($product->image ? $product->image->image : 'storage/uploads/placeholder.png') }}" 
                             alt="{{ $product->name }}" id="main_image">
                    </div>
                    
                    @if($product->images->count() > 0)
                        <div class="pro-thumb-list">
                            {{-- Main Image Thumbnail --}}
                            <img src="{{ asset($product->image ? $product->image->image : 'storage/uploads/placeholder.png') }}" 
                                 class="pro-thumb-img" onclick="changeImage(this.src)">
                            
                            {{-- Gallery Images --}}
                            @foreach($product->images as $img)
                                <img src="{{ asset($img->image) }}" class="pro-thumb-img" onclick="changeImage(this.src)">
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>

            <div class="card mt-3">
                <div class="card-body">
                    <div class="d-flex align-items-center mb-3">
                        <div class="flex-grow-1">
                            <h5 class="mt-0 mb-1">Brand Information</h5>
                            <p class="text-muted mb-0">
                                <i class="fe-box me-1"></i> {{ $product->brand ? $product->brand->name : 'No Brand' }}
                            </p>
                        </div>
                    </div>
                    <hr>
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <h5 class="mt-0 mb-1">Product Type</h5>
                            <p class="text-muted mb-0">
                                @if($product->is_digital)
                                    <span class="badge badge-soft-primary"><i class="fe-file-text me-1"></i>Digital Product</span>
                                @else
                                    <span class="badge badge-soft-info"><i class="fe-package me-1"></i>Physical Product</span>
                                @endif
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-7">
            <div class="card">
                <div class="card-body">
                    <h3 class="pro-title">{{ $product->name }}</h3>
                    
                    <div class="mb-3">
                        @if($product->status == 1)
                            <span class="badge badge-soft-success px-2 py-1">Active</span>
                        @else
                            <span class="badge badge-soft-danger px-2 py-1">Inactive</span>
                        @endif

                        @if($product->topsale == 1)
                            <span class="badge badge-soft-warning px-2 py-1 ms-1"><i class="fe-zap"></i> Hot Deal</span>
                        @endif

                        @if($product->feature_product == 1)
                            <span class="badge badge-soft-primary px-2 py-1 ms-1"><i class="fe-star"></i> Featured</span>
                        @endif
                    </div>

                    <div class="mt-3">
                        <span class="price-tag">৳{{ number_format($product->new_price, 2) }}</span>
                        @if($product->old_price)
                            <span class="old-price">৳{{ number_format($product->old_price, 2) }}</span>
                            <small class="text-danger ms-1">
                                ({{ round((($product->old_price - $product->new_price) / $product->old_price) * 100) }}% OFF)
                            </small>
                        @endif
                    </div>

                    <div class="stock-box mt-3 mb-4">
                        <div class="row align-items-center">
                            <div class="col-md-6">
                                <p class="mb-1 text-muted fw-bold">Current Stock</p>
                                <h4 class="mb-0 {{ $product->stock <= 5 ? 'text-danger' : 'text-success' }}">
                                    {{ $product->stock }} <small class="font-size-14 text-muted">{{ $product->pro_unit }}</small>
                                </h4>
                            </div>
                            <div class="col-md-6 border-start">
                                <p class="mb-1 text-muted fw-bold">Purchase Price</p>
                                <h5 class="mb-0 text-dark">৳{{ number_format($product->purchase_price, 2) }}</h5>
                            </div>
                        </div>
                    </div>

                    <h5 class="font-size-15 mb-3 text-uppercase text-muted">Specifications</h5>
                    <div class="table-responsive">
                        <table class="table table-nowrap table-bordered mb-0">
                            <tbody>
                                <tr>
                                    <th>Product Code</th>
                                    <td>#{{ $product->product_code }}</td>
                                </tr>
                                <tr>
                                    <th>Category</th>
                                    <td>
                                        {{ $product->category ? $product->category->name : 'N/A' }}
                                        @if($product->subcategory)
                                            <i class="fe-chevron-right mx-1 font-size-10"></i> {{ $product->subcategory->subcategoryName }}
                                        @endif
                                        @if($product->childcategory)
                                            <i class="fe-chevron-right mx-1 font-size-10"></i> {{ $product->childcategory->childcategoryName }}
                                        @endif
                                    </td>
                                </tr>
                                @if($product->is_digital)
                                <tr>
                                    <th>Digital File</th>
                                    <td>
                                        @if($product->digital_file)
                                            <a href="#" class="text-primary"><i class="fe-download me-1"></i> Download File</a>
                                        @else
                                            <span class="text-muted">No file uploaded</span>
                                        @endif
                                    </td>
                                </tr>
                                @endif
                                <tr>
                                    <th>Short Note</th>
                                    <td>{{ $product->note ?? 'N/A' }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-4">
                        <h5 class="font-size-15 mb-3 text-uppercase text-muted">Description</h5>
                        <div class="p-3 border rounded bg-light">
                            @if($product->description)
                                {!! $product->description !!}
                            @else
                                <span class="text-muted font-italic">No description available.</span>
                            @endif
                        </div>
                    </div>

                    @if($product->pro_video)
                    <div class="mt-4">
                        <h5 class="font-size-15 mb-2">Product Video</h5>
                        <a href="{{ $product->pro_video }}" target="_blank" class="btn btn-outline-danger btn-sm">
                            <i class="fe-youtube me-1"></i> Watch Video
                        </a>
                    </div>
                    @endif

                </div>
            </div>
        </div>
    </div>
</div>

<script>
    // Simple script to change main image on click
    function changeImage(src) {
        document.getElementById('main_image').src = src;
    }
</script>
@endsection