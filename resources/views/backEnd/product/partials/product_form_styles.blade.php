<style>
    body { background: #eef1f8; }

    .product-form-page {
        padding: 4px 0 32px;
        padding-left: max(8px, env(safe-area-inset-left));
        padding-right: max(8px, env(safe-area-inset-right));
    }

    /* Page header */
    .product-form-page .pf-page-header {
        display: flex;
        flex-wrap: wrap;
        align-items: flex-start;
        justify-content: space-between;
        gap: 14px;
        margin-bottom: 20px;
        padding: 18px 20px;
        background: linear-gradient(135deg, #ffffff 0%, #f8fafc 55%, #eef2ff 100%);
        border-radius: 16px;
        border: 1px solid rgba(148, 163, 184, 0.35);
        box-shadow: 0 10px 28px rgba(15, 23, 42, 0.06);
    }
    .product-form-page .pf-page-header h4 {
        margin: 0;
        font-weight: 700;
        color: #0f172a;
        font-size: 1.35rem;
        line-height: 1.3;
    }
    .product-form-page .pf-page-header .pf-sub {
        font-size: 13px;
        color: #64748b;
        margin-top: 6px;
        max-width: 560px;
    }
    .product-form-page .pf-header-actions {
        display: flex;
        flex-wrap: wrap;
        gap: 8px;
        align-items: center;
    }
    .product-form-page .pf-btn-manage {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        background: linear-gradient(135deg, #4f46e5, #6366f1);
        border: none;
        color: #fff !important;
        font-weight: 600;
        padding: 10px 20px;
        border-radius: 999px;
        font-size: 13px;
        box-shadow: 0 6px 18px rgba(79, 70, 229, 0.3);
        text-decoration: none;
        transition: transform 0.2s, box-shadow 0.2s;
    }
    .product-form-page .pf-btn-manage:hover {
        color: #fff;
        transform: translateY(-1px);
        box-shadow: 0 8px 22px rgba(79, 70, 229, 0.38);
    }

    /* Cards */
    .product-form-page .card {
        border: 1px solid rgba(148, 163, 184, 0.28);
        border-radius: 14px;
        box-shadow: 0 12px 28px rgba(15, 23, 42, 0.06);
        background: #fff;
        overflow: hidden;
        margin-bottom: 18px;
    }
    .product-form-page .card-body {
        padding: 20px 22px;
    }

    /* Section titles */
    .product-form-page .section-title {
        background: linear-gradient(90deg, #eef2ff 0%, #f8fafc 100%);
        padding: 12px 16px;
        border-radius: 10px;
        font-weight: 700;
        color: #1e293b;
        border-left: 4px solid #6366f1;
        margin-bottom: 20px;
        font-size: 14px;
        display: flex;
        align-items: center;
        flex-wrap: wrap;
        gap: 8px;
    }
    .product-form-page .section-title i {
        color: #4f46e5;
        font-size: 16px;
    }

    /* Labels & inputs */
    .product-form-page .form-label {
        font-weight: 600;
        font-size: 12px;
        color: #475569;
        margin-bottom: 6px;
        letter-spacing: 0.02em;
    }
    .product-form-page .form-control,
    .product-form-page .form-select {
        border-radius: 10px;
        border: 1px solid #e2e8f0;
        font-size: 13px;
        padding: 10px 12px;
        background: #f8fafc;
        transition: border-color 0.2s, box-shadow 0.2s, background 0.2s;
    }
    .product-form-page .form-control:focus,
    .product-form-page .form-select:focus {
        border-color: #818cf8;
        box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.15);
        background: #fff;
    }
    .product-form-page .form-control.border-primary {
        border-color: #6366f1 !important;
        background: #faf5ff;
    }
    .product-form-page .form-control.font-weight-bold {
        font-weight: 700;
        color: #0f172a;
    }
    .product-form-page .form-control.is-invalid {
        border-color: #f87171;
        background: #fff5f5;
    }
    .product-form-page .invalid-feedback {
        font-size: 12px;
    }
    .product-form-page small.text-muted {
        font-size: 11px;
        color: #94a3b8 !important;
    }

    /* Select2 */
    .product-form-page .select2-container--default .select2-selection--single,
    .product-form-page .select2-container--default .select2-selection--multiple {
        border-radius: 10px !important;
        border: 1px solid #e2e8f0 !important;
        min-height: 42px;
        background: #f8fafc !important;
    }
    .product-form-page .select2-container--default.select2-container--focus .select2-selection--single,
    .product-form-page .select2-container--default.select2-container--focus .select2-selection--multiple {
        border-color: #818cf8 !important;
        box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.12);
    }

    /* Summernote */
    .product-form-page .note-editor.note-frame {
        border-radius: 12px;
        border: 1px solid #e2e8f0;
        overflow: hidden;
        box-shadow: 0 4px 12px rgba(15, 23, 42, 0.04);
    }
    .product-form-page .note-toolbar {
        background: #f8fafc;
        border-bottom: 1px solid #e2e8f0;
    }

    /* Variant & wholesale rows */
    .product-form-page .variant-card {
        background: linear-gradient(180deg, #fafbff 0%, #f8fafc 100%);
        border: 1px dashed #c7d2fe;
        padding: 16px 18px;
        border-radius: 12px;
        margin-bottom: 14px;
        position: relative;
        transition: border-color 0.2s, box-shadow 0.2s;
    }
    .product-form-page .variant-card:hover {
        border-color: #a5b4fc;
        box-shadow: 0 6px 16px rgba(99, 102, 241, 0.08);
    }
    .product-form-page .color-group {
        margin-bottom: 18px;
    }
    .product-form-page .sizes-wrapper {
        margin-left: 16px;
        padding-left: 12px;
        border-left: 2px solid #e0e7ff;
    }
    .product-form-page .size-row {
        background: #fff;
        border: 1px solid #e2e8f0;
        border-radius: 10px;
        padding: 10px;
    }

    /* Toggle switch */
    .product-form-page .switch {
        position: relative;
        display: inline-block;
        width: 44px;
        height: 24px;
        flex-shrink: 0;
    }
    .product-form-page .switch input {
        opacity: 0;
        width: 0;
        height: 0;
    }
    .product-form-page .slider {
        position: absolute;
        cursor: pointer;
        inset: 0;
        background: #cbd5e1;
        transition: 0.3s;
        border-radius: 24px;
    }
    .product-form-page .slider:before {
        position: absolute;
        content: "";
        height: 18px;
        width: 18px;
        left: 3px;
        bottom: 3px;
        background: #fff;
        transition: 0.3s;
        border-radius: 50%;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.15);
    }
    .product-form-page input:checked + .slider {
        background: linear-gradient(135deg, #10b981, #059669);
    }
    .product-form-page input:checked + .slider:before {
        transform: translateX(20px);
    }

    /* Buttons in form */
    .product-form-page .btn-success.add-variant,
    .product-form-page .btn-success.add-wholesale-tier,
    .product-form-page .section-title .btn-success {
        background: linear-gradient(135deg, #10b981, #059669);
        border: none;
        font-weight: 600;
        box-shadow: 0 4px 12px rgba(16, 185, 129, 0.25);
    }
    .product-form-page .btn-success.add-variant:hover,
    .product-form-page .btn-success.add-wholesale-tier:hover {
        opacity: 0.95;
        transform: translateY(-1px);
    }
    .product-form-page .btn-danger.btn-remove-row,
    .product-form-page .btn-danger.remove-variant,
    .product-form-page .btn-danger.btn-remove-wholesale,
    .product-form-page .btn-remove-image {
        border-radius: 10px;
    }
    .product-form-page .btn-increment {
        border-radius: 10px;
        font-weight: 600;
    }
    .product-form-page .btn-remove-row {
        margin-top: 28px;
    }

    /* Wholesale toggle card */
    .product-form-page .card:has(#is_wholesale) .card-body,
    .product-form-page .wholesale-toggle-card .card-body {
        padding: 16px 22px;
    }
    .product-form-page #wholesale_area .variant-card {
        border-style: solid;
        border-color: #bbf7d0;
        background: linear-gradient(180deg, #f0fdf4 0%, #f8fafc 100%);
    }

    /* Media / gallery */
    .product-form-page .increment-wrapper .image-row {
        padding: 12px;
        background: #f8fafc;
        border-radius: 10px;
        border: 1px solid #e2e8f0;
        margin-bottom: 10px !important;
    }
    .product-form-page .edit-image {
        width: 72px;
        height: 72px;
        object-fit: cover;
        border-radius: 10px;
        border: 2px solid #e2e8f0;
        box-shadow: 0 4px 10px rgba(15, 23, 42, 0.08);
    }
    .product-form-page .product_img .position-relative img {
        transition: transform 0.2s;
    }
    .product-form-page .product_img .position-relative:hover img {
        transform: scale(1.03);
    }
    .product-form-page .variant-img-preview img {
        border-radius: 8px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
    }
    .product-form-page .variant-existing-imgs img {
        border-radius: 8px;
        box-shadow: 0 2px 6px rgba(0, 0, 0, 0.08);
    }

    /* Video section */
    .product-form-page .form-check-label {
        font-size: 13px;
        font-weight: 500;
        color: #334155;
    }
    .product-form-page #yt_preview_c iframe,
    .product-form-page #yt_preview_e iframe,
    .product-form-page #up_preview_c video,
    .product-form-page #up_preview_e video {
        border-radius: 12px !important;
        box-shadow: 0 8px 20px rgba(15, 23, 42, 0.12);
    }
    .product-form-page #digital_area {
        border-radius: 12px !important;
        border: 1px dashed #c7d2fe !important;
        background: #f5f3ff !important;
    }

    /* Settings toggles row */
    .product-form-page .row.text-center .form-label {
        font-size: 11px;
        text-transform: uppercase;
        letter-spacing: 0.03em;
        color: #64748b;
    }

    /* Submit */
    .product-form-page button[type="submit"].btn-success {
        background: linear-gradient(135deg, #059669, #10b981);
        border: none;
        font-weight: 700;
        padding: 14px 20px;
        letter-spacing: 0.02em;
        box-shadow: 0 10px 24px rgba(16, 185, 129, 0.35);
        transition: transform 0.2s, box-shadow 0.2s;
    }
    .product-form-page button[type="submit"].btn-success:hover {
        transform: translateY(-2px);
        box-shadow: 0 14px 28px rgba(16, 185, 129, 0.4);
    }

    /* Sidebar: compact, no inner scroll trap */
    .product-form-page .pf-sidebar-col .card {
        margin-bottom: 0;
    }
    .product-form-page .pf-sidebar-panel {
        border-radius: 14px;
        overflow: hidden;
    }
    .product-form-page .pf-sidebar-panel > .card-body {
        padding: 0;
    }
    .product-form-page .pf-side-block {
        padding: 12px 14px;
        border-bottom: 1px solid #e8ecf4;
    }
    .product-form-page .pf-side-block:last-of-type {
        border-bottom: none;
    }
    .product-form-page .pf-side-foot {
        padding: 12px 14px 14px;
        background: linear-gradient(180deg, #f8fafc 0%, #fff 100%);
        border-top: 1px solid #e8ecf4;
    }
    .product-form-page .pf-side-head {
        display: flex;
        align-items: center;
        gap: 6px;
        font-size: 12px;
        font-weight: 700;
        color: #334155;
        margin-bottom: 10px;
        text-transform: uppercase;
        letter-spacing: 0.04em;
    }
    .product-form-page .pf-side-head i {
        color: #6366f1;
        font-size: 14px;
    }
    .product-form-page .pf-sidebar-col .section-title {
        display: none;
    }
    .product-form-page .pf-sidebar-col .form-label {
        font-size: 11px;
        margin-bottom: 2px;
    }
    .product-form-page .pf-sidebar-col .form-label small {
        font-size: 10px;
    }
    .product-form-page .pf-sidebar-col .form-control,
    .product-form-page .pf-sidebar-col .form-select {
        padding: 6px 9px;
        font-size: 12px;
        min-height: 34px;
    }
    .product-form-page .pf-sidebar-col .form-control-sm {
        padding: 4px 8px;
        min-height: 30px;
    }
    .product-form-page .pf-sidebar-col .form-group,
    .product-form-page .pf-sidebar-col .mb-3,
    .product-form-page .pf-sidebar-col .mb-2 {
        margin-bottom: 8px !important;
    }
    .product-form-page .pf-sidebar-col .row.g-compact {
        --bs-gutter-y: 0.35rem;
        --bs-gutter-x: 0.5rem;
    }
    .product-form-page .pf-sidebar-col .pf-field-hint {
        display: none;
    }
    .product-form-page .pf-sidebar-col .increment-wrapper .image-row {
        padding: 6px 8px;
        margin-bottom: 6px !important;
    }
    .product-form-page .pf-sidebar-col .image-row .form-label.small {
        display: none;
    }
    .product-form-page .pf-sidebar-col .product_img {
        margin-top: 6px !important;
        max-height: 72px;
        overflow-x: auto;
        overflow-y: hidden;
        flex-wrap: nowrap !important;
    }
    .product-form-page .pf-sidebar-col .edit-image {
        width: 48px;
        height: 48px;
        margin-right: 4px;
    }
    .product-form-page .pf-sidebar-col .pf-video-radios {
        gap: 8px !important;
        margin-bottom: 6px !important;
    }
    .product-form-page .pf-sidebar-col .pf-video-radios .form-check-label {
        font-size: 11px;
    }
    .product-form-page .pf-sidebar-col #yt_preview_c iframe,
    .product-form-page .pf-sidebar-col #yt_preview_e iframe,
    .product-form-page .pf-sidebar-col #up_preview_c video,
    .product-form-page .pf-sidebar-col #up_preview_e video {
        height: 100px !important;
        max-height: 100px;
    }
    .product-form-page .pf-sidebar-col .pf-free-delivery {
        gap: 8px;
    }
    .product-form-page .pf-sidebar-col .pf-free-delivery small {
        font-size: 10px;
        line-height: 1.3;
    }
    .product-form-page .pf-sidebar-col .row.text-center {
        margin-bottom: 8px !important;
    }
    .product-form-page .pf-sidebar-col .row.text-center > [class*="col-"] {
        margin-bottom: 4px !important;
        padding-left: 4px;
        padding-right: 4px;
    }
    .product-form-page .pf-sidebar-col button[type="submit"] {
        padding: 10px 16px;
        font-size: 14px;
    }
    .product-form-page .pf-sidebar-col .select2-container--default .select2-selection--single,
    .product-form-page .pf-sidebar-col .select2-container--default .select2-selection--multiple {
        min-height: 34px !important;
    }
    .product-form-page .pf-sidebar-col #digital_area {
        padding: 8px !important;
        margin-bottom: 8px !important;
    }

    @media (max-width: 991px) {
        .product-form-page .pf-page-header {
            padding: 14px 16px;
        }
        .product-form-page .pf-page-header h4 {
            font-size: 1.15rem;
        }
    }

    .pf-ai-desc-btn {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 7px 14px;
        font-size: 12px;
        font-weight: 600;
        color: #fff !important;
        background: linear-gradient(135deg, #7c3aed, #6366f1);
        border: none;
        border-radius: 999px;
        box-shadow: 0 4px 12px rgba(99, 102, 241, 0.35);
        cursor: pointer;
        transition: opacity 0.2s, transform 0.2s;
    }
    .pf-ai-desc-btn:hover:not(:disabled) {
        opacity: 0.92;
        transform: translateY(-1px);
        color: #fff;
    }
    .pf-ai-desc-btn:disabled {
        opacity: 0.65;
        cursor: not-allowed;
    }
    .pf-desc-label-row {
        display: flex;
        flex-wrap: wrap;
        justify-content: space-between;
        align-items: center;
        gap: 8px;
        margin-bottom: 8px;
    }
</style>
