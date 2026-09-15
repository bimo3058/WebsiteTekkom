<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames((['variant' => 'default', 'size' => 'default', 'href' => null]));

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

foreach (array_filter((['variant' => 'default', 'size' => 'default', 'href' => null]), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars, $__key, $__value); ?>
<?php
    $base = "inline-flex items-center justify-center gap-2 whitespace-nowrap rounded-md text-sm font-medium transition-all disabled:pointer-events-none disabled:opacity-50 [&_svg]:pointer-events-none [&_svg:not([class*='size-'])]:size-4 shrink-0 [&_svg]:shrink-0 outline-none focus-visible:border-ring focus-visible:ring-ring/50 focus-visible:ring-[3px] aria-invalid:ring-destructive/20 dark:aria-invalid:ring-destructive/40 aria-invalid:border-destructive";
    $variants = ['default' => 'bg-primary text-primary-foreground hover:bg-primary/90', 'destructive' => 'bg-destructive text-white hover:bg-destructive/90 focus-visible:ring-destructive/20 dark:focus-visible:ring-destructive/40 dark:bg-destructive/60', 'outline' => 'border bg-background shadow-xs hover:bg-accent hover:text-accent-foreground dark:bg-input/30 dark:border-input dark:hover:bg-input/50', 'secondary' => 'bg-secondary text-secondary-foreground hover:bg-secondary/80', 'ghost' => 'hover:bg-accent hover:text-accent-foreground dark:hover:bg-accent/50', 'link' => 'text-primary underline-offset-4 hover:underline'];
    $sizes = ['default' => 'h-9 px-4 py-2 has-[>svg]:px-3', 'xs' => "h-6 gap-1 rounded-md px-2 text-xs has-[>svg]:px-1.5 [&_svg:not([class*='size-'])]:size-3", 'sm' => 'h-8 rounded-md gap-1.5 px-3 has-[>svg]:px-2.5', 'lg' => 'h-10 rounded-md px-6 has-[>svg]:px-4', 'icon' => 'size-9', 'icon-xs' => "size-6 rounded-md [&_svg:not([class*='size-'])]:size-3", 'icon-sm' => 'size-8', 'icon-lg' => 'size-10'];
    $featurePath = $href && str_starts_with($href, '/capstone/') ? substr($href, strlen('/capstone')) : $href;
    $state = request()->attributes->get('capstone_feature_access', []);
    $lockedReason = $href && $state ? \Modules\Capstone\Support\BladeFeatureAccess::reason($featurePath, $state) : null;
?>
<?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($href): ?>
<?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($lockedReason): ?>
<a role="link" aria-disabled="true" tabindex="-1" title="<?php echo e($lockedReason); ?>" <?php echo e($attributes->except(['href', ':href', '@click'])->class([$base, $variants[$variant] ?? $variants['default'], $sizes[$size] ?? $sizes['default'], 'opacity-50 cursor-not-allowed'])); ?>><?php echo e($slot); ?></a>
<?php else: ?>
<a href="<?php echo e(str_starts_with($href, '/') && !str_starts_with($href, '/capstone') ? url('/capstone'.$href) : $href); ?>" <?php echo e($attributes->class([$base, $variants[$variant] ?? $variants['default'], $sizes[$size] ?? $sizes['default']])); ?>><?php echo e($slot); ?></a>
<?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
<?php else: ?>
<button <?php echo e($attributes->merge(['type' => 'button'])->class([$base, $variants[$variant] ?? $variants['default'], $sizes[$size] ?? $sizes['default']])); ?>><?php echo e($slot); ?></button>
<?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
<?php /**PATH C:\WebsiteTekkom - Copy\Modules/Capstone\resources/views/components/button.blade.php ENDPATH**/ ?>