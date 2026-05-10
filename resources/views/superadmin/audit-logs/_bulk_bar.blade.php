{{-- resources/views/superadmin/audit-logs/_bulk_bar.blade.php --}}
<div id="bulkActionBar"
     style="display:none; margin-bottom:20px; padding:12px 16px; background:#ffffff; border: 1.5px solid #E5E7EB; border-radius:12px; align-items:center; justify-content:space-between;">

    <div style="display:flex; align-items:center; gap:12px;">
        <!-- Icon Box dengan warna Navy -->
        <div style="width:30px; height:30px; border-radius:8px; background:navy; display:flex; align-items:center; justify-content:center; flex-shrink:0;">
            <svg width="14" height="14" fill="none" stroke="#ffffff" viewBox="0 0 24 24" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                <path d="M20 6L9 17l-5-5"/>
            </svg>
        </div>
        
        <span style="font-size:13px; font-weight:500; color:#1F2937;">
            <span id="selectedCount" style="font-size:15px; font-weight:700; color:navy;">0</span>
            <span style="color:#6B7280; margin-left:4px;">Logs Terpilih</span>
        </span>
    </div>

    <div style="display:flex; align-items:center; gap:10px;">
        <!-- Tombol Hapus (Outline Style) -->
        <button onclick="openBulkDeleteModal()"
                style="display:flex; align-items:center; gap:6px; background:transparent; border:1.5px solid #dc2626; color:#dc2626; font-size:11px; font-weight:700; text-transform:uppercase; letter-spacing:0.08em; padding:7px 15px; border-radius:8px; cursor:pointer; font-family:inherit; transition:all .15s;"
                onmouseover="this.style.background='#dc2626'; this.style.color='#fff'" 
                onmouseout="this.style.background='transparent'; this.style.color='#dc2626'">
            <svg width="13" height="13" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
            </svg>
            Hapus
        </button>
        
        <div style="width:1px; height:18px; background:#E5E7EB;"></div>
        
        <!-- Tombol Batal -->
        <button onclick="deselectAll()"
                style="font-size:11px; font-weight:600; text-transform:uppercase; letter-spacing:0.06em; color:#6B7280; background:none; border:none; cursor:pointer; font-family:inherit; padding:4px 8px; transition:color .15s;"
                onmouseover="this.style.color='navy'" onmouseout="this.style.color='#6B7280'">
            Batal
        </button>
    </div>
</div>