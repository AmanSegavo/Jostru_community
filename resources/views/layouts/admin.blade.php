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
            background: rgba(255, 255, 255, 0.7);
            border: 1px solid rgba(255, 255, 255, 0.5);
            border-radius: 24px;
            padding: 1.5rem;
            box-shadow: 0 20px 40px rgba(0,0,0,0.08);
            backdrop-filter: blur(25px);
            -webkit-backdrop-filter: blur(25px);
            position: sticky;
            top: 100px;
            height: max-content;
            transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            z-index: 1040;
        }
        
        [data-theme="dark"] .admin-sidebar {
            background: rgba(17, 24, 39, 0.7);
            border-color: rgba(255,255,255,0.1);
            box-shadow: 0 20px 40px rgba(0,0,0,0.4);
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
            margin-bottom: 0.4rem;
            color: var(--text-secondary);
            text-decoration: none;
            border-radius: 12px;
            font-weight: 600;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }
        .nav-item-admin:hover, .nav-item-admin.active {
            background: rgba(34, 197, 94, 0.1);
            color: var(--primary-accent);
            transform: translateX(5px);
            padding-left: 1.2rem;
            border-left: 4px solid var(--primary-accent);
        }

        /* =======================================
           TAMPILAN PC SAJA (Tombol Toggle)
           ======================================= */
        body.pc-sidebar-hidden .admin-sidebar {
            margin-left: -320px;
            opacity: 0;
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
                transition: all 0.4s cubic-bezier(0.25, 1, 0.5, 1) !important;
                overflow-y: auto !important;
                display: block !important;
                opacity: 0 !important;
                visibility: hidden !important;
            }
            
            /* MUNCUL KETIKA DIGESER JARI */
            body.mobile-sidebar-open .admin-sidebar {
                left: 0 !important;
                opacity: 1 !important;
                visibility: visible !important;
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

    <!-- TOMBOL TOGGLE UNTUK HP -->
    <button class="btn d-lg-none w-100 mb-3 d-flex align-items-center justify-content-center gap-2" onclick="document.body.classList.add('mobile-sidebar-open')" style="background: var(--primary-color); color: white; border-radius: 12px; font-weight: 700; padding: 12px; border: none; box-shadow: 0 4px 10px rgba(34, 197, 94, 0.3);">
        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16"></path></svg>
        Buka Menu Admin
    </button>

    <div class="admin-wrapper">
        
        <!-- SIDEBAR -->
        <aside class="admin-sidebar glass">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h3 class="text-sm text-muted text-uppercase m-0" style="font-weight: 800;">Administrator</h3>
                <!-- Close Button for Mobile -->
                <button class="btn btn-sm d-lg-none" onclick="document.body.classList.remove('mobile-sidebar-open')" style="background: rgba(239,68,68,0.1); color: #ef4444; border-radius: 50%; width: 32px; height: 32px; display: flex; align-items: center; justify-content: center;">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16"><path d="M4.646 4.646a.5.5 0 0 1 .708 0L8 7.293l2.646-2.647a.5.5 0 0 1 .708.708L8.707 8l2.647 2.646a.5.5 0 0 1-.708.708L8 8.707l-2.646 2.647a.5.5 0 0 1-.708-.708L7.293 8 4.646 5.354a.5.5 0 0 1 0-.708z"/></svg>
                </button>
            </div>

            <nav class="d-flex flex-column w-100">
                <a href="{{ route('admin.dashboard') }}" class="nav-item-admin {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">📊 Dashboard</a>
                
                @if(auth()->user()->role === 'superadmin')
                <a href="{{ route('admin.erp.index') }}" class="nav-item-admin {{ request()->is('admin/erp*') ? 'active' : '' }}">🌐 ERP System</a>
                @endif
                
                @if(strtolower(auth()->user()->role) === 'superadmin' || strtolower(auth()->user()->role) === 'admin' || auth()->user()->can_manage_members)
                <a href="{{ route('admin.members') }}" class="nav-item-admin {{ request()->routeIs('admin.members') ? 'active' : '' }}">👥 Manajemen Anggota</a>
                @endif
                
                @if(strtolower(auth()->user()->role) === 'superadmin' || strtolower(auth()->user()->role) === 'admin' || auth()->user()->can_manage_waste)
                <a href="{{ route('admin.waste_deposits') }}" class="nav-item-admin {{ request()->routeIs('admin.waste_deposits') ? 'active' : '' }}">♻️ Setoran Limbah</a>
                <a href="{{ route('admin.waste_categories') }}" class="nav-item-admin {{ request()->routeIs('admin.waste_categories') ? 'active' : '' }}">🗂️ Kategori Limbah</a>
                @endif
                
                <h3 class="mb-2 mt-4 text-sm text-muted text-uppercase" style="font-weight: 800; padding-left:1rem;">ERP & Agribisnis</h3>
                <a href="{{ route('admin.divisions') }}" class="nav-item-admin {{ request()->routeIs('admin.divisions') || request()->routeIs('admin.divisions.*') ? 'active' : '' }}" style="border-left: 3px solid #22c55e;">🏢 Manajemen Divisi</a>
                <a href="{{ route('admin.productions') }}" class="nav-item-admin {{ request()->routeIs('admin.productions') ? 'active' : '' }}">🏭 Hasil Produksi</a>
                <a href="{{ route('admin.rabs') }}" class="nav-item-admin {{ request()->routeIs('admin.rabs') ? 'active' : '' }}">📑 Pengajuan RAB</a>
                
                <h3 class="mb-2 mt-4 text-sm text-muted text-uppercase" style="font-weight: 800; padding-left:1rem;">CMS & Konten</h3>
                <a href="{{ route('admin.media') }}" class="nav-item-admin {{ request()->routeIs('admin.media') ? 'active' : '' }}">🖼️ Galeri & Media CMS</a>
                <a href="{{ route('admin.settings') }}" class="nav-item-admin {{ request()->routeIs('admin.settings') ? 'active' : '' }}">⚙️ Pengaturan Sistem</a>
                <a href="{{ route('admin.ai_analytics') }}" class="nav-item-admin {{ request()->routeIs('admin.ai_analytics') ? 'active' : '' }}">
                    <span style="color:#22c55e; font-weight:700;">✨ Analisis AI (Colab)</span>
                </a>
                
                @if(strtolower(auth()->user()->role) === 'superadmin' || strtolower(auth()->user()->role) === 'admin' || auth()->user()->can_manage_posts)
                <a href="{{ route('admin.posts') }}" class="nav-item-admin {{ request()->routeIs('admin.posts') ? 'active' : '' }}">📰 Community Feed</a>
                <a href="{{ route('admin.events') }}" class="nav-item-admin {{ request()->routeIs('admin.events') ? 'active' : '' }}">📅 Agenda Event</a>
                @endif
                
                @if(strtolower(auth()->user()->role) === 'superadmin' || strtolower(auth()->user()->role) === 'admin' || auth()->user()->can_manage_members)
                <a href="{{ route('admin.cards') }}" class="nav-item-admin {{ request()->routeIs('admin.cards') ? 'active' : '' }}">💳 Kartu Digital</a>
                @endif
                
                @if(strtolower(auth()->user()->role) === 'superadmin' || strtolower(auth()->user()->role) === 'admin' || auth()->user()->can_manage_finances)
                <a href="{{ route('admin.finances') }}" class="nav-item-admin {{ request()->routeIs('admin.finances') ? 'active' : '' }}">💰 Laporan Keuangan</a>
                @endif
                
                <a href="{{ route('admin.messages') }}" class="nav-item-admin {{ request()->routeIs('admin.messages') ? 'active' : '' }}">✉️ Pesan Masuk</a>
                
                @if(strtolower(auth()->user()->role) === 'superadmin')
                <a href="{{ route('admin.logs') }}" class="nav-item-admin {{ request()->routeIs('admin.logs') ? 'active' : '' }}">📋 Log Aktivitas</a>
                
                <h3 class="mb-2 mt-4 text-sm text-muted text-uppercase" style="font-weight: 800; padding-left:1rem;">Superadmin Eksekutif</h3>
                <a href="{{ route('admin.data_lake.index') }}" class="nav-item-admin {{ request()->routeIs('admin.data_lake.*') ? 'active' : '' }}" style="background: {{ request()->routeIs('admin.data_lake.*') ? '' : 'rgba(59,130,246,0.06)' }}; border-left: 3px solid #3b82f6;">
                    <span style="color: #3b82f6; font-weight:700;"><i class="bi bi-cpu"></i> Data Lake Analytics</span>
                </a>
                <a href="{{ route('admin.dividends.index') }}" class="nav-item-admin {{ request()->routeIs('admin.dividends.*') ? 'active' : '' }}" style="background: {{ request()->routeIs('admin.dividends.*') ? '' : 'rgba(245,158,11,0.06)' }}; border-left: 3px solid #d97706;">
                    <span style="color: #d97706; font-weight:700;">📜 Sertifikat Dividen</span>
                </a>
                @endif


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
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show shadow-sm" role="alert" style="border-radius: 12px; font-weight: 600; border-left: 5px solid #198754;">
                    <i class="bi bi-check-circle-fill me-2"></i>{!! session('success') !!}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif
            
            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show shadow-sm" role="alert" style="border-radius: 12px; font-weight: 600; border-left: 5px solid #dc3545;">
                    <i class="bi bi-exclamation-triangle-fill me-2"></i>{!! session('error') !!}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            @if($errors->any())
                <div class="alert alert-danger alert-dismissible fade show shadow-sm" role="alert" style="border-radius: 12px; border-left: 5px solid #dc3545;">
                    <i class="bi bi-exclamation-triangle-fill me-2"></i><strong class="me-1">Terjadi Kesalahan:</strong>
                    <ul class="mb-0 mt-2" style="font-weight: 500;">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

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

    @php
        $isChatbotActive = \App\Models\Setting::getVal('chatbot_active', '0') === '1';
    @endphp

    @if($isChatbotActive)
    <!-- AI CHATBOT WIDGET -->
    <div id="chatbotWidgetContainer" style="position: fixed; bottom: 30px; right: 30px; z-index: 9999;">
        <!-- Teks Bantuan Mengambang (Sering Ganti) -->
        <div id="chatbotGreeting" class="shadow-sm d-none d-md-block" style="position: absolute; right: 70px; top: 10px; background: white; padding: 8px 15px; border-radius: 12px; font-size: 13px; font-weight: bold; color: var(--text-color); white-space: nowrap; border: 1px solid #e2e8f0;">
            👋 Butuh bantuan sistem?
        </div>

        <!-- Tombol Buka Tutup Chat -->
        <button id="chatbotToggleBtn" onclick="toggleChatbot()" class="btn shadow-lg" style="width: 60px; height: 60px; border-radius: 50%; background: linear-gradient(135deg, #6366f1, #3b82f6); color: white; border: none; display: flex; align-items: center; justify-content: center; transition: transform 0.2s;">
            <i class="bi bi-robot fs-3"></i>
        </button>

        <!-- Kotak Chat Panel -->
        <div id="chatbotPanel" class="card shadow-lg border-0" style="display: none; position: absolute; bottom: 75px; right: 0; width: 350px; height: 450px; border-radius: 16px; overflow: hidden; display: flex; flex-direction: column;">
            <div class="card-header border-0 text-white p-3" style="background: linear-gradient(135deg, #6366f1, #3b82f6);">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="mb-0 fw-bold"><i class="bi bi-robot"></i> Jostru Assistant</h6>
                        <small style="font-size: 11px; opacity: 0.8;">@if(\App\Models\Setting::getVal('llm_api_key') != '') Mode: AI (LLM) @else Mode: Rule-Based (Offline) @endif</small>
                    </div>
                    <button class="btn btn-sm text-white border-0" onclick="toggleChatbot()"><i class="bi bi-x-lg"></i></button>
                </div>
            </div>
            <div class="card-body p-3" id="chatbotMessages" style="overflow-y: auto; flex: 1; background: #f8fafc; display: flex; flex-direction: column; gap: 10px;">
                <div class="d-flex justify-content-start">
                    <div class="p-2" style="background: #e2e8f0; color: #334155; border-radius: 12px 12px 12px 0; max-width: 85%; font-size: 13px;">
                        Halo! Saya asisten AI Jostru. Ada yang bisa saya bantu terkait Sistem Holding Company ini?
                    </div>
                </div>
            </div>
            <div class="card-footer p-2 bg-white border-top">
                <form id="chatbotForm" class="d-flex gap-2 mb-0" onsubmit="sendChatMessage(event)">
                    <input type="text" id="chatInput" class="form-control border-0 bg-light" placeholder="Ketik pertanyaan..." style="border-radius: 20px; font-size: 13px;" required>
                    <button type="submit" class="btn btn-primary" style="border-radius: 50%; width: 40px; height: 40px; padding: 0; display: flex; align-items: center; justify-content: center;"><i class="bi bi-send-fill"></i></button>
                </form>
            </div>
        </div>
    </div>

    <style>
        #chatbotToggleBtn:hover { transform: scale(1.1); }
        .chat-msg-user { background: #3b82f6; color: white; border-radius: 12px 12px 0 12px; max-width: 85%; font-size: 13px; padding: 8px 12px; align-self: flex-end; }
        .chat-msg-bot { background: #e2e8f0; color: #334155; border-radius: 12px 12px 12px 0; max-width: 85%; font-size: 13px; padding: 8px 12px; align-self: flex-start; }
        .typing-indicator span { display: inline-block; width: 6px; height: 6px; background-color: #94a3b8; border-radius: 50%; margin: 0 2px; animation: bounce 1.4s infinite ease-in-out both; }
        .typing-indicator span:nth-child(1) { animation-delay: -0.32s; }
        .typing-indicator span:nth-child(2) { animation-delay: -0.16s; }
        @keyframes bounce { 0%, 80%, 100% { transform: scale(0); } 40% { transform: scale(1); } }
        /* Hide greeting on mobile */
        @media (max-width: 768px) {
            #chatbotWidgetContainer { bottom: 20px; right: 20px; }
            #chatbotPanel { width: 300px; right: -10px; }
        }
    </style>

    <script>
        const chatPanel = document.getElementById('chatbotPanel');
        const chatInput = document.getElementById('chatInput');
        const chatMessages = document.getElementById('chatbotMessages');
        const chatGreeting = document.getElementById('chatbotGreeting');

        // Hide greeting after 5 seconds
        setTimeout(() => { if(chatGreeting) chatGreeting.style.opacity = '0'; }, 5000);
        
        // Hide panel initially but correctly
        chatPanel.style.display = 'none';

        function toggleChatbot() {
            if(chatPanel.style.display === 'none') {
                chatPanel.style.display = 'flex';
                chatInput.focus();
                if(chatGreeting) chatGreeting.style.display = 'none';
            } else {
                chatPanel.style.display = 'none';
            }
        }

        async function sendChatMessage(e) {
            e.preventDefault();
            const msg = chatInput.value.trim();
            if(!msg) return;

            // Append User Message
            appendMessage(msg, 'user');
            chatInput.value = '';

            // Append Typing Indicator
            const typingId = 'typing-' + Date.now();
            const typingHtml = `<div id="${typingId}" class="d-flex justify-content-start"><div class="chat-msg-bot typing-indicator"><span></span><span></span><span></span></div></div>`;
            chatMessages.insertAdjacentHTML('beforeend', typingHtml);
            scrollToBottom();

            try {
                const response = await fetch("{{ url('/admin/chatbot/message') }}", {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({ message: msg })
                });
                
                const data = await response.json();
                document.getElementById(typingId).remove();
                
                if(data.success) {
                    appendMessage(data.reply, 'bot');
                } else {
                    appendMessage("Maaf, terjadi kesalahan koneksi.", 'bot');
                }
            } catch (err) {
                document.getElementById(typingId).remove();
                appendMessage("Gagal menghubungi server.", 'bot');
            }
        }

        function appendMessage(text, sender) {
            const cssClass = sender === 'user' ? 'chat-msg-user' : 'chat-msg-bot';
            const wrapperClass = sender === 'user' ? 'justify-content-end' : 'justify-content-start';
            
            // Format line breaks for bot
            const formattedText = text.replace(/\n/g, '<br>');
            
            const html = `
                <div class="d-flex ${wrapperClass}">
                    <div class="${cssClass}">${formattedText}</div>
                </div>
            `;
            chatMessages.insertAdjacentHTML('beforeend', html);
            scrollToBottom();
        }

        function scrollToBottom() {
            chatMessages.scrollTop = chatMessages.scrollHeight;
        }
    </script>
    @endif

@endsection