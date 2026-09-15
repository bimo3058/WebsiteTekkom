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

    <div class="flex justify-between items-center">
        <h1>Hello Mahasiswa</h1>
        <form method="POST" action="<?php echo e(route('logout')); ?>">
            <?php echo csrf_field(); ?>
            <button type="submit" class="text-red-600 hover:text-red-800 font-medium"><?php echo e(__('Log Out')); ?></button>
        </form>
    </div>

    <p>Module: <?php echo config('capstone.name'); ?></p>

    
    <div style="margin-top: 20px; padding: 16px; background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px;">
        <p style="font-size: 12px; font-weight: 700; color: #64748b; text-transform: uppercase; margin-bottom: 8px;">Permission Status</p>
        <div style="display: flex; gap: 8px; flex-wrap: wrap;">
            <span style="font-size: 11px; padding: 4px 10px; border-radius: 20px; font-weight: 600;
                <?php echo e(auth()->user()->can('capstone.view') ? 'background:#dcfce7;color:#166534;border:1px solid #bbf7d0;' : 'background:#fef2f2;color:#991b1b;border:1px solid #fecaca;'); ?>">
                <i class="fas fa-<?php echo e(auth()->user()->can('capstone.view') ? 'check' : 'times'); ?>" style="margin-right:3px;"></i> View
            </span>
            <span style="font-size: 11px; padding: 4px 10px; border-radius: 20px; font-weight: 600;
                <?php echo e(auth()->user()->can('capstone.edit') ? 'background:#dcfce7;color:#166534;border:1px solid #bbf7d0;' : 'background:#fef2f2;color:#991b1b;border:1px solid #fecaca;'); ?>">
                <i class="fas fa-<?php echo e(auth()->user()->can('capstone.edit') ? 'check' : 'times'); ?>" style="margin-right:3px;"></i> Edit
            </span>
            <span style="font-size: 11px; padding: 4px 10px; border-radius: 20px; font-weight: 600;
                <?php echo e(auth()->user()->can('capstone.delete') ? 'background:#dcfce7;color:#166534;border:1px solid #bbf7d0;' : 'background:#fef2f2;color:#991b1b;border:1px solid #fecaca;'); ?>">
                <i class="fas fa-<?php echo e(auth()->user()->can('capstone.delete') ? 'check' : 'times'); ?>" style="margin-right:3px;"></i> Delete
            </span>
        </div>
    </div>
 <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalef5f07d0883f2a2fe2325bdf3ef90a49)): ?>
<?php $attributes = $__attributesOriginalef5f07d0883f2a2fe2325bdf3ef90a49; ?>
<?php unset($__attributesOriginalef5f07d0883f2a2fe2325bdf3ef90a49); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalef5f07d0883f2a2fe2325bdf3ef90a49)): ?>
<?php $component = $__componentOriginalef5f07d0883f2a2fe2325bdf3ef90a49; ?>
<?php unset($__componentOriginalef5f07d0883f2a2fe2325bdf3ef90a49); ?>
<?php endif; ?><?php /**PATH C:\WebsiteTekkom - Copy\Modules\Capstone\resources\views\dashboard\mahasiswa.blade.php ENDPATH**/ ?>