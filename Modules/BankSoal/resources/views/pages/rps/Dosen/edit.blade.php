<x-banksoal::layouts.dosen-admin>
    @section('breadcrumbs')
        <a href="{{ route('banksoal.rps.dosen.index') }}" class="text-slate-500 hover:text-primary transition-colors">Manajemen RPS</a>
        <span class="mx-2 text-slate-300">/</span>
        <span class="text-slate-800 font-semibold">Edit RPS</span>
    @endsection
    @push('styles')
    <style>
        :root {
            --primary-blue: rgb(11, 38, 110);
            --primary-hover: rgb(8, 28, 82);
            --danger-red: #ef4444;
            --slate-50: #f8fafc; --slate-100: #f1f5f9; --slate-200: #e2e8f0;
            --slate-300: #cbd5e1; --slate-400: #94a3b8; --slate-500: #64748b;
            --slate-600: #475569; --slate-700: #334155; --slate-800: #1e293b;
        }
        * { box-sizing: border-box; }

        .page-header { display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 24px; gap: 16px; flex-wrap: wrap; }
        .header-content h1 { font-size: 26px; font-weight: 700; color: var(--slate-800); margin: 0; letter-spacing: -0.5px; }
        .header-content p { font-size: 14px; color: var(--slate-500); margin: 6px 0 0 0; }

        .form-card { background: #fff; border: 1px solid var(--slate-200); border-radius: 14px; padding: 28px; box-shadow: 0 1px 4px rgba(0,0,0,.05); margin-bottom: 24px; }
        .form-card-title { font-size: 16px; font-weight: 700; color: var(--slate-800); margin: 0 0 20px 0; padding-bottom: 14px; border-bottom: 1px solid var(--slate-100); display: flex; align-items: center; gap: 8px; }
        .form-card-title i { color: var(--primary-blue); }

        .form-row { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 20px; }
        @media (max-width: 640px) { .form-row { grid-template-columns: 1fr; } }
        .form-group { margin-bottom: 0; }
        .form-group-full { margin-bottom: 20px; }

        label.field-label { display: block; margin-bottom: 7px; font-size: 13px; font-weight: 600; color: var(--slate-700); }
        label.field-label .req { color: var(--danger-red); margin-left: 2px; }

        .field-control { width: 100%; padding: 10px 13px; border: 1px solid var(--slate-300); border-radius: 9px; font-size: 14px; color: var(--slate-800); background: #fff; transition: border-color .2s, box-shadow .2s; }
        .field-control:focus { outline: none; border-color: var(--primary-blue); box-shadow: 0 0 0 3px rgba(11,38,110,.1); }
        .field-control:disabled { background: var(--slate-50); color: var(--slate-400); cursor: not-allowed; }
        .field-control select { appearance: none; }

        .field-hint { font-size: 12px; color: var(--slate-500); margin-top: 5px; }
        .field-error { font-size: 12px; color: var(--danger-red); margin-top: 5px; }

        /* CPMK Table Section */
        .cpmk-section { border: 1px solid var(--slate-200); border-radius: 12px; overflow: hidden; margin-bottom: 20px; }
        .cpmk-header { display: flex; align-items: center; justify-content: space-between; padding: 14px 18px; background: var(--slate-50); border-bottom: 1px solid var(--slate-200); }
        .cpmk-header h3 { font-size: 14px; font-weight: 700; color: var(--slate-800); margin: 0; }
        .cpmk-header p { font-size: 12px; color: var(--slate-500); margin: 3px 0 0 0; }
        .cpmk-col-head { display: grid; grid-template-columns: 190px 130px 150px 1fr 1fr 44px; gap: 10px; padding: 10px 18px; background: #f1f5f9; border-bottom: 1px solid var(--slate-200); font-size: 11px; font-weight: 600; text-transform: uppercase; letter-spacing: .05em; color: var(--slate-500); }
        @media(max-width:900px){ .cpmk-col-head { display: none; } }
        .cpmk-rows { }
        .cpmk-row { display: grid; grid-template-columns: 190px 130px 150px 1fr 1fr 44px; gap: 10px; align-items: start; padding: 14px 18px; border-bottom: 1px solid var(--slate-100); }
        @media(max-width:900px){ .cpmk-row { grid-template-columns: 1fr; } }
        .cpmk-row:last-child { border-bottom: none; }
        .cpmk-preview { font-size: 11px; color: var(--slate-500); background: var(--slate-50); border-radius: 8px; padding: 7px 10px; margin-top: 6px; line-height: 1.5; }

        /* Upload Zone */
        .upload-zone { border: 2px dashed var(--slate-300); border-radius: 12px; padding: 36px 20px; text-align: center; cursor: pointer; transition: border-color .2s, background .2s; background: var(--slate-50); display: block; position: relative; }
        .upload-zone:hover { border-color: var(--primary-blue); background: rgba(11,38,110,.02); }
        .upload-zone.closed { background: #f5f5fa; border-color: #dfdfe6; cursor: not-allowed; opacity: .7; }
        .upload-zone input[type=file] { position: absolute; inset: 0; opacity: 0; cursor: pointer; width: 100%; height: 100%; }
        .upload-zone.closed input[type=file] { pointer-events: none; }
        .upload-zone i { font-size: 32px; color: var(--slate-400); display: block; margin-bottom: 10px; }
        .upload-zone strong { font-size: 14px; font-weight: 600; color: var(--slate-700); display: block; margin-bottom: 4px; }
        .upload-zone span { font-size: 12px; color: var(--slate-500); }

        /* Buttons */
        .btn { padding: 10px 22px; border-radius: 9px; border: none; font-weight: 600; font-size: 14px; cursor: pointer; transition: all .2s; display: inline-flex; align-items: center; gap: 6px; text-decoration: none; }
        .btn-primary { background: var(--primary-blue); color: #fff; box-shadow: 0 2px 4px rgba(11,38,110,.15); }
        .btn-primary:hover:not(:disabled) { background: var(--primary-hover); transform: translateY(-1px); box-shadow: 0 4px 8px rgba(11,38,110,.2); }
        .btn-primary:disabled { background: var(--slate-300); cursor: not-allowed; transform: none; box-shadow: none; }
        .btn-secondary { background: #fff; color: var(--slate-700); border: 1px solid var(--slate-300); }
        .btn-secondary:hover { background: var(--slate-50); border-color: var(--slate-400); }
        .btn-danger-sm { height: 40px; width: 40px; background: none; border: 1px solid #fecaca; border-radius: 9px; color: #ef4444; cursor: pointer; transition: all .2s; display: flex; align-items: center; justify-content: center; }
        .btn-danger-sm:hover { background: #fff1f2; }

        .form-actions { display: flex; justify-content: flex-end; gap: 12px; padding-top: 20px; border-top: 1px solid var(--slate-100); margin-top: 8px; }

        .alert-closed { display: flex; align-items: flex-start; gap: 12px; background: #fff7ed; border: 1px solid #fed7aa; border-radius: 10px; padding: 14px 16px; margin-bottom: 20px; font-size: 13px; color: #9a3412; }
        .alert-closed i { font-size: 16px; flex-shrink: 0; margin-top: 1px; }

        .mk-badge { display: inline-flex; align-items: center; gap: 8px; background: var(--slate-100); border: 1px solid var(--slate-200); border-radius: 8px; padding: 10px 14px; font-size: 14px; font-weight: 600; color: var(--slate-700); width: 100%; }
        .mk-badge i { color: var(--primary-blue); }
    </style>
    @endpush

    @section('breadcrumbs')
    <a href="{{ route('banksoal.rps.dosen.index') }}" class="text-slate-500 hover:text-primary transition-colors">Manajemen RPS</a>
    <span class="mx-2 text-slate-300">/</span>
    <span class="text-slate-800 font-semibold">Revisi RPS</span>
    @endsection

    <div class="page-header">
        <div class="header-content">
            <h1>Revisi RPS</h1>
            <p>Perbarui Rencana Pembelajaran Semester. Status saat ini: <span class="badge {{ match($rps->status->value) {
                'diajukan' => 'bg-amber-100 text-amber-800 border-amber-200',
                'revisi' => 'bg-rose-100 text-rose-800 border-rose-200',
                'disetujui' => 'bg-emerald-100 text-emerald-800 border-emerald-200',
                default => 'bg-slate-100 text-slate-800 border-slate-200'
            } }}" style="padding: 2px 8px; border-radius: 999px; font-size: 12px; font-weight: 600;">{{ $rps->status->label() }}</span></p>
        </div>
        <a href="{{ route('banksoal.rps.dosen.index') }}" class="btn btn-secondary">
            <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            Kembali
        </a>
    </div>

    @if(!$isUploadOpen)
    <div class="alert-closed">
        <i class="fas fa-lock"></i>
        <div>
            <strong>Periode upload RPS saat ini tidak aktif.</strong> Anda tidak dapat menyimpan perubahan.
        </div>
    </div>
    @endif

    @if($rps->status->value === 'revisi')
        <div style="display:flex;align-items:flex-start;gap:12px;background:#fffbeb;border:1px solid #fde68a;border-radius:10px;padding:14px 16px;margin-bottom:20px;font-size:13px;color:#92400e;">
            <i class="fas fa-exclamation-circle" style="font-size:16px;flex-shrink:0;margin-top:1px;"></i>
            <div>
                <strong>Status Revisi:</strong> RPS Anda dikembalikan untuk revisi. Silakan perbaiki sesuai masukan dan submit kembali.
            </div>
        </div>
    @endif

    @php
    $kkoOptions = [
        'C1'=>'Mengingat','C2'=>'Memahami','C3'=>'Menerapkan','C4'=>'Menganalisis','C5'=>'Mengevaluasi','C6'=>'Mencipta',
        'P1'=>'Meniru','P2'=>'Menyesuaikan','P3'=>'Membiasakan','P4'=>'Menguasai','P5'=>'Mahir',
        'A1'=>'Menerima','A2'=>'Merespon','A3'=>'Menilai','A4'=>'Mengorganisasi','A5'=>'Menghayati',
    ];
    @endphp

    <x-banksoal::notification.alerts />

    <form action="{{ route('banksoal.rps.dosen.update', $rps->id) }}" method="POST" enctype="multipart/form-data"
        id="rpsEditForm"
        data-route-cpl="{{ route('banksoal.rps.dosen.cpl') }}"
        data-route-dosen="{{ route('banksoal.rps.dosen.dosen') }}"
        data-selected-dosen-ids='{{ json_encode($selectedDosenIds) }}'
        data-cpmk-row-builder="1"
        data-edit-mode="1"
        data-rps-id="{{ $rps->id }}">
        @csrf
        @method('PUT')

        {{-- Informasi Mata Kuliah --}}
        <div class="form-card">
            <div class="form-card-title"><i class="fas fa-book-open"></i> Informasi Mata Kuliah &amp; Dosen</div>

            <div class="form-row">
                <div class="form-group">
                    <label class="field-label">Mata Kuliah</label>
                    <div class="mk-badge">
                        <i class="fas fa-book"></i>
                        {{ $rps->mataKuliah->kode }} – {{ $rps->mataKuliah->nama }} ({{ $rps->mataKuliah->sks }} SKS)
                    </div>
                    <input type="hidden" id="mkSelect" name="mata_kuliah_id" value="{{ $rps->mk_id }}">
                    <p class="field-hint">Mata kuliah tidak dapat diubah pada saat revisi.</p>
                </div>

                <div class="form-group">
                    <label class="field-label">Dosen Pengampu Lain</label>
                    <select name="dosen_lain[]" id="dosenSelect" class="field-control" multiple data-selected-dosen-ids='{{ json_encode($selectedDosenIds) }}' {{ !$isUploadOpen ? 'disabled' : '' }}></select>
                    <p class="field-hint">Opsional. Pilih satu atau lebih dosen pengampu tambahan.</p>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label class="field-label">Semester <span class="req">*</span></label>
                    <select name="semester" id="semester" class="field-control" required {{ !$isUploadOpen ? 'disabled' : '' }}>
                        <option value="Ganjil" {{ old('semester', $rps->semester) == 'Ganjil' ? 'selected' : '' }}>Ganjil</option>
                        <option value="Genap"  {{ old('semester', $rps->semester) == 'Genap'  ? 'selected' : '' }}>Genap</option>
                    </select>
                </div>
                <div class="form-group">
                    <label class="field-label">Tahun Ajaran <span class="req">*</span></label>
                    <select name="tahun_ajaran" id="tahun_ajaran" class="field-control" required {{ !$isUploadOpen ? 'disabled' : '' }}>
                        @foreach($tahunAjarans as $ta)
                            <option value="{{ $ta }}" {{ old('tahun_ajaran', $rps->tahun_ajaran) == $ta ? 'selected' : '' }}>{{ $ta }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>

        {{-- CPMK Section --}}
        <div class="form-card">
            <div class="form-card-title" style="margin-bottom:16px;"><i class="fas fa-list-check"></i> Capaian Pembelajaran Mata Kuliah (CPMK)</div>

            <div class="cpmk-section">
                <div class="cpmk-header">
                    <div>
                        <h3>Baris CPMK</h3>
                        <p>Setiap baris akan disimpan sebagai satu CPMK. Format: <em>Mahasiswa mampu (KKO) objek [konteks]</em></p>
                    </div>
                    <button type="button" class="btn btn-primary" id="addCpmkRowBtn" style="padding:8px 16px;font-size:13px;" {{ !$isUploadOpen ? 'disabled' : '' }}>
                        <i class="fas fa-plus"></i> Tambah Baris
                    </button>
                </div>

                <div class="cpmk-col-head">
                    <div>CPL</div><div>Kode CPMK</div><div>KKO</div><div>Objek</div><div>Konteks</div><div></div>
                </div>

                <div id="cpmkRows" class="cpmk-rows" data-cpmk-rows>
                    @foreach($existingCpmkRows as $index => $row)
                    <div class="cpmk-row" data-cpmk-row data-row-index="{{ $index }}">
                        <div>
                            <label class="field-label" style="font-size:11px;">CPL <span class="req">*</span></label>
                            <select name="cpmk_rows[{{ $index }}][cpl_id]" class="field-control" style="font-size:13px;" data-cpmk-cpl-select data-selected-value="{{ $row['cpl_id'] ?? '' }}" required {{ !$isUploadOpen ? 'disabled' : '' }}>
                                <option value="">Pilih CPL</option>
                            </select>
                            @error('cpmk_rows.'.$index.'.cpl_id')<p class="field-error">{{ $message }}</p>@enderror
                            <div class="cpmk-preview" data-cpmk-preview>Pratinjau CPMK akan muncul setelah field diisi.</div>
                        </div>
                        <div>
                            <label class="field-label" style="font-size:11px;">Kode <span class="req">*</span></label>
                            <div style="display:flex;align-items:center;gap:6px;">
                                <span style="font-size:12px;font-weight:600;color:var(--slate-500);white-space:nowrap;padding:10px 8px;background:var(--slate-50);border:1px solid var(--slate-200);border-radius:8px;">CPMK</span>
                                <input type="text" name="cpmk_rows[{{ $index }}][kode]" value="{{ $row['kode'] ?? '' }}" class="field-control" style="font-size:13px;" placeholder="1.1" required {{ !$isUploadOpen ? 'disabled' : '' }}>
                            </div>
                            @error('cpmk_rows.'.$index.'.kode')<p class="field-error">{{ $message }}</p>@enderror
                        </div>
                        <div>
                            <label class="field-label" style="font-size:11px;">KKO <span class="req">*</span></label>
                            <select name="cpmk_rows[{{ $index }}][kko]" class="field-control" style="font-size:13px;" required {{ !$isUploadOpen ? 'disabled' : '' }}>
                                <option value="">Pilih KKO</option>
                                @foreach($kkoOptions as $val => $lbl)
                                    <option value="{{ $val }}" {{ ($row['kko'] ?? '') === $val ? 'selected' : '' }}>{{ $val }} – {{ $lbl }}</option>
                                @endforeach
                            </select>
                            @error('cpmk_rows.'.$index.'.kko')<p class="field-error">{{ $message }}</p>@enderror
                        </div>
                        <div>
                            <label class="field-label" style="font-size:11px;">Objek <span class="req">*</span></label>
                            <input type="text" name="cpmk_rows[{{ $index }}][objek]" value="{{ $row['objek'] ?? '' }}" class="field-control" style="font-size:13px;" placeholder="merancang sistem IoT" required {{ !$isUploadOpen ? 'disabled' : '' }}>
                            @error('cpmk_rows.'.$index.'.objek')<p class="field-error">{{ $message }}</p>@enderror
                        </div>
                        <div>
                            <label class="field-label" style="font-size:11px;">Konteks</label>
                            <input type="text" name="cpmk_rows[{{ $index }}][konteks]" value="{{ $row['konteks'] ?? '' }}" class="field-control" style="font-size:13px;" placeholder="sesuai kebutuhan pengguna" {{ !$isUploadOpen ? 'disabled' : '' }}>
                        </div>
                        <div style="display:flex;align-items:flex-end;padding-bottom:1px;">
                            <button type="button" class="btn-danger-sm" data-remove-cpmk-row aria-label="Hapus baris" {{ !$isUploadOpen ? 'disabled' : '' }}>
                                <i class="fas fa-trash" style="font-size:13px;"></i>
                            </button>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>

            <template id="cpmkRowTemplate">
                <div class="cpmk-row" data-cpmk-row data-row-index="__INDEX__">
                    <div>
                        <label class="field-label" style="font-size:11px;">CPL <span class="req">*</span></label>
                        <select name="cpmk_rows[__INDEX__][cpl_id]" class="field-control" style="font-size:13px;" data-cpmk-cpl-select required>
                            <option value="">Pilih CPL</option>
                        </select>
                        <div class="cpmk-preview" data-cpmk-preview>Pratinjau CPMK akan muncul setelah field diisi.</div>
                    </div>
                    <div>
                        <label class="field-label" style="font-size:11px;">Kode <span class="req">*</span></label>
                        <div style="display:flex;align-items:center;gap:6px;">
                            <span style="font-size:12px;font-weight:600;color:var(--slate-500);white-space:nowrap;padding:10px 8px;background:var(--slate-50);border:1px solid var(--slate-200);border-radius:8px;">CPMK</span>
                            <input type="text" name="cpmk_rows[__INDEX__][kode]" class="field-control" style="font-size:13px;" placeholder="1.1" required>
                        </div>
                    </div>
                    <div>
                        <label class="field-label" style="font-size:11px;">KKO <span class="req">*</span></label>
                        <select name="cpmk_rows[__INDEX__][kko]" class="field-control" style="font-size:13px;" required>
                            <option value="">Pilih KKO</option>
                            @foreach($kkoOptions as $val => $lbl)
                                <option value="{{ $val }}">{{ $val }} – {{ $lbl }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="field-label" style="font-size:11px;">Objek <span class="req">*</span></label>
                        <input type="text" name="cpmk_rows[__INDEX__][objek]" class="field-control" style="font-size:13px;" placeholder="merancang sistem IoT" required>
                    </div>
                    <div>
                        <label class="field-label" style="font-size:11px;">Konteks</label>
                        <input type="text" name="cpmk_rows[__INDEX__][konteks]" class="field-control" style="font-size:13px;" placeholder="sesuai kebutuhan pengguna">
                    </div>
                    <div style="display:flex;align-items:flex-end;padding-bottom:1px;">
                        <button type="button" class="btn-danger-sm" data-remove-cpmk-row aria-label="Hapus baris">
                            <i class="fas fa-trash" style="font-size:13px;"></i>
                        </button>
                    </div>
                </div>
            </template>
        </div>

        <div class="form-card">
            <div class="form-card-title"><i class="fas fa-pen-to-square"></i> Catatan Revisi</div>
            <div class="form-group">
                <label class="field-label">Deskripsi Revisi {!! $rps->status->value === 'revisi' ? '<span class="req">*</span>' : '' !!}</label>
                <textarea
                    name="catatan"
                    class="field-control"
                    rows="3"
                    placeholder="Jelaskan bagian RPS yang diubah, misalnya CPL, CPMK, dosen pengampu, atau dokumen yang diperbarui."
                    {{ !$isUploadOpen ? 'disabled' : '' }}
                    {{ $rps->status->value === 'revisi' ? 'required' : '' }}
                >{{ old('catatan', $rps->catatan ?? '') }}</textarea>
                <p class="field-hint">Catatan ini akan tersimpan di detail RPS dan audit log.</p>
                @error('catatan')<p class="field-error">{{ $message }}</p>@enderror
            </div>
        </div>

        {{-- Dokumen Upload --}}
        <div class="form-card">
            <div class="form-card-title"><i class="fas fa-file-pdf"></i> Dokumen RPS</div>
            <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:12px;">
                <p style="font-size:13px;color:var(--slate-600);margin:0;">Unggah file PDF dokumen RPS Anda.</p>
                <a href="{{ route('rps.template.download') }}" target="_blank" style="font-size:13px;color:var(--primary-blue);text-decoration:none;font-weight:600;display:flex;align-items:center;gap:5px;">
                    <i class="fas fa-download"></i> Download Template
                </a>
            </div>
            <label class="upload-zone {{ !$isUploadOpen ? 'closed' : '' }}" id="uploadZone">
                <input type="file" name="dokumen" id="fileInput" accept=".pdf" {{ (!$isUploadOpen || $rps->status->value === 'revisi') ? 'required' : '' }} {{ !$isUploadOpen ? 'disabled' : '' }}>
                <i class="fas fa-cloud-upload-alt" id="uploadIcon" style="{{ !$isUploadOpen ? 'color:#ababba;' : '' }}"></i>
                <strong id="uploadText">{{ !$isUploadOpen ? 'Upload ditutup' : ($rps->status->value === 'revisi' ? 'Upload file revisi baru' : 'Klik untuk unggah atau seret file ke sini') }}</strong>
                <span id="uploadSub">PDF (Maks. 1MB) - File lama akan diganti jika ada</span>
            </label>
            @error('dokumen')<p class="field-error" style="margin-top:8px;">{{ $message }}</p>@enderror
        </div>

        {{-- Actions --}}
        <div class="form-actions">
            <a href="{{ route('banksoal.rps.dosen.index') }}" class="btn btn-secondary">Batal</a>
            <button type="submit" class="btn btn-primary" id="submitBtn" {{ !$isUploadOpen ? 'disabled' : '' }}>
                <i class="fas fa-save"></i> Simpan Perubahan
            </button>
        </div>
    </form>

    {{-- History Section (Optional, but good to have) --}}
    @if(isset($history) && $history->isNotEmpty())
    <div class="form-card" style="margin-top:24px;">
        <div class="form-card-title"><i class="fas fa-history"></i> Riwayat Aktivitas</div>
        <div style="display:flex;flex-direction:column;gap:16px;">
            @foreach($history as $log)
                <div style="display:flex;align-items:flex-start;gap:16px;padding:16px;border-radius:12px;border:1px solid var(--slate-200);background:var(--slate-50);">
                    <div style="width:40px;height:40px;border-radius:50%;background:#fff;border:1px solid var(--slate-200);display:flex;align-items:center;justify-content:center;flex-shrink:0;color:var(--slate-500);">
                        <i class="fas fa-user"></i>
                    </div>
                    <div>
                        <p style="font-size:14px;font-weight:600;color:var(--slate-800);margin:0 0 4px 0;">{{ $log->description }}</p>
                        <p style="font-size:12px;color:var(--slate-500);margin:0;">{{ \Carbon\Carbon::parse($log->created_at)->translatedFormat('d F Y, H:i') }} WIB</p>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
    @endif

    @push('scripts')
    <script src="{{ asset('modules/banksoal/js/Banksoal/components/RpsCpmkRows.js') }}"></script>
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        // File upload preview
        const fileInput = document.getElementById('fileInput');
        const uploadText = document.getElementById('uploadText');
        const uploadSub  = document.getElementById('uploadSub');
        if (fileInput) {
            fileInput.addEventListener('change', function() {
                if (fileInput.files[0]) {
                    uploadText.textContent = fileInput.files[0].name;
                    uploadSub.textContent  = (fileInput.files[0].size / 1024).toFixed(0) + ' KB';
                }
            });
        }
    });
    </script>
    @endpush
</x-banksoal::layouts.dosen-admin>
