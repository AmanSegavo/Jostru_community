@extends('layouts.admin')

@section('admin_content')
<style>
    .glass-header { background:linear-gradient(135deg, #1e293b, #0f172a); color:white; border-radius:24px; padding:2.5rem; position:relative; overflow:hidden; box-shadow:0 20px 40px rgba(0,0,0,0.1); margin-bottom:2rem; }
    .glass-header::before { content:''; position:absolute; top:-50%; right:-10%; width:300px; height:300px; background:radial-gradient(circle, rgba(34,197,94,0.15) 0%, transparent 70%); border-radius:50%; }
    .glass-panel { background:rgba(255,255,255,0.7); backdrop-filter:blur(12px); border:1px solid rgba(255,255,255,0.5); border-radius:24px; box-shadow:0 15px 35px rgba(0,0,0,0.05); }
    .table-modern { width:100%; border-collapse:collapse; }
    .table-modern th { padding:16px 20px; font-weight:700; color:var(--text-secondary); text-transform:uppercase; font-size:12px; letter-spacing:0.5px; border-bottom:2px solid rgba(0,0,0,0.05); }
    .table-modern td { padding:16px 20px; border-bottom:1px solid rgba(0,0,0,0.05); vertical-align:middle; }
</style>

<div class="animate-fade-in">
    <div class="glass-header d-flex flex-wrap justify-content-between align-items-center gap-4">
        <div style="position:relative; z-index:2;">
            <a href="{{ route('admin.divisions.show', $division->id) }}" class="text-decoration-none text-muted mb-3 d-inline-block" style="color:rgba(255,255,255,0.6) !important; font-weight:600; font-size:13px; letter-spacing:0.5px;">&larr; KEMBALI KE RUANG KERJA DIVISI</a>
            <h2 style="font-weight:900; font-size:2.5rem; margin-bottom:8px; letter-spacing:-0.5px;">Keuangan Divisi</h2>
            <div class="d-flex gap-3 align-items-center">
                <span class="badge" style="background:rgba(255,255,255,0.1); border:1px solid rgba(255,255,255,0.2); padding:6px 12px; font-size:12px; letter-spacing:1px; color:#4ade80;">Divisi {{ $division->name }}</span>
            </div>
        </div>
        <div style="position:relative; z-index:2;">
            <button class="btn" data-bs-toggle="modal" data-bs-target="#addFinanceModal" style="background:linear-gradient(135deg, #3b82f6, #2563eb); color:white; border:none; border-radius:14px; font-weight:700; padding:12px 24px; box-shadow:0 10px 25px rgba(59,130,246,0.4); transition:all 0.3s ease;">+ Catat Transaksi</button>
        </div>
    </div>

    <div class="row mb-4 g-4">
        <div class="col-md-4">
            <div class="card border-0 glass-panel h-100" style="border-left:4px solid #22c55e !important;">
                <div class="card-body p-4">
                    <p class="text-muted mb-1 font-weight-bold">Total Pemasukan Divisi</p>
                    <h3 style="font-weight:800; color:#16a34a;">Rp {{ number_format($totalPemasukan, 0, ',', '.') }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 glass-panel h-100" style="border-left:4px solid #ef4444 !important;">
                <div class="card-body p-4">
                    <p class="text-muted mb-1 font-weight-bold">Total Pengeluaran Divisi</p>
                    <h3 style="font-weight:800; color:#dc2626;">Rp {{ number_format($totalPengeluaran, 0, ',', '.') }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 glass-panel h-100" style="border-left:4px solid #3b82f6 !important;">
                <div class="card-body p-4">
                    <p class="text-muted mb-1 font-weight-bold">Saldo Bersih Divisi</p>
                    <h3 style="font-weight:800; color:#2563eb;">Rp {{ number_format($saldo, 0, ',', '.') }}</h3>
                </div>
            </div>
        </div>
    </div>

    <div class="glass-panel p-0 overflow-hidden mb-4">
        <div class="p-4" style="background:rgba(248,250,252,0.5); border-bottom:1px solid rgba(0,0,0,0.05);">
            <h4 style="font-weight:700; margin:0;">Riwayat Transaksi</h4>
        </div>
        <div class="table-responsive">
            <table class="table-modern">
                <thead>
                    <tr>
                        <th>Tanggal</th>
                        <th>Tipe</th>
                        <th>Deskripsi</th>
                        <th>Terkait Anggaran</th>
                        <th class="text-end">Jumlah (Rp)</th>
                        <th>Oleh</th>
                        <th class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($finances as $finance)
                    <tr>
                        <td style="font-weight:600;">{{ \Carbon\Carbon::parse($finance->transaction_date)->format('d M Y') }}</td>
                        <td>
                            @if($finance->type === 'PEMASUKAN')
                                <span class="badge" style="background:rgba(34,197,94,0.1); color:#16a34a;">PEMASUKAN</span>
                            @else
                                <span class="badge" style="background:rgba(239,68,68,0.1); color:#ef4444;">PENGELUARAN</span>
                            @endif
                        </td>
                        <td>{{ $finance->description }}</td>
                        <td>
                            @if($finance->budget)
                                <span style="font-size:12px; font-weight:600; background:rgba(59,130,246,0.1); color:#3b82f6; padding:4px 8px; border-radius:6px;">
                                    {{ $finance->budget->description ?? 'Anggaran (ID:'.$finance->budget->id.')' }}
                                </span>
                            @else
                                -
                            @endif
                        </td>
                        <td class="text-end" style="font-weight:700; color:{{ $finance->type === 'PEMASUKAN' ? '#16a34a' : '#ef4444' }};">
                            {{ $finance->type === 'PEMASUKAN' ? '+' : '-' }} {{ number_format($finance->amount, 0, ',', '.') }}
                        </td>
                        <td>{{ $finance->user->name ?? 'Admin' }}</td>
                        <td class="text-center">
                            <div class="d-flex justify-content-center gap-2">
                                <button class="btn btn-sm" data-bs-toggle="modal" data-bs-target="#editFinanceModal{{ $finance->id }}" style="background:rgba(59,130,246,0.1); color:#3b82f6; border-radius:8px; border:none;" title="Edit Transaksi">✏️</button>
                                <form action="{{ route('admin.divisions.finances.destroy', [$division->id, $finance->id]) }}" method="POST" class="m-0" onsubmit="return confirm('Apakah Anda yakin ingin menghapus transaksi ini?');">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-sm" style="background:rgba(239,68,68,0.1); color:#ef4444; border-radius:8px; border:none;" title="Hapus Transaksi">🗑️</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center py-5 text-muted">Belum ada transaksi keuangan divisi.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal Tambah Transaksi -->
<div class="modal fade" id="addFinanceModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <form action="{{ route('admin.divisions.finances.store', $division->id) }}" method="POST" class="w-100">
            @csrf
            <div class="modal-content glass-panel" style="border:none;">
                <div class="modal-header border-0 pb-0">
                    <h5 class="modal-title" style="font-weight:800;">Catat Transaksi Divisi</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label" style="font-weight:600;">Tipe Transaksi</label>
                        <select name="type" class="form-control" required style="border-radius:12px;" id="financeTypeSelect">
                            <option value="PEMASUKAN">Pemasukan (Masuk)</option>
                            <option value="PENGELUARAN">Pengeluaran (Keluar)</option>
                        </select>
                    </div>
                    
                    <div class="mb-3" id="budgetSelectDiv" style="display:none;">
                        <label class="form-label" style="font-weight:600;">Gunakan Dari Anggaran</label>
                        <select name="budget_id" class="form-control" style="border-radius:12px;">
                            <option value="">-- Tidak Terkait Anggaran --</option>
                            @foreach($budgets as $budget)
                                @if($budget->allocated_amount - $budget->used_amount > 0)
                                    <option value="{{ $budget->id }}">
                                        {{ $budget->description ?? 'Periode '.$budget->period }} 
                                        (Sisa: Rp {{ number_format($budget->allocated_amount - $budget->used_amount, 0, ',', '.') }})
                                    </option>
                                @endif
                            @endforeach
                        </select>
                        <small class="text-muted mt-1 d-block">Pilih jika pengeluaran ini memotong anggaran yang telah dialokasikan pusat.</small>
                    </div>

                    <div class="mb-3">
                        <label class="form-label" style="font-weight:600;">Jumlah (Rp)</label>
                        <input type="number" name="amount" class="form-control" required min="0" style="border-radius:12px;">
                    </div>
                    <div class="mb-3">
                        <label class="form-label" style="font-weight:600;">Tanggal</label>
                        <input type="date" name="transaction_date" class="form-control" required value="{{ date('Y-m-d') }}" style="border-radius:12px;">
                    </div>
                    <div class="mb-3">
                        <label class="form-label" style="font-weight:600;">Kategori</label>
                        <input type="text" name="kategori" class="form-control" placeholder="Contoh: Operasional, Pembelian Bibit" style="border-radius:12px;">
                    </div>
                    <div class="mb-3">
                        <label class="form-label" style="font-weight:600;">Deskripsi</label>
                        <textarea name="description" class="form-control" rows="3" required style="border-radius:12px;"></textarea>
                    </div>
                </div>
                <div class="modal-footer border-0 pt-0">
                    <button type="button" class="btn btn-outline" data-bs-dismiss="modal" style="border-radius:12px;">Batal</button>
                    <button type="submit" class="btn btn-primary" style="border-radius:12px; font-weight:600;">Simpan Transaksi</button>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const typeSelect = document.getElementById('financeTypeSelect');
        const budgetDiv = document.getElementById('budgetSelectDiv');
        
        typeSelect.addEventListener('change', function() {
            if (this.value === 'PENGELUARAN') {
                budgetDiv.style.display = 'block';
            } else {
                budgetDiv.style.display = 'none';
                budgetDiv.querySelector('select').value = '';
            }
        });
    });
</script>

<!-- Edit Finance Modals -->
@foreach($finances as $finance)
<div class="modal fade" id="editFinanceModal{{ $finance->id }}" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <form action="{{ route('admin.divisions.finances.update', [$division->id, $finance->id]) }}" method="POST" class="w-100">
            @csrf
            @method('PUT')
            <div class="modal-content glass-panel" style="border:none;">
                <div class="modal-header border-0 pb-0">
                    <h5 class="modal-title" style="font-weight:800;">Edit Transaksi Divisi</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label" style="font-weight:600;">Tipe Transaksi</label>
                        <select name="type" class="form-control" required style="border-radius:12px;" id="financeTypeSelectEdit{{ $finance->id }}" onchange="toggleBudgetEdit{{ $finance->id }}(this.value)">
                            <option value="PEMASUKAN" {{ $finance->type == 'PEMASUKAN' ? 'selected' : '' }}>Pemasukan (Masuk)</option>
                            <option value="PENGELUARAN" {{ $finance->type == 'PENGELUARAN' ? 'selected' : '' }}>Pengeluaran (Keluar)</option>
                        </select>
                    </div>
                    
                    <div class="mb-3" id="budgetSelectDivEdit{{ $finance->id }}" style="display: {{ $finance->type == 'PENGELUARAN' ? 'block' : 'none' }};">
                        <label class="form-label" style="font-weight:600;">Gunakan Dari Anggaran</label>
                        <select name="budget_id" class="form-control" style="border-radius:12px;">
                            <option value="">-- Tidak Terkait Anggaran --</option>
                            @foreach($budgets as $budget)
                                @php
                                    $sisa = $budget->allocated_amount - $budget->used_amount;
                                    // if this finance was using this budget, add it back to sisa
                                    if($finance->budget_id == $budget->id) {
                                        $sisa += $finance->amount;
                                    }
                                @endphp
                                @if($sisa > 0 || $finance->budget_id == $budget->id)
                                    <option value="{{ $budget->id }}" {{ $finance->budget_id == $budget->id ? 'selected' : '' }}>
                                        {{ $budget->description ?? 'Periode '.$budget->period }} 
                                        (Sisa Max: Rp {{ number_format($sisa, 0, ',', '.') }})
                                    </option>
                                @endif
                            @endforeach
                        </select>
                        <small class="text-muted mt-1 d-block">Pilih jika pengeluaran ini memotong anggaran yang telah dialokasikan pusat.</small>
                    </div>

                    <div class="mb-3">
                        <label class="form-label" style="font-weight:600;">Jumlah (Rp)</label>
                        <input type="number" name="amount" class="form-control" required min="0" value="{{ $finance->amount }}" style="border-radius:12px;">
                    </div>
                    <div class="mb-3">
                        <label class="form-label" style="font-weight:600;">Tanggal</label>
                        <input type="date" name="transaction_date" class="form-control" required value="{{ \Carbon\Carbon::parse($finance->transaction_date)->format('Y-m-d') }}" style="border-radius:12px;">
                    </div>
                    <div class="mb-3">
                        <label class="form-label" style="font-weight:600;">Kategori</label>
                        <input type="text" name="kategori" class="form-control" value="{{ $finance->kategori }}" placeholder="Contoh: Operasional, Pembelian Bibit" style="border-radius:12px;">
                    </div>
                    <div class="mb-3">
                        <label class="form-label" style="font-weight:600;">Deskripsi</label>
                        <textarea name="description" class="form-control" rows="3" required style="border-radius:12px;">{{ $finance->description }}</textarea>
                    </div>
                </div>
                <div class="modal-footer border-0 pt-0">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal" style="border-radius:12px;">Batal</button>
                    <button type="submit" class="btn btn-primary" style="border-radius:12px; font-weight:600;">Simpan Perubahan</button>
                </div>
            </div>
        </form>
    </div>
</div>
<script>
    function toggleBudgetEdit{{ $finance->id }}(type) {
        var budgetDiv = document.getElementById('budgetSelectDivEdit{{ $finance->id }}');
        if (type === 'PENGELUARAN') {
            budgetDiv.style.display = 'block';
        } else {
            budgetDiv.style.display = 'none';
            budgetDiv.querySelector('select').value = '';
        }
    }
</script>
@endforeach

@endsection
