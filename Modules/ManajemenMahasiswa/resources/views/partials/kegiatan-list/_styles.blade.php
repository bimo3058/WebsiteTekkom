{{--
    Gaya daftar kartu bab Manajemen Kegiatan — dipakai bersama oleh daftar Rencana
    Proker, Pelaksanaan Kegiatan, dan Laporan & Arsip. Dulu tiap daftar membawa salinan
    CSS sendiri dengan nama kelas berbeda (.proker-card, .pelaksanaan-card,
    .kegiatan-card) dan isinya pelan-pelan berbeda. Kartunya di _kartu.blade.php,
    panel pencarian & filter di _filter.blade.php.
--}}
<style>
    .kegiatan-card {
        background: var(--c-surface);
        border: 1px solid var(--c-border);
        border-radius: 12px;
        box-shadow: 0 1px 2px rgba(0, 0, 0, 0.04);
        display: flex;
        flex-direction: column;
        height: 100%;
        overflow: hidden;
        text-decoration: none !important;
        transition: all 0.2s ease;
    }
    .kegiatan-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 12px 24px -4px var(--c-primary-shadow);
        border-color: var(--c-primary-border);
    }
    .kegiatan-card-image {
        width: 100%;
        aspect-ratio: 16 / 9;
        background: linear-gradient(135deg, var(--c-primary-subtle) 0%, var(--c-primary-shadow) 100%);
        display: flex;
        align-items: center;
        justify-content: center;
        overflow: hidden;
        position: relative;
    }
    /* Badge tahap (Rencana Proker saja) menempel di pojok kiri atas banner */
    .kegiatan-card-tahap {
        position: absolute;
        top: 10px;
        left: 10px;
        box-shadow: 0 1px 4px rgba(13, 13, 18, 0.18) !important;
    }
    .kegiatan-card-image img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }
    .kegiatan-card-body {
        padding: 16px 18px 18px;
        display: flex;
        flex-direction: column;
        flex: 1;
    }
    .kegiatan-badges {
        display: flex;
        flex-wrap: wrap;
        gap: 6px;
    }
    .kegiatan-card .badge-bidang,
    .kegiatan-card .badge-kategori {
        font-size: 11px;
        font-weight: 700;
        padding: 3px 10px;
        border-radius: 20px;
        background: var(--c-primary-subtle);
        color: var(--c-primary);
    }
    .kegiatan-card-title {
        font-weight: 700;
        font-size: 15px;
        color: var(--c-fg);
        line-height: 1.4;
        margin: 8px 0 6px;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }
    .kegiatan-card-desc {
        font-size: 12px;
        color: var(--c-fg-muted);
        line-height: 1.55;
        margin-bottom: 12px;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }
    /* Satu isian per baris (tanggal, jam, lokasi, dosen pendamping) — teks panjang dipotong
       dengan elipsis, isi lengkapnya ada di tooltip. */
    .kegiatan-card-meta {
        display: flex;
        flex-direction: column;
        gap: 5px;
        margin-top: auto;
        padding-top: 10px;
        border-top: 1px solid var(--c-surface-muted);
        font-size: 12px;
        font-weight: 500;
        color: var(--c-fg-muted);
    }
    .kegiatan-card-meta > div {
        display: flex;
        align-items: center;
        gap: 6px;
        min-width: 0;
    }
    .kegiatan-card-meta > div > svg {
        flex-shrink: 0;
    }
    .kegiatan-card-meta .teks {
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }

    /* ── Empty State ── */
    .empty-state {
        text-align: center;
        padding: 50px 20px;
        color: var(--c-fg-muted);
    }
    .empty-state h5 {
        color: var(--c-fg-muted);
        font-weight: 600;
        margin-bottom: 4px;
    }
    .empty-state p {
        font-size: 14px;
        color: var(--c-fg-muted);
    }

    /* ── Kelompok per status (S-86): judul kiri, "View all →" kanan ── */
    .mk-kelompok + .mk-kelompok { margin-top: 28px; }
    .mk-kelompok__head {
        display: flex;
        align-items: baseline;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: 4px 16px;
        margin-bottom: 12px;
    }
    .mk-kelompok__judul {
        font-size: 16px;
        font-weight: 600;
        color: var(--c-fg);
        margin: 0;
    }
    .mk-kelompok__jumlah {
        font-weight: 500;
        color: var(--c-fg-muted);
    }
    .mk-kelompok__tautan {
        font-size: 13px;
        font-weight: 600;
        color: var(--c-primary);
        text-decoration: none;
        white-space: nowrap;
    }
    .mk-kelompok__tautan:hover,
    .mk-kelompok__tautan:focus-visible { text-decoration: underline; }
    .mk-kelompok__kosong {
        padding: 18px 16px;
        border: 1px dashed var(--c-border);
        border-radius: 12px;
        font-size: 13px;
        color: var(--c-fg-muted);
        text-align: center;
    }

    /* ── Pengalih tampilan Kartu / Daftar (S-87, admin saja) — segmented control
          setinggi tombol Filter ── */
    .mk-tampilan {
        display: inline-flex;
        align-items: center;
        height: 34px;
        padding: 3px;
        gap: 2px;
        border: 1px solid var(--c-border);
        border-radius: 8px;
        background: var(--c-surface);
        box-sizing: border-box;
    }
    .mk-tampilan__btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 5px;
        height: 26px;
        padding: 0 9px;
        border-radius: 6px;
        color: var(--c-fg-muted);
        font-size: 12px;
        font-weight: 600;
        text-decoration: none !important;
        transition: background 0.15s, color 0.15s;
    }
    .mk-tampilan__btn:hover { color: var(--c-fg); background: var(--c-surface-subtle); }
    .mk-tampilan__btn.is-aktif {
        background: var(--c-primary-subtle);
        color: var(--c-primary);
    }
    .mk-tampilan__btn:focus-visible { outline: 2px solid var(--c-primary); outline-offset: 1px; }
    @media (max-width: 575.98px) {
        .mk-tampilan__label { display: none; }
    }

    /* ── Mode Daftar (S-87): baris berkolom, thumbnail kiri, seluruh baris bisa diklik ── */
    .kegiatan-daftar {
        --kd-kolom: minmax(240px, 2.4fr) 150px minmax(150px, 1.3fr) 130px minmax(120px, 1fr) minmax(140px, 1.1fr) 18px;
        background: var(--c-surface);
        border: 1px solid var(--c-border);
        border-radius: 12px;
        overflow: hidden;
        /* Wadah konten halaman berupa flex kolom: tanpa ini daftar ikut menyusut dan
           baris bawahnya terpotong (terlihat di mode tersaring 12 baris). */
        flex-shrink: 0;
    }
    .kegiatan-daftar__head,
    .kegiatan-daftar__row {
        display: grid;
        grid-template-columns: var(--kd-kolom);
        align-items: center;
        gap: 16px;
        padding: 0 18px;
    }
    .kegiatan-daftar__head {
        height: 38px;
        background: var(--c-surface-subtle);
        border-bottom: 1px solid var(--c-border);
        font-size: 11px;
        font-weight: 700;
        letter-spacing: .04em;
        text-transform: uppercase;
        color: var(--c-fg-muted);
    }
    .kegiatan-daftar__row {
        min-height: 68px;
        padding-top: 10px;
        padding-bottom: 10px;
        text-decoration: none !important;
        color: var(--c-fg);
        transition: background 0.15s;
    }
    .kegiatan-daftar__row + .kegiatan-daftar__row { border-top: 1px solid var(--c-surface-muted); }
    .kegiatan-daftar__row:hover,
    .kegiatan-daftar__row:focus-visible {
        background: var(--c-primary-subtle);
        outline: none;
    }
    .kegiatan-daftar__row:hover .kd-chevron { color: var(--c-primary); transform: translateX(2px); }
    .kegiatan-daftar__row > div { min-width: 0; }

    .kd-proker { display: flex; align-items: center; gap: 12px; }
    .kd-thumb {
        flex-shrink: 0;
        width: 64px;
        height: 40px;
        border-radius: 8px;
        overflow: hidden;
        display: flex;
        align-items: center;
        justify-content: center;
        background: linear-gradient(135deg, var(--c-primary-subtle) 0%, var(--c-primary-shadow) 100%);
    }
    .kd-thumb img { width: 100%; height: 100%; object-fit: cover; }
    .kd-proker__teks { min-width: 0; }
    .kd-judul {
        font-size: 14px;
        font-weight: 700;
        line-height: 1.35;
        color: var(--c-fg);
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }
    .kd-desk {
        margin-top: 2px;
        font-size: 12px;
        color: var(--c-fg-muted);
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }
    .kd-badges { display: flex; flex-wrap: wrap; gap: 4px; }
    .kegiatan-daftar .badge-bidang,
    .kegiatan-daftar .badge-kategori {
        font-size: 11px;
        font-weight: 700;
        padding: 2px 8px;
        border-radius: 20px;
        background: var(--c-primary-subtle);
        color: var(--c-primary);
        white-space: nowrap;
    }
    .kegiatan-daftar .badge-bidang--peringatan { background: #F9ECCB; color: #956321; }
    .kd-utama { font-size: 12.5px; font-weight: 600; color: var(--c-fg); }
    .kd-sub { font-size: 11.5px; color: var(--c-fg-muted); margin-top: 1px; }
    .kd-sub--primer { color: var(--c-primary); font-weight: 700; }
    .kd-potong { overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
    .kd-kosong { color: var(--c-fg-muted); font-size: 12px; }
    .kd-chevron { color: var(--c-fg-muted); display: flex; transition: transform 0.15s, color 0.15s; }

    /* Layar menengah: lokasi & dosen disembunyikan dulu (ada di halaman detail) */
    @media (max-width: 1399.98px) {
        .kegiatan-daftar { --kd-kolom: minmax(240px, 2.4fr) 150px minmax(150px, 1.3fr) 130px minmax(140px, 1.1fr) 18px; }
        .kegiatan-daftar .kd-col-lokasi { display: none; }
    }
    @media (max-width: 1099.98px) {
        .kegiatan-daftar { --kd-kolom: minmax(220px, 2fr) 150px 130px 18px; }
        .kegiatan-daftar .kd-col-dosen,
        .kegiatan-daftar .kd-col-bidang { display: none; }
    }
    /* Ponsel: baris bertumpuk — judul di atas, status + jadwal di bawahnya */
    @media (max-width: 639.98px) {
        .kegiatan-daftar__head { display: none; }
        .kegiatan-daftar__row {
            grid-template-columns: 1fr auto;
            grid-template-areas: "proker proker" "status jadwal";
            gap: 8px 12px;
            padding: 12px 14px;
        }
        .kegiatan-daftar__row .kd-proker { grid-area: proker; }
        .kegiatan-daftar__row .kd-status { grid-area: status; }
        .kegiatan-daftar__row .kd-jadwal { grid-area: jadwal; text-align: right; }
        .kegiatan-daftar__row .kd-chevron { display: none; }
        .kd-thumb { width: 52px; height: 34px; }
    }
</style>
