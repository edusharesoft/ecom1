@extends('delivery.layouts.app')
@section('title', 'ড্যাশবোর্ড')
@section('header_title', 'হ্যালো, '.$boy->name)

@section('page_subtitle')
    <p>আজকের সারসংক্ষেপ ও দ্রুত অ্যাক্সেস।</p>
@endsection

@section('content')
@php
    $dCards = [
        ['fa-hourglass-half', 'বাকি ডেলিভারি', number_format($pending), 'অপেক্ষমান অর্ডার', '#fde6c2', '#c2410c', route('delivery.orders.index')],
        ['fa-truck-fast', 'আজ ডেলিভারি', number_format($today), now()->format('d M Y'), '#bfd7fd', '#1d4ed8', route('delivery.orders.history')],
        ['fa-circle-check', 'মোট সম্পন্ন', number_format($doneTotal), 'সব সময়', '#c0ead7', '#047857', route('delivery.orders.history')],
        ['fa-wallet', 'ওয়ালেট ব্যালেন্স', '৳'.number_format($boy->wallet_balance, 2), 'কমিশন: ৳'.number_format($boy->commission_per_delivery, 2).' / ডেলিভারি', '#d4c5f2', '#6d28d9', route('delivery.wallet')],
    ];
@endphp

<div class="dd-grid">
    @foreach ($dCards as [$icon, $label, $value, $sub, $bg, $color, $link])
        <a href="{{ $link }}" class="st-card" style="background:{{ $bg }}">
            <span class="st-l">{{ $label }}</span>
            <span class="st-ic" style="color:{{ $color }}"><i class="fa-solid {{ $icon }}"></i></span>
            <span class="st-v" style="color:{{ $color }}">{{ $value }}</span>
            <span class="st-s">{{ $sub }}</span>
        </a>
    @endforeach
</div>

<div class="dd-cols">
    <div class="dp yellow">
        <div class="dp-head">
            <h6><i class="fa-solid fa-clipboard-list" style="color:#d97706;margin-right:4px"></i> অপেক্ষমান অর্ডার</h6>
            <a href="{{ route('delivery.orders.index') }}" class="dd-more">সব দেখুন <i class="fa-solid fa-arrow-right"></i></a>
        </div>
        <div class="dp-body no-list">
            @forelse($recent as $o)
                <a href="{{ route('delivery.orders.show', $o->id) }}" class="row-o">
                    <span style="flex:1;min-width:0">
                        <span class="num" style="display:block">#{{ $o->invoice_id }}</span>
                        <span class="who" style="display:block;overflow:hidden;text-overflow:ellipsis;white-space:nowrap">{{ $o->shipping->name ?? '—' }} <small style="color:#6b7280">· {{ $o->shipping->phone ?? '' }}</small></span>
                    </span>
                    <i class="fa-solid fa-chevron-right" style="color:#9ca3af;font-size:12px"></i>
                </a>
            @empty
                <div class="dd-empty"><i class="fa-regular fa-folder-open"></i>কোনো অপেক্ষমান অর্ডার নেই</div>
            @endforelse
        </div>
    </div>

    <div class="dp purple">
        <div class="dp-head"><h6><i class="fa-solid fa-user-gear" style="color:#7c3aed;margin-right:4px"></i> প্রফাইল</h6></div>
        <div class="dp-body">
            <a href="{{ route('delivery.profile.edit') }}" class="dd-profile">
                @if($boy->photo_url)
                    <img src="{{ $boy->photo_url }}" alt="" width="54" height="54">
                @else
                    <span class="dd-fallback">{{ Str::upper(Str::substr($boy->name ?? '?', 0, 1)) }}</span>
                @endif
                <span style="min-width:0">
                    <strong>{{ $boy->name }}</strong>
                    <small>নাম, ইমেইল ও ফটো আপডেট করুন</small>
                </span>
                <i class="fa-solid fa-chevron-right" style="margin-left:auto;color:#7c3aed"></i>
            </a>
            <a href="{{ route('delivery.wallet') }}" class="dd-profile">
                <span class="dd-fallback" style="background:#dcfce7;color:#15803d"><i class="fa-solid fa-wallet"></i></span>
                <span style="min-width:0">
                    <strong>ওয়ালেট ও উইথড্র</strong>
                    <small>ব্যালেন্স ৳{{ number_format($boy->wallet_balance, 2) }}</small>
                </span>
                <i class="fa-solid fa-chevron-right" style="margin-left:auto;color:#7c3aed"></i>
            </a>
        </div>
    </div>
</div>
@endsection

@push('css')
<style>
    .dd-grid { display: grid; grid-template-columns: repeat(4, minmax(0, 1fr)); gap: 18px; margin-bottom: 22px; }
    @media (max-width: 1100px) { .dd-grid { grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 12px; } }
    .dd-cols { display: grid; grid-template-columns: 1.5fr 1fr; gap: 18px; }
    @media (max-width: 900px) { .dd-cols { grid-template-columns: 1fr; } }
    .dd-cols .dp-head h6 { font-size: 16px; }
    .dd-more { font-size: 13px; color: #2563eb; text-decoration: none; }
    .dd-empty { text-align: center; color: #6b7280; padding: 2rem 0; }
    .dd-empty i { display: block; font-size: 28px; margin-bottom: 8px; }
    .dd-profile { display: flex; align-items: center; gap: 12px; padding: 10px 0; border-bottom: 1px dashed #eef0f4; text-decoration: none; color: inherit; }
    .dd-profile:last-child { border-bottom: 0; }
    .dd-profile img { border-radius: 50%; object-fit: cover; border: 2px solid #e5e7eb; flex-shrink: 0; }
    .dd-profile strong { display: block; font-size: 15px; }
    .dd-profile small { display: block; font-size: 12px; color: #6b7280; }
    .dd-fallback { width: 54px; height: 54px; border-radius: 50%; background: linear-gradient(135deg, #dbeafe, #e0e7ff); color: #4338ca; font-weight: 800; font-size: 1.2rem;
        display: flex; align-items: center; justify-content: center; flex-shrink: 0; }
</style>
@endpush
