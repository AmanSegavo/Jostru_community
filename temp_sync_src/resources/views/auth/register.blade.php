@extends('layouts.app')

@section('title', 'Daftar - Jostru Community')

@section('content')
<style>
    .step-indicator {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 0;
        margin-bottom: 2rem;
    }
    .step-dot {
        width: 32px;
        height: 32px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 0.85rem;
        font-weight: 700;
        transition: all 0.3s ease;
        border: 2px solid var(--border-color);
        background: var(--surface-color);
        color: var(--text-secondary);
        position: relative;
        z-index: 1;
    }
    .step-dot.active {
        background: #22c55e;
        border-color: #22c55e;
        color: white;
        box-shadow: 0 0 0 4px rgba(34, 197, 94, 0.2);
    }
    .step-dot.done {
        background: #10b981;
        border-color: #10b981;
        color: white;
    }
    .step-line {
        width: 60px;
        height: 2px;
        background: var(--border-color);
        transition: background 0.3s ease;
    }
    .step-line.done {
        background: #22c55e;
    }
    .step-panel {
        display: none;
        animation: fadeSlideIn 0.35s ease-out;
    }
    .step-panel.active {
        display: block;
    }
    @keyframes fadeSlideIn {
        from { opacity: 0; transform: translateX(20px); }
        to { opacity: 1; transform: translateX(0); }
    }
    .step-label {
        font-size: 0.7rem;
        font-weight: 600;
        margin-top: 4px;
        color: var(--text-secondary);
    }
    .step-wrapper {
        display: flex;
        flex-direction: column;
        align-items: center;
    }
    @keyframes spin {
        from { transform: rotate(0deg); }
        to { transform: rotate(360deg); }
    }
</style>

<div class="container flex justify-center items-center min-h-screen">
    <div class="card p-4 mx-auto max-w-md w-full animate-fade-in glass">
        <h2 class="text-center mb-2">Buat Akun Baru</h2>
        <p class="text-center text-muted mb-4" style="font-size: 0.9rem;">Bergabung dengan komunitas peduli lingkungan</p>

        @if ($errors->any())
            <div style="background-color: rgba(255,0,0,0.1); color: red; padding: 1rem; border-radius: var(--radius-md); margin-bottom: 1rem;">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        {{-- Step Indicator --}}
        <div class="step-indicator">
            <div class="step-wrapper">
                <div class="step-dot active" id="dot-1">1</div>
                <div class="step-label">Data Diri</div>
            </div>
            <div class="step-line" id="line-1"></div>
            <div class="step-wrapper">
                <div class="step-dot" id="dot-2">2</div>
                <div class="step-label">Keamanan</div>
            </div>
        </div>

        <form method="POST" action="{{ route('register') }}" id="register-form">
            @csrf

            {{-- STEP 1: Data Diri --}}
            <div class="step-panel active" id="step-1">
                <div class="form-group">
                    <label class="form-label" for="name">Nama Lengkap</label>
                    <input id="name" type="text" class="form-control" name="name" value="{{ old('name') }}"
                        placeholder="Contoh: Budi Santoso" autofocus>
                </div>

                <div class="form-group">
                    <label class="form-label" for="email">Alamat Email</label>
                    <input id="email" type="email" class="form-control" name="email" value="{{ old('email') }}"
                        placeholder="email@domain.com">
                </div>

                <button type="button" id="btn-next" onclick="goToStep2()"
                    class="btn btn-primary"
                    style="width: 100%; display: block; padding: 0.85rem; font-size: 1rem; font-weight: 700; border-radius: var(--radius-md); margin-top: 0.5rem; background: linear-gradient(135deg, #22c55e, #10b981); border: none;">
                    Lanjut →
                </button>

                {{-- Divider --}}
                <div style="text-align: center; color: var(--text-secondary); margin: 1.5rem 0; display: flex; align-items: center; gap: 10px;">
                    <hr style="flex: 1; border: none; border-top: 1px solid var(--border-color);">
                    <span style="font-size: 12px; letter-spacing: 1px; white-space: nowrap;">ATAU DAFTAR DENGAN</span>
                    <hr style="flex: 1; border: none; border-top: 1px solid var(--border-color);">
                </div>

                {{-- Google Register Button --}}
                <a href="{{ route('auth.google') }}" id="btn-google-register"
                   style="display: flex; align-items: center; justify-content: center; gap: 12px;
                          width: 100%; padding: 0.75rem 1.25rem;
                          background: #ffffff; color: #3c4043;
                          border: 1.5px solid #dadce0; border-radius: var(--radius-md, 10px);
                          font-size: 0.9rem; font-weight: 600; letter-spacing: 0.01em;
                          text-decoration: none; cursor: pointer;
                          transition: box-shadow 0.2s ease, border-color 0.2s ease, background 0.2s ease;"
                   onmouseover="this.style.boxShadow='0 2px 12px rgba(66,133,244,0.25)'; this.style.borderColor='#4285F4'; this.style.background='#f8f9fa';"
                   onmouseout="this.style.boxShadow='none'; this.style.borderColor='#dadce0'; this.style.background='#ffffff';">
                    <svg width="20" height="20" viewBox="0 0 48 48" xmlns="http://www.w3.org/2000/svg">
                        <path fill="#EA4335" d="M24 9.5c3.54 0 6.71 1.22 9.21 3.6l6.85-6.85C35.9 2.38 30.47 0 24 0 14.62 0 6.51 5.38 2.56 13.22l7.98 6.19C12.43 13.72 17.74 9.5 24 9.5z"/>
                        <path fill="#4285F4" d="M46.98 24.55c0-1.57-.15-3.09-.38-4.55H24v9.02h12.94c-.58 2.96-2.26 5.48-4.78 7.18l7.73 6c4.51-4.18 7.09-10.36 7.09-17.65z"/>
                        <path fill="#FBBC05" d="M10.53 28.59c-.48-1.45-.76-2.99-.76-4.59s.27-3.14.76-4.59l-7.98-6.19C.92 16.46 0 20.12 0 24c0 3.88.92 7.54 2.56 10.78l7.97-6.19z"/>
                        <path fill="#34A853" d="M24 48c6.48 0 11.93-2.13 15.89-5.81l-7.73-6c-2.15 1.45-4.92 2.3-8.16 2.3-6.26 0-11.57-4.22-13.47-9.91l-7.98 6.19C6.51 42.62 14.62 48 24 48z"/>
                        <path fill="none" d="M0 0h48v48H0z"/>
                    </svg>
                    Daftar dengan Google
                </a>
            </div>

            {{-- STEP 2: Keamanan --}}
            <div class="step-panel" id="step-2">
                <button type="button" onclick="goToStep1()" style="background: none; border: none; color: var(--text-secondary); cursor: pointer; font-size: 0.85rem; margin-bottom: 1rem; padding: 0; display: flex; align-items: center; gap: 4px;">
                    ← Kembali
                </button>

                <div class="form-group">
                    <label class="form-label" for="password">Kata Sandi</label>
                    <input id="password" type="password" class="form-control" name="password"
                        placeholder="Minimal 8 karakter" required>
                    <small style="color: var(--text-secondary); font-size: 0.78rem; margin-top: 4px; display: block;">Gunakan huruf, angka, dan simbol untuk keamanan lebih baik.</small>
                </div>

                <div class="form-group">
                    <label class="form-label" for="password_confirmation">Konfirmasi Kata Sandi</label>
                    <input id="password_confirmation" type="password" class="form-control" name="password_confirmation"
                        placeholder="Ulangi kata sandi Anda" required>
                </div>

                <button type="submit" id="btn-submit-register" class="btn btn-primary"
                    style="width: 100%; display: flex; align-items: center; justify-content: center; gap: 8px; padding: 0.85rem; font-size: 1rem; font-weight: 700; border-radius: var(--radius-md); margin-top: 0.5rem; background: linear-gradient(135deg, #22c55e, #10b981); border: none;">
                    <span id="btn-register-text">✅ Buat Akun</span>
                    <svg id="btn-register-spinner" style="display:none; animation: spin 1s linear infinite;" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10" stroke-opacity="0.3"/><path d="M12 2a10 10 0 0 1 10 10" stroke-linecap="round"/></svg>
                </button>
            </div>
        </form>

        <p class="text-center mt-4 text-sm text-muted">Sudah punya akun? <a href="{{ route('login') }}">Masuk di sini</a></p>
    </div>
