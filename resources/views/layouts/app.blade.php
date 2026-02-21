<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    @hasSection('og_meta')
        @yield('og_meta')
    @else
        <meta property="og:title" content="{{ \App\Models\Setting::get('seo_meta_title', 'ClothStore') }}">
        <meta property="og:description" content="{{ \App\Models\Setting::get('seo_meta_description', 'Discover premium clothing at ClothStore.') }}">
        <meta property="og:type" content="website">
        <meta property="og:url" content="{{ url()->current() }}">
        @if(\App\Models\Setting::get('seo_og_image'))
        <meta property="og:image" content="{{ asset(\App\Models\Setting::get('seo_og_image')) }}">
        @endif
    @endif
    
    <title>@yield('title', \App\Models\Setting::get('seo_meta_title', 'ClothStore'))</title>
    <meta name="description" content="@yield('description', \App\Models\Setting::get('seo_meta_description', 'Discover premium clothing.'))">
    <meta name="keywords" content="{{ \App\Models\Setting::get('seo_meta_keywords', 'fashion, clothing') }}">
    
    @if(\App\Models\Setting::get('store_favicon'))
        <link rel="icon" href="{{ asset(\App\Models\Setting::get('store_favicon')) }}">
    @endif

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&family=Playfair+Display:wght@400;600;700&display=swap" rel="stylesheet">
    
    @if(\App\Models\Setting::get('google_analytics_id'))
    <!-- Global site tag (gtag.js) - Google Analytics -->
    <script async src="https://www.googletagmanager.com/gtag/js?id={{ \App\Models\Setting::get('google_analytics_id') }}"></script>
    <script>
      window.dataLayer = window.dataLayer || [];
      function gtag(){dataLayer.push(arguments);}
      gtag('js', new Date());
      gtag('config', '{{ \App\Models\Setting::get('google_analytics_id') }}');
    </script>
    @endif
    
    @if(\App\Models\Setting::get('facebook_pixel_id'))
    <!-- Facebook Pixel Code -->
    <script>
      !function(f,b,e,v,n,t,s)
      {if(f.fbq)return;n=f.fbq=function(){n.callMethod?
      n.callMethod.apply(n,arguments):n.queue.push(arguments)};
      if(!f._fbq)f._fbq=n;n.push=n;n.loaded=!0;n.version='2.0';
      n.queue=[];t=b.createElement(e);t.async=!0;
      t.src=v;s=b.getElementsByTagName(e)[0];
      s.parentNode.insertBefore(t,s)}(window, document,'script',
      'https://connect.facebook.net/en_US/fbevents.js');
      fbq('init', '{{ \App\Models\Setting::get('facebook_pixel_id') }}');
      fbq('track', 'PageView');
    </script>
    <noscript><img height="1" width="1" style="display:none" src="https://www.facebook.com/tr?id={{ \App\Models\Setting::get('facebook_pixel_id') }}&ev=PageView&noscript=1"/></noscript>
    @endif
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('head')
    <style>
        .playfair { font-family: 'Playfair Display', Georgia, serif; }
        /* Animate announcement bar scroll */
        @keyframes marquee { 0%{transform:translateX(0)} 100%{transform:translateX(-50%)} }
        .marquee-inner { display:inline-flex; animation: marquee 20s linear infinite; white-space:nowrap; }
        /* Hide x-cloak elements until Alpine initialises */
        [x-cloak] { display: none !important; }
    </style>
</head>
<body class="min-h-screen bg-white text-[#1A1A1A] pb-16 md:pb-0">

