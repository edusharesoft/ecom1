
<style>
/* ==========================================================================
   Multibranch-style back-office theme for ecom1 (Admin / Reseller / Vendor / Delivery)
   Same look as the Multi-Branch SaaS platform: white boxed sidebar with colourful
   icons, shadowed topbar, pastel statistic cards, colour-headed panels.
   ========================================================================== */
:root { --sb-w: 270px; --sb-mini: 80px; --pn-shadow: 0 6px 16px rgba(15, 23, 42, .10); --pn-shadow-lg: 0 10px 24px rgba(15, 23, 42, .16); }

body.panel-ui { background: #f3f4f8; font-family: 'Poppins', 'Hind Siliguri', system-ui, sans-serif; color: #1f2330; }
.panel-ui a { text-decoration: none; }

/* ---------- base layout ---------- */
.panel-ui .mg-sidebar { position: fixed; top: 0; bottom: 0; left: 0; z-index: 1030; transition: .2s; overflow-y: auto; overflow-x: hidden; scrollbar-width: thin; }
.panel-ui .mg-main { min-height: 100vh; }
.panel-ui .mg-topbar { background: #fff; display: flex; align-items: center; justify-content: space-between; position: sticky; top: 0; z-index: 1000; }
.panel-ui .mg-content { padding: 26px 22px; }
/* ---------- sidebar ---------- */
.panel-ui .mg-sidebar { width: var(--sb-w); background: #fff; color: #1f2937; box-shadow: 4px 0 18px rgba(15, 23, 42, .10); padding: 0 0 18px; }
.panel-ui .mg-sidebar .brand { color: #111827; border-bottom: 1px solid #e5e7eb; margin: 0 18px 12px; padding: 18px 4px 14px; font-size: 17px; display: flex; align-items: center; gap: 10px; flex-wrap: wrap; }
.panel-ui .mg-sidebar .brand .logo-ic { width: 40px; height: 40px; border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 19px; color: #fff; background: linear-gradient(135deg, #6366f1, #ec4899); box-shadow: 0 4px 10px rgba(99, 102, 241, .35); flex-shrink: 0; }
.panel-ui .mg-sidebar .brand .logo-img { width: 40px; height: 40px; border-radius: 10px; object-fit: contain; background: #fff; border: 1px solid #e5e7eb; padding: 2px; flex-shrink: 0; }
.panel-ui .mg-sidebar .brand .t { min-width: 0; flex: 1; line-height: 1.2; }
.panel-ui .mg-sidebar .brand .t b { display: block; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
.panel-ui .mg-sidebar .brand small { color: #6b7280; font-size: 11px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
.panel-ui .mg-sidebar .sec { font-size: 10.5px; letter-spacing: 1px; text-transform: uppercase; color: #9ca3af; padding: 12px 26px 4px; font-weight: 600; }

.panel-ui .mg-sidebar .pm-item { display: flex; align-items: center; gap: 12px; margin: 0 18px 7px; padding: 8px 12px; border: 1px solid #e5e7eb; border-radius: 8px; background: #fff;
    color: #1f2937 !important; font-size: 14px; font-weight: 500; text-decoration: none; cursor: pointer; transition: background .15s, box-shadow .15s, transform .15s; width: calc(100% - 36px); text-align: left; }
.panel-ui .mg-sidebar .pm-item:hover { background: #f5f3ff; box-shadow: 0 3px 8px rgba(99, 102, 241, .14); transform: translateX(2px); }
.panel-ui .mg-sidebar .pm-item.active, .panel-ui .mg-sidebar .pm-item.open { background: #e8e7fb; border-color: #d4d2f7; }
.pm-item .pm-ic { width: 30px; height: 30px; border-radius: 8px; display: flex; align-items: center; justify-content: center; font-size: 15px; flex-shrink: 0; }
.pm-item .pm-label { flex: 1; min-width: 0; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
.pm-item .pm-chev { font-size: 11px; color: #6b7280; transition: transform .2s; }
.pm-item[aria-expanded="true"] .pm-chev { transform: rotate(90deg); }
.pm-item .badge { font-size: 10px; }
.pm-sub { margin: -2px 18px 8px 34px; border-left: 2px dashed #e0e0f5; padding-left: 8px; }
.panel-ui .mg-sidebar .pm-sub .pm-item { margin: 0 0 5px; width: 100%; padding: 6px 10px; font-size: 13px; }
.pm-sub .pm-item .pm-ic { width: 24px; height: 24px; font-size: 12px; }

/* mini (icons only) sidebar on desktop */
@media (min-width: 992px) {
    body.sb-mini .mg-sidebar { width: var(--sb-mini); }
    body.sb-mini .mg-main { margin-left: var(--sb-mini); }
    body.sb-mini .mg-sidebar .brand .t, body.sb-mini .mg-sidebar .sec, body.sb-mini .pm-label, body.sb-mini .pm-chev, body.sb-mini .pm-item .badge,
    body.sb-mini .mg-sidebar .brand .plan { display: none; }
    body.sb-mini .pm-item { justify-content: center; margin: 0 12px 7px; width: calc(100% - 24px); padding: 8px 0; }
    body.sb-mini .pm-sub { margin: 0 8px 8px; padding: 0; border: 0; }
    body.sb-mini .mg-sidebar .brand { justify-content: center; margin: 0 10px 12px; }
}
.panel-ui .mg-main { margin-left: var(--sb-w); transition: margin .2s; }

/* ---------- topbar ---------- */
.panel-ui .mg-topbar { padding: 0 0 0 18px; min-height: 66px; border-bottom: 0; box-shadow: 0 4px 14px rgba(15, 23, 42, .10); }
.panel-ui .mg-topbar .burger { border: 0; background: transparent; font-size: 22px; color: #111827; padding: 6px 10px; border-radius: 8px; }
.panel-ui .mg-topbar .burger:hover { background: #f3f4f6; }
.panel-ui .mg-topbar .page-title { font-size: 17px; font-weight: 600; color: #374151; }
.tb-right { display: flex; align-items: stretch; align-self: stretch; }
.tb-right > * { display: flex; align-items: center; padding: 0 18px; border-left: 1px solid #e5e7eb; }
.tb-user { gap: 10px; color: #0e7490 !important; font-weight: 600; font-size: 16px; text-decoration: none; }
.tb-user .avatar { width: 38px; height: 38px; border-radius: 50%; color: #fff; display: flex; align-items: center; justify-content: center; font-weight: 700; font-size: 16px;
    background: linear-gradient(135deg, #0ea5e9, #6366f1); box-shadow: 0 3px 8px rgba(14, 165, 233, .35); }
.tb-user small { display: block; font-size: 11px; color: #6b7280; font-weight: 500; line-height: 1; }
.tb-logout { border: 0; background: transparent; font-size: 22px; color: #e11d48; height: 100%; padding: 0 20px; }
.tb-logout:hover { background: #fff1f2; }
.panel-ui .mg-content { padding: 26px 22px; }

/* ---------- statistic cards ----------
   [label ........ icon]
   [big value          ]
   [sub text           ]                                                     */
.st-card { display: grid; grid-template-columns: 1fr auto; grid-template-areas: "l ic" "v v" "s s"; align-items: center; column-gap: 10px; row-gap: 4px;
    padding: 18px 18px 16px; border-radius: 10px; min-height: 128px; height: 100%; color: #1f2937 !important; text-decoration: none;
    box-shadow: var(--pn-shadow); transition: transform .18s, box-shadow .18s; position: relative; overflow: hidden; }
.st-card:hover { transform: translateY(-4px); box-shadow: var(--pn-shadow-lg); }
.st-card::after { content: ''; position: absolute; right: -30px; bottom: -34px; width: 104px; height: 104px; border-radius: 50%; background: rgba(255, 255, 255, .32); pointer-events: none; }
.st-card .st-ic { grid-area: ic; width: 54px; height: 54px; border-radius: 15px; background: rgba(255, 255, 255, .8); display: flex; align-items: center; justify-content: center;
    font-size: 26px; box-shadow: 0 4px 10px rgba(15, 23, 42, .10); }
.st-card .st-l { grid-area: l; font-size: 15px; font-weight: 600; color: #374151; line-height: 1.25; align-self: start; padding-top: 2px; }
.st-card .st-v { grid-area: v; font-size: clamp(20px, 1.55vw, 27px); font-weight: 800; line-height: 1.15; letter-spacing: -.3px; white-space: nowrap; position: relative; z-index: 1; margin-top: 6px; }
.st-card .st-s { grid-area: s; font-size: 12px; color: #4b5563; position: relative; z-index: 1; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
@media (max-width: 575px) { .st-card { padding: 14px 12px; min-height: 112px; } .st-card .st-ic { width: 42px; height: 42px; font-size: 20px; border-radius: 12px; } .st-card .st-v { font-size: 19px; } .st-card .st-l { font-size: 13px; } }

/* ---------- colour-headed panels ---------- */
.dp { background: #fff; border-radius: 6px; box-shadow: var(--pn-shadow); height: 100%; display: flex; flex-direction: column; padding: 4px; }
.dp-head { display: flex; align-items: center; justify-content: space-between; gap: 10px; padding: 14px 18px; min-height: 58px; border-radius: 3px; }
.dp-head h6 { margin: 0; font-size: 18px; font-weight: 500; color: #1f2937; }
.dp-head .form-select { width: auto; min-width: 120px; font-size: 14px; background-color: transparent; border-color: #9ca3af; }
.dp-body { padding: 18px 20px; flex: 1; }
.dp.yellow .dp-head { background: #f3fccc; } .dp.green .dp-head { background: #ddfbe2; } .dp.pink .dp-head { background: #fde1ea; }
.dp.purple .dp-head { background: #efe3fb; } .dp.blue .dp-head { background: #dbeafe; } .dp.orange .dp-head { background: #ffedd5; }

.no-list .row-o { display: flex; align-items: center; gap: 10px; padding: 7px 0; border-bottom: 1px dashed #eef0f4; text-decoration: none; color: inherit; }
.no-list .row-o:last-child { border-bottom: 0; }
.no-list .row-o:hover { background: #fafafe; }
.no-list .num { font-size: 12.5px; color: #2563eb; }
.no-list .who { font-size: 15px; color: #111827; }
.no-list .when { font-size: 12px; color: #6b7280; text-align: center; min-width: 110px; line-height: 1.6; }
.no-list .stat { min-width: 96px; text-align: center; }
.no-list .stat .badge { font-size: 10.5px; padding: 4px 8px; }
.chart-box { position: relative; height: 290px; }
.chart-box.sm { height: 260px; }

/* ---------- mobile: sidebar slides in ---------- */
@media (max-width: 991px) {
    .panel-ui .mg-sidebar { left: calc(-1 * var(--sb-w) - 20px); }
    .panel-ui .mg-sidebar.show { left: 0; }
    .panel-ui .mg-main { margin-left: 0; }
    .tb-right > * { padding: 0 10px; }
    .tb-user .nm { display: none; }
}
.sb-backdrop { display: none; }
@media (max-width: 991px) { .mg-sidebar.show + .sb-backdrop { display: block; position: fixed; inset: 0; background: rgba(15, 23, 42, .35); z-index: 1025; } }

/* ==========================================================================
   Legacy <ul> menus (kept for their permission checks) drawn like .pm-item
   <ul class="lm"> <li><a><i icon></i><span>Label</span><span class="menu-arrow"></span></a>
                        <div class="collapse"><ul class="nav-second-level"><li><a>…</a></li></ul></div></li>
   ========================================================================== */
.lm, .lm ul { list-style: none; margin: 0; padding: 0; }
.lm > li { --c: #6366f1; }
.lm > li:nth-child(12n+2) { --c: #16a34a; } .lm > li:nth-child(12n+3) { --c: #f59e0b; } .lm > li:nth-child(12n+4) { --c: #0ea5e9; }
.lm > li:nth-child(12n+5) { --c: #ec4899; } .lm > li:nth-child(12n+6) { --c: #f97316; } .lm > li:nth-child(12n+7) { --c: #8b5cf6; }
.lm > li:nth-child(12n+8) { --c: #14b8a6; } .lm > li:nth-child(12n+9) { --c: #ef4444; } .lm > li:nth-child(12n+10) { --c: #0284c7; }
.lm > li:nth-child(12n+11) { --c: #a855f7; } .lm > li:nth-child(12n+12) { --c: #64748b; }
.lm > li.lm-sec { padding: 0; }
.panel-ui .mg-sidebar .lm > li > a { display: flex; align-items: center; gap: 12px; margin: 0 18px 7px; padding: 8px 12px; border: 1px solid #e5e7eb; border-radius: 8px; background: #fff;
    color: #1f2937 !important; font-size: 14px; font-weight: 500; text-decoration: none; cursor: pointer; transition: background .15s, box-shadow .15s, transform .15s; min-height: 0; line-height: 1.3; }
.panel-ui .mg-sidebar .lm > li > a:hover { background: #f5f3ff; box-shadow: 0 3px 8px rgba(99, 102, 241, .14); transform: translateX(2px); }
.panel-ui .mg-sidebar .lm > li > a.active, .panel-ui .mg-sidebar .lm > li.active > a, .panel-ui .mg-sidebar .lm > li.menuitem-active > a,
.panel-ui .mg-sidebar .lm > li > a[aria-expanded="true"] { background: #e8e7fb; border-color: #d4d2f7; }
.lm > li > a > i:first-child, .lm > li > a > svg:first-child { width: 30px; height: 30px; min-width: 30px; border-radius: 8px; display: inline-flex; align-items: center; justify-content: center;
    font-size: 15px; color: var(--c); background: #f3f4f6; background: color-mix(in srgb, var(--c) 12%, #fff); margin: 0 !important; }
.lm > li > a > span:not(.menu-arrow):not(.badge) { flex: 1; min-width: 0; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
.lm .menu-arrow { margin-left: auto; font-size: 11px; color: #6b7280; transition: transform .2s; display: inline-flex; }
.lm .menu-arrow::before { font-family: 'Font Awesome 6 Free'; font-weight: 900; content: '\f054'; }
.lm a[aria-expanded="true"] .menu-arrow, .lm li.menuitem-active > a .menu-arrow { transform: rotate(90deg); }
.lm .badge { font-size: 10px; margin-left: auto; float: none !important; }

.panel-ui .mg-sidebar .lm .nav-second-level { margin: -2px 18px 8px 34px; border-left: 2px dashed #e0e0f5; padding-left: 8px; }
.panel-ui .mg-sidebar .lm .nav-second-level li { padding: 0; }
.panel-ui .mg-sidebar .lm .nav-second-level li > a { display: flex; align-items: center; gap: 10px; margin: 0 0 5px; padding: 6px 10px; border: 1px solid #e5e7eb; border-radius: 8px; background: #fff;
    color: #1f2937 !important; font-size: 13px; font-weight: 500; line-height: 1.3; overflow: hidden; white-space: nowrap; text-overflow: ellipsis; transition: background .15s, transform .15s; }
.panel-ui .mg-sidebar .lm .nav-second-level li > a:hover { background: #f5f3ff; transform: translateX(2px); }
.panel-ui .mg-sidebar .lm .nav-second-level li > a.active, .panel-ui .mg-sidebar .lm .nav-second-level li.menuitem-active > a,
.panel-ui .mg-sidebar .lm .nav-second-level li.active > a { background: #e8e7fb; border-color: #d4d2f7; }
.lm .nav-second-level li > a > i:first-child, .lm .nav-second-level li > a > svg:first-child { width: 24px; height: 24px; min-width: 24px; border-radius: 7px; display: inline-flex; align-items: center; justify-content: center;
    font-size: 12px; color: var(--c); background: #f3f4f6; background: color-mix(in srgb, var(--c) 12%, #fff); margin: 0 !important; }
.lm .nav-second-level .dropdown-divider { margin: 6px 0; border-top: 1px dashed #e0e0f5; opacity: 1; }

@media (min-width: 992px) {
    body.sb-mini .lm > li > a > span, body.sb-mini .lm .badge { display: none !important; }
    body.sb-mini .panel-ui .mg-sidebar .lm > li > a, body.sb-mini.panel-ui .mg-sidebar .lm > li > a { justify-content: center; margin: 0 12px 7px; padding: 8px 0; }
    body.sb-mini.panel-ui .mg-sidebar .lm .nav-second-level { margin: 0 8px 8px; padding: 0; border: 0; }
    body.sb-mini.panel-ui .mg-sidebar .lm .nav-second-level li > a { font-size: 0; justify-content: center; padding: 6px 0; }
    body.sb-mini .mg-sidebar .tb-hide-mini { display: none !important; }
}

/* ---------- topbar extras ---------- */
.panel-ui .mg-topbar .tb-icon { color: #374151; font-size: 19px; position: relative; }
.panel-ui .mg-topbar .tb-icon .badge { position: absolute; top: -6px; right: -10px; font-size: 9px; }
.panel-ui .mg-topbar .dropdown-menu { border: 0; box-shadow: var(--pn-shadow-lg); border-radius: 10px; }
.panel-ui .mg-topbar .tb-user img.avatar { object-fit: cover; padding: 0; background: #fff; }
.panel-ui .mg-topbar .tb-right form { margin: 0; }

/* old template wrappers kept inside the new frame (UBold / custom panels) */
.panel-ui .mg-content .content-page { margin: 0 !important; padding: 0 !important; min-height: 0 !important; overflow: visible !important; }
.panel-ui .mg-content .content-page > .content { padding: 0 !important; }
.panel-ui .footer { position: static; left: 0; }

/* generic card polish so older pages sit nicely in the new frame */
.panel-ui .mg-content .card { border: 0; border-radius: 8px; box-shadow: var(--pn-shadow); }
.panel-ui .mg-content .page-title-box .page-title { font-weight: 600; }

/* quick links on dashboards */
.qa-grid { display: grid; grid-template-columns: repeat(4, 1fr); gap: 12px; }
@media (max-width: 700px) { .qa-grid { grid-template-columns: repeat(2, 1fr); } }
.qa-grid .qa { background: #fff; border-radius: 10px; box-shadow: var(--pn-shadow); padding: 14px 10px; text-align: center; color: #1f2937; font-weight: 500; font-size: 13.5px;
    display: flex; flex-direction: column; align-items: center; gap: 8px; transition: transform .15s, box-shadow .15s; }
.qa-grid .qa:hover { transform: translateY(-3px); box-shadow: var(--pn-shadow-lg); color: #1f2937; }
.qa-grid .qa .qi { width: 40px; height: 40px; border-radius: 11px; display: flex; align-items: center; justify-content: center; font-size: 17px; }
.dp table.table { margin: 0; }
.dp table.table th { font-size: 12px; font-weight: 600; color: #6b7280; text-transform: uppercase; letter-spacing: .4px; background: #fafafa; }
.min-w-0 { min-width: 0; }
.dp .dp-body.p-0 { padding: 0 !important; }
.dp table.table th, .dp table.table td { padding: 11px 16px; vertical-align: middle; }
.dp table.table th:first-child, .dp table.table td:first-child { padding-left: 20px; }
</style>

<?php /**PATH D:\laragon\www\ecom1\resources\views/partials/mb/panel-css.blade.php ENDPATH**/ ?>