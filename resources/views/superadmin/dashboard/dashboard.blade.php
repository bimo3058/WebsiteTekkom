{{-- resources/views/superadmin/dashboard/dashboard.blade.php --}}
<x-app-layout>
<x-sidebar :user="auth()->user()">

    <div class="min-h-screen bg-background font-sans">
        <div class="max-w-full px-7 py-7 pb-12">

            @include('superadmin.dashboard._header')
            @include('superadmin.dashboard._stats')
            @include('superadmin.dashboard._import')
            @include('superadmin.dashboard._modules')
            @include('superadmin.dashboard._activity')

        </div>
    </div>

    {{-- Modal Import di-extend dari user management --}}
    @include('superadmin.users._modal_import')

</x-sidebar>

<script>
// ── Modal ──────────────────────────────────────────────────────
function openModal(id) {
    const el = document.getElementById(id);
    if (el) { el.classList.remove('hidden'); document.body.style.overflow = 'hidden'; }
}
function closeModal(id) {
    const el = document.getElementById(id);
    if (el) { el.classList.add('hidden'); document.body.style.overflow = ''; }
}
document.addEventListener('keydown', e => { if (e.key === 'Escape') closeModal('modalImportUser'); });

// ── Progress helpers ───────────────────────────────────────────
let importTimer = null;

function showProgressUI(importId) {
    const container = document.getElementById('importProgressContainer');
    if (container) {
        container.setAttribute('data-import-id', importId);
        container.classList.remove('hidden');
        container.classList.add('flex');
    }
    const btnCancel = document.getElementById('btnCancelImportHeader');
    if (btnCancel) btnCancel.onclick = () => {
        if (typeof confirmCancelProgressModal === 'function') {
            confirmCancelProgressModal(importId);
        } else {
            cancelImport(importId);
        }
    };
    document.getElementById('importProgressBody')?.classList.remove('hidden');
}

function setProgress(pct) {
    const barH = document.getElementById('importProgressBar');
    const barB = document.getElementById('importProgressBarBody');
    const pctH = document.getElementById('importPercentText');
    const pctB = document.getElementById('importPercentTextBody');
    if (barH) barH.style.width = pct + '%';
    if (barB) barB.style.width = pct + '%';
    if (pctH) pctH.textContent = pct + '%';
    if (pctB) pctB.textContent = pct + '%';
}

function setStatus(html) {
    const h = document.getElementById('importStatusText');
    const b = document.getElementById('importStatusTextBody');
    if (h) h.innerHTML = html;
    if (b) b.innerHTML = html;
}

function setBarColor(color) {
    const cls = `h-full bg-${color}-500 transition-all duration-500 rounded-full`;
    const barH = document.getElementById('importProgressBar');
    const barB = document.getElementById('importProgressBarBody');
    if (barH) barH.className = cls;
    if (barB) barB.className = cls;
}

// ── Polling ────────────────────────────────────────────────────
function stopPolling() {
    if (importTimer) { clearInterval(importTimer); importTimer = null; }
}

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
                stopPolling();
                setProgress(100);
                setBarColor('emerald');
                setStatus('<span class="text-emerald-600 font-bold">Impor Berhasil Selesai!</span>');
                setTimeout(async () => {
                    await fetch('/superadmin/clear-import-session', { method: 'POST' });
                    await fetch('/superadmin/bust-stats-cache', {
                        method: 'POST',
                        headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '' }
                    });
                    window.location.reload();
                }, 1500);
            } else if (data.status === 'failed') {
                stopPolling();
                setBarColor('red');
                setStatus(`<span class="text-red-600 font-bold">❌ Gagal: ${data.error_message || 'Import gagal'}</span>`);
                setTimeout(() => {
                    fetch('/superadmin/clear-import-session', { method: 'POST' });
                    window.location.reload();
                }, 3000);
            }
        } catch (e) {
            console.error('Polling error:', e);
            stopPolling();
        }
    }, 2000);
}

async function cancelImport(importId) {
    if (!importId) return;
    try {
        const res = await fetch(`/superadmin/import-status/${importId}/cancel`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            }
        });
        if (res.ok) {
            stopPolling();
            setBarColor('red');
            setStatus('<span class="text-red-600 font-bold">Impor dibatalkan</span>');
            setTimeout(async () => {
                await fetch('/superadmin/clear-import-session', { method: 'POST' });
                await new Promise(resolve => setTimeout(resolve, 3000));
                await fetch('/superadmin/bust-stats-cache', {
                    method: 'POST',
                    headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
                });
                window.location.reload();
            }, 1500);
        }
    } catch (e) { console.error('Cancel error:', e); }
}

// ── Init ───────────────────────────────────────────────────────
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

        const btn            = document.getElementById('btnSubmitImport');
        const errorContainer = document.getElementById('importErrorContainer');
        const errorMessage   = document.getElementById('importErrorMessage');
        const dupeContainer  = document.getElementById('importDuplicateContainer');

        if (errorContainer) errorContainer.classList.add('hidden');
        if (dupeContainer)  dupeContainer.classList.add('hidden');

        btn.disabled  = true;
        btn.innerHTML = '<span class="animate-spin material-symbols-outlined" style="font-size:18px">sync</span> Memvalidasi...';

        try {
            const res  = await fetch(this.action, {
                method: 'POST',
                body: new FormData(this),
                headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' }
            });
            const data = await res.json();

            if (data.status === 'duplicate') {
                handleImportDuplicateResponse(data);
                isSubmitting = false;
                return;
            }

            if (!res.ok) throw new Error(data.message || 'Gagal memproses file.');

            if (data.import_id) {
                btn.innerHTML = '<span class="material-symbols-outlined">check_circle</span> Berhasil!';
                closeModal('modalImportUser');
                startPolling(data.import_id);
            } else if (data.status === 'success') {
                btn.innerHTML = '<span class="material-symbols-outlined">check_circle</span> Berhasil!';
                window.location.reload();
            }

        } catch (err) {
            if (errorMessage)   errorMessage.textContent = err.message;
            if (errorContainer) errorContainer.classList.remove('hidden');
            btn.disabled  = false;
            btn.innerHTML = '<span class="material-symbols-outlined" style="font-size:20px">upload</span> Mulai Impor';
            isSubmitting  = false;
        }
    });
});
</script>
</x-app-layout>