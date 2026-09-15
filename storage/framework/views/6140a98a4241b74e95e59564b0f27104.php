<?php
    $accountRole = match (true) {
        $user->hasRole('superadmin') => 'Superadmin',
        $user->hasRole('admin_eoffice') => 'Admin E-Office',
        $user->hasRole('dosen') => 'Dosen',
        $user->hasRole('mahasiswa') => 'Mahasiswa',
        $user->hasRole('koor_prak') => 'Koordinator Praktikum',
        $user->hasRole('asprak') => 'Asisten Praktikum',
        default => 'Pengguna E-Office',
    };
?>
<header class="eo-topbar">
    <nav class="eo-breadcrumb" aria-label="Breadcrumb">
        <button type="button" x-ref="eoMenuButton" class="eo-icon-button eo-mobile-menu" @click="sidebarOpen = !sidebarOpen; if (sidebarOpen) $nextTick(() => document.querySelector('#eo-navigation .eo-sidebar-toggle').focus())" :aria-expanded="sidebarOpen" aria-controls="eo-navigation" aria-label="Buka menu navigasi">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" aria-hidden="true"><path d="M4 6h16M4 12h16M4 18h16"/></svg>
        </button>
        <a href="<?php echo e(route('eoffice.dashboard')); ?>">E-Office</a>
        <span class="eo-breadcrumb-separator" aria-hidden="true">/</span>
        <strong aria-current="page">Dashboard</strong>
    </nav>
    <div class="eo-topbar-right">
        <a href="<?php echo e(route('profile.edit')); ?>" class="eo-topbar-account" aria-label="Pengaturan profil <?php echo e($name); ?>" title="Pengaturan profil">
            <span class="eo-avatar" aria-hidden="true">
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($user->avatar_url): ?>
                    <img src="<?php echo e($user->avatar_url); ?>" alt="" width="34" height="34" referrerpolicy="no-referrer">
                <?php else: ?>
                    <?php echo e($initials); ?>

                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </span>
            <span class="eo-account-meta">
                <strong><?php echo e($name); ?></strong>
                <span><?php echo e($accountRole); ?></span>
            </span>
        </a>
    </div>
</header>
<?php /**PATH C:\WebsiteTekkom - Copy\Modules/EOffice\resources/views/dashboard/_topbar.blade.php ENDPATH**/ ?>