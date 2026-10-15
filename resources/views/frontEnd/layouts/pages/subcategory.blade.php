@extends('frontEnd.layouts.master') 
@section('title',$subcategory->subcategoryName) 
@push('css')
<link rel="stylesheet" href="{{asset('public/frontEnd/css/jquery-ui.css')}}" />
@endpush 
@push('seo')
<meta name="app-url" content="{{route('subcategory',$subcategory->slug)}}" />
<meta name="robots" content="index, follow" />
<meta name="description" content="{{ $subcategory->meta_description}}" />
<meta name="keywords" content="{{ $subcategory->slug }}" />

<!-- Twitter Card data -->
<meta name="twitter:card" content="product" />
<meta name="twitter:site" content="{{$subcategory->subcategoryName}}" />
<meta name="twitter:title" content="{{$subcategory->subcategoryName}}" />
<meta name="twitter:description" content="{{ $subcategory->meta_description}}" />
<meta name="twitter:creator" content="gomobd.com" />
<meta property="og:url" content="{{route('subcategory',$subcategory->slug)}}" />
<meta name="twitter:image" content="{{asset($subcategory->image)}}" />

<!-- Open Graph data -->
<meta property="og:title" content="{{$subcategory->subcategoryName}}" />
<meta property="og:type" content="product" />
<meta property="og:url" content="{{route('subcategory',$subcategory->slug)}}" />
<meta property="og:image" content="{{asset($subcategory->image)}}" />
<meta property="og:description" content="{{ $subcategory->meta_description}}" />
<meta property="og:site_name" content="{{$subcategory->subcategoryName}}" />
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
                        <strong>{{ $subcategory->subcategoryName }}</strong>
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
                            <div class="filter_sort">
                                <div class="filter_btn">
                                    <i class="fa fa-list-ul"></i>
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
        </div>
        
        <div class="row">
            <div class="col-sm-3 filter_sidebar">
                
                <div class="filter_close"><i class="fa fa-long-arrow-left"></i> Filter</div>
                <form action="" class="attribute-submit">
                    <div class="sidebar_item wraper__item">
                        <div class="accordion" id="category_sidebar">
                            <div class="accordion-item">
                                <h2 class="accordion-header">
                                    <button class="accordion-button" type="button" data-bs-toggle="collapse"
                                        data-bs-target="#collapseCat" aria-expanded="true" aria-controls="collapseOne">
                                        {{ $subcategory->subcategoryName }}
                                    </button>
                                </h2>
                                <div id="collapseCat" class="accordion-collapse collapse show"
                                    data-bs-parent="#category_sidebar">
                                    <div class="accordion-body cust_according_body">
                                        <ul>
                                            @foreach ($subcategory->childcategories as $key => $childcat)
                                                <li>
                                                    <a
                                                        href="{{ url('products/' . $childcat->slug) }}">{{ $childcat->childcategoryName }}</a>
                                                </li>
                                            @endforeach
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!--sidebar item end-->
                    <div class="sidebar_item wraper__item">
                        <div class="accordion" id="price_sidebar">
                            <div class="accordion-item">
                                <h2 class="accordion-header">
                                    <button class="accordion-button" type="button" data-bs-toggle="collapse"
                                        data-bs-target="#collapsePrice" aria-expanded="true" aria-controls="collapseOne">
                                        Price
                                    </button>
                                </h2>
                                <div id="collapsePrice" class="accordion-collapse collapse show"
                                    data-bs-parent="#price_sidebar">
                                    <div class="accordion-body cust_according_body">
                                        <div class="category-filter-box category__wraper" id="categoryFilterBox">
                                            <div class="category-filter-item">
                                                <div class="filter-body">
                                                    <div class="slider-box">
                                                        <form action="" class="price-submit">
                                                            <div class="filter-price-inputs">
                                                                <p class="min-price">৳<input type="text"
                                                                        name="min_price" id="min_price" readonly="" />
                                                                </p>
                                                                <p class="max-price">৳<input type="text"
                                                                        name="max_price" id="max_price" readonly="" />
                                                                </p>
                                                            </div>
    
                                                            <div id="price-range" class="slider form-attribute"></div>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!--sidebar item end-->
                    <div class="sidebar_item wraper__item">
                        <div class="accordion" id="filter_sidebar">
                            <div class="accordion-item">
                                <h2 class="accordion-header">
                                    <button class="accordion-button" type="button" data-bs-toggle="collapse"
                                        data-bs-target="#collapseFilter" aria-expanded="true"
                                        aria-controls="collapseOne">
                                        Filter
                                    </button>
                                </h2>
                                <div id="collapseFilter" class="accordion-collapse collapse show"
                                    data-bs-parent="#filter_sidebar">
                                    <div class="accordion-body cust_according_body">
                                        <div class="filter-body">
                                            <form action="" class="subcategory-submit">
                                                <ul class="space-y-3">
                                                    @foreach ($childcategories as $childcategory)
                                                        <li class="subcategory-filter-list">
                                                            <label for="{{ $childcategory->slug . '-' . $childcategory->id }}"
                                                                class="subcategory-filter-label">
                                                                <input class="form-checkbox form-attribute"
                                                                    id="{{ $childcategory->slug . '-' . $childcategory->id }}"
                                                                    name="childcategory[]" value="{{ $childcategory->id }}"
                                                                    type="checkbox"
                                                                    @if (is_array(request()->get('childcategory')) && in_array($childcategory->id, request()->get('childcategory'))) checked @endif />
                                                                <p class="subcategory-filter-name">
                                                                    {{ $childcategory->childcategoryName }}</p>
                                                            </label>
                                                        </li>
                                                    @endforeach
                                                </ul>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!--sidebar item end-->
                </form>
            </div>
            <div class="col-sm-9">
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
                                $filledStars   = floor($averageRating);
                                $hasHalfStar   = $averageRating - $filledStars >= 0.5;
                                $emptyStars    = 5 - $filledStars - ($hasHalfStar ? 1 : 0);
                            @endphp

                            @if ($averageRating >= 0 && $averageRating <= 5)
                                {{-- Filled stars --}}
                                @for ($i = 0; $i < $filledStars; $i++)
                                    <i class="fas fa-star"></i>
                                @endfor

                                {{-- Half star --}}
                                @if ($hasHalfStar)
                                    <i class="fas fa-star-half-alt"></i>
                                @endif

                                {{-- Empty stars --}}
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

                            {{-- ✅ এখানে নতুন দুইটা বাটন (অর্ডার + কার্ট আইকন) --}} 
                            @if (!$value->prosizes->isEmpty() || !$value->procolors->isEmpty())
                                {{-- ভ্যারিয়েন্ট থাকলে: শুধু ডিটেইল পেজে পাঠাবে --}}
                                <div class="pro_btn">
                                    {{-- বড় "অর্ডার করুন" বাটন --}}
                                    <a href="{{ route('product', $value->slug) }}"
                                       class="order-btn-link order-btn">
                                        অর্ডার করুন
                                    </a>

                                    {{-- ডান পাশে ছোট কার্ট আইকন বাটন --}}
                                    <a href="{{ route('product', $value->slug) }}"
                                       class="cart-icon-link cart-icon-btn">
                                        <i class="fa-solid fa-cart-shopping"></i>
                                    </a>
                                </div>
                            @else
                                {{-- ভ্যারিয়েন্ট না থাকলে: সরাসরি কার্টে যোগ + অর্ডার --}}
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

