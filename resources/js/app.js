require('./bootstrap');

import Vue from 'vue';
Vue.config.ignoredElements = [/^ion-/]
Vue.config.productionTip = false

import { InertiaApp } from '@inertiajs/inertia-vue';
import { InertiaForm } from 'laravel-jetstream';
import PortalVue from 'portal-vue';
import VueMeta from 'vue-meta'
import Skeleton from 'vue-loading-skeleton';
import VueWait from 'vue-wait'
import Store from './store';
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
Vue.use(VueWait)
Vue.use(Skeleton)
Vue.use(VueInternationalization);
Vue.mixin({ methods: { route: window.route } })

Vue.directive('can', function (el, binding) {
    let permissions = document.head.querySelector('meta[name="user-permissions"]');

    if(permissions.content.indexOf(binding.value) == -1){
        el.style.display = 'none';
    }

    return permissions.content.indexOf(binding.value) !== -1;
});

const app = document.getElementById('app');

const lang = document.documentElement.lang.substr(0, 2);
const i18n = new VueInternationalization({
    locale: lang,
    messages: Locale
});

new Vue({
    i18n,
    Store,
    wait: new VueWait({
        // Defaults values are following:
        useVuex: false,              // Uses Vuex to manage wait state
        vuexModuleName: 'wait',      // Vuex module name
    }),
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
