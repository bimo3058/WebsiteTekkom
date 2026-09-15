
<?php $user = auth()->user(); ?>
<header class="flex-shrink-0 h-14 bg-white flex items-center justify-between px-4 sm:px-6 border-b border-[#DFE1E6] z-10">
    <!-- Left: Mobile toggle + Breadcrumb -->
    <div class="flex items-center gap-3">
        <button @click="sidebarOpen = true" class="lg:hidden p-1.5 text-[#A4ABB8] hover:text-[#353849] rounded-lg hover:bg-[#F8F9FB] transition-colors focus:outline-none">
            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16"/>
            </svg>
        </button>

        <!-- Breadcrumb -->
        <div class="hidden sm:flex items-center gap-1.5 text-[13px]">
            <span class="text-[#A4ABB8] font-medium">SIKP</span>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(isset($breadcrumbs) && count($breadcrumbs)): ?>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $breadcrumbs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $i => $crumb): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                <span class="text-[#C1C7CF] mx-0.5">/</span>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($i === count($breadcrumbs) - 1): ?>
                    <span class="font-semibold text-[#272835]"><?php echo e($crumb); ?></span>
                <?php else: ?>
                    <span class="text-[#A4ABB8]"><?php echo e($crumb); ?></span>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
            <?php elseif(isset($breadcrumb)): ?>
                <span class="text-[#C1C7CF] mx-0.5">/</span>
                <span class="font-semibold text-[#272835]"><?php echo e($breadcrumb); ?></span>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </div>
        <!-- Mobile title -->
        <span class="sm:hidden text-[15px] font-semibold text-[#272835]"><?php echo e($breadcrumb ?? 'SIKP'); ?></span>
    </div>

    <!-- Right: Search + Notification + User -->
    <div class="flex items-center gap-1.5">
        <!-- Search Button -->
        <button class="w-8 h-8 flex items-center justify-center text-[#808897] hover:text-[#353849] rounded-full hover:bg-[#F8F9FB] transition-colors focus:outline-none">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
            </svg>
        </button>

        <!-- Notification Bell -->
        <button class="relative w-8 h-8 flex items-center justify-center text-[#808897] hover:text-[#353849] rounded-full hover:bg-[#F8F9FB] transition-colors focus:outline-none">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
            </svg>
            <span class="absolute top-1.5 right-1.5 w-1.5 h-1.5 bg-red-500 rounded-full"></span>
        </button>

        <div class="hidden sm:block w-px h-5 bg-[#DFE1E6] mx-1"></div>

        <!-- User Profile Info -->
        <div class="flex items-center gap-2.5 cursor-pointer group">
            <div class="w-8 h-8 rounded-full bg-[#E8EEFF] flex items-center justify-center text-[#0065FF] border border-[#C1D0FF] overflow-hidden group-hover:border-[#0065FF] transition-colors flex-shrink-0">
                <span class="font-bold text-xs leading-none"><?php echo e(strtoupper(substr($user->name ?? 'M', 0, 1))); ?></span>
            </div>
            <div class="hidden sm:flex flex-col leading-none text-left">
                <span class="text-[13px] font-semibold text-[#272835]"><?php echo e($user->name ?? 'Mahasiswa'); ?></span>
                <span class="text-[11px] text-[#808897] font-normal mt-0.5">Mahasiswa</span>
            </div>
        </div>
    </div>
</header>
<?php /**PATH C:\WebsiteTekkom - Copy\Modules/EOffice\resources/views/kp/mahasiswa/partials/topbar.blade.php ENDPATH**/ ?>