@once
<style>
    .mp-settings-card{flex-shrink:0;overflow:visible}
    .mp-settings-card .mp-card-header{padding:20px 24px;align-items:center;justify-content:space-between;gap:16px;flex-wrap:wrap}
    .mp-settings-card .mp-card-title{margin:0;font-size:15px;line-height:22px}
    .mp-settings-description{margin:6px 0 0;font-size:13px;line-height:20px;color:var(--c-fg-muted)}
    .mp-settings-body{padding:20px 24px;display:grid;gap:20px;min-width:0}
    .mp-settings-label{display:block;margin:0 0 8px;font-size:12px;font-weight:700;color:var(--c-fg-sec,#353849)}
    .mp-settings-footer{display:flex;align-items:center;justify-content:space-between;gap:16px;flex-wrap:wrap;padding:16px 24px;border-top:1px solid var(--c-border);background:#FAFBFC;border-radius:0 0 13px 13px}
    .mp-settings-footer .mp-settings-description{margin:0;flex:1;min-width:200px}
    .mp-settings-table{width:100%;border-collapse:collapse;font-size:13px}
    .mp-settings-table thead{background:#F8F9FB}
    .mp-settings-table th.mp-th{padding:14px 20px;font-size:12px;line-height:18px}
    .mp-settings-table td{padding:16px 20px;vertical-align:middle;color:var(--c-fg);border-bottom:1px solid var(--c-border)}
    .mp-settings-table .mp-settings-no{text-align:center;font-weight:700;color:var(--c-fg-muted);width:64px}
    .mp-settings-table .mp-settings-empty{padding:36px 24px;text-align:center;color:var(--c-fg-muted)}
    .mp-settings-table .mp-input{width:100%;max-width:160px;min-width:76px}
    .mp-settings-range{border:1px solid var(--c-border);border-radius:12px;min-width:0;overflow:hidden}
    .mp-settings-range-head{display:flex;align-items:flex-end;gap:16px;flex-wrap:wrap;padding:16px 20px;border-bottom:1px solid var(--c-border)}
    .mp-settings-range-head>.mp-settings-field{flex:1;min-width:140px;max-width:220px}
    .mp-settings-trash{display:inline-flex;align-items:center;justify-content:center;width:34px;height:34px;border:0;border-radius:8px;background:transparent;color:var(--c-error,#DF1C41);cursor:pointer}
    .mp-settings-trash:hover{background:#FEF1F4}.mp-settings-trash:disabled{opacity:.45;cursor:not-allowed}
    .mp-settings-table details>summary{cursor:pointer;font-weight:600;line-height:20px}
    .mp-settings-current{display:flex;gap:12px;align-items:center;padding-bottom:20px;border-bottom:1px dashed var(--c-border)}
    .mp-settings-name{font-size:13px;font-weight:600;line-height:20px;color:var(--c-fg)}
    .mp-settings-email{font-size:12px;line-height:18px;color:var(--c-fg-muted);overflow-wrap:anywhere}
    @media(max-width:640px){.mp-settings-card .mp-card-header,.mp-settings-body{padding:16px}.mp-settings-footer{padding:16px}.mp-settings-range-head{padding:16px}.mp-settings-footer>.mp-btn{margin-left:auto}.mp-settings-table th.mp-th,.mp-settings-table td{padding:12px 16px}}
</style>
@endonce
