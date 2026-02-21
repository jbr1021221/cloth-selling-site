@extends('layouts.app')
@section('title', 'Order Confirmed')

@section('content')
<div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-16 sm:py-24 bg-white min-h-[70vh]">

    {{-- Success Message --}}
    <div class="text-center mb-12">
        <div class="w-16 h-16 border border-[#C9A84C] flex items-center justify-center mx-auto mb-6 bg-white" style="animation: fade-in-up 0.6s ease-out">
            <svg class="w-6 h-6 text-[#C9A84C]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M5 13l4 4L19 7"/>
            </svg>
        </div>
        <h1 class="playfair text-3xl sm:text-4xl font-bold text-[#1A1A1A] mb-3 uppercase tracking-widest">Order Confirmed</h1>
        <p class="text-sm text-gray-500 uppercase tracking-widest font-light">Thank you. Your order has been received.</p>
    </div>

    {{-- Order Summary Box --}}
    <div class="border border-gray-200">

        {{-- Top Info Row --}}
        <div class="flex flex-col sm:flex-row divide-y sm:divide-y-0 sm:divide-x divide-gray-200 bg-[#F8F8F8] border-b border-gray-200">
            <div class="flex-1 p-6 text-center sm:text-left">
                <p class="text-[10px] font-bold uppercase tracking-[0.2em] text-gray-400 mb-1">Order Number</p>
                <p class="text-lg font-bold text-[#1A1A1A]">#{{ $order->order_number }}</p>
            </div>
            <div class="flex-1 p-6 text-center sm:text-left">
                <p class="text-[10px] font-bold uppercase tracking-[0.2em] text-gray-400 mb-1">Date</p>
                <p class="text-sm font-semibold text-[#1A1A1A] mt-1">{{ $order->created_at->format('M d, Y') }}</p>
            </div>
            <div class="flex-1 p-6 text-center sm:text-left">
                <p class="text-[10px] font-bold uppercase tracking-[0.2em] text-gray-400 mb-1">Payment</p>
                <p class="text-sm font-bold uppercase tracking-widest text-[#C9A84C] mt-1">
                    {{ $order->payment_method === 'cod' ? 'Cash on Delivery' : strtoupper($order->payment_method) }}
                </p>
            </div>
        </div>

        {{-- Items List --}}
        <div class="p-6 sm:p-8 border-b border-gray-200 bg-white">
            <h3 class="text-xs font-bold uppercase tracking-[0.2em] text-[#1A1A1A] mb-6">Order Details</h3>
            @php $order->loadMissing('items'); @endphp
            <div class="space-y-6">
                @foreach($order->items as $item)
                <div class="flex items-start gap-4">
                    <div class="w-16 h-20 border border-gray-100 bg-[#F8F8F8] shrink-0">
                        @if($item->image)
                            <img src="{{ $item->image }}" alt="{{ $item->name }}" class="w-full h-full object-cover">
                        @endif
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-bold uppercase tracking-widest text-[#1A1A1A] mb-1 truncate">
                            {{ $item->name ?? ($item->product->name ?? 'Item') }}
                        </p>
                        <p class="text-xs text-gray-500 uppercase tracking-widest">Qty: {{ $item->quantity }} @if($item->size) | {{ $item->size }} @endif @if($item->color) | {{ $item->color }} @endif</p>
                    </div>
                    <p class="text-sm font-bold text-[#1A1A1A]">৳{{ number_format($item->price * $item->quantity) }}</p>
                </div>
                @endforeach
            </div>
        </div>

        {{-- Totals --}}
        <div class="p-6 sm:p-8 border-b border-gray-200 bg-[#F8F8F8]">
            <div class="max-w-xs ml-auto space-y-3">
                <div class="flex justify-between text-sm">
                    <span class="uppercase tracking-widest text-gray-500">Subtotal</span>
                    <span class="font-semibold text-[#1A1A1A]">৳{{ number_format($order->total_amount) }}</span>
                </div>
                <div class="flex justify-between text-sm">
                    <span class="uppercase tracking-widest text-gray-500">Shipping</span>
                    @if($order->shipping_charge == 0)
                        <span class="font-semibold text-[#C9A84C] uppercase tracking-widest text-[10px]">Free</span>
                    @else
                        <span class="font-semibold text-[#1A1A1A]">৳{{ number_format($order->shipping_charge) }}</span>
                    @endif
                </div>
                @if($order->coupon_discount > 0)
                <div class="flex justify-between text-sm text-[#C9A84C]">
                    <span class="uppercase tracking-widest font-bold text-[10px] mt-1">Discount</span>
                    <span class="font-bold">−৳{{ number_format($order->coupon_discount) }}</span>
                </div>
                @endif
                <div class="flex justify-between items-center border-t border-gray-200 pt-3 mt-3">
                    <span class="font-bold uppercase tracking-widest text-[#1A1A1A]">Total</span>
                    <span class="playfair text-2xl font-bold text-[#1A1A1A]">৳{{ number_format($order->final_amount) }}</span>
                </div>
            </div>
        </div>

        {{-- Shipping Address --}}
        @if($order->delivery_address)
        @php $addr = $order->delivery_address; @endphp
        <div class="p-6 sm:p-8 bg-white text-sm text-gray-600 font-light leading-relaxed text-center sm:text-left">
            <p class="text-[10px] font-bold uppercase tracking-[0.2em] text-[#1A1A1A] mb-3">Delivery Address</p>
            <p class="uppercase tracking-widest text-[#1A1A1A] font-semibold mb-1">{{ $addr['name'] ?? '' }} — {{ $addr['phone'] ?? '' }}</p>
            <p>{{ $addr['address'] ?? '' }}, {{ $addr['thana'] ?? '' }}, {{ implode(', ', array_filter([$addr['district'] ?? null, $addr['city'] ?? null])) }}</p>
        </div>
        @endif

    </div>

    {{-- Actions --}}
    <div class="mt-12 text-center">
        <a href="{{ route('products.index') }}" class="btn-outline-dark inline-block w-full sm:w-auto sm:px-12 py-4">Keep Shopping</a>
    </div>

</div>

<style>
@keyframes fade-in-up {
    from { opacity: 0; transform: translateY(10px); }
    to { opacity: 1; transform: translateY(0); }
}
</style>
@endsection
