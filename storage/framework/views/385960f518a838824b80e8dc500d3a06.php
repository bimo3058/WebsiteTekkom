<?php if (isset($component)) { $__componentOriginalc437526b4c56b06f9c16ab6722a84925 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalc437526b4c56b06f9c16ab6722a84925 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'eoffice::components.manajemen-praktikum.layout','data' => ['pageTitle' => 'Dashboard Asisten Praktikum']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('eoffice::manajemen-praktikum.layout'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['pageTitle' => 'Dashboard Asisten Praktikum']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>


<?php
    /** @var \Modules\EOffice\Models\AsistenPraktikum $asprak */
    /** @var \Illuminate\Support\Collection|\Modules\EOffice\Models\AsistenPraktikum[] $allAsprak */
    /** @var \Illuminate\Database\Eloquent\Collection|\Modules\EOffice\Models\Tugas[] $tugasMendatang */
    /** @var \Illuminate\Database\Eloquent\Collection|\Modules\EOffice\Models\PengumpulanTugas[] $pengumpulanPending */
    /** @var \Illuminate\Database\Eloquent\Collection|\Modules\EOffice\Models\Pengumuman[] $pengumumanTerbaru */
?><?php
    $name = auth()->user()->name;
    $nameParts = explode(' ', $name);
    $firstName = $nameParts[0];
?>


<div class="mp-page-header">
    <div>
        <div style="display:flex;align-items:center;gap:8px;margin-bottom:4px;">
            <h1 class="mp-page-title">Dashboard Asisten Praktikum</h1>
            <span class="mp-badge success sm"><span class="dot"></span>Asisten Praktikum</span>
        </div>
        <p class="mp-page-sub">
            Halo, <?php echo e($firstName); ?> · <?php echo e(now()->locale('id')->isoFormat('dddd, D MMMM YYYY')); ?> · <?php echo e($semesterLabel); ?>

            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(isset($asprak) && $asprak?->praktikum): ?> · <?php echo e($asprak->praktikum->nama); ?> <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </p>
    </div>
    <div class="mp-page-actions">
        <a href="<?php echo e(route('eoffice.manprak.asprak.tugas.create')); ?>" class="mp-btn secondary md" style="text-decoration:none;">
            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
            Buat Tugas
        </a>
        <a href="<?php echo e(route('eoffice.manprak.asprak.materi.index')); ?>" class="mp-btn primary md" style="text-decoration:none;">
            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15v4a2 2 0 01-2 2H5a2 2 0 01-2-2v-4M17 8l-5-5-5 5M12 3v12"/></svg>
            Upload Materi
        </a>
    </div>
</div>


<?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(isset($allAsprak) && $allAsprak->count() > 1): ?>
<div style="display:flex;align-items:center;gap:8px;flex-shrink:0;flex-wrap:wrap;padding:12px 0;border-bottom:1px solid #DFE1E7;">
    <span style="font-size:11px;font-weight:600;color:#666D80;text-transform:uppercase;letter-spacing:.06em;">Praktikum:</span>
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $allAsprak; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $ap): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
    <a href="<?php echo e(route('eoffice.manprak.asprak.dashboard')); ?>?praktikum_id=<?php echo e($ap->praktikum_id); ?>"
       class="<?php echo e($ap->id === $asprak->id ? 'mp-btn primary sm' : 'mp-btn secondary sm'); ?>"
       style="text-decoration:none;">
        <?php echo e($ap->praktikum?->nama ?? 'Praktikum'); ?>

    </a>
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
</div>
<?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

<?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(!isset($asprak) || !$asprak): ?>
<div class="mp-alert warning flex-shrink-0">
    <div style="font-size:13px;font-weight:600;margin-bottom:4px;">Status asisten praktikum Anda belum aktif.</div>
    <div style="font-size:12px;">Hubungi koordinator untuk mengaktifkan akun asisten praktikum Anda.</div>
</div>
<?php else: ?>


<div class="sec-head">
    <span class="sec-bar"></span>
    <span class="sec-title">Ringkasan</span>
    <span class="sec-rule"></span>
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(($tugasPendingNilai ?? 0) > 0): ?>
    <span class="mp-badge warning sm"><span class="dot"></span><?php echo e($tugasPendingNilai); ?> perlu dinilai</span>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
</div>


<div class="mp-stats-grid cols-4">
    <div class="mp-stat">
        <div class="mp-stat-icon navy">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M12 2L2 7l10 5 10-5-10-5zM2 17l10 5 10-5M2 12l10 5 10-5"/></svg>
        </div>
        <div class="mp-stat-label">Modul Diampu</div>
        <div class="mp-stat-value"><?php echo e($totalModul ?? 0); ?></div>
        <div class="mp-stat-sub">modul dikelola</div>
    </div>
    <div class="mp-stat">
        <div class="mp-stat-icon sky">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8zM14 2v6h6M16 13H8M16 17H8M10 9H8"/></svg>
        </div>
        <div class="mp-stat-label">Total Materi</div>
        <div class="mp-stat-value"><?php echo e($totalMateri ?? 0); ?></div>
        <div class="mp-stat-sub">materi diunggah</div>
    </div>
    <div class="mp-stat">
        <div class="mp-stat-icon yellow">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M11 4H4a2 2 0 00-2 2v14a2 2 0 002 2h14a2 2 0 002-2v-7M18.5 2.5a2.121 2.121 0 013 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
        </div>
        <div class="mp-stat-label">Tugas Perlu Dinilai</div>
        <div class="mp-stat-value"><?php echo e($tugasPendingNilai ?? 0); ?></div>
        <div class="mp-stat-sub">pengumpulan masuk</div>
    </div>
    <div class="mp-stat">
        <div class="mp-stat-icon green">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
        </div>
        <div class="mp-stat-label">Absensi Hari Ini</div>
        <div class="mp-stat-value"><?php echo e($absensiHariIni ?? 0); ?></div>
        <div class="mp-stat-sub">sesi tercatat</div>
    </div>
</div>


<div class="sec-head">
    <span class="sec-bar"></span>
    <span class="sec-title">Modul yang Diampu</span>
    <span class="sec-rule"></span>
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(isset($modulDiampu) && !$modulDiampu->isEmpty()): ?>
    <span class="mp-badge navy sm"><?php echo e($modulDiampu->count()); ?> modul</span>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
</div>

<div class="mp-card flex-shrink-0">
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(!isset($modulDiampu) || $modulDiampu->isEmpty()): ?>
    <div style="padding:40px;text-align:center;">
        <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="#A4ABB8" stroke-width="1.5" stroke-linecap="round" style="margin:0 auto 12px;display:block;"><path d="M12 2L2 7l10 5 10-5-10-5zM2 17l10 5 10-5M2 12l10 5 10-5"/></svg>
        <div style="font-size:13px;font-weight:500;color:#666D80;">Belum ada modul yang diampu.</div>
        <div style="font-size:12px;color:#808897;margin-top:4px;">Hubungi koordinator untuk pendistribusian modul.</div>
    </div>
    <?php else: ?>
    <div style="display:grid;grid-template-columns:repeat(3,1fr);gap:12px;padding:16px;">
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $modulDiampu; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $m): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
        <div style="border:1px solid #DFE1E7;border-radius:14px;padding:16px;transition:border-color .15s,box-shadow .15s;"
             onmouseover="this.style.borderColor='#B7C2DE';this.style.boxShadow='0 4px 14px rgba(11,38,110,.07)'"
             onmouseout="this.style.borderColor='#DFE1E7';this.style.boxShadow=''">
            <div style="display:flex;align-items:flex-start;gap:10px;margin-bottom:12px;">
                <div class="mp-stat-icon navy" style="flex-shrink:0;">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M4 19.5A2.5 2.5 0 016.5 17H20M6.5 2H20v20H6.5A2.5 2.5 0 014 19.5v-15A2.5 2.5 0 016.5 2z"/></svg>
                </div>
                <div class="min-w-0">
                    <div style="font-size:13px;font-weight:700;color:#0D0D12;margin-bottom:3px;" class="truncate"><?php echo e($m->nama ?? 'Modul'); ?></div>
                    <div style="font-size:11px;color:#666D80;" class="line-clamp-2"><?php echo e($m->deskripsi ?? '—'); ?></div>
                </div>
            </div>
            <div class="flex gap-2">
                <a href="<?php echo e(route('eoffice.manprak.asprak.absensi.show', $m->id)); ?>"
                   class="mp-btn secondary sm flex-1 text-center" style="text-decoration:none;">Absensi</a>
                <a href="<?php echo e(route('eoffice.manprak.asprak.tugas.index')); ?>"
                   class="mp-btn primary sm flex-1 text-center" style="text-decoration:none;">Tugas</a>
            </div>
        </div>
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
    </div>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
