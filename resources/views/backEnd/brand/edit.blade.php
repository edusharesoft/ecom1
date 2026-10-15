@extends('backEnd.layouts.master')
@section('title','Edit Brand')

@section('css')
<link href="{{asset('public/backEnd')}}/assets/libs/select2/css/select2.min.css" rel="stylesheet" type="text/css" />

<style>
    /* Global Styles */
    body { background-color: #f8fafc; }
    
    /* Card Styles */
    .card {
        border: none;
        border-radius: 12px;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.03);
        background: #ffffff;
        margin-bottom: 24px;
        transition: all 0.3s ease;
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
        font-weight: 700;
        color: #475569;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin-bottom: 8px;
        display: block;
    }

    .form-control {
        border: 1px solid #e2e8f0;
        border-radius: 8px;
        padding: 12px 16px;
        font-size: 15px;
        color: #1e293b;
        background-color: #f8fafc;
        transition: all 0.2s;
    }

    .form-control:focus {
        background-color: #ffffff;
        border-color: #6366f1;
        box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.1);
    }

    /* Logo Upload Area */
    .logo-upload-container {
        padding: 20px;
        text-align: center;
        border-radius: 12px;
        background: #fff;
    }
    
    .logo-preview-box {
        width: 100%;
        height: 160px;
        border-radius: 12px;
        object-fit: contain;
        background: #f8fafc;
        border: 2px dashed #cbd5e1;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: 0.3s;
        cursor: pointer;
        position: relative;
        overflow: hidden;
    }
    .logo-preview-box:hover {
        border-color: #6366f1;
        background: #f1f5f9;
    }
    .logo-preview-box img {
        max-width: 80%;
        max-height: 80%;
        z-index: 2;
    }
    .upload-hint {
        position: absolute;
        bottom: 10px;
        left: 0;
        right: 0;
        font-size: 11px;
        color: #94a3b8;
        z-index: 1;
    }

    /* Action Toolbar */
    .action-toolbar {
        background: #f8fafc;
        border-radius: 8px;
        padding: 15px 20px;
        margin-top: 25px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        border: 1px solid #e2e8f0;
    }

    /* Modern Switch */
    .toggle-checkbox { display: none; }
    .toggle-label {
        width: 44px;
        height: 24px;
        background: #cbd5e1;
        border-radius: 50px;
        position: relative;
        cursor: pointer;
        transition: 0.3s;
    }
    .toggle-label::after {
        content: "";
        width: 18px;
        height: 18px;
        background: #fff;
        border-radius: 50%;
        position: absolute;
        top: 3px;
        left: 3px;
        transition: 0.3s;
        box-shadow: 0 1px 3px rgba(0,0,0,0.1);
    }
    .toggle-checkbox:checked + .toggle-label { background: #10b981; }
    .toggle-checkbox:checked + .toggle-label::after { transform: translateX(20px); }

    /* Submit Button */
    .btn-update {
        background: #1e293b;
        color: #fff;
        border: none;
        padding: 10px 25px;
        border-radius: 6px;
        font-weight: 600;
        font-size: 14px;
        transition: 0.3s;
        display: inline-flex;
        align-items: center;
        gap: 8px;
    }
    .btn-update:hover {
        background: #0f172a;
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(15, 23, 42, 0.15);
    }
</style>
@endsection

@section('content')
<div class="container-fluid">
    
    <div class="row align-items-center mb-4 mt-4">
        <div class="col">
            <h4 class="m-0 font-weight-bold text-dark">Edit Brand: {{ $edit_data->name }}</h4>
        </div>
        <div class="col-auto">
            <a href="{{route('brands.index')}}" class="btn btn-light border btn-sm rounded-pill px-3 shadow-sm">
                <i class="fe-arrow-left me-1"></i> Back
            </a>
        </div>
    </div>

    <form action="{{route('brands.update')}}" method="POST" enctype="multipart/form-data" data-parsley-validate>
        @csrf
        <input type="hidden" value="{{$edit_data->id}}" name="id">

        <div class="row">
            
            <div class="col-lg-8">
                <div class="card">
                    <div class="card-body p-4">
                        
                        <div class="form-group">
                            <label for="name" class="form-label">Brand Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                   name="name" value="{{ $edit_data->name }}" id="name" 
                                   placeholder="Enter brand name" required>
                            @error('name')
                                <div class="invalid-feedback mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="action-toolbar">
                            <div class="d-flex align-items-center gap-3">
                                <label class="mb-0 fw-bold text-dark font-size-14" for="status" style="cursor: pointer;">Active Status</label>
                                <div>
                                    <input type="checkbox" id="status" name="status" value="1" class="toggle-checkbox" {{ $edit_data->status == 1 ? 'checked' : '' }}>
                                    <label for="status" class="toggle-label" title="Toggle Status"></label>
                                </div>
                            </div>

                            <button type="submit" class="btn-update">
                                <i class="fe-check-circle"></i> Update Brand
                            </button>
                        </div>

                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="card">
                    <div class="card-body p-4">
                        <label class="form-label mb-2">Brand Logo</label>
                        
                        <div class="logo-upload-container p-0">
                            <div class="logo-preview-box" onclick="document.getElementById('image').click()">
                                <img id="preview_image" src="{{ asset($edit_data->image) }}" alt="Logo">
                                <div class="upload-hint">Click to change</div>
                            </div>
                            
                            <input type="file" name="image" id="image" class="d-none" accept="image/*" onchange="readURL(this)">
                            
                            @error('image')
                                <div class="text-danger small mt-2 text-center">{{ $message }}</div>
                            @enderror

                            <div class="mt-3 text-center">
                                <small class="text-muted d-block" style="font-size: 11px;">
                                    Format: PNG, JPG, WEBP <br> Size: 120x120 px
                                </small>
                            </div>
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

<script>
    function readURL(input) {
        if (input.files && input.files[0]) {
            var reader = new FileReader();
            reader.onload = function(e) {
                $('#preview_image').attr('src', e.target.result);
                $('.upload-hint').text('Image Selected'); // Update hint text
            }
            reader.readAsDataURL(input.files[0]);
        }
    }
</script>
@endsection