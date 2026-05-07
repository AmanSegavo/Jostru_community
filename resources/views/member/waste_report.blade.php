@extends('layouts.app')
@section('title', 'Setor Limbah Cepat')

@push('styles')
<style>
    .input-limbah-container {
        max-width: 600px;
        margin: 0 auto;
        padding-bottom: 80px;
    }
    .big-btn {
        border-radius: 20px;
        font-weight: 800;
        transition: all 0.2s ease;
        border: none;
    }
    .big-btn:active {
        transform: scale(0.95);
    }
    
    /* Tombol Jenis Limbah */
    .type-btn {
        border: 2px solid var(--border-color);
        background: var(--surface-color);
        color: var(--text-secondary);
        border-radius: 16px;
        padding: 15px 10px;
        font-weight: 700;
        cursor: pointer;
        transition: all 0.2s;
        text-align: center;
        width: 100%;
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 8px;
    }
    .type-btn.active {
        border-color: #22c55e;
        background: rgba(34, 197, 94, 0.1);
        color: #22c55e;
    }
    
    /* Tombol Kamera Raksasa */
    .camera-btn {
        background: linear-gradient(135deg, #22c55e, #10b981);
        color: white;
        border-radius: 24px;
        height: 150px;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        font-size: 1.2rem;
        font-weight: 800;
        box-shadow: 0 10px 25px rgba(34, 197, 94, 0.3);
        position: relative;
        overflow: hidden;
    }
    
    /* Input Berat Besar */
    .weight-input-container {
        position: relative;
        display: flex;
        align-items: center;
    }
    .weight-input-container input {
        font-size: 3rem;
        font-weight: 900;
        height: 100px;
        text-align: center;
        border-radius: 24px;
        background: var(--surface-color);
        border: 2px solid var(--border-color);
        color: var(--text-primary);
    }
    .weight-input-container input:focus {
        border-color: #22c55e;
        box-shadow: 0 0 0 4px rgba(34, 197, 94, 0.1);
    }
    .weight-unit {
        position: absolute;
        right: 30px;
        font-size: 1.5rem;
        font-weight: 800;
        color: var(--text-secondary);
    }
</style>
@endpush

@section('content')
<div class="input-limbah-container animate-fade-in mt-3">
    <div class="text-center mb-4">
        <h2 style="font-weight: 800;">Setor Limbah</h2>
        <p class="text-muted">Proses cepat untuk pekerja lapangan</p>
    </div>

    @if(session('success'))
        <div class="alert alert-success" style="border-radius: 15px; font-weight: 600; text-align: center;">
            ✅ {{ session('success') }}
        </div>
    @endif

    <form action="{{ route('member.waste_report.store') }}" method="POST" enctype="multipart/form-data" id="wasteForm">
        @csrf
        
        <!-- 1. FOTO -->
        <div class="mb-4">
            <h5 class="fw-bold mb-3">1. Ambil Foto Limbah</h5>
            <label class="camera-btn w-100" id="cameraLabel">
                <div id="cameraIcon">
                    <svg width="48" height="48" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M23 19a2 2 0 0 1-2 2H3a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h4l2-3h6l2 3h4a2 2 0 0 1 2 2z"></path><circle cx="12" cy="13" r="4"></circle></svg>
                    <div class="mt-2">Ketuk untuk Foto</div>
                </div>
                <img id="preview-img" class="d-none w-100 h-100" style="object-fit: cover; position: absolute; top:0; left:0; border-radius: 24px;">
                <input type="file" name="image" id="mediaInput" accept="image/*,video/*" capture="environment" class="d-none">
            </label>
        </div>

        <!-- 2. JENIS LIMBAH -->
        <div class="mb-4">
            <h5 class="fw-bold mb-3">2. Jenis Limbah</h5>
            <input type="hidden" name="type" id="typeInput" value="Organik">
            <div class="row g-2">
                <div class="col-6">
                    <div class="type-btn active" onclick="selectType('Organik', this)">
                        <span style="font-size: 1.5rem;">🥬</span> Organik
                    </div>
                </div>
                <div class="col-6">
                    <div class="type-btn" onclick="selectType('Anorganik', this)">
                        <span style="font-size: 1.5rem;">🥤</span> Anorganik
                    </div>
                </div>
                <div class="col-6">
                    <div class="type-btn" onclick="selectType('B3', this)">
                        <span style="font-size: 1.5rem;">🔋</span> B3
                    </div>
                </div>
                <div class="col-6">
                    <div class="type-btn" onclick="selectType('Lainnya', this)">
                        <span style="font-size: 1.5rem;">📦</span> Lainnya
                    </div>
                </div>
            </div>
        </div>

        <!-- 3. BERAT -->
        <div class="mb-4">
            <h5 class="fw-bold mb-3">3. Berat Limbah</h5>
            <div class="weight-input-container">
                <input type="number" name="weight" step="0.1" min="0.1" class="form-control" required placeholder="0.0">
                <span class="weight-unit">KG</span>
            </div>
        </div>

        <!-- KETERANGAN (Opsional, Disembunyikan dalam Accordion) -->
        <div class="mb-4">
            <button class="btn btn-sm btn-link text-muted text-decoration-none p-0 fw-bold" type="button" data-bs-toggle="collapse" data-bs-target="#keteranganCollapse">
                + Tambah Keterangan (Opsional)
            </button>
            <div class="collapse mt-2" id="keteranganCollapse">
                <textarea name="description" class="form-control" rows="2" style="border-radius: 12px; background: var(--surface-color);" placeholder="Tulis catatan di sini..."></textarea>
            </div>
        </div>

        <button type="submit" class="btn btn-primary w-100 big-btn" id="submitBtn" style="padding: 20px; font-size: 1.3rem; background: #22c55e;">
            🚀 KIRIM SETORAN
        </button>
    </form>
    
    <hr class="my-5" style="border-color: var(--border-color);">
    
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h5 class="fw-bold m-0">Riwayat Terakhir</h5>
        <a href="#" class="text-success text-decoration-none fw-bold" style="font-size: 0.9rem;">Lihat Semua</a>
    </div>
    
    <div class="card glass p-0 border-0" style="border-radius: 20px; overflow: hidden;">
        @forelse($reports->take(3) as $report)
        <div class="p-3 border-bottom d-flex justify-content-between align-items-center" style="border-color: var(--border-color) !important;">
            <div>
                <div class="fw-bold">{{ $report->type }} ({{ $report->weight }} kg)</div>
                <small class="text-muted">{{ $report->created_at->format('d M, H:i') }}</small>
            </div>
            <div>
                @if($report->status == 'APPROVED')<span class="badge" style="background:rgba(34,197,94,0.1);color:#22c55e;">DISETUJUI</span>
                @elseif($report->status == 'REJECTED')<span class="badge" style="background:rgba(239,68,68,0.1);color:#ef4444;">DITOLAK</span>
                @else<span class="badge" style="background:rgba(245,158,11,0.1);color:#f59e0b;">PENDING</span>@endif
            </div>
        </div>
        @empty
        <div class="p-4 text-center text-muted">Belum ada riwayat</div>
        @endforelse
    </div>
</div>

@push('scripts')
<script>
    // Preview Image/Video
    const mediaInput = document.getElementById('mediaInput');
    const previewImg = document.getElementById('preview-img');
    const cameraIcon = document.getElementById('cameraIcon');

    mediaInput.addEventListener('change', function() {
        const file = this.files[0];
        if (!file) return;

        if (file.type.startsWith('image/')) {
            const reader = new FileReader();
            reader.onload = e => { 
                previewImg.src = e.target.result; 
                previewImg.classList.remove('d-none');
                cameraIcon.classList.add('d-none');
            };
            reader.readAsDataURL(file);
        } else if (file.type.startsWith('video/')) {
            // For video just show a video icon instead of preview to save memory on mobile
            cameraIcon.innerHTML = '<span style="font-size: 3rem;">🎥</span><div class="mt-2">Video Dipilih</div>';
        }
    });

    // Select Type
    function selectType(type, element) {
        document.getElementById('typeInput').value = type;
        document.querySelectorAll('.type-btn').forEach(btn => btn.classList.remove('active'));
        element.classList.add('active');
    }

    // Submit animation
    document.getElementById('wasteForm').addEventListener('submit', function() {
        const btn = document.getElementById('submitBtn');
        btn.disabled = true;
        btn.innerHTML = '⏳ MENGIRIM...';
        btn.style.opacity = '0.8';
    });
</script>
@endpush
@endsection