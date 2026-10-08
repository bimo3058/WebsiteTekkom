{{--
    Partial form kegiatan — dipakai bersama oleh:
      • Rencana Proker (create & edit)  → $showDokumentasi = false
      • Pelaksanaan Kegiatan (edit)     → $showDokumentasi = true

    Foto & Dokumen Kegiatan sengaja HANYA muncul di Pelaksanaan: keduanya
    dokumentasi acara yang sudah berlangsung, bukan bagian dari rencana.

    Variabel wajib: $proker (boleh instance kosong untuk mode create),
    $formAction, $formMethod ('POST'|'PUT'), $backUrl, $headerTitle,
    $headerSubtitle, $submitLabel, $showDokumentasi, $tahapRencana,
    $bidangList, $kategoriList, $mahasiswaList, $dosenList,
    $selectedKategoriIds, $selectedBidangIds, $existingPanitia, $existingPanitiaIds.
    Hanya saat $showDokumentasi: $existingFoto, $existingDokumen.

    Bagian "Akses Kelola" (_akses_kelola.blade.php, di-include dari sini) butuh
    $bolehAturAkses, $calonPengelola, $pengelolaTerpilih, $namaPembuat — semuanya
    dari PengelolaKegiatanService::dataForm().

    Ketua Pelaksana, Dosen Pendamping, dan Panitia dirender _personel.blade.php
    (kotak pilih Alpine) dari $existingPanitia dan $existingDosen — keduanya
    disiapkan controller.
--}}
@php
    $showDokumentasi = $showDokumentasi ?? false;
    // Tahap Rencana Proker: SEMUA detail boleh dikosongkan saat menyimpan draft —
    // ketua membuat kerangka, pengurus melengkapi kemudian. Kelengkapan baru
    // ditegakkan saat "Ajukan Proker" (lihat ProkerController::SYARAT_AJUKAN):
    // tombolnya nonaktif dengan tooltip berisi field yang masih kosong.
    // Karena itu di tahap ini `tanggal_mulai` TIDAK boleh diberi atribut
    // `required` — itu akan memblokir penyimpanan draft di sisi browser.
    $tahapRencana = $tahapRencana ?? false;
@endphp

<!-- Header -->
<x-manajemenmahasiswa::ui.page-header bordered :title="$headerTitle">
    {!! $headerSubtitle !!}
    <x-slot:leading>
        <a href="{{ $backUrl }}" class="btn-back mk-btn mk-btn--secondary mk-btn--icon mk-btn--sm" aria-label="Kembali">
            <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path d="M15 19l-7-7 7-7"/></svg>
        </a>
    </x-slot:leading>
</x-manajemenmahasiswa::ui.page-header>

<!-- Validation Errors -->
<x-manajemenmahasiswa::ui.flash type="error" title="Terjadi Kesalahan" :messages="$errors->all()" class="mb-3" />

