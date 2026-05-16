<x-manajemenmahasiswa::layouts.admin>

    @push('styles')
        <style>
            .main-wrapper {
                background: transparent !important;
                box-shadow: none !important;
                padding: 0 !important;
            }

            .create-header {
                margin-bottom: 28px;
            }

            .create-header h4 {
                font-size: 1.5rem;
                font-weight: 700;
                color: #1e1b4b;
                margin-bottom: 4px;
            }

            .create-header p {
                font-size: 0.95rem;
                color: #6b7280;
                margin-bottom: 0;
            }

            .form-card {
                background: #fff;
                border-radius: 16px;
                border: 1px solid #e5e7eb;
                padding: 32px;
            }

            .form-section-title {
                font-size: 0.95rem;
                font-weight: 700;
                color: #1e1b4b;
                margin-bottom: 20px;
                padding-bottom: 12px;
                border-bottom: 1px solid #f3f4f6;
            }

            .form-group {
                margin-bottom: 22px;
            }

            .form-group label {
                display: block;
                font-size: 0.88rem;
                font-weight: 600;
                color: #374151;
                margin-bottom: 8px;
            }

            .form-group label .required {
                color: #ef4444;
                margin-left: 2px;
            }

            .form-input,
            .form-select-custom,
            .form-textarea {
                width: 100%;
                padding: 12px 16px;
                border: 1px solid #e5e7eb;
                border-radius: 12px;
                font-size: 0.9rem;
                color: #374151;
                background: #fafafa;
                transition: all 0.25s ease;
                outline: none;
                font-family: inherit;
            }

            .form-input:focus,
            .form-select-custom:focus,
            .form-textarea:focus {
                border-color: #6B4FF4;
                background: #fff;
                box-shadow: 0 0 0 3px rgba(107, 79, 244, 0.12);
            }

            .form-input::placeholder,
            .form-textarea::placeholder {
                color: #9ca3af;
            }

            .form-select-custom {
                appearance: none;
                background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='16' height='16' viewBox='0 0 24 24' fill='none' stroke='%239ca3af' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpath d='m6 9 6 6 6-6'/%3E%3C/svg%3E");
                background-repeat: no-repeat;
                background-position: right 14px center;
                padding-right: 40px;
                cursor: pointer;
            }

            .form-row {
                display: grid;
                grid-template-columns: 1fr 1fr;
                gap: 20px;
            }

            @media (max-width: 768px) {
                .form-row {
                    grid-template-columns: 1fr;
                }
            }

            .file-upload-zone {
                border: 2px dashed #d1d5db;
                border-radius: 14px;
                padding: 32px;
                text-align: center;
                background: #fafafa;
                transition: all 0.25s ease;
                cursor: pointer;
                position: relative;
            }

            .file-upload-zone:hover,
            .file-upload-zone.drag-over {
                border-color: #6B4FF4;
                background: #F5F3FF;
            }

            .file-upload-zone .upload-icon {
                width: 56px;
                height: 56px;
                background: #F5F3FF;
                border-radius: 50%;
                display: flex;
                align-items: center;
                justify-content: center;
                margin: 0 auto 14px;
                color: #6B4FF4;
            }

            .file-upload-zone h6 {
                font-size: 0.95rem;
                font-weight: 600;
                color: #374151;
                margin-bottom: 4px;
            }

            .file-upload-zone p {
                font-size: 0.82rem;
                color: #9ca3af;
                margin-bottom: 0;
            }

            .file-upload-zone input[type="file"] {
                position: absolute;
                inset: 0;
                opacity: 0;
                cursor: pointer;
            }

            .file-list {
                margin-top: 14px;
                display: flex;
                flex-direction: column;
                gap: 8px;
            }

            .file-item {
                display: flex;
                align-items: center;
                justify-content: space-between;
                padding: 10px 14px;
                background: #f9fafb;
                border: 1px solid #e5e7eb;
                border-radius: 10px;
                font-size: 0.85rem;
                color: #374151;
            }

            .file-item .file-name {
                display: flex;
                align-items: center;
                gap: 8px;
                font-weight: 500;
            }

            .file-item .file-remove {
                color: #ef4444;
                cursor: pointer;
                background: none;
                border: none;
                padding: 2px 6px;
                border-radius: 6px;
                transition: background 0.15s;
                font-size: 0.8rem;
                font-weight: 600;
                text-decoration: none;
            }

            .file-item .file-remove:hover {
                background: #fee2e2;
            }

            .poster-preview {
                margin-top: 12px;
                max-width: 300px;
                border-radius: 12px;
                overflow: hidden;
                border: 1px solid #e5e7eb;
            }

            .poster-preview img {
                width: 100%;
                display: block;
            }

            .poster-label {
                font-size: 0.78rem;
                color: #6b7280;
                margin-top: 6px;
            }

            .form-actions {
                display: flex;
                justify-content: flex-end;
                gap: 12px;
                margin-top: 30px;
                padding-top: 20px;
                border-top: 1px solid #f3f4f6;
            }

            .btn-cancel {
                padding: 12px 28px;
                border: 1px solid #e5e7eb;
                border-radius: 12px;
                background: #fff;
                color: #374151;
                font-size: 0.9rem;
                font-weight: 600;
                cursor: pointer;
                transition: all 0.2s ease;
                text-decoration: none;
                display: inline-flex;
                align-items: center;
            }

            .btn-cancel:hover {
                border-color: #9ca3af;
                background: #f9fafb;
                color: #374151;
            }

            .btn-update {
                padding: 12px 28px;
                border: none;
                border-radius: 12px;
                background: #6B4FF4;
                color: #fff;
                font-size: 0.9rem;
                font-weight: 600;
                cursor: pointer;
                transition: all 0.2s ease;
            }

            .btn-update:hover {
                background: #8266F5;
                transform: translateY(-1px);
                box-shadow: 0 4px 12px rgba(107, 79, 244, 0.3);
            }

            .rich-editor-wrapper {
                border: 1px solid #e5e7eb;
                border-radius: 12px;
                overflow: hidden;
                background: #fafafa;
                transition: all 0.25s ease;
            }

            .rich-editor-wrapper:focus-within {
                border-color: #6B4FF4;
                background: #fff;
                box-shadow: 0 0 0 3px rgba(107, 79, 244, 0.12);
            }

            .editor-toolbar {
                display: flex;
                align-items: center;
                gap: 2px;
                padding: 10px 14px;
                background: #f3f4f6;
                border-bottom: 1px solid #e5e7eb;
                flex-wrap: wrap;
                row-gap: 6px;
            }

            .toolbar-group {
                display: flex;
                align-items: center;
                gap: 2px;
            }

            .toolbar-divider {
                width: 1px;
                height: 22px;
                background: #d1d5db;
                margin: 0 6px;
                flex-shrink: 0;
            }

            .toolbar-btn {
                display: inline-flex;
                align-items: center;
                gap: 4px;
                padding: 5px 9px;
                border: 1px solid transparent;
                border-radius: 6px;
                background: transparent;
                color: #374151;
                font-size: 0.82rem;
                font-weight: 600;
                cursor: pointer;
                transition: all 0.15s ease;
                white-space: nowrap;
                font-family: inherit;
                line-height: 1.4;
                user-select: none;
            }

            .toolbar-btn:hover {
                background: #fff;
                border-color: #d1d5db;
                color: #1e1b4b;
            }

            .toolbar-btn.active {
                background: #F5F3FF;
                border-color: #6B4FF4;
                color: #6B4FF4;
            }

            .toolbar-btn-bold {
                font-weight: 900;
            }

            .toolbar-btn-italic {
                font-style: italic;
            }

            .toolbar-btn-underline {
                text-decoration: underline;
            }

            .toolbar-btn-h1 {
                font-size: 0.78rem;
                letter-spacing: -0.3px;
            }

            .toolbar-btn-h2 {
                font-size: 0.78rem;
                letter-spacing: -0.3px;
            }

            .editor-content {
                min-height: 220px;
                padding: 16px 18px;
                font-size: 0.9rem;
                color: #374151;
                line-height: 1.7;
                outline: none;
                background: transparent;
            }

            .editor-content:empty::before {
                content: 'Tulis isi pengumuman di sini...';
                color: #9ca3af;
                pointer-events: none;
                display: block;
            }

            .editor-content h1 {
                font-size: 1.4rem;
                font-weight: 700;
                color: #1e1b4b;
                margin: 14px 0 8px;
            }

            .editor-content h2 {
                font-size: 1.15rem;
                font-weight: 700;
                color: #1e1b4b;
                margin: 12px 0 6px;
            }

            .editor-content ul,
            .editor-content ol {
                padding-left: 24px;
                margin: 8px 0;
            }

            .editor-content li {
                margin-bottom: 4px;
            }

            .editor-content a {
                color: #6B4FF4;
                text-decoration: underline;
            }

            .editor-content hr {
                border: none;
                border-top: 2px solid #e5e7eb;
                margin: 16px 0;
            }

            .editor-content table {
                width: 100%;
                border-collapse: collapse;
                margin: 12px 0;
                font-size: 0.88rem;
            }

            .editor-content table td,
            .editor-content table th {
                border: 1px solid #d1d5db;
                padding: 8px 12px;
                text-align: left;
            }

            .editor-content table th {
                background: #f3f4f6;
                font-weight: 600;
                color: #1e1b4b;
            }

            .editor-content img {
                max-width: 100%;
                border-radius: 8px;
                margin: 8px 0;
                display: block;
            }

            .form-error {
                font-size: 0.82rem;
                color: #ef4444;
                margin-top: 6px;
            }

            .alert-success {
                background: #d1fae5;
                color: #065f46;
                border: 1px solid #a7f3d0;
                border-radius: 12px;
                padding: 14px 18px;
                font-size: 0.9rem;
                font-weight: 500;
                margin-bottom: 20px;
            }

            .alert-danger {
                background: #fee2e2;
                color: #991b1b;
                border: 1px solid #fca5a5;
                border-radius: 12px;
                padding: 14px 18px;
                font-size: 0.9rem;
                font-weight: 500;
                margin-bottom: 20px;
            }
        </style>
    @endpush

    @php
        $existingLampiran = collect($pengumuman->repoMulmed ?? []);
        $existingPoster = $existingLampiran->first(function ($f) {
            return in_array(strtolower(pathinfo($f->nama_file ?? '', PATHINFO_EXTENSION)), ['jpg', 'jpeg', 'png', 'gif', 'webp']);
        });
        $posterUrl = $existingPoster
            ? app(\App\Services\SupabaseStorage::class)->getPublicUrl($existingPoster->path_file)
            : null;
    @endphp

    <!-- Header -->
    <div class="create-header">
        <h4>Edit Pengumuman</h4>
        <p>Perbarui isi pengumuman yang telah dibuat</p>
    </div>

    @if(session('success'))
        <div class="alert-success">{{ session('success') }}</div>
    @endif

    @if($errors->any())
        <div class="alert-danger">
            <strong>Terdapat kesalahan pada input:</strong>
            <ul style="margin: 6px 0 0; padding-left: 18px;">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- Form -->
    <form action="{{ route('manajemenmahasiswa.pengumuman.update', $pengumuman->id) }}" method="POST"
        enctype="multipart/form-data" id="editForm">
        @csrf
        @method('PUT')

        <div class="form-card">
            <div class="form-section-title">Informasi Pengumuman</div>

            <!-- Judul -->
            <div class="form-group">
                <label>Judul Pengumuman <span class="required">*</span></label>
                <input type="text" name="judul" class="form-input" placeholder="Masukkan judul pengumuman"
                    value="{{ old('judul', $pengumuman->judul) }}" required>
                @error('judul') <span class="form-error">{{ $message }}</span> @enderror
            </div>

            <!-- Kategori & Target Audience -->
            <div class="form-row">
                <div class="form-group">
                    <label>Kategori</label>
                    <select name="kategori" class="form-select-custom">
                        <option value="">Pilih Kategori</option>
                        <option value="akademik" {{ old('kategori', $pengumuman->kategori) === 'akademik' ? 'selected' : '' }}>Akademik</option>
                        <option value="himpunan" {{ old('kategori', $pengumuman->kategori) === 'himpunan' ? 'selected' : '' }}>Himpunan</option>
                        <option value="lowongan" {{ old('kategori', $pengumuman->kategori) === 'lowongan' ? 'selected' : '' }}>Lowongan</option>
                        <option value="event_prodi" {{ old('kategori', $pengumuman->kategori) === 'event_prodi' ? 'selected' : '' }}>Event Prodi</option>
                    </select>
                    @error('kategori') <span class="form-error">{{ $message }}</span> @enderror
                </div>

                <div class="form-group">
                    <label>Target Audiens <span class="required">*</span></label>
                    <select name="target_audience" class="form-select-custom" required>
                        <option value="all" {{ old('target_audience', $pengumuman->target_audience) === 'all' ? 'selected' : '' }}>Semua</option>
                        <option value="mahasiswa" {{ old('target_audience', $pengumuman->target_audience) === 'mahasiswa' ? 'selected' : '' }}>Mahasiswa</option>
                        <option value="alumni" {{ old('target_audience', $pengumuman->target_audience) === 'alumni' ? 'selected' : '' }}>Alumni</option>
                    </select>
                    @error('target_audience') <span class="form-error">{{ $message }}</span> @enderror
                </div>
            </div>

            <!-- Konten -->
            <div class="form-group">
                <label>Konten Pengumuman <span class="required">*</span></label>
                <div class="rich-editor-wrapper" id="editorWrapper">
                    <div class="editor-toolbar">
                        <div class="toolbar-group">
                            <button type="button" class="toolbar-btn toolbar-btn-bold" data-cmd="bold"
                                onclick="execCmd('bold')" title="Bold">B</button>
                            <button type="button" class="toolbar-btn toolbar-btn-italic" data-cmd="italic"
                                onclick="execCmd('italic')" title="Italic">I</button>
                            <button type="button" class="toolbar-btn toolbar-btn-underline" data-cmd="underline"
                                onclick="execCmd('underline')" title="Underline">U</button>
                            <button type="button" class="toolbar-btn toolbar-btn-h1" onclick="execFormatBlock('H1')"
                                title="Heading 1">H1</button>
                            <button type="button" class="toolbar-btn toolbar-btn-h2" onclick="execFormatBlock('H2')"
                                title="Heading 2">H2</button>
                            <button type="button" class="toolbar-btn" data-cmd="insertUnorderedList"
                                onclick="execCmd('insertUnorderedList')" title="Bullet List">
                                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                    stroke-width="2.5">
                                    <line x1="9" y1="6" x2="20" y2="6" />
                                    <line x1="9" y1="12" x2="20" y2="12" />
                                    <line x1="9" y1="18" x2="20" y2="18" />
                                    <circle cx="4" cy="6" r="1.5" fill="currentColor" />
                                    <circle cx="4" cy="12" r="1.5" fill="currentColor" />
                                    <circle cx="4" cy="18" r="1.5" fill="currentColor" />
                                </svg>
                                List
                            </button>
                            <button type="button" class="toolbar-btn" data-cmd="insertOrderedList"
                                onclick="execCmd('insertOrderedList')" title="Numbered List">
                                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                    stroke-width="2.5">
                                    <line x1="10" y1="6" x2="21" y2="6" />
                                    <line x1="10" y1="12" x2="21" y2="12" />
                                    <line x1="10" y1="18" x2="21" y2="18" /><text x="2" y="9" font-size="8"
                                        fill="currentColor" stroke="none" font-weight="bold">1.</text>
                                </svg>
                                List
                            </button>
                        </div>
                        <div class="toolbar-divider"></div>
                        <div class="toolbar-group">
                            <button type="button" class="toolbar-btn" onclick="insertLink()" title="Hyperlink">
                                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                    stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M10 13a5 5 0 0 0 7.54.54l3-3a5 5 0 0 0-7.07-7.07l-1.72 1.71" />
                                    <path d="M14 11a5 5 0 0 0-7.54-.54l-3 3a5 5 0 0 0 7.07 7.07l1.71-1.71" />
                                </svg>
                                Link
                            </button>
                            <button type="button" class="toolbar-btn" onclick="triggerImageInsert()"
                                title="Sisipkan Gambar">
                                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                    stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                    <rect width="18" height="18" x="3" y="3" rx="2" />
                                    <circle cx="9" cy="9" r="2" />
                                    <path d="m21 15-3.086-3.086a2 2 0 0 0-2.828 0L6 21" />
                                </svg>
                                Gambar
                            </button>
                            <button type="button" class="toolbar-btn" onclick="insertTable()" title="Sisipkan Tabel">
                                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                    stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                    <rect width="18" height="18" x="3" y="3" rx="2" />
                                    <path d="M3 9h18M3 15h18M9 3v18M15 3v18" />
                                </svg>
                                Tabel
                            </button>
                            <button type="button" class="toolbar-btn" onclick="insertSeparator()" title="Garis Pemisah">
                                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                    stroke-width="2.5" stroke-linecap="round">
                                    <line x1="3" y1="12" x2="21" y2="12" />
                                </svg>
                                Separator
                            </button>
                        </div>
                        <input type="file" id="inlineImageInput" accept="image/jpeg,image/png,image/gif,image/webp"
                            style="display:none;">
                    </div>
                    <div class="editor-content" id="editorContent" contenteditable="true"></div>
                </div>
                <textarea name="konten" id="kontenHidden"
                    style="display:none;">{{ old('konten', $pengumuman->konten) }}</textarea>
                @error('konten') <span class="form-error">{{ $message }}</span> @enderror
            </div>

            <div class="form-section-title" style="margin-top: 10px;">Poster & Lampiran</div>

            <!-- Poster -->
            <div class="form-group">
                <label>Poster / Gambar (opsional)</label>

                @if($posterUrl)
                    <div style="margin-bottom: 12px;">
                        <div class="file-item" style="max-width: 380px;">
                            <span class="file-name">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#6b7280"
                                    stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <rect width="18" height="18" x="3" y="3" rx="2" />
                                    <circle cx="9" cy="9" r="2" />
                                    <path d="m21 15-3.086-3.086a2 2 0 0 0-2.828 0L6 21" />
                                </svg>
                                {{ $existingPoster->nama_file }}
                            </span>
                            <button type="button" class="file-remove"
                                onclick="deleteLampiran('{{ route('manajemenmahasiswa.pengumuman.lampiran.remove', [$pengumuman->id, $existingPoster->id]) }}', this, 'Hapus poster ini?')">
                                Hapus
                            </button>
                        </div>
                        <div class="poster-preview" style="margin-top: 8px;">
                            <img src="{{ $posterUrl }}" alt="Poster saat ini">
                        </div>
                        <p class="poster-label">Upload gambar baru di bawah untuk mengganti poster</p>
                    </div>
                @endif

                <div class="file-upload-zone" id="posterZone">
                    <div class="upload-icon">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                            stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <rect width="18" height="18" x="3" y="3" rx="2" ry="2" />
                            <circle cx="9" cy="9" r="2" />
                            <path d="m21 15-3.086-3.086a2 2 0 0 0-2.828 0L6 21" />
                        </svg>
                    </div>
                    <h6>{{ $posterUrl ? 'Klik untuk mengganti poster' : 'Klik atau seret gambar ke sini' }}</h6>
                    <p>JPG, PNG — Maks. 10MB</p>
                    <input type="file" name="poster" accept="image/jpeg,image/png" id="posterInput">
                </div>
                <div class="poster-preview" id="posterPreview" style="display: none; margin-top: 12px;">
                    <img id="posterImg" src="" alt="Preview Poster Baru">
                </div>
            </div>

            <!-- Lampiran yang sudah ada -->
            @php
                $existingDokumen = $existingLampiran->filter(function ($f) {
                    return !in_array(strtolower(pathinfo($f->nama_file ?? '', PATHINFO_EXTENSION)), ['jpg', 'jpeg', 'png', 'gif', 'webp']);
                });
            @endphp
            @if($existingDokumen->isNotEmpty())
                <div class="form-group">
                    <label>Lampiran Saat Ini</label>
                    <div class="file-list">
                        @foreach($existingDokumen as $dok)
                            <div class="file-item">
                                <span class="file-name">
                                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#6b7280"
                                        stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M14.5 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V7.5L14.5 2z" />
                                        <polyline points="14 2 14 8 20 8" />
                                    </svg>
                                    {{ $dok->judul_file ?? $dok->nama_file }}
                                </span>
                                <button type="button" class="file-remove"
                                    onclick="deleteLampiran('{{ route('manajemenmahasiswa.pengumuman.lampiran.remove', [$pengumuman->id, $dok->id]) }}', this, 'Hapus lampiran ini?')">
                                    Hapus
                                </button>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            <!-- Upload lampiran baru -->
            <div class="form-group">
                <label>Tambah Lampiran / Dokumen (opsional)</label>
                <div class="file-upload-zone" id="lampiranZone">
                    <div class="upload-icon">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                            stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M14.5 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V7.5L14.5 2z" />
                            <polyline points="14 2 14 8 20 8" />
                            <line x1="16" x2="8" y1="13" y2="13" />
                            <line x1="16" x2="8" y1="17" y2="17" />
                            <line x1="10" x2="8" y1="9" y2="9" />
                        </svg>
                    </div>
                    <h6>Klik atau seret dokumen ke sini</h6>
                    <p>PDF, DOCX, XLSX — Maks. 10MB per file</p>
                    <input type="file" name="lampiran[]" accept=".pdf,.docx,.xlsx" multiple id="lampiranInput">
                </div>
                <div class="file-list" id="lampiranList"></div>
            </div>

            <!-- Pertahankan status_publish yang sudah ada -->
            <input type="hidden" name="status_publish" value="{{ $pengumuman->status_publish }}">

            <!-- Actions -->
            <div class="form-actions">
                <a href="{{ route('manajemenmahasiswa.pengumuman.show', $pengumuman->id) }}"
                    class="btn-cancel">Batal</a>
                <button type="submit" class="btn-update">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                        stroke-linecap="round" stroke-linejoin="round" style="vertical-align: -2px; margin-right: 4px;">
                        <path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z" />
                        <polyline points="17 21 17 13 7 13 7 21" />
                        <polyline points="7 3 7 8 15 8" />
                    </svg>
                    Simpan Perubahan
                </button>
            </div>
        </div>
    </form>

    @push('scripts')
        <script>
            // Hapus lampiran via fetch (menghindari nested form)
            function deleteLampiran(url, btn, confirmMsg) {
                if (!confirm(confirmMsg)) return;

                btn.disabled = true;
                btn.textContent = 'Menghapus...';

                fetch(url, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json',
                    },
                })
                    .then(response => {
                        if (response.ok || response.status === 302 || response.status === 303) {
                            // Reload halaman untuk menampilkan perubahan
                            window.location.reload();
                        } else {
                            throw new Error('Gagal menghapus lampiran (status: ' + response.status + ')');
                        }
                    })
                    .catch(error => {
                        alert(error.message);
                        btn.disabled = false;
                        btn.textContent = 'Hapus';
                    });
            }

            // Pre-fill editor dengan konten yang sudah ada
            const editor = document.getElementById('editorContent');
            const kontenHidden = document.getElementById('kontenHidden');

            editor.innerHTML = kontenHidden.value;

            function syncEditorContent() {
                kontenHidden.value = editor.innerHTML.replace(/<br\s*\/?>/gi, '\n').trim() === '' ? '' : editor.innerHTML;
            }

            function execCmd(cmd) {
                editor.focus();
                document.execCommand(cmd, false, null);
                syncEditorContent();
                updateToolbarState();
            }

            function execFormatBlock(tag) {
                editor.focus();
                const sel = window.getSelection();
                if (sel.rangeCount) {
                    const block = sel.getRangeAt(0).startContainer;
                    const parent = block.nodeType === 3 ? block.parentElement : block;
                    if (parent.closest(tag.toLowerCase())) {
                        document.execCommand('formatBlock', false, 'p');
                    } else {
                        document.execCommand('formatBlock', false, tag);
                    }
                }
                syncEditorContent();
                updateToolbarState();
            }

            function updateToolbarState() {
                document.querySelectorAll('.toolbar-btn[data-cmd]').forEach(btn => {
                    try { btn.classList.toggle('active', document.queryCommandState(btn.dataset.cmd)); } catch (e) { }
                });
            }

            function insertLink() {
                const sel = window.getSelection();
                const selectedText = sel ? sel.toString() : '';
                const url = prompt('Masukkan URL link:', 'https://');
                if (!url || url === 'https://') return;
                editor.focus();
                if (selectedText) {
                    document.execCommand('createLink', false, url);
                } else {
                    const text = prompt('Teks link:', url) || url;
                    document.execCommand('insertHTML', false,
                        `<a href="${url}" target="_blank" rel="noopener">${text}</a>`);
                }
                syncEditorContent();
            }

            function triggerImageInsert() {
                document.getElementById('inlineImageInput').click();
            }

            document.getElementById('inlineImageInput').addEventListener('change', async function (e) {
                const file = e.target.files[0];
                if (!file) return;

                const placeholderId = 'img-uploading-' + Date.now();
                editor.focus();
                document.execCommand('insertHTML', false,
                    `<span id="${placeholderId}" style="color:#6b7280;font-style:italic;font-size:0.9em;">[Mengupload gambar...]</span>`);
                syncEditorContent();

                const formData = new FormData();
                formData.append('image', file);
                formData.append('_token', '{{ csrf_token() }}');

                try {
                    const res = await fetch('{{ route("manajemenmahasiswa.pengumuman.inline.image") }}', {
                        method: 'POST',
                        body: formData,
                    });
                    const data = await res.json();

                    const placeholder = document.getElementById(placeholderId);
                    if (placeholder) placeholder.remove();

                    if (data.url) {
                        editor.focus();
                        document.execCommand('insertHTML', false,
                            `<img src="${data.url}" alt="${file.name}" style="max-width:100%;border-radius:8px;margin:8px 0;">`);
                        syncEditorContent();
                    } else {
                        alert('Gagal mengupload gambar. Silakan coba lagi.');
                    }
                } catch (err) {
                    const placeholder = document.getElementById(placeholderId);
                    if (placeholder) placeholder.remove();
                    alert('Gagal mengupload gambar. Periksa koneksi internet Anda.');
                }

                this.value = '';
            });

            function insertTable() {
                const rows = parseInt(prompt('Jumlah baris (termasuk header):', '3'));
                const cols = parseInt(prompt('Jumlah kolom:', '3'));
                if (!rows || !cols || rows < 1 || cols < 1) return;
                let html = '<table><thead><tr>';
                for (let c = 0; c < cols; c++) html += `<th>Header ${c + 1}</th>`;
                html += '</tr></thead><tbody>';
                for (let r = 0; r < rows - 1; r++) {
                    html += '<tr>';
                    for (let c = 0; c < cols; c++) html += '<td>&nbsp;</td>';
                    html += '</tr>';
                }
                html += '</tbody></table><p><br></p>';
                editor.focus();
                document.execCommand('insertHTML', false, html);
                syncEditorContent();
            }

            function insertSeparator() {
                editor.focus();
                document.execCommand('insertHTML', false, '<hr><p><br></p>');
                syncEditorContent();
            }

            // Poster preview (gambar baru)
            document.getElementById('posterInput').addEventListener('change', function (e) {
                const file = e.target.files[0];
                const preview = document.getElementById('posterPreview');
                const img = document.getElementById('posterImg');
                if (file && file.type.startsWith('image/')) {
                    const reader = new FileReader();
                    reader.onload = ev => { img.src = ev.target.result; preview.style.display = 'block'; };
                    reader.readAsDataURL(file);
                } else {
                    preview.style.display = 'none';
                    img.src = '';
                }
            });

            // Lampiran baru - tampilkan nama file
            document.getElementById('lampiranInput').addEventListener('change', function (e) {
                const list = document.getElementById('lampiranList');
                list.innerHTML = '';
                Array.from(e.target.files).forEach(file => {
                    const item = document.createElement('div');
                    item.className = 'file-item';
                    item.innerHTML = `
                                <span class="file-name">
                                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#6b7280"
                                        stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M14.5 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V7.5L14.5 2z"/>
                                        <polyline points="14 2 14 8 20 8"/>
                                    </svg>
                                    ${file.name}
                                </span>
                                <span style="color:#9ca3af;font-size:0.8rem;">${(file.size / 1024 / 1024).toFixed(2)} MB</span>
                            `;
                    list.appendChild(item);
                });
            });

            // Drag-over styling
            ['posterZone', 'lampiranZone'].forEach(id => {
                const zone = document.getElementById(id);
                zone.addEventListener('dragover', e => { e.preventDefault(); zone.classList.add('drag-over'); });
                zone.addEventListener('dragleave', () => zone.classList.remove('drag-over'));
                zone.addEventListener('drop', () => zone.classList.remove('drag-over'));
            });

            // Sync on input & toolbar state
            editor.addEventListener('input', syncEditorContent);
            editor.addEventListener('keyup', updateToolbarState);
            editor.addEventListener('mouseup', updateToolbarState);

            // Validate before submit
            document.getElementById('editForm').addEventListener('submit', function (e) {
                syncEditorContent();
                const content = kontenHidden.value.replace(/<[^>]*>/g, '').trim();
                if (content.length < 10) {
                    e.preventDefault();
                    editor.focus();
                    document.getElementById('editorWrapper').style.borderColor = '#ef4444';
                    document.getElementById('editorWrapper').style.boxShadow = '0 0 0 3px rgba(239,68,68,0.12)';
                    return;
                }
                document.getElementById('editorWrapper').style.borderColor = '';
                document.getElementById('editorWrapper').style.boxShadow = '';
            });

            syncEditorContent();
            updateToolbarState();
        </script>
    @endpush

</x-manajemenmahasiswa::layouts.admin>