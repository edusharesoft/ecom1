@extends('backEnd.layouts.master')
@section('title', $order_status->name . ' অর্ডার')

@section('css')
@include('backEnd.order.partials.index_styles')
@endsection

@section('content')
@include('backEnd.order.partials.index_header')
<tbody>
                                @foreach($show_data as $key => $value)
                                    <tr>
                                        <td><input type="checkbox" class="checkbox form-check-input" value="{{ $value->id }}"></td>
                                        <td>{{ $loop->iteration }}</td>
                                        <td class="oi-actions-cell">
                                            <div class="oi-row-actions">
                                                <button type="button"
                                                    class="oi-act-btn oi-act-view order-quick-view-btn"
                                                    data-order-id="{{ $value->id }}"
                                                    title="বিস্তারিত দেখুন"
                                                    aria-label="বিস্তারিত দেখুন">
                                                    <i class="fas fa-eye" aria-hidden="true"></i>
                                                    <span class="oi-act-label">ভিউ</span>
                                                </button>
                                                <form method="post" action="{{ route('admin.order.destroy') }}" class="oi-act-delete-form">
                                                    @csrf
                                                    <input type="hidden" value="{{ $value->id }}" name="id">
                                                    <button type="submit"
                                                        title="ডিলিট"
                                                        aria-label="অর্ডার ডিলিট"
                                                        class="oi-act-btn oi-act-delete delete-confirm">
                                                        <i class="fas fa-trash-alt" aria-hidden="true"></i>
                                                        <span class="oi-act-label">ডিলিট</span>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                        <td><a href="{{ route('admin.order.process', ['invoice_id' => $value->invoice_id]) }}" class="oi-invoice-link">#{{ $value->invoice_id }}</a></td>
                                        <td>
                                            {{ date('d-m-Y', strtotime($value->updated_at)) }}<br>
                                            {{ date('h:i:s a', strtotime($value->updated_at)) }}
                                        </td>
                                        <td>
                                            <strong>{{ $value->shipping ? $value->shipping->name : '' }}</strong>
                                            <p class="mb-0">{{ $value->shipping ? $value->shipping->phone : '' }}</p>
                                        </td>
                                        <td>
                                            @php
                                                $tsKey = strtolower(trim((string) ($value->traffic_source ?? 'direct')));
                                                $trafficOpts = isset($traffic_source_options) ? $traffic_source_options : [];
                                                $tsLabel = isset($trafficOpts[$tsKey]) ? $trafficOpts[$tsKey] : ucfirst($tsKey ?: 'direct');
                                                $tsBadgeClass = match ($tsKey) {
                                                    'facebook' => 'bg-primary',
                                                    'instagram' => 'bg-danger',
                                                    'google' => 'bg-success',
                                                    'tiktok' => 'bg-dark',
                                                    'youtube' => 'bg-danger',
                                                    'whatsapp' => 'bg-success',
                                                    'bing' => 'bg-info',
                                                    'yahoo' => 'bg-secondary',
                                                    'twitter' => 'bg-info',
                                                    'direct' => 'bg-secondary',
                                                    'other' => 'bg-warning',
                                                    default => 'bg-secondary',
                                                };
                                                $tsTitleTip = isset($value->traffic_referrer) ? trim((string) $value->traffic_referrer) : '';
                                            @endphp
                                            <span class="badge {{ $tsBadgeClass }}" @if($tsTitleTip !== '') title="{{ Str::limit($tsTitleTip, 240) }}" @endif>{{ Str::limit($tsLabel, 16) }}</span>
                                            @if($tsTitleTip !== '')
                                                <br><small class="text-muted" style="font-size: .68rem;">{{ Str::limit($tsTitleTip, 42) }}</small>
                                            @endif
                                        </td>

                                        {{-- Amount (show remaining if partial paid) --}}
                                        <td>
                                            @php
                                                $payment = \App\Models\Payment::where('order_id', $value->id)->first();
                                                $paid = $payment ? floatval($payment->amount) : 0;
                                                $total = floatval($value->amount);
                                                $showAmount = $total;
                                                if ($paid > 0 && $paid < $total) {
                                                    $showAmount = $total - $paid;
                                                }
                                            @endphp
                                            <span class="oi-amount">৳{{ number_format($showAmount, 2) }}</span>
                                        </td>

                                        <td><span class="oi-status-pill">{{ $value->status ? $value->status->name : '—' }}</span></td>

                                        <td>
                                            {{-- 
                                                LOGIC:
                                                - is_null() ব্যবহার করা হয়েছে কারণ 0 একটি ভ্যালিড রেট হতে পারে (ফ্রড)।
                                                - NULL হলে "যাচাই করুন" (হলুদ)।
                                                - অন্যথায় রেট দেখাবে (সবুজ/লাল)।
                                            --}}
                                            @if(is_null($value->fraud_rate))
                                                 <a href="javascript:void(0);" 
                                                class="btn btn-sm fraud-check"
                                                data-mobile="{{ $value->shipping ? $value->shipping->phone : '' }}"
                                                style="background:#fb8709; color:#fff; padding:5px 12px; border-radius:6px; font-size:13px;">
                                                চেকিং
                                            </a>
                                            @else
                                                <a href="javascript:void(0);" 
                                                   class="btn btn-sm fraud-check {{ $value->fraud_rate >= 80 ? 'btn-success' : 'btn-danger' }}"
                                                   data-mobile="{{ $value->shipping ? $value->shipping->phone : '' }}"
                                                   data-id="{{ $value->id }}"
                                                   style="padding:5px 12px; border-radius:6px; font-size:13px;">
                                                    {{ $value->fraud_rate }}% {{ $value->fraud_rate >= 80 ? 'নিরাপদ' : 'ঝুঁকি' }}
                                                </a>
                                            @endif
                                        </td>

                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div class="oi-paginate custom-paginate order-custom-paginate mt-3">
                        {{ $show_data->links('pagination::bootstrap-4') }}
                    </div>
        </div>
    </div>
