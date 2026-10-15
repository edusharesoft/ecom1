<?php $__env->startSection('title','Dashboard'); ?>

<?php $__env->startSection('css'); ?>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/apexcharts@3.44.0/dist/apexcharts.css">
<style>
.db-wrap { padding: 0 0 32px; }
.db-heading { display: flex; justify-content: space-between; align-items: flex-end; flex-wrap: wrap; gap: 8px; margin-bottom: 20px; }
.db-heading h1 { font-size: 21px; font-weight: 700; color: #111827; margin: 0; }
.db-heading p { font-size: 13px; color: #6b7280; margin: 0; }

/* courier / sms widgets: same shadowed white boxes as the rest */
.dash-top-row { display: grid; grid-template-columns: repeat(auto-fit, minmax(260px, 1fr)); gap: 16px; margin-bottom: 24px; }
.dash-top-row > div { border: 0 !important; border-radius: 10px !important; box-shadow: var(--pn-shadow); }

/* status badges */
.sb { display: inline-block; font-size: 11px; font-weight: 600; padding: 4px 10px; border-radius: 20px; white-space: nowrap; }
.sb-1 { background: #fef3c7; color: #92400e; } .sb-2 { background: #dbeafe; color: #1e40af; } .sb-3 { background: #ede9fe; color: #5b21b6; }
.sb-4 { background: #ffedd5; color: #9a3412; } .sb-5 { background: #cffafe; color: #155e75; } .sb-6 { background: #dcfce7; color: #166534; }
.sb-7 { background: #fee2e2; color: #991b1b; }

.p-thumb { width: 38px; height: 38px; border-radius: 8px; object-fit: cover; border: 1px solid #f3f4f6; flex-shrink: 0; }
.p-thumb-ph { width: 38px; height: 38px; border-radius: 8px; background: #f3f4f6; display: inline-flex; align-items: center; justify-content: center; color: #9ca3af; flex-shrink: 0; }
.dp .dp-body.p-0 { padding: 0 !important; }
.dp table.table td { font-size: 13.5px; vertical-align: middle; border-color: #f1f2f6; }
.cat-item { display: flex; align-items: center; justify-content: space-between; padding: 10px 20px; border-bottom: 1px dashed #eef0f4; font-size: 14px; color: #1f2937; }
.cat-item:last-child { border-bottom: 0; }
.cat-item .dot { width: 9px; height: 9px; border-radius: 50%; display: inline-block; margin-right: 10px; }
.cat-pill { background: #efe3fb; color: #6d28d9; font-size: 11.5px; font-weight: 600; padding: 3px 10px; border-radius: 20px; }
.src-box { background: #fafafe; border: 1px solid #eef0f4; border-radius: 10px; }
</style>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
<?php
    $tk = fn ($v) => '৳ '.number_format((float) $v, 0);
    $cards = [
        ['fa-cart-shopping', 'Total Orders', number_format($total_order), $pending_orders.' pending', '#bfd7fd', '#1d4ed8', route('admin.orders', ['slug' => 'all'])],
        ['fa-sack-dollar', 'Total Revenue', $tk($total_revenue), $total_delivery.' delivered', '#c0ead7', '#047857', route('admin.orders', ['slug' => 'all'])],
        ['fa-hourglass-half', 'Pending Orders', number_format($pending_orders), 'Need attention', '#fde6c2', '#c2410c', route('admin.orders', ['slug' => 'pending'])],
        ['fa-triangle-exclamation', 'Low Stock', number_format($low_stock), 'Below 10 units', '#fbd0d0', '#b91c1c', route('inhouse.products.index')],
        ['fa-bag-shopping', "Today's Orders", number_format($today_order), 'Orders placed today', '#d4c5f2', '#6d28d9', route('admin.orders', ['slug' => 'all'])],
        ['fa-coins', "Today's Revenue", $tk($today_revenue), now()->format('d M Y'), '#f3c9f9', '#a21caf', route('admin.orders', ['slug' => 'all'])],
        ['fa-truck-fast', 'Delivered Today', $tk($today_delivered_revenue), 'Delivered revenue', '#bfead6', '#15803d', route('admin.orders', ['slug' => 'all'])],
        ['fa-boxes-stacked', 'Total Products', number_format($total_product), 'In catalogue', '#bee8fb', '#0369a1', route('inhouse.products.index')],
        ['fa-wallet', 'Fund Balance', $tk($fund_balance), 'Fund / Account', '#bfd5fb', '#1e40af', route('admin.fund.index')],
        ['fa-money-bill-trend-up', 'Total Expenses', $tk($total_expenses), 'All time', '#f8bcd4', '#be185d', route('admin.expenses.index')],
        ['fa-receipt', "Today's Expenses", $tk($today_expenses), 'Today', '#fbd7f0', '#c026d3', route('admin.expenses.index')],
        ['fa-calendar-days', 'Monthly Expenses', $tk($monthly_expenses), now()->format('F Y'), '#d4c8f3', '#5b21b6', route('admin.expenses.index')],
    ];
    $catColors = ['#4f46e5', '#f97316', '#10b981', '#ec4899', '#0ea5e9', '#eab308', '#8b5cf6', '#64748b'];
?>
<div class="db-wrap">

    <div class="db-heading">
        <h1>Dashboard</h1>
        <p><i class="fa-regular fa-calendar me-1"></i><?php echo e(now()->format('l, F j, Y')); ?></p>
    </div>

    
    <div class="row g-4 mb-4">
        <?php $__currentLoopData = $cards; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as [$icon, $label, $value, $sub, $bg, $color, $link]): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <div class="col-6 col-md-4 col-xl-2">
                <a href="<?php echo e($link); ?>" class="st-card" style="background:<?php echo e($bg); ?>">
                    <span class="st-l"><?php echo e($label); ?></span>
                    <span class="st-ic" style="color:<?php echo e($color); ?>"><i class="fa-solid <?php echo e($icon); ?>"></i></span>
                    <span class="st-v" style="color:<?php echo e($color); ?>"><?php echo e($value); ?></span>
                    <span class="st-s"><?php echo e($sub); ?></span>
                </a>
            </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </div>

    
    <div class="dash-top-row">

    
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

    
    <div class="row g-4 mb-4">
        <div class="col-lg-7">
            <div class="dp purple">
                <div class="dp-head"><h6><i class="fa-solid fa-chart-line me-1" style="color:#7c3aed"></i> Order Trend</h6><span class="small text-muted">Last 7 days</span></div>
                <div class="dp-body"><div id="trendChart"></div></div>
            </div>
        </div>
        <div class="col-lg-5">
            <div class="dp green">
                <div class="dp-head"><h6><i class="fa-solid fa-chart-pie me-1 text-success"></i> Order Status</h6><span class="small text-muted">All time</span></div>
                <div class="dp-body"><div id="statusChart"></div></div>
            </div>
        </div>
    </div>

    
    <div class="dp blue mb-4">
        <div class="dp-head">
            <h6><i class="fa-solid fa-signal me-1 text-primary"></i> Traffic Source <small class="text-muted" style="font-size:12px">অর্ডার কোথা থেকে এসেছে &bull; মোট <?php echo e($trafficTotal); ?> অর্ডার</small></h6>
            <span class="badge bg-light text-dark border">Auto-tracked</span>
        </div>
        <div class="dp-body">
        <?php if($trafficSources->isEmpty()): ?>
        <div style="text-align:center;padding:24px;color:#9ca3af;font-size:13px;">
            <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="#d1d5db" stroke-width="1.5" style="display:block;margin:0 auto 8px;"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
            নতুন অর্ডার আসলে এখানে দেখা যাবে
        </div>
        <?php else: ?>
        <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(180px,1fr));gap:10px;">
            <?php $__currentLoopData = $trafficSources; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $src): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <?php $pct = $trafficTotal > 0 ? round(($src['count'] / $trafficTotal) * 100, 1) : 0; ?>
            <div style="background:#f9fafb;border:1px solid #eaecf0;border-radius:10px;padding:12px 14px;">
                <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:8px;">
                    <div style="display:flex;align-items:center;gap:7px;">
                        <?php if($src['source'] === 'facebook'): ?>
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="#1877F2"><path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/></svg>
                        <?php elseif($src['source'] === 'google'): ?>
                            <svg width="18" height="18" viewBox="0 0 24 24"><path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/><path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/><path fill="#FBBC05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z"/><path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z"/></svg>
                        <?php elseif($src['source'] === 'whatsapp'): ?>
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="#25D366"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
                        <?php elseif($src['source'] === 'tiktok'): ?>
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="#010101"><path d="M19.59 6.69a4.83 4.83 0 01-3.77-4.25V2h-3.45v13.67a2.89 2.89 0 01-2.88 2.5 2.89 2.89 0 01-2.89-2.89 2.89 2.89 0 012.89-2.89c.28 0 .54.04.79.1V9.01a6.33 6.33 0 00-.79-.05 6.34 6.34 0 00-6.34 6.34 6.34 6.34 0 006.34 6.34 6.34 6.34 0 006.33-6.34V8.69a8.07 8.07 0 004.74 1.53V6.79a4.86 4.86 0 01-.97-.1z"/></svg>
                        <?php elseif($src['source'] === 'instagram'): ?>
                            <svg width="18" height="18" viewBox="0 0 24 24"><defs><linearGradient id="ig<?php echo e($loop->index); ?>" x1="0%" y1="100%" x2="100%" y2="0%"><stop offset="0%" stop-color="#f09433"/><stop offset="50%" stop-color="#dc2743"/><stop offset="100%" stop-color="#bc1888"/></linearGradient></defs><path fill="url(#ig<?php echo e($loop->index); ?>)" d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zM12 0C8.741 0 8.333.014 7.053.072 2.695.272.273 2.69.073 7.052.014 8.333 0 8.741 0 12c0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98C8.333 23.986 8.741 24 12 24c3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98C15.668.014 15.259 0 12 0zm0 5.838a6.162 6.162 0 100 12.324 6.162 6.162 0 000-12.324zM12 16a4 4 0 110-8 4 4 0 010 8zm6.406-11.845a1.44 1.44 0 100 2.881 1.44 1.44 0 000-2.881z"/></svg>
                        <?php elseif($src['source'] === 'youtube'): ?>
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="#FF0000"><path d="M23.495 6.205a3.007 3.007 0 00-2.088-2.088c-1.87-.501-9.396-.501-9.396-.501s-7.507-.01-9.396.501A3.007 3.007 0 00.527 6.205a31.247 31.247 0 00-.522 5.805 31.247 31.247 0 00.522 5.783 3.007 3.007 0 002.088 2.088c1.868.502 9.396.502 9.396.502s7.506 0 9.396-.502a3.007 3.007 0 002.088-2.088 31.247 31.247 0 00.5-5.783 31.247 31.247 0 00-.5-5.805zM9.609 15.601V8.408l6.264 3.602z"/></svg>
                        <?php elseif($src['source'] === 'twitter'): ?>
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="#1DA1F2"><path d="M23.953 4.57a10 10 0 01-2.825.775 4.958 4.958 0 002.163-2.723c-.951.555-2.005.959-3.127 1.184a4.92 4.92 0 00-8.384 4.482C7.69 8.095 4.067 6.13 1.64 3.162a4.822 4.822 0 00-.666 2.475c0 1.71.87 3.213 2.188 4.096a4.904 4.904 0 01-2.228-.616v.06a4.923 4.923 0 003.946 4.827 4.996 4.996 0 01-2.212.085 4.936 4.936 0 004.604 3.417 9.867 9.867 0 01-6.102 2.105c-.39 0-.779-.023-1.17-.067a13.995 13.995 0 007.557 2.209c9.053 0 13.998-7.496 13.998-13.985 0-.21 0-.42-.015-.63A9.935 9.935 0 0024 4.59z"/></svg>
                        <?php elseif($src['source'] === 'direct'): ?>
                            <div style="width:18px;height:18px;border-radius:50%;background:#6366f1;display:flex;align-items:center;justify-content:center;">
                                <svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="2.5"><path d="M3 9l9-7 9 7v11a2 2 0 01-2 2H5a2 2 0 01-2-2z"/></svg>
                            </div>
                        <?php else: ?>
                            <div style="width:18px;height:18px;border-radius:50%;background:#9ca3af;display:flex;align-items:center;justify-content:center;">
                                <svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="2.5"><circle cx="12" cy="12" r="10"/><line x1="2" y1="12" x2="22" y2="12"/><path d="M12 2a15.3 15.3 0 014 10 15.3 15.3 0 01-4 10 15.3 15.3 0 01-4-10 15.3 15.3 0 014-10z"/></svg>
                            </div>
                        <?php endif; ?>
                        <span style="font-size:12px;font-weight:600;color:#1a1f2e;"><?php echo e($src['label']); ?></span>
                    </div>
                    <span style="font-size:13px;font-weight:700;color:#111827;"><?php echo e(number_format($src['count'])); ?></span>
                </div>
                <div style="background:#e5e7eb;border-radius:4px;height:5px;overflow:hidden;">
                    <div style="height:100%;width:<?php echo e($pct); ?>%;background:<?php echo e($src['color']); ?>;border-radius:4px;"></div>
                </div>
                <span style="font-size:10px;color:#9ca3af;margin-top:4px;display:block;"><?php echo e($pct); ?>%</span>
            </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
      </div>
        <?php endif; ?>
        </div>
    </div>

    
    <div class="dp yellow mb-4">
        <div class="dp-head">
            <h6><i class="fa-solid fa-bag-shopping me-1 text-warning"></i> Recent Orders</h6>
            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('order-list')): ?><a href="<?php echo e(route('admin.orders',['slug'=>'all'])); ?>" class="small">View all <i class="fa-solid fa-arrow-right"></i></a><?php endif; ?>
        </div>
        <div class="dp-body p-0">
            <div class="table-responsive">
                <table class="table mb-0">
                    <thead><tr><th>Customer</th><th>Invoice</th><th>Amount</th><th>Status</th><th>Date</th></tr></thead>
                    <tbody>
                    <?php $__empty_1 = true; $__currentLoopData = $latest_order; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $order): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <tr>
                            <td class="fw-semibold"><?php echo e($order->customer->name ?? 'Guest'); ?></td>
                            <td class="text-primary">#<?php echo e($order->invoice_id ?? '—'); ?></td>
                            <td class="fw-semibold">৳<?php echo e(number_format($order->amount ?? 0)); ?></td>
                            <td>
                                <?php $s = (int)($order->order_status ?? 0); ?>
                                <span class="sb sb-<?php echo e(in_array($s,[1,2,3,4,5,6,7]) ? $s : 1); ?>">
                                    <?php switch($s):
                                        case (1): ?> Pending <?php break; ?>
                                        <?php case (2): ?> Confirmed <?php break; ?>
                                        <?php case (3): ?> Processing <?php break; ?>
                                        <?php case (4): ?> Picked <?php break; ?>
                                        <?php case (5): ?> Shipped <?php break; ?>
                                        <?php case (6): ?> Delivered <?php break; ?>
                                        <?php case (7): ?> Cancelled <?php break; ?>
                                        <?php default: ?> <?php echo e($order->status->name ?? 'Unknown'); ?>

                                    <?php endswitch; ?>
                                </span>
                            </td>
                            <td class="text-muted text-nowrap"><?php echo e(optional($order->created_at)->format('d M, Y')); ?><br><small><?php echo e(optional($order->created_at)->format('h:i A')); ?></small></td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr><td colspan="5" class="text-center text-muted py-5"><i class="fa-regular fa-folder-open fs-2 d-block mb-2"></i>No orders yet</td></tr>
                    <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    
    <div class="row g-4 mb-4">
        <div class="col-lg-7">
            <div class="dp pink">
                <div class="dp-head">
                    <h6><i class="fa-solid fa-boxes-stacked me-1 text-danger"></i> Products <small class="text-muted" style="font-size:12px">(<?php echo e($total_product); ?>)</small></h6>
                    <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('product-list')): ?><a href="<?php echo e(route('inhouse.products.index')); ?>" class="small">View all <i class="fa-solid fa-arrow-right"></i></a><?php endif; ?>
                </div>
                <div class="dp-body p-0">
                    <div class="table-responsive">
                        <table class="table mb-0">
                            <thead><tr><th>Product</th><th>Category</th><th>Price</th><th>Stock</th></tr></thead>
                            <tbody>
                            <?php $__empty_1 = true; $__currentLoopData = $latest_products; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $prod): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center gap-2">
                                            <?php if($prod->image && $prod->image->image): ?>
                                                <img src="<?php echo e(asset($prod->image->image)); ?>" class="p-thumb" alt="">
                                            <?php else: ?>
                                                <span class="p-thumb-ph"><i class="fa-regular fa-image"></i></span>
                                            <?php endif; ?>
                                            <span class="fw-medium text-truncate d-block" style="max-width:180px"><?php echo e($prod->name); ?></span>
                                        </div>
                                    </td>
                                    <td class="text-muted"><?php echo e($prod->category->name ?? '—'); ?></td>
                                    <td class="fw-semibold">৳<?php echo e(number_format($prod->new_price ?? $prod->old_price ?? 0)); ?></td>
                                    <td><span class="sb <?php echo e(($prod->stock ?? 0) < 10 ? 'sb-7' : 'sb-6'); ?>"><?php echo e($prod->stock ?? 0); ?></span></td>
                                </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                <tr><td colspan="4" class="text-center text-muted py-4">No products</td></tr>
                            <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-5">
            <div class="dp orange">
                <div class="dp-head">
                    <h6><i class="fa-solid fa-sitemap me-1" style="color:#ea580c"></i> Product Categories</h6>
                    <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('category-list')): ?><a href="<?php echo e(route('categories.index')); ?>" class="small">View all <i class="fa-solid fa-arrow-right"></i></a><?php endif; ?>
                </div>
                <div class="dp-body p-0">
                    <?php $__empty_1 = true; $__currentLoopData = $categories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $cat): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <div class="cat-item">
                            <span><span class="dot" style="background:<?php echo e($catColors[$loop->index % 8]); ?>"></span><?php echo e($cat->name); ?></span>
                            <span class="cat-pill"><?php echo e($cat->products_count); ?></span>
                        </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <div class="text-center text-muted py-5">No categories</div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    
    <div class="qa-grid mb-2">
        <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('product-create')): ?>
        <a href="<?php echo e(route('products.create')); ?>" class="qa"><span class="qi" style="background:#e0e7ff;color:#4338ca"><i class="fa-solid fa-circle-plus"></i></span>Add Product</a>
        <?php endif; ?>
        <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('order-list')): ?>
        <a href="<?php echo e(route('admin.orders',['slug'=>'all'])); ?>" class="qa"><span class="qi" style="background:#fef3c7;color:#b45309"><i class="fa-solid fa-clipboard-list"></i></span>View Orders</a>
        <?php endif; ?>
        <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('category-list')): ?>
        <a href="<?php echo e(route('categories.index')); ?>" class="qa"><span class="qi" style="background:#dcfce7;color:#15803d"><i class="fa-solid fa-table-cells-large"></i></span>Categories</a>
        <?php endif; ?>
        <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('product-list')): ?>
        <a href="<?php echo e(route('inhouse.products.index')); ?>" class="qa"><span class="qi" style="background:#fce7f3;color:#be185d"><i class="fa-solid fa-bag-shopping"></i></span>Products</a>
        <?php endif; ?>
    </div>

