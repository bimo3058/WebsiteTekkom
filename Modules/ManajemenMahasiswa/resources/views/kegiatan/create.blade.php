<x-manajemenmahasiswa::layouts.mahasiswa>
@include('manajemenmahasiswa::partials.card-frame')

@include('manajemenmahasiswa::partials.kegiatan-theme')

<style>
    /* ── Form Card ── */
    .form-card {
        background: var(--c-surface);
        border-radius: 12px;
        padding: 24px;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05), 0 2px 4px -1px rgba(0, 0, 0, 0.03);
        margin-bottom: 20px;
    }
    .form-card-title {
        font-weight: 700;
        font-size: 16px;
        color: var(--c-fg);
        margin-bottom: 20px;
        display: flex;
        align-items: center;
        gap: 8px;
        padding-bottom: 14px;
        border-bottom: 1px solid var(--c-surface-muted);
    }

    /* ── Label & kotak isian: partials/sitkom-ui (gaya Edit User SITKOM) ── */
    textarea.form-control-custom {
        min-height: 140px;
        resize: vertical;
    }
    /* Kolom angka (Peserta & Anggaran) tanpa tombol panah naik/turun bawaan browser. */
    input[type="number"].form-control-custom {
        -moz-appearance: textfield;
        appearance: textfield;
    }
    input[type="number"].form-control-custom::-webkit-outer-spin-button,
    input[type="number"].form-control-custom::-webkit-inner-spin-button {
        -webkit-appearance: none;
        margin: 0;
    }

    /* ── Searchable Select ── */
    .search-select-wrapper {
        position: relative;
    }
    .search-select-wrapper input[type="text"] {
        width: 100%;
    }
    .search-select-dropdown {
        position: absolute;
        top: 100%;
        left: 0;
        right: 0;
        background: var(--c-surface);
        border: 1.5px solid var(--c-border);
        border-top: none;
        border-radius: 0 0 10px 10px;
        max-height: 200px;
        overflow-y: auto;
        z-index: 100;
        display: none;
        box-shadow: 0 8px 25px rgba(0,0,0,0.1);
    }
    .search-select-dropdown.show {
        display: block;
    }
    .search-select-option {
        padding: 10px 14px;
        cursor: pointer;
        font-size: 13px;
        font-weight: 500;
        color: var(--c-fg-sec);
        transition: background 0.15s;
        border-bottom: 1px solid var(--c-surface-subtle);
    }
    .search-select-option:hover {
        background: var(--c-primary-subtle);
        color: var(--c-primary);
    }
    .search-select-option .sub-text {
        font-size: 11px;
        color: var(--c-fg-muted);
        font-weight: 400;
    }

    /* ── Checkbox Card Group ── */
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
        border: 1.5px solid var(--c-border);
        border-radius: 10px;
        background: var(--c-surface);
        cursor: pointer;
        transition: all 0.2s;
        font-size: 13px;
        font-weight: 500;
        color: var(--c-fg-sec);
        user-select: none;
    }
    .checkbox-card:hover {
        border-color: var(--c-primary-border);
        background: var(--c-primary-subtle);
    }
    .checkbox-card input[type="checkbox"] {
        width: 16px;
        height: 16px;
        accent-color: var(--c-primary);
        cursor: pointer;
        flex-shrink: 0;
    }
    .checkbox-card.checked {
        border-color: var(--c-primary);
        background: var(--c-primary-subtle);
        color: var(--c-primary-hover);
        font-weight: 600;
    }
    .checkbox-card.disabled {
        opacity: 0.45;
        cursor: not-allowed;
        pointer-events: none;
    }
    .checkbox-hint {
        font-size: 11px;
        color: var(--c-fg-muted);
        font-weight: 400;
        margin-top: 6px;
    }

    /* ── Banner Preview ── */
    .banner-upload-area {
        border: 2px dashed var(--c-border-strong);
        border-radius: 12px;
        padding: 30px;
        text-align: center;
        cursor: pointer;
        transition: all 0.2s;
        background: var(--c-surface-subtle);
    }
    .banner-upload-area:hover {
        border-color: var(--c-primary);
        background: var(--c-primary-subtle);
    }
    .banner-upload-area .upload-icon {
        font-size: 36px;
        margin-bottom: 8px;
        opacity: 0.5;
    }
    .banner-upload-area p {
        color: var(--c-fg-muted);
        font-size: 13px;
        font-weight: 500;
        margin: 0;
    }
    .banner-upload-area small {
        color: var(--c-fg-muted);
        font-size: 12px;
    }
    .banner-preview {
        width: 100%;
        max-height: 220px;
        object-fit: cover;
        border-radius: 10px;
        margin-top: 12px;
        display: none;
        cursor: pointer;
        transition: transform 0.2s;
    }
    .banner-preview:hover {
        transform: scale(1.01);
        box-shadow: 0 4px 12px rgba(0,0,0,0.1);
    }
    /* ── Lightbox Modal ── */
    .lightbox-modal {
        display: none;
        position: fixed;
        inset: 0;
        z-index: 10000;
        background: rgba(0, 0, 0, 0.92);
        align-items: center;
        justify-content: center;
        animation: lightboxFadeIn 0.25s ease;
    }
    .lightbox-modal.active {
        display: flex;
    }
    @keyframes lightboxFadeIn {
        from { opacity: 0; }
        to { opacity: 1; }
    }
    .lightbox-content {
        position: relative;
        max-width: 90vw;
        max-height: 85vh;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    .lightbox-content img {
        max-width: 90vw;
        max-height: 82vh;
        object-fit: contain;
        border-radius: 8px;
        box-shadow: 0 25px 60px rgba(0, 0, 0, 0.4);
        animation: lightboxZoomIn 0.3s ease;
    }
    @keyframes lightboxZoomIn {
        from { transform: scale(0.9); opacity: 0; }
        to { transform: scale(1); opacity: 1; }
    }
    .lightbox-close {
        position: fixed;
        top: 20px;
        right: 24px;
        width: 44px;
        height: 44px;
        border-radius: 50%;
        background: rgba(255,255,255,0.1);
        backdrop-filter: blur(8px);
        border: 1px solid rgba(255,255,255,0.15);
        color: var(--c-surface);
        font-size: 20px;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: all 0.2s;
        z-index: 10001;
    }
    .lightbox-close:hover {
        background: rgba(255,255,255,0.2);
        transform: scale(1.05);
    }

    /* ── Multi File Upload ── */
    .file-upload-area {
        border: 2px dashed var(--c-border-strong);
        border-radius: 12px;
        padding: 24px;
        text-align: center;
        cursor: pointer;
        transition: all 0.2s;
        background: var(--c-surface-subtle);
    }
    .file-upload-area:hover,
    .file-upload-area.dragover {
        border-color: var(--c-primary);
        background: var(--c-primary-subtle);
    }
    .file-upload-area .upload-icon {
        font-size: 28px;
        margin-bottom: 6px;
        opacity: 0.5;
    }
    .file-upload-area p {
        color: var(--c-fg-muted);
        font-size: 13px;
        font-weight: 500;
        margin: 0;
    }
    .file-upload-area small {
        color: var(--c-fg-muted);
        font-size: 12px;
    }
    .file-preview-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(140px, 1fr));
        gap: 12px;
        margin-top: 14px;
    }
    .file-preview-item {
        position: relative;
        border-radius: 10px;
        overflow: hidden;
        border: 1px solid var(--c-border);
        background: var(--c-surface-subtle);
        transition: all 0.2s;
    }
    .file-preview-item img {
        width: 100%;
        height: 100px;
        object-fit: cover;
    }
    .file-preview-item .file-info {
        padding: 8px 10px;
        font-size: 11px;
        font-weight: 600;
        color: var(--c-fg-sec);
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }
    .file-preview-item .file-size {
        font-size: 10px;
        color: var(--c-fg-muted);
        font-weight: 400;
    }
    /* Hanya posisi; warna & ukuran dari .mk-btn. */
    .file-preview-item .btn-remove-file {
        position: absolute;
        top: 4px;
        right: 4px;
    }
    .doc-preview-item {
        display: flex;
        align-items: center;
        gap: 10px;
        padding: 12px 14px;
        border: 1px solid var(--c-border);
        border-radius: 10px;
        background: var(--c-surface-subtle);
        position: relative;
        margin-bottom: 8px;
    }
    .doc-preview-item .doc-icon {
        font-size: 24px;
        flex-shrink: 0;
    }
    .doc-preview-item .doc-info {
        flex: 1;
        min-width: 0;
    }
    .doc-preview-item .doc-name {
        font-size: 13px;
        font-weight: 600;
        color: var(--c-fg-sec);
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }
    .doc-preview-item .doc-size {
        font-size: 11px;
        color: var(--c-fg-muted);
    }
    .doc-preview-item .btn-remove-doc {
        flex-shrink: 0;
    }

    /* ── Back Button ── */
    .btn-back {
        width: 32px;
        min-width: 32px;
        height: 32px;
        padding: 0;
        border-radius: 8px;
        background: var(--c-surface);
        border: 1px solid var(--c-border);
        display: flex;
        align-items: center;
        justify-content: center;
        text-decoration: none;
        color: var(--c-fg-sec);
        box-shadow: 0 1px 2px rgba(0, 0, 0, .05);
        transition: all 0.2s;
        flex-shrink: 0;
    }
    .btn-back:hover {
        background: var(--c-bg);
        border-color: var(--c-border);
        color: var(--c-fg);
        transform: none;
    }

    /* ── Buttons ── */
    .btn-submit {
        background: var(--c-primary);
        color: var(--c-surface);
        font-weight: 600;
        font-size: 14px;
        min-height: 40px;
        padding: 0 18px;
        border-radius: 10px;
        border: none;
        cursor: pointer;
        transition: all 0.2s;
    }
    .btn-submit:hover {
        background: var(--c-primary-hover);
        transform: translateY(-1px);
        box-shadow: 0 4px 12px var(--c-primary-shadow-strong);
    }
    .btn-cancel {
        background: var(--c-surface);
        color: var(--c-fg-sec);
        font-weight: 600;
        font-size: 14px;
        min-height: 40px;
        padding: 0 18px;
        border-radius: 10px;
        border: 1px solid var(--c-border);
        cursor: pointer;
        text-decoration: none;
        transition: all 0.2s;
    }
    .btn-cancel:hover {
        background: var(--c-primary-subtle);
        border-color: var(--c-primary);
        color: var(--c-primary);
    }

    /* ── Multi-Select Panitia ── */
    .panitia-select-wrapper {
        position: relative;
    }
    .panitia-chips-container {
        display: flex;
        flex-wrap: wrap;
        gap: 8px;
        min-height: 44px;
        padding: 8px 12px;
        border: 1.5px solid var(--c-border);
        border-radius: 10px;
        background: var(--c-surface);
        cursor: text;
        transition: border-color 0.2s, box-shadow 0.2s;
        align-items: center;
    }
    .panitia-chips-container:focus-within {
        border-color: var(--c-primary);
        box-shadow: 0 0 0 3px var(--c-primary-subtle);
    }
    .panitia-chip {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 4px 10px;
        background: var(--c-primary-subtle);
        color: var(--c-primary-hover);
        border-radius: 20px;
        font-size: 12px;
        font-weight: 600;
        border: 1px solid var(--c-primary-border);
        transition: all 0.15s;
        white-space: nowrap;
    }
    .panitia-chip:hover {
        background: var(--c-primary-subtle);
    }
    .panitia-chip-remove {
        width: 16px;
        height: 16px;
        border-radius: 50%;
        background: transparent;
        color: var(--c-primary);
        border: 1px solid transparent;
        font-size: 11px;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        line-height: 1;
        padding: 0;
        transition: all 0.15s;
        flex-shrink: 0;
    }
    .panitia-chip-remove:hover {
        background: var(--c-primary);
        color: #fff;
    }
    .panitia-search-input {
        border: none;
        outline: none;
        font-size: 13px;
        font-weight: 500;
        color: var(--c-fg);
        flex: 1;
        min-width: 120px;
        background: transparent;
        padding: 2px 0;
    }
    .panitia-search-input::placeholder {
        color: var(--c-fg-muted);
        font-weight: 400;
    }
    .panitia-dropdown {
        position: absolute;
        top: 100%;
        left: 0;
        right: 0;
        background: var(--c-surface);
        border: 1.5px solid var(--c-border);
        border-top: none;
        border-radius: 0 0 10px 10px;
        max-height: 220px;
        overflow-y: auto;
        z-index: 200;
        display: none;
        box-shadow: 0 8px 25px rgba(0,0,0,0.1);
    }
    .panitia-dropdown.show {
        display: block;
    }
    .panitia-option {
        padding: 10px 14px;
        cursor: pointer;
        font-size: 13px;
        font-weight: 500;
        color: var(--c-fg-sec);
        transition: background 0.15s;
        border-bottom: 1px solid var(--c-surface-subtle);
        display: flex;
        align-items: center;
        gap: 8px;
    }
    .panitia-option:hover {
        background: var(--c-primary-subtle);
        color: var(--c-primary);
    }
    .panitia-option.selected {
        background: var(--c-success-subtle);
        color: var(--c-success);
        pointer-events: none;
        opacity: 0.6;
    }
    .panitia-option .sub-text {
        font-size: 11px;
        color: var(--c-fg-muted);
        font-weight: 400;
    }
    .panitia-option .check-icon {
        margin-left: auto;
        color: var(--c-success);
        font-size: 13px;
        display: none;
    }
    .panitia-option.selected .check-icon {
        display: inline;
    }
    .panitia-empty {
        padding: 14px;
        text-align: center;
        font-size: 13px;
        color: var(--c-fg-muted);
        font-weight: 400;
    }
    .panitia-count-badge {
        font-size: 11px;
        font-weight: 600;
        color: var(--c-fg-muted);
        background: var(--c-surface-muted);
        padding: 2px 8px;
        border-radius: 20px;
        margin-left: 6px;
    }
