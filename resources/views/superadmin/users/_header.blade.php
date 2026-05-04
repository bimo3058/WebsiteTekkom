{{-- resources/views/superadmin/users/_header.blade.php --}}

<nav style="display:flex; align-items:center; gap:6px; font-size:11px; color:var(--c-fg-muted); margin-bottom:16px;">
    <a href="{{ route('superadmin.dashboard') }}" style="color:var(--c-fg-muted); text-decoration:none; transition:color .15s;"
       onmouseover="this.style.color='var(--c-fg)'" onmouseout="this.style.color='var(--c-fg-muted)'">Dashboard</a>
    <svg width="12" height="12" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path d="M9 18l6-6-6-6"/></svg>
    <span style="color:var(--c-fg); font-weight:500;">User Management</span>
</nav>

<div style="display:flex; align-items:center; justify-content:space-between; margin-bottom:20px; gap:16px; flex-wrap:wrap;">
    <div>
        <h1 style="font-size:20px; font-weight:700; color:var(--c-fg); letter-spacing:-0.01em; line-height:1.2;">User Management</h1>
        <p style="font-size:12px; color:var(--c-fg-muted); margin-top:3px;">
            Total <span style="color:var(--c-primary); font-weight:600;">{{ $total }}</span> pengguna terdaftar
        </p>
    </div>
    <div style="display:flex; align-items:center; gap:8px;">

        {{-- Permissions --}}
        <a href="{{ route('superadmin.permissions') }}"
           style="display:inline-flex; align-items:center; gap:6px; padding:7px 13px; background:#fff; border:1px solid var(--c-border); border-radius:8px; font-size:12px; font-weight:600; color:var(--c-fg-sec); text-decoration:none; transition:background .15s;"
           onmouseover="this.style.background='var(--c-bg)'" onmouseout="this.style.background='#fff'">
            <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                <path d="M3.00059 7.59352C3.20646 13.6197 5.53308 19.0699 11.1059 20.8601C11.6866 21.0466 12.3134 21.0466 12.8941 20.8601C18.4669 19.0699 20.7935 13.6197 20.9994 7.59352C21.0169 7.08167 20.6467 6.65046 20.1578 6.55081C17.5104 6.01123 15.4106 4.85537 13.1163 3.3374C12.4363 2.88753 11.5637 2.88753 10.8837 3.3374C8.58942 4.85537 6.48962 6.01123 3.8422 6.55081C3.35327 6.65046 2.98311 7.08167 3.00059 7.59352Z"/>
            </svg>
            Permissions
        </a>

        {{-- Import CSV --}}
        <button onclick="openModal('modalImportUser')"
                style="display:inline-flex; align-items:center; gap:6px; padding:7px 13px; background:#fff; border:1px solid var(--c-border); border-radius:8px; font-size:12px; font-weight:600; color:var(--c-fg-sec); font-family:inherit; cursor:pointer; transition:background .15s;"
                onmouseover="this.style.background='var(--c-bg)'" onmouseout="this.style.background='#fff'">
            <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M12 14V6M12 6L14.5 8.5M12 6L9.5 8.5M4 18H20"/>
            </svg>
            Import CSV
        </button>

        {{-- Tambah User --}}
        <button onclick="openModal('modalAddUser')"
                style="display:inline-flex; align-items:center; gap:6px; padding:7px 13px; background:var(--c-primary); border:none; border-radius:8px; font-size:12px; font-weight:600; color:#fff; font-family:inherit; cursor:pointer; transition:background .15s;"
                onmouseover="this.style.background='var(--c-primary-hover)'" onmouseout="this.style.background='var(--c-primary)'">
            <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M21 11H18.5M16 11H18.5M18.5 11V8.5M18.5 11V13.5M8 14H12C14.7614 14 17 16.2386 17 19V20C17 20.5523 16.5523 21 16 21H4C3.44772 21 3 20.5523 3 20V19C3 16.2386 5.23858 14 8 14ZM14 7C14 9.20914 12.2091 11 10 11C7.79086 11 6 9.20914 6 7C6 4.79086 7.79086 3 10 3C12.2091 3 14 4.79086 14 7Z"/>
            </svg>
            Tambah User
        </button>
    </div>
</div>