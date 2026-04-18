<!-- RPS Page Scripts Component -->
<script src="{{ asset('modules/banksoal/js/Banksoal/components/Dropdown.js') }}"></script>
<script src="{{ asset('modules/banksoal/js/Banksoal/components/MultiSelect.js') }}"></script>
<script src="{{ asset('modules/banksoal/js/Banksoal/components/RpsForm.js') }}"></script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    try {
        const dropdownManager = new Dropdown();
        dropdownManager.initAll({
            '#mkSelect': 'Pilih Mata Kuliah',
            '#semester': 'Pilih Semester',
            '#tahun_ajaran': 'Pilih Tahun Ajaran',
        });

        const dosenMs = new MultiSelect(document.getElementById('dosenMs'), { maxWidth: null, keepOpen: true });
        const cplMs = new MultiSelect(document.getElementById('cplMs'), { maxWidth: null, keepOpen: true });
        const cpmkMs = new MultiSelect(document.getElementById('cpmkMs'), { maxWidth: null, keepOpen: true });

        const rpsForm = new RpsFormComponent();
        rpsForm.init({ dosenMs, cplMs, cpmkMs });
        console.log('RPS Form initialized successfully');
    } catch (error) {
        console.error('Error during RPS form initialization:', error);
    }
});
</script>

@include('banksoal::partials.dosen.layout-scripts')
