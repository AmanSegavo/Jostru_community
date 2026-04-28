@extends('layouts.admin')

@section('admin_content')
<div class="animate-fade-in">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 style="font-weight: 800;">Kelola Community Feed</h2>
        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#postModal" style="border-radius: 12px; background: #22c55e; border: none;">
            + Buat Postingan Baru
        </button>
    </div>

    @if(session('success'))
        <div class="alert alert-success border-0 shadow-sm mb-4" style="border-radius: 15px; background: rgba(34, 197, 94, 0.1); color: #22c55e;">
            {{ session('success') }}
        </div>
    @endif

    <div class="row">
        @foreach($posts as $post)
        <div class="col-md-6 col-lg-4 mb-4">
            <div class="card glass p-0 overflow-hidden h-100" style="border-radius: 20px; border: 1px solid rgba(var(--primary-rgb), 0.1);">
                <div class="p-4">
                    <div class="d-flex justify-content-between align-items-start mb-3">
                        <div class="d-flex align-items-center">
                            <div style="width: 35px; height: 35px; background: #22c55e; border-radius: 50%; display: flex; align-items: center; justify-content: center; color: white; font-weight: 700; margin-right: 12px; font-size: 0.8rem;">
                                {{ substr($post->user->name, 0, 1) }}
                            </div>
                            <div>
                                <div style="font-weight: 700; font-size: 0.9rem;">{{ $post->user->name }}</div>
                                <small class="text-muted" style="font-size: 0.75rem;">{{ $post->created_at->diffForHumans() }}</small>
                            </div>
                        </div>
                        <form action="{{ route('admin.posts.destroy', $post->id) }}" method="POST" onsubmit="return confirm('Hapus postingan ini?')">
                            @csrf
                            @method('DELETE')
                            <button class="btn btn-link text-danger p-0">
                                <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                            </button>
                        </form>
                    </div>
                    <p style="font-size: 0.95rem; line-height: 1.5;">{{ Str::limit($post->content, 150) }}</p>
                    @if($post->image_path)
                    <img src="{{ asset('storage/' . $post->image_path) }}" class="img-fluid rounded mt-2" style="width: 100%; height: 150px; object-fit: cover;">
                    @endif
                </div>
            </div>
        </div>
        @endforeach
    </div>
    <div class="mt-4">
        {{ $posts->links() }}
    </div>
</div>

<!-- Modal Create Post -->
<div class="modal fade" id="postModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content glass" style="border-radius: 24px; border: 1px solid rgba(255,255,255,0.2);">
            <div class="modal-header border-0">
                <h5 class="modal-title" style="font-weight: 800;">Buat Postingan Baru</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('admin.posts.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label" style="font-weight: 600;">Konten Postingan</label>
                        <textarea name="content" class="form-control" rows="5" required placeholder="Apa yang ingin Anda bagikan hari ini?" style="border-radius: 12px;"></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label" style="font-weight: 600;">Gambar (Opsional)</label>
                        <input type="file" name="image" class="form-control" style="border-radius: 12px;">
                    </div>
                </div>
                <div class="modal-footer border-0">
                    <button type="button" class="btn btn-outline" data-bs-dismiss="modal" style="border-radius: 12px;">Batal</button>
                    <button type="submit" class="btn btn-primary" style="border-radius: 12px; background: #22c55e; border: none; padding: 10px 25px; font-weight: 700;">Bagikan</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
