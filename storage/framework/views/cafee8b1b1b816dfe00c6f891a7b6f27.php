<?php $__env->startSection('content'); ?>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('styles'); ?>
    <style>
        .sidebar {
            width: 240px;
            height: 100vh;
            position: fixed;
            background: #ffffff;
            border-right: 1px solid #DFE1E7;
            padding: 0;
            display: flex;
            flex-direction: column;
            transition: width 0.25s ease;
        }

        .menu-title {
            font-size: 12px;
            color: #9ca3af;
            margin-top: 20px;
        }

        .sidebar a {
            position: relative;
            display: block;
            display: flex;
            align-items: center;
            gap: 9px;
            padding: 7px 10px 7px 14px;
            border-radius: 8px;
            text-decoration: none;
            color: #353849;
            font-weight: 500;
            font-size: 13px;
            margin-bottom: 1px;
            transition: background .12s, color .12s;
            white-space: nowrap;
        }

        .sidebar a:hover {
            background: #F6F8FA;
            color: #1A1C1E;
        }

        .sidebar a.active {
            background: rgba(11, 38, 110, 0.08);
            color: #0B266E;
            font-weight: 600;
            box-shadow: none;
        }

        .sidebar a.active::before {
            content: "";
            position: absolute;
            left: 0;
            top: 50%;
            transform: translateY(-50%);
            width: 3px;
            height: 20px;
            background: #0B266E;
            border-radius: 0 3px 3px 0;
        }

        .sidebar-collapsed .sidebar a.active::before {
            display: none;
        }

        .sidebar a svg {
            color: #666D80;
            width: 16px;
            height: 16px;
            transition: color 0.12s;
            flex-shrink: 0;
        }

        .sidebar a.active svg {
            color: #0B266E;
        }

        .sidebar a:hover svg {
            color: #1A1C1E;
        }

        /* Collapsed Sidebar */
        .sidebar-collapsed .sidebar {
            width: 64px;
            padding: 0;
        }

        .sidebar-collapsed .sidebar a {
            justify-content: center;
            padding: 7px 0;
            gap: 0;
        }

        .sidebar-collapsed .nav-label,
        .sidebar-collapsed .sb-section-label,
        .sidebar-collapsed .sb-brand-text,
        .sidebar-collapsed .dropdown-arrow,
        .sidebar-collapsed .sidebar-dropdown-menu {
            display: none !important;
        }

        .btn-logout {
            position: relative;
            display: flex;
            align-items: center;
            gap: 9px;
            padding: 7px 10px 7px 14px;
            border-radius: 8px;
            text-decoration: none;
            color: #353849;
            font-weight: 500;
            font-size: 13px;
            margin-bottom: 1px;
            transition: background .12s, color .12s;
            width: 100%;
            text-align: left;
            border: none;
            background: transparent;
            white-space: nowrap;
            font-family: inherit;
            cursor: pointer;
        }

        .sidebar-collapsed .btn-logout {
            justify-content: center;
            padding: 7px 0;
            gap: 0;
        }

        .btn-logout:hover {
            background: #FEF1F4;
            color: #DF1C41;
        }

        .btn-logout svg {
            color: #666D80;
            width: 16px;
            height: 16px;
            transition: color 0.12s;
            flex-shrink: 0;
        }

        .btn-logout:hover svg {
            color: #DF1C41;
        }

        .bottom-menu {
            margin-top: auto;
            padding-top: 10px;
            width: 100%;
        }

        .navbar-custom {
            margin-left: 240px;
            height: 60px;
            background: #fff;
            border-bottom: 1px solid #DFE1E7;
            display: flex;
            justify-content: flex-end;
            align-items: center;
            padding: 0 28px;
            transition: margin-left 0.25s ease;
        }

        .sidebar-collapsed .navbar-custom {
            margin-left: 64px;
        }

        .user-profile {
            display: flex;
            gap: 10px;
            align-items: center;
        }

        .user-profile img {
            width: 38px;
            height: 38px;
            border-radius: 50%;
        }

        .content {
            margin-left: 240px;
            padding: 24px 28px 48px;
            transition: margin-left 0.25s ease;
        }

        .sidebar-collapsed .content {
            margin-left: 64px;
        }

        .main-wrapper {
            background: #fff;
            border-radius: 12px;
            padding: 25px;
            min-height: calc(100vh - 120px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
        }

        /* Global Scrollbar Customization */
        ::-webkit-scrollbar {
            width: 10px;
            height: 10px;
        }

        ::-webkit-scrollbar-track {
            background: #f1f5f9;
        }

        ::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 10px;
            border: 2px solid #f1f5f9;
        }

        ::-webkit-scrollbar-thumb:hover {
            background: #94a3b8;
        }
    </style>
<?php $__env->stopPush(); ?>

<?php $__env->startSection('body'); ?>
<div x-data="{ sidebarOpen: localStorage.getItem('sidebarOpen') !== 'false' }" 
     x-init="$watch('sidebarOpen', val => localStorage.setItem('sidebarOpen', val))"
     :class="{ 'sidebar-collapsed': !sidebarOpen }">

    
    <?php if (isset($component)) { $__componentOriginalfa59d818f1b9c9962503a907553e7458 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalfa59d818f1b9c9962503a907553e7458 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'manajemenmahasiswa::components.ui.sidebar','data' => []] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('manajemenmahasiswa::ui.sidebar'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalfa59d818f1b9c9962503a907553e7458)): ?>
<?php $attributes = $__attributesOriginalfa59d818f1b9c9962503a907553e7458; ?>
<?php unset($__attributesOriginalfa59d818f1b9c9962503a907553e7458); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalfa59d818f1b9c9962503a907553e7458)): ?>
<?php $component = $__componentOriginalfa59d818f1b9c9962503a907553e7458; ?>
<?php unset($__componentOriginalfa59d818f1b9c9962503a907553e7458); ?>
<?php endif; ?>

    
    <?php if (isset($component)) { $__componentOriginal9b73b18245d728dcc53b17e9ac6130f6 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal9b73b18245d728dcc53b17e9ac6130f6 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'manajemenmahasiswa::components.ui.navbar-admin','data' => []] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('manajemenmahasiswa::ui.navbar-admin'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal9b73b18245d728dcc53b17e9ac6130f6)): ?>
<?php $attributes = $__attributesOriginal9b73b18245d728dcc53b17e9ac6130f6; ?>
<?php unset($__attributesOriginal9b73b18245d728dcc53b17e9ac6130f6); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal9b73b18245d728dcc53b17e9ac6130f6)): ?>
<?php $component = $__componentOriginal9b73b18245d728dcc53b17e9ac6130f6; ?>
<?php unset($__componentOriginal9b73b18245d728dcc53b17e9ac6130f6); ?>
<?php endif; ?>

    
    <div class="content">
        <div class="main-wrapper">
            <?php echo $__env->yieldContent('content'); ?>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.master', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\WebsiteTekkom - Copy\Modules\ManajemenMahasiswa\resources\views\components\layouts\dashboard.blade.php ENDPATH**/ ?>