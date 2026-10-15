<?php $__currentLoopData = $cartinfo; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key=>$value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
<tr>
  <td><img height="30" src="<?php echo e(asset($value->options->image)); ?>"></td>
  <td>
      <div class="fw-semibold"><?php echo e($value->name); ?></div>
        <?php
            $product = \App\Models\Product::find($value->id);
            $sizesList = collect();
            $colorsList = collect();
            if ($product) {
                $sizeIds = \App\Models\ProductVariantPrice::where('product_id', $product->id)->whereNotNull('size_id')->pluck('size_id')->unique()->filter();
                $colorIds = \App\Models\ProductVariantPrice::where('product_id', $product->id)->whereNotNull('color_id')->pluck('color_id')->unique()->filter();
                if ($sizeIds->isNotEmpty()) {
                    $sizesList = \App\Models\Size::whereIn('id', $sizeIds)->get();
                }
                if ($colorIds->isNotEmpty()) {
                    $colorsList = \App\Models\Color::whereIn('id', $colorIds)->get();
                }
                if ($sizesList->isEmpty() && $colorsList->isEmpty()) {
                    $sizesList = $product->sizes ?? collect();
                    $colorsList = $product->colors ?? collect();
                }
            }
            $hasSizes = $sizesList->isNotEmpty();
            $hasColors = $colorsList->isNotEmpty();
            $currentSizeId = $value->options->size_id ?? '';
            $currentColorId = $value->options->color_id ?? '';
        ?>

       <?php if($hasSizes || $hasColors): ?>
        <div class="d-flex flex-column gap-1 mt-2">
            <?php if($hasSizes): ?>
            <div>
                <label class="form-label small text-muted mb-0" style="font-size:11px">Size</label>
                <select class="form-select form-select-sm cart-size-selector" data-id="<?php echo e($value->rowId); ?>" data-product-id="<?php echo e($value->id); ?>" style="min-width:100px">
                    <option value="">Select</option>
                    <?php $__currentLoopData = $sizesList; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $s): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <option value="<?php echo e($s->id); ?>" <?php echo e($currentSizeId == $s->id ? 'selected' : ''); ?>><?php echo e($s->sizeName ?? $s->size_name ?? 'N/A'); ?></option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
            </div>
            <?php endif; ?>
            <?php if($hasColors): ?>
            <div>
                <label class="form-label small text-muted mb-0" style="font-size:11px">Color</label>
                <select class="form-select form-select-sm cart-color-selector" data-id="<?php echo e($value->rowId); ?>" data-product-id="<?php echo e($value->id); ?>" style="min-width:100px">
                    <option value="">Select</option>
                    <?php $__currentLoopData = $colorsList; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $c): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <option value="<?php echo e($c->id); ?>" <?php echo e($currentColorId == $c->id ? 'selected' : ''); ?>><?php echo e($c->colorName ?? $c->color_name ?? 'N/A'); ?></option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
            </div>
            <?php endif; ?>
        </div>
        <?php endif; ?>
  </td>
  <td>
    <div class="qty-cart vcart-qty">
      <div class="quantity">
          <button class="minus cart_decrement" value="<?php echo e($value->qty); ?>" data-id="<?php echo e($value->rowId); ?>">-</button>
          <input type="text" value="<?php echo e($value->qty); ?>" readonly />
          <button class="plus cart_increment" value="<?php echo e($value->qty); ?>" data-id="<?php echo e($value->rowId); ?>">+</button>
      </div>
  </div>
  </td>
  <td><?php echo e($value->price); ?></td>
  <td><?php echo e($value->price * $value->qty); ?></td>
  <td class="text-center">
    <button type="button" class="btn btn-light btn-sm cart_remove" data-id="<?php echo e($value->rowId); ?>">
        <i class="fa fa-times text-danger"></i>
    </button>
  </td>
</tr>
<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
<?php /**PATH D:\laragon\www\ecom1\resources\views/backEnd/order/cart_table_rows.blade.php ENDPATH**/ ?>