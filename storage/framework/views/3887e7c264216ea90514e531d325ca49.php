<?php $__env->startSection('title','My Schedule'); ?>
<?php $__env->startSection('content'); ?>
<div x-data="capstoneSchedules" class="space-y-6">
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div><h1 class="text-3xl font-bold tracking-tight">My Schedule</h1><p class="text-muted-foreground">View all your schedules: bimbingan sessions, examinations, and events.</p></div>
        <div class="flex flex-wrap items-center gap-2"><?php if (isset($component)) { $__componentOriginalb35a908784863ee4a31335a0ede028f1 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalb35a908784863ee4a31335a0ede028f1 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'capstone::components.button','data' => ['@click' => 'edit()']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('capstone::button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['@click' => 'edit()']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>
<?php if (isset($component)) { $__componentOriginal0ebc33cc959e9b0fde2e3c32f43abd3a = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal0ebc33cc959e9b0fde2e3c32f43abd3a = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'capstone::components.icon','data' => ['name' => 'Plus','class' => 'mr-2 h-4 w-4']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('capstone::icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'Plus','class' => 'mr-2 h-4 w-4']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal0ebc33cc959e9b0fde2e3c32f43abd3a)): ?>
<?php $attributes = $__attributesOriginal0ebc33cc959e9b0fde2e3c32f43abd3a; ?>
<?php unset($__attributesOriginal0ebc33cc959e9b0fde2e3c32f43abd3a); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal0ebc33cc959e9b0fde2e3c32f43abd3a)): ?>
<?php $component = $__componentOriginal0ebc33cc959e9b0fde2e3c32f43abd3a; ?>
<?php unset($__componentOriginal0ebc33cc959e9b0fde2e3c32f43abd3a); ?>
<?php endif; ?>New BIMBINGAN <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalb35a908784863ee4a31335a0ede028f1)): ?>
<?php $attributes = $__attributesOriginalb35a908784863ee4a31335a0ede028f1; ?>
<?php unset($__attributesOriginalb35a908784863ee4a31335a0ede028f1); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalb35a908784863ee4a31335a0ede028f1)): ?>
<?php $component = $__componentOriginalb35a908784863ee4a31335a0ede028f1; ?>
<?php unset($__componentOriginalb35a908784863ee4a31335a0ede028f1); ?>
<?php endif; ?>
            <select class="w-[200px] max-w-full h-9 rounded-md border px-3 text-sm bg-background" x-model="selectedPeriod" aria-label="Academic Period"><option value="all">All Periods</option><template x-for="period in periods" :key="period.id"><option :value="period.id" x-text="period.name+(period.is_active ? ' (Active)' : '')"></option></template></select>
        </div>
    </div>
    <?php echo $__env->make('capstone::partials.loading', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    <div x-show="!loading && !error" x-cloak class="space-y-6">
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4"><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = ['BIMBINGAN'=>'Bimbingan','SEMPRO'=>'Sempro','EXPO'=>'Expo','TA_DEFENSE'=>'TA Defense']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $type=>$label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?><div class="p-4 rounded-lg border" :class="color('<?php echo e($type); ?>')"><p class="text-2xl font-bold" x-text="count('<?php echo e($type); ?>')"></p><p class="text-sm text-muted-foreground"><?php echo e($label); ?></p></div><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?></div>
        <?php echo $__env->make('capstone::partials.schedule-calendar', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    </div>
    <?php if (isset($component)) { $__componentOriginal23d0c1a5dcb8d467892a41eeb359fba6 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal23d0c1a5dcb8d467892a41eeb359fba6 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'capstone::components.dialog','data' => ['id' => 'schedule-form','class' => 'sm:max-w-[480px]']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('capstone::dialog'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['id' => 'schedule-form','class' => 'sm:max-w-[480px]']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

        <h2 class="text-lg font-semibold" x-text="editing ? 'Edit BIMBINGAN Schedule' : 'New BIMBINGAN Schedule'"></h2>
        <p class="text-sm text-muted-foreground mt-2" x-text="editing ? 'Update the schedule details.' : 'Set up a new bimbingan session for your group.'"></p>
        <form @submit.prevent="save">
            <div class="grid gap-4 py-4">
                <?php if (isset($component)) { $__componentOriginal32678ef1aa05abec5694cf306077ecae = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal32678ef1aa05abec5694cf306077ecae = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'capstone::components.field','data' => ['name' => 'group_id','label' => 'Group','type' => 'select','options' => [''=>'Select a group'],'required' => true]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('capstone::field'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'group_id','label' => 'Group','type' => 'select','options' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute([''=>'Select a group']),'required' => true]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>
<template x-for="group in groups" :key="group.id"><option :value="group.id" x-text="group.title?.title || group.code || 'Group '+group.id"></option></template> <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal32678ef1aa05abec5694cf306077ecae)): ?>
<?php $attributes = $__attributesOriginal32678ef1aa05abec5694cf306077ecae; ?>
<?php unset($__attributesOriginal32678ef1aa05abec5694cf306077ecae); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal32678ef1aa05abec5694cf306077ecae)): ?>
<?php $component = $__componentOriginal32678ef1aa05abec5694cf306077ecae; ?>
<?php unset($__componentOriginal32678ef1aa05abec5694cf306077ecae); ?>
<?php endif; ?>
                <?php if (isset($component)) { $__componentOriginal32678ef1aa05abec5694cf306077ecae = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal32678ef1aa05abec5694cf306077ecae = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'capstone::components.field','data' => ['name' => 'date','label' => 'Date','type' => 'date','required' => true]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('capstone::field'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'date','label' => 'Date','type' => 'date','required' => true]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal32678ef1aa05abec5694cf306077ecae)): ?>
