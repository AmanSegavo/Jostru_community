@extends('layouts.admin')
@section('admin_content')

<div class="animate-fade-in">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 style="font-weight:800;">Manajemen Setoran Limbah</h2>
<div class="mt-2 mb-3"><a href="{{ route('admin.waste_deposits.export') }}" class="btn hover-lift" style="background:rgba(34,197,94,0.1); color:var(--primary-accent); border:1px solid rgba(34,197,94,0.3); padding:0.5rem 1rem; border-radius:12px; font-size:14px; text-decoration:none; display:inline-flex; align-items:center; gap:5px; font-weight:600;">
    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path><polyline points="7 10 12 15 17 10"></polyline><line x1="12" y1="15" x2="12" y2="3"></line></svg>
    Export CSV
</a></div>

            <p class="text-muted mb-0">Kelola laporan setoran dari anggota.</p>
        </div>
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addWasteDepositAdminModal" style="border-radius:12px; font-weight:600;">
            <i class="bi bi-plus-circle me-1"></i> Tambah Data (Oleh Admin)
        </button>
    </div>
    <!-- Analytics Cards -->
    <div class="row g-3 mb-4">
        <div class="col-md-4">
            <div class="card glass p-3 border-0 shadow-sm text-center" style="border-radius:16px;">
                <h6 class="text-muted fw-bold mb-2">Total Disetujui</h6>
                <h3 class="fw-bold mb-0 text-success">{{ $totalWeight }} <small class="text-muted" style="font-size:1rem;">Kg</small></h3>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card glass p-3 border-0 shadow-sm text-center" style="border-radius:16px;">
                <h6 class="text-muted fw-bold mb-2">Menunggu Persetujuan</h6>
                <h3 class="fw-bold mb-0 text-warning">{{ $pendingCount }} <small class="text-muted" style="font-size:1rem;">Data</small></h3>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card glass p-3 border-0 shadow-sm text-center" style="border-radius:16px;">
                <h6 class="text-muted fw-bold mb-2">Setoran Tanpa Bukti</h6>
                <h3 class="fw-bold mb-0 text-danger">{{ $withoutProofCount }} <small class="text-muted" style="font-size:1rem;">Data</small></h3>
            </div>
        </div>
    </div>

    <!-- Filter Tabs -->
    <div class="d-flex flex-wrap gap-2 mb-3">
        <a href="{{ route('admin.waste_deposits', ['filter' => 'all']) }}" class="btn btn-sm rounded-pill px-3 fw-bold {{ $filter == 'all' ? 'btn-primary' : 'btn-outline-secondary' }}">Semua</a>
        <a href="{{ route('admin.waste_deposits', ['filter' => 'pending']) }}" class="btn btn-sm rounded-pill px-3 fw-bold {{ $filter == 'pending' ? 'btn-warning text-dark' : 'btn-outline-warning text-dark' }}">Pending</a>
        <a href="{{ route('admin.waste_deposits', ['filter' => 'approved']) }}" class="btn btn-sm rounded-pill px-3 fw-bold {{ $filter == 'approved' ? 'btn-success' : 'btn-outline-success' }}">Disetujui</a>
        <a href="{{ route('admin.waste_deposits', ['filter' => 'rejected']) }}" class="btn btn-sm rounded-pill px-3 fw-bold {{ $filter == 'rejected' ? 'btn-danger' : 'btn-outline-danger' }}">Ditolak</a>
        <a href="{{ route('admin.waste_deposits', ['filter' => 'with_proof']) }}" class="btn btn-sm rounded-pill px-3 fw-bold {{ $filter == 'with_proof' ? 'btn-info text-white' : 'btn-outline-info' }}">Ada Bukti</a>
        <a href="{{ route('admin.waste_deposits', ['filter' => 'without_proof']) }}" class="btn btn-sm rounded-pill px-3 fw-bold {{ $filter == 'without_proof' ? 'btn-dark' : 'btn-outline-dark' }}">Tanpa Bukti</a>
    </div>

    <!-- LEAFLET CSS -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin=""/>



    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show mb-4" style="border-radius:12px;background:rgba(34,197,94,0.1);color:#22c55e;border:1px solid rgba(34,197,94,0.2);">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="card glass p-0 overflow-hidden" style="border-radius:20px;">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead style="background:rgba(var(--primary-rgb),0.05);">
                    <tr>
                        <th class="px-4 py-3">Tanggal</th>
                        <th class="px-4 py-3">Anggota</th>
                        <th class="px-4 py-3">Jenis</th>
                        <th class="px-4 py-3">Berat</th>
                        <th class="px-4 py-3">Foto</th>
                        <th class="px-4 py-3">Status</th>
                        <th class="px-4 py-3 text-end">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($deposits as $deposit)
                    <tr>
                        <td class="px-4 py-3">{{ $deposit->created_at->format('d M Y H:i') }}</td>
                        <td class="px-4 py-3">
                            <div style="font-weight:600;">{{ $deposit->user->name ?? 'Anonim' }}</div>
                            <small class="text-muted">{{ $deposit->user->member_id ?? '' }}</small>
                        </td>
                        <td class="px-4 py-3">
                            <span class="badge" style="background:rgba(var(--primary-rgb),0.1);color:var(--primary);">
                                {{ $deposit->type }}
                            </span>
                        </td>
                        <td class="px-4 py-3 font-weight-bold">{{ $deposit->weight }} kg</td>
                        <td class="px-4 py-3">
                            @if($deposit->media_path)
                                <a href="{{ asset('public/storage/' . $deposit->media_path) }}" target="_blank">
                                    <img src="{{ asset('public/storage/' . $deposit->media_path) }}" style="width:50px;height:50px;object-fit:cover;border-radius:8px;">
                                </a>
                            @else
                                <span class="text-muted">-</span>
                            @endif
                        </td>
                        <td class="px-4 py-3">
                            @if($deposit->status == 'APPROVED')
                                <span class="badge bg-success">DISETUJUI</span>
                            @elseif($deposit->status == 'REJECTED')
                                <span class="badge bg-danger">DITOLAK</span>
                            @else
                                <span class="badge bg-warning text-dark">PENDING</span>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-end">
                            @if($deposit->status == 'PENDING')
                                <form action="{{ route('admin.waste_deposits.status', $deposit->id) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('PUT')
                                    <input type="hidden" name="status" value="APPROVED">
                                    <button type="submit" class="btn btn-sm btn-success" style="border-radius:8px;">Setujui</button>
                                </form>
                                <form action="{{ route('admin.waste_deposits.status', $deposit->id) }}" method="POST" class="d-inline ms-1">
                                    @csrf
                                    @method('PUT')
                                    <input type="hidden" name="status" value="REJECTED">
                                    <button type="submit" class="btn btn-sm btn-danger" style="border-radius:8px;">Tolak</button>
                                </form>
                            @else
                                <span class="text-muted small">Selesai</span>
                            @endif

                            <button type="button" class="btn btn-sm btn-outline-primary ms-1" style="border-radius:8px;" onclick="editDeposit({{ $deposit->id }}, {{ $deposit->weight }}, '{{ $deposit->description }}')">
                                Edit
                            </button>
                            
                            <form action="{{ route('admin.waste_deposits.destroy', $deposit->id) }}" method="POST" class="d-inline ms-1" onsubmit="return confirm('Apakah Anda yakin ingin menghapus data limbah ini? (Poin yang sudah diberikan akan ditarik kembali)')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-outline-danger" style="border-radius:8px;">Hapus</button>
                            </form>

                            @if($deposit->latitude && $deposit->longitude)
                                <button type="button" class="btn btn-sm btn-info ms-1" style="border-radius:8px; color:white;" onclick="showLocationMap({{ $deposit->latitude }}, {{ $deposit->longitude }}, '{{ $deposit->user->name ?? 'Anonim' }}')">
                                    📍 Lokasi
                                </button>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center py-5">
                            <div style="font-size: 3rem; margin-bottom: 1rem; opacity: 0.5;">📭</div>
                            <h5 style="font-weight: 700; color: var(--text-secondary);">Belum ada laporan setoran</h5>
                            <p class="text-muted small mb-0">Laporan yang disubmit anggota akan muncul di sini.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-3">
        {{ $deposits->links() }}
    </div>
