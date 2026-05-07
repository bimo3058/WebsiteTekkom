{{-- resources/views/superadmin/dashboard/_import.blade.php --}}

<div style="display:flex; align-items:center; gap:10px; margin-bottom:12px;">
    <span style="font-size:11px; font-weight:700; text-transform:uppercase; letter-spacing:0.06em; color:var(--c-fg); white-space:nowrap;">Import User</span>
    <div style="flex:1; height:1px; background:var(--c-border);"></div>
</div>

<div style="background:#fff; border:1px solid var(--c-border); border-radius:14px; padding:16px 20px; margin-bottom:20px; box-shadow:0px 1px 2px rgba(228,229,231,0.24);">
    <div style="display:flex; align-items:center; justify-content:space-between; gap:16px; flex-wrap:wrap;">

        <div style="display:flex; align-items:center; gap:12px;">
            <div style="width:38px; height:38px; border-radius:10px; background:rgba(11,38,110,0.08); color:var(--c-primary); display:flex; align-items:center; justify-content:center; flex-shrink:0;">
                <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M12 14V6M12 6L14.5 8.5M12 6L9.5 8.5M4 18H20"/>
                </svg>
            </div>
            <div>
                <p style="font-size:13px; font-weight:600; color:var(--c-fg); line-height:1.3;">Import via CSV</p>
                <p style="font-size:11px; color:var(--c-fg-muted); margin-top:2px;">Tambahkan banyak user sekaligus dari file CSV</p>
            </div>
        </div>

        <div style="display:flex; align-items:center; gap:8px;">
            {{-- Kelola Users --}}
            <a href="{{ route('superadmin.users.index') }}"
               style="display:inline-flex; align-items:center; gap:6px; padding:7px 13px; background:#fff; border:1px solid var(--c-border); border-radius:8px; font-size:12px; font-weight:600; color:var(--c-fg-sec); text-decoration:none; transition:background .15s; white-space:nowrap;"
               onmouseover="this.style.background='var(--c-bg)'; this.style.borderColor='var(--c-border-strong)'"
               onmouseout="this.style.background='#fff'; this.style.borderColor='var(--c-border)'">
                <svg width="14" height="14" fill="none" viewBox="0 0 24 24">
                    <path fill-rule="evenodd" clip-rule="evenodd"
                        d="M6.1072 8.86495C6.4829 6.90273 7.81438 5.28 9.59848 4.49991C9.07975 3.60326 8.11027 3 6.99988 3C5.34303 3 3.99988 4.34315 3.99988 6C3.99988 7.34598 4.88629 8.48493 6.1072 8.86495ZM6.08454 11.0095C3.80371 11.159 2 13.0564 2 15.375C2 15.7202 2.27982 16 2.625 16H5.02185C5.60567 15.0881 6.41092 14.3317 7.36176 13.8064C6.71341 13.0173 6.2625 12.0598 6.08454 11.0095ZM18.9782 16H21.375C21.7202 16 22 15.7202 22 15.375C22 13.0563 20.1963 11.159 17.9154 11.0095C17.7375 12.0598 17.2866 13.0173 16.6383 13.8064C17.5891 14.3316 18.3944 15.0881 18.9782 16ZM17.8928 8.86489C19.1136 8.48481 19.9999 7.34591 19.9999 6C19.9999 4.34315 18.6567 3 16.9999 3C15.8895 3 14.9201 3.60322 14.4013 4.49984C16.1855 5.27989 17.517 6.90264 17.8928 8.86489Z"
                        fill="currentColor" stroke="none"/>
                    <path d="M12 13C13.6568 13 15 11.6569 15 10C15 8.34315 13.6568 7 12 7C10.3431 7 8.99998 8.34315 8.99998 10C8.99998 11.6569 10.3431 13 12 13Z" stroke="currentColor" stroke-width="1.8"/>
                    <path d="M16.5 20H7.50001C7.22386 20 6.99999 19.7761 7 19.5C7.00006 17.567 8.56708 16 10.5001 16H13.5001C15.4331 16 17.0001 17.567 17 19.5C17 19.7762 16.7761 20 16.5 20Z" stroke="currentColor" stroke-width="1.8"/>
                </svg>
                <span>Kelola Users</span>
            </a>

            {{-- Import --}}
            <button onclick="openModal('modalImportUser')"
                    style="display:inline-flex; align-items:center; gap:6px; padding:7px 13px; background:var(--c-primary); border:none; border-radius:8px; font-size:12px; font-weight:600; color:#fff; cursor:pointer; font-family:inherit; transition:background .15s; white-space:nowrap;"
                    onmouseover="this.style.background='var(--c-primary-hover)'"
                    onmouseout="this.style.background='var(--c-primary)'">
                <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M12 14V6M12 6L14.5 8.5M12 6L9.5 8.5M4 18H20"/>
                </svg>
                Import
            </button>
        </div>
    </div>

    {{-- Progress bar --}}
    <div id="importProgressBody"
         class="{{ ($activeImportId ?? session('import_id')) ? '' : 'hidden' }}"
         style="margin-top:16px; padding-top:16px; border-top:1px solid var(--c-border);">
        <div style="display:flex; align-items:center; justify-content:space-between; margin-bottom:6px;">
            <p id="importStatusTextBody" style="font-size:11px; font-weight:600; color:var(--c-fg);">Memproses impor...</p>
            <span id="importPercentTextBody" style="font-size:11px; font-weight:700; color:var(--c-primary); font-variant-numeric:tabular-nums;">0%</span>
        </div>
        <div style="height:6px; background:var(--c-border); border-radius:9999px; overflow:hidden;">
            <div id="importProgressBarBody"
                 style="height:100%; background:var(--c-primary); border-radius:9999px; width:0%; transition:width .5s ease;"></div>
        </div>
    </div>
</div>