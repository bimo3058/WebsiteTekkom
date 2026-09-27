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
       Bentuk & warnanya (garis + titik gaya SITKOM) ada di partials/sitkom-ui,
       yang dimuat semua halaman Alumni. belum_bekerja berlabel "Belum Terdata"
       (Alumni::STATUS_LABELS), jadi ikut abu seperti belum_terdata. */

    /* ── Tingkat prestasi ──
       Skema sama dengan partials/sitkom-ui (Verifikasi Data & Direktori
       Mahasiswa): tiap tingkat satu warna dari palet badge Role SITKOM (ungu,
       biru, langit, merah muda, abu), tanpa hijau/kuning/merah yang sudah
       dipakai warna status. */
    .tingkat-badge               { border: 1px solid var(--c-border); }
    .tingkat-badge.internasional { background: #EDE9FE;             color: #5B21B6;         border-color: #C4B5FD; }
    .tingkat-badge.nasional      { background: #EFF6FF;             color: #1D4ED8;         border-color: #BFDBFE; }
    .tingkat-badge.regional      { background: var(--c-sky-subtle); color: var(--c-sky);    border-color: #BAE6FD; }
    .tingkat-badge.universitas   { background: #FCE7F3;             color: #9D174D;         border-color: #FBCFE8; }
    .tingkat-badge.prodi         { background: var(--c-grey-50);    color: var(--c-fg-sec); border-color: var(--c-border); }
</style>
