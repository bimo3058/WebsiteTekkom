<?php if (isset($component)) { $__componentOriginalbfe3dfc9f8c7e29aaca1eb4d19fe5039 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalbfe3dfc9f8c7e29aaca1eb4d19fe5039 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'banksoal::components.layouts.gpm-master','data' => []] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('banksoal::layouts.gpm-master'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

    <div class="mb-6 flex flex-col gap-4 border-b border-slate-200 pb-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-2xl font-bold text-slate-900"><?php echo e($mataKuliah->nama); ?></h1>
            <p class="text-sm text-slate-500"><?php echo e($mataKuliah->kode); ?> • Program Studi Teknik Komputer</p>
        </div>
        <a href="<?php echo e(route('banksoal.soal.gpm.riwayat-validasi.bank-soal')); ?>" class="inline-flex items-center gap-2 rounded-xl border border-slate-200 px-4 py-2.5 text-sm font-semibold text-slate-600 hover:bg-slate-50">
            <i class="fas fa-arrow-left"></i> Kembali
        </a>
    </div>

    <div class="mb-6 flex justify-end">
        <?php echo e($riwayatSoal->links('banksoal::components.ui.laravel-pagination')); ?>

    </div>

    <div class="space-y-6">
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $riwayatSoal; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $soal): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
            <?php
                $isSesuai = $soal->status_review == 'Sesuai';
                $isKurang = $soal->status_review == 'Kurang Sesuai';
                $colorClass = $isSesuai ? 'emerald' : ($isKurang ? 'amber' : 'rose');
                $badgeBg = $isSesuai ? 'bg-emerald-600 text-white' : ($isKurang ? 'bg-amber-500 text-white' : 'bg-rose-600 text-white');
                $iconStatus = $isSesuai ? 'fa-check-circle' : ($isKurang ? 'fa-exclamation-triangle' : 'fa-times-circle');
            ?>

            <div class="bg-white border border-slate-200 rounded-2xl shadow-sm overflow-hidden">
                <div class="h-1 <?php echo e($colorClass === 'emerald' ? 'bg-emerald-500' : ($colorClass === 'amber' ? 'bg-amber-500' : 'bg-rose-500')); ?>"></div>
                <div class="p-6">
                    <div class="flex flex-col gap-3 border-b border-slate-100 pb-4 sm:flex-row sm:items-center sm:justify-between">
                        <div class="flex flex-wrap items-center gap-2">
                            <span class="inline-flex items-center rounded-full bg-primary px-3 py-1 text-[11px] font-semibold text-white">SOAL #<?php echo e($riwayatSoal->firstItem() + $index); ?></span>
                            <span class="inline-flex items-center rounded-full bg-slate-100 px-3 py-1 text-[11px] font-semibold text-slate-600 border border-slate-200">LEVEL: <?php echo e(strtoupper($soal->kesulitan)); ?></span>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($soal->cpl): ?>
                                <span class="inline-flex items-center rounded-full bg-slate-100 px-3 py-1 text-[11px] font-semibold text-slate-600 border border-slate-200"><i class="fas fa-tag mr-1"></i> <?php echo e($soal->cpl->kode); ?></span>
                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            <span class="text-xs text-slate-500">Dinilai pada: <?php echo e(\Carbon\Carbon::parse($soal->tanggal_review)->translatedFormat('d M Y')); ?></span>
                        </div>
                        <span class="inline-flex items-center gap-2 rounded-full <?php echo e($badgeBg); ?> px-3 py-1 text-[11px] font-semibold">
                            <i class="fas <?php echo e($iconStatus); ?>"></i> <?php echo e(strtoupper($soal->status_review)); ?>

                        </span>
                    </div>

                    <div class="prose prose-sm max-w-none text-slate-900 font-semibold leading-relaxed mt-4">
                        <?php echo $soal->soal; ?>

                    </div>

                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($soal->jawaban && count($soal->jawaban) > 0): ?>
                        <ul class="mt-4 space-y-2">
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $soal->jawaban; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $idx => $jawab): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                                <?php $char = chr(65 + $idx); ?>
                                <li class="flex items-center gap-3 rounded-lg border <?php echo e($jawab->is_benar ? 'border-emerald-200 bg-emerald-50' : 'border-slate-200 bg-slate-50'); ?> px-4 py-3">
                                    <span class="text-xs font-bold <?php echo e($jawab->is_benar ? 'text-emerald-600' : 'text-slate-500'); ?>"><?php echo e($jawab->opsi ?? $char); ?>.</span>
                                    <div class="prose prose-sm max-w-none text-slate-700"><?php echo $jawab->deskripsi; ?></div>
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($jawab->is_benar): ?>
                                        <i class="fas fa-check-circle text-emerald-500 ml-auto"></i>
                                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                </li>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                        </ul>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                    <div class="mt-5 rounded-xl border-l-4 <?php echo e($colorClass === 'emerald' ? 'border-emerald-500 bg-emerald-50' : ($colorClass === 'amber' ? 'border-amber-500 bg-amber-50' : 'border-rose-500 bg-rose-50')); ?> p-4">
                        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                            <div class="flex items-center gap-3">
                                <div class="flex h-8 w-8 items-center justify-center rounded-full bg-white/60">
                                    <i class="fas fa-comment-dots text-slate-500"></i>
                                </div>
                                <p class="text-sm font-semibold text-slate-700">Catatan Evaluator</p>
                            </div>
                            <button type="button" class="inline-flex items-center gap-2 rounded-lg border border-slate-200 bg-white px-3 py-2 text-xs font-semibold text-slate-600 hover:bg-slate-50" data-modal-open="editModal<?php echo e($soal->id); ?>">
                                <i class="fas fa-edit"></i> Edit Review
                            </button>
                        </div>
                        <p class="mt-3 text-sm text-slate-700"><?php echo e($soal->catatan ?: 'Tidak ada catatan.'); ?></p>
                    </div>
                </div>
            </div>

            <div id="editModal<?php echo e($soal->id); ?>" class="fixed inset-0 z-50 hidden" aria-hidden="true">
                <div class="absolute inset-0 bg-slate-900/40" data-modal-overlay="editModal<?php echo e($soal->id); ?>"></div>
                <div class="relative mx-auto mt-16 w-full max-w-lg rounded-2xl bg-white shadow-xl">
                    <form action="<?php echo e(route('banksoal.soal.gpm.validasi-bank-soal.update', $soal->id)); ?>" method="POST">
                        <?php echo csrf_field(); ?>
                        <?php echo method_field('PUT'); ?>
                        <div class="flex items-center justify-between border-b border-slate-200 px-5 py-4">
                            <h2 class="text-sm font-semibold text-slate-900">Edit Hasil Review</h2>
                            <button type="button" class="text-slate-400 hover:text-slate-600" data-modal-close="editModal<?php echo e($soal->id); ?>">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                        <div class="px-5 py-4 space-y-4">
                            <div>
                                <label class="text-xs font-semibold text-slate-600">Status Review</label>
                                <select name="status_review" class="mt-2 w-full rounded-lg border border-slate-200 px-3 py-2 text-sm" required>
                                    <option value="Sesuai" <?php echo e($soal->status_review == 'Sesuai' ? 'selected' : ''); ?>>Sesuai</option>
                                    <option value="Kurang Sesuai" <?php echo e($soal->status_review == 'Kurang Sesuai' ? 'selected' : ''); ?>>Kurang Sesuai</option>
                                    <option value="Revisi" <?php echo e($soal->status_review == 'Revisi' ? 'selected' : ''); ?>>Revisi</option>
                                </select>
                            </div>
                            <div>
                                <label class="text-xs font-semibold text-slate-600">Catatan Evaluator</label>
                                <textarea name="catatan" class="mt-2 w-full rounded-lg border border-slate-200 px-3 py-2 text-sm" rows="4" placeholder="Tuliskan catatan revisi jika ada..."><?php echo e($soal->catatan); ?></textarea>
                            </div>
                        </div>
                        <div class="flex items-center justify-end gap-3 border-t border-slate-200 bg-slate-50 px-5 py-4">
                            <button type="button" class="rounded-lg border border-slate-200 px-4 py-2 text-xs font-semibold text-slate-600" data-modal-close="editModal<?php echo e($soal->id); ?>">Batal</button>
                            <button type="submit" class="rounded-lg bg-primary px-4 py-2 text-xs font-semibold text-white hover:bg-primary/90">Simpan Perubahan</button>
                        </div>
                    </form>
                </div>
            </div>
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
            <div class="rounded-2xl border border-slate-200 bg-white p-8 text-center">
                <i class="fas fa-folder-open mb-3 text-3xl text-slate-300"></i>
                <p class="text-lg font-semibold text-slate-900">Riwayat Validasi Kosong</p>
                <p class="text-sm text-slate-500">Belum ada hasil review soal yang bisa ditampilkan untuk mata kuliah ini.</p>
                <a href="<?php echo e(route('banksoal.soal.gpm.riwayat-validasi.bank-soal')); ?>" class="mt-4 inline-flex items-center rounded-xl bg-primary px-4 py-2 text-sm font-semibold text-white hover:bg-primary/90">Kembali ke Daftar</a>
            </div>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    </div>

    <div class="mt-6 flex justify-center">
        <?php echo e($riwayatSoal->links('banksoal::components.ui.laravel-pagination')); ?>

    </div>
 <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalbfe3dfc9f8c7e29aaca1eb4d19fe5039)): ?>
<?php $attributes = $__attributesOriginalbfe3dfc9f8c7e29aaca1eb4d19fe5039; ?>
<?php unset($__attributesOriginalbfe3dfc9f8c7e29aaca1eb4d19fe5039); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalbfe3dfc9f8c7e29aaca1eb4d19fe5039)): ?>
<?php $component = $__componentOriginalbfe3dfc9f8c7e29aaca1eb4d19fe5039; ?>
<?php unset($__componentOriginalbfe3dfc9f8c7e29aaca1eb4d19fe5039); ?>
<?php endif; ?><?php /**PATH C:\WebsiteTekkom - Copy\Modules\BankSoal\resources\views\gpm\riwayat-validasi\bank-soal-detail.blade.php ENDPATH**/ ?>