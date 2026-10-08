<x-banksoal::layouts.dosen-admin>
    @section('breadcrumbs')
        <a href="{{ route('banksoal.rps.dosen.index') }}"
            class="text-slate-500 hover:text-primary transition-colors">Manajemen RPS</a>
        <span class="mx-2 text-slate-300">/</span>
        <span class="text-slate-800 font-semibold">Ajukan RPS Baru</span>
    @endsection

    @push('styles')
        <style>
            :root {
                --primary-blue: rgb(11, 38, 110);
                --primary-hover: rgb(8, 28, 82);
                --danger-red: #ef4444;
                --slate-50: #f8fafc;
                --slate-100: #f1f5f9;
                --slate-200: #e2e8f0;
                --slate-300: #cbd5e1;
                --slate-400: #94a3b8;
                --slate-500: #64748b;
                --slate-600: #475569;
                --slate-700: #334155;
                --slate-800: #1e293b;
            }

            .page-header {
                display: flex;
                justify-content: space-between;
                align-items: flex-start;
                margin-bottom: 24px;
                gap: 16px;
                flex-wrap: wrap;
            }

            .header-content h1 {
                font-size: 26px;
                font-weight: 700;
                color: var(--slate-800);
                margin: 0;
                letter-spacing: -0.5px;
            }

            .header-content p {
                font-size: 14px;
                color: var(--slate-500);
                margin: 6px 0 0 0;
            }

            .btn {
                padding: 10px 22px;
                border-radius: 9px;
                border: none;
                font-weight: 600;
                font-size: 14px;
                cursor: pointer;
                transition: all .2s;
                display: inline-flex;
                align-items: center;
                gap: 6px;
                text-decoration: none;
            }

            .btn-primary {
                background: var(--primary-blue);
                color: #fff;
                box-shadow: 0 2px 4px rgba(11, 38, 110, .15);
            }

            .btn-primary:hover:not(:disabled) {
                background: var(--primary-hover);
                transform: translateY(-1px);
                box-shadow: 0 4px 8px rgba(11, 38, 110, .2);
            }

            .btn-secondary {
                background: #fff;
                color: var(--slate-700);
                border: 1px solid var(--slate-300);
            }

            .btn-secondary:hover {
                background: var(--slate-50);
                border-color: var(--slate-400);
            }

            .btn-danger-sm {
                height: 40px;
                width: 40px;
                background: none;
                border: 1px solid #fecaca;
                border-radius: 9px;
                color: #ef4444;
                cursor: pointer;
                transition: all .2s;
                display: flex;
                align-items: center;
                justify-content: center;
            }

            .btn-danger-sm:hover {
                background: #fff1f2;
            }

            .form-card {
                background: #fff;
                border: 1px solid var(--slate-200);
                border-radius: 14px;
                padding: 28px;
                box-shadow: 0 1px 4px rgba(0, 0, 0, .05);
                margin-bottom: 24px;
            }

            .form-card-title {
                font-size: 16px;
                font-weight: 700;
                color: var(--slate-800);
                margin: 0 0 20px 0;
                padding-bottom: 14px;
                border-bottom: 1px solid var(--slate-100);
                display: flex;
                align-items: center;
                gap: 8px;
            }

            .form-card-title i {
                color: var(--primary-blue);
            }

            .form-row {
                display: grid;
                grid-template-columns: 1fr 1fr;
                gap: 20px;
                margin-bottom: 20px;
            }

            @media (max-width: 640px) {
                .form-row {
                    grid-template-columns: 1fr;
                }
            }

            .form-group {
                margin-bottom: 0;
            }

            .form-group-full {
                margin-bottom: 20px;
            }

            label.field-label {
                display: block;
                margin-bottom: 7px;
                font-size: 13px;
                font-weight: 600;
                color: var(--slate-700);
            }

            label.field-label .req {
                color: var(--danger-red);
                margin-left: 2px;
            }

            .field-control {
                width: 100%;
                padding: 10px 13px;
                border: 1px solid var(--slate-300);
                border-radius: 9px;
                font-size: 14px;
                color: var(--slate-800);
                background: #fff;
                transition: border-color .2s, box-shadow .2s;
            }

            .field-control:focus {
                outline: none;
                border-color: var(--primary-blue);
                box-shadow: 0 0 0 3px rgba(11, 38, 110, .1);
            }

            .field-control:disabled {
                background: var(--slate-50);
                color: var(--slate-400);
                cursor: not-allowed;
            }

            .field-hint {
                font-size: 12px;
                color: var(--slate-500);
                margin-top: 5px;
            }

            .field-error {
                font-size: 12px;
                color: var(--danger-red);
                margin-top: 5px;
            }

            /* Method Selection Cards */
            .method-cards {
                display: grid;
                grid-template-columns: 1fr 1fr;
                gap: 20px;
                margin-bottom: 24px;
            }

            @media (max-width: 640px) {
                .method-cards {
                    grid-template-columns: 1fr;
                }
            }

            .method-card {
                border: 2px solid var(--slate-200);
                border-radius: 12px;
                padding: 24px;
                cursor: pointer;
                transition: all .2s;
                background: #fff;
                display: flex;
                align-items: flex-start;
                gap: 16px;
                position: relative;
            }

            .method-card:hover {
                border-color: var(--primary-blue);
                transform: translateY(-2px);
                box-shadow: 0 4px 12px rgba(11, 38, 110, .05);
            }

            .method-card.active {
                border-color: var(--primary-blue);
                background: rgba(11, 38, 110, 0.02);
            }

            .method-card input[type="radio"] {
                position: absolute;
                top: 20px;
                right: 20px;
                width: 18px;
                height: 18px;
                accent-color: var(--primary-blue);
            }

            .method-icon {
                font-size: 32px;
                color: var(--primary-blue);
                background: var(--slate-100);
                width: 60px;
                height: 60px;
                border-radius: 10px;
                display: flex;
                align-items: center;
                justify-content: center;
            }

            .method-info h3 {
                font-size: 16px;
                font-weight: 700;
                margin: 0 0 6px 0;
                color: var(--slate-800);
            }

            .method-info p {
                font-size: 13px;
                color: var(--slate-500);
                margin: 0;
            }

            /* Wizard Steps Progress Bar */
            .wizard-steps {
                display: flex;
                justify-content: space-between;
                position: relative;
                margin-bottom: 30px;
                background: var(--slate-100);
                padding: 12px;
                border-radius: 50px;
            }

            .wizard-step {
                display: flex;
                align-items: center;
                gap: 10px;
                z-index: 2;
                position: relative;
                cursor: pointer;
                flex: 1;
                justify-content: center;
                padding: 10px;
                border-radius: 30px;
                transition: all .2s;
            }

            .wizard-step.active {
                background: #fff;
                box-shadow: 0 2px 6px rgba(0, 0, 0, .08);
                color: var(--primary-blue);
                font-weight: 700;
            }

            .wizard-step.completed {
                color: #10b981;
            }

            .step-num {
                width: 24px;
                height: 24px;
                border-radius: 50%;
                background: var(--slate-300);
                color: #fff;
                display: flex;
                align-items: center;
                justify-content: center;
                font-size: 12px;
                font-weight: bold;
            }

            .wizard-step.active .step-num {
                background: var(--primary-blue);
            }

            .wizard-step.completed .step-num {
                background: #10b981;
            }

            .step-label {
                font-size: 14px;
                color: var(--slate-600);
            }

            .wizard-step.active .step-label {
                color: var(--primary-blue);
            }

            /* CPMK Table Section */
            .cpmk-section {
                border: 1px solid var(--slate-200);
                border-radius: 12px;
                overflow: hidden;
                margin-bottom: 20px;
            }

            .cpmk-header {
                display: flex;
                align-items: center;
                justify-content: space-between;
                padding: 14px 18px;
                background: var(--slate-50);
                border-bottom: 1px solid var(--slate-200);
            }

            .cpmk-header h3 {
                font-size: 14px;
                font-weight: 700;
                color: var(--slate-800);
                margin: 0;
            }

            .cpmk-col-head {
                display: grid;
                grid-template-columns: 190px 130px 150px 1fr 1fr 44px;
                gap: 10px;
                padding: 10px 18px;
                background: #f1f5f9;
                border-bottom: 1px solid var(--slate-200);
                font-size: 11px;
                font-weight: 600;
                text-transform: uppercase;
                color: var(--slate-500);
            }

            .cpmk-row {
                display: grid;
                grid-template-columns: 190px 130px 150px 1fr 1fr 44px;
                gap: 10px;
                align-items: start;
                padding: 14px 18px;
                border-bottom: 1px solid var(--slate-100);
            }

            .cpmk-row:last-child {
                border-bottom: none;
            }

            .cpmk-preview {
                font-size: 11px;
                color: var(--slate-500);
                background: var(--slate-50);
                border-radius: 8px;
                padding: 7px 10px;
                margin-top: 6px;
            }

            /* Dynamic Pertemuan Table Grid */
            .grid-table {
                display: flex;
                flex-direction: column;
                border: 1px solid var(--slate-200);
                border-radius: 12px;
                overflow: visible;
                min-width: 1300px;
            }

            .grid-table-header {
                display: grid;
                grid-template-columns: 80px 200px 180px 150px 100px 200px 120px 180px 80px;
                background: #f1f5f9;
                border-bottom: 1.5px solid var(--slate-300);
                font-size: 11px;
                font-weight: bold;
                text-transform: uppercase;
                color: var(--slate-600);
                text-align: center;
            }

            .grid-table-header div {
                padding: 12px 6px;
                border-right: 1px solid var(--slate-200);
                display: flex;
                align-items: center;
                justify-content: center;
            }

            .grid-table-header div:last-child {
                border-right: none;
            }

            .grid-table-row {
                display: grid;
                grid-template-columns: 80px 200px 180px 150px 100px 200px 120px 180px 80px;
                border-bottom: 1px solid var(--slate-200);
                align-items: stretch;
            }

            .grid-table-row:last-child {
                border-bottom: none;
            }

            .grid-table-row>div {
                padding: 8px 6px;
                border-right: 1px solid var(--slate-200);
                display: flex;
                align-items: center;
                justify-content: center;
            }

            .grid-table-row>div:last-child {
                border-right: none;
            }

            .grid-table-row input,
            .grid-table-row textarea,
            .grid-table-row select {
                font-size: 12px !important;
                padding: 6px 8px !important;
            }

            .grid-table-row.uts-row,
            .grid-table-row.uas-row {
                background: var(--slate-100);
                font-weight: bold;
            }

            .grid-table-row.uts-row div:not(:first-child),
            .grid-table-row.uas-row div:not(:first-child) {
                display: none;
            }

            /* Reference row styling */
            .ref-row {
                display: flex;
                gap: 12px;
                align-items: center;
                margin-bottom: 10px;
            }

            .ref-row:last-child {
                margin-bottom: 0;
            }

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

            .hidden {
                display: none !important;
            }
        </style>
    @endpush

    <div class="page-header">
        <div class="header-content">
            <h1>Ajukan RPS Baru</h1>
            <p>Pilih metode pembuatan RPS di bawah ini untuk memulai pengajuan.</p>
        </div>
        <a href="{{ route('banksoal.rps.dosen.index') }}" class="btn btn-secondary">
            Kembali
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
                <p>Isi data melalui form bertahap, dan biarkan sistem merender dokumen PDF RPS & Kontrak Kuliah Anda.
                </p>
            </div>
        </div>
    </div>

        <!-- ======================= SHARED HEADER (di luar form) ======================= -->
    <div class="form-card" id="shared_info_card">
        <div class="form-card-title"><i class="fas fa-book-open"></i> Informasi Dasar RPS</div>
        <div class="form-row">
            <div class="form-group">
                <label class="field-label">Mata Kuliah <span class="req">*</span></label>
                <select id="mkSelect" class="field-control" required>
                    <option value="" disabled selected>-- Pilih Mata Kuliah --</option>
                    @foreach($mataKuliahs as $mk)
                        <option value="{{ $mk->id }}">
                            {{ $mk->kode }} – {{ $mk->nama }} ({{ $mk->sks }} SKS)
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="form-group">
                <label class="field-label">Dosen Pengampu Lain</label>
                <select id="dosenSelect" class="field-control" multiple></select>
                <p class="field-hint">Pilih satu atau lebih dosen pengampu tambahan.</p>
            </div>
        </div>
        <div class="form-row">
            <div class="form-group">
                <label class="field-label">Semester <span class="req">*</span></label>
                <select id="semester" class="field-control bg-slate-100 border-slate-200 text-slate-500 cursor-not-allowed" disabled>
                    <option value="Ganjil" {{ $semester == 'Ganjil' ? 'selected' : '' }}>Ganjil</option>
                    <option value="Genap" {{ $semester == 'Genap' ? 'selected' : '' }}>Genap</option>
                </select>
            </div>
            <div class="form-group">
                <label class="field-label">Tahun Ajaran <span class="req">*</span></label>
                <select id="tahun_ajaran" class="field-control bg-slate-100 border-slate-200 text-slate-500 cursor-not-allowed" disabled>
                    @foreach($tahunAjarans as $ta)
                        <option value="{{ $ta }}" {{ $ta == $academicYear ? 'selected' : '' }}>{{ $ta }}</option>
                    @endforeach
                </select>
            </div>
        </div>
        {{-- Draft resume banner --}}
        <div id="draftResumeBanner" class="hidden mt-3 rounded-xl border border-amber-200 bg-amber-50 px-4 py-3 flex items-center justify-between gap-3">
            <div class="flex items-center gap-2 text-sm text-amber-800">
                <i class="fas fa-history text-amber-500"></i>
                <span id="draftResumeBannerText">Ditemukan draft tersimpan untuk MK ini.</span>
            </div>
            <div class="flex items-center gap-2">
                <button type="button" id="btnResumeDraft"
                        style="height:30px; padding:0 12px; background:var(--c-primary,#0B266E); color:#fff; border:none; border-radius:7px; font-size:11.5px; font-weight:600; cursor:pointer; font-family:inherit;">
                    Lanjutkan Draft
                </button>
                <button type="button" id="btnDiscardDraft"
                        style="height:30px; padding:0 10px; background:transparent; color:var(--c-fg-sec,#475569); border:1px solid var(--c-border,#DFE1E7); border-radius:7px; font-size:11.5px; font-weight:600; cursor:pointer; font-family:inherit;">
                    Abaikan
                </button>
            </div>
        </div>
    </div>

    <!-- ======================= FORM A: UPLOAD PDF ======================= -->
    <form id="uploadForm"
          action="{{ route('banksoal.rps.dosen.store-upload') }}"
          method="POST" enctype="multipart/form-data" novalidate
          data-route-cpl="{{ route('banksoal.rps.dosen.cpl') }}"
          data-route-dosen="{{ route('banksoal.rps.dosen.dosen') }}"
          data-cpmk-row-builder="1">
        @csrf
        <input type="hidden" name="mata_kuliah_id" id="upload_mk_id">
        <input type="hidden" name="semester"       id="upload_semester"     value="{{ $semester }}">
        <input type="hidden" name="tahun_ajaran"   id="upload_tahun_ajaran" value="{{ $academicYear }}">
        {{-- Semester & tahun_ajaran sudah ada sebagai hidden inputs. Dosen diisi via JS saat submit. --}}
            {{-- CPMK Section --}}
            <div class="form-card">
                <div class="form-card-title"><i class="fas fa-list-check"></i> Capaian Pembelajaran Mata Kuliah (CPMK)
                </div>
                <div class="cpmk-section">
                    <div class="cpmk-header">
                        <div>
                            <p style="margin: 0; font-size: 13px; color: var(--slate-600);">Baris CPMK yang diajukan
                                dalam RPS ini.</p>
                        </div>
                        <button type="button" class="btn btn-secondary" id="addCpmkRowBtn"
                            style="padding:8px 16px;font-size:13px;">
                            Tambah Baris
                        </button>
                    </div>

                    <div class="cpmk-col-head">
                        <div>CPL</div>
                        <div>Kode CPMK</div>
                        <div>KKO</div>
                        <div>Objek</div>
                        <div>Konteks</div>
                        <div></div>
                    </div>

                    <div id="cpmkRows" class="cpmk-rows" data-cpmk-rows>
                        <div class="cpmk-row" data-cpmk-row data-row-index="0">
                            <div>
                                <select name="cpmk_rows[0][cpl_id]" class="field-control cpl-select-input"
                                    data-cpmk-cpl-select style="font-size:13px;" required>
                                    <option value="">Pilih CPL</option>
                                </select>
                            </div>
                            <div>
                                <div style="display:flex;align-items:center;gap:6px;">
                                    <span
                                        style="font-size:12px;font-weight:600;color:var(--slate-500);padding:10px 8px;background:var(--slate-50);border:1px solid var(--slate-200);border-radius:8px;">CPMK</span>
                                    <input type="text" name="cpmk_rows[0][kode]" class="field-control"
                                        style="font-size:13px;" placeholder="1" required>
                                </div>
                            </div>
                            <div>
                                <select name="cpmk_rows[0][kko]" class="field-control kko-select-input"
                                    style="font-size:13px;" required>
                                    <option value="">Pilih KKO</option>
                                    @foreach([
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
                                        ] as $val => $lbl)
                                        <option value="{{ $val }}">{{ $val }} – {{ $lbl }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <input type="text" name="cpmk_rows[0][objek]" class="field-control objek-input"
                                    style="font-size:13px;" placeholder="merancang sistem IoT" required>
                            </div>
                            <div>
                                <input type="text" name="cpmk_rows[0][konteks]" class="field-control"
                                    style="font-size:13px;" placeholder="sesuai kebutuhan">
                            </div>
                            <div style="display:flex;align-items:flex-end;">
                                <button type="button" class="btn-danger-sm" data-remove-cpmk-row>Hapus</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="form-card">
                <div class="form-card-title"><i class="fas fa-file-pdf"></i> Unggah Dokumen RPS</div>
                <x-banksoal::ui.upload-zone name="dokumen" inputId="fileInput" accept=".pdf" maxLabel="PDF (Maks. 5MB)"
                    :required="true" />
                @error('dokumen')
                <p class="field-error" style="margin-top:8px;">{{ $message }}</p>@enderror
            </div>

            <div class="form-card">
                <div class="form-card-title"><i class="fas fa-comment"></i> Catatan Pengajuan</div>
                <div class="form-group">
                    <textarea name="catatan" class="field-control" rows="3"
                        placeholder="Opsional. Tambahkan catatan untuk validator GPM."></textarea>
                </div>
            </div>

                        <div class="form-actions" style="display: flex; justify-content: space-between; align-items: flex-start; gap: 12px; margin-top: 24px;">
                <a href="{{ route('banksoal.rps.dosen.index') }}" class="btn btn-secondary" style="height:38px; display:inline-flex; align-items:center;">Batal</a>
                <div style="display:flex; align-items:flex-start; gap:12px;">
                    {{-- Tombol Simpan Draft --}}
                    <div style="display:flex; flex-direction:column; align-items:center; gap:3px;">
                        <button type="button" id="btnSaveDraftUpload" class="btn btn-secondary" disabled
                                onclick="saveDraftManual('upload')"
                                style="height:38px; display:inline-flex; align-items:center; opacity:0.45; cursor:not-allowed;">
                            Simpan Draft
                        </button>
                        <span id="uploadDraftLabel" style="font-size:10px; color:var(--c-fg-placeholder,#A4ABB8); white-space:nowrap;">
                            Pilih Mata Kuliah terlebih dahulu
                        </span>
                    </div>
                    <button type="submit" class="btn btn-primary" id="btnSubmitManual" style="height:38px; display:inline-flex; align-items:center;">
                        Ajukan RPS
                    </button>
                </div>
            </div>
    </form><!-- END uploadForm -->

    <!-- ======================= FORM B: GENERATOR ======================= -->
    <form id="generatorForm"
          action="{{ route('banksoal.rps.dosen.store-generator') }}"
          method="POST" novalidate
          data-route-cpl="{{ route('banksoal.rps.dosen.cpl') }}"
          data-route-dosen="{{ route('banksoal.rps.dosen.dosen') }}"
          data-cpmk-row-builder="1">
        @csrf
        <input type="hidden" name="mata_kuliah_id" id="generator_mk_id">
        <input type="hidden" name="semester"       id="generator_semester"     value="{{ $semester }}">
        <input type="hidden" name="tahun_ajaran"   id="generator_tahun_ajaran" value="{{ $academicYear }}">
        {{-- dosen_lain di-sync via JS --}}


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
                    <div class="form-card-title"><i class="fas fa-bullseye"></i> Capaian Pembelajaran Lulusan (CPL)
                    </div>
                    <div class="form-group-full">
                        <label class="field-label">Pilih CPL yang Terkait <span class="req">*</span></label>
                        <div id="cpl_checkbox_container"
                            style="display:flex; flex-direction:column; gap:10px; padding: 15px; border: 1px solid var(--slate-200); border-radius: 9px; max-height: 250px; overflow-y: auto;">
                            <p class="field-hint">Silakan pilih Mata Kuliah di atas terlebih dahulu untuk memuat daftar
                                CPL.</p>
                        </div>
                    </div>
                </div>

                <div class="form-card">
                    <div class="form-card-title" style="margin-bottom:16px;"><i class="fas fa-list-check"></i> Capaian
                        Pembelajaran Mata Kuliah (CPMK)</div>
                    <div class="cpmk-section">
                        <div class="cpmk-header">
                            <div>
                                <p style="margin: 0; font-size: 13px; color: var(--slate-600);">Tentukan CPMK. CPMK
                                    minimal 1 baris.</p>
                            </div>
                            <button type="button" class="btn btn-secondary" id="addCpmkRowBtnGenerator"
                                style="padding:8px 16px;font-size:13px;">
                                Tambah Baris
                            </button>
                        </div>

                        <div class="cpmk-col-head">
                            <div>CPL</div>
                            <div>Kode CPMK</div>
                            <div>KKO</div>
                            <div>Objek</div>
                            <div>Konteks</div>
                            <div></div>
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
                        <textarea name="deskripsi_mk" id="deskripsi_mk" class="field-control" rows="5"
                            placeholder="Tuliskan deskripsi singkat mengenai materi, fokus kajian, dan tujuan pembelajaran mata kuliah ini..."></textarea>
                    </div>
                </div>

                <div style="display: flex; justify-content: space-between; align-items: flex-start; gap: 12px;">
                    <a href="{{ route('banksoal.rps.dosen.index') }}" class="btn btn-secondary" style="height:38px; display:inline-flex; align-items:center;">Batal</a>
                    <div style="display:flex; align-items:flex-start; gap:12px;">
                        <div style="display:flex; flex-direction:column; align-items:center; gap:3px;">
                            <button type="button" class="btn btn-secondary btn-save-draft-generator" disabled onclick="saveDraftManual('generator')" style="height:38px; display:inline-flex; align-items:center; opacity:0.45; cursor:not-allowed;">
                                Simpan Draft
                            </button>
                            <span class="generator-draft-label" style="font-size:10px; color:var(--c-fg-placeholder,#A4ABB8); white-space:nowrap;">Pilih Mata Kuliah terlebih dahulu</span>
                        </div>
                        <button type="button" class="btn btn-primary btn-next-step" data-next="2" style="height:38px; display:inline-flex; align-items:center;">Next</button>
                    </div>
                </div>
            </div>

            <!-- STEP 2: KONTRAK KULIAH -->
            <div class="wizard-content-section hidden" id="step_2_panel">
                <div class="form-card">
                    <div class="form-card-title"><i class="fas fa-university"></i> Institusi Pelaksana</div>
                    <div class="form-row">
                        <div class="form-group">
                            <label class="field-label">Fakultas</label>
                            <input type="text" class="field-control bg-slate-100" value="Teknik"
                                data-always-disabled="true" disabled>
                        </div>
                        <div class="form-group">
                            <label class="field-label">Program Studi</label>
                            <input type="text" class="field-control bg-slate-100" value="Teknik Komputer"
                                data-always-disabled="true" disabled>
                        </div>
                    </div>
                </div>

                <div class="form-card">
                    <div class="form-card-title"><i class="fas fa-star-half-stroke"></i> Tabel Penilaian Mata Kuliah
                    </div>
                    <p style="font-size: 13px; color: var(--slate-600); margin-bottom: 16px;">Tentukan bobot (%) dan
                        komponen evaluasi yang dinilai. Total bobot harus berjumlah 100%.</p>

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
                                <td class="font-bold">Aktivitas Partisipatif<input type="hidden"
                                        name="penilaian_data[0][poin]" value="Aktivitas Partisipatif"></td>
                                <td><textarea name="penilaian_data[0][komponen]" class="field-control" rows="2"
                                        placeholder="Aktivitas Kelas / Diskusi" required></textarea></td>
                                <td><input type="number" name="penilaian_data[0][bobot]"
                                        class="field-control text-center weight-input" min="0" max="100" value="0"
                                        required></td>
                                <td><select name="penilaian_data[0][cpmk][]" class="field-control cpmk-target-select"
                                        multiple required></select></td>
                            </tr>
                            <tr>
                                <td class="text-center">2</td>
                                <td class="font-bold">Hasil Proyek<input type="hidden" name="penilaian_data[1][poin]"
                                        value="Hasil Proyek"></td>
                                <td><textarea name="penilaian_data[1][komponen]" class="field-control" rows="2"
                                        placeholder="Hasil Proyek Desain" required></textarea></td>
                                <td><input type="number" name="penilaian_data[1][bobot]"
                                        class="field-control text-center weight-input" min="0" max="100" value="0"
                                        required></td>
                                <td><select name="penilaian_data[1][cpmk][]" class="field-control cpmk-target-select"
                                        multiple required></select></td>
                            </tr>
                            <tr>
                                <td class="text-center">3</td>
                                <td class="font-bold">Tugas<input type="hidden" name="penilaian_data[2][poin]"
                                        value="Tugas"></td>
                                <td><textarea name="penilaian_data[2][komponen]" class="field-control" rows="2"
                                        placeholder="Tugas Kelompok" required></textarea></td>
                                <td><input type="number" name="penilaian_data[2][bobot]"
                                        class="field-control text-center weight-input" min="0" max="100" value="0"
                                        required></td>
                                <td><select name="penilaian_data[2][cpmk][]" class="field-control cpmk-target-select"
                                        multiple required></select></td>
                            </tr>
                            <tr>
                                <td class="text-center">4</td>
                                <td class="font-bold">Quiz<input type="hidden" name="penilaian_data[3][poin]"
                                        value="Quiz"></td>
                                <td><textarea name="penilaian_data[3][komponen]" class="field-control" rows="2"
                                        placeholder="Tugas Individu / Kuis" required></textarea></td>
                                <td><input type="number" name="penilaian_data[3][bobot]"
                                        class="field-control text-center weight-input" min="0" max="100" value="0"
                                        required></td>
                                <td><select name="penilaian_data[3][cpmk][]" class="field-control cpmk-target-select"
                                        multiple required></select></td>
                            </tr>
                            <!-- Kognitif has UTS and UAS -->
                            <tr>
                                <td class="text-center" rowspan="2">5</td>
                                <td class="font-bold" rowspan="2">Kognitif / Pengetahuan<input type="hidden"
                                        name="penilaian_data[4][poin]" value="Kognitif / Pengetahuan"></td>
                                <td>UTS (Ujian Tengah Semester)<input type="hidden"
                                        name="penilaian_data[4][sub_rows][0][komponen]" value="UTS"></td>
                                <td><input type="number" name="penilaian_data[4][sub_rows][0][bobot]"
                                        class="field-control text-center weight-input" min="0" max="100" value="0"
                                        required></td>
                                <td><select name="penilaian_data[4][sub_rows][0][cpmk][]"
                                        class="field-control cpmk-target-select" multiple required></select></td>
                            </tr>
                            <tr>
                                <td>UAS (Ujian Akhir Semester)<input type="hidden"
                                        name="penilaian_data[4][sub_rows][1][komponen]" value="UAS"></td>
                                <td><input type="number" name="penilaian_data[4][sub_rows][1][bobot]"
                                        class="field-control text-center weight-input" min="0" max="100" value="0"
                                        required></td>
                                <td><select name="penilaian_data[4][sub_rows][1][cpmk][]"
                                        class="field-control cpmk-target-select" multiple required></select></td>
                            </tr>
                            <tr class="font-bold" style="background-color: var(--slate-100);">
                                <td colspan="3" class="text-right">Total Bobot:</td>
                                <td class="text-center" id="total_weight_label">0%</td>
                                <td></td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <div style="display: flex; justify-content: space-between; align-items: flex-start; gap: 12px;">
                    <button type="button" class="btn btn-secondary btn-prev-step" data-prev="1" style="height:38px; display:inline-flex; align-items:center;">Back</button>
                    <div style="display:flex; align-items:flex-start; gap:12px;">
                        <div style="display:flex; flex-direction:column; align-items:center; gap:3px;">
                            <button type="button" class="btn btn-secondary btn-save-draft-generator" disabled onclick="saveDraftManual('generator')" style="height:38px; display:inline-flex; align-items:center; opacity:0.45; cursor:not-allowed;">
                                Simpan Draft
                            </button>
                            <span class="generator-draft-label" style="font-size:10px; color:var(--c-fg-placeholder,#A4ABB8); white-space:nowrap;">Pilih Mata Kuliah terlebih dahulu</span>
                        </div>
                        <button type="button" class="btn btn-primary btn-next-step" data-next="3" style="height:38px; display:inline-flex; align-items:center;">Next</button>
                    </div>
                </div>
            </div>

                        <!-- STEP 3: PERTEMUAN & REFERENSI -->
            <div class="wizard-content-section hidden" id="step_3_panel">

                <!-- Navigator grid -->
                <div class="form-card" style="padding: 18px 20px 14px;">
                    <div style="display:flex; align-items:center; justify-content:space-between; margin-bottom:12px;">
                        <div class="form-card-title" style="margin:0; padding:0; border:none;">
                            <i class="fas fa-calendar-alt"></i> Rencana Pembelajaran per Pertemuan
                        </div>
                        <span id="pertemuanProgress" style="font-size:12px; font-weight:600; color:var(--c-fg-sec,#475569);">0 / 14 terisi</span>
                    </div>

                    <!-- Grid nomor pertemuan -->
                    <div id="pertemuanNav" style="display:grid; grid-template-columns:repeat(8,1fr); gap:6px; margin-bottom:16px;">
                        @for($p = 1; $p <= 16; $p++)
                            @if($p === 8)
                                <div data-nav-p="8" style="grid-column:span 1;">
                                    <div style="height:36px; border-radius:8px; background:#f1f5f9; border:1px solid #DFE1E7; display:flex; align-items:center; justify-content:center; font-size:10px; font-weight:700; color:#94A3B8; letter-spacing:.03em;">
                                        UTS
                                    </div>
                                </div>
                            @elseif($p === 16)
                                <div data-nav-p="16" style="grid-column:span 1;">
                                    <div style="height:36px; border-radius:8px; background:#f1f5f9; border:1px solid #DFE1E7; display:flex; align-items:center; justify-content:center; font-size:10px; font-weight:700; color:#94A3B8; letter-spacing:.03em;">
                                        UAS
                                    </div>
                                </div>
                            @else
                                <button type="button"
                                        data-nav-p="{{ $p }}"
                                        onclick="goToPertemuan({{ $p }})"
                                        style="height:36px; border-radius:8px; border:1px solid #DFE1E7; background:#fff; font-size:13px; font-weight:600; color:#64748b; cursor:pointer; transition:all .15s; position:relative;">
                                    {{ $p < 8 ? $p : $p - 1 }}
                                    <span data-filled-{{ $p }} style="display:none; position:absolute; top:4px; right:4px; width:6px; height:6px; border-radius:50%; background:#0B266E;"></span>
                                </button>
                            @endif
                        @endfor
                    </div>

                    <!-- Panel form satu pertemuan -->
                    <div id="pertemuanFormPanel" style="border:1px solid #DFE1E7; border-radius:12px; overflow:hidden;">
                        <div style="display:flex; align-items:center; justify-content:space-between; padding:12px 16px; background:#FAFAFA; border-bottom:1px solid #DFE1E7;">
                            <span id="pertemuanTitle" style="font-size:14px; font-weight:700; color:#1e293b;">Pertemuan 1</span>
                        </div>
                        <div style="padding:16px 18px;" id="pertemuanFields">
                            <!-- Fields dirender via JS -->
                        </div>
                    </div>

                    <!-- Navigasi prev/next pertemuan -->
                    <div style="display:flex; align-items:center; justify-content:space-between; margin-top:14px;">
                        <button type="button" id="btnPrevPertemuan" onclick="prevPertemuan()"
                                style="height:34px; padding:0 16px; border:1px solid #DFE1E7; border-radius:8px; background:#fff; font-size:13px; font-weight:600; color:#475569; cursor:pointer; display:inline-flex; align-items:center; gap:6px; font-family:inherit; transition:all .15s;">
                            Sebelumnya
                        </button>
                        <button type="button" id="btnNextPertemuan" onclick="nextPertemuan()"
                                style="height:34px; padding:0 16px; border:1px solid #0B266E; border-radius:8px; background:#0B266E; color:#fff; font-size:13px; font-weight:600; cursor:pointer; display:inline-flex; align-items:center; gap:6px; font-family:inherit; transition:all .15s;">
                            Selanjutnya
                        </button>
                    </div>
                </div>

                <!-- Hidden inputs untuk UTS dan UAS (auto-populated) -->
                <input type="hidden" name="pertemuan_data[8][pertemuan]"        value="8">
                <input type="hidden" name="pertemuan_data[8][kemampuan_akhir]"  value="Evaluation">
                <input type="hidden" name="pertemuan_data[8][pokok_bahasan]"    value="-">
                <input type="hidden" name="pertemuan_data[8][metode]"           value="-">
                <input type="hidden" name="pertemuan_data[8][waktu]"            value="0">
                <input type="hidden" name="pertemuan_data[8][pengalaman_belajar]" value="-">
                <input type="hidden" name="pertemuan_data[8][target_cpmk]"      value="-">
                <input type="hidden" name="pertemuan_data[8][kriteria_penilaian]" value="-">
                <input type="hidden" name="pertemuan_data[8][bobot]"            value="0">
                <input type="hidden" name="pertemuan_data[16][pertemuan]"       value="16">
                <input type="hidden" name="pertemuan_data[16][kemampuan_akhir]" value="Evaluation">
                <input type="hidden" name="pertemuan_data[16][pokok_bahasan]"   value="-">
                <input type="hidden" name="pertemuan_data[16][metode]"          value="-">
                <input type="hidden" name="pertemuan_data[16][waktu]"           value="0">
                <input type="hidden" name="pertemuan_data[16][pengalaman_belajar]" value="-">
                <input type="hidden" name="pertemuan_data[16][target_cpmk]"     value="-">
                <input type="hidden" name="pertemuan_data[16][kriteria_penilaian]" value="-">
                <input type="hidden" name="pertemuan_data[16][bobot]"           value="0">

                <!-- Referensi & Catatan -->
                <div class="form-card">
                    <div class="form-card-title"><i class="fas fa-book"></i> Daftar Referensi</div>
                    <p style="font-size: 13px; color: var(--slate-600); margin-bottom: 12px;">Tambahkan satu atau lebih buku/jurnal referensi.</p>
                    <div id="references_container">
                        <div class="ref-row">
                            <input type="text" name="referensi_data[]" class="field-control"
                                placeholder="Contoh: Pressman, R.S. (2015). Software Engineering. McGraw-Hill." required>
                            <button type="button" class="btn-danger-sm btn-remove-ref" style="flex-shrink: 0; display:inline-flex; align-items:center; justify-content:center; width:36px; height:36px; padding:0;" title="Hapus referensi" aria-label="Hapus referensi">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    </div>
                    <button type="button" class="btn btn-secondary" id="btnAddReference" style="margin-top:12px; padding:8px 16px; font-size:13px;">
                        Tambah Referensi
                    </button>
                </div>

                <div class="form-card">
                    <div class="form-card-title"><i class="fas fa-comment"></i> Catatan Pengajuan</div>
                    <div class="form-group">
                        <textarea name="catatan" class="field-control" rows="3" placeholder="Opsional. Tambahkan catatan untuk validator GPM."></textarea>
                    </div>
                </div>

                <div style="display: flex; justify-content: space-between; align-items: flex-start; gap: 12px; margin-top: 16px;">
                    <button type="button" class="btn btn-secondary btn-prev-step" data-prev="2" style="height:38px; display:inline-flex; align-items:center;">Back</button>
                    <div style="display:flex; align-items:flex-start; gap:12px;">
                        <div style="display:flex; flex-direction:column; align-items:center; gap:3px;">
                            <button type="button" id="btnSaveDraftGenerator" class="btn btn-secondary" disabled onclick="saveDraftManual('generator')" style="height:38px; display:inline-flex; align-items:center; opacity:0.45; cursor:not-allowed;">
                                Simpan Draft
                            </button>
                            <span id="generatorDraftLabel" style="font-size:10px; color:var(--c-fg-placeholder,#A4ABB8); white-space:nowrap;">Pilih Mata Kuliah terlebih dahulu</span>
                        </div>
                        <button type="submit" class="btn btn-primary" id="btnSubmitGenerator" style="height:38px; display:inline-flex; align-items:center;">
                            Ajukan & Generate PDF
                        </button>
                    </div>
                </div>
            </div>
            </div>
        </div>
    </form><!-- END generatorForm -->


    <template id="cpmkRowTemplate">
        <div class="cpmk-row" data-cpmk-row data-row-index="__INDEX__">
            <div>
                <select name="cpmk_rows[__INDEX__][cpl_id]" class="field-control cpl-select-input" data-cpmk-cpl-select
                    style="font-size:13px;" required>
                    <option value="">Pilih CPL</option>
                </select>
            </div>
            <div>
                <div style="display:flex;align-items:center;gap:6px;">
                    <span
                        style="font-size:12px;font-weight:600;color:var(--slate-500);padding:10px 8px;background:var(--slate-50);border:1px solid var(--slate-200);border-radius:8px;">CPMK</span>
                    <input type="text" name="cpmk_rows[__INDEX__][kode]" class="field-control" style="font-size:13px;"
                        placeholder="1" required>
                </div>
            </div>
            <div>
                <select name="cpmk_rows[__INDEX__][kko]" class="field-control kko-select-input" style="font-size:13px;"
                    required>
                    <option value="">Pilih KKO</option>
                    @foreach([
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
                        ] as $val => $lbl)
                        <option value="{{ $val }}">{{ $val }} – {{ $lbl }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <input type="text" name="cpmk_rows[__INDEX__][objek]" class="field-control objek-input"
                    style="font-size:13px;" placeholder="merancang sistem IoT" required>
            </div>
            <div>
                <input type="text" name="cpmk_rows[__INDEX__][konteks]" class="field-control" style="font-size:13px;"
                    placeholder="sesuai kebutuhan">
            </div>
            <div style="display:flex;align-items:flex-end;">
                <button type="button" class="btn-danger-sm" data-remove-cpmk-row aria-label="Hapus baris CPMK" title="Hapus baris CPMK">
                    <i class="fas fa-trash"></i>
                </button>
            </div>
        </div>
    </template>

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                const methodCards = document.querySelectorAll('.method-card');
                const creationMethodInput = document.getElementById('creation_method_input');
                const methodUploadPanel = document.getElementById('method_upload_panel');
                const methodGeneratorPanel = document.getElementById('method_generator_panel');
                const rpsSubmitForm = document.getElementById('generatorForm');

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
                        if (parseInt(step.dataset.step) < stepNum) {
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

                function initDosenSelect(selectedIds = []) {
                    if (window.BanksoalRpsUploadForm && typeof window.BanksoalRpsUploadForm.loadDosenOptions === 'function') {
                        window.BanksoalRpsUploadForm.loadDosenOptions();
                    }
                }

                function setCreationMethod(method) {
                    if (creationMethodInput) creationMethodInput.value = method;
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

                    const uploadForm    = document.getElementById('uploadForm');
                    const generatorForm = document.getElementById('generatorForm');

                    if (method === 'upload') {
                        if (methodUploadPanel) methodUploadPanel.classList.remove('hidden');
                        if (methodGeneratorPanel) methodGeneratorPanel.classList.add('hidden');
                        if (uploadForm)    uploadForm.style.display    = '';
                        if (generatorForm) generatorForm.style.display = 'none';
                    } else {
                        if (methodUploadPanel) methodUploadPanel.classList.add('hidden');
                        if (methodGeneratorPanel) methodGeneratorPanel.classList.remove('hidden');
                        if (uploadForm)    uploadForm.style.display    = 'none';
                        if (generatorForm) generatorForm.style.display = '';
                        loadWizardStep(1);
                    }

                    if (window.BanksoalRpsUploadForm && typeof window.BanksoalRpsUploadForm.updateCpmkFormState === 'function') {
                        window.BanksoalRpsUploadForm.updateCpmkFormState();
                    }
                }
                // Init state tombol draft
                updateDraftButtonState();

                // Auto-resume dari URL jika ada
                const _urlP = new URLSearchParams(window.location.search);
                const _resumeMk = _urlP.get('mk');
                const _resumeMethod = _urlP.get('method') || 'generator';
                if (_resumeMk) {
                    const _mkSel = document.getElementById('mkSelect');
                    if (_mkSel) {
                        _mkSel.value = _resumeMk;
                        syncSharedFields();
                        setCreationMethod(_resumeMethod);
                    }
                }


                function syncSharedFields() {
                    const mkVal  = document.getElementById('mkSelect')?.value  || '';
                    const semVal = document.getElementById('semester')?.value  || '{{ $semester }}';
                    const taVal  = document.getElementById('tahun_ajaran')?.value || '{{ $academicYear }}';

                    ['upload', 'generator'].forEach(m => {
                        const mkEl = document.getElementById(`${m}_mk_id`);
                        const semEl = document.getElementById(`${m}_semester`);
                        const taEl  = document.getElementById(`${m}_tahun_ajaran`);
                        if (mkEl)  mkEl.value  = mkVal;
                        if (semEl) semEl.value = semVal;
                        if (taEl)  taEl.value  = taVal;
                    });

                    updateDraftButtonState();
                }

                // ─── State tombol Draft ───────────────────────────────────────────────────────
                function updateDraftButtonState() {
                    const mkId = document.getElementById('mkSelect')?.value;

                    ['upload', 'generator'].forEach(method => {
                        const suffix = method === 'upload' ? 'Upload' : 'Generator';
                        const btns   = document.querySelectorAll(`#btnSaveDraft${suffix}, .btn-save-draft-${method}`);
                        const labels = document.querySelectorAll(`#${method}DraftLabel, .${method}-draft-label`);

                        btns.forEach(btn => {
                            if (!btn) return;
                            if (!mkId) {
                                btn.disabled = true;
                                btn.style.opacity = '0.45';
                                btn.style.cursor  = 'not-allowed';
                            } else {
                                btn.disabled = false;
                                btn.style.opacity = '1';
                                btn.style.cursor  = 'pointer';
                            }
                        });

                        labels.forEach(label => {
                            if (!label) return;
                            if (!mkId) {
                                label.textContent = 'Pilih Mata Kuliah terlebih dahulu';
                            } else {
                                const saved = window.lastDraftSaved?.[method];
                                label.textContent = saved ? `Tersimpan ${saved}` : 'Belum disimpan';
                            }
                        });
                    });
                }

                // ─── Save draft saat tombol diklik manual ────────────────────────────────────
                async function saveDraftManual(method) {
                    const btns = document.querySelectorAll('#btnSaveDraftUpload, #btnSaveDraftGenerator, .btn-save-draft-upload, .btn-save-draft-generator');
                    btns.forEach(btn => {
                        btn.disabled = true;
                        btn.innerHTML = '<i class="fas fa-spinner fa-spin text-sm me-1"></i> Menyimpan...';
                    });

                    await saveStepToDraft(method, window.currentGeneratorStep || 1, null, true);

                    btns.forEach(btn => {
                        btn.innerHTML = '<i class="fas fa-spinner fa-spin text-sm me-1"></i> Mengalihkan...';
                    });

                    if (window.dispatchEvent) {
                        window.dispatchEvent(new CustomEvent('notify', {
                            detail: { type: 'info', message: 'Draft berhasil disimpan. Mengalihkan ke Manajemen RPS...' }
                        }));
                    }

                    setTimeout(() => {
                        window.location.href = '{{ route("banksoal.rps.dosen.index") }}';
                    }, 1000);
                }
                window.saveDraftManual = saveDraftManual;
                window.saveStepToDraft = saveStepToDraft;

                // ─── Ganti saveCache() lama → saveStepToDraft() DB-based ────────────────────
                async function saveStepToDraft(method, stepJustCompleted, onSuccess, isManual = false) {
                    const mkId = document.getElementById('mkSelect')?.value;
                    if (!mkId) {
                        if (onSuccess) onSuccess();
                        return;
                    }

                    const currentMax = Math.max(window.currentDraftStepReached || 0, stepJustCompleted || 1);
                    window.currentDraftStepReached = currentMax;

                    const payload = {
                        mk_id:           mkId,
                        semester:        document.getElementById('semester')?.value         || '{{ $semester }}',
                        tahun_ajaran:    document.getElementById('tahun_ajaran')?.value     || '{{ $academicYear }}',
                        creation_method: method,
                        step_reached:    currentMax,
                        dosen_lain:      getDosenLainIds(),
                    };

                    const s1 = collectStep1Data();
                    if (s1 && (s1.cpmk_rows?.length > 0 || s1.deskripsi_mk)) payload.data_step_1 = s1;

                    const s2 = collectStep2Data();
                    if (s2 && Object.keys(s2).length > 0) payload.data_step_2 = s2;

                    const s3 = collectStep3Data();
                    if (s3 && (s3.referensi_data?.length > 0 || Object.keys(s3.pertemuan_data || {}).length > 0)) payload.data_step_3 = s3;

                    if (window.showLoader) window.showLoader();
                    try {
                        const res  = await fetch('{{ route("banksoal.rps.dosen.save-draft") }}', {
                            method:  'POST',
                            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                            body:    JSON.stringify(payload),
                        });
                        const data = await res.json();
                        if (data.success) {
                            if (!window.lastDraftSaved) window.lastDraftSaved = {};
                            window.lastDraftSaved[method] = data.updated_at;
                            window.currentDraftId = data.draft_id;
                            updateDraftButtonState();
                            if (isManual) {
                                window.dispatchEvent(new CustomEvent('notify', {
                                    detail: { type: 'success', message: 'Draft berhasil disimpan!' }
                                }));
                            }
                        }
                    } catch (e) {
                        console.warn('Draft save failed:', e);
                    } finally {
                        if (window.hideLoader) window.hideLoader();
                        if (onSuccess) onSuccess();
                    }
                }

                // ─── Kumpulkan data per step ─────────────────────────────────────────────────
                function collectStep1Data() {
                    const cpmkRows = [];
                    document.querySelectorAll('#cpmkRowsGenerator [data-cpmk-row]').forEach(row => {
                        cpmkRows.push({
                            cpl_id:  row.querySelector('[data-cpmk-cpl-select]')?.value || row.querySelector('[data-cpmk-cpl-select]')?.dataset?.selectedValue || '',
                            kode:    row.querySelector('input[name*="[kode]"]')?.value   || '',
                            kko:     row.querySelector('select[name*="[kko]"]')?.value   || '',
                            objek:   row.querySelector('input[name*="[objek]"]')?.value  || '',
                            konteks: row.querySelector('input[name*="[konteks]"]')?.value || '',
                        });
                    });
                    const selectedCpls = Array.from(document.querySelectorAll('#cpl_checkbox_container input[name="cpl_ids[]"]:checked')).map(cb => cb.value);
                    return {
                        cpl_ids:      selectedCpls,
                        cpmk_rows:    cpmkRows,
                        deskripsi_mk: document.getElementById('deskripsi_mk')?.value || '',
                    };
                }

                function collectStep2Data() {
                    const table = document.getElementById('penilaian_data_table');
                    if (!table) return null;
                    const form = document.getElementById('generatorForm');
                    if (!form) return {};
                    const formData = new FormData(form);
                    const raw = {};
                    for (const [k, v] of formData.entries()) {
                        if (k.startsWith('penilaian_data')) raw[k] = v;
                    }
                    return raw;
                }

                function collectStep3Data() {
                    const refs = [];
                    document.querySelectorAll('#references_container input[name="referensi_data[]"]').forEach(i => {
                        if (i.value && i.value.trim()) refs.push(i.value.trim());
                    });
                    // Pertemuan data lives in the in-memory object (fields are dynamically rendered)
                    // Use window.pertemuanData since it's defined in the outer script scope
                    const pertemuan = {};
                    const pData = window.pertemuanData;
                    if (pData) {
                        for (const [p, d] of Object.entries(pData)) {
                            if (!d) continue;
                            const fields = ['kemampuan_akhir','pokok_bahasan','metode','waktu','pengalaman_belajar','kriteria_penilaian','bobot'];
                            fields.forEach(f => {
                                pertemuan[`pertemuan_data[${p}][${f}]`] = d[f] ?? '';
                            });
                            // target_cpmk: store as JSON array string
                            const tc = d.target_cpmk;
                            pertemuan[`pertemuan_data[${p}][target_cpmk]`] = Array.isArray(tc)
                                ? JSON.stringify(tc)
                                : (typeof tc === 'string' ? tc : '');
                        }
                    }
                    return { pertemuan_data: pertemuan, referensi_data: refs };
                }

                function getDosenLainIds() {
                    const sel = document.getElementById('dosenSelect');
                    if (!sel) return [];
                    if (sel.tomselect) return sel.tomselect.getValue();
                    return Array.from(sel.selectedOptions).map(o => o.value);
                }

                // ─── Load draft dari DB saat MK dipilih ──────────────────────────────────────
                async function loadDraftIfExists(mkId, autoRestore = false) {
                    if (!mkId) return;
                    try {
                        const res  = await fetch('{{ url("/api/v1/bank-soal/rps/dosen/draft/") }}/' + mkId, {
                            headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                        });
                        const data = await res.json();
                        if (data.success && data.data && data.data.length > 0) {
                            const method  = creationMethodInput?.value || 'generator';
                            const draft   = data.data.find(d => d.creation_method === method) || data.data[0];
                            if (autoRestore) {
                                setCreationMethod(draft.creation_method);
                                restoreDraftData(draft);
                            } else {
                                showDraftResumeBanner(draft);
                            }
                        } else {
                            hideDraftResumeBanner();
                        }
                    } catch (e) {
                        // Gagal fetch draft — tidak kritis
                    }
                }

                function showDraftResumeBanner(draft) {
                    const banner = document.getElementById('draftResumeBanner');
                    const text   = document.getElementById('draftResumeBannerText');
                    const btnResume  = document.getElementById('btnResumeDraft');
                    const btnDiscard = document.getElementById('btnDiscardDraft');
                    if (!banner) return;

                    const methodLabel = draft.creation_method === 'generator' ? 'Form Generator' : 'Upload PDF';
                    const stepLabel   = ['Belum dimulai','Langkah 1/3','Langkah 2/3','Langkah 3/3'][draft.step_reached] || '';
                    if (text) text.textContent = `Ditemukan draft ${methodLabel} (${stepLabel}) — terakhir disimpan ${draft.updated_at}.`;

                    banner.classList.remove('hidden');

                    if (btnResume) {
                        btnResume.onclick = () => {
                            setCreationMethod(draft.creation_method);
                            restoreDraftData(draft);
                            hideDraftResumeBanner();
                        };
                    }
                    if (btnDiscard) {
                        btnDiscard.onclick = () => hideDraftResumeBanner();
                    }
                }

                function hideDraftResumeBanner() {
                    document.getElementById('draftResumeBanner')?.classList.add('hidden');
                }

                function restoreDraftData(draft) {
                    if (!draft) return;
                    const method = draft.creation_method;
                    window.currentDraftStepReached = draft.step_reached || 1;
                    window._pendingDraftRestore = draft;

                    if (method === 'generator') {
                        if (draft.data_step_1) {
                            if (draft.data_step_1.cpmk_rows?.length) {
                                const container = document.getElementById('cpmkRowsGenerator');
                                if (container) {
                                    container.innerHTML = '';
                                    draft.data_step_1.cpmk_rows.forEach((row, i) => {
                                        const tpl = document.getElementById('cpmkRowTemplate')?.innerHTML;
                                        if (!tpl) return;
                                        const html = tpl.replace(/__INDEX__/g, i);
                                        const wrap = document.createElement('div');
                                        wrap.innerHTML = html;
                                        const rowEl = wrap.firstElementChild;
                                        if (rowEl) {
                                            const cplSel = rowEl.querySelector('[data-cpmk-cpl-select]');
                                            if (cplSel) cplSel.dataset.selectedValue = row.cpl_id;
                                            const kodeIn = rowEl.querySelector('input[name*="[kode]"]');
                                            if (kodeIn) kodeIn.value = row.kode || '';
                                            const kkoSel = rowEl.querySelector('select[name*="[kko]"]');
                                            if (kkoSel) kkoSel.value = row.kko || '';
                                            const objIn = rowEl.querySelector('input[name*="[objek]"]');
                                            if (objIn) objIn.value = row.objek || '';
                                            const konIn = rowEl.querySelector('input[name*="[konteks]"]');
                                            if (konIn) konIn.value = row.konteks || '';
                                            container.appendChild(rowEl);
                                        }
                                    });
                                    if (currentCplsList && currentCplsList.length > 0) {
                                        updateCplSelectDropdowns(currentCplsList);
                                    }
                                }
                            }
                            if (draft.data_step_1.cpl_ids && Array.isArray(draft.data_step_1.cpl_ids)) {
                                document.querySelectorAll('#cpl_checkbox_container input[name="cpl_ids[]"]').forEach(cb => {
                                    cb.checked = draft.data_step_1.cpl_ids.includes(cb.value) || draft.data_step_1.cpl_ids.includes(Number(cb.value));
                                });
                            }
                            if (draft.data_step_1.deskripsi_mk) {
                                const el = document.getElementById('deskripsi_mk');
                                if (el) el.value = draft.data_step_1.deskripsi_mk;
                            }
                        }

                        if (draft.data_step_2) {
                            for (const [key, val] of Object.entries(draft.data_step_2)) {
                                const input = document.querySelector(`[name="${key}"]`);
                                if (input) input.value = val;
                            }
                            if (typeof calculateTotalWeight === 'function') calculateTotalWeight();
                        }

                        if (draft.data_step_3) {
                            if (draft.data_step_3.referensi_data?.length) {
                                const refContainer = document.getElementById('references_container');
                                if (refContainer) {
                                    refContainer.innerHTML = '';
                                    draft.data_step_3.referensi_data.forEach(refText => {
                                        const row = document.createElement('div');
                                        row.className = 'ref-row';
                                        row.innerHTML = `
                                            <input type="text" name="referensi_data[]" class="field-control" value="${String(refText).replace(/"/g, '&quot;')}" required>
                                            <button type="button" class="btn-danger-sm btn-remove-ref" style="flex-shrink: 0; display:inline-flex; align-items:center; justify-content:center; width:36px; height:36px; padding:0;" title="Hapus referensi" aria-label="Hapus referensi">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        `;
                                        refContainer.appendChild(row);
                                    });
                                }
                            }
                            // Restore pertemuan data into in-memory object (fields are dynamically rendered)
                            if (draft.data_step_3.pertemuan_data) {
                                // Use window.pertemuanData since it's defined in outer script scope
                                const pData = window.pertemuanData;
                                if (pData) {
                                    for (const [key, val] of Object.entries(draft.data_step_3.pertemuan_data)) {
                                        // key format: "pertemuan_data[1][kemampuan_akhir]"
                                        const m = key.match(/pertemuan_data\[(\d+)\]\[(\w+)\]/);
                                        if (!m) continue;
                                        const p = parseInt(m[1]);
                                        const field = m[2];
                                        if (!pData[p]) pData[p] = {};
                                        if (field === 'target_cpmk') {
                                            // might be stored as JSON array string
                                            try {
                                                const parsed = typeof val === 'string' ? JSON.parse(val) : val;
                                                pData[p][field] = Array.isArray(parsed) ? parsed : (val ? String(val).split(',').map(s => s.trim()).filter(Boolean) : []);
                                            } catch(e) {
                                                pData[p][field] = val ? String(val).split(',').map(s => s.trim()).filter(Boolean) : [];
                                            }
                                        } else {
                                            pData[p][field] = val !== undefined && val !== null ? String(val) : '';
                                        }
                                    }
                                    if (typeof window.updateAllNavIndicators === 'function') window.updateAllNavIndicators();
                                    if (typeof window.updatePertemuanProgress === 'function') window.updatePertemuanProgress();
                                }
                            }
                        }
                    }

                    if (!window.lastDraftSaved) window.lastDraftSaved = {};
                    window.lastDraftSaved[method] = draft.updated_at?.substring(11, 16) || draft.updated_at || '';
                    updateDraftButtonState();
                }

                // Toggle Creation Method
                methodCards.forEach(card => {
                    card.addEventListener('click', function () {
                        setCreationMethod(this.dataset.method);
                    });
                });

                // Initial toggle state & dosen select init
                setCreationMethod(creationMethodInput ? creationMethodInput.value : 'upload');
                initDosenSelect();

                // CPL & Dosen loader
                const mkSelect = document.getElementById('mkSelect');
                const cplCheckboxContainer = document.getElementById('cpl_checkbox_container');

                if (mkSelect) {
                    mkSelect.addEventListener('change', function () {
                        const mkId = this.value;
                        syncSharedFields();
                        if (!mkId) return;

                        if (window.showLoader) window.showLoader();
                        if (cplCheckboxContainer) {
                            cplCheckboxContainer.innerHTML = '<p class="field-hint"><i class="fas fa-spinner fa-spin"></i> Memuat CPL</p>';
                        }

                        const cplPromise = fetch(`{{ route('banksoal.rps.dosen.cpl', '') }}/${mkId}`)
                            .then(res => res.json())
                            .then(data => {
                                if (cplCheckboxContainer) {
                                    cplCheckboxContainer.innerHTML = '';
                                    if (data.length === 0) {
                                        cplCheckboxContainer.innerHTML = '<p class="field-hint text-rose-500">Mata kuliah ini belum dipetakan ke CPL manapun.</p>';
                                    } else {
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
                                    }
                                }
                                updateCplSelectDropdowns(data);
                            });

                        const draftPromise = loadDraftIfExists(mkId);

                        Promise.all([cplPromise, draftPromise]).finally(() => {
                            if (window.hideLoader) window.hideLoader();
                        });
                    });

                    // Trigger initial change if URL contains mk parameter
                    const _urlP = new URLSearchParams(window.location.search);
                    const _resumeMk = _urlP.get('mk');
                    if (_resumeMk && mkSelect.value === _resumeMk) {
                        mkSelect.dispatchEvent(new Event('change'));
                    }
                }
                document.querySelectorAll('.btn-next-step').forEach(btn => {
                    btn.addEventListener('click', function () {
                        const currentPanel = this.closest('.wizard-content-section');
                        const inputs = currentPanel.querySelectorAll('input[required], select[required], textarea[required]');
                        let isValid = true;

                        inputs.forEach(input => {
                            let isInputEmpty = false;

                            if (input.tomselect) {
                                const val = input.tomselect.getValue();
                                if (!val || (Array.isArray(val) && val.length === 0)) {
                                    isInputEmpty = true;
                                }
                            } else if (!input.value || !input.value.trim() || !input.checkValidity()) {
                                isInputEmpty = true;
                            }

                            if (isInputEmpty) {
                                isValid = false;
                                if (input.tomselect && input.tomselect.control) {
                                    input.tomselect.control.style.borderColor = '#ef4444';
                                } else {
                                    input.style.borderColor = '#ef4444';
                                }
                            } else {
                                if (input.tomselect && input.tomselect.control) {
                                    input.tomselect.control.style.borderColor = '#cbd5e1';
                                } else {
                                    input.style.borderColor = '';
                                }
                            }
                        });

                        if (!isValid) {
                            alert('Harap lengkapi seluruh kolom yang wajib diisi (Required), termasuk Target CPMK pada tabel!');
                            return;
                        }

                        // Specifically for Step 2 total weight check (Kontrak Kuliah)
                        if (currentPanel && currentPanel.id === 'step_2_panel') {
                            const total = calculateTotalWeight();
                            if (total !== 100) {
                                alert('Total Bobot Penilaian Mata Kuliah harus tepat berjumlah 100%! Sekarang: ' + total + '%');
                                return;
                            }
                        }

                        const nextStep = parseInt(this.dataset.next);
                        if (nextStep) {
                            saveCache(nextStep);
                        }
                    });
                });

                document.querySelectorAll('.btn-prev-step').forEach(btn => {
                    btn.addEventListener('click', function () {
                        const prevStep = parseInt(this.dataset.prev);
                        saveCache(prevStep);
                    });
                });

                // Toggle sub-tabs
                const subTabBtns = document.querySelectorAll('.sub-tab-btn');
                const subTabPanels = document.querySelectorAll('.sub-tab-panel');

                subTabBtns.forEach(btn => {
                    btn.addEventListener('click', function () {
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
                    btn.addEventListener('click', function () {
                        const targetTab = this.dataset.nextTab;
                        const tabBtn = document.querySelector(`.sub-tab-btn[data-sub-tab="${targetTab}"]`);
                        if (tabBtn) tabBtn.click();
                    });
                });

                document.querySelectorAll('.btn-prev-sub-tab').forEach(btn => {
                    btn.addEventListener('click', function () {
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
                        if (total === 100) {
                            totalWeightLabel.style.color = '#10b981';
                        } else {
                            totalWeightLabel.style.color = '#ef4444';
                        }
                    }
                    return total;
                }

                document.addEventListener('input', function (e) {
                    if (e.target.type === 'number') {
                        const val = e.target.value;
                        if (val.length > 1 && val.startsWith('0')) {
                            e.target.value = val.replace(/^0+(?=\d)/, '');
                        }
                    }
                    if (e.target.classList.contains('weight-input')) {
                        calculateTotalWeight();
                    }
                });

                // Helper to attach toggle deselect hook & Navy badge formatting on TomSelect instance
                function attachTomSelectToggleDeselect(ts) {
                    if (!ts || typeof ts.hook !== 'function') return;

                    if (!ts._hasToggleHook) {
                        ts._hasToggleHook = true;
                        const orig_onOptionSelect = ts.onOptionSelect;
                        ts.hook('instead', 'onOptionSelect', (evt, option) => {
                            if (!option && evt && evt.target) {
                                option = evt.target.closest('.option');
                            }
                            if (!option) return;
                            const val = option.dataset?.value || option.getAttribute('data-value');
                            const normalizedVal = val ? String(val) : '';
                            const selectedValues = (ts.getValue ? ts.getValue() : ts.items || []).toString().split(',').filter(Boolean);
                            const isCurrentlySelected = !!(normalizedVal && selectedValues.includes(normalizedVal));
                            if (isCurrentlySelected || option.classList.contains('selected')) {
                                option.classList.remove('selected');
                                if (normalizedVal) {
                                    ts.removeItem(normalizedVal);
                                }
                                ts.refreshOptions(false);
                                ts.refreshItems();
                                if (evt && evt.preventDefault) evt.preventDefault();
                                return;
                            }
                            orig_onOptionSelect.call(ts, evt, option);
                        });
                    }

                    const updateDisplayAndSort = () => {
                        if (!ts) return;

                        const allOptions = Object.values(ts.options || {});
                        allOptions.forEach(opt => {
                            opt.$is_selected = ts.items.includes(String(opt.value));
                        });
                        allOptions.sort((a, b) => {
                            if (a.$is_selected && !b.$is_selected) return -1;
                            if (!a.$is_selected && b.$is_selected) return 1;
                            return (a.$order || 0) - (b.$order || 0);
                        });

                        const count = ts.items.length;
                        const control = ts.control;
                        if (control) {
                            control.style.display = 'flex';
                            control.style.flexWrap = 'nowrap';
                            control.style.overflowX = 'auto';
                            control.style.overflowY = 'hidden';
                            control.style.height = '38px';
                            control.style.minHeight = '38px';
                            control.style.maxHeight = '38px';
                            control.style.boxSizing = 'border-box';
                            control.style.alignItems = 'center';
                            control.style.gap = '4px';
                            control.style.width = '100%';
                            control.style.minWidth = '220px';
                            if (ts.wrapper) ts.wrapper.style.width = '100%';

                            const itemEls = control.querySelectorAll('.item');
                            itemEls.forEach((el, index) => {
                                const removeBtn = el.querySelector('.remove');
                                if (count > 0 && index === 0) {
                                    const textNode = Array.from(el.childNodes).find(n => n.nodeType === Node.TEXT_NODE);
                                    if (textNode) {
                                        textNode.textContent = `${count} Terpilih`;
                                    } else {
                                        el.insertBefore(document.createTextNode(`${count} Terpilih`), el.firstChild);
                                    }
                                    el.style.display = 'inline-flex';
                                    el.style.alignItems = 'center';
                                    el.style.backgroundColor = '#f1f5f9';
                                    el.style.color = '#0b266e';
                                    el.style.fontWeight = '600';
                                    el.style.border = '1px solid #cbd5e1';
                                    el.style.borderRadius = '6px';
                                    el.style.padding = '2px 8px';
                                    el.style.fontSize = '12px';
                                    el.style.whiteSpace = 'nowrap';
                                    el.style.flexShrink = '0';
                                    el.style.height = '24px';
                                    el.style.boxSizing = 'border-box';
                                    if (removeBtn) {
                                        removeBtn.style.display = '';
                                        removeBtn.style.marginLeft = '4px';
                                        removeBtn.style.color = '#0b266e';
                                    }
                                } else {
                                    el.style.display = 'none';
                                    if (removeBtn) removeBtn.style.display = 'none';
                                }
                            });
                        }
                    };

                    if (!ts._hasDisplayEvents) {
                        ts._hasDisplayEvents = true;
                        ts.on('change', () => {
                            updateDisplayAndSort();
                            ts.refreshOptions(false);
                        });
                        ts.on('item_add', updateDisplayAndSort);
                        ts.on('item_remove', updateDisplayAndSort);
                    }

                    updateDisplayAndSort();
                }

                window.attachTomSelectToggleDeselect = attachTomSelectToggleDeselect;

                // Populate Target CPMK dropdowns dynamically
                function populateCpmkDropdowns() {
                    const list = [];
                    const seen = new Set();
                    const rows = document.querySelectorAll('#cpmkRowsGenerator [data-cpmk-row], #cpmkRowsGenerator .cpmk-row');
                    rows.forEach((row, idx) => {
                        const cplSelect = row.querySelector('[data-cpmk-cpl-select], .cpl-select-input');
                        const kodeInput = row.querySelector('input[name*="[kode]"]');
                        const kodeVal = kodeInput ? kodeInput.value.trim() : '';
                        if (!kodeVal) return;

                        let cplNum = idx + 1;
                        if (cplSelect && cplSelect.selectedIndex >= 0) {
                            const opt = cplSelect.options[cplSelect.selectedIndex];
                            if (opt && opt.value) {
                                const m = opt.textContent.match(/\d+/);
                                if (m) cplNum = parseInt(m[0], 10);
                            }
                        }
                        const val = `${cplNum}.${kodeVal}`;
                        if (!seen.has(val)) {
                            seen.add(val);
                            list.push({
                                value: val,
                                label: `CPMK ${val}`
                            });
                        }
                    });

                    const targetSelects = document.querySelectorAll('.cpmk-target-select, .pertemuan-cpmk-select');
                    targetSelects.forEach(select => {
                        const placeholderOption = '<option value="" disabled>Belum ada CPMK — isi Step 1 terlebih dahulu</option>';

                        if (list.length === 0) {
                            if (select.tomselect) {
                                select.tomselect.destroy();
                            }
                            select.innerHTML = placeholderOption;
                            select.disabled = true;
                            select.setAttribute('aria-label', 'Target CPMK');
                            return;
                        }

                        select.disabled = false;
                        let currentVals = [];
                        if (select.tomselect) {
                            const tsVal = select.tomselect.getValue();
                            currentVals = Array.isArray(tsVal) ? tsVal : (tsVal ? [tsVal] : []);
                        } else {
                            currentVals = select.selectedOptions ? Array.from(select.selectedOptions).map(opt => opt.value) : [];
                        }

                        if (select.dataset.selectedValues) {
                            try {
                                const pre = JSON.parse(select.dataset.selectedValues);
                                if (Array.isArray(pre)) {
                                    currentVals = [...new Set([...currentVals, ...pre.map(String)])];
                                } else if (pre) {
                                    currentVals = [...new Set([...currentVals, String(pre)])];
                                }
                            } catch (e) { }
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
                            attachTomSelectToggleDeselect(ts);
                            ts.refreshOptions(false);
                        } else {
                            if (select.tomselect) select.tomselect.destroy();
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
                                const cpmkTs = new TomSelect(select, {
                                    plugins: { remove_button: { title: "Hapus CPMK" } },
                                    maxOptions: 100,
                                    persist: false,
                                    create: false,
                                    hideSelected: false,
                                    render: {
                                        item: function(data, escape) {
                                            return `<div class="item" style="background:#f1f5f9;color:#0b266e;font-weight:600;border:1px solid #cbd5e1;border-radius:6px;padding:2px 8px;font-size:12px;margin:2px;">${escape(data.text)}</div>`;
                                        },
                                        option: function(data, escape) {
                                            return `<div class="option py-2 px-3">${escape(data.text)}</div>`;
                                        }
                                    }
                                });
                                attachTomSelectToggleDeselect(cpmkTs);
                            }
                        }
                    });
                }

                // Reference Section CRUD
                const refContainer = document.getElementById('references_container');
                document.getElementById('btnAddReference').addEventListener('click', function () {
                    const row = document.createElement('div');
                    row.className = 'ref-row';
                    row.innerHTML = `
                        <input type="text" name="referensi_data[]" class="field-control" placeholder="Tulis rujukan referensi baru..." required>
                        <button type="button" class="btn-danger-sm btn-remove-ref" style="flex-shrink: 0; display:inline-flex; align-items:center; justify-content:center; width:36px; height:36px; padding:0;" title="Hapus referensi" aria-label="Hapus referensi">
                            <i class="fas fa-trash"></i>
                        </button>
                    `;
                    refContainer.appendChild(row);
                    triggerAutoSave();
                });

                document.addEventListener('click', function (e) {
                    if (e.target.closest('.btn-remove-ref')) {
                        const row = e.target.closest('.ref-row');
                        if (refContainer.querySelectorAll('.ref-row').length > 1) {
                            row.remove();
                            triggerAutoSave();
                        } else {
                            alert('Minimal harus mengisi satu referensi!');
                        }
                    }
                });



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
                            if (String(cpl.id) === String(selectedVal)) {
                                opt.selected = true;
                            }
                            select.appendChild(opt);
                        });
                        if (selectedVal) {
                            select.value = selectedVal;
                        }
                        if (cplList.length > 0) {
                            select.disabled = false;
                        }
                    });
                }

                // Generator CPMK row builder
                document.getElementById('addCpmkRowBtnGenerator').addEventListener('click', function () {
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
                document.addEventListener('click', function (e) {
                    if (e.target.closest('[data-remove-cpmk-row]')) {
                        const row = e.target.closest('[data-cpmk-row]');
                        const rowsContainer = row.parentNode;
                        if (rowsContainer.querySelectorAll('[data-cpmk-row]').length > 1) {
                            row.remove();
                            triggerAutoSave();
                        } else {
                            alert('Minimal harus menyertakan 1 CPMK!');
                        }
                    }
                });

                // Render 16 Pertemuan Grid dynamically (if legacy containers exist)
                const pertemuanSebelumUtsContainer = document.getElementById('pertemuan_sebelum_uts_container');
                const pertemuanSetelahUtsContainer = document.getElementById('pertemuan_setelah_uts_container');

                function renderPertemuanRows() {
                    if (!pertemuanSebelumUtsContainer || !pertemuanSetelahUtsContainer) return;
                    pertemuanSebelumUtsContainer.innerHTML = '';
                    pertemuanSetelahUtsContainer.innerHTML = '';
                    for (let i = 1; i <= 16; i++) {
                        const row = document.createElement('div');
                        row.className = `grid-table-row pertemuan-row-item`;
                        row.dataset.pertemuan = i;

                        if (i === 8 || i === 16) {
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
                    const method = creationMethodInput?.value || 'generator';
                    const stepDone = (nextStepNum || 1) - 1; // step yang baru selesai
                    window.currentGeneratorStep = nextStepNum;
                    saveStepToDraft(method, stepDone, () => {
                        if (nextStepNum) loadWizardStep(nextStepNum);
                    });
                }


                function restoreCache(mkId) {
                    return fetch("{{ route('banksoal.rps.dosen.get-wizard-cache', ['mkId' => 'PLACEHOLDER']) }}".replace('PLACEHOLDER', mkId))
                        .then(res => res.json())
                        .then(res => {
                            if (res.success && res.data) {
                                const cache = res.data;

                                // 1. Restore Step 1 CPMK rows
                                if (cache.cpmk_rows && cache.cpmk_rows.length > 0) {
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
                                        if (rowEl) {
                                            const cplSelect = rowEl.querySelector('select[name*="[cpl_id]"]');
                                            if (cplSelect) {
                                                cplSelect.dataset.selectedValue = row.cpl_id;
                                                cplSelect.value = row.cpl_id;
                                            }

                                            const kodeInput = rowEl.querySelector('input[name*="[kode]"]');
                                            if (kodeInput) kodeInput.value = row.kode;

                                            const kkoSelect = rowEl.querySelector('select[name*="[kko]"]');
                                            if (kkoSelect) kkoSelect.value = row.kko;

                                            const objekInput = rowEl.querySelector('input[name*="[objek]"]');
                                            if (objekInput) objekInput.value = row.objek;

                                            const konteksInput = rowEl.querySelector('input[name*="[konteks]"]');
                                            if (konteksInput) konteksInput.value = row.konteks || '';
                                        }
                                    });
                                } else {
                                    buildInitialCpmkGenerator();
                                }

                                // 2. Restore Step 2 Penilaian
                                if (cache.penilaian_data) {
                                    cache.penilaian_data.forEach((item, index) => {
                                        const komponenEl = document.querySelector(`textarea[name="penilaian_data[${index}][komponen]"]`);
                                        const bobotEl = document.querySelector(`input[name="penilaian_data[${index}][bobot]"]`);
                                        const cpmkSelect = document.querySelector(`select[name="penilaian_data[${index}][cpmk][]"]`);

                                        if (komponenEl) komponenEl.value = item.komponen || '';
                                        if (bobotEl) bobotEl.value = item.bobot || 0;
                                        if (cpmkSelect && item.cpmk) {
                                            const vals = Array.isArray(item.cpmk) ? item.cpmk : [item.cpmk];
                                            cpmkSelect.dataset.selectedValues = JSON.stringify(vals);
                                        }
                                    });
                                    // Restore sub_rows for UTS/UAS
                                    if (cache.penilaian_data[4] && cache.penilaian_data[4].sub_rows) {
                                        const utsBobot = document.querySelector(`input[name="penilaian_data[4][sub_rows][0][bobot]"]`);
                                        const utsSelect = document.querySelector(`select[name="penilaian_data[4][sub_rows][0][cpmk][]"]`);
                                        const uasBobot = document.querySelector(`input[name="penilaian_data[4][sub_rows][1][bobot]"]`);
                                        const uasSelect = document.querySelector(`select[name="penilaian_data[4][sub_rows][1][cpmk][]"]`);

                                        if (utsBobot) utsBobot.value = cache.penilaian_data[4].sub_rows[0].bobot || 0;
                                        if (utsSelect && cache.penilaian_data[4].sub_rows[0].cpmk) {
                                            const vals = Array.isArray(cache.penilaian_data[4].sub_rows[0].cpmk) ? cache.penilaian_data[4].sub_rows[0].cpmk : [cache.penilaian_data[4].sub_rows[0].cpmk];
                                            utsSelect.dataset.selectedValues = JSON.stringify(vals);
                                        }
                                        if (uasBobot) uasBobot.value = cache.penilaian_data[4].sub_rows[1].bobot || 0;
                                        if (uasSelect && cache.penilaian_data[4].sub_rows[1].cpmk) {
                                            const vals = Array.isArray(cache.penilaian_data[4].sub_rows[1].cpmk) ? cache.penilaian_data[4].sub_rows[1].cpmk : [cache.penilaian_data[4].sub_rows[1].cpmk];
                                            uasSelect.dataset.selectedValues = JSON.stringify(vals);
                                        }
                                    }
                                    calculateTotalWeight();
                                }

                                // Restore Step 2 Deskripsi
                                if (cache.deskripsi_mk) {
                                    document.getElementById('deskripsi_mk').value = cache.deskripsi_mk;
                                }

                                // Restore Step 3 Pertemuan
                                if (cache.pertemuan_data) {
                                    for (let key in cache.pertemuan_data) {
                                        const item = cache.pertemuan_data[key];
                                        if (document.querySelector(`textarea[name="pertemuan_data[${key}][kemampuan_akhir]"]`)) {
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
                                if (cache.referensi_data) {
                                    refContainer.innerHTML = '';
                                    cache.referensi_data.forEach(ref => {
                                        const row = document.createElement('div');
                                        row.className = 'ref-row';
                                        row.innerHTML = `
                                            <input type="text" name="referensi_data[]" class="field-control" value="${ref}" required>
                                            <button type="button" class="btn-danger-sm btn-remove-ref" style="flex-shrink: 0; display:inline-flex; align-items:center; justify-content:center; width:36px; height:36px; padding:0;" title="Hapus referensi" aria-label="Hapus referensi">
                                                <i class="fas fa-trash"></i>
                                            </button>
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
                        const activeForm = document.getElementById('generatorForm') || document.getElementById('uploadForm');
                        if (!activeForm) return;
                        const formData = new FormData(activeForm);
                        fetch("{{ route('banksoal.rps.dosen.wizard-cache') }}", {
                            method: 'POST',
                            headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                            body: formData
                        });
                    }, 2000);
                }

                document.addEventListener('input', (e) => { if (e.target.closest('.wizard-content-section')) triggerAutoSave(); });
                document.addEventListener('change', (e) => { if (e.target.closest('.wizard-content-section')) triggerAutoSave(); });

                if (rpsSubmitForm) rpsSubmitForm.addEventListener('submit', function (e) {
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

                    const mkShadow = document.getElementById('mkSelect_shadow');
                    const mkEl = document.getElementById('mkSelect');
                    if (mkShadow && mkEl) {
                        mkShadow.value = mkEl.value || mkShadow.value;
                    } else if (mkEl && !mkEl.disabled) {
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
            // ─── Card Navigator Pertemuan ─────────────────────────────────────────────────
            let currentPertemuan = 1;
            const PERTEMUAN_REAL = [1,2,3,4,5,6,7,9,10,11,12,13,14,15]; // skip 8 (UTS) dan 16 (UAS)
            const KKO_OPTIONS = {
                'C1':'Mengingat','C2':'Memahami','C3':'Menerapkan','C4':'Menganalisis',
                'C5':'Mengevaluasi','C6':'Mencipta','P1':'Meniru','P2':'Menyesuaikan',
                'P3':'Membiasakan','P4':'Menguasai','P5':'Mahir','A1':'Menerima',
                'A2':'Merespon','A3':'Menilai','A4':'Mengorganisasi','A5':'Menghayati',
            };

            // Simpan data semua pertemuan di memori (exposed via window for cross-scope access)
            const pertemuanData = window.pertemuanData = {};
            for (let i = 1; i <= 16; i++) {
                if (i === 8 || i === 16) continue;
                pertemuanData[i] = {
                    pertemuan: String(i),
                    kemampuan_akhir: '', pokok_bahasan: '', metode: '',
                    waktu: '150', pengalaman_belajar: '', target_cpmk: [],
                    kriteria_penilaian: '', bobot: '5',
                };
            }

            function renderPertemuanFields(p) {
                const data   = pertemuanData[p] || {};
                const cpmkList = buildCpmkListFromRows();
                // target_cpmk can be array, comma-string, or undefined — normalize to array
                const selectedCpmk = Array.isArray(data.target_cpmk)
                    ? data.target_cpmk.map(value => String(value).trim()).filter(Boolean)
                    : (typeof data.target_cpmk === 'string' && data.target_cpmk
                        ? data.target_cpmk.split(',').map(s => String(s).trim()).filter(Boolean)
                        : []);
                const cpmkOptions = cpmkList.map(c =>
                    `<option value="${c.value}" ${selectedCpmk.includes(String(c.value)) ? 'selected' : ''}>${c.label}</option>`
                ).join('');
                const noOptionsMsg = cpmkList.length === 0
                    ? '<option value="" disabled>Belum ada CPMK &mdash; isi Step 1 terlebih dahulu</option>'
                    : '';

                document.getElementById('pertemuanFields').innerHTML = `
                    <div style="display:grid; grid-template-columns:1fr 1fr; gap:14px; margin-bottom:14px;">
                        <div>
                            <label style="font-size:11px; font-weight:600; color:var(--c-fg-muted,#94A3B8); text-transform:uppercase; letter-spacing:.06em; display:block; margin-bottom:6px;">Kemampuan Akhir *</label>
                            <textarea id="pf_kemampuan_akhir" name="pertemuan_data[${p}][kemampuan_akhir]" rows="3"
                                style="width:100%; height:80px; padding:8px 11px; border:1px solid var(--c-border,#DFE1E7); border-radius:8px; font-size:13px; resize:vertical; font-family:inherit; box-sizing:border-box;"
                                oninput="savePertemuanField(${p},'kemampuan_akhir',this.value)">${data.kemampuan_akhir||''}</textarea>
                        </div>
                        <div>
                            <label style="font-size:11px; font-weight:600; color:var(--c-fg-muted,#94A3B8); text-transform:uppercase; letter-spacing:.06em; display:block; margin-bottom:6px;">Bahan Kajian *</label>
                            <textarea id="pf_pokok_bahasan" name="pertemuan_data[${p}][pokok_bahasan]" rows="3"
                                style="width:100%; height:80px; padding:8px 11px; border:1px solid var(--c-border,#DFE1E7); border-radius:8px; font-size:13px; resize:vertical; font-family:inherit; box-sizing:border-box;"
                                oninput="savePertemuanField(${p},'pokok_bahasan',this.value)">${data.pokok_bahasan||''}</textarea>
                        </div>
                    </div>
                    <div style="display:grid; grid-template-columns:2fr 1fr 2fr; gap:14px; margin-bottom:14px;">
                        <div>
                            <label style="font-size:11px; font-weight:600; color:var(--c-fg-muted,#94A3B8); text-transform:uppercase; letter-spacing:.06em; display:block; margin-bottom:6px;">Metode *</label>
                            <input type="text" id="pf_metode" name="pertemuan_data[${p}][metode]"
                                value="${data.metode||''}"
                                style="width:100%; height:36px; padding:0 11px; border:1px solid var(--c-border,#DFE1E7); border-radius:8px; font-size:13px; font-family:inherit; box-sizing:border-box;"
                                oninput="savePertemuanField(${p},'metode',this.value)">
                        </div>
                        <div>
                            <label style="font-size:11px; font-weight:600; color:var(--c-fg-muted,#94A3B8); text-transform:uppercase; letter-spacing:.06em; display:block; margin-bottom:6px;">Waktu (menit) *</label>
                            <input type="number" id="pf_waktu" name="pertemuan_data[${p}][waktu]"
                                value="${data.waktu||150}"
                                style="width:100%; height:36px; padding:0 11px; border:1px solid var(--c-border,#DFE1E7); border-radius:8px; font-size:13px; font-family:inherit; box-sizing:border-box; text-align:center;"
                                oninput="savePertemuanField(${p},'waktu',this.value)">
                        </div>
                        <div>
                            <label style="font-size:11px; font-weight:600; color:var(--c-fg-muted,#94A3B8); text-transform:uppercase; letter-spacing:.06em; display:block; margin-bottom:6px;">Bobot (%)</label>
                            <input type="number" id="pf_bobot" name="pertemuan_data[${p}][bobot]"
                                value="${data.bobot||5}" min="0" max="100"
                                style="width:100%; height:36px; padding:0 11px; border:1px solid var(--c-border,#DFE1E7); border-radius:8px; font-size:13px; font-family:inherit; box-sizing:border-box; text-align:center;"
                                oninput="savePertemuanField(${p},'bobot',this.value)">
                        </div>
                    </div>
                    <div style="margin-bottom:14px;">
                        <label style="font-size:11px; font-weight:600; color:var(--c-fg-muted,#94A3B8); text-transform:uppercase; letter-spacing:.06em; display:block; margin-bottom:6px;">Pengalaman Belajar *</label>
                        <textarea id="pf_pengalaman_belajar" name="pertemuan_data[${p}][pengalaman_belajar]" rows="2"
                            style="width:100%; padding:8px 11px; border:1px solid var(--c-border,#DFE1E7); border-radius:8px; font-size:13px; resize:vertical; font-family:inherit; box-sizing:border-box;"
                            oninput="savePertemuanField(${p},'pengalaman_belajar',this.value)">${data.pengalaman_belajar||''}</textarea>
                    </div>
                    <div style="display:grid; grid-template-columns:1fr 1fr; gap:14px;">
                        <div>
                            <label style="font-size:11px; font-weight:600; color:var(--c-fg-muted,#94A3B8); text-transform:uppercase; letter-spacing:.06em; display:block; margin-bottom:6px;">Target CPMK *</label>
                            <select id="pf_target_cpmk" name="pertemuan_data[${p}][target_cpmk][]" multiple
                                style="width:100%; padding:8px 11px; border:1px solid var(--c-border,#DFE1E7); border-radius:8px; font-size:13px; min-height:80px; font-family:inherit; box-sizing:border-box;"
                                onchange="savePertemuanField(${p},'target_cpmk', Array.from(this.selectedOptions).map(o=>o.value))">
                                ${cpmkOptions || noOptionsMsg}
                            </select>
                        </div>
                        <div>
                            <label style="font-size:11px; font-weight:600; color:var(--c-fg-muted,#94A3B8); text-transform:uppercase; letter-spacing:.06em; display:block; margin-bottom:6px;">Kriteria & Indikator *</label>
                            <textarea id="pf_kriteria" name="pertemuan_data[${p}][kriteria_penilaian]" rows="3"
                                style="width:100%; padding:8px 11px; border:1px solid var(--c-border,#DFE1E7); border-radius:8px; font-size:13px; resize:vertical; font-family:inherit; box-sizing:border-box;"
                                oninput="savePertemuanField(${p},'kriteria_penilaian',this.value)">${data.kriteria_penilaian||''}</textarea>
                        </div>
                    </div>
                    <input type="hidden" name="pertemuan_data[${p}][pertemuan]" value="${p}">
                `;

                if (typeof TomSelect !== 'undefined') {
                    const pfSel = document.getElementById('pf_target_cpmk');
                    if (pfSel) {
                        if (pfSel.tomselect) pfSel.tomselect.destroy();
                        const pfTs = new TomSelect(pfSel, {
                            plugins: { remove_button: { title: "Hapus CPMK" } },
                            maxOptions: 100,
                            persist: false,
                            create: false,
                            hideSelected: false,
                            render: {
                                item: function(data, escape) {
                                    return `<div class="item" style="background:#f1f5f9;color:#0b266e;font-weight:600;border:1px solid #cbd5e1;border-radius:6px;padding:2px 8px;font-size:12px;">${escape(data.text)}</div>`;
                                },
                                option: function(data, escape) {
                                    return `<div class="option py-2 px-3">${escape(data.text)}</div>`;
                                }
                            },
                            onChange: function(values) {
                                savePertemuanField(p, 'target_cpmk', Array.isArray(values) ? values : (values ? [values] : []));
                            }
                        });
                        attachTomSelectToggleDeselect(pfTs);
                    }
                }
            }

            function savePertemuanField(p, field, value) {
                if (!pertemuanData[p]) pertemuanData[p] = {};
                pertemuanData[p][field] = value;
                updatePertemuanNavIndicator(p);
                updatePertemuanProgress();
            }

            function isMeaningfulPertemuanValue(key, value) {
                if (value === undefined || value === null) return false;

                if (Array.isArray(value)) {
                    return value.some(item => String(item).trim() !== '');
                }

                if (typeof value === 'string') {
                    const trimmed = value.trim();
                    if (trimmed === '') return false;
                    if (key === 'waktu') return Number(trimmed) !== 150;
                    return true;
                }

                if (typeof value === 'number') {
                    if (!Number.isFinite(value)) return false;
                    if (key === 'waktu') return value !== 150;
                    return value > 0;
                }

                return !!value;
            }

            function getPertemuanState(p) {
                const d = pertemuanData[p];
                if (!d) return 'empty';

                const requiredFields = [
                    { key: 'kemampuan_akhir', validator: value => !!String(value || '').trim() },
                    { key: 'pokok_bahasan', validator: value => !!String(value || '').trim() },
                    { key: 'metode', validator: value => !!String(value || '').trim() },
                    { key: 'waktu', validator: value => Number(value) > 0 },
                    { key: 'pengalaman_belajar', validator: value => !!String(value || '').trim() },
                    { key: 'target_cpmk', validator: value => Array.isArray(value) ? value.length > 0 : !!value },
                    { key: 'kriteria_penilaian', validator: value => !!String(value || '').trim() },
                ];

                const entered = requiredFields.filter(({ key }) => isMeaningfulPertemuanValue(key, d[key])).length;

                const invalid = requiredFields.some(({ key, validator }) => {
                    const value = d[key];
                    if (!isMeaningfulPertemuanValue(key, value)) {
                        return false;
                    }
                    return !validator(value);
                });

                if (invalid) return 'invalid';
                if (entered === 0) return 'empty';

                const allFilled = requiredFields.every(({ key, validator }) => {
                    const value = d[key];
                    if (value === undefined || value === null || value === '' || (Array.isArray(value) && value.length === 0)) {
                        return false;
                    }
                    return validator(value);
                });

                return allFilled ? 'filled' : 'partial';
            }

            function updatePertemuanNavIndicator(p) {
                const state = getPertemuanState(p);
                const dot    = document.querySelector(`[data-filled-${p}]`);
                const btn    = document.querySelector(`button[data-nav-p="${p}"]`);
                if (dot) {
                    const showDot = state !== 'empty';
                    dot.style.display = showDot ? 'block' : 'none';
                    dot.style.background = state === 'invalid' ? '#ef4444' : '#0B266E';
                }
                if (btn) {
                    const isActive = p === currentPertemuan;
                    if (state === 'invalid') {
                        btn.style.background = isActive ? '#fff5f5' : '#fff5f5';
                        btn.style.color = '#b91c1c';
                        btn.style.borderColor = '#ef4444';
                    } else if (state === 'filled') {
                        btn.style.background = '#0B266E';
                        btn.style.color = '#fff';
                        btn.style.borderColor = '#0B266E';
                    } else if (state === 'partial') {
                        btn.style.background = '#fff';
                        btn.style.color = isActive ? '#0B266E' : '#64748b';
                        btn.style.borderColor = isActive ? '#0B266E' : '#DFE1E7';
                    } else {
                        btn.style.background = '#fff';
                        btn.style.color = isActive ? '#0B266E' : '#64748b';
                        btn.style.borderColor = isActive ? '#0B266E' : '#DFE1E7';
                    }
                    btn.dataset.active = isActive ? '1' : '0';
                }
            }

            function updatePertemuanProgress() {
                const filled = PERTEMUAN_REAL.filter(p => getPertemuanState(p) === 'filled').length;
                const el = document.getElementById('pertemuanProgress');
                if (el) el.textContent = `${filled} / 14 terisi`;
            }

            function goToPertemuan(p) {
                if (p === 8 || p === 16) return; // UTS/UAS — tidak bisa diklik
                currentPertemuan = p;
                document.body.dataset.currentPertemuan = String(p);
                renderPertemuanFields(p);
                updateAllNavIndicators();

                const title = document.getElementById('pertemuanTitle');
                const displayNum = p < 8 ? p : p - 1;
                if (title) title.textContent = `Pertemuan ${displayNum}`;

                // Prev/Next button state
                const idx  = PERTEMUAN_REAL.indexOf(p);
                const prev = document.getElementById('btnPrevPertemuan');
                const next = document.getElementById('btnNextPertemuan');
                if (prev) prev.disabled = idx <= 0;
                if (next) next.textContent = idx >= PERTEMUAN_REAL.length - 1
                    ? '← Ke Referensi →' : 'Selanjutnya ›';
            }

            function prevPertemuan() {
                const idx = PERTEMUAN_REAL.indexOf(currentPertemuan);
                if (idx > 0) goToPertemuan(PERTEMUAN_REAL[idx - 1]);
            }

            function nextPertemuan() {
                const idx = PERTEMUAN_REAL.indexOf(currentPertemuan);
                if (idx < PERTEMUAN_REAL.length - 1) {
                    goToPertemuan(PERTEMUAN_REAL[idx + 1]);
                } else {
                    // Semua pertemuan sudah selesai — scroll ke referensi
                    document.getElementById('references_container')?.scrollIntoView({ behavior: 'smooth' });
                }
            }

            function updateAllNavIndicators() {
                PERTEMUAN_REAL.forEach(p => updatePertemuanNavIndicator(p));
            }
            window.updateAllNavIndicators = updateAllNavIndicators;
            window.updatePertemuanProgress = updatePertemuanProgress;

            function buildCpmkListFromRows() {
                const list = [];
                const seen = new Set();
                document.querySelectorAll('#cpmkRowsGenerator [data-cpmk-row]').forEach(row => {
                    const cplSel  = row.querySelector('.cpl-select-input');
                    const kodeIn  = row.querySelector('input[name*="[kode]"]');
                    if (!cplSel?.value || !kodeIn?.value?.trim()) return;
                    const opt     = cplSel.options[cplSel.selectedIndex];
                    const cplText = opt?.textContent?.trim() || '';
                    const cplNum  = (cplText.match(/\d+/) || [cplText])[0];
                    const val     = `${parseInt(cplNum)||cplNum}.${kodeIn.value.trim()}`;
                    if (!seen.has(val)) {
                        seen.add(val);
                        list.push({ value: val, label: `CPMK ${val}` });
                    }
                });
                return list;
            }

            // Init card navigator saat step 3 pertama kali dibuka
            document.addEventListener('DOMContentLoaded', () => {
                // Akan dipanggil dari loadWizardStep(3)
            });

            // Override loadWizardStep untuk init navigator saat masuk step 3
            const _origLoadWizardStep = typeof loadWizardStep === 'function' ? loadWizardStep : null;
            // (dipanggil dari DOMContentLoaded setelah seluruh script siap)
            setTimeout(() => {
                const origFn = window.loadWizardStep;
                if (origFn) {
                    window.loadWizardStep = function(stepNum) {
                        origFn(stepNum);
                        if (stepNum === 3) {
                            const titleEl = document.getElementById('pertemuanTitle');
                            const currentTitleNum = titleEl ? Number((titleEl.textContent || '').match(/\d+/)?.[0]) : null;
                            const stored = Number(document.body.dataset.currentPertemuan || currentPertemuan || 1);
                            const safeMeeting = Number.isFinite(stored) && stored > 0 && stored !== 8 && stored !== 16 ? stored : 1;

                            if (titleEl && Number.isFinite(currentTitleNum) && currentTitleNum > 1) {
                                currentPertemuan = currentTitleNum;
                                document.body.dataset.currentPertemuan = String(currentTitleNum);
                                updateAllNavIndicators();
                                updatePertemuanProgress();
                                return;
                            }

                            if (safeMeeting !== 1 || !titleEl || !/^Pertemuan \d+$/.test(titleEl.textContent.trim())) {
                                goToPertemuan(safeMeeting);
                            }
                            updateAllNavIndicators();
                            updatePertemuanProgress();
                        }
                    };
                }
            }, 100);

        </script>
        <script src="{{ asset('modules/banksoal/js/Banksoal/components/RpsCpmkRows.js') }}?v={{ time() }}"></script>
    @endpush
</x-banksoal::layouts.dosen-admin>