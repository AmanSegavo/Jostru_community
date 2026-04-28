@extends('layouts.app')

@section('title', 'Daftar - Jostru Community')

@section('content')
<div class="container flex justify-center items-center min-h-screen">
    <div class="card p-4 mx-auto max-w-md w-full animate-fade-in glass">
        <h2 class="text-center mb-4">Buat Akun Baru</h2>

        @if ($errors->any())
            <div style="background-color: rgba(255,0,0,0.1); color: red; padding: 1rem; border-radius: var(--radius-md); margin-bottom: 1rem;">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('register') }}">
            @csrf
            <div class="form-group">
                <label class="form-label" for="name">Nama Lengkap</label>
                <input id="name" type="text" class="form-control" name="name" value="{{ old('name') }}" required autofocus>
            </div>

            <div class="form-group">
                <label class="form-label" for="email">Alamat Email</label>
                <input id="email" type="email" class="form-control" name="email" value="{{ old('email') }}" required>
            </div>

            <div class="form-group">
                <label class="form-label" for="password">Kata Sandi</label>
                <input id="password" type="password" class="form-control" name="password" required>
            </div>

            <div class="form-group">
                <label class="form-label" for="password_confirmation">Konfirmasi Kata Sandi</label>
                <input id="password_confirmation" type="password" class="form-control" name="password_confirmation" required>
            </div>

            <button type="submit" class="btn btn-primary w-full" style="margin-top: 1rem;">
                Daftar Akun
            </button>
        </form>

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
            {{-- Google Logo SVG --}}
            <svg width="20" height="20" viewBox="0 0 48 48" xmlns="http://www.w3.org/2000/svg">
                <path fill="#EA4335" d="M24 9.5c3.54 0 6.71 1.22 9.21 3.6l6.85-6.85C35.9 2.38 30.47 0 24 0 14.62 0 6.51 5.38 2.56 13.22l7.98 6.19C12.43 13.72 17.74 9.5 24 9.5z"/>
                <path fill="#4285F4" d="M46.98 24.55c0-1.57-.15-3.09-.38-4.55H24v9.02h12.94c-.58 2.96-2.26 5.48-4.78 7.18l7.73 6c4.51-4.18 7.09-10.36 7.09-17.65z"/>
                <path fill="#FBBC05" d="M10.53 28.59c-.48-1.45-.76-2.99-.76-4.59s.27-3.14.76-4.59l-7.98-6.19C.92 16.46 0 20.12 0 24c0 3.88.92 7.54 2.56 10.78l7.97-6.19z"/>
                <path fill="#34A853" d="M24 48c6.48 0 11.93-2.13 15.89-5.81l-7.73-6c-2.15 1.45-4.92 2.3-8.16 2.3-6.26 0-11.57-4.22-13.47-9.91l-7.98 6.19C6.51 42.62 14.62 48 24 48z"/>
                <path fill="none" d="M0 0h48v48H0z"/>
            </svg>
            Daftar dengan Google
        </a>

        <p class="text-center mt-4 text-sm text-muted">Sudah punya akun? <a href="{{ route('login') }}">Masuk di sini</a></p>
    </div>
</div>
@endsection
