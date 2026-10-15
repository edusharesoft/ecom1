@extends('backEnd.layouts.master')
@section('title', 'Login & Registration Page')

@section('content')
@php
    $img = function ($key, $label, $hint) use ($s) {
        return compact('key', 'label', 'hint') + ['path' => $s[$key] ?? null];
    };
    $imgs = [
        $img('login_bg', 'লগইন পেইজের ব্যাকগ্রাউন্ড ছবি', 'চওড়া ছবি, 1920×1080, সর্বোচ্চ 4 MB'),
        $img('register_bg', 'রেজিস্ট্রেশন পেইজের ব্যাকগ্রাউন্ড ছবি', 'খালি রাখলে লগইনের ছবিই দেখাবে'),
    ];
@endphp

@if (session('success'))
    <div class="alert alert-success alert-dismissible fade show">{{ session('success') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
@endif
@if ($errors->any())
    <div class="alert alert-danger"><ul class="mb-0">@foreach ($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul></div>
@endif

<form method="POST" action="{{ route('settings.auth_theme.update') }}" enctype="multipart/form-data">
    @csrf
    <div class="dp purple mb-4">
        <div class="dp-head"><h6><i class="fa-solid fa-image me-1" style="color:#7c3aed"></i> ব্যাকগ্রাউন্ড ছবি</h6></div>
        <div class="dp-body">
            <div class="row g-4">
                @foreach ($imgs as $i)
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">{{ $i['label'] }}</label>
                        <div class="rounded border mb-2 d-flex align-items-center justify-content-center overflow-hidden" style="height:170px;background:linear-gradient(135deg,#1e3a8a 0%,#6d28d9 55%,#db2777 100%)">
                            @if ($i['path'])
                                <img src="{{ asset($i['path']) }}" alt="" style="width:100%;height:100%;object-fit:cover">
                            @else
                                <span class="text-white small">ছবি নেই, ডিফল্ট গ্রেডিয়েন্ট দেখাবে</span>
                            @endif
                        </div>
                        <input type="file" name="{{ $i['key'] }}" accept="image/jpeg,image/png,image/webp" class="form-control">
                        <div class="form-text">{{ $i['hint'] }}</div>
                        @if ($i['path'])
                            <div class="form-check mt-1">
                                <input class="form-check-input" type="checkbox" name="remove_{{ $i['key'] }}" value="1" id="rm_{{ $i['key'] }}" data-no-bs-switch="true">
                                <label class="form-check-label small text-danger" for="rm_{{ $i['key'] }}">এই ছবি মুছে ফেলুন</label>
                            </div>
                        @endif
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    <div class="dp pink mb-4">
        <div class="dp-head"><h6><i class="fa-brands fa-youtube me-1 text-danger"></i> YouTube ভিডিও</h6></div>
        <div class="dp-body">
            <div class="row g-4">
                <div class="col-md-6">
                    <label class="form-label fw-semibold">লগইন পেইজের YouTube ভিডিও</label>
                    <input name="login_video" value="{{ old('login_video', $s['login_video'] ?? '') }}" class="form-control" placeholder="https://www.youtube.com/watch?v=...">
                    @if ($id = \App\Support\AuthTheme::youtubeId($s['login_video'] ?? ''))<div class="form-text text-success">✓ Video ID: {{ $id }}</div>@endif
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold">রেজিস্ট্রেশন পেইজের YouTube ভিডিও</label>
                    <input name="register_video" value="{{ old('register_video', $s['register_video'] ?? '') }}" class="form-control" placeholder="খালি রাখলে লগইনের ভিডিওই দেখাবে">
                    @if ($id = \App\Support\AuthTheme::youtubeId($s['register_video'] ?? ''))<div class="form-text text-success">✓ Video ID: {{ $id }}</div>@endif
                </div>
                <div class="col-12">
                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" name="show_video" value="1" id="show_video" @checked(($s['show_video'] ?? '1') !== '0')>
                        <label class="form-check-label" for="show_video">লগইন / রেজিস্ট্রেশন পেইজে ভিডিও দেখাবে</label>
                    </div>
                    <div class="form-text">youtube.com/watch?v=, youtu.be/, shorts বা embed লিংক, যেকোনোটা দিতে পারেন। ভিডিও প্রথমে ছবি হিসেবে আসে, ক্লিক করলে চালু হয়, তাই পেইজ স্লো হয় না।</div>
                </div>
            </div>
        </div>
    </div>

    <div class="d-flex flex-wrap gap-2 align-items-center">
        <button type="submit" class="btn btn-primary px-4"><i class="fa-solid fa-floppy-disk me-1"></i> সেভ করুন</button>
        <a href="{{ route('customer.login') }}" target="_blank" class="btn btn-outline-secondary"><i class="fa-solid fa-arrow-up-right-from-square me-1"></i> লগইন পেইজ দেখুন</a>
        <a href="{{ route('customer.register') }}" target="_blank" class="btn btn-outline-secondary"><i class="fa-solid fa-arrow-up-right-from-square me-1"></i> রেজিস্ট্রেশন পেইজ দেখুন</a>
    </div>
</form>
@endsection
