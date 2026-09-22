{{-- Style bersama halaman detail pengumuman (versi admin & mahasiswa).
     Mengikuti kosakata visual dashboard Super Admin: shell dash-box,
     kartu ber-header 13px/700, chip ikon, dan token warna global. --}}
<style>
    /* Token desain global SITKOM — disamakan dengan dashboard Super Admin
       (resources/views/components/sidebar.blade.php). Layout modul ini tidak
       mendefinisikan token tersebut, jadi harus dideklarasikan ulang di sini. */
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
        --shadow-card: 0px 1px 2px 0px rgba(228, 229, 231, 0.5);
    }

    /* Halaman ini menggambar kotak kontennya sendiri (.dash-wrap/.dash-box),
       jadi kotak bawaan .main-wrapper dari layout dimatikan. */
    .main-wrapper {
        background: transparent !important;
        border: none !important;
        box-shadow: none !important;
        margin: 0 !important;
        padding: 0 !important;
        overflow: visible !important;
    }

    /* ── Shell kotak: mengikuti dashboard Super Admin ───────────────── */
    .sitkom-content { padding: 0 !important; display: flex; flex-direction: column; flex: 1; overflow: hidden; }
    .dash-wrap { display: flex; flex-direction: column; height: calc(100vh - 60px); padding: 10px; box-sizing: border-box; font-family: 'Inter Tight', sans-serif; }
    .dash-box { display: flex; flex-direction: column; flex: 1; min-height: 0; background: #fff; border: 1px solid var(--c-border); border-radius: 12px; box-shadow: 0 1px 3px rgba(0, 0, 0, 0.06); overflow: hidden; width: 100%; box-sizing: border-box; }
    .dash-box-header { background: #fff; border-bottom: 1px solid var(--c-border); flex-shrink: 0; width: 100%; box-sizing: border-box; padding: 16px 24px; }
    .dash-box-body { flex: 1; overflow-y: auto; padding: 16px; }
    .dash-box-body::-webkit-scrollbar { width: 6px; }
    .dash-box-body::-webkit-scrollbar-thumb { background: var(--c-border-strong); border-radius: 10px; }
    @media (max-width: 767px) {
        .sitkom-content { padding: 8px 8px 80px !important; display: block !important; overflow: visible !important; }
        .dash-wrap { height: auto !important; min-height: 0 !important; padding: 0; }
        .dash-box { flex: none !important; min-height: 0 !important; overflow: visible !important; border-radius: 10px; }
        .dash-box-header { padding: 12px 14px; position: sticky; top: 52px; z-index: 10; }
        .dash-box-body { overflow-y: visible !important; flex: none !important; padding: 12px; }
    }

    /* ── Layout: isi utama + sidebar informasi ────────────────────────
       Sidebar mengisi sisi kanan yang sebelumnya kosong, dan menumpuk
       di bawah isi utama saat layar menyempit. */
    .dt-layout { display: grid; grid-template-columns: minmax(0, 1fr) 300px; gap: 10px; align-items: start; }
    @media (max-width: 1100px) { .dt-layout { grid-template-columns: 1fr; } }

    /* ── Kartu: pola chart-card / table-card Super Admin ─────────────── */
    .dt-card {
        background: #fff; border: 1px solid var(--c-border);
        border-radius: 14px; box-shadow: var(--shadow-card); overflow: hidden;
    }
    .dt-card + .dt-card { margin-top: 10px; }
    .dt-card-head {
        display: flex; align-items: center; gap: 8px; flex-wrap: wrap;
        padding: 12px 16px; border-bottom: 1px solid var(--c-border);
    }
    .dt-card-title { font-size: 13px; font-weight: 700; color: var(--c-fg); }
    .dt-card-count {
        font-size: 11px; font-weight: 600; color: var(--c-fg-muted);
        background: var(--c-bg); border: 1px solid var(--c-border);
        border-radius: 9999px; padding: 1px 8px; margin-left: auto;
    }
    .dt-card-body { padding: 16px; }

    /* ── Judul artikel ──────────────────────────────────────────────── */
    .dt-title {
        font-size: 22px; font-weight: 700; color: var(--c-fg);
        margin: 0; line-height: 1.3; letter-spacing: -0.02em;
        word-break: break-word; overflow-wrap: break-word;
    }
    .pin-badges { display: flex; gap: 6px; flex-wrap: wrap; margin-bottom: 8px; }
    .pin-status-badge {
        display: inline-flex; align-items: center; gap: 5px;
        padding: 4px 9px; border-radius: 8px;
        font-size: 11px; font-weight: 700; letter-spacing: .02em;
    }
    .pin-status-global   { background: var(--c-warning-subtle); color: var(--c-warning); }
    .pin-status-personal { background: var(--c-primary-subtle); color: var(--c-primary); }

    /* ── Galeri gambar ────────────────────────────────────────────────
       Bisa digeser lewat panah, drag/swipe, keyboard, atau klik thumbnail.
       Latar viewport dibiarkan putih (senada kartu) supaya gambar yang
       rasionya tidak pas tidak memunculkan kotak kosong di sisinya.
       Rasio asli dipertahankan — gambar tidak pernah terpotong. */
    .dt-gallery { margin-top: 14px; }
    .dt-gallery-viewport { position: relative; overflow: hidden; }
    .dt-gallery-track { display: flex; transition: transform .3s ease; }
    .dt-gallery-slide {
        flex: 0 0 100%; display: flex; align-items: center; justify-content: center;
        padding: 0 2px; box-sizing: border-box;
    }
    .dt-gallery-slide img {
        width: auto; max-width: 100%; max-height: 420px; display: block;
        border: 1px solid var(--c-border); border-radius: 10px;
        cursor: zoom-in; user-select: none; -webkit-user-drag: none;
        transition: border-color .15s;
    }
    .dt-gallery-slide img:hover { border-color: var(--c-primary-border); }

    .dt-gallery-nav {
        position: absolute; top: 50%; transform: translateY(-50%);
        width: 32px; height: 32px; border-radius: 50%; z-index: 2;
        background: rgba(255,255,255,.92); border: 1px solid var(--c-border);
        color: var(--c-fg-sec); cursor: pointer;
        display: flex; align-items: center; justify-content: center;
        box-shadow: 0 2px 8px rgba(0,0,0,.08);
        transition: all .15s;
    }
    .dt-gallery-nav:hover:not([disabled]) {
        background: #fff; border-color: var(--c-primary-border); color: var(--c-primary);
    }
    .dt-gallery-nav[disabled] { opacity: .3; cursor: default; }
    .dt-gallery-nav.prev { left: 8px; }
    .dt-gallery-nav.next { right: 8px; }

    .dt-gallery-counter {
        position: absolute; right: 10px; bottom: 10px; z-index: 2;
        padding: 3px 9px; border-radius: 9999px;
        background: rgba(13,13,18,.6); color: #fff;
        font-size: 11px; font-weight: 600; font-variant-numeric: tabular-nums;
    }

    .dt-gallery-thumbs {
        display: flex; gap: 8px; justify-content: center; flex-wrap: wrap; margin-top: 10px;
    }
    .dt-gallery-thumb {
        width: 52px; height: 52px; border-radius: 8px; overflow: hidden; padding: 0;
        border: 1px solid var(--c-border); background: var(--c-bg);
        cursor: pointer; transition: all .15s;
    }
    .dt-gallery-thumb img { width: 100%; height: 100%; object-fit: cover; display: block; }
    .dt-gallery-thumb:hover { border-color: var(--c-primary-border); }
    .dt-gallery-thumb.active {
        border-color: var(--c-primary);
        box-shadow: 0 0 0 2px var(--c-primary-subtle);
    }

    /* ── Isi pengumuman ─────────────────────────────────────────────── */
    .content-section {
        font-size: 14px; color: var(--c-fg-sec); line-height: 1.8;
        margin-top: 14px; word-wrap: break-word; overflow-wrap: break-word;
    }
    .content-section > :first-child { margin-top: 0; }
    .content-section > :last-child { margin-bottom: 0; }
    .content-section p { margin-bottom: 1em; }
    .content-section img { max-width: 100%; height: auto; display: block; border-radius: 10px; margin: 12px 0; }
    .content-section a { color: var(--c-primary); text-decoration: underline; text-underline-offset: 3px; }
    .content-section a:hover { color: var(--c-primary-hover); }
    .content-section h1 { font-size: 18px; font-weight: 700; color: var(--c-fg); margin: 16px 0 6px; }
    .content-section h2 { font-size: 15px; font-weight: 700; color: var(--c-fg); margin: 14px 0 6px; }
    .content-section ul, .content-section ol { padding-left: 22px; margin: 8px 0; }
    .content-section li { margin-bottom: 3px; }
    .content-section hr { border: none; border-top: 1px solid var(--c-border); margin: 14px 0; }
    .content-section table { width: 100%; border-collapse: collapse; margin: 12px 0; font-size: 12px; }
    .content-section table td,
    .content-section table th { border: 1px solid var(--c-border); padding: 8px 12px; text-align: left; }
    .content-section table th { background: #FBFBFC; font-weight: 600; color: var(--c-fg); }

    /* ── Sidebar informasi: pola chip ikon + label + nilai (_stats) ─── */
    .dt-info-row {
        display: flex; align-items: center; gap: 10px;
        padding: 9px 0; border-bottom: 1px solid var(--c-border);
    }
    .dt-info-row:first-child { padding-top: 0; }
    .dt-info-row:last-child { border-bottom: none; padding-bottom: 0; }
    .dt-info-icon {
        width: 26px; height: 26px; border-radius: 7px; flex-shrink: 0;
        background: var(--c-primary-subtle); color: var(--c-primary);
        display: flex; align-items: center; justify-content: center;
    }
    .dt-info-label { font-size: 11px; color: var(--c-fg-muted); margin: 0; line-height: 1.2; }
    .dt-info-value {
        font-size: 12px; font-weight: 600; color: var(--c-fg);
        margin: 1px 0 0; line-height: 1.3; word-break: break-word;
    }

    /* ── Lampiran ───────────────────────────────────────────────────── */
    .lampiran-list { display: grid; grid-template-columns: repeat(auto-fill, minmax(240px, 1fr)); gap: 10px; }
    .lampiran-item {
        display: flex; align-items: center; gap: 10px;
        padding: 10px 12px; background: #fff;
        border: 1px solid var(--c-border); border-radius: 10px;
        text-decoration: none; color: var(--c-fg);
        transition: border-color .15s, box-shadow .15s;
    }
    .lampiran-item:hover {
        border-color: var(--c-primary-border);
        box-shadow: 0 4px 14px rgba(11, 38, 110, 0.07);
        color: var(--c-fg);
    }
    .lampiran-icon {
        width: 30px; height: 30px; border-radius: 8px; flex-shrink: 0;
        background: var(--c-primary-subtle); color: var(--c-primary);
        display: flex; align-items: center; justify-content: center;
    }
    .lampiran-info { flex-grow: 1; overflow: hidden; }
    .lampiran-name {
        font-weight: 600; font-size: 12px; color: var(--c-fg);
        white-space: nowrap; overflow: hidden; text-overflow: ellipsis;
    }
    .lampiran-action {
        color: var(--c-fg-muted); font-size: 11px; font-weight: 500;
        display: flex; align-items: center; gap: 4px; margin-top: 1px;
        transition: color .15s;
    }
    .lampiran-item:hover .lampiran-action { color: var(--c-primary); }

    /* ── Tombol aksi ────────────────────────────────────────────────── */
    .btn-action {
        display: inline-flex; align-items: center; justify-content: center; gap: 6px;
        padding: 8px 14px; border-radius: 8px;
        border: 1px solid var(--c-border); background: #fff;
        font-size: 12px; font-weight: 600; color: var(--c-fg-sec);
        text-decoration: none; cursor: pointer;
        transition: all .15s; font-family: inherit;
        box-shadow: 0 1px 2px rgba(0,0,0,.04);
    }
    .btn-action:hover {
        background: var(--c-bg); border-color: var(--c-border-strong); color: var(--c-fg-sec);
    }
    .btn-pin-personal { color: var(--c-primary); }
    .btn-pin-personal:hover {
        background: var(--c-primary-subtle); border-color: var(--c-primary-border); color: var(--c-primary);
    }
    .btn-pin-global { color: var(--c-warning); }
    .btn-pin-global:hover {
        background: var(--c-warning-subtle); border-color: var(--c-warning); color: var(--c-warning);
    }

    /* Tombol di sidebar melebar penuh supaya rapi saat bertumpuk */
    .dt-actions { display: flex; flex-direction: column; gap: 8px; }
    .dt-actions form { margin: 0; }
    .dt-actions .btn-action { width: 100%; }

    /* ── Lightbox ───────────────────────────────────────────────────── */
    .lightbox-modal {
        display: none; position: fixed; inset: 0; z-index: 10000;
        background: rgba(13, 13, 18, 0.92);
        align-items: center; justify-content: center;
        animation: lightboxFadeIn .25s ease;
    }
    .lightbox-modal.active { display: flex; }
    @keyframes lightboxFadeIn { from { opacity: 0; } to { opacity: 1; } }
    .lightbox-content {
        position: relative; max-width: 90vw; max-height: 85vh;
        display: flex; align-items: center; justify-content: center;
    }
    .lightbox-content img {
        max-width: 90vw; max-height: 82vh; object-fit: contain;
        border-radius: 8px; box-shadow: 0 25px 60px rgba(0,0,0,.4);
        animation: lightboxZoomIn .3s ease;
    }
    @keyframes lightboxZoomIn {
        from { transform: scale(.95); opacity: 0; }
        to   { transform: scale(1); opacity: 1; }
    }
    .lightbox-close {
        position: fixed; top: 20px; right: 24px; width: 40px; height: 40px;
        border-radius: 50%;
        background: rgba(255,255,255,.12); backdrop-filter: blur(8px);
        border: 1px solid rgba(255,255,255,.2);
        color: #fff; cursor: pointer;
        display: flex; align-items: center; justify-content: center;
        transition: background .15s; z-index: 10001;
    }
    .lightbox-close:hover { background: rgba(255,255,255,.22); }
    .lightbox-info {
        position: fixed; bottom: 24px; left: 50%; transform: translateX(-50%);
        text-align: center; z-index: 10001;
    }
    .lightbox-info .lightbox-title { color: #fff; font-size: 13px; font-weight: 600; }

    @media (max-width: 640px) {
        .dt-title { font-size: 18px; }
    }
</style>
