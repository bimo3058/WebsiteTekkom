{{-- Shared dashboard styling, aligned with the superadmin dashboard. --}}
<style>
    .eo-dashboard {
        --c-primary: #0B266E; --c-primary-subtle: #EEF1F8; --c-primary-border: #CED4E0;
        --c-fg: #0D0D12; --c-fg-muted: #666D80; --c-border: #DFE1E7;
        --shadow-card: 0 1px 2px rgba(228,229,231,.5);
    }
    .eo-dashboard [x-cloak] { display: none !important; }
    .eo-dashboard a:focus-visible, .eo-dashboard button:focus-visible { outline: 2px solid var(--c-primary); outline-offset: 3px; }
    .eo-dashboard-main { display: flex; flex: 1; flex-direction: column; min-width: 0; overflow: hidden; }
    .eo-sidebar { display: flex; flex-direction: column; flex-shrink: 0; width: 240px; height: 100%; min-height: 0; background: white; border-right: 1px solid var(--c-border); transition: width .25s ease, transform .25s ease; z-index: 20; }
    .eo-sidebar.is-collapsed { width: 64px; }
    .eo-sidebar-brand { display: flex; align-items: center; gap: 8px; min-height: 60px; padding: 12px 14px; border-bottom: 1px solid var(--c-border); flex-shrink: 0; }
    .eo-brand-link { display: flex; align-items: center; gap: 8px; flex: 1; min-width: 0; text-decoration: none; }
    .eo-brand-link > img { width: 32px; height: 32px; object-fit: contain; flex-shrink: 0; }
    .eo-brand-text { min-width: 0; }
    .eo-brand-text strong { display: block; font-family: 'Geist','Inter Tight',sans-serif; font-size: 14px; font-weight: 700; line-height: 1.2; letter-spacing: -.01em; }
    .eo-brand-text > span { display: block; margin-top: 2px; font-size: 9px; color: var(--c-fg-muted); white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
    .eo-sidebar-toggle, .eo-icon-button { display: inline-flex; align-items: center; justify-content: center; width: 28px; height: 28px; flex-shrink: 0; padding: 0; border: 1px solid var(--c-border); border-radius: 7px; background: white; color: var(--c-fg-muted); cursor: pointer; transition: background .15s, border-color .15s; }
    .eo-sidebar-toggle:hover, .eo-icon-button:hover { background: #F6F8FA; border-color: #C1C7CF; color: var(--c-fg); }
    .eo-sidebar-toggle svg { transition: transform .25s ease; }
    .eo-sidebar.is-collapsed .eo-sidebar-brand { flex-direction: column; padding: 12px 10px 8px; }
    .eo-sidebar.is-collapsed .eo-brand-link { flex: none; }
    .eo-sidebar.is-collapsed .eo-sidebar-toggle svg { transform: rotate(180deg); }
    .eo-sidebar-nav { flex: 1; min-height: 0; overflow-y: auto; overflow-x: hidden; padding: 6px 10px 10px; scrollbar-width: thin; scrollbar-color: var(--c-border) transparent; }
    .eo-nav-label { padding: 12px 10px 5px; color: var(--c-fg-muted); font-size: 10px; font-weight: 600; letter-spacing: .06em; text-transform: uppercase; white-space: nowrap; }
    .eo-nav-link { position: relative; display: flex; align-items: center; gap: 9px; width: 100%; min-height: 36px; padding: 8px 10px 8px 14px; margin: 1px 0; border: 0; border-radius: 8px; background: transparent; color: #353849; font-family: inherit; font-size: 13px; font-weight: 500; line-height: 1.4; text-decoration: none; text-align: left; white-space: nowrap; cursor: pointer; transition: background .12s, color .12s; }
    .eo-nav-link > svg { width: 16px; height: 16px; flex-shrink: 0; color: var(--c-fg-muted); }
    .eo-nav-link > span { min-width: 0; overflow: hidden; text-overflow: ellipsis; }
    .eo-nav-link:hover { background: #F6F8FA; }
    .eo-nav-link.is-active { background: var(--c-primary-subtle); color: var(--c-primary); font-weight: 600; }
    .eo-nav-link.is-active > svg { color: var(--c-primary); }
    .eo-nav-link.is-active::before { content: ''; position: absolute; left: 0; top: 50%; transform: translateY(-50%); width: 3px; height: 20px; border-radius: 0 3px 3px 0; background: var(--c-primary); }
    .eo-sidebar.is-collapsed .eo-nav-link { justify-content: center; padding: 9px 0; }
    .eo-sidebar.is-collapsed .eo-nav-group + .eo-nav-group { border-top: 1px solid var(--c-border); margin-top: 10px; padding-top: 10px; }
    .eo-sidebar-footer { padding: 8px 10px 12px; border-top: 1px solid var(--c-border); flex-shrink: 0; }
    .eo-sidebar-footer form { margin: 0; }
    .eo-nav-logout, .eo-nav-logout > svg { color: #DF1C41; }
    .eo-nav-logout:hover { background: #FEF1F4; }
    .eo-topbar { height: 60px; padding: 0 28px; display: flex; align-items: center; justify-content: space-between; gap: 16px; background: white; border-bottom: 1px solid var(--c-border); flex-shrink: 0; }
    .eo-breadcrumb { display: flex; align-items: center; gap: 7px; font-size: 12px; color: var(--c-fg-muted); white-space: nowrap; }
    .eo-breadcrumb a { color: inherit; text-decoration: none; }
    .eo-breadcrumb a:hover { color: var(--c-primary); }
    .eo-breadcrumb-separator { color: #C1C7CF; }
    .eo-breadcrumb strong { color: var(--c-fg); font-weight: 600; }
    .eo-topbar-right { display: flex; align-items: center; min-width: 0; }
    .eo-topbar-account { display: flex; align-items: center; gap: 10px; min-width: 0; padding-left: 14px; border-left: 1px solid var(--c-border); text-decoration: none; border-radius: 2px; }
    .eo-account-meta { display: flex; flex-direction: column; gap: 3px; min-width: 0; line-height: 1.2; }
    .eo-account-meta > strong { max-width: 180px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; color: var(--c-fg); font-size: 13px; font-weight: 600; }
    .eo-account-meta > span { color: var(--c-fg-muted); font-size: 11px; }
    .eo-avatar { display: grid; place-items: center; width: 34px; height: 34px; flex-shrink: 0; overflow: hidden; border-radius: 50%; background: linear-gradient(135deg,#5C78B8,#0B266E); color: white; font-size: 12px; font-weight: 700; }
    .eo-avatar img { width: 100%; height: 100%; object-fit: cover; }
    .eo-dashboard-wrap { display: flex; flex: 1; min-height: 0; padding: 10px; }
    .eo-dashboard-box { display: flex; flex-direction: column; flex: 1; min-width: 0; min-height: 0; background: white; border: 1px solid var(--c-border); border-radius: 12px; box-shadow: 0 1px 3px rgba(0,0,0,.06); overflow: hidden; }
    .eo-dashboard-header { padding: 18px 24px; border-bottom: 1px solid var(--c-border); display: flex; align-items: center; justify-content: space-between; gap: 16px; flex-wrap: wrap; flex-shrink: 0; }
    .eo-heading-row { display: flex; align-items: center; gap: 8px; flex-wrap: wrap; }
    .eo-heading-row h1 { font-size: 22px; font-weight: 700; color: var(--c-fg); letter-spacing: -.02em; line-height: 1.2; }
    .eo-role-badge { font-size: 10px; font-weight: 600; color: var(--c-primary); background: var(--c-primary-subtle); border: 1px solid var(--c-primary-border); padding: 2px 8px; border-radius: 999px; }
    .eo-welcome { display: flex; flex-wrap: wrap; align-items: baseline; gap: 4px 8px; margin-top: 5px; color: var(--c-fg-muted); font-size: 12px; line-height: 1.7; overflow-wrap: anywhere; }
    .eo-welcome-separator { color: #A4ABB8; }
    .eo-welcome strong { color: var(--c-fg); font-weight: 600; }
    .eo-header-actions { display: flex; gap: 8px; flex-wrap: wrap; }
    .eo-action { display: inline-flex; align-items: center; justify-content: center; gap: 7px; padding: 8px 14px; border: 1px solid var(--c-border); border-radius: 8px; background: white; color: #353849; font-size: 12px; font-weight: 600; text-decoration: none; box-shadow: var(--shadow-card); transition: background .15s, border-color .15s; }
    .eo-action:hover { background: #F6F8FA; border-color: #C1C7CF; }
    .eo-action-primary { background: var(--c-primary); color: white; border-color: var(--c-primary); }
    .eo-action-primary:hover { background: #163982; border-color: #163982; }
    .eo-dashboard-content { flex: 1; overflow-y: auto; min-height: 0; padding: 20px 24px; display: flex; flex-direction: column; gap: 20px; scrollbar-width: thin; scrollbar-color: #C1C7CF transparent; }
    .eo-dashboard-content > * { flex-shrink: 0; min-width: 0; }
    .eo-summary { display: flex; gap: 8px 20px; align-items: center; flex-wrap: wrap; padding-bottom: 14px; border-bottom: 1px solid #ECEFF3; font-size: 11px; color: var(--c-fg-muted); }
    .eo-summary strong { color: var(--c-primary); font-weight: 700; }
    .eo-stat-grid { display: grid; grid-template-columns: repeat(4,minmax(0,1fr)); gap: 12px; }
    .eo-stat-grid > div { padding: 14px 16px; border-radius: 12px; box-shadow: var(--shadow-card); transition: border-color .15s, box-shadow .15s; }
    .eo-stat-grid > div:hover { border-color: var(--c-primary-border); box-shadow: 0 4px 14px rgba(11,38,110,.07); }
    .eo-stat-grid > div > div:first-child { flex-direction: row-reverse; justify-content: flex-end; align-items: center; gap: 8px; }
    .eo-stat-grid > div > div:first-child > div { width: 28px; height: 28px; border-radius: 8px; background: var(--c-primary-subtle) !important; color: var(--c-primary) !important; }
    .eo-stat-grid svg { stroke: var(--c-primary); }
    .eo-stat-grid > div > div:nth-child(2) { font-size: 24px; font-variant-numeric: tabular-nums; letter-spacing: -.02em; }
    .eo-section-title { display: flex; align-items: center; gap: 8px; color: var(--c-fg); font-size: 14px; font-weight: 700; margin-bottom: -8px; }
    .eo-section-title::before { content: ''; width: 3px; height: 14px; border-radius: 2px; background: var(--c-primary); }
    .eo-service-grid { display: grid; grid-template-columns: repeat(3,minmax(0,1fr)); gap: 12px; }
    .eo-two-grid { display: grid; grid-template-columns: repeat(2,minmax(0,1fr)); gap: 12px; }
    .eo-service-grid > a, .eo-two-grid > a { border-radius: 14px; padding: 16px; box-shadow: var(--shadow-card); }
    .eo-service-grid > a:hover, .eo-two-grid > a:hover { border-color: var(--c-primary-border); box-shadow: 0 4px 14px rgba(11,38,110,.07); }
    .eo-service-grid > a > div:last-child, .eo-two-grid > a > div:last-child { padding-top: 12px; border-top: 1px solid #ECEFF3; }
    .eo-detail-grid { display: grid; grid-template-columns: minmax(0,2fr) minmax(0,1fr); gap: 16px; align-items: start; }
    .eo-detail-grid-student { grid-template-columns: repeat(3,minmax(0,1fr)); }
    .eo-detail-grid > div { min-height: 220px; }
    .eo-table-scroll { overflow-x: auto; }
    .eo-table-wide { min-width: 620px; }
    .eo-dashboard .mp-btn { display: inline-flex; border: 1px solid var(--c-border); background: white; color: #353849; font-weight: 600; }
    .eo-sidebar-backdrop, .eo-mobile-menu { display: none; }
    @media (max-width: 1200px) {
        .eo-stat-grid { grid-template-columns: repeat(2,minmax(0,1fr)); }
        .eo-service-grid { grid-template-columns: repeat(2,minmax(0,1fr)); }
        .eo-detail-grid, .eo-detail-grid-student { grid-template-columns: minmax(0,1fr); }
    }
    @media (max-width: 767px) {
        body.eo-dashboard { height: auto; overflow: auto; }
        .eo-dashboard-shell { height: auto; min-height: 100svh; overflow: visible; }
        .eo-dashboard-main { overflow: visible; }
        .eo-dashboard-shell > aside { position: fixed; inset: 0 auto 0 0; height: 100dvh; width: 240px !important; z-index: 50; transform: translateX(-110%); visibility: hidden; }
        .eo-dashboard-shell.eo-sidebar-open > aside { transform: translateX(0); visibility: visible; }
        .eo-sidebar-backdrop { display: block; position: fixed; inset: 0; z-index: 40; background: rgba(13,13,18,.4); }
        .eo-mobile-menu { display: inline-flex; padding: 7px; }
        .eo-topbar { position: sticky; top: 0; z-index: 30; min-height: 52px; padding: 10px 14px; }
        .eo-account-meta { display: none; }
        .eo-topbar-account { padding-left: 0; border-left: 0; }
        .eo-icon-button { width: 36px; height: 36px; }
        .eo-nav-link { min-height: 44px; }
        .eo-sidebar-toggle { width: 32px; height: 32px; }
        .eo-welcome time { flex-basis: 100%; }
        .eo-welcome-separator { display: none; }
        .eo-dashboard-wrap { display: block; padding: 8px; }
        .eo-dashboard-box { overflow: visible; }
        .eo-dashboard-header { padding: 16px; }
        .eo-heading-row h1 { font-size: 20px; }
        .eo-dashboard-content { overflow: visible; padding: 16px; gap: 16px; }
        .eo-header-actions { width: 100%; }
        .eo-header-actions a { flex: 1; }
        .eo-two-grid, .eo-service-grid { grid-template-columns: minmax(0,1fr); }
        .eo-stat-grid { gap: 8px; }
        .eo-stat-grid > div { padding: 12px; }
    }
    .eo-section-heading { display: flex; align-items: flex-start; justify-content: space-between; gap: 12px; flex-wrap: wrap; margin-bottom: 16px; }
    .eo-section-heading .eo-section-title, .eo-timeline-header .eo-section-title { margin-bottom: 0; }
    .eo-section-heading p, .eo-timeline-header p { font-size: 12px; color: var(--c-fg-muted); margin-top: 6px; }
    .eo-updated { color: var(--c-fg-muted); font-size: 11px; }
    .eo-overview-grid { display: grid; grid-template-columns: repeat(2,minmax(0,1fr)); gap: 16px; }
    .eo-priority-timeline { grid-column: 1 / -1; min-width: 0; }
    .eo-overview-card { display: flex; flex-direction: column; border: 1px solid var(--c-border); border-radius: 12px; box-shadow: var(--shadow-card); overflow: hidden; }
    .eo-overview-heading { display: flex; align-items: center; gap: 12px; padding: 18px; }
    .eo-service-icon { color: var(--c-primary); background: var(--c-primary-subtle); display: grid; place-items: center; flex-shrink: 0; width: 38px; height: 38px; border-radius: 10px; }
    .eo-overview-heading h3 { font-size: 14px; font-weight: 700; }
    .eo-overview-heading p { font-size: 11px; color: var(--c-fg-muted); margin-top: 3px; }
    .eo-service-metrics { display: grid; grid-template-columns: repeat(2,minmax(0,1fr)); margin: 0 18px 16px; border: 1px solid #ECEFF3; border-radius: 10px; background: #F8F9FB; }
    .eo-service-metrics > div { padding: 12px; }
    .eo-service-metrics dt { font-size: 11px; color: var(--c-fg-muted); }
    .eo-service-metrics dd { font-size: 22px; line-height: 1.4; font-weight: 700; color: var(--c-primary); font-variant-numeric: tabular-nums; }
    .eo-service-records { padding: 0 18px 16px; flex: 1; }
    .eo-service-records h4 { font-size: 11px; font-weight: 600; color: var(--c-fg-muted); margin-bottom: 8px; }
    .eo-service-record { display: flex; align-items: flex-start; justify-content: space-between; gap: 12px; padding: 10px 0; border-top: 1px solid #ECEFF3; }
    .eo-service-record > div { min-width: 0; }
    .eo-service-record strong { font-size: 12px; font-weight: 600; overflow-wrap: anywhere; }
    .eo-service-record p { font-size: 11px; color: var(--c-fg-muted); line-height: 1.6; margin-top: 3px; overflow-wrap: anywhere; }
    .eo-status-label { font-size: 10px; padding: 3px 7px; background: var(--c-primary-subtle); color: var(--c-primary); border-radius: 6px; max-width: 110px; flex-shrink: 0; }
    .eo-overview-card footer { padding: 12px 18px; border-top: 1px solid var(--c-border); background: #FAFBFC; }
    .eo-overview-card footer p { font-size: 11px; color: var(--c-fg-muted); margin-bottom: 6px; }
    .eo-service-link { font-size: 12px; font-weight: 600; color: var(--c-primary); display: flex; justify-content: space-between; gap: 8px; }
    .eo-service-link:hover { text-decoration: underline; }
    .eo-empty-copy { font-size: 12px; color: var(--c-fg-muted); }
    /* Compact phase markers based on CTMS StatusGroup.tsx. */
    .eo-timeline-panel { padding: 20px; border: 1px solid #ECEFF3; border-radius: 12px; background: white; box-shadow: var(--shadow-card); }
    .eo-timeline-header { display: flex; align-items: flex-start; justify-content: space-between; gap: 12px; flex-wrap: wrap; margin-bottom: 20px; }
    .eo-timeline-header h2 { font-size: 16px; font-weight: 600; color: #272835; }
    .eo-timeline-header p { font-size: 12px; margin-top: 3px; color: var(--c-fg-muted); }
    .eo-timeline-actions { display: flex; align-items: center; flex-wrap: wrap; gap: 12px; }
    .eo-timeline-actions .eo-service-link { gap: 6px; }
    .eo-timeline-steps { --eo-node-gap: 8px; display: grid; grid-template-columns: repeat(4,minmax(0,1fr)); gap: var(--eo-node-gap); list-style: none; }
    .eo-timeline-step { min-width: 0; position: relative; }
    .eo-timeline-step:not(:last-child)::after { content: ''; position: absolute; top: 25px; left: calc(50% + 24px); width: calc(100% + var(--eo-node-gap) - 48px); height: 2px; background: #CED4E0; pointer-events: none; }
    .eo-step-trigger { display: flex; flex-direction: column; align-items: center; gap: 6px; padding: 6px 2px; width: 100%; border: 0; background: transparent; border-radius: 8px; cursor: pointer; text-align: center; }
    .eo-step-trigger:hover { background: #F8F9FB; }
    .eo-step-icon { display: grid; place-items: center; width: 40px; height: 40px; border: 2px solid #DFE1E7; border-radius: 8px; color: #808897; background: #F6F8FA; margin-bottom: 2px; transition: box-shadow .15s, border-color .15s; }
    .eo-step-active .eo-step-icon { color: var(--c-primary); border-color: var(--c-primary); background: white; box-shadow: 0 0 0 3px rgba(11,38,110,.16); }
    .eo-step-invalid .eo-step-icon { color: #956321; border-color: #D39C3D; background: #FFF6DF; }
    .eo-step-icon.eo-step-selected { box-shadow: 0 0 0 3px rgba(11,38,110,.16); border-color: var(--c-primary); }
    .eo-step-title { font-size: 12px; font-weight: 600; line-height: 1.4; color: #666D80; }
    .eo-step-active .eo-step-title, .eo-step-active .eo-step-status { color: var(--c-primary); }
    .eo-step-status, .eo-step-date { font-size: 10px; line-height: 1.4; color: var(--c-fg-muted); }
    .eo-step-date { font-variant-numeric: tabular-nums; }
    .eo-timeline-hint { display: flex; align-items: center; justify-content: space-between; gap: 8px; flex-wrap: wrap; padding-top: 16px; font-size: 11px; color: var(--c-fg-muted); }
    .eo-step-deadline { color: #956321; background: #FFF6DF; border-radius: 5px; padding: 4px 7px; }
    .eo-step-detail { margin-top: 14px; border: 1px solid var(--c-primary-border); border-radius: 8px; padding: 14px; background: #F8FAFF; }
    .eo-step-detail-heading { display: flex; align-items: center; gap: 10px; flex-wrap: wrap; font-size: 12px; }
    .eo-step-dates { display: flex; flex-wrap: wrap; gap: 12px 28px; margin-top: 12px; }
    .eo-step-dates dt { font-size: 10px; color: var(--c-fg-muted); }
    .eo-step-dates dd { font-size: 12px; font-weight: 600; margin-top: 3px; }
    .eo-step-detail p { font-size: 12px; line-height: 1.6; margin-top: 10px; color: #353849; }
    .eo-step-detail .eo-detail-note { font-size: 11px; color: var(--c-fg-muted); }
    .eo-timeline-notices { border-top: 1px solid #ECEFF3; margin-top: 14px; padding-top: 12px; font-size: 12px; }
    .eo-timeline-notices summary { font-weight: 500; color: var(--c-primary); cursor: pointer; }
    .eo-timeline-notices article { padding-top: 12px; }
    .eo-timeline-notices h3 { font-weight: 600; }
    .eo-timeline-notices p { margin-top: 8px; color: var(--c-fg-muted); line-height: 1.7; white-space: pre-line; overflow-wrap: anywhere; }
    @media (max-width: 767px) {
        .eo-overview-grid { grid-template-columns: minmax(0,1fr); }
        .eo-updated { width: 100%; }
        .eo-timeline-panel { padding: 16px 12px; }
        .eo-timeline-steps { --eo-node-gap: 4px; }
        .eo-step-title { font-size: 11px; }
        .eo-step-date { font-size: 9px; }
    }
    @media (prefers-reduced-motion: reduce) { .eo-dashboard *, .eo-dashboard *::before { transition: none !important; } }
</style>
