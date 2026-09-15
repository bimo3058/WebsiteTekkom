<?php if (isset($component)) { $__componentOriginal315efe54efa54bd7b6313284b579a4ac = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal315efe54efa54bd7b6313284b579a4ac = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'banksoal::components.layouts.dosen-admin','data' => []] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('banksoal::layouts.dosen-admin'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

    <?php $__env->startSection('breadcrumbs'); ?>
        <a href="<?php echo e(route('banksoal.arsip.dosen.index')); ?>" class="text-slate-500 hover:text-primary transition-colors">Arsip Soal</a>
        <span class="mx-2 text-slate-300">/</span>
        <span class="text-slate-800 font-semibold">Konfirmasi Arsipkan</span>
    <?php $__env->stopSection(); ?>

<!-- Modal Konfirmasi Arsipkan -->
<div class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 backdrop-blur-sm">
    <div class="bg-white rounded-2xl shadow-xl max-w-md w-full mx-4 p-8">
        <!-- Icon -->
        <div class="flex justify-center mb-4">
            <div class="w-16 h-16 bg-emerald-100 rounded-full flex items-center justify-center">
                <i class="fas fa-archive text-emerald-600 text-2xl"></i>
            </div>
        </div>

        <!-- Title -->
        <h2 class="text-center text-xl font-bold text-slate-900 mb-2">Arsipkan Penarikan?</h2>

        <!-- Message -->
        <p class="text-center text-slate-600 text-sm mb-6">
            Anda akan memindahkan <strong><?php echo e($penarikan->nama_ekstraksi); ?></strong> ke arsip final. Tindakan ini tidak dapat dibatalkan.
        </p>

        <!-- Form & Actions -->
        <form action="<?php echo e(route('banksoal.arsip.dosen.penarikan.update', $penarikan->id)); ?>" method="POST" class="flex items-center gap-3" onsubmit="if(this.checkValidity()){ window.showLoader(); return true; }">
            <?php echo csrf_field(); ?>
            <?php echo method_field('PUT'); ?>
            
            <!-- Hidden fields with default values -->
            <input type="hidden" name="nama_arsip" value="<?php echo e($penarikan->nama_ekstraksi); ?>">
            <input type="hidden" name="deskripsi" value="<?php echo e($penarikan->deskripsi ?? ''); ?>">
            <input type="hidden" name="catatan_internal" value="<?php echo e($penarikan->catatan_internal ?? ''); ?>">
            <input type="hidden" name="catatan_konversi" value="">
            
            <a href="<?php echo e(route('banksoal.arsip.dosen.index')); ?>" class="flex-1 rounded-lg border border-slate-300 px-4 py-2.5 text-center text-sm font-semibold text-slate-700 hover:bg-slate-50 transition-colors">
                Batal
            </a>
            <button type="submit" class="flex-1 rounded-lg bg-emerald-600 px-4 py-2.5 text-sm font-semibold text-white hover:bg-emerald-700 transition-colors">
                Arsipkan
            </button>
        </form>
    </div>
</div>

 <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal315efe54efa54bd7b6313284b579a4ac)): ?>
<?php $attributes = $__attributesOriginal315efe54efa54bd7b6313284b579a4ac; ?>
<?php unset($__attributesOriginal315efe54efa54bd7b6313284b579a4ac); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal315efe54efa54bd7b6313284b579a4ac)): ?>
<?php $component = $__componentOriginal315efe54efa54bd7b6313284b579a4ac; ?>
<?php unset($__componentOriginal315efe54efa54bd7b6313284b579a4ac); ?>
<?php endif; ?><?php /**PATH C:\WebsiteTekkom - Copy\Modules\BankSoal\resources\views\pages\arsip\Dosen\convert.blade.php ENDPATH**/ ?>