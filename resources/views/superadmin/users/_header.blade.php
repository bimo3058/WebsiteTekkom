{{-- resources/views/superadmin/users/_header.blade.php --}}
{{-- Breadcrumb sudah dihandle oleh topbar di sidebar.blade.php --}}

<div style="display:flex; align-items:center; justify-content:space-between; gap:16px; flex-wrap:wrap;">
    <div>
        <h1 style="font-size:22px; font-weight:700; color:var(--c-fg); letter-spacing:-0.02em; line-height:1.2; margin:0;">User Management</h1>
        <p style="font-size:12px; color:var(--c-fg-muted); margin-top:3px; margin-bottom:0;">
            Total <span style="color:var(--c-primary); font-weight:600;">{{ number_format($total) }}</span> user terdaftar dalam sistem
        </p>
    </div>

    <div style="display:flex; align-items:center; gap:8px;">

        {{-- Import Users --}}
        <button onclick="openModal('modalImportUser')"
                style="display:inline-flex; align-items:center; gap:6px; padding:8px 14px; background:#fff; border:1px solid var(--c-border); border-radius:8px; font-size:12px; font-weight:600; color:var(--c-fg-sec); font-family:inherit; cursor:pointer; transition:all .15s; box-shadow:0 1px 2px rgba(0,0,0,.04);"
                onmouseover="this.style.background='var(--c-bg)'; this.style.borderColor='var(--c-border-strong)'"
                onmouseout="this.style.background='#fff'; this.style.borderColor='var(--c-border)'">
            <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4M7 10l5 5 5-5M12 15V3"/>
            </svg>
            Import Users
        </button>

        {{-- Tambah User --}}
        <button onclick="openModal('modalAddUser')"
                style="display:inline-flex; align-items:center; gap:6px; padding:8px 16px; background:var(--c-primary); border:none; border-radius:8px; font-size:12px; font-weight:600; color:#fff; font-family:inherit; cursor:pointer; transition:all .15s; box-shadow:0 2px 6px rgba(94,83,244,0.3);"
                onmouseover="this.style.background='var(--c-primary-hover)'; this.style.boxShadow='0 4px 12px rgba(94,83,244,0.4)'"
                onmouseout="this.style.background='var(--c-primary)'; this.style.boxShadow='0 2px 6px rgba(94,83,244,0.3)'">
            <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M21 11H18.5M16 11H18.5M18.5 11V8.5M18.5 11V13.5M8 14H12C14.7614 14 17 16.2386 17 19V20C17 20.5523 16.5523 21 16 21H4C3.44772 21 3 20.5523 3 20V19C3 16.2386 5.23858 14 8 14ZM14 7C14 9.20914 12.2091 11 10 11C7.79086 11 6 9.20914 6 7C6 4.79086 7.79086 3 10 3C12.2091 3 14 4.79086 14 7Z"/>
            </svg>
            Tambah User
        </button>
    </div>
</div>