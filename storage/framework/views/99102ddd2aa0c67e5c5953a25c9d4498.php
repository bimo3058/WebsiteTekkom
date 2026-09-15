<?php if (isset($component)) { $__componentOriginalc437526b4c56b06f9c16ab6722a84925 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalc437526b4c56b06f9c16ab6722a84925 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'eoffice::components.manajemen-praktikum.layout','data' => ['pageTitle' => 'Absensi & Nilai Saya']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('eoffice::manajemen-praktikum.layout'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['pageTitle' => 'Absensi & Nilai Saya']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>



<div class="mp-page-header">
    <div>
        <div style="display:flex;align-items:center;gap:8px;margin-bottom:4px;">
            <h1 class="mp-page-title">Absensi & Nilai Saya</h1>
            <span class="mp-badge warning sm"><span class="dot"></span>Mahasiswa</span>
        </div>
        <p class="mp-page-sub">Rekap kehadiran dan nilai tugas setelah dipublikasikan oleh dosen &amp; koordinator · <?php echo e(now()->locale('id')->isoFormat('dddd, D MMMM YYYY')); ?></p>
    </div>
</div>

<?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($praktikumList->isEmpty()): ?>


<div class="mp-card flex-shrink-0" style="padding:48px;text-align:center;">
    <div style="width:56px;height:56px;border-radius:14px;background:#F4F6F8;display:flex;align-items:center;justify-content:center;margin:0 auto 16px;">
        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#A4ABB8" stroke-width="1.5" stroke-linecap="round">
            <rect x="3" y="3" width="18" height="18" rx="3"/><path d="M9 9h6M9 12h6M9 15h4"/>
        </svg>
    </div>
    <div style="font-size:15px;font-weight:600;color:#0D0D12;margin-bottom:6px;">Belum Terdaftar di Praktikum</div>
    <div style="font-size:13px;color:#666D80;max-width:280px;margin:0 auto;line-height:1.6;">
        Anda belum terdaftar di praktikum manapun. Daftarkan diri melalui menu Pendaftaran.
    </div>

</div>

<?php else: ?>


<?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($praktikumList->count() > 1): ?>
<div class="sec-head flex-shrink-0">
    <span class="sec-bar" style="background:#D39C3D;"></span>
    <span class="sec-title">Pilih Praktikum</span>
    <span class="sec-rule"></span>
</div>
<div class="mp-card flex-shrink-0" style="margin-bottom:20px;">
    <div style="padding:14px 18px;">
        <form method="GET" style="display:flex;align-items:center;gap:10px;flex-wrap:wrap;">
            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="#808897" stroke-width="2" stroke-linecap="round" style="flex-shrink:0;">
                <path d="M2 3h6a4 4 0 014 4v14a3 3 0 00-3-3H2zM22 3h-6a4 4 0 00-4 4v14a3 3 0 013-3h7z"/>
            </svg>
            <select name="praktikum_id" class="mp-input mp-select" style="max-width:380px;" onchange="this.form.submit()">
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $praktikumList; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $p): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                <option value="<?php echo e($p->id); ?>" <?php echo e(($praktikum?->id == $p->id) ? 'selected' : ''); ?>>
                    <?php echo e($p->nama); ?> · <?php echo e($p->semester); ?> <?php echo e($p->tahun_ajaran); ?>

                </option>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
            </select>
        </form>
    </div>
</div>
<?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

<?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(!$praktikum): ?>
<div class="mp-alert info flex-shrink-0">Silakan pilih praktikum terlebih dahulu.</div>
<?php elseif(!$isPublished): ?>


<div class="mp-card flex-shrink-0" style="padding:48px;text-align:center;">
    <div style="width:56px;height:56px;border-radius:14px;background:rgba(211,156,61,0.1);display:flex;align-items:center;justify-content:center;margin:0 auto 16px;">
        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#D39C3D" stroke-width="1.5" stroke-linecap="round">
            <circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/>
        </svg>
    </div>
    <div style="font-size:15px;font-weight:600;color:#0D0D12;margin-bottom:6px;">Nilai Belum Dipublikasikan</div>
    <div style="font-size:13px;color:#666D80;max-width:340px;margin:0 auto;line-height:1.6;">
        Nilai untuk <strong><?php echo e($praktikum->nama); ?></strong> belum dipublikasikan. Nilai akan tampil setelah disetujui oleh Koordinator dan Dosen.
    </div>
    <div style="margin-top:16px;display:inline-flex;align-items:center;gap:10px;padding:10px 16px;background:#FEF9EE;border:1px solid #F4C666;border-radius:10px;">
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#D39C3D" stroke-width="2" stroke-linecap="round">
            <rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0110 0v4"/>
        </svg>
        <span style="font-size:12px;color:#92631A;font-weight:500;">Menunggu persetujuan Koordinator &amp; Dosen</span>
    </div>
</div>

<?php else: ?>


<div class="mp-alert success flex-shrink-0" style="margin-bottom:20px;">
    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"><polyline points="20 6 9 17 4 12"/></svg>
    Nilai untuk <strong><?php echo e($praktikum->nama); ?></strong> sudah dipublikasikan. Berikut adalah rekap absensi &amp; nilai Anda.
</div>

<?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($moduls->isEmpty()): ?>
<div class="mp-card flex-shrink-0" style="padding:40px;text-align:center;color:#666D80;font-size:13px;">Belum ada modul yang tersedia.</div>
<?php else: ?>


<div class="sec-head flex-shrink-0">
    <span class="sec-bar"></span>
    <span class="sec-title">Rekap Absensi &amp; Nilai</span>
    <span class="sec-rule"></span>
    <span class="mp-badge neutral sm"><?php echo e($moduls->count()); ?> modul</span>
</div>

<?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $moduls; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $modul): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
<?php
    $absensi = $dp->absensi->firstWhere('modul_id', $modul->id);
    $statusAbsen = $absensi?->status;
