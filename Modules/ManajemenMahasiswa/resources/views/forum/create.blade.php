<x-manajemenmahasiswa::layouts.forum-layout>

    @push('styles')
        <style>
            /* ── Page Title ──────────────────────────────────────────────────── */
            .page-title {
                margin-bottom: 22px;
                display: flex;
                align-items: center;
                gap: 16px;
            }

            .page-title .back-btn {
                background: #fff;
                border: 1px solid #e5e7eb;
                border-radius: 50%;
                width: 40px;
                height: 40px;
                display: flex;
                align-items: center;
                justify-content: center;
                color: #4b5563;
                text-decoration: none;
                transition: background 0.2s;
            }

            .page-title .back-btn:hover {
                background: #f3f4f6;
            }

            .page-title h1 {
                font-size: 26px;
                font-weight: 700;
                color: #111827;
                margin: 0 0 2px;
                letter-spacing: -0.02em;
            }

            .page-title p {
                font-size: 14px;
                color: #6b7280;
                margin: 0;
            }

            .create-post-card {
                background: #ffffff;
                border-radius: 12px;
                padding: 30px;
                border: 1px solid #e5e7eb;
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
                background-color: #4f46e5;
                color: white;
            }

            .btn-post:hover {
                background-color: #4338ca;
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
                border-color: #4f46e5;
                color: #4338ca;
            }

            .section-toggle.active {
                background: #eef2ff;
                border-color: #4f46e5;
                color: #4338ca;
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
                border-color: #4f46e5;
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
        </style>
    @endpush

    <div class="page-title">
        <a href="{{ route('manajemenmahasiswa.forum.index') }}" class="back-btn">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                stroke-linecap="round" stroke-linejoin="round">
                <line x1="19" y1="12" x2="5" y2="12"></line>
                <polyline points="12 19 5 12 12 5"></polyline>
            </svg>
        </a>
        <div>
            <h1>Forum Diskusi</h1>
            <p>Wadah komunikasi mahasiswa & alumni</p>
        </div>
    </div>

    <div class="create-post-card">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h4 class="fw-bold text-dark mb-0">Buat Post</h4>
            @if(isset($drafts) && $drafts->count() > 0)
                <button type="button" class="btn btn-sm rounded-pill fw-bold px-4 text-white shadow-sm"
                    style="background: linear-gradient(135deg, #f59e0b 0%, #ea580c 100%); border: none; transition: transform 0.2s ease, box-shadow 0.2s ease;"
                    onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 4px 12px rgba(234, 88, 12, 0.3)';"
                    onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 2px 4px rgba(0,0,0,0.05)';"
                    data-bs-toggle="modal" data-bs-target="#draftsModal">
                    <i class="bi bi-cloud-arrow-down-fill me-1"></i> Load Draft ({{ $drafts->count() }})
                </button>
            @endif
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
            <input type="hidden" name="draft_id" id="draft_id" value="">

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
                <div class="d-flex flex-wrap gap-3 mt-2 @error('kategori') is-invalid @enderror">
                    @foreach($categories as $key => $label)
                        <div class="form-check form-check-inline m-0">
                            <input class="form-check-input shadow-none" style="cursor: pointer;" type="checkbox"
                                name="kategori[]" id="kategori_{{ $key }}" value="{{ $key }}" {{ in_array($key, old('kategori', [])) ? 'checked' : '' }}>
                            <label class="form-check-label text-dark" style="cursor: pointer; font-size: 14px;"
                                for="kategori_{{ $key }}">{{ $label }}</label>
                        </div>
                    @endforeach
                </div>
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
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" style="margin-right: 6px;"><rect x="3" y="3" width="18" height="18" rx="2" ry="2"/><circle cx="8.5" cy="8.5" r="1.5"/><polyline points="21 15 16 10 5 21"/></svg> Tambah Gambar / Video
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
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" style="margin-right: 6px;"><path d="M10 13a5 5 0 0 0 7.54.54l3-3a5 5 0 0 0-7.07-7.07l-1.72 1.71"/><path d="M14 11a5 5 0 0 0-7.54-.54l-3 3a5 5 0 0 0 7.07 7.07l1.71-1.71"/></svg> Tambah Link
                    <span style="margin-left: auto; font-size: 12px; opacity: 0.6;">▼</span>
                </button>
                <div class="section-content" id="sectionLink">
                    <input type="url" name="link_url" id="inputLinkUrl" class="custom-input"
                        placeholder="https://contoh.com/artikel-menarik" value="{{ old('link_url') }}">
                </div>
            </div>

            {{-- Action Buttons --}}
            <div class="d-flex justify-content-between align-items-center pb-2">
                <div class="d-flex align-items-center gap-2">
                    <span id="draftStatus" class="text-muted"
                        style="font-size: 13px; font-style: italic; display: none;">Menyimpan draf...</span>
                </div>
                <div class="d-flex justify-content-end gap-3 align-items-center">
                    <button type="button" class="btn-action btn-cancel text-decoration-none shadow-sm"
                        onclick="saveDraftManual()">
                        Simpan Draf
                    </button>
                    <a href="{{ route('manajemenmahasiswa.forum.index') }}"
                        class="btn-action btn-cancel text-decoration-none shadow-sm text-center">
                        <span><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg></span> Batal
                    </a>
                    <button type="submit" class="btn-action btn-post shadow-sm px-4">
                        Terbitkan
                    </button>
                </div>
            </div>
        </form>
    </div>

    {{-- Modal Drafts --}}
    @if(isset($drafts) && $drafts->count() > 0)
        <div class="modal fade" id="draftsModal" tabindex="-1" aria-labelledby="draftsModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
                <div class="modal-content border-0 shadow" style="border-radius: 16px;">
                    <div class="modal-header border-0 pb-0">
                        <h5 class="modal-title fw-bold" id="draftsModalLabel">Draf Anda</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="list-group list-group-flush">
                            @foreach($drafts as $draft)
                                <div class="list-group-item list-group-item-action d-flex justify-content-between align-items-center p-3"
                                    style="border-radius: 12px; margin-bottom: 8px; border: 1px solid #e5e7eb; cursor: pointer;">
                                    <div class="flex-grow-1 pe-3"
                                        onclick="loadDraft({{ $draft->id }}, {{ json_encode($draft->judul) }}, {{ json_encode($draft->kategori) }}, {{ json_encode($draft->konten) }})">
                                        <h6 class="mb-1 fw-bold text-dark" style="font-size: 15px;">
                                            {{ $draft->judul ?: '(Tanpa Judul)' }}
                                        </h6>
                                        <p class="mb-1 text-muted"
                                            style="font-size: 13px; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;">
                                            {{ $draft->konten ?: '(Tidak ada konten teks)' }}
                                        </p>
                                        <small class="text-muted" style="font-size: 11px;">
                                            Diperbarui: {{ $draft->updated_at->diffForHumans() }}
                                        </small>
                                    </div>
                                    <div class="ms-1">
                                        <form action="{{ route('manajemenmahasiswa.forum.drafts.destroy', $draft->id) }}"
                                            method="POST" class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit"
                                                class="btn btn-sm btn-light text-danger rounded-circle shadow-sm border border-danger-subtle"
                                                style="width: 32px; height: 32px; display: flex; align-items: center; justify-content: center;"
                                                title="Hapus draf ini">
                                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif

    @push('scripts')
        <script>
            // ---- Toggle Sections ----
            function toggleSection(section) {
                const content = document.getElementById(`section${section.charAt(0).toUpperCase() + section.slice(1)}`);
                const toggle = document.getElementById(`toggle${section.charAt(0).toUpperCase() + section.slice(1)}`);
                content.classList.toggle('open');
                toggle.classList.toggle('active');
            }

            // ---- Draft Auto-Save Logic ----
            let draftTimer;
            const DRAFT_DELAY = 60000; // 1 menit

            const formInputs = document.querySelectorAll('#inputJudul, #inputKonten, input[name="kategori[]"]');

            formInputs.forEach(input => {
                input.addEventListener('input', () => {
                    clearTimeout(draftTimer);
                    document.getElementById('draftStatus').style.display = 'none';
                    draftTimer = setTimeout(saveDraftAJAX, DRAFT_DELAY);
                });
            });

            function saveDraftManual() {
                saveDraftAJAX(true);
            }

            function saveDraftAJAX(isManual = false) {
                const draftStatus = document.getElementById('draftStatus');
                draftStatus.style.display = 'inline';
                draftStatus.textContent = 'Menyimpan draf...';

                const formData = new FormData(document.getElementById('createPostForm'));

                fetch('{{ route("manajemenmahasiswa.forum.drafts.store") }}', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    },
                    body: formData
                })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            document.getElementById('draft_id').value = data.draft_id;
                            draftStatus.textContent = 'Draf tersimpan.';
                            setTimeout(() => { draftStatus.style.display = 'none'; }, 3000);
                            if (isManual) {
                                alert('Draf berhasil disimpan!');
                            }
                        } else {
                            draftStatus.textContent = 'Gagal menyimpan draf.';
                            if (isManual) {
                                alert('Terjadi kesalahan saat menyimpan draf: ' + (data.message || 'Data tidak valid.'));
                            }
                        }
                    })
                    .catch(error => {
                        console.error('Error saving draft:', error);
                        draftStatus.textContent = 'Gagal menyimpan draf.';
                        if (isManual) {
                            alert('Terjadi kesalahan saat menyimpan draf.');
                        }
                    });
            }

            function loadDraft(id, judul, kategoriArr, konten) {
                document.getElementById('draft_id').value = id;
                document.getElementById('inputJudul').value = judul || '';
                if (judul) {
                    document.getElementById('judulCount').textContent = judul.length;
                } else {
                    document.getElementById('judulCount').textContent = '0';
                }

                document.getElementById('inputKonten').value = konten || '';

                // Reset checkboxes
                const checkboxes = document.querySelectorAll('input[name="kategori[]"]');
                checkboxes.forEach(cb => cb.checked = false);

                if (kategoriArr && Array.isArray(kategoriArr)) {
                    kategoriArr.forEach(cat => {
                        const cb = document.getElementById('kategori_' + cat);
                        if (cb) cb.checked = true;
                    });
                }

                // Hide modal via Bootstrap API if available
                if (typeof bootstrap !== 'undefined') {
                    const modalEl = document.getElementById('draftsModal');
                    const modal = bootstrap.Modal.getInstance(modalEl);
                    if (modal) modal.hide();
                }
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
            // ---- Prevent Double Submit ----
            const form = document.getElementById('createPostForm');
            const submitBtn = form.querySelector('button[type="submit"]');
            form.addEventListener('submit', function () {
                submitBtn.disabled = true;
                submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span> Loading...';
            });
        </script>
    @endpush

</x-manajemenmahasiswa::layouts.forum-layout>