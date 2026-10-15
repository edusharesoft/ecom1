@extends('backEnd.layouts.master')
@section('title', 'ক্রিয়েট পেজ ব্যবস্থাপনা')

@section('css')
<link href="{{asset('/public/backEnd/')}}/assets/libs/datatables.net-bs5/css/dataTables.bootstrap5.min.css" rel="stylesheet" type="text/css" />
<link href="{{asset('/public/backEnd/')}}/assets/libs/datatables.net-responsive-bs5/css/responsive.bootstrap5.min.css" rel="stylesheet" type="text/css" />
<link href="{{asset('/public/backEnd/')}}/assets/libs/datatables.net-buttons-bs5/css/buttons.bootstrap5.min.css" rel="stylesheet" type="text/css" />

<style>
    /* শুধু এই পেজ */
    .cp-manage {
        --cp-border: #e5e7eb;
        --cp-bg: #f8fafc;
        --cp-text: #111827;
        --cp-muted: #6b7280;
        --cp-accent: #2563eb;
        font-size: 14px;
        color: var(--cp-text);
        padding-bottom: 2rem;
    }
    .cp-manage .cp-shell {
        background: var(--cp-bg);
        margin: -0.75rem -12px 0;
        padding: 1.25rem 1rem 2rem;
        min-height: calc(100vh - 110px);
    }
    .cp-manage .cp-head {
        display: flex;
        flex-wrap: wrap;
        align-items: flex-start;
        justify-content: space-between;
        gap: 1rem;
        margin-bottom: 1.25rem;
        padding-bottom: 1rem;
        border-bottom: 1px solid var(--cp-border);
    }
    .cp-manage .cp-head h1 {
        font-size: 1.25rem;
        font-weight: 700;
        margin: 0 0 0.25rem;
        letter-spacing: -0.02em;
    }
    .cp-manage .cp-head p {
        margin: 0;
        font-size: 13px;
        color: var(--cp-muted);
    }
    .cp-manage .btn-cp-primary {
        font-size: 13px;
        font-weight: 600;
        padding: 0.5rem 1rem;
        border-radius: 8px;
        background: var(--cp-accent);
        border: 1px solid var(--cp-accent);
        color: #fff;
        display: inline-flex;
        align-items: center;
        gap: 0.4rem;
    }
    .cp-manage .btn-cp-primary:hover {
        background: #1d4ed8;
        border-color: #1d4ed8;
        color: #fff;
    }

    .cp-manage .cp-card {
        background: #fff;
        border: 1px solid var(--cp-border);
        border-radius: 12px;
        box-shadow: 0 1px 2px rgba(15, 23, 42, 0.04);
        overflow: hidden;
    }
    .cp-manage .cp-card-inner {
        padding: 1rem 1.25rem 1.25rem;
    }

    .cp-manage #datatable-buttons thead th {
        font-size: 11px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.04em;
        color: var(--cp-muted);
        border-bottom: 1px solid var(--cp-border);
        padding: 12px 10px;
        background: #fff;
        white-space: nowrap;
    }
    .cp-manage #datatable-buttons tbody td {
        vertical-align: middle;
        padding: 12px 10px;
        font-size: 13px;
        border-color: #f3f4f6;
    }
    .cp-manage #datatable-buttons tbody tr:hover {
        background: #fafafa;
    }

    .cp-manage .cp-slug {
        font-size: 12px;
        color: var(--cp-muted);
    }
    .cp-manage .cp-page-icon {
        width: 40px;
        height: 40px;
        flex-shrink: 0;
        background: #f3f4f6;
        border: 1px solid var(--cp-border);
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #4b5563;
    }

    .cp-manage .cp-badge-active {
        display: inline-flex;
        align-items: center;
        gap: 4px;
        font-size: 11px;
        font-weight: 600;
        padding: 4px 10px;
        border-radius: 6px;
        background: #ecfdf5;
        color: #047857;
        border: 1px solid #a7f3d0;
    }
    .cp-manage .cp-badge-inactive {
        display: inline-flex;
        align-items: center;
        gap: 4px;
        font-size: 11px;
        font-weight: 600;
        padding: 4px 10px;
        border-radius: 6px;
        background: #fef2f2;
        color: #b91c1c;
        border: 1px solid #fecaca;
    }

    .cp-manage .btn-cp-act {
        width: 34px;
        height: 34px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border-radius: 8px;
        border: 1px solid var(--cp-border);
        text-decoration: none;
        transition: background 0.15s, color 0.15s, border-color 0.15s;
    }
    .cp-manage .btn-cp-act.edit {
        background: #eff6ff;
        border-color: #bfdbfe;
        color: var(--cp-accent);
    }
    .cp-manage .btn-cp-act.edit:hover {
        background: var(--cp-accent);
        border-color: var(--cp-accent);
        color: #fff;
    }
    .cp-manage .btn-cp-act.del {
        background: #fef2f2;
        border-color: #fecaca;
        color: #dc2626;
    }
    .cp-manage .btn-cp-act.del:hover {
        background: #dc2626;
        border-color: #dc2626;
        color: #fff;
    }

    /* DataTables টুলবার — হালকা মাটি টোন */
    .cp-manage .dataTables_wrapper .dataTables_length,
    .cp-manage .dataTables_wrapper .dataTables_filter label {
        font-size: 13px;
        color: var(--cp-muted);
    }
    .cp-manage .dataTables_wrapper .dataTables_filter input {
        border: 1px solid var(--cp-border);
        border-radius: 8px;
        padding: 0.35rem 0.65rem;
        font-size: 13px;
    }
    .cp-manage .dataTables_wrapper .dt-buttons .btn-light {
        background: #fff;
        border: 1px solid var(--cp-border);
        color: #374151;
        font-size: 12px;
        border-radius: 8px;
    }
    .cp-manage .dataTables_wrapper .dt-buttons .btn-light:hover {
        background: #f9fafb;
    }
