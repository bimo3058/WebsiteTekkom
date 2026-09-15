<?php if (isset($component)) { $__componentOriginal95cb0509b50d1cf2ddf4aa1e75407aa4 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal95cb0509b50d1cf2ddf4aa1e75407aa4 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'eoffice::components.layouts.koordinator','data' => ['title' => 'Rubrik Penilaian']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('eoffice::layouts.koordinator'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => 'Rubrik Penilaian']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

    <div class="max-w-6xl mx-auto space-y-6">

        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(session('success')): ?>
            <div x-data="{ show: true }" x-show="show"
                class="mb-4 bg-green-50 text-green-800 p-4 rounded-lg flex justify-between items-center bg-opacity-70 border border-green-200"
                x-init="setTimeout(() => show = false, 5000)">
                <div class="flex items-center gap-2 font-medium text-sm">
                    <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <?php echo e(session('success')); ?>

                </div>
            </div>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <div>
                <h1 class="text-2xl font-bold text-[#0D0D12]" style="font-family: 'Inter Tight', sans-serif;">Rubrik
                    Penilaian</h1>
                <p class="text-sm text-[#666D80] font-medium mt-1">Kelola template master komponen penilaian yang dapat
                    digunakan di berbagai periode.</p>
            </div>
            <a href="<?php echo e(route('eoffice.kp.koordinator.master-rubrik.create')); ?>"
                class="inline-flex items-center gap-2 px-4 py-2.5 bg-[#2E3C5B] text-white text-[13px] font-semibold rounded-lg hover:bg-[#1D2742] transition-colors shadow-sm">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                </svg>
                Tambah Penilaian
            </a>
        </div>

        <div class="bg-white border border-[#E2E8F0] rounded-2xl shadow-sm overflow-hidden">
            <!-- Header Table & Search -->
            <div
                class="px-5 py-4 flex flex-col sm:flex-row justify-between items-center gap-4 border-b border-[#E2E8F0]">
                <h2 class="text-[15px] font-bold text-[#0D0D12]" style="font-family: 'Inter Tight', sans-serif;">Tabel
                    Komponen</h2>

                <form method="GET" action="<?php echo e(route('eoffice.kp.koordinator.master-rubrik.index')); ?>"
                    class="flex items-center gap-3 w-full sm:w-auto">
                    <div class="relative w-full sm:w-64">
                        <span class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                            </svg>
                        </span>
                        <input type="text" name="search" value="<?php echo e(request('search')); ?>" placeholder="Search..."
                            class="w-full text-sm border border-[#E2E8F0] bg-[#F8F9FA] rounded-lg pl-9 pr-3 py-2 outline-none focus:border-[#2E3C5B] focus:bg-white transition-colors">
                    </div>
                    <!-- Hidden submit to allow pressing enter -->
                    <button type="submit" class="hidden"></button>
                </form>
            </div>

            <!-- Table -->
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-[#F8F9FA] border-b border-[#E2E8F0]">
                            <th class="px-5 py-3 text-[12px] font-semibold text-[#666D80] w-[15%]">Kode</th>
                            <th class="px-5 py-3 text-[12px] font-semibold text-[#666D80] w-[35%]">Deskripsi</th>
                            <th class="px-5 py-3 text-[12px] font-semibold text-[#666D80] w-[20%]">Role Penilai</th>
                            <th class="px-5 py-3 text-[12px] font-semibold text-[#666D80] w-[10%] text-center">Status
                            </th>
                            <th class="px-5 py-3 text-[12px] font-semibold text-[#666D80] w-[10%] text-center">Bobot
                            </th>
                            <th class="px-5 py-3 text-[12px] font-semibold text-[#666D80] w-[10%] text-center">Action
                            </th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-[#E2E8F0] bg-white">
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $rubriks; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $r): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                            <tr class="hover:bg-gray-50 transition-colors">
                                <td class="px-5 py-3.5 align-top">
                                    <div class="flex items-center gap-3">
                                        <div
                                            class="w-7 h-7 rounded border border-gray-200 flex items-center justify-center bg-gray-50 text-[#2E3C5B]">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M4 6h16M4 10h16M4 14h16M4 18h16"></path>
                                            </svg>
                                        </div>
                                        <span class="text-[13px] font-semibold text-[#0D0D12]"
                                            style="font-family: 'Inter Tight', sans-serif;"><?php echo e($r->kode); ?></span>
                                    </div>
                                </td>
                                <td class="px-5 py-3.5 align-top text-[13px] font-medium text-[#666D80] leading-relaxed">
                                    <?php echo e($r->deskripsi); ?>

                                </td>
                                <td class="px-5 py-3.5 align-top">
                                    <span
                                        class="text-[12px] font-semibold text-[#353849] bg-gray-100 border border-gray-200 px-2 py-1 rounded inline-block">
                                        <?php echo e($r->role_penilai === 'dosen_pembimbing' ? 'Dosen Pembimbing' : 'Koordinator'); ?>

                                    </span>
                                </td>
                                <td class="px-5 py-3.5 align-top text-center">
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($r->is_active): ?>
                                        <span
                                            class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-bold bg-[#ECFDF3] text-[#027A48] border border-[#ABEFC6]">
                                            <span class="w-1.5 h-1.5 rounded-full bg-[#12B76A]"></span>
                                            Aktif
                                        </span>
                                    <?php else: ?>
                                        <span
                                            class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-bold bg-[#F2F4F7] text-[#344054] border border-[#D0D5DD]">
                                            <span class="w-1.5 h-1.5 rounded-full bg-[#667085]"></span>
                                            Nonaktif
                                        </span>
                                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                </td>
                                <td class="px-5 py-3.5 align-top text-center">
                                    <span class="px-2.5 py-0.5 bg-[#FEF3F2] text-[#B42318] text-[11.5px] font-bold rounded">
                                        <?php echo e($r->bobot); ?>%
                                    </span>
                                </td>
                                <td class="px-5 py-3.5 align-top text-center">
                                    <div class="relative" x-data="{ open: false }">
                                        <button @click="open = !open" @click.away="open = false"
                                            class="text-gray-400 hover:text-gray-600 transition-colors">
                                            <svg class="w-5 h-5 mx-auto" fill="currentColor" viewBox="0 0 20 20">
                                                <path
                                                    d="M6 10a2 2 0 11-4 0 2 2 0 014 0zM12 10a2 2 0 11-4 0 2 2 0 014 0zM16 12a2 2 0 100-4 2 2 0 000 4z" />
                                            </svg>
                                        </button>

                                        <!-- Dropdown Menu -->
                                        <div x-show="open" style="display: none;"
                                            class="absolute right-0 mt-2 w-36 bg-white border border-[#E2E8F0] rounded-lg shadow-lg z-10 py-1 text-left">
                                            <a href="<?php echo e(route('eoffice.kp.koordinator.master-rubrik.edit', $r->id)); ?>"
                                                class="block px-4 py-2 text-[12px] font-semibold text-gray-700 hover:bg-gray-50 flex items-center gap-2">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z">
                                                    </path>
                                                </svg>
                                                Edit
                                            </a>
                                            <form
                                                action="<?php echo e(route('eoffice.kp.koordinator.master-rubrik.destroy', $r->id)); ?>"
                                                method="POST"
                                                onsubmit="return confirm('Apakah Anda yakin ingin menghapus komponen ini?');">
                                                <?php echo csrf_field(); ?>
                                                <?php echo method_field('DELETE'); ?>
                                                <button type="submit"
                                                    class="w-full text-left px-4 py-2 text-[12px] font-semibold text-red-600 hover:bg-red-50 flex items-center gap-2">
                                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor"
                                                        viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2"
                                                            d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16">
                                                        </path>
                                                    </svg>
                                                    Hapus
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                            <tr>
                                <td colspan="6" class="px-5 py-8 text-center text-[13px] text-gray-500 font-medium">Belum
                                    ada
                                    rubrik penilaian yang dibuat.</td>
                            </tr>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($rubriks->hasPages()): ?>
                <div class="px-5 py-4 border-t border-[#E2E8F0] bg-white text-sm">
                    <?php echo e($rubriks->withQueryString()->links()); ?>

                </div>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </div>
    </div>
 <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal95cb0509b50d1cf2ddf4aa1e75407aa4)): ?>
<?php $attributes = $__attributesOriginal95cb0509b50d1cf2ddf4aa1e75407aa4; ?>
<?php unset($__attributesOriginal95cb0509b50d1cf2ddf4aa1e75407aa4); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal95cb0509b50d1cf2ddf4aa1e75407aa4)): ?>
<?php $component = $__componentOriginal95cb0509b50d1cf2ddf4aa1e75407aa4; ?>
<?php unset($__componentOriginal95cb0509b50d1cf2ddf4aa1e75407aa4); ?>
<?php endif; ?><?php /**PATH C:\WebsiteTekkom - Copy\Modules\EOffice\resources\views\koordinator\rubrik\index.blade.php ENDPATH**/ ?>