</div>

@include('backEnd.order.partials.order_quick_view_modal')

<div class="modal fade oi-modal" id="asignUser" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-fullscreen-sm-down">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title"><i class="fas fa-user-plus me-1"></i> ইউজার অ্যাসাইন</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form action="{{ route('admin.order.assign') }}" id="order_assign">
        <div class="modal-body">
            <div class="form-group">
                <select name="user_id" id="user_id" class="form-control">
                    <option value="">Select..</option>
                    @foreach($users as $u)
                        <option value="{{ $u->id }}">{{ $u->name }}</option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            <button type="submit" class="btn btn-success">Submit</button>
        </div>
      </form>
    </div>
  </div>
</div>

<div class="modal fade oi-modal" id="changeStatus" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-fullscreen-sm-down">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title"><i class="fas fa-flag me-1"></i> স্ট্যাটাস পরিবর্তন</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form action="{{ route('admin.order.status') }}" id="order_status_form" novalidate>
        <div class="modal-body">
            <div class="form-group">
                <label class="form-label">Select Status <span class="text-danger">*</span></label>
                <select name="order_status" id="order_status" class="form-control">
                    <option value="">Select Status..</option>
                    @if(isset($orderstatus) && $orderstatus->count() > 0)
                        @foreach($orderstatus as $s)
                            <option value="{{ $s->id }}">{{ $s->name }}</option>
                        @endforeach
                    @else
                        <option value="">No status available</option>
                    @endif
                </select>
                <small class="text-muted">Select orders first, then choose status</small>
                <div class="invalid-feedback" id="status_error" style="display: none;">Please select a status</div>
            </div>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            <button type="submit" class="btn btn-success">Update Status</button>
        </div>
      </form>
    </div>
  </div>
</div>

<div class="modal fade oi-modal" id="pathao" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-fullscreen-sm-down modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title"><i class="fas fa-truck me-1"></i> Pathao কুরিয়ার</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form action="{{ route('admin.order.pathao') }}" id="order_sendto_pathao" method="POST">
      @csrf
      <input type="hidden" name="order_ids" id="pathao_order_ids" value="">
      <div class="modal-body">
        <div class="form-group">
            <label for="pathaostore" class="form-label">Store</label>
           <select name="pathaostore" id="pathaostore" class="pathaostore form-control" >
             <option value="">Select Store...</option>
             @if(isset($pathaostore['data']['data']))
                 @foreach($pathaostore['data']['data'] as $store)
                     <option value="{{ $store['store_id'] }}">{{ $store['store_name'] }}</option>
                 @endforeach
             @endif
           </select>
        </div>

        <div class="form-group mt-3">
          <label for="pathaocity" class="form-label">City</label>
           <select name="pathaocity" id="pathaocity" class="chosen-select pathaocity form-control" style="width:100%" >
             <option value="">Select City...</option>
             @if(isset($pathaocities['data']['data']))
                 @foreach($pathaocities['data']['data'] as $city)
                     <option value="{{ $city['city_id'] }}">{{ $city['city_name'] }}</option>
                 @endforeach
             @endif
           </select>
        </div>

        <div class="form-group mt-3">
          <label class="form-label">Zone</label>
             <select name="pathaozone" id="pathaozone" class="pathaozone chosen-select form-control" style="width:100%"></select>
        </div>

        <div class="form-group mt-3">
          <label class="form-label">Area</label>
             <select name="pathaoarea" id="pathaoarea" class="pathaoarea chosen-select form-control" style="width:100%"></select>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
        <button type="submit" class="btn btn-success">Submit</button>
      </div>
      </form>
    </div>
  </div>
</div>

<div class="modal fade oi-modal" id="noteModal" tabindex="-1" aria-labelledby="noteModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-fullscreen-sm-down">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title text-white" id="noteModalLabel">Note</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>

      <div class="modal-body">
        <input type="hidden" id="note_order_id">
        <input type="hidden" id="note_type">

        <div class="form-group">
            <label id="note_label">Note</label>
            <textarea id="note_modal_text" class="form-control" rows="5" placeholder="Write note here..."></textarea>
        </div>
      </div>

      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
        <button type="button" class="btn btn-success" id="saveNoteBtn">Save</button>
      </div>
    </div>
  </div>
</div>

<div class="modal fade" id="fraudCheckModal" tabindex="-1">
    <div class="modal-dialog modal-xl modal-dialog-scrollable modal-dialog-centered modal-fullscreen-md-down">
        <div class="modal-content" style="border-radius:12px;">
            <div class="modal-header" style="background:#10b981; color:#fff;">
                <h5 class="modal-title">
                    <i class="fe-shield"></i> ফ্রড চেকার রিপোর্ট
                </h5>
                <button type="button" class="btn-close btn-light" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body" id="fraudModalBody" style="min-height:250px;">
                <div class="text-center py-5">
                    <div class="spinner-border text-success" style="width:3rem;height:3rem;"></div>
                    <p class="mt-3 fw-bold">ডাটা লোড হচ্ছে...</p>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@section('script')
