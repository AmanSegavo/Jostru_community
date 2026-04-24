@extends('layouts.app')

@section('content')
<div class="hero" style="min-height: 80vh; display: flex; align-items: center; justify-content: center; background: radial-gradient(circle at center, rgba(var(--primary-h), var(--primary-s), var(--primary-l), 0.1) 0%, transparent 70%);">
    <div class="container animate-fade-in text-center">
        <div style="display: inline-block; padding: 5px 15px; border-radius: 20px; background: rgba(var(--primary-h), var(--primary-s), var(--primary-l), 0.1); color: var(--primary); font-weight: 600; margin-bottom: 20px; font-size: 0.9rem; letter-spacing: 1px;">
            PLATFORM KOMUNITAS DIGITAL
        </div>
        <h1 class="text-5xl mb-4" style="line-height: 1.2;">Selamat Datang di<br><span class="navbar-brand" style="font-size: inherit;">Jostru Community</span></h1>
        <p style="font-size: 1.25rem; color: var(--text-secondary); max-width: 600px; margin: 0 auto 2.5rem; line-height: 1.6;">Wadah kreativitas, diskusi, dan perkembangan bersama. Tingkatkan koneksi dan kolaborasi dengan bergabung bersama kami sekarang juga!</p>
        <div class="flex justify-center gap-4 mt-4" style="flex-wrap: wrap;">
            <a href="{{ route('register') }}" class="btn btn-primary" style="padding: 1rem 2rem; font-size: 1.1rem; border-radius: 30px;">Gabung Sekarang</a>
            <a href="#tentang" class="btn btn-outline" style="padding: 1rem 2rem; font-size: 1.1rem; border-radius: 30px;">Pelajari Lebih Lanjut</a>
        </div>
    </div>
</div>

<div class="container mt-4 mb-4" id="tentang">
    <h2 class="text-center mb-4">Kenapa Memilih Jostru?</h2>
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 1.5rem;">
        <div class="card p-4 glass">
            <h3>🌐 Komunitas Aktif</h3>
            <p class="text-muted mt-4">Diskusikan topik menarik dan temukan teman baru dalam komunitas feed kami yang interaktif.</p>
        </div>
        <div class="card p-4 glass">
            <h3>💳 Kartu Member Digital</h3>
            <p class="text-muted mt-4">Dapatkan kartu identitas digital dengan sistem auto-generate yang elegan dan dapat diunduh.</p>
        </div>
        <div class="card p-4 glass">
            <h3>📅 Event Seru</h3>
            <p class="text-muted mt-4">Ikuti berbagai kegiatan seru dari sesi belajar hingga acara networking bulanan.</p>
        </div>
    </div>
</div>

<div class="container mt-4 mb-4" id="kontak">
    <div class="card mx-auto max-w-md p-4">
        <h2 class="text-center">Hubungi Kami</h2>
        
        @if(session('success'))
            <div style="background-color: rgba(0, 128, 0, 0.1); color: green; padding: 1rem; border-radius: var(--radius-md); margin-top: 1rem;">
                {{ session('success') }}
            </div>
        @endif

        <form action="/contact" method="POST" class="mt-4">
            @csrf
            <div class="form-group">
                <label class="form-label">Nama</label>
                <input type="text" name="name" class="form-control" required>
            </div>
            <div class="form-group">
                <label class="form-label">Email</label>
                <input type="email" name="email" class="form-control" required>
            </div>
            <div class="form-group">
                <label class="form-label">Pesan</label>
                <textarea name="message" class="form-control" rows="4" required></textarea>
            </div>
            <button type="submit" class="btn btn-primary w-full">Kirim Pesan</button>
        </form>
    </div>
</div>
@endsection
