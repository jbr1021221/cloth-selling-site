@extends('layouts.app')
@section('title', $product->name)

@section('og_meta')
    <meta property="og:title" content="{{ $product->name }} — ClothStore">
    <meta property="og:description" content="{{ Str::limit(strip_tags($product->description), 150) }}">
    <meta property="og:image" content="{{ is_array($product->images) && count($product->images) > 0 ? asset($product->images[0]) : asset('images/default.png') }}">
    <meta property="og:url" content="{{ url()->current() }}">
    <meta property="og:type" content="product">
    <meta property="product:price:amount" content="{{ $product->getCurrentPrice() }}">
    <meta property="product:price:currency" content="BDT">
@endsection

@section('content')
@php
    $isWishlisted = auth()->check()
        ? \App\Models\Wishlist::where('user_id', auth()->id())->where('product_id', $product->id)->exists()
        : false;
        
    $displayPrice = $product->getCurrentPrice();
    $hasDiscount  = $product->getHasDiscount();
    $isNew        = $product->created_at ? $product->created_at->diffInDays(now()) <= 7 : false;
    $isOutOfStock = $product->stock === 0;
    
    $images = is_array($product->images) && count($product->images) > 0
        ? $product->images
        : ['https://images.unsplash.com/photo-1523381210434-271e8be1f52b?q=80&w=800&auto=format&fit=crop'];
