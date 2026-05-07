<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Jostru Community')</title>

    <!-- Fonts & Frameworks -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/style.css') }}?v=3">
    <link rel="manifest" href="{{ asset('manifest.json') }}">

    <!-- Favicon (Pasti Muncul) -->
    <link rel="shortcut icon" type="image/png" href="{{ asset('images/logo.png') }}?v=100">
    <link rel="icon" type="image/png" href="{{ asset('images/logo.png') }}?v=100">
    <link rel="apple-touch-icon" href="{{ asset('images/logo.png') }}?v=100">

    <!-- Meta Tags SEO & Sosial Media -->
    <meta property="og:title" content="Jostru Community">
    <meta property="og:description" content="Selamat datang di Jostru Community, ubah limbah menjadi masa depan hijau.">
    <meta property="og:image" content="{{ asset('images/logo.png') }}">
    <meta property="og:url" content="{{ url()->current() }}">
    <meta property="og:type" content="website">
    <meta name="theme-color" content="#22c55e">

    <style>
        /* PENGATURAN WARNA MODE TERANG & GELAP */
        :root, [data-theme="light"] {
            --bg-color: #f8f9fa;
            --surface-color: #ffffff;
            --text-primary: #1f2937;
            --text-secondary: #6b7280;
            --border-color: rgba(0, 0, 0, 0.08);
            --glass-bg: rgba(255, 255, 255, 0.85);
            --glass-shadow: 0 8px 32px rgba(0, 0, 0, 0.05);
        }

        [data-theme="dark"] {
            --bg-color: #121212;
            --surface-color: #1e1e1e;
            --text-primary: #f9fafb;
            --text-secondary: #9ca3af;
            --border-color: rgba(255, 255, 255, 0.1);
            --glass-bg: rgba(30, 30, 30, 0.85);
            --glass-shadow: 0 8px 32px rgba(0, 0, 0, 0.4);
        }

        body {
            background-color: var(--bg-color);
            color: var(--text-primary);
            font-family: 'Inter', sans-serif;
            transition: background-color 0.3s ease, color 0.3s ease;
            -webkit-font-smoothing: antialiased;
        }

        /* Desain Kaca (Glassmorphism) */
        .glass {
            background: var(--glass-bg);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            border: 1px solid var(--border-color);
            box-shadow: var(--glass-shadow);
        }

        .text-muted {
            color: var(--text-secondary) !important;
        }

        /* --- Navbar Kustom --- */
        .navbar-floating {
            max-width: 1200px; 
            width: 95%; 
            border-radius: 50px; 
            margin-top: 15px; 
            padding: 0.6rem 1rem;
            transition: all 0.3s ease;
        }

        .nav-btn {
            border-radius: 50px;
            padding: 0.4rem 1.2rem;
            font-weight: 600;
            font-size: 0.9rem;
        }

        .theme-toggle-btn {
            background: var(--bg-color);
            border: 1px solid var(--border-color);
            color: var(--text-primary);
            width: 38px; height: 38px;
            border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
            transition: all 0.2s ease;
            cursor: pointer;
        }

        .theme-toggle-btn:hover {
            background: rgba(34, 197, 94, 0.1);
            transform: scale(1.05);
        }

        /* --- Perbaikan Bottom Nav (Mobile) --- */
        .bottom-nav {
            background: var(--surface-color);
            border-top: 1px solid var(--border-color);
            transition: background-color 0.3s ease;
            z-index: 1030;
        }
        .bottom-nav-item { color: var(--text-secondary); }
        .bottom-nav-item.active { color: #22c55e; }
        .bottom-nav-item:hover { color: #10b981; }
    </style>

    <!-- Script Swipe Layar Global -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const pages = [
                "{{ route('dashboard') }}",
                "{{ route('member.waste_report') }}",
                "{{ route('member.feed') }}",
                "{{ route('member.chat.list') }}",
                "{{ route('member.profile') }}"
            ];
            
            let currentUrl = window.location.href.split('?')[0].replace(/\/$/, "");
            let currentIndex = pages.indexOf(currentUrl);
            
            if (currentIndex !== -1) {
                let touchstartX = 0;
                let touchstartY = 0;
                let touchendX = 0;
                let touchendY = 0;
                const minSwipeDistance = 90;

                document.addEventListener('touchstart', e => {
                    touchstartX = e.changedTouches[0].screenX;
                    touchstartY = e.changedTouches[0].screenY;
                }, {passive: true});

                document.addEventListener('touchend', e => {
                    touchendX = e.changedTouches[0].screenX;
                    touchendY = e.changedTouches[0].screenY;
                    
                    let swipeX = touchendX - touchstartX;
                    let swipeY = touchendY - touchstartY;
                    
                    if (Math.abs(swipeX) > Math.abs(swipeY) && Math.abs(swipeX) > minSwipeDistance) {
                        if (document.body.classList.contains('sb-open')) return;
                        if (touchstartX < 50 && swipeX > 0) return;
                        if (document.activeElement && document.activeElement.closest('.table-responsive, .code-container, pre')) return;

                        if (swipeX < 0 && currentIndex < pages.length - 1) { 
                            document.body.style.opacity = '0.5';
                            window.location.href = pages[currentIndex + 1];
                        } else if (swipeX > 0 && currentIndex > 0) { 
                            document.body.style.opacity = '0.5';
                            window.location.href = pages[currentIndex - 1];
                        }
                    }
                }, {passive: true});
            }
        });
    </script>

    <script>
        // Service Worker & Theme Initializer
        if ('serviceWorker' in navigator) {
            window.addEventListener('load', () => {
                navigator.serviceWorker.register('/sw.js').catch(()=>{});
            });
        }

        let storedTheme = localStorage.getItem('jostru_theme');
        if (!storedTheme) {
            storedTheme = window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light';
        }
        document.documentElement.setAttribute('data-theme', storedTheme);
        
        document.addEventListener('DOMContentLoaded', () => {
            document.body.setAttribute('data-theme', storedTheme);
            updateThemeIcon(storedTheme);
        });
    </script>
