<style>
    body { background: #eef1f8; }
    .incomplete-orders-shell {
        padding: 8px 0 28px;
        padding-left: max(12px, env(safe-area-inset-left));
        padding-right: max(12px, env(safe-area-inset-right));
    }
    .io-page-header {
        display: flex;
        flex-wrap: wrap;
        align-items: flex-start;
        justify-content: space-between;
        gap: 12px;
        margin-bottom: 16px;
    }
    .io-page-header h4 { margin: 0; font-weight: 700; color: #0f172a; font-size: 1.35rem; }
    .io-page-header .io-sub { font-size: 13px; color: #64748b; margin-top: 4px; max-width: 560px; }
    .io-badge-count {
        background: linear-gradient(135deg, #d97706, #ea580c);
        color: #fff;
        font-size: 12px;
        font-weight: 600;
        padding: 4px 12px;
        border-radius: 999px;
        margin-left: 6px;
        vertical-align: middle;
    }
    .io-info-pill {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        font-size: 12px;
        color: #c2410c;
        background: #fff7ed;
        border: 1px solid #fed7aa;
        padding: 6px 12px;
        border-radius: 999px;
        margin-top: 8px;
    }
    .io-card {
        background: #fff;
        border-radius: 14px;
        border: 1px solid rgba(148, 163, 184, 0.28);
        box-shadow: 0 12px 28px rgba(15, 23, 42, 0.06);
        overflow: hidden;
        margin-bottom: 16px;
    }
    .io-card-head {
        padding: 14px 18px;
        border-bottom: 1px solid #e2e8f0;
        background: linear-gradient(180deg, #fff7ed 0%, #fff 100%);
    }
    .io-card-head h6 { margin: 0; font-size: 14px; font-weight: 700; color: #1e293b; }
    .io-card-head h6 i { color: #ea580c; margin-right: 6px; }
    .io-card-body { padding: 16px 18px; }
    .io-scroll-hint {
        font-size: 12px;
        color: #94a3b8;
        margin-bottom: 10px;
    }
    .io-table-rail {
        width: 100%;
        overflow-x: auto;
        -webkit-overflow-scrolling: touch;
        border-radius: 10px;
        border: 1px solid #e2e8f0;
    }
    .io-table {
        margin-bottom: 0;
        width: 100%;
        table-layout: auto;
        border-collapse: separate;
        border-spacing: 0;
    }
    .io-table thead { background: #f8fafc; }
    .io-table th {
        font-size: 11px;
        text-transform: uppercase;
        letter-spacing: 0.04em;
        color: #64748b;
        font-weight: 600;
        padding: 10px 12px;
        white-space: nowrap;
        border-bottom: 1px solid #e2e8f0;
    }
    .io-table td {
        padding: 10px 12px;
        font-size: 13px;
        color: #334155;
        vertical-align: middle;
        border-bottom: 1px solid #f1f5f9;
    }
    .io-parent-row {
        background: #fff;
        cursor: pointer;
        transition: background 0.15s ease;
    }
    .io-parent-row:hover { background: #fffbeb; }
    .io-parent-row.io-expanded { background: #fff7ed; }
    .io-expand-btn {
        width: 32px;
        height: 32px;
        border-radius: 999px;
        border: 1px solid #e2e8f0;
        background: #f8fafc;
        color: #64748b;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        transition: transform 0.2s ease, background 0.2s ease, color 0.2s ease;
        pointer-events: none;
    }
    .io-parent-row.io-expanded .io-expand-btn {
        transform: rotate(180deg);
        background: #ea580c;
        color: #fff;
        border-color: #ea580c;
    }
    .io-meta-name { font-weight: 600; color: #0f172a; }
    .io-meta-sub { font-size: 12px; color: #64748b; }
    .io-amount {
        font-weight: 700;
        color: #0f172a;
        background: linear-gradient(135deg, #fff7ed, #ffedd5);
        border: 1px solid #fed7aa;
        padding: 5px 10px;
        border-radius: 999px;
        font-size: 12px;
        display: inline-block;
    }
    .io-row-actions {
        display: inline-flex;
        gap: 6px;
        flex-wrap: nowrap;
        justify-content: flex-end;
    }
    .io-act-btn {
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
    }
    .io-act-accept {
        background: #ecfdf5;
        color: #047857;
        border-color: #a7f3d0;
    }
    .io-act-accept:hover { background: #059669; color: #fff; }
    .io-act-delete {
        background: #fef2f2;
        color: #b91c1c;
        border-color: #fecaca;
    }
    .io-act-delete:hover { background: #dc2626; color: #fff; }
    .io-details-row { display: none; }
    .io-details-row.io-open { display: table-row; }
    .io-details-row td {
        padding: 0;
        border-bottom: 1px solid #e2e8f0;
        background: #fafafa;
    }
    .io-details-box {
        padding: 16px 18px;
        border-left: 4px solid #ea580c;
        margin: 0;
    }
    .io-details-grid {
        display: grid;
        grid-template-columns: 1fr;
        gap: 16px;
    }
    @media (min-width: 768px) {
        .io-details-grid { grid-template-columns: minmax(200px, 280px) 1fr; }
    }
    .io-section-title {
        font-size: 11px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        color: #94a3b8;
        margin-bottom: 8px;
    }
    .io-address {
        font-size: 13px;
        color: #334155;
        line-height: 1.5;
        margin: 0;
    }
    .io-address i { color: #ea580c; margin-right: 4px; }
    .io-items-table {
        width: 100%;
        margin: 0;
        font-size: 13px;
    }
    .io-items-table th {
        font-size: 11px;
        text-transform: uppercase;
        color: #94a3b8;
        font-weight: 600;
        padding: 6px 8px;
        border-bottom: 1px solid #e2e8f0;
    }
    .io-items-table td {
        padding: 8px;
        border-bottom: 1px solid #f1f5f9;
        vertical-align: middle;
    }
    .io-item-thumb {
        width: 40px;
        height: 40px;
        object-fit: cover;
        border-radius: 8px;
        border: 1px solid #e2e8f0;
    }
    .io-product-fallback {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 12px;
        background: #fff;
        border: 1px solid #e2e8f0;
        border-radius: 10px;
    }
    .io-product-fallback img {
        width: 50px;
        height: 50px;
        object-fit: cover;
        border-radius: 8px;
    }
    .io-product-fallback a {
        font-weight: 600;
        color: #ea580c;
        text-decoration: none;
    }
    .io-product-fallback a:hover { text-decoration: underline; }
    .io-empty {
        text-align: center;
        padding: 48px 20px;
        color: #94a3b8;
    }
    .io-empty i { font-size: 3rem; opacity: 0.35; display: block; margin-bottom: 12px; }
    .io-paginate { margin-top: 16px; }
    .io-paginate .pagination {
        flex-wrap: wrap;
        justify-content: center;
        gap: 4px;
        margin-bottom: 0;
    }
    .io-paginate .page-link { border-radius: 8px; min-width: 38px; text-align: center; }
</style>
