@extends('layouts.app')

@section('title', 'ClothStore — Premium Fashion')
@section('description', 'Discover premium clothing — Shirts, T-Shirts, Jeans, Sarees and more.')

@section('content')

{{-- Hero Section --}}
<section class="relative overflow-hidden min-h-[90vh] flex items-center">
    {{-- Background gradient --}}
    <div class="absolute inset-0 bg-gradient-to-br from-indigo-950 via-gray-950 to-gray-950"></div>
    <div class="absolute inset-0" style="background: radial-gradient(ellipse 80% 60% at 60% 30%, rgba(99,102,241,0.15), transparent)"></div>
    {{-- Decorative circles --}}
    <div class="absolute top-20 right-20 w-96 h-96 bg-indigo-600/10 rounded-full blur-3xl"></div>
    <div class="absolute bottom-20 left-10 w-64 h-64 bg-purple-600/10 rounded-full blur-3xl"></div>

    <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-24 grid md:grid-cols-2 gap-16 items-center">
        <div>
            <div class="inline-flex items-center gap-2 bg-indigo-600/10 border border-indigo-500/30 text-indigo-400 text-sm font-medium px-4 py-1.5 rounded-full mb-6">
                <span class="w-2 h-2 bg-indigo-400 rounded-full animate-pulse"></span>
                New Season Collection 2025
            </div>
            <h1 class="text-5xl md:text-6xl lg:text-7xl font-extrabold text-white leading-tight mb-6">
                Dress for<br>
                <span class="text-transparent bg-clip-text bg-gradient-to-r from-indigo-400 to-purple-400">Every Moment</span>
            </h1>
            <p class="text-lg text-gray-400 leading-relaxed mb-8 max-w-md">
                From casual everyday wear to elegant special occasions — discover fashion that speaks your style at unbeatable prices.
            </p>
            <div class="flex items-center gap-4">
                <a href="{{ route('products.index') }}" class="btn-primary text-base px-8 py-3.5">
                    Shop Now →
                </a>
                <a href="{{ route('products.index', ['category' => 'Saree']) }}" class="btn-outline text-base px-8 py-3.5">
                    New Arrivals
                </a>
            </div>
            <div class="flex items-center gap-8 mt-10 pt-8 border-t border-gray-800">
                <div>
                    <p class="text-2xl font-bold text-white">200+</p>
                    <p class="text-sm text-gray-500">Products</p>
                </div>
                <div class="w-px h-8 bg-gray-800"></div>
                <div>
                    <p class="text-2xl font-bold text-white">50k+</p>
                    <p class="text-sm text-gray-500">Happy Customers</p>
                </div>
                <div class="w-px h-8 bg-gray-800"></div>
                <div>
                    <p class="text-2xl font-bold text-white">4.9★</p>
                    <p class="text-sm text-gray-500">Rating</p>
                </div>
            </div>
        </div>
        <div class="relative hidden md:block">
            <div class="relative w-full aspect-square max-w-lg mx-auto">
                <div class="absolute inset-0 bg-gradient-to-br from-indigo-600/20 to-purple-600/20 rounded-3xl"></div>
                <img src="https://images.unsplash.com/photo-1558618666-fcd25c85cd64?q=80&w=800&auto=format&fit=crop"
                     alt="Fashion model"
                     class="w-full h-full object-cover rounded-3xl">
                {{-- Floating card --}}
                <div class="absolute -bottom-4 -left-4 bg-gray-900 border border-gray-800 rounded-2xl p-4 shadow-2xl">
                    <p class="text-xs text-gray-500 mb-1">Today's Best Deal</p>
                    <p class="text-white font-bold">Up to 40% Off</p>
                    <p class="text-indigo-400 text-xs">Premium Collection</p>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- Categories --}}
<section class="py-20 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="flex items-center justify-between mb-10">
        <div>
            <h2 class="text-3xl font-bold text-white">Shop by Category</h2>
            <p class="text-gray-500 mt-1">Find exactly what you're looking for</p>
        </div>
        <a href="{{ route('products.index') }}" class="btn-outline text-sm">View All</a>
    </div>
    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-4">
        @php
            $categories = [
                ['name' => 'Shirt',    'icon' => '👔', 'color' => 'from-blue-600/20 to-blue-800/10'],
                ['name' => 'T-Shirt',  'icon' => '👕', 'color' => 'from-green-600/20 to-green-800/10'],
                ['name' => 'Jeans',    'icon' => '👖', 'color' => 'from-purple-600/20 to-purple-800/10'],
                ['name' => 'Saree',    'icon' => '🥻', 'color' => 'from-pink-600/20 to-pink-800/10'],
                ['name' => 'Salwar',   'icon' => '👗', 'color' => 'from-orange-600/20 to-orange-800/10'],
                ['name' => 'Kurti',    'icon' => '🎽', 'color' => 'from-red-600/20 to-red-800/10'],
            ];
        @endphp
        @foreach($categories as $cat)
            <a href="{{ route('products.index', ['category' => $cat['name']]) }}"
               class="group relative bg-gray-900 border border-gray-800 rounded-2xl p-6 text-center hover:border-indigo-500/50 hover:bg-gray-800 transition-all duration-300 hover:-translate-y-1">
                <div class="absolute inset-0 bg-gradient-to-br {{ $cat['color'] }} rounded-2xl opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                <div class="relative">
                    <div class="text-3xl mb-3">{{ $cat['icon'] }}</div>
                    <p class="text-sm font-semibold text-gray-300 group-hover:text-white transition-colors">{{ $cat['name'] }}</p>
                </div>
            </a>
        @endforeach
    </div>
</section>

{{-- Featured Products --}}
<section class="py-10 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="flex items-center justify-between mb-10">
        <div>
            <h2 class="text-3xl font-bold text-white">Featured Products</h2>
            <p class="text-gray-500 mt-1">Handpicked for you</p>
        </div>
        <a href="{{ route('products.index') }}" class="btn-outline text-sm">See All</a>
    </div>
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
        @forelse($featuredProducts as $product)
            @include('partials.product-card', ['product' => $product])
        @empty
            <div class="col-span-4 text-center py-16 text-gray-500">
                <p class="text-lg">No products yet. Seed the database to see products here.</p>
                <a href="{{ route('products.index') }}" class="btn-primary mt-4 inline-block">Browse All</a>
            </div>
        @endforelse
    </div>
</section>

{{-- Banner --}}
<section class="py-20 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="relative overflow-hidden bg-gradient-to-r from-indigo-600 to-purple-600 rounded-3xl p-10 md:p-16">
        <div class="absolute top-0 right-0 w-64 h-64 bg-white/10 rounded-full -translate-y-1/2 translate-x-1/2"></div>
        <div class="absolute bottom-0 left-0 w-40 h-40 bg-white/10 rounded-full translate-y-1/2 -translate-x-1/2"></div>
        <div class="relative text-center">
            <h2 class="text-3xl md:text-4xl font-bold text-white mb-4">Get 20% Off Your First Order!</h2>
            <p class="text-indigo-100 mb-8 max-w-xl mx-auto">Sign up now and unlock exclusive discounts, early access to new arrivals, and more.</p>
            <a href="{{ route('register') }}" class="bg-white text-indigo-600 font-bold px-8 py-3.5 rounded-xl hover:bg-indigo-50 transition-colors shadow-xl">
                Create Free Account
            </a>
        </div>
    </div>
</section>

@endsection