</head>
<body>

    <!-- Navbar Floating -->
    <nav class="navbar fixed-top mx-auto glass navbar-floating">
        <div class="container-fluid px-2">
            <!-- LOGO ICON DAN TEKS SELALU MUNCUL -->
            <a class="navbar-brand fw-bold m-0 d-flex align-items-center gap-2" href="{{ url('/') }}">
                <img src="{{ asset('images/logo.png') }}" alt="Jostru Logo" width="30" height="30" style="border-radius: 50%; object-fit: cover;">
                <span style="background:linear-gradient(135deg,#22c55e,#10b981);-webkit-background-clip:text;-webkit-text-fill-color:transparent; font-size: 1.4rem;">
                    Jostru
                </span>
            </a>
            
            <div class="d-flex align-items-center gap-2 gap-md-3">
                <button onclick="toggleTheme()" class="theme-toggle-btn" aria-label="Ganti Tema">
                    <span id="theme-icon" style="font-size: 1.1rem; line-height: 1;">☀️</span>
                </button>

                @guest
                    <a href="{{ route('login') }}" class="btn btn-outline-success nav-btn">Masuk</a>
                @else
                    <!-- Tampilan Layar Besar -->
                    <div class="d-none d-md-flex align-items-center gap-2">
                        @if(in_array(auth()->user()->role, ['admin', 'superadmin']))
                            <a href="{{ route('admin.dashboard') }}" class="btn btn-outline-success nav-btn">Admin Panel</a>
                        @endif
                        <a href="{{ route('member.profile') }}" class="btn btn-outline-success nav-btn">Profil</a>
                        <form action="{{ route('logout') }}" method="POST" class="d-inline m-0">
                            @csrf
                            <button type="submit" class="btn btn-danger nav-btn">Keluar</button>
                        </form>
                    </div>

                    <!-- Tampilan Layar Kecil -->
                    <div class="d-flex d-md-none align-items-center gap-2">
                        @if(in_array(auth()->user()->role, ['admin', 'superadmin']))
                            <a href="{{ route('admin.dashboard') }}" class="btn btn-outline-success nav-btn" style="padding: 0.3rem 0.8rem; font-size: 0.8rem;">Admin</a>
                        @endif
                        <form action="{{ route('logout') }}" method="POST" class="d-inline m-0">
                            @csrf
                            <button type="submit" class="btn btn-danger d-flex align-items-center justify-content-center" style="border-radius: 50%; width: 38px; height: 38px; padding: 0;" title="Keluar">
                                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="currentColor" viewBox="0 0 16 16">
                                  <path fill-rule="evenodd" d="M10 12.5a.5.5 0 0 1-.5.5h-8a.5.5 0 0 1-.5-.5v-9a.5.5 0 0 1 .5-.5h8a.5.5 0 0 1 .5.5v2a.5.5 0 0 0 1 0v-2A1.5 1.5 0 0 0 9.5 2h-8A1.5 1.5 0 0 0 0 3.5v9A1.5 1.5 0 0 0 1.5 14h8a1.5 1.5 0 0 0 1.5-1.5v-2a.5.5 0 0 0-1 0v2z"/>
                                  <path fill-rule="evenodd" d="M15.854 8.354a.5.5 0 0 0 0-.708l-3-3a.5.5 0 0 0-.708.708L14.293 7.5H5.5a.5.5 0 0 0 0 1h8.793l-2.147 2.146a.5.5 0 0 0 .708.708l3-3z"/>
                                </svg>
                            </button>
                        </form>
                    </div>
                @endguest
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <main class="container" style="max-width:1200px; padding-top: 100px; padding-bottom: 100px;">
        @yield('content')
    </main>

    <!-- Footer -->
    <footer class="text-center py-4 text-muted small mt-auto">
        <div class="mb-3 d-flex justify-content-center gap-3 flex-wrap">
            <a href="{{ route('about') }}" class="text-decoration-none text-muted fw-bold">Tentang Kami</a>
            <a href="{{ route('faq') }}" class="text-decoration-none text-muted fw-bold">FAQ</a>
            <a href="{{ route('privacy_policy') }}" class="text-decoration-none text-muted fw-bold">Kebijakan Privasi</a>
        </div>
        &copy; {{ date('Y') }} Jostru Community. All rights reserved.
    </footer>

    <!-- Mobile Bottom Navigation -->
    @auth
    <nav class="bottom-nav d-md-none fixed-bottom" style="top: auto;">
        <div class="bottom-nav-container d-flex justify-content-around py-2">
            <a href="{{ route('dashboard') }}" class="bottom-nav-item text-center text-decoration-none {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1v-5m0 0v5a1 1 0 01-1 1H9m6 0a1 1 0 01-1-1v-5m0 0v5a1 1 0 01-1 1H9"/></svg>
                <div style="font-size: 0.7rem; font-weight: 500; margin-top: 3px;">Beranda</div>
            </a>
            <a href="{{ route('member.waste_report') }}" class="bottom-nav-item text-center text-decoration-none {{ request()->routeIs('member.waste_report*') ? 'active' : '' }}">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2m0 0a2 2 0 002-2m-2 2v12"/></svg>
                <div style="font-size: 0.7rem; font-weight: 500; margin-top: 3px;">Setor</div>
            </a>
            <a href="{{ route('member.feed') }}" class="bottom-nav-item text-center text-decoration-none {{ request()->routeIs('member.feed*') ? 'active' : '' }}">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v12a2 2 0 01-2 2z"/></svg>
                <div style="font-size: 0.7rem; font-weight: 500; margin-top: 3px;">Feed</div>
            </a>
            <a href="{{ route('member.chat.list') }}" class="bottom-nav-item text-center text-decoration-none {{ request()->routeIs('member.chat*') ? 'active' : '' }}">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/></svg>
                <div style="font-size: 0.7rem; font-weight: 500; margin-top: 3px;">Chat</div>
            </a>
            <a href="{{ route('member.profile') }}" class="bottom-nav-item text-center text-decoration-none {{ request()->routeIs('member.profile*') ? 'active' : '' }}">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                <div style="font-size: 0.7rem; font-weight: 500; margin-top: 3px;">Profil</div>
            </a>
        </div>
    </nav>
    @endauth

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        function toggleTheme() {
            let currentTheme = document.documentElement.getAttribute('data-theme');
            let newTheme = currentTheme === 'dark' ? 'light' : 'dark';
            
            document.documentElement.setAttribute('data-theme', newTheme);
            document.body.setAttribute('data-theme', newTheme);
            localStorage.setItem('jostru_theme', newTheme);
            
            updateThemeIcon(newTheme);
        }

        function updateThemeIcon(theme) {
            const iconSpan = document.getElementById('theme-icon');
            if(iconSpan) {
                // KODE YANG SEBELUMNYA ERROR SUDAH DIPERBAIKI DI SINI
                iconSpan.innerHTML = theme === 'dark' ? '🌙' : '☀️';
            }
        }
    </script>
    
    @stack('scripts')
</body>
</html>