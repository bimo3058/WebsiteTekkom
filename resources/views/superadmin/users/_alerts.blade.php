{{-- resources/views/superadmin/users/_alerts.blade.php --}}
@php 
    $displayId = $activeImportId ?? session('import_id');
    if ($displayId && !$activeImportId) {
        $importExists = \App\Models\ImportStatus::where('id', $displayId)
            ->whereIn('status', ['pending', 'processing'])->exists();
        if (!$importExists) { $displayId = null; session()->forget('import_id'); }
    }
@endphp

{{-- Import Progress --}}
<div id="importProgressContainer"
     data-import-id="{{ $displayId ?? '' }}"
     style="{{ $displayId ? '' : 'display:none;' }} background:#fff; border:1px solid var(--c-border); border-radius:12px; overflow:hidden; margin-bottom:16px; box-shadow:0px 1px 2px rgba(228,229,231,0.24);">
    <div style="display:flex; align-items:center; justify-content:space-between; padding:10px 16px; background:var(--c-bg); border-bottom:1px solid var(--c-border);">
        <div style="display:flex; align-items:center; gap:10px;">
            <div style="width:28px; height:28px; border-radius:8px; background:rgba(11,38,110,0.08); display:flex; align-items:center; justify-content:center;">
                <svg id="importIcon" width="14" height="14" fill="none" stroke="var(--c-primary)" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round"
                     style="animation:spin 1s linear infinite;">
                    <path d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                </svg>
            </div>
            <div>
                <p id="importStatusText" style="font-size:10px; font-weight:700; text-transform:uppercase; letter-spacing:.08em; color:var(--c-fg);">Sinkronisasi Data...</p>
                <p style="font-size:9px; color:var(--c-fg-muted); font-style:italic;">Menghubungkan ke database...</p>
            </div>
        </div>
        <div style="display:flex; align-items:center; gap:12px;">
            <div style="text-align:right;">
                <span id="importPercentText" style="font-size:12px; font-weight:700; color:var(--c-primary); font-variant-numeric:tabular-nums;">0%</span>
                <span style="display:block; font-size:8px; font-weight:600; text-transform:uppercase; letter-spacing:.08em; color:var(--c-fg-placeholder);">Progress</span>
            </div>
            <button type="button" id="btnCancelImport" onclick="confirmCancelProgressModal('{{ $displayId }}')"
                    style="display:flex; align-items:center; gap:5px; padding:5px 10px; background:#fff; border:1px solid var(--c-border); border-radius:7px; color:var(--c-error); font-size:9px; font-weight:700; text-transform:uppercase; letter-spacing:.06em; cursor:pointer; font-family:inherit; transition:background .15s;"
                    onmouseover="this.style.background='var(--c-error-subtle)'" onmouseout="this.style.background='#fff'">
                <svg width="12" height="12" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5" stroke-linecap="round">
                    <path d="M18 6L6 18M6 6l12 12"/>
                </svg>
                Batal
            </button>
        </div>
    </div>
    <div style="padding:12px 16px; background:#fff;">
        <div style="width:100%; height:6px; background:var(--c-border); border-radius:9999px; overflow:hidden;">
            <div id="importProgressBar"
                 style="height:100%; background:var(--c-primary); border-radius:9999px; width:0%; transition:width 1s ease-out;"></div>
        </div>
    </div>
</div>

{{-- Validation Errors --}}
@if($errors->any())
<div style="background:#fff; border:1px solid var(--c-error-subtle); border-left:3px solid var(--c-error); border-radius:10px; margin-bottom:16px; overflow:hidden;">
    <div style="display:flex; align-items:center; gap:8px; padding:8px 14px; background:var(--c-error-subtle);">
        <svg width="14" height="14" fill="none" stroke="var(--c-error)" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round">
            <circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/>
        </svg>
        <span style="font-size:10px; font-weight:700; text-transform:uppercase; letter-spacing:.08em; color:var(--c-error);">Validation Error</span>
    </div>
    <ul style="padding:10px 14px 10px 28px; list-style:disc; margin:0;">
        @foreach($errors->all() as $error)
        <li style="font-size:11px; color:var(--c-error); font-weight:500; margin-bottom:2px;">{{ $error }}</li>
        @endforeach
    </ul>
</div>
@endif

{{-- Success --}}
@if(session('success') && !$displayId)
<div style="display:flex; align-items:center; justify-content:space-between; background:#fff; border:1px solid #9DE0D3; border-left:3px solid var(--c-success); border-radius:10px; padding:10px 14px; margin-bottom:16px;">
    <div style="display:flex; align-items:center; gap:10px;">
        <div style="width:26px; height:26px; border-radius:7px; background:var(--c-success-subtle); display:flex; align-items:center; justify-content:center; flex-shrink:0;">
            <svg width="13" height="13" fill="none" stroke="#287F6E" viewBox="0 0 24 24" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                <path d="M20 6L9 17l-5-5"/>
            </svg>
        </div>
        <span style="font-size:11px; font-weight:600; color:#287F6E;">{{ session('success') }}</span>
    </div>
    <button type="button" onclick="this.parentElement.remove()"
            style="background:none; border:none; cursor:pointer; color:var(--c-fg-muted); padding:2px; transition:color .15s;"
            onmouseover="this.style.color='var(--c-fg)'" onmouseout="this.style.color='var(--c-fg-muted)'">
        <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round">
            <path d="M18 6L6 18M6 6l12 12"/>
        </svg>
    </button>
</div>
@endif

<style>
@keyframes spin { to { transform: rotate(360deg); } }
</style>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const btnCancel = document.getElementById('btnCancelImport');
    if (btnCancel) {
        btnCancel.addEventListener('click', function () {
            const container = document.getElementById('importProgressContainer');
            const importId  = container?.getAttribute('data-import-id') || '';
            if (!importId) return;
            if (typeof confirmCancelProgressModal === 'function') confirmCancelProgressModal(importId);
            else if (typeof cancelImport === 'function') {
                if (confirm('Hentikan proses import?')) cancelImport(importId);
            }
        });
    }
});
</script>