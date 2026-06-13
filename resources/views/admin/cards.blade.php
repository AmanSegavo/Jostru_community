@extends('layouts.admin')
@section('title', 'Manajemen Kartu Digital')
@push('styles')
<style>
    /* CSS for Card - Same as Member View */
    .card-container { display: flex; justify-content: center; align-items: center; padding: 2rem; perspective: 1000px; }
    #id-card-wrapper { width: 856px; height: 540px; position: relative; border-radius: 24px; background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%); box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5), inset 0 0 0 1px rgba(255,255,255,0.1); overflow: hidden; font-family: 'Inter', sans-serif; color: #ffffff; transform-origin: top left; transform: scale(0.5); }
    @media (max-width: 768px) { #id-card-wrapper { transform: scale(0.35); } }
    .card-bg-decoration { position: absolute; width: 600px; height: 600px; background: radial-gradient(circle, rgba(37,99,235,0.4) 0%, rgba(37,99,235,0) 70%); top: -200px; right: -200px; border-radius: 50%; z-index: 1; }
    .card-bg-decoration-2 { position: absolute; width: 400px; height: 400px; background: radial-gradient(circle, rgba(34,197,94,0.3) 0%, rgba(34,197,94,0) 70%); bottom: -150px; left: -100px; border-radius: 50%; z-index: 1; }
    .card-content { position: relative; z-index: 10; height: 100%; display: flex; flex-direction: column; padding: 40px 50px; }
    .card-header { display: flex; justify-content: space-between; align-items: flex-start; border-bottom: 2px solid rgba(255,255,255,0.1); padding-bottom: 20px; margin-bottom: 30px; }
    .card-header img { height: 70px; object-fit: contain; }
    .card-title { text-align: right; }
    .card-title h2 { font-weight: 900; font-size: 28px; margin: 0; letter-spacing: 2px; color: #e2e8f0; }
    .card-title p { color: #38bdf8; font-weight: 600; font-size: 14px; margin: 5px 0 0 0; letter-spacing: 4px; text-transform: uppercase; }
    .card-body-inner { display: flex; justify-content: space-between; align-items: center; flex: 1; }
    .user-details { flex: 1; padding-right: 40px; }
    .detail-group { margin-bottom: 24px; }
    .detail-label { font-size: 13px; color: #94a3b8; font-weight: 600; text-transform: uppercase; letter-spacing: 1.5px; margin-bottom: 4px; }
    .detail-value { font-size: 26px; font-weight: 800; color: #ffffff; margin: 0; text-shadow: 0 2px 4px rgba(0,0,0,0.3); }
    .detail-value.name { font-size: 34px; color: #fbbf24; }
    .qr-container { background: rgba(255, 255, 255, 0.95); padding: 15px; border-radius: 20px; box-shadow: 0 10px 25px rgba(0,0,0,0.3); display: flex; flex-direction: column; align-items: center; }
    .qr-container img { width: 180px; height: 180px; border-radius: 10px; }
    .qr-footer { margin-top: 10px; font-size: 11px; font-weight: 700; color: #1e293b; text-transform: uppercase; letter-spacing: 1px; }
    .glass-panel { background: rgba(255, 255, 255, 0.05); backdrop-filter: blur(10px); -webkit-backdrop-filter: blur(10px); border: 1px solid rgba(255, 255, 255, 0.1); border-radius: 20px; padding: 30px; }
</style>
@endpush
@section('admin_content')

<div class="animate-fade-in">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 style="font-weight:800;">Manajemen Kartu Digital</h2>
            <p class="text-muted mb-0">Lihat dan kelola kartu anggota yang sudah dibuat.</p>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show mb-4" style="border-radius:12px;background:rgba(34,197,94,0.1);color:#22c55e;border:1px solid rgba(34,197,94,0.2);">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="card glass p-0 overflow-hidden" style="border-radius:20px;">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead style="background:rgba(var(--primary-rgb),0.05);">
                    <tr>
                        <th class="px-4 py-3">No</th>
                        <th class="px-4 py-3">Anggota</th>
                        <th class="px-4 py-3">ID Member</th>
                        <th class="px-4 py-3">Status</th>
                        <th class="px-4 py-3 text-end">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($users as $index => $user)
                    <tr>
                        <td class="px-4 py-3">{{ $index + 1 }}</td>
                        <td class="px-4 py-3">
                            <div style="font-weight:600;">{{ $user->name ?? 'Tidak Diketahui' }}</div>
                            <small class="text-muted">{{ $user->email ?? '' }}</small>
                        </td>
                        <td class="px-4 py-3">
                            <span class="badge bg-primary">{{ $user->member_id ?? '-' }}</span>
                        </td>
                        <td class="px-4 py-3">
                            <span class="badge bg-success">AKTIF / SUDAH ACC</span>
                        </td>
                        <td class="px-4 py-3 text-end">
                            <button class="btn btn-sm btn-primary" onclick="showCardPreview('{{ addslashes(strtoupper($user->name ?? 'TANPA NAMA')) }}', '{{ $user->member_id ?? 'JC-NEW' }}', '{{ strtoupper($user->jabatan ?? 'ANGGOTA') }}', '{{ url('/v/' . ($user->member_id ?? 'JC-NEW')) }}')" style="border-radius:8px; font-weight:700;">
                                👁️ Lihat & Unduh
                            </button>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="text-center py-5 text-muted">Belum ada anggota yang berstatus Aktif.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    </div>
</div>

<!-- Modal Preview & Download Card -->
<div class="modal fade" id="cardModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content" style="border-radius: 24px; border: none; background: #f8fafc;">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title" style="font-weight: 800;">Pratinjau Kartu Digital</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body d-flex justify-content-center p-0" style="overflow: hidden; height: 320px;">
                <div class="card-container" style="padding: 1rem;">
                    <!-- Full CSS Card exactly like Member's -->
                    <div id="id-card-wrapper">
                        <div class="card-bg-decoration"></div>
                        <div class="card-bg-decoration-2"></div>
                        <div class="card-content">
                            <div class="card-header">
                                <img src="{{ asset('images/logo.png') }}" alt="Jostru Logo" onerror="this.src='https://ui-avatars.com/api/?name=Jostru&background=0D8ABC&color=fff&size=100'">
                                <div class="card-title">
                                    <h2>JOSTRU COMMUNITY</h2>
                                    <p>Official Member Card</p>
                                </div>
                            </div>
                            <div class="card-body-inner">
                                <div class="user-details glass-panel">
                                    <div class="detail-group">
                                        <div class="detail-label">Nama Lengkap</div>
                                        <div class="detail-value name" id="card-name">NAMA ANGGOTA</div>
                                    </div>
                                    <div class="detail-group">
                                        <div class="detail-label">ID Anggota</div>
                                        <div class="detail-value" id="card-id" style="font-family: monospace; letter-spacing: 2px;">ID-MEMBER</div>
                                    </div>
                                    <div class="row">
                                        <div class="col-6 detail-group mb-0">
                                            <div class="detail-label">Jabatan Utama</div>
                                            <div class="detail-value" id="card-role" style="font-size: 20px;">ROLE</div>
                                        </div>
                                        <div class="col-6 detail-group mb-0">
                                            <div class="detail-label">Status</div>
                                            <div class="detail-value text-success" style="font-size: 20px; color: #4ade80 !important;">AKTIF</div>
                                        </div>
                                    </div>
                                </div>
                                <div class="qr-container">
                                    <img id="card-qr" src="" alt="QR Code" crossorigin="anonymous">
                                    <div class="qr-footer">Scan untuk Validasi</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer border-0 justify-content-center pt-0 pb-4">
                <button type="button" id="download-btn" class="btn btn-primary btn-lg w-75" style="border-radius: 16px; font-weight: 700;">
                    📥 Unduh Kartu Resolusi Tinggi (PNG)
                </button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
<script>
    let currentCardName = '';

    function showCardPreview(name, id, role, qrUrl) {
        currentCardName = name;
        document.getElementById('card-name').innerText = name;
        document.getElementById('card-id').innerText = id;
        document.getElementById('card-role').innerText = role;
        document.getElementById('card-qr').src = "https://api.qrserver.com/v1/create-qr-code/?size=300x300&data=" + encodeURIComponent(qrUrl) + "&margin=0";
        
        var cardModal = new bootstrap.Modal(document.getElementById('cardModal'));
        cardModal.show();
    }

    document.getElementById('download-btn').addEventListener('click', function() {
        const btn = this;
        const originalText = btn.innerHTML;
        btn.innerHTML = 'Sedang Merender... ⏳';
        btn.disabled = true;

        const cardElement = document.getElementById('id-card-wrapper');
        const oldTransform = cardElement.style.transform;
        cardElement.style.transform = 'none';

        html2canvas(cardElement, {
            scale: 3, 
            useCORS: true, 
            backgroundColor: null
        }).then(canvas => {
            cardElement.style.transform = oldTransform;
            const link = document.createElement('a');
            link.download = 'ID_Card_Jostru_' + currentCardName.replace(/\s+/g, '_') + '.png';
            link.href = canvas.toDataURL('image/png');
            link.click();
            btn.innerHTML = originalText;
            btn.disabled = false;
        }).catch(err => {
            console.error(err);
            alert('Terjadi kesalahan. Pastikan internet stabil untuk meload gambar logo/QR.');
            cardElement.style.transform = oldTransform;
            btn.innerHTML = originalText;
            btn.disabled = false;
        });
    });
</script>
@endpush
@endsection