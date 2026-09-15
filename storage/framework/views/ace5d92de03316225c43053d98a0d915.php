<?php if (isset($component)) { $__componentOriginal327816c8748951109e999046d52cab34 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal327816c8748951109e999046d52cab34 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'eoffice::components.layouts.dosen','data' => ['title' => 'Penilaian Laporan']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('eoffice::layouts.dosen'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => 'Penilaian Laporan']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

    <?php $__env->startSection('breadcrumbs'); ?>
        <span class="text-[#272835] font-semibold" style="font-family:'Inter Tight',sans-serif;">Penilaian Laporan</span>
    <?php $__env->stopSection(); ?>

                <!-- Flash Messages -->
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(session('success')): ?>
                    <div
                        class="mb-6 bg-emerald-50 border border-emerald-200 text-emerald-700 px-4 py-3 rounded-xl flex items-center gap-3">
                        <svg class="w-5 h-5 text-emerald-500 flex-shrink-0" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <p class="font-medium text-sm"><?php echo e(session('success')); ?></p>
                    </div>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(session('error')): ?>
                    <div
                        class="mb-6 bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-xl flex items-center gap-3">
                        <svg class="w-5 h-5 text-red-500 flex-shrink-0" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <p class="font-medium text-sm"><?php echo e(session('error')); ?></p>
                    </div>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                <!-- Page Header -->
                <div class="mb-6">
                    <h1 class="text-2xl font-bold text-slate-900">Penilaian Laporan</h1>
                    <p class="text-sm text-slate-500 mt-1">Tinjau dan setujui laporan serta makalah KP mahasiswa
                        bimbingan Anda.</p>
                </div>

                <!-- Stats Cards -->
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
                    <div class="bg-white rounded-xl border border-slate-200 p-4 shadow-sm">
                        <p class="text-xs font-medium text-slate-500">Total Dokumen</p>
                        <p class="text-2xl font-bold text-slate-900 mt-1"><?php echo e($stats['total']); ?></p>
                    </div>
                    <div class="bg-amber-50 rounded-xl border border-amber-200 p-4 shadow-sm">
                        <p class="text-xs font-medium text-amber-600">Menunggu Review</p>
                        <p class="text-2xl font-bold text-amber-700 mt-1"><?php echo e($stats['pending']); ?></p>
                    </div>
                    <div class="bg-emerald-50 rounded-xl border border-emerald-200 p-4 shadow-sm">
                        <p class="text-xs font-medium text-emerald-600">Disetujui (ACC)</p>
                        <p class="text-2xl font-bold text-emerald-700 mt-1"><?php echo e($stats['approved']); ?></p>
                    </div>
                    <div class="bg-red-50 rounded-xl border border-red-200 p-4 shadow-sm">
                        <p class="text-xs font-medium text-red-600">Perlu Revisi</p>
                        <p class="text-2xl font-bold text-red-700 mt-1"><?php echo e($stats['rejected']); ?></p>
                    </div>
                </div>

                <!-- Table -->
                <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
                    <div
                        class="px-5 py-4 border-b border-slate-200 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3">
                        <h2 class="text-base font-semibold text-slate-800">Daftar Dokumen Masuk</h2>
                        <div class="flex items-center gap-2 w-full sm:w-auto">
                            <div class="relative w-full sm:w-56">
                                <input type="text" id="searchInput" placeholder="Cari mahasiswa..."
                                    class="w-full pl-9 pr-3 py-2 border border-slate-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                                    oninput="filterTable()">
                                <svg class="absolute left-3 top-2.5 h-4 w-4 text-slate-400" fill="none"
                                    viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                </svg>
                            </div>
                        </div>
                    </div>

                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($dokumens->isEmpty()): ?>
                        <div class="p-12 text-center">
                            <svg class="mx-auto h-12 w-12 text-slate-300 mb-4" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                    d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                            <p class="text-sm font-medium text-slate-900">Belum ada dokumen yang diunggah</p>
                            <p class="text-xs text-slate-500 mt-1">Mahasiswa bimbingan Anda belum mengunggah Laporan atau
                                Makalah KP.</p>
                        </div>
                    <?php else: ?>
                        <!-- Desktop Table -->
                        <div class="hidden md:block overflow-x-auto">
                            <table class="min-w-full divide-y divide-slate-200" id="dokumenTable">
                                <thead class="bg-slate-50">
                                    <tr>
                                        <th
                                            class="px-5 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">
                                            Mahasiswa</th>
                                        <th
                                            class="px-5 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">
                                            Jenis Dokumen</th>
                                        <th
                                            class="px-5 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">
                                            File</th>
                                        <th
                                            class="px-5 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">
                                            Tgl Upload</th>
                                        <th
                                            class="px-5 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">
                                            Status</th>
                                        <th
                                            class="px-5 py-3 text-right text-xs font-semibold text-slate-500 uppercase tracking-wider">
                                            Aksi</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-slate-100">
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $dokumens; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $dok): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                                        <tr class="hover:bg-slate-50 transition-colors dok-row"
                                            data-name="<?php echo e(strtolower($dok->nama_mahasiswa ?? '')); ?> <?php echo e(strtolower($dok->nim ?? '')); ?>">
                                            <td class="px-5 py-4 whitespace-nowrap">
                                                <div class="flex items-center gap-3">
                                                    <div
                                                        class="h-8 w-8 rounded bg-indigo-100 flex items-center justify-center text-indigo-700 font-bold text-xs">
                                                        <?php echo e(strtoupper(substr($dok->nama_mahasiswa ?? 'M', 0, 2))); ?>

                                                    </div>
                                                    <div>
                                                        <p class="text-sm font-medium text-slate-900">
                                                            <?php echo e($dok->nama_mahasiswa ?? 'Mahasiswa'); ?></p>
                                                        <p class="text-xs text-slate-500"><?php echo e($dok->nim); ?></p>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="px-5 py-4 whitespace-nowrap">
                                                <span
                                                    class="inline-flex items-center px-2.5 py-1 rounded-md text-xs font-semibold
                                                <?php echo e($dok->jenis_dokumen == 'Laporan' ? 'bg-blue-100 text-blue-800' : 'bg-purple-100 text-purple-800'); ?>">
                                                    <?php echo e($dok->jenis_dokumen); ?>

                                                </span>
                                            </td>
                                            <td class="px-5 py-4">
                                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($dok->file_path): ?>
                                                    <a href="<?php echo e($dok->file_url); ?>" target="_blank"
                                                        class="text-xs text-blue-600 hover:text-blue-800 underline font-medium flex items-center gap-1">
                                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor"
                                                            viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                                d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                                        </svg>
                                                        <?php echo e(basename($dok->file_path)); ?>

                                                    </a>
                                                <?php else: ?>
                                                    <span class="text-xs text-slate-400">—</span>
                                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                            </td>
                                            <td class="px-5 py-4 whitespace-nowrap text-xs text-slate-500">
                                                <?php echo e($dok->tanggal_upload ? \Carbon\Carbon::parse($dok->tanggal_upload)->format('d M Y') : '—'); ?>

                                            </td>
                                            <td class="px-5 py-4 whitespace-nowrap">
                                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($dok->approval_status == 'pending'): ?>
                                                    <span
                                                        class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-amber-100 text-amber-800">
                                                        <span class="w-1.5 h-1.5 rounded-full bg-amber-400 mr-1.5"></span>Menunggu
                                                        Review
                                                    </span>
                                                <?php elseif($dok->approval_status == 'approved'): ?>
                                                    <span
                                                        class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-emerald-100 text-emerald-800">
                                                        <span
                                                            class="w-1.5 h-1.5 rounded-full bg-emerald-400 mr-1.5"></span>Disetujui
                                                    </span>
                                                <?php elseif($dok->approval_status == 'rejected'): ?>
                                                    <span
                                                        class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-red-100 text-red-800">
                                                        <span class="w-1.5 h-1.5 rounded-full bg-red-400 mr-1.5"></span>Perlu Revisi
                                                    </span>
                                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                            </td>
                                            <td class="px-5 py-4 whitespace-nowrap text-right">
                                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($dok->approval_status == 'pending'): ?>
                                                    <div class="flex items-center justify-end gap-2">
                                                        <form
                                                            action="<?php echo e(route('eoffice.kp.dosen.bimbingan.dokumen.reject', [$dok->kp_id, $dok->id])); ?>"
                                                            method="POST">
                                                            <?php echo csrf_field(); ?>
                                                            <button type="submit"
                                                                class="px-3 py-1.5 text-xs font-medium text-red-600 bg-red-50 hover:bg-red-100 border border-red-200 rounded-lg transition-colors">
                                                                Revisi
                                                            </button>
                                                        </form>
                                                        <form
                                                            action="<?php echo e(route('eoffice.kp.dosen.bimbingan.dokumen.approve', [$dok->kp_id, $dok->id])); ?>"
                                                            method="POST">
                                                            <?php echo csrf_field(); ?>
                                                            <button type="submit"
                                                                class="px-3 py-1.5 text-xs font-medium text-white bg-emerald-600 hover:bg-emerald-700 rounded-lg transition-colors flex items-center gap-1">
                                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor"
                                                                    viewBox="0 0 24 24">
                                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                                        stroke-width="2" d="M5 13l4 4L19 7" />
                                                                </svg>
                                                                ACC
                                                            </button>
                                                        </form>
                                                    </div>
                                                <?php elseif($dok->approval_status == 'approved'): ?>
                                                    <span class="text-xs text-slate-400">Sudah di-ACC</span>
                                                <?php else: ?>
                                                    <span class="text-xs text-slate-400">Menunggu revisi</span>
                                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                            </td>
                                        </tr>
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                                </tbody>
                            </table>
                        </div>

                        <!-- Mobile Card List -->
                        <div class="md:hidden divide-y divide-slate-100" id="mobileList">
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $dokumens; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $dok): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                                <div class="p-4 dok-row"
                                    data-name="<?php echo e(strtolower($dok->nama_mahasiswa ?? '')); ?> <?php echo e(strtolower($dok->nim ?? '')); ?>">
                                    <div class="flex justify-between items-start mb-3">
                                        <div class="flex items-center gap-3">
                                            <div
                                                class="h-9 w-9 rounded bg-indigo-100 flex items-center justify-center text-indigo-700 font-bold text-xs">
                                                <?php echo e(strtoupper(substr($dok->nama_mahasiswa ?? 'M', 0, 2))); ?>

                                            </div>
                                            <div>
                                                <p class="text-sm font-semibold text-slate-900">
                                                    <?php echo e($dok->nama_mahasiswa ?? 'Mahasiswa'); ?></p>
                                                <p class="text-xs text-slate-500"><?php echo e($dok->nim); ?> · <?php echo e($dok->jenis_dokumen); ?>

                                                </p>
                                            </div>
                                        </div>
                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($dok->approval_status == 'pending'): ?>
                                            <span
                                                class="px-2 py-1 text-[10px] font-bold rounded-full bg-amber-100 text-amber-700">Pending</span>
                                        <?php elseif($dok->approval_status == 'approved'): ?>
                                            <span
                                                class="px-2 py-1 text-[10px] font-bold rounded-full bg-emerald-100 text-emerald-700">ACC</span>
                                        <?php else: ?>
                                            <span
                                                class="px-2 py-1 text-[10px] font-bold rounded-full bg-red-100 text-red-700">Revisi</span>
                                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                    </div>
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($dok->file_path): ?>
                                        <a href="<?php echo e($dok->file_url); ?>" target="_blank"
                                            class="text-xs text-blue-600 underline mb-3 block">
                                            <?php echo e(basename($dok->file_path)); ?>

                                        </a>
                                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($dok->approval_status == 'pending'): ?>
                                        <div class="flex gap-2 mt-2">
                                            <form
                                                action="<?php echo e(route('eoffice.kp.dosen.bimbingan.dokumen.reject', [$dok->kp_id, $dok->id])); ?>"
                                                method="POST" class="flex-1">
                                                <?php echo csrf_field(); ?>
                                                <button
                                                    class="w-full py-2 text-xs font-medium text-red-600 bg-red-50 border border-red-200 rounded-lg">Revisi</button>
                                            </form>
                                            <form
                                                action="<?php echo e(route('eoffice.kp.dosen.bimbingan.dokumen.approve', [$dok->kp_id, $dok->id])); ?>"
                                                method="POST" class="flex-1">
                                                <?php echo csrf_field(); ?>
                                                <button
                                                    class="w-full py-2 text-xs font-medium text-white bg-emerald-600 rounded-lg">ACC</button>
                                            </form>
                                        </div>
                                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                </div>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                        </div>

                        <!-- Footer -->
                        <div class="px-5 py-3 border-t border-slate-200 bg-slate-50">
                            <p class="text-xs text-slate-500">Total <?php echo e($dokumens->count()); ?> dokumen ditemukan</p>
                        </div>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div>
    <?php $__env->startPush('scripts'); ?>
    <script>
        function filterTable() {
            const keyword = document.getElementById('searchInput').value.toLowerCase();
            document.querySelectorAll('.dok-row').forEach(row => {
                row.style.display = row.dataset.name.includes(keyword) ? '' : 'none';
            });
        }
    </script>
    <?php $__env->stopPush(); ?>
 <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal327816c8748951109e999046d52cab34)): ?>
<?php $attributes = $__attributesOriginal327816c8748951109e999046d52cab34; ?>
<?php unset($__attributesOriginal327816c8748951109e999046d52cab34); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal327816c8748951109e999046d52cab34)): ?>
<?php $component = $__componentOriginal327816c8748951109e999046d52cab34; ?>
<?php unset($__componentOriginal327816c8748951109e999046d52cab34); ?>
<?php endif; ?><?php /**PATH C:\WebsiteTekkom - Copy\Modules\EOffice\resources\views\dosen\validasi_berkas.blade.php ENDPATH**/ ?>