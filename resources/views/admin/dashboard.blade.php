@extends('layouts.admin')

@section('admin_content')
<div class="animate-fade-in">
    <h2>Overview Sistem</h2>
    <p class="text-muted mb-4">Informasi utama manajemen Jostru Community.</p>

    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1.5rem; margin-bottom: 2rem;">
        <div class="card p-4 glass">
            <h3 class="text-sm uppercase text-muted">Total Anggota</h3>
            <p class="text-4xl mt-4">{{\App\Models\User::where('role', 'member')->count()}}</p>
        </div>
        <div class="card p-4 glass">
            <h3 class="text-sm uppercase text-muted">Pesan Baru</h3>
            <p class="text-4xl mt-4">{{\App\Models\Contact::where('is_read', false)->count()}}</p>
        </div>
        <div class="card p-4 glass">
            <h3 class="text-sm uppercase text-muted">Kartu Dicetak</h3>
            <p class="text-4xl mt-4">{{\App\Models\MembershipCard::where('status', 'active')->count()}}</p>
        </div>
    </div>

    <div class="card p-4 glass">
        <h3 class="mb-4">Aktivitas Terakhir</h3>
        <p class="text-muted">Belum ada aktivitas yang tercatat untuk saat ini.</p>
    </div>
</div>
@endsection
