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
        <span class="text-slate-800 font-semibold">Arsip Soal</span>
    <?php $__env->stopSection(); ?>

    <style>
        :root {
            --navy: #0B266E;
            --navy-light: rgba(11, 38, 110, 0.1);
        }
        .bg-navy { background-color: var(--navy); }
        .text-navy { color: var(--navy); }
        .border-navy { border-color: var(--navy); }
        .shadow-navy { --tw-shadow-color: rgba(11, 38, 110, 0.2); }
        
        @keyframes popup {
            0% { opacity: 0; transform: scale(0.95) translateY(10px); }
            100% { opacity: 1; transform: scale(1) translateY(0); }
        }
        .animate-popup {
            animation: popup 0.3s cubic-bezier(0.34, 1.56, 0.64, 1) forwards;
        }

    </style>

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
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'banksoal::components.ui.page-header','data' => ['title' => 'Arsip Soal Dosen','subtitle' => 'Kelola riwayat penarikan dan arsip final dokumen ujian Anda.']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('banksoal::ui.page-header'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => 'Arsip Soal Dosen','subtitle' => 'Kelola riwayat penarikan dan arsip final dokumen ujian Anda.']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

         <?php $__env->slot('actions', null, []); ?> 
            <div class="flex flex-wrap items-center gap-3">
                <div class="relative" x-data="{ open: false }">
                    <button @click="open = !open" class="inline-flex items-center gap-2 rounded-xl bg-navy px-5 py-2.5 font-bold text-white shadow-lg shadow-navy/20 transition-all hover:opacity-90 active:scale-95">
                        <i class="fas fa-plus-circle"></i> Tambah Arsip
                        <i class="fas fa-chevron-down text-[10px] transition-transform" :class="open ? 'rotate-180' : ''"></i>
                    </button>
                    <div x-show="open" @click.away="open = false" x-cloak x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 translate-y-2 scale-95" x-transition:enter-end="opacity-100 translate-y-0 scale-100" class="absolute right-0 mt-2 w-56 origin-top-right rounded-2xl border border-slate-100 bg-white shadow-xl z-50 p-2 space-y-1">
                        <a href="<?php echo e(route('banksoal.arsip.dosen.create-pdf')); ?>" class="flex items-center gap-3 px-3 py-3 rounded-xl text-xs font-bold text-slate-600 hover:bg-slate-50 hover:text-navy transition-all group">
                            <span class="flex h-8 w-8 items-center justify-center rounded-lg bg-rose-50 text-rose-500 group-hover:bg-rose-100 transition-colors">
                                <i class="fas fa-file-pdf"></i>
                            </span>
                            <div class="flex flex-col">
                                <span>Upload PDF</span>
                                <span class="text-[10px] text-slate-400 font-normal">Format PDF Standar</span>
                            </div>
                        </a>
                        <a href="<?php echo e(route('banksoal.arsip.dosen.create-csv')); ?>" class="flex items-center gap-3 px-3 py-3 rounded-xl text-xs font-bold text-slate-600 hover:bg-slate-50 hover:text-navy transition-all group">
                            <span class="flex h-8 w-8 items-center justify-center rounded-lg bg-emerald-50 text-emerald-500 group-hover:bg-emerald-100 transition-colors">
                                <i class="fas fa-file-excel"></i>
                            </span>
                            <div class="flex flex-col">
                                <span>Import CSV/Excel</span>
                                <span class="text-[10px] text-slate-400 font-normal">Gunakan Template</span>
                            </div>
                        </a>
                    </div>
                </div>
            </div>
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

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
        <?php if (isset($component)) { $__componentOriginal463b94b5039cdd6c42deca583fe6719d = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal463b94b5039cdd6c42deca583fe6719d = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'banksoal::components.ui.stat-card','data' => ['label' => 'Total Arsip','value' => $stats['total_arsip'],'icon' => 'fa-archive','tone' => 'blue']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('banksoal::ui.stat-card'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['label' => 'Total Arsip','value' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($stats['total_arsip']),'icon' => 'fa-archive','tone' => 'blue']); ?>
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
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'banksoal::components.ui.stat-card','data' => ['label' => 'Riwayat Penarikan','value' => $stats['total_penarikan'],'icon' => 'fa-history','tone' => 'amber']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('banksoal::ui.stat-card'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['label' => 'Riwayat Penarikan','value' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($stats['total_penarikan']),'icon' => 'fa-history','tone' => 'amber']); ?>
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
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'banksoal::components.ui.stat-card','data' => ['label' => 'Mata Kuliah','value' => $stats['mata_kuliah'],'icon' => 'fa-book','tone' => 'indigo']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('banksoal::ui.stat-card'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['label' => 'Mata Kuliah','value' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($stats['mata_kuliah']),'icon' => 'fa-book','tone' => 'indigo']); ?>
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

    <div class="space-y-8 flex flex-col">
        <div class="bg-white rounded-3xl border border-slate-200 shadow-sm overflow-hidden order-1" x-data="{ expandedGroups: [] }">
            <div class="px-8 py-6 border-b border-slate-100 bg-white">
                <div class="flex flex-col gap-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <h3 class="text-xl font-bold text-slate-900 flex items-center gap-3">
                                <span class="flex h-10 w-10 items-center justify-center rounded-2xl bg-navy text-white shadow-lg shadow-navy/20">
                                    <i class="fas fa-archive text-sm"></i>
                                </span>
                                Daftar Arsip Final
                            </h3>
                        </div>
                        <span class="px-3 py-1 rounded-full bg-navy/5 text-navy text-xs font-bold"><?php echo e($stats['total_arsip']); ?> Arsip</span>
                    </div>

                    <div class="flex flex-col md:flex-row items-center justify-between gap-4">
                        <form action="<?php echo e(route('banksoal.arsip.dosen.index')); ?>" method="GET" class="flex flex-col md:flex-row items-center gap-3 w-full" id="filterForm">
                            <div class="relative w-full md:w-96">
                                <i class="fas fa-search absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 text-sm"></i>
                                <input type="text" name="search" value="<?php echo e($filters['search']); ?>" placeholder="Cari nama arsip atau MK..." class="w-full pl-11 pr-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:ring-4 focus:ring-navy/5 focus:border-navy transition-all outline-none">
                            </div>

                            <?php if (isset($component)) { $__componentOriginal3b959de169ce576b87a8cfe77cf2c842 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal3b959de169ce576b87a8cfe77cf2c842 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'banksoal::components.ui.filter-panel','data' => ['formId' => 'filterForm','hasActiveFilter' => request('years') || request('semesters') ? true : false,'resetRoute' => ''.e(route('banksoal.arsip.dosen.index')).'','applyLabel' => 'Terapkan']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('banksoal::ui.filter-panel'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['formId' => 'filterForm','hasActiveFilter' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(request('years') || request('semesters') ? true : false),'resetRoute' => ''.e(route('banksoal.arsip.dosen.index')).'','applyLabel' => 'Terapkan']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

                                <div>
                                    <label class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-3 block">Tahun Ajaran</label>
                                    <div class="space-y-2 max-h-40 overflow-y-auto pr-2">
                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $availableYears; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $year): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                                        <label class="flex items-center gap-3 p-2 rounded-lg hover:bg-slate-50 cursor-pointer group">
                                            <input type="checkbox" name="years[]" value="<?php echo e($year); ?>" <?php echo e(in_array($year, (array)request('years')) ? 'checked' : ''); ?> class="w-4 h-4 rounded border-slate-300 text-navy focus:ring-navy transition-all">
                                            <span class="text-sm text-slate-700 group-hover:text-navy transition-colors"><?php echo e($year); ?></span>
                                        </label>
                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                                    </div>
                                </div>
                                <div class="pt-3 border-t border-slate-100">
                                    <label class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-3 block">Semester</label>
                                    <div class="space-y-2">
                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = ['Ganjil', 'Genap']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $sem): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                                        <label class="flex items-center gap-3 p-2 rounded-lg hover:bg-slate-50 cursor-pointer group">
                                            <input type="checkbox" name="semesters[]" value="<?php echo e($sem); ?>" <?php echo e(in_array($sem, (array)request('semesters')) ? 'checked' : ''); ?> class="w-4 h-4 rounded border-slate-300 text-navy focus:ring-navy transition-all">
                                            <span class="text-sm text-slate-700 group-hover:text-navy transition-colors"><?php echo e($sem); ?></span>
                                        </label>
                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                                    </div>
                                </div>
                             <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal3b959de169ce576b87a8cfe77cf2c842)): ?>
