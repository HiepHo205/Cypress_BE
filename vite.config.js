import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import { bunny } from 'laravel-vite-plugin/fonts';
import tailwindcss from '@tailwindcss/vite';
import vue from '@vitejs/plugin-vue';

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.js', 'resources/js/admin/users.js'],
            refresh: true,
            fonts: [
                bunny('Instrument Sans', {
                    weights: [400, 500, 600]
                })
            ]
        }),

        vue({
            template: {
                transformAssetUrls: {
                    base: null,
                    includeAbsolute: false
                }
            }
        }),

        tailwindcss()
    ],

    resolve: {
        alias: {
            vue: 'vue/dist/vue.esm-bundler.js',
            '@': '/resources/js'
        }
    },

    server: {
        host: '0.0.0.0',
        port: 5173,
        cors: true,
        origin: 'http://localhost:5173',
        hmr: {
            host: 'admin.cypresshub.com'
        },
        watch: {
            ignored: ['**/storage/framework/views/**']
        }
    }
});
