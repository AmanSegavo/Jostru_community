@extends('layouts.admin')

@section('title', 'Scanner Sertifikat Dividen')

@section('content')
<div class="container-fluid animate-fade-in">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 style="font-weight: 800; color: var(--text-color); margin-bottom: 0.5rem;">Scanner Keabsahan</h1>
            <p class="text-muted">Arahkan kamera ke QR Code pada sertifikat fisik atau digital.</p>
        </div>
        <a href="{{ route('admin.dividends.index') }}" class="btn btn-outline-secondary" style="border-radius: 12px; font-weight: 600;">
            ← Kembali
        </a>
    </div>

    <div class="card glass p-4 text-center" style="border-radius: 20px; max-width: 600px; margin: 0 auto;">
        <div id="reader" style="width: 100%; border-radius: 16px; overflow: hidden; border: 4px dashed rgba(34,197,94,0.3);"></div>
        <p class="mt-4 text-muted" id="scan-status">Menunggu izin kamera...</p>
    </div>
</div>

@push('scripts')
<script src="https://unpkg.com/html5-qrcode"></script>
<script>
    document.addEventListener("DOMContentLoaded", function() {
        const html5QrCode = new Html5Qrcode("reader");
        const qrCodeSuccessCallback = (decodedText, decodedResult) => {
            document.getElementById('scan-status').innerText = "QR Code terdeteksi! Mengalihkan...";
            document.getElementById('scan-status').style.color = "#22c55e";
            document.getElementById('scan-status').style.fontWeight = "bold";
            html5QrCode.stop();
            // Assuming the QR contains the full URL like https://jostru.site/verify-cert/JSF-PS-2024-0001
            window.location.href = decodedText;
        };
        const config = { fps: 10, qrbox: { width: 250, height: 250 } };
        html5QrCode.start({ facingMode: "environment" }, config, qrCodeSuccessCallback)
        .then(() => {
            document.getElementById('scan-status').innerText = "Arahkan QR Code ke dalam kotak";
        })
        .catch(err => {
            document.getElementById('scan-status').innerText = "Gagal mengakses kamera. " + err;
            document.getElementById('scan-status').style.color = "red";
        });
    });
</script>
@endpush
@endsection
