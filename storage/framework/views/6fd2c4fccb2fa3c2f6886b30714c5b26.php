<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames((['href']));

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

foreach (array_filter((['href']), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars, $__key, $__value); ?>
<?php
    $path = str_starts_with($href, '/capstone/') ? substr($href, strlen('/capstone')) : $href;
    $state = request()->attributes->get('capstone_feature_access', []);
    $reason = $state ? \Modules\Capstone\Support\BladeFeatureAccess::reason($path, $state) : null;
?>
<a <?php if($reason): ?> role="link" aria-disabled="true" tabindex="-1" title="<?php echo e($reason); ?>" <?php else: ?> href="<?php echo e(url('/capstone'.$path)); ?>" <?php endif; ?> <?php echo e(($reason ? $attributes->except(['href', ':href', '@click']) : $attributes)->class(['opacity-50 cursor-not-allowed' => (bool) $reason])); ?>><?php echo e($slot); ?></a>
<?php /**PATH C:\WebsiteTekkom - Copy\Modules/Capstone\resources/views/components/feature-link.blade.php ENDPATH**/ ?>