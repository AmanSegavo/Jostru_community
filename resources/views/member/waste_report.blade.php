@extends('layouts.app')

@section('title', 'Lapor Setoran Limbah')

@section('content')
<div class="container mt-4 animate-fade-in">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 style="font-weight: 800;">Setoran Limbah Saya</h2>
        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#reportModal" style="border-radius: 12px; background: #22c55e; border: none;">
            + Lapor Setoran Baru
        </button>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show mb-4" role="alert" style="border-radius: 15px; background: rgba(34, 197, 94, 0.1); color: #22c55e; border: 1px solid rgba(34, 197, 94, 0.2);">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="card glass p-0 overflow-hidden" style="border-radius: 20px;">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead style="background: rgba(var(--primary-rgb), 0.05);">
                    <tr>
                        <th class="px-4 py-3">Tanggal</th>
                        <th class="px-4 py-3">Jenis Limbah</th>
                        <th class="px-4 py-3">Berat (kg)</th>
                        <th class="px-4 py-3">Status</th>
                        <th class="px-4 py-3">Keterangan</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($reports as $report)
                    <tr>
                        <td class="px-4 py-3">{{ $report->created_at->format('d M Y H:i') }}</td>
                        <td class="px-4 py-3"><span class="badge" style="background: rgba(var(--primary-rgb), 0.1); color: var(--primary);">{{ $report->type }}</span></td>
                        <td class="px-4 py-3" style="font-weight: 700;">{{ $report->weight }} kg</td>
                        <td class="px-4 py-3">
                            @if($report->status == 'APPROVED')
                                <span class="badge bg-success" style="background: #22c55e !important;">DISETUJUI</span>
                            @elseif($report->status == 'REJECTED')
                                <span class="badge bg-danger">DITOLAK</span>
                            @else
                                <span class="badge bg-warning text-dark">PENDING</span>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-muted">{{ $report->description ?? '-' }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="text-center py-5 text-muted">Belum ada riwayat setoran. Mulai kontribusi Anda hari ini!</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal Lapor Setoran -->
<div class="modal fade" id="reportModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content glass" style="border-radius: 24px; border: 1px solid rgba(255,255,255,0.2);">
            <div class="modal-header border-0">
                <h5 class="modal-title" style="font-weight: 800;">Kirim Laporan Setoran</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('member.waste_report.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label" style="font-weight: 600;">Jenis Limbah</label>
                        <select name="type" class="form-control" required style="border-radius: 12px;">
                            <option value="Organik">Organik (Sisa Makanan, Daun, dll)</option>
                            <option value="Anorganik">Anorganik (Plastik, Kertas, Logam)</option>
                            <option value="B3">B3 (Baterai, Elektronik, Bahan Kimia)</option>
                            <option value="Lainnya">Lainnya</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label" style="font-weight: 600;">Estimasi Berat (kg)</label>
                        <input type="number" name="weight" step="0.1" class="form-control" required placeholder="Contoh: 2.5" style="border-radius: 12px;">
                    </div>
                    <div class="mb-3">
                        <label class="form-label" style="font-weight: 600;">Keterangan Tambahan</label>
                        <textarea name="description" class="form-control" rows="3" placeholder="Detail limbah atau catatan untuk tim jemput..." style="border-radius: 12px;"></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label" style="font-weight: 600;">Foto Limbah (Opsional)</label>
                        <input type="file" name="image" class="form-control" style="border-radius: 12px;">
                    </div>
                </div>
                <div class="modal-footer border-0">
                    <button type="button" class="btn btn-outline" data-bs-dismiss="modal" style="border-radius: 12px;">Batal</button>
                    <button type="submit" class="btn btn-primary" style="border-radius: 12px; background: #22c55e; border: none; padding: 10px 25px; font-weight: 700;">Kirim Laporan</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
