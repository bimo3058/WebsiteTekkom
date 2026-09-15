<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames((['icon', 'label', 'route' => '#', 'active' => false, 'color' => 'text-white']));

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

foreach (array_filter((['icon', 'label', 'route' => '#', 'active' => false, 'color' => 'text-white']), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars, $__key, $__value); ?>

<a href="<?php echo e($route); ?>" 
   <?php echo e($attributes->merge(['class' => 'flex items-center px-2 py-2 rounded-lg transition-colors ' . ($active ? 'bg-slate-700/50 text-white' : 'text-slate-300 hover:text-white hover:bg-slate-700/30')])); ?>

   :class="!sidebarOpen ? 'justify-center' : ''">
    <div class="flex items-center" :class="!sidebarOpen ? 'justify-center w-full' : 'space-x-3'">
        <svg class="w-5 h-5 <?php echo e($color); ?>" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="<?php echo e($icon); ?>"/>
        </svg>
        <span x-show="sidebarOpen" class="text-sm font-medium whitespace-nowrap"><?php echo e($label); ?></span>
    </div>
</a><?php /**PATH C:\WebsiteTekkom - Copy\resources\views\components\nav-link-custom.blade.php ENDPATH**/ ?>