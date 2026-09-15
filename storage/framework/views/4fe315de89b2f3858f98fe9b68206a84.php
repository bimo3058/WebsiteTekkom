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
        <span class="text-slate-800 font-semibold">Detail Arsip</span>
    <?php $__env->stopSection(); ?>

<?php if (isset($component)) { $__componentOriginal4f4ab3c60e35d72431cf46a8394b8c9b = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal4f4ab3c60e35d72431cf46a8394b8c9b = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'banksoal::components.ui.page-header','data' => ['title' => ''.e($mode === 'arsip' ? 'Detail Arsip Soal' : 'Detail Riwayat Penarikan').'','subtitle' => ''.e($record->nama_arsip ?? $record->nama_ekstraksi).'']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('banksoal::ui.page-header'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => ''.e($mode === 'arsip' ? 'Detail Arsip Soal' : 'Detail Riwayat Penarikan').'','subtitle' => ''.e($record->nama_arsip ?? $record->nama_ekstraksi).'']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

     <?php $__env->slot('actions', null, []); ?> 
        <a href="<?php echo e(route('banksoal.arsip.dosen.index')); ?>" class="inline-flex items-center gap-2 bg-white border border-slate-200 hover:bg-slate-50 rounded-xl px-4 py-2.5 font-medium text-slate-700 transition-colors">
            <i class="fas fa-arrow-left w-4"></i> Kembali
        </a>
     <?php $__env->endSlot(); ?>
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

<?php if (isset($component)) { $__componentOriginaled3368a8117ee5d527ad55a40e435525 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginaled3368a8117ee5d527ad55a40e435525 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'banksoal::components.ui.panel','data' => ['title' => 'Informasi Umum','padding' => 'p-6']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('banksoal::ui.panel'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => 'Informasi Umum','padding' => 'p-6']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm text-slate-700">
        <div><span class="font-semibold">Mata Kuliah:</span> <?php echo e($record->mataKuliah->nama ?? '-'); ?></div>
        <div><span class="font-semibold">Dosen:</span> <?php echo e(auth()->user()->name ?? '-'); ?></div>
        <div><span class="font-semibold">Tahun Akademik:</span> <?php echo e($record->tahun_akademik ?? '-'); ?></div>
        <div><span class="font-semibold">Semester:</span> <?php echo e($record->semester ?? '-'); ?></div>
        <div><span class="font-semibold">Tipe Ujian:</span> <?php echo e(strtoupper($record->tipe_ujian ?? '-')); ?></div>
        <div>
            <span class="font-semibold">Metode Ujian:</span> 
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(($record->metode_ujian ?? '') === 'offline'): ?>
                <span class="inline-flex items-center rounded-md bg-amber-50 px-2 py-0.5 text-xs font-medium text-amber-700 ring-1 ring-inset ring-amber-600/20">Cetak Kertas (Offline)</span>
            <?php else: ?>
                <span class="inline-flex items-center rounded-md bg-green-50 px-2 py-0.5 text-xs font-medium text-green-700 ring-1 ring-inset ring-green-600/20">Online</span>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </div>
        <div><span class="font-semibold">Status:</span> <?php echo e(strtoupper($record->status ?? '-')); ?></div>
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(($record->metode_ujian ?? '') === 'offline' && $record->status_cetak): ?>
        <div>
            <span class="font-semibold">Status Cetak:</span> 
            <span class="inline-flex items-center rounded-md bg-blue-50 px-2 py-0.5 text-xs font-medium text-blue-700 ring-1 ring-inset ring-blue-600/20"><?php echo e(ucfirst($record->status_cetak)); ?></span>
        </div>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    </div>
 <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginaled3368a8117ee5d527ad55a40e435525)): ?>
<?php $attributes = $__attributesOriginaled3368a8117ee5d527ad55a40e435525; ?>
<?php unset($__attributesOriginaled3368a8117ee5d527ad55a40e435525); ?>
<?php endif; ?>
<?php if (isset($__componentOriginaled3368a8117ee5d527ad55a40e435525)): ?>
<?php $component = $__componentOriginaled3368a8117ee5d527ad55a40e435525; ?>
<?php unset($__componentOriginaled3368a8117ee5d527ad55a40e435525); ?>
<?php endif; ?>

