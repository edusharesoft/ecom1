@extends('frontEnd.layouts.master')
@section('title', $vendor->shop_name)

@push('css')
<link rel="stylesheet" href="{{ asset('public/frontEnd/css/jquery-ui.css') }}">
@endpush

@section('content')
<section class="product-section">
    <div class="container">

        {{-- Vendor Shop Header with Background Banner --}}
        <div class="vendor-shop-header-wrapper" style="position: relative; border-radius: 15px; overflow: hidden; margin-bottom: 30px; box-shadow: 0 4px 20px rgba(0,0,0,0.1);">
            {{-- Background Banner --}}
            <div class="vendor-banner-background" style="position: absolute; top: 0; left: 0; right: 0; bottom: 0; background-image: url('{{ $vendor->banner ? asset($vendor->banner) : asset('public/frontEnd/images/default-banner.jpg') }}'); background-size: cover; background-position: center; background-repeat: no-repeat; z-index: 0;">
                <div style="position: absolute; top: 0; left: 0; right: 0; bottom: 0; background: linear-gradient(135deg, rgba(102, 126, 234, 0.2) 0%, rgba(118, 75, 162, 0.3) 100%);"></div>
            </div>
            
            {{-- Content Overlay --}}
            <div class="vendor-shop-header" style="position: relative; padding: 40px 30px; z-index: 1;">
                <div class="row align-items-center">
                    <div class="col-md-3 text-center">
                        <div class="vendor-logo-large" style="position: relative; display: inline-block;">
                            @if($vendor->logo)
                                <img src="{{ asset($vendor->logo) }}" alt="{{ $vendor->shop_name }}" style="width: 120px; height: 120px; border-radius: 50%; border: 5px solid #fff; box-shadow: 0 4px 20px rgba(0,0,0,0.3); object-fit: cover;" />
                            @else
                                <div style="width: 120px; height: 120px; border-radius: 50%; background: rgba(255,255,255,0.25); border: 5px solid #fff; display: flex; align-items: center; justify-content: center; margin: 0 auto; font-size: 48px; font-weight: bold; color: #fff; box-shadow: 0 4px 20px rgba(0,0,0,0.3);">
                                    {{ strtoupper(substr($vendor->shop_name, 0, 1)) }}
                                </div>
                            @endif
                            @if($vendor->verification_status == 'approved')
                            <div style="position: absolute; bottom: 5px; right: 5px; width: 36px; height: 36px; background: #0d6efd; border-radius: 50%; display: flex; align-items: center; justify-content: center; border: 4px solid #fff; box-shadow: 0 2px 10px rgba(0,0,0,0.3);">
                                <i class="fas fa-check-circle" style="color: #fff; font-size: 18px;"></i>
                            </div>
                            @endif
                        </div>
                    </div>
                    <div class="col-md-9">
                        <h1 style="color: #fff; font-size: 32px; font-weight: 700; margin-bottom: 20px; text-shadow: 0 2px 8px rgba(0,0,0,0.5);">{{ $vendor->shop_name }}</h1>
                        
                        <div class="vendor-stats" style="display: flex; gap: 30px; flex-wrap: wrap;">
                            <div style="color: #fff; background: rgba(255,255,255,0.15); padding: 12px 20px; border-radius: 10px; backdrop-filter: blur(10px);">
                                <strong style="font-size: 24px; display: block; font-weight: 700;">{{ $vendor->total_products }}</strong>
                                <span style="font-size: 13px; display: block; opacity: 0.9;">Products</span>
                            </div>
                            <div style="color: #fff; background: rgba(255,255,255,0.15); padding: 12px 20px; border-radius: 10px; backdrop-filter: blur(10px);">
                                <strong style="font-size: 24px; display: block; font-weight: 700;">{{ $vendor->total_reviews }}</strong>
                                <span style="font-size: 13px; display: block; opacity: 0.9;">Reviews</span>
                            </div>
                            <div style="color: #fff; background: rgba(255,255,255,0.15); padding: 12px 20px; border-radius: 10px; backdrop-filter: blur(10px);">
                                <div style="display: flex; align-items: center; gap: 5px; margin-bottom: 5px;">
                                    @for($i = 1; $i <= 5; $i++)
                                        @if($i <= floor($vendor->average_rating))
                                            <i class="fas fa-star" style="color: #ffc107; font-size: 16px;"></i>
                                        @elseif($i - 0.5 <= $vendor->average_rating)
                                            <i class="fas fa-star-half-alt" style="color: #ffc107; font-size: 16px;"></i>
                                        @else
                                            <i class="far fa-star" style="color: rgba(255,255,255,0.6); font-size: 16px;"></i>
                                        @endif
                                    @endfor
                                    <span style="font-size: 16px; margin-left: 8px; font-weight: 600;">{{ $vendor->average_rating > 0 ? number_format($vendor->average_rating, 1) : '0.0' }}</span>
                                </div>
                                <span style="font-size: 13px; display: block; opacity: 0.9;">Average Rating</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Breadcrumb + Sorting --}}
        <div class="sorting-section" style="background: #fff; padding: 20px; border-radius: 10px; margin-bottom: 25px; box-shadow: 0 2px 10px rgba(0,0,0,0.05);">
            <div class="row align-items-center">
                <div class="col-sm-6">
                    <div class="category-breadcrumb d-flex align-items-center" style="font-size: 14px;">
                        <a href="{{ route('home') }}" style="color: #666; text-decoration: none;">Home</a>
                        <span style="margin: 0 8px; color: #999;">/</span>
                        <strong style="color: #222; font-weight: 600;">{{ $vendor->shop_name }}</strong>
                    </div>
                </div>

                <div class="col-sm-6">
                    <div class="row align-items-center">
                        <div class="col-sm-6">
                            <div class="showing-data" style="font-size: 14px; color: #666;">
                                <span>
                                    Showing {{ $products->firstItem() ?? 0 }}-{{ $products->lastItem() ?? 0 }}
                                    of {{ $products->total() }} Results
                                </span>
                            </div>
                        </div>

                        <div class="col-sm-6">
                            <form class="sort-form">
                                <select name="sort" class="form-control form-select sort" style="border-radius: 8px; border: 1px solid #e0e0e0; padding: 8px 12px; font-size: 14px;">
                                    <option value="1" @selected(request('sort')==1)>Product: Latest</option>
                                    <option value="2" @selected(request('sort')==2)>Product: Oldest</option>
                                    <option value="3" @selected(request('sort')==3)>Price: High To Low</option>
                                    <option value="4" @selected(request('sort')==4)>Price: Low To High</option>
                                </select>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Product Grid --}}
        <div class="row">
            <div class="col-sm-12">
                <div class="category-product main_product_inner">

                    @forelse($products as $key => $value)
                    <div class="product_item wist_item wow zoomIn"
                         data-wow-duration="1.5s"
                         data-wow-delay="0.{{ $key }}s">

                        <div class="product_item_inner">

                            {{-- Discount badge --}}
                            @if($value->old_price && $value->old_price > $value->new_price)
                            <div class="sale-badge">
                                <div class="sale-badge-inner">
                                    <div class="sale-badge-box">
                                        <span class="sale-badge-text">
                                            <p>
                                                @php
                                                    $discount = ((($value->old_price - $value->new_price) * 100) / $value->old_price);
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
                                         alt="{{ $value->name }}"
                                         class="img-fluid"
                                         loading="lazy" />
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
                            @for ($i = 0; $i < $filledStars; $i++)
                                <i class="fas fa-star"></i>
                            @endfor
                            @if ($hasHalfStar)
                                <i class="fas fa-star-half-alt"></i>
                            @endif
                            @for ($i = 0; $i < $emptyStars; $i++)
                                <i class="far fa-star"></i>
                            @endfor
                        @endif

                        <div class="pro_price">
                            <p>
                                @if($value->old_price)
                                    <del>৳ {{ $value->old_price }}</del>
                                @endif
                                ৳ {{ $value->new_price }}
                            </p>
                        </div>

                        {{-- Order and Cart Buttons --}}
                        @if (!$value->prosizes->isEmpty() || !$value->procolors->isEmpty())
                            {{-- Variant product – go to detail page --}}
                            <div class="pro_btn">
                                <a href="{{ route('product', $value->slug) }}" class="order-btn-link">
                                    অর্ডার করুন
                                </a>
                                <a href="{{ route('product', $value->slug) }}" class="cart-icon-link">
                                    <i class="fa-solid fa-cart-shopping"></i>
                                </a>
                            </div>
                        @else
                            {{-- Simple product --}}
                            <div class="pro_btn">
                                <form action="{{ route('cart.store') }}" method="POST">
                                    @csrf
                                    <input type="hidden" name="id" value="{{ $value->id }}" />
                                    <input type="hidden" name="qty" value="1" />
                                    <input type="hidden" name="order_now" value="1">
                                    <button type="submit" class="order-btn">অর্ডার করুন</button>
                                </form>

                                <form action="{{ route('cart.store') }}" method="POST">
                                    @csrf
                                    <input type="hidden" name="id" value="{{ $value->id }}" />
                                    <input type="hidden" name="qty" value="1" />
                                    <button type="submit" class="cart-icon-btn cart_store" data-id="{{ $value->id }}">
                                        <i class="fa-solid fa-cart-shopping"></i>
                                    </button>
                                </form>
                            </div>
                        @endif
                    </div>
                    @empty
                    <div class="col-12 text-center py-5">
                        <p style="font-size: 18px; color: #666;">No products found in this shop.</p>
                    </div>
                    @endforelse

                </div>
            </div>
        </div>

