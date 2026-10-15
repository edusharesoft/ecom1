@extends('backEnd.layouts.master')
@section('title','Edit Category')

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

    /* 2. FORM ELEMENTS */
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
</style>
@endsection

@section('content')
<div class="container-fluid py-4">
    
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold m-0 text-dark">Edit Category</h4>
            <span class="text-muted small">Modify placement category details</span>
        </div>
        <div class="d-flex gap-2">
            <a href="{{route('banner_category.index')}}" class="btn btn-light border fw-bold text-secondary px-3">
                Cancel
            </a>
            <button type="submit" form="editCategoryForm" class="btn btn-primary fw-bold px-4 shadow-sm">
                <i class="fe-save me-1"></i> Update Category
            </button>
        </div>
    </div>

    <div class="row justify-content-center">
        <div class="col-lg-6 col-md-8">
            
            <div class="studio-card p-4">
                <form action="{{route('banner_category.update')}}" method="POST" id="editCategoryForm" data-parsley-validate="">
                    @csrf
                    <input type="hidden" value="{{$edit_data->id}}" name="id">

                    <div class="mb-4">
                        <label for="name" class="form-label-custom">Category Name <span class="text-danger">*</span></label>
                        <input type="text" 
                               class="form-control input-clean @error('name') is-invalid @enderror" 
                               name="name" 
                               value="{{ $edit_data->name }}" 
                               id="name" 
                               required>
                        @error('name')
                            <div class="invalid-feedback d-block mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-2">
                        <label class="form-label-custom">Status Configuration</label>
                        <div class="status-toggle-box">
                            <div class="status-text">
                                <h6>Active Status</h6>
                                <small>Enable or disable this category</small>
                            </div>
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" name="status" value="1" @if($edit_data->status==1) checked @endif style="width: 3em; height: 1.5em; cursor:pointer;">
                            </div>
                        </div>
                        @error('status')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                </form>
            </div>

        </div>
    </div>

</div>
@endsection

@section('script')
<script src="{{asset('public/backEnd/')}}/assets/libs/parsleyjs/parsley.min.js"></script>
<script src="{{asset('public/backEnd/')}}/assets/js/pages/form-validation.init.js"></script>
@endsection