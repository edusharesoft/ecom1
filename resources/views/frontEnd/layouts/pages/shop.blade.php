@extends('frontEnd.layouts.master')
@section('title', 'All Products')

@php
    $sliderStep = max(1, min(500, ceil(($max_price - $min_price) / 80)));
@endphp

@push('seo')
    <meta name="robots" content="index, follow" />
    <meta name="description" content="{{ optional($generalsetting)->tagline ?? 'Browse all approved products in one place.' }}" />
@endpush

@push('css')
    <link rel="stylesheet" href="{{ asset('public/frontEnd/css/jquery-ui.css') }}" />
@endpush

@section('content')
    <section class="product-section">
        <div class="container">
            <div class="sorting-section">
                <div class="row">
                    <div class="col-sm-6">
                        <div class="category-breadcrumb d-flex align-items-center flex-wrap gap-2">
                            <a href="{{ route('home') }}">Home</a>
                            <span>/</span>
                            <strong>All Products</strong>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="row">
                            <div class="col-sm-6">
                                <div class="showing-data">
                                    @if ($products->total() > 0)
                                        <span>Showing {{ $products->firstItem() }}-{{ $products->lastItem() }} of
                                            {{ $products->total() }} Results</span>
                                    @else
                                        <span>No products found</span>
                                    @endif
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="filter_sort">
                                    <div class="filter_btn">
                                        <i class="fa fa-list-ul"></i>
                                    </div>
                                    <div class="page-sort">
                                        <form action="{{ url()->current() }}" method="GET" class="sort-form">
                                            <select name="sort" class="form-control form-select sort">
                                                <option value="1" @selected(request('sort') == 1)>Product: Latest</option>
                                                <option value="2" @selected(request('sort') == 2)>Product: Oldest</option>
                                                <option value="3" @selected(request('sort') == 3)>Price: High To Low</option>
                                                <option value="4" @selected(request('sort') == 4)>Price: Low To High</option>
                                                <option value="5" @selected(request('sort') == 5)>Name: A-Z</option>
                                                <option value="6" @selected(request('sort') == 6)>Name: Z-A</option>
                                            </select>
                                            <input type="hidden" name="min_price" value="{{ request('min_price', $min_price) }}" />
                                            <input type="hidden" name="max_price" value="{{ request('max_price', $max_price) }}" />
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-sm-3 filter_sidebar">
                    <div class="filter_close"><i class="fa fa-long-arrow-left"></i> Filter</div>
                    <form action="{{ url()->current() }}" method="GET" class="attribute-submit shop-filter-form">
                        @if(request()->filled('sort'))
                            <input type="hidden" name="sort" value="{{ request('sort') }}" />
                        @endif
                        <div class="sidebar_item wraper__item">
                            <div class="accordion" id="shop_categories_sidebar">
                                <div class="accordion-item">
                                    <h2 class="accordion-header">
                                        <button class="accordion-button" type="button" data-bs-toggle="collapse"
                                            data-bs-target="#collapseShopCat" aria-expanded="true">
                                            Categories
                                        </button>
                                    </h2>
                                    <div id="collapseShopCat" class="accordion-collapse collapse show"
                                        data-bs-parent="#shop_categories_sidebar">
                                        <div class="accordion-body cust_according_body">
                                            <ul class="mb-0">
                                                @foreach ($menucategories as $cat)
                                                    <li class="mb-1">
                                                        <a href="{{ route('category', $cat->slug) }}">{{ $cat->name }}</a>
                                                    </li>
                                                @endforeach
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="sidebar_item wraper__item">
                            <div class="accordion" id="shop_price_sidebar">
                                <div class="accordion-item">
                                    <h2 class="accordion-header">
                                        <button class="accordion-button" type="button" data-bs-toggle="collapse"
                                            data-bs-target="#collapseShopPrice" aria-expanded="true">
                                            Price
                                        </button>
                                    </h2>
                                    <div id="collapseShopPrice" class="accordion-collapse collapse show"
                                        data-bs-parent="#shop_price_sidebar">
                                        <div class="accordion-body cust_according_body">
                                            <div class="category-filter-box category__wraper">
                                                <div class="category-filter-item">
                                                    <div class="filter-body">
                                                        <div class="slider-box">
                                                            <div class="filter-price-inputs">
                                                                <p class="min-price">৳<input type="text" name="min_price"
                                                                        id="min_price" readonly /></p>
                                                                <p class="max-price">৳<input type="text" name="max_price"
                                                                        id="max_price" readonly /></p>
                                                            </div>
                                                            <div id="price-range" class="slider form-attribute ui-slider-shop"></div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <button type="submit" class="btn btn-sm btn-primary w-100 mt-2 rounded-pill">Apply filter</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>

                <div class="col-sm-9">
                    <div class="category-product main_product_inner">
                        @foreach ($products as $key => $value)
                            <div class="product_item wist_item wow zoomIn" data-wow-duration="1.5s"
                                data-wow-delay="0.{{ $key }}s">
                                <div class="product_item_inner">
                                    @if ($value->old_price)
                                        <div class="sale-badge">
                                            <div class="sale-badge-inner">
                                                <div class="sale-badge-box">
                                                    <span class="sale-badge-text">
                                                        @php
                                                            $discount = ((($value->old_price - $value->new_price) * 100) / $value->old_price);
                                                        @endphp
                                                        <p>{{ number_format($discount, 0) }}%</p>
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
                                    $averageRating = $value->reviews->avg('ratting') ?? 0;
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
                                    <span>&nbsp;</span>
                                @endif

                                <div class="pro_price">
                                    <p>
                                        @if ($value->old_price)
                                            <del>৳ {{ $value->old_price }}</del>
                                        @endif
                                        ৳ {{ $value->new_price }}
                                    </p>
                                </div>

                                @if (!$value->prosizes->isEmpty() || !$value->procolors->isEmpty())
                                    <div class="pro_btn">
                                        <a href="{{ route('product', $value->slug) }}" class="order-btn-link order-btn">
                                            অর্ডার করুন
                                        </a>
                                        <a href="{{ route('product', $value->slug) }}" class="cart-icon-link cart-icon-btn">
                                            <i class="fa-solid fa-cart-shopping"></i>
                                        </a>
                                    </div>
                                @else
                                    <div class="pro_btn">
                                        <form action="{{ route('cart.store') }}" method="POST">
                                            @csrf
                                            <input type="hidden" name="id" value="{{ $value->id }}" />
                                            <input type="hidden" name="qty" value="1" />
                                            <input type="hidden" name="order_now" value="1" />
                                            <button type="submit" class="order-btn">
                                                অর্ডার করুন
                                            </button>
                                        </form>
                                        <form action="{{ route('cart.store') }}" method="POST">
                                            @csrf
                                            <input type="hidden" name="id" value="{{ $value->id }}" />
                                            <input type="hidden" name="qty" value="1" />
                                            <button type="submit" class="cart-icon-btn cart_store"
                                                data-id="{{ $value->id }}">
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
                        {{ $products->links('pagination::bootstrap-4') }}
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

@push('script')
    <script src="https://ajax.googleapis.com/ajax/libs/jqueryui/1.13.2/jquery-ui.min.js"></script>
    <script>
        $(".sort").change(function() {
            $('#loading').show();
            $(".sort-form").submit();
        });

        $(".form-attribute").on('change slide', function() {
            // slider stop handled below
        });

        $(function() {
            $("#price-range").slider({
                step: {{ $sliderStep }},
                range: true,
                min: {{ $min_price }},
                max: {{ $max_price }},
                values: [
                    {{ request()->filled('min_price') ? (float) request('min_price') : $min_price }},
                    {{ request()->filled('max_price') ? (float) request('max_price') : $max_price }}
                ],
                slide: function(event, ui) {
                    $("#min_price").val(ui.values[0]);
                    $("#max_price").val(ui.values[1]);
                },
                stop: function() {
                    $(".shop-filter-form").submit();
                }
            });
            $("#min_price").val($("#price-range").slider("values", 0));
            $("#max_price").val($("#price-range").slider("values", 1));
        });
    </script>
@endpush
