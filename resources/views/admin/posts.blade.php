@extends('layouts.admin')
@section('title', 'CMS Community Feed - Jostru Admin')

@section('admin_content')
<style>
.cms-split { display: grid; grid-template-columns: 1fr 1fr; gap: 24px; align-items: start; }
.preview-card { background: var(--surface-color); border-radius: 20px; border: 1px solid rgba(var(--primary-rgb,99,102,241),0.15); overflow: hidden; }
.preview-avatar { width:44px;height:44px;background:#22c55e;border-radius:50%;display:flex;align-items:center;justify-content:center;color:#fff;font-weight:800;font-size:1rem;flex-shrink:0; }
.preview-content-text { font-size:1rem;line-height:1.65;white-space:pre-wrap;word-break:break-word; }
.preview-media { width:100%;max-height:400px;object-fit:cover;display:block;border-radius:0; }
.tag-pill { display:inline-block;padding:3px 12px;background:rgba(34,197,94,0.12);color:#22c55e;border-radius:50px;font-size:0.78rem;font-weight:600;margin:2px; }
.link-preview { margin-top:10px;padding:12px 14px;background:rgba(99,102,241,0.07);border-left:3px solid #6366f1;border-radius:0 10px 10px 0;word-break:break-all;font-size:0.85rem; }
.cms-section-label { font-size:0.7rem;font-weight:700;text-transform:uppercase;letter-spacing:.08em;color:var(--text-secondary);margin-bottom:6px; }
.feed-actions { display:flex;gap:20px;padding:12px 16px;border-top:1px solid var(--border-color,rgba(0,0,0,.07));background:var(--bg-color); }
.feed-action-btn { display:flex;align-items:center;gap:6px;font-size:0.87rem;color:var(--text-secondary);cursor:pointer; }
@media(max-width:900px){.cms-split{grid-template-columns:1fr;}}
</style>

<div class="animate-fade-in">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold mb-1" style="color:var(--primary);">✍️ CMS Community Feed</h2>
            <p class="text-muted mb-0">Buat & kelola konten feed — teks, media, link, dan tag — dengan pratinjau langsung.</p>
        </div>
    </div>

    @if(session('success'))
    <div class="alert border-0 mb-4" style="background:rgba(34,197,94,.1);color:#22c55e;border-radius:12px;">✅ {{ session('success') }}</div>
    @endif
    @if(session('error'))
    <div class="alert border-0 mb-4" style="background:rgba(239,68,68,.1);color:#ef4444;border-radius:12px;">❌ {{ session('error') }}</div>
    @endif

    {{-- ─── EDITOR + PREVIEW ─── --}}
    <div class="cms-split mb-5">

        {{-- EDITOR PANEL --}}
        <div class="card glass p-4" style="border-radius:20px;">
            <h5 class="fw-bold mb-3">📝 Editor Konten</h5>

            <form id="cmsForm" action="{{ route('admin.posts.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="_post_id" id="editPostId">

                {{-- Konten Teks --}}
                <div class="mb-3">
                    <div class="cms-section-label">Teks Konten *</div>
                    <textarea id="cmsContent" name="content" class="form-control" rows="6"
                        placeholder="Tulis teks postingan di sini..." required
                        style="border-radius:12px;resize:vertical;"
                        oninput="updatePreview()"></textarea>
                </div>

                {{-- Upload Media --}}
                <div class="mb-3">
                    <div class="cms-section-label">Foto / Video (Maks. 100MB)</div>
                    <label id="dropzone" style="display:block;border:2px dashed rgba(99,102,241,.35);border-radius:14px;padding:22px;text-align:center;cursor:pointer;transition:.2s;">
                        <input type="file" name="image" id="cmsMedia" accept="image/*,video/*" style="display:none" onchange="handleMedia(this)">
                        <div id="dropzoneHint">
                            <div style="font-size:2rem;">🖼️</div>
                            <p class="mb-0 text-muted small mt-1">Klik atau seret file gambar / video ke sini</p>
                        </div>
                        <div id="dropzoneInfo" class="d-none text-success fw-semibold small"></div>
                    </label>
                    <div class="mt-2" id="removeMediaRow" style="display:none!important">
                        <label class="d-flex align-items-center gap-2" style="cursor:pointer;font-size:.85rem;">
                            <input type="checkbox" name="remove_media" id="removeMedia" onchange="toggleRemoveMedia(this)"> Hapus media yang ada
                        </label>
                    </div>
                </div>

                {{-- Link URL --}}
                <div class="mb-3">
                    <div class="cms-section-label">Tautkan Link (Opsional)</div>
                    <input type="url" name="link_url" id="cmsLink" class="form-control"
                        placeholder="https://contoh.com/artikel"
                        style="border-radius:12px;"
                        oninput="updatePreview()">
                </div>

                {{-- Tags --}}
                <div class="mb-3">
                    <div class="cms-section-label">Tag / Topik (pisahkan dengan koma)</div>
                    <input type="text" name="tags" id="cmsTags" class="form-control"
                        placeholder="cth: limbah, daur ulang, jostru farm"
                        style="border-radius:12px;"
                        oninput="updatePreview()">
                </div>

                {{-- Sematkan --}}
                <div class="mb-4 d-flex align-items-center gap-2">
                    <input type="checkbox" name="pinned" id="cmsPinned" class="form-check-input" style="width:1.2rem;height:1.2rem;">
                    <label for="cmsPinned" class="fw-semibold small" style="cursor:pointer;">📌 Sematkan postingan ini (tampil di atas)</label>
                </div>

                <div class="d-flex gap-2">
                    <button type="submit" id="cmsSubmitBtn" class="btn btn-primary flex-grow-1" style="border-radius:12px;font-weight:700;background:#22c55e;border:none;padding:.75rem;">
                        📤 Publikasikan
                    </button>
                    <button type="button" onclick="resetEditor()" class="btn btn-outline-secondary" style="border-radius:12px;padding:.75rem 1.2rem;">Batal</button>
                </div>
            </form>
        </div>

        {{-- PREVIEW PANEL --}}
        <div style="position:sticky;top:90px;">
            <div class="cms-section-label mb-2 ps-1">👁️ Pratinjau — Persis seperti di Feed</div>
            <div class="preview-card">
                <div class="p-4">
                    <div class="d-flex align-items-center gap-3 mb-3">
                        <div class="preview-avatar">{{ substr(auth()->user()->name, 0, 1) }}</div>
                        <div>
                            <div class="fw-bold" style="font-size:.95rem;">{{ auth()->user()->name }}</div>
                            <small class="text-muted">Baru saja</small>
                        </div>
                        <span id="prevPinBadge" class="badge ms-auto d-none" style="background:rgba(99,102,241,.15);color:#6366f1;">📌 Disematkan</span>
                    </div>

                    <div id="prevText" class="preview-content-text text-muted" style="font-style:italic;">Teks postingan akan muncul di sini...</div>

                    <div id="prevTagsWrap" class="mt-2 d-none"></div>
                    <div id="prevLinkWrap" class="d-none">
                        <div class="link-preview">
                            <span style="color:#6366f1;">🔗</span>
                            <a id="prevLinkAnchor" href="#" target="_blank" style="color:#6366f1;text-decoration:none;font-weight:600;"></a>
                        </div>
                    </div>
                </div>

                <div id="prevMediaWrap" class="d-none">
                    <img id="prevMediaImg" class="preview-media d-none" alt="preview">
                    <video id="prevMediaVid" class="preview-media d-none" muted controls></video>
                </div>

                <div class="feed-actions">
                    <span class="feed-action-btn">
                        <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/></svg>
                        Suka
                    </span>
                    <span class="feed-action-btn">
                        <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 12.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/></svg>
                        Komentar
                    </span>
                </div>
            </div>

            <p class="text-muted text-center mt-2" style="font-size:.75rem;">Pratinjau diperbarui secara real-time</p>
        </div>
    </div>

    {{-- ─── DAFTAR POSTINGAN ─── --}}
    <div class="card glass p-4" style="border-radius:20px;">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h5 class="fw-bold mb-0">📋 Semua Postingan ({{ $posts->total() }})</h5>
        </div>

        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead style="background:rgba(var(--primary-rgb,99,102,241),.05);">
                    <tr>
                        <th class="px-3 py-3">Postingan</th>
                        <th class="px-3 py-3 text-center" style="width:90px;">Media</th>
                        <th class="px-3 py-3 text-center" style="width:80px;">Suka</th>
                        <th class="px-3 py-3 text-center" style="width:80px;">Komentar</th>
                        <th class="px-3 py-3" style="width:120px;">Tanggal</th>
                        <th class="px-3 py-3 text-end" style="width:120px;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($posts as $post)
                    <tr>
                        <td class="px-3 py-3">
                            <div class="fw-semibold small text-muted mb-1">{{ $post->user->name }}
                                @if($post->pinned) <span class="badge ms-1" style="background:rgba(99,102,241,.12);color:#6366f1;font-size:.7rem;">📌 Disematkan</span> @endif
                            </div>
                            <div style="font-size:.9rem;">{{ Str::limit($post->content, 100) }}</div>
                            @if($post->tags)
                            <div class="mt-1">
                                @foreach(explode(',', $post->tags) as $tag)
                                <span class="tag-pill">{{ trim($tag) }}</span>
                                @endforeach
                            </div>
                            @endif
                            @if($post->link_url)
                            <div class="mt-1"><a href="{{ $post->link_url }}" target="_blank" class="text-decoration-none" style="color:#6366f1;font-size:.8rem;">🔗 {{ Str::limit($post->link_url, 50) }}</a></div>
                            @endif
                        </td>
                        <td class="px-3 py-3 text-center">
                            @if($post->media_path)
                                @if($post->media_type === 'video')
                                    <span class="badge" style="background:rgba(99,102,241,.1);color:#6366f1;">🎬 Video</span>
                                @else
                                    <img src="{{ asset('feed/' . $post->media_path) }}" style="width:52px;height:52px;object-fit:cover;border-radius:8px;" onerror="this.replaceWith(document.createTextNode('⚠️'))">
                                @endif
                            @else
                                <span class="text-muted">—</span>
                            @endif
                        </td>
                        <td class="px-3 py-3 text-center">❤️ {{ $post->likes ? $post->likes->count() : 0 }}</td>
                        <td class="px-3 py-3 text-center">💬 {{ $post->comments ? $post->comments->count() : 0 }}</td>
                        <td class="px-3 py-3 text-muted" style="font-size:.82rem;">{{ $post->created_at->format('d M Y') }}</td>
                        <td class="px-3 py-3 text-end">
                            <button class="btn btn-sm btn-outline-primary me-1" style="border-radius:8px;"
                                onclick="editPost({{ json_encode([
                                    'id'       => $post->id,
                                    'content'  => $post->content,
                                    'link_url' => $post->link_url,
                                    'tags'     => $post->tags,
                                    'pinned'   => (bool)$post->pinned,
                                    'has_media'=> (bool)$post->media_path,
                                    'media_url'=> $post->media_path ? asset('feed/'.$post->media_path) : null,
                                    'media_type'=> $post->media_type,
                                ]) }})">✏️ Edit</button>
                            <form action="{{ route('admin.posts.destroy', $post->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Hapus postingan ini?')">
                                @csrf @method('DELETE')
                                <button class="btn btn-sm btn-outline-danger" style="border-radius:8px;">🗑️</button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="6" class="text-center py-5 text-muted">Belum ada postingan.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-3">{{ $posts->links() }}</div>
    </div>
