<?php
    $layoutComponent = 'banksoal::layouts.dosen-admin';
    if (auth()->user()->hasRole('gpm')) {
        $layoutComponent = 'banksoal::layouts.gpm-master';
    } elseif (auth()->user()->hasRole('admin_banksoal') || auth()->user()->hasRole('admin')) {
        $layoutComponent = 'banksoal::layouts.admin';
    }
?>

<?php if (isset($component)) { $__componentOriginal511d4862ff04963c3c16115c05a86a9d = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal511d4862ff04963c3c16115c05a86a9d = $attributes; } ?>
<?php $component = Illuminate\View\DynamicComponent::resolve(['component' => $layoutComponent] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('dynamic-component'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\DynamicComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

    <?php $__env->startSection('breadcrumbs'); ?>
    <span class="text-slate-500 hover:text-primary transition-colors">Manajemen Modul</span>
    <span class="mx-2 text-slate-300">/</span>
    <span class="text-slate-500 hover:text-primary transition-colors">RPS</span>
    <span class="mx-2 text-slate-300">/</span>
    <span class="text-slate-800 font-semibold">Preview RPS</span>
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
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'banksoal::components.ui.page-header','data' => ['title' => 'Detail & Preview RPS','subtitle' => 'Detail lengkap terkait RPS, parameter kesesuaian, audit log, dan dokumen PDF']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('banksoal::ui.page-header'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => 'Detail & Preview RPS','subtitle' => 'Detail lengkap terkait RPS, parameter kesesuaian, audit log, dan dokumen PDF']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

         <?php $__env->slot('actions', null, []); ?> 
            <button type="button" class="inline-flex items-center gap-2 rounded-xl border border-slate-200 px-4 py-2.5 text-sm font-semibold text-slate-600 hover:bg-slate-50 transition-all bg-white shadow-sm" onclick="window.history.back()">
                <i class="fas fa-arrow-left text-xs"></i> Kembali
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

    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(!empty($rps)): ?>
        <!-- Metadata Cards Grid -->
        <div class="mb-6 grid gap-4 xl:grid-cols-3">
            <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                <div class="mb-2 text-[11px] font-bold uppercase tracking-wider text-primary">Mata Kuliah</div>
                <div class="text-lg font-bold text-slate-900 leading-snug"><?php echo e($rps->mk_nama); ?></div>
                <div class="mt-2 text-sm text-slate-500 font-medium">
                    <span class="bg-slate-100 text-slate-700 px-2 py-0.5 rounded text-xs font-semibold mr-1.5"><?php echo e($rps->kode); ?></span>
                    Semester <?php echo e($rps->semester); ?> &middot; TA <?php echo e($rps->tahun_ajaran); ?>

                </div>
            </div>

            <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                <div class="mb-3 text-[11px] font-bold uppercase tracking-wider text-primary">Dosen Pengampu</div>
                <div class="flex flex-wrap gap-2 max-h-24 overflow-y-auto pr-1">
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $dosenPengampu; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $dosen): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                        <span class="inline-flex items-center rounded-full bg-primary/5 px-3 py-1 text-xs font-semibold text-primary border border-primary/10">
                            <?php echo e($dosen->name); ?>

                        </span>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                        <span class="text-sm text-slate-400 italic">Tidak ada dosen pengampu terdata</span>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div>
            </div>

            <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                <div class="mb-3 text-[11px] font-bold uppercase tracking-wider text-primary">CPL / CPMK Terkoneksi</div>
                <div class="space-y-3 max-h-24 overflow-y-auto pr-1">
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($cplCpmkMappings->isNotEmpty()): ?>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $cplCpmkMappings; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $cplId => $rows): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                            <?php $firstRow = $rows->first(); ?>
                            <div class="rounded-xl border border-slate-100 bg-slate-50 p-2.5">
                                <div class="text-xs font-bold text-slate-800"><?php echo e($firstRow->cpl_kode); ?></div>
                                <div class="mt-1.5 space-y-1.5">
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $rows; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $row): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                                        <div class="rounded-lg border border-emerald-100 bg-white px-2.5 py-1.5 text-[11px] text-slate-700">
                                            <span class="font-bold text-emerald-700"><?php echo e($row->cpmk_kode); ?></span>
                                            <div class="mt-0.5 text-[10px] text-slate-500 leading-normal"><?php echo e($row->cpmk_deskripsi); ?></div>
                                        </div>
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                                </div>
                            </div>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                            <div class="text-xs text-slate-400 italic">Belum ada pemetaan CPL/CPMK untuk MK ini.</div>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    <?php else: ?>
                        <div class="text-xs text-slate-400 italic">Belum ada pemetaan CPL/CPMK untuk MK ini.</div>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div>
            </div>
        </div>

        <?php
            $status = strtolower($rps->status ?? 'draft');
            if ($status === 'disetujui') {
                $statusClass = 'border-emerald-200 bg-emerald-50 text-emerald-700';
                $iconClass = 'bg-emerald-100 text-emerald-600';
                $statusText = 'Disetujui GPM';
                $statusIcon = '✓';
                $statusMsg = 'RPS ini telah disetujui dan siap digunakan sebagai acuan pengajaran.';
            } elseif ($status === 'revisi') {
                $statusClass = 'border-rose-200 bg-rose-50 text-rose-700';
                $iconClass = 'bg-rose-100 text-rose-600';
                $statusText = 'Perlu Revisi';
                $statusIcon = '✕';
                $statusMsg = 'RPS dikembalikan oleh GPM untuk dilakukan perbaikan sesuai catatan penilaian.';
            } elseif ($status === 'diajukan') {
                $statusClass = 'border-amber-200 bg-amber-50 text-amber-700';
                $iconClass = 'bg-amber-100 text-amber-600';
                $statusText = 'Menunggu Validasi GPM';
                $statusIcon = '⏳';
                $statusMsg = 'RPS telah diajukan dan sedang menunggu antrean peninjauan oleh tim GPM.';
            } else {
                $statusClass = 'border-slate-200 bg-slate-50 text-slate-700';
                $iconClass = 'bg-slate-100 text-slate-600';
                $statusText = 'Draf';
                $statusIcon = '📄';
                $statusMsg = 'RPS masih berupa draf dan belum diajukan untuk proses validasi.';
            }
        ?>

        <!-- Status Banner -->
        <div class="mb-6 rounded-2xl border p-4 shadow-sm <?php echo e($statusClass); ?> transition-all duration-300">
            <div class="flex items-start gap-3">
                <div class="flex h-8 w-8 flex-shrink-0 items-center justify-center rounded-full font-bold shadow-sm <?php echo e($iconClass); ?> text-sm">
                    <?php echo e($statusIcon); ?>

                </div>
                <div class="text-sm">
                    <p class="font-bold tracking-tight">Status: <?php echo e($statusText); ?></p>
                    <p class="text-xs mt-0.5 opacity-90 leading-relaxed"><?php echo e($statusMsg); ?></p>
                </div>
            </div>
        </div>

        <!-- Main Workspace: PDF on left, Review/Audit on right -->
        <div class="grid grid-cols-1 gap-6 xl:grid-cols-5 items-stretch">
            <!-- Left: PDF Frame (3/5) -->
            <div class="xl:col-span-3">
                <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden h-[600px] flex flex-col">
                    <div class="flex items-center justify-between border-b border-slate-200 bg-slate-50 px-4 py-3">
                        <div class="text-xs font-bold text-slate-600 flex items-center gap-2 truncate">
                            <i class="fas fa-file-pdf text-rose-500"></i> <?php echo e(basename($rps->dokumen ?? 'Dokumen RPS.pdf')); ?>

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

            <!-- Right: Ringkasan Penilaian & History Log (2/5) -->
            <div class="xl:col-span-2 flex flex-col gap-6">
                <!-- Ringkasan Penilaian Card -->
                <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-5 flex flex-col flex-1">
                    <div class="text-sm font-bold text-slate-800 flex items-center gap-2 mb-4 border-b border-slate-100 pb-3">
                        <span class="flex h-6 w-6 items-center justify-center rounded-lg bg-navy/10 text-navy">
                            <i class="fas fa-check-circle text-xs"></i>
                        </span>
                        Ringkasan Penilaian GPM
                    </div>
                    
                    <div class="mb-4 rounded-2xl border border-slate-200 bg-slate-50 p-4 text-center shadow-sm">
                        <p class="text-xs font-bold text-slate-500 uppercase tracking-wider mb-1">Skor Akhir Evaluasi</p>
                        <p class="text-3xl font-extrabold text-navy">
                            <?php echo e(isset($existingReview) ? $existingReview->nilai_akhir : '0'); ?><span class="text-lg text-slate-400 font-semibold">/<?php echo e($totalBobot); ?></span>
                        </p>
                    </div>

                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(isset($existingReview) && !empty($existingReview->catatan)): ?>
                        <div class="mb-4">
                            <label class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">Catatan Komentar GPM</label>
                            <div class="mt-1.5 w-full rounded-xl border border-slate-200 bg-slate-50 px-3.5 py-2.5 text-xs text-slate-600 font-medium leading-relaxed">
                                <?php echo e($existingReview->catatan); ?>

                            </div>
                        </div>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                    <!-- Parameter Checklist Breakdown -->
                    <div class="border-t border-slate-100 pt-4 flex-1 overflow-y-auto max-h-[220px] pr-1">
                        <label class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block mb-3">Breakdown Parameter Kesesuaian</label>
                        <div class="space-y-2">
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $parameters; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $param): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                                <?php
                                    $skorVal = $reviewChecklist[$param->id] ?? null;
                                ?>
                                <div class="flex items-start justify-between p-2.5 rounded-xl border <?php echo e($skorVal === '1' ? 'bg-emerald-50/50 border-emerald-100/70 text-emerald-800' : ($skorVal === '0' ? 'bg-rose-50/50 border-rose-100/70 text-rose-800' : 'bg-slate-50/50 border-slate-100 text-slate-500')); ?> transition-all duration-200">
                                    <div class="text-[11px] font-semibold leading-relaxed pr-2">
                                        <?php echo e($param->aspek); ?>

                                        <span class="text-[9px] text-slate-400 block font-normal mt-0.5">Bobot: <?php echo e($param->bobot); ?></span>
                                    </div>
                                    <div class="flex-shrink-0">
                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($skorVal === '1'): ?>
                                            <span class="inline-flex h-5 w-5 items-center justify-center rounded-full bg-emerald-100 text-emerald-700 font-extrabold text-[10px]">✓</span>
                                        <?php elseif($skorVal === '0'): ?>
                                            <span class="inline-flex h-5 w-5 items-center justify-center rounded-full bg-rose-100 text-rose-700 font-extrabold text-[10px]">✕</span>
                                        <?php else: ?>
                                            <span class="inline-flex h-5 w-5 items-center justify-center rounded-full bg-slate-100 text-slate-400 font-extrabold text-[10px]">-</span>
                                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                    </div>
                                </div>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                                <p class="text-xs text-slate-400 italic">Parameter kriteria penilaian kosong.</p>
                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </div>
                    </div>
                </div>

                <!-- History Log Card -->
                <div class="bg-white rounded-2xl border border-slate-200 p-5 shadow-sm max-h-[220px] overflow-hidden flex flex-col">
                    <div class="text-xs font-bold text-slate-800 uppercase tracking-wider mb-3 flex items-center gap-2 border-b border-slate-100 pb-2">
                        <i class="fas fa-history text-slate-400"></i> Audit History Log
                    </div>
                    <div class="space-y-3.5 overflow-y-auto pr-1 flex-1">
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $history; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                            <div class="relative pl-5 border-l-2 <?php echo e($loop->first ? 'border-primary' : 'border-slate-200'); ?> pb-1.5 last:pb-0">
                                <span class="absolute -left-[5px] top-1.5 h-2 w-2 rounded-full <?php echo e($loop->first ? 'bg-primary animate-pulse' : 'bg-slate-300'); ?>"></span>
                                <div class="flex items-center justify-between gap-2">
                                    <p class="text-[11px] font-bold text-slate-800 leading-none"><?php echo e(ucfirst($item->action)); ?></p>
                                    <p class="text-[9px] font-medium text-slate-400"><?php echo e(\Carbon\Carbon::parse($item->created_at)->format('d M Y - H:i')); ?></p>
                                </div>
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($item->description): ?>
                                    <p class="text-[10px] text-slate-500 mt-1 leading-normal font-medium"><?php echo e($item->description); ?></p>
                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            </div>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                            <div class="rounded-xl border border-slate-100 bg-slate-50 p-4 text-center text-xs text-slate-400 italic">
                                Belum ada riwayat aktivitas audit trail untuk RPS ini.
                            </div>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    <?php else: ?>
        <div class="bg-white rounded-2xl border border-slate-200 p-12 text-center shadow-sm">
            <div class="flex flex-col items-center justify-center">
                <i class="fas fa-exclamation-circle text-4xl text-rose-500 mb-3 animate-bounce"></i>
                <h2 class="text-lg font-bold text-slate-800">RPS Tidak Ditemukan</h2>
                <p class="text-xs text-slate-500 mt-1 max-w-sm">
                    Detail dokumen RPS yang Anda cari tidak tersedia atau terjadi kesalahan saat memuat data.
                </p>
                <button type="button" class="mt-4 inline-flex items-center gap-2 rounded-xl bg-primary px-4 py-2 text-xs font-semibold text-white hover:bg-primary/95 transition-all shadow-md shadow-primary/20" onclick="window.history.back()">
                    <i class="fas fa-arrow-left"></i> Kembali
                </button>
            </div>
        </div>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
 <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal511d4862ff04963c3c16115c05a86a9d)): ?>
<?php $attributes = $__attributesOriginal511d4862ff04963c3c16115c05a86a9d; ?>
<?php unset($__attributesOriginal511d4862ff04963c3c16115c05a86a9d); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal511d4862ff04963c3c16115c05a86a9d)): ?>
<?php $component = $__componentOriginal511d4862ff04963c3c16115c05a86a9d; ?>
<?php unset($__componentOriginal511d4862ff04963c3c16115c05a86a9d); ?>
<?php endif; ?>
<?php /**PATH C:\WebsiteTekkom - Copy\Modules\BankSoal\resources\views\pages\rps\preview-page.blade.php ENDPATH**/ ?>