<?php $attributes = $__attributesOriginal32678ef1aa05abec5694cf306077ecae; ?>
<?php unset($__attributesOriginal32678ef1aa05abec5694cf306077ecae); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal32678ef1aa05abec5694cf306077ecae)): ?>
<?php $component = $__componentOriginal32678ef1aa05abec5694cf306077ecae; ?>
<?php unset($__componentOriginal32678ef1aa05abec5694cf306077ecae); ?>
<?php endif; ?>
                <div class="grid grid-cols-2 gap-4"><?php if (isset($component)) { $__componentOriginal32678ef1aa05abec5694cf306077ecae = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal32678ef1aa05abec5694cf306077ecae = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'capstone::components.field','data' => ['name' => 'start_time','label' => 'Start Time','type' => 'time','required' => true]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('capstone::field'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'start_time','label' => 'Start Time','type' => 'time','required' => true]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal32678ef1aa05abec5694cf306077ecae)): ?>
<?php $attributes = $__attributesOriginal32678ef1aa05abec5694cf306077ecae; ?>
<?php unset($__attributesOriginal32678ef1aa05abec5694cf306077ecae); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal32678ef1aa05abec5694cf306077ecae)): ?>
<?php $component = $__componentOriginal32678ef1aa05abec5694cf306077ecae; ?>
<?php unset($__componentOriginal32678ef1aa05abec5694cf306077ecae); ?>
<?php endif; ?><?php if (isset($component)) { $__componentOriginal32678ef1aa05abec5694cf306077ecae = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal32678ef1aa05abec5694cf306077ecae = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'capstone::components.field','data' => ['name' => 'end_time','label' => 'End Time','type' => 'time','required' => true]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('capstone::field'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'end_time','label' => 'End Time','type' => 'time','required' => true]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal32678ef1aa05abec5694cf306077ecae)): ?>
<?php $attributes = $__attributesOriginal32678ef1aa05abec5694cf306077ecae; ?>
<?php unset($__attributesOriginal32678ef1aa05abec5694cf306077ecae); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal32678ef1aa05abec5694cf306077ecae)): ?>
<?php $component = $__componentOriginal32678ef1aa05abec5694cf306077ecae; ?>
<?php unset($__componentOriginal32678ef1aa05abec5694cf306077ecae); ?>
<?php endif; ?></div>
                <?php if (isset($component)) { $__componentOriginal32678ef1aa05abec5694cf306077ecae = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal32678ef1aa05abec5694cf306077ecae = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'capstone::components.field','data' => ['name' => 'mode','label' => 'Mode','type' => 'select','options' => ['offline'=>'Offline','online'=>'Online']]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('capstone::field'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'mode','label' => 'Mode','type' => 'select','options' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(['offline'=>'Offline','online'=>'Online'])]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal32678ef1aa05abec5694cf306077ecae)): ?>
<?php $attributes = $__attributesOriginal32678ef1aa05abec5694cf306077ecae; ?>
<?php unset($__attributesOriginal32678ef1aa05abec5694cf306077ecae); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal32678ef1aa05abec5694cf306077ecae)): ?>
<?php $component = $__componentOriginal32678ef1aa05abec5694cf306077ecae; ?>
<?php unset($__componentOriginal32678ef1aa05abec5694cf306077ecae); ?>
<?php endif; ?>
                <template x-if="form.mode!=='online'"><div><?php if (isset($component)) { $__componentOriginal32678ef1aa05abec5694cf306077ecae = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal32678ef1aa05abec5694cf306077ecae = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'capstone::components.field','data' => ['name' => 'room','label' => 'Location','type' => 'select','options' => [''=>'Select location'],'required' => true]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('capstone::field'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'room','label' => 'Location','type' => 'select','options' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute([''=>'Select location']),'required' => true]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>
<template x-for="location in locations.filter(l=>l.type==='offline')" :key="location.id"><option :value="location.name" x-text="location.name"></option></template> <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal32678ef1aa05abec5694cf306077ecae)): ?>
<?php $attributes = $__attributesOriginal32678ef1aa05abec5694cf306077ecae; ?>
<?php unset($__attributesOriginal32678ef1aa05abec5694cf306077ecae); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal32678ef1aa05abec5694cf306077ecae)): ?>
<?php $component = $__componentOriginal32678ef1aa05abec5694cf306077ecae; ?>
<?php unset($__componentOriginal32678ef1aa05abec5694cf306077ecae); ?>
<?php endif; ?></div></template>
                <template x-if="form.mode==='online'"><div><?php if (isset($component)) { $__componentOriginal32678ef1aa05abec5694cf306077ecae = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal32678ef1aa05abec5694cf306077ecae = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'capstone::components.field','data' => ['name' => 'room','label' => 'Meeting Link / Platform','placeholder' => 'e.g., Zoom link or Google Meet URL','required' => true]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('capstone::field'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'room','label' => 'Meeting Link / Platform','placeholder' => 'e.g., Zoom link or Google Meet URL','required' => true]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal32678ef1aa05abec5694cf306077ecae)): ?>
