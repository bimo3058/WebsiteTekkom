{{--
    Isi kartu "Banner Kegiatan" — dipakai bersama form Rencana Proker, Pelaksanaan,
    dan Laporan & Arsip (tambah & edit).

    Banner yang sudah terisi (tersimpan atau baru dipilih) menutup kotak unggah:
    gambarnya tampil dengan tombol "Upload Ulang" dan "Hapus" di pojok, dan klik
    gambar tetap membuka lightbox (openLightbox() milik halaman pemakai).
    Hapus pada banner tersimpan mengirim `hapus_banner=1`; file lama baru dibuang
    saat form disimpan. Di Laporan & Arsip banner wajib, jadi Hapus tanpa unggahan
    baru ditolak validasi KegiatanController::update().

    Banner salinan Rencana Proker ($bannerTerkunci, S-61) tampil tanpa tombol dan
    tanpa input berkas: semua yang diunggah di rencana terkunci di Pelaksanaan &
    Arsip. Server ikut menolak lewat RepoMulmedService::tolakUbahBannerRencana().

    Param: $bannerUrl (URL banner tersimpan, null bila belum ada),
           $bannerTerkunci (bool, opsional).
--}}
@php
    $bannerUrl      = $bannerUrl ?? null;
    $bannerTerkunci = ($bannerTerkunci ?? false) && $bannerUrl;
    // Pilihan Hapus dipertahankan saat form kembali karena galat validasi.
    $bannerDihapus = $bannerUrl && old('hapus_banner');
    $bannerTampil  = $bannerUrl && !$bannerDihapus;