</div>

<?php echo $__env->make('backEnd.admin.partials.new_orders_popup', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('script'); ?>
<script src="https://cdn.jsdelivr.net/npm/apexcharts@3.44.0"></script>
<script>
// ── 7-Day Trend ──
new ApexCharts(document.querySelector("#trendChart"), {
    chart: {
        type: 'area', height: 280,
        toolbar: { show: false },
        fontFamily: 'Poppins, Hind Siliguri, sans-serif',
        sparkline: { enabled: false }
    },
    series: [{ name: 'Orders', data: <?php echo json_encode($trend_data, 15, 512) ?> }],
    xaxis: {
        categories: <?php echo json_encode($trend_labels, 15, 512) ?>,
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
    colors: ['#2d2de8'],
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
        type: 'donut', height: 280,
        fontFamily: 'Poppins, Hind Siliguri, sans-serif'
    },
    labels: <?php echo json_encode($statusLabels, 15, 512) ?>,
    series: <?php echo json_encode($statusData, 15, 512) ?>,
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
const SMS_BAL_URL = "<?php echo e(route('smsgeteway.balance')); ?>";

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
            'X-CSRF-TOKEN':  '<?php echo e(csrf_token()); ?>'
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

const BD_PLAN_URL = "<?php echo e(route('bdcourier.myplan')); ?>";
const SF_WIDGET_URL = "<?php echo e(route('steadfast.dashboard.widget')); ?>";

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
            'X-CSRF-TOKEN': '<?php echo e(csrf_token()); ?>'
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
            'X-CSRF-TOKEN': '<?php echo e(csrf_token()); ?>'
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
<?php echo $__env->make('backEnd.admin.partials.new_orders_popup_script', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('backEnd.layouts.master', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\laragon\www\ecom1\resources\views/backEnd/admin/dashboard.blade.php ENDPATH**/ ?>