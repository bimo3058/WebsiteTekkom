<?php if (isset($component)) { $__componentOriginalbfe3dfc9f8c7e29aaca1eb4d19fe5039 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalbfe3dfc9f8c7e29aaca1eb4d19fe5039 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'banksoal::components.layouts.gpm-master','data' => []] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('banksoal::layouts.gpm-master'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

    <?php if (isset($component)) { $__componentOriginal4f4ab3c60e35d72431cf46a8394b8c9b = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal4f4ab3c60e35d72431cf46a8394b8c9b = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'banksoal::components.ui.page-header','data' => ['title' => 'Menu Riwayat Validasi','subtitle' => 'Pilih kategori riwayat dokumen yang ingin Anda pantau.']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('banksoal::ui.page-header'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => 'Menu Riwayat Validasi','subtitle' => 'Pilih kategori riwayat dokumen yang ingin Anda pantau.']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal4f4ab3c60e35d72431cf46a8394b8c9b)): ?>
<?php $attributes = $__attributesOriginal4f4ab3c60e35d72431cf46a8394b8c9b; ?>
<?php unset($__attributesOriginal4f4ab3c60e35d72431cf46a8394b8c9b); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal4f4ab3c60e35d72431cf46a8394b8c9b)): ?>
<?php $component = $__componentOriginal4f4ab3c60e35d72431cf46a8394b8c9b; ?>
<?php unset($__componentOriginal4f4ab3c60e35d72431cf46a8394b8c9b); ?>
<?php endif; ?>

    <div class="grid grid-cols-1 gap-6 md:grid-cols-2 mb-8">
        <div class="bg-white border border-slate-200 rounded-2xl p-6 text-center flex flex-col items-center hover:border-primary/20 hover:shadow-lg transition-all">
            <div class="mb-6 flex h-16 w-16 items-center justify-center rounded-full bg-primary/10 text-primary">
                <i class="far fa-file-alt text-2xl"></i>
            </div>
            <h3 class="text-lg font-semibold text-slate-900 mb-2">Riwayat Validasi RPS</h3>
            <p class="text-sm text-slate-600 mb-6">
                Pantau status, tanggal diajukan, dan hasil review dokumen Rencana Pembelajaran Semester (RPS) untuk setiap program studi.
            </p>
            <a href="<?php echo e(route('banksoal.rps.gpm.riwayat-validasi.rps')); ?>" class="mt-auto inline-flex items-center justify-center w-full rounded-xl bg-primary px-4 py-2.5 text-sm font-semibold text-white hover:bg-primary/90">
                Buka Riwayat RPS <i class="fas fa-arrow-right ml-2"></i>
            </a>
        </div>

        <div class="bg-white border border-slate-200 rounded-2xl p-6 text-center flex flex-col items-center hover:border-primary/20 hover:shadow-lg transition-all">
            <div class="mb-6 flex h-16 w-16 items-center justify-center rounded-full bg-primary/10 text-primary">
                <i class="far fa-question-circle text-2xl"></i>
            </div>
            <h3 class="text-lg font-semibold text-slate-900 mb-2">Riwayat Validasi Bank Soal</h3>
            <p class="text-sm text-slate-600 mb-6">
                Pantau status, jumlah butir soal, dan hasil review untuk paket Bank Soal mata kuliah pada tiap semester aktif.
            </p>
            <a href="<?php echo e(route('banksoal.soal.gpm.riwayat-validasi.bank-soal')); ?>" class="mt-auto inline-flex items-center justify-center w-full rounded-xl bg-primary px-4 py-2.5 text-sm font-semibold text-white hover:bg-primary/90">
                Buka Riwayat Bank Soal <i class="fas fa-arrow-right ml-2"></i>
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 gap-3 md:grid-cols-3">
        <?php if (isset($component)) { $__componentOriginal463b94b5039cdd6c42deca583fe6719d = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal463b94b5039cdd6c42deca583fe6719d = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'banksoal::components.ui.stat-card','data' => ['label' => 'Menunggu Review','value' => '12 Dokumen','icon' => 'fa-clipboard-check','tone' => 'blue']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('banksoal::ui.stat-card'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['label' => 'Menunggu Review','value' => '12 Dokumen','icon' => 'fa-clipboard-check','tone' => 'blue']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal463b94b5039cdd6c42deca583fe6719d)): ?>
