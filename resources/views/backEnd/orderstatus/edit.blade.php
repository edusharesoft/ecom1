@extends('backEnd.layouts.master')
@section('title', 'অর্ডার স্ট্যাটাস সম্পাদনা')

@section('css')
@include('backEnd.orderstatus.partials.os_styles')
@endsection

@section('content')
<div class="container-fluid order-status-shell order-status-page">

    <div class="os-page-header">
        <div>
            <h4>স্ট্যাটাস সম্পাদনা</h4>
            <p class="os-sub"><strong>{{ $edit_data->name }}</strong> — বিবরণ ও কনফিগারেশন আপডেট করুন</p>
        </div>
        <a href="{{ route('orderstatus.index') }}" class="os-btn-ghost">
            <i class="fas fa-arrow-left"></i> তালিকায় ফিরুন
        </a>
    </div>

    <form action="{{ route('orderstatus.update') }}" method="POST" data-parsley-validate>
        @csrf
        <input type="hidden" value="{{ $edit_data->id }}" name="id">

        <div class="row os-form-layout g-3">
            <div class="col-lg-8">
                <div class="os-card">
                    <div class="os-card-head">
                        <span class="os-card-icon"><i class="fas fa-pen"></i></span>
                        <h6>স্ট্যাটাস তথ্য</h6>
                    </div>
                    <div class="os-card-body">
                        <div class="mb-0">
                            <label for="name" class="os-label">স্ট্যাটাস নাম <span class="text-danger">*</span></label>
                            <input type="text" class="os-input @error('name') is-invalid @enderror"
                                   name="name" value="{{ $edit_data->name }}" id="name" required>
                            @error('name')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="os-card">
                    <div class="os-card-head">
                        <span class="os-card-icon"><i class="fas fa-cog"></i></span>
                        <h6>কনফিগারেশন</h6>
                    </div>
                    <div class="os-card-body">
                        <div class="os-toggle-box">
                            <div>
                                <h6>সক্রিয় স্ট্যাটাস</h6>
                                <p>এই স্ট্যাটাস ব্যবহারযোগ্য রাখুন</p>
                            </div>
                            <label class="os-switch">
                                <input type="checkbox" name="status" value="1" {{ $edit_data->status == 1 ? 'checked' : '' }}>
                                <span class="os-slider"></span>
                            </label>
                        </div>
                        @error('status')
                            <div class="text-danger small mb-3">{{ $message }}</div>
                        @enderror

                        <button type="submit" class="btn os-btn-save">
                            <i class="fas fa-save me-1"></i> আপডেট করুন
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection

@section('script')
<script src="{{ asset('public/backEnd/') }}/assets/libs/parsleyjs/parsley.min.js"></script>
<script src="{{ asset('public/backEnd/') }}/assets/js/pages/form-validation.init.js"></script>
@endsection
