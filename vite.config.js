import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.js'],
            refresh: true,
        }),
        
    ],
    server: {
            host: '192.168.101.13', // <-- Add or ensure this line exists
            // port: 5173 // Optional: you can specify the port if needed
            cors: {
                origin: '*', // This allows requests from any origin (for development)
                methods: ['GET', 'HEAD', 'PUT', 'POST', 'DELETE', 'PATCH'],
                allowedHeaders: ['Content-Type', 'Authorization', 'X-Requested-With'],
            },
        }
});
