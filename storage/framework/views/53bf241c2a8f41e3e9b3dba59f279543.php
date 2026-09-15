<?php $__env->startSection('title','Notifications'); ?>
<?php $__env->startSection('content'); ?>
<div x-data="capstoneNotifications" class="space-y-6">
    <?php echo $__env->make('capstone::partials.loading', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    <div x-show="!loading && !error" x-cloak class="space-y-6">
        <div class="flex items-start justify-between gap-3"><div><h1 class="text-3xl font-bold tracking-tight">Notifications</h1><p class="text-muted-foreground" x-text="total+' notifications · '+unreadCount+' unread'"></p></div><?php if (isset($component)) { $__componentOriginalb35a908784863ee4a31335a0ede028f1 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalb35a908784863ee4a31335a0ede028f1 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'capstone::components.button','data' => ['variant' => 'outline','size' => 'sm','xShow' => 'unreadCount>0','@click' => 'readAll',':disabled' => 'busy']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('capstone::button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['variant' => 'outline','size' => 'sm','x-show' => 'unreadCount>0','@click' => 'readAll',':disabled' => 'busy']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>
<?php if (isset($component)) { $__componentOriginal0ebc33cc959e9b0fde2e3c32f43abd3a = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal0ebc33cc959e9b0fde2e3c32f43abd3a = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'capstone::components.icon','data' => ['name' => 'CheckCheck','class' => 'mr-2 h-4 w-4']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('capstone::icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'CheckCheck','class' => 'mr-2 h-4 w-4']); ?>
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
<?php endif; ?>Mark All Read <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalb35a908784863ee4a31335a0ede028f1)): ?>
<?php $attributes = $__attributesOriginalb35a908784863ee4a31335a0ede028f1; ?>
<?php unset($__attributesOriginalb35a908784863ee4a31335a0ede028f1); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalb35a908784863ee4a31335a0ede028f1)): ?>
<?php $component = $__componentOriginalb35a908784863ee4a31335a0ede028f1; ?>
<?php unset($__componentOriginalb35a908784863ee4a31335a0ede028f1); ?>
<?php endif; ?></div>
        <div x-show="!items.length" class="rounded-lg border border-dashed py-12 text-center"><?php if (isset($component)) { $__componentOriginal0ebc33cc959e9b0fde2e3c32f43abd3a = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal0ebc33cc959e9b0fde2e3c32f43abd3a = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'capstone::components.icon','data' => ['name' => 'Bell','class' => 'text-muted-foreground mx-auto mb-4 h-12 w-12 opacity-50']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('capstone::icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'Bell','class' => 'text-muted-foreground mx-auto mb-4 h-12 w-12 opacity-50']); ?>
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
<?php endif; ?><h2 class="mb-2 text-xl font-bold">No Notifications</h2><p class="text-muted-foreground">You're all caught up!</p></div>
        <div class="space-y-2"><template x-for="n in items" :key="n.id"><article tabindex="0" :aria-label="n.title" @click="open(n)" @keydown.enter.self.prevent="open(n)" class="rounded-xl border bg-card text-card-foreground cursor-pointer transition-all duration-200" :class="!n.is_read?'border-primary/20 bg-primary/5 shadow-sm':'hover:border-primary/20 hover:bg-muted/30 shadow-none'">
            <div class="flex items-start gap-4 p-5"><div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full" :class="palette(n.type)"><?php if (isset($component)) { $__componentOriginal0ebc33cc959e9b0fde2e3c32f43abd3a = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal0ebc33cc959e9b0fde2e3c32f43abd3a = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'capstone::components.icon','data' => ['name' => 'MailOpen','class' => 'h-4 w-4','xShow' => 'n.is_read']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('capstone::icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'MailOpen','class' => 'h-4 w-4','x-show' => 'n.is_read']); ?>
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
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'capstone::components.icon','data' => ['name' => 'Mail','class' => 'h-4 w-4','xShow' => '!n.is_read']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('capstone::icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'Mail','class' => 'h-4 w-4','x-show' => '!n.is_read']); ?>
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
<?php endif; ?></div><div class="min-w-0 flex-1">
                <div class="flex items-center gap-2"><span class="text-sm font-medium" :class="!n.is_read?'text-primary':''" x-text="n.title"></span><?php if (isset($component)) { $__componentOriginal22d7e929995a9d34325929f7d2857b6a = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal22d7e929995a9d34325929f7d2857b6a = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'capstone::components.badge','data' => ['xShow' => '!n.is_read','class' => 'px-1.5 py-0 text-xs']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('capstone::badge'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['x-show' => '!n.is_read','class' => 'px-1.5 py-0 text-xs']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>
New <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal22d7e929995a9d34325929f7d2857b6a)): ?>
<?php $attributes = $__attributesOriginal22d7e929995a9d34325929f7d2857b6a; ?>
<?php unset($__attributesOriginal22d7e929995a9d34325929f7d2857b6a); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal22d7e929995a9d34325929f7d2857b6a)): ?>
<?php $component = $__componentOriginal22d7e929995a9d34325929f7d2857b6a; ?>
<?php unset($__componentOriginal22d7e929995a9d34325929f7d2857b6a); ?>
<?php endif; ?></div><p class="text-muted-foreground mt-0.5 line-clamp-2 text-sm" x-text="n.message"></p>
                <div x-show="n.type==='GROUP_INVITATION' && n.related_id" class="mt-4 flex gap-2"><template x-if="n.invitation_status==='PENDING' && !invitationActions[n.id]"><div class="flex gap-2"><?php if (isset($component)) { $__componentOriginalb35a908784863ee4a31335a0ede028f1 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalb35a908784863ee4a31335a0ede028f1 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'capstone::components.button','data' => ['size' => 'sm','@click.stop' => 'invitation(n,\'accept\')',':disabled' => 'busy']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('capstone::button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['size' => 'sm','@click.stop' => 'invitation(n,\'accept\')',':disabled' => 'busy']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>
Accept <?php echo $__env->renderComponent(); ?>
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
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'capstone::components.button','data' => ['size' => 'sm','variant' => 'outline','@click.stop' => 'invitation(n,\'reject\')',':disabled' => 'busy']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('capstone::button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['size' => 'sm','variant' => 'outline','@click.stop' => 'invitation(n,\'reject\')',':disabled' => 'busy']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>
Reject <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalb35a908784863ee4a31335a0ede028f1)): ?>
<?php $attributes = $__attributesOriginalb35a908784863ee4a31335a0ede028f1; ?>
<?php unset($__attributesOriginalb35a908784863ee4a31335a0ede028f1); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalb35a908784863ee4a31335a0ede028f1)): ?>
<?php $component = $__componentOriginalb35a908784863ee4a31335a0ede028f1; ?>
<?php unset($__componentOriginalb35a908784863ee4a31335a0ede028f1); ?>
<?php endif; ?></div></template><span x-show="n.invitation_status==='ACCEPTED'" class="text-muted-foreground flex items-center gap-2 text-sm font-medium"><?php if (isset($component)) { $__componentOriginal0ebc33cc959e9b0fde2e3c32f43abd3a = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal0ebc33cc959e9b0fde2e3c32f43abd3a = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'capstone::components.icon','data' => ['name' => 'CheckCheck','class' => 'h-4 w-4 text-green-500']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('capstone::icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'CheckCheck','class' => 'h-4 w-4 text-green-500']); ?>
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
<?php endif; ?>You accepted this invitation</span><span x-show="n.invitation_status==='REJECTED'" class="text-muted-foreground flex items-center gap-2 text-sm font-medium"><?php if (isset($component)) { $__componentOriginal0ebc33cc959e9b0fde2e3c32f43abd3a = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal0ebc33cc959e9b0fde2e3c32f43abd3a = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'capstone::components.icon','data' => ['name' => 'XCircle','class' => 'h-4 w-4 text-red-500']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('capstone::icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'XCircle','class' => 'h-4 w-4 text-red-500']); ?>
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
<?php endif; ?>You rejected this invitation</span></div>
                <time class="block text-muted-foreground mt-2 text-xs whitespace-nowrap" :datetime="n.created_at" x-text="relative(n.created_at)"></time>
            </div></div>
        </article></template></div>
        <div x-show="lastPage>1" class="flex justify-center gap-2"><?php if (isset($component)) { $__componentOriginalb35a908784863ee4a31335a0ede028f1 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalb35a908784863ee4a31335a0ede028f1 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'capstone::components.button','data' => ['variant' => 'outline','size' => 'sm',':disabled' => 'page<=1','@click' => 'load(page-1)']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('capstone::button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['variant' => 'outline','size' => 'sm',':disabled' => 'page<=1','@click' => 'load(page-1)']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>
Previous <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalb35a908784863ee4a31335a0ede028f1)): ?>
<?php $attributes = $__attributesOriginalb35a908784863ee4a31335a0ede028f1; ?>
<?php unset($__attributesOriginalb35a908784863ee4a31335a0ede028f1); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalb35a908784863ee4a31335a0ede028f1)): ?>
<?php $component = $__componentOriginalb35a908784863ee4a31335a0ede028f1; ?>
<?php unset($__componentOriginalb35a908784863ee4a31335a0ede028f1); ?>
<?php endif; ?><span class="text-muted-foreground flex items-center px-3 text-sm" x-text="'Page '+page+' of '+lastPage"></span><?php if (isset($component)) { $__componentOriginalb35a908784863ee4a31335a0ede028f1 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalb35a908784863ee4a31335a0ede028f1 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'capstone::components.button','data' => ['variant' => 'outline','size' => 'sm',':disabled' => 'page>=lastPage','@click' => 'load(page+1)']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('capstone::button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['variant' => 'outline','size' => 'sm',':disabled' => 'page>=lastPage','@click' => 'load(page+1)']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>
Next <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalb35a908784863ee4a31335a0ede028f1)): ?>
<?php $attributes = $__attributesOriginalb35a908784863ee4a31335a0ede028f1; ?>
<?php unset($__attributesOriginalb35a908784863ee4a31335a0ede028f1); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalb35a908784863ee4a31335a0ede028f1)): ?>
<?php $component = $__componentOriginalb35a908784863ee4a31335a0ede028f1; ?>
<?php unset($__componentOriginalb35a908784863ee4a31335a0ede028f1); ?>
<?php endif; ?></div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('capstone::layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\WebsiteTekkom - Copy\Modules\Capstone\resources\views\pages\notifications.blade.php ENDPATH**/ ?>