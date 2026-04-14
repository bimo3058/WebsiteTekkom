@props([
    'previewRouteTemplate' => route('banksoal.rps.dosen.preview', ['rpsId' => '__RPS_ID__']),
])

<!-- Document Preview Modal Component -->
<div id="dokumenModal" data-preview-route-template="{{ $previewRouteTemplate }}" class="fixed inset-0 bg-black/70 z-50 items-center justify-center hidden">
    <div class="bg-white rounded-lg w-11/12 max-w-4xl h-[88vh] flex flex-col overflow-hidden shadow-2xl">
        <!-- Modal Header -->
        <div class="px-6 py-4 border-b border-slate-200 bg-slate-50 flex items-center justify-between flex-shrink-0">
            <h3 id="modalTitle" class="text-lg font-semibold text-slate-900">Preview Dokumen</h3>
            <button type="button" data-dokumen-close class="text-slate-400 hover:text-slate-600 text-2xl w-8 h-8 flex items-center justify-center rounded hover:bg-slate-100 transition">×</button>
        </div>

        <!-- Modal Content -->
        <div class="flex-1 overflow-hidden min-h-0">
            <iframe id="dokumenFrame" class="w-full h-full border-none"></iframe>
        </div>

        <!-- Modal Footer -->
        <div class="px-6 py-3 border-t border-slate-200 bg-slate-50 flex justify-end flex-shrink-0">
            <button type="button" data-dokumen-close class="btn-primary">Tutup</button>
        </div>
    </div>
</div>

<script>
(function() {
    const modal = document.getElementById('dokumenModal');
    const frame = document.getElementById('dokumenFrame');
    const title = document.getElementById('modalTitle');

    function openDokumenModal(rpsId, titleText) {
        if (!modal || !frame || !title) return;
        title.textContent = 'Preview: ' + titleText;
        const template = modal.dataset.previewRouteTemplate || '/bank-soal/rps/dosen/preview/__RPS_ID__';
        const previewRoute = template.replace('__RPS_ID__', encodeURIComponent(String(rpsId)));
        frame.src = previewRoute;
        modal.classList.add('flex');
        modal.classList.remove('hidden');
    }

    function closeDokumenModal() {
        if (!modal || !frame) return;
        modal.classList.add('hidden');
        modal.classList.remove('flex');
        frame.src = '';
    }

    document.addEventListener('click', function(event) {
        const previewButton = event.target.closest('.preview-dokumen-btn');
        if (previewButton) {
            event.preventDefault();
            openDokumenModal(previewButton.dataset.id || '', previewButton.dataset.title || 'Dokumen');
            return;
        }

        if (event.target.matches('[data-dokumen-close]')) {
            closeDokumenModal();
            return;
        }

        if (event.target === modal) {
            closeDokumenModal();
        }
    });

    document.addEventListener('keydown', function(event) {
        if (event.key === 'Escape' && modal && !modal.classList.contains('hidden')) {
            closeDokumenModal();
        }
    });

    window.previewDokumen = function(rpsId, mkNama) {
        openDokumenModal(rpsId, mkNama);
    };
    window.closeDokumenModal = closeDokumenModal;
})();
</script>
