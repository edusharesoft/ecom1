@extends('vendor.layouts.app')

@section('title', 'Edit Product')
@section('page-title', 'Edit Product')

@push('styles')
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">
<link href="{{asset('public/backEnd')}}/assets/libs/select2/css/select2.min.css" rel="stylesheet" type="text/css" />
<link href="{{asset('public/backEnd')}}/assets/libs/summernote/summernote-lite.min.css" rel="stylesheet" type="text/css" />

<style>
    :root {
        --primary-color: #4e73df;
        --secondary-color: #858796;
        --border-color: #eaecf4;
        --bg-light: #f8f9fc;
    }

    body {
        font-family: 'Poppins', sans-serif;
        background-color: #f4f6f9;
        color: #5a5c69;
    }

    /* Card Styling */
    .card {
        border: none;
        box-shadow: 0 0.15rem 1.75rem 0 rgba(58,59,69,0.05);
        border-radius: 15px;
        margin-bottom: 24px;
        background: #fff;
    }
    .card-header {
        background-color: #fff;
        border-bottom: 1px solid var(--border-color);
        padding: 18px 25px;
        border-radius: 15px 15px 0 0;
        font-weight: 600;
        color: var(--primary-color);
        font-size: 1rem;
    }

    /* Form Controls */
    .form-label {
        font-weight: 500;
        font-size: 0.85rem;
        color: #444;
        margin-bottom: 8px;
    }
    .form-control, .form-select {
        border-radius: 10px;
        padding: 10px 15px;
        border: 1px solid var(--border-color);
        background-color: var(--bg-light);
        font-size: 0.9rem;
        transition: all 0.2s;
    }
    .form-control:focus {
        background-color: #fff;
        border-color: var(--primary-color);
        box-shadow: 0 0 0 0.2rem rgba(78, 115, 223, 0.1);
    }

    /* Custom Switch */
    .switch {
        position: relative;
        display: inline-block;
        width: 50px;
        height: 26px;
    }
    .switch input { opacity: 0; width: 0; height: 0; }
    .slider {
        position: absolute;
        cursor: pointer;
        top: 0; left: 0; right: 0; bottom: 0;
        background-color: #ccc;
        transition: .4s;
        border-radius: 34px;
    }
    .slider:before {
        position: absolute;
        content: "";
        height: 18px;
        width: 18px;
        left: 4px;
        bottom: 4px;
        background-color: white;
        transition: .4s;
        border-radius: 50%;
    }
    input:checked + .slider { background-color: var(--primary-color); }
    input:checked + .slider:before { transform: translateX(24px); }

    /* Select2 Customization */
    .select2-container .select2-selection--single, 
    .select2-container .select2-selection--multiple {
        height: auto !important;
        min-height: 45px;
        border: 1px solid var(--border-color) !important;
        border-radius: 10px !important;
        background-color: var(--bg-light) !important;
        padding: 6px;
    }
    .select2-container--default .select2-selection--single .select2-selection__arrow {
        height: 44px;
    }

    /* Edit Image Thumbnail */
    .edit-image-container {
        position: relative;
        width: 80px;
        height: 80px;
        border-radius: 8px;
        overflow: hidden;
        border: 1px solid #ddd;
    }
    .edit-image {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }
    .btn-remove-image-db {
        position: absolute;
        top: 2px;
        right: 2px;
        background: rgba(231, 74, 59, 0.9);
        color: white;
        border-radius: 50%;
        width: 20px;
        height: 20px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 10px;
        cursor: pointer;
        text-decoration: none;
    }

    /* Variant Card */
    .variant-item {
        background: #fff;
        border: 1px dashed var(--primary-color);
        padding: 20px;
        border-radius: 12px;
        margin-bottom: 15px;
        background-color: rgba(78, 115, 223, 0.02);
    }
    
    /* Image Upload Wrapper */
    .image-upload-wrapper {
        border: 2px dashed #d1d3e2;
        padding: 20px;
        border-radius: 10px;
        background: #fff;
        transition: all 0.3s;
    }
</style>
@endpush

