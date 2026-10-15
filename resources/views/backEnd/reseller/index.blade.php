@extends('backEnd.layouts.master')
@section('title', 'রিসেলার তালিকা')

@section('css')
@include('backEnd.reseller.partials.reseller_list_styles')
@endsection

@section('content')
<div class="container-fluid reseller-list-shell">

    <div class="rs-page-header">
        <div>
            <h4>রিসেলার ব্যবস্থাপনা <span class="rs-badge-count">{{ $stats['total'] }}</span></h4>
            <p class="rs-sub mb-0">নিবন্ধিত রিসেলার ও পার্টনারদের তালিকা, ওয়ালেট ও ভেরিফিকেশন</p>
        </div>
        <div class="rs-header-actions">
            <a href="{{ route('admin.reseller.verification.index') }}" class="rs-btn-ghost">
                <i class="fas fa-shield-alt"></i> ভেরিফিকেশন
            </a>
            <a href="{{ route('admin.reseller.withdrawals.index') }}" class="rs-btn-ghost">
                <i class="fas fa-wallet"></i> উইথড্র
            </a>
            <a href="{{ route('admin.reseller-deposits.index') }}" class="rs-btn-ghost">
                <i class="fas fa-piggy-bank"></i> ডিপোজিট
            </a>
        </div>
    </div>

    <div class="rs-stat-grid">
        <div class="rs-stat">
            <div class="rs-stat-label">মোট রিসেলার</div>
            <div class="rs-stat-val">{{ $stats['total'] }}</div>
        </div>
        <div class="rs-stat active-stat">
            <div class="rs-stat-label">সক্রিয়</div>
            <div class="rs-stat-val">{{ $stats['active'] }}</div>
        </div>
        <div class="rs-stat verified-stat">
            <div class="rs-stat-label">ভেরিফাইড</div>
            <div class="rs-stat-val">{{ $stats['verified'] }}</div>
        </div>
        <div class="rs-stat pending-stat">
            <div class="rs-stat-label">ভেরিফিকেশন বাকি</div>
            <div class="rs-stat-val">{{ $stats['pending'] }}</div>
        </div>
    </div>

    <div class="rs-card">
        <div class="rs-card-head">
            <h6><i class="fas fa-search"></i> খুঁজুন</h6>
        </div>
        <div class="rs-card-body">
            <form method="GET" action="{{ route('admin.resellers.index') }}" class="rs-filter-grid">
                <div>
                    <label class="rs-label">কীওয়ার্ড</label>
                    <input type="text" name="keyword" class="rs-input"
                           placeholder="নাম, শপ, ইমেইল..."
                           value="{{ request('keyword') }}">
                </div>
                <div class="rs-filter-actions">
                    <button type="submit" class="btn rs-btn-primary">
                        <i class="fas fa-search me-1"></i> খুঁজুন
                    </button>
                    @if(request('keyword'))
                    <a href="{{ route('admin.resellers.index') }}" class="rs-btn-ghost">
                        <i class="fas fa-redo"></i> রিসেট
                    </a>
                    @endif
                </div>
            </form>
        </div>
    </div>

    <div class="rs-card">
        <div class="rs-card-head">
            <h6><i class="fas fa-user-tie"></i> রিসেলার তালিকা</h6>
        </div>
        <div class="rs-card-body">
            <p class="d-lg-none rs-scroll-hint"><i class="fas fa-arrows-alt-h me-1"></i> বাকি কলাম দেখতে স্লাইড করুন</p>

            <div class="rs-table-rail">
                <table class="table rs-table mb-0">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>প্রোফাইল</th>
                            <th>শপ</th>
                            <th>যোগাযোগ</th>
                            <th>ওয়ালেট</th>
                            <th>ভেরিফিকেশন</th>
                            <th>স্ট্যাটাস</th>
                            <th class="text-end">অ্যাকশন</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($resellers as $reseller)
                        <tr>
                            <td class="text-muted">{{ $loop->iteration + ($resellers->currentPage() - 1) * $resellers->perPage() }}</td>
                            <td>
                                <div class="rs-shop-cell">
                                    @if(!empty($reseller->image))
                                        <img src="{{ asset($reseller->image) }}" alt="" class="rs-shop-avatar">
                                    @else
                                        <div class="rs-shop-placeholder">{{ strtoupper(substr($reseller->name, 0, 1)) }}</div>
                                    @endif
                                    <div>
                                        <div class="rs-shop-name">{{ $reseller->name }}</div>
                                        <div class="rs-shop-meta">ID #{{ $reseller->id }}</div>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <span class="fw-semibold text-dark">{{ $reseller->shop_name ?? '—' }}</span>
                            </td>
                            <td>
                                <div class="rs-contact-line mb-0">
                                    <i class="far fa-envelope"></i>
                                    <span title="{{ $reseller->email }}">{{ Str::limit($reseller->email, 24) }}</span>
                                </div>
                            </td>
                            <td>
                                <span class="rs-balance">৳{{ number_format($reseller->wallet_balance ?? 0, 2) }}</span>
                            </td>
                            <td>
                                @if($reseller->verification_status == 'approved')
                                    <span class="rs-pill rs-badge-verified"><span class="rs-dot"></span> Verified</span>
                                @elseif($reseller->verification_status == 'rejected')
                                    <span class="rs-pill rs-badge-rejected"><span class="rs-dot"></span> Rejected</span>
                                @else
                                    <span class="rs-pill rs-badge-pending"><span class="rs-dot"></span> Pending</span>
                                @endif
                            </td>
                            <td>
                                @if($reseller->status == 1)
                                    <span class="rs-pill rs-badge-active">Active</span>
                                @else
                                    <span class="rs-pill rs-badge-inactive">Inactive</span>
                                @endif
                            </td>
                            <td>
                                <div class="rs-actions">
                                    <form method="post" action="{{ route('admin.resellers.toggle-status', $reseller->id) }}" class="d-inline">
                                        @csrf
                                        @if($reseller->status == 1)
                                            <button type="submit" class="rs-action-btn deactivate" title="নিষ্ক্রিয় করুন">
                                                <i class="fas fa-ban"></i>
                                            </button>
                                        @else
                                            <button type="submit" class="rs-action-btn activate" title="সক্রিয় করুন">
                                                <i class="fas fa-check"></i>
                                            </button>
                                        @endif
                                    </form>
                                    <a href="{{ route('admin.resellers.edit', $reseller->id) }}" class="rs-action-btn edit" title="সম্পাদনা">
                                        <i class="fas fa-pen"></i>
                                    </a>
                                    <form method="post" action="{{ route('admin.resellers.destroy', $reseller->id) }}" class="d-inline"
                                          onsubmit="return confirm('এই রিসেলার মুছে ফেলবেন? সংশ্লিষ্ট অ্যাকাউন্টও মুছে যাবে।');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="rs-action-btn delete" title="মুছুন">
                                            <i class="fas fa-trash-alt"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8">
                                <div class="rs-empty">
                                    <div><i class="fas fa-user-slash"></i></div>
                                    <p class="mb-0 fw-semibold">কোনো রিসেলার পাওয়া যায়নি</p>
                                    @if(request('keyword'))
                                    <p class="small mb-0 mt-1">অন্য কীওয়ার্ড দিয়ে খুঁজুন অথবা ফিল্টার রিসেট করুন</p>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        @if($resellers->hasPages() || $resellers->total() > 0)
        <div class="rs-foot">
            <div class="rs-foot-meta">
                দেখানো হচ্ছে <strong>{{ $resellers->firstItem() ?? 0 }}</strong>–<strong>{{ $resellers->lastItem() ?? 0 }}</strong>
                / মোট <strong>{{ $resellers->total() }}</strong> রিসেলার
            </div>
            <div>{{ $resellers->links('pagination::bootstrap-4') }}</div>
        </div>
        @endif
    </div>
</div>
@endsection

