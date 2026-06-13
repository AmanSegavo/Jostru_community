@extends('layouts.app')

@section('title', 'Digital ID Card - Jostru')

@push('styles')
<style>
    body { background-color: #f1f5f9; }
    
    .card-container {
        display: flex;
        justify-content: center;
        align-items: center;
        padding: 2rem;
        perspective: 1000px;
    }

    #id-card-wrapper {
        width: 856px;
        height: 540px;
        position: relative;
        border-radius: 24px;
        background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);
        box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5), inset 0 0 0 1px rgba(255,255,255,0.1);
        overflow: hidden;
        font-family: 'Inter', sans-serif;
        color: #ffffff;
        transform-origin: top center;
    }

    /* Scaling for mobile */
    @media (max-width: 900px) {
        #id-card-wrapper { transform: scale(0.8); margin-bottom: -108px; }
    }
    @media (max-width: 700px) {
        #id-card-wrapper { transform: scale(0.6); margin-bottom: -216px; }
    }
    @media (max-width: 500px) {
        #id-card-wrapper { transform: scale(0.4); margin-bottom: -324px; }
    }

    .card-bg-decoration {
        position: absolute;
        width: 600px;
        height: 600px;
        background: radial-gradient(circle, rgba(37,99,235,0.4) 0%, rgba(37,99,235,0) 70%);
        top: -200px;
        right: -200px;
        border-radius: 50%;
        z-index: 1;
    }

    .card-bg-decoration-2 {
        position: absolute;
        width: 400px;
        height: 400px;
        background: radial-gradient(circle, rgba(34,197,94,0.3) 0%, rgba(34,197,94,0) 70%);
        bottom: -150px;
        left: -100px;
        border-radius: 50%;
        z-index: 1;
    }

    .card-content {
        position: relative;
        z-index: 10;
        height: 100%;
        display: flex;
        flex-direction: column;
        padding: 40px 50px;
    }

    .card-header {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        border-bottom: 2px solid rgba(255,255,255,0.1);
        padding-bottom: 20px;
        margin-bottom: 30px;
    }

    .card-header img {
        height: 70px;
        object-fit: contain;
    }

    .card-title {
        text-align: right;
    }
    
    .card-title h2 {
        font-weight: 900;
        font-size: 28px;
        margin: 0;
        letter-spacing: 2px;
        color: #e2e8f0;
    }
    .card-title p {
        color: #38bdf8;
        font-weight: 600;
        font-size: 14px;
        margin: 5px 0 0 0;
        letter-spacing: 4px;
        text-transform: uppercase;
    }

    .card-body-inner {
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex: 1;
    }

    .user-details {
        flex: 1;
        padding-right: 40px;
    }

    .detail-group {
        margin-bottom: 24px;
    }
    
    .detail-label {
        font-size: 13px;
        color: #94a3b8;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 1.5px;
        margin-bottom: 4px;
    }
    
    .detail-value {
        font-size: 26px;
        font-weight: 800;
        color: #ffffff;
        margin: 0;
        text-shadow: 0 2px 4px rgba(0,0,0,0.3);
    }
    
    .detail-value.name {
        font-size: 34px;
        color: #fbbf24; /* Gold */
    }

    .qr-container {
        background: rgba(255, 255, 255, 0.95);
        padding: 15px;
        border-radius: 20px;
        box-shadow: 0 10px 25px rgba(0,0,0,0.3);
        display: flex;
        flex-direction: column;
        align-items: center;
    }

    .qr-container img {
        width: 180px;
        height: 180px;
        border-radius: 10px;
    }

    .qr-footer {
        margin-top: 10px;
        font-size: 11px;
        font-weight: 700;
        color: #1e293b;
        text-transform: uppercase;
        letter-spacing: 1px;
    }

    .glass-panel {
        background: rgba(255, 255, 255, 0.05);
        backdrop-filter: blur(10px);
        -webkit-backdrop-filter: blur(10px);
        border: 1px solid rgba(255, 255, 255, 0.1);
        border-radius: 20px;
        padding: 30px;
    }

    .action-panel {
        background: white;
        border-radius: 24px;
        padding: 2rem;
        box-shadow: 0 20px 40px rgba(0,0,0,0.08);
        text-align: center;
        max-width: 600px;
        margin: 2rem auto;
    }
</style>
@endpush

