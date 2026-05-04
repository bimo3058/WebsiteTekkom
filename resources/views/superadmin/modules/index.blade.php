{{-- resources/views/superadmin/modules/index.blade.php --}}
<x-app-layout>
<x-sidebar :user="auth()->user()">

<div style="min-height:100vh; background:var(--c-bg); font-family:var(--font-sans);">
<div style="max-width:100%; padding:24px 24px 56px;">

    {{-- Breadcrumb --}}
    <nav style="display:flex; align-items:center; gap:6px; font-size:11px; color:var(--c-fg-muted); margin-bottom:16px;">
        <a href="{{ route('superadmin.dashboard') }}" style="color:var(--c-fg-muted); text-decoration:none; transition:color .15s;"
           onmouseover="this.style.color='var(--c-fg)'" onmouseout="this.style.color='var(--c-fg-muted)'">Dashboard</a>
        <svg width="12" height="12" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path d="M9 18l6-6-6-6"/></svg>
        <span style="color:var(--c-fg); font-weight:500;">System Modules</span>
    </nav>

    {{-- Header --}}
    <div style="display:flex; align-items:flex-start; justify-content:space-between; gap:16px; margin-bottom:24px; flex-wrap:wrap;">
        <div>
            <h1 style="font-size:20px; font-weight:700; color:var(--c-fg); letter-spacing:-0.01em; line-height:1.2;">System Modules</h1>
            <p style="font-size:12px; color:var(--c-fg-muted); margin-top:3px;">
                Total <span style="color:var(--c-primary); font-weight:600;">{{ $modules->count() }}</span> modul terintegrasi dalam ekosistem
            </p>
        </div>
        <a href="{{ route('superadmin.dashboard') }}"
           style="display:inline-flex; align-items:center; gap:6px; padding:7px 13px; background:#fff; border:1px solid var(--c-border); border-radius:8px; font-size:12px; font-weight:600; color:var(--c-fg-sec); text-decoration:none; transition:background .15s;"
           onmouseover="this.style.background='var(--c-bg)'" onmouseout="this.style.background='#fff'">
            <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round"><path d="M19 12H5M5 12l7 7M5 12l7-7"/></svg>
            Dashboard
        </a>
    </div>

    {{-- Module Grid --}}
    <div style="display:grid; grid-template-columns:repeat(2,1fr); gap:12px;">
        @foreach($modules as $module)
            @include('superadmin.modules._card', ['module' => $module])
        @endforeach
    </div>

</div>
</div>

{{-- Modals --}}
@foreach($modules as $module)
    @include('superadmin.modules._modal_manage', ['module' => $module])
@endforeach

<style>
@media (min-width: 1024px) { .mod-grid { grid-template-columns: repeat(4, 1fr) !important; } }
</style>

<script>
function openModal(id) {
    const m = document.getElementById(id);
    if (m) { m.style.display = 'flex'; document.body.style.overflow = 'hidden'; }
}
function closeModal(id) {
    const m = document.getElementById(id);
    if (m) { m.style.display = 'none'; document.body.style.overflow = ''; }
}
document.addEventListener('keydown', e => { if (e.key === 'Escape') document.querySelectorAll('[id^="modal-"]').forEach(m => { m.style.display = 'none'; }); document.body.style.overflow = ''; });
</script>

</x-sidebar>
</x-app-layout>