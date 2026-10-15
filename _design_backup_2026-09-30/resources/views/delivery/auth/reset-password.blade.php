@extends('delivery.layouts.app')
@section('title', 'নতুন পাসওয়ার্ড')

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
                <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 00-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 002.25-2.25v-6.75a2.25 2.25 0 00-2.25-2.25H6.75a2.25 2.25 0 00-2.25 2.25v6.75a2.25 2.25 0 002.25 2.25z"/>
            </svg>
        </div>
        <h1>নতুন পাসওয়ার্ড সেট করুন</h1>
        <p class="login-lead"><strong>{{ $maskedPhone ?? '' }}</strong> যাচাই হয়েছে। নতুন পাসওয়ার্ড সেট করুন।</p>

        <form method="post" action="{{ route('delivery.password.store') }}">
            @csrf
            <div class="login-field">
                <label for="rs-password">নতুন পাসওয়ার্ড</label>
                <div class="login-input-wrap has-toggle">
                    <svg class="icon-left" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                    <input id="rs-password" type="password" name="password" required autocomplete="new-password" minlength="6" placeholder="কমপক্ষে ৬ অক্ষর">
                    <button type="button" class="login-toggle-pw" id="rs-toggle-pw" aria-label="পাসওয়ার্ড দেখান">
                        <svg id="rs-eye-open" width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                        <svg id="rs-eye-shut" width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="display:none"><path stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/></svg>
                    </button>
                </div>
            </div>

            <div class="login-field">
                <label for="rs-password-confirm">পাসওয়ার্ড আবার</label>
                <div class="login-input-wrap">
                    <svg class="icon-left" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                    <input id="rs-password-confirm" type="password" name="password_confirmation" required autocomplete="new-password" minlength="6" placeholder="আবার লিখুন">
                </div>
            </div>

            <button type="submit" class="login-submit">সংরক্ষণ করুন</button>
        </form>

        <a href="{{ route('delivery.login') }}" class="auth-back-link">← লগইন</a>
    </div>
</div>
@endsection

@push('scripts')
<script>
(function(){
    var btn = document.getElementById('rs-toggle-pw');
    var input = document.getElementById('rs-password');
    var eyeO = document.getElementById('rs-eye-open');
    var eyeS = document.getElementById('rs-eye-shut');
    if (!btn || !input) return;
    btn.addEventListener('click', function() {
        var t = input.getAttribute('type') === 'password' ? 'text' : 'password';
        input.setAttribute('type', t);
        eyeO.style.display = t === 'password' ? 'block' : 'none';
        eyeS.style.display = t === 'password' ? 'none' : 'block';
    });
})();
</script>
@endpush