{{-- Pagination --}}
<div class="row">
    <div class="col-sm-12">
        <div class="pagination-wrapper text-center">
            
            {{-- কাস্টম স্টাইল (শুধুমাত্র এই সেকশনের জন্য) --}}
            <style>
                .prod-pagination {
                    display: inline-flex; /* text-center এর সাথে কাজ করার জন্য inline-flex */
                    gap: 5px;
                    list-style: none;
                    padding: 0;
                    margin: 20px 0;
                    flex-wrap: wrap;
                    justify-content: center;
                }
                .prod-pagination a, .prod-pagination span {
                    display: flex;
                    align-items: center;
                    justify-content: center;
                    width: 38px;
                    height: 38px;
                    border-radius: 50%; /* গোল বাটন */
                    text-decoration: none;
                    color: #555;
                    font-weight: 600;
                    border: 1px solid #e1e1e1;
                    transition: all 0.3s ease;
                    background: #fff;
                    font-size: 14px;
                }
                /* হোভার ইফেক্ট */
                .prod-pagination a:hover {
                    background-color: #ff5722; /* আপনার থিম কালার অনুযায়ী পাল্টাতে পারেন */
                    color: white;
                    border-color: #ff5722;
                    transform: translateY(-2px);
                    box-shadow: 0 4px 6px rgba(0,0,0,0.1);
                }
                /* একটিভ পেজ */
                .prod-pagination .active {
                    background-color: #ff5722; /* আপনার থিম কালার */
                    color: white;
                    border-color: #ff5722;
                    pointer-events: none;
                    box-shadow: 0 4px 6px rgba(0,0,0,0.1);
                }
                /* ডিজেবল বাটন */
                .prod-pagination .disabled {
                    background-color: #f8f9fa;
                    color: #ccc;
                    cursor: not-allowed;
                    border-color: #eee;
                }
            </style>

            {{-- পেজিনেশন লজিক --}}
            @if ($products->hasPages())
                <div class="prod-pagination">
                    
                    {{-- Previous Button --}}
                    @if ($products->onFirstPage())
                        <span class="disabled"><i class="fas fa-chevron-left"></i> &laquo;</span>
                    @else
                        <a href="{{ $products->previousPageUrl() }}"><i class="fas fa-chevron-left"></i> &laquo;</a>
                    @endif

                    {{-- Page Numbers --}}
                    {{-- নোট: অনেক বেশি পেজ থাকলে getUrlRange এর বদলে elements ব্যবহার করা ভালো --}}
                    @foreach ($products->getUrlRange(1, $products->lastPage()) as $page => $url)
                        @if ($page == $products->currentPage())
                            <span class="active">{{ $page }}</span>
                        @else
                            <a href="{{ $url }}">{{ $page }}</a>
                        @endif
                    @endforeach

                    {{-- Next Button --}}
                    @if ($products->hasMorePages())
                        <a href="{{ $products->nextPageUrl() }}">&raquo; <i class="fas fa-chevron-right"></i></a>
                    @else
                        <span class="disabled">&raquo; <i class="fas fa-chevron-right"></i></span>
                    @endif

                </div>
            @endif

        </div>
    </div>
</div>

    </div>
</section>
@endsection

@push('script')
<script>
    // Sort form auto submit
    document.querySelector('.sort-form select').addEventListener('change', function() {
        this.form.submit();
    });
</script>
@endpush
