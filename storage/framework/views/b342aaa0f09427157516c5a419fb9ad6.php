<div class="container-fluid order-index-shell order-index-page">

    <div class="oi-page-header">
        <div>
            <h4><?php echo e($order_status->name); ?> অর্ডার <span class="oi-badge-count"><?php echo e($order_status->orders_count); ?></span></h4>
            <div class="oi-sub">অর্ডার তালিকা · বাল্ক অ্যাকশন · ফ্রড চেক</div>
        </div>
        <div class="oi-header-actions">
            <a href="<?php echo e(route('admin.order.create')); ?>" class="btn btn-sm oi-btn-primary">
                <i class="fas fa-plus me-1"></i> POS অর্ডার
            </a>
        </div>
    </div>

    <div class="oi-card">
        <div class="oi-card-head">
            <h6><i class="fas fa-list-alt"></i> অর্ডার তালিকা</h6>
        </div>
        <div class="oi-card-body">
            <div class="oi-toolbar order-index-toolbar">
                <div class="oi-toolbar-actions order-index-toolbar-buttons flex-grow-1 min-w-0">
                    <ul class="oi-action-grid action2-btn action2-btn--wrap list-unstyled m-0">
                        <li><a data-bs-toggle="modal" data-bs-target="#asignUser" class="oi-btn-tool oi-btn-assign"><i class="fas fa-user-plus"></i> অ্যাসাইন</a></li>
                        <li><a data-bs-toggle="modal" data-bs-target="#changeStatus" class="oi-btn-tool oi-btn-status"><i class="fas fa-flag"></i> স্ট্যাটাস</a></li>
                        <li><a href="<?php echo e(route('admin.order.bulk_destroy')); ?>" class="oi-btn-tool oi-btn-delete order_delete"><i class="fas fa-trash-alt"></i> ডিলিট</a></li>
                        <li><a href="<?php echo e(route('admin.order.order_print')); ?>" class="oi-btn-tool oi-btn-print multi_order_print"><i class="fas fa-print"></i> প্রিন্ট</a></li>
                        <li><a href="<?php echo e(route('admin.order.order_print')); ?>" class="oi-btn-tool oi-btn-label multi_label_print"><i class="fas fa-tag"></i> লেবেল</a></li>
                        <?php if($steadfast): ?>
                            <li><a href="<?php echo e(route('admin.bulk_courier', 'steadfast')); ?>?status=5" class="oi-btn-tool oi-btn-courier multi_order_courier"><i class="fas fa-truck"></i> Steadfast</a></li>
                        <?php endif; ?>
                        <?php if($pathao_info): ?>
                            <li><a data-bs-toggle="modal" data-bs-target="#pathao" class="oi-btn-tool oi-btn-pathao"><i class="fas fa-truck"></i> Pathao</a></li>
                        <?php endif; ?>
                        <?php if(isset($redx_info) && $redx_info): ?>
                            <li><a href="<?php echo e(route('admin.bulk_courier', 'redx')); ?>?status=5" class="oi-btn-tool oi-btn-redx multi_order_courier"><i class="fas fa-truck"></i> RedX</a></li>
                        <?php endif; ?>
                    </ul>
                </div>
                <div class="oi-toolbar-search order-index-toolbar-search w-100 w-lg-auto">
                    <form class="oi-search-form order-search-form mb-0" method="GET">
                        <div class="oi-search-inner order-search-inner">
                            <input type="text" name="keyword" value="<?php echo e(request('keyword')); ?>" placeholder="ইনভয়েস, ফোন খুঁজুন..." class="form-control">
                            <select name="traffic_source" class="form-select order-traffic-filter" aria-label="ট্র্যাফিক উৎস">
                                <?php $__currentLoopData = isset($traffic_source_options) ? $traffic_source_options : ['' => 'সব ট্র্যাফিক']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $tsVal => $tsLabel): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($tsVal); ?>" <?php echo e((string) request('traffic_source', '') === (string) $tsVal ? 'selected' : ''); ?>><?php echo e($tsLabel); ?></option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                            <button type="submit" class="btn oi-btn-primary flex-shrink-0"><i class="fas fa-search me-1"></i> খুঁজুন</button>
                        </div>
                    </form>
                </div>
            </div>

            <p class="d-lg-none oi-scroll-hint" role="note"><i class="fas fa-arrows-alt-h me-1"></i> বাকি কলাম দেখতে বাম–ডানে স্লাইড করুন</p>

            <div class="oi-table-rail order-table-rail table-responsive" role="region" aria-label="অর্ডার টেবিল">
                <table id="datatable-buttons" class="table oi-table order-index-table w-100 mb-0">
                    <thead>
                        <tr>
                            <th><input type="checkbox" class="form-check-input checkall" value="" aria-label="সব সিলেক্ট"></th>
                            <th>#</th>
                            <th>অ্যাকশন</th>
                            <th>ইনভয়েস</th>
                            <th>তারিখ</th>
                            <th>গ্রাহক</th>
                            <th>ট্র্যাফিক</th>
                            <th>পরিমাণ</th>
                            <th>স্ট্যাটাস</th>
                            <th>ফ্রড চেক</th>
                        </tr>
                    </thead>
<?php /**PATH D:\laragon\www\ecom1\resources\views/backEnd/order/partials/index_header.blade.php ENDPATH**/ ?>