</style>

<!-- Header -->
<x-manajemenmahasiswa::ui.page-header bordered
    title="Tambah Kegiatan Baru"
    subtitle="Isi formulir berikut untuk menambahkan kegiatan baru">
    <x-slot:leading>
        <a href="{{ route('manajemenmahasiswa.kegiatan.index') }}" class="btn-back mk-btn mk-btn--secondary mk-btn--icon mk-btn--sm" aria-label="Kembali">
            <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path d="M15 19l-7-7 7-7"/></svg>
        </a>
    </x-slot:leading>
</x-manajemenmahasiswa::ui.page-header>

<!-- Validation Errors -->
<x-manajemenmahasiswa::ui.flash type="error" title="Terjadi Kesalahan" :messages="$errors->all()" class="mb-3" />

<form action="{{ route('manajemenmahasiswa.kegiatan.store') }}" method="POST" enctype="multipart/form-data">
    @csrf

    <!-- Info Utama -->
    <div class="form-card">
        <div class="form-card-title"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="vertical-align: -2px;"><path d="M16 4h2a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h2"></path><rect x="8" y="2" width="8" height="4" rx="1" ry="1"></rect></svg> Informasi Utama</div>

        <div class="mb-3">
            <label class="form-label-custom">Judul Kegiatan <span class="required">*</span></label>
            <input type="text" name="judul" id="judulInput" class="form-control form-control-custom"
                   placeholder="Contoh: Seminar Nasional IT 2026" value="{{ old('judul') }}" required maxlength="255"
                   oninput="updateCharCount('judulInput','judulCount',255)">
            <div style="font-size:11px;color:var(--c-fg-muted);text-align:right;margin-top:4px;font-weight:500;"><span id="judulCount">0</span>/255 karakter</div>
        </div>

        <div class="row g-3 mb-3">
            <div class="col-md-6">
                <label class="form-label-custom">Kategori <span class="required">*</span></label>
                <div class="checkbox-card-group" id="kategoriGroup">
                    @foreach($kategoriList as $kategori)
                        <label class="checkbox-card" id="kategoriCard{{ $kategori->id }}">
                            <input type="checkbox" name="kategori_kegiatan_id[]"
                                   value="{{ $kategori->id }}"
                                   data-is-prodi="{{ stripos($kategori->nama_kategori, 'prodi') !== false ? '1' : '0' }}"
                                   onchange="handleKategoriChange()"
                                   {{ is_array(old('kategori_kegiatan_id')) && in_array($kategori->id, old('kategori_kegiatan_id')) ? 'checked' : '' }}>
                            {{ $kategori->nama_kategori }}
                        </label>
                    @endforeach
                </div>
                <div class="checkbox-hint">Pilih maksimal 2 kategori</div>
            </div>
            <div class="col-md-6" id="bidangFieldWrapper">
                <label class="form-label-custom">Bidang <span class="required" id="bidangRequired">*</span></label>
                <div class="checkbox-card-group" id="bidangGroup">
                    @foreach($bidangList as $bidang)
                        <label class="checkbox-card" id="bidangCard{{ $bidang->id }}">
                            <input type="checkbox" name="bidang_id[]"
                                   value="{{ $bidang->id }}"
                                   {{ is_array(old('bidang_id')) && in_array($bidang->id, old('bidang_id')) ? 'checked' : '' }}>
                            {{ $bidang->nama_bidang }}
                        </label>
                    @endforeach
                </div>
                <div class="checkbox-hint">Pilih satu atau lebih bidang</div>
            </div>
        </div>

        <div class="mb-3">
            <label class="form-label-custom">Deskripsi <span class="required">*</span></label>
            <textarea name="deskripsi" id="deskripsiInput" class="form-control form-control-custom"
                      placeholder="Jelaskan tujuan, sasaran, dan detail kegiatan secara lengkap..."
                      required minlength="20" maxlength="3000"
                      oninput="updateCharCount('deskripsiInput','deskripsiCount',3000)">{{ old('deskripsi') }}</textarea>
            <div class="d-flex justify-content-between align-items-center" style="margin-top:4px;">
                <span style="font-size:11px;color:var(--c-fg-muted);font-weight:500;">Minimal 20 karakter</span>
                <span style="font-size:11px;color:var(--c-fg-muted);font-weight:500;"><span id="deskripsiCount">0</span>/3000 karakter</span>
            </div>
        </div>

    </div>

    <!-- Waktu & Lokasi -->
    <div class="form-card">
        <div class="form-card-title"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="vertical-align: -2px;"><rect width="18" height="18" x="3" y="4" rx="2" ry="2"></rect><line x1="16" x2="16" y1="2" y2="6"></line><line x1="8" x2="8" y1="2" y2="6"></line><line x1="3" x2="21" y1="10" y2="10"></line></svg> Waktu & Lokasi</div>

        <div class="row g-3 mb-3">
            <div class="col-md-3">
                <label class="form-label-custom">Tanggal Mulai <span class="required">*</span></label>
                <input type="date" name="tanggal_mulai" class="form-control form-control-custom"
                       value="{{ old('tanggal_mulai') }}" required>
            </div>
            <div class="col-md-3">
                <label class="form-label-custom">Jam Mulai</label>
                <input type="time" name="jam_mulai" class="form-control form-control-custom"
                       value="{{ old('jam_mulai') }}">
            </div>
            <div class="col-md-3">
                <label class="form-label-custom">Tanggal Selesai</label>
                <input type="date" name="tanggal_selesai" class="form-control form-control-custom"
                       value="{{ old('tanggal_selesai') }}">
            </div>
            <div class="col-md-3">
                <label class="form-label-custom">Jam Selesai</label>
                <input type="time" name="jam_selesai" class="form-control form-control-custom"
                       value="{{ old('jam_selesai') }}">
            </div>
        </div>

        <div class="mb-3">
            <label class="form-label-custom">Lokasi</label>
            <input type="text" name="lokasi" class="form-control form-control-custom"
                   placeholder="Contoh: Gedung A Lantai 3, Undip Tembalang" value="{{ old('lokasi') }}">
        </div>
    </div>

    <!-- Personel -->
    <div class="form-card">
        <div class="form-card-title"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="vertical-align: -2px;"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="9" cy="7" r="4"></circle><path d="M23 21v-2a4 4 0 0 0-3-3.87"></path><path d="M16 3.13a4 4 0 0 1 0 7.75"></path></svg> Personel Kegiatan</div>

        @include('manajemenmahasiswa::partials.kegiatan-form._personel', [
            'ketuaId'          => old('ketua_pelaksana_id'),
            'dosenTerpilih'    => $dosenList->whereIn('id', old('dosen_pendamping_ids', [])),
            'panitiaTerpilih'  => $mahasiswaList->whereIn('id', old('panitia_ids', [])),
            'panitiaPeranLama' => old('panitia_peran', []),
        ])
    </div>

    {{-- Akses Kelola — hanya dirender untuk pemilik kegiatan & override --}}
    @include('manajemenmahasiswa::partials.kegiatan-form._akses_kelola')

    <!-- Detail Tambahan -->
    <div class="form-card">
        <div class="form-card-title"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="vertical-align: -2px;"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path></svg> Detail Tambahan</div>

        <div class="row g-3 mb-3">
            <div class="col-md-6">
                <label class="form-label-custom">Peserta</label>
                <input type="number" name="target_peserta" class="form-control form-control-custom"
                       placeholder="Jumlah peserta" value="{{ old('target_peserta') }}" min="1">
            </div>
            <div class="col-md-6">
                <label class="form-label-custom">Anggaran (Rp)</label>
                {{-- step="any": step="1000" membuat browser diam-diam menolak angka yang
                     bukan kelipatan seribu (mis. 750500) tanpa keterangan apa pun di form. --}}
                <input type="number" name="anggaran" class="form-control form-control-custom"
                       placeholder="Contoh: 5000000" value="{{ old('anggaran') }}" min="0" max="9999999999999" step="any">
            </div>
        </div>
    </div>

    <!-- Banner -->
    <div class="form-card">
        <div class="form-card-title"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="vertical-align: -2px;"><rect width="18" height="18" x="3" y="3" rx="2" ry="2"></rect><circle cx="9" cy="9" r="2"></circle><path d="m21 15-3.086-3.086a2 2 0 0 0-2.828 0L6 21"></path></svg> Banner Kegiatan *</div>

        <div class="banner-upload-area" onclick="document.getElementById('bannerInput').click()">
            <div class="upload-icon"><svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect width="18" height="18" x="3" y="3" rx="2" ry="2"></rect><circle cx="9" cy="9" r="2"></circle><path d="m21 15-3.086-3.086a2 2 0 0 0-2.828 0L6 21"></path></svg></div>
            <p>Klik untuk upload banner kegiatan</p>
            <small>Format: JPG, PNG, WebP • Maks: 5MB<br><span style="color: var(--c-primary); font-weight: 500;">Rekomendasi: Resolusi 1280 x 720 (Rasio 16:9)</span></small>
        </div>
        <input type="file" name="banner" id="bannerInput" accept="image/jpeg,image/png,image/webp"
               style="display: none;" onchange="previewBanner(this)">
        <img id="bannerPreview" class="banner-preview" alt="Preview Banner" onclick="openLightbox(this.src)" title="Klik untuk memperbesar">
    </div>

    <!-- Foto Kegiatan -->
    <div class="form-card">
        <div class="form-card-title"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="vertical-align: -2px;"><path d="M23 19a2 2 0 0 1-2 2H3a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h4l2-3h6l2 3h4a2 2 0 0 1 2 2z"></path><circle cx="12" cy="13" r="4"></circle></svg> Foto Kegiatan <span style="color: var(--c-fg-muted); font-weight: 400; font-size: 13px;">(opsional, maks 10 foto)</span></div>

        <div class="file-upload-area" id="fotoUploadArea" onclick="document.getElementById('fotoInput').click()">
            <div class="upload-icon"><svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect width="18" height="18" x="3" y="3" rx="2" ry="2"></rect><circle cx="9" cy="9" r="2"></circle><path d="m21 15-3.086-3.086a2 2 0 0 0-2.828 0L6 21"></path></svg></div>
            <p>Klik atau drag & drop foto ke sini</p>
            <small>Format: JPG, PNG, WebP • Maks: 5MB per file</small>
        </div>
        <input type="file" name="foto_kegiatan[]" id="fotoInput" accept="image/jpeg,image/png,image/webp"
               multiple style="display: none;" onchange="handleFotoSelect(this)">
        <div class="file-preview-grid" id="fotoPreviewGrid"></div>
    </div>

    <!-- Dokumen Kegiatan -->
    <div class="form-card">
        <div class="form-card-title"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="vertical-align: -2px;"><path d="M14.5 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V7.5L14.5 2z"></path><polyline points="14 2 14 8 20 8"></polyline></svg> Dokumen Kegiatan <span style="color: var(--c-fg-muted); font-weight: 400; font-size: 13px;">(opsional, maks 2 dokumen)</span></div>

        <div class="file-upload-area" id="dokumenUploadArea" onclick="document.getElementById('dokumenInput').click()">
            <div class="upload-icon"><svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m21.44 11.05-9.19 9.19a6 6 0 0 1-8.49-8.49l9.19-9.19a4 4 0 0 1 5.66 5.66l-9.2 9.19a2 2 0 0 1-2.83-2.83l8.49-8.48"></path></svg></div>
            <p>Klik atau drag & drop dokumen ke sini</p>
            <small>Format: PDF, DOC, DOCX, XLS, XLSX, PPT, PPTX • Maks: 5MB per file</small>
        </div>
        <input type="file" name="dokumen_kegiatan[]" id="dokumenInput"
               accept=".pdf,.doc,.docx,.xls,.xlsx,.ppt,.pptx"
               multiple style="display: none;" onchange="handleDokumenSelect(this)">
        <div id="dokumenPreviewList"></div>
    </div>

    <!-- Action Buttons -->
    <div class="d-flex gap-3 justify-content-end mt-2">
        <a href="{{ route('manajemenmahasiswa.kegiatan.index') }}" class="mk-btn mk-btn--secondary">Batal</a>
        <button type="submit" class="mk-btn mk-btn--primary">
            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="vertical-align:-3px;margin-right:6px;"><path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v14a2 2 0 0 1-2 2z"/><polyline points="17 21 17 13 7 13 7 21"/><polyline points="7 3 7 8 15 8"/></svg>Simpan Kegiatan
        </button>
    </div>
