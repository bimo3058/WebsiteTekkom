<x-manajemenmahasiswa::layouts.forum-layout>

    @push('styles')
        <style>
            .create-post-card {
                background: #fff;
                border: 1px solid #DFE1E7;
                border-radius: 12px;
                padding: 24px;
                box-shadow: 0px 1px 2px 0px rgba(228,229,231,0.5);
                margin-bottom: 20px;
            }

            /* Form Elementss */
            .form-label {
                font-weight: 600;
                color: #0D0D12;
                font-size: 13px;
                margin-bottom: 6px;
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
                border: 1.5px solid #DFE1E7;
                border-radius: 10px;
                background: #fff;
                cursor: pointer;
                transition: all 0.2s;
                font-size: 13px;
                font-weight: 500;
                color: #353849;
                user-select: none;
            }
            .checkbox-card:hover {
                border-color: #5C78B8;
                background: rgba(11,38,110,0.04);
                color: #0B266E;
            }
            .checkbox-card input[type="checkbox"] {
                width: 16px;
                height: 16px;
                accent-color: #0B266E;
                cursor: pointer;
                flex-shrink: 0;
            }
            .checkbox-card.checked {
                border-color: #0B266E;
                background: rgba(11,38,110,0.08);
                color: #0B266E;
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
                background-color: #fff;
                border: 1px solid #DFE1E7;
                border-radius: 8px;
                padding: 10px 14px;
                font-size: 13px;
                width: 100%;
                transition: all 0.2s;
            }

            .custom-input:focus,
            .custom-select:focus,
            .custom-textarea:focus {
                background-color: #ffffff;
                border-color: #0B266E;
                box-shadow: 0 0 0 3px rgba(11,38,110,0.1);
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
                padding: 8px 20px;
                font-weight: 600;
                font-size: 13px;
                border: none;
                display: inline-flex;
                align-items: center;
                gap: 8px;
                transition: all 0.2s;
            }

            .btn-post {
                background-color: #0B266E;
                color: white;
            }

            .btn-post:hover {
                background-color: #091958;
            }

            .btn-cancel {
                background: #fff;
                color: #374151;
                border: 1px solid #DFE1E7;
            }

            .btn-cancel:hover {
                background: rgba(11,38,110,0.04);
                border-color: #5C78B8;
                color: #0B266E;
            }

            .btn-draft {
                background-color: #fff;
                color: #0B266E;
                border: 1.5px solid #0B266E !important;
            }

            .btn-draft:hover {
                background-color: rgba(11,38,110,0.06);
            }

            /* Collapsible Section */
            .section-toggle {
                display: flex;
                align-items: center;
                gap: 8px;
                padding: 8px 14px;
                background: #fff;
                border: 1px solid #DFE1E7;
                border-radius: 8px;
                cursor: pointer;
                font-size: 13px;
                font-weight: 600;
                color: #353849;
                transition: all 0.2s;
                width: 100%;
                text-align: left;
            }

            .section-toggle:hover {
                background: rgba(11,38,110,0.04);
                border-color: #0B266E;
                color: #0B266E;
            }

            .section-toggle.active {
                background: rgba(11,38,110,0.04);
                border-color: #0B266E;
                color: #0B266E;
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
                border: 2px dashed #DFE1E7;
                border-radius: 12px;
                padding: 32px 20px;
                text-align: center;
                cursor: pointer;
                transition: all 0.3s ease;
                background: #fff;
                position: relative;
            }

            .media-dropzone:hover,
            .media-dropzone.dragover {
                border-color: #0B266E;
                background: rgba(11,38,110,0.04);
            }

            .media-dropzone .dropzone-text {
                font-size: 13px;
                font-weight: 600;
                color: #353849;
                margin-bottom: 4px;
            }

            .media-dropzone .dropzone-hint {
                font-size: 12px;
                color: #808897;
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
                border: 1px solid #DFE1E7;
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
                font-size: 12px;
                font-weight: 600;
                padding: 3px 10px;
                border-radius: 8px;
                display: inline-block;
                margin-top: 8px;
            }

            .media-counter.ok   { background: #DDF2EE; color: #287F6E; }
            .media-counter.warn { background: #fef3c7; color: #b45309; }
            .media-counter.full { background: #fee2e2; color: #dc2626; }

            /* Cover drag-reorder badges */
            .cover-badge-pill {
                position: absolute; top: 5px; left: 5px;
                background: #0B266E; color: #fff;
                font-size: 9px; font-weight: 800; padding: 2px 8px;
                border-radius: 20px; letter-spacing: 0.05em; text-transform: uppercase;
                pointer-events: none;
            }
            .cover-order-pill {
                position: absolute; top: 5px; left: 5px;
                background: rgba(0,0,0,0.5); color: #fff;
                font-size: 11px; font-weight: 700; padding: 1px 7px; border-radius: 20px;
                pointer-events: none;
            }
            .media-preview-item { cursor: grab; }
            .media-preview-item.dragging  { opacity: 0.4; box-shadow: 0 6px 16px rgba(0,0,0,0.18); }
            .media-preview-item.drag-over { outline: 2px solid #5C78B8; outline-offset: 2px; }
            .media-preview-item:first-child { border-color: #0B266E; }
            .cover-hint-text { font-size: 12px; color: #6b7280; margin: 8px 0 0; display: none; }

            /* Halaman ini menggambar kotak kontennya sendiri (.dash-wrap/.dash-box),
               jadi kotak bawaan .main-wrapper dari layout dimatikan. */
            .main-wrapper {
                background: transparent !important;
                border: none !important;
                box-shadow: none !important;
                margin: 0 !important;
                padding: 0 !important;
                overflow: visible !important;
            }

            .sitkom-content { padding: 0 !important; display: flex; flex-direction: column; flex: 1; overflow: hidden; }
            .dash-wrap { display: flex; flex-direction: column; height: calc(100vh - 60px); padding: 10px; box-sizing: border-box; }
            .dash-box { display: flex; flex-direction: column; flex: 1; min-height: 0; background: #fff; border: 1px solid #DFE1E7; border-radius: 12px; box-shadow: 0 1px 3px rgba(0, 0, 0, 0.06); overflow: hidden; width: 100%; box-sizing: border-box; }
            .dash-box-header { background: #fff; border-bottom: 1px solid #DFE1E7; flex-shrink: 0; width: 100%; box-sizing: border-box; padding: 16px 24px; }
            .dash-box-body { flex: 1; overflow-y: auto; padding: 20px 24px; }
            .dash-box-body::-webkit-scrollbar { width: 6px; }
            .dash-box-body::-webkit-scrollbar-thumb { background: #C1C7CF; border-radius: 10px; }
            @media (max-width: 767px) {
                .sitkom-content { padding: 8px 8px 80px !important; display: block !important; overflow: visible !important; }
                .dash-wrap { height: auto !important; padding: 0; }
                .dash-box { flex: none !important; overflow: visible !important; border-radius: 10px; }
                .dash-box-header { padding: 12px 14px; position: sticky; top: 52px; z-index: 10; }
                .dash-box-body { overflow-y: visible !important; flex: none !important; padding: 14px; }
            }
            .create-post-card { background: transparent !important; border: none !important; box-shadow: none !important; padding: 0 !important; margin: 0 !important; }
        </style>
    @endpush

    <div class="dash-wrap">
    <div class="dash-box">
    <div class="dash-box-header">
        <x-manajemenmahasiswa::ui.page-header
            title="Buat Post Baru"
            subtitle="Bagikan sesuatu ke forum diskusi">
            <x-slot:leading>
                <a href="{{ route('manajemenmahasiswa.forum.index') }}" class="mk-btn mk-btn--secondary mk-btn--sm">
                    <x-manajemenmahasiswa::ui.icon name="arrow-narrow-left" size="20" />
                </a>
            </x-slot:leading>
            <x-slot:actions>
                @if(isset($drafts) && $drafts->count() > 0)
                    <button type="button" class="mk-btn mk-btn--secondary" data-bs-toggle="modal" data-bs-target="#draftsModal">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z" />
                        </svg>
                        Load Draft ({{ $drafts->count() }})
                    </button>
                @endif
            </x-slot:actions>
        </x-manajemenmahasiswa::ui.page-header>
    </div>
    <div class="dash-box-body">
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

        <form action="{{ route('manajemenmahasiswa.forum.store') }}" method="POST" id="createPostForm"
            enctype="multipart/form-data">
            @csrf
            <input type="hidden" name="draft_id" id="draft_id" value="">
            <div id="draftMediaInputs"></div>

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
                    <x-manajemenmahasiswa::ui.icon name="image-02" size="16" /> Tambah Gambar
                    <span style="margin-left: auto; font-size: 12px; opacity: 0.6;">▼</span>
                </button>
                <div class="section-content" id="sectionMedia">
                    {{-- Saved draft media (already uploaded to server) --}}
                    <div id="savedDraftMediaSection" style="display:none; margin-bottom:12px;">
                        <div style="font-size:12px; font-weight:600; color:#0B266E; margin-bottom:8px;">Gambar tersimpan di draf:</div>
                        <div id="savedDraftMediaGrid" class="media-preview-grid"></div>
                    </div>
                    <div class="media-dropzone" id="mediaDropzone">
                        <input type="file" name="media_files[]" id="mediaFileInput" multiple
                            accept="image/jpeg,image/png,image/webp">
                        <div class="dropzone-text">Click atau drag file ke sini</div>
                        <div class="dropzone-hint">JPG, PNG, WEBP • Maks 10MB per file • Maks 5 file
                        </div>
                    </div>
                    <div id="mediaCounter"></div>
                    <div class="media-preview-grid" id="mediaPreviewGrid"></div>
                    <p class="cover-hint-text" id="coverHintText">
                        Seret gambar untuk mengubah urutan — gambar paling depan dipakai sebagai <strong>cover</strong>.
                    </p>
                </div>
            </div>

            {{-- Link (Collapsible) --}}
            <div class="mb-3">
                <button type="button" class="section-toggle" id="toggleLink" onclick="toggleSection('link')">
                    <x-manajemenmahasiswa::ui.icon name="link-01" size="16" /> Tambah Link
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
                    <x-manajemenmahasiswa::ui.icon name="bar-chart-11" size="16" />
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
                            border: 1.5px solid #DFE1E7;
                            border-radius: 10px;
                            font-size: 13px;
                            font-weight: 500;
                            outline: none;
                            transition: border-color 0.2s;
                        }

                        .poll-option-input:focus {
                            border-color: #0B266E;
                            box-shadow: 0 0 0 3px rgba(11,38,110,0.1);
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
                            color: #0B266E;
                            background: rgba(11,38,110,0.06);
                            border: 1.5px dashed #5C78B8;
                            border-radius: 10px;
                            padding: 8px 16px;
                            cursor: pointer;
                            width: 100%;
                            text-align: center;
                            transition: all 0.2s;
                            margin-top: 4px;
                        }

                        .poll-add-option:hover {
                            background: rgba(11,38,110,0.1);
                        }

                        .poll-duration-label {
                            font-size: 12px;
                            font-weight: 600;
                            color: #666D80;
                            margin-bottom: 4px;
                            margin-top: 12px;
                            display: block;
                        }

                        .poll-duration-input {
                            padding: 8px 12px;
                            border: 1.5px solid #DFE1E7;
                            border-radius: 10px;
                            font-size: 13px;
                            outline: none;
                            transition: border-color 0.2s;
                        }

                        .poll-duration-input:focus {
                            border-color: #0B266E;
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
                        <x-manajemenmahasiswa::ui.icon name="download-01" size="16" />
                        Simpan Draft
                    </button>
                    <a href="{{ route('manajemenmahasiswa.forum.index') }}"
                        class="btn-action btn-cancel text-decoration-none shadow-sm text-center">
                        <x-manajemenmahasiswa::ui.icon name="minus-circle" size="16" /> Batal
                    </a>
                    <button type="submit" class="mk-btn mk-btn--primary">
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
                                @php
                                    $dm           = $draftMedia[$draft->id] ?? [];
                                    $draftAttrs   = $draft->getAttributes();
                                    $draftLinkUrl = $draftAttrs['link_url'] ?? null;
                                    $draftPollRaw = $draftAttrs['poll_data'] ?? null;
                                    $draftPollData = is_string($draftPollRaw) ? json_decode($draftPollRaw, true) : $draftPollRaw;
                                    $draftHasLink = !empty($draftLinkUrl);
                                    $draftHasPoll = !empty($draftPollData);
                                @endphp
                                <div class="list-group-item list-group-item-action d-flex justify-content-between align-items-center p-3"
                                    style="border-radius: 12px; margin-bottom: 8px; border: 1px solid #DFE1E7; cursor: pointer;">
                                    <div class="flex-grow-1 pe-3"
                                        onclick="loadDraft({{ $draft->id }}, {{ json_encode($draft->judul) }}, {{ json_encode($draft->kategori) }}, {{ json_encode($draft->konten) }}, {{ json_encode($dm) }}, {{ json_encode($draftLinkUrl) }}, {{ json_encode($draftPollData) }})">
                                        <h6 class="mb-1 fw-bold text-dark" style="font-size: 15px;">
                                            {{ $draft->judul ?: '(Tanpa Judul)' }}
                                        </h6>
                                        <p class="mb-1 text-muted"
                                            style="font-size: 13px; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;">
                                            {{ $draft->konten ?: '(Tidak ada konten teks)' }}
                                        </p>
                                        @if(!empty($dm) || $draftHasLink || $draftHasPoll)
                                            <div style="display:flex; gap:4px; margin-top:6px; flex-wrap:wrap; align-items:center;">
                                                @foreach(array_slice($dm, 0, 3) as $dmItem)
                                                    @if(($dmItem['type'] ?? '') === 'image')
                                                        <img src="{{ $dmItem['url'] }}" style="width:36px;height:36px;object-fit:cover;border-radius:6px;border:1px solid #DFE1E7;">
                                                    @else
                                                        <div style="width:36px;height:36px;background:#1f2937;border-radius:6px;display:flex;align-items:center;justify-content:center;font-size:9px;color:#fff;font-weight:700;">VID</div>
                                                    @endif
                                                @endforeach
                                                @if(count($dm) > 3)
                                                    <div style="width:36px;height:36px;background:#f3f4f6;border-radius:6px;display:flex;align-items:center;justify-content:center;font-size:11px;color:#6b7280;font-weight:600;">+{{ count($dm) - 3 }}</div>
                                                @endif
                                                @if($draftHasLink)
                                                    <span style="font-size:10px;padding:2px 7px;border-radius:20px;background:#eff6ff;color:#1d4ed8;font-weight:700;border:1px solid #bfdbfe;display:inline-flex;align-items:center;gap:3px;">
                                                        <svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M10 13a5 5 0 0 0 7.54.54l3-3a5 5 0 0 0-7.07-7.07l-1.72 1.71"/><path d="M14 11a5 5 0 0 0-7.54-.54l-3 3a5 5 0 0 0 7.07 7.07l1.71-1.71"/></svg>
                                                        Link
                                                    </span>
                                                @endif
                                                @if($draftHasPoll)
                                                    <span style="font-size:10px;padding:2px 7px;border-radius:20px;background:#faf5ff;color:#7c3aed;font-weight:700;border:1px solid #ddd6fe;display:inline-flex;align-items:center;gap:3px;">
                                                        <svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="20" x2="18" y2="10"/><line x1="12" y1="20" x2="12" y2="4"/><line x1="6" y1="20" x2="6" y2="14"/></svg>
                                                        Poll
                                                    </span>
                                                @endif
                                            </div>
                                        @endif
                                        <small class="text-muted" style="font-size: 11px; display:block; margin-top:4px;">
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
                                                <x-manajemenmahasiswa::ui.icon name="minus-circle" size="14" />
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

    </div>{{-- /dash-box-body --}}
    </div>{{-- /dash-box --}}
    </div>{{-- /dash-wrap --}}

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
                            // If server uploaded media, show them as saved draft media and clear new file picks
                            if (data.media && data.media.length > 0) {
                                renderDraftMedia(data.media);
                                selectedFiles = [];
                                syncFileInput();
                                renderPreviews();
                            }
                            if (isManual) {
                                mkNotify({ title: 'Draf Tersimpan', message: 'Draf berhasil disimpan!', variant: 'success' });
                            }
                        } else {
                            draftStatus.textContent = 'Gagal menyimpan draf.';
                            if (isManual) {
                                mkNotify({ title: 'Gagal Menyimpan Draf', message: data.message || 'Data tidak valid.', variant: 'danger' });
                            }
                        }
                    })
                    .catch(error => {
                        console.error('Error saving draft:', error);
                        draftStatus.textContent = 'Gagal menyimpan draf.';
                        if (isManual) {
                            mkNotify({ title: 'Gagal Menyimpan Draf', message: 'Terjadi kesalahan saat menyimpan draf.', variant: 'danger' });
                        }
                    });
            }

            function loadDraft(id, judul, kategoriArr, konten, mediaArr, linkUrl, pollData) {
                document.getElementById('draft_id').value = id;
                document.getElementById('inputJudul').value = judul || '';
                document.getElementById('judulCount').textContent = judul ? judul.length : '0';
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

                // Restore link URL
                const linkInput = document.getElementById('inputLinkUrl');
                if (linkInput) {
                    linkInput.value = linkUrl || '';
                    if (linkUrl) {
                        document.getElementById('sectionLink').classList.add('open');
                        document.getElementById('toggleLink').classList.add('active');
                    }
                }

                // Restore poll data
                if (pollData && pollData.options && pollData.options.length >= 2) {
                    // Open poll section
                    if (!pollOpen) togglePollSection();

                    // Clear existing options and rebuild
                    const container = document.getElementById('pollOptionsContainer');
                    container.innerHTML = '';
                    pollData.options.forEach((opt, i) => {
                        const row = document.createElement('div');
                        row.className = 'poll-option-row';
                        row.innerHTML = `
                            <input type="text" name="poll_options[]" class="poll-option-input"
                                   placeholder="Opsi ${i + 1}" maxlength="150" value="${opt.replace(/"/g, '&quot;')}">
                            <button type="button" class="poll-option-remove" onclick="removePollOption(this)" style="visibility:hidden;">×</button>`;
                        container.appendChild(row);
                    });
                    updatePollOptionCount();

                    // Restore expiry
                    const expiresInput = document.getElementById('pollExpiresAt');
                    if (expiresInput && pollData.expires_at) {
                        expiresInput.value = pollData.expires_at;
                    }
                } else if (pollOpen) {
                    // Close poll section if no poll data
                    togglePollSection();
                }

                // Reset new file selection and load saved draft media
                selectedFiles = [];
                syncFileInput();
                renderPreviews();
                renderDraftMedia(mediaArr || []);

                // Hide modal via Bootstrap API if available
                if (typeof bootstrap !== 'undefined') {
                    const modalEl = document.getElementById('draftsModal');
                    const modal = bootstrap.Modal.getInstance(modalEl);
                    if (modal) modal.hide();
                }
            }

            function renderDraftMedia(mediaArr) {
                savedDraftMedia = mediaArr || [];
                const section    = document.getElementById('savedDraftMediaSection');
                const grid       = document.getElementById('savedDraftMediaGrid');
                const inputsDiv  = document.getElementById('draftMediaInputs');

                inputsDiv.innerHTML = '';
                grid.innerHTML = '';

                if (!savedDraftMedia.length) {
                    section.style.display = 'none';
                    updateCounter();
                    return;
                }

                section.style.display = '';
                document.getElementById('sectionMedia').classList.add('open');
                document.getElementById('toggleMedia').classList.add('active');

                savedDraftMedia.forEach((media, idx) => {
                    // Hidden input carries the RepoMulmed ID to the server
                    const input = document.createElement('input');
                    input.type = 'hidden';
                    input.name = 'draft_media_ids[]';
                    input.value = media.id;
                    inputsDiv.appendChild(input);

                    const item = document.createElement('div');
                    item.className = 'media-preview-item';

                    const badge = document.createElement('span');
                    badge.className = idx === 0 ? 'cover-badge-pill' : 'cover-order-pill';
                    badge.textContent = idx === 0 ? 'Cover' : idx + 1;

                    const thumb = document.createElement('div');
                    thumb.className = 'media-thumb';

                    const removeBtn = document.createElement('button');
                    removeBtn.type = 'button';
                    removeBtn.className = 'remove-media';
                    removeBtn.innerHTML = '✕';
                    removeBtn.onclick = (e) => { e.stopPropagation(); removeDraftMedia(idx); };

                    let mediaEl;
                    if (media.type === 'image') {
                        mediaEl = document.createElement('img');
                        mediaEl.src = media.url;
                        mediaEl.alt = media.name;
                    } else {
                        mediaEl = document.createElement('video');
                        mediaEl.src = media.url;
                        mediaEl.muted = true;
                    }

                    thumb.appendChild(removeBtn);
                    thumb.appendChild(badge);
                    thumb.appendChild(mediaEl);
                    item.appendChild(thumb);

                    const fileInfo = document.createElement('div');
                    fileInfo.className = 'file-info';
                    const nameLabel = document.createElement('div');
                    nameLabel.className = 'file-name';
                    nameLabel.textContent = media.name;
                    nameLabel.title = media.name;
                    const savedLabel = document.createElement('div');
                    savedLabel.className = 'file-size';
                    savedLabel.textContent = 'Tersimpan di draf';
                    fileInfo.appendChild(nameLabel);
                    fileInfo.appendChild(savedLabel);
                    item.appendChild(fileInfo);

                    grid.appendChild(item);
                });

                updateCounter();
            }

            function removeDraftMedia(idx) {
                savedDraftMedia.splice(idx, 1);
                renderDraftMedia(savedDraftMedia);
            }

            // ---- Character Counter ----
            const judulInput = document.getElementById('inputJudul');
            const judulCount = document.getElementById('judulCount');
            if (judulInput.value) judulCount.textContent = judulInput.value.length;
            judulInput.addEventListener('input', function () {
                judulCount.textContent = this.value.length;
            });

            // ---- Media Upload ----
            const DRAFT_MEDIA = @json($draftMedia);
            const mediaFileInput = document.getElementById('mediaFileInput');
            const mediaDropzone  = document.getElementById('mediaDropzone');
            const mediaPreviewGrid = document.getElementById('mediaPreviewGrid');
            const mediaCounter   = document.getElementById('mediaCounter');
            const coverHintText  = document.getElementById('coverHintText');
            let selectedFiles   = [];
            let savedDraftMedia = [];
            let dragFromIdx     = null;
            const MAX_FILES     = 5;
            const MAX_SIZE      = 10 * 1024 * 1024;

            mediaDropzone.addEventListener('dragover',  e => { e.preventDefault(); mediaDropzone.classList.add('dragover'); });
            mediaDropzone.addEventListener('dragleave', ()  => mediaDropzone.classList.remove('dragover'));
            mediaDropzone.addEventListener('drop',      ()  => mediaDropzone.classList.remove('dragover'));
            mediaFileInput.addEventListener('change', function () { addMediaFiles(this.files); });

            function addMediaFiles(fileList) {
                for (const file of fileList) {
                    if (savedDraftMedia.length + selectedFiles.length >= MAX_FILES) {
                        mkNotify({ title: 'Batas File Tercapai', message: `Maksimal ${MAX_FILES} file yang bisa diupload.`, variant: 'warning' });
                        break;
                    }
                    if (file.size > MAX_SIZE) {
                        mkNotify({ title: 'File Terlalu Besar', message: `File "${file.name}" melebihi 10MB.`, variant: 'warning' });
                        continue;
                    }
                    if (!file.type.match(/^(image|video)\//)) {
                        mkNotify({ title: 'Format Tidak Didukung', message: `File "${file.name}" bukan gambar/video yang didukung.`, variant: 'warning' });
                        continue;
                    }
                    selectedFiles.push(file);
                }
                syncFileInput();
                renderPreviews();
                updateCounter();

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
                const totalCount = savedDraftMedia.length + selectedFiles.length;
                coverHintText.style.display = selectedFiles.length > 1 ? 'block' : 'none';
                if (!totalCount) { mediaCounter.innerHTML = ''; return; }
                const cls = totalCount >= MAX_FILES ? 'full' : totalCount >= 4 ? 'warn' : 'ok';
                mediaCounter.innerHTML = `<span class="media-counter ${cls}">${totalCount}/${MAX_FILES} file</span>`;
            }

            function renderPreviews() {
                mediaPreviewGrid.querySelectorAll('img, video').forEach(el => URL.revokeObjectURL(el.src));
                mediaPreviewGrid.innerHTML = '';

                selectedFiles.forEach((file, idx) => {
                    const item = document.createElement('div');
                    item.className = 'media-preview-item';
                    item.draggable = true;

                    // Badge: Cover or number
                    const badge = document.createElement('span');
                    if (idx === 0) {
                        badge.className = 'cover-badge-pill';
                        badge.textContent = 'Cover';
                    } else {
                        badge.className = 'cover-order-pill';
                        badge.textContent = idx + 1;
                    }

                    // Thumb
                    const thumb = document.createElement('div');
                    thumb.className = 'media-thumb';

                    const removeBtn = document.createElement('button');
                    removeBtn.type = 'button';
                    removeBtn.className = 'remove-media';
                    removeBtn.innerHTML = '✕';
                    removeBtn.onclick = e => { e.stopPropagation(); removeMediaFile(idx); };
                    thumb.appendChild(removeBtn);
                    thumb.appendChild(badge);

                    if (file.type.startsWith('image/')) {
                        const img = document.createElement('img');
                        img.src = URL.createObjectURL(file);
                        img.onload = () => URL.revokeObjectURL(img.src);
                        thumb.appendChild(img);
                    } else {
                        const vid = document.createElement('video');
                        vid.src = URL.createObjectURL(file);
                        vid.muted = true;
                        vid.onloadeddata = () => { vid.currentTime = 1; };
                        thumb.appendChild(vid);
                    }
                    item.appendChild(thumb);

                    // File info
                    const fileInfo = document.createElement('div');
                    fileInfo.className = 'file-info';
                    const nameLabel = document.createElement('div');
                    nameLabel.className = 'file-name';
                    nameLabel.textContent = file.name;
                    nameLabel.title = file.name;
                    const sizeLabel = document.createElement('div');
                    sizeLabel.className = 'file-size';
                    const kb = file.size / 1024;
                    sizeLabel.textContent = kb >= 1024 ? (kb/1024).toFixed(1)+' MB' : kb.toFixed(1)+' KB';
                    fileInfo.appendChild(nameLabel);
                    fileInfo.appendChild(sizeLabel);
                    item.appendChild(fileInfo);

                    // Drag events
                    item.addEventListener('dragstart', () => { dragFromIdx = idx; item.classList.add('dragging'); });
                    item.addEventListener('dragend',   () => {
                        dragFromIdx = null;
                        mediaPreviewGrid.querySelectorAll('.media-preview-item').forEach(el => el.classList.remove('dragging', 'drag-over'));
                    });
                    item.addEventListener('dragover',  e => { e.preventDefault(); if (dragFromIdx !== null && dragFromIdx !== idx) item.classList.add('drag-over'); });
                    item.addEventListener('dragleave', () => item.classList.remove('drag-over'));
                    item.addEventListener('drop', e => {
                        e.preventDefault();
                        if (dragFromIdx === null || dragFromIdx === idx) return;
                        const [moved] = selectedFiles.splice(dragFromIdx, 1);
                        selectedFiles.splice(idx, 0, moved);
                        syncFileInput();
                        renderPreviews();
                        updateCounter();
                    });

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
