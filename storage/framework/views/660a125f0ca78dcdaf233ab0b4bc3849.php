<style>
    .mp-app {
        --c-primary: #0B266E; --c-primary-hover: #091958; --c-primary-subtle: #EEF1F8; --c-primary-border: #CED4E0;
        --c-fg: #0D0D12; --c-fg-sec: #353849; --c-fg-muted: #666D80; --c-fg-placeholder: #818898;
        --c-bg: #F6F8FA; --c-border: #DFE1E7; --c-border-strong: #C1C7CF;
        --shadow-card: 0 1px 3px rgba(13,13,18,.04);
    }
    .mp-app [x-cloak] { display: none !important; }
    .mp-icon { display: inline-block; vertical-align: -.15em; flex-shrink: 0; }
    .mp-btn .mp-icon { width: 15px; height: 15px; }
    .mp-badge .mp-icon { width: 12px; height: 12px; }
    .mp-app .mp-btn.error { background: #DF1C41; color: white; }
    .mp-app .mp-btn.error:hover { background: #95122B; }
    .mp-app .mp-stat-icon { border: 1px solid var(--c-primary-border); background: var(--c-primary-subtle); color: var(--c-primary); }
    .mp-app .mp-card-header { background: #FAFBFD; }
    .mp-inline-note { display: flex; align-items: flex-start; gap: 8px; padding: 12px; border: 1px solid var(--c-primary-border); border-radius: 8px; background: #F8FAFD; color: var(--c-fg-muted); font-size: 12px; line-height: 1.6; }
    .mp-inline-note > .mp-icon { color: var(--c-primary); margin-top: 2px; }
    .mp-app :is(a,button,input,select,textarea):focus-visible { outline: 2px solid var(--c-primary); outline-offset: 3px; }
    .mp-shell { display: flex; height: 100dvh; overflow: hidden; }
    .mp-main { display: flex; flex: 1; flex-direction: column; min-width: 0; min-height: 0; overflow: hidden; }
    .mp-sidebar { display: flex; flex-direction: column; flex-shrink: 0; width: 256px; min-height: 0; border-right: 1px solid var(--c-border); background: white; transition: width .25s ease, transform .25s ease; z-index: 40; }
    .mp-sidebar.is-collapsed { width: 64px; }
    .mp-sidebar-brand { display: flex; align-items: center; gap: 8px; min-height: 60px; padding: 12px 14px; border-bottom: 1px solid var(--c-border); flex-shrink: 0; }
    .mp-brand { display: flex; flex: 1; align-items: center; gap: 8px; min-width: 0; text-decoration: none; color: var(--c-fg); }
    .mp-brand > img { width: 32px; height: 32px; object-fit: contain; flex-shrink: 0; }
    .mp-brand > span { min-width: 0; }
    .mp-brand strong { display: block; font-size: 14px; font-weight: 700; line-height: 1.2; letter-spacing: -.01em; }
    .mp-brand small { display: block; font-size: 10px; color: var(--c-fg-muted); margin-top: 3px; white-space: nowrap; }
    .mp-icon-button { display: inline-flex; justify-content: center; align-items: center; width: 30px; height: 30px; flex-shrink: 0; padding: 0; border: 1px solid var(--c-border); border-radius: 7px; background: white; color: var(--c-fg-muted); cursor: pointer; }
    .mp-icon-button:hover { color: var(--c-primary); background: var(--c-bg); }
    .mp-sidebar-toggle svg { transition: transform .25s; }
    .mp-sidebar.is-collapsed .mp-sidebar-brand { flex-direction: column; padding: 12px 10px 8px; }
    .mp-sidebar.is-collapsed .mp-brand { flex: none; }
    .mp-sidebar.is-collapsed .mp-sidebar-toggle svg { transform: rotate(180deg); }
    .mp-sidebar-nav { flex: 1; min-height: 0; overflow-y: auto; overflow-x: hidden; padding: 6px 10px 12px; scrollbar-width: thin; scrollbar-color: var(--c-border) transparent; }
    .mp-nav-section + .mp-nav-section { border-top: 1px solid var(--c-border); margin-top: 10px; padding-top: 10px; }
    .mp-nav-label { padding: 12px 10px 5px; color: var(--c-fg-muted); font-size: 10px; font-weight: 600; text-transform: uppercase; letter-spacing: .06em; }
    .mp-nav-link { position: relative; display: flex; align-items: center; gap: 9px; width: 100%; min-height: 36px; margin: 1px 0; padding: 8px 10px 8px 14px; border: 0; border-radius: 8px; background: transparent; color: var(--c-fg-sec); font: 500 13px/1.4 'Inter Tight',sans-serif; text-decoration: none; text-align: left; cursor: pointer; transition: background .12s, color .12s; }
    .mp-nav-link > svg { width: 16px; height: 16px; color: var(--c-fg-muted); flex-shrink: 0; }
    .mp-nav-link > span { min-width: 0; overflow-wrap: anywhere; }
    .mp-nav-link:hover { background: var(--c-bg); }
    .mp-nav-link.is-active { color: var(--c-primary); background: var(--c-primary-subtle); font-weight: 600; }
    .mp-nav-link.is-active > svg { color: var(--c-primary); }
    .mp-nav-link.is-active::before { content: ''; position: absolute; left: 0; top: 50%; transform: translateY(-50%); width: 3px; height: 20px; border-radius: 0 3px 3px 0; background: var(--c-primary); }
    .mp-sidebar.is-collapsed .mp-nav-link { justify-content: center; padding: 9px 0; }
    .mp-sidebar.is-collapsed .mp-nav-group + .mp-nav-group { border-top: 1px solid var(--c-border); margin-top: 8px; padding-top: 8px; }
    .mp-role-toggle { display: flex; align-items: center; gap: 8px; width: 100%; padding: 8px; border: 0; border-radius: 8px; background: var(--c-bg); color: var(--c-fg-sec); font-size: 12px; font-weight: 600; cursor: pointer; }
    .mp-role-name { flex: 1; text-align: left; }
    .mp-role-initial { display: grid; place-items: center; width: 24px; height: 24px; background: white; border: 1px solid var(--c-border); border-radius: 6px; color: var(--c-primary); font-size: 11px; }
    .mp-role-toggle svg { transition: transform .15s; }
    .mp-role-toggle svg.is-open { transform: rotate(180deg); }
    .mp-sidebar.is-collapsed .mp-role-toggle { justify-content: center; padding: 6px 0; }
    .mp-sidebar-footer { padding: 8px 10px 12px; border-top: 1px solid var(--c-border); flex-shrink: 0; }
    .mp-sidebar-footer form { margin: 0; }
    .mp-nav-logout, .mp-nav-logout > svg { color: #DF1C41; }
    .mp-nav-logout:hover { background: #FEF1F4; }
    .mp-sidebar-backdrop, .mp-mobile-menu { display: none; }
    .mp-topbar { display: flex; align-items: center; justify-content: space-between; gap: 16px; min-height: 60px; padding: 10px 24px; background: white; border-bottom: 1px solid var(--c-border); flex-shrink: 0; z-index: 30; }
    .mp-breadcrumb { display: flex; align-items: center; gap: 7px; min-width: 0; font-size: 12px; }
    .mp-breadcrumb a { color: var(--c-fg-muted); text-decoration: none; white-space: nowrap; flex-shrink: 0; }
    .mp-breadcrumb a:hover { color: var(--c-primary); }
    .mp-breadcrumb > span { color: var(--c-border-strong); flex-shrink: 0; }
    .mp-breadcrumb strong { min-width: 0; font-weight: 600; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
    .mp-topbar-actions { display: flex; align-items: center; gap: 14px; min-width: 0; }
    .mp-notification-count { display: inline-flex; align-items: center; gap: 5px; padding: 7px; border: 1px solid var(--c-border); border-radius: 8px; color: var(--c-fg-muted); font-size: 11px; }
    .mp-praktikum-switcher { position: relative; min-width: 0; }
    .mp-praktikum-switcher > button { width: 100%; }
    .mp-account { display: flex; align-items: center; gap: 10px; padding-left: 14px; border-left: 1px solid var(--c-border); text-decoration: none; }
    .mp-account-avatar { display: grid; place-items: center; width: 34px; height: 34px; flex-shrink: 0; overflow: hidden; border-radius: 50%; color: white; background: linear-gradient(135deg,#5C78B8,#0B266E); font-size: 12px; font-weight: 700; }
    .mp-account-avatar img { width: 100%; height: 100%; object-fit: cover; }
    .mp-account-meta { display: flex; flex-direction: column; gap: 3px; line-height: 1.2; }
    .mp-account-meta strong { max-width: 150px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; font-size: 13px; color: var(--c-fg); font-weight: 600; }
    .mp-account-meta > span { font-size: 11px; color: var(--c-fg-muted); }
    .mp-header-slot { padding: 20px 24px; border-bottom: 1px solid var(--c-border); flex-shrink: 0; }
    .mp-app .mp-box-body { min-width: 0; min-height: 0; gap: 20px; }
    .mp-box-body > * { min-width: 0; }
    .mp-app .mp-page-title { font-size: 22px; }
    .mp-app .mp-page-sub { line-height: 1.6; margin-top: 5px; }
    .mp-app .mp-card { border-radius: 12px; }
    .mp-app .mp-card-header { flex-wrap: wrap; padding: 14px 18px; }
    .mp-app .mp-card-title { font-size: 14px; }
    .mp-app .mp-card-body { min-width: 0; }
    .mp-app .mp-stat { padding: 16px; border-radius: 12px; }
    .mp-app .mp-stat-value { font-variant-numeric: tabular-nums; }
    .mp-app .mp-stats-grid { gap: 12px; }
    .mp-app .mp-stats-grid.cols-2 { grid-template-columns: repeat(2,minmax(0,1fr)); }
    .mp-app .mp-stats-grid.cols-3 { grid-template-columns: repeat(3,minmax(0,1fr)); }
    .mp-app .mp-stats-grid.cols-4 { grid-template-columns: repeat(4,minmax(0,1fr)); }
    .mp-app .mp-btn { justify-content: center; min-height: 34px; line-height: 1.3; }
    .mp-app .mp-btn:disabled { opacity: .5; cursor: not-allowed; box-shadow: none; }
    .mp-app .mp-input { min-height: 38px; }
    .mp-app .mp-table th { color: var(--c-fg-muted); text-transform: none; letter-spacing: 0; font-size: 12px; padding: 12px 16px; }
    .mp-app .mp-table td { padding: 12px 16px; }
    .mp-content-grid { display: grid; grid-template-columns: repeat(2,minmax(0,1fr)); gap: 16px; }
    .mp-content-grid > * { min-width: 0; }
    .mp-form-grid { display: grid; grid-template-columns: repeat(2,minmax(0,1fr)); gap: 16px; }
    .mp-data-scroll { overflow-x: auto; max-width: 100%; scrollbar-width: thin; }
    .mp-data-scroll > .grid { min-width: 800px; }
    .mp-heading-row { display: flex; align-items: center; flex-wrap: wrap; gap: 8px; }
    .mp-enrollment-guide { padding: 20px; }
    .mp-enrollment-intro { display: flex; align-items: flex-start; gap: 12px; }
    .mp-enrollment-intro h2 { font-size: 15px; font-weight: 600; }
    .mp-enrollment-intro p { margin-top: 5px; color: var(--c-fg-muted); font-size: 12px; line-height: 1.6; }
    .mp-enrollment-guide > h3 { margin-top: 20px; padding-top: 16px; border-top: 1px solid var(--c-border); font-size: 11px; font-weight: 600; color: var(--c-fg-muted); }
    .mp-enrollment-steps { list-style: none; display: grid; grid-template-columns: repeat(4,minmax(0,1fr)); gap: 16px; margin: 14px 0 0; padding: 0; }
    .mp-enrollment-steps li { position: relative; min-width: 0; }
    .mp-enrollment-steps li:not(:last-child)::before { content: ''; position: absolute; top: 16px; left: 32px; width: calc(100% - 16px); height: 1px; background: var(--c-border); }
    .mp-step-number { display: grid; place-items: center; width: 32px; height: 32px; border-radius: 50%; border: 1px solid var(--c-primary-border); background: var(--c-primary-subtle); color: var(--c-primary); font-size: 12px; font-weight: 600; margin-bottom: 10px; }
    .mp-enrollment-steps strong { font-size: 12px; font-weight: 600; }
    .mp-enrollment-steps p { color: var(--c-fg-muted); font-size: 11px; line-height: 1.5; margin-top: 3px; }
    @media (max-width: 1100px) {
        .mp-account-meta { display: none; }
        .mp-app .mp-stats-grid.cols-4 { grid-template-columns: repeat(2,minmax(0,1fr)); }
    }
    @media (max-width: 767px) {
        body.mp-app { height: 100dvh; overflow: hidden; }
        .mp-sidebar { position: fixed; inset: 0 auto 0 0; height: 100dvh; width: min(280px,85vw) !important; transform: translateX(-100%); visibility: hidden; }
        .mp-sidebar-open .mp-sidebar { transform: translateX(0); visibility: visible; }
        .mp-sidebar-backdrop { display: block; position: fixed; inset: 0; z-index: 35; background: rgba(13,13,18,.4); }
        .mp-mobile-menu { display: inline-flex; width: 36px; height: 36px; }
        .mp-nav-link { min-height: 42px; }
        .mp-topbar { position: sticky; top: 0; padding: 10px 14px; gap: 8px; flex-wrap: wrap; }
        .mp-breadcrumb { flex: 1; }
        .mp-breadcrumb .mp-breadcrumb-root { display: none; }
        .mp-topbar-actions { flex-wrap: wrap; }
        .mp-praktikum-switcher { max-width: 180px; }
        .mp-account { padding-left: 0; border-left: 0; }
        .mp-app .mp-wrap { padding: 8px; }
        .mp-app .mp-box-body, .mp-header-slot { padding: 16px; }
        .mp-app .mp-page-title { font-size: 20px; }
        .mp-app .mp-page-actions { width: 100%; }
        .mp-app .mp-page-actions > .mp-btn { flex: 1; }
        .mp-app .mp-card-header .right { margin-left: 0; flex-wrap: wrap; }
        .mp-content-grid, .mp-form-grid { grid-template-columns: minmax(0,1fr); }
        .mp-app .mp-stats-grid.cols-3 { grid-template-columns: repeat(2,minmax(0,1fr)); }
        .mp-app .mp-stat { padding: 14px; }
        .mp-app .sec-title { white-space: normal; }
        .mp-app .mp-divider { margin-inline: -16px; }
        .mp-enrollment-guide { padding: 16px; }
        .mp-enrollment-steps { grid-template-columns: minmax(0,1fr); }
        .mp-enrollment-steps li { display: flex; gap: 12px; }
        .mp-step-number { flex-shrink: 0; margin-bottom: 0; }
        .mp-enrollment-steps li:not(:last-child)::before { top: 32px; left: 16px; width: 1px; height: calc(100% - 16px); }
    }
    @media (max-width: 480px) {
        .mp-topbar:has(.mp-praktikum-switcher) .mp-topbar-actions { width: 100%; justify-content: space-between; }
        .mp-praktikum-switcher { max-width: calc(100% - 48px); }
        .mp-app .mp-stats-grid.cols-2, .mp-app .mp-stats-grid.cols-3, .mp-app .mp-stats-grid.cols-4 { grid-template-columns: minmax(0,1fr); }
    }
    @media (prefers-reduced-motion: reduce) { .mp-app *, .mp-app *::before { transition: none !important; scroll-behavior: auto !important; } }
</style>
<?php /**PATH C:\WebsiteTekkom - Copy\Modules\EOffice\resources\views\manajemen-praktikum\partials\_shell-styles.blade.php ENDPATH**/ ?>