<!-- RPS Page Scripts Component -->
<script src="{{ asset('modules/banksoal/js/Banksoal/shared/RpsMultiselectHandler.js') }}"></script>
<script src="{{ asset('modules/banksoal/js/Banksoal/components/Dropdown.js') }}"></script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    try {
        const dropdownManager = new Dropdown();
        dropdownManager.initAll({
            '#mkSelect': 'Pilih Mata Kuliah',
            '#semester': 'Pilih Semester',
            '#tahun_ajaran': 'Pilih Tahun Ajaran',
        });

        // Initialize multiselect handler
        const rpsMultiselect = new RpsMultiselectHandler();
        rpsMultiselect.init();

        // For edit form, populate pre-selected data
        const rpsId = document.querySelector('form')?.dataset.rpsId;
        if (rpsId) {
            // Pre-populate CPMK from RPS ID
            rpsMultiselect.populateCpmkForEdit(rpsId);

            // Trigger MK change to load CPL and Dosen
            const mkSelect = document.getElementById('mkSelect');
            if (mkSelect && mkSelect.value) {
                mkSelect.dispatchEvent(new Event('change'));
            }
        }

        console.log('RPS Form initialized successfully');
    } catch (error) {
        console.error('Error during RPS form initialization:', error);
    }
});
</script>

@include('banksoal::partials.dosen.layout-scripts')
