<div x-show="locked">
    <template x-if="readiness"><div role="alert" class="rounded-lg border border-amber-500 bg-amber-50 p-4 flex items-start gap-3"><?php if (isset($component)) { $__componentOriginal0ebc33cc959e9b0fde2e3c32f43abd3a = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal0ebc33cc959e9b0fde2e3c32f43abd3a = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'capstone::components.icon','data' => ['name' => 'AlertTriangle','class' => 'h-4 w-4 text-amber-600 shrink-0 mt-0.5']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('capstone::icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'AlertTriangle','class' => 'h-4 w-4 text-amber-600 shrink-0 mt-0.5']); ?>
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
<?php endif; ?><div class="text-sm text-amber-700"><h2 class="font-medium text-amber-800 mb-1">🔒 Belum Ready for TA Individual</h2><p x-show="!readiness.expo_completed" class="mb-2">Complete EXPO with your group.</p><div x-show="!readiness.expo_documents.completed" class="mb-2"><p class="font-medium">Dokumen EXPO belum lengkap/disetujui:</p><ul class="mt-1 list-disc pl-5 space-y-1"><template x-for="type in readiness.expo_documents.pending_types" :key="type"><li x-text="type"></li></template></ul></div>
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = ['nilai_dosen'=>['NILAI_DOSEN','Menunggu NILAI_DOSEN dosen pembimbing:'],'milestone'=>['MILESTONE','Menunggu penilaian MILESTONE dosen pembimbing:'],'expo_evaluation'=>['evaluasi EXPO','Menunggu evaluasi EXPO dari dosen pembimbing:']]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key=>[$label,$message]): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
        <p x-show="!readiness.<?php echo e($key); ?>.configured" class="mb-2">Komponen <?php echo e($label); ?> belum dikonfigurasi admin untuk periode ini.</p><div x-show="readiness.<?php echo e($key); ?>.configured && !readiness.<?php echo e($key); ?>.completed" class="mb-2"><p class="font-medium"><?php echo e($message); ?></p><ul class="mt-1 space-y-1"><template x-for="supervisor in readiness.<?php echo e($key); ?>.supervisors.filter(s=>s.status==='pending')" :key="supervisor.id"><li><span x-text="supervisor.name+' ('+roleLabel(supervisor.role)+')'"></span><span class="text-sm text-muted-foreground ml-2" x-text="'- '+supervisor.submitted_components+'/'+supervisor.total_components+' komponen'"></span></li></template></ul></div>
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
        <p x-show="!readiness.peer_review.configured" class="mb-2">Indikator peer review belum diset oleh admin pada Evaluation Setup.</p><div x-show="readiness.peer_review.configured && !readiness.peer_review.completed"><p class="font-medium">Peer review belum lengkap:</p><p class="text-sm text-muted-foreground mt-1" x-text="'Progress: '+readiness.peer_review.completed_members+'/'+readiness.peer_review.total_members+' mahasiswa selesai.'"></p><ul class="mt-1 space-y-1"><template x-for="student in readiness.peer_review.incomplete_students" :key="student.student_id"><li x-text="student.student_name+' ('+student.student_nim+')'"></li></template></ul></div>
    </div></div></template>
    <?php if (isset($component)) { $__componentOriginal5ac34a7ad891af7fa808b2dbb0e60582 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal5ac34a7ad891af7fa808b2dbb0e60582 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'capstone::components.card','data' => ['xShow' => '!readiness','class' => 'border-red-200 bg-red-50']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('capstone::card'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['x-show' => '!readiness','class' => 'border-red-200 bg-red-50']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>
<div class="px-6"><h2 class="font-semibold text-red-800 flex items-center gap-2"><?php if (isset($component)) { $__componentOriginal0ebc33cc959e9b0fde2e3c32f43abd3a = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal0ebc33cc959e9b0fde2e3c32f43abd3a = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'capstone::components.icon','data' => ['name' => 'Lock','class' => 'h-5 w-5 text-red-600']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('capstone::icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'Lock','class' => 'h-5 w-5 text-red-600']); ?>
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
<?php endif; ?>TA Phase Locked</h2><p class="text-sm text-red-700 mt-2">TA phase is currently locked.</p></div><div class="px-6"><p class="text-sm text-red-700 mb-4">To unlock TA phase, you need to:</p><ol class="space-y-3"><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = ['Complete EXPO with your group','Submit peer review for all group members']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $i=>$message): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?><li class="flex items-start gap-3"><span class="flex items-center justify-center w-6 h-6 rounded-full bg-red-200 text-red-700 text-xs font-semibold"><?php echo e($i+1); ?></span><span class="text-sm text-red-700"><?php echo e($message); ?></span></li><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?></ol></div> <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal5ac34a7ad891af7fa808b2dbb0e60582)): ?>
<?php $attributes = $__attributesOriginal5ac34a7ad891af7fa808b2dbb0e60582; ?>
<?php unset($__attributesOriginal5ac34a7ad891af7fa808b2dbb0e60582); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal5ac34a7ad891af7fa808b2dbb0e60582)): ?>
<?php $component = $__componentOriginal5ac34a7ad891af7fa808b2dbb0e60582; ?>
<?php unset($__componentOriginal5ac34a7ad891af7fa808b2dbb0e60582); ?>
<?php endif; ?>
</div>
<?php /**PATH C:\WebsiteTekkom - Copy\Modules/Capstone\resources/views/pages/mahasiswa/ta-submission/locked.blade.php ENDPATH**/ ?>