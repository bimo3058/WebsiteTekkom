{{-- resources/views/superadmin/audit-logs/_header.blade.php --}}

<div style="display:flex; align-items:center; justify-content:space-between; gap:16px; flex-wrap:wrap;">
    <div>
        <h1 style="font-size:22px; font-weight:700; color:var(--c-fg); letter-spacing:-0.02em; line-height:1.2; margin:0;">Audit Log System</h1>
        <p style="font-size:12px; color:var(--c-fg-muted); margin-top:3px; margin-bottom:0;">
            Total
            <span style="color:var(--c-primary); font-weight:600;">{{ number_format($logs->total()) }}</span>
            aktivitas tercatat dalam sistem
        </p>
    </div>

    <button onclick="openBulkDeleteModal()"
            style="display:inline-flex; align-items:center; gap:7px; padding:8px 16px; background:#fff; border:1px solid var(--c-error-subtle); border-radius:8px; color:var(--c-error); font-size:12px; font-weight:600; cursor:pointer; font-family:inherit; transition:background .15s; box-shadow:0 1px 2px rgba(0,0,0,.04);"
            onmouseover="this.style.background='var(--c-error-subtle)'" onmouseout="this.style.background='#fff'">
        <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <path d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
        </svg>
        Hapus Massal
    </button>
</div>