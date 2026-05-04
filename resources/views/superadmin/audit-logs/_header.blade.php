{{-- resources/views/superadmin/audit-logs/_header.blade.php --}}

<nav style="display:flex; align-items:center; gap:6px; font-size:11px; color:var(--c-fg-muted); margin-bottom:16px;">
    <a href="{{ route('superadmin.dashboard') }}" style="color:var(--c-fg-muted); text-decoration:none; transition:color .15s;"
       onmouseover="this.style.color='var(--c-fg)'" onmouseout="this.style.color='var(--c-fg-muted)'">Dashboard</a>
    <svg width="12" height="12" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path d="M9 18l6-6-6-6"/></svg>
    <span style="color:var(--c-fg); font-weight:500;">Audit Logs</span>
</nav>

<div style="display:flex; align-items:flex-start; justify-content:space-between; gap:16px; margin-bottom:24px; flex-wrap:wrap;">
    <div>
        <h1 style="font-size:20px; font-weight:700; color:var(--c-fg); letter-spacing:-0.01em; line-height:1.2;">Audit Log System</h1>
        <p style="font-size:12px; color:var(--c-fg-muted); margin-top:3px;">
            Total
            <span style="color:var(--c-primary); font-weight:600;">{{ number_format($logs->total()) }}</span>
            aktivitas tercatat dalam sistem
        </p>
    </div>

    <button onclick="openBulkDeleteModal()"
            style="display:inline-flex; align-items:center; gap:7px; padding:8px 16px; background:#fff; border:1px solid var(--c-error-subtle); border-radius:8px; color:var(--c-error); font-size:11px; font-weight:700; text-transform:uppercase; letter-spacing:0.08em; cursor:pointer; font-family:inherit; transition:background .15s;"
            onmouseover="this.style.background='var(--c-error-subtle)'" onmouseout="this.style.background='#fff'">
        <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <path d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
        </svg>
        Hapus Massal
    </button>
</div>