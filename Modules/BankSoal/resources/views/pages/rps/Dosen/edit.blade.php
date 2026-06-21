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

        .page-header { display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 24px; gap: 16px; flex-wrap: wrap; }
        .header-content h1 { font-size: 26px; font-weight: 700; color: var(--slate-800); margin: 0; letter-spacing: -0.5px; }
        .header-content p { font-size: 14px; color: var(--slate-500); margin: 6px 0 0 0; }

        .btn { padding: 10px 22px; border-radius: 9px; border: none; font-weight: 600; font-size: 14px; cursor: pointer; transition: all .2s; display: inline-flex; align-items: center; gap: 6px; text-decoration: none; }
        .btn-primary { background: var(--primary-blue); color: #fff; box-shadow: 0 2px 4px rgba(11,38,110,.15); }
        .btn-primary:hover:not(:disabled) { background: var(--primary-hover); transform: translateY(-1px); box-shadow: 0 4px 8px rgba(11,38,110,.2); }
        .btn-secondary { background: #fff; color: var(--slate-700); border: 1px solid var(--slate-300); }
        .btn-secondary:hover { background: var(--slate-50); border-color: var(--slate-400); }
        .btn-danger-sm { height: 40px; width: 40px; background: none; border: 1px solid #fecaca; border-radius: 9px; color: #ef4444; cursor: pointer; transition: all .2s; display: flex; align-items: center; justify-content: center; }
        .btn-danger-sm:hover { background: #fff1f2; }

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

        .field-hint { font-size: 12px; color: var(--slate-500); margin-top: 5px; }
        .field-error { font-size: 12px; color: var(--danger-red); margin-top: 5px; }

        /* Wizard Steps Progress Bar */
        .wizard-steps { display: flex; justify-content: space-between; position: relative; margin-bottom: 30px; background: var(--slate-100); padding: 12px; border-radius: 50px; }
        .wizard-step { display: flex; align-items: center; gap: 10px; z-index: 2; position: relative; cursor: pointer; flex: 1; justify-content: center; padding: 10px; border-radius: 30px; transition: all .2s; }
        .wizard-step.active { background: #fff; box-shadow: 0 2px 6px rgba(0,0,0,.08); color: var(--primary-blue); font-weight: 700; }
        .wizard-step.completed { color: #10b981; }
        .step-num { width: 24px; height: 24px; border-radius: 50%; background: var(--slate-300); color: #fff; display: flex; align-items: center; justify-content: center; font-size: 12px; font-weight: bold; }
        .wizard-step.active .step-num { background: var(--primary-blue); }
        .wizard-step.completed .step-num { background: #10b981; }
        .step-label { font-size: 14px; color: var(--slate-600); }
        .wizard-step.active .step-label { color: var(--primary-blue); }

        /* CPMK Table Section */
        .cpmk-section { border: 1px solid var(--slate-200); border-radius: 12px; overflow: hidden; margin-bottom: 20px; }
        .cpmk-header { display: flex; align-items: center; justify-content: space-between; padding: 14px 18px; background: var(--slate-50); border-bottom: 1px solid var(--slate-200); }
        .cpmk-header h3 { font-size: 14px; font-weight: 700; color: var(--slate-800); margin: 0; }
        .cpmk-col-head { display: grid; grid-template-columns: 190px 130px 150px 1fr 1fr 44px; gap: 10px; padding: 10px 18px; background: #f1f5f9; border-bottom: 1px solid var(--slate-200); font-size: 11px; font-weight: 600; text-transform: uppercase; color: var(--slate-500); }
        .cpmk-row { display: grid; grid-template-columns: 190px 130px 150px 1fr 1fr 44px; gap: 10px; align-items: start; padding: 14px 18px; border-bottom: 1px solid var(--slate-100); }
        .cpmk-row:last-child { border-bottom: none; }
        .cpmk-preview { font-size: 11px; color: var(--slate-500); background: var(--slate-50); border-radius: 8px; padding: 7px 10px; margin-top: 6px; }

        /* Dynamic Pertemuan Table Grid */
        .grid-table { display: flex; flex-direction: column; border: 1px solid var(--slate-200); border-radius: 12px; overflow: visible; min-width: 1300px; }
        .grid-table-header { display: grid; grid-template-columns: 80px 200px 180px 150px 100px 200px 120px 180px 80px; background: #f1f5f9; border-bottom: 1.5px solid var(--slate-300); font-size: 11px; font-weight: bold; text-transform: uppercase; color: var(--slate-600); text-align: center; }
        .grid-table-header div { padding: 12px 6px; border-right: 1px solid var(--slate-200); display: flex; align-items: center; justify-content: center; }
        .grid-table-header div:last-child { border-right: none; }
        .grid-table-row { display: grid; grid-template-columns: 80px 200px 180px 150px 100px 200px 120px 180px 80px; border-bottom: 1px solid var(--slate-200); align-items: stretch; }
        .grid-table-row:last-child { border-bottom: none; }
        .grid-table-row > div { padding: 8px 6px; border-right: 1px solid var(--slate-200); display: flex; align-items: center; justify-content: center; }
        .grid-table-row > div:last-child { border-right: none; }
        .grid-table-row input, .grid-table-row textarea, .grid-table-row select { font-size: 12px !important; padding: 6px 8px !important; }
        .grid-table-row.uts-row, .grid-table-row.uas-row { background: var(--slate-100); font-weight: bold; }
        .grid-table-row.uts-row div:not(:first-child), .grid-table-row.uas-row div:not(:first-child) { display: none; }

        /* Reference row styling */
        .ref-row { display: flex; gap: 12px; align-items: center; margin-bottom: 10px; }
        .ref-row:last-child { margin-bottom: 0; }

        /* Upload Zone Styles */
        .upload-zone {
            border: 2px dashed var(--slate-300);
            border-radius: 12px;
            padding: 36px 20px;
            text-align: center;
            cursor: pointer;
            transition: border-color 0.2s, background-color 0.2s;
            background: var(--slate-50);
            display: block;
            position: relative;
        }
        .upload-zone:hover {
            border-color: var(--slate-800);
            background: var(--slate-100);
        }
        .upload-zone i {
            font-size: 32px;
            color: var(--slate-600);
            margin-bottom: 10px;
            display: block;
        }
        .upload-zone strong {
            font-size: 14px;
            font-weight: 600;
            color: var(--slate-800);
            display: block;
            margin-bottom: 4px;
        }
        .upload-zone span {
            font-size: 12px;
            color: var(--slate-500);
        }
        .upload-zone input {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            opacity: 0;
            cursor: pointer;
        }

        /* Main Table Styling with explicit borders */
        .main-table {
            width: 100%;
            border-collapse: collapse;
            border: 1px solid var(--slate-200);
            border-radius: 8px;
            overflow: hidden;
            margin-bottom: 20px;
        }
        .main-table th {
            background: #f1f5f9;
            color: var(--slate-600);
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            padding: 12px 10px;
            border: 1px solid var(--slate-200);
            text-align: center;
        }
        .main-table td {
            padding: 10px;
            border: 1px solid var(--slate-200);
            vertical-align: middle;
        }
        .main-table tbody tr:hover {
            background-color: var(--slate-50);
        }

        .mk-badge { display: inline-flex; align-items: center; gap: 8px; background: var(--slate-100); border: 1px solid var(--slate-200); border-radius: 8px; padding: 10px 14px; font-size: 14px; font-weight: 600; color: var(--slate-700); width: 100%; }
        .mk-badge i { color: var(--primary-blue); }

        .hidden { display: none !important; }
    </style>
    @endpush

    @php
        $creationMethod = old('creation_method', $rps->creation_method ?? 'upload');
    @endphp

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
            <i class="fas fa-arrow-left"></i> Kembali
        </a>
    </div>

    <x-banksoal::notification.alerts />

    @if(!$isUploadOpen)
    <div class="alert-closed" style="display:flex;align-items:flex-start;gap:12px;background:#fff7ed;border:1px solid #fed7aa;border-radius:10px;padding:14px 16px;margin-bottom:20px;font-size:13px;color:#9a3412;">
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

    <!-- MAIN FORM -->
    <form action="{{ route('banksoal.rps.dosen.update', $rps->id) }}" method="POST" enctype="multipart/form-data" id="rpsSubmitForm"
        data-route-cpl="{{ route('banksoal.rps.dosen.cpl') }}"
        data-route-dosen="{{ route('banksoal.rps.dosen.dosen') }}"
        data-selected-dosen-ids='{{ json_encode($selectedDosenIds) }}'
        data-selected-cpl-ids='{{ json_encode($rps->cpls->pluck('id')->toArray()) }}'
        data-generate-detail='{{ json_encode($generateDetail) }}'
        data-cpmk-row-builder="1"
        data-edit-mode="1"
        data-rps-id="{{ $rps->id }}">
        @csrf
        @method('PUT')
        <input type="hidden" name="creation_method" id="creation_method_input" value="{{ $creationMethod }}">

        <!-- MUTUAL HEADER INFO -->
        <div class="form-card">
            <div class="form-card-title"><i class="fas fa-book-open"></i> Informasi Dasar RPS</div>
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
                    <select name="dosen_lain[]" id="dosenSelect" class="field-control" multiple></select>
                    <p class="field-hint">Pilih satu atau lebih dosen pengampu tambahan.</p>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label class="field-label">Semester <span class="req">*</span></label>
                    <select id="semester" class="field-control bg-slate-100 border-slate-200 text-slate-500 cursor-not-allowed" disabled>
                        <option value="Ganjil" {{ $rps->semester == 'Ganjil' ? 'selected' : '' }}>Ganjil</option>
                        <option value="Genap"  {{ $rps->semester == 'Genap'  ? 'selected' : '' }}>Genap</option>
                    </select>
                    <input type="hidden" name="semester" value="{{ $rps->semester }}">
                </div>
                <div class="form-group">
                    <label class="field-label">Tahun Ajaran <span class="req">*</span></label>
                    <select id="tahun_ajaran" class="field-control bg-slate-100 border-slate-200 text-slate-500 cursor-not-allowed" disabled>
                        <option value="{{ $rps->tahun_ajaran }}" selected>{{ $rps->tahun_ajaran }}</option>
                    </select>
                    <input type="hidden" name="tahun_ajaran" value="{{ $rps->tahun_ajaran }}">
                </div>
            </div>
        </div>

        @php
            $kkoOptions = [
                'C1'=>'Mengingat','C2'=>'Memahami','C3'=>'Menerapkan','C4'=>'Menganalisis','C5'=>'Mengevaluasi','C6'=>'Mencipta',
                'P1'=>'Meniru','P2'=>'Menyesuaikan','P3'=>'Membiasakan','P4'=>'Menguasai','P5'=>'Mahir',
                'A1'=>'Menerima','A2'=>'Merespon','A3'=>'Menilai','A4'=>'Mengorganisasi','A5'=>'Menghayati',
            ];
        @endphp

        <!-- ==================== METHOD A: UPLOAD PDF ==================== -->
        <div id="method_upload_panel" class="{{ $creationMethod === 'upload' ? '' : 'hidden' }}">
            {{-- CPMK Section --}}
            <div class="form-card">
                <div class="form-card-title"><i class="fas fa-list-check"></i> Capaian Pembelajaran Mata Kuliah (CPMK)</div>
                <div class="cpmk-section">
                    <div class="cpmk-header">
                        <div>
                            <p style="margin: 0; font-size: 13px; color: var(--slate-600);">Baris CPMK yang diajukan dalam RPS ini.</p>
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
                                <select name="cpmk_rows[{{ $index }}][cpl_id]" class="field-control cpl-select-input" data-cpmk-cpl-select style="font-size:13px;" data-selected-value="{{ $row['cpl_id'] ?? '' }}" required {{ !$isUploadOpen ? 'disabled' : '' }}>
                                    <option value="">Pilih CPL</option>
                                </select>
                            </div>
                            <div>
                                <div style="display:flex;align-items:center;gap:6px;">
                                    <span style="font-size:12px;font-weight:600;color:var(--slate-500);padding:10px 8px;background:var(--slate-50);border:1px solid var(--slate-200);border-radius:8px;">CPMK</span>
                                    <input type="text" name="cpmk_rows[{{ $index }}][kode]" value="{{ $row['kode'] ?? '' }}" class="field-control" style="font-size:13px;" placeholder="1" required {{ !$isUploadOpen ? 'disabled' : '' }}>
                                </div>
                            </div>
                            <div>
                                <select name="cpmk_rows[{{ $index }}][kko]" class="field-control kko-select-input" style="font-size:13px;" required {{ !$isUploadOpen ? 'disabled' : '' }}>
                                    <option value="">Pilih KKO</option>
                                    @foreach($kkoOptions as $val => $lbl)
                                        <option value="{{ $val }}" {{ ($row['kko'] ?? '') === $val ? 'selected' : '' }}>{{ $val }} – {{ $lbl }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <input type="text" name="cpmk_rows[{{ $index }}][objek]" value="{{ $row['objek'] ?? '' }}" class="field-control objek-input" style="font-size:13px;" placeholder="merancang sistem IoT" required {{ !$isUploadOpen ? 'disabled' : '' }}>
                            </div>
                            <div>
                                <input type="text" name="cpmk_rows[{{ $index }}][konteks]" value="{{ $row['konteks'] ?? '' }}" class="field-control" style="font-size:13px;" placeholder="sesuai kebutuhan" {{ !$isUploadOpen ? 'disabled' : '' }}>
                            </div>
                            <div style="display:flex;align-items:flex-end;">
                                <button type="button" class="btn-danger-sm" data-remove-cpmk-row {{ !$isUploadOpen ? 'disabled' : '' }}><i class="fas fa-trash"></i></button>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <div class="form-card">
                <div class="form-card-title"><i class="fas fa-pen-to-square"></i> Catatan Revisi</div>
                <div class="form-group">
                    <label class="field-label">Deskripsi Revisi {!! $rps->status->value === 'revisi' ? '<span class="req">*</span>' : '' !!}</label>
                    <textarea name="catatan" class="field-control" rows="3" placeholder="Jelaskan bagian RPS yang diubah..." {{ !$isUploadOpen ? 'disabled' : '' }} {{ $rps->status->value === 'revisi' ? 'required' : '' }}>{{ old('catatan', $rps->catatan ?? '') }}</textarea>
                </div>
            </div>

            <div class="form-card">
                <div class="form-card-title"><i class="fas fa-file-pdf"></i> Unggah Dokumen RPS</div>
                <x-banksoal::ui.upload-zone
                    name="dokumen"
                    inputId="fileInput"
                    accept=".pdf"
                    maxLabel="PDF (Maks. 5MB)"
                    :disabled="!$isUploadOpen"
                    :required="!$isUploadOpen || $rps->status->value === 'revisi'"
                />
                @error('dokumen')<p class="field-error" style="margin-top:8px;">{{ $message }}</p>@enderror
            </div>

            <div class="form-actions" style="display: flex; justify-content: flex-end; gap: 12px; margin-top: 24px;">
                <a href="{{ route('banksoal.rps.dosen.index') }}" class="btn btn-secondary">Batal</a>
                <button type="submit" class="btn btn-primary" id="btnSubmitManual" {{ !$isUploadOpen ? 'disabled' : '' }}><i class="fas fa-paper-plane"></i> Simpan Perubahan</button>
            </div>
        </div>

        <!-- ==================== METHOD B: WIZARD GENERATOR ==================== -->
        <div id="method_generator_panel" class="{{ $creationMethod === 'generator' ? '' : 'hidden' }}">
            <!-- Steps Indicators -->
            <div class="wizard-steps">
                <div class="wizard-step active" data-step="1">
                    <div class="step-num">1</div>
                    <div class="step-label">Perancangan MK</div>
                </div>
                <div class="wizard-step" data-step="2">
                    <div class="step-num">2</div>
                    <div class="step-label">Kontrak Kuliah</div>
                </div>
                <div class="wizard-step" data-step="3">
                    <div class="step-num">3</div>
                    <div class="step-label">Pertemuan & Referensi</div>
                </div>
            </div>

            <!-- STEP 1: PERANCANGAN MK -->
            <div class="wizard-content-section" id="step_1_panel">
                <div class="form-card">
                    <div class="form-card-title"><i class="fas fa-bullseye"></i> Capaian Pembelajaran Lulusan (CPL)</div>
                    <div class="form-group-full">
                        <label class="field-label">Pilih CPL yang Terkait <span class="req">*</span></label>
                        <div id="cpl_checkbox_container" style="display:flex; flex-direction:column; gap:10px; padding: 15px; border: 1px solid var(--slate-200); border-radius: 9px; max-height: 250px; overflow-y: auto;">
                            <p class="field-hint">Memuat daftar CPL...</p>
                        </div>
                    </div>
                </div>

                <div class="form-card">
                    <div class="form-card-title" style="margin-bottom:16px;"><i class="fas fa-list-check"></i> Capaian Pembelajaran Mata Kuliah (CPMK)</div>
                    <div class="cpmk-section">
                        <div class="cpmk-header">
                            <div>
                                <p style="margin: 0; font-size: 13px; color: var(--slate-600);">Tentukan CPMK. CPMK minimal 1 baris.</p>
                            </div>
                            <button type="button" class="btn btn-primary" id="addCpmkRowBtnGenerator" style="padding:8px 16px;font-size:13px;" {{ !$isUploadOpen ? 'disabled' : '' }}>
                                <i class="fas fa-plus"></i> Tambah Baris
                            </button>
                        </div>

                        <div class="cpmk-col-head">
                            <div>CPL</div><div>Kode CPMK</div><div>KKO</div><div>Objek</div><div>Konteks</div><div></div>
                        </div>

                        <div id="cpmkRowsGenerator" class="cpmk-rows">
                            @foreach($existingCpmkRows as $index => $row)
                            <div class="cpmk-row" data-cpmk-row data-row-index="{{ $index }}">
                                <div>
                                    <select name="cpmk_rows[{{ $index }}][cpl_id]" class="field-control cpl-select-input" data-cpmk-cpl-select style="font-size:13px;" data-selected-value="{{ $row['cpl_id'] ?? '' }}" required {{ !$isUploadOpen ? 'disabled' : '' }}>
                                        <option value="">Pilih CPL</option>
                                    </select>
                                </div>
                                <div>
                                    <div style="display:flex;align-items:center;gap:6px;">
                                        <span style="font-size:12px;font-weight:600;color:var(--slate-500);padding:10px 8px;background:var(--slate-50);border:1px solid var(--slate-200);border-radius:8px;">CPMK</span>
                                        <input type="text" name="cpmk_rows[{{ $index }}][kode]" value="{{ $row['kode'] ?? '' }}" class="field-control" style="font-size:13px;" placeholder="1" required {{ !$isUploadOpen ? 'disabled' : '' }}>
                                    </div>
                                </div>
                                <div>
                                    <select name="cpmk_rows[{{ $index }}][kko]" class="field-control kko-select-input" style="font-size:13px;" required {{ !$isUploadOpen ? 'disabled' : '' }}>
                                        <option value="">Pilih KKO</option>
                                        @foreach($kkoOptions as $val => $lbl)
                                            <option value="{{ $val }}" {{ ($row['kko'] ?? '') === $val ? 'selected' : '' }}>{{ $val }} – {{ $lbl }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div>
                                    <input type="text" name="cpmk_rows[{{ $index }}][objek]" value="{{ $row['objek'] ?? '' }}" class="field-control objek-input" style="font-size:13px;" placeholder="merancang sistem IoT" required {{ !$isUploadOpen ? 'disabled' : '' }}>
                                </div>
                                <div>
                                    <input type="text" name="cpmk_rows[{{ $index }}][konteks]" value="{{ $row['konteks'] ?? '' }}" class="field-control" style="font-size:13px;" placeholder="sesuai kebutuhan" {{ !$isUploadOpen ? 'disabled' : '' }}>
                                </div>
                                <div style="display:flex;align-items:flex-end;">
                                    <button type="button" class="btn-danger-sm" data-remove-cpmk-row {{ !$isUploadOpen ? 'disabled' : '' }}><i class="fas fa-trash"></i></button>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                </div>

                <div class="form-card">
                    <div class="form-card-title"><i class="fas fa-align-left"></i> Deskripsi Singkat Mata Kuliah</div>
                    <div class="form-group">
                        <label class="field-label">Deskripsi Mata Kuliah <span class="req">*</span></label>
                        <textarea name="deskripsi_mk" id="deskripsi_mk" class="field-control" rows="5" placeholder="Tuliskan deskripsi singkat mengenai materi, fokus kajian, dan tujuan pembelajaran mata kuliah ini..." {{ !$isUploadOpen ? 'disabled' : '' }}>{{ $generateDetail?->deskripsi_mk ?? '' }}</textarea>
                    </div>
                </div>

                <div style="display: flex; justify-content: flex-end; gap: 12px;">
                    <a href="{{ route('banksoal.rps.dosen.index') }}" class="btn btn-secondary">Batal</a>
                    <button type="button" class="btn btn-primary btn-next-step" data-next="2">Next <i class="fas fa-chevron-right"></i></button>
                </div>
            </div>

            <!-- STEP 2: KONTRAK KULIAH -->
            @php
                $penilaian = $generateDetail ? $generateDetail->penilaian_data : null;
            @endphp
            <div class="wizard-content-section hidden" id="step_2_panel">
                <div class="form-card">
                    <div class="form-card-title"><i class="fas fa-university"></i> Institusi Pelaksana</div>
                    <div class="form-row">
                        <div class="form-group">
                            <label class="field-label">Fakultas</label>
                            <input type="text" class="field-control bg-slate-100" value="Teknik" data-always-disabled="true" disabled>
                        </div>
                        <div class="form-group">
                            <label class="field-label">Program Studi</label>
                            <input type="text" class="field-control bg-slate-100" value="Teknik Komputer" data-always-disabled="true" disabled>
                        </div>
                    </div>
                </div>

                <div class="form-card">
                    <div class="form-card-title"><i class="fas fa-star-half-stroke"></i> Tabel Penilaian Mata Kuliah</div>
                    <p style="font-size: 13px; color: var(--slate-600); margin-bottom: 16px;">Tentukan bobot (%) dan komponen evaluasi yang dinilai. Total bobot harus berjumlah 100%.</p>

                    <table class="main-table" id="penilaian_data_table">
                        <thead>
                            <tr>
                                <th style="width: 5%;">No</th>
                                <th style="width: 25%;">Poin Evaluasi</th>
                                <th style="width: 40%;">Komponen Evaluasi</th>
                                <th style="width: 15%;">Bobot (%)</th>
                                <th style="width: 15%;">Target CPMK</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td class="text-center">1</td>
                                <td class="font-bold">Aktivitas Partisipatif<input type="hidden" name="penilaian_data[0][poin]" value="Aktivitas Partisipatif"></td>
                                <td><textarea name="penilaian_data[0][komponen]" class="field-control" rows="2" placeholder="Aktivitas Kelas / Diskusi" required {{ !$isUploadOpen ? 'disabled' : '' }}>{{ $penilaian[0]['komponen'] ?? '' }}</textarea></td>
                                <td><input type="number" name="penilaian_data[0][bobot]" class="field-control text-center weight-input" min="0" max="100" value="{{ $penilaian[0]['bobot'] ?? 0 }}" required {{ !$isUploadOpen ? 'disabled' : '' }}></td>
                                <td><select name="penilaian_data[0][cpmk][]" class="field-control cpmk-target-select" multiple required data-selected-values="{{ json_encode($penilaian[0]['cpmk'] ?? []) }}" {{ !$isUploadOpen ? 'disabled' : '' }}></select></td>
                            </tr>
                            <tr>
                                <td class="text-center">2</td>
                                <td class="font-bold">Hasil Proyek<input type="hidden" name="penilaian_data[1][poin]" value="Hasil Proyek"></td>
                                <td><textarea name="penilaian_data[1][komponen]" class="field-control" rows="2" placeholder="Hasil Proyek Desain" required {{ !$isUploadOpen ? 'disabled' : '' }}>{{ $penilaian[1]['komponen'] ?? '' }}</textarea></td>
                                <td><input type="number" name="penilaian_data[1][bobot]" class="field-control text-center weight-input" min="0" max="100" value="{{ $penilaian[1]['bobot'] ?? 0 }}" required {{ !$isUploadOpen ? 'disabled' : '' }}></td>
                                <td><select name="penilaian_data[1][cpmk][]" class="field-control cpmk-target-select" multiple required data-selected-values="{{ json_encode($penilaian[1]['cpmk'] ?? []) }}" {{ !$isUploadOpen ? 'disabled' : '' }}></select></td>
                            </tr>
                            <tr>
                                <td class="text-center">3</td>
                                <td class="font-bold">Tugas<input type="hidden" name="penilaian_data[2][poin]" value="Tugas"></td>
                                <td><textarea name="penilaian_data[2][komponen]" class="field-control" rows="2" placeholder="Tugas Kelompok" required {{ !$isUploadOpen ? 'disabled' : '' }}>{{ $penilaian[2]['komponen'] ?? '' }}</textarea></td>
                                <td><input type="number" name="penilaian_data[2][bobot]" class="field-control text-center weight-input" min="0" max="100" value="{{ $penilaian[2]['bobot'] ?? 0 }}" required {{ !$isUploadOpen ? 'disabled' : '' }}></td>
                                <td><select name="penilaian_data[2][cpmk][]" class="field-control cpmk-target-select" multiple required data-selected-values="{{ json_encode($penilaian[2]['cpmk'] ?? []) }}" {{ !$isUploadOpen ? 'disabled' : '' }}></select></td>
                            </tr>
                            <tr>
                                <td class="text-center">4</td>
                                <td class="font-bold">Quiz<input type="hidden" name="penilaian_data[3][poin]" value="Quiz"></td>
                                <td><textarea name="penilaian_data[3][komponen]" class="field-control" rows="2" placeholder="Tugas Individu / Kuis" required {{ !$isUploadOpen ? 'disabled' : '' }}>{{ $penilaian[3]['komponen'] ?? '' }}</textarea></td>
                                <td><input type="number" name="penilaian_data[3][bobot]" class="field-control text-center weight-input" min="0" max="100" value="{{ $penilaian[3]['bobot'] ?? 0 }}" required {{ !$isUploadOpen ? 'disabled' : '' }}></td>
                                <td><select name="penilaian_data[3][cpmk][]" class="field-control cpmk-target-select" multiple required data-selected-values="{{ json_encode($penilaian[3]['cpmk'] ?? []) }}" {{ !$isUploadOpen ? 'disabled' : '' }}></select></td>
                            </tr>
                            <!-- Kognitif has UTS and UAS -->
                            <tr>
                                <td class="text-center" rowspan="2">5</td>
                                <td class="font-bold" rowspan="2">Kognitif / Pengetahuan<input type="hidden" name="penilaian_data[4][poin]" value="Kognitif / Pengetahuan"></td>
                                <td>UTS (Ujian Tengah Semester)<input type="hidden" name="penilaian_data[4][sub_rows][0][komponen]" value="UTS"></td>
                                <td><input type="number" name="penilaian_data[4][sub_rows][0][bobot]" class="field-control text-center weight-input" min="0" max="100" value="{{ $penilaian[4]['sub_rows'][0]['bobot'] ?? 0 }}" required {{ !$isUploadOpen ? 'disabled' : '' }}></td>
                                <td><select name="penilaian_data[4][sub_rows][0][cpmk][]" class="field-control cpmk-target-select" multiple required data-selected-values="{{ json_encode($penilaian[4]['sub_rows'][0]['cpmk'] ?? []) }}" {{ !$isUploadOpen ? 'disabled' : '' }}></select></td>
                            </tr>
                            <tr>
                                <td>UAS (Ujian Akhir Semester)<input type="hidden" name="penilaian_data[4][sub_rows][1][komponen]" value="UAS"></td>
                                <td><input type="number" name="penilaian_data[4][sub_rows][1][bobot]" class="field-control text-center weight-input" min="0" max="100" value="{{ $penilaian[4]['sub_rows'][1]['bobot'] ?? 0 }}" required {{ !$isUploadOpen ? 'disabled' : '' }}></td>
                                <td><select name="penilaian_data[4][sub_rows][1][cpmk][]" class="field-control cpmk-target-select" multiple required data-selected-values="{{ json_encode($penilaian[4]['sub_rows'][1]['cpmk'] ?? []) }}" {{ !$isUploadOpen ? 'disabled' : '' }}></select></td>
                            </tr>
                            <tr class="font-bold" style="background-color: var(--slate-100);">
                                <td colspan="3" class="text-right">Total Bobot:</td>
                                <td class="text-center" id="total_weight_label">0%</td>
                                <td></td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <div style="display: flex; justify-content: space-between; gap: 12px;">
                    <button type="button" class="btn btn-secondary btn-prev-step" data-prev="1"><i class="fas fa-chevron-left"></i> Back</button>
                    <button type="button" class="btn btn-primary btn-next-step" data-next="3">Next <i class="fas fa-chevron-right"></i></button>
                </div>
            </div>

            <!-- STEP 3: PERTEMUAN & REFERENSI -->
            <div class="wizard-content-section hidden" id="step_3_panel">
                <!-- Sub-tabs Navigation -->
                <div class="sub-tabs-nav" style="display: flex; gap: 8px; margin-bottom: 24px; border-bottom: 2px solid var(--slate-200); padding-bottom: 8px;">
                    <button type="button" class="sub-tab-btn active" data-sub-tab="sebelum-uts" style="background: none; border: none; font-size: 14px; font-weight: 600; color: var(--primary-blue); cursor: pointer; padding: 8px 16px; border-bottom: 3px solid var(--primary-blue); margin-bottom: -10px; transition: all 0.2s;">
                        <i class="fas fa-arrow-right-to-bracket"></i> Pertemuan Sebelum UTS
                    </button>
                    <button type="button" class="sub-tab-btn" data-sub-tab="setelah-uts" style="background: none; border: none; font-size: 14px; font-weight: 600; color: var(--slate-500); cursor: pointer; padding: 8px 16px; border-bottom: 3px solid transparent; margin-bottom: -10px; transition: all 0.2s;">
                        <i class="fas fa-arrow-right-from-bracket"></i> Pertemuan Setelah UTS
                    </button>
                    <button type="button" class="sub-tab-btn" data-sub-tab="referensi-catatan" style="background: none; border: none; font-size: 14px; font-weight: 600; color: var(--slate-500); cursor: pointer; padding: 8px 16px; border-bottom: 3px solid transparent; margin-bottom: -10px; transition: all 0.2s;">
                        <i class="fas fa-book-open"></i> Referensi & Catatan
                    </button>
                </div>

                <!-- SUB-TAB 1: SEBELUM UTS -->
                <div class="sub-tab-panel" id="sub_tab_sebelum-uts_panel">
                    <div class="form-card" style="overflow-x: auto; padding: 20px;">
                        <div class="form-card-title"><i class="fas fa-calendar-alt"></i> Rencana Kegiatan Pembelajaran Sebelum UTS (Pertemuan 1 - 8)</div>
                        
                        <div class="grid-table">
                            <div class="grid-table-header">
                                <div>Pertemuan</div>
                                <div>Kemampuan Akhir</div>
                                <div>Bahan Kajian</div>
                                <div>Metode</div>
                                <div>Waktu (menit)</div>
                                <div>Pengalaman Belajar</div>
                                <div>Target CPMK</div>
                                <div>Kriteria & Indikator</div>
                                <div>Bobot (%)</div>
                            </div>
                            <div id="pertemuan_sebelum_uts_container">
                                <!-- Pertemuan 1-8 will be loaded here via JS -->
                            </div>
                        </div>
                    </div>
                    
                    <div style="display: flex; justify-content: space-between; gap: 12px; margin-top: 16px;">
                        <button type="button" class="btn btn-secondary btn-prev-step" data-prev="2"><i class="fas fa-chevron-left"></i> Back</button>
                        <button type="button" class="btn btn-primary btn-next-sub-tab" data-next-tab="setelah-uts">Next (Setelah UTS) <i class="fas fa-chevron-right"></i></button>
                    </div>
                </div>

                <!-- SUB-TAB 2: SETELAH UTS -->
                <div class="sub-tab-panel hidden" id="sub_tab_setelah-uts_panel">
                    <div class="form-card" style="overflow-x: auto; padding: 20px;">
                        <div class="form-card-title"><i class="fas fa-calendar-alt"></i> Rencana Kegiatan Pembelajaran Setelah UTS (Pertemuan 9 - 16)</div>
                        
                        <div class="grid-table">
                            <div class="grid-table-header">
                                <div>Pertemuan</div>
                                <div>Kemampuan Akhir</div>
                                <div>Bahan Kajian</div>
                                <div>Metode</div>
                                <div>Waktu (menit)</div>
                                <div>Pengalaman Belajar</div>
                                <div>Target CPMK</div>
                                <div>Kriteria & Indikator</div>
                                <div>Bobot (%)</div>
                            </div>
                            <div id="pertemuan_setelah_uts_container">
                                <!-- Pertemuan 9-16 will be loaded here via JS -->
                            </div>
                        </div>
                    </div>
                    
                    <div style="display: flex; justify-content: space-between; gap: 12px; margin-top: 16px;">
                        <button type="button" class="btn btn-secondary btn-prev-sub-tab" data-prev-tab="sebelum-uts"><i class="fas fa-chevron-left"></i> Back</button>
                        <button type="button" class="btn btn-primary btn-next-sub-tab" data-next-tab="referensi-catatan">Next (Referensi) <i class="fas fa-chevron-right"></i></button>
                    </div>
                </div>

                <!-- SUB-TAB 3: REFERENSI & CATATAN -->
                <div class="sub-tab-panel hidden" id="sub_tab_referensi-catatan_panel">
                    <div class="form-card">
                        <div class="form-card-title"><i class="fas fa-book"></i> Daftar Referensi</div>
                        <p style="font-size: 13px; color: var(--slate-600); margin-bottom: 12px;">Tambahkan satu atau lebih buku/jurnal referensi utama dan pendukung.</p>
                        <div id="references_container">
                            <div class="ref-row">
                                <input type="text" name="referensi_data[]" class="field-control" placeholder="Contoh: Pressman, R.S. (2015). Software Engineering: A Practitioner's Approach. McGraw-Hill." required {{ !$isUploadOpen ? 'disabled' : '' }}>
                                <button type="button" class="btn-danger-sm btn-remove-ref" style="flex-shrink: 0;" {{ !$isUploadOpen ? 'disabled' : '' }}><i class="fas fa-trash"></i></button>
                            </div>
                        </div>
                        <button type="button" class="btn btn-secondary" id="btnAddReference" style="margin-top:12px; padding:8px 16px; font-size:13px;" {{ !$isUploadOpen ? 'disabled' : '' }}>
                            <i class="fas fa-plus"></i> Tambah Referensi
                        </button>
                    </div>

                    <div class="form-card">
                        <div class="form-card-title"><i class="fas fa-comment"></i> Catatan Revisi</div>
                        <div class="form-group">
                            <textarea name="catatan" id="catatan_generator" class="field-control" rows="3" placeholder="Opsional. Tambahkan catatan revisi..." {{ !$isUploadOpen ? 'disabled' : '' }} {{ $rps->status->value === 'revisi' ? 'required' : '' }}>{{ old('catatan', $rps->catatan ?? '') }}</textarea>
                        </div>
                    </div>
                    
                    <div style="display: flex; justify-content: space-between; gap: 12px; margin-top: 16px;">
                        <button type="button" class="btn btn-secondary btn-prev-sub-tab" data-prev-tab="setelah-uts"><i class="fas fa-chevron-left"></i> Back</button>
                        <button type="submit" class="btn btn-primary" id="btnSubmitGenerator" {{ !$isUploadOpen ? 'disabled' : '' }}><i class="fas fa-paper-plane"></i> Simpan & Generate PDF</button>
                    </div>
                </div>
            </div>
        </div>
    </form>

    <template id="cpmkRowTemplate">
        <div class="cpmk-row" data-cpmk-row data-row-index="__INDEX__">
            <div>
                <select name="cpmk_rows[__INDEX__][cpl_id]" class="field-control cpl-select-input" data-cpmk-cpl-select style="font-size:13px;" required>
                    <option value="">Pilih CPL</option>
                </select>
            </div>
            <div>
                <div style="display:flex;align-items:center;gap:6px;">
                    <span style="font-size:12px;font-weight:600;color:var(--slate-500);padding:10px 8px;background:var(--slate-50);border:1px solid var(--slate-200);border-radius:8px;">CPMK</span>
                    <input type="text" name="cpmk_rows[__INDEX__][kode]" class="field-control" style="font-size:13px;" placeholder="1" required>
                </div>
            </div>
            <div>
                <select name="cpmk_rows[__INDEX__][kko]" class="field-control kko-select-input" style="font-size:13px;" required>
                    <option value="">Pilih KKO</option>
                    @foreach($kkoOptions as $val => $lbl)
                        <option value="{{ $val }}">{{ $val }} – {{ $lbl }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <input type="text" name="cpmk_rows[__INDEX__][objek]" class="field-control objek-input" style="font-size:13px;" placeholder="merancang sistem IoT" required>
            </div>
            <div>
                <input type="text" name="cpmk_rows[__INDEX__][konteks]" class="field-control" style="font-size:13px;" placeholder="sesuai kebutuhan">
            </div>
            <div style="display:flex;align-items:flex-end;">
                <button type="button" class="btn-danger-sm" data-remove-cpmk-row><i class="fas fa-trash"></i></button>
            </div>
        </div>
    </template>

    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const creationMethodInput = document.getElementById('creation_method_input');
            const methodUploadPanel = document.getElementById('method_upload_panel');
            const methodGeneratorPanel = document.getElementById('method_generator_panel');
            const rpsSubmitForm = document.getElementById('rpsSubmitForm');

            function toggleInputs(container, enabled) {
                if (!container) return;
                container.querySelectorAll('input, select, textarea').forEach(input => {
                    if (input.name === '_token' || input.id === 'creation_method_input') return;
                    if (input.hasAttribute('data-always-disabled')) {
                        input.disabled = true;
                        return;
                    }
                    input.disabled = !enabled;
                });
            }

            // Wizard Step Management
            const wizardSteps = document.querySelectorAll('.wizard-step');
            const stepPanels = document.querySelectorAll('.wizard-content-section');

            function loadWizardStep(stepNum) {
                wizardSteps.forEach(step => {
                    step.classList.remove('active');
                    if(parseInt(step.dataset.step) < stepNum) {
                        step.classList.add('completed');
                    } else {
                        step.classList.remove('completed');
                    }
                });
                document.querySelector(`.wizard-step[data-step="${stepNum}"]`).classList.add('active');

                stepPanels.forEach(panel => panel.classList.add('hidden'));
                document.getElementById(`step_${stepNum}_panel`).classList.remove('hidden');

                if (stepNum === 2 || stepNum === 3) {
                    populateCpmkDropdowns();
                }
            }

            function setCreationMethod(method) {
                creationMethodInput.value = method;

                if(method === 'upload') {
                    methodUploadPanel.classList.remove('hidden');
                    methodGeneratorPanel.classList.add('hidden');
                    const fileInput = document.getElementById('fileInput');
                    if (fileInput) fileInput.required = !fileInput.hasAttribute('disabled') && !document.querySelector('.upload-zone').classList.contains('closed');
                    
                    toggleInputs(methodGeneratorPanel, false);
                    toggleInputs(methodUploadPanel, true);
                } else {
                    methodUploadPanel.classList.add('hidden');
                    methodGeneratorPanel.classList.remove('hidden');
                    const fileInput = document.getElementById('fileInput');
                    if (fileInput) fileInput.required = false;
                    
                    toggleInputs(methodUploadPanel, false);
                    toggleInputs(methodGeneratorPanel, true);
                    loadWizardStep(1);
                }

                if (window.BanksoalRpsUploadForm && typeof window.BanksoalRpsUploadForm.updateCpmkFormState === 'function') {
                    window.BanksoalRpsUploadForm.updateCpmkFormState();
                }
            }

            // Initial setup based on creation method
            setCreationMethod(creationMethodInput ? creationMethodInput.value : 'upload');

            // Next / Prev triggers
            document.querySelectorAll('.btn-next-step').forEach(btn => {
                btn.addEventListener('click', function() {
                    const currentPanel = this.closest('.wizard-content-section');
                    const inputs = currentPanel.querySelectorAll('input[required], select[required], textarea[required]');
                    let isValid = true;
                    
                    inputs.forEach(input => {
                        if (!input.checkValidity()) {
                            isValid = false;
                            input.reportValidity();
                        }
                    });

                    // Specifically for Step 2 total weight check (Kontrak Kuliah)
                    if(currentPanel.id === 'step_2_panel') {
                        const total = calculateTotalWeight();
                        if(total !== 100) {
                            alert('Total Bobot Penilaian Mata Kuliah harus tepat berjumlah 100%! Sekarang: ' + total + '%');
                            isValid = false;
                        }
                    }

                    if(isValid) {
                        const nextStep = parseInt(this.dataset.next);
                        saveCache(nextStep);
                    }
                });
            });

            document.querySelectorAll('.btn-prev-step').forEach(btn => {
                btn.addEventListener('click', function() {
                    const prevStep = parseInt(this.dataset.prev);
                    saveCache(prevStep);
                });
            });

            // Toggle sub-tabs
            const subTabBtns = document.querySelectorAll('.sub-tab-btn');
            const subTabPanels = document.querySelectorAll('.sub-tab-panel');

            subTabBtns.forEach(btn => {
                btn.addEventListener('click', function() {
                    subTabBtns.forEach(b => {
                        b.classList.remove('active');
                        b.style.color = 'var(--slate-500)';
                        b.style.borderBottomColor = 'transparent';
                    });
                    this.classList.add('active');
                    this.style.color = 'var(--primary-blue)';
                    this.style.borderBottomColor = 'var(--primary-blue)';
                    
                    subTabPanels.forEach(p => p.classList.add('hidden'));
                    document.getElementById(`sub_tab_${this.dataset.subTab}_panel`).classList.remove('hidden');
                });
            });

            // Sub-tabs navigation buttons
            document.querySelectorAll('.btn-next-sub-tab').forEach(btn => {
                btn.addEventListener('click', function() {
                    const targetTab = this.dataset.nextTab;
                    const tabBtn = document.querySelector(`.sub-tab-btn[data-sub-tab="${targetTab}"]`);
                    if (tabBtn) tabBtn.click();
                });
            });

            document.querySelectorAll('.btn-prev-sub-tab').forEach(btn => {
                btn.addEventListener('click', function() {
                    const targetTab = this.dataset.prevTab;
                    const tabBtn = document.querySelector(`.sub-tab-btn[data-sub-tab="${targetTab}"]`);
                    if (tabBtn) tabBtn.click();
                });
            });

            // Total weight calculation helper
            const totalWeightLabel = document.getElementById('total_weight_label');

            function calculateTotalWeight() {
                let total = 0;
                document.querySelectorAll('.weight-input').forEach(input => {
                    total += parseInt(input.value || 0);
                });
                if (totalWeightLabel) {
                    totalWeightLabel.textContent = total + '%';
                    if(total === 100) {
                        totalWeightLabel.style.color = '#10b981';
                    } else {
                        totalWeightLabel.style.color = '#ef4444';
                    }
                }
                return total;
            }

            document.addEventListener('input', function(e) {
                if (e.target.type === 'number') {
                    const val = e.target.value;
                    if (val.length > 1 && val.startsWith('0')) {
                        e.target.value = val.replace(/^0+(?=\d)/, '');
                    }
                }
                if(e.target.classList.contains('weight-input')) {
                    calculateTotalWeight();
                }
            });

            // Populate Target CPMK dropdowns dynamically
            function populateCpmkDropdowns() {
                const list = [];
                const seen = new Set();
                const rows = document.querySelectorAll('#cpmkRowsGenerator .cpmk-row, #cpmkRows .cpmk-row');
                rows.forEach(row => {
                    const cplSelect = row.querySelector('.cpl-select-input, select[name*="[cpl_id]"]');
                    const kodeInput = row.querySelector('input[name*="[kode]"]');
                    if (cplSelect && cplSelect.value && kodeInput && kodeInput.value.trim()) {
                        const selectedOpt = cplSelect.options[cplSelect.selectedIndex];
                        if (selectedOpt) {
                            const cplText = selectedOpt.textContent.trim();
                            const match = cplText.match(/\d+/);
                            const cplNum = match ? parseInt(match[0], 10) : cplText;
                            const cpmkNum = kodeInput.value.trim();
                            const val = `${cplNum}.${cpmkNum}`;
                            if (!seen.has(val)) {
                                seen.add(val);
                                list.push({
                                    value: val,
                                    label: `CPMK ${val}`
                                });
                            }
                        }
                    }
                });

                const targetSelects = document.querySelectorAll('.cpmk-target-select, .pertemuan-cpmk-select');
                targetSelects.forEach(select => {
                    let currentVals = [];
                    if (select.tomselect) {
                        const tsVal = select.tomselect.getValue();
                        currentVals = Array.isArray(tsVal) ? tsVal : (tsVal ? [tsVal] : []);
                    } else {
                        currentVals = Array.from(select.selectedOptions).map(opt => opt.value);
                    }

                    if (select.dataset.selectedValues) {
                        try {
                            const pre = JSON.parse(select.dataset.selectedValues);
                            if (Array.isArray(pre)) {
                                currentVals = [...new Set([...currentVals, ...pre.map(String)])];
                            } else if (pre) {
                                currentVals = [...new Set([...currentVals, String(pre)])];
                            }
                        } catch(e) {}
                    }

                    currentVals = currentVals.map(val => {
                        const strVal = String(val);
                        const match = strVal.match(/cpl[-_\s]*0*(\d+)\.(\d+)/i) || strVal.match(/[-_\s]*0*(\d+)\.(\d+)/) || strVal.match(/CPMK\s*(\d+)\.(\d+)/i);
                        if (match) {
                            return `${parseInt(match[1], 10)}.${match[2]}`;
                        }
                        return strVal;
                    });

                    if (typeof TomSelect !== 'undefined' && select.tomselect) {
                        const ts = select.tomselect;
                        ts.clear(true);
                        ts.clearOptions();
                        list.forEach(item => {
                            ts.addOption({ value: item.value, text: item.label });
                        });
                        currentVals.forEach(val => {
                            ts.addItem(val, true);
                        });
                        ts.refreshOptions(false);
                    } else {
                        select.innerHTML = '';
                        list.forEach(item => {
                            const opt = document.createElement('option');
                            opt.value = item.value;
                            opt.textContent = item.label;
                            if (currentVals.includes(item.value)) {
                                opt.selected = true;
                            }
                            select.appendChild(opt);
                        });
                        if (typeof TomSelect !== 'undefined') {
                            new TomSelect(select, {
                                plugins: { remove_button: { title: "Hapus CPMK" } },
                                maxOptions: 100,
                                persist: false,
                                create: false
                            });
                        }
                    }
                });
            }

            // Reference Section CRUD
            const refContainer = document.getElementById('references_container');
            document.getElementById('btnAddReference').addEventListener('click', function() {
                const row = document.createElement('div');
                row.className = 'ref-row';
                row.innerHTML = `
                    <input type="text" name="referensi_data[]" class="field-control" placeholder="Tulis rujukan referensi baru..." required>
                    <button type="button" class="btn-danger-sm btn-remove-ref" style="flex-shrink: 0;"><i class="fas fa-trash"></i></button>
                `;
                refContainer.appendChild(row);
                triggerAutoSave();
            });

            document.addEventListener('click', function(e) {
                if(e.target.closest('.btn-remove-ref')) {
                    const row = e.target.closest('.ref-row');
                    if(refContainer.querySelectorAll('.ref-row').length > 1) {
                        row.remove();
                        triggerAutoSave();
                    } else {
                        alert('Minimal harus mengisi satu referensi!');
                    }
                }
            });

            // CPL & Dosen loader
            const mkSelect = document.getElementById('mkSelect');
            const cplCheckboxContainer = document.getElementById('cpl_checkbox_container');

            function loadCpls(mkId) {
                if(!mkId) return;

                if (window.showLoader) window.showLoader();
                cplCheckboxContainer.innerHTML = '<p class="field-hint"><i class="fas fa-spinner fa-spin"></i> Memuat CPL</p>';
                const cplPromise = fetch(`{{ route('banksoal.rps.dosen.cpl', '') }}/${mkId}`)
                    .then(res => res.json())
                    .then(data => {
                        cplCheckboxContainer.innerHTML = '';
                        if(data.length === 0) {
                            cplCheckboxContainer.innerHTML = '<p class="field-hint text-rose-500">Mata kuliah ini belum dipetakan ke CPL manapun.</p>';
                            return;
                        }
                        const selectedCplIds = JSON.parse(rpsSubmitForm.dataset.selectedCplIds || '[]');
                        data.forEach(cpl => {
                            const isChecked = selectedCplIds.includes(cpl.id);
                            const item = document.createElement('label');
                            item.style.display = 'flex';
                            item.style.alignItems = 'flex-start';
                            item.style.gap = '10px';
                            item.style.fontSize = '13px';
                            item.style.cursor = 'pointer';
                            item.innerHTML = `
                                <input type="checkbox" class="cpl-checkbox-item" value="${cpl.id}" ${isChecked ? 'checked' : ''} style="margin-top: 3px; accent-color: var(--primary-blue);" data-code="${cpl.kode}" disabled>
                                <div><strong>${cpl.kode}</strong>: ${cpl.deskripsi}</div>
                            `;
                            cplCheckboxContainer.appendChild(item);
                        });
                        updateCplSelectDropdowns(data);
                    });

                const restorePromise = initPrefilledGeneratorData();

                Promise.all([cplPromise, restorePromise]).finally(() => {
                    if (window.hideLoader) window.hideLoader();
                    calculateTotalWeight();
                });
            }

            if (mkSelect && mkSelect.value) {
                loadCpls(mkSelect.value);
            }

            // CPL Select options helper
            let currentCplsList = [];
            function updateCplSelectDropdowns(cplList) {
                currentCplsList = cplList;
                document.querySelectorAll('.cpl-select-input').forEach(select => {
                    const selectedVal = select.dataset.selectedValue || select.value;
                    select.innerHTML = '<option value="">Pilih CPL</option>';
                    cplList.forEach(cpl => {
                        const opt = document.createElement('option');
                        opt.value = cpl.id;
                        opt.textContent = cpl.kode;
                        if(cpl.id == selectedVal) {
                            opt.selected = true;
                        }
                        select.appendChild(opt);
                    });
                });
            }

            // Generator CPMK row builder
            document.getElementById('addCpmkRowBtnGenerator').addEventListener('click', function() {
                const index = Date.now();
                const tpl = document.getElementById('cpmkRowTemplate').innerHTML;
                const html = tpl.replace(/__INDEX__/g, index);
                const container = document.createElement('div');
                container.innerHTML = html;
                document.getElementById('cpmkRowsGenerator').appendChild(container.firstElementChild);
                updateCplSelectDropdowns(currentCplsList);
                triggerAutoSave();
            });

            // CPMK removal
            document.addEventListener('click', function(e) {
                if(e.target.closest('[data-remove-cpmk-row]')) {
                    const row = e.target.closest('[data-cpmk-row]');
                    const rowsContainer = row.parentNode;
                    if(rowsContainer.querySelectorAll('[data-cpmk-row]').length > 1) {
                        row.remove();
                        triggerAutoSave();
                    } else {
                        alert('Minimal harus menyertakan 1 CPMK!');
                    }
                }
            });

            // Render 16 Pertemuan Grid dynamically
            const pertemuanSebelumUtsContainer = document.getElementById('pertemuan_sebelum_uts_container');
            const pertemuanSetelahUtsContainer = document.getElementById('pertemuan_setelah_uts_container');

            function renderPertemuanRows(generateDetail) {
                pertemuanSebelumUtsContainer.innerHTML = '';
                pertemuanSetelahUtsContainer.innerHTML = '';
                for(let i=1; i<=16; i++) {
                    const row = document.createElement('div');
                    row.className = `grid-table-row pertemuan-row-item`;
                    row.dataset.pertemuan = i;
                    
                    const savedMeeting = generateDetail && generateDetail.pertemuan_data ? generateDetail.pertemuan_data[i] : null;

                    if(i === 8 || i === 16) {
                        row.className = `grid-table-row pertemuan-row-item ${i === 8 ? 'uts-row' : 'uas-row'}`;
                        row.innerHTML = `
                            <div style="grid-column: span 9; width: 100%; padding: 12px 10px; background: #e2e8f0; font-weight: bold; text-align: center;">
                                ${i === 8 ? 'UJIAN TENGAH SEMESTER (UTS)' : 'UJIAN AKHIR SEMESTER (UAS)'}
                                <input type="hidden" name="pertemuan_data[${i}][pertemuan]" value="${i}">
                                <input type="hidden" name="pertemuan_data[${i}][kemampuan_akhir]" value="Evaluation">
                                <input type="hidden" name="pertemuan_data[${i}][pokok_bahasan]" value="-">
                                <input type="hidden" name="pertemuan_data[${i}][metode]" value="-">
                                <input type="hidden" name="pertemuan_data[${i}][waktu]" value="0">
                                <input type="hidden" name="pertemuan_data[${i}][pengalaman_belajar]" value="-">
                                <input type="hidden" name="pertemuan_data[${i}][target_cpmk]" value="-">
                                <input type="hidden" name="pertemuan_data[${i}][kriteria_penilaian]" value="-">
                                <input type="hidden" name="pertemuan_data[${i}][bobot]" value="0">
                            </div>
                        `;
                    } else {
                        let targetCpmkVals = [];
                        if (savedMeeting && savedMeeting.target_cpmk) {
                            if (Array.isArray(savedMeeting.target_cpmk)) {
                                targetCpmkVals = savedMeeting.target_cpmk;
                            } else if (typeof savedMeeting.target_cpmk === 'string') {
                                targetCpmkVals = savedMeeting.target_cpmk.replace(/CPMK\s*/ig, '').split(',').map(s => s.trim());
                            }
                        }

                        row.innerHTML = `
                            <div class="text-center font-bold">${i}<input type="hidden" name="pertemuan_data[${i}][pertemuan]" value="${i}"></div>
                            <div><textarea name="pertemuan_data[${i}][kemampuan_akhir]" class="field-control" rows="2" required>${savedMeeting ? (savedMeeting.kemampuan_akhir || '') : ''}</textarea></div>
                            <div><textarea name="pertemuan_data[${i}][pokok_bahasan]" class="field-control" rows="2" required>${savedMeeting ? (savedMeeting.pokok_bahasan || '') : ''}</textarea></div>
                            <div><input type="text" name="pertemuan_data[${i}][metode]" class="field-control" value="${savedMeeting ? (savedMeeting.metode || '') : ''}" required></div>
                            <div><input type="number" name="pertemuan_data[${i}][waktu]" class="field-control text-center" value="${savedMeeting ? (savedMeeting.waktu || '150') : '150'}" required></div>
                            <div><textarea name="pertemuan_data[${i}][pengalaman_belajar]" class="field-control" rows="2" required>${savedMeeting ? (savedMeeting.pengalaman_belajar || '') : ''}</textarea></div>
                            <div><select name="pertemuan_data[${i}][target_cpmk][]" class="field-control pertemuan-cpmk-select" multiple required data-selected-values='${JSON.stringify(targetCpmkVals)}'></select></div>
                            <div><textarea name="pertemuan_data[${i}][kriteria_penilaian]" class="field-control" rows="2" required>${savedMeeting ? (savedMeeting.kriteria_penilaian || '') : ''}</textarea></div>
                            <div><input type="number" name="pertemuan_data[${i}][bobot]" class="field-control text-center" value="${savedMeeting ? (savedMeeting.bobot || '5') : '5'}" min="0" max="100" required></div>
                        `;
                    }
                    if (i <= 8) pertemuanSebelumUtsContainer.appendChild(row);
                    else pertemuanSetelahUtsContainer.appendChild(row);
                }
            }

            function saveCache(nextStepNum) {
                if (window.showLoader) window.showLoader();
                const formData = new FormData(rpsSubmitForm);
                fetch("{{ route('banksoal.rps.dosen.wizard-cache') }}", {
                    method: 'POST',
                    headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                    body: formData
                })
                .then(res => res.json())
                .then(res => {
                    if(res.success && nextStepNum) loadWizardStep(nextStepNum);
                })
                .finally(() => {
                    if (window.hideLoader) window.hideLoader();
                });
            }

            function initPrefilledGeneratorData() {
                const generateDetail = rpsSubmitForm.dataset.generateDetail ? JSON.parse(rpsSubmitForm.dataset.generateDetail) : null;
                
                // Render meetings with prefilled database values
                renderPertemuanRows(generateDetail);

                if (generateDetail) {
                    // Populate references
                    if (generateDetail.referensi_data && generateDetail.referensi_data.length > 0) {
                        refContainer.innerHTML = '';
                        generateDetail.referensi_data.forEach(ref => {
                            const row = document.createElement('div');
                            row.className = 'ref-row';
                            row.innerHTML = `
                                <input type="text" name="referensi_data[]" class="field-control" value="${ref}" required>
                                <button type="button" class="btn-danger-sm btn-remove-ref" style="flex-shrink: 0;"><i class="fas fa-trash"></i></button>
                            `;
                            refContainer.appendChild(row);
                        });
                    }
                }
                return Promise.resolve();
            }

            let autoSaveTimeout = null;
            function triggerAutoSave() {
                const mkId = document.getElementById('mkSelect') ? document.getElementById('mkSelect').value : null;
                if (!mkId) return;
                if (autoSaveTimeout) clearTimeout(autoSaveTimeout);
                autoSaveTimeout = setTimeout(() => {
                    const formData = new FormData(rpsSubmitForm);
                    fetch("{{ route('banksoal.rps.dosen.wizard-cache') }}", {
                        method: 'POST',
                        headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                        body: formData
                    });
                }, 2000);
            }

            document.addEventListener('input', (e) => { if (e.target.closest('.wizard-content-section')) triggerAutoSave(); });
            document.addEventListener('change', (e) => { if (e.target.closest('.wizard-content-section')) triggerAutoSave(); });

            rpsSubmitForm.addEventListener('submit', function(e) {
                e.preventDefault();
                const method = creationMethodInput ? creationMethodInput.value : 'upload';
                
                if (method === 'upload') {
                    if (!this.checkValidity()) {
                        this.reportValidity();
                        return;
                    }
                    const confirmSubmit = confirm("Apakah kamu sudah yakin? Form yang telah dikirim tidak dapat ditarik kembali.");
                    if (!confirmSubmit) return;
                    if (window.showLoader) window.showLoader();
                    methodUploadPanel.querySelectorAll('input, select, textarea').forEach(input => {
                        if (!input.hasAttribute('data-always-disabled')) input.disabled = false;
                    });
                    this.submit();
                    return;
                }
                
                // Generator validation
                const requiredInputs = rpsSubmitForm.querySelectorAll('input[required], select[required], textarea[required]');
                let firstInvalidInput = null;
                for (let input of requiredInputs) {
                    if (input.disabled) continue;
                    if (!input.checkValidity()) {
                        firstInvalidInput = input;
                        break;
                    }
                }
                
                if (firstInvalidInput) {
                    const stepPanel = firstInvalidInput.closest('.wizard-content-section');
                    if (stepPanel) {
                        const stepNum = parseInt(stepPanel.id.replace('step_', '').replace('_panel', ''));
                        loadWizardStep(stepNum);
                    }
                    const subTabPanel = firstInvalidInput.closest('.sub-tab-panel');
                    if (subTabPanel) {
                        const subTabBtn = document.querySelector(`.sub-tab-btn[data-sub-tab="${subTabPanel.id.replace('sub_tab_', '').replace('_panel', '')}"]`);
                        if (subTabBtn) subTabBtn.click();
                    }
                    setTimeout(() => firstInvalidInput.reportValidity(), 50);
                    return;
                }
                
                const confirmSubmit = confirm("Apakah kamu sudah yakin? Form yang telah dikirim tidak dapat ditarik kembali.");
                if (!confirmSubmit) return;
                
                if (window.showLoader) window.showLoader();
                
                // Temporarily enable fields so they get serialized
                const temporarilyDisabled = [];
                methodGeneratorPanel.querySelectorAll('input, select, textarea').forEach(input => {
                    if (input.disabled && !input.hasAttribute('data-always-disabled')) {
                        input.disabled = false;
                        temporarilyDisabled.push(input);
                    }
                });

                // Ensure hidden fields for Mata Kuliah, Semester, Tahun Ajaran are included
                const mkEl = document.getElementById('mkSelect');
                if (mkEl) mkEl.disabled = false;
                
                const formData = new FormData(rpsSubmitForm);
                
                // Restore disabled state
                temporarilyDisabled.forEach(input => { input.disabled = true; });
                if (mkEl) mkEl.disabled = true;
                
                fetch(rpsSubmitForm.action, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value,
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json',
                    },
                    body: formData,
                })
                .then(response => response.json().then(data => ({ status: response.status, data })))
                .then(({ status, data }) => {
                    if (data.success && data.redirect) {
                        if (typeof Snackbar !== 'undefined' && typeof Snackbar.show === 'function') {
                            Snackbar.show(data.message || 'RPS berhasil diperbarui.', 'success', 3000);
                        }
                        setTimeout(() => { window.location.href = data.redirect; }, 1500);
                    } else {
                        if (window.hideLoader) window.hideLoader();
                        const msg = data.message || (data.errors ? Object.values(data.errors).flat().join(', ') : 'Terjadi kesalahan tidak diketahui.');
                        if (typeof Snackbar !== 'undefined' && typeof Snackbar.show === 'function') {
                            Snackbar.show('Gagal menyimpan RPS: ' + msg, 'error', 5000);
                        } else {
                            alert("Terjadi kesalahan:\n\n" + msg);
                        }
                    }
                })
                .catch(err => {
                    if (window.hideLoader) window.hideLoader();
                    if (typeof Snackbar !== 'undefined' && typeof Snackbar.show === 'function') {
                        Snackbar.show('Koneksi gagal. Silakan periksa jaringan Anda dan coba lagi.', 'error', 5000);
                    } else {
                        alert("Koneksi gagal. Silakan periksa jaringan Anda dan coba lagi.");
                    }
                });
            });
        });
    </script>
    <script src="{{ asset('modules/banksoal/js/Banksoal/components/RpsCpmkRows.js') }}?v={{ time() }}"></script>
    @endpush
</x-banksoal::layouts.dosen-admin>
