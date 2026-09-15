<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames(([
    'previewRouteTemplate' => route('banksoal.rps.dosen.preview', ['rpsId' => '__RPS_ID__']),
]));

foreach ($attributes->all() as $__key => $__value) {
    if (in_array($__key, $__propNames)) {
        $$__key = $$__key ?? $__value;
    } else {
        $__newAttributes[$__key] = $__value;
    }
}

$attributes = new \Illuminate\View\ComponentAttributeBag($__newAttributes);

unset($__propNames);
unset($__newAttributes);

foreach (array_filter(([
    'previewRouteTemplate' => route('banksoal.rps.dosen.preview', ['rpsId' => '__RPS_ID__']),
]), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars, $__key, $__value); ?>

<!-- Document Preview Tab Interceptor Component -->
<script>
(function() {
    function navigateToPreview(rpsId) {
        const template = '<?php echo e($previewRouteTemplate); ?>' || '/bank-soal/rps/dosen/preview/__RPS_ID__';
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
<?php /**PATH C:\WebsiteTekkom - Copy\Modules\BankSoal\resources\views\components\ui\dokumen-rps-modal.blade.php ENDPATH**/ ?>