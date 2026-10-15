@extends('backEnd.layouts.master')
@section('title', 'আইপি ব্লক')

@section('css')
<link href="{{ asset('/public/backEnd/') }}/assets/libs/datatables.net-bs5/css/dataTables.bootstrap5.min.css" rel="stylesheet" type="text/css" />
<link href="{{ asset('/public/backEnd/') }}/assets/libs/datatables.net-responsive-bs5/css/responsive.bootstrap5.min.css" rel="stylesheet" type="text/css" />
@include('backEnd.reports.partials.ipblock_styles')
@endsection

@section('content')
<div class="container-fluid ip-block-shell ip-block-page">

    <div class="ipb-page-header">
        <h4>আইপি ব্লক <span class="ipb-badge-count">{{ $data->count() }}</span></h4>
        <p class="ipb-sub">নির্দিষ্ট আইপি ঠিকানা থেকে ওয়েবসাইটে প্রবেশ বন্ধ করুন</p>
    </div>

    <div class="row ipb-layout g-3">
        <div class="col-lg-4">
            <div class="ipb-card">
                <div class="ipb-card-head">
                    <span class="ipb-card-icon"><i class="fas fa-ban"></i></span>
                    <h6>নতুন আইপি ব্লক করুন</h6>
                </div>
                <div class="ipb-card-body">
                    <form action="{{ route('customers.ipblock.store') }}" method="POST" data-parsley-validate>
                        @csrf
                        <div class="mb-3">
                            <label for="ip_no" class="ipb-label">আইপি ঠিকানা <span class="text-danger">*</span></label>
                            <input type="text" class="ipb-input @error('ip_no') is-invalid @enderror"
                                   name="ip_no" value="{{ old('ip_no', $prefillIp ?? '') }}" id="ip_no"
                                   placeholder="যেমন: 192.168.0.1" required>
                            @error('ip_no')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label for="reason" class="ipb-label">কারণ <span class="text-danger">*</span></label>
                            <textarea class="ipb-textarea @error('reason') is-invalid @enderror"
                                      name="reason" id="reason"
                                      placeholder="কেন এই আইপি ব্লক করা হচ্ছে?" required>{{ old('reason', $prefillReason ?? '') }}</textarea>
                            @error('reason')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                        <button type="submit" class="btn ipb-btn-block">
                            <i class="fas fa-lock me-1"></i> আইপি ব্লক করুন
                        </button>
                    </form>
                </div>
            </div>

            <div class="ipb-tip">
                <i class="fas fa-exclamation-triangle"></i>
                <span>ব্লক করা আইপি থেকে সাইটে প্রবেশ করা যাবে না। সাবধানে ব্যবহার করুন।</span>
            </div>
        </div>

        <div class="col-lg-8">
            <div class="ipb-card">
                <div class="ipb-card-head">
                    <span class="ipb-card-icon" style="background: linear-gradient(135deg, #e0e7ff, #c7d2fe); color: #4338ca;">
                        <i class="fas fa-list"></i>
                    </span>
                    <h6>ব্লক করা আইপি তালিকা</h6>
                </div>
                <div class="ipb-card-body ipb-dt-wrap">
                    <div class="ipb-table-rail">
                        <table id="ipb-datatable" class="table ipb-table mb-0 w-100">
                            <thead>
                                <tr>
                                    <th style="width: 50px;">#</th>
                                    <th>আইপি</th>
                                    <th>কারণ</th>
                                    <th class="text-end" style="width: 110px;">অ্যাকশন</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($data as $value)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>
                                        <span class="ipb-ip-badge">
                                            <i class="fas fa-network-wired"></i>
                                            {{ $value->ip_no }}
                                        </span>
                                    </td>
                                    <td><span class="ipb-reason">{{ $value->reason }}</span></td>
                                    <td class="text-end">
                                        <div class="ipb-row-actions">
                                            <a href="javascript:void(0);" class="ipb-act-btn ipb-act-edit"
                                               data-bs-toggle="modal" data-bs-target="#ipEdit{{ $value->id }}" title="সম্পাদনা">
                                                <i class="fas fa-pen"></i>
                                            </a>
                                            <form method="post" action="{{ route('customers.ipblock.destroy') }}" class="d-inline">
                                                @csrf
                                                <input type="hidden" value="{{ $value->id }}" name="id">
                                                <button type="submit" class="ipb-act-btn ipb-act-delete delete-confirm" title="মুছুন">
                                                    <i class="fas fa-trash-alt"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="4">
                                        <div class="ipb-empty">
                                            <i class="fas fa-shield-alt"></i>
                                            <p class="mb-0">কোনো ব্লক করা আইপি নেই</p>
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
    </div>
</div>

@foreach($data as $value)
<div class="modal fade ipb-modal" id="ipEdit{{ $value->id }}" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">আইপি ব্লক সম্পাদনা</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="বন্ধ"></button>
            </div>
            <div class="modal-body">
                <form action="{{ route('customers.ipblock.update') }}" method="POST" data-parsley-validate>
                    @csrf
                    <input type="hidden" name="id" value="{{ $value->id }}">

                    <div class="mb-3">
                        <label class="ipb-label">আইপি ঠিকানা <span class="text-danger">*</span></label>
                        <input type="text" class="ipb-input" name="ip_no" value="{{ $value->ip_no }}" required>
                    </div>

                    <div class="mb-4">
                        <label class="ipb-label">কারণ <span class="text-danger">*</span></label>
                        <textarea class="ipb-textarea" name="reason" required>{{ $value->reason }}</textarea>
                    </div>

                    <button type="submit" class="btn ipb-btn-save">
                        <i class="fas fa-save me-1"></i> আপডেট করুন
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endforeach

@endsection

@section('script')
<script src="{{ asset('/public/backEnd/') }}/assets/libs/datatables.net/js/jquery.dataTables.min.js"></script>
<script src="{{ asset('/public/backEnd/') }}/assets/libs/datatables.net-bs5/js/dataTables.bootstrap5.min.js"></script>
<script src="{{ asset('/public/backEnd/') }}/assets/libs/datatables.net-responsive/js/dataTables.responsive.min.js"></script>
<script src="{{ asset('/public/backEnd/') }}/assets/libs/datatables.net-responsive-bs5/js/responsive.bootstrap5.min.js"></script>
<script src="{{ asset('public/backEnd/') }}/assets/libs/parsleyjs/parsley.min.js"></script>
<script src="{{ asset('public/backEnd/') }}/assets/js/pages/form-validation.init.js"></script>
<script>
$(function () {
    if ($.fn.DataTable && $('#ipb-datatable tbody tr').length > 0 && !$('#ipb-datatable tbody tr td[colspan]').length) {
        $('#ipb-datatable').DataTable({
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
