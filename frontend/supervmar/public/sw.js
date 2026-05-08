self.addEventListener('push', (event) => {
    const data = event.data ? event.data.json() : {};
    const title = data.title || 'SuperVMar';
    const options = {
        body: data.body || '',
        icon: data.icon || '/images/supervmar-logo.png',
        badge: '/images/supervmar-logo.png',
        vibrate: [200, 100, 200],
        data: data.data || {},
        tag: data.data?.zoneId ? `restock-${data.data.zoneId}` : 'supervmar',
        renotify: true,
    };
    event.waitUntil(self.registration.showNotification(title, options));
});

self.addEventListener('notificationclick', (event) => {
    event.notification.close();
    const url = event.notification.data?.url || '/';
    event.waitUntil(
        clients.matchAll({ type: 'window', includeUncontrolled: true }).then((clientList) => {
            for (const client of clientList) {
                if (client.url.includes(url) && 'focus' in client) {
                    return client.focus();
                }
            }
            return clients.openWindow(url);
        })
    );
});
