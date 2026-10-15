@extends('reseller.layouts.app')

@section('title', 'প্রোডাক্ট ক্যাটালগ')
@section('page-title', 'প্রোডাক্ট ক্যাটালগ')

@push('styles')
<style>
    :root {
        --primary-gradient: linear-gradient(135deg, #4f46e5 0%, #3730a3 100%);
        --success-gradient: linear-gradient(135deg, #10b981 0%, #059669 100%);
        --card-hover-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
    }

    /* --- Filter Section --- */
    .filter-card {
        background: #fff;
        border-radius: 16px;
        border: 1px solid #e2e8f0;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
        margin-bottom: 24px;
        padding: 24px;
    }

    .form-control, .form-select {
        border-radius: 10px;
        border: 1px solid #e2e8f0;
        padding: 10px 15px;
        font-size: 0.9rem;
    }
    .form-control:focus, .form-select:focus {
        border-color: #4f46e5;
        box-shadow: 0 0 0 4px rgba(79, 70, 229, 0.1);
    }

    /* --- Product Card Styling --- */
    .product-card {
        background: #fff;
        border-radius: 20px;
        overflow: hidden;
        border: 1px solid #f1f5f9;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        position: relative;
        height: 100%;
        display: flex;
        flex-direction: column;
    }
    
    .product-card:hover { 
        box-shadow: var(--card-hover-shadow);
        transform: translateY(-5px);
        border-color: #e2e8f0;
    }

    .img-wrapper {
        position: relative;
        overflow: hidden;
        border-bottom: 1px solid #f1f5f9;
    }

    .product-img {
        width: 100%;
        height: 220px;
        object-fit: cover;
        background: #f8fafc;
        transition: transform 0.5s ease;
    }

    .product-card:hover .product-img {
        transform: scale(1.05);
    }

    .product-badge {
        position: absolute;
        top: 15px;
        left: 15px;
        background: rgba(255, 255, 255, 0.95);
        backdrop-filter: blur(4px);
        padding: 6px 12px;
        border-radius: 8px;
        font-size: 0.75rem;
        font-weight: 700;
        color: #4f46e5;
        box-shadow: 0 4px 6px rgba(0,0,0,0.05);
        z-index: 10;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
    
    .product-badge.hot { color: #dc2626; }

    .profit-chip {
        background: #ecfdf5;
        color: #059669;
        font-size: 0.75rem;
        font-weight: 700;
        padding: 6px 12px;
        border-radius: 30px;
        display: inline-flex;
        align-items: center;
        gap: 5px;
    }

    /* Buttons */
    .btn-gradient-primary {
        background: var(--primary-gradient);
        color: white;
        border: none;
    }
    .btn-gradient-primary:hover {
        color: white;
        box-shadow: 0 4px 12px rgba(79, 70, 229, 0.3);
    }

    .btn-action-circle {
        width: 38px;
        height: 38px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: all 0.2s;
        border: 1px solid #e2e8f0;
        background: white;
        color: #64748b;
    }
    .btn-action-circle:hover {
        background: #4f46e5;
        color: white;
        border-color: #4f46e5;
    }

    /* Toast Notification Customization */
    .custom-toast {
        background: white;
        border-radius: 12px;
        box-shadow: 0 10px 30px rgba(0,0,0,0.15);
        border-left: 5px solid #10b981;
    }
</style>
@endpush

@section('content')

    <div class="filter-card">
        <form action="{{ route('reseller.products.index') }}" method="GET">
            <div class="row g-3 align-items-end">
                <div class="col-lg-4 col-md-12">
                    <label class="form-label small fw-bold text-secondary text-uppercase mb-2"><i class="fas fa-search me-1"></i> প্রোডাক্ট খুঁজুন</label>
                    <div class="input-group">
                        <span class="input-group-text bg-white border-end-0 text-muted"><i class="fas fa-search"></i></span>
                        <input type="text" name="keyword" class="form-control border-start-0 ps-0" placeholder="প্রোডাক্ট নাম বা কোড..." value="{{ request('keyword') }}">
                    </div>
                </div>
                
                <div class="col-lg-2 col-md-4">
                    <label class="form-label small fw-bold text-secondary text-uppercase mb-2"><i class="fas fa-layer-group me-1"></i> ক্যাটাগরি</label>
                    <select name="category_id" class="form-select">
                        <option value="">সকল ক্যাটাগরি</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}" {{ request('category_id') == $category->id ? 'selected' : '' }}>
                                {{ $category->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                
                <div class="col-lg-2 col-md-4">
                    <label class="form-label small fw-bold text-secondary text-uppercase mb-2"><i class="fas fa-tag me-1"></i> ব্র্যান্ড</label>
                    <select name="brand_id" class="form-select">
                        <option value="">সকল ব্র্যান্ড</option>
                        @foreach($brands as $brand)
                            <option value="{{ $brand->id }}" {{ request('brand_id') == $brand->id ? 'selected' : '' }}>
                                {{ $brand->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                
                <div class="col-lg-2 col-md-4">
                    <label class="form-label small fw-bold text-secondary text-uppercase mb-2"><i class="fas fa-sort-amount-down me-1"></i> ফিল্টার</label>
                    <select name="sort" class="form-select">
                        <option value="latest" {{ request('sort') == 'latest' ? 'selected' : '' }}>নতুন সংযোজন</option>
                        <option value="price_low" {{ request('sort') == 'price_low' ? 'selected' : '' }}>দাম: কম থেকে বেশি</option>
                        <option value="price_high" {{ request('sort') == 'price_high' ? 'selected' : '' }}>দাম: বেশি থেকে কম</option>
                        <option value="profit_high" {{ request('sort') == 'profit_high' ? 'selected' : '' }}>সর্বোচ্চ লাভ</option>
                    </select>
                </div>
                
                <div class="col-lg-2 col-md-12">
                    <div class="d-grid">
                        <button type="submit" class="btn btn-primary fw-bold py-2 shadow-sm" style="border-radius: 10px;">
                            ফিল্টার করুন
                        </button>
                    </div>
                </div>
            </div>
        </form>
    </div>

    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 px-2">
        <div>
            <h5 class="fw-bold m-0 text-dark">সকল পণ্য</h5>
            <small class="text-muted"><i class="fas fa-box-open me-1"></i> {{ $products->total() }} টি পণ্য পাওয়া গেছে</small>
        </div>
        @if(request()->hasAny(['keyword', 'category_id', 'brand_id', 'sort']))
            <a href="{{ route('reseller.products.index') }}" class="btn btn-sm btn-light border rounded-pill px-3 shadow-sm text-danger mt-2 mt-md-0">
                <i class="fas fa-times me-1"></i> ফিল্টার মুছুন
            </a>
        @endif
    </div>

    <div class="row g-4">
        @forelse($products as $product)
        <div class="col-xl-3 col-lg-4 col-md-6 col-sm-6">
            <div class="product-card h-100">
                <div class="img-wrapper">
                    @if($product->created_at->isToday())
                        <span class="product-badge"><i class="fas fa-bolt me-1"></i> New</span>
                    @elseif($product->topsale)
                        <span class="product-badge hot"><i class="fas fa-fire me-1"></i> Hot</span>
                    @endif

                    <a href="{{ route('reseller.products.show', $product->slug) }}">
                        @if($product->image && $product->image->image)
                            <img src="{{ asset($product->image->image) }}" class="product-img" alt="{{ $product->name }}">
                        @else
                            <div class="product-img d-flex align-items-center justify-content-center bg-light text-secondary">
                                <i class="fas fa-image fa-3x opacity-50"></i>
                            </div>
                        @endif
                    </a>
                </div>

                <div class="p-4 flex-grow-1 d-flex flex-column">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span class="text-uppercase small fw-bold text-muted" style="font-size: 11px; letter-spacing: 0.5px;">
                            {{ $product->category->name ?? 'Uncategorized' }}
                        </span>
                        <span class="badge {{ $product->stock > 10 ? 'bg-success' : 'bg-danger' }} bg-opacity-10 {{ $product->stock > 10 ? 'text-success' : 'text-danger' }} rounded-pill px-2">
                            স্টক: {{ $product->stock > 50 ? '৫০+' : $product->stock }}
                        </span>
                    </div>

                    <h6 class="fw-bold text-dark mb-2">
                        <a href="{{ route('reseller.products.show', $product->slug) }}" class="text-decoration-none text-dark stretched-link">
                            {{ Str::limit($product->name, 45) }}
                        </a>
                    </h6>

                    <div class="mb-3">
                        <span class="profit-chip">
                            <i class="fas fa-coins text-success"></i> লাভ: ৳{{ number_format($product->profit, 0) }}
                        </span>
                    </div>

                    <div class="mt-auto pt-3 border-top border-light">
                        <div class="d-flex justify-content-between align-items-end mb-3">
                            <div>
                                <small class="text-secondary d-block" style="font-size: 11px;">রিসেলার প্রাইস</small>
                                <div class="d-flex align-items-center gap-2">
                                    <span class="fw-bold fs-5 text-primary">৳ {{ number_format($product->reseller_price, 0) }}</span>
                                    @if($product->old_price > $product->reseller_price)
                                        <small class="text-muted text-decoration-line-through">৳{{ number_format($product->old_price, 0) }}</small>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <div class="d-flex gap-2 position-relative" style="z-index: 2;">
                            <button type="button" class="btn-action-circle" onclick="copyProductLink('{{ route('product', $product->slug) }}')" title="লিংক কপি করুন">
                                <i class="fas fa-link"></i>
                            </button>
                            
                            <a href="{{ route('reseller.products.show', $product->slug) }}" class="btn-action-circle" title="বিস্তারিত দেখুন">
                                <i class="fas fa-eye"></i>
                            </a>

                            {{-- Always navigate to product details page when clicking order --}}
                            <a href="{{ route('reseller.products.show', $product->slug) }}" class="btn btn-primary btn-sm rounded-pill w-100 d-flex align-items-center justify-content-center fw-bold shadow-sm" style="background: var(--primary-gradient); border:none; text-decoration: none;">
                                <i class="fas fa-shopping-cart me-2"></i> অর্ডার
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @empty
        <div class="col-12">
            <div class="card border-0 shadow-sm rounded-4 py-5 text-center">
                <div class="card-body">
                    <div class="mb-3">
                        <i class="fas fa-search fa-4x text-muted opacity-25"></i>
                    </div>
                    <h4 class="fw-bold text-secondary">কোন পণ্য পাওয়া যায়নি</h4>
                    <p class="text-muted">আপনার সার্চ ফিল্টার পরিবর্তন করে আবার চেষ্টা করুন</p>
                    <a href="{{ route('reseller.products.index') }}" class="btn btn-primary rounded-pill px-4 mt-2">
                        <i class="fas fa-sync-alt me-2"></i> সব পণ্য দেখুন
                    </a>
                </div>
            </div>
        </div>
        @endforelse
    </div>

@if($products->hasPages())
<div class="d-flex justify-content-center mt-5">
    <style>
        .custom-pagination .page-link {
            border: none;
            margin: 0 5px;
            border-radius: 30px !important; /* ক্যাপসুল শেপ */
            color: #555;
            font-weight: 600;
            padding: 8px 16px;
            background: #f8f9fa;
            transition: all 0.3s ease;
        }
        .custom-pagination .page-link:hover {
            background: #e2e6ea;
            color: #333;
            transform: translateY(-2px);
        }
        .custom-pagination .page-item.active .page-link {
            background: #4f46e5; /* আপনার প্রাইমারি কালার */
            color: #fff;
            box-shadow: 0 4px 10px rgba(79, 70, 229, 0.3);
        }
        .custom-pagination .page-item.disabled .page-link {
            background: #fff;
            color: #ccc;
        }
    </style>

    <nav aria-label="Page navigation">
        <ul class="pagination custom-pagination mb-0">
            {{-- Previous --}}
            @if ($products->onFirstPage())
                <li class="page-item disabled"><span class="page-link">&laquo; Prev</span></li>
            @else
                <li class="page-item"><a class="page-link" href="{{ $products->previousPageUrl() }}">&laquo; Prev</a></li>
            @endif

            {{-- Numbers --}}
            @foreach(range(1, $products->lastPage()) as $i)
                @if($i >= $products->currentPage() - 2 && $i <= $products->currentPage() + 2)
                    @if ($i == $products->currentPage())
                        <li class="page-item active"><span class="page-link">{{ $i }}</span></li>
                    @else
                        <li class="page-item"><a class="page-link" href="{{ $products->url($i) }}">{{ $i }}</a></li>
                    @endif
                @endif
            @endforeach

            {{-- Next --}}
            @if ($products->hasMorePages())
                <li class="page-item"><a class="page-link" href="{{ $products->nextPageUrl() }}">Next &raquo;</a></li>
            @else
                <li class="page-item disabled"><span class="page-link">Next &raquo;</span></li>
            @endif
        </ul>
    </nav>
</div>
@endif

@endsection

@push('scripts')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    // Add to Cart for Reseller
    function addToCartReseller(event, productId) {
        event.preventDefault();
        
        var form = event.target;
        var formData = new FormData(form);
        
        // Show loading
        var btn = form.querySelector('button[type="submit"]');
        var originalText = btn.innerHTML;
        btn.disabled = true;
        btn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i> যোগ করা হচ্ছে...';
        
        fetch('{{ route("reseller.cart.add.ajax") }}', {
            method: 'POST',
            body: formData,
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Show success toast
                showToast('সফল!', 'প্রোডাক্ট কার্টে যোগ করা হয়েছে', 'success');
                
                // Update cart count in sidebar
                updateCartCount(data.cart_count);
                
                // Reload page after 1 second to show updated cart
                setTimeout(function() {
                    window.location.reload();
                }, 1500);
            } else {
                showToast('ত্রুটি!', data.message || 'কার্টে যোগ করতে সমস্যা হয়েছে', 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showToast('ত্রুটি!', 'কার্টে যোগ করতে সমস্যা হয়েছে', 'error');
        })
        .finally(() => {
            btn.disabled = false;
            btn.innerHTML = originalText;
        });
        
        return false;
    }
    
    // Update Cart Count in Sidebar
    function updateCartCount(count) {
        // Update sidebar cart badge
        var sidebarBadge = document.getElementById('sidebar-cart-badge');
        if (sidebarBadge) {
            if (count > 0) {
                sidebarBadge.textContent = count;
                sidebarBadge.style.display = 'inline-block';
            } else {
                sidebarBadge.style.display = 'none';
            }
        }
        
        // Also try to find by selector as fallback
        var fallbackBadge = document.querySelector('.sidebar .nav-link[href*="checkout"] .badge');
        if (fallbackBadge && !sidebarBadge) {
            if (count > 0) {
                fallbackBadge.textContent = count;
                fallbackBadge.style.display = 'inline-block';
            } else {
                fallbackBadge.style.display = 'none';
            }
        }
        
        // Update any other cart count elements
        var cartCountElements = document.querySelectorAll('.cart-count, #cart-count, [data-cart-count]');
        cartCountElements.forEach(function(el) {
            el.textContent = count;
        });
    }
    
    // Show Toast Notification
    function showToast(title, message, type) {
        let container = document.getElementById('toast-container');
        if (!container) {
            container = document.createElement('div');
            container.id = 'toast-container';
            container.className = 'position-fixed bottom-0 end-0 p-3';
            container.style.zIndex = '9999';
            document.body.appendChild(container);
        }

        const toastId = 'toast-' + Date.now();
        const bgColor = type === 'success' ? '#10b981' : '#ef4444';
        const icon = type === 'success' ? 'fa-check-circle' : 'fa-exclamation-circle';
        
        const toastHtml = `
            <div id="${toastId}" class="toast custom-toast align-items-center border-0" role="alert" aria-live="assertive" aria-atomic="true">
                <div class="d-flex">
                    <div class="toast-body d-flex align-items-center py-3">
                        <i class="fas ${icon} fa-lg me-3" style="color: ${bgColor};"></i>
                        <div>
                            <h6 class="fw-bold mb-0">${title}</h6>
                            <small class="text-muted">${message}</small>
                        </div>
                    </div>
                    <button type="button" class="btn-close me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
                </div>
            </div>
        `;
        
        container.insertAdjacentHTML('beforeend', toastHtml);
        const toastElement = document.getElementById(toastId);
        const bsToast = new bootstrap.Toast(toastElement);
        bsToast.show();

        toastElement.addEventListener('hidden.bs.toast', function () {
            toastElement.remove();
        });
    }

    // Copy Product Link with Advanced Toast
    function copyProductLink(url) {
        navigator.clipboard.writeText(url).then(function() {
            showToast('সফল!', 'লিংক কপি হয়েছে', 'success');
        });
    }
</script>
@endpush