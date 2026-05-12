{{-- resources/views/superadmin/dashboard/_import.blade.php --}}

<div style="display:flex;align-items:center;gap:8px;margin-bottom:12px;">
    <span style="width:3px;height:14px;border-radius:2px;background:var(--c-primary);"></span>
    <span style="font-size:14px;font-weight:700;color:var(--c-fg);">Import User</span>
</div>

<div style="background:#fff;border:1px solid var(--c-border);border-radius:14px;padding:14px 18px;margin-bottom:24px;box-shadow:var(--shadow-card);">
    <div style="display:flex;align-items:center;justify-content:space-between;gap:16px;flex-wrap:wrap;">

        <div style="display:flex;align-items:center;gap:12px;">
            <div style="width:38px;height:38px;border-radius:10px;background:var(--c-primary-subtle);color:var(--c-primary);display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M12 14V4M12 4l-4 4M12 4l4 4M4 18h16"/>
                </svg>
            </div>
            <div>
                <p style="font-size:13px;font-weight:600;color:var(--c-fg);line-height:1.3;">Import via CSV</p>
                <p style="font-size:11px;color:var(--c-fg-muted);margin-top:2px;">Upload file CSV untuk menambahkan banyak user</p>
            </div>
        </div>

        <div style="display:flex;align-items:center;gap:8px;">
            <a href="{{ route('superadmin.users.index') }}"
               style="display:inline-flex;align-items:center;gap:7px;padding:8px 14px;background:#fff;border:1px solid var(--c-border);border-radius:8px;font-size:13px;font-weight:600;color:var(--c-fg-sec);text-decoration:none;transition:background .15s;white-space:nowrap;"
               onmouseover="this.style.background='var(--c-bg)';this.style.borderColor='var(--c-border-strong)'"
               onmouseout="this.style.background='#fff';this.style.borderColor='var(--c-border)'">
                <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="12" cy="8" r="3.5"/><path d="M5 20a7 7 0 0114 0"/>
                </svg>
                <span>Kelola Users</span>
            </a>

            <button onclick="openModal('modalImportUser')"
                    style="display:inline-flex;align-items:center;gap:7px;padding:8px 14px;background:var(--c-primary);border:1px solid var(--c-primary);border-radius:8px;font-size:13px;font-weight:600;color:#fff;cursor:pointer;font-family:inherit;transition:background .15s;white-space:nowrap;"
                    onmouseover="this.style.background='var(--c-primary-hover)';this.style.borderColor='var(--c-primary-hover)'"
                    onmouseout="this.style.background='var(--c-primary)';this.style.borderColor='var(--c-primary)'">
                <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M12 4v12M12 16l-4-4M12 16l4-4M4 20h16"/>
                </svg>
                Import
            </button>
        </div>
    </div>

    {{-- Progress bar --}}
    <div id="importProgressBody"
         class="{{ ($activeImportId ?? session('import_id')) ? '' : 'hidden' }}"
         style="margin-top:16px;padding-top:16px;border-top:1px solid var(--c-border);">
        <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:6px;">
            <p id="importStatusTextBody" style="font-size:12px;font-weight:600;color:var(--c-fg);">Memproses impor...</p>
            <span id="importPercentTextBody" style="font-size:12px;font-weight:700;color:var(--c-primary);font-variant-numeric:tabular-nums;">0%</span>
        </div>
        <div style="height:6px;background:var(--c-border);border-radius:9999px;overflow:hidden;">
            <div id="importProgressBarBody" style="height:100%;background:var(--c-primary);border-radius:9999px;width:0%;transition:width .5s ease;"></div>
        </div>
    </div>
</div>
