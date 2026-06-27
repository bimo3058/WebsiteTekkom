@props([
    'previewRouteTemplate' => route('banksoal.rps.dosen.preview', ['rpsId' => '__RPS_ID__']),
])

<!-- Document Preview Tab Interceptor Component -->
<script>
(function() {
    function navigateToPreview(rpsId) {
        const template = '{{ $previewRouteTemplate }}' || '/bank-soal/rps/dosen/preview/__RPS_ID__';
        const previewRoute = template.replace('__RPS_ID__', encodeURIComponent(String(rpsId)));
        window.location.href = previewRoute;
    }

    document.addEventListener('click', function(event) {
        const previewButton = event.target.closest('.preview-dokumen-btn');
        if (previewButton) {
            event.preventDefault();
            navigateToPreview(previewButton.dataset.id || '');
        }
    });

    window.previewDokumen = function(rpsId) {
        navigateToPreview(rpsId);
    };
})();
</script>
