
<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames(([
    'checked' => false,
    'name'    => null,
    'size'    => 'default',
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
    'checked' => false,
    'name'    => null,
    'size'    => 'default',
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
        'sm'      => ['track' => 'w-8 h-[18px]',  'thumb' => 'size-3.5', 'translate' => 'translate-x-3.5'],
        'default' => ['track' => 'w-10 h-[22px]',  'thumb' => 'size-4',   'translate' => 'translate-x-[18px]'],
        'lg'      => ['track' => 'w-12 h-[26px]',  'thumb' => 'size-5',   'translate' => 'translate-x-[22px]'],
    ];
    $s = $sizes[$size] ?? $sizes['default'];
?>

<label class="relative inline-flex items-center cursor-pointer">
    <input type="checkbox"
           <?php if($name): ?> name="<?php echo e($name); ?>" <?php endif; ?>
           <?php echo e($checked ? 'checked' : ''); ?>

           class="sr-only peer"
           <?php echo e($attributes->except(['class'])); ?>>
    <div class="<?php echo e($s['track']); ?> rounded-full bg-grey-200 peer-checked:bg-primary transition-colors peer-focus-visible:ring-2 peer-focus-visible:ring-ring peer-focus-visible:ring-offset-2 peer-disabled:opacity-50 peer-disabled:cursor-not-allowed">
        <div class="<?php echo e($s['thumb']); ?> absolute top-[3px] left-[3px] rounded-full bg-white shadow-sm transition-transform peer-checked:<?php echo e($s['translate']); ?>"></div>
    </div>
</label>
<?php /**PATH C:\WebsiteTekkom - Copy\resources\views\components\ui\toggle.blade.php ENDPATH**/ ?>