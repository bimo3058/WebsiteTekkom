<div class="mx-auto max-w-2xl space-y-8"><div class="flex items-start gap-3 border-b pb-4"><div class="rounded-lg bg-blue-50 p-2"><?php if (isset($component)) { $__componentOriginal0ebc33cc959e9b0fde2e3c32f43abd3a = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal0ebc33cc959e9b0fde2e3c32f43abd3a = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'capstone::components.icon','data' => ['name' => 'Users','class' => 'h-5 w-5 text-blue-600']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('capstone::icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'Users','class' => 'h-5 w-5 text-blue-600']); ?>
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
<?php endif; ?></div><div><h2 class="text-lg font-semibold text-gray-900">Konfigurasi Group</h2><p class="text-sm text-gray-500">Atur batasan jumlah anggota group dan beban dosen</p></div></div><div class="space-y-6"><div class="grid grid-cols-2 gap-6"><?php if (isset($component)) { $__componentOriginal32678ef1aa05abec5694cf306077ecae = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal32678ef1aa05abec5694cf306077ecae = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'capstone::components.field','data' => ['name' => 'min_group_size','label' => 'Minimal Jumlah Anggota','type' => 'number','min' => '1','max' => '10','class' => 'h-11','required' => true]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('capstone::field'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'min_group_size','label' => 'Minimal Jumlah Anggota','type' => 'number','min' => '1','max' => '10','class' => 'h-11','required' => true]); ?>
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
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'capstone::components.field','data' => ['name' => 'max_group_size','label' => 'Maximal Jumlah Anggota','type' => 'number','min' => '1','max' => '10','class' => 'h-11','required' => true]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('capstone::field'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'max_group_size','label' => 'Maximal Jumlah Anggota','type' => 'number','min' => '1','max' => '10','class' => 'h-11','required' => true]); ?>
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
<?php endif; ?></div><?php if (isset($component)) { $__componentOriginal32678ef1aa05abec5694cf306077ecae = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal32678ef1aa05abec5694cf306077ecae = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'capstone::components.field','data' => ['name' => 'max_supervisor_load','label' => 'Maximal Dosen Pembimbing (group/dosen)','type' => 'number','min' => '1','max' => '50','class' => 'h-11','required' => true]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('capstone::field'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'max_supervisor_load','label' => 'Maximal Dosen Pembimbing (group/dosen)','type' => 'number','min' => '1','max' => '50','class' => 'h-11','required' => true]); ?>
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
<?php endif; ?><div class="flex items-start gap-3 rounded-lg border border-amber-200 bg-amber-50 p-4"><?php if (isset($component)) { $__componentOriginal0ebc33cc959e9b0fde2e3c32f43abd3a = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal0ebc33cc959e9b0fde2e3c32f43abd3a = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'capstone::components.icon','data' => ['name' => 'UserCheck','class' => 'mt-0.5 h-5 w-5 text-amber-600']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('capstone::icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'UserCheck','class' => 'mt-0.5 h-5 w-5 text-amber-600']); ?>
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
<?php endif; ?><div class="space-y-1"><p class="text-sm font-medium text-amber-900">Informasi Beban Dosen</p><p class="text-sm text-amber-700">Maximal Dosen Pembimbing menentukan jumlah maksimal group yang dapat dibimbing oleh satu dosen dalam periode ini. Ini membantu menjaga keseimbangan beban mengajar dan memastikan kualitas bimbingan yang optimal.</p></div></div></div></div>
<?php /**PATH C:\WebsiteTekkom - Copy\Modules\Capstone\resources\views\pages\admin\periods\steps\group.blade.php ENDPATH**/ ?>