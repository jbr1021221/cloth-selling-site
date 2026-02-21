@extends('layouts.app')
@section('title', 'ClothStore — Premium Fashion Bangladesh')
@section('description', 'Discover the new 2025 collection — premium shirts, jeans, sarees and more. Free delivery above ৳999.')

@section('content')

{{-- ═══════════════════════════════════════════════════════════════════════ --}}
{{-- 1. HERO SECTION — ZARA Style                                             --}}
{{-- ═══════════════════════════════════════════════════════════════════════ --}}
<section class="relative bg-white overflow-hidden" style="min-height: 92vh;">

    {{-- Split layout: text left, image right --}}
    <div class="grid md:grid-cols-2 min-h-[92vh]">

        {{-- Left: Text content --}}
        <div class="flex flex-col justify-center px-8 sm:px-12 lg:px-20 xl:px-28 py-20 md:py-0 order-2 md:order-1 bg-[#FAFAF8]">

            {{-- Eyebrow label --}}
            <div class="flex items-center gap-3 mb-8">
                <div class="w-8 h-px bg-[#C9A84C]"></div>
                <span class="text-xs font-semibold tracking-[0.35em] uppercase text-[#C9A84C]">New Arrivals 2025</span>
            </div>

            {{-- Main heading --}}
            <h1 class="playfair text-5xl sm:text-6xl lg:text-7xl font-bold text-[#1A1A1A] leading-[1.05] tracking-tight mb-6">
                The New<br>
                <em class="not-italic text-[#C9A84C]">Collection</em><br>
                Is Here.
            </h1>

            <p class="text-sm sm:text-base text-gray-500 leading-relaxed mb-10 max-w-sm tracking-wide">
                Premium clothing for every occasion — from casual everyday wear to elegant celebrations. Delivered across Bangladesh.
            </p>

            {{-- CTAs --}}
            <div class="flex flex-wrap items-center gap-4 mb-12">
                <a href="{{ route('products.index') }}" class="btn-primary">Shop Now</a>
                <a href="{{ route('products.index', ['category' => 'Saree']) }}" class="btn-outline">View Lookbook</a>
            </div>

            {{-- Stats --}}
            <div class="flex items-center gap-8 pt-8 border-t border-gray-200">
                <div>
                    <p class="text-2xl font-bold text-[#1A1A1A] playfair">200+</p>
                    <p class="text-xs uppercase tracking-widest text-gray-400 mt-0.5">Products</p>
                </div>
                <div class="w-px h-8 bg-gray-200"></div>
                <div>
                    <p class="text-2xl font-bold text-[#1A1A1A] playfair">50k+</p>
                    <p class="text-xs uppercase tracking-widest text-gray-400 mt-0.5">Customers</p>
                </div>
                <div class="w-px h-8 bg-gray-200"></div>
                <div>
                    <p class="text-2xl font-bold text-[#C9A84C] playfair">4.9★</p>
                    <p class="text-xs uppercase tracking-widest text-gray-400 mt-0.5">Rating</p>
                </div>
            </div>
        </div>

        {{-- Right: Hero image --}}
        <div class="relative order-1 md:order-2 h-64 sm:h-80 md:h-auto">
            <img src="https://images.unsplash.com/photo-1558618666-fcd25c85cd64?q=80&w=1000&auto=format&fit=crop"
                 alt="New Collection 2025" class="absolute inset-0 w-full h-full object-cover">
            {{-- Overlay gradient for text readability --}}
            <div class="absolute inset-0 bg-gradient-to-r from-[#FAFAF8] via-transparent to-transparent opacity-20"></div>

            {{-- Floating badge —absolute positioned on image --}}
            <div class="absolute bottom-6 left-6 bg-white shadow-xl px-5 py-4">
                <p class="text-xs uppercase tracking-widest text-gray-400 mb-0.5">Today's Offer</p>
                <p class="playfair text-lg font-bold text-[#1A1A1A]">Up to 50% Off</p>
                <p class="text-xs text-[#C9A84C] font-semibold tracking-wider uppercase mt-0.5">Premium Collection</p>
            </div>
        </div>
    </div>
</section>

