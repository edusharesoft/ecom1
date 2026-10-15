@extends('backEnd.layouts.master')
@section('title','Product Management')

@section('css')
<meta name="csrf-token" content="{{ csrf_token() }}">
<style>
    /* শুধু এই পেজ — গ্লোবাল কার্ড/টেবিল ওভাররাইড নয় */
    .pm-manage {
        --pm-border: #e5e7eb;
        --pm-bg: #f8fafc;
        --pm-text: #111827;
        --pm-muted: #6b7280;
        --pm-accent: #2563eb;
        font-size: 14px;
        color: var(--pm-text);
        padding-bottom: 2rem;
    }
    .pm-manage .pm-shell {
        background: var(--pm-bg);
        margin: -0.75rem -12px 0;
        padding: 1.25rem 1rem 2rem;
        min-height: calc(100vh - 110px);
    }
    .pm-manage .pm-head {
        display: flex;
        flex-wrap: wrap;
        align-items: flex-start;
        justify-content: space-between;
        gap: 1rem;
        margin-bottom: 1.25rem;
        padding-bottom: 1rem;
        border-bottom: 1px solid var(--pm-border);
        background: transparent;
    }
    .pm-manage .pm-head h1 {
        font-size: 1.25rem;
        font-weight: 700;
        margin: 0 0 0.25rem;
        letter-spacing: -0.02em;
    }
    .pm-manage .pm-head p {
        margin: 0;
        font-size: 13px;
        color: var(--pm-muted);
    }
    .pm-manage .pm-actions {
        display: flex;
        flex-wrap: wrap;
        gap: 0.5rem;
        align-items: center;
    }
    .pm-manage .btn-pm {
        font-size: 13px;
        font-weight: 600;
        padding: 0.45rem 0.95rem;
        border-radius: 8px;
        border: 1px solid transparent;
        display: inline-flex;
        align-items: center;
        gap: 0.35rem;
    }
    .pm-manage .btn-pm-outline {
        background: #fff;
        border-color: var(--pm-border);
        color: #374151;
    }
    .pm-manage .btn-pm-outline:hover {
        border-color: #d1d5db;
        background: #fafafa;
        color: #111827;
    }
    .pm-manage .btn-pm-primary {
        background: var(--pm-accent);
        border-color: var(--pm-accent);
        color: #fff;
    }
    .pm-manage .btn-pm-primary:hover {
        background: #1d4ed8;
        border-color: #1d4ed8;
        color: #fff;
    }
    .pm-manage .btn-pm-warn {
        background: #fff;
        border-color: #fcd34d;
        color: #92400e;
    }
    .pm-manage .btn-pm-warn:hover {
        background: #fffbeb;
    }

    .pm-manage .pm-card {
        background: #fff;
        border: 1px solid var(--pm-border);
        border-radius: 12px;
        box-shadow: 0 1px 2px rgba(15, 23, 42, 0.04);
        overflow: hidden;
    }
    .pm-manage .pm-toolbar {
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        justify-content: space-between;
        gap: 1rem;
        padding: 1rem 1.25rem;
        border-bottom: 1px solid var(--pm-border);
        background: #fafafa;
    }
    .pm-manage .pm-bulk {
        display: flex;
        flex-wrap: wrap;
        gap: 0.5rem;
        align-items: center;
        list-style: none;
        padding: 0;
        margin: 0;
    }
    .pm-manage .pm-bulk .btn-pm {
        padding: 0.35rem 0.75rem;
        font-size: 12px;
        font-weight: 600;
    }
    .pm-manage .pm-search .input-group {
        max-width: 280px;
    }
    .pm-manage .pm-search .form-control {
        border-color: var(--pm-border);
        font-size: 13px;
        border-radius: 8px 0 0 8px;
    }
    .pm-manage .pm-search .btn {
        border-radius: 0 8px 8px 0;
        border-color: var(--pm-border);
        background: #fff;
        color: var(--pm-muted);
    }
    .pm-manage .pm-search .btn:hover {
        background: #f3f4f6;
        color: var(--pm-text);
    }

    .pm-manage .table-responsive {
        padding: 0 1rem 1rem;
    }
    .pm-manage .pm-table {
        margin-bottom: 0;
        font-size: 13px;
    }
    .pm-manage .pm-table thead th {
        font-size: 11px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.04em;
        color: var(--pm-muted);
        border-bottom: 1px solid var(--pm-border);
        background: #fff;
        padding: 12px 10px;
        white-space: nowrap;
        vertical-align: middle;
    }
    .pm-manage .pm-table tbody td {
        padding: 12px 10px;
        vertical-align: middle;
        border-color: #f3f4f6;
    }
    .pm-manage .pm-table tbody tr:hover {
        background: #fafafa;
    }

    .pm-manage .product-img {
        border-radius: 8px;
        object-fit: cover;
        border: 1px solid var(--pm-border);
    }

    .pm-manage .btn-action {
        width: 34px;
        height: 34px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border-radius: 8px;
        transition: background 0.15s, color 0.15s;
        border: 1px solid var(--pm-border);
        background: #fff;
        color: #4b5563;
    }
    .pm-manage .btn-edit {
        border-color: #bfdbfe;
        background: #eff6ff;
        color: var(--pm-accent);
    }
    .pm-manage .btn-edit:hover {
        background: var(--pm-accent);
        border-color: var(--pm-accent);
        color: #fff;
    }
    .pm-manage .btn-delete {
        border-color: #fecaca;
        background: #fef2f2;
        color: #dc2626;
    }
    .pm-manage .btn-delete:hover {
        background: #dc2626;
        border-color: #dc2626;
        color: #fff;
    }
    .pm-manage .btn-status-toggle {
        border-color: var(--pm-border);
        background: #fff;
    }
    .pm-manage .btn-status-toggle:hover {
        background: #f3f4f6;
    }

    .pm-manage .badge-soft-primary { background: #eff6ff; color: #1d4ed8; font-weight: 600; }
    .pm-manage .badge-soft-success { background: #ecfdf5; color: #047857; font-weight: 600; }
    .pm-manage .badge-soft-warning { background: #fffbeb; color: #b45309; font-weight: 600; }
    .pm-manage .badge-soft-danger { background: #fef2f2; color: #b91c1c; font-weight: 600; }
    .pm-manage .badge-soft-info { background: #ecfeff; color: #0e7490; font-weight: 600; }
    .pm-manage .badge-soft-secondary { background: #f3f4f6; color: #4b5563; font-weight: 600; }
    .pm-manage .badge-soft-light { background: #f9fafb; color: #6b7280; }

    .pm-manage .pm-foot {
        display: flex;
        flex-wrap: wrap;
        justify-content: space-between;
        align-items: center;
        gap: 1rem;
        padding: 1rem 1.25rem;
        border-top: 1px solid var(--pm-border);
        background: #fafafa;
        font-size: 13px;
        color: var(--pm-muted);
    }
</style>
@endsection

@section('content')
<div class="container-fluid pm-manage">
<div class="pm-shell">

    <header class="pm-head">
        <div>
            <h1>পণ্য ব্যবস্থাপনা</h1>
            <p>ভেন্ডর ও অ্যাডমিন পণ্য তালিকা — বাল্ক অ্যাকশন ও অনুসন্ধান।</p>
        </div>
        <div class="pm-actions">
            <a href="{{ route('products.pending') }}" class="btn btn-pm btn-pm-warn text-decoration-none">
                <i class="fe-clock"></i> অনির্ধারিত
            </a>
            <a href="{{ route('products.create') }}" class="btn btn-pm btn-pm-primary text-decoration-none">
                <i class="fe-plus"></i> নতুন পণ্য
            </a>
        </div>
    </header>

    <div class="pm-card">
        <div class="pm-toolbar">
            <ul class="pm-bulk">
                <li>
                    <button type="button" data-url="{{ route('products.update_deals') }}" data-status="1" class="btn btn-pm btn-pm-outline hotdeal_update">
                        <i class="fe-tag"></i> ডিল সেট
                    </button>
                </li>
                <li>
                    <button type="button" data-url="{{ route('products.update_deals') }}" data-status="0" class="btn btn-pm btn-pm-outline hotdeal_update">
                        <i class="fe-x-circle"></i> ডিল সরান
                    </button>
                </li>
                <li class="align-self-stretch border-start ps-3 ms-1 d-none d-md-block"></li>
                <li>
                    <button type="button" data-url="{{ route('products.update_status') }}" data-status="1" class="btn btn-pm btn-pm-primary update_status">
                        <i class="fe-check"></i> নির্বাচিত সক্রিয়
                    </button>
                </li>
                <li>
                    <button type="button" data-url="{{ route('products.update_status') }}" data-status="0" class="btn btn-pm btn-pm-outline update_status">
                        <i class="fe-x"></i> নির্বাচিত নিষ্ক্রিয়
                    </button>
                </li>
            </ul>
            <form method="GET" action="{{ route('products.index') }}" class="pm-search">
                <div class="input-group input-group-sm">
                    <input type="text" name="keyword" class="form-control" placeholder="নাম খুঁজুন…" value="{{ request('keyword') }}">
                    <button class="btn" type="submit" title="খুঁজুন"><i class="fe-search"></i></button>
                </div>
            </form>
        </div>

        <div class="table-responsive">
            <table class="table pm-table mb-0">
                <thead>
                    <tr>
                        <th style="width:42px;">
                            <div class="form-check mb-0">
                                <input type="checkbox" class="form-check-input checkall" id="parentCheck">
                            </div>
                        </th>
                        <th>#</th>
                        <th>ছবি</th>
                        <th style="min-width:200px;">পণ্যের নাম</th>
                        <th>ক্যাটাগরি / ভেন্ডর</th>
                        <th>মূল্য ও স্টক</th>
                        <th>ফিচার</th>
                        <th>স্ট্যাটাস</th>
                        <th>অনুমোদন</th>
                        <th class="text-center">অ্যাকশন</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($data as $value)
                    <tr>
                        <td>
                            <div class="form-check mb-0">
                                <input type="checkbox" class="form-check-input checkbox" value="{{ $value->id }}">
                            </div>
                        </td>
                        <td>{{ $loop->iteration }}</td>
                        <td>
                            <img src="{{ asset($value->image ? $value->image->image : 'storage/uploads/placeholder.png') }}"
                                 class="product-img" alt="" width="52" height="52">
                        </td>
                        <td>
                            <div class="fw-semibold text-dark">{{ Str::limit($value->name, 42) }}</div>
                            @php
                                $isDigital = (isset($value->is_digital) && $value->is_digital) || (isset($value->product_type) && $value->product_type === 'digital');
                            @endphp
                            <span class="badge {{ $isDigital ? 'badge-soft-primary' : 'badge-soft-info' }} mt-1" style="font-size:10px;">
                                {{ $isDigital ? 'ডিজিটাল' : 'ফিজিক্যাল' }}
                            </span>
                        </td>
                        <td>
                            <div class="text-muted small fw-semibold">{{ $value->category ? $value->category->name : 'ক্যাটাগরি নেই' }}</div>
                            <div class="text-primary small mt-1"><i class="fe-user"></i> {{ $value->vendor ? $value->vendor->shop_name : 'অ্যাডমিন' }}</div>
                        </td>
                        <td>
                            <div class="fw-semibold">৳{{ number_format($value->new_price, 2) }}</div>
                            <div class="small text-muted">স্টক: <span class="{{ $value->stock <= 5 ? 'text-danger fw-bold' : '' }}">{{ $value->stock }}</span></div>
                        </td>
                        <td>
                            <div class="d-flex flex-column gap-1">
                                <span class="badge {{ $value->topsale == 1 ? 'badge-soft-success' : 'badge-soft-light border' }}" style="font-size:10px;">
                                    হট ডিল: {{ $value->topsale == 1 ? 'হ্যাঁ' : 'না' }}
                                </span>
                                <span class="badge {{ $value->feature_product == 1 ? 'badge-soft-primary' : 'badge-soft-light border' }}" style="font-size:10px;">
                                    ফিচার: {{ $value->feature_product == 1 ? 'হ্যাঁ' : 'না' }}
                                </span>
                            </div>
                        </td>
                        <td>
                            @if($value->status == 1)
                                <span class="badge badge-soft-success">সক্রিয়</span>
                            @else
                                <span class="badge badge-soft-danger">নিষ্ক্রিয়</span>
                            @endif
                        </td>
                        <td>
                            @php
                                $app_class = [
                                    'approved' => 'badge-soft-success',
                                    'pending'  => 'badge-soft-warning',
                                    'rejected' => 'badge-soft-danger'
                                ][$value->approval_status] ?? 'badge-soft-secondary';
                            @endphp
                            <span class="badge {{ $app_class }} text-uppercase" style="font-size:10px;">
                                {{ $value->approval_status }}
                            </span>
                        </td>
                        <td>
                            <div class="d-flex justify-content-center gap-1 flex-wrap">
                                @if($value->status == 1)
                                    <form method="post" action="{{ route('products.inactive') }}" class="d-inline">
                                        @csrf
                                        <input type="hidden" value="{{ $value->id }}" name="hidden_id">
                                        <button type="submit" class="change-confirm btn-action btn-status-toggle" title="নিষ্ক্রিয়"><i class="fe-thumbs-down"></i></button>
                                    </form>
                                @else
                                    <form method="post" action="{{ route('products.active') }}" class="d-inline">
                                        @csrf
                                        <input type="hidden" value="{{ $value->id }}" name="hidden_id">
                                        <button type="submit" class="change-confirm btn-action btn-status-toggle text-success" title="সক্রিয়"><i class="fe-thumbs-up"></i></button>
                                    </form>
                                @endif

                                <a href="{{ route('products.edit', $value->id) }}" class="btn-action btn-edit" title="এডিট"><i class="fe-edit"></i></a>

                                <form method="post" action="{{ route('products.destroy') }}" class="d-inline">
                                    @csrf
                                    <input type="hidden" value="{{ $value->id }}" name="hidden_id">
                                    <button type="submit" class="delete-confirm btn-action btn-delete" title="মুছুন"><i class="fe-trash-2"></i></button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="10" class="text-center py-5 text-muted">
                            <i class="fe-package d-block mb-2" style="font-size:1.75rem;opacity:.5;"></i>
                            কোনো পণ্য পাওয়া যায়নি। অনুসন্ধান শর্ত বদলে দেখুন বা নতুন পণ্য যোগ করুন।
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="pm-foot">
            <span>
                @if($data->total() > 0)
                    {{ $data->firstItem() }} থেকে {{ $data->lastItem() }} — মোট {{ $data->total() }} টি
                @else
                    কোনো ফল নেই
                @endif
            </span>
            <div class="custom-paginate mb-0">
                {{ $data->links('pagination::bootstrap-4') }}
            </div>
        </div>
    </div>

</div>
</div>
@endsection

@section('script')
<script>
$(function(){
    $(".checkall").on('change', function(){
        $(".checkbox").prop('checked', $(this).is(":checked"));
    });

    function getCheckedIds() {
        return $('input.checkbox:checked').map(function(){ return $(this).val(); }).get();
    }

    function sendBulkRequest(url, status) {
        var ids = getCheckedIds();
        if(ids.length === 0){
            if (typeof toastr !== 'undefined') {
                toastr.error('কমপক্ষে একটি পণ্য নির্বাচন করুন।');
            } else {
                alert('কমপক্ষে একটি পণ্য নির্বাচন করুন।');
            }
            return;
        }

        var token = $('meta[name="csrf-token"]').attr('content');

        $.ajax({
            url: url,
            type: 'POST',
            data: JSON.stringify({ product_ids: ids, status: status }),
            contentType: 'application/json; charset=utf-8',
            dataType: 'json',
            headers: { 'X-CSRF-TOKEN': token },
            success: function(res){
                if(res.status === 'success'){
                    if (typeof toastr !== 'undefined') {
                        toastr.success(res.message);
                    }
                    setTimeout(function(){ location.reload(); }, 800);
                } else {
                    if (typeof toastr !== 'undefined') {
                        toastr.error(res.message || 'ব্যর্থ');
                    }
                }
            },
            error: function(xhr){
                var msg = (xhr.responseJSON && xhr.responseJSON.message) ? xhr.responseJSON.message : 'সার্ভার এরর';
                if (typeof toastr !== 'undefined') { toastr.error(msg); } else { alert(msg); }
            }
        });
    }

    $(document).on('click', '.hotdeal_update, .update_status', function(e){
        e.preventDefault();
        var url = $(this).data('url');
        var status = $(this).data('status');
        if(url) sendBulkRequest(url, status);
    });
});
</script>
@endsection
