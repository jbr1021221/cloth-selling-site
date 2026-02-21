@extends('layouts.app')
@section('title', 'Your Cart')

@section('content')
<div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-10 sm:py-16 bg-white">

    <div class="flex items-center gap-4 mb-10">
        <a href="{{ route('products.index') }}" class="w-10 h-10 border border-gray-200 flex items-center justify-center text-[#1A1A1A] hover:border-[#C9A84C] hover:text-[#C9A84C] transition-colors rounded-none">
            <svg class="w-5 h-5 -ml-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 19l-7-7 7-7"/></svg>
        </a>
        <h1 class="playfair text-3xl sm:text-4xl font-bold text-[#1A1A1A]">Shopping Bag</h1>
        @if(count($cart) > 0)
            <span class="bg-[#F8F8F8] border border-gray-200 text-[#1A1A1A] text-xs font-bold px-3 py-1 tracking-widest uppercase">
                {{ array_sum(array_column($cart, 'quantity')) }} Items
            </span>
        @endif
    </div>

    @if(count($cart) > 0)
        <div class="grid lg:grid-cols-3 gap-8 lg:gap-12">

            {{-- ── Cart Items ────────────────────────────────────────────── --}}
            <div class="lg:col-span-2 space-y-4">
                @foreach($cart as $key => $item)
                <div class="border border-gray-100 p-4 sm:p-5 flex gap-5 hover:border-[#C9A84C] transition-colors duration-300 bg-white">

                    {{-- Product image --}}
                    <a href="{{ route('products.show', $item['id']) }}" class="flex-shrink-0 bg-[#F8F8F8]">
                        <img src="{{ $item['image'] }}" alt="{{ $item['name'] }}" class="w-24 h-32 sm:w-28 sm:h-36 object-cover border border-gray-100">
                    </a>

                    {{-- Details --}}
                    <div class="flex-1 min-w-0 flex flex-col justify-between">
                        <div>
                            <div class="flex justify-between items-start gap-4 mb-1">
                                <h3 class="font-bold text-[#1A1A1A] text-sm sm:text-base tracking-wide uppercase truncate">
                                    <a href="{{ route('products.show', $item['id']) }}" class="hover:text-[#C9A84C] transition-colors">{{ $item['name'] }}</a>
                                </h3>
                                {{-- Remove --}}
                                <form method="POST" action="{{ route('cart.remove') }}">
                                    @csrf
                                    <input type="hidden" name="key" value="{{ $key }}">
                                    <button type="submit" class="text-gray-400 hover:text-red-500 transition-colors p-1" title="Remove">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M6 18L18 6M6 6l12 12"/></svg>
                                    </button>
                                </form>
                            </div>

                            <p class="text-[#1A1A1A] font-semibold text-sm mb-3">৳{{ number_format($item['price']) }}</p>

                            <div class="flex flex-wrap gap-x-4 gap-y-2 text-xs text-gray-500 uppercase tracking-widest">
                                @if($item['size']) <span>Size: <strong class="text-[#1A1A1A]">{{ $item['size'] }}</strong></span> @endif
                                @if($item['color']) <span>Color: <strong class="text-[#1A1A1A]">{{ $item['color'] }}</strong></span> @endif
                            </div>
                        </div>

                        {{-- Qty & Total row --}}
                        <div class="flex items-center justify-between mt-4 pt-4 border-t border-gray-100">
                            {{-- Qty stepper --}}
                            <form method="POST" action="{{ route('cart.update') }}" class="flex items-center">
                                @csrf
                                <input type="hidden" name="key" value="{{ $key }}">
                                <button type="submit" name="action" value="dec" class="w-8 h-8 flex items-center justify-center border border-gray-300 text-gray-500 hover:text-[#C9A84C] hover:border-[#C9A84C] transition-colors">−</button>
                                <span class="w-10 text-center text-sm font-semibold text-[#1A1A1A]">{{ $item['quantity'] }}</span>
                                <button type="submit" name="action" value="inc" class="w-8 h-8 flex items-center justify-center border border-gray-300 text-gray-500 hover:text-[#C9A84C] hover:border-[#C9A84C] transition-colors">+</button>
                            </form>
                            {{-- Line total --}}
                            <p class="text-sm font-bold text-[#1A1A1A]">Total: ৳{{ number_format($item['price'] * $item['quantity']) }}</p>
                        </div>
                    </div>
                </div>
                @endforeach

                <form method="POST" action="{{ route('cart.clear') }}" class="pt-2 text-right">
                    @csrf
                    <button type="submit" class="text-xs font-semibold tracking-widest uppercase text-gray-400 hover:text-red-500 transition-colors inline-flex items-center gap-1">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                        Clear Bag
                    </button>
                </form>
            </div>

            {{-- ── Order Summary ─────────────────────────────────────────── --}}
            <div>
                <div class="bg-[#F8F8F8] p-6 sm:p-8 lg:sticky lg:top-28 border border-gray-100">
                    <h2 class="text-lg font-bold text-[#1A1A1A] uppercase tracking-widest mb-6">Order Summary</h2>

                    <div class="space-y-4 mb-6">
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-500 uppercase tracking-wider">Subtotal</span>
                            <span class="text-[#1A1A1A] font-semibold">৳{{ number_format($subtotal) }}</span>
                        </div>
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-500 uppercase tracking-wider">Shipping</span>
                            <span class="text-[#C9A84C] font-semibold text-xs tracking-wider uppercase">Calc. at checkout</span>
                        </div>
                    </div>

                    <div class="bg-white border text-center border-[#C9A84C]/20 px-4 py-3 text-xs text-[#1A1A1A] font-medium tracking-wide uppercase mb-6">
                        Dhaka: <span class="text-[#C9A84C] font-bold">৳60</span> &nbsp;|&nbsp; Outside: <span class="text-[#C9A84C] font-bold">৳120</span>
                    </div>

                    <div class="border-t border-gray-200 pt-5 mb-8">
                        <div class="flex justify-between items-center">
                            <span class="font-bold text-[#1A1A1A] uppercase tracking-widest">Estimated Total</span>
                            <span class="font-bold text-2xl text-[#1A1A1A] playfair">৳{{ number_format($subtotal) }}</span>
                        </div>
                    </div>

                    <a href="{{ route('checkout.index') }}" class="btn-primary w-full block text-center mb-4">
                        Proceed to Checkout
                    </a>
                    <a href="{{ route('products.index') }}" class="btn-outline-dark w-full block text-center">
                        Continue Shopping
                    </a>
                </div>
            </div>
        </div>

    @else
        {{-- Empty state --}}
        <div class="text-center justify-center flex flex-col items-center py-24 sm:py-32 border border-gray-100 bg-[#F8F8F8] mt-6">
            <svg class="w-20 h-20 text-gray-300 mb-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/></svg>
            <h2 class="playfair text-3xl font-bold text-[#1A1A1A] mb-3">Your shopping bag is empty.</h2>
            <p class="text-gray-500 uppercase tracking-widest text-sm mb-8">Discover our new collection.</p>
            <a href="{{ route('products.index') }}" class="btn-primary px-10">Start Shopping</a>
        </div>
    @endif
</div>
@endsection
