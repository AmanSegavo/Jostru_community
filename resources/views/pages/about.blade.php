@extends('layouts.app')
@section('title', 'Tentang Kami - Jostru Community')

@push('styles')
<style>
    .hero-about {
        padding: 80px 0;
        background: radial-gradient(circle at top right, rgba(34, 197, 94, 0.1), transparent 50%);
    }
    .about-card {
        border-radius: 20px;
        transition: transform 0.3s ease;
        border: 1px solid rgba(34, 197, 94, 0.2);
    }
    .about-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 30px rgba(34, 197, 94, 0.15);
    }
</style>
@endpush

@section('content')
<div class="hero-about">
    <div class="container text-center">
        <h1 class="display-4 fw-bold mb-4" style="color: var(--primary);">Tentang Jostru Community</h1>
        <p class="lead text-muted mx-auto" style="max-width: 800px; font-size: 1.2rem; line-height: 1.8;">
            Kami adalah komunitas inovatif yang berdedikasi untuk mentransformasi cara masyarakat memandang dan mengelola limbah. Misi kami adalah menciptakan ekosistem berkelanjutan di mana limbah bukan lagi masalah, melainkan sumber daya ekonomi yang berharga.
        </p>
    </div>
</div>

<div class="container mb-5 pb-5">
    <div class="row g-4 mt-2">
        <div class="col-md-4">
            <div class="card p-4 h-100 glass about-card">
                <div style="font-size: 3rem; margin-bottom: 1rem;">🌱</div>
                <h3 class="fw-bold">Visi Kami</h3>
                <p class="text-muted mt-2">Mewujudkan lingkungan yang bersih dan mandiri secara ekonomi melalui inovasi pengelolaan limbah berbasis komunitas dan teknologi modern.</p>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card p-4 h-100 glass about-card">
                <div style="font-size: 3rem; margin-bottom: 1rem;">🤝</div>
                <h3 class="fw-bold">Misi Kami</h3>
                <p class="text-muted mt-2">Membangun kesadaran kolektif, menyediakan fasilitas penukaran limbah digital yang transparan, dan memberikan edukasi daur ulang yang bernilai jual.</p>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card p-4 h-100 glass about-card">
                <div style="font-size: 3rem; margin-bottom: 1rem;">💡</div>
                <h3 class="fw-bold">Inovasi Hijau</h3>
                <p class="text-muted mt-2">Memanfaatkan teknologi AI dan platform digital untuk melacak dampak positif yang kita buat bersama secara *real-time* dan transparan.</p>
            </div>
        </div>
    </div>

    <div class="mt-5 text-center">
        <h2 class="fw-bold mb-4">Bergabunglah Bersama Kami</h2>
        <p class="text-muted mb-4">Mari menjadi pahlawan lingkungan. Langkah kecil Anda akan membawa dampak besar bagi bumi kita.</p>
        <a href="{{ route('register') }}" class="btn btn-primary btn-lg" style="border-radius: 50px; font-weight: 700; padding: 12px 30px;">Mulai Sekarang</a>
    </div>
</div>
@endsection
