@php
    $displayPrice = $product->getCurrentPrice();
    $hasDiscount  = $product->getHasDiscount();
    $discountPct  = $hasDiscount ? round((($product->price - $displayPrice) / $product->price) * 100) : 0;
    
    // Check if added in last 7 days for "NEW" badge
    $isNew = $product->created_at ? $product->created_at->diffInDays(now()) <= 7 : false;

    $image = is_array($product->images) && count($product->images) > 0
        ? $product->images[0]
        : 'https://images.unsplash.com/photo-1523381210434-271e8be1f52b?q=80&w=500&auto=format&fit=crop';
    
    $isOutOfStock = $product->stock === 0;
    
    $isWishlisted = auth()->check()
        ? \App\Models\Wishlist::where('user_id', auth()->id())->where('product_id', $product->id)->exists()
        : false;
@endphp

<div class="group relative bg-white flex flex-col hover:shadow-sm" style="height: 100%; box-shadow: 0 2px 4px rgba(0,0,0,0.02);"
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

    {{-- Image Area (70% relative height styling applied below via styling classes) --}}
    <a href="{{ route('products.show', $product->id) }}" class="relative w-full overflow-hidden block bg-[#F8F8F8] border border-transparent group-hover:border-[#C9A84C] transition-colors duration-300" style="aspect-ratio: 3/4;">
        
        <img src="{{ $image }}" alt="{{ $product->name }}"
             class="w-full h-full object-cover transition-transform duration-700 ease-out group-hover:scale-[1.03] {{ $isOutOfStock ? 'grayscale opacity-70' : '' }}">

        {{-- Top Right Badges (Wishlist & NEW) --}}
        <div class="absolute top-2 right-2 flex flex-col gap-2 items-end">
            {{-- Wishlist Heart (Appears on hover) --}}
            <button @click.prevent="toggle()" :disabled="loading"
                    :class="wishlisted ? 'text-red-500 opacity-100' : 'text-[#1A1A1A] opacity-0 group-hover:opacity-100'"
                    class="p-2 transition-all duration-300">
                <svg class="w-5 h-5" :fill="wishlisted ? 'currentColor' : 'none'" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.2"
                          d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
                </svg>
            </button>
            
            {{-- NEW Badge --}}
            @if($isNew && !$isOutOfStock)
                <span class="border border-[#C9A84C] text-[#C9A84C] text-[9px] font-bold px-2 py-0.5 tracking-widest uppercase bg-white/80 backdrop-blur-sm mr-2">
                    NEW
                </span>
            @endif
        </div>

        {{-- Top Left Badge (Discount / Flash Sale) --}}
        @if($product->activeFlashSale && !$isOutOfStock)
            <div class="absolute top-3 left-3 z-10">
                <span class="bg-red-600 text-white text-[9px] font-bold px-3 py-1 tracking-wider uppercase rounded-full shadow-sm flex items-center gap-1">
                    <svg class="w-3 h-3 text-white" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M11.3 1.046A1 1 0 0112 2v5h4a1 1 0 01.82 1.573l-7 10A1 1 0 018 18v-5H4a1 1 0 01-.82-1.573l7-10a1 1 0 011.12-.38z" clip-rule="evenodd"></path></svg>
                    SALE
                </span>
            </div>
        @elseif($hasDiscount && !$isOutOfStock)
            <div class="absolute top-3 left-3 z-10">
                <span class="bg-[#C9A84C] text-white text-[9px] font-bold px-3 py-1 tracking-wider uppercase rounded-full">
                    -{{ $discountPct }}%
                </span>
            </div>
        @endif

        {{-- Add To Cart Overlay (Slides up on hover) --}}
        <div class="absolute inset-x-0 bottom-0 translate-y-full group-hover:translate-y-0 transition-transform duration-300 ease-in-out z-20">
            @if($isOutOfStock)
                <div class="w-full bg-white/90 backdrop-blur-sm text-[#1A1A1A] text-[10px] font-bold tracking-widest uppercase py-3 text-center border-t border-[#1A1A1A]">
                    SOLD OUT
                </div>
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
                    <button type="submit" class="w-full bg-[#C9A84C] text-white text-[10px] sm:text-xs font-bold tracking-[0.2em] uppercase py-3.5 hover:bg-[#b08a38] transition-colors">
                        Add To Cart
                    </button>
                </form>
            @endif
        </div>
    </a>

    {{-- Product Info (takes up the remaining space) --}}
    <div class="pt-4 flex flex-col flex-1 pb-2">
        {{-- Category Label --}}
        <p class="text-[9px] font-bold tracking-[0.2em] uppercase text-gray-400 mb-1.5">{{ $product->category }}</p>
        
        {{-- Product Name --}}
        <a href="{{ route('products.show', $product->id) }}" class="block flex-1 mb-2">
            <h3 class="text-[11px] sm:text-xs font-bold text-[#1A1A1A] uppercase tracking-widest line-clamp-2 leading-snug hover:text-[#C9A84C] transition-colors">
                {{ $product->name }}
            </h3>
        </a>

        {{-- Price Row --}}
        <div class="flex items-center gap-2 mt-auto">
            @if($hasDiscount)
                <span class="text-xs sm:text-sm font-bold text-[#C9A84C]">৳{{ number_format($displayPrice) }}</span>
                <span class="text-[10px] text-gray-400 line-through">৳{{ number_format($product->price) }}</span>
            @else
                <span class="text-xs sm:text-sm font-bold text-[#1A1A1A]">৳{{ number_format($displayPrice) }}</span>
            @endif
        </div>
    </div>

</div>
