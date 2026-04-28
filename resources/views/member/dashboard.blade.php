@extends('layouts.app')

@section('title', 'Member Dashboard')

@section('content')
<div class="container mt-4 animate-fade-in">
    <!-- Header Section with Profile Summary -->
    <div class="card p-0 glass mb-4 overflow-hidden border-0 shadow-lg" style="border-radius: 24px;">
        <div style="background: linear-gradient(135deg, #22c55e, #10b981); padding: 3rem 2rem; position: relative;">
            <div style="position: relative; z-index: 2;">
                <h1 style="color: white; font-weight: 800; margin-bottom: 0.5rem;">Halo, {{ auth()->user()->name }}! 👋</h1>
                <div style="display: inline-flex; align-items: center; gap: 8px; padding: 6px 16px; background: rgba(255,255,255,0.2); border-radius: 20px; color: white; font-weight: 600; font-size: 0.9rem; backdrop-filter: blur(10px);">
                    <span style="width: 8px; height: 8px; background: #fff; border-radius: 50%; display: inline-block;"></span>
                    Pejuang Lingkungan Jostru
                </div>
            </div>
            <!-- Decorative circle -->
            <div style="position: absolute; right: -50px; top: -50px; width: 200px; height: 200px; background: rgba(255,255,255,0.1); border-radius: 50%;"></div>
        </div>
        <div style="padding: 1.5rem 2rem; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1rem;">
            <div>
                <p class="text-muted mb-0">ID Anggota: <span style="font-weight: 700; color: var(--text-color);">{{ auth()->user()->member_id ?? 'JC-NEW' }}</span></p>
                <p class="text-muted mb-0">Status Akun: <span class="badge bg-success" style="background: rgba(34, 197, 94, 0.1) !important; color: #22c55e; border: 1px solid rgba(34,197,94,0.2);">{{ auth()->user()->status ?? 'AKTIF' }}</span></p>
            </div>
            <div class="d-flex gap-2">
                <a href="{{ route('member.profile') }}" class="btn btn-outline" style="border-radius: 12px;">Edit Profil</a>
                <a href="#" class="btn btn-primary" style="background: #22c55e; border: none; border-radius: 12px; font-weight: 700;">Download Kartu</a>
            </div>
        </div>
    </div>

    <!-- Quick Stats & Actions -->
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 1.5rem; margin-bottom: 2rem;">
        <div class="card p-4 glass card-eco" style="border-radius: 20px;">
            <div style="font-size: 2.5rem; margin-bottom: 1rem;">♻️</div>
            <h3 style="font-weight: 700;">Setoran Limbah</h3>
            <p class="text-muted">Total kontribusi Anda bulan ini: <strong>{{ $totalWeight ?? 0 }} kg</strong></p>
            <a href="{{ route('member.waste_report') }}" class="btn btn-outline w-full mt-3" style="border-radius: 12px;">Laporkan Setoran</a>
        </div>

        <div class="card p-4 glass card-eco" style="border-radius: 20px;">
            <div style="font-size: 2.5rem; margin-bottom: 1rem;">📰</div>
            <h3 style="font-weight: 700;">Update Komunitas</h3>
            <p class="text-muted">Lihat pengumuman dan berita terbaru Jostru.</p>
            <a href="{{ route('member.feed') }}" class="btn btn-outline w-full mt-3" style="border-radius: 12px;">Buka Feed</a>
        </div>

        <div class="card p-4 glass card-eco" style="border-radius: 20px;">
            <div style="font-size: 2.5rem; margin-bottom: 1rem;">📅</div>
            <h3 style="font-weight: 700;">Event Mendatang</h3>
            <p class="text-muted">{{ $nextEvent ? $nextEvent->title . ' (' . \Carbon\Carbon::parse($nextEvent->event_date)->format('d M') . ')' : 'Belum ada agenda terdekat.' }}</p>
            <a href="{{ route('member.events') }}" class="btn btn-outline w-full mt-3" style="border-radius: 12px;">Lihat Kalender</a>
        </div>

        <div class="card p-4 glass card-eco" style="border-radius: 20px; border-left: 4px solid #22c55e;">
            <div style="font-size: 2.5rem; margin-bottom: 1rem;">💬</div>
            <h3 style="font-weight: 700;">Pusat Komunikasi</h3>
            <p class="text-muted">Hubungi tim atau anggota lain via Chat & Call.</p>
            <a href="{{ route('member.chat.list') }}" class="btn btn-primary w-full mt-3" style="border-radius: 12px; background: #22c55e; border: none;">Buka Obrolan</a>
        </div>
    </div>

    <!-- Info Section -->
    <div class="card p-4 glass" style="border-radius: 20px; background: rgba(var(--primary-rgb), 0.03);">
        <div class="row align-items-center">
            <div class="col-md-2 text-center">
                <div style="width: 80px; height: 80px; background: #22c55e; border-radius: 20px; display: flex; align-items: center; justify-content: center; margin: 0 auto;">
                    <svg width="40" height="40" fill="white" viewBox="0 0 16 16"><path d="M12.354 4.354a.5.5 0 0 0-.708-.708L5 10.293 1.854 7.146a.5.5 0 1 0-.708.708l3.5 3.5a.5.5 0 0 0 .708 0l7-7zm-4.208 7-.896-.897.707-.707.543.543 6.646-6.647a.5.5 0 0 1 .708.708l-7 7a.5.5 0 0 1-.708 0z"/></svg>
                </div>
            </div>
            <div class="col-md-10">
                <h4 style="font-weight: 700; margin-bottom: 0.5rem;">Lengkapi Data Geolocation Anda</h4>
                <p class="text-muted mb-0">Pastikan koordinat rumah Anda sudah benar di halaman profil untuk memudahkan tim kurir dalam menjemput setoran limbah rumah tangga.</p>
            </div>
        </div>
    </div>
</div>

<style>
    .card-eco {
        transition: all 0.3s ease;
        border: 1px solid rgba(0,0,0,0.05);
    }
    .card-eco:hover {
        transform: translateY(-8px);
        box-shadow: 0 15px 30px rgba(34, 197, 94, 0.15);
        border-color: #22c55e;
    }
</style>
@endsection