</form>

<!-- Lightbox Modal -->
<div class="lightbox-modal" id="lightboxModal" onclick="closeLightbox(event)">
    <button class="lightbox-close" onclick="closeLightbox(event)" title="Tutup">&times;</button>
    <div class="lightbox-content">
        <img id="lightboxImage" src="" alt="Full Screen Banner">
    </div>
</div>

<script>
// ── Char counter (judul & deskripsi) ──
function updateCharCount(inputId, countId, max) {
    const el  = document.getElementById(inputId);
    const cnt = document.getElementById(countId);
    if (!el || !cnt) return;
    const len = el.value.length;
    cnt.textContent = len;
    cnt.style.color = len >= max ? 'var(--c-error)' : (len > max * 0.9 ? 'var(--c-warning)' : 'var(--c-fg-muted)');
}
document.addEventListener('DOMContentLoaded', () => {
    ['judulInput','deskripsiInput'].forEach(id => {
        const el = document.getElementById(id);
        if (el) el.dispatchEvent(new Event('input'));
    });
});

// ── Banner Preview ──
function previewBanner(input) {
    const preview = document.getElementById('bannerPreview');
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = function(e) {
            preview.src = e.target.result;
            preview.style.display = 'block';
        };
        reader.readAsDataURL(input.files[0]);
    }
}

