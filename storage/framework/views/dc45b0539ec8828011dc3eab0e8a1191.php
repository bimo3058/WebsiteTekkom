<?php if (isset($component)) { $__componentOriginal42d790b99f84288de5e768b36f962d79 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal42d790b99f84288de5e768b36f962d79 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'manajemenmahasiswa::components.layouts.master','data' => []] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('manajemenmahasiswa::layouts.master'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

    <h1>Hello World</h1>

    <p>Module: <?php echo config('manajemenmahasiswa.name'); ?></p>
 <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal42d790b99f84288de5e768b36f962d79)): ?>
<?php $attributes = $__attributesOriginal42d790b99f84288de5e768b36f962d79; ?>
<?php unset($__attributesOriginal42d790b99f84288de5e768b36f962d79); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal42d790b99f84288de5e768b36f962d79)): ?>
<?php $component = $__componentOriginal42d790b99f84288de5e768b36f962d79; ?>
<?php unset($__componentOriginal42d790b99f84288de5e768b36f962d79); ?>
<?php endif; ?>
<?php /**PATH C:\WebsiteTekkom - Copy\Modules\ManajemenMahasiswa\resources\views\index.blade.php ENDPATH**/ ?>