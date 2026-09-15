<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames((['name','label','type'=>'text','placeholder'=>'','required'=>false,'options'=>[]]));

foreach ($attributes->all() as $__key => $__value) {
    if (in_array($__key, $__propNames)) {
        $$__key = $$__key ?? $__value;
    } else {
        $__newAttributes[$__key] = $__value;
    }
}

$attributes = new \Illuminate\View\ComponentAttributeBag($__newAttributes);

unset($__propNames);
unset($__newAttributes);

foreach (array_filter((['name','label','type'=>'text','placeholder'=>'','required'=>false,'options'=>[]]), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars, $__key, $__value); ?>
<div class="space-y-2 <?php echo e($type === 'textarea' ? 'col-span-full' : ''); ?>">
    <label for="field-<?php echo e($name); ?>" class="text-sm font-medium leading-none"><?php echo e($label); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($required): ?><span class="ml-0.5 text-red-500">*</span><?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?></label>
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($type==='textarea'): ?><textarea id="field-<?php echo e($name); ?>" name="<?php echo e($name); ?>" x-model="form.<?php echo e($name); ?>" placeholder="<?php echo e($placeholder); ?>" <?php if($required): echo 'required'; endif; ?> class="border-input placeholder:text-muted-foreground min-h-24 w-full rounded-md border bg-transparent px-3 py-2 text-sm shadow-xs outline-none focus-visible:ring-2 focus-visible:ring-ring" <?php echo e($attributes); ?>></textarea>
    <?php elseif($type==='select'): ?><select id="field-<?php echo e($name); ?>" name="<?php echo e($name); ?>" x-model="form.<?php echo e($name); ?>" <?php if($required): echo 'required'; endif; ?> class="border-input h-9 w-full rounded-md border bg-transparent px-3 text-sm shadow-xs" <?php echo e($attributes); ?>><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $options; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key=>$value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?><option value="<?php echo e($key); ?>"><?php echo e($value); ?></option><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?><?php echo e($slot); ?></select>
    <?php elseif($type==='checkbox'): ?><div><input id="field-<?php echo e($name); ?>" name="<?php echo e($name); ?>" type="checkbox" role="switch" x-model="form.<?php echo e($name); ?>" class="h-4 w-8 accent-primary" <?php echo e($attributes); ?>></div>
    <?php else: ?><?php if (isset($component)) { $__componentOriginal2f30e5e2854777f60031a186edf35415 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal2f30e5e2854777f60031a186edf35415 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'capstone::components.input','data' => ['id' => 'field-'.e($name).'','name' => $name,'type' => $type,'xModel' => 'form.'.e($name).'','placeholder' => $placeholder,'required' => $required,'attributes' => $attributes]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('capstone::input'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['id' => 'field-'.e($name).'','name' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($name),'type' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($type),'x-model' => 'form.'.e($name).'','placeholder' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($placeholder),'required' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($required),'attributes' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($attributes)]); ?>
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
<?php endif; ?><?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    <p class="text-destructive text-sm" x-show="errors['<?php echo e($name); ?>']" x-text="errors['<?php echo e($name); ?>']?.[0]"></p>
</div>
<?php /**PATH C:\WebsiteTekkom - Copy\Modules\Capstone\resources\views\components\field.blade.php ENDPATH**/ ?>