



<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames(([
    'align' => 'right',
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
    'align' => 'right',
]), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars, $__key, $__value); ?>

<?php
    $alignClasses = match($align) {
        'left'   => 'left-0 origin-top-left',
        'right'  => 'right-0 origin-top-right',
        'center' => 'left-1/2 -translate-x-1/2 origin-top',
        default  => 'right-0 origin-top-right',
    };
?>

<div class="inline-block" x-data="{ open: false }" @click.outside="open = false" @close.stop="open = false">
    
    <button
        @click="open = !open"
        type="button"
        class="inline-flex items-center justify-center p-2 text-gray-500 rounded-md hover:text-primary hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors"
        title="More options"
    >
        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
            <path d="M3 10a1.5 1.5 0 113 0 1.5 1.5 0 01-3 0zM8.5 10a1.5 1.5 0 113 0 1.5 1.5 0 01-3 0zM14 10a1.5 1.5 0 113 0 1.5 1.5 0 01-3 0z" />
        </svg>
    </button>

    
    <div x-show="open"
         x-transition:enter="transition ease-out duration-150"
         x-transition:enter-start="opacity-0 scale-95"
         x-transition:enter-end="opacity-100 scale-100"
         x-transition:leave="transition ease-in duration-100"
         x-transition:leave-start="opacity-100 scale-100"
         x-transition:leave-end="opacity-0 scale-95"
         class="absolute z-50 mt-1 w-48 <?php echo e($alignClasses); ?> rounded-lg border border-border bg-popover shadow-lg"
         style="display: none;">
        <div class="py-1">
            <?php echo e($slot); ?>

        </div>
    </div>
</div>
<?php /**PATH C:\WebsiteTekkom - Copy\resources\views\components\ui\action-menu.blade.php ENDPATH**/ ?>