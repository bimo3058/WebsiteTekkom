{{-- resources/views/superadmin/dashboard/dashboard.blade.php --}}
<x-app-layout>
<x-sidebar :user="auth()->user()">

    {{-- Style Box Wrap khas SITKOM untuk Dashboard --}}
    <style>
        /* Hilangkan padding default agar wrap bisa full 100vh */
        .sitkom-content { padding: 0 !important; display: flex; flex-direction: column; flex: 1; overflow: hidden; }

        /* Container luar */
        .dash-wrap {
            display: flex; flex-direction: column; height: calc(100vh - 60px);
            padding: 10px; box-sizing: border-box; font-family: 'Inter Tight', sans-serif;
        }

        /* Kotak utama (Box) */
        .dash-box {
            display: flex; flex-direction: column; flex: 1; min-height: 0;
            background: #fff; border: 1px solid var(--c-border);
            border-radius: 12px; box-shadow: 0 1px 3px rgba(0,0,0,0.06);
            overflow: hidden; width: 100%; box-sizing: border-box;
        }

        /* Area Header Box (Fixed di atas kotak) */
        .dash-box-header {
            background: #fff;
            border-bottom: 1px solid var(--c-border);
            flex-shrink: 0; width: 100%; box-sizing: border-box;
            padding: 16px 24px;
        }

        /* Area Konten Box (Scrollable) */
        .dash-box-body {
            flex: 1; overflow-y: auto; padding: 20px 24px;
            display: flex; flex-direction: column; gap: 2px;
        }

        .dash-box-body > * {
            flex-shrink: 0; /* Mencegah elemen (terutama chart) menciut/gepeng */
            width: 100%;
            min-width: 0;   /* Penting agar Chart.js bisa kalkulasi lebar dengan benar */
        }

        /* Opsional: Percantik scrollbar agar senada dengan desain SITKOM kamu */
        .dash-box-body::-webkit-scrollbar { width: 6px; }
        .dash-box-body::-webkit-scrollbar-thumb {
            background: var(--c-border-strong);
            border-radius: 10px;
        }

        /* ── Mobile: scroll natively, bukan inner-scroll ── */
        @media (max-width: 767px) {
            .sitkom-content {
                padding: 8px 8px 80px !important;
                display: block !important;
                overflow: visible !important;
            }
            .dash-wrap {
                height: auto !important;
                min-height: 0 !important;
                padding: 0;
            }
            .dash-box {
                flex: none !important;
                min-height: 0 !important;
                overflow: visible !important;
                border-radius: 10px;
            }
            .dash-box-header {
                padding: 12px 14px;
                position: sticky;
                top: 52px; /* tinggi topbar mobile */
                z-index: 10;
            }
            .dash-box-body {
                overflow-y: visible !important;
                flex: none !important;
                padding: 14px;
            }
        }
    </style>

    <div class="dash-wrap">
        <div class="dash-box">
            
            {{-- Area Header (Diam) --}}
            <div class="dash-box-header">
                @include('superadmin.dashboard._header')
            </div>

            {{-- Area Konten (Bisa di-scroll) --}}
            <div class="dash-box-body">
                @include('superadmin.dashboard._stats')
                @include('superadmin.dashboard._chart')
                @include('superadmin.dashboard._import')
                @include('superadmin.dashboard._modules')
                @include('superadmin.dashboard._activity')
            </div>

        </div>
    </div>

{{-- Modals diletakkan di luar wrap agar overlay-nya menutupi layar penuh --}}
@include('superadmin.users._modal_import')

<script>
function openModal(id) {
    const el = document.getElementById(id);
    if (el) { el.style.display = 'flex'; document.body.style.overflow = 'hidden'; }
}
function closeModal(id) {
    const el = document.getElementById(id);
    if (el) { el.style.display = 'none'; document.body.style.overflow = ''; }
}
document.addEventListener('keydown', e => { if (e.key === 'Escape') closeModal('modalImportUser'); });

let importTimer = null;

function showProgressUI(importId) {
    const container = document.getElementById('importProgressContainer');
    if (container) { container.setAttribute('data-import-id', importId); container.style.display = 'flex'; }
    const btnCancel = document.getElementById('btnCancelImportHeader');
    if (btnCancel) btnCancel.onclick = () => {
        if (typeof confirmCancelProgressModal === 'function') confirmCancelProgressModal(importId);
        else cancelImport(importId);
    };
    document.getElementById('importProgressBody')?.classList.remove('hidden');
}

function setProgress(pct) {
    ['importProgressBar','importProgressBarBody'].forEach(id => { const el = document.getElementById(id); if(el) el.style.width = pct + '%'; });
    ['importPercentText','importPercentTextBody'].forEach(id => { const el = document.getElementById(id); if(el) el.textContent = pct + '%'; });
}

function setStatus(html) {
    ['importStatusText','importStatusTextBody'].forEach(id => { const el = document.getElementById(id); if(el) el.innerHTML = html; });
}

