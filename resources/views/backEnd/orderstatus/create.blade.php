@extends('backEnd.layouts.master')
@section('title', 'নতুন অর্ডার স্ট্যাটাস')

@section('css')
@include('backEnd.orderstatus.partials.os_styles')
@endsection

@section('content')
<div class="container-fluid order-status-shell order-status-page">

    <div class="os-page-header">
        <div>
            <h4>নতুন অর্ডার স্ট্যাটাস</h4>
            <p class="os-sub">অর্ডার ট্র্যাকিংয়ের জন্য নতুন স্ট্যাটাস যোগ করুন</p>
        </div>
        <a href="{{ route('orderstatus.index') }}" class="os-btn-ghost">
            <i class="fas fa-arrow-left"></i> তালিকায় ফিরুন
        </a>
    </div>

    <form action="{{ route('orderstatus.store') }}" method="POST" data-parsley-validate>
        @csrf
        <div class="row os-form-layout g-3">
            <div class="col-lg-8">
                <div class="os-card">
                    <div class="os-card-head">
                        <span class="os-card-icon"><i class="fas fa-flag"></i></span>
                        <h6>স্ট্যাটাস বিবরণ</h6>
                    </div>
                    <div class="os-card-body">
                        <div class="mb-0">
                            <label for="name" class="os-label">স্ট্যাটাস নাম <span class="text-danger">*</span></label>
                            <input type="text" class="os-input @error('name') is-invalid @enderror"
                                   name="name" value="{{ old('name') }}" id="name"
                                   placeholder="যেমন: পেন্ডিং, প্রসেসিং, ডেলিভার্ড" required>
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
                                <input type="checkbox" name="status" value="1" checked>
                                <span class="os-slider"></span>
                            </label>
                        </div>
                        @error('status')
                            <div class="text-danger small mb-3">{{ $message }}</div>
                        @enderror

                        <button type="submit" class="btn os-btn-save">
                            <i class="fas fa-check me-1"></i> সংরক্ষণ করুন
                        </button>
                    </div>
                </div>

                <div class="os-tip">
                    <i class="fas fa-info-circle"></i>
                    <span>স্পষ্ট নাম ব্যবহার করুন — গ্রাহক ও অ্যাডমিন উভয়ই অর্ডার অগ্রগতি বুঝতে পারবে।</span>
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