// ── Lightbox ──
function openLightbox(src) {
    const modal = document.getElementById('lightboxModal');
    const img = document.getElementById('lightboxImage');
    img.src = src;
    modal.classList.add('active');
    document.body.style.overflow = 'hidden'; // prevent scrolling
}

function closeLightbox(e) {
    if (e && e.target !== document.getElementById('lightboxModal') && e.target.tagName !== 'BUTTON') {
        // Allow clicking the image to do nothing, but clicking outside closes
        if (e.target.tagName === 'IMG') return;
    }
    const modal = document.getElementById('lightboxModal');
    modal.classList.remove('active');
    document.body.style.overflow = 'auto';
}

// ── Multi-File Upload: Foto ──
let fotoFiles = [];

function handleFotoSelect(input) {
    const newFiles = Array.from(input.files);
    newFiles.forEach(file => {
        if (fotoFiles.length >= 10) return;
        fotoFiles.push(file);
    });
    renderFotoPreviews();
    syncFotoInput();
}

function removeFoto(index) {
    fotoFiles.splice(index, 1);
    renderFotoPreviews();
    syncFotoInput();
}

function renderFotoPreviews() {
    const grid = document.getElementById('fotoPreviewGrid');
    grid.innerHTML = '';
    fotoFiles.forEach((file, i) => {
        const item = document.createElement('div');
        item.className = 'file-preview-item';
        const reader = new FileReader();
        reader.onload = function(e) {
            item.innerHTML = `
                <button type="button" class="btn-remove-file mk-btn mk-btn--secondary mk-btn--sm mk-btn--icon" onclick="removeFoto(${i})" title="Hapus foto" aria-label="Hapus foto ${file.name}"><svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg></button>
                <img src="${e.target.result}" alt="${file.name}" style="cursor: pointer;" onclick="openLightbox(this.src)" title="Klik untuk memperbesar">
                <div class="file-info">${file.name}<br><span class="file-size">${formatFileSize(file.size)}</span></div>
            `;
        };
        reader.readAsDataURL(file);
        grid.appendChild(item);
    });
}

