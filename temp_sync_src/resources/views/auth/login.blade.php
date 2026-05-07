@extends('layouts.app')

@section('title', 'Masuk - Jostru Community')

@section('content')
<!-- Import HTML5-QRCode -->
<script src="https://unpkg.com/html5-qrcode"></script>

<div class="container flex justify-center items-center min-h-screen">
    <div class="card p-4 mx-auto w-full animate-fade-in glass" style="max-width: 450px;">
        <h2 class="text-center mb-4">Mulai Akses</h2>
        
        @if ($errors->any())
            <div style="background-color: rgba(255,0,0,0.1); color: red; padding: 1rem; border-radius: var(--radius-md); margin-bottom: 1rem;">
                <ul style="margin: 0; padding-left: 20px;">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div id="reader-container" style="display: none; margin-bottom: 1.5rem;">
            <div id="reader" style="width: 100%; border-radius: 8px; overflow: hidden; border: 2px dashed var(--primary);"></div>
            <button type="button" onclick="stopScanner()" class="btn btn-outline w-full" style="margin-top: 10px;">Tutup Kamera</button>
        </div>

        <button type="button" id="btn-scan" onclick="startScanner()" class="btn w-full" style="background: var(--surface-color); color: var(--text-color); border: 1px solid var(--border-color); margin-bottom: 1.5rem; display: flex; justify-content: center; align-items: center; gap: 10px;">
            <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"></path><path stroke-linecap="round" stroke-linejoin="round" d="M3 10a2 2 0 012-2h4M21 10a2 2 0 00-2-2h-4m-6 8H5a2 2 0 01-2-2v-4m16.002 6h-4a2 2 0 00-2 2v4"></path></svg>
            Masuk Praktis via Scan Kartu QR
        </button>

        <div style="text-align: center; color: var(--text-secondary); margin-bottom: 1.5rem; display: flex; align-items: center; gap: 10px;">
            <hr style="flex: 1; border: none; border-top: 1px solid var(--border-color);">
            <span style="font-size: 12px; letter-spacing: 1px;">ATAU MANUAL</span>
            <hr style="flex: 1; border: none; border-top: 1px solid var(--border-color);">
        </div>

        <form method="POST" action="{{ route('login') }}" id="login-form">
            @csrf
            <div class="form-group">
                <label class="form-label" for="login_id">Alamat Email atau ID Member</label>
                <input id="login_id" type="text" class="form-control" name="login_id" value="{{ old('login_id') }}" required autofocus placeholder="member@domain.com / JC-1234">
            </div>

            <div class="form-group" id="pwd-group">
                <label class="form-label" for="password">Kata Sandi</label>
                <input id="password" type="password" class="form-control" name="password" required>
            </div>

            <div class="form-group flex justify-between items-center" style="margin-bottom: 2rem;">
                <label style="display: flex; align-items: center; gap: 0.5rem; color: var(--text-secondary); font-size: var(--text-sm);">
                    <input type="checkbox" name="remember"> Ingat saya
                </label>
                <a href="#" class="text-sm">Lupa Sandi?</a>
            </div>

            <button type="submit" id="btn-submit-login" class="btn btn-primary" style="width: 100%; display: block; padding: 0.85rem; font-size: 1rem; font-weight: 700; border-radius: var(--radius-md); display: flex; align-items: center; justify-content: center; gap: 8px;">
                <span id="btn-submit-text">Masuk</span>
                <svg id="btn-submit-spinner" style="display:none; animation: spin 1s linear infinite;" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10" stroke-opacity="0.3"/><path d="M12 2a10 10 0 0 1 10 10" stroke-linecap="round"/></svg>
            </button>
        </form>

        {{-- Divider --}}
        <div style="text-align: center; color: var(--text-secondary); margin: 1.5rem 0; display: flex; align-items: center; gap: 10px;">
            <hr style="flex: 1; border: none; border-top: 1px solid var(--border-color);">
            <span style="font-size: 12px; letter-spacing: 1px; white-space: nowrap;">ATAU MASUK DENGAN</span>
            <hr style="flex: 1; border: none; border-top: 1px solid var(--border-color);">
        </div>

        {{-- Google Login Button --}}
        <a href="{{ route('auth.google') }}" id="btn-google-login"
           style="display: flex; align-items: center; justify-content: center; gap: 12px;
                  width: 100%; padding: 0.75rem 1.25rem;
                  background: #ffffff; color: #3c4043;
                  border: 1.5px solid #dadce0; border-radius: var(--radius-md, 10px);
                  font-size: 0.9rem; font-weight: 600; letter-spacing: 0.01em;
                  text-decoration: none; cursor: pointer;
                  transition: box-shadow 0.2s ease, border-color 0.2s ease, background 0.2s ease;"
           onmouseover="this.style.boxShadow='0 2px 12px rgba(66,133,244,0.25)'; this.style.borderColor='#4285F4'; this.style.background='#f8f9fa';"
           onmouseout="this.style.boxShadow='none'; this.style.borderColor='#dadce0'; this.style.background='#ffffff';">
            {{-- Google Logo SVG --}}
            <svg width="20" height="20" viewBox="0 0 48 48" xmlns="http://www.w3.org/2000/svg">
                <path fill="#EA4335" d="M24 9.5c3.54 0 6.71 1.22 9.21 3.6l6.85-6.85C35.9 2.38 30.47 0 24 0 14.62 0 6.51 5.38 2.56 13.22l7.98 6.19C12.43 13.72 17.74 9.5 24 9.5z"/>
                <path fill="#4285F4" d="M46.98 24.55c0-1.57-.15-3.09-.38-4.55H24v9.02h12.94c-.58 2.96-2.26 5.48-4.78 7.18l7.73 6c4.51-4.18 7.09-10.36 7.09-17.65z"/>
                <path fill="#FBBC05" d="M10.53 28.59c-.48-1.45-.76-2.99-.76-4.59s.27-3.14.76-4.59l-7.98-6.19C.92 16.46 0 20.12 0 24c0 3.88.92 7.54 2.56 10.78l7.97-6.19z"/>
                <path fill="#34A853" d="M24 48c6.48 0 11.93-2.13 15.89-5.81l-7.73-6c-2.15 1.45-4.92 2.3-8.16 2.3-6.26 0-11.57-4.22-13.47-9.91l-7.98 6.19C6.51 42.62 14.62 48 24 48z"/>
                <path fill="none" d="M0 0h48v48H0z"/>
            </svg>
            Masuk dengan Google
        </a>

        <p class="text-center mt-4 text-sm text-muted">Belum punya akun? <a href="{{ route('register') }}">Daftar sekarang</a></p>
    </div>
