<div class="rounded-xl border bg-card"><div class="flex items-center justify-between p-4 pb-3"><h3 class="font-semibold text-sm"><?php echo e($heading); ?></h3><?php if (isset($component)) { $__componentOriginal751c60d6b55ecec60fe692173fafc657 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal751c60d6b55ecec60fe692173fafc657 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'capstone::components.feature-link','data' => ['xShow' => 'groups.length','href' => $link,'class' => 'text-xs text-primary hover:underline flex items-center gap-1']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('capstone::feature-link'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['x-show' => 'groups.length','href' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($link),'class' => 'text-xs text-primary hover:underline flex items-center gap-1']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>
View All <?php if (isset($component)) { $__componentOriginal0ebc33cc959e9b0fde2e3c32f43abd3a = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal0ebc33cc959e9b0fde2e3c32f43abd3a = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'capstone::components.icon','data' => ['name' => 'ArrowRight','class' => 'h-3 w-3']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('capstone::icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'ArrowRight','class' => 'h-3 w-3']); ?>
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
<?php if (isset($__attributesOriginal751c60d6b55ecec60fe692173fafc657)): ?>
<?php $attributes = $__attributesOriginal751c60d6b55ecec60fe692173fafc657; ?>
<?php unset($__attributesOriginal751c60d6b55ecec60fe692173fafc657); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal751c60d6b55ecec60fe692173fafc657)): ?>
<?php $component = $__componentOriginal751c60d6b55ecec60fe692173fafc657; ?>
<?php unset($__componentOriginal751c60d6b55ecec60fe692173fafc657); ?>
<?php endif; ?></div><p x-show="!groups.length" class="text-sm text-muted-foreground text-center py-4"><?php echo e($empty); ?></p><div class="divide-y divide-border"><template x-for="group in groups.slice(0,5)" :key="group.id"><div class="flex items-center justify-between p-4"><div class="min-w-0 flex-1"><?php if (isset($component)) { $__componentOriginal751c60d6b55ecec60fe692173fafc657 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal751c60d6b55ecec60fe692173fafc657 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'capstone::components.feature-link','data' => ['href' => $link,'class' => 'text-sm font-medium hover:text-primary truncate block','xText' => 'group.code || (\'Group \'+group.id)']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('capstone::feature-link'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['href' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($link),'class' => 'text-sm font-medium hover:text-primary truncate block','x-text' => 'group.code || (\'Group \'+group.id)']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>
 <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal751c60d6b55ecec60fe692173fafc657)): ?>
<?php $attributes = $__attributesOriginal751c60d6b55ecec60fe692173fafc657; ?>
<?php unset($__attributesOriginal751c60d6b55ecec60fe692173fafc657); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal751c60d6b55ecec60fe692173fafc657)): ?>
<?php $component = $__componentOriginal751c60d6b55ecec60fe692173fafc657; ?>
<?php unset($__componentOriginal751c60d6b55ecec60fe692173fafc657); ?>
<?php endif; ?><p class="text-xs text-muted-foreground truncate mt-0.5" x-text="group.period ? 'Period: '+group.period.name : group.status"></p></div><?php if (isset($component)) { $__componentOriginal22d7e929995a9d34325929f7d2857b6a = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal22d7e929995a9d34325929f7d2857b6a = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'capstone::components.badge','data' => ['variant' => 'outline','class' => 'ml-3 shrink-0','xText' => 'group.status || \'Unknown\'']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('capstone::badge'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['variant' => 'outline','class' => 'ml-3 shrink-0','x-text' => 'group.status || \'Unknown\'']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal22d7e929995a9d34325929f7d2857b6a)): ?>
<?php $attributes = $__attributesOriginal22d7e929995a9d34325929f7d2857b6a; ?>
<?php unset($__attributesOriginal22d7e929995a9d34325929f7d2857b6a); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal22d7e929995a9d34325929f7d2857b6a)): ?>
<?php $component = $__componentOriginal22d7e929995a9d34325929f7d2857b6a; ?>
<?php unset($__componentOriginal22d7e929995a9d34325929f7d2857b6a); ?>
<?php endif; ?></div></template></div></div>
<?php /**PATH C:\WebsiteTekkom - Copy\Modules/Capstone\resources/views/partials/recent-groups.blade.php ENDPATH**/ ?>