@extends('backEnd.layouts.master')
@section('title', 'SMS Gateway')

@section('css')
<style>
    @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap');

    .sms-pro {
        --sms-bg: #f4f6f9;
        --sms-surface: #ffffff;
        --sms-border: #e2e8f0;
        --sms-border-strong: #cbd5e1;
        --sms-text: #0f172a;
        --sms-muted: #64748b;
        --sms-accent: #2563eb;
        --sms-accent-hover: #1d4ed8;
        --sms-accent-soft: rgba(37, 99, 235, 0.08);
        --sms-success: #059669;
        --sms-success-soft: #ecfdf5;
        --sms-danger: #dc2626;
        --sms-radius: 12px;
        --sms-radius-sm: 8px;
        --sms-shadow: 0 1px 3px rgba(15, 23, 42, 0.06), 0 1px 2px rgba(15, 23, 42, 0.04);
        --sms-shadow-md: 0 4px 6px -1px rgba(15, 23, 42, 0.07), 0 2px 4px -2px rgba(15, 23, 42, 0.05);

        font-family: 'Inter', system-ui, -apple-system, Segoe UI, sans-serif;
        font-size: 14px;
        line-height: 1.5;
        color: var(--sms-text);
        background: var(--sms-bg);
        margin: -12px -15px 0;
        padding: 24px 20px 40px;
        min-height: calc(100vh - 120px);
        letter-spacing: -0.011em;
    }

    .sms-pro-inner {
        max-width: 1120px;
        margin: 0 auto;
    }

    /* Page intro */
    .sms-pro-intro {
        display: flex;
        flex-wrap: wrap;
        align-items: flex-start;
        justify-content: space-between;
        gap: 16px;
        margin-bottom: 24px;
    }
    .sms-pro-intro-main {
        flex: 1;
        min-width: 260px;
    }
    .sms-pro-kicker {
        font-size: 11px;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.06em;
        color: var(--sms-accent);
        margin: 0 0 6px;
    }
    .sms-pro-title {
        font-size: 1.5rem;
        font-weight: 700;
        margin: 0 0 8px;
        color: var(--sms-text);
        line-height: 1.25;
    }
    .sms-pro-desc {
        margin: 0;
        font-size: 14px;
        color: var(--sms-muted);
        max-width: 620px;
        line-height: 1.55;
    }
    .sms-pro-desc code {
        font-size: 12px;
        font-family: ui-monospace, SFMono-Regular, Menlo, monospace;
        background: var(--sms-surface);
        border: 1px solid var(--sms-border);
        padding: 2px 7px;
        border-radius: 6px;
        color: #334155;
    }

    /* Balance */
    .sms-pro-balance {
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        justify-content: space-between;
        gap: 16px;
        background: var(--sms-surface);
        border: 1px solid var(--sms-border);
        border-radius: var(--sms-radius);
        padding: 18px 22px;
        margin-bottom: 24px;
        box-shadow: var(--sms-shadow);
    }
    .sms-pro-balance-left {
        display: flex;
        align-items: center;
        gap: 14px;
    }
    .sms-pro-balance-icon {
        width: 48px;
        height: 48px;
        border-radius: var(--sms-radius-sm);
        background: var(--sms-accent-soft);
        color: var(--sms-accent);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.15rem;
    }
    .sms-pro-balance .sms-bl-label {
        font-size: 11px;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        color: var(--sms-muted);
        margin-bottom: 2px;
    }
    .sms-pro-balance .bal-num {
        font-size: 1.5rem;
        font-weight: 700;
        font-variant-numeric: tabular-nums;
        letter-spacing: -0.02em;
        color: var(--sms-text);
        line-height: 1.2;
    }
    .sms-pro-balance .bal-num small {
        font-size: 15px;
        font-weight: 500;
        color: var(--sms-muted);
    }
    .sms-pro-balance .bal-meta {
        font-size: 13px;
        color: var(--sms-muted);
        margin-top: 4px;
    }
    .sms-pro-balance .bal-meta.ok { color: var(--sms-success); font-weight: 500; }
    .sms-pro-balance .bal-meta.err { color: var(--sms-danger); font-weight: 500; }

    /* Cards */
    .sms-pro-card {
        background: var(--sms-surface);
        border: 1px solid var(--sms-border);
        border-radius: var(--sms-radius);
        margin-bottom: 20px;
        box-shadow: var(--sms-shadow);
        overflow: hidden;
        transition: box-shadow 0.2s ease;
    }
    .sms-pro-card:hover {
        box-shadow: var(--sms-shadow-md);
    }
    .sms-pro-card__head {
        padding: 14px 20px;
        border-bottom: 1px solid var(--sms-border);
        font-weight: 600;
        font-size: 14px;
        color: var(--sms-text);
        display: flex;
        align-items: center;
        gap: 10px;
        background: linear-gradient(to bottom, #fafbfc 0%, #f8fafc 100%);
    }
    .sms-pro-card__head i {
        color: var(--sms-accent);
        font-size: 15px;
        opacity: 0.9;
    }
    .sms-pro-card__head-sub {
        font-weight: 400;
        font-size: 12px;
        color: var(--sms-muted);
        margin-left: auto;
    }
    .sms-pro-card__body {
        padding: 20px;
    }

    /* Form */
    .sms-pro-grid2 {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 18px;
    }
    @media (max-width: 767px) {
        .sms-pro-grid2 { grid-template-columns: 1fr; }
    }
    .sms-pro-lbl {
        display: block;
        font-size: 13px;
        font-weight: 600;
        color: #334155;
        margin-bottom: 6px;
    }
    .sms-pro-hint {
        font-size: 12px;
        color: var(--sms-muted);
        margin-top: 6px;
        font-family: ui-monospace, monospace;
    }
    .sms-pro-inp {
        width: 100%;
        padding: 10px 12px;
        border: 1px solid var(--sms-border-strong);
        border-radius: var(--sms-radius-sm);
        font-size: 14px;
        background: var(--sms-surface);
        color: var(--sms-text);
        transition: border-color 0.15s, box-shadow 0.15s;
    }
    .sms-pro-inp:hover {
        border-color: #94a3b8;
    }
    .sms-pro-inp:focus {
        outline: none;
        border-color: var(--sms-accent);
        box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.15);
    }

    /* Buttons */
    .sms-pro-btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        padding: 10px 18px;
        font-size: 14px;
        font-weight: 600;
        border-radius: var(--sms-radius-sm);
        cursor: pointer;
        border: 1px solid transparent;
        transition: background 0.15s, border-color 0.15s, color 0.15s, box-shadow 0.15s;
        font-family: inherit;
    }
    .sms-pro-btn--secondary {
        background: var(--sms-surface);
        border-color: var(--sms-border-strong);
        color: #334155;
    }
    .sms-pro-btn--secondary:hover {
        background: #f8fafc;
        border-color: #94a3b8;
    }
    .sms-pro-btn--primary {
        background: var(--sms-accent);
        border-color: var(--sms-accent);
        color: #fff;
        box-shadow: 0 1px 2px rgba(37, 99, 235, 0.25);
    }
    .sms-pro-btn--primary:hover {
        background: var(--sms-accent-hover);
        border-color: var(--sms-accent-hover);
    }
    .sms-pro-btn--success {
        background: var(--sms-success);
        border-color: var(--sms-success);
        color: #fff;
        box-shadow: 0 1px 2px rgba(5, 150, 105, 0.2);
    }
    .sms-pro-btn--success:hover {
        background: #047857;
        border-color: #047857;
    }
    .sms-pro-btn.loading {
        opacity: 0.65;
        pointer-events: none;
    }

    /* Toggles */
    .sms-pro-toggle-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 12px;
    }
    @media (max-width: 767px) {
        .sms-pro-toggle-grid { grid-template-columns: 1fr; }
    }
    .sms-pro-toggle {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
        padding: 14px 16px;
        border: 1px solid var(--sms-border);
        border-radius: var(--sms-radius-sm);
        background: #fafbfc;
        margin-bottom: 0;
        transition: border-color 0.15s, background 0.15s;
    }
    .sms-pro-toggle:hover {
        border-color: #cbd5e1;
    }
    .sms-pro-toggle.on {
        background: var(--sms-success-soft);
        border-color: #a7f3d0;
    }
    .sms-pro-toggle .t-title {
        font-size: 14px;
        font-weight: 600;
        margin: 0 0 4px;
        color: var(--sms-text);
        display: flex;
        align-items: center;
        flex-wrap: wrap;
        gap: 6px;
    }
    .sms-pro-toggle .t-desc {
        font-size: 12px;
        color: var(--sms-muted);
        margin: 0;
        line-height: 1.4;
    }
    .sms-pro-badge {
        font-size: 10px;
        font-weight: 700;
        letter-spacing: 0.04em;
        padding: 3px 8px;
        border-radius: 6px;
        background: #e2e8f0;
        color: #475569;
    }
    .sms-pro-toggle.on .sms-pro-badge {
        background: #d1fae5;
        color: #047857;
    }

    .sms-tog {
        position: relative;
        width: 48px;
        height: 26px;
        flex-shrink: 0;
        cursor: pointer;
        display: inline-block;
    }
    .sms-tog input { opacity: 0; width: 0; height: 0; position: absolute; }
    .sms-tog span {
        position: absolute;
        inset: 0;
        border-radius: 26px;
        background: #cbd5e1;
        transition: background 0.2s ease;
    }
    .sms-tog span::before {
        content: '';
        position: absolute;
        width: 22px;
        height: 22px;
        left: 2px;
        top: 2px;
        background: #fff;
        border-radius: 50%;
        transition: transform 0.2s ease;
        box-shadow: 0 1px 3px rgba(15, 23, 42, 0.2);
    }
    .sms-tog input:checked + span {
        background: var(--sms-accent);
    }
    .sms-tog input:checked + span::before {
        transform: translateX(22px);
    }

    /* Gateway master toggle row */
    .sms-pro-toggle-master {
        margin-bottom: 16px;
        padding: 16px 18px;
        background: var(--sms-surface);
        border: 1px solid var(--sms-border-strong);
    }
    .sms-pro-toggle-master.on {
        border-color: var(--sms-accent);
        background: var(--sms-accent-soft);
    }
    .sms-pro-toggle-master.on .sms-pro-badge {
        background: rgba(37, 99, 235, 0.15);
        color: var(--sms-accent-hover);
    }

    /* Preview */
    .sms-pro-preview {
        font-family: ui-monospace, SFMono-Regular, Menlo, Consolas, monospace;
        font-size: 12px;
        line-height: 1.65;
        background: #0f172a;
        color: #e2e8f0;
        border-radius: var(--sms-radius-sm);
        padding: 16px;
        overflow-x: auto;
        white-space: pre-wrap;
        word-break: break-all;
        border: 1px solid #1e293b;
    }
    .sms-pro-preview .ck-m {
        font-weight: 700;
        color: #38bdf8;
    }
    .sms-pro-preview-foot {
        margin: 12px 0 0;
        font-size: 12px;
        color: var(--sms-muted);
        line-height: 1.45;
    }

    .sms-pro-steps {
        font-size: 14px;
        color: #334155;
        line-height: 1.85;
        padding-left: 20px;
        margin: 0;
    }
    .sms-pro-steps a {
        color: var(--sms-accent);
        font-weight: 500;
        text-decoration: none;
    }
    .sms-pro-steps a:hover {
        text-decoration: underline;
    }
    .sms-pro-callout {
        margin-top: 14px;
        padding: 12px 14px;
        font-size: 13px;
        background: #fffbeb;
        border: 1px solid #fde68a;
        border-radius: var(--sms-radius-sm);
        color: #92400e;
    }

    .sms-spin {
        display: inline-block;
        animation: sms-spin-k 0.65s linear infinite;
    }
    @keyframes sms-spin-k {
        to { transform: rotate(360deg); }
    }

    #sms-toast {
        position: fixed;
        bottom: 24px;
        right: 24px;
        z-index: 9999;
        padding: 14px 18px;
        font-size: 14px;
        font-weight: 500;
        display: none;
        border-radius: var(--sms-radius-sm);
        border: 1px solid var(--sms-border);
        background: var(--sms-surface);
        box-shadow: 0 10px 40px rgba(15, 23, 42, 0.15), 0 4px 12px rgba(15, 23, 42, 0.08);
        max-width: 340px;
        transition: opacity 0.25s ease;
    }
    #sms-toast.t-ok {
        border-color: #6ee7b7;
        background: #ecfdf5;
        color: #065f46;
    }
    #sms-toast.t-err {
        border-color: #fca5a5;
        background: #fef2f2;
        color: #991b1b;
    }
