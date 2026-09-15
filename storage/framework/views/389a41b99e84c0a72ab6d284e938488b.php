<!DOCTYPE html>
<html lang="<?php echo e(str_replace('_', '-', app()->getLocale())); ?>">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">

    <title>SIKP - <?php echo e($title ?? 'Koordinator Dashboard'); ?></title>

    <!-- Inter Tight Font (Figma Design System) -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Inter+Tight:ital,wght@0,300;0,400;0,500;0,600;0,700;0,800;1,400&display=swap"
        rel="stylesheet">

    <?php echo app('Illuminate\Foundation\Vite')(['resources/css/app.css', 'resources/js/app.js']); ?>
    <?php echo $__env->yieldPushContent('styles'); ?>

    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <style>
        html,
        body,
        * {
            font-family: 'Inter Tight', system-ui, -apple-system, sans-serif !important;
        }
    </style>
    <?php if (isset($component)) { $__componentOriginal798d336f107c986f84961f0c7e80911e = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal798d336f107c986f84961f0c7e80911e = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.mobile-navigation-assets','data' => []] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('mobile-navigation-assets'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal798d336f107c986f84961f0c7e80911e)): ?>
<?php $attributes = $__attributesOriginal798d336f107c986f84961f0c7e80911e; ?>
<?php unset($__attributesOriginal798d336f107c986f84961f0c7e80911e); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal798d336f107c986f84961f0c7e80911e)): ?>
<?php $component = $__componentOriginal798d336f107c986f84961f0c7e80911e; ?>
<?php unset($__componentOriginal798d336f107c986f84961f0c7e80911e); ?>
<?php endif; ?>
</head>

<body class="antialiased bg-[#ECEFF3]">
    <div x-data="{ sidebarOpen: true, ...(typeof pageData !== 'undefined' ? pageData : {}) }"
        class="flex h-screen bg-[#ECEFF3] overflow-hidden">

        <!-- ===== SIDEBAR (flush kiri, tanpa rounded) ===== -->
        <!-- Mobile overlay -->
        <div x-show="sidebarOpen" class="fixed inset-0 bg-black/20 z-10 md:hidden" @click="sidebarOpen = false"
            x-transition:enter="transition-opacity ease-out duration-200" x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100" x-transition:leave="transition-opacity ease-in duration-150"
            x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" style="display:none;">
        </div>

        <!-- Sidebar wrapper — no rounded, flush left, bg white (matches sidebar) -->
        <div data-mobile-sidebar-holder class="flex-shrink-0 relative z-20 bg-white border-r border-[#DFE1E6]"
            :class="sidebarOpen ? 'w-[260px]' : 'w-[72px]'" style="transition: width 0.3s;">
            <?php if (isset($component)) { $__componentOriginala7b6fd82e7bf9e0b2743ddfb75a29ef4 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginala7b6fd82e7bf9e0b2743ddfb75a29ef4 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'eoffice::components.ui.sidebar-koordinator','data' => []] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('eoffice::ui.sidebar-koordinator'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginala7b6fd82e7bf9e0b2743ddfb75a29ef4)): ?>
