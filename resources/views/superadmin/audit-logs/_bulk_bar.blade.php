{{-- resources/views/superadmin/audit-logs/_bulk_bar.blade.php --}}
<div id="bulkActionBar"
     style="display:none; margin-bottom:20px; padding:12px 16px; background:var(--c-fg); border-radius:12px; align-items:center; justify-content:space-between; box-shadow:0 4px 16px rgba(13,13,18,0.2);">

    <div style="display:flex; align-items:center; gap:12px;">
        <div style="width:30px; height:30px; border-radius:8px; background:var(--c-primary); display:flex; align-items:center; justify-content:center; flex-shrink:0;">
            <svg width="14" height="14" fill="none" stroke="#fff" viewBox="0 0 24 24" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                <path d="M20 6L9 17l-5-5"/>
            </svg>
        </div>
        <span style="font-size:13px; font-weight:500; color:#fff;">
            <span id="selectedCount" style="font-size:15px; font-weight:700; color:var(--c-primary-border);">0</span>
            <span style="color:#A4ABB8; margin-left:4px;">Logs Selected</span>
        </span>
    </div>

    <div style="display:flex; align-items:center; gap:10px;">
        <button onclick="openBulkDeleteModal()"
                style="display:flex; align-items:center; gap:6px; background:var(--c-error); border:none; color:#fff; font-size:11px; font-weight:700; text-transform:uppercase; letter-spacing:0.08em; padding:8px 16px; border-radius:8px; cursor:pointer; font-family:inherit; transition:background .15s;"
                onmouseover="this.style.background='#95122B'" onmouseout="this.style.background='var(--c-error)'">
            <svg width="13" height="13" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
            </svg>
            Hapus Terpilih
        </button>
        <div style="width:1px; height:18px; background:#353849;"></div>
        <button onclick="deselectAll()"
                style="font-size:11px; font-weight:600; text-transform:uppercase; letter-spacing:0.06em; color:#666D80; background:none; border:none; cursor:pointer; font-family:inherit; padding:4px 8px; transition:color .15s;"
                onmouseover="this.style.color='#fff'" onmouseout="this.style.color='#666D80'">
            Batal
        </button>
    </div>
</div>