@extends('frontEnd.layouts.master')
@section('title', $brand->name)

@push('css')
<link rel="stylesheet" href="{{ asset('public/frontEnd/css/jquery-ui.css') }}">
@endpush

@section('content')
<section class="product-section">
    <div class="container">

        {{-- 🔹 Breadcrumb + Sorting --}}
        <div class="sorting-section">
            <div class="row">
                <div class="col-sm-6">
                    <div class="category-breadcrumb d-flex align-items-center">
                        <a href="{{ route('home') }}">Home</a>
                        <span>/</span>
                        <strong>{{ $brand->name }}</strong>
                    </div>
                </div>

                <div class="col-sm-6">
                    <div class="row">
                        <div class="col-sm-6">
                            <div class="showing-data">
                                <span>
                                    Showing {{ $products->firstItem() }}-{{ $products->lastItem() }}
                                    of {{ $products->total() }} Results
                                </span>
                            </div>
                        </div>

                        <div class="col-sm-6">
                            <div class="page-sort">
                                <form class="sort-form">
                                    <select name="sort" class="form-control form-select sort">
                                        <option value="1" @selected(request('sort')==1)>Product: Latest</option>
                                        <option value="2" @selected(request('sort')==2)>Product: Oldest</option>
                                        <option value="3" @selected(request('sort')==3)>Price: High To Low</option>
                                        <option value="4" @selected(request('sort')==4)>Price: Low To High</option>
                                        <option value="5" @selected(request('sort')==5)>Name: A-Z</option>
                                        <option value="6" @selected(request('sort')==6)>Name: Z-A</option>
                                    </select>

                                    <input type="hidden" name="min_price" value="{{ request('min_price') }}">
                                    <input type="hidden" name="max_price" value="{{ request('max_price') }}">
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- 🔹 Product Grid --}}
        <div class="row">
            <div class="col-sm-12">
                <div class="category-product main_product_inner">

                    @foreach($products as $key => $value)
                    <div class="product_item wist_item wow zoomIn"
                         data-wow-duration="1.5s"
                         data-wow-delay="0.{{ $key }}s">

                        <div class="product_item_inner">

                            {{-- Discount badge --}}
                            @if($value->old_price)
                            <div class="sale-badge">
                                <div class="sale-badge-inner">
                                    <div class="sale-badge-box">
                                        <span class="sale-badge-text">
                                            @php
                                                $discount = (($value->old_price - $value->new_price) * 100) / $value->old_price;
                                            @endphp
                                            <p>{{ number_format($discount,0) }}%</p>
                                            ছাড়
                                        </span>
                                    </div>
                                </div>
                            </div>
                            @endif

                            {{-- Product image --}}
                            <div class="pro_img">
                                <a href="{{ route('product', $value->slug) }}">
                                    <img src="{{ asset($value->image ? $value->image->image : '') }}"
                                         alt="{{ $value->name }}">
                                </a>
                            </div>

                            {{-- Product name --}}
                            <div class="pro_des">
                                <div class="pro_name">
                                    <a href="{{ route('product', $value->slug) }}">
                                        {{ Str::limit($value->name, 35) }}
                                    </a>
                                </div>
                            </div>
                        </div>

                        {{-- Rating --}}
                        @php
                            $avg = $value->reviews->avg('ratting') ?? 0;
                            $full = floor($avg);
                        @endphp
                        @for($i=1; $i<=5; $i++)
                            <i class="{{ $i <= $full ? 'fas' : 'far' }} fa-star"></i>
                        @endfor

                        {{-- Price --}}
                        <div class="pro_price">
                            @if($value->old_price)
                                <del>৳ {{ $value->old_price }}</del>
                            @endif
                            ৳ {{ $value->new_price }}
                        </div>

                        {{-- 🔥 TWO BUTTONS --}}
                        <div class="pro_btn">

                            @if(!$value->prosizes->isEmpty() || !$value->procolors->isEmpty())
                                {{-- Variant product → details page --}}
                                <a href="{{ route('product', $value->slug) }}"
                                   class="order-btn-link">
                                    অর্ডার করুন
                                </a>

                                <a href="{{ route('product', $value->slug) }}"
                                   class="cart-icon-link">
                                    <i class="fa-solid fa-cart-shopping"></i>
                                </a>
                            @else
                                {{-- Simple product --}}
                                {{-- Order Now --}}
                                <form action="{{ route('cart.store') }}" method="POST">
                                    @csrf
                                    <input type="hidden" name="id" value="{{ $value->id }}">
                                    <input type="hidden" name="qty" value="1">
                                    <input type="hidden" name="order_now" value="1">

                                    <button type="submit" class="order-btn">
                                        অর্ডার করুন
                                    </button>
                                </form>

                                {{-- Add to Cart --}}
                                <form action="{{ route('cart.store') }}" method="POST">
                                    @csrf
                                    <input type="hidden" name="id" value="{{ $value->id }}">
                                    <input type="hidden" name="qty" value="1">

                                    <button type="submit" class="cart-icon-btn cart_store" data-id="{{ $value->id }}">
                                        <i class="fa-solid fa-cart-shopping"></i>
                                    </button>
                                </form>
                            @endif

                        </div>
                    </div>
                    @endforeach

                </div>
            </div>
        </div>

        {{-- 🔹 Pagination --}}
        <div class="row">
            <div class="col-sm-12">
                <div class="custom_paginate">
                    {{ $products->links('pagination::bootstrap-4') }}
                </div>
            </div>
        </div>

    </div>
</section>
@endsection

@push('script')
<script>
    $('.sort').change(function () {
        $('.sort-form').submit();
    });
</script>
@endpush
