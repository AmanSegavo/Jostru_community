@extends('layouts.app')

@section('content')
<link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />
<link rel="stylesheet" href="https://unpkg.com/leaflet-control-geocoder/dist/Control.Geocoder.css" />
<script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
<script src="https://unpkg.com/leaflet-control-geocoder/dist/Control.Geocoder.js"></script>

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <h2 class="mb-4 text-white">Profil & Biodata Saya</h2>

            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" style="background: rgba(34, 197, 94, 0.1); color: #22c55e; border: 1px solid rgba(34,197,94,0.2);">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if($errors->any())
                <div class="alert alert-danger" style="background: rgba(239, 68, 68, 0.1); color: #ef4444; border: 1px solid rgba(239,68,68,0.2);">
                    <ul class="mb-0">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="card p-4 glass" style="border-radius: 16px; border: 1px solid var(--border-color);">
                <form action="{{ route('member.profile.update') }}" method="POST" id="profile-form" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="latitude" id="lat" value="{{ $user->latitude }}">
                    <input type="hidden" name="longitude" id="lng" value="{{ $user->longitude }}">

                    <div class="row mb-3">
                        <div class="col-md-6 mb-3 mb-md-0">
                            <label class="form-label" style="color: var(--text-secondary);">Nama Lengkap</label>
                            <input type="text" class="form-control" value="{{ $user->name }}" readonly>
                            <small class="text-muted d-block mt-1" style="font-size: 11px;">Hubungi Admin untuk mengubah nama</small>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label" style="color: var(--text-secondary);">Email Akses</label>
                            <div class="d-flex align-items-center gap-2">
                                <input type="email" class="form-control" value="{{ $user->email }}" readonly style="flex:1;">
                                
                                @if($user->google_id)
                                    <span class="badge bg-success d-flex align-items-center gap-1" style="height:38px; padding:0 12px; border-radius:6px; font-size:12px;">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path><polyline points="22 4 12 14.01 9 11.01"></polyline></svg>
                                        Google
                                    </span>
                                @else
                                    <a href="{{ route('auth.google') }}" class="btn btn-outline-light d-flex align-items-center gap-2" style="height:38px; font-size:12px; white-space:nowrap;">
                                        <svg width="16" height="16" viewBox="0 0 24 24"><path d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z" fill="#4285F4"/><path d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z" fill="#34A853"/><path d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l3.66-2.84z" fill="#FBBC05"/><path d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 12-4.53z" fill="#EA4335"/></svg>
                                        Tautkan Google
                                    </a>
                                @endif
                            </div>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6 mb-3 mb-md-0">
                            <label class="form-label" style="color: var(--text-secondary);">Tanggal Lahir</label>
                            <input type="date" name="tanggal_lahir" class="form-control" required value="{{ $user->tanggal_lahir }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label" style="color: var(--text-secondary);">Ganti Password (Opsional)</label>
                            <input type="password" name="password" class="form-control" placeholder="Kosongkan jika tidak diubah">
                        </div>
                    </div>

                    <!-- Alamat & Peta -->
                    <div class="mb-4">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <label class="form-label mb-0" style="color: var(--text-secondary); font-weight: 600;">
                                Alamat Domisili & Koordinat
                            </label>
                            <button type="button" id="btn-reset-map" class="btn btn-sm btn-outline-secondary" style="font-size:11px; border-radius:20px;">
                                Reset Peta
                            </button>
                        </div>

                        <!-- Peta & GPS (Super Simpel) -->
                        <div class="mb-3 p-3" style="background: rgba(34,197,94,0.05); border: 2px dashed #22c55e; border-radius: 12px; text-align: center;">
                            <h5 style="color: #22c55e; font-weight: 700; margin-bottom: 10px;">📍 Atur Lokasi Anda (Otomatis)</h5>
                            <p style="font-size: 13px; color: var(--text-secondary); margin-bottom: 15px;">
                                Klik tombol di bawah ini agar sistem otomatis mencari alamat rumah Anda menggunakan GPS HP Anda.
                            </p>
                            <button type="button" id="btn-gps-auto" class="btn btn-success" style="font-weight: 800; padding: 12px 24px; border-radius: 50px; box-shadow: 0 4px 15px rgba(34,197,94,0.3); width: 100%; max-width: 300px;">
                                <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" style="margin-right: 8px;"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path><circle cx="12" cy="10" r="3"></circle></svg>
                                DETEKSI LOKASI SAYA
                            </button>
                        </div>

                        <div id="selfMap" style="height: 380px; width:100%; border-radius:12px; border:1px solid var(--border-color); z-index:1;"></div>

                        <textarea name="alamat" id="alamatDetailed" class="form-control mt-3" rows="2" placeholder="Tulis jalan/patokan rumah Anda (contoh: Depan masjid)..." required>{{ $user->alamat }}</textarea>
                        <small class="text-muted d-block mt-1" style="font-size: 11px;">Alamat dan lokasi ini akan memudahkan kurir menjemput sampah Anda.</small>
                    </div>

                    <!-- Keamanan Kartu Digital -->
                    <div class="mb-4 p-3" style="background: rgba(59, 130, 246, 0.05); border: 1px solid rgba(59, 130, 246, 0.2); border-radius: 12px;">
                        <h6 style="color: #3b82f6; font-weight: 700; margin-bottom: 10px;">🛡️ Keamanan Kartu Digital</h6>
                        <div class="form-check form-switch d-flex align-items-center gap-3">
                            <input class="form-check-input" type="checkbox" name="card_2fa_enabled" id="card_2fa_enabled" value="1" {{ $user->card_2fa_enabled ? 'checked' : '' }} style="width: 40px; height: 20px; cursor: pointer;">
                            <label class="form-check-label" for="card_2fa_enabled" style="color: var(--text-secondary); cursor: pointer; padding-top: 2px;">
                                Aktifkan Verifikasi 2 Langkah (Wajib Masukkan Sandi saat melihat Kartu Digital)
                            </label>
                        </div>
                    </div>
                    
                    <h5 style="font-weight:700; color:var(--text-primary); margin-top:2rem; margin-bottom:1rem; border-bottom: 2px solid var(--border-color); padding-bottom:10px;">Dokumen Pribadi</h5>
                    <p class="text-muted small mb-4">Anda bisa memperbarui atau mengunggah dokumen Anda di sini (Format: JPG, PNG, PDF | Maks: 5MB per file). Kosongkan jika tidak ingin mengubah.</p>

                    <div class="row mb-4">
                        <div class="col-md-6 mb-3">
                            <label class="form-label" style="font-weight:600; color:var(--text-secondary);">File KTP</label>
                            <input type="file" name="ktp" class="form-control" accept=".jpg,.jpeg,.png,.pdf">
                            @if($user->ktp_path)
                                <div class="mt-2"><a href="{{ asset('storage/'.$user->ktp_path) }}" target="_blank" class="badge bg-primary text-decoration-none"><i class="bi bi-download me-1"></i> Lihat KTP</a></div>
                            @endif
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label" style="font-weight:600; color:var(--text-secondary);">File Kartu Keluarga (KK)</label>
                            <input type="file" name="kk" class="form-control" accept=".jpg,.jpeg,.png,.pdf">
                            @if($user->kk_path)
                                <div class="mt-2"><a href="{{ asset('storage/'.$user->kk_path) }}" target="_blank" class="badge bg-primary text-decoration-none"><i class="bi bi-download me-1"></i> Lihat KK</a></div>
                            @endif
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label" style="font-weight:600; color:var(--text-secondary);">File Ijazah Terakhir</label>
                            <input type="file" name="ijazah" class="form-control" accept=".jpg,.jpeg,.png,.pdf">
                            @if($user->ijazah_path)
                                <div class="mt-2"><a href="{{ asset('storage/'.$user->ijazah_path) }}" target="_blank" class="badge bg-primary text-decoration-none"><i class="bi bi-download me-1"></i> Lihat Ijazah</a></div>
                            @endif
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label" style="font-weight:600; color:var(--text-secondary);">File Curriculum Vitae (CV)</label>
                            <input type="file" name="cv" class="form-control" accept=".pdf">
                            @if($user->cv_path)
                                <div class="mt-2"><a href="{{ asset('storage/'.$user->cv_path) }}" target="_blank" class="badge bg-primary text-decoration-none"><i class="bi bi-download me-1"></i> Lihat CV</a></div>
                            @endif
                        </div>
                        <div class="col-md-12 mb-3">
                            <label class="form-label" style="font-weight:600; color:var(--text-secondary);">Sertifikat / Penghargaan Lainnya</label>
                            <input type="file" name="sertifikat" class="form-control" accept=".jpg,.jpeg,.png,.pdf">
                            @if($user->sertifikat_path)
                                <div class="mt-2"><a href="{{ asset('storage/'.$user->sertifikat_path) }}" target="_blank" class="badge bg-primary text-decoration-none"><i class="bi bi-download me-1"></i> Lihat Sertifikat</a></div>
                            @endif
                        </div>
                    </div>

                    <div class="text-end">
                        <button type="submit" id="submit-btn" class="btn btn-primary px-4" style="padding:10px 28px; font-weight:600;">
                            Simpan Perubahan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    let preLat = document.getElementById('lat').value;
    let preLng = document.getElementById('lng').value;

    let center = (preLat && preLng) ? [parseFloat(preLat), parseFloat(preLng)] : [-6.2088, 106.8456];
    let zoomLevel = (preLat && preLng) ? 17 : 13;

    let map = L.map('selfMap').setView(center, zoomLevel);
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; OpenStreetMap contributors'
    }).addTo(map);

    let marker = L.marker(center, { draggable: true }).addTo(map);

    // Update koordinat saat marker di-drag
    marker.on('dragend', function () {
        let pos = marker.getLatLng();
        updateCoordinates(pos.lat, pos.lng);
    });

    // Klik peta untuk pindah marker
    map.on('click', function (e) {
        marker.setLatLng(e.latlng);
        updateCoordinates(e.latlng.lat, e.latlng.lng);
    });

    // Geocoder (pencarian lokasi)
    L.Control.geocoder({
        defaultMarkGeocode: false,
        placeholder: "Cari alamat atau daerah..."
    }).on('markgeocode', function (e) {
        marker.setLatLng(e.geocode.center);
        map.setView(e.geocode.center, 17);
        updateCoordinates(e.geocode.center.lat, e.geocode.center.lng, e.geocode.name);
    }).addTo(map);

    // Fungsi update koordinat + alamat
    function updateCoordinates(lat, lng, address = null) {
        document.getElementById('lat').value = lat;
        document.getElementById('lng').value = lng;

        if (address) {
            document.getElementById('alamatDetailed').value = address;
        } else {
            // Reverse Geocoding
            fetch(`https://nominatim.openstreetmap.org/reverse?format=jsonv2&lat=${lat}&lon=${lng}`)
                .then(res => res.json())
                .then(data => {
                    if (data.display_name) {
                        document.getElementById('alamatDetailed').value = data.display_name;
                    }
                })
                .catch(() => {});
        }
    }

    // === GPS Button (Simple) ===
    document.getElementById('btn-gps-auto').addEventListener('click', function () {
        if (!navigator.geolocation) {
            alert('Browser HP Anda tidak mendukung GPS. Silakan geser peta secara manual.');
            return;
        }

        const btn = this;
        const originalText = btn.innerHTML;
        btn.disabled = true;
        btn.innerHTML = `<span class="spinner-border spinner-border-sm"></span> Sedang Melacak...`;

        navigator.geolocation.getCurrentPosition(
            (position) => {
                const { latitude, longitude } = position.coords;
                marker.setLatLng([latitude, longitude]);
                map.setView([latitude, longitude], 18);
                updateCoordinates(latitude, longitude);
                btn.innerHTML = '✅ LOKASI BERHASIL DITEMUKAN';
                setTimeout(() => {
                    btn.innerHTML = originalText;
                    btn.disabled = false;
                }, 3000);
            },
            (error) => {
                alert('Gagal mendapatkan lokasi. Pastikan izin GPS / Lokasi di HP Anda sudah dinyalakan.');
                btn.innerHTML = originalText;
                btn.disabled = false;
            },
            { enableHighAccuracy: true, timeout: 10000 }
        );
    });

    // Reset Map
    document.getElementById('btn-reset-map').addEventListener('click', function () {
        if (preLat && preLng) {
            marker.setLatLng([parseFloat(preLat), parseFloat(preLng)]);
            map.setView([parseFloat(preLat), parseFloat(preLng)], 17);
        } else {
            marker.setLatLng([-6.2088, 106.8456]);
            map.setView([-6.2088, 106.8456], 13);
        }
    });

    // Form Submit
    document.getElementById('profile-form').addEventListener('submit', function (e) {
        // Hapus validasi JS yang memblokir, biarkan saja tersimpan (latitude/longitude sudah nullable di controller)
        const submitBtn = document.getElementById('submit-btn');
        submitBtn.disabled = true;
        submitBtn.innerHTML = 'Menyimpan...';
    });
});
</script>
@endsection