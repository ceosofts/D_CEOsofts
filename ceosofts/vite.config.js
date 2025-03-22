import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/sass/app.scss',
                'resources/css/app.css',
                'resources/js/app.js',
            ],
            refresh: true,
        }),
    ],
    css: {
        preprocessorOptions: {
            scss: {
                quietDeps: true, // Silence deprecation warnings from dependencies
            }
        },
        // Explicitly set the path to PostCSS config
        postcss: './postcss.config.cjs'
    },
    resolve: {
        alias: {
            '$': 'jQuery',
        },
    },
});
