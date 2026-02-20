<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Admin') — ClothStore</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-950 text-gray-100 font-sans">

<div class="flex h-screen overflow-hidden">

    {{-- Sidebar --}}
    <aside class="w-64 flex-shrink-0 bg-gray-900 border-r border-gray-800 flex flex-col">
        <div class="p-6 border-b border-gray-800">
            <div class="flex items-center gap-2">
                <div class="w-8 h-8 bg-indigo-600 rounded-lg flex items-center justify-center text-white font-bold text-sm">C</div>
                <div>
                    <p class="font-bold text-white text-sm">ClothStore</p>
                    <p class="text-xs text-indigo-400">Admin Panel</p>
                </div>
            </div>
        </div>

        <nav class="flex-1 p-4 space-y-1 overflow-y-auto">
            @php
                $navItems = [
                    ['route' => 'admin.dashboard',  'icon' => '📊', 'label' => 'Dashboard'],
                    ['route' => 'admin.orders',      'icon' => '📦', 'label' => 'Orders'],
                    ['route' => 'admin.products',    'icon' => '👔', 'label' => 'Products'],
                    ['route' => 'admin.users',       'icon' => '👤', 'label' => 'Customers'],
                    ['route' => 'admin.vendors',     'icon' => '🏭', 'label' => 'Vendors'],
                ];
            @endphp
            @foreach($navItems as $item)
                <a href="{{ route($item['route']) }}"
                   class="flex items-center gap-3 px-4 py-2.5 rounded-xl text-sm font-medium transition-all
                          {{ request()->routeIs($item['route']) ? 'bg-indigo-600/20 text-indigo-400 border border-indigo-500/30' : 'text-gray-400 hover:text-white hover:bg-gray-800' }}">
                    <span>{{ $item['icon'] }}</span>
                    {{ $item['label'] }}
                </a>
            @endforeach
        </nav>

        <div class="p-4 border-t border-gray-800">
            <div class="flex items-center gap-3 px-4 py-2 rounded-xl bg-gray-800 mb-2">
                <div class="w-7 h-7 bg-indigo-600 rounded-full flex items-center justify-center text-xs font-bold text-white">
                    {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                </div>
                <div class="min-w-0">
                    <p class="text-xs font-medium text-white truncate">{{ auth()->user()->name }}</p>
                    <p class="text-xs text-gray-500 truncate">{{ auth()->user()->email }}</p>
                </div>
            </div>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="flex items-center gap-2 w-full px-4 py-2 text-sm text-red-400 hover:bg-gray-800 rounded-xl transition-colors">
                    🚪 Sign Out
                </button>
            </form>
            <a href="{{ route('home') }}" class="flex items-center gap-2 px-4 py-2 text-sm text-gray-500 hover:text-gray-300 hover:bg-gray-800 rounded-xl transition-colors">
                🌐 View Store
            </a>
        </div>
    </aside>

    {{-- Main Content --}}
    <div class="flex-1 flex flex-col overflow-hidden">
        {{-- Top bar --}}
        <header class="bg-gray-900 border-b border-gray-800 px-6 py-4 flex items-center justify-between flex-shrink-0">
            <h1 class="text-xl font-bold text-white">@yield('page-title', 'Dashboard')</h1>
            @if(session('success'))
                <div class="flex items-center gap-2 bg-emerald-600/20 border border-emerald-500/30 text-emerald-400 text-sm px-4 py-2 rounded-xl">
                    ✓ {{ session('success') }}
                </div>
            @endif
            @if(session('error'))
                <div class="flex items-center gap-2 bg-red-600/20 border border-red-500/30 text-red-400 text-sm px-4 py-2 rounded-xl">
                    ✗ {{ session('error') }}
                </div>
            @endif
        </header>

        {{-- Page Content --}}
        <main class="flex-1 overflow-y-auto p-6">
            @yield('content')
        </main>
    </div>
</div>

@stack('scripts')
</body>
</html>
