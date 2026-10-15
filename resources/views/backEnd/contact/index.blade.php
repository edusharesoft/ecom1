@extends('backEnd.layouts.master')
@section('title','Contact Control Center')

@section('css')
<style>
    /* --- ELITE UI ARCHITECTURE --- */
    :root {
        --glass-bg: rgba(255, 255, 255, 0.95);
        --brand-blue: #2563eb;
        --sidebar-bg: #1e293b;
    }

    .control-center-wrapper {
        padding: 20px 0;
    }

    /* 1. LEFT INFO SIDEBAR (On Large Screens) */
    .sidebar-info-card {
        background: var(--sidebar-bg);
        border-radius: 20px;
        padding: 30px;
        color: #ffffff; /* মেইন টেক্সট সাদা */
        height: 100%;
        box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.2);
    }
    .sidebar-info-card .info-header {
        border-bottom: 1px solid rgba(255,255,255,0.15);
        margin-bottom: 25px;
        padding-bottom: 15px;
    }
    
    /* সাইডবার হেডলাইন সাদা */
    .sidebar-info-card h5 {
        color: #ffffff !important;
        font-weight: 700;
    }
    
    /* সাইডবার সাব-টাইটেল (সাদাটে হালকা রঙ) */
    .sidebar-info-card .text-muted {
        color: #cbd5e1 !important; 
    }

    .current-item {
        display: flex;
        align-items: center;
        gap: 15px;
        margin-bottom: 20px;
        padding: 15px;
        background: rgba(255,255,255,0.08); /* একটু উজ্জ্বল ব্যাকগ্রাউন্ড */
        border-radius: 12px;
        border: 1px solid rgba(255,255,255,0.05);
        transition: all 0.3s;
    }
    
    .current-item:hover {
        background: rgba(255,255,255,0.12);
    }

    .current-item i { font-size: 20px; color: #60a5fa; }
    
    /* লেবেল (যেমন: Hotline, Mail) - উজ্জ্বল সাদা */
    .current-item span { 
        font-size: 12px; 
        color: #e2e8f0; 
        display: block; 
        font-weight: 500;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
    
    /* ভ্যালু (যেমন: ফোন নাম্বার, ইমেইল) - একদম পিওর সাদা */
    .current-item strong { 
        font-size: 14px; 
        color: #ffffff; 
        font-weight: 600; 
        word-break: break-all; 
    }

    /* সিস্টেম হেলথ সেকশন */
    .sidebar-info-card .border-secondary {
        border-color: rgba(255,255,255,0.2) !important;
        background: rgba(255,255,255,0.03);
    }
    
    .sidebar-info-card .small {
        color: #f8fafc !important;
    }

    /* 2. RIGHT FORM CANVAS */
    .form-canvas {
        background: #fff;
        border-radius: 20px;
        border: 1px solid #e2e8f0;
        box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.05);
        padding: 40px;
    }
    
    .section-divider {
        display: flex;
        align-items: center;
        margin: 30px 0 20px 0;
        font-size: 11px;
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: 1.5px;
        color: #94a3b8;
    }
    .section-divider::after {
        content: "";
        flex: 1;
        height: 1px;
        background: #f1f5f9;
        margin-left: 15px;
    }

    /* 3. INPUT REFINEMENT */
    .input-wrapper {
        position: relative;
        margin-bottom: 20px;
    }
    .input-wrapper i {
        position: absolute;
        left: 18px;
        top: 42px;
        color: #94a3b8;
        transition: color 0.3s;
    }
    .elite-label {
        font-size: 13px;
        font-weight: 700;
        color: #334155;
        margin-bottom: 10px;
        display: block;
    }
    .elite-input {
        width: 100%;
        background: #f8fafc;
        border: 2px solid #f1f5f9;
        border-radius: 12px;
        padding: 12px 15px 12px 48px;
        font-size: 14px;
        color: #1e293b;
        transition: all 0.3s ease;
    }
    .elite-input:focus {
        background: #fff;
        border-color: var(--brand-blue);
        box-shadow: 0 0 0 4px rgba(37, 99, 235, 0.1);
        outline: none;
    }
    .elite-input:focus + i { color: var(--brand-blue); }

    /* Glossy submit */
    .btn-sync {
        background: var(--brand-blue);
        color: #fff;
        border: none;
        padding: 15px 35px;
        border-radius: 12px;
        font-weight: 700;
        letter-spacing: 0.5px;
        transition: all 0.3s;
        box-shadow: 0 10px 15px -3px rgba(37, 99, 235, 0.3);
    }
    .btn-sync:hover {
        transform: translateY(-2px);
        box-shadow: 0 20px 25px -5px rgba(37, 99, 235, 0.4);
        background: #1d4ed8;
        color: #fff;
    }
</style>
@endsection

@section('content')
<div class="container-fluid control-center-wrapper">
    
    <div class="row g-4">
        <div class="col-xl-3 col-lg-4">
            <div class="sidebar-info-card">
                <div class="info-header">
                    <h5 class="m-0">Live Preview</h5>
                    <small class="text-muted">Current published details</small>
                </div>

                <div class="current-item">
                    <i class="fe-phone-call"></i>
                    <div><span>Hotline</span><strong>{{ $contact->hotline ?? 'Not Set' }}</strong></div>
                </div>
                <div class="current-item">
                    <i class="fe-mail"></i>
                    <div><span>Business Mail</span><strong>{{ $contact->email }}</strong></div>
                </div>
                <div class="current-item">
                    <i class="fe-map-pin"></i>
                    <div><span>Location</span><strong>{{ Str::limit($contact->address, 40) }}</strong></div>
                </div>
                
                <div class="mt-4 p-3 rounded-4 border border-secondary border-dashed">
                    <small class="text-muted d-block mb-2">System Health</small>
                    <div class="d-flex align-items-center gap-2">
                        <div class="spinner-grow spinner-grow-sm text-success"></div>
                        <span class="small">Contact Module Online</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-9 col-lg-8">
            <div class="form-canvas">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h4 class="fw-bold text-dark m-0">Global Contact Settings</h4>
                    <a href="{{route('contact.index')}}" class="btn btn-sm btn-outline-secondary px-3 rounded-pill">
                        <i class="fe-refresh-cw me-1"></i> Reset View
                    </a>
                </div>

                <form action="{{route('contact.update')}}" method="POST" id="eliteContactForm" data-parsley-validate="" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" value="{{$contact->id}}" name="hidden_id">

                    <div class="section-divider">Communication Channels</div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="input-wrapper">
                                <label class="elite-label">Support Hotline</label>
                                <input type="text" class="elite-input" name="hotline" value="{{ old('hotline', $contact->hotline) }}">
                                <i class="fe-phone-outgoing"></i>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="input-wrapper">
                                <label class="elite-label">Official Email Address *</label>
                                <input type="email" class="elite-input" name="email" value="{{ old('email', $contact->email) }}" required>
                                <i class="fe-mail"></i>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="input-wrapper">
                                <label class="elite-label">Primary Phone (Mobile) *</label>
                                <input type="text" class="elite-input" name="phone" value="{{ old('phone', $contact->phone) }}" required>
                                <i class="fe-smartphone"></i>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="input-wrapper">
                                <label class="elite-label">Secondary / Help Email</label>
                                <input type="email" class="elite-input" name="hotmail" value="{{ old('hotmail', $contact->hotmail) }}">
                                <i class="fe-at-sign"></i>
                            </div>
                        </div>
                    </div>

                    <div class="section-divider">Location & Presence</div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="input-wrapper">
                                <label class="elite-label">WhatsApp Integration</label>
                                <input type="text" class="elite-input" name="whatsapp" value="{{ old('whatsapp', $contact->whatsapp) }}">
                                <i class="fab fa-whatsapp"></i>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="input-wrapper">
                                <label class="elite-label">Google Maps URL</label>
                                <input type="text" class="elite-input" name="maplink" value="{{ old('maplink', $contact->maplink) }}">
                                <i class="fe-map"></i>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="input-wrapper">
                                <label class="elite-label">Physical Office Address *</label>
                                <textarea class="elite-input" name="address" rows="2" style="padding-left: 48px;" required>{{ old('address', $contact->address) }}</textarea>
                                <i class="fe-navigation" style="top: 42px;"></i>
                            </div>
                        </div>
                    </div>

                    <div class="row mt-4">
                        <div class="col-12 text-end">
                            <button type="submit" class="btn btn-sync">
                                <i class="fe-zap me-2"></i> Deploy Settings
                            </button>
                        </div>
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