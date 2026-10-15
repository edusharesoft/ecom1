@extends('backEnd.layouts.master')
@section('title','Add TikTok Pixel')

@section('css')
<style>
    .card { border: none; box-shadow: 0 0 20px rgba(18,38,63,0.03); border-radius: 12px; overflow: hidden; }
    .card-header { background: #fff; border-bottom: 1px solid #f1f5f7; padding: 20px 25px; display: flex; align-items: center; gap: 10px; }
    .card-title { font-size: 16px; font-weight: 700; color: #2d3436; margin: 0; }
    .header-icon { width: 35px; height: 35px; background: #010101; border-radius: 8px; display: flex; align-items: center; justify-content: center; }
    .form-label { font-weight: 600; font-size: 13px; color: #636e72; margin-bottom: 8px; }
    .form-control { background-color: #fbfcff; border: 1px solid #eef2f7; padding: 12px 15px; border-radius: 8px; font-size: 14px; color: #2d3436; transition: all 0.3s; }
    .form-control:focus { background-color: #fff; border-color: #010101; box-shadow: 0 0 0 4px rgba(1,1,1,0.08); }
    .switch { position: relative; display: inline-block; width: 46px; height: 24px; }
    .switch input { opacity: 0; width: 0; height: 0; }
    .slider { position: absolute; cursor: pointer; top: 0; left: 0; right: 0; bottom: 0; background-color: #eef2f7; transition: .4s; border-radius: 34px; border: 1px solid #dee2e6; }
    .slider:before { position: absolute; content: ""; height: 18px; width: 18px; left: 2px; bottom: 2px; background-color: white; transition: .4s; border-radius: 50%; box-shadow: 0 2px 4px rgba(0,0,0,0.1); }
    input:checked + .slider { background-color: #0acf97; border-color: #0acf97; }
    input:checked + .slider:before { transform: translateX(22px); }
    .btn-submit { background: linear-gradient(45deg, #010101, #1a1a2e); border: none; color: white; padding: 12px 25px; font-weight: 600; border-radius: 50rem; transition: 0.3s; }
    .btn-submit:hover { transform: translateY(-2px); box-shadow: 0 6px 20px rgba(0,0,0,0.3); }
    .help-box { background: #f8f9fa; border-left: 3px solid #010101; padding: 12px 15px; border-radius: 0 8px 8px 0; margin-top: 15px; }
</style>
@endsection

@section('content')
<div class="container-fluid">
    <div class="row mb-3 mt-3">
        <div class="col-12 d-flex justify-content-between align-items-center">
            <div>
                <h4 class="page-title mb-0" style="font-weight: 700; color: #2d3436;">Add TikTok Pixel</h4>
                <p class="text-muted font-size-13 mb-0">Add your TikTok tracking pixel ID.</p>
            </div>
            <a href="{{route('tiktok.pixels.index')}}" class="btn btn-light rounded-pill border shadow-sm px-4">
                <i class="fe-arrow-left me-1"></i> Back to List
            </a>
        </div>
    </div>

    <div class="row justify-content-center">
        <div class="col-lg-6">
            <div class="card">
                <div class="card-header">
                    <div class="header-icon">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="white"><path d="M19.59 6.69a4.83 4.83 0 01-3.77-4.25V2h-3.45v13.67a2.89 2.89 0 01-2.88 2.5 2.89 2.89 0 01-2.89-2.89 2.89 2.89 0 012.89-2.89c.28 0 .54.04.79.1V9.01a6.33 6.33 0 00-.79-.05 6.34 6.34 0 00-6.34 6.34 6.34 6.34 0 006.34 6.34 6.34 6.34 0 006.33-6.34V9.05a8.16 8.16 0 004.77 1.52V7.13a4.85 4.85 0 01-1-.44z"/></svg>
                    </div>
                    <h5 class="card-title">TikTok Pixel Configuration</h5>
                </div>
                <div class="card-body">
                    <form action="{{route('tiktok.pixels.store')}}" method="POST" data-parsley-validate>
                        @csrf

                        <div class="form-group mb-4">
                            <label for="code" class="form-label">TikTok Pixel ID <span class="text-danger">*</span></label>
                            <input type="text" class="form-control form-control-lg @error('code') is-invalid @enderror"
                                   name="code" value="{{ old('code') }}" id="code"
                                   placeholder="e.g. C3ABC123DEF456789" required>
                            @error('code')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="help-box mt-2">
                                <small class="text-muted">
                                    <strong>কোথায় পাবেন?</strong> TikTok Ads Manager → Assets → Events → Web Events → Pixel ID
                                </small>
                            </div>
                        </div>

                        <div class="form-group mb-4">
                            <div class="d-flex justify-content-between align-items-center border p-3 rounded bg-light">
                                <div>
                                    <label class="form-label mb-0 text-dark">Active Status</label>
                                    <small class="d-block text-muted">Enable tracking immediately?</small>
                                </div>
                                <label class="switch">
                                    <input type="checkbox" name="status" value="1" checked>
                                    <span class="slider round"></span>
                                </label>
                            </div>
                        </div>

                        <div class="text-end">
                            <button type="submit" class="btn btn-submit">
                                <i class="fe-check-circle me-1"></i> Save Pixel
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('script')
<script src="{{asset('public/backEnd/')}}/assets/libs/parsleyjs/parsley.min.js"></script>
<script src="{{asset('public/backEnd/')}}/assets/js/pages/form-validation.init.js"></script>
@endsection
