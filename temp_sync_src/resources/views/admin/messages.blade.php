@extends('layouts.admin')
@section('admin_content')

<div class="animate-fade-in">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 style="font-weight:800;">Pesan Masuk (Kontak)</h2>
            <p class="text-muted mb-0">Daftar pesan yang dikirim melalui halaman kontak.</p>
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
                        <th class="px-4 py-3">Nama Pengirim</th>
                        <th class="px-4 py-3">Email</th>
                        <th class="px-4 py-3">Pesan</th>
                        <th class="px-4 py-3">Status</th>
                        <th class="px-4 py-3 text-end">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($messages as $msg)
                    <tr>
                        <td class="px-4 py-3 text-muted">{{ $msg->created_at->format('d M Y H:i') }}</td>
                        <td class="px-4 py-3 fw-bold">{{ $msg->name }}</td>
                        <td class="px-4 py-3">
                            <a href="mailto:{{ $msg->email }}" class="text-decoration-none">{{ $msg->email }}</a>
                        </td>
                        <td class="px-4 py-3" style="max-width:400px;">
                            {{ Str::limit($msg->message, 100) }}
                        </td>
                        <td class="px-4 py-3">
                            @if($msg->is_read)
                                <span class="badge bg-secondary">Sudah Dibaca</span>
                            @else
                                <span class="badge bg-warning text-dark">Belum Dibaca</span>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-end">
                            @if(!$msg->is_read)
                                <form action="{{ route('admin.messages.mark_read', $msg->id) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('PUT')
                                    <button type="submit" class="btn btn-sm btn-outline-primary" style="border-radius:8px;">
                                        Tandai Dibaca
                                    </button>
                                </form>
                            @endif
                            <a href="mailto:{{ $msg->email }}?subject=Balasan dari Jostru Community" class="btn btn-sm btn-outline-success ms-1" style="border-radius:8px;">
                                Balas
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center py-5 text-muted">Belum ada pesan masuk.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-3">
        {{ $messages->links() }}
    </div>
</div>
@endsection