<?php $attributes = $__attributesOriginal3b959de169ce576b87a8cfe77cf2c842; ?>
<?php unset($__attributesOriginal3b959de169ce576b87a8cfe77cf2c842); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal3b959de169ce576b87a8cfe77cf2c842)): ?>
<?php $component = $__componentOriginal3b959de169ce576b87a8cfe77cf2c842; ?>
<?php unset($__componentOriginal3b959de169ce576b87a8cfe77cf2c842); ?>
<?php endif; ?>

                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($filters['search'] || request('years') || request('semesters')): ?>
                            <a href="<?php echo e(route('banksoal.arsip.dosen.index')); ?>" class="text-rose-500 hover:text-rose-700 text-xs font-bold underline px-2">Reset</a>
                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </form>
                    </div>
                </div>
            </div>

            <div class="p-0">
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-primary text-[10px] font-bold text-white uppercase tracking-widest">
                                <th class="w-12 px-8 py-4"></th>
                                <th class="px-4 py-4">Mata Kuliah</th>
                                <th class="px-8 py-4">Jumlah Arsip</th>
                                <th class="px-8 py-4">Status</th>
                                <th class="px-8 py-4 text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            <?php $arsipGroups = $arsipPaginated->groupBy('mk_id'); ?>
                            <?php if($arsipGroups->isNotEmpty()): ?>
                                <?php foreach($arsipGroups as $mkId => $items): ?>
                                <?php $first = $items->first(); ?>
                                <tr class="hover:bg-slate-50/50 cursor-pointer transition-colors group" @click="expandedGroups.includes(<?php echo e($mkId); ?>) ? expandedGroups = expandedGroups.filter(i => i !== <?php echo e($mkId); ?>) : expandedGroups.push(<?php echo e($mkId); ?>)">
                                    <td class="px-8 py-5 text-center">
                                        <i class="fas fa-chevron-right text-slate-300 transition-transform duration-300" :class="expandedGroups.includes(<?php echo e($mkId); ?>) ? 'rotate-90 text-navy' : ''"></i>
                                    </td>
                                    <td class="px-4 py-5">
                                        <div class="flex items-center gap-4">
                                            <div class="flex h-11 w-11 items-center justify-center rounded-2xl bg-slate-50 text-slate-400 group-hover:bg-navy group-hover:text-white transition-all shadow-sm">
                                                <i class="fas fa-book text-sm"></i>
                                            </div>
                                            <div class="flex flex-col">
                                                <span class="font-bold text-slate-900 text-base"><?php echo e($first->mataKuliah->nama); ?></span>
                                                <span class="text-xs text-slate-400 font-medium tracking-wide uppercase"><?php echo e($first->mataKuliah->kode); ?></span>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-8 py-5">
                                        <div class="flex items-center gap-2">
                                            <span class="px-3 py-1 rounded-full bg-navy/5 text-navy text-xs font-bold"><?php echo e($items->count()); ?> Versi</span>
                                        </div>
                                    </td>
                                    <td class="px-8 py-5">
                                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg bg-emerald-50 text-[10px] font-bold text-emerald-600 border border-emerald-100 uppercase tracking-wider">
                                            <i class="fas fa-check-circle text-[8px]"></i> Aktif
                                        </span>
                                    </td>
                                    <td class="px-8 py-5 text-right">
                                        <div class="flex items-center justify-end gap-3">
                                            <div class="flex -space-x-2">
                                                <?php foreach($items->unique('dosen_id')->take(3) as $a): ?>
                                                    <div class="h-8 w-8 rounded-full border-2 border-white bg-slate-100 flex items-center justify-center text-[10px] font-bold text-slate-500 overflow-hidden shadow-sm" title="<?php echo e($a->dosen->name ?? 'Dosen'); ?>">
                                                        <?php echo e(substr($a->dosen->name ?? 'D', 0, 1)); ?>

                                                    </div>
                                                <?php endforeach; ?>
                                            </div>
                                            <i class="fas fa-ellipsis-v text-slate-300 group-hover:text-slate-600 transition-colors p-2"></i>
                                        </div>
                                    </td>
                                </tr>

                                <?php foreach($items as $arsip): ?>
                                <?php
                                    preg_match('/\((.*?)\)/', $arsip->nama_arsip, $matches);
                                    $categoryAbbr = $matches[1] ?? (strpos($arsip->nama_arsip, 'UTS') !== false ? 'UTS' : (strpos($arsip->nama_arsip, 'UAS') !== false ? 'UAS' : 'Arsip'));
                                ?>
                                <tr x-show="expandedGroups.includes(<?php echo e($mkId); ?>)" x-cloak x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 -translate-y-2" x-transition:enter-end="opacity-100 translate-y-0" class="bg-slate-50/30">
                                    <td class="px-8 py-0"></td>
                                    <td colspan="4" class="px-4 py-3">
                                        <div class="bg-white rounded-2xl border border-slate-200 p-4 shadow-sm hover:border-navy/30 transition-colors">
                                            <div class="grid grid-cols-1 md:grid-cols-5 gap-4 items-center">
                                                <div class="flex flex-col">
                                                    <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1">Pengarsip</p>
                                                    <div class="flex items-center gap-2">
                                                        <div class="h-6 w-6 rounded-full bg-slate-100 flex items-center justify-center text-[10px] font-bold text-slate-500 shadow-inner">
                                                            <?php echo e(substr($arsip->dosen->name ?? 'D', 0, 1)); ?>

                                                        </div>
                                                        <p class="text-sm font-bold text-slate-800 truncate" title="<?php echo e($arsip->dosen->name ?? '-'); ?>">
                                                            <?php echo e($arsip->dosen->name ?? '-'); ?>

                                                        </p>
                                                    </div>
                                                </div>
                                                <div class="flex flex-col border-l border-slate-100 pl-4">
                                                    <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1">Kategori</p>
                                                    <span class="inline-flex w-fit px-2.5 py-0.5 rounded-lg bg-navy/5 text-navy text-[10px] font-bold border border-navy/10">
                                                        <?php echo e($categoryAbbr); ?>

                                                    </span>
                                                </div>
                                                <div class="flex flex-col border-l border-slate-100 pl-4">
                                                    <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1">Periode</p>
                                                    <p class="text-sm text-slate-700 font-medium"><?php echo e($arsip->tahun_akademik); ?> - <span class="text-navy text-xs"><?php echo e($arsip->semester); ?></span></p>
                                                </div>
                                                <div class="flex flex-col border-l border-slate-100 pl-4">
                                                    <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1">Statistik</p>
                                                    <div class="flex items-center gap-3">
                                                        <span class="text-xs text-slate-600 flex items-center gap-1"><i class="fas fa-list-ol text-[10px] text-slate-300"></i> <?php echo e($arsip->jumlah_soal); ?></span>
                                                        <span class="text-xs text-slate-600 flex items-center gap-1"><i class="fas fa-star text-[10px] text-slate-300"></i> <?php echo e(number_format($arsip->total_bobot, 1)); ?></span>
                                                    </div>
                                                </div>
                                                <div class="flex items-center justify-end gap-2 relative" x-data="{ menuOpen: false }">
                                                    <button @click="menuOpen = !menuOpen" class="p-2 rounded-xl border border-slate-200 text-slate-400 hover:text-navy transition-all">
                                                        <i class="fas fa-ellipsis-h"></i>
                                                    </button>
                                                    <div x-show="menuOpen" @click.away="menuOpen = false" class="absolute right-0 top-12 w-48 bg-white rounded-xl border border-slate-100 shadow-xl z-50 p-2 space-y-1 text-left">
                                                        <a href="<?php echo e(route('banksoal.arsip.dosen.show', $arsip->id)); ?>" class="flex items-center gap-3 px-3 py-2 rounded-lg text-xs font-bold text-slate-600 hover:bg-slate-50 hover:text-navy transition-all">
                                                            <i class="fas fa-external-link-alt w-4"></i> Buka Detail
                                                        </a>
                                                        <form action="<?php echo e(route('banksoal.arsip.dosen.destroy', $arsip->id)); ?>" method="POST" onsubmit="if(confirm('Hapus arsip ini?')){ window.showLoader(); return true; } else { return false; }" class="block">
                                                            <?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?>
                                                            <button type="submit" class="flex w-full items-center gap-3 px-3 py-2 rounded-lg text-xs font-bold text-rose-500 hover:bg-rose-50 transition-all">
                                                                <i class="fas fa-trash-alt w-4"></i> Hapus Arsip
                                                            </button>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                                <?php endforeach; ?>
                            <?php else: ?>
                            <tr>
                                <td colspan="5" class="px-8 py-20 text-center text-slate-500">
                                    Tidak ada data arsip yang ditemukan.
                                </td>
                            </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>

                <div class="px-8 py-6 border-t border-slate-100 bg-slate-50/30">
                    <?php echo e($arsipPaginated->appends(request()->all())->links('banksoal::components.ui.laravel-pagination')); ?>

                </div>
            </div>
        </div>

        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($penarikanPending->isNotEmpty()): ?>
        <div class="bg-white rounded-3xl border border-slate-200 shadow-sm overflow-hidden order-2 mt-8">
            <div class="px-8 py-5 border-b border-slate-100 bg-slate-50/50 flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <span class="flex h-8 w-8 items-center justify-center rounded-lg bg-amber-100 text-amber-600">
                        <i class="fas fa-clock-rotate-left text-sm"></i>
                    </span>
                    <h3 class="font-bold text-slate-900">Riwayat Penarikan (Pending)</h3>
                </div>
                <span class="px-3 py-1 rounded-full bg-amber-100 text-amber-700 text-xs font-bold"><?php echo e($penarikanPending->count()); ?> Item</span>
            </div>
            <div class="p-0">
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-primary text-[10px] font-bold text-white uppercase tracking-widest">
                                <th class="px-8 py-4">Detail Penarikan</th>
                                <th class="px-8 py-4">Mata Kuliah</th>
                                <th class="px-8 py-4">Waktu</th>
                                <th class="px-8 py-4 text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $penarikanPending; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $penarikan): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                            <tr class="hover:bg-slate-50/50 transition-colors group">
                                <td class="px-8 py-5">
                                    <div class="flex flex-col">
                                        <span class="font-bold text-slate-900 group-hover:text-navy transition-colors"><?php echo e($penarikan->nama_ekstraksi); ?></span>
                                        <div class="flex items-center gap-2 mt-1">
                                            <span class="px-2 py-0.5 rounded-md bg-amber-50 text-[10px] font-bold text-amber-600 border border-amber-100 uppercase"><?php echo e($penarikan->tipe_ujian); ?></span>
                                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($penarikan->metode_ujian === 'offline'): ?>
                                            <span class="px-2 py-0.5 rounded-md bg-slate-100 text-[10px] font-bold text-slate-600 border border-slate-200 uppercase">Offline</span>
                                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-8 py-5">
                                    <div class="flex flex-col">
                                        <span class="text-sm font-medium text-slate-700"><?php echo e($penarikan->mataKuliah->nama); ?></span>
                                        <span class="text-xs text-slate-400"><?php echo e($penarikan->mataKuliah->kode); ?></span>
                                    </div>
                                </td>
                                <td class="px-8 py-5">
                                    <span class="text-sm text-slate-600"><?php echo e($penarikan->created_at->diffForHumans()); ?></span>
                                </td>
                                <td class="px-8 py-5 text-right">
                                    <div class="flex items-center justify-end gap-2 relative" x-data="{ menuOpen: false }">
                                        <button @click="menuOpen = !menuOpen" class="p-2 rounded-xl border border-slate-200 text-slate-400 hover:text-navy transition-all">
                                            <i class="fas fa-ellipsis-h"></i>
                                        </button>
                                        <div x-show="menuOpen" @click.away="menuOpen = false" class="absolute right-0 top-12 w-48 bg-white rounded-xl border border-slate-100 shadow-xl z-50 p-2 space-y-1 text-left">
                                            <a href="<?php echo e(route('banksoal.arsip.dosen.penarikan.edit', $penarikan->id)); ?>" class="flex items-center gap-3 px-3 py-2 rounded-lg text-xs font-bold text-navy hover:bg-slate-50 transition-all">
                                                <i class="fas fa-file-export w-4"></i> Konversi
                                            </a>
                                            <form action="<?php echo e(route('banksoal.arsip.dosen.penarikan.destroy', $penarikan->id)); ?>" method="POST" onsubmit="if(confirm('Hapus riwayat penarikan ini?')){ window.showLoader(); return true; } else { return false; }" class="block">
                                                <?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?>
                                                <button type="submit" class="flex w-full items-center gap-3 px-3 py-2 rounded-lg text-xs font-bold text-rose-500 hover:bg-rose-50 transition-all">
                                                    <i class="fas fa-trash-alt w-4"></i> Hapus
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    </div>

    <script>
        document.getElementById('filterForm').addEventListener('submit', function() {
            window.showLoader();
        });
        document.querySelectorAll('.pagination a').forEach(link => {
            link.addEventListener('click', function() {
                window.showLoader();
            });
        });
    </script>
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
<?php /**PATH C:\WebsiteTekkom - Copy\Modules\BankSoal\resources\views\pages\arsip\Dosen\index.blade.php ENDPATH**/ ?>