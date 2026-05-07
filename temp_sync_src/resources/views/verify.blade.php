<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistem Verifikasi Jostru Community</title>
    <style>
        body { font-family: 'Inter', Arial, sans-serif; background-color: #f3f4f6; margin: 0; padding: 2rem; display: flex; justify-content: center; align-items: center; min-height: 100vh; }
        .card { background: white; border-radius: 12px; padding: 2rem; max-width: 400px; width: 100%; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1); text-align: center; }
        .success-icon { color: #22c55e; margin-bottom: 1rem; }
        .error-icon { color: #ef4444; margin-bottom: 1rem; }
        .title { font-size: 1.5rem; font-weight: bold; margin-bottom: 0.5rem; color: #111827; }
        .subtitle { color: #6b7280; font-size: 0.875rem; margin-bottom: 2rem; }
        .profile { border-top: 1px solid #e5e7eb; padding-top: 1.5rem; text-align: left; }
        .row { margin-bottom: 1rem; }
        .label { font-size: 0.75rem; text-transform: uppercase; color: #9ca3af; font-weight: 600; letter-spacing: 0.05em; }
        .value { font-size: 1rem; font-weight: 500; color: #374151; margin-top: 0.25rem; }
        .badge { display: inline-block; padding: 0.25rem 0.75rem; border-radius: 9999px; font-size: 0.875rem; font-weight: 600; }
        .badge-active { background: #dcfce7; color: #166534; }
        .badge-inactive { background: #fee2e2; color: #991b1b; }
        .btn { display: inline-block; background: #2563eb; color: white; padding: 0.75rem 1.5rem; border-radius: 8px; text-decoration: none; font-weight: 500; margin-top: 1.5rem; width: 100%; box-sizing: border-box; }
        .btn:hover { background: #1d4ed8; }
    </style>
</head>
<body>
    <div class="card">
        @if($isValid)
            <div class="success-icon">
                <svg width="64" height="64" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            </div>
            <h1 class="title">Kartu Resmi</h1>
            <p class="subtitle">Identitas Anggota diterbitkan dan diawasi oleh Jostru Community</p>
            
            <div class="profile">
                <div class="row">
                    <div class="label">Nama Lengkap</div>
                    <div class="value">{{ $user->name }}</div>
                </div>
                <div class="row">
                    <div class="label">ID Autentikasi</div>
                    <div class="value" style="font-family: monospace; color: #4f46e5;">{{ $user->member_id }}</div>
                </div>
                <div class="row">
                    <div class="label">Jabatan</div>
                    <div class="value">{{ $user->jabatan ?? 'Anggota' }}</div>
                </div>
                <div class="row">
                    <div class="label">Status</div>
                    <div class="value" style="margin-top: 0.5rem;">
                        @if(($user->status ?? 'AKTIF') === 'AKTIF')
                            <span class="badge badge-active">AKTIF</span>
                        @else
                            <span class="badge badge-inactive">TIDAK AKTIF</span>
                        @endif
                    </div>
                </div>
            </div>
            <a href="{{ route('login') }}" class="btn">Login sebagai {{ $user->name }}</a>
        @else
            <div class="error-icon">
                <svg width="64" height="64" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            </div>
            <h1 class="title">ID Tidak Valid</h1>
            <p class="subtitle" style="margin-bottom: 0;">{{ $message }}</p>
        @endif
    </div>
</body>
</html>
