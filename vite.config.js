import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import vue from '@vitejs/plugin-vue';

export default defineConfig({
    plugins: [
        laravel({
            input: 'resources/js/app.js',
            refresh: true,
        }),
        vue({
            template: {
                transformAssetUrls: {
                    base: null,
                    includeAbsolute: false,
                },
            },
        }),
    ],
    resolve: {
        alias: {
            vue: 'vue/dist/vue.esm-bundler.js',
        },
    },

    server: {
        host: '0.0.0.0', // Agar Vite bisa didengarkan dari luar container Docker
        port: 5173,
        strictPort: true,
        hmr: {
            host: 'localhost', // Memaksa browser/Laravel mencari Hot Module Replacement di localhost Windows
            port: 5173,
        },
    },
});
