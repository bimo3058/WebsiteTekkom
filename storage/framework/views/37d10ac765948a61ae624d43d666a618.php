<div class="mx-auto max-w-4xl space-y-6"><div class="flex items-start gap-3 border-b pb-4"><div class="rounded-lg bg-blue-50 p-2"><?php if (isset($component)) { $__componentOriginal0ebc33cc959e9b0fde2e3c32f43abd3a = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal0ebc33cc959e9b0fde2e3c32f43abd3a = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'capstone::components.icon','data' => ['name' => 'ClipboardCheck','class' => 'h-5 w-5 text-blue-600']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('capstone::icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'ClipboardCheck','class' => 'h-5 w-5 text-blue-600']); ?>
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
<?php endif; ?></div><div><h2 class="text-lg font-semibold text-gray-900">Review Konfigurasi Periode</h2><p class="text-sm text-gray-500">Periksa kembali data sebelum menyimpan periode</p></div></div><div class="grid gap-6 md:grid-cols-2"><div class="rounded-xl border p-5"><h3 class="mb-4 font-semibold">Informasi Dasar</h3><p class="text-lg font-medium" x-text="form.name"></p><p class="mt-2 text-sm text-gray-600" x-text="form.start_date+' — '+form.end_date"></p><?php if (isset($component)) { $__componentOriginal22d7e929995a9d34325929f7d2857b6a = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal22d7e929995a9d34325929f7d2857b6a = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'capstone::components.badge','data' => ['variant' => 'outline','class' => 'mt-3','xText' => 'form.is_active ? \'Aktif\' : \'Nonaktif\'']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('capstone::badge'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['variant' => 'outline','class' => 'mt-3','x-text' => 'form.is_active ? \'Aktif\' : \'Nonaktif\'']); ?>
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
<?php endif; ?></div><div class="rounded-xl border p-5"><h3 class="mb-4 font-semibold">Konfigurasi Group</h3><p class="text-sm text-gray-600" x-text="form.min_group_size+'–'+form.max_group_size+' anggota per group'"></p><p class="mt-2 text-sm text-gray-600" x-text="form.max_supervisor_load+' group/dosen'"></p></div><div class="rounded-xl border p-5"><h3 class="mb-4 font-semibold">Tanggal Fase</h3><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = ['bidding'=>'Bidding','pdc1'=>'PDC1','pdc2'=>'PDC2','ta'=>'Sidang TA']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key=>$label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?><div class="mb-3 text-sm"><p class="font-medium"><?php echo e($label); ?></p><p class="text-gray-600" x-text="(form.<?php echo e($key); ?>_start || '-')+' — '+(form.<?php echo e($key); ?>_end || '-')"></p><p class="text-xs text-gray-500" x-text="'Pengingat: '+(form.<?php echo e($key); ?>_reminder_at || '-')"></p></div><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?><p class="text-sm font-medium">EXPO TA</p><p class="text-sm text-gray-600" x-text="form.expo_date || '-'"></p><p class="text-xs text-gray-500" x-text="'Pengingat: '+(form.expo_reminder_at || '-')"></p></div><div class="rounded-xl border p-5"><h3 class="mb-4 font-semibold">Setup Evaluasi</h3><template x-for="type in types" :key="type"><div class="flex justify-between gap-3 py-2 text-sm"><span x-text="type.replaceAll('_',' ')"></span><span x-text="assessments[type].length+' komponen / '+weight(assessments[type])+'%'"></span></div></template><div class="flex justify-between py-2 text-sm"><span>Peer Review</span><span x-text="peerIds.length+' indikator / '+weight(peerIds,true)+'%'"></span></div></div></div></div>
<?php /**PATH C:\WebsiteTekkom - Copy\Modules/Capstone\resources/views/pages/admin/periods/steps/review.blade.php ENDPATH**/ ?>