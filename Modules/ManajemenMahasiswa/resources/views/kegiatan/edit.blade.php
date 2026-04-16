<x-manajemenmahasiswa::layouts.mahasiswa>

<style>
    /* ── Form Card ── */
    .form-card {
        background: #ffffff;
        border-radius: 12px;
        padding: 24px;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05), 0 2px 4px -1px rgba(0, 0, 0, 0.03);
        margin-bottom: 20px;
    }
    .form-card-title {
        font-weight: 700;
        font-size: 16px;
        color: #1f2937;
        margin-bottom: 20px;
        display: flex;
        align-items: center;
        gap: 8px;
        padding-bottom: 14px;
        border-bottom: 1px solid #f3f4f6;
    }

    /* ── Custom Form Styles ── */
    .form-label-custom {
        font-weight: 600;
        font-size: 13px;
        color: #374151;
        margin-bottom: 6px;
    }
    .form-label-custom .required {
        color: #dc2626;
    }
    .form-control-custom,
    .form-select-custom {
        border: 1.5px solid #e5e7eb;
        border-radius: 10px;
        padding: 10px 14px;
        font-size: 14px;
        font-weight: 500;
        color: #1f2937;
        transition: all 0.2s;
        background: #fff;
    }
    .form-control-custom:focus,
    .form-select-custom:focus {
        border-color: #818cf8;
        box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.1);
        outline: none;
    }
    .form-control-custom::placeholder {
        color: #9ca3af;
        font-weight: 400;
    }
    textarea.form-control-custom {
        min-height: 140px;
        resize: vertical;
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
        background: #fff;
        border: 1.5px solid #e5e7eb;
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
        color: #374151;
        transition: background 0.15s;
        border-bottom: 1px solid #f9fafb;
    }
    .search-select-option:hover {
        background: #eef2ff;
        color: #4f46e5;
    }
    .search-select-option .sub-text {
        font-size: 11px;
        color: #9ca3af;
        font-weight: 400;
    }

    /* ── Banner Preview ── */
    .banner-upload-area {
        border: 2px dashed #d1d5db;
        border-radius: 12px;
        padding: 30px;
        text-align: center;
        cursor: pointer;
        transition: all 0.2s;
        background: #fafafa;
    }
    .banner-upload-area:hover {
        border-color: #818cf8;
        background: #f5f3ff;
    }
    .banner-upload-area .upload-icon {
        font-size: 36px;
        margin-bottom: 8px;
        opacity: 0.5;
    }
    .banner-upload-area p {
        color: #6b7280;
        font-size: 13px;
        font-weight: 500;
        margin: 0;
    }
    .banner-upload-area small {
        color: #9ca3af;
        font-size: 12px;
    }
    .banner-preview {
        width: 100%;
        max-height: 220px;
        object-fit: cover;
        border-radius: 10px;
        margin-top: 12px;
    }
    .banner-current {
        position: relative;
        margin-bottom: 12px;
    }
    .banner-current .badge-current {
        position: absolute;
        top: 10px;
        left: 10px;
        background: rgba(0,0,0,0.6);
        color: #fff;
        font-size: 11px;
        font-weight: 600;
        padding: 4px 10px;
        border-radius: 6px;
    }

    /* ── Multi File Upload ── */
    .file-upload-area {
        border: 2px dashed #d1d5db;
        border-radius: 12px;
        padding: 24px;
        text-align: center;
        cursor: pointer;
        transition: all 0.2s;
        background: #fafafa;
    }
    .file-upload-area:hover,
    .file-upload-area.dragover {
        border-color: #818cf8;
        background: #f5f3ff;
    }
    .file-upload-area .upload-icon {
        font-size: 28px;
        margin-bottom: 6px;
        opacity: 0.5;
    }
    .file-upload-area p {
        color: #6b7280;
        font-size: 13px;
        font-weight: 500;
        margin: 0;
    }
    .file-upload-area small {
        color: #9ca3af;
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
        border: 1px solid #e5e7eb;
        background: #f9fafb;
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
        color: #374151;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }
    .file-preview-item .file-size {
        font-size: 10px;
        color: #9ca3af;
        font-weight: 400;
    }
    .file-preview-item .btn-remove-file {
        position: absolute;
        top: 4px;
        right: 4px;
        width: 22px;
        height: 22px;
        border-radius: 50%;
        background: rgba(220, 38, 38, 0.85);
        color: #fff;
        border: none;
        font-size: 12px;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: all 0.15s;
        line-height: 1;
    }
    .file-preview-item .btn-remove-file:hover {
        background: #dc2626;
        transform: scale(1.1);
    }
    .doc-preview-item {
        display: flex;
        align-items: center;
        gap: 10px;
        padding: 12px 14px;
        border: 1px solid #e5e7eb;
        border-radius: 10px;
        background: #f9fafb;
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
        color: #374151;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }
    .doc-preview-item .doc-size {
        font-size: 11px;
        color: #9ca3af;
    }
    .doc-preview-item .btn-remove-doc {
        width: 26px;
        height: 26px;
        border-radius: 50%;
        background: #fee2e2;
        color: #dc2626;
        border: none;
        font-size: 13px;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
        transition: all 0.15s;
    }
    .doc-preview-item .btn-remove-doc:hover {
        background: #dc2626;
        color: #fff;
    }
    .existing-file-label {
        font-size: 12px;
        font-weight: 700;
        color: #6b7280;
        text-transform: uppercase;
        letter-spacing: 0.3px;
        margin-bottom: 10px;
        margin-top: 4px;
    }

    /* ── Back Button ── */
    .detail-header {
        display: flex;
        align-items: center;
        gap: 14px;
        margin-bottom: 24px;
    }
    .btn-back {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        background: #ffffff;
        border: 1px solid #e5e7eb;
        display: flex;
        align-items: center;
        justify-content: center;
        text-decoration: none;
        color: #374151;
        font-size: 18px;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.06);
        transition: all 0.2s;
        flex-shrink: 0;
    }
    .btn-back:hover {
        background: #f3f4f6;
        border-color: #d1d5db;
        color: #1f2937;
    }

    /* ── Buttons ── */
    .btn-submit {
        background: #4f46e5;
        color: #ffffff;
        font-weight: 600;
        font-size: 14px;
        padding: 12px 28px;
        border-radius: 10px;
        border: none;
        cursor: pointer;
        transition: all 0.2s;
    }
    .btn-submit:hover {
        background: #4338ca;
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(79, 70, 229, 0.3);
    }
    .btn-cancel {
        background: #f3f4f6;
        color: #374151;
        font-weight: 600;
        font-size: 14px;
        padding: 12px 28px;
        border-radius: 10px;
        border: none;
        cursor: pointer;
        text-decoration: none;
        transition: all 0.2s;
    }
    .btn-cancel:hover {
        background: #e5e7eb;
        color: #1f2937;
    }
</style>

@php
    $ketuaNama = '';
    if ($kegiatan->ketua_pelaksana_id) {
        $ketua = $kegiatan->ketuaPelaksana;
        $ketuaNama = $ketua?->user?->name ?? '';
    }
    $dosenNama = '';
    if ($kegiatan->dosen_pendamping_id) {
        $dosen = $kegiatan->dosenPendamping;
        $dosenNama = $dosen?->user?->name ?? '';
    }
    $existingFoto = $kegiatan->repoMulmed->where('tipe_file', 'image');
    $existingDokumen = $kegiatan->repoMulmed->where('tipe_file', 'document');
@endphp

<!-- Header -->
<div class="detail-header">
    <a href="{{ route('manajemenmahasiswa.kegiatan.show', $kegiatan->id) }}" class="btn-back">
        &larr;
    </a>
    <div>
        <h3 class="fw-bold mb-0 text-dark">Edit Kegiatan</h3>
        <p class="text-muted mb-0" style="font-size: 14px; font-weight: 500;">Perbarui informasi kegiatan <strong>{{ $kegiatan->judul }}</strong></p>
    </div>
</div>

<!-- Validation Errors -->
@if($errors->any())
    <div class="alert alert-danger" style="border-radius: 10px; border: none; background: #fee2e2; color: #991b1b; font-size: 14px;">
        <strong>⚠️ Terjadi kesalahan:</strong>
        <ul class="mb-0 mt-1">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<form action="{{ route('manajemenmahasiswa.kegiatan.update', $kegiatan->id) }}" method="POST" enctype="multipart/form-data">
    @csrf
    @method('PUT')

    <!-- Info Utama -->
    <div class="form-card">
        <div class="form-card-title">📋 Informasi Utama</div>

        <div class="mb-3">
            <label class="form-label-custom">Judul Kegiatan <span class="required">*</span></label>
            <input type="text" name="judul" class="form-control form-control-custom"
                   value="{{ old('judul', $kegiatan->judul) }}" required>
        </div>

        <div class="row g-3 mb-3">
            <div class="col-md-4">
                <label class="form-label-custom">Kategori <span class="required">*</span></label>
                <select name="kategori_kegiatan_id" id="kategoriSelect" class="form-select form-select-custom" required onchange="toggleBidangField()">
                    <option value="">— Pilih Kategori —</option>
                    @foreach($kategoriList as $kategori)
                        <option value="{{ $kategori->id }}"
                                data-is-prodi="{{ stripos($kategori->nama_kategori, 'prodi') !== false ? '1' : '0' }}"
                            {{ old('kategori_kegiatan_id', $kegiatan->kategori_kegiatan_id) == $kategori->id ? 'selected' : '' }}>
                            {{ $kategori->nama_kategori }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-4" id="bidangFieldWrapper">
                <label class="form-label-custom">Bidang <span class="required" id="bidangRequired">*</span></label>
                <select name="bidang_id" id="bidangSelect" class="form-select form-select-custom" required>
                    <option value="">— Pilih Bidang —</option>
                    @foreach($bidangList as $bidang)
                        <option value="{{ $bidang->id }}"
                            {{ old('bidang_id', $kegiatan->bidang_id) == $bidang->id ? 'selected' : '' }}>
                            {{ $bidang->nama_bidang }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-4">
                <label class="form-label-custom">Kepengurusan</label>
                <select name="kepengurusan_id" class="form-select form-select-custom">
                    <option value="">— Pilih Kepengurusan —</option>
                    @foreach($kepengurusanList as $kp)
                        <option value="{{ $kp->id }}"
                            {{ old('kepengurusan_id', $kegiatan->kepengurusan_id) == $kp->id ? 'selected' : '' }}>
                            {{ $kp->tahun_periode }}
                        </option>
                    @endforeach
                </select>
            </div>
        </div>

        <div class="mb-3">
            <label class="form-label-custom">Deskripsi <span class="required">*</span></label>
            <textarea name="deskripsi" class="form-control form-control-custom" required>{{ old('deskripsi', $kegiatan->deskripsi) }}</textarea>
        </div>

        <div class="mb-3">
            <label class="form-label-custom">Status <span class="required">*</span></label>
            <select name="status" class="form-select form-select-custom" required>
                <option value="akan_datang" {{ old('status', $kegiatan->status) == 'akan_datang' ? 'selected' : '' }}>🟡 Akan Datang</option>
                <option value="berlangsung" {{ old('status', $kegiatan->status) == 'berlangsung' ? 'selected' : '' }}>🔵 Berlangsung</option>
                <option value="selesai" {{ old('status', $kegiatan->status) == 'selesai' ? 'selected' : '' }}>🟢 Selesai</option>
            </select>
        </div>
    </div>

    <!-- Waktu & Lokasi -->
    <div class="form-card">
        <div class="form-card-title">📅 Waktu & Lokasi</div>

        <div class="row g-3 mb-3">
            <div class="col-md-3">
                <label class="form-label-custom">Tanggal Mulai <span class="required">*</span></label>
                <input type="date" name="tanggal_mulai" class="form-control form-control-custom"
                       value="{{ old('tanggal_mulai', $kegiatan->tanggal_mulai?->format('Y-m-d')) }}" required>
            </div>
            <div class="col-md-3">
                <label class="form-label-custom">Jam Mulai</label>
                <input type="time" name="jam_mulai" class="form-control form-control-custom"
                       value="{{ old('jam_mulai', $kegiatan->jam_mulai_formatted) }}">
            </div>
            <div class="col-md-3">
                <label class="form-label-custom">Tanggal Selesai</label>
                <input type="date" name="tanggal_selesai" class="form-control form-control-custom"
                       value="{{ old('tanggal_selesai', $kegiatan->tanggal_selesai?->format('Y-m-d')) }}">
            </div>
            <div class="col-md-3">
                <label class="form-label-custom">Jam Selesai</label>
                <input type="time" name="jam_selesai" class="form-control form-control-custom"
                       value="{{ old('jam_selesai', $kegiatan->jam_selesai_formatted) }}">
            </div>
        </div>

        <div class="mb-3">
            <label class="form-label-custom">Lokasi</label>
            <input type="text" name="lokasi" class="form-control form-control-custom"
                   value="{{ old('lokasi', $kegiatan->lokasi) }}">
        </div>
    </div>

    <!-- Personel -->
    <div class="form-card">
        <div class="form-card-title">👥 Personel Kegiatan</div>

        <div class="row g-3 mb-3">
            <div class="col-md-6">
                <label class="form-label-custom">Ketua Pelaksana</label>
                <div class="search-select-wrapper">
                    <input type="hidden" name="ketua_pelaksana_id" id="ketuaPelaksanaId"
                           value="{{ old('ketua_pelaksana_id', $kegiatan->ketua_pelaksana_id) }}">
                    <input type="text" class="form-control form-control-custom" id="ketuaPelaksanaSearch"
                           placeholder="🔍 Cari nama mahasiswa..."
                           value="{{ $ketuaNama }}"
                           autocomplete="off"
                           onfocus="showDropdown('ketuaPelaksanaDropdown')"
                           oninput="filterOptions('ketuaPelaksanaSearch', 'ketuaPelaksanaDropdown')">
                    <div class="search-select-dropdown" id="ketuaPelaksanaDropdown">
                        @foreach($mahasiswaList as $mhs)
                            <div class="search-select-option"
                                 onclick="selectOption('ketuaPelaksanaId', '{{ $mhs->id }}', 'ketuaPelaksanaSearch', '{{ $mhs->user->name ?? 'N/A' }}', 'ketuaPelaksanaDropdown')"
                                 data-name="{{ strtolower($mhs->user->name ?? '') }}"
                                 data-nim="{{ $mhs->student_number }}">
                                {{ $mhs->user->name ?? 'N/A' }}
                                <div class="sub-text">NIM: {{ $mhs->student_number }} • Angkatan {{ $mhs->cohort_year }}</div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <div class="col-md-6">
                <label class="form-label-custom">Dosen Pendamping <span style="color: #9ca3af; font-weight: 400;">(opsional)</span></label>
                <div class="search-select-wrapper">
                    <input type="hidden" name="dosen_pendamping_id" id="dosenPendampingId"
                           value="{{ old('dosen_pendamping_id', $kegiatan->dosen_pendamping_id) }}">
                    <input type="text" class="form-control form-control-custom" id="dosenPendampingSearch"
                           placeholder="🔍 Cari nama dosen..."
                           value="{{ $dosenNama }}"
                           autocomplete="off"
                           onfocus="showDropdown('dosenPendampingDropdown')"
                           oninput="filterOptions('dosenPendampingSearch', 'dosenPendampingDropdown')">
                    <div class="search-select-dropdown" id="dosenPendampingDropdown">
                        @foreach($dosenList as $dosen)
                            <div class="search-select-option"
                                 onclick="selectOption('dosenPendampingId', '{{ $dosen->id }}', 'dosenPendampingSearch', '{{ $dosen->user->name ?? 'N/A' }}', 'dosenPendampingDropdown')"
                                 data-name="{{ strtolower($dosen->user->name ?? '') }}"
                                 data-nip="{{ $dosen->employee_number }}">
                                {{ $dosen->user->name ?? 'N/A' }}
                                <div class="sub-text">NIP: {{ $dosen->employee_number }}</div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Detail Tambahan -->
    <div class="form-card">
        <div class="form-card-title">📝 Detail Tambahan</div>

        <div class="row g-3 mb-3">
            <div class="col-md-6">
                <label class="form-label-custom">Target Peserta</label>
                <input type="number" name="target_peserta" class="form-control form-control-custom"
                       value="{{ old('target_peserta', $kegiatan->target_peserta) }}" min="1">
            </div>
            <div class="col-md-6">
                <label class="form-label-custom">Anggaran (Rp)</label>
                <input type="number" name="anggaran" class="form-control form-control-custom"
                       value="{{ old('anggaran', $kegiatan->anggaran) }}" min="0" step="1000">
            </div>
        </div>
    </div>

    <!-- Banner -->
    <div class="form-card">
        <div class="form-card-title">🖼️ Banner Kegiatan</div>

        @if($kegiatan->banner)
            <div class="banner-current">
                <span class="badge-current">Banner Saat Ini</span>
                <img src="{{ asset('storage/' . $kegiatan->banner) }}" alt="Banner saat ini" class="banner-preview" style="display: block;">
            </div>
            <p style="font-size: 13px; color: #6b7280; margin-bottom: 12px;">Upload gambar baru untuk mengganti banner saat ini.</p>
        @endif

        <div class="banner-upload-area" onclick="document.getElementById('bannerInput').click()">
            <div class="upload-icon">📷</div>
            <p>Klik untuk upload banner {{ $kegiatan->banner ? 'baru' : 'kegiatan' }}</p>
            <small>Format: JPG, PNG, WebP • Maks: 5MB</small>
        </div>
        <input type="file" name="banner" id="bannerInput" accept="image/jpeg,image/png,image/webp"
               style="display: none;" onchange="previewBanner(this)">
        <img id="bannerPreview" class="banner-preview" alt="Preview Banner" style="display: none;">
    </div>

    <!-- Foto Kegiatan -->
    <div class="form-card">
        <div class="form-card-title">📸 Foto Kegiatan <span style="color: #9ca3af; font-weight: 400; font-size: 13px;">(opsional, maks 10 foto)</span></div>

        @if($existingFoto->count() > 0)
            <div class="existing-file-label">Foto yang sudah diupload</div>
            <div class="file-preview-grid" style="margin-bottom: 16px;">
                @foreach($existingFoto as $foto)
                    <div class="file-preview-item" id="existingFile{{ $foto->id }}">
                        <button type="button" class="btn-remove-file" onclick="markFileForDeletion({{ $foto->id }})">✕</button>
                        <img src="{{ asset('storage/' . $foto->path_file) }}" alt="{{ $foto->judul_file }}">
                        <div class="file-info">{{ $foto->nama_file }}</div>
                    </div>
                @endforeach
            </div>
        @endif

        <div class="file-upload-area" id="fotoUploadArea" onclick="document.getElementById('fotoInput').click()">
            <div class="upload-icon">🖼️</div>
            <p>Klik atau drag & drop foto baru ke sini</p>
            <small>Format: JPG, PNG, WebP • Maks: 5MB per file</small>
        </div>
        <input type="file" name="foto_kegiatan[]" id="fotoInput" accept="image/jpeg,image/png,image/webp"
               multiple style="display: none;" onchange="handleFotoSelect(this)">
        <div class="file-preview-grid" id="fotoPreviewGrid"></div>
    </div>

    <!-- Dokumen Kegiatan -->
    <div class="form-card">
        <div class="form-card-title">📄 Dokumen Kegiatan <span style="color: #9ca3af; font-weight: 400; font-size: 13px;">(opsional, maks 10 dokumen)</span></div>

        @if($existingDokumen->count() > 0)
            <div class="existing-file-label">Dokumen yang sudah diupload</div>
            @foreach($existingDokumen as $doc)
                @php
                    $ext = pathinfo($doc->nama_file, PATHINFO_EXTENSION);
                    $iconMap = ['pdf' => '📕', 'doc' => '📘', 'docx' => '📘', 'xls' => '📗', 'xlsx' => '📗', 'ppt' => '📙', 'pptx' => '📙'];
                    $icon = $iconMap[strtolower($ext)] ?? '📄';
                @endphp
                <div class="doc-preview-item" id="existingFile{{ $doc->id }}">
                    <span class="doc-icon">{{ $icon }}</span>
                    <div class="doc-info">
                        <div class="doc-name">{{ $doc->nama_file }}</div>
                        <div class="doc-size">{{ strtoupper($ext) }}</div>
                    </div>
                    <a href="{{ asset('storage/' . $doc->path_file) }}" target="_blank" class="btn-remove-doc" style="background: #dbeafe; color: #2563eb;" title="Download">⬇</a>
                    <button type="button" class="btn-remove-doc" onclick="markFileForDeletion({{ $doc->id }})" title="Hapus">✕</button>
                </div>
            @endforeach
            <div style="margin-bottom: 16px;"></div>
        @endif

        <div class="file-upload-area" id="dokumenUploadArea" onclick="document.getElementById('dokumenInput').click()">
            <div class="upload-icon">📎</div>
            <p>Klik atau drag & drop dokumen baru ke sini</p>
            <small>Format: PDF, DOC, DOCX, XLS, XLSX, PPT, PPTX • Maks: 10MB per file</small>
        </div>
        <input type="file" name="dokumen_kegiatan[]" id="dokumenInput"
               accept=".pdf,.doc,.docx,.xls,.xlsx,.ppt,.pptx"
               multiple style="display: none;" onchange="handleDokumenSelect(this)">
        <div id="dokumenPreviewList"></div>
    </div>

    <!-- Hidden inputs for file deletion -->
    <div id="deleteFileInputs"></div>

    <!-- Action Buttons -->
    <div class="d-flex gap-3 justify-content-end mt-2">
        <a href="{{ route('manajemenmahasiswa.kegiatan.show', $kegiatan->id) }}" class="btn-cancel">Batal</a>
        <button type="submit" class="btn-submit">
            💾 Simpan Perubahan
        </button>
    </div>
</form>

<script>
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

// ── Searchable Dropdown ──
function showDropdown(dropdownId) {
    document.getElementById(dropdownId).classList.add('show');
}

function filterOptions(inputId, dropdownId) {
    const query = document.getElementById(inputId).value.toLowerCase();
    const dropdown = document.getElementById(dropdownId);
    const options = dropdown.querySelectorAll('.search-select-option');
    let hasVisible = false;

    options.forEach(opt => {
        const name = opt.getAttribute('data-name') || '';
        const secondary = opt.getAttribute('data-nim') || opt.getAttribute('data-nip') || '';
        const match = name.includes(query) || secondary.includes(query);
        opt.style.display = match ? 'block' : 'none';
        if (match) hasVisible = true;
    });

    dropdown.classList.toggle('show', hasVisible);
}

function selectOption(hiddenId, value, inputId, label, dropdownId) {
    document.getElementById(hiddenId).value = value;
    document.getElementById(inputId).value = label;
    document.getElementById(dropdownId).classList.remove('show');
}

document.addEventListener('click', function(e) {
    document.querySelectorAll('.search-select-dropdown').forEach(d => {
        if (!d.parentElement.contains(e.target)) {
            d.classList.remove('show');
        }
    });
});

// ── File Deletion (existing files) ──
function markFileForDeletion(fileId) {
    const el = document.getElementById('existingFile' + fileId);
    if (el) {
        el.style.opacity = '0.3';
        el.style.pointerEvents = 'none';
    }
    const container = document.getElementById('deleteFileInputs');
    const input = document.createElement('input');
    input.type = 'hidden';
    input.name = 'hapus_file[]';
    input.value = fileId;
    container.appendChild(input);
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
                <button type="button" class="btn-remove-file" onclick="removeFoto(${i})">✕</button>
                <img src="${e.target.result}" alt="${file.name}">
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
        if (dokumenFiles.length >= 10) return;
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
        const icons = { pdf: '📕', doc: '📘', docx: '📘', xls: '📗', xlsx: '📗', ppt: '📙', pptx: '📙' };
        const icon = icons[ext] || '📄';
        const item = document.createElement('div');
        item.className = 'doc-preview-item';
        item.innerHTML = `
            <span class="doc-icon">${icon}</span>
            <div class="doc-info">
                <div class="doc-name">${file.name}</div>
                <div class="doc-size">${formatFileSize(file.size)} • ${ext.toUpperCase()}</div>
            </div>
            <button type="button" class="btn-remove-doc" onclick="removeDokumen(${i})">✕</button>
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
            Array.from(files).forEach(f => { if (dokumenFiles.length < 10) dokumenFiles.push(f); });
            renderDokumenPreviews();
            syncDokumenInput();
        }
    });
});

// ── Toggle Bidang Field based on Kategori ──
function toggleBidangField() {
    const kategoriSelect = document.getElementById('kategoriSelect');
    const bidangWrapper = document.getElementById('bidangFieldWrapper');
    const bidangSelect = document.getElementById('bidangSelect');
    const bidangRequired = document.getElementById('bidangRequired');
    const selected = kategoriSelect.options[kategoriSelect.selectedIndex];
    const isProdi = selected && selected.getAttribute('data-is-prodi') === '1';

    if (isProdi) {
        bidangWrapper.style.display = 'none';
        bidangSelect.removeAttribute('required');
        bidangSelect.value = '';
        if (bidangRequired) bidangRequired.style.display = 'none';
    } else {
        bidangWrapper.style.display = '';
        bidangSelect.setAttribute('required', 'required');
        if (bidangRequired) bidangRequired.style.display = '';
    }
}

// Run on page load in case kegiatan already has prodi kategori
document.addEventListener('DOMContentLoaded', toggleBidangField);
</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</x-manajemenmahasiswa::layouts.mahasiswa>
