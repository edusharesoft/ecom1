<style>
    body { background: #eef1f8; }
    .ip-block-shell {
        padding: 8px 0 28px;
        padding-left: max(12px, env(safe-area-inset-left));
        padding-right: max(12px, env(safe-area-inset-right));
    }
    .ipb-page-header {
        margin-bottom: 16px;
    }
    .ipb-page-header h4 {
        margin: 0;
        font-weight: 700;
        color: #0f172a;
        font-size: 1.35rem;
    }
    .ipb-page-header .ipb-sub {
        font-size: 13px;
        color: #64748b;
        margin-top: 4px;
        max-width: 560px;
    }
    .ipb-badge-count {
        background: linear-gradient(135deg, #dc2626, #b91c1c);
        color: #fff;
        font-size: 12px;
        font-weight: 600;
        padding: 4px 12px;
        border-radius: 999px;
        margin-left: 6px;
        vertical-align: middle;
    }
    .ipb-card {
        background: #fff;
        border-radius: 14px;
        border: 1px solid rgba(148, 163, 184, 0.28);
        box-shadow: 0 12px 28px rgba(15, 23, 42, 0.06);
        overflow: hidden;
        margin-bottom: 16px;
        height: 100%;
    }
    .ipb-card-head {
        padding: 14px 18px;
        border-bottom: 1px solid #e2e8f0;
        background: linear-gradient(180deg, #fef2f2 0%, #fff 100%);
        display: flex;
        align-items: center;
        gap: 10px;
    }
    .ipb-card-head h6 {
        margin: 0;
        font-size: 14px;
        font-weight: 700;
        color: #1e293b;
    }
    .ipb-card-icon {
        width: 36px;
        height: 36px;
        border-radius: 10px;
        background: linear-gradient(135deg, #fee2e2, #fecaca);
        color: #b91c1c;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 15px;
        flex-shrink: 0;
    }
    .ipb-card-body { padding: 18px; }
    .ipb-label {
        font-size: 11px;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.04em;
        color: #64748b;
        margin-bottom: 6px;
        display: block;
    }
    .ipb-input, .ipb-textarea {
        border-radius: 10px;
        border: 1px solid #e2e8f0;
        font-size: 13px;
        padding: 10px 12px;
        width: 100%;
        background: #f8fafc;
        transition: border-color 0.2s, box-shadow 0.2s, background 0.2s;
    }
    .ipb-input:focus, .ipb-textarea:focus {
        background: #fff;
        border-color: #ef4444;
        box-shadow: 0 0 0 3px rgba(239, 68, 68, 0.12);
        outline: none;
    }
    .ipb-textarea { min-height: 100px; resize: vertical; }
    .ipb-btn-block {
        width: 100%;
        background: linear-gradient(135deg, #dc2626, #b91c1c);
        border: none;
        color: #fff !important;
        font-weight: 600;
        padding: 11px 18px;
        border-radius: 999px;
        font-size: 13px;
        box-shadow: 0 6px 16px rgba(220, 38, 38, 0.28);
        transition: opacity 0.2s, transform 0.2s;
    }
    .ipb-btn-block:hover { opacity: 0.95; color: #fff; transform: translateY(-1px); }
    .ipb-tip {
        display: flex;
        align-items: flex-start;
        gap: 10px;
        padding: 14px 16px;
        background: #fffbeb;
        border: 1px solid #fde68a;
        border-radius: 12px;
        font-size: 12px;
        color: #92400e;
        line-height: 1.5;
    }
    .ipb-tip i { color: #d97706; margin-top: 2px; }
    .ipb-table-rail {
        width: 100%;
        overflow-x: auto;
        -webkit-overflow-scrolling: touch;
        border-radius: 10px;
        border: 1px solid #e2e8f0;
    }
    .ipb-table {
        margin-bottom: 0;
        width: 100%;
    }
    .ipb-table thead { background: #f8fafc; }
    .ipb-table th {
        font-size: 11px;
        text-transform: uppercase;
        letter-spacing: 0.04em;
        color: #64748b;
        font-weight: 600;
        padding: 10px 14px;
        white-space: nowrap;
        border-bottom: 1px solid #e2e8f0;
    }
    .ipb-table td {
        padding: 12px 14px;
        font-size: 13px;
        color: #334155;
        vertical-align: middle;
        border-bottom: 1px solid #f1f5f9;
    }
    .ipb-table tbody tr:hover { background: #fef2f2; }
    .ipb-ip-badge {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, monospace;
        font-weight: 700;
        font-size: 13px;
        color: #0f172a;
        background: #f1f5f9;
        border: 1px solid #e2e8f0;
        padding: 5px 12px;
        border-radius: 999px;
    }
    .ipb-ip-badge i { color: #64748b; font-size: 11px; }
    .ipb-reason { color: #475569; line-height: 1.45; max-width: 420px; }
    .ipb-row-actions {
        display: inline-flex;
        gap: 6px;
        justify-content: flex-end;
    }
    .ipb-act-btn {
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
    .ipb-act-edit {
        background: #eef2ff;
        color: #4338ca;
        border-color: #c7d2fe;
    }
    .ipb-act-edit:hover { background: #4f46e5; color: #fff; }
    .ipb-act-delete {
        background: #fef2f2;
        color: #b91c1c;
        border-color: #fecaca;
    }
    .ipb-act-delete:hover { background: #dc2626; color: #fff; }
    .ipb-empty {
        text-align: center;
        padding: 40px 20px;
        color: #94a3b8;
    }
    .ipb-empty i { font-size: 2.5rem; opacity: 0.35; display: block; margin-bottom: 10px; }
    /* DataTables overrides */
    .ipb-dt-wrap .dataTables_wrapper .dataTables_filter input,
    .ipb-dt-wrap .dataTables_wrapper .dataTables_length select {
        border-radius: 8px;
        border: 1px solid #e2e8f0;
        padding: 6px 10px;
        font-size: 13px;
    }
    .ipb-dt-wrap .dataTables_wrapper .dataTables_info,
    .ipb-dt-wrap .dataTables_wrapper .dataTables_paginate {
        font-size: 12px;
        color: #64748b;
        padding-top: 12px;
    }
    .ipb-dt-wrap .page-item.active .page-link {
        background: #dc2626;
        border-color: #dc2626;
    }
    .ipb-dt-wrap .page-link { border-radius: 8px; margin: 0 2px; }
    /* Modal */
    .ipb-modal .modal-content {
        border-radius: 14px;
        border: 1px solid rgba(148, 163, 184, 0.28);
        box-shadow: 0 20px 50px rgba(15, 23, 42, 0.15);
        overflow: hidden;
    }
    .ipb-modal .modal-header {
        background: linear-gradient(180deg, #fef2f2 0%, #fff 100%);
        border-bottom: 1px solid #e2e8f0;
        padding: 16px 20px;
    }
    .ipb-modal .modal-title {
        font-weight: 700;
        color: #0f172a;
        font-size: 1rem;
    }
    .ipb-modal .modal-body { padding: 20px; }
    .ipb-btn-save {
        width: 100%;
        background: linear-gradient(135deg, #4f46e5, #6366f1);
        border: none;
        color: #fff;
        font-weight: 600;
        padding: 11px 18px;
        border-radius: 999px;
        font-size: 13px;
    }
    .ipb-btn-save:hover { opacity: 0.95; color: #fff; }
    @media (min-width: 992px) {
        .ipb-layout { align-items: stretch; }
    }
</style>
