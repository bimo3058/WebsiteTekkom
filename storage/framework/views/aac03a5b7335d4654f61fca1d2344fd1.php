<?php if (isset($component)) { $__componentOriginal751c60d6b55ecec60fe692173fafc657 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal751c60d6b55ecec60fe692173fafc657 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'capstone::components.feature-link','data' => ['href' => '/mahasiswa/group']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('capstone::feature-link'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['href' => '/mahasiswa/group']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>
Open <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal751c60d6b55ecec60fe692173fafc657)): ?>
<?php $attributes = $__attributesOriginal751c60d6b55ecec60fe692173fafc657; ?>
<?php unset($__attributesOriginal751c60d6b55ecec60fe692173fafc657); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal751c60d6b55ecec60fe692173fafc657)): ?>
<?php $component = $__componentOriginal751c60d6b55ecec60fe692173fafc657; ?>
<?php unset($__componentOriginal751c60d6b55ecec60fe692173fafc657); ?>
<?php endif; ?><?php /**PATH C:\WebsiteTekkom - Copy\storage\framework\views/2ccda9736b319c4a4d9dc7385eb7ecc3.blade.php ENDPATH**/ ?>