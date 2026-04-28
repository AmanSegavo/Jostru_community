@extends('layouts.admin')

@section('admin_content')
<div class="animate-fade-in">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 style="font-weight: 800;">Kelola Agenda & Event</h2>
        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#eventModal" style="border-radius: 12px; background: #22c55e; border: none;">
            + Jadwalkan Event
        </button>
    </div>

    @if(session('success'))
        <div class="alert alert-success border-0 shadow-sm mb-4" style="border-radius: 15px; background: rgba(34, 197, 94, 0.1); color: #22c55e;">
            {{ session('success') }}
        </div>
    @endif

    <div class="card glass p-0 overflow-hidden" style="border-radius: 20px; border: 1px solid rgba(var(--primary-rgb), 0.1);">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead style="background: rgba(var(--primary-rgb), 0.05);">
                    <tr>
                        <th class="px-4 py-3">Nama Event</th>
                        <th class="px-4 py-3">Tanggal & Waktu</th>
                        <th class="px-4 py-3">Lokasi</th>
                        <th class="px-4 py-3 text-end">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($events as $event)
                    <tr>
                        <td class="px-4 py-3">
                            <div style="font-weight: 700;">{{ $event->title }}</div>
                            <small class="text-muted">{{ Str::limit($event->description, 50) }}</small>
                        </td>
                        <td class="px-4 py-3">
                            <div style="font-weight: 600;">{{ \Carbon\Carbon::parse($event->event_date)->translatedFormat('d F Y') }}</div>
                            <small class="text-muted">{{ \Carbon\Carbon::parse($event->event_date)->format('H:i') }} WIB</small>
                        </td>
                        <td class="px-4 py-3 text-muted">{{ $event->location ?? '-' }}</td>
                        <td class="px-4 py-3 text-end">
                            <form action="{{ route('admin.events.destroy', $event->id) }}" method="POST" onsubmit="return confirm('Hapus event ini?')">
                                @csrf
                                @method('DELETE')
                                <button class="btn btn-link text-danger p-0" title="Hapus">
                                    <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                </button>
                            </form>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal Create Event -->
<div class="modal fade" id="eventModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content glass" style="border-radius: 24px; border: 1px solid rgba(255,255,255,0.2);">
            <div class="modal-header border-0">
                <h5 class="modal-title" style="font-weight: 800;">Jadwalkan Event Baru</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('admin.events.store') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label" style="font-weight: 600;">Nama Event</label>
                        <input type="text" name="title" class="form-control" required style="border-radius: 12px;">
                    </div>
                    <div class="mb-3">
                        <label class="form-label" style="font-weight: 600;">Tanggal & Waktu</label>
                        <input type="datetime-local" name="event_date" class="form-control" required style="border-radius: 12px;">
                    </div>
                    <div class="mb-3">
                        <label class="form-label" style="font-weight: 600;">Lokasi</label>
                        <input type="text" name="location" class="form-control" placeholder="Contoh: Balai Desa atau Online" style="border-radius: 12px;">
                    </div>
                    <div class="mb-3">
                        <label class="form-label" style="font-weight: 600;">Deskripsi</label>
                        <textarea name="description" class="form-control" rows="4" required style="border-radius: 12px;"></textarea>
                    </div>
                </div>
                <div class="modal-footer border-0">
                    <button type="button" class="btn btn-outline" data-bs-dismiss="modal" style="border-radius: 12px;">Batal</button>
                    <button type="submit" class="btn btn-primary" style="border-radius: 12px; background: #22c55e; border: none; padding: 10px 25px; font-weight: 700;">Simpan Event</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
