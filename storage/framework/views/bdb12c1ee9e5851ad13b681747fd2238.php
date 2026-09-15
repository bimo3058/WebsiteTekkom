<?php $__env->startSection('title','Tugas Akhir (TA)'); ?>
<?php $__env->startSection('content'); ?>
<div x-data="capstoneTaSubmission" class="space-y-6 max-w-5xl mx-auto">
    <?php echo $__env->make('capstone::partials.loading', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    <div x-show="!loading && !error" x-cloak class="space-y-6">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4"><div><h1 class="text-3xl font-bold tracking-tight">Tugas Akhir (TA)</h1><p class="text-muted-foreground" x-text="locked?'Individual thesis phase.':'Individual thesis submission and defense.'"></p></div><span x-show="!locked" class="inline-flex items-center rounded-md px-3 py-1 text-sm font-medium" :class="config.color"><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = ['Lock','FileText','Clock','CheckCircle','Calendar','FileCheck']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $icon): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?><?php if (isset($component)) { $__componentOriginal0ebc33cc959e9b0fde2e3c32f43abd3a = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal0ebc33cc959e9b0fde2e3c32f43abd3a = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'capstone::components.icon','data' => ['name' => $icon,'class' => 'w-4 h-4 mr-1','xShow' => 'config.icon===\''.e($icon).'\'']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('capstone::icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($icon),'class' => 'w-4 h-4 mr-1','x-show' => 'config.icon===\''.e($icon).'\'']); ?>
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
<?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?><span x-text="config.label"></span></span></div>
        <?php echo $__env->make('capstone::pages.mahasiswa.ta-submission.locked', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
        <template x-if="data && !locked"><div class="space-y-6">
            <?php if (isset($component)) { $__componentOriginal5ac34a7ad891af7fa808b2dbb0e60582 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal5ac34a7ad891af7fa808b2dbb0e60582 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'capstone::components.card','data' => []] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('capstone::card'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>
<div class="px-6 pb-3"><h2 class="text-lg font-semibold flex items-center gap-2"><?php if (isset($component)) { $__componentOriginal0ebc33cc959e9b0fde2e3c32f43abd3a = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal0ebc33cc959e9b0fde2e3c32f43abd3a = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'capstone::components.icon','data' => ['name' => 'User','class' => 'w-5 h-5']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('capstone::icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'User','class' => 'w-5 h-5']); ?>
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
<?php endif; ?>Student Information</h2></div><div class="px-6 flex flex-col md:flex-row gap-6"><div class="flex items-center gap-4"><span class="h-16 w-16 shrink-0 rounded-full flex items-center justify-center text-lg bg-primary/10 text-primary"><?php echo e(mb_substr($actor['name'] ?? 'Student',0,1)); ?></span><div><p class="font-semibold text-lg"><?php echo e($actor['name'] ?? 'Student'); ?></p><p class="text-sm text-muted-foreground">Individual TA Phase</p></div></div><div class="hidden md:block h-16 w-px bg-border"></div><div class="flex-1 grid grid-cols-1 md:grid-cols-2 gap-4"><div><p class="text-sm text-muted-foreground">Group</p><p class="font-medium flex items-center gap-2"><?php if (isset($component)) { $__componentOriginal0ebc33cc959e9b0fde2e3c32f43abd3a = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal0ebc33cc959e9b0fde2e3c32f43abd3a = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'capstone::components.icon','data' => ['name' => 'Users','class' => 'w-4 h-4']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('capstone::icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'Users','class' => 'w-4 h-4']); ?>
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
<?php endif; ?><span x-text="'Group '+(data.group?.id || 'N/A')"></span></p></div><div class="min-w-0"><p class="text-sm text-muted-foreground">Title</p><p class="font-medium truncate" x-text="data.group?.title?.title || 'No title assigned'"></p></div><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = [1,2]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $number): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?><div><p class="text-sm text-muted-foreground">Supervisor <?php echo e($number); ?></p><p class="font-medium" x-text="data.group?.supervisor<?php echo e($number); ?>?.name || 'Not assigned'"></p></div><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?></div></div> <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal5ac34a7ad891af7fa808b2dbb0e60582)): ?>
<?php $attributes = $__attributesOriginal5ac34a7ad891af7fa808b2dbb0e60582; ?>
<?php unset($__attributesOriginal5ac34a7ad891af7fa808b2dbb0e60582); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal5ac34a7ad891af7fa808b2dbb0e60582)): ?>
<?php $component = $__componentOriginal5ac34a7ad891af7fa808b2dbb0e60582; ?>
<?php unset($__componentOriginal5ac34a7ad891af7fa808b2dbb0e60582); ?>
<?php endif; ?>
            <?php if (isset($component)) { $__componentOriginal5ac34a7ad891af7fa808b2dbb0e60582 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal5ac34a7ad891af7fa808b2dbb0e60582 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'capstone::components.card','data' => ['title' => 'Progress Overview','description' => 'Track your TA submission progress']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('capstone::card'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => 'Progress Overview','description' => 'Track your TA submission progress']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>
<div class="px-6"><div class="relative"><div class="hidden md:block absolute top-6 left-0 right-0 h-0.5 bg-muted"></div><div class="grid grid-cols-1 md:grid-cols-5 gap-4 md:gap-2">
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = [['Upload Documents','Submit all required documents'],['Document Review','Supervisor review in progress'],['Documents Approved','Ready for scheduling'],['Sidang Scheduled','Defense date confirmed'],['Defense Complete','TA defense finished']]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index=>$step): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                <div class="relative flex md:flex-col items-start md:items-center gap-3 md:gap-2"><div class="relative z-10 flex h-12 w-12 shrink-0 items-center justify-center rounded-full border-2 transition-all duration-300" :class="<?php echo e($index); ?><currentStep?'bg-primary border-primary text-primary-foreground':<?php echo e($index); ?>===currentStep?'bg-primary border-primary text-primary-foreground ring-4 ring-primary/20':'bg-background border-muted text-muted-foreground'"><?php if (isset($component)) { $__componentOriginal0ebc33cc959e9b0fde2e3c32f43abd3a = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal0ebc33cc959e9b0fde2e3c32f43abd3a = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'capstone::components.icon','data' => ['name' => 'CheckCircle','class' => 'h-5 w-5','xShow' => ''.e($index).'<currentStep']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('capstone::icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'CheckCircle','class' => 'h-5 w-5','x-show' => ''.e($index).'<currentStep']); ?>
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
<?php endif; ?><span class="text-sm font-semibold" x-show="<?php echo e($index); ?>>=currentStep"><?php echo e($index+1); ?></span></div><div class="flex-1 md:text-center"><p class="text-sm font-medium" :class="<?php echo e($index); ?>===currentStep?'text-primary':<?php echo e($index); ?><currentStep?'text-foreground':'text-muted-foreground'"><?php echo e($step[0]); ?></p><p class="text-xs text-muted-foreground hidden md:block"><?php echo e($step[1]); ?></p></div><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($index<4): ?><div class="md:hidden absolute left-6 top-12 w-0.5 h-full" :class="<?php echo e($index); ?><currentStep?'bg-primary':'bg-muted'"></div><?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?></div>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
            </div></div></div> <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal5ac34a7ad891af7fa808b2dbb0e60582)): ?>
<?php $attributes = $__attributesOriginal5ac34a7ad891af7fa808b2dbb0e60582; ?>
<?php unset($__attributesOriginal5ac34a7ad891af7fa808b2dbb0e60582); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal5ac34a7ad891af7fa808b2dbb0e60582)): ?>
<?php $component = $__componentOriginal5ac34a7ad891af7fa808b2dbb0e60582; ?>
<?php unset($__componentOriginal5ac34a7ad891af7fa808b2dbb0e60582); ?>
<?php endif; ?>
            <div role="status" class="rounded-lg border p-4 flex gap-3" :class="config.color"><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = ['FileText','Clock','CheckCircle','Calendar','FileCheck','Lock']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $icon): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?><?php if (isset($component)) { $__componentOriginal0ebc33cc959e9b0fde2e3c32f43abd3a = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal0ebc33cc959e9b0fde2e3c32f43abd3a = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'capstone::components.icon','data' => ['name' => $icon,'class' => 'h-4 w-4 shrink-0 mt-0.5','xShow' => 'config.icon===\''.e($icon).'\'']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('capstone::icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($icon),'class' => 'h-4 w-4 shrink-0 mt-0.5','x-show' => 'config.icon===\''.e($icon).'\'']); ?>
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
<?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?><div><h2 class="font-semibold" x-text="config.label"></h2><p class="text-sm" x-text="config.description"></p></div></div>
            <?php echo $__env->make('capstone::pages.mahasiswa.ta-submission.documents', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
            <?php if (isset($component)) { $__componentOriginal5ac34a7ad891af7fa808b2dbb0e60582 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal5ac34a7ad891af7fa808b2dbb0e60582 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'capstone::components.card','data' => ['xShow' => 'currentStatus===\'TA_DOCUMENTS_APPROVED\'','class' => 'bg-green-50 border-green-200']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('capstone::card'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['x-show' => 'currentStatus===\'TA_DOCUMENTS_APPROVED\'','class' => 'bg-green-50 border-green-200']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>
<h2 class="px-6 text-lg font-semibold flex items-center gap-2 text-green-800"><?php if (isset($component)) { $__componentOriginal0ebc33cc959e9b0fde2e3c32f43abd3a = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal0ebc33cc959e9b0fde2e3c32f43abd3a = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'capstone::components.icon','data' => ['name' => 'CheckCircle','class' => 'w-5 h-5']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('capstone::icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'CheckCircle','class' => 'w-5 h-5']); ?>
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
<?php endif; ?>Documents Approved!</h2><p class="px-6 text-green-700">All your TA documents have been approved. You are now waiting for the administrator to schedule your sidang TA.</p> <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal5ac34a7ad891af7fa808b2dbb0e60582)): ?>
<?php $attributes = $__attributesOriginal5ac34a7ad891af7fa808b2dbb0e60582; ?>
<?php unset($__attributesOriginal5ac34a7ad891af7fa808b2dbb0e60582); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal5ac34a7ad891af7fa808b2dbb0e60582)): ?>
<?php $component = $__componentOriginal5ac34a7ad891af7fa808b2dbb0e60582; ?>
<?php unset($__componentOriginal5ac34a7ad891af7fa808b2dbb0e60582); ?>
<?php endif; ?>
            <?php echo $__env->make('capstone::pages.mahasiswa.ta-submission.defense', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
            <?php if (isset($component)) { $__componentOriginal5ac34a7ad891af7fa808b2dbb0e60582 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal5ac34a7ad891af7fa808b2dbb0e60582 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'capstone::components.card','data' => ['class' => 'bg-muted/50']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('capstone::card'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'bg-muted/50']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>
<h2 class="px-6 text-base font-semibold flex items-center gap-2"><?php if (isset($component)) { $__componentOriginal0ebc33cc959e9b0fde2e3c32f43abd3a = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal0ebc33cc959e9b0fde2e3c32f43abd3a = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'capstone::components.icon','data' => ['name' => 'Info','class' => 'w-4 h-4']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('capstone::icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'Info','class' => 'w-4 h-4']); ?>
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
<?php endif; ?>Important Information</h2><ul class="px-6 space-y-2 text-sm text-muted-foreground"><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = ['Upload all required documents to proceed to sidang TA.','Documents must be approved by your supervisors before scheduling.','Contact your supervisor for guidance during the TA phase.']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $info): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?><li class="flex items-start gap-2"><?php if (isset($component)) { $__componentOriginal0ebc33cc959e9b0fde2e3c32f43abd3a = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal0ebc33cc959e9b0fde2e3c32f43abd3a = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'capstone::components.icon','data' => ['name' => 'CheckCircle','class' => 'h-4 w-4 shrink-0 mt-0.5 text-primary']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('capstone::icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'CheckCircle','class' => 'h-4 w-4 shrink-0 mt-0.5 text-primary']); ?>
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
<?php endif; ?><span><?php echo e($info); ?></span></li><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?></ul> <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal5ac34a7ad891af7fa808b2dbb0e60582)): ?>
<?php $attributes = $__attributesOriginal5ac34a7ad891af7fa808b2dbb0e60582; ?>
<?php unset($__attributesOriginal5ac34a7ad891af7fa808b2dbb0e60582); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal5ac34a7ad891af7fa808b2dbb0e60582)): ?>
<?php $component = $__componentOriginal5ac34a7ad891af7fa808b2dbb0e60582; ?>
<?php unset($__componentOriginal5ac34a7ad891af7fa808b2dbb0e60582); ?>
<?php endif; ?>
        </div></template>
    </div>
    <?php if (isset($component)) { $__componentOriginal23d0c1a5dcb8d467892a41eeb359fba6 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal23d0c1a5dcb8d467892a41eeb359fba6 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'capstone::components.dialog','data' => ['id' => 'ta-upload','title' => 'Upload Document','width' => 'sm:max-w-[500px]']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('capstone::dialog'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['id' => 'ta-upload','title' => 'Upload Document','width' => 'sm:max-w-[500px]']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>
<p class="text-sm text-muted-foreground" x-text="'Upload '+selectedType+' for TA submission'"></p><form @submit.prevent="submit"><div class="grid gap-4 py-4"><div class="grid gap-2"><label for="ta-file" class="text-sm font-medium">Document File</label><?php if (isset($component)) { $__componentOriginal2f30e5e2854777f60031a186edf35415 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal2f30e5e2854777f60031a186edf35415 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'capstone::components.input','data' => ['id' => 'ta-file','type' => 'file','accept' => '.pdf,.doc,.docx','@change' => 'selectFile($event.target.files[0])',':disabled' => 'uploading']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('capstone::input'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['id' => 'ta-file','type' => 'file','accept' => '.pdf,.doc,.docx','@change' => 'selectFile($event.target.files[0])',':disabled' => 'uploading']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal2f30e5e2854777f60031a186edf35415)): ?>
<?php $attributes = $__attributesOriginal2f30e5e2854777f60031a186edf35415; ?>
<?php unset($__attributesOriginal2f30e5e2854777f60031a186edf35415); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal2f30e5e2854777f60031a186edf35415)): ?>
<?php $component = $__componentOriginal2f30e5e2854777f60031a186edf35415; ?>
<?php unset($__componentOriginal2f30e5e2854777f60031a186edf35415); ?>
<?php endif; ?><p class="text-xs text-muted-foreground">Accepted formats: PDF, DOC, DOCX. Max size: 10MB</p></div><div x-show="file" class="p-3 bg-muted rounded-lg"><p class="text-sm font-medium">Selected file:</p><p class="text-sm text-muted-foreground" x-text="file?.name"></p><p class="text-xs text-muted-foreground" x-text="((file?.size || 0)/1024/1024).toFixed(2)+' MB'"></p></div><div x-show="uploading" class="space-y-2"><div class="flex justify-between text-sm"><span>Uploading...</span><span x-text="progress+'%'"></span></div><div role="progressbar" aria-valuemin="0" aria-valuemax="100" :aria-valuenow="progress" class="h-2 bg-primary/20 rounded-full overflow-hidden"><div class="h-full bg-primary transition-all" :style="{width:progress+'%'}"></div></div></div></div><div class="flex justify-end gap-2"><?php if (isset($component)) { $__componentOriginalb35a908784863ee4a31335a0ede028f1 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalb35a908784863ee4a31335a0ede028f1 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'capstone::components.button','data' => ['variant' => 'outline',':disabled' => 'uploading','@click' => 'document.getElementById(\'ta-upload\').close()']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('capstone::button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['variant' => 'outline',':disabled' => 'uploading','@click' => 'document.getElementById(\'ta-upload\').close()']); ?>
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
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'capstone::components.button','data' => ['type' => 'submit',':disabled' => '!file || uploading']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('capstone::button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['type' => 'submit',':disabled' => '!file || uploading']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>
<?php if (isset($component)) { $__componentOriginal0ebc33cc959e9b0fde2e3c32f43abd3a = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal0ebc33cc959e9b0fde2e3c32f43abd3a = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'capstone::components.icon','data' => ['name' => 'Upload','class' => 'mr-2 h-4 w-4','xShow' => '!uploading']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('capstone::icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'Upload','class' => 'mr-2 h-4 w-4','x-show' => '!uploading']); ?>
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
<?php endif; ?><?php if (isset($component)) { $__componentOriginal0ebc33cc959e9b0fde2e3c32f43abd3a = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal0ebc33cc959e9b0fde2e3c32f43abd3a = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'capstone::components.icon','data' => ['name' => 'Loader2','class' => 'mr-2 h-4 w-4 animate-spin','xShow' => 'uploading']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('capstone::icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'Loader2','class' => 'mr-2 h-4 w-4 animate-spin','x-show' => 'uploading']); ?>
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
<?php endif; ?><span x-text="uploading?'Uploading...':'Upload Document'"></span> <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalb35a908784863ee4a31335a0ede028f1)): ?>
<?php $attributes = $__attributesOriginalb35a908784863ee4a31335a0ede028f1; ?>
<?php unset($__attributesOriginalb35a908784863ee4a31335a0ede028f1); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalb35a908784863ee4a31335a0ede028f1)): ?>
<?php $component = $__componentOriginalb35a908784863ee4a31335a0ede028f1; ?>
<?php unset($__componentOriginalb35a908784863ee4a31335a0ede028f1); ?>
<?php endif; ?></div></form> <?php echo $__env->renderComponent(); ?>
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

<?php echo $__env->make('capstone::layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\WebsiteTekkom - Copy\Modules\Capstone\resources\views\pages\mahasiswa\ta-submission\index.blade.php ENDPATH**/ ?>