@extends('layouts.admin')

@section('admin_content')
<div class="animate-fade-in">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem; flex-wrap: wrap; gap: 1rem;">
        <h2 style="margin: 0;">Laporan Keuangan Komunitas</h2>
        <div style="display: flex; gap: 10px; align-items: center; flex-wrap: wrap;">
            <form action="{{ route('admin.finances') }}" method="GET" style="display: flex; align-items: center; gap: 5px; flex-wrap: wrap;">
                <input type="date" name="start_date" value="{{ request('start_date') }}" style="padding: 0.6rem; border: 1px solid var(--border-color); border-radius: 8px; background: rgba(0,0,0,0.05); color: var(--text-color); outline: none;">
                <span class="text-muted">-</span>
                <input type="date" name="end_date" value="{{ request('end_date') }}" style="padding: 0.6rem; border: 1px solid var(--border-color); border-radius: 8px; background: rgba(0,0,0,0.05); color: var(--text-color); outline: none;">
                
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari keterangan/kategori..." style="padding: 0.6rem 1rem; border: 1px solid var(--border-color); border-radius: 8px; background: rgba(0,0,0,0.05); color: var(--text-color); outline: none;">
                <button type="submit" style="padding: 0.6rem 1.2rem; background: var(--primary); color: white; border: none; border-radius: 8px; cursor: pointer; font-weight: 600;">Filter</button>
                @if(request('search') || request('start_date') || request('end_date'))
                    <a href="{{ route('admin.finances') }}" style="margin-left: 5px; font-size: 13px; color: #ef4444;">Reset</a>
                @endif
            </form>
            <a href="{{ route('admin.finances.export') }}" class="btn" style="background: rgba(16, 185, 129, 0.1); color: #10b981; border: 1px solid rgba(16,185,129,0.3);">
                <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" style="margin-right: 5px;"><path stroke-linecap="round" stroke-linejoin="round" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                Export .CSV
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success" style="background: rgba(34, 197, 94, 0.1); color: #22c55e; border: 1px solid rgba(34, 197, 94, 0.2); padding: 1rem; border-radius: 8px; margin-bottom: 1rem;">
            {{ session('success') }}
        </div>
    @endif

    <!-- Top Summary Cards -->
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(230px, 1fr)); gap: 1.5rem;" class="mb-4">
        <div class="card p-3 glass border-0" style="border-left: 4px solid #22c55e !important;">
            <div class="d-flex justify-content-between">
                <h6 class="text-muted mb-2">Total Pemasukan</h6>
                @php $diff = $thisMonthPemasukan - $lastMonthPemasukan; @endphp
                <small style="color: {{ $diff >= 0 ? '#22c55e' : '#ef4444' }}; font-size: 10px;">
                    {{ $diff >= 0 ? '▲' : '▼' }} Bln ini
                </small>
            </div>
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
        <div class="card p-3 glass border-0" style="border-left: 4px solid #f59e0b !important;">
            <h6 class="text-muted mb-2">Kategori Aktif</h6>
            <h3 style="color: #f59e0b; margin: 0;">{{ $pemasukanPerKategori->count() + $pengeluaranPerKategori->count() }}</h3>
        </div>
    </div>

    <!-- Category Breakdown (Detailed Section) -->
    <div class="card p-4 glass mb-4">
        <h4 class="mb-3">Analisis Kategori</h4>
        <div class="row">
            <div class="col-md-6">
                <h6 class="text-muted mb-3">Sumber Pemasukan</h6>
                @foreach($pemasukanPerKategori as $pk)
                    <div class="d-flex justify-content-between align-items-center mb-2 p-2" style="background: rgba(34, 197, 94, 0.05); border-radius: 8px;">
                        <span>{{ $pk->kategori ?: 'Lain-lain' }}</span>
                        <span style="font-weight: 600; color: #22c55e;">Rp {{ number_format($pk->total, 0, ',', '.') }}</span>
                    </div>
                @endforeach
            </div>
            <div class="col-md-6">
                <h6 class="text-muted mb-3">Alokasi Pengeluaran</h6>
                @foreach($pengeluaranPerKategori as $pk)
                    <div class="d-flex justify-content-between align-items-center mb-2 p-2" style="background: rgba(239, 68, 68, 0.05); border-radius: 8px;">
                        <span>{{ $pk->kategori ?: 'Lain-lain' }}</span>
                        <span style="font-weight: 600; color: #ef4444;">Rp {{ number_format($pk->total, 0, ',', '.') }}</span>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    <!-- Main Content Area -->
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(350px, 1fr)); gap: 2rem; align-items: start;">
        <div style="flex: 1; min-width: 300px;">
            <div class="card p-4 glass">
                <h4 class="mb-4" id="formTitle">Input Data Baru</h4>
                <form action="{{ route('admin.finances.store') }}" method="POST" id="financeForm">
                    @csrf
                    <input type="hidden" name="_method" value="POST" id="formMethod">
                    
                    <div class="mb-3" style="margin-bottom: 1.25rem;">
                        <label class="form-label" style="color: var(--text-secondary);">Jenis Transaksi</label>
                        <select name="type" id="typeInput" class="form-control" required style="background: transparent; border: 1px solid var(--border-color); color: var(--text-color);">
                            <option style="color: black;" value="PEMASUKAN">Pemasukan</option>
                            <option style="color: black;" value="PENGELUARAN">Pengeluaran</option>
                        </select>
                    </div>
                    <div class="mb-3" style="margin-bottom: 1.25rem;">
                        <label class="form-label" style="color: var(--text-secondary);">Kategori (Opsional)</label>
                        <input type="text" name="kategori" id="kategoriInput" class="form-control" placeholder="Iuran, Donasi, Operasional..." style="background: transparent; border: 1px solid var(--border-color); color: var(--text-color);">
                    </div>
                    <div class="mb-3" style="margin-bottom: 1.25rem;">
                        <label class="form-label" style="color: var(--text-secondary);">Nominal (Rp)</label>
                        <input type="number" name="amount" id="amountInput" class="form-control" required min="0" placeholder="100000" style="background: transparent; border: 1px solid var(--border-color); color: var(--text-color);">
                    </div>
                    <div class="mb-3" style="margin-bottom: 1.25rem;">
                        <label class="form-label" style="color: var(--text-secondary);">Tanggal Transaksi</label>
                        <input type="date" name="transaction_date" id="dateInput" class="form-control" required value="{{ date('Y-m-d') }}" style="background: transparent; border: 1px solid var(--border-color); color: var(--text-color); color-scheme: dark;">
                    </div>
                    <div class="mb-4">
                        <label class="form-label" style="color: var(--text-secondary);">Keterangan Lengkap</label>
                        <textarea name="description" id="descInput" class="form-control" rows="3" required placeholder="Iuran bulan April dari hamba Allah..." style="background: transparent; border: 1px solid var(--border-color); color: var(--text-color);"></textarea>
                    </div>
                    <div style="display: flex; gap: 10px;">
                        <button type="submit" class="btn btn-primary w-100" id="submitBtn">Simpan Transaksi</button>
                        <button type="button" class="btn btn-outline w-100" id="cancelBtn" style="display: none;" onclick="resetForm()">Batal Edit</button>
                    </div>
                </form>
            </div>
        </div>

        <div style="flex: 2; min-width: 450px; grid-column: span 2;">
            <div class="card p-4 glass">
                <h4 class="mb-4">Riwayat Transaksi</h4>
                <div class="table-responsive" style="overflow-x: auto;">
                    <table class="table text-white" style="width: 100%; min-width: 700px;">
                        <thead>
                            <tr>
                                <th style="color: var(--text-secondary);">Tanggal</th>
                                <th style="color: var(--text-secondary);">Jenis</th>
                                <th style="color: var(--text-secondary);">Kategori</th>
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
                                    <td>{{ $finance->kategori ?? '-' }}</td>
                                    <td>{{ $finance->description }}</td>
                                    <td style="font-weight: bold; color: {{ $finance->type == 'PEMASUKAN' ? '#22c55e' : '#ef4444' }}">
                                        Rp {{ number_format($finance->amount, 0, ',', '.') }}
                                    </td>
                                    <td>
                                        <div style="display: flex; gap: 5px;">
                                            <button type="button" class="btn btn-sm btn-outline" style="padding: 5px 10px; font-size: 12px;" onclick="editFinance({{ $finance->id }}, '{{ $finance->type }}', '{{ $finance->kategori }}', {{ $finance->amount }}, '{{ $finance->transaction_date->format('Y-m-d') }}', '{{ $finance->description }}')">Edit</button>
                                            
                                            <form action="{{ route('admin.finances.destroy', $finance->id) }}" method="POST" style="display:inline;" onsubmit="return confirm('Apakah Anda yakin ingin menghapus data ini? Saldo akan dihitung ulang secara otomatis.');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-outline" style="color: #ef4444; border-color: rgba(239,68,68,0.5); padding: 5px 10px; font-size: 12px;">Hapus</button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center text-muted py-4">Belum ada riwayat transaksi keuangan.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="mt-4" style="display: flex; justify-content: center;">
                    {{ $finances->links() }}
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    function editFinance(id, type, kategori, amount, date, description) {
        document.getElementById('formTitle').innerText = 'Edit Transaksi';
        document.getElementById('financeForm').action = `/admin/finances/${id}`;
        document.getElementById('formMethod').value = 'PUT';
        
        document.getElementById('typeInput').value = type;
        document.getElementById('kategoriInput').value = kategori !== '-' ? kategori : '';
        document.getElementById('amountInput').value = amount;
        document.getElementById('dateInput').value = date;
        document.getElementById('descInput').value = description;
        
        document.getElementById('submitBtn').innerText = 'Update Transaksi';
        document.getElementById('cancelBtn').style.display = 'block';
        
        // Scroll to form
        window.scrollTo({ top: 0, behavior: 'smooth' });
    }

    function resetForm() {
        document.getElementById('formTitle').innerText = 'Input Data Baru';
        document.getElementById('financeForm').action = `{{ route('admin.finances.store') }}`;
        document.getElementById('formMethod').value = 'POST';
        
        document.getElementById('financeForm').reset();
        document.getElementById('dateInput').value = '{{ date('Y-m-d') }}';
        
        document.getElementById('submitBtn').innerText = 'Simpan Transaksi';
        document.getElementById('cancelBtn').style.display = 'none';
    }
</script>
@endsection
