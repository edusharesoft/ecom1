@extends('backEnd.layouts.master')
@section('title','Edit Permission')

@section('css')
<link href="{{asset('public/backEnd')}}/assets/libs/select2/css/select2.min.css" rel="stylesheet" type="text/css" />

<style>
    /* Premium Card */
    .card {
        border: none;
        box-shadow: 0 0 20px rgba(18, 38, 63, 0.03);
        border-radius: 12px;
        overflow: hidden;
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

    /* Button Style */
    .btn-submit {
        background: linear-gradient(45deg, #0acf97, #06b6d4);
        border: none;
        color: white;
        padding: 12px 25px;
        font-weight: 600;
        letter-spacing: 0.5px;
        box-shadow: 0 4px 15px rgba(10, 207, 151, 0.3);
        transition: 0.3s;
        border-radius: 50rem;
    }
    .btn-submit:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(10, 207, 151, 0.4);
    }
</style>
@endsection

@section('content')
<div class="container-fluid">
    
    <div class="row mb-3 mt-3">
        <div class="col-12 d-flex justify-content-between align-items-center">
            <div>
                <h4 class="page-title mb-0" style="font-weight: 700; color: #2d3436;">Edit Permission</h4>
                <p class="text-muted font-size-13 mb-0">Update system access control definition.</p>
            </div>
            <a href="{{route('permissions.index')}}" class="btn btn-light rounded-pill border shadow-sm px-4">
                <i class="fe-arrow-left me-1"></i> Back to List
            </a>
        </div>
    </div>

    <div class="row justify-content-center">
        <div class="col-lg-6">
            <div class="card">
                <div class="card-header">
                    <div class="header-icon"><i class="fe-edit"></i></div>
                    <h5 class="card-title">Update Permission</h5>
                </div>
                <div class="card-body">
                    <form action="{{route('permissions.update')}}" method="POST" data-parsley-validate>
                        @csrf
                        <input type="hidden" name="hidden_id" value="{{$edit_data->id}}">
                        
                        <div class="form-group mb-4">
                            <label for="name" class="form-label">Permission Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control form-control-lg @error('name') is-invalid @enderror" 
                                   name="name" value="{{ $edit_data->name }}" id="name" required>
                            
                            <small class="text-muted d-block mt-2">
                                <i class="fe-info"></i> Make sure the permission name follows your naming convention (e.g., <code>blog-edit</code>).
                            </small>

                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="text-end">
                            <button type="submit" class="btn btn-submit">
                                <i class="fe-check-circle me-1"></i> Update Permission
                            </button>
                        </div>

                    </form>
                </div> </div> </div> </div>
</div>
@endsection

@section('script')
<script src="{{asset('public/backEnd/')}}/assets/libs/parsleyjs/parsley.min.js"></script>
<script src="{{asset('public/backEnd/')}}/assets/js/pages/form-validation.init.js"></script>
<script src="{{asset('public/backEnd/')}}/assets/libs/select2/js/select2.min.js"></script>
<script src="{{asset('public/backEnd/')}}/assets/js/pages/form-advanced.init.js"></script>
@endsection