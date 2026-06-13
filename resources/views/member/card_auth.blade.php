@extends('layouts.app')

@section('title', 'Verifikasi Keamanan Kartu - Jostru')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-5">
            <div class="card glass text-center p-4" style="border-radius: 20px; border: 1px solid rgba(255,255,255,0.2);">
                <div class="mb-4">
                    <div style="width: 80px; height: 80px; background: rgba(59, 130, 246, 0.1); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto;">
                        <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="#3b82f6" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect>
                            <path d="M7 11V7a5 5 0 0 1 10 0v4"></path>
                        </svg>
                    </div>
                </div>
                
                <h3 style="font-weight: 800; color: #1e293b;">Verifikasi Akses</h3>
                <p class="text-muted mb-4" style="font-size: 14px;">Kartu identitas Anda dilindungi oleh Verifikasi 2 Langkah. Silakan masukkan kata sandi Anda untuk membukanya.</p>

                @if($errors->any())
                    <div class="alert alert-danger" style="border-radius: 10px; font-size: 14px;">
                        {{ $errors->first() }}
                    </div>
                @endif

                <form action="{{ route('member.card.auth.verify') }}" method="POST">
                    @csrf
                    <div class="mb-4">
                        <input type="password" name="password" class="form-control form-control-lg text-center" placeholder="Masukkan Kata Sandi..." required style="border-radius: 12px; font-size: 16px; font-weight: 600; letter-spacing: 2px;">
                    </div>
                    
                    <button type="submit" class="btn btn-primary btn-lg w-100 mb-3" style="border-radius: 12px; font-weight: 700;">
                        Buka Kunci Kartu
                    </button>
                </form>

                <div class="mt-3 pt-3 border-top">
                    <p class="text-muted mb-1" style="font-size: 13px;">Lupa kata sandi Anda?</p>
                    <a href="https://wa.me/6289654471125" target="_blank" class="btn btn-outline-success btn-sm w-100" style="border-radius: 10px; font-weight: 600;">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="margin-right: 5px;"><path d="M21 11.5a8.38 8.38 0 0 1-.9 3.8 8.5 8.5 0 0 1-7.6 4.7 8.38 8.38 0 0 1-3.8-.9L3 21l1.9-5.7a8.38 8.38 0 0 1-.9-3.8 8.5 8.5 0 0 1 4.7-7.6 8.38 8.38 0 0 1 3.8-.9h.5a8.48 8.48 0 0 1 8 8v.5z"></path></svg>
                        Hubungi Admin (089654471125)
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
