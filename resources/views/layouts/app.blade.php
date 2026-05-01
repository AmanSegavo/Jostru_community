<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Jostru Community')</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/style.css') }}?v=2">
    <link rel="manifest" href="{{ asset('manifest.json') }}">
    <meta name="theme-color" content="#22c55e">
    <script>
        if ('serviceWorker' in navigator) {
            window.addEventListener('load', () => {
                navigator.serviceWorker.register('/sw.js');
            });
        }
    </script>
    <script>
        // Init theme early to prevent flash
        let storedTheme = localStorage.getItem('jostru_theme');
        if (!storedTheme) {
            // Default check OS level
            storedTheme = window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light';
        }
        document.documentElement.setAttribute('data-theme', storedTheme);
        document.body.setAttribute('data-theme', storedTheme);
    </script>
    
    <style>
        .dashboard-watermark {
            position: relative;
        }

        .dashboard-watermark::before {
            content: "";
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            width: 50vw;
            height: 50vw;
            max-width: 600px;
            max-height: 600px;
            background-image: url("{{ asset('images/logo.png') }}");
            background-position: center;
            background-repeat: no-repeat;
            background-size: contain;
            opacity: 0.05;
            pointer-events: none;
            z-index: 0;
        }

        /* Make sure content is above watermark */
        .dashboard-watermark>* {
            position: relative;
            z-index: 1;
        }

        .theme-toggle-btn {
            background: none;
            border: none;
            color: var(--text-color);
            cursor: pointer;
            width: 38px;
            height: 38px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            background-color: var(--surface-color);
            box-shadow: var(--shadow-sm);
            border: 1px solid var(--border-color);
            margin-left: 10px;
        }

        .theme-toggle-btn:hover {
            background-color: var(--bg-color);
        }
    </style>
    <style media="(max-width: 768px)">
        .navbar .container {
            /* Keep single row on mobile — no column wrap */
            flex-wrap: nowrap;
            gap: 8px;
            padding: 8px 14px !important;
        }
        .nav-links {
            width: auto;
            justify-content: flex-end;
            flex-wrap: nowrap;
            gap: 6px;
            flex-shrink: 0;
        }
        .navbar-brand img {
            height: 26px !important;
        }
        .navbar-brand span {
            font-size: 0.95rem;
        }
        .navbar .btn {
            padding: 0.38rem 0.85rem !important;
            font-size: 0.82rem !important;
        }
        .theme-toggle-btn {
            width: 32px !important;
            height: 32px !important;
            margin-left: 4px !important;
        }
    </style>
    @stack('styles')
</head>

