@extends('layouts.app')

@section('content')
<div class="hero">
    <div class="container animate-fade-in">
        <h1 class="text-4xl mb-4">Selamat Datang di<br><span class="navbar-brand">Jostru Community</span></h1>
        <p>Wadah kreativitas, diskusi, dan perkembangan bersama. Bergabunglah dengan kami sekarang juga!</p>
        <div class="flex justify-center gap-4 mt-4">
            <a href="{{ route('register') }}" class="btn btn-primary">Gabung Sekarang</a>
            <a href="#tentang" class="btn btn-outline">Pelajari Lebih Lanjut</a>
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
