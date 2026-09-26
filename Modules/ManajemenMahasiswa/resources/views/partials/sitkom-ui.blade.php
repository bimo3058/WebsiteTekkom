{{--
    Kit tampilan SITKOM untuk bab Direktori Mahasiswa, Manajemen Kegiatan, dan
    Verifikasi Data. Satu sumber untuk token warna, badge, kotak isian form,
    header modal, dan tabel — supaya status yang sama tidak lagi tampil dengan
    warna berbeda antar halaman.

    Acuan (tiap komponen meniru padanannya di SITKOM):
      token        resources/views/components/sidebar.blade.php (blok :root)
      badge status resources/views/components/ui/status-badge.blade.php
      badge tingkat resources/views/components/ui/role-badge.blade.php
      form         resources/views/superadmin/users/edit.blade.php (.input-field)
      modal        resources/views/superadmin/users/_modal_add.blade.php
      tabel        resources/views/superadmin/users/_table.blade.php

    Dua penyimpangan yang disengaja:
      - Warna teks badge & label form memakai token SITKOM, bukan nilai Tailwind
        di komponen aslinya (#059669, #94A3B8): nilai aslinya di bawah kontras
        4.5:1 WCAG AA untuk huruf 10–11px. Bentuknya tetap sama persis.
      - Cincin fokus form navy, bukan biru #3B82F6 milik Edit User — halaman
        SITKOM lain (pencarian User Management) sudah memakai navy.

    Layout mahasiswa & dosen tidak mendeklarasikan token, jadi blok :root di sini
    juga yang membuat halaman Verifikasi tampil benar untuk akun mahasiswa/alumni.

    Dipasang SETELAH partial palet/tema bab (palette, kegiatan-theme) supaya
    menang atas aturan badge lama yang masih ada di sana. Bab lain (Alumni,
    Pengumuman, Forum, Pengaduan) tidak memuat partial ini.
--}}
@once
<style>
    :root {
        --c-primary: #0B266E;
        --c-primary-hover: #091958;
        --c-primary-subtle: rgba(11, 38, 110, 0.08);
        --c-primary-border: #5C78B8;
        --c-primary-shadow: rgba(11, 38, 110, 0.12);
        --c-primary-shadow-strong: rgba(11, 38, 110, 0.30);
        --c-bg: #F6F8FA;
        --c-card: #FFFFFF;
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
        --c-sky: #0C4D6E;
        --c-sky-subtle: #D1F0F9;
        --shadow-card: 0px 1px 2px 0px rgba(228, 229, 231, 0.5);

        /* Skala tambahan dari resources/css/app.css (nama sama) */
        --c-grey-0: #F8F9FB;
        --c-grey-50: #ECEFF3;
        --c-success-50: #9DE0D3;
        --c-success-100: #40C4AA;
        --c-success-300: #174E43;
        --c-warning-50: #FBD982;
        --c-warning-100: #FFBD4C;
        --c-error-0: #FEEFF2;
        --c-error-50: #ED8296;
        --c-error-200: #95122B;
        --c-sky-50: #7EDCF1;
        --c-sky-100: #33CFFF;
        --c-grey-300: #A4ABB8;
    }

    [x-cloak] { display: none !important; }

    /* ── Badge status ─────────────────────────────────────────────────────────
       Bentuk kolom Status User Management: latar putih, garis tepi berwarna,
       titik di depan label. Nama kelas lama dipertahankan karena sebagian badge
       digambar JavaScript (modal Tinjau) dengan nama kelas yang sama.

       Satu status = satu warna di semua halaman:
         hijau  Aktif, Disetujui
         kuning Menunggu Review, Diajukan, Mangkir
         merah  Ditolak, DO
         langit Cuti, Berlangsung
         navy   Pindah Studi
         abu    Draft, Belum diklaim, Selesai, Wafat                            */
    .mm-status,
    .status-badge,
    .status-verif,
    .claim-badge,
    .detail-status-pill {
        --st-fg: var(--c-fg-muted);
        --st-border: var(--c-border);
        --st-dot: var(--c-grey-300);
        display: inline-flex;
        align-items: center;
        gap: 5px;
        padding: 3px 12px;
        border: 1px solid var(--st-border);
        border-radius: 9999px;
        background: #ffffff;
        box-shadow: none;
        color: var(--st-fg);
        font-size: 11px;
        font-weight: 500;
        line-height: 1.4;
        letter-spacing: normal;
        text-transform: none;
        white-space: nowrap;
    }
    .mm-status::before,
    .status-badge::before,
    .status-verif::before,
    .claim-badge::before,
    .detail-status-pill::before {
        content: "";
        flex-shrink: 0;
        width: 6px;
        height: 6px;
        border-radius: 50%;
        background: var(--st-dot);
    }

    /* Aturan warna ditulis ulang lengkap (latar, bayangan, garis) supaya menang
       atas aturan bertingkat sama di partial palet Direktori. */
    .mm-status--success,
    .status-badge.aktif, .status-badge.status-disetujui,
    .status-verif.approved, .claim-badge.disetujui, .detail-status-pill.disetujui {
        --st-fg: var(--c-success); --st-border: var(--c-success-50); --st-dot: var(--c-success-100);
        background: #ffffff; box-shadow: none; color: var(--st-fg); border-color: var(--st-border);
    }
    .mm-status--warning,
    .status-badge.mangkir, .status-badge.status-diajukan,
    .status-verif.pending, .claim-badge.diajukan, .detail-status-pill.diajukan {
        --st-fg: var(--c-warning); --st-border: var(--c-warning-50); --st-dot: var(--c-warning-100);
        background: #ffffff; box-shadow: none; color: var(--st-fg); border-color: var(--st-border);
    }
    .mm-status--error,
    .status-badge.drop_out, .status-badge.status-ditolak,
    .status-verif.rejected, .claim-badge.ditolak, .detail-status-pill.ditolak {
        --st-fg: var(--c-error); --st-border: var(--c-error-50); --st-dot: var(--c-error);
        background: #ffffff; box-shadow: none; color: var(--st-fg); border-color: var(--st-border);
    }
    .mm-status--sky,
    .status-badge.cuti, .status-badge.alumni, .status-badge.status-berlangsung {
        --st-fg: var(--c-sky); --st-border: var(--c-sky-50); --st-dot: var(--c-sky-100);
        background: #ffffff; box-shadow: none; color: var(--st-fg); border-color: var(--st-border);
    }
    .mm-status--primary,
    .status-badge.pindah_studi {
        --st-fg: var(--c-primary); --st-border: rgba(11, 38, 110, 0.25); --st-dot: var(--c-primary);
        background: #ffffff; box-shadow: none; color: var(--st-fg); border-color: var(--st-border);
    }
    .mm-status--neutral,
    .status-badge.wafat, .status-badge.status-draft, .status-badge.status-selesai,
    .claim-badge.belum {
        --st-fg: var(--c-fg-muted); --st-border: var(--c-border); --st-dot: var(--c-grey-300);
        background: #ffffff; box-shadow: none; color: var(--st-fg); border-color: var(--st-border);
    }

    /* ── Badge tingkat prestasi ───────────────────────────────────────────────
       Bentuk badge Role SITKOM (terisi tipis + garis, huruf kapital). Tingkat
       memakai keluarga navy supaya tidak bentrok dengan kuning "Menunggu". */
    .tingkat-badge {
        display: inline-block;
        padding: 3px 12px;
        border: 1px solid var(--c-border);
        border-radius: 9999px;
        background: var(--c-grey-50);
        color: var(--c-fg-sec);
        font-size: 11px;
        font-weight: 600;
        line-height: 1.4;
        letter-spacing: 0.02em;
        text-transform: uppercase;
        white-space: nowrap;
    }
    .tingkat-badge.internasional { background: var(--c-primary);        color: #ffffff;           border-color: var(--c-primary); }
    .tingkat-badge.nasional      { background: var(--c-primary-subtle); color: var(--c-primary);  border-color: rgba(11, 38, 110, 0.18); }
    .tingkat-badge.regional      { background: var(--c-sky-subtle);     color: var(--c-sky);      border-color: #BAE6FD; }
    /* Teks hijau tingkat lebih gelap: --c-success di atas latar hijau muda hanya 4.1:1 */
    .tingkat-badge.universitas   { background: var(--c-success-subtle); color: var(--c-success-300); border-color: var(--c-success-50); }
    .tingkat-badge.prodi         { background: var(--c-grey-50);        color: var(--c-fg-sec);   border-color: var(--c-border); }

    /* ── Kotak isian form ─────────────────────────────────────────────────────
       Edit User SITKOM: label kecil huruf kapital, kotak putih 13px sudut 8px.
       Nama kelas lama dipertahankan; aturannya menimpa .form-control Bootstrap
       yang ikut terpasang di elemen yang sama. */
    .form-label-custom {
        display: block;
        margin-bottom: 6px;
        color: var(--c-fg-muted);
        font-size: 10px;
        font-weight: 700;
        line-height: 1.4;
        letter-spacing: 0.08em;
        text-transform: uppercase;
    }
    .form-label-custom .required { color: var(--c-error); }

    .form-control-custom,
    .form-select-custom {
        width: 100%;
        box-sizing: border-box;
        padding: 8px 12px;
        border: 1px solid var(--c-border-strong);
        border-radius: 8px;
        background-color: #ffffff;
        color: var(--c-fg-sec);
        font-family: inherit;
        font-size: 13px;
        font-weight: 600;
        line-height: 1.5;
        outline: none;
        transition: border-color 0.2s, box-shadow 0.2s;
    }
    .form-select-custom { padding-right: 36px; }
    .form-control-custom::placeholder {
        color: var(--c-fg-placeholder);
        font-weight: 400;
    }
    .form-control-custom:focus,
    .form-select-custom:focus {
        border-color: var(--c-primary);
        box-shadow: 0 0 0 3px rgba(11, 38, 110, 0.10);
        background-color: #ffffff;
        color: var(--c-fg-sec);
        outline: none;
    }
    .form-control-custom:disabled,
    .form-select-custom:disabled {
        background-color: var(--c-bg);
        color: var(--c-fg-muted);
        cursor: not-allowed;
    }
    .form-control-custom.is-invalid,
    .form-select-custom.is-invalid { border-color: var(--c-error); }
    .form-control-custom.is-invalid:focus,
    .form-select-custom.is-invalid:focus { box-shadow: 0 0 0 3px rgba(223, 28, 65, 0.12); }

    /* Kotak pilih Alpine (components/ui/select) ukuran form — md & lg — disamakan
       tinggi (38px), sudut, dan hurufnya dengan kotak isian di atas. Ukuran sm
       (panel Filter, Per page) sengaja tidak diubah. Selektor dua kelas supaya
       menang atas gaya komponen yang dicetak belakangan. */
    .mk-select.mk-select--md,
    .mk-select.mk-select--lg { --mks-h: 38px; --mks-radius: 8px; --mks-font: 13px; --mks-pad: 12px; }
    .mk-select--md .mk-select-btn,
    .mk-select--lg .mk-select-btn { border-color: var(--c-border-strong); color: var(--c-fg-sec); font-weight: 600; }

    /* ── Modal ────────────────────────────────────────────────────────────────
       Tambah User SITKOM: sudut 16px, pita judul navy muda, ikon kotak navy,
       judul kapital 14px + subjudul navy, tombol tutup berupa ikon.
       Header dirakit komponen ui/modal-header. */
    .modal .modal-content {
        border: 1px solid var(--c-border);
        border-radius: 16px;
        box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
        overflow: hidden;
    }
    .modal .mm-modal-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
        padding: 16px 24px;
        background: rgba(11, 38, 110, 0.06);
        border-bottom: 1px solid var(--c-primary-subtle);
    }
    .mm-modal-header__lead {
        display: flex;
        align-items: center;
        gap: 12px;
        min-width: 0;
    }
    .mm-modal-header__icon {
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
        width: 36px;
        height: 36px;
        border-radius: 12px;
        background: var(--c-primary);
        color: #ffffff;
    }
    .mm-modal-header__icon svg { width: 18px; height: 18px; }
    .modal .mm-modal-header__title {
        margin: 0;
        color: var(--c-fg);
        font-size: 14px;
        font-weight: 700;
        line-height: 1.3;
        letter-spacing: -0.01em;
        text-transform: uppercase;
    }
    .mm-modal-header__subtitle {
        margin: 2px 0 0;
        color: var(--c-primary);
        font-size: 10px;
        font-weight: 500;
        line-height: 1.4;
    }
    .mm-modal-header__close {
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
        width: 32px;
        height: 32px;
        padding: 0;
        border: none;
        border-radius: 8px;
        background: transparent;
        color: var(--c-primary);
        cursor: pointer;
        transition: background-color 0.15s, color 0.15s;
    }
    .mm-modal-header__close:hover {
        background: var(--c-primary-subtle);
        color: var(--c-primary-hover);
    }
    .modal .modal-body { padding: 24px; }
    .modal .modal-footer {
        gap: 12px;
        padding: 16px 24px;
        background: rgba(248, 250, 252, 0.5);
        border-top: 1px solid #F1F5F9;
    }
    .modal .modal-footer > * { margin: 0; }
    .modal-backdrop {
        --bs-backdrop-bg: #0F172A;
        --bs-backdrop-opacity: 0.4;
    }

    /* ── Tabel ────────────────────────────────────────────────────────────────
       User Management: kepala #FAFAFA teks 11px abu, isi 13px hitam, garis
       antarbaris #F3F4F6, sorot baris #FAFAFA. */
    .mm-table { width: 100%; border-collapse: separate; border-spacing: 0; }
    .mm-table thead th {
        padding: 11px 16px;
        background: #FAFAFA;
        border-bottom: 1px solid var(--c-border);
        color: var(--c-fg-muted);
        font-size: 11px;
        font-weight: 600;
        text-align: left;
        white-space: nowrap;
    }
    .mm-table tbody td {
        padding: 14px 16px;
        border-bottom: 1px solid #F3F4F6;
        color: var(--c-fg);
        font-size: 13px;
        vertical-align: middle;
    }
    .mm-table tbody tr { transition: background 0.12s; }
    .mm-table tbody tr:hover td { background: #FAFAFA; }
    .mm-table tbody tr:last-child td { border-bottom: none; }
</style>
@endonce
