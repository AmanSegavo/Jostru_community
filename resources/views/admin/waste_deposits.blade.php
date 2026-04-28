@extends('layouts.admin')

@section('admin_content')
<div class="animate-fade-in">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 style="font-weight: 800;">Kelola Setoran Limbah</h2>
        <div class="badge bg-primary" style="padding: 10px 20px; border-radius: 30px;">Total Laporan: {{ $deposits->total() }}</div>
    </div>

    @if(session('success'))
        <div class="alert alert-success border-0 shadow-sm mb-4" style="border-radius: 15px; background: rgba(34, 197, 94, 0.1); color: #22c55e;">
            {{ session('success') }}
        </div>
    @endif

    <div class="card glass p-0 overflow-hidden" style="border-radius: 20px; border: 1px solid rgba(var(--primary-rgb), 0.1);">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead style="background: rgba(var(--primary-rgb), 0.05);">
                    <tr>
                        <th class="px-4 py-3">Anggota</th>
                        <th class="px-4 py-3">Jenis</th>
                        <th class="px-4 py-3">Berat</th>
                        <th class="px-4 py-3">Status</th>
                        <th class="px-4 py-3">Tanggal</th>
                        <th class="px-4 py-3 text-end">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($deposits as $deposit)
                    <tr>
                        <td class="px-4 py-3">
                            <div class="d-flex align-items-center">
                                <div style="width: 35px; height: 35px; background: #22c55e; border-radius: 50%; display: flex; align-items: center; justify-content: center; color: white; font-weight: 700; margin-right: 12px; font-size: 0.8rem;">
                                    {{ substr($deposit->user->name, 0, 1) }}
                                </div>
                                <div>
                                    <div style="font-weight: 700;">{{ $deposit->user->name }}</div>
                                    <small class="text-muted">{{ $deposit->user->member_id }}</small>
                                </div>
                            </div>
                        </td>
                        <td class="px-4 py-3">
                            <span class="badge" style="background: rgba(var(--primary-rgb), 0.1); color: var(--primary);">{{ $deposit->type }}</span>
                        </td>
                        <td class="px-4 py-3" style="font-weight: 700;">{{ $deposit->weight }} kg</td>
                        <td class="px-4 py-3">
                            <form action="{{ route('admin.waste_deposits.status', $deposit->id) }}" method="POST" class="d-inline">
                                @csrf
                                @method('PUT')
                                <select name="status" onchange="this.form.submit()" class="form-select form-select-sm" style="width: auto; border-radius: 8px; font-weight: 600; {{ $deposit->status == 'APPROVED' ? 'color: #22c55e; border-color: #22c55e;' : ($deposit->status == 'REJECTED' ? 'color: #dc3545; border-color: #dc3545;' : 'color: #ffc107; border-color: #ffc107;') }}">
                                    <option value="PENDING" {{ $deposit->status == 'PENDING' ? 'selected' : '' }}>PENDING</option>
                                    <option value="APPROVED" {{ $deposit->status == 'APPROVED' ? 'selected' : '' }}>APPROVED</option>
                                    <option value="REJECTED" {{ $deposit->status == 'REJECTED' ? 'selected' : '' }}>REJECTED</option>
                                </select>
                            </form>
                        </td>
                        <td class="px-4 py-3 text-muted">{{ $deposit->created_at->format('d/m/Y H:i') }}</td>
                        <td class="px-4 py-3 text-end">
                            <form action="{{ route('admin.waste_deposits.destroy', $deposit->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Hapus data setoran ini?')">
                                @csrf
                                @method('DELETE')
                                <button class="btn btn-link text-danger p-0" title="Hapus">
                                    <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                </button>
                            </form>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="p-4">
            {{ $deposits->links() }}
        </div>
    </div>
</div>
@endsection
