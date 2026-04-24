<x-manajemenmahasiswa::layouts.mahasiswa>

    @push('styles')
        <style>
            .create-post-card {
                background: #ffffff;
                border-radius: 12px;
                padding: 30px;
                box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05), 0 2px 4px -1px rgba(0, 0, 0, 0.03);
                margin-bottom: 20px;
            }

            /* Form Elements */
            .form-label {
                font-weight: 600;
                color: #1f2937;
                font-size: 14px;
                margin-bottom: 8px;
            }

            .custom-input,
            .custom-select,
            .custom-textarea {
                background-color: #f9fafb;
                border: 1px solid #e5e7eb;
                border-radius: 8px;
                padding: 12px 16px;
                font-size: 14px;
                width: 100%;
                transition: all 0.2s;
            }

            .custom-input:focus,
            .custom-select:focus,
            .custom-textarea:focus {
                background-color: #ffffff;
                border-color: #818cf8;
                box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.1);
                outline: none;
            }

            .char-count {
                font-size: 12px;
                color: #9ca3af;
                position: absolute;
                bottom: 12px;
                right: 16px;
            }

            .input-wrapper {
                position: relative;
            }

            /* Action Buttons */
            .btn-action {
                border-radius: 8px;
                padding: 10px 24px;
                font-weight: 600;
                font-size: 14px;
                border: none;
                display: inline-flex;
                align-items: center;
                gap: 8px;
                transition: all 0.2s;
            }

            .btn-post {
                background-color: #818cf8;
                color: white;
            }

            .btn-post:hover {
                background-color: #6366f1;
            }

            .btn-cancel {
                background-color: #ef4444;
                color: white;
            }

            .btn-cancel:hover {
                background-color: #dc2626;
            }

            /* Collapsible Section */
            .section-toggle {
                display: flex;
                align-items: center;
                gap: 8px;
                padding: 10px 16px;
                background: #f9fafb;
                border: 1px solid #e5e7eb;
                border-radius: 8px;
                cursor: pointer;
                font-size: 14px;
                font-weight: 600;
                color: #374151;
                transition: all 0.2s;
                width: 100%;
                text-align: left;
            }

            .section-toggle:hover {
                background: #eef2ff;
                border-color: #818cf8;
                color: #4f46e5;
            }

            .section-toggle.active {
                background: #eef2ff;
                border-color: #818cf8;
                color: #4f46e5;
            }

            .section-content {
                max-height: 0;
                overflow: hidden;
                transition: max-height 0.3s ease, padding 0.3s ease;
            }

            .section-content.open {
                max-height: 600px;
                padding-top: 16px;
            }

            /* Media Upload Dropzone */
            .media-dropzone {
                border: 2px dashed #d1d5db;
                border-radius: 12px;
                padding: 32px 20px;
                text-align: center;
                cursor: pointer;
                transition: all 0.3s ease;
                background: #f9fafb;
                position: relative;
            }

            .media-dropzone:hover,
            .media-dropzone.dragover {
                border-color: #818cf8;
                background: #eef2ff;
            }

            .media-dropzone .dropzone-text {
                font-size: 15px;
                font-weight: 600;
                color: #374151;
                margin-bottom: 4px;
            }

            .media-dropzone .dropzone-hint {
                font-size: 13px;
                color: #9ca3af;
            }

            .media-dropzone input[type="file"] {
                position: absolute;
                inset: 0;
                opacity: 0;
                cursor: pointer;
            }

            .media-preview-grid {
                display: grid;
                grid-template-columns: repeat(auto-fill, minmax(140px, 1fr));
                gap: 12px;
                margin-top: 16px;
            }

            .media-preview-item {
                position: relative;
                border-radius: 10px;
                overflow: hidden;
                border: 1px solid #e5e7eb;
                background: #f9fafb;
                aspect-ratio: 1;
            }

            .media-preview-item img,
            .media-preview-item video {
                width: 100%;
                height: 100%;
                object-fit: cover;
            }

            .media-preview-item .remove-media {
                position: absolute;
                top: 6px;
                right: 6px;
                width: 26px;
                height: 26px;
                border-radius: 50%;
                background: rgba(239, 68, 68, 0.9);
                color: white;
                border: none;
                font-size: 14px;
                display: flex;
                align-items: center;
                justify-content: center;
                cursor: pointer;
                transition: transform 0.15s;
                z-index: 2;
            }

            .media-preview-item .remove-media:hover {
                transform: scale(1.15);
            }

            .media-preview-item .file-name {
                position: absolute;
                bottom: 0;
                left: 0;
                right: 0;
                padding: 4px 8px;
                background: rgba(0, 0, 0, 0.55);
                color: white;
                font-size: 11px;
                white-space: nowrap;
                overflow: hidden;
                text-overflow: ellipsis;
            }

            .media-counter {
                font-size: 13px;
                font-weight: 600;
                padding: 4px 12px;
                border-radius: 20px;
                display: inline-block;
                margin-top: 8px;
            }

            .media-counter.ok {
                background: #dcfce7;
                color: #16a34a;
            }

            .media-counter.warn {
                background: #fef3c7;
                color: #d97706;
            }

            .media-counter.full {
                background: #fee2e2;
                color: #dc2626;
            }
        </style>
    @endpush

    <div class="mb-4">
        <h3 class="fw-bold mb-1 text-dark">Forum Diskusi</h3>
        <p class="text-dark fw-bold" style="font-size: 14px;">Wadah komunikasi mahasiswa & alumni</p>
    </div>

    <div class="create-post-card">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h4 class="fw-bold text-dark mb-0">Buat Post</h4>
        </div>

        @if($errors->any())
            <div class="alert alert-danger" style="border-radius: 10px; border: none; font-size: 14px;">
                <ul class="mb-0">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('manajemenmahasiswa.forum.store') }}" method="POST" id="createPostForm"
            enctype="multipart/form-data">
            @csrf

            {{-- Judul --}}
            <div class="mb-4">
                <label class="form-label">Judul Postingan <span class="text-danger">*</span></label>
                <div class="input-wrapper">
                    <input type="text" name="judul" id="inputJudul"
                        class="custom-input @error('judul') border-danger @enderror"
                        placeholder="Tuliskan judul yang menarik dan deskriptif..." maxlength="100" required
                        value="{{ old('judul') }}">
                    <span class="char-count"><span id="judulCount">0</span>/100</span>
                </div>
            </div>

            {{-- Kategori --}}
            <div class="mb-4">
                <label class="form-label">Kategori Postingan <span class="text-danger">*</span></label>
                <select name="kategori" id="inputKategori"
                    class="custom-select form-select @error('kategori') border-danger @enderror" required>
                    <option value="" disabled {{ old('kategori') ? '' : 'selected' }}>Pilih Kategori Obrolan</option>
                    @foreach($categories as $key => $label)
                        <option value="{{ $key }}" {{ old('kategori') === $key ? 'selected' : '' }}>{{ $label }}</option>
                    @endforeach
                </select>
            </div>

            {{-- Konten Teks --}}
            <div class="mb-4">
                <label class="form-label">Isi Postingan</label>
                <textarea name="konten" id="inputKonten" class="custom-textarea" rows="6"
                    placeholder="Bagikan apa yang ada di pikiranmu...">{{ old('konten') }}</textarea>
            </div>

            {{-- Media Upload (Collapsible) --}}
            <div class="mb-4">
                <button type="button" class="section-toggle" id="toggleMedia" onclick="toggleSection('media')">
                    📷 Tambah Gambar / Video
                    <span style="margin-left: auto; font-size: 12px; opacity: 0.6;">▼</span>
                </button>
                <div class="section-content" id="sectionMedia">
                    <div class="media-dropzone" id="mediaDropzone">
                        <input type="file" name="media_files[]" id="mediaFileInput" multiple
                            accept="image/jpeg,image/png,image/gif,image/webp,video/mp4,video/webm">
                        <div class="dropzone-text">Click atau drag file ke sini</div>
                        <div class="dropzone-hint">JPG, PNG, GIF, WEBP, MP4, WEBM • Maks 10MB per file • Maks 5 file
                        </div>
                    </div>
                    <div id="mediaCounter"></div>
                    <div class="media-preview-grid" id="mediaPreviewGrid"></div>
                </div>
            </div>

            {{-- Link (Collapsible) --}}
            <div class="mb-5">
                <button type="button" class="section-toggle" id="toggleLink" onclick="toggleSection('link')">
                    🔗 Tambah Link
                    <span style="margin-left: auto; font-size: 12px; opacity: 0.6;">▼</span>
                </button>
                <div class="section-content" id="sectionLink">
                    <input type="url" name="link_url" id="inputLinkUrl" class="custom-input"
                        placeholder="https://contoh.com/artikel-menarik" value="{{ old('link_url') }}">
                </div>
            </div>

            {{-- Action Buttons --}}
            <div class="d-flex justify-content-end gap-3 align-items-center pb-2">
                <a href="{{ route('manajemenmahasiswa.forum.index') }}"
                    class="btn-action btn-cancel text-decoration-none shadow-sm">
                    <span>✕</span> Batal
                </a>
                <button type="submit" class="btn-action btn-post shadow-sm px-4">
                    Terbitkan
                </button>
            </div>
        </form>
    </div>

    @push('scripts')
        <script>
            // ---- Toggle Sections ----
            function toggleSection(section) {
                const content = document.getElementById(`section${section.charAt(0).toUpperCase() + section.slice(1)}`);
                const toggle = document.getElementById(`toggle${section.charAt(0).toUpperCase() + section.slice(1)}`);
                content.classList.toggle('open');
                toggle.classList.toggle('active');
            }

            // ---- Character Counter ----
            const judulInput = document.getElementById('inputJudul');
            const judulCount = document.getElementById('judulCount');
            if (judulInput.value) judulCount.textContent = judulInput.value.length;
            judulInput.addEventListener('input', function () {
                judulCount.textContent = this.value.length;
            });

            // ---- Media Upload ----
            const mediaFileInput = document.getElementById('mediaFileInput');
            const mediaDropzone = document.getElementById('mediaDropzone');
            const mediaPreviewGrid = document.getElementById('mediaPreviewGrid');
            const mediaCounter = document.getElementById('mediaCounter');
            let selectedFiles = [];
            const MAX_FILES = 5;
            const MAX_SIZE = 10 * 1024 * 1024;

            mediaDropzone.addEventListener('dragover', (e) => { e.preventDefault(); mediaDropzone.classList.add('dragover'); });
            mediaDropzone.addEventListener('dragleave', () => mediaDropzone.classList.remove('dragover'));
            mediaDropzone.addEventListener('drop', () => mediaDropzone.classList.remove('dragover'));

            mediaFileInput.addEventListener('change', function () {
                addMediaFiles(this.files);
            });

            function addMediaFiles(fileList) {
                for (const file of fileList) {
                    if (selectedFiles.length >= MAX_FILES) {
                        alert(`Maksimal ${MAX_FILES} file yang bisa diupload.`);
                        break;
                    }
                    if (file.size > MAX_SIZE) {
                        alert(`File "${file.name}" terlalu besar. Maksimal 10MB per file.`);
                        continue;
                    }
                    if (!file.type.match(/^(image|video)\//)) {
                        alert(`File "${file.name}" bukan gambar/video yang didukung.`);
                        continue;
                    }
                    selectedFiles.push(file);
                }
                syncFileInput();
                renderPreviews();
                updateCounter();

                // Auto-open media section
                if (selectedFiles.length > 0) {
                    document.getElementById('sectionMedia').classList.add('open');
                    document.getElementById('toggleMedia').classList.add('active');
                }
            }

            function removeMediaFile(index) {
                selectedFiles.splice(index, 1);
                syncFileInput();
                renderPreviews();
                updateCounter();
            }

            function syncFileInput() {
                const dt = new DataTransfer();
                selectedFiles.forEach(f => dt.items.add(f));
                mediaFileInput.files = dt.files;
            }

            function updateCounter() {
                if (selectedFiles.length === 0) {
                    mediaCounter.innerHTML = '';
                    return;
                }
                let cls = 'ok';
                if (selectedFiles.length >= 4) cls = 'warn';
                if (selectedFiles.length >= MAX_FILES) cls = 'full';
                mediaCounter.innerHTML = `<span class="media-counter ${cls}">${selectedFiles.length}/${MAX_FILES} file</span>`;
            }

            function renderPreviews() {
                mediaPreviewGrid.innerHTML = '';
                selectedFiles.forEach((file, idx) => {
                    const item = document.createElement('div');
                    item.className = 'media-preview-item';

                    const removeBtn = document.createElement('button');
                    removeBtn.type = 'button';
                    removeBtn.className = 'remove-media';
                    removeBtn.innerHTML = '✕';
                    removeBtn.onclick = () => removeMediaFile(idx);
                    item.appendChild(removeBtn);

                    if (file.type.startsWith('image/')) {
                        const img = document.createElement('img');
                        img.src = URL.createObjectURL(file);
                        img.onload = () => URL.revokeObjectURL(img.src);
                        item.appendChild(img);
                    } else if (file.type.startsWith('video/')) {
                        const video = document.createElement('video');
                        video.src = URL.createObjectURL(file);
                        video.muted = true;
                        video.onloadeddata = () => { video.currentTime = 1; };
                        item.appendChild(video);
                    }

                    const nameLabel = document.createElement('div');
                    nameLabel.className = 'file-name';
                    nameLabel.textContent = file.name;
                    item.appendChild(nameLabel);

                    mediaPreviewGrid.appendChild(item);
                });
            }
        </script>
    @endpush

</x-manajemenmahasiswa::layouts.mahasiswa>