@extends('layouts.admin')
@section('admin_content')

<div class="animate-fade-in">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 style="font-weight:800;">Manajemen Setoran Limbah</h2>
            <p class="text-muted mb-0">Kelola laporan setoran dari anggota.</p>
        </div>
    </div>

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
                            @if($deposit->image_path)
                                <a href="{{ asset('storage/' . $deposit->image_path) }}" target="_blank">
                                    <img src="{{ asset('storage/' . $deposit->image_path) }}" style="width:50px;height:50px;object-fit:cover;border-radius:8px;">
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
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center py-5 text-muted">Belum ada laporan setoran.</td>
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
@endsection