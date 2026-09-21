<x-manajemenmahasiswa::layouts.admin>

    @push('styles')
        <style>
            /* Token desain global SITKOM — disamakan dengan dashboard Super Admin
               (resources/views/components/sidebar.blade.php). Layout modul ini tidak
               mendefinisikan token tersebut, jadi harus dideklarasikan ulang di sini. */
            :root {
                --c-primary: #0B266E;
                --c-primary-hover: #091958;
                --c-primary-subtle: rgba(11, 38, 110, 0.08);
                --c-primary-border: #5C78B8;
                --c-bg: #F6F8FA;
                --c-fg: #0D0D12;
                --c-fg-sec: #353849;
                --c-fg-muted: #666D80;
                --c-fg-placeholder: #808897;
                --c-border: #DFE1E7;
                --c-border-strong: #C1C7CF;
                --c-success: #287F6E;
                --c-success-subtle: #DDF2EE;
                --c-error: #DF1C41;
                --c-error-subtle: #FADAE1;
                --c-warning: #956321;
                --c-warning-subtle: #F9ECCB;
                --shadow-card: 0px 1px 2px 0px rgba(228, 229, 231, 0.5);
            }

            .main-wrapper { background: transparent !important; box-shadow: none !important; padding: 0 !important; }

            /* ── Shell kotak: mengikuti dashboard Super Admin ───────────── */
            .sitkom-content { padding: 0 !important; display: flex; flex-direction: column; flex: 1; overflow: hidden; }
            .dash-wrap { display: flex; flex-direction: column; height: calc(100vh - 60px); padding: 10px; box-sizing: border-box; font-family: 'Inter Tight', sans-serif; }
            .dash-box { display: flex; flex-direction: column; flex: 1; min-height: 0; background: #fff; border: 1px solid var(--c-border, #DFE1E7); border-radius: 12px; box-shadow: var(--shadow-card, 0px 1px 2px 0px rgba(228,229,231,0.5)); overflow: hidden; width: 100%; box-sizing: border-box; }
            .dash-box-header { background: #fff; border-bottom: 1px solid var(--c-border, #DFE1E7); flex-shrink: 0; width: 100%; box-sizing: border-box; padding: 16px 24px; }
            .dash-box-body { flex: 1; overflow-y: auto; padding: 20px 24px; }
            .dash-box-body::-webkit-scrollbar { width: 6px; }
            .dash-box-body::-webkit-scrollbar-thumb { background: var(--c-border-strong, #C1C7CF); border-radius: 10px; }
            @media (max-width: 767px) {
                .sitkom-content { padding: 8px 8px 80px !important; display: block !important; overflow: visible !important; }
                .dash-wrap { height: auto !important; min-height: 0 !important; padding: 0; }
                .dash-box { flex: none !important; min-height: 0 !important; overflow: visible !important; border-radius: 10px; }
                .dash-box-header { padding: 12px 14px; position: sticky; top: 52px; z-index: 10; }
                .dash-box-body { overflow-y: visible !important; flex: none !important; padding: 14px; }
            }

            /* ── Kartu section: pola table-card / chart-card Super Admin ── */
            .fc-card {
                background: #fff;
                border: 1px solid var(--c-border, #DFE1E7);
                border-radius: 14px;
                box-shadow: var(--shadow-card, 0px 1px 2px 0px rgba(228,229,231,0.5));
                margin-bottom: 10px;
                overflow: hidden;
            }
            .fc-card-header {
                display: flex; align-items: center; justify-content: space-between; gap: 10px;
                padding: 13px 18px;
                border-bottom: 1px solid var(--c-border, #DFE1E7);
                font-size: 13px; font-weight: 700; color: var(--c-fg, #0D0D12);
            }
            .fc-card-body { padding: 18px; }

            /* ── Form controls ──────────────────────────────────────────── */
            .form-group { margin-bottom: 10px; }
            .form-group:last-child { margin-bottom: 0; }

            .form-group label {
                display: block;
                font-size: 12px; font-weight: 600; color: var(--c-fg-sec, #353849);
                margin-bottom: 6px;
            }
            .form-group label .required { color: var(--c-error, #DF1C41); margin-left: 2px; }

            .form-input,
            .form-select-custom,
            .form-textarea {
                width: 100%;
                box-sizing: border-box;
                padding: 9px 12px;
                border: 1px solid var(--c-border, #DFE1E7);
                border-radius: 8px;
                font-size: 13px;
                color: var(--c-fg-sec, #353849);
                background: #fff;
                transition: border-color .15s, box-shadow .15s;
                outline: none;
                font-family: inherit;
            }

            .form-input:focus,
            .form-select-custom:focus,
            .form-textarea:focus {
                border-color: var(--c-primary, #0B266E);
                box-shadow: 0 0 0 3px rgba(11, 38, 110, 0.1);
            }

            .form-input::placeholder,
            .form-textarea::placeholder { color: var(--c-fg-placeholder, #808897); }

            .form-textarea { min-height: 200px; resize: vertical; line-height: 1.6; }

            .form-select-custom {
                appearance: none;
                background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='16' height='16' viewBox='0 0 24 24' fill='none' stroke='%23808897' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpath d='m6 9 6 6 6-6'/%3E%3C/svg%3E");
                background-repeat: no-repeat;
                background-position: right 12px center;
                padding-right: 36px;
                cursor: pointer;
            }

            .form-row { display: grid; grid-template-columns: 1fr 1fr; gap: 16px; }
            @media (max-width: 768px) { .form-row { grid-template-columns: 1fr; } }

            /* ── File upload ────────────────────────────────────────────── */
            .file-upload-zone {
                border: 1px dashed var(--c-border-strong, #C1C7CF);
                border-radius: 12px;
                padding: 20px;
                text-align: center;
                background: var(--c-bg, #F6F8FA);
                transition: border-color .15s, background .15s;
                cursor: pointer;
                position: relative;
            }
            .file-upload-zone:hover,
            .file-upload-zone.drag-over {
                border-color: var(--c-primary, #0B266E);
                background: var(--c-primary-subtle, #EEF1F8);
            }
            .file-upload-zone .upload-icon {
                width: 44px; height: 44px; border-radius: 10px;
                background: var(--c-primary-subtle, #EEF1F8);
                color: var(--c-primary, #0B266E);
                display: flex; align-items: center; justify-content: center;
                margin: 0 auto 12px;
            }
            .file-upload-zone h6 { font-size: 13px; font-weight: 700; color: var(--c-fg, #0D0D12); margin-bottom: 3px; }
            .file-upload-zone p  { font-size: 11.5px; color: var(--c-fg-muted, #666D80); margin-bottom: 0; }
            .file-upload-zone input[type="file"] { position: absolute; inset: 0; opacity: 0; cursor: pointer; }

            .file-list { margin-top: 12px; display: flex; flex-direction: column; gap: 8px; }
            .file-item {
                display: flex; align-items: center; justify-content: space-between;
                padding: 9px 12px;
                background: #fff;
                border: 1px solid var(--c-border, #DFE1E7);
                border-radius: 8px;
                font-size: 12px; color: var(--c-fg-sec, #353849);
            }
            .file-item .file-name { display: flex; align-items: center; gap: 8px; font-weight: 600; }
            .file-item .file-remove {
                color: var(--c-error, #DF1C41); cursor: pointer; background: none; border: none;
                padding: 2px 6px; border-radius: 6px; transition: background .15s;
            }
            .file-item .file-remove:hover { background: var(--c-error-subtle, #FADAE1); }

            /* ── Grid gambar pengumuman (cover) ─────────────────────────── */
            .cover-grid {
                display: grid; grid-template-columns: repeat(auto-fill, minmax(118px, 1fr));
                gap: 10px; margin-top: 10px;
            }
            .cover-item {
                position: relative; aspect-ratio: 1;
                border: 1px solid var(--c-border, #DFE1E7); border-radius: 10px;
                overflow: hidden; background: var(--c-bg, #F6F8FA);
                transition: border-color .15s, box-shadow .15s, opacity .15s;
            }
            .cover-item img { width: 100%; height: 100%; object-fit: cover; display: block; pointer-events: none; }

            /* Kartu yang bisa diseret untuk mengatur urutan */
            .cover-item[draggable="true"] { cursor: grab; }
            .cover-item[draggable="true"]:hover { border-color: var(--c-primary-border, #5C78B8); }
            .cover-item.dragging { opacity: .35; cursor: grabbing; }
            .cover-item.drag-target {
                border-color: var(--c-primary, #0B266E);
                box-shadow: 0 0 0 3px var(--c-primary-subtle, #EEF1F8);
            }
            .cover-item.is-saved { border-color: var(--c-success, #287F6E); }

            .cover-badge {
                position: absolute; top: 6px; left: 6px;
                padding: 2px 7px; border-radius: 6px;
                font-size: 10px; font-weight: 700; letter-spacing: .02em;
                background: var(--c-primary, #0B266E); color: #fff;
            }
            .cover-order {
                position: absolute; bottom: 6px; left: 6px;
                width: 19px; height: 19px; border-radius: 50%;
                background: rgba(13,13,18,.6); color: #fff;
                font-size: 10px; font-weight: 700;
                display: flex; align-items: center; justify-content: center;
            }
            .cover-remove {
                position: absolute; top: 6px; right: 6px;
                width: 22px; height: 22px; border-radius: 6px;
                border: none; background: rgba(13,13,18,.55); color: #fff;
                display: flex; align-items: center; justify-content: center;
                cursor: pointer; transition: background .15s; padding: 0;
            }
            .cover-remove:hover { background: var(--c-error, #DF1C41); }

            .cover-hint { font-size: 11.5px; color: var(--c-fg-muted, #666D80); margin: 10px 0 0; }
            .cover-hint strong { color: var(--c-primary, #0B266E); }

            /* Penanda file yang sudah tersimpan di draf */
            .saved-note {
                display: flex; align-items: center; gap: 6px;
                margin-top: 10px; padding: 7px 11px;
                background: var(--c-success-subtle, #DDF2EE);
                color: var(--c-success, #287F6E);
                border: 1px solid var(--c-success, #287F6E);
                border-radius: 8px;
                font-size: 11.5px; font-weight: 600;
            }
            .file-item.is-saved {
                background: var(--c-success-subtle, #DDF2EE);
                border-color: var(--c-success, #287F6E);
                color: var(--c-success, #287F6E);
            }
            .file-item .file-meta { font-size: 11.5px; color: var(--c-fg-placeholder, #808897); }
            .file-item.is-saved .file-meta { color: var(--c-success, #287F6E); }

            /* ── Actions ────────────────────────────────────────────────── */
            .form-actions {
                display: flex; align-items: center; gap: 8px; flex-wrap: wrap;
                margin-top: 16px; padding-top: 16px;
                border-top: 1px solid var(--c-border, #DFE1E7);
            }
            .fa-spacer { flex: 1; }

            .btn-cancel,
            .btn-draft {
                display: inline-flex; align-items: center; gap: 6px;
                padding: 8px 14px;
                border: 1px solid var(--c-border, #DFE1E7);
                border-radius: 8px;
                background: #fff;
                font-size: 12px; font-weight: 600;
                cursor: pointer; text-decoration: none;
                transition: all .15s;
                box-shadow: 0 1px 2px rgba(0,0,0,.04);
                font-family: inherit;
            }
            .btn-cancel { color: var(--c-fg-sec, #353849); }
            .btn-cancel:hover {
                background: var(--c-bg, #F6F8FA);
                border-color: var(--c-border-strong, #C1C7CF);
                color: var(--c-fg-sec, #353849);
            }
            .btn-draft { color: var(--c-primary, #0B266E); }
            .btn-draft:hover {
                background: var(--c-primary-subtle, #EEF1F8);
                border-color: var(--c-primary-border, #5C78B8);
            }

            .btn-publish {
                display: inline-flex; align-items: center; gap: 6px;
                padding: 8px 16px;
                border: 1px solid var(--c-primary, #0B266E);
                border-radius: 8px;
                background: var(--c-primary, #0B266E);
                color: #fff;
                font-size: 12px; font-weight: 600;
                cursor: pointer; transition: all .15s;
                box-shadow: 0 2px 6px rgba(11, 38, 110, 0.3);
                font-family: inherit;
            }
            .btn-publish:hover {
                background: var(--c-primary-hover, #091958);
                box-shadow: 0 4px 12px rgba(11, 38, 110, 0.4);
            }

            .draft-status { font-size: 12px; color: var(--c-fg-muted, #666D80); font-style: italic; }

            /* ── Rich text editor ───────────────────────────────────────── */
            .rich-editor-wrapper {
                border: 1px solid var(--c-border, #DFE1E7);
                border-radius: 8px;
                overflow: hidden;
                background: #fff;
                transition: border-color .15s, box-shadow .15s;
            }
            .rich-editor-wrapper:focus-within {
                border-color: var(--c-primary, #0B266E);
                box-shadow: 0 0 0 3px rgba(11, 38, 110, 0.1);
            }

            .editor-toolbar {
                display: flex; align-items: center; gap: 2px;
                padding: 8px 12px;
                background: var(--c-bg, #F6F8FA);
                border-bottom: 1px solid var(--c-border, #DFE1E7);
                flex-wrap: wrap; row-gap: 6px;
            }
            .toolbar-group { display: flex; align-items: center; gap: 2px; }
            .toolbar-divider {
                width: 1px; height: 20px;
                background: var(--c-border, #DFE1E7);
                margin: 0 6px; flex-shrink: 0;
            }
            .toolbar-btn {
                display: inline-flex; align-items: center; gap: 4px;
                padding: 5px 9px;
                border: 1px solid transparent; border-radius: 6px;
                background: transparent;
                color: var(--c-fg-sec, #353849);
                font-size: 12px; font-weight: 600;
                cursor: pointer; transition: all .15s;
                white-space: nowrap; font-family: inherit;
                line-height: 1.4; user-select: none;
            }
            .toolbar-btn:hover {
                background: #fff;
                border-color: var(--c-border, #DFE1E7);
                color: var(--c-fg, #0D0D12);
            }
            .toolbar-btn.active {
                background: var(--c-primary-subtle, #EEF1F8);
                border-color: var(--c-primary-border, #5C78B8);
                color: var(--c-primary, #0B266E);
            }
            .toolbar-btn-bold { font-weight: 800; }
            .toolbar-btn-italic { font-style: italic; }
            .toolbar-btn-underline { text-decoration: underline; }

            .editor-content {
                min-height: 220px;
                padding: 12px 14px;
                font-size: 13px;
                color: var(--c-fg-sec, #353849);
                line-height: 1.7;
                outline: none;
                background: transparent;
            }
            .editor-content:empty::before {
                content: 'Tulis isi pengumuman di sini...';
                color: var(--c-fg-placeholder, #808897);
                pointer-events: none; display: block;
            }
            .editor-content h1 { font-size: 19px; font-weight: 700; color: var(--c-fg, #0D0D12); margin: 14px 0 8px; }
            .editor-content h2 { font-size: 16px; font-weight: 700; color: var(--c-fg, #0D0D12); margin: 12px 0 6px; }
            .editor-content ul,
            .editor-content ol { padding-left: 24px; margin: 8px 0; }
            .editor-content li { margin-bottom: 4px; }
            .editor-content a { color: var(--c-primary, #0B266E); text-decoration: underline; }
            .editor-content hr { border: none; border-top: 1px solid var(--c-border, #DFE1E7); margin: 16px 0; }
            .editor-content table { width: 100%; border-collapse: collapse; margin: 12px 0; font-size: 12.5px; }
            .editor-content table td,
            .editor-content table th {
                border: 1px solid var(--c-border, #DFE1E7);
                padding: 8px 12px; text-align: left;
            }
            .editor-content table th {
                background: #FBFBFC; font-weight: 600; color: var(--c-fg, #0D0D12);
            }
            .editor-content img { max-width: 100%; border-radius: 8px; margin: 8px 0; display: block; }

            /* ── Alerts & errors ────────────────────────────────────────── */
            .form-error { font-size: 11.5px; color: var(--c-error, #DF1C41); margin-top: 5px; display: block; }

            .alert-success,
            .alert-danger {
                border-radius: 10px; padding: 12px 16px;
                font-size: 12px; font-weight: 500; margin-bottom: 10px;
            }
            .alert-success {
                background: var(--c-success-subtle, #DDF2EE);
                color: var(--c-success, #287F6E);
                border: 1px solid var(--c-success, #287F6E);
            }
            .alert-danger {
                background: var(--c-error-subtle, #FADAE1);
                color: var(--c-error, #DF1C41);
                border: 1px solid var(--c-error, #DF1C41);
            }

            /* ── Drafts modal ───────────────────────────────────────────── */
            .draft-item {
                display: flex; align-items: center; justify-content: space-between; gap: 10px;
                padding: 12px 14px;
                border: 1px solid var(--c-border, #DFE1E7);
                border-radius: 10px; margin-bottom: 8px;
                cursor: pointer; transition: border-color .15s, background .15s;
            }
            .draft-item:hover { border-color: var(--c-primary-border, #5C78B8); background: var(--c-bg, #F6F8FA); }
            .draft-item-title { font-size: 13px; font-weight: 700; color: var(--c-fg, #0D0D12); margin-bottom: 3px; }
            .draft-item-excerpt {
                font-size: 12px; color: var(--c-fg-muted, #666D80); margin-bottom: 3px;
                display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;
            }
            .draft-item-meta { font-size: 11px; color: var(--c-fg-placeholder, #808897); }
            .draft-delete-btn {
                width: 30px; height: 30px; border-radius: 8px;
                display: flex; align-items: center; justify-content: center;
                border: 1px solid var(--c-border, #DFE1E7); background: #fff;
                color: var(--c-fg-muted, #666D80); cursor: pointer; transition: all .15s;
            }
            .draft-delete-btn:hover {
                border-color: var(--c-error, #DF1C41);
                color: var(--c-error, #DF1C41);
                background: var(--c-error-subtle, #FADAE1);
            }
        </style>
    @endpush

    <div class="dash-wrap">
        <div class="dash-box">

            {{-- ── Header ─────────────────────────────────── --}}
            <div class="dash-box-header">
                <div style="display:flex; align-items:center; justify-content:space-between; gap:16px; flex-wrap:wrap;">
                    <div>
                        <div style="display:flex; align-items:center; gap:8px; margin-bottom:3px;">
                            <h1 style="font-size:22px; font-weight:700; color:var(--c-fg, #0D0D12); letter-spacing:-0.02em; line-height:1.2; margin:0;">Buat Pengumuman Baru</h1>
                            <span style="font-size:10px; font-weight:600; color:var(--c-primary, #0B266E); background:rgba(11,38,110,0.09); border:1px solid rgba(11,38,110,0.18); padding:2px 8px; border-radius:9999px; letter-spacing:0.03em;">Modul Mahasiswa</span>
                        </div>
                        <p style="font-size:12px; color:var(--c-fg-muted, #666D80); margin:0;">
                            Buat dan publikasikan pengumuman untuk mahasiswa dan alumni
                        </p>
                    </div>

                    @if(isset($drafts) && $drafts->count() > 0)
                        <button type="button" class="mk-btn mk-btn--secondary" data-bs-toggle="modal" data-bs-target="#draftsModal">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z" />
                            </svg>
                            <span>Load Draft ({{ $drafts->count() }})</span>
                        </button>
                    @endif
                </div>
            </div>

            <div class="dash-box-body">

                {{-- Flash Messages --}}
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

                <form action="{{ route('manajemenmahasiswa.pengumuman.store') }}" method="POST" enctype="multipart/form-data"
                    id="createForm">
                    @csrf
                    <input type="hidden" name="draft_id" id="draft_id" value="">

                    {{-- ── Informasi Pengumuman ──────────────────── --}}
                    <div class="fc-card">
                        <div class="fc-card-header">Informasi Pengumuman</div>
                        <div class="fc-card-body">

                            <div class="form-group">
                                <label>Judul Pengumuman <span class="required">*</span></label>
                                <input type="text" name="judul" class="form-input" placeholder="Masukkan judul pengumuman"
                                    value="{{ old('judul') }}" required>
                                @error('judul') <span class="form-error">{{ $message }}</span> @enderror
                            </div>

                            <div class="form-row">
                                <div class="form-group">
                                    <label>Kategori</label>
                                    <x-manajemenmahasiswa::ui.select name="kategori" size="md">
                                        <option value="">Pilih Kategori</option>
                                        <option value="akademik" {{ old('kategori') === 'akademik' ? 'selected' : '' }}>Akademik</option>
                                        <option value="himpunan" {{ old('kategori') === 'himpunan' ? 'selected' : '' }}>Himpunan</option>
                                        <option value="lowongan" {{ old('kategori') === 'lowongan' ? 'selected' : '' }}>Lowongan</option>
                                        <option value="event_prodi" {{ old('kategori') === 'event_prodi' ? 'selected' : '' }}>Event Prodi
                                        </option>
                                    </x-manajemenmahasiswa::ui.select>
                                    @error('kategori') <span class="form-error">{{ $message }}</span> @enderror
                                </div>

                                <div class="form-group">
                                    <label>Target Audiens <span class="required">*</span></label>
                                    <x-manajemenmahasiswa::ui.select name="target_audience" size="md" required>
                                        <option value="all" {{ old('target_audience') === 'all' ? 'selected' : '' }}>Semua</option>
                                        <option value="mahasiswa" {{ old('target_audience') === 'mahasiswa' ? 'selected' : '' }}>Mahasiswa
                                        </option>
                                        <option value="alumni" {{ old('target_audience') === 'alumni' ? 'selected' : '' }}>Alumni</option>
                                    </x-manajemenmahasiswa::ui.select>
                                    @error('target_audience') <span class="form-error">{{ $message }}</span> @enderror
                                </div>
                            </div>

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
                                            <button type="button" class="toolbar-btn"
                                                onclick="execFormatBlock('H1')" title="Heading 1">H1</button>
                                            <button type="button" class="toolbar-btn"
                                                onclick="execFormatBlock('H2')" title="Heading 2">H2</button>
                                            <button type="button" class="toolbar-btn" data-cmd="insertUnorderedList"
                                                onclick="execCmd('insertUnorderedList')" title="Bullet List">
                                                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="9" y1="6" x2="20" y2="6"/><line x1="9" y1="12" x2="20" y2="12"/><line x1="9" y1="18" x2="20" y2="18"/><circle cx="4" cy="6" r="1.5" fill="currentColor"/><circle cx="4" cy="12" r="1.5" fill="currentColor"/><circle cx="4" cy="18" r="1.5" fill="currentColor"/></svg>
                                                List
                                            </button>
                                            <button type="button" class="toolbar-btn" data-cmd="insertOrderedList"
                                                onclick="execCmd('insertOrderedList')" title="Numbered List">
                                                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="10" y1="6" x2="21" y2="6"/><line x1="10" y1="12" x2="21" y2="12"/><line x1="10" y1="18" x2="21" y2="18"/><text x="2" y="9" font-size="8" fill="currentColor" stroke="none" font-weight="bold">1.</text></svg>
                                                List
                                            </button>
                                        </div>
                                        <div class="toolbar-divider"></div>
                                        <div class="toolbar-group">
                                            <button type="button" class="toolbar-btn" onclick="insertLink()" title="Hyperlink">
                                                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M10 13a5 5 0 0 0 7.54.54l3-3a5 5 0 0 0-7.07-7.07l-1.72 1.71"/><path d="M14 11a5 5 0 0 0-7.54-.54l-3 3a5 5 0 0 0 7.07 7.07l1.71-1.71"/></svg>
                                                Link
                                            </button>
                                            <button type="button" class="toolbar-btn" onclick="triggerImageInsert()" title="Sisipkan Gambar">
                                                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><rect width="18" height="18" x="3" y="3" rx="2"/><circle cx="9" cy="9" r="2"/><path d="m21 15-3.086-3.086a2 2 0 0 0-2.828 0L6 21"/></svg>
                                                Gambar
                                            </button>
                                            <button type="button" class="toolbar-btn" onclick="insertTable()" title="Sisipkan Tabel">
                                                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><rect width="18" height="18" x="3" y="3" rx="2"/><path d="M3 9h18M3 15h18M9 3v18M15 3v18"/></svg>
                                                Tabel
                                            </button>
                                            <button type="button" class="toolbar-btn" onclick="insertSeparator()" title="Garis Pemisah">
                                                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"><line x1="3" y1="12" x2="21" y2="12"/></svg>
                                                Separator
                                            </button>
                                        </div>
                                        <input type="file" id="inlineImageInput" accept="image/jpeg,image/png,image/gif,image/webp"
                                            style="display:none;">
                                    </div>
                                    <div class="editor-content" id="editorContent" contenteditable="true">{!! old('konten') !!}</div>
                                </div>
                                <textarea name="konten" id="kontenHidden" style="display:none;">{{ old('konten') }}</textarea>
                                @error('konten') <span class="form-error">{{ $message }}</span> @enderror
                            </div>

                        </div>
                    </div>

                    {{-- ── Poster & Lampiran ─────────────────────── --}}
                    <div class="fc-card">
                        <div class="fc-card-header">Poster &amp; Lampiran</div>
                        <div class="fc-card-body">

                            <div class="form-group">
                                <label>Gambar Pengumuman <span style="color:var(--c-fg-placeholder, #808897); font-weight:400;">(opsional, maks. 5)</span></label>
                                <div class="file-upload-zone" id="posterZone">
                                    <div class="upload-icon">
                                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                            stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                                            <rect width="18" height="18" x="3" y="3" rx="2" ry="2" />
                                            <circle cx="9" cy="9" r="2" />
                                            <path d="m21 15-3.086-3.086a2 2 0 0 0-2.828 0L6 21" />
                                        </svg>
                                    </div>
                                    <h6>Klik atau seret gambar ke sini</h6>
                                    <p>JPG, PNG — maks. 5 gambar, 10MB per gambar</p>
                                    <input type="file" name="poster[]" accept="image/jpeg,image/png" id="posterInput" multiple>
                                </div>

                                {{-- Gambar yang baru dipilih: bisa diseret untuk menentukan cover --}}
                                <p class="cover-hint" id="coverHint" style="display: none;">
                                    Seret gambar untuk mengubah urutan — gambar paling depan dipakai sebagai <strong>cover</strong>.
                                </p>
                                <div class="cover-grid" id="posterPreview"></div>

                                {{-- Gambar yang sudah tersimpan di draf (hanya tampilan) --}}
                                <div class="saved-note" id="posterSavedNote" style="display: none;">
                                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M20 6 9 17l-5-5"/>
                                    </svg>
                                    <span id="posterSavedName">Gambar tersimpan di draf</span>
                                </div>
                                <div class="cover-grid" id="posterSavedList"></div>
                            </div>

                            <div class="form-group">
                                <label>Lampiran / Dokumen (opsional)</label>
                                <div class="file-upload-zone" id="lampiranZone">
                                    <div class="upload-icon">
                                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                            stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
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
                                <div class="file-list" id="lampiranSavedList"></div>
                                <div class="file-list" id="lampiranList"></div>
                            </div>

                        </div>
                    </div>

                    <input type="hidden" name="status_publish" id="statusPublish" value="published">

                    {{-- ── Actions ───────────────────────────────── --}}
                    <div class="form-actions">
                        <a href="{{ route('manajemenmahasiswa.pengumuman.index') }}" class="mk-btn mk-btn--secondary">Batal</a>

                        <div class="fa-spacer"></div>

                        <span id="draftStatus" class="draft-status" style="display: none;">Menyimpan draf...</span>

                        <button type="button" class="mk-btn mk-btn--secondary" onclick="saveDraftManual()">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"
                                stroke-linecap="round" stroke-linejoin="round">
                                <path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z" />
                                <polyline points="17 21 17 13 7 13 7 21" />
                                <polyline points="7 3 7 8 15 8" />
                            </svg>
                            <span>Simpan Draft</span>
                        </button>

                        <button type="submit" class="mk-btn mk-btn--primary">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"
                                stroke-linecap="round" stroke-linejoin="round">
                                <line x1="22" x2="11" y1="2" y2="13" />
                                <polygon points="22 2 15 22 11 13 2 9 22 2" />
                            </svg>
                            <span>Publikasikan</span>
                        </button>
                    </div>

                </form>

            </div> <!-- end dash-box-body -->
        </div> <!-- end dash-box -->
    </div> <!-- end dash-wrap -->

    {{-- Modal Drafts --}}
    @if(isset($drafts) && $drafts->count() > 0)
        <div class="modal fade" id="draftsModal" tabindex="-1" aria-labelledby="draftsModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
                <div class="modal-content border-0" style="border-radius: 14px; box-shadow: 0 24px 60px rgba(0,0,0,.18);">
                    <div class="modal-header" style="border-bottom: 1px solid var(--c-border, #DFE1E7); padding: 14px 18px;">
                        <h5 class="modal-title" id="draftsModalLabel"
                            style="font-size: 14px; font-weight: 700; color: var(--c-fg, #0D0D12); margin: 0;">Draf Anda</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body" style="padding: 14px 18px;">
                        @foreach($drafts as $draft)
                            <div class="draft-item">
                                <div style="flex: 1; min-width: 0;"
                                    onclick="loadDraft({{ $draft->id }}, {{ json_encode($draft->judul) }}, {{ json_encode($draft->kategori) }}, {{ json_encode($draft->target_audience) }}, {{ json_encode($draft->konten) }})">
                                    <div class="draft-item-title">{{ $draft->judul ?: '(Tanpa Judul)' }}</div>
                                    <div class="draft-item-excerpt">
                                        {{ strip_tags($draft->konten) ?: '(Tidak ada konten teks)' }}
                                    </div>
                                    <div class="draft-item-meta">
                                        Diperbarui: {{ $draft->updated_at->diffForHumans() }}
                                    </div>
                                </div>
                                <form action="{{ route('manajemenmahasiswa.pengumuman.drafts.destroy', $draft->id) }}"
                                    method="POST" style="margin: 0;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="draft-delete-btn" title="Hapus draf ini">
                                        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                            stroke-width="2" stroke-linecap="round">
                                            <path d="M18 6L6 18M6 6l12 12" />
                                        </svg>
                                    </button>
                                </form>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    @endif

    @push('scripts')
        <script>
            // Poster & lampiran yang sudah diupload per draf, dikirim dari controller.
            const DRAFT_ATTACHMENTS = @json($draftAttachments ?? []);

            function escHtml(str) {
                return String(str).replace(/[&<>"']/g, c => (
                    { '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[c]
                ));
            }

            /**
             * Tampilkan file yang sudah tersimpan di draf (gambar + lampiran).
             * Dipanggil saat draf di-load dan setelah draf berhasil disimpan.
             */
            function renderDraftAttachments(payload) {
                const savedNote = document.getElementById('posterSavedNote');
                const savedName = document.getElementById('posterSavedName');
                const savedGrid = document.getElementById('posterSavedList');
                const savedList = document.getElementById('lampiranSavedList');

                const gambar = payload?.gambar ?? [];
                savedNote.style.display = gambar.length ? 'flex' : 'none';
                savedName.textContent = gambar.length + ' gambar tersimpan di draf'
                    + (gambar.length > 1 ? ' — yang pertama menjadi cover' : '');

                savedGrid.innerHTML = gambar.map((f, i) => `
                    <div class="cover-item is-saved" title="${escHtml(f.nama)}">
                        <img src="${escHtml(f.url)}" alt="${escHtml(f.nama)}">
                        ${i === 0
                            ? '<span class="cover-badge">Cover</span>'
                            : `<span class="cover-order">${i + 1}</span>`}
                    </div>
                `).join('');

                const lampiran = payload?.lampiran ?? [];
                savedList.innerHTML = lampiran.map(f => `
                    <div class="file-item is-saved">
                        <span class="file-name">
                            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M20 6 9 17l-5-5"/>
                            </svg>
                            <a href="${escHtml(f.url)}" target="_blank" rel="noopener"
                               style="color:inherit;text-decoration:underline;">${escHtml(f.nama)}</a>
                        </span>
                        <span class="file-meta">Tersimpan di draf</span>
                    </div>
                `).join('');
            }

            // ── Gambar pengumuman: pilih maks. 5, urutkan dengan drag ──────────
            const MAX_GAMBAR = 5;
            const posterInput = document.getElementById('posterInput');
            let gambarTerpilih = [];   // File[] — indeks 0 dipakai sebagai cover
            let dragDari = null;

            /** Tulis ulang FileList input sesuai urutan kartu supaya ikut terkirim. */
            function syncInputGambar() {
                const dt = new DataTransfer();
                gambarTerpilih.forEach(f => dt.items.add(f));
                posterInput.files = dt.files;
            }

            function renderGambarTerpilih() {
                const grid = document.getElementById('posterPreview');
                const hint = document.getElementById('coverHint');

                // Bebaskan object URL kartu lama sebelum digambar ulang
                grid.querySelectorAll('img').forEach(img => URL.revokeObjectURL(img.src));
                grid.innerHTML = '';
                hint.style.display = gambarTerpilih.length > 1 ? 'block' : 'none';

                gambarTerpilih.forEach((file, i) => {
                    const card = document.createElement('div');
                    card.className = 'cover-item';
                    card.draggable = true;
                    card.title = file.name;
                    card.innerHTML = `
                        <img src="${URL.createObjectURL(file)}" alt="">
                        ${i === 0
                            ? '<span class="cover-badge">Cover</span>'
                            : `<span class="cover-order">${i + 1}</span>`}
                        <button type="button" class="cover-remove" title="Hapus gambar">
                            <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                stroke-width="2.5" stroke-linecap="round">
                                <path d="M18 6L6 18M6 6l12 12"/>
                            </svg>
                        </button>`;

                    card.querySelector('.cover-remove').addEventListener('click', () => {
                        gambarTerpilih.splice(i, 1);
                        syncInputGambar();
                        renderGambarTerpilih();
                    });

                    card.addEventListener('dragstart', () => {
                        dragDari = i;
                        card.classList.add('dragging');
                    });
                    card.addEventListener('dragend', () => {
                        dragDari = null;
                        grid.querySelectorAll('.cover-item').forEach(c => {
                            c.classList.remove('dragging', 'drag-target');
                        });
                    });
                    card.addEventListener('dragover', e => {
                        e.preventDefault();
                        if (dragDari !== null && dragDari !== i) card.classList.add('drag-target');
                    });
                    card.addEventListener('dragleave', () => card.classList.remove('drag-target'));
                    card.addEventListener('drop', e => {
                        e.preventDefault();
                        if (dragDari === null || dragDari === i) return;
                        const [dipindah] = gambarTerpilih.splice(dragDari, 1);
                        gambarTerpilih.splice(i, 0, dipindah);
                        syncInputGambar();
                        renderGambarTerpilih();
                    });

                    grid.appendChild(card);
                });
            }

            posterInput.addEventListener('change', function (e) {
                // Input di-reset tiap render, jadi file baru ditambahkan ke daftar
                // (bukan menggantikan) dengan penjagaan duplikat.
                const kunci = new Set(gambarTerpilih.map(f => f.name + f.size + f.lastModified));
                let ditolak = 0;

                Array.from(e.target.files).forEach(f => {
                    const k = f.name + f.size + f.lastModified;
                    if (kunci.has(k)) return;
                    if (gambarTerpilih.length >= MAX_GAMBAR) { ditolak++; return; }
                    gambarTerpilih.push(f);
                    kunci.add(k);
                });

                if (ditolak > 0) {
                    mkNotify({ title: 'Batas Gambar Tercapai', message: 'Maksimal ' + MAX_GAMBAR + ' gambar. ' + ditolak + ' gambar terakhir tidak ditambahkan.', variant: 'warning' });
                }

                // Gambar baru akan menggantikan gambar yang tersimpan di draf.
                if (gambarTerpilih.length) {
                    document.getElementById('posterSavedNote').style.display = 'none';
                    document.getElementById('posterSavedList').innerHTML = '';
                }

                syncInputGambar();
                renderGambarTerpilih();
            });

            // Lampiran file list
            document.getElementById('lampiranInput').addEventListener('change', function (e) {
                const list = document.getElementById('lampiranList');
                list.innerHTML = '';
                Array.from(e.target.files).forEach((file, i) => {
                    const item = document.createElement('div');
                    item.className = 'file-item';
                    item.innerHTML = `
                        <span class="file-name">
                            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="#666D80"
                                stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M14.5 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V7.5L14.5 2z"/>
                                <polyline points="14 2 14 8 20 8"/>
                            </svg>
                            ${file.name}
                        </span>
                        <span style="color: #808897; font-size: 11.5px;">${(file.size / 1024 / 1024).toFixed(2)} MB</span>
                    `;
                    list.appendChild(item);
                });
            });

            // Drag-over styling
            ['posterZone', 'lampiranZone'].forEach(id => {
                const zone = document.getElementById(id);
                zone.addEventListener('dragover', (e) => { e.preventDefault(); zone.classList.add('drag-over'); });
                zone.addEventListener('dragleave', () => zone.classList.remove('drag-over'));
                zone.addEventListener('drop', () => zone.classList.remove('drag-over'));
            });

            // ---- Draft Auto-Save Logic ----
            let draftTimer;
            const DRAFT_DELAY = 60000; // 1 menit

            const formInputs = document.querySelectorAll('input[name="judul"], select[name="kategori"], select[name="target_audience"]');

            formInputs.forEach(input => {
                input.addEventListener('input', () => {
                    clearTimeout(draftTimer);
                    document.getElementById('draftStatus').style.display = 'none';
                    draftTimer = setTimeout(saveDraftAJAX, DRAFT_DELAY);
                });
            });

            // For rich text editor
            document.getElementById('editorContent').addEventListener('input', () => {
                syncEditorContent();
                clearTimeout(draftTimer);
                document.getElementById('draftStatus').style.display = 'none';
                draftTimer = setTimeout(saveDraftAJAX, DRAFT_DELAY);
            });

            function saveDraftManual() {
                saveDraftAJAX(true);
            }

            function saveDraftAJAX(isManual = false) {
                const draftStatus = document.getElementById('draftStatus');
                draftStatus.style.display = 'inline';
                draftStatus.textContent = 'Menyimpan draf...';

                // Ensure hidden content is synced
                syncEditorContent();

                const formData = new FormData(document.getElementById('createForm'));

                fetch('{{ route("manajemenmahasiswa.pengumuman.drafts.store") }}', {
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
                        DRAFT_ATTACHMENTS[data.draft_id] = data.attachments;
                        renderDraftAttachments(data.attachments);

                        // File sudah tersimpan di server — kosongkan input supaya
                        // auto-save berikutnya tidak mengupload ulang file yang sama.
                        gambarTerpilih = [];
                        posterInput.value = '';
                        renderGambarTerpilih();
                        document.getElementById('lampiranInput').value = '';
                        document.getElementById('lampiranList').innerHTML = '';

                        draftStatus.textContent = 'Draf tersimpan.';
                        setTimeout(() => { draftStatus.style.display = 'none'; }, 3000);
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

            function loadDraft(id, judul, kategori, target_audience, konten) {
                document.getElementById('draft_id').value = id;
                document.querySelector('input[name="judul"]').value = judul || '';

                const selectKategori = document.querySelector('select[name="kategori"]');
                if (kategori) {
                    selectKategori.value = kategori;
                } else {
                    selectKategori.value = '';
                }

                const selectAudience = document.querySelector('select[name="target_audience"]');
                if (target_audience) {
                    selectAudience.value = target_audience;
                } else {
                    selectAudience.value = 'all'; // default
                }

                document.getElementById('editorContent').innerHTML = konten || '';
                syncEditorContent();

                // Gambar & lampiran yang ikut tersimpan di draf ini
                gambarTerpilih = [];
                posterInput.value = '';
                renderGambarTerpilih();
                document.getElementById('lampiranInput').value = '';
                document.getElementById('lampiranList').innerHTML = '';
                renderDraftAttachments(DRAFT_ATTACHMENTS[id] ?? null);

                // Hide modal via Bootstrap API if available
                if (typeof bootstrap !== 'undefined') {
                    const modalEl = document.getElementById('draftsModal');
                    const modal = bootstrap.Modal.getInstance(modalEl);
                    if (modal) modal.hide();
                }
            }

            // ── Rich Text Editor ──────────────────────────────────────────
            const editor = document.getElementById('editorContent');
            const kontenHidden = document.getElementById('kontenHidden');

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
                // toggle: if already in same block, switch back to p
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
                    try {
                        btn.classList.toggle('active', document.queryCommandState(btn.dataset.cmd));
                    } catch (e) {}
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
                    `<span id="${placeholderId}" style="color:#666D80;font-style:italic;font-size:0.9em;">[Mengupload gambar...]</span>`);
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
                        mkNotify({ title: 'Gagal Mengunggah', message: 'Gagal mengupload gambar. Silakan coba lagi.', variant: 'danger' });
                    }
                } catch (err) {
                    const placeholder = document.getElementById(placeholderId);
                    if (placeholder) placeholder.remove();
                    mkNotify({ title: 'Gagal Mengunggah', message: 'Gagal mengupload gambar. Periksa koneksi internet Anda.', variant: 'danger' });
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

            // Sync on every input/selection change
            editor.addEventListener('input', syncEditorContent);
            editor.addEventListener('keyup', updateToolbarState);
            editor.addEventListener('mouseup', updateToolbarState);

            // Sync before submit
            document.getElementById('createForm').addEventListener('submit', function (e) {
                syncEditorContent();
                const content = kontenHidden.value.replace(/<[^>]*>/g, '').trim();
                if (content.length < 10) {
                    e.preventDefault();
                    editor.focus();
                    document.getElementById('editorWrapper').style.borderColor = '#DF1C41';
                    document.getElementById('editorWrapper').style.boxShadow = '0 0 0 3px rgba(223,28,65,0.12)';
                    return;
                }
                document.getElementById('editorWrapper').style.borderColor = '';
                document.getElementById('editorWrapper').style.boxShadow = '';
            });

            // Initial state
            syncEditorContent();
            updateToolbarState();
        </script>
    @endpush

</x-manajemenmahasiswa::layouts.admin>