</div>

<script>
// ─── Live Preview ───
function updatePreview() {
    const text    = document.getElementById('cmsContent').value;
    const link    = document.getElementById('cmsLink').value.trim();
    const tags    = document.getElementById('cmsTags').value.trim();
    const pinned  = document.getElementById('cmsPinned').checked;

    // Text
    const prevText = document.getElementById('prevText');
    if (text) {
        prevText.textContent = text;
        prevText.style.fontStyle = 'normal';
        prevText.style.color = 'var(--text-primary)';
    } else {
        prevText.textContent = 'Teks postingan akan muncul di sini...';
        prevText.style.fontStyle = 'italic';
        prevText.style.color = 'var(--text-secondary)';
    }

    // Pin badge
    document.getElementById('prevPinBadge').classList.toggle('d-none', !pinned);

    // Tags
    const tagsWrap = document.getElementById('prevTagsWrap');
    if (tags) {
        tagsWrap.innerHTML = tags.split(',').map(t => t.trim() ? `<span class="tag-pill">${t.trim()}</span>` : '').join('');
        tagsWrap.classList.remove('d-none');
    } else {
        tagsWrap.classList.add('d-none');
    }

    // Link
    const linkWrap = document.getElementById('prevLinkWrap');
    const linkAnchor = document.getElementById('prevLinkAnchor');
    if (link) {
        linkAnchor.href = link;
        linkAnchor.textContent = link;
        linkWrap.classList.remove('d-none');
    } else {
        linkWrap.classList.add('d-none');
    }
}

