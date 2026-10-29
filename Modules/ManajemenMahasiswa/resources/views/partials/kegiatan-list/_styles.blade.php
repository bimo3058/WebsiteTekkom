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
</style>
