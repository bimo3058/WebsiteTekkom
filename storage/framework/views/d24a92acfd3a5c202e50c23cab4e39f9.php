<?php if (isset($component)) { $__componentOriginalc437526b4c56b06f9c16ab6722a84925 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalc437526b4c56b06f9c16ab6722a84925 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'eoffice::components.manajemen-praktikum.layout','data' => ['pageTitle' => 'Detail Modul']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('eoffice::manajemen-praktikum.layout'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['pageTitle' => 'Detail Modul']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>



<div class="mp-page-header">
    <div>
        <div style="display:flex;align-items:center;gap:8px;margin-bottom:4px;">
            <h1 class="mp-page-title"><?php echo e($modul->nama); ?></h1>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($isAssigned): ?>
            <span class="mp-badge success sm"><span class="dot"></span>Diampu Anda</span>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </div>
        <p class="mp-page-sub">
            <?php echo e($modul->praktikum?->nama); ?> &middot; Urutan <?php echo e($modul->urutan); ?> &middot; <?php echo e($modul->jadwal_minggu ?? 'Jadwal belum diisi'); ?>

        </p>
    </div>
    <div class="mp-page-actions">
        <a href="<?php echo e(route('eoffice.manprak.asprak.modul.index')); ?>" class="mp-btn secondary md" style="text-decoration:none;">← Kembali</a>
    </div>
</div>


<div class="mp-stats-grid cols-4">
    <div class="mp-stat">
        <div class="mp-stat-icon navy">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round">
                <path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/><polyline points="14 2 14 8 20 8"/>
            </svg>
        </div>
        <div>
            <div class="mp-stat-label">Materi</div>
            <div class="mp-stat-value"><?php echo e($modul->materi->count()); ?></div>
        </div>
    </div>
    <div class="mp-stat">
        <div class="mp-stat-icon sky">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round">
                <path d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
            </svg>
        </div>
        <div>
            <div class="mp-stat-label">Tugas</div>
            <div class="mp-stat-value"><?php echo e($modul->tugas->count()); ?></div>
        </div>
    </div>
    <div class="mp-stat">
        <div class="mp-stat-icon green">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round">
                <path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 00-3-3.87M16 3.13a4 4 0 010 7.75"/>
            </svg>
        </div>
        <div>
            <div class="mp-stat-label">Asisten Praktikum</div>
            <div class="mp-stat-value"><?php echo e($modul->modulAsprak->count()); ?></div>
        </div>
    </div>
    <div class="mp-stat">
        <div class="mp-stat-icon yellow">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round">
                <path d="M20 21v-2a4 4 0 00-4-4H8a4 4 0 00-4 4v2"/><circle cx="12" cy="7" r="4"/>
            </svg>
        </div>
        <div>
            <div class="mp-stat-label">Praktikan</div>
            <div class="mp-stat-value"><?php echo e($daftarPraktikan->count()); ?></div>
        </div>
    </div>
</div>


<?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($modul->deskripsi): ?>
<div class="mp-card flex-shrink-0" style="padding:20px;">
    <div style="font-weight:700;font-size:13px;color:#0D0D12;margin-bottom:8px;">Deskripsi Modul</div>
    <div style="font-size:13px;color:#353849;line-height:1.6;"><?php echo e($modul->deskripsi); ?></div>
</div>
<?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

<div style="display:grid;grid-template-columns:340px 1fr;gap:14px;flex:1;min-height:0;">

    
    <div style="display:flex;flex-direction:column;gap:14px;">

        
        <div class="mp-card flex-shrink-0" style="padding:20px;">
            <div class="sec-head" style="margin-bottom:16px;">
                <span class="sec-bar"></span>
                <span class="sec-title">Edit Modul</span>
            </div>
            <form method="POST" action="<?php echo e(route('eoffice.manprak.asprak.modul.update', $modul->id)); ?>"
                  style="display:flex;flex-direction:column;gap:12px;">
                <?php echo csrf_field(); ?> <?php echo method_field('PUT'); ?>
                <div>
                    <label style="display:block;font-size:12px;font-weight:600;margin-bottom:6px;color:#353849;">Nama Modul</label>
                    <input name="nama" value="<?php echo e($modul->nama); ?>" required class="mp-input" style="width:100%;">
                </div>
                <div style="display:grid;grid-template-columns:1fr 1fr;gap:8px;">
                    <div>
                        <label style="display:block;font-size:12px;font-weight:600;margin-bottom:6px;color:#353849;">Urutan</label>
                        <input type="number" name="urutan" value="<?php echo e($modul->urutan); ?>" min="1" required class="mp-input" style="width:100%;">
                    </div>
                    <div>
                        <label style="display:block;font-size:12px;font-weight:600;margin-bottom:6px;color:#353849;">Jadwal</label>
                        <input name="jadwal_minggu" value="<?php echo e($modul->jadwal_minggu); ?>" class="mp-input" style="width:100%;">
                    </div>
                </div>
                <div>
                    <label style="display:block;font-size:12px;font-weight:600;margin-bottom:6px;color:#353849;">Deskripsi</label>
                    <textarea name="deskripsi" rows="3" class="mp-input" style="width:100%;"><?php echo e($modul->deskripsi); ?></textarea>
                </div>
                <button class="mp-btn primary md">Simpan Perubahan</button>
            </form>
        </div>

        
        <div class="mp-card flex-shrink-0" style="padding:20px;">
            <div class="sec-head" style="margin-bottom:12px;">
                <span class="sec-bar"></span>
                <span class="sec-title">Asisten Praktikum Pengampu</span>
            </div>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $modul->modulAsprak; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $ma): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
            <div style="display:flex;align-items:center;gap:12px;padding:10px 0;border-bottom:1px solid #DFE1E7;">
                <div class="mp-av green"><?php echo e(strtoupper(substr($ma->asprak?->user?->name ?? 'A', 0, 2))); ?></div>
                <div style="flex:1;min-width:0;">
                    <div style="font-size:13px;font-weight:600;color:#0D0D12;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;"><?php echo e($ma->asprak?->user?->name ?? '—'); ?></div>
                    <div style="font-size:11px;color:#666D80;"><?php echo e($ma->asprak?->user?->email ?? ''); ?></div>
                </div>
            </div>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
            <div style="font-size:13px;color:#808897;">Belum ada asisten praktikum pengampu.</div>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </div>

    </div>

    
    <div style="display:flex;flex-direction:column;gap:14px;overflow-y:auto;">

        
        <div class="mp-card flex-shrink-0">
            <div class="mp-card-header">
                <span class="mp-card-title">Materi Modul</span>
                <div class="right">
                    <a href="<?php echo e(route('eoffice.manprak.asprak.materi.index')); ?>"
                       class="mp-btn ghost sm" style="text-decoration:none;">Kelola Materi →</a>
                </div>
            </div>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $modul->materi; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $materi): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
            <div class="mp-tr" style="display:flex;align-items:center;justify-content:space-between;gap:12px;padding:10px 20px;border-bottom:1px solid #DFE1E7;">
                <div style="display:flex;align-items:center;gap:12px;min-width:0;">
                    <div class="mp-stat-icon navy" style="width:32px;height:32px;border-radius:8px;flex-shrink:0;">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round">
                            <path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/><polyline points="14 2 14 8 20 8"/>
                        </svg>
                    </div>
                    <div style="min-width:0;">
                        <div style="font-weight:600;font-size:13px;color:#0D0D12;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;"><?php echo e($materi->judul); ?></div>
                        <div style="font-size:11px;color:#666D80;"><?php echo e($materi->created_at?->format('d M Y')); ?></div>
                    </div>
                </div>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($materi->file_path): ?>
                <a href="<?php echo e(app(\App\Services\SupabaseStorage::class)->publicUrl($materi->file_path, 'eoffice')); ?>" target="_blank" class="mp-btn primary sm flex-shrink-0" style="text-decoration:none;">Unduh</a>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </div>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
            <div style="padding:32px;text-align:center;font-size:13px;color:#808897;">Belum ada materi.</div>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </div>

        
        <div class="mp-card flex-shrink-0">
            <div class="mp-card-header">
                <span class="mp-card-title">Tugas Modul</span>
                <div class="right">
                    <a href="<?php echo e(route('eoffice.manprak.asprak.tugas.index')); ?>"
                       class="mp-btn ghost sm" style="text-decoration:none;">Kelola Tugas →</a>
                </div>
            </div>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $modul->tugas; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $tugas): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
            <?php
                $totalKumpul   = $tugas->pengumpulan->count();
                $totalDinilai  = $tugas->pengumpulan->where('status_pengumpulan', 'dinilai')->count();
                $totalPending  = $tugas->pengumpulan->where('status_pengumpulan', 'belum_dicek')->count();
            ?>
            <div class="mp-tr" style="padding:12px 20px;border-bottom:1px solid #DFE1E7;">
                <div style="display:flex;align-items:flex-start;justify-content:space-between;gap:12px;">
                    <div>
                        <div style="font-weight:600;font-size:13px;color:#0D0D12;"><?php echo e($tugas->judul); ?></div>
                        <div style="font-size:11px;color:#666D80;margin-top:2px;">
                            Deadline: <?php echo e($tugas->deadline?->format('d M Y H:i') ?? 'Tanpa deadline'); ?>

                        </div>
                    </div>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($tugas->is_published): ?>
                    <span class="mp-badge success sm flex-shrink-0"><span class="dot"></span>Dipublikasikan</span>
                    <?php else: ?>
                    <span class="mp-badge neutral sm flex-shrink-0">Draft</span>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div>
                <div style="display:flex;gap:12px;margin-top:8px;font-size:11px;color:#666D80;">
                    <span><?php echo e($totalKumpul); ?> pengumpulan</span>
                    <span style="<?php echo e($totalPending > 0 ? 'color:#D39C3D;font-weight:600;' : ''); ?>"><?php echo e($totalPending); ?> belum dinilai</span>
                    <span><?php echo e($totalDinilai); ?> sudah dinilai</span>
                </div>
            </div>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
            <div style="padding:32px;text-align:center;font-size:13px;color:#808897;">Belum ada tugas.</div>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </div>

        
        <div class="mp-card flex-shrink-0">
            <div class="mp-card-header">
                <span class="mp-card-title">Rekap Absensi</span>
                <div class="right">
                    <a href="<?php echo e(route('eoffice.manprak.asprak.absensi.show', $modul->id)); ?>"
                       class="mp-btn ghost sm" style="text-decoration:none;">Kelola Absensi →</a>
                </div>
            </div>
            <?php
                $absensiGroup = $modul->absensi->groupBy('tanggal');
            ?>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $absensiGroup; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $tgl => $absList): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
            <?php
                $hadir = $absList->where('status','hadir')->count();
                $total = $absList->count();
                $pct   = $total > 0 ? round($hadir / $total * 100) : 0;
            ?>
            <div class="mp-tr" style="display:flex;align-items:center;justify-content:space-between;padding:12px 20px;border-bottom:1px solid #DFE1E7;">
                <div style="font-size:13px;font-weight:500;color:#0D0D12;">
                    <?php echo e(\Carbon\Carbon::parse($tgl)->isoFormat('dddd, D MMMM YYYY')); ?>

                </div>
                <div style="display:flex;align-items:center;gap:8px;">
                    <span style="font-size:12px;color:#666D80;"><?php echo e($hadir); ?>/<?php echo e($total); ?> hadir</span>
                    <div style="width:60px;background:#F0F1F4;border-radius:999px;height:6px;">
                        <div style="height:6px;border-radius:999px;background:#0B266E;width:<?php echo e($pct); ?>%"></div>
                    </div>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($pct >= 75): ?>
                    <span class="mp-badge success sm"><?php echo e($pct); ?>%</span>
                    <?php else: ?>
                    <span class="mp-badge error sm"><?php echo e($pct); ?>%</span>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div>
            </div>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
            <div style="padding:32px;text-align:center;font-size:13px;color:#808897;">Belum ada data absensi.</div>
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
<?php endif; ?>
<?php /**PATH C:\WebsiteTekkom - Copy\Modules\EOffice\resources\views\manajemen-praktikum_backup_kode\asprak\modul-detail.blade.php ENDPATH**/ ?>