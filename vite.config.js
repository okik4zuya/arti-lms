import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/css/app.css', 'resources/css/site.css', 'resources/js/app.js', 'resources/js/contact-panel.js', 'resources/js/subscribe.js'],
            refresh: true,
        }),
    ],
    server: {
        host: '127.0.0.1',
        port: 5600,
        strictPort: true,
        cors: true,
        hmr: {
            host: 'localhost',
        },
    },
});
