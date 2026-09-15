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
    <span class="text-slate-800 font-semibold">Revisi RPS</span>
    <?php $__env->stopSection(); ?>
    <?php
        $skorMinimum = \Illuminate\Support\Facades\DB::table('bs_pengaturan')->where('kunci', 'standar_skor_minimum')->value('nilai') ?? 60;
    ?>
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
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'banksoal::components.ui.page-header','data' => ['title' => 'Revisi RPS','subtitle' => 'Lakukan revisi atau tinjau ulang perbaikan RPS']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('banksoal::ui.page-header'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => 'Revisi RPS','subtitle' => 'Lakukan revisi atau tinjau ulang perbaikan RPS']); ?>
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
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($selectedCpls->isNotEmpty()): ?>
                        <div class="rounded-xl border border-slate-200 bg-slate-50 p-3">
                            <div class="text-sm font-semibold text-slate-900 mb-2">CPL Terpilih</div>
                            <div class="flex flex-wrap gap-2">
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $selectedCpls; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $cpl): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                                    <span class="inline-flex items-center rounded-full bg-slate-100 px-2.5 py-1 text-[11px] font-semibold text-slate-700 border border-slate-200">
                                        <?php echo e($cpl->kode); ?>

                                    </span>
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                            </div>
                        </div>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                    <div class="rounded-xl border border-amber-200 bg-amber-50 p-3">
                        <div class="text-sm font-semibold text-amber-900 mb-2">Draft CPMK</div>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(!empty($draftCpmkItems)): ?>
                            <div class="space-y-2">
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $draftCpmkItems; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                                    <div class="rounded-lg border border-amber-200 bg-white px-3 py-2 text-xs text-slate-700">
                                        <span class="font-semibold text-slate-900"><?php echo e($item->cpl_kode); ?></span>
                                        <span class="mx-1 text-slate-400">&rarr;</span>
                                        <span class="font-semibold text-amber-700"><?php echo e($item->cpmk_kode); ?></span>
                                        <div class="mt-1 text-[11px] text-slate-500"><?php echo e($item->cpmk_deskripsi); ?></div>
                                    </div>
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                            </div>
                        <?php else: ?>
                            <div class="text-sm text-amber-800">Belum ada draft CPMK yang diajukan.</div>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </div>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </div>
        </div>
    </div>

    <?php
        $status = strtolower($rps->status ?? 'diajukan');
        $statusClass = $status === 'revisi'
            ? 'border-rose-200 bg-rose-50 text-rose-700'
            : ($status === 'disetujui'
                ? 'border-emerald-200 bg-emerald-50 text-emerald-700'
                : 'border-amber-200 bg-amber-50 text-amber-700');
        $iconClass = $status === 'revisi'
            ? 'bg-rose-100 text-rose-600'
            : ($status === 'disetujui'
                ? 'bg-emerald-100 text-emerald-600'
                : 'bg-amber-100 text-amber-600');
    ?>

    <div id="statusBanner" class="mb-6 rounded-2xl border p-4 <?php echo e($statusClass); ?>">
        <div class="flex items-start gap-3">
            <div id="statusIcon" class="flex h-8 w-8 items-center justify-center rounded-full font-semibold <?php echo e($iconClass); ?>">!</div>
            <div class="text-sm">
                <p class="font-semibold">Status: <span id="statusText"><?php echo e(ucfirst($rps->status)); ?></span></p>
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
                            class="absolute inset-0 w-full h-full border-0" 
                            onload="handleIframeLoad()"
                            onerror="handleIframeError()">
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
                <div class="text-sm font-semibold text-slate-900 flex items-center gap-2 mb-4">
                    <i class="fas fa-clipboard-check text-primary"></i> Form Penilaian GPM
                </div>

                <form id="validasiForm" method="POST" class="flex-1 flex flex-col">
                    <?php echo csrf_field(); ?>
                    <input type="hidden" name="rps_id" value="<?php echo e($rps->rps_id); ?>">
                    <input type="hidden" name="action" id="actionInput" value="">
                    <div class="flex-1 overflow-y-auto pr-4 lg:max-h-[380px] max-h-[320px]">
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $parameters; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $param): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                            <div class="mb-4 rounded-xl border border-slate-200 bg-slate-50 p-4 shadow-sm">
                                <p class="text-sm font-semibold text-slate-700"><?php echo e($index + 1); ?>. <?php echo e($param->aspek); ?> <span class="text-primary">(<?php echo e($param->bobot); ?> poin)</span></p>
                                <div class="mt-3 flex gap-6 text-sm text-slate-600">
                                    <label class="inline-flex items-center gap-2 cursor-pointer font-medium hover:text-primary transition-colors">
                                        <input type="radio" name="parameter_<?php echo e($param->id); ?>" value="1" data-bobot="<?php echo e($param->bobot); ?>" class="w-4 h-4 text-primary border-slate-300 focus:ring-primary" onchange="hitungSkor()" required> Sesuai
                                    </label>
                                    <label class="inline-flex items-center gap-2 cursor-pointer font-medium hover:text-rose-600 transition-colors">
                                        <input type="radio" name="parameter_<?php echo e($param->id); ?>" value="0" data-bobot="<?php echo e($param->bobot); ?>" class="w-4 h-4 text-rose-600 border-slate-300 focus:ring-rose-500" onchange="hitungSkor()" required> Tidak Sesuai
                                    </label>
                                </div>
                            </div>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                            <div class="rounded-lg border border-slate-200 bg-slate-50 px-3 py-2 text-xs text-slate-600">Tidak ada parameter penilaian yang tersedia</div>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </div>

                    <div class="mt-4 border-y border-dashed border-slate-200 py-3 flex items-center justify-between">
                        <span class="text-xs font-semibold text-slate-500">Skor Evaluasi</span>
                        <span class="text-lg font-bold text-primary" id="nilaiAkhir"><?php echo e(isset($existingReview) ? $existingReview->nilai_akhir : '0'); ?>/<?php echo e($totalBobot); ?></span>
                    </div>

                    <div class="mt-4">
                        <label for="catatan" class="text-xs font-semibold text-slate-600">Catatan Revisi</label>
                        <textarea id="catatan" name="catatan" class="mt-2 w-full rounded-lg border border-slate-200 px-3 py-2 text-sm" rows="4" placeholder="Masukkan detail perbaikan jika diperlukan..."><?php echo e(isset($existingReview) ? $existingReview->catatan : ''); ?></textarea>
                    </div>

                    <div class="mt-4 flex gap-3">
                        <button type="button" class="flex-1 rounded-lg border border-rose-200 px-3 py-2 text-xs font-semibold text-rose-600 hover:bg-rose-50" id="btnKembalikan">Kembalikan</button>
                        <button type="button" class="flex-1 rounded-lg bg-primary px-3 py-2 text-xs font-semibold text-white hover:bg-primary/90 disabled:bg-slate-300 disabled:text-slate-500 disabled:cursor-not-allowed" id="btnSetujui">Setujui RPS</button>
                    </div>
                </form>
            </div>

            <div class="bg-slate-50 rounded-2xl border border-slate-200 p-4">
                <p class="text-[11px] font-semibold text-primary uppercase tracking-wider mb-3">History Log</p>
                <div class="space-y-3">
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

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            window.handleIframeLoad = function () {
                const pdfFrame = document.getElementById('pdfFrame');
                if (pdfFrame) {
                    pdfFrame.classList.add('ready');
                }
            };

            window.handleIframeError = function () {
                const pdfFrame = document.getElementById('pdfFrame');
                if (pdfFrame) {
                    pdfFrame.classList.add('ready');
                }
            };

            const pdfFrame = document.getElementById('pdfFrame');
            if (pdfFrame) {
                pdfFrame.classList.add('loading');
            }

            setTimeout(() => {
                if (pdfFrame && !pdfFrame.classList.contains('ready')) {
                    window.handleIframeLoad();
                }
            }, 3000);

            window.hitungSkor = function () {
                const form = document.getElementById('validasiForm');
                const nilaiAkhirEl = document.getElementById('nilaiAkhir');
                let totalNilai = 0;

                const inputs = form.querySelectorAll('input[type="radio"]:checked');
                inputs.forEach(input => {
                    if (input.value === '1') {
                        totalNilai += parseInt(input.getAttribute('data-bobot')) || 0;
                    }
                });

                nilaiAkhirEl.textContent = totalNilai + '/<?php echo e($totalBobot); ?>';
                
                const MIN_SCORE = <?php echo e($skorMinimum); ?>;
                if (totalNilai >= MIN_SCORE) {
                    nilaiAkhirEl.classList.remove('text-slate-300', 'text-rose-600');
                    nilaiAkhirEl.classList.add('text-emerald-600');
                } else if (totalNilai > 0) {
                    nilaiAkhirEl.classList.remove('text-slate-300', 'text-emerald-600');
                    nilaiAkhirEl.classList.add('text-rose-600');
                } else {
                    nilaiAkhirEl.classList.remove('text-emerald-600', 'text-rose-600');
                    nilaiAkhirEl.classList.add('text-slate-300');
                }

                updateButtonState(totalNilai);
            };

            window.updateButtonState = function (score) {
                const MIN_SCORE = <?php echo e($skorMinimum); ?>;
                const btnSetujui = document.getElementById('btnSetujui');
                if (!btnSetujui) return;

                if (score < MIN_SCORE) {
                    btnSetujui.disabled = true;
                    btnSetujui.setAttribute('title', 'Nilai di bawah standar, ajukan revisi terlebih dahulu');
                } else {
                    btnSetujui.disabled = false;
                    btnSetujui.setAttribute('title', '');
                }
            };

            window.validateParametersNotEmpty = function () {
                const form = document.getElementById('validasiForm');
                const radioLabels = form.querySelectorAll('[name^="parameter_"]');
                const grouped = {};

                radioLabels.forEach((radio) => {
                    const name = radio.getAttribute('name');
                    grouped[name] = grouped[name] || [];
                    grouped[name].push(radio);
                });

                const emptyParameters = Object.keys(grouped).filter(name => {
                    return !grouped[name].some(radio => radio.checked);
                });

                if (emptyParameters.length > 0) {
                    showToast('Parameter belum lengkap. Harap lengkapi semua penilaian.', 'error');
                    return false;
                }
                return true;
            };

            const btnKembalikan = document.getElementById('btnKembalikan');
            const btnSetujui = document.getElementById('btnSetujui');
            const form = document.getElementById('validasiForm');
            const actionInput = document.getElementById('actionInput');
            const catatanTextarea = document.getElementById('catatan');

            updateButtonState(0);
            if (typeof hitungSkor === 'function') {
                hitungSkor();
            }

            if (btnKembalikan) {
                btnKembalikan.addEventListener('click', function (e) {
                    e.preventDefault();

                    if (!validateParametersNotEmpty()) {
                        return;
                    }

                    const catatan = catatanTextarea.value.trim();
                    if (!catatan) {
                        showToast('Catatan revisi harus diisi sebelum mengembalikan RPS', 'error');
                        catatanTextarea.focus();
                        return;
                    }

                    actionInput.value = 'revisi';
                    submitForm();
                });
            }

            if (btnSetujui) {
                btnSetujui.addEventListener('click', function (e) {
                    e.preventDefault();

                    if (!validateParametersNotEmpty()) {
                        return;
                    }

                    actionInput.value = 'setuju';
                    submitForm();
                });

                btnSetujui.addEventListener('mousedown', function (e) {
                    if (this.disabled) {
                        e.preventDefault();
                        showToast('Nilai di bawah standar, ajukan revisi terlebih dahulu', 'error');
                    }
                });
            }

            function submitForm() {
                const formData = new FormData(form);

                fetch('<?php echo e(route("banksoal.rps.gpm.validasi-rps.store")); ?>', {
                    method: 'POST',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json',
                    },
                    body: formData,
                    credentials: 'same-origin'
                })
                .then(response => {
                    if (!response.ok) {
                        return response.json().then(data => {
                            throw new Error(data.message || 'Terjadi kesalahan');
                        });
                    }
                    return response.json();
                })
                .then(data => {
                    const message = data.message + ' (Skor: ' + data.nilai_akhir + '/<?php echo e($totalBobot); ?>)';
                    showToast(message, 'success');
                    updateStatusBanner(data.status);
                    setTimeout(() => {
                        window.location.href = data.redirect;
                    }, 1500);
                })
                .catch(error => {
                    console.error('Error:', error);
                    showToast(error.message || 'Gagal menyimpan validasi', 'error');
                });
            }

            function updateStatusBanner(status) {
                const banner = document.getElementById('statusBanner');
                const statusIcon = document.getElementById('statusIcon');
                const statusText = document.getElementById('statusText');

                const baseClasses = ['border-amber-200', 'bg-amber-50', 'text-amber-700', 'border-rose-200', 'bg-rose-50', 'text-rose-700', 'border-emerald-200', 'bg-emerald-50', 'text-emerald-700'];
                baseClasses.forEach(cls => banner.classList.remove(cls));

                const iconBase = ['bg-amber-100', 'text-amber-600', 'bg-rose-100', 'text-rose-600', 'bg-emerald-100', 'text-emerald-600'];
                iconBase.forEach(cls => statusIcon.classList.remove(cls));

                if (status === 'revisi') {
                    banner.classList.add('border-rose-200', 'bg-rose-50', 'text-rose-700');
                    statusIcon.classList.add('bg-rose-100', 'text-rose-600');
                    statusIcon.textContent = '◄';
                    statusText.textContent = 'Revisi';
                } else if (status === 'disetujui') {
                    banner.classList.add('border-emerald-200', 'bg-emerald-50', 'text-emerald-700');
                    statusIcon.classList.add('bg-emerald-100', 'text-emerald-600');
                    statusIcon.textContent = '✓';
                    statusText.textContent = 'Disetujui';
                } else {
                    banner.classList.add('border-amber-200', 'bg-amber-50', 'text-amber-700');
                    statusIcon.classList.add('bg-amber-100', 'text-amber-600');
                    statusIcon.textContent = '!';
                    statusText.textContent = 'Diajukan';
                }
            }

            function showToast(message, type) {
                if (typeof Snackbar !== 'undefined' && typeof Snackbar.show === 'function') {
                    Snackbar.show(message, type);
                } else {
                    alert(message);
                }
            }
        });
    </script>
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
<?php /**PATH C:\WebsiteTekkom - Copy\Modules\BankSoal\resources\views\gpm\validasi-rps-revisi.blade.php ENDPATH**/ ?>