@extends('frontEnd.layouts.master')
@section('title','Shopping Cart')
@section('content')

<section class="breadcrumb-section">
    <div class="container">
        <div class="row">
            <div class="col-sm-12">
                <div class="custom-breadcrumb">
                    <ul>
                        <li><a href="{{route('home')}}">Home </a></li>
                        <li>
                            <a><i class="fa-solid fa-angles-right"></i> </a>
                        </li>
                        <li><a href="">Shopping Cart</a></li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</section>
<!-- breadcrumb end -->

<section class="vcart-section">
    @php
        $subtotal = Cart::instance('shopping')->subtotal();
        $subtotal = str_replace(',', '', $subtotal);
        $subtotal = str_replace('.00', '', $subtotal);
        view()->share('subtotal', $subtotal);

        $shipping   = Session::get('shipping') ? Session::get('shipping') : 0;
        $discount   = Session::get('discount') ? Session::get('discount') : 0;
        $grandTotal = ($subtotal + $shipping) - $discount;
        $cartCount  = Cart::instance('shopping')->count();
    @endphp

    <div class="container">
        <div class="row" id="cartlist">
            <div class="col-sm-9">
                <div class="vcart-inner">
                    <div class="cart-title">
                        <h4>Shopping Cart</h4>
                    </div>
                    <div class="vcart-content">
                        <div class="table-responsive">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>Image</th>
                                        <th>Product</th>
                                        <th>Price</th>
                                        <th>Qty</th>
                                        <th>Total</th>
                                        <th>Remove</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($data as $value)
                                    <tr
                                        data-row-id="{{ $value->rowId }}"
                                        data-product-id="{{ $value->id }}"
                                        data-product-name="{{ e($value->name) }}"
                                        data-price="{{ (float) $value->price }}"
                                    >
                                        <td>
                                            <img height="30" src="{{asset($value->options->image)}}" alt="" />
                                        </td>
                                        <td class="cart_name">{{$value->name}}</td>
                                        <td>{{$value->price}} ৳</td>
                                        <td>
                                            <div class="qty-cart vcart-qty">
                                                <div class="quantity">
                                                    <button class="minus cart_decrement" data-id="{{$value->rowId}}">-</button>
                                                    <input type="text" value="{{$value->qty}}" readonly />
                                                    <button class="plus cart_increment" data-id="{{$value->rowId}}">+</button>
                                                </div>
                                            </div>
                                        </td>
                                        <td>{{$value->price * $value->qty}} ৳</td>
                                        <td>
                                            <button class="remove-cart cart_remove" data-id="{{$value->rowId}}">
                                                <i data-feather="x"></i>
                                            </button>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <div class="coupon-form">
                    <form action="">
                        <input type="text" placeholder="apply coupon" id="cartCoupon" />
                        <button type="submit" id="applyCouponBtn">Apply</button>
                    </form>
                </div>
            </div>

            <div class="col-sm-3">
                <div class="cart-summary">
                    <h5>Cart Summary</h5>
                    <table class="table">
                        <tbody>
                            <tr>
                                <td>Items</td>
                                <td>{{ $cartCount }} (qty)</td>
                            </tr>
                            <tr>
                                <td>Total</td>
                                <td>৳{{ $subtotal }}</td>
                            </tr>
                            <tr>
                                <td>Shipping</td>
                                <td>৳{{ $shipping }}</td>
                            </tr>
                            <tr>
                                <td>Discount</td>
                                <td>৳{{ $discount }}</td>
                            </tr>
                            <tr>
                                <td>Total</td>
                                <td>৳{{ $grandTotal }}</td>
                            </tr>
                        </tbody>
                    </table>
                    {{-- Checkout button – ট্র্যাকিংয়ের জন্য ID দিলাম --}}
                    <a href="{{route('customer.checkout')}}" class="go_cart" id="checkoutButton">
                        PROCESS TO CHECKOUT
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>

@endsection

@push('script')
<script>
// =============================
//   CART PAGE TRACKING SCRIPT
// =============================

window.dataLayer = window.dataLayer || [];

