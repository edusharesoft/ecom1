@php
    $subtotal = Cart::instance('shopping')->subtotal();
    $subtotal=str_replace(',','',$subtotal);
    $subtotal=str_replace('.00', '',$subtotal);
    view()->share('subtotal',$subtotal);
    $shipping = Session::get('shipping')?Session::get('shipping'):0;
    $discount = Session::get('discount')?Session::get('discount'):0;
@endphp

<h5>Cart Summary</h5>
    <table class="table">
        <tbody>
            <tr>
                <td>Items</td>
                <td>{{Cart::instance('shopping')->count()}} (qty)</td>
            </tr>
            <tr>
                <td>Total</td>
                <td>৳{{$subtotal}}</td>
            </tr>
            <tr>
                <td>Shipping</td>
                <td>৳{{$shipping}}</td>
            </tr>
            <tr>
                <td>Discount</td>
                <td>৳{{$discount}}</td>
            </tr>
            <tr>
                <td>Total</td>
                <td>৳{{($subtotal+$shipping) - $discount}}</td>
            </tr>
        </tbody>
    </table>