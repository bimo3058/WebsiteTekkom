<?php if (isset($component)) { $__componentOriginal544c4f22773da773738fd88478b17597 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal544c4f22773da773738fd88478b17597 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'eoffice::components.manajemen-ruangan.layout','data' => ['pageTitle' => 'Arsip']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('eoffice::manajemen-ruangan.layout'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['pageTitle' => 'Arsip']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>


    <div class="mp-page-header">
        <div>
            <h1 class="mp-page-title">Arsip</h1>
            <p class="mp-page-sub">Gudang arsip seluruh data histori pengajuan ruangan yang telah berstatus Selesai.</p>
        </div>
    </div>

    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(session('success')): ?>
        <div
            class="bg-emerald-50 text-emerald-700 border border-emerald-200 px-4 py-3 rounded-lg mb-6 flex items-center gap-3">
            <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" class="flex-shrink-0">
                <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"></path>
            </svg>
            <p class="text-sm font-medium"><?php echo e(session('success')); ?></p>
        </div>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

    <div class="bg-white border border-gray-200 rounded-[12px] mt-6" style="box-shadow: 0 1px 3px rgba(0,0,0,0.03);"
        x-data="filterManager()">
        <div
            class="px-5 py-4 border-b border-gray-100 flex flex-col md:flex-row justify-between items-start md:items-center gap-4 bg-white rounded-t-[12px]">
            <h2 class="text-base font-bold text-gray-900 tracking-tight">Arsip Peminjaman</h2>

            <form action="<?php echo e(route('eoffice.peminjaman.admin.riwayat.index')); ?>" method="GET"
                class="flex flex-wrap items-center gap-2.5">
                
                <div class="relative w-full sm:w-auto">
                    <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                    </div>
                    <input type="text" name="search" value="<?php echo e(request('search')); ?>"
                        class="w-full sm:w-56 h-[38px] pl-9 pr-3 text-[13px] bg-white border border-gray-200 rounded-lg focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500 outline-none transition-all placeholder-gray-400"
                        placeholder="Search" x-on:input.debounce.700ms="$el.form.submit()">
                </div>

                
                <div class="relative" x-data="{ open: false }">
                    <button type="button" @click="open = !open"
                        class="h-[38px] px-3.5 bg-white border border-gray-200 rounded-lg flex items-center gap-2 text-[13px] font-medium text-gray-700 hover:bg-gray-50 hover:border-gray-300 transition-colors">
                        <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z">
                            </path>
                        </svg>
                        Filter
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(request()->hasAny(['status', 'ruangan_id', 'start_date', 'end_date']) && array_filter(request()->only(['status', 'ruangan_id', 'start_date', 'end_date']))): ?>
                            <span class="w-2 h-2 rounded-full bg-indigo-500 absolute -top-0.5 -right-0.5"></span>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </button>

                    <div x-show="open" @click.outside="open = false" style="display: none;"
                        class="absolute right-0 mt-2 w-72 bg-white rounded-xl shadow-[0_10px_25px_-5px_rgba(0,0,0,0.1)] border border-gray-100 z-50 overflow-hidden transform origin-top-right">
                        <div class="p-4 space-y-4">
                            <div>
                                <label
                                    class="block text-xs font-semibold text-gray-700 mb-1.5 relative z-10 w-full bg-white">Rentang
                                    Waktu</label>
                                <div class="flex gap-2">
                                    <input type="date" name="start_date" value="<?php echo e(request('start_date')); ?>"
                                        class="w-1/2 text-xs border border-gray-200 rounded block p-2 outline-none focus:border-indigo-500">
                                    <input type="date" name="end_date" value="<?php echo e(request('end_date')); ?>"
                                        class="w-1/2 text-xs border border-gray-200 rounded block p-2 outline-none focus:border-indigo-500">
                                </div>
                            </div>
                            <div>
                                <label
                                    class="block text-xs font-semibold text-gray-700 mb-1.5 focus:border-indigo-500 relative z-10 w-full bg-white">Ruangan</label>
                                <select name="ruangan_id"
                                    class="w-full text-xs border border-gray-200 rounded block p-2 outline-none">
                                    <option value="">Semua Ruangan</option>
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $ruangans; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $ruangan): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                                        <option value="<?php echo e($ruangan->id); ?>" <?php echo e(request('ruangan_id') == $ruangan->id ? 'selected' : ''); ?>><?php echo e($ruangan->nama); ?></option>
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                                </select>
                            </div>
                            <div>
                                <label
                                    class="block text-xs font-semibold text-gray-700 mb-1.5 relative z-10 w-full bg-white">Status</label>
                                <select name="status"
                                    class="w-full text-xs border border-gray-200 rounded block p-2 outline-none focus:border-indigo-500">
                                    <option value="">Semua Status</option>
                                    <option value="disetujui" <?php echo e(request('status') === 'disetujui' ? 'selected' : ''); ?>>
                                        Disetujui</option>
                                    <option value="ditolak" <?php echo e(request('status') === 'ditolak' ? 'selected' : ''); ?>>
                                        Ditolak</option>
                                    <option value="dibatalkan" <?php echo e(request('status') === 'dibatalkan' ? 'selected' : ''); ?>>
                                        Dibatalkan</option>
                                </select>
                            </div>
                            <div class="pt-2 flex justify-end gap-2 border-t border-gray-100">
                                <a href="<?php echo e(route('eoffice.peminjaman.admin.riwayat.index')); ?>"
                                    class="px-3 py-1.5 text-xs font-medium text-gray-600 hover:text-gray-900 border border-transparent">Reset</a>
                                <button type="submit"
                                    class="px-3 py-1.5 text-xs font-medium text-white bg-gray-900 rounded-md hover:bg-black">Terapkan
                                    Filter</button>
                            </div>
                        </div>
                    </div>
                </div>

            </form>
        </div>

        <div class="overflow-x-auto bg-[#F9FAFB]/50">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="border-b border-gray-100 bg-[#F9FAFB]/80">
                        <th class="py-3.5 px-5 text-[13px] font-medium text-[#6B7280]">Pengaju</th>
                        <th class="py-3.5 px-5 text-[13px] font-medium text-[#6B7280]">Ruangan & Tujuan</th>
                        <th class="py-3.5 px-5 text-[13px] font-medium text-[#6B7280]">Waktu Acara</th>
                        <th class="py-3.5 px-5 text-[13px] font-medium text-[#6B7280]">Status</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-100">
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $peminjamans; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $pinjam): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                        <tr class="hover:bg-gray-50/50 transition-colors group">
                            <td class="py-4 px-5 align-middle">
                                <div class="flex items-center gap-3">
                                    <div class="text-[13px] font-medium text-[#111827]">
                                        <?php echo e($pinjam->user->name ?? 'User Tidak Diketahui'); ?>

                                    </div>
                                </div>
                            </td>
                            <td class="py-4 px-5 align-middle">
                                <div class="text-[13px] text-[#111827]"><?php echo e($pinjam->ruangan->nama ?? 'Dihapus'); ?></div>
                                <div class="text-[11px] text-gray-500 max-w-[200px] truncate mt-0.5"
                                    title="<?php echo e($pinjam->tujuan); ?>"><?php echo e($pinjam->tujuan); ?></div>
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($pinjam->berkas_pendukung): ?>
                                    <a href="<?php echo e(app(\App\Services\SupabaseStorage::class)->getPublicUrl($pinjam->berkas_pendukung)); ?>"
                                        target="_blank"
                                        class="inline-flex items-center gap-1 text-[11px] font-medium text-indigo-600 hover:text-indigo-800 mt-1">
                                        <svg width="12" height="12" fill="none" stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14">
                                            </path>
                                        </svg>
                                        Lihat Berkas
                                    </a>
                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            </td>
                            <td class="py-4 px-5 align-middle">
                                <div class="text-[13px] text-[#111827]">
                                    <?php echo e(\Carbon\Carbon::parse($pinjam->tanggal_pinjam)->translatedFormat('d F Y')); ?> <span
                                        class="text-gray-400 mx-1">•</span>
                                    <?php echo e(\Carbon\Carbon::parse($pinjam->jam_mulai)->format('H:i')); ?> -
                                    <?php echo e(\Carbon\Carbon::parse($pinjam->jam_selesai)->format('H:i')); ?> WIB
                                </div>
                            </td>
                            <td class="py-4 px-5 align-middle">
                                <?php
                                    $style = '';
                                    if (strtolower($pinjam->status) === 'disetujui')
                                        $style = 'bg-[#ECF9F7] text-[#267666]';
                                    elseif (strtolower($pinjam->status) === 'ditolak')
                                        $style = 'bg-[#FEF2F2] text-[#B91C1C]';
                                    elseif (strtolower($pinjam->status) === 'menunggu')
                                        $style = 'bg-[#FFF9ED] text-[#A77B2E]';
                                    else
                                        $style = 'bg-[#ECEFF3] text-[#0D0D12]';
                                ?>
                                <span
                                    class="inline-flex items-center justify-center px-[12px] py-[4px] rounded-full <?php echo e($style); ?> text-[12px] font-medium tracking-wide">
                                    <?php echo e(ucfirst($pinjam->status)); ?>

                                </span>
                            </td>
                        </tr>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                        <tr>
                            <td colspan="4" class="py-12 text-center text-gray-500 text-[13px]">Belum ada arsip peminjaman
                                yang tersedia.</td>
                        </tr>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </tbody>
            </table>
        </div>

        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($peminjamans->hasPages()): ?>
            <div class="px-5 py-3 border-t border-gray-100 flex items-center justify-between">
                <div class="flex items-center gap-2 text-[13px] text-gray-600">
                    <span>Per page</span>
                    <select class="border border-gray-200 rounded px-1.5 py-0.5 outline-none font-medium text-gray-800">
                        <option>15</option>
                    </select>
                    <span class="ml-2">Showing <?php echo e($peminjamans->firstItem()); ?> to <?php echo e($peminjamans->lastItem()); ?> of
                        <?php echo e($peminjamans->total()); ?> results</span>
                </div>

                <div class="flex items-center">
                    <?php echo e($peminjamans->links('pagination::tailwind')); ?>

                </div>
            </div>
        <?php else: ?>
            <div class="flex items-center gap-2 text-[13px] text-gray-600 px-5 py-3 border-t border-gray-100">
                <span>Showing <?php echo e($peminjamans->count()); ?> results</span>
            </div>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    </div>

    <script>
        function filterManager() {
            return {};
        }
    </script>
 <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal544c4f22773da773738fd88478b17597)): ?>
<?php $attributes = $__attributesOriginal544c4f22773da773738fd88478b17597; ?>
<?php unset($__attributesOriginal544c4f22773da773738fd88478b17597); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal544c4f22773da773738fd88478b17597)): ?>
<?php $component = $__componentOriginal544c4f22773da773738fd88478b17597; ?>
<?php unset($__componentOriginal544c4f22773da773738fd88478b17597); ?>
<?php endif; ?><?php /**PATH C:\WebsiteTekkom - Copy\Modules\EOffice\resources\views\manajemen-ruangan\admin\persetujuan\riwayat.blade.php ENDPATH**/ ?>