<form action="{{ $formAction }}" method="POST" enctype="multipart/form-data">
    @csrf
    @if($formMethod === 'PUT')
        @method('PUT')
    @endif

    <!-- Info Utama -->
    <div class="form-card">
        <div class="form-card-title"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="vertical-align: -2px;"><path d="M16 4h2a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h2"></path><rect x="8" y="2" width="8" height="4" rx="1" ry="1"></rect></svg> Informasi Utama</div>

        <div class="mb-3">
            <label class="form-label-custom">Judul Kegiatan <span class="required">*</span></label>
            <input type="text" name="judul" id="judulInput" class="form-control form-control-custom"
                   value="{{ old('judul', $proker->judul) }}" required maxlength="255"
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
                                   {{ in_array($kategori->id, $selectedKategoriIds) ? 'checked' : '' }}>
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
                                   {{ in_array($bidang->id, $selectedBidangIds) ? 'checked' : '' }}>
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
                      required minlength="20" maxlength="3000"
                      oninput="updateCharCount('deskripsiInput','deskripsiCount',3000)">{{ old('deskripsi', $proker->deskripsi) }}</textarea>
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
                <label class="form-label-custom">Tanggal Mulai
                    @unless($tahapRencana)<span class="required">*</span>@endunless
                </label>
                <input type="date" name="tanggal_mulai" class="form-control form-control-custom"
                       value="{{ old('tanggal_mulai', $proker->tanggal_mulai?->format('Y-m-d')) }}"
                       @unless($tahapRencana) required @endunless>
            </div>
            <div class="col-md-3">
                <label class="form-label-custom">Jam Mulai</label>
                <input type="time" name="jam_mulai" class="form-control form-control-custom"
                       value="{{ old('jam_mulai', $proker->jam_mulai_formatted) }}">
            </div>
            <div class="col-md-3">
                <label class="form-label-custom">Tanggal Selesai</label>
                <input type="date" name="tanggal_selesai" class="form-control form-control-custom"
                       value="{{ old('tanggal_selesai', $proker->tanggal_selesai?->format('Y-m-d')) }}">
            </div>
            <div class="col-md-3">
                <label class="form-label-custom">Jam Selesai</label>
                <input type="time" name="jam_selesai" class="form-control form-control-custom"
                       value="{{ old('jam_selesai', $proker->jam_selesai_formatted) }}">
            </div>
        </div>

        <div class="mb-3">
            <label class="form-label-custom">Lokasi</label>
            <input type="text" name="lokasi" class="form-control form-control-custom"
                   value="{{ old('lokasi', $proker->lokasi) }}">
        </div>
    </div>

    <!-- Personel -->
    <div class="form-card">
        <div class="form-card-title"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="vertical-align: -2px;"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="9" cy="7" r="4"></circle><path d="M23 21v-2a4 4 0 0 0-3-3.87"></path><path d="M16 3.13a4 4 0 0 1 0 7.75"></path></svg> Personel Kegiatan</div>

        @include('manajemenmahasiswa::partials.kegiatan-form._personel', [
            'ketuaId'          => old('ketua_pelaksana_id', $proker->ketua_pelaksana_id),
            'dosenTerpilih'    => $existingDosen,
            'panitiaTerpilih'  => old('panitia_ids') !== null
                ? $mahasiswaList->whereIn('id', old('panitia_ids'))
                : $existingPanitia,
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
                       value="{{ old('target_peserta', $proker->target_peserta) }}" min="1">
            </div>
            <div class="col-md-6">
                @php
                    // Cast `decimal:2` membuat anggaran keluar sebagai "2999000.00" dan itulah
                    // yang tampil mentah di kolom angka. Ekor desimalnya dibuang supaya user
                    // melihat 2999000, bukan 2999000.00.
                    $anggaranValue = old('anggaran', $proker->anggaran);
                    if (is_string($anggaranValue) && str_contains($anggaranValue, '.')) {
                        $anggaranValue = rtrim(rtrim($anggaranValue, '0'), '.');
                    }
                @endphp
                <label class="form-label-custom">Anggaran (Rp)</label>
                {{--
                    step="any", BUKAN step="1000". Dengan step="1000" browser diam-diam
                    menolak angka yang bukan kelipatan seribu (mis. 750500): tombol Simpan
                    seolah tidak berfungsi dan yang muncul cuma gelembung bawaan browser,
                    tanpa satu pun keterangan di form bahwa anggaran harus kelipatan 1000.
                --}}
                <input type="number" name="anggaran" class="form-control form-control-custom"
                       value="{{ $anggaranValue }}" min="0" max="9999999999999" step="any">
            </div>
        </div>
    </div>

    <!-- Banner -->
    <div class="form-card">
        <div class="form-card-title"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="vertical-align: -2px;"><rect width="18" height="18" x="3" y="3" rx="2" ry="2"></rect><circle cx="9" cy="9" r="2"></circle><path d="m21 15-3.086-3.086a2 2 0 0 0-2.828 0L6 21"></path></svg> Banner Kegiatan *</div>

        @if($proker->banner)
            <div class="banner-current">
                <span class="badge-current">Banner Saat Ini</span>
                <img src="{{ $proker->banner_url }}" alt="Banner saat ini" class="banner-preview" style="display: block;" onclick="openLightbox(this.src)" title="Klik untuk memperbesar">
            </div>
            <p style="font-size: 13px; color: var(--c-fg-muted); margin-bottom: 12px;">Upload gambar baru untuk mengganti banner saat ini.</p>
        @endif

        <div class="banner-upload-area" onclick="document.getElementById('bannerInput').click()">
            <div class="upload-icon"><svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect width="18" height="18" x="3" y="3" rx="2" ry="2"></rect><circle cx="9" cy="9" r="2"></circle><path d="m21 15-3.086-3.086a2 2 0 0 0-2.828 0L6 21"></path></svg></div>
            <p>Klik untuk upload banner {{ $proker->banner ? 'baru' : 'kegiatan' }}</p>
            <small>Format: JPG, PNG, WebP • Maks: 5MB<br><span style="color: var(--c-primary); font-weight: 500;">Rekomendasi: Resolusi 1280 x 720 (Rasio 16:9)</span></small>
        </div>
        <input type="file" name="banner" id="bannerInput" accept="image/jpeg,image/png,image/webp"
               style="display: none;" onchange="previewBanner(this)">
        <img id="bannerPreview" class="banner-preview" alt="Preview Banner" style="display: none;" onclick="openLightbox(this.src)" title="Klik untuk memperbesar">
    </div>

    {{-- Foto & Dokumen hanya di Pelaksanaan — dokumentasi acara yang sudah berlangsung --}}
    @if($showDokumentasi)
    <!-- Foto Kegiatan -->
    <div class="form-card">
        <div class="form-card-title"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="vertical-align: -2px;"><path d="M23 19a2 2 0 0 1-2 2H3a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h4l2-3h6l2 3h4a2 2 0 0 1 2 2z"></path><circle cx="12" cy="13" r="4"></circle></svg> Foto Kegiatan <span style="color: var(--c-fg-muted); font-weight: 400; font-size: 13px;">(opsional, maks 10 foto)</span></div>

        @if($existingFoto->count() > 0)
            <div class="existing-file-label">Foto yang sudah diupload</div>
            <div class="file-preview-grid" style="margin-bottom: 16px;">
                @foreach($existingFoto as $foto)
                    <div class="file-preview-item" id="existingFile{{ $foto->id }}">
                        <button type="button" class="btn-remove-file mk-btn mk-btn--secondary mk-btn--sm mk-btn--icon" onclick="markFileForDeletion({{ $foto->id }})" title="Hapus foto" aria-label="Hapus foto {{ $foto->nama_file }}"><svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg></button>
                        <img src="{{ $foto->url }}" alt="{{ $foto->judul_file }}" style="cursor: pointer;" onclick="openLightbox(this.src)" title="Klik untuk memperbesar">
                        <div class="file-info">{{ $foto->nama_file }}</div>
                    </div>
                @endforeach
            </div>
        @endif

        <div class="file-upload-area" id="fotoUploadArea" onclick="document.getElementById('fotoInput').click()">
            <div class="upload-icon"><svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect width="18" height="18" x="3" y="3" rx="2" ry="2"></rect><circle cx="9" cy="9" r="2"></circle><path d="m21 15-3.086-3.086a2 2 0 0 0-2.828 0L6 21"></path></svg></div>
            <p>Klik atau drag & drop foto baru ke sini</p>
            <small>Format: JPG, PNG, WebP • Maks: 5MB per file</small>
        </div>
        <input type="file" name="foto_kegiatan[]" id="fotoInput" accept="image/jpeg,image/png,image/webp"
               multiple style="display: none;" onchange="handleFotoSelect(this)">
        <div class="file-preview-grid" id="fotoPreviewGrid"></div>
    </div>

    <!-- Dokumen Kegiatan -->
    <div class="form-card">
        <div class="form-card-title"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="vertical-align: -2px;"><path d="M14.5 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V7.5L14.5 2z"></path><polyline points="14 2 14 8 20 8"></polyline></svg> Dokumen Kegiatan <span style="color: var(--c-fg-muted); font-weight: 400; font-size: 13px;">(opsional, maks 2 dokumen)</span></div>

        @if($existingDokumen->count() > 0)
            <div class="existing-file-label">Dokumen yang sudah diupload</div>
            @foreach($existingDokumen as $doc)
                @php
                    $ext = pathinfo($doc->nama_file, PATHINFO_EXTENSION);
                    $iconMap = ['pdf' => 'PDF', 'doc' => 'DOC', 'docx' => 'DOC', 'xls' => 'XLS', 'xlsx' => 'XLS', 'ppt' => 'PPT', 'pptx' => 'PPT'];
                    $icon = $iconMap[strtolower($ext)] ?? 'FILE';
                @endphp
                <div class="doc-preview-item" id="existingFile{{ $doc->id }}">
                    <span class="doc-icon">{{ $icon }}</span>
                    <div class="doc-info">
                        <div class="doc-name">{{ $doc->nama_file }}</div>
                        <div class="doc-size">{{ strtoupper($ext) }}</div>
                    </div>
                    <a href="{{ $doc->url }}" target="_blank" class="mk-btn mk-btn--secondary mk-btn--sm mk-btn--icon" title="Unduh dokumen" aria-label="Unduh dokumen {{ $doc->nama_file }}"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg></a>
                    <button type="button" class="btn-remove-doc mk-btn mk-btn--secondary mk-btn--sm mk-btn--icon" onclick="markFileForDeletion({{ $doc->id }})" title="Hapus dokumen" aria-label="Hapus dokumen {{ $doc->nama_file }}"><svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg></button>
                </div>
            @endforeach
            <div style="margin-bottom: 16px;"></div>
        @endif

        <div class="file-upload-area" id="dokumenUploadArea" onclick="document.getElementById('dokumenInput').click()">
            <div class="upload-icon"><svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m21.44 11.05-9.19 9.19a6 6 0 0 1-8.49-8.49l9.19-9.19a4 4 0 0 1 5.66 5.66l-9.2 9.19a2 2 0 0 1-2.83-2.83l8.49-8.48"></path></svg></div>
            <p>Klik atau drag & drop dokumen baru ke sini</p>
            <small>Format: PDF, DOC, DOCX, XLS, XLSX, PPT, PPTX • Maks: 5MB per file</small>
        </div>
        <input type="file" name="dokumen_kegiatan[]" id="dokumenInput"
               accept=".pdf,.doc,.docx,.xls,.xlsx,.ppt,.pptx"
               multiple style="display: none;" onchange="handleDokumenSelect(this)">
        <div id="dokumenPreviewList"></div>
    </div>

    <!-- Hidden inputs for file deletion -->
    <div id="deleteFileInputs"></div>
    @endif

    <!-- Action Buttons -->
    <div class="d-flex gap-3 justify-content-end mt-2">
        <a href="{{ $backUrl }}" class="mk-btn mk-btn--secondary">Batal</a>
        <button type="submit" class="mk-btn mk-btn--primary">
            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="vertical-align:-3px;margin-right:6px;"><path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v14a2 2 0 0 1-2 2z"/><polyline points="17 21 17 13 7 13 7 21"/><polyline points="7 3 7 8 15 8"/></svg>{{ $submitLabel }}
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