<script>
    // Safe number helper
    function toNum(v) {
        if (v === null || v === undefined || v === '') return 0;
        var n = Number(v);
        return isNaN(n) ? 0 : n;
    }

    // buildSummary: Updated to handle New API keys
    function buildSummary(raw) {
        var pathao = raw.pathao || raw.Pathao || raw.pathao_data || raw.pathao || {};
        var redx = raw.redx || raw.RedX || raw.redx_data || raw.redx || {};
        var steadfast = raw.steadfast || raw.Steadfast || raw.steadfast_data || raw.steadfast || {};
        var parceldex = raw.parceldex || raw.ParcelDex || {};
        var paperfly = raw.paperfly || raw.PaperFly || {};

        function getStats(obj) {
            var t = toNum(obj.total_parcel || obj.total || obj.orders || obj.count);
            var s = toNum(obj.success_parcel || obj.success || obj.complete || obj.delivered);
            var c = toNum(obj.cancelled_parcel || obj.cancel || obj.cancelled || obj.failed);
            var r = (obj.success_ratio !== undefined) ? toNum(obj.success_ratio) : (t > 0 ? Math.round((s / t) * 100) : 0);
            return { total: t, success: s, cancel: c, rate: r };
        }

        var p = getStats(pathao);
        var r = getStats(redx);
        var s = getStats(steadfast);
        var pd = getStats(parceldex);
        var pf = getStats(paperfly);

        var total = p.total + r.total + s.total + pd.total + pf.total;
        var success = p.success + r.success + s.success + pd.success + pf.success;
        var cancel = p.cancel + r.cancel + s.cancel + pd.cancel + pf.cancel;

        var rate = 0;
        if (total > 0) rate = Math.round((success / total) * 100);

        return {
            total: total,
            success: success,
            cancel: cancel,
            rate: rate,
            couriers: {
                Pathao: p,
                RedX: r,
                Steadfast: s,
                ParcelDex: pd,
                PaperFly: pf
            }
        };
    }

    // Render HTML for modal from canonical summary (IN BANGLA)
    function loadFraudHtml(data, mobile) {
        if (data.total === 0) {
            return `
            <div class="container-fluid">
                <div class="p-3 mb-3" style="background:#f8f9fa;border-radius:8px;">
                    <h5><i class="fe-phone-call"></i> ${mobile}</h5>
                    <small>সফলতার হার: 0%</small>
                    <span class="badge bg-secondary float-end">কোন তথ্য নেই</span>
                </div>
                <div class="alert alert-light text-center py-3" style="border:1px solid #ddd;">
                    <h5 class="text-muted mb-0">😕 কোনো তথ্য খুঁজে পাওয়া যায়নি</h5>
                    <small>এই কাস্টমারের সম্পর্কে কোনো তথ্য পাওয়া যায়নি। অতিরিক্ত সতর্কতার জন্য নিজের যাচাই করুন।</small>
                </div>
            </div>`;
        }

        var rateText = (data.rate || data.rate === 0) ? (data.rate + '%') : 'N/A';
        
        // Bangla Risk Tags
        var riskTag = '<span class="badge bg-success">নিরাপদ</span>';
        var showWarning = (data.total > 0 && data.rate < 80);
        if (showWarning) { riskTag = '<span class="badge bg-danger">উচ্চ ঝুঁকি</span>'; }

        var courierRows = '';
        Object.entries(data.couriers).forEach(function([name, c]) {
            if(c.total === 0) return;

            var cRateNum = toNum(c.rate);
            var cRate = (c.total === 0) ? 'N/A' : (cRateNum + '%');
            var badgeClass = 'bg-secondary';
            if (c.total === 0) { badgeClass = 'bg-secondary'; }
            else if (cRateNum >= 90) { badgeClass = 'bg-success'; }
            else if (cRateNum >= 70) { badgeClass = 'bg-warning text-dark'; }
            else { badgeClass = 'bg-danger'; }

            courierRows += `
                <tr>
                    <td>${name}</td>
                    <td>${c.total}</td>
                    <td class="text-success">${c.success}</td>
                    <td class="text-danger">${c.cancel}</td>
                    <td><span class="badge ${badgeClass}">${cRate}</span></td>
                </tr>`;
        });

        var warningHtml = '';
        if (showWarning) {
            warningHtml = `<div class="alert alert-danger text-center py-2">⚠️ সতর্কতা: ডেলিভারি হার কম - COD যাচাই করুন অথবা এডভান্স নিন</div>`;
        } else {
            warningHtml = `<div class="text-start mb-3"><small class="text-success">✓ নিরাপদ - কাস্টমারের ডেলিভারি রেকর্ড ভালো।</small></div>`;
        }

        return `
            <div class="container-fluid">
                <div class="p-3 mb-3" style="background:#e8fff3;border-radius:8px;">
                    <h5><i class="fe-phone-call"></i> ${mobile}</h5>
                    <small>সফলতার হার: ${rateText}</small>
                    <span class="float-end">${riskTag}</span>
                </div>
                ${warningHtml}
                <div class="row text-center mb-4">
                    <div class="col-md-3 mb-2">
                        <div class="p-3 text-white" style="background:#6366f1;border-radius:10px;">
                            <h3>${data.total}</h3><span>মোট পার্সেল</span>
                        </div>
                    </div>
                    <div class="col-md-3 mb-2">
                        <div class="p-3 text-white" style="background:#10b981;border-radius:10px;">
                            <h3>${data.success}</h3><span>ডেলিভারি</span>
                        </div>
                    </div>
                    <div class="col-md-3 mb-2">
                        <div class="p-3 text-white" style="background:#ef4444;border-radius:10px;">
                            <h3>${data.cancel}</h3><span>বাতিল/রিটার্ন</span>
                        </div>
                    </div>
                    <div class="col-md-3 mb-2">
                        <div class="p-3 text-white" style="background:#f97316;border-radius:10px;">
                            <h3>${rateText}</h3><span>হার</span>
                        </div>
                    </div>
                </div>
                <table class="table table-bordered">
                    <thead class="table-light">
                        <tr>
                            <th>কুরিয়ার</th><th>মোট</th><th>সফল</th><th>বাতিল</th><th>হার</th>
                        </tr>
                    </thead>
                    <tbody>
                        ${courierRows}
                    </tbody>
                </table>
            </div>
        `;
    }
</script>

