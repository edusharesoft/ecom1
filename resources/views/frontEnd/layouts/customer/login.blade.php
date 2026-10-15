@php
    $generalsetting = $generalsetting ?? \App\Models\GeneralSetting::first();
@endphp
@extends('layouts.mb-auth')
@section('title', 'Customer Login')
@section('heading', 'Login To Your Account')
@section('subheading', 'আপনার মোবাইল নাম্বার বা ইমেইল এবং পাসওয়ার্ড দিন')

@section('content')
<form action="{{ route('customer.signin') }}" method="POST">
    @csrf
    <div @class(['au-field', 'is-invalid' => $errors->has('login')])>
        <span class="ic"><i class="fa-regular fa-user"></i></span>
        <input type="text" id="login" name="login" value="{{ old('login') }}" placeholder="মোবাইল নাম্বার (017xxxxxxxx) অথবা ইমেইল" autocomplete="username" required autofocus>
    </div>
    @error('login')<div class="au-err">{{ $message }}</div>@enderror

    <div @class(['au-field', 'is-invalid' => $errors->has('password')])>
        <span class="ic"><i class="fa-solid fa-lock"></i></span>
        <input type="password" id="password" name="password" placeholder="পাসওয়ার্ড" autocomplete="current-password" required>
        <button type="button" class="eye" tabindex="-1" aria-label="Show password"><i class="fa-regular fa-eye-slash"></i></button>
    </div>
    @error('password')<div class="au-err">{{ $message }}</div>@enderror

    <button type="submit" class="au-btn">লগিন করুন</button>
    <a href="{{ route('customer.forgot.password') }}" class="au-btn-2">পাসওয়ার্ড ভুলে গেছেন?</a>
</form>

@if(isset($demoMode) && $demoMode)
    <div class="au-note">
        <div class="mb-1 fw-semibold"><i class="fa-solid fa-circle-info text-primary"></i> ডেমো একাউন্ট</div>
        <small class="d-block mb-1 text-muted">রিসেলার ইউজার</small>
        <div class="d-flex gap-2 align-items-center flex-wrap mb-2">
            <input type="text" class="form-control form-control-sm" value="01631843149" readonly style="flex:1;min-width:0">
            <input type="text" class="form-control form-control-sm" value="12345678" readonly style="width:100px">
            <button type="button" class="btn btn-sm btn-outline-primary" onclick="fillDemoCreds('01631843149','12345678')">Use</button>
        </div>
        <small class="d-block mb-1 text-muted">ভেন্ডর ইউজার</small>
        <div class="d-flex gap-2 align-items-center flex-wrap">
            <input type="text" class="form-control form-control-sm" value="01870829343" readonly style="flex:1;min-width:0">
            <input type="text" class="form-control form-control-sm" value="123456789" readonly style="width:100px">
            <button type="button" class="btn btn-sm btn-outline-primary" onclick="fillDemoCreds('01870829343','123456789')">Use</button>
        </div>
    </div>
@endif

<div class="au-links">একাউন্ট না থাকলে? <a href="{{ route('customer.register') }}">রেজিস্ট্রেশন করুন</a></div>
@if(!empty($generalsetting->hotline ?? null))
    <div class="au-links"><i class="fa-solid fa-phone"></i> হটলাইন: {{ $generalsetting->hotline }}</div>
@endif
@endsection

@push('scripts')
<script>
function fillDemoCreds(login, pass) {
    document.getElementById('login').value = login;
    document.getElementById('password').value = pass;
}
</script>
@endpush
