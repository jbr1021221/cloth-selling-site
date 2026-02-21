@extends('layouts.app')
@section('title', 'Checkout')

@section('content')
@php
$districts = [
    'Dhaka Division'     => ['Dhaka','Faridpur','Gazipur','Gopalganj','Kishoreganj','Madaripur','Manikganj','Munshiganj','Narayanganj','Narsingdi','Rajbari','Shariatpur','Tangail'],
    'Chittagong Division'=> ['Bandarban','Brahmanbaria','Chandpur','Chattogram','Cox\'s Bazar','Cumilla','Feni','Khagrachhari','Lakshmipur','Noakhali','Rangamati'],
    'Rajshahi Division'  => ['Bogura','Chapainawabganj','Joypurhat','Naogaon','Natore','Pabna','Rajshahi','Sirajganj'],
    'Khulna Division'    => ['Bagerhat','Chuadanga','Jashore','Jhenaidah','Khulna','Kushtia','Magura','Meherpur','Narail','Satkhira'],
    'Barishal Division'  => ['Barguna','Barishal','Bhola','Jhalokati','Patuakhali','Pirojpur'],
    'Sylhet Division'    => ['Habiganj','Moulvibazar','Sunamganj','Sylhet'],
    'Rangpur Division'   => ['Dinajpur','Gaibandha','Kurigram','Lalmonirhat','Nilphamari','Panchagarh','Rangpur','Thakurgaon'],
    'Mymensingh Division'=> ['Jamalpur','Mymensingh','Netrokona','Sherpur'],
];
$dhakaShipping   = 60;
$outsideShipping = 120;
@endphp

