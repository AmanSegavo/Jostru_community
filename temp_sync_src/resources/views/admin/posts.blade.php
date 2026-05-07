@extends('layouts.admin')
@section('admin_content')

<div class="animate-fade-in">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 style="font-weight:800;">Kelola Community Feed</h2>
        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#postModal" 
                style="border-radius:12px;background:#22c55e;border:none;">
            + Buat Postingan Baru
        </button>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show mb-4" 
             style="border-radius:12px;background:rgba(34,197,94,0.1);color:#22c55e;border:1px solid rgba(34,197,94,0.2);">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="row">
        @forelse($posts as $post)
        <div class="col-md-6 col-lg-4 mb-4">
            <div class="card glass p-0 overflow-hidden h-100" 
                 style="border-radius:20px;border:1px solid rgba(var(--primary-rgb),0.1);">
                <div class="p-4">
                    <!-- Statistik Suka & Komentar untuk Admin -->
                    <div class="mt-3 pt-3" style="border-top: 1px solid rgba(0,0,0,0.05); display: flex; gap: 15px; color: var(--text-secondary); font-size: 0.85rem;">
                        <span class="badge bg-light text-dark border">
                            ❤️ {{ $post->likes ? $post->likes->count() : 0 }} Suka
                        </span>
                        <span class="badge bg-light text-dark border">
                            💬 {{ $post->comments ? $post->comments->count() : 0 }} Komentar
                        </span>
                    </div>
                    <!-- Header -->
                    <div class="d-flex justify-content-between align-items-start mb-3">
                        <div class="d-flex align-items-center">
                            <div style="width:35px;height:35px;background:#22c55e;border-radius:50%;
                                        display:flex;align-items:center;justify-content:center;color:white;
                                        font-weight:700;margin-right:12px;font-size:0.8rem;">
                                {{ substr($post->user->name, 0, 1) }}
                            </div>
                            <div>
                                <div style="font-weight:700;font-size:0.9rem;">{{ $post->user->name }}</div>
                                <small class="text-muted" style="font-size:0.75rem;">
                                    {{ $post->created_at->diffForHumans() }}
                                </small>
                            </div>
                        </div>

                        <form action="{{ route('admin.posts.destroy', $post->id) }}" method="POST" 
                              onsubmit="return confirm('Hapus postingan ini?')">
                            @csrf
                            @method('DELETE')
                            <button class="btn btn-link text-danger p-0">
                                <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                </svg>
                            </button>
                        </form>
                    </div>

                    <!-- Konten Teks -->
                    <p style="font-size:0.95rem;line-height:1.5;">{{ Str::limit($post->content, 150) }}</p>

                    <!-- MEDIA (Gambar / Video) -->
                   <!-- MEDIA (Gambar / Video) - DENGAN ZOOM -->
@if($post->media_path)
    <div class="mt-3 position-relative" style="border-radius:12px; overflow:hidden; border:1px solid #eee; cursor:pointer;" 
         onclick="showZoomMedia('{{ asset('feed/' . $post->media_path) }}', '{{ $post->media_type }}')">
        
        @php
            $ext = strtolower(pathinfo($post->media_path, PATHINFO_EXTENSION));
            $isVideo = in_array($ext, ['mp4', 'mov', 'avi', 'webm']);
        @endphp

        @if($isVideo)
            <video style="width:100%; max-height:260px; background:#000;" muted>
                <source src="{{ asset('feed/' . $post->media_path) }}">
            </video>
            <div class="position-absolute top-50 start-50 translate-middle text-white" style="font-size:2.5rem; opacity:0.8;">
                ▶
            </div>
        @else
            <img src="{{ asset('feed/' . $post->media_path) }}" 
                 style="width:100%; max-height:260px; object-fit:cover; display:block;"
                 onerror="this.style.display='none'">
        @endif
    </div>
@endif

{{-- Fallback untuk data lama --}}
@if(!$post->media_path && $post->image_path)
    <div class="mt-3 position-relative" style="border-radius:12px; overflow:hidden; border:1px solid #eee; cursor:pointer;" 
         onclick="showZoomMedia('{{ asset('storage/' . $post->image_path) }}', 'image')">
        <img src="{{ asset('storage/' . $post->image_path) }}" 
             style="width:100%; max-height:260px; object-fit:cover; display:block;"
             onerror="this.style.display='none'">
    </div>
