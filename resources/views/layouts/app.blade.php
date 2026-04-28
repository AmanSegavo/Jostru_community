<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Jostru Community')</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
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

        /* Tambahkan di style.css */
        @media (max-width: 768px) {
            .navbar .container {
                flex-direction: column;
                gap: 10px;
            }

            .nav-links {
                width: 100%;
                justify-content: center;
                flex-wrap: wrap;
            }
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
                @jostru_community
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

    @stack('scripts')
</body>

</html>