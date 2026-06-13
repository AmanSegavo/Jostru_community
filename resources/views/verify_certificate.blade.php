<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verifikasi Sertifikat Dividen - Jostru Farm</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background: #f8fafc; font-family: 'Inter', sans-serif; display: flex; align-items: center; justify-content: center; min-height: 100vh; margin: 0; }
        .glass { background: rgba(255, 255, 255, 0.95); backdrop-filter: blur(10px); border: 1px solid rgba(255, 255, 255, 0.5); box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.1); border-radius: 24px; padding: 2.5rem; width: 100%; max-width: 500px; text-align: center; }
        .logo { width: 100px; margin-bottom: 1.5rem; }
        .verified-badge { color: #16a34a; background: rgba(34, 197, 94, 0.1); border: 1px solid rgba(34, 197, 94, 0.2); padding: 0.5rem 1rem; border-radius: 50px; display: inline-block; font-weight: 700; margin-bottom: 1.5rem; }
    </style>
</head>
<body>
    <div class="glass">
        <img src="{{ asset('images/logo.png') }}" alt="Logo Jostru" class="logo">
        <h3 style="font-weight: 800; color: #1e293b;">Verifikasi Sertifikat</h3>
        <p class="text-muted mb-4">Sertifikat ID: <strong>{{ $shareholder->certificate_id }}</strong></p>

        @if(session('verified_cert_' . $shareholder->certificate_id))
            <div class="verified-badge">✅ SERTIFIKAT TERVERIFIKASI SAH</div>
            <div style="text-align: left; background: #f1f5f9; padding: 1.5rem; border-radius: 16px;">
                <p class="mb-2 text-muted" style="font-size: 0.85rem; font-weight: 600;">NAMA PEMEGANG</p>
                <h5 style="font-weight: 800; margin-bottom: 1rem;">{{ $shareholder->name }}</h5>
                
                <p class="mb-2 text-muted" style="font-size: 0.85rem; font-weight: 600;">KEPEMILIKAN DIVIDEN</p>
                <h5 style="font-weight: 800; color: #2563eb; margin-bottom: 1rem;">{{ $shareholder->percentage }}% ({{ $shareholder->percentage_text }})</h5>

                <p class="mb-2 text-muted" style="font-size: 0.85rem; font-weight: 600;">TANGGAL TERBIT</p>
                <h5 style="font-weight: 800;">{{ \Carbon\Carbon::parse($shareholder->issue_date)->translatedFormat('d F Y') }}</h5>
            </div>
            <a href="/" class="btn btn-outline-secondary w-100 mt-4" style="border-radius: 12px; font-weight: 600;">Kembali ke Beranda</a>
        @else
            <form action="{{ route('verify.cert.post', $shareholder->certificate_id) }}" method="POST">
                @csrf
                <div class="mb-4 text-start">
                    <label style="font-weight: 700; color: #475569; margin-bottom: 8px;">Masukkan PIN Rahasia Anggota</label>
                    <input type="text" name="pin" class="form-control" placeholder="6 Karakter PIN" required style="border-radius: 12px; padding: 12px; text-transform: uppercase; text-align: center; font-size: 1.25rem; font-weight: 800; letter-spacing: 4px;">
                </div>
                
                @if(session('error'))
                    <div class="alert alert-danger" style="border-radius: 12px; font-weight: 600;">{{ session('error') }}</div>
                @endif
                @if(session('success'))
                    <div class="alert alert-success" style="border-radius: 12px; font-weight: 600;">{{ session('success') }}</div>
                @endif

                <button type="submit" class="btn btn-primary w-100" style="border-radius: 12px; padding: 12px; font-weight: 700; background: linear-gradient(135deg, #2563eb, #1d4ed8); border: none;">
                    Verifikasi Sertifikat
                </button>
            </form>
        @endif
    </div>
</body>
</html>
