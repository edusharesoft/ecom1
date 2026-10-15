@extends('delivery.layouts.app')
@section('title', 'ডেলিভারি লগইন')

@section('shell_class', 'app-shell--login')
@section('body_class', 'delivery-login-page')

@section('app_header')
@endsection

@section('header_title', '')

@push('css')
<style>
@include('delivery.auth.partials.card-styles')
</style>
@endpush

@section('content')
<div class="login-screen">
    <div class="login-panel">
        <div class="login-brand" aria-hidden="true">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 18.75a1.5 1.5 0 01-3 0m3 0a1.5 1.5 0 00-3 0m3 0h6m-9 0H3.375a1.125 1.125 0 01-1.125-1.125V14.25m17.25 4.5a1.5 1.5 0 01-3 0m3 0a1.5 1.5 0 00-3 0m3 0h1.125c.621 0 1.129-.504 1.09-1.124a17.902 17.902 0 00-3.213-9.193 2.056 2.056 0 00-1.58-.86H14.25M16.5 18.75h1.875a1.875 1.875 0 001.865-2.165l-1.622-8.946A2.25 2.25 0 0018.75 4.5H6.75a2.25 2.25 0 00-2.24 2.016l-1.622 8.946A1.875 1.875 0 004.875 18.75H9"/>
            </svg>
        </div>
        <h1>ডেলিভারি পোর্টাল</h1>
        <p class="login-lead">রাইডার একাউন্টে প্রবেশ করতে মোবাইল নম্বর ও পাসওয়ার্ড লিখুন।</p>

        <form method="post" action="{{ route('delivery.login.post') }}" novalidate>
            @csrf

            <div class="login-field">
                <label for="login-phone">মোবাইল নম্বর</label>
                <div class="login-input-wrap">
                    <svg class="icon-left" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                    <input id="login-phone" type="tel" name="phone" value="{{ old('phone') }}" placeholder="01XXXXXXXXX" required autocomplete="username" inputmode="tel">
                </div>
            </div>

            <div class="login-field">
                <label for="login-password">পাসওয়ার্ড</label>
                <div class="login-input-wrap has-toggle">
                    <svg class="icon-left" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                    <input id="login-password" type="password" name="password" required autocomplete="current-password" minlength="6" placeholder="পাসওয়ার্ড">
                    <button type="button" class="login-toggle-pw" id="login-toggle-pw" aria-label="পাসওয়ার্ড দেখান">
                        <svg id="eye-open" width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                        <svg id="eye-shut" width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="display:none"><path stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/></svg>
                    </button>
                </div>
            </div>

            <label class="login-remember">
                <input type="checkbox" name="remember" value="1" {{ old('remember') ? 'checked' : '' }}>
                <span>আমাকে মনে রাখুন</span>
            </label>

            <button type="submit" class="login-submit">লগইন</button>
        </form>

        <p class="login-foot" style="margin-top:1rem;">
            <a href="{{ route('delivery.password.request') }}" class="auth-back-link" style="margin-top:0;">পাসওয়ার্ড ভুলে গেছেন?</a>
        </p>
        <p class="login-foot" style="margin-top:0.75rem;">মোবাইল OTP দিয়ে পাসওয়ার্ড রিসেট করা যাবে।</p>
    </div>
</div>
@endsection

@push('scripts')
<script>
(function(){
    var btn = document.getElementById('login-toggle-pw');
    var input = document.getElementById('login-password');
    var eyeO = document.getElementById('eye-open');
    var eyeS = document.getElementById('eye-shut');
    if (!btn || !input) return;
    btn.addEventListener('click', function() {
        var t = input.getAttribute('type') === 'password' ? 'text' : 'password';
        input.setAttribute('type', t);
        eyeO.style.display = t === 'password' ? 'block' : 'none';
        eyeS.style.display = t === 'password' ? 'none' : 'block';
        btn.setAttribute('aria-label', t === 'password' ? 'পাসওয়ার্ড দেখান' : 'লুকান');
    });
})();
</script>
@endpush