</div>

<script>
    function goToStep2() {
        const name = document.getElementById('name').value.trim();
        const email = document.getElementById('email').value.trim();

        if (!name) {
            document.getElementById('name').focus();
            document.getElementById('name').style.borderColor = '#ef4444';
            return;
        }
        if (!email || !email.includes('@')) {
            document.getElementById('email').focus();
            document.getElementById('email').style.borderColor = '#ef4444';
            return;
        }

        // Mark fields as required now
        document.getElementById('name').setAttribute('required', '');
        document.getElementById('email').setAttribute('required', '');

        document.getElementById('step-1').classList.remove('active');
        document.getElementById('step-2').classList.add('active');

        // Update step indicators
        document.getElementById('dot-1').classList.remove('active');
        document.getElementById('dot-1').classList.add('done');
        document.getElementById('dot-1').innerHTML = '✓';
        document.getElementById('line-1').classList.add('done');
        document.getElementById('dot-2').classList.add('active');

        document.getElementById('password').focus();
    }

    function goToStep1() {
        document.getElementById('step-2').classList.remove('active');
        document.getElementById('step-1').classList.add('active');

        // Restore step indicators
        document.getElementById('dot-1').classList.add('active');
        document.getElementById('dot-1').classList.remove('done');
        document.getElementById('dot-1').innerHTML = '1';
        document.getElementById('line-1').classList.remove('done');
        document.getElementById('dot-2').classList.remove('active');
    }

    // Loading state on register submit
    document.getElementById('register-form').addEventListener('submit', function() {
        const btn = document.getElementById('btn-submit-register');
        const text = document.getElementById('btn-register-text');
        const spinner = document.getElementById('btn-register-spinner');
        btn.disabled = true;
        btn.style.opacity = '0.8';
        text.textContent = 'Mendaftar...';
        spinner.style.display = 'block';
    });

    // Reset border on input
    document.getElementById('name').addEventListener('input', function() { this.style.borderColor = ''; });
    document.getElementById('email').addEventListener('input', function() { this.style.borderColor = ''; });

    // If there are validation errors, jump back and show step 1
    @if ($errors->any())
        // Show step 1 by default (already shown), or step 2 if password errors
        @if ($errors->has('password') || $errors->has('password_confirmation'))
            goToStep2();
        @endif
    @endif
</script>
@endsection
