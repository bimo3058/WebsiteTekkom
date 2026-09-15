<?php if (isset($component)) { $__componentOriginalc437526b4c56b06f9c16ab6722a84925 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalc437526b4c56b06f9c16ab6722a84925 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'eoffice::components.manajemen-praktikum.layout','data' => ['pageTitle' => 'Dashboard Koordinator — Manajemen Praktikum']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('eoffice::manajemen-praktikum.layout'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['pageTitle' => 'Dashboard Koordinator — Manajemen Praktikum']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php
    /** @var \Modules\EOffice\Models\Praktikum|null $praktikum */
    /** @var \Illuminate\Database\Eloquent\Collection|\Modules\EOffice\Models\Pengumuman[] $pengumumanTerbaru */
    /** @var \Illuminate\Database\Eloquent\Collection|\Modules\EOffice\Models\AsistenPraktikum[] $daftarAsprak */
    $name = auth()->user()->name;
    $nameParts = explode(' ', $name);
    $firstName = $nameParts[0];
    $pct = ($totalAsprak ?? 0) > 0 ? round(($asprakTerdistribusi ?? 0) / ($totalAsprak ?? 1) * 100) : 0;
?>


<div class="mp-page-header">
    <div>
        <div style="display:flex;align-items:center;gap:8px;margin-bottom:4px;">
            <h1 class="mp-page-title">Dashboard Koordinator</h1>
            <span class="mp-badge sm" style="background:#E0E7FF;color:#6366F1;"><span class="dot"></span>Koor. Prak.</span>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(isset($praktikum) && $praktikum): ?>
            <span class="mp-badge sm" style="background:<?php echo e($praktikum->status === 'aktif' ? '#DDF2EE' : '#ECEFF3'); ?>;color:<?php echo e($praktikum->status === 'aktif' ? '#174E43' : '#666D80'); ?>;">
                <span class="dot" style="background:<?php echo e($praktikum->status === 'aktif' ? '#40C4AA' : '#A4ABB8'); ?>;"></span>
                <?php echo e(ucfirst($praktikum->status)); ?>

            </span>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </div>
        <p class="mp-page-sub">
            Halo, <?php echo e($firstName); ?> · <?php echo e(now()->locale('id')->isoFormat('dddd, D MMMM YYYY')); ?> · <?php echo e($semesterLabel); ?>

            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(isset($praktikum) && $praktikum): ?> · <strong><?php echo e($praktikum->nama); ?></strong> <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </p>
        
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(isset($allPraktikum) && $allPraktikum->count() > 1): ?>
        <p class="mp-page-sub" style="margin-top:3px;color:#808897;">
            Anda mengampu <?php echo e($allPraktikum->count()); ?> praktikum · Ganti tampilan via tombol di kanan atas
        </p>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    </div>
    <div class="mp-page-actions">
        <a href="<?php echo e(route('eoffice.manprak.koor.pengumuman.index')); ?>" class="mp-btn secondary md" style="text-decoration:none;">
            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M18 8A6 6 0 006 8c0 7-3 9-3 9h18s-3-2-3-9M13.73 21a2 2 0 01-3.46 0"/></svg>
            Pengumuman
        </a>
        <a href="<?php echo e(route('eoffice.manprak.koor.bagi-modul.index')); ?>" class="mp-btn primary md" style="text-decoration:none;">
            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M12 2L2 7l10 5 10-5-10-5zM2 17l10 5 10-5M2 12l10 5 10-5"/></svg>
            Bagi Modul
        </a>
    </div>
</div>

<?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(!isset($praktikum) || !$praktikum): ?>
<div class="mp-alert warning flex-shrink-0">
    <div style="font-size:13px;font-weight:600;margin-bottom:4px;">Anda belum ditunjuk sebagai koordinator praktikum manapun.</div>
    <div style="font-size:12px;">Hubungi dosen pengampu untuk mendapatkan penugasan.</div>
</div>
<?php else: ?>


<div class="sec-head">
    <span class="sec-bar"></span>
    <span class="sec-title">Ringkasan Praktikum</span>
    <span class="sec-rule"></span>
    <span style="font-size:12px;color:#666D80;"><?php echo e($praktikum->nama); ?></span>
</div>


<div class="mp-stats-grid cols-4">
    <div class="mp-stat">
        <div class="mp-stat-icon yellow">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2M9 11a4 4 0 100-8 4 4 0 000 8zM23 21v-2a4 4 0 00-3-3.87M16 3.13a4 4 0 010 7.75"/></svg>
        </div>
        <div class="mp-stat-label">Total Praktikan</div>
        <div class="mp-stat-value"><?php echo e($totalPraktikan ?? 0); ?></div>
        <div class="mp-stat-sub">terdaftar</div>
    </div>
    <div class="mp-stat">
        <div class="mp-stat-icon green">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M16 21v-2a4 4 0 00-4-4H6a4 4 0 00-4 4v2M9 11a4 4 0 100-8 4 4 0 000 8M22 11l-3.5 3.5-1.5-1.5"/></svg>
        </div>
        <div class="mp-stat-label">Total Asisten Praktikum</div>
        <div class="mp-stat-value"><?php echo e($totalAsprak ?? 0); ?></div>
        <div class="mp-stat-sub">asisten aktif</div>
    </div>
    <div class="mp-stat">
        <div class="mp-stat-icon green">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
        </div>
        <div class="mp-stat-label">Asisten Praktikum Terdistribusi</div>
        <div class="mp-stat-value"><?php echo e($asprakTerdistribusi ?? 0); ?></div>
        <div class="mp-stat-sub">sudah dapat modul</div>
    </div>
    <div class="mp-stat">
        <div class="mp-stat-icon navy">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M4 19.5A2.5 2.5 0 016.5 17H20M6.5 2H20v20H6.5A2.5 2.5 0 014 19.5v-15A2.5 2.5 0 016.5 2z"/></svg>
        </div>
        <div class="mp-stat-label">Total Modul</div>
        <div class="mp-stat-value"><?php echo e($totalModul ?? 0); ?></div>
        <div class="mp-stat-sub">modul tersedia</div>
    </div>
</div>


<div class="sec-head">
    <span class="sec-bar"></span>
    <span class="sec-title">Operasional</span>
    <span class="sec-rule"></span>
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(($asprakBelumModul ?? 0) > 0): ?>
    <span class="mp-badge warning sm"><span class="dot"></span><?php echo e($asprakBelumModul); ?> asisten praktikum belum dapat modul</span>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
</div>


<div style="display:grid;grid-template-columns:1fr;gap:14px;flex-shrink:0;">

    
    <div class="mp-card" style="padding:20px;">
        <div style="font-size:11px;font-weight:600;color:#666D80;text-transform:uppercase;letter-spacing:.06em;margin-bottom:8px;">Distribusi Asisten Praktikum ke Modul</div>
        <div class="flex items-end justify-between mb-3">
            <div style="font-size:13px;color:#666D80;"><?php echo e($asprakTerdistribusi ?? 0); ?> dari <?php echo e($totalAsprak ?? 0); ?> asisten praktikum sudah mendapat modul</div>
            <div style="font-size:28px;font-weight:700;color:#0B266E;line-height:1;letter-spacing:-.02em;"><?php echo e($pct); ?>%</div>
        </div>
        <div style="width:100%;background:#ECEFF3;border-radius:999px;height:8px;overflow:hidden;">
            <div style="height:8px;border-radius:999px;width:<?php echo e($pct); ?>%;background:linear-gradient(90deg,#0B266E,#4C619A);transition:width .4s ease;"></div>
        </div>
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(($asprakBelumModul ?? 0) > 0): ?>
        <div style="margin-top:10px;display:flex;align-items:center;gap:6px;">
            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="#956321" stroke-width="2" stroke-linecap="round"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/></svg>
            <span style="font-size:11px;color:#956321;font-weight:500;"><?php echo e($asprakBelumModul); ?> asisten praktikum belum mendapat modul</span>
            <a href="<?php echo e(route('eoffice.manprak.koor.bagi-modul.index')); ?>"
               style="font-size:11px;font-weight:700;color:#0B266E;text-decoration:none;margin-left:4px;" class="hover:underline">Bagi Modul <?php if (isset($component)) { $__componentOriginal22b2b62d2a6bf157ddbb8ecb4421b995 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal22b2b62d2a6bf157ddbb8ecb4421b995 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'eoffice::components.manajemen-praktikum.ui.icon','data' => ['name' => 'arrow-right']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('eoffice::manajemen-praktikum.ui.icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'arrow-right']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal22b2b62d2a6bf157ddbb8ecb4421b995)): ?>
<?php $attributes = $__attributesOriginal22b2b62d2a6bf157ddbb8ecb4421b995; ?>
<?php unset($__attributesOriginal22b2b62d2a6bf157ddbb8ecb4421b995); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal22b2b62d2a6bf157ddbb8ecb4421b995)): ?>
<?php $component = $__componentOriginal22b2b62d2a6bf157ddbb8ecb4421b995; ?>
<?php unset($__componentOriginal22b2b62d2a6bf157ddbb8ecb4421b995); ?>
<?php endif; ?></a>
        </div>
        <?php else: ?>
        <div style="margin-top:10px;display:flex;align-items:center;gap:6px;">
            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="#287F6E" stroke-width="2" stroke-linecap="round"><polyline points="20 6 9 17 4 12"/></svg>
            <span style="font-size:11px;color:#287F6E;font-weight:500;">Semua asisten praktikum sudah mendapat modul</span>
        </div>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    </div>



</div>


<div class="sec-head">
    <span class="sec-bar"></span>
    <span class="sec-title">Tim &amp; Pengumuman</span>
    <span class="sec-rule"></span>
</div>


<div class="flex gap-[14px] flex-1 min-h-0 mb-1">

    
    <div class="mp-card min-w-0" style="flex:2;">
        <div class="mp-card-header" style="flex-shrink:0;">
            <span class="mp-card-title">Asisten Praktikum</span>
            <span class="mp-badge green sm"><?php echo e($totalAsprak ?? 0); ?> aktif</span>
            <div class="right">
                <a href="<?php echo e(route('eoffice.manprak.koor.bagi-modul.index')); ?>" class="mp-btn secondary sm" style="text-decoration:none;">Bagi Modul</a>
            </div>
        </div>
        <div style="display:flex;align-items:center;padding:10px 20px;background:#F9FAFB;border-bottom:1px solid #DFE1E7;flex-shrink:0;">
            <div class="mp-th flex-1">Nama Asisten Praktikum</div>
            <div class="mp-th" style="width:90px;">NIM</div>
            <div class="mp-th" style="width:140px;">Modul Diampu</div>
            <div class="mp-th" style="width:80px;text-align:center;">Status</div>
        </div>
        <div class="overflow-y-auto flex-1">
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $asistenList ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $a): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
            <div class="mp-tr" style="display:flex;align-items:center;padding:11px 20px;">
                <div class="flex-1 flex items-center gap-[10px] min-w-0 pr-2">
                    <div class="mp-av green"><?php echo e(strtoupper(substr($a->user?->name ?? 'A', 0, 1))); ?><?php echo e(strtoupper(substr($a->user?->name ?? 'A', strpos(($a->user?->name ?? 'A').' ',' ')+1, 1))); ?></div>
                    <span style="font-size:13px;font-weight:500;color:#0D0D12;" class="truncate"><?php echo e($a->user?->name ?? '—'); ?></span>
                </div>
                <div style="width:90px;font-size:12px;color:#666D80;font-family:ui-monospace,monospace;"><?php echo e($a->user?->student_number ?? '—'); ?></div>
                <div style="width:140px;font-size:12px;color:#353849;" class="truncate">
                    <?php echo e($a->modulAsprak->pluck('modul.nama')->join(', ') ?: '—'); ?>

                </div>
                <div style="width:80px;text-align:center;">
                    <span class="mp-badge success sm"><span class="dot"></span>Aktif</span>
                </div>
            </div>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
            <div style="padding:40px;text-align:center;">
                <svg width="36" height="36" viewBox="0 0 24 24" fill="none" stroke="#A4ABB8" stroke-width="1.5" stroke-linecap="round" style="margin:0 auto 12px;display:block;"><path d="M16 21v-2a4 4 0 00-4-4H6a4 4 0 00-4 4v2M9 11a4 4 0 100-8 4 4 0 000 8M22 11l-3.5 3.5-1.5-1.5"/></svg>
                <div style="font-size:13px;font-weight:500;color:#666D80;">Belum ada asisten praktikum.</div>
            </div>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </div>
    </div>

    
    <div class="mp-card flex-1 min-w-0">
        <div class="mp-card-header" style="flex-shrink:0;">
            <span class="mp-card-title">Pengumuman</span>
            <div class="right">
                <a href="<?php echo e(route('eoffice.manprak.koor.pengumuman.index')); ?>" class="mp-btn primary sm" style="text-decoration:none;">
                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                    Buat
                </a>
            </div>
        </div>
        <div class="overflow-y-auto flex-1">
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $pengumuman ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $peng): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
            <div style="padding:14px 20px;border-bottom:1px solid #ECEFF3;">
                <div style="font-size:13px;font-weight:600;color:#0D0D12;margin-bottom:4px;"><?php echo e($peng->judul); ?></div>
                <div style="font-size:12px;color:#666D80;line-height:1.5;" class="line-clamp-2"><?php echo e($peng->konten); ?></div>
                <div style="font-size:11px;color:#808897;margin-top:6px;"><?php echo e($peng->created_at->diffForHumans()); ?></div>
            </div>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
            <div style="padding:40px;text-align:center;">
                <svg width="36" height="36" viewBox="0 0 24 24" fill="none" stroke="#A4ABB8" stroke-width="1.5" stroke-linecap="round" style="margin:0 auto 12px;display:block;"><path d="M18 8A6 6 0 006 8c0 7-3 9-3 9h18s-3-2-3-9M13.73 21a2 2 0 01-3.46 0"/></svg>
                <div style="font-size:13px;font-weight:500;color:#666D80;">Belum ada pengumuman.</div>
            </div>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
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
<?php /**PATH C:\WebsiteTekkom - Copy\Modules\EOffice\resources\views\manajemen-praktikum\koordinator\dashboard.blade.php ENDPATH**/ ?>