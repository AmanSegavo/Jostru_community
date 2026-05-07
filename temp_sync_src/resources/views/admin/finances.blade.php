@extends('layouts.admin')
@section('admin_content')

<div class="animate-fade-in">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 style="font-weight:800;">Laporan Keuangan</h2>
            <p class="text-muted mb-0">Catat dan pantau pemasukan & pengeluaran komunitas.</p>
        </div>
        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#financeModal" style="border-radius:12px;background:#22c55e;border:none;">
            + Tambah Transaksi
        </button>
    </div>

    <!-- Statistik Ringkas -->
    <div class="row mb-4">
        <div class="col-md-4 mb-3">
            <div class="card glass p-4" style="border-radius:16px;">
                <div class="text-muted small">Total Pemasukan</div>
                <div class="fs-3 fw-bold text-success">Rp {{ number_format($totalPemasukan, 0, ',', '.') }}</div>
            </div>
        </div>
        <div class="col-md-4 mb-3">
            <div class="card glass p-4" style="border-radius:16px;">
                <div class="text-muted small">Total Pengeluaran</div>
                <div class="fs-3 fw-bold text-danger">Rp {{ number_format($totalPengeluaran, 0, ',', '.') }}</div>
            </div>
        </div>
        <div class="col-md-4 mb-3">
            <div class="card glass p-4" style="border-radius:16px; border-left:5px solid #22c55e;">
                <div class="text-muted small">Saldo Saat Ini</div>
                <div class="fs-3 fw-bold {{ $saldo >= 0 ? 'text-success' : 'text-danger' }}">
                    Rp {{ number_format($saldo, 0, ',', '.') }}
                </div>
            </div>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show mb-4" style="border-radius:12px;background:rgba(34,197,94,0.1);color:#22c55e;">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- Tabel Transaksi -->
    <div class="card glass p-0 overflow-hidden" style="border-radius:20px;">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead style="background:rgba(var(--primary-rgb),0.05);">
                    <tr>
                        <th class="px-4 py-3">Tanggal</th>
                        <th class="px-4 py-3">Jenis</th>
                        <th class="px-4 py-3">Kategori</th>
                        <th class="px-4 py-3">Keterangan</th>
                        <th class="px-4 py-3 text-end">Nominal</th>
                        <th class="px-4 py-3 text-end">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($finances as $finance)
                    <tr>
                        <td class="px-4 py-3">{{ $finance->transaction_date ? \Carbon\Carbon::parse($finance->transaction_date)->format('d M Y') : '-' }}</td>
                        <td class="px-4 py-3">
                            @if($finance->type == 'PEMASUKAN')
                                <span class="badge bg-success">PEMASUKAN</span>
                            @else
                                <span class="badge bg-danger">PENGELUARAN</span>
                            @endif
                        </td>
                        <td class="px-4 py-3"><span class="text-muted">{{ $finance->kategori ?? '-' }}</span></td>
                        <td class="px-4 py-3">{{ $finance->description }}</td>
                        <td class="px-4 py-3 text-end fw-bold {{ $finance->type == 'PEMASUKAN' ? 'text-success' : 'text-danger' }}">
                            {{ $finance->type == 'PEMASUKAN' ? '+' : '-' }} Rp {{ number_format($finance->amount, 0, ',', '.') }}
                        </td>
                        <td class="px-4 py-3 text-end">
                            <form action="{{ route('admin.finances.destroy', $finance->id) }}" method="POST" onsubmit="return confirm('Hapus transaksi ini?')">
                                @csrf
                                @method('DELETE')
                                <button class="btn btn-link text-danger p-0">
                                    <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                        <path d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                    </svg>
                                </button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center py-5 text-muted">Belum ada transaksi keuangan.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-3">
        {{ $finances->links() }}
    </div>
</div>

<!-- Modal Tambah Transaksi -->
<div class="modal fade" id="financeModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content glass" style="border-radius:24px;">
            <div class="modal-header border-0">
                <h5 class="modal-title" style="font-weight:800;">Tambah Transaksi Baru</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('admin.finances.store') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label" style="font-weight:600;">Jenis Transaksi</label>
                        <select name="type" class="form-control" required style="border-radius:12px;">
                            <option value="PEMASUKAN">Pemasukan</option>
                            <option value="PENGELUARAN">Pengeluaran</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label" style="font-weight:600;">Kategori</label>
                        <input type="text" name="kategori" class="form-control" placeholder="Contoh: Donasi / Operasional" style="border-radius:12px;">
                    </div>
                    <div class="mb-3">
                        <label class="form-label" style="font-weight:600;">Keterangan</label>
                        <input type="text" name="description" class="form-control" required placeholder="Deskripsi transaksi" style="border-radius:12px;">
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label" style="font-weight:600;">Nominal (Rp)</label>
                            <input type="number" name="amount" class="form-control" required style="border-radius:12px;">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label" style="font-weight:600;">Tanggal Transaksi</label>
                            <input type="date" name="transaction_date" class="form-control" value="{{ date('Y-m-d') }}" required style="border-radius:12px;">
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-0">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal" style="border-radius:12px;">Batal</button>
                    <button type="submit" class="btn btn-primary" style="border-radius:12px;background:#22c55e;border:none;">Simpan Transaksi</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection