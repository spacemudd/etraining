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

const echoDebug = typeof console !== 'undefined' ? console : { log() {}, warn() {}, error() {}, info() {}, group() {}, groupEnd() {} };

const metaKey = metaContent('pusher-key');
const metaHost = metaContent('pusher-host');
const metaPort = metaContent('pusher-port');
const metaScheme = metaContent('pusher-scheme');
const metaCluster = metaContent('pusher-cluster');

const pusherKey = metaKey || process.env.MIX_PUSHER_APP_KEY;
const pusherHost = metaHost || process.env.MIX_PUSHER_HOST;
const pusherPort = Number(metaPort || process.env.MIX_PUSHER_PORT || 443);
const pusherScheme = metaScheme || process.env.MIX_PUSHER_SCHEME || 'https';
const pusherCluster = metaCluster || process.env.MIX_PUSHER_APP_CLUSTER || 'mt1';

echoDebug.group('[Echo] Init');
echoDebug.log('meta tags', {
    key: metaKey ? `${String(metaKey).slice(0, 8)}…` : null,
    host: metaHost,
    port: metaPort,
    scheme: metaScheme,
    cluster: metaCluster,
});
echoDebug.log('resolved config', {
    key: pusherKey ? `${String(pusherKey).slice(0, 8)}…` : null,
    host: pusherHost,
    port: pusherPort,
    scheme: pusherScheme,
    cluster: pusherCluster,
    keySource: metaKey ? 'meta' : (process.env.MIX_PUSHER_APP_KEY ? 'mix' : 'missing'),
    hostSource: metaHost ? 'meta' : (process.env.MIX_PUSHER_HOST ? 'mix' : 'missing'),
});

if (pusherKey && pusherHost) {
    const forceTLS = pusherScheme === 'https';
    const wsUrl = `${forceTLS ? 'wss' : 'ws'}://${pusherHost}:${pusherPort}/app/${pusherKey}`;

    echoDebug.log('creating Echo →', wsUrl);

    try {
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

        const pusher = window.Echo.connector && window.Echo.connector.pusher;
        if (pusher && pusher.connection) {
            pusher.connection.bind('state_change', (states) => {
                echoDebug.log('[Echo] connection state:', states.previous, '→', states.current);
            });
            pusher.connection.bind('connected', () => {
                echoDebug.info('[Echo] connected', {
                    socketId: pusher.connection.socket_id,
                    host: pusherHost,
                });
            });
            pusher.connection.bind('unavailable', () => {
                echoDebug.warn('[Echo] connection unavailable');
            });
            pusher.connection.bind('failed', () => {
                echoDebug.error('[Echo] connection failed');
            });
            pusher.connection.bind('error', (err) => {
                echoDebug.error('[Echo] connection error', err);
            });
            pusher.connection.bind('disconnected', () => {
                echoDebug.warn('[Echo] disconnected');
            });
        } else {
            echoDebug.warn('[Echo] Echo created but pusher connector missing');
        }
    } catch (error) {
        echoDebug.error('[Echo] failed to create Echo instance', error);
    }
} else {
    echoDebug.warn('[Echo] Skipped init — missing pusher-key or pusher-host (check PUSHER_APP_KEY / PUSHER_HOST in .env, then php artisan config:clear).', {
        hasKey: !!pusherKey,
        hasHost: !!pusherHost,
    });
}

echoDebug.groupEnd();
