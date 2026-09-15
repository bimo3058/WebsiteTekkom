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
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'banksoal::components.ui.page-header','data' => ['title' => 'Dashboard GPM','subtitle' => 'Ringkasan aktivitas penjaminan mutu akademik.']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('banksoal::ui.page-header'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => 'Dashboard GPM','subtitle' => 'Ringkasan aktivitas penjaminan mutu akademik.']); ?>
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

    <div class="mb-6 rounded-2xl border border-amber-200 bg-amber-50 p-4">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div class="flex items-center gap-3">
                <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-amber-100 text-amber-600">
                    <i class="fas fa-bell"></i>
                </div>
                <div>
                    <p class="text-sm font-semibold text-amber-900">Perhatian: Ada <?php echo e($statRpsMenunggu ?? 0); ?> RPS dan <?php echo e($statBankSoalMenunggu ?? 0); ?> Paket Soal yang masuk ke daftar antrean review.</p>
                    <p class="text-xs text-amber-800">Segera lakukan peninjauan untuk menjaga kualitas akademik.</p>
                </div>
            </div>
        </div>
    </div>

    <div class="mb-6 grid grid-cols-1 gap-3 md:grid-cols-3">
        <?php if (isset($component)) { $__componentOriginal463b94b5039cdd6c42deca583fe6719d = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal463b94b5039cdd6c42deca583fe6719d = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'banksoal::components.ui.stat-card','data' => ['label' => 'RPS Menunggu Validasi','value' => $statRpsMenunggu,'icon' => 'fa-file-alt','tone' => 'blue']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('banksoal::ui.stat-card'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['label' => 'RPS Menunggu Validasi','value' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($statRpsMenunggu),'icon' => 'fa-file-alt','tone' => 'blue']); ?>
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
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'banksoal::components.ui.stat-card','data' => ['label' => 'Bank Soal Menunggu','value' => $statBankSoalMenunggu,'icon' => 'fa-question-circle','tone' => 'amber']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('banksoal::ui.stat-card'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['label' => 'Bank Soal Menunggu','value' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($statBankSoalMenunggu),'icon' => 'fa-question-circle','tone' => 'amber']); ?>
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
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'banksoal::components.ui.stat-card','data' => ['label' => 'Selesai Direview Bulan Ini','value' => $tugasSelesai,'icon' => 'fa-check-circle','tone' => 'green']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('banksoal::ui.stat-card'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['label' => 'Selesai Direview Bulan Ini','value' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($tugasSelesai),'icon' => 'fa-check-circle','tone' => 'green']); ?>
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

    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-slate-200 bg-slate-50 flex items-center justify-between">
            <h2 class="text-lg font-semibold text-slate-900">Tugas Prioritas</h2>
            <a href="#" class="text-xs font-medium text-primary hover:text-primary/90">Lihat Semua</a>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-slate-50 border-b border-slate-200">
                    <tr>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Tipe Dokumen</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Mata Kuliah</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Deadline</th>
                        <th class="px-6 py-4 text-right text-xs font-semibold text-slate-500 uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $tugasPrioritas; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $tugas): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                        <tr class="hover:bg-slate-50/50 transition-colors">
                            <td class="px-6 py-4">
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($tugas->tipe_dokumen == 'Bank Soal'): ?>
                                    <span class="inline-flex items-center rounded-full bg-purple-50 px-3 py-1 text-[11px] font-semibold text-purple-700 border border-purple-200">Bank Soal</span>
                                <?php elseif(($tugas->sub_status ?? '') == 'revisi'): ?>
                                    <span class="inline-flex items-center rounded-full bg-amber-50 px-3 py-1 text-[11px] font-semibold text-amber-700 border border-amber-200">RPS - Revisi</span>
                                <?php else: ?>
                                    <span class="inline-flex items-center rounded-full bg-primary/10 px-3 py-1 text-[11px] font-semibold text-primary border border-primary/20">RPS - Diajukan</span>
                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            </td>
                            <td class="px-6 py-4">
                                <div class="font-semibold text-slate-900"><?php echo e($tugas->mk_nama); ?></div>
                                <div class="text-xs text-slate-500"><?php echo e($tugas->mk_kode); ?></div>
                            </td>
                            <td class="px-6 py-4 text-sm text-slate-600">Menunggu Review</td>
                            <td class="px-6 py-4 text-right">
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($tugas->tipe_dokumen == 'Bank Soal'): ?>
                                    <a href="<?php echo e(route('banksoal.soal.gpm.validasi-bank-soal')); ?>" class="inline-flex items-center rounded-xl bg-primary px-3 py-2 text-xs font-semibold text-white hover:bg-primary/90">Review Sekarang</a>
                                <?php else: ?>
                                    <a href="<?php echo e(route('banksoal.rps.gpm.validasi-rps.review', $tugas->rps_id)); ?>" class="inline-flex items-center rounded-xl bg-primary px-3 py-2 text-xs font-semibold text-white hover:bg-primary/90">Review Sekarang</a>
                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            </td>
                        </tr>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                        <tr>
                            <td colspan="4" class="px-6 py-12 text-center text-slate-600">
                                <div class="flex flex-col items-center justify-center">
                                    <i class="fas fa-check-circle text-3xl text-slate-300 mb-3"></i>
                                    <p class="font-medium">Semua tugas selesai.</p>
                                    <p class="text-xs text-slate-500">Tidak ada tugas prioritas yang perlu segera direview.</p>
                                </div>
                            </td>
                        </tr>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </tbody>
            </table>
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
<?php endif; ?><?php /**PATH C:\WebsiteTekkom - Copy\Modules\BankSoal\resources\views\dashboard\gpm.blade.php ENDPATH**/ ?>