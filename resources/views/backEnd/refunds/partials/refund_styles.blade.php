<style>
    body { background: #eef1f8; }
    .refund-shell {
        padding: 8px 0 28px;
        padding-left: max(12px, env(safe-area-inset-left));
        padding-right: max(12px, env(safe-area-inset-right));
    }
    .rf-page-header {
        display: flex;
        flex-wrap: wrap;
        align-items: flex-start;
        justify-content: space-between;
        gap: 12px;
        margin-bottom: 16px;
    }
    .rf-page-header h4 { margin: 0; font-weight: 700; color: #0f172a; font-size: 1.35rem; }
    .rf-page-header .rf-sub { font-size: 13px; color: #64748b; margin-top: 4px; }
    .rf-badge-count {
        background: linear-gradient(135deg, #e11d48, #f43f5e);
        color: #fff;
        font-size: 12px;
        font-weight: 600;
        padding: 4px 12px;
        border-radius: 999px;
        margin-left: 6px;
        vertical-align: middle;
    }
    .rf-stat-grid {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 10px;
        margin-bottom: 16px;
    }
    @media (min-width: 768px) {
        .rf-stat-grid { grid-template-columns: repeat(4, 1fr); }
    }
    .rf-stat {
        background: #fff;
        border: 1px solid #e2e8f0;
        border-radius: 12px;
        padding: 12px 14px;
        text-decoration: none;
        color: inherit;
        transition: border-color 0.2s, box-shadow 0.2s;
    }
    .rf-stat:hover {
        border-color: #fda4af;
        box-shadow: 0 4px 14px rgba(225, 29, 72, 0.1);
        color: inherit;
    }
    .rf-stat.active {
        border-color: #f43f5e;
        background: linear-gradient(180deg, #fff1f2 0%, #fff 100%);
        box-shadow: 0 4px 14px rgba(244, 63, 94, 0.12);
    }
    .rf-stat-label { font-size: 11px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.04em; color: #64748b; }
    .rf-stat-val { font-size: 1.35rem; font-weight: 800; color: #0f172a; margin-top: 2px; }
    .rf-stat.pending .rf-stat-val { color: #ea580c; }
    .rf-stat.approved .rf-stat-val { color: #2563eb; }
    .rf-stat.rejected .rf-stat-val { color: #dc2626; }
    .rf-stat.processed .rf-stat-val { color: #059669; }
    .rf-card {
        background: #fff;
        border-radius: 14px;
        border: 1px solid rgba(148, 163, 184, 0.28);
        box-shadow: 0 12px 28px rgba(15, 23, 42, 0.06);
        overflow: hidden;
        margin-bottom: 16px;
    }
    .rf-card-head {
        padding: 14px 18px;
        border-bottom: 1px solid #e2e8f0;
        background: linear-gradient(180deg, #fff1f2 0%, #fff 100%);
        display: flex;
        align-items: center;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: 8px;
    }
    .rf-card-head h6 { margin: 0; font-size: 14px; font-weight: 700; color: #1e293b; }
    .rf-card-head h6 i { color: #e11d48; margin-right: 6px; }
    .rf-card-body { padding: 16px 18px; }
    .rf-label {
        font-size: 11px;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.04em;
        color: #64748b;
        margin-bottom: 6px;
        display: block;
    }
    .rf-input, .rf-select {
        border-radius: 10px;
        border: 1px solid #e2e8f0;
        font-size: 13px;
        padding: 9px 12px;
        width: 100%;
        background: #f8fafc;
    }
    .rf-input:focus, .rf-select:focus {
        border-color: #f43f5e;
        box-shadow: 0 0 0 3px rgba(244, 63, 94, 0.12);
        background: #fff;
        outline: none;
    }
    .rf-filter-grid {
        display: grid;
        grid-template-columns: 1fr;
        gap: 12px;
    }
    @media (min-width: 768px) {
        .rf-filter-grid { grid-template-columns: 1fr 200px auto; align-items: flex-end; }
    }
    .rf-filter-actions { display: flex; gap: 8px; flex-wrap: wrap; }
    .rf-btn-primary {
        background: linear-gradient(135deg, #e11d48, #f43f5e);
        border: none;
        color: #fff !important;
        font-weight: 600;
        padding: 9px 18px;
        border-radius: 999px;
        font-size: 13px;
        box-shadow: 0 6px 16px rgba(225, 29, 72, 0.25);
    }
    .rf-btn-primary:hover { opacity: 0.95; color: #fff; }
    .rf-btn-ghost {
        background: #f8fafc;
        color: #475569;
        border: 1px solid #e2e8f0;
        font-weight: 600;
        padding: 9px 16px;
        border-radius: 999px;
        font-size: 13px;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 6px;
    }
    .rf-btn-ghost:hover { background: #f1f5f9; color: #334155; }
    .rf-table-rail {
        width: 100%;
        overflow-x: auto;
        -webkit-overflow-scrolling: touch;
        border-radius: 10px;
        border: 1px solid #e2e8f0;
    }
    .rf-table { margin-bottom: 0; width: 100%; }
    .rf-table thead { background: #f8fafc; }
    .rf-table th {
        font-size: 11px;
        text-transform: uppercase;
        letter-spacing: 0.04em;
        color: #64748b;
        font-weight: 600;
        padding: 10px 12px;
        white-space: nowrap;
        border-bottom: 1px solid #e2e8f0;
    }
    .rf-table td {
        padding: 11px 12px;
        font-size: 13px;
        color: #334155;
        vertical-align: middle;
        border-bottom: 1px solid #f1f5f9;
    }
    .rf-table tbody tr:hover { background: #fff1f2; }
    .rf-refund-id { font-weight: 700; color: #0f172a; }
    .rf-invoice-link {
        font-size: 12px;
        font-weight: 600;
        color: #e11d48;
        text-decoration: none;
    }
    .rf-invoice-link:hover { text-decoration: underline; }
    .rf-customer-name { font-weight: 600; color: #0f172a; }
    .rf-meta-sub { font-size: 12px; color: #64748b; }
    .rf-amount { font-weight: 700; color: #0f172a; }
    .rf-pill {
        display: inline-block;
        font-size: 11px;
        font-weight: 600;
        padding: 5px 10px;
        border-radius: 999px;
    }
    .rf-pill-pending { background: #fff7ed; color: #c2410c; border: 1px solid #fed7aa; }
    .rf-pill-approved { background: #eff6ff; color: #1d4ed8; border: 1px solid #bfdbfe; }
    .rf-pill-rejected { background: #fef2f2; color: #b91c1c; border: 1px solid #fecaca; }
    .rf-pill-processed { background: #ecfdf5; color: #047857; border: 1px solid #a7f3d0; }
    .rf-row-actions {
        display: inline-flex;
        gap: 5px;
        flex-wrap: wrap;
        justify-content: flex-end;
    }
    .rf-act-btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-height: 32px;
        min-width: 32px;
        padding: 0 10px;
        font-size: 12px;
        font-weight: 600;
        border-radius: 999px;
        border: 1px solid transparent;
        text-decoration: none;
        cursor: pointer;
        background: transparent;
        transition: all 0.2s;
    }
    .rf-act-view { background: #eef2ff; color: #4338ca; border-color: #c7d2fe; }
    .rf-act-view:hover { background: #4f46e5; color: #fff; }
    .rf-act-approve { background: #ecfdf5; color: #047857; border-color: #a7f3d0; }
    .rf-act-approve:hover { background: #059669; color: #fff; }
    .rf-act-reject { background: #fef2f2; color: #b91c1c; border-color: #fecaca; }
    .rf-act-reject:hover { background: #dc2626; color: #fff; }
    .rf-act-process { background: #eff6ff; color: #1d4ed8; border-color: #bfdbfe; }
    .rf-act-process:hover { background: #2563eb; color: #fff; }
    .rf-empty { text-align: center; padding: 48px 20px; color: #94a3b8; }
    .rf-empty i { font-size: 3rem; opacity: 0.35; display: block; margin-bottom: 12px; }
    .rf-paginate { margin-top: 16px; }
    .rf-paginate .pagination { flex-wrap: wrap; justify-content: center; gap: 4px; margin-bottom: 0; }
    .rf-paginate .page-item.active .page-link { background: #e11d48; border-color: #e11d48; }
    .rf-paginate .page-link { border-radius: 8px; }
    .rf-scroll-hint { font-size: 12px; color: #94a3b8; margin-bottom: 10px; }
    /* Show page */
    .rf-hero-amount {
        font-size: 1.75rem;
        font-weight: 800;
        color: #e11d48;
        letter-spacing: -0.02em;
    }
    .rf-info-grid {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 12px;
    }
    @media (min-width: 768px) {
        .rf-info-grid { grid-template-columns: repeat(3, 1fr); }
    }
    .rf-info-box {
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        border-radius: 10px;
        padding: 12px 14px;
    }
    .rf-info-box .rf-label { margin-bottom: 4px; }
    .rf-info-box .rf-val { font-size: 14px; font-weight: 600; color: #0f172a; }
    .rf-reason-box {
        background: #f8fafc;
        border-left: 4px solid #f43f5e;
        border-radius: 0 10px 10px 0;
        padding: 14px 16px;
        font-size: 14px;
        color: #334155;
        line-height: 1.55;
    }
    .rf-product-thumb {
        width: 40px;
        height: 40px;
        object-fit: cover;
        border-radius: 8px;
        border: 1px solid #e2e8f0;
    }
    .rf-sidebar-card .rf-card-body { padding: 18px; }
    .rf-btn-action {
        width: 100%;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        padding: 11px 16px;
        font-weight: 600;
        font-size: 13px;
        border-radius: 999px;
        border: none;
        margin-bottom: 8px;
        transition: opacity 0.2s;
    }
    .rf-btn-action:last-child { margin-bottom: 0; }
    .rf-btn-success { background: linear-gradient(135deg, #059669, #10b981); color: #fff; }
    .rf-btn-danger { background: #fff; color: #dc2626; border: 1px solid #fecaca; }
    .rf-btn-danger:hover { background: #fef2f2; }
    .rf-btn-primary-block { background: linear-gradient(135deg, #2563eb, #3b82f6); color: #fff; }
    .rf-customer-card {
        text-align: center;
        padding-top: 8px;
    }
    .rf-avatar {
        width: 56px;
        height: 56px;
        border-radius: 50%;
        background: linear-gradient(135deg, #ffe4e6, #fecdd3);
        color: #e11d48;
        font-weight: 800;
        font-size: 1.25rem;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        margin-bottom: 10px;
    }
    .rf-modal .modal-content {
        border-radius: 14px;
        border: 1px solid rgba(148, 163, 184, 0.28);
        overflow: hidden;
    }
    .rf-modal .modal-header {
        background: linear-gradient(180deg, #fff1f2 0%, #fff 100%);
        border-bottom: 1px solid #e2e8f0;
    }
    .rf-modal .modal-title { font-weight: 700; color: #0f172a; }
    .rf-modal .modal-footer { border-top: 1px solid #e2e8f0; background: #f8fafc; }
    .rf-textarea {
        border-radius: 10px;
        border: 1px solid #e2e8f0;
        font-size: 13px;
        padding: 10px 12px;
        width: 100%;
        background: #f8fafc;
        min-height: 80px;
    }
    .rf-textarea:focus {
        border-color: #f43f5e;
        box-shadow: 0 0 0 3px rgba(244, 63, 94, 0.12);
        outline: none;
        background: #fff;
    }
</style>
