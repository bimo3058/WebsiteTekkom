<?php $__env->startSection('title','Locations'); ?>
<?php $__env->startSection('content'); ?>
<div class="min-h-screen" x-data="capstoneTable({endpoint:'/locations/all',writeEndpoint:'/locations',defaults:{name:'',type:'offline',capacity:null,description:'',is_active:true}})"><div class="mx-auto max-w-7xl space-y-6">
    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between"><h1 class="text-xl font-bold text-gray-900">Locations</h1><?php if (isset($component)) { $__componentOriginalb35a908784863ee4a31335a0ede028f1 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalb35a908784863ee4a31335a0ede028f1 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'capstone::components.button','data' => ['@click' => 'edit()','class' => 'cursor-pointer gap-2']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('capstone::button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['@click' => 'edit()','class' => 'cursor-pointer gap-2']); ?>
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
<?php endif; ?>Tambah Lokasi <?php echo $__env->renderComponent(); ?>
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
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'capstone::components.data-table','data' => ['title' => 'Location Table','searchPlaceholder' => 'Cari lokasi...','columns' => ['name'=>'Nama','type'=>'Tipe','capacity'=>'Kapasitas','is_active'=>'Status',0=>'Action'],'emptyTitle' => 'Tidak ada lokasi ditemukan','emptyDescription' => 'Coba ubah filter atau buat lokasi baru','icon' => 'MapPin']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('capstone::data-table'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => 'Location Table','search-placeholder' => 'Cari lokasi...','columns' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(['name'=>'Nama','type'=>'Tipe','capacity'=>'Kapasitas','is_active'=>'Status',0=>'Action']),'empty-title' => 'Tidak ada lokasi ditemukan','empty-description' => 'Coba ubah filter atau buat lokasi baru','icon' => 'MapPin']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

         <?php $__env->slot('filters', null, []); ?> <select x-model="filters.type" @change="page=1" class="h-10 rounded-md border px-3 text-sm" aria-label="Tipe"><option value="all">Semua Tipe</option><option value="offline">Offline</option><option value="online">Online</option></select><select x-model="filters.is_active" @change="page=1" class="h-10 rounded-md border px-3 text-sm" aria-label="Status"><option value="all">Semua Status</option><option value="true">Aktif</option><option value="false">Nonaktif</option></select> <?php $__env->endSlot(); ?>
        <template x-for="item in visible" :key="item.id"><tr class="border-b transition-colors hover:bg-muted/50"><td class="p-2 align-middle"><div class="pl-2"><div class="flex items-center gap-2"><?php if (isset($component)) { $__componentOriginal0ebc33cc959e9b0fde2e3c32f43abd3a = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal0ebc33cc959e9b0fde2e3c32f43abd3a = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'capstone::components.icon','data' => ['name' => 'MapPin','class' => 'h-4 w-4',':class' => 'item.is_active ? \'text-blue-600\' : \'text-gray-400\'']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('capstone::icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'MapPin','class' => 'h-4 w-4',':class' => 'item.is_active ? \'text-blue-600\' : \'text-gray-400\'']); ?>
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
<?php endif; ?><span class="font-medium" x-text="item.name"></span></div><p x-show="item.description" class="mt-0.5 ml-6 text-xs text-gray-500" x-text="item.description"></p></div></td><td class="p-2"><?php if (isset($component)) { $__componentOriginal22d7e929995a9d34325929f7d2857b6a = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal22d7e929995a9d34325929f7d2857b6a = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'capstone::components.badge','data' => ['xText' => 'item.type===\'offline\' ? \'Offline\' : \'Online\'']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('capstone::badge'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['x-text' => 'item.type===\'offline\' ? \'Offline\' : \'Online\'']); ?>
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
<?php endif; ?></td><td class="p-2 text-sm text-gray-600" x-text="item.capacity || '—'"></td><td class="p-2"><?php if (isset($component)) { $__componentOriginal22d7e929995a9d34325929f7d2857b6a = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal22d7e929995a9d34325929f7d2857b6a = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'capstone::components.badge','data' => ['variant' => 'outline',':class' => 'item.is_active ? \'border-green-600 text-green-800\' : \'border-gray-600 text-gray-600\'']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('capstone::badge'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['variant' => 'outline',':class' => 'item.is_active ? \'border-green-600 text-green-800\' : \'border-gray-600 text-gray-600\'']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>
<span class="mr-0.5 h-1.5 w-1.5 rounded-full" :class="item.is_active ? 'bg-green-600' : 'bg-gray-400'"></span><span x-text="item.is_active ? 'Aktif' : 'Nonaktif'"></span> <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal22d7e929995a9d34325929f7d2857b6a)): ?>
<?php $attributes = $__attributesOriginal22d7e929995a9d34325929f7d2857b6a; ?>
<?php unset($__attributesOriginal22d7e929995a9d34325929f7d2857b6a); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal22d7e929995a9d34325929f7d2857b6a)): ?>
<?php $component = $__componentOriginal22d7e929995a9d34325929f7d2857b6a; ?>
<?php unset($__componentOriginal22d7e929995a9d34325929f7d2857b6a); ?>
<?php endif; ?></td><td class="p-2"><?php echo $__env->make('capstone::partials.row-actions', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?></td></tr></template>
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
    <?php if (isset($component)) { $__componentOriginal23d0c1a5dcb8d467892a41eeb359fba6 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal23d0c1a5dcb8d467892a41eeb359fba6 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'capstone::components.dialog','data' => ['id' => 'record-form','class' => 'sm:max-w-[500px]']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('capstone::dialog'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['id' => 'record-form','class' => 'sm:max-w-[500px]']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>
<h2 class="text-lg font-semibold" x-text="editing ? 'Edit Location' : 'Add Location'"></h2><p class="mt-2 text-muted-foreground text-sm" x-text="editing ? 'Update the location details below.' : 'Create a new location for scheduling events and sessions.'"></p><form @submit.prevent="save"><div class="space-y-4 py-4"><?php if (isset($component)) { $__componentOriginal32678ef1aa05abec5694cf306077ecae = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal32678ef1aa05abec5694cf306077ecae = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'capstone::components.field','data' => ['name' => 'name','label' => 'Name','placeholder' => 'e.g., Lab IF-101, Zoom Meeting Room','required' => true]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('capstone::field'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'name','label' => 'Name','placeholder' => 'e.g., Lab IF-101, Zoom Meeting Room','required' => true]); ?>
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
<?php endif; ?><div class="grid grid-cols-2 gap-4"><?php if (isset($component)) { $__componentOriginal32678ef1aa05abec5694cf306077ecae = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal32678ef1aa05abec5694cf306077ecae = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'capstone::components.field','data' => ['name' => 'type','label' => 'Type','type' => 'select','options' => ['offline'=>'Offline','online'=>'Online'],'required' => true]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('capstone::field'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'type','label' => 'Type','type' => 'select','options' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(['offline'=>'Offline','online'=>'Online']),'required' => true]); ?>
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
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'capstone::components.field','data' => ['name' => 'capacity','label' => 'Capacity','type' => 'number','min' => '1']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('capstone::field'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'capacity','label' => 'Capacity','type' => 'number','min' => '1']); ?>
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
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'capstone::components.field','data' => ['name' => 'description','label' => 'Description','type' => 'textarea']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('capstone::field'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'description','label' => 'Description','type' => 'textarea']); ?>
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
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'capstone::components.field','data' => ['name' => 'is_active','label' => 'Active','type' => 'checkbox']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('capstone::field'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'is_active','label' => 'Active','type' => 'checkbox']); ?>
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
<?php endif; ?></div><div class="flex justify-end gap-2"><?php if (isset($component)) { $__componentOriginalb35a908784863ee4a31335a0ede028f1 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalb35a908784863ee4a31335a0ede028f1 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'capstone::components.button','data' => ['variant' => 'outline','@click' => '$el.closest(\'dialog\').close()']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('capstone::button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['variant' => 'outline','@click' => '$el.closest(\'dialog\').close()']); ?>
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
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'capstone::components.button','data' => ['type' => 'submit',':disabled' => 'saving','xText' => 'saving ? \'Saving...\' : \'Save\'']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('capstone::button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['type' => 'submit',':disabled' => 'saving','x-text' => 'saving ? \'Saving...\' : \'Save\'']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
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
    <?php echo $__env->make('capstone::partials.delete-dialog',['deleteTitle'=>'Hapus Lokasi','deleteDescription'=>'Lokasi harus dinonaktifkan sebelum dihapus. Apakah Anda yakin ingin menghapus'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
</div></div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('capstone::layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\WebsiteTekkom - Copy\Modules/Capstone\resources/views/pages/admin/locations.blade.php ENDPATH**/ ?>