{{--
    Kerangka modal Tinjau — dipakai bersama halaman Verifikasi (admin.blade.php)
    dan Klaim Reward (reward.blade.php).

    Bukti (kiri) & data yang diklaim mahasiswa (kanan) tampil bersamaan, supaya
    verifikator membandingkan keduanya dalam satu layar tanpa pindah tab.

    JANGAN menyalin style ini ke masing-masing halaman: dua salinan pasti
    melenceng seiring waktu, dan hilanglah gunanya menyeragamkan tampilan.

    Lebar kolom data bisa diatur per halaman lewat --tp-lebar-data pada elemen
    modal, mis. Reward butuh lebih lega karena memuat kuota + daftar mata kuliah.

    Nilai warna memakai fallback (var(--x, #hex)) supaya tetap benar di halaman
    yang belum memuat token warna global.
--}}
<style>
    /* Tombol pembuka modal di kolom Aksi — ikut tinggal di sini supaya pemicu
       dan modalnya tidak pernah berbeda tampilan antar halaman */
    .btn-tinjau {
        display: inline-flex; align-items: center; gap: 4px;
        background: var(--c-primary-subtle, rgba(11,38,110,0.06)); color: var(--c-primary, #0B266E);
        border: 1px solid rgba(11,38,110,0.18); padding: 5px 14px; border-radius: 8px;
        font-size: .8rem; font-weight: 600; cursor: pointer; transition: all .15s;
    }
    .btn-tinjau:hover { background: rgba(11,38,110,0.12); border-color: var(--c-primary-border, #3C518B); }

    /* Sedikit lebih lebar dari modal-xl — cukup agar sertifikat terbaca,
       tanpa memenuhi seluruh layar */
    .tinjau-modal .modal-dialog { max-width: min(1280px, 94vw); }

    /* Kolom data dipatok lebarnya; sisa ruang sepenuhnya untuk bukti */
    .tp-grid { display: grid; grid-template-columns: minmax(0, 1fr) var(--tp-lebar-data, 320px); }

    /* Tinggi penampil mengikuti tinggi layar, dibatasi agar tidak melebihi viewport */
    .tp-viewer { width: 100%; height: min(64vh, 680px); border: none; border-radius: 10px; background: #fff; }
    .tp-viewer-img { max-width: 100%; max-height: min(64vh, 680px); border-radius: 10px; box-shadow: 0 2px 14px rgba(0,0,0,.10); }

    .tp-pane-bukti {
        background: #f4f6fa; border-right: 1px solid #eef0f4; padding: 16px; gap: 10px;
        display: flex; flex-direction: column; align-items: center; justify-content: center;
    }
    .tp-pane-data { padding: 20px 22px; display: flex; flex-direction: column; overflow-y: auto; max-height: min(78vh, 820px); }

    /* Judul kecil pemisah antar blok di kolom data */
    .tp-pane-heading {
        font-size: 10.5px; font-weight: 700; color: var(--c-fg-muted, #666D80);
        text-transform: uppercase; letter-spacing: .05em; margin: 0 0 6px;
    }

    /* Satu baris bisa memuat lebih dari satu kelompok data (mis. data pengajuan
       + klaim rewardnya). Tiap kelompok jadi satu .tp-section dengan judulnya
       sendiri, supaya batas antar kelompok terbaca tanpa garis tambahan. */
    .tp-section + .tp-section { margin-top: 20px; }

    .tp-field { display: flex; gap: 12px; padding: 8px 0; border-bottom: 1px dashed #eef0f4; }
    .tp-field-label {
        flex: 0 0 92px; padding-top: 2px; font-size: 10.5px; font-weight: 700;
        color: var(--c-fg-muted, #666D80); text-transform: uppercase; letter-spacing: .03em;
    }
    .tp-field-value { font-size: 13.5px; font-weight: 600; color: var(--c-fg, #0D0D12); line-height: 1.45; word-break: break-word; }

    /* Nilai yang berupa daftar pendek — mis. mata kuliah yang diusulkan */
    .tp-chips { display: flex; flex-wrap: wrap; gap: 4px; }
    .tp-chip {
        font-size: 12px; font-weight: 600; color: var(--c-primary, #0B266E); background: #fff;
        border: 1px solid rgba(11,38,110,0.18); border-radius: 50px; padding: 2px 10px;
    }

    .tp-thumbs { display: flex; gap: 6px; flex-wrap: wrap; justify-content: center; }
    .tp-thumb {
        width: 34px; height: 34px; border-radius: 6px; border: 1px solid var(--c-border, #DFE1E7);
        background: #fff; cursor: pointer; padding: 0; overflow: hidden;
        display: inline-flex; align-items: center; justify-content: center;
        font-size: 9px; font-weight: 700; color: var(--c-error, #dc2626);
    }
    .tp-thumb img { width: 100%; height: 100%; object-fit: cover; }
    .tp-thumb.active { border-color: var(--c-primary, #0B266E); box-shadow: 0 0 0 2px rgba(11,38,110,.15); }

    /* Ringkasan data klaim dalam satu kotak (dipakai kolom data Reward) */
    .tinjau-info {
        font-size: .87rem; color: var(--c-fg-sec, #374151); background: #fafafa;
        border: 1px solid var(--c-border, #DFE1E7); border-radius: 10px;
        padding: 12px 14px; line-height: 1.7;
    }
    .tinjau-info .lbl { color: var(--c-fg-muted, #666D80); }

    /* Rambu kuota — sengaja mencolok saat penuh, karena inilah penentu
       boleh-tidaknya klaim reward disetujui */
    .kuota-pill {
        display: inline-block; font-size: .8rem; font-weight: 600;
        padding: 5px 12px; border-radius: 50px;
        background: var(--c-primary-subtle, rgba(11,38,110,0.06)); color: var(--c-primary, #0B266E);
    }
    .kuota-pill.penuh { background: var(--c-error-subtle, #fef2f2); color: var(--c-error, #dc2626); }

    /* Rincian klaim yang memakan kuota. Kotaknya memakai .tinjau-info dan
       pemisah antar barisnya memakai garis putus-putus seperti .tp-field —
       dua pola yang sudah ada, supaya blok ini tidak jadi pola ketiga. */
    .kuota-dipakai-blok { margin-bottom: 12px; }
    .kuota-dipakai-item { padding: 7px 0; border-bottom: 1px dashed #eef0f4; }
    .kuota-dipakai-item:first-child { padding-top: 0; }
    .kuota-dipakai-item:last-child { padding-bottom: 0; border-bottom: none; }
    .kuota-dipakai-nama {
        font-size: 12.5px; font-weight: 600; line-height: 1.4;
        color: var(--c-fg, #0D0D12); word-break: break-word;
    }
    .kuota-dipakai-ket {
        font-size: 11px; line-height: 1.4; color: var(--c-fg-muted, #666D80); margin-top: 2px;
    }

    /* Penanda klaim yang diajukan di bawah SK lama — sengaja kuning, bukan merah:
       ini bukan kesalahan, hanya konteks yang harus disadari sebelum memutuskan */
    .sk-lawas {
        margin-top: 8px; padding: 8px 12px; border-radius: 8px;
        background: #FFFBEB; border: 1px solid #fde68a; color: #92400e;
        font-size: 11.5px; font-weight: 600; line-height: 1.45;
    }

    /* Tombol keputusan — Tolak sengaja dibuat outline agar tidak bersaing
       menarik perhatian dengan jalur utamanya */
    .tp-aksi { display: flex; gap: 10px; margin-top: 14px; }
    .tp-btn-tolak {
        flex: 1; padding: 9px 0; border-radius: 10px; border: 1px solid #fecaca;
        background: var(--c-error-subtle, #fef2f2); color: var(--c-error, #dc2626);
        font-weight: 700; font-size: 13.5px; cursor: pointer;
    }
    .tp-btn-setujui {
        flex: 1; padding: 9px 0; border-radius: 10px; border: none;
        background: #16a34a; color: #fff; font-weight: 700; font-size: 13.5px; cursor: pointer;
    }

    /* Tombol pengiriman formulir di dalam kerangka yang sama (mis. Ajukan
       Reward) — bentuknya menyamai tombol keputusan agar kedua modal terasa
       satu keluarga meski isinya berbeda. */
    .tp-btn-utama {
        flex: 1; padding: 9px 0; border-radius: 10px; border: none;
        background: var(--c-primary, #0B266E); color: #fff;
        font-weight: 700; font-size: 13.5px; cursor: pointer;
        display: inline-flex; align-items: center; justify-content: center; gap: 6px;
    }
    .tp-btn-utama:disabled { background: #C1C7CF; cursor: not-allowed; }
    .tp-btn-netral {
        flex: 0 0 auto; padding: 9px 20px; border-radius: 10px;
        border: 1px solid var(--c-border, #DFE1E7); background: #fff;
        color: var(--c-fg-sec, #374151); font-weight: 600; font-size: 13.5px; cursor: pointer;
    }
    .tp-btn-netral:hover { background: var(--c-bg, #f6f7f9); }

    /* Aksi mundur/destruktif pada modal baca-saja (mis. batalkan pengajuan) —
       selebar panel & di bawah datanya, jadi hanya diambil setelah dibaca. */
    .tp-btn-batal {
        width: 100%; padding: 9px 0; border-radius: 10px; border: 1px solid #fecaca;
        background: var(--c-error-subtle, #fef2f2); color: var(--c-error, #dc2626);
        font-weight: 700; font-size: 13.5px; cursor: pointer;
    }
    .tp-btn-batal:hover { background: #fee2e2; }

    @media (max-width: 991px) {
        .tp-grid { grid-template-columns: 1fr; }
        .tp-pane-bukti { border-right: none; border-bottom: 1px solid #eef0f4; }
        .tp-pane-data { max-height: none; }
        .tp-viewer { height: 58vh; }
        .tp-viewer-img { max-height: 58vh; }
    }
</style>
