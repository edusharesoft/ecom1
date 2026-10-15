@extends('delivery.layouts.app')
@section('title', 'ড্যাশবোর্ড')
@section('header_title', 'হ্যালো, '.$boy->name)

@section('page_subtitle')
    <p>আজকের সারসংক্ষেপ ও দ্রুত অ্যাক্সেস।</p>
@endsection

@section('content')
<div class="d-card d-profile-quick">
    <a href="{{ route('delivery.profile.edit') }}" class="d-profile-quick-link">
        <span class="d-profile-quick-left">
            @if($boy->photo_url)
                <img src="{{ $boy->photo_url }}" alt="" width="44" height="44" class="d-profile-quick-img">
            @else
                <span class="d-profile-quick-fallback">{{ Str::upper(Str::substr($boy->name ?? '?', 0, 1)) }}</span>
            @endif
            <span class="d-profile-quick-text">
                <strong>প্রফাইল ও ছবি</strong>
                <small>নাম, ইমেইল ও ফটো আপডেট</small>
            </span>
        </span>
        <span class="d-profile-quick-arrow" aria-hidden="true">›</span>
    </a>
</div>

<div class="d-stat-grid">
    <div class="d-stat"><b>{{ $pending }}</b><span>বাকি ডেলিভারি</span></div>
    <div class="d-stat"><b>{{ $today }}</b><span>আজ ডেলিভারি</span></div>
    <div class="d-stat"><b>{{ $doneTotal }}</b><span>মোট সম্পন্ন</span></div>
</div>

<div class="d-card">
    <div class="d-wallet-strip">
        <div>
            <div class="d-muted-label">ওয়ালেট ব্যালেন্স</div>
            <div class="d-wallet-amt">৳{{ number_format($boy->wallet_balance, 2) }}</div>
        </div>
        <div class="d-commission-hint">প্রতি ডেলিভারি কমিশন: ৳{{ number_format($boy->commission_per_delivery, 2) }}</div>
    </div>
</div>

<div class="d-card">
    <div class="d-section-title">অপেক্ষমান অর্ডার</div>
    @forelse($recent as $o)
        <a href="{{ route('delivery.orders.show', $o->id) }}" class="d-list-link">
            <div class="d-row-top">
                <span class="d-inv">#{{ $o->invoice_id }}</span>
            </div>
            <small class="d-muted-small">{{ $o->shipping->name ?? '—' }} · {{ $o->shipping->phone ?? '' }}</small>
        </a>
    @empty
        <p class="d-empty" style="padding:1rem 0;margin:0;">কোনো অপেক্ষমান অর্ডার নেই</p>
    @endforelse
    @if($pending > 5)
        <div style="padding:12px 16px 4px;">
            <a href="{{ route('delivery.orders.index') }}" class="d-btn d-btn--outline">সব অর্ডার দেখুন</a>
        </div>
    @endif
</div>
@endsection

@push('css')
<style>
    .d-wallet-strip { display: flex; flex-wrap: wrap; align-items: flex-start; justify-content: space-between; gap: 12px; }
    .d-wallet-amt { font-size: 1.5rem; font-weight: 800; color: var(--d-success); letter-spacing: -0.02em; margin-top: 4px; }
    .d-commission-hint { font-size: 0.8rem; color: var(--d-muted); text-align: right; flex: 1; min-width: 140px; }
    @media (max-width: 767px) { .d-commission-hint { text-align: left; width: 100%; } }
    .d-section-title { font-weight: 800; font-size: 0.95rem; margin-bottom: 4px; padding: 0 4px 8px; border-bottom: 1px solid #f1f5f9; }
    .d-row-top { margin-bottom: 4px; }
    .d-profile-quick-link {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
        text-decoration: none;
        color: inherit;
        padding: 4px 2px;
    }
    .d-profile-quick-link:active { opacity: 0.92; }
    .d-profile-quick-left {
        display: flex;
        align-items: center;
        gap: 12px;
        min-width: 0;
    }
    .d-profile-quick-img {
        border-radius: 50%;
        object-fit: cover;
        border: 2px solid var(--d-border);
        flex-shrink: 0;
    }
    .d-profile-quick-fallback {
        width: 44px;
        height: 44px;
        border-radius: 50%;
        background: linear-gradient(135deg, #dbeafe 0%, #e0e7ff 100%);
        color: #4338ca;
        font-weight: 800;
        font-size: 1.1rem;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
        border: 2px solid var(--d-border);
    }
    .d-profile-quick-text { display: flex; flex-direction: column; gap: 2px; min-width: 0; }
    .d-profile-quick-text strong { font-size: 0.95rem; }
    .d-profile-quick-text small { font-size: 0.78rem; color: var(--d-muted); }
    .d-profile-quick-arrow {
        font-size: 1.35rem;
        font-weight: 300;
        color: var(--d-accent);
        flex-shrink: 0;
        line-height: 1;
    }
</style>
@endpush
