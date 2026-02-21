@extends('layouts.app')
@section('title', $product->name)

@section('content')
@php
    $isWishlisted = auth()->check()
        ? \App\Models\Wishlist::where('user_id', auth()->id())->where('product_id', $product->id)->exists()
        : false;
@endphp
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10 sm:py-16 bg-white">

    {{-- Breadcrumb --}}
    <nav class="flex items-center gap-2 text-[10px] font-semibold tracking-[0.2em] uppercase text-gray-400 mb-8 sm:mb-12">
        <a href="{{ route('home') }}" class="hover:text-[#1A1A1A] transition-colors">Home</a>
        <span>/</span>
        <a href="{{ route('products.index') }}" class="hover:text-[#1A1A1A] transition-colors">Shop</a>
        <span>/</span>
        <a href="{{ route('products.index', ['category' => $product->category]) }}" class="hover:text-[#1A1A1A] transition-colors">{{ $product->category }}</a>
        <span>/</span>
        <span class="text-[#1A1A1A]">{{ Str::limit($product->name, 30) }}</span>
    </nav>

    <div class="grid md:grid-cols-2 gap-10 lg:gap-16 mb-24">

        {{-- ── Left: Images Gallery ─────────────────────────────── --}}
        @php
            $images = is_array($product->images) && count($product->images) > 0
                ? $product->images
                : ['https://images.unsplash.com/photo-1523381210434-271e8be1f52b?q=80&w=800&auto=format&fit=crop'];
        @endphp
        <div x-data="{ activeImg: '{{ $images[0] }}' }" class="flex flex-col md:flex-row-reverse gap-4">

            {{-- Main image (taller aspect ratio) --}}
            <div class="flex-1 bg-[#F8F8F8] border border-gray-100 overflow-hidden relative group" style="aspect-ratio: 3/4">
                <img :src="activeImg" alt="{{ $product->name }}"
                     class="w-full h-full object-cover transition-opacity duration-300">
                @if($product->stock <= 0)
                <div class="absolute inset-0 bg-white/60 flex items-center justify-center backdrop-blur-sm">
                    <span class="text-[#1A1A1A] text-sm font-bold tracking-[0.3em] uppercase border border-[#1A1A1A] px-6 py-3 bg-white/90">Sold Out</span>
                </div>
                @endif
            </div>

            {{-- Thumbnails (Vertical on desktop, horizontal on mobile) --}}
            @if(count($images) > 1)
            <div class="flex flex-row md:flex-col gap-3 overflow-x-auto md:overflow-y-auto md:w-20 lg:w-24 scrollbar-hide py-1 md:py-0">
                @foreach($images as $img)
                <button type="button" @click="activeImg = '{{ $img }}'"
                        class="flex-shrink-0 w-20 h-24 md:w-full md:h-32 bg-[#F8F8F8] border overflow-hidden transition-all duration-300"
                        :class="activeImg === '{{ $img }}' ? 'border-[#C9A84C]' : 'border-gray-100 hover:border-gray-300 opacity-60 hover:opacity-100'">
                    <img src="{{ $img }}" alt="Thumbnail" class="w-full h-full object-cover">
                </button>
                @endforeach
            </div>
            @endif
        </div>

        {{-- ── Right: Product Info ──────────────────────────────── --}}
        <div x-data="{
                 wishlisted: {{ $isWishlisted ? 'true' : 'false' }},
                 loading: false,
                 qty: 1,
                 async toggleWishlist() {
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
             }" class="flex flex-col">

            <p class="text-xs font-bold tracking-[0.2em] uppercase text-[#C9A84C] mb-2">{{ $product->category }}</p>
            <h1 class="playfair text-3xl sm:text-4xl lg:text-5xl font-bold text-[#1A1A1A] leading-tight mb-4">{{ $product->name }}</h1>

            {{-- Reviews / Stars --}}
            @if($ratingCount > 0)
            <a href="#reviews" class="inline-flex items-center gap-2 mb-6 group">
                <div class="flex items-center gap-1">
                    @for($s = 1; $s <= 5; $s++)
                        <svg class="w-4 h-4 {{ $s <= round($avgRating) ? 'text-amber-400' : 'text-gray-200' }}" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                    @endfor
                </div>
                <span class="text-xs font-bold uppercase tracking-widest text-[#1A1A1A] mt-0.5">{{ number_format($avgRating, 1) }} / 5</span>
                <span class="text-xs text-gray-400 uppercase tracking-widest mt-0.5">({{ $ratingCount }} Reviews)</span>
            </a>
            @else
            <p class="text-xs uppercase tracking-widest text-gray-400 mb-6">No reviews yet</p>
            @endif

            {{-- Price --}}
            <div class="flex items-end gap-4 mb-8">
                @if($product->discount_price && $product->discount_price < $product->price)
                    <span class="text-3xl sm:text-4xl font-extrabold text-[#1A1A1A]">৳{{ number_format($product->discount_price) }}</span>
                    <span class="text-lg text-gray-400 line-through mb-1 block">৳{{ number_format($product->price) }}</span>
                    <span class="bg-red-50 text-red-600 border border-red-100 text-[10px] font-bold tracking-widest uppercase px-2 py-1 mb-2 block">
                        -{{ round((($product->price - $product->discount_price) / $product->price) * 100) }}% Off
                    </span>
                @else
                    <span class="text-3xl sm:text-4xl font-extrabold text-[#1A1A1A]">৳{{ number_format($product->price) }}</span>
                @endif
            </div>

            {{-- Description --}}
            <p class="text-sm text-gray-600 leading-relaxed mb-8 font-light tracking-wide">{{ $product->description }}</p>

            {{-- Urgency Badge --}}
            <div class="mb-8">
                @if($product->stock === 0)
                    <div class="bg-gray-50 border border-gray-200 px-4 py-3 text-sm text-gray-600 font-semibold tracking-wider uppercase text-center">
                        Out of Stock
                    </div>
                @elseif($product->stock < 10)
                    <div class="bg-red-50 border border-red-200 px-4 py-3 text-xs text-red-600 font-bold tracking-widest uppercase flex items-center gap-2">
                        <span class="animate-pulse">🔥</span> Only {{ $product->stock }} left in stock
                    </div>
                @endif
            </div>

            {{-- Add to Cart Form --}}
            @if($product->stock > 0)
            <form method="POST" action="{{ route('cart.add') }}" class="space-y-6">
                @csrf
                <input type="hidden" name="product_id" value="{{ $product->id }}">

                <div class="grid grid-cols-2 gap-6">
                    {{-- Size --}}
                    @if(is_array($product->sizes) && count($product->sizes) > 0)
                    <div>
                        <label class="text-[10px] font-bold uppercase tracking-[0.2em] text-gray-400 mb-2 block">Size</label>
                        <select name="size" class="w-full border border-gray-300 text-[#1A1A1A] text-sm uppercase px-4 py-3 focus:border-[#C9A84C] focus:outline-none bg-transparent">
                            @foreach($product->sizes as $size)
                                <option value="{{ $size }}">{{ $size }}</option>
                            @endforeach
                        </select>
                    </div>
                    @endif

                    {{-- Color --}}
                    @if(is_array($product->colors) && count($product->colors) > 0)
                    <div>
                        <label class="text-[10px] font-bold uppercase tracking-[0.2em] text-gray-400 mb-2 block">Color</label>
                        <select name="color" class="w-full border border-gray-300 text-[#1A1A1A] text-sm uppercase px-4 py-3 focus:border-[#C9A84C] focus:outline-none bg-transparent">
                            @foreach($product->colors as $color)
                                <option value="{{ $color }}">{{ $color }}</option>
                            @endforeach
                        </select>
                    </div>
                    @endif
                </div>

                {{-- Quantity --}}
                <div>
                    <label class="text-[10px] font-bold uppercase tracking-[0.2em] text-gray-400 mb-2 block">Quantity</label>
                    <div class="flex items-center w-32 border border-gray-300">
                        <button type="button" @click="qty = Math.max(1, qty - 1)" class="w-10 h-12 flex items-center justify-center text-gray-500 hover:text-[#C9A84C] transition-colors">−</button>
                        <input type="number" name="quantity" x-model="qty" min="1" max="{{ $product->stock }}" class="flex-1 text-center h-12 bg-transparent text-[#1A1A1A] font-semibold text-sm focus:outline-none pointer-events-none">
                        <button type="button" @click="qty = Math.min({{ $product->stock }}, qty + 1)" class="w-10 h-12 flex items-center justify-center text-gray-500 hover:text-[#C9A84C] transition-colors">+</button>
                    </div>
                </div>

                {{-- Action Buttons --}}
                <div class="flex flex-col sm:flex-row gap-3 pt-4">
                    <button type="submit" class="btn-primary flex-1 py-4 text-sm bg-[#1A1A1A] hover:bg-gray-800">
                        Add to Bag
                    </button>
                    <div class="flex gap-3">
                        <button type="button" onclick="window.location='{{ route('cart.index') }}'" class="btn-outline flex-1 py-4 text-sm sm:px-8 border-gray-300 text-[#1A1A1A] hover:bg-gray-50 hover:text-[#1A1A1A] hover:border-gray-400">
                            Buy Now
                        </button>
                        {{-- Wishlist toggle --}}
                        <button type="button" @click="toggleWishlist()" :disabled="loading"
                                class="w-14 shrink-0 flex items-center justify-center border border-gray-300 hover:border-red-200 transition-colors"
                                :class="wishlisted ? 'bg-red-50 text-red-500 border-red-200' : 'bg-transparent text-gray-400 hover:text-red-500'">
                            <svg class="w-5 h-5" :fill="wishlisted ? 'currentColor' : 'none'" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/></svg>
                        </button>
                    </div>
                </div>
            </form>
            @endif

            {{-- Delivery details dropdown style --}}
            <div class="mt-10 border-t border-gray-200 pt-6">
                <div class="flex items-start gap-4 mb-4">
                    <svg class="w-5 h-5 text-gray-400 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10a1 1 0 001 1h1m8-1a1 1 0 01-1 1H9m4-1V8a1 1 0 011-1h2.586a1 1 0 01.707.293l3.414 3.414a1 1 0 01.293.707V16a1 1 0 01-1 1h-1m-6-1a1 1 0 001 1h1M5 17a2 2 0 104 0m-4 0a2 2 0 114 0m6 0a2 2 0 104 0m-4 0a2 2 0 114 0"/></svg>
                    <div>
                        <p class="text-[11px] font-bold uppercase tracking-widest text-[#1A1A1A] mb-1">Shipping & Returns</p>
                        <p class="text-xs text-gray-500 leading-relaxed mb-2">Dhaka delivery in 1-2 days (৳60). Outside Dhaka in 3-5 days (৳120). Cash on delivery available.</p>
                        <p class="text-[11px] uppercase tracking-wider text-[#C9A84C] font-semibold underline underline-offset-4 cursor-pointer">Read Policy</p>
                    </div>
                </div>
            </div>

            @if($product->sku)
                <p class="text-[10px] uppercase tracking-widest text-gray-400 mt-6 border-t border-gray-100 pt-6">SKU: <span class="text-[#1A1A1A] font-semibold">{{ $product->sku }}</span></p>
            @endif
        </div>
    </div>

    {{-- ── Reviews Section ──────────────────────────────────────────────── --}}
    <div id="reviews" class="border-t border-gray-200 pt-16 sm:pt-20 mb-24 scroll-mt-24">
        <h2 class="section-heading gold-underline text-center sm:text-left mb-12">Customer Reviews</h2>

        <div class="grid lg:grid-cols-3 gap-12 lg:gap-16">
            {{-- Review List (2/3 width) --}}
            <div class="lg:col-span-2 space-y-8">
                @forelse($reviews as $review)
                <div class="border-b border-gray-100 pb-8 last:border-b-0">
                    <div class="flex items-start justify-between mb-4">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 bg-[#F8F8F8] border border-gray-200 text-[#1A1A1A] flex items-center justify-center text-xs font-bold uppercase">
                                {{ substr($review->user->name, 0, 1) }}
                            </div>
                            <div>
                                <p class="text-xs font-bold uppercase tracking-widest text-[#1A1A1A]">{{ $review->user->name }}</p>
                                <p class="text-[10px] text-gray-400 uppercase tracking-widest mt-0.5">{{ $review->created_at->format('F d, Y') }}</p>
                            </div>
                        </div>
                        <div class="flex items-center">
                            @for($s = 1; $s <= 5; $s++)
                                <svg class="w-3.5 h-3.5 {{ $s <= $review->rating ? 'text-amber-400' : 'text-gray-200' }}" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                            @endfor
                        </div>
                    </div>
                    @if($review->title)
                        <h4 class="text-sm font-bold text-[#1A1A1A] mb-2">{{ $review->title }}</h4>
                    @endif
                    @if($review->body)
                        <p class="text-sm text-gray-600 leading-relaxed font-light">{{ $review->body }}</p>
                    @endif
                    @auth
                        @if($review->user_id === auth()->id())
                        <form method="POST" action="{{ route('reviews.destroy', $review->id) }}" class="mt-4 text-right">
                            @csrf @method('DELETE')
                            <button type="submit" class="text-[10px] font-bold uppercase tracking-widest text-gray-400 hover:text-red-500 transition-colors">Delete Review</button>
                        </form>
                        @endif
                    @endauth
                </div>
                @empty
                <div class="text-center py-16 bg-[#F8F8F8] border border-gray-100">
                    <p class="text-xs uppercase tracking-widest text-[#1A1A1A] font-bold mb-2">No reviews yet</p>
                    <p class="text-sm text-gray-500 font-light">Be the first to share your thoughts on this item.</p>
                </div>
                @endforelse
            </div>

            {{-- Write Review (1/3 width) --}}
            <div class="lg:col-span-1">
                @auth
                    @if($userReview)
                        <div class="bg-[#F8F8F8] border border-gray-100 p-8 text-center">
                            <h3 class="text-sm font-bold uppercase tracking-[0.2em] text-[#1A1A1A] mb-4">Review Submitted</h3>
                            <p class="text-sm text-gray-500 font-light mb-4">Thank you for sharing your experience.</p>
                            <div class="flex items-center justify-center gap-1">
                                @for($s = 1; $s <= 5; $s++)
                                    <svg class="w-4 h-4 {{ $s <= $userReview->rating ? 'text-amber-400' : 'text-gray-200' }}" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                                @endfor
                            </div>
                        </div>
                    @else
                        <div class="bg-[#F8F8F8] border border-gray-100 p-6 sm:p-8" x-data="{ rating: 0, hovered: 0 }">
                            <h3 class="text-sm font-bold uppercase tracking-[0.2em] text-[#1A1A1A] mb-6">Write a Review</h3>
                            <form method="POST" action="{{ route('reviews.store', $product->id) }}">
                                @csrf
                                <div class="mb-5">
                                    <label class="text-[10px] font-bold uppercase tracking-widest text-[#1A1A1A] mb-2 block">Rating *</label>
                                    <div class="flex items-center gap-1">
                                        @for($s = 1; $s <= 5; $s++)
                                        <button type="button" @mouseenter="hovered = {{ $s }}" @mouseleave="hovered = 0" @click="rating = {{ $s }}">
                                            <svg class="w-6 h-6 transition-colors duration-100" :class="(hovered || rating) >= {{ $s }} ? 'text-amber-400' : 'text-gray-300'" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                                        </button>
                                        @endfor
                                    </div>
                                    <input type="hidden" name="rating" :value="rating" required>
                                </div>
                                <div class="mb-5">
                                    <label class="text-[10px] font-bold uppercase tracking-widest text-[#1A1A1A] mb-2 block">Title</label>
                                    <input type="text" name="title" class="w-full border border-gray-300 bg-white p-3 text-sm focus:border-[#C9A84C] focus:outline-none" required>
                                </div>
                                <div class="mb-5">
                                    <label class="text-[10px] font-bold uppercase tracking-widest text-[#1A1A1A] mb-2 block">Review</label>
                                    <textarea name="body" rows="4" class="w-full border border-gray-300 bg-white p-3 text-sm focus:border-[#C9A84C] focus:outline-none resize-none" required></textarea>
                                </div>
                                <button type="submit" :disabled="rating === 0" class="btn-primary w-full py-4 text-xs tracking-[0.2m] disabled:opacity-50 disabled:cursor-not-allowed">
                                    Submit Review
                                </button>
                            </form>
                        </div>
                    @endif
                @else
                    <div class="bg-[#F8F8F8] border border-gray-100 p-8 text-center">
                        <h3 class="text-sm font-bold uppercase tracking-[0.2em] text-[#1A1A1A] mb-4">Write a Review</h3>
                        <p class="text-sm text-gray-500 font-light mb-6">Sign in to leave a review for this product.</p>
                        <a href="{{ route('login') }}" class="btn-outline-dark w-full inline-block">Login</a>
                    </div>
                @endauth
            </div>
        </div>
    </div>

    {{-- ── Related Products ─────────────────────────────────────────────── --}}
    @if($relatedProducts->count() > 0)
        <div class="border-t border-gray-200 pt-16 sm:pt-20">
            <h2 class="section-heading gold-underline text-center sm:text-left mb-12">You May Also Like</h2>
            {{-- Horizontal scroll on mobile, grid on desktop --}}
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
@endsection
