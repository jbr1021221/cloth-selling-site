@php
    $displayPrice = $product->discount_price ?? $product->price;
    $hasDiscount  = $product->discount_price && $product->discount_price < $product->price;
    $discountPct  = $hasDiscount ? round((($product->price - $product->discount_price) / $product->price) * 100) : 0;
    $image        = is_array($product->images) && count($product->images) > 0
        ? $product->images[0]
        : 'https://images.unsplash.com/photo-1523381210434-271e8be1f52b?q=80&w=500&auto=format&fit=crop';
    $isOutOfStock = $product->stock === 0;
    $isLowStock   = !$isOutOfStock && $product->stock < 10;
    $isWishlisted = auth()->check()
        ? \App\Models\Wishlist::where('user_id', auth()->id())->where('product_id', $product->id)->exists()
        : false;
@endphp

<div class="group bg-white border border-gray-100 hover:border-[#C9A84C] transition-all duration-300 hover:shadow-md flex flex-col"
     x-data="{
         wishlisted: {{ $isWishlisted ? 'true' : 'false' }},
         loading: false,
         async toggle() {
             if(this.loading) return;
             @auth
             this.loading = true;
             try {
                 const res = await fetch('{{ route('wishlist.toggle', $product->id) }}', {
                     method:'POST', headers:{'X-CSRF-TOKEN':'{{ csrf_token() }}','Accept':'application/json'}
                 });
                 const d = await res.json();
                 this.wishlisted = d.wishlisted;
             } finally { this.loading = false; }
             @else
             window.location = '{{ route('login') }}';
             @endauth
         }
     }">

    {{-- Image area --}}
    <a href="{{ route('products.show', $product->id) }}" class="relative overflow-hidden block bg-[#F8F8F8]" style="aspect-ratio:3/4">
        <img src="{{ $image }}" alt="{{ $product->name }}"
             class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-600 {{ $isOutOfStock ? 'opacity-40 grayscale' : '' }}">

        {{-- Discount badge --}}
        @if($hasDiscount)
            <span class="absolute top-0 left-0 bg-[#C9A84C] text-white text-[10px] font-bold px-2.5 py-1 tracking-wider uppercase">-{{ $discountPct }}%</span>
        @endif

        {{-- Low stock / out of stock --}}
        @if($isLowStock)
            <span class="absolute top-0 right-0 bg-red-500 text-white text-[10px] font-bold px-2.5 py-1 tracking-wider uppercase">Only {{ $product->stock }} left</span>
        @elseif($isOutOfStock)
            <span class="absolute top-0 left-0 right-0 text-center bg-black/60 text-white text-xs font-bold py-2 tracking-widest uppercase">Sold Out</span>
        @endif

        {{-- Wishlist heart — always visible on mobile --}}
        @if(!$isOutOfStock)
        <button @click.prevent="toggle()" :disabled="loading"
                :class="wishlisted
                    ? 'text-red-500 bg-white/90 border-red-200'
                    : 'text-gray-500 bg-white/90 border-gray-200 hover:text-red-500'"
                class="absolute bottom-3 right-3 w-8 h-8 border flex items-center justify-center
                       transition-all duration-200 active:scale-90 disabled:opacity-50 z-10
                       opacity-100 md:opacity-0 md:group-hover:opacity-100 md:translate-y-2 md:group-hover:translate-y-0">
            <svg class="w-4 h-4" :fill="wishlisted ? 'currentColor' : 'none'" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                      d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
            </svg>
        </button>
        @endif
    </a>

    {{-- Product info --}}
    <div class="p-3 sm:p-4 flex flex-col flex-1 border-t border-gray-100 group-hover:border-[#C9A84C]/20 transition-colors">
        <p class="text-[10px] font-bold tracking-[0.2em] uppercase text-[#C9A84C] mb-1">{{ $product->category }}</p>
        <a href="{{ route('products.show', $product->id) }}" class="block flex-1 mb-3">
            <h3 class="text-xs sm:text-sm font-semibold text-[#1A1A1A] hover:text-[#C9A84C] transition-colors line-clamp-2 leading-snug tracking-wide">{{ $product->name }}</h3>
        </a>

        {{-- Price --}}
        <div class="flex items-center gap-2 mb-3">
            <span class="text-sm sm:text-base font-bold text-[#1A1A1A]">৳{{ number_format($displayPrice) }}</span>
            @if($hasDiscount)
                <span class="text-xs text-gray-400 line-through">৳{{ number_format($product->price) }}</span>
            @endif
        </div>

        {{-- Add to cart --}}
        @if($isOutOfStock)
            <button disabled class="w-full border border-gray-200 text-gray-400 text-xs font-semibold tracking-widest uppercase py-2.5 cursor-not-allowed">
                Sold Out
            </button>
        @else
            <form method="POST" action="{{ route('cart.add') }}">
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
                        class="w-full bg-[#1A1A1A] hover:bg-[#C9A84C] text-white text-[10px] sm:text-xs font-bold tracking-widest uppercase py-2.5 transition-all duration-300 active:scale-95">
                    Add to Cart
                </button>
            </form>
        @endif
    </div>
</div>
