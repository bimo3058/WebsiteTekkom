<?php $__env->startSection('title','Document Uploads'); ?>
<?php $__env->startSection('content'); ?>
<div x-data="adminDocumentUploads" class="space-y-6"><div class="flex flex-wrap items-center justify-between gap-4"><h1 class="text-3xl font-bold">Document Uploads</h1></div><?php echo $__env->make('capstone::pages.admin.shared.toolbar', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
<?php echo $__env->make('capstone::partials.loading', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
<div class="flex flex-wrap gap-3"><select x-model="source" @change="page=1;load()" class="rounded border bg-background p-2 text-sm"><option value="">Semua sumber</option><option value="phase_documents">Dokumen fase</option><option value="expo_documents">Expo</option><option value="ta_documents">Tugas Akhir</option></select><select x-model="status" @change="page=1;load()" class="rounded border bg-background p-2 text-sm"><option value="">Semua status</option><option>SUBMITTED</option><option>APPROVED</option><option>REJECTED</option><option>DRAFT</option></select><label class="text-sm">Dari <input type="date" x-model="dateFrom" @change="page=1;load()" class="rounded border p-2"></label><label class="text-sm">Sampai <input type="date" x-model="dateTo" @change="page=1;load()" class="rounded border p-2"></label><?php if (isset($component)) { $__componentOriginalb35a908784863ee4a31335a0ede028f1 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalb35a908784863ee4a31335a0ede028f1 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'capstone::components.button','data' => ['variant' => 'outline','size' => 'sm','@click' => 'page=1;load()',':disabled' => 'saving']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('capstone::button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['variant' => 'outline','size' => 'sm','@click' => 'page=1;load()',':disabled' => 'saving']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>
Terapkan pencarian <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalb35a908784863ee4a31335a0ede028f1)): ?>
<?php $attributes = $__attributesOriginalb35a908784863ee4a31335a0ede028f1; ?>
<?php unset($__attributesOriginalb35a908784863ee4a31335a0ede028f1); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalb35a908784863ee4a31335a0ede028f1)): ?>
<?php $component = $__componentOriginalb35a908784863ee4a31335a0ede028f1; ?>
<?php unset($__componentOriginalb35a908784863ee4a31335a0ede028f1); ?>
<?php endif; ?></div><div x-show="!loading && !error" x-cloak class="space-y-5"><div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4"><?php if (isset($component)) { $__componentOriginal5ac34a7ad891af7fa808b2dbb0e60582 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal5ac34a7ad891af7fa808b2dbb0e60582 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'capstone::components.card','data' => ['title' => 'Total']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('capstone::card'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => 'Total']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>
<p class="px-6 text-3xl font-semibold" x-text="summary.total_documents ?? 0"></p> <?php echo $__env->renderComponent(); ?>
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
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'capstone::components.card','data' => ['title' => 'Dokumen Fase']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('capstone::card'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => 'Dokumen Fase']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>
<p class="px-6 text-3xl font-semibold" x-text="summary.phase_documents ?? 0"></p> <?php echo $__env->renderComponent(); ?>
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
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'capstone::components.card','data' => ['title' => 'Expo']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('capstone::card'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => 'Expo']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>
<p class="px-6 text-3xl font-semibold" x-text="summary.expo_documents ?? 0"></p> <?php echo $__env->renderComponent(); ?>
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
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'capstone::components.card','data' => ['title' => 'Tugas Akhir']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('capstone::card'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => 'Tugas Akhir']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>
<p class="px-6 text-3xl font-semibold" x-text="summary.ta_documents ?? 0"></p> <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal5ac34a7ad891af7fa808b2dbb0e60582)): ?>
<?php $attributes = $__attributesOriginal5ac34a7ad891af7fa808b2dbb0e60582; ?>
<?php unset($__attributesOriginal5ac34a7ad891af7fa808b2dbb0e60582); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal5ac34a7ad891af7fa808b2dbb0e60582)): ?>
<?php $component = $__componentOriginal5ac34a7ad891af7fa808b2dbb0e60582; ?>
<?php unset($__componentOriginal5ac34a7ad891af7fa808b2dbb0e60582); ?>
<?php endif; ?></div><template x-for="item in items" :key="item.group_id"><div class="overflow-hidden rounded-xl border bg-card"><button class="flex w-full flex-wrap items-center justify-between gap-3 p-4 text-left" @click="expanded=expanded===item.group_id?null:item.group_id" :aria-expanded="expanded===item.group_id"><div><strong x-text="item.group_code"></strong><p class="text-sm text-muted-foreground" x-text="item.period?.name+' - '+item.members.map(m=>m.name).join(', ')"></p></div><span x-text="item.total_documents+' dokumen'"></span></button><div x-show="expanded===item.group_id" class="overflow-x-auto border-t"><table class="w-full min-w-[720px] text-left text-sm"><thead class="bg-muted/40"><tr><th class="p-3">Dokumen</th><th class="p-3">Fase</th><th class="p-3">Pengunggah</th><th class="p-3">Status</th><th class="p-3">Tanggal</th><th class="p-3">Aksi</th></tr></thead><tbody><template x-for="doc in item.documents" :key="doc.source+':'+doc.id+':'+doc.document_type"><tr class="border-t"><td class="p-3" x-text="doc.original_name || doc.document_type"></td><td class="p-3" x-text="doc.phase"></td><td class="p-3" x-text="doc.student?.name || '-'"></td><td class="p-3"><span x-text="doc.status"></span><p class="text-muted-foreground" x-text="doc.feedback"></p></td><td class="p-3" x-text="date(doc.uploaded_at,true)"></td><td class="p-3"><?php if (isset($component)) { $__componentOriginalb35a908784863ee4a31335a0ede028f1 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalb35a908784863ee4a31335a0ede028f1 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'capstone::components.button','data' => ['variant' => 'outline','size' => 'sm','@click' => 'downloadDoc(doc)',':disabled' => 'saving']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('capstone::button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['variant' => 'outline','size' => 'sm','@click' => 'downloadDoc(doc)',':disabled' => 'saving']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>
Unduh <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalb35a908784863ee4a31335a0ede028f1)): ?>
<?php $attributes = $__attributesOriginalb35a908784863ee4a31335a0ede028f1; ?>
<?php unset($__attributesOriginalb35a908784863ee4a31335a0ede028f1); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalb35a908784863ee4a31335a0ede028f1)): ?>
<?php $component = $__componentOriginalb35a908784863ee4a31335a0ede028f1; ?>
<?php unset($__componentOriginalb35a908784863ee4a31335a0ede028f1); ?>
<?php endif; ?></td></tr></template></tbody></table></div></div></template><p x-show="!items.length" class="rounded-xl border p-8 text-center text-muted-foreground">Tidak ada unggahan yang sesuai filter.</p><div class="flex justify-between items-center"><span x-text="(pagination.total||0)+' kelompok'"></span><div class="flex gap-3"><?php if (isset($component)) { $__componentOriginalb35a908784863ee4a31335a0ede028f1 = $component; } ?>
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
<?php endif; ?></div></div></div></div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('capstone::layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\WebsiteTekkom - Copy\Modules\Capstone\tests/../resources/views/pages\admin\document-uploads.blade.php ENDPATH**/ ?>