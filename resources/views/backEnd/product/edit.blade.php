@extends('backEnd.layouts.master')
@section('title','Product Edit')

@section('css')
<link href="{{asset('public/backEnd')}}/assets/libs/select2/css/select2.min.css" rel="stylesheet" type="text/css" />
<link href="{{asset('public/backEnd')}}/assets/libs/summernote/summernote-lite.min.css" rel="stylesheet" type="text/css" />

@include('backEnd.product.partials.product_form_styles')
@endsection

@section('content')
<div class="container-fluid product-form-page">
    <div class="pf-page-header">
        <div>
            <h4>প্রোডাক্ট সম্পাদনা</h4>
            <p class="pf-sub mb-0">{{ $edit_data->name }} — সকল অপশন ও ফিচার আগের মতোই থাকবে, শুধু UI আপডেট।</p>
        </div>
        <div class="pf-header-actions">
            <a href="{{ route('products.index') }}" class="pf-btn-manage"><i class="fe-list"></i> প্রোডাক্ট তালিকা</a>
        </div>
    </div>
    <form action="{{route('products.update')}}" method="POST" data-parsley-validate="" enctype="multipart/form-data" name="editForm">
        @csrf
        <input type="hidden" value="{{$edit_data->id}}" name="id" />

        <div class="row">
            <div class="col-lg-8">
                
                <div class="card mb-4">
                    <div class="card-body">
                        <div class="section-title"><i class="fe-info me-1"></i> Basic Information</div>

                        <div class="form-group mb-3">
                            <label for="name" class="form-label">Product Name *</label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror"
                                   name="name" value="{{$edit_data->name }}" id="name" required />
                            @error('name')
                            <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                            @enderror
                        </div>

                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label for="category_id" class="form-label">Categories *</label>
                                <select class="form-control form-select select2 @error('category_id') is-invalid @enderror"
                                        name="category_id" id="category_id" required>
                                    <optgroup>
                                        <option value="">Select..</option>
                                        @foreach($categories as $category)
                                            <option value="{{$category->id}}" @if($edit_data->category_id==$category->id) selected @endif>
                                                {{$category->name}}
                                            </option>
                                            @foreach ($category->childrenCategories as $childCategory)
                                                <option value="{{$childCategory->id}}" @if($edit_data->category_id==$childCategory->id) selected @endif>
                                                    - {{$childCategory->name}}
                                                </option>
                                            @endforeach
                                        @endforeach
                                    </optgroup>
                                </select>
                                @error('category_id')
                                <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                                @enderror
                            </div>

                            <div class="col-md-4 mb-3">
                                <label for="subcategory_id" class="form-label">SubCategories</label>
                                <select class="form-control form-select select2 @error('subcategory_id') is-invalid @enderror"
                                        id="subcategory_id" name="subcategory_id">
                                    <optgroup>
                                        <option value="">Select..</option>
                                        @foreach($subcategory as $value)
                                            <option value="{{$value->id}}" @if($edit_data->subcategory_id==$value->id) selected @endif>
                                                {{$value->subcategoryName}}
                                            </option>
                                        @endforeach
                                    </optgroup>
                                </select>
                                @error('subcategory_id')
                                <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                                @enderror
                            </div>

                            <div class="col-md-4 mb-3">
                                <label for="childcategory_id" class="form-label">Child Categories</label>
                                <select class="form-control form-select select2 @error('childcategory_id') is-invalid @enderror"
                                        id="childcategory_id" name="childcategory_id">
                                    <optgroup>
                                        <option value="">Select..</option>
                                        @foreach($childcategory as $value)
                                            <option value="{{$value->id}}" @if($edit_data->childcategory_id==$value->id) selected @endif>
                                                {{$value->childcategoryName}}
                                            </option>
                                        @endforeach
                                    </optgroup>
                                </select>
                                @error('childcategory_id')
                                <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group mb-3">
                            <div class="pf-desc-label-row">
                                <label for="description" class="form-label mb-0">Description</label>
                                @include('backEnd.product.partials.ai_description_button')
                            </div>
                            <textarea name="description" rows="6"
                                      class="summernote form-control @error('description') is-invalid @enderror">
                                {{$edit_data->description}}
                            </textarea>
                            @error('description')
                            <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                            @enderror
                        </div>

                        <div class="form-group mb-0">
                            <label for="note" class="form-label">Note</label>
                            <textarea name="note" rows="2"
                                      class="form-control @error('note') is-invalid @enderror">{{$edit_data->note}}</textarea>
                            @error('note')
                            <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="card mb-4">
                    <div class="card-body">
                        <div class="form-group mb-3">
                            <label class="d-block form-label">Wholesale Product</label>
                            <label class="switch">
                                <input type="checkbox" value="1" name="is_wholesale" id="is_wholesale" {{ old('is_wholesale', $edit_data->is_wholesale ?? 0) ? 'checked' : '' }}>
                                <span class="slider round"></span>
                            </label>
                        </div>
                    </div>
                </div>

                {{-- WHOLESALE PRICING TIERS --}}
                <div id="wholesale_area" style="{{ old('is_wholesale', $edit_data->is_wholesale ?? 0) ? 'display:block;' : 'display:none;' }}" class="card mb-4">
                    <div class="card-body">
                        <div class="section-title d-flex justify-content-between align-items-center">
                            <span><i class="fe-dollar-sign me-1"></i> Wholesale Pricing Tiers</span>
                            <button type="button" class="btn btn-sm btn-success add-wholesale-tier rounded-pill px-3"><i class="fa fa-plus me-1"></i> Add New Tier</button>
                        </div>
                        
                        <div id="wholesale-wrapper">
                            @if($wholesalePrices && $wholesalePrices->count() > 0)
                                @foreach($wholesalePrices as $key => $tier)
                                    <div class="variant-card">
                                        <div class="row align-items-end">
                                            <div class="col-md-3 mb-2">
                                                <label class="form-label">Min Quantity</label>
                                                <input type="number" name="wholesale_price[{{ $key }}][min_quantity]" class="form-control" 
                                                       value="{{ old('wholesale_price.'.$key.'.min_quantity', $tier->min_quantity) }}">
                                            </div>
                                            <div class="col-md-3 mb-2">
                                                <label class="form-label">Max Quantity</label>
                                                <input type="number" name="wholesale_price[{{ $key }}][max_quantity]" class="form-control" 
                                                       value="{{ old('wholesale_price.'.$key.'.max_quantity', $tier->max_quantity) }}" placeholder="Optional">
                                            </div>
                                            <div class="col-md-2 mb-2">
                                                <label class="form-label">Wholesale Price</label>
                                                <input type="number" step="0.01" name="wholesale_price[{{ $key }}][wholesale_price]" class="form-control" 
                                                       value="{{ old('wholesale_price.'.$key.'.wholesale_price', $tier->wholesale_price) }}">
                                            </div>
                                            <div class="col-md-2 mb-2">
                                                <label class="form-label">Stock Qty</label>
                                                <input type="number" name="wholesale_price[{{ $key }}][stock]" class="form-control" 
                                                       value="{{ old('wholesale_price.'.$key.'.stock', $tier->stock ?? 0) }}" placeholder="0">
                                            </div>
                                            <div class="col-md-2 mb-2">
                                                @if($loop->first)
                                                    <button type="button" class="btn btn-success add-wholesale-tier w-100"><i class="fa fa-plus"></i></button>
                                                @else
                                                    <button type="button" class="btn btn-danger btn-remove-wholesale w-100"><i class="fa fa-trash"></i></button>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            @else
                                <div class="variant-card">
                                    <div class="row align-items-end">
                                        <div class="col-md-3 mb-2">
                                            <label class="form-label">Min Quantity</label>
                                            <input type="number" name="wholesale_price[0][min_quantity]" class="form-control" placeholder="e.g. 10">
                                        </div>
                                        <div class="col-md-3 mb-2">
                                            <label class="form-label">Max Quantity</label>
                                            <input type="number" name="wholesale_price[0][max_quantity]" class="form-control" placeholder="e.g. 50 (optional)">
                                        </div>
                                        <div class="col-md-2 mb-2">
                                            <label class="form-label">Wholesale Price</label>
                                            <input type="number" step="0.01" name="wholesale_price[0][wholesale_price]" class="form-control" placeholder="0.00">
                                        </div>
                                        <div class="col-md-2 mb-2">
                                            <label class="form-label">Stock Qty</label>
                                            <input type="number" name="wholesale_price[0][stock]" class="form-control" placeholder="0">
                                        </div>
                                        <div class="col-md-2 mb-2">
                                            <button type="button" class="btn btn-success add-wholesale-tier w-100"><i class="fa fa-plus"></i></button>
                                        </div>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                {{-- VARIANT PRICE CARD --}}
                <div class="card mb-4" id="variant_section">
                    <div class="card-body">
                        <div class="section-title d-flex justify-content-between align-items-center">
                            <span><i class="fe-layers me-1"></i> Product Variants (Color & Size)</span>
                            <button type="button" class="btn btn-sm btn-success add-variant rounded-pill px-3"><i class="fa fa-plus me-1"></i> Add New Variant</button>
                        </div>

                        <div id="variant-wrapper">
                            @php
                                // Group variants by color_id, then by size_id
                                // First, group by color_id (including null colors)
                                $groupedByColor = $edit_data->variantPrices->groupBy(function($variant) {
                                    return $variant->color_id ?? 'no_color';
                                });
                                $variantIndex = 0;
                            @endphp
                            
                            @forelse($groupedByColor as $colorId => $variantsForColor)
                                @php
                                    // Get all size IDs for this color group
                                    $sizeIds = $variantsForColor->pluck('size_id')->filter()->toArray();
                                    $firstVariant = $variantsForColor->first();
                                @endphp
                                <div class="variant-card variant-item">
                                    <div class="row align-items-end">
                                        <div class="col-md-2 mb-2">
                                            <label class="form-label">Color</label>
                                            <select name="variant_price[{{ $variantIndex }}][color_id]" class="form-control select2 variant-color-select">
                                                <option value="">Select Color (Optional)</option>
                                                @foreach($totalcolors as $color)
                                                    <option value="{{ $color->id }}" {{ $colorId == $color->id ? 'selected' : '' }}>
                                                        {{ $color->colorName ?? $color->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>

                                        <div class="col-md-2 mb-2">
                                            <label class="form-label">Size</label>
                                            <select name="variant_price[{{ $variantIndex }}][size_id][]" class="form-control select2 variant-size-select" multiple>
                                                @foreach($totalsizes as $size)
                                                    <option value="{{ $size->id }}" {{ in_array($size->id, $sizeIds) ? 'selected' : '' }}>
                                                        {{ $size->sizeName ?? $size->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>

                                        <div class="col-md-2 mb-2">
                                            <label class="form-label">Price</label>
                                            <input type="number" step="0.01" name="variant_price[{{ $variantIndex }}][price]"
                                                   value="{{ $firstVariant->price }}" class="form-control" placeholder="Enter Price">
                                        </div>

                                        <div class="col-md-2 mb-2">
                                            <label class="form-label">Stock</label>
                                            <input type="number" name="variant_price[{{ $variantIndex }}][stock]"
                                                   value="{{ $firstVariant->stock }}" class="form-control" placeholder="0">
                                        </div>

                                        <div class="col-md-2 mb-2">
                                            <label class="form-label">Variant Image</label>
                                            @php
                                                $variantColorId = ($colorId === 'no_color') ? null : $colorId;
                                                $variantImages = $edit_data->images->filter(function($img) use ($variantColorId, $sizeIds) {
                                                    $colorMatch = ($img->color_id == $variantColorId) || (empty($img->color_id) && empty($variantColorId));
                                                    $sizeMatch = empty($sizeIds) ? empty($img->size_id) : in_array($img->size_id, $sizeIds);
                                                    return $colorMatch && $sizeMatch;
                                                })->unique('image');
                                            @endphp
                                            @if($variantImages->isNotEmpty())
                                                <div class="variant-existing-imgs d-flex flex-wrap gap-1 mb-2">
                                                    @foreach($variantImages as $vImg)
                                                        <div class="position-relative">
                                                            <img src="{{ asset($vImg->image) }}" class="rounded border" style="width:50px;height:50px;object-fit:cover;" alt="">
                                                            <a href="{{ route('products.image.destroy', ['id' => $vImg->id]) }}" class="btn btn-xs btn-danger position-absolute top-0 end-0 rounded-circle" style="padding:0 4px;top:-4px;right:-4px;" onclick="return confirm('Delete this image?')"><i class="mdi mdi-close"></i></a>
                                                        </div>
                                                    @endforeach
                                                </div>
                                            @endif
                                            <div class="variant-img-upload">
                                                <input type="file" name="variant_image[{{ $variantIndex }}][image]" class="form-control form-control-sm variant-img-input" accept="image/*">
                                                <div class="variant-img-preview mt-1" style="display:none;">
                                                    <img src="" alt="Preview" class="rounded border" style="max-width:60px;max-height:60px;object-fit:cover;">
                                                    <button type="button" class="btn btn-sm btn-danger variant-img-clear ms-1" title="Remove"><i class="fe-x"></i></button>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-md-1 mb-2 d-flex justify-content-end">
                                            @if($loop->first)
                                                <button type="button" class="btn btn-success add-variant" style="margin-top:5px;">
                                                    <i class="fa fa-plus"></i>
                                                </button>
                                            @else
                                                <button type="button" class="btn btn-danger remove-variant" style="margin-top:5px;">
                                                    <i class="fa fa-trash"></i>
                                                </button>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-12">
                                            <small class="text-muted">
                                                <i class="fa fa-info-circle"></i> 
                                                আপনি শুধু Color, শুধু Size, অথবা Color + Size উভয় add করতে পারবেন
                                            </small>
                                        </div>
                                    </div>
                                </div>
                                @php $variantIndex++; @endphp
                            @empty
                                <div class="variant-card variant-item">
                                    <div class="row align-items-end">
                                        <div class="col-md-3 mb-2">
                                            <label class="form-label">Color <small class="text-muted">(Optional)</small></label>
                                            <select name="variant_price[0][color_id]" class="form-control select2 variant-color-select">
                                                <option value="">Select Color (Optional)</option>
                                                @foreach($totalcolors as $color)
                                                    <option value="{{ $color->id }}">{{ $color->colorName ?? $color->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>

                                        <div class="col-md-3 mb-2">
                                            <label class="form-label">Size <small class="text-muted">(Optional)</small></label>
                                            <select name="variant_price[0][size_id][]" class="form-control select2 variant-size-select" multiple>
                                                @foreach($totalsizes as $size)
                                                    <option value="{{ $size->id }}">{{ $size->sizeName ?? $size->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>

                                        <div class="col-md-2 mb-2">
                                            <label class="form-label">Price <small class="text-muted">(Optional)</small></label>
                                            <input type="number" step="0.01" name="variant_price[0][price]"
                                                   class="form-control" placeholder="Enter Price">
                                        </div>

                                        <div class="col-md-2 mb-2">
                                            <label class="form-label">Stock</label>
                                            <input type="number" name="variant_price[0][stock]" class="form-control" placeholder="0">
                                        </div>

                                        <div class="col-md-2 mb-2">
                                            <label class="form-label">Variant Image</label>
                                            <div class="variant-img-upload">
                                                <input type="file" name="variant_image[0][image]" class="form-control form-control-sm variant-img-input" accept="image/*">
                                                <div class="variant-img-preview mt-1" style="display:none;">
                                                    <img src="" alt="Preview" class="rounded border" style="max-width:60px;max-height:60px;object-fit:cover;">
                                                    <button type="button" class="btn btn-sm btn-danger variant-img-clear ms-1" title="Remove"><i class="fe-x"></i></button>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-md-1 mb-2 d-flex justify-content-end">
                                            <button type="button" class="btn btn-success add-variant" style="margin-top:5px;">
                                                <i class="fa fa-plus"></i>
                                            </button>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-12">
                                            <small class="text-muted">
                                                <i class="fa fa-info-circle"></i> 
                                                আপনি শুধু Color, শুধু Size, অথবা Color + Size উভয় add করতে পারবেন
                                            </small>
                                        </div>
                                    </div>
                                </div>
                            @endforelse
                        </div>
                    </div>
                </div>

                {{-- SEO CONFIG CARD --}}
                <div class="card mb-4">
                    <div class="card-body">
                        <div class="section-title"><i class="fe-search me-1"></i> SEO Configuration</div>
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="meta_title" class="form-label">Meta Title</label>
                                <input type="text" name="meta_title" id="meta_title" class="form-control"
                                       value="{{ $edit_data->meta_title ?? $edit_data->name }}"
                                       placeholder="Enter meta title">
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="meta_keywords" class="form-label">Meta Keywords</label>
                                <input type="text" name="meta_keywords" id="meta_keywords" class="form-control"
                                       value="{{ $edit_data->meta_keywords ?? '' }}"
                                       placeholder="meta1, meta2, meta3">
                            </div>

                            <div class="col-md-12 mb-3">
                                <label for="meta_description" class="form-label">Meta Description</label>
                                <textarea name="meta_description" id="meta_description" class="form-control" rows="3"
                                          placeholder="Enter short SEO description...">{{ $edit_data->meta_description ?? \Illuminate\Support\Str::limit(strip_tags($edit_data->description), 160) }}</textarea>
                            </div>

                            <div class="col-md-12 mb-3">
                                <label for="meta_image" class="form-label">Meta Image (og:image)</label>
                                <input type="file" name="meta_image" id="meta_image" class="form-control">

                                @if(!empty($edit_data->meta_image))
                                    <div class="mt-2">
                                        <img src="{{ asset($edit_data->meta_image) }}" alt="Meta Image"
                                             class="border rounded" width="120">
                                    </div>
                                @endif
                                <small class="text-muted d-block mt-1">Recommended size: 1200x630px</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-4 pf-sidebar-col">

                @include('backEnd.product.partials.edit_sidebar')
            </div>
            </div>
    </form>
</div>
@endsection

@section('script')
<script src="{{asset('public/backEnd/')}}/assets/libs/parsleyjs/parsley.min.js"></script>
<script src="{{asset('public/backEnd/')}}/assets/js/pages/form-validation.init.js"></script>
<script src="{{asset('public/backEnd/')}}/assets/libs/select2/js/select2.min.js"></script>
<script src="{{asset('public/backEnd/')}}/assets/js/pages/form-advanced.init.js"></script>
<script src="{{asset('public/backEnd/')}}/assets/libs//summernote/summernote-lite.min.js"></script>

<script>
    $(".summernote").summernote({
        placeholder: "Enter Your Text Here",
    });
</script>

<script>
    $(document).ready(function () {
        // Gallery image add
        $(".increment-wrapper .btn-increment").click(function () {
            var html = $(".clone").html();
            $(".increment-wrapper").append(html);
        });
        $("body").on("click", ".btn-remove-image", function () {
            $(this).parents(".control-group").remove();
        });

        $(".select2").select2();
    });
</script>

<script>
    // Category to subcategory & childcategory
    $("#category_id").on("change", function () {
        var ajaxId = $(this).val();
        if (ajaxId) {
            $.ajax({
                type: "GET",
                url: "{{url('ajax-product-subcategory')}}?category_id=" + ajaxId,
                success: function (res) {
                    if (res) {
                        $("#subcategory_id").empty();
                        $("#subcategory_id").append('<option value="0">Choose...</option>');
                        $.each(res, function (key, value) {
                            $("#subcategory_id").append('<option value="' + key + '">' + value + "</option>");
                        });
                    } else {
                        $("#subcategory_id").empty();
                    }
                },
            });
        } else {
            $("#subcategory_id").empty();
        }
    });

    $("#subcategory_id").on("change", function () {
        var ajaxId = $(this).val();
        if (ajaxId) {
            $.ajax({
                type: "GET",
                url: "{{url('ajax-product-childcategory')}}?subcategory_id=" + ajaxId,
                success: function (res) {
                    if (res) {
                        $("#childcategory_id").empty();
                        $("#childcategory_id").append('<option value="0">Choose...</option>');
                        $.each(res, function (key, value) {
                            $("#childcategory_id").append('<option value="' + key + '">' + value + "</option>");
                        });
                    } else {
                        $("#childcategory_id").empty();
                    }
                },
            });
        } else {
            $("#childcategory_id").empty();
        }
    });

    // Set selected values on load
    document.forms["editForm"].elements["category_id"].value = "{{$edit_data->category_id}}";
    document.forms["editForm"].elements["subcategory_id"].value = "{{$edit_data->subcategory_id}}";
    document.forms["editForm"].elements["childcategory_id"].value = "{{$edit_data->childcategory_id}}";
</script>

{{-- Variant add/remove with Multiple Size Select --}}
<script>
document.addEventListener('DOMContentLoaded', function () {
    let variantIndex = {{ $edit_data->variantPrices->count() ?? 1 }};
    
    // Initialize Select2 with multiple for size
    $('.variant-size-select').select2({
        multiple: true,
        width: '100%'
    });
    
    $('.variant-color-select').select2({
        width: '100%'
    });

    // Add new variant row
    document.body.addEventListener('click', function (e) {
        const target = e.target.closest('.add-variant, .remove-variant');
        if (!target) return;

        if (target.classList.contains('add-variant')) {
            const wrapper = document.getElementById('variant-wrapper');
            const firstRow = wrapper.querySelector('.variant-item');
            if (!firstRow) return;

            const newRow = $(firstRow.cloneNode(true));
            newRow.find('.select2-container').remove();

            newRow.find('.variant-existing-imgs').remove();
            newRow.find('input, select').each(function () {
                const oldName = $(this).attr('name');
                if (oldName) {
                    if (oldName.includes('[size_id][]')) {
                        $(this).attr('name', 'variant_price[' + variantIndex + '][size_id][]');
                    } else if (oldName.includes('variant_image')) {
                        $(this).attr('name', 'variant_image[' + variantIndex + '][image]');
                    } else {
                        $(this).attr('name', oldName.replace(/\[\d+\]/, '[' + variantIndex + ']'));
                    }
                }
                if ($(this).attr('type') === 'file') {
                    $(this).val('');
                    $(this).siblings('.variant-img-preview').hide().find('img').attr('src', '');
                } else if ($(this).is('input')) $(this).val('');
                else if ($(this).is('select')) $(this).val(null).trigger('change');
            });

            newRow.find('.add-variant')
                .removeClass('btn-success add-variant')
                .addClass('btn-danger remove-variant')
                .html('<i class="fa fa-trash"></i>');

            newRow.appendTo(wrapper);

            // Reinitialize Select2 for new row
            setTimeout(() => {
                newRow.find('.variant-size-select').select2({
                    multiple: true,
                    width: '100%',
                    dropdownParent: $('#variant-wrapper')
                });
                newRow.find('.variant-color-select').select2({
                    width: '100%',
                    dropdownParent: $('#variant-wrapper')
                });
            }, 100);

            variantIndex++;
        }

        if (target.classList.contains('remove-variant')) {
            target.closest('.variant-item').remove();
        }
    });

    // Variant Image Preview & Clear
    $(document).on('change', '.variant-img-input', function() {
        var $input = $(this);
        var $preview = $input.siblings('.variant-img-preview');
        var $img = $preview.find('img');
        var file = this.files[0];
        if (file && file.type.startsWith('image/')) {
            var reader = new FileReader();
            reader.onload = function(e) { $img.attr('src', e.target.result); $preview.show(); };
            reader.readAsDataURL(file);
        } else { $preview.hide(); $img.attr('src', ''); }
    });
    $(document).on('click', '.variant-img-clear', function() {
        var $preview = $(this).closest('.variant-img-preview');
        $preview.siblings('.variant-img-input').val('');
        $preview.find('img').attr('src', '');
        $preview.hide();
    });
    
    // Handle form submission - expand multiple sizes into separate entries
    $('form[name="editForm"]').on('submit', function(e) {
        let formData = new FormData(this);
        let variantData = [];
        let variantIndex = 0;
        let rowIndex = 0;
        
            $('#variant-wrapper .variant-item').each(function() {
                let $row = $(this);
                let colorId = $row.find('.variant-color-select').val() || null;
                let selectedSizes = $row.find('.variant-size-select').val() || [];
                let price = $row.find('input[name*="[price]"]').val() || 0;
                let stock = $row.find('input[name*="[stock]"]').val() || 0;
                
                // Validate: At least color or size must be selected
                if (!colorId && selectedSizes.length === 0) {
                    // Skip if neither color nor size is selected
                    return;
                }
                
                // If sizes are selected, create separate entry for each size
                if (selectedSizes.length > 0) {
                    selectedSizes.forEach(function(sizeId) {
                        variantData.push({ index: variantIndex++, color_id: colorId, size_id: sizeId, price: price, stock: stock, image_row: rowIndex });
                    });
                } else {
                    variantData.push({ index: variantIndex++, color_id: colorId, size_id: null, price: price, stock: stock, image_row: rowIndex });
                }
                rowIndex++;
            });
        
        $(this).find('input[name*="variant_price"]:not([type="file"]), select[name*="variant_price"]').remove();
        
        // Add new hidden inputs for each variant
        variantData.forEach(function(variant) {
            $('<input>').attr({
                type: 'hidden',
                name: 'variant_price[' + variant.index + '][color_id]',
                value: variant.color_id
            }).appendTo($('form[name="editForm"]'));
            
            $('<input>').attr({
                type: 'hidden',
                name: 'variant_price[' + variant.index + '][size_id]',
                value: variant.size_id
            }).appendTo($('form[name="editForm"]'));
            
            $('<input>').attr({
                type: 'hidden',
                name: 'variant_price[' + variant.index + '][price]',
                value: variant.price
            }).appendTo($('form[name="editForm"]'));
            
            $('<input>').attr({
                type: 'hidden',
                name: 'variant_price[' + variant.index + '][stock]',
                value: variant.stock
            }).appendTo($('form[name="editForm"]'));
            
            $('<input>').attr({
                type: 'hidden',
                name: 'variant_price[' + variant.index + '][image_row]',
                value: variant.image_row
            }).appendTo($('form[name="editForm"]'));
        });
    });
});
</script>

{{-- Product type toggle --}}
<script>
document.addEventListener('DOMContentLoaded', function () {
    function toggleFields() {
        let type = document.getElementById('product_type').value;
        if (type === 'digital') {
            document.getElementById('digital_area').style.display = 'block';
            document.getElementById('advance_area').style.display = 'none';
        } else {
            document.getElementById('digital_area').style.display = 'none';
            document.getElementById('advance_area').style.display = 'block';
        }
    }

    document.getElementById('product_type').addEventListener('change', toggleFields);
    
    // Wholesale toggle
    document.getElementById('is_wholesale').addEventListener('change', function() {
        var wholesaleArea = document.getElementById('wholesale_area');
        if (this.checked) {
            wholesaleArea.style.display = 'block';
            wholesaleArea.querySelectorAll('input').forEach(function(input) {
                input.setAttribute('required', 'required');
            });
        } else {
            wholesaleArea.style.display = 'none';
            wholesaleArea.querySelectorAll('input').forEach(function(input) {
                input.removeAttribute('required');
            });
        }
    });
    toggleFields(); // initial
    // Wholesale pricing tiers
    let wholesaleIndex = {{ ($wholesalePrices && $wholesalePrices->count() > 0) ? $wholesalePrices->count() : 1 }};
    $('.add-wholesale-tier').on('click', function() {
        let wrapper = $('#wholesale-wrapper');
        let firstRow = wrapper.find('.variant-card').first().clone();
        
        firstRow.find('input').each(function(){
            let oldName = $(this).attr('name');
            $(this).attr('name', oldName.replace(/\[\d+\]/, '[' + wholesaleIndex + ']'));
            $(this).val('');
        });

        firstRow.find('.btn-remove-wholesale').removeClass('d-none');
        wrapper.append(firstRow);
        wholesaleIndex++;
    });
    
    $("body").on("click", ".btn-remove-wholesale", function () {
        $(this).parents(".variant-card").remove();
    });

    // Variant Image Add/Remove
    let variantImgIndex = 1;
    $(".add-variant-image").click(function () {
        let wrapper = $("#variant-image-wrapper");
        let firstRow = wrapper.find(".variant-image-row").first().clone();
        firstRow.find('.select2-container').remove();
        firstRow.find('input[type="file"]').val('');
        firstRow.find('select').each(function(){
            let name = $(this).attr('name');
            if (name) $(this).attr('name', name.replace(/\[\d+\]/, '[' + variantImgIndex + ']'));
            $(this).val(null);
        });
        firstRow.find('input[type="file"]').each(function(){
            let name = $(this).attr('name');
            if (name) $(this).attr('name', name.replace(/\[\d+\]/, '[' + variantImgIndex + ']'));
        });
        firstRow.find('.btn-remove-variant-img').show();
        wrapper.append(firstRow);
        firstRow.find('.variant-img-color, .variant-img-size').select2({ width: '100%' });
        variantImgIndex++;
    });
    $("body").on("click", ".btn-remove-variant-img", function () {
        $(this).closest(".variant-image-row").remove();
    });
});

// ===== VIDEO SOURCE SWITCHER (Edit) =====
(function () {
    var radios = document.querySelectorAll('input[name="pro_video_source"]');
    var ytSec  = document.getElementById('yt_section_e');
    var upSec  = document.getElementById('up_section_e');

    function switchVideo(val) {
        if (val === 'upload') {
            if (ytSec) ytSec.style.display = 'none';
            if (upSec) upSec.style.display = '';
        } else {
            if (ytSec) ytSec.style.display = '';
            if (upSec) upSec.style.display = 'none';
        }
    }

    radios.forEach(function (r) {
        r.addEventListener('change', function () { switchVideo(this.value); });
    });

    // YouTube live preview
    var ytInput = document.getElementById('pro_video_e');
    if (ytInput) {
        ytInput.addEventListener('input', function () {
            var val = this.value.trim();
            var id  = extractYtId(val);
            var box = document.getElementById('yt_preview_e');
            var fr  = document.getElementById('yt_iframe_e');
            if (id && box && fr) {
                fr.src = 'https://www.youtube.com/embed/' + id;
                box.style.display = '';
            } else if (box && fr) {
                fr.src = '';
                box.style.display = 'none';
            }
        });
    }

    // Upload local preview
    var upInput = document.getElementById('pro_video_file_e');
    if (upInput) {
        upInput.addEventListener('change', function () {
            var file = this.files[0];
            var box  = document.getElementById('up_preview_e');
            var vid  = document.getElementById('up_video_e');
            if (file && box && vid) {
                vid.src = URL.createObjectURL(file);
                box.style.display = '';
            }
        });
    }

    function extractYtId(input) {
        if (!input) return null;
        if (/^[a-zA-Z0-9_-]{11}$/.test(input)) return input;
        var m = input.match(/(?:youtube\.com\/(?:[^\/\n\s]+\/\S+\/|(?:v|e(?:mbed)?)\/|.*[?&]v=)|youtu\.be\/)([a-zA-Z0-9_-]{11})/);
        return m ? m[1] : null;
    }
})();
</script>
@include('backEnd.product.partials.ai_description_script')
@endsection
