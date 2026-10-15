@extends('frontEnd.layouts.master') 
@section('title',$keyword) 
@push('css')
<link rel="stylesheet" href="{{asset('public/frontEnd/css/jquery-ui.css')}}" />
@endpush 

@section('content')
<section class="product-section">
    <div class="container">
        <div class="sorting-section">
            <div class="row">
                <div class="col-sm-6">
                    <div class="category-breadcrumb d-flex align-items-center">
                        <a href="{{ route('home') }}">Home</a>
                        <span>/</span>
                        <strong>{{ $keyword }}</strong>
                    </div>
                </div>
                <div class="col-sm-6">
                    <div class="row">
                        <div class="col-sm-6">
                            <div class="showing-data">
                                <span>Showing {{ $products->firstItem() }}-{{ $products->lastItem() }} of {{ $products->total() }} Results</span>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="mobile-filter-toggle">
                                <i class="fa fa-list-ul"></i><span>filter</span>
                            </div>
                            <div class="page-sort">
                                <form action="" class="sort-form">
                                    <select name="sort" class="form-control form-select sort">
                                        <option value="1" @if(request()->get('sort')==1)selected @endif>Product: Latest</option>
                                        <option value="2" @if(request()->get('sort')==2)selected @endif>Product: Oldest</option>
                                        <option value="3" @if(request()->get('sort')==3)selected @endif>Price: High To Low</option>
                                        <option value="4" @if(request()->get('sort')==4)selected @endif>Price: Low To High</option>
                                        <option value="5" @if(request()->get('sort')==5)selected @endif>Name: A-Z</option>
                                        <option value="6" @if(request()->get('sort')==6)selected @endif>Name: Z-A</option>
                                    </select>
                                    <input type="hidden" name="min_price" value="{{request()->get('min_price')}}" />
                                    <input type="hidden" name="max_price" value="{{request()->get('max_price')}}" />
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="row">
            <div class="col-sm-12">
                <div class="category-product main_product_inner">
                    @foreach($products as $key=>$value)
                    <div class="product_item wist_item wow zoomIn" data-wow-duration="1.5s"
                            data-wow-delay="0.{{ $key }}s">
                            <div class="product_item_inner">
                                @if($value->old_price)
                                <div class="sale-badge">
                                    <div class="sale-badge-inner">
                                        <div class="sale-badge-box">
                                            <span class="sale-badge-text">
                                                <p>
                                                    @php 
                                                        $discount=(((($value->old_price)-($value->new_price))*100) / ($value->old_price)) 
                                                    @endphp 
                                                    {{ number_format($discount, 0) }}%
                                                </p>
                                                ছাড়
                                            </span>
                                        </div>
                                    </div>
                                </div>
                                @endif

                                <div class="pro_img">
                                    <a href="{{ route('product', $value->slug) }}">
                                        <img src="{{ asset($value->image ? $value->image->image : '') }}"
                                            alt="{{ $value->name }}" />
                                    </a>
                                </div>

                                <div class="pro_des">
                                    <div class="pro_name">
                                        <a href="{{ route('product', $value->slug) }}">
                                            {{ Str::limit($value->name, 35) }}
                                        </a>
                                    </div>
                                </div>
                            </div>

                            @php
                                $averageRating = $value->reviews->avg('ratting'); 
                                $filledStars = floor($averageRating);
                                $hasHalfStar = $averageRating - $filledStars >= 0.5;
                                $emptyStars = 5 - $filledStars - ($hasHalfStar ? 1 : 0);
                            @endphp

                            @if ($averageRating >= 0 && $averageRating <= 5)
                                @for ($i = 0; $i < $filledStars; $i++)
                                    <i class="fas fa-star"></i>
                                @endfor
                                @if ($hasHalfStar)
                                    <i class="fas fa-star-half-alt"></i>
                                @endif
                                @for ($i = 0; $i < $emptyStars; $i++)
                                    <i class="far fa-star"></i>
                                @endfor
                            @else
                                <span>Invalid rating range</span>
                            @endif

                            <div class="pro_price">
                                <p>
                                    <del>৳ {{ $value->old_price }}</del>
                                    ৳ {{ $value->new_price }}
                                </p>
                            </div>

                            {{-- 🔥 UPDATED BUTTON SECTION (NOTHING REMOVED) --}}
                            @if (!$value->prosizes->isEmpty() || !$value->procolors->isEmpty())
                                <div class="pro_btn">
                                    <a href="{{ route('product', $value->slug) }}" class="addcartbutton">
                                        <span>অর্ডার করুন</span>
                                    </a>
                                    <a href="{{ route('product', $value->slug) }}" class="cart-icon-btn">
                                        <i class="fa-solid fa-cart-shopping"></i>
                                    </a>
                                </div>
                            @else
                                <div class="pro_btn">
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
                                </div>
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-sm-12">
                <div class="custom_paginate">
                    {{$products->links('pagination::bootstrap-4')}}
                </div>
            </div>
        </div>
    </div>
</section>
@endsection

@push('script')
<script>
    $(".sort").change(function(){
       $('#loading').show();
       $(".sort-form").submit();
    })
</script>
@endpush
