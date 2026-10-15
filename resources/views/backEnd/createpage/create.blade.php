@extends('backEnd.layouts.master')
@section('title','Create Page')

@section('css')
<link href="{{asset('public/backEnd')}}/assets/libs/summernote/summernote-lite.min.css" rel="stylesheet" type="text/css" />
<style>
    /* 1. PROFESSIONAL CARD CONTAINER */
    .studio-card {
        background: #fff;
        border-radius: 12px;
        box-shadow: 0 5px 20px rgba(0,0,0,0.05);
        border: 1px solid #e2e8f0;
        overflow: hidden;
    }

    /* 2. FORM ELEMENTS Styling */
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
    
    .form-label-custom {
        font-size: 11px;
        text-transform: uppercase;
        color: #64748b;
        letter-spacing: 0.8px;
        font-weight: 700;
        margin-bottom: 8px;
        display: block;
    }

    /* 3. STATUS TOGGLE AREA */
    .status-toggle-box {
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        border-radius: 8px;
        padding: 15px;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    .status-text h6 {
        font-size: 14px;
        font-weight: 700;
        color: #334155;
        margin: 0;
    }
    .status-text small {
        font-size: 12px;
        color: #94a3b8;
    }

    /* Summernote Custom Styling */
    .note-editor.note-frame {
        border: 1px solid #e2e8f0 !important;
        border-radius: 8px !important;
        background: #f8fafc !important;
    }
</style>
@endsection

@section('content')
<div class="container-fluid py-4">
    
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold m-0 text-dark">Create New Page</h4>
            <span class="text-muted small">Publish dynamic content and legal pages</span>
        </div>
        <div class="d-flex gap-2">
            <a href="{{route('pages.index')}}" class="btn btn-light border fw-bold text-secondary px-3">
                Cancel
            </a>
            <button type="submit" form="pageCreateForm" class="btn btn-primary fw-bold px-4 shadow-sm">
                <i class="fe-plus me-1"></i> Create Page
            </button>
        </div>
    </div>

    <div class="row justify-content-center">
        <div class="col-lg-10 col-md-12">
            
            <div class="studio-card p-4">
                <form action="{{route('pages.store')}}" method="POST" id="pageCreateForm" data-parsley-validate="" enctype="multipart/form-data">
                    @csrf

                    <div class="row g-4">
                        
                        <div class="col-md-6">
                            <label class="form-label-custom">Page Name <span class="text-danger">*</span></label>
                            <input type="text" 
                                   class="form-control input-clean @error('name') is-invalid @enderror" 
                                   name="name" 
                                   value="{{ old('name') }}" 
                                   id="name" 
                                   placeholder="e.g. Terms & Conditions" 
                                   required>
                            @error('name')
                                <div class="invalid-feedback d-block mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label-custom">Page Title <span class="text-danger">*</span></label>
                            <input type="text" 
                                   class="form-control input-clean @error('title') is-invalid @enderror" 
                                   name="title" 
                                   value="{{ old('title') }}" 
                                   id="title" 
                                   placeholder="e.g. Terms and Conditions - Our Store" 
                                   required>
                            @error('title')
                                <div class="invalid-feedback d-block mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-12">
                            <label class="form-label-custom">Page Content / Description <span class="text-danger">*</span></label>
                            <textarea class="summernote form-control @error('description') is-invalid @enderror" 
                                      name="description" 
                                      id="description" 
                                      required>{{ old('description') }}</textarea>
                            @error('description')
                                <div class="invalid-feedback d-block mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label-custom">Publication Status</label>
                            <div class="status-toggle-box">
                                <div class="status-text">
                                    <h6>Active for Visitors</h6>
                                    <small>Visible on website footer/menu</small>
                                </div>
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" name="status" value="1" checked style="width: 3em; height: 1.5em; cursor:pointer;">
                                </div>
                            </div>
                            @error('status')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                    </div> </form>
            </div>

        </div>
    </div>

</div>
@endsection

@section('script')
<script src="{{asset('public/backEnd/')}}/assets/libs/parsleyjs/parsley.min.js"></script>
<script src="{{asset('public/backEnd/')}}/assets/js/pages/form-validation.init.js"></script>
<script src="{{asset('public/backEnd/')}}/assets/js/pages/form-advanced.init.js"></script>
<script src="{{asset('public/backEnd/')}}/assets/libs/summernote/summernote-lite.min.js"></script>
<script>
    $(document).ready(function() {
        $(".summernote").summernote({
            placeholder: "Start typing your page content here...",
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
</script>
@endsection