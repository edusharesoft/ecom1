{{-- অর্ডার কুইক ভিউ মডাল --}}
<div class="modal fade oi-modal oqv-modal-center" id="orderQuickViewModal" tabindex="-1" aria-labelledby="orderQuickViewModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title text-white" id="orderQuickViewModalLabel">
                    <i class="fas fa-eye me-1"></i> অর্ডার বিস্তারিত
                    <span id="oqvModalInvoice" class="badge bg-light text-dark ms-2"></span>
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="বন্ধ"></button>
            </div>
            <div class="modal-body" id="orderQuickViewBody">
                <div class="oqv-loading">
                    <div class="spinner-border text-primary" role="status"></div>
                    <p class="mt-3 mb-0">লোড হচ্ছে...</p>
                </div>
            </div>
        </div>
    </div>
</div>
