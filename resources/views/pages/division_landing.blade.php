<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $division->name }} - Divisi Jostru Community</title>
    <meta name="description" content="{{ $division->meta_description ?? $division->description ?? 'Informasi tentang divisi ' . $division->name . ' di bawah naungan Jostru Community Holding.' }}">
    <meta name="keywords" content="{{ $division->meta_keywords ?? 'jostru, divisi, ' . strtolower($division->name) }}">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;700;900&family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    
    <style>
        :root {
            --primary: #10b981;
            --primary-dark: #059669;
            --dark: #0f172a;
            --light: #f8fafc;
            --text-main: #1e293b;
            --text-muted: #64748b;
        }

        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background-color: var(--light);
            color: var(--text-main);
            overflow-x: hidden;
        }

        h1, h2, h3, h4, h5, h6 {
            font-family: 'Outfit', sans-serif;
        }

        /* Navbar */
        .navbar {
            background: rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(10px);
            border-bottom: 1px solid rgba(0,0,0,0.05);
            padding: 15px 0;
            transition: all 0.3s ease;
        }

        /* Hero Section */
        .hero {
            padding: 160px 0 100px;
            background: linear-gradient(135deg, rgba(16,185,129,0.05) 0%, rgba(255,255,255,1) 100%);
            position: relative;
        }
        
        .hero h1 {
            font-weight: 900;
            font-size: 3.5rem;
            line-height: 1.1;
            margin-bottom: 1.5rem;
            color: var(--dark);
        }

        .hero h1 span {
            color: var(--primary);
        }

        /* Glass Card */
        .glass-card {
            background: rgba(255, 255, 255, 0.7);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.5);
            border-radius: 24px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.05);
            padding: 40px;
        }

        /* Masonry Gallery */
        .masonry-grid {
            column-count: 3;
            column-gap: 20px;
            padding: 20px 0;
        }
        
        @media (max-width: 992px) { .masonry-grid { column-count: 2; } }
        @media (max-width: 576px) { .masonry-grid { column-count: 1; } }

        .masonry-item {
            break-inside: avoid;
            margin-bottom: 20px;
            border-radius: 16px;
            overflow: hidden;
            position: relative;
            background: #e2e8f0;
            box-shadow: 0 10px 20px rgba(0,0,0,0.05);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .masonry-item:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 30px rgba(0,0,0,0.1);
        }

        .masonry-item img, .masonry-item video {
            width: 100%;
            display: block;
            object-fit: cover;
        }

        /* Orientations */
        .ori-landscape { aspect-ratio: 4/3; }
        .ori-portrait { aspect-ratio: 3/4; }
        .ori-square { aspect-ratio: 1/1; }

        .masonry-overlay {
            position: absolute;
            bottom: 0; left: 0; right: 0;
            background: linear-gradient(to top, rgba(0,0,0,0.8), transparent);
            padding: 30px 20px 20px;
            color: white;
            opacity: 0;
            transition: opacity 0.3s ease;
        }

        .masonry-item:hover .masonry-overlay {
            opacity: 1;
        }

        /* Contact Section */
        .contact-icon {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            background: rgba(16,185,129,0.1);
            color: var(--primary);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            margin-bottom: 1rem;
        }

        /* Footer */
        footer {
            background: var(--dark);
            color: white;
            padding: 40px 0;
            text-align: center;
        }
    </style>
