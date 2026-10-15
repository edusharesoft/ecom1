@extends('backEnd.layouts.master')
@section('title','Add Social Media')

@section('css')
<style>
    /* 1. PROFESSIONAL CARD CONTAINER */
    .studio-card {
        background: #fff;
        border-radius: 15px;
        box-shadow: 0 5px 25px rgba(0,0,0,0.05);
        border: 1px solid #e2e8f0;
        overflow: hidden;
    }

    /* 2. FORM ELEMENTS Styling */
    .form-label-custom {
        font-size: 13px;
        font-weight: 700;
        color: #475569;
        margin-bottom: 8px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .input-clean {
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        padding: 12px 15px;
        border-radius: 10px;
        font-size: 14px;
        color: #334155;
        transition: all 0.2s ease-in-out;
    }

    .input-clean:focus {
        background: #fff;
        border-color: #2563eb;
        box-shadow: 0 0 0 4px rgba(37, 99, 235, 0.1);
        outline: none;
    }

    .input-group-text {
        background-color: #f1f5f9;
        border: 1px solid #e2e8f0;
        border-radius: 10px 0 0 10px;
        color: #64748b;
        min-width: 45px;
        justify-content: center;
    }

    .input-group .input-clean {
        border-radius: 0 10px 10px 0;
    }

    /* 3. STATUS TOGGLE BOX */
    .status-toggle-box {
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        border-radius: 12px;
        padding: 18px;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .status-text h6 { font-size: 14px; font-weight: 700; color: #1e293b; margin: 0; }
    .status-text small { font-size: 12px; color: #64748b; }

    /* 4. COLOR PICKER */
    .color-input-wrapper {
        height: 48px;
        padding: 5px;
        cursor: pointer;
    }
</style>
@endsection

@section('content')
<div class="container-fluid py-4">
    
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold m-0 text-dark">Add New Platform</h4>
            <span class="text-muted small">Connect a new social media profile</span>
        </div>
        <div class="d-flex gap-2">
            <a href="{{route('socialmedias.index')}}" class="btn btn-light border fw-bold text-secondary px-3 rounded-pill">
                Cancel
            </a>
            <button type="submit" form="socialCreateForm" class="btn btn-primary fw-bold px-4 shadow-sm rounded-pill">
                <i class="mdi mdi-content-save-outline me-1"></i> Save Platform
            </button>
        </div>
    </div>

    <div class="row justify-content-center">
        <div class="col-lg-9 col-md-11">
            
            <div class="studio-card p-4 p-md-5">
                <form action="{{route('socialmedias.store')}}" method="POST" id="socialCreateForm" data-parsley-validate="" enctype="multipart/form-data">
                    @csrf

                    <div class="row g-4">
                        
                        <div class="col-md-6">
                            <label class="form-label-custom">Platform Name *</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="mdi mdi-label-outline"></i></span>
                                <input type="text" class="form-control input-clean @error('title') is-invalid @enderror" 
                                       name="title" value="{{ old('title') }}" 
                                       placeholder="e.g. Facebook" required>
                            </div>
                            @error('title') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label-custom">Icon Class *</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="mdi mdi-emoticon-happy-outline"></i></span>
                                <input type="text" class="form-control input-clean @error('icon') is-invalid @enderror" 
                                       name="icon" value="{{ old('icon') }}" 
                                       placeholder="e.g. fab fa-facebook" required>
                            </div>
                            <small class="text-muted">Example: <b>fab fa-facebook-f</b> or <b>fe-facebook</b></small>
                            @error('icon') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label-custom">Profile URL / Link *</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="mdi mdi-link-variant"></i></span>
                                <input type="text" class="form-control input-clean @error('link') is-invalid @enderror" 
                                       name="link" value="{{ old('link') }}" 
                                       placeholder="https://facebook.com/yourpage" required>
                            </div>
                            @error('link') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label-custom">Brand Theme Color *</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="mdi mdi-palette-outline"></i></span>
                                <input type="color" class="form-control input-clean color-input-wrapper @error('color') is-invalid @enderror" 
                                       name="color" value="{{ old('color', '#2563eb') }}" required>
                            </div>
                            @error('color') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                        </div>

                        <div class="col-12">
                            <div class="status-toggle-box">
                                <div class="status-text">
                                    <h6 class="d-flex align-items-center"><i class="mdi mdi-eye-outline me-2 text-primary"></i> Publication Status</h6>
                                    <small>Enable this link to be visible on the website frontend</small>
                                </div>
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" name="status" value="1" checked 
                                           style="width: 3.5em; height: 1.8em; cursor:pointer;">
                                </div>
                            </div>
                            @error('status') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                        </div>

                    </div> </form>
            </div>

        </div>
    </div>

</div>
@endsection