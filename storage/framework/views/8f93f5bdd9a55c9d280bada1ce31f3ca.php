<?php
    $subtotal = Cart::instance('shopping')->subtotal();
    $subtotal = str_replace(',', '', $subtotal);
    $subtotal = str_replace('.00', '', $subtotal);
    $shipping = Session::get('shipping') ? Session::get('shipping') : 0;
?>
<table class="cart_table table table-bordered table-striped text-center mb-0">
    <thead>
        <tr>
            <th style="width: 40%;">প্রোডাক্ট</th>
            <th style="width: 20%;">পরিমাণ</th>
            <th style="width: 20%;">মূল্য</th>
        </tr>
    </thead>
    <tbody>
        <?php $__currentLoopData = Cart::instance('shopping')->content(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <tr>
                <td class="text-left">
                    <a style="font-size: 14px;" href="<?php echo e(route('product', $value->options->slug)); ?>">
                        <img src="<?php echo e(asset($value->options->image)); ?>" height="30" width="30" alt="">
                        <?php echo e(Str::limit($value->name, 20)); ?>

                    </a>
                    <?php if(!empty($value->options->product_color)): ?>
                        <div class="small text-muted mt-1">কালার: <?php echo e($value->options->product_color); ?></div>
                    <?php endif; ?>
                    <?php if(!empty($value->options->product_size)): ?>
                        <div class="small text-muted">সাইজ: <?php echo e($value->options->product_size); ?></div>
                    <?php endif; ?>
                </td>
                <td width="15%" class="cart_qty">
                    <div class="qty-cart vcart-qty">
                        <div class="quantity">
                            <button type="button" class="minus cart_decrement" data-id="<?php echo e($value->rowId); ?>" data-campaign="1">-</button>
                            <input type="text" value="<?php echo e($value->qty); ?>" readonly />
                            <button type="button" class="plus cart_increment" data-id="<?php echo e($value->rowId); ?>" data-campaign="1">+</button>
                        </div>
                    </div>
                </td>
                <td><span class="alinur">৳</span> <?php echo e($value->price * $value->qty); ?></td>
            </tr>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </tbody>
    <tfoot>
        <tr>
            <th colspan="2" class="text-end px-4">মোট</th>
            <td>
                <span id="net_total"><span class="alinur">৳ </span><strong><?php echo e($subtotal); ?></strong></span>
            </td>
        </tr>
        <tr>
            <th colspan="2" class="text-end px-4">ডেলিভারি চার্জ</th>
            <td>
                <span id="cart_shipping_cost"><span class="alinur">৳ </span><strong><?php echo e($shipping); ?></strong></span>
            </td>
        </tr>
        <tr>
            <th colspan="2" class="text-end px-4">সর্বমোট</th>
            <td>
                <span id="grand_total"><span class="alinur">৳ </span><strong><?php echo e($subtotal + $shipping); ?></strong></span>
            </td>
        </tr>
    </tfoot>
</table>
<?php /**PATH D:\laragon\www\ecom1\resources\views/frontEnd/layouts/ajax/campaign-cart-table.blade.php ENDPATH**/ ?>