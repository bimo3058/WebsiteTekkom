<?php if (isset($component)) { $__componentOriginalc437526b4c56b06f9c16ab6722a84925 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalc437526b4c56b06f9c16ab6722a84925 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'eoffice::components.manajemen-praktikum.layout','data' => ['pageTitle' => 'Kelola Modul']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('eoffice::manajemen-praktikum.layout'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['pageTitle' => 'Kelola Modul']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>



<div class="mp-page-header">
    <div>
        <div style="display:flex;align-items:center;gap:8px;margin-bottom:4px;">
            <h1 class="mp-page-title">Kelola Modul Praktikum</h1>
            <span class="mp-badge" style="background:#E0E7FF;color:#6366F1;border-radius:999px;padding:3px 10px;font-size:11px;font-weight:600;display:inline-flex;align-items:center;gap:5px;"><span class="dot" style="background:#6366F1;"></span>Koordinator</span>
        </div>
        <p class="mp-page-sub"><?php echo e($praktikum?->nama ?? 'Belum ada praktikum aktif'); ?> · <?php echo e(now()->locale('id')->isoFormat('dddd, D MMMM YYYY')); ?></p>
    </div>
</div>

<?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(!$praktikum): ?>
<div class="mp-alert warning flex-shrink-0">
    Anda belum ditugaskan sebagai koordinator praktikum aktif.
</div>
<?php else: ?>

<div class="sec-head">
    <span class="sec-bar"></span>
    <span class="sec-title">Tambah & Kelola Modul</span>
    <span class="sec-rule"></span>
</div>

<div class="grid grid-cols-[360px_1fr] gap-[14px] flex-1 min-h-0">
    
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($praktikum->is_active): ?>
    <div class="mp-card flex-shrink-0" style="padding:20px;">
        <div style="font-weight:700;font-size:14px;color:#0D0D12;margin-bottom:16px;">Tambah Modul</div>
        <form method="POST" action="<?php echo e(route('eoffice.manprak.koor.modul.store')); ?>" class="flex flex-col gap-3">
            <?php echo csrf_field(); ?>
            <div>
                <label class="block text-[12px] font-semibold text-[#353849] mb-1">Nama Modul</label>
                <input name="nama" required class="mp-input w-full" placeholder="Modul 1">
            </div>
            <div>
                <label class="block text-[12px] font-semibold text-[#353849] mb-1">Urutan</label>
                <input type="number" name="urutan" min="1" value="<?php echo e(($moduls->max('urutan') ?? 0) + 1); ?>" required class="mp-input w-full">
            </div>
            <div>
                <label class="block text-[12px] font-semibold text-[#353849] mb-1">Jadwal / Minggu</label>
                <input name="jadwal_minggu" class="mp-input w-full" placeholder="Minggu ke-1">
            </div>
            <div>
                <label class="block text-[12px] font-semibold text-[#353849] mb-1">Deskripsi</label>
                <textarea name="deskripsi" rows="4" class="mp-input w-full resize-none"></textarea>
            </div>
            <button class="mp-btn primary md w-full">Simpan Modul</button>
        </form>
    </div>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

    
    <div class="mp-card min-h-0">
        <div class="mp-card-header">
            <span class="mp-card-title">Daftar Modul</span>
            <div class="right">
                <span class="mp-badge neutral sm"><?php echo e($moduls->count()); ?> modul</span>
            </div>
        </div>
        <div class="overflow-x-auto flex-1">
            <table class="mp-table">
                <thead>
                    <tr style="background:#F9FAFB;">
                        <th class="mp-th text-left" style="padding:10px 16px;">Modul</th>

                        <th class="mp-th text-left" style="padding:10px 16px;">Asisten Praktikum</th>
                        <th class="mp-th text-left" style="padding:10px 16px;">Konten</th>
                        <th class="mp-th text-left" style="padding:10px 16px;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $moduls; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $m): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                    <tr class="mp-tr" style="border-bottom:1px solid #DFE1E7;">
                        <td style="padding:12px 16px;">
                            <div style="font-weight:600;color:#0D0D12;"><?php echo e($m->urutan); ?>. <?php echo e($m->nama); ?></div>
                            <div style="font-size:11px;color:#666D80;"><?php echo e($m->jadwal_minggu ?? 'Jadwal belum diisi'); ?></div>
                        </td>

                        <td style="padding:12px 16px;font-size:12px;color:#666D80;"><?php echo e($m->modulAsprak->pluck('asprak.user.name')->filter()->join(', ') ?: '-'); ?></td>
                        <td style="padding:12px 16px;font-size:12px;color:#666D80;"><?php echo e($m->materi->count()); ?> materi, <?php echo e($m->tugas->count()); ?> tugas</td>
                        <td style="padding:12px 16px;">
                            <div class="flex gap-2">
                                <a href="<?php echo e(route('eoffice.manprak.koor.modul.show', $m->id)); ?>" class="mp-btn primary sm" style="text-decoration:none;">Detail</a>
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($praktikum->is_active): ?>
                                <form method="POST" action="<?php echo e(route('eoffice.manprak.koor.modul.destroy', $m->id)); ?>" onsubmit="return confirm('Hapus modul ini?')">
                                    <?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?>
                                    <button class="mp-btn destructive sm">Hapus</button>
                                </form>
                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            </div>
                        </td>
                    </tr>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                    <tr>
                        <td colspan="5">
                            <div style="padding:48px;text-align:center;">
                                <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="#A4ABB8" stroke-width="1.5" stroke-linecap="round" style="margin:0 auto 12px;display:block;"><rect x="2" y="3" width="20" height="14" rx="2"/><path d="M8 21h8M12 17v4"/></svg>
                                <div style="font-size:13px;font-weight:500;color:#666D80;">Belum ada modul.</div>
                            </div>
                        </td>
                    </tr>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

 <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalc437526b4c56b06f9c16ab6722a84925)): ?>
<?php $attributes = $__attributesOriginalc437526b4c56b06f9c16ab6722a84925; ?>
<?php unset($__attributesOriginalc437526b4c56b06f9c16ab6722a84925); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalc437526b4c56b06f9c16ab6722a84925)): ?>
<?php $component = $__componentOriginalc437526b4c56b06f9c16ab6722a84925; ?>
<?php unset($__componentOriginalc437526b4c56b06f9c16ab6722a84925); ?>
<?php endif; ?>
<?php /**PATH C:\WebsiteTekkom - Copy\Modules\EOffice\resources\views\manajemen-praktikum\koordinator\modul.blade.php ENDPATH**/ ?>