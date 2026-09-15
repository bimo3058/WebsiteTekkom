<?php if (isset($component)) { $__componentOriginalef5f07d0883f2a2fe2325bdf3ef90a49 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalef5f07d0883f2a2fe2325bdf3ef90a49 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'capstone::components.layouts.master','data' => []] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('capstone::layouts.master'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

    <h1>Hello World</h1>

    <p>Module: <?php echo config('capstone.name'); ?></p>
 <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalef5f07d0883f2a2fe2325bdf3ef90a49)): ?>
<?php $attributes = $__attributesOriginalef5f07d0883f2a2fe2325bdf3ef90a49; ?>
<?php unset($__attributesOriginalef5f07d0883f2a2fe2325bdf3ef90a49); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalef5f07d0883f2a2fe2325bdf3ef90a49)): ?>
<?php $component = $__componentOriginalef5f07d0883f2a2fe2325bdf3ef90a49; ?>
<?php unset($__componentOriginalef5f07d0883f2a2fe2325bdf3ef90a49); ?>
<?php endif; ?>
<?php /**PATH C:\WebsiteTekkom - Copy\Modules\Capstone\resources\views\index.blade.php ENDPATH**/ ?>