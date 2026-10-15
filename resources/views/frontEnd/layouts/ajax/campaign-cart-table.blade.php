@php
    $subtotal = Cart::instance('shopping')->subtotal();
    $subtotal = str_replace(',', '', $subtotal);
    $subtotal = str_replace('.00', '', $subtotal);
    $shipping = Session::get('shipping') ? Session::get('shipping') : 0;
@endphp
<table class="cart_table table table-bordered table-striped text-center mb-0">
    <thead>
        <tr>
            <th style="width: 40%;">প্রোডাক্ট</th>
            <th style="width: 20%;">পরিমাণ</th>
            <th style="width: 20%;">মূল্য</th>
        </tr>
    </thead>
    <tbody>
        @foreach(Cart::instance('shopping')->content() as $value)
            <tr>
                <td class="text-left">
                    <a style="font-size: 14px;" href="{{ route('product', $value->options->slug) }}">
                        <img src="{{ asset($value->options->image) }}" height="30" width="30" alt="">
                        {{ Str::limit($value->name, 20) }}
                    </a>
                    @if(!empty($value->options->product_color))
                        <div class="small text-muted mt-1">কালার: {{ $value->options->product_color }}</div>
                    @endif
                    @if(!empty($value->options->product_size))
                        <div class="small text-muted">সাইজ: {{ $value->options->product_size }}</div>
                    @endif
                </td>
                <td width="15%" class="cart_qty">
                    <div class="qty-cart vcart-qty">
                        <div class="quantity">
                            <button type="button" class="minus cart_decrement" data-id="{{ $value->rowId }}" data-campaign="1">-</button>
                            <input type="text" value="{{ $value->qty }}" readonly />
                            <button type="button" class="plus cart_increment" data-id="{{ $value->rowId }}" data-campaign="1">+</button>
                        </div>
                    </div>
                </td>
                <td><span class="alinur">৳</span> {{ $value->price * $value->qty }}</td>
            </tr>
        @endforeach
    </tbody>
    <tfoot>
        <tr>
            <th colspan="2" class="text-end px-4">মোট</th>
            <td>
                <span id="net_total"><span class="alinur">৳ </span><strong>{{ $subtotal }}</strong></span>
            </td>
        </tr>
        <tr>
            <th colspan="2" class="text-end px-4">ডেলিভারি চার্জ</th>
            <td>
                <span id="cart_shipping_cost"><span class="alinur">৳ </span><strong>{{ $shipping }}</strong></span>
            </td>
        </tr>
        <tr>
            <th colspan="2" class="text-end px-4">সর্বমোট</th>
            <td>
                <span id="grand_total"><span class="alinur">৳ </span><strong>{{ $subtotal + $shipping }}</strong></span>
            </td>
        </tr>
    </tfoot>
</table>
