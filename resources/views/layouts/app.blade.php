<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'Merkado' }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        * { font-family: 'Plus Jakarta Sans', sans-serif; }
        :root {
            --orange: #F97316;
            --orange-dark: #EA6C10;
            --orange-light: #FFF7ED;
            --orange-border: #FED7AA;
        }
        .btn-orange {
            background: var(--orange);
            color: white;
            transition: background 0.2s, transform 0.1s;
        }
        .btn-orange:hover { background: var(--orange-dark); transform: translateY(-1px); }
        .btn-orange:active { transform: translateY(0); }
        .nav-link {
            color: #6B7280;
            font-size: 0.875rem;
            font-weight: 500;
            padding: 0.5rem 0.75rem;
            border-radius: 0.5rem;
            transition: all 0.15s;
        }
        .nav-link:hover { color: var(--orange); background: var(--orange-light); }
        .card-hover {
            transition: transform 0.2s, box-shadow 0.2s;
        }
        .card-hover:hover {
            transform: translateY(-3px);
            box-shadow: 0 12px 32px rgba(0,0,0,0.1);
        }
    </style>
</head>
<body class="bg-gray-50 min-h-screen">

    {{-- ═══════════════════════════════════════════ --}}
    {{-- NAVBAR --}}
    {{-- ═══════════════════════════════════════════ --}}
    <nav class="bg-white border-b border-gray-200 sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6">
            <div class="flex items-center justify-between h-16 gap-4">

                {{-- Logo --}}
                <a href="/" class="flex items-center gap-2 flex-shrink-0">
                    <div class="w-8 h-8 rounded-lg flex items-center justify-center" style="background:var(--orange)">
                        <svg class="w-4 h-4 text-white" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/>
                        </svg>
                    </div>
                    <span class="font-extrabold text-gray-900 text-lg tracking-tight">Merkado</span>
                </a>

                {{-- Search Bar --}}
                <form action="/search" method="GET" class="flex-1 max-w-xl hidden sm:block">
                    <div class="relative">
                        <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                        <input
                            type="text"
                            name="q"
                            placeholder="Search products, brands, categories..."
                            value="{{ request('q') }}"
                            class="w-full pl-10 pr-4 py-2.5 bg-gray-100 border border-transparent rounded-xl text-sm focus:outline-none focus:bg-white focus:border-orange-300 focus:ring-2 focus:ring-orange-100 transition-all"
                        >
                    </div>
                </form>

                {{-- Nav Actions --}}
                <div class="flex items-center gap-2">
                    @guest
                        <a href="/login" class="nav-link hidden sm:block">Login</a>
                        <a href="/register"
                           class="btn-orange px-4 py-2 rounded-xl text-sm font-semibold">
                            Sign up
                        </a>
                    @endguest

                    @auth
                        {{-- Seller Links --}}
                        @if(auth()->user()->isSeller())
                            <a href="/seller/products/create"
                               class="btn-orange px-4 py-2 rounded-xl text-sm font-semibold flex items-center gap-1.5">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                                </svg>
                                Sell
                            </a>
                            <a href="/seller/dashboard" class="nav-link hidden sm:block">Dashboard</a>
                        @endif

                        {{-- Admin Links --}}
                        @if(auth()->user()->isAdmin())
                            <a href="/admin/dashboard"
                               class="nav-link flex items-center gap-1 text-red-600 hover:bg-red-50">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                                </svg>
                                Admin
                            </a>
                        @endif

                        {{-- Buyer Links --}}
                        @if(auth()->user()->isBuyer())
                            <a href="/orders" class="nav-link hidden sm:flex items-center gap-1">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 7H4l1-7z"/>
                                </svg>
                                Orders
                            </a>
                        @endif

                        {{-- User Menu --}}
                        <div class="flex items-center gap-2 pl-2 border-l border-gray-200">
                            <div class="w-8 h-8 rounded-full flex items-center justify-center text-white text-xs font-bold flex-shrink-0"
                                 style="background: var(--orange)">
                                {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                            </div>
                            <span class="text-sm font-medium text-gray-700 hidden md:block max-w-24 truncate">
                                {{ auth()->user()->name }}
                            </span>
                        </div>

                        {{-- Logout --}}
                        <form method="POST" action="/logout">
                            @csrf
                            <button type="submit"
                                    class="nav-link flex items-center gap-1 text-gray-500 hover:text-red-500 hover:bg-red-50">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                                </svg>
                                <span class="hidden sm:block">Logout</span>
                            </button>
                        </form>
                    @endauth
                </div>

            </div>
        </div>
    </nav>

    {{-- ═══════════════════════════════════════════ --}}
    {{-- FLASH MESSAGES --}}
    {{-- ═══════════════════════════════════════════ --}}
    @if(session('success'))
        <div class="max-w-7xl mx-auto px-4 sm:px-6 mt-4">
            <div class="flex items-center gap-3 bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-xl text-sm">
                <svg class="w-5 h-5 text-green-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                {{ session('success') }}
            </div>
        </div>
    @endif

    @if(session('error'))
        <div class="max-w-7xl mx-auto px-4 sm:px-6 mt-4">
            <div class="flex items-center gap-3 bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-xl text-sm">
                <svg class="w-5 h-5 text-red-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                {{ session('error') }}
            </div>
        </div>
    @endif

    @if($errors->any())
        <div class="max-w-7xl mx-auto px-4 sm:px-6 mt-4">
            <div class="bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-xl text-sm">
                <p class="font-semibold mb-1">Please fix the following:</p>
                <ul class="list-disc list-inside space-y-1">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        </div>
    @endif

    {{-- ═══════════════════════════════════════════ --}}
    {{-- MAIN CONTENT --}}
    {{-- ═══════════════════════════════════════════ --}}
    <main class="max-w-7xl mx-auto px-4 sm:px-6 py-6">
        @yield('content')
    </main>

    {{-- ═══════════════════════════════════════════ --}}
    {{-- FOOTER --}}
    {{-- ═══════════════════════════════════════════ --}}
    <footer class="bg-white border-t border-gray-200 mt-16">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 py-8">
            <div class="flex flex-col sm:flex-row items-center justify-between gap-4">
                <div class="flex items-center gap-2">
                    <div class="w-6 h-6 rounded-md flex items-center justify-center" style="background:var(--orange)">
                        <svg class="w-3 h-3 text-white" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/>
                        </svg>
                    </div>
                    <span class="font-bold text-gray-800">Merkado</span>
                </div>
                <p class="text-gray-400 text-sm">
                    &copy; {{ date('Y') }} Merkado. Buy and sell locally.
                </p>
            </div>
        </div>
    </footer>

</body>
</html>