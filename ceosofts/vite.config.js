import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                // 'resources/sass/app.scss',
                'resources/js/app.js',
		'resources/css/app.css',
            ],
            refresh: true,
        }),
    ],
    css: {
        preprocessorOptions: {
            scss: {
                // ปิด Deprecation Warnings เกี่ยวกับ @import
                additionalData: `
                    @use 'sass:color';
                    @use 'sass:math';
                `,
            },
        },
    },
    server: {
        hmr: {
            overlay: false, // ปิด Error Overlay เพื่อไม่ให้แสดงในหน้าเว็บ
        },
    },
});
