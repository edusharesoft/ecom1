@extends('reseller.layouts.app')

@section('title', $product->name)
@section('page-title', 'প্রোডাক্ট বিস্তারিত')

@push('styles')
<style>
    .product-detail-card {
        background: #fff;
        border-radius: 16px;
        padding: 24px;
        border: 1px solid #e2e8f0;
        box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.05);
    }

    .product-main-image {
        width: 100%;
        height: 400px;
        object-fit: cover;
        border-radius: 12px;
        background: #f8fafc;
    }

    .product-thumbnail {
        width: 80px;
        height: 80px;
        object-fit: cover;
        border-radius: 8px;
        cursor: pointer;
        border: 2px solid transparent;
        transition: all 0.2s;
    }

    .product-thumbnail:hover,
    .product-thumbnail.active {
        border-color: #4f46e5;
    }

    .profit-badge {
        background: #ecfdf5;
        color: #059669;
        padding: 8px 16px;
        border-radius: 8px;
        font-weight: 700;
        display: inline-block;
    }

    .price-section {
        background: #f8fafc;
        padding: 20px;
        border-radius: 12px;
        border: 2px solid #e2e8f0;
    }
</style>
@endpush

@section('content')

    <div class="row g-4">
        <div class="col-lg-8">
            <!-- Product Images -->
            <div class="product-detail-card mb-4">
                <div class="row g-3">
                    <div class="col-md-2">
                        <div class="d-flex flex-column gap-2">
                            @if($product->images && $product->images->count() > 0)
                                @foreach($product->images->take(4) as $image)
                                <img src="{{ asset($image->image) }}" 
                                     class="product-thumbnail {{ $loop->first ? 'active' : '' }}" 
                                     onclick="changeMainImage('{{ asset($image->image) }}', this)"
                                     alt="Product Image">
                                @endforeach
                            @elseif($product->image && $product->image->image)
                                <img src="{{ asset($product->image->image) }}" 
                                     class="product-thumbnail active" 
                                     onclick="changeMainImage('{{ asset($product->image->image) }}', this)"
                                     alt="Product Image">
                            @endif
                        </div>
                    </div>
                    <div class="col-md-10">
                        <img id="mainProductImage" 
                             src="{{ asset($product->image && $product->image->image ? $product->image->image : 'storage/uploads/placeholder.png') }}" 
                             class="product-main-image" 
                             alt="{{ $product->name }}">
                    </div>
                </div>
            </div>

            <!-- Product Details -->
            <div class="product-detail-card">
                <h4 class="fw-bold mb-3">{{ $product->name }}</h4>
                
                <div class="mb-3">
                    <span class="badge bg-primary me-2">{{ $product->category->name ?? 'N/A' }}</span>
                    @if($product->brand)
                        <span class="badge bg-secondary">{{ $product->brand->name }}</span>
                    @endif
                    @if($product->product_code)
                        <span class="badge bg-light text-dark">Code: {{ $product->product_code }}</span>
                    @endif
                </div>

                <div class="mb-4">
                    <h6 class="fw-bold mb-2">বর্ণনা</h6>
                    <div class="text-muted">
                        {!! $product->description ?? 'কোন বর্ণনা নেই' !!}
                    </div>
                </div>

                @if($product->note)
                <div class="alert alert-info">
                    <strong>Note:</strong> {{ $product->note }}
                </div>
                @endif
            </div>
        </div>

        <div class="col-lg-4">
            <!-- Price & Order Section -->
            <div class="product-detail-card mb-4">
                <div class="price-section text-center mb-4">
                    <small class="text-secondary d-block mb-2" style="font-size: 12px;">রিসেলার প্রাইস</small>
                    <h2 class="fw-bold text-primary mb-0">৳ {{ number_format($product->reseller_price, 0) }}</h2>
                    @if($product->old_price && $product->old_price > $product->reseller_price)
                    <small class="text-muted text-decoration-line-through">
                        ৳{{ number_format($product->old_price, 0) }}
                    </small>
                    @endif
                </div>

                <div class="mb-4 text-center">
                    <div class="profit-badge">
                        <i class="fas fa-coins me-1"></i> আপনার লাভ: ৳{{ number_format($product->profit, 0) }}
                    </div>
                </div>

                <div class="row g-2 mb-4">
                    <div class="col-6">
                        <div class="text-center p-3 bg-light rounded">
                            <small class="text-muted d-block">স্টক</small>
                            <strong class="text-dark" id="stock-display">{{ $product->stock }}</strong>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="text-center p-3 bg-light rounded">
                            <small class="text-muted d-block">রেগুলার প্রাইস</small>
                            <strong class="text-dark">৳{{ number_format($product->new_price, 0) }}</strong>
                        </div>
                    </div>
                </div>

                {{-- Size & Color Selectors --}}
                @php
                    $productcolors = $product->variantPrices->pluck('color')->unique('id')->filter();
                    $productsizes = $product->variantPrices->pluck('size')->unique('id')->filter();
                @endphp

                @if($productcolors->count() > 0 || $productsizes->count() > 0)
                <div class="mb-4">
                    {{-- Color Selector --}}
                    @if($productcolors->count() > 0)
                    <div class="mb-3">
                        <label class="form-label fw-semibold mb-2">কালর নির্বাচন করুন <span class="text-danger">*</span></label>
                        <div class="d-flex flex-wrap gap-2">
                            @foreach($productcolors as $procolor)
                            <div class="form-check form-check-inline">
                                <input class="form-check-input color-radio" 
                                       type="radio" 
                                       name="product_color" 
                                       id="color-{{ $procolor->id }}" 
                                       value="{{ $procolor->id }}"
                                       required>
                                <label class="form-check-label" for="color-{{ $procolor->id }}">
                                    <span class="badge" style="background-color: {{ $procolor->color ?? '#ccc' }}; padding: 8px 12px; border-radius: 4px;">
                                        {{ $procolor->colorName ?? $procolor->name ?? 'Color' }}
                                    </span>
                                </label>
                            </div>
                            @endforeach
                        </div>
                    </div>
                    @endif

                    {{-- Size Selector --}}
                    @if($productsizes->count() > 0)
                    <div class="mb-3">
                        <label class="form-label fw-semibold mb-2">সাইজ নির্বাচন করুন <span class="text-danger">*</span></label>
                        <select name="product_size" id="product_size" class="form-select size-select" required>
                            <option value="">সাইজ নির্বাচন করুন</option>
                            @foreach($productsizes as $prosize)
                            <option value="{{ $prosize->id }}" data-stock="{{ $prosize->pivot->stock ?? $product->stock }}">
                                {{ $prosize->sizeName ?? $prosize->name }} 
                                @if(isset($prosize->pivot->stock))
                                    (স্টক: {{ $prosize->pivot->stock }})
                                @endif
                            </option>
                            @endforeach
                        </select>
                    </div>
                    @endif
                </div>
                @endif

                {{-- Quantity Selector --}}
                <div class="mb-4">
                    <label class="form-label fw-semibold mb-2">পরিমাণ</label>
                    <div class="input-group">
                        <button type="button" class="btn btn-outline-secondary" onclick="decreaseQty()">-</button>
                        <input type="number" 
                               name="qty" 
                               id="product_qty" 
                               class="form-control text-center" 
                               value="1" 
                               min="1" 
                               max="{{ $product->stock }}">
                        <button type="button" class="btn btn-outline-secondary" onclick="increaseQty()">+</button>
                    </div>
                </div>

                <div class="d-grid gap-2">
                    <form action="{{ route('reseller.cart.add') }}" method="POST" id="addToCartForm">
                        @csrf
                        <input type="hidden" name="product_id" value="{{ $product->id }}">
                        <input type="hidden" name="qty" id="hidden_qty" value="1">
                        <input type="hidden" name="product_size" id="hidden_size" value="">
                        <input type="hidden" name="product_color" id="hidden_color" value="">
                        <button type="submit" class="btn btn-primary btn-lg rounded-pill w-100">
                            <i class="fas fa-cart-plus me-2"></i> কার্টে যোগ করুন
                        </button>
                    </form>
                    <a href="{{ route('product', $product->slug) }}" target="_blank" class="btn btn-outline-primary rounded-pill">
                        <i class="fas fa-external-link-alt me-2"></i> মূল ওয়েবসাইটে দেখুন
                    </a>
                    <button class="btn btn-outline-secondary rounded-pill" onclick="copyProductLink('{{ route('product', $product->slug) }}')">
                        <i class="fas fa-link me-2"></i> লিঙ্ক কপি করুন
                    </button>
                </div>
            </div>

            <!-- Product Info -->
            <div class="product-detail-card">
                <h6 class="fw-bold mb-3">পণ্যের তথ্য</h6>
                <table class="table table-sm">
                    <tr>
                        <td class="text-muted">ক্যাটাগরি:</td>
                        <td class="fw-semibold">{{ $product->category->name ?? 'N/A' }}</td>
                    </tr>
                    @if($product->subcategory)
                    <tr>
                        <td class="text-muted">সাব-ক্যাটাগরি:</td>
                        <td class="fw-semibold">{{ $product->subcategory->subcategoryName ?? 'N/A' }}</td>
                    </tr>
                    @endif
                    @if($product->brand)
                    <tr>
                        <td class="text-muted">ব্র্যান্ড:</td>
                        <td class="fw-semibold">{{ $product->brand->name }}</td>
                    </tr>
                    @endif
                    <tr>
                        <td class="text-muted">স্টক:</td>
                        <td class="fw-semibold">
                            @if($product->stock > 0)
                                <span class="text-success">{{ $product->stock }} পিস</span>
                            @else
                                <span class="text-danger">স্টক নেই</span>
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <td class="text-muted">প্রোডাক্ট কোড:</td>
                        <td class="fw-semibold">{{ $product->product_code ?? 'N/A' }}</td>
                    </tr>
                </table>
            </div>
        </div>
    </div>

   

