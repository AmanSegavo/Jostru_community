@extends('layouts.app')
@section('title', 'Lapor Setoran Limbah')

@section('content')
<div class="container mt-4 animate-fade-in">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 style="font-weight:800;">Setoran Limbah Saya</h2>
        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#reportModal" style="border-radius:12px;background:#22c55e;border:none;font-weight:600;">
            + Lapor Setoran Baru
        </button>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show mb-4" style="border-radius:15px;background:rgba(34,197,94,0.1);color:#22c55e;border:1px solid rgba(34,197,94,0.2);">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="card glass p-0 overflow-hidden" style="border-radius:20px;">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead style="background:rgba(var(--primary-rgb),0.05);">
                    <tr>
                        <th class="px-4 py-3">Tanggal</th>
                        <th class="px-4 py-3">Jenis Limbah</th>
                        <th class="px-4 py-3">Berat (kg)</th>
                        <th class="px-4 py-3">Status</th>
                        <th class="px-4 py-3">Keterangan</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($reports as $report)
                    <tr>
                        <td class="px-4 py-3">{{ $report->created_at->format('d M Y H:i') }}</td>
                        <td class="px-4 py-3"><span class="badge" style="background:rgba(var(--primary-rgb),0.1);color:var(--primary);">{{ $report->type }}</span></td>
                        <td class="px-4 py-3" style="font-weight:700;">{{ $report->weight }} kg</td>
                        <td class="px-4 py-3">
                            @if($report->status == 'APPROVED')<span class="badge bg-success" style="background:#22c55e!important;">DISETUJUI</span>
                            @elseif($report->status == 'REJECTED')<span class="badge bg-danger">DITOLAK</span>
                            @else<span class="badge bg-warning text-dark">PENDING</span>@endif
                        </td>
                        <td class="px-4 py-3 text-muted">{{ $report->description ?? '-' }}</td>
                    </tr>
                    @empty
                    <tr><td colspan="5" class="text-center py-5 text-muted">Belum ada riwayat setoran.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal -->
<div class="modal fade" id="reportModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content glass" style="border-radius:24px;">
            <div class="modal-header border-0">
                <h5 class="modal-title" style="font-weight:800;">Kirim Laporan Setoran</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('member.waste_report.store') }}" method="POST" enctype="multipart/form-data" id="wasteForm">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label" style="font-weight:600;">Jenis Limbah</label>
                        <select name="type" class="form-control" required style="border-radius:12px;">
                            <option value="Organik">Organik</option>
                            <option value="Anorganik">Anorganik</option>
                            <option value="B3">B3</option>
                            <option value="Lainnya">Lainnya</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label" style="font-weight:600;">Estimasi Berat (kg)</label>
                        <input type="number" name="weight" step="0.1" min="0.1" class="form-control" required style="border-radius:12px;">
                    </div>

                    <div class="mb-3">
                        <label class="form-label" style="font-weight:600;">Keterangan</label>
                        <textarea name="description" class="form-control" rows="2" style="border-radius:12px;"></textarea>
                    </div>

                    <!-- Upload Foto/Video -->
                    <div class="mb-3">
                        <label class="form-label" style="font-weight:600;">Foto / Video (Opsional)</label>
                        <input type="file" name="image" id="mediaInput" accept="image/*,video/*" class="form-control" style="border-radius:12px;">
                        
                        <div id="media-preview" class="mt-2 d-none">
                            <img id="preview-img" class="img-fluid rounded d-none" style="max-height:180px;">
                            <video id="preview-video" class="img-fluid rounded d-none" style="max-height:180px;" controls></video>
                            <button type="button" id="remove-media" class="btn btn-sm btn-outline-danger mt-2">Hapus</button>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-0">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal" style="border-radius:12px;">Batal</button>
                    <button type="submit" class="btn btn-primary" id="submitBtn" style="border-radius:12px;background:#22c55e;border:none;">Kirim Laporan</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
const mediaInput = document.getElementById('mediaInput');
const previewContainer = document.getElementById('media-preview');
const previewImg = document.getElementById('preview-img');
const previewVideo = document.getElementById('preview-video');
const removeBtn = document.getElementById('remove-media');
const form = document.getElementById('wasteForm');
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