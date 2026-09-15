<?php $__env->startSection('title','Dashboard'); ?>
<?php $__env->startSection('content'); ?>
<div x-data="capstoneDashboard">
    <div class="mb-6"><h1 class="text-2xl font-bold tracking-tight">Welcome back, <?php echo e(explode(' ', $actor['name'])[0]); ?>!</h1><p class="text-muted-foreground">Here's an overview of your administration dashboard.</p></div>
    <section class="space-y-4">
        <div><h2 class="text-lg font-semibold tracking-tight">Admin Overview</h2><p class="text-sm text-muted-foreground">System management &amp; monitoring</p></div>
        <?php echo $__env->make('capstone::partials.loading', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
        <div x-show="!loading && !error" x-cloak class="space-y-4">
            <div class="grid grid-cols-2 gap-3"><?php if (isset($component)) { $__componentOriginal3ec4da56c1883f197f1fbeb161334ca2 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal3ec4da56c1883f197f1fbeb161334ca2 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'capstone::components.stat','data' => ['title' => 'Total Users','value' => 'data.total_users ?? 0','icon' => 'Users','variant' => 'primary']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('capstone::stat'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => 'Total Users','value' => 'data.total_users ?? 0','icon' => 'Users','variant' => 'primary']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal3ec4da56c1883f197f1fbeb161334ca2)): ?>
<?php $attributes = $__attributesOriginal3ec4da56c1883f197f1fbeb161334ca2; ?>
<?php unset($__attributesOriginal3ec4da56c1883f197f1fbeb161334ca2); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal3ec4da56c1883f197f1fbeb161334ca2)): ?>
<?php $component = $__componentOriginal3ec4da56c1883f197f1fbeb161334ca2; ?>
<?php unset($__componentOriginal3ec4da56c1883f197f1fbeb161334ca2); ?>
<?php endif; ?><?php if (isset($component)) { $__componentOriginal3ec4da56c1883f197f1fbeb161334ca2 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal3ec4da56c1883f197f1fbeb161334ca2 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'capstone::components.stat','data' => ['title' => 'Total Periods','value' => 'data.total_periods ?? 0','icon' => 'Calendar']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('capstone::stat'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => 'Total Periods','value' => 'data.total_periods ?? 0','icon' => 'Calendar']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal3ec4da56c1883f197f1fbeb161334ca2)): ?>
<?php $attributes = $__attributesOriginal3ec4da56c1883f197f1fbeb161334ca2; ?>
<?php unset($__attributesOriginal3ec4da56c1883f197f1fbeb161334ca2); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal3ec4da56c1883f197f1fbeb161334ca2)): ?>
<?php $component = $__componentOriginal3ec4da56c1883f197f1fbeb161334ca2; ?>
<?php unset($__componentOriginal3ec4da56c1883f197f1fbeb161334ca2); ?>
<?php endif; ?><?php if (isset($component)) { $__componentOriginal3ec4da56c1883f197f1fbeb161334ca2 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal3ec4da56c1883f197f1fbeb161334ca2 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'capstone::components.stat','data' => ['title' => 'Total Groups','value' => 'data.total_groups ?? 0','icon' => 'Users']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('capstone::stat'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => 'Total Groups','value' => 'data.total_groups ?? 0','icon' => 'Users']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal3ec4da56c1883f197f1fbeb161334ca2)): ?>
<?php $attributes = $__attributesOriginal3ec4da56c1883f197f1fbeb161334ca2; ?>
<?php unset($__attributesOriginal3ec4da56c1883f197f1fbeb161334ca2); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal3ec4da56c1883f197f1fbeb161334ca2)): ?>
<?php $component = $__componentOriginal3ec4da56c1883f197f1fbeb161334ca2; ?>
<?php unset($__componentOriginal3ec4da56c1883f197f1fbeb161334ca2); ?>
<?php endif; ?><?php if (isset($component)) { $__componentOriginal3ec4da56c1883f197f1fbeb161334ca2 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal3ec4da56c1883f197f1fbeb161334ca2 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'capstone::components.stat','data' => ['title' => 'Pending Finalization','value' => 'data.pending_finalization ?? 0','icon' => 'ClipboardCheck']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('capstone::stat'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => 'Pending Finalization','value' => 'data.pending_finalization ?? 0','icon' => 'ClipboardCheck']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal3ec4da56c1883f197f1fbeb161334ca2)): ?>
<?php $attributes = $__attributesOriginal3ec4da56c1883f197f1fbeb161334ca2; ?>
<?php unset($__attributesOriginal3ec4da56c1883f197f1fbeb161334ca2); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal3ec4da56c1883f197f1fbeb161334ca2)): ?>
<?php $component = $__componentOriginal3ec4da56c1883f197f1fbeb161334ca2; ?>
<?php unset($__componentOriginal3ec4da56c1883f197f1fbeb161334ca2); ?>
<?php endif; ?></div>
            <?php echo $__env->make('capstone::partials.recent-groups',['heading'=>'Recent Groups','link'=>'/admin/groups','empty'=>'No groups yet'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
            <?php echo $__env->make('capstone::partials.quick-actions',['actions'=>[['Manage Groups','/admin/groups','Users','View and manage'],['TA Defense','/admin/ta-defense','GraduationCap','Schedule defense'],['Schedule Exam','/admin/schedule','Calendar','SEMPRO & Expo'],['Finalization','/admin/finalization','ClipboardCheck','Finalize groups']]], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
        </div>
    </section>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('capstone::layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\WebsiteTekkom - Copy\Modules\Capstone\resources\views\pages\admin\dashboard.blade.php ENDPATH**/ ?>