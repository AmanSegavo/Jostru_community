@extends('layouts.admin')

@section('admin_content')
<style>
    .glass-card { background:rgba(255,255,255,0.7); backdrop-filter:blur(12px); border:1px solid rgba(255,255,255,0.5); border-radius:24px; box-shadow:0 15px 35px rgba(0,0,0,0.05); transition:all 0.3s ease; }
    .glass-card:hover { transform:translateY(-5px); box-shadow:0 25px 45px rgba(0,0,0,0.08); background:rgba(255,255,255,0.9); }
    
    .btn-gradient { background:linear-gradient(135deg, #22c55e, #10b981); color:white; font-weight:700; border:none; border-radius:30px; padding:12px 24px; box-shadow:0 8px 20px rgba(34,197,94,0.3); transition:all 0.3s ease; }
    .btn-gradient:hover { transform:translateY(-2px); box-shadow:0 12px 25px rgba(34,197,94,0.4); color:white; }
    
    .division-icon { width:48px; height:48px; border-radius:14px; display:flex; align-items:center; justify-content:center; font-size:24px; flex-shrink:0; }
    .icon-farm { background:rgba(34,197,94,0.1); color:#22c55e; }
    .icon-livestock { background:rgba(239,68,68,0.1); color:#ef4444; }
    .icon-cafe { background:rgba(245,158,11,0.1); color:#f59e0b; }
    .icon-production { background:rgba(59,130,246,0.1); color:#3b82f6; }
    .icon-general { background:rgba(99,102,241,0.1); color:#6366f1; }
</style>

<div class="animate-fade-in">
    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:2rem; flex-wrap:wrap; gap:1.5rem;">
        <div>
            <h2 style="margin:0; font-weight:900; font-size:2rem; letter-spacing:-0.5px;">Manajemen <span style="color:#22c55e;">Divisi</span></h2>
            <p style="color:var(--text-secondary); margin:4px 0 0 0;">Kelola seluruh unit usaha dan kelompok operasional Jostru.</p>
        </div>
        <button type="button" class="btn-gradient" data-bs-toggle="modal" data-bs-target="#addDivisionModal">
            + Tambah Divisi Baru
        </button>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show mb-4" style="border-radius:12px;">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="row">
        @forelse($divisions as $division)
        <div class="col-md-6 col-lg-4 mb-4">
            @php
                $iconClass = 'icon-general';
                $emoji = '🏢';
                if($division->type == 'FARM') { $iconClass = 'icon-farm'; $emoji = '🌱'; }
                elseif($division->type == 'LIVESTOCK') { $iconClass = 'icon-livestock'; $emoji = '🐄'; }
                elseif($division->type == 'CAFE') { $iconClass = 'icon-cafe'; $emoji = '☕'; }
                elseif($division->type == 'PRODUCTION') { $iconClass = 'icon-production'; $emoji = '🏭'; }
            @endphp
            
            <div class="glass-card h-100 d-flex flex-column p-4">
                <div class="d-flex justify-content-between align-items-start mb-4">
                    <div class="d-flex gap-3 align-items-center">
                        <div class="division-icon {{ $iconClass }}">{{ $emoji }}</div>
                        <div>
                            <h4 class="fw-bold mb-1" style="font-size:1.1rem; color:#1e293b;">{{ $division->name }}</h4>
                            <span class="badge" style="background:rgba(0,0,0,0.05); color:var(--text-secondary); font-size:10px; letter-spacing:0.5px;">{{ $division->type }}</span>
                        </div>
                    </div>
                    <div class="d-flex gap-2">
                        <button type="button" class="btn btn-sm" data-bs-toggle="modal" data-bs-target="#editDivisionModal{{ $division->id }}" style="background:none; border:none; color:#94a3b8; font-size:1.2rem; padding:0;" title="Edit Divisi">⚙️</button>
                        <form action="{{ route('admin.divisions.destroy', $division->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus divisi ini beserta semua data di dalamnya?');" class="m-0">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-sm" style="background:none; border:none; color:#ef4444; font-size:1.2rem; padding:0;" title="Hapus Divisi">🗑️</button>
                        </form>
                    </div>
                </div>
                
                <p class="text-muted small mb-4 flex-grow-1" style="display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; line-height:1.6;">
                    {{ $division->description ?: 'Belum ada deskripsi khusus untuk divisi ini.' }}
                </p>
                
                <div style="background:rgba(248,250,252,0.8); border-radius:16px; padding:12px; display:flex; justify-content:space-between; align-items:center; margin-bottom:1rem; border:1px solid rgba(0,0,0,0.02);">
                    <div style="display:flex; align-items:center; gap:8px;">
                        <div style="width:32px; height:32px; border-radius:50%; background:#22c55e; color:white; display:flex; justify-content:center; align-items:center; font-weight:bold; font-size:12px;">{{ $division->assigned_users_count }}</div>
                        <span style="font-size:13px; font-weight:600; color:#475569;">Anggota Aktif</span>
                    </div>
                    <a href="{{ route('admin.divisions.export') }}" title="Export CSV" style="color:#10b981; font-weight:bold; text-decoration:none; padding:4px 8px; border-radius:8px; background:rgba(16,185,129,0.1);">⬇️ CSV</a>
                </div>
                
                <a href="{{ route('admin.divisions.show', $division->id) }}" class="btn" style="width:100%; border-radius:14px; background:white; color:#334155; font-weight:700; border:2px solid rgba(0,0,0,0.05); transition:all 0.3s ease;"> Buka Ruang Kerja &rarr; </a>
            </div>
        </div>

        @empty
        <div class="col-12 text-center py-5">
            <div class="text-muted mb-3">Belum ada divisi yang dibuat.</div>
        </div>
        @endforelse
    </div>

    <div class="mt-3">
        {{ $divisions->links() }}
    </div>
</div>

<!-- Edit Modals -->
@foreach($divisions as $division)
<div class="modal fade" id="editDivisionModal{{ $division->id }}" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content glass" style="border-radius:24px;">
            <div class="modal-header border-0">
                <h5 class="modal-title fw-bold">Edit Divisi</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('admin.divisions.update', $division->id) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <div class="modal-body" style="max-height: 70vh; overflow-y: auto;">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Nama Divisi</label>
                            <input type="text" name="name" class="form-control" required value="{{ $division->name }}" style="border-radius:12px;">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Tipe Modul / Perkakas</label>
                            <select name="type" class="form-select" required style="border-radius:12px;">
                                <option value="GENERAL" {{ $division->type == 'GENERAL' ? 'selected' : '' }}>General (Default)</option>
                                <option value="FARM" {{ $division->type == 'FARM' ? 'selected' : '' }}>Pertanian (Farm)</option>
                                <option value="LIVESTOCK" {{ $division->type == 'LIVESTOCK' ? 'selected' : '' }}>Peternakan (Livestock)</option>
                                <option value="PRODUCTION" {{ $division->type == 'PRODUCTION' ? 'selected' : '' }}>Produksi (Pupuk/Pakan)</option>
                                <option value="CAFE" {{ $division->type == 'CAFE' ? 'selected' : '' }}>Kafe / Warung (POS)</option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label fw-bold">Deskripsi Singkat</label>
                        <textarea name="description" class="form-control" rows="2" style="border-radius:12px;">{{ $division->description }}</textarea>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">Logo Divisi (Opsional)</label>
                        <input type="file" name="logo" class="form-control" accept="image/*,.svg" style="border-radius:12px;">
                        <small class="text-muted">Format: JPG, PNG, atau SVG (Max 2MB).</small>
                        @if($division->logo)
                            <div class="mt-2">
                                <img src="{{ asset('media/divisions/' . $division->logo) }}" alt="Logo" style="height:40px; border-radius:8px;">
                            </div>
                        @endif
                    </div>

                    <hr class="my-4">
                    <h6 class="fw-bold mb-3" style="color:var(--primary);">Pangaturan Landing Page & Kontak</h6>
                    
                    <div class="mb-3">
                        <label class="form-label fw-bold">Tentang Divisi (About)</label>
                        <textarea name="about_text" class="form-control" rows="3" placeholder="Teks panjang tentang divisi ini..." style="border-radius:12px;">{{ $division->about_text }}</textarea>
                    </div>

                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label class="form-label fw-bold">Nomor Telepon / WA</label>
                            <input type="text" name="phone_number" class="form-control" value="{{ $division->phone_number }}" placeholder="08..." style="border-radius:12px;">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label fw-bold">Email</label>
                            <input type="email" name="email" class="form-control" value="{{ $division->email }}" placeholder="email@jostru.site" style="border-radius:12px;">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label fw-bold">Alamat Fisik</label>
                            <input type="text" name="address" class="form-control" value="{{ $division->address }}" placeholder="Jl. Raya..." style="border-radius:12px;">
                        </div>
                    </div>

                    <hr class="my-4">
                    <h6 class="fw-bold mb-3" style="color:var(--primary);">Pengaturan SEO</h6>
                    
                    <div class="mb-3">
                        <label class="form-label fw-bold">Meta Keywords</label>
                        <input type="text" name="meta_keywords" class="form-control" value="{{ $division->meta_keywords }}" placeholder="peternakan, pakan ternak, jostru" style="border-radius:12px;">
                        <small class="text-muted">Pisahkan dengan koma.</small>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label fw-bold">Meta Description</label>
                        <textarea name="meta_description" class="form-control" rows="2" placeholder="Deskripsi pendek untuk Google Search (Max 160 karakter)" style="border-radius:12px;">{{ $division->meta_description }}</textarea>
                    </div>
                </div>
                <div class="modal-footer border-0">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal" style="border-radius:12px;">Batal</button>
                    <button type="submit" class="btn btn-primary" style="border-radius:12px;">Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endforeach

<!-- Modal Tambah Divisi -->
<div class="modal fade" id="addDivisionModal" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content glass" style="border-radius:24px;">
            <div class="modal-header border-0">
                <h5 class="modal-title fw-bold">Buat Divisi Baru</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('admin.divisions.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-body" style="max-height: 70vh; overflow-y: auto;">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Nama Divisi</label>
                            <input type="text" name="name" class="form-control" required placeholder="Cth: Jostru Farm" style="border-radius:12px;">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Tipe Modul / Perkakas</label>
                            <select name="type" class="form-select" required style="border-radius:12px;">
                                <option value="GENERAL">General (Default)</option>
                                <option value="FARM">Pertanian (Farm)</option>
                                <option value="LIVESTOCK">Peternakan (Livestock)</option>
                                <option value="PRODUCTION">Produksi (Pupuk/Pakan)</option>
                                <option value="CAFE">Kafe / Warung (POS)</option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label fw-bold">Deskripsi Singkat</label>
                        <textarea name="description" class="form-control" rows="2" placeholder="Jelaskan fungsi divisi ini..." style="border-radius:12px;"></textarea>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">Logo Divisi (Opsional)</label>
                        <input type="file" name="logo" class="form-control" accept="image/*,.svg" style="border-radius:12px;">
                        <small class="text-muted">Format: JPG, PNG, atau SVG (Max 2MB).</small>
                    </div>

                    <hr class="my-4">
                    <h6 class="fw-bold mb-3" style="color:var(--primary);">Pangaturan Landing Page & Kontak</h6>
                    
                    <div class="mb-3">
                        <label class="form-label fw-bold">Tentang Divisi (About)</label>
                        <textarea name="about_text" class="form-control" rows="3" placeholder="Teks panjang tentang divisi ini..." style="border-radius:12px;"></textarea>
                    </div>

                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label class="form-label fw-bold">Nomor Telepon / WA</label>
                            <input type="text" name="phone_number" class="form-control" placeholder="08..." style="border-radius:12px;">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label fw-bold">Email</label>
                            <input type="email" name="email" class="form-control" placeholder="email@jostru.site" style="border-radius:12px;">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label fw-bold">Alamat Fisik</label>
                            <input type="text" name="address" class="form-control" placeholder="Jl. Raya..." style="border-radius:12px;">
                        </div>
                    </div>

                    <hr class="my-4">
                    <h6 class="fw-bold mb-3" style="color:var(--primary);">Pengaturan SEO</h6>
                    
                    <div class="mb-3">
                        <label class="form-label fw-bold">Meta Keywords</label>
                        <input type="text" name="meta_keywords" class="form-control" placeholder="peternakan, pakan ternak, jostru" style="border-radius:12px;">
                        <small class="text-muted">Pisahkan dengan koma.</small>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label fw-bold">Meta Description</label>
                        <textarea name="meta_description" class="form-control" rows="2" placeholder="Deskripsi pendek untuk Google Search (Max 160 karakter)" style="border-radius:12px;"></textarea>
                    </div>
                </div>
                <div class="modal-footer border-0">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal" style="border-radius:12px;">Batal</button>
                    <button type="submit" class="btn btn-primary" style="border-radius:12px;background:#22c55e;border:none;">Buat Divisi</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
