/* Chat PWA service worker — push notifications + open chat on click. */
self.addEventListener('install', function (event) {
    self.skipWaiting();
});

self.addEventListener('activate', function (event) {
    event.waitUntil(self.clients.claim());
});

self.addEventListener('push', function (event) {
    var payload = {
        title: 'New WhatsApp message',
        body: '',
        url: '/back/chat',
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

    event.waitUntil(
        self.registration.showNotification(payload.title, {
            body: payload.body,
            icon: '/android-chrome-192x192.png',
            badge: '/android-chrome-192x192.png',
            data: { url: payload.url },
            tag: 'whatsapp-chat',
            renotify: true,
        })
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