(function() {

    // ---- Cart items array (GA4 / GTM এর জন্য) ----
    var cartItems = [
        @foreach($data as $item)
        {
            item_id: '{{ $item->id }}',
            item_name: @json($item->name),
            price: {{ (float) $item->price }},
            quantity: {{ (int) $item->qty }}
            // চাইলে এখানে brand, category ইত্যাদি অ্যাড করা যাবে
        }@if(!$loop->last),@endif
        @endforeach
    ];

    var cartValue     = {{ (float) $grandTotal }};
    var cartItemCount = {{ (int) $cartCount }};
    var currency      = 'BDT';

    if (typeof window.EcomTracking !== 'undefined') {
        EcomTracking.viewCart({
            value: cartValue,
            items: cartItems.map(function (i) {
                return { id: i.item_id, name: i.item_name, price: i.price, qty: i.quantity };
            })
        });
    } else {
        window.dataLayer.push({
            event: 'view_cart',
            ecommerce: { currency: currency, value: cartValue, items: cartItems }
        });
        if (typeof fbq === 'function') {
            fbq('trackCustom', 'ViewCart', { value: cartValue, currency: currency, num_items: cartItemCount });
        }
        if (typeof ttq !== 'undefined') {
            ttq.track('ViewContent', {
                content_type: 'product_group',
                value: cartValue,
                currency: currency,
                quantity: cartItemCount
            });
        }
    }

    function getItemData($row) {
        return {
            item_id: $row.data('product-id'),
            item_name: $row.data('product-name'),
            price: parseFloat($row.data('price')) || 0
        };
    }

    // Helper: GA4 + FB Pixel push
    function pushCartEvent(type, item, quantityChange) {

        var eventNameGtm;
        var qty = Math.abs(quantityChange) || 1;
        var value = (item.price || 0) * qty;

        if (type === 'add_to_cart') {
            eventNameGtm = 'add_to_cart';
        } else if (type === 'remove_from_cart') {
            eventNameGtm = 'remove_from_cart';
        } else {
            eventNameGtm = 'update_cart';
        }

        if (type === 'add_to_cart' && typeof window.EcomTracking !== 'undefined') {
            EcomTracking.addToCart({
                items: [{ id: item.item_id, name: item.item_name, price: item.price, qty: qty }],
                value: value
            });
            return;
        }

        // ---- GA4 / GTM ----
        window.dataLayer.push({
            event: eventNameGtm,
            ecommerce: {
                currency: currency,
                value: value,
                items: [
                    Object.assign({}, item, { quantity: qty })
                ]
            }
        });

        // ---- Facebook Pixel ----
        if (typeof fbq === 'function') {
            if (type === 'add_to_cart') {
                fbq('track', 'AddToCart', {
                    value: value,
                    currency: currency,
                    content_ids: [item.item_id],
                    content_name: item.item_name,
                    contents: [
                        { id: item.item_id, quantity: qty }
                    ]
                });
                if (typeof ttq !== 'undefined') {
                    ttq.track('AddToCart', {
                        content_type: 'product',
                        value: value,
                        currency: currency,
                        contents: [{ content_id: String(item.item_id), content_name: item.item_name, quantity: qty, price: item.price }]
                    });
                }
            } else if (type === 'remove_from_cart') {
                fbq('trackCustom', 'RemoveFromCart', {
                    value: value,
                    currency: currency,
                    content_ids: [item.item_id],
                    content_name: item.item_name,
                    contents: [
                        { id: item.item_id, quantity: qty }
                    ]
                });
            } else {
                fbq('trackCustom', 'UpdateCart', {
                    value: value,
                    currency: currency,
                    content_ids: [item.item_id],
                    content_name: item.item_name,
                    contents: [
                        { id: item.item_id, quantity: qty }
                    ]
                });
            }
        }
    }

    // ৩) Checkout button -> InitiateCheckout + begin_checkout
    var checkoutBtn = document.getElementById('checkoutButton');
    if (checkoutBtn) {
        checkoutBtn.addEventListener('click', function() {
            var lineItems = cartItems.map(function (i) {
                return { id: i.item_id, name: i.item_name, price: i.price, qty: i.quantity };
            });
            if (typeof window.EcomTracking !== 'undefined') {
                EcomTracking.initiateCheckout({ items: lineItems, value: cartValue });
            } else {
                if (typeof fbq === 'function') {
                    fbq('track', 'InitiateCheckout', {
                        value: cartValue,
                        currency: currency,
                        num_items: cartItemCount,
                        content_ids: cartItems.map(function(i){ return i.item_id; }),
                        contents: cartItems.map(function(i){ return { id: i.item_id, quantity: i.quantity, item_price: i.price }; })
                    });
                }
                if (typeof ttq !== 'undefined') {
                    ttq.track('InitiateCheckout', {
                        value: cartValue,
                        currency: currency,
                        quantity: cartItemCount,
                        content_type: 'product'
                    });
                }
                window.dataLayer.push({
                    event: 'begin_checkout',
                    ecommerce: { currency: currency, value: cartValue, items: cartItems }
                });
            }
        });
    }

    // ৪) Qty Increment -> add_to_cart / update_cart
    $(document).on('click', '.cart_increment', function() {
        var $row = $(this).closest('tr');
        var item = getItemData($row);
        var currentQty = parseInt($row.find('input').val()) || 1;
        var newQty = currentQty + 1;

        // এখানে আমরা ধরছি increment মানে add_to_cart type event
        pushCartEvent('add_to_cart', item, newQty - currentQty);
    });

    // ৫) Qty Decrement -> update_cart (বা remove এর আগে quantity কমছে)
    $(document).on('click', '.cart_decrement', function() {
        var $row = $(this).closest('tr');
        var item = getItemData($row);
        var currentQty = parseInt($row.find('input').val()) || 1;
        var newQty = Math.max(currentQty - 1, 0);

        if (newQty < currentQty) {
            // Qty কমে গেলে update_cart
            pushCartEvent('update_cart', item, newQty - currentQty);
        }
    });

    // ৬) Remove button -> remove_from_cart
    $(document).on('click', '.cart_remove', function() {
        var $row = $(this).closest('tr');
        var item = getItemData($row);
        var currentQty = parseInt($row.find('input').val()) || 1;

        pushCartEvent('remove_from_cart', item, currentQty);
    });

    // ৭) Coupon Apply -> apply_coupon (GA4) + ApplyCoupon (FB)
    $('.coupon-form form').on('submit', function() {
        var code = $('#cartCoupon').val() || '';

        // GA4 / GTM
        window.dataLayer.push({
            event: 'apply_coupon',
            ecommerce: {
                coupon: code
            }
        });

        // Facebook Pixel
        if (typeof fbq === 'function') {
            fbq('trackCustom', 'ApplyCoupon', {
                coupon: code
            });
        }
        // preventDefault করা হয়নি, যাতে তোমার existing কুপন লজিক স্বাভাবিক মতোই কাজ করে
    });

})();
</script>
@endpush
