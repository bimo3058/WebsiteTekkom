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
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'banksoal::components.ui.page-header','data' => ['title' => 'Manajemen Jadwal RPS','subtitle' => 'Kelola periode unggah RPS untuk Dosen']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('banksoal::ui.page-header'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => 'Manajemen Jadwal RPS','subtitle' => 'Kelola periode unggah RPS untuk Dosen']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

         <?php $__env->slot('actions', null, []); ?> 
            <a href="<?php echo e(route('banksoal.rps.gpm.periode-rps.create')); ?>" class="inline-flex items-center gap-2 rounded-xl bg-primary px-4 py-2.5 text-sm font-semibold text-white hover:bg-primary/90">
                <i class="fas fa-plus"></i> Buat Periode & Template
            </a>
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

    <div class="rounded-2xl border border-slate-200 bg-white shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead class="bg-primary text-xs uppercase text-white">
                    <tr>
                        <th class="px-6 py-4 text-left">No</th>
                        <th class="px-6 py-4 text-left">Info Periode</th>
                        <th class="px-6 py-4 text-left">Rentang Waktu</th>
                        <th class="px-6 py-4 text-left">Status</th>
                        <th class="px-6 py-4 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200">
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $periodes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $periode): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                        <tr>
                            <td class="px-6 py-4 text-sm text-slate-600"><?php echo e($index + 1); ?></td>
                            <td class="px-6 py-4">
                                <p class="text-sm font-semibold text-slate-900"><?php echo e($periode->judul); ?></p>
                                <p class="text-xs text-slate-500">Semester <?php echo e($periode->semester); ?> &bull; TA <?php echo e($periode->tahun_ajaran); ?></p>
                            </td>
                            <td class="px-6 py-4 text-sm text-slate-600">
                                <div class="flex items-center gap-2">
                                    <i class="fas fa-calendar-alt text-slate-400"></i>
                                    <?php echo e(\Carbon\Carbon::parse($periode->tanggal_mulai)->locale('id')->translatedFormat('d M Y, H:i')); ?>

                                </div>
                                <div class="mt-2 flex items-center gap-2 text-rose-500">
                                    <i class="fas fa-flag-checkered"></i>
                                    <span class="text-slate-600"><?php echo e(\Carbon\Carbon::parse($periode->tanggal_selesai)->locale('id')->translatedFormat('d M Y, H:i')); ?></span>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($periode->is_active): ?>
                                    <span class="inline-flex items-center gap-2 rounded-md border border-emerald-200 bg-emerald-50 px-2.5 py-1 text-[11px] font-semibold text-emerald-700">
                                        <span class="h-2 w-2 rounded-full bg-emerald-500"></span> Aktif
                                    </span>
                                <?php else: ?>
                                    <span class="inline-flex items-center rounded-md border border-slate-200 bg-slate-50 px-2.5 py-1 text-[11px] font-semibold text-slate-600">Non-Aktif</span>
                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            </td>
                            <td class="px-6 py-4 text-right">
                                <button type="button" class="inline-flex items-center justify-center rounded-lg border border-primary/20 px-3 py-2 text-xs font-semibold text-primary hover:bg-primary/10" data-modal-open="modalEdit<?php echo e($periode->id); ?>">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button type="button" class="inline-flex items-center justify-center rounded-lg border border-rose-200 px-3 py-2 text-xs font-semibold text-rose-600 hover:bg-rose-50" data-modal-open="modalHapus<?php echo e($periode->id); ?>">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </td>
                        </tr>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                        <tr>
                            <td colspan="5" class="px-6 py-12 text-center text-slate-500">
                                <div class="flex flex-col items-center gap-2">
                                    <i class="fas fa-calendar-times text-3xl text-slate-300"></i>
                                    <p class="text-sm font-semibold">Belum ada jadwal RPS</p>
                                    <p class="text-xs">Silakan tambah periode baru untuk mengaktifkan pengajuan RPS Dosen.</p>
                                    <a href="<?php echo e(route('banksoal.rps.gpm.periode-rps.create')); ?>" class="mt-2 inline-flex items-center gap-2 rounded-lg border border-primary/20 px-3 py-2 text-xs font-semibold text-primary hover:bg-primary/10">
                                        <i class="fas fa-plus"></i> Tambah Periode Pertama
                                    </a>
                                </div>
                            </td>
                        </tr>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $periodes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $periode): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
        <div id="modalEdit<?php echo e($periode->id); ?>" class="fixed inset-0 z-50 hidden" aria-hidden="true">
            <div class="absolute inset-0 bg-slate-900/40" data-modal-overlay="modalEdit<?php echo e($periode->id); ?>"></div>
            <div class="relative mx-auto mt-16 w-full max-w-xl rounded-2xl bg-white shadow-xl">
                <form action="<?php echo e(route('banksoal.rps.gpm.periode-rps.update', $periode->id)); ?>" method="POST">
                    <?php echo csrf_field(); ?>
                    <?php echo method_field('PUT'); ?>
                    <div class="flex items-center justify-between border-b border-slate-200 px-5 py-4">
                        <h2 class="text-sm font-semibold text-slate-900">Edit Periode RPS</h2>
                        <button type="button" class="text-slate-400 hover:text-slate-600" data-modal-close="modalEdit<?php echo e($periode->id); ?>">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                    <div class="px-5 py-4 space-y-4">
                        <div>
                            <label class="text-xs font-semibold text-slate-600">Judul Periode <span class="text-rose-500">*</span></label>
                            <input type="text" class="mt-2 w-full rounded-lg border border-slate-200 px-3 py-2 text-sm" name="judul" value="<?php echo e($periode->judul); ?>" required placeholder="Contoh: Pengajuan RPS Genap 2025/2026">
                        </div>
                        <div class="grid grid-cols-1 gap-3 sm:grid-cols-2">
                            <div>
                                <label class="text-xs font-semibold text-slate-600">Semester <span class="text-rose-500">*</span></label>
                                <select class="mt-2 w-full rounded-lg border border-slate-200 bg-slate-100 text-slate-500 cursor-not-allowed px-3 py-2 text-sm" disabled>
                                    <option value="Ganjil" <?php echo e($periode->semester == 'Ganjil' ? 'selected' : ''); ?>>Ganjil</option>
                                    <option value="Genap" <?php echo e($periode->semester == 'Genap' ? 'selected' : ''); ?>>Genap</option>
                                </select>
                                <input type="hidden" name="semester" value="<?php echo e($periode->semester); ?>">
                            </div>
                            <div>
                                <label class="text-xs font-semibold text-slate-600">Tahun Ajaran <span class="text-rose-500">*</span></label>
                                <select class="mt-2 w-full rounded-lg border border-slate-200 bg-slate-100 text-slate-500 cursor-not-allowed px-3 py-2 text-sm" disabled>
                                    <option value="" disabled>Pilih Tahun Ajaran</option>
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $tahunAjarans; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $ta): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                                        <option value="<?php echo e($ta); ?>" <?php echo e($periode->tahun_ajaran == $ta ? 'selected' : ''); ?>><?php echo e($ta); ?></option>
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                                </select>
                                <input type="hidden" name="tahun_ajaran" value="<?php echo e($periode->tahun_ajaran); ?>">
                            </div>
                        </div>
                        <div>
                            <label class="text-xs font-semibold text-slate-600">Waktu Mulai <span class="text-rose-500">*</span></label>
                            <input type="datetime-local" class="mt-2 w-full rounded-lg border border-slate-200 px-3 py-2 text-sm" name="tanggal_mulai" value="<?php echo e(\Carbon\Carbon::parse($periode->tanggal_mulai)->format('Y-m-d\TH:i')); ?>" required>
                        </div>
                        <div>
                            <label class="text-xs font-semibold text-slate-600">Waktu Selesai (Tenggat) <span class="text-rose-500">*</span></label>
                            <input type="datetime-local" class="mt-2 w-full rounded-lg border border-slate-200 px-3 py-2 text-sm" name="tanggal_selesai" value="<?php echo e(\Carbon\Carbon::parse($periode->tanggal_selesai)->format('Y-m-d\TH:i')); ?>" required>
                        </div>
                        <label class="flex items-start gap-3 rounded-lg border border-slate-200 p-3 text-xs text-slate-600">
                            <input class="mt-1" type="checkbox" name="is_active" value="1" <?php echo e($periode->is_active ? 'checked' : ''); ?>>
                            <span>
                                <span class="font-semibold text-slate-700">Set sebagai periode aktif saat ini</span>
                                <span class="block text-[11px] text-slate-500">Hanya satu periode yang bisa aktif. Mengaktifkan ini akan menonaktifkan periode lainnya.</span>
                            </span>
                        </label>
                    </div>
                    <div class="flex items-center justify-end gap-3 border-t border-slate-200 bg-slate-50 px-5 py-4">
                        <button type="button" class="rounded-lg border border-slate-200 px-4 py-2 text-xs font-semibold text-slate-600" data-modal-close="modalEdit<?php echo e($periode->id); ?>">Batal</button>
                        <button type="submit" class="rounded-lg bg-primary px-4 py-2 text-xs font-semibold text-white hover:bg-primary/90">Simpan Perubahan</button>
                    </div>
                </form>
            </div>
        </div>

        <div id="modalHapus<?php echo e($periode->id); ?>" class="fixed inset-0 z-50 hidden" aria-hidden="true">
            <div class="absolute inset-0 bg-slate-900/40" data-modal-overlay="modalHapus<?php echo e($periode->id); ?>"></div>
            <div class="relative mx-auto mt-24 w-full max-w-sm rounded-2xl bg-white shadow-xl">
                <div class="px-5 py-5 text-center">
                    <div class="text-rose-500 mb-3"><i class="fas fa-exclamation-triangle text-3xl"></i></div>
                    <h3 class="text-sm font-semibold text-slate-900">Hapus Periode?</h3>
                    <p class="text-xs text-slate-500 mt-2">Anda yakin ingin menghapus jadwal <strong><?php echo e($periode->judul); ?></strong>?</p>
                    <div class="mt-4 flex gap-2">
                        <button type="button" class="flex-1 rounded-lg border border-slate-200 px-3 py-2 text-xs font-semibold text-slate-600" data-modal-close="modalHapus<?php echo e($periode->id); ?>">Batal</button>
                        <form action="<?php echo e(route('banksoal.rps.gpm.periode-rps.destroy', $periode->id)); ?>" method="POST" class="flex-1">
                            <?php echo csrf_field(); ?>
                            <?php echo method_field('DELETE'); ?>
                            <button type="submit" class="w-full rounded-lg bg-rose-600 px-3 py-2 text-xs font-semibold text-white hover:bg-rose-700">Hapus</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>



 <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalbfe3dfc9f8c7e29aaca1eb4d19fe5039)): ?>
<?php $attributes = $__attributesOriginalbfe3dfc9f8c7e29aaca1eb4d19fe5039; ?>
<?php unset($__attributesOriginalbfe3dfc9f8c7e29aaca1eb4d19fe5039); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalbfe3dfc9f8c7e29aaca1eb4d19fe5039)): ?>
<?php $component = $__componentOriginalbfe3dfc9f8c7e29aaca1eb4d19fe5039; ?>
<?php unset($__componentOriginalbfe3dfc9f8c7e29aaca1eb4d19fe5039); ?>
<?php endif; ?>
<?php /**PATH C:\WebsiteTekkom - Copy\Modules\BankSoal\resources\views\gpm\periode-rps\index.blade.php ENDPATH**/ ?>