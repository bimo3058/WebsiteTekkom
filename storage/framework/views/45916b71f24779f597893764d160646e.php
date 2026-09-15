<aside data-mobile-sidebar id="mp-navigation" class="mp-sidebar" :class="{ 'is-collapsed': !sidebarOpen }"
       :inert="isMobile &amp;&amp; !sidebarOpen" aria-label="Navigasi praktikum">
    <div class="mp-sidebar-brand">
        <a href="<?php echo e(route('eoffice.dashboard')); ?>" class="mp-brand" title="Dashboard E-Office" aria-label="Dashboard E-Office">
            <img src="<?php echo e(asset('images/UNDIPOfficial.png')); ?>" width="32" height="32" alt="">
            <span x-show="sidebarOpen"><strong>SIPERKOM</strong><small>Manajemen Praktikum</small></span>
        </a>
        <button type="button" x-ref="mpSidebarToggle" class="mp-icon-button mp-sidebar-toggle"
                @click="sidebarOpen = !sidebarOpen; if (isMobile) $refs.mpMenuButton.focus()"
                :aria-expanded="sidebarOpen" aria-controls="mp-navigation"
                :aria-label="sidebarOpen ? 'Lipat menu praktikum' : 'Perluas menu praktikum'"
                :title="sidebarOpen ? 'Lipat menu' : 'Perluas menu'">
            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="m15 18-6-6 6-6"/></svg>
        </button>
    </div>
    <nav class="mp-sidebar-nav" aria-label="Menu manajemen praktikum">
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $sections; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $section): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
            <?php $sectionActive = str_contains($currentRoute, $section['match']); ?>
            <section class="mp-nav-section" x-data="{ openSection: <?php echo e(($sectionActive || !$multiRole) ? 'true' : 'false'); ?> }" aria-label="Menu <?php echo e($section['label']); ?>">
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($multiRole): ?>
                    <button type="button" class="mp-role-toggle" :aria-expanded="openSection" aria-controls="mp-role-<?php echo e($loop->index); ?>"
                            title="Menu <?php echo e($section['label']); ?>" aria-label="Menu <?php echo e($section['label']); ?>"
                            @click="if (!sidebarOpen) { sidebarOpen = true; openSection = true; } else { openSection = !openSection; }">
                        <span class="mp-role-initial" aria-hidden="true"><?php echo e(mb_substr($section['label'], 0, 1)); ?></span>
                        <span class="mp-role-name" x-show="sidebarOpen"><?php echo e($section['label']); ?></span>
                        <svg x-show="sidebarOpen" :class="{ 'is-open': openSection }" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="m6 9 6 6 6-6"/></svg>
                    </button>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                <div id="mp-role-<?php echo e($loop->index); ?>" x-show="openSection || !sidebarOpen">
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $section['groups']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $groupLabel => $items): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                        <div class="mp-nav-group">
                            <div class="mp-nav-label" x-show="sidebarOpen"><?php echo e($groupLabel); ?></div>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                                <?php $active = $sectionActive && str_contains($currentRoute, $item['match']); ?>
                                <a href="<?php echo e($item['href']); ?>" class="mp-nav-link <?php echo e($active ? 'is-active' : ''); ?>"
                                   title="<?php echo e($item['label']); ?>" aria-label="<?php echo e($item['label']); ?> — <?php echo e($section['label']); ?>"
                                   <?php if($active): ?> aria-current="page" <?php endif; ?>
                                   @click="if (isMobile) sidebarOpen = false">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="<?php echo e($item['icon']); ?>"/></svg>
                                    <span x-show="sidebarOpen"><?php echo e($item['label']); ?></span>
                                </a>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                        </div>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                </div>
            </section>
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
    </nav>
    <div class="mp-sidebar-footer">
        <a href="<?php echo e(route('eoffice.dashboard')); ?>" class="mp-nav-link" title="Kembali ke E-Office" aria-label="Kembali ke E-Office">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="<?php echo e($iBack); ?>"/></svg>
            <span x-show="sidebarOpen">Kembali ke E-Office</span>
        </a>
        <a href="<?php echo e(route('profile.edit')); ?>" class="mp-nav-link" title="Pengaturan profil" aria-label="Pengaturan profil">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="<?php echo e($iGear); ?>"/></svg>
            <span x-show="sidebarOpen">Pengaturan Profil</span>
        </a>
        <form method="POST" action="<?php echo e(route('logout')); ?>" data-no-loader>
            <?php echo csrf_field(); ?>
            <button type="submit" class="mp-nav-link mp-nav-logout" title="Keluar" aria-label="Keluar dari akun">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="<?php echo e($iLogout); ?>"/></svg>
                <span x-show="sidebarOpen">Keluar</span>
            </button>
        </form>
    </div>
</aside>
<?php /**PATH C:\WebsiteTekkom - Copy\Modules\EOffice\resources\views\manajemen-praktikum\partials\_sidebar.blade.php ENDPATH**/ ?>