?>
<div class="mp-card flex-shrink-0" style="margin-bottom:20px;">
    
    <div class="mp-card-header" style="background:#F9FAFB;border-bottom:1px solid #EDF0F4;border-radius:12px 12px 0 0;display:flex;align-items:center;gap:12px;">
        <div style="width:32px;height:32px;border-radius:8px;background:linear-gradient(135deg,#6366F1,#8B5CF6);display:flex;align-items:center;justify-content:center;flex-shrink:0;">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="2" stroke-linecap="round">
                <path d="M2 3h6a4 4 0 014 4v14a3 3 0 00-3-3H2zM22 3h-6a4 4 0 00-4 4v14a3 3 0 013-3h7z"/>
            </svg>
        </div>
        <span class="mp-card-title" style="font-size:14px;color:#0D0D12;"><?php echo e($modul->nama); ?></span>
        
        <div style="margin-left:auto;">
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($statusAbsen === 'hadir'): ?>
                <span class="mp-badge success sm" style="background:#ECFDF5;color:#10B981;">Hadir</span>
            <?php elseif($statusAbsen === 'izin'): ?>
                <span class="mp-badge sky sm" style="background:#EFF6FF;color:#3B82F6;">Izin</span>
            <?php elseif($statusAbsen === 'tidak_hadir'): ?>
                <span class="mp-badge danger sm" style="background:#FEF2F2;color:#EF4444;">Alpha</span>
            <?php else: ?>
                <span style="font-size:11px;color:#A4ABB8;">Belum diisi</span>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </div>
    </div>

    
    <div style="overflow-x:auto;">
        <table class="mp-table" style="min-width:600px;">
            <thead>
                <tr style="background:#fff;">
                    <th class="mp-th text-left" style="padding:11px 16px;width:120px;">Kehadiran</th>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $modul->tugas; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $tugas): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                    <th class="mp-th text-center" style="padding:11px 16px;min-width:140px;"><?php echo e($tugas->judul); ?></th>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($modul->tugas->isEmpty()): ?>
                    <th class="mp-th text-center" style="padding:11px 16px;color:#A4ABB8;">— Belum ada tugas —</th>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </tr>
            </thead>
            <tbody>
                <tr class="mp-tr">
                    
                    <td style="padding:14px 16px;">
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($statusAbsen === 'hadir'): ?>
                            <span class="mp-badge success sm" style="background:#ECFDF5;color:#10B981;">✓ Hadir</span>
                        <?php elseif($statusAbsen === 'izin'): ?>
                            <span class="mp-badge sky sm" style="background:#EFF6FF;color:#3B82F6;">Izin</span>
                        <?php elseif($statusAbsen === 'tidak_hadir'): ?>
                            <span class="mp-badge danger sm" style="background:#FEF2F2;color:#EF4444;">✗ Alpha</span>
                        <?php else: ?>
                            <span style="color:#A4ABB8;font-size:12px;">—</span>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </td>
                    
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $modul->tugas; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $tugas): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                    <?php
                        $pengumpulan = $dp->pengumpulanTugas->firstWhere('tugas_id', $tugas->id);
                        $nilaiVal    = $pengumpulan?->nilai;
                    ?>
                    <td class="text-center" style="padding:14px 16px;">
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(!is_null($nilaiVal)): ?>
                            <?php
                                $color = $nilaiVal >= 80 ? '#10B981' : ($nilaiVal >= 60 ? '#F59E0B' : '#EF4444');
                                $bg    = $nilaiVal >= 80 ? '#ECFDF5' : ($nilaiVal >= 60 ? '#FFFBEB' : '#FEF2F2');
                            ?>
                            <span style="display:inline-block;font-size:16px;font-weight:700;color:<?php echo e($color); ?>;background:<?php echo e($bg); ?>;padding:4px 14px;border-radius:8px;">
                                <?php echo e($nilaiVal); ?>

                            </span>
                        <?php elseif($pengumpulan): ?>
                            <span class="mp-badge neutral sm">Dikumpul</span>
                        <?php else: ?>
                            <span style="color:#A4ABB8;font-size:12px;">—</span>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </td>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($modul->tugas->isEmpty()): ?>
                    <td style="padding:14px 16px;text-align:center;color:#A4ABB8;font-size:12px;">Belum ada tugas</td>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </tr>
            </tbody>
        </table>
    </div>
