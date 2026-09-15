<?php $__env->startSection('title','Periods'); ?>
<?php $__env->startSection('content'); ?>
<div class="min-h-screen" x-data="capstoneTable({endpoint:'/admin/periods'})"><div class="mx-auto max-w-7xl space-y-6">
    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between"><h1 class="text-xl font-bold text-gray-900">Periode Capstone &amp; TA</h1><?php if (isset($component)) { $__componentOriginalb35a908784863ee4a31335a0ede028f1 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalb35a908784863ee4a31335a0ede028f1 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'capstone::components.button','data' => ['href' => '/admin/periods/new','class' => 'cursor-pointer gap-2']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('capstone::button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['href' => '/admin/periods/new','class' => 'cursor-pointer gap-2']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>
<?php if (isset($component)) { $__componentOriginal0ebc33cc959e9b0fde2e3c32f43abd3a = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal0ebc33cc959e9b0fde2e3c32f43abd3a = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'capstone::components.icon','data' => ['name' => 'Plus']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('capstone::icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'Plus']); ?>
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
<?php endif; ?>Periode Baru <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalb35a908784863ee4a31335a0ede028f1)): ?>
<?php $attributes = $__attributesOriginalb35a908784863ee4a31335a0ede028f1; ?>
<?php unset($__attributesOriginalb35a908784863ee4a31335a0ede028f1); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalb35a908784863ee4a31335a0ede028f1)): ?>
<?php $component = $__componentOriginalb35a908784863ee4a31335a0ede028f1; ?>
<?php unset($__componentOriginalb35a908784863ee4a31335a0ede028f1); ?>
<?php endif; ?></div>
    <?php if (isset($component)) { $__componentOriginal97c37f231f26d0bd39c948179e52e41f = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal97c37f231f26d0bd39c948179e52e41f = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'capstone::components.data-table','data' => ['title' => 'Period Table','searchPlaceholder' => 'Cari periode...','columns' => [0=>'', 'name'=>'Nama Periode','start_date'=>'Durasi',1=>'Konfigurasi Group','is_active'=>'Status',2=>'Action'],'emptyTitle' => 'Tidak ada periode ditemukan','emptyDescription' => 'Coba ubah filter atau buat periode baru','icon' => 'CalendarDays','grouped' => true]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('capstone::data-table'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => 'Period Table','search-placeholder' => 'Cari periode...','columns' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute([0=>'', 'name'=>'Nama Periode','start_date'=>'Durasi',1=>'Konfigurasi Group','is_active'=>'Status',2=>'Action']),'empty-title' => 'Tidak ada periode ditemukan','empty-description' => 'Coba ubah filter atau buat periode baru','icon' => 'CalendarDays','grouped' => true]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

         <?php $__env->slot('filters', null, []); ?> <select x-model="filters.is_active" @change="page=1" class="h-10 rounded-md border px-3 text-sm" aria-label="Status"><option value="all">Semua Status</option><option value="true">Aktif</option><option value="false">Nonaktif</option></select> <?php $__env->endSlot(); ?>
        <template x-for="item in visible" :key="item.id"><tbody><tr class="border-b transition-colors hover:bg-muted/50"><td class="p-2"><?php if (isset($component)) { $__componentOriginalb35a908784863ee4a31335a0ede028f1 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalb35a908784863ee4a31335a0ede028f1 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'capstone::components.button','data' => ['variant' => 'ghost','size' => 'icon','@click' => 'expanded=expanded===item.id ? null : item.id','ariaLabel' => 'Expand period']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('capstone::button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['variant' => 'ghost','size' => 'icon','@click' => 'expanded=expanded===item.id ? null : item.id','aria-label' => 'Expand period']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>
<?php if (isset($component)) { $__componentOriginal0ebc33cc959e9b0fde2e3c32f43abd3a = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal0ebc33cc959e9b0fde2e3c32f43abd3a = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'capstone::components.icon','data' => ['name' => 'ChevronDown']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('capstone::icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'ChevronDown']); ?>
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
<?php endif; ?></td><td class="p-2 font-medium text-gray-900" x-text="item.name"></td><td class="p-2 text-gray-600" x-text="date(item.start_date)+' — '+date(item.end_date)"></td><td class="p-2 text-sm text-gray-600" x-text="item.min_group_size==null && item.max_group_size==null ? 'Belum dikonfigurasi' : `${item.min_group_size}-${item.max_group_size} anggota · ${item.max_supervisor_load || '—'}/dosen`"></td><td class="p-2"><button type="button" @click="toggle(item)" class="inline-flex items-center gap-1 rounded-md border px-2 py-0.5 text-xs font-medium" :class="item.is_active ? 'border-green-600 text-green-800' : 'border-gray-600 text-gray-600'" x-text="item.is_active ? 'Aktif' : 'Nonaktif'"></button></td><td class="p-2"><?php echo $__env->make('capstone::partials.row-actions',['editPath'=>'/admin/periods/{id}/edit'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?></td></tr><tr x-show="expanded===item.id" class="bg-gray-50/30"><td colspan="6"><?php echo $__env->make('capstone::pages.admin.periods.expanded', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?></td></tr></tbody></template>
     <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal97c37f231f26d0bd39c948179e52e41f)): ?>
<?php $attributes = $__attributesOriginal97c37f231f26d0bd39c948179e52e41f; ?>
<?php unset($__attributesOriginal97c37f231f26d0bd39c948179e52e41f); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal97c37f231f26d0bd39c948179e52e41f)): ?>
<?php $component = $__componentOriginal97c37f231f26d0bd39c948179e52e41f; ?>
<?php unset($__componentOriginal97c37f231f26d0bd39c948179e52e41f); ?>
<?php endif; ?>
    <?php echo $__env->make('capstone::partials.delete-dialog',['deleteTitle'=>'Hapus Periode'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
</div></div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('capstone::layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\WebsiteTekkom - Copy\Modules/Capstone\resources/views/pages/admin/periods.blade.php ENDPATH**/ ?>