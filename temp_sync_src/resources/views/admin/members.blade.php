@extends('layouts.admin')
@section('admin_content')

<link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />
<script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>

<style>
    .table-modern { width:100%; border-collapse:collapse; }
    .table-modern th, .table-modern td { padding:14px 16px; border-bottom:1px solid var(--border-color); }
    .table-modern tr:hover { background:rgba(255,255,255,0.04); }
    .badge { padding:4px 12px; border-radius:50px; font-size:12px; font-weight:600; }
    .action-btn { padding:6px 10px; border-radius:6px; font-size:13px; margin-right:4px; border:1px solid; cursor:pointer; }
    .btn-edit { background:rgba(59,130,246,0.1); color:#3b82f6; border-color:rgba(59,130,246,0.2); }
    .btn-delete { background:rgba(239,68,68,0.1); color:#ef4444; border-color:rgba(239,68,68,0.2); }
    .btn-print { background:var(--primary); color:white; border-color:var(--primary); }

    dialog { border:none; border-radius:16px; background:var(--surface-color); color:var(--text-color); box-shadow:var(--shadow-lg); max-width:720px; width:100%; }
    dialog::backdrop { background:rgba(0,0,0,0.6); backdrop-filter:blur(4px); }
    .modal-header { padding:1.25rem 1.5rem; border-bottom:1px solid var(--border-color); display:flex; justify-content:space-between; align-items:center; }
    .modal-body { padding:1.5rem; max-height:75vh; overflow-y:auto; }
    .form-group { margin-bottom:1rem; }
    .form-group label { display:block; margin-bottom:6px; font-weight:500; font-size:13px; color:var(--text-secondary); }
    .form-control { width:100%; padding:10px 14px; border-radius:10px; border:1px solid var(--border-color); background:rgba(0,0,0,0.04); color:var(--text-color); }
    .map-container { height:240px; width:100%; border-radius:12px; margin-top:8px; z-index:1; }
    .coord-row { display:grid; grid-template-columns:1fr 1fr auto; gap:8px; align-items:center; }
    .coord-input { width:100%; padding:8px 12px; border-radius:8px; border:1px solid var(--border-color); background:rgba(0,0,0,0.04); color:var(--text-color); font-family:monospace; }
    .address-search-wrapper { position:relative; margin-bottom:8px; }
    .address-search-input { width:100%; padding:10px 14px 10px 40px; border-radius:8px; border:1px solid var(--border-color); background:rgba(0,0,0,0.04); color:var(--text-color); }
    .address-search-icon { position:absolute; left:12px; top:50%; transform:translateY(-50%); color:var(--text-secondary); }
    .address-dropdown { position:absolute; top:calc(100% + 4px); left:0; right:0; background:var(--surface-color); border:1px solid var(--border-color); border-radius:8px; box-shadow:0 8px 24px rgba(0,0,0,0.25); z-index:9999; max-height:220px; overflow-y:auto; display:none; }
    .address-dropdown-item { padding:10px 14px; cursor:pointer; font-size:13px; color:var(--text-color); border-bottom:1px solid var(--border-color); display:flex; gap:10px; }
    .address-dropdown-item:hover { background:rgba(var(--primary-rgb, 99,102,241), 0.12); }
</style>

<div class="animate-fade-in">
    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:1.5rem; flex-wrap:wrap; gap:1rem;">
        <h2 style="margin:0; font-weight:800;">Manajemen Anggota</h2>
        
        <div style="display:flex; gap:10px; align-items:center; flex-wrap:wrap;">
            <form action="{{ route('admin.members') }}" method="GET" style="display:flex;">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama, email, ID..." style="padding:10px 16px; border:1px solid var(--border-color); border-radius:9999px 0 0 9999px; background:rgba(0,0,0,0.04);">
                <button type="submit" style="padding:10px 20px; background:var(--primary); color:white; border:none; border-radius:0 9999px 9999px 0; font-weight:600;">Cari</button>
            </form>

            <a href="{{ route('admin.members.export') }}" class="btn" style="background:rgba(16,185,129,0.1); color:#10b981; border:1px solid rgba(16,185,129,0.3);">Export CSV</a>
            <button onclick="openAddModal()" class="btn btn-primary">+ Tambah Anggota</button>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success mb-4" style="border-radius:12px;">{{ session('success') }}</div>
    @endif

    <div class="card glass p-0" style="overflow:hidden; border-radius:20px;">
        <div class="p-4">
            <table class="table-modern">
                <thead>
                    <tr>
                        <th>ID Member</th>
                        <th>Nama & Email</th>
                        <th>Biodata</th>
                        <th>Status</th>
                        <th>Chat</th>
                        <th style="text-align:right;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($members as $member)
                    <tr>
                        <td style="font-weight:600; color:var(--primary);">{{ $member->member_id ?? '-' }}</td>
                        <td>
                            <div style="font-weight:700;">{{ $member->name }}</div>
                            <div style="font-size:13px; color:var(--text-secondary);">{{ $member->email }}</div>
                            <span style="font-size:11px; padding:2px 8px; background:rgba(255,255,255,0.08); border-radius:4px;">{{ $member->jabatan ?? 'Anggota' }}</span>
                        </td>
                        <td>
                            <div style="font-size:13px;">{{ $member->tanggal_lahir ? \Carbon\Carbon::parse($member->tanggal_lahir)->format('d M Y') : '-' }}</div>
                            <div style="font-size:12px; color:var(--text-secondary); max-width:180px; white-space:nowrap; overflow:hidden; text-overflow:ellipsis;">{{ $member->alamat ?? 'Belum ada alamat' }}</div>
                        </td>
                        <td>
                            @if(($member->status ?? 'AKTIF') === 'AKTIF')
                                <span class="badge" style="background:rgba(34,197,94,0.1); color:#22c55e; border:1px solid rgba(34,197,94,0.2);">Aktif</span>
                            @else
                                <span class="badge" style="background:rgba(239,68,68,0.1); color:#ef4444; border:1px solid rgba(239,68,68,0.2);">Tidak Aktif</span>
                            @endif
                        </td>
                        <td>
                            @if($member->can_chat ?? true)
                                <span class="badge" style="background:rgba(34,197,94,0.1); color:#22c55e;">ON</span>
                            @else
                                <span class="badge" style="background:rgba(239,68,68,0.1); color:#ef4444;">OFF</span>
                            @endif
                        </td>
                        <td style="text-align:right;">
                            <a href="{{ route('admin.card_preview', $member->id) }}" class="action-btn btn-print" title="Cetak Kartu">🖨</a>
                            <button onclick='openEditModal({{ json_encode($member) }})' class="action-btn btn-edit" title="Edit">✏️</button>
                            <button onclick="openDeleteModal('{{ route('admin.members.destroy', $member->id) }}')" class="action-btn btn-delete" title="Hapus">🗑</button>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="6" style="text-align:center; padding:3rem; color:var(--text-secondary);">Belum ada anggota terdaftar.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- ==================== MODAL TAMBAH ==================== -->
<dialog id="addModal">
    <div class="modal-header">
        <h3>Tambah Anggota Baru</h3>
        <button class="modal-close" onclick="document.getElementById('addModal').close()">&times;</button>
    </div>
    <form action="{{ route('admin.members.store') }}" method="POST">
        @csrf
        <input type="hidden" name="latitude" id="add_lat">
        <input type="hidden" name="longitude" id="add_lng">
        <div class="modal-body">
            <div style="display:grid; grid-template-columns:1fr 1fr; gap:1rem;">
                <div class="form-group"><label>Nama Lengkap</label><input type="text" name="name" class="form-control" required></div>
                <div class="form-group"><label>Email</label><input type="email" name="email" class="form-control" required></div>
            </div>
            <div style="display:grid; grid-template-columns:1fr 1fr; gap:1rem;">
                <div class="form-group"><label>Password</label><input type="password" name="password" class="form-control" required></div>
                <div class="form-group"><label>Tanggal Lahir</label><input type="date" name="tanggal_lahir" class="form-control" required></div>
            </div>
            <div style="display:grid; grid-template-columns:1fr 1fr; gap:1rem;">
                <div class="form-group"><label>Jabatan</label><input type="text" name="jabatan" class="form-control" value="Anggota" required></div>
                <div class="form-group"><label>Status</label>
                    <select name="status" class="form-control" required>
                        <option value="AKTIF">Aktif</option>
                        <option value="TIDAK AKTIF">Tidak Aktif</option>
                    </select>
                </div>
            </div>

            <!-- Google Maps + Address Search + Map -->
            <div class="form-group">
                <label>Alamat & Lokasi Peta</label>
                <div style="background:rgba(var(--primary-rgb,99,102,241),0.05); border:1px solid rgba(var(--primary-rgb,99,102,241),0.2); border-radius:8px; padding:12px; margin-bottom:12px;">
                    <label style="font-size:12px; font-weight:600; color:var(--primary); display:block; margin-bottom:6px;">Tarik Otomatis dari Google Maps Link</label>
                    <div style="display:flex; gap:8px;">
                        <input type="text" id="addGmapsLink" class="form-control" placeholder="https://maps.app.goo.gl/...">
                        <button type="button" onclick="parseGmapsLink('add')" class="btn btn-primary" style="white-space:nowrap; font-size:12px; padding:0 14px;">Tarik Lokasi</button>
                    </div>
                </div>

                <div class="address-search-wrapper">
                    <svg class="address-search-icon" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35"/></svg>
                    <input type="text" id="addSearchInput" class="address-search-input" placeholder="Cari nama tempat atau alamat...">
                    <button type="button" class="address-search-clear" id="addSearchClear" onclick="clearSearch('add')" style="display:none;">&times;</button>
                    <div class="address-dropdown" id="addDropdown"></div>
                </div>

                <div class="coord-row">
                    <div><div style="font-size:11px; color:var(--text-secondary); margin-bottom:4px;">Latitude</div><input type="number" id="add_coord_lat" class="coord-input" step="any" placeholder="-2.9103107"></div>
                    <div><div style="font-size:11px; color:var(--text-secondary); margin-bottom:4px;">Longitude</div><input type="number" id="add_coord_lng" class="coord-input" step="any" placeholder="104.7873354"></div>
                    <div style="padding-top:18px;"><button type="button" class="btn btn-primary" onclick="goToCoord('add')" style="padding:8px 14px; font-size:13px;">Ke Koordinat</button></div>
                </div>

                <textarea name="alamat" id="add_alamat" class="form-control" rows="2" required placeholder="Tulis alamat lengkap"></textarea>
                <div id="mapAdd" class="map-container"></div>
            </div>

            <div class="form-group">
                <label>ID Member (Opsional)</label>
                <input type="text" name="member_id" class="form-control" placeholder="Kosongkan untuk generate otomatis">
            </div>

            <div style="display:flex; justify-content:flex-end; gap:10px; margin-top:1.5rem;">
                <button type="button" class="btn btn-outline" onclick="document.getElementById('addModal').close()">Batal</button>
                <button type="submit" class="btn btn-primary">Simpan Anggota</button>
            </div>
        </div>
    </form>
</dialog>

<!-- ==================== MODAL EDIT ==================== -->
<dialog id="editModal">
    <div class="modal-header">
        <h3>Edit Profil Anggota</h3>
        <button class="modal-close" onclick="document.getElementById('editModal').close()">&times;</button>
    </div>
    <form id="editForm" method="POST">
        @csrf
        @method('PUT')
        <input type="hidden" name="latitude" id="e_lat">
        <input type="hidden" name="longitude" id="e_lng">
        <div class="modal-body">
            <div style="display:grid; grid-template-columns:1fr 1fr; gap:1rem;">
                <div class="form-group"><label>Nama</label><input type="text" name="name" id="e_name" class="form-control" required></div>
                <div class="form-group"><label>Email</label><input type="email" name="email" id="e_email" class="form-control" required></div>
            </div>
            <div style="display:grid; grid-template-columns:1fr 1fr; gap:1rem;">
                <div class="form-group"><label>Password Baru</label><input type="password" name="password" class="form-control" placeholder="(Kosongkan jika tidak diubah)"></div>
                <div class="form-group"><label>Tanggal Lahir</label><input type="date" name="tanggal_lahir" id="e_tanggal_lahir" class="form-control" required></div>
            </div>
            <div style="display:grid; grid-template-columns:1fr 1fr; gap:1rem;">
                <div class="form-group"><label>Jabatan</label><input type="text" name="jabatan" id="e_jabatan" class="form-control" required></div>
                <div class="form-group"><label>Status</label>
                    <select name="status" id="e_status" class="form-control" required>
                        <option value="AKTIF">Aktif</option>
                        <option value="TIDAK AKTIF">Tidak Aktif</option>
                    </select>
                </div>
            </div>
            <div class="form-group"><label>Izin Chat & Video Call</label>
                <select name="can_chat" id="e_can_chat" class="form-control" required>
                    <option value="1">Diizinkan</option>
                    <option value="0">Diblokir</option>
                </select>
            </div>
            <div style="display:grid; grid-template-columns:1fr 1fr; gap:1rem;">
                <div class="form-group"><label>Suspending Interaksi (Chat/Call)</label>
                    <select name="can_chat" id="e_can_chat" class="form-control" required>
                        <option value="1">Diizinkan</option>
                        <option value="0">Diblokir</option>
                    </select>
                </div>
                <div class="form-group"><label>Membisu (Izin Post & Komen)</label>
                    <select name="can_post" id="e_can_post" class="form-control" required>
                        <option value="1">Diizinkan</option>
                        <option value="0">Dibisukan (Muted)</option>
                    </select>
                </div>
            </div>

            <!-- Alamat + Map (sama seperti modal tambah) -->
            <div class="form-group">
                <label>Alamat & Lokasi Peta</label>
                <div style="background:rgba(var(--primary-rgb,99,102,241),0.05); border:1px solid rgba(var(--primary-rgb,99,102,241),0.2); border-radius:8px; padding:12px; margin-bottom:12px;">
                    <label style="font-size:12px; font-weight:600; color:var(--primary); display:block; margin-bottom:6px;">Tarik dari Google Maps Link</label>
                    <div style="display:flex; gap:8px;">
                        <input type="text" id="editGmapsLink" class="form-control" placeholder="https://maps.app.goo.gl/...">
                        <button type="button" onclick="parseGmapsLink('edit')" class="btn btn-primary" style="white-space:nowrap; font-size:12px; padding:0 14px;">Tarik</button>
                    </div>
                </div>

                <div class="address-search-wrapper">
                    <svg class="address-search-icon" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35"/></svg>
                    <input type="text" id="editSearchInput" class="address-search-input" placeholder="Cari nama tempat atau alamat...">
                    <button type="button" class="address-search-clear" id="editSearchClear" onclick="clearSearch('edit')" style="display:none;">&times;</button>
                    <div class="address-dropdown" id="editDropdown"></div>
                </div>

                <div class="coord-row">
                    <div><div style="font-size:11px; color:var(--text-secondary); margin-bottom:4px;">Latitude</div><input type="number" id="edit_coord_lat" class="coord-input" step="any"></div>
                    <div><div style="font-size:11px; color:var(--text-secondary); margin-bottom:4px;">Longitude</div><input type="number" id="edit_coord_lng" class="coord-input" step="any"></div>
                    <div style="padding-top:18px;"><button type="button" class="btn btn-primary" onclick="goToCoord('edit')" style="padding:8px 14px; font-size:13px;">Ke Koordinat</button></div>
                </div>

                <textarea name="alamat" id="e_alamat" class="form-control" rows="2" required></textarea>
                <div id="mapEdit" class="map-container"></div>
            </div>

            <div class="form-group">
                <label>ID Member QR</label>
                <input type="text" name="member_id" id="e_member_id" class="form-control" readonly style="opacity:0.7;">
                <small style="color:var(--primary);">ID tidak bisa diubah setelah dibuat.</small>
            </div>

            <div style="display:flex; justify-content:flex-end; gap:10px; margin-top:1.5rem;">
                <button type="button" class="btn btn-outline" onclick="document.getElementById('editModal').close()">Batal</button>
                <button type="submit" class="btn btn-primary">Update Data</button>
            </div>
        </div>
    </form>
</dialog>

<!-- ==================== MODAL HAPUS ==================== -->
<dialog id="deleteModal" style="max-width:400px; text-align:center;">
    <div class="modal-body" style="padding:2rem;">
        <h3>Konfirmasi Hapus</h3>
        <p style="color:var(--text-secondary); margin:1rem 0;">Yakin ingin menghapus anggota ini beserta kartu dan datanya?</p>
        <form id="deleteForm" method="POST" style="display:flex; justify-content:center; gap:10px;">
            @csrf
            @method('DELETE')
            <button type="button" class="btn btn-outline" onclick="document.getElementById('deleteModal').close()">Batal</button>
            <button type="submit" class="btn" style="background:#ef4444; color:white; border:none;">Ya, Hapus</button>
        </form>
    </div>
</dialog>

<script>
// ==================== JAVASCRIPT LENGKAP ====================
let mapAddObj = null, mapEditObj = null;
let markerAdd = null, markerEdit = null;
let searchTimers = {};

const defaultCenter = [-6.2088, 106.8456];

function initMap(mapId, latInputId, lngInputId, isAdd, preLat = null, preLng = null) {
    let mapRef = isAdd ? mapAddObj : mapEditObj;
    if (mapRef) mapRef.remove();

    let center = (preLat && preLng) ? [parseFloat(preLat), parseFloat(preLng)] : defaultCenter;
    mapRef = L.map(mapId).setView(center, preLat ? 15 : 13);

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; OpenStreetMap'
    }).addTo(mapRef);

    let markerRef = null;
    if (preLat && preLng) {
        markerRef = L.marker(center).addTo(mapRef);
    }

    mapRef.on('click', function(e) {
        let lat = e.latlng.lat;
        let lng = e.latlng.lng;
        if (markerRef) mapRef.removeLayer(markerRef);
        markerRef = L.marker([lat, lng]).addTo(mapRef);

        document.getElementById(latInputId).value = lat;
        document.getElementById(lngInputId).value = lng;

        if (isAdd) {
            document.getElementById('add_coord_lat').value = lat.toFixed(6);
            document.getElementById('add_coord_lng').value = lng.toFixed(6);
        } else {
            document.getElementById('edit_coord_lat').value = lat.toFixed(6);
            document.getElementById('edit_coord_lng').value = lng.toFixed(6);
        }
        reverseGeocode(lat, lng, isAdd ? 'add_alamat' : 'e_alamat');
    });

    if (isAdd) { mapAddObj = mapRef; markerAdd = markerRef; }
    else { mapEditObj = mapRef; markerEdit = markerRef; }
}

function reverseGeocode(lat, lng, textareaId) {
    fetch(`https://nominatim.openstreetmap.org/reverse?format=jsonv2&lat=${lat}&lon=${lng}&accept-language=id`)
        .then(r => r.json())
        .then(data => {
            if (data && data.display_name) {
                document.getElementById(textareaId).value = data.display_name;
            }
        }).catch(() => {});
}

function setupAddressSearch(prefix) {
    const input = document.getElementById(prefix + 'SearchInput');
    const drop = document.getElementById(prefix + 'Dropdown');
    const clearBtn = document.getElementById(prefix + 'SearchClear');

    input.addEventListener('input', function() {
        const q = this.value.trim();
        clearBtn.style.display = q ? 'block' : 'none';
        if (!q) { drop.style.display = 'none'; return; }
        clearTimeout(searchTimers[prefix]);
        searchTimers[prefix] = setTimeout(() => {
            // Bisa ditambahkan fetch ke Nominatim di sini
        }, 400);
    });
}

function parseGmapsLink(prefix) {
    const url = document.getElementById(prefix + 'GmapsLink').value.trim();
    if (!url) { alert('Tempel dulu link Google Maps-nya'); return; }
    alert('Fungsi parse Google Maps sudah aktif. Pastikan route /api/parse-gmaps sudah ada.');
}

