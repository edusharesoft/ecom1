@extends('reseller.layouts.app')

@section('title', 'ওয়ালেট ডিপোজিট')
@section('page-title', 'ওয়ালেট ডিপোজিট')

@section('content')
<div class="mb-4">
    <h4 class="fw-bold text-dark mb-1">ওয়ালেট ডিপোজিট</h4>
    <p class="text-muted small">উদ্যোক্তা পে এর মাধ্যমে ওয়ালেটে টাকা ডিপোজিট করুন। প্রথমে ডিপোজিট করেই অর্ডার করতে পারবেন।</p>
</div>

<div class="row">
    <div class="col-lg-6">
        <div class="card border-0 shadow-sm rounded-3">
            <div class="card-body p-4">
                <h5 class="fw-bold mb-4"><i class="fas fa-plus-circle text-success me-2"></i> ডিপোজিট করুন</h5>
                <form action="{{ route('reseller.deposit.store') }}" method="POST">
                    @csrf
                    <div class="mb-4">
                        <label class="form-label fw-bold">টাকার পরিমাণ (৳)</label>
                        <input type="number" name="amount" class="form-control form-control-lg @error('amount') is-invalid @enderror"
                               placeholder="ন্যূনতম ৳{{ number_format($depositMin ?? 100, 0) }}" min="{{ $depositMin ?? 100 }}" max="{{ $depositMax ?? 1000000 }}" step="1" value="{{ old('amount', max($depositMin ?? 100, 500)) }}" required>
                        @error('amount')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <small class="text-muted">ন্যূনতম ৳{{ number_format($depositMin ?? 100, 0) }} - সর্বোচ্চ ৳{{ number_format($depositMax ?? 1000000, 0) }} ডিপোজিট করুন</small>
                    </div>
                    <button type="submit" class="btn btn-success btn-lg w-100">
                        <i class="fas fa-wallet me-2"></i> উদ্যোক্তা পে দিয়ে পেমেন্ট করুন
                    </button>
                </form>
            </div>
        </div>
    </div>
    <div class="col-lg-6">
        <div class="card border-0 shadow-sm rounded-3">
            <div class="card-body p-4">
                <h5 class="fw-bold mb-4"><i class="fas fa-history text-primary me-2"></i> সম্প্রতি ডিপোজিট</h5>
                @forelse($deposits ?? [] as $d)
                <div class="d-flex justify-content-between align-items-center py-3 border-bottom">
                    <div>
                        <span class="fw-bold text-success">+৳{{ number_format($d->amount, 2) }}</span>
                        <br><small class="text-muted">{{ $d->created_at->format('d M Y, h:i A') }}</small>
                    </div>
                    <span class="badge bg-success">সম্পন্ন</span>
                </div>
                @empty
                <p class="text-muted mb-0">এখনও কোনো ডিপোজিট নেই</p>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection
