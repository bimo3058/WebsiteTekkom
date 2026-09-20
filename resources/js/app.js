import "./bootstrap";
import Alpine from "alpinejs";
import "../../Modules/BankSoal/resources/js/alpine-components";

// Livewire bundles its own Alpine, exposes it on window.Alpine and starts it
// automatically. Creating/overwriting a second instance here makes components
// register against the wrong Alpine — buttons/modals silently stop working
// (e.g. "Tambah Sesi" on the Jadwal & Sesi page, "confirmBtnColor" on the
// Periode page). Only run npm Alpine when the page has no Livewire at all.
const livewireIsPresent = !!window.Livewire
    || !!window.livewireScriptConfig
    || !!document.querySelector('script[data-update-uri][data-csrf]');

if (!livewireIsPresent) {
    window.Alpine = Alpine;

    document.addEventListener("DOMContentLoaded", () => {
        if (!window.Livewire) {
            window.Alpine.start();
        }
    });
}
