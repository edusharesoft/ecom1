{{-- নতুন অর্ডার পপআপ (লগইনের পর ড্যাশবোর্ডে) --}}
<style>
    #newOrdersModal .modal-dialog {
        max-width: 420px;
        margin: 1rem auto;
    }
    #newOrdersModal .modal-content {
        border: none;
        border-radius: 14px;
        overflow: hidden;
        box-shadow: 0 20px 50px rgba(15, 23, 42, 0.18), 0 0 0 1px rgba(255,255,255,0.06) inset;
    }
    #newOrdersModal .modal-header {
        background: linear-gradient(135deg, #4338ca 0%, #6366f1 100%);
        border: none;
        padding: 12px 14px;
        align-items: center;
    }
    #newOrdersModal .modal-header .btn-close {
        filter: brightness(0) invert(1);
        opacity: 0.9;
        width: 0.65rem;
        height: 0.65rem;
        margin: 0;
        padding: 0.5rem;
    }
    #newOrdersModal .modal-title,
    #newOrdersModal .modal-title span,
    #newOrdersModal .modal-title i {
        color: #ffffff !important;
    }
    #newOrdersModal .modal-title {
        font-weight: 700;
        font-size: 14px;
        display: flex;
        align-items: center;
        gap: 8px;
        line-height: 1.3;
        margin: 0;
    }
    #newOrdersModal .modal-title .nop-badge {
        background: rgba(255, 255, 255, 0.25);
        color: #ffffff !important;
        font-size: 11px;
        font-weight: 700;
        padding: 2px 8px;
        border-radius: 999px;
        min-width: 22px;
        text-align: center;
    }
    #newOrdersModal .nop-bell {
        font-size: 13px;
        opacity: 0.95;
        animation: nop-pulse 1.4s ease-in-out infinite;
    }
    #newOrdersModal .modal-body {
        padding: 6px 0;
        max-height: min(55vh, 340px);
        overflow-y: auto;
        background: #fafbfc;
    }
    #newOrdersModal .modal-body::-webkit-scrollbar { width: 4px; }
    #newOrdersModal .modal-body::-webkit-scrollbar-thumb {
        background: #cbd5e1;
        border-radius: 4px;
    }
    .nop-list { list-style: none; margin: 0; padding: 4px 8px; }
    .nop-item {
        display: grid;
        grid-template-columns: 1fr auto;
        gap: 6px 10px;
        align-items: center;
        padding: 8px 10px;
        margin-bottom: 4px;
        background: #fff;
        border: 1px solid #e8ecf1;
        border-radius: 10px;
        transition: border-color 0.15s, box-shadow 0.15s;
    }
    .nop-item:last-child { margin-bottom: 0; }
    .nop-item:hover {
        border-color: #c7d2fe;
        box-shadow: 0 2px 8px rgba(79, 70, 229, 0.08);
    }
    .nop-item-main { min-width: 0; }
    .nop-row-top {
        display: flex;
        align-items: center;
        gap: 6px;
        flex-wrap: wrap;
    }
    .nop-inv {
        font-weight: 700;
        color: #4f46e5;
        font-size: 12px;
    }
    .nop-status {
        font-size: 9px;
        font-weight: 700;
        padding: 2px 6px;
        border-radius: 999px;
        background: #eef2ff;
        color: #4338ca;
        text-transform: uppercase;
        letter-spacing: 0.02em;
    }
    .nop-name {
        font-weight: 600;
        color: #0f172a;
        font-size: 12px;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        margin-top: 2px;
    }
    .nop-meta {
        font-size: 10px;
        color: #94a3b8;
        margin-top: 1px;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }
    .nop-item-side {
        text-align: right;
        flex-shrink: 0;
    }
    .nop-amount {
        font-weight: 700;
        color: #059669;
        font-size: 12px;
        white-space: nowrap;
    }
    .nop-open {
        display: inline-block;
        margin-top: 4px;
        font-size: 10px;
        font-weight: 600;
        color: #fff !important;
        text-decoration: none;
        padding: 4px 10px;
        border-radius: 999px;
        background: linear-gradient(135deg, #4f46e5, #6366f1);
        border: none;
        line-height: 1.2;
    }
    .nop-open:hover {
        opacity: 0.92;
        color: #fff !important;
        transform: translateY(-1px);
    }
    #newOrdersModal .modal-footer {
        border-top: 1px solid #e8ecf1;
        padding: 8px 12px;
        background: #fff;
        gap: 8px;
    }
    .nop-sound-hint {
        font-size: 10px;
        color: #94a3b8;
        margin: 0;
        line-height: 1.3;
    }
    .nop-btn-dismiss {
        background: linear-gradient(135deg, #4f46e5, #6366f1);
        border: none;
        color: #fff !important;
        font-weight: 600;
        padding: 6px 16px;
        border-radius: 999px;
        font-size: 12px;
        white-space: nowrap;
    }
    .nop-btn-dismiss:hover { opacity: 0.92; color: #fff !important; }
    @keyframes nop-pulse {
        0%, 100% { transform: scale(1); opacity: 1; }
        50% { transform: scale(1.12); opacity: 0.85; }
    }
</style>

<div class="modal fade" id="newOrdersModal" tabindex="-1" aria-labelledby="newOrdersModalLabel" aria-hidden="true" data-bs-backdrop="static">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="newOrdersModalLabel">
                    <i class="fas fa-bell nop-bell"></i>
                    <span>নতুন অর্ডার</span>
                    <span class="nop-badge" id="nopCountBadge">০</span>
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="বন্ধ" id="nopCloseBtn"></button>
            </div>
            <div class="modal-body">
                <ul class="nop-list" id="nopOrderList"></ul>
            </div>
            <div class="modal-footer d-flex justify-content-between align-items-center">
                <p class="nop-sound-hint mb-0"><i class="fas fa-volume-up me-1"></i>নোটিফিকেশন</p>
                <button type="button" class="btn nop-btn-dismiss" id="nopDismissBtn">বুঝেছি</button>
            </div>
        </div>
    </div>
</div>
