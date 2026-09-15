<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames((['name', 'size' => 16, 'class' => '']));

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

foreach (array_filter((['name', 'size' => 16, 'class' => '']), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars, $__key, $__value); ?>
<?php
    $iconPath = public_path("images/icons/{$name}.svg");
    $svg = file_exists($iconPath) ? file_get_contents($iconPath) : '';
    // Replace known hardcoded colors with currentColor
    $svg = str_replace(
        ['#0D0D12', 'stroke="black"', 'fill="black"', 'stroke="#000"', 'fill="#000"'],
        ['currentColor', 'stroke="currentColor"', 'fill="currentColor"', 'stroke="currentColor"', 'fill="currentColor"'],
        $svg
    );
    // Force width/height to 100% so it fills the container
    $svg = preg_replace('/\bwidth="24"/', 'width="100%"', $svg);
    $svg = preg_replace('/\bheight="24"/', 'height="100%"', $svg);
?>
<span style="display:inline-flex;align-items:center;justify-content:center;width:<?php echo e($size); ?>px;height:<?php echo e($size); ?>px;flex-shrink:0;vertical-align:middle;line-height:0;" <?php if($class): ?> class="<?php echo e($class); ?>" <?php endif; ?>><?php echo $svg; ?></span>
<?php /**PATH C:\WebsiteTekkom - Copy\Modules\ManajemenMahasiswa\resources\views\components\ui\icon.blade.php ENDPATH**/ ?>