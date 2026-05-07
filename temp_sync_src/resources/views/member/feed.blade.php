@extends('layouts.app')
@section('title', 'Community Feed')

@section('content')
<div class="container mt-4 animate-fade-in">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2 style="font-weight: 800; color: var(--text-primary);">Community Feed</h2>
                <div class="badge bg-success" style="padding: 8px 16px; border-radius: 20px; background: rgba(34,197,94,0.1) !important; color: #22c55e;">Aktivitas Terkini</div>
            </div>

            <!-- Form Buat Postingan untuk Member -->
            <div class="card glass mb-4 p-3 d-flex flex-row align-items-center gap-3 shadow-sm" style="border-radius: 20px; cursor: pointer;" data-bs-toggle="modal" data-bs-target="#postModal">
                <div style="width: 45px; height: 45px; background: #22c55e; border-radius: 50%; display: flex; align-items: center; justify-content: center; color: white; font-weight: 800;">
                    {{ substr(auth()->user()->name, 0, 1) }}
                </div>
                <div class="text-muted" style="flex: 1; background: var(--bg-color); padding: 12px 20px; border-radius: 50px; border: 1px solid var(--border-color);">
                    Tulis sesuatu untuk komunitas...
                </div>
            </div>

            @forelse($posts as $post)
            <div class="card glass mb-4 p-0 overflow-hidden" style="border-radius: 24px;">
                <div class="p-4">
                    <div class="d-flex align-items-center mb-3">
                        <div style="width: 45px; height: 45px; background: #22c55e; border-radius: 50%; display: flex; align-items: center; justify-content: center; color: white; font-weight: 800; margin-right: 15px;">
                            {{ substr($post->user->name, 0, 1) }}
                        </div>
                        <div>
                            <h6 class="mb-0" style="font-weight: 700; color: var(--text-primary);">{{ $post->user->name }}</h6>
                            <small class="text-muted">{{ $post->created_at->diffForHumans() }}</small>
                        </div>
                    </div>
                    <p style="font-size: 1.1rem; line-height: 1.6; color: var(--text-primary);">{{ $post->content }}</p>
                    
                    <!-- Media dengan fitur Zoom -->
                    @if($post->media_path)
                    <div class="mt-2 position-relative" style="border-radius: 16px; overflow: hidden; border: 1px solid var(--border-color); cursor: pointer;" 
                         onclick="showZoomMedia('{{ asset('feed/' . $post->media_path) }}', '{{ $post->media_type }}')">
                        @if($post->media_type === 'video')
                        <video src="{{ asset('feed/' . $post->media_path) }}" class="img-fluid" style="width: 100%; max-height: 400px; object-fit: cover; background: #000;" muted></video>
                        <div class="position-absolute top-50 start-50 translate-middle text-white" style="font-size: 3rem; opacity: 0.8;">▶</div>
                        @else
                        <img src="{{ asset('feed/' . $post->media_path) }}" class="img-fluid" style="width: 100%; max-height: 400px; object-fit: cover;" onerror="this.style.display='none'">
                        @endif
                    </div>
                    @endif
                </div>

                <!-- Bagian Suka & Komentar -->
                <div class="px-4 py-3" style="background: var(--bg-color); border-top: 1px solid var(--border-color); display: flex; gap: 20px;">
                    @php
                        $isLiked = $post->likes ? $post->likes->where('user_id', auth()->id())->isNotEmpty() : false;
                        $likeCount = $post->likes ? $post->likes->count() : 0;
                    @endphp
                    <form action="{{ route('member.feed.like', $post->id) }}" method="POST" class="m-0">
                        @csrf
                        <button type="submit" class="btn btn-link p-0 {{ $isLiked ? 'text-primary' : 'text-muted' }} d-flex align-items-center gap-2" style="text-decoration: none;">
                            <svg width="20" height="20" fill="{{ $isLiked ? '#22c55e' : 'none' }}" stroke="{{ $isLiked ? '#22c55e' : 'currentColor' }}" viewBox="0 0 24 24"><path d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path></svg>
                            <span style="color: {{ $isLiked ? '#22c55e' : 'var(--text-secondary)' }}">Suka {{ $likeCount > 0 ? "($likeCount)" : '' }}</span>
                        </button>
                    </form>

                    <button class="btn btn-link p-0 text-muted d-flex align-items-center gap-2" style="text-decoration: none;" data-bs-toggle="collapse" data-bs-target="#comments-{{ $post->id }}">
                        <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path></svg>
                        <span>Komentar {{ $post->comments ? "({$post->comments->count()})" : '' }}</span>
                    </button>
                </div>

                <!-- Collapse Ruang Komentar -->
                <div class="collapse" id="comments-{{ $post->id }}">
                    <div class="p-4 pt-0" style="background: var(--surface-color);">
                        <hr class="mt-0 mb-3" style="border-color: var(--border-color);">
                        @if($post->comments)
                            @foreach($post->comments as $comment)
                                <div class="mb-3 p-3" style="background: var(--bg-color); border-radius: 12px; border: 1px solid var(--border-color);">
                                    <strong style="font-size: 0.9rem; color: #22c55e;">{{ $comment->user->name }}</strong>
                                    <p class="mb-1 mt-1" style="font-size: 0.9rem; color: var(--text-primary);">{{ $comment->content }}</p>
                                    <small class="text-muted" style="font-size: 0.75rem;">{{ $comment->created_at->diffForHumans() }}</small>
                                </div>
                            @endforeach
                        @endif
                        
                        <form action="{{ route('member.feed.comment', $post->id) }}" method="POST" class="mt-2 d-flex gap-2">
                            @csrf
                            <input type="text" name="content" class="form-control" placeholder="Tulis komentar..." required style="border-radius: 12px; border: 1px solid var(--border-color); background: var(--bg-color); color: var(--text-primary);">
                            <button type="submit" class="btn btn-primary px-4" style="border-radius: 12px; background: #22c55e; border: none; font-weight: bold;">Kirim</button>
                        </form>
                    </div>
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

