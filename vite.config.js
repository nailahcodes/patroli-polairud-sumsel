import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import { bunny } from 'laravel-vite-plugin/fonts';
import tailwindcss from '@tailwindcss/vite';
import { VitePWA } from 'vite-plugin-pwa';

export default defineConfig({

    plugins: [

        laravel({
            input: [
                'resources/css/app.css',
                'resources/js/app.js'
            ],
            refresh: true,
        }),

        tailwindcss(),

        VitePWA({
            registerType: 'autoUpdate',
            
            workbox: {

                navigateFallback: '/',

                runtimeCaching: [
                    {
                        urlPattern: ({ request }) =>
                            request.mode === 'navigate',

                        handler: 'NetworkFirst',

                        options: {
                            cacheName: 'pages-cache'
                        }
                    }
                ]
            },

            manifest: {
                name: 'Monitoring Patroli Polairud',
                short_name: 'POLAIRUD',
                description: 'Sistem Monitoring Patroli Perairan',
                theme_color: '#123f7a',
                background_color: '#efe2c8',
                display: 'standalone',
                start_url: '/',
                icons: [
                    {
                        src: '/icons/icon-192.png',
                        sizes: '192x192',
                        type: 'image/png'
                    },
                    {
                        src: '/icons/icon-512.png',
                        sizes: '512x512',
                        type: 'image/png'
                    }
                ]
            }
        })

        
    ]
});