</div>

<!-- Modal Tambah Limbah oleh Admin -->
<div class="modal fade" id="addWasteDepositAdminModal" tabindex="-1">
    <div class="modal-dialog">
        <form action="{{ route('admin.waste_deposits.store') }}" method="POST">
            @csrf
            <div class="modal-content" style="border-radius:20px; border:none;">
                <div class="modal-header border-0 pb-0">
                    <h5 class="modal-title" style="font-weight:800;">Input Limbah Anggota</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-bold">Pilih Anggota</label>
                        <select name="user_id" class="form-select" required>
                            <option value="">-- Pilih Anggota --</option>
                            @foreach($members as $member)
                                <option value="{{ $member->id }}">{{ $member->name }} ({{ $member->member_id }})</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Kategori Limbah</label>
                        <select name="waste_category_id" class="form-select" required>
                            <option value="">-- Pilih Kategori --</option>
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}">{{ $category->name }} ({{ $category->point_multiplier }} Poin/Kg)</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Berat (Kg)</label>
                        <input type="number" step="0.1" name="weight" class="form-control" required placeholder="Contoh: 2.5">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Status Awal</label>
                        <select name="status" class="form-select" required>
                            <option value="APPROVED">Langsung Disetujui (APPROVED)</option>
                            <option value="PENDING">Menunggu Persetujuan (PENDING)</option>
                        </select>
                        <small class="text-muted">Jika langsung disetujui, poin akan otomatis ditambahkan ke anggota.</small>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Catatan/Deskripsi</label>
                        <textarea name="description" class="form-control" rows="2"></textarea>
                    </div>
                </div>
                <div class="modal-footer border-0 pt-0">
                    <button type="submit" class="btn btn-primary w-100" style="border-radius:12px;">Simpan Data Limbah</button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Edit Modal -->
