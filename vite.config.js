import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

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
    server: {
        cors: {
            origin: 'http://192.168.100.53:8000', // Allow requests from this origin
            methods: ['GET', 'POST', 'PUT', 'DELETE', 'OPTIONS'], // Allowed HTTP methods
            allowedHeaders: ['Content-Type', 'Authorization'], // Allowed headers
        },
    },
});