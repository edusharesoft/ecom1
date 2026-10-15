@extends('backEnd.layouts.master')
@section('title','Create Category')

@section('css')
<link href="{{asset('public/backEnd')}}/assets/libs/select2/css/select2.min.css" rel="stylesheet" type="text/css" />
<link href="{{asset('public/backEnd')}}/assets/libs/summernote/summernote-lite.min.css" rel="stylesheet" type="text/css" />

<style>
    /* Premium Card Design */
    .card {
        border: none;
        box-shadow: 0 0 20px rgba(18, 38, 63, 0.03);
        border-radius: 10px;
        background: #fff;
    }
    .card-header {
        background: #fff;
        border-bottom: 1px solid #f1f5f7;
        padding: 15px 20px;
    }
    .card-title {
        font-size: 16px;
        font-weight: 700;
        color: #343a40;
        margin: 0;
        display: flex;
        align-items: center;
    }
    .card-title i {
        margin-right: 8px;
        color: #727cf5;
        background: rgba(114, 124, 245, 0.1);
        padding: 6px;
        border-radius: 5px;
        font-size: 14px;
    }

    /* Form Inputs */
    .form-label {
        font-weight: 600;
        font-size: 13px;
        color: #6c757d;
        margin-bottom: 8px;
    }
    .form-control {
        border: 1px solid #eef2f7;
        padding: 10px 15px;
        border-radius: 6px;
        font-size: 14px;
        color: #313b5e;
        transition: all 0.3s;
    }
    .form-control:focus {
        border-color: #727cf5;
        box-shadow: 0 0 0 0.2rem rgba(114, 124, 245, 0.15);
    }

    /* Custom Image Upload Box */
    .image-upload-box {
        border: 2px dashed #eef2f7;
        border-radius: 10px;
        padding: 20px;
        text-align: center;
        cursor: pointer;
        position: relative;
        transition: 0.3s;
        background: #f9fbfd;
    }
    .image-upload-box:hover {
        border-color: #727cf5;
        background: #fff;
    }
    .preview-img {
        max-width: 100%;
        max-height: 120px;
        border-radius: 6px;
        display: none; /* Initially Hidden */
        margin: 0 auto;
        box-shadow: 0 4px 10px rgba(0,0,0,0.05);
    }
    .upload-icon {
        font-size: 24px;
        color: #98a6ad;
        margin-bottom: 8px;
    }
    .upload-text {
        font-size: 12px;
        color: #98a6ad;
    }

    /* Toggle Switch */
    .switch { position: relative; display: inline-block; width: 42px; height: 22px; }
    .switch input { opacity: 0; width: 0; height: 0; }
    .slider { position: absolute; cursor: pointer; top: 0; left: 0; right: 0; bottom: 0; background-color: #eef2f7; transition: .4s; border-radius: 34px; border: 1px solid #dee2e6; }
    .slider:before { position: absolute; content: ""; height: 16px; width: 16px; left: 2px; bottom: 2px; background-color: white; transition: .4s; border-radius: 50%; box-shadow: 0 1px 2px rgba(0,0,0,0.2); }
    input:checked + .slider { background-color: #0acf97; border-color: #0acf97; }
    input:checked + .slider:before { transform: translateX(20px); }
</style>
@endsection

@section('content')
<div class="container-fluid">
    
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-flex align-items-center justify-content-between py-3">
                <h4 class="page-title mb-0 text-dark font-weight-bold">Create New Category</h4>
                <div class="page-title-right">
                    <a href="{{route('categories.index')}}" class="btn btn-light rounded-pill border shadow-sm">
                        <i class="fe-arrow-left me-1"></i> Back to List
                    </a>
                </div>
            </div>
        </div>
    </div>

    <form action="{{route('categories.store')}}" method="POST" enctype="multipart/form-data" data-parsley-validate>
        @csrf
        <div class="row">
            
            <div class="col-lg-8">
                
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="card-title"><i class="fe-file-text"></i> General Information</h5>
                    </div>
                    <div class="card-body">
                        <div class="form-group mb-3">
                            <label for="name" class="form-label">Category Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                   name="name" value="{{ old('name') }}" id="name" 
                                   placeholder="e.g. Smart Phones" required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="card-title"><i class="fe-search"></i> SEO Configuration</h5>
                    </div>
                    <div class="card-body">
                        <div class="form-group mb-3">
                            <label for="meta_title" class="form-label">Meta Title</label>
                            <input type="text" class="form-control @error('meta_title') is-invalid @enderror" 
                                   name="meta_title" value="{{ old('meta_title') }}" id="meta_title" 
                                   placeholder="Enter meta title for search engines">
                            @error('meta_title')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group mb-0">
                            <label for="meta_description" class="form-label">Meta Description</label>
                            <textarea class="summernote form-control @error('meta_description') is-invalid @enderror" 
                                      name="meta_description" id="meta_description">{{ old('meta_description') }}</textarea>
                            @error('meta_description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="card-title"><i class="fe-settings"></i> Visibility</h5>
                    </div>
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-3 p-2 bg-light rounded">
                            <div>
                                <label class="form-label mb-0">Publish Status</label>
                                <small class="d-block text-muted">Show in store</small>
                            </div>
                            <label class="switch">
                                <input type="checkbox" name="status" value="1" checked>
                                <span class="slider round"></span>
                            </label>
                        </div>

                        <div class="d-flex justify-content-between align-items-center p-2 bg-light rounded">
                            <div>
                                <label class="form-label mb-0">Front View</label>
                                <small class="d-block text-muted">Show on homepage</small>
                            </div>
                            <label class="switch">
                                <input type="checkbox" name="front_view" value="1">
                                <span class="slider round"></span>
                            </label>
                        </div>
                    </div>
                </div>

                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="card-title"><i class="fe-image"></i> Media</h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-4">
                            <label class="form-label">Main Image <span class="text-danger">*</span></label>
                            <div class="image-upload-box" onclick="document.getElementById('image').click()">
                                <i class="fe-upload-cloud upload-icon" id="icon_main"></i>
                                <p class="upload-text mb-0" id="text_main">Click to upload image</p>
                                <img id="preview_main" class="preview-img mt-2" src="#" alt="Preview">
                                <input type="file" name="image" id="image" class="d-none" onchange="readURL(this, 'preview_main', 'icon_main', 'text_main')" required>
                            </div>
                            @error('image')
                                <span class="text-danger small">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="mb-0">
                            <label class="form-label">Category Icon</label>
                            <div class="image-upload-box" onclick="document.getElementById('icon').click()">
                                <i class="fe-image upload-icon" id="icon_sub"></i>
                                <p class="upload-text mb-0" id="text_sub">Click to upload icon</p>
                                <img id="preview_sub" class="preview-img mt-2" src="#" alt="Preview" style="max-height: 60px;">
                                <input type="file" name="icon" id="icon" class="d-none" onchange="readURL(this, 'preview_sub', 'icon_sub', 'text_sub')">
                            </div>
                            @error('icon')
                                <span class="text-danger small">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                </div>

                <button type="submit" class="btn btn-success w-100 rounded-pill shadow-lg py-2 fw-bold">
                    <i class="fe-check-circle me-1"></i> Save Category
                </button>

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
        // Initialize Summernote with cleaner toolbar
        $(".summernote").summernote({
            placeholder: "Write a short SEO description...",
            height: 120,
            toolbar: [
                ['style', ['bold', 'italic', 'underline', 'clear']],
                ['para', ['ul', 'ol']],
                ['misc', ['fullscreen', 'codeview']]
            ]
        });
        
        $(".select2").select2();
    });

    // Image Preview Function
    function readURL(input, previewId, iconId, textId) {
        if (input.files && input.files[0]) {
            var reader = new FileReader();
            reader.onload = function(e) {
                $('#' + previewId).attr('src', e.target.result).show();
                $('#' + iconId).hide();
                $('#' + textId).hide();
            }
            reader.readAsDataURL(input.files[0]);
        }
    }
</script>
@endsection