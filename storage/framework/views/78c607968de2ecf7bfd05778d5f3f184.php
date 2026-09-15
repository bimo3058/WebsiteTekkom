<?php if (isset($component)) { $__componentOriginal315efe54efa54bd7b6313284b579a4ac = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal315efe54efa54bd7b6313284b579a4ac = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'banksoal::components.layouts.dosen-admin','data' => []] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('banksoal::layouts.dosen-admin'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

    <?php $__env->startSection('breadcrumbs'); ?>
        <a href="<?php echo e(route('banksoal.soal.dosen.index')); ?>" class="text-slate-500 hover:text-primary transition-colors">Bank Soal</a>
        <span class="mx-2 text-slate-300">/</span>
        <span class="text-slate-800 font-semibold">Detail Soal</span>
    <?php $__env->stopSection(); ?>
    <?php if (isset($component)) { $__componentOriginal4f4ab3c60e35d72431cf46a8394b8c9b = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal4f4ab3c60e35d72431cf46a8394b8c9b = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'banksoal::components.ui.page-header','data' => ['title' => 'Detail Soal','subtitle' => 'Informasi detail pertanyaan dan opsi jawaban.']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('banksoal::ui.page-header'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => 'Detail Soal','subtitle' => 'Informasi detail pertanyaan dan opsi jawaban.']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

         <?php $__env->slot('actions', null, []); ?> 
            <a href="<?php echo e(route('banksoal.soal.dosen.index')); ?>" class="inline-flex items-center gap-2 rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm font-medium text-slate-700 hover:bg-slate-50">
                <i class="fas fa-arrow-left"></i> Kembali
            </a>
         <?php $__env->endSlot(); ?>
     <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal4f4ab3c60e35d72431cf46a8394b8c9b)): ?>