</style>
@endsection

@section('content')
<div class="sms-pro">
<div class="sms-pro-inner container-fluid">

    <header class="sms-pro-intro">
        <div class="sms-pro-intro-main">
            <p class="sms-pro-kicker">ইন্টিগ্রেশন</p>
            <h4 class="sms-pro-title">BulkSMSBD · SMS গেটওয়ে</h4>
            <p class="sms-pro-desc">
                API কী ও Sender ID সেট করুন। অর্ডার ও OTP টাইপ SMS চালু করতে টগল ব্যবহার করুন।
                এন্ডপয়েন্ট: <code>GET bulksmsbd.net/api/smsapi</code>
            </p>
        </div>
    </header>

    <div class="sms-pro-balance">
        <div class="sms-pro-balance-left">
            <div class="sms-pro-balance-icon" aria-hidden="true"><i class="fas fa-wallet"></i></div>
            <div>
                <div class="sms-bl-label">ব্যালেন্স</div>
                <div class="bal-num" id="bl-amount"><span id="bl-val">—</span><small id="bl-unit"></small></div>
                <div class="bal-meta" id="bl-status">API কী সেভ করে তারপর চেক করুন।</div>
            </div>
        </div>
        <button type="button" class="sms-pro-btn sms-pro-btn--secondary" id="btn-balance" onclick="checkBalance()">
            <span id="bl-btn-icon"><i class="fas fa-sync-alt"></i></span>
            ব্যালেন্স চেক
        </button>
    </div>

    <div class="row">
        <div class="col-lg-7">

            <div class="sms-pro-card">
                <div class="sms-pro-card__head">
                    <i class="fas fa-key"></i>
                    API সেটিং
                </div>
                <div class="sms-pro-card__body">
                    <form action="{{ route('smsgeteway.update') }}" method="POST" id="sms-form">
                        @csrf
                        <input type="hidden" name="id" value="{{ $sms->id }}">
                        <input type="hidden" name="gateway_name" value="BulkSMSBD">
                        <input type="hidden" name="url" value="http://bulksmsbd.net/api/smsapi">
                        <input type="hidden" name="method" value="GET">
                        <input type="hidden" name="param_api_key" value="api_key">
                        <input type="hidden" name="param_phone" value="number">
                        <input type="hidden" name="param_message" value="message">
                        <input type="hidden" name="param_senderid" value="senderid">
                        <input type="hidden" name="extra_params" value='{"type":"text"}'>
                        <input type="hidden" name="success_check" value="202">
                        <input type="hidden" name="auth_type" value="none">

                        <div class="sms-pro-grid2">
                            <div>
                                <label class="sms-pro-lbl">API Key <span class="text-danger">*</span></label>
                                <input type="text" class="sms-pro-inp" name="api_key" value="{{ $sms->api_key }}"
                                       placeholder="" required autocomplete="off">
                                <div class="sms-pro-hint">bulksmsbd.net → My Account → API</div>
                            </div>
                            <div>
                                <label class="sms-pro-lbl">Sender ID</label>
                                <input type="text" class="sms-pro-inp" name="senderid" id="f-sender"
                                       value="{{ $sms->senderid ?: ($sms->serderid ?? '') }}"
                                       placeholder="" oninput="updatePreview()" autocomplete="off">
                                <div class="sms-pro-hint">অনুমোদিত মাস্কিং নম্বর</div>
                            </div>
                        </div>

                        <div style="margin-top:20px;">
                            <label class="sms-pro-lbl">অ্যাডমিন নম্বর (কমা দিয়ে)</label>
                            <input type="text" class="sms-pro-inp" name="admin_phone_list"
                                   value="{{ $sms->admin_phone ?? '' }}"
                                   placeholder="017..., 018..." autocomplete="off">
                            <div class="sms-pro-hint">নতুন অর্ডার হলে এই নম্বরগুলোতে SMS</div>
                        </div>

                        <div style="margin-top:22px;">
                            <button type="submit" class="sms-pro-btn sms-pro-btn--primary">
                                <i class="fas fa-save"></i> সংরক্ষণ
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <div class="sms-pro-card">
                <div class="sms-pro-card__head">
                    <i class="fas fa-sliders-h"></i>
                    SMS ট্রিগার
                    <span class="sms-pro-card__head-sub">টগল করলেই সেভ হয়</span>
                </div>
                <div class="sms-pro-card__body">

                    <div class="sms-pro-toggle sms-pro-toggle-master {{ $sms->status==1 ? 'on' : '' }}" id="fc-status">
                        <div>
                            <p class="t-title">
                                গেটওয়ে চালু
                                <span class="sms-pro-badge" id="b-status">{{ $sms->status==1?'ON':'OFF' }}</span>
                            </p>
                            <p class="t-desc">সাইট থেকে যাবতীয় আউটগয়িং SMS চালু বা সম্পূর্ণ বন্ধ</p>
                        </div>
                        <label class="sms-tog">
                            <input type="checkbox" data-field="status" {{ $sms->status==1?'checked':'' }} onchange="ajaxToggle(this,'fc-status','b-status')">
                            <span></span>
                        </label>
                    </div>

                    <div class="sms-pro-toggle-grid">
                        <div class="sms-pro-toggle {{ $sms->order==1 ? 'on' : '' }}" id="fc-order">
                            <div>
                                <p class="t-title">
                                    অর্ডার SMS
                                    <span class="sms-pro-badge" id="b-order">{{ $sms->order==1?'ON':'OFF' }}</span>
                                </p>
                                <p class="t-desc">কাস্টমার অর্ডার কনফার্মেশন</p>
                            </div>
                            <label class="sms-tog">
                                <input type="checkbox" data-field="order" {{ $sms->order==1?'checked':'' }} onchange="ajaxToggle(this,'fc-order','b-order')">
                                <span></span>
                            </label>
                        </div>

                        <div class="sms-pro-toggle {{ $sms->forget_pass==1 ? 'on' : '' }}" id="fc-forgot">
                            <div>
                                <p class="t-title">
                                    ফরগট পাসওয়ার্ড OTP
                                    <span class="sms-pro-badge" id="b-forgot">{{ $sms->forget_pass==1?'ON':'OFF' }}</span>
                                </p>
                                <p class="t-desc">রিসেট ভেরিফিকেশন কোড</p>
                            </div>
                            <label class="sms-tog">
                                <input type="checkbox" data-field="forget_pass" {{ $sms->forget_pass==1?'checked':'' }} onchange="ajaxToggle(this,'fc-forgot','b-forgot')">
                                <span></span>
                            </label>
                        </div>

                        <div class="sms-pro-toggle {{ $sms->password_g==1 ? 'on' : '' }}" id="fc-reg">
                            <div>
                                <p class="t-title">
                                    রেজিস্ট্রেশন OTP
                                    <span class="sms-pro-badge" id="b-reg">{{ $sms->password_g==1?'ON':'OFF' }}</span>
                                </p>
                                <p class="t-desc">নতুন একাউন্ট নিবন্ধন</p>
                            </div>
                            <label class="sms-tog">
                                <input type="checkbox" data-field="password_g" {{ $sms->password_g==1?'checked':'' }} onchange="ajaxToggle(this,'fc-reg','b-reg')">
                                <span></span>
                            </label>
                        </div>
                    </div>
                </div>
            </div>

            <div class="sms-pro-card">
                <div class="sms-pro-card__head">
                    <i class="fas fa-paper-plane"></i>
                    টেস্ট SMS
                </div>
                <div class="sms-pro-card__body">
                    <form action="{{ route('admin.sms.custom.send') }}" method="POST">
                        @csrf
                        <div class="sms-pro-grid2">
                            <div>
                                <label class="sms-pro-lbl">নম্বর</label>
                                <input type="text" class="sms-pro-inp" name="phone" placeholder="" required>
                            </div>
                            <div>
                                <label class="sms-pro-lbl">টেক্সট</label>
                                <input type="text" class="sms-pro-inp" name="message"
                                       value="Test SMS from {{ $generalsetting->name ?? config('app.name') }}" required>
                            </div>
                        </div>
                        <div style="margin-top:18px;">
                            <button type="submit" class="sms-pro-btn sms-pro-btn--success">
                                <i class="fas fa-check"></i> পাঠান
                            </button>
                        </div>
                    </form>
                </div>
            </div>

        </div>

        <div class="col-lg-5">

            <div class="sms-pro-card">
                <div class="sms-pro-card__head">
                    <i class="fas fa-code"></i>
                    রিকোয়েস্ট প্রিভিউ
                </div>
                <div class="sms-pro-card__body">
                    <div class="sms-pro-preview" id="preview-box">...</div>
                    <p class="sms-pro-preview-foot">API কী ও Sender ID লিখলে এই প্রিভিউ আপডেট হয়।</p>
                </div>
            </div>

            <div class="sms-pro-card">
                <div class="sms-pro-card__head">
                    <i class="fas fa-info-circle"></i>
                    দ্রুত গাইড
                </div>
                <div class="sms-pro-card__body">
                    <ol class="sms-pro-steps">
                        <li><a href="https://bulksmsbd.net" target="_blank" rel="noopener">bulksmsbd.net</a> এ লগইন করুন</li>
                        <li><strong>My Account → API</strong> থেকে কী সংগ্রহ করুন</li>
                        <li>Sender ID অপারেটর অনুমোদিত হতে হবে</li>
                        <li>উপরের ফর্ম পূরণ করে সংরক্ষণ করুন</li>
                    </ol>
                    <div class="sms-pro-callout">
                        সাফল্য রেসপন্স কোড কনফিগার করা আছে: <strong>202</strong>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>
