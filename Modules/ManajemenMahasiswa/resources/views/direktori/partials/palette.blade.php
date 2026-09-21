{{-- Palet warna bab Direktori Mahasiswa & Alumni.
     Token disamakan 1:1 dengan palet global SITKOM (blok :root di
     resources/views/components/sidebar.blade.php). Layout modul ini tidak memuat
     token tersebut, jadi dideklarasikan ulang — pola yang sama dengan
     dashboard-analitik dan permissions/_styles. Token skala tambahan (--c-grey-*,
     --c-error-0/200) diambil dari resources/css/app.css dengan nama yang sama.

     Pemetaan warna status sengaja dikumpulkan di sini supaya halaman index, detail,
     dan edit tidak bisa lagi memakai warna berbeda untuk status yang sama. --}}
<style>
    :root {
        --c-primary: #0B266E;
        --c-primary-hover: #091958;
        --c-primary-subtle: rgba(11, 38, 110, 0.08);
        --c-primary-border: #5C78B8;
        --c-bg: #F6F8FA;
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

        --c-grey-0: #F8F9FB;
        --c-grey-50: #ECEFF3;
        --c-error-0: #FEEFF2;
        /* Teks merah di atas latar merah muda: #DF1C41 terlalu terang untuk huruf
           kecil, jadi dipakai tingkat lebih gelap — sama seperti pill SIPERKOM global. */
        --c-error-200: #95122B;
    }

    /* Tombol kembali standar untuk seluruh halaman Direktori. */
    .detail-back {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: auto;
        min-width: 0;
        height: 32px;
        padding: 0 12px;
        color: var(--c-fg-sec);
        background: #fff;
        border: 1px solid var(--c-border);
        border-radius: 8px;
        box-shadow: 0 1px 2px rgba(0, 0, 0, .05);
        text-decoration: none;
        transition: all .2s;
    }
    .detail-back:hover {
        background: var(--c-bg);
        color: var(--c-fg);
    }
    .detail-back-label {
        color: inherit;
        font-size: 13px;
        font-weight: 600;
        line-height: 1.2;
    }

    /* ── Nada ikon kartu statistik (SVG memakai stroke="currentColor") ── */
    .tone-primary { background: var(--c-primary-subtle); color: var(--c-primary); }
    .tone-success { background: var(--c-success-subtle); color: var(--c-success); }
    .tone-warning { background: var(--c-warning-subtle); color: var(--c-warning); }
    .tone-sky     { background: var(--c-sky-subtle);     color: var(--c-sky); }
    .tone-error   { background: var(--c-error-subtle);   color: var(--c-error); }
    .tone-neutral { background: var(--c-grey-50);        color: var(--c-fg-sec); }
    /* Versi bergaris tanpa latar, meniru badge "Offline/Nonaktif" global
       (components/ui/status-badge). Garis dibuat dengan inset box-shadow, bukan
       border, supaya ukurannya persis sama dengan badge/ikon terisi lainnya. */
    .tone-outline { background: #ffffff; color: var(--c-fg-muted); box-shadow: inset 0 0 0 1px var(--c-border); }

    /* ── Status mahasiswa ── */
    .status-badge.aktif        { background: var(--c-success-subtle); color: var(--c-success); }
    .status-badge.cuti         { background: var(--c-sky-subtle);     color: var(--c-sky); }
    .status-badge.mangkir      { background: var(--c-warning-subtle); color: var(--c-warning); }
    .status-badge.drop_out     { background: var(--c-error-subtle);   color: var(--c-error-200); }
    .status-badge.pindah_studi { background: var(--c-primary-subtle); color: var(--c-primary); }
    /* Wafat sengaja bergaris, bukan terisi: latar abu terisi nyaris sama dengan
       navy muda Pindah Studi (#ECEFF3 vs ≈#EBEEF3) sehingga keduanya tak terbedakan. */
    .status-badge.wafat        { background: #ffffff; color: var(--c-fg-muted); box-shadow: inset 0 0 0 1px var(--c-border); }
    .status-badge.alumni       { background: var(--c-sky-subtle);     color: var(--c-sky); }

    /* ── Status karir alumni ──
       belum_bekerja berlabel "Belum Terdata" (Alumni::STATUS_LABELS), jadi warnanya
       disamakan dengan belum_terdata (status kosong) dan kartu statistiknya. */
    .status-badge.bekerja,       .status-badge-lg.bekerja       { background: var(--c-success-subtle); color: var(--c-success); }
    .status-badge.wirausaha,     .status-badge-lg.wirausaha     { background: var(--c-warning-subtle); color: var(--c-warning); }
    .status-badge.studi_lanjut,  .status-badge-lg.studi_lanjut  { background: var(--c-sky-subtle);     color: var(--c-sky); }
    .status-badge.belum_bekerja, .status-badge-lg.belum_bekerja,
    .status-badge.belum_terdata, .status-badge-lg.belum_terdata { background: var(--c-grey-50);        color: var(--c-fg-sec); }

    /* ── Tingkat prestasi ── */
    .tingkat-badge.internasional { background: var(--c-warning-subtle); color: var(--c-warning); }
    .tingkat-badge.nasional      { background: var(--c-sky-subtle);     color: var(--c-sky); }
    .tingkat-badge.regional      { background: var(--c-error-subtle);   color: var(--c-error-200); }
    .tingkat-badge.universitas   { background: var(--c-success-subtle); color: var(--c-success); }
    .tingkat-badge.prodi         { background: var(--c-primary-subtle); color: var(--c-primary); }
</style>