</div>
<?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>

<?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?> 
<?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?> 
<?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?> 

<style>
.sec-head {
    display: flex;
    align-items: center;
    gap: 10px;
    flex-shrink: 0;
    margin-bottom: 14px;
}
.sec-bar {
    width: 4px;
    height: 18px;
    background: #6366F1;
    border-radius: 2px;
    flex-shrink: 0;
}
.sec-title {
    font-size: 14px;
    font-weight: 700;
    color: #0D0D12;
    white-space: nowrap;
}
.sec-rule {
    flex: 1;
    height: 1px;
    background: #ECEFF3;
}
.mp-alert.success {
    display: flex;
    align-items: center;
    gap: 8px;
    padding: 12px 16px;
    background: #ECFDF5;
    border: 1px solid #A7F3D0;
    border-radius: 10px;
    font-size: 13px;
    color: #065F46;
}
.mp-alert.info {
    display: flex;
    align-items: center;
    gap: 8px;
    padding: 12px 16px;
    background: #EFF6FF;
    border: 1px solid #BFDBFE;
    border-radius: 10px;
    font-size: 13px;
    color: #1E40AF;
}
</style>

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
<?php /**PATH C:\WebsiteTekkom - Copy\Modules\EOffice\resources\views\manajemen-praktikum_backup\mahasiswa\nilai.blade.php ENDPATH**/ ?>