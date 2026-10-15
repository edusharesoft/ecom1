@php
    $subtotalRaw = Cart::instance('pos_shopping')->subtotal();
    $subtotalNum = (float) str_replace([',', '.00'], '', (string) $subtotalRaw);
    $shippingNum = (float) (Session::get('pos_shipping') ?? 0);
    $lineProductDiscount = 0;
    foreach (Cart::instance('pos_shopping')->content() as $cartLine) {
        $lineProductDiscount += (float) ($cartLine->options->product_discount ?? 0) * $cartLine->qty;
    }
    Session::put('product_discount', $lineProductDiscount);
    $totalDiscount = (float) (Session::get('pos_discount') ?? 0) + $lineProductDiscount;
    $grandTotal = max(0, $subtotalNum + $shippingNum - $totalDiscount);
    $orderId = request()->get('order_id');
    $advancePaid = 0;
    $dueAmount = $grandTotal;
    if ($orderId) {
        $paidAmount = \App\Models\Payment::where('order_id', $orderId)->sum('amount');
        if ($paidAmount > 0 && $paidAmount < $grandTotal) {
            $advancePaid = $paidAmount;
            $dueAmount = $grandTotal - $advancePaid;
        }
    }
@endphp
<tr>
    <td>সাবটোটাল</td>
    <td class="text-end">৳{{ number_format($subtotalNum, 2) }}</td>
</tr>
<tr>
    <td>ডেলিভারি চার্জ</td>
    <td class="text-end">৳{{ number_format($shippingNum, 2) }}</td>
</tr>
<tr>
    <td>মোট ছাড়</td>
    <td class="text-end text-danger">−৳{{ number_format($totalDiscount, 2) }}</td>
</tr>
<tr class="oe-summary-total">
    <td>মোট পরিশোধ</td>
    <td class="text-end">৳{{ number_format($grandTotal, 2) }}</td>
</tr>
@if($advancePaid > 0)
<tr>
    <td>অগ্রিম পরিশোধ</td>
    <td class="text-end text-success">৳{{ number_format($advancePaid, 2) }}</td>
</tr>
<tr class="oe-summary-due">
    <td>বাকি</td>
    <td class="text-end">৳{{ number_format($dueAmount, 2) }}</td>
</tr>
@endif
