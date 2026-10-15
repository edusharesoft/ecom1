@extends('frontEnd.layouts.master')
@section('title', 'Sellers - All Verified Shops')

@push('css')
<link rel="stylesheet" href="{{ asset('public/frontEnd/css/jquery-ui.css') }}">
<style>
    /* Custom Variables */
    :root {
        --primary-color: #667eea;
        --secondary-color: #764ba2;
        --text-dark: #333;
        --text-muted: #777;
        --card-bg: #ffffff;
    }

    /* Page Header */
    .page-header-section {
        background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%);
        border-radius: 12px;
        padding: 50px 20px;
        margin-bottom: 30px;
        text-align: center;
        color: white;
        position: relative;
        overflow: hidden;
    }
    .page-header-section::after {
        content: '';
        position: absolute;
        top: 0; left: 0; right: 0; bottom: 0;
        background: url('data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iMjAiIGhlaWdodD0iMjAiIHhtbG5zPSJodHRwOi8vd3d3LnczLm9yZy8yMDAwL3N2ZyI+PGNpcmNsZSBjeD0iMSIgY3k9IjEiIHI9IjEiIGZpbGw9InJnYmEoMjU1LDI1NSwyNTUsMC4xKSIvPjwvc3ZnPg==');
        opacity: 0.3;
    }

    /* Search & Filter */
    .sorting-section {
        background: #fff;
        padding: 20px;
        border-radius: 12px;
        box-shadow: 0 5px 20px rgba(0,0,0,0.03);
        margin-bottom: 30px;
    }
    .search-box .form-control {
        border-radius: 50px 0 0 50px;
        border: 1px solid #eee;
        padding-left: 20px;
    }
    .search-box .form-control:focus {
        box-shadow: none;
        border-color: var(--primary-color);
    }
    .search-box .btn {
        border-radius: 0 50px 50px 0;
        padding-left: 20px;
        padding-right: 20px;
    }

    /* Seller Card */
    .seller-card {
        background: var(--card-bg);
        border-radius: 12px;
        overflow: hidden;
        border: 1px solid #f0f0f0;
        transition: all 0.3s cubic-bezier(0.25, 0.8, 0.25, 1);
        height: 100%;
        display: flex;
        flex-direction: column;
        position: relative;
    }
    .seller-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 15px 30px rgba(0,0,0,0.08);
        border-color: transparent;
    }
    
    .seller-banner {
        height: 110px;
        background-size: cover;
        background-position: center;
        position: relative;
    }
    .seller-banner::before {
        content: '';
        position: absolute;
        top: 0; left: 0; width: 100%; height: 100%;
        background: linear-gradient(to bottom, rgba(0,0,0,0.1), rgba(0,0,0,0.3));
    }

    .seller-logo-container {
        position: relative;
        margin-top: -50px;
        text-align: center;
        margin-bottom: 10px;
    }
    .seller-logo {
        width: 90px;
        height: 90px;
        border-radius: 50%;
        border: 4px solid #fff;
        background: #fff;
        box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        display: inline-flex;
        align-items: center;
        justify-content: center;
        overflow: hidden;
        position: relative;
    }
    .seller-logo img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }
    .seller-initial {
        font-size: 32px;
        font-weight: 700;
        color: #fff;
        background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
        width: 100%;
        height: 100%;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    
    .verified-badge {
        position: absolute;
        bottom: 5px;
        left: 50%;
        transform: translateX(25px); /* Position next to circle */
        background: #0d6efd;
        color: white;
        width: 24px;
        height: 24px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        border: 2px solid #fff;
        font-size: 10px;
        z-index: 2;
    }

    .seller-body {
        padding: 0 20px 20px;
        text-align: center;
        flex-grow: 1;
        display: flex;
        flex-direction: column;
    }
    
    .shop-name {
        font-size: 18px;
        font-weight: 700;
        color: var(--text-dark);
        margin-bottom: 5px;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }
    
    .shop-rating {
        margin-bottom: 15px;
        color: #ffc107;
        font-size: 13px;
    }
    .shop-rating span {
        color: var(--text-muted);
        margin-left: 5px;
    }

    .shop-stats {
        display: flex;
        justify-content: center;
        gap: 15px;
        background: #f8f9fa;
        padding: 10px;
        border-radius: 8px;
        margin-bottom: 20px;
    }
    .stat-item h5 {
        font-size: 16px;
        font-weight: 700;
        margin: 0;
        color: var(--primary-color);
    }
    .stat-item p {
        font-size: 11px;
        margin: 0;
        color: var(--text-muted);
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .visit-btn {
        margin-top: auto;
        display: block;
        width: 100%;
        padding: 10px;
        border: 1px solid var(--primary-color);
        color: var(--primary-color);
        border-radius: 8px;
        text-decoration: none;
        font-weight: 600;
        font-size: 14px;
        transition: 0.3s;
    }
    .visit-btn:hover {
        background: var(--primary-color);
        color: #fff;
    }

    /* Empty State */
    .empty-state {
        text-align: center;
        padding: 60px 20px;
        background: #fff;
        border-radius: 12px;
        border: 1px dashed #ddd;
    }
    .empty-state i {
        font-size: 60px;
        color: #e0e0e0;
        margin-bottom: 20px;
    }

    @media (max-width: 576px) {
        .page-header-section h1 { font-size: 28px; }
        .seller-banner { height: 90px; }
        .seller-logo { width: 75px; height: 75px; }
        .verified-badge { transform: translateX(20px); }
    }
</style>
@endpush

@section('content')
<section class="product-section py-5 bg-light">
    <div class="container">



        {{-- Search and Filter --}}
        <div class="sorting-section">
            <div class="row align-items-center g-3">
                <div class="col-md-6">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb mb-0">
                            <li class="breadcrumb-item"><a href="{{ route('home') }}" class="text-decoration-none text-muted">Home</a></li>
                            <li class="breadcrumb-item active text-dark fw-bold" aria-current="page">Sellers</li>
                        </ol>
                    </nav>
                </div>

                <div class="col-md-6">
                    <form method="GET" action="{{ route('sellers') }}" class="search-box d-flex">
                        <input type="text" name="keyword" class="form-control" placeholder="Search by shop name..." value="{{ request('keyword') }}">
                        <button type="submit" class="btn btn-primary bg-gradient border-0">
                            <i class="fas fa-search"></i>
                        </button>
                        @if(request('keyword'))
                        <a href="{{ route('sellers') }}" class="btn btn-light border ms-2" style="border-radius: 8px;">
                            <i class="fas fa-times text-danger"></i>
                        </a>
                        @endif
                    </form>
                </div>
            </div>
        </div>

        {{-- Sellers Grid --}}
        @if($vendors->count() > 0)
        <div class="row g-4">
            @foreach($vendors as $vendor)
            <div class="col-xl-3 col-lg-4 col-sm-6">
                <div class="seller-card">
                    {{-- Background Banner --}}
                    <div class="seller-banner" 
                         style="background-image: url('{{ $vendor->banner ? asset($vendor->banner) : asset('public/frontEnd/images/default-banner.jpg') }}');">
                    </div>
                    
                    {{-- Logo & Verification --}}
                    <div class="seller-logo-container">
                        <div class="seller-logo">
                            @if($vendor->logo)
                                <img src="{{ asset($vendor->logo) }}" alt="{{ $vendor->shop_name }}">
                            @else
                                <div class="seller-initial">
                                    {{ strtoupper(substr($vendor->shop_name, 0, 1)) }}
                                </div>
                            @endif
                        </div>
                        @if($vendor->verification_status == 'approved')
                            <div class="verified-badge" title="Verified Seller">
                                <i class="fas fa-check"></i>
                            </div>
                        @endif
                    </div>
                    
                    {{-- Content --}}
                    <div class="seller-body">
                        <h3 class="shop-name" title="{{ $vendor->shop_name }}">{{ $vendor->shop_name }}</h3>
                        
                        {{-- Rating --}}
                        <div class="shop-rating">
                            @php $rating = $vendor->average_rating; @endphp
                            @for($i = 1; $i <= 5; $i++)
                                <i class="{{ $i <= floor($rating) ? 'fas' : ($i - 0.5 <= $rating ? 'fas fa-star-half-alt' : 'far') }} fa-star"></i>
                            @endfor
                            <span>({{ number_format($rating, 1) }})</span>
                        </div>

                        {{-- Stats --}}
                        <div class="shop-stats">
                            <div class="stat-item">
                                <h5>{{ $vendor->products_count }}</h5>
                                <p>Items</p>
                            </div>
                            <div class="stat-item border-start ps-3">
                                <h5>{{ $vendor->total_reviews }}</h5>
                                <p>Reviews</p>
                            </div>
                        </div>
                        
                        {{-- Link --}}
                        <a href="{{ route('vendor.shop', $vendor->slug) }}" class="visit-btn">
                            Visit Store <i class="fas fa-arrow-right ms-1"></i>
                        </a>
                    </div>
                </div>
            </div>
            @endforeach
        </div>

<div class="row mt-5">
    <div class="col-12">
        <div class="d-flex justify-content-center">

            {{-- কাস্টম CSS (শুধুমাত্র এই পেজের জন্য) --}}
            <style>
                .my-pagination {
                    display: flex;
                    gap: 5px;
                    list-style: none;
                    padding: 0;
                    margin: 0;
                }
                .my-pagination a, .my-pagination span {
                    display: flex;
                    align-items: center;
                    justify-content: center;
                    width: 35px;
                    height: 35px;
                    border-radius: 50%; /* গোল বাটন */
                    text-decoration: none;
                    color: #333;
                    font-weight: bold;
                    border: 1px solid #ddd;
                    transition: 0.3s;
                    background: white;
                }
                /* হোভার করলে কালার চেঞ্জ হবে */
                .my-pagination a:hover {
                    background-color: #0d6efd;
                    color: white;
                    border-color: #0d6efd;
                }
                /* বর্তমানে যেই পেজে আছেন */
                .my-pagination .active {
                    background-color: #0d6efd;
                    color: white;
                    border-color: #0d6efd;
                    pointer-events: none;
                }
                /* ডিজেবল বাটন */
                .my-pagination .disabled {
                    background-color: #f1f1f1;
                    color: #ccc;
                    cursor: not-allowed;
                }
            </style>

            {{-- পেজিনেশন লজিক --}}
            @if ($vendors->hasPages())
                <div class="my-pagination">
                    
                    {{-- Previous Button --}}
                    @if ($vendors->onFirstPage())
                        <span class="disabled">&laquo;</span>
                    @else
                        <a href="{{ $vendors->previousPageUrl() }}">&laquo;</a>
                    @endif

                    {{-- Page Numbers --}}
                    @foreach ($vendors->getUrlRange(1, $vendors->lastPage()) as $page => $url)
                        @if ($page == $vendors->currentPage())
                            <span class="active">{{ $page }}</span>
                        @else
                            <a href="{{ $url }}">{{ $page }}</a>
                        @endif
                    @endforeach

                    {{-- Next Button --}}
                    @if ($vendors->hasMorePages())
                        <a href="{{ $vendors->nextPageUrl() }}">&raquo;</a>
                    @else
                        <span class="disabled">&raquo;</span>
                    @endif

                </div>
            @endif

        </div>
    </div>
</div>

        @else
        {{-- Empty State --}}
        <div class="empty-state">
            <i class="fas fa-store-slash"></i>
            <h3>No Sellers Found</h3>
            <p class="text-muted">
                @if(request('keyword')) 
                    We couldn't find any shops matching "{{ request('keyword') }}".
                @else 
                    There are no active sellers at the moment.
                @endif
            </p>
            @if(request('keyword'))
                <a href="{{ route('sellers') }}" class="btn btn-primary mt-3">View All Sellers</a>
            @endif
        </div>
        @endif

    </div>
</section>
@endsection