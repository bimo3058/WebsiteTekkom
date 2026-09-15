        <header class="mp-topbar">
            <nav class="mp-breadcrumb" aria-label="Breadcrumb">
                <button type="button" class="mp-icon-button mp-mobile-menu" x-ref="mpMenuButton"
                        @click="sidebarOpen = true; $nextTick(() => $refs.mpSidebarToggle.focus())"
                        :aria-expanded="sidebarOpen" aria-controls="mp-navigation" aria-label="Buka menu praktikum">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" aria-hidden="true"><path d="M4 6h16M4 12h16M4 18h16"/></svg>
                </button>
                <a class="mp-breadcrumb-root" href="<?php echo e(route('eoffice.dashboard')); ?>">E-Office</a>
                <span class="mp-breadcrumb-root" aria-hidden="true">/</span>
                <a href="<?php echo e(route('eoffice.manprak.dashboard')); ?>">Praktikum</a>
                <span aria-hidden="true">/</span>
                <strong aria-current="page" title="<?php echo e($pageTitle ?? 'Dashboard'); ?>"><?php echo e($pageTitle ?? 'Dashboard'); ?></strong>
            </nav>
            <div class="mp-topbar-actions">
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($notifCount > 0): ?>
                    <span class="mp-notification-count" title="<?php echo e($notifCount); ?> notifikasi belum dibaca" aria-label="<?php echo e($notifCount); ?> notifikasi belum dibaca">
                        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" aria-hidden="true"><path d="M6 8a6 6 0 0 1 12 0c0 7 3 9 3 9H3s3-2 3-9 M10 21a2 2 0 0 0 4 0"/></svg>
                        <?php echo e($notifCount > 99 ? '99+' : $notifCount); ?>

                    </span>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                <a href="<?php echo e(route('profile.edit')); ?>" class="mp-account" title="Pengaturan profil" aria-label="Pengaturan profil <?php echo e($name); ?>">
                    <span class="mp-account-avatar" aria-hidden="true">
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($user->avatar_url): ?>
                            <img src="<?php echo e($user->avatar_url); ?>" alt="" width="34" height="34">
                        <?php else: ?>
                            <?php echo e($initials); ?>

                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </span>
                    <span class="mp-account-meta"><strong><?php echo e($name); ?></strong><span><?php echo e($activeRoleLabel); ?></span></span>
                </a>
            </div>
        </header>
<?php /**PATH C:\WebsiteTekkom - Copy\Modules/EOffice\resources/views/manajemen-praktikum/partials/_topbar.blade.php ENDPATH**/ ?>