<?php $attributes = $__attributesOriginal4f4ab3c60e35d72431cf46a8394b8c9b; ?>
<?php unset($__attributesOriginal4f4ab3c60e35d72431cf46a8394b8c9b); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal4f4ab3c60e35d72431cf46a8394b8c9b)): ?>
<?php $component = $__componentOriginal4f4ab3c60e35d72431cf46a8394b8c9b; ?>
<?php unset($__componentOriginal4f4ab3c60e35d72431cf46a8394b8c9b); ?>
<?php endif; ?>

    <?php if (isset($component)) { $__componentOriginaled3368a8117ee5d527ad55a40e435525 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginaled3368a8117ee5d527ad55a40e435525 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'banksoal::components.ui.panel','data' => ['title' => 'Detail Soal (' . ('Q-' . str_pad($soal->id, 3, '0', STR_PAD_LEFT)) . ')','subtitle' => 'Status dan metadata soal','padding' => 'p-6']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('banksoal::ui.panel'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute('Detail Soal (' . ('Q-' . str_pad($soal->id, 3, '0', STR_PAD_LEFT)) . ')'),'subtitle' => 'Status dan metadata soal','padding' => 'p-6']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

        <div class="mb-6 grid grid-cols-1 gap-4 sm:grid-cols-2 md:grid-cols-3">
            <div class="rounded-xl border border-slate-200 bg-slate-50 p-4">
                <p class="text-xs font-semibold uppercase tracking-wider text-slate-500">Mata Kuliah</p>
                <p class="mt-1 text-sm font-semibold text-slate-800"><?php echo e($soal->mataKuliah->kode ?? '-'); ?> - <?php echo e($soal->mataKuliah->nama ?? '-'); ?></p>
            </div>
            <div class="rounded-xl border border-slate-200 bg-slate-50 p-4">
                <p class="text-xs font-semibold uppercase tracking-wider text-slate-500">CPL / Topik</p>
                <p class="mt-1 text-sm font-semibold text-slate-800"><?php echo e($soal->cpl->kode ?? '-'); ?></p>
            </div>
            <div class="rounded-xl border border-slate-200 bg-slate-50 p-4">
                <p class="text-xs font-semibold uppercase tracking-wider text-slate-500">CPMK</p>
                <p class="mt-1 text-sm font-semibold text-slate-800"><?php echo e($soal->cpmk->kode ?? '-'); ?></p>
            </div>
            <div class="rounded-xl border border-slate-200 bg-slate-50 p-4">
                <p class="text-xs font-semibold uppercase tracking-wider text-slate-500">Kesulitan / Bobot</p>
                <p class="mt-1 text-sm font-semibold text-slate-800"><?php echo e(ucfirst($soal->kesulitan)); ?> (<?php echo e($soal->bobot); ?> Poin)</p>
            </div>
            <div class="rounded-xl border border-slate-200 bg-slate-50 p-4">
                <p class="text-xs font-semibold uppercase tracking-wider text-slate-500">Tipe Soal</p>
                <p class="mt-1 text-sm font-semibold text-slate-800"><?php echo e(ucwords(str_replace('_', ' ', $soal->tipe_soal ?? 'Pilihan Ganda'))); ?></p>
            </div>
            <div class="rounded-xl border border-slate-200 bg-slate-50 p-4">
                <p class="text-xs font-semibold uppercase tracking-wider text-slate-500">Status Pertanyaan</p>
                <span class="mt-1 inline-flex rounded-full bg-slate-200 px-2.5 py-1 text-xs font-semibold text-slate-700"><?php echo e(strtoupper($soal->status)); ?></span>
            </div>
        </div>
        
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(isset($review) && !empty($review->catatan)): ?>
        <div class="mb-6">
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(in_array(strtolower($soal->status), ['revisi', 'ditolak'])): ?>
            <div class="rounded-xl border border-red-200 bg-red-50 p-4">
                <div class="flex items-start gap-3">
                    <i class="fas fa-exclamation-triangle mt-1 text-red-500"></i>
                    <div>
                        <h4 class="text-sm font-bold text-red-800">Catatan Revisi dari GPM</h4>
                        <p class="mt-1 text-sm text-red-700 leading-relaxed"><?php echo e($review->catatan); ?></p>
                    </div>
                </div>
            </div>
            <?php else: ?>
            <div class="rounded-xl border border-amber-200 bg-amber-50 p-4">
                <div class="flex items-start gap-3">
                    <i class="fas fa-history mt-1 text-amber-500"></i>
                    <div>
                        <h4 class="text-sm font-bold text-amber-800">Riwayat Catatan GPM Sebelumnya</h4>
                        <p class="mt-1 text-sm text-amber-700 leading-relaxed"><?php echo e($review->catatan); ?></p>
                    </div>
                </div>
            </div>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </div>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

        <div class="mb-6">
            <p class="mb-2 text-sm font-semibold text-slate-700">Pertanyaan</p>
            <div class="rounded-xl border border-slate-200 bg-white p-4 text-sm leading-relaxed text-slate-900 prose quill-content max-w-none">
                <?php echo $soal->soal; ?>

            </div>
        </div>

        <?php $__env->startPush('styles'); ?>
        <style>
            .quill-content p { margin-bottom: 1em; }
            .quill-content ul { list-style-type: disc; margin-left: 1.5em; margin-bottom: 1em; }
            .quill-content ol { list-style-type: decimal; margin-left: 1.5em; margin-bottom: 1em; }
            .quill-content blockquote { border-left: 4px solid #cbd5e1; padding-left: 1em; color: #64748b; font-style: italic; }
            .quill-content pre { background-color: #1e293b; color: #f8fafc; padding: 1em; border-radius: 0.5rem; overflow-x: auto; }
            .quill-content img { max-width: 100%; border-radius: 0.5rem; margin-top: 1em; margin-bottom: 1em; }
            .quill-content table { width: 100%; border-collapse: collapse; margin-bottom: 1em; }
            .quill-content th, .quill-content td { border: 1px solid #cbd5e1; padding: 0.5rem; }
        </style>
        <?php $__env->stopPush(); ?>

        <div>
            <p class="mb-2 text-sm font-semibold text-slate-700">Pilihan Jawaban</p>
            <div class="space-y-2">
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $soal->jawaban; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $jawab): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                    <div class="flex items-start gap-3 rounded-xl border p-4 <?php echo e($jawab->is_benar ? 'border-green-400 bg-green-50' : 'border-slate-200 bg-white'); ?>">
                        <span class="inline-flex h-6 w-6 items-center justify-center rounded-full text-xs font-bold <?php echo e($jawab->is_benar ? 'bg-green-600 text-white' : 'bg-slate-200 text-slate-700'); ?>"><?php echo e($jawab->opsi); ?></span>
                        <div class="flex-1 text-sm text-slate-700 prose prose-sm max-w-none"><?php echo $jawab->deskripsi; ?></div>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($jawab->is_benar): ?>
                            <i class="fas fa-check-circle text-green-600" title="Jawaban Benar"></i>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </div>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
            </div>
        </div>

        <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('banksoal.edit')): ?>
            <div class="mt-6 flex justify-end">
                <a href="<?php echo e(route('banksoal.soal.dosen.edit', $soal->id)); ?>" class="inline-flex items-center gap-2 rounded-xl bg-primary px-4 py-2.5 text-sm font-semibold text-white hover:bg-primary/90">
                    <i class="fas fa-pen"></i> Edit Soal
                </a>
            </div>
        <?php endif; ?>
     <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginaled3368a8117ee5d527ad55a40e435525)): ?>
<?php $attributes = $__attributesOriginaled3368a8117ee5d527ad55a40e435525; ?>
<?php unset($__attributesOriginaled3368a8117ee5d527ad55a40e435525); ?>
<?php endif; ?>
<?php if (isset($__componentOriginaled3368a8117ee5d527ad55a40e435525)): ?>
<?php $component = $__componentOriginaled3368a8117ee5d527ad55a40e435525; ?>
<?php unset($__componentOriginaled3368a8117ee5d527ad55a40e435525); ?>
<?php endif; ?>
 <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal315efe54efa54bd7b6313284b579a4ac)): ?>
<?php $attributes = $__attributesOriginal315efe54efa54bd7b6313284b579a4ac; ?>
<?php unset($__attributesOriginal315efe54efa54bd7b6313284b579a4ac); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal315efe54efa54bd7b6313284b579a4ac)): ?>
<?php $component = $__componentOriginal315efe54efa54bd7b6313284b579a4ac; ?>
<?php unset($__componentOriginal315efe54efa54bd7b6313284b579a4ac); ?>
<?php endif; ?>
<?php /**PATH C:\WebsiteTekkom - Copy\Modules\BankSoal\resources\views\pages\bank-soal\Dosen\show.blade.php ENDPATH**/ ?>