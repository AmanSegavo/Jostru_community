@extends('layouts.app')

@section('title', 'Beranda Anggota')

@section('content')
<div class="container mt-4 animate-fade-in">
    <!-- Header Gaptek Friendly -->
    <div class="card p-0 glass mb-4 overflow-hidden border-0 shadow-sm" style="border-radius: 20px;">
        <div style="background: linear-gradient(135deg, #10b981, #059669); padding: 2rem; text-align: center;">
            <h2 style="color: white; font-weight: 800; margin-bottom: 0.5rem;">Halo, {{ auth()->user()->name }}! 👋</h2>
            <p style="color: rgba(255,255,255,0.9); font-weight: 500; font-size: 1.1rem; margin-bottom: 0;">Selamat Datang di Aplikasi Jostru</p>
        </div>
        <div style="padding: 1rem; text-align: center; background: white;">
            <div class="d-flex justify-content-center gap-2 flex-wrap">
                <a href="{{ route('member.profile') }}" class="btn btn-light" style="border-radius: 12px; font-weight: 600; border: 1px solid #e5e7eb;">⚙️ Atur Profil</a>
                @if(auth()->user()->status === 'AKTIF' || in_array(auth()->user()->role, ['admin', 'superadmin']))
                <a href="{{ route('member.card.editor') }}" class="btn btn-light" style="border-radius: 12px; font-weight: 600; border: 1px solid #e5e7eb;">🪪 Kartu Anggota</a>
                @endif
            </div>
        </div>
    </div>

    <!-- Section Aplikasi Mobile -->
    @if(auth()->user()->status === 'AKTIF' && (auth()->user()->can_use_app || in_array(auth()->user()->role, ['admin', 'superadmin'])))
    <div class="card p-4 glass mb-4" style="border-radius: 20px; border: 2px solid #8b5cf6; background: rgba(139, 92, 246, 0.05);">
        <h5 class="fw-bold mb-2" style="color: #6d28d9;">📱 Aplikasi Jostru Mobile & Desktop</h5>
        <p class="text-muted mb-3" style="font-size: 0.95rem;">
            Dapatkan pengalaman Jostru yang lebih cepat, aman, dan bisa digunakan saat Offline. Tersedia untuk Android, Windows, dan MacOS.
        </p>
        
        @if(auth()->user()->device_id)
            <div class="alert alert-success d-flex align-items-center" style="border-radius: 12px; border: 1px solid #10b981;">
                <div style="font-size: 2rem; margin-right: 15px;">🔒</div>
                <div>
                    <strong>Aplikasi Aktif & Terkunci di Perangkat Anda</strong><br>
                    <span style="font-size: 0.85rem;">Aplikasi Jostru Mobile saat ini sudah terhubung ke HP Anda. Demi keamanan, aplikasi tidak dapat diunduh atau dipasang di perangkat lain.</span>
                </div>
            </div>
            <form action="{{ route('member.app.reset') }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin MENCABUT akses perangkat lama? Anda harus login ulang di HP baru.');">
                @csrf
                <button type="submit" class="btn btn-outline-danger fw-bold" style="border-radius: 12px;">
                    🔄 Cabut Akses & Unduh Ulang
                </button>
            </form>
        @else
            @if(auth()->user()->pairing_code)
                <div class="alert alert-warning mb-3" style="border-radius: 12px; border: 2px dashed #f59e0b;">
                    <strong>Kode Pairing Anda:</strong> 
                    <span style="font-size: 1.5rem; font-family: monospace; letter-spacing: 3px; color: #b45309; display: inline-block; padding: 5px 15px; background: rgba(245, 158, 11, 0.2); border-radius: 8px;">{{ auth()->user()->pairing_code }}</span>
                    <br><span style="font-size: 0.8rem; color: #b45309;">Masukkan kode ini saat pertama kali membuka aplikasi. Kode akan kedaluwarsa dalam 1 Jam.</span>
                </div>
                <div class="d-flex flex-wrap gap-2 mb-2 android-download-btn">
                    <a href="{{ url('/downloads/jostru-app.apk') }}" class="btn fw-bold text-white" style="background: linear-gradient(135deg, #8b5cf6, #6d28d9); border-radius: 12px; padding: 0.75rem 1.5rem;" download>
                        ⬇️ Unduh Android (APK)
                    </a>
                    <a href="{{ url('/downloads/JostruDesktop.exe') }}" class="btn fw-bold text-white" style="background: linear-gradient(135deg, #3b82f6, #2563eb); border-radius: 12px; padding: 0.75rem 1.5rem;">
                        💻 Unduh Windows (.exe)
                    </a>
                    <a href="{{ url('/downloads/JostruDesktop.dmg') }}" class="btn fw-bold text-white" style="background: linear-gradient(135deg, #1f2937, #111827); border-radius: 12px; padding: 0.75rem 1.5rem;">
                        🍎 Unduh Mac (.dmg)
                    </a>
                </div>
                <form action="{{ route('member.app.reset') }}" method="POST" style="display: inline-block;">
                    @csrf
                    <button type="submit" class="btn btn-light fw-bold" style="border-radius: 12px;">Batal</button>
                </form>
            @else
                <div id="member-app-promo-section">
                    <button type="button" class="btn fw-bold text-white disabled" style="background: linear-gradient(135deg, #9ca3af, #6b7280); border-radius: 12px; padding: 0.75rem 2rem; cursor: not-allowed;" title="Aplikasi sedang dalam proses perakitan">
                        📲 Dapatkan Kode & Unduh Aplikasi (Segera)
                    </button>
                </div>

                <div id="member-desktop-pairing-form" style="display: none;">
                    <div class="alert alert-info d-flex align-items-center" style="border-radius: 12px; border: 1px solid #3b82f6;">
                        <div style="font-size: 2rem; margin-right: 15px;">💻</div>
                        <div>
                            <strong>Hubungkan Aplikasi PC Ini</strong><br>
                            <span style="font-size: 0.85rem;">Masukkan 6-Digit Kode Pairing yang Anda buat dari HP/Web untuk mengunci aplikasi di PC ini.</span>
                        </div>
                    </div>
                    <form method="POST" action="{{ route('member.app.bind') }}" class="d-flex gap-2 align-items-center">
                        @csrf
                        <input type="hidden" name="device_id" id="member_dashboard_hidden_device_id">
                        <input type="text" name="pairing_code" class="form-control fw-bold text-center" style="max-width: 200px; font-size: 1.2rem; letter-spacing: 3px;" placeholder="X X X X X X" required maxlength="6">
                        <button type="submit" class="btn fw-bold text-white" style="background: linear-gradient(135deg, #10b981, #059669); border-radius: 12px; padding: 0.75rem 1.5rem;">
                            🔗 Kunci Perangkat
                        </button>
                    </form>
                </div>
            @endif
        @endif
    </div>
    
    <script>
        // Desktop App Device Binding Logic for Member Dashboard
        if (window.jostruApp && window.jostruApp.isDesktopApp) {
            const promoSection = document.getElementById('member-app-promo-section');
            const pairingForm = document.getElementById('member-desktop-pairing-form');
            if (promoSection && pairingForm) {
                promoSection.style.display = 'none';
                pairingForm.style.display = 'block';
                
                window.jostruApp.getDeviceId().then(id => {
                    document.getElementById('member_dashboard_hidden_device_id').value = id;
                }).catch(err => console.error("Gagal mendapatkan Hardware ID", err));
            }
        }
    </script>
    @endif

    @if(auth()->user()->status === 'PENDING')
    {{-- Tampilan khusus untuk anggota yang belum di-ACC --}}
    <div class="card p-4 glass mb-4 text-center" style="border-radius: 20px; border: 2px solid #f59e0b; background: rgba(245, 158, 11, 0.05);">
        <div style="font-size: 4rem; margin-bottom: 1rem;">⏳</div>
        <h4 class="fw-bold" style="color: #b45309;">Akun Anda Sedang Menunggu Persetujuan</h4>
        <p class="text-muted mb-3">
            Terima kasih sudah bergabung di Jostru Community! Saat ini akun Anda masih dalam proses verifikasi oleh Admin.<br>
            <strong>Anda belum ditentukan divisinya.</strong>
        </p>
        <p class="text-muted mb-4" style="font-size: 0.9rem;">
            Sambil menunggu, silakan lengkapi formulir perkenalan diri agar Admin dapat mengenal Anda lebih baik dan mempercepat proses persetujuan.
        </p>
        <a href="{{ route('member.onboarding') }}" class="btn btn-warning fw-bold" style="border-radius: 12px; padding: 0.75rem 2rem; font-size: 1rem;">
            📝 Lengkapi Formulir Onboarding
        </a>
    </div>
    @else
    {{-- Menu utama hanya untuk anggota resmi (AKTIF) --}}
    <!-- Main Menu Grid (Mobile App Style) -->
    <h5 class="fw-bold mb-3" style="color: #374151; padding-left: 5px;">Menu Utama Aplikasi</h5>
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(140px, 1fr)); gap: 1rem; margin-bottom: 2rem;">
        
        <!-- Menu Lapor Sampah -->
        <a href="{{ route('member.waste_report') }}" class="card glass text-center text-decoration-none app-menu-card p-3" style="border-radius: 20px; border: 2px solid transparent;">
            <div style="font-size: 3.5rem; margin-bottom: 10px; line-height: 1;">♻️</div>
            <h6 style="font-weight: 800; color: #1f2937; margin: 0;">Lapor Setor Sampah</h6>
            <small class="text-muted" style="font-size: 0.75rem; font-weight: 600;">(Total: {{ $totalWeight ?? 0 }} Kg)</small>
        </a>

        <!-- Menu Berita -->
        <a href="{{ route('member.feed') }}" class="card glass text-center text-decoration-none app-menu-card p-3" style="border-radius: 20px; border: 2px solid transparent;">
            <div style="font-size: 3.5rem; margin-bottom: 10px; line-height: 1;">📰</div>
            <h6 style="font-weight: 800; color: #1f2937; margin: 0;">Berita & Info</h6>
            <small class="text-muted" style="font-size: 0.75rem; font-weight: 600;">Kabar Jostru</small>
        </a>

        <!-- Menu Jadwal -->
        <a href="{{ route('member.events') }}" class="card glass text-center text-decoration-none app-menu-card p-3" style="border-radius: 20px; border: 2px solid transparent;">
            <div style="font-size: 3.5rem; margin-bottom: 10px; line-height: 1;">📅</div>
            <h6 style="font-weight: 800; color: #1f2937; margin: 0;">Jadwal Kegiatan</h6>
            <small class="text-muted" style="font-size: 0.75rem; font-weight: 600;">Event Terdekat</small>
        </a>

        <!-- Menu Chat -->
        <a href="{{ route('member.chat.list') }}" class="card glass text-center text-decoration-none app-menu-card p-3" style="border-radius: 20px; border: 2px solid transparent;">
            <div style="font-size: 3.5rem; margin-bottom: 10px; line-height: 1;">💬</div>
            <h6 style="font-weight: 800; color: #1f2937; margin: 0;">Chat & Telepon</h6>
            <small class="text-muted" style="font-size: 0.75rem; font-weight: 600;">Hubungi Anggota</small>
        </a>

        <!-- Menu Keuangan (Hanya jika diizinkan) -->
        @if(auth()->user()->finance_view_scope !== 'none' && (auth()->user()->finance_view_scope || auth()->user()->can_view_finances))
        <a href="{{ route('member.finances') }}" class="card glass text-center text-decoration-none app-menu-card p-3" style="border-radius: 20px; border: 2px solid #f59e0b; background: rgba(245, 158, 11, 0.05);">
            <div style="font-size: 3.5rem; margin-bottom: 10px; line-height: 1;">💰</div>
            <h6 style="font-weight: 800; color: #b45309; margin: 0;">Buku Keuangan</h6>
            <small class="text-muted" style="font-size: 0.75rem; font-weight: 600;">Catatan Kas/Hutang</small>
        </a>
        @endif

        <!-- Menu Pendelegasian (Hanya jika Admin mendelegasikan hak mengelola ke dia) -->
        @if(auth()->user()->can_manage_finances || auth()->user()->can_allocate_budgets || auth()->user()->can_manage_members)
        <a href="{{ route('member.delegations') }}" class="card glass text-center text-decoration-none app-menu-card p-3" style="border-radius: 20px; border: 2px solid #3b82f6; background: rgba(59, 130, 246, 0.05);">
            <div style="font-size: 3.5rem; margin-bottom: 10px; line-height: 1;">🔑</div>
            <h6 style="font-weight: 800; color: #1d4ed8; margin: 0;">Beri Hak Akses</h6>
            <small class="text-muted" style="font-size: 0.75rem; font-weight: 600;">Oper Izin Ke Anggota</small>
        </a>
        @endif

    </div>

    <!-- Divisi Yang Diamanahkan -->
    @if((auth()->user()->assignedDivisions && auth()->user()->assignedDivisions->count() > 0) || auth()->user()->division_id)
    <div class="card p-4 glass mb-4" style="border-radius: 20px; border: 1px solid #e5e7eb;">
        <h5 class="fw-bold mb-3" style="color: #374151;">🏢 Jabatan Divisi Anda</h5>
        <div class="list-group list-group-flush">
            @if(auth()->user()->division_id && auth()->user()->division)
            <div class="list-group-item bg-transparent border-0 px-0 py-2 d-flex align-items-center">
                <div style="font-size: 1.5rem; margin-right: 15px;">📌</div>
                <div>
                    <h6 class="mb-0 fw-bold">{{ auth()->user()->division->name }} (Utama)</h6>
                    <small class="text-muted">Sebagai: {{ auth()->user()->jabatan ?? 'Anggota Biasa' }}</small>
                </div>
            </div>
            @endif

            @if(auth()->user()->assignedDivisions)
                @foreach(auth()->user()->assignedDivisions as $divisi)
                <div class="list-group-item bg-transparent border-0 px-0 py-2 d-flex align-items-center">
                    <div style="font-size: 1.5rem; margin-right: 15px;">📎</div>
                    <div>
                        <h6 class="mb-0 fw-bold">{{ $divisi->name }}</h6>
                        <small class="text-muted">Sebagai: {{ $divisi->pivot->jabatan ?? 'Anggota Tambahan' }}</small>
                    </div>
                </div>
                @endforeach
            @endif
        </div>
    </div>
    @endif

    <!-- Sertifikat (Tampil hanya jika ada) -->
    @if(isset($certificates) && count($certificates) > 0)
    <div class="card p-4 glass mb-4 bg-primary bg-opacity-10" style="border-radius: 20px; border: 1px solid #bfdbfe;">
        <h5 class="fw-bold mb-3 text-primary">📜 Sertifikat Saham / Dividen</h5>
        <p class="small text-muted mb-3">Tunjukkan PIN ini ke pengurus untuk pencairan atau verifikasi.</p>
        @foreach($certificates as $cert)
        <div class="bg-white p-3 rounded mb-2 shadow-sm d-flex justify-content-between align-items-center">
            <div>
                <strong class="text-dark">Sertifikat {{ $cert->percentage }}%</strong><br>
                <small class="text-muted">Terbit: {{ \Carbon\Carbon::parse($cert->issue_date)->format('d/m/Y') }}</small>
            </div>
            <div class="text-end">
                <small class="text-muted d-block">PIN Rahasia:</small>
                <span class="badge bg-success" style="font-size: 1.1rem; letter-spacing: 2px;">{{ $cert->secret_pin }}</span>
            </div>
        </div>
        @endforeach
    </div>
    @endif
    @endif {{-- end status AKTIF --}}

</div>

<style>
    .app-menu-card {
        transition: transform 0.2s, box-shadow 0.2s;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
    }
    .app-menu-card:hover, .app-menu-card:active {
        transform: translateY(-5px) scale(1.02);
        box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
        border-color: #10b981 !important;
    }
</style>
@endsection
