@extends('layouts.admin')
@section('title', 'API & Integrasi OAuth - Jostru Admin')

@section('admin_content')
<div class="animate-fade-in">

    <div class="d-flex justify-content-between align-items-center mb-2">
        <div>
            <h2 class="fw-bold mb-1" style="color: var(--primary);">🔗 API & Integrasi OAuth 2.0</h2>
            <p class="text-muted mb-0">Kelola aplikasi pihak ketiga yang dapat terhubung ke Jostru Community menggunakan protokol OAuth 2.0.</p>
        </div>
    </div>

    {{-- Notifikasi --}}
    @if(session('success'))
    <div class="alert border-0 mb-4 p-3 d-flex align-items-center gap-2" style="background:rgba(34,197,94,0.12);color:#22c55e;border-radius:14px;">
        <span style="font-size:1.3rem;">✅</span> <span>{{ session('success') }}</span>
    </div>
    @endif
    @if(session('error'))
    <div class="alert border-0 mb-4 p-3" style="background:rgba(239,68,68,0.1);color:#ef4444;border-radius:14px;">
        ❌ {{ session('error') }}
    </div>
    @endif

    {{-- Info Endpoints --}}
    <div class="card glass p-4 mb-4" style="border-radius:20px; border-left: 4px solid var(--primary);">
        <h5 class="fw-bold mb-3">📡 Endpoint OAuth 2.0 Jostru</h5>
        <p class="text-muted small mb-3">Berikan informasi ini kepada developer atau aplikasi yang ingin konek ke Jostru:</p>
        <div class="row g-3">
            <div class="col-md-6">
                <label class="text-muted small fw-semibold">Authorization Endpoint</label>
                <div class="input-group mt-1">
                    <input type="text" class="form-control" readonly value="{{ url('/oauth/authorize') }}" id="ep_auth" style="font-family: monospace; font-size:0.85rem;">
                    <button class="btn btn-outline-secondary btn-sm" onclick="copyText('ep_auth')">Salin</button>
                </div>
            </div>
            <div class="col-md-6">
                <label class="text-muted small fw-semibold">Token Endpoint</label>
                <div class="input-group mt-1">
                    <input type="text" class="form-control" readonly value="{{ url('/oauth/token') }}" id="ep_token" style="font-family: monospace; font-size:0.85rem;">
                    <button class="btn btn-outline-secondary btn-sm" onclick="copyText('ep_token')">Salin</button>
                </div>
            </div>
            <div class="col-md-6">
                <label class="text-muted small fw-semibold">User Info Endpoint</label>
                <div class="input-group mt-1">
                    <input type="text" class="form-control" readonly value="{{ url('/api/user/me') }}" id="ep_user" style="font-family: monospace; font-size:0.85rem;">
                    <button class="btn btn-outline-secondary btn-sm" onclick="copyText('ep_user')">Salin</button>
                </div>
            </div>
            <div class="col-md-6">
                <label class="text-muted small fw-semibold">Cakupan (Scope) Tersedia</label>
                <div class="input-group mt-1">
                    <input type="text" class="form-control" readonly value="read-user" id="ep_scope" style="font-family: monospace; font-size:0.85rem;">
                    <button class="btn btn-outline-secondary btn-sm" onclick="copyText('ep_scope')">Salin</button>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4">
        {{-- Form Buat Client Baru --}}
        <div class="col-lg-5">
            <div class="card glass p-4 h-100" style="border-radius:20px;">
                <h5 class="fw-bold mb-3">➕ Buat OAuth Client Baru</h5>
                <form action="{{ route('admin.integrations.store') }}" method="POST">
                    @csrf
                    <div class="form-group mb-3">
                        <label class="fw-semibold small">Nama Aplikasi Klien</label>
                        <input type="text" name="name" class="form-control mt-1" placeholder="cth: Jostru Farm App, Jostru Coffee Dashboard" required style="border-radius:10px;">
                        <small class="text-muted">Nama aplikasi yang akan terhubung ke Jostru.</small>
                    </div>
                    <div class="form-group mb-4">
                        <label class="fw-semibold small">Redirect URL (Callback)</label>
                        <input type="url" name="redirect" class="form-control mt-1" placeholder="https://aplikasilain.com/auth/callback" required style="border-radius:10px;">
                        <small class="text-muted">URL yang akan menerima kode otorisasi setelah login berhasil.</small>
                    </div>
                    <button type="submit" class="btn btn-primary w-100" style="border-radius:10px; font-weight:700;">
                        🔑 Generate Client ID & Secret
                    </button>
                </form>
            </div>
        </div>

        {{-- Daftar Client Aktif --}}
        <div class="col-lg-7">
            <div class="card glass p-4 h-100" style="border-radius:20px;">
                <h5 class="fw-bold mb-3">📋 Aplikasi Terhubung ({{ count($clients) }})</h5>
                @forelse($clients as $client)
                <div class="p-3 mb-3" style="background: rgba(var(--primary-rgb,99,102,241),0.06); border-radius:12px; border:1px solid rgba(var(--primary-rgb,99,102,241),0.15);">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <h6 class="fw-bold mb-1">{{ $client->name }}</h6>
                            <small class="text-muted d-block">Redirect: {{ $client->redirect }}</small>
                            <div class="mt-2 d-flex gap-2 flex-wrap">
                                <span class="badge" style="background:rgba(99,102,241,0.15); color: var(--primary); font-size:0.75rem; font-family:monospace;">ID: {{ $client->id }}</span>
                                <span class="badge" style="background:rgba(34,197,94,0.1); color:#22c55e;">Aktif</span>
                            </div>
                        </div>
                        <form action="{{ route('admin.integrations.revoke', $client->id) }}" method="POST" onsubmit="return confirm('Cabut akses client ini? Semua token yang ada akan dinonaktifkan.')">
                            @csrf
                            @method('DELETE')
                            <button class="btn btn-sm btn-outline-danger" style="border-radius:8px;">Cabut</button>
                        </form>
                    </div>
                </div>
                @empty
                <div class="text-center text-muted py-4">
                    <div style="font-size:2.5rem;">🔌</div>
                    <p class="mt-2">Belum ada aplikasi yang terhubung.</p>
                    <small>Buat OAuth Client baru di sebelah kiri untuk mulai integrasi.</small>
                </div>
                @endforelse
            </div>
        </div>
    </div>

    {{-- Panduan Singkat --}}
    <div class="card glass p-4 mt-4" style="border-radius:20px;">
        <h5 class="fw-bold mb-3">📖 Cara Menghubungkan Aplikasi Lain (Custom Connector)</h5>
        <div class="row g-3">
            <div class="col-md-3 text-center p-3" style="border-right: 1px solid rgba(255,255,255,0.1);">
                <div style="font-size:2rem;">1️⃣</div>
                <p class="fw-semibold mt-2 mb-1 small">Buat Client</p>
                <p class="text-muted" style="font-size:0.8rem;">Buat OAuth Client di halaman ini. Salin Client ID & Secret yang muncul.</p>
            </div>
            <div class="col-md-3 text-center p-3" style="border-right: 1px solid rgba(255,255,255,0.1);">
                <div style="font-size:2rem;">2️⃣</div>
                <p class="fw-semibold mt-2 mb-1 small">Isi Custom Connector</p>
                <p class="text-muted" style="font-size:0.8rem;">Masukkan Client ID, Client Secret, dan endpoint ke aplikasi klien (contoh: Make.com, Zapier).</p>
            </div>
            <div class="col-md-3 text-center p-3" style="border-right: 1px solid rgba(255,255,255,0.1);">
                <div style="font-size:2rem;">3️⃣</div>
                <p class="fw-semibold mt-2 mb-1 small">Login via Jostru</p>
                <p class="text-muted" style="font-size:0.8rem;">Pengguna dari aplikasi klien akan diarahkan ke Jostru untuk login & otorisasi.</p>
            </div>
            <div class="col-md-3 text-center p-3">
                <div style="font-size:2rem;">4️⃣</div>
                <p class="fw-semibold mt-2 mb-1 small">Dapat Token & Data</p>
                <p class="text-muted" style="font-size:0.8rem;">Aplikasi klien mendapat access token dan bisa akses data pengguna dari endpoint <code>/api/user/me</code>.</p>
            </div>
        </div>
    </div>
</div>

<script>
function copyText(id) {
    const el = document.getElementById(id);
    navigator.clipboard.writeText(el.value).then(() => {
        alert('Disalin: ' + el.value);
    });
}
</script>
@endsection
