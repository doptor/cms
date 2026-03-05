// RyaanCMS Service Worker — PWA Support
const CACHE_NAME = 'ryaancms-v1.0.0';
const STATIC_ASSETS = [
  '/',
  '/dashboard',
  '/manifest.json',
  'https://fonts.googleapis.com/css2?family=Syne:wght@400;600;700;800&family=Instrument+Sans:wght@400;500;600&family=DM+Mono:wght@400;500&display=swap',
];

// Install — cache static assets
self.addEventListener('install', event => {
  event.waitUntil(
    caches.open(CACHE_NAME).then(cache => {
      console.log('[RyaanCMS SW] Caching static assets');
      return cache.addAll(STATIC_ASSETS);
    })
  );
  self.skipWaiting();
});

// Activate — clean up old caches
self.addEventListener('activate', event => {
  event.waitUntil(
    caches.keys().then(keys =>
      Promise.all(keys.filter(k => k !== CACHE_NAME).map(k => caches.delete(k)))
    )
  );
  self.clients.claim();
});

// Fetch — network first, fallback to cache
self.addEventListener('fetch', event => {
  // Skip non-GET and API requests
  if (event.request.method !== 'GET') return;
  if (event.request.url.includes('/ai/') || event.request.url.includes('/api/')) return;

  event.respondWith(
    fetch(event.request)
      .then(response => {
        // Cache successful responses
        if (response.status === 200) {
          const clone = response.clone();
          caches.open(CACHE_NAME).then(cache => cache.put(event.request, clone));
        }
        return response;
      })
      .catch(() => {
        // Fallback to cache when offline
        return caches.match(event.request).then(cached => {
          if (cached) return cached;
          // Offline fallback page
          if (event.request.headers.get('accept').includes('text/html')) {
            return new Response(`
              <!DOCTYPE html>
              <html><head><meta charset="UTF-8"><title>Offline — RyaanCMS</title>
              <style>body{font-family:sans-serif;background:#0a0a0f;color:#fff;display:flex;align-items:center;justify-content:center;min-height:100vh;text-align:center}
              h1{font-size:2rem;margin-bottom:1rem}p{color:#6b6b8a}</style></head>
              <body><div><div style="font-size:3rem">⚡</div><h1>RyaanCMS</h1><p>You're offline. Check your connection and try again.</p></div></body></html>
            `, { headers: { 'Content-Type': 'text/html' } });
          }
        });
      })
  );
});

// Background sync for AI requests when back online
self.addEventListener('sync', event => {
  if (event.tag === 'ai-generate') {
    event.waitUntil(syncPendingRequests());
  }
});

async function syncPendingRequests() {
  console.log('[RyaanCMS SW] Syncing pending AI requests...');
}
