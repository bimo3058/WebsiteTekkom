<!-- RPS Edit Modal Form Partial -->
<div class="card mb-8">
    <div class="card-header">
        <h2 class="text-lg font-semibold text-slate-900">Revisi Rencana Pembelajaran Semester</h2>
        <p class="text-sm text-slate-600 mt-1">Status: <span class="badge {{ match($rps->status->value) {
            'diajukan' => 'badge-warning',
            'revisi' => 'badge-danger',
            'disetujui' => 'badge-success',
            default => 'badge-secondary'
        } }}">{{ $rps->status->label() }}</span></p>
    </div>

    <form action="{{ route('banksoal.rps.dosen.update', $rps->id) }}" method="POST" enctype="multipart/form-data" class="p-4 space-y-6"
        data-route-dosen="{{ route('banksoal.rps.dosen.dosen') }}"
        data-route-cpl="{{ route('banksoal.rps.dosen.cpl') }}"
        data-route-cpmk="{{ route('banksoal.rps.dosen.cpmk') }}"
        data-route-cpmk-by-rps="{{ route('banksoal.rps.dosen.cpmk-by-rps', $rps->id) }}"
        data-edit-mode="1"
        data-rps-id="{{ $rps->id }}"
        data-selected-dosen-ids='{{ json_encode($selectedDosenIds) }}'
        data-selected-cpl-ids='{{ json_encode($selectedCplIds) }}'
        data-selected-cpmk-ids='{{ json_encode($selectedCpmkIds) }}'
        >
        @csrf
        @method('PUT')

        <!-- Row 1: Mata Kuliah & Dosen Lain -->
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div class="form-group-compact">
                <label class="form-label form-label-required">Mata Kuliah</label>
                <select name="mata_kuliah_id" id="mkSelect" class="form-control compact-control" required {{ ($rps->status->value === 'revisi' || !$isUploadOpen) ? 'disabled' : '' }}>
                    <option value="" disabled>Pilih Mata Kuliah</option>
                    @foreach ($mataKuliahs as $mk)
                        <option value="{{ $mk->id }}" {{ $mk->id == $rps->mk_id ? 'selected' : '' }}>
                            {{ $mk->kode }} - {{ $mk->nama }} ({{ $mk->sks }} SKS)
                        </option>
                    @endforeach
                </select>
                @if($rps->status->value === 'revisi')
                    <input type="hidden" name="mata_kuliah_id" value="{{ $rps->mk_id }}">
                @endif
                @if($rps->status->value === 'revisi')
                    <small class="form-hint">Mata kuliah dikunci saat revisi.</small>
                @endif
                @error('mata_kuliah_id')
                    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="form-group-compact">
                <label class="form-label">Dosen Pengampu Lain</label>
                <select name="dosen_lain[]" id="dosenSelect" class="form-control compact-control" multiple {{ !$isUploadOpen ? 'disabled' : '' }}>
                </select>
                <small class="form-hint">Data dosen lama sudah dimuat. Tambah atau hapus jika perlu.</small>
            </div>
        </div>

        <!-- Row 2: Semester & Tahun Ajaran -->
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div class="form-group-compact">
                <label class="form-label">Semester</label>
                <select name="semester" id="semester" class="form-control compact-control" {{ !$isUploadOpen ? 'disabled' : '' }}>
                    <option value="Ganjil" {{ $rps->semester == 'Ganjil' ? 'selected' : '' }}>Ganjil</option>
                    <option value="Genap" {{ $rps->semester == 'Genap' ? 'selected' : '' }}>Genap</option>
                </select>
            </div>

            <div class="form-group-compact">
                <label class="form-label">Tahun Ajaran</label>
                <select name="tahun_ajaran" id="tahun_ajaran" class="form-control compact-control" {{ !$isUploadOpen ? 'disabled' : '' }}>
                    @foreach($tahunAjarans as $ta)
                        <option value="{{ $ta }}" {{ $ta == $rps->tahun_ajaran ? 'selected' : '' }}>{{ $ta }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <!-- CPL (Full Width) - Multiselect -->
        <div class="form-group">
            <label class="form-label form-label-required">Capaian Pembelajaran Lulusan (CPL)</label>
            <select name="cpl_id[]" id="cplSelect" class="form-control" multiple required {{ !$isUploadOpen ? 'disabled' : '' }}>
            </select>
            <small class="form-hint">Data CPL lama sudah dimuat. Tambah atau hapus jika perlu.</small>
            @error('cpl_id')
                <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>

        <!-- CPMK (Full Width) - Multiselect -->
        <div class="form-group">
            <label class="form-label form-label-required">Capaian Pembelajaran Mata Kuliah (CPMK)</label>
            <select name="cpmk_id[]" id="cpmkSelect" class="form-control" multiple required {{ !$isUploadOpen ? 'disabled' : '' }}>
            </select>
            <small class="form-hint">Data CPMK lama sudah dimuat. Tambah atau hapus jika perlu.</small>
            @error('cpmk_id')
                <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>

        <div class="form-group">
            <label class="form-label form-label-required">Catatan Revisi</label>
            <textarea
                name="catatan"
                id="catatan"
                class="form-control"
                rows="4"
                placeholder="Jelaskan bagian RPS yang diubah, misalnya CPL, CPMK, dosen pengampu, atau dokumen yang diperbarui."
                {{ !$isUploadOpen ? 'disabled' : '' }}
                {{ $rps->status->value === 'revisi' ? 'required' : '' }}
            >{{ old('catatan', $rps->catatan ?? '') }}</textarea>
            <small class="form-hint">Catatan ini akan tersimpan di detail RPS dan audit log.</small>
            @error('catatan')
                <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>

        <!-- File Upload -->
        <div class="form-group">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.5rem;">
                <label class="form-label">Dokumen RPS</label>
                <a href="{{ route('rps.template.download') }}" target="_blank" style="color: #2563eb; text-decoration: none; font-size: 0.875rem; display: flex; align-items: center; gap: 0.25rem;" title="Download template RPS">
                    <i class="fas fa-download"></i> Download Template
                </a>
            </div>
            <label class="upload-zone {{ !$isUploadOpen ? 'upload-zone-closed' : '' }}" id="uploadZone">
                <input type="file" name="dokumen" accept=".pdf" id="fileInput" {{ (!$isUploadOpen || $rps->status->value === 'revisi') ? 'required' : '' }} {{ !$isUploadOpen ? 'disabled' : '' }}>
                <i class="fas fa-cloud-upload-alt {{ !$isUploadOpen ? 'upload-icon-closed' : '' }}" id="uploadIcon"></i>
                <strong id="uploadText" class="{{ !$isUploadOpen ? 'upload-text-closed' : '' }}">
                    {{ !$isUploadOpen ? 'Upload ditutup' : ($rps->status->value === 'revisi' ? 'Upload file revisi baru' : 'Klik untuk unggah atau seret file ke sini') }}
                </strong>
                <span id="uploadSub">PDF (Maks. 1MB) - File lama akan diganti jika ada</span>
            </label>
        </div>

        @once
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
        @endonce

        <!-- Form Actions -->
        <div class="flex justify-end gap-3 pt-4 border-t border-slate-200">
            <button type="button" class="btn-secondary" onclick="if (window.closeRpsEditModal) { window.closeRpsEditModal(); } else { history.back(); }">Batal</button>
            <button type="submit" class="btn-primary" id="submitBtn" {{ !$isUploadOpen ? 'disabled' : '' }}>
                <i class="fas fa-save"></i> Simpan Perubahan
            </button>
        </div>
    </form>

    @if($rps->status->value === 'revisi')
        <div class="mt-4 p-4 bg-amber-50 border border-amber-200 rounded-lg">
            <p class="text-sm text-amber-800">
                <i class="fas fa-exclamation-circle mr-2"></i>
                <strong>Status Revisi:</strong> RPS Anda dikembalikan untuk revisi. Silakan perbaiki sesuai masukan dan submit kembali.
            </p>
        </div>
    @endif
</div>


