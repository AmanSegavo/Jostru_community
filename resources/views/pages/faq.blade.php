@extends('layouts.app')
@section('title', 'FAQ - Pertanyaan Seputar Jostru Community')

@push('styles')
<style>
    .faq-hero {
        padding: 60px 0 40px;
        text-align: center;
    }
    .accordion-button:not(.collapsed) {
        background-color: rgba(34, 197, 94, 0.1);
        color: var(--primary);
        font-weight: 700;
        box-shadow: none;
    }
    .accordion-button {
        font-weight: 600;
        border-radius: 12px !important;
    }
    .accordion-item {
        border: 1px solid rgba(255, 255, 255, 0.1);
        border-radius: 12px !important;
        margin-bottom: 12px;
        background: var(--surface-color);
        overflow: hidden;
    }
    .accordion-button:focus {
        box-shadow: none;
        border-color: rgba(34, 197, 94, 0.5);
    }
</style>
@endpush

@section('content')
<div class="faq-hero">
    <div class="container">
        <h1 class="fw-bold" style="color: var(--primary);">Pertanyaan yang Sering Diajukan (FAQ)</h1>
        <p class="text-muted mt-3">Temukan jawaban atas pertanyaan umum mengenai layanan dan komunitas Jostru.</p>
    </div>
</div>

<div class="container mb-5 pb-5" style="max-width: 800px;">
    <div class="accordion" id="faqAccordion">
        
        <!-- FAQ Item 1 -->
        <div class="accordion-item glass">
            <h2 class="accordion-header" id="headingOne">
                <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
                    Apa itu Jostru Community?
                </button>
            </h2>
            <div id="collapseOne" class="accordion-collapse collapse show" aria-labelledby="headingOne" data-bs-parent="#faqAccordion">
                <div class="accordion-body text-muted">
                    Jostru Community adalah platform dan komunitas pengelolaan limbah digital yang memungkinkan anggotanya untuk melaporkan dan menyetorkan limbah mereka agar dikelola secara tepat, serta menukarkannya dengan berbagai *reward* yang menarik.
                </div>
            </div>
        </div>

        <!-- FAQ Item 2 -->
        <div class="accordion-item glass">
            <h2 class="accordion-header" id="headingTwo">
                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseTwo" aria-expanded="false" aria-controls="collapseTwo">
                    Limbah apa saja yang bisa disetorkan?
                </button>
            </h2>
            <div id="collapseTwo" class="accordion-collapse collapse" aria-labelledby="headingTwo" data-bs-parent="#faqAccordion">
                <div class="accordion-body text-muted">
                    Kami menerima berbagai jenis limbah, antara lain:
                    <ul>
                        <li><strong>Organik:</strong> Sisa makanan, sayuran, dedaunan.</li>
                        <li><strong>Anorganik:</strong> Plastik, kardus, kertas, logam, kaca.</li>
                        <li><strong>B3 (Bahan Berbahaya Beracun):</strong> Baterai bekas, lampu neon, kemasan disinfektan.</li>
                    </ul>
                </div>
            </div>
        </div>

        <!-- FAQ Item 3 -->
        <div class="accordion-item glass">
            <h2 class="accordion-header" id="headingThree">
                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseThree" aria-expanded="false" aria-controls="collapseThree">
                    Bagaimana cara melaporkan/menyetorkan limbah?
                </button>
            </h2>
            <div id="collapseThree" class="accordion-collapse collapse" aria-labelledby="headingThree" data-bs-parent="#faqAccordion">
                <div class="accordion-body text-muted">
                    Setelah Anda membuat akun dan login, buka halaman <strong>Dashboard</strong> lalu pilih menu <strong>Input Limbah</strong>. Ambil foto limbah Anda, masukkan perkiraan beratnya, lalu klik tombol kirim. Petugas kami akan segera memvalidasi laporan Anda.
                </div>
            </div>
        </div>

        <!-- FAQ Item 4 -->
        <div class="accordion-item glass">
            <h2 class="accordion-header" id="headingFour">
                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseFour" aria-expanded="false" aria-controls="collapseFour">
                    Apakah aplikasi ini berbayar?
                </button>
            </h2>
            <div id="collapseFour" class="accordion-collapse collapse" aria-labelledby="headingFour" data-bs-parent="#faqAccordion">
                <div class="accordion-body text-muted">
                    Tidak. Pendaftaran dan penggunaan layanan utama Jostru Community sepenuhnya <strong>GRATIS</strong> untuk seluruh masyarakat.
                </div>
            </div>
        </div>

    </div>

    <div class="text-center mt-5">
        <p class="text-muted">Masih memiliki pertanyaan?</p>
        <a href="/#kontak" class="btn btn-outline-primary" style="border-radius: 50px;">Hubungi Kami</a>
    </div>
</div>
@endsection
