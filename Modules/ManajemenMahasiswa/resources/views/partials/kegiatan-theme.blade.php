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

    /*
     * Shared controls for Rencana Proker, Pelaksanaan, and Laporan & Arsip.
     * Keep these namespaced so Bootstrap and other SIMENMA pages are untouched.
     */
    .mk-kegiatan-btn {
        appearance: none;
        min-height: 40px;
        padding: 0 18px;
        border: 1px solid transparent;
        border-radius: 10px;
        background: var(--c-surface);
        color: var(--c-fg-sec);
        font: inherit;
        font-size: 14px;
        font-weight: 600;
        line-height: 1.2;
        text-align: center;
        text-decoration: none !important;
        white-space: nowrap;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        cursor: pointer;
        transition: background-color 150ms ease, border-color 150ms ease, color 150ms ease,
            box-shadow 150ms ease, transform 150ms ease;
    }

    .mk-kegiatan-btn > svg {
        flex: 0 0 auto;
    }

    .mk-kegiatan-btn:focus {
        outline: none;
    }

    .mk-kegiatan-btn--primary {
        background: var(--c-primary);
        border-color: var(--c-primary);
        color: var(--c-surface);
    }

    .mk-kegiatan-btn--primary:not(:disabled):not([aria-disabled="true"]):not(.mk-kegiatan-btn--disabled):hover {
        background: var(--c-primary-hover);
        border-color: var(--c-primary-hover);
        color: var(--c-surface);
        transform: translateY(-1px);
    }

    .mk-kegiatan-btn--primary:not(:disabled):not([aria-disabled="true"]):not(.mk-kegiatan-btn--disabled):active {
        background: var(--c-primary-hover);
        border-color: var(--c-primary-hover);
        color: var(--c-surface);
        transform: translateY(0);
    }

    .mk-kegiatan-btn--primary:focus-visible {
        box-shadow: 0 0 0 3px var(--c-primary-shadow-strong);
    }

    .mk-kegiatan-btn--secondary {
        background: var(--c-surface);
        border-color: var(--c-border);
        color: var(--c-fg-sec);
    }

    .mk-kegiatan-btn--secondary:not(:disabled):not([aria-disabled="true"]):not(.mk-kegiatan-btn--disabled):hover {
        background: var(--c-primary-subtle);
        border-color: var(--c-primary);
        color: var(--c-primary);
        transform: translateY(-1px);
    }

    .mk-kegiatan-btn--secondary:not(:disabled):not([aria-disabled="true"]):not(.mk-kegiatan-btn--disabled):active {
        background: var(--c-primary-subtle);
        border-color: var(--c-primary);
        color: var(--c-primary);
        transform: translateY(0);
    }

    .mk-kegiatan-btn--secondary:focus-visible {
        border-color: var(--c-primary);
        box-shadow: 0 0 0 3px var(--c-primary-shadow);
    }

    .mk-kegiatan-btn--danger-subtle {
        background: var(--c-error-subtle);
        border-color: transparent;
        color: var(--c-error);
    }

    .mk-kegiatan-btn--danger-subtle:not(:disabled):not([aria-disabled="true"]):not(.mk-kegiatan-btn--disabled):hover {
        background: var(--c-error);
        border-color: var(--c-error);
        color: var(--c-surface);
        transform: translateY(-1px);
    }

    .mk-kegiatan-btn--danger-subtle:not(:disabled):not([aria-disabled="true"]):not(.mk-kegiatan-btn--disabled):active {
        background: var(--c-error-hover);
        border-color: var(--c-error-hover);
        color: var(--c-surface);
        transform: translateY(0);
    }

    .mk-kegiatan-btn--danger-subtle:focus-visible {
        box-shadow: 0 0 0 3px rgba(220, 38, 38, 0.24);
    }

    .mk-kegiatan-btn--danger,
    .mk-kegiatan-btn--danger-solid {
        background: var(--c-error);
        border-color: var(--c-error);
        color: var(--c-surface);
    }

    .mk-kegiatan-btn--danger:not(:disabled):not([aria-disabled="true"]):not(.mk-kegiatan-btn--disabled):hover,
    .mk-kegiatan-btn--danger-solid:not(:disabled):not([aria-disabled="true"]):not(.mk-kegiatan-btn--disabled):hover {
        background: var(--c-error-hover);
        border-color: var(--c-error-hover);
        color: var(--c-surface);
        transform: translateY(-1px);
    }

    .mk-kegiatan-btn--danger:not(:disabled):not([aria-disabled="true"]):not(.mk-kegiatan-btn--disabled):active,
    .mk-kegiatan-btn--danger-solid:not(:disabled):not([aria-disabled="true"]):not(.mk-kegiatan-btn--disabled):active {
        background: var(--c-error-hover);
        border-color: var(--c-error-hover);
        color: var(--c-surface);
        transform: translateY(0);
    }

    .mk-kegiatan-btn--danger:focus-visible,
    .mk-kegiatan-btn--danger-solid:focus-visible {
        box-shadow: 0 0 0 3px rgba(220, 38, 38, 0.24);
    }

    .mk-kegiatan-btn--download {
        background: var(--c-primary-subtle);
        border-color: rgba(11, 38, 110, 0.18);
        color: var(--c-primary);
    }

    .mk-kegiatan-btn--download:not(:disabled):not([aria-disabled="true"]):not(.mk-kegiatan-btn--disabled):hover {
        background: var(--c-primary);
        border-color: var(--c-primary);
        color: var(--c-surface);
        transform: translateY(-1px);
    }

    .mk-kegiatan-btn--download:not(:disabled):not([aria-disabled="true"]):not(.mk-kegiatan-btn--disabled):active {
        background: var(--c-primary-hover);
        border-color: var(--c-primary-hover);
        color: var(--c-surface);
        transform: translateY(0);
    }

    .mk-kegiatan-btn--download:focus-visible {
        box-shadow: 0 0 0 3px var(--c-primary-shadow);
    }

    .mk-kegiatan-btn--compact {
        min-height: 38px;
        padding: 0 14px;
        border-radius: 10px;
        font-size: 13px;
    }

    .mk-kegiatan-btn--form,
    .mk-kegiatan-btn--modal {
        min-height: 40px;
        padding: 0 18px;
        border-radius: 10px;
        font-size: 14px;
    }

    .mk-kegiatan-btn--icon,
    .mk-kegiatan-btn--icon-back {
        width: 38px;
        min-width: 38px;
        min-height: 38px;
        height: 38px;
        padding: 0;
        gap: 0;
    }

    .mk-kegiatan-btn--icon-sm {
        width: 32px;
        min-width: 32px;
        min-height: 32px;
        height: 32px;
        padding: 0;
        gap: 0;
    }

    .mk-kegiatan-btn--icon-back {
        width: 32px;
        min-width: 32px;
        min-height: 32px;
        height: 32px;
        padding: 0;
        gap: 0;
        background: var(--c-surface);
        border-color: var(--c-border);
        color: var(--c-fg-sec);
        border-radius: 8px;
        box-shadow: 0 1px 2px rgba(0, 0, 0, .05);
    }

    .mk-kegiatan-btn--icon-back:not(:disabled):not([aria-disabled="true"]):not(.mk-kegiatan-btn--disabled):hover {
        background: var(--c-bg);
        border-color: var(--c-border);
        color: var(--c-fg);
        transform: none;
    }

    .mk-kegiatan-btn--icon-back:not(:disabled):not([aria-disabled="true"]):not(.mk-kegiatan-btn--disabled):active {
        background: var(--c-bg);
        border-color: var(--c-border);
        color: var(--c-fg);
        transform: translateY(0);
    }

    .mk-kegiatan-btn--icon-back:focus-visible {
        border-color: var(--c-primary);
        box-shadow: 0 0 0 3px var(--c-primary-shadow);
    }


    .mk-kegiatan-btn--pill {
        min-height: auto;
        height: auto;
        padding: 5px 12px;
        border-radius: 20px;
        font-size: 12px;
    }

    .mk-kegiatan-btn:disabled,
    .mk-kegiatan-btn[aria-disabled="true"],
    .mk-kegiatan-btn--disabled {
        background: var(--c-surface-muted) !important;
        border-color: var(--c-border) !important;
        box-shadow: none !important;
        color: var(--c-fg-muted) !important;
        cursor: not-allowed;
        opacity: 1;
        pointer-events: none;
        transform: none !important;
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
