@extends('backEnd.layouts.master')
@section('title','Create Blog')

@section('css')
{{-- Summernote CSS --}}
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
    .form-control {
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

    /* Image Upload Box */
    .image-upload-box {
        border: 2px dashed #eef2f7;
        border-radius: 10px;
        padding: 20px;
        text-align: center;
        cursor: pointer;
        position: relative;
        transition: 0.3s;
        background: #f9fbfd;
        min-height: 200px;
        display: flex;
        flex-direction: column;
        justify-content: center;
        align-items: center;
    }
    .image-upload-box:hover {
        border-color: #727cf5;
        background: #fff;
    }
    .preview-img {
        max-width: 100%;
        max-height: 180px;
        border-radius: 6px;
        display: none;
        box-shadow: 0 4px 10px rgba(0,0,0,0.05);
        object-fit: cover;
    }
    .upload-placeholder i {
        font-size: 32px;
        color: #98a6ad;
        margin-bottom: 10px;
    }
    .upload-placeholder p {
        font-size: 13px;
        color: #6c757d;
        font-weight: 500;
        margin: 0;
    }

    /* Summernote Customization */
    .note-editor.note-frame {
        border: 1px solid #eef2f7;
        border-radius: 8px;
        box-shadow: none;
    }
    .note-toolbar {
        background-color: #f9fbfd;
        border-bottom: 1px solid #eef2f7;
        border-radius: 8px 8px 0 0;
    }
    .note-editable {
        background-color: #fff !important;
        min-height: 300px;
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
                    <h4 class="page-title mb-1 text-dark fw-bold">Create New Blog</h4>
                    <p class="text-muted font-size-13 mb-0">Write engaging content for your audience.</p>
                </div>
                <div class="page-title-right">
                    <a href="{{ route('admin.blog.index') }}" class="btn btn-light rounded-pill border shadow-sm px-4">
                        <i class="fe-arrow-left me-1"></i> Back to List
                    </a>
                </div>
            </div>
        </div>
    </div>

    <form action="{{ route('admin.blog.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <div class="row">
            
            <div class="col-lg-8">
                
                <div class="card">
                    <div class="card-header">
                        <div class="header-icon"><i class="fe-edit-3"></i></div>
                        <h5 class="card-title">Blog Content</h5>
                    </div>
                    <div class="card-body">
                        
                        <div class="form-group mb-4">
                            <label class="form-label">Blog Title <span class="text-danger">*</span></label>
                            <input type="text" name="title" class="form-control @error('title') is-invalid @enderror" 
                                   value="{{ old('title') }}" placeholder="Enter a catchy title" required>
                            @error('title')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group mb-4">
                            <label class="form-label">Short Description</label>
                            <textarea name="short_description" class="form-control" rows="3" 
                                      placeholder="Brief summary of the blog...">{{ old('short_description') }}</textarea>
                        </div>

                        <div class="form-group mb-0">
                            <label class="form-label">Full Content <span class="text-danger">*</span></label>
                            <textarea name="description" class="summernote form-control @error('description') is-invalid @enderror" 
                                      required>{{ old('description') }}</textarea>
                            @error('description')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                
                <div class="card mb-4">
                    <div class="card-header">
                        <div class="header-icon"><i class="fe-settings"></i></div>
                        <h5 class="card-title">Publish</h5>
                    </div>
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-4 p-3 bg-light rounded border border-light">
                            <div>
                                <h6 class="mb-1 text-dark fw-bold">Active Status</h6>
                                <p class="text-muted font-size-12 mb-0">Publish this post?</p>
                            </div>
                            <label class="switch">
                                <input type="checkbox" name="status" value="1" checked>
                                <span class="slider round"></span>
                            </label>
                        </div>

                        <button type="submit" class="btn btn-submit w-100 rounded-pill">
                            <i class="fe-save me-1"></i> Save Blog
                        </button>
                    </div>
                </div>

                <div class="card">
                    <div class="card-header">
                        <div class="header-icon"><i class="fe-image"></i></div>
                        <h5 class="card-title">Featured Image</h5>
                    </div>
                    <div class="card-body">
                        <div class="image-upload-box" onclick="document.getElementById('image').click()">
                            <input type="file" name="image" id="image" class="d-none" accept="image/*" onchange="readURL(this)">
                            
                            <div id="upload_placeholder" class="upload-placeholder">
                                <i class="fe-upload-cloud"></i>
                                <p>Click to upload image</p>
                                <small class="text-muted d-block mt-2">JPG, PNG, WEBP (Max 2MB)</small>
                            </div>
                            
                            <img id="preview_image" class="preview-img" src="#" alt="Preview">
                        </div>
                        @error('image')
                            <div class="text-danger small mt-2 text-center">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

            </div>
        </div>
    </form>
</div>
@endsection

@section('script')
{{-- Summernote JS --}}
<script src="{{asset('public/backEnd')}}/assets/libs/summernote/summernote-lite.min.js"></script>

<script>
    $(document).ready(function() {
        // Initialize Summernote
        $(".summernote").summernote({
            placeholder: "Write your blog content here...",
            tabsize: 2,
            height: 300,
            toolbar: [
                ['style', ['style']],
                ['font', ['bold', 'underline', 'clear']],
                ['color', ['color']],
                ['para', ['ul', 'ol', 'paragraph']],
                ['table', ['table']],
                ['insert', ['link', 'picture', 'video']],
                ['view', ['fullscreen', 'codeview', 'help']]
            ]
        });
    });

    // Image Upload Preview Function
    function readURL(input) {
        if (input.files && input.files[0]) {
            var reader = new FileReader();
            reader.onload = function(e) {
                $('#preview_image').attr('src', e.target.result).show();
                $('#upload_placeholder').hide();
            }
            reader.readAsDataURL(input.files[0]);
        }
    }
</script>
@endsection