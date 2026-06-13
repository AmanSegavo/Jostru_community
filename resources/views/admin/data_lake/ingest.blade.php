@extends('layouts.admin')

@section('title', 'Ingest Data Mentah - Data Lake')

@section('admin_content')
<!-- Leaflet CSS for Maps -->
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin=""/>
<!-- Leaflet JS -->
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>

<div class="container mt-4 animate-fade-in">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="mb-0" style="font-weight: 800; color: var(--text-color);">Ingest Data Mentah (RAW)</h2>
            <p class="text-muted mb-0">Pusat pengumpulan data terstruktur, semi-terstruktur, dan file media (tidak terstruktur).</p>
        </div>
        <a href="{{ route('admin.data_lake.index') }}" class="btn btn-outline-secondary" style="border-radius: 12px;">Kembali ke Data Lake</a>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach($errors->all() as $err)
                    <li>{{ $err }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="card p-4 glass border-0" style="border-radius: 16px;">
        <form action="{{ route('admin.data_lake.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            
            <div class="row mb-4">
                <div class="col-md-6">
                    <label class="form-label fw-bold">Kategori Data <span class="text-danger">*</span></label>
                    <select name="category" class="form-select" id="categorySelect" required>
                        <option value="">Pilih Kategori...</option>
                        <option value="MAPS_LOCATION">Data Perusahaan / Lokasi Maps</option>
                        <option value="MEDIA_DUMP">Media (Foto / Video) Unstructured</option>
                        <option value="SURVEY">Hasil Survei Lapangan</option>
                        <option value="CUSTOM_JSON">Custom Data (Semi-Structured)</option>
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-bold">Kaitkan ke Divisi (Opsional)</label>
                    <select name="division_id" class="form-select">
                        <option value="">-- Global (Tanpa Divisi) --</option>
                        @foreach($divisions as $div)
                            <option value="{{ $div->id }}">{{ $div->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <hr class="mb-4">

            <!-- Section Khusus Maps/Location -->
            <div id="mapsSection" class="mb-4" style="display: none; background: rgba(59,130,246,0.05); padding: 1.5rem; border-radius: 12px; border-left: 4px solid #3b82f6;">
                <h5 class="fw-bold mb-3">📍 Data Lokasi (Maps Scrape / Manual)</h5>
                
                <div class="mb-3">
                    <label class="form-label fw-bold">Paste Link Google Maps (Opsional)</label>
                    <div class="input-group">
                        <input type="text" id="gmapsLink" class="form-control" placeholder="https://www.google.com/maps/place/...">
                        <button type="button" class="btn btn-outline-primary fw-bold" onclick="extractFromLink()">Ekstrak Koordinat</button>
                    </div>
                    <small class="text-muted">Tempelkan link dari Google Maps, sistem akan otomatis mengisi titik kordinat.</small>
                </div>

                <div class="row">
                    <div class="col-md-12 mb-3">
                        <label class="form-label">Nama Perusahaan / Tempat</label>
                        <input type="text" name="company_name" class="form-control" placeholder="Cth: PT. Jostru Jaya">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Latitude</label>
                        <input type="text" name="latitude" id="latInput" class="form-control" placeholder="-6.200000">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Longitude</label>
                        <input type="text" name="longitude" id="lngInput" class="form-control" placeholder="106.816666">
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-bold">Atau Pilih Titik Peta Secara Langsung</label>
                    <div id="ingestMapSelector" style="height: 300px; border-radius: 12px; border: 1px solid #ccc; z-index:1;"></div>
                    <small class="text-muted"><i class="bi bi-info-circle"></i> Klik pada peta untuk memilih lokasi. Pin biru adalah lokasi yang sudah ada (untuk mencegah duplikat).</small>
                </div>
            </div>

            <!-- Section Semi-Structured / JSON -->
            <div class="mb-4">
                <label class="form-label fw-bold">Payload Data (JSON / Semi-Structured)</label>
                <div class="alert alert-info py-2 small mb-2">Anda bisa memasukkan string JSON mentah. Kolom-kolom akan diekstrak otomatis.</div>
                <textarea name="payload" class="form-control font-monospace" rows="6" placeholder='{
  "omzet_tahunan": "1M",
  "jumlah_karyawan": 50,
  "catatan_khusus": "Potensial untuk diajak kerjasama"
}'></textarea>
            </div>

            <!-- Section Unstructured / Media -->
            <div class="mb-4">
                <label class="form-label fw-bold">Upload Unstructured Data (Foto, Video, Dokumen)</label>
                <div class="alert alert-warning py-2 small mb-2"><i class="bi bi-exclamation-triangle"></i> Data berformat media, teks dokumen, gambar bukti, atau video survei dapat diunggah di sini. Max 20MB per file.</div>
                <input type="file" name="files[]" class="form-control" multiple accept="image/*,video/*,application/pdf,.doc,.docx,.xls,.xlsx">
            </div>

            <div class="text-end mt-4">
                <button type="submit" class="btn btn-primary btn-lg fw-bold" style="border-radius: 12px; padding: 12px 30px;">
                    <i class="bi bi-cloud-arrow-up"></i> Ingest Data Mentah
                </button>
            </div>
        </form>
    </div>
<style>
/* Animasi pin Leaflet */
.custom-pin span {
    transition: transform 0.2s;
}
.custom-pin:hover span {
    transform: rotate(45deg) scale(1.1);
}
</style>

<script>
    let mapInitialized = false;
    let ingestMap = null;
    let selectedMarker = null;

    function initIngestMap() {
        if(mapInitialized) return;
        
        // Initialize Map
        ingestMap = L.map('ingestMapSelector').setView([-2.5489, 118.0149], 5);
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 19,
            attribution: '© OpenStreetMap'
        }).addTo(ingestMap);

        const coloredIcon = (color) => L.divIcon({
            className: "custom-pin",
            iconAnchor: [0, 24],
            labelAnchor: [-6, 0],
            popupAnchor: [0, -36],
            html: `<span style="background-color: ${color || 'gray'};
                width: 1.5rem;
                height: 1.5rem;
                display: block;
                left: -0.75rem;
                top: -1.5rem;
                position: relative;
                border-radius: 3rem 3rem 0;
                transform: rotate(45deg);
                border: 2px solid #FFFFFF;
                box-shadow: 0px 4px 6px rgba(0,0,0,0.3)"></span>`
        });

        // Load existing markers
        var existingMarkers = @json($mapMarkers ?? []);
        existingMarkers.forEach(function(m) {
            if(m.lat && m.lng) {
                L.marker([m.lat, m.lng], {icon: coloredIcon(m.color), opacity: 0.7}).addTo(ingestMap)
                 .bindTooltip(m.title + " (" + m.type + ")");
            }
        });

        // Click event to place new pin
        ingestMap.on('click', function(e) {
            var lat = e.latlng.lat;
            var lng = e.latlng.lng;
            
            document.getElementById('latInput').value = lat.toFixed(6);
            document.getElementById('lngInput').value = lng.toFixed(6);

            if(selectedMarker) {
                ingestMap.removeLayer(selectedMarker);
            }
            
            // Icon merah untuk pin baru
            selectedMarker = L.marker([lat, lng], {icon: coloredIcon('#ec4899')}).addTo(ingestMap)
                              .bindPopup("<b>Titik Baru</b>").openPopup();
        });

        mapInitialized = true;
        
        // Fix rendering issue inside hidden div
        setTimeout(function() {
            ingestMap.invalidateSize();
        }, 300);
    }

    document.getElementById('categorySelect').addEventListener('change', function() {
        if(this.value === 'MAPS_LOCATION') {
            document.getElementById('mapsSection').style.display = 'block';
            initIngestMap();
        } else {
            document.getElementById('mapsSection').style.display = 'none';
        }
    });

    function extractFromLink() {
        var link = document.getElementById('gmapsLink').value;
        if(!link) return alert('Silakan tempel link Google Maps terlebih dahulu.');
        
        // Coba regex /@lat,lng/
        var regex = /@(-?\d+\.\d+),(-?\d+\.\d+)/;
        var match = link.match(regex);
        
        if(!match) {
            // Coba regex query=lat,lng
            var regex2 = /query=(-?\d+\.\d+),(-?\d+\.\d+)/;
            match = link.match(regex2);
        }
        
        if(!match) {
            // Coba regex ll=lat,lng
            var regex3 = /ll=(-?\d+\.\d+),(-?\d+\.\d+)/;
            match = link.match(regex3);
        }

        if(match && match.length >= 3) {
            var lat = parseFloat(match[1]);
            var lng = parseFloat(match[2]);
            
            document.getElementById('latInput').value = lat;
            document.getElementById('lngInput').value = lng;
            
            if(ingestMap) {
                if(selectedMarker) {
                    ingestMap.removeLayer(selectedMarker);
                }
                selectedMarker = L.marker([lat, lng]).addTo(ingestMap)
                                  .bindPopup("<b>Titik Baru (Dari Link)</b>").openPopup();
                ingestMap.setView([lat, lng], 15);
            }
            alert('Berhasil mengekstrak kordinat!');
        } else {
            alert('Tidak dapat mendeteksi koordinat dari link tersebut. Pastikan format link benar (mengandung /@lat,lng atau query=lat,lng).');
        }
    }
</script>
@endsection
