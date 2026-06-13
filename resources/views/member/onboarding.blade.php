@extends('layouts.member')

@section('member_content')
<style>
    .onboarding-hero {
        background: linear-gradient(135deg, var(--primary), #3b82f6);
        border-radius: 20px;
        padding: 3rem 2rem;
        color: white;
        text-align: center;
        margin-bottom: 2rem;
        box-shadow: 0 10px 30px rgba(99, 102, 241, 0.2);
    }
    .onboarding-hero h1 {
        font-weight: 800;
        font-size: 2.5rem;
        margin-bottom: 1rem;
    }
    .onboarding-hero p {
        font-size: 1.1rem;
        opacity: 0.9;
        max-width: 600px;
        margin: 0 auto;
    }
    .feature-card {
        background: var(--surface-color);
        border-radius: 16px;
        padding: 2rem;
        height: 100%;
        box-shadow: 0 4px 15px rgba(0,0,0,0.05);
        border: 1px solid var(--border-color);
        transition: transform 0.2s;
    }
    .feature-card:hover {
        transform: translateY(-5px);
    }
    .feature-icon {
        width: 60px;
        height: 60px;
        border-radius: 12px;
        background: rgba(99, 102, 241, 0.1);
        color: var(--primary);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.8rem;
        margin-bottom: 1.5rem;
    }
    .interview-form {
        background: var(--surface-color);
        border-radius: 20px;
        padding: 2.5rem;
        box-shadow: 0 4px 25px rgba(0,0,0,0.06);
        border: 1px solid var(--border-color);
    }
    .form-control-custom {
        background: var(--bg-color);
        border: 1px solid var(--border-color);
        border-radius: 12px;
        padding: 1rem;
        color: var(--text-primary);
        transition: all 0.3s;
    }
    .form-control-custom:focus {
        border-color: var(--primary);
        box-shadow: 0 0 0 4px rgba(99, 102, 241, 0.1);
        background: var(--surface-color);
    }
</style>

<div class="container-fluid">
    <div class="onboarding-hero">
        <h1>Selamat Datang di Jostru! 🚀</h1>
        <p>Anda belum ditentukan divisi mana. Mari kenalan lebih dalam agar kami bisa menempatkan Anda di posisi yang paling tepat untuk berkembang bersama.</p>
    </div>

    <div class="row g-4 mb-5">
        <div class="col-md-4">
            <div class="feature-card text-center">
                <div class="feature-icon mx-auto">🌱</div>
                <h4 style="font-weight:700; color:var(--text-primary);">Visi Berkelanjutan</h4>
                <p class="text-muted mb-0">Kami berkomitmen membangun ekosistem peternakan dan pertanian modern yang terintegrasi, mandiri, dan berwawasan lingkungan.</p>
            </div>
        </div>
        <div class="col-md-4">
            <div class="feature-card text-center">
                <div class="feature-icon mx-auto" style="background:rgba(34, 197, 94, 0.1); color:#22c55e;">🤝</div>
                <h4 style="font-weight:700; color:var(--text-primary);">Komunitas Solid</h4>
                <p class="text-muted mb-0">Jostru bukan sekadar tempat kerja, melainkan keluarga besar yang saling mendukung, berbagi ilmu, dan tumbuh bersama.</p>
            </div>
        </div>
        <div class="col-md-4">
            <div class="feature-card text-center">
                <div class="feature-icon mx-auto" style="background:rgba(245, 158, 11, 0.1); color:#f59e0b;">💡</div>
                <h4 style="font-weight:700; color:var(--text-primary);">Inovasi Tanpa Batas</h4>
                <p class="text-muted mb-0">Kami selalu terbuka terhadap ide-ide baru, teknologi tepat guna, dan strategi kreatif untuk memajukan komunitas.</p>
            </div>
        </div>
    </div>

    @if($interview)
        <div class="alert alert-success d-flex align-items-center" style="border-radius:16px; padding:2rem;">
            <i class="bi bi-check-circle-fill me-3" style="font-size:2rem;"></i>
            <div>
                <h4 class="alert-heading fw-bold mb-1">Formulir Sedang Ditinjau</h4>
                <p class="mb-0">Terima kasih telah mengisi mini-interview. Tim Admin kami sedang meninjau jawaban Anda untuk menentukan penempatan divisi yang paling sesuai. Mohon tunggu informasi selanjutnya.</p>
            </div>
        </div>
    @else
        <div class="interview-form">
            <div class="text-center mb-4">
                <h3 style="font-weight:800; color:var(--text-primary);">Mari Mengenal Anda Lebih Jauh</h3>
                <p class="text-muted">Isi pertanyaan singkat berikut untuk membantu kami mengenal potensi Anda.</p>
            </div>

            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show shadow-sm" role="alert" style="border-radius: 12px; font-weight: 600; border-left: 5px solid #198754;">
                    <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            <form action="{{ route('member.onboarding.submit') }}" method="POST" enctype="multipart/form-data">
                @csrf
                
                <h5 style="font-weight:700; color:var(--text-primary); margin-top:2rem; margin-bottom:1rem; border-bottom: 2px solid var(--border-color); padding-bottom:10px;">Lengkapi Biodata Anda</h5>
                
                <div class="row mb-3">
                    <div class="col-md-6 mb-3">
                        <label class="form-label" style="font-weight:600; color:var(--text-secondary);">Tanggal Lahir <span class="text-danger">*</span></label>
                        <input type="date" name="tanggal_lahir" class="form-control form-control-custom" required value="{{ auth()->user()->tanggal_lahir }}">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label" style="font-weight:600; color:var(--text-secondary);">Alamat Lengkap <span class="text-danger">*</span></label>
                        <textarea name="alamat" class="form-control form-control-custom" rows="1" placeholder="Masukkan alamat lengkap..." required>{{ auth()->user()->alamat }}</textarea>
                    </div>
                </div>

                <h5 style="font-weight:700; color:var(--text-primary); margin-top:2rem; margin-bottom:1rem; border-bottom: 2px solid var(--border-color); padding-bottom:10px;">Pertanyaan Interview</h5>

                <div class="mb-4">
                    <label class="form-label" style="font-weight:600; color:var(--text-secondary);">1. Apa motivasi utama Anda bergabung dengan Jostru? <span class="text-danger">*</span></label>
                    <textarea name="motivation" class="form-control form-control-custom" rows="3" placeholder="Ceritakan motivasi Anda di sini..." required></textarea>
                </div>
                
                <div class="mb-4">
                    <label class="form-label" style="font-weight:600; color:var(--text-secondary);">2. Keahlian atau hobi apa yang paling Anda kuasai? <span class="text-danger">*</span></label>
                    <textarea name="skills" class="form-control form-control-custom" rows="3" placeholder="Misal: Desain Grafis, Bertani, Akuntansi, dll..." required></textarea>
                </div>

                <div class="mb-4">
                    <label class="form-label" style="font-weight:600; color:var(--text-secondary);">3. Ceritakan sedikit tentang pengalaman organisasi atau kerja Anda sebelumnya. <span class="text-danger">*</span></label>
                    <textarea name="experience" class="form-control form-control-custom" rows="3" placeholder="Ceritakan pengalaman Anda di sini..." required></textarea>
                </div>

                <div class="mb-4">
                    <label class="form-label" style="font-weight:600; color:var(--text-secondary);">4. Apa yang Anda harapkan dengan bergabung di divisi Jostru nantinya? <span class="text-danger">*</span></label>
                    <textarea name="expectations" class="form-control form-control-custom" rows="3" placeholder="Harapan Anda ke depan..." required></textarea>
                </div>

                <h5 style="font-weight:700; color:var(--text-primary); margin-top:2rem; margin-bottom:1rem; border-bottom: 2px solid var(--border-color); padding-bottom:10px;">Unggah Dokumen (Opsional)</h5>
                <p class="text-muted small mb-4">Unggah dokumen pendukung untuk memperkuat profil Anda (Format: JPG, PNG, PDF | Maks: 5MB per file).</p>
                
                <div class="row mb-4">
                    <div class="col-md-6 mb-3">
                        <label class="form-label" style="font-weight:600; color:var(--text-secondary);">File KTP</label>
                        <input type="file" name="ktp" class="form-control form-control-custom" accept=".jpg,.jpeg,.png,.pdf">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label" style="font-weight:600; color:var(--text-secondary);">File Kartu Keluarga (KK)</label>
                        <input type="file" name="kk" class="form-control form-control-custom" accept=".jpg,.jpeg,.png,.pdf">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label" style="font-weight:600; color:var(--text-secondary);">File Ijazah Terakhir</label>
                        <input type="file" name="ijazah" class="form-control form-control-custom" accept=".jpg,.jpeg,.png,.pdf">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label" style="font-weight:600; color:var(--text-secondary);">File Curriculum Vitae (CV)</label>
                        <input type="file" name="cv" class="form-control form-control-custom" accept=".pdf">
                    </div>
                    <div class="col-md-12 mb-3">
                        <label class="form-label" style="font-weight:600; color:var(--text-secondary);">Sertifikat / Penghargaan Lainnya</label>
                        <input type="file" name="sertifikat" class="form-control form-control-custom" accept=".jpg,.jpeg,.png,.pdf">
                    </div>
                </div>

                <button type="submit" class="btn btn-primary w-100 mt-2" style="padding:1rem; border-radius:12px; font-weight:700; font-size:1.1rem; box-shadow:0 4px 15px rgba(99,102,241,0.3);">
                    Kirim Jawaban & Dokumen <i class="bi bi-arrow-right ms-2"></i>
                </button>
            </form>
        </div>
    @endif
</div>
@endsection
