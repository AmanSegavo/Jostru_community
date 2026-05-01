@extends('layouts.app')

@section('content')
@push('styles')
    <style>
        @keyframes float {
            0% { transform: translateY(0px); }
            50% { transform: translateY(-20px); }
            100% { transform: translateY(0px); }
        }
        
        .floating-logo {
            animation: float 4s ease-in-out infinite;
        }

        .scroll-reveal {
            opacity: 0;
            transform: translateY(30px);
            transition: all 0.8s ease-out;
        }

        .scroll-reveal.active {
            opacity: 1;
            transform: translateY(0);
        }

        .card-eco:hover {
            transform: translateY(-10px) scale(1.02);
            box-shadow: 0 20px 40px rgba(34, 197, 94, 0.2);
            border-color: #22c55e !important;
        }

        .btn-glow:hover {
            box-shadow: 0 0 20px rgba(34, 197, 94, 0.6);
        }

        @keyframes bounce {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(8px); }
        }

        @media (max-width: 768px) {
            .text-5xl { font-size: 2.2rem !important; }
            .hero { padding: 30px 0 !important; }
        }
    </style>
@endpush

<div class="hero" style="min-height: 80vh; display: flex; align-items: center; justify-content: center; background: radial-gradient(circle at center, rgba(34, 197, 94, 0.08) 0%, transparent 70%); overflow: hidden; padding: 40px 0;">
    <div class="container text-center">
        <div class="floating-logo mb-3">
            <img src="{{ asset('images/logo.png') }}" alt="Jostru Logo" style="width: 90px; filter: drop-shadow(0 10px 15px rgba(0,0,0,0.1));">
        </div>
        <div style="display: inline-block; padding: 4px 14px; border-radius: 20px; background: rgba(34, 197, 94, 0.1); color: #22c55e; border: 1px solid rgba(34, 197, 94, 0.2); font-weight: 800; margin-bottom: 16px; font-size: 0.85rem; letter-spacing: 2px;">
            PENGELOLAAN LIMBAH &amp; LINGKUNGAN
        </div>
        <h1 class="text-5xl mb-3" style="line-height: 1.1; font-weight: 800;">Ubah Limbah Menjadi<br><span style="color: #22c55e; background: linear-gradient(90deg, #22c55e, #10b981); -webkit-background-clip: text; -webkit-text-fill-color: transparent;">Masa Depan Hijau</span></h1>
        <p style="font-size: 1.1rem; color: var(--text-secondary); max-width: 600px; margin: 0 auto 2rem; line-height: 1.7;">Bergabunglah dengan komunitas Jostru untuk mengelola limbah rumah tangga Anda secara cerdas dan berkontribusi bagi kelestarian bumi.</p>
        <div class="flex justify-center gap-4 mt-2" style="flex-wrap: wrap;">
            <a href="{{ route('register') }}" class="btn btn-primary btn-glow" style="padding: 1rem 2.2rem; font-size: 1.05rem; border-radius: 50px; background: linear-gradient(135deg, #22c55e, #10b981); border: none; font-weight: 700; box-shadow: 0 8px 24px rgba(34, 197, 94, 0.4);">🌿 Daftar Sekarang</a>
            <a href="#download" class="btn btn-outline" style="padding: 1rem 2.2rem; font-size: 1.05rem; border-radius: 50px; font-weight: 700;">📱 Unduh Aplikasi</a>
        </div>
        {{-- Scroll indicator --}}
        <div style="margin-top: 2.5rem; animation: bounce 2s ease-in-out infinite; opacity: 0.5;">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="6 9 12 15 18 9"></polyline></svg>
        </div>
    </div>
</div>

<div class="container mt-5 mb-5 scroll-reveal" id="tentang">
    <h2 class="text-center mb-5" style="font-weight: 800;">Program Unggulan Kami</h2>
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 2rem;">
        <div class="card p-4 glass card-eco" style="border-top: 5px solid #22c55e; transition: all 0.3s ease;">
            <div style="font-size: 3rem; margin-bottom: 1.5rem;">♻️</div>
            <h3 style="font-weight: 700;">Setoran Limbah</h3>
            <p class="text-muted mt-3">Laporkan dan kumpulkan limbah Anda untuk ditukar dengan poin atau produk hasil daur ulang komunitas.</p>
        </div>
        <div class="card p-4 glass card-eco" style="border-top: 5px solid #22c55e; transition: all 0.3s ease;">
            <div style="font-size: 3rem; margin-bottom: 1.5rem;">💳</div>
            <h3 style="font-weight: 700;">Kartu Member Hijau</h3>
            <p class="text-muted mt-3">Identitas digital pejuang lingkungan dengan QR Code untuk pencatatan setoran limbah yang akurat.</p>
        </div>
        <div class="card p-4 glass card-eco" style="border-top: 5px solid #22c55e; transition: all 0.3s ease;">
            <div style="font-size: 3rem; margin-bottom: 1.5rem;">📚</div>
            <h3 style="font-weight: 700;">Edukasi & Workshop</h3>
            <p class="text-muted mt-3">Akses ribuan materi cara mengolah limbah organik dan anorganik menjadi barang ekonomis.</p>
        </div>
    </div>
</div>

