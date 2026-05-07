@extends('layouts.app')
@section('title', 'Admin Panel - Jostru')

@section('content')
    <!-- CSS LANGSUNG DIMASUKKAN KE SINI AGAR PASTI TERBACA BROWSER -->
    <style>
        /* WRAPPER UTAMA KONTEN ADMIN */
        .admin-wrapper {
            display: flex;
            flex-direction: row;
            align-items: flex-start;
            gap: 2rem;
            width: 100%;
            position: relative;
        }

        /* =======================================
           SIDEBAR DEFAULT (PC / LAPTOP)
           ======================================= */
        .admin-sidebar {
            width: 280px;
            flex-shrink: 0;
            background: var(--surface-color);
            border: 1px solid var(--border-color);
            border-radius: 16px;
            padding: 1.5rem;
            box-shadow: 0 10px 30px rgba(0,0,0,0.05);
            position: sticky;
            top: 100px; /* Jarak dari navbar atas */
            height: max-content;
            transition: all 0.3s ease;
            z-index: 1040;
        }

        /* AREA KONTEN KANAN */
        .admin-content {
            flex-grow: 1;
            min-width: 0;
            width: 100%;
        }

        /* DESAIN MENU (LINK) SIDEBAR */
        .nav-item-admin {
            display: flex;
            align-items: center;
            padding: 0.8rem 1rem;
            margin-bottom: 0.3rem;
            color: var(--text-secondary);
            font-weight: 600;
            border-radius: 10px;
            text-decoration: none;
            transition: all 0.2s;
        }
        .nav-item-admin:hover, .nav-item-admin.active {
            background: rgba(34, 197, 94, 0.1);
            color: #22c55e !important;
            transform: translateX(4px);
        }

        /* =======================================
           TAMPILAN PC SAJA (Tombol Toggle)
           ======================================= */
        body.pc-sidebar-hidden .admin-sidebar {
            display: none !important;
        }
        
        .btn-toggle-pc {
            position: fixed;
            bottom: 30px;
            left: 30px;
            width: 50px;
            height: 50px;
            background: #22c55e;
            color: white;
            border: none;
            border-radius: 50%;
            box-shadow: 0 4px 15px rgba(34, 197, 94, 0.4);
            z-index: 1050;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: transform 0.2s;
        }
        .btn-toggle-pc:hover { transform: scale(1.1); }

        /* =======================================
           TAMPILAN MOBILE / ANDROID (< 992px)
           ======================================= */
        @media (max-width: 991px) {
            .admin-wrapper {
                flex-direction: column !important;
                gap: 1rem;
            }
            
            /* PAKSA SIDEBAR JADI MENU MELUNCUR DI HP */
            .admin-sidebar {
                position: fixed !important;
                top: 0 !important;
                left: -350px !important; /* Paksa sembunyi di luar layar */
                width: 280px !important;
                height: 100vh !important;
                max-height: 100vh !important;
                margin: 0 !important;
                padding: 1.5rem !important;
                border-radius: 0 !important;
                border: none !important;
                background: var(--surface-color) !important;
                box-shadow: 5px 0 25px rgba(0,0,0,0.5) !important;
                z-index: 999999 !important; /* Lapis paling atas anti-gagal */
                transition: left 0.3s ease-out !important;
                overflow-y: auto !important;
                display: block !important;
                opacity: 1 !important;
                visibility: visible !important;
            }
            
            /* MUNCUL KETIKA DIGESER JARI */
            body.mobile-sidebar-open .admin-sidebar {
                left: 0 !important;
            }

            /* EFEK KABUR DI BELAKANG SIDEBAR */
            .sidebar-overlay {
                position: fixed !important;
                top: 0 !important; 
                left: 0 !important; 
                width: 100vw !important; 
                height: 100vh !important;
                background: rgba(0,0,0,0.6) !important;
                z-index: 999990 !important; /* Tepat di bawah sidebar */
                opacity: 0;
                pointer-events: none;
                transition: opacity 0.3s ease !important;
                backdrop-filter: blur(3px);
            }
            
            body.mobile-sidebar-open .sidebar-overlay {
                opacity: 1 !important;
                pointer-events: auto !important;
            }
            
            /* HILANGKAN TOMBOL PC DI HP SECARA TOTAL */
            .btn-toggle-pc { 
                display: none !important; 
                opacity: 0 !important;
                pointer-events: none !important;
            }
        }
    </style>

    <!-- Tombol Toggle Khusus PC -->
    <button class="btn-toggle-pc" onclick="document.body.classList.toggle('pc-sidebar-hidden')" title="Tutup/Buka Sidebar">
        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h7"></path>
        </svg>
    </button>

    <!-- Overlay Kabur Mobile -->
    <div class="sidebar-overlay" onclick="document.body.classList.remove('mobile-sidebar-open')"></div>

    <div class="admin-wrapper">
        
        <!-- SIDEBAR -->
        <aside class="admin-sidebar glass">
            <h3 class="mb-4 text-sm text-muted text-uppercase" style="font-weight: 800;">Administrator</h3>

            <nav class="d-flex flex-column w-100">
                <a href="{{ route('admin.dashboard') }}" class="nav-item-admin {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">📊 Dashboard</a>
                <a href="{{ route('admin.members') }}" class="nav-item-admin {{ request()->routeIs('admin.members') ? 'active' : '' }}">👥 Manajemen Anggota</a>
                <a href="{{ route('admin.waste_deposits') }}" class="nav-item-admin {{ request()->routeIs('admin.waste_deposits') ? 'active' : '' }}">♻️ Setoran Limbah</a>
                <a href="{{ route('admin.productions') }}" class="nav-item-admin {{ request()->routeIs('admin.productions') ? 'active' : '' }}">🏭 Hasil Produksi (V1.2)</a>
                <a href="{{ route('admin.media') }}" class="nav-item-admin {{ request()->routeIs('admin.media') ? 'active' : '' }}">🖼️ Galeri & Media CMS</a>
                <a href="{{ route('admin.ai_analytics') }}" class="nav-item-admin {{ request()->routeIs('admin.ai_analytics') ? 'active' : '' }}">
                    <span style="color:#22c55e; font-weight:700;">✨ Analisis AI (Colab)</span>
                </a>
                <a href="{{ route('admin.posts') }}" class="nav-item-admin {{ request()->routeIs('admin.posts') ? 'active' : '' }}">📰 Community Feed</a>
                <a href="{{ route('admin.events') }}" class="nav-item-admin {{ request()->routeIs('admin.events') ? 'active' : '' }}">📅 Agenda Event</a>
                <a href="{{ route('admin.cards') }}" class="nav-item-admin {{ request()->routeIs('admin.cards') ? 'active' : '' }}">💳 Kartu Digital</a>
                <a href="{{ route('admin.finances') }}" class="nav-item-admin {{ request()->routeIs('admin.finances') ? 'active' : '' }}">💰 Laporan Keuangan</a>
                <a href="{{ route('admin.messages') }}" class="nav-item-admin {{ request()->routeIs('admin.messages') ? 'active' : '' }}">✉️ Pesan Masuk</a>
                <a href="{{ route('admin.logs') }}" class="nav-item-admin {{ request()->routeIs('admin.logs') ? 'active' : '' }}">📋 Log Aktivitas</a>

                <h3 class="mb-2 mt-4 text-sm text-muted text-uppercase" style="font-weight: 800; padding-left:1rem;">Ekosistem & API</h3>
                <a href="{{ route('admin.integrations') }}" class="nav-item-admin {{ request()->routeIs('admin.integrations') ? 'active' : '' }}" style="background: {{ request()->routeIs('admin.integrations') ? '' : 'rgba(99,102,241,0.06)' }}">
                    <span style="color: #6366f1; font-weight:700;">🔗 API & Integrasi OAuth</span>
                </a>
                <h3 class="mb-2 mt-4 text-sm text-muted text-uppercase" style="font-weight: 800; padding-left:1rem;">Akun Saya</h3>
                <a href="{{ route('member.profile') }}" class="nav-item-admin {{ request()->routeIs('member.profile') ? 'active' : '' }}">Profil & Keamanan</a>
            </nav>
        </aside>

        <!-- KONTEN UTAMA HALAMAN -->
        <main class="admin-content">
            @yield('admin_content')
        </main>
        
    </div>

    <!-- SCRIPT GESER MOBILE -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            let touchStartX = 0;
            let touchStartY = 0;
            let touchEndX = 0;
            let touchEndY = 0;
            const minSwipeDistance = 60;

            document.addEventListener('touchstart', e => {
                touchStartX = e.changedTouches[0].screenX;
                touchStartY = e.changedTouches[0].screenY;
            }, { passive: true });

            document.addEventListener('touchend', e => {
                if (window.innerWidth > 991) return;

                touchEndX = e.changedTouches[0].screenX;
                touchEndY = e.changedTouches[0].screenY;

                let swipeX = touchEndX - touchStartX;
                let swipeY = touchEndY - touchStartY;

                if (Math.abs(swipeX) > Math.abs(swipeY) && Math.abs(swipeX) > minSwipeDistance) {
                    const isSidebarOpen = document.body.classList.contains('mobile-sidebar-open');

                    // BUKA SIDEBAR: Geser ke KANAN dari Tepi Kiri layar
                    if (swipeX > 0 && touchStartX < 80 && !isSidebarOpen) {
                        document.body.classList.add('mobile-sidebar-open');
                    }
                    // TUTUP SIDEBAR: Geser ke KIRI
                    else if (swipeX < 0 && isSidebarOpen) {
                        document.body.classList.remove('mobile-sidebar-open');
                    }
                }
            }, { passive: true });
        });
    </script>
@endsection