// Jostru Service Worker v4 — Network-First Strategy
// Selalu mengambil versi terbaru dari server, cache hanya sebagai fallback offline.
const CACHE_NAME = 'jostru-cache-v4';
const STATIC_ASSETS = [
  '/images/logo.png',
  '/css/style.css',
  'https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap'
];

// Install: cache static assets only
self.addEventListener('install', event => {
  self.skipWaiting(); // Langsung aktif, jangan menunggu tab ditutup
  event.waitUntil(
    caches.open(CACHE_NAME).then(cache => cache.addAll(STATIC_ASSETS))
  );
});

// Activate: hapus cache lama
self.addEventListener('activate', event => {
  event.waitUntil(
    caches.keys().then(keys =>
      Promise.all(keys.filter(k => k !== CACHE_NAME).map(k => caches.delete(k)))
    ).then(() => self.clients.claim()) // Ambil alih semua tab langsung
  );
});

// Fetch: Network-first untuk HTML, Cache-first hanya untuk static assets
self.addEventListener('fetch', event => {
  const url = new URL(event.request.url);

  // Jangan cache request POST, non-GET, atau Chrome extensions
  if (event.request.method !== 'GET') return;
  if (url.protocol === 'chrome-extension:') return;

  // HTML pages (navigasi) → SELALU dari network
  if (event.request.mode === 'navigate' || event.request.headers.get('accept')?.includes('text/html')) {
    event.respondWith(
      fetch(event.request)
        .catch(() => caches.match(event.request)) // Fallback ke cache hanya jika offline
    );
    return;
  }

  // Static assets (CSS, JS, images, fonts) → Network first, fallback ke cache
  event.respondWith(
    fetch(event.request)
      .then(response => {
        // Update cache dengan versi baru
        const clone = response.clone();
        caches.open(CACHE_NAME).then(cache => cache.put(event.request, clone));
        return response;
      })
      .catch(() => caches.match(event.request))
  );
});
