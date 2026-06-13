@extends('layouts.admin')

@section('title', 'Enterprise Resource Planning (ERP)')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 style="font-weight: 800; color: var(--primary);">Pusat Kendali ERP</h1>
    <p class="text-muted m-0">Kelola operasional, divisi, perizinan, dan alat komunitas dalam satu pintu.</p>
</div>

<div class="row g-4">
    <!-- Card Hak Akses -->
    <div class="col-md-6 col-lg-4">
        <div class="card h-100" style="border:none; border-radius:24px; box-shadow:0 10px 30px rgba(0,0,0,0.05); transition:transform 0.3s; background:linear-gradient(145deg, #ffffff, #f8fafc);">
            <div class="card-body p-4 text-center">
                <div style="font-size:3rem; margin-bottom:1rem;">🛡️</div>
                <h4 style="font-weight:800; color:#1e293b;">Hak Akses & Perizinan</h4>
                <p style="color:#64748b; font-size:0.9rem; margin-bottom:20px;">Atur hak akses anggota, izin divisi, admin pendamping, dan akses fitur sistem secara massal.</p>
                <a href="{{ route('admin.erp.roles') }}" class="btn btn-primary" style="border-radius:50px; padding:10px 24px; font-weight:700; width:100%;">Kelola Hak Akses</a>
            </div>
        </div>
    </div>

    <!-- Card Chat Management -->
    <div class="col-md-6 col-lg-4">
        <div class="card h-100" style="border:none; border-radius:24px; box-shadow:0 10px 30px rgba(0,0,0,0.05); transition:transform 0.3s; background:linear-gradient(145deg, #ffffff, #f8fafc);">
            <div class="card-body p-4 text-center">
                <div style="font-size:3rem; margin-bottom:1rem;">💬</div>
                <h4 style="font-weight:800; color:#1e293b;">Manajemen Chat</h4>
                <p style="color:#64748b; font-size:0.9rem; margin-bottom:20px;">Atur peta relasi percakapan antar anggota, antar divisi, secara visual dan intuitif.</p>
                <a href="{{ route('admin.erp.chat_relations') }}" class="btn" style="background:linear-gradient(135deg, #22c55e, #10b981); color:white; border-radius:50px; padding:10px 24px; font-weight:700; width:100%;">Kelola Relasi Chat</a>
            </div>
        </div>
    </div>

    <!-- Card Tools (Coming Soon) -->
    <div class="col-md-6 col-lg-4">
        <div class="card h-100" style="border:none; border-radius:24px; box-shadow:0 10px 30px rgba(0,0,0,0.05); transition:transform 0.3s; background:linear-gradient(145deg, #ffffff, #f8fafc); position:relative; overflow:hidden;">
            <div style="position:absolute; top:20px; right:-30px; background:#f59e0b; color:white; font-weight:800; font-size:0.8rem; padding:4px 30px; transform:rotate(45deg); box-shadow:0 2px 10px rgba(245,158,11,0.3);">Segera Hadir</div>
            <div class="card-body p-4 text-center" style="opacity:0.7;">
                <div style="font-size:3rem; margin-bottom:1rem;">🛠️</div>
                <h4 style="font-weight:800; color:#1e293b; margin-bottom:10px;">Productivity Tools</h4>
                <p style="color:#64748b; font-size:0.9rem; margin-bottom:20px;">Akses ke sistem Point of Sales (POS), manajemen inventori pusat, dan alat operasional bisnis lainnya.</p>
                <a href="{{ route('admin.erp.tools') }}" class="btn" style="background:#e2e8f0; color:#475569; border-radius:50px; padding:10px 24px; font-weight:700; width:100%;">Lihat Modul</a>
            </div>
        </div>
    </div>
</div>
@endsection
