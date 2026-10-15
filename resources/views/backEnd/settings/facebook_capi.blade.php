@extends('backEnd.layouts.master')
@section('title','Facebook Conversion API Settings')

@section('css')
<style>
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
        background: rgba(10, 207, 151, 0.08);
        color: #0acf97;
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 18px;
    }
    .form-label {
        font-weight: 600;
        font-size: 13px;
        color: #636e72;
        margin-bottom: 6px;
    }
    .form-control {
        background-color: #fbfcff;
        border: 1px solid #eef2f7;
        padding: 11px 14px;
        border-radius: 8px;
        font-size: 14px;
    }
    .form-control:focus {
        background-color: #fff;
        border-color: #0acf97;
        box-shadow: 0 0 0 3px rgba(10, 207, 151, 0.15);
    }
    .small-help {
        font-size: 12px;
        color: #95a5a6;
    }
    .btn-submit {
        background: linear-gradient(45deg, #0acf97, #06b6d4);
        border: none;
        color: white;
        padding: 10px 24px;
        font-weight: 600;
        letter-spacing: .4px;
        border-radius: 40px;
        box-shadow: 0 4px 14px rgba(10, 207, 151, 0.35);
    }
    .btn-submit:hover {
        transform: translateY(-1px);
        box-shadow: 0 6px 18px rgba(10, 207, 151, 0.45);
    }
    .form-switch .form-check-input {
        width: 3rem;
        height: 1.5rem;
    }
</style>
@endsection

@section('content')
<div class="container-fluid">
    <div class="row mb-3 mt-3">
        <div class="col-12 d-flex justify-content-between align-items-center">
            <div>
                <h4 class="page-title mb-0" style="font-weight: 700; color: #2d3436;">
                    Facebook Conversion API Settings
                </h4>
                <p class="text-muted font-size-13 mb-0">
                    এখানে Facebook CAPI এর Pixel ID এবং Access Token সংরক্ষণ করবেন।
                </p>
            </div>
        </div>
    </div>

    <div class="row justify-content-center">
        <div class="col-lg-7 col-xl-6">
            <div class="card">
                <div class="card-header">
                    <div class="header-icon">
                        <i class="fe-share-2"></i>
                    </div>
                    <h5 class="card-title mb-0">Credentials Configuration</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.facebook_capi.update') }}" method="POST">
                        @csrf

                        <div class="mb-3">
                            <label class="form-label" for="pixel_id">
                                Facebook Pixel ID <span class="text-danger">*</span>
                            </label>
                            <input
                                type="text"
                                class="form-control @error('pixel_id') is-invalid @enderror"
                                id="pixel_id"
                                name="pixel_id"
                                value="{{ old('pixel_id', $setting->pixel_id ?? '') }}"
                                placeholder="e.g. 123456789012345"
                                required
                            >
                            <small class="small-help">
                                Facebook Events Manager থেকে Pixel ID কপি করে এখানে পেস্ট করুন।
                            </small>
                            @error('pixel_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label" for="access_token">
                                Long-lived Access Token <span class="text-danger">*</span>
                            </label>
                            <textarea
                                class="form-control @error('access_token') is-invalid @enderror"
                                id="access_token"
                                name="access_token"
                                rows="3"
                                placeholder="Paste your long-lived access token here"
                                required
                            >{{ old('access_token', $setting->access_token ?? '') }}</textarea>
                            <small class="small-help">
                                Facebook Developer Tools থেকে generated CAPI এর long-lived access token এখানে রাখবেন।
                            </small>
                            @error('access_token')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label" for="test_event_code">
                                Test Event Code (optional)
                            </label>
                            <input
                                type="text"
                                class="form-control @error('test_event_code') is-invalid @enderror"
                                id="test_event_code"
                                name="test_event_code"
                                value="{{ old('test_event_code', $setting->test_event_code ?? '') }}"
                                placeholder="e.g. TEST1234"
                            >
                            <small class="small-help">
                                Events Manager &gt; Test Events থেকে পাওয়া Test Event Code (যদি ব্যবহার করেন)।
                            </small>
                            @error('test_event_code')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-4 form-check form-switch">
                            <input
                                class="form-check-input"
                                type="checkbox"
                                id="status"
                                name="status"
                                value="1"
                                {{ old('status', $setting->status ?? 1) ? 'checked' : '' }}
                            >
                            <label class="form-check-label" for="status">
                                Facebook CAPI Active রাখুন
                            </label>
                        </div>

                        <div class="text-end">
                            <button type="submit" class="btn btn-submit">
                                <i class="fe-save me-1"></i> Save Settings
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