{{-- ═══════════════════════════════════════════════════════════════════════ --}}
{{-- 1. ANNOUNCEMENT BAR                                                     --}}
{{-- ═══════════════════════════════════════════════════════════════════════ --}}
<div class="bg-[#C9A84C] text-white text-xs font-semibold tracking-widest uppercase overflow-hidden py-2.5">
    <div class="marquee-inner">
        @php
            $announcementsRaw = \App\Models\Setting::get('announcement_text', 'Free Delivery On Orders Above ৳999 | Cash On Delivery Available | 7-Day Easy Returns | 100% Authentic Products');
            $announcements = collect(explode('|', $announcementsRaw))->map(fn($s) => trim($s))->filter();
        @endphp
        @if($announcements->count() > 0)
            @for($i = 0; $i < 4; $i++) {{-- Duplicate loop to ensure marquee scrolls seamlessly --}}
                @foreach($announcements as $announcement)
                    <span class="px-12">✦ {{ $announcement }}</span>
                @endforeach
            @endfor
        @endif
    </div>
</div>

{{-- ═══════════════════════════════════════════════════════════════════════ --}}
{{-- 2. NAVBAR                                                                --}}
{{-- ═══════════════════════════════════════════════════════════════════════ --}}
<nav class="sticky top-0 z-50 bg-white transition-all duration-300"
     :class="scrolled ? 'border-b border-[#C9A84C]/40 shadow-sm' : 'border-b border-gray-100'"
     x-data="{
         mobileOpen: false,
         searchOpen: false,
         scrolled: false,
         init() { window.addEventListener('scroll', () => this.scrolled = window.scrollY > 20); }
     }">

    {{-- Desktop navbar row --}}
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-3 items-center h-16 sm:h-20">

            {{-- Left: Logo --}}
            <div class="flex items-center">
                <a href="{{ route('home') }}" class="flex flex-col leading-none">
                    @if(\App\Models\Setting::get('store_logo'))
                        <img src="{{ asset(\App\Models\Setting::get('store_logo')) }}" alt="Logo" class="h-8 object-contain">
                    @else
                        @php
                            $sp = explode(' ', \App\Models\Setting::get('store_name', 'ClothStore'), 2);
                        @endphp
                        <span class="playfair text-xl sm:text-2xl font-bold text-[#1A1A1A] tracking-widest">{{ strtoupper($sp[0]) }}</span>
                        <span class="text-[9px] font-semibold tracking-[0.4em] text-[#C9A84C] uppercase">{{ $sp[1] ?? 'Store' }}</span>
                    @endif
                </a>
            </div>

            {{-- Center: Nav links (desktop) --}}
            <div class="hidden md:flex items-center justify-center gap-8">
                <a href="{{ route('home') }}" class="navbar-link {{ request()->routeIs('home') ? 'navbar-link-active' : '' }}">Home</a>
                <a href="{{ route('products.index') }}" class="navbar-link {{ request()->routeIs('products.*') ? 'navbar-link-active' : '' }}">Shop</a>
                <div class="relative group">
                    <button class="navbar-link flex items-center gap-1">
                        Categories
                        <svg class="w-3 h-3 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                    </button>
                    <div class="absolute top-full left-1/2 -translate-x-1/2 mt-3 w-48 bg-white border border-gray-100 shadow-lg py-2 opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-200">
                        @foreach(['Shirt','T-Shirt','Pant','Jeans','Saree','Salwar','Kurti'] as $cat)
                            <a href="{{ route('products.index', ['category' => $cat]) }}"
                               class="block px-5 py-2.5 text-xs tracking-widest uppercase font-medium text-gray-600 hover:text-[#C9A84C] hover:bg-gray-50 transition-colors">{{ $cat }}</a>
                        @endforeach
                    </div>
                </div>
                @auth
                    @if(auth()->user()->role === 'admin')
                        <a href="{{ route('admin.dashboard') }}" class="navbar-link {{ request()->routeIs('admin.*') ? 'navbar-link-active' : '' }}">Admin</a>
                    @endif
                @endauth
            </div>

            {{-- Right: Icons --}}
            <div class="flex items-center justify-end gap-1 sm:gap-2">

                {{-- Search toggle --}}
                <button @click="searchOpen = !searchOpen"
                        class="p-2.5 text-[#1A1A1A] hover:text-[#C9A84C] transition-colors" aria-label="Search">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0"/></svg>
                </button>

                {{-- Wishlist (desktop) --}}
                @auth
                <a href="{{ route('wishlist.index') }}" class="hidden sm:flex p-2.5 text-[#1A1A1A] hover:text-[#C9A84C] transition-colors relative" aria-label="Wishlist">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/></svg>
                </a>
                @endauth

                {{-- Cart --}}
                <a href="{{ route('cart.index') }}" class="hidden md:flex relative p-2.5 text-[#1A1A1A] hover:text-[#C9A84C] transition-colors" aria-label="Cart">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                    @if(session('cart') && count(session('cart')) > 0)
                        <span class="absolute top-1.5 right-1.5 bg-[#C9A84C] text-white text-xs w-4 h-4 rounded-full flex items-center justify-center font-bold text-[10px]">{{ count(session('cart')) }}</span>
                    @endif
                </a>

                {{-- Account --}}
                @guest
                    <a href="{{ route('login') }}" class="hidden sm:flex items-center gap-1.5 text-xs font-semibold tracking-widest uppercase text-[#1A1A1A] hover:text-[#C9A84C] transition-colors px-3 py-2.5">Login</a>
                    <a href="{{ route('register') }}" class="hidden sm:flex btn-primary text-[10px] px-4 py-2.5">Join Us</a>
                @else
                    <div class="relative" x-data="{ open: false }">
                        <button @click="open = !open"
                                class="flex items-center gap-2 p-2 text-[#1A1A1A] hover:text-[#C9A84C] transition-colors"
                                aria-label="Account">
                            <div class="w-8 h-8 bg-[#C9A84C] flex items-center justify-center text-white text-xs font-bold uppercase">
                                {{ substr(auth()->user()->name, 0, 1) }}
                            </div>
                            <svg class="w-3 h-3 hidden sm:block transition-transform duration-200" :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                        </button>
                        <div x-show="open" @click.away="open = false"
                             x-transition:enter="transition ease-out duration-150"
                             x-transition:enter-start="opacity-0 translate-y-1"
                             x-transition:enter-end="opacity-100 translate-y-0"
                             class="absolute right-0 mt-2 w-56 bg-white border border-gray-100 shadow-xl py-2 z-50">
                            <div class="px-5 py-3 border-b border-gray-100">
                                <p class="text-sm font-semibold text-[#1A1A1A] truncate">{{ auth()->user()->name }}</p>
                                <p class="text-xs text-gray-400 truncate mb-2">{{ auth()->user()->email }}</p>
                                @php
                                    $tierColors = [
                                        'silver'  => 'bg-gray-200 text-gray-600',
                                        'gold'    => 'bg-[#C9A84C] text-white',
                                        'diamond' => 'bg-[#1A1A1A] text-[#C9A84C]'
                                    ];
                                @endphp
                                <span class="inline-block px-2 py-0.5 text-[9px] font-bold uppercase tracking-widest rounded-full {{ $tierColors[auth()->user()->tier] ?? 'bg-gray-200' }}">
                                    @if(auth()->user()->tier === 'diamond') 💎 @elseif(auth()->user()->tier === 'gold') 🥇 @else 🥉 @endif
                                    {{ auth()->user()->tier }}
                                </span>
                            </div>
                            <a href="{{ route('orders.index') }}" class="flex items-center gap-3 px-5 py-3 text-xs tracking-wider uppercase font-medium text-gray-600 hover:text-[#C9A84C] hover:bg-gray-50 transition-colors">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                                My Orders
                            </a>
                            <a href="{{ route('wishlist.index') }}" class="flex items-center gap-3 px-5 py-3 text-xs tracking-wider uppercase font-medium text-gray-600 hover:text-[#C9A84C] hover:bg-gray-50 transition-colors">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/></svg>
                                Wishlist
                            </a>
                            <a href="{{ route('loyalty.index') }}" class="flex items-center gap-3 px-5 py-3 text-xs tracking-wider uppercase font-medium text-[#C9A84C] hover:text-[#b08a38] hover:bg-gray-50 transition-colors bg-[#FFFBF0]">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z" /></svg>
                                {{ number_format(auth()->user()->total_points) }} PTS
                            </a>
                            @if(auth()->user()->role === 'admin')
                            <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-3 px-5 py-3 text-xs tracking-wider uppercase font-medium text-gray-600 hover:text-[#C9A84C] hover:bg-gray-50 transition-colors">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                Admin Panel
                            </a>
                            @endif
                            <div class="border-t border-gray-100 mt-1 pt-1">
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="flex items-center gap-3 w-full px-5 py-3 text-xs tracking-wider uppercase font-medium text-red-500 hover:bg-red-50 transition-colors">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                                        Sign Out
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                @endguest

                {{-- Mobile hamburger --}}
                <button @click="mobileOpen = !mobileOpen"
                        class="md:hidden p-2.5 text-[#1A1A1A] hover:text-[#C9A84C] transition-colors ml-1"
                        aria-label="Menu">
                    {{-- Hamburger: shown when closed; x-cloak prevents flash of both icons --}}
                    <svg x-show="!mobileOpen" x-cloak class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 6h16M4 12h16M4 18h16"/></svg>
                    {{-- Close ×: shown when open --}}
                    <svg x-show="mobileOpen" x-cloak class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
        </div>
    </div>

    {{-- Search bar (slides down) --}}
    <div x-show="searchOpen"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0 -translate-y-2"
         x-transition:enter-end="opacity-100 translate-y-0"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="border-t border-gray-100 bg-gray-50">
        <div class="max-w-2xl mx-auto px-4 py-4">
            <form method="GET" action="{{ route('products.index') }}" class="relative">
                <svg class="absolute left-4 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0"/></svg>
                <input type="text" name="search" value="{{ request('search') }}"
                       placeholder="Search for products, categories..."
                       class="input pl-11 text-sm">
            </form>
        </div>
    </div>

    {{-- Mobile menu backdrop (closes menu on outside tap) --}}
    <div x-show="mobileOpen"
         x-cloak
         @click="mobileOpen = false"
         class="md:hidden fixed inset-0 bg-black/30 z-30"
         style="top: calc(4rem + 2rem);"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0">
    </div>

    {{-- Mobile slide-down menu panel --}}
    <div x-show="mobileOpen"
         x-cloak
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0 -translate-y-4"
         x-transition:enter-end="opacity-100 translate-y-0"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100 translate-y-0"
         x-transition:leave-end="opacity-0 -translate-y-4"
         class="md:hidden absolute left-0 right-0 bg-white z-40 overflow-y-auto shadow-xl border-t border-gray-100"
         style="max-height: calc(100vh - 6rem);"
         @click.stop>
        <div class="px-6 py-6">

            {{-- Main nav links --}}
            @foreach([['route' => 'home', 'label' => 'Home'], ['route' => 'products.index', 'label' => 'Shop All'], ['route' => 'cart.index', 'label' => 'My Cart'], ['route' => 'orders.index', 'label' => 'My Orders'], ['route' => 'wishlist.index', 'label' => 'Wishlist']] as $link)
            <a href="{{ route($link['route']) }}"
               @click="mobileOpen = false"
               class="flex items-center justify-between py-4 border-b border-gray-100 text-sm font-semibold uppercase tracking-widest text-[#1A1A1A] hover:text-[#C9A84C] transition-colors">
                {{ $link['label'] }}
                <svg class="w-4 h-4 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
            </a>
            @endforeach

            {{-- Categories --}}
            <div class="pt-5 pb-2">
                <p class="text-[10px] font-bold tracking-[0.3em] uppercase text-[#C9A84C] mb-3">Categories</p>
                <div class="grid grid-cols-3 gap-2">
                    @foreach(['Shirt','T-Shirt','Pant','Jeans','Saree','Salwar','Kurti'] as $cat)
                    <a href="{{ route('products.index', ['category' => $cat]) }}"
                       @click="mobileOpen = false"
                       class="text-center py-2.5 px-2 border border-gray-200 text-xs font-medium tracking-wider uppercase text-gray-600 hover:border-[#C9A84C] hover:text-[#C9A84C] transition-colors">{{ $cat }}</a>
                    @endforeach
                </div>
            </div>

            {{-- Auth section --}}
            <div class="pt-5 pb-2">
                @guest
                    {{-- Gold styled Login button as requested --}}
                    <a href="{{ route('login') }}"
                       @click="mobileOpen = false"
                       class="flex items-center justify-center w-full py-3.5 px-6 mb-3 bg-[#C9A84C] text-white text-xs font-bold tracking-widest uppercase hover:bg-[#b08a38] transition-colors">
                        Login
                    </a>
                    <a href="{{ route('register') }}"
                       @click="mobileOpen = false"
                       class="flex items-center justify-center w-full py-3.5 px-6 border border-[#1A1A1A] text-[#1A1A1A] text-xs font-bold tracking-widest uppercase hover:bg-gray-50 transition-colors">
                        Create Account
                    </a>
                @else
                    <div class="flex items-center gap-3 mb-4 pb-4 border-b border-gray-100">
                        <div class="w-10 h-10 bg-[#C9A84C] flex items-center justify-center text-white text-sm font-bold uppercase">{{ substr(auth()->user()->name, 0, 1) }}</div>
                        <div>
                            <p class="text-sm font-semibold text-[#1A1A1A]">{{ auth()->user()->name }}</p>
                            <p class="text-xs text-gray-400">{{ auth()->user()->email }}</p>
                        </div>
                    </div>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="text-sm font-semibold tracking-widest uppercase text-red-500 hover:text-red-600 transition-colors">Sign Out</button>
                    </form>
                @endguest
            </div>

            {{-- Search bar at the BOTTOM so it never auto-focuses on menu open --}}
            <div class="pt-4 pb-2 border-t border-gray-100 mt-2">
                <form method="GET" action="{{ route('products.index') }}" class="relative">
                    <svg class="absolute left-4 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0"/></svg>
                    <input type="text" name="search" value="{{ request('search') }}"
                           placeholder="Search products..."
                           autocomplete="off"
                           class="w-full pl-11 pr-4 py-3 border border-gray-200 text-sm text-[#1A1A1A] bg-gray-50 focus:outline-none focus:border-[#C9A84C] transition-colors">
                </form>
            </div>

        </div>
    </div>
</nav>

{{-- ═══════════════════════════════════════════════════════════════════════ --}}
{{-- FLASH MESSAGES                                                           --}}
{{-- ═══════════════════════════════════════════════════════════════════════ --}}
@if(session('success'))
<div id="flash-success"
     class="fixed top-6 left-1/2 -translate-x-1/2 z-[9999] flex items-center gap-3 bg-white border border-[#C9A84C] shadow-lg px-5 py-3.5 min-w-[280px] max-w-sm"
     style="animation: slideDownFade .4s ease both">
    <div class="w-6 h-6 bg-[#C9A84C] flex items-center justify-center flex-shrink-0">
        <svg class="w-3.5 h-3.5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
    </div>
    <p class="text-sm font-medium text-[#1A1A1A]">{{ session('success') }}</p>
</div>
@endif
@if(session('error'))
<div id="flash-error"
     class="fixed top-6 left-1/2 -translate-x-1/2 z-[9999] flex items-center gap-3 bg-white border border-red-400 shadow-lg px-5 py-3.5 min-w-[280px] max-w-sm"
     style="animation: slideDownFade .4s ease both">
    <div class="w-6 h-6 bg-red-500 flex items-center justify-center flex-shrink-0">
        <svg class="w-3.5 h-3.5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/></svg>
    </div>
    <p class="text-sm font-medium text-[#1A1A1A]">{{ session('error') }}</p>
</div>
@endif

{{-- Main Content --}}
<main>@yield('content')</main>

{{-- ═══════════════════════════════════════════════════════════════════════ --}}
{{-- FOOTER                                                                   --}}
{{-- ═══════════════════════════════════════════════════════════════════════ --}}
<footer class="hidden md:block bg-white border-t border-gray-200 mt-20">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-14">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-10">

            {{-- About --}}
            <div>
                <a href="{{ route('home') }}" class="flex flex-col leading-none mb-5">
                    @if(\App\Models\Setting::get('store_logo'))
                        <img src="{{ asset(\App\Models\Setting::get('store_logo')) }}" alt="Logo" class="h-8 object-contain self-start">
                    @else
                        @php
                            $sp = explode(' ', \App\Models\Setting::get('store_name', 'ClothStore'), 2);
                        @endphp
                        <span class="playfair text-xl sm:text-2xl font-bold text-[#1A1A1A] tracking-widest">{{ strtoupper($sp[0]) }}</span>
                        <span class="text-[9px] font-semibold tracking-[0.4em] text-[#C9A84C] uppercase">{{ $sp[1] ?? 'Store' }}</span>
                    @endif
                </a>
                <p class="text-sm text-gray-500 leading-relaxed max-w-xs">{{ \App\Models\Setting::get('store_tagline', 'Premium fashion for every occasion.') }}</p>
                {{-- Social icons --}}
                <div class="flex items-center gap-3 mt-5">
                    @if(\App\Models\Setting::get('social_facebook'))
                    <a href="{{ \App\Models\Setting::get('social_facebook') }}" target="_blank" class="w-8 h-8 border border-gray-200 flex items-center justify-center text-gray-500 hover:border-[#1877F2] hover:text-[#1877F2] transition-colors">
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/></svg>
                    </a>
                    @endif
                    @if(\App\Models\Setting::get('social_instagram'))
                    <a href="{{ \App\Models\Setting::get('social_instagram') }}" target="_blank" class="w-8 h-8 border border-gray-200 flex items-center justify-center text-gray-500 hover:border-[#E1306C] hover:text-[#E1306C] transition-colors">
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zM12 0C8.741 0 8.333.014 7.053.072 2.695.272.273 2.69.073 7.052.014 8.333 0 8.741 0 12c0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98C8.333 23.986 8.741 24 12 24c3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98C15.668.014 15.259 0 12 0zm0 5.838a6.162 6.162 0 100 12.324 6.162 6.162 0 000-12.324zM12 16a4 4 0 110-8 4 4 0 010 8zm6.406-11.845a1.44 1.44 0 100 2.881 1.44 1.44 0 000-2.881z"/></svg>
                    </a>
                    @endif
                    @if(\App\Models\Setting::get('whatsapp_number'))
                    <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', \App\Models\Setting::get('whatsapp_number')) }}" target="_blank" class="w-8 h-8 border border-gray-200 flex items-center justify-center text-gray-500 hover:border-[#25D366] hover:text-[#25D366] transition-colors">
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.890-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
                    </a>
                    @endif
                    @if(\App\Models\Setting::get('social_youtube'))
                    <a href="{{ \App\Models\Setting::get('social_youtube') }}" target="_blank" class="w-8 h-8 border border-gray-200 flex items-center justify-center text-gray-500 hover:border-[#FF0000] hover:text-[#FF0000] transition-colors">
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M23.498 6.186a3.016 3.016 0 0 0-2.122-2.136C19.505 3.545 12 3.545 12 3.545s-7.505 0-9.377.505A3.017 3.017 0 0 0 .502 6.186C0 8.07 0 12 0 12s0 3.93.502 5.814a3.016 3.016 0 0 0 2.122 2.136c1.871.505 9.376.505 9.376.505s7.505 0 9.377-.505a3.015 3.015 0 0 0 2.122-2.136C24 15.93 24 12 24 12s0-3.93-.502-5.814zM9.545 15.568V8.432L15.818 12l-6.273 3.568z"/></svg>
                    </a>
                    @endif
                    @if(\App\Models\Setting::get('social_tiktok'))
                    <a href="{{ \App\Models\Setting::get('social_tiktok') }}" target="_blank" class="w-8 h-8 border border-gray-200 flex items-center justify-center text-gray-500 hover:border-[#000000] hover:text-[#000000] transition-colors">
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M12.525.02c1.31-.02 2.61-.01 3.91-.02.08 1.53.63 3.09 1.75 4.17 1.12 1.11 2.7 1.62 4.24 1.79v4.03c-1.44-.05-2.89-.35-4.2-.97-.57-.26-1.1-.59-1.62-.93-.01 2.92.01 5.84-.02 8.75-.08 1.4-.54 2.79-1.35 3.94-1.31 1.92-3.58 3.17-5.91 3.21-1.43.08-2.86-.31-4.08-1.03-2.02-1.19-3.44-3.37-3.65-5.71-.24-2.37.58-4.71 2.22-6.32 1.5-1.48 3.56-2.35 5.61-2.28 0 1.34.02 2.68 0 4.02-1.29-.16-2.61.08-3.71.74-.83.51-1.46 1.32-1.67 2.26-.23 1.05-.03 2.17.57 3.03.65.91 1.73 1.48 2.81 1.55 1.16.08 2.33-.31 3.16-1.1.91-.85 1.42-2.11 1.45-3.38.04-5.26.01-10.51.03-15.77z"/></svg>
                    </a>
                    @endif
                </div>
            </div>

            {{-- Quick Links --}}
            <div>
                <h4 class="text-xs font-bold tracking-[0.25em] uppercase text-[#C9A84C] mb-5">Quick Links</h4>
                <ul class="space-y-3">
                    @foreach([['route' => 'home', 'label' => 'Home'], ['route' => 'products.index', 'label' => 'Shop All'], ['route' => 'cart.index', 'label' => 'Shopping Cart'], ['route' => 'orders.index', 'label' => 'My Orders']] as $link)
                        <li><a href="{{ route($link['route']) }}" class="text-sm text-gray-500 hover:text-[#C9A84C] transition-colors tracking-wide">{{ $link['label'] }}</a></li>
                    @endforeach
                </ul>
            </div>

            {{-- Categories --}}
            <div>
                <h4 class="text-xs font-bold tracking-[0.25em] uppercase text-[#C9A84C] mb-5">Categories</h4>
                <ul class="space-y-3">
                    @foreach(['Shirt','T-Shirt','Jeans','Saree','Salwar','Kurti','Pant'] as $cat)
                        <li><a href="{{ route('products.index', ['category' => $cat]) }}" class="text-sm text-gray-500 hover:text-[#C9A84C] transition-colors tracking-wide">{{ $cat }}</a></li>
                    @endforeach
                </ul>
            </div>

            {{-- Contact --}}
            <div>
                <h4 class="text-xs font-bold tracking-[0.25em] uppercase text-[#C9A84C] mb-5">Contact Us</h4>
                <ul class="space-y-3 text-sm text-gray-500">
                    <li class="flex items-start gap-2"><span>📍</span><span>{{ \App\Models\Setting::get('store_address', 'Dhaka, Bangladesh') }}</span></li>
                    <li class="flex items-start gap-2"><span>📞</span><a href="tel:{{ preg_replace('/[^0-9]/', '', \App\Models\Setting::get('contact_phone', '0170000000')) }}" class="hover:text-[#C9A84C] transition-colors">{{ \App\Models\Setting::get('contact_phone') }}</a></li>
                    <li class="flex items-start gap-2"><span>📧</span><a href="mailto:{{ \App\Models\Setting::get('contact_email') }}" class="hover:text-[#C9A84C] transition-colors">{{ \App\Models\Setting::get('contact_email') }}</a></li>
                    <li class="flex items-start gap-2"><span>⏰</span><span>Sat–Thu: 10 AM – 8 PM</span></li>
                </ul>
            </div>
        </div>

        {{-- Bottom bar --}}
        <div class="mt-12 pt-6 border-t border-gray-100 flex flex-col sm:flex-row items-center justify-between gap-3">
            <p class="text-xs text-gray-400 tracking-wider">{{ \App\Models\Setting::get('copyright_text', '© ' . date('Y') . ' ClothStore. All rights reserved.') }}</p>
            <p class="text-xs text-gray-400 tracking-wider">Made with ♥ in Bangladesh</p>
        </div>
    </div>
</footer>

{{-- ═══════════════════════════════════════════════════════════════════════ --}}
{{-- MOBILE BOTTOM NAVIGATION                                                 --}}
{{-- ═══════════════════════════════════════════════════════════════════════ --}}
<nav class="md:hidden fixed bottom-0 left-0 right-0 z-50 bg-white border-t border-gray-200">
    <div class="grid grid-cols-4 h-16">
        @php $cartCount = session('cart') ? count(session('cart')) : 0; @endphp
        <a href="{{ route('home') }}" class="flex flex-col items-center justify-center gap-1 transition-colors {{ request()->routeIs('home') ? 'text-[#C9A84C]' : 'text-gray-400 hover:text-gray-600' }}">
            <svg class="w-6 h-6" fill="{{ request()->routeIs('home') ? 'currentColor' : 'none' }}" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
            <span class="text-[10px] font-semibold tracking-widest uppercase">Home</span>
        </a>
        <a href="{{ route('products.index') }}" class="flex flex-col items-center justify-center gap-1 transition-colors {{ request()->routeIs('products.*') ? 'text-[#C9A84C]' : 'text-gray-400 hover:text-gray-600' }}">
            <svg class="w-6 h-6" fill="{{ request()->routeIs('products.*') ? 'currentColor' : 'none' }}" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/></svg>
            <span class="text-[10px] font-semibold tracking-widest uppercase">Shop</span>
        </a>
        <a href="{{ route('cart.index') }}" class="flex flex-col items-center justify-center gap-1 transition-colors {{ request()->routeIs('cart.*') ? 'text-[#C9A84C]' : 'text-gray-400 hover:text-gray-600' }}">
            <div class="relative">
                <svg class="w-6 h-6" fill="{{ request()->routeIs('cart.*') ? 'currentColor' : 'none' }}" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                @if($cartCount > 0)
                    <span class="absolute -top-2 -right-2 bg-[#C9A84C] text-white text-[10px] w-4 h-4 rounded-full flex items-center justify-center font-bold">{{ $cartCount > 9 ? '9+' : $cartCount }}</span>
                @endif
            </div>
            <span class="text-[10px] font-semibold tracking-widest uppercase">Cart</span>
        </a>
        @auth
        <a href="{{ route('orders.index') }}" class="flex flex-col items-center justify-center gap-1 transition-colors {{ request()->routeIs('orders.*') ? 'text-[#C9A84C]' : 'text-gray-400 hover:text-gray-600' }}">
            <svg class="w-6 h-6" fill="{{ request()->routeIs('orders.*') ? 'currentColor' : 'none' }}" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
            <span class="text-[10px] font-semibold tracking-widest uppercase">Orders</span>
        </a>
        @else
        <a href="{{ route('login') }}" class="flex flex-col items-center justify-center gap-1 text-gray-400 hover:text-gray-600 transition-colors">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
            <span class="text-[10px] font-semibold tracking-widest uppercase">Login</span>
        </a>
        @endauth
    </div>
</nav>

@if(\App\Models\Setting::get('whatsapp_number'))
<a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', \App\Models\Setting::get('whatsapp_number')) }}?text=Hello%20{{ \App\Models\Setting::get('store_name', 'ClothStore') }}!"
   target="_blank" rel="noopener"
   class="fixed right-5 z-[9998] flex items-center justify-center w-12 h-12 rounded-full shadow-lg hover:scale-110 transition-transform"
   style="background:#25D366; bottom: 5rem;"
   title="Chat on WhatsApp">
    <svg class="w-6 h-6" fill="white" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.890-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
</a>
@endif

<style>
    @keyframes slideDownFade {
        from { opacity: 0; transform: translate(-50%, -20px); }
        to   { opacity: 1; transform: translate(-50%, 0); }
    }
</style>

@stack('scripts')
<script>
    setTimeout(() => {
        ['flash-success','flash-error'].forEach(id => {
            const el = document.getElementById(id);
            if (el) { el.style.transition='opacity .5s'; el.style.opacity='0'; setTimeout(()=>el?.remove(), 500); }
        });
    }, 3500);
</script>
</body>
</html>
