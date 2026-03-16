<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'Marketplace' }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-100 min-h-screen">

    {{-- NAVBAR --}}
    <nav class="bg-white shadow-sm">
        <div class="max-w-7xl mx-auto px-4 py-3 flex justify-between items-center">

            {{-- Logo --}}
            <a href="/" class="text-xl font-bold text-blue-600">
                🛒 Marketplace
            </a>

            {{-- Search Bar --}}
            <form action="/search" method="GET" class="flex-1 mx-8">
                <input
                    type="text"
                    name="q"
                    placeholder="Search products..."
                    value="{{ request('q') }}"
                    class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                >
            </form>

            {{-- Nav Links --}}
            <div class="flex items-center gap-4">
                @guest
                    <a href="/login" class="text-gray-600 hover:text-blue-600">Login</a>
                    <a href="/register" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700">Register</a>
                @endguest

                @auth
                    {{-- Seller Links --}}
                    @if(auth()->user()->isSeller())
                        <a href="/seller/dashboard" class="text-gray-600 hover:text-blue-600">Dashboard</a>
                        <a href="/seller/products/create" class="text-gray-600 hover:text-blue-600">+ Sell</a>
                    @endif

                    {{-- Admin Links --}}
                    @if(auth()->user()->isAdmin())
                        <a href="/admin/dashboard" class="text-gray-600 hover:text-blue-600">Admin</a>
                    @endif

                    {{-- User Info --}}
                    <span class="text-gray-700 font-medium">{{ auth()->user()->name }}</span>

                    {{-- Logout --}}
                    <form method="POST" action="/logout">
                        @csrf
                        <button type="submit" class="text-gray-600 hover:text-red-500">Logout</button>
                    </form>
                @endauth
            </div>

        </div>
    </nav>

    {{-- FLASH MESSAGES --}}
    @if(session('success'))
        <div class="max-w-7xl mx-auto px-4 mt-4">
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded">
                {{ session('success') }}
            </div>
        </div>
    @endif

    @if(session('error'))
        <div class="max-w-7xl mx-auto px-4 mt-4">
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">
                {{ session('error') }}
            </div>
        </div>
    @endif

    {{-- MAIN CONTENT --}}
    <main class="max-w-7xl mx-auto px-4 py-6">
        @yield('content')
    </main>

    {{-- FOOTER --}}
    <footer class="bg-white border-t mt-12 py-6">
        <div class="max-w-7xl mx-auto px-4 text-center text-gray-500 text-sm">
            &copy; {{ date('Y') }} Marketplace. All rights reserved.
        </div>
    </footer>

</body>
</html>