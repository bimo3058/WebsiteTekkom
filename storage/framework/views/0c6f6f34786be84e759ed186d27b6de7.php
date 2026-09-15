<?php if (isset($component)) { $__componentOriginal327816c8748951109e999046d52cab34 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal327816c8748951109e999046d52cab34 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'eoffice::components.layouts.dosen','data' => ['title' => 'Penilaian Mahasiswa']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('eoffice::layouts.dosen'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => 'Penilaian Mahasiswa']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

    <?php $__env->startSection('breadcrumbs'); ?>
        <span class="text-[#272835] font-semibold" style="font-family:'Inter Tight',sans-serif;">Penilaian Mahasiswa</span>
    <?php $__env->stopSection(); ?>

    <?php $__env->startPush('styles'); ?>
        <style>
            [x-cloak] { display: none !important; }
        </style>
    <?php $__env->stopPush(); ?>

    <div x-data="{ searchQuery: '' }" class="flex flex-col max-w-7xl mx-auto">
        <!-- Page Header -->
        <div class="flex flex-col sm:flex-row sm:items-end justify-between gap-5 mb-8">
            <div>
                <h1 class="text-3xl font-extrabold text-slate-900 tracking-tight mb-2">Penilaian Mahasiswa</h1>
                <p class="text-sm text-slate-500 max-w-2xl leading-relaxed">
                    Daftar mahasiswa bimbingan yang telah menyelesaikan proses Kerja Praktik dan siap dinilai berdasarkan rubrik penilaian Dosen Pembimbing.
                </p>
            </div>
        </div>

        <!-- Search Bar -->
        <div class="bg-white p-4 rounded-2xl border border-slate-200 shadow-sm mb-6 flex flex-col lg:flex-row gap-4 items-center justify-between">
            <div class="relative w-full lg:w-96">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <svg class="h-4 w-4 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                </div>
                <input type="text" x-model="searchQuery" placeholder="Cari nama mahasiswa atau NIM..."
                    class="block w-full pl-10 pr-3 py-2 border border-slate-200 rounded-xl text-sm bg-slate-50 focus:bg-white focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition-colors outline-none">
            </div>
        </div>

        <!-- Tabel Utama -->
        <div class="bg-white rounded-2xl border border-slate-200 shadow-[0_4px_24px_rgba(0,0,0,0.02)] overflow-hidden relative">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-slate-50/50">
                            <th class="py-4 px-6 text-[11px] font-bold text-slate-400 uppercase tracking-wider border-b border-slate-100">
                                Mahasiswa
                            </th>
                            <th class="py-4 px-6 text-[11px] font-bold text-slate-400 uppercase tracking-wider border-b border-slate-100">
                                Informasi KP
                            </th>
                            <th class="py-4 px-6 text-[11px] font-bold text-slate-400 uppercase tracking-wider border-b border-slate-100 text-center">
                                Nilai Laporan (Otomatis)
                            </th>
                            <th class="py-4 px-6 text-[11px] font-bold text-slate-400 uppercase tracking-wider border-b border-slate-100 text-center">
                                Nilai Seminar (Manual/Rubrik)
                            </th>
                            <th class="py-4 px-6 text-[11px] font-bold text-slate-400 uppercase tracking-wider border-b border-slate-100 w-32 text-center">
                                Aksi
                            </th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $mahasiswas; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $mhs): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                            <tr class="hover:bg-slate-50/80 transition-colors duration-200 group"
                                x-show="searchQuery === '' || '<?php echo e(strtolower($mhs->nama)); ?>'.includes(searchQuery.toLowerCase()) || '<?php echo e(strtolower($mhs->nim)); ?>'.includes(searchQuery.toLowerCase())">

                                <!-- Mahasiswa -->
                                <td class="py-4 px-6">
                                    <div class="flex items-center gap-4">
                                        <div class="w-10 h-10 rounded-full bg-indigo-100 text-indigo-600 flex items-center justify-center font-bold text-sm shrink-0 border border-indigo-200">
                                            <span><?php echo e(substr($mhs->nama, 0, 1)); ?></span>
                                        </div>
                                        <div>
                                            <p class="text-sm font-bold text-slate-900 group-hover:text-indigo-700 transition-colors"><?php echo e($mhs->nama); ?></p>
                                            <p class="text-xs text-slate-500 mt-0.5 font-mono"><?php echo e($mhs->nim); ?></p>
                                        </div>
                                    </div>
                                </td>

                                <!-- Informasi KP -->
                                <td class="py-4 px-6">
                                    <div class="max-w-xs">
                                        <p class="text-sm font-semibold text-slate-800 truncate" title="<?php echo e($mhs->judul_kp); ?>"><?php echo e($mhs->judul_kp ?? '-'); ?></p>
                                        <div class="flex items-center gap-1.5 mt-1 text-xs text-slate-500 truncate">
                                            <svg class="w-3.5 h-3.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2-2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                                            </svg>
                                            <span class="truncate"><?php echo e($mhs->tempat_kp ?? 'Perusahaan Belum Ditentukan'); ?></span>
                                        </div>
                                    </div>
                                </td>

                                <!-- Nilai Laporan (Koordinator/Otomatis) -->
                                <td class="py-4 px-6 text-center align-middle">
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($mhs->nilai_laporan !== null): ?>
                                        <span class="inline-flex items-center justify-center w-10 h-10 rounded-xl bg-blue-50 text-blue-700 font-extrabold text-lg border border-blue-100 shadow-sm">
                                            <?php echo e($mhs->nilai_laporan); ?>

                                        </span>
                                    <?php else: ?>
                                        <span class="text-xs font-medium text-slate-400 italic">Belum</span>
                                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                </td>

                                <!-- Nilai Seminar (Manual/Rubrik) -->
                                <td class="py-4 px-6 text-center align-middle">
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($mhs->nilai_seminar !== null): ?>
                                        <span class="inline-flex items-center justify-center w-10 h-10 rounded-xl bg-emerald-50 text-emerald-700 font-extrabold text-lg border border-emerald-100 shadow-sm">
                                            <?php echo e($mhs->nilai_seminar); ?>

                                        </span>
                                    <?php else: ?>
                                        <span class="text-xs font-medium text-slate-400 italic">Belum Dinilai</span>
                                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                </td>

                                <!-- Aksi -->
                                <td class="py-4 px-6 text-center">
                                    <a href="<?php echo e(route('eoffice.kp.dosen.bimbingan.penilaian', $mhs->id)); ?>"
                                        class="px-4 py-2 text-sm font-bold rounded-xl transition-all flex items-center justify-center gap-2 w-full
                                        <?php if($mhs->nilai_seminar !== null): ?>
                                            bg-slate-100 text-slate-600 hover:bg-slate-200 border-slate-200
                                        <?php else: ?>
                                            bg-indigo-50 text-indigo-600 hover:bg-indigo-600 hover:text-white border border-indigo-100 hover:border-transparent shadow-sm hover:shadow-md hover:shadow-indigo-500/20
                                        <?php endif; ?>
                                        ">
                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($mhs->nilai_seminar !== null): ?>
                                            Lihat / Edit Nilai
                                        <?php else: ?>
                                            Beri Penilaian
                                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                    </a>
                                </td>
                            </tr>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                            <!-- Empty State -->
                            <tr>
                                <td colspan="5" class="py-16 text-center">
                                    <div class="w-20 h-20 bg-slate-50 rounded-full flex items-center justify-center mx-auto mb-4 border border-slate-100">
                                        <svg class="w-10 h-10 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                                        </svg>
                                    </div>
                                    <h3 class="text-lg font-bold text-slate-900 mb-1">Tidak Ada Data Mahasiswa</h3>
                                    <p class="text-sm text-slate-500">Saat ini tidak ada mahasiswa yang sedang melaksanakan atau menyelesaikan KP.</p>
                                </td>
                            </tr>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </tbody>
                </table>
            </div>

            <!-- Pagination Footer -->
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(count($mahasiswas) > 0): ?>
                <div class="bg-slate-50 px-6 py-4 border-t border-slate-100 flex items-center justify-between">
                    <p class="text-sm text-slate-500">Menampilkan <span class="font-bold text-slate-900"><?php echo e(count($mahasiswas)); ?></span> mahasiswa bimbingan</p>
                    <!-- Static Pagination for Demo -->
                    <div class="flex gap-1" x-show="searchQuery === ''">
                        <button class="w-8 h-8 flex items-center justify-center rounded-lg border border-slate-200 bg-white text-slate-400 cursor-not-allowed">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                            </svg>
                        </button>
                        <button class="w-8 h-8 flex items-center justify-center rounded-lg border border-indigo-600 bg-indigo-600 text-white font-medium text-xs">1</button>
                        <button class="w-8 h-8 flex items-center justify-center rounded-lg border border-slate-200 bg-white text-slate-400 cursor-not-allowed">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                            </svg>
                        </button>
                    </div>
                </div>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </div>
    </div>
 <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal327816c8748951109e999046d52cab34)): ?>
<?php $attributes = $__attributesOriginal327816c8748951109e999046d52cab34; ?>
<?php unset($__attributesOriginal327816c8748951109e999046d52cab34); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal327816c8748951109e999046d52cab34)): ?>
<?php $component = $__componentOriginal327816c8748951109e999046d52cab34; ?>
<?php unset($__componentOriginal327816c8748951109e999046d52cab34); ?>
<?php endif; ?><?php /**PATH C:\WebsiteTekkom - Copy\Modules\EOffice\resources\views\dosen\penilaian_index.blade.php ENDPATH**/ ?>