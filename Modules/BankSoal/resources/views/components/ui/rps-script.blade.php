<!-- RPS Page Scripts Component -->
<script src="{{ asset('modules/banksoal/js/Banksoal/shared/RpsMultiselectHandler.js') }}"></script>
<script src="{{ asset('modules/banksoal/js/Banksoal/components/Dropdown.js') }}"></script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    try {
        const uploadForm = document.querySelector('#rpsUploadModal form[data-route-cpl]');
        if (!uploadForm) return;

        const safeInitDropdown = (selector, placeholder) => {
            const element = uploadForm.querySelector(selector);
            if (!element || element.tomselect) return;

            new TomSelect(element, {
                maxItems: 1,
                placeholder,
                allowEmptyOption: false,
                create: false,
            });
        };

        safeInitDropdown('#mkSelect', 'Pilih Mata Kuliah');
        safeInitDropdown('#semester', 'Pilih Semester');
        safeInitDropdown('#tahun_ajaran', 'Pilih Tahun Ajaran');

        // Initialize multiselect handler
        const rpsMultiselect = new RpsMultiselectHandler({
            rootElement: uploadForm,
            routeSourceElement: uploadForm,
        });
        rpsMultiselect.init();

        // Restore dependent fields if MK already selected due to old input.
        const mkSelect = uploadForm.querySelector('#mkSelect');
        if (mkSelect && mkSelect.value) {
            mkSelect.dispatchEvent(new Event('change', { bubbles: true }));
        }

        console.log('RPS Form initialized successfully');
    } catch (error) {
        console.error('Error during RPS form initialization:', error);
    }
});
</script>

@include('banksoal::partials.dosen.layout-scripts')
