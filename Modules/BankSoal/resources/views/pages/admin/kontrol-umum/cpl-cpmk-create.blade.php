<x-banksoal::layouts.admin>
    @push('styles')
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11.10.5/dist/sweetalert2.min.css" rel="stylesheet">
    <style>
        :root {
            --primary-blue: rgb(11, 38, 110);
            --primary-hover: rgb(8, 28, 82);
            --danger-red: #ef4444;
            --danger-hover: #dc2626;
            --slate-50: #f8fafc;
            --slate-100: #f1f5f9;
            --slate-200: #e2e8f0;
            --slate-300: #cbd5e1;
            --slate-400: #94a3b8;
            --slate-600: #475569;
            --slate-700: #334155;
            --slate-800: #1e293b;
        }

        * {
            box-sizing: border-box;
        }

        /* Breadcrumbs */
        .breadcrumbs {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 13px;
            color: var(--slate-500);
            margin-bottom: 16px;
        }

        .breadcrumbs a {
            color: var(--slate-600);
            text-decoration: none;
            font-weight: 500;
            transition: color 0.2s;
        }

        .breadcrumbs a:hover {
            color: var(--primary-blue);
        }

        .breadcrumbs .separator {
            color: var(--slate-300);
        }

        .breadcrumbs .current {
            color: var(--slate-800);
            font-weight: 600;
        }

        .page-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 24px;
            gap: 16px;
            flex-wrap: wrap;
        }

        .header-content h1 {
            font-size: 28px;
            font-weight: 700;
            color: var(--slate-800);
            margin: 0;
            letter-spacing: -0.5px;
        }

        .header-content p {
            font-size: 14px;
            color: #64748b;
            margin: 8px 0 0 0;
        }

        /* Tabs */
        .tabs {
            display: flex;
            gap: 8px;
            margin-bottom: 24px;
            background: #fff;
            padding: 8px;
            border-radius: 12px;
            border: 1px solid var(--slate-200);
            width: fit-content;
        }

        .tab-btn {
            padding: 10px 24px;
            border-radius: 8px;
            border: none;
            background: transparent;
            font-weight: 600;
            font-size: 14px;
            color: var(--slate-600);
            cursor: pointer;
            transition: all 0.2s;
        }

        .tab-btn:hover {
            color: var(--slate-800);
            background: var(--slate-50);
        }

        .tab-btn.active {
            background: var(--primary-blue);
            color: #fff;
            box-shadow: 0 2px 4px rgba(11, 38, 110, 0.15);
        }

        /* Content Areas */
        .tab-content {
            display: none;
            animation: fadeIn 0.3s ease;
        }

        .tab-content.active {
            display: block;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(5px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .form-section {
            background: #fff;
            border: 1px solid var(--slate-200);
            border-radius: 12px;
            padding: 24px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
            margin-bottom: 24px;
        }

        .form-title {
            font-size: 18px;
            font-weight: 700;
            color: var(--slate-800);
            margin: 0 0 20px 0;
            padding-bottom: 12px;
            border-bottom: 1px solid var(--slate-100);
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-size: 14px;
            font-weight: 600;
            color: var(--slate-700);
        }

        .form-group input,
        .form-group textarea,
        .form-group select {
            width: 100%;
            padding: 12px 14px;
            border: 1px solid var(--slate-300);
            border-radius: 8px;
            font-size: 14px;
            color: var(--slate-800);
            transition: all 0.2s;
        }
        
        .form-group textarea {
            min-height: 100px;
            resize: vertical;
        }

        .form-group input:focus,
        .form-group textarea:focus,
        .form-group select:focus {
            outline: none;
            border-color: var(--primary-blue);
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
        }

        .form-error {
            display: none;
            margin-top: 6px;
            font-size: 12px;
            color: var(--danger-red);
        }

        .form-error.show {
            display: block;
        }

        .form-actions {
            display: flex;
            justify-content: flex-end;
            gap: 12px;
            margin-top: 24px;
            padding-top: 20px;
            border-top: 1px solid var(--slate-100);
        }

        .btn {
            padding: 12px 24px;
            border-radius: 8px;
            border: none;
            font-weight: 600;
            font-size: 14px;
            cursor: pointer;
            transition: all 0.2s;
        }

        .btn-primary {
            background: var(--primary-blue);
            color: #fff;
            box-shadow: 0 2px 4px rgba(11, 38, 110, 0.15);
        }

        .btn-primary:hover {
            background: var(--primary-hover);
            box-shadow: 0 4px 6px rgba(11, 38, 110, 0.2);
            transform: translateY(-1px);
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

        .divider {
            display: flex;
            align-items: center;
            text-align: center;
            margin: 32px 0;
            color: var(--slate-500);
            font-size: 14px;
            font-weight: 500;
        }

        .divider::before,
        .divider::after {
            content: '';
            flex: 1;
            border-bottom: 1px solid var(--slate-200);
        }

        .divider:not(:empty)::before {
            margin-right: 16px;
        }

        .divider:not(:empty)::after {
            margin-left: 16px;
        }

        .upload-section {
            background: #fff;
            border: 1px solid var(--slate-200);
            border-radius: 12px;
            padding: 24px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
            margin-bottom: 32px;
        }

        .upload-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            padding-bottom: 12px;
            border-bottom: 1px solid var(--slate-100);
        }

        .upload-header h2 {
            font-size: 18px;
            font-weight: 700;
            color: var(--slate-800);
            margin: 0;
        }

        .btn-template {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            color: var(--primary-blue);
            background: rgba(11, 38, 110, 0.05);
            padding: 8px 16px;
            border-radius: 8px;
            font-size: 13px;
            font-weight: 600;
            text-decoration: none;
            transition: all 0.2s;
        }

        .btn-template:hover {
            background: rgba(11, 38, 110, 0.1);
        }

        .btn-template svg {
            width: 16px;
            height: 16px;
        }

        .upload-zone {
            border: 2px dashed var(--slate-300);
            border-radius: 12px;
            padding: 40px 24px;
            text-align: center;
            background: var(--slate-50);
            transition: all 0.2s;
            cursor: pointer;
            position: relative;
        }

        .upload-zone.drag-active {
            border-color: var(--primary-blue);
            background: rgba(11, 38, 110, 0.02);
        }

        .upload-zone input[type="file"] {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            opacity: 0;
            cursor: pointer;
        }

        .upload-icon {
            width: 48px;
            height: 48px;
            margin: 0 auto 16px;
            color: var(--slate-400);
            background: #fff;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 2px 4px rgba(0,0,0,0.05);
        }

        .upload-icon svg {
            width: 24px;
            height: 24px;
        }

        .upload-text {
            font-size: 15px;
            color: var(--slate-700);
            margin-bottom: 8px;
            font-weight: 500;
        }

        .upload-hint {
            font-size: 13px;
            color: var(--slate-500);
        }

        .selected-file-info {
            display: none;
            align-items: center;
            gap: 12px;
            padding: 16px;
            background: #fff;
            border: 1px solid var(--primary-blue);
            border-radius: 8px;
            margin-top: 16px;
        }

        .selected-file-info.show {
            display: flex;
        }

        .file-icon {
            color: var(--primary-blue);
        }

        .file-name {
            flex: 1;
            font-size: 14px;
            font-weight: 600;
            color: var(--slate-800);
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        .btn-remove-file {
            background: none;
            border: none;
            color: var(--slate-400);
            cursor: pointer;
            padding: 4px;
            border-radius: 4px;
        }

        .btn-remove-file:hover {
            color: var(--danger-red);
            background: var(--slate-100);
        }
    </style>
    @endpush

    @section('breadcrumbs')
    <a href="{{ route('banksoal.admin.kontrol-umum.cpl-cpmk') }}" class="text-slate-500 hover:text-primary transition-colors">Kontrol Umum</a>
    <span class="mx-2 text-slate-300">/</span>
    <a href="{{ route('banksoal.admin.kontrol-umum.cpl-cpmk') }}" class="text-slate-500 hover:text-primary transition-colors">Manajemen CPL & CPMK</a>
    <span class="mx-2 text-slate-300">/</span>
    <span class="text-slate-800 font-semibold">Tambah Data</span>
    @endsection

    <div class="page-header" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px;">
        <div class="header-content">
            <h1 style="font-size: 24px; font-weight: 700; color: var(--slate-800); margin: 0;">Tambah CPL / CPMK</h1>
            <p style="font-size: 14px; color: var(--slate-500); margin-top: 4px;">Isi formulir di bawah ini atau unggah data melalui file Excel/CSV</p>
        </div>
        <a href="{{ route('banksoal.admin.kontrol-umum.cpl-cpmk') }}" class="btn btn-secondary" style="text-decoration: none; display: inline-flex; align-items: center; gap: 6px; padding: 8px 16px;">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
            Kembali
        </a>
    </div>

    <div class="tabs">
        <button type="button" class="tab-btn active" onclick="switchTab('cpl')">Tambah CPL</button>
        <button type="button" class="tab-btn" onclick="switchTab('cpmk')">Tambah CPMK</button>
    </div>

    <!-- CPL Content -->
    <div id="tab-cpl" class="tab-content active">
        <div class="form-section">
            <h2 class="form-title">Informasi CPL</h2>
            <form id="cplForm" onsubmit="handleFormSubmit(event, 'cpl')">
                @csrf
                <div class="form-group">
                    <label for="cpl_kode">Kode CPL (Opsional)</label>
                    <input type="text" id="cpl_kode" name="kode" placeholder="Contoh: CPL-1 (Kosongkan untuk otomatis)" maxlength="50">
                    <div class="form-error" id="error-cpl-kode"></div>
                    <p style="font-size: 12px; color: var(--slate-500); margin-top: 6px;">Format harus berawalan CPL- diikuti angka.</p>
                </div>

                <div class="form-group">
                    <label for="cpl_deskripsi">Deskripsi CPL <span style="color: var(--danger-red)">*</span></label>
                    <textarea id="cpl_deskripsi" name="deskripsi" placeholder="Masukkan deskripsi CPL..." required></textarea>
                    <div class="form-error" id="error-cpl-deskripsi"></div>
                </div>

                    <div class="form-actions" style="margin-top: 24px; padding-top: 20px; border-top: 1px solid var(--slate-100); display: flex; justify-content: flex-end;">
                        <button type="submit" class="btn btn-primary">Simpan CPL</button>
                    </div>
            </form>
        </div>

        <div class="divider">Atau bisa juga</div>

        <div class="upload-section">
            <div class="upload-header">
                <h2>Upload Data Kolektif (CPL)</h2>
                <a href="{{ route('banksoal.api.v1.admin.cpl.export-template') }}" class="btn-template">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
                    </svg>
                    Unduh Template CPL
                </a>
            </div>
            
            <form id="importFormCpl" onsubmit="handleImportSubmit(event, 'cpl')">
                @csrf
                <div class="upload-zone" id="dropZoneCpl">
                    <input type="file" id="import_file_cpl" name="file" accept=".csv, application/vnd.openxmlformats-officedocument.spreadsheetml.sheet, application/vnd.ms-excel" required onchange="handleFileSelect(this, 'cpl')">
                    <div class="upload-icon">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path>
                        </svg>
                    </div>
                    <div class="upload-text">Klik atau seret file ke area ini untuk mengunggah</div>
                    <div class="upload-hint">Mendukung format .xls, .xlsx, atau .csv (Maksimal 5MB)</div>
                </div>

                <div class="selected-file-info" id="selectedFileInfoCpl">
                    <svg class="file-icon" width="24" height="24" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                    <div class="file-name" id="fileNameCpl">nama-file.xlsx</div>
                    <button type="button" class="btn-remove-file" onclick="removeFile('cpl')">
                        <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>

                <div class="form-actions" id="uploadActionsCpl" style="display: none;">
                    <button type="submit" class="btn btn-primary" id="btnSubmitImportCpl">Mulai Mengunggah Data</button>
                </div>
            </form>
        </div>
    </div>

    <!-- CPMK Content -->
    <div id="tab-cpmk" class="tab-content">
        <div class="form-section">
            <h2 class="form-title">Informasi CPMK</h2>
            <form id="cpmkForm" onsubmit="handleFormSubmit(event, 'cpmk')">
                @csrf
                <div class="form-group">
                    <label for="cpmk_kode">Kode CPMK (Opsional)</label>
                    <input type="text" id="cpmk_kode" name="kode" placeholder="Contoh: CPMK-1.1 atau 1.1 (Kosongkan untuk otomatis)" maxlength="50">
                    <div class="form-error" id="error-cpmk-kode"></div>
                    <p style="font-size: 12px; color: var(--slate-500); margin-top: 6px;">Format bisa berupa CPMK-1.1 atau 1.1</p>
                </div>

                <div class="form-group">
                    <label for="cpmk_deskripsi">Deskripsi CPMK <span style="color: var(--danger-red)">*</span></label>
                    <textarea id="cpmk_deskripsi" name="deskripsi" placeholder="Masukkan deskripsi CPMK..." required></textarea>
                    <div class="form-error" id="error-cpmk-deskripsi"></div>
                </div>

                    <div class="form-actions" style="margin-top: 24px; padding-top: 20px; border-top: 1px solid var(--slate-100); display: flex; justify-content: flex-end;">
                        <button type="submit" class="btn btn-primary">Simpan CPMK</button>
                    </div>
            </form>
        </div>

        <div class="divider">Atau bisa juga</div>

        <div class="upload-section">
            <div class="upload-header">
                <h2>Upload Data Kolektif (CPMK)</h2>
                <a href="{{ route('banksoal.api.v1.admin.cpmk.export-template') }}" class="btn-template">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
                    </svg>
                    Unduh Template CPMK
                </a>
            </div>
            
            <form id="importFormCpmk" onsubmit="handleImportSubmit(event, 'cpmk')">
                @csrf
                <div class="upload-zone" id="dropZoneCpmk">
                    <input type="file" id="import_file_cpmk" name="file" accept=".csv, application/vnd.openxmlformats-officedocument.spreadsheetml.sheet, application/vnd.ms-excel" required onchange="handleFileSelect(this, 'cpmk')">
                    <div class="upload-icon">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path>
                        </svg>
                    </div>
                    <div class="upload-text">Klik atau seret file ke area ini untuk mengunggah</div>
                    <div class="upload-hint">Mendukung format .xls, .xlsx, atau .csv (Maksimal 5MB)</div>
                </div>

                <div class="selected-file-info" id="selectedFileInfoCpmk">
                    <svg class="file-icon" width="24" height="24" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                    <div class="file-name" id="fileNameCpmk">nama-file.xlsx</div>
                    <button type="button" class="btn-remove-file" onclick="removeFile('cpmk')">
                        <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>

                <div class="form-actions" id="uploadActionsCpmk" style="display: none;">
                    <button type="submit" class="btn btn-primary" id="btnSubmitImportCpmk">Mulai Mengunggah Data</button>
                </div>
            </form>
        </div>
    </div>

    @push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.10.5/dist/sweetalert2.all.min.js"></script>
    <script>
        const csrfToken = '{{ csrf_token() }}';

        const CONFIG = {
            cpl: {
                api: '{{ url("/bank-soal/admin/api/cpl") }}',
                import: '{{ route("banksoal.api.v1.admin.cpl.import") }}'
            },
            cpmk: {
                api: '{{ url("/bank-soal/admin/api/cpmk") }}',
                import: '{{ route("banksoal.api.v1.admin.cpmk.import") }}'
            }
        };

        // Tab Switching
        function switchTab(tabId) {
            document.querySelectorAll('.tab-btn').forEach(btn => btn.classList.remove('active'));
            document.querySelectorAll('.tab-content').forEach(content => content.classList.remove('active'));

            const btnIndex = tabId === 'cpl' ? 0 : 1;
            document.querySelectorAll('.tab-btn')[btnIndex].classList.add('active');
            document.getElementById(`tab-${tabId}`).classList.add('active');
        }

        // Handle File Selection
        function handleFileSelect(input, type) {
            if (input.files && input.files[0]) {
                const fileName = input.files[0].name;
                const capitalizedType = type.charAt(0).toUpperCase() + type.slice(1);
                
                document.getElementById(`fileName${capitalizedType}`).textContent = fileName;
                document.getElementById(`selectedFileInfo${capitalizedType}`).classList.add('show');
                document.getElementById(`uploadActions${capitalizedType}`).style.display = 'flex';
                
                const dropZone = document.getElementById(`dropZone${capitalizedType}`);
                dropZone.classList.add('drag-active');
            }
        }

        function removeFile(type) {
            const capitalizedType = type.charAt(0).toUpperCase() + type.slice(1);
            const input = document.getElementById(`import_file_${type}`);
            
            input.value = '';
            document.getElementById(`selectedFileInfo${capitalizedType}`).classList.remove('show');
            document.getElementById(`uploadActions${capitalizedType}`).style.display = 'none';
            
            const dropZone = document.getElementById(`dropZone${capitalizedType}`);
            dropZone.classList.remove('drag-active');
        }

        // Form Submit
        async function handleFormSubmit(e, type) {
            e.preventDefault();
            
            // Clear errors
            document.querySelectorAll('.form-error').forEach(el => {
                el.classList.remove('show');
                el.textContent = '';
            });

            const submitBtn = e.target.querySelector('button[type="submit"]');
            const originalText = submitBtn.textContent;
            submitBtn.disabled = true;
            submitBtn.textContent = 'Menyimpan...';

            const payload = {
                kode: document.getElementById(`${type}_kode`).value,
                deskripsi: document.getElementById(`${type}_deskripsi`).value
            };

            // If empty string, don't send kode so backend auto-generates
            if (!payload.kode) {
                delete payload.kode;
            }

            try {
                const response = await fetch(CONFIG[type].api, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify(payload)
                });

                const data = await response.json();

                if (response.ok && data.success) {
                    await Swal.fire({
                        icon: 'success',
                        title: 'Berhasil',
                        text: data.message || `Data ${type.toUpperCase()} berhasil ditambahkan`,
                        timer: 1500,
                        showConfirmButton: false
                    });
                    
                    window.location.href = '{{ route("banksoal.admin.kontrol-umum.cpl-cpmk") }}';
                } else {
                    if (response.status === 422 && data.errors) {
                        for (const [field, messages] of Object.entries(data.errors)) {
                            const errEl = document.getElementById(`error-${type}-${field}`);
                            if (errEl) {
                                errEl.textContent = messages[0];
                                errEl.classList.add('show');
                            }
                        }
                    } else {
                        throw new Error(data.message || `Gagal menambahkan data ${type.toUpperCase()}`);
                    }
                }
            } catch (error) {
                Swal.fire({
                    icon: 'error',
                    title: 'Oops...',
                    text: error.message || 'Terjadi kesalahan sistem'
                });
            } finally {
                submitBtn.disabled = false;
                submitBtn.textContent = originalText;
            }
        }

        // Import Submit
        async function handleImportSubmit(e, type) {
            e.preventDefault();
            
            const capitalizedType = type.charAt(0).toUpperCase() + type.slice(1);
            const submitBtn = document.getElementById(`btnSubmitImport${capitalizedType}`);
            const originalText = submitBtn.textContent;
            
            submitBtn.disabled = true;
            submitBtn.textContent = 'Mengunggah...';

            const formData = new FormData();
            const fileInput = document.getElementById(`import_file_${type}`);
            
            if (!fileInput.files[0]) {
                Swal.fire({
                    icon: 'warning',
                    title: 'File Kosong',
                    text: 'Silakan pilih file terlebih dahulu'
                });
                submitBtn.disabled = false;
                submitBtn.textContent = originalText;
                return;
            }

            formData.append('file', fileInput.files[0]);

            try {
                const response = await fetch(CONFIG[type].import, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json'
                    },
                    body: formData
                });

                const data = await response.json();

                if (response.ok && data.success) {
                    await Swal.fire({
                        icon: 'success',
                        title: 'Berhasil',
                        text: data.message || 'Data berhasil diunggah',
                        timer: 1500,
                        showConfirmButton: false
                    });
                    
                    window.location.href = '{{ route("banksoal.admin.kontrol-umum.cpl-cpmk") }}';
                } else {
                    throw new Error(data.message || 'Gagal mengunggah data');
                }
            } catch (error) {
                Swal.fire({
                    icon: 'error',
                    title: 'Oops...',
                    text: error.message || 'Terjadi kesalahan sistem saat mengunggah file'
                });
            } finally {
                submitBtn.disabled = false;
                submitBtn.textContent = originalText;
            }
        }
    </script>
    @endpush
</x-banksoal::layouts.admin>
