import { wayfinder } from '@laravel/vite-plugin-wayfinder';
import tailwindcss from '@tailwindcss/vite';
import vue from '@vitejs/plugin-vue';
import laravel from 'laravel-vite-plugin';
import { defineConfig } from 'vite';
import path from 'path';

export default defineConfig({
    build: {
        rollupOptions: {
               external: [/\/routes\//],

        },
    },
    plugins: [
        laravel({
            input: ['resources/js/app.ts',
                    'resources/js/pages/app/Login.vue' 
            ],
            ssr: 'resources/js/ssr.ts',
            refresh: true,
        }),
        tailwindcss(),
        vue({
            template: {
                transformAssetUrls: {
                    base: null,
                    includeAbsolute: false,
                },
            },
        }),
        // wayfinder({
        //     formVariants: true,
        // }),
    ],

    server: {
        host: '127.0.0.1',
        port: 5173,
        strictPort: true,
        hmr: {
            overlay: false,
        },

        cors: {
            origin: 'http://xpaynowlimited.test',
        },
    },

    // server: {
    //     cors: {
    //         origin: "https://kpljrogw49.sharedwithexpose.com",
    //         methods: ["GET", "POST", "PUT", "DELETE", "PATCH", "OPTIONS"],
    //         allowedHeaders: ["Content-Type", "Authorization", "X-Requested-With"],
    //     },
    //     headers: {
    //         "Access-Control-Allow-Private-Network": "true",
    //     }
    // },

    resolve: {
        alias: {
            '@stores': path.resolve(__dirname, 'resources/js/stores'),
        },
    },
});
