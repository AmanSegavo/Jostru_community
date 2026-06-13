@extends('layouts.admin')

@section('title', 'Manajemen Sertifikat Dividen')

@section('content')
<div class="container-fluid animate-fade-in">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 style="font-weight: 800; color: var(--text-color); margin-bottom: 0.5rem;">Sertifikat Dividen Jostru Farm</h1>
            <p class="text-muted">Kelola data pemegang saham dan cetak sertifikat kepemilikan.</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ url('admin/dividends/scanner') }}" class="btn btn-outline-primary glass" style="border-radius: 12px; font-weight: 600;">
                📷 Scanner Keabsahan
            </a>
            <button class="btn btn-primary glass" onclick="document.getElementById('addModal').style.display='flex'" style="border-radius: 12px; font-weight: 600;">
                + Tambah Pemegang Saham
            </button>
        </div>
    </div>

    @if(session('success'))
    <div class="alert alert-success" style="border-radius: 12px; border: none; background: rgba(34,197,94,0.1); color: #16a34a; font-weight: 600;">
        {{ session('success') }}
    </div>
    @endif

    <div class="card glass p-0" style="border-radius: 20px; overflow: hidden;">
        <div class="p-4 table-responsive">
            <table class="table-modern">
                <thead>
                    <tr>
                        <th>ID Sertifikat</th>
                        <th>Nama Pemegang</th>
                        <th>Persentase</th>
                        <th>Tanggal Terbit</th>
                        <th style="text-align:right;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($shareholders as $s)
                    <tr>
                        <td style="font-weight: 700; color: var(--primary);">{{ $s->certificate_id }}</td>
                        <td style="font-weight: 700;">{{ $s->name }}</td>
                        <td>
                            <span class="badge" style="background:rgba(245,158,11,0.1); color:#d97706; font-size: 14px;">{{ $s->percentage }}%</span>
                            <div style="font-size: 11px; color: var(--text-secondary); margin-top: 4px;">{{ $s->percentage_text }}</div>
                        </td>
                        <td>{{ \Carbon\Carbon::parse($s->issue_date)->translatedFormat('d M Y') }}</td>
                        <td style="text-align:right;">
                            <div class="d-flex justify-content-end gap-2">
                                <a href="{{ route('admin.dividends.generate', $s->id) }}" target="_blank" class="btn btn-sm btn-outline-primary" style="border-radius: 10px; font-weight: 600;">Preview</a>
                                <a href="{{ route('admin.dividends.generate', $s->id) }}?download=1" class="btn btn-sm btn-success" style="border-radius: 10px; font-weight: 600;">Cetak / PDF</a>
                                <form action="{{ route('admin.dividends.destroy', $s->id) }}" method="POST" onsubmit="return confirm('Yakin ingin menghapus sertifikat ini?')" style="display:inline;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger" style="border-radius: 10px; font-weight: 600;">Hapus</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="text-center p-5 text-muted">Belum ada data sertifikat dividen.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal Tambah -->
<div id="addModal" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.5); z-index:9999; align-items:center; justify-content:center; backdrop-filter:blur(5px);">
    <div class="card glass animate-fade-in" style="width:100%; max-width:500px; border-radius:24px; padding:2rem; max-height:90vh; overflow-y:auto;">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h3 style="font-weight:800; margin:0;">Tambah Pemegang Saham</h3>
            <button onclick="document.getElementById('addModal').style.display='none'" style="background:none; border:none; font-size:1.5rem; cursor:pointer;">&times;</button>
        </div>
        
        <form action="{{ route('admin.dividends.store') }}" method="POST">
            @csrf
            <div class="form-group mb-3">
                <label style="font-weight:600; margin-bottom:8px;">Nama di Sertifikat</label>
                <input type="text" name="name" class="form-control input-modern" required placeholder="Contoh: Bpk. H. Abdurrahman">
            </div>

            <div class="form-group mb-3">
                <label style="font-weight:600; margin-bottom:8px;">Pilih Anggota Terkait</label>
                <select name="user_id" class="form-control input-modern" required>
                    <option value="">-- Pilih Anggota --</option>
                    @foreach($users as $user)
                        <option value="{{ $user->id }}">{{ $user->name }} ({{ $user->email }})</option>
                    @endforeach
                </select>
                <small class="text-muted">Anggota ini akan bisa melihat sertifikat dan PIN rahasianya di Dashboard.</small>
            </div>

            <div class="form-group mb-3">
                <label style="font-weight:600; margin-bottom:8px;">Divisi (Opsional)</label>
                <select name="division_id" class="form-control input-modern">
                    <option value="">-- Pilih Divisi (Bawaan: Jostru Farm) --</option>
                    @foreach($divisions as $division)
                        <option value="{{ $division->id }}">Jostru {{ $division->name }}</option>
                    @endforeach
                </select>
                <small class="text-muted">Nama divisi akan dicetak pada Sertifikat Kepemilikan.</small>
            </div>
            
            <div class="row">
                <div class="col-md-4 form-group mb-3">
                    <label style="font-weight:600; margin-bottom:8px;">Angka (%)</label>
                    <input type="number" step="0.01" name="percentage" class="form-control input-modern" required placeholder="10">
                </div>
                <div class="col-md-8 form-group mb-3">
                    <label style="font-weight:600; margin-bottom:8px;">Teks Persentase</label>
                    <input type="text" name="percentage_text" class="form-control input-modern" required placeholder="SEPULUH PERSEN">
                </div>
            </div>

            <div class="form-group mb-4">
                <label style="font-weight:600; margin-bottom:8px;">Tanggal Terbit</label>
                <input type="date" name="issue_date" class="form-control input-modern" required value="{{ date('Y-m-d') }}">
            </div>
            
            <div class="alert alert-info" style="font-size:0.9rem; border-radius:12px; background:rgba(14, 165, 233, 0.1); border:none; color:#0369a1;">
                <strong>Info:</strong> Tanda tangan akan dikosongkan agar Anda dapat menandatangani langsung di atas kertas fisik setelah sertifikat dicetak.
            </div>

            <div class="d-flex gap-2 justify-content-end mt-4">
                <button type="button" class="btn btn-outline" onclick="document.getElementById('addModal').style.display='none'" style="border-radius:12px;">Batal</button>
                <button type="submit" class="btn btn-primary" style="border-radius:12px; font-weight:600;">Simpan & Buat ID</button>
            </div>
        </form>
    </div>
</div>

<style>
    .input-modern {
        border-radius: 12px;
        border: 1px solid var(--border-color);
        padding: 0.75rem 1rem;
        background: rgba(255,255,255,0.5);
    }
    .input-modern:focus {
        box-shadow: 0 0 0 3px rgba(var(--primary-rgb), 0.2);
        border-color: var(--primary);
        background: #fff;
    }
</style>
@endsection
