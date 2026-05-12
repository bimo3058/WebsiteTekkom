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
        .form-group select {
            width: 100%;
            padding: 12px 14px;
            border: 1px solid var(--slate-300);
            border-radius: 8px;
            font-size: 14px;
            color: var(--slate-800);
            transition: all 0.2s;
        }

        .form-group input:focus,
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

        .sks-counter {
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .sks-counter button {
            width: 42px;
            height: 42px;
            border: 1px solid var(--slate-300);
            background: var(--slate-50);
            border-radius: 8px;
            cursor: pointer;
            font-size: 18px;
            font-weight: 600;
            color: var(--slate-600);
            transition: all 0.2s;
        }

        .sks-counter button:hover {
            background: var(--slate-100);
            border-color: var(--slate-400);
            color: var(--slate-800);
        }

        .sks-counter input {
            width: 70px;
            text-align: center;
            font-weight: 600;
            font-size: 16px;
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

        .upload-zone:hover {
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
    <a href="{{ route('banksoal.admin.kontrol-umum.mata-kuliah') }}" class="text-slate-500 hover:text-primary transition-colors">Kontrol Umum</a>
    <span class="mx-2 text-slate-300">/</span>
    <a href="{{ route('banksoal.admin.kontrol-umum.mata-kuliah') }}" class="text-slate-500 hover:text-primary transition-colors">Manajemen Mata Kuliah</a>
    <span class="mx-2 text-slate-300">/</span>
    <span class="text-slate-800 font-semibold">Tambah Mata Kuliah</span>
    @endsection

    <div class="page-header" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px;">
        <div class="header-content">
            <h1 style="font-size: 24px; font-weight: 700; color: var(--slate-800); margin: 0;">Tambah Mata Kuliah Baru</h1>
            <p style="font-size: 14px; color: var(--slate-500); margin-top: 4px;">Isi formulir di bawah ini atau unggah data melalui file Excel/CSV</p>
        </div>
        <a href="{{ route('banksoal.admin.kontrol-umum.mata-kuliah') }}" class="btn btn-secondary" style="text-decoration: none; display: inline-flex; align-items: center; gap: 6px; padding: 8px 16px;">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
            Kembali
        </a>
    </div>

    <div class="form-section">
        <h2 class="form-title">Informasi Mata Kuliah</h2>
        <form id="mataKuliahForm" onsubmit="handleFormSubmit(event)">
            @csrf
            
            <div class="form-group">
                <label for="kode">Kode Mata Kuliah <span style="color: var(--danger-red)">*</span></label>
                <input type="text" id="kode" name="kode" placeholder="Contoh: PTSK6103" required maxlength="50">
                <div class="form-error" id="error-kode"></div>
            </div>

            <div class="form-group">
                <label for="nama">Nama Mata Kuliah <span style="color: var(--danger-red)">*</span></label>
                <input type="text" id="nama" name="nama" placeholder="Contoh: Dasar Komputer &amp; Pemrograman" required>
                <div class="form-error" id="error-nama"></div>
            </div>

            <div class="form-group">
                <label for="sks">Jumlah SKS <span style="color: var(--danger-red)">*</span></label>
                <div class="sks-counter">
                    <button type="button" onclick="decrementSKS()">-</button>
                    <input type="number" id="sks" name="sks" value="2" min="1" max="3" readonly>
                    <button type="button" onclick="incrementSKS()">+</button>
                </div>
                <div class="form-error" id="error-sks"></div>
            </div>

            <div class="form-group">
                <label for="semester">Semester <span style="color: var(--danger-red)">*</span></label>
                <select id="semester" name="semester" required>
                    <option value="">-- Pilih Semester --</option>
                    <option value="1">Semester 1</option>
                    <option value="2">Semester 2</option>
                    <option value="3">Semester 3</option>
                    <option value="4">Semester 4</option>
                    <option value="5">Semester 5</option>
                    <option value="6">Semester 6</option>
                    <option value="7">Semester 7</option>
                    <option value="8">Semester 8</option>
                </select>
                <div class="form-error" id="error-semester"></div>
            </div>

            <div class="form-actions" style="margin-top: 24px; padding-top: 20px; border-top: 1px solid var(--slate-100); display: flex; justify-content: flex-end;">
                <button type="submit" class="btn btn-primary">Simpan Mata Kuliah</button>
            </div>
        </form>
    </div>

    <div class="divider">Atau bisa juga</div>

    <div class="upload-section">
        <div class="upload-header">
            <h2>Upload Data Kolektif</h2>
            <a href="{{ route('banksoal.api.v1.admin.mata-kuliah.export-template') }}" class="btn-template">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
                </svg>
                Unduh Template
            </a>
        </div>
        
        <form id="importForm" onsubmit="handleImportSubmit(event)">
            @csrf
            <div class="upload-zone" id="dropZone">
                <input type="file" id="import_file" name="file" accept=".csv, application/vnd.openxmlformats-officedocument.spreadsheetml.sheet, application/vnd.ms-excel" required onchange="handleFileSelect(this)">
                <div class="upload-icon">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path>
                    </svg>
                </div>
                <div class="upload-text">Klik atau seret file ke area ini untuk mengunggah</div>
                <div class="upload-hint">Mendukung format .xls, .xlsx, atau .csv (Maksimal 5MB)</div>
            </div>

            <div class="selected-file-info" id="selectedFileInfo">
                <svg class="file-icon" width="24" height="24" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
                <div class="file-name" id="fileName">nama-file.xlsx</div>
                <button type="button" class="btn-remove-file" onclick="removeFile()">
                    <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>

            <div class="form-actions" id="uploadActions" style="display: none;">
                <button type="submit" class="btn btn-primary" id="btnSubmitImport">Mulai Mengunggah Data</button>
            </div>
        </form>
    </div>

    @push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.10.5/dist/sweetalert2.all.min.js"></script>
    <script>
        const API_URL = '{{ url("/bank-soal/admin/api/mata-kuliah") }}';
        const IMPORT_URL = '{{ route("banksoal.api.v1.admin.mata-kuliah.import") }}';
        const csrfToken = '{{ csrf_token() }}';

        // SKS Counter Logic
        function incrementSKS() {
            const input = document.getElementById('sks');
            if (parseInt(input.value) < 3) {
                input.value = parseInt(input.value) + 1;
            }
        }

        function decrementSKS() {
            const input = document.getElementById('sks');
            if (parseInt(input.value) > 1) {
                input.value = parseInt(input.value) - 1;
            }
        }

        // Handle File Selection
        function handleFileSelect(input) {
            if (input.files && input.files[0]) {
                const fileName = input.files[0].name;
                document.getElementById('fileName').textContent = fileName;
                document.getElementById('selectedFileInfo').classList.add('show');
                document.getElementById('uploadActions').style.display = 'flex';
                document.getElementById('dropZone').style.borderColor = 'var(--primary-blue)';
                document.getElementById('dropZone').style.background = 'rgba(11, 38, 110, 0.02)';
            }
        }

        function removeFile() {
            const input = document.getElementById('import_file');
            input.value = '';
            document.getElementById('selectedFileInfo').classList.remove('show');
            document.getElementById('uploadActions').style.display = 'none';
            document.getElementById('dropZone').style.borderColor = 'var(--slate-300)';
            document.getElementById('dropZone').style.background = 'var(--slate-50)';
        }

        // Form Submit
        async function handleFormSubmit(e) {
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
                kode: document.getElementById('kode').value,
                nama: document.getElementById('nama').value,
                sks: parseInt(document.getElementById('sks').value),
                semester: parseInt(document.getElementById('semester').value)
            };

            try {
                const response = await fetch(API_URL, {
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
                        text: data.message || 'Mata kuliah berhasil ditambahkan',
                        timer: 1500,
                        showConfirmButton: false
                    });
                    
                    window.location.href = '{{ route("banksoal.admin.kontrol-umum.mata-kuliah") }}';
                } else {
                    if (response.status === 422 && data.errors) {
                        for (const [field, messages] of Object.entries(data.errors)) {
                            const errEl = document.getElementById(`error-${field}`);
                            if (errEl) {
                                errEl.textContent = messages[0];
                                errEl.classList.add('show');
                            }
                        }
                    } else {
                        throw new Error(data.message || 'Gagal menambahkan mata kuliah');
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
        async function handleImportSubmit(e) {
            e.preventDefault();
            
            const submitBtn = document.getElementById('btnSubmitImport');
            const originalText = submitBtn.textContent;
            submitBtn.disabled = true;
            submitBtn.textContent = 'Mengunggah...';

            const formData = new FormData();
            const fileInput = document.getElementById('import_file');
            
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
                const response = await fetch(IMPORT_URL, {
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
                    
                    window.location.href = '{{ route("banksoal.admin.kontrol-umum.mata-kuliah") }}';
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
