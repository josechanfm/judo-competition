import './bootstrap';
import '../css/app.css';

import { createApp, h } from 'vue';
import { createInertiaApp, Head, Link } from '@inertiajs/vue3';
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';
import { ZiggyVue } from '../../vendor/tightenco/ziggy/dist/vue.m';
import { i18nVue } from 'laravel-vue-i18n';
import Antd from 'ant-design-vue';

const appName = import.meta.env.VITE_APP_NAME || 'Laravel';

// 使用者選過的語系存在 localStorage，重新整理（F5）後才能沿用；
// 沒有記錄時退回伺服器渲染在 <html lang> 的語系（來自 session／預設語系）。
const getInitialLocale = () => {
    const saved = localStorage.getItem('app-locale');
    if (saved) {
        return saved;
    }

    const htmlLang = document.documentElement.lang;
    return htmlLang ? htmlLang.replace('-', '_') : 'en';
};

createInertiaApp({
    title: (title) => `${title} - ${appName}`,
    resolve: (name) => resolvePageComponent(`./Pages/${name}.vue`, import.meta.glob('./Pages/**/*.vue')),
    setup({ el, App, props, plugin }) {
        return createApp({ render: () => h(App, props) })
            .use(plugin)
            .use(i18nVue, {
                resolve: async lang => import(`../../lang/${lang}.json`),
                lang: getInitialLocale(),
                fallbackLang: 'en',
                // 當目前語言缺少某個 key 時，回退使用 en.json，避免畫面直接顯示 key 名稱
                fallbackMissingTranslations: true,
            })
            .use(ZiggyVue, Ziggy)
            .use(Antd)
            .component('inertia-head', Head)
            .component('inertia-link', Link)
            .mount(el);
    },
    progress: {
        color: '#4B5563',
    },
});
