@extends('backEnd.layouts.master')
@section('title', 'অর্ডার স্ট্যাটাস')

@section('css')
<link href="{{ asset('/public/backEnd/') }}/assets/libs/datatables.net-bs5/css/dataTables.bootstrap5.min.css" rel="stylesheet" type="text/css" />
<link href="{{ asset('/public/backEnd/') }}/assets/libs/datatables.net-responsive-bs5/css/responsive.bootstrap5.min.css" rel="stylesheet" type="text/css" />
@include('backEnd.orderstatus.partials.os_styles')
@endsection

@section('content')
<div class="container-fluid order-status-shell order-status-page">

    <div class="os-page-header">
        <div>
            <h4>অর্ডার স্ট্যাটাস <span class="os-badge-count">{{ $data->count() }}</span></h4>
            <p class="os-sub">অর্ডার ট্র্যাকিংয়ের জন্য স্ট্যাটাস পরিচালনা (যেমন: পেন্ডিং, শিপড, ডেলিভার্ড)</p>
        </div>
        <a href="{{ route('orderstatus.create') }}" class="os-btn-primary">
            <i class="fas fa-plus"></i> নতুন স্ট্যাটাস
        </a>
    </div>

    <div class="os-card">
        <div class="os-card-head">
            <span class="os-card-icon"><i class="fas fa-flag"></i></span>
            <h6>স্ট্যাটাস তালিকা</h6>
        </div>
        <div class="os-card-body os-dt-wrap">
            <div class="os-table-rail">
                <table id="os-datatable" class="table os-table mb-0 w-100">
                    <thead>
                        <tr>
                            <th style="width: 50px;">#</th>
                            <th>স্ট্যাটাস নাম</th>
                            <th>অবস্থা</th>
                            <th class="text-end" style="width: 150px;">অ্যাকশন</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($data as $value)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>
                                <span class="os-status-name">
                                    <i class="fas fa-circle"></i>
                                    {{ $value->name }}
                                </span>
                            </td>
                            <td>
                                @if($value->status == 1)
                                    <span class="os-pill-active">সক্রিয়</span>
                                @else
                                    <span class="os-pill-inactive">নিষ্ক্রিয়</span>
                                @endif
                            </td>
                            <td class="text-end">
                                <div class="os-row-actions">
                                    @if($value->status == 1)
                                        <form method="post" action="{{ route('orderstatus.inactive') }}" class="d-inline">
                                            @csrf
                                            <input type="hidden" value="{{ $value->id }}" name="hidden_id">
                                            <button type="submit" class="os-act-btn os-act-toggle-on" title="নিষ্ক্রিয় করুন">
                                                <i class="fas fa-eye-slash"></i>
                                            </button>
                                        </form>
                                    @else
                                        <form method="post" action="{{ route('orderstatus.active') }}" class="d-inline">
                                            @csrf
                                            <input type="hidden" value="{{ $value->id }}" name="hidden_id">
                                            <button type="submit" class="os-act-btn os-act-toggle-off" title="সক্রিয় করুন">
                                                <i class="fas fa-eye"></i>
                                            </button>
                                        </form>
                                    @endif

                                    <a href="{{ route('orderstatus.edit', $value->id) }}" class="os-act-btn os-act-edit" title="সম্পাদনা">
                                        <i class="fas fa-pen"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4">
                                <div class="os-empty">
                                    <i class="fas fa-flag"></i>
                                    <p class="mb-0">কোনো স্ট্যাটাস নেই</p>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

@section('script')
<script src="{{ asset('/public/backEnd/') }}/assets/libs/datatables.net/js/jquery.dataTables.min.js"></script>
<script src="{{ asset('/public/backEnd/') }}/assets/libs/datatables.net-bs5/js/dataTables.bootstrap5.min.js"></script>
<script src="{{ asset('/public/backEnd/') }}/assets/libs/datatables.net-responsive/js/dataTables.responsive.min.js"></script>
<script src="{{ asset('/public/backEnd/') }}/assets/libs/datatables.net-responsive-bs5/js/responsive.bootstrap5.min.js"></script>
<script>
$(function () {
    if ($.fn.DataTable && $('#os-datatable tbody tr').length > 0 && !$('#os-datatable tbody tr td[colspan]').length) {
        $('#os-datatable').DataTable({
            responsive: true,
            pageLength: 25,
            order: [[0, 'asc']],
            language: {
                search: 'খুঁজুন:',
                lengthMenu: '_MENU_ টি দেখান',
                info: '_TOTAL_ এর মধ্যে _START_–_END_',
                paginate: { previous: '‹', next: '›' },
                emptyTable: 'কোনো ডেটা নেই',
                zeroRecords: 'মিল পাওয়া যায়নি'
            },
            columnDefs: [{ orderable: false, targets: -1 }]
        });
    }
});
</script>
@endsection
