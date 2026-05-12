import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import tailwindcss from '@tailwindcss/vite';

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.js'],
            refresh: true,
        }),
        tailwindcss(),
    ],
    server: {
        host: "0.0.0.0", // Membuka akses agar IP lain (HP) bisa konek
        hmr: {
            host: "localhost",
            protocol: "ws",
        },
        watch: {
            ignored: ['**/storage/framework/views/**'],
        },
    },
});