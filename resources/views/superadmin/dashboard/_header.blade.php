{{-- resources/views/superadmin/dashboard/_header.blade.php --}}

<nav style="display:flex; align-items:center; gap:6px; font-size:11px; color:var(--c-fg-muted); margin-bottom:16px;">
    <a href="/" style="color:var(--c-fg-muted); text-decoration:none; transition:color .15s;"
       onmouseover="this.style.color='var(--c-fg)'" onmouseout="this.style.color='var(--c-fg-muted)'">Home</a>
    <svg width="12" height="12" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path d="M9 18l6-6-6-6"/></svg>
    <span style="color:var(--c-fg); font-weight:500;">Dashboard</span>
</nav>

<div style="display:flex; align-items:flex-start; justify-content:space-between; gap:16px; margin-bottom:24px; flex-wrap:wrap;">

    {{-- Left: title + import badge --}}
    <div style="display:flex; align-items:center; gap:12px; flex-wrap:wrap;">
        <div>
            <h1 style="font-size:20px; font-weight:700; color:var(--c-fg); letter-spacing:-0.01em; line-height:1.2;">Superadmin Dashboard</h1>
            <p style="font-size:12px; color:var(--c-fg-muted); margin-top:3px;">
                Selamat datang, <span style="color:var(--c-fg); font-weight:600;">{{ auth()->user()->name }}</span>
            </p>
        </div>

        {{-- Import progress badge (compact) --}}
        <div id="importProgressContainer"
             data-import-id="{{ $activeImportId ?? session('import_id') ?? '' }}"
             style="display:none; align-items:center; gap:8px; background:#fff; border:1px solid var(--c-border); border-radius:8px; padding:6px 12px;">
            <svg style="width:14px;height:14px;color:var(--c-primary);animation:spin 1s linear infinite;flex-shrink:0;"
                 fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round">
                <path d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
            </svg>
            <span id="importPercentText" style="font-size:11px; font-weight:700; color:var(--c-primary); font-variant-numeric:tabular-nums;">0%</span>
            <button type="button" id="btnCancelImportHeader" title="Batalkan impor"
                    style="background:none; border:none; cursor:pointer; color:var(--c-fg-muted); display:flex; align-items:center; padding:0; transition:color .15s;"
                    onmouseover="this.style.color='var(--c-error)'" onmouseout="this.style.color='var(--c-fg-muted)'">
                <svg width="12" height="12" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5" stroke-linecap="round">
                    <path d="M18 6L6 18M6 6l12 12"/>
                </svg>
            </button>
        </div>
    </div>

    {{-- Right: action buttons --}}
    <div style="display:flex; gap:8px; align-items:center; flex-wrap:wrap;">

        {{-- Audit Logs --}}
        <a href="{{ route('superadmin.audit-logs') }}"
           style="display:inline-flex; align-items:center; gap:6px; padding:7px 13px; background:#fff; border:1px solid var(--c-border); border-radius:8px; font-size:12px; font-weight:600; color:var(--c-fg-sec); text-decoration:none; transition:background .15s; white-space:nowrap;"
           onmouseover="this.style.background='var(--c-bg)'; this.style.borderColor='var(--c-border-strong)'"
           onmouseout="this.style.background='#fff'; this.style.borderColor='var(--c-border)'">
            <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round">
                <path d="M12 4H6C4.34315 4 3 5.34315 3 7V18C3 19.6569 4.34315 21 6 21H17C18.6569 21 20 19.6569 20 18V12M7 17H12M7 13H15M21 5.5C21 6.88071 19.8807 8 18.5 8C17.1193 8 16 6.88071 16 5.5C16 4.11929 17.1193 3 18.5 3C19.8807 3 21 4.11929 21 5.5Z"/>
            </svg>
            <span>Audit Logs</span>
        </a>

        {{-- Permissions --}}
        <a href="{{ route('superadmin.permissions') }}"
           style="display:inline-flex; align-items:center; gap:6px; padding:7px 13px; background:var(--c-primary); border:1px solid var(--c-primary); border-radius:8px; font-size:12px; font-weight:600; color:#fff; text-decoration:none; transition:background .15s; white-space:nowrap;"
           onmouseover="this.style.background='var(--c-primary-hover)'"
           onmouseout="this.style.background='var(--c-primary)'">
            <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" stroke-linejoin="round">
                <path d="M3.00059 7.59352C3.20646 13.6197 5.53308 19.0699 11.1059 20.8601C11.6866 21.0466 12.3134 21.0466 12.8941 20.8601C18.4669 19.0699 20.7935 13.6197 20.9994 7.59352C21.0169 7.08167 20.6467 6.65046 20.1578 6.55081C17.5104 6.01123 15.4106 4.85537 13.1163 3.3374C12.4363 2.88753 11.5637 2.88753 10.8837 3.3374C8.58942 4.85537 6.48962 6.01123 3.8422 6.55081C3.35327 6.65046 2.98311 7.08167 3.00059 7.59352Z"/>
            </svg>
            <span>Permissions</span>
        </a>
    </div>
</div>

<style>
@keyframes spin { to { transform: rotate(360deg); } }
</style>