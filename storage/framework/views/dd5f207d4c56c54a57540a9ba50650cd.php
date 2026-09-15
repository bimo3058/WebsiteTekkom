<?php
    $praktikumRoute = match ($dashboardRole) {
        'admin' => 'eoffice.manprak.admin.dashboard',
        'dosen' => 'eoffice.manprak.dosen.dashboard',
        default => 'eoffice.manprak.mahasiswa.dashboard',
    };
    $kpRoute = match ($dashboardRole) {
        'admin' => 'eoffice.kp.koordinator.dashboard',
        'dosen' => 'eoffice.kp.dosen.dashboard',
        default => 'eoffice.kp.mahasiswa.dashboard',
    };
?>
<div class="eo-dashboard-header">
    <div>
        <div class="eo-heading-row">
            <h1 id="eo-dashboard-title">Dashboard</h1>
            <span class="eo-role-badge"><?php echo e(ucfirst($dashboardRole)); ?></span>
        </div>
        <p class="eo-welcome"><span>Selamat datang kembali, <strong><?php echo e($name); ?></strong></span><span class="eo-welcome-separator" aria-hidden="true">&middot;</span><time datetime="<?php echo e(now()->toDateString()); ?>"><?php echo e(now()->locale('id')->translatedFormat('l, d F Y')); ?></time></p>
    </div>
    <div class="eo-header-actions">
        <a href="<?php echo e(route($kpRoute)); ?>" class="eo-action">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="<?php echo e($iKP); ?>"/></svg>
            Kerja Praktik
        </a>
        <a href="<?php echo e(route($praktikumRoute)); ?>" class="eo-action eo-action-primary">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="<?php echo e($iPraktikum); ?>"/></svg>
            <?php echo e($dashboardRole === 'mahasiswa' ? 'Buka Praktikum' : 'Kelola Praktikum'); ?>

        </a>
    </div>
</div>
<?php /**PATH C:\WebsiteTekkom - Copy\Modules\EOffice\resources\views\dashboard\_header.blade.php ENDPATH**/ ?>