<div class="container mt-5 mb-5 scroll-reveal" id="download">
    <div class="card p-5 glass" style="background: linear-gradient(135deg, rgba(34, 197, 94, 0.1), rgba(16, 185, 129, 0.1)); border: 2px solid rgba(34, 197, 94, 0.2); border-radius: 30px;">
        <div class="row align-items-center">
            <div class="col-md-6 text-center text-md-start">
                <h2 style="font-weight: 800; margin-bottom: 1rem;">Bawa Jostru ke Genggaman Anda</h2>
                <p class="text-muted mb-4" style="font-size: 1.1rem;">Pantau setoran limbah, lihat kartu member, dan kelola profil Anda lebih mudah melalui aplikasi mobile resmi Jostru Community.</p>
                <div class="d-flex gap-3 justify-content-center justify-content-md-start flex-wrap">
                    <button id="pwa-install" class="btn btn-primary d-flex align-items-center gap-3" style="padding: 15px 35px; border-radius: 50px; background: linear-gradient(135deg, #22c55e, #10b981); border: none; box-shadow: 0 10px 25px rgba(34, 197, 94, 0.4);">
                        <svg width="28" height="28" fill="currentColor" viewBox="0 0 16 16"><path d="M8 0a8 8 0 1 0 0 16A8 8 0 0 0 8 0zm3.5 7.5a.5.5 0 0 1 0 1H9.5V11a.5.5 0 0 1-1 0V8.5H6a.5.5 0 0 1 0-1h2.5V5a.5.5 0 0 1 1 0v2.5h2z"/></svg>
                        <div style="text-align: left;">
                            <span style="font-weight: 800; font-size: 1.1rem; display: block;">Install Web App</span>
                            <small style="opacity: 0.8; font-size: 0.75rem;">Cepat, Ringan & Otomatis Update</small>
                        </div>
                    </button>
                </div>
            </div>
            <div class="col-md-6 text-center mt-5 mt-md-0">
                <div style="position: relative; display: inline-block;">
                    <img src="{{ asset('images/logo.png') }}" class="floating-logo" style="width: 200px; opacity: 0.2; position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); z-index: 0;">
                    <div style="background: white; padding: 20px; border-radius: 20px; box-shadow: 0 20px 40px rgba(0,0,0,0.1); position: relative; z-index: 1;">
                        <img src="{{ asset('images/logo.png') }}" style="width: 150px; border-radius: 15px;">
                        <div class="mt-3" style="font-weight: 800; color: #000;">JOSTRU APP v1.0</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="container mt-5 mb-5 scroll-reveal" id="kontak">
    <div class="card mx-auto max-w-md p-5 glass" style="border-radius: 30px;">
        <h2 class="text-center" style="font-weight: 800;">Ada Pertanyaan?</h2>
        <p class="text-center text-muted mb-4">Tim kami siap membantu Anda 24/7</p>
        
        <form action="/contact" method="POST">
            @csrf
            <div class="form-group mb-3">
                <label class="form-label" style="font-weight: 600;">Nama Lengkap</label>
                <input type="text" name="name" class="form-control" required style="border-radius: 12px; padding: 12px;">
            </div>
            <div class="form-group mb-3">
                <label class="form-label" style="font-weight: 600;">Email Aktif</label>
                <input type="email" name="email" class="form-control" required style="border-radius: 12px; padding: 12px;">
            </div>
            <div class="form-group mb-4">
                <label class="form-label" style="font-weight: 600;">Detail Pesan</label>
                <textarea name="message" class="form-control" rows="4" required style="border-radius: 12px; padding: 12px;"></textarea>
            </div>
            <button type="submit" class="btn btn-primary w-full btn-glow" style="padding: 15px; border-radius: 15px; background: #22c55e; border: none; font-weight: 700;">Kirim Pesan</button>
        </form>
    </div>
</div>

@push('scripts')
<script>
    // PWA Install Logic
    let deferredPrompt;
    const installBtn = document.getElementById('pwa-install');

    window.addEventListener('beforeinstallprompt', (e) => {
        // Prevent Chrome 67 and earlier from automatically showing the prompt
        e.preventDefault();
        // Stash the event so it can be triggered later.
        deferredPrompt = e;
        // Update UI notify the user they can add to home screen
        installBtn.style.display = 'flex';
    });

    installBtn.addEventListener('click', (e) => {
        if (deferredPrompt) {
            // Show the prompt
            deferredPrompt.prompt();
            // Wait for the user to respond to the prompt
            deferredPrompt.userChoice.then((choiceResult) => {
                if (choiceResult.outcome === 'accepted') {
                    console.log('User accepted the A2HS prompt');
                } else {
                    console.log('User dismissed the A2HS prompt');
                }
                deferredPrompt = null;
            });
        } else {
            alert('Aplikasi sudah terpasang atau browser Anda tidak mendukung instalasi otomatis. Silakan gunakan menu Chrome "Tambahkan ke Layar Utama".');
        }
    });

    // Intersection Observer for scroll animations
    const observerOptions = {
        threshold: 0.1
    };

    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add('active');
            }
        });
    }, observerOptions);

    document.querySelectorAll('.scroll-reveal').forEach(el => {
        observer.observe(el);
    });
</script>
@endpush
@endsection