{{-- ═══════════════════════════════════════════════════════════════════════ --}}
{{-- 2. TRUST BAR — Gold background                                           --}}
{{-- ═══════════════════════════════════════════════════════════════════════ --}}
<section class="bg-[#C9A84C]">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-2 md:grid-cols-4 divide-x divide-[#b08a38]/30">
            @php
            $trustItems = [
                ['icon' => '🚚', 'title' => 'Free Delivery', 'sub' => 'On orders above ৳999'],
                ['icon' => '💵', 'title' => 'Cash on Delivery', 'sub' => 'Pay when it arrives'],
                ['icon' => '↩️', 'title' => '7-Day Returns', 'sub' => 'Hassle-free returns'],
                ['icon' => '✅', 'title' => '100% Authentic', 'sub' => 'Genuine products only'],
            ];
            @endphp
            @foreach($trustItems as $item)
            <div class="flex items-center gap-3 px-6 py-5 sm:py-6">
                <span class="text-2xl hidden sm:block">{{ $item['icon'] }}</span>
                <div>
                    <p class="text-xs sm:text-sm font-bold text-white uppercase tracking-wider">{{ $item['title'] }}</p>
                    <p class="text-[11px] text-white/70 tracking-wide mt-0.5 hidden sm:block">{{ $item['sub'] }}</p>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</section>

{{-- ═══════════════════════════════════════════════════════════════════════ --}}
{{-- 3. SHOP BY CATEGORY                                                       --}}
{{-- ═══════════════════════════════════════════════════════════════════════ --}}
<section class="py-16 sm:py-24 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

        {{-- Section header --}}
        <div class="text-center mb-12">
            <span class="text-xs font-semibold tracking-[0.35em] uppercase text-[#C9A84C] block mb-3">Collections</span>
            <h2 class="section-heading gold-underline">Shop by Category</h2>
        </div>

        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-3 sm:gap-4">
            @php
            $categories = [
                ['name' => 'Shirt',   'icon' => '👔', 'image' => 'https://images.unsplash.com/photo-1596755094514-f87e34085b2c?q=80&w=400&auto=format&fit=crop'],
                ['name' => 'T-Shirt', 'icon' => '👕', 'image' => 'https://images.unsplash.com/photo-1521572163474-6864f9cf17ab?q=80&w=400&auto=format&fit=crop'],
                ['name' => 'Jeans',   'icon' => '👖', 'image' => 'https://images.unsplash.com/photo-1542272604-787c3835535d?q=80&w=400&auto=format&fit=crop'],
                ['name' => 'Saree',   'icon' => '🥻', 'image' => 'https://images.unsplash.com/photo-1610030469983-98e550d6193c?q=80&w=400&auto=format&fit=crop'],
                ['name' => 'Panjabi', 'icon' => '🎽', 'image' => 'https://images.unsplash.com/photo-1550639524-a31c777ca9a2?q=80&w=400&auto=format&fit=crop'],
                ['name' => 'Kids',    'icon' => '👶', 'image' => 'https://images.unsplash.com/photo-1518831959646-742c3a14ebf7?q=80&w=400&auto=format&fit=crop'],
            ];
            @endphp

            @foreach($categories as $cat)
            <a href="{{ route('products.index', ['category' => $cat['name']]) }}"
               class="group relative overflow-hidden bg-[#F8F8F8] border border-gray-100 hover:border-[#C9A84C] transition-all duration-300 hover:-translate-y-1 hover:shadow-lg text-center">
                {{-- Category image --}}
                <div class="aspect-square overflow-hidden">
                    <img src="{{ $cat['image'] }}" alt="{{ $cat['name'] }}"
                         class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500 opacity-80 group-hover:opacity-100">
                </div>
                {{-- Label --}}
                <div class="py-3 px-2 border-t border-gray-100 group-hover:border-[#C9A84C] transition-colors bg-white">
                    <p class="text-xs font-bold uppercase tracking-[0.2em] text-[#1A1A1A] group-hover:text-[#C9A84C] transition-colors">{{ $cat['name'] }}</p>
                </div>
            </a>
            @endforeach
        </div>

        <div class="text-center mt-8">
            <a href="{{ route('products.index') }}" class="btn-outline-dark">View All Products</a>
        </div>
    </div>
</section>

{{-- ═══════════════════════════════════════════════════════════════════════ --}}
{{-- 4. FEATURED PRODUCTS                                                      --}}
{{-- ═══════════════════════════════════════════════════════════════════════ --}}
<section class="py-16 sm:py-24 bg-[#F8F8F8]">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

        <div class="flex items-end justify-between mb-10">
            <div>
                <span class="text-xs font-semibold tracking-[0.35em] uppercase text-[#C9A84C] block mb-2">Curated for You</span>
                <h2 class="section-heading">Featured Collection</h2>
            </div>
            <a href="{{ route('products.index') }}" class="text-xs font-semibold tracking-widest uppercase text-[#1A1A1A] hover:text-[#C9A84C] transition-colors flex items-center gap-2 hidden sm:flex">
                See All
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/></svg>
            </a>
        </div>

        {{-- 2 cols mobile → 3 tablet → 4 desktop --}}
        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-3 sm:gap-5">
            @forelse($featuredProducts as $product)
                @include('partials.product-card', ['product' => $product])
            @empty
                <div class="col-span-4 text-center py-16">
                    <p class="text-gray-400 text-sm uppercase tracking-widest mb-6">No products yet</p>
                    <a href="{{ route('products.index') }}" class="btn-primary">Browse All</a>
                </div>
            @endforelse
        </div>

        <div class="text-center mt-10 sm:hidden">
            <a href="{{ route('products.index') }}" class="btn-outline-dark">See All Products</a>
        </div>
    </div>
</section>

{{-- ═══════════════════════════════════════════════════════════════════════ --}}
{{-- 5. BANNER — Exclusive Offer                                               --}}
{{-- ═══════════════════════════════════════════════════════════════════════ --}}
<section class="relative overflow-hidden" style="background: linear-gradient(135deg, #C9A84C 0%, #e8cf84 50%, #C9A84C 100%);">
    {{-- Decorative circles --}}
    <div class="absolute top-0 right-0 w-80 h-80 bg-white/10 rounded-full -translate-y-1/2 translate-x-1/3"></div>
    <div class="absolute bottom-0 left-0 w-60 h-60 bg-white/10 rounded-full translate-y-1/2 -translate-x-1/3"></div>

    <div class="relative max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-16 sm:py-24 text-center">
        <span class="text-white/70 text-xs font-bold tracking-[0.4em] uppercase block mb-4">Limited Time Offer</span>
        <h2 class="playfair text-3xl sm:text-4xl md:text-5xl font-bold text-white mb-4 leading-tight">
            Exclusive Sale — Up to 50% Off
        </h2>
        <p class="text-white/80 text-sm tracking-wider mb-8 max-w-md mx-auto">
            Shop our biggest sale of the season. Premium quality clothing at unbeatable prices.
        </p>
        <a href="{{ route('products.index') }}"
           class="inline-flex items-center gap-3 bg-white text-[#C9A84C] font-bold tracking-widest uppercase text-sm px-8 py-4 hover:bg-[#1A1A1A] hover:text-white transition-all duration-300">
            Shop Sale
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/></svg>
        </a>
    </div>
</section>

{{-- ═══════════════════════════════════════════════════════════════════════ --}}
{{-- 6. WHY CHOOSE US                                                           --}}
{{-- ═══════════════════════════════════════════════════════════════════════ --}}
<section class="py-16 sm:py-24 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

        <div class="text-center mb-12">
            <span class="text-xs font-semibold tracking-[0.35em] uppercase text-[#C9A84C] block mb-3">Our Promise</span>
            <h2 class="section-heading gold-underline">Why Choose Us</h2>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-3 gap-6 sm:gap-8">
            @php
            $features = [
                [
                    'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>',
                    'title' => 'Premium Quality',
                    'desc'  => 'Every garment is carefully selected and quality-checked. We stock only the finest fabrics and stitching.',
                ],
                [
                    'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M13 10V3L4 14h7v7l9-11h-7z"/>',
                    'title' => 'Fast Delivery',
                    'desc'  => 'Dhaka in 2 days, all Bangladesh in 5 days. Cash on delivery available — no advance payment needed.',
                ],
                [
                    'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>',
                    'title' => 'Easy Returns',
                    'desc'  => '7-day hassle-free return policy. Not happy with your order? We will make it right — no questions asked.',
                ],
            ];
            @endphp
            @foreach($features as $f)
            <div class="group text-center p-8 border border-gray-100 hover:border-[#C9A84C] transition-all duration-300 hover:-translate-y-1 hover:shadow-lg">
                {{-- Gold icon --}}
                <div class="w-14 h-14 border-2 border-[#C9A84C] flex items-center justify-center mx-auto mb-6 group-hover:bg-[#C9A84C] transition-colors duration-300">
                    <svg class="w-7 h-7 text-[#C9A84C] group-hover:text-white transition-colors duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        {!! $f['icon'] !!}
                    </svg>
                </div>
                <h3 class="text-sm font-bold uppercase tracking-[0.2em] text-[#1A1A1A] mb-3">{{ $f['title'] }}</h3>
                <p class="text-sm text-gray-500 leading-relaxed">{{ $f['desc'] }}</p>
            </div>
            @endforeach
        </div>
    </div>
</section>

{{-- ═══════════════════════════════════════════════════════════════════════ --}}
{{-- 7. NEWSLETTER / SIGN UP STRIP                                             --}}
{{-- ═══════════════════════════════════════════════════════════════════════ --}}
@guest
<section class="bg-[#1A1A1A] py-14 sm:py-16">
    <div class="max-w-2xl mx-auto px-4 sm:px-6 text-center">
        <span class="text-xs font-semibold tracking-[0.35em] uppercase text-[#C9A84C] block mb-4">Exclusive Members</span>
        <h2 class="playfair text-2xl sm:text-3xl font-bold text-white mb-3">Get 20% Off Your First Order</h2>
        <p class="text-sm text-gray-400 mb-8 tracking-wide">Create a free account and unlock exclusive discounts, early access to new arrivals, and more.</p>
        <div class="flex flex-col sm:flex-row gap-3 justify-center">
            <a href="{{ route('register') }}"
               class="inline-flex items-center justify-center gap-2 bg-[#C9A84C] hover:bg-[#b08a38] text-white font-bold tracking-widest uppercase text-sm px-8 py-4 transition-colors duration-300">
                Create Free Account
            </a>
            <a href="{{ route('login') }}"
               class="inline-flex items-center justify-center gap-2 border border-white/20 text-white font-semibold tracking-widest uppercase text-sm px-8 py-4 hover:bg-white/5 transition-colors duration-300">
                Already a Member? Login
            </a>
        </div>
    </div>
</section>
@endguest

@endsection
