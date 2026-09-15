<?php $__env->startSection('title','Group Progress'); ?>
<?php $__env->startSection('content'); ?>
<div x-data="adminProgress" class="space-y-6"><div class="flex flex-wrap items-center justify-between gap-4"><h1 class="text-3xl font-bold">Group Progress</h1><?php if (isset($component)) { $__componentOriginalb35a908784863ee4a31335a0ede028f1 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalb35a908784863ee4a31335a0ede028f1 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'capstone::components.button','data' => ['variant' => 'outline','size' => 'sm','@click' => 'exportCsv()',':disabled' => 'loading || saving']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('capstone::button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['variant' => 'outline','size' => 'sm','@click' => 'exportCsv()',':disabled' => 'loading || saving']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>
Export CSV <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalb35a908784863ee4a31335a0ede028f1)): ?>
<?php $attributes = $__attributesOriginalb35a908784863ee4a31335a0ede028f1; ?>
<?php unset($__attributesOriginalb35a908784863ee4a31335a0ede028f1); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalb35a908784863ee4a31335a0ede028f1)): ?>
<?php $component = $__componentOriginalb35a908784863ee4a31335a0ede028f1; ?>
<?php unset($__componentOriginalb35a908784863ee4a31335a0ede028f1); ?>
<?php endif; ?></div><?php echo $__env->make('capstone::pages.admin.shared.toolbar', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
<?php echo $__env->make('capstone::partials.loading', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
<div x-show="!loading && !error" x-cloak class="space-y-4"><div class="rounded-xl border bg-card"><div class="overflow-x-auto"><table class="w-full min-w-[800px] text-left text-sm"><thead class="border-b bg-muted/40"><tr><th class="p-4">Kelompok / Judul</th><th class="p-4">Periode</th><th class="p-4">Anggota</th><th class="p-4">Status</th><th class="p-4">Progress</th><th class="p-4">Fase</th><th class="p-4">Aksi</th></tr></thead><tbody><template x-for="item in visible" :key="item.id"><tr class="border-b"><td class="p-4"><strong x-text="groupName(item)"></strong><p class="mt-1 text-muted-foreground" x-text="item.title?.title"></p></td><td class="p-4" x-text="item.period?.name"></td><td class="p-4" x-text="item.members_count"></td><td class="p-4" x-text="item.status"></td><td class="p-4 min-w-36"><div class="mb-2" x-text="percent(item)+'%'"></div><progress max="100" :value="percent(item)" class="w-full h-2 accent-primary"></progress></td><td class="p-4"><div class="flex flex-wrap gap-2"><template x-for="phase in item.progress?.phases || []" :key="phase.phase"><span :title="phase.status" class="rounded border px-2 py-1 text-xs" :class="phase.status==='completed'?'border-emerald-200 bg-emerald-50 text-emerald-700':phase.status==='locked'?'text-muted-foreground':'bg-blue-50 text-blue-700'" x-text="phase.phase"></span></template></div></td><td class="p-4"><a :href="url('/admin/groups/'+item.id)" class="text-primary underline">Detail</a></td></tr></template></tbody></table></div><p x-show="!filtered.length" class="p-8 text-center text-muted-foreground">Belum ada kelompok.</p><?php echo $__env->make('capstone::pages.admin.shared.pagination', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?></div></div></div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('capstone::layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\WebsiteTekkom - Copy\Modules/Capstone\resources/views/pages/admin/analytics/progress.blade.php ENDPATH**/ ?>