function setBarColor(color) {
    const colors = { emerald: 'var(--c-success)', red: 'var(--c-error)', primary: 'var(--c-primary)' };
    const c = colors[color] || color;
    ['importProgressBar','importProgressBarBody'].forEach(id => { const el = document.getElementById(id); if(el) el.style.background = c; });
}

function stopPolling() { if (importTimer) { clearInterval(importTimer); importTimer = null; } }

function startPolling(importId) {
    if (!importId || importId === 'null' || importId === '') return;
    if (importTimer) clearInterval(importTimer);
    showProgressUI(importId);
    importTimer = setInterval(async () => {
        try {
            const res = await fetch(`/superadmin/import-status/${importId}`);
            const ct  = res.headers.get('content-type');
            if (!ct?.includes('application/json')) { stopPolling(); return; }
            if (res.status === 401 || res.status === 403) { window.location.href = '/login'; return; }
            if (!res.ok) { stopPolling(); return; }
            const data = await res.json();
            if (!data || typeof data !== 'object') { stopPolling(); return; }
            const pct = data.total > 0 ? Math.round((data.processed / data.total) * 100) : 0;
            setProgress(pct);
            if (data.status === 'processing') {
                setStatus(`Memproses: ${data.processed} / ${data.total} user...`);
            } else if (data.status === 'completed') {
                stopPolling(); setProgress(100); setBarColor('emerald');
                setStatus('<span style="color:#287F6E;font-weight:700;">✓ Impor Berhasil Selesai!</span>');
                setTimeout(async () => {
                    await fetch('/superadmin/clear-import-session', { method: 'POST' });
                    await fetch('/superadmin/bust-stats-cache', { method: 'POST', headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '' } });
                    window.location.reload();
                }, 1500);
            } else if (data.status === 'failed') {
                stopPolling(); setBarColor('red');
                setStatus(`<span style="color:var(--c-error);font-weight:700;">✕ Gagal: ${data.error_message || 'Import gagal'}</span>`);
                setTimeout(() => { fetch('/superadmin/clear-import-session', { method: 'POST' }); window.location.reload(); }, 3000);
            }
        } catch (e) { console.error('Polling error:', e); stopPolling(); }
    }, 2000);
}

async function cancelImport(importId) {
    if (!importId) return;
    try {
        const res = await fetch(`/superadmin/import-status/${importId}/cancel`, {
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Content-Type': 'application/json', 'Accept': 'application/json' }
        });
        if (res.ok) {
            stopPolling(); setBarColor('red');
            setStatus('<span style="color:var(--c-error);font-weight:700;">Impor dibatalkan</span>');
            setTimeout(async () => {
                await fetch('/superadmin/clear-import-session', { method: 'POST' });
                await new Promise(r => setTimeout(r, 3000));
                await fetch('/superadmin/bust-stats-cache', { method: 'POST', headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' } });
                window.location.reload();
            }, 1500);
        }
    } catch (e) { console.error('Cancel error:', e); }
}

document.addEventListener('DOMContentLoaded', function () {
    const container = document.getElementById('importProgressContainer');
    const sessionId = container?.getAttribute('data-import-id') || '';
    if (sessionId && sessionId !== '') startPolling(sessionId);

    const importForm = document.getElementById('formImportUser');
    if (!importForm) return;
    let isSubmitting = false;

    importForm.addEventListener('submit', async function (e) {
        e.preventDefault();
        if (isSubmitting) return;
        isSubmitting = true;
        const btn = document.getElementById('btnSubmitImport');
        const errorContainer = document.getElementById('importErrorContainer');
        const dupeContainer  = document.getElementById('importDuplicateContainer');
        if (errorContainer) errorContainer.classList.add('hidden');
        if (dupeContainer)  dupeContainer.classList.add('hidden');
        btn.disabled  = true;
        btn.innerHTML = '⟳ Memvalidasi...';
        try {
            const res  = await fetch(this.action, { method: 'POST', body: new FormData(this), headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' } });
            const data = await res.json();
            if (data.status === 'duplicate') { handleImportDuplicateResponse(data); isSubmitting = false; return; }
            if (!res.ok) throw new Error(data.message || 'Gagal memproses file.');
            if (data.import_id) { btn.innerHTML = '✓ Berhasil!'; closeModal('modalImportUser'); startPolling(data.import_id); }
            else if (data.status === 'success') { btn.innerHTML = '✓ Berhasil!'; window.location.reload(); }
        } catch (err) {
            const errorMessage = document.getElementById('importErrorMessage');
            if (errorMessage) errorMessage.textContent = err.message;
            if (errorContainer) errorContainer.classList.remove('hidden');
            btn.disabled  = false;
            btn.innerHTML = '↑ Mulai Impor';
            isSubmitting  = false;
        }
    });
});
</script>

</x-sidebar>
</x-app-layout>