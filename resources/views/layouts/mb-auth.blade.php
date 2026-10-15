{{--
    Multibranch-style login / registration layout (same design as the Multi-Branch SaaS platform).
    Child views set:  @section('title')  @section('heading')  @section('subheading')  @section('content')
    optional:         @section('auth_size', 'wide')   → wider card (long forms)
                      @section('auth_home', url)      → "Home" link at the top right
                      @section('auth_kind', 'register') → registration background + video
                      @section('auth_video', 'off')    → no video (OTP, reset, lock pages)
--}}
@php
    $gs = $generalsetting ?? (class_exists(\App\Models\GeneralSetting::class) ? \App\Models\GeneralSetting::first() : null);
    $brandName = $gs->name ?? config('app.name');
    $logo = !empty($gs?->dark_logo) ? asset($gs->dark_logo) : (!empty($gs?->white_logo) ? asset($gs->white_logo) : null);
    $size = trim($__env->yieldContent('auth_size'));
    $kind = trim($__env->yieldContent('auth_kind')) === 'register' ? 'register' : 'login';
    $bg = \App\Support\AuthTheme::background($kind);
    $video = trim($__env->yieldContent('auth_video')) === 'off' ? null : \App\Support\AuthTheme::video($kind);
    $home = trim($__env->yieldContent('auth_home')) ?: url('/');
    $footer = 'Developed By : Shataj Soft';
    [$fPre, $fName] = array_map('trim', explode(':', $footer, 2));
@endphp
<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Hind+Siliguri:wght@400;500;600;700&family=Poppins:wght@400;500;600;700;800&family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('public/backEnd/assets/css/toastr.min.css') }}">
    @include('partials.mb.auth-css')
    @if (!empty($gs?->favicon))<link rel="shortcut icon" href="{{ asset($gs->favicon) }}">@endif
    <title>@yield('title') | {{ $brandName }}</title>
    @stack('styles')
</head>
<body class="auth-ui">
<div class="au-bg" @if ($bg) style="--au-img:url('{{ $bg }}')" @endif></div>
<div class="au-top">
    <a href="{{ $home }}"><i class="fa-solid fa-house"></i> Home</a>
</div>

<main class="au-wrap">
    <div @class(['au-card', 'wide' => $size === 'wide' && $video, 'solo' => ! $video && $size !== 'wide'])>
        <div class="au-head">
            @if ($logo)
                <img src="{{ $logo }}" alt="{{ $brandName }}" class="au-logo">
            @else
                <div class="au-name"><i class="fa-solid fa-store" style="color:#0ea5e9;-webkit-text-fill-color:#0ea5e9"></i> {{ $brandName }}</div>
            @endif
            <div class="au-title">@yield('heading')</div>
            @hasSection('subheading')<div class="au-sub">@yield('subheading')</div>@endif
        </div>

        @foreach (['success' => 'success', 'status' => 'success', 'message' => 'info', 'warning' => 'warning', 'error' => 'danger'] as $key => $type)
            @if (session($key) && is_string(session($key)))
                <div class="alert alert-{{ $type }} alert-dismissible fade show small" role="alert">
                    {{ session($key) }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif
        @endforeach

        @if ($video)
            <div class="row g-3 align-items-stretch">
                <div class="col-md-6 order-2 order-md-1">
                    <button type="button" class="au-media" data-yt="{{ $video }}" aria-label="Play video">
                        <img src="https://i.ytimg.com/vi/{{ $video }}/hqdefault.jpg" alt="" loading="lazy">
                        <span class="play"></span>
                    </button>
                </div>
                <div class="col-md-6 order-1 order-md-2 au-form">@yield('content')</div>
            </div>
        @else
            <div class="au-form">@yield('content')</div>
        @endif
    </div>
    <div class="au-foot">{{ $fPre }} : <b>{{ $fName }}</b></div>
</main>

<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="{{ asset('public/backEnd/assets/js/toastr.min.js') }}"></script>
@if (class_exists('Toastr'))
    {!! Toastr::message() !!}
@endif
<script>
(() => {
    // show / hide password
    document.querySelectorAll('.au-field .eye').forEach(b => b.addEventListener('click', () => {
        const i = b.parentElement.querySelector('input');
        const show = i.type === 'password';
        i.type = show ? 'text' : 'password';
        b.innerHTML = show ? '<i class="fa-regular fa-eye"></i>' : '<i class="fa-regular fa-eye-slash"></i>';
    }));
    // YouTube: the player is loaded only when clicked (fast page, no tracking before that)
    document.querySelectorAll('.au-media[data-yt]').forEach(b => b.addEventListener('click', () => {
        const id = b.dataset.yt;
        if (!/^[A-Za-z0-9_-]{11}$/.test(id)) return;
        b.innerHTML = '<iframe src="https://www.youtube-nocookie.com/embed/' + id + '?autoplay=1&rel=0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>';
        b.style.cursor = 'default';
    }, {once: true}));
})();
</script>
@stack('scripts')
</body>
</html>