<script>
(function ($) {
    if (!$) return;

$(document).ready(function(){

    // আটকে থাকা modal backdrop / scroll লক সরানো
    $('body').removeClass('modal-open').css({ overflow: '', paddingRight: '' });
    $('.modal-backdrop').remove();
    document.querySelectorAll('.modal.show').forEach(function (m) {
        m.classList.remove('show');
        m.style.display = '';
        m.setAttribute('aria-hidden', 'true');
    });

    // Order Note / Admin Note popup open
    $(document).on('click', '.note-modal-btn', function (e) {
        e.preventDefault();
        let orderId = $(this).data('id');
        let type    = $(this).data('type');
        let note    = $(this).data('note') || '';

        $('#note_order_id').val(orderId);
        $('#note_type').val(type);
        $('#note_modal_text').val(note);

        if (type === 'admin') {
            $('#noteModalLabel').text('Admin Note');
            $('#note_label').text('Admin Note');
        } else {
            $('#noteModalLabel').text('Order Note (Customer)');
            $('#note_label').text('Order Note (Customer)');
        }

        $('#noteModal').modal('show');
    });

    // Save Note (AJAX)
    $('#saveNoteBtn').on('click', function () {
        let orderId = $('#note_order_id').val();
        let type    = $('#note_type').val();
        let note    = $('#note_modal_text').val();

        $.ajax({
            url: "{{ route('admin.order.update_note') }}",
            type: "POST",
            data: {
                _token: "{{ csrf_token() }}",
                order_id: orderId,
                note_type: type,
                note: note
            },
            success: function (res) {
                if (res.status === 'success') {
                    toastr.success('Note updated successfully');
                    let selector = '.note-modal-btn[data-id="' + orderId + '"][data-type="' + type + '"]';
                    let $btn = $(selector);
                    $btn.data('note', note);
                    $btn.text(note ? 'View' : 'Add');
                    $('#noteModal').modal('hide');
                } else {
                    toastr.error(res.message || 'Update failed');
                }
            },
            error: function () {
                toastr.error('Something went wrong');
            }
        });
    });

    // checkall
    $(".checkall").on('change',function(){
      $(".checkbox").prop('checked',$(this).is(":checked"));
    });

    // ── অর্ডার কুইক ভিউ মডাল ──
    function oqvShowModal() {
        var el = document.getElementById('orderQuickViewModal');
        if (!el) return;
        $('.modal-backdrop').not('.oqv-temp-backdrop').remove();
        if (typeof bootstrap !== 'undefined' && bootstrap.Modal) {
            var inst = bootstrap.Modal.getOrCreateInstance(el);
            inst.show();
        } else if ($.fn.modal) {
            $(el).modal('show');
        }
    }

    $('#orderQuickViewModal').on('hidden.bs.modal', function () {
        $('body').removeClass('modal-open').css({ overflow: '', paddingRight: '' });
        $('.modal-backdrop').remove();
        $('#orderQuickViewBody').html(
            '<div class="oqv-loading"><div class="spinner-border text-primary" role="status"></div><p class="mt-3 mb-0">লোড হচ্ছে...</p></div>'
        );
    });

    function oqvUpdateFraudBadge(rate, isFraud) {
        var $box = $('#oqvFraudStatus');
        if (!$box.length) return;
        if (isFraud) {
            $box.html('<span class="badge bg-danger oqv-fraud-badge">ফ্রড (ঝুঁকি)</span>');
            return;
        }
        if (rate >= 80) {
            $box.html('<span class="badge bg-success oqv-fraud-badge">' + rate + '% নিরাপদ</span>');
        } else {
            $box.html('<span class="badge bg-danger oqv-fraud-badge">' + rate + '% ঝুঁকি</span>');
        }
    }

    $(document).on('click', '.order-quick-view-btn', function () {
        var orderId = $(this).data('order-id');
        if (!orderId) return;
        $('#orderQuickViewBody').html(
            '<div class="oqv-loading"><div class="spinner-border text-primary"></div><p class="mt-3 mb-0">লোড হচ্ছে...</p></div>'
        );
        $('#oqvModalInvoice').text('');
        oqvShowModal();
        $.get('{{ url("admin/order/quick-view") }}/' + orderId, function (res) {
            if (res.status === 'success') {
                $('#orderQuickViewBody').html(res.html);
                $('#oqvModalInvoice').text('#' + res.invoice_id);
            } else {
                $('#orderQuickViewBody').html('<div class="alert alert-danger">ডাটা লোড করা যায়নি</div>');
            }
        }).fail(function () {
            $('#orderQuickViewBody').html('<div class="alert alert-danger">সার্ভার এরর — আবার চেষ্টা করুন</div>');
        });
    });

    $(document).on('click', '.fraud-check-oqv', function (e) {
        e.preventDefault();
        e.stopImmediatePropagation();
        var mobile = $(this).data('mobile');
        if (!mobile) { toastr.error('মোবাইল নম্বর নেই'); return; }
        var $btn = $(this);
        var $report = $('#oqvFraudReport');
        $btn.prop('disabled', true);
        $('#oqvFraudStatus').html('<span class="badge bg-secondary">চেক হচ্ছে...</span>');
        $report.html(
            '<div class="text-center py-4">' +
            '<div class="spinner-border text-primary" role="status"></div>' +
            '<p class="mt-2 mb-0 small text-muted">কুরিয়ার ডাটা যাচাই হচ্ছে...</p></div>'
        ).show();
        $.ajax({
            url: "{{ route('admin.fraud.check') }}",
            type: 'POST',
            data: { mobile: mobile, _token: "{{ csrf_token() }}" },
            timeout: 60000,
            success: function (res) {
                $btn.prop('disabled', false);
                if (res && res.status === 'success') {
                    if (res.data && res.data.is_fraud === true) {
                        oqvUpdateFraudBadge(0, true);
                        $report.html(
                            '<div class="alert alert-danger mb-0 text-center">' +
                            '<h6 class="mb-1"><i class="fas fa-exclamation-triangle"></i> ফ্রড ডিটেক্টেড</h6>' +
                            '<p class="mb-0 small">মোবাইল: ' + mobile + '</p></div>'
                        ).show();
                        $('.fraud-check[data-mobile="' + mobile + '"]').not('.fraud-check-oqv')
                            .removeClass('btn-warning btn-success').addClass('btn-danger').text('ফ্রড (ঝুঁকি)');
                        toastr.warning('ফ্রড ডিটেক্টেড');
                        return;
                    }
                    var apiData = (res.data && res.data.data) ? res.data.data : (res.data || {});
                    var summary = buildSummary(apiData);
                    oqvUpdateFraudBadge(summary.rate, false);
                    $report.html(loadFraudHtml(summary, mobile)).show();
                    var allBtns = $('.fraud-check[data-mobile="' + mobile + '"]').not('.fraud-check-oqv');
                    allBtns.removeClass('btn-warning btn-success btn-danger');
                    if (summary.rate >= 80) {
                        allBtns.addClass('btn-success').text(summary.rate + '% নিরাপদ');
                    } else {
                        allBtns.addClass('btn-danger').text(summary.rate + '% ঝুঁকি');
                    }
                    var $mb = $('#orderQuickViewBody');
                    if ($report.length && $mb.length) {
                        $mb.animate({
                            scrollTop: $report.offset().top - $mb.offset().top + $mb.scrollTop() - 16
                        }, 300);
                    }
                    toastr.success('ফ্রড চেক সম্পন্ন');
                } else {
                    $report.html('<div class="alert alert-danger mb-0">' + ((res && res.message) ? res.message : 'ফ্রড চেক ব্যর্থ') + '</div>').show();
                    toastr.error((res && res.message) ? res.message : 'ফ্রড চেক ব্যর্থ');
                }
            },
            error: function (xhr) {
                $btn.prop('disabled', false);
                var msg = 'ফ্রড চেক ব্যর্থ';
                if (xhr.responseJSON && xhr.responseJSON.message) msg = xhr.responseJSON.message;
                $report.html('<div class="alert alert-danger mb-0">' + msg + '</div>').show();
                toastr.error(msg);
            }
        });
    });

    $(document).on('click', '.oi-quick-courier', function () {
        var slug = $(this).data('courier');
        var orderId = $(this).data('order-id');
        var $btn = $(this);
        if (!orderId || !slug) return;
        if (!confirm('এই অর্ডার ' + slug.toUpperCase() + ' কুরিয়ারে পাঠাতে চান?')) return;
        $btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i>');
        $.get('{{ url("admin/bulk-courier") }}/' + slug + '?status=5', { order_ids: [orderId] }, function (res) {
            if (res.status === 'success') {
                toastr.success(res.message || 'কুরিয়ারে পাঠানো হয়েছে');
                setTimeout(function () { location.reload(); }, 1200);
            } else {
                toastr.error(res.message || 'ব্যর্থ');
                $btn.prop('disabled', false).html('<i class="fas fa-truck"></i> ' + slug.charAt(0).toUpperCase() + slug.slice(1));
            }
        }).fail(function () {
            toastr.error('কুরিয়ার রিকোয়েস্ট ব্যর্থ');
            $btn.prop('disabled', false);
        });
    });

    $(document).on('click', '.oi-quick-pathao', function () {
        var orderId = $(this).data('order-id');
        $('#pathao_order_ids').val(orderId);
        var qvEl = document.getElementById('orderQuickViewModal');
        if (typeof bootstrap !== 'undefined' && bootstrap.Modal) {
            bootstrap.Modal.getOrCreateInstance(qvEl).hide();
        } else {
            $(qvEl).modal('hide');
        }
        $('#pathao').modal('show');
    });

    // Fraud check → Popup Modal Open
    $(document).on('click', '.fraud-check', function(e){
        e.preventDefault();
        let mobile  = $(this).data('mobile');
        
        if (!mobile) { return toastr.error("No mobile number found"); }

        $("#fraudModalBody").html(`
            <div class="text-center py-5">
                <div class="spinner-border text-success" style="width:3rem;height:3rem;"></div>
                <p class="mt-3 fw-bold">তথ্য যাচাই করা হচ্ছে...</p>
            </div>
        `);

        $("#fraudCheckModal").modal("show");

        $.ajax({
            url: "{{ route('admin.fraud.check') }}",
            type: "POST",
            data: { 
                mobile: mobile,
                // আমরা এখানে order_id পাঠাচ্ছি না, কারণ কন্ট্রোলার মোবাইল নম্বর দিয়ে 
                // সব অর্ডার আপডেট করবে।
                _token: "{{ csrf_token() }}" 
            },
            timeout: 60000, // 60 seconds timeout
            beforeSend: function() {
                // Show loading state
                $("#fraudModalBody").html(`
                    <div class="text-center p-5">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                        <p class="mt-3">ফ্রড চেক করা হচ্ছে... অনুগ্রহ করে অপেক্ষা করুন।</p>
                    </div>
                `);
            },
            success: function(res) {
                
                if (res && res.status === "success") {
                    let apiData = {};
                    
                    if(res.data && res.data.data) {
                        apiData = res.data.data;
                    } else if (res.data) {
                        apiData = res.data;
                    }

                    // এখন আমরা পেইজে থাকা ওই মোবাইল নাম্বারের *সব বাটন* খুঁজে বের করব
                    let allBtns = $('.fraud-check[data-mobile="'+mobile+'"]');

                    if(res.data && res.data.is_fraud === true) {
                         $("#fraudModalBody").html(`
                            <div class="alert alert-danger text-center p-5">
                                <h3>⚠️ ফ্রড ডিটেক্টেড!</h3>
                                <p>এই নাম্বারটি ফ্রড তালিকায় রয়েছে।</p>
                            </div>
                         `);
                         
                         // সব বাটন লাল করে দেওয়া
                         allBtns.removeClass('btn-warning text-dark btn-success').addClass('btn-danger').text('ফ্রড (ঝুঁকি)');
                         return;
                    }

                    // Build Summary
                    var summary = buildSummary(apiData);
                    $("#fraudModalBody").html(loadFraudHtml(summary, mobile));

                    // ==========================================
                    // INSTANT BUTTON UPDATE LOGIC (ALL BUTTONS)
                    // ==========================================
                    let r = summary.rate;
                    
                    // আগের ক্লাস রিমুভ
                    allBtns.removeClass('btn-warning text-dark btn-success btn-danger');

                    if(r >= 80) {
                        // Safe
                        allBtns.addClass('btn-success');
                        allBtns.text(r + '% নিরাপদ');
                    } else {
                        // Risk
                        allBtns.addClass('btn-danger');
                        allBtns.text(r + '% ঝুঁকি');
                    }

                    toastr.success('স্ট্যাটাস সফলভাবে সেভ হয়েছে!');

                } else {
                    var msg = (res && res.message) ? res.message : 'No data returned';
                    $("#fraudModalBody").html(`<div class="alert alert-danger text-center p-4">${msg}</div>`);
                }
            },

            error: function(xhr, status, error) {
                console.error('Fraud Check AJAX Error:', {
                    status: status,
                    error: error,
                    response: xhr.responseJSON,
                    statusCode: xhr.status
                });
                
                let errorMessage = 'অনুগ্রহ করে আবার চেষ্টা করুন।';
                
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    errorMessage = xhr.responseJSON.message;
                } else if (status === 'timeout') {
                    errorMessage = 'Request timeout! API server response নেওয়া যায়নি। অনুগ্রহ করে আবার চেষ্টা করুন।';
                } else if (status === 'error') {
                    errorMessage = 'Connection error! API server-এ connection করতে পারছে না।';
                } else if (xhr.status === 400) {
                    errorMessage = 'Invalid request! দয়া করে মোবাইল নাম্বার চেক করুন।';
                } else if (xhr.status === 500) {
                    errorMessage = 'Server error! দয়া করে admin-কে জানান।';
                } else if (xhr.status === 404) {
                    errorMessage = 'API endpoint not found!';
                }
                
                $("#fraudModalBody").html(`
                    <div class="alert alert-danger text-center p-4">
                        <h5>❌ Error!</h5>
                        <p>${errorMessage}</p>
                        ${xhr.responseJSON && xhr.responseJSON.message ? `<small>${xhr.responseJSON.message}</small>` : ''}
                    </div>
                `);
                
                // Reset button to original state
                let allBtns = $('.fraud-check[data-mobile="'+mobile+'"]');
                allBtns.removeClass('btn-success btn-danger').addClass('btn-warning').text('চেকিং');
                
                toastr.error('Fraud check failed: ' + errorMessage);
            }
        });
    });

    // order assign
    $(document).on('submit', 'form#order_assign', function(e){
        e.preventDefault();
        var url = $(this).attr('action');
        let user_id = $('#user_id').val();

        var order = $('input.checkbox:checked').map(function(){
          return $(this).val();
        });
        var order_ids = order.get();

        if(order_ids.length == 0){
            toastr.error('Please Select An Order First !');
            return;
        }

        $.ajax({
           type: 'GET',
           url: url,
           data: { user_id: user_id, order_ids: order_ids },
           success: function(res){
               if(res.status == 'success'){
                   toastr.success(res.message);
                   window.location.reload();
               } else {
                   toastr.error(res.message || 'Failed something wrong');
               }
           },
           error: function(){
               toastr.error('Something went wrong');
           }
        });
    });

    // order status change
    $(document).on('submit', 'form#order_status_form', function(e){
        e.preventDefault();
        e.stopPropagation();
        e.stopImmediatePropagation();
        
        var url = $(this).attr('action');
        let order_status = $('#order_status').val();
        var $statusSelect = $('#order_status');
        var $statusError = $('#status_error');
        
        // Clear any previous validation state
        $statusSelect.removeClass('is-invalid is-valid');
        $statusError.hide();

        var order = $('input.checkbox:checked').map(function(){
          return $(this).val();
        });
        var order_ids = order.get();

        // Validate orders selected FIRST
        if(order_ids.length == 0){
            toastr.error('Please Select An Order First !');
            return false;
        }
        
        // Validate status selected - check multiple conditions
        var statusValue = String(order_status || '').trim();
        if(!statusValue || statusValue === '' || statusValue === 'null' || statusValue === 'undefined' || statusValue === '0'){
            $statusSelect.addClass('is-invalid');
            $statusError.text('Please select a status').show();
            toastr.error('Please Select A Status First !');
            // Focus on select field and scroll to it
            $statusSelect.focus();
            $('html, body').animate({
                scrollTop: $statusSelect.offset().top - 100
            }, 300);
            return false;
        }
        
        // Additional check - make sure it's a valid number
        if(isNaN(parseInt(statusValue)) || parseInt(statusValue) <= 0){
            $statusSelect.addClass('is-invalid');
            $statusError.text('Please select a valid status').show();
            toastr.error('Please Select A Valid Status !');
            $statusSelect.focus();
            return false;
        }

        // Show loading
        var $form = $(this);
        var $submitBtn = $form.find('button[type="submit"]');
        var originalHtml = $submitBtn.html();
        $submitBtn.prop('disabled', true).html('<i class="fe-loader"></i> Updating...');

        $.ajax({
           type: 'GET',
           url: url,
           data: { order_status: order_status, order_ids: order_ids },
           success: function(res){
               if(res.status == 'success'){
                   toastr.success(res.message);
                   $('#changeStatus').modal('hide');
                   setTimeout(function(){
                       window.location.reload();
                   }, 1000);
               } else {
                   toastr.error(res.message || 'Failed something wrong');
                   $submitBtn.prop('disabled', false).html(originalHtml);
               }
           },
           error: function(xhr){
               console.error('Status update error:', xhr);
               var errorMsg = 'Something went wrong';
               
               // Handle Laravel validation errors
               if(xhr.status === 422 && xhr.responseJSON && xhr.responseJSON.errors){
                   var errors = xhr.responseJSON.errors;
                   if(errors.order_status){
                       $statusSelect.addClass('is-invalid');
                       $statusError.text(errors.order_status[0]).show();
                       errorMsg = errors.order_status[0];
                   } else if(errors.order_ids){
                       errorMsg = errors.order_ids[0];
                   }
               } else if(xhr.responseJSON && xhr.responseJSON.message){
                   errorMsg = xhr.responseJSON.message;
               } else if(xhr.status === 400){
                   errorMsg = 'Bad request. Please check your selection.';
               }
               
               toastr.error(errorMsg);
               $submitBtn.prop('disabled', false).html(originalHtml);
           }
        });
        
        return false;
    });

    // order delete (bulk)
    $(document).on('click', '.order_delete', function(e){
        e.preventDefault();
        var url = $(this).attr('href');
        var order = $('input.checkbox:checked').map(function(){
          return $(this).val();
        });
        var order_ids = order.get();

        if(order_ids.length == 0){
            toastr.error('Please Select An Order First !');
            return;
        }

        $.ajax({
           type: 'GET',
           url: url,
           data: { order_ids: order_ids },
           success: function(res){
               if(res.status == 'success'){
                   toastr.success(res.message);
                   window.location.reload();
               } else {
                   toastr.error(res.message || 'Failed something wrong');
               }
           },
           error: function(){
               toastr.error('Something went wrong');
           }
        });
    });

    // multiple print
    $(document).on('click', '.multi_order_print', function(e){
        e.preventDefault();
        var url = $(this).attr('href');
        var order = $('input.checkbox:checked').map(function(){
          return $(this).val();
        });
        var order_ids = order.get();

        if(order_ids.length == 0){
            toastr.error('Please Select Atleast One Order!');
            return;
        }
        $.ajax({
           type: 'GET',
           url: url,
           data: { order_ids: order_ids },
           success: function(res){
               if(res.status == 'success'){
                   var myWindow = window.open("", "_blank");
                   myWindow.document.write(res.view);
               } else {
                   toastr.error(res.message || 'Failed something wrong');
               }
           },
           error: function(){
               toastr.error('Something went wrong');
           }
        });
    });

    // label print
    $(document).on('click', '.multi_label_print', function(e){
        e.preventDefault();
        var order_ids = $('input.checkbox:checked').map(function(){ return $(this).val(); }).get();
        if(order_ids.length == 0){ toastr.error('Please Select Atleast One Order!'); return; }
        $.ajax({
            type: 'GET',
            url: $(this).attr('href'),
            data: { order_ids: order_ids, type: 'label' },
            success: function(res){
                if(res.status == 'success'){
                    var w = window.open("","_blank");
                    w.document.write(res.view);
                } else { toastr.error(res.message || 'Failed'); }
            },
            error: function(){ toastr.error('Something went wrong'); }
        });
    });

    // multiple courier
    $(document).on('click', '.multi_order_courier', function(e){
        e.preventDefault();
        var url = $(this).attr('href');
        var order = $('input.checkbox:checked').map(function(){
          return $(this).val();
        });
        var order_ids = order.get();

        if(order_ids.length == 0){
            toastr.error('Please Select An Order First !');
            return;
        }
        
        // Show loading
        var $btn = $(this);
        var originalHtml = $btn.html();
        $btn.prop('disabled', true).html('<i class="fe-loader"></i> Sending...');

        $.ajax({
           type: 'GET',
           url: url,
           data: { order_ids: order_ids },
           success: function(res){
               console.log('Courier Response:', res); // Debug log
               
               if(res.status == 'success'){
                    if(res.success && res.success.length > 0){
                        toastr.success('Orders sent to courier successfully!');
                    }
                    if(res.failed && res.failed.length > 0){
                        res.failed.forEach(function(fail){
                            console.error('Failed order:', fail);
                            toastr.warning('Order ' + fail.order_id + ': ' + fail.message);
                        });
                    }
                    // Reload page to show courier information
                    setTimeout(function(){
                        window.location.reload();
                    }, 1000);
               } else {
                    toastr.error(res.message || 'Failed something wrong');
                    $btn.prop('disabled', false).html(originalHtml);
               }
           },
           error: function(xhr){
               console.error('Courier Error:', xhr);
               var errorMsg = 'Something went wrong';
               
               if(xhr.responseJSON){
                   // Check for failed orders with detailed messages
                   if(xhr.responseJSON.failed && xhr.responseJSON.failed.length > 0){
                       xhr.responseJSON.failed.forEach(function(fail){
                           var msg = fail.message || 'Failed to send order';
                           if(fail.status_code === 401){
                               msg = 'Account is not active! Please check your Steadfast account status and API credentials.';
                           } else if(fail.status_code === 403){
                               msg = 'Access forbidden! Please check your API credentials.';
                           } else if(fail.status_code === 404){
                               msg = 'API endpoint not found! Please check the API URL.';
                           }
                           toastr.error('Order ' + fail.order_id + ': ' + msg);
                       });
                   } else if(xhr.responseJSON.message){
                       errorMsg = xhr.responseJSON.message;
                   }
               } else if(xhr.status === 401){
                   errorMsg = 'Account is not active! Please check your Steadfast account status and API credentials.';
               } else if(xhr.status === 403){
                   errorMsg = 'Access forbidden! Please check your API credentials.';
               } else if(xhr.status === 404){
                   errorMsg = 'API endpoint not found! Please check the API URL.';
               }
               
               toastr.error(errorMsg);
               $btn.prop('disabled', false).html(originalHtml);
           }
        });
    });

    // Quick IP Block from order page
    $(document).on('click', '.block-ip-btn', function(e){
        e.preventDefault();
        var $btn = $(this);
        var ip = $btn.data('ip');
        var reason = $btn.data('reason') || 'ফেইক অর্ডার';
        
        if(!ip){
            toastr.error('IP address not found');
            return;
        }
        
        // Disable button and show loading
        $btn.prop('disabled', true);
        var originalHtml = $btn.html();
        $btn.html('<i class="fe-loader"></i> Blocking...');
        
        $.ajax({
            url: "{{ route('customers.ipblock.quick') }}",
            type: "POST",
            data: {
                _token: "{{ csrf_token() }}",
                ip: ip,
                reason: reason
            },
            success: function(res){
                if(res.status === 'success'){
                    toastr.success(res.message || 'IP blocked successfully');
                    // Change button to show blocked state (badge style)
                    $btn.replaceWith('<span class="badge bg-secondary" title="This IP is already blocked"><i class="fe-shield"></i> Blocked</span>');
                } else {
                    toastr.error(res.message || 'Failed to block IP');
                    $btn.prop('disabled', false);
                    $btn.html(originalHtml);
                }
            },
            error: function(xhr){
                var errorMsg = 'Failed to block IP';
                if(xhr.responseJSON && xhr.responseJSON.message){
                    errorMsg = xhr.responseJSON.message;
                }
                toastr.error(errorMsg);
                $btn.prop('disabled', false);
                $btn.html(originalHtml);
            }
        });
    });

    // Pathao Modal Open - Set selected order IDs
    $(document).on('click', '[data-bs-target="#pathao"]', function(e){
        var order = $('input.checkbox:checked').map(function(){
            return $(this).val();
        });
        var order_ids = order.get();
        
        if(order_ids.length == 0){
            toastr.error('Please Select Atleast One Order First!');
            e.preventDefault();
            return false;
        }
        
        $('#pathao_order_ids').val(order_ids.join(','));
    });

    // Pathao City Change - Load Zones
    $(document).on('change', '#pathaocity', function(){
        var cityId = $(this).val();
        if(!cityId){
            $('#pathaozone').html('<option value="">Select Zone...</option>');
            $('#pathaoarea').html('<option value="">Select Area...</option>');
            return;
        }
        
        $.ajax({
            url: "{{ route('pathaocity') }}",
            type: "GET",
            data: { city_id: cityId },
            success: function(res){
                var options = '<option value="">Select Zone...</option>';
                if(res && res.data && res.data.data && res.data.data.length > 0){
                    $.each(res.data.data, function(key, zone){
                        options += '<option value="' + zone.zone_id + '">' + zone.zone_name + '</option>';
                    });
                } else {
                    toastr.warning('No zones found for this city');
                }
                $('#pathaozone').html(options);
                $('#pathaoarea').html('<option value="">Select Area...</option>');
            },
            error: function(xhr){
                var errorMsg = 'Failed to load zones';
                if(xhr.responseJSON && xhr.responseJSON.message){
                    errorMsg = xhr.responseJSON.message;
                }
                toastr.error(errorMsg);
                $('#pathaozone').html('<option value="">Select Zone...</option>');
                $('#pathaoarea').html('<option value="">Select Area...</option>');
            }
        });
    });

    // Pathao Zone Change - Load Areas
    $(document).on('change', '#pathaozone', function(){
        var zoneId = $(this).val();
        if(!zoneId){
            $('#pathaoarea').html('<option value="">Select Area...</option>');
            return;
        }
        
        $.ajax({
            url: "{{ route('pathaozone') }}",
            type: "GET",
            data: { zone_id: zoneId },
            success: function(res){
                var options = '<option value="">Select Area...</option>';
                if(res && res.data && res.data.data && res.data.data.length > 0){
                    $.each(res.data.data, function(key, area){
                        options += '<option value="' + area.area_id + '">' + area.area_name + '</option>';
                    });
                } else {
                    toastr.warning('No areas found for this zone');
                }
                $('#pathaoarea').html(options);
            },
            error: function(xhr){
                var errorMsg = 'Failed to load areas';
                if(xhr.responseJSON && xhr.responseJSON.message){
                    errorMsg = xhr.responseJSON.message;
                }
                toastr.error(errorMsg);
                $('#pathaoarea').html('<option value="">Select Area...</option>');
            }
        });
    });

    // Pathao Form Submit
    $(document).on('submit', '#order_sendto_pathao', function(e){
        e.preventDefault();
        
        var orderIds = $('#pathao_order_ids').val();
        if(!orderIds){
            toastr.error('Please select orders first');
            return;
        }
        
        var formData = $(this).serialize();
        formData += '&order_ids=' + orderIds.split(',').map(function(id){ return id.trim(); }).join(',');
        
        // Validate required fields
        if(!$('#pathaostore').val() || !$('#pathaocity').val() || !$('#pathaozone').val() || !$('#pathaoarea').val()){
            toastr.error('Please fill all required fields (Store, City, Zone, Area)');
            return;
        }
        
        $.ajax({
            url: $(this).attr('action'),
            type: "POST",
            data: formData,
            success: function(res){
                if(res.status === 'success'){
                    var successCount = res.result.success ? res.result.success.length : 0;
                    var failedCount = res.result.failed ? res.result.failed.length : 0;
                    
                    if(successCount > 0){
                        toastr.success(successCount + ' order(s) sent to Pathao successfully');
                    }
                    if(failedCount > 0){
                        toastr.warning(failedCount + ' order(s) failed to send');
                    }
                    
                    $('#pathao').modal('hide');
                    setTimeout(function(){
                        window.location.reload();
                    }, 1500);
                } else {
                    toastr.error(res.message || 'Failed to send orders');
                }
            },
            error: function(xhr){
                var errorMsg = 'Failed to send orders';
                if(xhr.responseJSON && xhr.responseJSON.message){
                    errorMsg = xhr.responseJSON.message;
                }
                toastr.error(errorMsg);
            }
        });
    });

});

})(window.jQuery);
</script>
@endsection