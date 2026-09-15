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

    <?php $__env->startSection('breadcrumbs'); ?>
    <span class="text-slate-500 hover:text-primary transition-colors">Manajemen Modul</span>
    <span class="mx-2 text-slate-300">/</span>
    <a href="<?php echo e(route('banksoal.rps.gpm.validasi-rps')); ?>" class="text-slate-500 hover:text-primary transition-colors">Validasi RPS</a>
    <span class="mx-2 text-slate-300">/</span>
    <span class="text-slate-800 font-semibold">RPS Disetujui</span>
    <?php $__env->stopSection(); ?>
    
    <?php if (isset($component)) { $__componentOriginal7543ab43ddc66fd305c9f53ce0a7b55c = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal7543ab43ddc66fd305c9f53ce0a7b55c = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'banksoal::components.notification.alerts','data' => []] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('banksoal::notification.alerts'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal7543ab43ddc66fd305c9f53ce0a7b55c)): ?>
<?php $attributes = $__attributesOriginal7543ab43ddc66fd305c9f53ce0a7b55c; ?>
<?php unset($__attributesOriginal7543ab43ddc66fd305c9f53ce0a7b55c); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal7543ab43ddc66fd305c9f53ce0a7b55c)): ?>
<?php $component = $__componentOriginal7543ab43ddc66fd305c9f53ce0a7b55c; ?>
<?php unset($__componentOriginal7543ab43ddc66fd305c9f53ce0a7b55c); ?>
<?php endif; ?>
    <?php if (isset($component)) { $__componentOriginal4f4ab3c60e35d72431cf46a8394b8c9b = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal4f4ab3c60e35d72431cf46a8394b8c9b = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'banksoal::components.ui.page-header','data' => ['title' => 'RPS Disetujui','subtitle' => 'Lihat hasil akhir RPS yang telah divalidasi']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('banksoal::ui.page-header'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => 'RPS Disetujui','subtitle' => 'Lihat hasil akhir RPS yang telah divalidasi']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

         <?php $__env->slot('actions', null, []); ?> 
            <button type="button" class="inline-flex items-center gap-2 rounded-xl border border-slate-200 px-4 py-2.5 text-sm font-semibold text-slate-600 hover:bg-slate-50" onclick="window.history.back()">
                <i class="fas fa-arrow-left"></i> Kembali
            </button>
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

    <div class="mb-6 grid gap-4 xl:grid-cols-3">
        <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
            <div class="mb-2 text-[11px] font-semibold uppercase tracking-wider text-primary">Mata Kuliah</div>
            <div class="text-lg font-bold text-slate-900"><?php echo e($rps->mk_nama); ?></div>
            <div class="mt-2 text-sm text-slate-600"><?php echo e($rps->kode); ?> &middot; Semester <?php echo e($rps->semester); ?> <?php echo e($rps->tahun_ajaran); ?></div>
        </div>

        <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
            <div class="mb-3 text-[11px] font-semibold uppercase tracking-wider text-primary">Dosen Pengampu</div>
            <div class="flex flex-wrap gap-2">
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $dosenPengampu; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $dosen): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                    <span class="inline-flex items-center rounded-full bg-primary/10 px-3 py-1 text-xs font-semibold text-primary border border-primary/20">
                        <?php echo e($dosen->name); ?>

                    </span>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                    <span class="text-sm text-slate-500">Tidak ada dosen terdata</span>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </div>
        </div>

        <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
            <div class="mb-3 text-[11px] font-semibold uppercase tracking-wider text-primary">CPL / CPMK Terkoneksi</div>
            <div class="space-y-3 max-h-40 overflow-y-auto pr-1">
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($cplCpmkMappings->isNotEmpty()): ?>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $cplCpmkMappings; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $cplId => $rows): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                        <?php $firstRow = $rows->first(); ?>
                        <div class="rounded-xl border border-slate-200 bg-slate-50 p-3">
                            <div class="text-sm font-semibold text-slate-900"><?php echo e($firstRow->cpl_kode); ?></div>
                            <div class="mt-2 space-y-2">
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $rows; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $row): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                                    <div class="rounded-lg border border-emerald-200 bg-white px-3 py-2 text-xs text-slate-700">
                                        <span class="font-semibold text-emerald-700"><?php echo e($row->cpmk_kode); ?></span>
                                        <div class="mt-1 text-[11px] text-slate-500"><?php echo e($row->cpmk_deskripsi); ?></div>
                                    </div>
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                            </div>
                        </div>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                        <div class="text-sm text-slate-500">Belum ada pemetaan CPL/CPMK untuk MK ini.</div>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                <?php else: ?>
                    <div class="text-sm text-slate-500">Belum ada pemetaan CPL/CPMK untuk MK ini.</div>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </div>
        </div>
    </div>

    <?php
        $statusClass = 'border-emerald-200 bg-emerald-50 text-emerald-700';
        $iconClass = 'bg-emerald-100 text-emerald-600';
    ?>

    <div id="statusBanner" class="mb-6 rounded-2xl border p-4 <?php echo e($statusClass); ?>">
        <div class="flex items-start gap-3">
            <div id="statusIcon" class="flex h-8 w-8 items-center justify-center rounded-full font-semibold <?php echo e($iconClass); ?>">✓</div>
            <div class="text-sm">
                <p class="font-semibold">Status: <span id="statusText">Disetujui</span></p>
                <p class="text-xs">
                    Mata Kuliah: <?php echo e($rps->mk_nama); ?> (<?php echo e($rps->kode); ?>) &bull; Diserahkan oleh:
                    <?php
                        $dosensList = !empty($rps->dosens_list) ? array_map('trim', explode(',', $rps->dosens_list)) : [];
                    ?>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $dosensList; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $dosenItem): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                        <?php
                            $parts = explode('|', $dosenItem, 3);
                            $dosenName = $parts[1] ?? $dosenItem;
                        ?>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(!empty($dosenName)): ?>
                            <?php echo e($dosenName); ?><?php echo e($index < count($dosensList) - 1 ? ', ' : ''); ?>

                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                        -
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </p>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 gap-6 lg:grid-cols-5">
        <div class="lg:col-span-3">
            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden lg:h-[700px] h-[500px] flex flex-col">
                <div class="flex items-center justify-between border-b border-slate-200 bg-slate-50 px-4 py-3">
                    <div class="text-xs font-semibold text-slate-600 flex items-center gap-2">
                        <i class="fas fa-file-pdf text-rose-500"></i> <?php echo e(basename($rps->dokumen)); ?>

                    </div>
                    <div class="flex items-center gap-2">
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(!empty($fileUrl)): ?>
                            <a href="<?php echo e($fileUrl); ?>" target="_blank" class="rounded-lg p-1.5 text-slate-500 hover:bg-slate-200 hover:text-slate-800 transition-colors" title="Buka PDF di tab baru">
                                <i class="fas fa-external-link-alt text-xs"></i>
                            </a>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(!empty($downloadUrl)): ?>
                            <a href="<?php echo e($downloadUrl); ?>" class="rounded-lg p-1.5 text-slate-500 hover:bg-slate-200 hover:text-slate-800 transition-colors" title="Unduh berkas PDF">
                                <i class="fas fa-download text-xs"></i>
                            </a>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </div>
                </div>
                <div class="flex-1 bg-slate-100 relative"> 
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(!empty($fileUrl)): ?>
                        <iframe
                            id="pdfFrame"
                            src="<?php echo e($fileUrl); ?>"
                            loading="eager"
                            title="PDF Preview RPS"
                            class="absolute inset-0 w-full h-full border-0">
                        </iframe>
                    <?php else: ?>
                        <div class="absolute inset-0 flex flex-col items-center justify-center p-6 text-center">
                            <i class="fas fa-exclamation-triangle text-3xl text-amber-500 mb-3 animate-pulse"></i>
                            <h3 class="text-sm font-bold text-slate-700">Berkas PDF Tidak Tersedia</h3>
                            <p class="text-xs text-slate-500 mt-1 max-w-xs leading-relaxed">
                                <?php echo e($errorMessage ?? 'File RPS belum diunggah atau tidak ditemukan di storage.'); ?>

                            </p>
                        </div>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div>
            </div>
        </div>

        <div class="lg:col-span-2 lg:sticky lg:top-6 lg:self-start space-y-6">
            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-5 flex flex-col">
                <div class="text-sm font-semibold text-emerald-700 flex items-center gap-2 mb-4">
                    <i class="fas fa-check-circle text-emerald-500"></i> Ringkasan Penilaian
                </div>
                
                <div class="mb-4 rounded-xl border border-emerald-200 bg-emerald-50 p-4 shadow-sm text-center">
                    <p class="text-sm font-semibold text-emerald-800 mb-1">Skor Evaluasi</p>
                    <p class="text-3xl font-bold text-emerald-600"><?php echo e(isset($existingReview) ? $existingReview->nilai_akhir : '0'); ?><span class="text-lg text-emerald-400">/<?php echo e($totalBobot); ?></span></p>
                </div>

                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(isset($existingReview) && !empty($existingReview->catatan)): ?>
                <div class="mt-2 mb-4">
                    <label class="text-xs font-semibold text-slate-600">Catatan GPM</label>
                    <div class="mt-2 w-full rounded-lg border border-slate-200 bg-slate-50 px-3 py-2 text-sm text-slate-700">
                        <?php echo e($existingReview->catatan); ?>

                    </div>
                </div>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </div>

            <div class="bg-slate-50 rounded-2xl border border-slate-200 p-4">
                <p class="text-[11px] font-semibold text-primary uppercase tracking-wider mb-3">History Log</p>
                <div class="space-y-3 max-h-60 overflow-y-auto pr-2">
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $history; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                        <div class="relative pl-4">
                            <span class="absolute left-0 top-1.5 h-2 w-2 rounded-full <?php echo e($loop->first ? 'bg-primary' : 'bg-amber-400'); ?>"></span>
                            <p class="text-xs font-semibold text-slate-700"><?php echo e(ucfirst($item->action)); ?></p>
                            <p class="text-[11px] text-slate-500"><?php echo e(\Carbon\Carbon::parse($item->created_at)->format('d M Y - H:i')); ?></p>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($item->description): ?>
                                <p class="text-[11px] text-slate-500"><?php echo e($item->description); ?></p>
                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </div>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                        <div class="rounded-lg border border-slate-200 bg-white px-3 py-2 text-xs text-slate-600">Belum ada riwayat aktivitas</div>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div>
            </div>
        </div>
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
<?php endif; ?>
<?php /**PATH C:\WebsiteTekkom - Copy\Modules\BankSoal\resources\views\gpm\validasi-rps-setuju.blade.php ENDPATH**/ ?>