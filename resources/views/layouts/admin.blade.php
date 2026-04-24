@extends('layouts.app')

@section('title', 'Admin Panel - Jostru')

@push('styles')
    <style>
        .admin-layout {
            display: flex;
            min-height: calc(100vh - 120px);
            max-width: 1400px;
            margin: 0 auto;
            gap: 2rem;
            padding: 0 1.5rem;
        }

        .admin-sidebar {
            width: 280px;
            flex-shrink: 0;
            padding: 2rem 1.5rem;
            height: max-content;
            border-radius: var(--radius-lg);
            position: sticky;
            top: 100px;
        }

        .admin-content {
            flex: 1;
            padding-bottom: 3rem;
        }

        .nav-item {
            display: flex;
            align-items: center;
            padding: 0.85rem 1.25rem;
            border-radius: var(--radius-full);
            margin-bottom: 0.5rem;
            color: var(--text-secondary);
            font-weight: 600;
            transition: var(--transition-fast);
            position: relative;
            overflow: hidden;
            border: 1px solid transparent;
        }

        .nav-item:hover,
        .nav-item.active {
            background-color: rgba(var(--primary-h), var(--primary-s), var(--primary-l), 0.1);
            color: var(--primary);
            border-color: rgba(var(--primary-h), var(--primary-s), var(--primary-l), 0.2);
            transform: translateX(4px);
            box-shadow: 0 4px 12px rgba(var(--primary-h), var(--primary-s), var(--primary-l), 0.05);
        }

        .nav-item.active::before {
            content: '';
            position: absolute;
            left: 0;
            top: 0;
            height: 100%;
            width: 4px;
            background: linear-gradient(to bottom, var(--primary), var(--secondary));
            border-top-right-radius: 4px;
            border-bottom-right-radius: 4px;
        }

        h3.menu-title {
            font-size: 0.75rem;
            letter-spacing: 0.1em;
            color: var(--text-secondary);
            font-weight: 800;
            margin-bottom: 1.5rem;
            padding-left: 1rem;
            opacity: 0.7;
        }

        @media (max-width: 768px) {
            .admin-layout {
                flex-direction: column;
                padding: 0 1rem;
            }

            .admin-sidebar {
                width: 100%;
                position: relative;
                top: 0;
                padding: 1rem;
            }
        }
    </style>
@endpush

@section('content')
    <div class="admin-layout">
        <aside class="admin-sidebar glass">
            <h3 class="mb-4 text-sm text-muted uppercase">Administrator</h3>
            <nav>
                <a href="{{ route('admin.dashboard') }}"
                    class="nav-item {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">Dashboard</a>
                <a href="{{ route('admin.members') }}"
                    class="nav-item {{ request()->routeIs('admin.members') ? 'active' : '' }}">Manajemen Anggota</a>
                <a href="{{ route('admin.cards') }}"
                    class="nav-item {{ request()->routeIs('admin.cards') ? 'active' : '' }}">Kartu Digital</a>
                <a href="{{ route('admin.finances') }}"
                    class="nav-item {{ request()->routeIs('admin.finances') ? 'active' : '' }}">Laporan Keuangan</a>
                <a href="{{ route('admin.messages') }}"
                    class="nav-item {{ request()->routeIs('admin.messages') ? 'active' : '' }}">Pesan Masuk</a>
                <a href="{{ route('admin.logs') }}"
                    class="nav-item {{ request()->routeIs('admin.logs') ? 'active' : '' }}">Log Aktivitas</a>
            </nav>
        </aside>
        <section class="admin-content">
            @yield('admin_content')
        </section>
    </div>
@endsection