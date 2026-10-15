<?php $__env->startSection('title','Point of Sale'); ?>

<?php $__env->startSection('css'); ?>
<style>
    body{
        background:#eef1f8;
    }
    .pos-shell{
        background:#eef1f8;
        padding:10px 0 25px;
    }
    .pos-card{
        background:#ffffff;
        border-radius:14px;
        box-shadow:0 15px 30px rgba(15,23,42,0.08);
        padding:14px 14px 10px;
        border:1px solid rgba(148,163,184,0.25);
    }
    .pos-header-bar{
        background:linear-gradient(135deg,#4f46e5,#6366f1);
        color:#fff;
        border-radius:12px;
        padding:10px 14px;
        margin-bottom:10px;
        display:flex;
        justify-content:space-between;
        align-items:center;
    }
    .pos-header-bar h5{
        margin:0;
        font-size:16px;
        font-weight:600;
        letter-spacing:.3px;
    }
    .pos-badge-soft{
        padding:3px 10px;
        border-radius:999px;
        background:rgba(15,23,42,.18);
        font-size:12px;
    }

    /* LEFT – CART TABLE */
    .pos-cart-table thead{
        background:#f9fafb;
    }
    .pos-cart-table th{
        font-size:12px;
        text-transform:uppercase;
        letter-spacing:.03em;
        color:#64748b;
        border-bottom:1px solid #e2e8f0;
    }
    .pos-cart-table td{
        vertical-align:middle;
    }
    .pos-cart-table th:nth-child(2),
    .pos-cart-table td:nth-child(2){
        min-width:220px;
    }

    .qty-cart .quantity{
        display:flex;
        align-items:center;
        justify-content:center;
    }
    .qty-cart .quantity button{
        border:1px solid #cbd5f5;
        background:#e5edff;
        width:28px;
        height:28px;
        border-radius:6px;
        line-height:26px;
        text-align:center;
        padding:0;
        font-weight:600;
        color:#4f46e5;
    }
    .qty-cart .quantity input{
        width:40px;
        text-align:center;
        border:0;
        background:transparent;
        font-weight:600;
    }

    /* CUSTOMER + TOTAL CARD */
    .pos-section-title{
        font-size:13px;
        text-transform:uppercase;
        letter-spacing:.12em;
        color:#94a3b8;
        font-weight:600;
        margin-bottom:6px;
    }
    .pos-summary-table td{
        padding:6px 10px;
        font-size:14px;
    }
    .pos-summary-table tr:last-child td{
        border-top:1px dashed #e2e8f0;
        font-size:15px;
        font-weight:700;
    }
    .pos-grand-total{
        font-size:18px !important;
        color:#16a34a;
    }

    .btn-pos-primary{
        background:linear-gradient(135deg,#4f46e5,#6366f1);
        border:none;
        padding:9px 22px;
        border-radius:999px;
        font-weight:600;
        font-size:14px;
        box-shadow:0 10px 20px rgba(79,70,229,.35);
        color:#fff;
    }
    .btn-pos-primary:hover{
        opacity:.94;
        box-shadow:0 16px 30px rgba(79,70,229,.45);
    }

    /* RIGHT – PRODUCTS GRID */
    .pos-products-wrapper{
        max-height:520px;
        overflow-y:auto;
        padding-right:4px;
    }
    .pos-product-card{
        border-radius:12px;
        padding:8px 8px 10px;
        margin-bottom:10px;
        text-align:center;
        cursor:pointer;
        transition:.18s all;
        background:linear-gradient(145deg,#f9fafb,#e5edff);
        border:1px solid rgba(148,163,184,.35);
        position:relative;
    }
    .pos-product-card:hover{
        transform:translateY(-2px);
        box-shadow:0 12px 24px rgba(15,23,42,.15);
    }
    .pos-product-img{
        height:72px;
        object-fit:contain;
        margin-bottom:4px;
    }
    .pos-product-name{
        font-size:13px;
        font-weight:600;
        min-height:34px;
        color:#111827;
    }
    .pos-product-price{
        font-size:14px;
        font-weight:700;
        color:#16a34a;
    }
    .pos-stock-badge{
        position:absolute;
        top:6px;
        left:8px;
        background:rgba(30,64,175,.12);
        color:#1d4ed8;
        font-size:11px;
        padding:2px 6px;
        border-radius:999px;
    }

    .pos-search-bar input{
        border-radius:999px;
        border:1px solid #cbd5f5;
        font-size:13px;
        padding-left:32px;
    }
    .pos-search-bar .icon{
        position:absolute;
        top:50%;
        left:10px;
        transform:translateY(-50%);
        color:#94a3b8;
        font-size:13px;
    }
</style>
<link href="<?php echo e(asset('public/backEnd')); ?>/assets/libs/select2/css/select2.min.css" rel="stylesheet" type="text/css" />
<link href="<?php echo e(asset('public/backEnd')); ?>/assets/libs/summernote/summernote-lite.min.css" rel="stylesheet" type="text/css" />
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
<div class="container-fluid pos-shell">

    
    <div class="row mb-2">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <h4 class="mb-0">Point of Sale</h4>
                <form method="get" action="<?php echo e(route('admin.order.cart_clear')); ?>" class="d-inline">
                    <?php echo csrf_field(); ?>
                    <button type="submit" class="btn btn-sm btn-outline-danger rounded-pill delete-confirm" title="Clear Cart">
                        <i class="fas fa-trash-alt"></i> Cart Clear
                    </button>
                </form>
            </div>
        </div>
    </div>

    <div class="row g-3">


 
 <div class="col-lg-5">
            <div class="pos-card h-100">

                
                <div class="mb-2">
                    <div class="pos-section-title">Products</div>
                    <div class="pos-search-bar position-relative">
                        <span class="icon"><i class="fa fa-search"></i></span>
                        <input type="text"
                               id="product_search"
                               class="form-control form-control-sm"
                               placeholder="Search product by name...">
                    </div>
                </div>

                <div class="pos-products-wrapper">
                    <div class="row">
                        <?php $__currentLoopData = $products; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $p): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <?php $img = optional($p->image)->image ?? 'public/no-image.png'; ?>
                            <div class="col-6 mb-2 pos-product-wrapper" data-name="<?php echo e(strtolower($p->name)); ?>">
                                <div class="pos-product-card pos-add-product" data-id="<?php echo e($p->id); ?>">
                                    <span class="pos-stock-badge">Stock: <?php echo e($p->stock); ?></span>
                                    <img src="<?php echo e(asset($img)); ?>" class="pos-product-img" alt="">
                                    <div class="pos-product-name"><?php echo e($p->name); ?></div>
                                    <div class="pos-product-price">
                                        TK <?php echo e($p->new_price ?? $p->old_price); ?>

                                    </div>
                                </div>
                            </div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </div>
                </div>

            </div>
        </div>  
   
   
   
   
     
        <div class="col-lg-7">
            <div class="pos-card h-100">

                
                <div class="pos-header-bar mb-3">
                    <div>
                        <h5>Shop Store</h5>
                        <small class="pos-badge-soft">Walk-in Customer POS</small>
                    </div>
                    <div class="text-end">
                        <div style="font-size:12px;opacity:.8;">Session</div>
                        <div style="font-weight:600;">SL-<?php echo e(date('dmy-His')); ?></div>
                    </div>
                </div>

                
                <div class="table-responsive mb-3">
                    <table class="table table-bordered table-sm mb-0 pos-cart-table">
                        <thead>
                            <tr>
                                <th>Image</th>
                                <th>Item</th>
                                <th>Qty</th>
                                <th>Price</th>
                                <th>Subtotal</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody id="cartTable">
                            <?php echo $__env->make('backEnd.order.cart_table_rows', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                        </tbody>
                    </table>
                </div>

                
                <div class="mb-3 p-3 rounded" style="background:#f8fafc; border:1px solid #e2e8f0;">
                    <div class="pos-section-title mb-2">কুপন কোড</div>
                    <div class="d-flex gap-2 align-items-center flex-wrap">
                        <input type="text" id="pos_coupon_code" class="form-control form-control-sm" placeholder="কুপন কোড লিখুন" value="<?php echo e(Session::get('pos_coupon_code', '')); ?>" style="max-width:180px">
                        <button type="button" id="pos_apply_coupon" class="btn btn-sm btn-success rounded-pill">
                            <i class="fas fa-tag me-1"></i> অ্যাপ্লাই
                        </button>
                        <button type="button" id="pos_remove_coupon" class="btn btn-sm btn-outline-secondary rounded-pill" style="<?php echo e(Session::has('pos_coupon_code') ? '' : 'display:none'); ?>">
                            <i class="fas fa-times me-1"></i> রিমুভ
                        </button>
                        <span id="pos_coupon_msg" class="small text-muted"></span>
                    </div>
                </div>

                
                <form action="<?php echo e(route('admin.order.store')); ?>" method="POST" class="row pos_form" data-parsley-validate="" enctype="multipart/form-data" id="pos_order_form">
                    <?php echo csrf_field(); ?>
                    <input type="hidden" name="coupon_code" value="<?php echo e(Session::get('pos_coupon_code', '')); ?>">

                    
                    <div class="col-md-6">
                        <div class="pos-section-title">Customer</div>

                        <div class="mb-2">
                            <input type="text"
                                   id="name"
                                   class="form-control form-control-sm <?php $__errorArgs = ['name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                   placeholder="Customer Name"
                                   name="name" required>
                            <?php $__errorArgs = ['name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><span class="invalid-feedback"><strong><?php echo e($message); ?></strong></span><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>

                        <div class="mb-2">
                            <input type="number"
                                   id="phone"
                                   class="form-control form-control-sm <?php $__errorArgs = ['phone'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                   placeholder="Mobile Number"
                                   name="phone" required>
                            <?php $__errorArgs = ['phone'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><span class="invalid-feedback"><strong><?php echo e($message); ?></strong></span><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>

                        <div class="mb-2">
                            <input type="text"
                                   id="address"
                                   class="form-control form-control-sm <?php $__errorArgs = ['address'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                   placeholder="Address"
                                   name="address" required>
                            <?php $__errorArgs = ['address'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><span class="invalid-feedback"><strong><?php echo e($message); ?></strong></span><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>

                        <div class="mb-2">
                            <label class="small text-muted d-block mb-1">বিভাগ</label>
                            <select id="adm_pos_division"
                                    class="form-control form-control-sm <?php $__errorArgs = ['division_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                    name="division_id" required>
                                <option value="">বিভাগ নির্বাচন...</option>
                                <?php $__currentLoopData = ($divisions ?? []); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $d): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($d->id); ?>" <?php echo e((int) old('division_id', 0) === (int) $d->id ? 'selected' : ''); ?>><?php echo e($d->name); ?></option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                            <?php $__errorArgs = ['division_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><span class="invalid-feedback"><strong><?php echo e($message); ?></strong></span><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>
                        <div class="mb-2">
                            <label class="small text-muted d-block mb-1">জেলা</label>
                            <select id="adm_pos_district"
                                    class="form-control form-control-sm <?php $__errorArgs = ['district_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                    name="district_id" required disabled>
                                <option value="">আগে বিভাগ সিলেক্ট করুন</option>
                            </select>
                            <?php $__errorArgs = ['district_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><span class="invalid-feedback"><strong><?php echo e($message); ?></strong></span><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>
                        <div class="mb-2">
                            <label class="small text-muted d-block mb-1">উপজেলা</label>
                            <select id="adm_pos_upazila"
                                    class="form-control form-control-sm <?php $__errorArgs = ['upazila_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                    name="upazila_id" required disabled>
                                <option value="">আগে জেলা সিলেক্ট করুন</option>
                            </select>
                            <?php $__errorArgs = ['upazila_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><span class="invalid-feedback"><strong><?php echo e($message); ?></strong></span><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>
                    </div>

                    
                    <div class="col-md-6">
                        <div class="pos-section-title">Summary</div>

                        <?php
                            $subtotal = Cart::instance('pos_shopping')->subtotal();
                            $subtotal = str_replace(',','',$subtotal);
                            $subtotal = str_replace('.00', '',$subtotal);
                            $shipping = Session::get('pos_shipping');
                            $couponDiscount = Session::get('pos_discount', 0);
                            $grand = ($subtotal + $shipping) - $couponDiscount;
                        ?>

                        <table class="table table-borderless pos-summary-table mb-2" id="cart_details">
                            <tr>
                                <td>Sub Total</td>
                                <td class="text-end">৳<?php echo e($subtotal); ?></td>
                            </tr>
                            <tr>
                                <td>Shipping Fee</td>
                                <td class="text-end">৳<?php echo e($shipping); ?></td>
                            </tr>
                            <tr>
                                <td>কুপন ডিস্কাউন্ট</td>
                                <td class="text-end">৳<?php echo e($couponDiscount); ?></td>
                            </tr>
                            <tr>
                                <td>Grand Total</td>
                                <td class="text-end pos-grand-total">৳<?php echo e($grand); ?></td>
                            </tr>
                        </table>

                        <div class="text-end mt-1">
                            <button type="submit" class="btn btn-pos-primary">
                                Complete Sale
                            </button>
                        </div>
                    </div>

                </form>
            </div>
        </div>

 

    </div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('script'); ?>
<script src="<?php echo e(asset('public/backEnd/')); ?>/assets/libs/parsleyjs/parsley.min.js"></script>
<script src="<?php echo e(asset('public/backEnd/')); ?>/assets/js/pages/form-validation.init.js"></script>
<script src="<?php echo e(asset('public/backEnd/')); ?>/assets/libs/select2/js/select2.min.js"></script>
<script src="<?php echo e(asset('public/backEnd/')); ?>/assets/js/pages/form-advanced.init.js"></script>
<script src="<?php echo e(asset('public/backEnd/')); ?>/assets/libs//summernote/summernote-lite.min.js"></script>

<script>
    $(".summernote").summernote({
        placeholder: "Enter Your Text Here",
    });
</script>

<script type="text/javascript">
    $(document).ready(function () {
        $(".select2").select2();
    });

    // -------- CART CONTENT LOADERS ----------
    function cart_content() {
        $.ajax({
            type: "GET",
            url: "<?php echo e(route('admin.order.cart_content')); ?>",
            dataType: "html",
            success: function (cartinfo) {
                $("#cartTable").html(cartinfo);
            },
        });
    }
    function cart_details() {
        $.ajax({
            type: "GET",
            url: "<?php echo e(route('admin.order.cart_details')); ?>",
            dataType: "html",
            success: function (cartinfo) {
                $("#cart_details").html(cartinfo);
            },
        });
    }

    // -------- PRODUCT CLICK -> ADD TO CART ----------
    $(document).on("click", ".pos-add-product", function (e) {
        e.preventDefault();
        var id = $(this).data("id");
        if (id) {
            $.ajax({
                cache: false,
                type: "GET",
                data: { id: id },
                url: "<?php echo e(route('admin.order.cart_add')); ?>",
                dataType: "json",
                success: function (cartinfo) {
                    cart_content();
                    cart_details();
                },
            });
        }
    });

    // -------- CART QTY + / - (Delegated) ----------
    $(document).on("click", ".cart_increment", function (e) {
        e.preventDefault();
        var id = $(this).data("id");
        var qty = $(this).val();
        if (id) {
            $.ajax({
                cache: false,
                data: { id: id, qty: qty },
                type: "GET",
                url: "<?php echo e(route('admin.order.cart_increment')); ?>",
                dataType: "json",
                success: function (cartinfo) {
                    cart_content();
                    cart_details();
                },
            });
        }
    });

    $(document).on("click", ".cart_decrement", function (e) {
        e.preventDefault();
        var id = $(this).data("id");
        var qty = $(this).val();
        if (id) {
            $.ajax({
                cache: false,
                type: "GET",
                data: { id: id, qty: qty },
                url: "<?php echo e(route('admin.order.cart_decrement')); ?>",
                dataType: "json",
                success: function (cartinfo) {
                    cart_content();
                    cart_details();
                },
            });
        }
    });

    // -------- CART REMOVE ----------
    $(document).on("click", ".cart_remove", function (e) {
        e.preventDefault();
        var id = $(this).data("id");
        if (id) {
            $.ajax({
                cache: false,
                type: "GET",
                data: { id: id },
                url: "<?php echo e(route('admin.order.cart_remove')); ?>",
                dataType: "json",
                success: function (cartinfo) {
                    cart_content();
                    cart_details();
                },
            });
        }
    });

    // -------- COUPON APPLY ----------
    $("#pos_apply_coupon").on("click", function () {
        var code = $("#pos_coupon_code").val().trim();
        if (!code) {
            $("#pos_coupon_msg").removeClass("text-success").addClass("text-danger").text("কুপন কোড লিখুন");
            return;
        }
        $.ajax({
            type: "POST",
            url: "<?php echo e(route('admin.order.pos.apply_coupon')); ?>",
            data: { _token: "<?php echo e(csrf_token()); ?>", coupon_code: code },
            dataType: "json",
            success: function (res) {
                if (res.success) {
                    $("#pos_coupon_msg").removeClass("text-danger").addClass("text-success").text(res.message);
                    $("#pos_remove_coupon").show();
                    cart_details();
                } else {
                    $("#pos_coupon_msg").removeClass("text-success").addClass("text-danger").text(res.message || "কুপন বৈধ নয়");
                }
            },
            error: function (xhr) {
                var msg = xhr.responseJSON && xhr.responseJSON.message ? xhr.responseJSON.message : "ত্রুটি হয়েছে";
                $("#pos_coupon_msg").removeClass("text-success").addClass("text-danger").text(msg);
            }
        });
    });

    // -------- COUPON REMOVE ----------
    $("#pos_remove_coupon").on("click", function () {
        $.ajax({
            type: "GET",
            url: "<?php echo e(route('admin.order.pos.remove_coupon')); ?>",
            dataType: "json",
            success: function () {
                $("#pos_coupon_code").val("");
                $("#pos_coupon_msg").text("");
                $("#pos_remove_coupon").hide();
                cart_details();
            }
        });
    });

    // -------- SHIPPING (বিভাগ / জেলা / উপজেলা) ----------
    function admPosFillDistricts(divId, preselect, cb) {
        var $d = $('#adm_pos_district');
        var $u = $('#adm_pos_upazila');
        $d.prop('disabled', !divId).html(divId ? '<option value="">লোড...</option>' : '<option value="">আগে বিভাগ...</option>');
        $u.html('<option value="">আগে জেলা...</option>').prop('disabled', true);
        if (!divId) { if (cb) cb(); return; }
        $.get('<?php echo e(url('/ajax/delivery/districts')); ?>/' + divId, function (res) {
            var opts = '<option value="">জেলা নির্বাচন...</option>';
            (res.data || []).forEach(function (r) {
                opts += '<option value="' + r.id + '" data-charge="' + r.delivery_charge + '">' + r.name + ' (৳' + r.delivery_charge + ')</option>';
            });
            $d.html(opts).prop('disabled', false);
            if (preselect) $d.val(String(preselect));
            if (cb) cb();
        });
    }
    function admPosFillUpazilas(distId, preselect, cb) {
        var $u = $('#adm_pos_upazila');
        $u.prop('disabled', !distId).html(distId ? '<option value="">লোড...</option>' : '<option value="">আগে জেলা...</option>');
        if (!distId) { if (cb) cb(); return; }
        $.get('<?php echo e(url('/ajax/delivery/upazilas')); ?>/' + distId, function (res) {
            var opts = '<option value="">উপজেলা নির্বাচন...</option>';
            (res.data || []).forEach(function (r) { opts += '<option value="' + r.id + '">' + r.name + '</option>'; });
            $u.html(opts).prop('disabled', false);
            if (preselect) $u.val(String(preselect));
            if (cb) cb();
        });
    }
    $(document).on("change", "#adm_pos_division", function () {
        admPosFillDistricts($(this).val(), null, function () {
            cart_content(); cart_details();
        });
    });
    $(document).on("change", "#adm_pos_district", function () {
        var id = $(this).val();
        if (id) {
            $.ajax({
                type: "GET", data: { id: id }, url: "<?php echo e(route('admin.order.cart_shipping')); ?>", dataType: "json",
                complete: function () { cart_content(); cart_details(); }
            });
        }
        admPosFillUpazilas(id, null, function () {
            cart_content(); cart_details();
        });
    });

    <?php if((int)old('division_id',0)): ?>
    $(function () {
        var odiv = <?php echo e((int) old('division_id', 0)); ?>;
        var odist = <?php echo e((int) old('district_id', 0)); ?>;
        var oup = <?php echo e((int) old('upazila_id', 0)); ?>;
        admPosFillDistricts(odiv, odist || null, function () {
            if (odist) {
                $.ajax({ type: 'GET', data: { id: odist }, url: "<?php echo e(route('admin.order.cart_shipping')); ?>", dataType: 'json', complete: function() {} });
                admPosFillUpazilas(odist, oup || null, null);
            }
        });
    });
    <?php endif; ?>

    // -------- SIZE / COLOR SELECT (variant price update) ----------
    function updateCartVariant(rowId, productId, sizeId, colorId) {
        var $row = $('.cart-size-selector[data-id="'+rowId+'"]').closest('tr');
        if (!$row.length) $row = $('.cart-color-selector[data-id="'+rowId+'"]').closest('tr');
        var $sizeSelect = $row.find('.cart-size-selector');
        var $colorSelect = $row.find('.cart-color-selector');
        var sId = sizeId !== undefined ? sizeId : ($sizeSelect.length ? $sizeSelect.val() : '');
        var cId = colorId !== undefined ? colorId : ($colorSelect.length ? $colorSelect.val() : '');
        var pid = productId || $row.find('.cart-size-selector, .cart-color-selector').first().data('product-id') || '';
        $.ajax({
            cache: false,
            type: "GET",
            data: { id: rowId, product_id: pid, size_id: sId || '', color_id: cId || '' },
            url: "<?php echo e(route('admin.order.cart.update')); ?>",
            dataType: "json",
            success: function () {
                cart_content();
                cart_details();
            },
        });
    }
    $(document).on("change", ".cart-size-selector", function () {
        var rowId = $(this).data("id");
        var productId = $(this).data("product-id");
        var sizeId = $(this).val();
        updateCartVariant(rowId, productId, sizeId, undefined);
    });
    $(document).on("change", ".cart-color-selector", function () {
        var rowId = $(this).data("id");
        var productId = $(this).data("product-id");
        var colorId = $(this).val();
        updateCartVariant(rowId, productId, undefined, colorId);
    });

    // -------- FORM SUBMIT - আগে Size/Color সিঙ্ক করুন --------
    var posFormSubmitting = false;
    $("#pos_order_form").on("submit", function (e) {
        if (posFormSubmitting) return;
        e.preventDefault();
        var form = this;
        var rows = [];
        $(".cart-size-selector, .cart-color-selector").each(function () {
            var rowId = $(this).data("id");
            if (rowId && rows.indexOf(rowId) === -1) rows.push(rowId);
        });
        if (rows.length === 0) {
            posFormSubmitting = true;
            form.submit();
            return;
        }
        var promises = [];
        rows.forEach(function (rowId) {
            var $row = $('.cart-size-selector[data-id="'+rowId+'"]').closest('tr');
            if (!$row.length) $row = $('.cart-color-selector[data-id="'+rowId+'"]').closest('tr');
            var sId = $row.find('.cart-size-selector').val() || '';
            var cId = $row.find('.cart-color-selector').val() || '';
            var productId = $row.find('.cart-size-selector, .cart-color-selector').first().data('product-id') || '';
            promises.push($.ajax({
                type: "GET",
                url: "<?php echo e(route('admin.order.cart.update')); ?>",
                data: { id: rowId, product_id: productId, size_id: sId, color_id: cId },
                dataType: "json"
            }));
        });
        $.when.apply($, promises).always(function () {
            posFormSubmitting = true;
            setTimeout(function () { form.submit(); }, 150);
        });
    });

    // -------- PRODUCT SEARCH (Right side) ----------
    $("#product_search").on("keyup", function () {
        var q = $(this).val().toLowerCase();
        $(".pos-product-wrapper").each(function(){
            var name = $(this).data("name");
            if(name.indexOf(q) !== -1){
                $(this).show();
            }else{
                $(this).hide();
            }
        });
    });

</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('backEnd.layouts.master', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\laragon\www\ecom1\resources\views/backEnd/order/create.blade.php ENDPATH**/ ?>