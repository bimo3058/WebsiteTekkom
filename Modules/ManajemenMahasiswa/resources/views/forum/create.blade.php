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

            /* Form Elementss */
            .form-label {
                font-weight: 600;
                color: #1f2937;
                font-size: 14px;
                margin-bottom: 8px;
            }

            .checkbox-card-group {
                display: flex;
                flex-wrap: wrap;
                gap: 10px;
            }
            .checkbox-card {
                position: relative;
                display: flex;
                align-items: center;
                gap: 8px;
                padding: 10px 16px;
                border: 1.5px solid #e5e7eb;
                border-radius: 10px;
                background: #fff;
                cursor: pointer;
                transition: all 0.2s;
                font-size: 13px;
                font-weight: 500;
                color: #374151;
                user-select: none;
            }
            .checkbox-card:hover {
                border-color: #a5b4fc;
                background: #f5f3ff;
            }
            .checkbox-card input[type="checkbox"] {
                width: 16px;
                height: 16px;
                accent-color: #4f46e5;
                cursor: pointer;
                flex-shrink: 0;
            }
            .checkbox-card.checked {
                border-color: #4f46e5;
                background: #eef2ff;
                color: #4338ca;
                font-weight: 600;
            }
            .checkbox-hint {
                font-size: 11px;
                color: #9ca3af;
                font-weight: 400;
                margin-top: 6px;
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

            .btn-draft {
                background-color: #fff;
                color: #4f46e5;
                border: 1.5px solid #4f46e5 !important;
            }

            .btn-draft:hover {
                background-color: #eef2ff;
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
                border-radius: 12px;
                overflow: hidden;
                border: 1px solid #e5e7eb;
                background: #000;
                display: flex;
                flex-direction: column;
            }

            .media-preview-item .media-thumb {
                position: relative;
                width: 100%;
                aspect-ratio: 1;
                overflow: hidden;
                flex-shrink: 0;
            }

            .media-preview-item img,
            .media-preview-item video {
                width: 100%;
                height: 100%;
                object-fit: cover;
                display: block;
            }

            .media-preview-item .remove-media {
                position: absolute;
                top: 6px;
                right: 6px;
                width: 26px;
                height: 26px;
                border-radius: 50%;
                background: #ef4444;
                color: white;
                border: 2px solid #fff;
                font-size: 13px;
                display: flex;
                align-items: center;
                justify-content: center;
                cursor: pointer;
                transition: transform 0.15s;
                z-index: 2;
                box-shadow: 0 1px 4px rgba(0,0,0,0.25);
            }

            .media-preview-item .remove-media:hover {
                transform: scale(1.12);
            }

            .media-preview-item .file-info {
                padding: 8px 10px;
                background: #fff;
                border-top: 1px solid #f3f4f6;
            }

            .media-preview-item .file-name {
                color: #111827;
                font-size: 12px;
                font-weight: 600;
                white-space: nowrap;
                overflow: hidden;
                text-overflow: ellipsis;
                line-height: 1.4;
            }

            .media-preview-item .file-size {
                color: #9ca3af;
                font-size: 11px;
                font-weight: 400;
                margin-top: 1px;
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
                <div class="checkbox-card-group mt-2 @error('kategori') is-invalid @enderror" id="kategoriGroup">
                    @foreach($categories as $key => $label)
                        <label class="checkbox-card {{ in_array($key, old('kategori', [])) ? 'checked' : '' }}"
                               id="kategoriCard_{{ $key }}">
                            <input type="checkbox" name="kategori[]"
                                   id="kategori_{{ $key }}" value="{{ $key }}"
                                   {{ in_array($key, old('kategori', [])) ? 'checked' : '' }}>
                            {{ $label }}
                        </label>
                    @endforeach
                </div>
                <div class="checkbox-hint">Pilih satu atau lebih kategori</div>
                @error('kategori')
                    <div class="invalid-feedback d-block" style="font-size:12px;">{{ $message }}</div>
                @enderror
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
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"
                        stroke-linecap="round" stroke-linejoin="round" style="margin-right: 6px;">
                        <rect x="3" y="3" width="18" height="18" rx="2" ry="2" />
                        <circle cx="8.5" cy="8.5" r="1.5" />
                        <polyline points="21 15 16 10 5 21" />
                    </svg> Tambah Gambar / Video
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
            <div class="mb-3">
                <button type="button" class="section-toggle" id="toggleLink" onclick="toggleSection('link')">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"
                        stroke-linecap="round" stroke-linejoin="round" style="margin-right: 6px;">
                        <path d="M10 13a5 5 0 0 0 7.54.54l3-3a5 5 0 0 0-7.07-7.07l-1.72 1.71" />
                        <path d="M14 11a5 5 0 0 0-7.54-.54l-3 3a5 5 0 0 0 7.07 7.07l1.71-1.71" />
                    </svg> Tambah Link
                    <span style="margin-left: auto; font-size: 12px; opacity: 0.6;">▼</span>
                </button>
                <div class="section-content" id="sectionLink">
                    <input type="url" name="link_url" id="inputLinkUrl" class="custom-input"
                        placeholder="https://contoh.com/artikel-menarik" value="{{ old('link_url') }}">
                </div>
            </div>

            {{-- Poll (Collapsible) --}}
            <div class="mb-5">
                <button type="button" class="section-toggle" id="togglePoll" onclick="togglePollSection()">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"
                        stroke-linecap="round" stroke-linejoin="round" style="margin-right: 6px;">
                        <rect x="3" y="3" width="18" height="18" rx="2" />
                        <path d="M9 9h6M9 12h6M9 15h4" />
                    </svg>
                    Tambah Poll
                    <span id="pollToggleChevron" style="margin-left: auto; font-size: 12px; opacity: 0.6;">▼</span>
                </button>
                <div class="section-content" id="sectionPoll">
                    <input type="hidden" name="has_poll" id="hasPollInput" value="0">
                    <style>
                        .poll-option-row {
                            display: flex;
                            align-items: center;
                            gap: 8px;
                            margin-bottom: 8px;
                        }

                        .poll-option-input {
                            flex: 1;
                            padding: 9px 14px;
                            border: 1.5px solid #e5e7eb;
                            border-radius: 10px;
                            font-size: 13px;
                            font-weight: 500;
                            outline: none;
                            transition: border-color 0.2s;
                        }

                        .poll-option-input:focus {
                            border-color: #4f46e5;
                            box-shadow: 0 0 0 3px rgba(79, 70, 229, 0.1);
                        }

                        .poll-option-remove {
                            width: 30px;
                            height: 30px;
                            border-radius: 50%;
                            border: none;
                            background: #fee2e2;
                            color: #dc2626;
                            font-size: 16px;
                            cursor: pointer;
                            display: flex;
                            align-items: center;
                            justify-content: center;
                            flex-shrink: 0;
                            transition: background 0.2s;
                        }

                        .poll-option-remove:hover {
                            background: #fca5a5;
                        }

                        .poll-add-option {
                            font-size: 13px;
                            font-weight: 600;
                            color: #4f46e5;
                            background: #eef2ff;
                            border: 1.5px dashed #a5b4fc;
                            border-radius: 10px;
                            padding: 8px 16px;
                            cursor: pointer;
                            width: 100%;
                            text-align: center;
                            transition: all 0.2s;
                            margin-top: 4px;
                        }

                        .poll-add-option:hover {
                            background: #e0e7ff;
                        }

                        .poll-duration-label {
                            font-size: 12px;
                            font-weight: 600;
                            color: #6b7280;
                            margin-bottom: 4px;
                            margin-top: 12px;
                            display: block;
                        }

                        .poll-duration-input {
                            padding: 8px 12px;
                            border: 1.5px solid #e5e7eb;
                            border-radius: 10px;
                            font-size: 13px;
                            outline: none;
                            transition: border-color 0.2s;
                        }

                        .poll-duration-input:focus {
                            border-color: #4f46e5;
                        }
                    </style>
                    <div id="pollOptionsContainer">
                        <div class="poll-option-row">
                            <input type="text" name="poll_options[]" class="poll-option-input" placeholder="Opsi 1"
                                maxlength="150">
                            <button type="button" class="poll-option-remove" onclick="removePollOption(this)"
                                style="visibility:hidden;">×</button>
                        </div>
                        <div class="poll-option-row">
                            <input type="text" name="poll_options[]" class="poll-option-input" placeholder="Opsi 2"
                                maxlength="150">
                            <button type="button" class="poll-option-remove" onclick="removePollOption(this)"
                                style="visibility:hidden;">×</button>
                        </div>
                    </div>
                    <button type="button" class="poll-add-option" id="btnAddPollOption" onclick="addPollOption()">
                        + Tambah Opsi <span id="pollOptionCount" style="color:#9ca3af;">(2/6)</span>
                    </button>
                    <span class="poll-duration-label">Batas Waktu Poll (opsional)</span>
                    <input type="datetime-local" name="poll_expires_at" class="poll-duration-input" id="pollExpiresAt"
                        min="{{ now()->addHours(1)->format('Y-m-d\TH:i') }}">
                </div>
            </div>

            {{-- Action Buttons --}}
            <div class="d-flex justify-content-between align-items-center pb-2">
                <div class="d-flex align-items-center gap-2">
                    <span id="draftStatus" class="text-muted"
                        style="font-size: 13px; font-style: italic; display: none;">Menyimpan draf...</span>
                </div>
                <div class="d-flex justify-content-end gap-3 align-items-center">
                    <button type="button" class="btn-action btn-draft text-decoration-none"
                        onclick="saveDraftManual()">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                             stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"/>
                            <polyline points="17 21 17 13 7 13 7 21"/>
                            <polyline points="7 3 7 8 15 8"/>
                        </svg>
                        Simpan Draft
                    </button>
                    <a href="{{ route('manajemenmahasiswa.forum.index') }}"
                        class="btn-action btn-cancel text-decoration-none shadow-sm text-center">
                        <span><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                <line x1="18" y1="6" x2="6" y2="18" />
                                <line x1="6" y1="6" x2="18" y2="18" />
                            </svg></span> Batal
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
                                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none"
                                                    stroke="currentColor" stroke-width="2.5" stroke-linecap="round"
                                                    stroke-linejoin="round">
                                                    <line x1="18" y1="6" x2="6" y2="18" />
                                                    <line x1="6" y1="6" x2="18" y2="18" />
                                                </svg>
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
            // ---- Checkbox Cards (Kategori) ----
            document.querySelectorAll('#kategoriGroup .checkbox-card input[type="checkbox"]').forEach(function (cb) {
                cb.addEventListener('change', function () {
                    this.closest('.checkbox-card').classList.toggle('checked', this.checked);
                });
            });

            // ---- Toggle Sections ----
            function toggleSection(section) {
                const content = document.getElementById(`section${section.charAt(0).toUpperCase() + section.slice(1)}`);
                const toggle = document.getElementById(`toggle${section.charAt(0).toUpperCase() + section.slice(1)}`);
                content.classList.toggle('open');
                toggle.classList.toggle('active');
            }

            // ---- Poll Builder ----
            let pollOpen = false;
            const MAX_POLL_OPTIONS = 6;

            function togglePollSection() {
                pollOpen = !pollOpen;
                const content = document.getElementById('sectionPoll');
                const chevron = document.getElementById('pollToggleChevron');
                const btn = document.getElementById('togglePoll');
                const input = document.getElementById('hasPollInput');

                content.classList.toggle('open', pollOpen);
                btn.classList.toggle('active', pollOpen);
                chevron.textContent = pollOpen ? '▲' : '▼';
                input.value = pollOpen ? '1' : '0';
            }

            function updatePollOptionCount() {
                const rows = document.querySelectorAll('#pollOptionsContainer .poll-option-row');
                const count = rows.length;
                document.getElementById('pollOptionCount').textContent = `(${count}/${MAX_POLL_OPTIONS})`;
                document.getElementById('btnAddPollOption').style.display = count >= MAX_POLL_OPTIONS ? 'none' : '';

                // Tampilkan/sembunyikan tombol hapus — min 2 opsi
                rows.forEach((row, i) => {
                    const btn = row.querySelector('.poll-option-remove');
                    btn.style.visibility = count > 2 ? 'visible' : 'hidden';
                });
            }

            function addPollOption() {
                const container = document.getElementById('pollOptionsContainer');
                const count = container.querySelectorAll('.poll-option-row').length;
                if (count >= MAX_POLL_OPTIONS) return;

                const row = document.createElement('div');
                row.className = 'poll-option-row';
                row.innerHTML = `
                        <input type="text" name="poll_options[]" class="poll-option-input"
                               placeholder="Opsi ${count + 1}" maxlength="150">
                        <button type="button" class="poll-option-remove" onclick="removePollOption(this)">×</button>`;
                container.appendChild(row);
                updatePollOptionCount();
                row.querySelector('input').focus();
            }

            function removePollOption(btn) {
                const rows = document.querySelectorAll('#pollOptionsContainer .poll-option-row');
                if (rows.length <= 2) return;
                btn.closest('.poll-option-row').remove();
                // Re-number placeholders
                document.querySelectorAll('#pollOptionsContainer .poll-option-input').forEach((inp, i) => {
                    if (!inp.value) inp.placeholder = `Opsi ${i + 1}`;
                });
                updatePollOptionCount();
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

                    // Thumb wrapper (image/video + remove button)
                    const thumb = document.createElement('div');
                    thumb.className = 'media-thumb';

                    const removeBtn = document.createElement('button');
                    removeBtn.type = 'button';
                    removeBtn.className = 'remove-media';
                    removeBtn.innerHTML = '✕';
                    removeBtn.onclick = () => removeMediaFile(idx);
                    thumb.appendChild(removeBtn);

                    if (file.type.startsWith('image/')) {
                        const img = document.createElement('img');
                        img.src = URL.createObjectURL(file);
                        img.onload = () => URL.revokeObjectURL(img.src);
                        thumb.appendChild(img);
                    } else if (file.type.startsWith('video/')) {
                        const video = document.createElement('video');
                        video.src = URL.createObjectURL(file);
                        video.muted = true;
                        video.onloadeddata = () => { video.currentTime = 1; };
                        thumb.appendChild(video);
                    }

                    item.appendChild(thumb);

                    // File info strip below the image
                    const fileInfo = document.createElement('div');
                    fileInfo.className = 'file-info';

                    const nameLabel = document.createElement('div');
                    nameLabel.className = 'file-name';
                    nameLabel.textContent = file.name;
                    nameLabel.title = file.name;

                    const sizeLabel = document.createElement('div');
                    sizeLabel.className = 'file-size';
                    const kb = file.size / 1024;
                    sizeLabel.textContent = kb >= 1024
                        ? (kb / 1024).toFixed(1) + ' MB'
                        : kb.toFixed(1) + ' KB';

                    fileInfo.appendChild(nameLabel);
                    fileInfo.appendChild(sizeLabel);
                    item.appendChild(fileInfo);

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