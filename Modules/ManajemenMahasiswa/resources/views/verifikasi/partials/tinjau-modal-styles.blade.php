{{--
    Kerangka modal Tinjau — dipakai bersama halaman Verifikasi (admin.blade.php)
    dan Klaim Reward (reward.blade.php).

    Bukti (kiri) & data yang diklaim mahasiswa (kanan) tampil bersamaan, supaya
    verifikator membandingkan keduanya dalam satu layar tanpa pindah tab.

    JANGAN menyalin style ini ke masing-masing halaman: dua salinan pasti
    melenceng seiring waktu, dan hilanglah gunanya menyeragamkan tampilan.

    Lebar kolom data bisa diatur per halaman lewat --tp-lebar-data pada elemen
    modal, mis. Reward butuh lebih lega karena memuat kuota + daftar mata kuliah.

    Nilai warna mengikuti token global SITKOM; fallback yang tersisa memakai
    nilai token yang sama agar modal tetap aman saat dipakai lintas layout.
--}}
<style>
    /* Tombol pembuka modal di kolom Aksi — ikut tinggal di sini supaya pemicu
       dan modalnya tidak pernah berbeda tampilan antar halaman */
    .btn-tinjau {
        display: inline-flex; align-items: center; gap: 4px;
        background: var(--c-primary-subtle, rgba(11,38,110,0.08)); color: var(--c-primary, #0B266E);
        border: 1px solid var(--c-primary-border, #5C78B8); padding: 5px 14px; border-radius: 8px;
        font-size: .8rem; font-weight: 600; cursor: pointer; transition: all .15s;
    }
    .btn-tinjau:hover { background: var(--c-primary-subtle, rgba(11,38,110,0.08)); border-color: var(--c-primary, #0B266E); }

    /* Sedikit lebih lebar dari modal-xl — cukup agar sertifikat terbaca,
       tanpa memenuhi seluruh layar */
    .tinjau-modal .modal-dialog { max-width: min(1280px, 94vw); }

    /* Kolom data dipatok lebarnya; sisa ruang sepenuhnya untuk bukti */
    .tp-grid { display: grid; grid-template-columns: minmax(0, 1fr) var(--tp-lebar-data, 320px); }

    /* Tinggi penampil mengikuti tinggi layar, dibatasi agar tidak melebihi viewport */
    .tp-viewer { width: 100%; height: min(64vh, 680px); border: none; border-radius: 10px; background: var(--c-card, #fff); }
    .tp-viewer-img { max-width: 100%; max-height: min(64vh, 680px); border-radius: 10px; box-shadow: 0 2px 14px rgba(0,0,0,.10); }

    .tp-pane-bukti {
        background: var(--c-bg, #F6F8FA); border-right: 1px solid var(--c-border, #DFE1E7); padding: 16px; gap: 10px;
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

    .tp-field { display: flex; gap: 12px; padding: 8px 0; border-bottom: 1px dashed var(--c-border, #DFE1E7); }
    .tp-field-label {
        flex: 0 0 92px; padding-top: 2px; font-size: 10.5px; font-weight: 700;
        color: var(--c-fg-muted, #666D80); text-transform: uppercase; letter-spacing: .03em;
    }
    .tp-field-value { font-size: 13.5px; font-weight: 600; color: var(--c-fg, #0D0D12); line-height: 1.45; word-break: break-word; }

    /* Nilai yang berupa daftar pendek — mis. mata kuliah yang diusulkan */
    .tp-chips { display: flex; flex-wrap: wrap; gap: 4px; }
    .tp-chip {
        font-size: 12px; font-weight: 600; color: var(--c-primary, #0B266E); background: var(--c-card, #fff);
        border: 1px solid var(--c-primary-border, #5C78B8); border-radius: 50px; padding: 2px 10px;
    }

    .tp-thumbs { display: flex; gap: 6px; flex-wrap: wrap; justify-content: center; }
    .tp-thumb {
        width: 34px; height: 34px; border-radius: 6px; border: 1px solid var(--c-border, #DFE1E7);
        background: var(--c-card, #fff); cursor: pointer; padding: 0; overflow: hidden;
        display: inline-flex; align-items: center; justify-content: center;
        font-size: 9px; font-weight: 700; color: var(--c-error, #DF1C41);
    }
    .tp-thumb img { width: 100%; height: 100%; object-fit: cover; }
    .tp-thumb.active { border-color: var(--c-primary, #0B266E); box-shadow: 0 0 0 2px var(--c-primary-subtle, rgba(11,38,110,.08)); }

    /* Ringkasan data klaim dalam satu kotak (dipakai kolom data Reward) */
    .tinjau-info {
        font-size: .87rem; color: var(--c-fg-sec, #353849); background: var(--c-bg, #F6F8FA);
        border: 1px solid var(--c-border, #DFE1E7); border-radius: 10px;
        padding: 12px 14px; line-height: 1.7;
    }
    .tinjau-info .lbl { color: var(--c-fg-muted, #666D80); }

    /* Rambu kuota — sengaja mencolok saat penuh, karena inilah penentu
       boleh-tidaknya klaim reward disetujui */
    .kuota-pill {
        display: inline-block; font-size: .8rem; font-weight: 600;
        padding: 5px 12px; border-radius: 50px;
        background: var(--c-primary-subtle, rgba(11,38,110,0.08)); color: var(--c-primary, #0B266E);
    }
    .kuota-pill.penuh { background: var(--c-error-subtle, #FADAE1); color: var(--c-error, #DF1C41); }
    /* Sisa satu jatah — kuning seperti .sk-lawas: belum menghalangi, tapi layak disadari */
    .kuota-pill.hampir { background: var(--c-warning-subtle, #F9ECCB); color: var(--c-warning, #956321); }

    /* Penanda klaim yang diajukan di bawah SK lama — sengaja kuning, bukan merah:
       ini bukan kesalahan, hanya konteks yang harus disadari sebelum memutuskan */
    .sk-lawas {
        margin-top: 8px; padding: 8px 12px; border-radius: 8px;
        background: var(--c-warning-subtle, #F9ECCB); border: 1px solid var(--c-warning, #956321); color: var(--c-warning, #956321);
        font-size: 11.5px; font-weight: 600; line-height: 1.45;
    }

    /* Baris tombol keputusan Tolak/Setujui; bentuk tombolnya dari .mk-btn. */
    .tp-aksi { display: flex; gap: 10px; margin-top: 14px; justify-content: flex-end; }

    /* Konfirmasi keputusan yang tidak bisa ditarik kembali (Setujui verifikasi,
       Batalkan Persetujuan reward) — muncul di tempat tombol aksinya, bukan
       sebagai modal kedua, supaya tidak ada backdrop Bootstrap yang bertumpuk. */
    .tp-konfirmasi {
        margin-top: 14px; padding: 12px 14px; border-radius: 10px;
        background: var(--c-warning-subtle, #F9ECCB); border: 1px solid var(--c-warning, #956321);
        color: var(--c-warning, #956321); font-size: 12.5px; font-weight: 600; line-height: 1.5;
    }
    .tp-konfirmasi p { margin: 0; }
    .tp-konfirmasi .tp-aksi { margin-top: 10px; }

    @media (max-width: 991px) {
        .tp-grid { grid-template-columns: 1fr; }
        .tp-pane-bukti { border-right: none; border-bottom: 1px solid var(--c-border, #DFE1E7); }
        .tp-pane-data { max-height: none; }
        .tp-viewer { height: 58vh; }
        .tp-viewer-img { max-height: 58vh; }
    }
</style>
