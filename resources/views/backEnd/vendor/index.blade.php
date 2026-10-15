@extends('backEnd.layouts.master')
@section('title', 'ভেন্ডর তালিকা')

@section('css')
@include('backEnd.vendor.partials.vendor_list_styles')
@endsection

@section('content')
<div class="container-fluid vendor-list-shell">

    <div class="vn-page-header">
        <div>
            <h4>ভেন্ডর ব্যবস্থাপনা <span class="vn-badge-count">{{ $stats['total'] }}</span></h4>
            <p class="vn-sub mb-0">নিবন্ধিত সেলার ও শপের তালিকা, স্ট্যাটাস ও ভেরিফিকেশন পরিচালনা</p>
        </div>
        <div class="vn-header-actions">
            <a href="{{ route('admin.vendor.verification.index') }}" class="vn-btn-ghost">
                <i class="fas fa-shield-alt"></i> ভেরিফিকেশন
            </a>
            <a href="{{ route('admin.vendor.withdrawals.index') }}" class="vn-btn-ghost">
                <i class="fas fa-wallet"></i> উইথড্র
            </a>
        </div>
    </div>

    <div class="vn-stat-grid">
        <div class="vn-stat">
            <div class="vn-stat-label">মোট ভেন্ডর</div>
            <div class="vn-stat-val">{{ $stats['total'] }}</div>
        </div>
        <div class="vn-stat active-stat">
            <div class="vn-stat-label">সক্রিয়</div>
            <div class="vn-stat-val">{{ $stats['active'] }}</div>
        </div>
        <div class="vn-stat verified-stat">
            <div class="vn-stat-label">ভেরিফাইড</div>
            <div class="vn-stat-val">{{ $stats['verified'] }}</div>
        </div>
        <div class="vn-stat pending-stat">
            <div class="vn-stat-label">ভেরিফিকেশন বাকি</div>
            <div class="vn-stat-val">{{ $stats['pending'] }}</div>
        </div>
    </div>

    <div class="vn-card">
        <div class="vn-card-head">
            <h6><i class="fas fa-search"></i> খুঁজুন</h6>
        </div>
        <div class="vn-card-body">
            <form method="GET" action="{{ route('admin.vendors.index') }}" class="vn-filter-grid">
                <div>
                    <label class="vn-label">কীওয়ার্ড</label>
                    <input type="text" name="keyword" class="vn-input"
                           placeholder="শপ, মালিক, ইমেইল, ফোন..."
                           value="{{ request('keyword') }}">
                </div>
                <div class="vn-filter-actions">
                    <button type="submit" class="btn vn-btn-primary">
                        <i class="fas fa-search me-1"></i> খুঁজুন
                    </button>
                    @if(request('keyword'))
                    <a href="{{ route('admin.vendors.index') }}" class="vn-btn-ghost">
                        <i class="fas fa-redo"></i> রিসেট
                    </a>
                    @endif
                </div>
            </form>
        </div>
    </div>

    <div class="vn-card">
        <div class="vn-card-head">
            <h6><i class="fas fa-store"></i> ভেন্ডর তালিকা</h6>
        </div>
        <div class="vn-card-body">
            <p class="d-lg-none vn-scroll-hint"><i class="fas fa-arrows-alt-h me-1"></i> বাকি কলাম দেখতে স্লাইড করুন</p>

            <div class="vn-table-rail">
                <table class="table vn-table mb-0">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>শপ</th>
                            <th>মালিক</th>
                            <th>যোগাযোগ</th>
                            <th>প্রোডাক্ট</th>
                            <th>ব্যালেন্স</th>
                            <th>ভেরিফিকেশন</th>
                            <th>স্ট্যাটাস</th>
                            <th class="text-end">অ্যাকশন</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($vendors as $vendor)
                        <tr>
                            <td class="text-muted">{{ $loop->iteration + ($vendors->currentPage() - 1) * $vendors->perPage() }}</td>
                            <td>
                                <div class="vn-shop-cell">
                                    @if($vendor->logo)
                                        <img src="{{ asset($vendor->logo) }}" alt="" class="vn-shop-avatar">
                                    @else
                                        <div class="vn-shop-placeholder">{{ strtoupper(substr($vendor->shop_name, 0, 1)) }}</div>
                                    @endif
                                    <div>
                                        <div class="vn-shop-name">{{ $vendor->shop_name }}</div>
                                        <div class="vn-shop-meta">ID #{{ $vendor->id }}</div>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <span class="fw-semibold text-dark">{{ $vendor->owner_name }}</span>
                            </td>
                            <td>
                                <div class="vn-contact-line">
                                    <i class="far fa-envelope"></i>
                                    <span title="{{ $vendor->email }}">{{ Str::limit($vendor->email, 22) }}</span>
                                </div>
                                <div class="vn-contact-line mb-0">
                                    <i class="fas fa-phone-alt"></i>
                                    <span>{{ $vendor->phone }}</span>
                                </div>
                            </td>
                            <td>
                                <span class="vn-pill vn-pill-products">
                                    <i class="fas fa-box-open"></i>
                                    {{ $vendor->products->count() }}
                                </span>
                            </td>
                            <td>
                                <span class="vn-balance">৳{{ number_format($vendor->wallet ? $vendor->wallet->balance : 0, 2) }}</span>
                            </td>
                            <td>
                                @if($vendor->verification_status == 'approved')
                                    <span class="vn-pill vn-badge-verified"><span class="vn-dot"></span> Verified</span>
                                @elseif($vendor->verification_status == 'rejected')
                                    <span class="vn-pill vn-badge-rejected"><span class="vn-dot"></span> Rejected</span>
                                @else
                                    <span class="vn-pill vn-badge-pending"><span class="vn-dot"></span> Pending</span>
                                @endif
                            </td>
                            <td>
                                @if($vendor->status == 1)
                                    <span class="vn-pill vn-badge-active">Active</span>
                                @else
                                    <span class="vn-pill vn-badge-inactive">Inactive</span>
                                @endif
                            </td>
                            <td>
                                <div class="vn-actions">
                                    <form method="post" action="{{ route('admin.vendors.toggle-status', $vendor->id) }}" class="d-inline">
                                        @csrf
                                        @if($vendor->status == 1)
                                            <button type="submit" class="vn-action-btn deactivate" title="নিষ্ক্রিয় করুন">
                                                <i class="fas fa-ban"></i>
                                            </button>
                                        @else
                                            <button type="submit" class="vn-action-btn activate" title="সক্রিয় করুন">
                                                <i class="fas fa-check"></i>
                                            </button>
                                        @endif
                                    </form>
                                    <a href="{{ route('admin.vendors.edit', $vendor->id) }}" class="vn-action-btn edit" title="সম্পাদনা">
                                        <i class="fas fa-pen"></i>
                                    </a>
                                    <form method="post" action="{{ route('admin.vendors.destroy', $vendor->id) }}" class="d-inline"
                                          onsubmit="return confirm('এই ভেন্ডর মুছে ফেলবেন? এটি ফিরিয়ে আনা যাবে না।');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="vn-action-btn delete" title="মুছুন">
                                            <i class="fas fa-trash-alt"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="9">
                                <div class="vn-empty">
                                    <div><i class="fas fa-store-slash"></i></div>
                                    <p class="mb-0 fw-semibold">কোনো ভেন্ডর পাওয়া যায়নি</p>
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

        @if($vendors->hasPages() || $vendors->total() > 0)
        <div class="vn-foot">
            <div class="vn-foot-meta">
                দেখানো হচ্ছে <strong>{{ $vendors->firstItem() ?? 0 }}</strong>–<strong>{{ $vendors->lastItem() ?? 0 }}</strong>
                / মোট <strong>{{ $vendors->total() }}</strong> ভেন্ডর
            </div>
            <div>{{ $vendors->links('pagination::bootstrap-4') }}</div>
        </div>
        @endif
    </div>
</div>
@endsection

@section('script')
<script>
    if (typeof feather !== 'undefined') { feather.replace(); }
</script>
@endsection