@endphp
<style>
    .banner-field [hidden] { display: none !important; }
    .banner-upload-area.dragover {
        border-color: var(--c-primary);
        background: var(--c-primary-subtle);
    }
    .banner-filled {
        position: relative;
        border-radius: 10px;
        overflow: hidden;
    }
    .banner-filled .banner-preview {
        display: block;
        margin-top: 0;
    }
    .banner-filled .banner-badge {
        position: absolute;
        top: 10px;
        left: 10px;
        background: rgba(0, 0, 0, 0.6);
        color: var(--c-surface);
        font-size: 11px;
        font-weight: 600;
        padding: 4px 10px;
        border-radius: 6px;
        pointer-events: none;
    }
    .banner-filled .banner-actions {
        position: absolute;
        top: 10px;
        right: 10px;
        display: flex;
        gap: 8px;
    }
    .banner-filled .banner-actions .mk-btn {
        box-shadow: 0 2px 6px rgba(0, 0, 0, 0.15);
    }
    /* Hover bawaan mk-btn--secondary semi-transparan — di atas foto jadi tembus. */
    .banner-filled .banner-actions .mk-btn:hover {
        background: color-mix(in srgb, var(--c-primary, #0B266E) 8%, #ffffff) !important;
    }
    /* Di HP tombol berteks menutupi badge di pojok kiri: tampil sebagai ikon saja. */
    @media (max-width: 576px) {
        .banner-filled .banner-actions .mk-btn { width: 32px; padding: 0; justify-content: center; }
        .banner-filled .banner-actions .banner-aksi-teks { display: none; }
        .banner-filled .banner-actions svg { margin-right: 0 !important; }
    }
</style>

@if($bannerTerkunci)
<div class="banner-field">
    <div class="banner-filled">
        <img class="banner-preview" src="{{ $bannerUrl }}" alt="Banner kegiatan" title="Klik untuk memperbesar" onclick="openLightbox(this.src)">
        <span class="banner-badge"><svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" style="vertical-align: -1px; margin-right: 3px;"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>Dari Rencana</span>
    </div>
    <p style="font-size: 12px; color: var(--c-fg-muted); margin: 8px 0 0;">Banner disalin dari Rencana Proker dan tidak bisa diganti atau dihapus.</p>
</div>
@else
<div class="banner-field">
    <div class="banner-upload-area" id="bannerUploadArea" role="button" tabindex="0" @if($bannerTampil) hidden @endif>
        <div class="upload-icon"><svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect width="18" height="18" x="3" y="3" rx="2" ry="2"></rect><circle cx="9" cy="9" r="2"></circle><path d="m21 15-3.086-3.086a2 2 0 0 0-2.828 0L6 21"></path></svg></div>
        <p>Klik atau drag & drop banner kegiatan ke sini</p>
        <small>Format: JPG, PNG, WebP • Maks: 5MB<br><span style="color: var(--c-primary); font-weight: 500;">Rekomendasi: Resolusi 1280 x 720 (Rasio 16:9)</span></small>
    </div>

    <div class="banner-filled" id="bannerFilled" @unless($bannerTampil) hidden @endunless>
        <img id="bannerPreview" class="banner-preview" alt="Banner kegiatan" title="Klik untuk memperbesar"
             @if($bannerTampil) src="{{ $bannerUrl }}" @endif>
        <span class="banner-badge" id="bannerBadge">Banner saat ini</span>
        <div class="banner-actions">
            <button type="button" class="mk-btn mk-btn--secondary mk-btn--sm" id="bannerGanti" title="Upload ulang banner" aria-label="Upload ulang banner">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="vertical-align: -2px; margin-right: 4px;"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="17 8 12 3 7 8"/><line x1="12" y1="3" x2="12" y2="15"/></svg><span class="banner-aksi-teks">Upload Ulang</span>
            </button>
            <button type="button" class="mk-btn mk-btn--secondary mk-btn--sm" id="bannerHapus" title="Hapus banner" aria-label="Hapus banner">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="vertical-align: -2px; margin-right: 4px;"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6"/><path d="M10 11v6"/><path d="M14 11v6"/><path d="M9 6V4a1 1 0 0 1 1-1h4a1 1 0 0 1 1 1v2"/></svg><span class="banner-aksi-teks">Hapus</span>
            </button>
        </div>
    </div>

    <input type="file" name="banner" id="bannerInput" accept="image/jpeg,image/png,image/webp" hidden>
    @if($bannerUrl)
        <input type="hidden" name="hapus_banner" id="bannerHapusInput" value="{{ $bannerDihapus ? 1 : 0 }}">
    @endif
</div>

<script>
(function () {
    const input  = document.getElementById('bannerInput');
    const area   = document.getElementById('bannerUploadArea');
    const filled = document.getElementById('bannerFilled');
    const img    = document.getElementById('bannerPreview');
    const badge  = document.getElementById('bannerBadge');
    const hapus  = document.getElementById('bannerHapusInput');
    // Batal di jendela pilih file mengosongkan input; berkas terakhir dipasang lagi
    // supaya pratinjau yang masih tampil tetap ikut terkirim.
    let berkasTerakhir = null;

    const pasang = file => {
        const dt = new DataTransfer();
        dt.items.add(file);
        input.files = dt.files;
    };
    const tampilkan = file => {
        berkasTerakhir = file;
        const reader = new FileReader();
        reader.onload = e => {
            img.src = e.target.result;
            badge.textContent = 'Banner baru · belum disimpan';
            area.hidden = true;
            filled.hidden = false;
        };
        reader.readAsDataURL(file);
    };
    const pilih = () => input.click();

    input.addEventListener('change', () => {
        if (input.files[0]) {
            tampilkan(input.files[0]);
        } else if (berkasTerakhir) {
            pasang(berkasTerakhir);
        }
    });

    area.addEventListener('click', pilih);
    area.addEventListener('keydown', e => {
        if (e.key !== 'Enter' && e.key !== ' ') return;
        e.preventDefault();
        pilih();
    });
    ['dragenter', 'dragover'].forEach(evt => {
        area.addEventListener(evt, e => { e.preventDefault(); area.classList.add('dragover'); });
    });
    ['dragleave', 'drop'].forEach(evt => {
        area.addEventListener(evt, e => { e.preventDefault(); area.classList.remove('dragover'); });
    });
    area.addEventListener('drop', e => {
        const file = Array.from(e.dataTransfer.files).find(f => /^image\/(jpeg|png|webp)$/.test(f.type));
        if (!file) return;
        pasang(file);
        tampilkan(file);
    });

    img.addEventListener('click', () => openLightbox(img.src));
    document.getElementById('bannerGanti').addEventListener('click', pilih);
    document.getElementById('bannerHapus').addEventListener('click', () => {
        berkasTerakhir = null;
        input.value = '';
        if (hapus) hapus.value = 1;
        img.removeAttribute('src');
        filled.hidden = true;
        area.hidden = false;
    });
})();
</script>
@endif
