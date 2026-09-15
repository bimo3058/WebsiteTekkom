<?php $__env->startSection('title','Dashboard'); ?>
<?php $__env->startSection('content'); ?>
<div x-data="capstoneDashboard">
    <div class="mb-6 flex flex-wrap items-center justify-between gap-4"><div><h1 class="text-2xl font-bold tracking-tight">Welcome back, <?php echo e(explode(' ', $actor['name'])[0]); ?>!</h1><p class="text-muted-foreground">Here's an overview of your mentoring dashboard.</p></div><select x-model="selectedPeriod" @change="load" class="h-9 rounded-md border px-3 text-sm"><option value="all">All Periods</option><template x-for="period in periods" :key="period.id"><option :value="period.id" x-text="period.name"></option></template></select></div>
    <section class="space-y-4"><div><h2 class="text-lg font-semibold tracking-tight">Dosen Overview</h2><p class="text-sm text-muted-foreground">Teaching &amp; mentoring activities</p></div>
        <?php echo $__env->make('capstone::partials.loading', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
        <div x-show="!loading && !error" x-cloak class="space-y-4"><div class="grid grid-cols-2 gap-3"><?php if (isset($component)) { $__componentOriginal3ec4da56c1883f197f1fbeb161334ca2 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal3ec4da56c1883f197f1fbeb161334ca2 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'capstone::components.stat','data' => ['title' => 'Supervised Groups','value' => 'data.active_groups ?? 0','icon' => 'Users','variant' => 'primary']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('capstone::stat'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => 'Supervised Groups','value' => 'data.active_groups ?? 0','icon' => 'Users','variant' => 'primary']); ?>
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
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'capstone::components.stat','data' => ['title' => 'Pending Evaluations','value' => 'pending','icon' => 'Star']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('capstone::stat'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => 'Pending Evaluations','value' => 'pending','icon' => 'Star']); ?>
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
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'capstone::components.stat','data' => ['title' => 'Pending Proposals','value' => 'data.pending_proposals ?? 0','icon' => 'FileText']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('capstone::stat'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => 'Pending Proposals','value' => 'data.pending_proposals ?? 0','icon' => 'FileText']); ?>
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
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'capstone::components.stat','data' => ['title' => 'Available Periods','value' => 'periods.length','icon' => 'Calendar']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('capstone::stat'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => 'Available Periods','value' => 'periods.length','icon' => 'Calendar']); ?>
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
        <?php echo $__env->make('capstone::partials.recent-groups',['heading'=>'Supervised Groups','link'=>'/dosen/supervised-groups','empty'=>'No supervised groups yet'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
        <?php echo $__env->make('capstone::partials.quick-actions',['actions'=>[['Review TA','/dosen/ta-review','FileText','Review submissions'],['Evaluate','/dosen/supervisor-evaluation','Star','Score students'],['My Titles','/dosen/titles','BookOpen','Manage titles'],['Bids','/dosen/bids','Gavel','Review bids']]], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?></div>
    </section>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('capstone::layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\WebsiteTekkom - Copy\Modules\Capstone\resources\views\pages\dosen\dashboard.blade.php ENDPATH**/ ?>