<div class="modal fade" id="editDepositModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <form id="editDepositForm" method="POST" class="w-100">
            @csrf
            @method('PUT')
            <div class="modal-content glass" style="border-radius:24px;">
                <div class="modal-header border-0 pb-0">
                    <h5 class="modal-title" style="font-weight:800;">Edit Setoran Limbah</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-bold">Berat (Kg)</label>
                        <input type="number" step="0.1" name="weight" id="edit_weight" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Catatan/Deskripsi</label>
                        <textarea name="description" id="edit_description" class="form-control" rows="2"></textarea>
                    </div>
                    <small class="text-muted">Perubahan pada berat limbah yang sudah disetujui akan otomatis menyesuaikan poin anggota.</small>
                </div>
                <div class="modal-footer border-0 pt-0">
                    <button type="submit" class="btn btn-primary w-100" style="border-radius:12px;">Simpan Perubahan</button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Map Modal -->
<div class="modal fade" id="mapModal" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content glass" style="border-radius:24px;">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title" style="font-weight:800;">📍 Lokasi Penyetoran Limbah</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div id="mapContainer" style="width: 100%; height: 400px; border-radius: 12px; background: #e9ecef;"></div>
            </div>
        </div>
    </div>
</div>

<!-- LEAFLET JS -->
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
<script>
    let map = null;
    let marker = null;

    function showLocationMap(lat, lng, memberName) {
        var mapModal = new bootstrap.Modal(document.getElementById('mapModal'));
        mapModal.show();

        document.getElementById('mapModal').addEventListener('shown.bs.modal', function () {
            if (!map) {
                map = L.map('mapContainer').setView([lat, lng], 15);
                L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                    attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
                }).addTo(map);
                marker = L.marker([lat, lng]).addTo(map);
            } else {
                map.setView([lat, lng], 15);
                marker.setLatLng([lat, lng]);
            }
            marker.bindPopup(`<b>${memberName}</b><br>Lokasi Setoran Limbah`).openPopup();
            map.invalidateSize();
        }, { once: true });
    }

    function editDeposit(id, weight, description) {
        document.getElementById('edit_weight').value = weight;
        document.getElementById('edit_description').value = description || '';
        document.getElementById('editDepositForm').action = '/waste-deposits/' + id;
        var editModal = new bootstrap.Modal(document.getElementById('editDepositModal'));
        editModal.show();
    }
</script>
@endsection