import { createInertiaApp } from '@inertiajs/vue3';
import { createApp, createSSRApp, h } from 'vue';
import { createI18n } from 'vue-i18n';
import { initializeTheme } from '@/composables/useAppearance';
import AppLayout from '@/layouts/AppLayout.vue';
import AuthLayout from '@/layouts/AuthLayout.vue';
import SettingsLayout from '@/layouts/settings/Layout.vue';
import { initializeFlashToast } from '@/lib/flashToast';
import en from '@/locales/en';
import fr from '@/locales/fr';

const appName = import.meta.env.VITE_APP_NAME || 'Laravel';

createInertiaApp({
    title: (title) => (title ? `${title} - ${appName}` : appName),
    layout: (name) => {
        switch (true) {
            case name === 'Welcome':
                return null;
            case name.startsWith('auth/'):
                return AuthLayout;
            case name.startsWith('settings/'):
                return [AppLayout, SettingsLayout];
            default:
                return AppLayout;
        }
    },
    progress: {
        color: '#4B5563',
    },
    setup({ el, App, props, plugin }) {
        const locale = (props.initialPage.props.locale as string) ?? 'fr';

        const i18n = createI18n({
            legacy: false,
            locale,
            fallbackLocale: 'fr',
            messages: { fr, en },
        });

        const isSSR = el === null;
        const useSSRApp =
            isSSR ||
            (el instanceof Element && el.hasAttribute('data-server-rendered'));

        const vueApp = (useSSRApp ? createSSRApp : createApp)({
            render: () => h(App, props),
        })
            .use(plugin)
            .use(i18n);

        if (!isSSR) {
            vueApp.mount(el as Element);
        }

        return vueApp;
    },
});

// This will set light / dark mode on page load...
initializeTheme();

// This will listen for flash toast data from the server...
initializeFlashToast();
