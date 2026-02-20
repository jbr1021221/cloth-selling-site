@extends('layouts.app')

@section('title', 'All Products')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">

    {{-- Header --}}
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-white">All Products</h1>
        <p class="text-gray-500 mt-1">{{ $products->total() }} products found</p>
    </div>

    <div class="flex flex-col lg:flex-row gap-8">

        {{-- Sidebar Filters --}}
        <aside class="w-full lg:w-64 flex-shrink-0">
            <div class="bg-gray-900 border border-gray-800 rounded-2xl p-6 sticky top-24">
                <div class="flex items-center justify-between mb-6">
                    <h3 class="font-semibold text-white">Filters</h3>
                    <a href="{{ route('products.index') }}" class="text-xs text-indigo-400 hover:underline">Reset</a>
                </div>

                <form method="GET" action="{{ route('products.index') }}" id="filter-form">
                    {{-- Search --}}
                    <div class="mb-5">
                        <label class="label">Search</label>
                        <input type="text" name="search" value="{{ request('search') }}" placeholder="Search products..."
                               class="input text-sm" onchange="this.form.submit()">
                    </div>

                    {{-- Category --}}
                    <div class="mb-5">
                        <label class="label">Category</label>
                        <div class="space-y-2">
                            @php $cats = ['Shirt','T-Shirt','Pant','Jeans','Saree','Salwar','Kurti','Others']; @endphp
                            <label class="flex items-center gap-2 cursor-pointer group">
                                <input type="radio" name="category" value="" {{ !request('category') ? 'checked' : '' }}
                                       class="accent-indigo-600" onchange="this.form.submit()">
                                <span class="text-sm text-gray-400 group-hover:text-white transition-colors">All Categories</span>
                            </label>
                            @foreach($cats as $cat)
                                <label class="flex items-center gap-2 cursor-pointer group">
                                    <input type="radio" name="category" value="{{ $cat }}" {{ request('category') === $cat ? 'checked' : '' }}
                                           class="accent-indigo-600" onchange="this.form.submit()">
                                    <span class="text-sm text-gray-400 group-hover:text-white transition-colors">{{ $cat }}</span>
                                </label>
                            @endforeach
                        </div>
                    </div>

                    {{-- Price Range --}}
                    <div class="mb-5">
                        <label class="label">Price Range (৳)</label>
                        <div class="flex gap-2">
                            <input type="number" name="min_price" value="{{ request('min_price') }}"
                                   placeholder="Min" class="input text-sm" onchange="this.form.submit()">
                            <input type="number" name="max_price" value="{{ request('max_price') }}"
                                   placeholder="Max" class="input text-sm" onchange="this.form.submit()">
                        </div>
                    </div>

                    {{-- Size --}}
                    <div class="mb-5">
                        <label class="label">Size</label>
                        <select name="size" class="input text-sm" onchange="this.form.submit()">
                            <option value="">All Sizes</option>
                            @foreach(['XS','S','M','L','XL','XXL','30','32','34','36'] as $size)
                                <option value="{{ $size }}" {{ request('size') === $size ? 'selected' : '' }}>{{ $size }}</option>
                            @endforeach
                        </select>
                    </div>

                    <input type="hidden" name="sort" value="{{ request('sort', 'newest') }}">
                </form>
            </div>
        </aside>

        {{-- Products Grid --}}
        <div class="flex-1">
            {{-- Sort Bar --}}
            <div class="flex items-center justify-between mb-6 bg-gray-900 border border-gray-800 rounded-2xl px-4 py-3">
                <p class="text-sm text-gray-400">
                    Showing <span class="text-white font-medium">{{ $products->firstItem() }}–{{ $products->lastItem() }}</span>
                    of <span class="text-white font-medium">{{ $products->total() }}</span> results
                </p>
                <select onchange="window.location.href='{{ route('products.index') }}?' + new URLSearchParams({...Object.fromEntries(new URLSearchParams(window.location.search)), sort: this.value})"
                        class="bg-gray-800 border border-gray-700 text-gray-300 text-sm rounded-xl px-3 py-2 focus:outline-none focus:border-indigo-500">
                    <option value="newest" {{ request('sort','newest') === 'newest' ? 'selected' : '' }}>Newest</option>
                    <option value="price_asc" {{ request('sort') === 'price_asc' ? 'selected' : '' }}>Price: Low → High</option>
                    <option value="price_desc" {{ request('sort') === 'price_desc' ? 'selected' : '' }}>Price: High → Low</option>
                </select>
            </div>

            @if($products->count() > 0)
                <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-6">
                    @foreach($products as $product)
                        @include('partials.product-card', ['product' => $product])
                    @endforeach
                </div>
                <div class="mt-10">
                    {{ $products->withQueryString()->links() }}
                </div>
            @else
                <div class="text-center py-24">
                    <div class="text-6xl mb-4">🔍</div>
                    <h3 class="text-xl font-semibold text-white mb-2">No products found</h3>
                    <p class="text-gray-500 mb-6">Try adjusting your filters or search term.</p>
                    <a href="{{ route('products.index') }}" class="btn-primary">Clear Filters</a>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
