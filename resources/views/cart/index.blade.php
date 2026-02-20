@extends('layouts.app')

@section('title', 'Your Cart')

@section('content')
<div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
    <h1 class="text-3xl font-bold text-white mb-8">Shopping Cart</h1>

    @if(count($cart) > 0)
        <div class="grid lg:grid-cols-3 gap-8">

            {{-- Cart Items --}}
            <div class="lg:col-span-2 space-y-4">
                @foreach($cart as $key => $item)
                    <div class="card p-4 flex gap-4">
                        <img src="{{ $item['image'] }}" alt="{{ $item['name'] }}"
                             class="w-24 h-24 object-cover rounded-xl flex-shrink-0">
                        <div class="flex-1 min-w-0">
                            <h3 class="font-semibold text-white mb-1 truncate">{{ $item['name'] }}</h3>
                            <div class="flex flex-wrap gap-2 text-xs text-gray-500 mb-2">
                                @if($item['size']) <span class="bg-gray-800 px-2 py-0.5 rounded">Size: {{ $item['size'] }}</span> @endif
                                @if($item['color']) <span class="bg-gray-800 px-2 py-0.5 rounded">Color: {{ $item['color'] }}</span> @endif
                            </div>
                            <p class="text-indigo-400 font-bold">৳{{ number_format($item['price']) }}</p>
                        </div>
                        <div class="flex flex-col items-end justify-between">
                            <form method="POST" action="{{ route('cart.remove') }}">
                                @csrf
                                <input type="hidden" name="key" value="{{ $key }}">
                                <button type="submit" class="text-gray-600 hover:text-red-400 transition-colors">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                    </svg>
                                </button>
                            </form>
                            <form method="POST" action="{{ route('cart.update') }}" class="flex items-center gap-2">
                                @csrf
                                <input type="hidden" name="key" value="{{ $key }}">
                                <button type="submit" name="action" value="dec"
                                        class="w-8 h-8 bg-gray-800 border border-gray-700 rounded-lg text-white hover:border-indigo-500 transition-colors text-sm font-bold">−</button>
                                <span class="w-8 text-center text-white font-medium text-sm">{{ $item['quantity'] }}</span>
                                <button type="submit" name="action" value="inc"
                                        class="w-8 h-8 bg-gray-800 border border-gray-700 rounded-lg text-white hover:border-indigo-500 transition-colors text-sm font-bold">+</button>
                            </form>
                            <p class="text-sm font-semibold text-white">৳{{ number_format($item['price'] * $item['quantity']) }}</p>
                        </div>
                    </div>
                @endforeach

                <form method="POST" action="{{ route('cart.clear') }}">
                    @csrf
                    <button type="submit" class="text-sm text-red-400 hover:underline mt-2">Clear all items</button>
                </form>
            </div>

            {{-- Order Summary --}}
            <div>
                <div class="bg-gray-900 border border-gray-800 rounded-2xl p-6 sticky top-24">
                    <h2 class="text-lg font-semibold text-white mb-6">Order Summary</h2>

                    <div class="space-y-3 mb-6">
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-400">Subtotal ({{ array_sum(array_column($cart, 'quantity')) }} items)</span>
                            <span class="text-white">৳{{ number_format($subtotal) }}</span>
                        </div>
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-400">Shipping</span>
                            <span class="text-emerald-400">{{ $subtotal >= 2000 ? 'Free' : '৳' . number_format(100) }}</span>
                        </div>
                        @if($subtotal < 2000)
                            <p class="text-xs text-indigo-400">Add ৳{{ number_format(2000 - $subtotal) }} more for free shipping!</p>
                        @endif
                    </div>

                    <div class="border-t border-gray-800 pt-4 mb-6">
                        <div class="flex justify-between">
                            <span class="font-semibold text-white">Total</span>
                            <span class="font-bold text-xl text-indigo-400">৳{{ number_format($total) }}</span>
                        </div>
                    </div>

                    <a href="{{ route('checkout.index') }}" class="btn-primary w-full block text-center py-3.5">
                        Proceed to Checkout →
                    </a>
                    <a href="{{ route('products.index') }}" class="btn-outline w-full block text-center py-3 mt-3 text-sm">
                        Continue Shopping
                    </a>
                </div>
            </div>
        </div>
    @else
        <div class="text-center py-24">
            <div class="text-8xl mb-6">🛒</div>
            <h2 class="text-2xl font-bold text-white mb-3">Your cart is empty</h2>
            <p class="text-gray-500 mb-8">Looks like you haven't added anything yet.</p>
            <a href="{{ route('products.index') }}" class="btn-primary px-8 py-3.5">Start Shopping</a>
        </div>
    @endif
</div>
@endsection
