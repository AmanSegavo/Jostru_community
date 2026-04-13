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

            <button type="submit" class="btn btn-primary w-full">
                Masuk
            </button>
        </form>
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
</script>
@endsection
