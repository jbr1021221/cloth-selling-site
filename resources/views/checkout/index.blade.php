@extends('layouts.app')

@section('title', 'Checkout')

@section('content')
<div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
    <h1 class="text-3xl font-bold text-white mb-8">Checkout</h1>

    <form method="POST" action="{{ route('checkout.place') }}" class="grid lg:grid-cols-3 gap-8">
        @csrf

        {{-- Shipping & Payment --}}
        <div class="lg:col-span-2 space-y-6">

            {{-- Shipping Details --}}
            <div class="bg-gray-900 border border-gray-800 rounded-2xl p-6">
                <h2 class="text-lg font-semibold text-white mb-5 flex items-center gap-2">
                    <span class="w-7 h-7 bg-indigo-600 rounded-full flex items-center justify-center text-sm font-bold">1</span>
                    Shipping Details
                </h2>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="label">Full Name *</label>
                        <input type="text" name="name" value="{{ auth()->user()->name ?? old('name') }}" required class="input"
                               placeholder="Your full name">
                        @error('name') <p class="text-red-400 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="label">Phone Number *</label>
                        <input type="tel" name="phone" value="{{ auth()->user()->phone ?? old('phone') }}" required class="input"
                               placeholder="01XXXXXXXXX">
                        @error('phone') <p class="text-red-400 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div class="sm:col-span-2">
                        <label class="label">Email</label>
                        <input type="email" name="email" value="{{ auth()->user()->email ?? old('email') }}" class="input"
                               placeholder="your@email.com">
                    </div>
                    <div class="sm:col-span-2">
                        <label class="label">Street Address *</label>
                        <input type="text" name="address" value="{{ old('address') }}" required class="input"
                               placeholder="House/Road/Block">
                        @error('address') <p class="text-red-400 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="label">City *</label>
                        <input type="text" name="city" value="{{ old('city') }}" required class="input"
                               placeholder="Dhaka">
                        @error('city') <p class="text-red-400 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="label">District</label>
                        <input type="text" name="district" value="{{ old('district') }}" class="input"
                               placeholder="Dhaka">
                    </div>
                    <div>
                        <label class="label">Postal Code</label>
                        <input type="text" name="postal_code" value="{{ old('postal_code') }}" class="input"
                               placeholder="1212">
                    </div>
                    <div>
                        <label class="label">Country</label>
                        <input type="text" name="country" value="Bangladesh" class="input" readonly>
                    </div>
                    <div class="sm:col-span-2">
                        <label class="label">Order Notes (optional)</label>
                        <textarea name="notes" rows="2" class="input resize-none"
                                  placeholder="Any delivery instructions..."></textarea>
                    </div>
                </div>
            </div>

            {{-- Payment Method --}}
            <div class="bg-gray-900 border border-gray-800 rounded-2xl p-6">
                <h2 class="text-lg font-semibold text-white mb-5 flex items-center gap-2">
                    <span class="w-7 h-7 bg-indigo-600 rounded-full flex items-center justify-center text-sm font-bold">2</span>
                    Payment Method
                </h2>
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                    @php $methods = [['cod', '💵', 'Cash on Delivery'], ['bkash', '📱', 'bKash'], ['nagad', '💳', 'Nagad']]; @endphp
                    @foreach($methods as [$val, $icon, $label])
                        <label class="cursor-pointer group">
                            <input type="radio" name="payment_method" value="{{ $val }}" {{ $val === 'cod' ? 'checked' : '' }} class="sr-only peer">
                            <div class="border border-gray-700 rounded-xl p-4 text-center peer-checked:border-indigo-500 peer-checked:bg-indigo-600/10 hover:border-gray-600 transition-all">
                                <div class="text-2xl mb-1">{{ $icon }}</div>
                                <p class="text-sm font-medium text-gray-300 peer-checked:text-indigo-400">{{ $label }}</p>
                            </div>
                        </label>
                    @endforeach
                </div>
            </div>
        </div>

        {{-- Order Summary --}}
        <div>
            <div class="bg-gray-900 border border-gray-800 rounded-2xl p-6 sticky top-24">
                <h2 class="text-lg font-semibold text-white mb-5">Your Order</h2>

                <div class="space-y-3 mb-5">
                    @foreach($cart as $item)
                        <div class="flex items-center gap-3">
                            <img src="{{ $item['image'] }}" alt="{{ $item['name'] }}"
                                 class="w-12 h-12 rounded-lg object-cover flex-shrink-0">
                            <div class="flex-1 min-w-0">
                                <p class="text-sm text-gray-300 truncate">{{ $item['name'] }}</p>
                                <p class="text-xs text-gray-500">Qty: {{ $item['quantity'] }}</p>
                            </div>
                            <p class="text-sm font-medium text-white flex-shrink-0">৳{{ number_format($item['price'] * $item['quantity']) }}</p>
                        </div>
                    @endforeach
                </div>

                <div class="border-t border-gray-800 pt-4 space-y-2 mb-4">
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-400">Subtotal</span>
                        <span class="text-white">৳{{ number_format($subtotal) }}</span>
                    </div>
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-400">Shipping</span>
                        <span class="{{ $shipping == 0 ? 'text-emerald-400' : 'text-white' }}">
                            {{ $shipping == 0 ? 'Free' : '৳' . number_format($shipping) }}
                        </span>
                    </div>
                </div>

                <div class="border-t border-gray-800 pt-4 mb-6">
                    <div class="flex justify-between">
                        <span class="font-bold text-white">Total</span>
                        <span class="font-bold text-xl text-indigo-400">৳{{ number_format($total) }}</span>
                    </div>
                </div>

                <button type="submit" class="btn-primary w-full py-3.5 text-base">
                    Place Order →
                </button>
                <p class="text-xs text-gray-600 text-center mt-3">🔒 Secure & encrypted checkout</p>
            </div>
        </div>

    </form>
</div>
@endsection