// ─── Handle Media File ───
function handleMedia(input) {
    const file = input.files[0];
    if (!file) return;

    const hint = document.getElementById('dropzoneHint');
    const info = document.getElementById('dropzoneInfo');
    hint.classList.add('d-none');
    info.classList.remove('d-none');
    info.textContent = `✅ ${file.name} (${(file.size/1048576).toFixed(2)} MB)`;

    const wrap = document.getElementById('prevMediaWrap');
    const img  = document.getElementById('prevMediaImg');
    const vid  = document.getElementById('prevMediaVid');
    img.classList.add('d-none');
    vid.classList.add('d-none');
    wrap.classList.remove('d-none');

    const url = URL.createObjectURL(file);
    if (file.type.startsWith('video/')) {
        vid.src = url;
        vid.classList.remove('d-none');
    } else {
        img.src = url;
        img.classList.remove('d-none');
    }
}

function toggleRemoveMedia(cb) {
    if (cb.checked) {
        document.getElementById('prevMediaWrap').classList.add('d-none');
        document.getElementById('prevMediaImg').src = '';
        document.getElementById('prevMediaVid').src = '';
    }
}

// ─── Edit Post ───
function editPost(post) {
    window.scrollTo({top:0,behavior:'smooth'});
    document.getElementById('editPostId').name = '_method_unused';

    // Change form action
    document.getElementById('cmsForm').action = `/admin/posts/${post.id}`;
    document.getElementById('cmsSubmitBtn').textContent = '💾 Simpan Perubahan';
    document.getElementById('cmsContent').value  = post.content;
    document.getElementById('cmsLink').value     = post.link_url || '';
    document.getElementById('cmsTags').value     = post.tags || '';
    document.getElementById('cmsPinned').checked = post.pinned;

    // Show existing media in preview
    const wrap = document.getElementById('prevMediaWrap');
    const img  = document.getElementById('prevMediaImg');
    const vid  = document.getElementById('prevMediaVid');
    img.classList.add('d-none');
    vid.classList.add('d-none');

    if (post.has_media && post.media_url) {
        wrap.classList.remove('d-none');
        if (post.media_type === 'video') { vid.src = post.media_url; vid.classList.remove('d-none'); }
        else { img.src = post.media_url; img.classList.remove('d-none'); }

        // Show remove media checkbox
        const row = document.getElementById('removeMediaRow');
        row.style.display = 'block';
    } else {
        wrap.classList.add('d-none');
    }

    updatePreview();
}

// ─── Reset Editor ───
function resetEditor() {
    document.getElementById('cmsForm').action = "{{ route('admin.posts.store') }}";
    document.getElementById('cmsSubmitBtn').textContent = '📤 Publikasikan';
    document.getElementById('cmsForm').reset();
    document.getElementById('dropzoneHint').classList.remove('d-none');
    document.getElementById('dropzoneInfo').classList.add('d-none');
    document.getElementById('removeMediaRow').style.display = 'none';
    document.getElementById('prevMediaWrap').classList.add('d-none');
    updatePreview();
}

// Drag & drop
const dz = document.getElementById('dropzone');
dz.addEventListener('dragover', e => { e.preventDefault(); dz.style.borderColor='#22c55e'; });
dz.addEventListener('dragleave', () => { dz.style.borderColor=''; });
dz.addEventListener('drop', e => {
    e.preventDefault();
    dz.style.borderColor='';
    const inp = document.getElementById('cmsMedia');
    inp.files = e.dataTransfer.files;
    handleMedia(inp);
});

// Init
updatePreview();

// Pin checkbox listener
document.getElementById('cmsPinned').addEventListener('change', updatePreview);
</script>
@endsection