</head>
<body>

    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg fixed-top">
        <div class="container">
            <a class="navbar-brand fw-bold fs-4 d-flex align-items-center gap-2" href="{{ url('/') }}">
                @if($division->logo)
                    <img src="{{ asset('media/divisions/' . $division->logo) }}" alt="Logo" style="height: 40px; border-radius: 5px; object-fit: contain;">
                @else
                    <span style="width: 35px; height: 35px; background: var(--primary); border-radius: 10px; display: inline-block;"></span>
                @endif
                Jostru <span class="text-muted" style="font-size:1.2rem; font-weight:400;">| {{ $division->name }}</span>
            </a>
            <div class="d-flex gap-3">
                <a href="{{ url('/') }}" class="btn btn-light" style="border-radius: 30px; font-weight: 600;">Kembali ke Holding</a>
                <a href="{{ route('login') }}" class="btn btn-primary" style="border-radius: 30px; font-weight: 600;">Login Anggota</a>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="hero">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-6 mb-5 mb-lg-0">
                    <span class="badge bg-primary mb-3" style="font-size:0.9rem; padding:8px 16px; border-radius:30px;">Divisi Resmi Jostru Community</span>
                    
                    @if($division->logo)
                        <div class="mb-4">
                            <img src="{{ asset('media/divisions/' . $division->logo) }}" alt="{{ $division->name }} Logo" style="max-height: 120px; object-fit: contain; filter: drop-shadow(0 10px 15px rgba(0,0,0,0.1));">
                        </div>
                    @endif

                    <h1>Divisi <span>{{ $division->name }}</span></h1>
                    <p class="lead text-muted mb-4" style="line-height: 1.8;">{{ $division->description }}</p>
                    <a href="#about" class="btn btn-primary btn-lg" style="border-radius: 30px; font-weight: 600; padding: 12px 30px;">Pelajari Lebih Lanjut</a>
                </div>
                <div class="col-lg-6">
                    @if($banners->count() > 0)
                        <div id="heroCarousel" class="carousel slide" data-bs-ride="carousel" style="border-radius: 24px; overflow: hidden; box-shadow: 0 20px 40px rgba(0,0,0,0.1); background: #e2e8f0;">
                            <div class="carousel-inner">
                                @foreach($banners as $index => $banner)
                                <div class="carousel-item {{ $index == 0 ? 'active' : '' }}">
                                    @if($banner->type == 'image')
                                        <img src="{{ asset('media/' . $banner->filename) }}" class="d-block w-100" alt="{{ $banner->title }}" style="width: 100%; aspect-ratio: 16/9; object-fit: contain;">
                                    @endif
                                </div>
                                @endforeach
                            </div>
                        </div>
                    @else
                        <div class="glass-card text-center py-5" style="border: 2px dashed rgba(16,185,129,0.3);">
                            <h3 class="text-muted"><i class="fas fa-image mb-3"></i><br>Tidak Ada Banner</h3>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </section>

    <!-- About Section -->
    <section id="about" class="py-5">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-10">
                    <div class="glass-card text-center">
                        <h2 class="fw-bold mb-4">Tentang Divisi Ini</h2>
                        <div class="fs-5 text-muted" style="line-height: 1.8; text-align: justify;">
                            {!! nl2br(e($division->about_text ?? 'Belum ada informasi lengkap tentang divisi ini. Kami sedang memperbarui data kami.')) !!}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Gallery Section (Masonry) -->
    <section class="py-5 bg-white">
        <div class="container">
            <div class="text-center mb-5">
                <h2 class="fw-bold">Galeri & Kegiatan</h2>
                <p class="text-muted">Dokumentasi dan aktivitas terbaru dari {{ $division->name }}</p>
            </div>

            @if($galleryMedia->count() > 0)
            <div class="masonry-grid">
                @foreach($galleryMedia as $media)
                <div class="masonry-item ori-{{ $media->orientation ?? 'landscape' }}">
                    @if($media->type == 'image')
                        <img src="{{ asset('media/' . $media->filename) }}" alt="{{ $media->title }}" loading="lazy">
                    @elseif($media->type == 'video')
                        <video src="{{ asset('media/' . $media->filename) }}" muted loop autoplay playsinline></video>
                    @elseif($media->type == 'embed')
                        <div style="width:100%; height:100%; background:#1e293b; display:flex; align-items:center; justify-content:center; padding:20px;">
                            <a href="{{ $media->source_url }}" target="_blank" class="btn btn-outline-light" style="border-radius:20px;">Tonton di Sumber <i class="fas fa-external-link-alt ms-2"></i></a>
                        </div>
                    @endif
                    <div class="masonry-overlay">
                        <h5 class="mb-0 fw-bold">{{ $media->title }}</h5>
                    </div>
                </div>
                @endforeach
            </div>
            @else
            <div class="text-center py-5 text-muted">
                <i class="fas fa-folder-open fs-1 mb-3"></i>
                <h5>Belum ada media di galeri ini.</h5>
            </div>
            @endif
        </div>
    </section>

    <!-- Contact Section -->
    <section class="py-5 mb-5">
        <div class="container">
            <div class="glass-card">
                <div class="text-center mb-5">
                    <h2 class="fw-bold">Hubungi Divisi</h2>
                    <p class="text-muted">Punya pertanyaan atau ingin bekerja sama dengan {{ $division->name }}?</p>
                </div>
                <div class="row text-center g-4">
                    <div class="col-md-4">
                        <div class="d-flex flex-column align-items-center">
                            <div class="contact-icon"><i class="fas fa-map-marker-alt"></i></div>
                            <h5 class="fw-bold">Alamat</h5>
                            <p class="text-muted">{{ $division->address ?: 'Belum ada data alamat.' }}</p>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="d-flex flex-column align-items-center">
                            <div class="contact-icon"><i class="fas fa-envelope"></i></div>
                            <h5 class="fw-bold">Email</h5>
                            <p class="text-muted">{{ $division->email ?: 'Belum ada email.' }}</p>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="d-flex flex-column align-items-center">
                            <div class="contact-icon"><i class="fas fa-phone"></i></div>
                            <h5 class="fw-bold">Telepon/WA</h5>
                            <p class="text-muted">{{ $division->phone_number ?: 'Belum ada nomor telepon.' }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer>
        <div class="container">
            <h4 class="fw-bold mb-3">Jostru Community Holding</h4>
            <p class="mb-0 text-muted">© {{ date('Y') }} Divisi {{ $division->name }}. All rights reserved.</p>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