@endif

                </div>
            </div>
        </div>
        @empty
        <div class="col-12">
            <div class="text-center py-5 text-muted">
                Belum ada postingan di Community Feed.
            </div>
        </div>
        @endforelse
    </div>

    <div class="mt-2">
        {{ $posts->links() }}
    </div>
</div>

<!-- Modal Buat Postingan -->
<div class="modal fade" id="postModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content glass" style="border-radius:24px;">
            <div class="modal-header border-0">
                <h5 class="modal-title" style="font-weight:800;">Buat Postingan Baru</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('admin.posts.store') }}" method="POST" enctype="multipart/form-data" id="postForm">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label" style="font-weight:600;">Konten Postingan</label>
                        <textarea name="content" class="form-control" rows="4" required 
                                  placeholder="Apa yang ingin Anda bagikan?" style="border-radius:12px;"></textarea>
                    </div>

                    <div class="mb-3">
                        <label class="form-label" style="font-weight:600;">Foto / Video (Opsional)</label>
                        <input type="file" name="image" id="mediaInput" accept="image/*,video/*" 
                               class="form-control" style="border-radius:12px;">
                        
                        <div id="media-preview" class="mt-2 d-none">
                            <img id="preview-img" class="img-fluid rounded d-none" style="max-height:180px;">
                            <video id="preview-video" class="img-fluid rounded d-none" style="max-height:180px;" controls></video>
                            <button type="button" id="remove-media" class="btn btn-sm btn-outline-danger mt-2">Hapus</button>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-0">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal" 
                            style="border-radius:12px;">Batal</button>
                    <button type="submit" class="btn btn-primary" id="submitBtn" 
                            style="border-radius:12px;background:#22c55e;border:none;">Bagikan</button>
                </div>
            </form>
        </div>
    </div>
</div>
<!-- ==================== MODAL ZOOM IMAGE / VIDEO ==================== -->
<div class="modal fade" id="zoomModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content" style="background:transparent; border:none;">
            <div class="modal-body p-0 position-relative">
                <button type="button" class="btn-close btn-close-white position-absolute top-0 end-0 m-3" 
                        style="z-index:10; font-size:1.5rem;" data-bs-dismiss="modal"></button>
                
                <div id="zoomContent" class="text-center">
                    <!-- Isi diisi via JavaScript -->
                </div>
            </div>
        </div>
    </div>
</div>

@endsection


@push('scripts')
<script>
function showZoomMedia(url, type) {
    const content = document.getElementById('zoomContent');
    
    if (type === 'video') {
        content.innerHTML = `
            <video controls autoplay style="max-width:100%; max-height:90vh; border-radius:12px;">
                <source src="${url}">
                Browser Anda tidak mendukung video.
            </video>
        `;
    } else {
        content.innerHTML = `
            <img src="${url}" style="max-width:100%; max-height:90vh; border-radius:12px; box-shadow:0 10px 30px rgba(0,0,0,0.6);" 
                 onerror="this.src='https://via.placeholder.com/800x600?text=Error+Loading+Image'">
        `;
    }
    
    const modal = new bootstrap.Modal(document.getElementById('zoomModal'));
    modal.show();
}
</script>
<script>
const mediaInput = document.getElementById('mediaInput');
const previewContainer = document.getElementById('media-preview');
const previewImg = document.getElementById('preview-img');
const previewVideo = document.getElementById('preview-video');
const removeBtn = document.getElementById('remove-media');
const form = document.getElementById('postForm');
const submitBtn = document.getElementById('submitBtn');

mediaInput.addEventListener('change', function() {
    const file = this.files[0];
    if (!file) return;

    previewContainer.classList.remove('d-none');
    previewImg.classList.add('d-none');
    previewVideo.classList.add('d-none');

    if (file.type.startsWith('image/')) {
        const reader = new FileReader();
        reader.onload = e => { previewImg.src = e.target.result; previewImg.classList.remove('d-none'); };
        reader.readAsDataURL(file);
    } else if (file.type.startsWith('video/')) {
        previewVideo.src = URL.createObjectURL(file);
        previewVideo.classList.remove('d-none');
    }
});

removeBtn.addEventListener('click', () => {
    mediaInput.value = '';
    previewContainer.classList.add('d-none');
    previewImg.src = '';
    previewVideo.src = '';
});

form.addEventListener('submit', () => {
    submitBtn.disabled = true;
    submitBtn.innerHTML = 'Mengirim...';
});
</script>
@endpush