</style>
@endsection

@section('content')
<div class="container-fluid cp-manage">
<div class="cp-shell">

    <header class="cp-head">
        <div>
            <h1>ক্রিয়েট পেজ ব্যবস্থাপনা</h1>
            <p>ডায়নামিক কন্টেন্ট পেজ তালিকা — এডিট, মুছে ফেলা ও এক্সপোর্ট (কপি / প্রিন্ট / PDF)।</p>
        </div>
        <div>
            <a href="{{ route('pages.create') }}" class="btn btn-cp-primary text-decoration-none">
                <i class="fe-plus"></i> নতুন পেজ
            </a>
        </div>
    </header>

    <div class="cp-card">
        <div class="cp-card-inner">
            <div class="table-responsive">
                <table id="datatable-buttons" class="table align-middle mb-0 w-100">
                    <thead>
                        <tr>
                            <th style="width:5%;">ক্রম</th>
                            <th style="width:35%;">পেজের নাম</th>
                            <th style="width:30%;">শিরোনাম</th>
                            <th style="width:15%;">স্ট্যাটাস</th>
                            <th style="width:15%;" class="text-end">অ্যাকশন</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($show_data as $value)
                        <tr>
                            <td><span class="text-muted fw-semibold">#{{ $loop->iteration }}</span></td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="cp-page-icon me-2">
                                        <i class="fe-file-text"></i>
                                    </div>
                                    <div>
                                        <span class="fw-semibold text-dark d-block">{{ $value->name }}</span>
                                        <span class="cp-slug">Slug: /{{ $value->slug ?? 'n-a' }}</span>
                                    </div>
                                </div>
                            </td>
                            <td>{{ $value->title }}</td>
                            <td>
                                @if($value->status == 1)
                                    <span class="cp-badge-active"><i class="fe-check-circle"></i> সক্রিয়</span>
                                @else
                                    <span class="cp-badge-inactive"><i class="fe-slash"></i> নিষ্ক্রিয়</span>
                                @endif
                            </td>
                            <td class="text-end">
                                <div class="d-flex justify-content-end align-items-center gap-1 flex-wrap">
                                    <a href="{{ route('pages.edit', $value->id) }}" class="btn-cp-act edit shadow-none" title="এডিট">
                                        <i class="fe-edit-2"></i>
                                    </a>
                                    <form method="post" action="{{ route('pages.destroy') }}" class="d-inline">
                                        @csrf
                                        <input type="hidden" value="{{ $value->id }}" name="hidden_id">
                                        <button type="submit" class="btn-cp-act del delete-confirm border-0 shadow-none" title="মুছুন">
                                            <i class="fe-trash-2"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="text-center py-5 text-muted">
                                <i class="fe-file-plus d-block mb-2" style="font-size:1.75rem;opacity:.45;"></i>
                                কোনো পেজ নেই। <a href="{{ route('pages.create') }}">প্রথম পেজ তৈরি করুন</a>।
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</div>
</div>
@endsection

@section('script')
<script src="{{asset('/public/backEnd/')}}/assets/libs/datatables.net/js/jquery.dataTables.min.js"></script>
<script src="{{asset('/public/backEnd/')}}/assets/libs/datatables.net-bs5/js/dataTables.bootstrap5.min.js"></script>
<script src="{{asset('/public/backEnd/')}}/assets/libs/datatables.net-responsive/js/dataTables.responsive.min.js"></script>
<script src="{{asset('/public/backEnd/')}}/assets/libs/datatables.net-responsive-bs5/js/responsive.bootstrap5.min.js"></script>
<script src="{{asset('/public/backEnd/')}}/assets/libs/datatables.net-buttons/js/dataTables.buttons.min.js"></script>
<script src="{{asset('/public/backEnd/')}}/assets/libs/datatables.net-buttons-bs5/js/buttons.bootstrap5.min.js"></script>
<script src="{{asset('/public/backEnd/')}}/assets/libs/datatables.net-buttons/js/buttons.html5.min.js"></script>
<script src="{{asset('/public/backEnd/')}}/assets/libs/datatables.net-buttons/js/buttons.print.min.js"></script>
<script src="{{asset('/public/backEnd/')}}/assets/js/pages/datatables.init.js"></script>
@endsection
