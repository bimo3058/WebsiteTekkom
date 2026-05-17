<!-- RPS Form Component -->
@php
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
@endphp

<div class="card mb-8">
    <div class="card-header flex items-center justify-between gap-4">
        <div>
            <h2 class="text-lg font-semibold text-slate-900">Formulir Rencana Pembelajaran</h2>
            <p class="text-sm text-slate-500 mt-1">Setiap baris CPMK akan disimpan ke <span class="font-semibold">bs_cpmk</span> dan dipasangkan dengan CPL per baris.</p>
        </div>
    </div>

    <form action="{{ route('banksoal.rps.dosen.store') }}" method="POST" enctype="multipart/form-data" class="p-4 space-y-6"
        data-route-cpl="{{ route('banksoal.rps.dosen.cpl') }}"
        data-route-dosen="{{ route('banksoal.rps.dosen.dosen') }}"
        data-cpmk-row-builder="1">
        @csrf

        <!-- Row 1: Mata Kuliah & Dosen Lain -->
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div class="form-group-compact">
                <label class="form-label form-label-required">Mata Kuliah</label>
                <select name="mata_kuliah_id" id="mkSelect" class="form-control compact-control" required {{ !$isUploadOpen ? 'disabled' : '' }}>
                    <option value="" disabled selected>Pilih Mata Kuliah</option>
                    @foreach ($mataKuliahs as $mk)
                        <option value="{{ $mk->id }}">{{ $mk->kode }} - {{ $mk->nama }} ({{ $mk->sks }} SKS)</option>
                    @endforeach
                </select>
            </div>

            <div class="form-group-compact">
                <label class="form-label">Dosen Pengampu Lain</label>
                <select name="dosen_lain[]" id="dosenSelect" class="form-control compact-control" multiple {{ !$isUploadOpen ? 'disabled' : '' }}></select>
                <small class="form-hint">Pilih satu atau lebih dosen pengampu tambahan.</small>
            </div>
        </div>

        <!-- Row 2: Semester & Tahun Ajaran -->
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div class="form-group-compact">
                <label class="form-label">Semester</label>
                <select name="semester" id="semester" class="form-control compact-control" {{ !$isUploadOpen ? 'disabled' : '' }}>
                    <option value="Ganjil" {{ $semester == 'Ganjil' ? 'selected' : '' }}>Ganjil</option>
                    <option value="Genap" {{ $semester == 'Genap' ? 'selected' : '' }}>Genap</option>
                </select>
            </div>

            <div class="form-group-compact">
                <label class="form-label">Tahun Ajaran</label>
                <select name="tahun_ajaran" id="tahun_ajaran" class="form-control compact-control" {{ !$isUploadOpen ? 'disabled' : '' }}>
                    @foreach($tahunAjarans as $ta)
                        <option value="{{ $ta }}" {{ $ta == $academicYear ? 'selected' : '' }}>{{ $ta }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <!-- CPMK Rows -->
        <div class="rounded-2xl border border-slate-200 bg-white shadow-sm overflow-hidden">
            <div class="flex items-center justify-between gap-4 border-b border-slate-200 px-4 py-3">
                <div>
                    <h3 class="text-base font-semibold text-slate-900">CPMK per Baris</h3>
                    <p class="text-xs text-slate-500 mt-1">Format: CPMK 1.1 - Mahasiswa mampu (KKO C6) merancang ...</p>
                </div>
                <button type="button" class="btn-primary inline-flex items-center gap-2" id="addCpmkRowBtn" {{ !$isUploadOpen ? 'disabled' : '' }}>
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
                @foreach($cpmkRows as $index => $row)
                    <div class="grid gap-3 px-4 py-4 grid-cols-1 xl:grid-cols-[220px_1fr] items-start" data-cpmk-row data-row-index="{{ $index }}">
                        <div>
                            <label class="text-xs font-semibold text-slate-500">CPL</label>
                            <select name="cpmk_rows[{{ $index }}][cpl_id]" class="form-control compact-control" data-cpmk-cpl-select data-selected-value="{{ $row['cpl_id'] }}" required {{ !$isUploadOpen ? 'disabled' : '' }}>
                                <option value="">Pilih CPL</option>
                            </select>
                            @error('cpmk_rows.' . $index . '.cpl_id')
                                <p class="text-xs text-red-600">{{ $message }}</p>
                            @enderror

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
                                        <input type="text" name="cpmk_rows[{{ $index }}][kode]" value="{{ $row['kode'] }}" class="form-control compact-control" placeholder="1.1" required {{ !$isUploadOpen ? 'disabled' : '' }}>
                                    </div>
                                    @error('cpmk_rows.' . $index . '.kode')
                                        <p class="text-xs text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label class="text-xs font-semibold text-slate-500">KKO</label>
                                    <select name="cpmk_rows[{{ $index }}][kko]" class="form-control compact-control" required {{ !$isUploadOpen ? 'disabled' : '' }}>
                                        <option value="">Pilih KKO</option>
                                        @foreach($kkoOptions as $value => $label)
                                            <option value="{{ $value }}" {{ $row['kko'] === $value ? 'selected' : '' }}>{{ $value }} - {{ $label }}</option>
                                        @endforeach
                                    </select>
                                    @error('cpmk_rows.' . $index . '.kko')
                                        <p class="text-xs text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>

                            <div class="mt-3 grid gap-3 grid-cols-1 md:grid-cols-2">
                                <div>
                                    <label class="text-xs font-semibold text-slate-500">Objek</label>
                                    <input type="text" name="cpmk_rows[{{ $index }}][objek]" value="{{ $row['objek'] }}" class="form-control compact-control" placeholder="contoh: merancang sistem IoT" required {{ !$isUploadOpen ? 'disabled' : '' }}>
                                    @error('cpmk_rows.' . $index . '.objek')
                                        <p class="text-xs text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label class="text-xs font-semibold text-slate-500">Konteks</label>
                                    <input type="text" name="cpmk_rows[{{ $index }}][konteks]" value="{{ $row['konteks'] }}" class="form-control compact-control" placeholder="contoh: sesuai kebutuhan pengguna" {{ !$isUploadOpen ? 'disabled' : '' }}>
                                    @error('cpmk_rows.' . $index . '.konteks')
                                        <p class="text-xs text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>

                            <div class="mt-3 flex justify-end">
                                <button type="button" class="inline-flex h-11 w-11 items-center justify-center rounded-xl border border-rose-200 text-rose-500 hover:bg-rose-50" data-remove-cpmk-row aria-label="Hapus baris CPMK" {{ !$isUploadOpen ? 'disabled' : '' }}>
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>

                            <div class="mt-3 rounded-xl bg-slate-50 px-3 py-2 text-xs text-slate-500 block xl:hidden" data-cpmk-preview>
                                Pratinjau CPMK akan muncul setelah field diisi.
                            </div>
                        </div>
                    </div>
                @endforeach
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
                                <input type="text" name="cpmk_rows[__INDEX__][kode]" class="form-control compact-control" placeholder="1.1" required>
                            </div>
                        </div>

                        <div>
                            <label class="text-xs font-semibold text-slate-500">KKO</label>
                            <select name="cpmk_rows[__INDEX__][kko]" class="form-control compact-control" required>
                                <option value="">Pilih KKO</option>
                                @foreach($kkoOptions as $value => $label)
                                    <option value="{{ $value }}">{{ $value }} - {{ $label }}</option>
                                @endforeach
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
                <a href="{{ route('rps.template.download') }}" target="_blank" style="color: #2563eb; text-decoration: none; font-size: 0.875rem; display: flex; align-items: center; gap: 0.25rem;" title="Download template RPS">
                    <i class="fas fa-download"></i> Download Template
                </a>
            </div>
            <label class="upload-zone {{ !$isUploadOpen ? 'upload-zone-closed' : '' }}" id="uploadZone">
                <input type="file" name="dokumen" accept=".pdf" id="fileInput" required {{ !$isUploadOpen ? 'disabled' : '' }}>
                <i class="fas fa-cloud-upload-alt {{ !$isUploadOpen ? 'upload-icon-closed' : '' }}" id="uploadIcon"></i>
                <strong id="uploadText" class="{{ !$isUploadOpen ? 'upload-text-closed' : '' }}">
                    {{ !$isUploadOpen ? 'Upload ditutup' : 'Klik untuk unggah atau seret file ke sini' }}
                </strong>
                <span id="uploadSub">PDF (Maks. 1MB)</span>
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
            <button type="button" class="btn-secondary" onclick="if (window.closeRpsUploadModal) { window.closeRpsUploadModal(); } else { history.back(); }">Batal</button>
            <button type="submit" class="btn-primary" id="submitBtn" {{ !$isUploadOpen ? 'disabled' : '' }}>
                <i class="fas fa-floppy-disk"></i> Simpan RPS
            </button>
        </div>
    </form>
</div>
