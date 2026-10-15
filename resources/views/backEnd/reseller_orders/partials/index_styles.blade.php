<style>
    body { background: #eef1f8; }
    .reseller-orders-shell {
        padding: 8px 0 28px;
        padding-left: max(12px, env(safe-area-inset-left));
        padding-right: max(12px, env(safe-area-inset-right));
    }
    .ro-page-header {
        display: flex;
        flex-wrap: wrap;
        align-items: flex-start;
        justify-content: space-between;
        gap: 12px;
        margin-bottom: 16px;
    }
    .ro-page-header h4 { margin: 0; font-weight: 700; color: #0f172a; font-size: 1.35rem; }
    .ro-page-header .ro-sub { font-size: 13px; color: #64748b; margin-top: 4px; max-width: 520px; }
    .ro-badge-count {
        background: linear-gradient(135deg, #059669, #0d9488);
        color: #fff;
        font-size: 12px;
        font-weight: 600;
        padding: 4px 12px;
        border-radius: 999px;
        margin-left: 6px;
        vertical-align: middle;
    }
    .ro-info-pill {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        font-size: 12px;
        color: #0f766e;
        background: #ecfdf5;
        border: 1px solid #a7f3d0;
        padding: 6px 12px;
        border-radius: 999px;
        margin-top: 8px;
    }
    .ro-card {
        background: #fff;
        border-radius: 14px;
        border: 1px solid rgba(148, 163, 184, 0.28);
        box-shadow: 0 12px 28px rgba(15, 23, 42, 0.06);
        overflow: hidden;
        margin-bottom: 16px;
    }
    .ro-card-head {
        padding: 14px 18px;
        border-bottom: 1px solid #e2e8f0;
        background: linear-gradient(180deg, #f0fdfa 0%, #fff 100%);
    }
    .ro-card-head h6 { margin: 0; font-size: 14px; font-weight: 700; color: #1e293b; }
    .ro-card-head h6 i { color: #0d9488; margin-right: 6px; }
    .ro-card-body { padding: 16px 18px; }
    .ro-filter-grid {
        display: grid;
        grid-template-columns: 1fr;
        gap: 12px;
    }
    @media (min-width: 768px) {
        .ro-filter-grid { grid-template-columns: repeat(2, 1fr); }
    }
    @media (min-width: 1200px) {
        .ro-filter-grid { grid-template-columns: repeat(4, 1fr); }
    }
    .ro-label {
        font-size: 11px;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.04em;
        color: #64748b;
        margin-bottom: 6px;
        display: block;
    }
    .ro-input, .ro-select {
        border-radius: 10px;
        border: 1px solid #e2e8f0;
        font-size: 13px;
        padding: 9px 12px;
        width: 100%;
    }
    .ro-input:focus, .ro-select:focus {
        border-color: #14b8a6;
        box-shadow: 0 0 0 3px rgba(20, 184, 166, 0.15);
    }
    .ro-filter-actions {
        display: flex;
        gap: 8px;
        align-items: flex-end;
        height: 100%;
        padding-top: 22px;
    }
    .ro-btn-primary {
        background: linear-gradient(135deg, #059669, #0d9488);
        border: none;
        color: #fff !important;
        font-weight: 600;
        padding: 9px 18px;
        border-radius: 999px;
        font-size: 13px;
        box-shadow: 0 6px 16px rgba(13, 148, 136, 0.3);
        white-space: nowrap;
    }
    .ro-btn-primary:hover { opacity: .95; color: #fff; }
    .ro-btn-ghost {
        background: #f8fafc;
        color: #475569;
        border: 1px solid #e2e8f0;
        font-weight: 600;
        padding: 9px 16px;
        border-radius: 999px;
        font-size: 13px;
        text-decoration: none;
        white-space: nowrap;
    }
    .ro-btn-ghost:hover { background: #f1f5f9; color: #334155; }
    .ro-bulk-bar {
        display: flex;
        flex-wrap: wrap;
        align-items: flex-end;
        gap: 12px;
        padding: 14px 16px;
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        border-radius: 12px;
        margin-bottom: 16px;
    }
    .ro-bulk-bar .ro-select { max-width: 220px; }
    .ro-btn-bulk {
        background: linear-gradient(135deg, #4f46e5, #6366f1);
        border: none;
        color: #fff;
        font-weight: 600;
        padding: 9px 16px;
        border-radius: 999px;
        font-size: 13px;
    }
    .ro-btn-bulk:disabled {
        opacity: 0.5;
        cursor: not-allowed;
    }
    .ro-selected-count {
        margin-left: auto;
        font-size: 13px;
        font-weight: 600;
        color: #64748b;
    }
    .ro-table-rail {
        width: 100%;
        overflow-x: auto;
        -webkit-overflow-scrolling: touch;
        border-radius: 10px;
        border: 1px solid #e2e8f0;
    }
    .ro-table {
        margin-bottom: 0;
        width: 100%;
        table-layout: auto;
    }
    .ro-table thead { background: #f8fafc; }
    .ro-table th {
        font-size: 11px;
        text-transform: uppercase;
        letter-spacing: 0.04em;
        color: #64748b;
        font-weight: 600;
        padding: 10px 12px;
        white-space: nowrap;
        border-bottom: 1px solid #e2e8f0;
    }
    .ro-table td {
        padding: 10px 12px;
        font-size: 13px;
        color: #334155;
        vertical-align: middle;
        border-bottom: 1px solid #f1f5f9;
    }
    .ro-table tbody tr:hover { background: #f0fdfa; }
    .ro-invoice-link {
        font-weight: 700;
        color: #0d9488;
        text-decoration: none;
    }
    .ro-invoice-link:hover { color: #0f766e; text-decoration: underline; }
    .ro-meta-name { font-weight: 600; color: #0f172a; }
    .ro-meta-sub { font-size: 12px; color: #64748b; }
    .ro-product-thumbs { display: flex; flex-wrap: wrap; gap: 4px; align-items: center; }
    .ro-product-thumb {
        width: 36px;
        height: 36px;
        object-fit: cover;
        border-radius: 8px;
        border: 1px solid #e2e8f0;
    }
    .ro-product-more {
        width: 36px;
        height: 36px;
        background: #e2e8f0;
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 11px;
        font-weight: 700;
        color: #64748b;
    }
    .ro-amount { font-weight: 700; color: #0f172a; }
    .ro-profit-badge {
        display: inline-block;
        background: linear-gradient(135deg, #ecfdf5, #d1fae5);
        color: #047857;
        font-weight: 700;
        font-size: 12px;
        padding: 5px 10px;
        border-radius: 999px;
        border: 1px solid #a7f3d0;
    }
    .ro-status-select {
        min-width: 140px;
        max-width: 180px;
        font-size: 12px;
        font-weight: 600;
        border-radius: 999px;
        border: 1px solid #cbd5e1;
        padding: 6px 10px;
        background: #fff;
    }
    .ro-row-actions {
        display: inline-flex;
        gap: 6px;
        flex-wrap: nowrap;
    }
    .ro-act-btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 4px;
        min-height: 32px;
        padding: 0 12px;
        font-size: 12px;
        font-weight: 600;
        border-radius: 999px;
        border: 1px solid transparent;
        text-decoration: none;
        transition: all 0.2s ease;
    }
    .ro-act-view {
        background: #eef2ff;
        color: #4338ca;
        border-color: #c7d2fe;
    }
    .ro-act-view:hover { background: #4f46e5; color: #fff; }
    .ro-act-process {
        background: #ecfdf5;
        color: #047857;
        border-color: #a7f3d0;
    }
    .ro-act-process:hover { background: #059669; color: #fff; }
    .ro-empty {
        text-align: center;
        padding: 48px 20px;
        color: #94a3b8;
    }
    .ro-empty i { font-size: 3rem; opacity: 0.35; display: block; margin-bottom: 12px; }
    .ro-paginate { margin-top: 16px; }
    .ro-paginate .pagination {
        flex-wrap: wrap;
        justify-content: center;
        gap: 4px;
        margin-bottom: 0;
    }
    .ro-paginate .page-link { border-radius: 8px; min-width: 38px; text-align: center; }
    .ro-scroll-hint {
        font-size: 12px;
        color: #94a3b8;
        margin-bottom: 10px;
    }
</style>