function syncFotoInput() {
    const dt = new DataTransfer();
    fotoFiles.forEach(f => dt.items.add(f));
    document.getElementById('fotoInput').files = dt.files;
}

// ── Multi-File Upload: Dokumen ──
let dokumenFiles = [];

function handleDokumenSelect(input) {
    const newFiles = Array.from(input.files);
    newFiles.forEach(file => {
        if (dokumenFiles.length >= 2) return;
        dokumenFiles.push(file);
    });
    renderDokumenPreviews();
    syncDokumenInput();
}

function removeDokumen(index) {
    dokumenFiles.splice(index, 1);
    renderDokumenPreviews();
    syncDokumenInput();
}

function renderDokumenPreviews() {
    const list = document.getElementById('dokumenPreviewList');
    list.innerHTML = '';
    dokumenFiles.forEach((file, i) => {
        const ext = file.name.split('.').pop().toLowerCase();
        const icons = { pdf: 'PDF', doc: 'DOC', docx: 'DOC', xls: 'XLS', xlsx: 'XLS', ppt: 'PPT', pptx: 'PPT' };
        const icon = icons[ext] || '\u25A1';
        const item = document.createElement('div');
        item.className = 'doc-preview-item';
        item.innerHTML = `
            <span class="doc-icon">${icon}</span>
            <div class="doc-info">
                <div class="doc-name">${file.name}</div>
                <div class="doc-size">${formatFileSize(file.size)} • ${ext.toUpperCase()}</div>
            </div>
            <button type="button" class="btn-remove-doc mk-btn mk-btn--secondary mk-btn--sm mk-btn--icon" onclick="removeDokumen(${i})" title="Hapus dokumen" aria-label="Hapus dokumen ${file.name}"><svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg></button>
        `;
        list.appendChild(item);
    });
}

