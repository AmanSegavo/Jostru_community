@extends('layouts.admin')
@section('admin_content')

<link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />
<script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>

<style>
    .table-modern { width:100%; border-collapse:collapse; }
    .table-modern th { padding:16px 20px; font-weight:700; color:var(--text-secondary); text-transform:uppercase; font-size:12px; letter-spacing:0.5px; border-bottom:2px solid rgba(0,0,0,0.05); }
    .table-modern td { padding:16px 20px; border-bottom:1px solid rgba(0,0,0,0.05); vertical-align:middle; }
    .table-modern tr:hover { background:rgba(34,197,94,0.03); }
    
    .member-avatar { width:45px; height:45px; border-radius:50%; background:linear-gradient(135deg, #22c55e, #10b981); color:white; display:flex; align-items:center; justify-content:center; font-weight:bold; font-size:18px; text-shadow:0 2px 4px rgba(0,0,0,0.2); flex-shrink:0; }
    
    .badge { padding:6px 12px; border-radius:50px; font-size:12px; font-weight:700; letter-spacing:0.5px; }
    .badge-soft-success { background:rgba(34,197,94,0.1); color:#16a34a; border:1px solid rgba(34,197,94,0.2); }
    .badge-soft-danger { background:rgba(239,68,68,0.1); color:#dc2626; border:1px solid rgba(239,68,68,0.2); }
    .badge-soft-info { background:rgba(59,130,246,0.1); color:#2563eb; border:1px solid rgba(59,130,246,0.2); }
    
    .action-btn { width:35px; height:35px; display:inline-flex; align-items:center; justify-content:center; border-radius:10px; font-size:14px; transition:all 0.3s ease; border:none; }
    .btn-edit { background:rgba(59,130,246,0.1); color:#3b82f6; }
    .btn-edit:hover { background:#3b82f6; color:white; transform:translateY(-2px); box-shadow:0 5px 15px rgba(59,130,246,0.3); }
    .btn-delete { background:rgba(239,68,68,0.1); color:#ef4444; }
    .btn-delete:hover { background:#ef4444; color:white; transform:translateY(-2px); box-shadow:0 5px 15px rgba(239,68,68,0.3); }

    .glass-search { padding:12px 20px; border:1px solid rgba(34,197,94,0.2); border-radius:30px 0 0 30px; background:rgba(255,255,255,0.7); backdrop-filter:blur(10px); box-shadow:inset 0 2px 5px rgba(0,0,0,0.02); outline:none; transition:all 0.3s ease; }
    .glass-search:focus { border-color:#22c55e; background:#fff; }
    .glass-search-btn { padding:12px 24px; background:linear-gradient(135deg, #22c55e, #10b981); color:white; border:none; border-radius:0 30px 30px 0; font-weight:700; box-shadow:0 5px 15px rgba(34,197,94,0.3); transition:all 0.3s ease; }
    .glass-search-btn:hover { transform:translateY(-1px); box-shadow:0 8px 20px rgba(34,197,94,0.4); }

    dialog { border:none; border-radius:24px; background:var(--surface-color); color:var(--text-color); box-shadow:0 25px 50px -12px rgba(0,0,0,0.25); max-width:720px; width:90vw; box-sizing:border-box; margin:auto; overflow:hidden; }
    dialog::backdrop { background:rgba(15,23,42,0.7); backdrop-filter:blur(8px); }
    .modal-header { padding:1.5rem 2rem; border-bottom:1px solid rgba(0,0,0,0.05); background:rgba(248,250,252,0.8); display:flex; justify-content:space-between; align-items:center; }
    .modal-body { padding:2rem; max-height:75vh; overflow-y:auto; }
    .form-group { margin-bottom:1.25rem; }
    .form-group label { display:block; margin-bottom:8px; font-weight:600; font-size:13px; color:var(--text-secondary); text-transform:uppercase; letter-spacing:0.5px; }
    .form-control { width:100%; padding:12px 16px; border-radius:12px; border:1px solid rgba(0,0,0,0.1); background:#f8fafc; color:var(--text-color); transition:all 0.3s ease; }
    .form-control:focus { background:#fff; border-color:#22c55e; box-shadow:0 0 0 4px rgba(34,197,94,0.1); outline:none; }
    .map-container { height:240px; width:100%; border-radius:16px; margin-top:8px; z-index:1; border:2px solid rgba(0,0,0,0.05); }
    
    .nav-pills .nav-link { border-radius:30px; font-weight:700; padding:12px 28px; color:var(--text-secondary); transition:all 0.3s ease; margin-right:8px; border:1px solid transparent; }
    .nav-pills .nav-link:hover { background:rgba(34,197,94,0.05); color:#22c55e; }
    .nav-pills .nav-link.active { background:#22c55e; color:white; box-shadow:0 8px 20px rgba(34,197,94,0.3); }
</style>

<div class="animate-fade-in">
    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:2rem; flex-wrap:wrap; gap:1.5rem;">
        <div>
            <h2 style="margin:0; font-weight:900; font-size:2rem; letter-spacing:-0.5px;">Manajemen <span style="color:#22c55e;">Anggota</span></h2>
            <p style="color:var(--text-secondary); margin:4px 0 0 0;">Kelola data anggota, divisi, dan perizinan sistem Jostru.</p>
        </div>
        
        <div style="display:flex; gap:12px; align-items:center; flex-wrap:wrap;">
            <form action="{{ route('admin.members') }}" method="GET" style="display:flex;">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama, email, ID..." class="glass-search">
                <button type="submit" class="glass-search-btn">🔍 Cari</button>
            </form>

            <a href="{{ route('admin.members.export') }}" class="btn" style="background:white; color:#10b981; border:1px solid #10b981; border-radius:30px; padding:12px 20px; font-weight:700; box-shadow:0 4px 10px rgba(16,185,129,0.1);">⬇️ Export CSV</a>
            <button onclick="openAddModal()" class="btn btn-primary" style="border-radius:30px; padding:12px 20px; font-weight:700; background:linear-gradient(135deg,#3b82f6,#2563eb); box-shadow:0 8px 20px rgba(59,130,246,0.3);">+ Tambah Anggota</button>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success mb-4" style="border-radius:16px; background:rgba(34,197,94,0.1); color:#16a34a; border:1px solid rgba(34,197,94,0.2); font-weight:600; padding:1rem 1.5rem;">
            ✅ {{ session('success') }}
        </div>
    @endif

    <ul class="nav nav-pills mb-4" id="memberTab" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link active" id="semua-tab" data-bs-toggle="tab" data-bs-target="#semua" type="button" role="tab">Semua Anggota</button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="aktif-tab" data-bs-toggle="tab" data-bs-target="#aktif" type="button" role="tab">Telah Diverifikasi (Aktif)</button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="pending-tab" data-bs-toggle="tab" data-bs-target="#pending" type="button" role="tab">Menunggu ACC (Pending)</button>
        </li>
    </ul>

    <div class="tab-content" id="memberTabContent">
        <!-- Tab Semua Anggota -->
        <div class="tab-pane fade show active" id="semua" role="tabpanel">
            <div class="card p-0" style="overflow:hidden; border-radius:24px; border:none; background:white; box-shadow:0 10px 30px rgba(0,0,0,0.03);">
                <div class="table-responsive">
                    <table class="table-modern m-0">
                        <thead>
                            <tr style="background:#f8fafc;">
                                <th>Profil Anggota</th>
                                <th>Kredensial</th>
                                <th>Data Diri & Akses</th>
                                <th class="text-center">Akses Chat</th>
                                <th class="text-center">Status</th>
                                <th style="text-align:right;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($members as $member)
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center gap-3">
                                        <div class="member-avatar" @if($member->status == 'PENDING') style="background:linear-gradient(135deg, #f59e0b, #d97706);" @endif>{{ strtoupper(substr($member->name, 0, 1)) }}</div>
                                        <div>
                                            <div style="font-weight:800; color:#1e293b; font-size:15px;">{{ $member->name }}</div>
                                            <div style="font-size:12px; color:#64748b; font-weight:600;">ID: {{ $member->member_id ?? '-' }}</div>
                                            <div style="margin-top:6px; display:flex; flex-wrap:wrap; gap:4px;">
                                                <span class="badge badge-soft-info">{{ $member->jabatan ?? 'Anggota' }}</span>
                                                @if($member->division)
                                                    <span class="badge bg-secondary" style="font-size:10px;">{{ $member->division->name }} (Utama)</span>
                                                @endif
                                                @foreach($member->assignedDivisions as $div)
                                                    <span class="badge" style="background:rgba(139,92,246,0.1); color:#7c3aed; border:1px solid rgba(139,92,246,0.2); font-size:10px;">{{ $div->name }} ({{ $div->pivot->jabatan ?? 'Anggota' }})</span>
                                                @endforeach
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div style="font-size:13px; font-weight:600; color:#334155;">{{ $member->email }}</div>
                                    <div style="font-size:11px; color:#94a3b8; font-weight:500;">Role: {{ ucfirst($member->role) }}</div>
                                </td>
                                <td>
                                    <div style="font-size:13px; color:#475569; display:flex; gap:6px; align-items:center;">
                                        <span>📅</span> {{ $member->tanggal_lahir ? \Carbon\Carbon::parse($member->tanggal_lahir)->format('d M Y') : '-' }}
                                    </div>
                                    <div style="font-size:13px; color:#475569; display:flex; gap:6px; align-items:center; margin-top:4px;">
                                        <span>📍</span> <span style="white-space:nowrap; overflow:hidden; text-overflow:ellipsis; max-width:120px;" title="{{ $member->alamat }}">{{ $member->alamat ?: '-' }}</span>
                                    </div>
                                </td>
                                <td class="text-center">
                                    <form action="{{ route('admin.members.toggle_chat', $member->id) }}" method="POST" style="display:inline-block;">
                                        @csrf
                                        <button type="submit" class="toggle-btn {{ $member->can_chat ? 'active' : '' }}" title="Klik untuk mengubah akses">
                                            <div class="toggle-knob"></div>
                                        </button>
                                    </form>
                                    <div style="font-size:11px; margin-top:4px; font-weight:600; color:{{ $member->can_chat ? '#22c55e' : '#ef4444' }};">
                                        {{ $member->can_chat ? 'Diizinkan' : 'Diblokir' }}
                                    </div>
                                </td>
                                <td class="text-center">
                                    @if($member->status == 'AKTIF')
                                        <span class="badge badge-soft-success">● Aktif</span>
                                    @elseif($member->status == 'PENDING')
                                        <span class="badge badge-soft-warning" style="background:rgba(245,158,11,0.1); color:#d97706;">● Pending</span>
                                    @else
                                        <span class="badge badge-soft-danger">● {{ $member->status }}</span>
                                    @endif
                                </td>
                                <td style="text-align:right; vertical-align:middle; display:flex; justify-content:flex-end; gap:8px;">
                                    <button onclick='openEditModal({{ json_encode($member) }})' class="action-btn btn-edit" title="Edit Anggota">✏️</button>
                                    <button onclick="openDeleteModal('{{ route('admin.members.destroy', $member->id) }}')" class="action-btn btn-delete" title="Hapus Anggota">🗑</button>
                                </td>
                            </tr>
                            @empty
                            <tr><td colspan="6" style="text-align:center; padding:3rem; color:var(--text-secondary);">Belum ada data anggota.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Tab Aktif -->
        <div class="tab-pane fade" id="aktif" role="tabpanel">
            <div class="card p-0" style="overflow:hidden; border-radius:24px; border:none; background:white; box-shadow:0 10px 30px rgba(0,0,0,0.03);">
                <div class="table-responsive">
                    <table class="table-modern m-0">
                        <thead>
                            <tr style="background:#f8fafc;">
                                <th>Profil Anggota</th>
                                <th>Kredensial</th>
                                <th>Data Diri & Akses</th>
                                <th class="text-center">Akses Chat</th>
                                <th class="text-center">Status</th>
                                <th style="text-align:right;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($members->where('status', 'AKTIF') as $member)
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center gap-3">
                                        <div class="member-avatar">{{ strtoupper(substr($member->name, 0, 1)) }}</div>
                                        <div>
                                            <div style="font-weight:800; color:#1e293b; font-size:15px;">{{ $member->name }}</div>
                                            <div style="font-size:12px; color:#64748b; font-weight:600;">ID: {{ $member->member_id ?? '-' }}</div>
                                            <div style="margin-top:6px; display:flex; flex-wrap:wrap; gap:4px;">
                                                <span class="badge badge-soft-info">{{ $member->jabatan ?? 'Anggota' }}</span>
                                                @if($member->division)
                                                    <span class="badge bg-secondary" style="font-size:10px;">{{ $member->division->name }} (Utama)</span>
                                                @endif
                                                @foreach($member->assignedDivisions as $div)
                                                    <span class="badge" style="background:rgba(139,92,246,0.1); color:#7c3aed; border:1px solid rgba(139,92,246,0.2); font-size:10px;">{{ $div->name }} ({{ $div->pivot->jabatan ?? 'Anggota' }})</span>
                                                @endforeach
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div style="font-size:13px; font-weight:600; color:#334155;">{{ $member->email }}</div>
                                    <div style="font-size:12px; color:#94a3b8; margin-top:2px;">Role: <span style="text-transform:capitalize; font-weight:700;">{{ $member->role ?? 'member' }}</span></div>
                                </td>
                                <td>
                                    <div style="font-size:13px; color:#475569; display:flex; gap:6px; align-items:center;">
                                        <span>📅</span> {{ $member->tanggal_lahir ? \Carbon\Carbon::parse($member->tanggal_lahir)->format('d M Y') : '-' }}
                                    </div>
                                    <div style="font-size:13px; color:#475569; max-width:200px; white-space:nowrap; overflow:hidden; text-overflow:ellipsis; display:flex; gap:6px; align-items:center; margin-top:4px;">
                                        <span>📍</span> {{ $member->alamat ?? 'Belum ada alamat' }}
                                    </div>
                                </td>
                                <td class="text-center">
                                    <div class="form-check form-switch d-flex justify-content-center m-0">
                                        <input class="form-check-input chat-toggle" type="checkbox" data-id="{{ $member->id }}" {{ ($member->can_chat ?? 1) ? 'checked' : '' }} style="cursor:pointer; width:45px; height:24px;">
                                    </div>
                                </td>
                                <td class="text-center">
                                    <span class="badge badge-soft-success">● Aktif</span>
                                </td>
                                <td style="text-align:right; vertical-align:middle;">
                                    <button onclick='openEditModal({{ json_encode($member) }})' class="action-btn btn-edit" title="Edit Data">✏️</button>
                                    <button onclick="openDeleteModal('{{ route('admin.members.destroy', $member->id) }}')" class="action-btn btn-delete" title="Hapus Akun">🗑</button>
                                </td>
                            </tr>
                            @empty
                            <tr><td colspan="6" style="text-align:center; padding:4rem; color:var(--text-secondary);">
                                <div style="font-size:3rem; margin-bottom:1rem;">👥</div>
                                <h4 style="font-weight:700; color:#64748b;">Belum Ada Anggota Aktif</h4>
                                <p>Anggota yang telah diverifikasi akan muncul di sini.</p>
                            </td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Tab Pending -->
        <div class="tab-pane fade" id="pending" role="tabpanel">
            <div class="card p-0" style="overflow:hidden; border-radius:24px; border:none; background:white; box-shadow:0 10px 30px rgba(0,0,0,0.03);">
                <div class="table-responsive">
                    <table class="table-modern m-0">
                        <thead>
                            <tr style="background:#f8fafc;">
                                <th>Profil Anggota</th>
                                <th>Kredensial</th>
                                <th>Data Diri</th>
                                <th class="text-center">Status</th>
                                <th style="text-align:right;">Aksi Persetujuan</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($members->where('status', '!=', 'AKTIF') as $member)
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center gap-3">
                                        <div class="member-avatar" style="background:linear-gradient(135deg, #f59e0b, #d97706);">{{ strtoupper(substr($member->name, 0, 1)) }}</div>
                                        <div>
                                            <div style="font-weight:800; color:#1e293b; font-size:15px;">{{ $member->name }}</div>
                                            <div style="font-size:12px; color:#64748b; font-weight:600;">ID: {{ $member->member_id ?? '-' }}</div>
                                            <div class="mt-1 d-flex flex-wrap gap-1">
                                                @if($member->division)
                                                    <span class="badge bg-secondary" style="font-size:10px;">{{ $member->division->name }} (Utama)</span>
                                                @endif
                                                @foreach($member->assignedDivisions as $div)
                                                    <span class="badge" style="background:rgba(139,92,246,0.1); color:#7c3aed; border:1px solid rgba(139,92,246,0.2); font-size:10px;">{{ $div->name }} ({{ $div->pivot->jabatan ?? 'Anggota' }})</span>
                                                @endforeach
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div style="font-size:13px; font-weight:600; color:#334155;">{{ $member->email }}</div>
                                </td>
                                <td>
                                    <div style="font-size:13px; color:#475569; display:flex; gap:6px; align-items:center;">
                                        <span>📅</span> {{ $member->tanggal_lahir ? \Carbon\Carbon::parse($member->tanggal_lahir)->format('d M Y') : '-' }}
                                    </div>
                                </td>
                                <td class="text-center">
                                    <span class="badge badge-soft-danger">● {{ $member->status }}</span>
                                </td>
                                <td style="text-align:right; vertical-align:middle; display:flex; justify-content:flex-end; gap:8px;">
                                    @if(isset($interviews[$member->id]))
                                        <button onclick='openInterviewModal(@json($interviews[$member->id]), {{ json_encode($member) }})' class="btn btn-sm btn-info text-white" style="font-weight:700; border-radius:10px; padding:8px 16px;"><i class="bi bi-file-text me-1"></i> Interview</button>
                                    @endif
                                    <button onclick='openEditModal({{ json_encode($member) }})' class="btn btn-sm" style="background:#22c55e; color:white; font-weight:700; border-radius:10px; padding:8px 16px; border:none; box-shadow:0 4px 10px rgba(34,197,94,0.3);">Terima</button>
                                    <button onclick="openDeleteModal('{{ route('admin.members.destroy', $member->id) }}')" class="action-btn btn-delete" title="Tolak Pendaftaran">🗑</button>
                                </td>
                            </tr>
                            @empty
                            <tr><td colspan="5" style="text-align:center; padding:4rem; color:var(--text-secondary);">
                                <div style="font-size:3rem; margin-bottom:1rem;">✅</div>
                                <h4 style="font-weight:700; color:#64748b;">Semua Pendaftar Sudah Diverifikasi</h4>
                                <p>Tidak ada pendaftar baru yang menunggu persetujuan.</p>
                            </td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
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



            <!-- Alamat & Peta -->
            <div class="form-group">
                <label>Lokasi Peta (Otomatis GPS)</label>
                <div style="margin-bottom:12px;">
                    <button type="button" onclick="detectGPS('add')" class="btn btn-primary" style="width:100%; padding:10px; font-weight:700; border-radius:50px;">
                        📍 DETEKSI LOKASI SAYA (GPS)
                    </button>
                </div>

                <div class="coord-row">
                    <div><div style="font-size:11px; color:var(--text-secondary); margin-bottom:4px;">Latitude</div><input type="number" id="add_coord_lat" class="coord-input" step="any"></div>
                    <div><div style="font-size:11px; color:var(--text-secondary); margin-bottom:4px;">Longitude</div><input type="number" id="add_coord_lng" class="coord-input" step="any"></div>
                    <div style="padding-top:18px;"><button type="button" class="btn btn-outline" onclick="goToCoord('add')" style="padding:8px 14px; font-size:13px;">Ke Koordinat</button></div>
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
    <form id="editForm" method="POST" enctype="multipart/form-data">
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
            <div style="display:grid; grid-template-columns:1fr 1fr 1fr; gap:1rem;">
                <div class="form-group"><label>Jabatan</label><input type="text" name="jabatan" id="e_jabatan" class="form-control" required></div>
                <div class="form-group"><label>Divisi (Utama)</label>
                    <select name="division_id" id="e_division_id" class="form-control">
                        <option value="">-- Tidak Ada Divisi --</option>
                        @foreach($divisions as $div)
                            <option value="{{ $div->id }}">{{ $div->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group"><label>Status</label>
                    <select name="status" id="e_status" class="form-control" required>
                        <option value="AKTIF">Telah Diverifikasi (AKTIF)</option>
                        <option value="PENDING">Menunggu ACC (PENDING)</option>
                        <option value="TIDAK AKTIF">Tidak Aktif</option>
                        <option value="NONAKTIF">Nonaktif (Suspend)</option>
                        <option value="BANNED">Diblokir (Banned)</option>
                    </select>
                </div>
            </div>


            <!-- Alamat + Map -->
            <div class="form-group">
                <label>Lokasi Peta (Otomatis GPS)</label>
                <div style="margin-bottom:12px;">
                    <button type="button" onclick="detectGPS('edit')" class="btn btn-primary" style="width:100%; padding:10px; font-weight:700; border-radius:50px;">
                        📍 DETEKSI LOKASI SAYA (GPS)
                    </button>
                </div>

                <div class="coord-row">
                    <div><div style="font-size:11px; color:var(--text-secondary); margin-bottom:4px;">Latitude</div><input type="number" id="edit_coord_lat" class="coord-input" step="any"></div>
                    <div><div style="font-size:11px; color:var(--text-secondary); margin-bottom:4px;">Longitude</div><input type="number" id="edit_coord_lng" class="coord-input" step="any"></div>
                    <div style="padding-top:18px;"><button type="button" class="btn btn-outline" onclick="goToCoord('edit')" style="padding:8px 14px; font-size:13px;">Ke Koordinat</button></div>
                </div>

                <textarea name="alamat" id="e_alamat" class="form-control" rows="2" required></textarea>
                <div id="mapEdit" class="map-container"></div>
            </div>

            <div class="form-group" style="margin-top:20px;">
                <label>Dokumen Pendukung (Opsional - Kosongkan jika tidak ingin mengubah)</label>
                <div class="row">
                    <div class="col-md-6 mb-2">
                        <small>KTP</small>
                        <input type="file" name="ktp" class="form-control" accept=".jpg,.jpeg,.png,.pdf">
                        <div id="e_ktp_link" style="font-size:12px; margin-top:4px;"></div>
                    </div>
                    <div class="col-md-6 mb-2">
                        <small>Kartu Keluarga (KK)</small>
                        <input type="file" name="kk" class="form-control" accept=".jpg,.jpeg,.png,.pdf">
                        <div id="e_kk_link" style="font-size:12px; margin-top:4px;"></div>
                    </div>
                    <div class="col-md-6 mb-2">
                        <small>Ijazah</small>
                        <input type="file" name="ijazah" class="form-control" accept=".jpg,.jpeg,.png,.pdf">
                        <div id="e_ijazah_link" style="font-size:12px; margin-top:4px;"></div>
                    </div>
                    <div class="col-md-6 mb-2">
                        <small>CV</small>
                        <input type="file" name="cv" class="form-control" accept=".pdf">
                        <div id="e_cv_link" style="font-size:12px; margin-top:4px;"></div>
                    </div>
                    <div class="col-md-12 mb-2">
                        <small>Sertifikat / Penghargaan</small>
                        <input type="file" name="sertifikat" class="form-control" accept=".jpg,.jpeg,.png,.pdf">
                        <div id="e_sertifikat_link" style="font-size:12px; margin-top:4px;"></div>
                    </div>
                </div>
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

<!-- ==================== MODAL INTERVIEW ==================== -->
<dialog id="interviewModal" style="max-width:600px; width:90%; border:none; border-radius:24px; padding:0; box-shadow:0 20px 40px rgba(0,0,0,0.2);">
    <div style="background:var(--surface-color); padding:2rem;">
        <h3 style="font-weight:800; color:var(--text-primary); margin-bottom:1.5rem;"><i class="bi bi-person-lines-fill me-2 text-primary"></i> Hasil Interview Anggota</h3>
        
        <div class="mb-3">
            <h6 style="color:var(--text-secondary); font-weight:700;">1. Motivasi Bergabung</h6>
            <div id="int_motivation" style="background:var(--bg-color); padding:1rem; border-radius:12px; border:1px solid var(--border-color); font-size:14px;"></div>
        </div>
        
        <div class="mb-3">
            <h6 style="color:var(--text-secondary); font-weight:700;">2. Keahlian / Hobi</h6>
            <div id="int_skills" style="background:var(--bg-color); padding:1rem; border-radius:12px; border:1px solid var(--border-color); font-size:14px;"></div>
        </div>
        
        <div class="mb-3">
            <h6 style="color:var(--text-secondary); font-weight:700;">3. Pengalaman Organisasi/Kerja</h6>
            <div id="int_experience" style="background:var(--bg-color); padding:1rem; border-radius:12px; border:1px solid var(--border-color); font-size:14px;"></div>
        </div>
        
        <div class="mb-4">
            <h6 style="color:var(--text-secondary); font-weight:700;">4. Harapan Kedepan</h6>
            <div id="int_expectations" style="background:var(--bg-color); padding:1rem; border-radius:12px; border:1px solid var(--border-color); font-size:14px;"></div>
        </div>

        <div class="mb-4">
            <h6 style="color:var(--text-secondary); font-weight:700;"><i class="bi bi-folder-check me-2"></i>Dokumen Pendukung (Jika Ada)</h6>
            <div id="int_documents" style="display:flex; flex-wrap:wrap; gap:10px;"></div>
        </div>

        <div style="display:flex; justify-content:flex-end;">
            <button type="button" class="btn btn-outline-primary" onclick="document.getElementById('interviewModal').close()" style="border-radius:12px; font-weight:600; padding:8px 24px;">Tutup</button>
        </div>
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

function detectGPS(prefix) {
    if (!navigator.geolocation) {
        alert("Browser Anda tidak mendukung GPS.");
        return;
    }
    navigator.geolocation.getCurrentPosition(pos => {
        let lat = pos.coords.latitude;
        let lng = pos.coords.longitude;
        
        let mapObj = prefix === 'add' ? mapAddObj : mapEditObj;
        if (mapObj) {
            mapObj.setView([lat, lng], 17);
            let markerRef = prefix === 'add' ? markerAdd : markerEdit;
            if (markerRef) mapObj.removeLayer(markerRef);
            markerRef = L.marker([lat, lng]).addTo(mapObj);
            if (prefix === 'add') markerAdd = markerRef; else markerEdit = markerRef;
        }

        document.getElementById(prefix + '_coord_lat').value = lat.toFixed(6);
        document.getElementById(prefix + '_coord_lng').value = lng.toFixed(6);
        document.getElementById(prefix === 'add' ? 'add_lat' : 'e_lat').value = lat;
        document.getElementById(prefix === 'add' ? 'add_lng' : 'e_lng').value = lng;
        reverseGeocode(lat, lng, prefix === 'add' ? 'add_alamat' : 'e_alamat');
        
        alert("Lokasi berhasil dideteksi!");
    }, err => {
        alert("Gagal deteksi GPS: " + err.message);
    });
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
    document.getElementById('e_division_id').value = member.division_id || '';
    document.getElementById('e_tanggal_lahir').value = member.tanggal_lahir || '';
    document.getElementById('e_alamat').value = member.alamat || '';
    document.getElementById('e_lat').value = member.latitude || '';
    document.getElementById('e_lng').value = member.longitude || '';
    document.getElementById('e_member_id').value = member.member_id || '';

    const docKeys = ['ktp', 'kk', 'ijazah', 'cv', 'sertifikat'];
    docKeys.forEach(key => {
        let path = member[key + '_path'];
        let linkContainer = document.getElementById('e_' + key + '_link');
        if (path) {
            linkContainer.innerHTML = `<a href="/storage/${path}" target="_blank" style="color:var(--primary); font-weight:600;"><i class="bi bi-download me-1"></i> Lihat ${key.toUpperCase()} Tersimpan</a>`;
        } else {
            linkContainer.innerHTML = '<span class="text-muted">Belum ada file.</span>';
        }
    });


    document.getElementById('editModal').showModal();
    setTimeout(() => {
        initMap('mapEdit', 'e_lat', 'e_lng', false, member.latitude, member.longitude);
        if (member.latitude) document.getElementById('edit_coord_lat').value = parseFloat(member.latitude).toFixed(6);
        if (member.longitude) document.getElementById('edit_coord_lng').value = parseFloat(member.longitude).toFixed(6);
    }, 300);
}

function openDeleteModal(url) {
    document.getElementById('deleteForm').action = url;
    document.getElementById('deleteModal').showModal();
}

function openInterviewModal(interview, member) {
    document.getElementById('int_motivation').innerText = interview.motivation || '-';
    document.getElementById('int_skills').innerText = interview.skills || '-';
    document.getElementById('int_experience').innerText = interview.experience || '-';
    document.getElementById('int_expectations').innerText = interview.expectations || '-';
    
    let docsHtml = '';
    const docs = [
        { key: 'ktp_path', label: 'KTP' },
        { key: 'kk_path', label: 'Kartu Keluarga' },
        { key: 'ijazah_path', label: 'Ijazah' },
        { key: 'cv_path', label: 'CV' },
        { key: 'sertifikat_path', label: 'Sertifikat' }
    ];
    
    docs.forEach(doc => {
        if (member[doc.key]) {
            docsHtml += `<a href="/storage/${member[doc.key]}" target="_blank" class="badge bg-primary" style="text-decoration:none; padding:8px 12px; border-radius:8px;"><i class="bi bi-file-earmark-arrow-down me-1"></i> ${doc.label}</a>`;
        }
    });

    if (docsHtml === '') {
        docsHtml = '<span class="text-muted small">Tidak ada dokumen yang diunggah.</span>';
    }
    document.getElementById('int_documents').innerHTML = docsHtml;

    document.getElementById('interviewModal').showModal();
}
</script>

<script>
document.querySelectorAll('.chat-toggle').forEach(function(toggle) {
    toggle.addEventListener('change', function() {
        let userId = this.getAttribute('data-id');
        let isChecked = this.checked;
        
        fetch(`/admin/members/${userId}/toggle-chat`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Content-Type': 'application/json'
            }
        })
        .then(res => res.json())
        .then(data => {
            if(!data.success) {
                alert('Gagal mengubah status chat');
                this.checked = !isChecked; // revert
            }
        })
        .catch(err => {
            alert('Terjadi kesalahan jaringan');
            this.checked = !isChecked; // revert
        });
    });
});
</script>
@endsection