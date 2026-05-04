<<<<<<< HEAD
import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import tailwindcss from '@tailwindcss/vite';
=======
import { defineConfig } from "vite";
import laravel from "laravel-vite-plugin";
>>>>>>> 907aff17a69304925ed419e8a818c3b3b4292d9f

export default defineConfig({
    plugins: [
        laravel({
<<<<<<< HEAD
            input: ['resources/css/app.css', 'resources/js/app.js'],
            refresh: true,
        }),
        tailwindcss(),
    ],
    server: {
        watch: {
            ignored: ['**/storage/framework/views/**'],
=======
            input: ["resources/css/app.css", "resources/js/app.js"],
            refresh: true,
        }),
    ],
    server: {
        host: "0.0.0.0", // Membuka akses agar IP lain (HP) bisa konek
        hmr: {
            host: process.env.VITE_HMR_HOST || "localhost",
            protocol: "ws",
>>>>>>> 907aff17a69304925ed419e8a818c3b3b4292d9f
        },
    },
});
