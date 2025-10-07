require('./bootstrap');

import Vue from 'vue';
import Vuex from "vuex";
Vue.config.ignoredElements = [/^ion-/]
Vue.config.productionTip = false

import { InertiaApp } from '@inertiajs/inertia-vue';
import { InertiaForm } from 'laravel-jetstream';
import { plugin } from '@inertiajs/inertia-vue';
import PortalVue from 'portal-vue';
import VueMeta from 'vue-meta'
import Skeleton from 'vue-loading-skeleton';
import VueWait from 'vue-wait'
import LottieAnimation from 'lottie-vuejs/src/LottieAnimation.vue';
import VueInternationalization from 'vue-i18n';
import Locale from './vue-i18n-locales.generated';
import VModal from 'vue-js-modal';
import { InertiaProgress } from '@inertiajs/progress';
import DateMixin from "./Mixins/DateMixin";
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

Vue.use(plugin);
Vue.use(InertiaForm);
Vue.use(PortalVue);
Vue.use(VueMeta);
Vue.use(VueWait);
Vue.use(Vuex);
Vue.use(Skeleton);
Vue.use(VModal);
Vue.use(LottieAnimation);
Vue.use(VueInternationalization);
Vue.mixin({ 
    methods: { 
        route: function(name, params = {}) {
            if (typeof window.route === 'function') {
                return window.route(name, params);
            }
            console.error('Route helper not available. Make sure @routes is included in your blade template.');
            return '#';
        }
    } 
});
Vue.mixin(DateMixin);

// import { loadCldr, L10n } from '@syncfusion/ej2-base';
import { DateTimePickerPlugin } from '@syncfusion/ej2-vue-calendars';
// import * as numberingSystems from 'cldr-data/supplemental/numberingSystems.json';
// import * as gregorian from 'cldr-data/main/ar/ca-gregorian.json';
// import * as numbers from 'cldr-data/main/ar/numbers.json';
// import * as timeZoneNames from 'cldr-data/main/ar/timeZoneNames.json';
// import * as weekData from 'cldr-data/supplemental/weekdata.json'; // To load the culture based first day of week
Vue.use(DateTimePickerPlugin);
// loadCldr(numberingSystems, gregorian, numbers, timeZoneNames, weekData);
// L10n.load({
//     'ar': {
//         'datetimepicker': { placeholder: 'حدد التاريخ والوقت', today: 'اليوم'},
//     },
// });

Vue.directive('can', function (el, binding) {
    let permissions = document.head.querySelector('meta[name="user-permissions"]');

    if (permissions) {
        return permissions.content.indexOf(binding) !== -1;
    }

    return false;
})

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



const appName = lang === 'ar' ? 'منصة التدريب' : 'eTraining';

// PullToRefresh.init({
//     mainElement: 'body',
//     instructionsPullToRefresh: lang === 'ar' ? 'قم بالسحب لتحديث الصفحة' : 'Pull to refresh',
//     instructionsReleaseToRefresh: lang === 'ar' ? 'حرر للتحديث' : 'Release to refresh',
//     instructionsRefreshing: lang === 'ar' ? 'جاري التحديث...' : 'Refresh...',
//     onRefresh() {
//         window.location.reload();
//     }
// });

import Store from './Store/index';

new Vue({
    i18n,
    Store,
    wait: new VueWait({
        // Defaults values are following:
        useVuex: false,              // Uses Vuex to manage wait state
        vuexModuleName: 'wait',      // Vuex module name
    }),
    metaInfo: {
        titleTemplate: (title) => title ? `${title} - ${appName}` : `${appName}`
    },
    render: (h) =>
        h(InertiaApp, {
            props: {
                initialPage: JSON.parse(app.dataset.page),
                resolveComponent: (name) => require(`./Pages/${name}`).default,
            },
        }),
}).$mount(app);