<?php $attributes = $__attributesOriginal32678ef1aa05abec5694cf306077ecae; ?>
<?php unset($__attributesOriginal32678ef1aa05abec5694cf306077ecae); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal32678ef1aa05abec5694cf306077ecae)): ?>
<?php $component = $__componentOriginal32678ef1aa05abec5694cf306077ecae; ?>
<?php unset($__componentOriginal32678ef1aa05abec5694cf306077ecae); ?>
<?php endif; ?></div></template>
                <?php if (isset($component)) { $__componentOriginal32678ef1aa05abec5694cf306077ecae = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal32678ef1aa05abec5694cf306077ecae = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'capstone::components.field','data' => ['name' => 'notes','label' => 'Notes (Optional)','type' => 'textarea','maxlength' => '1000']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('capstone::field'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'notes','label' => 'Notes (Optional)','type' => 'textarea','maxlength' => '1000']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal32678ef1aa05abec5694cf306077ecae)): ?>
<?php $attributes = $__attributesOriginal32678ef1aa05abec5694cf306077ecae; ?>
<?php unset($__attributesOriginal32678ef1aa05abec5694cf306077ecae); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal32678ef1aa05abec5694cf306077ecae)): ?>
<?php $component = $__componentOriginal32678ef1aa05abec5694cf306077ecae; ?>
<?php unset($__componentOriginal32678ef1aa05abec5694cf306077ecae); ?>
<?php endif; ?>
            </div>
            <div class="flex justify-end gap-2"><?php if (isset($component)) { $__componentOriginalb35a908784863ee4a31335a0ede028f1 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalb35a908784863ee4a31335a0ede028f1 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'capstone::components.button','data' => ['variant' => 'outline','@click' => '$el.closest(\'dialog\').close()']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('capstone::button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['variant' => 'outline','@click' => '$el.closest(\'dialog\').close()']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>
Cancel <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalb35a908784863ee4a31335a0ede028f1)): ?>
<?php $attributes = $__attributesOriginalb35a908784863ee4a31335a0ede028f1; ?>
<?php unset($__attributesOriginalb35a908784863ee4a31335a0ede028f1); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalb35a908784863ee4a31335a0ede028f1)): ?>
<?php $component = $__componentOriginalb35a908784863ee4a31335a0ede028f1; ?>
<?php unset($__componentOriginalb35a908784863ee4a31335a0ede028f1); ?>
<?php endif; ?><?php if (isset($component)) { $__componentOriginalb35a908784863ee4a31335a0ede028f1 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalb35a908784863ee4a31335a0ede028f1 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'capstone::components.button','data' => ['type' => 'submit',':disabled' => 'saving']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('capstone::button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['type' => 'submit',':disabled' => 'saving']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>
<span x-text="saving ? 'Saving...' : editing ? 'Save Changes' : 'Create Schedule'"></span> <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalb35a908784863ee4a31335a0ede028f1)): ?>
<?php $attributes = $__attributesOriginalb35a908784863ee4a31335a0ede028f1; ?>
<?php unset($__attributesOriginalb35a908784863ee4a31335a0ede028f1); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalb35a908784863ee4a31335a0ede028f1)): ?>
<?php $component = $__componentOriginalb35a908784863ee4a31335a0ede028f1; ?>
<?php unset($__componentOriginalb35a908784863ee4a31335a0ede028f1); ?>
<?php endif; ?></div>
        </form>
     <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal23d0c1a5dcb8d467892a41eeb359fba6)): ?>
<?php $attributes = $__attributesOriginal23d0c1a5dcb8d467892a41eeb359fba6; ?>
<?php unset($__attributesOriginal23d0c1a5dcb8d467892a41eeb359fba6); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal23d0c1a5dcb8d467892a41eeb359fba6)): ?>
<?php $component = $__componentOriginal23d0c1a5dcb8d467892a41eeb359fba6; ?>
<?php unset($__componentOriginal23d0c1a5dcb8d467892a41eeb359fba6); ?>
<?php endif; ?>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('capstone::layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\WebsiteTekkom - Copy\Modules\Capstone\tests/../resources/views/pages\dosen\schedule.blade.php ENDPATH**/ ?>