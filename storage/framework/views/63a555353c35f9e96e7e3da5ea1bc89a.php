
<?php $__env->startSection('title','Edit Shipping Charge'); ?>

<?php $__env->startSection('css'); ?>
<style>
    /* 1. PROFESSIONAL CARD CONTAINER */
    .studio-card {
        background: #fff;
        border-radius: 12px;
        box-shadow: 0 5px 20px rgba(0,0,0,0.05);
        border: 1px solid #e2e8f0;
        overflow: hidden;
    }

    /* 2. FORM ELEMENTS */
    .input-clean {
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        padding: 12px 15px;
        border-radius: 8px;
        font-size: 14px;
        color: #334155;
        transition: all 0.2s;
    }
    .input-clean:focus {
        background: #fff;
        border-color: #2563eb;
        box-shadow: 0 0 0 4px rgba(37, 99, 235, 0.1);
    }
    
    .form-label-custom {
        font-size: 11px;
        text-transform: uppercase;
        color: #64748b;
        letter-spacing: 0.8px;
        font-weight: 700;
        margin-bottom: 8px;
        display: block;
    }

    /* 3. STATUS TOGGLE AREA */
    .status-toggle-box {
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        border-radius: 8px;
        padding: 15px;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    .status-text h6 {
        font-size: 14px;
        font-weight: 700;
        color: #334155;
        margin: 0;
    }
    .status-text small {
        font-size: 12px;
        color: #94a3b8;
    }
</style>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
<div class="container-fluid py-4">
    
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold m-0 text-dark">Edit Shipping Charge</h4>
            <span class="text-muted small">Update delivery area and cost details</span>
        </div>
        <div class="d-flex gap-2">
            <a href="<?php echo e(route('shippingcharges.index')); ?>" class="btn btn-light border fw-bold text-secondary px-3">
                Cancel
            </a>
            <button type="submit" form="shippingForm" class="btn btn-primary fw-bold px-4 shadow-sm">
                <i class="fe-save me-1"></i> Update Changes
            </button>
        </div>
    </div>

    <div class="row justify-content-center">
        <div class="col-lg-8 col-md-10">
            
            <div class="studio-card p-4">
                <form action="<?php echo e(route('shippingcharges.update')); ?>" method="POST" id="shippingForm" data-parsley-validate="">
                    <?php echo csrf_field(); ?>
                    <input type="hidden" value="<?php echo e($edit_data->id); ?>" name="id" />

                    <div class="row g-4">
                        
                        <div class="col-md-6">
                            <label class="form-label-custom">Area / Location Name <span class="text-danger">*</span></label>
                            <input type="text" 
                                   class="form-control input-clean <?php $__errorArgs = ['name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                                   name="name" 
                                   value="<?php echo e($edit_data->name); ?>" 
                                   id="name" 
                                   placeholder="e.g. Inside Dhaka" 
                                   required>
                            <?php $__errorArgs = ['name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                <div class="invalid-feedback d-block mt-1"><?php echo e($message); ?></div>
                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label-custom">Shipping Cost <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-end-0 text-muted">৳</span>
                                <input type="number" 
                                       class="form-control input-clean border-start-0 <?php $__errorArgs = ['amount'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                                       name="amount" 
                                       value="<?php echo e($edit_data->amount); ?>" 
                                       id="amount" 
                                       placeholder="0.00" 
                                       required>
                            </div>
                            <?php $__errorArgs = ['amount'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                <div class="invalid-feedback d-block mt-1"><?php echo e($message); ?></div>
                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>

                        <div class="col-12">
                            <label class="form-label-custom">Configuration Status</label>
                            <div class="status-toggle-box">
                                <div class="status-text">
                                    <h6>Active Status</h6>
                                    <small>Enable or disable this shipping charge</small>
                                </div>
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" name="status" value="1" 
                                           <?php if($edit_data->status==1): ?> checked <?php endif; ?> 
                                           style="width: 3em; height: 1.5em; cursor:pointer;">
                                </div>
                            </div>
                            <?php $__errorArgs = ['status'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                <div class="text-danger small mt-1"><?php echo e($message); ?></div>
                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>

                    </div> </form>
            </div>

        </div>
    </div>

</div>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('script'); ?>
<script src="<?php echo e(asset('public/backEnd/')); ?>/assets/libs/parsleyjs/parsley.min.js"></script>
<script src="<?php echo e(asset('public/backEnd/')); ?>/assets/js/pages/form-validation.init.js"></script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('backEnd.layouts.master', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\laragon\www\ecom1\resources\views/backEnd/shippingcharge/edit.blade.php ENDPATH**/ ?>