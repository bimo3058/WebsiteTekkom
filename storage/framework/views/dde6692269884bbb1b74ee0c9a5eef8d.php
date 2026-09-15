
<?php
    $active = $active ?? 'home';
?>

<aside data-mobile-sidebar class="sidebar">
    <div class="sidebar-brand">
        <div class="brand-icon"><i class="fas fa-university"></i></div>
        <div class="brand-text">
            <strong>Departemen Teknik Komputer</strong>
            <span>Universitas Wakamsi</span>
        </div>
    </div>
    <nav class="sidebar-nav">
        <a href="<?php echo e(route('banksoal.dashboard')); ?>" class="nav-item <?php echo e($active === 'home' ? 'active' : ''); ?>">
            <span class="nav-icon"><i class="fas fa-th-large"></i></span> Home
        </a>
        <a href="<?php echo e(route('banksoal.rps.dosen.index')); ?>" class="nav-item <?php echo e($active === 'rps' ? 'active' : ''); ?>">
            <span class="nav-icon"><i class="fas fa-file-alt"></i></span> Manajemen RPS
        </a>
        <a href="<?php echo e(route('banksoal.soal.dosen.index')); ?>" class="nav-item <?php echo e($active === 'bank-soal' ? 'active' : ''); ?>">
            <span class="nav-icon"><i class="fas fa-database"></i></span> Bank Soal
        </a>
        <a href="<?php echo e(route('banksoal.arsip.dosen.index')); ?>" class="nav-item <?php echo e($active === 'arsip' ? 'active' : ''); ?>">
            <span class="nav-icon"><i class="fas fa-archive"></i></span> Arsip Soal
        </a>
    </nav>
</aside>
<?php /**PATH C:\WebsiteTekkom - Copy\Modules\BankSoal\resources\views\partials\dosen\sidebar.blade.php ENDPATH**/ ?>