@extends('layouts.admin')
@section('admin_content')
<link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />
<script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>

<style>
    /* Responsive Table Styles & Status Badges */
    .table-container { overflow-x: auto; margin-top: 1rem; }
    .table-modern { width: 100%; text-align: left; border-collapse: collapse; white-space: nowrap; }
    .table-modern th { border-bottom: 2px solid var(--border-color); padding: 12px 16px; font-weight: 600; color: var(--text-color); }
    .table-modern td { border-bottom: 1px solid var(--border-color); padding: 12px 16px; color: var(--text-secondary); }
    .table-modern tr:hover { background-color: rgba(255, 255, 255, 0.05); }
    
    .badge { display: inline-flex; align-items: center; padding: 4px 12px; border-radius: 50px; font-size: 12px; font-weight: 600; text-transform: uppercase; }
    .badge-aktif { background: rgba(34, 197, 94, 0.1); color: #22c55e; border: 1px solid rgba(34, 197, 94, 0.2); }
    .badge-tidak-aktif { background: rgba(239, 68, 68, 0.1); color: #ef4444; border: 1px solid rgba(239, 68, 68, 0.2); }
    
    .action-btn { display: inline-flex; justify-content: center; align-items: center; padding: 6px 12px; border-radius: 6px; font-size: 13px; font-weight: 500; cursor: pointer; text-decoration: none; border: none; outline: none; margin-right: 5px; }
    .btn-edit { background: rgba(59, 130, 246, 0.1); color: #3b82f6; border: 1px solid rgba(59, 130, 246, 0.2); }
    .btn-edit:hover { background: #3b82f6; color: white; }
    .btn-delete { background: rgba(239, 68, 68, 0.1); color: #ef4444; border: 1px solid rgba(239, 68, 68, 0.2); }
    .btn-delete:hover { background: #ef4444; color: white; }
    .btn-print { background: var(--primary); color: white; border: 1px solid var(--primary); }
    .btn-print:hover { filter: brightness(1.1); color: white; }

    /* Dialog/Modal Styling */
    dialog { border: none; border-radius: 12px; background: var(--surface-color); color: var(--text-color); box-shadow: var(--shadow-lg); padding: 0; max-width: 600px; width: 100%; top: 50%; transform: translateY(-50%); outline: none; border: 1px solid var(--border-color); }
    dialog::backdrop { background: rgba(0, 0, 0, 0.5); backdrop-filter: blur(4px); }
    .modal-header { padding: 1.5rem; border-bottom: 1px solid var(--border-color); display: flex; justify-content: space-between; align-items: center; }
    .modal-header h3 { margin: 0; font-size: 1.25rem; }
    .modal-close { background: none; border: none; color: var(--text-secondary); cursor: pointer; font-size: 1.5rem; }
    .modal-body { padding: 1.5rem; max-height: 70vh; overflow-y: auto; }
    .form-group { margin-bottom: 1rem; text-align: left; }
    .form-group label { display: block; margin-bottom: 0.5rem; font-weight: 500; color: var(--text-secondary); font-size: 14px; }
    .form-control { width: 100%; padding: 10px 14px; border-radius: 8px; border: 1px solid var(--border-color); background: rgba(0,0,0,0.05); color: var(--text-color); font-size: 14px; }
    .form-control:focus { outline: 2px solid var(--primary); }
    .map-container { height: 220px; width: 100%; border-radius: 8px; margin-top: 8px; z-index: 1; }

    /* Coordinate Input */
    .coord-row {
        display: grid; grid-template-columns: 1fr 1fr auto; gap: 8px;
        align-items: center; margin-bottom: 8px;
    }
    .coord-input {
        width: 100%; padding: 8px 12px; border-radius: 8px;
        border: 1px solid var(--border-color); background: rgba(0,0,0,0.05);
        color: var(--text-color); font-size: 13px; font-family: monospace;
        box-sizing: border-box;
    }
    .coord-input:focus { outline: 2px solid var(--primary); }
    .coord-input::placeholder { font-family: sans-serif; color: var(--text-secondary); }
    .coord-go-btn {
        display: inline-flex; align-items: center; gap: 6px;
        padding: 8px 14px; border-radius: 8px; border: none; cursor: pointer;
        background: var(--primary, #6366f1); color: white; font-size: 13px;
        font-weight: 600; white-space: nowrap; transition: filter 0.15s;
    }
    .coord-go-btn:hover { filter: brightness(1.15); }
    .coord-label {
        font-size: 11px; color: var(--text-secondary);
        margin-bottom: 4px; font-weight: 500; text-transform: uppercase; letter-spacing: 0.5px;
    }

    /* Address Search Box */
    .address-search-wrapper { position: relative; margin-bottom: 8px; }
    .address-search-input {
        width: 100%; padding: 10px 14px 10px 40px;
        border-radius: 8px; border: 1px solid var(--border-color);
        background: rgba(0,0,0,0.05); color: var(--text-color);
        font-size: 14px; box-sizing: border-box;
    }
    .address-search-input:focus { outline: 2px solid var(--primary); }
    .address-search-icon {
        position: absolute; left: 12px; top: 50%;
        transform: translateY(-50%); pointer-events: none;
        color: var(--text-secondary);
    }
    .address-search-clear {
        position: absolute; right: 10px; top: 50%;
        transform: translateY(-50%); background: none;
        border: none; cursor: pointer; color: var(--text-secondary);
        font-size: 16px; display: none; padding: 2px 6px;
    }
    .address-search-clear:hover { color: var(--text-color); }
    .address-dropdown {
        position: absolute; top: calc(100% + 4px); left: 0; right: 0;
        background: var(--surface-color); border: 1px solid var(--border-color);
        border-radius: 8px; box-shadow: 0 8px 24px rgba(0,0,0,0.25);
        z-index: 9999; max-height: 220px; overflow-y: auto;
        display: none;
    }
    .address-dropdown-item {
        padding: 10px 14px; cursor: pointer; font-size: 13px;
        color: var(--text-color); border-bottom: 1px solid var(--border-color);
        display: flex; align-items: flex-start; gap: 10px;
        transition: background 0.15s;
    }
    .address-dropdown-item:last-child { border-bottom: none; }
    .address-dropdown-item:hover { background: rgba(var(--primary-rgb, 99,102,241), 0.12); }
    .address-dropdown-item svg { flex-shrink: 0; margin-top: 2px; }
    .address-dropdown-item .addr-main { font-weight: 600; font-size: 13px; }
    .address-dropdown-item .addr-sub { font-size: 11px; color: var(--text-secondary); margin-top: 2px; }
    .address-search-loading { padding: 12px 14px; font-size: 13px; color: var(--text-secondary); text-align: center; }
    .address-search-empty { padding: 12px 14px; font-size: 13px; color: var(--text-secondary); text-align: center; }
</style>

<div class="animate-fade-in">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem; flex-wrap: wrap; gap: 1rem;">
        <h2 style="margin: 0;">Manajemen Anggota</h2>
        <div style="display: flex; gap: 10px; align-items: center; flex-wrap: wrap;">
            <form action="{{ route('admin.members') }}" method="GET" style="display: flex; align-items: center;">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama, email, ID..." required style="padding: 0.6rem 1rem; border: 1px solid var(--border-color); border-radius: 20px 0 0 20px; background: rgba(0,0,0,0.05); color: var(--text-color); outline: none;">
                <button type="submit" style="padding: 0.6rem 1.2rem; background: var(--primary); color: white; border: none; border-radius: 0 20px 20px 0; cursor: pointer; font-weight: 600;">Cari</button>
                @if(request('search'))
                    <a href="{{ route('admin.members') }}" style="margin-left: 10px; font-size: 13px; color: #ef4444;">Reset</a>
                @endif
            </form>
            <a href="{{ route('admin.members.export') }}" class="btn" style="background: rgba(16, 185, 129, 0.1); color: #10b981; border: 1px solid rgba(16,185,129,0.3);">
                <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" style="margin-right: 5px;"><path stroke-linecap="round" stroke-linejoin="round" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                Export .CSV
            </a>
            <button onclick="openAddModal()" class="btn btn-primary" style="display: inline-flex; align-items: center; gap: 8px;">
                <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"></path></svg>
                Tambah
            </button>
        </div>
    </div>

    @if(session('success'))
        <div style="background: rgba(34, 197, 94, 0.1); color: #22c55e; border: 1px solid rgba(34, 197, 94, 0.2); padding: 1rem; border-radius: 8px; margin-bottom: 1.5rem;">
            {{ session('success') }}
        </div>
    @endif
    @if($errors->any())
        <div style="background: rgba(239, 68, 68, 0.1); color: #ef4444; border: 1px solid rgba(239, 68, 68, 0.2); padding: 1rem; border-radius: 8px; margin-bottom: 1.5rem;">
            <ul style="margin: 0; padding-left: 20px;">
                @foreach($errors->all() as $error) <li>{{ $error }}</li> @endforeach
            </ul>
        </div>
    @endif

    <div class="card glass p-0" style="overflow: hidden;">
        <div class="table-container p-4">
            <table class="table-modern">
                <thead>
                    <tr>
                        <th>ID Member</th>
                        <th>Kredensial</th>
                        <th>Biodata</th>
                        <th>Status</th>
                        <th>Chat</th>
                        <th style="text-align: right;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($members as $member)
                        <tr>
                            <td style="font-weight: 500; color: var(--primary);">{{ $member->member_id ?? '-' }}</td>
                            <td>
                                <div style="font-weight: 600; color: var(--text-color);">{{ $member->name }}</div>
                                <div style="font-size: 12px; margin-bottom: 4px;">{{ $member->email }}</div>
                                <span style="font-size: 11px; padding: 2px 6px; background: rgba(255,255,255,0.1); border-radius: 4px;">{{ $member->jabatan ?? 'Anggota' }}</span>
                            </td>
                            <td>
                                <div style="font-size: 13px;">TTL: {{ $member->tanggal_lahir ? \Carbon\Carbon::parse($member->tanggal_lahir)->format('d M Y') : '-' }}</div>
                                <div style="font-size: 12px; color: var(--text-secondary); max-width: 150px; overflow: hidden; text-overflow: ellipsis;">{{ $member->alamat ?? 'Belum ada alamat' }}</div>
                            </td>
                            <td>
                                @if(($member->status ?? 'AKTIF') === 'AKTIF')
                                    <span class="badge badge-aktif">Aktif</span>
                                @else
                                    <span class="badge badge-tidak-aktif">Tidak Aktif</span>
                                @endif
                            </td>
                            <td>
                                @if($member->can_chat ?? true)
                                    <span class="badge bg-success" style="background: rgba(34, 197, 94, 0.1); color: #22c55e; border: 1px solid rgba(34, 197, 94, 0.2);">ON</span>
                                @else
                                    <span class="badge bg-danger" style="background: rgba(239, 68, 68, 0.1); color: #ef4444; border: 1px solid rgba(239, 68, 68, 0.2);">OFF</span>
                                @endif
                            </td>
                            <td style="text-align: right;">
                                <a href="{{ route('admin.card_preview', $member->id) }}" class="action-btn btn-print" title="Cetak Kartu">
                                    <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M6 9V2h12v7M6 18H4a2 2 0 01-2-2v-5a2 2 0 012-2h16a2 2 0 012 2v5a2 2 0 01-2 2h-2M6 18v4h12v-4M6 18h12"></path></svg>
                                </a>
                                <button onclick="openEditModal({{ json_encode($member) }})" class="action-btn btn-edit" title="Edit Profil">
                                    <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                </button>
                                <button onclick="openDeleteModal('{{ route('admin.members.destroy', $member->id) }}')" class="action-btn btn-delete" title="Hapus Akun">
                                    <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" style="text-align: center; padding: 3rem;">Belum ada anggota yang terdaftar.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal Tambah -->
<dialog id="addModal">
    <div class="modal-header">
        <h3>Tambah Anggota Baru</h3>
        <button class="modal-close" onclick="document.getElementById('addModal').close()">&times;</button>
    </div>
    <form action="{{ route('admin.members.store') }}" method="POST">
        @csrf
        <input type="hidden" name="role" value="member">
        <input type="hidden" name="latitude" id="add_lat" value="">
        <input type="hidden" name="longitude" id="add_lng" value="">

        <div class="modal-body">
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                <div class="form-group">
                    <label>Nama Lengkap</label>
                    <input type="text" name="name" class="form-control" required placeholder="Contoh: Ryan D.V., S.Kom">
                </div>
                <div class="form-group">
                    <label>Email Akses</label>
                    <input type="email" name="email" class="form-control" required placeholder="member@domain.com">
                </div>
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                <div class="form-group">
                    <label>Kata Sandi</label>
                    <input type="password" name="password" class="form-control" required>
                </div>
                <div class="form-group">
                    <label>Tanggal Lahir</label>
                    <input type="date" name="tanggal_lahir" class="form-control" required>
                </div>
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                <div class="form-group">
                    <label>Posisi / Jabatan</label>
                    <input type="text" name="jabatan" class="form-control" required value="Anggota">
                </div>
                <div class="form-group">
                    <label>Status Akun</label>
                    <select name="status" class="form-control" required>
                        <option value="AKTIF">Aktif</option>
                        <option value="TIDAK AKTIF">Tidak Aktif</option>
                    </select>
                </div>
            </div>

            <div class="form-group">
                <label>Alamat &amp; Lokasi Peta</label>
                
                <!-- Google Maps Link Parser -->
                <div style="background: rgba(var(--primary-rgb, 99, 102, 241), 0.05); border: 1px solid rgba(var(--primary-rgb, 99, 102, 241), 0.2); border-radius: 8px; padding: 12px; margin-bottom: 12px;">
                    <label style="font-size: 12px; font-weight: 600; color: var(--primary); margin-bottom: 6px; display: block;">Tarik Otomatis dari Link Google Maps (Paling Akurat & Mudah)</label>
                    <div style="display: flex; gap: 8px;">
                        <input type="text" id="addGmapsLink" class="form-control" placeholder="Tempel link (contoh: https://maps.app.goo.gl/...)" style="font-size: 13px;">
                        <button type="button" id="btn-parse-add" onclick="parseGmapsLink('add')" class="btn btn-primary" style="white-space: nowrap; font-size: 12px; padding: 0 12px;">Tarik Lokasi</button>
                    </div>
                </div>

                <div class="address-search-wrapper" id="addSearchWrapper">
                    <svg class="address-search-icon" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35"/></svg>
                    <input type="text" id="addSearchInput" class="address-search-input" placeholder="Cari nama tempat atau alamat..." autocomplete="off">
                    <button type="button" class="address-search-clear" id="addSearchClear" onclick="clearSearch('add')">&#10005;</button>
                    <div class="address-dropdown" id="addDropdown"></div>
                </div>
                <!-- Direct Coordinate Input -->
                <div class="coord-row">
                    <div>
                        <div class="coord-label">Latitude</div>
                        <input type="number" id="add_coord_lat" class="coord-input" step="any" placeholder="-6.200000" oninput="syncCoordDisplay('add')" onkeydown="if(event.key==='Enter'){event.preventDefault();goToCoord('add');}">
                    </div>
                    <div>
                        <div class="coord-label">Longitude</div>
                        <input type="number" id="add_coord_lng" class="coord-input" step="any" placeholder="106.816666" oninput="syncCoordDisplay('add')" onkeydown="if(event.key==='Enter'){event.preventDefault();goToCoord('add');}">
                    </div>
                    <div style="padding-top: 18px;">
                        <button type="button" class="coord-go-btn" onclick="goToCoord('add')">
                            <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7zm0 9.5a2.5 2.5 0 110-5 2.5 2.5 0 010 5z"/></svg>
                            Ke Koordinat
                        </button>
                    </div>
                </div>
                <textarea name="alamat" id="add_alamat" class="form-control" rows="2" required placeholder="Tulis jalan, kelurahan..."></textarea>
                <div id="mapAdd" class="map-container"></div>
                <small style="color: var(--text-secondary); display: block; margin-top: 5px;">💡 Cari nama tempat, tempel koordinat, atau klik langsung pada map.</small>
            </div>

            <div class="form-group" style="margin-bottom: 2rem;">
                <label>ID Member (Opsional)</label>
                <input type="text" name="member_id" class="form-control" placeholder="Kosongkan untuk Generate QR ID Terenkripsi otomatis">
            </div>

            <div style="display: flex; justify-content: flex-end; gap: 10px;">
                <button type="button" class="btn btn-outline" onclick="document.getElementById('addModal').close()">Batal</button>
                <button type="submit" class="btn btn-primary">Simpan Pendaftar</button>
            </div>
        </div>
    </form>
</dialog>

<!-- Modal Edit -->
<dialog id="editModal">
    <div class="modal-header">
        <h3>Edit Profil Anggota</h3>
        <button class="modal-close" onclick="document.getElementById('editModal').close()">&times;</button>
    </div>
    <form id="editForm" method="POST">
        @csrf
        @method('PUT')
        <input type="hidden" name="role" id="e_role">
        <input type="hidden" name="latitude" id="e_lat">
        <input type="hidden" name="longitude" id="e_lng">

        <div class="modal-body">
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                <div class="form-group">
                    <label>Nama Lengkap</label>
                    <input type="text" name="name" id="e_name" class="form-control" required>
                </div>
                <div class="form-group">
                    <label>Email Akses</label>
                    <input type="email" name="email" id="e_email" class="form-control" required>
                </div>
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                <div class="form-group">
                    <label>Kata Sandi Baru</label>
                    <input type="password" name="password" class="form-control" placeholder="(Kosongkan jika sama)">
                </div>
                <div class="form-group">
                    <label>Tanggal Lahir</label>
                    <input type="date" name="tanggal_lahir" id="e_tanggal_lahir" class="form-control" required>
                </div>
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                <div class="form-group">
                    <label>Posisi / Jabatan</label>
                    <input type="text" name="jabatan" id="e_jabatan" class="form-control" required>
                </div>
                    <div class="form-group">
                        <label>Status Akun</label>
                        <select name="status" id="e_status" class="form-control" required>
                            <option value="AKTIF">Aktif</option>
                            <option value="TIDAK AKTIF">Tidak Aktif</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Izin Komunikasi (Chat/Call)</label>
                        <select name="can_chat" id="e_can_chat" class="form-control" required>
                            <option value="1">Izinkan</option>
                            <option value="0">Blokir</option>
                        </select>
                    </div>
                </div>

            <div class="form-group">
                <label>Alamat &amp; Lokasi Peta</label>
                
                <!-- Google Maps Link Parser -->
                <div style="background: rgba(var(--primary-rgb, 99, 102, 241), 0.05); border: 1px solid rgba(var(--primary-rgb, 99, 102, 241), 0.2); border-radius: 8px; padding: 12px; margin-bottom: 12px;">
                    <label style="font-size: 12px; font-weight: 600; color: var(--primary); margin-bottom: 6px; display: block;">Tarik Otomatis dari Link Google Maps (Paling Akurat & Mudah)</label>
                    <div style="display: flex; gap: 8px;">
                        <input type="text" id="editGmapsLink" class="form-control" placeholder="Tempel link (contoh: https://maps.app.goo.gl/...)" style="font-size: 13px;">
                        <button type="button" id="btn-parse-edit" onclick="parseGmapsLink('edit')" class="btn btn-primary" style="white-space: nowrap; font-size: 12px; padding: 0 12px;">Tarik Lokasi</button>
                    </div>
                </div>

                <div class="address-search-wrapper" id="editSearchWrapper">
                    <svg class="address-search-icon" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35"/></svg>
                    <input type="text" id="editSearchInput" class="address-search-input" placeholder="Cari nama tempat atau alamat..." autocomplete="off">
                    <button type="button" class="address-search-clear" id="editSearchClear" onclick="clearSearch('edit')">&#10005;</button>
                    <div class="address-dropdown" id="editDropdown"></div>
                </div>
                <!-- Direct Coordinate Input -->
                <div class="coord-row">
                    <div>
                        <div class="coord-label">Latitude</div>
                        <input type="number" id="edit_coord_lat" class="coord-input" step="any" placeholder="-6.200000" oninput="syncCoordDisplay('edit')" onkeydown="if(event.key==='Enter'){event.preventDefault();goToCoord('edit');}">
                    </div>
                    <div>
                        <div class="coord-label">Longitude</div>
                        <input type="number" id="edit_coord_lng" class="coord-input" step="any" placeholder="106.816666" oninput="syncCoordDisplay('edit')" onkeydown="if(event.key==='Enter'){event.preventDefault();goToCoord('edit');}">
                    </div>
                    <div style="padding-top: 18px;">
                        <button type="button" class="coord-go-btn" onclick="goToCoord('edit')">
                            <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7zm0 9.5a2.5 2.5 0 110-5 2.5 2.5 0 010 5z"/></svg>
                            Ke Koordinat
                        </button>
                    </div>
                </div>
                <textarea name="alamat" id="e_alamat" class="form-control" rows="2" required></textarea>
                <div id="mapEdit" class="map-container"></div>
                <small style="color: var(--text-secondary); display: block; margin-top: 5px;">💡 Cari nama tempat, tempel koordinat, atau klik ulang map untuk mengubah titik.</small>
            </div>

            <div class="form-group" style="margin-bottom: 2rem;">
                <label>ID Member QR Code</label>
                <input type="text" name="member_id" id="e_member_id" class="form-control" readonly style="opacity: 0.7;">
                <small style="color: var(--primary);">ID Terenkripsi tidak boleh diubah setelah pembuatan awal.</small>
            </div>

            <div style="display: flex; justify-content: flex-end; gap: 10px;">
                <button type="button" class="btn btn-outline" onclick="document.getElementById('editModal').close()">Batal</button>
                <button type="submit" class="btn btn-primary">Update Data</button>
            </div>
        </div>
    </form>
</dialog>

<!-- Modal Hapus -->
<dialog id="deleteModal" style="max-width: 400px; text-align: center;">
    <div class="modal-body" style="padding: 2rem;">
        <svg width="48" height="48" fill="none" stroke="#ef4444" stroke-width="2" viewBox="0 0 24 24" style="margin: 0 auto 1rem;"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
        <h3 style="margin-bottom: 1rem;">Konfirmasi Hapus</h3>
        <p style="color: var(--text-secondary); margin-bottom: 2rem; font-size: 14px;">Apakah Anda yakin ingin menghapus data keanggotaan beserta kartu dan rekam jejak ini?</p>
        
        <form id="deleteForm" method="POST" style="display: flex; justify-content: center; gap: 10px;">
            @csrf
            @method('DELETE')
            <button type="button" class="btn btn-outline" onclick="document.getElementById('deleteModal').close()">Batal</button>
            <button type="submit" class="btn" style="background: #ef4444; color: white; border: none;">Ya, Hapus!</button>
        </form>
    </div>
</dialog>

<script>
    let mapAddObj = null;
    let mapEditObj = null;
    let markerAdd = null;
    let markerEdit = null;
    let searchTimers = {};

    // Default Map center (Indonesia)
    const defaultCenter = [-6.2088, 106.8456];

    /* ─── Map Init ─── */
    function initMap(mapId, latInputId, lngInputId, isAdd, preLat, preLng) {
        let mapRef = isAdd ? mapAddObj : mapEditObj;
        let markerRef = isAdd ? markerAdd : markerEdit;

        if (mapRef) { mapRef.remove(); }

        let center = (preLat && preLng) ? [parseFloat(preLat), parseFloat(preLng)] : defaultCenter;
        mapRef = L.map(mapId).setView(center, preLat ? 15 : 13);

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a>'
        }).addTo(mapRef);

        if (preLat && preLng) {
            markerRef = L.marker(center).addTo(mapRef);
            document.getElementById(latInputId).value = preLat;
            document.getElementById(lngInputId).value = preLng;
        }

        mapRef.on('click', function(e) {
            let lat = e.latlng.lat;
            let lng = e.latlng.lng;
            if (markerRef) mapRef.removeLayer(markerRef);
            markerRef = L.marker([lat, lng]).addTo(mapRef);
            document.getElementById(latInputId).value = lat;
            document.getElementById(lngInputId).value = lng;
            // Also sync coord display inputs
            if (isAdd) {
                document.getElementById('add_coord_lat').value = lat.toFixed(6);
                document.getElementById('add_coord_lng').value = lng.toFixed(6);
            } else {
                document.getElementById('edit_coord_lat').value = lat.toFixed(6);
                document.getElementById('edit_coord_lng').value = lng.toFixed(6);
            }
            // Reverse geocode on click to fill address
            reverseGeocode(lat, lng, isAdd ? 'add_alamat' : 'e_alamat');
            if (isAdd) { mapAddObj = mapRef; markerAdd = markerRef; }
            else { mapEditObj = mapRef; markerEdit = markerRef; }
        });

        if (isAdd) { mapAddObj = mapRef; markerAdd = markerRef; }
        else { mapEditObj = mapRef; markerEdit = markerRef; }
    }

    /* ─── Reverse Geocode (click on map → fill textarea) ─── */
    function reverseGeocode(lat, lng, textareaId) {
        fetch(`https://nominatim.openstreetmap.org/reverse?format=jsonv2&lat=${lat}&lon=${lng}&accept-language=id`)
            .then(r => r.json())
            .then(data => {
                if (data && data.display_name) {
                    document.getElementById(textareaId).value = data.display_name;
                }
            }).catch(() => {});
    }

    /* ─── Search Geocode ─── */
    function setupAddressSearch(prefix, latInputId, lngInputId, alamatId) {
        const inputEl = document.getElementById(prefix + 'SearchInput');
        const dropEl  = document.getElementById(prefix + 'Dropdown');
        const clearEl = document.getElementById(prefix + 'SearchClear');
        const isAdd   = (prefix === 'add');

        inputEl.addEventListener('input', function() {
            const q = this.value.trim();
            clearEl.style.display = q ? 'block' : 'none';
            if (!q) { dropEl.style.display = 'none'; return; }
            clearTimeout(searchTimers[prefix]);
            searchTimers[prefix] = setTimeout(() => geocodeSearch(q, dropEl, prefix, latInputId, lngInputId, alamatId, isAdd), 400);
        });

        // Close dropdown when clicking outside
        document.addEventListener('click', function(e) {
            if (!inputEl.contains(e.target) && !dropEl.contains(e.target)) {
                dropEl.style.display = 'none';
            }
        });
    }

    function geocodeSearch(query, dropEl, prefix, latInputId, lngInputId, alamatId, isAdd) {
        dropEl.innerHTML = '<div class="address-search-loading">🔍 Mencari...</div>';
        dropEl.style.display = 'block';

        fetch(`https://nominatim.openstreetmap.org/search?format=jsonv2&q=${encodeURIComponent(query)}&limit=6&accept-language=id&addressdetails=1`)
            .then(r => r.json())
            .then(results => {
                if (!results.length) {
                    dropEl.innerHTML = '<div class="address-search-empty">Lokasi tidak ditemukan</div>';
                    return;
                }
                dropEl.innerHTML = '';
                results.forEach(item => {
                    const parts = item.display_name.split(', ');
                    const main  = parts.slice(0, 2).join(', ');
                    const sub   = parts.slice(2).join(', ');
                    const el    = document.createElement('div');
                    el.className = 'address-dropdown-item';
                    el.innerHTML = `
                        <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7zm0 9.5a2.5 2.5 0 110-5 2.5 2.5 0 010 5z"/></svg>
                        <div>
                            <div class="addr-main">${main}</div>
                            <div class="addr-sub">${sub}</div>
                        </div>`;
                    el.addEventListener('click', () => {
                        const lat = parseFloat(item.lat);
                        const lng = parseFloat(item.lon);
                        const fullAddr = item.display_name;

                        // Pan & place marker
                        let mapObj = isAdd ? mapAddObj : mapEditObj;
                        let markerRef = isAdd ? markerAdd : markerEdit;
                        if (mapObj) {
                            mapObj.flyTo([lat, lng], 16, { animate: true, duration: 1 });
                            if (markerRef) mapObj.removeLayer(markerRef);
                            markerRef = L.marker([lat, lng]).addTo(mapObj);
                            if (isAdd) markerAdd = markerRef;
                            else markerEdit = markerRef;
                        }

                        // Fill fields
                        document.getElementById(latInputId).value = lat;
                        document.getElementById(lngInputId).value = lng;
                        document.getElementById(alamatId).value = fullAddr;
                        document.getElementById(prefix + 'SearchInput').value = main;
                        document.getElementById(prefix + 'SearchClear').style.display = 'block';
                        dropEl.style.display = 'none';
                    });
                    dropEl.appendChild(el);
                });
            })
            .catch(() => {
                dropEl.innerHTML = '<div class="address-search-empty">Gagal menghubungi server pencarian.</div>';
            });
    }

    function clearSearch(prefix) {
        document.getElementById(prefix + 'SearchInput').value = '';
        document.getElementById(prefix + 'SearchClear').style.display = 'none';
        document.getElementById(prefix + 'Dropdown').style.display = 'none';
    }

    /* ─── Direct Coordinate Input ─── */
    function goToCoord(prefix) {
        const isAdd = (prefix === 'add');
        const latEl = document.getElementById(prefix + '_coord_lat');
        const lngEl = document.getElementById(prefix + '_coord_lng');
        const lat   = parseFloat(latEl.value);
        const lng   = parseFloat(lngEl.value);

        if (isNaN(lat) || isNaN(lng)) {
            latEl.style.outline = '2px solid #ef4444';
            lngEl.style.outline = '2px solid #ef4444';
            setTimeout(() => { latEl.style.outline = ''; lngEl.style.outline = ''; }, 1500);
            return;
        }
        if (lat < -90 || lat > 90 || lng < -180 || lng > 180) {
            alert('Koordinat di luar rentang valid. Latitude: -90 s/d 90, Longitude: -180 s/d 180');
            return;
        }

        const mapObj    = isAdd ? mapAddObj : mapEditObj;
        const latHidId  = isAdd ? 'add_lat' : 'e_lat';
        const lngHidId  = isAdd ? 'add_lng' : 'e_lng';
        const alamatId  = isAdd ? 'add_alamat' : 'e_alamat';

        if (!mapObj) return;

        // Fly to & place marker
        mapObj.flyTo([lat, lng], 17, { animate: true, duration: 1.2 });
        let markerRef = isAdd ? markerAdd : markerEdit;
        if (markerRef) mapObj.removeLayer(markerRef);
        markerRef = L.marker([lat, lng]).addTo(mapObj);
        if (isAdd) markerAdd = markerRef; else markerEdit = markerRef;

        // Update hidden fields
        document.getElementById(latHidId).value = lat;
        document.getElementById(lngHidId).value = lng;

        // Reverse geocode to fill address textarea
        reverseGeocode(lat, lng, alamatId);
    }

    // Keep coord display inputs in sync when called from search/click results
    function syncCoordDisplay(prefix) {
        // Nothing extra needed here – inputs are already the source of truth for goToCoord
        // But also clear red outline if user is re-editing
        document.getElementById(prefix + '_coord_lat').style.outline = '';
        document.getElementById(prefix + '_coord_lng').style.outline = '';
    }

    /* ─── Modal Openers ─── */
    function openAddModal() {
        document.getElementById('addModal').showModal();
        setTimeout(() => {
            initMap('mapAdd', 'add_lat', 'add_lng', true);
            setupAddressSearch('add', 'add_lat', 'add_lng', 'add_alamat');
        }, 300);
    }

    function openEditModal(member) {
        document.getElementById('editForm').action = "/admin/members/" + member.id;
        document.getElementById('e_name').value = member.name;
        document.getElementById('e_email').value = member.email;
        document.getElementById('e_jabatan').value = member.jabatan || 'Anggota';
        document.getElementById('e_status').value = member.status || 'AKTIF';
        document.getElementById('e_can_chat').value = (member.can_chat === undefined || member.can_chat) ? "1" : "0";
        document.getElementById('e_member_id').value = member.member_id || '';
        document.getElementById('e_role').value = member.role || 'member';
        document.getElementById('e_tanggal_lahir').value = member.tanggal_lahir || '';
        document.getElementById('e_alamat').value = member.alamat || '';
        document.getElementById('e_lat').value = member.latitude || '';
        document.getElementById('e_lng').value = member.longitude || '';

        // Pre-fill search box with existing address
        if (member.alamat) {
            const parts = member.alamat.split(', ');
            document.getElementById('editSearchInput').value = parts.slice(0, 2).join(', ');
            document.getElementById('editSearchClear').style.display = 'block';
        } else {
            document.getElementById('editSearchInput').value = '';
            document.getElementById('editSearchClear').style.display = 'none';
        }

        document.getElementById('editModal').showModal();
        setTimeout(() => {
            initMap('mapEdit', 'e_lat', 'e_lng', false, member.latitude, member.longitude);
            setupAddressSearch('edit', 'e_lat', 'e_lng', 'e_alamat');
            // Pre-fill coord inputs
            if (member.latitude) document.getElementById('edit_coord_lat').value = parseFloat(member.latitude).toFixed(6);
            if (member.longitude) document.getElementById('edit_coord_lng').value = parseFloat(member.longitude).toFixed(6);
        }, 300);
    }

    function openDeleteModal(actionUrl) {
        document.getElementById('deleteForm').action = actionUrl;
        document.getElementById('deleteModal').showModal();
    }

    function parseGmapsLink(prefix) {
        let inputEl = document.getElementById(prefix + 'GmapsLink');
        let url = inputEl.value.trim();
        if (!url) {
            alert('Silakan tempel (paste) link Google Maps terlebih dahulu.');
            return;
        }

        let btn = document.getElementById('btn-parse-' + prefix);
        let originalText = btn.innerHTML;
        btn.innerHTML = 'Mengekstrak...';
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
                document.getElementById(prefix + '_coord_lat').value = data.lat;
                document.getElementById(prefix + '_coord_lng').value = data.lng;
                goToCoord(prefix); // This will update map, marker, and reverse geocode address
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
    }
</script>
@endsection