@extends('layouts.app')

@section('title', 'Masuk - Jostru Community')

@section('content')
<div class="container flex justify-center items-center min-h-screen">
    <div class="card p-4 mx-auto max-w-md w-full animate-fade-in glass">
        <h2 class="text-center mb-4">Mulai Akses</h2>
        
        @if ($errors->any())
            <div style="background-color: rgba(255,0,0,0.1); color: red; padding: 1rem; border-radius: var(--radius-md); margin-bottom: 1rem;">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('login') }}">
            @csrf
            <div class="form-group">
                <label class="form-label" for="email">Alamat Email</label>
                <input id="email" type="email" class="form-control" name="email" value="{{ old('email') }}" required autofocus>
            </div>

            <div class="form-group">
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
@endsection
