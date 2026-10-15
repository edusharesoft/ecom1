@extends('backEnd.layouts.master')
@section('title','Create Brand')

@section('css')
<link href="{{asset('public/backEnd')}}/assets/libs/select2/css/select2.min.css" rel="stylesheet" type="text/css" />

<style>
    /* Global Styles */
    body { background-color: #f3f4f6; color: #475569; }
    
    /* Card Styles */
    .card {
        border: none;
        border-radius: 10px;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05), 0 2px 4px -1px rgba(0, 0, 0, 0.03);
        background: #ffffff;
        margin-bottom: 20px;
    }
    .card-header {
        background: #fff;
        border-bottom: 1px solid #f1f5f9;
        padding: 15px 20px;
    }
    .card-title {
        font-size: 15px;
        font-weight: 700;
        color: #1e293b;
        margin: 0;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    /* Form Elements */
    .form-label {
        font-size: 13px;
        font-weight: 600;
        color: #64748b;
        margin-bottom: 8px;
    }
    .form-control {
        border: 1px solid #e2e8f0;
        border-radius: 6px;
        padding: 10px 15px;
        font-size: 14px;
        color: #334155;
        transition: all 0.2s;
    }
    .form-control:focus {
        border-color: #6366f1;
        box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.1);
        outline: none;
    }

    /* Logo Upload Area */
    .logo-upload-box {
        border: 2px dashed #cbd5e1;
        border-radius: 8px;
        padding: 20px;
        text-align: center;
        cursor: pointer;
        background: #f8fafc;
        transition: 0.3s;
        position: relative;
        min-height: 160px;
        display: flex;
        flex-direction: column;
        justify-content: center;
        align-items: center;
    }
    .logo-upload-box:hover {
        border-color: #6366f1;
        background: #fff;
    }
    .preview-img {
        max-width: 100%;
        max-height: 120px;
        object-fit: contain;
        display: none;
    }
    .upload-placeholder i {
        font-size: 28px;
        color: #94a3b8;
        margin-bottom: 8px;
    }
    .upload-placeholder p {
        font-size: 12px;
        color: #64748b;
        margin: 0;
    }

    /* Switch & Button */
    .switch { position: relative; display: inline-block; width: 42px; height: 24px; }
    .switch input { opacity: 0; width: 0; height: 0; }
    .slider { position: absolute; cursor: pointer; top: 0; left: 0; right: 0; bottom: 0; background-color: #cbd5e1; transition: .4s; border-radius: 34px; }
    .slider:before { position: absolute; content: ""; height: 18px; width: 18px; left: 3px; bottom: 3px; background-color: white; transition: .4s; border-radius: 50%; }
    input:checked + .slider { background-color: #10b981; }
    input:checked + .slider:before { transform: translateX(18px); }

    .btn-save {
        background-color: #1e293b;
        color: #fff;
        font-weight: 600;
        border: none;
        padding: 10px 20px;
        border-radius: 6px;
        width: 100%;
        transition: 0.3s;
    }
    .btn-save:hover { background-color: #0f172a; }
    
    .status-box {
        display: flex;
        justify-content: space-between;
        align-items: center;
        background: #f8fafc;
        padding: 12px 15px;
        border-radius: 8px;
        border: 1px solid #e2e8f0;
    }
</style>
@endsection

@section('content')
<div class="container-fluid">
    
    <div class="row align-items-center mb-3 mt-3">
        <div class="col-6">
            <h4 class="m-0 font-weight-bold text-dark">Add New Brand</h4>
        </div>
        <div class="col-6 text-end">
            <a href="{{route('brands.index')}}" class="btn btn-outline-secondary btn-sm rounded-pill px-3">
                <i class="fe-arrow-left me-1"></i> Back
            </a>
        </div>
    </div>

    <form action="{{route('brands.store')}}" method="POST" enctype="multipart/form-data" data-parsley-validate>
        @csrf
        <div class="row">
            
            <div class="col-lg-8">
                
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title">Basic Information</h5>
                    </div>
                    <div class="card-body">
                        <div class="form-group mb-0">
                            <label for="name" class="form-label">Brand Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                   name="name" value="{{ old('name') }}" id="name" 
                                   placeholder="e.g. Nike" required autofocus>
                            @error('name')
                                <div class="invalid-feedback mt-1">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title">Publish</h5>
                    </div>
                    <div class="card-body">
                        <div class="status-box">
                            <div>
                                <h6 class="mb-0 fw-bold text-dark font-size-14">Active Status</h6>
                                <small class="text-muted" style="font-size: 11px;">Enable this brand on website</small>
                            </div>
                            <label class="switch">
                                <input type="checkbox" name="status" value="1" checked>
                                <span class="slider round"></span>
                            </label>
                        </div>
                        @error('status')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror

                        <button type="submit" class="btn-save mt-3">
                            <i class="fe-save me-1"></i> Save Brand
                        </button>
                    </div>
                </div>

            </div>

            <div class="col-lg-4">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title">Brand Logo</h5>
                    </div>
                    <div class="card-body">
                        <div class="logo-upload-box" onclick="document.getElementById('image').click()">
                            <input type="file" name="image" id="image" class="d-none" accept="image/*" onchange="readURL(this)">
                            
                            <img id="preview_image" class="preview-img" src="#" alt="Preview">
                            
                            <div id="upload_placeholder" class="upload-placeholder">
                                <i class="fe-image"></i>
                                <p>Click to upload logo</p>
                                <small class="text-muted d-block mt-1">(PNG, JPG, WEBP)</small>
                            </div>
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
<script src="{{asset('public/backEnd/')}}/assets/libs/parsleyjs/parsley.min.js"></script>
<script src="{{asset('public/backEnd/')}}/assets/js/pages/form-validation.init.js"></script>

<script>
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