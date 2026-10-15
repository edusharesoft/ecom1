
<?php $__env->startSection('title', 'ডেলিভারি বিভাগ'); ?>
<?php $__env->startSection('content'); ?>
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
        <h4 class="fw-bold m-0">ডেলিভারি লোকেশন — বিভাগ</h4>
        <a href="<?php echo e(route('admin.delivery.divisions.create')); ?>" class="btn btn-primary"><i class="fe-plus"></i> নতুন বিভাগ</a>
    </div>
    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-striped">
                    <thead><tr><th>#</th><th>নাম</th><th>সাজানো</th><th>স্ট্যাটাস</th><th class="text-end">অ্যাকশন</th></tr></thead>
                    <tbody>
                        <?php $__currentLoopData = $show_data; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <tr>
                            <td><?php echo e($loop->iteration); ?></td>
                            <td><?php echo e($value->name); ?></td>
                            <td><?php echo e($value->sort_order); ?></td>
                            <td><?php echo e($value->status ? 'চালু' : 'বন্ধ'); ?></td>
                            <td class="text-end">
                                <a href="<?php echo e(route('admin.delivery.districts.index', ['division' => $value->id])); ?>" class="btn btn-sm btn-soft-info">জেলাসমূহ</a>
                                <a href="<?php echo e(route('admin.delivery.divisions.edit', $value->id)); ?>" class="btn btn-sm btn-primary">এডিট</a>
                                <form action="<?php echo e(route('admin.delivery.divisions.destroy')); ?>" method="POST" class="d-inline delete-form-delivery">
                                    <?php echo csrf_field(); ?>
                                    <input type="hidden" name="hidden_id" value="<?php echo e($value->id); ?>">
                                    <button type="submit" class="btn btn-sm btn-danger">ডিলিট</button>
                                </form>
                            </td>
                        </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
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

<?php echo $__env->make('backEnd.layouts.master', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\laragon\www\ecom1\resources\views/backEnd/delivery/division_index.blade.php ENDPATH**/ ?>