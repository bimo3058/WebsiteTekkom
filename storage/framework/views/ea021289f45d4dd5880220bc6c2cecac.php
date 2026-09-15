<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames(([
    'variant' => 'default',
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
    'variant' => 'default',
]), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars, $__key, $__value); ?>

<?php
    $baseClass = 'inline-flex items-center rounded-full border px-2.5 py-0.5 text-xs font-semibold transition-colors';
    
    $variants = [
        'default' => 'border-transparent bg-primary text-white',
        'secondary' => 'border-transparent bg-slate-100 text-slate-700',
        'destructive' => 'border-transparent bg-red-100 text-red-700',
        'outline' => 'border border-slate-200 text-slate-700 bg-white',
        
        // Role Colors
        'mahasiswa' => 'border-transparent bg-amber-100 text-amber-700',
        'dosen' => 'border-transparent bg-green-100 text-green-700',
        'admin' => 'border-transparent bg-red-100 text-red-700',
        'super-admin' => 'border-transparent bg-purple-100 text-purple-700',
        'gpm' => 'border-transparent bg-purple-100 text-purple-700',
        'alumni' => 'border-transparent bg-amber-100 text-amber-700',
        
        // Status Colors
        'active' => 'border-transparent bg-green-100 text-green-700',
        'inactive' => 'border-transparent bg-slate-100 text-slate-700',
        'suspend' => 'border-transparent bg-red-100 text-red-700',
        'pending' => 'border-transparent bg-amber-100 text-amber-700',
        
        // Semantic Colors
        'success' => 'border-transparent bg-green-100 text-green-700',
        'warning' => 'border-transparent bg-amber-100 text-amber-700',
        'info' => 'border-transparent bg-blue-100 text-blue-700',
        'error' => 'border-transparent bg-red-100 text-red-700',
        
            // RPS Validation Status
            'rps-pending' => 'border-amber-200 bg-amber-50 text-amber-700',
            'rps-revision' => 'border-red-200 bg-red-50 text-red-700',
            'rps-approved' => 'border-emerald-200 bg-emerald-50 text-emerald-700',
    ];

    $classes = $baseClass . ' ' . ($variants[$variant] ?? $variants['default']);
?>

<span <?php echo e($attributes->merge(['class' => $classes])); ?>>
    <?php echo e($slot); ?>

</span>
<?php /**PATH C:\WebsiteTekkom - Copy\Modules\BankSoal\resources\views\components\ui\badge.blade.php ENDPATH**/ ?>