require('./bootstrap');

import Vue from 'vue';
Vue.config.ignoredElements = [/^ion-/]
Vue.config.productionTip = false

import { InertiaApp } from '@inertiajs/inertia-vue';
import { InertiaForm } from 'laravel-jetstream';
import PortalVue from 'portal-vue';
import VueMeta from 'vue-meta'
import VueInternationalization from 'vue-i18n';
import Locale from './vue-i18n-locales.generated';
import { InertiaProgress } from '@inertiajs/progress'
InertiaProgress.init({
    // The delay after which the progress bar will
    // appear during navigation, in milliseconds.
    delay: 250,
    // The color of the progress bar.
    color: '#ea1d3c',
    // Whether to include the default NProgress styles.
    includeCSS: true,
    // Whether the NProgress spinner will be shown.
    showSpinner: false,
})

Vue.use(InertiaApp);
Vue.use(InertiaForm);
Vue.use(PortalVue);
Vue.use(VueMeta)
Vue.use(VueInternationalization);
Vue.mixin({ methods: { route: window.route } })

const app = document.getElementById('app');

const lang = document.documentElement.lang.substr(0, 2);
const i18n = new VueInternationalization({
    locale: lang,
    messages: Locale
});

new Vue({
    i18n,
    metaInfo: {
        titleTemplate: (title) => title ? `${title} - eTraining` : 'eTraining'
    },
    render: (h) =>
        h(InertiaApp, {
            props: {
                initialPage: JSON.parse(app.dataset.page),
                resolveComponent: (name) => require(`./Pages/${name}`).default,
            },
        }),
}).$mount(app);
