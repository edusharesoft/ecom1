@extends('backEnd.layouts.master')
@section('title','Edit Banner')

@section('css')
<style>
    /* 1. PROFESSIONAL CARD CONTAINER */
    .studio-card {
        background: #fff;
        border-radius: 12px;
        box-shadow: 0 5px 20px rgba(0,0,0,0.05);
        border: 1px solid #e2e8f0;
        overflow: hidden;
    }

    /* 2. REAL VIEW IMAGE CANVAS (FIXED) */
    .image-canvas-wrapper {
        background-color: #f1f5f9;
        /* Checkerboard pattern for transparency */
        background-image:
            linear-gradient(45deg, #e2e8f0 25%, transparent 25%),
            linear-gradient(-45deg, #e2e8f0 25%, transparent 25%),
            linear-gradient(45deg, transparent 75%, #e2e8f0 75%),
            linear-gradient(-45deg, transparent 75%, #e2e8f0 75%);
        background-size: 20px 20px;
        background-position: 0 0, 0 10px, 10px -10px, -10px 0px;
        
        /* Removed padding to let image fill area */
        padding: 0; 
        
        position: relative;
        text-align: center;
        border-bottom: 1px solid #e2e8f0;
        /* Changed from min-height to allow content to dictate height, 
           but keeping a flexible display */
        display: flex;
        justify-content: center;
        align-items: center;
        width: 100%;
        overflow: hidden; /* Ensures no spillover */
    }

    .real-view-image {
        width: 100%; /* Forces image to take full width */
        height: auto; /* Maintains aspect ratio */
        display: block;
        /* Removed max-height restriction to allow full viewing of large banners, 
           or you can set it if you want to limit vertical scrolling */
    }
    
    /* Upload Button Overlay */
    .upload-overlay-btn {
        position: absolute;
        bottom: 20px;
        right: 20px;
        background: rgba(255, 255, 255, 0.95);
        color: #0f172a;
        padding: 10px 24px;
        border-radius: 50px; /* Pill shape for modern look */
        box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        cursor: pointer;
        font-weight: 600;
        font-size: 13px;
        transition: all 0.2s;
        border: 1px solid #cbd5e1;
        z-index: 10;
        display: flex;
        align-items: center;
        gap: 8px;
    }
    .upload-overlay-btn:hover {
        background: #fff;
        transform: translateY(-2px);
        box-shadow: 0 6px 15px rgba(0,0,0,0.2);
    }

    /* 3. MODERN CATEGORY TAGS */
    .category-label {
        font-size: 11px;
        text-transform: uppercase;
        color: #64748b;
        letter-spacing: 0.8px;
        font-weight: 700;
        margin-bottom: 12px;
        display: block;
    }
    
    .radio-tile-group {
        display: flex;
        flex-wrap: wrap;
        gap: 10px;
    }
    .radio-input {
        display: none;
    }
    .radio-tile {
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 10px 20px;
        background-color: #fff;
        border: 1px solid #e2e8f0;
        border-radius: 6px;
        cursor: pointer;
        transition: all 0.2s ease;
        color: #475569;
        font-weight: 600;
        font-size: 13px;
    }
    .radio-tile:hover {
        border-color: #94a3b8;
        background-color: #f8fafc;
    }
    /* Active State */
    .radio-input:checked + .radio-tile {
        background-color: #2563eb;
        border-color: #2563eb;
        color: white;
        box-shadow: 0 4px 10px rgba(37, 99, 235, 0.2);
    }

    /* 4. SETTINGS AREA */
    .settings-area {
        padding: 30px;
    }
    .input-clean {
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        padding: 12px 15px;
        border-radius: 8px;
        font-size: 14px;
        color: #334155;
        transition: all 0.2s;
    }
    .input-clean:focus {
        background: #fff;
        border-color: #2563eb;
        box-shadow: 0 0 0 4px rgba(37, 99, 235, 0.1);
    }
</style>
@endsection

@section('content')
<div class="container-fluid py-4">
    
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold m-0 text-dark">Edit Banner</h4>
            <span class="text-muted small">Update visual content & links</span>
        </div>
        <div class="d-flex gap-2">
            <a href="{{route('banners.index')}}" class="btn btn-light border fw-bold text-secondary px-3">
                Cancel
            </a>
            <button type="submit" form="bannerForm" class="btn btn-primary fw-bold px-4 shadow-sm">
                <i class="fe-save me-1"></i> Save Changes
            </button>
        </div>
    </div>

    <form action="{{route('banners.update')}}" method="POST" id="bannerForm" enctype="multipart/form-data">
        @csrf
        <input type="hidden" value="{{$edit_data->id}}" name="id">

        <div class="row justify-content-center">
            <div class="col-xl-9 col-lg-10">
                
                <div class="studio-card">
                    
                    <div class="image-canvas-wrapper">
                        <img id="realPreview" src="{{asset($edit_data->image)}}" class="real-view-image" alt="Banner Preview">
                        
                        <label class="upload-overlay-btn" for="imageUpload">
                            <i class="fe-camera"></i> <span>Change Image</span>
                        </label>
                        <input type="file" name="image" id="imageUpload" class="d-none" accept="image/*" onchange="updateCanvas(this)">
                    </div>

                    <div class="settings-area">
                        <div class="row g-4">
                            
                            <div class="col-lg-7">
                                <label class="category-label">Select Placement Category</label>
                                <div class="radio-tile-group">
                                    @foreach($categories as $cat)
                                        <label>
                                            <input type="radio" 
                                                   name="category_id" 
                                                   class="radio-input" 
                                                   value="{{$cat->id}}"
                                                   @if($edit_data->category_id == $cat->id) checked @endif>
                                            <span class="radio-tile">
                                                {{$cat->name}}
                                            </span>
                                        </label>
                                    @endforeach
                                </div>
                                @error('category_id') <div class="text-danger small mt-2">{{ $message }}</div> @enderror
                            </div>

                            <div class="col-lg-5 ps-lg-4 border-start-lg">
                                <div class="mb-4">
                                    <label class="category-label">Destination URL</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-white border-end-0 text-muted"><i class="fe-link"></i></span>
                                        <input type="text" class="form-control input-clean border-start-0" name="link" value="{{$edit_data->link}}" placeholder="https://example.com/offer">
                                    </div>
                                    @error('link') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                                </div>

                                <div>
                                    <label class="category-label">Publication Status</label>
                                    <div class="d-flex align-items-center justify-content-between p-3 rounded border bg-light">
                                        <div>
                                            <span class="fw-bold text-dark d-block" style="font-size: 14px;">Active Mode</span>
                                            <small class="text-muted" style="font-size: 12px;">Visible on website</small>
                                        </div>
                                        <div class="form-check form-switch">
                                            <input class="form-check-input" type="checkbox" name="status" value="1" @if($edit_data->status==1) checked @endif style="width: 3em; height: 1.5em; cursor:pointer;">
                                        </div>
                                    </div>
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

@section('script')
<script>
    // Real-time Canvas Update
    function updateCanvas(input) {
        if (input.files && input.files[0]) {
            var reader = new FileReader();
            reader.onload = function(e) {
                var img = document.getElementById('realPreview');
                img.src = e.target.result;
                // Fade effect
                img.style.opacity = 0.5;
                setTimeout(() => { img.style.opacity = 1; }, 200);
            }
            reader.readAsDataURL(input.files[0]);
        }
    }
</script>
@endsection