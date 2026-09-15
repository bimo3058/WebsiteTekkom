<?php if (isset($component)) { $__componentOriginalc437526b4c56b06f9c16ab6722a84925 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalc437526b4c56b06f9c16ab6722a84925 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'eoffice::components.manajemen-praktikum.layout','data' => ['pageTitle' => 'Detail Pengumpulan Tugas']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('eoffice::manajemen-praktikum.layout'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['pageTitle' => 'Detail Pengumpulan Tugas']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>



<div class="mp-page-header">
    <div>
        <div style="display:flex;align-items:center;gap:8px;margin-bottom:4px;">
            <h1 class="mp-page-title">Detail Pengumpulan</h1>
            <span class="mp-badge primary sm"><span class="dot"></span>Dosen</span>
        </div>
        <p class="mp-page-sub"><?php echo e($tugas->judul); ?> · <?php echo e($tugas->modul?->praktikum?->nama); ?></p>
    </div>
    <div class="mp-page-actions">
        <a href="<?php echo e(route('eoffice.manprak.dosen.tugas.index')); ?>" class="mp-btn secondary md" style="text-decoration:none;">
            <?php if (isset($component)) { $__componentOriginal22b2b62d2a6bf157ddbb8ecb4421b995 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal22b2b62d2a6bf157ddbb8ecb4421b995 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'eoffice::components.manajemen-praktikum.ui.icon','data' => ['name' => 'arrow-left']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('eoffice::manajemen-praktikum.ui.icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'arrow-left']); ?>
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
<?php endif; ?> Kembali ke Daftar Tugas
        </a>
    </div>
</div>


<div class="mp-card flex-shrink-0">
    <div class="mp-card-header">
        <span class="mp-card-title">Informasi Tugas</span>
    </div>
    <div class="mp-content-grid" style="padding:16px 20px;">
        <div>
            <div style="font-size:11px;font-weight:600;color:#808897;text-transform:uppercase;letter-spacing:.04em;margin-bottom:4px;">Judul</div>
            <div style="font-size:13px;font-weight:600;color:#0D0D12;"><?php echo e($tugas->judul); ?></div>
        </div>
        <div>
            <div style="font-size:11px;font-weight:600;color:#808897;text-transform:uppercase;letter-spacing:.04em;margin-bottom:4px;">Modul</div>
            <div style="font-size:13px;color:#353849;"><?php echo e($tugas->modul?->nama ?? '—'); ?></div>
        </div>
        <div>
            <div style="font-size:11px;font-weight:600;color:#808897;text-transform:uppercase;letter-spacing:.04em;margin-bottom:4px;">Deadline</div>
            <?php $dl = $tugas->deadline ? \Carbon\Carbon::parse($tugas->deadline) : null; ?>
            <div style="font-size:13px;color:#353849;">
                <?php echo e($dl ? $dl->locale('id')->isoFormat('D MMM YYYY, HH:mm') : 'Tanpa deadline'); ?>

            </div>
        </div>
    </div>
</div>


<div class="mp-stats-grid cols-3 flex-shrink-0">
    <div class="mp-stat">
        <div class="mp-stat-icon navy">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"><path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2M9 11a4 4 0 100-8 4 4 0 000 8"/></svg>
        </div>
        <div class="mp-stat-label">Total Pengumpul</div>
        <div class="mp-stat-value"><?php echo e($pengumpulan->count()); ?></div>
    </div>
    <div class="mp-stat">
        <div class="mp-stat-icon green">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"><polyline points="20 6 9 17 4 12"/></svg>
        </div>
        <div class="mp-stat-label">Sudah Dinilai</div>
        <div class="mp-stat-value"><?php echo e($pengumpulan->whereNotNull('nilai')->count()); ?></div>
    </div>
    <div class="mp-stat">
        <div class="mp-stat-icon yellow">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
        </div>
        <div class="mp-stat-label">Belum Dinilai</div>
        <div class="mp-stat-value"><?php echo e($pengumpulan->whereNull('nilai')->count()); ?></div>
    </div>
</div>


<div class="sec-head">
    <span class="sec-bar"></span>
    <span class="sec-title">Daftar Pengumpulan</span>
    <span class="sec-rule"></span>
    <span class="mp-badge neutral sm"><?php echo e($pengumpulan->count()); ?> mahasiswa</span>
</div>

<div class="mp-card flex-1 min-h-0">
    <div class="overflow-x-auto">
        <table class="w-full" style="font-size:13px;">
            <thead style="background:#F9FAFB;border-bottom:1px solid #DFE1E7;">
                <tr>
                    <th class="mp-th text-left" style="padding:10px 20px;">Mahasiswa</th>
                    <th class="mp-th text-left" style="padding:10px 16px;">File</th>
                    <th class="mp-th text-center" style="padding:10px 16px;">Dikumpul</th>
                    <th class="mp-th text-center" style="padding:10px 16px;">Status</th>
                    <th class="mp-th text-center" style="padding:10px 16px;">Nilai</th>
                </tr>
            </thead>
            <tbody>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $pengumpulan; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $pk): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                <?php
                    $mhs        = $pk->daftarPraktikan?->user;
                    $nameParts  = explode(' ', $mhs?->name ?? 'MH');
                    $initials   = strtoupper(substr($nameParts[0] ?? 'M', 0, 1) . substr($nameParts[1] ?? $nameParts[0] ?? 'H', 0, 1));
                    $avColors   = ['sky','navy','green','yellow','violet'];
                    $avColor    = $avColors[crc32($mhs?->email ?? '') % count($avColors)];
                    $statusColor = match($pk->status ?? '') {
                        'acc'    => ['bg' => '#DDF2EE', 'c' => '#174E43', 'label' => 'ACC'],
                        'revisi' => ['bg' => '#FADAE1', 'c' => '#710E21', 'label' => 'Revisi'],
                        default  => ['bg' => '#F9ECCB', 'c' => '#5B3D1E', 'label' => 'Menunggu'],
                    };
                ?>
                <tr class="mp-tr" style="border-bottom:1px solid #DFE1E7;">
                    <td style="padding:12px 20px;">
                        <div class="flex items-center gap-3">
                            <div class="mp-av <?php echo e($avColor); ?>"><?php echo e($initials); ?></div>
                            <div>
                                <div style="font-weight:600;color:#0D0D12;"><?php echo e($mhs?->name ?? '—'); ?></div>
                                <div style="font-size:11px;color:#666D80;"><?php echo e($mhs?->email); ?></div>
                            </div>
                        </div>
                    </td>
                    <td style="padding:12px 16px;">
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($pk->file_path): ?>
                        <a href="<?php echo e(app(\App\Services\SupabaseStorage::class)->publicUrl($pk->file_path, 'eoffice')); ?>" target="_blank"
                           class="mp-btn secondary sm" style="text-decoration:none;">
                            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M21 15v4a2 2 0 01-2 2H5a2 2 0 01-2-2v-4M7 10l5 5 5-5M12 15V3"/></svg>
                            Unduh
                        </a>
                        <?php else: ?>
                        <span style="font-size:12px;color:#A4ABB8;">—</span>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </td>
                    <td style="padding:12px 16px;text-align:center;font-size:12px;color:#666D80;">
                        <?php echo e($pk->created_at?->locale('id')->isoFormat('D MMM YYYY, HH:mm') ?? '—'); ?>

                    </td>
                    <td style="padding:12px 16px;text-align:center;">
                        <span style="display:inline-flex;align-items:center;gap:4px;padding:3px 8px;border-radius:999px;
                                     font-size:11px;font-weight:600;background:<?php echo e($statusColor['bg']); ?>;color:<?php echo e($statusColor['c']); ?>;">
                            <?php echo e($statusColor['label']); ?>

                        </span>
                    </td>
                    <td style="padding:12px 16px;text-align:center;">
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($pk->nilai !== null): ?>
                        <span style="font-size:16px;font-weight:700;color:#0B266E;"><?php echo e($pk->nilai); ?></span>
                        <?php else: ?>
                        <span style="font-size:12px;color:#A4ABB8;">—</span>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </td>
                </tr>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                <tr>
                    <td colspan="5" style="padding:48px;text-align:center;">
                        <svg width="36" height="36" viewBox="0 0 24 24" fill="none" stroke="#A4ABB8" stroke-width="1.5"
                             stroke-linecap="round" style="margin:0 auto 12px;display:block;">
                            <path d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                        </svg>
                        <div style="font-size:13px;color:#666D80;">Belum ada mahasiswa yang mengumpulkan tugas ini.</div>
                    </td>
                </tr>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </tbody>
        </table>
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
<?php /**PATH C:\WebsiteTekkom - Copy\Modules\EOffice\resources\views\manajemen-praktikum\dosen\tugas-pengumpulan.blade.php ENDPATH**/ ?>