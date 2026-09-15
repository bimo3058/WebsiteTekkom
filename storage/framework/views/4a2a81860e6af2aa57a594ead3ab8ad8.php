
<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames(([
    'user'      => null,   // User model instance atau null
    'size'      => 'sm',   // 'xs'=28px, 'sm'=32px, 'md'=36px, 'lg'=40px
    'onlineDot' => false,  // tampilkan dot hijau online
    'suspended' => false,  // mode suspended: grayscale
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
    'user'      => null,   // User model instance atau null
    'size'      => 'sm',   // 'xs'=28px, 'sm'=32px, 'md'=36px, 'lg'=40px
    'onlineDot' => false,  // tampilkan dot hijau online
    'suspended' => false,  // mode suspended: grayscale
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
        'xs' => ['px' => 28, 'font' => '10px', 'icon' => 12, 'dot' => '7px',  'border' => '1.5px'],
        'sm' => ['px' => 32, 'font' => '11px', 'icon' => 14, 'dot' => '8px',  'border' => '1.5px'],
        'md' => ['px' => 36, 'font' => '12px', 'icon' => 14, 'dot' => '9px',  'border' => '1.5px'],
        'lg' => ['px' => 40, 'font' => '13px', 'icon' => 16, 'dot' => '10px', 'border' => '2px'],
        'xl' => ['px' => 84, 'font' => '28px', 'icon' => 32, 'dot' => '12px', 'border' => '1.5px'],
    ];
    $s = $sizes[$size] ?? $sizes['sm'];

    // Initials dari nama user
    $initials = '';
    if ($user) {
        $parts    = explode(' ', trim($user->name));
        $initials = strtoupper(substr($parts[0], 0, 1));
        if (count($parts) > 1) $initials .= strtoupper(substr(end($parts), 0, 1));
    }

    // Foto profil atau inisial dengan warna netral yang sama untuk semua pengguna.
    $wrapStyle = 'background:#F3F4F6; border:' . $s['border'] . ' solid #E5E7EB;';
    $textColor = 'color:#6B7280;';
    $filterStyle = $suspended ? 'filter:grayscale(0.5); opacity:0.7;' : '';

    $showDot = $onlineDot && ($user?->is_online ?? false) && !$suspended;
?>

<div style="position:relative; flex-shrink:0;">
    <div style="width:<?php echo e($s['px']); ?>px; height:<?php echo e($s['px']); ?>px; border-radius:50%; overflow:hidden; display:flex; align-items:center; justify-content:center; font-size:<?php echo e($s['font']); ?>; font-weight:700; <?php echo e($wrapStyle); ?> <?php echo e($filterStyle); ?>">
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($user?->avatar_url): ?>
            <img src="<?php echo e($user->avatar_url); ?>" alt="avatar" loading="lazy" decoding="async"
                 style="width:100%; height:100%; object-fit:cover;">
        <?php elseif($user): ?>
            <span style="<?php echo e($textColor); ?>"><?php echo e($initials ?: '?'); ?></span>
        <?php else: ?>
            
            <svg width="<?php echo e($s['icon']); ?>" height="<?php echo e($s['icon']); ?>" fill="none" stroke="currentColor"
                 viewBox="0 0 24 24" stroke-width="1.8" style="<?php echo e($textColor); ?>">
                <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/>
            </svg>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    </div>
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($showDot): ?>
        <span style="position:absolute; bottom:-1px; right:-1px; width:<?php echo e($s['dot']); ?>; height:<?php echo e($s['dot']); ?>; border-radius:50%; background:#22C55E; border:<?php echo e($s['border']); ?> solid #fff;"></span>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
</div>
<?php /**PATH C:\WebsiteTekkom - Copy\resources\views/components/ui/user-avatar.blade.php ENDPATH**/ ?>