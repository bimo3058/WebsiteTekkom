/**
 * Alpine.js Components for BankSoal Module
 *
 * This file is loaded AFTER @livewireScripts in the layout.
 * Livewire v4 exposes window.Alpine after initializing it.
 * We register components here using window.Alpine directly.
 */

function registerBankSoalAlpineComponents() {
    if (!window.Alpine) return;

    /**
     * periodeManagerApp — halaman Manajemen Periode Ujian
     * Data di-pass via <script id="periode-init-data" type="application/json">
     */
    window.Alpine.data('periodeManagerApp', () => {
        const initEl = document.getElementById('periode-init-data');
        const init   = initEl ? JSON.parse(initEl.textContent) : {};

        return {
            openModal:     init.openModal     ?? false,
            editModal:     init.editModal     ?? false,
            editData:      init.editData      ?? {
                id: null, nama_periode: '',
                tanggal_mulai: '', tanggal_selesai: '',
                tanggal_mulai_ujian: '', tanggal_selesai_ujian: '',
                kuota_peserta: ''
            },
            createOptions: init.createOptions ?? [''],
            editOptions:   [''],

            openEdit(periodeData) {
                this.editData = {
                    id:                    periodeData.id,
                    nama_periode:          periodeData.nama_periode,
                    tanggal_mulai:         periodeData.tanggal_mulai,
                    tanggal_selesai:       periodeData.tanggal_selesai,
                    tanggal_mulai_ujian:   periodeData.tanggal_mulai_ujian,
                    tanggal_selesai_ujian: periodeData.tanggal_selesai_ujian,
                    kuota_peserta:         periodeData.kuota_peserta,
                };
                this.editOptions = (
                    periodeData.target_wisuda_options &&
                    periodeData.target_wisuda_options.length > 0
                )
                    ? [...periodeData.target_wisuda_options]
                    : [''];
                this.editModal = true;
            },
        };
    });
}

// Livewire v4 fires 'alpine:init' before Alpine.start().
// If the event already fired (page cached / race condition), use window.Alpine directly.
if (window.Alpine && window.Alpine.data) {
    registerBankSoalAlpineComponents();
} else {
    document.addEventListener('alpine:init', registerBankSoalAlpineComponents);
}
