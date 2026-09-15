<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames((['name', 'size' => 16]));

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

foreach (array_filter((['name', 'size' => 16]), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars, $__key, $__value); ?>
<?php
    $path = match ($name) {
        'lock' => 'M6 11V7a6 6 0 0 1 12 0v4 M5 11h14v10H5z M12 15v2',
        'info' => 'M12 22a10 10 0 1 0 0-20 10 10 0 0 0 0 20 M12 11v6 M12 7h.01',
        'announcement' => 'M3 10v4h4l12 5V5L7 10H3z M7 14l2 7h3l-2-6 M22 9v6',
        'calendar' => 'M4 5h16v16H4z M8 3v4 M16 3v4 M4 11h16 M8 15h2 M14 15h2',
        'file' => 'M14 2H5v20h14V7z M14 2v5h5 M8 12h8 M8 16h6',
        'message' => 'M21 11a8 8 0 0 1-8 8H7l-4 3V5a2 2 0 0 1 2-2h8a8 8 0 0 1 8 8z M7 8h9 M7 12h6',
        'book' => 'M4 19.5A2.5 2.5 0 0 1 6.5 17H20 M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z',
        'clock' => 'M12 22a10 10 0 1 0 0-20 10 10 0 0 0 0 20 M12 6v6l4 2',
        'check' => 'm5 12 4 4L19 6',
        'close' => 'm6 6 12 12 M6 18 18 6',
        'arrow-left' => 'M19 12H5 m7-7-7 7 7 7',
        'arrow-right' => 'M5 12h14 m-7-7 7 7-7 7',
        'upload' => 'M12 16V3 m-5 5 5-5 5 5 M4 15v6h16v-6',
        'file-check' => 'M14 2H5v20h14V7z M14 2v5h5 m-11 7 3 3 5-6',
        'key' => 'M15 13a5 5 0 1 0 0-10 5 5 0 0 0 0 10 M11.5 11.5 3 20v1h4v-4h4v-3 M16 7h.01',
        default => '',
    };
?>
<svg <?php echo e($attributes->class(['mp-icon'])); ?> width="<?php echo e($size); ?>" height="<?php echo e($size); ?>" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false"><path d="<?php echo e($path); ?>"/></svg>
<?php /**PATH C:\WebsiteTekkom - Copy\Modules\EOffice\resources\views\components\manajemen-praktikum\ui\icon.blade.php ENDPATH**/ ?>