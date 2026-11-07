@once
<style>
    .mp-pagination { display:flex;align-items:center;justify-content:space-between;gap:10px;padding:12px 16px;border-top:1px solid #DFE1E7;background:#fff;flex-shrink:0; }
    .mp-pagination-info { display:flex;align-items:center;gap:10px;flex-wrap:wrap;font-size:12px;color:#353849; }
    .mp-pagination-size { display:flex;align-items:center;gap:6px;color:#666D80;white-space:nowrap; }
    .mp-pagination-dropdown { position:relative; }
    .mp-pagination-select { display:flex;align-items:center;gap:6px;height:28px;padding:0 8px;border:1px solid #DFE1E7;border-radius:6px;background:#fff;color:#353849;font-size:12px;font-weight:600;cursor:pointer; }
    .mp-pagination-select svg { color:#666D80;transition:transform .15s; }
    .mp-page-button svg { width:13px;height:13px; }
    .mp-pagination-options { position:absolute;bottom:calc(100% + 5px);left:0;min-width:80px;z-index:50;background:#fff;border:1px solid #DFE1E7;border-radius:8px;box-shadow:0 4px 12px rgba(13,13,18,.12);overflow:hidden; }
    .mp-pagination-options[x-cloak] { display:none!important; }
    .mp-pagination-option { display:block;width:100%;padding:7px 14px;border:0;background:transparent;color:#353849;font-size:12px;font-weight:500;text-align:left;text-decoration:none;cursor:pointer; }
    .mp-pagination-option:hover { background:#F6F8FA; }
    .mp-pagination-option.is-selected { background:#EEF1F8;color:#0B266E;font-weight:700; }
    .mp-pagination-summary { border-left:1px solid #DFE1E7;padding-left:12px;white-space:nowrap; }
    .mp-pagination-summary strong { color:#0D0D12;font-weight:600; }
    .mp-pagination-pages { display:flex;align-items:center;gap:4px;flex-shrink:0; }
    .mp-page-button { display:inline-flex;align-items:center;justify-content:center;min-width:28px;height:28px;padding:0 6px;box-sizing:border-box;border:1px solid #DFE1E7;border-radius:6px;background:#fff;color:#353849;font-size:12px;text-decoration:none;cursor:pointer; }
    .mp-page-button:hover { background:#F6F8FA; }
    .mp-page-button.is-current { background:#0B266E;border-color:#0B266E;color:#fff;box-shadow:0 2px 5px rgba(11,38,110,.18); }
    .mp-page-button:disabled,.mp-page-button[aria-disabled="true"] { background:#F9FAFB;border-color:#EEF0F3;color:#C5CAD3;cursor:default; }
    .mp-page-button:focus-visible,.mp-pagination-select:focus-visible { outline:2px solid #0B266E;outline-offset:2px; }
    .mp-page-gap { display:inline-flex;align-items:center;justify-content:center;min-width:30px;color:#666D80;font-size:12px; }
    @media(max-width:640px) { .mp-pagination { flex-wrap:wrap;padding:12px;gap:12px; }.mp-pagination-summary { white-space:normal; }.mp-pagination-pages { margin-left:auto;max-width:100%;flex-wrap:wrap; } }
</style>
@endonce
