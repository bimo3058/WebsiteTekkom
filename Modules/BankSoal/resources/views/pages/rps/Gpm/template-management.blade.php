<x-banksoal::layouts.gpm-master>

    @section('page-title', 'Manajemen Template RPS')
    @section('page-subtitle', 'Kelola template RPS yang dapat diunduh oleh dosen')

    <style>
        .template-card {
            background: white;
            border-radius: 8px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
            margin-bottom: 1.5rem;
            overflow: hidden;
        }

        .template-card-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 1.5rem;
            border-bottom: 3px solid #764ba2;
        }

        .template-card-body {
            padding: 1.5rem;
        }

        .template-list {
            list-style: none;
            padding: 0;
            margin: 0;
        }

        .template-item {
            display: flex;
            align-items: center;
            padding: 1rem;
            border-bottom: 1px solid #e2e8f0;
            transition: background-color 0.2s;
        }

        .template-item:last-child {
            border-bottom: none;
        }

        .template-item:hover {
            background-color: #f8fafc;
        }

        .template-badge {
            display: inline-block;
            padding: 0.25rem 0.75rem;
            border-radius: 9999px;
            font-size: 0.75rem;
            font-weight: 600;
            margin-right: 0.5rem;
        }

        .badge-latest {
            background-color: #dcfce7;
            color: #166534;
        }

        .badge-version {
            background-color: #e0e7ff;
            color: #3730a3;
        }

        .template-info {
            flex: 1;
        }

        .template-filename {
            font-weight: 600;
            color: #111827;
            margin-bottom: 0.25rem;
        }

        .template-meta {
            font-size: 0.875rem;
            color: #64748b;
        }

        .template-actions {
            display: flex;
            gap: 0.5rem;
            align-items: center;
        }

        .btn-icon {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 36px;
            height: 36px;
            border-radius: 6px;
            border: none;
            cursor: pointer;
            font-size: 14px;
            transition: all 0.2s;
        }

        .btn-delete {
            background-color: #fee2e2;
            color: #991b1b;
        }

        .btn-delete:hover {
            background-color: #fca5a5;
            color: #7f1d1d;
        }

        .upload-form-card {
            background: white;
            border-radius: 8px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
            padding: 1.5rem;
            margin-bottom: 2rem;
        }

        .form-group-upload {
            margin-bottom: 1.5rem;
        }

        .form-group-upload label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 600;
            color: #111827;
        }

        .upload-box {
            border: 2px dashed #cbd5e1;
            border-radius: 8px;
            padding: 2rem;
            text-align: center;
            cursor: pointer;
            transition: all 0.2s;
            background-color: #f8fafc;
        }

        .upload-box:hover {
            border-color: #667eea;
            background-color: #f1f5f9;
        }

        .upload-box.drag-over {
            border-color: #667eea;
            background-color: rgba(102, 126, 234, 0.05);
        }

        .upload-icon {
            font-size: 2rem;
            color: #64748b;
            margin-bottom: 0.5rem;
        }

        .upload-text {
            margin-bottom: 0.25rem;
        }

        .upload-subtext {
            font-size: 0.875rem;
            color: #64748b;
        }

        #fileTemplate {
            display: none;
        }

        .btn-primary {
            background-color: #667eea;
            color: white;
            padding: 0.75rem 1.5rem;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-weight: 600;
            transition: all 0.2s;
        }

        .btn-primary:hover {
            background-color: #5568d3;
        }

        .btn-secondary {
            background-color: #e2e8f0;
            color: #334155;
            padding: 0.75rem 1.5rem;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-weight: 600;
            transition: all 0.2s;
        }

        .btn-secondary:hover {
            background-color: #cbd5e1;
        }

        .empty-state {
            text-align: center;
            padding: 2rem;
            color: #64748b;
        }

        .empty-icon {
            font-size: 3rem;
            margin-bottom: 1rem;
            opacity: 0.5;
        }
    </style>

    <div class="container-lg" style="padding: 2rem 0;">

        <!-- Upload Form -->
        <div class="upload-form-card">
            <h3 style="margin-top: 0; margin-bottom: 1.5rem; color: #111827;">
                <i class="fas fa-upload" style="margin-right: 0.5rem;"></i> Upload Template RPS Baru
            </h3>

            <form action="{{ route('banksoal.rps.gpm.template.store') }}" method="POST" enctype="multipart/form-data">
                @csrf

                <div class="form-group-upload">
                    <label for="fileTemplate">
                        <i class="fas fa-file-word" style="color: #2563eb; margin-right: 0.5rem;"></i>
                        File Template (Word/DOCX)
                    </label>
                    <div class="upload-box" id="uploadBox">
                        <div class="upload-icon"><i class="fas fa-cloud-upload-alt"></i></div>
                        <div class="upload-text" style="font-weight: 600; color: #111827;">
                            Klik di sini atau seret file untuk upload
                        </div>
                        <div class="upload-subtext">
                            Format: .doc atau .docx (Maksimal 1MB)
                        </div>
                        <input type="file" id="fileTemplate" name="dokumen" accept=".doc,.docx" required>
                    </div>
                    @error('dokumen')
                        <div style="color: #991b1b; font-size: 0.875rem; margin-top: 0.5rem;">
                            <i class="fas fa-exclamation-circle"></i> {{ $message }}
                        </div>
                    @enderror
                </div>

                <div class="form-group-upload">
                    <label for="keterangan">
                        <i class="fas fa-note-sticky" style="color: #6b7280; margin-right: 0.5rem;"></i>
                        Catatan / Keterangan (Opsional)
                    </label>
                    <textarea 
                        id="keterangan" 
                        name="keterangan" 
                        rows="3" 
                        placeholder="Masukkan keterangan tentang template ini, misal: Perbaikan format, update standar, dll."
                        style="width: 100%; padding: 0.75rem; border: 1px solid #cbd5e1; border-radius: 6px; font-family: inherit; resize: vertical;"
                    ></textarea>
                    @error('keterangan')
                        <div style="color: #991b1b; font-size: 0.875rem; margin-top: 0.5rem;">
                            <i class="fas fa-exclamation-circle"></i> {{ $message }}
                        </div>
                    @enderror
                </div>

                <div style="display: flex; gap: 1rem; justify-content: flex-end;">
                    <button type="reset" class="btn-secondary">
                        <i class="fas fa-times" style="margin-right: 0.5rem;"></i> Reset
                    </button>
                    <button type="submit" class="btn-primary" id="submitBtn">
                        <i class="fas fa-arrow-up-from-bracket" style="margin-right: 0.5rem;"></i> Upload Template
                    </button>
                </div>

                <div id="fileSelected" style="margin-top: 1rem; padding: 0.75rem; background-color: #dcfce7; border-radius: 6px; color: #166534; display: none;">
                    <i class="fas fa-check-circle" style="margin-right: 0.5rem;"></i>
                    File terpilih: <strong id="selectedFileName"></strong>
                </div>
            </form>
        </div>

        <!-- Template List -->
        <div class="template-card">
            <div class="template-card-header">
                <h3 style="margin: 0; display: flex; align-items: center; gap: 0.75rem;">
                    <i class="fas fa-list"></i>
                    Daftar Template ({{ $templates->count() }} versi)
                </h3>
            </div>

            <div class="template-card-body">
                @if($templates->isEmpty())
                    <div class="empty-state">
                        <div class="empty-icon"><i class="fas fa-file-circle-question"></i></div>
                        <p>Belum ada template yang diupload</p>
                        <p style="font-size: 0.875rem;">Upload template pertama Anda di atas untuk memulai</p>
                    </div>
                @else
                    <ul class="template-list">
                        @foreach($templates as $template)
                            <li class="template-item">
                                <div style="display: flex; align-items: center; gap: 0.75rem; flex: 1;">
                                    <i class="fas fa-file-word" style="font-size: 1.5rem; color: #2563eb;"></i>
                                    
                                    <div class="template-info">
                                        <div class="template-filename">
                                            {{ $template->original_filename }}
                                            @if($template->is_latest)
                                                <span class="template-badge badge-latest">
                                                    <i class="fas fa-star"></i> Versi Terbaru
                                                </span>
                                            @endif
                                        </div>
                                        <div class="template-meta">
                                            <span class="template-badge badge-version">v{{ $template->version }}</span>
                                            Upload oleh: <strong>{{ $template->uploadedBy->name }}</strong>
                                            <br>
                                            Tanggal: <strong>{{ $template->created_at->format('d M Y H:i') }}</strong>
                                            @if($template->keterangan)
                                                <br>
                                                Keterangan: <em>{{ $template->keterangan }}</em>
                                            @endif
                                        </div>
                                    </div>
                                </div>

                                <div class="template-actions">
                                    @if($template->is_latest)
                                        <span style="padding: 0.5rem 0.75rem; background-color: #dcfce7; color: #166534; border-radius: 6px; font-size: 0.75rem; font-weight: 600;">
                                            AKTIF
                                        </span>
                                    @else
                                        <form action="{{ route('banksoal.rps.gpm.template.destroy', $template->id) }}" method="POST" style="display: inline;" onsubmit="return confirm('Yakin ingin menghapus template ini?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn-icon btn-delete" title="Hapus template">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </li>
                        @endforeach
                    </ul>
                @endif
            </div>
        </div>

        <!-- Info Box -->
        <div style="background-color: #e0e7ff; border-left: 4px solid #667eea; padding: 1rem; border-radius: 6px; margin-top: 2rem;">
            <h4 style="margin-top: 0; color: #3730a3; display: flex; align-items: center; gap: 0.5rem;">
                <i class="fas fa-info-circle"></i> Informasi Penting
            </h4>
            <ul style="margin: 0.5rem 0 0 0; padding-left: 1.5rem; color: #3730a3;">
                <li>Template disimpan dengan struktur folder: <code>TemplateRPS_V1</code>, <code>TemplateRPS_V2</code>, dst</li>
                <li>Dosen hanya dapat mengunduh <strong>versi tertinggi</strong> (V dengan angka paling besar)</li>
                <li>Versi sebelumnya tetap tersimpan untuk referensi dan riwayat</li>
                <li>Hanya satu template yang dapat menjadi "Versi Terbaru" pada saat tertentu</li>
                <li>Ketika upload template baru, template sebelumnya otomatis menjadi tidak aktif</li>
                <li>Template disimpan di Supabase: <code>rps/templates/rps/</code></li>
            </ul>
        </div>
    </div>

    <script>
        // Handle drag & drop upload
        const uploadBox = document.getElementById('uploadBox');
        const fileInput = document.getElementById('fileTemplate');
        const fileSelected = document.getElementById('fileSelected');
        const selectedFileName = document.getElementById('selectedFileName');

        // Prevent default drag behaviors
        ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
            uploadBox.addEventListener(eventName, preventDefaults, false);
            document.body.addEventListener(eventName, preventDefaults, false);
        });

        function preventDefaults(e) {
            e.preventDefault();
            e.stopPropagation();
        }

        // Highlight drop area when item is dragged over it
        ['dragenter', 'dragover'].forEach(eventName => {
            uploadBox.addEventListener(eventName, () => {
                uploadBox.classList.add('drag-over');
            });
        });

        ['dragleave', 'drop'].forEach(eventName => {
            uploadBox.addEventListener(eventName, () => {
                uploadBox.classList.remove('drag-over');
            });
        });

        // Handle dropped files
        uploadBox.addEventListener('drop', (e) => {
            const dt = e.dataTransfer;
            const files = dt.files;
            fileInput.files = files;
            updateFileDisplay();
        });

        // Handle file input change
        fileInput.addEventListener('change', updateFileDisplay);

        function updateFileDisplay() {
            if (fileInput.files.length > 0) {
                const fileName = fileInput.files[0].name;
                selectedFileName.textContent = fileName;
                fileSelected.style.display = 'block';
            } else {
                fileSelected.style.display = 'none';
            }
        }

        // Make upload box clickable
        uploadBox.addEventListener('click', () => {
            fileInput.click();
        });
    </script>
</x-banksoal::layouts.gpm-master>