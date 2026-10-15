<div class="mb-3">
    <label class="form-label">API Base URL <small class="text-muted fw-normal">(ঐচ্ছিক)</small></label>
    <input type="text" class="form-control" name="url"
           value="<?php echo e($steadfast->url ?? 'https://portal.packzy.com/api/v1'); ?>"
           placeholder="https://portal.packzy.com/api/v1" autocomplete="off" />
    <small class="text-muted small-hint d-block mt-1">ডিফল্ট: portal.packzy.com/api/v1</small>
</div>

<div class="mb-3">
    <label class="form-label">Webhook URL <small class="text-muted fw-normal">(Steadfast ড্যাশবোর্ডে বসান)</small></label>
    <div class="input-group">
        <input type="text" class="form-control" name="webhook_url" id="steadfast_webhook_url"
               value="<?php echo e($steadfast->webhook_url ?? ''); ?>"
               placeholder="<?php echo e(rtrim(config('app.url'), '/')); ?>/api/steadfast/webhook"
               autocomplete="off" />
        <button type="button" class="btn btn-outline-secondary copy-steadfast-webhook" title="কপি">
            <i class="fe-copy"></i>
        </button>
    </div>
    <small class="text-muted small-hint d-block mt-1">
        প্রস্তাবিত: <code id="steadfast_suggested_webhook"><?php echo e(rtrim(config('app.url'), '/')); ?>/api/steadfast/webhook</code>
    </small>
</div>

<div class="mb-3">
    <label class="form-label">Webhook Bearer Token</label>
    <div class="input-group">
        <input type="text" class="form-control <?php $__errorArgs = ['token'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
               name="token" id="steadfast_webhook_token"
               value="<?php echo e($steadfast->token ?? ''); ?>"
               placeholder="Authorization: Bearer {token}"
               autocomplete="off" />
        <button type="button" class="btn btn-outline-primary" id="generate_steadfast_webhook_token" title="নতুন টোকেন">
            <i class="fe-refresh-cw"></i>
        </button>
    </div>
    <small class="text-muted small-hint d-block mt-1">
        Steadfast-এ <strong>Authorization: Bearer {token}</strong> হেডারে একই মান দিন। খালি রাখলে যাচাই বন্ধ (প্রোডাকশনে ব্যবহার করবেন না)।
    </small>
    <?php $__errorArgs = ['token'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
        <div class="invalid-feedback d-block"><?php echo e($message); ?></div>
    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
</div>
<?php /**PATH D:\laragon\www\ecom1\resources\views/backEnd/apiintegration/partials/steadfast_webhook_fields.blade.php ENDPATH**/ ?>