<?php if (isset($component)) { $__componentOriginalc437526b4c56b06f9c16ab6722a84925 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalc437526b4c56b06f9c16ab6722a84925 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'eoffice::components.manajemen-praktikum.layout','data' => ['pageTitle' => 'Seleksi Asprak']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('eoffice::manajemen-praktikum.layout'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['pageTitle' => 'Seleksi Asprak']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>



<div class="mp-page-header">
    <div>
        <div style="display:flex;align-items:center;gap:8px;margin-bottom:4px;">
            <h1 class="mp-page-title">Seleksi Asisten Praktikum</h1>
            <span class="mp-badge" style="background:#E0E7FF;color:#6366F1;border-radius:999px;padding:3px 10px;font-size:11px;font-weight:600;display:inline-flex;align-items:center;gap:5px;"><span class="dot" style="background:#6366F1;"></span>Koordinator</span>
        </div>
        <p class="mp-page-sub">Review dan seleksi calon asprak untuk praktikum yang Anda koordinatori · <?php echo e(now()->locale('id')->isoFormat('dddd, D MMMM YYYY')); ?></p>
    </div>
</div>

<?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($periodeAktif): ?>
<div class="mp-alert success flex-shrink-0">
    <strong>Pendaftaran Sedang Buka:</strong> <?php echo e($periodeAktif->nama); ?>

    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($periodeAktif->ditutup_pada): ?>
    · Ditutup: <?php echo e($periodeAktif->ditutup_pada->format('d M Y H:i')); ?>

    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
</div>
<?php else: ?>
<div class="mp-alert warning flex-shrink-0">
    <strong>Periode pendaftaran sedang tutup.</strong> Hubungi Admin untuk membuka periode pendaftaran baru.
</div>
<?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

<div class="sec-head">
    <span class="sec-bar"></span>
    <span class="sec-title">Daftar Pendaftar Asprak</span>
    <span class="sec-rule"></span>
</div>


<div class="flex gap-2 flex-wrap flex-shrink-0">
    <form method="GET" class="flex gap-2 flex-wrap">
        <input type="text" name="search" value="<?php echo e(request('search')); ?>" placeholder="Cari nama..."
               class="mp-input" style="width:180px;">
        <select name="status" class="mp-input mp-select">
            <option value="">Semua Status</option>
            <option value="pending"  <?php echo e(request('status')=='pending'  ? 'selected' : ''); ?>>Menunggu</option>
            <option value="approved" <?php echo e(request('status')=='approved' ? 'selected' : ''); ?>>Diterima</option>
            <option value="rejected" <?php echo e(request('status')=='rejected' ? 'selected' : ''); ?>>Ditolak</option>
        </select>
        <select name="sort" class="mp-input mp-select">
            <option value="terbaru" <?php echo e(request('sort') == 'terbaru' ? 'selected' : ''); ?>>Terbaru</option>
            <option value="ipk_tertinggi" <?php echo e(request('sort') == 'ipk_tertinggi' ? 'selected' : ''); ?>>IPK Tertinggi</option>
        </select>
        <button type="submit" class="mp-btn primary sm">Filter</button>
    </form>
</div>

<div class="mp-card flex-1 min-h-0">
    <div class="overflow-x-auto flex-1">
        <table class="mp-table">
            <thead>
                <tr style="background:#F9FAFB;">
                    <th class="mp-th text-left" style="padding:10px 16px;">Mahasiswa</th>
                    <th class="mp-th text-left" style="padding:10px 16px;">IPK</th>
                    <th class="mp-th text-left" style="padding:10px 16px;">Motivasi</th>
                    <th class="mp-th text-left" style="padding:10px 16px;">CV / Transkrip</th>
                    <th class="mp-th text-left" style="padding:10px 16px;">Jadwal</th>
                    <th class="mp-th text-left" style="padding:10px 16px;">Status</th>
                    <th class="mp-th text-left" style="padding:10px 16px;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $pendaftaran; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $p): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                <tr class="mp-tr" style="border-bottom:1px solid #DFE1E7;">
                    <td style="padding:12px 16px;">
                        <div class="flex items-center gap-[10px]">
                            <div class="mp-av yellow"><?php echo e(strtoupper(substr($p->user?->name ?? 'M', 0, 2))); ?></div>
                            <div>
                                <div style="font-weight:600;color:#0D0D12;"><?php echo e($p->user?->name ?? '—'); ?></div>
                                <div style="font-size:11px;color:#666D80;"><?php echo e($p->user?->email); ?></div>
                                <div style="font-size:10px;color:#808897;"><?php echo e($p->created_at?->format('d M Y')); ?></div>
                            </div>
                        </div>
                    </td>
                    <td style="padding:12px 16px;font-weight:700;color:<?php echo e(($p->ipk ?? 0) >= 3.0 ? '#16a34a' : '#DF1C41'); ?>;">
                        <?php echo e(number_format($p->ipk ?? 0, 2)); ?>

                    </td>
                    <td style="padding:12px 16px;color:#666D80;max-width:180px;">
                        <div class="line-clamp-2" style="font-size:12px;"><?php echo e($p->motivasi ?? '—'); ?></div>
                    </td>
                    <td style="padding:12px 16px;">
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($p->cv_path): ?>
                        <a href="<?php echo e(app(\App\Services\SupabaseStorage::class)->publicUrl($p->cv_path, 'eoffice')); ?>" target="_blank"
                           style="font-size:11px;font-weight:600;color:#0B266E;text-decoration:none;display:block;" class="hover:underline">CV</a>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($p->transkrip_path): ?>
                        <a href="<?php echo e(app(\App\Services\SupabaseStorage::class)->publicUrl($p->transkrip_path, 'eoffice')); ?>" target="_blank"
                           style="font-size:11px;font-weight:600;color:#0B266E;text-decoration:none;display:block;" class="hover:underline">Transkrip</a>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(!$p->cv_path && !$p->transkrip_path): ?>
                        <span style="font-size:11px;color:#808897;">—</span>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </td>
                    <td style="padding:12px 16px;font-size:11px;color:#666D80;">
                        <?php echo e(collect($p->jadwal ?? [])->join(', ') ?: '—'); ?>

                    </td>
                    <td style="padding:12px 16px;">
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($p->status_koor === 'disetujui'): ?>
                        <div>
                            <span class="mp-badge success sm"><span class="dot"></span>Disetujui Koor</span>
                            <div style="font-size:10px;color:#666D80;margin-top:2px;">Menunggu Admin</div>
                        </div>
                        <?php elseif($p->status_koor === 'ditolak' || $p->status === 'rejected'): ?>
                        <span class="mp-badge error sm"><span class="dot"></span>Ditolak</span>
                        <?php else: ?>
                        <span class="mp-badge warning sm"><span class="dot"></span>Menunggu Review</span>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </td>
                    <td style="padding:12px 16px;">
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($p->status_koor === 'menunggu'): ?>
                        <div class="flex gap-2">
                            <form method="POST" action="<?php echo e(route('eoffice.manprak.koor.pendaftaran-asprak.approve', $p->id)); ?>">
                                <?php echo csrf_field(); ?>
                                <button type="submit" class="mp-btn primary sm">Terima</button>
                            </form>
                            <form method="POST" action="<?php echo e(route('eoffice.manprak.koor.pendaftaran-asprak.reject', $p->id)); ?>" x-data="{ alasan: '' }">
                                <?php echo csrf_field(); ?>
                                <input type="hidden" name="alasan_penolakan" :value="alasan">
                                <button type="button"
                                        @click="alasan = prompt('Alasan penolakan:'); if(alasan !== null) $el.closest('form').submit()"
                                        class="mp-btn destructive sm">Tolak</button>
                            </form>
                        </div>
                        <?php else: ?>
                        <span style="font-size:11px;color:#808897;">Sudah diproses</span>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </td>
                </tr>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                <tr>
                    <td colspan="7">
                        <div style="padding:48px;text-align:center;">
                            <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="#A4ABB8" stroke-width="1.5" stroke-linecap="round" style="margin:0 auto 12px;display:block;"><path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 00-3-3.87"/><path d="M16 3.13a4 4 0 010 7.75"/></svg>
                            <div style="font-size:13px;font-weight:500;color:#666D80;">Belum ada pendaftar asprak.</div>
                        </div>
                    </td>
                </tr>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </tbody>
        </table>
    </div>
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($pendaftaran->hasPages()): ?>
    <div style="padding:12px 16px;border-top:1px solid #DFE1E7;flex-shrink:0;"><?php echo e($pendaftaran->links()); ?></div>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
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
<?php /**PATH C:\WebsiteTekkom - Copy\Modules\EOffice\resources\views\manajemen-praktikum_backup\koordinator\pendaftaran-asprak.blade.php ENDPATH**/ ?>