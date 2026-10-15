<?php
    $ship = $order->shipping;
    $customer = $order->customer;
    $oqvDefaultAvatar = asset('public/uploads/default/user.svg');
    $customerImg = $oqvDefaultAvatar;
    if ($customer) {
        $imgPath = trim((string) ($customer->profile_image_url ?? $customer->image ?? ''));
        if ($imgPath !== '' && file_exists(public_path($imgPath))) {
            $customerImg = asset($imgPath);
        }
    }

    $isReseller = !empty($order->reseller_profit) || !empty($order->customer_payable_amount);
    $isIpBlocked = $order->ip_address && in_array($order->ip_address, $blockedIps ?? []);

    $types = [];
    foreach ($order->orderdetails as $item) {
        if ($item->product && $item->product->is_digital == 1) {
            $types[] = 'Digital';
        } else {
            $types[] = 'Physical';
        }
    }
    $types = array_unique($types);
    $productTypeLabel = count($types) === 1 ? $types[0] : (count($types) > 1 ? 'Mixed' : '—');

    $vendors = [];
    $hasAdminProduct = false;
    foreach ($order->orderdetails as $item) {
        if ($item->vendor_id && $item->vendor) {
            $vendors[$item->vendor_id] = $item->vendor->shop_name ?? $item->vendor->owner_name ?? 'Vendor';
        } else {
            $hasAdminProduct = true;
        }
    }

    $tsKey = strtolower(trim((string) ($order->traffic_source ?? 'direct')));
    $tsLabel = $traffic_source_options[$tsKey] ?? ucfirst($tsKey ?: 'direct');
    $tsReferrer = trim((string) ($order->traffic_referrer ?? ''));

    $orderNote = $order->order_note ?? $order->note ?? '';
    $adminNote = $order->admin_note ?? '';

    $fraudRate = $order->fraud_rate;
    $fraudNull = is_null($fraudRate);

    if ($isReseller && $order->customer_payable_amount) {
        $subtotal = $order->customer_payable_amount - ($order->shipping_charge ?? 0);
        $finalTotal = $order->customer_payable_amount;
    } else {
        $subtotal = $order->orderdetails->sum(fn ($i) => $i->sale_price * $i->qty);
        $finalTotal = $order->amount;
    }

    $trackingId = $order->courier_tracking_id ?? $order->consignment_id ?? null;
    $courierType = $order->courier_type;
    if (!$courierType && $trackingId) {
        $courierType = 'steadfast';
    }
?>

