@foreach($cartinfo as $key=>$value)
<tr>
  <td><img height="30" src="{{asset($value->options->image)}}"></td>
  <td>
      <div class="fw-semibold">{{$value->name}}</div>
        @php
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
            $currentSizeId = $value->options->size_id ?? '';
            $currentColorId = $value->options->color_id ?? '';
        @endphp

       @if($hasSizes || $hasColors)
        <div class="d-flex flex-column gap-1 mt-2">
            @if($hasSizes)
            <div>
                <label class="form-label small text-muted mb-0" style="font-size:11px">Size</label>
                <select class="form-select form-select-sm cart-size-selector" data-id="{{ $value->rowId }}" data-product-id="{{ $value->id }}" style="min-width:100px">
                    <option value="">Select</option>
                    @foreach($sizesList as $s)
                    <option value="{{ $s->id }}" {{ $currentSizeId == $s->id ? 'selected' : '' }}>{{ $s->sizeName ?? $s->size_name ?? 'N/A' }}</option>
                    @endforeach
                </select>
            </div>
            @endif
            @if($hasColors)
            <div>
                <label class="form-label small text-muted mb-0" style="font-size:11px">Color</label>
                <select class="form-select form-select-sm cart-color-selector" data-id="{{ $value->rowId }}" data-product-id="{{ $value->id }}" style="min-width:100px">
                    <option value="">Select</option>
                    @foreach($colorsList as $c)
                    <option value="{{ $c->id }}" {{ $currentColorId == $c->id ? 'selected' : '' }}>{{ $c->colorName ?? $c->color_name ?? 'N/A' }}</option>
                    @endforeach
                </select>
            </div>
            @endif
        </div>
        @endif
  </td>
  <td>
    <div class="qty-cart vcart-qty">
      <div class="quantity">
          <button class="minus cart_decrement" value="{{$value->qty}}" data-id="{{$value->rowId}}">-</button>
          <input type="text" value="{{$value->qty}}" readonly />
          <button class="plus cart_increment" value="{{$value->qty}}" data-id="{{$value->rowId}}">+</button>
      </div>
  </div>
  </td>
  <td>{{$value->price}}</td>
  <td>{{$value->price * $value->qty}}</td>
  <td class="text-center">
    <button type="button" class="btn btn-light btn-sm cart_remove" data-id="{{$value->rowId}}">
        <i class="fa fa-times text-danger"></i>
    </button>
  </td>
</tr>
@endforeach
