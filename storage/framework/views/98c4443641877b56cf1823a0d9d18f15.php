<?php if (isset($component)) { $__componentOriginal5f8703e57583ba4eb55a214d5c40ce87 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal5f8703e57583ba4eb55a214d5c40ce87 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'manajemenmahasiswa::components.layouts.admin','data' => []] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('manajemenmahasiswa::layouts.admin'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

    <h1>Hello Admin</h1>
    
    <p>Module: <?php echo config('manajemenmahasiswa.name'); ?></p>
 <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal5f8703e57583ba4eb55a214d5c40ce87)): ?>
<?php $attributes = $__attributesOriginal5f8703e57583ba4eb55a214d5c40ce87; ?>
<?php unset($__attributesOriginal5f8703e57583ba4eb55a214d5c40ce87); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal5f8703e57583ba4eb55a214d5c40ce87)): ?>
<?php $component = $__componentOriginal5f8703e57583ba4eb55a214d5c40ce87; ?>
<?php unset($__componentOriginal5f8703e57583ba4eb55a214d5c40ce87); ?>
<?php endif; ?><?php /**PATH C:\WebsiteTekkom - Copy\Modules\ManajemenMahasiswa\resources\views\dashboard\admin.blade.php ENDPATH**/ ?>