</div>

<div id="sms-toast"></div>
@endsection

@section('script')
<script>
const TOGGLE_URL = "{{ route('smsgeteway.toggle') }}";
const CSRF       = "{{ csrf_token() }}";

function ajaxToggle(cb, cardId, badgeId) {
    var card  = document.getElementById(cardId);
    var badge = document.getElementById(badgeId);
    var field = cb.dataset.field;
    var val   = cb.checked ? 1 : 0;

    card.classList.toggle('on', !!val);
    badge.textContent = val ? 'ON' : 'OFF';
    cb.disabled = true;

    fetch(TOGGLE_URL, {
        method : 'POST',
        headers: { 'Content-Type':'application/json', 'X-CSRF-TOKEN':CSRF, 'Accept':'application/json' },
        body   : JSON.stringify({ field: field, value: val }),
    })
    .then(function(r) { return r.json(); })
    .then(function(d) {
        cb.disabled = false;
        if (d.success) {
            toast(val ? field + ' চালু' : field + ' বন্ধ', 'ok');
        } else {
            revert(cb, card, badge, val);
            toast(d.message || 'সংরক্ষণ হয়নি', 'err');
        }
    })
    .catch(function() {
        cb.disabled = false;
        revert(cb, card, badge, val);
        toast('নেটওয়ার্ক সমস্যা', 'err');
    });
}

