<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames(([
    'currentPage' => 1,
    'totalPages'  => 1,
    'jsCallback'  => 'goToPage',
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
    'currentPage' => 1,
    'totalPages'  => 1,
    'jsCallback'  => 'goToPage',
]), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars, $__key, $__value); ?>

<?php
    $currentPage = (int) $currentPage;
    $totalPages  = (int) $totalPages;

    // Tidak render apapun jika hanya 1 halaman atau kurang
    if ($totalPages <= 1) {
        return;
    }

    if ($totalPages < 10) {
        // Tampilkan maks 5 halaman dengan currentPage sebagai anchor tengah
        $start = max(1, $currentPage - 2);
        $end   = min($totalPages, $start + 4);
        $start = max(1, $end - 4); // koreksi jika end terlalu kecil
        $pages = range($start, $end);
    } else {
        // 3 halaman pertama + ellipsis + 2 halaman terakhir
        $pages = [1, 2, 3, '...', $totalPages - 1, $totalPages];
    }
?>

<div class="pagination-list">
    
    <button
        type="button"
        class="pagination-btn"
        onclick="<?php echo e($jsCallback); ?>(<?php echo e(max(1, $currentPage - 1)); ?>)"
        <?php echo e($currentPage === 1 ? 'disabled' : ''); ?>

    >&lsaquo;</button>

    
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $pages; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $page): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($page === '...'): ?>
            <span class="pagination-ellipsis">...</span>
        <?php else: ?>
            <button
                type="button"
                class="pagination-btn <?php echo e((int) $page === $currentPage ? 'active' : ''); ?>"
                onclick="<?php echo e($jsCallback); ?>(<?php echo e($page); ?>)"
            ><?php echo e($page); ?></button>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>

    
    <button
        type="button"
        class="pagination-btn"
        onclick="<?php echo e($jsCallback); ?>(<?php echo e(min($totalPages, $currentPage + 1)); ?>)"
        <?php echo e($currentPage === $totalPages ? 'disabled' : ''); ?>

    >&rsaquo;</button>
</div>
<?php /**PATH C:\WebsiteTekkom - Copy\Modules\BankSoal\resources\views\components\ui\pagination.blade.php ENDPATH**/ ?>