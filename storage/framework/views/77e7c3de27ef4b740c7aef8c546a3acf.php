<?php if (isset($component)) { $__componentOriginalc437526b4c56b06f9c16ab6722a84925 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalc437526b4c56b06f9c16ab6722a84925 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'eoffice::components.manajemen-praktikum.layout','data' => ['pageTitle' => 'Dashboard']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('eoffice::manajemen-praktikum.layout'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['pageTitle' => 'Dashboard']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php
    $name      = auth()->user()->name;
    $firstName = explode(' ', $name)[0];
    $semesterLabel = $semesterLabel ?? 'Semester Genap 2025/2026';
?>

 <?php $__env->slot('header', null, []); ?> 
<div style="display:flex; align-items:center; justify-content:space-between; gap:16px; flex-wrap:wrap;">
    <div>
        <div style="display:flex; align-items:center; gap:8px; margin-bottom:3px;">
            <h1 style="font-size:22px; font-weight:700; color:var(--c-fg); letter-spacing:-0.02em; line-height:1.2;">Dashboard Dosen</h1>
            <span style="font-size:10px; font-weight:600; color:var(--c-primary); background:rgba(94,83,244,0.09); border:1px solid rgba(94,83,244,0.18); padding:2px 8px; border-radius:9999px; letter-spacing:0.03em;">Dosen</span>
        </div>
        <p style="font-size:12px; color:var(--c-fg-muted);">
            Selamat datang, <span style="color:var(--c-fg); font-weight:600;"><?php echo e($firstName); ?></span>
            <span style="margin-left:4px; color:var(--c-fg-placeholder);">·</span>
            <span style="margin-left:4px; color:var(--c-fg-muted);"><?php echo e(now()->locale('id')->isoFormat('dddd, D MMMM YYYY')); ?> · <?php echo e($semesterLabel); ?></span>
        </p>
    </div>
</div>
 <?php $__env->endSlot(); ?>


<div class="mp-stats-grid cols-4" style="flex-shrink:0;">

    
    <div class="mp-stat" style="border:1px solid var(--c-border); border-radius:14px; box-shadow:0 1px 3px rgba(0,0,0,.04);">
        <div style="display:flex; align-items:center; gap:12px; margin-bottom:12px;">
            <div class="mp-stat-icon green">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M4 19.5A2.5 2.5 0 016.5 17H20M6.5 2H20v20H6.5A2.5 2.5 0 014 19.5v-15A2.5 2.5 0 016.5 2z"/>
                </svg>
            </div>
            <div class="mp-stat-label" style="font-size:14px; color:var(--c-fg); margin:0;">Praktikum Aktif</div>
        </div>
        <div class="mp-stat-value" style="font-size:32px;"><?php echo e($totalPraktikumAktif ?? 0); ?></div>
        <div class="mp-stat-sub" style="font-size:13px;">Saat Ini</div>
    </div>

    
    <div class="mp-stat" style="border:1px solid var(--c-border); border-radius:14px; box-shadow:0 1px 3px rgba(0,0,0,.04);">
        <div style="display:flex; align-items:center; gap:12px; margin-bottom:12px;">
            <div class="mp-stat-icon sky">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2M9 11a4 4 0 100-8 4 4 0 000 8M23 21v-2a4 4 0 00-3-3.87M16 3.13a4 4 0 010 7.75"/>
                </svg>
            </div>
            <div class="mp-stat-label" style="font-size:14px; color:var(--c-fg); margin:0;">Total Praktikan</div>
        </div>
        <div class="mp-stat-value" style="font-size:32px;"><?php echo e($totalMahasiswa ?? 0); ?></div>
        <div class="mp-stat-sub" style="font-size:13px;">Mahasiswa Terdaftar</div>
    </div>

    
    <div class="mp-stat" style="border:1px solid var(--c-border); border-radius:14px; box-shadow:0 1px 3px rgba(0,0,0,.04);">
        <div style="display:flex; align-items:center; gap:12px; margin-bottom:12px;">
            <div class="mp-stat-icon red">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M11 4H4a2 2 0 00-2 2v14a2 2 0 002 2h14a2 2 0 002-2v-7M18.5 2.5a2.121 2.121 0 013 3L12 15l-4 1 1-4 9.5-9.5z"/>
                </svg>
            </div>
            <div class="mp-stat-label" style="font-size:14px; color:var(--c-fg); margin:0;">Persetujuan Nilai</div>
        </div>
        <div class="mp-stat-value" style="font-size:32px;"><?php echo e($nilaiMenungguApproval ?? 0); ?></div>
        <div class="mp-stat-sub" style="font-size:13px;">Belum Disetujui</div>
    </div>

    
    <div class="mp-stat" style="border:1px solid var(--c-border); border-radius:14px; box-shadow:0 1px 3px rgba(0,0,0,.04);">
        <div style="display:flex; align-items:center; gap:12px; margin-bottom:12px;">
            <div class="mp-stat-icon yellow">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="12" cy="12" r="10"/><path d="M12 6v6l4 2"/>
                </svg>
            </div>
            <div class="mp-stat-label" style="font-size:14px; color:var(--c-fg); margin:0;">Pendaftaran Koor</div>
        </div>
        <div class="mp-stat-value" style="font-size:32px;"><?php echo e(isset($pendaftaranKoorPending) ? $pendaftaranKoorPending->count() : 0); ?></div>
        <div class="mp-stat-sub" style="font-size:13px;">Perlu Peninjauan</div>
    </div>

</div>


<div style="display:grid; grid-template-columns:1fr 1fr; gap:16px; margin-top:24px; flex:1; min-height:0;">

    
    <div style="background:#fff; border:1px solid var(--c-border); border-radius:14px; overflow:hidden; box-shadow:0 1px 3px rgba(0,0,0,.04); display:flex; flex-direction:column; height:100%;">
        <div style="display:flex; align-items:center; justify-content:space-between; padding:16px 20px; border-bottom:1px solid var(--c-border); flex-shrink:0;">
            <div style="font-size:15px; font-weight:700; color:var(--c-fg);">Daftar Praktikum</div>
            <a href="<?php echo e(route('eoffice.manprak.dosen.praktikum.index')); ?>"
               style="font-size:12px; font-weight:600; color:var(--c-primary); text-decoration:none;">Lihat Semua &rarr;</a>
        </div>
        <div style="display:grid; grid-template-columns:1fr 280px; padding:12px 20px; border-bottom:1px solid var(--c-border); background:#FAFAFA; flex-shrink:0;">
            <div style="font-size:12px; font-weight:600; color:var(--c-fg-sec);">Nama Praktikum</div>
            <div style="font-size:12px; font-weight:600; color:var(--c-fg-sec); text-align:right;">Status</div>
        </div>
        <div style="flex:1; overflow-y:auto; min-height:0;">
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $praktikums ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $p): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
            <div style="display:grid; grid-template-columns:1fr 280px; padding:14px 20px; border-bottom:1px solid var(--c-border); align-items:center;">
                <div style="font-size:13px; font-weight:600; color:var(--c-fg);"><?php echo e($p->nama); ?></div>
                <div style="text-align:right;">
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($p->status === 'aktif'): ?>
                    <span class="mp-badge success sm">Aktif</span>
                    <?php else: ?>
                    <span class="mp-badge neutral sm">Tutup</span>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div>
            </div>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
            <div style="padding:40px 24px; text-align:center; display:flex; flex-direction:column; align-items:center; justify-content:center; gap:12px; height:100%;">
                <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="var(--c-border-strong)" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"/>
                    <path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"/>
                </svg>
                <span style="font-size:13px; font-weight:500; color:var(--c-fg-muted); max-width:280px; line-height:1.5;">Belum ada praktikum yang diampu.</span>
            </div>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </div>
    </div>

    
    <div style="background:#fff; border:1px solid var(--c-border); border-radius:14px; overflow:hidden; box-shadow:0 1px 3px rgba(0,0,0,.04); display:flex; flex-direction:column; height:100%;">
        <div style="display:flex; align-items:center; justify-content:space-between; padding:16px 20px; border-bottom:1px solid var(--c-border); flex-shrink:0;">
            <div style="font-size:15px; font-weight:700; color:var(--c-fg);">Pendaftaran Koordinator Praktikum</div>
        </div>
        <div style="display:grid; grid-template-columns:1fr 280px; padding:12px 20px; border-bottom:1px solid var(--c-border); background:#FAFAFA; flex-shrink:0;">
            <div style="font-size:12px; font-weight:600; color:var(--c-fg-sec);">Nama Mahasiswa</div>
            <div style="font-size:12px; font-weight:600; color:var(--c-fg-sec); text-align:right;">Nama Praktikum</div>
        </div>
        <div style="flex:1; overflow-y:auto; min-height:0;">
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $pendaftaranKoorPending; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $pend): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
            <div style="display:grid; grid-template-columns:1fr 280px; padding:14px 20px; border-bottom:1px solid var(--c-border); align-items:center;">
                <div>
                    <div style="font-size:13px; font-weight:600; color:var(--c-fg);"><?php echo e($pend->user?->name ?? 'Mahasiswa'); ?></div>
                    <div style="font-size:12px; color:var(--c-fg-muted); margin-top: 2px;">IPK: <?php echo e(number_format($pend->ipk ?? 0, 2)); ?></div>
                </div>
                <div style="text-align:right;">
                    <span style="display:inline-block; font-size:11px; font-weight:600; background:rgba(94,83,244,0.1); color:var(--c-primary); border:1px solid rgba(94,83,244,0.2); padding:4px 10px; border-radius:8px; white-space:nowrap;"><?php echo e($pend->praktikum->nama); ?></span>
                </div>
            </div>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
            <div style="padding:40px 24px; text-align:center; display:flex; flex-direction:column; align-items:center; justify-content:center; gap:12px; height:100%;">
                <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="var(--c-border-strong)" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M14 2H6a2 2 0 0 0-2 2v16c0 1.1.9 2 2 2h12a2 2 0 0 0 2-2V8l-6-6z"/>
                    <path d="M14 3v5h5M16 13H8M16 17H8M10 9H8"/>
                </svg>
                <span style="font-size:13px; font-weight:500; color:var(--c-fg-muted); max-width:280px; line-height:1.5;">Tidak ada pendaftaran yang perlu ditinjau.</span>
            </div>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
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
<?php endif; ?><?php /**PATH C:\WebsiteTekkom - Copy\Modules/EOffice\resources/views/manajemen-praktikum/dosen/dashboard.blade.php ENDPATH**/ ?>