<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Admin Dashboard') — ClothStore</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        .btn-admin-primary { background-color: #C9A84C; color: white; padding: 0.75rem 1.5rem; font-size: 0.6875rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.1em; transition: background-color 0.2s; }
        .btn-admin-primary:hover { background-color: #b08a38; }
        .btn-admin-danger { background-color: #ef4444; color: white; padding: 0.75rem 1.5rem; font-size: 0.6875rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.1em; transition: background-color 0.2s; }
        .btn-admin-danger:hover { background-color: #dc2626; }
        .btn-admin-secondary { background-color: white; color: #1A1A1A; border: 1px solid #e5e7eb; padding: 0.75rem 1.5rem; font-size: 0.6875rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.1em; transition: all 0.2s; }
        .btn-admin-secondary:hover { border-color: #C9A84C; color: #C9A84C; }
        /* Prevent Alpine.js flash before JS loads */
        [x-cloak] { display: none !important; }
    </style>
</head>
<body class="bg-[#F8F8F8] text-[#1A1A1A] font-sans antialiased" x-data="{ sidebarOpen: false }">

<div class="flex h-screen overflow-hidden">

    {{-- Mobile Sidebar Overlay --}}
    <div x-show="sidebarOpen"
         x-cloak
         @click="sidebarOpen = false"
         class="fixed inset-0 bg-black/50 z-40 lg:hidden"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0">
    </div>

    {{-- Sidebar --}}
    {{-- On mobile: hidden off-screen by default (-translate-x-full via Alpine).
         On desktop (lg+): always visible (lg:translate-x-0), part of the flex row. --}}
    <aside :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full lg:translate-x-0'"
           class="fixed inset-y-0 left-0 z-50 w-72 bg-white border-r border-gray-200 flex flex-col
                  transition-transform duration-300 ease-in-out
                  lg:relative lg:z-auto lg:w-64 lg:flex-shrink-0 lg:translate-x-0 -translate-x-full"
           style="">
        
        {{-- Logo Area with mobile close button --}}
        <div class="h-20 flex items-center px-6 border-b border-gray-100 flex-shrink-0">
            <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-3 flex-1">
                <div class="w-8 h-8 flex items-center justify-center border border-[#C9A84C] text-[#C9A84C] font-bold text-lg">C</div>
                <div>
                    <h2 class="font-bold text-[#1A1A1A] tracking-wider uppercase text-sm">{{ \App\Models\Setting::get('store_name', 'ClothStore') }}</h2>
                    <p class="text-[10px] text-gray-400 tracking-widest uppercase">Admin</p>
                </div>
            </a>
            {{-- Close button — mobile only --}}
            <button @click="sidebarOpen = false"
                    class="lg:hidden ml-auto p-2 text-gray-400 hover:text-[#1A1A1A] transition-colors"
                    aria-label="Close sidebar">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>

        {{-- Navigation --}}
        <nav class="flex-1 py-6 overflow-y-auto space-y-1">
            @php
                $navItems = [
                    ['route' => 'admin.dashboard',     'icon' => '📊', 'label' => 'Dashboard'],
                    ['route' => 'admin.analytics',     'icon' => '📈', 'label' => 'Sales Analytics'],
                    ['route' => 'admin.orders',        'icon' => '📦', 'label' => 'Orders'],
                    ['route' => 'admin.products',      'icon' => '👔', 'label' => 'Products'],
                    ['route' => 'admin.reports.inventory.index', 'icon' => '📋', 'label' => 'Inventory Report'],
                    ['route' => 'admin.users',         'icon' => '👤', 'label' => 'Customers'],
                    ['route' => 'admin.vendors',       'icon' => '🏭', 'label' => 'Vendors'],
                    ['route' => 'admin.delivery-zones.index', 'icon' => '🚚', 'label' => 'Delivery Zones'],
                    ['route' => 'admin.flash-sales.index', 'icon' => '⚡', 'label' => 'Flash Sales'],
                    ['route' => 'admin.coupons.index', 'icon' => '🎟️', 'label' => 'Coupons'],
                    ['route' => 'admin.loyalty.index', 'icon' => '🎫', 'label' => 'Loyalty Points'],
                    ['route' => '#',                   'icon' => '📱', 'label' => 'SMS Marketing'],
                    ['route' => 'admin.settings.index','icon' => '⚙️', 'label' => 'Settings'],
                ];
            @endphp
            
            @foreach($navItems as $item)
                @php 
                    $isActive = $item['route'] !== '#' && request()->routeIs(str_replace('.index', '.*', $item['route']) . '*');
                    // simple active check
                    if($item['route'] == 'admin.dashboard') $isActive = request()->routeIs('admin.dashboard');
                @endphp
                <a href="{{ $item['route'] !== '#' ? route($item['route']) : '#' }}"
                   @click="sidebarOpen = false"
                   class="flex items-center gap-3 px-8 py-3 text-sm transition-all border-l-[3px]
                          {{ $isActive ? 'border-[#C9A84C] text-[#C9A84C] bg-[#F8F8F8] font-bold' : 'border-transparent text-gray-500 hover:text-[#1A1A1A] hover:bg-gray-50 font-medium' }}">
                    <span class="text-lg {{ $isActive ? 'opacity-100' : 'opacity-70 grayscale' }}">{{ $item['icon'] }}</span>
                    <span class="uppercase tracking-widest text-[11px]">{{ $item['label'] }}</span>
                </a>
            @endforeach
        </nav>

        {{-- Bottom Action --}}
        <div class="p-6 border-t border-gray-100 flex-shrink-0">
            <a href="{{ route('home') }}" target="_blank" class="flex items-center gap-2 px-4 py-2 text-[10px] uppercase tracking-widest font-bold text-gray-500 hover:text-[#1A1A1A] transition-colors mb-4">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                View Store
            </a>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="w-full flex items-center justify-center gap-2 border border-gray-200 text-[#1A1A1A] hover:border-[#1A1A1A] bg-white text-[10px] uppercase tracking-widest font-bold py-3 transition-colors">
                    Log Out
                </button>
            </form>
        </div>
    </aside>

    {{-- Main Content — always full width on mobile, flex-1 on desktop --}}
    <div class="flex-1 flex flex-col overflow-hidden min-w-0">
        
        {{-- Top Bar --}}
        <header class="h-20 bg-white border-b border-gray-200 px-4 sm:px-8 flex items-center justify-between flex-shrink-0">
            {{-- Mobile Toggle & Title --}}
            <div class="flex items-center gap-4">
                <button @click="sidebarOpen = true" class="lg:hidden text-gray-500 hover:text-[#1A1A1A]">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
                </button>
                <h1 class="text-lg font-bold text-[#1A1A1A] uppercase tracking-widest">
                    @yield('page-title', 'Dashboard')
                </h1>
            </div>

            {{-- Right Actions --}}
            <div class="flex items-center gap-6">
                
                {{-- Notification Bell --}}
                @php
                    $pendingOrdersCount = \App\Models\Order::where('status', 'pending')->count();
                    $recentPendingOrders = \App\Models\Order::where('status', 'pending')->latest()->take(5)->get();
                @endphp
                <div class="relative" x-data="{ open: false }">
                    <button @click="open = !open" class="relative text-gray-400 hover:text-[#C9A84C] transition-colors focus:outline-none pt-1">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                        </svg>
                        @if($pendingOrdersCount > 0)
                            <span class="absolute top-0 -right-1 flex h-4 w-4 items-center justify-center rounded-full bg-[#1A1A1A] text-[9px] font-bold text-white border border-white">
                                {{ $pendingOrdersCount }}
                            </span>
                        @endif
                    </button>

                    <div x-show="open" 
                         x-cloak 
                         @click.away="open = false"
                         x-transition:enter="transition ease-out duration-200"
                         x-transition:enter-start="opacity-0 translate-y-1"
                         x-transition:enter-end="opacity-100 translate-y-0"
                         x-transition:leave="transition ease-in duration-150"
                         x-transition:leave-start="opacity-100 translate-y-0"
                         x-transition:leave-end="opacity-0 translate-y-1"
                         class="absolute right-0 mt-3 w-80 bg-white border border-gray-100 shadow-xl z-50">
                        
                        <div class="p-4 border-b border-gray-50 flex items-center justify-between">
                            <h3 class="text-[10px] font-bold uppercase tracking-widest text-[#1A1A1A]">Pending Orders</h3>
                            <span class="bg-[#C9A84C] text-white text-[9px] font-bold px-2 py-0.5">{{ $pendingOrdersCount }}</span>
                        </div>

                        <div class="max-h-96 overflow-y-auto">
                            @forelse($recentPendingOrders as $pOrder)
                                <a href="{{ route('admin.orders.show', $pOrder->id) }}" class="flex items-center gap-4 p-4 hover:bg-gray-50 transition-colors border-b border-gray-50 last:border-0">
                                    <div class="w-10 h-10 bg-[#F8F8F8] flex items-center justify-center text-[#C9A84C] font-bold text-xs uppercase shrink-0">
                                        {{ substr($pOrder->delivery_address['name'] ?? 'C', 0, 1) }}
                                    </div>
                                    <div class="flex-1 min-w-0 text-left">
                                        <p class="text-[11px] font-bold text-[#1A1A1A] uppercase tracking-wider truncate">#{{ $pOrder->order_number }}</p>
                                        <p class="text-[10px] text-gray-400 mt-0.5 truncate">{{ $pOrder->delivery_address['name'] ?? 'N/A' }}</p>
                                    </div>
                                    <div class="text-[9px] text-gray-400 uppercase font-medium shrink-0">
                                        {{ $pOrder->created_at->diffForHumans(null, true) }}
                                    </div>
                                </a>
                            @empty
                                <div class="p-8 text-center text-gray-400">
                                    <div class="text-2xl mb-2">📦</div>
                                    <p class="text-[10px] uppercase tracking-widest">No pending orders</p>
                                </div>
                            @endforelse
                        </div>

                        <a href="{{ route('admin.orders') }}" @click="open = false" class="block p-4 text-center text-[10px] font-bold uppercase tracking-widest text-[#C9A84C] border-t border-gray-50 hover:bg-gray-50 transition-colors">
                            View All Orders &rarr;
                        </a>
                    </div>
                </div>

                {{-- User Info --}}
                <div class="hidden sm:flex items-center gap-3 pl-6 border-l border-gray-200">
                    <div class="text-right">
                        <p class="text-xs font-bold text-[#1A1A1A] uppercase tracking-widest">{{ auth()->user()->name }}</p>
                        <p class="text-[10px] text-gray-400 uppercase tracking-widest">Administrator</p>
                    </div>
                    <div class="w-10 h-10 border border-gray-200 bg-[#F8F8F8] flex items-center justify-center text-[#1A1A1A] font-bold text-sm">
                        {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                    </div>
                </div>
            </div>
        </header>

        {{-- Alerts --}}
        @if(session('success') || session('error'))
            <div class="px-8 pt-6 pb-0 flex-shrink-0">
                @if(session('success'))
                    <div class="bg-green-50 border border-green-200 text-green-700 text-[11px] font-bold uppercase tracking-widest px-4 py-3 flex items-center gap-2">
                        <svg class="w-4 h-4 text-green-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        {{ session('success') }}
                    </div>
                @endif
                @if(session('error'))
                    <div class="bg-red-50 border border-red-200 text-red-700 text-[11px] font-bold uppercase tracking-widest px-4 py-3 flex items-center gap-2">
                        <svg class="w-4 h-4 text-red-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                        {{ session('error') }}
                    </div>
                @endif
            </div>
        @endif

        {{-- Page Content --}}
        <main class="flex-1 overflow-y-auto p-4 sm:p-8">
            @yield('content')
        </main>
        
    </div>
</div>

@stack('scripts')
</body>
</html>