@endphp

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10 sm:py-16 bg-white shrink-0">

    {{-- Breadcrumb --}}
    <nav class="flex items-center gap-2 text-[10px] font-bold tracking-[0.2em] uppercase text-gray-400 mb-10 w-full">
        <a href="{{ route('home') }}" class="hover:text-[#1A1A1A] transition-colors">Home</a>
        <span>/</span>
        <a href="{{ route('products.index') }}" class="hover:text-[#1A1A1A] transition-colors">Shop</a>
        <span>/</span>
        <a href="{{ route('products.index', ['category' => $product->category]) }}" class="hover:text-[#1A1A1A] transition-colors">{{ $product->category }}</a>
        <span>/</span>
        <span class="text-[#1A1A1A]">{{ Str::limit($product->name, 30) }}</span>
    </nav>

    <div class="grid lg:grid-cols-2 gap-12 lg:gap-20 mb-20">

        {{-- ── 1. LEFT SIDE: Image Gallery ─────────────────────────────── --}}
        <div x-data="{ activeImg: '{{ $images[0] }}' }" class="flex flex-col gap-4">
            {{-- Main Image --}}
            <div class="relative w-full bg-[#F8F8F8] border border-gray-100 flex items-center justify-center overflow-hidden" style="aspect-ratio: 3/4;">
                <img :src="activeImg" alt="{{ $product->name }}" class="w-full h-full object-cover transition-opacity duration-300">
                
                {{-- NEW Badge --}}
                @if($isNew && !$isOutOfStock)
                    <div class="absolute top-4 right-4 border border-[#C9A84C] text-[#C9A84C] bg-white/90 text-[10px] font-bold tracking-widest uppercase px-3 py-1">
                        New Arrival
                    </div>
                @endif
                
                {{-- Out of Stock Overlay --}}
                @if($isOutOfStock)
                <div class="absolute inset-0 bg-white/50 backdrop-blur-sm flex items-center justify-center">
                    <span class="border border-[#1A1A1A] bg-white px-6 py-3 text-[#1A1A1A] text-sm font-bold tracking-[0.3em] uppercase">Sold Out</span>
                </div>
                @endif
            </div>

            {{-- Thumbnails --}}
            @if(count($images) > 1)
            <div class="grid grid-cols-4 gap-3">
                @foreach($images as $img)
                <button type="button" @click="activeImg = '{{ $img }}'"
                        class="relative w-full aspect-[3/4] bg-[#F8F8F8] border transition-all duration-300 overflow-hidden"
                        :class="activeImg === '{{ $img }}' ? 'border-[#C9A84C]' : 'border-gray-100 hover:border-gray-300 opacity-60 hover:opacity-100'">
                    <img src="{{ $img }}" alt="Thumbnail" class="w-full h-full object-cover">
                </button>
                @endforeach
            </div>
            @endif
        </div>


        {{-- ── 2. RIGHT SIDE: Product Info ──────────────────────────────── --}}
        @php
            $productVariants = $product->variants;
            $uniqueSizes = $productVariants->pluck('size')->filter()->unique()->values();
            $uniqueColors = $productVariants->pluck('color')->filter()->unique()->values();
            $basePrice = $hasDiscount ? $displayPrice : $product->price;
        @endphp

        <div class="flex flex-col pt-2" x-data="{
                 wishlisted: {{ $isWishlisted ? 'true' : 'false' }},
                 loadingWishlist: false,
                 qty: 1,
                 
                 // Variant system
                 variants: @json($productVariants),
                 selectedSize: '{{ $uniqueSizes->first() ?? '' }}',
                 selectedColor: '{{ $uniqueColors->first() ?? '' }}',
                 
                 get activeVariant() {
                     if (this.variants.length === 0) return null;
                     return this.variants.find(v => (v.size || '') == this.selectedSize && (v.color || '') == this.selectedColor) || null;
                 },
                 get currentPrice() {
                     let base = parseFloat({{ $basePrice }}) || 0;
                     if (this.activeVariant) {
                         base += parseFloat(this.activeVariant.price_modifier) || 0;
                     }
                     return base.toLocaleString('en-US', { minimumFractionDigits: 0, maximumFractionDigits: 2 });
                 },
                 get maxStock() {
                     if (this.variants.length > 0) {
                         return this.activeVariant ? this.activeVariant.stock : 0;
                     }
                     return {{ $product->stock }};
                 },
                 get isOutOfStock() {
                     if (this.variants.length > 0) return this.maxStock < 1;
                     return {{ $isOutOfStock ? 'true' : 'false' }};
                 },

                 async toggleWishlist() {
                     if(this.loadingWishlist) return;
                     @auth
                     this.loadingWishlist = true;
                     try {
                         const res = await fetch('{{ route('wishlist.toggle', $product->id) }}', {
                             method:'POST', headers:{'X-CSRF-TOKEN':'{{ csrf_token() }}','Accept':'application/json'}
                         });
                         const d = await res.json();
                         this.wishlisted = d.wishlisted;
                     } finally { this.loadingWishlist = false; }
                     @else
                     window.location = '{{ route('login') }}';
                     @endauth
                 }
             }" x-init="if (variants.length > 0 && maxStock > 0 && qty > maxStock) qty = maxStock; $watch('maxStock', val => { if(qty > val) qty = Math.max(1, val); })">

            <p class="text-[10px] font-bold tracking-[0.2em] uppercase text-[#C9A84C] mb-2">{{ $product->category }}</p>
            
            @if($product->activeFlashSale && !$isOutOfStock)
                <div class="inline-flex items-center gap-2 bg-red-600 text-white text-[10px] font-bold tracking-[0.2em] uppercase px-3 py-1 mb-3">
                    <svg class="w-3 h-3 text-white" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M11.3 1.046A1 1 0 0112 2v5h4a1 1 0 01.82 1.573l-7 10A1 1 0 018 18v-5H4a1 1 0 01-.82-1.573l7-10a1 1 0 011.12-.38z" clip-rule="evenodd"></path></svg>
                    Flash Sale
                </div>
            @endif

            <h1 class="text-2xl sm:text-3xl lg:text-4xl font-extrabold uppercase tracking-wide text-[#1A1A1A] mb-4 leading-tight">{{ $product->name }}</h1>

            {{-- Star Rating Row --}}
            <div class="flex items-center gap-3 mb-6">
                <div class="flex items-center gap-1">
                    @for($s = 1; $s <= 5; $s++)
                        <svg class="w-4 h-4 {{ $s <= round($avgRating ?? 0) ? 'text-[#C9A84C]' : 'text-gray-200' }}" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                    @endfor
                </div>
                <span class="text-[10px] uppercase tracking-widest text-[#1A1A1A] font-bold">{{ $ratingCount > 0 ? number_format($avgRating, 1) : 'NEW' }}</span>
                @if($ratingCount > 0)
                <a href="#details-tabs" class="text-[10px] uppercase tracking-widest text-gray-400 hover:text-[#C9A84C] transition-colors underline underline-offset-4">Read {{ $ratingCount }} Reviews</a>
                @endif
            </div>

            {{-- Dynamic Price Row --}}
            <div class="flex items-baseline gap-3 mb-6">
                {{-- Show PHP price immediately; Alpine updates it only when variant modifier changes --}}
                <span class="text-2xl sm:text-3xl font-bold text-[#C9A84C]">৳<span x-text="currentPrice">{{ number_format((float)($basePrice ?? $product->price), 0) }}</span></span>
                @if($hasDiscount)
                    <span class="text-base sm:text-lg text-gray-400 line-through font-normal">৳{{ number_format((float)$product->price, 0) }}</span>
                    @php
                        $discountPct = $product->price > 0 ? round((($product->price - $displayPrice) / $product->price) * 100) : 0;
                    @endphp
                    @if($discountPct > 0)
                        <span class="text-[10px] font-bold bg-red-500 text-white px-2 py-0.5 tracking-widest uppercase">-{{ $discountPct }}%</span>
                    @endif
                @endif
            </div>

            {{-- Gold Divider --}}
            <div class="h-px w-16 bg-[#C9A84C] mb-6"></div>

            {{-- Short Description --}}
            <p class="text-sm text-gray-500 font-light tracking-wide leading-relaxed mb-8">
                {{ Str::limit($product->description, 180) }}
            </p>

            {{-- Dynamic Form: PHP @if guard so the form is ALWAYS rendered on in-stock products --}}
            @if(!$isOutOfStock)
            <form method="POST" action="{{ route('cart.add') }}" class="space-y-8">
                @csrf
                <input type="hidden" name="product_id" value="{{ $product->id }}">
                {{-- variant_id: uses Alpine :value but also has a safe empty default --}}
                <input type="hidden" name="variant_id" id="variant_id_input" value="" :value="activeVariant ? activeVariant.id : ''">

                {{-- Size Selector --}}
                @if(count($uniqueSizes) > 0)
                <div>
                    <div class="flex justify-between items-end mb-3">
                        <label class="text-[10px] font-bold uppercase tracking-[0.2em] text-[#1A1A1A]">Size</label>
                        <a href="#details-tabs" class="text-[10px] uppercase tracking-widest text-gray-400 hover:text-[#C9A84C] underline underline-offset-4">Size Guide</a>
                    </div>
                    <div class="flex flex-wrap gap-3">
                        @foreach($uniqueSizes as $size)
                        <label class="cursor-pointer relative">
                            <input type="radio" name="size" value="{{ $size }}" x-model="selectedSize" class="peer sr-only">
                            <div class="w-12 h-12 flex items-center justify-center border text-[11px] font-bold tracking-wider transition-all border-gray-200 text-[#1A1A1A] peer-checked:border-[#C9A84C] peer-checked:text-[#C9A84C] peer-checked:border-2 hover:border-[#1A1A1A]">
                                {{ $size }}
                            </div>
                        </label>
                        @endforeach
                    </div>
                </div>
                @endif

                {{-- Color Selector (Circles) --}}
                @if(count($uniqueColors) > 0)
                <div>
                    <label class="text-[10px] font-bold uppercase tracking-[0.2em] text-[#1A1A1A] mb-3 block">Color</label>
                    <div class="flex flex-wrap gap-4">
                        @foreach($uniqueColors as $color)
                        @php
                            $bg = strtolower($color);
                            $cssColor = in_array($bg, ['white','black','red','blue','green','yellow','gray','pink','purple','navy'])
                                ? ($bg === 'navy' ? '#000080' : $bg) : '#'.$color;
                        @endphp
                        <label class="cursor-pointer flex items-center gap-2 group">
                            <input type="radio" name="color" value="{{ $color }}" x-model="selectedColor" class="peer sr-only">
                            <div class="w-8 h-8 rounded-full border border-gray-200 flex items-center justify-center transition-all peer-checked:ring-2 peer-checked:ring-[#C9A84C] peer-checked:ring-offset-2">
                                <span class="w-6 h-6 rounded-full block border border-black/10" style="background-color: {{ strtolower($color) }};"></span>
                            </div>
                            <span class="text-[10px] font-bold uppercase tracking-widest text-[#1A1A1A] opacity-0 group-hover:opacity-100 peer-checked:opacity-100 transition-opacity">{{ $color }}</span>
                        </label>
                        @endforeach
                    </div>
                </div>
                @endif

                {{-- Low Stock Indicator --}}
                <div x-show="maxStock > 0 && maxStock <= 5" x-cloak class="flex items-center gap-2 text-red-500 bg-red-50 p-3 text-[10px] font-bold uppercase tracking-widest border border-red-100">
                    ⚠️ Only <span x-text="maxStock"></span> Left in this Variant!
                </div>

                <div x-show="variants.length > 0 && !activeVariant" x-cloak class="flex items-center gap-2 text-gray-500 bg-gray-50 p-3 text-[10px] font-bold uppercase tracking-widest border border-gray-200">
                    ❌ This combination is currently unavailable.
                </div>

                {{-- Quantity Selector: always rendered so input is always submitted --}}
                <div>
                    <label class="text-[10px] font-bold uppercase tracking-[0.2em] text-[#1A1A1A] mb-3 block">Quantity</label>
                    <div class="flex items-center w-28 border border-gray-200 h-12">
                        <button type="button" @click="qty = Math.max(1, qty - 1)" class="w-10 h-full flex items-center justify-center text-gray-500 hover:text-[#C9A84C] transition-colors text-lg font-light">−</button>
                        <input type="number" name="quantity" x-model="qty" value="1" min="1" :max="maxStock || 99"
                               class="flex-1 h-full text-center bg-transparent text-[#1A1A1A] font-bold text-sm focus:outline-none">
                        <button type="button" @click="qty = Math.min(maxStock || 99, qty + 1)" class="w-10 h-full flex items-center justify-center text-gray-500 hover:text-[#C9A84C] transition-colors text-lg font-light">+</button>
                    </div>
                </div>

                <div class="pt-4 space-y-3">
                    {{-- Add To Cart Button --}}
                    <button type="submit" name="buy_now" value="0"
                            :disabled="variants.length > 0 && !activeVariant"
                            class="w-full bg-[#C9A84C] hover:bg-[#b08a38] disabled:bg-gray-300 disabled:cursor-not-allowed text-white text-[11px] font-bold tracking-[0.2em] uppercase py-4 transition-colors">
                        Add To Cart
                    </button>

                    {{-- BUY NOW Button --}}
                    <button type="submit" name="buy_now" value="1"
                            :disabled="variants.length > 0 && !activeVariant"
                            class="w-full bg-[#1A1A1A] hover:bg-black disabled:bg-gray-300 disabled:cursor-not-allowed text-white text-[11px] font-bold tracking-[0.2em] uppercase py-4 transition-colors flex items-center justify-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                        Buy Now
                    </button>

                    {{-- Add To Wishlist Button --}}
                    <button type="button" @click="toggleWishlist()" :disabled="loadingWishlist"
                            class="w-full border border-[#C9A84C] bg-white text-[#C9A84C] hover:bg-[#C9A84C] hover:text-white text-[11px] font-bold tracking-[0.2em] uppercase py-4 transition-all flex items-center justify-center gap-2">
                        <svg class="w-4 h-4" :fill="wishlisted ? 'currentColor' : 'none'" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/></svg>
                        <span x-text="wishlisted ? 'Saved To Wishlist' : 'Add To Wishlist'"></span>
                    </button>
                </div>
            </form>
            @else
            {{-- OUT OF STOCK --}}
            <div class="pt-6 border-t border-gray-100">
                <div class="bg-gray-50 border border-gray-200 p-6 text-center">
                    <p class="text-xs font-bold uppercase tracking-widest text-[#1A1A1A] mb-2">Out of Stock</p>
                    <p class="text-[10px] text-gray-500 uppercase tracking-widest">This item is currently unavailable. Please check back later.</p>
                </div>
            </div>
            @endif

            {{-- Trust & Delivery Row --}}
            <div class="mt-10 border-t border-gray-100 pt-6 space-y-4">
                <div class="flex items-center gap-6 justify-center sm:justify-start flex-wrap">
                    <div class="flex items-center gap-2 text-[10px] font-bold uppercase tracking-widest text-gray-500">
                        <span class="text-lg">🚚</span> Fast Delivery
                    </div>
                    @if(\App\Models\Setting::get('cod_enabled', '1') == '1')
                    <div class="flex items-center gap-2 text-[10px] font-bold uppercase tracking-widest text-gray-500">
                        {{ \App\Models\Setting::get('cod_badge_text', '✅ COD Available') }}
                    </div>
                    @endif
                    <div class="flex items-center gap-2 text-[10px] font-bold uppercase tracking-widest text-gray-500">
                        <span class="text-lg">🔄</span> Returns
                    </div>
                </div>
                
                @if(\App\Models\Setting::get('show_delivery_estimate', '1') == '1')
                <div class="bg-[#F8F8F8] border border-gray-100 px-4 py-3 text-center sm:text-left">
                    <p class="text-[10px] font-bold uppercase tracking-widest text-[#1A1A1A] mb-1">Estimated Delivery Time</p>
                    <p class="text-[11px] text-gray-500 font-light tracking-wider">Dhaka: {{ \App\Models\Setting::get('estimated_delivery_dhaka', '1-2 days') }} | Outside: {{ \App\Models\Setting::get('estimated_delivery_outside', '3-5 days') }}</p>
                </div>
                @endif
            </div>

            {{-- 📱 Social Share & Earn --}}
            <div class="mt-8 pt-6 border-t border-gray-100" x-data="{
                copied: false,
                shareUrl: '{{ url()->current() }}{{ auth()->check() ? '?ref='.auth()->id() : '' }}',
                shareText: `Check out this product: {{ $product->name }}\nPrice: ৳{{ number_format($product->getCurrentPrice()) }}\n{{ url()->current() }}{{ auth()->check() ? '?ref='.auth()->id() : '' }}\nShop at ClothStore`,
                
                copyLink() {
                    navigator.clipboard.writeText(this.shareUrl);
                    this.copied = true;
                    setTimeout(() => this.copied = false, 2000);
                }
            }">
                <p class="text-[10px] font-bold tracking-widest uppercase text-[#1A1A1A] mb-3 flex items-center justify-between">
                    <span>Share This Product</span>
                    @auth
                    <span class="text-[#C9A84C] text-[9px] bg-[#FFFBF0] px-2 py-0.5 border border-[#C9A84C]/30 flex items-center gap-1">
                        <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path d="M10 18a8 8 0 100-16 8 8 0 000 16zM9.555 7.168A1 1 0 008 8v4a1 1 0 001.555.832l3-2a1 1 0 000-1.664l-3-2z"/></svg> 
                        Share & Earn 5 Pts
                    </span>
                    @endauth
                </p>
                
                <div class="flex items-center gap-3">
                    {{-- WhatsApp Share --}}
                    <a :href="`https://wa.me/?text=${encodeURIComponent(shareText + '\n\n' + shareUrl)}`" target="_blank" class="w-10 h-10 rounded-full border border-[#25D366] text-[#25D366] hover:bg-[#25D366] hover:text-white flex items-center justify-center transition-colors">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M12.031 0C5.385 0 0 5.385 0 12.031c0 2.646.685 5.146 1.954 7.377L.586 24l4.754-1.354C7.546 23.738 9.746 24 12.031 24 18.677 24 24 18.615 24 11.969 24 5.323 18.677 0 12.031 0zm7.169 16.954c-.308.862-1.723 1.631-2.4 1.769-.646.123-1.462.246-4.631-1.077-4.015-1.677-6.585-5.831-6.785-6.092-.2-.277-1.615-2.154-1.615-4.108 0-1.954 1.015-2.923 1.369-3.292.354-.369.754-.462 1.015-.462.246 0 .508 0 .723.015.231.015.538-.092.831.646.308.769 1.046 2.585 1.138 2.769.092.2.169.415.046.662-.123.231-.185.385-.369.6-.185.215-.385.446-.554.631-.185.2-.385.415-.169.8.215.369.954 1.585 2.062 2.569 1.431 1.262 2.615 1.662 3.015 1.846.385.185.615.154.846-.108.231-.262.985-1.154 1.246-1.554.262-.4.523-.323.877-.2.354.123 2.246 1.062 2.631 1.246.385.2.646.292.738.462.092.154.092.892-.215 1.769z"/></svg>
                    </a>
                    
                    {{-- Facebook Share --}}
                    <a :href="`https://www.facebook.com/sharer/sharer.php?u=${encodeURIComponent(shareUrl)}`" target="_blank" class="w-10 h-10 rounded-full border border-[#1877F2] text-[#1877F2] hover:bg-[#1877F2] hover:text-white flex items-center justify-center transition-colors">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.469h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.469h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/></svg>
                    </a>

                    {{-- Copy Link --}}
                    <button type="button" @click="copyLink" class="relative w-10 h-10 rounded-full border border-gray-300 text-gray-500 hover:bg-gray-100 flex items-center justify-center transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"/></svg>
                        <span x-show="copied" x-cloak class="absolute -top-8 left-1/2 -translate-x-1/2 bg-[#1A1A1A] text-white text-[9px] font-bold uppercase tracking-widest px-2 py-1 rounded whitespace-nowrap">Copied!</span>
                    </button>
                    
                    @guest
                    <div class="ml-2">
                        <p class="text-[9px] text-gray-400">Log in to earn points on shares.</p>
                    </div>
                    @endguest
                </div>
            </div>
        </div>
    </div>


    {{-- ── 3. BELOW TABS (Alpine.js) ────────────────────────────────────── --}}
    <div id="details-tabs" class="border-t border-gray-200 pt-16 mt-16 scroll-mt-24" x-data="{ tab: 'description' }">
        
        {{-- Tab Headers --}}
        <div class="flex items-center justify-center gap-8 sm:gap-16 border-b border-gray-100">
            <button @click="tab = 'description'" class="pb-4 text-[10px] sm:text-xs font-bold uppercase tracking-[0.2em] transition-colors relative" :class="tab === 'description' ? 'text-[#1A1A1A]' : 'text-gray-400 hover:text-[#1A1A1A]'">
                Description
                <div class="absolute bottom-0 left-0 w-full h-0.5 bg-[#C9A84C] transition-transform duration-300" x-show="tab === 'description'" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="scale-x-0" x-transition:enter-end="scale-x-100"></div>
            </button>
            <button @click="tab = 'guide'" class="pb-4 text-[10px] sm:text-xs font-bold uppercase tracking-[0.2em] transition-colors relative" :class="tab === 'guide' ? 'text-[#1A1A1A]' : 'text-gray-400 hover:text-[#1A1A1A]'">
                Size Guide
                <div class="absolute bottom-0 left-0 w-full h-0.5 bg-[#C9A84C] transition-transform duration-300" x-show="tab === 'guide'" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="scale-x-0" x-transition:enter-end="scale-x-100"></div>
            </button>
            <button @click="tab = 'reviews'" class="pb-4 text-[10px] sm:text-xs font-bold uppercase tracking-[0.2em] transition-colors relative" :class="tab === 'reviews' ? 'text-[#1A1A1A]' : 'text-gray-400 hover:text-[#1A1A1A]'">
                Reviews ({{ $ratingCount }})
                <div class="absolute bottom-0 left-0 w-full h-0.5 bg-[#C9A84C] transition-transform duration-300" x-show="tab === 'reviews'" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="scale-x-0" x-transition:enter-end="scale-x-100"></div>
            </button>
        </div>

        {{-- Tab Content --}}
        <div class="py-12 max-w-4xl mx-auto">
            
            {{-- Description Tab --}}
            <div x-show="tab === 'description'" x-cloak>
                <h3 class="playfair text-2xl font-bold text-[#1A1A1A] mb-6">Product Details</h3>
                <div class="text-sm text-gray-500 font-light tracking-wide leading-relaxed space-y-4">
                    <p>{{ $product->description }}</p>
                    @if($product->sku)
                        <p class="pt-4 mt-6 border-t border-gray-100"><strong class="uppercase text-[10px] tracking-widest text-[#1A1A1A]">SKU:</strong> {{ $product->sku }}</p>
                    @endif
                    <p><strong class="uppercase text-[10px] tracking-widest text-[#1A1A1A]">Category:</strong> {{ $product->category }}</p>
                </div>
            </div>

            {{-- Size Guide Tab --}}
            <div x-show="tab === 'guide'" x-cloak>
                <h3 class="playfair text-2xl font-bold text-[#1A1A1A] mb-6">Measurement Guide</h3>
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse text-sm text-gray-600 font-light tracking-wide">
                        <thead>
                            <tr class="border-b border-gray-200">
                                <th class="py-4 text-[10px] font-bold uppercase tracking-widest text-[#1A1A1A]">Size</th>
                                <th class="py-4 text-[10px] font-bold uppercase tracking-widest text-[#1A1A1A]">Chest (Inches)</th>
                                <th class="py-4 text-[10px] font-bold uppercase tracking-widest text-[#1A1A1A]">Length (Inches)</th>
                                <th class="py-4 text-[10px] font-bold uppercase tracking-widest text-[#1A1A1A]">Shoulder (Inches)</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            <tr><td class="py-4 font-bold text-[#1A1A1A]">XS</td><td class="py-4">36"</td><td class="py-4">26"</td><td class="py-4">16"</td></tr>
                            <tr><td class="py-4 font-bold text-[#1A1A1A]">S</td><td class="py-4">38"</td><td class="py-4">27"</td><td class="py-4">16.5"</td></tr>
                            <tr><td class="py-4 font-bold text-[#1A1A1A]">M</td><td class="py-4">40"</td><td class="py-4">28"</td><td class="py-4">17"</td></tr>
                            <tr><td class="py-4 font-bold text-[#1A1A1A]">L</td><td class="py-4">42"</td><td class="py-4">29"</td><td class="py-4">18"</td></tr>
                            <tr><td class="py-4 font-bold text-[#1A1A1A]">XL</td><td class="py-4">44"</td><td class="py-4">30"</td><td class="py-4">19"</td></tr>
                            <tr><td class="py-4 font-bold text-[#1A1A1A]">XXL</td><td class="py-4">46"</td><td class="py-4">31"</td><td class="py-4">20"</td></tr>
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- Reviews Tab --}}
            <div x-show="tab === 'reviews'" x-cloak class="grid md:grid-cols-3 gap-12">
                {{-- Write a review side --}}
                <div class="md:col-span-1">
                    @auth
                        @if($userReview)
                            <div class="bg-[#F8F8F8] border border-gray-100 p-6 text-center">
                                <h4 class="text-[10px] font-bold uppercase tracking-[0.2em] text-[#1A1A1A] mb-3">Review Submitted</h4>
                                <div class="flex justify-center items-center gap-1 mb-2">
                                    @for($s = 1; $s <= 5; $s++)
                                        <svg class="w-3.5 h-3.5 {{ $s <= $userReview->rating ? 'text-[#C9A84C]' : 'text-gray-200' }}" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                                    @endfor
                                </div>
                                <p class="text-[11px] font-light text-gray-500 uppercase tracking-widest">Thank you</p>
                            </div>
                        @else
                            <div class="bg-[#F8F8F8] border border-gray-100 p-6" x-data="{ r: 0, h: 0 }">
                                <h4 class="text-[10px] font-bold uppercase tracking-[0.2em] text-[#1A1A1A] mb-4">Write a Review</h4>
                                <form method="POST" action="{{ route('reviews.store', $product->id) }}" enctype="multipart/form-data">
                                    @csrf
                                    <div class="mb-4">
                                        <label class="text-[9px] font-bold uppercase tracking-widest text-[#1A1A1A] mb-1 block">Rating</label>
                                        <div class="flex items-center gap-1">
                                            @for($s = 1; $s <= 5; $s++)
                                            <button type="button" @mouseenter="h = {{ $s }}" @mouseleave="h = 0" @click="r = {{ $s }}">
                                                <svg class="w-5 h-5 transition-colors" :class="(h || r) >= {{ $s }} ? 'text-[#C9A84C]' : 'text-gray-200'" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                                            </button>
                                            @endfor
                                        </div>
                                        <input type="hidden" name="rating" :value="r" required>
                                    </div>
                                    <div class="mb-4">
                                        <label class="text-[9px] font-bold uppercase tracking-widest text-[#1A1A1A] mb-1 block">Title</label>
                                        <input type="text" name="title" class="w-full border-b border-gray-300 bg-transparent py-2 text-sm focus:outline-none focus:border-[#C9A84C]" required>
                                    </div>
                                    <div class="mb-4">
                                        <label class="text-[9px] font-bold uppercase tracking-widest text-[#1A1A1A] mb-1 block">Review</label>
                                        <textarea name="body" rows="3" class="w-full border-b border-gray-300 bg-transparent py-2 text-sm focus:outline-none focus:border-[#C9A84C] resize-none" required></textarea>
                                    </div>
                                    <div class="mb-6">
                                        <label class="text-[9px] font-bold uppercase tracking-widest text-[#1A1A1A] mb-1 block">Upload Photos (Max 3)</label>
                                        <input type="file" name="images[]" multiple accept="image/*" class="w-full text-xs text-gray-500 file:mr-4 file:py-2 file:px-4 file:border-0 file:text-[9px] file:font-bold file:uppercase file:tracking-widest file:bg-[#1A1A1A] file:text-white hover:file:bg-[#333] cursor-pointer bg-white border border-gray-200">
                                    </div>
                                    <button type="submit" :disabled="r === 0" class="w-full bg-[#1A1A1A] text-white py-3 text-[10px] font-bold uppercase tracking-widest disabled:opacity-50">Publish</button>
                                </form>
                            </div>
                        @endif
                    @else
                        <div class="bg-[#F8F8F8] border border-gray-100 p-8 text-center text-sm text-gray-500 font-light">
                            <p class="mb-4">Sign in to leave a review.</p>
                            <a href="{{ route('login') }}" class="btn-outline-dark inline-block px-8 py-3 w-full">Sign In</a>
                        </div>
                    @endauth
                </div>

                {{-- Review lists --}}
                <div class="md:col-span-2 space-y-6">
                    @forelse($reviews as $review)
                        <div class="border-b border-gray-100 pb-6">
                            <div class="flex items-center justify-between mb-2">
                                <p class="text-[11px] font-bold uppercase tracking-widest text-[#1A1A1A]">{{ $review->user->name }}</p>
                                <p class="text-[10px] text-gray-400 font-light tracking-wide">{{ $review->created_at->format('M d, Y') }}</p>
                            </div>
                            <div class="flex items-center gap-1 mb-3">
                                @for($s = 1; $s <= 5; $s++)
                                    <svg class="w-3.5 h-3.5 {{ $s <= $review->rating ? 'text-[#C9A84C]' : 'text-gray-200' }}" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                                @endfor
                            </div>
                            @if($review->title)
                                <h5 class="text-xs font-bold text-[#1A1A1A] mb-1.5">{{ $review->title }}</h5>
                            @endif
                            @if($review->body)
                                <p class="text-sm text-gray-600 font-light leading-relaxed mb-3">{{ $review->body }}</p>
                            @endif
                            
                            {{-- Render images if present in review --}}
                            @if($review->images && is_array($review->images) && count($review->images) > 0)
                                <div class="flex gap-2 mt-3">
                                    @foreach($review->images as $revImg)
                                        <a href="{{ $revImg }}" target="_blank" class="block w-16 h-20 border border-gray-200 overflow-hidden hover:border-[#C9A84C] transition-colors relative group">
                                            <div class="absolute inset-0 bg-black/20 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center">
                                                <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0zM10 7v3m0 0v3m0-3h3m-3 0H7"/></svg>
                                            </div>
                                            <img src="{{ $revImg }}" alt="Customer photo" class="w-full h-full object-cover">
                                        </a>
                                    @endforeach
                                </div>
                            @endif

                            @auth
                                @if($review->user_id === auth()->id())
                                <form method="POST" action="{{ route('reviews.destroy', $review->id) }}" class="mt-4">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="text-[9px] font-bold tracking-[0.2em] uppercase text-gray-400 hover:text-red-500 transition-colors">Delete Request</button>
                                </form>
                                @endif
                            @endauth
                        </div>
                    @empty
                        <div class="text-center py-10 bg-[#F8F8F8] border border-gray-100">
                            <p class="text-[11px] font-bold uppercase tracking-[0.2em] text-[#1A1A1A] mb-2">No Reviews Yet</p>
                            <p class="text-sm font-light text-gray-500 tracking-wide">Be the first to share your thoughts.</p>
                        </div>
                    @endforelse
                </div>

                {{-- Real Customer Photos Gallery (extracted) --}}
                @php
                    $allReviewPhotos = collect([]);
                    foreach($reviews as $r) {
                        if(is_array($r->images)) {
                            foreach($r->images as $photo) { $allReviewPhotos->push($photo); }
                        }
                    }
                @endphp
                @if($allReviewPhotos->count() > 0)
                <div class="md:col-span-3 mt-10">
                    <h3 class="playfair text-xl font-bold text-[#1A1A1A] mb-4 border-l-2 border-[#C9A84C] pl-3">Real Customer Photos</h3>
                    <div class="flex overflow-x-auto gap-4 scrollbar-hide pb-4 snap-x">
                        @foreach($allReviewPhotos->take(12) as $photoSrc)
                            <a href="{{ $photoSrc }}" target="_blank" class="w-32 h-40 shrink-0 snap-start bg-[#F8F8F8] border border-gray-200 overflow-hidden group relative">
                                <div class="absolute inset-0 bg-black/40 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center pointer-events-none">
                                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0zM2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                </div>
                                <img src="{{ $photoSrc }}" class="w-full h-full object-cover">
                            </a>
                        @endforeach
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>

    {{-- ── 4. RELATED PRODUCTS ─────────────────────────────────────────── --}}
    @if($relatedProducts && count($relatedProducts) > 0)
    <div class="mt-20 pt-16 border-t border-gray-200">
        <div class="text-center mb-10">
            <h2 class="section-heading gold-underline inline-block">You May Also Like</h2>
        </div>
        
        <div class="flex overflow-x-auto snap-x snap-mandatory sm:grid sm:grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-6 pb-4 sm:pb-0 scrollbar-hide">
            @foreach($relatedProducts as $related)
                <div class="w-64 sm:w-auto shrink-0 snap-start">
                    @include('partials.product-card', ['product' => $related])
                </div>
            @endforeach
        </div>
    </div>
    @endif
</div>

<style>
/* Hides Alpine.js elements before they load */
[x-cloak] { display: none !important; }
</style>

@endsection
