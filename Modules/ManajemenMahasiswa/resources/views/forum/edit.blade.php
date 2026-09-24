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

            .form-label {
                font-weight: 600;
                color: #0D0D12;
                font-size: 13px;
                margin-bottom: 6px;
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

            .btn-post { background-color: #0B266E; color: white; }
            .btn-post:hover { background-color: #091958; }
            .btn-cancel { background: #fff; color: #374151; border: 1px solid #DFE1E7; }
            .btn-cancel:hover { background: rgba(11,38,110,0.04); border-color: #5C78B8; color: #0B266E; }

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

            .section-toggle:hover { background: rgba(11,38,110,0.04); border-color: #0B266E; color: #0B266E; }
            .section-toggle.active { background: rgba(11,38,110,0.04); border-color: #0B266E; color: #0B266E; }

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
                border: 2px dashed #DFE1E7;
                border-radius: 12px;
                padding: 32px 20px;
                text-align: center;
                cursor: pointer;
                transition: all 0.3s ease;
                background: #fff;
                position: relative;
            }

            .media-dropzone:hover, .media-dropzone.dragover {
                border-color: #0B266E;
                background: rgba(11,38,110,0.04);
            }

            .media-dropzone .dropzone-text { font-size: 13px; font-weight: 600; color: #353849; margin-bottom: 4px; }
            .media-dropzone .dropzone-hint { font-size: 12px; color: #808897; }
            .media-dropzone input[type="file"] { position: absolute; inset: 0; opacity: 0; cursor: pointer; }

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

            .media-preview-item img, .media-preview-item video {
                width: 100%; height: 100%; object-fit: cover; display: block;
            }

            .media-preview-item .remove-media {
                position: absolute; top: 6px; right: 6px;
                width: 26px; height: 26px; border-radius: 50%;
                background: #ef4444; color: white;
                border: 2px solid #fff; font-size: 13px;
                display: flex; align-items: center; justify-content: center;
                cursor: pointer; transition: transform 0.15s; z-index: 2;
                box-shadow: 0 1px 4px rgba(0,0,0,0.25);
            }

            .media-preview-item .remove-media:hover { transform: scale(1.12); }

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
                font-size: 12px; font-weight: 600;
                padding: 3px 10px; border-radius: 8px;
                display: inline-block; margin-top: 8px;
            }
            .media-counter.ok   { background: #DDF2EE; color: #287F6E; }
            .media-counter.warn { background: #F9ECCB; color: #956321; }
            .media-counter.full { background: #FADAE1; color: #DF1C41; }

            /* Cover drag-reorder badges */
            .cover-badge-pill {
                position: absolute; top: 5px; left: 5px;
                background: #0B266E; color: #fff;
                font-size: 9px; font-weight: 800; padding: 2px 8px;
                border-radius: 20px; letter-spacing: 0.05em; text-transform: uppercase;
                pointer-events: none; z-index: 2;
            }
            .cover-order-pill {
                position: absolute; top: 5px; left: 5px;
                background: rgba(0,0,0,0.5); color: #fff;
                font-size: 11px; font-weight: 700; padding: 1px 7px;
                border-radius: 20px; pointer-events: none; z-index: 2;
            }
            .cover-new-pill {
                position: absolute; bottom: 5px; left: 5px;
                background: rgba(16,185,129,0.9); color: #fff;
                font-size: 9px; font-weight: 700; padding: 1px 6px;
                border-radius: 20px; pointer-events: none; z-index: 2;
            }
            .media-preview-item { cursor: grab; }
            .media-preview-item.dragging  { opacity: 0.4; box-shadow: 0 6px 16px rgba(0,0,0,0.18); }
            .media-preview-item.drag-over { outline: 2px solid #5C78B8; outline-offset: 2px; }
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
            title="Edit Thread"
            subtitle="Perbarui konten postingan Anda">
            <x-slot:leading>
                <a href="{{ route('manajemenmahasiswa.forum.show', $thread->id) }}" class="mk-btn mk-btn--secondary mk-btn--sm">
                    <x-manajemenmahasiswa::ui.icon name="arrow-narrow-left" size="20" />
                </a>
            </x-slot:leading>
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
                <div class="d-flex flex-wrap gap-3 mt-2 @error('kategori') is-invalid @enderror">
                    @php
                        $selectedKategori = old('kategori', is_array($thread->kategori) ? $thread->kategori : [$thread->kategori]);
                    @endphp
                    @foreach($categories as $key => $label)
                        <div class="form-check form-check-inline m-0">
                            <input class="form-check-input shadow-none" style="cursor: pointer;" type="checkbox" name="kategori[]" id="kategori_{{ $key }}" value="{{ $key }}" {{ in_array($key, $selectedKategori) ? 'checked' : '' }}>
                            <label class="form-check-label text-dark" style="cursor: pointer; font-size: 13px;" for="kategori_{{ $key }}">{{ $label }}</label>
                        </div>
                    @endforeach
                </div>
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
                    <x-manajemenmahasiswa::ui.icon name="image-02" size="16" /> Gambar / Video
                    @if(count($existingMedia) > 0)
                        <span style="background: #0B266E; color: white; font-size: 11px; padding: 1px 8px; border-radius: 10px; margin-left: 4px;">{{ count($existingMedia) }} file</span>
                    @endif
                    <span style="margin-left: auto; font-size: 12px; opacity: 0.6;">▼</span>
                </button>
                <div class="section-content {{ count($existingMedia) > 0 ? 'open' : '' }}" id="sectionMedia">
                    {{-- Existing media — reorderable --}}
                    @if(count($existingMedia) > 0)
                        <div class="media-preview-grid mb-2" id="existingMediaGrid">
                            @foreach($existingMedia as $i => $media)
                                <div class="media-preview-item" id="existing-media-{{ $i }}"
                                     data-url="{{ $media['url'] }}" data-type="{{ $media['type'] }}" draggable="true">
                                    <div class="media-thumb">
                                        <span class="{{ $i === 0 ? 'cover-badge-pill' : 'cover-order-pill' }}" id="badge-existing-{{ $i }}">
                                            {{ $i === 0 ? 'Cover' : $i + 1 }}
                                        </span>
                                        <button type="button" class="remove-media" onclick="removeExistingMedia({{ $i }}, '{{ $media['url'] }}')">✕</button>
                                        @if($media['type'] === 'image')
                                            <img src="{{ $media['url'] }}" alt="Media" style="width:100%; aspect-ratio:4/3; object-fit:cover; display:block;">
                                        @else
                                            <video src="{{ $media['url'] }}" muted style="width:100%; aspect-ratio:4/3; object-fit:cover; display:block;"></video>
                                        @endif
                                    </div>
                                    <div class="file-info">
                                        <div class="file-name" title="{{ basename($media['url']) }}">{{ basename($media['url']) }}</div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        <p class="cover-hint-text" id="coverHintText" style="{{ count($existingMedia) > 1 ? 'display:block' : 'display:none' }}">
                            Seret gambar untuk mengubah urutan — gambar paling depan dipakai sebagai <strong>cover</strong>.
                        </p>
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
            <div class="mb-3">
                <button type="button" class="section-toggle {{ $existingLink ? 'active' : '' }}" id="toggleLink" onclick="toggleSection('link')">
                    <x-manajemenmahasiswa::ui.icon name="link-01" size="16" /> Link
                    <span style="margin-left: auto; font-size: 12px; opacity: 0.6;">▼</span>
                </button>
                <div class="section-content {{ $existingLink ? 'open' : '' }}" id="sectionLink">
                    <input type="url" name="link_url" id="inputLinkUrl" class="custom-input"
                        placeholder="https://contoh.com/artikel-menarik" value="{{ old('link_url', $existingLink) }}">
                </div>
            </div>

            {{-- Poll (Collapsible) --}}
            @php $poll = $thread->poll; @endphp
            <div class="mb-5">
                <button type="button" class="section-toggle {{ $poll ? 'active' : '' }}" id="togglePoll" onclick="togglePollSection()">
                    <x-manajemenmahasiswa::ui.icon name="bar-chart-11" size="16" />
                    Poll
                    @if($poll)
                        <span style="background:#0B266E;color:#fff;font-size:11px;padding:1px 8px;border-radius:10px;margin-left:4px;">
                            {{ $poll->options->count() }} opsi
                        </span>
                    @endif
                    <span id="pollToggleChevron" style="margin-left:auto;font-size:12px;opacity:0.6;">{{ $poll ? '▲' : '▼' }}</span>
                </button>

                <div class="section-content {{ $poll ? 'open' : '' }}" id="sectionPoll">
                    <style>
                        .poll-edit-row { display:flex;align-items:center;gap:8px;margin-bottom:8px; }
                        .poll-edit-input { flex:1;padding:9px 14px;border:1.5px solid #DFE1E7;border-radius:10px;font-size:13px;font-weight:500;outline:none;transition:border-color 0.2s; }
                        .poll-edit-input:focus { border-color:#0B266E;box-shadow:0 0 0 3px rgba(11,38,110,0.1); }
                        .poll-edit-remove { width:30px;height:30px;border-radius:50%;border:none;background:#fee2e2;color:#dc2626;font-size:16px;cursor:pointer;display:flex;align-items:center;justify-content:center;flex-shrink:0;transition:background 0.2s; }
                        .poll-edit-remove:hover { background:#fca5a5; }
                        .poll-edit-remove:disabled { opacity:0.35;cursor:not-allowed; }
                        .poll-votes-badge { font-size:11px;font-weight:700;color:#666D80;background:#F6F8FA;padding:2px 8px;border-radius:20px;white-space:nowrap; }
                        .poll-add-btn { font-size:13px;font-weight:600;color:#0B266E;background:rgba(11,38,110,0.06);border:1.5px dashed #5C78B8;border-radius:10px;padding:8px 16px;cursor:pointer;width:100%;text-align:center;transition:all 0.2s;margin-top:4px; }
                        .poll-add-btn:hover { background:rgba(11,38,110,0.1); }
                        .poll-section-label { font-size:12px;font-weight:600;color:#666D80;margin-bottom:6px;margin-top:14px;display:block; }
                        .poll-close-toggle { display:flex;align-items:center;gap:10px;padding:10px 14px;border:1.5px solid #DFE1E7;border-radius:10px;cursor:pointer;font-size:13px;font-weight:600;color:#374151;background:#fff;width:100%;margin-top:10px;transition:all 0.2s; }
                        .poll-close-toggle:hover { border-color:#0B266E;background:rgba(11,38,110,0.04); }
                    </style>

                    @if($poll)
                        {{-- Edit existing poll --}}
                        <span class="poll-section-label">Opsi Poll</span>
                        <div id="pollExistingOptions">
                            @foreach($poll->options as $opt)
                                <div class="poll-edit-row" id="poll-opt-row-{{ $opt->id }}">
                                    <input type="text" name="poll_option_text[{{ $opt->id }}]"
                                           class="poll-edit-input"
                                           value="{{ old('poll_option_text.'.$opt->id, $opt->text) }}"
                                           maxlength="150"
                                           placeholder="Teks opsi...">
                                    <span class="poll-votes-badge" title="Jumlah suara">{{ $opt->votes_count }} suara</span>
                                    <button type="button" class="poll-edit-remove"
                                            onclick="markDeleteOption({{ $opt->id }}, this)"
                                            {{ $opt->votes_count > 0 ? 'disabled title=Tidak bisa dihapus karena sudah ada suara' : '' }}>×</button>
                                </div>
                            @endforeach
                        </div>
                        <div id="pollDeleteInputs"></div>

                        {{-- Tambah opsi baru --}}
                        <div id="pollNewOptionsContainer"></div>
                        <button type="button" class="poll-add-btn" id="btnAddNewOption" onclick="addNewPollOption()">
                            + Tambah Opsi Baru
                            <span id="pollNewCount" style="color:#9ca3af;"></span>
                        </button>

                        {{-- Expiry --}}
                        <span class="poll-section-label">Batas Waktu Poll</span>
                        <input type="datetime-local" name="poll_expires_at" class="poll-edit-input"
                               style="width:auto;"
                               value="{{ old('poll_expires_at', $poll->expires_at?->format('Y-m-d\TH:i')) }}"
                               min="{{ now()->addHours(1)->format('Y-m-d\TH:i') }}">

                        {{-- Tutup / buka poll --}}
                        <button type="button" class="poll-close-toggle" id="btnClosePoll" onclick="toggleClosePoll()">
                            <span id="closePollIcon">
                                @if($poll->isClosed())
                                    <x-manajemenmahasiswa::ui.icon name="check-circle" size="16" />
                                @else
                                    <x-manajemenmahasiswa::ui.icon name="minus-circle" size="16" />
                                @endif
                            </span>
                            <span id="closePollLabel">
                                {{ $poll->isClosed() ? 'Buka kembali poll' : 'Tutup poll sekarang' }}
                            </span>
                        </button>
                        <input type="hidden" name="poll_is_closed" id="pollIsClosedInput"
                               value="{{ old('poll_is_closed', $poll->isClosed() ? '1' : '0') }}">

                    @else
                        {{-- Thread belum punya poll — buat baru --}}
                        <input type="hidden" name="has_poll" id="hasPollInput" value="0">
                        <span class="poll-section-label">Opsi Poll (min. 2, maks. 6)</span>
                        <div id="pollOptionsContainer">
                            <div class="poll-edit-row">
                                <input type="text" name="poll_options[]" class="poll-edit-input" placeholder="Opsi 1" maxlength="150">
                                <button type="button" class="poll-edit-remove" onclick="removePollOption(this)" style="visibility:hidden;">×</button>
                            </div>
                            <div class="poll-edit-row">
                                <input type="text" name="poll_options[]" class="poll-edit-input" placeholder="Opsi 2" maxlength="150">
                                <button type="button" class="poll-edit-remove" onclick="removePollOption(this)" style="visibility:hidden;">×</button>
                            </div>
                        </div>
                        <button type="button" class="poll-add-btn" id="btnAddPollOption" onclick="addPollOption()">
                            + Tambah Opsi <span id="pollOptionCount" style="color:#9ca3af;">(2/6)</span>
                        </button>
                        <span class="poll-section-label">Batas Waktu (opsional)</span>
                        <input type="datetime-local" name="poll_expires_at" class="poll-edit-input"
                               style="width:auto;" min="{{ now()->addHours(1)->format('Y-m-d\TH:i') }}">
                    @endif
                </div>
            </div>

            {{-- Action Buttons --}}
            <div class="d-flex justify-content-end gap-3 align-items-center pb-2">
                <a href="{{ route('manajemenmahasiswa.forum.show', $thread->id) }}"
                    class="btn-action btn-cancel text-decoration-none shadow-sm">
                    <x-manajemenmahasiswa::ui.icon name="minus-circle" size="16" /> Batal
                </a>
                <button type="submit" class="mk-btn mk-btn--primary">
                    <x-manajemenmahasiswa::ui.icon name="download-01" size="16" /> Simpan Perubahan
                </button>
            </div>
        </form>
    </div>

    </div>{{-- /dash-box-body --}}
    </div>{{-- /dash-box --}}
    </div>{{-- /dash-wrap --}}

    @push('scripts')
        <script>
            // ---- Toggle Sections ----
            function toggleSection(section) {
                const content = document.getElementById(`section${section.charAt(0).toUpperCase() + section.slice(1)}`);
                const toggle = document.getElementById(`toggle${section.charAt(0).toUpperCase() + section.slice(1)}`);
                content.classList.toggle('open');
                toggle.classList.toggle('active');
            }

            // ---- Poll Section Toggle ----
            let pollOpen = {{ $poll ? 'true' : 'false' }};
            function togglePollSection() {
                pollOpen = !pollOpen;
                const content  = document.getElementById('sectionPoll');
                const chevron  = document.getElementById('pollToggleChevron');
                const btn      = document.getElementById('togglePoll');
                content.classList.toggle('open', pollOpen);
                btn.classList.toggle('active', pollOpen);
                chevron.textContent = pollOpen ? '▲' : '▼';
                // Aktifkan has_poll jika bukan edit poll yang sudah ada
                const hasPoll = document.getElementById('hasPollInput');
                if (hasPoll) hasPoll.value = pollOpen ? '1' : '0';
            }

            @if($poll)
            // ---- Edit existing poll ----
            const deletedOptions = new Set();

            function markDeleteOption(optId, btn) {
                const row = document.getElementById('poll-opt-row-' + optId);
                if (deletedOptions.has(optId)) {
                    // Undo delete
                    deletedOptions.delete(optId);
                    row.style.opacity = '1';
                    row.querySelector('input').disabled = false;
                    btn.title = '';
                    btn.textContent = '×';
                    const existing = document.getElementById('del-input-' + optId);
                    if (existing) existing.remove();
                } else {
                    deletedOptions.add(optId);
                    row.style.opacity = '0.4';
                    row.querySelector('input').disabled = true;
                    btn.title = 'Klik lagi untuk batal';
                    btn.textContent = '↩';
                    const inp = document.createElement('input');
                    inp.type = 'hidden';
                    inp.name = 'poll_delete_options[]';
                    inp.value = optId;
                    inp.id = 'del-input-' + optId;
                    document.getElementById('pollDeleteInputs').appendChild(inp);
                }
            }

            let newOptionCount = 0;
            const MAX_NEW = 4;
            function addNewPollOption() {
                const existingCount = document.querySelectorAll('#pollExistingOptions .poll-edit-row').length - deletedOptions.size;
                const total = existingCount + newOptionCount;
                if (total >= 6) return;

                newOptionCount++;
                const container = document.getElementById('pollNewOptionsContainer');
                const div = document.createElement('div');
                div.className = 'poll-edit-row';
                div.innerHTML = `
                    <input type="text" name="poll_new_options[]" class="poll-edit-input"
                           placeholder="Opsi baru ${newOptionCount}" maxlength="150">
                    <button type="button" class="poll-edit-remove" onclick="this.closest('.poll-edit-row').remove(); newOptionCount--; updateNewCount();">×</button>`;
                container.appendChild(div);
                updateNewCount();
                div.querySelector('input').focus();
            }
            function updateNewCount() {
                const el = document.getElementById('pollNewCount');
                if (el) el.textContent = newOptionCount > 0 ? `(+${newOptionCount})` : '';
            }

            // ---- Close/reopen poll toggle ----
            let isClosed = {{ $poll->isClosed() ? 'true' : 'false' }};
            function toggleClosePoll() {
                isClosed = !isClosed;
                document.getElementById('pollIsClosedInput').value = isClosed ? '1' : '0';
                document.getElementById('closePollLabel').textContent = isClosed ? 'Buka kembali poll' : 'Tutup poll sekarang';
                document.getElementById('closePollIcon').innerHTML = isClosed
                    ? `<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#10b981" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M12 22c5.52 0 10-4.48 10-10S17.52 2 12 2 2 6.48 2 12s4.48 10 10 10z"/><path d="m9 12 2 2 4-4"/></svg>`
                    : `<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#ef4444" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="15" y1="9" x2="9" y2="15"/><line x1="9" y1="9" x2="15" y2="15"/></svg>`;
            }

            @else
            // ---- Create new poll (thread belum punya poll) ----
            const MAX_OPTS = 6;
            function addPollOption() {
                const container = document.getElementById('pollOptionsContainer');
                const count = container.querySelectorAll('.poll-edit-row').length;
                if (count >= MAX_OPTS) return;
                const row = document.createElement('div');
                row.className = 'poll-edit-row';
                row.innerHTML = `
                    <input type="text" name="poll_options[]" class="poll-edit-input"
                           placeholder="Opsi ${count + 1}" maxlength="150">
                    <button type="button" class="poll-edit-remove" onclick="removePollOption(this)">×</button>`;
                container.appendChild(row);
                updatePollCount();
                row.querySelector('input').focus();
            }
            function removePollOption(btn) {
                const rows = document.querySelectorAll('#pollOptionsContainer .poll-edit-row');
                if (rows.length <= 2) return;
                btn.closest('.poll-edit-row').remove();
                document.querySelectorAll('#pollOptionsContainer .poll-edit-input').forEach((inp, i) => {
                    if (!inp.value) inp.placeholder = `Opsi ${i + 1}`;
                });
                updatePollCount();
            }
            function updatePollCount() {
                const count = document.querySelectorAll('#pollOptionsContainer .poll-edit-row').length;
                const el = document.getElementById('pollOptionCount');
                if (el) el.textContent = `(${count}/${MAX_OPTS})`;
                document.querySelectorAll('#pollOptionsContainer .poll-edit-remove').forEach((btn, _, all) => {
                    btn.style.visibility = all.length > 2 ? 'visible' : 'hidden';
                });
            }
            @endif

            // ---- Character Counter ----
            const judulInput = document.getElementById('inputJudul');
            const judulCount = document.getElementById('judulCount');
            judulCount.textContent = judulInput.value.length;
            judulInput.addEventListener('input', function() {
                judulCount.textContent = this.value.length;
            });

            // ---- Existing Media: Drag-Reorder + Removal ----
            let removedMediaUrls  = [];
            let existingKeptCount = {{ count($existingMedia) }};
            let existingDragFrom  = null;

            // Build ordered URL list from DOM order
            function getExistingOrder() {
                const grid = document.getElementById('existingMediaGrid');
                if (!grid) return [];
                return Array.from(grid.querySelectorAll('.media-preview-item:not([data-removed])')).map(el => el.dataset.url);
            }

            // Sync hidden media_order[] inputs
            function syncMediaOrder() {
                document.querySelectorAll('input[name="media_order[]"]').forEach(el => el.remove());
                const form = document.getElementById('removeMediaInputs').closest('form') || document.getElementById('editPostForm');
                getExistingOrder().forEach(url => {
                    const inp = document.createElement('input');
                    inp.type = 'hidden'; inp.name = 'media_order[]'; inp.value = url;
                    form.appendChild(inp);
                });
            }

            // Refresh Cover/number badges on existing grid
            function refreshExistingBadges() {
                const grid = document.getElementById('existingMediaGrid');
                if (!grid) return;
                const items = Array.from(grid.querySelectorAll('.media-preview-item:not([data-removed])'));
                const allItems = Array.from(grid.querySelectorAll('.media-preview-item'));
                // count total shown (existing kept + new)
                const totalVisible = existingKeptCount + selectedFiles.length;
                items.forEach((el, i) => {
                    const badge = el.querySelector('.cover-badge-pill, .cover-order-pill');
                    if (!badge) return;
                    if (i === 0) {
                        badge.className = 'cover-badge-pill';
                        badge.textContent = 'Cover';
                    } else {
                        badge.className = 'cover-order-pill';
                        badge.textContent = i + 1;
                    }
                });
                const hint = document.getElementById('coverHintText');
                if (hint) hint.style.display = totalVisible > 1 ? 'block' : 'none';
            }

            // Wire up drag events on existing grid items
            function initExistingDrag() {
                const grid = document.getElementById('existingMediaGrid');
                if (!grid) return;
                grid.querySelectorAll('.media-preview-item').forEach(item => {
                    item.addEventListener('dragstart', () => {
                        existingDragFrom = item;
                        item.classList.add('dragging');
                    });
                    item.addEventListener('dragend', () => {
                        existingDragFrom = null;
                        grid.querySelectorAll('.media-preview-item').forEach(el => el.classList.remove('dragging','drag-over'));
                        refreshExistingBadges();
                        syncMediaOrder();
                    });
                    item.addEventListener('dragover', e => {
                        e.preventDefault();
                        if (existingDragFrom && existingDragFrom !== item) item.classList.add('drag-over');
                    });
                    item.addEventListener('dragleave', () => item.classList.remove('drag-over'));
                    item.addEventListener('drop', e => {
                        e.preventDefault();
                        if (!existingDragFrom || existingDragFrom === item) return;
                        const allItems = Array.from(grid.children);
                        const fromIdx = allItems.indexOf(existingDragFrom);
                        const toIdx   = allItems.indexOf(item);
                        if (fromIdx < toIdx) {
                            grid.insertBefore(existingDragFrom, item.nextSibling);
                        } else {
                            grid.insertBefore(existingDragFrom, item);
                        }
                    });
                });
            }
            initExistingDrag();

            function removeExistingMedia(index, url) {
                const el = document.getElementById(`existing-media-${index}`);
                el.setAttribute('data-removed', '1');
                el.style.display = 'none';
                removedMediaUrls.push(url);
                existingKeptCount--;

                const container = document.getElementById('removeMediaInputs');
                const input = document.createElement('input');
                input.type = 'hidden'; input.name = 'remove_media[]'; input.value = url;
                container.appendChild(input);

                refreshExistingBadges();
                syncMediaOrder();
                updateCounter();
            }

            // ---- New Media Upload ----
            const mediaFileInput   = document.getElementById('mediaFileInput');
            const mediaDropzone    = document.getElementById('mediaDropzone');
            const mediaPreviewGrid = document.getElementById('mediaPreviewGrid');
            const mediaCounter     = document.getElementById('mediaCounter');
            let selectedFiles = [];
            let newDragFrom   = null;
            const MAX_FILES   = 5;
            const MAX_SIZE    = 10 * 1024 * 1024;

            mediaDropzone.addEventListener('dragover',  e => { e.preventDefault(); mediaDropzone.classList.add('dragover'); });
            mediaDropzone.addEventListener('dragleave', () => mediaDropzone.classList.remove('dragover'));
            mediaDropzone.addEventListener('drop',      () => mediaDropzone.classList.remove('dragover'));
            mediaFileInput.addEventListener('change', function() { addMediaFiles(this.files); });

            function addMediaFiles(fileList) {
                const totalAllowed = MAX_FILES - existingKeptCount;
                for (const file of fileList) {
                    if (selectedFiles.length >= totalAllowed) {
                        mkNotify({ title: 'Batas File Tercapai', message: `Maksimal ${MAX_FILES} file total.`, variant: 'warning' });
                        break;
                    }
                    if (file.size > MAX_SIZE) {
                        mkNotify({ title: 'File Terlalu Besar', message: `"${file.name}" melebihi 10MB.`, variant: 'warning' });
                        continue;
                    }
                    if (!file.type.match(/^(image|video)\//)) {
                        mkNotify({ title: 'Format Tidak Didukung', message: `"${file.name}" bukan gambar/video.`, variant: 'warning' });
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
                const hint  = document.getElementById('coverHintText');
                if (hint) hint.style.display = total > 1 ? 'block' : 'none';
                if (!total) { mediaCounter.innerHTML = ''; return; }
                const cls = total >= MAX_FILES ? 'full' : total >= 4 ? 'warn' : 'ok';
                mediaCounter.innerHTML = `<span class="media-counter ${cls}">${total}/${MAX_FILES} file</span>`;
            }

            function renderPreviews() {
                mediaPreviewGrid.querySelectorAll('img,video').forEach(el => URL.revokeObjectURL(el.src));
                mediaPreviewGrid.innerHTML = '';

                selectedFiles.forEach((file, idx) => {
                    const globalIdx = existingKeptCount + idx; // position in overall list
                    const item = document.createElement('div');
                    item.className = 'media-preview-item';
                    item.draggable = true;

                    const thumb = document.createElement('div');
                    thumb.className = 'media-thumb';

                    // Badge
                    const badge = document.createElement('span');
                    if (existingKeptCount === 0 && idx === 0) {
                        badge.className = 'cover-badge-pill';
                        badge.textContent = 'Cover';
                    } else {
                        badge.className = 'cover-order-pill';
                        badge.textContent = globalIdx + 1;
                    }
                    // "Baru" label
                    const newBadge = document.createElement('span');
                    newBadge.className = 'cover-new-pill';
                    newBadge.textContent = 'Baru';

                    const removeBtn = document.createElement('button');
                    removeBtn.type = 'button';
                    removeBtn.className = 'remove-media';
                    removeBtn.innerHTML = '✕';
                    removeBtn.onclick = e => { e.stopPropagation(); removeMediaFile(idx); };
                    thumb.appendChild(removeBtn);
                    thumb.appendChild(badge);
                    thumb.appendChild(newBadge);

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

                    // Drag to reorder new files
                    item.addEventListener('dragstart', () => { newDragFrom = idx; item.classList.add('dragging'); });
                    item.addEventListener('dragend',   () => {
                        newDragFrom = null;
                        mediaPreviewGrid.querySelectorAll('.media-preview-item').forEach(el => el.classList.remove('dragging','drag-over'));
                    });
                    item.addEventListener('dragover',  e => { e.preventDefault(); if (newDragFrom !== null && newDragFrom !== idx) item.classList.add('drag-over'); });
                    item.addEventListener('dragleave', () => item.classList.remove('drag-over'));
                    item.addEventListener('drop', e => {
                        e.preventDefault();
                        if (newDragFrom === null || newDragFrom === idx) return;
                        const [moved] = selectedFiles.splice(newDragFrom, 1);
                        selectedFiles.splice(idx, 0, moved);
                        syncFileInput();
                        renderPreviews();
                        updateCounter();
                    });

                    mediaPreviewGrid.appendChild(item);
                });
            }

            // Init counter
            updateCounter();
            // ---- Prevent Double Submit ----
            const form = document.getElementById('editPostForm');
            const submitBtn = form.querySelector('button[type="submit"]');
            form.addEventListener('submit', function() {
                submitBtn.disabled = true;
                submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span> Loading...';
            });
        </script>
    @endpush

</x-manajemenmahasiswa::layouts.forum-layout>
