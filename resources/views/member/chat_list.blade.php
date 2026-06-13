@extends('layouts.app')

@section('title', 'Daftar Obrolan')

@section('content')
<div class="container mt-4 animate-fade-in">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 style="font-weight: 800; margin-bottom: 0.2rem; color: var(--text-primary);">Pusat Komunikasi</h2>
            <p class="text-muted mb-0">Hubungi tim dan rekan Jostru Community.</p>
        </div>
        <div style="font-size: 2.5rem;">💬</div>
    </div>

    @if(session('error'))
        <div class="alert alert-danger" style="border-radius: 15px; border: none; background: rgba(239, 68, 68, 0.1); color: #ef4444;">
            {{ session('error') }}
        </div>
    @endif

    <div class="row">
        <div class="col-md-8 mx-auto">
            <!-- Search Bar -->
            <form action="{{ route('member.chat.list') }}" method="GET" class="mb-4">
                <div class="input-group">
                    <input type="text" name="q" class="form-control p-3" placeholder="Cari anggota berdasarkan nama atau jabatan..." value="{{ $search ?? '' }}" style="border-radius: 16px 0 0 16px; border: 1px solid rgba(var(--primary-rgb), 0.2); background: var(--surface-color); color: var(--text-primary);">
                    <button class="btn btn-primary px-4" type="submit" style="border-radius: 0 16px 16px 0; font-weight: bold;">
                        Cari
                    </button>
                </div>
            </form>

            <div class="card glass p-0 overflow-hidden" style="border-radius: 24px; border: 1px solid rgba(var(--primary-rgb), 0.1);">
                <div class="list-group list-group-flush">
                    @forelse($users as $user)
                    <a href="{{ route('member.chat.room', $user->id) }}" class="list-group-item list-group-item-action p-4 d-flex align-items-center justify-content-between border-0 position-relative" style="background: transparent; border-bottom: 1px solid rgba(var(--primary-rgb), 0.05) !important; transition: all 0.3s ease;">
                        <div class="d-flex align-items-center w-100">
                            <div class="position-relative">
                                <div style="width: 55px; height: 55px; background: linear-gradient(135deg, #22c55e, #10b981); border-radius: 50%; display: flex; align-items: center; justify-content: center; color: white; font-weight: 800; font-size: 1.2rem; margin-right: 15px; box-shadow: 0 5px 15px rgba(34, 197, 94, 0.2);">
                                    {{ strtoupper(substr($user->name, 0, 1)) }}
                                </div>
                                <span style="position: absolute; bottom: 2px; right: 15px; width: 12px; height: 12px; background: {{ $user->is_online ? '#22c55e' : '#cbd5e1' }}; border: 2px solid white; border-radius: 50%;"></span>
                            </div>
                            
                            <div style="flex: 1; overflow: hidden;">
                                <div class="d-flex justify-content-between align-items-center">
                                    <h6 class="mb-1 text-truncate" style="font-weight: 700; font-size: 1.1rem; color: var(--text-primary);">{{ $user->name }}</h6>
                                    @if($user->last_message_time)
                                        <small class="text-muted" style="font-size: 0.75rem;">{{ \Carbon\Carbon::parse($user->last_message_time)->diffForHumans(null, true, true) }}</small>
                                    @endif
                                </div>
                                <div class="d-flex align-items-center justify-content-between">
                                    <div class="text-muted text-truncate w-75" style="font-size: 0.9rem;">
                                        @if($user->last_message)
                                            {{ $user->last_message }}
                                        @else
                                            <span style="font-style: italic; opacity: 0.7;">{{ $user->jabatan ?? ($user->role == 'admin' ? 'Administrator' : 'Anggota') }}</span>
                                        @endif
                                    </div>
                                    @if($user->unread_count > 0)
                                        <span class="badge bg-success rounded-pill">{{ $user->unread_count }}</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </a>
                    @empty
                    <div class="p-5 text-center text-muted">
                        <div style="font-size: 3rem; margin-bottom: 1rem; opacity: 0.5;">📭</div>
                        @if($search)
                            <h6>Tidak ada pengguna yang cocok dengan pencarian.</h6>
                        @else
                            <h6>Belum ada riwayat percakapan.</h6>
                            <p class="small">Gunakan fitur pencarian di atas untuk memulai obrolan dengan anggota lain.</p>
                        @endif
                    </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .list-group-item:hover {
        background: rgba(34, 197, 94, 0.05) !important;
        transform: translateX(5px);
    }
</style>
@endsection
