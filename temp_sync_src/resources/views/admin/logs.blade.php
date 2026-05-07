@extends('layouts.admin')
@section('admin_content')

<div class="animate-fade-in">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 style="font-weight:800;">Activity Log</h2>
            <p class="text-muted mb-0">Riwayat aktivitas admin dan anggota.</p>
        </div>
    </div>

    <div class="card glass p-0 overflow-hidden" style="border-radius:20px;">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead style="background:rgba(var(--primary-rgb),0.05);">
                    <tr>
                        <th class="px-4 py-3">Waktu</th>
                        <th class="px-4 py-3">User</th>
                        <th class="px-4 py-3">Aksi</th>
                        <th class="px-4 py-3">Deskripsi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($logs as $log)
                    <tr>
                        <td class="px-4 py-3 text-muted">{{ $log->created_at->format('d M Y H:i') }}</td>
                        <td class="px-4 py-3">
                            @if($log->user)
                                <span style="font-weight:600;">{{ $log->user->name }}</span>
                                <br>
                                <small class="text-muted">{{ $log->user->email }}</small>
                            @else
                                <span class="text-muted">System / Guest</span>
                            @endif
                        </td>
                        <td class="px-4 py-3">
                            <span class="badge" style="background:rgba(var(--primary-rgb),0.1); color:var(--primary);">
                                {{ $log->action }}
                            </span>
                        </td>
                        <td class="px-4 py-3">{{ $log->description }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="text-center py-5 text-muted">Belum ada aktivitas tercatat.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-3">
        {{ $logs->links() }}
    </div>
</div>
@endsection