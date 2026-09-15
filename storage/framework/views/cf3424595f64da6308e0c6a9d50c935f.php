<?php if (isset($component)) { $__componentOriginalc437526b4c56b06f9c16ab6722a84925 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalc437526b4c56b06f9c16ab6722a84925 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'eoffice::components.manajemen-praktikum.layout','data' => ['pageTitle' => 'Edit Periode Pendaftaran']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('eoffice::manajemen-praktikum.layout'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['pageTitle' => 'Edit Periode Pendaftaran']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>



<div class="mp-page-header">
    <div>
        <div style="display:flex;align-items:center;gap:8px;margin-bottom:4px;">
            <h1 class="mp-page-title">Edit Periode Pendaftaran</h1>
            <span class="mp-badge error sm"><span class="dot"></span>Admin</span>
        </div>
        <p class="mp-page-sub">Atur jadwal buka/tutup pendaftaran dan status aktifnya · <?php echo e(now()->locale('id')->isoFormat('dddd, D MMMM YYYY')); ?></p>
    </div>
    <div class="mp-page-actions">
        <a href="<?php echo e(route('eoffice.manprak.admin.periode-pendaftaran.index', ['matkul_id' => $periode->praktikum?->matkul_id, 'praktikum_id' => $periode->praktikum_id])); ?>"
           class="mp-btn secondary md" style="text-decoration:none;">
            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><polyline points="15 18 9 12 15 6"/></svg>
            Kembali
        </a>
    </div>
</div>

<?php
    $statusLabel = $periode->isSedangBuka()
        ? ['label' => 'Sedang Buka', 'variant' => 'success']
        : ($periode->is_aktif ? ['label' => 'Terjadwal / Lewat Waktu', 'variant' => 'warning'] : ['label' => 'Ditutup', 'variant' => 'neutral']);
?>


<div class="sec-head">
    <span class="sec-bar"></span>
    <span class="sec-title">Edit Periode</span>
    <span class="sec-rule"></span>
    <span class="mp-badge <?php echo e($statusLabel['variant']); ?> sm"><span class="dot"></span><?php echo e($statusLabel['label']); ?></span>
</div>

<div style="display:grid;grid-template-columns:1fr 320px;gap:14px;flex:1;min-height:0;">

    
    <div class="mp-card">
        <div class="mp-card-header">
            <div>
                <div style="font-weight:700;font-size:15px;color:#0D0D12;"><?php echo e($periode->nama); ?></div>
                <div style="font-size:12px;color:#666D80;margin-top:2px;"><?php echo e($periode->praktikum?->nama ?? '-'); ?></div>
            </div>
            <div class="right">
                <span class="mp-badge <?php echo e($statusLabel['variant']); ?> sm"><span class="dot"></span><?php echo e($statusLabel['label']); ?></span>
            </div>
        </div>
        <div style="padding:24px;">
            <form method="POST" action="<?php echo e(route('eoffice.manprak.admin.periode-pendaftaran.update', $periode->id)); ?>" style="display:flex;flex-direction:column;gap:16px;">
                <?php echo csrf_field(); ?>
                <?php echo method_field('PUT'); ?>

                <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;">
                    <div>
                        <label style="display:block;font-size:12px;font-weight:600;color:#353849;margin-bottom:4px;">Praktikum</label>
                        <select name="praktikum_id" required class="mp-input mp-select w-full">
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $praktikumList; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $praktikum): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                            <option value="<?php echo e($praktikum->id); ?>" <?php echo e($periode->praktikum_id === $praktikum->id ? 'selected' : ''); ?>>
                                <?php echo e($praktikum->nama); ?>

                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($praktikum->matkul): ?> - <?php echo e($praktikum->matkul->kode); ?> <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($praktikum->dosen): ?> - <?php echo e($praktikum->dosen->name); ?> <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            </option>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                        </select>
                    </div>
                    <div>
                        <label style="display:block;font-size:12px;font-weight:600;color:#353849;margin-bottom:4px;">Jenis</label>
                        <select name="jenis" required class="mp-input mp-select w-full">
                            <option value="koor" <?php echo e($periode->jenis === 'koor' ? 'selected' : ''); ?>>Koordinator Praktikum</option>
                            <option value="asprak" <?php echo e($periode->jenis === 'asprak' ? 'selected' : ''); ?>>Asisten Praktikum</option>

                        </select>
                    </div>
                </div>

                <div>
                    <label style="display:block;font-size:12px;font-weight:600;color:#353849;margin-bottom:4px;">Nama Periode</label>
                    <input name="nama" value="<?php echo e(old('nama', $periode->nama)); ?>" required class="mp-input w-full">
                </div>

                <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;">
                    <div>
                        <label style="display:block;font-size:12px;font-weight:600;color:#353849;margin-bottom:4px;">Dibuka Pada <span style="color:#DF1C41;">*</span></label>
                        <input type="datetime-local" name="dibuka_pada" required
                               value="<?php echo e(old('dibuka_pada', $periode->dibuka_pada?->format('Y-m-d\TH:i'))); ?>"
                               class="mp-input w-full">
                    </div>
                    <div>
                        <label style="display:block;font-size:12px;font-weight:600;color:#353849;margin-bottom:4px;">Ditutup Pada <span style="color:#DF1C41;">*</span></label>
                        <input type="datetime-local" name="ditutup_pada" required
                               value="<?php echo e(old('ditutup_pada', $periode->ditutup_pada?->format('Y-m-d\TH:i'))); ?>"
                               class="mp-input w-full">
                    </div>
                </div>

                <label style="display:flex;align-items:center;gap:8px;font-size:13px;font-weight:600;color:#353849;cursor:pointer;">
                    <input type="hidden" name="is_aktif" value="0">
                    <input type="checkbox" name="is_aktif" value="1" class="accent-[#0B266E]" <?php echo e(old('is_aktif', $periode->is_aktif) ? 'checked' : ''); ?>>
                    Periode aktif
                </label>

                <div style="display:flex;justify-content:flex-end;gap:8px;padding-top:8px;border-top:1px solid #DFE1E7;">
                    <a href="<?php echo e(route('eoffice.manprak.admin.periode-pendaftaran.index', ['matkul_id' => $periode->praktikum?->matkul_id, 'praktikum_id' => $periode->praktikum_id])); ?>"
                       class="mp-btn secondary md" style="text-decoration:none;">Batal</a>
                    <button type="submit" class="mp-btn primary md">Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>

    
    <div style="display:flex;flex-direction:column;gap:14px;">

        
        <div class="mp-card flex-shrink-0">
            <div class="mp-card-header">
                <span class="mp-card-title">Aksi Cepat</span>
            </div>
            <div style="padding:20px;">
                <p style="font-size:12px;color:#666D80;margin-bottom:16px;line-height:1.6;">Tutup manual akan menyembunyikan pengumuman pembukaan dan menghapus notifikasi pembukaan dari dashboard global.</p>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($periode->is_aktif): ?>
                <form method="POST" action="<?php echo e(route('eoffice.manprak.admin.periode-pendaftaran.tutup', $periode->id)); ?>">
                    <?php echo csrf_field(); ?>
                    <button type="submit" class="mp-btn secondary md w-full" style="color:#DF1C41;border-color:#DF1C41;">Tutup Sekarang</button>
                </form>
                <?php else: ?>
                <div style="padding:10px;border-radius:9px;background:#F6F8FA;color:#666D80;font-size:13px;font-weight:600;text-align:center;">Periode sudah ditutup</div>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </div>
        </div>

        
        <div class="mp-card flex-shrink-0">
            <div class="mp-card-header">
                <span class="mp-card-title">Ringkasan</span>
            </div>
            <div style="padding:20px;">
                <div style="display:flex;flex-direction:column;gap:10px;font-size:12px;">
                    <div style="display:flex;justify-content:space-between;align-items:center;">
                        <span style="color:#666D80;">Dibuka oleh</span>
                        <span style="font-weight:600;color:#353849;"><?php echo e($periode->dibukaOleh?->name ?? '-'); ?></span>
                    </div>
                    <div style="height:1px;background:#DFE1E7;"></div>
                    <div style="display:flex;justify-content:space-between;align-items:center;">
                        <span style="color:#666D80;">Dibuat</span>
                        <span style="font-weight:600;color:#353849;"><?php echo e($periode->created_at?->format('d M Y H:i')); ?></span>
                    </div>
                    <div style="height:1px;background:#DFE1E7;"></div>
                    <div style="display:flex;justify-content:space-between;align-items:center;">
                        <span style="color:#666D80;">Terakhir diubah</span>
                        <span style="font-weight:600;color:#353849;"><?php echo e($periode->updated_at?->format('d M Y H:i')); ?></span>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>

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
<?php /**PATH C:\WebsiteTekkom - Copy\Modules\EOffice\resources\views\manajemen-praktikum_backup\admin\periode-pendaftaran-edit.blade.php ENDPATH**/ ?>