@extends('layouts.mb-auth')
@section('auth_video', 'off')
@section('title', 'Reset Password')
@section('heading', 'Reset Password')
@section('subheading', 'Enter your new password below.')

@push('styles')
<style>
.strength-bar { height: 4px; border-radius: 2px; background: #e9ecef; margin: -6px 0 4px; overflow: hidden; }
.strength-fill { height: 100%; width: 0; border-radius: 2px; transition: width .3s, background .3s; }
.strength-text { font-size: 11px; color: #aaa; margin-bottom: 10px; min-height: 14px; }
</style>
@endpush

@section('content')
@if ($errors->any() && ! $errors->has('email') && ! $errors->has('password'))
    <div class="alert alert-danger small">{{ $errors->first() }}</div>
@endif
<form method="POST" action="{{ route('admin.password.update') }}">
    @csrf
    <input type="hidden" name="token" value="{{ $token }}">
    <div @class(['au-field', 'is-invalid' => $errors->has('email')])>
        <span class="ic"><i class="fa-regular fa-envelope"></i></span>
        <input id="email" type="email" name="email" value="{{ old('email', $email ?? '') }}" placeholder="Email" required autofocus>
    </div>
    @error('email')<div class="au-err">{{ $message }}</div>@enderror

    <div @class(['au-field', 'is-invalid' => $errors->has('password')])>
        <span class="ic"><i class="fa-solid fa-lock"></i></span>
        <input id="password" type="password" name="password" placeholder="New Password" autocomplete="new-password" required>
        <button type="button" class="eye" tabindex="-1"><i class="fa-regular fa-eye-slash"></i></button>
    </div>
    @error('password')<div class="au-err">{{ $message }}</div>@enderror
    <div class="strength-bar"><div class="strength-fill" id="strength-fill"></div></div>
    <div class="strength-text" id="strength-text"></div>

    <div class="au-field">
        <span class="ic"><i class="fa-solid fa-lock"></i></span>
        <input id="password-confirm" type="password" name="password_confirmation" placeholder="Confirm New Password" autocomplete="new-password" required>
        <button type="button" class="eye" tabindex="-1"><i class="fa-regular fa-eye-slash"></i></button>
    </div>

    <button type="submit" class="au-btn">Reset Password</button>
    <a href="{{ route('login') }}" class="au-btn-2">Back to Login</a>
</form>
@endsection

@push('scripts')
<script>
document.getElementById('password').addEventListener('input', function () {
    const val = this.value, fill = document.getElementById('strength-fill'), text = document.getElementById('strength-text');
    let score = 0;
    if (val.length >= 8) score++;
    if (/[A-Z]/.test(val)) score++;
    if (/[0-9]/.test(val)) score++;
    if (/[^A-Za-z0-9]/.test(val)) score++;
    if (!val.length) { fill.style.width = '0'; text.textContent = ''; return; }
    const colors = ['#ef4444', '#f97316', '#eab308', '#22c55e'], labels = ['Weak', 'Fair', 'Good', 'Strong'], i = Math.max(score - 1, 0);
    fill.style.width = ((i + 1) * 25) + '%'; fill.style.background = colors[i];
    text.textContent = labels[i]; text.style.color = colors[i];
});
</script>
@endpush
