<style>
    /* Token desain global SITKOM — disamakan dengan halaman User Management
       Super Admin (resources/views/components/sidebar.blade.php). Layout modul
       ini tidak mendefinisikan token tersebut, jadi dideklarasikan ulang. */
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
        --c-input-border: #D0D5DD;
        --c-success: #287F6E;
        --c-success-subtle: #DDF2EE;
        --c-error: #DF1C41;
        --c-error-subtle: #FADAE1;
        --c-warning: #956321;
        --c-warning-subtle: #F9ECCB;
        --shadow-card: 0px 1px 2px 0px rgba(228, 229, 231, 0.5);
    }

    .main-wrapper { background:transparent !important; box-shadow:none !important; padding:0 !important; }

    /* ── Shell kotak: mengikuti halaman User Management Super Admin ──── */
    .sitkom-content { padding: 0 !important; display: flex; flex-direction: column; flex: 1; overflow: hidden; }
    .user-wrap { display: flex; flex-direction: column; height: calc(100vh - 60px); padding: 10px; box-sizing: border-box; font-family: 'Inter Tight', sans-serif; }
    .user-box { display: flex; flex-direction: column; flex: 1; min-height: 0; background: #fff; border: 1px solid var(--c-border); border-radius: 12px; box-shadow: var(--shadow-card); overflow: hidden; width: 100%; box-sizing: border-box; }
    .user-box-header { background: #fff; border-bottom: 1px solid var(--c-border); flex-shrink: 0; width: 100%; box-sizing: border-box; padding: 16px 24px; }
    .user-box-body { flex: 1; overflow-y: auto; padding: 20px 24px; }
    .user-box-body::-webkit-scrollbar { width: 6px; }
    .user-box-body::-webkit-scrollbar-thumb { background: var(--c-border-strong); border-radius: 10px; }
    @media (max-width: 767px) {
        .sitkom-content { padding: 8px 8px 80px !important; display: block !important; overflow: visible !important; }
        .user-wrap { height: auto !important; min-height: 0 !important; padding: 0; }
        .user-box { flex: none !important; min-height: 0 !important; overflow: visible !important; border-radius: 10px; }
        .user-box-header { padding: 12px 14px; position: sticky; top: 52px; z-index: 10; }
        .user-box-body { overflow-y: visible !important; flex: none !important; padding: 12px 14px; }
    }

    /* ── Tombol ─────────────────────────────────────────────────────── */
    .mp-btn-outline {
        display: inline-flex; align-items: center; gap: 6px;
        padding: 8px 14px; border-radius: 8px;
        background: #fff; border: 1px solid var(--c-border);
        font-size: 12px; font-weight: 600; color: var(--c-fg-sec);
        font-family: inherit; cursor: pointer; text-decoration: none;
        transition: all .15s; box-shadow: 0 1px 2px rgba(0,0,0,.04);
    }
    .mp-btn-outline:hover {
        background: var(--c-bg); border-color: var(--c-border-strong); color: var(--c-fg-sec);
    }
    .mp-btn-outline.danger { color: var(--c-error); }
    .mp-btn-outline.danger:hover { background: var(--c-error-subtle); border-color: var(--c-error); color: var(--c-error); }

    .mp-btn-primary {
        display: inline-flex; align-items: center; gap: 6px;
        padding: 8px 16px; border-radius: 8px;
        background: var(--c-primary); border: 1px solid var(--c-primary);
        font-size: 12px; font-weight: 600; color: #fff;
        font-family: inherit; cursor: pointer; text-decoration: none;
        transition: all .15s; box-shadow: 0 2px 6px rgba(11,38,110,.3);
    }
    .mp-btn-primary:hover { background: var(--c-primary-hover); color: #fff; box-shadow: 0 4px 12px rgba(11,38,110,.4); }
    .mp-btn-primary:disabled {
        background: var(--c-bg); border-color: var(--c-border);
        color: var(--c-fg-placeholder); cursor: not-allowed; box-shadow: none;
    }
    .mp-btn-danger {
        display: inline-flex; align-items: center; gap: 6px;
        padding: 8px 16px; border-radius: 8px;
        background: var(--c-error); border: 1px solid var(--c-error);
        font-size: 12px; font-weight: 600; color: #fff;
        font-family: inherit; cursor: pointer;
        transition: all .15s; box-shadow: 0 2px 6px rgba(223,28,65,.25);
    }
    .mp-btn-danger:hover { background: #C21433; border-color: #C21433; }

    /* ── Filter bar: pola _search_filter Super Admin ─────────────────── */
    .mp-filter-bar {
        background: #fff; border: 1px solid var(--c-border);
        border-radius: 12px; padding: 12px 16px; margin-bottom: 10px;
        position: relative; z-index: 40;
    }
    .mp-filter-row { display: flex; flex-wrap: wrap; align-items: flex-end; gap: 8px; }
    .mp-field { position: relative; }
    .mp-field-grow { flex: 1; min-width: 180px; }
    .mp-label {
        display: block; margin-bottom: 6px;
        font-size: 10px; font-weight: 700; text-transform: uppercase;
        letter-spacing: .08em; color: var(--c-fg-muted);
    }
    .mp-input {
        width: 100%; height: 32px; box-sizing: border-box;
        padding: 0 12px 0 30px; border-radius: 8px;
        background: #fff; border: 1px solid var(--c-input-border);
        font-size: 12px; color: var(--c-fg); font-family: inherit;
        outline: none; transition: border-color .15s, box-shadow .15s;
    }
    .mp-input::placeholder { color: var(--c-fg-placeholder); }
    .mp-input:focus { border-color: var(--c-primary); box-shadow: 0 0 0 3px var(--c-primary-subtle); }
    .mp-input-icon {
        position: absolute; left: 10px; top: 50%; transform: translateY(-50%);
        color: var(--c-fg-placeholder); pointer-events: none;
    }

    .mp-select-btn {
        width: 100%; height: 32px; box-sizing: border-box;
        display: flex; align-items: center; justify-content: space-between; gap: 8px;
        padding: 0 12px; border-radius: 8px;
        background: #fff; border: 1px solid var(--c-input-border);
        font-size: 12px; font-weight: 500; color: var(--c-fg);
        font-family: inherit; cursor: pointer; transition: border-color .15s, box-shadow .15s;
    }
    .mp-select-btn:hover { border-color: var(--c-border-strong); }
    .mp-select-btn.open { border-color: var(--c-primary); box-shadow: 0 0 0 3px var(--c-primary-subtle); }
    .mp-select-btn svg { flex-shrink: 0; color: var(--c-fg-placeholder); transition: transform .2s; }
    .mp-select-btn.open svg { transform: rotate(180deg); }
    .mp-select-menu {
        position: absolute; left: 0; top: calc(100% + 4px); width: 100%;
        background: #fff; border: 1px solid var(--c-border); border-radius: 8px;
        box-shadow: 0 10px 25px rgba(0,0,0,.1);
        padding: 4px 0; overflow: hidden; z-index: 100;
        max-height: 200px; overflow-y: auto;
    }
    .mp-select-option {
        width: 100%; text-align: left; padding: 6px 12px;
        font-size: 12px; color: var(--c-fg-sec); font-family: inherit;
        background: none; border: none; cursor: pointer; transition: background .12s;
    }
    .mp-select-option:hover { background: var(--c-bg); }
    .mp-select-option.selected { color: var(--c-primary); font-weight: 600; background: rgba(11,38,110,.04); }

    /* ── Flash messages ─────────────────────────────────────────────── */
    .mp-flash {
        border-radius: 10px; padding: 12px 16px; margin-bottom: 10px;
        display: flex; align-items: center; gap: 10px;
        font-size: 12px; font-weight: 500;
    }
    .mp-flash.success { background: var(--c-success-subtle); color: var(--c-success); border: 1px solid var(--c-success); }
    .mp-flash.error   { background: var(--c-error-subtle);   color: var(--c-error);   border: 1px solid var(--c-error); }

    /* ── Empty state ────────────────────────────────────────────────── */
    .mp-empty {
        background: #fff; border: 1px dashed var(--c-border-strong);
        border-radius: 12px; padding: 40px; text-align: center;
    }
    .mp-empty p { color: var(--c-fg-muted); font-size: 12px; font-weight: 600; margin: 0; }

    /* ── Tabel: pola _table.blade.php User Management Super Admin ───── */
    .mp-table-card {
        background: #fff; border: 1px solid var(--c-border);
        border-radius: 12px; overflow: hidden;
        box-shadow: var(--shadow-card);
    }
    .mp-table-wrap { overflow-x: auto; }
    .mp-table { width: 100%; border-collapse: collapse; min-width: 820px; }

    .mp-table thead tr { border-bottom: 1px solid var(--c-border); background: #FAFAFA; }
    .mp-th {
        padding: 11px 16px; text-align: left;
        font-size: 11px; font-weight: 600; color: var(--c-fg-muted);
        white-space: nowrap;
    }
    .mp-th-no { width: 48px; padding-left: 16px; padding-right: 12px; }
    .mp-th-action { text-align: right; width: 130px; }

    .mp-row { border-bottom: 1px solid #F3F4F6; transition: background .12s; }
    .mp-row:hover { background: #FAFAFA; }
    .mp-td { padding: 12px 16px; font-size: 13px; color: var(--c-fg); vertical-align: middle; }
    .mp-td-no { width: 48px; padding-right: 12px; color: var(--c-fg-muted); }
    .mp-td-mono { color: var(--c-fg-sec); font-variant-numeric: tabular-nums; white-space: nowrap; }
    .mp-td-action { text-align: right; white-space: nowrap; }

    .mp-user-cell { display: flex; align-items: center; gap: 10px; min-width: 0; }
    .mp-avatar {
        width: 34px; height: 34px; border-radius: 50%;
        display: flex; align-items: center; justify-content: center;
        flex-shrink: 0; overflow: hidden;
    }
    .mp-avatar img { width: 100%; height: 100%; object-fit: cover; }
    .mp-avatar span { font-size: 12px; font-weight: 700; }
    .mp-user-name {
        font-size: 13px; font-weight: 600; color: var(--c-fg); margin: 0;
        white-space: nowrap; overflow: hidden; text-overflow: ellipsis; max-width: 220px;
    }
    .mp-user-email {
        font-size: 11px; color: var(--c-fg-muted); margin: 1px 0 0;
        white-space: nowrap; overflow: hidden; text-overflow: ellipsis; max-width: 220px;
    }

    .mp-badges { display: flex; flex-wrap: wrap; gap: 4px; align-items: center; }
    .mp-role-badge {
        padding: 3px 9px; border-radius: 8px;
        font-size: 11px; font-weight: 700; letter-spacing: .02em; white-space: nowrap;
    }

    .mp-row-action {
        display: inline-flex; align-items: center; gap: 6px;
        padding: 6px 11px; border-radius: 6px;
        background: #fff; border: 1px solid var(--c-border);
        font-size: 11.5px; font-weight: 600; color: var(--c-fg-sec);
        font-family: inherit; cursor: pointer; transition: all .15s;
    }
    .mp-row-action:hover { background: var(--c-bg); border-color: var(--c-border-strong); }
    .mp-row-action svg { transition: transform .3s; }
    .mp-locked { display: inline-flex; color: var(--c-border-strong); }

    .mp-empty-cell { padding: 44px 24px; text-align: center; color: var(--c-border-strong); }
    .mp-empty-title {
        font-size: 12px; font-weight: 600; color: var(--c-fg-muted);
        text-transform: uppercase; letter-spacing: .06em; margin: 8px 0 2px;
    }
    .mp-empty-sub { font-size: 11px; color: var(--c-fg-placeholder); margin: 0; }

    /* ── Baris form ubah role ───────────────────────────────────────── */
    .mp-edit-row { background: var(--c-bg); }
    .mp-edit-cell {
        padding: 16px; border-bottom: 1px solid var(--c-border);
        background: var(--c-bg);
    }
    .uc-section-header { display: flex; align-items: center; gap: 8px; margin-bottom:8px; }
    .uc-section-header::before {
        content: ''; display: inline-block; width: 3px; height: 14px;
        border-radius: 2px; background: var(--c-primary);
    }
    .uc-section-label { font-size: 13px; font-weight: 700; color: var(--c-fg); }
    .uc-hint { font-size: 11.5px; color: var(--c-fg-muted); margin: 0 0 12px; line-height: 1.5; }
    .uc-hint strong { color: var(--c-fg-sec); }

    .uc-role-options { display: flex; flex-wrap: wrap; gap: 8px; margin-bottom: 10px; }
    .uc-role-options label { cursor: pointer; }
    .mk-role-pill {
        display: flex; align-items: center; gap: 8px;
        padding: 6px 12px; border-radius: 8px;
        border: 1px solid var(--c-border); background: #fff;
        transition: background .15s, border-color .15s, color .15s;
    }
    .mk-role-pill span { font-size: 11.5px; font-weight: 600; letter-spacing: .01em; }
    .mk-dot { width: 7px; height: 7px; border-radius: 50%; flex-shrink: 0; transition: background .15s; }

    .uc-actions {
        display: flex; justify-content: flex-end; gap: 8px;
        padding-top: 12px; border-top: 1px solid var(--c-border);
    }

    /* ── Modal ──────────────────────────────────────────────────────── */
    .mp-modal {
        display: none; position: fixed; inset: 0; z-index: 9999;
        background: rgba(13,13,18,.45);
        align-items: center; justify-content: center; padding: 20px;
    }
    .mp-modal.open { display: flex; }
    .mp-modal-box {
        background: #fff; border-radius: 14px; width: 100%;
        box-shadow: 0 24px 60px rgba(0,0,0,.18);
        display: flex; flex-direction: column; max-height: 88vh;
    }
    .mp-modal-head {
        display: flex; align-items: flex-start; gap: 12px;
        padding: 16px 18px; border-bottom: 1px solid var(--c-border);
    }
    .mp-modal-icon {
        width: 36px; height: 36px; border-radius: 10px; flex-shrink: 0;
        display: flex; align-items: center; justify-content: center;
    }
    .mp-modal-head h3 { font-size: 14px; font-weight: 700; color: var(--c-fg); margin: 0; }
    .mp-modal-head p  { font-size: 11.5px; color: var(--c-fg-muted); margin: 3px 0 0; }
    .mp-modal-body { padding: 16px 18px; overflow-y: auto; }
    .mp-modal-foot {
        display: flex; gap: 8px; justify-content: flex-end;
        padding: 12px 18px; border-top: 1px solid var(--c-border);
    }
    .mp-preview-box {
        background: var(--c-bg); border: 1px solid var(--c-border);
        border-radius: 8px; padding: 12px 14px; min-height: 60px;
        display: flex; align-items: center; justify-content: center;
        font-size: 12px; color: var(--c-fg-muted);
    }
    .mp-warning-box {
        background: var(--c-error-subtle); border: 1px solid var(--c-error);
        border-radius: 8px; padding: 10px 14px; margin-bottom: 10px;
        font-size: 11.5px; color: var(--c-error); line-height: 1.5;
        display: flex; gap: 8px; align-items: flex-start;
    }
    .mp-warning-box svg { flex-shrink: 0; margin-top: 1px; }

    /* ── Pagination ─────────────────────────────────────────────────── */
    .page-btn {
        display: inline-flex; align-items: center; justify-content: center;
        min-width: 32px; height: 32px; padding: 0 10px;
        border-radius: 8px; font-size: 12px; font-weight: 600;
        color: var(--c-fg-sec); background: #fff;
        border: 1px solid var(--c-border);
        text-decoration: none !important; transition: all .15s; cursor: pointer;
    }
    .page-btn:hover:not(.disabled):not(.page-btn-active) {
        background: var(--c-primary-subtle); border-color: var(--c-primary-border); color: var(--c-primary);
    }
    .page-btn-active {
        background: var(--c-primary); border-color: var(--c-primary);
        color: #fff !important; cursor: default;
    }
    .page-btn-nav { color: var(--c-fg-muted); }
    .page-btn-nav.disabled { opacity: .35; cursor: not-allowed; }
    .page-btn-dots { border: none; background: transparent; color: var(--c-fg-placeholder); cursor: default; min-width: 24px; padding: 0; }
    .mp-page-info { font-size: 12px; color: var(--c-fg-muted); font-weight: 500; }
</style>
