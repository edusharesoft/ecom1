<style>
    body { background: #eef1f8; }
    .vendor-list-shell {
        padding: 8px 0 28px;
        padding-left: max(12px, env(safe-area-inset-left));
        padding-right: max(12px, env(safe-area-inset-right));
    }
    .vn-page-header {
        display: flex;
        flex-wrap: wrap;
        align-items: flex-start;
        justify-content: space-between;
        gap: 12px;
        margin-bottom: 16px;
    }
    .vn-page-header h4 {
        margin: 0;
        font-weight: 700;
        color: #0f172a;
        font-size: 1.35rem;
    }
    .vn-page-header .vn-sub {
        font-size: 13px;
        color: #64748b;
        margin-top: 4px;
    }
    .vn-header-actions {
        display: flex;
        flex-wrap: wrap;
        gap: 8px;
        align-items: center;
    }
    .vn-badge-count {
        background: linear-gradient(135deg, #7c3aed, #8b5cf6);
        color: #fff;
        font-size: 12px;
        font-weight: 600;
        padding: 4px 12px;
        border-radius: 999px;
        margin-left: 6px;
        vertical-align: middle;
    }
    .vn-btn-ghost {
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
    .vn-btn-ghost:hover {
        background: #f8fafc;
        color: #334155;
        transform: translateY(-1px);
    }
    .vn-stat-grid {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 10px;
        margin-bottom: 16px;
    }
    @media (min-width: 768px) {
        .vn-stat-grid { grid-template-columns: repeat(4, 1fr); }
    }
    .vn-stat {
        background: #fff;
        border: 1px solid #e2e8f0;
        border-radius: 12px;
        padding: 12px 14px;
        transition: border-color 0.2s, box-shadow 0.2s;
    }
    .vn-stat-label {
        font-size: 11px;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.04em;
        color: #64748b;
    }
    .vn-stat-val {
        font-size: 1.35rem;
        font-weight: 800;
        color: #0f172a;
        margin-top: 2px;
    }
    .vn-stat.active-stat .vn-stat-val { color: #059669; }
    .vn-stat.verified-stat .vn-stat-val { color: #2563eb; }
    .vn-stat.pending-stat .vn-stat-val { color: #ea580c; }
    .vn-card {
        background: #fff;
        border-radius: 14px;
        border: 1px solid rgba(148, 163, 184, 0.28);
        box-shadow: 0 12px 28px rgba(15, 23, 42, 0.06);
        overflow: hidden;
        margin-bottom: 16px;
    }
    .vn-card-head {
        padding: 14px 18px;
        border-bottom: 1px solid #e2e8f0;
        background: linear-gradient(180deg, #f5f3ff 0%, #fff 100%);
        display: flex;
        align-items: center;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: 8px;
    }
    .vn-card-head h6 {
        margin: 0;
        font-size: 14px;
        font-weight: 700;
        color: #1e293b;
    }
    .vn-card-head h6 i {
        color: #7c3aed;
        margin-right: 6px;
    }
    .vn-card-body { padding: 16px 18px; }
    .vn-label {
        font-size: 11px;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.04em;
        color: #64748b;
        margin-bottom: 6px;
        display: block;
    }
    .vn-input {
        border-radius: 10px;
        border: 1px solid #e2e8f0;
        font-size: 13px;
        padding: 9px 12px;
        width: 100%;
        background: #f8fafc;
    }
    .vn-input:focus {
        border-color: #a78bfa;
        box-shadow: 0 0 0 3px rgba(139, 92, 246, 0.15);
        background: #fff;
        outline: none;
    }
    .vn-filter-grid {
        display: grid;
        grid-template-columns: 1fr;
        gap: 12px;
    }
    @media (min-width: 768px) {
        .vn-filter-grid {
            grid-template-columns: 1fr auto;
            align-items: flex-end;
        }
    }
    .vn-filter-actions {
        display: flex;
        gap: 8px;
        flex-wrap: wrap;
    }
    .vn-btn-primary {
        background: linear-gradient(135deg, #7c3aed, #8b5cf6);
        border: none;
        color: #fff !important;
        font-weight: 600;
        padding: 9px 18px;
        border-radius: 999px;
        font-size: 13px;
        box-shadow: 0 6px 16px rgba(124, 58, 237, 0.28);
    }
    .vn-btn-primary:hover { opacity: 0.95; color: #fff; }
    .vn-table-rail {
        width: 100%;
        overflow-x: auto;
        -webkit-overflow-scrolling: touch;
        border-radius: 10px;
        border: 1px solid #e2e8f0;
    }
    .vn-table { margin-bottom: 0; width: 100%; min-width: 900px; }
    .vn-table thead { background: #f8fafc; }
    .vn-table th {
        font-size: 11px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.04em;
        color: #64748b;
        padding: 12px 14px;
        border-bottom: 2px solid #e2e8f0;
        white-space: nowrap;
    }
    .vn-table td {
        padding: 12px 14px;
        vertical-align: middle;
        font-size: 13px;
        color: #334155;
        border-bottom: 1px solid #f1f5f9;
    }
    .vn-table tbody tr:hover td { background: #faf5ff; }
    .vn-scroll-hint {
        font-size: 12px;
        color: #94a3b8;
        margin-bottom: 10px;
    }
    .vn-shop-cell {
        display: flex;
        align-items: center;
        gap: 10px;
        min-width: 180px;
    }
    .vn-shop-avatar {
        width: 44px;
        height: 44px;
        border-radius: 12px;
        object-fit: cover;
        border: 2px solid #e9d5ff;
        flex-shrink: 0;
    }
    .vn-shop-placeholder {
        width: 44px;
        height: 44px;
        border-radius: 12px;
        background: linear-gradient(135deg, #ede9fe, #ddd6fe);
        color: #6d28d9;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 800;
        font-size: 17px;
        flex-shrink: 0;
    }
    .vn-shop-name {
        font-weight: 700;
        color: #0f172a;
        line-height: 1.25;
    }
    .vn-shop-meta {
        font-size: 11px;
        color: #94a3b8;
        margin-top: 2px;
    }
    .vn-contact-line {
        display: flex;
        align-items: center;
        gap: 6px;
        font-size: 12px;
        color: #475569;
        margin-bottom: 3px;
    }
    .vn-contact-line i { color: #a78bfa; width: 14px; text-align: center; }
    .vn-pill {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        padding: 4px 10px;
        border-radius: 999px;
        font-size: 11px;
        font-weight: 600;
        white-space: nowrap;
    }
    .vn-pill-products {
        background: #f1f5f9;
        color: #475569;
        border: 1px solid #e2e8f0;
    }
    .vn-balance {
        font-weight: 800;
        color: #0f172a;
        font-size: 14px;
    }
    .vn-badge-verified { background: #dcfce7; color: #166534; }
    .vn-badge-rejected { background: #fee2e2; color: #991b1b; }
    .vn-badge-pending { background: #fef3c7; color: #92400e; }
    .vn-badge-active { background: #dcfce7; color: #166534; }
    .vn-badge-inactive { background: #fee2e2; color: #991b1b; }
    .vn-dot {
        width: 6px;
        height: 6px;
        border-radius: 50%;
        background: currentColor;
    }
    .vn-actions {
        display: flex;
        justify-content: flex-end;
        gap: 6px;
        flex-wrap: nowrap;
    }
    .vn-action-btn {
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
    .vn-action-btn:hover {
        transform: translateY(-2px);
        text-decoration: none;
    }
    .vn-action-btn.edit:hover {
        background: #ede9fe;
        border-color: #c4b5fd;
        color: #6d28d9;
    }
    .vn-action-btn.deactivate:hover {
        background: #fef2f2;
        border-color: #fecaca;
        color: #dc2626;
    }
    .vn-action-btn.activate:hover {
        background: #ecfdf5;
        border-color: #a7f3d0;
        color: #059669;
    }
    .vn-action-btn.delete:hover {
        background: #fee2e2;
        border-color: #fca5a5;
        color: #b91c1c;
    }
    .vn-foot {
        padding: 14px 18px;
        border-top: 1px solid #e2e8f0;
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
        background: #fafafa;
    }
    .vn-foot-meta {
        font-size: 12px;
        color: #64748b;
    }
    .vn-empty {
        text-align: center;
        padding: 48px 20px;
        color: #94a3b8;
    }
    .vn-empty i {
        font-size: 2.5rem;
        color: #c4b5fd;
        margin-bottom: 12px;
    }
    .pagination { margin-bottom: 0; }
</style>
