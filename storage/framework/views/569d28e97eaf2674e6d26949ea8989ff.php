

<header class="topbar">
    <button class="sidebar-toggle"><i class="fas fa-bars"></i></button>
    <button class="topbar-btn"><i class="fas fa-cog"></i></button>
    <button class="topbar-btn notif-btn"><i class="fas fa-bell"></i><span class="notif-dot"></span></button>

    
    <div class="user-dropdown-wrapper">
        <button class="user-chip" onclick="toggleProfileDropdown(event)">
            <div class="user-avatar-chip"><?php echo e(strtoupper(substr(auth()->user()->name, 0, 1))); ?></div>
            <div class="user-info">
                <strong><?php echo e(auth()->user()->name); ?></strong>
                <span><?php echo e(auth()->user()->lecturer?->employee_number ?? auth()->user()->email); ?></span>
            </div>
            <i class="fas fa-chevron-down" style="margin-left: auto; font-size: 12px;"></i>
        </button>

        <div class="profile-dropdown" id="profileDropdown">
            <div class="dropdown-header">
                <div class="dropdown-avatar"><?php echo e(strtoupper(substr(auth()->user()->name, 0, 1))); ?></div>
                <div class="dropdown-info">
                    <div class="dropdown-name"><?php echo e(auth()->user()->name); ?></div>
                    <div class="dropdown-role">Dosen</div>
                </div>
            </div>
            <div class="dropdown-divider"></div>
            <button class="dropdown-logout" onclick="document.getElementById('logout-form').submit();">
                <i class="fas fa-sign-out-alt"></i> Logout
            </button>
        </div>
    </div>

    <form id="logout-form" action="<?php echo e(route('logout')); ?>" method="POST" style="display: none;">
        <?php echo csrf_field(); ?>
    </form>
</header>
<?php /**PATH C:\WebsiteTekkom - Copy\Modules\BankSoal\resources\views\partials\dosen\topbar.blade.php ENDPATH**/ ?>