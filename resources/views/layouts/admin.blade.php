@extends('layouts.app')

@section('title', 'Admin Panel - Jostru')

@push('styles')
<style>
    .admin-layout {
        display: flex;
        min-height: calc(100vh - 70px);
    }
    .admin-sidebar {
        width: 250px;
        background-color: var(--surface-color);
        border-right: 1px solid var(--border-color);
        padding: 1.5rem;
    }
    .admin-content {
        flex: 1;
        padding: 2rem;
    }
    .nav-item {
        display: block;
        padding: 0.75rem 1rem;
        border-radius: var(--radius-md);
        margin-bottom: 0.5rem;
        color: var(--text-secondary);
        font-weight: 500;
    }
    .nav-item:hover, .nav-item.active {
        background-color: rgba(var(--primary-h), var(--primary-s), var(--primary-l), 0.1);
        color: var(--primary);
    }
</style>
@endpush

@section('content')
<div class="admin-layout">
    <aside class="admin-sidebar glass">
        <h3 class="mb-4 text-sm text-muted uppercase">Administrator</h3>
        <nav>
            <a href="{{ route('admin.dashboard') }}" class="nav-item active">Dashboard</a>
            <a href="#" class="nav-item">Manajemen Anggota</a>
            <a href="#" class="nav-item">Kartu Digital</a>
            <a href="#" class="nav-item">Pesan Masuk</a>
            <a href="#" class="nav-item">Log Aktivitas</a>
        </nav>
    </aside>
    <section class="admin-content">
        @yield('admin_content')
    </section>
</div>
@endsection