<div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-10 sm:py-16 bg-white">

    <div class="flex items-center gap-4 mb-10">
        <a href="{{ route('cart.index') }}" class="w-10 h-10 border border-gray-200 flex items-center justify-center text-[#1A1A1A] hover:border-[#C9A84C] hover:text-[#C9A84C] transition-colors">
            <svg class="w-5 h-5 -ml-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 19l-7-7 7-7"/></svg>
        </a>
        <h1 class="playfair text-3xl sm:text-4xl font-bold text-[#1A1A1A]">Checkout</h1>
    </div>

    @if($errors->any())
    <div class="mb-8 border border-red-200 bg-red-50 p-5">
        <p class="text-xs font-bold uppercase tracking-widest text-[#1A1A1A] mb-3">Please fix the following errors:</p>
        <ul class="text-sm text-red-600 space-y-1">
            @foreach($errors->all() as $err)
                <li>• {{ $err }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <form method="POST" action="{{ route('checkout.place') }}" class="grid lg:grid-cols-3 gap-10 lg:gap-16" x-data="checkoutApp()" @submit="applyShipping()">
        @csrf

        {{-- ── Left: Shipping + Payment ───────────────────────────────────────── --}}
        <div class="lg:col-span-2 space-y-10">

            {{-- Shipping Details --}}
            <div>
                <h2 class="text-sm font-bold uppercase tracking-[0.2em] text-[#1A1A1A] border-b border-gray-200 pb-3 mb-6">1. Shipping Details</h2>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-x-6 gap-y-6">
                    <div>
                        <label class="text-[10px] font-bold uppercase tracking-widest text-gray-500 mb-2 block">Full Name *</label>
                        <input type="text" name="name" value="{{ old('name', auth()->user()->name ?? '') }}" required class="w-full border-b border-gray-300 py-2 focus:outline-none focus:border-[#C9A84C] text-[#1A1A1A] text-sm bg-transparent placeholder-gray-300" placeholder="Your full name">
                    </div>

                    <div>
                        <label class="text-[10px] font-bold uppercase tracking-widest text-gray-500 mb-2 block">Phone Number *</label>
                        <div class="relative">
                            <span class="absolute left-0 top-1/2 -translate-y-1/2 text-[#1A1A1A] text-sm font-medium">+880</span>
                            <input type="tel" name="phone" id="phone" value="{{ old('phone', auth()->user()->phone ?? '') }}" required pattern="01[3-9]\d{8}" maxlength="11" class="w-full border-b border-gray-300 py-2 pl-12 focus:outline-none focus:border-[#C9A84C] text-[#1A1A1A] text-sm bg-transparent placeholder-gray-300" placeholder="1XXXXXXXXX" x-model="phone" @input="validatePhone()">
                        </div>
                        <p class="text-[10px] mt-1 tracking-widest uppercase transition-all" x-show="phoneMsg" :class="phoneValid ? 'text-green-600' : 'text-red-500'" x-text="phoneMsg"></p>
                    </div>

                    <div class="sm:col-span-2">
                        <label class="text-[10px] font-bold uppercase tracking-widest text-gray-500 mb-2 block">Email <span class="text-gray-400 font-normal normal-case tracking-normal">(optional)</span></label>
                        <input type="email" name="email" value="{{ old('email', auth()->user()->email ?? '') }}" class="w-full border-b border-gray-300 py-2 focus:outline-none focus:border-[#C9A84C] text-[#1A1A1A] text-sm bg-transparent placeholder-gray-300" placeholder="your@email.com">
                    </div>

                    <div class="sm:col-span-2">
                        <label class="text-[10px] font-bold uppercase tracking-widest text-gray-500 mb-2 block">Street Address *</label>
                        <input type="text" name="address" value="{{ old('address') }}" required class="w-full border-b border-gray-300 py-2 focus:outline-none focus:border-[#C9A84C] text-[#1A1A1A] text-sm bg-transparent placeholder-gray-300" placeholder="House No, Road, Area">
                    </div>

                    <div>
                        <label class="text-[10px] font-bold uppercase tracking-widest text-gray-500 mb-2 block">District *</label>
                        <select name="district" required class="w-full border-b border-gray-300 py-2 focus:outline-none focus:border-[#C9A84C] text-[#1A1A1A] text-sm bg-transparent cursor-pointer" x-model="district" @change="onDistrictChange()">
                            <option value="">-- Select District --</option>
                            @foreach($districts as $division => $divDistricts)
                                <optgroup label="{{ $division }}">
                                    @foreach($divDistricts as $d)
                                        <option value="{{ $d }}" {{ old('district') === $d ? 'selected' : '' }}>{{ $d }}</option>
                                    @endforeach
                                </optgroup>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="text-[10px] font-bold uppercase tracking-widest text-gray-500 mb-2 block">Thana / Upazila *</label>
                        <input type="text" name="thana" value="{{ old('thana') }}" required class="w-full border-b border-gray-300 py-2 focus:outline-none focus:border-[#C9A84C] text-[#1A1A1A] text-sm bg-transparent placeholder-gray-300">
                    </div>

                    <div>
                        <label class="text-[10px] font-bold uppercase tracking-widest text-gray-500 mb-2 block">City / Town</label>
                        <input type="text" name="city" value="{{ old('city') }}" class="w-full border-b border-gray-300 py-2 focus:outline-none focus:border-[#C9A84C] text-[#1A1A1A] text-sm bg-transparent placeholder-gray-300">
                    </div>

                    <div>
                        <label class="text-[10px] font-bold uppercase tracking-widest text-gray-500 mb-2 block">Postal Code</label>
                        <input type="text" name="postal_code" value="{{ old('postal_code') }}" class="w-full border-b border-gray-300 py-2 focus:outline-none focus:border-[#C9A84C] text-[#1A1A1A] text-sm bg-transparent placeholder-gray-300" maxlength="10">
                    </div>

                    {{-- Delivery Estimate Banner --}}
                    <div class="sm:col-span-2" x-show="district" x-transition>
                        <div class="bg-[#F8F8F8] border border-gray-200 p-4 flex items-start gap-4">
                            <svg class="w-5 h-5 text-[#C9A84C] shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10a1 1 0 001 1h1m8-1a1 1 0 01-1 1H9m4-1V8a1 1 0 011-1h2.586a1 1 0 01.707.293l3.414 3.414a1 1 0 01.293.707V16a1 1 0 01-1 1h-1m-6-1a1 1 0 001 1h1M5 17a2 2 0 104 0m-4 0a2 2 0 114 0m6 0a2 2 0 104 0m-4 0a2 2 0 114 0"/></svg>
                            <div>
                                <p class="text-sm font-bold tracking-wide uppercase text-[#1A1A1A] mb-1" x-text="isDhaka ? 'Dhaka Delivery' : 'Outside Dhaka Delivery'"></p>
                                <p class="text-sm text-gray-600">
                                    Est. Delivery: <strong class="text-[#1A1A1A] font-semibold" x-text="deliveryDate"></strong> &nbsp;|&nbsp;
                                    Fee: <strong class="text-[#C9A84C]">৳<span x-text="shipping"></span></strong>
                                </p>
                            </div>
                        </div>
                    </div>

                    <div class="sm:col-span-2">
                        <label class="text-[10px] font-bold uppercase tracking-widest text-gray-500 mb-2 block">Order Notes <span class="text-gray-400 font-normal normal-case tracking-normal">(optional)</span></label>
                        <textarea name="notes" rows="2" class="w-full border-b border-gray-300 py-2 focus:outline-none focus:border-[#C9A84C] text-[#1A1A1A] text-sm bg-transparent placeholder-gray-300 resize-none" placeholder="Any special delivery instructions...">{{ old('notes') }}</textarea>
                    </div>
                </div>
            </div>

            {{-- Payment Method --}}
            <div x-data="{ method: 'cod' }">
                <h2 class="text-sm font-bold uppercase tracking-[0.2em] text-[#1A1A1A] border-b border-gray-200 pb-3 mb-6">2. Payment Method</h2>

                <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
                    {{-- COD --}}
                    <label class="cursor-pointer group">
                        <input type="radio" name="payment_method" value="cod" checked x-model="method" class="sr-only peer">
                        <div class="border border-gray-200 bg-[#F8F8F8] p-5 text-center peer-checked:border-[#C9A84C] peer-checked:bg-white transition-all h-full flex flex-col items-center justify-center">
                            <span class="text-2xl mb-2 grayscale opacity-60 peer-checked:grayscale-0 peer-checked:opacity-100 transition-all block">🛵</span>
                            <p class="text-[10px] font-bold tracking-widest uppercase text-gray-500 peer-checked:text-[#C9A84C]">Cash On Delivery</p>
                        </div>
                    </label>

                    {{-- SSLCommerz --}}
                    <label class="cursor-pointer group">
                        <input type="radio" name="payment_method" value="sslcommerz" x-model="method" class="sr-only peer">
                        <div class="border border-gray-200 bg-[#F8F8F8] p-5 text-center peer-checked:border-[#C9A84C] peer-checked:bg-white transition-all h-full relative overflow-hidden flex flex-col items-center justify-center">
                            <span class="absolute top-0 right-0 bg-[#C9A84C] text-white text-[9px] font-bold tracking-wider px-1.5 py-0.5 uppercase">SSL</span>
                            <span class="text-2xl mb-2 grayscale opacity-60 peer-checked:grayscale-0 peer-checked:opacity-100 transition-all block">💳</span>
                            <p class="text-[10px] font-bold tracking-widest uppercase text-gray-500 peer-checked:text-[#C9A84C]">bKash/Card</p>
                        </div>
                    </label>

                    {{-- bKash manual --}}
                    <label class="cursor-pointer group">
                        <input type="radio" name="payment_method" value="bkash" x-model="method" class="sr-only peer">
                        <div class="border border-gray-200 bg-[#F8F8F8] p-5 text-center peer-checked:border-pink-500 peer-checked:bg-white transition-all h-full flex flex-col items-center justify-center">
                            <span class="text-2xl mb-2 grayscale opacity-60 peer-checked:grayscale-0 peer-checked:opacity-100 transition-all block">📱</span>
                            <p class="text-[10px] font-bold tracking-widest uppercase text-gray-500 peer-checked:text-pink-500">bKash <span class="block normal-case tracking-normal font-normal text-gray-400 mt-1">Manual</span></p>
                        </div>
                    </label>

                    {{-- Nagad manual --}}
                    <label class="cursor-pointer group">
                        <input type="radio" name="payment_method" value="nagad" x-model="method" class="sr-only peer">
                        <div class="border border-gray-200 bg-[#F8F8F8] p-5 text-center peer-checked:border-orange-500 peer-checked:bg-white transition-all h-full flex flex-col items-center justify-center">
                            <span class="text-2xl mb-2 grayscale opacity-60 peer-checked:grayscale-0 peer-checked:opacity-100 transition-all block">📱</span>
                            <p class="text-[10px] font-bold tracking-widest uppercase text-gray-500 peer-checked:text-orange-500">Nagad <span class="block normal-case tracking-normal font-normal text-gray-400 mt-1">Manual</span></p>
                        </div>
                    </label>
                </div>

                {{-- Contextual Info --}}
                <div class="text-xs text-gray-500 border border-gray-100 p-4 bg-[#F8F8F8]">
                    <template x-if="method === 'cod'">
                        <p>Pay with cash when your order arrives. No advance payment needed.</p>
                    </template>
                    <template x-if="method === 'sslcommerz'">
                        <p>You will be securely redirected to SSLCommerz. We accept Visa, Mastercard, AMEX, bKash, and Nagad.</p>
                    </template>
                    <template x-if="method === 'bkash'">
                        <p>Send payment to our bKash Agent/Personal number. Instructions will be provided on the next page.</p>
                    </template>
                    <template x-if="method === 'nagad'">
                        <p>Send payment to our Nagad Agent/Personal number. Instructions will be provided on the next page.</p>
                    </template>
                </div>
            </div>
        </div>

        {{-- ── Right: Order Summary ─────────────────────────────────────────────── --}}
        <div>
            <div class="bg-[#F8F8F8] border border-gray-200 p-6 sm:p-8 lg:sticky lg:top-28">
                <h2 class="text-sm font-bold uppercase tracking-[0.2em] text-[#1A1A1A] mb-6">Your Order</h2>

                {{-- Items --}}
                <div class="space-y-4 mb-8">
                    @foreach($cart as $item)
                    <div class="flex items-start gap-4">
                        <img src="{{ $item['image'] }}" alt="Product" class="w-16 h-20 object-cover border border-gray-200 bg-white">
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-bold tracking-wide uppercase text-[#1A1A1A] truncate">{{ $item['name'] }}</p>
                            <p class="text-xs text-gray-500 mt-1 uppercase tracking-widest">Qty: {{ $item['quantity'] }} @if($item['size']) | {{ $item['size'] }} @endif</p>
                            <p class="text-sm font-semibold text-[#1A1A1A] mt-2">৳{{ number_format($item['price'] * $item['quantity']) }}</p>
                        </div>
                    </div>
                    @endforeach
                </div>

                {{-- Totals Alpine --}}
                <div x-data="{
                        code: '', applied: false, loading: false, couponDiscount: 0, msg: '', msgType: '',
                        async apply() {
                            if (!this.code.trim() || this.loading) return;
                            this.loading = true; this.msg = ''; this.applied = false;
                            try {
                                const res  = await fetch('{{ route('coupon.apply') }}', {
                                    method: 'POST',
                                    headers: {'Content-Type':'application/json','Accept':'application/json','X-CSRF-TOKEN':'{{ csrf_token() }}'},
                                    body: JSON.stringify({ code: this.code, subtotal: {{ $subtotal }} }),
                                });
                                const data = await res.json();
                                this.msg = data.message; this.msgType = data.valid ? 'success' : 'error';
                                if (data.valid) { this.applied = true; this.couponDiscount = data.discount; }
                            } catch(e) { this.msg = 'Error applying coupon.'; this.msgType = 'error'; }
                            finally { this.loading = false; }
                        },
                        remove() { this.applied = false; this.couponDiscount = 0; this.code = ''; this.msg = ''; },
                        get grandTotal() {
                            return Math.max(0, {{ $subtotal }} + $root.shipping - (this.applied ? this.couponDiscount : 0));
                        }
                     }" class="border-t border-gray-200 pt-6">

                    <div class="space-y-4 mb-6">
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-500 uppercase tracking-widest">Subtotal</span>
                            <span class="font-semibold text-[#1A1A1A]">৳{{ number_format($subtotal) }}</span>
                        </div>

                        <div class="flex justify-between text-sm">
                            <span class="text-gray-500 uppercase tracking-widest">Shipping</span>
                            <span class="font-semibold text-[#1A1A1A]">
                                <template x-if="$root.district"><span>৳<span x-text="$root.shipping"></span></span></template>
                                <template x-if="!$root.district"><span class="text-[#C9A84C] text-[10px]">Select District</span></template>
                            </span>
                        </div>

                        <div class="flex justify-between text-sm" x-show="applied" x-transition>
                            <span class="text-[#C9A84C] uppercase tracking-widest font-bold flex items-center gap-1">
                                Coupon (<span x-text="code"></span>)
                            </span>
                            <span class="text-[#C9A84C] font-bold">−৳<span x-text="couponDiscount"></span></span>
                        </div>
                    </div>

                    {{-- Promo box --}}
                    <div class="mb-6">
                        <div class="flex border border-gray-300 bg-white" x-show="!applied">
                            <input type="text" x-model="code" @keydown.enter.prevent="apply()" placeholder="Promo code" class="w-full px-4 py-3 text-xs uppercase tracking-widest focus:outline-none">
                            <button type="button" @click="apply()" :disabled="loading || !code.trim()" class="px-4 text-xs font-bold uppercase tracking-widest border-l border-gray-300 text-[#1A1A1A] hover:bg-gray-50 disabled:opacity-50 transition-colors">Apply</button>
                        </div>
                        <div class="flex items-center justify-between border border-[#C9A84C] bg-[#C9A84C]/5 px-4 py-3" x-show="applied" x-transition>
                            <span class="text-xs font-bold uppercase tracking-widest text-[#C9A84C]" x-text="'Applied: ' + code"></span>
                            <button type="button" @click="remove()" class="text-[10px] font-bold uppercase tracking-[0.2em] text-gray-500 hover:text-red-500">Remove</button>
                        </div>
                        <p class="text-[10px] mt-2 uppercase tracking-widest" x-show="msg" :class="msgType === 'success' ? 'text-green-600' : 'text-red-500'" x-text="msg"></p>
                    </div>

                    <div class="border-t border-gray-200 pt-6 mb-8">
                        <div class="flex justify-between items-center">
                            <span class="font-bold text-[#1A1A1A] uppercase tracking-widest">Total</span>
                            <span class="playfair text-3xl font-bold text-[#1A1A1A]">৳<span x-text="grandTotal.toLocaleString('en-IN',{maximumFractionDigits:0})">{{ number_format($subtotal) }}</span></span>
                        </div>
                    </div>

                    <input type="hidden" name="coupon_code" :value="applied ? code.toUpperCase() : ''">
                    <input type="hidden" name="computed_shipping" :value="$root.shipping">
                    <input type="hidden" name="computed_district_type" :value="$root.isDhaka ? 'dhaka' : 'outside'">
                </div>

                <button type="submit" class="btn-primary w-full py-4 bg-[#1A1A1A] hover:bg-black text-[11px] disabled:opacity-50" :disabled="!$root.district || !phoneValid">
                    Place Order
                </button>

                <p class="text-[10px] uppercase tracking-[0.2em] text-center text-gray-400 mt-4 flex items-center justify-center gap-2">
                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                    Secure Checkout
                </p>
            </div>
        </div>
    </form>
</div>

<script>
function checkoutApp() {
    return {
        district: '{{ old('district', '') }}',
        isDhaka: {{ old('district') === 'Dhaka' ? 'true' : 'false' }},
        shipping: {{ old('district') === 'Dhaka' ? $dhakaShipping : (old('district') ? $outsideShipping : 0) }},
        deliveryDate: '',
        phone: '{{ old('phone', auth()->user()->phone ?? '') }}',
        phoneValid: true,
        phoneMsg: '',

        init() { if (this.district) this.onDistrictChange(); },
        onDistrictChange() {
            this.isDhaka  = this.district === 'Dhaka';
            this.shipping = this.isDhaka ? {{ $dhakaShipping }} : {{ $outsideShipping }};
            const d = new Date(); d.setDate(d.getDate() + (this.isDhaka ? 2 : 5));
            this.deliveryDate = d.toLocaleDateString('en-GB', { day: 'numeric', month: 'long', year: 'numeric' });
        },
        validatePhone() {
            const p = this.phone.trim();
            if (!p) { this.phoneMsg = ''; this.phoneValid = false; return; }
            if (!/^01/.test(p)) { this.phoneMsg = 'Must start with 01'; this.phoneValid = false; return; }
            if (p.length !== 11) { this.phoneMsg = `${p.length}/11 digits`; this.phoneValid = p.length === 11; return; }
            if (!/^01[3-9]\d{8}$/.test(p)) { this.phoneMsg = 'Invalid BD Number format'; this.phoneValid = false; return; }
            this.phoneMsg = 'Valid Number'; this.phoneValid = true;
        },
        applyShipping() {
            if (!this.district) this.shipping = {{ $outsideShipping }};
        }
    };
}
</script>
@endsection
