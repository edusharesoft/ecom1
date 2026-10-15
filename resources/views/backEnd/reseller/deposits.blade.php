@extends('backEnd.layouts.master')
@section('title', 'Reseller Deposits')

@section('css')
@include('backEnd.partials.admin_layout_gap_fix')
<style>
    html, body { background: #eef1f8; }
</style>
@endsection

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="mb-1 fw-bold text-dark">
                <i data-feather="wallet" class="text-primary me-2"></i> রিসেলার ডিপোজিট
            </h4>
            <p class="text-muted small mb-0">পেন্ডিং ডিপোজিট এডমিন পেইড মার্ক করলে ওয়ালেটে যোগ হবে</p>
        </div>
    </div>

    {{-- Filter --}}
    <div class="card border-0 shadow-sm rounded-3 mb-4">
        <div class="card-body py-3">
            <div class="d-flex gap-2 flex-wrap">
                <a href="{{ route('admin.reseller-deposits.index') }}" class="btn btn-sm {{ !request('status') ? 'btn-primary' : 'btn-outline-secondary' }}">
                    সব
                </a>
                <a href="{{ route('admin.reseller-deposits.index', ['status' => 'pending']) }}" class="btn btn-sm {{ request('status') === 'pending' ? 'btn-warning' : 'btn-outline-warning' }}">
                    পেন্ডিং
                </a>
                <a href="{{ route('admin.reseller-deposits.index', ['status' => 'completed']) }}" class="btn btn-sm {{ request('status') === 'completed' ? 'btn-success' : 'btn-outline-success' }}">
                    পেইড
                </a>
                <a href="{{ route('admin.reseller-deposits.index', ['status' => 'failed']) }}" class="btn btn-sm {{ request('status') === 'failed' ? 'btn-danger' : 'btn-outline-danger' }}">
                    ব্যর্থ
                </a>
            </div>
        </div>
    </div>

    <div class="card border-0 shadow-sm rounded-3">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>#</th>
                        <th>রিসেলার</th>
                        <th>পরিমাণ</th>
                        <th>স্ট্যাটাস</th>
                        <th>তারিখ</th>
                        <th>ট্রানজেকশন ID</th>
                        <th class="text-end">অ্যাকশন</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($deposits as $d)
                    <tr>
                        <td>{{ $d->id }}</td>
                        <td>
                            <div>
                                <strong>{{ $d->user->name ?? 'N/A' }}</strong>
                                @if($d->user->shop_name)
                                <br><small class="text-muted">{{ $d->user->shop_name }}</small>
                                @endif
                                <br><small class="text-muted">{{ $d->user->email ?? '' }}</small>
                            </div>
                        </td>
                        <td><strong class="text-success">৳{{ number_format($d->amount, 2) }}</strong></td>
                        <td>
                            @if($d->status === 'pending')
                            <span class="badge bg-warning">পেন্ডিং</span>
                            @elseif($d->status === 'completed')
                            <span class="badge bg-success">পেইড</span>
                            @else
                            <span class="badge bg-danger">ব্যর্থ</span>
                            @endif
                        </td>
                        <td>{{ $d->created_at->format('d M Y, h:i A') }}</td>
                        <td><small class="text-muted">{{ $d->transaction_id ?? '-' }}</small></td>
                        <td class="text-end">
                            @if($d->status === 'pending')
                            <form action="{{ route('admin.reseller-deposits.mark-paid', $d->id) }}" method="POST" class="d-inline" onsubmit="return confirm('পেমেন্ট নিশ্চিত করেছেন? ওয়ালেটে টাকা যোগ হবে।');">
                                @csrf
                                <button type="submit" class="btn btn-sm btn-success">
                                    <i class="mdi mdi-check-circle"></i> পেইড মার্ক করুন
                                </button>
                            </form>
                            @else
                            <span class="text-muted small">-</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center py-5 text-muted">কোনো ডিপোজিট নেই</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($deposits->hasPages())
        <div class="card-footer">
            {{ $deposits->withQueryString()->links() }}
        </div>
        @endif
    </div>
</div>
@endsection
