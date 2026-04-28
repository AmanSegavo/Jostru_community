@extends('layouts.app')

@section('title', 'Community Feed')

@section('content')
<div class="container mt-4 animate-fade-in">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2 style="font-weight: 800;">Community Feed</h2>
                <div class="badge bg-success" style="padding: 8px 16px; border-radius: 20px; background: rgba(34,197,94,0.1) !important; color: #22c55e;">Aktivitas Terkini</div>
            </div>

            @forelse($posts as $post)
            <div class="card glass mb-4 p-0 overflow-hidden" style="border-radius: 24px; border: 1px solid rgba(var(--primary-rgb), 0.1);">
                <div class="p-4">
                    <div class="d-flex align-items-center mb-3">
                        <div style="width: 45px; height: 45px; background: #22c55e; border-radius: 50%; display: flex; align-items: center; justify-content: center; color: white; font-weight: 800; margin-right: 15px;">
                            {{ substr($post->user->name, 0, 1) }}
                        </div>
                        <div>
                            <h6 class="mb-0" style="font-weight: 700;">{{ $post->user->name }}</h6>
                            <small class="text-muted">{{ $post->created_at->diffForHumans() }}</small>
                        </div>
                    </div>
                    <p style="font-size: 1.1rem; line-height: 1.6; color: var(--text-color);">{{ $post->content }}</p>
                    @if($post->image_path)
                    <img src="{{ asset('storage/' . $post->image_path) }}" class="img-fluid mt-2" style="border-radius: 16px; width: 100%; max-height: 400px; object-fit: cover;">
                    @endif
                </div>
                <div class="px-4 py-3" style="background: rgba(var(--primary-rgb), 0.03); border-top: 1px solid rgba(var(--primary-rgb), 0.05); display: flex; gap: 20px;">
                    <button class="btn btn-link p-0 text-muted d-flex align-items-center gap-2" style="text-decoration: none;">
                        <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path></svg>
                        <span>Suka</span>
                    </button>
                    <button class="btn btn-link p-0 text-muted d-flex align-items-center gap-2" style="text-decoration: none;">
                        <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path></svg>
                        <span>Komentar</span>
                    </button>
                </div>
            </div>
            @empty
            <div class="text-center py-5">
                <div style="font-size: 4rem; opacity: 0.2;">📭</div>
                <h4 class="text-muted">Belum ada postingan komunitas.</h4>
            </div>
            @endforelse

            <div class="mt-4">
                {{ $posts->links() }}
            </div>
        </div>
    </div>
</div>
@endsection
