<style>
    body { background: #eef1f8; }
    .order-index-shell {
        padding: 8px 0 28px;
        padding-left: max(12px, env(safe-area-inset-left));
        padding-right: max(12px, env(safe-area-inset-right));
    }
    .oi-page-header {
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
        margin-bottom: 16px;
    }
    .oi-page-header h4 { margin: 0; font-weight: 700; color: #0f172a; font-size: 1.35rem; }
    .oi-page-header .oi-sub { font-size: 13px; color: #64748b; margin-top: 4px; }
    .oi-header-actions { display: flex; flex-wrap: wrap; gap: 8px; align-items: center; }
    .oi-badge-count {
        background: linear-gradient(135deg, #4f46e5, #6366f1);
        color: #fff;
        font-size: 12px;
        font-weight: 600;
        padding: 4px 12px;
        border-radius: 999px;
        margin-left: 6px;
        vertical-align: middle;
    }
    .oi-card {
        background: #fff;
        border-radius: 14px;
        border: 1px solid rgba(148, 163, 184, 0.28);
        box-shadow: 0 12px 28px rgba(15, 23, 42, 0.06);
        overflow: hidden;
        margin-bottom: 16px;
    }
    .oi-card-head {
        padding: 14px 18px;
        border-bottom: 1px solid #e2e8f0;
        background: linear-gradient(180deg, #fafbff 0%, #fff 100%);
    }
    .oi-card-head h6 { margin: 0; font-size: 14px; font-weight: 700; color: #1e293b; }
    .oi-card-head h6 i { color: #6366f1; margin-right: 6px; }
    .oi-card-body { padding: 16px 18px; }
    .oi-toolbar {
        display: flex;
        flex-direction: column;
        gap: 14px;
        margin-bottom: 16px;
    }
    @media (min-width: 992px) {
        .oi-toolbar { flex-direction: row; align-items: flex-start; justify-content: space-between; }
        .oi-toolbar-search { max-width: 440px; flex-shrink: 0; }
    }
    .oi-action-grid {
        display: flex;
        flex-wrap: wrap;
        gap: 8px;
        list-style: none;
        padding: 0;
        margin: 0;
    }
    .oi-action-grid > li { flex: 1 1 auto; }
    @media (min-width: 576px) { .oi-action-grid > li { flex: 0 1 auto; } }
    .oi-btn-tool {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 6px;
        width: 100%;
        padding: 8px 14px;
        font-size: 12px;
        font-weight: 600;
        border-radius: 999px;
        border: 1px solid transparent;
        white-space: nowrap;
        text-decoration: none;
        cursor: pointer;
    }
    @media (min-width: 576px) { .oi-btn-tool { width: auto; } }
    .oi-btn-tool:hover { text-decoration: none; opacity: .92; }
    .oi-btn-assign { background: #ecfdf5; color: #047857; border-color: #a7f3d0; }
    .oi-btn-status { background: #eef2ff; color: #4338ca; border-color: #c7d2fe; }
    .oi-btn-delete { background: #fef2f2; color: #b91c1c; border-color: #fecaca; }
    .oi-btn-print { background: #ecfeff; color: #0e7490; border-color: #a5f3fc; }
    .oi-btn-label { background: #f8fafc; color: #475569; border-color: #e2e8f0; }
    .oi-btn-courier { background: #fff7ed; color: #c2410c; border-color: #fed7aa; }
    .oi-btn-pathao { background: #fef9c3; color: #a16207; border-color: #fde047; }
    .oi-btn-redx { background: #fff7ed; color: #ea580c; border-color: #fdba74; }
    .oi-btn-primary {
        background: linear-gradient(135deg, #4f46e5, #6366f1);
        border: none;
        color: #fff !important;
        font-weight: 600;
        padding: 9px 20px;
        border-radius: 999px;
        box-shadow: 0 8px 18px rgba(79, 70, 229, 0.3);
    }
    .oi-search-form .oi-search-inner {
        display: flex;
        flex-direction: column;
        gap: 8px;
        width: 100%;
    }
    @media (min-width: 576px) {
        .oi-search-form .oi-search-inner { flex-direction: row; align-items: stretch; }
        .oi-search-form .oi-search-inner .form-control { flex: 1 1 auto; min-width: 0; }
        .oi-search-form .oi-search-inner .form-select { flex: 0 1 180px; }
    }
    .oi-search-form .form-control,
    .oi-search-form .form-select {
        border-radius: 10px;
        border-color: #cbd5e1;
        font-size: 13px;
    }
    .oi-table-rail, .order-table-rail {
        width: 100%;
        overflow-x: auto;
        overflow-y: visible;
        -webkit-overflow-scrolling: touch;
        overscroll-behavior: contain;
        border-radius: 10px;
        border: 1px solid #e2e8f0;
        position: relative;
        z-index: 1;
    }
    .oi-table, .order-index-table {
        margin-bottom: 0;
        width: 100%;
        max-width: 100%;
        table-layout: auto;
        vertical-align: middle;
    }
    /* মোবাইলে কলাম ভিড় এড়াতে স্ক্রল — ডেস্কটপে অপ্রয়োজনীয় গেপ নেই */
    @media (max-width: 991.98px) {
        .order-index-page .oi-table,
        .order-index-page .order-index-table {
            min-width: 860px;
        }
    }
    .oi-table thead, .order-index-table thead { background: #f8fafc; }
    .oi-table th, .order-index-table th {
        font-size: 11px;
        text-transform: uppercase;
        letter-spacing: 0.04em;
        color: #64748b;
        font-weight: 600;
        border-bottom: 1px solid #e2e8f0;
        padding: 10px 12px;
        white-space: nowrap;
    }
    .oi-table td, .order-index-table td {
        padding: 10px 12px;
        font-size: 13px;
        color: #334155;
        vertical-align: middle;
        border-bottom: 1px solid #f1f5f9;
    }
    .oi-table tbody tr:hover, .order-index-table tbody tr:hover { background: #fafbff; }
    .oi-actions-cell { white-space: nowrap; }
    .oi-row-actions {
        display: inline-flex;
        align-items: center;
        gap: 6px;
    }
    .oi-act-delete-form {
        display: inline-flex;
        margin: 0;
    }
    .oi-act-btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 5px;
        min-height: 34px;
        padding: 0 12px;
        font-size: 12px;
        font-weight: 600;
        line-height: 1;
        border-radius: 999px;
        border: 1px solid transparent;
        cursor: pointer;
        transition: background 0.2s ease, color 0.2s ease, border-color 0.2s ease, box-shadow 0.2s ease, transform 0.15s ease;
        box-shadow: 0 1px 2px rgba(15, 23, 42, 0.06);
    }
    .oi-act-btn i { font-size: 13px; }
    .oi-act-btn:focus {
        outline: none;
        box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.25);
    }
    .oi-act-btn:active { transform: scale(0.97); }
    .oi-act-view {
        background: linear-gradient(135deg, #eef2ff 0%, #e0e7ff 100%);
        color: #4338ca;
        border-color: #c7d2fe;
    }
    .oi-act-view:hover {
        background: linear-gradient(135deg, #4f46e5 0%, #6366f1 100%);
        color: #fff;
        border-color: #4f46e5;
        box-shadow: 0 4px 12px rgba(79, 70, 229, 0.35);
    }
    .oi-act-delete {
        background: linear-gradient(135deg, #fff1f2 0%, #ffe4e6 100%);
        color: #be123c;
        border-color: #fecdd3;
    }
    .oi-act-delete:hover {
        background: linear-gradient(135deg, #e11d48 0%, #f43f5e 100%);
        color: #fff;
        border-color: #e11d48;
        box-shadow: 0 4px 12px rgba(225, 29, 72, 0.3);
    }
    .oi-act-delete:focus {
        box-shadow: 0 0 0 3px rgba(244, 63, 94, 0.28);
    }
    @media (max-width: 575.98px) {
        .oi-act-label { display: none; }
        .oi-act-btn {
            width: 34px;
            padding: 0;
        }
    }
    .oi-invoice-link { font-weight: 700; color: #4f46e5; text-decoration: none; }
    .oi-invoice-link:hover { color: #3730a3; text-decoration: underline; }
    .oi-amount { font-weight: 700; color: #0f172a; }
    .oi-status-pill {
        display: inline-block;
        font-size: 11px;
        font-weight: 600;
        padding: 4px 10px;
        border-radius: 999px;
        background: #fef3c7;
        color: #b45309;
    }
    .oi-scroll-hint { font-size: 12px; color: #94a3b8; margin-bottom: 10px; }
    .oi-paginate .pagination { flex-wrap: wrap; justify-content: center; gap: 4px; margin-bottom: 0; }
    .oi-paginate .page-link { border-radius: 8px; min-width: 38px; text-align: center; }
    .oi-modal .modal-content { border-radius: 14px; border: none; overflow: hidden; }
    .oi-modal .modal-header {
        background: linear-gradient(135deg, #4f46e5, #6366f1);
        color: #fff;
        border: none;
    }
    .oi-modal .modal-header .btn-close { filter: brightness(0) invert(1); }
    #orderQuickViewModal .modal-header .modal-title,
    #orderQuickViewModal .modal-header .modal-title i,
    #noteModal .modal-header .modal-title,
    #noteModal .modal-header .modal-title i {
        color: #fff !important;
    }
    .oqv-notes-row {
        margin-bottom: 1.25rem;
    }
    .oqv-note-section .oqv-note-text {
        margin-bottom: 14px;
    }
    .oqv-note-section .oqv-section-title {
        margin-bottom: 10px;
    }
    /* কুইক ভিউ মডাল — শুধু খোলা থাকলে স্ক্রিনের মাঝখানে */
    #orderQuickViewModal.oqv-modal-center:not(.show) {
        display: none !important;
        pointer-events: none !important;
    }
    #orderQuickViewModal.oqv-modal-center.show {
        display: flex !important;
        align-items: center;
        justify-content: center;
        padding: 1rem;
    }
    #orderQuickViewModal.oqv-modal-center.show .modal-dialog {
        margin: 0;
        width: 100%;
        max-width: 920px;
        max-height: calc(100vh - 2rem);
        min-height: auto;
        display: flex;
        flex-direction: column;
    }
    #orderQuickViewModal .modal-content {
        max-height: calc(100vh - 2rem);
        width: 100%;
        display: flex;
        flex-direction: column;
    }
    #orderQuickViewModal.show .modal-body {
        max-height: calc(100vh - 10rem);
        overflow-y: auto;
        -webkit-overflow-scrolling: touch;
    }
    .oqv-loading { text-align: center; padding: 48px 20px; color: #64748b; }
    .oqv-section {
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        border-radius: 12px;
        padding: 14px 16px;
        margin-bottom: 14px;
    }
    .oqv-section-title {
        font-size: 13px;
        font-weight: 700;
        color: #1e293b;
        margin: 0 0 12px;
    }
    .oqv-section-title i { color: #6366f1; margin-right: 6px; }
    .oqv-customer-card { display: flex; gap: 14px; align-items: flex-start; }
    .oqv-avatar {
        width: 72px;
        height: 72px;
        border-radius: 12px;
        object-fit: cover;
        border: 2px solid #e2e8f0;
        flex-shrink: 0;
    }
    .oqv-customer-name { font-size: 16px; font-weight: 700; color: #0f172a; }
    .oqv-customer-phone, .oqv-customer-email { font-size: 13px; color: #475569; }
    .oqv-label {
        font-size: 10px;
        text-transform: uppercase;
        letter-spacing: 0.06em;
        color: #94a3b8;
        font-weight: 700;
    }
    .oqv-ip-code { font-size: 12px; background: #fff; padding: 4px 8px; border-radius: 6px; }
    .oqv-status-badge { background: #fef3c7; color: #b45309; font-weight: 600; }
    .oqv-tag-admin { background: #eef2ff; color: #4338ca; }
    .oqv-tag-vendor { background: #ecfeff; color: #0e7490; }
    .oqv-tag-reseller { background: #fff7ed; color: #c2410c; }
    .oqv-courier-info { font-size: 12px; color: #64748b; }
    .oqv-note-text { font-size: 13px; color: #334155; min-height: 2em; }
    .oqv-items-table thead { background: #fff; }
    .oqv-items-table th {
        font-size: 10px;
        text-transform: uppercase;
        color: #64748b;
        font-weight: 600;
    }
    .oqv-variant {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        font-size: 12px;
        font-weight: 600;
        color: #334155;
    }
    .oqv-color-swatch {
        width: 14px;
        height: 14px;
        border-radius: 50%;
        border: 1px solid rgba(15, 23, 42, 0.15);
        flex-shrink: 0;
    }
    .oqv-variant-size {
        padding: 2px 8px;
        background: #f1f5f9;
        border-radius: 6px;
        border: 1px solid #e2e8f0;
    }
    .oqv-product-thumb {
        width: 36px;
        height: 36px;
        object-fit: cover;
        border-radius: 6px;
        border: 1px solid #e2e8f0;
    }
    .oqv-total-row td { font-weight: 700; color: #4f46e5; }
    .oqv-fraud-badge { font-size: 12px; padding: 6px 12px; }
    .oqv-fraud-btn { border-radius: 999px; font-weight: 600; }
    .oqv-fraud-report {
        margin-top: 12px;
        padding-top: 12px;
        border-top: 1px dashed #cbd5e1;
    }
    .oqv-fraud-report:empty { display: none !important; margin: 0; padding: 0; border: none; }
    .oqv-fraud-report .container-fluid { padding: 0; }
    .oqv-fraud-report .table { font-size: 12px; margin-bottom: 0; }
    .oqv-fraud-report .table th,
    .oqv-fraud-report .table td { padding: 8px 10px; vertical-align: middle; }
    .oqv-fraud-report h5 { font-size: 15px; }
    .oqv-fraud-report h3 { font-size: 1.25rem; }
    .oqv-fraud-report .row.text-center .col-md-3 { flex: 1 1 50%; max-width: 50%; }
    @media (min-width: 768px) {
        .oqv-fraud-report .row.text-center .col-md-3 { max-width: 25%; flex: 0 0 25%; }
    }
    .oqv-actions {
        display: flex;
        flex-wrap: wrap;
        gap: 8px;
        padding-top: 8px;
        border-top: 1px solid #e2e8f0;
    }
    .oqv-actions .btn { border-radius: 999px; font-size: 12px; font-weight: 600; }
    .oqv-act-invoice { background: #eef2ff; color: #4338ca; border: 1px solid #c7d2fe; }
    .oqv-act-process { background: #ecfdf5; color: #047857; border: 1px solid #a7f3d0; }
    .oqv-act-edit { background: #f8fafc; color: #475569; border: 1px solid #e2e8f0; }
    .oqv-act-courier { background: #fff7ed; color: #c2410c; border: 1px solid #fed7aa; }
    @media (max-width: 991.98px) {
        .oi-table, .order-index-table { font-size: 12px; }
        .oi-table th, .oi-table td, .order-index-table th, .order-index-table td { padding: 8px !important; }
        .order-ip-wrap { flex-direction: column !important; align-items: flex-start !important; }
        #fraudCheckModal.modal .modal-dialog {
            max-height: calc(100vh - 1rem - env(safe-area-inset-top) - env(safe-area-inset-bottom));
        }
        #orderQuickViewModal.oqv-modal-center.show {
            padding: 0.5rem;
            align-items: center;
        }
        #orderQuickViewModal.oqv-modal-center.show .modal-dialog {
            max-height: calc(100vh - 1rem);
            max-width: calc(100vw - 1rem);
        }
    }
</style>
