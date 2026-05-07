@extends('layouts.admin')
@section('admin_content')

<div class="animate-fade-in">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 style="font-weight:800;">Manajemen Kartu Digital</h2>
            <p class="text-muted mb-0">Lihat dan kelola kartu anggota yang sudah dibuat.</p>
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
                        <th class="px-4 py-3">No</th>
                        <th class="px-4 py-3">Anggota</th>
                        <th class="px-4 py-3">ID Member</th>
                        <th class="px-4 py-3">Status Kartu</th>
                        <th class="px-4 py-3">Tanggal Dibuat</th>
                        <th class="px-4 py-3 text-end">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($cards as $index => $card)
                    <tr>
                        <td class="px-4 py-3">{{ $index + 1 }}</td>
                        <td class="px-4 py-3">
                            <div style="font-weight:600;">{{ $card->user->name ?? 'Tidak Diketahui' }}</div>
                            <small class="text-muted">{{ $card->user->email ?? '' }}</small>
                        </td>
                        <td class="px-4 py-3">
                            <span class="badge bg-primary">{{ $card->user->member_id ?? '-' }}</span>
                        </td>
                        <td class="px-4 py-3">
                            @if($card->status == 'active')
                                <span class="badge bg-success">Aktif</span>
                            @else
                                <span class="badge bg-secondary">{{ ucfirst($card->status) }}</span>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-muted">{{ $card->created_at->format('d M Y H:i') }}</td>
                        <td class="px-4 py-3 text-end">
                            <a href="{{ route('admin.card_preview', $card->user_id) }}" class="btn btn-sm btn-outline-primary" style="border-radius:8px;">
                                Lihat Kartu
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center py-5 text-muted">Belum ada kartu digital yang dibuat.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-3">
        {{ $cards->links() }}
    </div>
</div>
@endsection