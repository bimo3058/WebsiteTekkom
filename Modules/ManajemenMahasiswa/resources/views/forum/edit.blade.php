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

            .btn-post { background-color: #818cf8; color: white; }
            .btn-post:hover { background-color: #6366f1; }
            .btn-cancel { background-color: #ef4444; color: white; }
            .btn-cancel:hover { background-color: #dc2626; }

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

            .section-toggle:hover { background: #eef2ff; border-color: #818cf8; color: #4f46e5; }
            .section-toggle.active { background: #eef2ff; border-color: #818cf8; color: #4f46e5; }

            .section-content {
                max-height: 0;
                overflow: hidden;
                transition: max-height 0.3s ease, padding 0.3s ease;
            }

            .section-content.open {
                max-height: 600px;
                padding-top: 16px;
            }

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

            .media-dropzone:hover, .media-dropzone.dragover {
                border-color: #818cf8;
                background: #eef2ff;
            }

            .media-dropzone .dropzone-text { font-size: 15px; font-weight: 600; color: #374151; margin-bottom: 4px; }
            .media-dropzone .dropzone-hint { font-size: 13px; color: #9ca3af; }
            .media-dropzone input[type="file"] { position: absolute; inset: 0; opacity: 0; cursor: pointer; }

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

            .media-preview-item img, .media-preview-item video {
                width: 100%; height: 100%; object-fit: cover;
            }

            .media-preview-item .remove-media {
                position: absolute; top: 6px; right: 6px;
                width: 26px; height: 26px; border-radius: 50%;
                background: rgba(239, 68, 68, 0.9); color: white;
                border: none; font-size: 14px;
                display: flex; align-items: center; justify-content: center;
                cursor: pointer; transition: transform 0.15s; z-index: 2;
            }

            .media-preview-item .remove-media:hover { transform: scale(1.15); }

            .media-preview-item .file-name {
                position: absolute; bottom: 0; left: 0; right: 0;
                padding: 4px 8px; background: rgba(0, 0, 0, 0.55);
                color: white; font-size: 11px;
                white-space: nowrap; overflow: hidden; text-overflow: ellipsis;
            }

            .existing-badge {
                position: absolute; top: 6px; left: 6px;
                background: rgba(79, 70, 229, 0.85); color: white;
                font-size: 10px; font-weight: 700;
                padding: 2px 6px; border-radius: 4px; z-index: 2;
            }

            .media-counter {
                font-size: 13px; font-weight: 600;
                padding: 4px 12px; border-radius: 20px;
                display: inline-block; margin-top: 8px;
            }
            .media-counter.ok { background: #dcfce7; color: #16a34a; }
            .media-counter.warn { background: #fef3c7; color: #d97706; }
            .media-counter.full { background: #fee2e2; color: #dc2626; }
        </style>
    @endpush

    <div class="mb-4 d-flex align-items-center gap-3">
        <a href="{{ route('manajemenmahasiswa.forum.show', $thread->id) }}"
            class="btn btn-light rounded-circle shadow-sm d-flex align-items-center justify-content-center"
            style="width: 40px; height: 40px;">
            <span aria-hidden="true">&larr;</span>
        </a>
        <div>
            <h3 class="fw-bold mb-0 text-dark">Edit Thread</h3>
            <p class="text-muted text-sm fw-medium mb-0">Perbarui konten postingan Anda</p>
        </div>
    </div>

    <div class="create-post-card">
        @if($errors->any())
            <div class="alert alert-danger" style="border-radius: 10px; border: none; font-size: 14px;">
                <ul class="mb-0">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @php
            $existingMedia = $thread->extractMediaUrls();
            $existingText = $thread->getTextContent();
            // Extract link from konten
            $existingLink = '';
            if (preg_match('/href=["\']([^"\']+)["\']/', $thread->konten ?? '', $m)) {
                $existingLink = $m[1];
            }
        @endphp

        <form action="{{ route('manajemenmahasiswa.forum.update', $thread->id) }}" method="POST" id="editPostForm"
            enctype="multipart/form-data">
            @csrf
            @method('PUT')

            {{-- Judul --}}
            <div class="mb-4">
                <label class="form-label">Judul Postingan <span class="text-danger">*</span></label>
                <div class="input-wrapper">
                    <input type="text" name="judul" id="inputJudul"
                        class="custom-input @error('judul') border-danger @enderror"
                        placeholder="Tuliskan judul yang menarik dan deskriptif..." maxlength="200" required
                        value="{{ old('judul', $thread->judul) }}">
                    <span class="char-count"><span id="judulCount">0</span>/200</span>
                </div>
            </div>

            {{-- Kategori --}}
            <div class="mb-4">
                <label class="form-label">Kategori Postingan <span class="text-danger">*</span></label>
                <select name="kategori" id="inputKategori"
                    class="custom-select form-select @error('kategori') border-danger @enderror" required>
                    @foreach($categories as $key => $label)
                        <option value="{{ $key }}" {{ old('kategori', $thread->kategori) === $key ? 'selected' : '' }}>{{ $label }}</option>
                    @endforeach
                </select>
            </div>

            {{-- Konten Teks --}}
            <div class="mb-4">
                <label class="form-label">Isi Postingan</label>
                <textarea name="konten" id="inputKonten" class="custom-textarea" rows="6"
                    placeholder="Bagikan apa yang ada di pikiranmu...">{{ old('konten', $existingText) }}</textarea>
            </div>

            {{-- Media Upload (Collapsible) --}}
            <div class="mb-4">
                <button type="button" class="section-toggle {{ count($existingMedia) > 0 ? 'active' : '' }}" id="toggleMedia" onclick="toggleSection('media')">
                    📷 Gambar / Video
                    @if(count($existingMedia) > 0)
                        <span style="background: #818cf8; color: white; font-size: 11px; padding: 1px 8px; border-radius: 10px; margin-left: 4px;">{{ count($existingMedia) }} file</span>
                    @endif
                    <span style="margin-left: auto; font-size: 12px; opacity: 0.6;">▼</span>
                </button>
                <div class="section-content {{ count($existingMedia) > 0 ? 'open' : '' }}" id="sectionMedia">
                    {{-- Existing media --}}
                    @if(count($existingMedia) > 0)
                        <label class="form-label mb-2" style="font-size: 13px; color: #6b7280;">Media yang sudah ada (klik ✕ untuk menghapus):</label>
                        <div class="media-preview-grid mb-3" id="existingMediaGrid">
                            @foreach($existingMedia as $i => $media)
                                <div class="media-preview-item" id="existing-media-{{ $i }}">
                                    <span class="existing-badge">Existing</span>
                                    <button type="button" class="remove-media" onclick="removeExistingMedia({{ $i }}, '{{ $media['url'] }}')">✕</button>
                                    @if($media['type'] === 'image')
                                        <img src="{{ $media['url'] }}" alt="Media">
                                    @else
                                        <video src="{{ $media['url'] }}" muted></video>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    @endif

                    {{-- Upload new media --}}
                    <div class="media-dropzone" id="mediaDropzone">
                        <input type="file" name="media_files[]" id="mediaFileInput" multiple
                            accept="image/jpeg,image/png,image/gif,image/webp,video/mp4,video/webm">
                        <div class="dropzone-text">Tambah gambar / video baru</div>
                        <div class="dropzone-hint">JPG, PNG, GIF, WEBP, MP4, WEBM • Maks 10MB per file</div>
                    </div>
                    <div id="mediaCounter"></div>
                    <div class="media-preview-grid" id="mediaPreviewGrid"></div>
                </div>
            </div>

            {{-- Hidden inputs untuk media yang dihapus --}}
            <div id="removeMediaInputs"></div>

            {{-- Link (Collapsible) --}}
            <div class="mb-5">
                <button type="button" class="section-toggle {{ $existingLink ? 'active' : '' }}" id="toggleLink" onclick="toggleSection('link')">
                    🔗 Link
                    <span style="margin-left: auto; font-size: 12px; opacity: 0.6;">▼</span>
                </button>
                <div class="section-content {{ $existingLink ? 'open' : '' }}" id="sectionLink">
                    <input type="url" name="link_url" id="inputLinkUrl" class="custom-input"
                        placeholder="https://contoh.com/artikel-menarik" value="{{ old('link_url', $existingLink) }}">
                </div>
            </div>

            {{-- Action Buttons --}}
            <div class="d-flex justify-content-end gap-3 align-items-center pb-2">
                <a href="{{ route('manajemenmahasiswa.forum.show', $thread->id) }}"
                    class="btn-action btn-cancel text-decoration-none shadow-sm">
                    <span>✕</span> Batal
                </a>
                <button type="submit" class="btn-action btn-post shadow-sm px-4">
                    💾 Simpan Perubahan
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
            judulCount.textContent = judulInput.value.length;
            judulInput.addEventListener('input', function() {
                judulCount.textContent = this.value.length;
            });

            // ---- Existing Media Removal ----
            let removedMediaUrls = [];
            let existingKeptCount = {{ count($existingMedia) }};

            function removeExistingMedia(index, url) {
                document.getElementById(`existing-media-${index}`).remove();
                removedMediaUrls.push(url);
                existingKeptCount--;

                // Add hidden input
                const container = document.getElementById('removeMediaInputs');
                const input = document.createElement('input');
                input.type = 'hidden';
                input.name = 'remove_media[]';
                input.value = url;
                container.appendChild(input);

                updateCounter();
            }

            // ---- New Media Upload ----
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

            mediaFileInput.addEventListener('change', function() {
                addMediaFiles(this.files);
            });

            function addMediaFiles(fileList) {
                const totalAllowed = MAX_FILES - existingKeptCount;
                for (const file of fileList) {
                    if (selectedFiles.length >= totalAllowed) {
                        alert(`Maksimal ${MAX_FILES} file total (${existingKeptCount} existing + ${totalAllowed} baru).`);
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
                const total = existingKeptCount + selectedFiles.length;
                if (total === 0) {
                    mediaCounter.innerHTML = '';
                    return;
                }
                let cls = 'ok';
                if (total >= 4) cls = 'warn';
                if (total >= MAX_FILES) cls = 'full';
                mediaCounter.innerHTML = `<span class="media-counter ${cls}">${total}/${MAX_FILES} file (${existingKeptCount} existing + ${selectedFiles.length} baru)</span>`;
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

            // Init counter
            updateCounter();
        </script>
    @endpush

</x-manajemenmahasiswa::layouts.mahasiswa>
