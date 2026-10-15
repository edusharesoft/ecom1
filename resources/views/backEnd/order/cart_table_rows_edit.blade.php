@php $product_discount = 0; @endphp
@foreach($cartinfo as $value)
@php
    $lineDiscountKey = $value->options->details_id ?? ('row_' . $value->rowId);
    $lineDiscountVal = (float) ($value->options->product_discount ?? 0);

    $product = \App\Models\Product::find($value->id);
    $sizesList = collect();
    $colorsList = collect();
    if ($product) {
        $sizeIds = \App\Models\ProductVariantPrice::where('product_id', $product->id)->whereNotNull('size_id')->pluck('size_id')->unique()->filter();
        $colorIds = \App\Models\ProductVariantPrice::where('product_id', $product->id)->whereNotNull('color_id')->pluck('color_id')->unique()->filter();
        if ($sizeIds->isNotEmpty()) {
            $sizesList = \App\Models\Size::whereIn('id', $sizeIds)->get();
        }
        if ($colorIds->isNotEmpty()) {
            $colorsList = \App\Models\Color::whereIn('id', $colorIds)->get();
        }
        if ($sizesList->isEmpty() && $colorsList->isEmpty()) {
            $sizesList = $product->sizes ?? collect();
            $colorsList = $product->colors ?? collect();
        }
    }
    $hasSizes = $sizesList->isNotEmpty();
    $hasColors = $colorsList->isNotEmpty();
    $currentSizeId = $value->options->size_id ?? $value->options->product_size ?? '';
    $currentColorId = $value->options->color_id ?? $value->options->product_color ?? '';
@endphp
<tr>
    <td>
        <img class="oe-product-img" src="{{ asset($value->options->image) }}" alt="">
    </td>
    <td><strong>{{ $value->name }}</strong></td>
    <td>
        @if($hasColors)
            <select class="form-select form-select-sm cart-color-selector oe-variant-select" data-id="{{ $value->rowId }}" data-product-id="{{ $value->id }}">
                <option value="">রঙ নির্বাচন</option>
                @foreach($colorsList as $c)
                    <option value="{{ $c->id }}" {{ (string) $currentColorId === (string) $c->id ? 'selected' : '' }}>
                        {{ $c->colorName ?? $c->color_name ?? $c->name ?? 'N/A' }}
                    </option>
                @endforeach
            </select>
        @else
            <span class="text-muted small">{{ $value->options->product_color_name ?? 'N/A' }}</span>
        @endif
    </td>
    <td>
        @if($hasSizes)
            <select class="form-select form-select-sm cart-size-selector oe-variant-select" data-id="{{ $value->rowId }}" data-product-id="{{ $value->id }}">
                <option value="">সাইজ নির্বাচন</option>
                @foreach($sizesList as $s)
                    <option value="{{ $s->id }}" {{ (string) $currentSizeId === (string) $s->id ? 'selected' : '' }}>
                        {{ $s->sizeName ?? $s->size_name ?? $s->name ?? 'N/A' }}
                    </option>
                @endforeach
            </select>
        @else
            <span class="text-muted small">{{ $value->options->product_size_name ?? 'N/A' }}</span>
        @endif
    </td>
    <td class="text-center">
        <div class="oe-qty-control">
            <button type="button" class="oe-qty-btn cart_decrement" value="{{ $value->qty }}" data-id="{{ $value->rowId }}" aria-label="কমান">−</button>
            <input type="text" value="{{ $value->qty }}" readonly class="oe-qty-input" />
            <button type="button" class="oe-qty-btn cart_increment" value="{{ $value->qty }}" data-id="{{ $value->rowId }}" aria-label="বাড়ান">+</button>
        </div>
    </td>
    <td class="text-end">৳{{ number_format($value->price, 2) }}</td>
    <td class="text-center">
        <input type="number"
               class="product_discount"
               value="{{ $lineDiscountVal }}"
               data-id="{{ $value->rowId }}"
               data-line-key="{{ $lineDiscountKey }}"
               min="0"
               step="0.01">
        <input type="hidden"
               name="line_discount[{{ $lineDiscountKey }}]"
               value="{{ $lineDiscountVal }}"
               class="line-discount-hidden">
    </td>
    <td class="text-end fw-semibold">৳{{ number_format(($value->price - $value->options->product_discount) * $value->qty, 2) }}</td>
    <td class="text-center">
        <button type="button" class="btn btn-outline-danger btn-sm btn-remove cart_remove" data-id="{{ $value->rowId }}" title="সরান">
            <i class="fa fa-times"></i>
        </button>
    </td>
</tr>
@php
    $product_discount += $value->options->product_discount * $value->qty;
@endphp
@endforeach
@php Session::put('product_discount', $product_discount); @endphp
