<x-banksoal::layouts.dosen-admin :bank-soal="true">
    @section('breadcrumbs')
        <a href="{{ route('banksoal.rps.dosen.index') }}"
            class="text-slate-500 hover:text-primary transition-colors">Manajemen RPS</a>
        <span class="mx-2 text-slate-300">/</span>
        <span class="text-slate-800 font-semibold">Edit RPS</span>
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
                overflow: visible;
                margin-bottom: 20px;
            }

            .ts-dropdown {
                z-index: 99999 !important;
            }

            .has-summary-badge .ts-control>.item,
            #dosenSelect+.ts-wrapper .ts-control>.item {
                display: none !important;
            }

            .ts-summary-badge {
                display: inline-flex;
                align-items: center;
                background: #f1f5f9;
                color: #0b266e;
                font-weight: 600;
                border: 1px solid #cbd5e1;
                border-radius: 6px;
                padding: 2px 8px;
                font-size: 12px;
                white-space: nowrap;
                flex-shrink: 0;
                height: 24px;
                box-sizing: border-box;
                cursor: default;
                user-select: none;
            }

            .ts-summary-badge .ts-summary-clear {
                display: inline-flex;
                align-items: center;
                justify-content: center;
                margin-left: 6px;
                color: #0b266e;
                font-size: 14px;
                font-weight: 700;
                cursor: pointer;
                line-height: 1;
                opacity: 0.7;
                transition: opacity 0.15s;
            }

            .ts-summary-badge .ts-summary-clear:hover {
                opacity: 1;
                color: #ef4444;
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

            .mk-badge {
                display: inline-flex;
                align-items: center;
                gap: 8px;
                background: var(--slate-100);
                border: 1px solid var(--slate-200);
                border-radius: 8px;
                padding: 10px 14px;
                font-size: 14px;
                font-weight: 600;
                color: var(--slate-700);
                width: 100%;
            }

            .mk-badge i {
                color: var(--primary-blue);
            }

            .hidden {
                display: none !important;
            }
        </style>
    @endpush

    @php
        $creationMethod = old('creation_method', $rps->creation_method ?? 'upload');
    @endphp

    <x-banksoal::ui.bank-soal-page>
        <x-slot:header>
            <div class="bs-heading-row">
                <div>
                    <div class="bs-heading-label">
                        <h1>Revisi RPS</h1>
                        <span class="bs-role-badge">Dosen</span>
                        <span class="badge {{ match ($rps->status->value) {
    'diajukan' => 'bg-amber-100 text-amber-800 border-amber-200',
    'revisi' => 'bg-rose-100 text-rose-800 border-rose-200',
    'disetujui' => 'bg-emerald-100 text-emerald-800 border-emerald-200',
    default => 'bg-slate-100 text-slate-800 border-slate-200'
} }}"
                            style="padding: 2px 8px; border-radius: 999px; font-size: 12px; font-weight: 600;">{{ $rps->status->label() }}</span>
                    </div>
                    <p>Perbarui Rencana Pembelajaran Semester mata kuliah Anda.</p>
                </div>
                <a href="{{ route('banksoal.rps.dosen.index') }}" class="dosen-management-btn">
                    <i class="fas fa-arrow-left"></i> Kembali
                </a>
            </div>
        </x-slot:header>

        <x-banksoal::notification.alerts />

        @if(!$isUploadOpen)
            <div class="alert-closed"
                style="display:flex;align-items:flex-start;gap:12px;background:#fff7ed;border:1px solid #fed7aa;border-radius:10px;padding:14px 16px;margin-bottom:20px;font-size:13px;color:#9a3412;">
                <i class="fas fa-lock"></i>
                <div>
                    <strong>Periode upload RPS saat ini tidak aktif.</strong> Anda tidak dapat menyimpan perubahan.
                </div>
            </div>
        @endif

        @if($rps->status->value === 'revisi')
            <div
                style="display:flex;align-items:flex-start;gap:12px;background:#fffbeb;border:1px solid #fde68a;border-radius:10px;padding:14px 16px;margin-bottom:20px;font-size:13px;color:#92400e;">
                <i class="fas fa-exclamation-circle" style="font-size:16px;flex-shrink:0;margin-top:1px;"></i>
                <div>
                    <strong>Status Revisi:</strong> RPS Anda dikembalikan untuk revisi. Silakan perbaiki sesuai masukan dan
                    submit kembali.
                </div>
            </div>
        @endif

        <!-- MAIN FORM -->
        <form action="{{ route('banksoal.rps.dosen.update', $rps->id) }}" method="POST" enctype="multipart/form-data"
            id="rpsSubmitForm" data-route-cpl="{{ route('banksoal.rps.dosen.cpl') }}"
            data-route-dosen="{{ route('banksoal.rps.dosen.dosen') }}"
            data-selected-dosen-ids="{{ json_encode($selectedDosenIds) }}"
            data-selected-cpl-ids="{{ json_encode($rps->cpls->pluck('id')->toArray()) }}"
            data-generate-detail="{{ json_encode($generateDetail ? $generateDetail->toArray() : null) }}"
            data-cpmk-row-builder="1" data-edit-mode="1" data-rps-id="{{ $rps->id }}">
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
                            {{ $rps->mataKuliah->kode }} – {{ $rps->mataKuliah->nama }} ({{ $rps->mataKuliah->sks }}
                            SKS)
                        </div>
                        <input type="hidden" id="mkSelect" name="mata_kuliah_id" value="{{ $rps->mk_id }}">
                        <p class="field-hint">Mata kuliah tidak dapat diubah pada saat revisi.</p>
                    </div>

                    <div class="form-group">
                        <label class="field-label">Dosen Pengampu Lain</label>
                        <select name="dosen_lain[]" id="dosenSelect" class="field-control" multiple
                            data-selected-dosen-ids='{{ json_encode($selectedDosenIds) }}'></select>
                        <p class="field-hint">Pilih satu atau lebih dosen pengampu tambahan.</p>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label class="field-label">Semester <span class="req">*</span></label>
                        <select id="semester"
                            class="field-control bg-slate-100 border-slate-200 text-slate-500 cursor-not-allowed"
                            disabled>
                            <option value="Ganjil" {{ $rps->semester == 'Ganjil' ? 'selected' : '' }}>Ganjil</option>
                            <option value="Genap" {{ $rps->semester == 'Genap' ? 'selected' : '' }}>Genap</option>
                        </select>
                        <input type="hidden" name="semester" value="{{ $rps->semester }}">
                    </div>
                    <div class="form-group">
                        <label class="field-label">Tahun Ajaran <span class="req">*</span></label>
                        <select id="tahun_ajaran"
                            class="field-control bg-slate-100 border-slate-200 text-slate-500 cursor-not-allowed"
                            disabled>
                            <option value="{{ $rps->tahun_ajaran }}" selected>{{ $rps->tahun_ajaran }}</option>
                        </select>
                        <input type="hidden" name="tahun_ajaran" value="{{ $rps->tahun_ajaran }}">
                    </div>
                </div>
            </div>

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
            @endphp

            <!-- ==================== METHOD A: UPLOAD PDF ==================== -->
            <div id="method_upload_panel" class="{{ $creationMethod === 'upload' ? '' : 'hidden' }}">
                {{-- CPMK Section --}}
                <div class="form-card">
                    <div class="form-card-title"><i class="fas fa-list-check"></i> Capaian Pembelajaran Mata Kuliah
                        (CPMK)
                    </div>
                    <div class="cpmk-section">
                        <div class="cpmk-header">
                            <div>
                                <p style="margin: 0; font-size: 13px; color: var(--slate-600);">Baris CPMK yang diajukan
                                    dalam RPS ini.</p>
                            </div>
                            <button type="button" class="btn btn-secondary" id="addCpmkRowBtn"
                                style="padding:8px 16px;font-size:13px;" {{ !$isUploadOpen ? 'disabled' : '' }}>
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
                            @foreach($existingCpmkRows as $index => $row)
                                <div class="cpmk-row" data-cpmk-row data-row-index="{{ $index }}">
                                    <div>
                                        <select name="cpmk_rows[{{ $index }}][cpl_id]"
                                            class="field-control cpl-select-input" data-cpmk-cpl-select
                                            style="font-size:13px;" data-selected-value="{{ $row['cpl_id'] ?? '' }}"
                                            required {{ !$isUploadOpen ? 'disabled' : '' }}>
                                            <option value="">Pilih CPL</option>
                                        </select>
                                    </div>
                                    <div>
                                        <div style="display:flex;align-items:center;gap:6px;">
                                            <span
                                                style="font-size:12px;font-weight:600;color:var(--slate-500);padding:10px 8px;background:var(--slate-50);border:1px solid var(--slate-200);border-radius:8px;">CPMK</span>
                                            <input type="text" name="cpmk_rows[{{ $index }}][kode]"
                                                value="{{ $row['kode'] ?? '' }}" class="field-control"
                                                style="font-size:13px;" placeholder="1" required {{ !$isUploadOpen ? 'disabled' : '' }}>
                                        </div>
                                    </div>
                                    <div>
                                        <select name="cpmk_rows[{{ $index }}][kko]" class="field-control kko-select-input"
                                            style="font-size:13px;" required {{ !$isUploadOpen ? 'disabled' : '' }}>
                                            <option value="">Pilih KKO</option>
                                            @foreach($kkoOptions as $val => $lbl)
                                                <option value="{{ $val }}" {{ ($row['kko'] ?? '') === $val ? 'selected' : '' }}>
                                                    {{ $val }} – {{ $lbl }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div>
                                        <input type="text" name="cpmk_rows[{{ $index }}][objek]"
                                            value="{{ $row['objek'] ?? '' }}" class="field-control objek-input"
                                            style="font-size:13px;" placeholder="merancang sistem IoT" required {{ !$isUploadOpen ? 'disabled' : '' }}>
                                    </div>
                                    <div>
                                        <input type="text" name="cpmk_rows[{{ $index }}][konteks]"
                                            value="{{ $row['konteks'] ?? '' }}" class="field-control"
                                            style="font-size:13px;" placeholder="sesuai kebutuhan" {{ !$isUploadOpen ? 'disabled' : '' }}>
                                    </div>
                                    <div style="display:flex;align-items:flex-end;">
                                        <button type="button" class="btn-danger-sm" data-remove-cpmk-row
                                            aria-label="Hapus baris CPMK" title="Hapus baris CPMK" {{ !$isUploadOpen ? 'disabled' : '' }}>
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>

                <div class="form-card">
                    <div class="form-card-title"><i class="fas fa-pen-to-square"></i> Catatan Revisi</div>
                    <div class="form-group">
                        <label class="field-label">Deskripsi Revisi
                            {!! $rps->status->value === 'revisi' ? '<span class="req">*</span>' : '' !!}</label>
                        <textarea name="catatan" class="field-control" rows="3"
                            placeholder="Jelaskan bagian RPS yang diubah..." {{ !$isUploadOpen ? 'disabled' : '' }} {{ $rps->status->value === 'revisi' ? 'required' : '' }}>{{ old('catatan', $rps->catatan ?? '') }}</textarea>
                    </div>
                </div>

                <div class="form-card">
                    <div class="form-card-title"><i class="fas fa-file-pdf"></i> Unggah Dokumen RPS</div>
                    <x-banksoal::ui.upload-zone name="dokumen" inputId="fileInput" accept=".pdf"
                        maxLabel="PDF (Maks. 5MB)" :disabled="!$isUploadOpen" :required="!$isUploadOpen || $rps->status->value === 'revisi'" />
                    @error('dokumen')
                    <p class="field-error" style="margin-top:8px;">{{ $message }}</p>@enderror
                </div>

                <div class="form-actions"
                    style="display: flex; justify-content: flex-end; gap: 12px; margin-top: 24px;">
                    <a href="{{ route('banksoal.rps.dosen.index') }}" class="btn btn-secondary">Batal</a>
                    <button type="submit" class="btn btn-primary" id="btnSubmitManual" {{ !$isUploadOpen ? 'disabled' : '' }}>Simpan Perubahan</button>
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
                        <div class="form-card-title"><i class="fas fa-bullseye"></i> Capaian Pembelajaran Lulusan (CPL)
                        </div>
                        <div class="form-group-full">
                            <label class="field-label">Pilih CPL yang Terkait <span class="req">*</span></label>
                            <div id="cpl_checkbox_container"
                                style="display:flex; flex-direction:column; gap:10px; padding: 15px; border: 1px solid var(--slate-200); border-radius: 9px; max-height: 250px; overflow-y: auto;">
                                <p class="field-hint">Memuat daftar CPL...</p>
                            </div>
                        </div>
                    </div>

                    <div class="form-card">
                        <div class="form-card-title" style="margin-bottom:16px;"><i class="fas fa-list-check"></i>
                            Capaian
                            Pembelajaran Mata Kuliah (CPMK)</div>
                        <div class="cpmk-section">
                            <div class="cpmk-header">
                                <div>
                                    <p style="margin: 0; font-size: 13px; color: var(--slate-600);">Tentukan CPMK. CPMK
                                        minimal 1 baris.</p>
                                </div>
                                <button type="button" class="btn btn-secondary" id="addCpmkRowBtnGenerator"
                                    style="padding:8px 16px;font-size:13px;" {{ !$isUploadOpen ? 'disabled' : '' }}>
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

                            <div id="cpmkRowsGenerator" class="cpmk-rows" data-cpmk-rows>
                                @foreach($existingCpmkRows as $index => $row)
                                    <div class="cpmk-row" data-cpmk-row data-row-index="{{ $index }}">
                                        <div>
                                            <select name="cpmk_rows[{{ $index }}][cpl_id]"
                                                class="field-control cpl-select-input" data-cpmk-cpl-select
                                                style="font-size:13px;" data-selected-value="{{ $row['cpl_id'] ?? '' }}"
                                                required {{ !$isUploadOpen ? 'disabled' : '' }}>
                                                <option value="">Pilih CPL</option>
                                            </select>
                                        </div>
                                        <div>
                                            <div style="display:flex;align-items:center;gap:6px;">
                                                <span
                                                    style="font-size:12px;font-weight:600;color:var(--slate-500);padding:10px 8px;background:var(--slate-50);border:1px solid var(--slate-200);border-radius:8px;">CPMK</span>
                                                <input type="text" name="cpmk_rows[{{ $index }}][kode]"
                                                    value="{{ $row['kode'] ?? '' }}" class="field-control"
                                                    style="font-size:13px;" placeholder="1" required {{ !$isUploadOpen ? 'disabled' : '' }}>
                                            </div>
                                        </div>
                                        <div>
                                            <select name="cpmk_rows[{{ $index }}][kko]"
                                                class="field-control kko-select-input" style="font-size:13px;" required {{ !$isUploadOpen ? 'disabled' : '' }}>
                                                <option value="">Pilih KKO</option>
                                                @foreach($kkoOptions as $val => $lbl)
                                                    <option value="{{ $val }}" {{ ($row['kko'] ?? '') === $val ? 'selected' : '' }}>
                                                        {{ $val }} – {{ $lbl }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div>
                                            <input type="text" name="cpmk_rows[{{ $index }}][objek]"
                                                value="{{ $row['objek'] ?? '' }}" class="field-control objek-input"
                                                style="font-size:13px;" placeholder="merancang sistem IoT" required {{ !$isUploadOpen ? 'disabled' : '' }}>
                                        </div>
                                        <div>
                                            <input type="text" name="cpmk_rows[{{ $index }}][konteks]"
                                                value="{{ $row['konteks'] ?? '' }}" class="field-control"
                                                style="font-size:13px;" placeholder="sesuai kebutuhan" {{ !$isUploadOpen ? 'disabled' : '' }}>
                                        </div>
                                        <div style="display:flex;align-items:flex-end;">
                                            <button type="button" class="btn-danger-sm" data-remove-cpmk-row
                                                aria-label="Hapus baris CPMK" title="Hapus baris CPMK" {{ !$isUploadOpen ? 'disabled' : '' }}>
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>

                    <div class="form-card">
                        <div class="form-card-title"><i class="fas fa-align-left"></i> Deskripsi Singkat Mata Kuliah
                        </div>
                        <div class="form-group">
                            <label class="field-label">Deskripsi Mata Kuliah <span class="req">*</span></label>
                            <textarea name="deskripsi_mk" id="deskripsi_mk" class="field-control" rows="5"
                                placeholder="Tuliskan deskripsi singkat mengenai materi, fokus kajian, dan tujuan pembelajaran mata kuliah ini..."
                                {{ !$isUploadOpen ? 'disabled' : '' }}>{{ $generateDetail?->deskripsi_mk ?? '' }}</textarea>
                        </div>
                    </div>

                    <div style="display: flex; justify-content: flex-end; gap: 12px;">
                        <a href="{{ route('banksoal.rps.dosen.index') }}" class="btn btn-secondary">Batal</a>
                        <button type="button" class="btn btn-primary btn-next-step" data-next="2">Next</button>
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
                                            placeholder="Aktivitas Kelas / Diskusi" required {{ !$isUploadOpen ? 'disabled' : '' }}>{{ $penilaian[0]['komponen'] ?? '' }}</textarea></td>
                                    <td><input type="number" name="penilaian_data[0][bobot]"
                                            class="field-control text-center weight-input" min="0" max="100"
                                            value="{{ $penilaian[0]['bobot'] ?? 0 }}" required {{ !$isUploadOpen ? 'disabled' : '' }}></td>
                                    <td><select name="penilaian_data[0][cpmk][]"
                                            class="field-control cpmk-target-select" multiple required
                                            data-selected-values="{{ json_encode($penilaian[0]['cpmk'] ?? []) }}" {{ !$isUploadOpen ? 'disabled' : '' }}></select></td>
                                </tr>
                                <tr>
                                    <td class="text-center">2</td>
                                    <td class="font-bold">Hasil Proyek<input type="hidden"
                                            name="penilaian_data[1][poin]" value="Hasil Proyek"></td>
                                    <td><textarea name="penilaian_data[1][komponen]" class="field-control" rows="2"
                                            placeholder="Hasil Proyek Desain" required {{ !$isUploadOpen ? 'disabled' : '' }}>{{ $penilaian[1]['komponen'] ?? '' }}</textarea></td>
                                    <td><input type="number" name="penilaian_data[1][bobot]"
                                            class="field-control text-center weight-input" min="0" max="100"
                                            value="{{ $penilaian[1]['bobot'] ?? 0 }}" required {{ !$isUploadOpen ? 'disabled' : '' }}></td>
                                    <td><select name="penilaian_data[1][cpmk][]"
                                            class="field-control cpmk-target-select" multiple required
                                            data-selected-values="{{ json_encode($penilaian[1]['cpmk'] ?? []) }}" {{ !$isUploadOpen ? 'disabled' : '' }}></select></td>
                                </tr>
                                <tr>
                                    <td class="text-center">3</td>
                                    <td class="font-bold">Tugas<input type="hidden" name="penilaian_data[2][poin]"
                                            value="Tugas"></td>
                                    <td><textarea name="penilaian_data[2][komponen]" class="field-control" rows="2"
                                            placeholder="Tugas Kelompok" required {{ !$isUploadOpen ? 'disabled' : '' }}>{{ $penilaian[2]['komponen'] ?? '' }}</textarea></td>
                                    <td><input type="number" name="penilaian_data[2][bobot]"
                                            class="field-control text-center weight-input" min="0" max="100"
                                            value="{{ $penilaian[2]['bobot'] ?? 0 }}" required {{ !$isUploadOpen ? 'disabled' : '' }}></td>
                                    <td><select name="penilaian_data[2][cpmk][]"
                                            class="field-control cpmk-target-select" multiple required
                                            data-selected-values="{{ json_encode($penilaian[2]['cpmk'] ?? []) }}" {{ !$isUploadOpen ? 'disabled' : '' }}></select></td>
                                </tr>
                                <tr>
                                    <td class="text-center">4</td>
                                    <td class="font-bold">Quiz<input type="hidden" name="penilaian_data[3][poin]"
                                            value="Quiz"></td>
                                    <td><textarea name="penilaian_data[3][komponen]" class="field-control" rows="2"
                                            placeholder="Tugas Individu / Kuis" required {{ !$isUploadOpen ? 'disabled' : '' }}>{{ $penilaian[3]['komponen'] ?? '' }}</textarea></td>
                                    <td><input type="number" name="penilaian_data[3][bobot]"
                                            class="field-control text-center weight-input" min="0" max="100"
                                            value="{{ $penilaian[3]['bobot'] ?? 0 }}" required {{ !$isUploadOpen ? 'disabled' : '' }}></td>
                                    <td><select name="penilaian_data[3][cpmk][]"
                                            class="field-control cpmk-target-select" multiple required
                                            data-selected-values="{{ json_encode($penilaian[3]['cpmk'] ?? []) }}" {{ !$isUploadOpen ? 'disabled' : '' }}></select></td>
                                </tr>
                                <!-- Kognitif has UTS and UAS -->
                                <tr>
                                    <td class="text-center" rowspan="2">5</td>
                                    <td class="font-bold" rowspan="2">Kognitif / Pengetahuan<input type="hidden"
                                            name="penilaian_data[4][poin]" value="Kognitif / Pengetahuan"></td>
                                    <td>UTS (Ujian Tengah Semester)<input type="hidden"
                                            name="penilaian_data[4][sub_rows][0][komponen]" value="UTS"></td>
                                    <td><input type="number" name="penilaian_data[4][sub_rows][0][bobot]"
                                            class="field-control text-center weight-input" min="0" max="100"
                                            value="{{ $penilaian[4]['sub_rows'][0]['bobot'] ?? 0 }}" required {{ !$isUploadOpen ? 'disabled' : '' }}></td>
                                    <td><select name="penilaian_data[4][sub_rows][0][cpmk][]"
                                            class="field-control cpmk-target-select" multiple required
                                            data-selected-values="{{ json_encode($penilaian[4]['sub_rows'][0]['cpmk'] ?? []) }}"
                                            {{ !$isUploadOpen ? 'disabled' : '' }}></select></td>
                                </tr>
                                <tr>
                                    <td>UAS (Ujian Akhir Semester)<input type="hidden"
                                            name="penilaian_data[4][sub_rows][1][komponen]" value="UAS"></td>
                                    <td><input type="number" name="penilaian_data[4][sub_rows][1][bobot]"
                                            class="field-control text-center weight-input" min="0" max="100"
                                            value="{{ $penilaian[4]['sub_rows'][1]['bobot'] ?? 0 }}" required {{ !$isUploadOpen ? 'disabled' : '' }}></td>
                                    <td><select name="penilaian_data[4][sub_rows][1][cpmk][]"
                                            class="field-control cpmk-target-select" multiple required
                                            data-selected-values="{{ json_encode($penilaian[4]['sub_rows'][1]['cpmk'] ?? []) }}"
                                            {{ !$isUploadOpen ? 'disabled' : '' }}></select></td>
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
                        <button type="button" class="btn btn-secondary btn-prev-step" data-prev="1">Back</button>
                        <button type="button" class="btn btn-primary btn-next-step" data-next="3">Next</button>
                    </div>
                </div>

                <!-- STEP 3: PERTEMUAN & REFERENSI -->
                <div class="wizard-content-section hidden" id="step_3_panel">

                    <!-- Navigator kartu pertemuan (sama dengan halaman pembuatan RPS) -->
                    <div class="form-card" style="padding: 18px 20px 14px;">
                        <div
                            style="display:flex; align-items:center; justify-content:space-between; margin-bottom:12px;">
                            <div class="form-card-title" style="margin:0; padding:0; border:none;">
                                <i class="fas fa-calendar-alt"></i> Rencana Pembelajaran per Pertemuan
                            </div>
                            <span id="pertemuanProgress"
                                style="font-size:12px; font-weight:600; color:var(--c-fg-sec,#475569);">0 / 14
                                terisi</span>
                        </div>

                        <!-- Grid nomor pertemuan -->
                        <div id="pertemuanNav"
                            style="display:grid; grid-template-columns:repeat(8,1fr); gap:6px; margin-bottom:16px;">
                            @for($p = 1; $p <= 16; $p++)
                                @if($p === 8)
                                    <div data-nav-p="8" style="grid-column:span 1;">
                                        <div
                                            style="height:36px; border-radius:8px; background:#f1f5f9; border:1px solid #DFE1E7; display:flex; align-items:center; justify-content:center; font-size:10px; font-weight:700; color:#94A3B8; letter-spacing:.03em;">
                                            UTS
                                        </div>
                                    </div>
                                @elseif($p === 16)
                                    <div data-nav-p="16" style="grid-column:span 1;">
                                        <div
                                            style="height:36px; border-radius:8px; background:#f1f5f9; border:1px solid #DFE1E7; display:flex; align-items:center; justify-content:center; font-size:10px; font-weight:700; color:#94A3B8; letter-spacing:.03em;">
                                            UAS
                                        </div>
                                    </div>
                                @else
                                    <button type="button" data-nav-p="{{ $p }}"
                                        onclick="goToPertemuan({{ $p }})" {{ !$isUploadOpen ? 'disabled' : '' }}
                                        style="height:36px; border-radius:8px; border:1px solid #DFE1E7; background:#fff; font-size:13px; font-weight:600; color:#64748b; cursor:pointer; transition:all .15s; position:relative;">
                                        {{ $p < 8 ? $p : $p - 1 }}
                                        <span data-filled-{{ $p }}
                                            style="display:none; position:absolute; top:4px; right:4px; width:6px; height:6px; border-radius:50%; background:#0B266E;"></span>
                                    </button>
                                @endif
                            @endfor
                        </div>

                        <!-- Panel form satu pertemuan -->
                        <div id="pertemuanFormPanel"
                            style="border:1px solid #DFE1E7; border-radius:12px; overflow:hidden;">
                            <div
                                style="display:flex; align-items:center; justify-content:space-between; padding:12px 16px; background:#FAFAFA; border-bottom:1px solid #DFE1E7;">
                                <span id="pertemuanTitle"
                                    style="font-size:14px; font-weight:700; color:#1e293b;">Pertemuan 1</span>
                            </div>
                            <div style="padding:16px 18px;" id="pertemuanFields">
                                <!-- Fields dirender via JS -->
                            </div>
                        </div>

                        <!-- Navigasi prev/next pertemuan -->
                        <div style="display:flex; align-items:center; justify-content:space-between; margin-top:14px;">
                            <button type="button" id="btnPrevPertemuan" onclick="prevPertemuan()"
                                {{ !$isUploadOpen ? 'disabled' : '' }}
                                style="height:34px; padding:0 16px; border:1px solid #DFE1E7; border-radius:8px; background:#fff; font-size:13px; font-weight:600; color:#475569; cursor:pointer; display:inline-flex; align-items:center; gap:6px; font-family:inherit; transition:all .15s;">
                                Sebelumnya
                            </button>
                            <button type="button" id="btnNextPertemuan" onclick="nextPertemuan()"
                                {{ !$isUploadOpen ? 'disabled' : '' }}
                                style="height:34px; padding:0 16px; border:1px solid #0B266E; border-radius:8px; background:#0B266E; color:#fff; font-size:13px; font-weight:600; cursor:pointer; display:inline-flex; align-items:center; gap:6px; font-family:inherit; transition:all .15s;">
                                Selanjutnya
                            </button>
                        </div>
                    </div>

                    <!-- Hidden inputs untuk UTS dan UAS (auto-populated) -->
                    <input type="hidden" name="pertemuan_data[8][pertemuan]" value="8">
                    <input type="hidden" name="pertemuan_data[8][kemampuan_akhir]" value="Evaluation">
                    <input type="hidden" name="pertemuan_data[8][pokok_bahasan]" value="-">
                    <input type="hidden" name="pertemuan_data[8][metode]" value="-">
                    <input type="hidden" name="pertemuan_data[8][waktu]" value="0">
                    <input type="hidden" name="pertemuan_data[8][pengalaman_belajar]" value="-">
                    <input type="hidden" name="pertemuan_data[8][target_cpmk]" value="-">
                    <input type="hidden" name="pertemuan_data[8][kriteria_penilaian]" value="-">
                    <input type="hidden" name="pertemuan_data[8][bobot]" value="0">
                    <input type="hidden" name="pertemuan_data[16][pertemuan]" value="16">
                    <input type="hidden" name="pertemuan_data[16][kemampuan_akhir]" value="Evaluation">
                    <input type="hidden" name="pertemuan_data[16][pokok_bahasan]" value="-">
                    <input type="hidden" name="pertemuan_data[16][metode]" value="-">
                    <input type="hidden" name="pertemuan_data[16][waktu]" value="0">
                    <input type="hidden" name="pertemuan_data[16][pengalaman_belajar]" value="-">
                    <input type="hidden" name="pertemuan_data[16][target_cpmk]" value="-">
                    <input type="hidden" name="pertemuan_data[16][kriteria_penilaian]" value="-">
                    <input type="hidden" name="pertemuan_data[16][bobot]" value="0">

                    <!-- Referensi -->
                    <div class="form-card">
                        <div class="form-card-title"><i class="fas fa-book"></i> Daftar Referensi</div>
                        <p style="font-size: 13px; color: var(--slate-600); margin-bottom: 12px;">Tambahkan satu
                            atau
                            lebih buku/jurnal referensi utama dan pendukung.</p>
                        <div id="references_container">
                            <div class="ref-row">
                                <input type="text" name="referensi_data[]" class="field-control"
                                    placeholder="Contoh: Pressman, R.S. (2015). Software Engineering: A Practitioner's Approach. McGraw-Hill."
                                    required {{ !$isUploadOpen ? 'disabled' : '' }}>
                                <button type="button" class="btn-danger-sm btn-remove-ref"
                                    style="flex-shrink: 0;" title="Hapus referensi" aria-label="Hapus referensi"
                                    {{ !$isUploadOpen ? 'disabled' : '' }}>
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </div>
                        <button type="button" class="btn btn-secondary" id="btnAddReference"
                            style="margin-top:12px; padding:8px 16px; font-size:13px;" {{ !$isUploadOpen ? 'disabled' : '' }}>
                            Tambah Referensi
                        </button>
                    </div>

                    <div class="form-card">
                        <div class="form-card-title"><i class="fas fa-comment"></i> Catatan Revisi</div>
                        <div class="form-group">
                            <textarea name="catatan" id="catatan_generator" class="field-control" rows="3"
                                placeholder="Opsional. Tambahkan catatan revisi..." {{ !$isUploadOpen ? 'disabled' : '' }} {{ $rps->status->value === 'revisi' ? 'required' : '' }}>{{ old('catatan', $rps->catatan ?? '') }}</textarea>
                        </div>
                    </div>

                    <div style="display: flex; justify-content: space-between; gap: 12px; margin-top: 16px;">
                        <button type="button" class="btn btn-secondary btn-prev-step" data-prev="2">Back</button>
                        <button type="submit" class="btn btn-primary" id="btnSubmitGenerator" {{ !$isUploadOpen ? 'disabled' : '' }}>Simpan & Generate PDF</button>
                    </div>
                </div>
            </div>
        </form>

        <template id="cpmkRowTemplate">
            <div class="cpmk-row" data-cpmk-row data-row-index="__INDEX__">
                <div>
                    <select name="cpmk_rows[__INDEX__][cpl_id]" class="field-control cpl-select-input"
                        data-cpmk-cpl-select style="font-size:13px;" required>
                        <option value="">Pilih CPL</option>
                    </select>
                </div>
                <div>
                    <div style="display:flex;align-items:center;gap:6px;">
                        <span
                            style="font-size:12px;font-weight:600;color:var(--slate-500);padding:10px 8px;background:var(--slate-50);border:1px solid var(--slate-200);border-radius:8px;">CPMK</span>
                        <input type="text" name="cpmk_rows[__INDEX__][kode]" class="field-control"
                            style="font-size:13px;" placeholder="1" required>
                    </div>
                </div>
                <div>
                    <select name="cpmk_rows[__INDEX__][kko]" class="field-control kko-select-input"
                        style="font-size:13px;" required>
                        <option value="">Pilih KKO</option>
                        @foreach($kkoOptions as $val => $lbl)
                            <option value="{{ $val }}">{{ $val }} – {{ $lbl }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <input type="text" name="cpmk_rows[__INDEX__][objek]" class="field-control objek-input"
                        style="font-size:13px;" placeholder="merancang sistem IoT" required>
                </div>
                <div>
                    <input type="text" name="cpmk_rows[__INDEX__][konteks]" class="field-control"
                        style="font-size:13px;" placeholder="sesuai kebutuhan">
                </div>
                <div style="display:flex;align-items:flex-end;">
                    <button type="button" class="btn-danger-sm" data-remove-cpmk-row aria-label="Hapus baris CPMK"
                        title="Hapus baris CPMK">
                        <i class="fas fa-trash"></i>
                    </button>
                </div>
            </div>
        </template>

    </x-banksoal::ui.bank-soal-page>

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function () {
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

                /**
                 * Baca payload JSON dari data-attribute secara aman.
                 * Payload rusak tidak boleh menghentikan seluruh skrip halaman.
                 */
                function parseDataAttr(value, fallback = null) {
                    if (!value) return fallback;

                    try {
                        return JSON.parse(value);
                    } catch (error) {
                        console.error('Payload JSON pada data-attribute tidak valid:', error);
                        return fallback;
                    }
                }

                // Failsafe: error tak terduga jangan meninggalkan overlay loader tertutup layar.
                window.addEventListener('error', () => {
                    if (window.hideLoader) window.hideLoader();
                });

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

                    if (stepNum === 2 || stepNum === 3) {
                        populateCpmkDropdowns();
                    }
                    if (stepNum === 3) {
                        // Re-render pertemuan rows each time step 3 is entered
                        // so CPMK target selects reflect the latest CPMK rows
                        initPrefilledGeneratorData();
                    }
                }

                function setCreationMethod(method) {
                    creationMethodInput.value = method;

                    if (method === 'upload') {
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

                // Helper to attach toggle deselect hook & "{count} Terpilih" badge on TomSelect instance
                function attachTomSelectToggleDeselect(ts) {
                    if (!ts) return;

                    if (ts.wrapper) {
                        ts.wrapper.classList.add('has-summary-badge');
                    }

                    if (!ts._hasToggleHook) {
                        ts._hasToggleHook = true;
                        const orig_onOptionSelect = ts.onOptionSelect;
                        ts.onOptionSelect = function (evt, option) {
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
                                if (evt && evt.preventDefault) evt.preventDefault();
                                return;
                            }
                            orig_onOptionSelect.call(ts, evt, option);
                        };
                    }

                    const updateSummaryBadge = () => {
                        if (!ts) return;
                        const count = ts.items ? ts.items.length : 0;
                        const control = ts.control;
                        if (!control) return;

                        control.style.display = 'flex';
                        control.style.alignItems = 'center';
                        control.style.height = '38px';
                        control.style.minHeight = '38px';
                        control.style.maxHeight = '38px';
                        control.style.boxSizing = 'border-box';
                        control.style.padding = '0 8px';

                        let badge = control.querySelector('.ts-summary-badge');
                        if (!badge) {
                            badge = document.createElement('div');
                            badge.className = 'ts-summary-badge';
                            badge.innerHTML = `<span class="ts-summary-text"></span><span class="ts-summary-clear" title="Hapus semua pilihan">×</span>`;
                            const clearBtn = badge.querySelector('.ts-summary-clear');
                            if (clearBtn) {
                                clearBtn.addEventListener('click', (e) => {
                                    e.preventDefault();
                                    e.stopPropagation();
                                    ts.clear();
                                    ts.refreshOptions(false);
                                    updateSummaryBadge();
                                });
                            }
                            control.insertBefore(badge, ts.control_input || control.firstChild);
                        }

                        if (count > 0) {
                            badge.style.display = 'inline-flex';
                            badge.querySelector('.ts-summary-text').textContent = `${count} Terpilih`;
                            if (ts.control_input) {
                                ts.control_input.placeholder = '';
                                ts.control_input.style.width = '0px';
                                ts.control_input.style.minWidth = '0px';
                                ts.control_input.style.opacity = '0';
                            }
                        } else {
                            badge.style.display = 'none';
                            if (ts.control_input) {
                                ts.control_input.placeholder = ts.settings.placeholder || '';
                                ts.control_input.style.width = '';
                                ts.control_input.style.minWidth = '';
                                ts.control_input.style.opacity = '';
                            }
                        }
                    };

                    if (!ts._hasDisplayEvents) {
                        ts._hasDisplayEvents = true;
                        ts.on('change', () => {
                            updateSummaryBadge();
                            ts.refreshOptions(false);
                        });
                        ts.on('item_add', updateSummaryBadge);
                        ts.on('item_remove', updateSummaryBadge);
                        ts.on('clear', updateSummaryBadge);
                    }

                    updateSummaryBadge();
                }

                async function initDosenSelect(selectedIds = []) {
                    const sel = document.getElementById('dosenSelect');
                    if (!sel) return;

                    try {
                        const res = await fetch("{{ route('banksoal.rps.dosen.dosen') }}");
                        if (!res.ok) throw new Error(`HTTP ${res.status}`);
                        const data = await res.json();

                        if (sel.tomselect) {
                            sel.tomselect.destroy();
                        }

                        sel.innerHTML = '<option value="">Pilih dosen pengampu lain</option>';
                        data.forEach(dosen => {
                            const opt = document.createElement('option');
                            opt.value = String(dosen.id);
                            opt.textContent = dosen.name;
                            if (selectedIds.map(String).includes(String(dosen.id))) {
                                opt.selected = true;
                            }
                            sel.appendChild(opt);
                        });

                        if (typeof TomSelect !== 'undefined') {
                            const ts = new TomSelect(sel, {
                                maxOptions: 100,
                                searchField: ["text"],
                                persist: false,
                                hideSelected: false,
                                placeholder: 'Pilih dosen pengampu lain...',
                                dropdownParent: 'body',
                                render: {
                                    item: function (data, escape) {
                                        return `<div class="item" data-value="${escape(data.value)}" style="display:none !important;"></div>`;
                                    },
                                    option: function (data, escape) {
                                        return `<div class="option py-2 px-3" data-value="${escape(data.value)}">${escape(data.text)}</div>`;
                                    }
                                }
                            });
                            attachTomSelectToggleDeselect(ts);
                            if (selectedIds.length > 0) {
                                ts.setValue(selectedIds.map(String), true);
                            }
                        }
                    } catch (e) {
                        console.warn('Gagal memuat dosen pengampu lain:', e);
                    }
                }
                window.initDosenSelect = initDosenSelect;

                // Initial setup based on creation method
                setCreationMethod(creationMethodInput ? creationMethodInput.value : 'upload');
                try {
                    const initialDosenIds = parseDataAttr(rpsSubmitForm.dataset.selectedDosenIds, []);
                    initDosenSelect(initialDosenIds);
                } catch (e) {
                    initDosenSelect();
                }


                // Next / Prev triggers
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

                        // Validasi duplikasi CPMK khusus untuk Step 1
                        if (currentPanel && currentPanel.id === 'step_1_panel') {
                            const genContainer = document.getElementById('cpmkRowsGenerator');
                            const isCpmkValid = window.validateCpmkDuplicates ? window.validateCpmkDuplicates(genContainer) : true;
                            if (!isCpmkValid) {
                                if (window.Swal) {
                                    Swal.fire({
                                        icon: 'error',
                                        title: 'Kombinasi CPMK Duplikat',
                                        text: 'Terdapat kombinasi CPL dan Kode CPMK yang sama. Mohon bedakan kode CPMK untuk setiap baris CPL yang sama.',
                                        confirmButtonColor: '#0B266E'
                                    });
                                } else {
                                    alert('Terdapat kombinasi CPL dan Kode CPMK yang sama. Mohon bedakan kode CPMK untuk setiap baris CPL yang sama.');
                                }
                                const firstDup = genContainer?.querySelector('.cpmk-duplicate-warning, input.is-invalid');
                                if (firstDup) {
                                    firstDup.scrollIntoView({ behavior: 'smooth', block: 'center' });
                                }
                                return;
                            }
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
                            const match = strVal.match(/cpl[-_\s]*0*(\d+)\.(\d+)/i) || strVal.match(/[-_\s]*0*(\d+)\.(\d+)/) || strVal.match(/CPMK\s*(\d+)\.(\d+)/i);
                            if (match) {
                                return `${parseInt(match[1], 10)}.${match[2]}`;
                            }
                            return strVal;
                        });

                        if (typeof TomSelect !== 'undefined' && select.tomselect) {
                            const ts = select.tomselect;
                            ts.clear();
                            ts.clearOptions();
                            list.forEach(item => {
                                ts.addOption({ value: item.value, text: item.label });
                            });
                            currentVals.forEach(val => {
                                ts.addItem(val);
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
                                    maxOptions: 100,
                                    persist: false,
                                    create: false,
                                    hideSelected: false,
                                    placeholder: 'Pilih CPMK...',
                                    dropdownParent: 'body',
                                    render: {
                                        item: function (data, escape) {
                                            return `<div class="item" data-value="${escape(data.value)}" style="display:none !important;"></div>`;
                                        },
                                        option: function (data, escape) {
                                            return `<div class="option py-2 px-3" data-value="${escape(data.value)}">${escape(data.text)}</div>`;
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
                            <button type="button" class="btn-danger-sm btn-remove-ref" style="flex-shrink: 0;" title="Hapus referensi" aria-label="Hapus referensi">
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

                // CPL & Dosen loader
                const mkSelect = document.getElementById('mkSelect');
                const cplCheckboxContainer = document.getElementById('cpl_checkbox_container');

                function loadCpls(mkId) {
                    if (!mkId) return;

                    if (window.showLoader) window.showLoader();
                    cplCheckboxContainer.innerHTML = '<p class="field-hint"><i class="fas fa-spinner fa-spin"></i> Memuat CPL</p>';
                    const cplPromise = fetch(`{{ route('banksoal.rps.dosen.cpl', '') }}/${mkId}`)
                        .then(res => res.json())
                        .then(data => {
                            cplCheckboxContainer.innerHTML = '';
                            if (data.length === 0) {
                                cplCheckboxContainer.innerHTML = '<p class="field-hint text-rose-500">Mata kuliah ini belum dipetakan ke CPL manapun.</p>';
                                return;
                            }
                            const selectedCplIds = parseDataAttr(rpsSubmitForm.dataset.selectedCplIds, []);
                            data.forEach(cpl => {
                                const isChecked = selectedCplIds.includes(cpl.id);
                                const item = document.createElement('label');
                                item.style.display = 'flex';
                                item.style.alignItems = 'flex-start';
                                item.style.gap = '10px';
                                item.style.fontSize = '13px';
                                item.style.cursor = 'pointer';
                                item.innerHTML = `
                                        <input type="checkbox" class="cpl-checkbox-item" value="${cpl.id}" ${isChecked ? 'checked' : ''} style="margin-top: 3px; accent-color: var(--primary-blue);" data-code="${cpl.kode}">
                                        <div><strong>${cpl.kode}</strong>: ${cpl.deskripsi}</div>
                                    `;
                                cplCheckboxContainer.appendChild(item);
                            });
                            updateCplSelectDropdowns(data);
                            if (window.BanksoalRpsUploadForm) {
                                window.BanksoalRpsUploadForm.cplOptions = data;
                                window.BanksoalRpsUploadForm.renderAllCplSelects();
                            }
                        });

                    // Render data wizard harus tahan gagal: kalau gagal, loader tetap ditutup.
                    let restorePromise = Promise.resolve();
                    try {
                        restorePromise = initPrefilledGeneratorData();
                    } catch (error) {
                        console.error('Gagal menyiapkan data generator RPS:', error);
                    }

                    Promise.all([cplPromise, restorePromise]).finally(() => {
                        if (window.hideLoader) window.hideLoader();
                        calculateTotalWeight();
                        // CPL baru diketahui setelah fetch selesai — segarkan kartu pertemuan
                        // supaya pilihan Target CPMK ikut terisi.
                        if (typeof goToPertemuan === 'function') {
                            goToPertemuan(currentPertemuan || 1);
                        }
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

                // Generator CPMK row builder is now handled by RpsCpmkRows.js

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

                // ─── Navigator kartu pertemuan (sama seperti halaman pembuatan RPS) ───
                let currentPertemuan = 1;
                const PERTEMUAN_REAL = [1, 2, 3, 4, 5, 6, 7, 9, 10, 11, 12, 13, 14, 15]; // skip 8 (UTS) dan 16 (UAS)

                // Data semua pertemuan disimpan di memori supaya tidak hilang saat pindah kartu
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

                /** Normalisasi target_cpmk dari DB ("CPMK 3.1, CPMK 4.1" atau array). */
                function normalizeTargetCpmk(value) {
                    if (Array.isArray(value)) {
                        return value.map(v => String(v).replace(/^CPMK\s*/i, '').trim()).filter(Boolean);
                    }
                    if (typeof value === 'string' && value.trim() !== '' && value.trim() !== '-') {
                        return value.replace(/CPMK\s*/ig, '').split(',').map(s => s.trim()).filter(Boolean);
                    }
                    return [];
                }

                /** Isi pertemuanData dari generateDetail RPS yang tersimpan. */
                function seedPertemuanData(generateDetail) {
                    const raw = generateDetail ? generateDetail.pertemuan_data : null;
                    if (!raw) return;

                    const rows = Array.isArray(raw) ? raw : Object.values(raw);
                    rows.forEach(row => {
                        const p = parseInt(row ? row.pertemuan : NaN, 10);
                        if (!p || p === 8 || p === 16) return;

                        pertemuanData[p] = {
                            pertemuan: String(p),
                            kemampuan_akhir: row.kemampuan_akhir || '',
                            pokok_bahasan: row.pokok_bahasan || '',
                            metode: row.metode || '',
                            waktu: (row.waktu === undefined || row.waktu === null) ? '150' : String(row.waktu),
                            pengalaman_belajar: row.pengalaman_belajar || '',
                            target_cpmk: normalizeTargetCpmk(row.target_cpmk),
                            kriteria_penilaian: row.kriteria_penilaian || '',
                            bobot: (row.bobot === undefined || row.bobot === null) ? '5' : String(row.bobot),
                        };
                    });

                    updateAllNavIndicators();
                    updatePertemuanProgress();
                }

                function buildCpmkListFromRows() {
                    const list = [];
                    const seen = new Set();
                    document.querySelectorAll('#cpmkRowsGenerator [data-cpmk-row], #cpmkRows [data-cpmk-row]').forEach(row => {
                        const cplSel = row.querySelector('.cpl-select-input, [data-cpmk-cpl-select]');
                        const kodeIn = row.querySelector('input[name*="[kode]"]');
                        if (!cplSel || !kodeIn || !cplSel.value || !kodeIn.value.trim()) return;

                        const opt = cplSel.options[cplSel.selectedIndex];
                        const cplText = opt ? opt.textContent.trim() : '';
                        const cplNum = (cplText.match(/\d+/) || [cplText])[0];
                        const val = `${parseInt(cplNum) || cplNum}.${kodeIn.value.trim()}`;
                        if (!seen.has(val)) {
                            seen.add(val);
                            list.push({ value: val, label: `CPMK ${val}` });
                        }
                    });
                    return list;
                }

                function renderPertemuanFields(p) {
                    const fields = document.getElementById('pertemuanFields');
                    if (!fields) return;

                    const data = pertemuanData[p] || {};
                    const cpmkList = buildCpmkListFromRows();
                    const selectedCpmk = normalizeTargetCpmk(data.target_cpmk);
                    const cpmkOptions = cpmkList.map(c =>
                        `<option value="${c.value}" ${selectedCpmk.includes(String(c.value)) ? 'selected' : ''}>${c.label}</option>`
                    ).join('');
                    const noOptionsMsg = cpmkList.length === 0
                        ? '<option value="" disabled>Belum ada CPMK &mdash; isi Step 1 terlebih dahulu</option>'
                        : '';
                    const disabledAttr = @json(!$isUploadOpen) ? 'disabled' : '';

                    fields.innerHTML = `
                                                    <div style="display:grid; grid-template-columns:1fr 1fr; gap:14px; margin-bottom:14px;">
                                                        <div>
                                                            <label style="font-size:11px; font-weight:600; color:var(--c-fg-muted,#94A3B8); text-transform:uppercase; letter-spacing:.06em; display:block; margin-bottom:6px;">Kemampuan Akhir *</label>
                                                            <textarea id="pf_kemampuan_akhir" name="pertemuan_data[${p}][kemampuan_akhir]" rows="3" ${disabledAttr}
                                                                style="width:100%; height:80px; padding:8px 11px; border:1px solid var(--c-border,#DFE1E7); border-radius:8px; font-size:13px; resize:vertical; font-family:inherit; box-sizing:border-box;"
                                                                oninput="savePertemuanField(${p},'kemampuan_akhir',this.value)">${data.kemampuan_akhir || ''}</textarea>
                                                        </div>
                                                        <div>
                                                            <label style="font-size:11px; font-weight:600; color:var(--c-fg-muted,#94A3B8); text-transform:uppercase; letter-spacing:.06em; display:block; margin-bottom:6px;">Bahan Kajian *</label>
                                                            <textarea id="pf_pokok_bahasan" name="pertemuan_data[${p}][pokok_bahasan]" rows="3" ${disabledAttr}
                                                                style="width:100%; height:80px; padding:8px 11px; border:1px solid var(--c-border,#DFE1E7); border-radius:8px; font-size:13px; resize:vertical; font-family:inherit; box-sizing:border-box;"
                                                                oninput="savePertemuanField(${p},'pokok_bahasan',this.value)">${data.pokok_bahasan || ''}</textarea>
                                                        </div>
                                                    </div>
                                                    <div style="display:grid; grid-template-columns:2fr 1fr 2fr; gap:14px; margin-bottom:14px;">
                                                        <div>
                                                            <label style="font-size:11px; font-weight:600; color:var(--c-fg-muted,#94A3B8); text-transform:uppercase; letter-spacing:.06em; display:block; margin-bottom:6px;">Metode *</label>
                                                            <input type="text" id="pf_metode" name="pertemuan_data[${p}][metode]" ${disabledAttr}
                                                                value="${data.metode || ''}"
                                                                style="width:100%; height:36px; padding:0 11px; border:1px solid var(--c-border,#DFE1E7); border-radius:8px; font-size:13px; font-family:inherit; box-sizing:border-box;"
                                                                oninput="savePertemuanField(${p},'metode',this.value)">
                                                        </div>
                                                        <div>
                                                            <label style="font-size:11px; font-weight:600; color:var(--c-fg-muted,#94A3B8); text-transform:uppercase; letter-spacing:.06em; display:block; margin-bottom:6px;">Waktu (menit) *</label>
                                                            <input type="number" id="pf_waktu" name="pertemuan_data[${p}][waktu]" ${disabledAttr}
                                                                value="${data.waktu || 150}"
                                                                style="width:100%; height:36px; padding:0 11px; border:1px solid var(--c-border,#DFE1E7); border-radius:8px; font-size:13px; font-family:inherit; box-sizing:border-box; text-align:center;"
                                                                oninput="savePertemuanField(${p},'waktu',this.value)">
                                                        </div>
                                                        <div>
                                                            <label style="font-size:11px; font-weight:600; color:var(--c-fg-muted,#94A3B8); text-transform:uppercase; letter-spacing:.06em; display:block; margin-bottom:6px;">Bobot (%)</label>
                                                            <input type="number" id="pf_bobot" name="pertemuan_data[${p}][bobot]" ${disabledAttr}
                                                                value="${data.bobot || 5}" min="0" max="100"
                                                                style="width:100%; height:36px; padding:0 11px; border:1px solid var(--c-border,#DFE1E7); border-radius:8px; font-size:13px; font-family:inherit; box-sizing:border-box; text-align:center;"
                                                                oninput="savePertemuanField(${p},'bobot',this.value)">
                                                        </div>
                                                    </div>
                                                    <div style="margin-bottom:14px;">
                                                        <label style="font-size:11px; font-weight:600; color:var(--c-fg-muted,#94A3B8); text-transform:uppercase; letter-spacing:.06em; display:block; margin-bottom:6px;">Pengalaman Belajar *</label>
                                                        <textarea id="pf_pengalaman_belajar" name="pertemuan_data[${p}][pengalaman_belajar]" rows="2" ${disabledAttr}
                                                            style="width:100%; padding:8px 11px; border:1px solid var(--c-border,#DFE1E7); border-radius:8px; font-size:13px; resize:vertical; font-family:inherit; box-sizing:border-box;"
                                                            oninput="savePertemuanField(${p},'pengalaman_belajar',this.value)">${data.pengalaman_belajar || ''}</textarea>
                                                    </div>
                                                    <div style="display:grid; grid-template-columns:1fr 1fr; gap:14px;">
                                                        <div>
                                                            <label style="font-size:11px; font-weight:600; color:var(--c-fg-muted,#94A3B8); text-transform:uppercase; letter-spacing:.06em; display:block; margin-bottom:6px;">Target CPMK *</label>
                                                            <select id="pf_target_cpmk" name="pertemuan_data[${p}][target_cpmk][]" multiple ${disabledAttr}
                                                                style="width:100%; padding:8px 11px; border:1px solid var(--c-border,#DFE1E7); border-radius:8px; font-size:13px; min-height:80px; font-family:inherit; box-sizing:border-box;"
                                                                onchange="savePertemuanField(${p},'target_cpmk', Array.from(this.selectedOptions).map(o=>o.value))">
                                                                ${cpmkOptions || noOptionsMsg}
                                                            </select>
                                                        </div>
                                                        <div>
                                                            <label style="font-size:11px; font-weight:600; color:var(--c-fg-muted,#94A3B8); text-transform:uppercase; letter-spacing:.06em; display:block; margin-bottom:6px;">Kriteria & Indikator *</label>
                                                            <textarea id="pf_kriteria" name="pertemuan_data[${p}][kriteria_penilaian]" rows="3" ${disabledAttr}
                                                                style="width:100%; padding:8px 11px; border:1px solid var(--c-border,#DFE1E7); border-radius:8px; font-size:13px; resize:vertical; font-family:inherit; box-sizing:border-box;"
                                                                oninput="savePertemuanField(${p},'kriteria_penilaian',this.value)">${data.kriteria_penilaian || ''}</textarea>
                                                        </div>
                                                    </div>
                                                    <input type="hidden" name="pertemuan_data[${p}][pertemuan]" value="${p}">
                                                `;

                    if (typeof TomSelect !== 'undefined') {
                        const pfSel = document.getElementById('pf_target_cpmk');
                        if (pfSel && !pfSel.disabled) {
                            if (pfSel.tomselect) pfSel.tomselect.destroy();
                            const pfTs = new TomSelect(pfSel, {
                                maxOptions: 100,
                                persist: false,
                                create: false,
                                hideSelected: false,
                                placeholder: 'Pilih CPMK...',
                                dropdownParent: 'body',
                                render: {
                                    item: function (data, escape) {
                                        return `<div class="item" data-value="${escape(data.value)}" style="display:none !important;"></div>`;
                                    },
                                    option: function (data, escape) {
                                        return `<div class="option py-2 px-3" data-value="${escape(data.value)}">${escape(data.text)}</div>`;
                                    }
                                },
                                onChange: function (values) {
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
                    const dot = document.querySelector(`[data-filled-${p}]`);
                    const btn = document.querySelector(`button[data-nav-p="${p}"]`);
                    if (dot) {
                        dot.style.display = state !== 'empty' ? 'block' : 'none';
                        dot.style.background = state === 'invalid' ? '#ef4444' : '#0B266E';
                    }
                    if (btn) {
                        const isActive = p === currentPertemuan;
                        if (state === 'invalid') {
                            btn.style.background = '#fff5f5';
                            btn.style.color = '#b91c1c';
                            btn.style.borderColor = '#ef4444';
                        } else if (state === 'filled') {
                            btn.style.background = '#0B266E';
                            btn.style.color = '#fff';
                            btn.style.borderColor = '#0B266E';
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

                    const idx = PERTEMUAN_REAL.indexOf(p);
                    const prev = document.getElementById('btnPrevPertemuan');
                    const next = document.getElementById('btnNextPertemuan');
                    if (prev) prev.disabled = idx <= 0;
                    if (next) {
                        next.textContent = idx >= PERTEMUAN_REAL.length - 1
                            ? 'Lanjut ke Referensi ›' : 'Selanjutnya ›';
                        next.disabled = false;
                    }
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
                        const refCard = document.getElementById('references_container')?.closest('.form-card');
                        if (refCard) {
                            refCard.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
                            const firstRef = refCard.querySelector('input[name="referensi_data[]"]');
                            if (firstRef) {
                                setTimeout(() => firstRef.focus({ preventScroll: true }), 250);
                            }
                        }
                    }
                }

                function updateAllNavIndicators() {
                    PERTEMUAN_REAL.forEach(p => updatePertemuanNavIndicator(p));
                }

                // Dipakai oleh atribut onclick pada markup
                window.goToPertemuan = goToPertemuan;
                window.prevPertemuan = prevPertemuan;
                window.nextPertemuan = nextPertemuan;
                window.savePertemuanField = savePertemuanField;
                window.updateAllNavIndicators = updateAllNavIndicators;
                window.updatePertemuanProgress = updatePertemuanProgress;

                function saveCache(nextStepNum) {
                    if (window.showLoader) window.showLoader();
                    const formData = new FormData(rpsSubmitForm);
                    fetch("{{ route('banksoal.rps.dosen.wizard-cache') }}", {
                        method: 'POST',
                        headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                        body: formData
                    })
                        .then(res => {
                            if (!res.ok) throw new Error(`Cache save failed: HTTP ${res.status}`);
                            return res.json();
                        })
                        .then(res => {
                            if (nextStepNum) loadWizardStep(nextStepNum);
                        })
                        .catch(err => {
                            console.warn('saveCache error (non-blocking):', err);
                            // Advance to next step anyway — cache is best-effort
                            if (nextStepNum) loadWizardStep(nextStepNum);
                        })
                        .finally(() => {
                            if (window.hideLoader) window.hideLoader();
                        });
                }

                let pertemuanSeeded = false;

                function initPrefilledGeneratorData() {
                    const generateDetail = parseDataAttr(rpsSubmitForm.dataset.generateDetail, null);

                    // Prefill dari DB hanya sekali. Pindah step tidak boleh menimpa isian dosen.
                    if (!pertemuanSeeded) {
                        seedPertemuanData(generateDetail);

                        if (generateDetail && Array.isArray(generateDetail.referensi_data) && generateDetail.referensi_data.length > 0) {
                            const isOpen = @json($isUploadOpen);
                            refContainer.innerHTML = '';
                            generateDetail.referensi_data.forEach(ref => {
                                const row = document.createElement('div');
                                row.className = 'ref-row';

                                const input = document.createElement('input');
                                input.type = 'text';
                                input.name = 'referensi_data[]';
                                input.className = 'field-control';
                                input.value = ref ?? '';
                                input.required = true;
                                input.disabled = !isOpen;
                                row.appendChild(input);

                                const removeBtn = document.createElement('button');
                                removeBtn.type = 'button';
                                removeBtn.className = 'btn-danger-sm btn-remove-ref';
                                removeBtn.style.flexShrink = '0';
                                removeBtn.title = 'Hapus referensi';
                                removeBtn.setAttribute('aria-label', 'Hapus referensi');
                                removeBtn.innerHTML = '<i class="fas fa-trash"></i>';
                                removeBtn.disabled = !isOpen;
                                row.appendChild(removeBtn);

                                refContainer.appendChild(row);
                            });
                        }

                        pertemuanSeeded = true;
                    }

                    // Selalu tampilkan kartu pertemuan yang sedang aktif
                    goToPertemuan(currentPertemuan || 1);

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

                // Generator edit mode: pre-render pertemuan & referensi from DB on page load
                // All function definitions are ready at this point
                if ((creationMethodInput ? creationMethodInput.value : 'upload') === 'generator') {
                    try {
                        initPrefilledGeneratorData();
                    } catch (error) {
                        console.error('Gagal menyiapkan data generator RPS:', error);
                    }
                }

                document.addEventListener('input', (e) => { if (e.target.closest('.wizard-content-section')) triggerAutoSave(); });
                document.addEventListener('change', (e) => { if (e.target.closest('.wizard-content-section')) triggerAutoSave(); });

                rpsSubmitForm.addEventListener('submit', function (e) {
                    e.preventDefault();
                    const method = creationMethodInput ? creationMethodInput.value : 'upload';

                    if (method === 'upload') {
                        const uploadContainer = document.getElementById('cpmkRows');
                        const isCpmkValid = window.validateCpmkDuplicates ? window.validateCpmkDuplicates(uploadContainer) : true;
                        if (!isCpmkValid) {
                            if (window.Swal) {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Kombinasi CPMK Duplikat',
                                    text: 'Terdapat kombinasi CPL dan Kode CPMK yang sama pada baris CPMK. Mohon bedakan kodenya.',
                                    confirmButtonColor: '#0B266E'
                                });
                            } else {
                                alert('Terdapat kombinasi CPL dan Kode CPMK yang sama pada baris CPMK. Mohon bedakan kodenya.');
                            }
                            return;
                        }

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
                    const genContainer = document.getElementById('cpmkRowsGenerator');
                    const isCpmkValid = window.validateCpmkDuplicates ? window.validateCpmkDuplicates(genContainer) : true;
                    if (!isCpmkValid) {
                        loadWizardStep(1);
                        if (window.Swal) {
                            Swal.fire({
                                icon: 'error',
                                title: 'Kombinasi CPMK Duplikat',
                                text: 'Terdapat kombinasi CPL dan Kode CPMK yang sama pada Step 1. Mohon bedakan kode CPMK untuk CPL yang sama.',
                                confirmButtonColor: '#0B266E'
                            });
                        } else {
                            alert('Terdapat kombinasi CPL dan Kode CPMK yang sama pada Step 1. Mohon bedakan kode CPMK untuk CPL yang sama.');
                        }
                        const firstDup = genContainer?.querySelector('.cpmk-duplicate-warning, input.is-invalid');
                        if (firstDup) {
                            firstDup.scrollIntoView({ behavior: 'smooth', block: 'center' });
                        }
                        return;
                    }

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

                    // Buang pertemuan_data parsial dari DOM, lalu kirim seluruh 16 pertemuan
                    // dari window.pertemuanData supaya isian kartu lain tidak hilang.
                    for (const key of Array.from(formData.keys())) {
                        if (key.startsWith('pertemuan_data[')) {
                            formData.delete(key);
                        }
                    }

                    const pData = window.pertemuanData || {};
                    for (let i = 1; i <= 16; i++) {
                        if (i === 8 || i === 16) {
                            formData.append(`pertemuan_data[${i}][pertemuan]`, String(i));
                            formData.append(`pertemuan_data[${i}][kemampuan_akhir]`, 'Evaluation');
                            formData.append(`pertemuan_data[${i}][pokok_bahasan]`, '-');
                            formData.append(`pertemuan_data[${i}][metode]`, '-');
                            formData.append(`pertemuan_data[${i}][waktu]`, '0');
                            formData.append(`pertemuan_data[${i}][bobot]`, '0');
                            formData.append(`pertemuan_data[${i}][pengalaman_belajar]`, '-');
                            formData.append(`pertemuan_data[${i}][kriteria_penilaian]`, '-');
                            continue;
                        }

                        const d = pData[i] || {};
                        formData.append(`pertemuan_data[${i}][pertemuan]`, String(i));
                        formData.append(`pertemuan_data[${i}][kemampuan_akhir]`, d.kemampuan_akhir || '');
                        formData.append(`pertemuan_data[${i}][pokok_bahasan]`, d.pokok_bahasan || '');
                        formData.append(`pertemuan_data[${i}][metode]`, d.metode || '');
                        formData.append(`pertemuan_data[${i}][waktu]`, d.waktu || '150');
                        formData.append(`pertemuan_data[${i}][bobot]`, d.bobot || '5');
                        formData.append(`pertemuan_data[${i}][pengalaman_belajar]`, d.pengalaman_belajar || '');
                        formData.append(`pertemuan_data[${i}][kriteria_penilaian]`, d.kriteria_penilaian || '');

                        const tc = d.target_cpmk;
                        if (Array.isArray(tc)) {
                            tc.forEach(c => formData.append(`pertemuan_data[${i}][target_cpmk][]`, c));
                        } else if (typeof tc === 'string' && tc) {
                            tc.split(',').map(s => s.trim()).filter(Boolean).forEach(c => {
                                formData.append(`pertemuan_data[${i}][target_cpmk][]`, c);
                            });
                        }
                    }

                    fetch(rpsSubmitForm.action, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value,
                            'X-Requested-With': 'XMLHttpRequest',
                            'Accept': 'application/json',
                        },
                        body: formData,
                    })
                        .then(async response => {
                            console.log("Server response status:", response.status);
                            const text = await response.text();
                            let data;
                            try {
                                data = JSON.parse(text);
                            } catch (e) {
                                console.error("Server returned non-JSON response:", text.substring(0, 500));
                                throw new Error('Server mengembalikan respons yang tidak valid. Silakan coba lagi atau hubungi administrator.');
                            }
                            return { status: response.status, data };
                        })
                        .then(({ status, data }) => {
                            if (data.success && data.redirect) {
                                // Badge notifikasi ditampilkan di halaman tujuan lewat session flash.
                                setTimeout(() => { window.location.href = data.redirect; }, 900);
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
                            const errMsg = err.message && !err.message.includes('JSON') ? err.message : 'Koneksi gagal atau respons server tidak valid. Silakan periksa jaringan Anda dan coba lagi.';
                            if (typeof Snackbar !== 'undefined' && typeof Snackbar.show === 'function') {
                                Snackbar.show(errMsg, 'error', 6000);
                            } else {
                                alert(errMsg);
                            }
                        });
                });
            });
        </script>
        <script src="{{ asset('modules/banksoal/js/Banksoal/components/RpsCpmkRows.js') }}?v={{ time() }}"></script>
    @endpush
</x-banksoal::layouts.dosen-admin>