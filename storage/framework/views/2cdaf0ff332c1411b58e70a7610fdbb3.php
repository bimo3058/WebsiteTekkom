<!-- RPS Edit Modal Form Partial -->
<div class="card mb-8">
    <div class="card-header">
        <h2 class="text-lg font-semibold text-slate-900">Revisi Rencana Pembelajaran Semester</h2>
        <p class="text-sm text-slate-600 mt-1">Status: <span class="badge <?php echo e(match($rps->status->value) {
            'diajukan' => 'badge-warning',
            'revisi' => 'badge-danger',
            'disetujui' => 'badge-success',
            default => 'badge-secondary'
        }); ?>"><?php echo e($rps->status->label()); ?></span></p>
    </div>

    <form action="<?php echo e(route('banksoal.rps.dosen.update', $rps->id)); ?>" method="POST" enctype="multipart/form-data" class="p-4 space-y-6"
        data-route-dosen="<?php echo e(route('banksoal.rps.dosen.dosen')); ?>"
        data-route-cpl="<?php echo e(route('banksoal.rps.dosen.cpl')); ?>"
        data-route-cpmk="<?php echo e(route('banksoal.rps.dosen.cpmk')); ?>"
        data-route-cpmk-by-rps="<?php echo e(route('banksoal.rps.dosen.cpmk-by-rps', $rps->id)); ?>"
        data-edit-mode="1"
        data-rps-id="<?php echo e($rps->id); ?>"
        data-selected-dosen-ids='<?php echo e(json_encode($selectedDosenIds)); ?>'
        data-selected-cpl-ids='<?php echo e(json_encode($selectedCplIds)); ?>'
        data-selected-cpmk-ids='<?php echo e(json_encode($selectedCpmkIds)); ?>'
        >
        <?php echo csrf_field(); ?>
        <?php echo method_field('PUT'); ?>

        <!-- Row 1: Mata Kuliah & Dosen Lain -->
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div class="form-group-compact">
                <label class="form-label form-label-required">Mata Kuliah</label>
                <select name="mata_kuliah_id" id="mkSelect" class="form-control compact-control" required <?php echo e(($rps->status->value === 'revisi' || !$isUploadOpen) ? 'disabled' : ''); ?>>
                    <option value="" disabled>Pilih Mata Kuliah</option>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $mataKuliahs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $mk): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                        <option value="<?php echo e($mk->id); ?>" <?php echo e($mk->id == $rps->mk_id ? 'selected' : ''); ?>>
                            <?php echo e($mk->kode); ?> - <?php echo e($mk->nama); ?> (<?php echo e($mk->sks); ?> SKS)
                        </option>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                </select>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($rps->status->value === 'revisi'): ?>
                    <input type="hidden" name="mata_kuliah_id" value="<?php echo e($rps->mk_id); ?>">
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($rps->status->value === 'revisi'): ?>
                    <small class="form-hint">Mata kuliah dikunci saat revisi.</small>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['mata_kuliah_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                    <p class="text-red-600 text-sm mt-1"><?php echo e($message); ?></p>
                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </div>

            <div class="form-group-compact">
                <label class="form-label">Dosen Pengampu Lain</label>
                <select name="dosen_lain[]" id="dosenSelect" class="form-control compact-control" multiple <?php echo e(!$isUploadOpen ? 'disabled' : ''); ?>>
                </select>
                <small class="form-hint">Data dosen lama sudah dimuat. Tambah atau hapus jika perlu.</small>
            </div>
        </div>

        <!-- Row 2: Semester & Tahun Ajaran -->
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div class="form-group-compact">
                <label class="form-label">Semester</label>
                <select name="semester" id="semester" class="form-control compact-control" <?php echo e(!$isUploadOpen ? 'disabled' : ''); ?>>
                    <option value="Ganjil" <?php echo e($rps->semester == 'Ganjil' ? 'selected' : ''); ?>>Ganjil</option>
                    <option value="Genap" <?php echo e($rps->semester == 'Genap' ? 'selected' : ''); ?>>Genap</option>
                </select>
            </div>

            <div class="form-group-compact">
                <label class="form-label">Tahun Ajaran</label>
                <select name="tahun_ajaran" id="tahun_ajaran" class="form-control compact-control" <?php echo e(!$isUploadOpen ? 'disabled' : ''); ?>>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $tahunAjarans; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $ta): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                        <option value="<?php echo e($ta); ?>" <?php echo e($ta == $rps->tahun_ajaran ? 'selected' : ''); ?>><?php echo e($ta); ?></option>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                </select>
            </div>
        </div>

        <!-- CPL (Full Width) - Multiselect -->
        <div class="form-group">
            <label class="form-label form-label-required">Capaian Pembelajaran Lulusan (CPL)</label>
            <select name="cpl_id[]" id="cplSelect" class="form-control" multiple required <?php echo e(!$isUploadOpen ? 'disabled' : ''); ?>>
            </select>
            <small class="form-hint">Data CPL lama sudah dimuat. Tambah atau hapus jika perlu.</small>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['cpl_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                <p class="text-red-600 text-sm mt-1"><?php echo e($message); ?></p>
            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </div>

        <!-- CPMK (Full Width) - Multiselect -->
        <div class="form-group">
            <label class="form-label form-label-required">Capaian Pembelajaran Mata Kuliah (CPMK)</label>
            <select name="cpmk_id[]" id="cpmkSelect" class="form-control" multiple required <?php echo e(!$isUploadOpen ? 'disabled' : ''); ?>>
            </select>
            <small class="form-hint">Data CPMK lama sudah dimuat. Tambah atau hapus jika perlu.</small>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['cpmk_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                <p class="text-red-600 text-sm mt-1"><?php echo e($message); ?></p>
            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </div>

        <div class="form-group">
            <label class="form-label form-label-required">Catatan Revisi</label>
            <textarea
                name="catatan"
                id="catatan"
                class="form-control"
                rows="4"
                placeholder="Jelaskan bagian RPS yang diubah, misalnya CPL, CPMK, dosen pengampu, atau dokumen yang diperbarui."
                <?php echo e(!$isUploadOpen ? 'disabled' : ''); ?>

                <?php echo e($rps->status->value === 'revisi' ? 'required' : ''); ?>

            ><?php echo e(old('catatan', $rps->catatan ?? '')); ?></textarea>
            <small class="form-hint">Catatan ini akan tersimpan di detail RPS dan audit log.</small>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['catatan'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                <p class="text-red-600 text-sm mt-1"><?php echo e($message); ?></p>
            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </div>

        <!-- File Upload -->
        <div class="form-group">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.5rem;">
                <label class="form-label">Dokumen RPS</label>
                <a href="<?php echo e(route('rps.template.download')); ?>" target="_blank" style="color: #2563eb; text-decoration: none; font-size: 0.875rem; display: flex; align-items: center; gap: 0.25rem;" title="Download template RPS">
                    <i class="fas fa-download"></i> Download Template
                </a>
            </div>
            <label class="upload-zone <?php echo e(!$isUploadOpen ? 'upload-zone-closed' : ''); ?>" id="uploadZone">
                <input type="file" name="dokumen" accept=".pdf" id="fileInput" <?php echo e((!$isUploadOpen || $rps->status->value === 'revisi') ? 'required' : ''); ?> <?php echo e(!$isUploadOpen ? 'disabled' : ''); ?>>
                <i class="fas fa-cloud-upload-alt <?php echo e(!$isUploadOpen ? 'upload-icon-closed' : ''); ?>" id="uploadIcon"></i>
                <strong id="uploadText" class="<?php echo e(!$isUploadOpen ? 'upload-text-closed' : ''); ?>">
                    <?php echo e(!$isUploadOpen ? 'Upload ditutup' : ($rps->status->value === 'revisi' ? 'Upload file revisi baru' : 'Klik untuk unggah atau seret file ke sini')); ?>

                </strong>
                <span id="uploadSub">PDF (Maks. 1MB) - File lama akan diganti jika ada</span>
            </label>
        </div>

        <?php if (! $__env->hasRenderedOnce('7bcf8ae0-d987-44cb-b28c-6ed5cb251a06')): $__env->markAsRenderedOnce('7bcf8ae0-d987-44cb-b28c-6ed5cb251a06'); ?>
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
            <button type="button" class="btn-secondary" onclick="if (window.closeRpsEditModal) { window.closeRpsEditModal(); } else { history.back(); }">Batal</button>
            <button type="submit" class="btn-primary" id="submitBtn" <?php echo e(!$isUploadOpen ? 'disabled' : ''); ?>>
                <i class="fas fa-save"></i> Simpan Perubahan
            </button>
        </div>
    </form>

    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($rps->status->value === 'revisi'): ?>
        <div class="mt-4 p-4 bg-amber-50 border border-amber-200 rounded-lg">
            <p class="text-sm text-amber-800">
                <i class="fas fa-exclamation-circle mr-2"></i>
                <strong>Status Revisi:</strong> RPS Anda dikembalikan untuk revisi. Silakan perbaiki sesuai masukan dan submit kembali.
            </p>
        </div>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
</div>


<?php /**PATH C:\WebsiteTekkom - Copy\Modules\BankSoal\resources\views\partials\dosen\rps-edit-modal-form.blade.php ENDPATH**/ ?>