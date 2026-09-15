
<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames(([
    'status' => 'active', // 'active' | 'suspended' | 'online' | 'offline' | 'system'
    'pulse'  => false,    // animasi pulse untuk dot (khusus online)
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
    'status' => 'active', // 'active' | 'suspended' | 'online' | 'offline' | 'system'
    'pulse'  => false,    // animasi pulse untuk dot (khusus online)
]), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars, $__key, $__value); ?>

<?php
    $cfg = match($status) {
        'active'    => ['color' => '#059669', 'border' => '#6EE7B7', 'dot' => '#22C55E',  'label' => 'Active',   'pulse' => false],
        'suspended' => ['color' => '#DC2626', 'border' => '#FECACA', 'dot' => '#DC2626',  'label' => 'Suspend',  'pulse' => false],
        'online'    => ['color' => '#059669', 'border' => '#A7F3D0', 'dot' => '#22C55E',  'label' => 'Online',   'pulse' => true],
        'offline'   => ['color' => '#6B7280', 'border' => '#E5E7EB', 'dot' => '#9CA3AF',  'label' => 'Offline',  'pulse' => false],
        'nonaktif'  => ['color' => '#6B7280', 'border' => '#E5E7EB', 'dot' => '#9CA3AF',  'label' => 'Nonaktif', 'pulse' => false],
        'system'    => ['color' => '#6B7280', 'border' => '#E5E7EB', 'dot' => null,        'label' => 'System',   'pulse' => false],
        default     => ['color' => '#6B7280', 'border' => '#E5E7EB', 'dot' => '#9CA3AF',  'label' => ucfirst($status), 'pulse' => false],
    };

    $doPulse = $pulse || $cfg['pulse'];
?>

<span style="display:inline-flex; align-items:center; gap:5px; font-size:11px; font-weight:500; color:<?php echo e($cfg['color']); ?>; border:1px solid <?php echo e($cfg['border']); ?>; padding:3px 12px; border-radius:9999px; white-space:nowrap;">
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($cfg['dot']): ?>
        <span style="width:6px; height:6px; border-radius:50%; background:<?php echo e($cfg['dot']); ?>; flex-shrink:0;<?php echo e($doPulse ? ' animation:pulse-dot 2s infinite;' : ''); ?>"></span>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    <?php echo e($cfg['label']); ?>

</span>
<?php /**PATH C:\WebsiteTekkom - Copy\resources\views\components\ui\status-badge.blade.php ENDPATH**/ ?>