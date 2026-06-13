@extends('layouts.admin')

@section('admin_content')
<div class="animate-fade-in">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 style="font-weight:800;">Manajemen RAB</h2>
<div class="mt-2 mb-3"><a href="{{ route('admin.rabs.export') }}" class="btn hover-lift" style="background:rgba(34,197,94,0.1); color:var(--primary-accent); border:1px solid rgba(34,197,94,0.3); padding:0.5rem 1rem; border-radius:12px; font-size:14px; text-decoration:none; display:inline-flex; align-items:center; gap:5px; font-weight:600;">
    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path><polyline points="7 10 12 15 17 10"></polyline><line x1="12" y1="15" x2="12" y2="3"></line></svg>
    Export CSV
</a></div>

            <p class="text-muted mb-0">Rencana Anggaran Biaya untuk pengajuan dana divisi.</p>
        </div>
        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#rabModal" style="border-radius:12px;background:#22c55e;border:none;">
            + Ajukan RAB Baru
        </button>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show mb-4" style="border-radius:12px;">
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
                        <th class="px-4 py-3">Judul RAB</th>
                        <th class="px-4 py-3">Divisi</th>
                        <th class="px-4 py-3">Total Anggaran</th>
                        <th class="px-4 py-3">Status</th>
                        <th class="px-4 py-3 text-end">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($rabs as $rab)
                    <tr>
                        <td class="px-4 py-3">{{ $rab->created_at->format('d M Y') }}</td>
                        <td class="px-4 py-3 fw-bold">{{ $rab->title }}</td>
                        <td class="px-4 py-3">{{ $rab->division ? $rab->division->name : '-' }}</td>
                        <td class="px-4 py-3 fw-bold text-primary">Rp {{ number_format($rab->total_amount, 0, ',', '.') }}</td>
                        <td class="px-4 py-3">
                            @if($rab->status == 'PENDING')
                                <span class="badge bg-warning text-dark">Menunggu</span>
                            @elseif($rab->status == 'APPROVED')
                                <span class="badge bg-success">Disetujui</span>
                            @else
                                <span class="badge bg-danger">Ditolak</span>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-end data-no-export">
                            <button class="btn btn-sm btn-outline-secondary" data-bs-toggle="modal" data-bs-target="#viewRab{{ $rab->id }}" style="border-radius:8px;" title="Lihat Detail">Lihat / Proses</button>
                            <button type="button" class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#editRabModal{{ $rab->id }}" style="border-radius:6px; padding:0.15rem 0.4rem; margin-left:4px;" title="Edit RAB">
                                <i class="bi bi-pencil"></i>
                            </button>
                            <form action="{{ route('admin.rabs.destroy', $rab->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Hapus pengajuan RAB ini secara permanen?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-outline-danger" style="border-radius:6px; padding:0.15rem 0.4rem; margin-left:2px;" title="Hapus RAB">
                                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path></svg>
                                </button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center py-5 text-muted">Belum ada pengajuan RAB.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    
    <div class="mt-3">
        {{ $rabs->links() }}
    </div>
</div>