<?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(!empty($pdfUrl)): ?>
<div class="mt-6">
    <?php if (isset($component)) { $__componentOriginaled3368a8117ee5d527ad55a40e435525 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginaled3368a8117ee5d527ad55a40e435525 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'banksoal::components.ui.panel','data' => ['title' => 'File PDF Soal Asli','padding' => 'p-0 overflow-hidden']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('banksoal::ui.panel'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => 'File PDF Soal Asli','padding' => 'p-0 overflow-hidden']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

        <div class="p-4 bg-slate-50 border-b border-slate-100 flex items-center justify-between">
            <span class="text-sm font-bold text-slate-700 flex items-center gap-2">
                <i class="fas fa-file-pdf text-rose-500 text-lg"></i>
                Dokumen Soal.pdf
            </span>
            <a href="<?php echo e($pdfUrl); ?>" target="_blank" class="inline-flex items-center gap-2 rounded-lg border border-slate-200 bg-white px-3 py-1.5 text-xs font-semibold text-slate-700 hover:bg-slate-50 transition-all">
                <i class="fas fa-external-link-alt"></i> Buka di Tab Baru
            </a>
        </div>
        <div class="w-full h-[650px] bg-slate-100">
            <object data="<?php echo e($pdfUrl); ?>" type="application/pdf" class="w-full h-full">
                <iframe src="<?php echo e($pdfUrl); ?>" class="w-full h-full border-0">
                    <p class="p-6 text-center text-slate-500">Browser Anda tidak mendukung pratinjau PDF. Silakan <a href="<?php echo e($pdfUrl); ?>" class="text-primary underline">unduh file PDF</a> untuk melihatnya.</p>
                </iframe>
            </object>
        </div>
     <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginaled3368a8117ee5d527ad55a40e435525)): ?>
<?php $attributes = $__attributesOriginaled3368a8117ee5d527ad55a40e435525; ?>
<?php unset($__attributesOriginaled3368a8117ee5d527ad55a40e435525); ?>
<?php endif; ?>
<?php if (isset($__componentOriginaled3368a8117ee5d527ad55a40e435525)): ?>
<?php $component = $__componentOriginaled3368a8117ee5d527ad55a40e435525; ?>
<?php unset($__componentOriginaled3368a8117ee5d527ad55a40e435525); ?>
<?php endif; ?>
</div>
<?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

<?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(empty($pdfUrl) || !empty($soalList)): ?>
<div class="mt-6">
    <?php if (isset($component)) { $__componentOriginaled3368a8117ee5d527ad55a40e435525 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginaled3368a8117ee5d527ad55a40e435525 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'banksoal::components.ui.panel','data' => ['title' => 'Daftar Soal','padding' => 'p-0']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('banksoal::ui.panel'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => 'Daftar Soal','padding' => 'p-0']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

        <div class="divide-y divide-slate-100">
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $soalList; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $soal): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                <div class="p-5">
                    <div class="flex items-start gap-3">
                        <div class="w-9 h-9 rounded-lg bg-primary/10 text-primary font-bold flex items-center justify-center"><?php echo e($soal['nomor'] ?? $loop->iteration); ?></div>
                        <div class="flex-1">
                            <div class="text-sm text-slate-800 leading-relaxed">
                                <?php echo $soal['soal'] ?? '-'; ?>

                            </div>
                            <div class="mt-2 flex flex-wrap gap-2 text-xs text-slate-500">
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(!empty($soal['cpl'])): ?><span class="px-2 py-1 rounded-full bg-slate-100">CPL: <?php echo e($soal['cpl']); ?></span><?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(!empty($soal['cpmk'])): ?><span class="px-2 py-1 rounded-full bg-emerald-50 text-emerald-700">CPMK: <?php echo e($soal['cpmk']); ?></span><?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(!empty($soal['bobot'])): ?><span class="px-2 py-1 rounded-full bg-amber-50 text-amber-700">Bobot: <?php echo e($soal['bobot']); ?></span><?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(empty($pdfUrl)): ?>
                    <div class="p-8 text-center text-slate-500">Tidak ada soal yang tersimpan.</div>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </div>
     <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginaled3368a8117ee5d527ad55a40e435525)): ?>
<?php $attributes = $__attributesOriginaled3368a8117ee5d527ad55a40e435525; ?>
<?php unset($__attributesOriginaled3368a8117ee5d527ad55a40e435525); ?>
<?php endif; ?>
<?php if (isset($__componentOriginaled3368a8117ee5d527ad55a40e435525)): ?>
<?php $component = $__componentOriginaled3368a8117ee5d527ad55a40e435525; ?>
<?php unset($__componentOriginaled3368a8117ee5d527ad55a40e435525); ?>
<?php endif; ?>
</div>
<?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

 <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal315efe54efa54bd7b6313284b579a4ac)): ?>
<?php $attributes = $__attributesOriginal315efe54efa54bd7b6313284b579a4ac; ?>
<?php unset($__attributesOriginal315efe54efa54bd7b6313284b579a4ac); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal315efe54efa54bd7b6313284b579a4ac)): ?>
<?php $component = $__componentOriginal315efe54efa54bd7b6313284b579a4ac; ?>
<?php unset($__componentOriginal315efe54efa54bd7b6313284b579a4ac); ?>
<?php endif; ?><?php /**PATH C:\WebsiteTekkom - Copy\Modules\BankSoal\resources\views\pages\arsip\Dosen\show.blade.php ENDPATH**/ ?>