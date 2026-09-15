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

    <div class="flex justify-between items-center">
        <h1>Hello Pengurus</h1>
        <div class="flex items-center gap-3">
            
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(auth()->user()->hasRole('mahasiswa')): ?>
                <form method="POST" action="<?php echo e(route('manajemenmahasiswa.switch.mode')); ?>">
                    <?php echo csrf_field(); ?>
                    <input type="hidden" name="mode" value="mahasiswa">
                    <button type="submit" 
                        class="text-sm font-medium text-blue-600 hover:text-blue-800 border border-blue-200 hover:border-blue-400 px-3 py-1.5 rounded-lg transition-all">
                        Lihat sebagai Mahasiswa
                    </button>
                </form>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            <form method="POST" action="<?php echo e(route('logout')); ?>">
                <?php echo csrf_field(); ?>
                <button type="submit" class="text-red-600 hover:text-red-800 font-medium">
                    <?php echo e(__('Log Out')); ?>

                </button>
            </form>
        </div>
    </div>

    <p>Module: <?php echo config('manajemenmahasiswa.name'); ?></p>

    
    <div style="margin-top: 20px; padding: 16px; background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px;">
        <p style="font-size: 12px; font-weight: 700; color: #64748b; text-transform: uppercase; margin-bottom: 8px;">Permission Status</p>
        <div style="display: flex; gap: 8px; flex-wrap: wrap;">
            <span style="font-size: 11px; padding: 4px 10px; border-radius: 20px; font-weight: 600;
                <?php echo e(auth()->user()->can('kemahasiswaan.view') ? 'background:#dcfce7;color:#166534;border:1px solid #bbf7d0;' : 'background:#fef2f2;color:#991b1b;border:1px solid #fecaca;'); ?>">
                <i class="fas fa-<?php echo e(auth()->user()->can('kemahasiswaan.view') ? 'check' : 'times'); ?>" style="margin-right:3px;"></i> View
            </span>
            <span style="font-size: 11px; padding: 4px 10px; border-radius: 20px; font-weight: 600;
                <?php echo e(auth()->user()->can('kemahasiswaan.edit') ? 'background:#dcfce7;color:#166534;border:1px solid #bbf7d0;' : 'background:#fef2f2;color:#991b1b;border:1px solid #fecaca;'); ?>">
                <i class="fas fa-<?php echo e(auth()->user()->can('kemahasiswaan.edit') ? 'check' : 'times'); ?>" style="margin-right:3px;"></i> Edit
            </span>
            <span style="font-size: 11px; padding: 4px 10px; border-radius: 20px; font-weight: 600;
                <?php echo e(auth()->user()->can('kemahasiswaan.delete') ? 'background:#dcfce7;color:#166534;border:1px solid #bbf7d0;' : 'background:#fef2f2;color:#991b1b;border:1px solid #fecaca;'); ?>">
                <i class="fas fa-<?php echo e(auth()->user()->can('kemahasiswaan.delete') ? 'check' : 'times'); ?>" style="margin-right:3px;"></i> Delete
            </span>
        </div>
    </div>
 <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal42d790b99f84288de5e768b36f962d79)): ?>
<?php $attributes = $__attributesOriginal42d790b99f84288de5e768b36f962d79; ?>
<?php unset($__attributesOriginal42d790b99f84288de5e768b36f962d79); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal42d790b99f84288de5e768b36f962d79)): ?>
<?php $component = $__componentOriginal42d790b99f84288de5e768b36f962d79; ?>
<?php unset($__componentOriginal42d790b99f84288de5e768b36f962d79); ?>
<?php endif; ?><?php /**PATH C:\WebsiteTekkom - Copy\Modules\ManajemenMahasiswa\resources\views\dashboard\pengurus.blade.php ENDPATH**/ ?>