<?php if (isset($component)) { $__componentOriginal3e4fe3b26585e820af7a1b481c38fe80 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal3e4fe3b26585e820af7a1b481c38fe80 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'banksoal::components.layouts.admin','data' => []] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('banksoal::layouts.admin'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

    <?php $__env->startSection('breadcrumbs'); ?>
    <a href="#" class="text-slate-500 hover:text-primary transition-colors">Bank Soal</a>
    <span class="mx-2 text-slate-300">/</span>
    <span class="text-slate-800 font-semibold">Manajemen Soal</span>
    <?php $__env->stopSection(); ?>

    <!-- Header Title -->
    <div class="mb-6 lg:mb-8 flex justify-between items-center">
        <div>
            <h1 class="text-2xl lg:text-3xl font-bold text-slate-800 tracking-tight">Cetak Soal Ujian (Offline)</h1>
            <p class="text-slate-500 text-sm mt-2">Daftar permintaan pencetakan lembar soal fisik dari Dosen</p>
        </div>
    </div>

    <!-- Filter Form -->
    <form action="<?php echo e(route('banksoal.admin.kontrol-banksoal.soal')); ?>" method="GET" class="bg-white rounded-2xl border border-slate-200 p-4 mb-6 flex flex-col gap-3 lg:flex-row lg:items-center lg:justify-between" id="filterForm" onsubmit="window.showLoader();">
        <div class="relative flex-1">
            <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                </svg>
            </div>
            <input type="text" name="searchSoal" value="<?php echo e(request('searchSoal')); ?>" placeholder="Cari agenda atau mata kuliah..." class="w-full pl-10 pr-4 py-2.5 bg-white border border-slate-200 rounded-lg text-sm focus:outline-none focus:ring-4 focus:ring-primary/5 focus:border-primary transition-all outline-none">
        </div>

        <div class="flex items-center gap-3">
            <?php if (isset($component)) { $__componentOriginal3b959de169ce576b87a8cfe77cf2c842 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal3b959de169ce576b87a8cfe77cf2c842 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'banksoal::components.ui.filter-panel','data' => ['formId' => 'filterForm','hasActiveFilter' => request('filterStatus') ? true : false,'resetRoute' => ''.e(route('banksoal.admin.kontrol-banksoal.soal')).'','applyLabel' => 'Terapkan']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('banksoal::ui.filter-panel'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['formId' => 'filterForm','hasActiveFilter' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(request('filterStatus') ? true : false),'resetRoute' => ''.e(route('banksoal.admin.kontrol-banksoal.soal')).'','applyLabel' => 'Terapkan']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

                <div>
                    <label class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-3 block">Status Cetak</label>
                    <div class="space-y-2">
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = [
                            'pending' => '🕒 Menunggu Dicetak',
                            'diproses' => '⏳ Sedang Diproses',
                            'selesai' => '✅ Sudah Dicetak'
                        ]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $val => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                        <label class="flex items-center gap-3 p-2 rounded-lg hover:bg-slate-50 cursor-pointer group">
                            <input type="radio" name="filterStatus" value="<?php echo e($val); ?>" <?php if(request('filterStatus') == $val): echo 'checked'; endif; ?> class="w-4 h-4 rounded-full border-slate-300 text-primary focus:ring-primary transition-all">
                            <span class="text-sm text-slate-700 group-hover:text-primary transition-colors"><?php echo e($label); ?></span>
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
        </div>
    </form>

    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(session('success')): ?>
        <div class="mb-5 bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-xl relative flex items-center gap-3">
            <i class="fas fa-check-circle text-green-500"></i>
            <span class="text-sm font-medium"><?php echo e(session('success')); ?></span>
        </div>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(session('error')): ?>
        <div class="mb-5 bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-xl relative flex items-center gap-3">
            <i class="fas fa-exclamation-circle text-red-500"></i>
            <span class="text-sm font-medium"><?php echo e(session('error')); ?></span>
        </div>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

    <!-- Table -->
    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm text-slate-600">
                <thead class="bg-slate-50 text-slate-900 border-b border-slate-200">
                    <tr>
                        <th class="px-6 py-4 font-semibold">Tgl / Waktu</th>
                        <th class="px-6 py-4 font-semibold">Agenda Ujian</th>
                        <th class="px-6 py-4 font-semibold">Mata Kuliah / Dosen</th>
                        <th class="px-6 py-4 font-semibold text-center">Metode</th>
                        <th class="px-6 py-4 font-semibold text-center">Status Cetak</th>
                        <th class="px-6 py-4 font-semibold text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $antreanCetak; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                    <tr class="hover:bg-slate-50/50 transition-colors">
                        <td class="px-6 py-4 whitespace-nowrap text-xs font-medium text-slate-500">
                            <?php echo e($item->created_at->format('d M Y')); ?><br>
                            <span class="text-[10px]"><?php echo e($item->created_at->format('H:i')); ?> WIB</span>
                        </td>
                        <td class="px-6 py-4 font-semibold text-slate-700">
                            <?php echo e($item->nama_ekstraksi); ?><br>
                            <span class="text-xs text-slate-500 font-normal">TA: <?php echo e($item->tahun_akademik); ?> - Sem <?php echo e(ucfirst($item->semester)); ?></span>
                        </td>
                        <td class="px-6 py-4 font-medium text-slate-800 text-xs leading-relaxed">
                            <span class="font-bold text-[#0f172a]"><?php echo e($item->mataKuliah ? $item->mataKuliah->nama : 'MK Tidak Ditemukan'); ?></span><br>
                            <span class="text-slate-500 italic"><?php echo e($item->dosen ? $item->dosen->name : 'N/A'); ?></span>
                        </td>
                        <td class="px-6 py-4 text-center">
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(strtolower($item->metode_ujian) == 'offline'): ?>
                                <span class="bg-blue-100 text-blue-800 text-xs font-semibold px-2.5 py-1 rounded-full border border-blue-200 whitespace-nowrap">Kertas (Offline)</span>
                            <?php else: ?>
                                <span class="bg-purple-100 text-purple-800 text-xs font-semibold px-2.5 py-1 rounded-full border border-purple-200 whitespace-nowrap">Online</span>
                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </td>
                        <td class="px-6 py-4 text-center">
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($item->status_cetak == 'pending'): ?>
                                <span class="bg-amber-100 text-amber-700 text-[11px] font-bold px-3 py-1.5 rounded-lg border border-amber-200 uppercase tracking-wider"><i class="fas fa-clock mr-1"></i> Menunggu</span>
                            <?php elseif($item->status_cetak == 'diproses'): ?>
                                <span class="bg-sky-100 text-sky-700 text-[11px] font-bold px-3 py-1.5 rounded-lg border border-sky-200 uppercase tracking-wider"><i class="fas fa-spinner fa-spin mr-1"></i> Diproses</span>
                            <?php elseif($item->status_cetak == 'selesai'): ?>
                                <span class="bg-emerald-100 text-emerald-700 text-[11px] font-bold px-3 py-1.5 rounded-lg border border-emerald-200 uppercase tracking-wider"><i class="fas fa-check mr-1"></i> Selesai</span>
                            <?php else: ?>
                                <span class="bg-slate-100 text-slate-700 text-[11px] font-bold px-3 py-1.5 rounded-lg border border-slate-200 uppercase tracking-wider"><?php echo e($item->status_cetak); ?></span>
                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex items-center justify-center gap-2">
                                <a href="<?php echo e(route('banksoal.admin.kontrol-banksoal.soal.cetak', $item->id)); ?>" target="_blank" class="inline-flex items-center gap-1.5 bg-slate-800 hover:bg-slate-700 text-white rounded-lg px-3 py-1.5 text-xs font-semibold transition" title="Print Dokumen PDF">
                                    <i class="fas fa-print"></i> Cetak
                                </a>
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($item->status_cetak != 'selesai'): ?>
                                <form action="<?php echo e(route('banksoal.admin.kontrol-banksoal.soal.tandai-selesai', $item->id)); ?>" method="POST" onsubmit="if(confirm('Tandai bahwa berkas fisik soal ini sudah tercetak dan siap dibagikan?')) { window.showLoader(); return true; } else { return false; }">
                                    <?php echo csrf_field(); ?>
                                    <button type="submit" class="inline-flex items-center gap-1.5 bg-[#059669] hover:bg-[#047857] text-white rounded-lg px-3 py-1.5 text-xs font-semibold transition shadow-sm" title="Tandai Selesai Dicetak">
                                        <i class="fas fa-check-double"></i> Selesai
                                    </button>
                                </form>
                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            </div>
                        </td>
                    </tr>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                    <tr>
                        <td colspan="6" class="px-6 py-16 text-center text-slate-400">
                            <i class="fas fa-inbox text-4xl mb-3 text-slate-300"></i>
                            <p class="font-medium text-slate-500">Belum ada antrean cetak ujian fisik dari Dosen saat ini.</p>
                        </td>
                    </tr>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </tbody>
            </table>
        </div>
        <div class="px-6 py-4 border-t border-slate-200 bg-slate-50/80">
            <?php echo e($antreanCetak->links('banksoal::components.ui.laravel-pagination')); ?>

        </div>
    </div>
 <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal3e4fe3b26585e820af7a1b481c38fe80)): ?>
<?php $attributes = $__attributesOriginal3e4fe3b26585e820af7a1b481c38fe80; ?>
<?php unset($__attributesOriginal3e4fe3b26585e820af7a1b481c38fe80); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal3e4fe3b26585e820af7a1b481c38fe80)): ?>
<?php $component = $__componentOriginal3e4fe3b26585e820af7a1b481c38fe80; ?>
<?php unset($__componentOriginal3e4fe3b26585e820af7a1b481c38fe80); ?>
<?php endif; ?>
<?php /**PATH C:\WebsiteTekkom - Copy\Modules\BankSoal\resources\views\pages\admin\kontrol-banksoal\soal.blade.php ENDPATH**/ ?>