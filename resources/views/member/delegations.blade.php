@extends('layouts.app')

@section('title', 'Pendelegasian Akses')

@section('content')
<div class="container mt-4 animate-fade-in">
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
        <div>
            <h2 class="mb-0" style="font-weight: 800; color: var(--text-color);">Beri Hak Akses</h2>
            <p class="text-muted mb-0">Oper izin atau delegasikan hak kelola kepada anggota lain.</p>
        </div>
        <div class="d-flex gap-2">
            <button class="btn btn-primary fw-bold" style="border-radius: 12px;" data-bs-toggle="modal" data-bs-target="#addDelegationModal">+ Buat Delegasi Baru</button>
            <a href="{{ route('dashboard') }}" class="btn btn-outline" style="border-radius: 12px;">Kembali</a>
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
                        <th class="py-3 px-4 border-0">Waktu</th>
                        <th class="py-3 px-4 border-0">Anggota Penerima</th>
                        <th class="py-3 px-4 border-0">Hak Akses Diberikan</th>
                        <th class="py-3 px-4 border-0">Cakupan (Scope)</th>
                        <th class="py-3 px-4 border-0">Status ACC Admin</th>
                        <th class="py-3 px-4 border-0 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($myDelegations as $del)
                    <tr>
                        <td class="py-3 px-4 text-muted">{{ $del->created_at->format('d M Y') }}</td>
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
                                <span class="badge bg-success">Disetujui / Aktif</span>
                            @else
                                <span class="badge bg-danger">Ditolak Admin</span>
                            @endif
                        </td>
                        <td class="py-3 px-4 text-center">
                            <form action="{{ route('member.delegations.revoke', $del->id) }}" method="POST">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-outline-danger" style="border-radius: 8px;" onclick="return confirm('Cabut hak akses ini?')">Cabut Akses</button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center py-5 text-muted">Anda belum pernah mendelegasikan hak akses ke siapapun.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal Buat Delegasi -->
<div class="modal fade" id="addDelegationModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <form action="{{ route('member.delegations.store') }}" method="POST" class="modal-content glass-panel" style="border-radius:24px; border:none;">
            @csrf
            <div class="modal-header border-0 pb-0" style="padding:2rem 2rem 1rem;">
                <h4 class="modal-title" style="font-weight:800; color:#1e293b;">Beri Hak Akses ke Anggota</h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" style="padding:1rem 2rem 2rem;">
                <div class="alert alert-info" style="border-radius: 12px; font-size: 0.9rem;">
                    <strong>Penting:</strong> Anda hanya bisa mendelegasikan hak akses yang saat ini Anda miliki secara sah.
                </div>
                
                <div class="mb-3">
                    <label class="form-label" style="font-weight:700;">Pilih Anggota Penerima</label>
                    <select name="delegatee_id" class="form-select" required style="border-radius:12px;">
                        <option value="">Pilih Anggota...</option>
                        @foreach($users as $u)
                            <option value="{{ $u->id }}">{{ $u->name }} ({{ $u->jabatan ?? 'Anggota' }})</option>
                        @endforeach
                    </select>
                </div>
                
                <div class="mb-3">
                    <label class="form-label" style="font-weight:700;">Hak Akses Yang Dioper</label>
                    <select name="permission" class="form-select" required style="border-radius:12px;">
                        @if(auth()->user()->can_manage_finances)
                            <option value="can_manage_finances">Mengelola Keuangan (Input Pemasukan/Pengeluaran/Hutang)</option>
                        @endif
                        @if(auth()->user()->can_allocate_budgets)
                            <option value="can_allocate_budgets">Mengalokasikan Anggaran (Budget Divisi)</option>
                        @endif
                        @if(auth()->user()->can_manage_members)
                            <option value="can_manage_members">Mengelola Anggota Divisi (Menerima/Mengeluarkan Anggota)</option>
                        @endif
                    </select>
                </div>

                <div class="mb-3">
                    <label class="form-label" style="font-weight:700;">Cakupan Wewenang</label>
                    <select name="scope" class="form-select" required style="border-radius:12px;">
                        <option value="division">Hanya Divisi Sendiri</option>
                        <option value="community">Global (Seluruh Komunitas)</option>
                    </select>
                </div>

                <div class="mb-3 form-check form-switch mt-4">
                    <input class="form-check-input" type="checkbox" role="switch" id="requiresAcc" name="requires_approval" value="1" checked>
                    <label class="form-check-label ms-2" for="requiresAcc" style="font-weight:700;">Minta ACC Super Admin</label>
                    <small class="d-block text-muted">Jika dimatikan, akses akan langsung diberikan. Sebaiknya nyalakan untuk keamanan.</small>
                </div>
            </div>
            <div class="modal-footer border-0" style="padding:0 2rem 2rem;">
                <button type="submit" class="btn btn-primary w-100" style="border-radius:12px; font-weight:700; padding:12px;">Delegasikan Sekarang</button>
            </div>
        </form>
    </div>
</div>
@endsection