</div>

<script>
    let html5QrcodeScanner = null;

    function startScanner() {
        document.getElementById('btn-scan').style.display = 'none';
        document.getElementById('reader-container').style.display = 'block';

        html5QrcodeScanner = new Html5QrcodeScanner(
            "reader",
            { fps: 10, qrbox: {width: 250, height: 250} },
            /* verbose= */ false
        );
        html5QrcodeScanner.render(onScanSuccess, onScanFailure);
    }

    function stopScanner() {
        if (html5QrcodeScanner) {
            html5QrcodeScanner.clear();
        }
        document.getElementById('btn-scan').style.display = 'flex';
        document.getElementById('reader-container').style.display = 'none';
    }

    function onScanSuccess(decodedText, decodedResult) {
        // Teks QR biasanya berupa URL: http://domain.com/v/JC-A1B2C3
        // Mari kita ekstrak ID member dari pola "JC-"
        let match = decodedText.match(/(JC-[A-Z0-9\-]+)/i);
        let idResult = match ? match[1] : decodedText;

        // Isikan ke form login
        document.getElementById('login_id').value = idResult;
        
        // Mainkan notifikasi
        stopScanner();

        // Peringatkan bahwa kartu dikenali, tapi butuh password untuk keamanan berlapis
        alert("Kartu Dikenali (" + idResult + ")! Demi Keamanan, silakan masukkan Password Anda lalu klik Masuk.");
        
        // Arahkan kursor ke password untuk kenyamanan
        document.getElementById('password').focus();
    }

    function onScanFailure(error) {
        // Abaikan error continous scan
    }

    // Loading state on form submit
    document.getElementById('login-form').addEventListener('submit', function() {
        const btn = document.getElementById('btn-submit-login');
        const text = document.getElementById('btn-submit-text');
        const spinner = document.getElementById('btn-submit-spinner');
        btn.disabled = true;
        btn.style.opacity = '0.8';
        text.textContent = 'Memproses...';
        spinner.style.display = 'block';
    });
</script>
<style>
    @keyframes spin {
        from { transform: rotate(0deg); }
        to { transform: rotate(360deg); }
    }
</style>
@endsection
