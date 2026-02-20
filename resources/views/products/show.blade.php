@extends('layouts.app')

@section('title', $product->name)

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">

    {{-- Breadcrumb --}}
    <nav class="flex items-center gap-2 text-sm text-gray-500 mb-8">
        <a href="{{ route('home') }}" class="hover:text-indigo-400 transition-colors">Home</a>
        <span>/</span>
        <a href="{{ route('products.index') }}" class="hover:text-indigo-400 transition-colors">Products</a>
        <span>/</span>
        <a href="{{ route('products.index', ['category' => $product->category]) }}" class="hover:text-indigo-400 transition-colors">{{ $product->category }}</a>
        <span>/</span>
        <span class="text-gray-300">{{ Str::limit($product->name, 40) }}</span>
    </nav>

    <div class="grid md:grid-cols-2 gap-12 mb-20">

        {{-- Images --}}
        <div>
            <div class="bg-gray-900 border border-gray-800 rounded-3xl overflow-hidden aspect-square mb-4">
                <img id="main-image"
                     src="{{ is_array($product->images) && count($product->images) > 0 ? $product->images[0] : 'https://images.unsplash.com/photo-1523381210434-271e8be1f52b?q=80&w=800&auto=format&fit=crop' }}"
                     alt="{{ $product->name }}"
                     class="w-full h-full object-cover">
            </div>
            @if(is_array($product->images) && count($product->images) > 1)
                <div class="flex gap-3">
                    @foreach($product->images as $img)
                        <button onclick="document.getElementById('main-image').src='{{ $img }}'"
                                class="w-20 h-20 bg-gray-900 border border-gray-800 rounded-xl overflow-hidden hover:border-indigo-500 transition-colors">
                            <img src="{{ $img }}" alt="Product image" class="w-full h-full object-cover">
                        </button>
                    @endforeach
                </div>
            @endif
        </div>

        {{-- Product Info --}}
        <div>
            <span class="badge bg-indigo-600/20 text-indigo-400 border border-indigo-500/30 mb-4">{{ $product->category }}</span>
            <h1 class="text-3xl font-bold text-white mb-4">{{ $product->name }}</h1>

            {{-- Price --}}
            <div class="flex items-center gap-4 mb-6">
                @if($product->discount_price && $product->discount_price < $product->price)
                    <span class="text-3xl font-extrabold text-indigo-400">৳{{ number_format($product->discount_price) }}</span>
                    <span class="text-xl text-gray-600 line-through">৳{{ number_format($product->price) }}</span>
                    <span class="badge bg-red-500/20 text-red-400 border border-red-500/30">
                        {{ round((($product->price - $product->discount_price) / $product->price) * 100) }}% OFF
                    </span>
                @else
                    <span class="text-3xl font-extrabold text-indigo-400">৳{{ number_format($product->price) }}</span>
                @endif
            </div>

            {{-- Description --}}
            <p class="text-gray-400 leading-relaxed mb-6">{{ $product->description }}</p>

            {{-- Stock --}}
            <div class="flex items-center gap-2 mb-6">
                @if($product->stock > 0)
                    <span class="w-2 h-2 bg-emerald-500 rounded-full"></span>
                    <span class="text-sm text-emerald-400 font-medium">In Stock ({{ $product->stock }} available)</span>
                @else
                    <span class="w-2 h-2 bg-red-500 rounded-full"></span>
                    <span class="text-sm text-red-400 font-medium">Out of Stock</span>
                @endif
            </div>

            {{-- Add to Cart Form --}}
            @if($product->stock > 0)
            <form method="POST" action="{{ route('cart.add') }}" class="space-y-4">
                @csrf
                <input type="hidden" name="product_id" value="{{ $product->id }}">

                {{-- Size --}}
                @if(is_array($product->sizes) && count($product->sizes) > 0)
                    <div>
                        <label class="label">Select Size</label>
                        <div class="flex flex-wrap gap-2">
                            @foreach($product->sizes as $size)
                                <label class="cursor-pointer">
                                    <input type="radio" name="size" value="{{ $size }}" class="sr-only peer" {{ $loop->first ? 'checked' : '' }}>
                                    <span class="inline-block px-4 py-2 border border-gray-700 rounded-xl text-sm font-medium text-gray-400 peer-checked:border-indigo-500 peer-checked:bg-indigo-600/20 peer-checked:text-indigo-400 hover:border-gray-600 transition-all">
                                        {{ $size }}
                                    </span>
                                </label>
                            @endforeach
                        </div>
                    </div>
                @endif

                {{-- Color --}}
                @if(is_array($product->colors) && count($product->colors) > 0)
                    <div>
                        <label class="label">Select Color</label>
                        <div class="flex flex-wrap gap-2">
                            @foreach($product->colors as $color)
                                <label class="cursor-pointer">
                                    <input type="radio" name="color" value="{{ $color }}" class="sr-only peer" {{ $loop->first ? 'checked' : '' }}>
                                    <span class="inline-block px-4 py-2 border border-gray-700 rounded-xl text-sm font-medium text-gray-400 peer-checked:border-indigo-500 peer-checked:bg-indigo-600/20 peer-checked:text-indigo-400 hover:border-gray-600 transition-all">
                                        {{ $color }}
                                    </span>
                                </label>
                            @endforeach
                        </div>
                    </div>
                @endif

                {{-- Quantity --}}
                <div>
                    <label class="label">Quantity</label>
                    <div class="flex items-center gap-3">
                        <button type="button" onclick="let q=document.getElementById('qty'); q.value=Math.max(1,+q.value-1)"
                                class="w-10 h-10 bg-gray-800 border border-gray-700 rounded-xl text-white hover:border-indigo-500 transition-colors flex items-center justify-center font-bold">−</button>
                        <input type="number" id="qty" name="quantity" value="1" min="1" max="{{ $product->stock }}"
                               class="w-16 text-center input py-2">
                        <button type="button" onclick="let q=document.getElementById('qty'); q.value=Math.min({{ $product->stock }},+q.value+1)"
                                class="w-10 h-10 bg-gray-800 border border-gray-700 rounded-xl text-white hover:border-indigo-500 transition-colors flex items-center justify-center font-bold">+</button>
                    </div>
                </div>

                <div class="flex gap-3 pt-2">
                    <button type="submit" class="btn-primary flex-1 py-3.5 text-base">
                        Add to Cart 🛒
                    </button>
                    <button type="button" onclick="window.location='{{ route('cart.index') }}'"
                            class="btn-outline px-6 py-3.5">
                        Buy Now
                    </button>
                </div>
            </form>
            @else
                <div class="bg-gray-800/50 border border-gray-700 rounded-xl p-4 text-center text-gray-500">
                    This item is currently out of stock.
                </div>
            @endif

            {{-- SKU --}}
            @if($product->sku)
                <p class="text-xs text-gray-600 mt-4">SKU: {{ $product->sku }}</p>
            @endif
        </div>
    </div>

    {{-- Related Products --}}
    @if($relatedProducts->count() > 0)
        <div>
            <h2 class="text-2xl font-bold text-white mb-6">You May Also Like</h2>
            <div class="grid grid-cols-2 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                @foreach($relatedProducts as $related)
                    @include('partials.product-card', ['product' => $related])
                @endforeach
            </div>
        </div>
    @endif
</div>
@endsection
