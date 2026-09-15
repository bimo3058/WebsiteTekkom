
<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames(([
    'variant' => 'default',
    'size'    => 'sm',  // 'sm' (default) | 'xs' (compact untuk audit log, dll)
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
    'size'    => 'sm',  // 'sm' (default) | 'xs' (compact untuk audit log, dll)
]), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars, $__key, $__value); ?>

<?php
    $base = $size === 'xs'
        ? 'inline-flex items-center rounded-full px-2 py-0.5 text-[10px] font-semibold transition-colors'
        : 'inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-semibold transition-colors focus:outline-none focus:ring-2 focus:ring-ring focus:ring-offset-2';

    $variants = [
        'default'     => 'bg-primary text-primary-foreground hover:bg-primary/80',
        'secondary'   => 'bg-secondary text-secondary-foreground hover:bg-secondary/80',
        'destructive' => 'bg-destructive text-destructive-foreground hover:bg-destructive/80',
        'outline'     => 'text-foreground border border-border',
        'success'     => 'bg-success-50 text-success-300 border border-success-200/30',
        'warning'     => 'bg-warning-50 text-warning-300 border border-warning-200/30',
        'sky'         => 'bg-sky-50 text-sky-300 border border-sky-200/30',
        'purple'      => 'bg-primary-50 text-primary-400 border border-primary-200/30',

        // ── Role variants ──
        'role-superadmin' => 'bg-[#F1E9FF] text-[#5E53F4] border border-[#D1BFFF] uppercase tracking-wider',
        'role-dosen'      => 'bg-[#E7F9F3] text-[#00C08D] border border-[#B2EBD9] uppercase tracking-wider',
        'role-mahasiswa'  => 'bg-[#FFF9E6] text-[#FFB800] border border-[#FFEBB3] uppercase tracking-wider',
        'role-default'    => 'bg-[#F0F5FF] text-[#5E53F4] border border-[#D1DFFF] uppercase tracking-wider',

        // ── Module variants (audit log) ──
        'module-auth'       => 'bg-[#ECFDF5] text-[#059669] border border-[#A7F3D0]',
        'module-banksoal'   => 'bg-[#FFFBEB] text-[#D97706] border border-[#FDE68A]',
        'module-capstone'   => 'bg-[#EFF6FF] text-[#3B82F6] border border-[#BFDBFE]',
        'module-eoffice'    => 'bg-[rgba(11,38,110,0.06)] text-[#0B266E] border border-[rgba(11,38,110,0.18)]',
        'module-management' => 'bg-[rgba(11,38,110,0.06)] text-[#0B266E] border border-[rgba(11,38,110,0.18)]',
        'module-default'    => 'bg-[#F9FAFB] text-[#6B7280] border border-[#E5E7EB]',

        // ── Action variants (audit log) ──
        'action-create'  => 'bg-[#ECFDF5] text-[#059669] border border-[#A7F3D0]',
        'action-update'  => 'bg-[#FFFBEB] text-[#D97706] border border-[#FDE68A]',
        'action-delete'  => 'bg-[#FEF2F2] text-[#DC2626] border border-[#FECACA]',
        'action-login'   => 'bg-[rgba(11,38,110,0.06)] text-[#0B266E] border border-[rgba(11,38,110,0.18)]',
        'action-logout'  => 'bg-[#F9FAFB] text-[#6B7280] border border-[#E5E7EB]',
        'action-view'    => 'bg-[#EFF6FF] text-[#3B82F6] border border-[#BFDBFE]',
        'action-default' => 'bg-[#F9FAFB] text-[#6B7280] border border-[#E5E7EB]',
    ];

    $classes = $base . ' ' . ($variants[$variant] ?? $variants['default']);
?>

<span <?php echo e($attributes->merge(['class' => $classes])); ?>>
    <?php echo e($slot); ?>

</span><?php /**PATH C:\WebsiteTekkom - Copy\resources\views/components/ui/badge.blade.php ENDPATH**/ ?>