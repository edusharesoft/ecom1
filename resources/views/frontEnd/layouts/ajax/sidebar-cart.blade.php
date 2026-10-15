@php
    $subtotal = Cart::instance('shopping')->subtotal();
    $subtotal = floatval(preg_replace('/[^\d.]/', '', $subtotal));
    $primaryColor = optional($generalsetting)->primary_color ?? '#007bff';
@endphp

<div class="sidebar-cart-header">
    <button type="button" class="sidebar-cart-close" onclick="closeSidebarCart()" aria-label="বন্ধ করুন">
        <i class="fa-solid fa-times"></i>
    </button>
    <h3 class="sidebar-cart-title">আপনার কার্ট</h3>
</div>

<div class="sidebar-cart-body">
    @if(Cart::instance('shopping')->count() > 0)
        @foreach(Cart::instance('shopping')->content() as $value)
        <div class="sidebar-cart-item">
            <div class="sidebar-cart-item-img">
                <a href="{{ route('product', $value->options->slug ?? '#') }}">
                    <img src="{{ asset($value->options->image ?? 'public/uploads/default.webp') }}" alt="{{ $value->name }}">
                </a>
            </div>
            <div class="sidebar-cart-item-details">
                <a href="{{ route('product', $value->options->slug ?? '#') }}" class="sidebar-cart-item-title">
                    {{ Str::limit($value->name, 45) }}
                </a>
                @if(!empty($value->options->product_size) || !empty($value->options->product_color))
                    <p class="sidebar-cart-item-variant" style="font-size: 11px; color: #666; margin: 2px 0;">
                        @if(!empty($value->options->product_size)) Size: {{ $value->options->product_size }} @endif
                        @if(!empty($value->options->product_color)) | Color: {{ $value->options->product_color }} @endif
                    </p>
                @endif
                <p class="sidebar-cart-item-price">৳ {{ $value->price }}</p>
                @if(!empty($value->options->old_price) && $value->options->old_price > $value->price)
                    @php $savings = $value->options->old_price - $value->price; @endphp
                    <p class="sidebar-cart-item-savings">৳ {{ number_format($savings, 0) }} ছাড়</p>
                @endif
                <div class="sidebar-cart-qty">
                    <button type="button" class="sidebar-qty-btn cart_decrement" data-id="{{ $value->rowId }}">−</button>
                    <span class="sidebar-qty-num">{{ $value->qty }}</span>
                    <button type="button" class="sidebar-qty-btn cart_increment" data-id="{{ $value->rowId }}">+</button>
                </div>
                <button type="button" class="sidebar-cart-item-remove cart_remove" data-id="{{ $value->rowId }}" title="রিমুভ করুন">
                    <i class="fa-solid fa-trash-can"></i>
                </button>
            </div>
        </div>
        @endforeach
    @else
        <div class="sidebar-cart-empty">
            <i class="fa-solid fa-cart-shopping"></i>
            <p>আপনার কার্ট খালি</p>
            <a href="{{ route('shop') }}" class="btn btn-sm btn-outline-primary mt-2">শপিং করুন</a>
        </div>
    @endif
</div>

@if(Cart::instance('shopping')->count() > 0)
<div class="sidebar-cart-footer">
    <div class="sidebar-cart-total">
        <span class="sidebar-cart-total-label">সর্বমোট</span>
        <span class="sidebar-cart-total-amount">৳ {{ number_format($subtotal, 0) }}</span>
    </div>
    <a href="{{ route('customer.checkout') }}" class="sidebar-cart-checkout-btn">অর্ডার করুন</a>
</div>
@endif
