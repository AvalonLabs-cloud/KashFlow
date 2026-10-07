import { createInertiaApp } from '@inertiajs/vue3';
import Echo from '@laravel/echo-vue';
import { configureEcho } from '@laravel/echo-vue';

import Nora from '@primeuix/themes/nora';
import axios from "axios";

import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';
import {createPinia} from 'pinia';
import piniaPluginPersistedstate from 'pinia-plugin-persistedstate'
import PrimeVue from 'primevue/config';
import Pusher from 'pusher-js';
import type { DefineComponent } from 'vue';
import { createApp, h } from 'vue';

import '../css/app.css';
import { SnackbarService, Vue3Snackbar } from "vue3-snackbar";

import "vue3-snackbar/styles";



// const echo = new Echo({
//     broadcaster: 'pusher',
//     key: import.meta.env.VITE_PUSHER_APP_KEY,
//     cluster: import.meta.env.VITE_PUSHER_APP_CLUSTER,
//     forceTLS: true,
// });

const pusher = new Pusher(import.meta.env.VITE_PUSHER_APP_KEY,
    {
  cluster: import.meta.env.VITE_PUSHER_APP_CLUSTER,
});

window.Pusher = pusher;

// import '../js/helpers/registration.js'

// import { initializeTheme } from '@/composables/useAppearance';

const appName = import.meta.env.VITE_APP_NAME || 'Laravel';
const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

if (token) {
    axios.defaults.headers.common['X-CSRF-TOKEN'] = token;
}

axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

axios.defaults.withCredentials = true;

createInertiaApp({
    defaults:{
           future: {
            useDialogForErrorModal: true,
        },
    },
    title: (title) => (title ? `${title} - ${appName}` : appName),
    resolve: (name) =>
        resolvePageComponent(
            `./pages/${name}.vue`,
            import.meta.glob<DefineComponent>('./pages/**/*.vue'),
        ),
    setup({ el, App, props, plugin }) {
        const pinia = createPinia()
        pinia.use(piniaPluginPersistedstate)

        createApp({ render: () => h(App, props) })
            .use(SnackbarService)
            .component('vue3-snackbar', Vue3Snackbar)
            .use(pinia)
            .use(plugin)
            .use(PrimeVue , {
                theme:{
                    preset: Nora

                }
            })
            .mount(el);
    },
    progress: {
        color: '#4B5563',
        includeCSS: true,
    },


});


