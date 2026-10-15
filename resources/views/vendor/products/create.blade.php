@extends('vendor.layouts.app')

@section('title', 'Create Product')
@section('page-title', 'Create Product')

@push('styles')
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">
<link href="{{asset('public/backEnd')}}/assets/libs/select2/css/select2.min.css" rel="stylesheet" type="text/css" />
<link href="{{asset('public/backEnd')}}/assets/libs/summernote/summernote-lite.min.css" rel="stylesheet" type="text/css" />

<style>
    :root {
        --primary-color: #4e73df; /* Change to your brand color */
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

    /* Custom Switch (iOS Style) */
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

    /* Image Upload Wrapper */
    .image-upload-wrapper {
        border: 2px dashed #d1d3e2;
        padding: 20px;
        border-radius: 10px;
        background: #fff;
        transition: all 0.3s;
    }
    .image-upload-wrapper:hover {
        border-color: var(--primary-color);
        background-color: #f0f4ff;
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
</style>
@endpush

@section('content')
<div class="container-fluid px-0">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="mb-1 fw-bold text-dark">Create Product</h4>
            <p class="text-muted mb-0 small">Add a new product to your inventory</p>
        </div>
        <a href="{{ route('vendor.products.index') }}" class="btn btn-outline-secondary rounded-pill px-4">
            <i class="fas fa-arrow-left me-2"></i>Back to List
        </a>
    </div>

    <form action="{{ route('vendor.products.store') }}" method="POST" data-parsley-validate="" enctype="multipart/form-data">
        @csrf
        <div class="row g-4">
            <div class="col-lg-8">
                
                <div class="card">
                    <div class="card-header">Basic Information</div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label for="name" class="form-label">Product Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control form-control-lg fw-bold @error('name') is-invalid @enderror" name="name" value="{{ old('name') }}" id="name" placeholder="e.g. Cotton T-Shirt" required />
                            @error('name') <span class="invalid-feedback"><strong>{{ $message }}</strong></span> @enderror
                        </div>

                        <div class="mb-3">
                            <label for="description" class="form-label">Description <span class="text-danger">*</span></label>
                            <textarea name="description" rows="6" class="summernote form-control @error('description') is-invalid @enderror" required>{{ old('description') }}</textarea>
                            @error('description') <span class="invalid-feedback"><strong>{{ $message }}</strong></span> @enderror
                        </div>

                        <div class="mb-0">
                            <label for="note" class="form-label">Internal Note</label>
                            <textarea name="note" rows="2" class="form-control" placeholder="Private note for this product..."></textarea>
                        </div>
                    </div>
                </div>

                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <span>Product Images</span>
                        <button class="btn btn-sm btn-primary btn-increment rounded-pill" type="button"><i class="fa fa-plus me-1"></i> Add Image</button>
                    </div>
                    <div class="card-body">
                        <div class="increment">
                            <div class="image-upload-wrapper control-group mb-3">
                                <div class="d-flex align-items-center">
                                    <div class="flex-grow-1">
                                        <input type="file" name="image[]" class="form-control @error('image') is-invalid @enderror" required />
                                        @error('image') <span class="invalid-feedback"><strong>{{ $message }}</strong></span> @enderror
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
                                <input type="text" class="form-control" name="pro_video" value="{{ old('pro_video') }}" id="pro_video" placeholder="https://...">
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
                            <div class="variant-item variant-card">
                                <div class="row g-2 align-items-end">
                                    <div class="col-md-3">
                                        <label class="form-label small">Color <small class="text-muted">(Optional)</small></label>
                                        <select name="variant_price[0][color_id]" class="form-control select2 variant-color-select">
                                            <option value="">Select Color (Optional)</option>
                                            @foreach($colors as $color)
                                            <option value="{{ $color->id }}">{{ $color->colorName ?? $color->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label small">Size <small class="text-muted">(Optional)</small></label>
                                        <select name="variant_price[0][size_id][]" class="form-control select2 variant-size-select" multiple>
                                            @foreach($sizes as $size)
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
                        </div>
                    </div>
                </div>

                <div class="card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h6 class="mb-0 fw-bold"><i class="fas fa-tags me-2 text-primary"></i>Wholesale Configuration</h6>
                            <label class="switch">
                                <input type="checkbox" value="1" name="is_wholesale" id="is_wholesale">
                                <span class="slider round"></span>
                            </label>
                        </div>

                        <div id="wholesale_area" style="display:none;" class="mt-4 pt-3 border-top">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <span class="text-muted small">Define pricing tiers for bulk purchases</span>
                                <button type="button" class="btn btn-sm btn-success add-wholesale-tier"><i class="fa fa-plus me-1"></i> Add Tier</button>
                            </div>
                            
                            <div id="wholesale-wrapper">
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
                                <input type="text" name="meta_title" id="meta_title" class="form-control" placeholder="SEO Title">
                            </div>
                            <div class="col-md-6">
                                <label for="meta_keywords" class="form-label">Meta Keywords</label>
                                <input type="text" name="meta_keywords" id="meta_keywords" class="form-control" placeholder="keyword1, keyword2">
                            </div>
                            <div class="col-12">
                                <label for="meta_description" class="form-label">Meta Description</label>
                                <textarea name="meta_description" id="meta_description" class="form-control" rows="3"></textarea>
                            </div>
                            <div class="col-12">
                                <label for="meta_image" class="form-label">Meta Image</label>
                                <input type="file" name="meta_image" id="meta_image" class="form-control">
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
                            <label class="form-label mb-0">Status (Active)</label>
                            <label class="switch">
                                <input type="checkbox" value="1" name="status" checked />
                                <span class="slider round"></span>
                            </label>
                        </div>
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <label class="form-label mb-0">Hot Deal</label>
                            <label class="switch">
                                <input type="checkbox" value="1" name="topsale" />
                                <span class="slider round"></span>
                            </label>
                        </div>
                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <label class="form-label mb-0">Flash Sale</label>
                            <label class="switch">
                                <input type="checkbox" value="1" name="flashsale" />
                                <span class="slider round"></span>
                            </label>
                        </div>

                        <div class="mb-3">
                             <label for="sold" class="form-label">Sold Count</label>
                             <input type="text" class="form-control" name="sold" value="{{ old('sold') }}" id="sold" placeholder="0" />
                        </div>

                        <button type="submit" class="btn btn-primary w-100 py-2 rounded-pill fw-bold shadow-sm">
                            <i class="fas fa-save me-2"></i> Save & Publish
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
                                <option value="{{$category->id}}">{{$category->name}}</option>
                                @endforeach
                            </select>
                            @error('category_id') <span class="invalid-feedback"><strong>{{ $message }}</strong></span> @enderror
                        </div>

                        <div class="mb-3">
                            <label for="subcategory_id" class="form-label">Sub Category</label>
                            <select class="form-control select2" id="subcategory_id" name="subcategory_id">
                                <option value="">Select..</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="childcategory_id" class="form-label">Child Category</label>
                            <select class="form-control select2" id="childcategory_id" name="childcategory_id">
                                <option value="">Select..</option>
                            </select>
                        </div>

                        <div class="mb-0">
                            <label for="brand_id" class="form-label">Brand</label>
                            <select class="form-control select2" name="brand_id">
                                <option value="">Select Brand</option>
                                @foreach($brands as $brand)
                                <option value="{{$brand->id}}">{{$brand->name}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>

                <div class="card">
                    <div class="card-header">Pricing & Inventory</div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label for="product_type" class="form-label">Product Type</label>
                            <select class="form-select bg-light" id="product_type" name="product_type">
                                <option value="physical" selected>Physical Product</option>
                                <option value="digital">Digital Product</option>
                            </select>
                        </div>

                        <div class="row g-2 mb-3">
                            <div class="col-6">
                                <label for="purchase_price" class="form-label">Purchase Price <small class="text-muted">(Optional)</small></label>
                                <input type="text" class="form-control" name="purchase_price" value="{{ old('purchase_price') }}" id="purchase_price" placeholder="0" />
                            </div>
                            <div class="col-6">
                                <label for="old_price" class="form-label">Old Price</label>
                                <input type="text" class="form-control" name="old_price" value="{{ old('old_price') }}" id="old_price" />
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="new_price" class="form-label">New Price (Sale) <small class="text-muted">(Optional)</small></label>
                            <input type="text" class="form-control fw-bold text-success fs-5" name="new_price" value="{{ old('new_price') }}" id="new_price" placeholder="0" />
                        </div>

                        <div class="mb-3">
                            <label for="reseller_price" class="form-label">Reseller Price</label>
                            <input type="text" step="0.01" class="form-control" name="reseller_price" value="{{ old('reseller_price') }}" id="reseller_price" placeholder="Reseller price (optional)" />
                            <small class="text-muted">Special price for resellers. Leave empty if not applicable.</small>
                        </div>

                        <div class="row g-2 mb-3">
                            <div class="col-6">
                                <label for="stock" class="form-label">Stock <small class="text-muted">(Optional)</small></label>
                                <input type="text" class="form-control" name="stock" value="{{ old('stock') }}" id="stock" placeholder="0" />
                            </div>
                            <div class="col-6">
                                <label for="pro_unit" class="form-label">Unit</label>
                                <input type="text" class="form-control" name="pro_unit" value="{{ old('pro_unit') }}" id="pro_unit" placeholder="pc/kg" />
                            </div>
                        </div>

                        <div id="advance_area">
                            <div class="mb-0">
                                <label for="advance_amount" class="form-label">Advance Payment (Tk)</label>
                                <input type="text" class="form-control border-primary" name="advance_amount" value="{{ old('advance_amount') }}" id="advance_amount" placeholder="0" />
                            </div>
                        </div>

                        <div id="digital_area" style="display:none;" class="mt-3 p-3 bg-light rounded border border-dashed">
                            <div class="mb-3">
                                <label for="digital_file" class="form-label">Upload File (ZIP/PDF)</label>
                                <input type="file" class="form-control" name="digital_file" id="digital_file">
                            </div>
                            <div class="row g-2">
                                <div class="col-6">
                                    <label for="download_limit" class="form-label small">Download Limit</label>
                                    <input type="number" class="form-control" name="download_limit" id="download_limit" value="5" min="1">
                                </div>
                                <div class="col-6">
                                    <label for="download_expire_days" class="form-label small">Expire Days</label>
                                    <input type="number" class="form-control" name="download_expire_days" id="download_expire_days" value="7" min="1">
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
        // Init Summernote & Select2
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

        // Product Type Toggle Logic
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
        toggleFields(); // Run on load

        // Wholesale Toggle Logic
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
        let wholesaleIndex = 1;
        $('.add-wholesale-tier').on('click', function() {
            let wrapper = $('#wholesale-wrapper');
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

        // Image Increment Logic
        $(".btn-increment").click(function () {
            var html = $(".clone").html();
            $(".increment").append(html);
        });
        $("body").on("click", ".btn-danger", function () {
            $(this).closest(".control-group").remove();
        });

        // Initialize Select2 for Variants
        $('.variant-size-select').select2({ multiple: true, width: '100%' });
        $('.variant-color-select').select2({ width: '100%' });

        // Dynamic Variant Add Logic
        let variantIndex = 1;
        $(".add-variant").click(function () {
            let wrapper = $("#variant-wrapper");
            let firstRow = wrapper.find('.variant-item').first().clone();
            
            // Clean up cloned row
            firstRow.find('.select2-container').remove();
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
                if ($(this).attr('type') !== 'file') $(this).val(null).trigger('change');
                else {
                    $(this).val('');
                    $(this).siblings('.variant-img-preview').hide().find('img').attr('src', '');
                }
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