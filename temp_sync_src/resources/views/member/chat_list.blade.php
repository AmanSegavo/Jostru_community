@extends('layouts.app')

@section('title', 'Daftar Obrolan')

@section('content')
<div class="container mt-4 animate-fade-in">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 style="font-weight: 800; margin-bottom: 0.5rem;">Pusat Komunikasi</h2>
            <p class="text-muted">Hubungi tim Jostru Community secara langsung.</p>
        </div>
        <div style="font-size: 2.5rem;">💬</div>
    </div>

    @if(session('error'))
        <div class="alert alert-danger" style="border-radius: 15px; border: none; background: rgba(220, 53, 69, 0.1); color: #dc3545;">
            {{ session('error') }}
        </div>
    @endif

    <div class="row">
        <div class="col-md-8 mx-auto">
            <div class="card glass p-0 overflow-hidden" style="border-radius: 24px; border: 1px solid rgba(var(--primary-rgb), 0.1);">
                <div class="list-group list-group-flush">
                    @foreach($users as $user)
                    <a href="{{ route('member.chat.room', $user->id) }}" class="list-group-item list-group-item-action p-4 d-flex align-items-center justify-content-between border-0" style="background: transparent; border-bottom: 1px solid rgba(var(--primary-rgb), 0.05) !important; transition: all 0.3s ease;">
                        <div class="d-flex align-items-center">
                            <div style="width: 55px; height: 55px; background: linear-gradient(135deg, #22c55e, #10b981); border-radius: 50%; display: flex; align-items: center; justify-content: center; color: white; font-weight: 800; font-size: 1.2rem; margin-right: 20px; box-shadow: 0 5px 15px rgba(34, 197, 94, 0.2);">
                                {{ substr($user->name, 0, 1) }}
                            </div>
                            <div>
                                <h6 class="mb-1" style="font-weight: 700; font-size: 1.1rem;">{{ $user->name }}</h6>
                                <div class="d-flex align-items-center gap-2">
                                    <span style="width: 8px; height: 8px; background: {{ $user->is_online ? '#22c55e' : '#cbd5e1' }}; border-radius: 50%;"></span>
                                    <small class="text-muted">{{ $user->jabatan ?? ($user->role == 'admin' ? 'Administrator' : 'Anggota') }}</small>
                                </div>
                            </div>
                        </div>
                        <div class="d-flex align-items-center gap-3">
                            <div class="text-end d-none d-md-block">
                                <small class="text-muted d-block">Tersedia</small>
                            </div>
                            <svg width="24" height="24" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" class="text-muted"><path d="M9 5l7 7-7 7"></path></svg>
                        </div>
                    </a>
                    @endforeach
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
