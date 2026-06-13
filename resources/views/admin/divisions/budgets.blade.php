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
            <h2 style="font-weight:900; font-size:2.5rem; margin-bottom:8px; letter-spacing:-0.5px;">Pusat Anggaran</h2>
            <div class="d-flex gap-3 align-items-center">
                <span class="badge" style="background:rgba(255,255,255,0.1); border:1px solid rgba(255,255,255,0.2); padding:6px 12px; font-size:12px; letter-spacing:1px; color:#4ade80;">Divisi {{ $division->name }}</span>
            </div>
        </div>
        @if(auth()->user()->can_allocate_budgets || auth()->user()->role === 'admin' || auth()->user()->role === 'superadmin')
        <div style="position:relative; z-index:2;">
            <button class="btn" data-bs-toggle="modal" data-bs-target="#addBudgetModal" style="background:linear-gradient(135deg, #22c55e, #10b981); color:white; border:none; border-radius:14px; font-weight:700; padding:12px 24px; box-shadow:0 10px 25px rgba(34,197,94,0.4); transition:all 0.3s ease;">+ Alokasikan Anggaran</button>
        </div>
        @endif
    </div>

    @php
        $totalAllocated = $budgets->sum('allocated_amount');
        $totalUsed = $budgets->sum('used_amount');
        $totalRemaining = $totalAllocated - $totalUsed;
    @endphp

    <div class="row mb-4 g-4">
        <div class="col-md-4">
            <div class="card border-0 glass-panel h-100" style="border-left:4px solid #3b82f6 !important;">
                <div class="card-body p-4">
                    <p class="text-muted mb-1 font-weight-bold">Total Anggaran Dialokasikan</p>
                    <h3 style="font-weight:800; color:#2563eb;">Rp {{ number_format($totalAllocated, 0, ',', '.') }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 glass-panel h-100" style="border-left:4px solid #ef4444 !important;">
                <div class="card-body p-4">
                    <p class="text-muted mb-1 font-weight-bold">Total Anggaran Digunakan</p>
                    <h3 style="font-weight:800; color:#dc2626;">Rp {{ number_format($totalUsed, 0, ',', '.') }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 glass-panel h-100" style="border-left:4px solid #22c55e !important;">
                <div class="card-body p-4">
                    <p class="text-muted mb-1 font-weight-bold">Sisa Anggaran Tersedia</p>
                    <h3 style="font-weight:800; color:#16a34a;">Rp {{ number_format($totalRemaining, 0, ',', '.') }}</h3>
                </div>
            </div>
        </div>
    </div>

    <div class="glass-panel p-0 overflow-hidden mb-4">
        <div class="p-4" style="background:rgba(248,250,252,0.5); border-bottom:1px solid rgba(0,0,0,0.05);">
            <h4 style="font-weight:700; margin:0;">Riwayat Alokasi Anggaran</h4>
        </div>
        <div class="table-responsive">
            <table class="table-modern">
                <thead>
                    <tr>
                        <th>Tanggal/Periode</th>
                        <th>Deskripsi</th>
                        <th class="text-end">Alokasi (Rp)</th>
                        <th class="text-end">Digunakan (Rp)</th>
                        <th class="text-end">Sisa (Rp)</th>
                        <th class="text-center">Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($budgets as $budget)
                    <tr>
                        <td>
                            <div style="font-weight:700; color:var(--text-color);">{{ \Carbon\Carbon::parse($budget->created_at)->format('d M Y') }}</div>
                            <div style="font-size:12px; color:var(--text-muted);">Periode: {{ $budget->period ?? '-' }}</div>
                        </td>
                        <td>{{ $budget->description ?? '-' }}</td>
                        <td class="text-end" style="font-weight:600; color:#3b82f6;">{{ number_format($budget->allocated_amount, 0, ',', '.') }}</td>
                        <td class="text-end" style="font-weight:600; color:#ef4444;">{{ number_format($budget->used_amount, 0, ',', '.') }}</td>
                        <td class="text-end" style="font-weight:700; color:#22c55e;">{{ number_format($budget->allocated_amount - $budget->used_amount, 0, ',', '.') }}</td>
                        <td class="text-center">
                            @if($budget->used_amount >= $budget->allocated_amount)
                                <span class="badge" style="background:rgba(239,68,68,0.1); color:#ef4444;">Habis</span>
                            @else
                                <span class="badge" style="background:rgba(34,197,94,0.1); color:#22c55e;">Tersedia</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center py-5 text-muted">Belum ada alokasi anggaran untuk divisi ini.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

@if(auth()->user()->can_allocate_budgets || auth()->user()->role === 'admin' || auth()->user()->role === 'superadmin')
<!-- Modal Tambah Anggaran -->
<div class="modal fade" id="addBudgetModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <form action="{{ route('admin.divisions.budgets.store', $division->id) }}" method="POST" class="w-100">
            @csrf
            <div class="modal-content glass-panel" style="border:none;">
                <div class="modal-header border-0 pb-0">
                    <h5 class="modal-title" style="font-weight:800;">Alokasikan Anggaran Baru</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label" style="font-weight:600;">Jumlah Alokasi (Rp)</label>
                        <input type="number" name="allocated_amount" class="form-control" required min="0" style="border-radius:12px;">
                    </div>
                    <div class="mb-3">
                        <label class="form-label" style="font-weight:600;">Periode (Opsional)</label>
                        <input type="text" name="period" class="form-control" placeholder="Contoh: Juni 2026 atau Q3 2026" style="border-radius:12px;">
                    </div>
                    <div class="mb-3">
                        <label class="form-label" style="font-weight:600;">Deskripsi/Catatan</label>
                        <textarea name="description" class="form-control" rows="3" style="border-radius:12px;"></textarea>
                    </div>
                </div>
                <div class="modal-footer border-0 pt-0">
                    <button type="button" class="btn btn-outline" data-bs-dismiss="modal" style="border-radius:12px;">Batal</button>
                    <button type="submit" class="btn btn-primary" style="border-radius:12px; font-weight:600;">Simpan Alokasi</button>
                </div>
            </div>
        </form>
    </div>
</div>
@endif
@endsection