<body>
    <nav class="navbar">
        <div class="container" style="display: flex; align-items: center; justify-content: space-between;">
            <a href="/" class="navbar-brand" style="display: flex; align-items: center; gap: 10px;">
                <img src="{{ asset('images/logo.png') }}" alt="Jostru Logo" style="height: 35px; width: auto;">
                <span>Jostru</span>
            </a>

            <div class="nav-links" style="display: flex; align-items: center;">
                @guest
                    <a href="{{ route('login') }}" class="btn btn-outline" style="margin-right: 10px;">Masuk</a>
                    <a href="{{ route('register') }}" class="btn btn-primary">Daftar</a>
                @else
                    @if(in_array(auth()->user()->role, ['admin', 'superadmin']))
                        <a href="{{ route('admin.dashboard') }}" style="margin-right: 15px;">Admin Panel</a>
                        <a href="{{ route('member.profile') }}" style="margin-right: 15px;">Profil</a>
                    @else
                        <a href="{{ route('dashboard') }}" style="margin-right: 15px;">Dashboard</a>
                        <a href="{{ route('member.profile') }}" style="margin-right: 15px;">Profil</a>
                    @endif
                    <form action="{{ route('logout') }}" method="POST" style="display:inline; margin-right: 10px;">
                        @csrf
                        <button type="submit" class="btn btn-outline">Keluar</button>
                    </form>
                @endguest

                <!-- Dark Mode Toggle Button -->
                <button type="button" class="theme-toggle-btn" onclick="toggleTheme()" title="Toggle Dark/Light Mode">
                    <svg id="icon-sun" style="display: none;" width="20" height="20" fill="none" stroke="currentColor"
                        stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z">
                        </path>
                    </svg>
                    <svg id="icon-moon" style="display: none;" width="20" height="20" fill="none" stroke="currentColor"
                        stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z">
                        </path>
                    </svg>
                </button>
            </div>
        </div>
    </nav>

    <main class="dashboard-watermark">
        @yield('content')
    </main>

    <footer class="container text-center text-muted"
        style="padding: 3rem 0; margin-top: auto; position: relative; z-index: 1;">
        <div style="margin-bottom: 1.5rem;">
            <a href="https://www.instagram.com/jostru_community/" target="_blank" class="text-muted" style="text-decoration: none; display: inline-flex; align-items: center; gap: 8px; font-weight: 600;">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-instagram"><rect x="2" y="2" width="20" height="20" rx="5" ry="5"></rect><path d="M16 11.37A4 4 0 1 1 12.63 8 4 4 0 0 1 16 11.37z"></path><line x1="17.5" y1="6.5" x2="17.51" y2="6.5"></line></svg>
                &#64;jostru_community
            </a>
        </div>
        &copy; {{ date('Y') }} Jostru Community. All rights reserved.
    </footer>

    <script>
        // Set correct icon on load
        function updateThemeIcon() {
            let t = document.body.getAttribute('data-theme');
            if (t === 'dark') {
                document.getElementById('icon-sun').style.display = 'block';
                document.getElementById('icon-moon').style.display = 'none';
            } else {
                document.getElementById('icon-sun').style.display = 'none';
                document.getElementById('icon-moon').style.display = 'block';
            }
        }

        function toggleTheme() {
            let current = document.body.getAttribute('data-theme');
            let next = (current === 'dark') ? 'light' : 'dark';
            document.documentElement.setAttribute('data-theme', next);
            document.body.setAttribute('data-theme', next);
            localStorage.setItem('jostru_theme', next);
            updateThemeIcon();
        }

        document.addEventListener('DOMContentLoaded', updateThemeIcon);
        updateThemeIcon(); // Run immediately for fast render
    </script>

    @auth
    <!-- Mobile Bottom Navigation (Visible only on mobile for logged in users) -->
    <nav class="bottom-nav d-md-none" style="display: none;">
        <div class="bottom-nav-container">
            @if(in_array(auth()->user()->role, ['admin', 'superadmin']))
                <a href="{{ route('admin.dashboard') }}" class="bottom-nav-item {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path></svg>
                    <span>Dasbor</span>
                </a>
                <a href="{{ route('admin.waste_deposits') }}" class="bottom-nav-item {{ request()->routeIs('admin.waste_deposits') ? 'active' : '' }}">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>
                    <span>Limbah</span>
                </a>
                <a href="{{ route('admin.productions') }}" class="bottom-nav-item {{ request()->routeIs('admin.productions') ? 'active' : '' }}">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"></path></svg>
                    <span>Produksi</span>
                </a>
                <a href="{{ route('admin.members') }}" class="bottom-nav-item {{ request()->routeIs('admin.members') ? 'active' : '' }}">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                    <span>Anggota</span>
                </a>
            @else
                <a href="{{ route('dashboard') }}" class="bottom-nav-item {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path></svg>
                    <span>Beranda</span>
                </a>
                <a href="{{ route('member.waste_report') }}" class="bottom-nav-item {{ request()->routeIs('member.waste_report') ? 'active' : '' }}">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>
                    <span>Setor</span>
                </a>
                <a href="{{ route('member.feed') }}" class="bottom-nav-item {{ request()->routeIs('member.feed') ? 'active' : '' }}">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z"></path></svg>
                    <span>Feed</span>
                </a>
            @endif
            <a href="{{ route('member.profile') }}" class="bottom-nav-item {{ request()->routeIs('member.profile') ? 'active' : '' }}">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                <span>Profil</span>
            </a>
        </div>
    </nav>

    @endauth

    @stack('scripts')
</body>

</html>