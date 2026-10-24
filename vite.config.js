import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/css/app.css', 'resources/css/site.css', 'resources/js/app.js', 'resources/js/subscribe.js', 'resources/js/sticky-buy.js', 'resources/js/submit-loading.js', 'resources/js/tools/composite.js', 'resources/js/tools/diagram.js', 'resources/js/tools/plot.js', 'resources/js/tools/gem.js'],
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
