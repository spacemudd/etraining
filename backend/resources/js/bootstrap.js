window._ = require('lodash');

/**
 * We'll load the axios HTTP library which allows us to easily issue requests
 * to our Laravel back-end. This library automatically handles sending the
 * CSRF token as a header based on the value of the "XSRF" token cookie.
 */

window.token = document.head.querySelector('meta[name="csrf-token"]');

window.axios = require('axios');
window.axios.defaults.withCredentials = true;
window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

const csrfMeta = document.head.querySelector('meta[name="csrf-token"]');
if (csrfMeta && csrfMeta.content) {
    window.axios.defaults.headers.common['X-CSRF-TOKEN'] = csrfMeta.content;
}

try {
    window.$ = window.jQuery = require('jquery');

    // require('bootstrap-sass');
} catch (e) {}

try {
    window.PullToRefresh = require('pulltorefreshjs');
} catch (e) {}

/**
 * Echo exposes an expressive API for subscribing to channels and listening
 * for events that are broadcast by Laravel. Echo and event broadcasting
 * allows your team to easily build robust real-time web applications.
 *
 * Prefer runtime meta tags from Laravel (.env) over MIX_* bake-in values,
 * so Coolify/Soketi host changes do not require a frontend rebuild.
 */

import Echo from 'laravel-echo';

window.Pusher = require('pusher-js');

function metaContent(name) {
    const el = document.head.querySelector(`meta[name="${name}"]`);
    return el && el.content ? el.content : null;
}

const pusherKey = metaContent('pusher-key') || process.env.MIX_PUSHER_APP_KEY;
const pusherHost = metaContent('pusher-host') || process.env.MIX_PUSHER_HOST;
const pusherPort = Number(metaContent('pusher-port') || process.env.MIX_PUSHER_PORT || 443);
const pusherScheme = metaContent('pusher-scheme') || process.env.MIX_PUSHER_SCHEME || 'https';
const pusherCluster = metaContent('pusher-cluster') || process.env.MIX_PUSHER_APP_CLUSTER || 'mt1';

if (pusherKey && pusherHost) {
    const forceTLS = pusherScheme === 'https';

    window.Echo = new Echo({
        broadcaster: 'pusher',
        key: pusherKey,
        cluster: pusherCluster,
        wsHost: pusherHost,
        wsPort: pusherPort,
        wssPort: pusherPort,
        forceTLS: forceTLS,
        encrypted: forceTLS,
        disableStats: true,
        enabledTransports: ['ws', 'wss'],
    });
} else if (typeof console !== 'undefined' && console.warn) {
    console.warn('[Echo] Skipped init — missing pusher-key or pusher-host meta (check PUSHER_APP_KEY / PUSHER_HOST in .env, then php artisan config:clear).', {
        hasKey: !!pusherKey,
        hasHost: !!pusherHost,
    });
}
