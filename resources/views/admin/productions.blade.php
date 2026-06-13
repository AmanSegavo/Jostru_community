@extends('layouts.admin')

@section('title', 'Manajemen Hasil Produksi - Jostru')

@section('admin_content')
<div class="animate-fade-in">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 style="font-weight: 800;">Manajemen Hasil Produksi (V1.2)</h2>
<div class="mt-2 mb-3"><a href="{{ route('admin.productions.export') }}" class="btn hover-lift" style="background:rgba(34,197,94,0.1); color:var(--primary-accent); border:1px solid rgba(34,197,94,0.3); padding:0.5rem 1rem; border-radius:12px; font-size:14px; text-decoration:none; display:inline-flex; align-items:center; gap:5px; font-weight:600;">
    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path><polyline points="7 10 12 15 17 10"></polyline><line x1="12" y1="15" x2="12" y2="3"></line></svg>
    Export CSV
</a></div>

            <p class="text-muted mb-0">Catat hasil olahan limbah menjadi produk siap jual.</p>
        </div>
        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalTambahProduksi" 
                style="border-radius: 12px; background: #22c55e; border: none;">
            + Tambah Produksi
        </button>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show mb-4" style="border-radius: 12px; background: rgba(34,197,94,0.1); color: #22c55e; border: 1px solid rgba(34,197,94,0.2);">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="card glass p-0 overflow-hidden" style="border-radius: 20px; border: 1px solid rgba(var(--primary-rgb), 0.1);">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead style="background: rgba(var(--primary-rgb), 0.05);">
                    <tr>
                        <th class="px-4 py-3">ID</th>
                        <th class="px-4 py-3">Kode SKU</th>
                        <th class="px-4 py-3">Kuantitas</th>
                        <th class="px-4 py-3">Harga Satuan</th>
                        <th class="px-4 py-3">Sumber Limbah</th>
                        <th class="px-4 py-3">Tanggal Produksi</th>
                        <th class="px-4 py-3 text-end">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($productions as $batch)
                    <tr>
                        <td class="px-4 py-3 font-weight-bold">#{{ $batch->id }}</td>
                        <td class="px-4 py-3">
                            <span class="badge bg-primary" style="background: #3b82f6;">{{ $batch->product_sku }}</span>
                        </td>
                        <td class="px-4 py-3 font-weight-bold">{{ $batch->quantity_produced }} Unit</td>
                        <td class="px-4 py-3 text-success font-weight-bold">
                            Rp {{ number_format($batch->price, 0, ',', '.') }}
                        </td>
                        <td class="px-4 py-3">
                            @if($batch->sourceWaste)
                                <small>L-{{ $batch->sourceWaste->id }} ({{ $batch->sourceWaste->type }})</small><br>
                                <span class="text-muted" style="font-size: 0.75rem;">{{ $batch->sourceWaste->user->name ?? 'Anonim' }}</span>
                            @else
                                <span class="text-muted fst-italic">Bahan Baku Luar</span>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-muted">{{ $batch->produced_at->format('d M Y') }}</td>
                        <td class="px-4 py-3 text-end">
                            <form action="{{ route('admin.productions.destroy', $batch->id) }}" method="POST" class="d-inline"
                                  onsubmit="return confirm('Hapus catatan produksi ini?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-link text-danger p-0">
                                    <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                        <path d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                    </svg>
                                </button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center py-5 text-muted">Belum ada data hasil produksi.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal Tambah Produksi -->
<div class="modal fade" id="modalTambahProduksi" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content glass" style="border-radius: 20px; border: 1px solid rgba(255,255,255,0.15);">
            <div class="modal-header border-0">
                <h5 class="modal-title" style="font-weight: 800;">Catat Hasil Produksi Baru</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('admin.productions.store') }}" method="POST" id="formProduksi">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label" style="font-weight: 600;">Kode SKU Produk <span class="text-danger">*</span></label>
                        <input type="text" name="product_sku" class="form-control" placeholder="Contoh: PUPUK-KOMPOS-01" required style="border-radius: 10px;">
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label" style="font-weight: 600;">Kuantitas Dihasilkan <span class="text-danger">*</span></label>
                            <input type="number" step="0.01" name="quantity_produced" class="form-control" placeholder="0.00" required style="border-radius: 10px;">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label" style="font-weight: 600;">Harga Satuan (Rp) <span class="text-danger">*</span></label>
                            <input type="number" name="price" class="form-control" placeholder="15000" required style="border-radius: 10px;">
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label" style="font-weight: 600;">Sumber Limbah (Opsional)</label>
                        <select name="source_waste_id" class="form-control" style="border-radius: 10px;">
                            <option value="">-- Bukan dari limbah tersimpan --</option>
                            @foreach($approvedWastes as $waste)
                                <option value="{{ $waste->id }}">
                                    L-{{ $waste->id }} | {{ $waste->type }} ({{ $waste->weight }} Kg) — {{ $waste->user->name ?? 'Anonim' }}
                                </option>
                            @endforeach
                        </select>
                        <small class="text-muted">Jika dipilih, status limbah akan berubah menjadi PROCESSED.</small>
                    </div>

                    <div class="mb-3">
                        <label class="form-label" style="font-weight: 600;">Tanggal Produksi <span class="text-danger">*</span></label>
                        <input type="date" name="produced_at" class="form-control" value="{{ date('Y-m-d') }}" required style="border-radius: 10px;">
                    </div>
                </div>
                <div class="modal-footer border-0">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal" style="border-radius: 10px;">Batal</button>
                    <button type="submit" class="btn btn-primary" style="border-radius: 10px; background: #22c55e; border: none;">
                        Simpan Produksi
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Optional: Tambah loading state saat submit
    document.getElementById('formProduksi').addEventListener('submit', function() {
        const btns = this.querySelectorAll('button[type="submit"]');
        btns.forEach(btn => {
            btn.disabled = true;
            btn.innerHTML = 'Menyimpan...';
        });
    });
</script>
@endpush