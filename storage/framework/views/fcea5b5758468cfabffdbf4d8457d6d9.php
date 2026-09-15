<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames((['route' => '#', 'icon' => '', 'label' => '', 'routeName' => '']));

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

foreach (array_filter((['route' => '#', 'icon' => '', 'label' => '', 'routeName' => '']), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars, $__key, $__value); ?>

<?php
    $isActive = $routeName && request()->routeIs($routeName . '*');
?>

<a href="<?php echo e($route); ?>"
   class="nav-link-item <?php echo e($isActive ? 'active' : ''); ?>"
   :class="{ 'justify-content-center': !sidebarOpen }">
    <span class="nav-icon d-inline-flex"><?php echo $iconSlot ?? $icon; ?></span>
    <span class="nav-label" x-show="sidebarOpen" style="flex-grow:1;"><?php echo e($slot->isNotEmpty() ? $slot : $label); ?></span>
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(isset($badge)): ?>
        <span x-show="sidebarOpen"><?php echo $badge; ?></span>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
</a>
<?php /**PATH C:\WebsiteTekkom - Copy\Modules\ManajemenMahasiswa\resources\views\components\ui\sidebar-item.blade.php ENDPATH**/ ?>