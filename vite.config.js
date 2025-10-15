import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/css/app.css',
                'resources/js/app.js',
                // Remove simple-realtime.js from here since it's imported in app.js
                // All other JS files are also imported in app.js and don't need separate entries
            ],
            refresh: true,
        }),
    ],
    build: {
        rollupOptions: {
            output: {
                manualChunks: undefined,
            }
        }
    }
});
