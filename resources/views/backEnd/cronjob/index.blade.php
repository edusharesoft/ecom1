@extends('backEnd.layouts.master')
@section('title', 'Scheduled tasks')

@section('css')
<style>
    @import url('https://fonts.googleapis.com/css2?family=DM+Sans:ital,opsz,wght@0,9..40,400;0,9..40,600;0,9..40,700;1,9..40,400&display=swap');

    /* ── Cron suite (scoped page chrome) ───────────────────────── */
    .cron-suite {
        --cron-bg: #f0f2f5;
        --cron-surface: #ffffff;
        --cron-border: #e6e9ef;
        --cron-text: #1a1d26;
        --cron-muted: #5c6378;
        --cron-accent: #0d9488;
        --cron-accent-soft: rgba(13, 148, 136, 0.08);
        --cron-radius: 14px;
        --cron-shadow: 0 1px 2px rgba(16, 24, 40, 0.04), 0 8px 24px rgba(16, 24, 40, 0.06);
        --cron-font: 'DM Sans', system-ui, -apple-system, 'Segoe UI', Roboto, sans-serif;
        font-family: var(--cron-font);
        color: var(--cron-text);
        letter-spacing: -0.01em;
    }
    .cron-suite * { box-sizing: border-box; }

    .cron-suite-hero {
        background: var(--cron-surface);
        border: 1px solid var(--cron-border);
        border-radius: var(--cron-radius);
        box-shadow: var(--cron-shadow);
        padding: 22px 26px;
        margin-bottom: 22px;
        display: flex;
        flex-wrap: wrap;
        align-items: flex-start;
        justify-content: space-between;
        gap: 16px;
        position: relative;
        overflow: hidden;
    }
    .cron-suite-hero::before {
        content: '';
        position: absolute;
        left: 0; top: 0; bottom: 0;
        width: 4px;
        background: linear-gradient(180deg, var(--cron-accent), #0891b2);
        border-radius: 4px 0 0 4px;
    }
    .cron-suite-hero h1 {
        font-size: 1.35rem;
        font-weight: 700;
        margin: 0 0 6px;
        color: var(--cron-text);
    }
    .cron-suite-hero p {
        margin: 0;
        font-size: 0.875rem;
        color: var(--cron-muted);
        max-width: 520px;
        line-height: 1.55;
    }
    .cron-suite-meta {
        display: flex;
        flex-wrap: wrap;
        gap: 10px;
        align-items: center;
    }
    .cron-pill {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 7px 12px;
        border-radius: 999px;
        font-size: 0.75rem;
        font-weight: 600;
        background: var(--cron-accent-soft);
        color: #0f766e;
        border: 1px solid rgba(13, 148, 136, 0.18);
    }
    .cron-pill.muted {
        background: #f4f5f8;
        color: var(--cron-muted);
        border-color: var(--cron-border);
    }

    .cron-job-card {
        background: var(--cron-surface);
        border: 1px solid var(--cron-border);
        border-radius: var(--cron-radius);
        box-shadow: var(--cron-shadow);
        overflow: hidden;
        transition: box-shadow .22s ease, border-color .22s ease;
    }
    .cron-job-card:hover {
        box-shadow: 0 2px 6px rgba(16, 24, 40, 0.06), 0 14px 36px rgba(16, 24, 40, 0.08);
        border-color: #dce1ea;
    }

    .cron-job-head {
        padding: 18px 22px;
        display: flex;
        align-items: flex-start;
        gap: 14px;
        border-bottom: 1px solid var(--cron-border);
        background: linear-gradient(180deg, #fafbfd 0%, #fff 100%);
    }
    .cron-job-icon {
        width: 48px;
        height: 48px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.15rem;
        color: #fff;
        flex-shrink: 0;
        background: linear-gradient(145deg, #14b8a6, #0d9488);
        box-shadow: 0 4px 14px rgba(13, 148, 136, 0.35);
    }
    .cron-job-head-body { flex: 1; min-width: 0; }
    .cron-job-head-body h2 {
        font-size: 1.05rem;
        font-weight: 700;
        margin: 0 0 4px;
        color: var(--cron-text);
    }
    .cron-job-head-body .cron-desc {
        margin: 0;
        font-size: 0.8125rem;
        color: var(--cron-muted);
        line-height: 1.5;
    }

    /* Toggle */
    .cron-switch-wrap { flex-shrink: 0; padding-top: 2px; }
    .cron-switch {
        position: relative;
        display: inline-block;
        width: 48px;
        height: 26px;
    }
    .cron-switch input { opacity: 0; width: 0; height: 0; }
    .cron-switch-slider {
        position: absolute;
        cursor: pointer;
        inset: 0;
        background: #cfd4dc;
        border-radius: 26px;
        transition: .25s;
    }
    .cron-switch-slider::before {
        content: '';
        position: absolute;
        height: 20px;
        width: 20px;
        left: 3px;
        bottom: 3px;
        background: #fff;
        border-radius: 50%;
        transition: .25s;
        box-shadow: 0 1px 4px rgba(0, 0, 0, 0.12);
    }
    .cron-switch input:checked + .cron-switch-slider {
        background: linear-gradient(90deg, #10b981, #059669);
    }
    .cron-switch input:checked + .cron-switch-slider::before {
        transform: translateX(22px);
    }
    .cron-switch input:focus-visible + .cron-switch-slider {
        outline: 2px solid var(--cron-accent);
        outline-offset: 2px;
    }

    .cron-job-main { padding: 20px 22px 18px; }

    .cron-stats {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 12px;
        margin-bottom: 18px;
    }
    @media (max-width: 575px) {
        .cron-stats { grid-template-columns: 1fr; }
    }
    .cron-stat {
        border: 1px solid var(--cron-border);
        border-radius: 12px;
        padding: 14px 14px 12px;
        background: #fafbfc;
        text-align: center;
        transition: background .15s ease;
    }
    .cron-job-card:hover .cron-stat { background: #f7f8fa; }
    .cron-stat-val {
        font-size: 1.35rem;
        font-weight: 700;
        line-height: 1.15;
        font-variant-numeric: tabular-nums;
    }
    .cron-stat-val.ok { color: #047857; }
    .cron-stat-val.bad { color: #b91c1c; }
    .cron-stat-val.hl { color: #0369a1; }
    .cron-stat-lbl {
        font-size: 0.6875rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.06em;
        color: var(--cron-muted);
        margin-top: 6px;
    }

    .cron-run-row {
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        justify-content: space-between;
        gap: 10px;
        margin-bottom: 14px;
        padding-bottom: 14px;
        border-bottom: 1px dashed var(--cron-border);
    }
    .cron-run-row .cron-last-label {
        font-size: 0.6875rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        color: var(--cron-muted);
        display: block;
        margin-bottom: 4px;
    }
    .cron-run-row .cron-last-time {
        font-size: 0.875rem;
        font-weight: 600;
        color: var(--cron-text);
        font-variant-numeric: tabular-nums;
    }

    .run-badge {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 6px 12px;
        border-radius: 999px;
        font-size: 0.75rem;
        font-weight: 600;
    }
    .run-badge.success { background: #ecfdf5; color: #047857; border: 1px solid #a7f3d0; }
    .run-badge.failed { background: #fef2f2; color: #b91c1c; border: 1px solid #fecaca; }
    .run-badge.running { background: #fffbeb; color: #b45309; border: 1px solid #fde68a; }
    .run-badge.none { background: #f8fafc; color: #64748b; border: 1px solid var(--cron-border); }

    .cron-result {
        font-size: 0.8125rem;
        line-height: 1.5;
        color: var(--cron-muted);
        background: #f8fafc;
        border: 1px solid var(--cron-border);
        border-radius: 10px;
        padding: 10px 12px;
        margin-bottom: 16px;
    }
    .cron-result i { opacity: 0.7; margin-right: 6px; }

    .cron-form-grid {
        display: grid;
        grid-template-columns: 1fr 1fr auto;
        gap: 12px;
        align-items: end;
    }
    @media (max-width: 767px) {
        .cron-form-grid { grid-template-columns: 1fr; }
    }
    .cron-field label {
        display: block;
        font-size: 0.6875rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.06em;
        color: var(--cron-muted);
        margin-bottom: 6px;
    }
    .cron-select {
        width: 100%;
        border: 1px solid var(--cron-border);
        border-radius: 10px;
        padding: 9px 12px;
        font-size: 0.8125rem;
        background: #fff;
        color: var(--cron-text);
        transition: border-color .15s, box-shadow .15s;
    }
    .cron-select:focus {
        outline: none;
        border-color: var(--cron-accent);
        box-shadow: 0 0 0 3px var(--cron-accent-soft);
    }

    .btn-cron-save {
        border: 1px solid var(--cron-border);
        background: #fff;
        color: var(--cron-text);
        font-size: 0.8125rem;
        font-weight: 600;
        padding: 9px 18px;
        border-radius: 10px;
        white-space: nowrap;
        transition: background .15s, border-color .15s;
    }
    .btn-cron-save:hover {
        background: #f8fafc;
        border-color: #cbd1dc;
    }
    .btn-cron-save:disabled { opacity: 0.65; cursor: not-allowed; }

    .cron-job-foot {
        padding: 14px 22px;
        background: linear-gradient(180deg, #fafbfd 0%, #f4f6f9 100%);
        border-top: 1px solid var(--cron-border);
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
    }
    .cron-foot-note {
        font-size: 0.8125rem;
        color: var(--cron-muted);
        display: flex;
        align-items: center;
        gap: 8px;
    }
    .cron-foot-note i { color: #94a3b8; }

    .btn-run-now {
        background: linear-gradient(145deg, #14b8a6, #0d9488);
        color: #fff !important;
        border: none;
        padding: 10px 20px;
        border-radius: 10px;
        font-size: 0.8125rem;
        font-weight: 700;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        box-shadow: 0 4px 14px rgba(13, 148, 136, 0.35);
        transition: transform .15s, box-shadow .15s;
    }
    .btn-run-now:hover {
        transform: translateY(-1px);
        box-shadow: 0 6px 20px rgba(13, 148, 136, 0.42);
    }
    .btn-run-now:disabled {
        opacity: 0.65;
        cursor: not-allowed;
        transform: none;
        box-shadow: none;
    }

    /* Docs panel */
    .cron-docs {
        position: sticky;
        top: 88px;
        background: linear-gradient(165deg, #0f172a 0%, #1e293b 55%, #0f172a 100%);
        border-radius: var(--cron-radius);
        border: 1px solid #243047;
        box-shadow: var(--cron-shadow);
        padding: 22px;
        color: #e2e8f0;
    }
    .cron-docs h3 {
        font-size: 0.8125rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.08em;
        color: #94a3b8;
        margin: 0 0 12px;
    }
    .cron-docs h3:first-of-type { margin-top: 0; }
    .cron-docs .lead {
        font-size: 0.875rem;
        color: #cbd5e1;
        line-height: 1.55;
        margin: 0 0 12px;
    }
    .cron-code-block {
        position: relative;
        background: #020617;
        border: 1px solid #334155;
        border-radius: 10px;
        padding: 12px 14px;
        margin: 10px 0;
    }
    .cron-code-block code {
        display: block;
        font-size: 0.75rem;
        line-height: 1.55;
        color: #5eead4;
        word-break: break-all;
        font-family: ui-monospace, 'Cascadia Code', 'SF Mono', Menlo, monospace;
    }
    .btn-cron-copy {
        position: absolute;
        top: 8px;
        right: 8px;
        font-size: 0.6875rem;
        font-weight: 600;
        padding: 5px 10px;
        border-radius: 6px;
        border: 1px solid #475569;
        background: #1e293b;
        color: #e2e8f0;
        cursor: pointer;
    }
    .btn-cron-copy:hover { background: #334155; }
    .cron-docs ul {
        margin: 0;
        padding-left: 18px;
        font-size: 0.8125rem;
        color: #94a3b8;
        line-height: 1.75;
    }
    .cron-docs ul strong { color: #5eead4; font-weight: 600; }
    .cron-docs hr {
        border: 0;
        border-top: 1px solid #334155;
        margin: 18px 0;
    }

    .spinner-border-sm { width: 14px; height: 14px; border-width: 2px; }
    @keyframes cron-pulse-dot { 0%, 100% { opacity: 1; } 50% { opacity: 0.35; } }
    .pulse-dot { animation: cron-pulse-dot 1.1s ease-in-out infinite; }
</style>
@endsection

@section('content')
<div class="cron-suite">
    <div class="page-title-area">
        <div class="row align-items-center">
            <div class="col-sm-6">
                <div class="breadcrumbs-area clearfix">
                    <h4 class="page-title pull-left mb-0" style="font-weight:700;">Cron &amp; scheduled tasks</h4>
                </div>
            </div>
        </div>
    </div>

    <div class="main-content-inner">
        <div class="cron-suite-hero">
            <div>
                <h1>ব্যাকগ্রাউন্ড সিঙ্ক ও শিডুল</h1>
                <p>
                    কুরিয়ার স্ট্যাটাস আপডেটের মতো কাজগুলো এখান থেকে চালু/বন্ধ, রিদম ও লিমিট সেট করুন।
                    সার্ভারে শুধু এক লাইনের cron দিলেই Laravel <code style="font-size:0.8em;">schedule:run</code> বাকিটা সামলাবে।
                </p>
            </div>
            <div class="cron-suite-meta">
                <span class="cron-pill">
                    <i class="fa fa-bolt"></i> {{ $jobs->where('is_enabled', true)->count() }}/{{ $jobs->count() }} সক্রিয়
                </span>
                <span class="cron-pill muted"><i class="fa fa-clock-o"></i> লাইভ সেটিংস</span>
            </div>
        </div>

        <div class="row">
            <div class="col-lg-8 col-xl-7 mb-4">
                @foreach($jobs as $job)
                <article class="cron-job-card mb-4" id="card-job-{{ $job->id }}">
                    <header class="cron-job-head">
                        <div class="cron-job-icon" aria-hidden="true">
                            <i class="fa fa-truck"></i>
                        </div>
                        <div class="cron-job-head-body">
                            <h2>{{ $job->job_title }}</h2>
                            <p class="cron-desc">{{ $job->job_description }}</p>
                        </div>
                        <div class="cron-switch-wrap">
                            <label class="cron-switch mb-0" title="{{ $job->is_enabled ? 'বন্ধ করুন' : 'চালু করুন' }}">
                                <input type="checkbox" class="toggle-cron" data-id="{{ $job->id }}" {{ $job->is_enabled ? 'checked' : '' }}>
                                <span class="cron-switch-slider"></span>
                            </label>
                        </div>
                    </header>

                    <div class="cron-job-main">
                        <div class="cron-stats">
                            <div class="cron-stat">
                                <div class="cron-stat-val ok" id="updated-{{ $job->id }}">{{ $job->last_updated_count ?? 0 }}</div>
                                <div class="cron-stat-lbl">আপডেট হয়েছে</div>
                            </div>
                            <div class="cron-stat">
                                <div class="cron-stat-val bad" id="failed-{{ $job->id }}">{{ $job->last_failed_count ?? 0 }}</div>
                                <div class="cron-stat-lbl">ব্যর্থ</div>
                            </div>
                            <div class="cron-stat">
                                <div class="cron-stat-val hl">{{ $job->frequency_minutes }}</div>
                                <div class="cron-stat-lbl">মিনিট ইন্টারভাল</div>
                            </div>
                        </div>

                        <div class="cron-run-row">
                            <div>
                                <span class="cron-last-label">শেষ রান</span>
                                <span class="cron-last-time" id="last-run-at-{{ $job->id }}">
                                    {{ $job->last_run_at ? $job->last_run_at->format('d M Y, h:i A') : 'এখনও রান হয়নি' }}
                                </span>
                            </div>
                            <span class="run-badge {{ $job->last_run_status ?? 'none' }}" id="status-badge-{{ $job->id }}">
                                @if($job->last_run_status === 'success')
                                    <i class="fa fa-check-circle"></i> সফল
                                @elseif($job->last_run_status === 'failed')
                                    <i class="fa fa-times-circle"></i> ব্যর্থ
                                @elseif($job->last_run_status === 'running')
                                    <i class="fa fa-circle pulse-dot"></i> চলছে
                                @else
                                    <i class="fa fa-clock-o"></i> অপেক্ষমান
                                @endif
                            </span>
                        </div>

                        @if($job->last_run_result)
                        <div class="cron-result" id="result-text-{{ $job->id }}">
                            <i class="fa fa-commenting-o"></i>{{ $job->last_run_result }}
                        </div>
                        @else
                        <div class="cron-result" id="result-text-{{ $job->id }}" style="display:none;"></div>
                        @endif

                        <div class="cron-form-grid">
                            <div class="cron-field">
                                <label for="freq-{{ $job->id }}">ফ্রিকোয়েন্সি</label>
                                <select class="cron-select freq-select" id="freq-{{ $job->id }}">
                                    @foreach([1,2,5,10,15,30,60,120] as $min)
                                    <option value="{{ $min }}" {{ $job->frequency_minutes == $min ? 'selected' : '' }}>
                                        {{ $min >= 60 ? ($min/60).' ঘণ্টা' : $min.' মিনিট' }}
                                    </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="cron-field">
                                <label for="limit-{{ $job->id }}">অর্ডার লিমিট</label>
                                <select class="cron-select freq-select" id="limit-{{ $job->id }}">
                                    @foreach([10,25,50,100,200,500] as $lim)
                                    <option value="{{ $lim }}" {{ $job->order_limit == $lim ? 'selected' : '' }}>
                                        {{ $lim }}টি
                                    </option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <button type="button" class="btn-cron-save btn-save-settings w-100"
                                        data-id="{{ $job->id }}">
                                    <i class="fa fa-check"></i> সেভ সেটিংস
                                </button>
                            </div>
                        </div>
                    </div>

                    <footer class="cron-job-foot">
                        <span class="cron-foot-note">
                            <i class="fa fa-repeat"></i>
                            স্বয়ংক্রিয়: প্রতি <strong id="freq-display-{{ $job->id }}">{{ $job->frequency_minutes >= 60 ? ($job->frequency_minutes/60).' ঘণ্টা' : $job->frequency_minutes.' মিনিট' }}</strong>
                        </span>
                        <button type="button" class="btn-run-now" id="btn-run-{{ $job->id }}" data-id="{{ $job->id }}">
                            <i class="fa fa-play"></i> এখনই রান
                        </button>
                    </footer>
                </article>
                @endforeach
            </div>

            <div class="col-lg-4 col-xl-5 mb-4">
                <aside class="cron-docs">
                    <h3><i class="fa fa-server"></i> সার্ভার ক্রন</h3>
                    <p class="lead">cPanel বা VPS-এ একটি লাইন যোগ করুন। পাথ আপনার হোস্ট অনুযায়ী গ্রহণ করা হবে।</p>
                    <div class="cron-code-block">
                        <button type="button" class="btn-cron-copy" data-copy-cron>* কপি</button>
                        <code id="cron-cmd-line">* * * * * cd {{ base_path() }} && php artisan schedule:run >> /dev/null 2>&1</code>
                    </div>
                    <p class="lead" style="margin-bottom:0;font-size:0.8rem;color:#64748b;">
                        <i class="fa fa-lightbulb-o"></i> একটি ক্রন লাইনই যথেষ্ট — বাকি ইন্টারভেল Laravel শিডুলার মেনে চলবে।
                    </p>

                    <hr>

                    <h3><i class="fa fa-question-circle"></i> ফ্লো সংক্ষেপে</h3>
                    <ul>
                        <li>কুরিয়ারে গেলে স্ট্যাটাস সাধারণত <strong>5</strong></li>
                        <li>Cron রান হলে API থেকে আপডেট আসে</li>
                        <li>ডেলিভারড → <strong>6</strong> · বাতিল → <strong>11</strong></li>
                        <li>«এখনই রান» দিয়ে হাতে সিঙ্ক করা যায়</li>
                    </ul>

                    <hr>

                    <h3><i class="fa fa-terminal"></i> টার্মিনাল</h3>
                    <div class="cron-code-block">
                        <code>php artisan courier:check-status --limit=50</code>
                    </div>
                    <div class="cron-code-block" style="margin-top:8px;">
                        <code>php artisan courier:check-status --force</code>
                    </div>
                </aside>
            </div>
        </div>
    </div>
</div>
@endsection

@section('js')
<script>
(function() {
    const csrfToken = '{{ csrf_token() }}';

    document.querySelectorAll('[data-copy-cron]').forEach(function(btn) {
        btn.addEventListener('click', function() {
            var el = document.getElementById('cron-cmd-line');
            if (!el) return;
            var text = el.textContent.trim();
            if (navigator.clipboard && navigator.clipboard.writeText) {
                navigator.clipboard.writeText(text).then(function() {
                    if (typeof toastr !== 'undefined') toastr.success('ক্রন লাইন ক্লিপবোর্ডে কপি হয়েছে।');
                    else alert('কপি হয়েছে।');
                });
            } else {
                var ta = document.createElement('textarea');
                ta.value = text;
                document.body.appendChild(ta);
                ta.select();
                try { document.execCommand('copy'); } catch (e) {}
                document.body.removeChild(ta);
                if (typeof toastr !== 'undefined') toastr.success('কপি হয়েছে।');
            }
        });
    });

    document.querySelectorAll('.toggle-cron').forEach(function(el) {
        el.addEventListener('change', function() {
            var id = this.dataset.id;
            fetch('{{ url("admin/cron-jobs") }}/' + id + '/toggle', {
                method: 'POST',
                headers: { 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' }
            })
            .then(function(r) { return r.json(); })
            .then(function(data) {
                if (data.success) showToast(data.message, 'success');
            })
            .catch(function() { showToast('সমস্যা হয়েছে, আবার চেষ্টা করুন।', 'error'); });
        });
    });

    document.querySelectorAll('.btn-save-settings').forEach(function(btn) {
        btn.addEventListener('click', function() {
            var id = this.dataset.id;
            var freq = document.getElementById('freq-' + id).value;
            var lim = document.getElementById('limit-' + id).value;
            btn.disabled = true;
            var orig = btn.innerHTML;
            btn.innerHTML = '<i class="fa fa-spinner fa-spin"></i> সেভ…';

            fetch('{{ url("admin/cron-jobs") }}/' + id + '/settings', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json',
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({ frequency_minutes: freq, order_limit: lim })
            })
            .then(function(r) { return r.json(); })
            .then(function(data) {
                btn.disabled = false;
                btn.innerHTML = orig;
                if (data.success) {
                    showToast(data.message, 'success');
                    var dispEl = document.getElementById('freq-display-' + id);
                    if (dispEl) dispEl.textContent = freq >= 60 ? (freq / 60) + ' ঘণ্টা' : freq + ' মিনিট';
                    var stats = document.querySelectorAll('#card-job-' + id + ' .cron-stat-val.hl');
                    if (stats.length) stats[0].textContent = freq;
                } else showToast('সেভ ব্যর্থ হয়েছে।', 'error');
            })
            .catch(function() {
                btn.disabled = false;
                btn.innerHTML = orig;
                showToast('নেটওয়ার্ক সমস্যা।', 'error');
            });
        });
    });

    document.querySelectorAll('.btn-run-now').forEach(function(btn) {
        btn.addEventListener('click', function() {
            var id = btn.dataset.id;
            btn.disabled = true;
            btn.innerHTML = '<span class="spinner-border spinner-border-sm mr-1"></span> চলছে…';
            setBadge(id, 'running', '<i class="fa fa-circle pulse-dot"></i> চলছে');

            fetch('{{ url("admin/cron-jobs") }}/' + id + '/run-now', {
                method: 'POST',
                headers: { 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' }
            })
            .then(function(r) { return r.json(); })
            .then(function(data) {
                btn.disabled = false;
                btn.innerHTML = '<i class="fa fa-play"></i> এখনই রান';

                if (data.success) {
                    showToast(data.message, 'success');
                    setBadge(id, data.last_run_status, getBadgeHtml(data.last_run_status));
                    if (data.last_run_at) {
                        var la = document.getElementById('last-run-at-' + id);
                        if (la) la.textContent = data.last_run_at;
                    }
                    if (data.last_run_result) {
                        var res = document.getElementById('result-text-' + id);
                        if (res) {
                            res.style.display = '';
                            res.innerHTML = '<i class="fa fa-commenting-o"></i>' + data.last_run_result;
                        }
                    }
                    if (typeof data.updated_count !== 'undefined') {
                        var u = document.getElementById('updated-' + id);
                        if (u) u.textContent = data.updated_count;
                    }
                    if (typeof data.failed_count !== 'undefined') {
                        var f = document.getElementById('failed-' + id);
                        if (f) f.textContent = data.failed_count;
                    }
                } else {
                    showToast(data.message || 'রান ব্যর্থ হয়েছে।', 'error');
                    setBadge(id, 'failed', '<i class="fa fa-times-circle"></i> ব্যর্থ');
                }
            })
            .catch(function() {
                btn.disabled = false;
                btn.innerHTML = '<i class="fa fa-play"></i> এখনই রান';
                setBadge(id, 'failed', '<i class="fa fa-times-circle"></i> ব্যর্থ');
                showToast('নেটওয়ার্ক সমস্যা।', 'error');
            });
        });
    });

    function setBadge(id, status, html) {
        var el = document.getElementById('status-badge-' + id);
        if (!el) return;
        el.className = 'run-badge ' + (status || 'none');
        el.innerHTML = html;
    }

    function getBadgeHtml(status) {
        if (status === 'success') return '<i class="fa fa-check-circle"></i> সফল';
        if (status === 'failed') return '<i class="fa fa-times-circle"></i> ব্যর্থ';
        if (status === 'running') return '<i class="fa fa-circle pulse-dot"></i> চলছে';
        return '<i class="fa fa-clock-o"></i> অপেক্ষমান';
    }

    function showToast(msg, type) {
        if (typeof toastr !== 'undefined') {
            toastr[type === 'success' ? 'success' : 'error'](msg);
        } else alert(msg);
    }
})();
</script>
@endsection
