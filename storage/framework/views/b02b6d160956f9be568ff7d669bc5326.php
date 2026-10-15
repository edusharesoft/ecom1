
<?php $__env->startSection('title', 'জেলা এডিট'); ?>
<?php $__env->startSection('content'); ?>
<div class="container-fluid py-4">
    <div class="card">
        <div class="card-body">
            <h5 class="mb-3">জেলা এডিট</h5>
            <form action="<?php echo e(route('admin.delivery.districts.update')); ?>" method="POST">
                <?php echo csrf_field(); ?>
                <input type="hidden" name="id" value="<?php echo e($edit_data->id); ?>">
                <input type="hidden" name="division_id" value="<?php echo e($edit_data->division_id); ?>">
                <div class="mb-3"><label class="form-label">বিভাগ</label><input type="text" class="form-control" value="<?php echo e($edit_data->division->name ?? ''); ?>" disabled></div>
                <div class="mb-3"><label class="form-label">জেলার নাম *</label><input type="text" name="name" value="<?php echo e(old('name', $edit_data->name)); ?>" class="form-control" required maxlength="190"></div>
                <div class="mb-3"><label class="form-label">ডেলিভারি চার্জ (৳) *</label><input type="number" name="delivery_charge" value="<?php echo e(old('delivery_charge', $edit_data->delivery_charge)); ?>" class="form-control" required min="0"></div>
                <div class="mb-3"><label class="form-label">সাজানো</label><input type="number" name="sort_order" value="<?php echo e(old('sort_order', $edit_data->sort_order)); ?>" class="form-control"></div>
                <div class="form-check mb-3"><input class="form-check-input" type="checkbox" name="status" value="1" id="st" <?php echo e($edit_data->status ? 'checked' : ''); ?>><label class="form-check-label" for="st">সক্রিয়</label></div>
                <button type="submit" class="btn btn-success">আপডেট</button>
                <a href="<?php echo e(route('admin.delivery.districts.index', $edit_data->division_id)); ?>" class="btn btn-light">ফিরে যান</a>
            </form>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('backEnd.layouts.master', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\laragon\www\ecom1\resources\views/backEnd/delivery/district_edit.blade.php ENDPATH**/ ?>