function syncDokumenInput() {
    const dt = new DataTransfer();
    dokumenFiles.forEach(f => dt.items.add(f));
    document.getElementById('dokumenInput').files = dt.files;
}

// ── Helper ──
function formatFileSize(bytes) {
    if (bytes < 1024) return bytes + ' B';
    if (bytes < 1048576) return (bytes / 1024).toFixed(1) + ' KB';
    return (bytes / 1048576).toFixed(1) + ' MB';
}

// ── Drag & Drop ──
['fotoUploadArea', 'dokumenUploadArea'].forEach(id => {
    const area = document.getElementById(id);
    if (!area) return;
    ['dragenter', 'dragover'].forEach(evt => {
        area.addEventListener(evt, e => { e.preventDefault(); area.classList.add('dragover'); });
    });
    ['dragleave', 'drop'].forEach(evt => {
        area.addEventListener(evt, e => { e.preventDefault(); area.classList.remove('dragover'); });
    });
    area.addEventListener('drop', e => {
        const files = e.dataTransfer.files;
        if (id === 'fotoUploadArea') {
            Array.from(files).forEach(f => { if (fotoFiles.length < 10 && f.type.startsWith('image/')) fotoFiles.push(f); });
            renderFotoPreviews();
            syncFotoInput();
        } else {
            Array.from(files).forEach(f => { if (dokumenFiles.length < 2) dokumenFiles.push(f); });
            renderDokumenPreviews();
            syncDokumenInput();
        }
    });
});