</div>


<div class="sec-head">
    <span class="sec-bar"></span>
    <span class="sec-title">Aktivitas</span>
    <span class="sec-rule"></span>
</div>


<div class="flex gap-[14px] flex-1 min-h-0 mb-1">

    
    <div class="mp-card min-w-0" style="flex:2;">
        <div class="mp-card-header" style="flex-shrink:0;">
            <span class="mp-card-title">Pengumpulan Tugas</span>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(($tugasPendingNilai ?? 0) > 0): ?>
            <span class="mp-badge warning sm"><span class="dot"></span><?php echo e($tugasPendingNilai); ?> belum dinilai</span>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            <div class="right">
                <a href="<?php echo e(route('eoffice.manprak.asprak.tugas.index')); ?>" class="mp-btn secondary sm" style="text-decoration:none;">Lihat Semua <?php if (isset($component)) { $__componentOriginal22b2b62d2a6bf157ddbb8ecb4421b995 = $component; } ?>
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
        </div>
        <div class="overflow-y-auto flex-1">
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $pengumpulanPending ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $peng): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
            <div class="mp-tr flex items-center justify-between" style="padding:12px 20px;">
                <div class="flex items-center gap-[10px] min-w-0">
                    <div class="mp-av green"><?php echo e(strtoupper(substr($peng->daftarPraktikan?->user?->name ?? 'M', 0, 1))); ?><?php echo e(strtoupper(substr($peng->daftarPraktikan?->user?->name ?? 'M', strpos(($peng->daftarPraktikan?->user?->name ?? 'M').' ',' ')+1, 1))); ?></div>
                    <div class="min-w-0">
                        <div style="font-size:13px;font-weight:500;color:#0D0D12;" class="truncate"><?php echo e($peng->daftarPraktikan?->user?->name ?? '—'); ?></div>
                        <div style="font-size:11px;color:#666D80;"><?php echo e($peng->tugas?->judul ?? '—'); ?></div>
                    </div>
                </div>
                <div class="flex items-center gap-2 flex-shrink-0">
                    <span style="font-size:11px;color:#808897;"><?php echo e($peng->created_at?->diffForHumans()); ?></span>
                    <a href="<?php echo e(route('eoffice.manprak.asprak.tugas.pengumpulan', $peng->tugas_id)); ?>"
                       class="mp-btn ghost sm" style="text-decoration:none;">Nilai</a>
                </div>
            </div>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
            <div style="padding:40px;text-align:center;">
                <svg width="36" height="36" viewBox="0 0 24 24" fill="none" stroke="#A4ABB8" stroke-width="1.5" stroke-linecap="round" style="margin:0 auto 12px;display:block;"><polyline points="20 6 9 17 4 12"/></svg>
                <div style="font-size:13px;font-weight:500;color:#666D80;">Semua pengumpulan sudah dinilai.</div>
            </div>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </div>
    </div>

    
    <div class="mp-card flex-1 min-w-0">
        <div class="mp-card-header" style="flex-shrink:0;">
            <span class="mp-card-title">Tugas Mendatang</span>
            <div class="right">
                <a href="<?php echo e(route('eoffice.manprak.asprak.tugas.create')); ?>" class="mp-btn primary sm" style="text-decoration:none;">
                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                    Buat
                </a>
            </div>
        </div>
        <div class="overflow-y-auto flex-1">
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $tugasMendatang ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $t): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
            <?php
                $sisa = now()->diffInDays(\Carbon\Carbon::parse($t->deadline), false);
                $warn = $sisa <= 2;
            ?>
            <div style="display:flex;align-items:flex-start;gap:10px;padding:12px 20px;border-bottom:1px solid #ECEFF3;">
                <div style="width:6px;height:6px;border-radius:999px;margin-top:6px;flex-shrink:0;background:<?php echo e($warn ? '#DF1C41' : '#40C4AA'); ?>;"></div>
                <div class="flex-1 min-w-0">
                    <div style="font-size:13px;font-weight:500;color:#0D0D12;"><?php echo e($t->judul); ?></div>
                    <div style="font-size:11px;margin-top:3px;color:<?php echo e($warn ? '#DF1C41' : '#666D80'); ?>;font-weight:<?php echo e($warn ? '600' : '400'); ?>;">
                        Deadline: <?php echo e(\Carbon\Carbon::parse($t->deadline)->locale('id')->isoFormat('D MMM YYYY, HH:mm')); ?>

                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($warn && $sisa >= 0): ?> <span style="opacity:.8;">(<?php echo e($sisa); ?> hari lagi)</span> <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($sisa < 0): ?> <span>(sudah lewat)</span> <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </div>
                </div>
            </div>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
            <div style="padding:40px;text-align:center;">
                <svg width="36" height="36" viewBox="0 0 24 24" fill="none" stroke="#A4ABB8" stroke-width="1.5" stroke-linecap="round" style="margin:0 auto 12px;display:block;"><path d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                <div style="font-size:13px;font-weight:500;color:#666D80;">Tidak ada tugas mendatang.</div>
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
<?php /**PATH C:\WebsiteTekkom - Copy\Modules\EOffice\resources\views\manajemen-praktikum\asprak\dashboard.blade.php ENDPATH**/ ?>