@section('content')
<div class="container-fluid px-0">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="mb-1 fw-bold text-dark">Edit Product</h4>
            <p class="text-muted mb-0 small">Update product information and settings</p>
        </div>
        <a href="{{ route('vendor.products.index') }}" class="btn btn-outline-secondary rounded-pill px-4">
            <i class="fas fa-arrow-left me-2"></i>Back to List
        </a>
    </div>

    <form action="{{ route('vendor.products.update') }}" method="POST" data-parsley-validate="" enctype="multipart/form-data">
        @csrf
        <input type="hidden" value="{{$edit_data->id}}" name="id" />

        <div class="row g-4">
            <div class="col-lg-8">
                
                <div class="card">
                    <div class="card-header">Basic Information</div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label for="name" class="form-label">Product Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control form-control-lg fw-bold @error('name') is-invalid @enderror" 
                                   name="name" value="{{ old('name', $edit_data->name) }}" id="name" required />
                            @error('name') <span class="invalid-feedback"><strong>{{ $message }}</strong></span> @enderror
                        </div>

                        <div class="mb-3">
                            <label for="description" class="form-label">Description <span class="text-danger">*</span></label>
                            <textarea name="description" rows="6" class="summernote form-control @error('description') is-invalid @enderror" required>{{ old('description', $edit_data->description) }}</textarea>
                            @error('description') <span class="invalid-feedback"><strong>{{ $message }}</strong></span> @enderror
                        </div>

                        <div class="mb-0">
                            <label for="note" class="form-label">Internal Note</label>
                            <textarea name="note" rows="2" class="form-control">{{ old('note', $edit_data->note) }}</textarea>
                        </div>
                    </div>
                </div>

                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <span>Product Images</span>
                        <button class="btn btn-sm btn-primary btn-increment rounded-pill" type="button"><i class="fa fa-plus me-1"></i> Add New</button>
                    </div>
                    <div class="card-body">
                        <div class="d-flex flex-wrap gap-2 mb-4">
                            @foreach($edit_data->images->filter(fn($img) => !$img->color_id && !$img->size_id) as $image)
                            <div class="edit-image-container">
                                <img src="{{asset($image->image)}}" class="edit-image" alt="Product Image">
                                <a href="{{route('vendor.products.image.destroy',['id'=>$image->id])}}" class="btn-remove-image-db" onclick="return confirm('Are you sure you want to delete this image?')">
                                    <i class="fas fa-times"></i>
                                </a>
                            </div>
                            @endforeach
                        </div>

                        <div class="increment">
                            <div class="image-upload-wrapper control-group mb-3">
                                <div class="d-flex align-items-center">
                                    <div class="flex-grow-1">
                                        <label class="form-label small text-muted">Upload New Image</label>
                                        <input type="file" name="image[]" class="form-control" />
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="clone hide" style="display: none;">
                            <div class="image-upload-wrapper control-group mb-3 position-relative">
                                <input type="file" name="image[]" class="form-control" />
                                <button class="btn btn-danger btn-sm position-absolute top-0 end-0 mt-2 me-2 rounded-circle shadow-sm" type="button" style="width: 30px; height: 30px; padding: 0;">
                                    <i class="fa fa-trash"></i>
                                </button>
                            </div>
                        </div>

                        <div class="mt-3">
                            <label for="pro_video" class="form-label">Video URL (YouTube/Vimeo)</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light"><i class="fas fa-video"></i></span>
                                <input type="text" class="form-control" name="pro_video" value="{{ old('pro_video', $edit_data->pro_video) }}" id="pro_video">
                            </div>
                        </div>

                    </div>
                </div>

                <div class="card" id="variant_section">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <span>Product Variants <small class="text-muted">(Optional)</small></span>
                        <button type="button" class="btn btn-sm btn-success add-variant rounded-pill px-3"><i class="fa fa-plus me-1"></i> Add Variant</button>
                    </div>
                    <div class="card-body">
                        <div id="variant-wrapper">
                            @forelse($groupedVariants ?? [] as $key => $variant)
                            <div class="variant-item variant-card">
                                <div class="row g-2 align-items-end">
                                    <div class="col-md-3">
                                        <label class="form-label small">Color <small class="text-muted">(Optional)</small></label>
                                        <select name="variant_price[{{ $key }}][color_id]" class="form-control select2 variant-color-select">
                                            <option value="">Select Color (Optional)</option>
                                            @foreach($totalcolors as $color)
                                            <option value="{{ $color->id }}" {{ ($variant['color_id'] ?? null) == $color->id ? 'selected' : '' }}>
                                                {{ $color->colorName ?? $color->name }}
                                            </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label small">Size <small class="text-muted">(Optional)</small></label>
                                        <select name="variant_price[{{ $key }}][size_id][]" class="form-control select2 variant-size-select" multiple>
                                            @foreach($totalsizes as $size)
                                            <option value="{{ $size->id }}" {{ in_array($size->id, $variant['size_ids'] ?? []) ? 'selected' : '' }}>
                                                {{ $size->sizeName ?? $size->name }}
                                            </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-2">
                                        <label class="form-label small">Price</label>
                                        <input type="number" step="0.01" name="variant_price[{{ $key }}][price]" value="{{ $variant['price'] ?? 0 }}" class="form-control variant-price-input" placeholder="0.00">
                                    </div>
                                    <div class="col-md-2">
                                        <label class="form-label small">Stock</label>
                                        <input type="number" name="variant_price[{{ $key }}][stock]" value="{{ $variant['stock'] ?? 0 }}" class="form-control variant-stock-input" placeholder="Qty">
                                    </div>
                                    <div class="col-md-2">
                                        <label class="form-label small">Variant Image</label>
                                        @php
                                            $vColorId = $variant['color_id'] ?? null;
                                            $vSizeIds = $variant['size_ids'] ?? [];
                                            $vVariantImages = $edit_data->images->filter(function($img) use ($vColorId, $vSizeIds) {
                                                $colorMatch = ($img->color_id == $vColorId) || (empty($img->color_id) && empty($vColorId));
                                                $sizeMatch = empty($vSizeIds) ? empty($img->size_id) : in_array($img->size_id, $vSizeIds);
                                                return $colorMatch && $sizeMatch;
                                            })->unique('image');
                                        @endphp
                                        @if($vVariantImages->isNotEmpty())
                                            <div class="variant-existing-imgs d-flex flex-wrap gap-1 mb-2">
                                                @foreach($vVariantImages as $vImg)
                                                    <div class="position-relative">
                                                        <img src="{{ asset($vImg->image) }}" class="rounded border" style="width:50px;height:50px;object-fit:cover;" alt="">
                                                        <a href="{{ route('vendor.products.image.destroy', ['id' => $vImg->id]) }}" class="btn btn-xs btn-danger position-absolute top-0 end-0 rounded-circle" style="padding:0 4px;top:-4px;right:-4px;" onclick="return confirm('Delete this image?')"><i class="fa fa-times"></i></a>
                                                    </div>
                                                @endforeach
                                            </div>
                                        @endif
                                        <div class="variant-img-upload">
                                            <input type="file" name="variant_image[{{ $key }}][image]" class="form-control form-control-sm variant-img-input" accept="image/*">
                                            <div class="variant-img-preview mt-1" style="display:none;">
                                                <img src="" alt="Preview" class="rounded border" style="max-width:60px;max-height:60px;object-fit:cover;">
                                                <button type="button" class="btn btn-sm btn-danger variant-img-clear ms-1" title="Remove"><i class="fa fa-times"></i></button>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-1">
                                        @if($loop->first)
                                            <button type="button" class="btn btn-danger btn-remove-row d-none w-100"><i class="fa fa-trash"></i></button>
                                        @else
                                            <button type="button" class="btn btn-danger btn-remove-row w-100"><i class="fa fa-trash"></i></button>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            @empty
                            <div class="variant-item variant-card">
                                <div class="row g-2 align-items-end">
                                    <div class="col-md-3">
                                        <label class="form-label small">Color <small class="text-muted">(Optional)</small></label>
                                        <select name="variant_price[0][color_id]" class="form-control select2 variant-color-select">
                                            <option value="">Select Color (Optional)</option>
                                            @foreach($totalcolors as $color)
                                            <option value="{{ $color->id }}">{{ $color->colorName ?? $color->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label small">Size <small class="text-muted">(Optional)</small></label>
                                        <select name="variant_price[0][size_id][]" class="form-control select2 variant-size-select" multiple>
                                            @foreach($totalsizes as $size)
                                            <option value="{{ $size->id }}">{{ $size->sizeName ?? $size->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-2">
                                        <label class="form-label small">Price</label>
                                        <input type="number" step="0.01" name="variant_price[0][price]" class="form-control variant-price-input" placeholder="0.00">
                                    </div>
                                    <div class="col-md-2">
                                        <label class="form-label small">Stock</label>
                                        <input type="number" name="variant_price[0][stock]" class="form-control variant-stock-input" placeholder="Qty">
                                    </div>
                                    <div class="col-md-2">
                                        <label class="form-label small">Variant Image</label>
                                        <div class="variant-img-upload">
                                            <input type="file" name="variant_image[0][image]" class="form-control form-control-sm variant-img-input" accept="image/*">
                                            <div class="variant-img-preview mt-1" style="display:none;">
                                                <img src="" alt="Preview" class="rounded border" style="max-width:60px;max-height:60px;object-fit:cover;">
                                                <button type="button" class="btn btn-sm btn-danger variant-img-clear ms-1" title="Remove"><i class="fa fa-times"></i></button>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-1">
                                        <button type="button" class="btn btn-danger btn-remove-row d-none w-100"><i class="fa fa-trash"></i></button>
                                    </div>
                                </div>
                                <div class="mt-2 text-muted small">
                                    <i class="fa fa-info-circle me-1"></i> Variants are optional. You can skip this section if your product doesn't have size/color variations.
                                </div>
                            </div>
                            @endforelse
                        </div>
                    </div>
                </div>

                <div class="card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h6 class="mb-0 fw-bold"><i class="fas fa-tags me-2 text-primary"></i>Wholesale Configuration</h6>
                            <label class="switch">
                                <input type="checkbox" value="1" name="is_wholesale" id="is_wholesale" {{ old('is_wholesale', $edit_data->is_wholesale ?? 0) ? 'checked' : '' }}>
                                <span class="slider round"></span>
                            </label>
                        </div>

                        <div id="wholesale_area" style="{{ old('is_wholesale', $edit_data->is_wholesale ?? 0) ? '' : 'display:none;' }}" class="mt-4 pt-3 border-top">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <span class="text-muted small">Define pricing tiers for bulk purchases</span>
                                <button type="button" class="btn btn-sm btn-success add-wholesale-tier"><i class="fa fa-plus me-1"></i> Add Tier</button>
                            </div>
                            
                            <div id="wholesale-wrapper">
                                @if($wholesalePrices && $wholesalePrices->count() > 0)
                                    @foreach($wholesalePrices as $key => $tier)
                                    <div class="variant-item mb-3 p-3 bg-light rounded border">
                                        <div class="row g-2 align-items-end">
                                            <div class="col-md-3">
                                                <label class="form-label small">Min Qty</label>
                                                <input type="number" name="wholesale_price[{{ $key }}][min_quantity]" class="form-control" value="{{ $tier->min_quantity }}">
                                            </div>
                                            <div class="col-md-3">
                                                <label class="form-label small">Max Qty</label>
                                                <input type="number" name="wholesale_price[{{ $key }}][max_quantity]" class="form-control" value="{{ $tier->max_quantity }}">
                                            </div>
                                            <div class="col-md-2">
                                                <label class="form-label small">Price</label>
                                                <input type="number" step="0.01" name="wholesale_price[{{ $key }}][wholesale_price]" class="form-control" value="{{ $tier->wholesale_price }}">
                                            </div>
                                            <div class="col-md-2">
                                                <label class="form-label small">Stock</label>
                                                <input type="number" name="wholesale_price[{{ $key }}][stock]" class="form-control" value="{{ $tier->stock ?? 0 }}">
                                            </div>
                                            <div class="col-md-2">
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
                                    <div class="variant-item mb-3 p-3 bg-light rounded border">
                                        <div class="row g-2 align-items-end">
                                            <div class="col-md-3">
                                                <label class="form-label small">Min Qty</label>
                                                <input type="number" name="wholesale_price[0][min_quantity]" class="form-control" placeholder="10">
                                            </div>
                                            <div class="col-md-3">
                                                <label class="form-label small">Max Qty</label>
                                                <input type="number" name="wholesale_price[0][max_quantity]" class="form-control" placeholder="50">
                                            </div>
                                            <div class="col-md-2">
                                                <label class="form-label small">Price</label>
                                                <input type="number" step="0.01" name="wholesale_price[0][wholesale_price]" class="form-control" placeholder="0.00">
                                            </div>
                                            <div class="col-md-2">
                                                <label class="form-label small">Stock</label>
                                                <input type="number" name="wholesale_price[0][stock]" class="form-control" placeholder="0">
                                            </div>
                                            <div class="col-md-2">
                                                <button type="button" class="btn btn-success add-wholesale-tier w-100"><i class="fa fa-plus"></i></button>
                                            </div>
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card">
                    <div class="card-header">SEO Configuration</div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="meta_title" class="form-label">Meta Title</label>
                                <input type="text" name="meta_title" id="meta_title" class="form-control" value="{{ old('meta_title', $edit_data->meta_title ?? $edit_data->name) }}">
                            </div>
                            <div class="col-md-6">
                                <label for="meta_keywords" class="form-label">Meta Keywords</label>
                                <input type="text" name="meta_keywords" id="meta_keywords" class="form-control" value="{{ old('meta_keywords', $edit_data->meta_keywords ?? '') }}">
                            </div>
                            <div class="col-12">
                                <label for="meta_description" class="form-label">Meta Description</label>
                                <textarea name="meta_description" id="meta_description" class="form-control" rows="3">{{ old('meta_description', $edit_data->meta_description ?? '') }}</textarea>
                            </div>
                            <div class="col-12">
                                <label for="meta_image" class="form-label">Meta Image</label>
                                <input type="file" name="meta_image" id="meta_image" class="form-control">
                                @if(!empty($edit_data->meta_image))
                                <div class="mt-2">
                                    <img src="{{ asset($edit_data->meta_image) }}" alt="Meta Image" class="border rounded" width="100">
                                </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                
                <div class="card">
                    <div class="card-body">
                        <h6 class="fw-bold mb-3 text-dark">Publish Settings</h6>
                        
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <label class="form-label mb-0">Status</label>
                            <label class="switch">
                                <input type="checkbox" value="1" name="status" @if($edit_data->status==1) checked @endif />
                                <span class="slider round"></span>
                            </label>
                        </div>
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <label class="form-label mb-0">Hot Deal</label>
                            <label class="switch">
                                <input type="checkbox" value="1" name="topsale" @if($edit_data->topsale==1) checked @endif />
                                <span class="slider round"></span>
                            </label>
                        </div>
                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <label class="form-label mb-0">Flash Sale</label>
                            <label class="switch">
                                <input type="checkbox" value="1" name="flashsale" @if($edit_data->flashsale==1) checked @endif />
                                <span class="slider round"></span>
                            </label>
                        </div>

                        <div class="mb-3">
                             <label for="sold" class="form-label">Sold Count</label>
                             <input type="text" class="form-control" name="sold" value="{{ old('sold', $edit_data->sold) }}" id="sold" />
                        </div>

                        <button type="submit" class="btn btn-primary w-100 py-2 rounded-pill fw-bold shadow-sm">
                            <i class="fas fa-save me-2"></i> Update Product
                        </button>
                    </div>
                </div>

                <div class="card">
                    <div class="card-header">Organization</div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label for="category_id" class="form-label">Category <span class="text-danger">*</span></label>
                            <select class="form-control select2 @error('category_id') is-invalid @enderror" name="category_id" id="category_id" required>
                                <option value="">Select Category</option>
                                @foreach($categories as $category)
                                <option value="{{$category->id}}" @if($edit_data->category_id==$category->id) selected @endif>{{$category->name}}</option>
                                    @foreach ($category->childrenCategories as $childCategory)
                                    <option value="{{$childCategory->id}}" @if($edit_data->category_id==$childCategory->id) selected @endif>- {{$childCategory->name}}</option>
                                    @endforeach
                                @endforeach
                            </select>
                            @error('category_id') <span class="invalid-feedback"><strong>{{ $message }}</strong></span> @enderror
                        </div>

                        <div class="mb-3">
                            <label for="subcategory_id" class="form-label">Sub Category</label>
                            <select class="form-control select2" id="subcategory_id" name="subcategory_id">
                                <option value="">Select..</option>
                                @foreach($subcategory as $value)
                                <option value="{{$value->id}}" @if($edit_data->subcategory_id==$value->id) selected @endif>{{$value->subcategoryName}}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="childcategory_id" class="form-label">Child Category</label>
                            <select class="form-control select2" id="childcategory_id" name="childcategory_id">
                                <option value="">Select..</option>
                                @foreach($childcategory as $value)
                                <option value="{{$value->id}}" @if($edit_data->childcategory_id==$value->id) selected @endif>{{$value->childcategoryName}}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="mb-0">
                            <label for="brand_id" class="form-label">Brand</label>
                            <select class="form-control select2" name="brand_id">
                                <option value="">Select Brand</option>
                                @foreach($brands as $value)
                                <option value="{{$value->id}}" @if($edit_data->brand_id==$value->id) selected @endif>{{$value->name}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>

                <div class="card">
                    <div class="card-header">Pricing & Inventory</div>
                    <div class="card-body">
                        @php
                            $currentType = old('product_type', $edit_data->is_digital ? 'digital' : 'physical');
                            $isDigital = $currentType === 'digital';
                        @endphp
                        <div class="mb-3">
                            <label for="product_type" class="form-label">Product Type</label>
                            <select class="form-select bg-light" id="product_type" name="product_type">
                                <option value="physical" {{ $currentType === 'physical' ? 'selected' : '' }}>Physical Product</option>
                                <option value="digital" {{ $currentType === 'digital' ? 'selected' : '' }}>Digital Product</option>
                            </select>
                        </div>

                        <div class="row g-2 mb-3">
                            <div class="col-6">
                                <label for="purchase_price" class="form-label">Purchase Price <small class="text-muted">(Optional)</small></label>
                                <input type="text" class="form-control" name="purchase_price" value="{{ old('purchase_price', $edit_data->purchase_price) }}" id="purchase_price" placeholder="0" />
                            </div>
                            <div class="col-6">
                                <label for="old_price" class="form-label">Old Price</label>
                                <input type="text" class="form-control" name="old_price" value="{{ old('old_price', $edit_data->old_price) }}" id="old_price" />
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="new_price" class="form-label">New Price (Sale) <small class="text-muted">(Optional)</small></label>
                            <input type="text" class="form-control fw-bold text-success fs-5" name="new_price" value="{{ old('new_price', $edit_data->new_price) }}" id="new_price" placeholder="0" />
                        </div>

                        <div class="mb-3">
                            <label for="reseller_price" class="form-label">Reseller Price</label>
                            <input type="text" step="0.01" class="form-control" name="reseller_price" value="{{ old('reseller_price', $edit_data->reseller_price) }}" id="reseller_price" placeholder="Reseller price (optional)" />
                            <small class="text-muted">Special price for resellers. Leave empty if not applicable.</small>
                        </div>

                        <div class="row g-2 mb-3">
                            <div class="col-6">
                                <label for="stock" class="form-label">Stock <small class="text-muted">(Optional)</small></label>
                                <input type="text" class="form-control" name="stock" value="{{ old('stock', $edit_data->stock) }}" id="stock" placeholder="0" />
                            </div>
                            <div class="col-6">
                                <label for="pro_unit" class="form-label">Unit</label>
                                <input type="text" class="form-control" name="pro_unit" value="{{ old('pro_unit', $edit_data->pro_unit) }}" id="pro_unit" />
                            </div>
                        </div>

                        <div id="advance_area" style="{{ $isDigital ? 'display:none;' : '' }}">
                            <div class="mb-0">
                                <label for="advance_amount" class="form-label">Advance Payment (Tk)</label>
                                <input type="text" class="form-control border-primary" name="advance_amount" id="advance_amount" value="{{ old('advance_amount', $edit_data->advance_amount) }}" />
                            </div>
                        </div>

                        <div id="digital_area" style="{{ $isDigital ? '' : 'display:none;' }}" class="mt-3 p-3 bg-light rounded border border-dashed">
                            @if($edit_data->digital_file)
                            <div class="mb-2">
                                <span class="badge bg-success">Current File: {{ $edit_data->digital_file }}</span>
                            </div>
                            @endif
                            <div class="mb-3">
                                <label for="digital_file" class="form-label">Change File (ZIP/PDF)</label>
                                <input type="file" class="form-control" name="digital_file" id="digital_file">
                            </div>
                            <div class="row g-2">
                                <div class="col-6">
                                    <label for="download_limit" class="form-label small">Limit</label>
                                    <input type="number" class="form-control" name="download_limit" id="download_limit" value="{{ old('download_limit', $edit_data->download_limit ?? 5) }}">
                                </div>
                                <div class="col-6">
                                    <label for="download_expire_days" class="form-label small">Expire Days</label>
                                    <input type="number" class="form-control" name="download_expire_days" id="download_expire_days" value="{{ old('download_expire_days', $edit_data->download_expire_days ?? 7) }}">
                                </div>
                            </div>
                        </div>

                    </div>
                </div>

            </div>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="{{asset('public/backEnd/')}}/assets/libs/parsleyjs/parsley.min.js"></script>
<script src="{{asset('public/backEnd/')}}/assets/libs/select2/js/select2.min.js"></script>
<script src="{{asset('public/backEnd/')}}/assets/libs/summernote/summernote-lite.min.js"></script>

<script>
    $(document).ready(function() {
        // Init Plugins
        $(".summernote").summernote({ 
            height: 250, 
            placeholder: "Detailed product description...",
            toolbar: [
                ['style', ['style']],
                ['font', ['bold', 'underline', 'clear']],
                ['para', ['ul', 'ol', 'paragraph']],
                ['insert', ['link', 'picture']],
                ['view', ['fullscreen']]
            ]
        });
        $(".select2").select2({ width: '100%' });

        // Product Type Toggle
        function toggleFields() {
            let type = $('#product_type').val();
            if (type === 'digital') {
                $('#digital_area').slideDown();
                $('#advance_area').slideUp();
                $('#variant_section').slideUp();
            } else {
                $('#digital_area').slideUp();
                $('#advance_area').slideDown();
                $('#variant_section').slideDown();
            }
        }
        $('#product_type').on('change', toggleFields);

        // Wholesale Toggle
        $('#is_wholesale').on('change', function() {
            var wholesaleArea = $('#wholesale_area');
            if (this.checked) {
                wholesaleArea.slideDown();
                wholesaleArea.find('input').prop('required', true);
            } else {
                wholesaleArea.slideUp();
                wholesaleArea.find('input').prop('required', false);
            }
        });

        // Wholesale Tier Dynamic Add
        // Initialize index based on existing count
        let wholesaleIndex = {{ ($wholesalePrices && $wholesalePrices->count() > 0) ? $wholesalePrices->count() : 1 }};
        
        $('body').on('click', '.add-wholesale-tier', function() {
            let wrapper = $('#wholesale-wrapper');
            // Clone first row, if it doesn't exist create a template
            let firstRow = wrapper.find('.variant-item').first().clone();
            
            // Fix Names & Values
            firstRow.find('input').each(function(){
                let oldName = $(this).attr('name');
                if(oldName) {
                    $(this).attr('name', oldName.replace(/\[\d+\]/, '[' + wholesaleIndex + ']'));
                    $(this).val('');
                }
            });

            // Change Add Button to Remove Button
            let btnCol = firstRow.find('.add-wholesale-tier').parent();
            btnCol.html('<button type="button" class="btn btn-danger btn-remove-wholesale w-100"><i class="fa fa-trash"></i></button>');
            
            wrapper.append(firstRow);
            wholesaleIndex++;
        });

        $("body").on("click", ".btn-remove-wholesale", function () {
            $(this).closest('.variant-item').remove();
        });

        // Image Increment
        $(".btn-increment").click(function () {
            var html = $(".clone").html();
            $(".increment").append(html);
        });
        $("body").on("click", ".btn-danger", function () {
            $(this).closest(".control-group").remove();
        });

        // Select2 for Variants
        $('.variant-size-select').select2({ multiple: true, width: '100%' });
        $('.variant-color-select').select2({ width: '100%' });

        // Dynamic Variant Add
        let variantIndex = {{ count($groupedVariants ?? []) > 0 ? count($groupedVariants) : 1 }};
        
        $(".add-variant").click(function () {
            let wrapper = $("#variant-wrapper");
            let firstRow = wrapper.find('.variant-item').first().clone();
            
            // Cleanup cloned row
            firstRow.find('.select2-container').remove();
            firstRow.find('.variant-existing-imgs').remove();
            firstRow.find('input').val('');
            
            firstRow.find('select, input').each(function(){
                let oldName = $(this).attr('name');
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
                } else $(this).val(null).trigger('change');
            });

            // Show remove button
            firstRow.find('.btn-remove-row').removeClass('d-none');
            
            wrapper.append(firstRow);
            
            // Re-init Select2
            setTimeout(() => {
                firstRow.find('.variant-size-select').select2({ multiple: true, width: '100%' });
                firstRow.find('.variant-color-select').select2({ width: '100%' });
            }, 100);
            
            variantIndex++;
        });

        $("body").on("click", ".btn-remove-row", function () {
            $(this).closest(".variant-item").remove();
        });

        // Variant Image Preview & Clear
        $("body").on("change", ".variant-img-input", function() {
            var $input = $(this);
            var $preview = $input.siblings(".variant-img-preview");
            var $img = $preview.find("img");
            var file = this.files[0];
            if (file && file.type.startsWith("image/")) {
                var reader = new FileReader();
                reader.onload = function(e) { $img.attr("src", e.target.result); $preview.show(); };
                reader.readAsDataURL(file);
            } else { $preview.hide(); $img.attr("src", ""); }
        });
        $("body").on("click", ".variant-img-clear", function() {
            var $preview = $(this).closest(".variant-img-preview");
            $preview.siblings(".variant-img-input").val("");
            $preview.find("img").attr("src", "");
            $preview.hide();
        });

        // Form Submit Handler - expand sizes, add image_row, keep variant_image files
        $('form[data-parsley-validate]').on('submit', function(e) {
            let variantData = [];
            let vIdx = 0;
            
            $('#variant-wrapper .variant-item').each(function() {
                let $row = $(this);
                let colorId = $row.find('.variant-color-select').val();
                let selectedSizes = $row.find('.variant-size-select').val() || [];
                let price = $row.find('input[name*="[price]"]').val();
                let stock = $row.find('input[name*="[stock]"]').val();

                // Skip if no color, no size, no price, and no stock (completely empty variant)
                if (!colorId && selectedSizes.length === 0 && !price && !stock) {
                    return;
                }

                let rowIdx = $('#variant-wrapper .variant-item').index($row);
                if (selectedSizes.length > 0) {
                    selectedSizes.forEach(function(sizeId) {
                        variantData.push({ idx: vIdx++, c: colorId || null, s: sizeId, p: price || 0, st: stock || 0, row: rowIdx });
                    });
                } else {
                    variantData.push({ idx: vIdx++, c: colorId || null, s: null, p: price || 0, st: stock || 0, row: rowIdx });
                }
            });

            $(this).find('input[name*="variant_price"]:not([type="file"]), select[name*="variant_price"]').remove();
            if (variantData.length > 0) {
                variantData.forEach(function(v) {
                    $('<input>').attr({type:'hidden', name:`variant_price[${v.idx}][color_id]`, value:v.c || ''}).appendTo('form');
                    $('<input>').attr({type:'hidden', name:`variant_price[${v.idx}][size_id]`, value:v.s || ''}).appendTo('form');
                    $('<input>').attr({type:'hidden', name:`variant_price[${v.idx}][price]`, value:v.p}).appendTo('form');
                    $('<input>').attr({type:'hidden', name:`variant_price[${v.idx}][stock]`, value:v.st}).appendTo('form');
                    $('<input>').attr({type:'hidden', name:`variant_price[${v.idx}][image_row]`, value:v.row}).appendTo('form');
                });
            }
        });

        // AJAX Categories
        $("#category_id").on("change", function () {
            var ajaxId = $(this).val();
            if (ajaxId) {
                $.get("{{ route('vendor.ajax.subcategory') }}?category_id=" + ajaxId, function (res) {
                    $("#subcategory_id").empty().append('<option value="">Select..</option>');
                    if(res) {
                        $.each(res, function (key, value) {
                            $("#subcategory_id").append('<option value="' + key + '">' + value + "</option>");
                        });
                    }
                });
            } else {
                $("#subcategory_id").empty();
            }
        });

        $("#subcategory_id").on("change", function () {
            var ajaxId = $(this).val();
            if (ajaxId) {
                $.get("{{ route('vendor.ajax.childcategory') }}?subcategory_id=" + ajaxId, function (res) {
                    $("#childcategory_id").empty().append('<option value="">Select..</option>');
                    if(res) {
                        $.each(res, function (key, value) {
                            $("#childcategory_id").append('<option value="' + key + '">' + value + "</option>");
                        });
                    }
                });
            } else {
                $("#childcategory_id").empty();
            }
        });
    });
</script>
@endpush