<?php $attributes = $__attributesOriginal463b94b5039cdd6c42deca583fe6719d; ?>
<?php unset($__attributesOriginal463b94b5039cdd6c42deca583fe6719d); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal463b94b5039cdd6c42deca583fe6719d)): ?>
<?php $component = $__componentOriginal463b94b5039cdd6c42deca583fe6719d; ?>
<?php unset($__componentOriginal463b94b5039cdd6c42deca583fe6719d); ?>
<?php endif; ?>
        <?php if (isset($component)) { $__componentOriginal463b94b5039cdd6c42deca583fe6719d = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal463b94b5039cdd6c42deca583fe6719d = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'banksoal::components.ui.stat-card','data' => ['label' => 'Selesai Validasi','value' => '48 Dokumen','icon' => 'fa-check-circle','tone' => 'green']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('banksoal::ui.stat-card'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['label' => 'Selesai Validasi','value' => '48 Dokumen','icon' => 'fa-check-circle','tone' => 'green']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal463b94b5039cdd6c42deca583fe6719d)): ?>
<?php $attributes = $__attributesOriginal463b94b5039cdd6c42deca583fe6719d; ?>
<?php unset($__attributesOriginal463b94b5039cdd6c42deca583fe6719d); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal463b94b5039cdd6c42deca583fe6719d)): ?>
<?php $component = $__componentOriginal463b94b5039cdd6c42deca583fe6719d; ?>
<?php unset($__componentOriginal463b94b5039cdd6c42deca583fe6719d); ?>
<?php endif; ?>
        <?php if (isset($component)) { $__componentOriginal463b94b5039cdd6c42deca583fe6719d = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal463b94b5039cdd6c42deca583fe6719d = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'banksoal::components.ui.stat-card','data' => ['label' => 'Update Terakhir','value' => '2 Jam Lalu','icon' => 'fa-calendar','tone' => 'amber']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('banksoal::ui.stat-card'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['label' => 'Update Terakhir','value' => '2 Jam Lalu','icon' => 'fa-calendar','tone' => 'amber']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal463b94b5039cdd6c42deca583fe6719d)): ?>
<?php $attributes = $__attributesOriginal463b94b5039cdd6c42deca583fe6719d; ?>
<?php unset($__attributesOriginal463b94b5039cdd6c42deca583fe6719d); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal463b94b5039cdd6c42deca583fe6719d)): ?>
<?php $component = $__componentOriginal463b94b5039cdd6c42deca583fe6719d; ?>
<?php unset($__componentOriginal463b94b5039cdd6c42deca583fe6719d); ?>
<?php endif; ?>
    </div>
 <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalbfe3dfc9f8c7e29aaca1eb4d19fe5039)): ?>
<?php $attributes = $__attributesOriginalbfe3dfc9f8c7e29aaca1eb4d19fe5039; ?>
<?php unset($__attributesOriginalbfe3dfc9f8c7e29aaca1eb4d19fe5039); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalbfe3dfc9f8c7e29aaca1eb4d19fe5039)): ?>
<?php $component = $__componentOriginalbfe3dfc9f8c7e29aaca1eb4d19fe5039; ?>
<?php unset($__componentOriginalbfe3dfc9f8c7e29aaca1eb4d19fe5039); ?>
<?php endif; ?><?php /**PATH C:\WebsiteTekkom - Copy\Modules\BankSoal\resources\views\gpm\riwayat-validasi\index.blade.php ENDPATH**/ ?>