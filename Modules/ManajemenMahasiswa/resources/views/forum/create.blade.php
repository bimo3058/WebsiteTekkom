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

            /* Post Tabs */
            .post-tabs {
                border-bottom: 2px solid #f3f4f6;
                margin-bottom: 24px;
                display: flex;
                gap: 24px;
            }

            .post-tab-item {
                padding-bottom: 12px;
                color: #1f2937;
                font-weight: 600;
                font-size: 15px;
                cursor: pointer;
                position: relative;
            }

            .post-tab-item:hover {
                color: #4f46e5;
            }

            .post-tab-item.active {
                color: #4f46e5;
            }

            .post-tab-item.active::after {
                content: '';
                position: absolute;
                bottom: -2px;
                left: 0;
                right: 0;
                height: 2px;
                background-color: #4f46e5;
                border-radius: 2px;
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

            .btn-drafts {
                background: transparent;
                border: none;
                color: #111827;
                font-weight: 700;
                font-size: 15px;
                padding: 6px 12px;
                border-radius: 6px;
                transition: background 0.2s;
            }

            .btn-drafts:hover {
                background: #f3f4f6;
            }

            /* Media Upload Dropzone */
            .media-dropzone {
                border: 2px dashed #d1d5db;
                border-radius: 12px;
                padding: 40px 20px;
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

            .media-dropzone .dropzone-icon {
                font-size: 48px;
                margin-bottom: 12px;
                display: block;
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
                grid-template-columns: repeat(auto-fill, minmax(150px, 1fr));
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
        </style>
    @endpush

    <div class="mb-4">
        <h3 class="fw-bold mb-1 text-dark">Forum Diskusi</h3>
        <p class="text-dark fw-bold" style="font-size: 14px;">Wadah komunikasi mahasiswa & alumni</p>
    </div>

    <div class="create-post-card">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h4 class="fw-bold text-dark mb-0">Buat Post</h4>
            <button type="button" class="btn-drafts" onclick="loadDraft()">Load Draft</button>
        </div>

        <div class="post-tabs">
            <div class="post-tab-item active" data-tab="text" onclick="switchTab('text')">Teks</div>
            <div class="post-tab-item" data-tab="media" onclick="switchTab('media')">Image & Video</div>
            <div class="post-tab-item" data-tab="link" onclick="switchTab('link')">Link</div>
        </div>

        <form action="{{ route('manajemenmahasiswa.forum.store') }}" method="POST" id="createPostForm"
            enctype="multipart/form-data">
            @csrf
            <input type="hidden" name="format" id="postFormat" value="text">
            <input type="hidden" name="konten" id="finalKonten" value="">

            <div class="mb-4">
                <label class="form-label">Judul Postingan</label>
                <div class="input-wrapper">
                    <input type="text" name="judul" id="inputJudul"
                        class="custom-input align-content-start @error('judul') border-danger @enderror"
                        placeholder="Tuliskan judul yang menarik dan deskriptif..." maxlength="200" required
                        value="{{ old('judul') }}">
                    <span class="char-count"><span id="judulCount">0</span>/200</span>
                </div>
                @error('judul')
                    <div class="text-danger mt-1 fw-medium" style="font-size: 13px;">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-4">
                <label class="form-label">Kategori Postingan</label>
                <select name="kategori" id="inputKategori"
                    class="custom-select form-select @error('kategori') border-danger @enderror" required>
                    <option value="" disabled {{ old('kategori') ? '' : 'selected' }}>Pilih Kategori Obrolan</option>
                    @foreach($categories as $key => $label)
                        <option value="{{ $key }}" {{ old('kategori') === $key ? 'selected' : '' }}>{{ $label }}</option>
                    @endforeach
                </select>
                @error('kategori')
                    <div class="text-danger mt-1 fw-medium" style="font-size: 13px;">{{ $message }}</div>
                @enderror
            </div>

            <!-- Tab Content: Text -->
            <div class="mb-5 tab-content" id="content-text">
                <label class="form-label">Isi Postingan</label>
                <textarea id="inputTextBody" class="custom-textarea @error('konten') border-danger @enderror" rows="8"
                    placeholder="Bagikan apa yang ada di pikiranmu..."></textarea>
                @error('konten')
                    <div class="text-danger mt-1 fw-medium" style="font-size: 13px;">{{ $message }}</div>
                @enderror
            </div>

            <!-- Tab Content: Media -->
            <div class="mb-5 tab-content" id="content-media" style="display: none;">
                <label class="form-label">Upload Gambar / Video</label>
                <div class="media-dropzone" id="mediaDropzone">
                    <input type="file" name="media_files[]" id="mediaFileInput" multiple
                        accept="image/jpeg,image/png,image/gif,image/webp,video/mp4,video/webm">
                    <div class="dropzone-text">Click or drag file ke sini</div>
                    <div class="dropzone-hint">Mendukung: JPG, PNG, GIF, WEBP, MP4, WEBM &bull; Maks 10MB per file
                        &bull; Maks 5 file</div>
                </div>
                <div class="media-preview-grid" id="mediaPreviewGrid"></div>
                @error('media_files')
                    <div class="text-danger mt-1 fw-medium" style="font-size: 13px;">{{ $message }}</div>
                @enderror
                @error('media_files.*')
                    <div class="text-danger mt-1 fw-medium" style="font-size: 13px;">{{ $message }}</div>
                @enderror

                <label class="form-label mt-3">Deskripsi Tambahan (Opsional)</label>
                <textarea id="inputMediaCaption" name="media_caption" class="custom-textarea" rows="4"
                    placeholder="Ceritakan lebih banyak tentang media ini..."></textarea>
            </div>

            <!-- Tab Content: Link -->
            <div class="mb-5 tab-content" id="content-link" style="display: none;">
                <label class="form-label">Tautan (URL)</label>
                <input type="url" id="inputLinkUrl" class="custom-input mb-3"
                    placeholder="Contoh: https://medium.com/@username/title">
                <label class="form-label">Deskripsi Tautan (Opsional)</label>
                <textarea id="inputLinkCaption" class="custom-textarea" rows="4"
                    placeholder="Mengapa tautan ini menarik untuk dibagikan?"></textarea>
            </div>

            <div class="d-flex justify-content-end gap-3 align-items-center pb-2">
                <span id="draftStatus" class="text-muted me-auto bg-light rounded px-3 py-2 fw-medium"
                    style="font-size: 13px; display: none;"></span>

                <button type="button" class="btn btn-light fw-bold text-secondary px-4 py-2 shadow-sm"
                    onclick="saveDraft()" style="border-radius: 8px;">
                    Simpan Draft
                </button>
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
            // Tab switching logic
            function switchTab(tab) {
                // Toggle Active Class on Tabs
                document.querySelectorAll('.post-tab-item').forEach(el => el.classList.remove('active'));
                document.querySelector(`.post-tab-item[data-tab="${tab}"]`).classList.add('active');

                // Toggle Display Content
                document.querySelectorAll('.tab-content').forEach(el => el.style.display = 'none');
                document.getElementById(`content-${tab}`).style.display = 'block';

                // Update hidden format input
                document.getElementById('postFormat').value = tab;
            }

            // Live Character counter for Judul
            const judulInput = document.getElementById('inputJudul');
            const judulCount = document.getElementById('judulCount');

            // Check initial length (if validation fail return back with old)
            if (judulInput.value) {
                judulCount.textContent = judulInput.value.length;
            }

            judulInput.addEventListener('input', function () {
                judulCount.textContent = this.value.length;
            });

            // ---- Media Upload Logic ----
            const mediaFileInput = document.getElementById('mediaFileInput');
            const mediaDropzone = document.getElementById('mediaDropzone');
            const mediaPreviewGrid = document.getElementById('mediaPreviewGrid');
            let selectedFiles = []; // Track files in a DataTransfer object

            // Drag & drop visual feedback
            mediaDropzone.addEventListener('dragover', (e) => {
                e.preventDefault();
                mediaDropzone.classList.add('dragover');
            });
            mediaDropzone.addEventListener('dragleave', () => mediaDropzone.classList.remove('dragover'));
            mediaDropzone.addEventListener('drop', () => mediaDropzone.classList.remove('dragover'));

            mediaFileInput.addEventListener('change', function () {
                addMediaFiles(this.files);
            });

            function addMediaFiles(fileList) {
                const maxFiles = 5;
                const maxSize = 10 * 1024 * 1024; // 10MB

                for (const file of fileList) {
                    if (selectedFiles.length >= maxFiles) {
                        alert(`Maksimal ${maxFiles} file yang bisa diupload.`);
                        break;
                    }
                    if (file.size > maxSize) {
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
            }

            function removeMediaFile(index) {
                selectedFiles.splice(index, 1);
                syncFileInput();
                renderPreviews();
            }

            function syncFileInput() {
                // Rebuild the file input with a DataTransfer
                const dt = new DataTransfer();
                selectedFiles.forEach(f => dt.items.add(f));
                mediaFileInput.files = dt.files;
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

            // Handle Form Submission Interceptor
            const form = document.getElementById('createPostForm');
            form.addEventListener('submit', function (e) {
                const format = document.getElementById('postFormat').value;
                let combinedKonten = '';

                if (format === 'text') {
                    combinedKonten = document.getElementById('inputTextBody').value;
                }
                else if (format === 'media') {
                    // Media files are handled server-side via multipart upload
                    // Only set caption as konten placeholder; server will prepend media HTML
                    const caption = document.getElementById('inputMediaCaption').value;
                    combinedKonten = caption || '';
                }
                else if (format === 'link') {
                    const linkUrl = document.getElementById('inputLinkUrl').value;
                    const caption = document.getElementById('inputLinkCaption').value;
                    if (linkUrl) {
                        combinedKonten = `<a href="${linkUrl}" target="_blank" class="d-inline-flex p-3 rounded bg-light border border-primary-subtle text-primary fw-bold text-decoration-none mb-3">🔗 ${linkUrl}</a><br>${caption}`;
                    } else {
                        combinedKonten = caption;
                    }
                }

                // Fill the final hidden input to be sent to backend
                document.getElementById('finalKonten').value = combinedKonten;

                // Clean draft completely upon successful submission
                localStorage.removeItem('forum_draft_judul');
                localStorage.removeItem('forum_draft_kategori');
                localStorage.removeItem('forum_draft_text');
                localStorage.removeItem('forum_draft_mediaCap');
                localStorage.removeItem('forum_draft_linkUrl');
                localStorage.removeItem('forum_draft_linkCap');
                localStorage.removeItem('forum_draft_format');
            });

            // -----------------------------------------
            // Drafts Logic using Client Side localStorage
            // -----------------------------------------
            function saveDraft() {
                // Save current format tab
                const format = document.getElementById('postFormat').value;
                localStorage.setItem('forum_draft_format', format);

                // Save Global Fields
                localStorage.setItem('forum_draft_judul', document.getElementById('inputJudul').value);
                localStorage.setItem('forum_draft_kategori', document.getElementById('inputKategori').value);

                // Save tab-specific fields
                localStorage.setItem('forum_draft_text', document.getElementById('inputTextBody').value);
                localStorage.setItem('forum_draft_mediaCap', document.getElementById('inputMediaCaption').value);
                localStorage.setItem('forum_draft_linkUrl', document.getElementById('inputLinkUrl').value);
                localStorage.setItem('forum_draft_linkCap', document.getElementById('inputLinkCaption').value);

                showDraftStatus('✅ Draft berhasil disimpan: ' + new Date().toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' }));
            }

            function loadDraft() {
                const title = localStorage.getItem('forum_draft_judul');
                const category = localStorage.getItem('forum_draft_kategori');
                const format = localStorage.getItem('forum_draft_format') || 'text';

                let draftFound = false;

                // Restore global fields
                if (title) { document.getElementById('inputJudul').value = title; draftFound = true; }
                if (category) { document.getElementById('inputKategori').value = category; draftFound = true; }

                // Restore tab-specific fields
                const text = localStorage.getItem('forum_draft_text');
                if (text) document.getElementById('inputTextBody').value = text;

                const mediaCap = localStorage.getItem('forum_draft_mediaCap');
                if (mediaCap) document.getElementById('inputMediaCaption').value = mediaCap;

                const linkUrl = localStorage.getItem('forum_draft_linkUrl');
                if (linkUrl) document.getElementById('inputLinkUrl').value = linkUrl;

                const linkCap = localStorage.getItem('forum_draft_linkCap');
                if (linkCap) document.getElementById('inputLinkCaption').value = linkCap;

                if (draftFound) {
                    // Trigger length calculation
                    judulInput.dispatchEvent(new Event('input'));

                    // Switch to the correct tab where the draft was saved
                    switchTab(format);

                    showDraftStatus('📥 Berhasil memuat draft terakhir!');
                } else {
                    showDraftStatus('⚠️ Tidak ada draft yang tersimpan.');
                }
            }

            function showDraftStatus(message) {
                const status = document.getElementById('draftStatus');
                status.style.display = 'block';
                status.textContent = message;
                setTimeout(() => status.style.display = 'none', 4000);
            }

            // Auto-save draft quietly every 1 min if user changes things
            setInterval(() => {
                if (document.getElementById('inputJudul').value.length > 5 || document.getElementById('inputTextBody').value.length > 5) {
                    saveDraft();
                }
            }, 60000);

        </script>
    @endpush

</x-manajemenmahasiswa::layouts.mahasiswa>