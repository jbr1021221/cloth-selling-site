@extends('layouts.app')
@section('title', 'My Wishlist — ClothStore')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">

    {{-- Page Header --}}
    <div class="flex items-center justify-between mb-8">
        <div>
            <h1 class="text-3xl font-bold text-white">My Wishlist</h1>
            <p class="text-gray-400 mt-1">
                {{ $wishlistItems->count() }} saved {{ Str::plural('item', $wishlistItems->count()) }}
            </p>
        </div>
        @if($wishlistItems->count() > 0)
            <a href="{{ route('products.index') }}"
               class="btn-outline text-sm flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                Continue Shopping
            </a>
        @endif
    </div>

    @if($wishlistItems->isEmpty())
        {{-- Empty State --}}
        <div class="text-center py-24">
            <div class="w-24 h-24 bg-gray-900 rounded-full flex items-center justify-center mx-auto mb-6 border border-gray-800">
                <svg class="w-12 h-12 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                          d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
                </svg>
            </div>
            <h2 class="text-2xl font-bold text-white mb-3">Your wishlist is empty</h2>
            <p class="text-gray-400 max-w-sm mx-auto mb-8">
                Browse our products and click the 🤍 heart button to save items you love for later.
            </p>
            <a href="{{ route('products.index') }}" class="btn-primary inline-flex items-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 16H4L5 9z"/>
                </svg>
                Explore Products
            </a>
        </div>

    @else
        {{-- Wishlist Grid --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
            @foreach($wishlistItems as $item)
                @php
                    $product     = $item->product;
                    $displayPrice = $product->discount_price && $product->discount_price < $product->price
                                        ? $product->discount_price : $product->price;
                    $hasDiscount  = $product->discount_price && $product->discount_price < $product->price;
                    $discountPct  = $hasDiscount ? round((($product->price - $product->discount_price) / $product->price) * 100) : 0;
                    $image        = is_array($product->images) && count($product->images) > 0
                                        ? $product->images[0]
                                        : 'https://images.unsplash.com/photo-1523381210434-271e8be1f52b?q=80&w=500&auto=format&fit=crop';
                    $isOutOfStock = $product->stock === 0;
                @endphp

                <div class="card group"
                     x-data="{
                         wishlisted: true,
                         loading: false,
                         async toggle() {
                             if(this.loading) return;
                             this.loading = true;
                             try {
                                 const res = await fetch('{{ route('wishlist.toggle', $product->id) }}', {
                                     method: 'POST',
                                     headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' }
                                 });
                                 const data = await res.json();
                                 this.wishlisted = data.wishlisted;
                                 if (!this.wishlisted) {
                                     this.$el.closest('[x-data]').style.opacity = '0';
                                     this.$el.closest('[x-data]').style.transform = 'scale(0.95)';
                                     setTimeout(() => this.$el.closest('[x-data]').remove(), 300);
                                 }
                             } finally { this.loading = false; }
                         }
                     }"
                     style="transition: opacity 0.3s ease, transform 0.3s ease;">

                    {{-- Card Image --}}
                    <div class="relative overflow-hidden h-56">
                        <a href="{{ route('products.show', $product->id) }}">
                            <img src="{{ $image }}" alt="{{ $product->name }}"
                                 class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500 {{ $isOutOfStock ? 'opacity-50 grayscale' : '' }}">
                        </a>

                        {{-- Discount badge --}}
                        @if($hasDiscount)
                            <span class="absolute top-3 left-3 badge bg-red-500 text-white">{{ $discountPct }}% OFF</span>
                        @endif

                        {{-- Out of stock badge --}}
                        @if($isOutOfStock)
                            <span class="absolute top-3 left-3 badge bg-gray-900 border border-gray-700 text-gray-400">Out of Stock</span>
                        @endif

                        {{-- Remove from wishlist (heart) --}}
                        <button @click="toggle()"
                                :class="wishlisted ? 'text-red-500 bg-red-500/10 border-red-500/30' : 'text-gray-400 bg-gray-900/80 border-gray-700'"
                                :disabled="loading"
                                class="absolute top-3 right-3 w-9 h-9 rounded-full border flex items-center justify-center transition-all hover:scale-110 active:scale-95 disabled:opacity-50">
                            <svg class="w-4 h-4" :fill="wishlisted ? 'currentColor' : 'none'" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
                            </svg>
                        </button>

                        {{-- Hover overlay: Quick add --}}
                        <div class="absolute inset-0 bg-black/50 opacity-0 group-hover:opacity-100 transition-opacity duration-300 flex items-end p-4">
                            @if($isOutOfStock)
                                <button type="button" disabled
                                        class="w-full bg-gray-700 border border-gray-600 text-gray-500 font-semibold py-2.5 rounded-xl text-sm cursor-not-allowed">
                                    Out of Stock
                                </button>
                            @else
                                <form method="POST" action="{{ route('cart.add') }}" class="w-full">
                                    @csrf
                                    <input type="hidden" name="product_id" value="{{ $product->id }}">
                                    <input type="hidden" name="quantity" value="1">
                                    @if(is_array($product->sizes) && count($product->sizes) > 0)
                                        <input type="hidden" name="size" value="{{ $product->sizes[0] }}">
                                    @endif
                                    @if(is_array($product->colors) && count($product->colors) > 0)
                                        <input type="hidden" name="color" value="{{ $product->colors[0] }}">
                                    @endif
                                    <button type="submit"
                                            class="w-full bg-indigo-600 hover:bg-indigo-500 text-white font-semibold py-2.5 rounded-xl transition-all text-sm">
                                        Add to Cart
                                    </button>
                                </form>
                            @endif
                        </div>
                    </div>

                    {{-- Card Info --}}
                    <div class="p-4">
                        <p class="text-xs text-indigo-400 font-medium mb-1">{{ $product->category }}</p>
                        <a href="{{ route('products.show', $product->id) }}" class="block">
                            <h3 class="font-semibold text-gray-100 hover:text-indigo-400 transition-colors line-clamp-2 mb-2">
                                {{ $product->name }}
                            </h3>
                        </a>
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-2">
                                <span class="text-lg font-bold text-indigo-400">৳{{ number_format($displayPrice) }}</span>
                                @if($hasDiscount)
                                    <span class="text-sm text-gray-600 line-through">৳{{ number_format($product->price) }}</span>
                                @endif
                            </div>
                            <span class="text-xs text-gray-600">{{ \Carbon\Carbon::parse($item->created_at)->diffForHumans() }}</span>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @endif

</div>
@endsection
