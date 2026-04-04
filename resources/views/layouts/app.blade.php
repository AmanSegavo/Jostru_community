<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Jostru Community')</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    @stack('styles')
</head>
<body>
    <nav class="navbar">
        <div class="container">
            <a href="/" class="navbar-brand">Jostru</a>
            <div class="nav-links">
                @guest
                    <a href="{{ route('login') }}" class="btn btn-outline">Masuk</a>
                    <a href="{{ route('register') }}" class="btn btn-primary">Daftar</a>
                @else
                    @if(auth()->user()->role === 'admin')
                        <a href="{{ route('admin.dashboard') }}">Admin Panel</a>
                    @else
                        <a href="{{ route('dashboard') }}">Dashboard</a>
                    @endif
                    <form action="{{ route('logout') }}" method="POST" style="display:inline;">
                        @csrf
                        <button type="submit" class="btn btn-outline">Keluar</button>
                    </form>
                @endguest
            </div>
        </div>
    </nav>

    <main>
        @yield('content')
    </main>

    <footer class="container text-center text-muted" style="padding: 2rem 0; margin-top: auto;">
        &copy; {{ date('Y') }} Jostru Community. All rights reserved.
    </footer>

    @stack('scripts')
</body>
</html>
