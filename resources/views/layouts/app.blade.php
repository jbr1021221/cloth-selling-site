<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'ClothStore') — Premium Fashion</title>
    <meta name="description" content="@yield('description', 'Discover premium clothing at ClothStore — your destination for modern fashion.')">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('head')
</head>
<body class="min-h-screen bg-gray-950 text-gray-100">

    {{-- Navbar --}}
    <nav class="sticky top-0 z-50 bg-gray-950/80 backdrop-blur-xl border-b border-gray-800/50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-16">
                {{-- Logo --}}
                <a href="{{ route('home') }}" class="flex items-center gap-2">
                    <div class="w-8 h-8 bg-indigo-600 rounded-lg flex items-center justify-center text-white font-bold text-sm">C</div>
                    <span class="text-xl font-bold text-white">ClothStore</span>
                </a>

                {{-- Desktop Nav --}}
                <div class="hidden md:flex items-center gap-8">
                    <a href="{{ route('home') }}" class="navbar-link {{ request()->routeIs('home') ? 'text-indigo-400' : '' }}">Home</a>
                    <a href="{{ route('products.index') }}" class="navbar-link {{ request()->routeIs('products.*') ? 'text-indigo-400' : '' }}">Products</a>
                    @auth
                        @if(auth()->user()->role === 'admin')
                            <a href="{{ route('admin.dashboard') }}" class="navbar-link {{ request()->routeIs('admin.*') ? 'text-indigo-400' : '' }}">Admin</a>
                        @endif
                    @endauth
                </div>

                {{-- Actions --}}
                <div class="flex items-center gap-3">
                    {{-- Cart --}}
                    <a href="{{ route('cart.index') }}" class="relative p-2.5 rounded-xl text-gray-400 hover:text-indigo-400 hover:bg-gray-800 transition-all">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/>
                        </svg>
                        @if(session('cart') && count(session('cart')) > 0)
                            <span class="absolute -top-0.5 -right-0.5 bg-indigo-600 text-white text-xs w-4 h-4 rounded-full flex items-center justify-center font-bold">
                                {{ count(session('cart')) }}
                            </span>
                        @endif
                    </a>

                    @guest
                        <a href="{{ route('login') }}" class="btn-outline text-sm">Login</a>
                        <a href="{{ route('register') }}" class="btn-primary text-sm">Sign Up</a>
                    @else
                        <div class="relative" x-data="{ open: false }">
                            <button @click="open = !open" class="flex items-center gap-2 p-2 rounded-xl text-gray-400 hover:text-indigo-400 hover:bg-gray-800 transition-all">
                                <div class="w-8 h-8 bg-indigo-600 rounded-full flex items-center justify-center text-white text-sm font-bold">
                                    {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                                </div>
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                </svg>
                            </button>
                            <div x-show="open" @click.away="open = false" x-transition
                                class="absolute right-0 mt-2 w-48 bg-gray-900 border border-gray-700 rounded-2xl shadow-2xl py-2 z-50">
                                <div class="px-4 py-2 border-b border-gray-800">
                                    <p class="text-sm font-semibold text-white">{{ auth()->user()->name }}</p>
                                    <p class="text-xs text-gray-500">{{ auth()->user()->email }}</p>
                                </div>
                                <a href="{{ route('orders.index') }}" class="flex items-center gap-2 px-4 py-2 text-sm text-gray-300 hover:text-indigo-400 hover:bg-gray-800 transition-colors">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                                    My Orders
                                </a>
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="flex items-center gap-2 w-full px-4 py-2 text-sm text-red-400 hover:bg-gray-800 transition-colors">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                                        Sign Out
                                    </button>
                                </form>
                            </div>
                        </div>
                    @endguest
                </div>
            </div>
        </div>
    </nav>

    {{-- Flash Messages --}}
    @if(session('success'))
        <div id="flash-success" class="fixed top-20 right-4 z-50 bg-emerald-600 text-white px-5 py-3 rounded-xl shadow-xl flex items-center gap-2 animate-bounce-once">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
            {{ session('success') }}
        </div>
    @endif
    @if(session('error'))
        <div id="flash-error" class="fixed top-20 right-4 z-50 bg-red-600 text-white px-5 py-3 rounded-xl shadow-xl flex items-center gap-2">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            {{ session('error') }}
        </div>
    @endif

    {{-- Main Content --}}
    <main>
        @yield('content')
    </main>

    {{-- Footer --}}
    <footer class="bg-gray-900 border-t border-gray-800 mt-20">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
                <div class="col-span-1 md:col-span-2">
                    <div class="flex items-center gap-2 mb-4">
                        <div class="w-8 h-8 bg-indigo-600 rounded-lg flex items-center justify-center text-white font-bold text-sm">C</div>
                        <span class="text-xl font-bold text-white">ClothStore</span>
                    </div>
                    <p class="text-gray-500 text-sm leading-relaxed max-w-xs">Premium fashion for every occasion. Quality clothing delivered to your doorstep.</p>
                </div>
                <div>
                    <h4 class="font-semibold text-white mb-4 text-sm">Shop</h4>
                    <ul class="space-y-2">
                        @foreach(['Shirt','T-Shirt','Jeans','Saree','Salwar','Kurti'] as $cat)
                            <li><a href="{{ route('products.index', ['category' => $cat]) }}" class="text-gray-500 hover:text-indigo-400 text-sm transition-colors">{{ $cat }}</a></li>
                        @endforeach
                    </ul>
                </div>
                <div>
                    <h4 class="font-semibold text-white mb-4 text-sm">Account</h4>
                    <ul class="space-y-2">
                        @guest
                            <li><a href="{{ route('login') }}" class="text-gray-500 hover:text-indigo-400 text-sm transition-colors">Login</a></li>
                            <li><a href="{{ route('register') }}" class="text-gray-500 hover:text-indigo-400 text-sm transition-colors">Register</a></li>
                        @else
                            <li><a href="{{ route('orders.index') }}" class="text-gray-500 hover:text-indigo-400 text-sm transition-colors">My Orders</a></li>
                            <li><a href="{{ route('cart.index') }}" class="text-gray-500 hover:text-indigo-400 text-sm transition-colors">Cart</a></li>
                        @endguest
                    </ul>
                </div>
            </div>
            <div class="mt-8 pt-8 border-t border-gray-800 flex flex-col md:flex-row justify-between items-center">
                <p class="text-gray-600 text-sm">© {{ date('Y') }} ClothStore. All rights reserved.</p>
                <p class="text-gray-600 text-sm mt-2 md:mt-0">Made with ❤️ in Bangladesh</p>
            </div>
        </div>
    </footer>

    @stack('scripts')
    <script>
        // Auto-dismiss flash messages
        setTimeout(() => {
            ['flash-success','flash-error'].forEach(id => {
                const el = document.getElementById(id);
                if (el) el.style.display = 'none';
            });
        }, 4000);
    </script>
</body>
</html>
