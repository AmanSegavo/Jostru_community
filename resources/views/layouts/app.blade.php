<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    

    
    <title>@yield('title', 'Jostru Community')</title>

    <!-- Preload Fonts & Frameworks -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=Outfit:wght@500;600;700;800;900&display=swap" rel="stylesheet">
    
    <!-- Defer Bootstrap & Style.css to Prevent Render-Blocking -->
    <link rel="preload" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" as="style" onload="this.onload=null;this.rel='stylesheet'">
    <noscript><link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"></noscript>
    
    <link rel="stylesheet" href="{{ asset('css/style.css') }}?v=5">
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
        /* NOTIFICATION DROPDOWN MOBILE FIX */
        @media (max-width: 576px) {
            .notif-dropdown-menu {
                width: 95vw !important;
                max-width: 95vw !important;
                position: fixed !important;
                top: 70px !important;
                left: 2.5vw !important;
                right: auto !important;
                transform: none !important;
                margin: 0 !important;
            }
        }
        
        /* PENGATURAN WARNA MODE TERANG & GELAP */
        :root, [data-theme="light"] {
            --bg-color: #f8f9fa;
            --surface-color: #ffffff;
            --text-primary: #1f2937;
            --text-secondary: #6b7280;
            --border-color: rgba(0, 0, 0, 0.08);
            --glass-bg: rgba(255, 255, 255, 0.7);
            --glass-border: rgba(255, 255, 255, 0.5);
            --glass-shadow: 0 8px 32px rgba(0, 0, 0, 0.05);
            --primary-accent: #22c55e;
            --primary-accent-hover: #16a34a;
        }

        [data-theme="dark"] {
            --bg-color: #121212;
            --surface-color: #1e1e1e;
            --text-primary: #f9fafb;
            --text-secondary: #9ca3af;
            --border-color: rgba(255, 255, 255, 0.1);
            --glass-bg: rgba(30, 30, 30, 0.7);
            --glass-border: rgba(255, 255, 255, 0.05);
            --glass-shadow: 0 8px 32px rgba(0, 0, 0, 0.4);
            --primary-accent: #22c55e;
            --primary-accent-hover: #4ade80;
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
            border: 1px solid var(--glass-border);
            box-shadow: var(--glass-shadow);
        }

        /* Table Responsive Khusus Android/PC (Scrollable Tanpa Terpotong) */
        .table-responsive-wrapper {
            background: var(--surface-color);
            border-radius: 16px;
            padding: 1rem;
            box-shadow: var(--glass-shadow);
            border: 1px solid var(--border-color);
            width: 100%;
            overflow: hidden;
            margin-bottom: 1.5rem;
        }
        .table-responsive {
            display: block;
            width: 100%;
            overflow-x: auto;
            -webkit-overflow-scrolling: touch; /* Supaya lancar di iOS/Android */
            white-space: nowrap; /* Mencegah teks turun agar layout tabel rapi */
        }
        .table-responsive::-webkit-scrollbar {
            height: 8px;
        }
        .table-responsive::-webkit-scrollbar-thumb {
            background-color: var(--border-color);
            border-radius: 4px;
        }
        .table-responsive table {
            min-width: 800px; /* Minimal ukuran agar kolom tidak tergencet */
            width: 100%;
        }

        /* Elemen Kustom Global Premium */
        .card {
            background: var(--surface-color);
            border: 1px solid var(--border-color);
            border-radius: 20px;
            box-shadow: var(--glass-shadow);
            transition: transform 0.3s cubic-bezier(0.4, 0, 0.2, 1), box-shadow 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }
        .btn {
            border-radius: 50px;
            font-weight: 600;
            padding: 0.5rem 1.5rem;
            transition: all 0.3s ease;
        }
        .btn-primary {
            background: linear-gradient(135deg, var(--primary-accent), var(--primary-accent-hover));
            border: none;
            box-shadow: 0 4px 15px rgba(34, 197, 94, 0.3);
        }
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(34, 197, 94, 0.4);
        }
        .form-control, .form-select {
            border-radius: 12px;
            border: 1px solid var(--border-color);
            background: var(--bg-color);
            color: var(--text-primary);
            padding: 0.75rem 1rem;
            transition: all 0.3s ease;
        }
        .form-control:focus, .form-select:focus {
            box-shadow: 0 0 0 3px rgba(34, 197, 94, 0.2);
            border-color: var(--primary-accent);
        }

        /* Animations */
        .hover-lift {
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }
        .hover-lift:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 35px rgba(34, 197, 94, 0.15);
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
            background: var(--glass-bg);
            border: 1px solid var(--glass-border);
        }

        .nav-btn {
            border-radius: 50px;
            padding: 0.4rem 1.2rem;
            font-weight: 600;
            font-size: 0.9rem;
            transition: all 0.3s ease;
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
            backdrop-filter: blur(24px);
            -webkit-backdrop-filter: blur(24px);
            padding-bottom: env(safe-area-inset-bottom);
        }
        .bottom-nav-item { color: var(--text-secondary); transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1); }
        .bottom-nav-item.active { color: var(--primary-accent); }
        .bottom-nav-item:active { transform: scale(0.9); }
        
        /* Page Transition Loader */
        .page-transition-overlay {
            position: fixed;
            top: 0; left: 0; width: 100vw; height: 100vh;
            background: var(--bg-color);
            z-index: 999999;
            display: flex;
            align-items: center;
            justify-content: center;
            opacity: 1;
            transition: opacity 0.5s cubic-bezier(0.4, 0, 0.2, 1);
        }
        .page-transition-overlay.hidden {
            opacity: 0;
            visibility: hidden;
            pointer-events: none;
        }
        .loader-spinner {
            width: 48px;
            height: 48px;
            border: 5px solid rgba(34, 197, 94, 0.2);
            border-top-color: var(--primary-accent);
            border-radius: 50%;
            animation: spin 1s linear infinite;
        }
        @keyframes spin { 100% { transform: rotate(360deg); } }

        /* Sembunyikan elemen download aplikasi jika sedang dibuka dalam Aplikasi Android (TWA) atau PWA */
        @media (display-mode: standalone) {
            #mobile-app-promo-section,
            .android-download-btn {
                display: none !important;
            }
        }
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
                navigator.serviceWorker.register('/sw.js').then(reg => {
                    reg.onupdatefound = () => {
                        const installingWorker = reg.installing;
                        installingWorker.onstatechange = () => {
                            if (installingWorker.state === 'installed' && navigator.serviceWorker.controller) {
                                // Versi baru ditemukan di sw.js, langsung otomatis reload halaman!
                                window.location.reload(true);
                            }
                        };
                    };
                }).catch(()=>{});
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
            
            // Hide page loader
            setTimeout(() => {
                const loader = document.getElementById('pageLoader');
                if(loader) {
                    loader.classList.add('hidden');
                    setTimeout(() => { loader.style.display = 'none'; }, 600);
                }
            }, 150);
        });
        
        // Show loader on page leave
        window.addEventListener('beforeunload', () => {
            const loader = document.getElementById('pageLoader');
            if(loader) {
                loader.style.display = 'flex';
                loader.classList.remove('hidden');
            }
        });
    </script>
    @stack('styles')
