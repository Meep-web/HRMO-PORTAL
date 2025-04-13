import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/css/app.css', 
                'resources/js/app.js',
                'resources/css/leaveCredits.css',
                'resources/js/leaveCredits.js',
            ],
            refresh: true,
        }),
    ]
    // ,
    // server: {
    //     cors: {
    //         origin: 'http://192.168.100.53:8000', // Allow requests from this origin
    //         methods: ['GET', 'POST', 'PUT', 'DELETE', 'OPTIONS'], // Allowed HTTP methods
    //         allowedHeaders: ['Content-Type', 'Authorization'], // Allowed headers
    //     },
    // },
});