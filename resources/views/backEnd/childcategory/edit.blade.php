@extends('backEnd.layouts.master')
@section('title','Edit Childcategory')

@section('css')
<link href="{{asset('public/backEnd')}}/assets/libs/select2/css/select2.min.css" rel="stylesheet" type="text/css" />
<link href="{{asset('public/backEnd')}}/assets/libs/summernote/summernote-lite.min.css" rel="stylesheet" type="text/css" />

<style>
    /* Premium Card Design */
    .card {
        border: none;
        box-shadow: 0 0 20px rgba(18, 38, 63, 0.03);
        border-radius: 12px;
        background: #fff;
        margin-bottom: 24px;
    }
    .card-header {
        background: #fff;
        border-bottom: 1px solid #f1f5f7;
        padding: 20px 25px;
        display: flex;
        align-items: center;
        gap: 10px;
    }
    .card-title {
        font-size: 16px;
        font-weight: 700;
        color: #2d3436;
        margin: 0;
    }
    .header-icon {
        width: 35px;
        height: 35px;
        background: rgba(114, 124, 245, 0.1);
        color: #727cf5;
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 16px;
    }

    /* Form Styles */
    .form-label {
        font-weight: 600;
        font-size: 13px;
        color: #636e72;
        margin-bottom: 8px;
    }
    .form-control, .form-select {
        background-color: #fbfcff;
        border: 1px solid #eef2f7;
        padding: 12px 15px;
        border-radius: 8px;
        font-size: 14px;
        color: #2d3436;
        transition: all 0.3s;
    }
    .form-control:focus {
        background-color: #fff;
        border-color: #727cf5;
        box-shadow: 0 0 0 4px rgba(114, 124, 245, 0.1);
    }

    /* Select2 Customization */
    .select2-container--default .select2-selection--single {
        background-color: #fbfcff;
        border: 1px solid #eef2f7;
        border-radius: 8px;
        height: 46px;
        display: flex;
        align-items: center;
    }
    .select2-container--default .select2-selection--single .select2-selection__rendered {
        padding-left: 15px;
        color: #2d3436;
        font-size: 14px;
    }
    .select2-container--default .select2-selection--single .select2-selection__arrow {
        height: 44px;
        right: 10px;
    }

    /* Toggle Switch */
    .switch { position: relative; display: inline-block; width: 46px; height: 24px; }
    .switch input { opacity: 0; width: 0; height: 0; }
    .slider { position: absolute; cursor: pointer; top: 0; left: 0; right: 0; bottom: 0; background-color: #eef2f7; transition: .4s; border-radius: 34px; border: 1px solid #dee2e6; }
    .slider:before { position: absolute; content: ""; height: 18px; width: 18px; left: 2px; bottom: 2px; background-color: white; transition: .4s; border-radius: 50%; box-shadow: 0 2px 4px rgba(0,0,0,0.1); }
    input:checked + .slider { background-color: #0acf97; border-color: #0acf97; }
    input:checked + .slider:before { transform: translateX(22px); }

    /* Button Style */
    .btn-submit {
        background: linear-gradient(45deg, #0acf97, #06b6d4);
        border: none;
        color: white;
        padding: 12px;
        font-weight: 600;
        letter-spacing: 0.5px;
        box-shadow: 0 4px 15px rgba(10, 207, 151, 0.3);
        transition: 0.3s;
    }
    .btn-submit:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(10, 207, 151, 0.4);
    }
</style>
@endsection

@section('content')
<div class="container-fluid">
    
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-flex align-items-center justify-content-between py-4">
                <div>
                    <h4 class="page-title mb-1 text-dark fw-bold">Edit Childcategory: {{ $edit_data->childcategoryName }}</h4>
                    <p class="text-muted font-size-13 mb-0">Update childcategory details and mapping.</p>
                </div>
                <div class="page-title-right">
                    <a href="{{route('childcategories.index')}}" class="btn btn-light rounded-pill border shadow-sm px-4">
                        <i class="fe-arrow-left me-1"></i> Back to List
                    </a>
                </div>
            </div>
        </div>
    </div>

    <form action="{{route('childcategories.update')}}" method="POST" name="editForm" enctype="multipart/form-data" data-parsley-validate>
        @csrf
        <input type="hidden" value="{{$edit_data->id}}" name="id">

        <div class="row">
            
            <div class="col-lg-8">
                
                <div class="card mb-4">
                    <div class="card-header">
                        <div class="header-icon"><i class="fe-edit"></i></div>
                        <h5 class="card-title">Basic Information</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group mb-4">
                                    <label for="subcategory_id" class="form-label">Parent Subcategory <span class="text-danger">*</span></label>
                                    <select class="form-control select2 @error('subcategory_id') is-invalid @enderror" 
                                            id="subcategory_id" name="subcategory_id" required>
                                        <option value="">Select Subcategory</option>
                                        @foreach ($menucategories as $category)
                                            <optgroup label="{{ $category->name }}">
                                                @foreach ($category->subcategories as $subcat)
                                                    <option value="{{ $subcat->id }}" 
                                                        @if($subcat->id == $edit_data->subcategory_id) selected @endif>
                                                        {{ $subcat->subcategoryName }}
                                                    </option>
                                                @endforeach
                                            </optgroup>
                                        @endforeach
                                    </select>
                                    @error('subcategory_id')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group mb-4">
                                    <label for="childcategoryName" class="form-label">Childcategory Name <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('childcategoryName') is-invalid @enderror" 
                                           name="childcategoryName" value="{{ $edit_data->childcategoryName }}" 
                                           id="childcategoryName" required>
                                    @error('childcategoryName')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card mb-4">
                    <div class="card-header">
                        <div class="header-icon"><i class="fe-search"></i></div>
                        <h5 class="card-title">SEO Configuration</h5>
                    </div>
                    <div class="card-body">
                        <div class="form-group mb-4">
                            <label for="meta_title" class="form-label">Meta Title</label>
                            <input type="text" class="form-control @error('meta_title') is-invalid @enderror" 
                                   name="meta_title" value="{{ $edit_data->meta_title }}" id="meta_title">
                            @error('meta_title')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group mb-0">
                            <label for="meta_description" class="form-label">Meta Description</label>
                            <textarea class="summernote form-control @error('meta_description') is-invalid @enderror" 
                                      name="meta_description" id="meta_description">{!! $edit_data->meta_description !!}</textarea>
                            @error('meta_description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                
                <div class="card">
                    <div class="card-header">
                        <div class="header-icon"><i class="fe-settings"></i></div>
                        <h5 class="card-title">Visibility</h5>
                    </div>
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-4 p-3 bg-light rounded border border-light">
                            <div>
                                <h6 class="mb-1 text-dark fw-bold">Status</h6>
                                <p class="text-muted font-size-12 mb-0">Enable or disable childcategory</p>
                            </div>
                            <label class="switch">
                                <input type="checkbox" name="status" value="1" {{ $edit_data->status == 1 ? 'checked' : '' }}>
                                <span class="slider round"></span>
                            </label>
                        </div>

                        <button type="submit" class="btn btn-submit w-100 rounded-pill">
                            <i class="fe-save me-1"></i> Update Changes
                        </button>
                    </div>
                </div>

                <div class="card bg-soft-primary border-0 mt-4">
                    <div class="card-body">
                        <div class="d-flex align-items-start">
                            <i class="fe-info font-size-18 me-2 text-primary"></i>
                            <p class="mb-0 font-size-13 text-primary">
                                <strong>Note:</strong> Childcategories provide the deepest level of product organization. Ensure SEO titles are unique.
                            </p>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </form>
</div>
@endsection

@section('script')
<script src="{{asset('public/backEnd/')}}/assets/libs/parsleyjs/parsley.min.js"></script>
<script src="{{asset('public/backEnd/')}}/assets/js/pages/form-validation.init.js"></script>
<script src="{{asset('public/backEnd/')}}/assets/libs/select2/js/select2.min.js"></script>
<script src="{{asset('public/backEnd/')}}/assets/libs/summernote/summernote-lite.min.js"></script>

<script>
    $(document).ready(function(){
        // Summernote Setup
        $(".summernote").summernote({
            placeholder: "Enter SEO description...",
            height: 120,
            toolbar: [
                ['style', ['bold', 'italic', 'underline', 'clear']],
                ['para', ['ul', 'ol']],
                ['misc', ['fullscreen', 'codeview']]
            ]
        });

        // Select2 Setup
        $(".select2").select2({
            width: '100%'
        });
    });
</script>
@endsection