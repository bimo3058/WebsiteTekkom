<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames((['variant' => 'default']));

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

foreach (array_filter((['variant' => 'default']), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars, $__key, $__value); ?>
<?php $colors = ['default'=>'border-transparent bg-primary text-primary-foreground', 'secondary'=>'border-transparent bg-secondary text-secondary-foreground', 'destructive'=>'border-transparent bg-destructive text-white', 'outline'=>'text-foreground']; ?>
<span <?php echo e($attributes->class(['inline-flex items-center justify-center rounded-md border px-2 py-0.5 text-xs font-medium w-fit whitespace-nowrap shrink-0 gap-1', $colors[$variant] ?? $colors['default']])); ?>><?php echo e($slot); ?></span>
<?php /**PATH C:\WebsiteTekkom - Copy\Modules/Capstone\resources/views/components/badge.blade.php ENDPATH**/ ?>