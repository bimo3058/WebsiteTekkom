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
        <a href="#" class="hover:text-primary transition-colors text-gray-500">Ujian Komprehensif</a>
        <span class="mx-2 text-gray-300">/</span>
        <span class="text-gray-900 font-semibold">Riwayat Ujian</span>
    <?php $__env->stopSection(); ?>

    <div class="w-full">
        <!-- Page Header -->
        <div class="mb-6 flex flex-col sm:flex-row sm:items-start justify-between gap-4">
            <div>
                <h1 class="text-[22px] font-bold text-gray-900 tracking-tight">Riwayat Ujian Komprehensif</h1>
                <p class="text-[13px] text-gray-500 mt-0.5">Rekapitulasi nilai akhir mahasiswa yang telah menyelesaikan ujian.</p>
            </div>
        </div>

        
        <div
            x-data="{
                openFilter: false,
                pendingPeriode: '<?php echo e(request('periode_id')); ?>',
                pendingKeterangan: '<?php echo e(request('keterangan')); ?>',
                activeCount: <?php echo e((request('periode_id') ? 1 : 0) + (request('keterangan') ? 1 : 0)); ?>,
                applyFilters() {
                    const url = new URL(window.location.href);
                    if (this.pendingPeriode) url.searchParams.set('periode_id', this.pendingPeriode);
                    else url.searchParams.delete('periode_id');
                    if (this.pendingKeterangan) url.searchParams.set('keterangan', this.pendingKeterangan);
                    else url.searchParams.delete('keterangan');
                    url.searchParams.delete('page');
                    window.location.href = url.toString();
                },
                clearFilters() {
                    this.pendingPeriode = '';
                    this.pendingKeterangan = '';
                    const url = new URL(window.location.href);
                    url.searchParams.delete('periode_id');
                    url.searchParams.delete('keterangan');
                    url.searchParams.delete('page');
                    window.location.href = url.toString();
                }
            }"
            @mousedown.outside="openFilter = false"
        >

        
        <div class="bg-white border border-gray-200 rounded-xl shadow-sm mb-8 relative">
            
            <div class="p-4 sm:px-6 border-b border-gray-200 flex flex-col sm:flex-row sm:items-center justify-between gap-4 bg-white">
                <h2 class="text-[15px] font-semibold text-gray-900">Tabel Riwayat Ujian</h2>

                <!-- Filters & Search (Kanan) -->
                <div class="flex flex-col sm:flex-row items-center gap-3 w-full sm:w-auto">
                    <!-- Search -->
                    <form method="GET" action="<?php echo e(route('banksoal.admin.cbt.riwayat')); ?>" class="relative w-full sm:w-64 flex items-center">
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(request('periode_id')): ?><input type="hidden" name="periode_id" value="<?php echo e(request('periode_id')); ?>"><?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(request('keterangan')): ?><input type="hidden" name="keterangan" value="<?php echo e(request('keterangan')); ?>"><?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(request('per_page')): ?><input type="hidden" name="per_page" value="<?php echo e(request('per_page')); ?>"><?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                            <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                            </svg>
                        </div>
                        <input type="text" name="q" value="<?php echo e(request('q')); ?>"
                            placeholder="Search mahasiswa..."
                            class="w-full pl-10 pr-4 py-2 border border-gray-200 rounded-lg text-[13px] focus:outline-none focus:border-primary focus:ring-0 transition-all placeholder:text-gray-400 bg-white text-gray-700">
                    </form>

                    <!-- Filter Button -->
                    <div class="relative">
                        <button @click="openFilter = !openFilter" type="button"
                            class="relative inline-flex items-center gap-2 px-4 py-2 bg-white border rounded-lg text-[13px] font-medium transition-all"
                            :class="activeCount > 0 ? 'border-primary text-primary bg-primary/5' : 'border-gray-200 text-gray-600 hover:bg-gray-50 hover:text-gray-800'">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"></path>
                            </svg>
                            Filter
                            <span x-show="activeCount > 0"
                                class="inline-flex items-center justify-center w-4 h-4 text-[10px] font-bold bg-primary text-white rounded-full"
                                x-text="activeCount">
                            </span>
                        </button>

                        
                        <div x-show="openFilter"
                            x-transition:enter="transition ease-out duration-150"
                            x-transition:enter-start="opacity-0 scale-95 translate-y-1"
                            x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                            x-transition:leave="transition ease-in duration-100"
                            x-transition:leave-start="opacity-100 scale-100"
                            x-transition:leave-end="opacity-0 scale-95"
                            x-cloak
                            class="absolute right-0 mt-2 w-72 bg-white border border-gray-200 rounded-xl shadow-xl z-50 overflow-hidden">

                            
                            <div class="px-5 py-3.5 border-b border-gray-100 flex items-center justify-between">
                                <span class="text-[13px] font-semibold text-gray-800">Filter</span>
                                <button @click="openFilter = false" type="button" class="text-gray-400 hover:text-gray-600 transition-colors">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                </button>
                            </div>

                            
                            <div class="px-5 py-4 space-y-4">

                                <!-- Filter Periode Ujian -->
                                <div>
                                    <label class="block text-[12px] font-semibold text-gray-600 mb-1.5 uppercase tracking-wide">Periode Ujian</label>
                                    <select x-model="pendingPeriode"
                                        class="w-full px-3 py-2 border border-gray-200 rounded-lg text-[13px] focus:outline-none focus:border-primary transition-all cursor-pointer bg-white text-gray-700">
                                        <option value="">Semua Periode</option>
                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $periodes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $periode): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                                            <option value="<?php echo e($periode->id); ?>"><?php echo e($periode->nama_periode); ?></option>
                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                                    </select>
                                </div>

                                <!-- Filter Keterangan -->
                                <div>
                                    <label class="block text-[12px] font-semibold text-gray-600 mb-1.5 uppercase tracking-wide">Keterangan</label>
                                    <select x-model="pendingKeterangan"
                                        class="w-full px-3 py-2 border border-gray-200 rounded-lg text-[13px] focus:outline-none focus:border-primary transition-all cursor-pointer bg-white text-gray-700">
                                        <option value="">Semua Keterangan</option>
                                        <option value="lulus">Lulus</option>
                                        <option value="tidak_lulus">Tidak Lulus</option>
                                    </select>
                                </div>
                            </div>

                            
                            <div class="px-5 py-3.5 border-t border-gray-100 flex items-center justify-between bg-gray-50/50">
                                <button @click="clearFilters()" type="button"
                                    class="px-4 py-1.5 text-[13px] font-medium text-gray-600 hover:text-gray-800 rounded-lg hover:bg-gray-100 transition-colors">
                                    Clear
                                </button>
                                <button @click="applyFilters()" type="button"
                                    class="px-5 py-1.5 text-[13px] font-semibold bg-primary text-white rounded-lg hover:bg-[#1e2d60] transition-colors shadow-sm">
                                    Terapkan
                                </button>
                            </div>
                        </div>
                    </div>

                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(request()->hasAny(['q','periode_id','keterangan'])): ?>
                        <a href="<?php echo e(route('banksoal.admin.cbt.riwayat')); ?>"
                           class="inline-flex items-center justify-center px-4 py-2 border border-gray-200 text-gray-500 hover:bg-gray-50 hover:text-gray-700 text-[13px] font-medium rounded-lg transition-colors shrink-0">
                            Reset
                        </a>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div>
            </div>

            <!-- Table -->
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-gray-50 border-b border-gray-200 text-gray-500 text-[13px] tracking-wide font-semibold">
                            <th class="px-6 py-4 w-10 text-center">No.</th>
                            <th class="px-6 py-4 whitespace-nowrap">NIM</th>
                            <th class="px-6 py-4 whitespace-nowrap">Nama Mahasiswa</th>
                            <th class="px-6 py-4 whitespace-nowrap">Periode Ujian</th>
                            <th class="px-6 py-4 whitespace-nowrap text-center">Ujian Ke-</th>
                            <th class="px-6 py-4 whitespace-nowrap text-center">Total Poin</th>
                            <th class="px-6 py-4 whitespace-nowrap text-center">Keterangan</th>
                            <th class="px-6 py-4 whitespace-nowrap text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 text-[13px]">
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $sessions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $session): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                            <?php
                                $nim       = $session->user?->student?->student_number ?? '—';
                                $nama      = $session->user?->name ?? '—';
                                $poin      = $session->score;
                                $lulus     = $poin !== null && $poin >= 60;
                                $ujianKe   = $session->ujian_ke ?? '—';
                            ?>
                            <tr class="hover:bg-gray-50/50 transition-colors">
                                <td class="px-6 py-4 text-center text-gray-400">
                                    <?php echo e($sessions->firstItem() + $index); ?>

                                </td>
                                <td class="px-6 py-4 text-gray-700 font-mono whitespace-nowrap">
                                    <?php echo e($nim); ?>

                                </td>
                                <td class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap">
                                    <?php echo e($nama); ?>

                                </td>
                                <td class="px-6 py-4">
                                    <p class="text-[13px] text-gray-800 font-medium whitespace-nowrap">
                                        <?php echo e($session->jadwal?->periode?->nama_periode ?? '—'); ?>

                                    </p>
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($session->jadwal): ?>
                                        <p class="text-[11px] text-gray-400 mt-0.5 whitespace-nowrap">
                                            Sesi <?php echo e($session->jadwal->nama_sesi ?? '—'); ?>

                                        </p>
                                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <span class="text-[13px] font-medium text-gray-800">
                                        <?php echo e($ujianKe); ?>

                                    </span>
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($poin !== null): ?>
                                        <div style="display:flex; align-items:baseline; justify-content:center; gap:2px;">
                                            <span class="text-[14px] font-bold text-gray-900"><?php echo e((int)$poin); ?></span>
                                          
                                        </div>
                                    <?php else: ?>
                                        <span class="text-gray-400 italic">—</span>
                                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($poin !== null): ?>
                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($lulus): ?>
                                            <span style="display:inline-flex; align-items:center; gap:5px; padding:3px 8px 3px 6px; border-radius:9999px; font-size:10px; font-weight:600; text-transform:uppercase; letter-spacing:0.04em; background:#ECFDF5; color:#059669;">
                                                <span style="width:6px; height:6px; border-radius:50%; background:#059669;"></span>
                                                LULUS
                                            </span>
                                        <?php else: ?>
                                            <span style="display:inline-flex; align-items:center; gap:5px; padding:3px 8px 3px 6px; border-radius:9999px; font-size:10px; font-weight:600; text-transform:uppercase; letter-spacing:0.04em; background:#FEF2F2; color:#DC2626;">
                                                <span style="width:6px; height:6px; border-radius:50%; background:#DC2626;"></span>
                                                TIDAK LULUS
                                            </span>
                                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                    <?php else: ?>
                                        <span class="text-gray-400 italic">Belum dikalkulasi</span>
                                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <a href="<?php echo e(route('banksoal.admin.cbt.detail', $session->id)); ?>"
                                       class="inline-flex items-center justify-center bg-white border border-gray-200 text-gray-600 hover:text-primary hover:border-primary px-3 py-1.5 rounded-lg text-xs font-semibold transition-colors shadow-sm whitespace-nowrap">
                                        Lihat Detail
                                    </a>
                                </td>
                            </tr>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                            <tr>
                                <td colspan="7" class="px-6 py-20 text-center">
                                    <div class="flex flex-col items-center justify-center">
                                        <div class="w-14 h-14 bg-gray-50 rounded-2xl flex items-center justify-center mb-4 border border-gray-100 shadow-sm">
                                            <svg class="w-7 h-7 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                                      d="M9 5H7a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V7a2 2 0 0 0-2-2h-2M9 5a2 2 0 0 0 2 2h2a2 2 0 0 0 2-2M9 5a2 2 0 0 1 2-2h2a2 2 0 0 1 2 2"/>
                                            </svg>
                                        </div>
                                        <p class="text-gray-500 text-[13px] font-medium">Belum ada riwayat ujian.</p>
                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(request()->hasAny(['q','periode_id','keterangan'])): ?>
                                            <p class="text-gray-400 text-[12px] mt-1">Coba ubah kata kunci atau filter pencarian.</p>
                                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                    </div>
                                </td>
                            </tr>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </tbody>
                </table>
            </div>

            <!-- Footer Pagination / Info -->
            <div class="px-6 py-4 border-t border-gray-200 bg-white flex flex-col sm:flex-row items-center justify-between gap-4">
                <div class="flex flex-col sm:flex-row sm:items-center gap-4 w-full">
                    <div class="flex items-center gap-2">
                        <span class="text-[13px] text-gray-700 font-medium whitespace-nowrap">Per page</span>
                        <div class="relative">
                            <select onchange="const url = new URL(window.location.href); url.searchParams.set('per_page', this.value); url.searchParams.delete('page'); window.location.href = url.toString();" class="pl-3 pr-8 py-1.5 bg-white border border-gray-300 rounded-lg text-[13px] text-gray-700 font-medium focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all cursor-pointer outline-none disabled:bg-gray-50 disabled:cursor-not-allowed">
                                <option value="5"  <?php echo e(request('per_page', 5) == 5  ? 'selected' : ''); ?>>5</option>
                                <option value="10" <?php echo e(request('per_page', 5) == 10 ? 'selected' : ''); ?>>10</option>
                                <option value="15" <?php echo e(request('per_page', 5) == 15 ? 'selected' : ''); ?>>15</option>
                                <option value="25" <?php echo e(request('per_page', 5) == 25 ? 'selected' : ''); ?>>25</option>
                                <option value="50" <?php echo e(request('per_page', 5) == 50 ? 'selected' : ''); ?>>50</option>
                            </select>
                        </div>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($sessions instanceof \Illuminate\Pagination\LengthAwarePaginator): ?>
                        <span class="text-[13px] text-gray-600 font-medium ml-2">
                            Showing <?php echo e($sessions->firstItem() ?? 0); ?> to <?php echo e($sessions->lastItem() ?? 0); ?> of <?php echo e($sessions->total()); ?> results
                        </span>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </div>
                    
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($sessions instanceof \Illuminate\Pagination\LengthAwarePaginator && $sessions->hasPages()): ?>
                    <div class="sm:ml-auto">
                        <?php echo e($sessions->links('pagination::tailwind')); ?>

                    </div>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div>
            </div>
        </div>

        
        
        
        <div id="reset-section" x-data="{ showResetModal: false }" class="hidden mt-4 mb-8">
            <div class="border border-rose-200 bg-rose-50 rounded-2xl p-6 shadow-sm">
                <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
                    <div class="flex items-start gap-4">
                        <div class="w-10 h-10 bg-white border border-rose-100 rounded-xl flex items-center justify-center flex-shrink-0 mt-0.5 shadow-sm">
                            <svg class="w-5 h-5 text-rose-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-[15px] font-bold text-rose-800">Reset Semua Data Ujian</h3>
                            <p class="text-[13px] text-rose-600 mt-1">Menghapus <strong>seluruh</strong> data periode, jadwal, pendaftar, sesi ujian, jawaban, dan log pelanggaran secara permanen. Aksi ini <strong>tidak dapat dibatalkan</strong>.</p>
                        </div>
                    </div>
                    <button @click="showResetModal = true" type="button"
                        class="flex-shrink-0 inline-flex items-center gap-2 px-5 py-2 bg-rose-600 hover:bg-rose-700 text-white text-[13px] font-semibold rounded-lg transition-colors shadow-sm">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0 1 16.138 21H7.862a2 2 0 0 1-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 0 0-1-1h-4a1 1 0 0 0-1 1v3M4 7h16"/>
                        </svg>
                        Reset Semua Data
                    </button>
                </div>

                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($errors->has('konfirmasi_password')): ?>
                    <div class="mt-4 p-3 bg-white border border-rose-200 rounded-xl text-[13px] text-rose-700 font-medium flex items-center gap-2 shadow-sm">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                        </svg>
                        <?php echo e($errors->first('konfirmasi_password')); ?>

                    </div>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </div>

            
            <div x-show="showResetModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4">
                <div class="absolute inset-0 bg-gray-900/50 backdrop-blur-sm" @click="showResetModal = false"></div>
                <div x-show="showResetModal"
                    x-transition:enter="ease-out duration-200" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
                    x-transition:leave="ease-in duration-150" x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95"
                    class="relative w-full max-w-md bg-white rounded-2xl shadow-2xl overflow-hidden border border-gray-100">

                    <div class="bg-rose-600 px-6 py-5 flex items-center gap-3">
                        <div class="w-9 h-9 bg-white/20 rounded-xl flex items-center justify-center">
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-[15px] font-bold text-white">Konfirmasi Reset Data</h3>
                            <p class="text-rose-200 text-[12px] mt-0.5">Aksi ini permanen dan tidak dapat dibatalkan</p>
                        </div>
                    </div>

                    <form action="<?php echo e(route('banksoal.admin.cbt.reset-semua')); ?>" method="POST" class="p-6 space-y-5">
                        <?php echo csrf_field(); ?>
                        <div class="bg-amber-50 border border-amber-200 rounded-xl p-4 text-[13px] text-amber-800">
                            <p class="font-bold mb-2">Data yang akan dihapus permanen:</p>
                            <ul class="list-disc list-inside space-y-1 text-amber-700">
                                <li>Semua Periode Ujian</li>
                                <li>Semua Jadwal & Token Sesi</li>
                                <li>Semua Data Pendaftar</li>
                                <li>Semua Sesi & Jawaban Ujian Mahasiswa</li>
                                <li>Semua Log Pelanggaran (Cheat Logs)</li>
                            </ul>
                        </div>
                        <div class="space-y-2">
                            <label class="block text-[13px] font-semibold text-gray-700">Masukkan password akun Anda untuk konfirmasi:</label>
                            <input type="password" name="konfirmasi_password" required
                                placeholder="Password Anda"
                                class="w-full px-4 py-2 bg-white border border-gray-300 rounded-lg text-[13px] focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all placeholder-gray-400">
                        </div>
                        <div class="flex gap-3 pt-2">
                            <button type="button" @click="showResetModal = false"
                                class="flex-1 px-4 py-2.5 border border-gray-200 text-gray-700 font-semibold rounded-xl hover:bg-gray-50 transition-colors text-[13px]">
                                Batal
                            </button>
                            <button type="submit"
                                class="flex-1 px-4 py-2.5 bg-rose-600 hover:bg-rose-700 text-white font-semibold rounded-xl transition-colors text-[13px] shadow-sm">
                                Ya, Reset Sekarang
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

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

<?php /**PATH C:\WebsiteTekkom - Copy\Modules\BankSoal\resources\views\admin\cbt\riwayat.blade.php ENDPATH**/ ?>