@extends('layouts.admin')
@section('title', 'Galeri & Media CMS - Jostru')

@section('admin_content')
<style>
    .glass-card {
        background: rgba(255, 255, 255, 0.6);
        backdrop-filter: blur(12px);
        border: 1px solid rgba(255, 255, 255, 0.5);
        border-radius: 20px;
        box-shadow: 0 8px 32px rgba(0, 0, 0, 0.05);
    }
    .media-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
        gap: 1.5rem;
    }
    .media-item {
        position: relative;
        border-radius: 16px;
        overflow: hidden;
        aspect-ratio: 4/3;
        background: #e2e8f0;
        transition: transform 0.3s ease, box-shadow 0.3s ease;
    }
    .media-item:hover {
        transform: translateY(-5px);
        box-shadow: 0 12px 24px rgba(0,0,0,0.1);
    }
    .media-overlay {
        position: absolute;
        bottom: 0; left: 0; right: 0;
        background: linear-gradient(to top, rgba(0,0,0,0.8), transparent);
        padding: 20px 15px 15px;
        color: white;
        opacity: 0;
        transition: opacity 0.3s ease;
        display: flex;
        flex-direction: column;
        justify-content: flex-end;
    }
    .media-item:hover .media-overlay {
        opacity: 1;
    }
    .badge-category {
        position: absolute;
        top: 10px;
        right: 10px;
        padding: 5px 12px;
        border-radius: 20px;
        font-size: 0.75rem;
        font-weight: bold;
        background: rgba(255,255,255,0.9);
        color: #0f172a;
        box-shadow: 0 4px 6px rgba(0,0,0,0.1);
    }
    .nav-tabs .nav-link.active {
        background-color: var(--primary);
        color: white !important;
        border-radius: 12px;
    }
    .nav-tabs .nav-link {
        color: var(--text-secondary);
        border: none;
        font-weight: 600;
        padding: 10px 20px;
    }
</style>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="fw-bold mb-1" style="color: var(--text-primary);">Galeri & Media CMS</h2>
        <p class="text-muted">Pusat kontrol multimedia interaktif untuk Landing Page.</p>
    </div>
</div>

@if(session('success'))
<div class="alert alert-success border-0 mb-4" style="background: rgba(34, 197, 94, 0.1); color: #22c55e; border-radius: 12px;">
    {{ session('success') }}
</div>
@endif
@if($errors->any())
<div class="alert alert-danger border-0 mb-4" style="background: rgba(239, 68, 68, 0.1); color: #ef4444; border-radius: 12px;">
    @foreach ($errors->all() as $error)
        <div>- {{ $error }}</div>
    @endforeach
</div>
@endif

