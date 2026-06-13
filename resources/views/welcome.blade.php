@extends('layouts.app')

@section('content')
@push('styles')
    <!-- AOS Animation -->
    <link rel="preload" href="https://unpkg.com/aos@2.3.1/dist/aos.css" as="style" onload="this.onload=null;this.rel='stylesheet'">
    <noscript><link rel="stylesheet" href="https://unpkg.com/aos@2.3.1/dist/aos.css"></noscript>
    
    <style>
        :root {
            --brand-primary: #22c55e;
            --brand-secondary: #10b981;
            --brand-dark: #064e3b;
            --font-heading: 'Outfit', sans-serif;
            --font-body: 'Inter', sans-serif;
        }

        body {
            font-family: var(--font-body);
            background-color: #f8fafc;
            color: #1e293b;
            overflow-x: hidden;
        }

        h1, h2, h3, h4, h5, h6 {
            font-family: var(--font-heading);
            letter-spacing: -0.02em;
        }

        /* Hero Background with Glowing Orbs */
        .hero-section {
            position: relative;
            min-height: 90vh;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
            background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
            padding: 80px 0;
        }

        .hero-orb {
            position: absolute;
            border-radius: 50%;
            filter: blur(80px);
            opacity: 0.4;
            z-index: 0;
            animation: float-orb 10s infinite alternate ease-in-out;
        }

        .orb-1 { width: 400px; height: 400px; background: var(--brand-primary); top: -10%; left: -10%; }
        .orb-2 { width: 500px; height: 500px; background: #3b82f6; bottom: -20%; right: -10%; animation-delay: -5s; }
        
        @keyframes float-orb {
            0% { transform: translate(0, 0) scale(1); }
            100% { transform: translate(50px, 50px) scale(1.1); }
        }

        .hero-content {
            position: relative;
            z-index: 2;
        }

        /* Typography */
        .text-gradient {
            background: linear-gradient(135deg, var(--brand-dark), var(--brand-primary), #3b82f6);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-size: 200% 200%;
            animation: gradient-shift 5s ease infinite;
        }

        @keyframes gradient-shift {
            0% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
            100% { background-position: 0% 50%; }
        }

        .hero-title {
            font-size: clamp(3rem, 5vw, 4.5rem);
            font-weight: 900;
            line-height: 1.1;
            margin-bottom: 1.5rem;
        }

        .hero-subtitle {
            font-size: clamp(1.1rem, 2vw, 1.25rem);
            color: #64748b;
            line-height: 1.7;
            max-width: 650px;
            margin: 0 auto 2.5rem;
        }

        /* Buttons */
        .btn-modern {
            padding: 14px 32px;
            border-radius: 50px;
            font-weight: 600;
            font-size: 1.1rem;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            font-family: var(--font-heading);
            display: inline-flex;
            align-items: center;
            gap: 10px;
            text-decoration: none;
        }

        .btn-glow {
            background: linear-gradient(135deg, var(--brand-primary), var(--brand-secondary));
            color: white;
            border: none;
            box-shadow: 0 10px 25px -5px rgba(34, 197, 94, 0.4), 0 8px 10px -6px rgba(34, 197, 94, 0.1);
        }

        .btn-glow:hover {
            transform: translateY(-3px);
            box-shadow: 0 20px 30px -5px rgba(34, 197, 94, 0.5), 0 10px 10px -5px rgba(34, 197, 94, 0.2);
            color: white;
        }

        .btn-outline-modern {
            background: rgba(255, 255, 255, 0.5);
            backdrop-filter: blur(10px);
            border: 2px solid rgba(15, 23, 42, 0.1);
            color: #0f172a;
        }

        .btn-outline-modern:hover {
            background: rgba(255, 255, 255, 0.9);
            border-color: rgba(15, 23, 42, 0.2);
            transform: translateY(-3px);
            color: #0f172a;
        }

        /* Glassmorphism Cards */
        .glass-card {
            background: rgba(255, 255, 255, 0.7);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            border: 1px solid rgba(255, 255, 255, 0.5);
            border-radius: 24px;
            box-shadow: 0 10px 30px -10px rgba(0, 0, 0, 0.05);
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .glass-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 20px 40px -10px rgba(34, 197, 94, 0.15);
            border-color: rgba(34, 197, 94, 0.3);
            background: rgba(255, 255, 255, 0.9);
        }

        .feature-icon-wrapper {
            width: 60px;
            height: 60px;
            border-radius: 16px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
            margin-bottom: 1.5rem;
            background: linear-gradient(135deg, rgba(34, 197, 94, 0.1), rgba(16, 185, 129, 0.1));
            color: var(--brand-primary);
        }

        /* Gallery Grid */
        .lightbox {
            position: fixed;
            z-index: 9999;
            top: 0; left: 0; right: 0; bottom: 0;
            background: rgba(0,0,0,0.9);
            display: none;
            align-items: center;
            justify-content: center;
            flex-direction: column;
            backdrop-filter: blur(5px);
        }
        .lightbox.active { display: flex; }
        .lightbox-close {
            position: absolute;
            top: 20px; right: 30px;
            color: white;
            font-size: 2.5rem;
            cursor: pointer;
            z-index: 10000;
        }
        .lightbox-content {
            max-width: 90%;
            max-height: 80vh;
        }
        .lightbox-caption {
            color: white;
            margin-top: 15px;
            font-size: 1.2rem;
            font-weight: 600;
        }
        
        .gallery-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
            gap: 20px;
        }
        
        .gallery-item {
            position: relative;
            border-radius: 20px;
            overflow: hidden;
            aspect-ratio: 1;
            box-shadow: 0 10px 20px rgba(0,0,0,0.05);
        }

        .gallery-item img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.5s ease;
        }

        .gallery-item:hover img {
            transform: scale(1.1);
        }

        .gallery-overlay {
            position: absolute;
            inset: 0;
            background: linear-gradient(to top, rgba(0,0,0,0.7) 0%, transparent 60%);
            opacity: 0;
            transition: opacity 0.3s ease;
            display: flex;
            align-items: flex-end;
            padding: 20px;
            color: white;
        }

        .gallery-item:hover .gallery-overlay {
            opacity: 1;
        }

        /* Stats Section */
        .stats-section {
            background: var(--brand-dark);
            color: white;
            padding: 5rem 0;
            position: relative;
            overflow: hidden;
        }

        .stat-number {
            font-family: var(--font-heading);
            font-size: 3.5rem;
            font-weight: 900;
            background: linear-gradient(180deg, #fff, #94a3b8);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            margin-bottom: 0.5rem;
        }

        /* Wave Separator */
        .wave-separator {
            position: absolute;
            bottom: 0;
            left: 0;
            width: 100%;
            overflow: hidden;
            line-height: 0;
            transform: rotate(180deg);
        }
        .wave-separator svg {
            position: relative;
            display: block;
            width: calc(100% + 1.3px);
            height: 70px;
        }
        .wave-separator .shape-fill { fill: #f8fafc; }

        /* Accordion Custom */
        .accordion-button {
            font-weight: 600;
            padding: 1.25rem 1.5rem;
            border-radius: 16px !important;
            background: rgba(255, 255, 255, 0.7);
        }
        .accordion-button:not(.collapsed) {
            background: rgba(34, 197, 94, 0.1);
            color: var(--brand-primary);
            box-shadow: none;
        }
        .accordion-item {
            border: 1px solid rgba(0,0,0,0.05);
            border-radius: 16px !important;
            margin-bottom: 1rem;
            background: transparent;
        }
        .accordion-body {
            background: rgba(255,255,255,0.9);
            border-bottom-left-radius: 16px;
            border-bottom-right-radius: 16px;
            color: #475569;
            line-height: 1.7;
        }

    </style>
@endpush

<!-- HERO SECTION -->
<section class="hero-section" id="beranda" style="position:relative; min-height:100vh; overflow:hidden; display:flex; align-items:center;">
    @if(isset($banners) && $banners->count() > 0)
    <!-- Carousel Background -->
    <div id="heroBannerCarousel" class="carousel slide carousel-fade" data-bs-ride="carousel" data-bs-interval="5000" style="position:absolute; inset:0; z-index:0;">
        <div class="carousel-inner" style="height:100%;">
            @foreach($banners as $index => $banner)
            <div class="carousel-item {{ $index == 0 ? 'active' : '' }}" style="height:100%;">
                @if($banner->type == 'image')
                    <img src="{{ $banner->url }}" style="width:100%; height:100%; object-fit:cover; filter:brightness(0.6);" alt="{{ $banner->title }}" {{ $index == 0 ? 'fetchpriority="high"' : 'loading="lazy"' }}>
                @elseif($banner->type == 'video')
                    <video src="{{ $banner->url }}" autoplay muted loop style="width:100%; height:100%; object-fit:cover; filter:brightness(0.6);"></video>
                @endif
                <div class="carousel-caption d-none d-md-block" style="bottom:20%; z-index:10;">
                    <h3 class="fw-bold" style="text-shadow: 2px 2px 8px rgba(0,0,0,0.8);">{{ $banner->title }}</h3>
                </div>
            </div>
            @endforeach
        </div>
        @if($banners->count() > 1)
        <button class="carousel-control-prev" type="button" data-bs-target="#heroBannerCarousel" data-bs-slide="prev" style="z-index:10; background:none; border:none;">
            <span class="carousel-control-prev-icon" aria-hidden="true"></span>
        </button>
        <button class="carousel-control-next" type="button" data-bs-target="#heroBannerCarousel" data-bs-slide="next" style="z-index:10; background:none; border:none;">
            <span class="carousel-control-next-icon" aria-hidden="true"></span>
        </button>
        @endif
    </div>
    @else
    <!-- Fallback Orbs if no banner -->
    <div class="hero-orb orb-1"></div>
    <div class="hero-orb orb-2"></div>
    @endif

    <div class="container text-center hero-content" style="z-index:2; position:relative;">
        <div data-aos="fade-up" data-aos-duration="800" data-aos-delay="200">
            <div class="badge-premium" style="{{ (isset($banners) && $banners->count() > 0) ? 'background:rgba(0,0,0,0.5); border-color:rgba(255,255,255,0.2); color:white;' : '' }}">
                <span style="width:8px; height:8px; background:#22c55e; border-radius:50%; display:inline-block; animation: pulse 2s infinite;"></span>
                KOLABORASI HIJAU BERKELANJUTAN
            </div>
        </div>
        <h1 class="hero-title" data-aos="fade-up" data-aos-duration="800" data-aos-delay="300" style="{{ (isset($banners) && $banners->count() > 0) ? 'color:white; text-shadow:0 4px 15px rgba(0,0,0,0.5);' : '' }}">
            Satukan Potensi<br>
            <span style="{{ (isset($banners) && $banners->count() > 0) ? 'color:#4ade80;' : 'color: var(--brand-primary);' }}">Untuk Kelestarian</span>
        </h1>
        <p class="hero-subtitle" data-aos="fade-up" data-aos-duration="800" data-aos-delay="400" style="{{ (isset($banners) && $banners->count() > 0) ? 'color:#f8fafc; text-shadow:0 2px 10px rgba(0,0,0,0.5);' : '' }}">
            Platform ekosistem sirkular pertama yang mengintegrasikan komunitas, ekonomi kreatif, dan pengelolaan sumber daya secara transparan.
        </p>
        
        <div class="d-flex justify-content-center gap-3 flex-wrap" data-aos="fade-up" data-aos-duration="800" data-aos-delay="500">
            <a href="{{ route('register') }}" class="btn-modern btn-glow">
                Mulai Berkolaborasi 
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M5 12h14M12 5l7 7-7 7"/></svg>
            </a>
            <a href="#visi-misi" class="btn-modern btn-outline-modern" style="{{ (isset($banners) && $banners->count() > 0) ? 'background:rgba(255,255,255,0.1); color:white; border-color:white;' : '' }}">
                Pelajari Lebih Lanjut
            </a>
        </div>
    </div>
</section>

<!-- VISI MISI & TENTANG KAMI -->
<section id="visi-misi" class="py-5" style="position:relative; z-index:2;">
    <div class="container py-5">
        <div class="glass-card p-4 p-md-5" data-aos="fade-up" data-aos-duration="1000">
            <div class="row align-items-center">
                <div class="col-lg-5 mb-5 mb-lg-0">
                    <h2 class="mb-4" style="font-weight: 800; font-size:2.5rem; line-height: 1.2;">
                        Tentang Kami &<br>
                        <span style="color: var(--brand-primary);">Komunitas Terpadu</span>
                    </h2>
                    <p class="text-muted mb-4" style="line-height: 1.8; font-size: 1.1rem;">
                        Komunitas Jostru hadir sebagai ekosistem progresif yang menghubungkan berbagai aktivitas ekonomi, produk kreatif, dan inovasi ramah lingkungan ke dalam satu integrasi hijau yang solid.
                    </p>
                    <p class="text-muted" style="line-height: 1.8; font-size: 1.05rem;">
                        Fokus kami adalah pemberdayaan masyarakat dan peningkatan kualitas lingkungan melalui sistem ekonomi sirkular yang adil dan transparan.
                    </p>
                </div>
                <div class="col-lg-6 offset-lg-1">
                    <h4 class="mb-4" style="font-weight: 700;">Misi Utama Kami</h4>
                    <div class="d-flex flex-column gap-3">
                        <div class="p-3 rounded-4 bg-white shadow-sm border" data-aos="fade-left" data-aos-delay="100">
                            <div class="d-flex align-items-center gap-3">
                                <div class="bg-success bg-opacity-10 text-success p-2 rounded-circle">
                                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 2v20M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/></svg>
                                </div>
                                <div class="fw-semibold">Meningkatkan Perekonomian & Kesejahteraan Anggota</div>
                            </div>
                        </div>
                        <div class="p-3 rounded-4 bg-white shadow-sm border" data-aos="fade-left" data-aos-delay="200">
                            <div class="d-flex align-items-center gap-3">
                                <div class="bg-primary bg-opacity-10 text-primary p-2 rounded-circle">
                                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><path d="M12 8v8M8 12h8"/></svg>
                                </div>
                                <div class="fw-semibold">Menciptakan Peluang Usaha Baru & Inovatif</div>
                            </div>
                        </div>
                        <div class="p-3 rounded-4 bg-white shadow-sm border" data-aos="fade-left" data-aos-delay="300">
                            <div class="d-flex align-items-center gap-3">
                                <div class="bg-warning bg-opacity-10 text-warning p-2 rounded-circle">
                                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path><polyline points="22 4 12 14.01 9 11.01"></polyline></svg>
                                </div>
                                <div class="fw-semibold">Edukasi Nilai Ekologis & Peningkatan Kualitas Hidup</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- DIVISIONS / LAYANAN TERPADU -->
<section id="divisi" class="py-5 bg-white">
    <div class="container py-5">
        <div class="text-center mb-5" data-aos="fade-up">
            <span class="text-success fw-bold text-uppercase" style="letter-spacing:2px; font-size:0.9rem;">Holding Company</span>
            <h2 class="mt-2" style="font-size:2.5rem; font-weight:800;">Divisi Resmi <span style="color:var(--brand-primary);">Jostru Community</span></h2>
            <p class="text-muted mx-auto" style="max-width: 700px;">Jostru Community adalah ekosistem holding yang menaungi berbagai divisi operasional. Berikut adalah {{ $divisions->count() }} divisi resmi yang tergabung.</p>
        </div>
        
        <div class="row g-4 justify-content-center">
            @if(isset($divisions) && $divisions->count() > 0)
                @foreach($divisions as $index => $divisi)
                <div class="col-md-6 col-lg-4" data-aos="fade-up" data-aos-delay="{{ ($index % 3) * 100 + 100 }}">
                    <div class="glass-card p-4 h-100 text-center d-flex flex-column">
                        @php
                            $iconColor = 'rgba(34,197,94,0.1)';
                            $textColor = '#22c55e';
                            $iconSvg = '<svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg>';
                            
                            if($divisi->type == 'FARM') {
                                $iconSvg = '<svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 2v20M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/></svg>';
                            } elseif($divisi->type == 'PRODUCTION') {
                                $iconColor = 'linear-gradient(135deg, rgba(245,158,11,0.1), rgba(217,119,6,0.1))';
                                $textColor = '#f59e0b';
                                $iconSvg = '<svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="4" width="20" height="16" rx="2"/><line x1="2" y1="10" x2="22" y2="10"/></svg>';
                            } elseif($divisi->type == 'LIVESTOCK') {
                                $iconColor = 'linear-gradient(135deg, rgba(59,130,246,0.1), rgba(37,99,235,0.1))';
                                $textColor = '#3b82f6';
                                $iconSvg = '<svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 21h18M3 10h18M5 6l7-3 7 3M4 10v11m16-11v11"/></svg>';
                            }
                        @endphp
                        @if($divisi->logo)
                            <div class="mx-auto mb-3" style="height: 70px; display: flex; align-items: center; justify-content: center;">
                                <img src="{{ asset('media/divisions/' . $divisi->logo) }}" alt="Logo {{ $divisi->name }}" style="max-height: 100%; max-width: 100%; object-fit: contain;" loading="lazy">
                            </div>
                        @else
                            <div class="feature-icon-wrapper mx-auto" style="background: {{ $iconColor }}; color: {{ $textColor }};">
                                {!! $iconSvg !!}
                            </div>
                        @endif
                        <h4 class="fw-bold mb-3">{{ $divisi->name }}</h4>
                        <p class="text-muted small flex-grow-1">{{ Str::limit($divisi->description, 100) }}</p>
                        @if($divisi->slug)
                        <div class="mt-3">
                            <a href="{{ route('public.division', $divisi->slug) }}" class="btn btn-outline-success" style="border-radius: 20px; font-weight: 600;">Kunjungi Divisi</a>
                        </div>
                        @endif
                    </div>
                </div>
                @endforeach
            @else
                <div class="col-12 text-center py-4">
                    <p class="text-muted">Divisi sedang dalam tahap pengembangan.</p>
                </div>
            @endif
        </div>
    </div>
</section>

<!-- GALERI KEGIATAN -->
<section id="galeri" class="py-5">
    <div class="container py-5">
        <div class="text-center mb-5" data-aos="fade-up">
            <h2 class="mt-2" style="font-size:2.5rem; font-weight:800;">Jejak Karya & <span style="color:var(--brand-primary);">Kolaborasi</span></h2>
            <p class="text-muted mx-auto" style="max-width: 600px;">Momen-momen inspiratif dari anggota komunitas Jostru dalam menciptakan perubahan nyata.</p>
        </div>
        
        <div class="gallery-grid">
            @if(isset($galleryMedia) && $galleryMedia->count() > 0)
                @foreach($galleryMedia as $index => $media)
                    <div class="gallery-item" data-aos="fade-up" data-aos-delay="{{ ($index % 4) * 100 + 100 }}" onclick="openLightbox('{{ $media->url }}', '{{ $media->type }}', '{{ addslashes($media->title) }}')">
                        @if($media->type == 'image')
                            <img src="{{ $media->url }}" alt="{{ $media->title }}" loading="lazy">
                        @elseif($media->type == 'video')
                            <video src="{{ $media->url }}" style="width: 100%; height: 100%; object-fit: cover;"></video>
                            <div style="position:absolute; top:50%; left:50%; transform:translate(-50%,-50%); background:rgba(0,0,0,0.5); border-radius:50%; padding:15px; color:white; pointer-events:none;">▶</div>
                        @elseif($media->type == 'embed')
                            <div style="width: 100%; height: 100%; display:flex; align-items:center; justify-content:center; background:#1e293b; color:white;">
                                <div class="text-center">
                                    <svg width="40" height="40" fill="currentColor" viewBox="0 0 16 16"><path d="M8 0C5.829 0 5.556.01 4.703.048 3.85.088 3.269.222 2.76.42a3.917 3.917 0 0 0-1.417.923A3.927 3.927 0 0 0 .42 2.76C.222 3.268.087 3.85.048 4.7.01 5.555 0 5.827 0 8.001c0 2.172.01 2.444.048 3.297.04.852.174 1.433.372 1.942.205.526.478.972.923 1.417.444.445.89.719 1.416.923.51.198 1.09.333 1.942.372C5.555 15.99 5.827 16 8 16s2.444-.01 3.298-.048c.851-.04 1.434-.174 1.943-.372a3.916 3.916 0 0 0 1.416-.923c.445-.445.718-.891.923-1.417.197-.509.332-1.09.372-1.942C15.99 10.445 16 10.173 16 8s-.01-2.445-.048-3.299c-.04-.851-.175-1.433-.372-1.941a3.926 3.926 0 0 0-.923-1.417A3.911 3.911 0 0 0 13.24.42c-.51-.198-1.092-.333-1.943-.372C10.443.01 10.172 0 7.998 0h.003zm-.717 1.442h.718c2.136 0 2.389.007 3.232.046.78.036 1.204.166 1.486.275.373.145.64.319.92.599.28.28.453.546.598.92.11.281.24.705.275 1.485.039.843.047 1.096.047 3.231s-.008 2.389-.047 3.232c-.035.78-.166 1.203-.275 1.485a2.47 2.47 0 0 1-.599.919c-.28.28-.546.453-.92.598-.28.11-.704.24-1.485.276-.843.038-1.096.047-3.232.047s-2.39-.009-3.233-.047c-.78-.036-1.203-.166-1.485-.276a2.478 2.478 0 0 1-.92-.598 2.48 2.48 0 0 1-.6-.92c-.109-.281-.24-.705-.275-1.485-.038-.843-.046-1.096-.046-3.233 0-2.136.008-2.388.046-3.231.036-.78.166-1.204.276-1.486.145-.373.319-.64.599-.92.28-.28.546-.453.92-.598.282-.11.705-.24 1.485-.276.738-.034 1.024-.044 2.515-.045v.002z"/></svg>
                                    <div class="mt-2">Sosial Media Link</div>
                                </div>
                            </div>
                        @endif
                        <div class="gallery-overlay">
                            <h5 class="m-0 fw-bold">{{ $media->title }}</h5>
                            @if($media->category == 'post') <span class="badge bg-primary mt-2">Postingan Khusus</span> @endif
                        </div>
                    </div>
                @endforeach
            @else
                <div class="w-100 text-center py-4" style="grid-column: 1 / -1;">
                    <p class="text-muted mb-0">Belum ada media di Galeri. Tambahkan lewat CMS.</p>
                </div>
            @endif
        </div>
        <div class="text-center mt-5">
            <a href="{{ route('register') }}" class="btn-modern btn-outline-modern">Bergabung ke dalam Komunitas</a>
        </div>
    </div>
</section>

<!-- KONTAK & FAQ -->
<section id="kontak" class="py-5 bg-white">
    <div class="container py-5">
        <div class="row g-5">
            <div class="col-lg-5" data-aos="fade-right">
                <h2 style="font-size:2.5rem; font-weight:800; margin-bottom:1.5rem;">Mari Terhubung & <br><span style="color:var(--brand-primary);">Tumbuh Bersama</span></h2>
                <p class="text-muted mb-4" style="line-height:1.8;">Ada pertanyaan mengenai kemitraan, program edukasi, atau fitur aplikasi? Tim Jostru selalu siap membantu Anda mencapai potensi penuh dalam komunitas kami.</p>
                
                <div class="d-flex align-items-center gap-3 mb-4">
                    <div class="bg-success bg-opacity-10 text-success p-3 rounded-circle">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"/></svg>
                    </div>
                    <div>
                        <div class="text-muted small">Pusat Bantuan</div>
                        <div class="fw-bold">08137929313</div>
                    </div>
                </div>
                <div class="d-flex align-items-center gap-3 mb-4">
                    <div class="bg-primary bg-opacity-10 text-primary p-3 rounded-circle">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg>
                    </div>
                    <div>
                        <div class="text-muted small">Email Kemitraan</div>
                        <div class="fw-bold">plikocommunity@gmail.com</div>
                    </div>
                </div>
                <div class="d-flex align-items-center gap-3 mb-4">
                    <div class="p-3 rounded-circle" style="color: #e1306c; background: rgba(225, 48, 108, 0.1);">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="2" width="20" height="20" rx="5" ry="5"/><path d="M16 11.37A4 4 0 1 1 12.63 8 4 4 0 0 1 16 11.37z"/><line x1="17.5" y1="6.5" x2="17.51" y2="6.5"/></svg>
                    </div>
                    <div>
                        <div class="text-muted small">Instagram</div>
                        <div class="fw-bold"><a href="https://instagram.com/jostru_community" target="_blank" class="text-decoration-none text-dark">@jostru_community</a></div>
                    </div>
                </div>
            </div>
            
            <div class="col-lg-7" data-aos="fade-left">
                <div class="accordion" id="faqAccordion">
                    <!-- FAQ 1 -->
                    <div class="accordion-item">
                        <h2 class="accordion-header">
                            <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#faq1">
                                Bagaimana cara bergabung dengan ekosistem Jostru?
                            </button>
                        </h2>
                        <div id="faq1" class="accordion-collapse collapse show" data-bs-parent="#faqAccordion">
                            <div class="accordion-body">
                                Sangat mudah! Anda cukup klik tombol "Mulai Berkolaborasi" di halaman utama, isi data diri yang diperlukan, dan Anda langsung menjadi bagian dari gerakan ekonomi hijau kami.
                            </div>
                        </div>
                    </div>
                    <!-- FAQ 2 -->
                    <div class="accordion-item">
                        <h2 class="accordion-header">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq2">
                                Apa manfaat dari penyetoran potensi sumber daya ini?
                            </button>
                        </h2>
                        <div id="faq2" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                            <div class="accordion-body">
                                Selain membantu pelestarian lingkungan hidup secara nyata, sumber daya yang dikelola akan dikonversi menjadi poin insentif, edukasi gratis, serta membuka jaringan dan peluang ekonomi baru bagi setiap anggota.
                            </div>
                        </div>
                    </div>
                    <!-- FAQ 3 -->
                    <div class="accordion-item">
                        <h2 class="accordion-header">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq3">
                                Apakah platform ini dapat diakses melalui perangkat seluler?
                            </button>
                        </h2>
                        <div id="faq3" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                            <div class="accordion-body">
                                Tentu! Aplikasi web kami (PWA) sepenuhnya responsif. Anda dapat menyimpannya ke Layar Utama (Home Screen) smartphone Anda dan menggunakannya layaknya aplikasi seluler modern.
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- STATISTICS / COUNTER -->
<section class="stats-section">
    <div class="wave-separator">
        <svg data-name="Layer 1" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1200 120" preserveAspectRatio="none">
            <path d="M321.39,56.44c58-10.79,114.16-30.13,172-41.86,82.39-16.72,168.19-17.73,250.45-.39C823.78,31,906.67,72,985.66,92.83c70.05,18.48,146.53,26.09,214.34,3V0H0V27.35A600.21,600.21,0,0,0,321.39,56.44Z" class="shape-fill"></path>
        </svg>
    </div>
    <div class="container text-center pt-5">
        <div class="row g-4">
            <div class="col-md-4" data-aos="zoom-in" data-aos-delay="100">
                <div class="stat-number">{{\App\Models\User::whereNotIn('role', ['admin', 'superadmin'])->count()}}+</div>
                <div class="text-uppercase fw-bold" style="letter-spacing:1px; color:#cbd5e1;">Anggota Penggerak</div>
            </div>
            <div class="col-md-4" data-aos="zoom-in" data-aos-delay="200">
                <div class="stat-number">{{\App\Models\WasteDeposit::where('status', 'APPROVED')->sum('weight') ?? 0}}</div>
                <div class="text-uppercase fw-bold" style="letter-spacing:1px; color:#cbd5e1;">Kg Potensi Ekonomi Dikelola</div>
            </div>
            <div class="col-md-4" data-aos="zoom-in" data-aos-delay="300">
                <div class="stat-number">100%</div>
                <div class="text-uppercase fw-bold" style="letter-spacing:1px; color:#cbd5e1;">Dampak Positif & Transparansi</div>
            </div>
        </div>
        <div class="mt-5 pt-4 pb-5" data-aos="fade-up">
            <h3 class="mb-4 fw-bold">Wujudkan Lingkungan Asri Bersama Kami</h3>
            <a href="{{ route('register') }}" class="btn-modern btn-glow bg-white text-success border-0 px-5 py-3" style="font-size:1.2rem;">
                Gabung Komunitas
            </a>
        </div>
    </div>
</section>

<div id="mediaLightbox" class="lightbox">
    <div class="lightbox-close" onclick="closeLightbox()">&times;</div>
    <div id="lightboxContentContainer"></div>
    <div id="lightboxCaption" class="lightbox-caption"></div>
</div>

@push('scripts')
<!-- AOS Animation Script -->
<script src="https://unpkg.com/aos@2.3.1/dist/aos.js" defer></script>
<script>
function openLightbox(url, type, title) {
    let container = document.getElementById('lightboxContentContainer');
    let caption = document.getElementById('lightboxCaption');
    container.innerHTML = '';
    
    if (type === 'image') {
        container.innerHTML = '<img src="' + url + '" class="lightbox-content" style="max-height:80vh; max-width:90vw; object-fit:contain;">';
    } else if (type === 'video') {
        container.innerHTML = '<video src="' + url + '" controls autoplay class="lightbox-content" style="max-height:80vh; max-width:90vw;"></video>';
    } else if (type === 'embed') {
        if(url.includes('youtube.com/watch?v=')) {
            let vidId = url.split('v=')[1].split('&')[0];
            container.innerHTML = '<div style="width:80vw; max-width:800px; aspect-ratio:16/9;"><iframe width="100%" height="100%" src="https://www.youtube.com/embed/' + vidId + '?autoplay=1" frameborder="0" allowfullscreen></iframe></div>';
        } else if (url.includes('youtu.be/')) {
            let vidId = url.split('youtu.be/')[1].split('?')[0];
            container.innerHTML = '<div style="width:80vw; max-width:800px; aspect-ratio:16/9;"><iframe width="100%" height="100%" src="https://www.youtube.com/embed/' + vidId + '?autoplay=1" frameborder="0" allowfullscreen></iframe></div>';
        } else if (url.includes('instagram.com')) {
            container.innerHTML = '<div style="width:80vw; max-width:400px; height:80vh; background:white;"><iframe src="' + url + 'embed" width="100%" height="100%" frameborder="0"></iframe><div class="p-3 text-center"><a href="' + url + '" target="_blank" class="btn btn-primary">Buka IG Asli</a></div></div>';
        } else {
            container.innerHTML = '<div style="width:80vw; max-width:500px; height:80vh; background:white;"><iframe src="' + url + '" width="100%" height="100%" frameborder="0"></iframe><div class="p-3 text-center"><a href="' + url + '" target="_blank" class="btn btn-primary">Buka Link Asli</a></div></div>';
        }
    }
    
    caption.innerText = title;
    document.getElementById('mediaLightbox').classList.add('active');
}

function closeLightbox() {
    document.getElementById('mediaLightbox').classList.remove('active');
    document.getElementById('lightboxContentContainer').innerHTML = ''; 
}

document.getElementById('mediaLightbox').addEventListener('click', function(e) {
    if(e.target === this) {
        closeLightbox();
    }
});
</script>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        AOS.init({
            once: true,
            offset: 50,
            duration: 800,
            easing: 'ease-out-cubic',
        });
    });

    // PWA Android Install Prompt
    let deferredPrompt = null;
    window.addEventListener('beforeinstallprompt', (e) => {
        // Prevent Chrome 67 and earlier from automatically showing the prompt
        e.preventDefault();
        // Stash the event so it can be triggered later.
        deferredPrompt = e;
        // Update UI notify the user they can add to home screen
        const installBtn = document.getElementById('installBtn');
        if(installBtn) installBtn.style.display = 'inline-flex';
    });

    const installBtn = document.getElementById('installBtn');
    if(installBtn) {
        installBtn.addEventListener('click', async () => {
            if (deferredPrompt !== null) {
                deferredPrompt.prompt();
                const { outcome } = await deferredPrompt.userChoice;
                if (outcome === 'accepted') {
                    console.log('User accepted the A2HS prompt');
                } else {
                    console.log('User dismissed the A2HS prompt');
                }
                deferredPrompt = null;
            } else {
                alert('Aplikasi sudah terpasang atau browser Anda tidak mendukung instalasi otomatis. Silakan gunakan menu Chrome "Tambahkan ke Layar Utama".');
            }
        });
    }
</script>
@endpush
@endsection