<section class="homeproduct">
    <div class="container">
        <div class="row">
            <div class="col-sm-12">
                <div class="meta_des">
                    {!!$subcategory->meta_description!!}
                </div>
            </div>
        </div>
    </div>
</section>

@endsection
@push('script')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/2.1.3/jquery.min.js"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jqueryui/1.11.2/jquery-ui.min.js"></script>
    <script>
        $("#price-range").click(function() {
            $(".price-submit").submit();
        })
        $(".form-attribute").on('change click',function(){
            $(".attribute-submit").submit();
        })
        $(".sort").change(function() {
            $(".sort-form").submit();
        })
        $(".form-checkbox").change(function() {
            $(".subcategory-submit").submit();
        })
    </script>
    <script>
        $(function() {
            $("#price-range").slider({
                step: 5,
                range: true,
                min: {{ $min_price }},
                max: {{ $max_price }},
                values: [
                    {{ request()->get('min_price') ? request()->get('min_price') : $min_price }},
                    {{ request()->get('max_price') ? request()->get('max_price') : $max_price }}
                ],
                slide: function(event, ui) {
                    $("#min_price").val(ui.values[0]);
                    $("#max_price").val(ui.values[1]);
                }
            });
            $("#min_price").val({{ request()->get('min_price') ? request()->get('min_price') : $min_price }});
            $("#max_price").val({{ request()->get('max_price') ? request()->get('max_price') : $max_price }});
            $("#priceRange").val($("#price-range").slider("values", 0) + " - " + $("#price-range").slider("values",
                1));

            $("#mobile-price-range").slider({
                step: 5,
                range: true,
                min: {{ $min_price }},
                max: {{ $max_price }},
                values: [
                    {{ request()->get('min_price') ? request()->get('min_price') : $min_price }},
                    {{ request()->get('max_price') ? request()->get('max_price') : $max_price }}
                ],
                slide: function(event, ui) {
                    $("#min_price").val(ui.values[0]);
                    $("#max_price").val(ui.values[1]);
                }
            });
            $("#min_price").val({{ request()->get('min_price') ? request()->get('min_price') : $min_price }});
            $("#max_price").val({{ request()->get('max_price') ? request()->get('max_price') : $max_price }});
            $("#priceRange").val($("#price-range").slider("values", 0) + " - " + $("#price-range").slider("values",
                1));

        });
    </script>

