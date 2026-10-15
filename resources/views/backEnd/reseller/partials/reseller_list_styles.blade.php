<style>
    /* টপবারের উপর সাদা গ্যাপ এড়াতে html/body একই ব্যাকগ্রাউন্ড + fixed navbar শীর্ষে */
    html,
    body {
        margin: 0;
        padding: 0;
        background: #eef1f8;
    }
    .navbar-custom {
        top: 0 !important;
    }
    .reseller-list-shell {
        padding: 0 0 28px;
        padding-left: max(12px, env(safe-area-inset-left));
        padding-right: max(12px, env(safe-area-inset-right));
    }
    .rs-page-header {
        display: flex;
        flex-wrap: wrap;
        align-items: flex-start;
        justify-content: space-between;
        gap: 12px;
        margin-bottom: 16px;
    }
    .rs-page-header h4 {
        margin: 0;
        font-weight: 700;
        color: #0f172a;
        font-size: 1.35rem;
    }
    .rs-page-header .rs-sub {
        font-size: 13px;
        color: #64748b;
        margin-top: 4px;
    }
    .rs-header-actions {
        display: flex;
        flex-wrap: wrap;
        gap: 8px;
        align-items: center;
    }
    .rs-badge-count {
        background: linear-gradient(135deg, #0d9488, #14b8a6);
        color: #fff;
        font-size: 12px;
        font-weight: 600;
        padding: 4px 12px;
        border-radius: 999px;
        margin-left: 6px;
        vertical-align: middle;
    }
    .rs-btn-ghost {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        background: #fff;
        color: #475569;
        border: 1px solid #e2e8f0;
        font-weight: 600;
        padding: 9px 16px;
        border-radius: 999px;
        font-size: 13px;
        text-decoration: none;
        box-shadow: 0 2px 8px rgba(15, 23, 42, 0.04);
        transition: background 0.2s, transform 0.2s;
    }
    .rs-btn-ghost:hover {
        background: #f8fafc;
        color: #334155;
        transform: translateY(-1px);
    }
    .rs-stat-grid {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 10px;
        margin-bottom: 16px;
    }
    @media (min-width: 768px) {
        .rs-stat-grid { grid-template-columns: repeat(4, 1fr); }
    }
    .rs-stat {
        background: #fff;
        border: 1px solid #e2e8f0;
        border-radius: 12px;
        padding: 12px 14px;
        transition: border-color 0.2s, box-shadow 0.2s;
    }
    .rs-stat-label {
        font-size: 11px;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.04em;
        color: #64748b;
    }
    .rs-stat-val {
        font-size: 1.35rem;
        font-weight: 800;
        color: #0f172a;
        margin-top: 2px;
    }
    .rs-stat.active-stat .rs-stat-val { color: #059669; }
    .rs-stat.verified-stat .rs-stat-val { color: #2563eb; }
    .rs-stat.pending-stat .rs-stat-val { color: #ea580c; }
    .rs-card {
        background: #fff;
        border-radius: 14px;
        border: 1px solid rgba(148, 163, 184, 0.28);
        box-shadow: 0 12px 28px rgba(15, 23, 42, 0.06);
        overflow: hidden;
        margin-bottom: 16px;
    }
    .rs-card-head {
        padding: 14px 18px;
        border-bottom: 1px solid #e2e8f0;
        background: linear-gradient(180deg, #ecfdf5 0%, #fff 100%);
        display: flex;
        align-items: center;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: 8px;
    }
    .rs-card-head h6 {
        margin: 0;
        font-size: 14px;
        font-weight: 700;
        color: #1e293b;
    }
    .rs-card-head h6 i {
        color: #0d9488;
        margin-right: 6px;
    }
    .rs-card-body { padding: 16px 18px; }
    .rs-label {
        font-size: 11px;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.04em;
        color: #64748b;
        margin-bottom: 6px;
        display: block;
    }
    .rs-input {
        border-radius: 10px;
        border: 1px solid #e2e8f0;
        font-size: 13px;
        padding: 9px 12px;
        width: 100%;
        background: #f8fafc;
    }
    .rs-input:focus {
        border-color: #2dd4bf;
        box-shadow: 0 0 0 3px rgba(20, 184, 166, 0.15);
        background: #fff;
        outline: none;
    }
    .rs-filter-grid {
        display: grid;
        grid-template-columns: 1fr;
        gap: 12px;
    }
    @media (min-width: 768px) {
        .rs-filter-grid {
            grid-template-columns: 1fr auto;
            align-items: flex-end;
        }
    }
    .rs-filter-actions {
        display: flex;
        gap: 8px;
        flex-wrap: wrap;
    }
    .rs-btn-primary {
        background: linear-gradient(135deg, #0d9488, #14b8a6);
        border: none;
        color: #fff !important;
        font-weight: 600;
        padding: 9px 18px;
        border-radius: 999px;
        font-size: 13px;
        box-shadow: 0 6px 16px rgba(13, 148, 136, 0.28);
    }
    .rs-btn-primary:hover { opacity: 0.95; color: #fff; }
    .rs-table-rail {
        width: 100%;
        overflow-x: auto;
        -webkit-overflow-scrolling: touch;
        border-radius: 10px;
        border: 1px solid #e2e8f0;
    }
    .rs-table { margin-bottom: 0; width: 100%; min-width: 860px; }
    .rs-table thead { background: #f8fafc; }
    .rs-table th {
        font-size: 11px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.04em;
        color: #64748b;
        padding: 12px 14px;
        border-bottom: 2px solid #e2e8f0;
        white-space: nowrap;
    }
    .rs-table td {
        padding: 12px 14px;
        vertical-align: middle;
        font-size: 13px;
        color: #334155;
        border-bottom: 1px solid #f1f5f9;
    }
    .rs-table tbody tr:hover td { background: #f0fdfa; }
    .rs-scroll-hint {
        font-size: 12px;
        color: #94a3b8;
        margin-bottom: 10px;
    }
    .rs-shop-cell {
        display: flex;
        align-items: center;
        gap: 10px;
        min-width: 180px;
    }
    .rs-shop-avatar {
        width: 44px;
        height: 44px;
        border-radius: 12px;
        object-fit: cover;
        border: 2px solid #99f6e4;
        flex-shrink: 0;
    }
    .rs-shop-placeholder {
        width: 44px;
        height: 44px;
        border-radius: 12px;
        background: linear-gradient(135deg, #ccfbf1, #99f6e4);
        color: #0f766e;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 800;
        font-size: 17px;
        flex-shrink: 0;
    }
    .rs-shop-name {
        font-weight: 700;
        color: #0f172a;
        line-height: 1.25;
    }
    .rs-shop-meta {
        font-size: 11px;
        color: #94a3b8;
        margin-top: 2px;
    }
    .rs-contact-line {
        display: flex;
        align-items: center;
        gap: 6px;
        font-size: 12px;
        color: #475569;
        margin-bottom: 3px;
    }
    .rs-contact-line i { color: #2dd4bf; width: 14px; text-align: center; }
    .rs-pill {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        padding: 4px 10px;
        border-radius: 999px;
        font-size: 11px;
        font-weight: 600;
        white-space: nowrap;
    }
    .rs-pill-wallet {
        background: #f1f5f9;
        color: #475569;
        border: 1px solid #e2e8f0;
    }
    .rs-balance {
        font-weight: 800;
        color: #0f172a;
        font-size: 14px;
    }
    .rs-badge-verified { background: #dcfce7; color: #166534; }
    .rs-badge-rejected { background: #fee2e2; color: #991b1b; }
    .rs-badge-pending { background: #fef3c7; color: #92400e; }
    .rs-badge-active { background: #dcfce7; color: #166534; }
    .rs-badge-inactive { background: #fee2e2; color: #991b1b; }
    .rs-dot {
        width: 6px;
        height: 6px;
        border-radius: 50%;
        background: currentColor;
    }
    .rs-actions {
        display: flex;
        justify-content: flex-end;
        gap: 6px;
        flex-wrap: nowrap;
    }
    .rs-action-btn {
        width: 34px;
        height: 34px;
        border-radius: 10px;
        border: 1px solid #e2e8f0;
        background: #fff;
        color: #64748b;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        padding: 0;
        cursor: pointer;
        text-decoration: none;
        transition: transform 0.15s, background 0.15s, color 0.15s, border-color 0.15s;
    }
    .rs-action-btn:hover {
        transform: translateY(-2px);
        text-decoration: none;
    }
    .rs-action-btn.edit:hover {
        background: #ccfbf1;
        border-color: #5eead4;
        color: #0f766e;
    }
    .rs-action-btn.deactivate:hover {
        background: #fef2f2;
        border-color: #fecaca;
        color: #dc2626;
    }
    .rs-action-btn.activate:hover {
        background: #ecfdf5;
        border-color: #a7f3d0;
        color: #059669;
    }
    .rs-action-btn.delete:hover {
        background: #fee2e2;
        border-color: #fca5a5;
        color: #b91c1c;
    }
    .rs-foot {
        padding: 14px 18px;
        border-top: 1px solid #e2e8f0;
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
        background: #fafafa;
    }
    .rs-foot-meta {
        font-size: 12px;
        color: #64748b;
    }
    .rs-empty {
        text-align: center;
        padding: 48px 20px;
        color: #94a3b8;
    }
    .rs-empty i {
        font-size: 2.5rem;
        color: #5eead4;
        margin-bottom: 12px;
    }
    .pagination { margin-bottom: 0; }
</style>