@section('content')
<div class="container mt-4 mb-5 animate-fade-in">
    <div class="text-center mb-4">
        <h1 style="font-weight: 900; color: #1e293b;">Digital ID Card</h1>
        <p class="text-muted">Kartu Identitas Resmi Anggota Jostru Community</p>
    </div>

    <!-- The ID Card -->
    <div class="card-container">
        <div id="id-card-wrapper">
            <div class="card-bg-decoration"></div>
            <div class="card-bg-decoration-2"></div>
            
            <div class="card-content">
                <!-- Header -->
                <div class="card-header">
                    <img src="{{ asset('images/logo.png') }}" alt="Jostru Logo" onerror="this.src='https://ui-avatars.com/api/?name=Jostru&background=0D8ABC&color=fff&size=100'">
                    <div class="card-title">
                        <h2>JOSTRU COMMUNITY</h2>
                        <p>Official Member Card</p>
                    </div>
                </div>

                <!-- Body -->
                <div class="card-body-inner">
                    <div class="user-details glass-panel">
                        <div class="detail-group">
                            <div class="detail-label">Nama Lengkap</div>
                            <div class="detail-value name">{{ strtoupper(auth()->user()->name) }}</div>
                        </div>
                        
                        <div class="detail-group">
                            <div class="detail-label">ID Anggota</div>
                            <div class="detail-value" style="font-family: monospace; letter-spacing: 2px;">{{ auth()->user()->member_id ?? 'JC-NEW' }}</div>
                        </div>

                        <div class="row">
                            <div class="col-6 detail-group mb-0">
                                <div class="detail-label">Jabatan Utama</div>
                                <div class="detail-value" style="font-size: 20px;">
                                    {{ strtoupper(auth()->user()->jabatan ?? 'ANGGOTA') }} 
                                    @if(auth()->user()->google_id) <br><span style="font-size: 14px; color:#94a3b8;">(Google)</span> @endif
                                </div>
                            </div>
                            <div class="col-6 detail-group mb-0">
                                <div class="detail-label">Status</div>
                                <div class="detail-value text-success" style="font-size: 20px; color: #4ade80 !important;">AKTIF</div>
                            </div>
                        </div>
                    </div>

                    <!-- QR Code -->
                    <div class="qr-container">
                        @php
                            $qrUrl = urlencode(url('/v/' . (auth()->user()->member_id ?? 'JC-NEW')));
                        @endphp
                        <img src="https://api.qrserver.com/v1/create-qr-code/?size=300x300&data={{ $qrUrl }}&margin=0" alt="QR Code" crossorigin="anonymous">
                        <div class="qr-footer">Scan untuk Validasi</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Actions -->
    <div class="action-panel">
        <h4 style="font-weight: 800; margin-bottom: 1rem;">Unduh Kartu Anda</h4>
        <p class="text-muted mb-4">Kartu identitas ini menggunakan teknologi CSS modern. Klik tombol di bawah untuk mengunduhnya sebagai file gambar kualitas tinggi.</p>
        
        <button id="download-btn" class="btn btn-primary btn-lg" style="border-radius: 16px; font-weight: 700; padding: 15px 40px; font-size: 18px; box-shadow: 0 10px 25px rgba(37, 99, 235, 0.3);">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="margin-right: 8px; margin-top:-3px;"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path><polyline points="7 10 12 15 17 10"></polyline><line x1="12" y1="15" x2="12" y2="3"></line></svg>
            Unduh Kartu (PNG)
        </button>
    </div>
</div>

@push('scripts')
<!-- Load html2canvas -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
<script>
    document.getElementById('download-btn').addEventListener('click', function() {
        const btn = this;
        const originalText = btn.innerHTML;
        btn.innerHTML = 'Sedang Merender... ⏳';
        btn.disabled = true;

        const cardElement = document.getElementById('id-card-wrapper');
        
        // We temporarily remove the scaling to get a full-resolution 856x540 capture
        const oldTransform = cardElement.style.transform;
        cardElement.style.transform = 'none';

        html2canvas(cardElement, {
            scale: 3, // High resolution (856*3 x 540*3)
            useCORS: true, // Allow external QR code images
            backgroundColor: null // Transparent background
        }).then(canvas => {
            // Restore scale
            cardElement.style.transform = oldTransform;
            
            // Trigger download
            const link = document.createElement('a');
            link.download = 'ID_Card_Jostru_{{ auth()->user()->name }}.png';
            link.href = canvas.toDataURL('image/png');
            link.click();

            btn.innerHTML = originalText;
            btn.disabled = false;
        }).catch(err => {
            console.error('Error generating image:', err);
            alert('Terjadi kesalahan saat mengunduh gambar. Pastikan internet Anda stabil untuk memuat QR Code.');
            btn.innerHTML = originalText;
            btn.disabled = false;
        });
    });
</script>
@endpush
@endsection