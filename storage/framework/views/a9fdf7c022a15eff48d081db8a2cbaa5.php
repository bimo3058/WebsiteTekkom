<?php $__env->startSection('title','Finalization'); ?>
<?php $__env->startSection('content'); ?>
<div x-data="adminFinalization" class="space-y-6"><div class="flex flex-wrap items-center justify-between gap-4"><h1 class="text-3xl font-bold tracking-tight">Finalization</h1><div class="flex gap-2"><?php if (isset($component)) { $__componentOriginalb35a908784863ee4a31335a0ede028f1 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalb35a908784863ee4a31335a0ede028f1 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'capstone::components.button','data' => ['variant' => 'outline','size' => 'sm','@click' => 'confirm(\'lock\')',':disabled' => 'saving || !periodId || locked || period?.is_finalized']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('capstone::button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['variant' => 'outline','size' => 'sm','@click' => 'confirm(\'lock\')',':disabled' => 'saving || !periodId || locked || period?.is_finalized']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>
Kunci Bidding <?php echo $__env->renderComponent(); ?>
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
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'capstone::components.button','data' => ['variant' => 'outline','size' => 'sm','@click' => 'confirm(\'finalize-period\')',':disabled' => 'saving || !period?.is_active || period?.is_finalized']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('capstone::button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['variant' => 'outline','size' => 'sm','@click' => 'confirm(\'finalize-period\')',':disabled' => 'saving || !period?.is_active || period?.is_finalized']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>
Finalisasi Periode <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalb35a908784863ee4a31335a0ede028f1)): ?>
<?php $attributes = $__attributesOriginalb35a908784863ee4a31335a0ede028f1; ?>
<?php unset($__attributesOriginalb35a908784863ee4a31335a0ede028f1); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalb35a908784863ee4a31335a0ede028f1)): ?>
<?php $component = $__componentOriginalb35a908784863ee4a31335a0ede028f1; ?>
<?php unset($__componentOriginalb35a908784863ee4a31335a0ede028f1); ?>
<?php endif; ?></div></div>
<?php echo $__env->make('capstone::pages.admin.shared.toolbar', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
<?php echo $__env->make('capstone::partials.loading', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
<div x-show="periodId && !loading && !error" x-cloak class="space-y-6"><div class="grid gap-4 md:grid-cols-3"><?php if (isset($component)) { $__componentOriginal5ac34a7ad891af7fa808b2dbb0e60582 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal5ac34a7ad891af7fa808b2dbb0e60582 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'capstone::components.card','data' => ['title' => 'Kelompok']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('capstone::card'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => 'Kelompok']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>
<p class="px-6 text-xl font-semibold" x-text="groups.length"></p> <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal5ac34a7ad891af7fa808b2dbb0e60582)): ?>
<?php $attributes = $__attributesOriginal5ac34a7ad891af7fa808b2dbb0e60582; ?>
<?php unset($__attributesOriginal5ac34a7ad891af7fa808b2dbb0e60582); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal5ac34a7ad891af7fa808b2dbb0e60582)): ?>
<?php $component = $__componentOriginal5ac34a7ad891af7fa808b2dbb0e60582; ?>
<?php unset($__componentOriginal5ac34a7ad891af7fa808b2dbb0e60582); ?>
<?php endif; ?><?php if (isset($component)) { $__componentOriginal5ac34a7ad891af7fa808b2dbb0e60582 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal5ac34a7ad891af7fa808b2dbb0e60582 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'capstone::components.card','data' => ['title' => 'Judul siap alokasi']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('capstone::card'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => 'Judul siap alokasi']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>
<p class="px-6 text-xl font-semibold" x-text="titles.length"></p> <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal5ac34a7ad891af7fa808b2dbb0e60582)): ?>
<?php $attributes = $__attributesOriginal5ac34a7ad891af7fa808b2dbb0e60582; ?>
<?php unset($__attributesOriginal5ac34a7ad891af7fa808b2dbb0e60582); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal5ac34a7ad891af7fa808b2dbb0e60582)): ?>
<?php $component = $__componentOriginal5ac34a7ad891af7fa808b2dbb0e60582; ?>
<?php unset($__componentOriginal5ac34a7ad891af7fa808b2dbb0e60582); ?>
<?php endif; ?><?php if (isset($component)) { $__componentOriginal5ac34a7ad891af7fa808b2dbb0e60582 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal5ac34a7ad891af7fa808b2dbb0e60582 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'capstone::components.card','data' => ['title' => 'Status periode']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('capstone::card'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => 'Status periode']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>
<p class="px-6 text-xl font-semibold" x-text="period?.is_finalized?'Finalized':locked?'Bidding locked':'Bidding open'"></p> <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal5ac34a7ad891af7fa808b2dbb0e60582)): ?>
<?php $attributes = $__attributesOriginal5ac34a7ad891af7fa808b2dbb0e60582; ?>
<?php unset($__attributesOriginal5ac34a7ad891af7fa808b2dbb0e60582); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal5ac34a7ad891af7fa808b2dbb0e60582)): ?>
<?php $component = $__componentOriginal5ac34a7ad891af7fa808b2dbb0e60582; ?>
<?php unset($__componentOriginal5ac34a7ad891af7fa808b2dbb0e60582); ?>
<?php endif; ?></div><div x-show="!loading && !error" x-cloak class="overflow-hidden rounded-xl border bg-card"><div class="overflow-x-auto"><table class="w-full min-w-[640px] text-left text-sm"><thead class="border-b bg-muted/40"><tr><th class="p-4">Judul</th><th class="p-4">Dosen</th><th class="p-4">Kuota Tersisa</th><th class="p-4">Alokasi</th></tr></thead><tbody><template x-for="item in visible" :key="item.id"><tr class="border-b hover:bg-muted/30"><td class="p-4 font-medium" x-text="item.title"></td><td class="p-4 " x-text="person(item.lecturer)"></td><td class="p-4 " x-text="item.remaining_quota"></td><td class="p-4"><template x-for="bid in item.bids || []" :key="bid.id"><div class="mb-2 flex items-center justify-between gap-3"><span x-text="groupName(bid.group)"></span><?php if (isset($component)) { $__componentOriginalb35a908784863ee4a31335a0ede028f1 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalb35a908784863ee4a31335a0ede028f1 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'capstone::components.button','data' => ['variant' => 'outline','size' => 'sm','@click' => 'allocate(item,bid)',':disabled' => 'saving || period?.is_finalized || item.remaining_quota<=0']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('capstone::button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['variant' => 'outline','size' => 'sm','@click' => 'allocate(item,bid)',':disabled' => 'saving || period?.is_finalized || item.remaining_quota<=0']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>
Alokasikan <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalb35a908784863ee4a31335a0ede028f1)): ?>
<?php $attributes = $__attributesOriginalb35a908784863ee4a31335a0ede028f1; ?>
<?php unset($__attributesOriginalb35a908784863ee4a31335a0ede028f1); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalb35a908784863ee4a31335a0ede028f1)): ?>
<?php $component = $__componentOriginalb35a908784863ee4a31335a0ede028f1; ?>
<?php unset($__componentOriginalb35a908784863ee4a31335a0ede028f1); ?>
<?php endif; ?></div></template><div x-show="item.title_source==='STUDENT'"><?php if (isset($component)) { $__componentOriginalb35a908784863ee4a31335a0ede028f1 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalb35a908784863ee4a31335a0ede028f1 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'capstone::components.button','data' => ['variant' => 'outline','size' => 'sm','@click' => 'allocate(item)',':disabled' => 'saving || period?.is_finalized || item.remaining_quota<=0']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('capstone::button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['variant' => 'outline','size' => 'sm','@click' => 'allocate(item)',':disabled' => 'saving || period?.is_finalized || item.remaining_quota<=0']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>
Alokasi usulan mahasiswa <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalb35a908784863ee4a31335a0ede028f1)): ?>
<?php $attributes = $__attributesOriginalb35a908784863ee4a31335a0ede028f1; ?>
<?php unset($__attributesOriginalb35a908784863ee4a31335a0ede028f1); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalb35a908784863ee4a31335a0ede028f1)): ?>
<?php $component = $__componentOriginalb35a908784863ee4a31335a0ede028f1; ?>
<?php unset($__componentOriginalb35a908784863ee4a31335a0ede028f1); ?>
<?php endif; ?></div></td></tr></template></tbody></table></div><p x-show="!filtered.length" class="p-8 text-center text-muted-foreground">Tidak ada data.</p><?php echo $__env->make('capstone::pages.admin.shared.pagination', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?></div><?php if (isset($component)) { $__componentOriginal5ac34a7ad891af7fa808b2dbb0e60582 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal5ac34a7ad891af7fa808b2dbb0e60582 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'capstone::components.card','data' => ['title' => 'Beban Pembimbing']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('capstone::card'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => 'Beban Pembimbing']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>
<div class="px-6"><template x-for="(lecturer,index) in loads" :key="index"><div class="flex justify-between border-b py-3 text-sm"><span x-text="person(lecturer.lecturer)"></span><span x-text="(lecturer.current_load ?? lecturer.total_load ?? lecturer.load ?? 0)+' / '+(lecturer.max_load ?? period?.max_supervise_load ?? '-')"></span></div></template></div> <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal5ac34a7ad891af7fa808b2dbb0e60582)): ?>
<?php $attributes = $__attributesOriginal5ac34a7ad891af7fa808b2dbb0e60582; ?>
<?php unset($__attributesOriginal5ac34a7ad891af7fa808b2dbb0e60582); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal5ac34a7ad891af7fa808b2dbb0e60582)): ?>
<?php $component = $__componentOriginal5ac34a7ad891af7fa808b2dbb0e60582; ?>
<?php unset($__componentOriginal5ac34a7ad891af7fa808b2dbb0e60582); ?>
<?php endif; ?></div><?php if (isset($component)) { $__componentOriginal23d0c1a5dcb8d467892a41eeb359fba6 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal23d0c1a5dcb8d467892a41eeb359fba6 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'capstone::components.dialog','data' => ['id' => 'allocate-form','title' => 'Alokasi Kelompok']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('capstone::dialog'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['id' => 'allocate-form','title' => 'Alokasi Kelompok']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>
<form @submit.prevent="save()" class="mt-5 space-y-4"><label class="block space-y-2 text-sm"><span>Kelompok</span><select x-model="form.group_id" class="w-full rounded border bg-background p-2" :disabled="action==='allocate'" required><option value="">Pilih...</option><template x-for="option in groups" :key="option.id"><option :value="option.id" x-text="groupName(option)"></option></template></select></label><label class="block space-y-2 text-sm"><span>Pembimbing 1</span><select x-model="form.supervisor_1_id" class="w-full rounded border bg-background p-2"  required><option value="">Pilih...</option><template x-for="option in lecturers" :key="option.id"><option :value="option.lecturer_id || option.lecturer?.id || option.id" x-text="person(option)"></option></template></select></label><label class="block space-y-2 text-sm"><span>Pembimbing 2</span><select x-model="form.supervisor_2_id" class="w-full rounded border bg-background p-2" ><option value="">Pilih...</option><template x-for="option in lecturers" :key="option.id"><option :value="option.lecturer_id || option.lecturer?.id || option.id" x-text="person(option)"></option></template></select></label><template x-for="(messages,key) in errors" :key="key"><p class="text-sm text-destructive" x-text="Array.isArray(messages)?messages.join(' '):messages"></p></template><div class="flex justify-end gap-3"><?php if (isset($component)) { $__componentOriginalb35a908784863ee4a31335a0ede028f1 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalb35a908784863ee4a31335a0ede028f1 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'capstone::components.button','data' => ['type' => 'button','variant' => 'outline','@click' => '$el.closest(\'dialog\').close()']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('capstone::button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['type' => 'button','variant' => 'outline','@click' => '$el.closest(\'dialog\').close()']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>
Batal <?php echo $__env->renderComponent(); ?>
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
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'capstone::components.button','data' => ['type' => 'submit',':disabled' => 'saving']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('capstone::button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['type' => 'submit',':disabled' => 'saving']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>
Simpan <?php echo $__env->renderComponent(); ?>
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
<?php endif; ?><?php if (isset($component)) { $__componentOriginal23d0c1a5dcb8d467892a41eeb359fba6 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal23d0c1a5dcb8d467892a41eeb359fba6 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'capstone::components.dialog','data' => ['id' => 'finalization-confirm','title' => 'Konfirmasi Finalisasi']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('capstone::dialog'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['id' => 'finalization-confirm','title' => 'Konfirmasi Finalisasi']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>
<p class="my-4" x-text="action==='lock'?'Kunci bidding pada periode '+period?.name+'?':'Finalisasi seluruh kelompok yang memenuhi syarat pada periode '+period?.name+'?'"></p><?php if (isset($component)) { $__componentOriginalb35a908784863ee4a31335a0ede028f1 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalb35a908784863ee4a31335a0ede028f1 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'capstone::components.button','data' => ['variant' => 'outline','size' => 'sm','@click' => 'apply()',':disabled' => 'saving']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('capstone::button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['variant' => 'outline','size' => 'sm','@click' => 'apply()',':disabled' => 'saving']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>
Konfirmasi <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalb35a908784863ee4a31335a0ede028f1)): ?>
<?php $attributes = $__attributesOriginalb35a908784863ee4a31335a0ede028f1; ?>
<?php unset($__attributesOriginalb35a908784863ee4a31335a0ede028f1); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalb35a908784863ee4a31335a0ede028f1)): ?>
<?php $component = $__componentOriginalb35a908784863ee4a31335a0ede028f1; ?>
<?php unset($__componentOriginalb35a908784863ee4a31335a0ede028f1); ?>
<?php endif; ?> <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal23d0c1a5dcb8d467892a41eeb359fba6)): ?>
<?php $attributes = $__attributesOriginal23d0c1a5dcb8d467892a41eeb359fba6; ?>
<?php unset($__attributesOriginal23d0c1a5dcb8d467892a41eeb359fba6); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal23d0c1a5dcb8d467892a41eeb359fba6)): ?>
<?php $component = $__componentOriginal23d0c1a5dcb8d467892a41eeb359fba6; ?>
<?php unset($__componentOriginal23d0c1a5dcb8d467892a41eeb359fba6); ?>
<?php endif; ?>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('capstone::layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\WebsiteTekkom - Copy\Modules\Capstone\tests/../resources/views/pages\admin\finalization.blade.php ENDPATH**/ ?>