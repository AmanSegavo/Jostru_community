@extends('layouts.admin')

@section('title', 'Data Lake Command Center')

@section('admin_content')
<!-- Leaflet CSS for Maps -->
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin=""/>
<!-- Leaflet JS -->
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>

<div class="container-fluid mt-4 animate-fade-in" style="max-width: 1600px;">
    <!-- Header & Filter -->
    <div class="row align-items-center mb-4">
        <div class="col-md-6">
            <h2 class="mb-0" style="font-weight: 900; color: var(--text-color); letter-spacing: -1px;">DATA LAKE <span class="text-primary">INTELLIGENCE</span></h2>
            <p class="text-muted mb-0">Pusat Analitik Big Data Dinamis (Structured, Unstructured, Spatial Data)</p>
        </div>
        <div class="col-md-6 text-md-end mt-3 mt-md-0">
            <form action="{{ route('admin.data_lake.index') }}" method="GET" class="d-flex flex-wrap flex-md-nowrap gap-2 justify-content-md-end">
                <input type="text" name="search" class="form-control bg-light border-0" placeholder="🔍 Cari..." value="{{ request('search') }}" style="border-radius: 12px; width: 200px;">
                <input type="hidden" name="status" value="{{ $statusFilter }}">
                <select name="division_id" class="form-select w-auto fw-bold" style="border-radius: 12px; background: rgba(59,130,246,0.1); border-color: rgba(59,130,246,0.2); color: #3b82f6;" onchange="this.form.submit()">
                    <option value="">🌍 GLOBAL (Semua Divisi)</option>
                    @foreach($divisions as $div)
                        <option value="{{ $div->id }}" {{ $selectedDivisionId == $div->id ? 'selected' : '' }}>🏢 {{ $div->name }}</option>
                    @endforeach
                </select>
                <button type="submit" class="btn btn-outline-primary" style="border-radius: 12px;"><i class="bi bi-search"></i></button>
                <a href="{{ route('admin.data_lake.ingest') }}" class="btn btn-primary fw-bold" style="border-radius: 12px;"><i class="bi bi-cloud-arrow-up"></i> Ingest Data Baru</a>
            </form>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <!-- KPI Tiles -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card glass p-3 border-0 h-100" style="border-radius: 16px; border-left: 4px solid #ef4444 !important;">
                <div class="text-muted fw-bold small text-uppercase mb-1">Total Data Mentah (RAW)</div>
                <h3 class="fw-black mb-0 text-danger">{{ number_format($stats['total_raw']) }} <span class="fs-6 fw-normal text-muted">Records</span></h3>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card glass p-3 border-0 h-100" style="border-radius: 16px; border-left: 4px solid #10b981 !important;">
                <div class="text-muted fw-bold small text-uppercase mb-1">Data Diproses (CLEAN)</div>
                <h3 class="fw-black mb-0 text-success">{{ number_format($stats['total_processed']) }} <span class="fs-6 fw-normal text-muted">Records</span></h3>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card glass p-3 border-0 h-100" style="border-radius: 16px; border-left: 4px solid #3b82f6 !important;">
                <div class="text-muted fw-bold small text-uppercase mb-1">Titik Lokasi (Maps)</div>
                <h3 class="fw-black mb-0 text-primary">{{ count($mapMarkers) }} <span class="fs-6 fw-normal text-muted">Locations</span></h3>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card p-3 border-0 h-100 text-white" style="border-radius: 16px; background: linear-gradient(135deg, #1e293b, #0f172a);">
                <div class="fw-bold small text-uppercase mb-1" style="color: #94a3b8;">Status Data Engine</div>
                <h4 class="fw-black mb-0 text-info"><i class="bi bi-activity"></i> ACTIVE</h4>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Interactive Map Panel -->
        <div class="col-lg-12 mb-4">
            <div class="card p-0 glass border-0 overflow-hidden" style="border-radius: 20px;">
                <div class="card-header bg-transparent border-0 p-3 pb-0">
                    <h5 class="fw-bold mb-0" style="color: var(--text-color);"><i class="bi bi-geo-alt-fill text-danger"></i> Spatial Intelligence (Peta Sebaran)</h5>
                </div>
                <div class="card-body p-3">
                    <div id="dataLakeMap" style="height: 400px; border-radius: 16px; z-index: 1;"></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Data Lake Pipeline Toggle -->
    <div class="d-flex mb-3 gap-2">
        <a href="{{ route('admin.data_lake.index', ['status' => 'RAW', 'division_id' => $selectedDivisionId]) }}" class="btn {{ $statusFilter == 'RAW' ? 'btn-danger' : 'btn-outline-secondary' }} fw-bold" style="border-radius: 12px; padding: 10px 20px;">
            <i class="bi bi-funnel"></i> Data Mentah (RAW)
        </a>
        <a href="{{ route('admin.data_lake.index', ['status' => 'PROCESSED', 'division_id' => $selectedDivisionId]) }}" class="btn {{ $statusFilter == 'PROCESSED' ? 'btn-success' : 'btn-outline-secondary' }} fw-bold" style="border-radius: 12px; padding: 10px 20px;">
            <i class="bi bi-check-circle"></i> Sudah Diproses
        </a>
        <a href="{{ route('admin.data_lake.index', ['status' => 'ALL', 'division_id' => $selectedDivisionId]) }}" class="btn {{ $statusFilter == 'ALL' ? 'btn-primary' : 'btn-outline-secondary' }} fw-bold" style="border-radius: 12px; padding: 10px 20px;">
            <i class="bi bi-database"></i> Tampilkan Semua
        </a>
    </div>

    <!-- Dynamic Schema-less Table -->
    <div class="card p-0 glass border-0 overflow-hidden mb-4" style="border-radius: 20px;">
        <div class="table-responsive">
            <table class="table table-hover mb-0 align-middle">
                <thead style="background: rgba(var(--primary-rgb), 0.05);">
                    <tr>
                        <th class="px-4 py-3 border-0">Timestamp</th>
                        <th class="px-4 py-3 border-0">Kategori</th>
                        <th class="px-4 py-3 border-0">Divisi</th>
                        <th class="px-4 py-3 border-0" style="width: 40%;">Dynamic Payload (JSON / Semi-Structured)</th>
                        <th class="px-4 py-3 border-0">Unstructured Media</th>
                        <th class="px-4 py-3 border-0 text-center">Status / Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($records as $rec)
                        @php 
                            $payloadArr = is_string($rec->payload) ? json_decode($rec->payload, true) : $rec->payload; 
                            $mediaArr = is_string($rec->media_paths) ? json_decode($rec->media_paths, true) : $rec->media_paths;
                        @endphp
                        <tr>
                            <td class="px-4 py-3 text-muted small">{{ $rec->created_at->format('d M Y H:i') }}</td>
                            <td class="px-4 py-3 fw-bold text-primary">{{ $rec->category }}</td>
                            <td class="px-4 py-3">{{ $rec->division->name ?? 'Global' }}</td>
                            <td class="px-4 py-3">
                                @if(is_array($payloadArr) && count($payloadArr) > 0)
                                    <div class="d-flex flex-wrap gap-1">
                                        @foreach($payloadArr as $key => $val)
                                            <span class="badge bg-light text-dark border"><span class="text-muted">{{ $key }}:</span> {{ Str::limit(is_array($val) ? json_encode($val) : $val, 30) }}</span>
                                        @endforeach
                                    </div>
                                @else
                                    <span class="text-muted italic">-</span>
                                @endif
                            </td>
                            <td class="px-4 py-3">
                                @if(is_array($mediaArr) && count($mediaArr) > 0)
                                    <span class="badge bg-info text-dark"><i class="bi bi-files"></i> {{ count($mediaArr) }} Files</span>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-center">
                                <div class="d-flex gap-2 justify-content-center">
                                    @if($rec->status === 'RAW')
                                        <span class="badge bg-danger">RAW</span>
                                        <form action="{{ route('admin.data_lake.process', $rec->id) }}" method="POST">
                                            @csrf
                                            <button class="btn btn-sm btn-success" style="border-radius:6px;" title="Tandai Sudah Diproses"><i class="bi bi-check-lg"></i></button>
                                        </form>
                                    @else
                                        <span class="badge bg-success">PROCESSED</span>
                                    @endif
                                    
                                    @php
                                        $signedUrl = \Illuminate\Support\Facades\URL::temporarySignedRoute('shared.report.datalake', now()->addDays(7), ['id' => $rec->id]);
                                    @endphp
                                    <button class="btn btn-sm btn-outline-success" style="border-radius:6px;" title="Bagikan ke WhatsApp" onclick="shareTableDataToWA('{{ rawurlencode(is_string($rec->payload) ? $rec->payload : json_encode($rec->payload)) }}', '{{ $rec->category }}', '{{ $rec->division->name ?? 'Global' }}', '{{ $rec->status }}', '{{ $rec->created_at->format('d M Y H:i') }}', '{!! $signedUrl !!}')">
                                        <i class="bi bi-whatsapp"></i>
                                    </button>
                                    
                                    <form action="{{ route('admin.data_lake.destroy', $rec->id) }}" method="POST" onsubmit="return confirm('Hapus permanen record ini beserta filenya?')">
                                        @csrf
                                        @method('DELETE')
                                        <button class="btn btn-sm btn-outline-danger" style="border-radius:6px;" title="Hapus"><i class="bi bi-trash"></i></button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-5 text-muted">Belum ada data di Data Lake untuk filter ini.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($records->hasPages())
            <div class="card-footer bg-transparent border-0 p-4">
                {{ $records->links() }}
            </div>
        @endif
    </div>

    <!-- Structured Data (Operational Data Sync) -->
    <div class="card p-0 glass border-0 overflow-hidden mb-4" style="border-radius: 20px;">
        <div class="card-header bg-transparent border-0 p-4 pb-0">
            <h5 class="fw-bold mb-0" style="color: var(--text-color);"><i class="bi bi-diagram-3 text-primary"></i> Live Operational Data (Terstruktur)</h5>
            <p class="text-muted small">Data transaksi, RAB, dan produksi tersinkronisasi otomatis dari modul ERP.</p>
        </div>
        <div class="card-body p-4 pt-2">
            <ul class="nav nav-pills mb-3 gap-2" id="structuredTabs" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active rounded-pill fw-bold" data-bs-toggle="pill" data-bs-target="#dl-finances" type="button">Keuangan ({{ count($structuredData['finances']) }})</button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link rounded-pill fw-bold" data-bs-toggle="pill" data-bs-target="#dl-rabs" type="button">RAB & Anggaran ({{ count($structuredData['rabs']) }})</button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link rounded-pill fw-bold" data-bs-toggle="pill" data-bs-target="#dl-productions" type="button">Produksi & Panen ({{ count($structuredData['productions']) }})</button>
                </li>
            </ul>

            <div class="tab-content" id="structuredTabsContent">
                <!-- Finances Tab -->
                <div class="tab-pane fade show active" id="dl-finances">
                    <div class="table-responsive">
                        <table class="table table-sm table-hover align-middle">
                            <thead class="table-light">
                                <tr><th>Tanggal</th><th>Divisi</th><th>Jenis</th><th>Uraian</th><th>Nominal</th></tr>
                            </thead>
                            <tbody>
                                @forelse($structuredData['finances'] as $f)
                                <tr>
                                    <td>{{ \Carbon\Carbon::parse($f->transaction_date)->format('d/m/Y') }}</td>
                                    <td>{{ $f->division->name ?? 'Kas Pusat' }}</td>
                                    <td>
                                        @if($f->type == 'PEMASUKAN') <span class="badge bg-success">Masuk</span>
                                        @else <span class="badge bg-danger">Keluar</span> @endif
                                    </td>
                                    <td>{{ $f->description }}</td>
                                    <td class="fw-bold text-{{ $f->type == 'PEMASUKAN' ? 'success' : 'danger' }}">Rp {{ number_format($f->amount, 0, ',', '.') }}</td>
                                </tr>
                                @empty
                                <tr><td colspan="5" class="text-center py-3 text-muted">Belum ada data keuangan.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- RABs Tab -->
                <div class="tab-pane fade" id="dl-rabs">
                    <div class="table-responsive">
                        <table class="table table-sm table-hover align-middle">
                            <thead class="table-light">
                                <tr><th>Tanggal</th><th>Divisi</th><th>Judul RAB</th><th>Total Anggaran</th><th>Status</th></tr>
                            </thead>
                            <tbody>
                                @forelse($structuredData['rabs'] as $r)
                                <tr>
                                    <td>{{ $r->created_at->format('d/m/Y') }}</td>
                                    <td>{{ $r->division->name ?? 'Global' }}</td>
                                    <td>{{ $r->title }}</td>
                                    <td class="fw-bold">Rp {{ number_format($r->total_budget, 0, ',', '.') }}</td>
                                    <td><span class="badge bg-primary">{{ $r->status }}</span></td>
                                </tr>
                                @empty
                                <tr><td colspan="5" class="text-center py-3 text-muted">Belum ada data RAB.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Productions Tab -->
                <div class="tab-pane fade" id="dl-productions">
                    <div class="table-responsive">
                        <table class="table table-sm table-hover align-middle">
                            <thead class="table-light">
                                <tr><th>Tanggal Produksi</th><th>Divisi</th><th>SKU / Produk</th><th>Kuantitas</th></tr>
                            </thead>
                            <tbody>
                                @forelse($structuredData['productions'] as $p)
                                <tr>
                                    <td>{{ \Carbon\Carbon::parse($p->produced_at)->format('d/m/Y') }}</td>
                                    <td>{{ $p->division->name ?? 'Global' }}</td>
                                    <td>{{ $p->product_sku }}</td>
                                    <td class="fw-bold">{{ $p->quantity_produced }}</td>
                                </tr>
                                @empty
                                <tr><td colspan="4" class="text-center py-3 text-muted">Belum ada batch produksi.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>

<!-- Modal Detail Marker -->
<div class="modal fade" id="markerDetailModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-lg">
    <div class="modal-content" style="border-radius: 20px; border: none; box-shadow: 0 25px 50px -12px rgba(0,0,0,0.5);">
      <div class="modal-header border-0 pb-0">
        <h5 class="modal-title fw-bold" id="modalTitle">Detail Data</h5>
        <div class="d-flex gap-2">
            <button type="button" class="btn btn-sm btn-success fw-bold" id="waShareBtn" style="border-radius: 8px;" onclick="shareMarkerDataToWA()">
                <i class="bi bi-whatsapp"></i> Bagikan
            </button>
            <button type="button" class="btn-close mt-1" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
      </div>
      <div class="modal-body pt-2">
        <div class="d-flex align-items-center mb-3">
            <span id="modalTypeBadge" class="badge rounded-pill me-2"></span>
            <small id="modalDiv" class="text-muted"></small>
        </div>
        
        <h6 class="fw-bold mb-2">Payload Data (JSON):</h6>
        <div id="modalPayload" class="bg-light p-3 rounded font-monospace small" style="overflow-x:auto; border: 1px solid #e2e8f0; color:#334155;"></div>

        <div id="modalMediaSection" class="mt-3" style="display:none;">
            <h6 class="fw-bold mb-2">Media Tersimpan:</h6>
            <div id="modalMediaList" class="d-flex flex-wrap gap-2"></div>
        </div>
      </div>
    </div>
  </div>
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
    document.addEventListener('DOMContentLoaded', function() {
        // Inisialisasi Peta Leaflet
        var map = L.map('dataLakeMap').setView([-2.5489, 118.0149], 5); // Default center: Indonesia
        
        // Menggunakan OpenStreetMap tiles
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 19,
            attribution: '© OpenStreetMap'
        }).addTo(map);

        // Fungsi ikon warna-warni
        const coloredIcon = (color) => L.divIcon({
            className: "custom-pin",
            iconAnchor: [0, 24],
            labelAnchor: [-6, 0],
            popupAnchor: [0, -36],
            html: `<span style="background-color: ${color};
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

        // Data marker dari Controller (PHP ke JS)
        var markers = @json($mapMarkers);
        var bounds = [];
        
        markers.forEach(function(m) {
            if(m.lat && m.lng) {
                // Konversi objek m ke string JSON aman untuk dimasukkan ke onclick
                // Menggunakan encodeURIComponent untuk menghindari masalah kutip
                var safeData = encodeURIComponent(JSON.stringify(m));

                var popupContent = `
                    <div style="text-align:center; min-width: 180px;">
                        <h6 class="fw-bold mb-1" style="font-size:14px;">${m.title}</h6>
                        <span class="badge mb-1" style="background:${m.color}; color:white; font-size:10px;">${m.type}</span><br>
                        <small class="text-muted" style="font-size:11px;">${m.division}</small>
                        <div style="margin-top:10px;">
                            <button class="btn btn-sm btn-outline-primary w-100" style="border-radius:6px; font-size:12px; font-weight:600;" onclick="openDetailModal('${safeData}')">Lihat Data Lengkap</button>
                        </div>
                    </div>
                `;
                var marker = L.marker([m.lat, m.lng], {icon: coloredIcon(m.color)}).addTo(map)
                    .bindPopup(popupContent);
                bounds.push([m.lat, m.lng]);
            }
        });

        // Fit map to markers bounds if any exist
        if(bounds.length > 0) {
            map.fitBounds(bounds, {padding: [50, 50]});
        }
    });

    // Variabel global untuk menyimpan data marker aktif
    let currentMarkerData = null;

    // Fungsi membuka modal
    function openDetailModal(encodedData) {
        var m = JSON.parse(decodeURIComponent(encodedData));
        currentMarkerData = m; // simpan untuk wa share
        document.getElementById('modalTitle').innerText = m.title;
        document.getElementById('modalTypeBadge').innerText = m.type;
        document.getElementById('modalTypeBadge').style.backgroundColor = m.color;
        document.getElementById('modalTypeBadge').style.color = '#fff';
        document.getElementById('modalDiv').innerText = "Divisi: " + m.division;
        
        // Format JSON Payload
        document.getElementById('modalPayload').innerHTML = '<pre style="margin:0;">' + JSON.stringify(m.full_payload, null, 4) + '</pre>';

        // Tampilkan media jika ada
        if(m.media && m.media.length > 0) {
            document.getElementById('modalMediaSection').style.display = 'block';
            var mediaHtml = '';
            m.media.forEach(function(path) {
                // cek ekstensi
                if(path.match(/\.(jpeg|jpg|gif|png|webp)$/) != null) {
                    mediaHtml += `<a href="/${path}" target="_blank"><img src="/${path}" style="width:80px; height:80px; object-fit:cover; border-radius:8px; border:1px solid #ccc;"></a>`;
                } else {
                    mediaHtml += `<a href="/${path}" target="_blank" class="btn btn-outline-secondary btn-sm" style="height:80px; display:flex; align-items:center; justify-content:center; flex-direction:column; border-radius:8px;"><i class="bi bi-file-earmark-text fs-4"></i> Dokumen</a>`;
                }
            });
            document.getElementById('modalMediaList').innerHTML = mediaHtml;
        } else {
            document.getElementById('modalMediaSection').style.display = 'none';
        }

        // Tampilkan Modal via Bootstrap
        var modal = new bootstrap.Modal(document.getElementById('markerDetailModal'));
        modal.show();
    }

    // Fungsi Share ke WhatsApp
    function generateWaText(title, category, division, status, date, payloadObj) {
        let text = `*📊 DATA INTELLIGENCE REPORT*\n*Jostru Holding Company*\n--------------------------------\n`;
        text += `*Judul/Entitas:* ${title}\n`;
        text += `*Kategori:* ${category}\n`;
        text += `*Divisi:* ${division}\n`;
        if(status) text += `*Status:* ${status}\n`;
        if(date) text += `*Tanggal Input:* ${date}\n`;
        text += `\n*📑 Rincian Data:*\n`;
        
        for (const [key, value] of Object.entries(payloadObj)) {
            // Abaikan data gmaps link panjang jika ada
            if(key === 'company_name' || key === 'title') continue; // sudah ada di judul
            let valStr = typeof value === 'object' ? JSON.stringify(value) : value;
            if(valStr) {
                // capitalize key
                let cleanKey = key.replace(/_/g, ' ').replace(/\b\w/g, l => l.toUpperCase());
                text += `• *${cleanKey}:* ${valStr}\n`;
            }
        }
        
        text += `\n_Dikirim otomatis dari Jostru Command Center_`;
        return encodeURIComponent(text);
    }

    function shareTableDataToWA(encodedPayload, category, division, status, date, signedUrl) {
        try {
            let payloadStr = decodeURIComponent(encodedPayload);
            let payloadObj = JSON.parse(payloadStr);
            let title = payloadObj.company_name || payloadObj.title || payloadObj.name || 'Data ' + category;
            
            let waTextRaw = generateWaText(title, category, division, status, date, payloadObj);
            
            let finalWaText = waTextRaw + encodeURIComponent(`\n\n🔗 *Akses Laporan Penuh & Media:*\n${signedUrl}\n_(Link ini bersifat aman dan eksklusif)_`);
            window.open(`https://api.whatsapp.com/send?text=${finalWaText}`, '_blank');
        } catch(e) {
            alert('Gagal memproses data untuk WhatsApp.');
            console.error(e);
        }
    }

    function shareMarkerDataToWA() {
        if(!currentMarkerData) return;
        let m = currentMarkerData;
        let title = m.title;
        let category = m.category + ' (' + m.type + ')';
        let division = m.division || 'Global';
        
        let waTextRaw = generateWaText(title, category, division, '', '', m.full_payload);
        
        // Asumsi signed_url digenerate di controller dan dimasukkan ke m.signed_url
        let finalWaText = waTextRaw + encodeURIComponent(`\n\n🔗 *Akses Laporan Penuh & Media:*\n${m.signed_url}\n_(Link ini bersifat aman dan eksklusif)_`);
        window.open(`https://api.whatsapp.com/send?text=${finalWaText}`, '_blank');
    }
</script>
@endsection
