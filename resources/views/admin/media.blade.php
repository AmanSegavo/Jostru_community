@extends('layouts.admin')
@section('title', 'Galeri & Media CMS - Jostru')

@section('admin_content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="fw-bold mb-1" style="color: var(--primary);">Galeri & Media CMS</h2>
        <p class="text-muted">Kelola foto dan video resolusi tinggi untuk komunitas.</p>
    </div>
</div>

@if(session('success'))
<div class="alert alert-success border-0" style="background: rgba(34, 197, 94, 0.1); color: #22c55e; border-radius: 12px;">
    {{ session('success') }}
</div>
@endif
@if(session('error'))
<div class="alert alert-danger border-0" style="background: rgba(239, 68, 68, 0.1); color: #ef4444; border-radius: 12px;">
    {{ session('error') }}
</div>
@endif

<div class="card glass p-4 mb-4" style="border-radius: 20px;">
    <h5 class="fw-bold mb-3">Upload Media Baru</h5>
    <form action="{{ route('admin.media.store') }}" method="POST" enctype="multipart/form-data" class="d-flex gap-3 align-items-center flex-wrap">
        @csrf
        <div class="flex-grow-1">
            <input type="file" name="media" class="form-control form-control-lg" accept="image/*,video/*" required style="border-radius: 12px; background: var(--surface-color);">
            <small class="text-muted mt-2 d-block">Maksimal ukuran: 100MB. Format: JPG, PNG, MP4, MOV, AVI.</small>
        </div>
        <button type="submit" class="btn btn-primary btn-lg" style="border-radius: 12px; font-weight: 700; background: #22c55e; border: none; padding: 0 30px;">
            Upload
        </button>
    </form>
</div>

<div class="row g-4">
    @forelse($mediaFiles as $media)
    <div class="col-md-4 col-lg-3">
        <div class="card glass h-100 p-0 overflow-hidden" style="border-radius: 16px;">
            @if($media['type'] == 'image')
                <div style="height: 200px; width: 100%; background: url('{{ $media['url'] }}') center/cover;"></div>
            @else
                <video src="{{ $media['url'] }}" style="height: 200px; width: 100%; object-fit: cover;" controls preload="metadata"></video>
            @endif
            <div class="p-3 d-flex flex-column justify-content-between flex-grow-1">
                <div>
                    <h6 class="text-truncate mb-1" title="{{ $media['name'] }}">{{ $media['name'] }}</h6>
                    <small class="text-muted d-block">{{ $media['size'] }}</small>
                </div>
                <div class="mt-3 d-flex justify-content-between align-items-center">
                    <button class="btn btn-sm btn-outline-primary" onclick="copyToClipboard('{{ $media['url'] }}')" style="border-radius: 8px;">
                        Salin URL
                    </button>
                    <form action="{{ route('admin.media.destroy', $media['name']) }}" method="POST" onsubmit="return confirm('Hapus media ini?')">
                        @csrf
                        @method('DELETE')
                        <button class="btn btn-sm btn-danger" style="border-radius: 8px;">Hapus</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    @empty
    <div class="col-12 text-center text-muted py-5">
        <div style="font-size: 3rem; margin-bottom: 1rem;">📭</div>
        <h5>Belum ada media yang diunggah.</h5>
    </div>
    @endforelse
</div>

<script>
    function copyToClipboard(text) {
        navigator.clipboard.writeText(text).then(() => {
            alert('URL berhasil disalin!');
        });
    }
</script>
@endsection
