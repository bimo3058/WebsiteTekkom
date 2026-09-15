<!-- RPS Form Component -->
<?php
    $kkoOptions = [
        'C1' => 'Mengingat',
        'C2' => 'Memahami',
        'C3' => 'Menerapkan',
        'C4' => 'Menganalisis',
        'C5' => 'Mengevaluasi',
        'C6' => 'Mencipta',
        'P1' => 'Meniru',
        'P2' => 'Menyesuaikan',
        'P3' => 'Membiasakan',
        'P4' => 'Menguasai',
        'P5' => 'Mahir',
        'A1' => 'Menerima',
        'A2' => 'Merespon',
        'A3' => 'Menilai',
        'A4' => 'Mengorganisasi',
        'A5' => 'Menghayati',
    ];

    $cpmkRows = old('cpmk_rows', [[
        'cpl_id' => '',
        'kode' => '',
        'kko' => '',
        'objek' => '',
        'konteks' => '',
    ]]);
?>

<div class="card mb-8">
    <div class="card-header flex items-center justify-between gap-4">
        <div>
            <h2 class="text-lg font-semibold text-slate-900">Formulir Rencana Pembelajaran</h2>
            <p class="text-sm text-slate-500 mt-1">Setiap baris CPMK akan disimpan ke <span class="font-semibold">bs_cpmk</span> dan dipasangkan dengan CPL per baris.</p>
        </div>
    </div>

    <form action="<?php echo e(route('banksoal.rps.dosen.store')); ?>" method="POST" enctype="multipart/form-data" class="p-4 space-y-6"
        data-route-cpl="<?php echo e(route('banksoal.rps.dosen.cpl')); ?>"
        data-route-dosen="<?php echo e(route('banksoal.rps.dosen.dosen')); ?>"
        data-cpmk-row-builder="1">
        <?php echo csrf_field(); ?>

        <!-- Row 1: Mata Kuliah & Dosen Lain -->
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div class="form-group-compact">
                <label class="form-label form-label-required">Mata Kuliah</label>
                <select name="mata_kuliah_id" id="mkSelect" class="form-control compact-control" required <?php echo e(!$isUploadOpen ? 'disabled' : ''); ?>>
                    <option value="" disabled selected>Pilih Mata Kuliah</option>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $mataKuliahs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $mk): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                        <option value="<?php echo e($mk->id); ?>"><?php echo e($mk->kode); ?> - <?php echo e($mk->nama); ?> (<?php echo e($mk->sks); ?> SKS)</option>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                </select>
            </div>

            <div class="form-group-compact">
                <label class="form-label">Dosen Pengampu Lain</label>
                <select name="dosen_lain[]" id="dosenSelect" class="form-control compact-control" multiple <?php echo e(!$isUploadOpen ? 'disabled' : ''); ?>></select>
                <small class="form-hint">Pilih satu atau lebih dosen pengampu tambahan.</small>
            </div>
        </div>

        <!-- Row 2: Semester & Tahun Ajaran -->
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div class="form-group-compact">
                <label class="form-label">Semester</label>
                <select name="semester" id="semester" class="form-control compact-control" <?php echo e(!$isUploadOpen ? 'disabled' : ''); ?>>
                    <option value="Ganjil" <?php echo e($semester == 'Ganjil' ? 'selected' : ''); ?>>Ganjil</option>
                    <option value="Genap" <?php echo e($semester == 'Genap' ? 'selected' : ''); ?>>Genap</option>
                </select>
            </div>

            <div class="form-group-compact">
                <label class="form-label">Tahun Ajaran</label>
                <select name="tahun_ajaran" id="tahun_ajaran" class="form-control compact-control" <?php echo e(!$isUploadOpen ? 'disabled' : ''); ?>>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $tahunAjarans; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $ta): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                        <option value="<?php echo e($ta); ?>" <?php echo e($ta == $academicYear ? 'selected' : ''); ?>><?php echo e($ta); ?></option>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                </select>
            </div>
        </div>

        <!-- CPMK Rows -->
        <div class="rounded-2xl border border-slate-200 bg-white shadow-sm overflow-hidden">
            <div class="flex items-center justify-between gap-4 border-b border-slate-200 px-4 py-3">
                <div>
                    <h3 class="text-base font-semibold text-slate-900">CPMK per Baris</h3>
                    <p class="text-xs text-slate-500 mt-1">Format: CPMK 1 - Mahasiswa mampu (KKO C6) merancang ...</p>
                </div>
                <button type="button" class="btn-primary inline-flex items-center gap-2" id="addCpmkRowBtn" <?php echo e(!$isUploadOpen ? 'disabled' : ''); ?>>
                    <i class="fas fa-plus"></i> Tambah
                </button>
            </div>

            <div class="hidden xl:grid xl:grid-cols-[220px_220px_170px_1fr_1fr_44px] gap-3 border-b border-slate-200 bg-slate-50 px-4 py-3 text-[11px] font-semibold uppercase tracking-wide text-slate-500">
                <div>CPL</div>
                <div>Kode CPMK</div>
                <div>KKO</div>
                <div>Objek</div>
                <div>Konteks</div>
                <div>Aksi</div>
            </div>

            <div id="cpmkRows" class="divide-y divide-slate-200" data-cpmk-rows>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $cpmkRows; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $row): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                    <div class="grid gap-3 px-4 py-4 grid-cols-1 xl:grid-cols-[220px_1fr] items-start" data-cpmk-row data-row-index="<?php echo e($index); ?>">
                        <div>
                            <label class="text-xs font-semibold text-slate-500">CPL</label>
                            <select name="cpmk_rows[<?php echo e($index); ?>][cpl_id]" class="form-control compact-control" data-cpmk-cpl-select data-selected-value="<?php echo e($row['cpl_id']); ?>" required <?php echo e(!$isUploadOpen ? 'disabled' : ''); ?>>
                                <option value="">Pilih CPL</option>
                            </select>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['cpmk_rows.' . $index . '.cpl_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                <p class="text-xs text-red-600"><?php echo e($message); ?></p>
                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                            <div class="mt-4 rounded-xl bg-slate-50 px-3 py-2 text-xs text-slate-500 hidden xl:block" data-cpmk-preview>
                                Pratinjau CPMK akan muncul setelah field diisi.
                            </div>
                        </div>

                        <div>
                            <div class="grid gap-3 grid-cols-1 md:grid-cols-2">
                                <div>
                                    <label class="text-xs font-semibold text-slate-500">Kode CPMK</label>
                                    <div class="flex items-stretch gap-2">
                                        <span class="inline-flex items-center rounded-xl border border-slate-200 bg-slate-50 px-3 text-sm font-semibold text-slate-500">CPMK</span>
                                        <input type="text" name="cpmk_rows[<?php echo e($index); ?>][kode]" value="<?php echo e($row['kode']); ?>" class="form-control compact-control" placeholder="1" required <?php echo e(!$isUploadOpen ? 'disabled' : ''); ?>>
                                    </div>
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['cpmk_rows.' . $index . '.kode'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                        <p class="text-xs text-red-600"><?php echo e($message); ?></p>
                                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                </div>

                                <div>
                                    <label class="text-xs font-semibold text-slate-500">KKO</label>
                                    <select name="cpmk_rows[<?php echo e($index); ?>][kko]" class="form-control compact-control" required <?php echo e(!$isUploadOpen ? 'disabled' : ''); ?>>
                                        <option value="">Pilih KKO</option>
                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $kkoOptions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $value => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                                            <option value="<?php echo e($value); ?>" <?php echo e($row['kko'] === $value ? 'selected' : ''); ?>><?php echo e($value); ?> - <?php echo e($label); ?></option>
                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                                    </select>
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['cpmk_rows.' . $index . '.kko'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                        <p class="text-xs text-red-600"><?php echo e($message); ?></p>
                                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                </div>
                            </div>

                            <div class="mt-3 grid gap-3 grid-cols-1 md:grid-cols-2">
                                <div>
                                    <label class="text-xs font-semibold text-slate-500">Objek</label>
                                    <input type="text" name="cpmk_rows[<?php echo e($index); ?>][objek]" value="<?php echo e($row['objek']); ?>" class="form-control compact-control" placeholder="contoh: merancang sistem IoT" required <?php echo e(!$isUploadOpen ? 'disabled' : ''); ?>>
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['cpmk_rows.' . $index . '.objek'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                        <p class="text-xs text-red-600"><?php echo e($message); ?></p>
                                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                </div>

                                <div>
                                    <label class="text-xs font-semibold text-slate-500">Konteks</label>
                                    <input type="text" name="cpmk_rows[<?php echo e($index); ?>][konteks]" value="<?php echo e($row['konteks']); ?>" class="form-control compact-control" placeholder="contoh: sesuai kebutuhan pengguna" <?php echo e(!$isUploadOpen ? 'disabled' : ''); ?>>
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['cpmk_rows.' . $index . '.konteks'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                        <p class="text-xs text-red-600"><?php echo e($message); ?></p>
                                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                </div>
                            </div>

                            <div class="mt-3 flex justify-end">
                                <button type="button" class="inline-flex h-11 w-11 items-center justify-center rounded-xl border border-rose-200 text-rose-500 hover:bg-rose-50" data-remove-cpmk-row aria-label="Hapus baris CPMK" <?php echo e(!$isUploadOpen ? 'disabled' : ''); ?>>
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>

                            <div class="mt-3 rounded-xl bg-slate-50 px-3 py-2 text-xs text-slate-500 block xl:hidden" data-cpmk-preview>
                                Pratinjau CPMK akan muncul setelah field diisi.
                            </div>
                        </div>
                    </div>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
            </div>
        </div>

        <template id="cpmkRowTemplate">
            <div class="grid gap-3 px-4 py-4 grid-cols-1 xl:grid-cols-[220px_1fr] items-start" data-cpmk-row data-row-index="__INDEX__">
                <div>
                    <label class="text-xs font-semibold text-slate-500">CPL</label>
                    <select name="cpmk_rows[__INDEX__][cpl_id]" class="form-control compact-control" data-cpmk-cpl-select required>
                        <option value="">Pilih CPL</option>
                    </select>
                    <div class="mt-4 rounded-xl bg-slate-50 px-3 py-2 text-xs text-slate-500 hidden xl:block" data-cpmk-preview>
                        Pratinjau CPMK akan muncul setelah field diisi.
                    </div>
                </div>

                <div>
                    <div class="grid gap-3 grid-cols-1 md:grid-cols-2">
                        <div>
                            <label class="text-xs font-semibold text-slate-500">Kode CPMK</label>
                            <div class="flex items-stretch gap-2">
                                <span class="inline-flex items-center rounded-xl border border-slate-200 bg-slate-50 px-3 text-sm font-semibold text-slate-500">CPMK</span>
                                <input type="text" name="cpmk_rows[__INDEX__][kode]" class="form-control compact-control" placeholder="1" required>
                            </div>
                        </div>

                        <div>
                            <label class="text-xs font-semibold text-slate-500">KKO</label>
                            <select name="cpmk_rows[__INDEX__][kko]" class="form-control compact-control" required>
                                <option value="">Pilih KKO</option>
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $kkoOptions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $value => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                                    <option value="<?php echo e($value); ?>"><?php echo e($value); ?> - <?php echo e($label); ?></option>
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                            </select>
                        </div>
                    </div>

                    <div class="mt-3 grid gap-3 grid-cols-1 md:grid-cols-2">
                        <div>
                            <label class="text-xs font-semibold text-slate-500">Objek</label>
                            <input type="text" name="cpmk_rows[__INDEX__][objek]" class="form-control compact-control" placeholder="contoh: merancang sistem IoT" required>
                        </div>

                        <div>
                            <label class="text-xs font-semibold text-slate-500">Konteks</label>
                            <input type="text" name="cpmk_rows[__INDEX__][konteks]" class="form-control compact-control" placeholder="contoh: sesuai kebutuhan pengguna">
                        </div>
                    </div>

                    <div class="mt-3 flex items-center justify-between gap-3">
                        <div class="rounded-xl bg-slate-50 px-3 py-2 text-xs text-slate-500 block xl:hidden" data-cpmk-preview>
                            Pratinjau CPMK akan muncul setelah field diisi.
                        </div>
                        <button type="button" class="inline-flex h-11 w-11 items-center justify-center rounded-xl border border-rose-200 text-rose-500 hover:bg-rose-50 ml-auto" data-remove-cpmk-row aria-label="Hapus baris CPMK">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                </div>
            </div>
        </template>

        <!-- File Upload -->
        <div class="form-group">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.5rem;">
                <label class="form-label form-label-required">Dokumen RPS</label>
                <a href="<?php echo e(route('rps.template.download')); ?>" target="_blank" style="color: #2563eb; text-decoration: none; font-size: 0.875rem; display: flex; align-items: center; gap: 0.25rem;" title="Download template RPS">
                    <i class="fas fa-download"></i> Download Template
                </a>
            </div>
            <label class="upload-zone <?php echo e(!$isUploadOpen ? 'upload-zone-closed' : ''); ?>" id="uploadZone">
                <input type="file" name="dokumen" accept=".pdf" id="fileInput" required <?php echo e(!$isUploadOpen ? 'disabled' : ''); ?>>
                <i class="fas fa-cloud-upload-alt <?php echo e(!$isUploadOpen ? 'upload-icon-closed' : ''); ?>" id="uploadIcon"></i>
                <strong id="uploadText" class="<?php echo e(!$isUploadOpen ? 'upload-text-closed' : ''); ?>">
                    <?php echo e(!$isUploadOpen ? 'Upload ditutup' : 'Klik untuk unggah atau seret file ke sini'); ?>

                </strong>
                <span id="uploadSub">PDF (Maks. 1MB)</span>
            </label>
        </div>

        <?php if (! $__env->hasRenderedOnce('3918b126-4a2d-4bb3-ab95-e4359852ba02')): $__env->markAsRenderedOnce('3918b126-4a2d-4bb3-ab95-e4359852ba02'); ?>
            <style>
                .upload-zone {
                    border: 2px dashed #cbd5e1;
                    border-radius: 12px;
                    padding: 36px 20px;
                    text-align: center;
                    cursor: pointer;
                    transition: border-color 0.2s, background-color 0.2s;
                    background: #f8fafc;
                    display: block;
                }

                .upload-zone:hover {
                    border-color: #111827;
                    background: #f1f5f9;
                }

                .upload-zone i {
                    font-size: 32px;
                    color: #111827;
                    margin-bottom: 10px;
                    display: block;
                }

                .upload-zone strong {
                    font-size: 14px;
                    font-weight: 600;
                    color: #111827;
                    display: block;
                    margin-bottom: 4px;
                }

                .upload-zone span {
                    font-size: 12px;
                    color: #64748b;
                }

                .upload-zone input {
                    display: none;
                }

                .upload-zone-closed {
                    background-color: #f5f5fa;
                    border-color: #dfdfe6;
                    cursor: not-allowed;
                    opacity: 0.7;
                }

                .upload-icon-closed {
                    color: #ababba;
                }

                .upload-text-closed {
                    color: #6e6e83;
                }

                .compact-control {
                    height: auto;
                    min-height: 44px;
                    padding: 8px 12px;
                }
            </style>
        <?php endif; ?>

        <!-- Form Actions -->
        <div class="flex justify-end gap-3 pt-4 border-t border-slate-200">
            <button type="button" class="btn-secondary" onclick="if (window.closeRpsUploadModal) { window.closeRpsUploadModal(); } else { history.back(); }">Batal</button>
            <button type="submit" class="btn-primary" id="submitBtn" <?php echo e(!$isUploadOpen ? 'disabled' : ''); ?>>
                <i class="fas fa-floppy-disk"></i> Simpan RPS
            </button>
        </div>
    </form>
</div>
<?php /**PATH C:\WebsiteTekkom - Copy\Modules\BankSoal\resources\views\components\ui\rps-form.blade.php ENDPATH**/ ?>