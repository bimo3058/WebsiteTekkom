import { defineConfig } from "vite";
import laravel from "laravel-vite-plugin";

export default defineConfig({
    plugins: [
        laravel({
            input: ["resources/css/app.css", "resources/js/app.js"],

            // refresh: true,
            refresh: [
                "resources/views/**",
                "routes/**",
                "app/Livewire/**",

                // ─── MODUL: EOFFICE ───
                "Modules/EOffice/resources/views/**/*.blade.php",
                "Modules/EOffice/routes/**/*.php",

                // ─── MODUL: BANK SOAL ───
                "Modules/BankSoal/resources/views/**/*.blade.php",
                "Modules/BankSoal/routes/**/*.php",

                // ─── MODUL: CAPSTONE ───
                "Modules/Capstone/resources/views/**/*.blade.php",
                "Modules/Capstone/routes/**/*.php",

                // ─── MODUL: MANAJEMEN MAHASISWA ───
                "Modules/ManajemenMahasiswa/resources/views/**/*.blade.php",
                "Modules/ManajemenMahasiswa/routes/**/*.php",
            ],
        }),
    ],
    server: {
        host: "0.0.0.0",
        hmr: {
            host: process.env.VITE_HMR_HOST || "localhost",
            protocol: "ws",
        },
    },
});
