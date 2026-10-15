
<?php $__env->startSection('title', 'জেলা'); ?>
<?php $__env->startSection('content'); ?>
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
        <div>
            <a href="<?php echo e(route('admin.delivery.divisions.index')); ?>" class="btn btn-sm btn-outline-secondary mb-1">← বিভাগ</a>
            <h4 class="fw-bold m-0">বিভাগ: <?php echo e($division->name); ?></h4>
            <small class="text-muted">জেলা অনুযায়ী ডেলিভারি চার্জ সেট করুন</small>
        </div>
        <a href="<?php echo e(route('admin.delivery.districts.create', ['division' => $division->id])); ?>" class="btn btn-primary"><i class="fe-plus"></i> নতুন জেলা</a>
    </div>
    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-striped">
                    <thead><tr><th>#</th><th>জেলা</th><th>চার্জ (৳)</th><th>স্ট্যাটাস</th><th class="text-end">অ্যাকশন</th></tr></thead>
                    <tbody>
                        <?php $__empty_1 = true; $__currentLoopData = $show_data; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <tr>
                            <td><?php echo e($loop->iteration); ?></td>
                            <td><?php echo e($value->name); ?></td>
                            <td><?php echo e(number_format($value->delivery_charge)); ?></td>
                            <td><?php echo e($value->status ? 'চালু' : 'বন্ধ'); ?></td>
                            <td class="text-end">
                                <a href="<?php echo e(route('admin.delivery.upazilas.index', $value->id)); ?>" class="btn btn-sm btn-soft-info">উপজেলা</a>
                                <a href="<?php echo e(route('admin.delivery.districts.edit', $value->id)); ?>" class="btn btn-sm btn-primary">এডিট</a>
                                <form action="<?php echo e(route('admin.delivery.districts.destroy')); ?>" method="POST" class="d-inline delete-form-delivery">
                                    <?php echo csrf_field(); ?>
                                    <input type="hidden" name="hidden_id" value="<?php echo e($value->id); ?>">
                                    <button type="submit" class="btn btn-sm btn-danger">ডিলিট</button>
                                </form>
                            </td>
                        </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr><td colspan="5" class="text-center text-muted py-4">কোনো জেলা নেই। প্রথমে জেলা যোগ করুন।</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>
<?php $__env->startSection('script'); ?>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
document.querySelectorAll('.delete-form-delivery').forEach(function(form) {
    form.addEventListener('submit', function(e) {
        e.preventDefault();
        Swal.fire({title:'মুছে ফেলবেন?', icon:'warning', showCancelButton:true, confirmButtonText:'হ্যাঁ'})
            .then(r => { if (r.isConfirmed) form.submit(); });
    });
});
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('backEnd.layouts.master', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\laragon\www\ecom1\resources\views/backEnd/delivery/district_index.blade.php ENDPATH**/ ?>