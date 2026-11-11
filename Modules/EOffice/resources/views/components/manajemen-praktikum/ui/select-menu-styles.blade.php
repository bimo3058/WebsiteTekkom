@once
<style>
.ta-select{display:flex;align-items:center;justify-content:space-between;gap:12px;width:100%;min-height:42px;border:1px solid #DFE1E7;border-radius:8px;background:white;padding:10px 12px;text-align:left;font-size:13px;color:#353849;cursor:pointer}
.ta-options{position:absolute;top:calc(100% + 5px);left:0;right:0;z-index:50;background:white;border:1px solid #DFE1E7;border-radius:8px;padding:0;max-height:240px;overflow-y:auto;box-shadow:0 4px 12px rgba(13,13,18,.12)}
.ta-options .ta-option{display:block;width:100%;border:0;text-align:left;cursor:pointer}
.ta-options .ta-option{padding:7px 14px;border-radius:0;background:transparent;color:var(--c-fg-sec);font-size:12px;font-weight:500;line-height:20px}
.ta-options .ta-option:hover,.ta-options .ta-option:focus-visible{background:var(--c-bg);color:var(--c-fg-sec)}
.ta-options .ta-option.selected{background:#EEF1F8;color:var(--c-primary);font-weight:700}
</style>
@endonce
