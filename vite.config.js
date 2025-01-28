import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

// https://vitejs.dev/config/
export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/css/app.css', // Your CSS file
                'resources/js/app.js',   // Your JS file
            ],
            refresh: true,
        }),
    ],
});
