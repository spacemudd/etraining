/**
 * Chat-only PWA helpers: service worker registration and Web Push subscribe/unsubscribe.
 */

export function isChatPwaStandalone() {
    if (typeof window === 'undefined') {
        return false;
    }

    const displayStandalone = window.matchMedia && window.matchMedia('(display-mode: standalone)').matches;
    const iosStandalone = typeof navigator !== 'undefined' && navigator.standalone === true;

    return !!(displayStandalone || iosStandalone);
}

export async function registerChatServiceWorker() {
    if (typeof navigator === 'undefined' || !('serviceWorker' in navigator)) {
        return null;
    }

    try {
        return await navigator.serviceWorker.register('/sw-chat.js');
    } catch (error) {
        console.warn('[Chat PWA] Service worker registration failed', error);
        return null;
    }
}

function urlBase64ToUint8Array(base64String) {
    const padding = '='.repeat((4 - (base64String.length % 4)) % 4);
    const base64 = (base64String + padding).replace(/-/g, '+').replace(/_/g, '/');
    const rawData = window.atob(base64);
    const outputArray = new Uint8Array(rawData.length);

    for (let i = 0; i < rawData.length; i += 1) {
        outputArray[i] = rawData.charCodeAt(i);
    }

    return outputArray;
}

export async function getExistingPushSubscription() {
    if (typeof navigator === 'undefined' || !('serviceWorker' in navigator) || !('PushManager' in window)) {
        return null;
    }

    const registration = await navigator.serviceWorker.ready;
    return registration.pushManager.getSubscription();
}

export async function subscribeChatPush({ vapidPublicKey, storeUrl }) {
    if (!vapidPublicKey) {
        throw new Error('Missing VAPID public key');
    }

    if (typeof Notification === 'undefined') {
        throw new Error('Notifications are not supported');
    }

    const permission = await Notification.requestPermission();
    if (permission !== 'granted') {
        const error = new Error('Notification permission denied');
        error.code = 'permission-denied';
        throw error;
    }

    const registration = await registerChatServiceWorker();
    if (!registration) {
        throw new Error('Service worker unavailable');
    }

    await navigator.serviceWorker.ready;

    let subscription = await registration.pushManager.getSubscription();
    if (!subscription) {
        subscription = await registration.pushManager.subscribe({
            userVisibleOnly: true,
            applicationServerKey: urlBase64ToUint8Array(vapidPublicKey),
        });
    }

    const json = subscription.toJSON();
    await window.axios.post(storeUrl, {
        endpoint: json.endpoint,
        public_key: json.keys && json.keys.p256dh ? json.keys.p256dh : null,
        auth_token: json.keys && json.keys.auth ? json.keys.auth : null,
        content_encoding: (PushManager.supportedContentEncodings && PushManager.supportedContentEncodings[0]) || 'aesgcm',
    });

    return subscription;
}

export async function unsubscribeChatPush({ destroyUrl }) {
    const subscription = await getExistingPushSubscription();
    if (!subscription) {
        return false;
    }

    const endpoint = subscription.endpoint;
    await subscription.unsubscribe();

    if (destroyUrl) {
        await window.axios.delete(destroyUrl, {
            data: { endpoint },
        });
    }

    return true;
}
