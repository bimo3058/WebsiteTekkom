<x-banksoal::layouts.admin>
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
            --slate-600: #475569;
            --slate-700: #334155;
            --slate-800: #1e293b;
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

        .form-group input, .form-group textarea {
            width: 100%;
            padding: 12px 14px;
            border: 1px solid var(--slate-300);
            border-radius: 8px;
            font-size: 14px;
            color: var(--slate-800);
            transition: all 0.2s;
        }

        .form-group textarea {
            min-height: 120px;
            resize: vertical;
        }

        .form-group input:focus, .form-group textarea:focus {
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

        .form-error.show { display: block; }

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

        .btn-secondary {
            background: #fff;
            color: var(--slate-700);
            border: 1px solid var(--slate-300);
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 6px;
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

        .divider::before, .divider::after {
            content: '';
            flex: 1;
            border-bottom: 1px solid var(--slate-200);
        }

        .divider:not(:empty)::before { margin-right: 16px; }
        .divider:not(:empty)::after { margin-left: 16px; }

        .upload-section {
            background: #fff;
            border: 1px solid var(--slate-200);
            border-radius: 12px;
            padding: 24px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
        }

        .upload-zone {
            border: 2px dashed var(--slate-300);
            border-radius: 12px;
            padding: 40px 24px;
            text-align: center;
            background: var(--slate-50);
            cursor: pointer;
            position: relative;
        }

        .upload-zone input[type="file"] {
            position: absolute;
            top: 0; left: 0; width: 100%; height: 100%;
            opacity: 0; cursor: pointer;
        }

        .selected-file-info {
            display: none;
            align-items: center;
            gap: 12px;
            padding: 16px;
            border: 1px solid var(--primary-blue);
            border-radius: 8px;
            margin-top: 16px;
        }

        .selected-file-info.show { display: flex; }
    </style>
    @endpush

    @section('breadcrumbs')
    <a href="{{ route('banksoal.admin.kontrol-umum.mata-kuliah') }}" class="text-slate-500 hover:text-primary transition-colors">Kontrol Umum</a>
    <span class="mx-2 text-slate-300">/</span>
    <a href="{{ route('banksoal.admin.kontrol-umum.mata-kuliah') }}" class="text-slate-500 hover:text-primary transition-colors">Manajemen Data</a>
    <span class="mx-2 text-slate-300">/</span>
    <span class="text-slate-800 font-semibold">Tambah CPL</span>
    @endsection

    <div class="page-header" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px;">
        <div class="header-content">
            <h1 style="font-size: 24px; font-weight: 700; color: var(--slate-800); margin: 0;">Tambah CPL Baru</h1>
            <p style="font-size: 14px; color: var(--slate-500); margin-top: 4px;">Tambahkan Capaian Pembelajaran Lulusan baru.</p>
        </div>
        <a href="{{ route('banksoal.admin.kontrol-umum.mata-kuliah') }}" class="btn btn-secondary">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
            Kembali
        </a>
    </div>

    <!-- CPL Content -->
    <div id="tab-cpl" class="tab-content active">
        <div class="form-section">
            <h2 class="form-title">Informasi CPL</h2>
            <form id="cplForm" onsubmit="handleFormSubmit(event, 'cpl')">
                @csrf
                <div class="form-group">
                    <label for="kode">Kode CPL (Opsional)</label>
                    <input type="text" id="kode" name="kode" placeholder="Contoh: CPL-1 (Kosongkan untuk otomatis)" maxlength="50">
                    <div class="form-error" id="error-kode"></div>
                </div>

                <div class="form-group">
                    <label for="deskripsi">Deskripsi CPL <span style="color: var(--danger-red)">*</span></label>
                    <textarea id="deskripsi" name="deskripsi" placeholder="Masukkan deskripsi CPL..." required></textarea>
                    <div class="form-error" id="error-deskripsi"></div>
                </div>

                <div class="form-actions">
                    <button type="submit" class="btn btn-primary">Simpan CPL</button>
                </div>
            </form>
        </div>

        <div class="divider">Atau bisa juga</div>

        <div class="upload-section">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
                <h2 style="font-size: 18px; font-weight: 700; margin: 0;">Upload Data Kolektif CPL</h2>
                <a href="{{ route('banksoal.api.v1.admin.cpl.export-template') }}" style="color: var(--primary-blue); font-size: 13px; font-weight: 600; text-decoration: none;">Unduh Template</a>
            </div>
            
            <form id="importForm" onsubmit="handleImportSubmit(event, 'cpl')">
                @csrf
                <div class="upload-zone" id="dropZone">
                    <input type="file" id="import_file" name="file" accept=".csv, .xlsx, .xls" required onchange="handleFileSelect(this, 'cpl')">
                    <div class="upload-text">Klik atau seret file ke area ini</div>
                </div>

                <div class="selected-file-info" id="selectedFileInfo">
                    <div id="fileName" style="flex: 1; font-size: 14px; font-weight: 600;"></div>
                    <button type="button" onclick="removeFile('cpl')" style="color: var(--danger-red); border: none; background: none; cursor: pointer;">Hapus</button>
                </div>

                <div class="form-actions" id="uploadActions" style="display: none;">
                    <button type="submit" class="btn btn-primary">Mulai Unggah</button>
                </div>
            </form>
        </div>
    </div>

    @push('scripts')
    <script>
        const API_URL = '{{ route("banksoal.api.v1.admin.cpl.store") }}';
        const IMPORT_URL = '{{ route("banksoal.api.v1.admin.cpl.import") }}';
        const csrfToken = '{{ csrf_token() }}';

        function handleFileSelect(input) {
            if (input.files && input.files[0]) {
                document.getElementById('fileName').textContent = input.files[0].name;
                document.getElementById('selectedFileInfo').classList.add('show');
                document.getElementById('uploadActions').style.display = 'flex';
            }
        }

        function removeFile() {
            document.getElementById('import_file').value = '';
            document.getElementById('selectedFileInfo').classList.remove('show');
            document.getElementById('uploadActions').style.display = 'none';
        }

        async function handleFormSubmit(e) {
            e.preventDefault();
            const submitBtn = e.target.querySelector('button[type="submit"]');
            submitBtn.disabled = true;
            window.showLoader();

            const payload = {
                kode: document.getElementById('kode').value,
                deskripsi: document.getElementById('deskripsi').value
            };

            try {
                const response = await fetch(API_URL, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' },
                    body: JSON.stringify(payload)
                });
                const data = await response.json();
                if (data.success) {
                    window.dispatchEvent(new CustomEvent('notify', { detail: { type: 'success', message: 'Data berhasil disimpan.' } }));
                    setTimeout(() => {
                        window.location.href = '{{ route("banksoal.admin.kontrol-umum.mata-kuliah") }}';
                    }, 1500);
                } else {
                    window.dispatchEvent(new CustomEvent('notify', { detail: { type: 'error', message: data.message || 'Terjadi kesalahan.' } }));
                }
            } catch (error) {
                window.dispatchEvent(new CustomEvent('notify', { detail: { type: 'error', message: 'Terjadi kesalahan sistem.' } }));
            } finally { 
                submitBtn.disabled = false; 
                window.hideLoader();
            }
        }

        async function handleImportSubmit(e) {
            e.preventDefault();
            const submitBtn = e.target.querySelector('button[type="submit"]');
            submitBtn.disabled = true;
            window.showLoader();

            const formData = new FormData();
            formData.append('file', document.getElementById('import_file').files[0]);

            try {
                const response = await fetch(IMPORT_URL, {
                    method: 'POST',
                    headers: { 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' },
                    body: formData
                });
                const data = await response.json();
                if (data.success) {
                    window.dispatchEvent(new CustomEvent('notify', { detail: { type: 'success', message: 'Data berhasil disimpan.' } }));
                    setTimeout(() => {
                        window.location.href = '{{ route("banksoal.admin.kontrol-umum.mata-kuliah") }}';
                    }, 1500);
                } else {
                    window.dispatchEvent(new CustomEvent('notify', { detail: { type: 'error', message: data.message || 'Terjadi kesalahan.' } }));
                }
            } catch (error) {
                window.dispatchEvent(new CustomEvent('notify', { detail: { type: 'error', message: 'Terjadi kesalahan sistem.' } }));
            } finally {
                submitBtn.disabled = false;
                window.hideLoader();
            }
        }
    </script>
    @endpush
</x-banksoal::layouts.admin>
