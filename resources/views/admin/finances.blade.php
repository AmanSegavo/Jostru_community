@extends('layouts.admin')

@section('admin_content')
<div class="animate-fade-in">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem; flex-wrap: wrap; gap: 1rem;">
        <h2 style="margin: 0;">Laporan Keuangan Komunitas</h2>
        <div style="display: flex; gap: 10px; align-items: center; flex-wrap: wrap;">
            <form action="{{ route('admin.finances') }}" method="GET" style="display: flex; align-items: center;">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari keterangan..." required style="padding: 0.6rem 1rem; border: 1px solid var(--border-color); border-radius: 20px 0 0 20px; background: rgba(0,0,0,0.05); color: var(--text-color); outline: none;">
                <button type="submit" style="padding: 0.6rem 1.2rem; background: var(--primary); color: white; border: none; border-radius: 0 20px 20px 0; cursor: pointer; font-weight: 600;">Cari</button>
                @if(request('search'))
                    <a href="{{ route('admin.finances') }}" style="margin-left: 10px; font-size: 13px; color: #ef4444;">Reset</a>
                @endif
            </form>
            <a href="{{ route('admin.finances.export') }}" class="btn" style="background: rgba(16, 185, 129, 0.1); color: #10b981; border: 1px solid rgba(16,185,129,0.3);">
                <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" style="margin-right: 5px;"><path stroke-linecap="round" stroke-linejoin="round" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                Export .CSV
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success" style="background: rgba(34, 197, 94, 0.1); color: #22c55e; border: 1px solid rgba(34, 197, 94, 0.2); padding: 1rem; border-radius: 8px;">
            {{ session('success') }}
        </div>
    @endif

    <!-- Top Summary Cards -->
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 1.5rem;" class="mb-4">
        <div class="card p-3 glass border-0" style="border-left: 4px solid #22c55e !important;">
            <h6 class="text-muted mb-2">Total Pemasukan</h6>
            <h3 style="color: #22c55e; margin: 0;">Rp {{ number_format($totalPemasukan, 0, ',', '.') }}</h3>
        </div>
        <div class="card p-3 glass border-0" style="border-left: 4px solid #ef4444 !important;">
            <h6 class="text-muted mb-2">Total Pengeluaran</h6>
            <h3 style="color: #ef4444; margin: 0;">Rp {{ number_format($totalPengeluaran, 0, ',', '.') }}</h3>
        </div>
        <div class="card p-3 glass border-0" style="border-left: 4px solid #3b82f6 !important;">
            <h6 class="text-muted mb-2">Saldo Akhir</h6>
            <h3 style="color: #3b82f6; margin: 0;">Rp {{ number_format($saldo, 0, ',', '.') }}</h3>
        </div>
    </div>

    <!-- Main Content Area -->
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(350px, 1fr)); gap: 2rem; align-items: start;">
        <div style="flex: 1; min-width: 300px;">
            <div class="card p-4 glass">
                <h4 class="mb-4">Input Data Baru</h4>
                <form action="{{ route('admin.finances.store') }}" method="POST">
                    @csrf
                    <div class="mb-3" style="margin-bottom: 1.25rem;">
                        <label class="form-label" style="color: var(--text-secondary);">Jenis Transaksi</label>
                        <select name="type" class="form-control" required style="background: transparent; border: 1px solid var(--border-color); color: var(--text-color);">
                            <option style="color: black;" value="PEMASUKAN">Pemasukan</option>
                            <option style="color: black;" value="PENGELUARAN">Pengeluaran</option>
                        </select>
                    </div>
                    <div class="mb-3" style="margin-bottom: 1.25rem;">
                        <label class="form-label" style="color: var(--text-secondary);">Nominal (Rp)</label>
                        <input type="number" name="amount" class="form-control" required min="0" placeholder="100000" style="background: transparent; border: 1px solid var(--border-color); color: var(--text-color);">
                    </div>
                    <div class="mb-3" style="margin-bottom: 1.25rem;">
                        <label class="form-label" style="color: var(--text-secondary);">Tanggal Transaksi</label>
                        <input type="date" name="transaction_date" class="form-control" required value="{{ date('Y-m-d') }}" style="background: transparent; border: 1px solid var(--border-color); color: var(--text-color); color-scheme: dark;">
                    </div>
                    <div class="mb-4">
                        <label class="form-label" style="color: var(--text-secondary);">Keterangan Lengkap</label>
                        <textarea name="description" class="form-control" rows="3" required placeholder="Iuran bulan April dari hamba Allah..." style="background: transparent; border: 1px solid var(--border-color); color: var(--text-color);"></textarea>
                    </div>
                    <button type="submit" class="btn btn-primary w-100">Simpan Transaksi</button>
                </form>
            </div>
        </div>

        <div style="flex: 2; min-width: 450px; grid-column: span 2;">
            <div class="card p-4 glass">
                <h4 class="mb-4">Riwayat Transaksi</h4>
                <div class="table-responsive" style="overflow-x: auto;">
                    <table class="table text-white" style="width: 100%; min-width: 600px;">
                        <thead>
                            <tr>
                                <th style="color: var(--text-secondary);">Tanggal</th>
                                <th style="color: var(--text-secondary);">Jenis</th>
                                <th style="color: var(--text-secondary);">Keterangan</th>
                                <th style="color: var(--text-secondary);">Nominal</th>
                                <th style="color: var(--text-secondary);">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($finances as $finance)
                                <tr>
                                    <td>{{ \Carbon\Carbon::parse($finance->transaction_date)->format('d/m/Y') }}</td>
                                    <td>
                                        @if($finance->type == 'PEMASUKAN')
                                            <span class="badge bg-success" style="background: rgba(34, 197, 94, 0.2) !important; color: #22c55e;">Pemasukan</span>
                                        @else
                                            <span class="badge bg-danger" style="background: rgba(239, 68, 68, 0.2) !important; color: #ef4444;">Pengeluaran</span>
                                        @endif
                                    </td>
                                    <td>{{ $finance->description }}</td>
                                    <td style="font-weight: bold; color: {{ $finance->type == 'PEMASUKAN' ? '#22c55e' : '#ef4444' }}">
                                        Rp {{ number_format($finance->amount, 0, ',', '.') }}
                                    </td>
                                    <td>
                                        <form action="{{ route('admin.finances.destroy', $finance->id) }}" method="POST" style="display:inline;" onsubmit="return confirm('Apakah Anda yakin ingin menghapus data ini? Saldo akan dihitung ulang secara otomatis.');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline" style="color: #ef4444; border-color: rgba(239,68,68,0.5); padding: 5px 10px; font-size: 12px;">Hapus</button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center text-muted py-4">Belum ada riwayat transaksi keuangan.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="mt-4">
                    {{ $finances->links() }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
