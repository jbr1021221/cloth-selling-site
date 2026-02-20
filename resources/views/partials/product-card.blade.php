{{-- resources/views/partials/product-card.blade.php --}}
@php
    $displayPrice = $product->discount_price ?? $product->price;
    $hasDiscount = $product->discount_price && $product->discount_price < $product->price;
    $discountPct = $hasDiscount ? round((($product->price - $product->discount_price) / $product->price) * 100) : 0;
    $image = is_array($product->images) && count($product->images) > 0 ? $product->images[0] : 'https://images.unsplash.com/photo-1523381210434-271e8be1f52b?q=80&w=500&auto=format&fit=crop';
@endphp
<div class="card group">
    <div class="relative overflow-hidden h-56">
        <img src="{{ $image }}" alt="{{ $product->name }}"
             class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500">
        @if($hasDiscount)
            <span class="absolute top-3 left-3 badge bg-red-500 text-white">{{ $discountPct }}% OFF</span>
        @endif
        {{-- Quick add to cart --}}
        <div class="absolute inset-0 bg-black/50 opacity-0 group-hover:opacity-100 transition-opacity duration-300 flex items-end p-4">
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
                <button type="submit" class="w-full bg-indigo-600 hover:bg-indigo-500 text-white font-semibold py-2.5 rounded-xl transition-all text-sm">
                    Quick Add to Cart
                </button>
            </form>
        </div>
    </div>
    <div class="p-4">
        <p class="text-xs text-indigo-400 font-medium mb-1">{{ $product->category }}</p>
        <a href="{{ route('products.show', $product->id) }}" class="block">
            <h3 class="font-semibold text-gray-100 hover:text-indigo-400 transition-colors line-clamp-2 mb-2">{{ $product->name }}</h3>
        </a>
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-2">
                <span class="text-lg font-bold text-indigo-400">৳{{ number_format($displayPrice) }}</span>
                @if($hasDiscount)
                    <span class="text-sm text-gray-600 line-through">৳{{ number_format($product->price) }}</span>
                @endif
            </div>
            @if(is_array($product->sizes) && count($product->sizes) > 0)
                <div class="flex gap-1">
                    @foreach(array_slice($product->sizes, 0, 3) as $size)
                        <span class="text-xs bg-gray-800 text-gray-400 px-1.5 py-0.5 rounded">{{ $size }}</span>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
</div>
