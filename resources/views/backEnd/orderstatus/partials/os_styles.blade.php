<style>
    body { background: #eef1f8; }
    .order-status-shell {
        padding: 8px 0 28px;
        padding-left: max(12px, env(safe-area-inset-left));
        padding-right: max(12px, env(safe-area-inset-right));
    }
    .os-page-header {
        display: flex;
        flex-wrap: wrap;
        align-items: flex-start;
        justify-content: space-between;
        gap: 12px;
        margin-bottom: 16px;
    }
    .os-page-header h4 {
        margin: 0;
        font-weight: 700;
        color: #0f172a;
        font-size: 1.35rem;
    }
    .os-page-header .os-sub {
        font-size: 13px;
        color: #64748b;
        margin-top: 4px;
        max-width: 520px;
    }
    .os-badge-count {
        background: linear-gradient(135deg, #4f46e5, #6366f1);
        color: #fff;
        font-size: 12px;
        font-weight: 600;
        padding: 4px 12px;
        border-radius: 999px;
        margin-left: 6px;
        vertical-align: middle;
    }
    .os-btn-primary {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        background: linear-gradient(135deg, #4f46e5, #6366f1);
        border: none;
        color: #fff !important;
        font-weight: 600;
        padding: 9px 18px;
        border-radius: 999px;
        font-size: 13px;
        box-shadow: 0 6px 16px rgba(79, 70, 229, 0.28);
        text-decoration: none;
        transition: opacity 0.2s, transform 0.2s;
    }
    .os-btn-primary:hover { opacity: 0.95; color: #fff; transform: translateY(-1px); }
    .os-btn-ghost {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        background: #f8fafc;
        color: #475569;
        border: 1px solid #e2e8f0;
        font-weight: 600;
        padding: 9px 16px;
        border-radius: 999px;
        font-size: 13px;
        text-decoration: none;
        transition: background 0.2s;
    }
    .os-btn-ghost:hover { background: #f1f5f9; color: #334155; }
    .os-card {
        background: #fff;
        border-radius: 14px;
        border: 1px solid rgba(148, 163, 184, 0.28);
        box-shadow: 0 12px 28px rgba(15, 23, 42, 0.06);
        overflow: hidden;
        margin-bottom: 16px;
    }
    .os-card-head {
        padding: 14px 18px;
        border-bottom: 1px solid #e2e8f0;
        background: linear-gradient(180deg, #eef2ff 0%, #fff 100%);
        display: flex;
        align-items: center;
        gap: 10px;
    }
    .os-card-head h6 {
        margin: 0;
        font-size: 14px;
        font-weight: 700;
        color: #1e293b;
    }
    .os-card-icon {
        width: 36px;
        height: 36px;
        border-radius: 10px;
        background: linear-gradient(135deg, #e0e7ff, #c7d2fe);
        color: #4338ca;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 15px;
        flex-shrink: 0;
    }
    .os-card-body { padding: 18px; }
    .os-label {
        font-size: 11px;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.04em;
        color: #64748b;
        margin-bottom: 6px;
        display: block;
    }
    .os-input {
        border-radius: 10px;
        border: 1px solid #e2e8f0;
        font-size: 13px;
        padding: 10px 12px;
        width: 100%;
        background: #f8fafc;
        transition: border-color 0.2s, box-shadow 0.2s, background 0.2s;
    }
    .os-input:focus {
        background: #fff;
        border-color: #6366f1;
        box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.15);
        outline: none;
    }
    .os-table-rail {
        width: 100%;
        overflow-x: auto;
        -webkit-overflow-scrolling: touch;
        border-radius: 10px;
        border: 1px solid #e2e8f0;
    }
    .os-table { margin-bottom: 0; width: 100%; }
    .os-table thead { background: #f8fafc; }
    .os-table th {
        font-size: 11px;
        text-transform: uppercase;
        letter-spacing: 0.04em;
        color: #64748b;
        font-weight: 600;
        padding: 10px 14px;
        white-space: nowrap;
        border-bottom: 1px solid #e2e8f0;
    }
    .os-table td {
        padding: 12px 14px;
        font-size: 13px;
        color: #334155;
        vertical-align: middle;
        border-bottom: 1px solid #f1f5f9;
    }
    .os-table tbody tr:hover { background: #f5f3ff; }
    .os-status-name {
        font-weight: 700;
        color: #0f172a;
        display: inline-flex;
        align-items: center;
        gap: 8px;
    }
    .os-status-name i { color: #6366f1; font-size: 12px; }
    .os-pill-active {
        display: inline-block;
        background: linear-gradient(135deg, #ecfdf5, #d1fae5);
        color: #047857;
        font-weight: 600;
        font-size: 11px;
        padding: 5px 10px;
        border-radius: 999px;
        border: 1px solid #a7f3d0;
    }
    .os-pill-inactive {
        display: inline-block;
        background: #fef2f2;
        color: #b91c1c;
        font-weight: 600;
        font-size: 11px;
        padding: 5px 10px;
        border-radius: 999px;
        border: 1px solid #fecaca;
    }
    .os-row-actions {
        display: inline-flex;
        gap: 6px;
        justify-content: flex-end;
        flex-wrap: wrap;
    }
    .os-act-btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-height: 32px;
        min-width: 32px;
        padding: 0 12px;
        font-size: 12px;
        font-weight: 600;
        border-radius: 999px;
        border: 1px solid transparent;
        text-decoration: none;
        transition: all 0.2s ease;
        cursor: pointer;
        background: transparent;
    }
    .os-act-edit { background: #eef2ff; color: #4338ca; border-color: #c7d2fe; }
    .os-act-edit:hover { background: #4f46e5; color: #fff; }
    .os-act-delete { background: #fef2f2; color: #b91c1c; border-color: #fecaca; }
    .os-act-delete:hover { background: #dc2626; color: #fff; }
    .os-act-toggle-on { background: #fffbeb; color: #b45309; border-color: #fde68a; }
    .os-act-toggle-on:hover { background: #d97706; color: #fff; }
    .os-act-toggle-off { background: #ecfdf5; color: #047857; border-color: #a7f3d0; }
    .os-act-toggle-off:hover { background: #059669; color: #fff; }
    .os-empty {
        text-align: center;
        padding: 48px 20px;
        color: #94a3b8;
    }
    .os-empty i { font-size: 3rem; opacity: 0.35; display: block; margin-bottom: 12px; }
    .os-dt-wrap .dataTables_wrapper .dataTables_filter input,
    .os-dt-wrap .dataTables_wrapper .dataTables_length select {
        border-radius: 8px;
        border: 1px solid #e2e8f0;
        padding: 6px 10px;
        font-size: 13px;
    }
    .os-dt-wrap .dataTables_wrapper .dataTables_info,
    .os-dt-wrap .dataTables_wrapper .dataTables_paginate {
        font-size: 12px;
        color: #64748b;
        padding-top: 12px;
    }
    .os-dt-wrap .page-item.active .page-link {
        background: #4f46e5;
        border-color: #4f46e5;
    }
    .os-dt-wrap .page-link { border-radius: 8px; margin: 0 2px; }
    /* Form layout */
    .os-form-layout { align-items: flex-start; }
    .os-toggle-box {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 12px;
        padding: 14px 16px;
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        border-radius: 12px;
        margin-bottom: 16px;
    }
    .os-toggle-box h6 { margin: 0 0 2px; font-size: 14px; font-weight: 700; color: #0f172a; }
    .os-toggle-box p { margin: 0; font-size: 12px; color: #64748b; }
    .os-switch { position: relative; display: inline-block; width: 48px; height: 26px; flex-shrink: 0; }
    .os-switch input { opacity: 0; width: 0; height: 0; }
    .os-switch .os-slider {
        position: absolute;
        cursor: pointer;
        inset: 0;
        background: #cbd5e1;
        transition: 0.25s;
        border-radius: 999px;
    }
    .os-switch .os-slider:before {
        position: absolute;
        content: "";
        height: 20px;
        width: 20px;
        left: 3px;
        bottom: 3px;
        background: #fff;
        transition: 0.25s;
        border-radius: 50%;
        box-shadow: 0 2px 4px rgba(0,0,0,0.12);
    }
    .os-switch input:checked + .os-slider { background: linear-gradient(135deg, #4f46e5, #6366f1); }
    .os-switch input:checked + .os-slider:before { transform: translateX(22px); }
    .os-btn-save {
        width: 100%;
        background: linear-gradient(135deg, #4f46e5, #6366f1);
        border: none;
        color: #fff !important;
        font-weight: 600;
        padding: 11px 18px;
        border-radius: 999px;
        font-size: 13px;
        box-shadow: 0 6px 16px rgba(79, 70, 229, 0.28);
    }
    .os-btn-save:hover { opacity: 0.95; color: #fff; }
    .os-tip {
        display: flex;
        align-items: flex-start;
        gap: 10px;
        padding: 14px 16px;
        background: #eef2ff;
        border: 1px solid #c7d2fe;
        border-radius: 12px;
        font-size: 12px;
        color: #4338ca;
        line-height: 1.5;
    }
    .os-tip i { margin-top: 2px; }
</style>
