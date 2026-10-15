@extends('backEnd.layouts.master')
@section('title', 'Courier API Settings')

@section('css')
<link href="{{ asset('public/backEnd/assets/libs/select2/css/select2.min.css') }}" rel="stylesheet" type="text/css" />
<style>
    @import url('https://fonts.googleapis.com/css2?family=DM+Sans:ital,opsz,wght@0,9..40,400;0,9..40,600;0,9..40,700&display=swap');

    .courier-hub {
        --ch-surface: #ffffff;
        --ch-border: #e8ecf1;
        --ch-muted: #64748b;
        --ch-text: #0f172a;
        --ch-radius: 14px;
        --ch-shadow: 0 1px 2px rgba(15, 23, 42, 0.04), 0 10px 28px rgba(15, 23, 42, 0.06);
        font-family: 'DM Sans', system-ui, sans-serif;
        color: var(--ch-text);
        letter-spacing: -0.01em;
    }

    .courier-hub-hero {
        background: var(--ch-surface);
        border: 1px solid var(--ch-border);
        border-radius: var(--ch-radius);
        box-shadow: var(--ch-shadow);
        padding: 1.35rem 1.5rem;
        margin-bottom: 1.5rem;
        display: flex;
        flex-wrap: wrap;
        align-items: flex-start;
        justify-content: space-between;
        gap: 1rem;
        position: relative;
        overflow: hidden;
    }
    .courier-hub-hero::before {
        content: '';
        position: absolute;
        left: 0;
        top: 0;
        bottom: 0;
        width: 4px;
        background: linear-gradient(180deg, #6366f1, #8b5cf6);
        border-radius: 4px 0 0 4px;
    }
    .courier-hub-hero h1 {
        font-size: 1.35rem;
        font-weight: 700;
        margin: 0 0 0.35rem;
    }
    .courier-hub-hero p {
        margin: 0;
        font-size: 0.875rem;
        color: var(--ch-muted);
        max-width: 560px;
        line-height: 1.55;
    }
    .courier-hub-meta {
        display: flex;
        flex-wrap: wrap;
        gap: 0.5rem;
        align-items: center;
    }
    .courier-hub-pill {
        font-size: 0.72rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.06em;
        padding: 0.45rem 0.75rem;
        border-radius: 999px;
        background: #eef2ff;
        color: #4338ca;
        border: 1px solid #c7d2fe;
    }
    .courier-hub-pill.soft {
        background: #f8fafc;
        color: var(--ch-muted);
        border-color: var(--ch-border);
    }

    .courier-panel {
        background: var(--ch-surface);
        border: 1px solid var(--ch-border);
        border-radius: var(--ch-radius);
        box-shadow: var(--ch-shadow);
        height: 100%;
        overflow: hidden;
        transition: box-shadow 0.22s ease, border-color 0.22s ease;
    }
    .courier-panel:hover {
        box-shadow: 0 4px 14px rgba(15, 23, 42, 0.08);
        border-color: #dce3ec;
    }

    .courier-panel-head {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 1rem;
        padding: 1.15rem 1.25rem;
        border-bottom: 1px solid var(--ch-border);
    }
    .courier-panel-head.steadfast {
        background: linear-gradient(135deg, #fff5f5 0%, #fff 55%);
        border-left: 4px solid #ef4444;
    }
    .courier-panel-head.pathao {
        background: linear-gradient(135deg, #eff6ff 0%, #fff 55%);
        border-left: 4px solid #0ea5e9;
    }
    .courier-panel-head.redx {
        background: linear-gradient(135deg, #fffbeb 0%, #fff 55%);
        border-left: 4px solid #f59e0b;
    }

    .courier-panel-title {
        margin: 0;
        font-size: 1.05rem;
        font-weight: 700;
        color: var(--ch-text);
    }
    .courier-panel-tag {
        display: block;
        font-size: 0.75rem;
        color: var(--ch-muted);
        margin-top: 0.15rem;
        font-weight: 500;
    }

    .courier-logo {
        width: 56px;
        height: 56px;
        border-radius: 12px;
        background: #fff;
        border: 1px solid var(--ch-border);
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 6px;
        flex-shrink: 0;
        box-shadow: 0 2px 8px rgba(15, 23, 42, 0.06);
    }
    .courier-logo img {
        width: 100%;
        height: 100%;
        object-fit: contain;
    }

    .courier-panel-body {
        padding: 1.25rem;
    }

    .courier-hub .form-label {
        font-size: 0.65rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.07em;
        color: var(--ch-muted);
        margin-bottom: 0.45rem;
    }

    .courier-hub .form-control {
        border: 1px solid var(--ch-border);
        border-radius: 10px;
        padding: 0.65rem 0.85rem;
        font-size: 0.875rem;
        background: #fafbfc;
        transition: border-color 0.15s, box-shadow 0.15s, background 0.15s;
    }
    .courier-hub .form-control:focus {
        border-color: #6366f1;
        background: #fff;
        box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.12);
    }

    .courier-status-row {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 0.75rem;
        padding: 0.85rem 1rem;
        border-radius: 10px;
        border: 1px solid var(--ch-border);
        background: linear-gradient(180deg, #fafbfd 0%, #fff 100%);
        margin-bottom: 1rem;
    }
    .courier-status-row span {
        font-size: 0.8125rem;
        font-weight: 700;
        color: var(--ch-text);
    }
    .courier-hub .form-check-input {
        width: 2.65rem;
        height: 1.35rem;
        cursor: pointer;
    }

    .courier-btn-save {
        width: 100%;
        border: none;
        border-radius: 10px;
        padding: 0.72rem 1rem;
        font-size: 0.8125rem;
        font-weight: 700;
        letter-spacing: 0.02em;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 0.5rem;
        transition: transform 0.15s, box-shadow 0.15s;
    }
    .courier-btn-save:hover {
        transform: translateY(-1px);
    }
    .courier-btn-save.steadfast {
        background: linear-gradient(145deg, #ef4444, #dc2626);
        color: #fff;
        box-shadow: 0 6px 18px rgba(239, 68, 68, 0.35);
    }
    .courier-btn-save.pathao {
        background: linear-gradient(145deg, #0ea5e9, #0284c7);
        color: #fff;
        box-shadow: 0 6px 18px rgba(14, 165, 233, 0.35);
    }
    .courier-btn-save.redx {
        background: linear-gradient(145deg, #f59e0b, #d97706);
        color: #fff;
        box-shadow: 0 6px 18px rgba(245, 158, 11, 0.35);
    }

    .courier-hub small.form-text,
    .courier-hub .text-muted.small-hint {
        font-size: 0.72rem;
        line-height: 1.45;
    }

    .courier-hub .input-group .btn {
        border-radius: 0 10px 10px 0;
        border-color: var(--ch-border);
    }

    .courier-hub code {
        font-size: 0.72rem;
        padding: 0.15rem 0.35rem;
        border-radius: 4px;
        background: #f1f5f9;
        color: #0f172a;
    }

    .courier-hub-security-note {
        font-size: 0.75rem;
        color: var(--ch-muted);
        padding: 0.65rem 0.85rem;
        border-radius: 10px;
        background: #fffbeb;
        border: 1px solid #fde68a;
        margin-bottom: 1.25rem;
    }
    .courier-hub-security-note i {
        color: #d97706;
        margin-right: 0.35rem;
    }
</style>
@endsection

@section('content')
<div class="courier-hub">
    <div class="container-fluid py-4">

        <header class="courier-hub-hero">
            <div>
                <h1>Courier API integration</h1>
                <p>
                    স্টেডফাস্ট, পাঠাও ও রেডএক্সের কী ও টোকেন এখানে সংরক্ষণ হয়। চালু থাকা গেটওয়ে অর্ডার ফ্লোতে ব্যবহারযোগ্য হবে —
                    সংবেদনশীল ডাটা শুধু প্রয়োজনীয় ফিল্ডে রাখুন।
                </p>
            </div>
            <div class="courier-hub-meta">
                <span class="courier-hub-pill"><i class="mdi mdi-api"></i> ৩টি গেটওয়ে</span>
                <span class="courier-hub-pill soft"><i class="mdi mdi-shield-lock-outline"></i> HTTPS রিকমেন্ডেড</span>
            </div>
        </header>

        <div class="courier-hub-security-note">
            <i class="mdi mdi-information-outline"></i>
            কী/টোকেন কখনও পাবলিক রিপোজিটরিতে দিবেন না। প্রোডাকশন ও স্যান্ডবক্স আলাদা ক্রেডেনশিয়াল ব্যবহার করুন।
        </div>

        <div class="row g-4">

            {{-- Steadfast --}}
            <div class="col-lg-4 col-md-12">
                <div class="courier-panel">
                    <div class="courier-panel-head steadfast">
                        <div>
                            <h2 class="courier-panel-title">Steadfast</h2>
                            <span class="courier-panel-tag">API · ওয়েবহুক</span>
                        </div>
                        <div class="courier-logo">
                            <img src="{{ asset('public/frontEnd/images/stade.svg') }}" alt="Steadfast">
                        </div>
                    </div>
                    <div class="courier-panel-body">
                        <form action="{{ route('courierapi.update') }}" method="POST" data-parsley-validate>
                            @csrf
                            <input type="hidden" name="id" value="{{ $steadfast->id }}">

                            <div class="mb-3">
                                <label class="form-label">API Key <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('api_key') is-invalid @enderror"
                                       name="api_key" value="{{ $steadfast->api_key }}" required autocomplete="off" />
                                @error('api_key')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Secret Key <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('secret_key') is-invalid @enderror"
                                       name="secret_key" value="{{ $steadfast->secret_key }}" required autocomplete="off" />
                                @error('secret_key')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            @include('backEnd.apiintegration.partials.steadfast_webhook_fields')

                            <div class="courier-status-row">
                                <span><i class="fas fa-power-off text-muted me-1"></i> সার্ভিস চালু</span>
                                <div class="form-check form-switch m-0">
                                    <input class="form-check-input" type="checkbox" name="status" value="1"
                                           @if($steadfast->status==1) checked @endif>
                                </div>
                            </div>

                            <button type="submit" class="courier-btn-save steadfast">
                                <i class="fas fa-save"></i> সংরক্ষণ করুন
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            {{-- Pathao --}}
            <div class="col-lg-4 col-md-12">
                <div class="courier-panel">
                    <div class="courier-panel-head pathao">
                        <div>
                            <h2 class="courier-panel-title">Pathao Courier</h2>
                            <span class="courier-panel-tag">Hermes API · টোকেন জেনারেট</span>
                        </div>
                        <div class="courier-logo">
                            <img src="https://merchant.pathao.com/assets/logo_pathao_courier.a3ef9b7c.svg" alt="Pathao">
                        </div>
                    </div>
                    <div class="courier-panel-body">
                        <form action="{{ route('courierapi.update') }}" method="POST" data-parsley-validate id="pathao_form">
                            @csrf
                            <input type="hidden" name="id" value="{{ $pathao->id ?? '' }}">
                            <input type="hidden" name="type" value="pathao">

                            <div class="mb-3">
                                <label class="form-label">API URL <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="url"
                                       value="{{ $pathao->url ?? 'https://api-hermes.pathao.com' }}"
                                       placeholder="https://api-hermes.pathao.com" required />
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Client ID <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('client_id') is-invalid @enderror"
                                       name="client_id" value="{{ $pathao->client_id ?? '' }}"
                                       placeholder="Pathao Client ID" required autocomplete="off" />
                                @error('client_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Client Secret <span class="text-danger">*</span></label>
                                <input type="password" class="form-control @error('client_secret') is-invalid @enderror"
                                       name="client_secret" value="{{ $pathao->client_secret ?? '' }}"
                                       placeholder="••••••••" required autocomplete="new-password" />
                                @error('client_secret')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Username / Email <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('username') is-invalid @enderror"
                                       name="username" value="{{ $pathao->username ?? '' }}"
                                       placeholder="test@pathao.com (sandbox)" required autocomplete="username" />
                                <small class="text-muted small-hint d-block mt-1">
                                    Sandbox: <code>test@pathao.com</code> · Production: আপনার পাঠাও অ্যাকাউন্ট ইমেইল
                                </small>
                                @error('username')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Password <span class="text-danger">*</span></label>
                                <input type="password" class="form-control @error('password') is-invalid @enderror"
                                       name="password" value="{{ $pathao->password ?? '' }}"
                                       placeholder="••••••••" required autocomplete="new-password" />
                                <small class="text-muted small-hint d-block mt-1">
                                    Sandbox: <code>lovePathao</code> · Production: আপনার অ্যাকাউন্ট পাসওয়ার্ড
                                </small>
                                @error('password')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Access Token</label>
                                <div class="input-group">
                                    <input type="text" class="form-control"
                                           value="{{ $pathao->token ?? '' }}"
                                           id="pathao_token_display" readonly
                                           placeholder="সংরক্ষণ বা জেনারেট করলে দেখাবে" />
                                    <button type="button" class="btn btn-outline-secondary" id="generate_pathao_token">
                                        <i class="fe-refresh-cw"></i> জেনারেট
                                    </button>
                                </div>
                                <small class="text-muted small-hint d-block mt-1">Client ID ও সিক্রেট সেভের পর অথবা একইভাবে টোকেন তৈরি করা যায়।</small>
                            </div>

                            <div class="courier-status-row">
                                <span><i class="fas fa-power-off text-muted me-1"></i> সার্ভিস চালু</span>
                                <div class="form-check form-switch m-0">
                                    <input class="form-check-input" type="checkbox" name="status" value="1"
                                           @if(isset($pathao) && $pathao->status==1) checked @endif>
                                </div>
                            </div>

                            <button type="submit" class="courier-btn-save pathao">
                                <i class="fe-save"></i> সংরক্ষণ করুন
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            {{-- RedX --}}
            <div class="col-lg-4 col-md-12">
                <div class="courier-panel">
                    <div class="courier-panel-head redx">
                        <div>
                            <h2 class="courier-panel-title">RedX Courier</h2>
                            <span class="courier-panel-tag">OpenAPI · ওয়েবহুক</span>
                        </div>
                        <div class="courier-logo">
                            <img src="https://redx.com.bd/images/logo.png" alt="RedX"
                                 onerror="this.src='data:image/svg+xml,%3Csvg xmlns=%22http://www.w3.org/2000/svg%22 viewBox=%220 0 100 100%22%3E%3Ctext x=%2250%22 y=%2255%22 font-size=%2236%22 text-anchor=%22middle%22 fill=%22%23f59e0b%22%3ERX%3C/text%3E%3C/svg%3E'">
                        </div>
                    </div>
                    <div class="courier-panel-body">
                        <form action="{{ route('courierapi.update') }}" method="POST" data-parsley-validate>
                            @csrf
                            <input type="hidden" name="id" value="{{ $redx->id ?? '' }}">
                            <input type="hidden" name="type" value="redx">

                            <div class="mb-3">
                                <label class="form-label">Base URL <span class="text-danger">*</span></label>
                                @php
                                    $currentUrl = $redx->url ?? '';
                                    $currentUrlNormalized = preg_replace('/^https?:\/\//', '', $currentUrl);
                                    $currentUrlNormalized = rtrim($currentUrlNormalized, '/');
                                @endphp
                                <select class="form-control" name="url" id="redx_url" required>
                                    <option value="sandbox.redx.com.bd/v1.0.0-beta" {{ $currentUrlNormalized == 'sandbox.redx.com.bd/v1.0.0-beta' || strpos($currentUrlNormalized, 'sandbox.redx.com.bd') !== false ? 'selected' : '' }}>Sandbox (টেস্টিং)</option>
                                    <option value="openapi.redx.com.bd/v1.0.0-beta" {{ $currentUrlNormalized == 'openapi.redx.com.bd/v1.0.0-beta' || strpos($currentUrlNormalized, 'openapi.redx.com.bd') !== false ? 'selected' : '' }}>Production (লাইভ)</option>
                                </select>
                                <small class="text-muted small-hint d-block mt-1">ইনভায়রনমেন্ট ও টোকেন একই হতে হবে।</small>
                                @if(!empty($currentUrl))
                                    <small class="text-info d-block mt-1"><i class="fe-info"></i> বর্তমান: {{ $currentUrl }}</small>
                                @endif
                            </div>

                            <div class="mb-3">
                                <label class="form-label">API Access Token <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('token') is-invalid @enderror"
                                       name="token" value="{{ $redx->token ?? '' }}"
                                       placeholder="Bearer ছাড়া শুধু টোকেন" required autocomplete="off" />
                                <small class="text-muted small-hint d-block mt-1">
                                    <strong>স্যান্ডবক্স</strong> ড্যাশবোর্ড থেকে স্যান্ডবক্স টোকেন · <strong>প্রোডাকশন</strong> থেকে লাইভ টোকেন।
                                    <span class="text-danger d-block mt-1">⚠ টোকেন নির্বাচিত পরিবেশের সাথে মিলাতে হবে।</span>
                                </small>
                                @error('token')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Webhook URL <small class="text-muted fw-normal">(ঐচ্ছিক)</small></label>
                                <div class="input-group">
                                    <input type="text" class="form-control"
                                           name="webhook_url"
                                           id="redx_webhook_url"
                                           value="{{ $redx->webhook_url ?? '' }}"
                                           placeholder="{{ config('app.url') }}/api/redx/webhook"
                                           autocomplete="off" />
                                    <button type="button" class="btn btn-outline-secondary" id="copy_webhook_url" title="কপি">
                                        <i class="fe-copy"></i>
                                    </button>
                                </div>
                                <small class="text-muted small-hint d-block mt-1">
                                    প্রস্তাবিত: <code id="suggested_webhook_url">{{ config('app.url') }}/api/redx/webhook</code><br>
                                    RedX ড্যাশবোর্ডে URL সেট করলে পার্সেল স্ট্যাটাস আপডেট পাবেন। খালি রাখলে ওয়েবহুক ব্যবহার হবে না।
                                </small>
                            </div>

                            <div class="courier-status-row">
                                <span><i class="fas fa-power-off text-muted me-1"></i> সার্ভিস চালু</span>
                                <div class="form-check form-switch m-0">
                                    <input class="form-check-input" type="checkbox" name="status" value="1"
                                           @if(isset($redx) && $redx->status==1) checked @endif>
                                </div>
                            </div>

                            <button type="submit" class="courier-btn-save redx">
                                <i class="fas fa-save"></i> সংরক্ষণ করুন
                            </button>
                        </form>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>
@endsection

@section('script')
<script src="{{ asset('public/backEnd/assets/libs/parsleyjs/parsley.min.js') }}"></script>
<script src="{{ asset('public/backEnd/assets/js/pages/form-validation.init.js') }}"></script>
<script src="{{ asset('public/backEnd/assets/libs/select2/js/select2.min.js') }}"></script>
<script>
    $(document).ready(function() {
        $(".select2").select2();

        $('#generate_pathao_token').on('click', function(){
            var $btn = $(this);
            var originalHtml = $btn.html();
            $btn.prop('disabled', true).html('<i class="fe-loader"></i> …');

            $.ajax({
                url: "/admin/courierapi/pathao-generate-token",
                type: "POST",
                data: { _token: "{{ csrf_token() }}" },
                success: function(res){
                    if(res.status === 'success' && res.token){
                        $('#pathao_token_display').val(res.token);
                        var tokenPreview = res.token.substring(0, 20) + '…';
                        var expiryInfo = '';
                        if(res.expiry_info) expiryInfo = '<br><small><strong>মেয়াদ:</strong> ' + res.expiry_info + '</small>';
                        if(res.expires_at) expiryInfo += '<br><small><strong>শেষ হবে:</strong> ' + res.expires_at + '</small>';

                        toastr.success(
                            'টোকেন তৈরি ও ডাটাবেজে সেভ হয়েছে।' + expiryInfo + '<br>' +
                            '<small><strong>প্রিভিউ:</strong> ' + tokenPreview + '</small>',
                            'Pathao টোকেন',
                            {timeOut: 7000}
                        );

                        var $tokenField = $('#pathao_token_display');
                        $tokenField.css({'background-color': '#ecfdf5', 'border-color': '#10b981'});
                        setTimeout(function(){
                            $tokenField.css({'background-color': '', 'border-color': ''});
                        }, 2000);
                    } else {
                        toastr.error(res.message || 'টোকেন তৈরি ব্যর্থ');
                    }
                    $btn.prop('disabled', false).html(originalHtml);
                },
                error: function(xhr){
                    var errorMsg = 'টোকেন তৈরি ব্যর্থ';
                    if(xhr.status === 404) errorMsg = 'রুট পাওয়া যায়নি।';
                    else if(xhr.status === 500) errorMsg = 'সার্ভার ত্রুটি।'; 
                    else if(xhr.responseJSON && xhr.responseJSON.message) errorMsg = xhr.responseJSON.message;
                    else if(xhr.responseText) {
                        try {
                            var response = JSON.parse(xhr.responseText);
                            errorMsg = response.message || errorMsg;
                        } catch(e) {
                            errorMsg = xhr.responseText.substring(0, 200);
                        }
                    }
                    toastr.error(errorMsg);
                    $btn.prop('disabled', false).html(originalHtml);
                }
            });
        });

        $('#copy_webhook_url').on('click', function(){
            var webhookUrl = $('#redx_webhook_url').val();
            if (!webhookUrl) webhookUrl = $('#suggested_webhook_url').text();

            var $copyBtn = $(this);

            navigator.clipboard.writeText(webhookUrl).then(function() {
                toastr.success('Webhook URL কপি হয়েছে।', 'কপি', {timeOut: 3000});
                $copyBtn.html('<i class="fe-check"></i>').addClass('btn-success').removeClass('btn-outline-secondary');
                setTimeout(function(){
                    $copyBtn.html('<i class="fe-copy"></i>').removeClass('btn-success').addClass('btn-outline-secondary');
                }, 2000);
            }).catch(function() {
                var $temp = $('<input>');
                $('body').append($temp);
                $temp.val(webhookUrl).select();
                document.execCommand('copy');
                $temp.remove();
                toastr.success('Webhook URL কপি হয়েছে।', 'কপি', {timeOut: 3000});
            });
        });

        $('#redx_webhook_url').on('focus', function(){
            if (!$(this).val()) {
                $(this).val($('#suggested_webhook_url').text());
            }
        });

        $('.copy-steadfast-webhook').on('click', function(){
            var url = $('#steadfast_webhook_url').val() || $('#steadfast_suggested_webhook').text();
            navigator.clipboard.writeText(url).then(function() {
                toastr.success('Steadfast Webhook URL কপি হয়েছে।');
            }).catch(function() {
                var $t = $('<input>').val(url).appendTo('body').select();
                document.execCommand('copy');
                $t.remove();
                toastr.success('Steadfast Webhook URL কপি হয়েছে।');
            });
        });

        $('#steadfast_webhook_url').on('focus', function(){
            if (!$(this).val()) {
                $(this).val($('#steadfast_suggested_webhook').text());
            }
        });

        $('#generate_steadfast_webhook_token').on('click', function(){
            var arr = new Uint8Array(24);
            crypto.getRandomValues(arr);
            var hex = Array.from(arr).map(function(b){ return b.toString(16).padStart(2,'0'); }).join('');
            $('#steadfast_webhook_token').val('sfwh_' + hex);
            toastr.info('টোকেন তৈরি হয়েছে — সংরক্ষণ করুন এবং Steadfast ড্যাশবোর্ডে বসান।');
        });
    });
</script>
@endsection
