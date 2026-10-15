@extends('backEnd.layouts.master')
@section('title','Dashboard')

@section('css')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/apexcharts@3.44.0/dist/apexcharts.css">
<style>
.db-wrap { padding: 4px 0 32px; }

/* ─── Page heading ─── */
.db-heading { margin-bottom: 24px; }
.db-heading h1 { font-size: 20px; font-weight: 700; color: #111827; margin: 0 0 2px; letter-spacing: -.3px; }
.db-heading p  { font-size: 13px; color: #9ca3af; margin: 0; }

/* Top row: SMS + BD Courier + Steadfast */
.dash-top-row { display: grid; grid-template-columns: repeat(auto-fit, minmax(260px, 1fr)); gap: 12px; margin-bottom: 16px; }

/* ─── Stat cards ─── */
.stat-grid { display: grid; grid-template-columns: repeat(4,1fr); gap: 16px; margin-bottom: 20px; }
@media(max-width:1100px){ .stat-grid { grid-template-columns: repeat(2,1fr); } }
@media(max-width:540px) { .stat-grid { grid-template-columns: 1fr 1fr; gap:12px; } }

.sc {
    background: #fff;
    border: 1px solid #e5e7eb;
    border-radius: 12px;
    padding: 20px;
    display: flex; align-items: flex-start; gap: 14px;
    transition: box-shadow .2s;
}
.sc:hover { box-shadow: 0 4px 18px rgba(0,0,0,.07); }
.sc-ico {
    width: 44px; height: 44px; border-radius: 10px;
    display: flex; align-items: center; justify-content: center;
    flex-shrink: 0; font-size: 18px;
}
.sc-label { font-size: 11px; font-weight: 600; letter-spacing: .6px; text-transform: uppercase; color: #9ca3af; }
.sc-val   { font-size: 24px; font-weight: 700; color: #111827; line-height: 1.15; margin: 3px 0 1px; letter-spacing: -.5px; }
.sc-note  { font-size: 12px; color: #9ca3af; }

/* ─── Section label ─── */
.section-label {
    font-size: 11px; font-weight: 600; letter-spacing: 1px;
    text-transform: uppercase; color: #6b7280;
    margin: 24px 0 12px;
}

/* ─── Today snapshot ─── */
.snap-grid { display: grid; grid-template-columns: repeat(3,1fr); gap: 14px; margin-bottom: 20px; }
@media(max-width:700px){ .snap-grid { grid-template-columns: 1fr; } }

.sn {
    background: #fff; border: 1px solid #e5e7eb; border-radius: 12px;
    padding: 16px 18px;
}
.sn-top { display: flex; align-items: center; justify-content: space-between; margin-bottom: 10px; }
.sn-name { font-size: 12px; font-weight: 600; color: #6b7280; text-transform: uppercase; letter-spacing: .6px; }
.sn-badge { width: 32px; height: 32px; border-radius: 8px; display: flex; align-items: center; justify-content: center; font-size: 15px; }
.sn-val  { font-size: 22px; font-weight: 700; color: #111827; letter-spacing: -.4px; }
.sn-sub  { font-size: 12px; color: #9ca3af; margin-top: 2px; }

/* ─── Quick actions ─── */
.qa-grid { display: grid; grid-template-columns: repeat(4,1fr); gap: 12px; margin-bottom: 20px; }
@media(max-width:700px){ .qa-grid { grid-template-columns: repeat(2,1fr); } }

.qa {
    background: #fff; border: 1px solid #e5e7eb; border-radius: 10px;
    padding: 13px 10px; text-align: center; text-decoration: none;
    color: #374151; font-size: 13px; font-weight: 500;
    display: flex; flex-direction: column; align-items: center; gap: 5px;
    transition: border-color .15s, background .15s;
}
.qa:hover { border-color: #243b22; background: #f6fbf6; color: #243b22; text-decoration: none; }
.qa svg { width: 18px; height: 18px; stroke: #6b7280; transition: stroke .15s; }
.qa:hover svg { stroke: #243b22; }

/* ─── Charts ─── */
.chart-row { display: grid; grid-template-columns: 1.5fr 1fr; gap: 16px; margin-bottom: 20px; }
@media(max-width:900px){ .chart-row { grid-template-columns: 1fr; } }

.chart-box {
    background: #fff; border: 1px solid #e5e7eb; border-radius: 12px; padding: 20px;
}
.chart-box-title {
    font-size: 14px; font-weight: 600; color: #111827; margin-bottom: 4px;
}
.chart-box-sub { font-size: 12px; color: #9ca3af; margin-bottom: 16px; }

/* ─── Table cards ─── */
.tbl-card { background: #fff; border: 1px solid #e5e7eb; border-radius: 12px; overflow: hidden; }
.tbl-card-head {
    padding: 16px 20px; display: flex; align-items: center; justify-content: space-between;
    border-bottom: 1px solid #f3f4f6;
}
.tbl-card-head h4 { font-size: 14px; font-weight: 600; color: #111827; margin: 0; }
.tbl-card-head a  { font-size: 12px; font-weight: 500; color: #243b22; text-decoration: none; }
.tbl-card-head a:hover { text-decoration: underline; }
.tbl-card-body { padding: 0; }

table.clean th {
    font-size: 11px; font-weight: 600; text-transform: uppercase;
    letter-spacing: .5px; color: #9ca3af;
    background: #fafafa; padding: 10px 18px;
    border-bottom: 1px solid #f3f4f6; white-space: nowrap;
}
table.clean td {
    padding: 11px 18px; font-size: 13px; color: #374151;
    border-bottom: 1px solid #f9fafb; vertical-align: middle;
}
table.clean tbody tr:last-child td { border-bottom: none; }
table.clean tbody tr:hover td { background: #fafafa; }

/* Status badges */
.sb {
    display: inline-block; font-size: 11px; font-weight: 600;
    padding: 3px 10px; border-radius: 20px; white-space: nowrap;
}
.sb-1 { background: #fef3c7; color: #92400e; }
.sb-2 { background: #dbeafe; color: #1e40af; }
.sb-3 { background: #ede9fe; color: #5b21b6; }
.sb-4 { background: #ffedd5; color: #9a3412; }
.sb-5 { background: #cffafe; color: #155e75; }
.sb-6 { background: #dcfce7; color: #166534; }
.sb-7 { background: #fee2e2; color: #991b1b; }

/* Product thumbnail */
.p-thumb {
    width: 36px; height: 36px; border-radius: 8px;
    object-fit: cover; border: 1px solid #f3f4f6; flex-shrink: 0;
}
.p-thumb-ph {
    width: 36px; height: 36px; border-radius: 8px;
    background: #f3f4f6; display: inline-flex; align-items: center;
    justify-content: center; color: #9ca3af; font-size: 15px; flex-shrink: 0;
}

/* ─── Category list ─── */
.cat-item {
    display: flex; align-items: center; justify-content: space-between;
    padding: 11px 18px; border-bottom: 1px solid #f9fafb;
    font-size: 13px; color: #374151;
}
.cat-item:last-child { border-bottom: none; }
.cat-item-left { display: flex; align-items: center; gap: 10px; }
.cat-dot { width: 7px; height: 7px; border-radius: 50%; background: #243b22; }
.cat-pill {
    background: #f0fdf4; color: #166534; border: 1px solid #bbf7d0;
    font-size: 11px; font-weight: 600; padding: 2px 9px; border-radius: 20px;
}

/* ─── Finance strip ─── */
.fin-strip { display: grid; grid-template-columns: repeat(4,1fr); gap: 12px; margin-bottom: 20px; }
@media(max-width:900px){ .fin-strip { grid-template-columns: repeat(2,1fr); } }
@media(max-width:540px) { .fin-strip { grid-template-columns: 1fr 1fr; } }

.fin-item {
    background: #fff; border: 1px solid #e5e7eb; border-radius: 10px;
    padding: 14px 16px;
}
.fin-item-label { font-size: 11px; font-weight: 600; color: #9ca3af; text-transform: uppercase; letter-spacing: .6px; }
.fin-item-val   { font-size: 18px; font-weight: 700; color: #111827; margin-top: 4px; letter-spacing: -.3px; }

/* ─── Two col layout ─── */
.two-col { display: grid; grid-template-columns: 1.4fr 1fr; gap: 16px; margin-bottom: 20px; }
@media(max-width:900px){ .two-col { grid-template-columns: 1fr; } }
</style>
@endsection

@section('content')
<div class="db-wrap">

    {{-- Heading --}}
    <div class="db-heading">
        <h1>Dashboard</h1>
        <p>{{ now()->format('l, F j, Y') }}</p>
  </div>

    <div class="dash-top-row">

    {{-- ── SMS Balance Strip ── --}}
    <div style="background:#fff;border:1px solid #e5e7eb;border-radius:12px;padding:14px 20px;display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:10px;">
        <div style="display:flex;align-items:center;gap:12px;">
            <div style="width:38px;height:38px;border-radius:9px;background:#eff6ff;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#3b82f6" stroke-width="2"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07A19.5 19.5 0 0 1 4.69 12 19.79 19.79 0 0 1 1.65 3.35 2 2 0 0 1 3.62 1h3a2 2 0 0 1 2 1.72c.127.96.361 1.903.7 2.81a2 2 0 0 1-.45 2.11L7.91 8.6a16 16 0 0 0 5.49 5.49l.96-.96a2 2 0 0 1 2.11-.45c.907.339 1.85.573 2.81.7A2 2 0 0 1 21 15.42z"/></svg>
            </div>
    <div>
                <span style="font-size:11px;font-weight:600;letter-spacing:.6px;text-transform:uppercase;color:#9ca3af;">BulkSMSBD Balance</span>
                <div style="display:flex;align-items:baseline;gap:8px;">
                    <span id="sms-bal-val" style="font-size:20px;font-weight:700;color:#111827;letter-spacing:-.3px;">—</span>
                    <span id="sms-bal-msg" style="font-size:12px;color:#9ca3af;">Click to check</span>
                </div>
            </div>
        </div>
        <button onclick="fetchSmsBalance()" id="sms-bal-btn"
            style="display:inline-flex;align-items:center;gap:6px;background:#f9fafb;border:1px solid #e5e7eb;border-radius:8px;padding:7px 14px;font-size:12px;font-weight:500;color:#374151;cursor:pointer;transition:all .15s;white-space:nowrap;"
            onmouseover="this.style.borderColor='#243b22';this.style.color='#243b22';this.style.background='#f0f7f0'"
            onmouseout="this.style.borderColor='#e5e7eb';this.style.color='#374151';this.style.background='#f9fafb'">
            <svg id="sms-bal-ico" xmlns="http://www.w3.org/2000/svg" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="23 4 23 10 17 10"/><path d="M20.49 15a9 9 0 1 1-.48-3.44"/></svg>
            Refresh
        </button>
    </div>

    {{-- ── BD Courier My Plan ── --}}
    <div style="background:#fff;border:1px solid #e5e7eb;border-radius:12px;padding:14px 20px;display:flex;align-items:flex-start;justify-content:space-between;flex-wrap:wrap;gap:10px;">
        <div style="display:flex;align-items:flex-start;gap:12px;flex:1;min-width:0;">
            <div style="width:38px;height:38px;border-radius:9px;background:#f0f7f0;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#243b22" stroke-width="2"><rect x="1" y="3" width="15" height="13"/><polygon points="16 8 20 8 23 11 23 16 16 16 16 8"/><circle cx="5.5" cy="18.5" r="2.5"/><circle cx="18.5" cy="18.5" r="2.5"/></svg>
            </div>
            <div style="min-width:0;">
                <span style="font-size:11px;font-weight:600;letter-spacing:.6px;text-transform:uppercase;color:#9ca3af;">BD Courier · My Plan</span>
                <div id="bd-plan-main" style="font-size:17px;font-weight:700;color:#111827;margin-top:2px;line-height:1.25;">—</div>
                <div id="bd-plan-detail" style="font-size:12px;color:#6b7280;margin-top:6px;line-height:1.45;white-space:pre-line;"></div>
                <span id="bd-plan-msg" style="font-size:11px;display:block;margin-top:4px;"></span>
            </div>
        </div>
        <button type="button" onclick="fetchBdCourierPlan()" id="bd-plan-btn"
            style="display:inline-flex;align-items:center;gap:6px;background:#f9fafb;border:1px solid #e5e7eb;border-radius:8px;padding:7px 14px;font-size:12px;font-weight:500;color:#374151;cursor:pointer;transition:all .15s;white-space:nowrap;align-self:center;"
            onmouseover="this.style.borderColor='#243b22';this.style.color='#243b22';this.style.background='#f0f7f0'"
            onmouseout="this.style.borderColor='#e5e7eb';this.style.color='#374151';this.style.background='#f9fafb'">
            <svg id="bd-plan-ico" xmlns="http://www.w3.org/2000/svg" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="23 4 23 10 17 10"/><path d="M20.49 15a9 9 0 1 1-.48-3.44"/></svg>
            Refresh
        </button>
    </div>

    {{-- ── Steadfast balance + ইন রিভিউ (নিকট অর্ডার API চেক) ── --}}
    <div style="background:#fff;border:1px solid #e5e7eb;border-radius:12px;padding:14px 20px;display:flex;align-items:flex-start;justify-content:space-between;flex-wrap:wrap;gap:10px;">
        <div style="display:flex;align-items:flex-start;gap:12px;flex:1;min-width:0;">
            <div style="width:38px;height:38px;border-radius:9px;background:#faf5ff;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#7c3aed" stroke-width="2"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg>
            </div>
            <div style="min-width:0;">
                <span style="font-size:11px;font-weight:600;letter-spacing:.6px;text-transform:uppercase;color:#9ca3af;">Steadfast Courier</span>
                <div id="sf-main" style="font-size:17px;font-weight:700;color:#111827;margin-top:2px;line-height:1.25;">—</div>
                <div id="sf-detail" style="font-size:12px;color:#6b7280;margin-top:6px;line-height:1.45;white-space:pre-line;"></div>
                <span id="sf-msg" style="font-size:11px;display:block;margin-top:4px;"></span>
            </div>
        </div>
        <button type="button" onclick="fetchSteadfastWidget()" id="sf-btn"
            style="display:inline-flex;align-items:center;gap:6px;background:#f9fafb;border:1px solid #e5e7eb;border-radius:8px;padding:7px 14px;font-size:12px;font-weight:500;color:#374151;cursor:pointer;transition:all .15s;white-space:nowrap;align-self:center;"
            onmouseover="this.style.borderColor='#7c3aed';this.style.color='#7c3aed';this.style.background='#faf5ff'"
            onmouseout="this.style.borderColor='#e5e7eb';this.style.color='#374151';this.style.background='#f9fafb'">
            <svg id="sf-ico" xmlns="http://www.w3.org/2000/svg" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="23 4 23 10 17 10"/><path d="M20.49 15a9 9 0 1 1-.48-3.44"/></svg>
            Refresh
        </button>
  </div>

    </div>

    {{-- ── Traffic Source Widget ── --}}
    <div style="background:#fff;border:1px solid #eaecf0;border-radius:12px;padding:18px 20px;margin-bottom:16px;">
        <div style="display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:8px;margin-bottom:14px;">
            <div>
                <h4 style="font-size:14px;font-weight:700;color:#1a1f2e;margin:0;">Traffic Source</h4>
                <p style="font-size:11px;color:#9ca3af;margin:2px 0 0;">অর্ডার কোথা থেকে এসেছে &bull; মোট {{ $trafficTotal }} অর্ডার</p>
            </div>
            <span style="font-size:11px;color:#6b7280;background:#f9fafb;border:1px solid #e5e7eb;border-radius:6px;padding:4px 10px;">Auto-tracked</span>
        </div>

        @if($trafficSources->isEmpty())
        <div style="text-align:center;padding:24px;color:#9ca3af;font-size:13px;">
            <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="#d1d5db" stroke-width="1.5" style="display:block;margin:0 auto 8px;"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
            নতুন অর্ডার আসলে এখানে দেখা যাবে
        </div>
        @else
        <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(180px,1fr));gap:10px;">
            @foreach($trafficSources as $src)
            @php $pct = $trafficTotal > 0 ? round(($src['count'] / $trafficTotal) * 100, 1) : 0; @endphp
            <div style="background:#f9fafb;border:1px solid #eaecf0;border-radius:10px;padding:12px 14px;">
                <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:8px;">
                    <div style="display:flex;align-items:center;gap:7px;">
                        @if($src['source'] === 'facebook')
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="#1877F2"><path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/></svg>
                        @elseif($src['source'] === 'google')
                            <svg width="18" height="18" viewBox="0 0 24 24"><path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/><path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/><path fill="#FBBC05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z"/><path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z"/></svg>
                        @elseif($src['source'] === 'whatsapp')
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="#25D366"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
                        @elseif($src['source'] === 'tiktok')
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="#010101"><path d="M19.59 6.69a4.83 4.83 0 01-3.77-4.25V2h-3.45v13.67a2.89 2.89 0 01-2.88 2.5 2.89 2.89 0 01-2.89-2.89 2.89 2.89 0 012.89-2.89c.28 0 .54.04.79.1V9.01a6.33 6.33 0 00-.79-.05 6.34 6.34 0 00-6.34 6.34 6.34 6.34 0 006.34 6.34 6.34 6.34 0 006.33-6.34V8.69a8.07 8.07 0 004.74 1.53V6.79a4.86 4.86 0 01-.97-.1z"/></svg>
                        @elseif($src['source'] === 'instagram')
                            <svg width="18" height="18" viewBox="0 0 24 24"><defs><linearGradient id="ig{{ $loop->index }}" x1="0%" y1="100%" x2="100%" y2="0%"><stop offset="0%" stop-color="#f09433"/><stop offset="50%" stop-color="#dc2743"/><stop offset="100%" stop-color="#bc1888"/></linearGradient></defs><path fill="url(#ig{{ $loop->index }})" d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zM12 0C8.741 0 8.333.014 7.053.072 2.695.272.273 2.69.073 7.052.014 8.333 0 8.741 0 12c0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98C8.333 23.986 8.741 24 12 24c3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98C15.668.014 15.259 0 12 0zm0 5.838a6.162 6.162 0 100 12.324 6.162 6.162 0 000-12.324zM12 16a4 4 0 110-8 4 4 0 010 8zm6.406-11.845a1.44 1.44 0 100 2.881 1.44 1.44 0 000-2.881z"/></svg>
                        @elseif($src['source'] === 'youtube')
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="#FF0000"><path d="M23.495 6.205a3.007 3.007 0 00-2.088-2.088c-1.87-.501-9.396-.501-9.396-.501s-7.507-.01-9.396.501A3.007 3.007 0 00.527 6.205a31.247 31.247 0 00-.522 5.805 31.247 31.247 0 00.522 5.783 3.007 3.007 0 002.088 2.088c1.868.502 9.396.502 9.396.502s7.506 0 9.396-.502a3.007 3.007 0 002.088-2.088 31.247 31.247 0 00.5-5.783 31.247 31.247 0 00-.5-5.805zM9.609 15.601V8.408l6.264 3.602z"/></svg>
                        @elseif($src['source'] === 'twitter')
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="#1DA1F2"><path d="M23.953 4.57a10 10 0 01-2.825.775 4.958 4.958 0 002.163-2.723c-.951.555-2.005.959-3.127 1.184a4.92 4.92 0 00-8.384 4.482C7.69 8.095 4.067 6.13 1.64 3.162a4.822 4.822 0 00-.666 2.475c0 1.71.87 3.213 2.188 4.096a4.904 4.904 0 01-2.228-.616v.06a4.923 4.923 0 003.946 4.827 4.996 4.996 0 01-2.212.085 4.936 4.936 0 004.604 3.417 9.867 9.867 0 01-6.102 2.105c-.39 0-.779-.023-1.17-.067a13.995 13.995 0 007.557 2.209c9.053 0 13.998-7.496 13.998-13.985 0-.21 0-.42-.015-.63A9.935 9.935 0 0024 4.59z"/></svg>
                        @elseif($src['source'] === 'direct')
                            <div style="width:18px;height:18px;border-radius:50%;background:#6366f1;display:flex;align-items:center;justify-content:center;">
                                <svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="2.5"><path d="M3 9l9-7 9 7v11a2 2 0 01-2 2H5a2 2 0 01-2-2z"/></svg>
                            </div>
                        @else
                            <div style="width:18px;height:18px;border-radius:50%;background:#9ca3af;display:flex;align-items:center;justify-content:center;">
                                <svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="2.5"><circle cx="12" cy="12" r="10"/><line x1="2" y1="12" x2="22" y2="12"/><path d="M12 2a15.3 15.3 0 014 10 15.3 15.3 0 01-4 10 15.3 15.3 0 01-4-10 15.3 15.3 0 014-10z"/></svg>
                            </div>
                        @endif
                        <span style="font-size:12px;font-weight:600;color:#1a1f2e;">{{ $src['label'] }}</span>
                    </div>
                    <span style="font-size:13px;font-weight:700;color:#111827;">{{ number_format($src['count']) }}</span>
                </div>
                <div style="background:#e5e7eb;border-radius:4px;height:5px;overflow:hidden;">
                    <div style="height:100%;width:{{ $pct }}%;background:{{ $src['color'] }};border-radius:4px;"></div>
                </div>
                <span style="font-size:10px;color:#9ca3af;margin-top:4px;display:block;">{{ $pct }}%</span>
            </div>
            @endforeach
      </div>
        @endif
    </div>

    {{-- ── Stat Cards ── --}}
    <div class="stat-grid">
        <div class="sc">
            <div class="sc-ico" style="background:#eff6ff;">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#3b82f6" stroke-width="2"><circle cx="9" cy="21" r="1"/><circle cx="20" cy="21" r="1"/><path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"/></svg>
            </div>
            <div>
                <div class="sc-label">Total Orders</div>
                <div class="sc-val">{{ number_format($total_order) }}</div>
                <div class="sc-note">{{ $pending_orders }} pending</div>
            </div>
        </div>

        <div class="sc">
            <div class="sc-ico" style="background:#f0fdf4;">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#22c55e" stroke-width="2"><line x1="12" y1="1" x2="12" y2="23"/><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/></svg>
            </div>
            <div>
                <div class="sc-label">Total Revenue</div>
                <div class="sc-val">৳{{ number_format($total_revenue) }}</div>
                <div class="sc-note">{{ $total_delivery }} delivered</div>
            </div>
        </div>

        <div class="sc">
            <div class="sc-ico" style="background:#fffbeb;">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#f59e0b" stroke-width="2"><polyline points="23 6 13.5 15.5 8.5 10.5 1 18"/><polyline points="17 6 23 6 23 12"/></svg>
            </div>
            <div>
                <div class="sc-label">Pending Orders</div>
                <div class="sc-val">{{ number_format($pending_orders) }}</div>
                <div class="sc-note">Need attention</div>
      </div>
    </div>

        <div class="sc">
            <div class="sc-ico" style="background:#fff1f2;">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#f43f5e" stroke-width="2"><path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>
            </div>
            <div>
                <div class="sc-label">Low Stock</div>
                <div class="sc-val">{{ number_format($low_stock) }}</div>
                <div class="sc-note">Below 10 units</div>
      </div>
    </div>
  </div>

    {{-- ── Today's Snapshot ── --}}
    <div class="section-label">Today's Snapshot</div>
    <div class="snap-grid">
        <div class="sn">
            <div class="sn-top">
                <span class="sn-name">Orders</span>
                <span class="sn-badge" style="background:#eff6ff;">
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="#3b82f6" stroke-width="2"><circle cx="9" cy="21" r="1"/><circle cx="20" cy="21" r="1"/><path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"/></svg>
                </span>
            </div>
            <div class="sn-val">{{ $today_order }}</div>
            <div class="sn-sub">Orders placed today</div>
        </div>

        <div class="sn">
            <div class="sn-top">
                <span class="sn-name">Revenue</span>
                <span class="sn-badge" style="background:#f0fdf4;">
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="#22c55e" stroke-width="2"><line x1="12" y1="1" x2="12" y2="23"/><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/></svg>
                </span>
            </div>
            <div class="sn-val">৳{{ number_format($today_revenue) }}</div>
            <div class="sn-sub">Revenue today</div>
        </div>

        <div class="sn">
            <div class="sn-top">
                <span class="sn-name">Delivered</span>
                <span class="sn-badge" style="background:#f0fdf4;">
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="#22c55e" stroke-width="2"><polyline points="20 6 9 17 4 12"/></svg>
                </span>
            </div>
            <div class="sn-val">৳{{ number_format($today_delivered_revenue) }}</div>
            <div class="sn-sub">Delivered revenue</div>
      </div>
    </div>

    {{-- ── Quick Actions ── --}}
    <div class="section-label">Quick Actions</div>
    <div class="qa-grid">
        @can('product-create')
        <a href="{{ route('products.create') }}" class="qa">
            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke-width="2"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
            Add Product
        </a>
        @endcan
        @can('order-list')
        <a href="{{ route('admin.orders',['slug'=>'all']) }}" class="qa">
            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke-width="2"><path d="M9 5H7a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V7a2 2 0 0 0-2-2h-2"/><rect x="9" y="3" width="6" height="4" rx="2"/></svg>
            View Orders
        </a>
        @endcan
        @can('category-list')
        <a href="{{ route('categories.index') }}" class="qa">
            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke-width="2"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/></svg>
            Categories
        </a>
        @endcan
        @can('product-list')
        <a href="{{ route('inhouse.products.index') }}" class="qa">
            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke-width="2"><path d="M6 2L3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z"/><line x1="3" y1="6" x2="21" y2="6"/><path d="M16 10a4 4 0 0 1-8 0"/></svg>
            Products
        </a>
        @endcan
    </div>

    {{-- ── Charts ── --}}
    <div class="chart-row">
        <div class="chart-box">
            <div class="chart-box-title">Order Trend</div>
            <div class="chart-box-sub">Last 7 days</div>
            <div id="trendChart"></div>
        </div>
      <div class="chart-box">
            <div class="chart-box-title">Order Status</div>
            <div class="chart-box-sub">All time breakdown</div>
            <div id="statusChart"></div>
        </div>
    </div>

    {{-- ── Products & Categories ── --}}
    <div class="two-col">
        <div class="tbl-card">
            <div class="tbl-card-head">
                <h4>Products <span style="font-weight:400;color:#9ca3af;font-size:12px;">({{ $total_product }})</span></h4>
                @can('product-list')
                <a href="{{ route('inhouse.products.index') }}">View all</a>
                @endcan
            </div>
            <div class="tbl-card-body">
                <table class="table clean mb-0">
                    <thead><tr>
                        <th>Product</th>
                        <th>Category</th>
                        <th>Price</th>
                        <th>Stock</th>
                    </tr></thead>
                    <tbody>
                    @forelse($latest_products as $prod)
                    <tr>
                        <td>
                            <div style="display:flex;align-items:center;gap:10px;">
                                @if($prod->image && $prod->image->image)
                                    <img src="{{ asset($prod->image->image) }}" class="p-thumb" alt="">
                                @else
                                    <div class="p-thumb-ph">
                                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="3" width="20" height="14" rx="2"/></svg>
                                    </div>
                                @endif
                                <span style="font-weight:500;max-width:140px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;display:block;">{{ $prod->name }}</span>
                            </div>
                        </td>
                        <td style="color:#6b7280;">{{ $prod->category->name ?? '—' }}</td>
                        <td style="font-weight:600;">৳{{ number_format($prod->new_price ?? $prod->old_price ?? 0) }}</td>
                        <td>
                            <span class="sb {{ ($prod->stock ?? 0) < 10 ? 'sb-7' : 'sb-6' }}">
                                {{ $prod->stock ?? 0 }}
                            </span>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="4" class="text-center text-muted py-3">No products</td></tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="tbl-card">
            <div class="tbl-card-head">
                <h4>Product Categories</h4>
                @can('category-list')
                <a href="{{ route('categories.index') }}">View all</a>
                @endcan
            </div>
            <div class="tbl-card-body">
                @forelse($categories as $cat)
                <div class="cat-item">
                    <div class="cat-item-left">
                        <div class="cat-dot"></div>
                        <span>{{ $cat->name }}</span>
                    </div>
                    <span class="cat-pill">{{ $cat->products_count }}</span>
                </div>
                @empty
                <div class="text-center text-muted py-4">No categories</div>
                @endforelse
      </div>
    </div>
  </div>

    {{-- ── Recent Orders ── --}}
    <div class="tbl-card mb-4">
        <div class="tbl-card-head">
            <h4>Recent Orders</h4>
            @can('order-list')
            <a href="{{ route('admin.orders',['slug'=>'all']) }}">View all</a>
            @endcan
        </div>
        <div class="tbl-card-body">
            <table class="table clean mb-0">
                <thead><tr>
                <th>Customer</th>
                <th>Invoice</th>
                    <th>Amount</th>
                <th>Status</th>
                <th>Date</th>
                </tr></thead>
            <tbody>
                @forelse($latest_order as $order)
                <tr>
                    <td style="font-weight:500;">{{ $order->customer->name ?? 'Guest' }}</td>
                    <td style="color:#9ca3af;">#{{ $order->invoice_id ?? '—' }}</td>
                    <td style="font-weight:600;">৳{{ number_format($order->amount ?? 0) }}</td>
                    <td>
                        @php $s = (int)($order->order_status ?? 0); @endphp
                        <span class="sb sb-{{ in_array($s,[1,2,3,4,5,6,7]) ? $s : 1 }}">
                            @switch($s)
                                @case(1) Pending @break
                                @case(2) Confirmed @break
                                @case(3) Processing @break
                                @case(4) Picked @break
                                @case(5) Shipped @break
                                @case(6) Delivered @break
                                @case(7) Cancelled @break
                                @default {{ $order->status->name ?? 'Unknown' }}
                            @endswitch
                        </span>
                </td>
                    <td style="color:#9ca3af;white-space:nowrap;">{{ optional($order->created_at)->format('d M Y') }}</td>
              </tr>
            @empty
                <tr><td colspan="5" class="text-center text-muted py-3">No orders yet</td></tr>
            @endforelse
            </tbody>
          </table>
        </div>
      </div>

    {{-- ── Finance ── --}}
    <div class="section-label">Finance Summary</div>
    <div class="fin-strip">
        <div class="fin-item">
            <div class="fin-item-label">Fund Balance</div>
            <div class="fin-item-val">৳{{ number_format($fund_balance) }}</div>
        </div>
        <div class="fin-item">
            <div class="fin-item-label">Total Expenses</div>
            <div class="fin-item-val">৳{{ number_format($total_expenses) }}</div>
        </div>
        <div class="fin-item">
            <div class="fin-item-label">Today's Expenses</div>
            <div class="fin-item-val">৳{{ number_format($today_expenses) }}</div>
    </div>
        <div class="fin-item">
            <div class="fin-item-label">Monthly Expenses</div>
            <div class="fin-item-val">৳{{ number_format($monthly_expenses) }}</div>
    </div>
  </div>

</div>

@include('backEnd.admin.partials.new_orders_popup')
@endsection

@section('script')
<script src="https://cdn.jsdelivr.net/npm/apexcharts@3.44.0"></script>
<script>
// ── 7-Day Trend ──
new ApexCharts(document.querySelector("#trendChart"), {
    chart: {
        type: 'area', height: 200,
        toolbar: { show: false },
        fontFamily: 'Inter, sans-serif',
        sparkline: { enabled: false }
    },
    series: [{ name: 'Orders', data: @json($trend_data) }],
    xaxis: {
        categories: @json($trend_labels),
        labels: { style: { fontSize: '11px', colors: '#9ca3af' } },
        axisBorder: { show: false },
        axisTicks: { show: false }
    },
    yaxis: {
        labels: { style: { fontSize: '11px', colors: '#9ca3af' } },
        min: 0
    },
    stroke: { curve: 'smooth', width: 2 },
    fill: {
        type: 'gradient',
        gradient: { shadeIntensity: 1, opacityFrom: 0.25, opacityTo: 0.02, stops: [0, 100] }
    },
    colors: ['#243b22'],
    dataLabels: { enabled: false },
    grid: { borderColor: '#f3f4f6', strokeDashArray: 3, padding: { top: 0, bottom: 0 } },
    tooltip: {
        style: { fontSize: '12px' },
        y: { formatter: v => v + ' orders' }
    },
}).render();

// ── Status Donut ──
new ApexCharts(document.querySelector("#statusChart"), {
    chart: {
        type: 'donut', height: 220,
        fontFamily: 'Inter, sans-serif'
    },
    labels: @json($statusLabels),
    series: @json($statusData),
    colors: ['#f59e0b','#3b82f6','#8b5cf6','#f97316','#06b6d4','#22c55e','#ef4444'],
    legend: {
        position: 'bottom', fontSize: '12px',
        markers: { width: 8, height: 8, radius: 8 },
        itemMargin: { horizontal: 6 }
    },
    dataLabels: { enabled: false },
    plotOptions: {
        pie: {
            donut: {
                size: '68%',
                labels: {
                    show: true,
                    name: { fontSize: '12px', color: '#9ca3af', offsetY: 4 },
                    value: { fontSize: '20px', fontWeight: 700, color: '#111827', offsetY: -4 },
                    total: {
                        show: true, label: 'Total',
                        color: '#9ca3af', fontSize: '12px',
                        formatter: w => w.globals.seriesTotals.reduce((a,b) => a+b, 0)
                    }
                }
            }
        }
    },
    stroke: { width: 2, colors: ['#fff'] },
    tooltip: { style: { fontSize: '12px' }, y: { formatter: v => v + ' orders' } },
}).render();

// ── SMS Balance ──
const SMS_BAL_URL = "{{ route('smsgeteway.balance') }}";

function fetchSmsBalance() {
    const btn = document.getElementById('sms-bal-btn');
    const val = document.getElementById('sms-bal-val');
    const msg = document.getElementById('sms-bal-msg');
    const ico = document.getElementById('sms-bal-ico');

    if (btn) btn.disabled = true;
    if (ico) ico.style.animation = 'spin .7s linear infinite';
    val.innerHTML = '<span style="font-size:13px;color:#9ca3af;font-weight:500;">Loading...</span>';
    msg.textContent = '';

    fetch(SMS_BAL_URL, {
        headers: {
            'Accept':        'application/json',
            'X-Requested-With': 'XMLHttpRequest',
            'X-CSRF-TOKEN':  '{{ csrf_token() }}'
        }
    })
    .then(function(r) {
        // If response is a redirect or HTML (license/auth page), handle gracefully
        const ct = r.headers.get('content-type') || '';
        if (!ct.includes('application/json')) {
            return r.text().then(function(txt) {
                throw new Error('Server returned non-JSON response (status ' + r.status + '). Possibly license or auth redirect.');
            });
        }
        return r.json();
    })
    .then(function(d) {
        if (btn) { btn.disabled = false; btn.textContent = ''; btn.innerHTML = '<svg id="sms-bal-ico" xmlns="http://www.w3.org/2000/svg" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="23 4 23 10 17 10"/><path d="M20.49 15a9 9 0 1 1-.48-3.44"/></svg> Refresh'; }
        if (ico && document.getElementById('sms-bal-ico')) document.getElementById('sms-bal-ico').style.animation = '';

        if (d.success) {
            const raw = String(d.balance);
            const num = raw.match(/[\d.]+/);
            val.textContent = num
                ? parseFloat(num[0]).toLocaleString('en-BD', { minimumFractionDigits: 2 }) + ' tk'
                : raw;
            val.style.color = '#111827';
            msg.textContent = 'Updated ' + new Date().toLocaleTimeString('en-BD');
            msg.style.color = '#22c55e';
        } else {
            val.textContent = '—';
            val.style.color = '#9ca3af';
            msg.textContent = d.message || 'Could not fetch balance';
            msg.style.color = '#ef4444';
        }
    })
    .catch(function(err) {
        if (btn) { btn.disabled = false; btn.innerHTML = '<svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="23 4 23 10 17 10"/><path d="M20.49 15a9 9 0 1 1-.48-3.44"/></svg> Retry'; }
        val.textContent = '—';
        val.style.color = '#9ca3af';
        // Show useful debug info
        const errTxt = err && err.message ? err.message : 'Request failed';
        if (errTxt.includes('non-JSON')) {
            msg.textContent = 'Session expired — please reload page';
        } else if (errTxt.includes('Failed to fetch')) {
            msg.textContent = 'Cannot connect to server';
        } else {
            msg.textContent = errTxt.substring(0, 60);
        }
        msg.style.color = '#ef4444';
    });
}

// Auto-load after page is fully ready
document.addEventListener('DOMContentLoaded', function() {
    setTimeout(fetchSmsBalance, 500); // slight delay so page renders first
    setTimeout(fetchBdCourierPlan, 700);
    setTimeout(fetchSteadfastWidget, 900);
});

const BD_PLAN_URL = "{{ route('bdcourier.myplan') }}";
const SF_WIDGET_URL = "{{ route('steadfast.dashboard.widget') }}";

function fetchSteadfastWidget() {
    var btn   = document.getElementById('sf-btn');
    var main  = document.getElementById('sf-main');
    var det   = document.getElementById('sf-detail');
    var msgEl = document.getElementById('sf-msg');
    var ico   = document.getElementById('sf-ico');

    if (btn)  btn.disabled = true;
    if (ico)  ico.style.animation = 'spin .7s linear infinite';
    if (main) main.textContent = '…';
    if (det)  det.textContent = '';
    if (msgEl){ msgEl.textContent = ''; msgEl.style.color = ''; }

    fetch(SF_WIDGET_URL, {
        headers: {
            'Accept': 'application/json',
            'X-Requested-With': 'XMLHttpRequest',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        }
    })
    .then(function(r) {
        var ct = r.headers.get('content-type') || '';
        if (!ct.includes('application/json')) throw new Error('non-JSON');
        return r.json();
    })
    .then(function(d) {
        if (btn) {
            btn.disabled = false;
            btn.innerHTML = '<svg id="sf-ico" xmlns="http://www.w3.org/2000/svg" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="23 4 23 10 17 10"/><path d="M20.49 15a9 9 0 1 1-.48-3.44"/></svg> Refresh';
        }
        ico = document.getElementById('sf-ico');
        if (ico) ico.style.animation = '';

        if (!d.success) {
            if (main) { main.textContent = '—'; main.style.color = '#9ca3af'; }
            if (msgEl){ msgEl.textContent = d.message || 'লোড করতে পারিনি'; msgEl.style.color = '#ef4444'; }
            return;
        }

        if (main) main.style.color = '#111827';
        if (main) main.textContent = d.balance_display != null && d.balance_display !== '' ? d.balance_display : '৳—';

        var invLine = 'ইন রিভিউতে: ' + (d.in_review_count != null ? d.in_review_count : '—') + ' টি পার্সেল';
        if (d.in_review_checked != null && d.in_review_sample_cap != null && d.in_review_sample_cap > 0) {
            invLine += '\n(শেষ ' + d.in_review_sample_cap + ' টি অর্ডার API চেক; সফল ' + d.in_review_checked + ' টি)';
        } else if (d.in_review_sample_cap === 0) {
            invLine += '\n(এখন কুরিয়ারে পাঠানো সক্রিয় অর্ডার নেই)';
        }

        var pendLine = 'কুরিয়ারে মোট (অ্যাপ): ' + (d.courier_pending_orders != null ? d.courier_pending_orders : '—') + ' টি অর্ডার';

        if (det) det.textContent = [invLine, pendLine].join('\n');
    })
    .catch(function() {
        if (btn) {
            btn.disabled = false;
            btn.innerHTML = '<svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="23 4 23 10 17 10"/><path d="M20.49 15a9 9 0 1 1-.48-3.44"/></svg> Retry';
        }
        if (main) main.textContent = '—';
        if (det)  det.textContent = '';
        if (msgEl){ msgEl.textContent = 'সার্ভার বা নেটওয়ার্ক সমস্যা'; msgEl.style.color = '#ef4444'; }
    });
}

function fetchBdCourierPlan() {
    var btn  = document.getElementById('bd-plan-btn');
    var main = document.getElementById('bd-plan-main');
    var det  = document.getElementById('bd-plan-detail');
    var msg  = document.getElementById('bd-plan-msg');
    var ico  = document.getElementById('bd-plan-ico');

    if (btn)  btn.disabled = true;
    if (ico)  ico.style.animation = 'spin .7s linear infinite';
    if (main) main.textContent = '…';
    if (det)  det.textContent = '';
    if (msg)  { msg.textContent = ''; msg.style.color = ''; }

    fetch(BD_PLAN_URL, {
        headers: {
            'Accept': 'application/json',
            'X-Requested-With': 'XMLHttpRequest',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        }
    })
    .then(function(r) {
        var ct = r.headers.get('content-type') || '';
        if (!ct.includes('application/json')) {
            throw new Error('non-JSON');
        }
        return r.json();
    })
    .then(function(d) {
        if (btn) {
            btn.disabled = false;
            btn.innerHTML = '<svg id="bd-plan-ico" xmlns="http://www.w3.org/2000/svg" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="23 4 23 10 17 10"/><path d="M20.49 15a9 9 0 1 1-.48-3.44"/></svg> Refresh';
        }
        ico = document.getElementById('bd-plan-ico');
        if (ico) ico.style.animation = '';

        if (!d.success) {
            if (main) { main.textContent = '—'; main.style.color = '#9ca3af'; }
            if (msg) { msg.textContent = d.message || 'লোড করতে পারিনি'; msg.style.color = '#ef4444'; }
            return;
        }

        var p = d.data || {};
        if (main) main.style.color = '#111827';

        var apiLine = 'API কল (ব্যবহার): ' + (p.api_calls != null && p.api_calls !== '' ? p.api_calls : '—');
        // API: data.next_due_date / expires_at — সার্ভার next_due_display আগে
        var nextDueRaw = p.next_due_display || p.next_due_date || p.expires_at || p.nextDueDate
            || (p.subscription && (p.subscription.next_due_date || p.subscription.nextDueDate))
            || (p.plan && (p.plan.next_due_date || p.plan.nextDueDate))
            || '';
        var dueLine = 'নেক্সট ডিউ ডেট: ' + (nextDueRaw || '—');
        var planDetailLines = [apiLine, dueLine].join('\n');

        if (p.has_subscription === false) {
            if (main) main.textContent = 'কোনো সক্রিয় সাবস্ক্রিপশন নেই';
            if (det) det.textContent = planDetailLines;
            if (msg) { msg.textContent = ''; msg.style.color = ''; }
            return;
        }

        var title = p.plan_name || 'BD Courier';
        if (main) main.textContent = title;

        if (det) det.textContent = planDetailLines;
        if (msg) { msg.textContent = ''; msg.style.color = ''; }
    })
    .catch(function() {
        if (btn) {
            btn.disabled = false;
            btn.innerHTML = '<svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="23 4 23 10 17 10"/><path d="M20.49 15a9 9 0 1 1-.48-3.44"/></svg> Retry';
        }
        if (main) main.textContent = '—';
        if (det)  det.textContent = '';
        if (msg)  { msg.textContent = 'সার্ভার বা নেটওয়ার্ক সমস্যা'; msg.style.color = '#ef4444'; }
    });
}
</script>
<style>
@keyframes spin { to { transform: rotate(360deg); } }
#sms-bal-ico, #bd-plan-ico { display:inline-block; }
</style>
@include('backEnd.admin.partials.new_orders_popup_script')
@endsection
