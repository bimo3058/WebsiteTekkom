<!DOCTYPE html>
<html lang="<?php echo e(str_replace('_', '-', app()->getLocale())); ?>">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">

    <title><?php echo e(config('app.name', 'SIBASO')); ?> - GPM</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700,800&display=swap" rel="stylesheet" />

    <!-- Font Awesome for Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- Scripts -->
    <?php echo app('Illuminate\Foundation\Vite')(['resources/css/app.css', 'resources/js/app.js']); ?>
    <?php echo \Livewire\Mechanisms\FrontendAssets\FrontendAssets::styles(); ?>

    <?php echo $__env->yieldPushContent('styles'); ?>
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
<body class="font-sans antialiased text-slate-900 bg-slate-50 selection:bg-primary selection:text-white">
    <div x-data="{ sidebarOpen: true }" class="flex h-screen bg-slate-50 font-sans text-slate-900 overflow-hidden">

        <!-- Sidebar Component -->
        <?php if (isset($component)) { $__componentOriginal52d761c01b87db1b93d40faa9e61f470 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal52d761c01b87db1b93d40faa9e61f470 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'banksoal::components.ui.sidebar-gpm','data' => []] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('banksoal::ui.sidebar-gpm'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal52d761c01b87db1b93d40faa9e61f470)): ?>
<?php $attributes = $__attributesOriginal52d761c01b87db1b93d40faa9e61f470; ?>
<?php unset($__attributesOriginal52d761c01b87db1b93d40faa9e61f470); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal52d761c01b87db1b93d40faa9e61f470)): ?>
<?php $component = $__componentOriginal52d761c01b87db1b93d40faa9e61f470; ?>
<?php unset($__componentOriginal52d761c01b87db1b93d40faa9e61f470); ?>
<?php endif; ?>

        <!-- Main Content Wrapper -->
        <div class="flex-1 flex flex-col h-screen overflow-hidden relative">

            <!-- Topbar -->
            <header class="bg-white border-b border-slate-200 h-16 flex-shrink-0 flex items-center justify-between px-6 z-10">
                <div class="flex items-center text-sm font-medium text-slate-600">
                    <span class="mr-2">SIBASO</span>
                    <span class="mx-2 text-slate-300">/</span>
                    <span class="text-slate-500 font-semibold">GPM</span>
                    <?php if (! empty(trim($__env->yieldContent('breadcrumbs')))): ?>
                        <span class="mx-2 text-slate-300">/</span>
                        <?php echo $__env->yieldContent('breadcrumbs'); ?>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div>

                <div class="flex items-center gap-4">
                    <button class="w-8 h-8 flex items-center justify-center text-slate-400 hover:text-slate-600 rounded-full hover:bg-slate-50 transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                    </button>

                    <button class="w-8 h-8 flex items-center justify-center text-slate-400 hover:text-slate-600 rounded-full hover:bg-slate-50 transition-colors relative">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>
                        <span class="absolute top-1.5 right-1.5 w-2 h-2 bg-red-500 rounded-full border-2 border-white"></span>
                    </button>

                    <div class="h-6 w-px bg-slate-200 mx-1"></div>

                    <div class="flex items-center gap-3 cursor-pointer group">
                        <div class="w-9 h-9 rounded-full bg-slate-200 flex items-center justify-center text-slate-600 overflow-hidden border border-slate-300 group-hover:border-primary transition-colors">
                            <span class="font-bold text-sm"><?php echo e(strtoupper(substr(auth()->user()->name ?? 'G', 0, 1))); ?></span>
                        </div>
                        <div class="flex flex-col">
                            <span class="text-sm font-bold text-slate-800 leading-tight"><?php echo e(auth()->user()->name ?? 'GPM'); ?></span>
                            <span class="text-[11px] text-slate-500 font-medium">Dosen GPM</span>
                        </div>
                    </div>
                </div>
            </header>

            <!-- Main Content Area -->
            <main class="w-full flex-1 overflow-y-auto">
                <div class="p-8 w-full max-w-screen-2xl mx-auto">
                    <?php echo e($slot); ?>

                </div>
            </main>
        </div>

    </div>

    <!-- Global Component untuk Toast Message -->
    <?php if (isset($component)) { $__componentOriginal25c5464ae39d43368ae46e423f17f7cd = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal25c5464ae39d43368ae46e423f17f7cd = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'banksoal::components.global-toast','data' => []] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('banksoal::global-toast'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal25c5464ae39d43368ae46e423f17f7cd)): ?>
<?php $attributes = $__attributesOriginal25c5464ae39d43368ae46e423f17f7cd; ?>
<?php unset($__attributesOriginal25c5464ae39d43368ae46e423f17f7cd); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal25c5464ae39d43368ae46e423f17f7cd)): ?>
<?php $component = $__componentOriginal25c5464ae39d43368ae46e423f17f7cd; ?>
<?php unset($__componentOriginal25c5464ae39d43368ae46e423f17f7cd); ?>
<?php endif; ?>

    <!-- Global Loader Overlay (Style 1 for non-interruptible POST/mutation queries) -->
    <div class="pm-loader" id="loaderOverlay" style="position:fixed;inset:0;background:rgba(255,255,255,.7);display:none;align-items:center;justify-content:center;z-index:999999;">
        <div class="pm-spinner" style="width:36px;height:36px;border:3px solid #e2e8f0;border-top-color:rgb(11,38,110);border-radius:50%;animation:pm-spin .7s linear infinite;"></div>
    </div>
    <style>
        .pm-loader.show { display: flex !important; }
        @keyframes pm-spin { to { transform: rotate(360deg); } }
    </style>
    <script>
        window.showLoader = function() {
            const overlay = document.getElementById('loaderOverlay');
            if (overlay) overlay.classList.add('show');
        };
        window.hideLoader = function() {
            const overlay = document.getElementById('loaderOverlay');
            if (overlay) overlay.classList.remove('show');
        };
    </script>

    <?php echo $__env->make('banksoal::partials.gpm.layout-scripts', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    <?php echo \Livewire\Mechanisms\FrontendAssets\FrontendAssets::scripts(); ?>

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
<?php /**PATH C:\WebsiteTekkom - Copy\Modules\BankSoal\resources\views\components\layouts\gpm-master.blade.php ENDPATH**/ ?>