<script>
    // $(".sort").change(function(){
    //   $('#loading').show();
    //   $(".sort-form").submit();
    // })
</script>

    {{-- 🔹 GA4 DataLayer + Facebook Pixel for Subcategory Page --}}
    <script type="text/javascript">
        window.dataLayer = window.dataLayer || [];

        (function () {
            var listName = @json($subcategory->subcategoryName);
            var listSlug = @json($subcategory->slug);

            var listItems = [
                @foreach($products as $index => $value)
                {
                    item_id: "{{ $value->id }}",
                    item_name: @json($value->name),
                    price: {{ (float) $value->new_price }},
                    item_brand: @json(optional($value->brand)->name),
                    item_category: @json(optional($value->category)->name ?? $subcategory->subcategoryName),
                    item_list_id: listSlug,
                    item_list_name: listName,
                    index: {{ $loop->iteration }},
                    slug: @json($value->slug),
                    currency: "BDT"
                }@if(!$loop->last),@endif
                @endforeach
            ];

            // GA4: view_item_list
            if (listItems.length) {
                window.dataLayer.push({ ecommerce: null });
                window.dataLayer.push({
                    event: "view_item_list",
                    ecommerce: {
                        item_list_id: listSlug,
                        item_list_name: listName,
                        items: listItems.map(function (item) {
                            return {
                                item_id: item.item_id,
                                item_name: item.item_name,
                                index: item.index,
                                price: item.price,
                                item_brand: item.item_brand,
                                item_category: item.item_category,
                                item_list_id: item.item_list_id,
                                item_list_name: item.item_list_name,
                                currency: item.currency
                            };
                        })
                    }
                });
            }

            // Facebook Pixel: ViewSubcategory (custom)
            if (typeof fbq === "function") {
                fbq("trackCustom", "ViewSubcategory", {
                    content_category: listName,
                    content_ids: listItems.map(function (i) { return i.item_id; }),
                    currency: "BDT"
                });
            }

            function findItemByHref(href) {
                if (!href) return null;
                try {
                    var parts = href.split("/");
                    var last = parts[parts.length - 1].split("?")[0];
                    return listItems.find(function (i) { return i.slug === last; }) || null;
                } catch (e) {
                    return null;
                }
            }

            // product click -> select_item + FB event
            $(document).on("click", ".category-product .product_item a", function () {
                var href = $(this).attr("href") || "";
                var item = findItemByHref(href);
                if (!item) return;

                window.dataLayer.push({ ecommerce: null });
                window.dataLayer.push({
                    event: "select_item",
                    ecommerce: {
                        item_list_id: listSlug,
                        item_list_name: listName,
                        items: [{
                            item_id: item.item_id,
                            item_name: item.item_name,
                            index: item.index,
                            price: item.price,
                            item_brand: item.item_brand,
                            item_category: item.item_category,
                            item_list_id: item.item_list_id,
                            item_list_name: item.item_list_name,
                            currency: item.currency
                        }]
                    }
                });

                if (typeof fbq === "function") {
                    fbq("trackCustom", "SubcategoryProductClick", {
                        content_ids: [item.item_id],
                        content_name: item.item_name,
                        content_category: item.item_category,
                        value: item.price,
                        currency: "BDT"
                    });
                }
            });

        })();
    </script>
@endpush
