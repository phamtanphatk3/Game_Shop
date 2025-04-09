self.addEventListener('install', (event) => {
    event.waitUntil(self.skipWaiting());
});

self.addEventListener('activate', (event) => {
    event.waitUntil(self.clients.claim());
});

self.addEventListener('fetch', event => {
    // Bỏ qua các request không cần thiết
    // Cache-first strategy cho file audio
    if (event.request.url.includes('Music.mp3')) {
        event.respondWith(
            caches.open('audio-cache').then(cache => 
                cache.match(event.request).then(response => 
                    response || fetch(event.request).then(fetchResponse => {
                        cache.put(event.request, fetchResponse.clone());
                        return fetchResponse;
                    })
                )
            )
        );
    }
}); 