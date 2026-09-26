{{--
    Tema bab Manajemen Kegiatan (Rencana Proker, Pelaksanaan, Laporan & Arsip).
    Token warna, badge status, form, dan modal diambil dari partials/sitkom-ui
    (satu sumber untuk tiga bab); di sini hanya token permukaan khusus kartu
    kegiatan dan baris pencarian.
--}}
@include('manajemenmahasiswa::partials.sitkom-ui')
<style>
    :root {
        --c-surface: #FFFFFF;
        --c-surface-subtle: #F8F9FB;
        --c-surface-muted: #ECEFF3;
        --c-success-border: rgba(40, 127, 110, 0.25);
    }

    /* Baris pencarian + tombol Filter setinggi 34px, sama dengan tabel User Management.
       Pencarian + tombol Filter (panel partials/filter-popover) selalu sebaris, juga di
       layar sempit: tombolnya cuma satu, dan di ujung kanan panelnya tidak menjulur
       keluar layar di sisi kiri. */
    .mk-kegiatan-filter-row {
        display: flex;
        flex-direction: row;
        align-items: center;
        justify-content: space-between;
        gap: 8px;
        margin-bottom: 20px;
    }

    .mk-kegiatan-filter-controls {
        display: flex;
        flex: 0 0 auto;
        flex-wrap: wrap;
        align-items: center;
        gap: 8px;
    }

    .mk-kegiatan-search {
        position: relative;
        flex: 1 1 auto;
        min-width: 0;
    }

    .mk-kegiatan-search__icon {
        position: absolute;
        top: 50%;
        left: 12px;
        color: var(--c-fg-placeholder);
        pointer-events: none;
        transform: translateY(-50%);
    }

    .mk-kegiatan-search__input {
        width: 100%;
        height: 34px;
        min-height: 34px;
        padding: 0 12px 0 34px;
        border: 1px solid var(--c-border);
        border-radius: 8px;
        background-color: var(--c-surface);
        color: var(--c-fg);
        font: inherit;
        font-size: 12.5px;
        font-weight: 500;
        line-height: 1.2;
        transition: border-color 150ms ease, box-shadow 150ms ease;
    }

    .mk-kegiatan-search__input:focus,
    .mk-kegiatan-search__input:focus-visible {
        border-color: var(--c-primary);
        box-shadow: 0 0 0 3px var(--c-primary-shadow);
        outline: none;
    }

</style>
