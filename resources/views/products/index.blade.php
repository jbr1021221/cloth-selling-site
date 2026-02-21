@extends('layouts.app')
@section('title', 'Shop — All Products')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10 sm:py-16 bg-white">

    {{-- Header --}}
    <div class="flex flex-col sm:flex-row sm:items-end justify-between mb-10 gap-6 border-b border-gray-100 pb-6">
        <div>
            <nav class="flex items-center gap-2 text-xs font-semibold tracking-widest uppercase text-gray-400 mb-4">
                <a href="{{ route('home') }}" class="hover:text-[#1A1A1A] transition-colors">Home</a>
                <span class="text-gray-300">/</span>
                <span class="text-[#1A1A1A]">Shop</span>
            </nav>
            <h1 class="playfair text-4xl sm:text-5xl font-bold text-[#1A1A1A]">Collection</h1>
            <p class="text-gray-500 mt-2 text-sm tracking-wide">{{ $products->total() }} items</p>
        </div>
        {{-- Mobile filter toggle --}}
        <button id="filter-toggle"
                class="lg:hidden w-full sm:w-auto btn-outline-dark py-3 flex justify-center"
                onclick="document.getElementById('sidebar-filters').classList.toggle('hidden')">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4"/></svg>
            Filters & Sort
        </button>
    </div>

    <div class="flex flex-col lg:flex-row gap-10">

        {{-- ── Sidebar Filters ─────────────────────────────────────── --}}
        <aside id="sidebar-filters" class="hidden lg:block w-full lg:w-64 flex-shrink-0">
            <div class="lg:sticky lg:top-28">

                <form method="GET" action="{{ route('products.index') }}" id="filter-form">

                    {{-- Sort Bar --}}
                    <div class="mb-8 pb-8 border-b border-gray-100">
                        <label class="text-xs font-bold uppercase tracking-[0.2em] text-[#1A1A1A] mb-4 block">Sort By</label>
                        <select name="sort" onchange="this.form.submit()" class="w-full border-b border-gray-300 py-2 text-sm text-[#1A1A1A] focus:outline-none focus:border-[#C9A84C] bg-transparent cursor-pointer">
                            <option value="newest" {{ request('sort','newest') === 'newest' ? 'selected' : '' }}>Newest Arrivals</option>
                            <option value="price_asc" {{ request('sort') === 'price_asc' ? 'selected' : '' }}>Price: Low to High</option>
                            <option value="price_desc" {{ request('sort') === 'price_desc' ? 'selected' : '' }}>Price: High to Low</option>
                        </select>
                    </div>

                    {{-- Search --}}
                    <div class="mb-8 pb-8 border-b border-gray-100">
                        <label class="text-xs font-bold uppercase tracking-[0.2em] text-[#1A1A1A] mb-4 block">Search</label>
                        <div class="relative">
                            <input type="text" name="search" value="{{ request('search') }}" placeholder="Search collection..."
                                   class="w-full border-b border-gray-300 py-2 pr-8 text-sm placeholder-gray-400 focus:outline-none focus:border-[#C9A84C] bg-transparent" onchange="this.form.submit()">
                            <svg class="w-4 h-4 text-gray-400 absolute right-0 top-1/2 -translate-y-1/2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0"/></svg>
                        </div>
                    </div>

                    {{-- Category --}}
                    <div class="mb-8 pb-8 border-b border-gray-100">
                        <label class="text-xs font-bold uppercase tracking-[0.2em] text-[#1A1A1A] mb-4 block">Category</label>
                        <div class="space-y-3">
                            @php $cats = ['Shirt','T-Shirt','Pant','Jeans','Saree','Salwar','Kurti','Others']; @endphp
                            <label class="flex items-center gap-3 cursor-pointer group">
                                <input type="radio" name="category" value="" {{ !request('category') ? 'checked' : '' }}
                                       class="w-4 h-4 accent-[#1A1A1A]" onchange="this.form.submit()">
                                <span class="text-sm text-gray-600 group-hover:text-[#1A1A1A] transition-colors">All Categories</span>
                            </label>
                            @foreach($cats as $cat)
                                <label class="flex items-center gap-3 cursor-pointer group">
                                    <input type="radio" name="category" value="{{ $cat }}" {{ request('category') === $cat ? 'checked' : '' }}
                                           class="w-4 h-4 accent-[#1A1A1A]" onchange="this.form.submit()">
                                    <span class="text-sm text-gray-600 group-hover:text-[#1A1A1A] transition-colors">{{ $cat }}</span>
                                </label>
                            @endforeach
                        </div>
                    </div>

                    {{-- Price Range --}}
                    <div class="mb-8 pb-8 border-b border-gray-100">
                        <label class="text-xs font-bold uppercase tracking-[0.2em] text-[#1A1A1A] mb-4 block">Price Range</label>
                        <div class="flex items-center gap-4">
                            <div class="flex-1 border-b border-gray-300 relative">
                                <span class="absolute left-0 top-1/2 -translate-y-1/2 text-gray-400 text-sm">৳</span>
                                <input type="number" name="min_price" value="{{ request('min_price') }}" placeholder="Min"
                                       class="w-full pl-4 py-2 text-sm focus:outline-none focus:border-[#C9A84C] bg-transparent" onchange="this.form.submit()">
                            </div>
                            <span class="text-gray-400">-</span>
                            <div class="flex-1 border-b border-gray-300 relative">
                                <span class="absolute left-0 top-1/2 -translate-y-1/2 text-gray-400 text-sm">৳</span>
                                <input type="number" name="max_price" value="{{ request('max_price') }}" placeholder="Max"
                                       class="w-full pl-4 py-2 text-sm focus:outline-none focus:border-[#C9A84C] bg-transparent" onchange="this.form.submit()">
                            </div>
                        </div>
                    </div>

                    {{-- Size --}}
                    <div class="mb-8">
                        <label class="text-xs font-bold uppercase tracking-[0.2em] text-[#1A1A1A] mb-4 block">Size</label>
                        <select name="size" class="w-full border-b border-gray-300 py-2 text-sm text-[#1A1A1A] focus:outline-none focus:border-[#C9A84C] bg-transparent cursor-pointer" onchange="this.form.submit()">
                            <option value="">All Sizes</option>
                            @foreach(['XS','S','M','L','XL','XXL','30','32','34','36'] as $size)
                                <option value="{{ $size }}" {{ request('size') === $size ? 'selected' : '' }}>{{ $size }}</option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Form Actions --}}
                    <div class="flex gap-2">
                        <button type="submit" class="btn-primary w-full py-3">Apply</button>
                        <a href="{{ route('products.index') }}" class="btn-outline w-full py-3 border-gray-300 text-gray-600 hover:bg-gray-100 hover:text-[#1A1A1A]">Reset</a>
                    </div>
                </form>
            </div>
        </aside>

        {{-- ── Products Grid ───────────────────────────────────────── --}}
        <div class="flex-1 min-w-0">

            @if($products->count() > 0)
                {{-- 2 cols mobile | 3 tablet | 4 large desktop --}}
                <div class="grid grid-cols-2 md:grid-cols-3 xl:grid-cols-4 gap-4 sm:gap-6 lg:gap-8">
                    @foreach($products as $product)
                        @include('partials.product-card', ['product' => $product])
                    @endforeach
                </div>
                {{-- Pagination --}}
                <div class="mt-16 border-t border-gray-100 pt-8 flex justify-center">
                    {{ $products->withQueryString()->links() }}
                </div>
            @else
                {{-- Empty state --}}
                <div class="text-center py-24 sm:py-32 bg-[#F8F8F8] border border-gray-100 flex flex-col items-center justify-center">
                    <svg class="w-16 h-16 text-gray-300 mb-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0"/></svg>
                    <h3 class="playfair text-2xl font-bold text-[#1A1A1A] mb-2">No products found</h3>
                    <p class="text-sm text-gray-500 mb-8 uppercase tracking-widest">Adjust your filters and try again.</p>
                    <a href="{{ route('products.index') }}" class="btn-outline-dark">Clear All Filters</a>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
