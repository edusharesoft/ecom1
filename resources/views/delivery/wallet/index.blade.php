@extends('delivery.layouts.app')
@section('title', 'ওয়ালেট')
@section('header_title', 'ওয়ালেট ও কমিশন')

@section('page_subtitle')
    <p>ব্যালেন্স, উথড্র’ অনুরোধ ও লেনদেনের ইতিহাস।</p>
@endsection

@section('content')
<div class="d-wallet-layout">
    <div class="d-wallet-col">
        <div class="d-card d-balance-card">
            <div class="d-muted-label">বর্তমান ব্যালেন্স</div>
            <div class="d-balance-big">৳{{ number_format($boy->wallet_balance, 2) }}</div>
        </div>

        @if($pending->count())
        <div class="d-card">
            <div class="d-section-title-sm">অপেক্ষমান উথড্র’র</div>
            @foreach($pending as $p)
                <div class="d-pending-row">
                    <span class="fw-amount">৳{{ number_format($p->amount, 2) }}</span>
                    <span class="d-pending-meta">{{ $p->payout_method }} · {{ $p->payout_number }}</span>
                </div>
            @endforeach
        </div>
        @endif
    </div>

    <div class="d-wallet-col">
        <div class="d-card d-form">
            <div class="d-section-title-sm">উথড্র’ অনুরোধ</div>
            <form method="post" action="{{ route('delivery.wallet.withdraw') }}">
                @csrf
                <label class="d-label">টাকার পরিমাণ (৳)</label>
                <input type="number" name="amount" min="1" step="1" required>
                <label class="d-label" style="margin-top:12px;">পেমেন্ট মাধ্যম</label>
                <select name="payout_method" required>
                    <option value="bkash">bKash</option>
                    <option value="nagad">Nagad</option>
                    <option value="bank">Bank</option>
                </select>
                <label class="d-label" style="margin-top:12px;">নম্বর / অ্যাকাউন্ট</label>
                <input type="text" name="payout_number" required placeholder="01xxxxxxxxx">
                <label class="d-label" style="margin-top:12px;">নোট (ঐচ্ছিক)</label>
                <textarea name="note" rows="2" placeholder=""></textarea>
                <button type="submit" class="d-btn d-btn--primary" style="margin-top:14px;">অনুরোধ পাঠান</button>
            </form>
            <p class="d-form-note">অ্যাডমিন অনুমোদনের পর ব্যালেন্স থেকে কাটা হবে।</p>
        </div>
    </div>
</div>

<div class="d-card d-card--flush" style="margin-bottom:0;">
    <div class="d-section-bar">লেনদেন</div>

    <div class="d-table-wrap">
        <table class="d-table-desktop">
            <thead>
                <tr>
                    <th>ধরন</th>
                    <th>পরিমাণ</th>
                    <th>সময়</th>
                    <th>নোট</th>
                </tr>
            </thead>
            <tbody>
                @foreach($tx as $t)
                    <tr>
                        <td>{{ $t->type }}</td>
                        <td><strong>{{ $t->direction === 'credit' ? '+' : '-' }}৳{{ number_format($t->amount, 2) }}</strong></td>
                        <td>{{ $t->created_at->format('d M Y, h:i A') }}</td>
                        <td>{{ $t->note ?? '—' }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    @foreach($tx as $t)
        <div class="d-tx-mobile">
            <div class="d-tx-line">
                <span>{{ $t->type }}</span>
                <strong>{{ $t->direction === 'credit' ? '+' : '-' }}৳{{ number_format($t->amount, 2) }}</strong>
            </div>
            <small class="d-tx-time">{{ $t->created_at->format('d M Y, h:i A') }}</small>
            @if($t->note)<small class="d-tx-note">{{ $t->note }}</small>@endif
        </div>
    @endforeach

    @if($tx->isEmpty())
        <p class="d-empty" style="padding:24px 16px;margin:0;">এখনো কোনো লেনদেন নেই</p>
    @endif
</div>

<div class="d-pagination">{{ $tx->links() }}</div>
@endsection

@push('css')
<style>
    .d-wallet-layout { display: block; }
    @media (min-width: 768px) {
        .d-wallet-layout {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
            align-items: start;
        }
    }
    .d-balance-card { text-align: center; }
    @media (min-width: 768px) { .d-balance-card { text-align: left; } }
    .d-balance-big {
        font-size: 2rem;
        font-weight: 800;
        color: var(--d-success);
        letter-spacing: -0.03em;
        margin-top: 6px;
    }
    .d-section-title-sm {
        font-weight: 800;
        font-size: 0.92rem;
        margin-bottom: 12px;
        padding-bottom: 8px;
        border-bottom: 1px solid #f1f5f9;
    }
    .d-section-bar {
        padding: 14px 18px;
        font-weight: 800;
        border-bottom: 1px solid var(--d-border);
        background: #fafbfc;
        font-size: 0.95rem;
    }
    .d-pending-row {
        padding: 12px 0;
        border-bottom: 1px solid #f1f5f9;
    }
    .d-pending-row:last-child { border-bottom: 0; }
    .fw-amount { font-weight: 800; font-size: 1rem; display: block; }
    .d-pending-meta { font-size: 0.8rem; color: var(--d-muted); }
    .d-form-note { font-size: 0.75rem; color: var(--d-muted); margin: 12px 0 0; }
    .d-tx-line { display: flex; justify-content: space-between; align-items: center; gap: 8px; }
    .d-tx-time { display: block; color: var(--d-muted); margin-top: 4px; font-size: 0.78rem; }
    .d-tx-note { display: block; color: var(--d-muted); margin-top: 6px; font-size: 0.82rem; }
</style>
@endpush
