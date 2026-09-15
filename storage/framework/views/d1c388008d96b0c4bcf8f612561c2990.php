<div x-data="{menu:false}" @click.outside="menu=false" @keydown.escape.window="menu=false" class="relative text-center">
<?php if (isset($component)) { $__componentOriginalb35a908784863ee4a31335a0ede028f1 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalb35a908784863ee4a31335a0ede028f1 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'capstone::components.button','data' => ['variant' => 'ghost','size' => 'icon','class' => 'h-8 w-8','@click' => 'menu=!menu','ariaLabel' => 'Actions',':ariaExpanded' => 'menu']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('capstone::button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['variant' => 'ghost','size' => 'icon','class' => 'h-8 w-8','@click' => 'menu=!menu','aria-label' => 'Actions',':aria-expanded' => 'menu']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>
<?php if (isset($component)) { $__componentOriginal0ebc33cc959e9b0fde2e3c32f43abd3a = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal0ebc33cc959e9b0fde2e3c32f43abd3a = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'capstone::components.icon','data' => ['name' => 'Ellipsis','class' => 'h-4 w-4 text-gray-500']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('capstone::icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'Ellipsis','class' => 'h-4 w-4 text-gray-500']); ?>
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
<?php endif; ?> <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalb35a908784863ee4a31335a0ede028f1)): ?>
<?php $attributes = $__attributesOriginalb35a908784863ee4a31335a0ede028f1; ?>
<?php unset($__attributesOriginalb35a908784863ee4a31335a0ede028f1); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalb35a908784863ee4a31335a0ede028f1)): ?>
<?php $component = $__componentOriginalb35a908784863ee4a31335a0ede028f1; ?>
<?php unset($__componentOriginalb35a908784863ee4a31335a0ede028f1); ?>
<?php endif; ?>
<div x-show="menu" x-cloak class="absolute right-0 z-20 min-w-32 rounded-md border bg-popover p-1 shadow-md text-left">
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(isset($detailPath)): ?><?php if (isset($component)) { $__componentOriginal751c60d6b55ecec60fe692173fafc657 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal751c60d6b55ecec60fe692173fafc657 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'capstone::components.feature-link','data' => ['href' => $detailPath,':href' => 'url(\''.e($detailPath).'\'.replace(\'{id}\',item.id))','class' => 'flex items-center gap-2 rounded-sm px-2 py-1.5 text-sm hover:bg-accent']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('capstone::feature-link'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['href' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($detailPath),':href' => 'url(\''.e($detailPath).'\'.replace(\'{id}\',item.id))','class' => 'flex items-center gap-2 rounded-sm px-2 py-1.5 text-sm hover:bg-accent']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>
<?php if (isset($component)) { $__componentOriginal0ebc33cc959e9b0fde2e3c32f43abd3a = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal0ebc33cc959e9b0fde2e3c32f43abd3a = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'capstone::components.icon','data' => ['name' => 'Eye']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('capstone::icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'Eye']); ?>
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
<?php endif; ?>Detail <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal751c60d6b55ecec60fe692173fafc657)): ?>
<?php $attributes = $__attributesOriginal751c60d6b55ecec60fe692173fafc657; ?>
<?php unset($__attributesOriginal751c60d6b55ecec60fe692173fafc657); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal751c60d6b55ecec60fe692173fafc657)): ?>
<?php $component = $__componentOriginal751c60d6b55ecec60fe692173fafc657; ?>
<?php unset($__componentOriginal751c60d6b55ecec60fe692173fafc657); ?>
<?php endif; ?><?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($canEdit ?? true): ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(isset($editPath)): ?><?php if (isset($component)) { $__componentOriginal751c60d6b55ecec60fe692173fafc657 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal751c60d6b55ecec60fe692173fafc657 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'capstone::components.feature-link','data' => ['href' => $editPath,':href' => 'url(\''.e($editPath).'\'.replace(\'{id}\',item.id))','class' => 'flex items-center gap-2 rounded-sm px-2 py-1.5 text-sm hover:bg-accent']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('capstone::feature-link'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['href' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($editPath),':href' => 'url(\''.e($editPath).'\'.replace(\'{id}\',item.id))','class' => 'flex items-center gap-2 rounded-sm px-2 py-1.5 text-sm hover:bg-accent']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>
<?php if (isset($component)) { $__componentOriginal0ebc33cc959e9b0fde2e3c32f43abd3a = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal0ebc33cc959e9b0fde2e3c32f43abd3a = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'capstone::components.icon','data' => ['name' => 'Edit']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('capstone::icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'Edit']); ?>
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
<?php endif; ?>Edit <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal751c60d6b55ecec60fe692173fafc657)): ?>
<?php $attributes = $__attributesOriginal751c60d6b55ecec60fe692173fafc657; ?>
<?php unset($__attributesOriginal751c60d6b55ecec60fe692173fafc657); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal751c60d6b55ecec60fe692173fafc657)): ?>
<?php $component = $__componentOriginal751c60d6b55ecec60fe692173fafc657; ?>
<?php unset($__componentOriginal751c60d6b55ecec60fe692173fafc657); ?>
<?php endif; ?><?php else: ?><button type="button" class="flex w-full items-center gap-2 rounded-sm px-2 py-1.5 text-sm hover:bg-accent" @click="menu=false;edit(item)"><?php if (isset($component)) { $__componentOriginal0ebc33cc959e9b0fde2e3c32f43abd3a = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal0ebc33cc959e9b0fde2e3c32f43abd3a = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'capstone::components.icon','data' => ['name' => 'Edit']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('capstone::icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'Edit']); ?>
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
<?php endif; ?>Edit</button><?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?> <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($canDelete ?? true): ?><button type="button" class="flex w-full items-center gap-2 rounded-sm px-2 py-1.5 text-sm text-red-600 hover:bg-accent" @click="menu=false;confirmDelete(item)"><?php if (isset($component)) { $__componentOriginal0ebc33cc959e9b0fde2e3c32f43abd3a = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal0ebc33cc959e9b0fde2e3c32f43abd3a = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'capstone::components.icon','data' => ['name' => 'Trash2']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('capstone::icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'Trash2']); ?>
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
<?php endif; ?>Hapus</button><?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
</div></div>
<?php /**PATH C:\WebsiteTekkom - Copy\Modules\Capstone\resources\views\partials\row-actions.blade.php ENDPATH**/ ?>