<!-- Add Media Section -->
<div class="glass-card p-4 mb-5">
    <h5 class="fw-bold mb-4">Tambah Media Baru</h5>
    
    <ul class="nav nav-tabs mb-4 border-0 gap-2" id="mediaTabs" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link active" id="upload-tab" data-bs-toggle="tab" data-bs-target="#upload" type="button" role="tab">📤 Upload File Asli</button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="embed-tab" data-bs-toggle="tab" data-bs-target="#embed" type="button" role="tab">🔗 Embed Sosial Media</button>
        </li>
    </ul>

    <div class="tab-content" id="mediaTabContent">
        <!-- Upload Tab -->
        <div class="tab-pane fade show active" id="upload" role="tabpanel">
            <form action="{{ route('admin.media.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="upload_type" value="file">
                <div class="row g-3">
                    <div class="col-md-4">
                        <label class="form-label fw-bold">Pilih File (Gambar/Video)</label>
                        <input type="file" name="media" class="form-control" accept="image/*,video/*" required style="border-radius: 12px;">
                        <small class="text-muted mt-1 d-block">Maksimal 100MB.</small>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label fw-bold">Label / Judul</label>
                        <input type="text" name="title" class="form-control" placeholder="Contoh: Kegiatan 2026" style="border-radius: 12px;">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label fw-bold">Kategori</label>
                        <select name="category" class="form-select" style="border-radius: 12px;" required>
                            <option value="gallery">Galeri Umum</option>
                            <option value="banner">Banner Atas</option>
                            <option value="post">Postingan Khusus</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label fw-bold">Divisi (Opsional)</label>
                        <select name="division_id" class="form-select" style="border-radius: 12px;">
                            <option value="">-- Semua / Umum --</option>
                            @foreach($divisions as $div)
                            <option value="{{ $div->id }}">{{ $div->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    
                    <div class="col-12 mt-3">
                        <label class="form-label fw-bold d-block">Orientasi Gambar (Masonry Grid)</label>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="orientation" id="ori_land1" value="landscape" checked>
                            <label class="form-check-label" for="ori_land1">Horizontal (Landscape)</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="orientation" id="ori_port1" value="portrait">
                            <label class="form-check-label" for="ori_port1">Vertikal (Portrait)</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="orientation" id="ori_sq1" value="square">
                            <label class="form-check-label" for="ori_sq1">Kotak (1:1)</label>
                        </div>
                    </div>
                </div>
                <div class="mt-4 text-end">
                    <button type="submit" class="btn btn-primary px-5" style="border-radius: 12px; font-weight: 700;">Simpan & Upload</button>
                </div>
            </form>
        </div>

        <!-- Embed Tab -->
        <div class="tab-pane fade" id="embed" role="tabpanel">
            <form action="{{ route('admin.media.store') }}" method="POST">
                @csrf
                <input type="hidden" name="upload_type" value="embed">
                <div class="row g-3">
                    <div class="col-md-4">
                        <label class="form-label fw-bold">Link URL (IG/YT)</label>
                        <input type="url" name="source_url" class="form-control" placeholder="https://..." required style="border-radius: 12px;">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label fw-bold">Label / Judul</label>
                        <input type="text" name="title" class="form-control" placeholder="Contoh: Instagram Reel" style="border-radius: 12px;" required>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label fw-bold">Kategori</label>
                        <select name="category" class="form-select" style="border-radius: 12px;" required>
                            <option value="post">Postingan Khusus</option>
                            <option value="gallery">Galeri Umum</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label fw-bold">Divisi (Opsional)</label>
                        <select name="division_id" class="form-select" style="border-radius: 12px;">
                            <option value="">-- Semua / Umum --</option>
                            @foreach($divisions as $div)
                            <option value="{{ $div->id }}">{{ $div->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    
                    <div class="col-12 mt-3">
                        <label class="form-label fw-bold d-block">Orientasi Kotak Tampil</label>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="orientation" id="ori_land2" value="landscape">
                            <label class="form-check-label" for="ori_land2">Horizontal (Landscape)</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="orientation" id="ori_port2" value="portrait" checked>
                            <label class="form-check-label" for="ori_port2">Vertikal (Portrait)</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="orientation" id="ori_sq2" value="square">
                            <label class="form-check-label" for="ori_sq2">Kotak (1:1)</label>
                        </div>
                    </div>
                </div>
                <div class="mt-4 text-end">
                    <button type="submit" class="btn btn-primary px-5" style="border-radius: 12px; font-weight: 700; background: #8b5cf6; border:none;">Simpan Embed</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Gallery Grid -->
<h4 class="fw-bold mb-4">Koleksi Media</h4>
<div class="media-grid">
    @forelse($mediaFiles as $media)
    <div class="media-item">
        @if($media->category == 'banner')
            <span class="badge-category" style="color: #ea580c;">🌟 Banner</span>
        @elseif($media->category == 'post')
            <span class="badge-category" style="color: #4f46e5;">📝 Post</span>
        @else
            <span class="badge-category">📸 Galeri</span>
        @endif

        @if($media->type == 'image')
            <div style="height: 100%; width: 100%; background: url('{{ $media->url }}') center/cover;"></div>
        @elseif($media->type == 'video')
            <video src="{{ $media->url }}" style="height: 100%; width: 100%; object-fit: cover;" preload="metadata"></video>
            <div style="position: absolute; top:50%; left:50%; transform:translate(-50%,-50%); width:50px; height:50px; background:rgba(0,0,0,0.5); border-radius:50%; display:flex; align-items:center; justify-content:center; pointer-events:none;">
                <svg width="24" height="24" fill="white" viewBox="0 0 16 16"><path d="M10.804 8 5 4.633v6.734L10.804 8zm.792-.696a.802.802 0 0 1 0 1.392l-6.363 3.692C4.713 12.69 4 12.345 4 11.692V4.308c0-.653.713-.998 1.233-.696l6.363 3.692z"/></svg>
            </div>
        @elseif($media->type == 'embed')
            <div style="height: 100%; width: 100%; background: #1e293b; display:flex; align-items:center; justify-content:center; color:white; text-align:center; padding:20px;">
                <div>
                    <svg width="40" height="40" fill="currentColor" class="mb-2 text-primary" viewBox="0 0 16 16"><path d="M8 0C5.829 0 5.556.01 4.703.048 3.85.088 3.269.222 2.76.42a3.917 3.917 0 0 0-1.417.923A3.927 3.927 0 0 0 .42 2.76C.222 3.268.087 3.85.048 4.7.01 5.555 0 5.827 0 8.001c0 2.172.01 2.444.048 3.297.04.852.174 1.433.372 1.942.205.526.478.972.923 1.417.444.445.89.719 1.416.923.51.198 1.09.333 1.942.372C5.555 15.99 5.827 16 8 16s2.444-.01 3.298-.048c.851-.04 1.434-.174 1.943-.372a3.916 3.916 0 0 0 1.416-.923c.445-.445.718-.891.923-1.417.197-.509.332-1.09.372-1.942C15.99 10.445 16 10.173 16 8s-.01-2.445-.048-3.299c-.04-.851-.175-1.433-.372-1.941a3.926 3.926 0 0 0-.923-1.417A3.911 3.911 0 0 0 13.24.42c-.51-.198-1.092-.333-1.943-.372C10.443.01 10.172 0 7.998 0h.003zm-.717 1.442h.718c2.136 0 2.389.007 3.232.046.78.036 1.204.166 1.486.275.373.145.64.319.92.599.28.28.453.546.598.92.11.281.24.705.275 1.485.039.843.047 1.096.047 3.231s-.008 2.389-.047 3.232c-.035.78-.166 1.203-.275 1.485a2.47 2.47 0 0 1-.599.919c-.28.28-.546.453-.92.598-.28.11-.704.24-1.485.276-.843.038-1.096.047-3.232.047s-2.39-.009-3.233-.047c-.78-.036-1.203-.166-1.485-.276a2.478 2.478 0 0 1-.92-.598 2.48 2.48 0 0 1-.6-.92c-.109-.281-.24-.705-.275-1.485-.038-.843-.046-1.096-.046-3.233 0-2.136.008-2.388.046-3.231.036-.78.166-1.204.276-1.486.145-.373.319-.64.599-.92.28-.28.546-.453.92-.598.282-.11.705-.24 1.485-.276.738-.034 1.024-.044 2.515-.045v.002zm4.988 1.328a.96.96 0 1 0 0 1.92.96.96 0 0 0 0-1.92zm-4.27 1.122a4.109 4.109 0 1 0 0 8.217 4.109 4.109 0 0 0 0-8.217zm0 1.441a2.667 2.667 0 1 1 0 5.334 2.667 2.667 0 0 1 0-5.334z"/></svg>
                    <div style="font-size: 0.85rem;">Tautan Embed</div>
                </div>
            </div>
        @endif

        <div class="media-overlay">
            <h6 class="text-truncate mb-1 fw-bold" style="font-size: 1rem;">{{ $media->title }}</h6>
            <div class="d-flex justify-content-between align-items-center mt-2">
                <small style="color: #cbd5e1;">{{ $media->size ?? 'Embed Link' }}</small>
                <div class="d-flex gap-2">
                    <button class="btn btn-sm btn-light" onclick="openEditModal({{ $media->id }}, '{{ addslashes($media->title) }}', '{{ $media->category }}', '{{ $media->division_id }}', '{{ $media->orientation }}')" style="border-radius: 8px; padding: 4px 10px;">Edit</button>
                    <form action="{{ route('admin.media.destroy', $media->id) }}" method="POST" onsubmit="return confirm('Hapus media ini?')">
                        @csrf
                        @method('DELETE')
                        <button class="btn btn-sm btn-danger" style="border-radius: 8px; padding: 4px 10px;">Hapus</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    @empty
    <div style="grid-column: 1 / -1;" class="text-center text-muted py-5">
        <div style="font-size: 3rem; margin-bottom: 1rem;">📭</div>
        <h5>Belum ada media di CMS ini.</h5>
        <p>Silakan upload file atau tempel tautan embed.</p>
    </div>
    @endforelse
</div>

<!-- Modal Edit Label -->
<div class="modal fade" id="editMediaModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <form id="editMediaForm" method="POST" class="w-100">
            @csrf
            @method('PUT')
            <div class="modal-content glass-card" style="border-radius: 24px;">
                <div class="modal-header border-0 pb-0">
                    <h5 class="modal-title fw-bold">Edit Informasi Media</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-bold">Label / Judul / Caption</label>
                        <input type="text" name="title" id="edit_title" class="form-control" style="border-radius: 12px;" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Kategori Tampil</label>
                        <select name="category" id="edit_category" class="form-select" style="border-radius: 12px;" required>
                            <option value="banner">Banner Atas (Korsel)</option>
                            <option value="gallery">Galeri Umum</option>
                            <option value="post">Postingan Khusus</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Divisi Terkait (Opsional)</label>
                        <select name="division_id" id="edit_division" class="form-select" style="border-radius: 12px;">
                            <option value="">-- Semua / Umum --</option>
                            @foreach($divisions as $div)
                            <option value="{{ $div->id }}">{{ $div->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold d-block">Orientasi Gambar (Masonry)</label>
                        <select name="orientation" id="edit_orientation" class="form-select" style="border-radius: 12px;">
                            <option value="landscape">Horizontal (Landscape)</option>
                            <option value="portrait">Vertikal (Portrait)</option>
                            <option value="square">Kotak (1:1)</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer border-0 pt-0">
                    <button type="submit" class="btn btn-primary w-100" style="border-radius: 12px; font-weight: 700;">Simpan Perubahan</button>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
    function openEditModal(id, title, category, division_id, orientation) {
        document.getElementById('editMediaForm').action = `/admin/media/${id}`;
        document.getElementById('edit_title').value = title;
        document.getElementById('edit_category').value = category;
        document.getElementById('edit_division').value = division_id;
        document.getElementById('edit_orientation').value = orientation;
        var myModal = new bootstrap.Modal(document.getElementById('editMediaModal'));
        myModal.show();
    }
</script>
@endsection
