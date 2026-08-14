/* Chat PWA service worker — push notifications, badge, open chat on click. */

var pendingBadgeCount = null;

function applyAppBadge(count) {
    var n = Math.max(0, Number(count) || 0);
    pendingBadgeCount = n;

    if (!self.navigator) {
        return Promise.resolve();
    }

    try {
        if (n > 0 && typeof self.navigator.setAppBadge === 'function') {
            return self.navigator.setAppBadge(n).catch(function () {});
        }
        if (n === 0 && typeof self.navigator.clearAppBadge === 'function') {
            return self.navigator.clearAppBadge().catch(function () {});
        }
    } catch (e) {}

    return Promise.resolve();
}

function bumpAppBadge() {
    var next = (pendingBadgeCount == null ? 0 : pendingBadgeCount) + 1;
    return applyAppBadge(next);
}

self.addEventListener('install', function (event) {
    self.skipWaiting();
});

self.addEventListener('activate', function (event) {
    event.waitUntil(self.clients.claim());
});

self.addEventListener('message', function (event) {
    var data = event.data || {};
    if (data.type === 'CHAT_APP_BADGE') {
        event.waitUntil(applyAppBadge(data.count));
    }
});

self.addEventListener('push', function (event) {
    var payload = {
        title: 'New WhatsApp message',
        body: '',
        url: '/back/chat',
        badge: null,
    };

    try {
        if (event.data) {
            var data = event.data.json();
            if (data && typeof data === 'object') {
                if (data.title) {
                    payload.title = String(data.title);
                }
                if (data.body) {
                    payload.body = String(data.body);
                }
                if (data.url) {
                    payload.url = String(data.url);
                }
                if (data.badge != null && data.badge !== '') {
                    payload.badge = Number(data.badge);
                }
            }
        }
    } catch (e) {
        try {
            var text = event.data && event.data.text();
            if (text) {
                payload.body = text;
            }
        } catch (ignore) {}
    }

    var badgePromise = (payload.badge != null && !isNaN(payload.badge))
        ? applyAppBadge(payload.badge)
        : bumpAppBadge();

    event.waitUntil(
        Promise.all([
            self.registration.showNotification(payload.title, {
                body: payload.body,
                icon: '/android-chrome-192x192.png',
                badge: '/notification-badge.png',
                data: { url: payload.url },
                tag: 'whatsapp-chat',
                renotify: true,
            }),
            badgePromise,
        ])
    );
});

self.addEventListener('notificationclick', function (event) {
    event.notification.close();

    var targetUrl = '/back/chat';
    if (event.notification && event.notification.data && event.notification.data.url) {
        targetUrl = event.notification.data.url;
    }

    event.waitUntil(
        self.clients.matchAll({ type: 'window', includeUncontrolled: true }).then(function (clientList) {
            for (var i = 0; i < clientList.length; i++) {
                var client = clientList[i];
                if (client.url && client.url.indexOf('/back/chat') !== -1 && 'focus' in client) {
                    return client.focus();
                }
            }
            if (self.clients.openWindow) {
                return self.clients.openWindow(targetUrl);
            }
            return undefined;
        })
    );
});
