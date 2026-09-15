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
        <span class="text-slate-800 font-semibold">Dashboard</span>
    <?php $__env->stopSection(); ?>
    <?php if (isset($component)) { $__componentOriginal4f4ab3c60e35d72431cf46a8394b8c9b = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal4f4ab3c60e35d72431cf46a8394b8c9b = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'banksoal::components.ui.page-header','data' => ['title' => 'Dashboard Dosen','subtitle' => 'Ringkasan performa bank soal, RPS, dan distribusi soal aktif.']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('banksoal::ui.page-header'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => 'Dashboard Dosen','subtitle' => 'Ringkasan performa bank soal, RPS, dan distribusi soal aktif.']); ?>
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

    
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(count($mkTanpaRps) > 0): ?>
    <div class="mb-4 rounded-2xl border border-amber-200 bg-amber-50 p-3 sm:p-4">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div class="flex items-center gap-3">
                <div class="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg bg-amber-100 text-amber-600">
                    <i class="fas fa-exclamation-triangle text-xs"></i>
                </div>
                <div>
                    <p class="text-sm font-semibold text-amber-900">Peringatan: Anda belum mengupload RPS Mata Kuliah <?php echo e(implode(', ', $mkTanpaRps)); ?>.</p>
                    <p class="text-xs text-amber-800">Segera upload RPS sebelum Anda bisa mengelola bank soal untuk mata kuliah tersebut.</p>
                </div>
            </div>
            <a href="<?php echo e(route('banksoal.rps.dosen.index')); ?>" class="inline-flex items-center justify-center rounded-xl bg-amber-600 px-3 py-2 text-xs font-semibold text-white hover:bg-amber-700 whitespace-nowrap">
                Upload Sekarang
            </a>
        </div>
    </div>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

    
    <div class="mb-3 grid grid-cols-2 gap-2 sm:grid-cols-4">
        <?php if (isset($component)) { $__componentOriginal463b94b5039cdd6c42deca583fe6719d = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal463b94b5039cdd6c42deca583fe6719d = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'banksoal::components.ui.stat-card','data' => ['label' => 'Total Soal','value' => $totalSoal,'icon' => 'fa-layer-group','tone' => 'blue']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('banksoal::ui.stat-card'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['label' => 'Total Soal','value' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($totalSoal),'icon' => 'fa-layer-group','tone' => 'blue']); ?>
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
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'banksoal::components.ui.stat-card','data' => ['label' => 'Approved','value' => $approved,'icon' => 'fa-circle-check','tone' => 'green']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('banksoal::ui.stat-card'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['label' => 'Approved','value' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($approved),'icon' => 'fa-circle-check','tone' => 'green']); ?>
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
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'banksoal::components.ui.stat-card','data' => ['label' => 'Dalam Pengajuan','value' => $perluReview,'icon' => 'fa-clock-rotate-left','tone' => 'blue']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('banksoal::ui.stat-card'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['label' => 'Dalam Pengajuan','value' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($perluReview),'icon' => 'fa-clock-rotate-left','tone' => 'blue']); ?>
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
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'banksoal::components.ui.stat-card','data' => ['label' => 'Revisi / Ditolak','value' => $revisi + $ditolak,'icon' => 'fa-circle-xmark','tone' => 'red']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('banksoal::ui.stat-card'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['label' => 'Revisi / Ditolak','value' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($revisi + $ditolak),'icon' => 'fa-circle-xmark','tone' => 'red']); ?>
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

    
    <div class="mb-3 grid grid-cols-1 gap-2 md:grid-cols-3">

        
        <?php if (isset($component)) { $__componentOriginaled3368a8117ee5d527ad55a40e435525 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginaled3368a8117ee5d527ad55a40e435525 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'banksoal::components.ui.panel','data' => ['title' => 'Question Analytics','subtitle' => 'Komposisi status soal saat ini','padding' => 'p-3']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('banksoal::ui.panel'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => 'Question Analytics','subtitle' => 'Komposisi status soal saat ini','padding' => 'p-3']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

            <div class="flex items-center gap-3">
                
                <div class="relative shrink-0" style="width:80px;height:80px">
                    <svg width="80" height="80" viewBox="0 0 80 80" id="donutChart"></svg>
                    <div class="absolute inset-0 flex flex-col items-center justify-center pointer-events-none">
                        <strong class="text-sm font-bold text-slate-900"><?php echo e($totalSoal); ?></strong>
                        <span class="text-[9px] uppercase tracking-wider text-slate-500">Total</span>
                    </div>
                </div>
                
                <div class="flex-1 space-y-0.5 text-[11px]">
                    <div class="flex items-center justify-between text-slate-700">
                        <span class="flex items-center gap-1"><span class="h-1.5 w-1.5 rounded-full bg-green-500"></span>Disetujui</span><span><?php echo e($approved); ?></span>
                    </div>
                    <div class="flex items-center justify-between text-slate-700">
                        <span class="flex items-center gap-1"><span class="h-1.5 w-1.5 rounded-full bg-primary"></span>Diajukan</span><span><?php echo e($perluReview); ?></span>
                    </div>
                    <div class="flex items-center justify-between text-slate-700">
                        <span class="flex items-center gap-1"><span class="h-1.5 w-1.5 rounded-full bg-amber-500"></span>Revisi</span><span><?php echo e($revisi); ?></span>
                    </div>
                </div>
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

        
        <?php if (isset($component)) { $__componentOriginaled3368a8117ee5d527ad55a40e435525 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginaled3368a8117ee5d527ad55a40e435525 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'banksoal::components.ui.panel','data' => ['title' => 'Academic Period','subtitle' => 'Informasi semester dan MK aktif','padding' => 'p-3']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('banksoal::ui.panel'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => 'Academic Period','subtitle' => 'Informasi semester dan MK aktif','padding' => 'p-3']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

            <div class="text-center">
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(count($mkTanpaRps) > 0): ?>
                    <span class="inline-flex rounded-full bg-rose-100 px-2 py-0.5 text-[10px] font-semibold text-rose-700">RPS: NOT UPLOADED</span>
                <?php else: ?>
                    <span class="inline-flex rounded-full bg-emerald-100 px-2 py-0.5 text-[10px] font-semibold text-emerald-700">RPS: UPLOADED</span>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                <p class="mt-2 text-[10px] uppercase tracking-wider text-slate-500">Academic Year</p>
                <p class="text-sm font-bold text-slate-900">Semester Berjalan</p>
                <p class="mt-2 text-[10px] uppercase tracking-wider text-slate-500">Active Courses</p>
                <div class="mt-1.5 flex justify-center gap-1.5 flex-wrap">
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $mataKuliah; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $mk): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                        <span class="rounded-lg bg-primary/10 px-2 py-0.5 text-[11px] font-semibold text-primary"><?php echo e($mk->kode); ?></span>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($mataKuliah->isEmpty()): ?>
                        <span class="text-xs text-slate-400">Belum ada mata kuliah</span>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div>
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

        
        <?php if (isset($component)) { $__componentOriginaled3368a8117ee5d527ad55a40e435525 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginaled3368a8117ee5d527ad55a40e435525 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'banksoal::components.ui.panel','data' => ['title' => 'Lecturer Profile','subtitle' => 'Ringkasan profil dosen','padding' => 'p-3']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('banksoal::ui.panel'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => 'Lecturer Profile','subtitle' => 'Ringkasan profil dosen','padding' => 'p-3']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

            <div class="text-center">
                <div class="mx-auto mb-1.5 flex h-10 w-10 items-center justify-center rounded-full bg-slate-200 text-sm font-bold text-slate-700">
                    <?php echo e(strtoupper(substr(auth()->user()->name, 0, 1))); ?>

                </div>
                <p class="text-xs font-semibold text-slate-900"><?php echo e(auth()->user()->name); ?></p>
                <p class="text-[11px] text-slate-600"><?php echo e(auth()->user()->lecturer?->employee_number ?? auth()->user()->email); ?></p>
                <p class="text-[11px] text-slate-500"><?php echo e(auth()->user()->lecturer?->department ?? 'Teknik Komputer'); ?></p>
                <a href="/profile" class="mt-2 inline-flex items-center rounded-lg border border-slate-200 bg-white px-2 py-1 text-[11px] font-semibold text-slate-700 hover:bg-slate-50">
                    Lihat Profil
                </a>
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

    
    <div class="grid grid-cols-1 gap-2 md:grid-cols-2">
        <?php if (isset($component)) { $__componentOriginaled3368a8117ee5d527ad55a40e435525 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginaled3368a8117ee5d527ad55a40e435525 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'banksoal::components.ui.panel','data' => ['title' => 'Question Distribution per CPL','subtitle' => 'Berdasarkan Capaian Pembelajaran (CPL)','padding' => 'p-4']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('banksoal::ui.panel'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => 'Question Distribution per CPL','subtitle' => 'Berdasarkan Capaian Pembelajaran (CPL)','padding' => 'p-4']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

             <?php $__env->slot('actions', null, []); ?> 
                <a href="<?php echo e(route('banksoal.soal.dosen.index')); ?>" class="text-xs font-medium text-primary hover:text-primary/90">Details <i class="fas fa-arrow-up-right-from-square text-xs"></i></a>
             <?php $__env->endSlot(); ?>
            <div class="h-64 flex items-end gap-4" id="cplChart"></div>
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

        <?php if (isset($component)) { $__componentOriginaled3368a8117ee5d527ad55a40e435525 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginaled3368a8117ee5d527ad55a40e435525 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'banksoal::components.ui.panel','data' => ['title' => 'Question Count per MK','subtitle' => 'Distribusi seluruh bank soal dosen','padding' => 'p-4']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('banksoal::ui.panel'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => 'Question Count per MK','subtitle' => 'Distribusi seluruh bank soal dosen','padding' => 'p-4']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

             <?php $__env->slot('actions', null, []); ?> 
                <a href="<?php echo e(route('banksoal.soal.dosen.index')); ?>" class="text-xs font-medium text-primary hover:text-primary/90">Details <i class="fas fa-arrow-up-right-from-square text-xs"></i></a>
             <?php $__env->endSlot(); ?>
            <div class="h-64 flex items-end gap-4" id="mkChart"></div>
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

    <script src="<?php echo e(asset('modules/banksoal/js/Banksoal/components/DosenDashboard.js')); ?>"></script>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            // Data dinamis dari server
            const baseDonutData = <?php echo json_encode($donutData, 15, 512) ?>;
            const baseCplData = <?php echo json_encode($cplDist, 15, 512) ?>;
            const baseMkData = <?php echo json_encode($mkDist, 15, 512) ?>;

            // Update menggunakan method pada DosenDashboardComponent (Global Instance)
            setTimeout(() => {
                if(typeof DosenDashboard !== 'undefined') {
                    DosenDashboard.updateDonutChart('donutChart', baseDonutData);
                    DosenDashboard.updateCplBarChart('cplChart', baseCplData);
                    DosenDashboard.updateMkBarChart('mkChart', baseMkData);
                }
            }, 100);
        });
    </script>
    <?php echo $__env->make('banksoal::partials.dosen.layout-scripts', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
 <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal315efe54efa54bd7b6313284b579a4ac)): ?>
<?php $attributes = $__attributesOriginal315efe54efa54bd7b6313284b579a4ac; ?>
<?php unset($__attributesOriginal315efe54efa54bd7b6313284b579a4ac); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal315efe54efa54bd7b6313284b579a4ac)): ?>
<?php $component = $__componentOriginal315efe54efa54bd7b6313284b579a4ac; ?>
<?php unset($__componentOriginal315efe54efa54bd7b6313284b579a4ac); ?>
<?php endif; ?>
<?php /**PATH C:\WebsiteTekkom - Copy\Modules\BankSoal\resources\views\dashboard\dosen.blade.php ENDPATH**/ ?>