@if($payment && \App\Models\ManualPaymentGateway::isManualPaymentMethod((string) $payment->payment_method))
<div class="alert alert-info border-info mb-3 mt-2" role="alert">
    <div class="d-flex justify-content-between flex-wrap gap-2 align-items-start mb-2">
        <strong><i class="fa fa-exchange-alt"></i> ম্যানুয়াল গেটওয়ে</strong>
        <span class="badge bg-secondary">Trx/নম্বর নিচে দেখে নিজে খাতায় মিলিয়ে <strong>Paid</strong> সিলেক্ট করুন — আবার লিখতে হবে না</span>
    </div>
    <div class="row small mb-0">
        <div class="col-md-6 mb-2">
            <strong>কাস্টমারের Trx ID:</strong>
            <code class="user-select-all d-block mt-1 p-2 bg-light rounded">{{ $payment->trx_id ?: '—' }}</code>
        </div>
        <div class="col-md-6 mb-2">
            <strong>কাস্টমারের প্রেরক নম্বর:</strong>
            <code class="user-select-all d-block mt-1 p-2 bg-light rounded">{{ $payment->sender_number ?: '(দেয়নি)' }}</code>
        </div>
        @php($expect = ($payment->manual_payable_snapshot !== null && $payment->manual_payable_snapshot > 0) ? $payment->manual_payable_snapshot : null)
        <div class="col-12">
            <strong>এই চেকআউটের পরিমাণ (Paid হলে সেট হবে):</strong>
            <span class="text-success fw-bold">৳{{ number_format((float) ($expect ?: 0), 2) }}</span>
            @if(!$expect)<span class="text-muted ms-1">(স্ন্যাপশট নেই — অর্ডার অনুযায়ী টাকা ধরা হবে)</span>@endif
        </div>
    </div>
</div>
@endif
