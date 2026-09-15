
<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames(([
    'src'      => null,
    'alt'      => '',
    'fallback' => '?',
    'size'     => 'default',
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
    'src'      => null,
    'alt'      => '',
    'fallback' => '?',
    'size'     => 'default',
]), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars, $__key, $__value); ?>

<?php
    $sizes = [
        'xs'      => 'size-7 text-[10px]',
        'sm'      => 'size-8 text-xs',
        'default' => 'size-10 text-sm',
        'lg'      => 'size-12 text-base',
        'xl'      => 'size-14 text-lg',
    ];
    $sizeClass = $sizes[$size] ?? $sizes['default'];
?>

<div <?php echo e($attributes->merge(['class' => "relative flex shrink-0 overflow-hidden rounded-full $sizeClass"])); ?>>
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($src): ?>
        <img src="<?php echo e($src); ?>" alt="<?php echo e($alt); ?>" loading="lazy" decoding="async" class="aspect-square h-full w-full object-cover" />
    <?php else: ?>
        <div class="flex h-full w-full items-center justify-center rounded-full bg-muted font-medium text-muted-foreground">
            <?php echo $fallback; ?>

        </div>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
</div><?php /**PATH C:\WebsiteTekkom - Copy\resources\views\components\ui\avatar.blade.php ENDPATH**/ ?>