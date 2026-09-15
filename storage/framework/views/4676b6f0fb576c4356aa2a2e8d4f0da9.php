<div class="space-y-6">
    <div class="flex items-center justify-between gap-3 rounded-lg border p-4" :class="hasTemplates ? 'border-green-200 bg-green-50' : 'border-amber-200 bg-amber-50'"><div><h2 class="font-medium" :class="hasTemplates ? 'text-green-900' : 'text-amber-900'" x-text="hasTemplates ? 'Setup Evaluasi Siap' : 'Setup Evaluasi Diperlukan'"></h2><p class="text-sm" x-text="hasTemplates ? templates.filter(t=>t.is_active).length+' template tersedia' : 'Konfigurasi template evaluasi harus diselesaikan sebelum membuat periode.'"></p></div><?php if (isset($component)) { $__componentOriginalb35a908784863ee4a31335a0ede028f1 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalb35a908784863ee4a31335a0ede028f1 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'capstone::components.button','data' => ['href' => '/admin/assessment-bank','target' => '_blank','rel' => 'noopener noreferrer','variant' => 'outline','size' => 'sm']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('capstone::button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['href' => '/admin/assessment-bank','target' => '_blank','rel' => 'noopener noreferrer','variant' => 'outline','size' => 'sm']); ?>
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
<?php endif; ?>Tambah Template <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalb35a908784863ee4a31335a0ede028f1)): ?>
<?php $attributes = $__attributesOriginalb35a908784863ee4a31335a0ede028f1; ?>
<?php unset($__attributesOriginalb35a908784863ee4a31335a0ede028f1); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalb35a908784863ee4a31335a0ede028f1)): ?>
<?php $component = $__componentOriginalb35a908784863ee4a31335a0ede028f1; ?>
<?php unset($__componentOriginalb35a908784863ee4a31335a0ede028f1); ?>
<?php endif; ?></div>
    <div class="flex flex-wrap items-center justify-between gap-3 border-b pb-4"><span class="flex items-center gap-2 text-sm font-medium text-gray-700"><?php if (isset($component)) { $__componentOriginal0ebc33cc959e9b0fde2e3c32f43abd3a = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal0ebc33cc959e9b0fde2e3c32f43abd3a = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'capstone::components.icon','data' => ['name' => 'Copy','class' => 'h-4 w-4 text-gray-500']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('capstone::icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'Copy','class' => 'h-4 w-4 text-gray-500']); ?>
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
<?php endif; ?>Salin Penilaian dari Periode:</span><select x-model="copyPeriod" @change="copy()" class="h-9 rounded-md border px-3 text-sm" aria-label="Salin penilaian dari periode"><option value="">Pilih periode</option><template x-for="period in periods.filter(p=>String(p.id)!==String(id))" :key="period.id"><option :value="period.id" x-text="period.name"></option></template></select></div>
    <div class="inline-flex gap-1 rounded-lg bg-muted p-1" role="tablist"><button type="button" role="tab" :aria-selected="tab==='assessment'" @click="tab='assessment';search=''" class="rounded-md px-4 py-2 text-sm" :class="tab==='assessment' && 'bg-white shadow-sm'">Tipe Penilaian</button><button type="button" role="tab" :aria-selected="tab==='peer'" @click="tab='peer';search=''" class="rounded-md px-4 py-2 text-sm" :class="tab==='peer' && 'bg-white shadow-sm'">Peer Review</button></div>
    <div class="grid grid-cols-1 gap-6 lg:grid-cols-[1fr_300px]"><div class="min-w-0 space-y-4"><div x-show="tab==='assessment'" class="flex flex-wrap gap-2"><template x-for="type in types" :key="type"><button type="button" @click="evaluationType=type;search=''" class="rounded-md border px-3 py-2 text-xs font-medium" :class="evaluationType===type ? 'bg-primary text-primary-foreground' : 'hover:bg-muted'" x-text="type.replaceAll('_',' ')"></button></template></div><div class="relative"><?php if (isset($component)) { $__componentOriginal0ebc33cc959e9b0fde2e3c32f43abd3a = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal0ebc33cc959e9b0fde2e3c32f43abd3a = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'capstone::components.icon','data' => ['name' => 'Search','class' => 'absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-gray-400']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('capstone::icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'Search','class' => 'absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-gray-400']); ?>
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
<?php endif; ?><?php if (isset($component)) { $__componentOriginal2f30e5e2854777f60031a186edf35415 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal2f30e5e2854777f60031a186edf35415 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'capstone::components.input','data' => ['xModel' => 'search','placeholder' => 'Cari komponen...','class' => 'pl-9','ariaLabel' => 'Cari komponen']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('capstone::input'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['x-model' => 'search','placeholder' => 'Cari komponen...','class' => 'pl-9','aria-label' => 'Cari komponen']); ?>
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
<?php endif; ?></div><div class="overflow-x-auto rounded-lg border"><table class="w-full"><thead class="bg-gray-50"><tr><th class="w-12 px-4 py-3"><input type="checkbox" @change="toggleAll($event.target.checked)" :checked="choices.length>0 && choices.every(t=>selectedIds.includes(t.id))" aria-label="Pilih semua komponen" /></th><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = ['Kode','Nama','Deskripsi','Bobot']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?><th class="px-4 py-3 text-left text-xs font-semibold text-gray-700"><?php echo e($label); ?></th><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?></tr></thead><tbody class="divide-y divide-gray-100"><tr x-show="!choices.length"><td colspan="5" class="px-4 py-8 text-center text-gray-500">Tidak ada komponen yang tersedia untuk fase ini.</td></tr><template x-for="item in choices" :key="item.id"><tr class="hover:bg-gray-50"><td class="px-4 py-3"><input type="checkbox" :checked="selectedIds.includes(item.id)" @change="toggle(item.id)" :aria-label="item.name" /></td><td class="px-4 py-3 text-sm font-medium text-blue-600" x-text="item.code || '-'"></td><td class="px-4 py-3 text-sm font-medium text-gray-900" x-text="item.name"></td><td class="px-4 py-3 text-sm text-gray-700" x-text="item.description || '-'"></td><td class="px-4 py-3 text-center"><?php if (isset($component)) { $__componentOriginal22d7e929995a9d34325929f7d2857b6a = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal22d7e929995a9d34325929f7d2857b6a = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'capstone::components.badge','data' => ['variant' => 'outline','xText' => 'item.weight+\'%\'']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('capstone::badge'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['variant' => 'outline','x-text' => 'item.weight+\'%\'']); ?>
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
<?php endif; ?></td></tr></template></tbody></table></div></div><aside class="space-y-4"><div class="rounded-xl border p-5"><h3 class="font-semibold">Ringkasan Penilaian</h3><p class="mt-3 text-sm text-muted-foreground"><span x-text="selectedIds.length"></span> komponen dipilih</p><p class="mt-2 text-2xl font-semibold" :class="totalWeight===100 ? 'text-green-600' : 'text-amber-600'" x-text="totalWeight+'%'"></p><p class="mt-1 text-xs text-muted-foreground">Total bobot komponen yang dipilih harus 100%.</p></div><div class="rounded-xl border p-5"><h3 class="mb-3 font-semibold">Ringkasan Semua Fase</h3><template x-for="type in types" :key="type"><div class="flex justify-between gap-2 py-1 text-xs"><span x-text="type.replaceAll('_',' ')"></span><span x-text="weight(assessments[type])+'%'"></span></div></template><div class="flex justify-between py-1 text-xs"><span>Peer Review</span><span x-text="weight(peerIds,true)+'%'"></span></div></div></aside></div>
</div>
<?php /**PATH C:\WebsiteTekkom - Copy\Modules/Capstone\resources/views/pages/admin/periods/steps/evaluation.blade.php ENDPATH**/ ?>