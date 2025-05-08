import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import dotenv from 'dotenv';

// Load Laravel .env file
dotenv.config();

export default defineConfig({
    server: {
        host: process.env.VITE_HOST || '192.168.100.13',
        port: parseInt(process.env.VITE_PORT || '5173'),
    },
    plugins: [
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.js'],
            refresh: true,
        }),
    ],
});
