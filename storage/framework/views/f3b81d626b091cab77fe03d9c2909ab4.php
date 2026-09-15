<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames(([
    'name'     => 'file',
    'accept'   => '.pdf',
    'maxLabel' => 'PDF (Maks. 1MB)',
    'disabled' => false,
    'required' => false,
    'inputId'  => null,
]));

foreach ($attributes->all() as $__key => $__value) {
    if (in_array($__key, $__propNames)) {
        $$__key = $$__key ?? $__value;
    } else {
        $__newAttributes[$__key] = $__value;
    }
}

$attributes = new \Illuminate\View\ComponentAttributeBag($__newAttributes);

unset($__propNames);
unset($__newAttributes);

foreach (array_filter(([
    'name'     => 'file',
    'accept'   => '.pdf',
    'maxLabel' => 'PDF (Maks. 1MB)',
    'disabled' => false,
    'required' => false,
    'inputId'  => null,
]), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars, $__key, $__value); ?>
<?php
    $inputId = $inputId ?? 'upload-' . $name;
?>

<label class="upload-zone <?php echo e($disabled ? 'closed' : ''); ?>" id="zone-<?php echo e($inputId); ?>">
    <input
        type="file"
        name="<?php echo e($name); ?>"
        id="<?php echo e($inputId); ?>"
        accept="<?php echo e($accept); ?>"
        <?php echo e($required ? 'required' : ''); ?>

        <?php echo e($disabled ? 'disabled' : ''); ?>

        onchange="handleUploadChange('<?php echo e($inputId); ?>')">
    <i class="fas fa-cloud-upload-alt" id="icon-<?php echo e($inputId); ?>"></i>
    <strong id="text-<?php echo e($inputId); ?>">
        <?php echo e($disabled ? 'Upload tidak tersedia' : 'Klik untuk unggah atau seret file ke sini'); ?>

    </strong>
    <span id="sub-<?php echo e($inputId); ?>"><?php echo e($maxLabel); ?></span>
</label>

<script>
if (typeof handleUploadChange === 'undefined') {
    function handleUploadChange(inputId) {
        const input  = document.getElementById(inputId);
        if (!input || !input.files[0]) return;

        const file   = input.files[0];
        const textEl = document.getElementById('text-' + inputId);
        const subEl  = document.getElementById('sub-'  + inputId);
        const iconEl = document.getElementById('icon-' + inputId);

        if (textEl) textEl.textContent = file.name;

        if (subEl) {
            const kb = file.size / 1024;
            subEl.textContent = kb >= 1024
                ? (kb / 1024).toFixed(1) + ' MB'
                : kb.toFixed(0) + ' KB';
        }

        if (iconEl) {
            iconEl.className   = 'fas fa-file-check';
            iconEl.style.color = 'var(--primary-blue)';
        }
    }
}
</script>
<?php /**PATH C:\WebsiteTekkom - Copy\Modules\BankSoal\resources\views\components\ui\upload-zone.blade.php ENDPATH**/ ?>