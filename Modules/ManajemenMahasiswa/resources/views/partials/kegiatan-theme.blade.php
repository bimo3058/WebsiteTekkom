{{--
    Palette khusus Manajemen Kegiatan.
    Nilai mengikuti token aktif pada shell global SITKOM
    (resources/views/components/sidebar.blade.php), tanpa memengaruhi halaman
    SIMENMA lain yang memakai layout mahasiswa yang sama.
--}}
<style>
    :root {
        --c-primary: #0B266E;
        --c-primary-hover: #091958;
        --c-primary-subtle: rgba(11, 38, 110, 0.08);
        --c-primary-border: #5C78B8;
        --c-primary-shadow: rgba(11, 38, 110, 0.12);
        --c-primary-shadow-strong: rgba(11, 38, 110, 0.30);

        --c-bg: #F6F8FA;
        --c-surface: #FFFFFF;
        --c-surface-subtle: #F8F9FB;
        --c-surface-muted: #ECEFF3;
        --c-fg: #0D0D12;
        --c-fg-sec: #353849;
        --c-fg-muted: #666D80;
        --c-fg-placeholder: #808897;
        --c-border: #DFE1E7;
        --c-border-strong: #C1C7CF;

        --c-success: #287F6E;
        --c-success-subtle: #DDF2EE;
        --c-success-border: rgba(40, 127, 110, 0.25);
        --c-warning: #956321;
        --c-warning-subtle: #F9ECCB;
        --c-error: #DC2626;
        --c-error-hover: #B91C1C;
        --c-error-subtle: #FEE2E2;
        --c-sky: #0C4D6E;
        --c-sky-subtle: #D1F0F9;

        --shadow-card: 0px 1px 2px 0px rgba(228, 229, 231, 0.5);
    }

    /* Shared 38px search and filter controls. Pencarian + tombol Filter (panel
       partials/filter-popover) selalu sebaris, juga di layar sempit: tombolnya cuma
       satu, dan di ujung kanan panelnya tidak menjulur keluar layar di sisi kiri. */
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
        color: var(--c-fg-muted);
        pointer-events: none;
        transform: translateY(-50%);
    }

    .mk-kegiatan-search__input {
        width: 100%;
        height: 38px;
        min-height: 38px;
        padding: 0 12px 0 36px;
        border: 1px solid var(--c-border);
        border-radius: 8px;
        background-color: var(--c-surface);
        color: var(--c-fg-sec);
        font: inherit;
        font-size: 13px;
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
