@extends('layouts.admin')

@section('admin_content')
<style>
    .glass-header { background:linear-gradient(135deg, #1e293b, #0f172a); color:white; border-radius:24px; padding:2.5rem; position:relative; overflow:hidden; box-shadow:0 20px 40px rgba(0,0,0,0.1); margin-bottom:2rem; }
    .glass-header::before { content:''; position:absolute; top:-50%; right:-10%; width:300px; height:300px; background:radial-gradient(circle, rgba(34,197,94,0.15) 0%, transparent 70%); border-radius:50%; }
    .glass-panel { background:rgba(255,255,255,0.7); backdrop-filter:blur(12px); border:1px solid rgba(255,255,255,0.5); border-radius:24px; box-shadow:0 15px 35px rgba(0,0,0,0.05); }
    
    .nav-pills .nav-link { border-radius:30px; font-weight:700; padding:12px 28px; color:var(--text-secondary); transition:all 0.3s ease; margin-right:8px; border:1px solid transparent; background:rgba(255,255,255,0.5); }
    .nav-pills .nav-link:hover { background:rgba(34,197,94,0.05); color:#22c55e; }
    .nav-pills .nav-link.active { background:linear-gradient(135deg, #22c55e, #10b981); color:white; box-shadow:0 8px 20px rgba(34,197,94,0.3); border-color:transparent; }
    
    .table-modern { width:100%; border-collapse:collapse; }
    .table-modern th { padding:16px 20px; font-weight:700; color:var(--text-secondary); text-transform:uppercase; font-size:12px; letter-spacing:0.5px; border-bottom:2px solid rgba(0,0,0,0.05); }
    .table-modern td { padding:16px 20px; border-bottom:1px solid rgba(0,0,0,0.05); vertical-align:middle; }
    .table-modern tr:hover { background:rgba(34,197,94,0.03); }
</style>

<div class="animate-fade-in">
    <div class="glass-header d-flex flex-wrap justify-content-between align-items-center gap-4">
        <div style="position:relative; z-index:2;">
            <a href="{{ route('admin.divisions') }}" class="text-decoration-none text-muted mb-3 d-inline-block" style="color:rgba(255,255,255,0.6) !important; font-weight:600; font-size:13px; letter-spacing:0.5px;">&larr; KEMBALI KE SEMUA DIVISI</a>
            <h2 style="font-weight:900; font-size:2.5rem; margin-bottom:8px; letter-spacing:-0.5px;">{{ $division->name }}</h2>
            <div class="d-flex gap-3 align-items-center">
                <span class="badge" style="background:rgba(255,255,255,0.1); border:1px solid rgba(255,255,255,0.2); padding:6px 12px; font-size:12px; letter-spacing:1px; color:#4ade80;">{{ $division->type }}</span>
                <span style="font-size:14px; font-weight:600; color:rgba(255,255,255,0.8);">👥 {{ $division->assignedUsers->count() }} Anggota Aktif</span>
            </div>
        </div>
        <div class="d-flex gap-3 flex-wrap" style="position:relative; z-index:2;">
            <a href="{{ route('admin.divisions.budgets', $division->id) }}" class="btn" style="background:rgba(255,255,255,0.1); color:white; border:1px solid rgba(255,255,255,0.2); border-radius:14px; font-weight:700; padding:12px 24px; backdrop-filter:blur(5px); text-decoration:none; transition:all 0.3s ease;">📊 Pusat Anggaran</a>
            <a href="{{ route('admin.divisions.finances', $division->id) }}" class="btn" style="background:linear-gradient(135deg, #22c55e, #10b981); color:white; border:none; border-radius:14px; font-weight:700; padding:12px 24px; text-decoration:none; box-shadow:0 10px 25px rgba(34,197,94,0.4); transition:all 0.3s ease;">💰 Keuangan Divisi</a>
        </div>
    </div>

    <!-- Nav Tabs untuk Fitur Divisi -->
    <ul class="nav nav-pills mb-4 gap-2" id="divisionTabs" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link active" id="overview-tab" data-bs-toggle="pill" data-bs-target="#overview" type="button" role="tab" style="border-radius:10px;">Ringkasan</button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="members-tab" data-bs-toggle="pill" data-bs-target="#members" type="button" role="tab" style="border-radius:10px;">Anggota / Pegawai</button>
        </li>

        @if(in_array($division->type, ['FARM', 'LIVESTOCK', 'PRODUCTION']))
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="inventory-tab" data-bs-toggle="pill" data-bs-target="#inventory" type="button" role="tab" style="border-radius:10px;">Gudang / Inventaris</button>
        </li>
        @endif

        @if(in_array($division->type, ['LIVESTOCK']))
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="livestock-tab" data-bs-toggle="pill" data-bs-target="#livestock" type="button" role="tab" style="border-radius:10px;">Data Ternak</button>
        </li>
        @endif

        @if(in_array($division->type, ['FARM', 'PRODUCTION']))
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="production-tab" data-bs-toggle="pill" data-bs-target="#production" type="button" role="tab" style="border-radius:10px;">Batch Produksi</button>
        </li>
        @endif

        @if($division->type == 'CAFE')
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="pos-tab" data-bs-toggle="pill" data-bs-target="#pos" type="button" role="tab" style="border-radius:10px;">Menu & Penjualan Kafe</button>
        </li>
        @endif
    </ul>

    <!-- Tab Contents -->
    <div class="tab-content" id="divisionTabsContent">
        <!-- TAB: OVERVIEW -->
        <div class="tab-pane fade show active" id="overview" role="tabpanel">
            <div class="glass-panel p-5">
                <h4 class="fw-bold mb-4" style="color:#1e293b;">Ringkasan Divisi</h4>
                <p style="color:#475569; font-size:15px; line-height:1.7;">{{ $division->description ?: 'Belum ada deskripsi khusus untuk divisi ini.' }}</p>
                
                <div class="row mt-5">
                    <div class="col-md-4 mb-3">
                        <div style="background:white; border-radius:20px; padding:24px; box-shadow:0 10px 30px rgba(0,0,0,0.02); border:1px solid rgba(0,0,0,0.03);">
                            <div class="text-muted small fw-bold text-uppercase mb-2" style="letter-spacing:1px;">Alokasi Budget Sisa</div>
                            <h2 class="fw-bold text-success m-0">Rp 0</h2>
                        </div>
                    </div>
                    <div class="col-md-4 mb-3">
                        <div style="background:white; border-radius:20px; padding:24px; box-shadow:0 10px 30px rgba(0,0,0,0.02); border:1px solid rgba(0,0,0,0.03);">
                            <div class="text-muted small fw-bold text-uppercase mb-2" style="letter-spacing:1px;">Total Pengeluaran</div>
                            <h2 class="fw-bold text-danger m-0">Rp 0</h2>
                        </div>
                    </div>
                    <div class="col-md-4 mb-3">
                        <div style="background:white; border-radius:20px; padding:24px; box-shadow:0 10px 30px rgba(0,0,0,0.02); border:1px solid rgba(0,0,0,0.03);">
                            <div class="text-muted small fw-bold text-uppercase mb-2" style="letter-spacing:1px;">Total Pemasukan</div>
                            <h2 class="fw-bold text-primary m-0">Rp 0</h2>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- TAB: MEMBERS -->
        <div class="tab-pane fade" id="members" role="tabpanel">
            <div class="d-flex justify-content-end mb-4">
                <button type="button" class="btn" data-bs-toggle="modal" data-bs-target="#assignMemberModal" style="background:linear-gradient(135deg, #3b82f6, #2563eb); color:white; border-radius:30px; font-weight:700; padding:12px 24px; box-shadow:0 8px 20px rgba(59,130,246,0.3);">
                    + Tarik Anggota ke Divisi
                </button>
            </div>
            
            <div class="glass-panel p-0 overflow-hidden">
                <div class="table-responsive">
                    <table class="table-modern">
                        <thead style="background:rgba(248,250,252,0.8);">
                            <tr>
                                <th>Profil Anggota</th>
                                <th>Jabatan Spesifik Divisi</th>
                                <th class="text-center">Akses Kelola (Admin)</th>
                                <th class="text-end">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($division->assignedUsers as $user)
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center gap-3">
                                        <div style="width:40px; height:40px; border-radius:50%; background:#e2e8f0; color:#475569; display:flex; align-items:center; justify-content:center; font-weight:bold; font-size:16px;">
                                            {{ strtoupper(substr($user->name, 0, 1)) }}
                                        </div>
                                        <div>
                                            <div style="font-weight:800; color:#1e293b;">{{ $user->name }}</div>
                                            <div style="font-size:12px; color:#64748b;">ID: {{ $user->member_id ?? '-' }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <span style="font-weight:600; color:#334155;">{{ $user->pivot->jabatan ?? 'Anggota Biasa' }}</span>
                                </td>
                                <td class="text-center">
                                    {!! $user->pivot->is_admin ? '<span style="background:rgba(34,197,94,0.1); color:#16a34a; padding:6px 12px; border-radius:30px; font-size:12px; font-weight:700;">✅ Ya (Admin)</span>' : '<span style="background:rgba(100,116,139,0.1); color:#64748b; padding:6px 12px; border-radius:30px; font-size:12px; font-weight:700;">Tidak</span>' !!}
                                </td>
                                <td class="text-end">
                                    <form action="{{ route('admin.divisions.remove', [$division->id, $user->id]) }}" method="POST" class="d-inline" onsubmit="return confirm('Keluarkan anggota ini dari divisi?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" style="background:rgba(239,68,68,0.1); color:#ef4444; border:none; padding:8px 16px; border-radius:10px; font-weight:600; transition:all 0.3s ease;">Keluarkan</button>
                                    </form>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="4" class="text-center py-5">
                                    <div style="font-size:3rem; margin-bottom:1rem; opacity:0.5;">🤝</div>
                                    <h5 style="font-weight:700; color:#64748b;">Belum Ada Anggota</h5>
                                    <p class="text-muted">Klik tombol "Tarik Anggota" di atas untuk menambahkan tim ke divisi ini.</p>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        @if(in_array($division->type, ['FARM', 'LIVESTOCK', 'PRODUCTION']))
        <!-- TAB: INVENTORY -->
        <div class="tab-pane fade" id="inventory" role="tabpanel">
            <div class="glass-panel p-5 text-center">
                <h4 class="fw-bold mb-3" style="color:#1e293b;">📦 Gudang & Inventaris</h4>
                <p class="text-muted">Fitur ini sedang disiapkan (Coming Soon). Anda akan bisa mengelola stok pakan, benih, dan alat-alat di sini.</p>
            </div>
        </div>
        @endif
        
        @if(in_array($division->type, ['LIVESTOCK']))
        <!-- TAB: LIVESTOCK -->
        <div class="tab-pane fade" id="livestock" role="tabpanel">
            <div class="glass-panel p-5 text-center">
                <h4 class="fw-bold mb-3" style="color:#1e293b;">🐄 Manajemen Ternak</h4>
                <p class="text-muted">Fitur ini sedang disiapkan (Coming Soon). Pantau siklus hidup dan kesehatan ternak/maggot.</p>
            </div>
        </div>
        @endif

        @if(in_array($division->type, ['FARM', 'PRODUCTION']))
        <!-- TAB: PRODUCTION -->
        <div class="tab-pane fade" id="production" role="tabpanel">
            <div class="glass-panel p-5 text-center">
                <h4 class="fw-bold mb-3" style="color:#1e293b;">🏭 Batch Panen & Produksi</h4>
                <p class="text-muted">Fitur ini sedang disiapkan (Coming Soon). Catat setiap sesi panen dan konversi sampah ke produk jadi di sini.</p>
            </div>
        </div>
        @endif

        @if($division->type == 'CAFE')
        <!-- TAB: POS -->
        <div class="tab-pane fade" id="pos" role="tabpanel">
            <div class="glass-panel p-5 text-center">
                <h4 class="fw-bold mb-3" style="color:#1e293b;">☕ Point of Sale (Kasir Kafe)</h4>
                <p class="text-muted">Fitur Kasir akan terbuka di akun pegawai. Di tab ini, Anda dapat memantau produk terjual dan grafik pemasukan harian.</p>
            </div>
        </div>
        @endif

    </div>
</div>

<!-- Modal Tarik Anggota -->
<div class="modal fade" id="assignMemberModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <form action="{{ route('admin.divisions.assign', $division->id) }}" method="POST" class="w-100">
            @csrf
            <div class="modal-content glass-panel" style="border-radius:24px; border:none; box-shadow:0 30px 60px rgba(0,0,0,0.15);">
                <div class="modal-header border-0 pb-0" style="padding:2rem 2rem 1rem;">
                    <h4 class="modal-title" style="font-weight:800; color:#1e293b;">Tarik Anggota ke Divisi</h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body" style="padding:1rem 2rem 2rem;">
                    <div class="mb-4">
                        <label class="form-label" style="font-weight:700; color:#475569; font-size:13px; text-transform:uppercase;">Pilih Kandidat (Anggota)</label>
                        <select name="user_id" class="form-select" required style="border-radius:14px; padding:12px; background:#f8fafc; border:1px solid rgba(0,0,0,0.1);">
                            <option value="">-- Ketik / Pilih Anggota --</option>
                            @foreach($availableUsers as $u)
                                <option value="{{ $u->id }}">{{ $u->name }} (ID: {{ $u->member_id ?? '-' }})</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-4">
                        <label class="form-label" style="font-weight:700; color:#475569; font-size:13px; text-transform:uppercase;">Jabatan Spesifik (Peran)</label>
                        <input type="text" name="jabatan" class="form-control" placeholder="Cth: Kepala Kebun, Kasir Utama, Staf..." required style="border-radius:14px; padding:12px; background:#f8fafc; border:1px solid rgba(0,0,0,0.1);">
                    </div>
                    <div class="p-3" style="background:rgba(34,197,94,0.05); border:1px solid rgba(34,197,94,0.2); border-radius:16px;">
                        <div class="form-check d-flex align-items-center gap-2 m-0">
                            <input class="form-check-input" type="checkbox" name="is_admin" value="1" id="isAdminCheck" style="width:20px; height:20px; cursor:pointer;">
                            <label class="form-check-label" for="isAdminCheck" style="font-weight:600; color:#16a34a; cursor:pointer; padding-top:3px;">
                                Jadikan Admin Divisi Ini (Bisa kelola anggaran)
                            </label>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-0" style="padding:0 2rem 2rem;">
                    <button type="submit" class="btn w-100" style="background:linear-gradient(135deg, #22c55e, #10b981); color:white; font-weight:800; padding:14px; border-radius:16px; border:none; box-shadow:0 10px 20px rgba(34,197,94,0.3);">🚀 Tetapkan Anggota Sekarang</button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection
