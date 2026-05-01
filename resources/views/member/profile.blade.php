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
                <div class="alert alert-success" style="background: rgba(34, 197, 94, 0.1); color: #22c55e; border: 1px solid rgba(34, 197, 94, 0.2); padding: 1rem; border-radius: 8px;">
                    {{ session('success') }}
                </div>
            @endif
            @if($errors->any())
                <div class="alert alert-danger" style="background: rgba(239, 68, 68, 0.1); color: #ef4444; border: 1px solid rgba(239, 68, 68, 0.2); padding: 1rem; border-radius: 8px;">
                    <ul style="margin: 0;">
                        @foreach($errors->all() as $error) <li>{{ $error }}</li> @endforeach
                    </ul>
                </div>
            @endif

            <div class="card p-4" style="background: var(--surface-color); border: 1px solid var(--border-color); border-radius: 12px; color: var(--text-color);">
                <form action="{{ route('member.profile.update') }}" method="POST">
                    @csrf
                    <input type="hidden" name="latitude" id="lat" value="{{ $user->latitude }}">
                    <input type="hidden" name="longitude" id="lng" value="{{ $user->longitude }}">

                    <div class="row mb-3">
                        <div class="col-md-6 mb-3 mb-md-0">
                            <label class="form-label" style="color: var(--text-secondary);">Nama Lengkap</label>
                            <input type="text" class="form-control" value="{{ $user->name }}" readonly style="background: rgba(0,0,0,0.2); border: 1px solid var(--border-color); color: var(--text-color);">
                            <small class="text-muted d-block mt-1" style="font-size: 11px;">(Hubungi Admin untuk ubah nama)</small>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label" style="color: var(--text-secondary);">Email Akses</label>
                            <div class="d-flex align-items-center gap-2">
                                <input type="email" class="form-control" value="{{ $user->email }}" readonly style="background: rgba(0,0,0,0.2); border: 1px solid var(--border-color); color: var(--text-color); flex: 1;">
                                @if($user->google_id)
                                    <span class="badge bg-success d-flex align-items-center gap-1" style="height: 38px; padding: 0 12px; border-radius: 6px; font-weight: 500; font-size: 12px;">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path><polyline points="22 4 12 14.01 9 11.01"></polyline></svg>
                                        Google
                                    </span>
                                @else
                                    <a href="{{ route('auth.google') }}" class="btn btn-outline-light d-flex align-items-center gap-2" style="height: 38px; border-color: var(--border-color); font-size: 12px; white-space: nowrap;">
                                        <svg width="16" height="16" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z" fill="#4285F4"/><path d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z" fill="#34A853"/><path d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l3.66-2.84z" fill="#FBBC05"/><path d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 12-4.53z" fill="#EA4335"/></svg>
                                        Tautkan
                                    </a>
                                @endif
                            </div>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6 mb-3 mb-md-0">
                            <label class="form-label" style="color: var(--text-secondary);">Tanggal Lahir</label>
                            <input type="date" name="tanggal_lahir" class="form-control" required value="{{ $user->tanggal_lahir }}" style="background: transparent; border: 1px solid var(--border-color); color: var(--text-color); color-scheme: dark;">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label" style="color: var(--text-secondary);">Ganti Password (Opsional)</label>
                            <input type="password" name="password" class="form-control" placeholder="Kosongkan jika tak diubah" style="background: transparent; border: 1px solid var(--border-color); color: var(--text-color);">
                        </div>
                    </div>

                    <div class="mb-4">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <label class="form-label mb-0" style="color: var(--text-secondary);">Alamat Domisili & Titik Koordinat Peta</label>
                            <button type="button" id="btn-location" class="btn btn-sm btn-outline-primary d-flex align-items-center gap-1" style="font-size: 11px; border-radius: 20px;">
                                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path><circle cx="12" cy="10" r="3"></circle></svg>
                                Gunakan Lokasi Saya (GPS)
                            </button>
                        </div>
                        
                        <!-- Google Maps Link Parser -->
                        <div class="mb-3 p-3" style="background: rgba(0,0,0,0.2); border: 1px solid var(--border-color); border-radius: 8px;">
                            <label class="form-label" style="font-size: 13px; color: var(--primary); font-weight: 600;">
                                Lokasi Sulit Ditemukan? Ekstrak dari Google Maps (Gratis & Akurat)
                            </label>
                            <div class="d-flex gap-2">
                                <input type="text" id="gmapsLink" class="form-control" placeholder="Paste Link Google Maps (Buka Google Maps > Cari Rumah > Klik Bagikan > Salin Link)" style="font-size: 13px; background: transparent; border: 1px solid var(--border-color); color: var(--text-color);">
                                <button type="button" id="btn-parse-gmaps" class="btn btn-primary" style="white-space: nowrap; font-size: 13px;">Tarik Lokasi</button>
                            </div>
                            <small class="d-block mt-2" style="color: var(--text-secondary); font-size: 11px;">
                                Tempel URL `https://maps.app.goo.gl/...` di atas untuk otomatis memindahkan jarum merah ke lokasi rumah Anda tanpa harus mencari ulang di peta.
                            </small>
                        </div>

                        <div id="selfMap" class="mb-3" style="height: 350px; width: 100%; border-radius: 8px; border: 1px solid var(--border-color); z-index: 1;"></div>
                        
                        <textarea name="alamat" id="alamatDetailed" class="form-control mt-3" rows="2" required placeholder="Detail Alamat (Jalan Mawar No 10...)" style="background: transparent; border: 1px solid var(--border-color); color: var(--text-color);">{{ $user->alamat }}</textarea>
                        
                        <div class="mt-2 p-2" style="background: rgba(var(--primary-rgb), 0.05); border-radius: 6px; border-left: 3px solid var(--primary);">
                            <small class="d-block" style="color: var(--text-color); font-size: 12px;">
                                <strong>Tips:</strong> Tarik otomatis dari Google Maps menggunakan link (cara termudah), klik tombol GPS ponsel, atau geser jarum merah secara manual di atas atap rumah Anda pada peta.
                            </small>
                        </div>
                    </div>

                    <div class="text-end">
                        <button type="submit" class="btn btn-primary" style="padding: 10px 24px;">Simpan Profil Saya</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    let preLat = document.getElementById('lat').value;
    let preLng = document.getElementById('lng').value;
    
    let centerOptions = preLat && preLng ? [parseFloat(preLat), parseFloat(preLng)] : [-6.2088, 106.8456];
    
    let map = L.map('selfMap').setView(centerOptions, 14);
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; OpenStreetMap'
    }).addTo(map);

    let marker = L.marker(centerOptions, {draggable: true}).addTo(map);
    setupMarkerEvents(marker);

    // Geocoder Search
    let geocoder = L.Control.geocoder({
        defaultMarkGeocode: false,
        placeholder: "Cari lokasi rumah / daerah..."
    }).on('markgeocode', function(e) {
        updatePosition(e.geocode.center.lat, e.geocode.center.lng, e.geocode.name, 17);
    }).addTo(map);

    // Click Map to move marker
    map.on('click', function(e) {
        updatePosition(e.latlng.lat, e.latlng.lng);
    });

    // GPS Button
    document.getElementById('btn-location').addEventListener('click', function() {
        if (!navigator.geolocation) {
            alert('Browser Anda tidak mendukung deteksi lokasi.');
            return;
        }

        this.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Mencari...';
        this.disabled = true;

        navigator.geolocation.getCurrentPosition(
            (position) => {
                const { latitude, longitude } = position.coords;
                updatePosition(latitude, longitude, null, 18);
                this.innerHTML = '<svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path><circle cx="12" cy="10" r="3"></circle></svg> Lokasi Ditemukan!';
                this.className = 'btn btn-sm btn-success d-flex align-items-center gap-1';
                setTimeout(() => {
                    this.disabled = false;
                    this.innerHTML = '<svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path><circle cx="12" cy="10" r="3"></circle></svg> Gunakan Lokasi Saya (GPS)';
                    this.className = 'btn btn-sm btn-outline-primary d-flex align-items-center gap-1';
                }, 3000);
            },
            (error) => {
                alert('Gagal mendapatkan lokasi: ' + error.message);
                this.disabled = false;
                this.innerHTML = '<svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path><circle cx="12" cy="10" r="3"></circle></svg> Gunakan Lokasi Saya (GPS)';
            },
            { enableHighAccuracy: true, timeout: 5000, maximumAge: 0 }
        );
    });

    // Google Maps Link Parser
    document.getElementById('btn-parse-gmaps').addEventListener('click', function() {
        let url = document.getElementById('gmapsLink').value.trim();
        if (!url) {
            alert('Silakan tempel (paste) link Google Maps terlebih dahulu.');
            return;
        }

        let btn = this;
        let originalText = btn.innerHTML;
        btn.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Mengekstrak...';
        btn.disabled = true;

        fetch('/api/parse-gmaps', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ url: url })
        })
        .then(response => response.json())
        .then(data => {
            btn.innerHTML = originalText;
            btn.disabled = false;
            if (data.success) {
                updatePosition(data.lat, data.lng, null, 18);
                alert('Berhasil menarik lokasi dari Google Maps!');
            } else {
                alert(data.message || 'Gagal mengekstrak koordinat dari link tersebut. Pastikan link valid dari aplikasi Google Maps.');
            }
        })
        .catch(error => {
            btn.innerHTML = originalText;
            btn.disabled = false;
            alert('Terjadi kesalahan koneksi saat mengekstrak link.');
        });
    });

    function updatePosition(lat, lng, address = null, zoom = null) {
        marker.setLatLng([lat, lng]);
        if (zoom) map.setView([lat, lng], zoom);
        
        document.getElementById('lat').value = lat;
        document.getElementById('lng').value = lng;

        if (address) {
            document.getElementById('alamatDetailed').value = address;
        } else {
            // Reverse Geocoding
            fetch(`https://nominatim.openstreetmap.org/reverse?format=jsonv2&lat=${lat}&lon=${lng}`)
                .then(response => response.json())
                .then(data => {
                    if (data.display_name) {
                        document.getElementById('alamatDetailed').value = data.display_name;
                    }
                });
        }
    }

    function setupMarkerEvents(m) {
        m.on('dragend', function(e) {
            let position = m.getLatLng();
            updatePosition(position.lat, position.lng);
        });
    }
});
</script>
@endsection
