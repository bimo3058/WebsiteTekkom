<?php if (isset($component)) { $__componentOriginal544c4f22773da773738fd88478b17597 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal544c4f22773da773738fd88478b17597 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'eoffice::components.manajemen-ruangan.layout','data' => ['pageTitle' => 'Dashboard Admin']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('eoffice::manajemen-ruangan.layout'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['pageTitle' => 'Dashboard Admin']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>


    <div class="mp-page-header">
        <div>
            <h1 class="mp-page-title">Dashboard Admin Ruangan</h1>
            <p class="mp-page-sub">Pantau aktivitas peminjaman, jumlah ruangan terpakai, dan tiket approval hari ini.
            </p>
        </div>
    </div>
    <style>
        .mp-stat.clickable {
            text-decoration: none;
            transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
            cursor: pointer;
            display: block;
        }

        .mp-stat.clickable:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.05), 0 4px 6px -2px rgba(0, 0, 0, 0.025);
            border-color: #d1d5db;
        }
    </style>

    <div class="mp-stats-grid cols-3" style="margin-top: 20px;">
        <a href="<?php echo e(route('eoffice.peminjaman.admin.ruangan.index')); ?>" class="mp-stat clickable">
            <div class="mp-stat-icon sky"><svg width="18" height="18" viewBox="0 0 24 24" fill="none"
                    stroke="currentColor" stroke-width="2" stroke-linecap="round">
                    <path d="M4 19.5V4.5C4 3.11929 5.11929 2 6.5 2H20V22H6.5C5.11929 22 4 20.8807 4 19.5Z" />
                </svg></div>
            <div class="mp-stat-label">Total Ruangan Aktif</div>
            <div class="mp-stat-value"><?php echo e(number_format($totalRuangan ?? 0)); ?></div>
            <div class="mp-stat-sub">Siap dipinjam</div>
        </a>
        <a href="<?php echo e(route('eoffice.peminjaman.admin.persetujuan.index')); ?>" class="mp-stat clickable">
            <div class="mp-stat-icon yellow"><svg width="18" height="18" viewBox="0 0 24 24" fill="none"
                    stroke="currentColor" stroke-width="2" stroke-linecap="round">
                    <circle cx="12" cy="12" r="10" />
                    <polyline points="12 6 12 12 16 14" />
                </svg></div>
            <div class="mp-stat-label">Menunggu Approval</div>
            <div class="mp-stat-value" <?php echo ($pendingApproval ?? 0) > 0 ? 'style="color: #D97706;"' : ''; ?>>
                <?php echo e(number_format($pendingApproval ?? 0)); ?></div>
            <div class="mp-stat-sub">Butuh tindakan admin</div>
        </a>
        <div class="mp-stat">
            <div class="mp-stat-icon violet"><svg width="18" height="18" viewBox="0 0 24 24" fill="none"
                    stroke="currentColor" stroke-width="2" stroke-linecap="round">
                    <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                    <line x1="16" y1="2" x2="16" y2="6"></line>
                    <line x1="8" y1="2" x2="8" y2="6"></line>
                    <line x1="3" y1="10" x2="21" y2="10"></line>
                </svg></div>
            <div class="mp-stat-label">Digunakan Hari Ini</div>
            <div class="mp-stat-value"><?php echo e(number_format($dipakaiHariIni ?? 0)); ?></div>
            <div class="mp-stat-sub">Jadwal disetujui hari ini</div>
        </div>
    </div>

    <div class="mp-card" style="margin-top: 24px;">
        <div class="mp-card-header">
            <h3 class="mp-card-title">Ruangan Baru Ditambahkan</h3>
            <div class="right">
                <a href="<?php echo e(route('eoffice.peminjaman.admin.ruangan.index')); ?>" class="mp-btn secondary sm">Lihat
                    Semua</a>
            </div>
        </div>
        <div class="mp-card-body">
            <div class="mp-table-wrap">
                <table class="mp-table">
                    <thead>
                        <tr>
                            <th>NAMA RUANG</th>
                            <th>LOKASI</th>
                            <th>KAPASITAS</th>
                            <th>JADWAL TERPAKAI</th>
                            <th>STATUS</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $recentRuangan; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $r): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                            <tr class="mp-tr">
                                <td style="font-weight: 600;"><?php echo e($r->nama); ?></td>
                                <td><?php echo e($r->lokasi); ?> <span style="font-size:11px; color:#A4ABB8;">(Lt.
                                        <?php echo e($r->lantai ?? '-'); ?>)</span></td>
                                <td><?php echo e($r->kapasitas); ?> Orang</td>
                                <td><span style="font-size:12px; color:#A4ABB8;">Segera Hadir...</span></td>
                                <td>
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($r->is_active): ?>
                                        <span class="mp-badge success sm">Aktif</span>
                                    <?php else: ?>
                                        <span class="mp-badge sm" style="background:#FADAE1; color:#710E21;">Non-aktif</span>
                                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                </td>
                            </tr>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                            <tr>
                                <td colspan="5" style="text-align:center; padding: 30px; color: #666D80;">
                                    Belum ada data ruangan yang ditambahkan.<br>
                                    <a href="<?php echo e(route('eoffice.peminjaman.admin.ruangan.create')); ?>"
                                        style="color:#0B266E; font-weight:600; text-decoration:none; margin-top:8px; display:inline-block;">+
                                        Tambah Ruangan</a>
                                </td>
                            </tr>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

 <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal544c4f22773da773738fd88478b17597)): ?>
<?php $attributes = $__attributesOriginal544c4f22773da773738fd88478b17597; ?>
<?php unset($__attributesOriginal544c4f22773da773738fd88478b17597); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal544c4f22773da773738fd88478b17597)): ?>
<?php $component = $__componentOriginal544c4f22773da773738fd88478b17597; ?>
<?php unset($__componentOriginal544c4f22773da773738fd88478b17597); ?>
<?php endif; ?><?php /**PATH C:\WebsiteTekkom - Copy\Modules\EOffice\resources\views\manajemen-ruangan\admin\dashboard.blade.php ENDPATH**/ ?>