<?php
    $isAdmin = $user->hasRole('superadmin') || $user->hasRole('admin_eoffice');
    $isDosen = $user->hasRole('dosen');
    $isKoor = $user->hasRole('koor_prak');
    $praktikumRoles = collect([
        'admin' => $isAdmin,
        'dosen' => $isDosen,
        'koor' => $isKoor,
        'asprak' => $user->hasRole('asprak'),
        'mahasiswa' => $user->hasRole('mahasiswa'),
    ])->filter()->keys();
    // Preserve role-specific destinations and the existing multi-role entry.
    $praktikumLink = $praktikumRoles->count() === 1
        ? route('eoffice.manprak.'.$praktikumRoles->first().'.dashboard')
        : route('eoffice.manprak.dashboard');
    $kpLink = match (true) {
        $isAdmin || $user->hasRole('koor_kp') => route('eoffice.kp.koordinator.dashboard'),
        $isDosen => route('eoffice.kp.dosen.dashboard'),
        default => route('eoffice.kp.mahasiswa.dashboard'),
    };
    $navigationGroups = [
        'Menu utama' => [
            ['label' => 'Dashboard', 'href' => route('eoffice.dashboard'), 'active' => str_contains($currentRoute, 'eoffice.dashboard'), 'icon' => $iDashboard],
        ],
        'Akademik' => array_values(array_filter([
            $praktikumRoles->isNotEmpty() ? ['label' => 'Manajemen Praktikum', 'href' => $praktikumLink, 'active' => str_contains($currentRoute, 'manprak'), 'icon' => $iPraktikum] : null,
            ['label' => 'Kerja Praktik (KP)', 'href' => $kpLink, 'active' => str_contains($currentRoute, 'eoffice.kp'), 'icon' => $iKP],
        ])),
        'Layanan' => [
            ['label' => 'Manajemen Surat', 'href' => route('eoffice.dashboard').'#eo-service-surat', 'active' => str_contains($currentRoute, 'surat'), 'icon' => 'M13 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V9z M13 2v7h7 M8 13h8 M8 17h5'],
            ['label' => 'Peminjaman Ruangan', 'href' => route('eoffice.peminjaman.dashboard'), 'active' => str_contains($currentRoute, 'peminjaman'), 'icon' => 'M3 22V6a1 1 0 0 1 1-1h7a1 1 0 0 1 1 1v16 M13 22V10a1 1 0 0 1 1-1h7a1 1 0 0 1 1 1v12 M2 22h20 M6 9h2 M6 13h2 M16 13h2 M16 17h2'],
        ],
    ];
?>

<aside data-mobile-sidebar id="eo-navigation" class="eo-sidebar" :class="{ 'is-collapsed': !sidebarOpen }" aria-label="Sidebar E-Office"
       :inert="!sidebarOpen && isMobile">
    <div class="eo-sidebar-brand">
        <a href="<?php echo e(route('eoffice.dashboard')); ?>" class="eo-brand-link" title="Dashboard SIPERKOM" aria-label="Dashboard SIPERKOM">
            <img src="<?php echo e(asset('images/UNDIPOfficial.png')); ?>" alt="" width="32" height="32">
            <span class="eo-brand-text" x-show="sidebarOpen">
                <strong>SIPERKOM</strong>
                <span>Layanan E-Office Teknik Komputer</span>
            </span>
        </a>
        <button type="button" class="eo-sidebar-toggle" @click="sidebarOpen = !sidebarOpen"
                :aria-expanded="sidebarOpen" aria-controls="eo-navigation"
                :aria-label="sidebarOpen ? 'Lipat menu navigasi' : 'Perluas menu navigasi'"
                :title="sidebarOpen ? 'Lipat menu' : 'Perluas menu'">
            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M15 18l-6-6 6-6"/></svg>
        </button>
    </div>
    <nav class="eo-sidebar-nav" aria-label="Menu E-Office">
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $navigationGroups; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $label => $links): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
            <div class="eo-nav-group">
                <div class="eo-nav-label" x-show="sidebarOpen"><?php echo e($label); ?></div>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $links; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $link): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                    <a href="<?php echo e($link['href']); ?>" class="eo-nav-link <?php echo e($link['active'] ? 'is-active' : ''); ?>"
                       title="<?php echo e($link['label']); ?>" aria-label="<?php echo e($link['label']); ?>"
                       <?php if($link['active']): ?> aria-current="page" <?php endif; ?>
                       @click="if (window.innerWidth < 768) sidebarOpen = false">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="<?php echo e($link['icon']); ?>"/></svg>
                        <span x-show="sidebarOpen"><?php echo e($link['label']); ?></span>
                    </a>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
            </div>
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
    </nav>
    <div class="eo-sidebar-footer">
        <a href="<?php echo e(route('dashboard')); ?>" class="eo-nav-link" title="Dasbor utama SITKOM" aria-label="Dasbor utama SITKOM">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M19 12H5m7-7-7 7 7 7"/></svg>
            <span x-show="sidebarOpen">Dasbor Utama SITKOM</span>
        </a>
        <a href="<?php echo e(route('profile.edit')); ?>" class="eo-nav-link" title="Pengaturan profil" aria-label="Pengaturan profil">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M19 21v-2a7 7 0 0 0-14 0v2 M12 11a4 4 0 1 0 0-8 4 4 0 0 0 0 8"/></svg>
            <span x-show="sidebarOpen">Pengaturan Profil</span>
        </a>
        <form method="POST" action="<?php echo e(route('logout')); ?>" data-no-loader>
            <?php echo csrf_field(); ?>
            <button type="submit" class="eo-nav-link eo-nav-logout" title="Keluar" aria-label="Keluar dari akun">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4 M16 17l5-5-5-5 M21 12H9"/></svg>
                <span x-show="sidebarOpen">Keluar</span>
            </button>
        </form>
    </div>
</aside>
<?php /**PATH C:\WebsiteTekkom - Copy\Modules/EOffice\resources/views/dashboard/_sidebar.blade.php ENDPATH**/ ?>