<?php $attributes = $__attributesOriginala7b6fd82e7bf9e0b2743ddfb75a29ef4; ?>
<?php unset($__attributesOriginala7b6fd82e7bf9e0b2743ddfb75a29ef4); ?>
<?php endif; ?>
<?php if (isset($__componentOriginala7b6fd82e7bf9e0b2743ddfb75a29ef4)): ?>
<?php $component = $__componentOriginala7b6fd82e7bf9e0b2743ddfb75a29ef4; ?>
<?php unset($__componentOriginala7b6fd82e7bf9e0b2743ddfb75a29ef4); ?>
<?php endif; ?>
        </div>

        <!-- ===== MAIN AREA: with padding, white rounded rectangle ===== -->
        <div class="flex-1 flex flex-col min-w-0 overflow-hidden p-3">

            <!-- White rounded rectangle card (topbar + content) -->
            <div class="flex-1 flex flex-col min-w-0 bg-white rounded-xl overflow-hidden shadow-sm">

                <!-- Topbar — inside the white card -->
                <header class="flex-shrink-0 h-14 flex items-center justify-between px-6 border-b border-[#DFE1E6]">

                    <!-- Left: Mobile hamburger + Breadcrumb -->
                    <div class="flex items-center gap-3">
                        <button @click="sidebarOpen = !sidebarOpen"
                            class="p-1.5 text-[#A4ABB8] hover:text-[#353849] rounded-lg hover:bg-[#F8F9FB] transition-colors">
                            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M4 6h16M4 12h16M4 18h16" />
                            </svg>
                        </button>

                        <!-- Breadcrumb -->
                        <div class="flex items-center gap-1.5 text-[13px]">
                            <span class="text-[#A4ABB8] font-medium">SIKP</span>
                            <?php if (! empty(trim($__env->yieldContent('breadcrumbs')))): ?>
                                <span class="text-[#C1C7CF] mx-0.5">/</span>
                                <?php echo $__env->yieldContent('breadcrumbs'); ?>
                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </div>
                    </div>

                    <!-- Right: Search + Notif + User -->
                    <div class="flex items-center gap-1.5">
                        <!-- Search -->
                        <button
                            class="w-8 h-8 flex items-center justify-center text-[#808897] hover:text-[#353849] rounded-full hover:bg-[#F8F9FB] transition-colors">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75"
                                    d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                        </button>

                        <!-- Notification -->
                        <button
                            class="w-8 h-8 flex items-center justify-center text-[#808897] hover:text-[#353849] rounded-full hover:bg-[#F8F9FB] transition-colors relative">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75"
                                    d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                            </svg>
                            <span class="absolute top-1.5 right-1.5 w-1.5 h-1.5 bg-red-500 rounded-full"></span>
                        </button>

                        <div class="w-px h-5 bg-[#DFE1E6] mx-1"></div>

                        <!-- User -->
                        <div class="flex items-center gap-2.5 cursor-pointer group">
                            <div
                                class="w-8 h-8 rounded-full bg-[#E8EEFF] flex items-center justify-center text-[#0065FF] border border-[#C1D0FF] overflow-hidden group-hover:border-[#0065FF] transition-colors flex-shrink-0">
                                <span
                                    class="font-bold text-xs leading-none"><?php echo e(strtoupper(substr(auth()->user()->name ?? 'K', 0, 1))); ?></span>
                            </div>
                            <div class="hidden sm:flex flex-col leading-none">
                                <span
                                    class="text-[13px] font-semibold text-[#272835]"><?php echo e(auth()->user()->name ?? 'Koordinator KP'); ?></span>
                                <span class="text-[11px] text-[#808897] font-normal mt-0.5">Koordinator KP</span>
                            </div>
                        </div>
                    </div>
                </header>

                <!-- Page Content -->
                <main class="flex-1 overflow-y-auto" style="scrollbar-width: thin;">
                    <div class="p-6 lg:p-8 max-w-screen-2xl mx-auto">
                        <?php echo e($slot); ?>

                    </div>
                </main>

            </div>

        </div>

    </div>

    <?php echo $__env->yieldPushContent('scripts'); ?>
    <?php if (isset($component)) { $__componentOriginal0d3b2b7a1a27f99acddd84d2b2599baa = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal0d3b2b7a1a27f99acddd84d2b2599baa = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.mobile-navigation','data' => []] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('mobile-navigation'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal0d3b2b7a1a27f99acddd84d2b2599baa)): ?>
<?php $attributes = $__attributesOriginal0d3b2b7a1a27f99acddd84d2b2599baa; ?>
<?php unset($__attributesOriginal0d3b2b7a1a27f99acddd84d2b2599baa); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal0d3b2b7a1a27f99acddd84d2b2599baa)): ?>
<?php $component = $__componentOriginal0d3b2b7a1a27f99acddd84d2b2599baa; ?>
<?php unset($__componentOriginal0d3b2b7a1a27f99acddd84d2b2599baa); ?>
<?php endif; ?>
</body>

</html>
<?php /**PATH C:\WebsiteTekkom - Copy\Modules\EOffice\resources\views\components\layouts\koordinator.blade.php ENDPATH**/ ?>