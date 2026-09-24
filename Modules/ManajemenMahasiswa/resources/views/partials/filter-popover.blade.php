{{-- Gaya tombol "Filter" + panel "Advanced Filters" untuk seluruh tabel/daftar modul ini
     (Direktori, Kegiatan, Verifikasi, Klaim Reward, Pengaduan).

     Bentuknya disamakan dengan panel filter tabel Audit Log global
     (resources/views/superadmin/audit-logs/_table.blade.php): tombol Filter membuka
     panel Alpine.js berisi label + <select> bertumpuk, lalu tombol "Terapkan" & "Reset".

     Sengaja memakai kelas CSS biasa, bukan utilitas Tailwind milik versi global,
     karena layout modul ini tidak memuat Tailwind. Tiap token warna diberi nilai
     bawaan yang sama dengan palet global, jadi partial ini tetap benar di halaman yang
     tidak mendeklarasikan token --c-* (mis. Pengaduan). Markup panelnya ada di tiap
     halaman karena isi <select>-nya berbeda-beda.

     Ukuran tombol mengikuti kolom pencarian di sebelahnya:
       (default)          34px — toolbar dalam kartu tabel (Direktori, Verifikasi)
       .filter-pop--md    38px — baris pencarian bab Kegiatan
       .filter-pop--lg    44px — toolbar Pengaduan --}}
<style>
    .filter-pop {
        --fp-h: 34px;
        --fp-radius: 8px;
        --fp-font: 12.5px;
        position: relative;
        display: inline-block;
    }
    .filter-pop--md { --fp-h: 38px; --fp-font: 13px; }
    .filter-pop--lg { --fp-h: 44px; --fp-radius: 12px; --fp-font: 13.5px; }

    .filter-pop-btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 6px;
        height: var(--fp-h);
        padding: 0 14px;
        background: #ffffff;
        border: 1px solid var(--c-border, #DFE1E7);
        border-radius: var(--fp-radius);
        color: var(--c-fg-sec, #353849);
        font-family: inherit;
        font-size: var(--fp-font);
        font-weight: 600;
        white-space: nowrap;
        cursor: pointer;
        box-sizing: border-box;
        transition: all 0.15s;
    }
    .filter-pop-btn.is-open {
        border-color: var(--c-primary, #0B266E);
        color: var(--c-primary, #0B266E);
    }
    /* Titik penanda: ada filter dropdown yang sedang aktif */
    .filter-pop-dot {
        width: 6px;
        height: 6px;
        margin-left: 2px;
        border-radius: 50%;
        background: var(--c-primary, #0B266E);
        flex-shrink: 0;
    }
    /* Lapisan transparan di belakang panel: klik di luar panel menutupnya. Dipakai
       (bukan @click.outside) supaya klik pada <select> di dalam panel tidak ikut menutup. */
    .filter-pop-backdrop {
        position: fixed;
        inset: 0;
        z-index: 48;
        background: transparent;
    }
    .filter-pop-panel {
        position: absolute;
        right: 0;
        top: calc(100% + 6px);
        z-index: 49;
        min-width: 280px;
        max-width: calc(100vw - 32px);
        padding: 14px;
        background: #ffffff;
        border: 1px solid var(--c-border, #DFE1E7);
        border-radius: 12px;
        box-shadow: 0 8px 24px rgba(0, 0, 0, 0.1);
        text-align: left;
    }
    .filter-pop-title {
        margin: 0 0 10px 0;
        font-size: 10px;
        font-weight: 700;
        letter-spacing: 0.08em;
        text-transform: uppercase;
        color: var(--c-fg-muted, #666D80);
    }
    .filter-pop-fields {
        display: flex;
        flex-direction: column;
        gap: 10px;
    }
    .filter-pop-label {
        display: block;
        margin: 0 0 4px 0;
        font-size: 10px;
        font-weight: 600;
        letter-spacing: 0.06em;
        text-transform: uppercase;
        color: var(--c-fg-muted, #666D80);
    }
    .filter-pop-select {
        width: 100%;
        height: 32px;
        padding: 0 10px;
        background: #ffffff;
        border: 1px solid var(--c-border, #DFE1E7);
        border-radius: 7px;
        color: var(--c-fg, #0D0D12);
        font-family: inherit;
        font-size: 12px;
        outline: none;
        cursor: pointer;
        box-sizing: border-box;
        transition: border-color 0.15s, box-shadow 0.15s;
    }
    .filter-pop-select:focus {
        border-color: var(--c-primary, #0B266E);
        box-shadow: 0 0 0 3px var(--c-primary-subtle, rgba(11, 38, 110, 0.08));
    }
    .filter-pop-actions {
        display: flex;
        gap: 6px;
        padding-top: 2px;
    }
    .filter-pop-submit,
    .filter-pop-reset {
        flex: 1;
        display: flex;
        align-items: center;
        justify-content: center;
        height: 32px;
        border-radius: 7px;
        font-family: inherit;
        font-size: 12px;
        cursor: pointer;
        text-decoration: none !important;
        transition: background 0.15s;
    }
    .filter-pop-submit {
        background: var(--c-primary, #0B266E);
        border: none;
        color: #ffffff;
        font-weight: 700;
    }
    .filter-pop-submit:hover { background: var(--c-primary-hover, #091958); }
    .filter-pop-reset {
        background: #ffffff;
        border: 1px solid var(--c-border, #DFE1E7);
        color: var(--c-fg-muted, #666D80);
        font-weight: 500;
    }
    .filter-pop-reset:hover { background: var(--c-bg, #F6F8FA); color: var(--c-fg-muted, #666D80); }

    /* Khusus kartu tabel yang toolbar-nya memuat panel ini (Direktori, Verifikasi, Klaim
       Reward). Panel menjulur keluar dari toolbar, jadi kartunya TIDAK boleh overflow:hidden
       — kalau hasil filter cuma 1–2 baris, kartu lebih pendek dari panel dan tombol
       "Terapkan" terpotong. Sudut bawah kartu dibulatkan lewat elemen <div> terakhirnya
       (tabel/pagination/keadaan kosong) supaya baris yang di-hover tidak menembus lengkungan. */
    .filter-pop-host { overflow: visible; }
    .filter-pop-host > div:last-of-type { border-radius: 0 0 14px 14px; }
</style>
