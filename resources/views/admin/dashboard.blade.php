@extends('layouts.admin')

@section('admin_content')
<div class="animate-fade-in">
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-3 mb-4">
        <div>
            <h2 style="font-weight: 800; background: linear-gradient(135deg, var(--primary), #3b82f6); -webkit-background-clip: text; -webkit-text-fill-color: transparent;">Overview Sistem</h2>
            <p class="text-muted m-0" style="font-size: 0.95rem;">Informasi utama manajemen Jostru Community.</p>
        </div>
        <div class="w-100 w-md-auto">
            @if(Auth::user()->google_id)
                <span class="badge bg-success d-flex justify-content-center align-items-center gap-2 py-2 px-3 w-100 w-md-auto" style="border-radius: 12px; font-weight: 600; font-size: 14px; box-shadow: 0 4px 12px rgba(34,197,94,0.2);">
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path><polyline points="22 4 12 14.01 9 11.01"></polyline></svg>
                    Google Tertaut
                </span>
            @else
                <a href="{{ route('auth.google') }}" class="btn glass d-flex justify-content-center align-items-center gap-2 w-100 w-md-auto" style="border: 1px solid var(--border-color); font-size: 14px; font-weight: 600; border-radius: 12px; padding: 10px 20px;">
                    <svg width="20" height="20" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z" fill="#4285F4"/><path d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z" fill="#34A853"/><path d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l3.66-2.84z" fill="#FBBC05"/><path d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 12-4.53z" fill="#EA4335"/></svg>
                    Tautkan Google
                </a>
            @endif
        </div>
    </div>

    <style>
        .premium-card {
            background: var(--glass-bg);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border-radius: 20px;
            padding: 1.5rem;
            border: 1px solid var(--glass-border);
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            position: relative;
            overflow: hidden;
            box-shadow: 0 10px 30px rgba(0,0,0,0.05);
        }
        .premium-card::before {
            content: '';
            position: absolute;
            top: 0; left: 0; width: 100%; height: 100%;
            background: linear-gradient(135deg, rgba(255,255,255,0.1) 0%, transparent 100%);
            z-index: 0;
            pointer-events: none;
        }
        .premium-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 20px 40px rgba(0,0,0,0.1);
        }
        .premium-card > * {
            position: relative;
            z-index: 1;
        }
        
        /* Grid responsif untuk HP */
        .dashboard-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap: 1.5rem;
            margin-bottom: 2.5rem;
        }
        .dashboard-grid-large {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(100%, 1fr));
            gap: 1.5rem;
            margin-bottom: 2rem;
        }
        @media (min-width: 992px) {
            .dashboard-grid-large { grid-template-columns: repeat(2, 1fr); }
        }

        /* TIMELINE UI UNTUK AKTIVITAS */
        .timeline-container {
            position: relative;
            padding-left: 24px;
            margin-top: 1rem;
        }
        .timeline-container::before {
            content: '';
            position: absolute;
            left: 7px;
            top: 0;
            bottom: 0;
            width: 2px;
            background: rgba(34, 197, 94, 0.2);
            border-radius: 2px;
        }
        .timeline-item {
            position: relative;
            margin-bottom: 1.5rem;
            padding-bottom: 1.5rem;
            border-bottom: 1px dashed var(--border-color);
        }
        .timeline-item:last-child {
            margin-bottom: 0;
            padding-bottom: 0;
            border-bottom: none;
        }
        .timeline-dot {
            position: absolute;
            left: -28px;
            top: 4px;
            width: 12px;
            height: 12px;
            border-radius: 50%;
            background: var(--primary);
            box-shadow: 0 0 0 4px rgba(34, 197, 94, 0.15);
        }
    </style>

    <!-- Section Aplikasi Mobile -->
    <div class="premium-card mb-4" style="border: 2px solid #8b5cf6; background: rgba(139, 92, 246, 0.05);">
        <h5 class="fw-bold mb-2" style="color: #6d28d9;">📱 Aplikasi Jostru Mobile & Desktop (Admin)</h5>
        <p class="text-muted mb-3" style="font-size: 0.95rem;">
            Akses Dasbor Admin Jostru dari genggaman Anda, lebih cepat dan aman dengan dukungan Offline. Tersedia untuk Android, Windows, dan MacOS.
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
                <div id="mobile-app-promo-section">
                    <form action="{{ route('member.app.generate_code') }}" method="POST">
                        @csrf
                        <button type="submit" class="btn fw-bold text-white" style="background: linear-gradient(135deg, #8b5cf6, #6d28d9); border-radius: 12px; padding: 0.75rem 2rem;">
                            📲 Dapatkan Kode & Unduh Aplikasi
                        </button>
                    </form>
                </div>

                <div id="desktop-pairing-form" style="display: none;">
                    <div class="alert alert-info d-flex align-items-center" style="border-radius: 12px; border: 1px solid #3b82f6;">
                        <div style="font-size: 2rem; margin-right: 15px;">💻</div>
                        <div>
                            <strong>Hubungkan Aplikasi PC Ini</strong><br>
                            <span style="font-size: 0.85rem;">Masukkan 6-Digit Kode Pairing yang Anda buat dari HP/Web untuk mengunci aplikasi di PC ini.</span>
                        </div>
                    </div>
                    <form method="POST" action="{{ route('member.app.bind') }}" class="d-flex gap-2 align-items-center">
                        @csrf
                        <input type="hidden" name="device_id" id="dashboard_hidden_device_id">
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
        // Desktop App Device Binding Logic for Dashboard
        if (window.jostruApp && window.jostruApp.isDesktopApp) {
            const promoSection = document.getElementById('mobile-app-promo-section');
            const pairingForm = document.getElementById('desktop-pairing-form');
            if (promoSection && pairingForm) {
                promoSection.style.display = 'none';
                pairingForm.style.display = 'block';
                
                window.jostruApp.getDeviceId().then(id => {
                    document.getElementById('dashboard_hidden_device_id').value = id;
                }).catch(err => console.error("Gagal mendapatkan Hardware ID", err));
            }
        }
    </script>

    <div class="dashboard-grid">
        <div class="premium-card">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h3 class="text-sm uppercase text-muted m-0 fw-bold" style="letter-spacing: 1px;">Total Anggota</h3>
                <div style="width: 40px; height: 40px; border-radius: 12px; background: rgba(59, 130, 246, 0.1); display: flex; align-items: center; justify-content: center; color: #3b82f6;">
                    <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                </div>
            </div>
            <p class="mt-2 m-0" style="font-size: 2.5rem; font-weight: 900; color: #3b82f6; line-height: 1;">{{\App\Models\User::whereNotIn('role', ['admin', 'superadmin'])->count()}}</p>
        </div>
        
        <div class="premium-card" style="border-left: 5px solid #22c55e;">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h3 class="text-sm uppercase text-muted m-0 fw-bold" style="letter-spacing: 1px;">Kontribusi Limbah</h3>
                <div style="width: 40px; height: 40px; border-radius: 12px; background: rgba(34, 197, 94, 0.1); display: flex; align-items: center; justify-content: center; color: #22c55e;">
                    <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>
                </div>
            </div>
            <p class="mt-2 m-0" style="font-size: 2.5rem; font-weight: 900; color: #22c55e; line-height: 1;">{{ number_format($totalWaste ?? 0, 1) }} <span style="font-size: 1.2rem; font-weight: 600;">kg</span></p>
        </div>
        
        <div class="premium-card" style="border-left: 5px solid #f59e0b;">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h3 class="text-sm uppercase text-muted m-0 fw-bold" style="letter-spacing: 1px;">Laporan Pending</h3>
                <div style="width: 40px; height: 40px; border-radius: 12px; background: rgba(245, 158, 11, 0.1); display: flex; align-items: center; justify-content: center; color: #f59e0b;">
                    <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                </div>
            </div>
            <p class="mt-2 m-0" style="font-size: 2.5rem; font-weight: 900; color: #f59e0b; line-height: 1;">{{ $pendingWaste ?? 0 }}</p>
        </div>
        
        <div class="premium-card" style="border-left: 5px solid #8b5cf6;">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h3 class="text-sm uppercase text-muted m-0 fw-bold" style="letter-spacing: 1px;">Community Feed</h3>
                <div style="width: 40px; height: 40px; border-radius: 12px; background: rgba(139, 92, 246, 0.1); display: flex; align-items: center; justify-content: center; color: #8b5cf6;">
                    <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17 8h2a2 2 0 012 2v6a2 2 0 01-2 2h-2v4l-4-4H9a1.994 1.994 0 01-1.414-.586m0 0L11 14h4a2 2 0 002-2V6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2v4l.586-.586z"></path></svg>
                </div>
            </div>
            <p class="mt-2 m-0" style="font-size: 2.5rem; font-weight: 900; color: #8b5cf6; line-height: 1;">{{ $totalPosts ?? 0 }}</p>
        </div>
    </div>

    <div class="dashboard-grid-large">
        <div class="premium-card">
            <h3 class="mb-4" style="font-weight: 700; font-size: 1.2rem;">Arus Kas 6 Bulan Terakhir</h3>
            <div style="position: relative; height: 300px; width: 100%;">
                <canvas id="financeChart"></canvas>
            </div>
        </div>

        <div class="premium-card">
            <h3 class="mb-2" style="font-weight: 700; font-size: 1.2rem;">Aktivitas Sistem</h3>
            <p class="text-muted" style="font-size: 0.9rem; margin-bottom: 1rem;">Rekam jejak aktivitas terbaru di dalam sistem.</p>
            
            <div style="max-height: 350px; overflow-y: auto; padding-right: 10px;">
                @if($activities->count() > 0)
                    <div class="timeline-container">
                        @foreach($activities as $log)
                            <div class="timeline-item">
                                <div class="timeline-dot"></div>
                                <div style="display: flex; justify-content: space-between; margin-bottom: 4px; align-items: center; flex-wrap: wrap; gap: 8px;">
                                    <strong style="font-size: 14px; color: var(--text-primary);">{{ $log->action }}</strong>
                                    <span class="badge" style="background: rgba(34,197,94,0.1); color: var(--primary); font-size: 11px; font-weight: 600;">{{ $log->created_at->diffForHumans() }}</span>
                                </div>
                                <p class="text-muted" style="margin: 0; font-size: 13px; line-height: 1.5;">
                                    <span style="font-weight: 600; color: var(--text-primary);">{{ $log->user->name ?? 'Sistem' }}</span> 
                                    melakukan aktivitas: <br/>{{ $log->description }}
                                </p>
                            </div>
                        @endforeach
                    </div>
                    <div style="text-align: center; margin-top: 1.5rem;">
                        <a href="{{ route('admin.logs') }}" class="btn btn-sm glass" style="border-radius: 9999px; padding: 8px 24px; font-weight: 600; font-size: 13px; border: 1px solid var(--border-color); color: var(--text-primary);">
                            Lihat Riwayat Lengkap &rarr;
                        </a>
                    </div>
                @else
                    <div style="text-align: center; padding: 2rem 0;">
                        <svg width="48" height="48" fill="none" stroke="currentColor" stroke-width="1" viewBox="0 0 24 24" style="color: var(--text-secondary); opacity: 0.5; margin-bottom: 1rem;">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <p class="text-muted m-0" style="font-size: 14px;">Belum ada aktivitas tercatat.</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const ctx = document.getElementById('financeChart').getContext('2d');
        const months = {!! json_encode($months) !!};
        const pemasukanData = {!! json_encode($pemasukanData) !!};
        const pengeluaranData = {!! json_encode($pengeluaranData) !!};

        // Get theme colors
        const isDark = document.body.getAttribute('data-theme') === 'dark';
        const gridColor = isDark ? 'rgba(255, 255, 255, 0.1)' : 'rgba(0, 0, 0, 0.1)';
        const textColor = isDark ? '#94a3b8' : '#475569';

        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: months,
                datasets: [
                    {
                        label: 'Pemasukan',
                        data: pemasukanData,
                        backgroundColor: 'rgba(34, 197, 94, 0.8)',
                        borderColor: '#22c55e',
                        borderWidth: 1,
                        borderRadius: 4
                    },
                    {
                        label: 'Pengeluaran',
                        data: pengeluaranData,
                        backgroundColor: 'rgba(239, 68, 68, 0.8)',
                        borderColor: '#ef4444',
                        borderWidth: 1,
                        borderRadius: 4
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: { color: gridColor },
                        ticks: {
                            color: textColor,
                            callback: function(value) {
                                if (value >= 1000000) return 'Rp ' + (value / 1000000).toFixed(1) + ' Jt';
                                if (value >= 1000) return 'Rp ' + (value / 1000).toFixed(0) + ' Rb';
                                return 'Rp ' + value;
                            }
                        }
                    },
                    x: {
                        grid: { display: false },
                        ticks: { color: textColor }
                    }
                },
                plugins: {
                    legend: {
                        labels: { color: textColor }
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                let label = context.dataset.label || '';
                                if (label) {
                                    label += ': ';
                                }
                                if (context.parsed.y !== null) {
                                    label += new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR' }).format(context.parsed.y);
                                }
                                return label;
                            }
                        }
                    }
                }
            }
        });
    });
</script>
@endpush
@endsection