function revert(cb, card, badge, failedVal) {
    cb.checked = !failedVal;
    card.classList.toggle('on', !failedVal);
    badge.textContent = !failedVal ? 'ON' : 'OFF';
}

function toast(msg, type) {
    var t = document.getElementById('sms-toast');
    t.textContent = msg;
    t.className = 't-' + type;
    t.style.display = 'block';
    t.style.opacity = '1';
    clearTimeout(t._timer);
    t._timer = setTimeout(function() {
        t.style.opacity = '0';
        setTimeout(function() { t.style.display = 'none'; }, 300);
    }, 2400);
}

function escapeHtml(s) {
    var d = document.createElement('div');
    d.textContent = s;
    return d.innerHTML;
}

function updatePreview() {
    var apiKey = (document.querySelector('[name="api_key"]') && document.querySelector('[name="api_key"]').value || 'YOUR_API_KEY').trim() || 'YOUR_API_KEY';
    var sender = (document.getElementById('f-sender') && document.getElementById('f-sender').value || 'SENDER_ID').trim() || 'SENDER_ID';

    document.getElementById('preview-box').innerHTML =
        '<span class="ck-m">GET</span> http://bulksmsbd.net/api/smsapi\n\n' +
        'api_key=' + escapeHtml(apiKey) + '\n' +
        'type=text\n' +
        'number=8801711111111\n' +
        'senderid=' + escapeHtml(sender) + '\n' +
        'message=Hello';
}