<!-- Modal Buat Postingan Member -->
<div class="modal fade" id="postModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content glass" style="border-radius:24px;">
            <div class="modal-header border-0">
                <h5 class="modal-title" style="font-weight:800; color: var(--text-primary);">Buat Postingan</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('member.feed.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <textarea name="content" class="form-control" rows="4" required placeholder="Apa yang ingin Anda bagikan?" style="border-radius:12px; background: var(--bg-color); color: var(--text-primary); border: 1px solid var(--border-color);"></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold" style="color: var(--text-primary);">Foto / Video (Opsional)</label>
                        <input type="file" name="image" accept="image/*,video/*" class="form-control" style="border-radius:12px; background: var(--bg-color); color: var(--text-primary); border: 1px solid var(--border-color);">
                    </div>
                </div>
                <div class="modal-footer border-0">
                    <button type="submit" class="btn btn-primary w-100" style="border-radius:12px; background:#22c55e; border:none; font-weight:bold;">Bagikan Sekarang</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- MODAL FULLSCREEN ZOOM & DOWNLOAD -->
<div class="modal fade" id="zoomModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-fullscreen">
        <div class="modal-content" style="background: rgba(0,0,0,0.95); border: none;">
            <div class="modal-header border-0 d-flex justify-content-end p-4 position-absolute w-100" style="z-index: 1050;">
                <div id="downloadBtnContainer" class="me-3"></div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close" style="font-size: 1.2rem;"></button>
            </div>
            
            <div class="modal-body d-flex align-items-center justify-content-center p-0" id="zoomContent">
                <!-- Media akan dirender di sini oleh JS -->
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
function showZoomMedia(url, type) {
    const content = document.getElementById('zoomContent');
    const downloadContainer = document.getElementById('downloadBtnContainer');
    
    downloadContainer.innerHTML = `<a href="${url}" download class="btn btn-success fw-bold" style="border-radius: 8px; background: #22c55e; border: none;">📥 Unduh Media</a>`;

    if (type === 'video') {
        content.innerHTML = `
            <video controls autoplay style="max-width:100%; max-height:100vh; outline: none;">
                <source src="${url}">
                Browser Anda tidak mendukung pemutar video.
            </video>
        `;
    } else {
        content.innerHTML = `
            <img src="${url}" style="max-width:100%; max-height:100vh; object-fit:contain;">
        `;
    }

    const modal = new bootstrap.Modal(document.getElementById('zoomModal'));
    modal.show();
}

document.getElementById('zoomModal').addEventListener('hidden.bs.modal', function () {
    document.getElementById('zoomContent').innerHTML = '';
});
</script>
@endpush