<div class="oqv-wrap" data-order-id="<?php echo e($order->id); ?>" data-invoice-id="<?php echo e($order->invoice_id); ?>">
    <div class="row g-3">
        
        <div class="col-md-5">
            <div class="oqv-section">
                <h6 class="oqv-section-title"><i class="fas fa-user"></i> কাস্টমার</h6>
                <div class="oqv-customer-card">
                    <img src="<?php echo e($customerImg); ?>"
                         alt="<?php echo e($ship->name ?? $customer->name ?? 'কাস্টমার'); ?>"
                         class="oqv-avatar"
                         data-fallback="<?php echo e($oqvDefaultAvatar); ?>"
                         onerror="if(this.dataset.fallback){this.onerror=null;this.src=this.dataset.fallback;}">
                    <div class="oqv-customer-meta">
                        <div class="oqv-customer-name"><?php echo e($ship->name ?? $customer->name ?? '—'); ?></div>
                        <div class="oqv-customer-phone"><i class="fas fa-phone text-muted me-1"></i><?php echo e($ship->phone ?? $customer->phone ?? '—'); ?></div>
                        <?php if($customer && $customer->email): ?>
                        <div class="oqv-customer-email"><i class="fas fa-envelope text-muted me-1"></i><?php echo e($customer->email); ?></div>
                        <?php endif; ?>
                        <?php if($ship && $ship->address): ?>
                        <div class="oqv-customer-addr mt-2"><i class="fas fa-map-marker-alt text-muted me-1"></i><?php echo e($ship->address); ?><?php if($ship->area): ?>, <?php echo e($ship->area); ?><?php endif; ?></div>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="oqv-ip-box mt-3">
                    <span class="oqv-label">IP অ্যাড্রেস</span>
                    <div class="d-flex align-items-center gap-2 flex-wrap mt-1">
                        <code class="oqv-ip-code"><?php echo e($order->ip_address ?: '—'); ?></code>
                        <?php if($order->ip_address): ?>
                            <?php if($isIpBlocked): ?>
                                <span class="badge bg-secondary"><i class="fas fa-shield-alt"></i> ব্লক করা</span>
                            <?php else: ?>
                                <button type="button" class="btn btn-sm btn-danger block-ip-btn"
                                    data-ip="<?php echo e($order->ip_address); ?>"
                                    data-reason="ফেইক অর্ডার">
                                    <i class="fas fa-ban"></i> IP ব্লক
                                </button>
                            <?php endif; ?>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>

        
        <div class="col-md-7">
            <div class="oqv-section">
                <h6 class="oqv-section-title"><i class="fas fa-receipt"></i> অর্ডার তথ্য</h6>
                <div class="row g-2 oqv-meta-grid">
                    <div class="col-6 col-lg-4">
                        <span class="oqv-label">ইনভয়েস</span>
                        <strong class="d-block">#<?php echo e($order->invoice_id); ?></strong>
                    </div>
                    <div class="col-6 col-lg-4">
                        <span class="oqv-label">তারিখ</span>
                        <strong class="d-block"><?php echo e($order->created_at->format('d M Y, h:i A')); ?></strong>
                    </div>
                    <div class="col-6 col-lg-4">
                        <span class="oqv-label">স্ট্যাটাস</span>
                        <span class="badge oqv-status-badge"><?php echo e($order->status->name ?? '—'); ?></span>
                    </div>
                    <div class="col-6 col-lg-4">
                        <span class="oqv-label">পণ্যের ধরন</span>
                        <strong class="d-block"><?php echo e($productTypeLabel); ?></strong>
                    </div>
                    <div class="col-6 col-lg-4">
                        <span class="oqv-label">মোট</span>
                        <strong class="d-block text-success">৳<?php echo e(number_format($finalTotal, 2)); ?></strong>
                    </div>
                    <div class="col-6 col-lg-4">
                        <span class="oqv-label">ট্র্যাফিক</span>
                        <span class="badge bg-primary"><?php echo e($tsLabel); ?></span>
                        <?php if($tsReferrer !== ''): ?>
                        <small class="d-block text-muted mt-1"><?php echo e(Str::limit($tsReferrer, 60)); ?></small>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="oqv-tags mt-2">
                    <?php if($hasAdminProduct): ?>
                        <span class="badge oqv-tag-admin"><i class="fas fa-store"></i> অ্যাডমিন পণ্য</span>
                    <?php endif; ?>
                    <?php $__currentLoopData = $vendors; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $vName): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <span class="badge oqv-tag-vendor"><i class="fas fa-store"></i> <?php echo e($vName); ?></span>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    <?php if($isReseller): ?>
                        <span class="badge oqv-tag-reseller">
                            <i class="fas fa-user-tag"></i>
                            রিসেলার
                            <?php if($order->user): ?> — <?php echo e($order->user->name); ?><?php endif; ?>
                            <?php if($order->reseller_profit): ?> · লাভ ৳<?php echo e(number_format($order->reseller_profit, 0)); ?><?php endif; ?>
                        </span>
                    <?php endif; ?>
                </div>

                <?php if($courierType || $trackingId): ?>
                <div class="oqv-courier-info mt-2">
                    <i class="fas fa-truck"></i> <?php echo e(ucfirst($courierType ?? 'Courier')); ?>

                    <?php if($trackingId): ?> · <?php echo e($trackingId); ?><?php endif; ?>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    
    <div class="oqv-section oqv-fraud-section">
        <h6 class="oqv-section-title"><i class="fas fa-shield-alt"></i> ফ্রড চেক</h6>
        <div class="d-flex align-items-center gap-3 flex-wrap mb-2">
            <div id="oqvFraudStatus" class="oqv-fraud-status">
                <?php if($fraudNull): ?>
                    <span class="badge bg-warning text-dark oqv-fraud-badge">যাচাই করা হয়নি</span>
                <?php elseif($fraudRate >= 80): ?>
                    <span class="badge bg-success oqv-fraud-badge"><?php echo e($fraudRate); ?>% নিরাপদ</span>
                <?php else: ?>
                    <span class="badge bg-danger oqv-fraud-badge"><?php echo e($fraudRate); ?>% ঝুঁকি</span>
                <?php endif; ?>
            </div>
            <?php if($ship && $ship->phone): ?>
            <button type="button"
                    class="btn btn-sm oqv-fraud-btn fraud-check-oqv <?php echo e($fraudNull ? 'btn-warning' : ($fraudRate >= 80 ? 'btn-success' : 'btn-danger')); ?>"
                    data-mobile="<?php echo e($ship->phone); ?>"
                    data-order-id="<?php echo e($order->id); ?>">
                <i class="fas fa-search"></i> ফ্রড চেক করুন
            </button>
            <?php else: ?>
            <span class="text-muted small">ফোন নম্বর নেই</span>
            <?php endif; ?>
        </div>
        <div id="oqvFraudReport" class="oqv-fraud-report"></div>
    </div>

    
    <div class="row g-3 oqv-notes-row">
        <div class="col-md-6">
            <div class="oqv-section oqv-note-section">
                <h6 class="oqv-section-title"><i class="fas fa-sticky-note"></i> অর্ডার নোট</h6>
                <p class="oqv-note-text"><?php echo e($orderNote ?: '— কোনো নোট নেই —'); ?></p>
                <button type="button" class="btn btn-sm btn-outline-info note-modal-btn"
                    data-type="order" data-id="<?php echo e($order->id); ?>" data-note="<?php echo e($orderNote); ?>">
                    <i class="fas fa-edit"></i> <?php echo e($orderNote ? 'সম্পাদনা' : 'যোগ করুন'); ?>

                </button>
            </div>
        </div>
        <div class="col-md-6">
            <div class="oqv-section oqv-note-section">
                <h6 class="oqv-section-title"><i class="fas fa-user-shield"></i> অ্যাডমিন নোট</h6>
                <p class="oqv-note-text"><?php echo e($adminNote ?: '— কোনো নোট নেই —'); ?></p>
                <button type="button" class="btn btn-sm btn-outline-warning note-modal-btn"
                    data-type="admin" data-id="<?php echo e($order->id); ?>" data-note="<?php echo e($adminNote); ?>">
                    <i class="fas fa-edit"></i> <?php echo e($adminNote ? 'সম্পাদনা' : 'যোগ করুন'); ?>

                </button>
            </div>
        </div>
    </div>

    
    <div class="oqv-section">
        <h6 class="oqv-section-title"><i class="fas fa-box-open"></i> পণ্য (<?php echo e($order->orderdetails->count()); ?>)</h6>
        <div class="table-responsive">
            <table class="table table-sm oqv-items-table mb-0">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>পণ্য</th>
                        <th>রঙ</th>
                        <th>সাইজ</th>
                        <th>উৎস</th>
                        <th class="text-center">পরিমাণ</th>
                        <th class="text-end">দাম</th>
                        <th class="text-end">সাবটোটাল</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $__currentLoopData = $order->orderdetails; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <?php
                        $ownerLabel = ($item->vendor_id && $item->vendor)
                            ? ($item->vendor->shop_name ?? 'ভেন্ডর')
                            : 'অ্যাডমিন';
                        $ownerClass = $item->vendor_id ? 'oqv-tag-vendor' : 'oqv-tag-admin';
                        $itemType = ($item->product && $item->product->is_digital == 1) ? 'Digital' : 'Physical';

                        $colorName = '—';
                        $colorHex = null;
                        if ($item->color) {
                            $colorName = $item->color->name ?? $item->color->colorName ?? $item->color->color_name ?? '—';
                            $colorHex = $item->color->color ?? null;
                        } elseif (!empty($item->product_color)) {
                            if (is_numeric($item->product_color)) {
                                $c = \App\Models\Color::find($item->product_color);
                                if ($c) {
                                    $colorName = $c->name ?? $c->colorName ?? $c->color_name ?? '—';
                                    $colorHex = $c->color ?? null;
                                }
                            } else {
                                $colorName = $item->product_color;
                            }
                        }

                        $sizeDisplay = '—';
                        if ($item->size) {
                            $sizeDisplay = $item->size->sizeName ?? $item->size->size_name ?? $item->size->name ?? '—';
                        } elseif (!empty($item->product_size)) {
                            if (is_numeric($item->product_size)) {
                                $s = \App\Models\Size::find($item->product_size);
                                $sizeDisplay = $s ? ($s->sizeName ?? $s->size_name ?? $s->name ?? '—') : '—';
                            } else {
                                $sizeDisplay = $item->product_size;
                            }
                        }
                    ?>
                    <tr>
                        <td><?php echo e($loop->iteration); ?></td>
                        <td>
                            <div class="d-flex align-items-center gap-2">
                                <?php if($item->image && $item->image->image): ?>
                                <img src="<?php echo e(asset($item->image->image)); ?>" alt="" class="oqv-product-thumb">
                                <?php endif; ?>
                                <div>
                                    <strong><?php echo e($item->product_name); ?></strong>
                                    <small class="d-block text-muted"><?php echo e($itemType); ?></small>
                                </div>
                            </div>
                        </td>
                        <td>
                            <span class="oqv-variant oqv-variant-color">
                                <?php if($colorHex): ?>
                                <span class="oqv-color-swatch" style="background-color: <?php echo e($colorHex); ?>;" title="<?php echo e($colorName); ?>"></span>
                                <?php endif; ?>
                                <?php echo e($colorName); ?>

                            </span>
                        </td>
                        <td><span class="oqv-variant oqv-variant-size"><?php echo e($sizeDisplay); ?></span></td>
                        <td><span class="badge <?php echo e($ownerClass); ?>"><?php echo e($ownerLabel); ?></span></td>
                        <td class="text-center"><?php echo e($item->qty); ?></td>
                        <td class="text-end">৳<?php echo e(number_format($item->sale_price, 2)); ?></td>
                        <td class="text-end fw-semibold">৳<?php echo e(number_format($item->sale_price * $item->qty, 2)); ?></td>
                    </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="7" class="text-end">সাবটোটাল</td>
                        <td class="text-end fw-bold">৳<?php echo e(number_format($subtotal, 2)); ?></td>
                    </tr>
                    <tr>
                        <td colspan="7" class="text-end">ডেলিভারি</td>
                        <td class="text-end">৳<?php echo e(number_format($order->shipping_charge ?? 0, 2)); ?></td>
                    </tr>
                    <?php if(($order->discount ?? 0) > 0): ?>
                    <tr>
                        <td colspan="7" class="text-end">ছাড়</td>
                        <td class="text-end text-danger">−৳<?php echo e(number_format($order->discount, 2)); ?></td>
                    </tr>
                    <?php endif; ?>
                    <tr class="oqv-total-row">
                        <td colspan="7" class="text-end"><?php echo e($isReseller ? 'গ্রাহক প্রদেয়' : 'মোট'); ?></td>
                        <td class="text-end">৳<?php echo e(number_format($finalTotal, 2)); ?></td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>

    
    <div class="oqv-actions">
        <a href="<?php echo e(route('admin.order.invoice', $order->invoice_id)); ?>" class="btn btn-sm oqv-act-invoice" target="_blank">
            <i class="fas fa-file-invoice"></i> ইনভয়েস
        </a>
        <a href="<?php echo e(route('admin.order.process', $order->invoice_id)); ?>" class="btn btn-sm oqv-act-process">
            <i class="fas fa-cog"></i> প্রসেসিং
        </a>
        <a href="<?php echo e(route('admin.order.edit', $order->invoice_id)); ?>" class="btn btn-sm oqv-act-edit">
            <i class="fas fa-edit"></i> এডিট
        </a>
        <?php if($steadfast): ?>
        <button type="button" class="btn btn-sm oqv-act-courier oi-quick-courier" data-courier="steadfast" data-order-id="<?php echo e($order->id); ?>">
            <i class="fas fa-truck"></i> Steadfast
        </button>
        <?php endif; ?>
        <?php if($pathao_info): ?>
        <button type="button" class="btn btn-sm oqv-act-courier oi-quick-pathao" data-order-id="<?php echo e($order->id); ?>">
            <i class="fas fa-truck"></i> Pathao
        </button>
        <?php endif; ?>
        <?php if($redx_info): ?>
        <button type="button" class="btn btn-sm oqv-act-courier oi-quick-courier" data-courier="redx" data-order-id="<?php echo e($order->id); ?>">
            <i class="fas fa-truck"></i> RedX
        </button>
        <?php endif; ?>
    </div>
</div>
<?php /**PATH D:\laragon\www\ecom1\resources\views/backEnd/order/partials/order_quick_view_body.blade.php ENDPATH**/ ?>