@endsection

@push('scripts')
<script>
    // Change Main Image
    function changeMainImage(src, element) {
        document.getElementById('mainProductImage').src = src;
        document.querySelectorAll('.product-thumbnail').forEach(img => {
            img.classList.remove('active');
        });
        element.classList.add('active');
    }

    // Copy Product Link
    function copyProductLink(url) {
        navigator.clipboard.writeText(url).then(function() {
            const toast = document.createElement('div');
            toast.className = 'position-fixed top-0 start-50 translate-middle-x mt-3 alert alert-success alert-dismissible fade show';
            toast.style.zIndex = '9999';
            toast.innerHTML = `
                <strong>সফল!</strong> Product link copied to clipboard!
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            `;
            document.body.appendChild(toast);
            setTimeout(() => toast.remove(), 3000);
        });
    }

    // Quantity Functions
    function increaseQty() {
        const qtyInput = document.getElementById('product_qty');
        const maxQty = parseInt(qtyInput.getAttribute('max')) || 999;
        const currentQty = parseInt(qtyInput.value) || 1;
        if (currentQty < maxQty) {
            qtyInput.value = currentQty + 1;
            document.getElementById('hidden_qty').value = qtyInput.value;
        }
    }

    function decreaseQty() {
        const qtyInput = document.getElementById('product_qty');
        const currentQty = parseInt(qtyInput.value) || 1;
        if (currentQty > 1) {
            qtyInput.value = currentQty - 1;
            document.getElementById('hidden_qty').value = qtyInput.value;
        }
    }

    // Update hidden fields and validate before submit
    document.getElementById('addToCartForm').addEventListener('submit', function(e) {
        const qtyInput = document.getElementById('product_qty');
        const sizeSelect = document.getElementById('product_size');
        const colorRadios = document.querySelectorAll('.color-radio:checked');
        
        // Update hidden fields
        document.getElementById('hidden_qty').value = qtyInput.value;
        
        if (sizeSelect) {
            document.getElementById('hidden_size').value = sizeSelect.value;
        }
        
        if (colorRadios.length > 0) {
            document.getElementById('hidden_color').value = colorRadios[0].value;
        }

        // Validate size/color if product has variants
        @if($productcolors->count() > 0 || $productsizes->count() > 0)
            @if($productcolors->count() > 0)
            if (colorRadios.length === 0) {
                e.preventDefault();
                alert('অনুগ্রহ করে কালর নির্বাচন করুন');
                return false;
            }
            @endif
            
            @if($productsizes->count() > 0)
            if (!sizeSelect.value) {
                e.preventDefault();
                alert('অনুগ্রহ করে সাইজ নির্বাচন করুন');
                return false;
            }
            @endif
        @endif
    });

    // Update stock display when size changes
    @if($productsizes->count() > 0)
    document.getElementById('product_size').addEventListener('change', function() {
        const selectedOption = this.options[this.selectedIndex];
        const stock = selectedOption.getAttribute('data-stock') || {{ $product->stock }};
        document.getElementById('stock-display').textContent = stock;
        
        // Update max quantity
        const qtyInput = document.getElementById('product_qty');
        qtyInput.setAttribute('max', stock);
        if (parseInt(qtyInput.value) > stock) {
            qtyInput.value = stock;
            document.getElementById('hidden_qty').value = stock;
        }
    });
    @endif

    // Update quantity input
    document.getElementById('product_qty').addEventListener('change', function() {
        document.getElementById('hidden_qty').value = this.value;
    });
</script>
@endpush
