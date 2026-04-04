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
        <p class="text-center mt-4 text-sm text-muted">Sudah punya akun? <a href="{{ route('login') }}">Masuk di sini</a></p>
    </div>
</div>
@endsection
