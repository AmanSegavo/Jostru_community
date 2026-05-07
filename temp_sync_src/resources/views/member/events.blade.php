@extends('layouts.app')

@section('title', 'Community Events')

@section('content')
<div class="container mt-4 animate-fade-in">
    <div class="d-flex justify-content-between align-items-center mb-5">
        <div>
            <h2 style="font-weight: 800; margin-bottom: 0.5rem;">Agenda Komunitas Jostru</h2>
            <p class="text-muted">Ikuti kegiatan seru kami untuk lingkungan yang lebih baik.</p>
        </div>
        <div style="font-size: 3rem; opacity: 0.5;">📅</div>
    </div>

    <div class="row">
        @forelse($events as $event)
        <div class="col-md-6 col-lg-4 mb-4">
            <div class="card glass p-0 overflow-hidden h-100" style="border-radius: 24px; border: 1px solid rgba(var(--primary-rgb), 0.1); transition: transform 0.3s ease;">
                <div style="background: linear-gradient(135deg, #22c55e, #10b981); height: 120px; padding: 20px; display: flex; align-items: center; justify-content: center; position: relative;">
                    <div style="background: rgba(255,255,255,0.2); backdrop-filter: blur(5px); padding: 10px 20px; border-radius: 15px; color: white; text-align: center; border: 1px solid rgba(255,255,255,0.3);">
                        <div style="font-size: 1.2rem; font-weight: 800; line-height: 1;">{{ \Carbon\Carbon::parse($event->event_date)->format('d') }}</div>
                        <div style="font-size: 0.8rem; font-weight: 600; text-transform: uppercase;">{{ \Carbon\Carbon::parse($event->event_date)->translatedFormat('M') }}</div>
                    </div>
                    <div style="position: absolute; right: 20px; top: 20px; color: rgba(255,255,255,0.3); font-size: 3rem; font-weight: 900;">{{ \Carbon\Carbon::parse($event->event_date)->format('Y') }}</div>
                </div>
                <div class="p-4 d-flex flex-column" style="flex: 1;">
                    <h5 style="font-weight: 800; margin-bottom: 1rem;">{{ $event->title }}</h5>
                    <p class="text-muted mb-4" style="font-size: 0.95rem; line-height: 1.6; flex: 1;">{{ $event->description }}</p>
                    <div class="mt-auto">
                        <div class="d-flex align-items-center gap-2 mb-2 text-muted" style="font-size: 0.9rem;">
                            <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            <span>{{ \Carbon\Carbon::parse($event->event_date)->format('H:i') }} WIB</span>
                        </div>
                        <div class="d-flex align-items-center gap-2 text-muted" style="font-size: 0.9rem;">
                            <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                            <span>{{ $event->location ?? 'Lokasi via Grup WA' }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @empty
        <div class="col-12 text-center py-5">
            <h4 class="text-muted">Belum ada agenda event yang terdaftar.</h4>
        </div>
        @endforelse
    </div>
</div>

<style>
    .card:hover {
        transform: translateY(-10px);
        box-shadow: 0 20px 40px rgba(34, 197, 94, 0.2);
    }
</style>
@endsection
