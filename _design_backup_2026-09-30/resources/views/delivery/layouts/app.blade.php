<!DOCTYPE html>
<html lang="bn">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <meta name="theme-color" content="#2563eb">
    <link rel="icon" href="{{ asset('public/delivery-favicon.svg') }}" type="image/svg+xml">
    <link rel="shortcut icon" href="{{ asset('public/delivery-favicon.svg') }}" type="image/svg+xml">
    <link rel="apple-touch-icon" href="{{ asset('public/delivery-favicon.svg') }}">
    <title>@yield('title', 'Delivery')</title>
    <style>
        :root {
            --d-bg: #f1f5f9;
            --d-surface: #ffffff;
            --d-text: #0f172a;
            --d-muted: #64748b;
            --d-accent: #2563eb;
            --d-accent-soft: rgba(37, 99, 235, 0.1);
            --d-success: #059669;
            --d-success-soft: #d1fae5;
            --d-danger: #dc2626;
            --d-border: #e2e8f0;
            --d-radius: 16px;
            --d-radius-sm: 12px;
            --d-shadow: 0 1px 3px rgba(15, 23, 42, 0.06), 0 8px 24px rgba(15, 23, 42, 0.06);
            --d-sidebar: #0f172a;
            --safe-b: env(safe-area-inset-bottom, 12px);
            --safe-t: env(safe-area-inset-top, 0px);
            --nav-h: 56px;
            --bp-md: 768px;
            --bp-lg: 1024px;
        }
        * { box-sizing: border-box; }
        html { -webkit-text-size-adjust: 100%; }
        body {
            margin: 0;
            font-family: ui-sans-serif, system-ui, -apple-system, "Segoe UI", Roboto, "Noto Sans Bengali", sans-serif;
            background: var(--d-bg);
            color: var(--d-text);
            min-height: 100dvh;
            padding-bottom: calc(var(--nav-h) + var(--safe-b));
        }
        body.delivery-login-page {
            padding-bottom: var(--safe-b);
        }
        @media (min-width: 768px) {
            body:has(.delivery-sidebar) {
                padding-bottom: 0;
            }
        }

        /* লগইন: পূর্ণ প্রস্থ */
        .app-shell {
            margin: 0 auto;
            min-height: 100dvh;
            width: 100%;
            background: var(--d-bg);
        }
        .app-shell.app-shell--login {
            max-width: none;
            min-height: 100dvh;
            display: flex;
            flex-direction: column;
            background: #fafafa;
        }
        .app-shell--login .delivery-inner {
            flex: 1;
            display: flex;
            flex-direction: column;
            min-height: 0;
        }
        .app-shell--login .app-main {
            flex: 1;
            display: flex;
            flex-direction: column;
            min-height: 0;
            padding-top: max(12px, env(safe-area-inset-top));
            padding-right: max(16px, env(safe-area-inset-right));
            padding-bottom: max(16px, env(safe-area-inset-bottom));
            padding-left: max(16px, env(safe-area-inset-left));
        }

        /* অটhed: মোবাইল = ফোন-ফ্রেন্ডলি কলাম; ডেস্কটপ = সাইডবার */
        .app-shell--app {
            max-width: none;
        }
        .delivery-layout {
            display: flex;
            min-height: 100dvh;
            align-items: stretch;
        }

        .delivery-sidebar {
            display: none;
            width: 268px;
            flex-shrink: 0;
            background: linear-gradient(180deg, var(--d-sidebar) 0%, #1e293b 100%);
            color: #fff;
            flex-direction: column;
            padding: 1.25rem 0 var(--safe-b);
            position: sticky;
            top: 0;
            align-self: flex-start;
            min-height: 100dvh;
        }
        @media (min-width: 768px) {
            .delivery-sidebar {
                display: flex;
            }
        }
        .delivery-sidebar__brand {
            padding: 0 1.25rem 1.25rem;
            border-bottom: 1px solid rgba(255,255,255,.08);
            margin-bottom: 0.75rem;
        }
        .delivery-sidebar__brand-row {
            display: flex;
            align-items: center;
            gap: 12px;
        }
        .delivery-sidebar__avatar {
            width: 44px;
            height: 44px;
            border-radius: 50%;
            object-fit: cover;
            flex-shrink: 0;
            border: 2px solid rgba(255,255,255,.15);
        }
        .delivery-sidebar__avatar--fallback {
            display: flex;
            align-items: center;
            justify-content: center;
            background: rgba(255,255,255,.12);
            color: #fff;
            font-weight: 800;
            font-size: 1rem;
        }
        .delivery-sidebar__brand-text strong {
            display: block;
            font-size: 1.05rem;
            font-weight: 800;
            letter-spacing: -0.02em;
        }
        .delivery-sidebar__brand-text span {
            font-size: 0.78rem;
            color: #94a3b8;
            margin-top: 4px;
            display: block;
        }
        .delivery-sidebar__nav {
            flex: 1;
            display: flex;
            flex-direction: column;
            gap: 4px;
            padding: 0 10px;
        }
        .delivery-sidebar__nav a {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 12px 14px;
            border-radius: var(--d-radius-sm);
            color: #cbd5e1;
            text-decoration: none;
            font-size: 0.92rem;
            font-weight: 600;
            transition: background .15s, color .15s;
        }
        .delivery-sidebar__nav a svg {
            width: 22px;
            height: 22px;
            opacity: 0.9;
            flex-shrink: 0;
        }
        .delivery-sidebar__nav a:hover {
            background: rgba(255,255,255,.08);
            color: #fff;
        }
        .delivery-sidebar__nav a.is-active {
            background: rgba(37, 99, 235, 0.35);
            color: #fff;
        }
        .delivery-sidebar__foot {
            padding: 1rem 1.25rem 0;
            border-top: 1px solid rgba(255,255,255,.08);
        }
        .delivery-sidebar__foot form { margin: 0; }
        .delivery-sidebar__logout {
            width: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            padding: 10px 14px;
            border-radius: var(--d-radius-sm);
            border: 1px solid rgba(255,255,255,.2);
            background: transparent;
            color: #e2e8f0;
            font-size: 0.88rem;
            font-weight: 600;
            cursor: pointer;
        }
        .delivery-sidebar__logout:hover {
            background: rgba(255,255,255,.1);
        }

        .delivery-inner {
            flex: 1;
            min-width: 0;
            display: flex;
            flex-direction: column;
        }

        .app-header {
            position: sticky;
            top: 0;
            z-index: 40;
            background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);
            color: #fff;
            padding: max(12px, var(--safe-t)) 16px 14px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            box-shadow: 0 4px 24px rgba(15,23,42,.2);
        }
        @media (min-width: 768px) {
            .delivery-layout .app-header {
                display: none;
            }
        }
        .app-header h1 {
            margin: 0;
            font-size: clamp(0.95rem, 3vw, 1.1rem);
            font-weight: 700;
            line-height: 1.35;
            flex: 1;
            min-width: 0;
        }
        .app-header .hdr-actions { flex-shrink: 0; }
        .app-header button, .app-header a.logout-link {
            background: none;
            border: none;
            color: #93c5fd;
            font-size: 0.8rem;
            font-weight: 600;
            cursor: pointer;
            padding: 6px 4px;
            text-decoration: none;
        }

        /* ডেস্কটপ পেইজ শিরোনাম বার */
        .delivery-page-head {
            display: none;
            padding: 1.5rem 2rem 0.5rem;
            max-width: 1040px;
            margin: 0 auto;
            width: 100%;
        }
        @media (min-width: 768px) {
            .delivery-page-head {
                display: block;
            }
        }
        .delivery-page-head h1 {
            margin: 0;
            font-size: 1.5rem;
            font-weight: 800;
            letter-spacing: -0.03em;
            color: var(--d-text);
        }
        .delivery-page-head p {
            margin: 0.35rem 0 0;
            font-size: 0.9rem;
            color: var(--d-muted);
        }

        .app-main {
            padding: 14px 14px 28px;
            flex: 1;
        }
        @media (min-width: 768px) {
            .app-shell--app .app-main {
                max-width: 1040px;
                margin: 0 auto;
                width: 100%;
                padding: 0 2rem 3rem;
            }
        }

        .app-shell--login .app-main > .alert {
            flex-shrink: 0;
            max-width: 400px;
            width: 100%;
            margin-left: auto;
            margin-right: auto;
            margin-bottom: 12px;
        }
        @media (min-width: 900px) {
            .app-shell--login .app-main > .alert {
                max-width: 400px;
            }
        }
        .app-shell--login .app-main > :last-child {
            flex: 1;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            min-height: 0;
            width: 100%;
        }

        /* কার্ড ও উপাদান */
        .d-card {
            background: var(--d-surface);
            border-radius: var(--d-radius);
            padding: 1rem 1.15rem;
            margin-bottom: 14px;
            border: 1px solid var(--d-border);
            box-shadow: var(--d-shadow);
        }
        @media (min-width: 768px) {
            .d-card {
                padding: 1.25rem 1.35rem;
                margin-bottom: 18px;
                border-radius: 18px;
            }
        }
        .d-card--flush { padding: 0; overflow: hidden; }

        .d-stat-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 10px;
            margin-bottom: 16px;
        }
        @media (min-width: 768px) {
            .d-stat-grid {
                grid-template-columns: repeat(3, 1fr);
                gap: 16px;
            }
        }
        .d-stat {
            background: var(--d-surface);
            border-radius: var(--d-radius-sm);
            padding: 14px 10px;
            text-align: center;
            border: 1px solid var(--d-border);
            box-shadow: var(--d-shadow);
        }
        @media (min-width: 768px) {
            .d-stat {
                padding: 1.25rem 1rem;
                border-radius: var(--d-radius);
            }
            .d-stat b { font-size: 1.5rem !important; }
        }
        .d-stat b {
            display: block;
            font-size: 1.35rem;
            font-weight: 800;
            color: var(--d-accent);
            letter-spacing: -0.02em;
        }
        .d-stat span {
            font-size: 0.72rem;
            color: var(--d-muted);
            font-weight: 600;
            margin-top: 4px;
            display: block;
        }

        .d-list-link {
            display: block;
            padding: 14px 16px;
            border-bottom: 1px solid #f1f5f9;
            text-decoration: none;
            color: inherit;
            transition: background .12s;
        }
        .d-list-link:last-child { border-bottom: 0; }
        .d-list-link:hover { background: #f8fafc; }

        .d-badge {
            display: inline-flex;
            align-items: center;
            padding: 4px 10px;
            border-radius: 999px;
            font-size: 0.7rem;
            font-weight: 700;
            background: #e0e7ff;
            color: #3730a3;
        }

        .d-btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            padding: 12px 18px;
            border-radius: var(--d-radius-sm);
            border: none;
            font-weight: 700;
            font-size: 0.95rem;
            cursor: pointer;
            text-decoration: none;
            width: 100%;
            transition: transform .1s, box-shadow .15s;
        }
        .d-btn:active { transform: scale(0.99); }
        .d-btn--primary {
            background: linear-gradient(135deg, var(--d-accent) 0%, #1d4ed8 100%);
            color: #fff;
            box-shadow: 0 4px 14px rgba(37, 99, 235, 0.35);
        }
        .d-btn--success {
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
            color: #fff;
            box-shadow: 0 4px 14px rgba(5, 150, 105, 0.3);
        }
        .d-btn--outline {
            background: #fff;
            color: var(--d-accent);
            border: 2px solid var(--d-accent);
            box-shadow: none;
        }

        .d-link-top {
            display: block;
            text-align: center;
            margin-bottom: 14px;
        }
        .d-link-top a {
            color: var(--d-accent);
            font-weight: 700;
            text-decoration: none;
            font-size: 0.9rem;
        }
        .d-link-top a:hover { text-decoration: underline; }

        .d-form label, label.d-label {
            display: block;
            font-size: 0.8rem;
            font-weight: 600;
            margin-bottom: 6px;
            color: #334155;
        }
        .d-form input, .d-form select, .d-form textarea,
        .app-main input, .app-main select, .app-main textarea {
            width: 100%;
            padding: 12px 14px;
            border-radius: var(--d-radius-sm);
            border: 1px solid #cbd5e1;
            font-size: 16px;
        }
        .d-form input:focus, .d-form select:focus, .d-form textarea:focus {
            outline: none;
            border-color: var(--d-accent);
            box-shadow: 0 0 0 3px var(--d-accent-soft);
        }

        .d-empty {
            text-align: center;
            padding: 2rem 1rem;
            color: var(--d-muted);
            font-size: 0.9rem;
        }
        .d-muted-label { font-size: 0.8rem; color: var(--d-muted); font-weight: 600; }
        .d-inv { font-weight: 800; font-size: 1rem; }
        .d-muted-small { color: var(--d-muted); font-size: 0.82rem; line-height: 1.45; }
        .mb-3 { margin-bottom: 12px !important; }

        .d-pagination { margin-top: 1.25rem; padding: 0 4px; }
        .d-pagination nav { width: 100%; display: flex; justify-content: center; }
        .d-pagination ul { display: flex; flex-wrap: wrap; justify-content: center; gap: 6px; align-items: center; list-style: none; padding: 0; margin: 0; }
        .d-pagination li span,
        .d-pagination li a {
            display: inline-flex;
            align-items: center;
            min-height: 36px;
            padding: 6px 12px;
            border-radius: 10px;
            font-size: 0.85rem;
            border: 1px solid var(--d-border);
            background: #fff;
            color: var(--d-text);
            text-decoration: none;
        }
        .d-pagination li.active span,
        .d-pagination li span[aria-current="page"] {
            background: var(--d-accent);
            color: #fff;
            border-color: var(--d-accent);
        }
        .d-pagination li.disabled span {
            opacity: 0.5;
        }

        .alert {
            padding: 12px 14px;
            border-radius: var(--d-radius-sm);
            margin-bottom: 12px;
            font-size: 0.88rem;
            border: 1px solid transparent;
        }
        .alert-success { background: #ecfdf5; color: #047857; border-color: #a7f3d0; }
        .alert-error { background: #fef2f2; color: #b91c1c; border-color: #fecaca; }

        /* নিচের ন্যাভ: শুধু মোবাইল */
        .bottom-nav {
            position: fixed;
            left: 0;
            right: 0;
            bottom: 0;
            width: 100%;
            background: rgba(255,255,255,.94);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            border-top: 1px solid var(--d-border);
            display: flex;
            justify-content: space-around;
            align-items: stretch;
            min-height: calc(var(--nav-h) + var(--safe-b));
            padding-bottom: var(--safe-b);
            z-index: 100;
            box-shadow: 0 -8px 32px rgba(15, 23, 42, 0.08);
        }
        @media (min-width: 768px) {
            .bottom-nav { display: none !important; }
        }
        .bottom-nav a {
            flex: 1;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            gap: 3px;
            padding: 8px 2px 10px;
            text-decoration: none;
            color: var(--d-muted);
            font-size: 0.62rem;
            font-weight: 700;
            min-height: 52px;
        }
        .bottom-nav a.active {
            color: var(--d-accent);
        }
        .bottom-nav svg {
            width: 21px;
            height: 21px;
            opacity: 0.85;
        }

        /* টেবিল ডেস্কটপ (ওয়ালেট ট্রানজ্যাকশন) */
        .d-table-wrap {
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
        }
        @media (min-width: 768px) {
            .d-table-desktop {
                display: table;
                width: 100%;
                border-collapse: collapse;
                font-size: 0.9rem;
            }
            .d-table-desktop th {
                text-align: left;
                padding: 12px 14px;
                background: #f8fafc;
                color: var(--d-muted);
                font-size: 0.72rem;
                font-weight: 700;
                text-transform: uppercase;
                letter-spacing: 0.04em;
                border-bottom: 2px solid var(--d-border);
            }
            .d-table-desktop td {
                padding: 14px;
                border-bottom: 1px solid #f1f5f9;
            }
        }
        .d-tx-mobile {
            padding: 14px 16px;
            border-bottom: 1px solid #f1f5f9;
            font-size: 0.88rem;
        }
        @media (min-width: 768px) {
            .d-tx-mobile { display: none; }
            .d-table-desktop { display: table; }
        }
        @media (max-width: 767px) {
            .d-table-wrap > table.d-table-desktop { display: none; }
        }
    </style>
    @stack('css')
</head>
<body class="@yield('body_class')">
<div class="app-shell @yield('shell_class') @auth('delivery_boy') app-shell--app @endauth">
    @auth('delivery_boy')
    <div class="delivery-layout">
        <aside class="delivery-sidebar" aria-label="মেনু">
            <div class="delivery-sidebar__brand">
                @php $sbBoy = auth()->guard('delivery_boy')->user(); @endphp
                <div class="delivery-sidebar__brand-row">
                    @if($sbBoy && $sbBoy->photo_url)
                        <img src="{{ $sbBoy->photo_url }}" alt="" class="delivery-sidebar__avatar" width="44" height="44">
                    @else
                        <div class="delivery-sidebar__avatar delivery-sidebar__avatar--fallback" aria-hidden="true">{{ Str::upper(Str::substr($sbBoy->name ?? '?', 0, 1)) }}</div>
                    @endif
                    <div class="delivery-sidebar__brand-text">
                        <strong>ডেলিভারি</strong>
                        <span>{{ Str::limit($sbBoy->name ?? '', 28) }}</span>
                    </div>
                </div>
            </div>
            <nav class="delivery-sidebar__nav">
                <a href="{{ route('delivery.dashboard') }}" class="{{ request()->routeIs('delivery.dashboard') ? 'is-active' : '' }}">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="2" stroke-linecap="round" stroke-linejoin="round" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                    ড্যাশবোর্ড
                </a>
                <a href="{{ route('delivery.orders.index') }}" class="{{ request()->routeIs('delivery.orders.index') || request()->routeIs('delivery.orders.show') ? 'is-active' : '' }}">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2"/></svg>
                    অর্ডার
                </a>
                <a href="{{ route('delivery.orders.history') }}" class="{{ request()->routeIs('delivery.orders.history') ? 'is-active' : '' }}">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="2" stroke-linecap="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    ইতিহাস
                </a>
                <a href="{{ route('delivery.wallet') }}" class="{{ request()->routeIs('delivery.wallet*') ? 'is-active' : '' }}">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg>
                    ওয়ালেট
                </a>
                <a href="{{ route('delivery.profile.edit') }}" class="{{ request()->routeIs('delivery.profile.*') ? 'is-active' : '' }}">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="2" stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                    প্রফাইল
                </a>
            </nav>
            <div class="delivery-sidebar__foot">
                <form action="{{ route('delivery.logout') }}" method="post">
                    @csrf
                    <button type="submit" class="delivery-sidebar__logout">লগআউট</button>
                </form>
            </div>
        </aside>
    @endauth

        <div class="delivery-inner">
            @hasSection('app_header')
                @yield('app_header')
            @else
                <header class="app-header">
                    <h1>@yield('header_title', 'ডেলিভারি')</h1>
                    <div class="hdr-actions">
                        @auth('delivery_boy')
                            <form action="{{ route('delivery.logout') }}" method="post" style="margin:0;display:inline;">
                                @csrf
                                <button type="submit">লগআউট</button>
                            </form>
                        @endauth
                    </div>
                </header>
            @endif

            @auth('delivery_boy')
                @hasSection('page_heading')
                    @yield('page_heading')
                @else
                    @php
                        $__dTitle = trim($__env->yieldContent('header_title')) ?: 'ডেলিভারি';
                    @endphp
                    <div class="delivery-page-head">
                        <h1>{{ $__dTitle }}</h1>
                        @yield('page_subtitle')
                    </div>
                @endif
            @endauth

            <main class="app-main">
                @if(session('success'))<div class="alert alert-success">{{ session('success') }}</div>@endif
                @if(session('error'))<div class="alert alert-error">{{ session('error') }}</div>@endif
                @if($errors->any())
                    <div class="alert alert-error">{{ $errors->first() }}</div>
                @endif
                @yield('content')
            </main>
        </div>
    @auth('delivery_boy')
    </div>
    @endauth
</div>

@auth('delivery_boy')
<nav class="bottom-nav" aria-label="নেভিগেশন">
    <a href="{{ route('delivery.dashboard') }}" class="{{ request()->routeIs('delivery.dashboard') ? 'active' : '' }}">
        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
        হোম
    </a>
    <a href="{{ route('delivery.orders.index') }}" class="{{ request()->routeIs('delivery.orders.*') ? 'active' : '' }}">
        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
        অর্ডার
    </a>
    <a href="{{ route('delivery.wallet') }}" class="{{ request()->routeIs('delivery.wallet*') ? 'active' : '' }}">
        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg>
        ওয়ালেট
    </a>
    <a href="{{ route('delivery.profile.edit') }}" class="{{ request()->routeIs('delivery.profile.*') ? 'active' : '' }}">
        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="2" stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
        প্রফাইল
    </a>
</nav>
@endauth
@stack('scripts')
</body>
</html>
