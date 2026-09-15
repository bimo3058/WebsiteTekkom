
<?php
    $canView = auth()->user()?->can('banksoal.view');
    $canEdit = auth()->user()?->can('banksoal.edit');
    $canDelete = auth()->user()?->can('banksoal.delete');
    $actionCount = collect([$canView, $canEdit, $canDelete])->filter()->count();
?>

<?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($actionCount > 1): ?>
    
    <?php if (isset($component)) { $__componentOriginal92c331ec96a7db61eb849a0f13afbc4a = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal92c331ec96a7db61eb849a0f13afbc4a = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.action-menu','data' => ['align' => 'right']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.action-menu'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['align' => 'right']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

        <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('banksoal.view')): ?>
            <a href="<?php echo e(route('banksoal.soal.dosen.show', $soal->id)); ?>" class="flex items-center gap-3 px-4 py-2.5 text-sm text-gray-700 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-gray-700">
                <i class="fas fa-eye w-4 text-gray-500"></i>
                <span>Lihat Detail</span>
            </a>
        <?php endif; ?>

        <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('banksoal.edit')): ?>
            <a href="<?php echo e(route('banksoal.soal.dosen.edit', $soal->id)); ?>" class="flex items-center gap-3 px-4 py-2.5 text-sm text-gray-700 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-gray-700">
                <i class="fas fa-pen w-4 text-gray-500"></i>
                <span>Edit Soal</span>
            </a>
        <?php endif; ?>

        <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('banksoal.delete')): ?>
            <?php if (isset($component)) { $__componentOriginal5e6ec01e4d5569930b0a388c1c82197f = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal5e6ec01e4d5569930b0a388c1c82197f = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.dropdown-separator','data' => []] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.dropdown-separator'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal5e6ec01e4d5569930b0a388c1c82197f)): ?>
<?php $attributes = $__attributesOriginal5e6ec01e4d5569930b0a388c1c82197f; ?>
<?php unset($__attributesOriginal5e6ec01e4d5569930b0a388c1c82197f); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal5e6ec01e4d5569930b0a388c1c82197f)): ?>
<?php $component = $__componentOriginal5e6ec01e4d5569930b0a388c1c82197f; ?>
<?php unset($__componentOriginal5e6ec01e4d5569930b0a388c1c82197f); ?>
<?php endif; ?>
            <form action="<?php echo e(route('banksoal.destroy', $soal->id)); ?>" method="POST" class="m-0 block" onsubmit="return confirm('Yakin ingin menghapus soal ini?');">
                <?php echo csrf_field(); ?>
                <?php echo method_field('DELETE'); ?>
                <button type="submit" class="w-full flex items-center gap-3 px-4 py-2.5 text-sm text-red-600 hover:bg-red-50 dark:text-red-400 dark:hover:bg-red-900/20 border-0 cursor-pointer bg-transparent text-left">
                    <i class="fas fa-trash w-4"></i>
                    <span>Hapus Soal</span>
                </button>
            </form>
        <?php endif; ?>
     <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal92c331ec96a7db61eb849a0f13afbc4a)): ?>
<?php $attributes = $__attributesOriginal92c331ec96a7db61eb849a0f13afbc4a; ?>
<?php unset($__attributesOriginal92c331ec96a7db61eb849a0f13afbc4a); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal92c331ec96a7db61eb849a0f13afbc4a)): ?>
<?php $component = $__componentOriginal92c331ec96a7db61eb849a0f13afbc4a; ?>
<?php unset($__componentOriginal92c331ec96a7db61eb849a0f13afbc4a); ?>
<?php endif; ?>
<?php else: ?>
    
    <div class="inline-block">
        <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('banksoal.view')): ?>
            <a href="<?php echo e(route('banksoal.soal.dosen.show', $soal->id)); ?>" class="inline-flex items-center justify-center w-8 h-8 text-slate-600 hover:bg-slate-100 hover:text-primary rounded-lg transition-colors" title="Lihat Detail">
                <i class="fas fa-eye text-sm"></i>
            </a>
        <?php endif; ?>

        <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('banksoal.edit')): ?>
            <a href="<?php echo e(route('banksoal.soal.dosen.edit', $soal->id)); ?>" class="inline-flex items-center justify-center w-8 h-8 text-slate-600 hover:bg-slate-100 hover:text-primary rounded-lg transition-colors" title="Edit Soal">
                <i class="fas fa-pen text-sm"></i>
            </a>
        <?php endif; ?>

        <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('banksoal.delete')): ?>
            <form action="<?php echo e(route('banksoal.destroy', $soal->id)); ?>" method="POST" class="m-0 inline-block" onsubmit="return confirm('Yakin ingin menghapus soal ini?');">
                <?php echo csrf_field(); ?>
                <?php echo method_field('DELETE'); ?>
                <button type="submit" class="inline-flex items-center justify-center w-8 h-8 text-slate-600 hover:bg-slate-100 hover:text-red-600 rounded-lg transition-colors border-0 cursor-pointer bg-transparent" title="Hapus Soal">
                    <i class="fas fa-trash text-sm"></i>
                </button>
            </form>
        <?php endif; ?>
    </div>
<?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
<?php /**PATH C:\WebsiteTekkom - Copy\Modules\BankSoal\resources\views\partials\dosen\soal-actions.blade.php ENDPATH**/ ?>