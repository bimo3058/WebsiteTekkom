{{--
    Sistem tombol & menu aksi modul Manajemen Mahasiswa.

    Satu bentuk tombol untuk seluruh modul, menggantikan puluhan kelas tombol yang dulu
    didefinisikan sendiri-sendiri di tiap halaman. Bentuknya mengikuti pasangan tombol
    "Batal / Simpan Perubahan": tinggi 40px, radius 10px, teks 14px/600, ikon dan label
    berjarak 8px.

    Diangkat dari .mk-kegiatan-btn yang sebelumnya hanya dipakai bab Kegiatan
    (resources/views/partials/kegiatan-theme.blade.php), lalu diseragamkan untuk
    seluruh modul.

    Hanya ada DUA warna, sesuai keputusan tim:
      --primary     navy solid  — aksi utama (Simpan, Kirim, Setujui, Hapus, Buat, ...)
      --secondary   putih outline — aksi pendamping (Batal, Kembali, Reset, ...)

    Warna semantik (merah untuk hapus, hijau untuk setuju) sengaja TIDAK dipakai lagi;
    perbedaan aksi disampaikan lewat label dan dialog konfirmasi, bukan warna tombol.

    ── Cara pakai ──────────────────────────────────────────────────────────────────────
        <button type="submit" class="mk-btn mk-btn--primary">
            <svg ...></svg> Simpan Perubahan
        </button>

        <a href="..." class="mk-btn mk-btn--secondary">
            <svg ...></svg> Batal
        </a>

    ── Pengubah ────────────────────────────────────────────────────────────────────────
      .mk-btn--icon     tombol ikon saja; jadi persegi 40x40 tanpa padding teks
      .mk-btn--sm       ukuran kecil (32px, teks 13px) untuk toolbar & baris tabel
      .mk-btn--block    melebar penuh mengikuti induknya
      .mk-btn--disabled tampilan nonaktif untuk <a> yang tidak bisa memakai :disabled

    Ditulis sebagai CSS biasa karena layout modul ini memuat Bootstrap, bukan Tailwind.
    Disisipkan SETELAH @stack('styles') di layout supaya menang atas sisa gaya lama
    yang masih menempel di masing-masing halaman.
--}}
<style>
    .mk-btn {
        appearance: none;
        -webkit-appearance: none;
        box-sizing: border-box;
        min-height: 40px;
        padding: 0 18px;
        border: 1px solid transparent;
        border-radius: 10px;
        background: #ffffff;
        color: var(--c-fg-sec, #353849);
        font: inherit;
        font-size: 14px;
        font-weight: 600;
        line-height: 1.2;
        text-align: center;
        text-decoration: none !important;
        white-space: nowrap;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        cursor: pointer;
        transition: background-color 150ms ease, border-color 150ms ease, color 150ms ease,
            box-shadow 150ms ease, transform 150ms ease;
    }

    /* Ikon tidak boleh ikut memipih saat labelnya panjang. */
    .mk-btn > svg,
    .mk-btn > .material-symbols-outlined { flex: 0 0 auto; }
    .mk-btn > .material-symbols-outlined { font-size: 18px; line-height: 1; }

    .mk-btn:focus { outline: none; }

    /* ── Aksi utama: navy solid ─────────────────────────────────────────────── */
    .mk-btn--primary {
        background: var(--c-primary, #0B266E);
        border-color: var(--c-primary, #0B266E);
        color: #ffffff;
    }
    .mk-btn--primary:not(:disabled):not(.mk-btn--disabled):not([aria-disabled="true"]):hover {
        background: var(--c-primary-hover, #091958);
        border-color: var(--c-primary-hover, #091958);
        color: #ffffff;
        transform: translateY(-1px);
    }
    .mk-btn--primary:not(:disabled):not(.mk-btn--disabled):not([aria-disabled="true"]):active {
        transform: translateY(0);
    }
    .mk-btn--primary:focus-visible {
        box-shadow: 0 0 0 3px var(--c-primary-shadow-strong, rgba(11, 38, 110, 0.30));
    }

    /* ── Aksi pendamping: putih outline ─────────────────────────────────────── */
    .mk-btn--secondary {
        background: #ffffff;
        border-color: var(--c-border, #DFE1E7);
        color: var(--c-fg-sec, #353849);
    }
    .mk-btn--secondary:not(:disabled):not(.mk-btn--disabled):not([aria-disabled="true"]):hover {
        background: var(--c-primary-subtle, rgba(11, 38, 110, 0.08));
        border-color: var(--c-primary, #0B266E);
        color: var(--c-primary, #0B266E);
        transform: translateY(-1px);
    }
    .mk-btn--secondary:not(:disabled):not(.mk-btn--disabled):not([aria-disabled="true"]):active {
        transform: translateY(0);
    }
    .mk-btn--secondary:focus-visible {
        border-color: var(--c-primary, #0B266E);
        box-shadow: 0 0 0 3px var(--c-primary-shadow, rgba(11, 38, 110, 0.12));
    }

    /* ── Pengubah bentuk ────────────────────────────────────────────────────── */
    .mk-btn--icon {
        width: 40px;
        min-width: 40px;
        padding: 0;
        gap: 0;
    }

    .mk-btn--sm {
        min-height: 32px;
        padding: 0 14px;
        font-size: 13px;
        border-radius: 8px;
    }
    .mk-btn--sm.mk-btn--icon { width: 32px; min-width: 32px; padding: 0; }
    .mk-btn--sm > .material-symbols-outlined { font-size: 16px; }

    .mk-btn--block { display: flex; width: 100%; }

    /* ── Nonaktif ───────────────────────────────────────────────────────────── */
    .mk-btn:disabled,
    .mk-btn--disabled,
    .mk-btn[aria-disabled="true"] {
        opacity: 0.55;
        cursor: not-allowed;
        transform: none;
    }

    /* ── Menu aksi "⋯" ──────────────────────────────────────────────────────
       Panel dropdown yang berpasangan dengan tombol .mk-btn--icon berisi titik
       tiga. Dipakai di kolom/baris aksi daftar (Direktori, Pengaduan, Pengumuman)
       supaya tidak ada deretan tombol ikon yang berjejer.

       Pembungkusnya wajib position:relative dan memegang x-data="{ open: false }".

           <div style="position: relative;" x-data="{ open: false }">
               <button class="mk-btn mk-btn--secondary mk-btn--icon mk-btn--sm"
                       @click="open = !open" @click.outside="open = false"> ⋯ </button>
               <div class="mk-menu" x-show="open" x-cloak style="display: none;"> ... </div>
           </div>
    */
    .mk-menu {
        position: absolute;
        right: 0;
        top: calc(100% + 5px);
        z-index: 40;
        min-width: 180px;
        padding: 5px;
        background: #ffffff;
        border: 1px solid var(--c-border, #DFE1E7);
        border-radius: 10px;
        box-shadow: 0 8px 24px rgba(0, 0, 0, 0.1);
        text-align: left;
    }
    /* Untuk baris paling bawah daftar, supaya panel tidak terpotong tepi kartu. */
    .mk-menu--up { top: auto; bottom: calc(100% + 5px); }

    /* Form pembungkus tombol aksi tidak boleh menambah jarak di dalam menu. */
    .mk-menu form { margin: 0; }

    .mk-menu-item {
        display: flex;
        align-items: center;
        gap: 8px;
        width: 100%;
        box-sizing: border-box;
        padding: 7px 10px;
        border: none;
        border-radius: 6px;
        background: none;
        color: var(--c-fg-sec, #353849);
        font-family: inherit;
        font-size: 12px;
        font-weight: 500;
        line-height: 1.3;
        text-align: left;
        text-decoration: none !important;
        white-space: nowrap;
        cursor: pointer;
        transition: background 0.12s;
    }
    .mk-menu-item:hover { background: var(--c-bg, #F6F8FA); color: var(--c-fg-sec, #353849); }
    /* Ukuran disamakan lewat CSS supaya atribut width/height pada tiap <svg> warisan
       markup lama tidak perlu disunting satu per satu. */
    .mk-menu-item > svg {
        flex: 0 0 auto;
        width: 14px;
        height: 14px;
        color: var(--c-fg-muted, #666D80);
    }

    /* Keadaan menyala, mis. "Lepas Pin" saat pengumuman sedang dipin. */
    .mk-menu-item.is-active,
    .mk-menu-item.is-active > svg { color: var(--c-primary, #0B266E); }
    .mk-menu-item.is-active { font-weight: 600; }

    .mk-menu-sep {
        height: 1px;
        margin: 4px 0;
        background: var(--c-border, #DFE1E7);
    }
</style>