</head>
<body>
    @if(session()->has('impersonator_id'))
    <div style="background-color: #ef4444; color: white; text-align: center; padding: 10px; font-weight: bold; position: fixed; top: 0; width: 100%; z-index: 999999; box-shadow: 0 2px 10px rgba(0,0,0,0.2);">
        👁️ Anda sedang dalam Mode Pratinjau sebagai {{ auth()->user()->name }}. Data bersifat sensitif. 
        <a href="{{ route('impersonate.leave') }}" style="color: white; text-decoration: underline; margin-left: 10px; background: rgba(0,0,0,0.2); padding: 2px 8px; border-radius: 4px;">Kembali ke Admin</a>
    </div>
    @endif

    <!-- Page Loader -->
    <div id="pageLoader" class="page-transition-overlay">
        <div class="loader-spinner"></div>
    </div>

    <!-- Navbar Floating -->
    <nav class="navbar fixed-top mx-auto glass navbar-floating" style="{{ session()->has('impersonator_id') ? 'top: 40px;' : '' }}">
        <div class="container-fluid px-2">
            <!-- LOGO ICON DAN TEKS SELALU MUNCUL -->
            <a class="navbar-brand fw-bold m-0 d-flex align-items-center gap-2" href="{{ url('/') }}">
                <img src="{{ asset('images/logo.png') }}" alt="Jostru Logo" width="30" height="30" style="border-radius: 50%; object-fit: cover;">
                <span style="background:linear-gradient(135deg,#22c55e,#10b981);-webkit-background-clip:text;-webkit-text-fill-color:transparent; font-size: 1.4rem;">
                    Jostru
                </span>
            </a>
            
            <!-- Tampilan Desktop Nav -->
            <div class="d-none d-lg-flex align-items-center gap-4 fw-semibold" style="font-size: 0.95rem;">
                @guest
                    <a href="{{ url('/#visi-misi') }}" class="text-decoration-none" style="color: var(--text-primary);">Tentang Kami</a>
                    <a href="{{ url('/#tentang') }}" class="text-decoration-none" style="color: var(--text-primary);">Program</a>
                    <a href="{{ url('/#galeri') }}" class="text-decoration-none" style="color: var(--text-primary);">Galeri</a>
                    <a href="{{ url('/#kontak') }}" class="text-decoration-none" style="color: var(--text-primary);">Kontak & FAQ</a>
                @else
                    @if(auth()->user()->status === 'PENDING')
                        <span class="px-3 py-1 rounded-pill" style="color: #f59e0b; background: rgba(245, 158, 11, 0.1);">⏳ Menunggu Verifikasi</span>
                    @else
                        @if(in_array(auth()->user()->role, ['admin', 'superadmin']))
                            <a href="{{ route('admin.dashboard') }}" class="text-decoration-none px-3 py-1 rounded-pill" style="color: var(--primary-accent); background: rgba(34, 197, 94, 0.1);">💻 Panel Admin</a>
                        @endif
                        <a href="{{ route('dashboard') }}" class="text-decoration-none {{ request()->routeIs('dashboard') ? 'border-bottom border-success border-2' : '' }}" style="color: var(--text-primary); padding-bottom: 2px;">Beranda</a>
                        <a href="{{ route('member.waste_report') }}" class="text-decoration-none {{ request()->routeIs('member.waste_report*') ? 'border-bottom border-success border-2' : '' }}" style="color: var(--text-primary); padding-bottom: 2px;">Setor</a>
                        <a href="{{ route('member.feed') }}" class="text-decoration-none {{ request()->routeIs('member.feed*') ? 'border-bottom border-success border-2' : '' }}" style="color: var(--text-primary); padding-bottom: 2px;">Feed</a>
                        <a href="{{ route('member.chat.list') }}" class="text-decoration-none {{ request()->routeIs('member.chat*') ? 'border-bottom border-success border-2' : '' }}" style="color: var(--text-primary); padding-bottom: 2px;">Chat</a>
                    @endif
                @endguest
            </div>
            
            <!-- Mobile Menu Toggler -->
            <button class="d-lg-none btn btn-link text-decoration-none ms-auto" type="button" data-bs-toggle="collapse" data-bs-target="#mobileNav" style="color: var(--text-primary);">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="3" y1="12" x2="21" y2="12"></line><line x1="3" y1="6" x2="21" y2="6"></line><line x1="3" y1="18" x2="21" y2="18"></line></svg>
            </button>
            
            <div class="d-flex align-items-center gap-2 gap-md-3">
                @auth
                    @php
                        $unreadNotifications = \App\Models\Notification::where('user_id', auth()->id())->where('is_read', false)->latest()->get();
                    @endphp
                    <div class="dropdown">
                        <button class="btn btn-link text-decoration-none p-0 position-relative" type="button" id="notifDropdown" data-bs-toggle="dropdown" aria-expanded="false" style="color: var(--text-primary);">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"></path><path d="M13.73 21a2 2 0 0 1-3.46 0"></path></svg>
                            @if($unreadNotifications->count() > 0)
                                <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger" style="font-size: 0.6rem;">
                                    {{ $unreadNotifications->count() }}
                                </span>
                            @endif
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end shadow notif-dropdown-menu" aria-labelledby="notifDropdown" style="width: 320px; max-height: 400px; overflow-y: auto; border-radius: 16px; border: 1px solid var(--border-color); background: var(--surface-color);">
                            <li class="p-3 border-bottom d-flex justify-content-between align-items-center">
                                <h6 class="mb-0 fw-bold">Notifikasi</h6>
                                @if($unreadNotifications->count() > 0)
                                <form action="{{ route('notifications.read_all') }}" method="POST" class="m-0">
                                    @csrf
                                    <button type="submit" class="btn btn-sm btn-link text-success p-0 text-decoration-none">Tandai dibaca</button>
                                </form>
                                @endif
                            </li>
                            @forelse($unreadNotifications as $notif)
                                <li>
                                    <form action="{{ route('notifications.read', $notif->id) }}" method="POST">
                                        @csrf
                                        <button type="submit" class="dropdown-item py-3 border-bottom" style="white-space: normal;">
                                            <div class="fw-bold" style="font-size: 0.9rem;">{{ $notif->title }}</div>
                                            <div class="text-muted" style="font-size: 0.8rem;">{{ $notif->message }}</div>
                                            <small class="text-muted" style="font-size: 0.7rem;">{{ $notif->created_at->diffForHumans() }}</small>
                                        </button>
                                    </form>
                                </li>
                            @empty
                                <li>
                                    <div class="p-4 text-center text-muted">
                                        <div class="fs-4 mb-2">📭</div>
                                        Tidak ada notifikasi baru
                                    </div>
                                </li>
                            @endforelse
                        </ul>
                    </div>
                @endauth

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
            
            <!-- Mobile Menu Panel -->
            <div class="collapse w-100 mt-3 d-lg-none" id="mobileNav">
                <div class="d-flex flex-column gap-3 p-3 glass" style="border-radius: 12px;">
                    @guest
                        <a href="{{ url('/#visi-misi') }}" class="text-decoration-none fw-semibold" style="color: var(--text-primary);">Tentang Kami</a>
                        <a href="{{ url('/#tentang') }}" class="text-decoration-none fw-semibold" style="color: var(--text-primary);">Program</a>
                        <a href="{{ url('/#galeri') }}" class="text-decoration-none fw-semibold" style="color: var(--text-primary);">Galeri</a>
                        <a href="{{ url('/#kontak') }}" class="text-decoration-none fw-semibold" style="color: var(--text-primary);">Kontak & FAQ</a>
                    @else
                        @if(auth()->user()->status === 'PENDING')
                            <span class="text-decoration-none fw-semibold p-2 rounded text-center" style="color: #f59e0b; background: rgba(245, 158, 11, 0.1);">⏳ Menunggu Verifikasi Admin</span>
                        @else
                            @if(in_array(auth()->user()->role, ['admin', 'superadmin']))
                                <a href="{{ route('admin.dashboard') }}" class="text-decoration-none fw-semibold p-2 rounded" style="color: var(--primary-accent); background: rgba(34, 197, 94, 0.1);">💻 Panel Admin</a>
                            @endif
                            <a href="{{ route('dashboard') }}" class="text-decoration-none fw-semibold {{ request()->routeIs('dashboard') ? 'text-success' : '' }}" style="color: var(--text-primary);">🏠 Beranda</a>
                            <a href="{{ route('member.waste_report') }}" class="text-decoration-none fw-semibold {{ request()->routeIs('member.waste_report*') ? 'text-success' : '' }}" style="color: var(--text-primary);">♻️ Setor Limbah</a>
                            <a href="{{ route('member.feed') }}" class="text-decoration-none fw-semibold {{ request()->routeIs('member.feed*') ? 'text-success' : '' }}" style="color: var(--text-primary);">📰 Feed Komunitas</a>
                            <a href="{{ route('member.profile') }}" class="text-decoration-none fw-semibold {{ request()->routeIs('member.profile*') ? 'text-success' : '' }}" style="color: var(--text-primary);">👤 Profil Saya</a>
                        @endif
                    @endguest
                </div>
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
    @if(!request()->is('admin*') && auth()->user()->status !== 'PENDING')
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
    @endif
    @endauth

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" defer></script>
    
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