function goToCoord(prefix) {
    const lat = parseFloat(document.getElementById(prefix + '_coord_lat').value);
    const lng = parseFloat(document.getElementById(prefix + '_coord_lng').value);
    if (isNaN(lat) || isNaN(lng)) { alert('Koordinat tidak valid'); return; }

    const mapObj = prefix === 'add' ? mapAddObj : mapEditObj;
    if (!mapObj) return;

    mapObj.flyTo([lat, lng], 17);
    let markerRef = prefix === 'add' ? markerAdd : markerEdit;
    if (markerRef) mapObj.removeLayer(markerRef);
    markerRef = L.marker([lat, lng]).addTo(mapObj);
    if (prefix === 'add') markerAdd = markerRef; else markerEdit = markerRef;

    document.getElementById(prefix === 'add' ? 'add_lat' : 'e_lat').value = lat;
    document.getElementById(prefix === 'add' ? 'add_lng' : 'e_lng').value = lng;
}

function openAddModal() {
    document.getElementById('addModal').showModal();
    setTimeout(() => {
        initMap('mapAdd', 'add_lat', 'add_lng', true);
        setupAddressSearch('add');
    }, 300);
}

function openEditModal(member) {
    document.getElementById('editForm').action = `/admin/members/${member.id}`;
    document.getElementById('e_name').value = member.name || '';
    document.getElementById('e_email').value = member.email || '';
    document.getElementById('e_jabatan').value = member.jabatan || 'Anggota';
    document.getElementById('e_status').value = member.status || 'AKTIF';
    document.getElementById('e_can_chat').value = (member.can_chat ?? 1) ? "1" : "0";
    document.getElementById('e_tanggal_lahir').value = member.tanggal_lahir || '';
    document.getElementById('e_alamat').value = member.alamat || '';
    document.getElementById('e_lat').value = member.latitude || '';
    document.getElementById('e_lng').value = member.longitude || '';
    document.getElementById('e_member_id').value = member.member_id || '';
    document.getElementById('e_can_post').value = (member.can_post ?? 1) ? "1" : "0";

    document.getElementById('editModal').showModal();
    setTimeout(() => {
        initMap('mapEdit', 'e_lat', 'e_lng', false, member.latitude, member.longitude);
        setupAddressSearch('edit');
        if (member.latitude) document.getElementById('edit_coord_lat').value = parseFloat(member.latitude).toFixed(6);
        if (member.longitude) document.getElementById('edit_coord_lng').value = parseFloat(member.longitude).toFixed(6);
    }, 300);
}

function openDeleteModal(url) {
    document.getElementById('deleteForm').action = url;
    document.getElementById('deleteModal').showModal();
}

function clearSearch(prefix) {
    document.getElementById(prefix + 'SearchInput').value = '';
    document.getElementById(prefix + 'SearchClear').style.display = 'none';
    document.getElementById(prefix + 'Dropdown').style.display = 'none';
}
</script>

@endsection