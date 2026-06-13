@extends('layouts.admin')

@section('title', 'Productivity Tools - ERP')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <a href="{{ route('admin.erp.index') }}" class="btn btn-outline mb-2" style="border-radius:50px; font-weight:600; font-size:13px;">&larr; Kembali ke ERP</a>
        <h1 style="font-weight: 800; color: var(--primary);">Productivity Tools</h1>
        <p class="text-muted m-0">Fitur lanjutan Enterprise Resource Planning Jostru.</p>
    </div>
</div>

<div class="card p-5 text-center" style="border:none; border-radius:24px; box-shadow:0 10px 30px rgba(0,0,0,0.05); background:linear-gradient(145deg, #ffffff, #f8fafc);">
    <div style="font-size:5rem; margin-bottom:1rem;">🛠️</div>
    <h2 style="font-weight:800; color:#1e293b; margin-bottom:1rem;">Segera Hadir (Coming Soon)</h2>
    <p style="color:#64748b; max-width:600px; margin:0 auto; line-height:1.6;">Modul <strong>Productivity Tools</strong> (seperti sistem Point of Sales, Pusat Inventori Gudang, dan Alat Produksi Terpusat) saat ini sedang dalam tahap pengembangan. Fitur ini akan membantu mengotomatisasi lebih banyak proses bisnis komunitas Anda!</p>
</div>
@endsection
