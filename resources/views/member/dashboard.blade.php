@extends('layouts.app')

@section('title', 'Member Dashboard')

@section('content')
<div class="container mt-4 animate-fade-in">
    <div class="card p-4 glass mb-4">
        <h2>Halo, {{ auth()->user()->name }} 👋</h2>
        <p class="text-muted">Selamat datang di dashboard anggota Jostru Community.</p>
    </div>

    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 1.5rem;">
        <div class="card p-4 glass text-center">
            <h3>Feed Komunitas</h3>
            <p class="text-muted mb-4">Lihat aktivitas terbaru.</p>
            <a href="#" class="btn btn-outline w-full">Buka Feed</a>
        </div>
        <div class="card p-4 glass text-center">
            <h3>Events</h3>
            <p class="text-muted mb-4">Kalender acara mendatang.</p>
            <a href="#" class="btn btn-outline w-full">Lihat Kalender</a>
        </div>
        <div class="card p-4 glass text-center">
            <h3>Keuangan Pribadi</h3>
            <p class="text-muted mb-4">Status iuran keanggotaan.</p>
            <a href="#" class="btn btn-outline w-full">Detail Finansial</a>
        </div>
        <div class="card p-4 glass text-center" style="background: linear-gradient(135deg, hsla(var(--primary-h), var(--primary-s), var(--primary-l), 0.1), hsla(var(--secondary-h), var(--secondary-s), var(--secondary-l), 0.1));">
            <h3>Kartu Anggota</h3>
            <p class="text-muted mb-4">Unduh ID Digital Anda.</p>
            <a href="#" class="btn btn-primary w-full">Kartu Saya</a>
        </div>
    </div>
</div>
@endsection
