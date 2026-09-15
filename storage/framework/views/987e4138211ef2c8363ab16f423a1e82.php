<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames(([
    'label',
    'value' => 0,
    'icon' => 'fa-chart-bar',
    'tone' => 'blue',
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
    'label',
    'value' => 0,
    'icon' => 'fa-chart-bar',
    'tone' => 'blue',
]), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars, $__key, $__value); ?>

<?php
    $tones = [
        'blue'  => 'bg-primary/10 text-primary',
        'green' => 'bg-emerald-100 text-emerald-600',
        'amber' => 'bg-amber-100 text-amber-600',
        'red'   => 'bg-rose-100 text-rose-600',
        'slate' => 'bg-slate-100 text-slate-600',
    ];
    $toneKey     = is_string($tone) ? $tone : 'blue';
    $toneClasses = $tones[$toneKey] ?? $tones['blue'];
?>

<div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-3">
    <div class="flex items-center gap-2.5">
        <div class="w-8 h-8 rounded-lg flex items-center justify-center text-xs <?php echo e($toneClasses); ?> shrink-0">
            <i class="fas <?php echo e($icon); ?>"></i>
        </div>
        <div>
            <p class="text-lg font-bold text-slate-900 leading-none"><?php echo e($value); ?></p>
            <p class="text-[11px] text-slate-500 mt-0.5"><?php echo e($label); ?></p>
        </div>
    </div>
</div><?php /**PATH C:\WebsiteTekkom - Copy\Modules\BankSoal\resources\views\components\ui\stat-card.blade.php ENDPATH**/ ?>