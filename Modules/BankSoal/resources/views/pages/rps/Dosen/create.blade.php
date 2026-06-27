<x-banksoal::layouts.dosen-admin>
    @section('breadcrumbs')
        <a href="{{ route('banksoal.rps.dosen.index') }}" class="text-slate-500 hover:text-primary transition-colors">Manajemen RPS</a>
        <span class="mx-2 text-slate-300">/</span>
        <span class="text-slate-800 font-semibold">Ajukan RPS Baru</span>
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

        /* Method Selection Cards */
        .method-cards { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 24px; }
        @media (max-width: 640px) { .method-cards { grid-template-columns: 1fr; } }
        .method-card { border: 2px solid var(--slate-200); border-radius: 12px; padding: 24px; cursor: pointer; transition: all .2s; background: #fff; display: flex; align-items: flex-start; gap: 16px; position: relative; }
        .method-card:hover { border-color: var(--primary-blue); transform: translateY(-2px); box-shadow: 0 4px 12px rgba(11,38,110,.05); }
        .method-card.active { border-color: var(--primary-blue); background: rgba(11, 38, 110, 0.02); }
        .method-card input[type="radio"] { position: absolute; top: 20px; right: 20px; width: 18px; height: 18px; accent-color: var(--primary-blue); }
        .method-icon { font-size: 32px; color: var(--primary-blue); background: var(--slate-100); width: 60px; height: 60px; border-radius: 10px; display: flex; align-items: center; justify-content: center; }
        .method-info h3 { font-size: 16px; font-weight: 700; margin: 0 0 6px 0; color: var(--slate-800); }
        .method-info p { font-size: 13px; color: var(--slate-500); margin: 0; }

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

        .hidden { display: none !important; }
    </style>
    @endpush

    <div class="page-header">
        <div class="header-content">
            <h1>Ajukan RPS Baru</h1>
            <p>Pilih metode pembuatan RPS di bawah ini untuk memulai pengajuan.</p>
        </div>
        <a href="{{ route('banksoal.rps.dosen.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Kembali
        </a>
    </div>

    <x-banksoal::notification.alerts />

    @php
        $creationMethod = old('creation_method', 'upload');
    @endphp
    <!-- 1. Selection of Creation Method -->
    <div class="method-cards">
        <div class="method-card {{ $creationMethod === 'upload' ? 'active' : '' }}" data-method="upload">
            <input type="radio" name="creation_method_trigger" value="upload" {{ $creationMethod === 'upload' ? 'checked' : '' }}>
            <div class="method-icon"><i class="fas fa-file-pdf"></i></div>
            <div class="method-info">
                <h3>Upload Berkas RPS (PDF)</h3>
                <p>Unggah file dokumen RPS berformat PDF yang sudah Anda buat secara manual.</p>
            </div>
        </div>
        <div class="method-card {{ $creationMethod === 'generator' ? 'active' : '' }}" data-method="generator">
            <input type="radio" name="creation_method_trigger" value="generator" {{ $creationMethod === 'generator' ? 'checked' : '' }}>
            <div class="method-icon"><i class="fas fa-magic"></i></div>
            <div class="method-info">
                <h3>Buat RPS Otomatis (Progressive Form)</h3>
                <p>Isi data melalui form bertahap, dan biarkan sistem merender dokumen PDF RPS & Kontrak Kuliah Anda.</p>
            </div>
        </div>
    </div>

    <!-- MAIN FORM -->
    <form action="{{ route('banksoal.rps.dosen.store') }}" method="POST" enctype="multipart/form-data" id="rpsSubmitForm"
        data-route-cpl="{{ route('banksoal.rps.dosen.cpl') }}"
        data-route-dosen="{{ route('banksoal.rps.dosen.dosen') }}"
        data-cpmk-row-builder="1">
        @csrf
        <input type="hidden" name="creation_method" id="creation_method_input" value="{{ $creationMethod }}">

        <!-- MUTUAL HEADER INFO -->
        <div class="form-card">
            <div class="form-card-title"><i class="fas fa-book-open"></i> Informasi Dasar RPS</div>
            <div class="form-row">
                <div class="form-group">
                    <label class="field-label">Mata Kuliah <span class="req">*</span></label>
                    <select name="mata_kuliah_id" id="mkSelect" class="field-control" required>
                        <option value="" disabled selected>-- Pilih Mata Kuliah --</option>
                        @foreach($mataKuliahs as $mk)
                            <option value="{{ $mk->id }}" {{ old('mata_kuliah_id') == $mk->id ? 'selected' : '' }}>
                                {{ $mk->kode }} – {{ $mk->nama }} ({{ $mk->sks }} SKS)
                            </option>
                        @endforeach
                    </select>
                    @error('mata_kuliah_id')<p class="field-error">{{ $message }}</p>@enderror
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
                        <option value="Ganjil" {{ $semester == 'Ganjil' ? 'selected' : '' }}>Ganjil</option>
                        <option value="Genap"  {{ $semester == 'Genap'  ? 'selected' : '' }}>Genap</option>
                    </select>
                    <input type="hidden" name="semester" value="{{ $semester }}">
                </div>
                <div class="form-group">
                    <label class="field-label">Tahun Ajaran <span class="req">*</span></label>
                    <select id="tahun_ajaran" class="field-control bg-slate-100 border-slate-200 text-slate-500 cursor-not-allowed" disabled>
                        @foreach($tahunAjarans as $ta)
                            <option value="{{ $ta }}" {{ $ta == $academicYear ? 'selected' : '' }}>{{ $ta }}</option>
                        @endforeach
                    </select>
                    <input type="hidden" name="tahun_ajaran" value="{{ $academicYear }}">
                </div>
            </div>
        </div>

        <!-- ==================== METHOD A: UPLOAD PDF (Default) ==================== -->
        <div id="method_upload_panel" class="{{ $creationMethod === 'upload' ? '' : 'hidden' }}">
            {{-- CPMK Section --}}
            <div class="form-card">
                <div class="form-card-title"><i class="fas fa-list-check"></i> Capaian Pembelajaran Mata Kuliah (CPMK)</div>
                <div class="cpmk-section">
                    <div class="cpmk-header">
                        <div>
                            <p style="margin: 0; font-size: 13px; color: var(--slate-600);">Baris CPMK yang diajukan dalam RPS ini.</p>
                        </div>
                        <button type="button" class="btn btn-primary" id="addCpmkRowBtn" style="padding:8px 16px;font-size:13px;">
                            <i class="fas fa-plus"></i> Tambah Baris
                        </button>
                    </div>

                    <div class="cpmk-col-head">
                        <div>CPL</div><div>Kode CPMK</div><div>KKO</div><div>Objek</div><div>Konteks</div><div></div>
                    </div>

                    <div id="cpmkRows" class="cpmk-rows" data-cpmk-rows>
                        <div class="cpmk-row" data-cpmk-row data-row-index="0">
                            <div>
                                <select name="cpmk_rows[0][cpl_id]" class="field-control cpl-select-input" data-cpmk-cpl-select style="font-size:13px;" required>
                                    <option value="">Pilih CPL</option>
                                </select>
                            </div>
                            <div>
                                <div style="display:flex;align-items:center;gap:6px;">
                                    <span style="font-size:12px;font-weight:600;color:var(--slate-500);padding:10px 8px;background:var(--slate-50);border:1px solid var(--slate-200);border-radius:8px;">CPMK</span>
                                    <input type="text" name="cpmk_rows[0][kode]" class="field-control" style="font-size:13px;" placeholder="1" required>
                                </div>
                            </div>
                            <div>
                                <select name="cpmk_rows[0][kko]" class="field-control kko-select-input" style="font-size:13px;" required>
                                    <option value="">Pilih KKO</option>
                                    @foreach([
                                        'C1'=>'Mengingat','C2'=>'Memahami','C3'=>'Menerapkan','C4'=>'Menganalisis','C5'=>'Mengevaluasi','C6'=>'Mencipta',
                                        'P1'=>'Meniru','P2'=>'Menyesuaikan','P3'=>'Membiasakan','P4'=>'Menguasai','P5'=>'Mahir',
                                        'A1'=>'Menerima','A2'=>'Merespon','A3'=>'Menilai','A4'=>'Mengorganisasi','A5'=>'Menghayati',
                                    ] as $val => $lbl)
                                        <option value="{{ $val }}">{{ $val }} – {{ $lbl }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <input type="text" name="cpmk_rows[0][objek]" class="field-control objek-input" style="font-size:13px;" placeholder="merancang sistem IoT" required>
                            </div>
                            <div>
                                <input type="text" name="cpmk_rows[0][konteks]" class="field-control" style="font-size:13px;" placeholder="sesuai kebutuhan">
                            </div>
                            <div style="display:flex;align-items:flex-end;">
                                <button type="button" class="btn-danger-sm" data-remove-cpmk-row><i class="fas fa-trash"></i></button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="form-card">
                <div class="form-card-title"><i class="fas fa-file-pdf"></i> Unggah Dokumen RPS</div>
                <x-banksoal::ui.upload-zone
                    name="dokumen"
                    inputId="fileInput"
                    accept=".pdf"
                    maxLabel="PDF (Maks. 5MB)"
                    :required="true"
                />
                @error('dokumen')<p class="field-error" style="margin-top:8px;">{{ $message }}</p>@enderror
            </div>

            <div class="form-card">
                <div class="form-card-title"><i class="fas fa-comment"></i> Catatan Pengajuan</div>
                <div class="form-group">
                    <textarea name="catatan" class="field-control" rows="3" placeholder="Opsional. Tambahkan catatan untuk validator GPM."></textarea>
                </div>
            </div>

            <div class="form-actions" style="display: flex; justify-content: flex-end; gap: 12px; margin-top: 24px;">
                <a href="{{ route('banksoal.rps.dosen.index') }}" class="btn btn-secondary">Batal</a>
                <button type="submit" class="btn btn-primary" id="btnSubmitManual"><i class="fas fa-paper-plane"></i> Ajukan RPS</button>
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
                            <p class="field-hint">Silakan pilih Mata Kuliah di atas terlebih dahulu untuk memuat daftar CPL.</p>
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
                            <button type="button" class="btn btn-primary" id="addCpmkRowBtnGenerator" style="padding:8px 16px;font-size:13px;">
                                <i class="fas fa-plus"></i> Tambah Baris
                            </button>
                        </div>

                        <div class="cpmk-col-head">
                            <div>CPL</div><div>Kode CPMK</div><div>KKO</div><div>Objek</div><div>Konteks</div><div></div>
                        </div>

                        <div id="cpmkRowsGenerator" class="cpmk-rows">
                            <!-- Rows will be injected and bound dynamically -->
                        </div>
                    </div>
                </div>

                <div class="form-card">
                    <div class="form-card-title"><i class="fas fa-align-left"></i> Deskripsi Singkat Mata Kuliah</div>
                    <div class="form-group">
                        <label class="field-label">Deskripsi Mata Kuliah <span class="req">*</span></label>
                        <textarea name="deskripsi_mk" id="deskripsi_mk" class="field-control" rows="5" placeholder="Tuliskan deskripsi singkat mengenai materi, fokus kajian, dan tujuan pembelajaran mata kuliah ini..."></textarea>
                    </div>
                </div>

                <div style="display: flex; justify-content: flex-end; gap: 12px;">
                    <a href="{{ route('banksoal.rps.dosen.index') }}" class="btn btn-secondary">Batal</a>
                    <button type="button" class="btn btn-primary btn-next-step" data-next="2">Next <i class="fas fa-chevron-right"></i></button>
                </div>
            </div>

            <!-- STEP 2: KONTRAK KULIAH -->
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
                                <td><textarea name="penilaian_data[0][komponen]" class="field-control" rows="2" placeholder="Aktivitas Kelas / Diskusi" required></textarea></td>
                                <td><input type="number" name="penilaian_data[0][bobot]" class="field-control text-center weight-input" min="0" max="100" value="0" required></td>
                                <td><select name="penilaian_data[0][cpmk][]" class="field-control cpmk-target-select" multiple required></select></td>
                            </tr>
                            <tr>
                                <td class="text-center">2</td>
                                <td class="font-bold">Hasil Proyek<input type="hidden" name="penilaian_data[1][poin]" value="Hasil Proyek"></td>
                                <td><textarea name="penilaian_data[1][komponen]" class="field-control" rows="2" placeholder="Hasil Proyek Desain" required></textarea></td>
                                <td><input type="number" name="penilaian_data[1][bobot]" class="field-control text-center weight-input" min="0" max="100" value="0" required></td>
                                <td><select name="penilaian_data[1][cpmk][]" class="field-control cpmk-target-select" multiple required></select></td>
                            </tr>
                            <tr>
                                <td class="text-center">3</td>
                                <td class="font-bold">Tugas<input type="hidden" name="penilaian_data[2][poin]" value="Tugas"></td>
                                <td><textarea name="penilaian_data[2][komponen]" class="field-control" rows="2" placeholder="Tugas Kelompok" required></textarea></td>
                                <td><input type="number" name="penilaian_data[2][bobot]" class="field-control text-center weight-input" min="0" max="100" value="0" required></td>
                                <td><select name="penilaian_data[2][cpmk][]" class="field-control cpmk-target-select" multiple required></select></td>
                            </tr>
                            <tr>
                                <td class="text-center">4</td>
                                <td class="font-bold">Quiz<input type="hidden" name="penilaian_data[3][poin]" value="Quiz"></td>
                                <td><textarea name="penilaian_data[3][komponen]" class="field-control" rows="2" placeholder="Tugas Individu / Kuis" required></textarea></td>
                                <td><input type="number" name="penilaian_data[3][bobot]" class="field-control text-center weight-input" min="0" max="100" value="0" required></td>
                                <td><select name="penilaian_data[3][cpmk][]" class="field-control cpmk-target-select" multiple required></select></td>
                            </tr>
                            <!-- Kognitif has UTS and UAS -->
                            <tr>
                                <td class="text-center" rowspan="2">5</td>
                                <td class="font-bold" rowspan="2">Kognitif / Pengetahuan<input type="hidden" name="penilaian_data[4][poin]" value="Kognitif / Pengetahuan"></td>
                                <td>UTS (Ujian Tengah Semester)<input type="hidden" name="penilaian_data[4][sub_rows][0][komponen]" value="UTS"></td>
                                <td><input type="number" name="penilaian_data[4][sub_rows][0][bobot]" class="field-control text-center weight-input" min="0" max="100" value="0" required></td>
                                <td><select name="penilaian_data[4][sub_rows][0][cpmk][]" class="field-control cpmk-target-select" multiple required></select></td>
                            </tr>
                            <tr>
                                <td>UAS (Ujian Akhir Semester)<input type="hidden" name="penilaian_data[4][sub_rows][1][komponen]" value="UAS"></td>
                                <td><input type="number" name="penilaian_data[4][sub_rows][1][bobot]" class="field-control text-center weight-input" min="0" max="100" value="0" required></td>
                                <td><select name="penilaian_data[4][sub_rows][1][cpmk][]" class="field-control cpmk-target-select" multiple required></select></td>
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
                                <input type="text" name="referensi_data[]" class="field-control" placeholder="Contoh: Pressman, R.S. (2015). Software Engineering: A Practitioner's Approach. McGraw-Hill." required>
                                <button type="button" class="btn-danger-sm btn-remove-ref" style="flex-shrink: 0;"><i class="fas fa-trash"></i></button>
                            </div>
                        </div>
                        <button type="button" class="btn btn-secondary" id="btnAddReference" style="margin-top:12px; padding:8px 16px; font-size:13px;">
                            <i class="fas fa-plus"></i> Tambah Referensi
                        </button>
                    </div>

                    <div class="form-card">
                        <div class="form-card-title"><i class="fas fa-comment"></i> Catatan Pengajuan</div>
                        <div class="form-group">
                            <textarea name="catatan" class="field-control" rows="3" placeholder="Opsional. Tambahkan catatan untuk validator GPM."></textarea>
                        </div>
                    </div>
                    
                    <div style="display: flex; justify-content: space-between; gap: 12px; margin-top: 16px;">
                        <button type="button" class="btn btn-secondary btn-prev-sub-tab" data-prev-tab="setelah-uts"><i class="fas fa-chevron-left"></i> Back</button>
                        <button type="submit" class="btn btn-primary" id="btnSubmitGenerator"><i class="fas fa-paper-plane"></i> Ajukan & Generate PDF</button>
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
                    @foreach([
                        'C1'=>'Mengingat','C2'=>'Memahami','C3'=>'Menerapkan','C4'=>'Menganalisis','C5'=>'Mengevaluasi','C6'=>'Mencipta',
                        'P1'=>'Meniru','P2'=>'Menyesuaikan','P3'=>'Membiasakan','P4'=>'Menguasai','P5'=>'Mahir',
                        'A1'=>'Menerima','A2'=>'Merespon','A3'=>'Menilai','A4'=>'Mengorganisasi','A5'=>'Menghayati',
                    ] as $val => $lbl)
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
            const methodCards = document.querySelectorAll('.method-card');
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

            // Toggle logic handles are placed after loadWizardStep definition to avoid race conditions.

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

                // Lock Informasi Dasar fields after Step 1
                const mkEl = document.getElementById('mkSelect');
                const dosenEl = document.getElementById('dosenSelect');
                if (stepNum > 1) {
                    if (mkEl) {
                        let shadow = document.getElementById('mkSelect_shadow');
                        if (!shadow) {
                            shadow = document.createElement('input');
                            shadow.type = 'hidden';
                            shadow.name = mkEl.name;
                            shadow.id = 'mkSelect_shadow';
                            mkEl.parentNode.appendChild(shadow);
                        }
                        shadow.value = mkEl.value;
                        mkEl.name = ''; 
                        mkEl.disabled = true;
                        mkEl.style.opacity = '0.7';
                    }
                    if (dosenEl) {
                        const tsWrapper = dosenEl.nextElementSibling;
                        if (tsWrapper && tsWrapper.classList.contains('ts-wrapper')) {
                            tsWrapper.style.pointerEvents = 'none';
                            tsWrapper.style.opacity = '0.7';
                        } else {
                            dosenEl.style.pointerEvents = 'none';
                            dosenEl.style.opacity = '0.7';
                        }
                    }
                } else {
                    if (mkEl) {
                        const shadow = document.getElementById('mkSelect_shadow');
                        if (shadow) { shadow.remove(); }
                        mkEl.name = 'mata_kuliah_id';
                        mkEl.disabled = false;
                        mkEl.style.opacity = '';
                    }
                    if (dosenEl) {
                        const tsWrapper = dosenEl.nextElementSibling;
                        if (tsWrapper && tsWrapper.classList.contains('ts-wrapper')) {
                            tsWrapper.style.pointerEvents = '';
                            tsWrapper.style.opacity = '';
                        } else {
                            dosenEl.style.pointerEvents = '';
                            dosenEl.style.opacity = '';
                        }
                    }
                }

                if (stepNum === 2 || stepNum === 3) {
                    populateCpmkDropdowns();
                }
            }

            function setCreationMethod(method) {
                creationMethodInput.value = method;
                methodCards.forEach(c => {
                    const radio = c.querySelector('input[type="radio"]');
                    if (c.dataset.method === method) {
                        c.classList.add('active');
                        if (radio) radio.checked = true;
                    } else {
                        c.classList.remove('active');
                        if (radio) radio.checked = false;
                    }
                });

                if(method === 'upload') {
                    methodUploadPanel.classList.remove('hidden');
                    methodGeneratorPanel.classList.add('hidden');
                    const mkEl = document.getElementById('mkSelect');
                    if (mkEl) mkEl.required = true;
                    const fileInput = document.getElementById('fileInput');
                    if (fileInput) fileInput.required = true;
                    
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

            // Toggle Creation Method
            methodCards.forEach(card => {
                card.addEventListener('click', function() {
                    setCreationMethod(this.dataset.method);
                });
            });

            // Initial toggle state
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
                const rows = document.querySelectorAll('#cpmkRowsGenerator .cpmk-row');
                rows.forEach(row => {
                    const cplSelect = row.querySelector('.cpl-select-input');
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
                        const match = strVal.match(/cpl[-_\s]*0*(\d+)\.(\d+)/i) || strVal.match(/[-_\s]*0*(\d+)\.(\d+)/);
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

            mkSelect.addEventListener('change', function() {
                const mkId = this.value;
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
                        data.forEach(cpl => {
                            const item = document.createElement('label');
                            item.style.display = 'flex';
                            item.style.alignItems = 'flex-start';
                            item.style.gap = '10px';
                            item.style.fontSize = '13px';
                            item.style.cursor = 'pointer';
                            item.innerHTML = `
                                <input type="checkbox" class="cpl-checkbox-item" value="${cpl.id}" style="margin-top: 3px; accent-color: var(--primary-blue);" data-code="${cpl.kode}">
                                <div><strong>${cpl.kode}</strong>: ${cpl.deskripsi}</div>
                            `;
                            cplCheckboxContainer.appendChild(item);
                        });
                        updateCplSelectDropdowns(data);
                    });
                const cachePromise = restoreCache(mkId);

                Promise.all([cplPromise, cachePromise]).finally(() => {
                    if (window.hideLoader) window.hideLoader();
                });
            });

            if (mkSelect && mkSelect.value) {
                mkSelect.dispatchEvent(new Event('change'));
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

            function renderPertemuanRows() {
                pertemuanSebelumUtsContainer.innerHTML = '';
                pertemuanSetelahUtsContainer.innerHTML = '';
                for(let i=1; i<=16; i++) {
                    const row = document.createElement('div');
                    row.className = `grid-table-row pertemuan-row-item`;
                    row.dataset.pertemuan = i;
                    
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
                        row.innerHTML = `
                            <div class="text-center font-bold">${i}<input type="hidden" name="pertemuan_data[${i}][pertemuan]" value="${i}"></div>
                            <div><textarea name="pertemuan_data[${i}][kemampuan_akhir]" class="field-control" rows="2" required></textarea></div>
                            <div><textarea name="pertemuan_data[${i}][pokok_bahasan]" class="field-control" rows="2" required></textarea></div>
                            <div><input type="text" name="pertemuan_data[${i}][metode]" class="field-control" required></div>
                            <div><input type="number" name="pertemuan_data[${i}][waktu]" class="field-control text-center" value="150" required></div>
                            <div><textarea name="pertemuan_data[${i}][pengalaman_belajar]" class="field-control" rows="2" required></textarea></div>
                            <div><select name="pertemuan_data[${i}][target_cpmk][]" class="field-control pertemuan-cpmk-select" multiple required></select></div>
                            <div><textarea name="pertemuan_data[${i}][kriteria_penilaian]" class="field-control" rows="2" required></textarea></div>
                            <div><input type="number" name="pertemuan_data[${i}][bobot]" class="field-control text-center" value="5" min="0" max="100" required></div>
                        `;
                    }
                    if (i <= 8) pertemuanSebelumUtsContainer.appendChild(row);
                    else pertemuanSetelahUtsContainer.appendChild(row);
                }
            }
            renderPertemuanRows();

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

            function restoreCache(mkId) {
                return fetch("{{ route('banksoal.rps.dosen.get-wizard-cache', ['mkId' => 'PLACEHOLDER']) }}".replace('PLACEHOLDER', mkId))
                    .then(res => res.json())
                    .then(res => {
                        if(res.success && res.data) {
                            const cache = res.data;
                            
                            // 1. Restore Step 1 CPMK rows
                            if(cache.cpmk_rows && cache.cpmk_rows.length > 0) {
                                cpmkRowsGenerator.innerHTML = '';
                                cpmkCount = 0;
                                cache.cpmk_rows.forEach((row, i) => {
                                    const index = cpmkCount++;
                                    const tpl = document.getElementById('cpmkRowTemplate').innerHTML;
                                    const html = tpl.replace(/__INDEX__/g, index);
                                    const container = document.createElement('div');
                                    container.innerHTML = html;
                                    cpmkRowsGenerator.appendChild(container.firstElementChild);
                                });

                                // Update CPL options for newly created rows
                                updateCplSelectDropdowns(currentCplsList);

                                cache.cpmk_rows.forEach((row, i) => {
                                    const rowEl = cpmkRowsGenerator.querySelector(`[data-row-index="${i}"]`);
                                    if(rowEl) {
                                        const cplSelect = rowEl.querySelector('select[name*="[cpl_id]"]');
                                        if(cplSelect) {
                                            cplSelect.dataset.selectedValue = row.cpl_id;
                                            cplSelect.value = row.cpl_id;
                                        }
                                        
                                        const kodeInput = rowEl.querySelector('input[name*="[kode]"]');
                                        if(kodeInput) kodeInput.value = row.kode;
                                        
                                        const kkoSelect = rowEl.querySelector('select[name*="[kko]"]');
                                        if(kkoSelect) kkoSelect.value = row.kko;
                                        
                                        const objekInput = rowEl.querySelector('input[name*="[objek]"]');
                                        if(objekInput) objekInput.value = row.objek;
                                        
                                        const konteksInput = rowEl.querySelector('input[name*="[konteks]"]');
                                        if(konteksInput) konteksInput.value = row.konteks || '';
                                    }
                                });
                            } else {
                                buildInitialCpmkGenerator();
                            }

                            // 2. Restore Step 2 Penilaian
                            if(cache.penilaian_data) {
                                cache.penilaian_data.forEach((item, index) => {
                                    const komponenEl = document.querySelector(`textarea[name="penilaian_data[${index}][komponen]"]`);
                                    const bobotEl = document.querySelector(`input[name="penilaian_data[${index}][bobot]"]`);
                                    const cpmkSelect = document.querySelector(`select[name="penilaian_data[${index}][cpmk][]"]`);
                                    
                                    if(komponenEl) komponenEl.value = item.komponen || '';
                                    if(bobotEl) bobotEl.value = item.bobot || 0;
                                    if(cpmkSelect && item.cpmk) {
                                        const vals = Array.isArray(item.cpmk) ? item.cpmk : [item.cpmk];
                                        cpmkSelect.dataset.selectedValues = JSON.stringify(vals);
                                    }
                                });
                                // Restore sub_rows for UTS/UAS
                                if(cache.penilaian_data[4] && cache.penilaian_data[4].sub_rows) {
                                    const utsBobot = document.querySelector(`input[name="penilaian_data[4][sub_rows][0][bobot]"]`);
                                    const utsSelect = document.querySelector(`select[name="penilaian_data[4][sub_rows][0][cpmk][]"]`);
                                    const uasBobot = document.querySelector(`input[name="penilaian_data[4][sub_rows][1][bobot]"]`);
                                    const uasSelect = document.querySelector(`select[name="penilaian_data[4][sub_rows][1][cpmk][]"]`);
                                    
                                    if(utsBobot) utsBobot.value = cache.penilaian_data[4].sub_rows[0].bobot || 0;
                                    if(utsSelect && cache.penilaian_data[4].sub_rows[0].cpmk) {
                                        const vals = Array.isArray(cache.penilaian_data[4].sub_rows[0].cpmk) ? cache.penilaian_data[4].sub_rows[0].cpmk : [cache.penilaian_data[4].sub_rows[0].cpmk];
                                        utsSelect.dataset.selectedValues = JSON.stringify(vals);
                                    }
                                    if(uasBobot) uasBobot.value = cache.penilaian_data[4].sub_rows[1].bobot || 0;
                                    if(uasSelect && cache.penilaian_data[4].sub_rows[1].cpmk) {
                                        const vals = Array.isArray(cache.penilaian_data[4].sub_rows[1].cpmk) ? cache.penilaian_data[4].sub_rows[1].cpmk : [cache.penilaian_data[4].sub_rows[1].cpmk];
                                        uasSelect.dataset.selectedValues = JSON.stringify(vals);
                                    }
                                }
                                calculateTotalWeight();
                            }

                            // Restore Step 2 Deskripsi
                            if(cache.deskripsi_mk) {
                                document.getElementById('deskripsi_mk').value = cache.deskripsi_mk;
                            }

                            // Restore Step 3 Pertemuan
                            if(cache.pertemuan_data) {
                                for(let key in cache.pertemuan_data) {
                                    const item = cache.pertemuan_data[key];
                                    if(document.querySelector(`textarea[name="pertemuan_data[${key}][kemampuan_akhir]"]`)) {
                                        document.querySelector(`textarea[name="pertemuan_data[${key}][kemampuan_akhir]"]`).value = item.kemampuan_akhir || '';
                                        document.querySelector(`textarea[name="pertemuan_data[${key}][pokok_bahasan]"]`).value = item.pokok_bahasan || '';
                                        document.querySelector(`input[name="pertemuan_data[${key}][metode]"]`).value = item.metode || '';
                                        document.querySelector(`input[name="pertemuan_data[${key}][waktu]"]`).value = item.waktu || '150';
                                        document.querySelector(`textarea[name="pertemuan_data[${key}][pengalaman_belajar]"]`).value = item.pengalaman_belajar || '';
                                        document.querySelector(`textarea[name="pertemuan_data[${key}][kriteria_penilaian]"]`).value = item.kriteria_penilaian || '';
                                        document.querySelector(`input[name="pertemuan_data[${key}][bobot]"]`).value = item.bobot || '';

                                        const cpmkSelect = document.querySelector(`select[name="pertemuan_data[${key}][target_cpmk][]"]`);
                                        if (cpmkSelect && item.target_cpmk) {
                                            let vals = [];
                                            if (Array.isArray(item.target_cpmk)) {
                                                vals = item.target_cpmk;
                                            } else if (typeof item.target_cpmk === 'string') {
                                                vals = item.target_cpmk.split(',').map(s => s.trim().replace(/^cpmk\s*/i, ''));
                                            }
                                            cpmkSelect.dataset.selectedValues = JSON.stringify(vals);
                                        }
                                    }
                                }
                            }

                            // Restore References
                            if(cache.referensi_data) {
                                refContainer.innerHTML = '';
                                cache.referensi_data.forEach(ref => {
                                    const row = document.createElement('div');
                                    row.className = 'ref-row';
                                    row.innerHTML = `
                                        <input type="text" name="referensi_data[]" class="field-control" value="${ref}" required>
                                        <button type="button" class="btn-danger-sm btn-remove-ref" style="flex-shrink: 0;"><i class="fas fa-trash"></i></button>
                                    `;
                                    refContainer.appendChild(row);
                                });
                            }
                        } else {
                            buildInitialCpmkGenerator();
                        }
                    });
            }

            const buildInitialCpmkGenerator = () => {
                document.getElementById('cpmkRowsGenerator').innerHTML = '';
                const tpl = document.getElementById('cpmkRowTemplate').innerHTML;
                document.getElementById('cpmkRowsGenerator').innerHTML = tpl.replace(/__INDEX__/g, Date.now());
                if (currentCplsList.length > 0) updateCplSelectDropdowns(currentCplsList);
            };
            buildInitialCpmkGenerator();

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
                console.log("Form submit event triggered!");
                const method = creationMethodInput ? creationMethodInput.value : 'generator';
                console.log("Creation method:", method);
                
                if (method === 'upload') {
                    if (!this.checkValidity()) {
                        this.reportValidity();
                        return;
                    }
                    const confirmSubmit = confirm("Apakah kamu sudah yakin? Form yang telah dikirim tidak dapat ditarik kembali.");
                    if (!confirmSubmit) return;
                    if (window.showLoader) window.showLoader();
                    // Re-enable all disabled inputs in upload panel before submit
                    methodUploadPanel.querySelectorAll('input, select, textarea').forEach(input => {
                        if (!input.hasAttribute('data-always-disabled')) input.disabled = false;
                    });
                    this.submit();
                    return;
                }
                
                // Generator mode - validate visible/non-disabled required inputs
                const requiredInputs = rpsSubmitForm.querySelectorAll('input[required], select[required], textarea[required]');
                console.log("Total required inputs found:", requiredInputs.length);
                
                let firstInvalidInput = null;
                for (let input of requiredInputs) {
                    if (input.disabled) {
                        console.log("Skipping disabled input:", input.name || input.id, "disabled:", input.disabled);
                        continue;
                    }
                    if (!input.checkValidity()) {
                        console.log("Found invalid input:", input.name || input.id, "value:", input.value, "validationMessage:", input.validationMessage);
                        firstInvalidInput = input;
                        break;
                    }
                }
                
                if (firstInvalidInput) {
                    console.log("Form submission prevented due to invalid input:", firstInvalidInput.name || firstInvalidInput.id);
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
                
                console.log("All client-side validations passed. Submitting via AJAX...");
                if (window.showLoader) window.showLoader();
                
                // Re-enable ALL inputs in generator panel (including disabled ones from wizard steps)
                // so they get picked up by FormData
                const temporarilyDisabled = [];
                methodGeneratorPanel.querySelectorAll('input, select, textarea').forEach(input => {
                    if (input.disabled && !input.hasAttribute('data-always-disabled')) {
                        input.disabled = false;
                        temporarilyDisabled.push(input);
                    }
                });
                
                // Also ensure mkSelect_shadow has the right value
                const mkShadow = document.getElementById('mkSelect_shadow');
                const mkEl = document.getElementById('mkSelect');
                if (mkShadow && mkEl) {
                    mkShadow.value = mkEl.value || mkShadow.value;
                } else if (mkEl && !mkEl.disabled) {
                    // mkSelect still has name, it will be included
                }
                
                const formData = new FormData(rpsSubmitForm);
                
                // Restore disabled state
                temporarilyDisabled.forEach(input => { input.disabled = true; });
                
                console.log("FormData entries:");
                for (let [key, val] of formData.entries()) {
                    if (key !== '_token') console.log(key, '=', typeof val === 'object' ? '[File]' : val);
                }
                
                // Submit via fetch with AJAX header so controller returns JSON
                fetch(rpsSubmitForm.action, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value,
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json',
                    },
                    body: formData,
                })
                .then(response => {
                    console.log("Server response status:", response.status);
                    return response.json().then(data => ({ status: response.status, data }));
                })
                .then(({ status, data }) => {
                    console.log("Server JSON response:", data);
                    if (data.success && data.redirect) {
                        // Show success snackbar then redirect
                        if (typeof Snackbar !== 'undefined' && typeof Snackbar.show === 'function') {
                            Snackbar.show(data.message || 'RPS berhasil disimpan dan sedang menunggu verifikasi GPM.', 'success', 3000);
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
                    console.error("Fetch error:", err);
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