<!-- Modal Ajukan RAB -->
<div class="modal fade" id="rabModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content glass" style="border-radius:24px;">
            <div class="modal-header border-0">
                <h5 class="modal-title fw-bold">Pengajuan Rencana Anggaran Biaya</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('admin.rabs.store') }}" method="POST" id="rabForm">
                @csrf
                <div class="modal-body">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Judul RAB</label>
                            <input type="text" name="title" class="form-control" required placeholder="Cth: Anggaran Pakan Bulan Juni" style="border-radius:12px;">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Divisi Pemohon</label>
                            <select name="division_id" class="form-select" required style="border-radius:12px;">
                                @foreach($divisions as $div)
                                    <option value="{{ $div->id }}">{{ $div->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="mb-4">
                        <label class="form-label fw-bold">Keterangan Singkat</label>
                        <textarea name="description" class="form-control" rows="2" style="border-radius:12px;"></textarea>
                    </div>

                    <h6 class="fw-bold text-success border-bottom pb-2 mb-3">Detail Item RAB</h6>
                    <div id="rabItemsContainer">
                        <div class="row align-items-center mb-2 rab-item-row">
                            <div class="col-md-5">
                                <input type="text" name="items[0][name]" class="form-control form-control-sm" placeholder="Nama Barang/Jasa" required>
                            </div>
                            <div class="col-md-2">
                                <input type="number" name="items[0][qty]" class="form-control form-control-sm" placeholder="Qty" value="1" required min="1">
                            </div>
                            <div class="col-md-4">
                                <input type="number" name="items[0][price]" class="form-control form-control-sm" placeholder="Harga Satuan" required min="0">
                            </div>
                            <div class="col-md-1">
                                <button type="button" class="btn btn-sm btn-danger remove-item" style="display:none;">&times;</button>
                            </div>
                        </div>
                    </div>
                    <button type="button" class="btn btn-sm btn-outline-success mt-2" id="addRabItem">+ Tambah Item Lainnya</button>
                </div>
                <div class="modal-footer border-0">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal" style="border-radius:12px;">Batal</button>
                    <button type="submit" class="btn btn-primary" style="border-radius:12px;background:#22c55e;border:none;">Ajukan RAB</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    let itemIndex = 1;
    const container = document.getElementById('rabItemsContainer');
    const addBtn = document.getElementById('addRabItem');
    
    addBtn.addEventListener('click', function() {
        const row = document.createElement('div');
        row.className = 'row align-items-center mb-2 rab-item-row';
        row.innerHTML = `
            <div class="col-md-5">
                <input type="text" name="items[${itemIndex}][name]" class="form-control form-control-sm" placeholder="Nama Barang/Jasa" required>
            </div>
            <div class="col-md-2">
                <input type="number" name="items[${itemIndex}][qty]" class="form-control form-control-sm" placeholder="Qty" value="1" required min="1">
            </div>
            <div class="col-md-4">
                <input type="number" name="items[${itemIndex}][price]" class="form-control form-control-sm" placeholder="Harga Satuan" required min="0">
            </div>
            <div class="col-md-1">
                <button type="button" class="btn btn-sm btn-danger remove-item">&times;</button>
            </div>
        `;
        container.appendChild(row);
        itemIndex++;
        
        row.querySelector('.remove-item').addEventListener('click', function() {
            row.remove();
        });
    });
});
</script>