// ── Toggle Bidang Field based on Kategori (checkbox version) ──
function handleKategoriChange() {
    const checkboxes = document.querySelectorAll('#kategoriGroup input[type="checkbox"]');
    const checked = document.querySelectorAll('#kategoriGroup input[type="checkbox"]:checked');
    const maxKategori = 2;

    // Enforce max 2 selections
    checkboxes.forEach(cb => {
        const card = cb.closest('.checkbox-card');
        if (cb.checked) {
            card.classList.add('checked');
        } else {
            card.classList.remove('checked');
        }
    });

    if (checked.length >= maxKategori) {
        checkboxes.forEach(cb => {
            if (!cb.checked) {
                cb.closest('.checkbox-card').classList.add('disabled');
                cb.disabled = true;
            }
        });
    } else {
        checkboxes.forEach(cb => {
            cb.closest('.checkbox-card').classList.remove('disabled');
            cb.disabled = false;
        });
    }

    // Toggle bidang visibility
    toggleBidangField();
}

function toggleBidangField() {
    const checked = document.querySelectorAll('#kategoriGroup input[type="checkbox"]:checked');
    const bidangRequired = document.getElementById('bidangRequired');
    const bidangWrapper = document.getElementById('bidangFieldWrapper');

    // "Hanya Prodi" = ada kategori terpilih DAN semuanya prodi (tidak ada Himpunan)
    let allProdi = checked.length > 0;
    checked.forEach(cb => {
        if (cb.getAttribute('data-is-prodi') !== '1') {
            allProdi = false;
        }
    });

    if (allProdi && checked.length > 0) {
        // Hanya Kegiatan Prodi → sembunyikan kolom Bidang & kosongkan pilihannya
        if (bidangWrapper) bidangWrapper.style.display = 'none';
        if (bidangRequired) bidangRequired.style.display = 'none';
        document.querySelectorAll('#bidangGroup input[type="checkbox"]').forEach(cb => {
            if (cb.checked) {
                cb.checked = false;
                cb.dispatchEvent(new Event('change'));
            }
        });
    } else {
        // Ada Kegiatan Himpunan (atau belum memilih) → tampilkan kolom Bidang
        if (bidangWrapper) bidangWrapper.style.display = '';
        if (bidangRequired) bidangRequired.style.display = '';
    }
}

// ── Initialize checkbox card states on page load ──
document.addEventListener('DOMContentLoaded', function() {
    // Set initial 'checked' class on pre-selected cards
    document.querySelectorAll('.checkbox-card input[type="checkbox"]:checked').forEach(cb => {
        cb.closest('.checkbox-card').classList.add('checked');
    });

    // Add change listeners for bidang cards styling
    document.querySelectorAll('#bidangGroup input[type="checkbox"]').forEach(cb => {
        cb.addEventListener('change', function() {
            if (this.checked) {
                this.closest('.checkbox-card').classList.add('checked');
            } else {
                this.closest('.checkbox-card').classList.remove('checked');
            }
        });
    });

    handleKategoriChange();
});
</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</x-manajemenmahasiswa::layouts.mahasiswa>
