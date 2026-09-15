<?php $__env->startSection('title','Groups'); ?>
<?php $__env->startSection('content'); ?>
<div x-data="adminGroups(false)" class="space-y-6"><div class="flex flex-wrap items-center justify-between gap-4"><h1 class="text-3xl font-bold tracking-tight">Groups</h1><div class="flex gap-2"></div></div>
<?php echo $__env->make('capstone::pages.admin.shared.toolbar', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
<?php echo $__env->make('capstone::partials.loading', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
<label class="text-sm">Status <select x-model="status" @change="page=1;load()" class="rounded border p-2"><option value="">Semua status</option><template x-for="s in ['FORMING','READY_FOR_BIDDING','TITLE_APPROVED','READY_FOR_FINALIZATION','KELOMPOK_FINAL','PDC1_ACTIVE','READY_FOR_SEMPRO','SEMPRO_DONE','PDC2_ACTIVE','EXPO_REGISTERED','READY_FOR_TA_INDIVIDUAL','CLOSED','DISSOLVED']"><option x-text="s"></option></template></select></label><div x-show="!loading && !error" x-cloak class="rounded-xl border bg-card overflow-hidden"><div class="overflow-x-auto"><table class="w-full text-left text-sm"><thead class="border-b bg-muted/40"><tr><th class="p-4">Kelompok</th><th class="p-4">Judul</th><th class="p-4">Periode</th><th class="p-4">Anggota</th><th class="p-4">Status</th><th class="p-4">Aksi</th></tr></thead><tbody><template x-for="item in items" :key="item.id"><tr class="border-b"><td class="p-4 " x-text="groupName(item)"></td><td class="p-4 " x-text="item.title?.title || item.name || '-'"></td><td class="p-4 " x-text="item.period?.name"></td><td class="p-4 " x-text="(item.members||[]).map(m=>person(m.student)).join(', ')"></td><td class="p-4 " x-text="item.status_label || item.status"></td><td class="p-4"><a :href="url('/admin/groups/'+item.id)" class="text-primary underline">Detail</a></td></tr></template></tbody></table></div><p x-show="!items.length" class="p-8 text-center">Tidak ada kelompok.</p><div class="flex items-center justify-between gap-3 p-4"><span x-text="(pagination.total||0)+' kelompok'"></span><div class="flex items-center gap-3"><select x-model.number="pageSize" @change="page=1;load()" class="rounded border p-1"><option>10</option><option>25</option><option>50</option></select><?php if (isset($component)) { $__componentOriginalb35a908784863ee4a31335a0ede028f1 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalb35a908784863ee4a31335a0ede028f1 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'capstone::components.button','data' => ['variant' => 'outline','size' => 'sm','@click' => 'page--;load()',':disabled' => 'loading || page<=1']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('capstone::button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['variant' => 'outline','size' => 'sm','@click' => 'page--;load()',':disabled' => 'loading || page<=1']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>
Sebelumnya <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalb35a908784863ee4a31335a0ede028f1)): ?>
<?php $attributes = $__attributesOriginalb35a908784863ee4a31335a0ede028f1; ?>
<?php unset($__attributesOriginalb35a908784863ee4a31335a0ede028f1); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalb35a908784863ee4a31335a0ede028f1)): ?>
<?php $component = $__componentOriginalb35a908784863ee4a31335a0ede028f1; ?>
<?php unset($__componentOriginalb35a908784863ee4a31335a0ede028f1); ?>
<?php endif; ?><span x-text="page+' / '+(pagination.last_page||1)"></span><?php if (isset($component)) { $__componentOriginalb35a908784863ee4a31335a0ede028f1 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalb35a908784863ee4a31335a0ede028f1 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'capstone::components.button','data' => ['variant' => 'outline','size' => 'sm','@click' => 'page++;load()',':disabled' => 'loading || page>=(pagination.last_page||1)']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('capstone::button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['variant' => 'outline','size' => 'sm','@click' => 'page++;load()',':disabled' => 'loading || page>=(pagination.last_page||1)']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>
Berikutnya <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalb35a908784863ee4a31335a0ede028f1)): ?>
<?php $attributes = $__attributesOriginalb35a908784863ee4a31335a0ede028f1; ?>
<?php unset($__attributesOriginalb35a908784863ee4a31335a0ede028f1); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalb35a908784863ee4a31335a0ede028f1)): ?>
<?php $component = $__componentOriginalb35a908784863ee4a31335a0ede028f1; ?>
<?php unset($__componentOriginalb35a908784863ee4a31335a0ede028f1); ?>
<?php endif; ?></div></div></div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('capstone::layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\WebsiteTekkom - Copy\Modules\Capstone\tests/../resources/views/pages\admin\groups.blade.php ENDPATH**/ ?>