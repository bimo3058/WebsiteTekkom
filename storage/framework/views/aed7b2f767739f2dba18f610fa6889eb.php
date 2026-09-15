<?php if (isset($component)) { $__componentOriginal34cff0fbce2cc3615503b4e5e9a314c8 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal34cff0fbce2cc3615503b4e5e9a314c8 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'eoffice::components.layouts.master','data' => []] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('eoffice::layouts.master'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

    <h1>Hello World</h1>

    <p>Module: <?php echo config('eoffice.name'); ?></p>
 <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal34cff0fbce2cc3615503b4e5e9a314c8)): ?>
<?php $attributes = $__attributesOriginal34cff0fbce2cc3615503b4e5e9a314c8; ?>
<?php unset($__attributesOriginal34cff0fbce2cc3615503b4e5e9a314c8); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal34cff0fbce2cc3615503b4e5e9a314c8)): ?>
<?php $component = $__componentOriginal34cff0fbce2cc3615503b4e5e9a314c8; ?>
<?php unset($__componentOriginal34cff0fbce2cc3615503b4e5e9a314c8); ?>
<?php endif; ?>
<?php /**PATH C:\WebsiteTekkom - Copy\Modules\EOffice\resources\views\index.blade.php ENDPATH**/ ?>