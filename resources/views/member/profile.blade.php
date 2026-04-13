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
                            <input type="email" class="form-control" value="{{ $user->email }}" readonly style="background: rgba(0,0,0,0.2); border: 1px solid var(--border-color); color: var(--text-color);">
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
                        <label class="form-label" style="color: var(--text-secondary);">Alamat Domisili & Titik Koordinat Peta</label>
                        
                        <div id="selfMap" class="mb-3" style="height: 350px; width: 100%; border-radius: 8px; border: 1px solid var(--border-color); z-index: 1;"></div>
                        
                        <textarea name="alamat" id="alamatDetailed" class="form-control mt-3" rows="2" required placeholder="Detail Alamat (Jalan Mawar No 10...)" style="background: transparent; border: 1px solid var(--border-color); color: var(--text-color);">{{ $user->alamat }}</textarea>
                        
                        <small class="d-block mt-2" style="color: var(--primary);">Tips: Gunakan 🔍 ikon kaca pembesar di pojok kanan peta untuk mencari kecamatan/kota Anda. Anda bisa menggeser pin merah untuk mendapatkan akurasi yang lebih pas.</small>
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
    
    // Default Jakarta center jika tidak ada koordinat sama sekali
    let centerOptions = preLat && preLng ? [parseFloat(preLat), parseFloat(preLng)] : [-6.2088, 106.8456];
    
    // Inisialisasi Peta Leaflet
    let map = L.map('selfMap').setView(centerOptions, 14);
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; OpenStreetMap'
    }).addTo(map);

    let marker = null;
    
    // Setel Marker Awal
    if (preLat && preLng) {
        marker = L.marker(centerOptions, {draggable: true}).addTo(map);
        setupMarkerEvents(marker);
    } else {
        // Jika masih kosong (pengguna baru), buat marker saat diklik saja dulu
        marker = L.marker(centerOptions, {draggable: true}).addTo(map);
        setupMarkerEvents(marker);
    }

    // Tambahkan Fitur Pencarian (Geocoder) secara Gratis via OSM/Nominatim
    let geocoder = L.Control.geocoder({
        defaultMarkGeocode: false,
        placeholder: "Cari lokasi rumah / daerah..."
    }).on('markgeocode', function(e) {
        let latlng = e.geocode.center;
        
        // Pindahkan Peta ke Hasil Pencarian
        map.setView(latlng, 17);
        
        // Pindahkan Marker
        marker.setLatLng(latlng);
        document.getElementById('lat').value = latlng.lat;
        document.getElementById('lng').value = latlng.lng;
        
        // Coba isi otomatis alamat dengan hasil geocoder
        document.getElementById('alamatDetailed').value = e.geocode.name;
    }).addTo(map);

    // Kalau Peta di-klik, pindahkan Pin Map ke situ
    map.on('click', function(e) {
        let lat = e.latlng.lat;
        let lng = e.latlng.lng;
        
        marker.setLatLng([lat, lng]);
        
        document.getElementById('lat').value = lat;
        document.getElementById('lng').value = lng;
    });
    
    // Fungsi agar saat marker di drag (geser) datanya selalu ter-update
    function setupMarkerEvents(m) {
        m.on('dragend', function(e) {
            let position = m.getLatLng();
            document.getElementById('lat').value = position.lat;
            document.getElementById('lng').value = position.lng;
        });
    }
});
</script>
@endsection