var ak = document.querySelector('[name="api_key"]');
if (ak) ak.addEventListener('input', updatePreview);
var fs = document.getElementById('f-sender');
if (fs) fs.addEventListener('input', updatePreview);
updatePreview();

var BALANCE_URL = "{{ route('smsgeteway.balance') }}";

function checkBalance() {
    var btn = document.getElementById('btn-balance');
    var amount = document.getElementById('bl-val');
    var unit = document.getElementById('bl-unit');
    var status = document.getElementById('bl-status');
    var iconWrap = document.getElementById('bl-btn-icon');

    btn.classList.add('loading');
    iconWrap.innerHTML = '<i class="fas fa-sync-alt sms-spin"></i>';
    amount.textContent = '…';
    unit.textContent = '';
    status.className = 'bal-meta';
    status.textContent = 'লোড হচ্ছে…';

    fetch(BALANCE_URL, {
        headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': CSRF }
    })
    .then(function(r) { return r.json(); })
    .then(function(d) {
        btn.classList.remove('loading');
        iconWrap.innerHTML = '<i class="fas fa-sync-alt"></i>';

        if (d.success) {
            var raw = String(d.balance);
            var num = raw.match(/[\d.]+/);
            if (num) {
                amount.textContent = parseFloat(num[0]).toLocaleString('en-BD');
                unit.textContent = raw.toLowerCase().indexOf('tk') !== -1 ? ' tk' : '';
            } else {
                amount.textContent = raw;
                unit.textContent = '';
            }
            status.className = 'bal-meta ok';
            status.textContent = 'ঠিক আছে';
            toast('ব্যালেন্স: ' + d.balance, 'ok');
        } else {
            amount.textContent = '—';
            unit.textContent = '';
            status.className = 'bal-meta err';
            status.textContent = d.message || 'ব্যর্থ';
            toast(d.message || 'চেক ব্যর্থ', 'err');
        }
    })
    .catch(function() {
        btn.classList.remove('loading');
        iconWrap.innerHTML = '<i class="fas fa-sync-alt"></i>';
        amount.textContent = '—';
        status.className = 'bal-meta err';
        status.textContent = 'নেটওয়ার্ক ত্রুটি';
        toast('নেটওয়ার্ক ত্রুটি', 'err');
    });
}
</script>
@endsection
