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
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'banksoal::components.ui.page-header','data' => ['title' => 'Riwayat Validasi Bank Soal','subtitle' => 'Pantau riwayat paket soal mata kuliah yang telah selesai dievaluasi']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('banksoal::ui.page-header'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => 'Riwayat Validasi Bank Soal','subtitle' => 'Pantau riwayat paket soal mata kuliah yang telah selesai dievaluasi']); ?>
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

    <div class="mb-6 border-b border-slate-200">
        <nav class="flex gap-6 text-sm font-semibold">
            <a href="<?php echo e(route('banksoal.soal.gpm.validasi-bank-soal')); ?>" class="pb-3 border-b-2 border-transparent text-slate-500 hover:text-slate-700 flex items-center">
                Menunggu Validasi
                <span class="ml-2 inline-flex items-center rounded-full bg-slate-100 px-2 py-0.5 text-[11px] font-semibold text-slate-600 border border-slate-200"><?php echo e($counts->menunggu ?? 0); ?></span>
            </a>
            <a href="#" class="pb-3 border-b-2 border-primary text-primary flex items-center">
                Selesai Direview
                <span class="ml-2 inline-flex items-center rounded-full bg-primary/10 px-2 py-0.5 text-[11px] font-semibold text-primary border border-primary/20"><?php echo e($counts->selesai ?? $riwayat_soal->count()); ?></span>
            </a>
        </nav>
    </div>

    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-4 mb-6 flex flex-col gap-3 lg:flex-row lg:items-center lg:justify-between">
        <form action="<?php echo e(route('banksoal.soal.gpm.riwayat-validasi.bank-soal')); ?>" method="GET" class="relative flex-1">
            <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                </svg>
            </div>
            <input type="text" name="search" autocomplete="off" list="datalistRiwayat" value="<?php echo e(request('search')); ?>" placeholder="Cari mata kuliah..." class="w-full pl-10 pr-4 py-2.5 bg-white border border-slate-200 rounded-lg text-sm focus:outline-none" onchange="this.form.submit()">
            <datalist id="datalistRiwayat">
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $all_riwayat_soal; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                    <option value="<?php echo e($item->mk_nama); ?>"></option>
                    <option value="<?php echo e($item->mk_kode); ?>"></option>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
            </datalist>
        </form>
        <button class="inline-flex items-center gap-2 rounded-xl border border-slate-200 px-4 py-2.5 text-sm font-semibold text-slate-600 hover:bg-slate-50">
            <i class="fas fa-filter"></i> Filter
        </button>
    </div>

    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-primary text-white border-b border-primary/20">
                    <tr>
                        <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider">Mata Kuliah</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider">Dosen Pengampu</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider">Jumlah Soal</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider">Tanggal Review Terakhir</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider">Status</th>
                        <th class="px-6 py-4 text-center text-xs font-semibold uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $riwayat_soal; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $riwayat): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                        <tr class="hover:bg-slate-50/50 transition-colors">
                            <td class="px-6 py-4">
                                <div class="font-semibold text-slate-900"><?php echo e($riwayat->mk_nama); ?></div>
                                <div class="text-xs text-slate-500"><?php echo e($riwayat->mk_kode); ?></div>
                            </td>
                            <td class="px-6 py-4 text-sm text-slate-600">Dosen Pengampu</td>
                            <td class="px-6 py-4 text-sm text-slate-600"><?php echo e($riwayat->jumlah_soal); ?> Butir Direview</td>
                            <td class="px-6 py-4 text-sm text-slate-600">
                                <?php echo e($riwayat->tanggal_review ? \Carbon\Carbon::parse($riwayat->tanggal_review)->format('d M Y') : '-'); ?>

                            </td>
                            <td class="px-6 py-4">
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($riwayat->jumlah_revisi > 0): ?>
                                    <span class="inline-flex w-full justify-center rounded-full bg-rose-50 px-2.5 py-1 text-[11px] font-semibold text-rose-700 border border-rose-200">Dikembalikan</span>
                                <?php else: ?>
                                    <span class="inline-flex w-full justify-center rounded-full bg-emerald-50 px-2.5 py-1 text-[11px] font-semibold text-emerald-700 border border-emerald-200">Selesai</span>
                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <a href="<?php echo e(route('banksoal.soal.gpm.riwayat-validasi.bank-soal.detail', $riwayat->mk_id)); ?>" class="inline-flex flex-col items-center text-primary hover:text-primary/90">
                                    <i class="fas fa-eye"></i>
                                    <span class="text-[11px] font-semibold">Lihat Detail</span>
                                </a>
                            </td>
                        </tr>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center text-slate-600">
                                <div class="flex flex-col items-center justify-center">
                                    <i class="fas fa-history text-3xl text-slate-300 mb-3"></i>
                                    <p class="font-medium">Belum ada riwayat.</p>
                                    <p class="text-xs text-slate-500">Belum ada paket soal mata kuliah yang selesai divalidasi oleh GPM.</p>
                                </div>
                            </td>
                        </tr>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </tbody>
            </table>
        </div>

        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($riwayat_soal->count() > 0): ?>
            <div class="px-6 py-4 border-t border-slate-200 bg-slate-50 flex items-center justify-between">
                <span class="text-xs text-slate-500">Menampilkan <?php echo e($riwayat_soal->count()); ?> item</span>
            </div>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
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
<?php endif; ?><?php /**PATH C:\WebsiteTekkom - Copy\Modules\BankSoal\resources\views\gpm\riwayat-validasi\bank-soal.blade.php ENDPATH**/ ?>