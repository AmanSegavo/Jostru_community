@extends('layouts.admin')

@section('title', 'Persetujuan Delegasi Akses')

@section('admin_content')
<div class="container mt-4 animate-fade-in">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="mb-0" style="font-weight: 800; color: var(--text-color);">Persetujuan Delegasi Hak Akses</h2>
            <p class="text-muted mb-0">Kelola permintaan oper izin antar anggota / pengurus.</p>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    <div class="card p-0 glass border-0 overflow-hidden" style="border-radius: 16px;">
        <div class="table-responsive">
            <table class="table table-hover mb-0 align-middle">
                <thead style="background: rgba(var(--primary-rgb), 0.05);">
                    <tr>
                        <th class="py-3 px-4 border-0">Waktu Pengajuan</th>
                        <th class="py-3 px-4 border-0">Dari (Pemberi)</th>
                        <th class="py-3 px-4 border-0">Ke (Penerima)</th>
                        <th class="py-3 px-4 border-0">Hak Akses</th>
                        <th class="py-3 px-4 border-0">Cakupan</th>
                        <th class="py-3 px-4 border-0">Status</th>
                        <th class="py-3 px-4 border-0 text-center">Aksi (ACC)</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($delegations as $del)
                    <tr>
                        <td class="py-3 px-4 text-muted">{{ $del->created_at->format('d M Y H:i') }}</td>
                        <td class="py-3 px-4 fw-bold">{{ $del->delegator->name ?? 'User Hapus' }}</td>
                        <td class="py-3 px-4 fw-bold">{{ $del->delegatee->name ?? 'User Hapus' }}</td>
                        <td class="py-3 px-4">
                            <span class="badge bg-info text-dark">{{ str_replace('_', ' ', strtoupper($del->permission)) }}</span>
                        </td>
                        <td class="py-3 px-4">
                            @if($del->scope === 'community')
                                <span class="badge bg-primary">Global (Semua Divisi)</span>
                            @else
                                <span class="badge bg-secondary">Hanya Divisinya</span>
                            @endif
                        </td>
                        <td class="py-3 px-4">
                            @if($del->status === 'PENDING')
                                <span class="badge bg-warning text-dark">Menunggu ACC</span>
                            @elseif($del->status === 'APPROVED')
                                <span class="badge bg-success">Disetujui</span>
                            @else
                                <span class="badge bg-danger">Ditolak</span>
                            @endif
                        </td>
                        <td class="py-3 px-4 text-center">
                            @if($del->status === 'PENDING')
                                <div class="d-flex gap-2 justify-content-center">
                                    <form action="{{ route('admin.delegations.approve', $del->id) }}" method="POST">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-success" style="border-radius: 8px;" onclick="return confirm('Yakin ACC delegasi ini?')">ACC</button>
                                    </form>
                                    <form action="{{ route('admin.delegations.reject', $del->id) }}" method="POST">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-danger" style="border-radius: 8px;" onclick="return confirm('Tolak delegasi ini?')">Tolak</button>
                                    </form>
                                </div>
                            @else
                                <span class="text-muted">-</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center py-5 text-muted">Belum ada data pendelegasian hak akses.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