<!-- View RAB Modals -->
@foreach($rabs as $rab)
<div class="modal fade" id="viewRab{{ $rab->id }}" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content glass" style="border-radius:24px;">
            <div class="modal-header border-0">
                <h5 class="modal-title fw-bold">Detail RAB: {{ $rab->title }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p class="text-muted">{{ $rab->description }}</p>
                <table class="table table-bordered mt-3">
                    <thead class="table-light">
                        <tr>
                            <th>Nama Item/Kebutuhan</th>
                            <th>Qty</th>
                            <th>Harga Satuan</th>
                            <th>Subtotal</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($rab->items as $item)
                        <tr>
                            <td>{{ $item->name }}</td>
                            <td>{{ $item->qty }}</td>
                            <td>Rp {{ number_format($item->unit_price, 0, ',', '.') }}</td>
                            <td class="fw-bold">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr>
                            <th colspan="3" class="text-end">TOTAL KESELURUHAN</th>
                            <th class="text-success fs-5">Rp {{ number_format($rab->total_amount, 0, ',', '.') }}</th>
                        </tr>
                    </tfoot>
                </table>
                
                @if($rab->status == 'APPROVED')
                    @php
                        $terserap = $rab->finances()->sum('amount');
                        $persen = $rab->total_amount > 0 ? min(100, round(($terserap / $rab->total_amount) * 100)) : 0;
                    @endphp
                    <div class="mt-4 p-3 rounded" style="background: rgba(34,197,94,0.05); border: 1px solid rgba(34,197,94,0.2);">
                        <h6 class="fw-bold mb-2 text-success">Progres Serapan Dana:</h6>
                        <div class="d-flex justify-content-between small fw-bold mb-1 text-success">
                            <span>Terserap: Rp {{ number_format($terserap, 0, ',', '.') }}</span>
                            <span>{{ $persen }}%</span>
                        </div>
                        <div class="progress" style="height: 12px; border-radius: 6px; background-color: #e5e7eb; box-shadow: inset 0 1px 2px rgba(0,0,0,0.1);">
                            <div class="progress-bar progress-bar-striped progress-bar-animated bg-success" role="progressbar" style="width: {{ $persen }}%;" aria-valuenow="{{ $persen }}" aria-valuemin="0" aria-valuemax="100"></div>
                        </div>
                        <small class="text-muted mt-2 d-block">Dana terserap dari Kas Pusat menuju RAB ini.</small>
                    </div>
                @endif

                @if($rab->status == 'PENDING')
                <form action="{{ route('admin.rabs.status', $rab->id) }}" method="POST" class="mt-4 p-3 rounded" style="background: rgba(0,0,0,0.02); border: 1px dashed #ccc;">
                    @csrf
                    @method('PUT')
                    <h6 class="fw-bold mb-3">Tindakan Admin:</h6>
                    <div class="d-flex gap-3 align-items-center">
                        <select name="status" class="form-select" style="border-radius:8px; max-width:200px;">
                            <option value="APPROVED">Setujui RAB</option>
                            <option value="REJECTED">Tolak RAB</option>
                        </select>
                        <label class="d-flex align-items-center gap-2">
                            <input type="checkbox" name="create_budget" value="1" checked>
                            <span>Buat Alokasi Budget Otomatis jika Disetujui</span>
                        </label>
                        <button type="submit" class="btn btn-primary" style="border-radius:8px;">Proses RAB</button>
                    </div>
                </form>
                @endif
            </div>
        </div>
    </div>
</div>
@endforeach

<!-- Edit RAB Modals -->
@foreach($rabs as $rab)
<div class="modal fade" id="editRabModal{{ $rab->id }}" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content glass-panel" style="border:none;">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title" style="font-weight:800;">Edit Pengajuan RAB</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('admin.rabs.update', $rab->id) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-body p-4">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Judul Pengajuan</label>
                            <input type="text" name="title" class="form-control" value="{{ $rab->title }}" required style="border-radius:12px;">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Divisi Pemohon</label>
                            <select name="division_id" class="form-select" required style="border-radius:12px;">
                                @foreach($divisions as $div)
                                    <option value="{{ $div->id }}" {{ $rab->division_id == $div->id ? 'selected' : '' }}>{{ $div->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="mb-4">
                        <label class="form-label fw-bold">Keterangan Singkat</label>
                        <textarea name="description" class="form-control" rows="2" style="border-radius:12px;">{{ $rab->description }}</textarea>
                    </div>

                    <h6 class="fw-bold text-success border-bottom pb-2 mb-3">Detail Item RAB</h6>
                    <div id="rabItemsContainerEdit{{ $rab->id }}">
                        @foreach($rab->items as $index => $item)
                        <div class="row align-items-center mb-2 rab-item-row-edit">
                            <div class="col-md-5">
                                <input type="text" name="items[{{ $index }}][name]" class="form-control form-control-sm" value="{{ $item->name }}" required>
                            </div>
                            <div class="col-md-2">
                                <input type="number" name="items[{{ $index }}][qty]" class="form-control form-control-sm" value="{{ $item->qty }}" required min="1">
                            </div>
                            <div class="col-md-4">
                                <input type="number" name="items[{{ $index }}][price]" class="form-control form-control-sm" value="{{ $item->unit_price }}" required min="0">
                            </div>
                            <div class="col-md-1">
                                <button type="button" class="btn btn-sm btn-danger remove-item-edit" {{ $index == 0 ? 'style=display:none;' : '' }}>&times;</button>
                            </div>
                        </div>
                        @endforeach
                    </div>
                    <button type="button" class="btn btn-sm btn-outline-success mt-2" onclick="addRabItemEdit({{ $rab->id }}, {{ $rab->items->count() }})">+ Tambah Item Lainnya</button>
                </div>
                <div class="modal-footer border-0">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal" style="border-radius:12px;">Batal</button>
                    <button type="submit" class="btn btn-primary" style="border-radius:12px;background:#22c55e;border:none;">Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endforeach

<script>
    function addRabItemEdit(rabId, startIndex) {
        // Find current max index for this modal
        let container = document.getElementById('rabItemsContainerEdit' + rabId);
        let rows = container.querySelectorAll('.rab-item-row-edit');
        let index = startIndex + rows.length; // ensures uniqueness
        
        const row = document.createElement('div');
        row.className = 'row align-items-center mb-2 rab-item-row-edit';
        row.innerHTML = `
            <div class="col-md-5">
                <input type="text" name="items[${index}][name]" class="form-control form-control-sm" placeholder="Nama Barang/Jasa" required>
            </div>
            <div class="col-md-2">
                <input type="number" name="items[${index}][qty]" class="form-control form-control-sm" placeholder="Qty" value="1" required min="1">
            </div>
            <div class="col-md-4">
                <input type="number" name="items[${index}][price]" class="form-control form-control-sm" placeholder="Harga Satuan" required min="0">
            </div>
            <div class="col-md-1">
                <button type="button" class="btn btn-sm btn-danger remove-item-edit" onclick="this.closest('.rab-item-row-edit').remove()">&times;</button>
            </div>
        `;
        container.appendChild(row);
    }
    
    // Attach remove event for existing items
    document.addEventListener('DOMContentLoaded', function() {
        document.querySelectorAll('.remove-item-edit').forEach(btn => {
            btn.addEventListener('click', function() {
                this.closest('.rab-item-row-edit').remove();
            });
        });
    });
</script>

@endsection
