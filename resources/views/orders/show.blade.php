@extends('layouts.app')
@section('title', 'Order #' . $order->order_number . ' – Tracking')

@section('content')
@php
    $steps = [
        'pending'    => ['label' => 'Order Placed',   'icon' => '📋', 'desc' => 'Your order has been received and is awaiting confirmation.'],
        'processing' => ['label' => 'Processing',     'icon' => '⚙️',  'desc' => 'Your order is being picked, packed, and prepared for shipment.'],
        'shipped'    => ['label' => 'Shipped',        'icon' => '🚚', 'desc' => 'Your order is on its way and will arrive soon.'],
        'delivered'  => ['label' => 'Delivered',      'icon' => '✅', 'desc' => 'Your order has been successfully delivered.'],
    ];

    $statusOrder   = array_keys($steps);
    $currentStatus = $order->status;
    $isCancelled   = $currentStatus === 'cancelled';

    // Index of current step (0-based). If cancelled, keep at 0.
    $currentIndex  = $isCancelled ? -1 : (int) array_search($currentStatus, $statusOrder);
    $progressPct   = $isCancelled ? 0 : ($currentIndex / (count($steps) - 1)) * 100;

    $statusColors = [
        'pending'    => ['text' => 'text-amber-400',   'bg' => 'bg-amber-500/15',   'border' => 'border-amber-500/40'],
        'processing' => ['text' => 'text-blue-400',    'bg' => 'bg-blue-500/15',    'border' => 'border-blue-500/40'],
        'shipped'    => ['text' => 'text-purple-400',  'bg' => 'bg-purple-500/15',  'border' => 'border-purple-500/40'],
        'delivered'  => ['text' => 'text-emerald-400', 'bg' => 'bg-emerald-500/15', 'border' => 'border-emerald-500/40'],
        'cancelled'  => ['text' => 'text-red-400',     'bg' => 'bg-red-500/15',     'border' => 'border-red-500/40'],
    ];
    $sc = $statusColors[$currentStatus] ?? $statusColors['pending'];
@endphp

<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-10">

    {{-- Back nav + order number header --}}
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-8">
        <div>
            <a href="{{ route('orders.index') }}"
               class="inline-flex items-center gap-2 text-sm text-gray-500 hover:text-indigo-400 transition-colors mb-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                My Orders
            </a>
            <h1 class="text-2xl font-bold text-white">Order <span class="font-mono text-indigo-400">#{{ $order->order_number }}</span></h1>
            <p class="text-sm text-gray-500 mt-0.5">Placed on {{ $order->created_at->format('d M Y, h:i A') }}</p>
        </div>
        <div class="flex items-center gap-3">
            <span class="inline-flex items-center gap-2 px-4 py-2 rounded-xl text-sm font-semibold border {{ $sc['text'] }} {{ $sc['bg'] }} {{ $sc['border'] }}">
                {{ $isCancelled ? '❌' : ($steps[$currentStatus]['icon'] ?? '📦') }}
                {{ ucfirst($currentStatus) }}
            </span>
            @if($order->payment_status === 'paid')
                <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl text-xs font-semibold bg-emerald-500/15 border border-emerald-500/30 text-emerald-400">
                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 inline-block"></span>
                    Paid
                </span>
            @else
                <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl text-xs font-semibold bg-amber-500/15 border border-amber-500/30 text-amber-400">
                    <span class="w-1.5 h-1.5 rounded-full bg-amber-400 animate-pulse inline-block"></span>
                    {{ ucfirst($order->payment_status) }}
                </span>
            @endif
        </div>
    </div>

    {{-- ── Order Tracking Timeline ───────────────────────────────────────────── --}}
    @if(!$isCancelled)
    <div class="bg-gray-900 border border-gray-800 rounded-2xl p-6 sm:p-8 mb-8">
        <h2 class="text-lg font-bold text-white mb-8 flex items-center gap-2">
            <svg class="w-5 h-5 text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"/>
            </svg>
            Live Tracking
        </h2>

        {{-- Step indicators --}}
        <div class="relative">
            {{-- Connector line (background track) --}}
            <div class="hidden sm:block absolute top-6 left-[calc(12.5%)] right-[calc(12.5%)] h-0.5 bg-gray-800 z-0"></div>

            {{-- Animated fill line --}}
            <div class="hidden sm:block absolute top-6 left-[calc(12.5%)] h-0.5 bg-gradient-to-r from-indigo-500 to-indigo-400 z-0 transition-all duration-[1500ms] ease-out"
                 style="width: calc({{ $progressPct }}% * 0.75)"></div>

            {{-- Steps --}}
            <div class="grid grid-cols-4 gap-2 relative z-10">
                @foreach($steps as $key => $step)
                    @php
                        $stepIdx   = array_search($key, $statusOrder);
                        $isDone    = $stepIdx < $currentIndex;
                        $isCurrent = $stepIdx === $currentIndex;
                        $isPending = $stepIdx > $currentIndex;
                    @endphp
                    <div class="flex flex-col items-center text-center">
                        {{-- Circle icon --}}
                        <div class="relative mb-3">
                            @if($isDone)
                                <div class="w-12 h-12 rounded-full bg-indigo-600 border-2 border-indigo-400 flex items-center justify-center shadow-lg shadow-indigo-500/30">
                                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                                    </svg>
                                </div>
                            @elseif($isCurrent)
                                <div class="w-12 h-12 rounded-full bg-indigo-600 border-2 border-indigo-300 flex items-center justify-center shadow-lg shadow-indigo-500/40"
                                     style="animation: pulse-ring 2s ease-in-out infinite;">
                                    <span class="text-xl leading-none">{{ $step['icon'] }}</span>
                                </div>
                                <span class="absolute -top-1 -right-1 w-3.5 h-3.5 bg-emerald-400 border-2 border-gray-900 rounded-full animate-pulse"></span>
                            @else
                                <div class="w-12 h-12 rounded-full bg-gray-800 border-2 border-gray-700 flex items-center justify-center opacity-40">
                                    <span class="text-xl leading-none">{{ $step['icon'] }}</span>
                                </div>
                            @endif
                        </div>

                        {{-- Label --}}
                        <p class="text-xs font-bold mb-0.5
                            @if($isCurrent) text-indigo-400
                            @elseif($isDone) text-gray-300
                            @else text-gray-600
                            @endif">
                            {{ $step['label'] }}
                        </p>

                        {{-- Date/Time or pending --}}
                        @if($isCurrent)
                            <p class="text-xs text-gray-500">{{ $order->updated_at->format('d M, h:i A') }}</p>
                        @elseif($isDone)
                            <p class="text-xs text-gray-600">Completed</p>
                        @else
                            <p class="text-xs text-gray-700">Pending</p>
                        @endif
                    </div>
                @endforeach
            </div>
        </div>

        {{-- Current step description --}}
        @if(isset($steps[$currentStatus]))
        <div class="mt-8 p-4 bg-indigo-600/10 border border-indigo-500/20 rounded-xl flex items-start gap-3">
            <span class="text-2xl flex-shrink-0">{{ $steps[$currentStatus]['icon'] }}</span>
            <div>
                <p class="text-sm font-semibold text-indigo-300 mb-0.5">{{ $steps[$currentStatus]['label'] }}</p>
                <p class="text-sm text-gray-400">{{ $steps[$currentStatus]['desc'] }}</p>
                @if($currentStatus === 'shipped')
                    <p class="text-xs text-amber-400 mt-1.5">
                        📅 Expected delivery: {{ $order->updated_at->addDays(3)->format('d M Y') }} – {{ $order->updated_at->addDays(5)->format('d M Y') }}
                    </p>
                @endif
            </div>
        </div>
        @endif

        @if($order->transaction_id)
        <div class="mt-4 flex items-center gap-2 text-xs text-gray-600">
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
            Transaction ID: <span class="font-mono text-gray-500">{{ $order->transaction_id }}</span>
        </div>
        @endif
    </div>
    @else
    {{-- Cancelled State --}}
    <div class="bg-red-500/10 border border-red-500/30 rounded-2xl p-6 mb-8 flex items-center gap-4">
        <div class="w-12 h-12 rounded-full bg-red-500/20 border border-red-500/40 flex items-center justify-center flex-shrink-0">
            <svg class="w-6 h-6 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
            </svg>
        </div>
        <div>
            <p class="text-red-400 font-bold text-base">Order Cancelled</p>
            <p class="text-gray-500 text-sm mt-0.5">This order has been cancelled. If you were charged, a refund will be processed within 3–5 business days.</p>
        </div>
    </div>
    @endif
    {{-- ── End Tracking Timeline ─────────────────────────────────────────────── --}}

    <div class="grid md:grid-cols-3 gap-6">

        {{-- Left: Items + Address --}}
        <div class="md:col-span-2 space-y-6">

            {{-- Order Items --}}
            <div class="bg-gray-900 border border-gray-800 rounded-2xl overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-800 flex items-center gap-2">
                    <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 16H4L5 9z"/>
                    </svg>
                    <h2 class="font-semibold text-white">Order Items ({{ $order->items->count() }})</h2>
                </div>
                <div class="divide-y divide-gray-800">
                    @foreach($order->items as $item)
                    @php
                        $thumb = is_array($item->product?->images) && count($item->product->images) > 0
                                    ? $item->product->images[0]
                                    : null;
                    @endphp
                    <div class="flex items-center gap-4 px-6 py-4">
                        {{-- Thumbnail --}}
                        <div class="w-16 h-16 rounded-xl overflow-hidden bg-gray-800 flex-shrink-0">
                            @if($thumb)
                                <img src="{{ $thumb }}" alt="{{ $item->product->name }}" class="w-full h-full object-cover">
                            @else
                                <div class="w-full h-full flex items-center justify-center text-2xl">👔</div>
                            @endif
                        </div>

                        {{-- Info --}}
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-medium text-white truncate">
                                {{ $item->product->name ?? 'Product #' . $item->product_id }}
                            </p>
                            <div class="flex flex-wrap gap-2 mt-1">
                                <span class="text-xs text-gray-500">Qty: {{ $item->quantity }}</span>
                                @if($item->size)
                                    <span class="text-xs bg-gray-800 text-gray-400 px-2 py-0.5 rounded-md">{{ $item->size }}</span>
                                @endif
                                @if($item->color)
                                    <span class="text-xs bg-gray-800 text-gray-400 px-2 py-0.5 rounded-md">{{ $item->color }}</span>
                                @endif
                            </div>
                        </div>

                        {{-- Price + reorder --}}
                        <div class="text-right flex-shrink-0">
                            <p class="text-sm font-bold text-white">৳{{ number_format($item->price * $item->quantity) }}</p>
                            <p class="text-xs text-gray-600 mt-0.5">৳{{ number_format($item->price) }} each</p>
                            @if($item->product)
                                <a href="{{ route('products.show', $item->product_id) }}"
                                   class="text-xs text-indigo-400 hover:text-indigo-300 transition-colors mt-1 inline-block">
                                    Buy again →
                                </a>
                            @endif
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>

            {{-- Shipping Address --}}
            <div class="bg-gray-900 border border-gray-800 rounded-2xl overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-800 flex items-center gap-2">
                    <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                    <h2 class="font-semibold text-white">Delivery Address</h2>
                </div>
                @if($order->delivery_address)
                    @php $addr = $order->delivery_address; @endphp
                    <div class="px-6 py-5 text-sm text-gray-400 space-y-1">
                        <p class="text-white font-semibold text-base">{{ $addr['name'] ?? '' }}</p>
                        @if(isset($addr['phone']))
                            <p class="flex items-center gap-2 text-gray-400">
                                <svg class="w-4 h-4 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                                {{ $addr['phone'] }}
                            </p>
                        @endif
                        <p>{{ $addr['address'] ?? '' }}</p>
                        <p>{{ implode(', ', array_filter([$addr['city'] ?? null, $addr['district'] ?? null, $addr['postal_code'] ?? null])) }}</p>
                        <p>{{ $addr['country'] ?? 'Bangladesh' }}</p>
                    </div>
                @else
                    <p class="px-6 py-5 text-gray-500 text-sm">No address on file.</p>
                @endif
            </div>
        </div>

        {{-- Right: Summary --}}
        <div class="space-y-6">
            <div class="bg-gray-900 border border-gray-800 rounded-2xl overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-800">
                    <h2 class="font-semibold text-white">Order Summary</h2>
                </div>
                <div class="px-6 py-5 space-y-3 text-sm">
                    <div class="flex justify-between">
                        <span class="text-gray-400">Order #</span>
                        <span class="text-white font-mono text-xs">{{ $order->order_number }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-400">Date</span>
                        <span class="text-white">{{ $order->created_at->format('d M Y') }}</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-gray-400">Payment</span>
                        <span class="text-white uppercase text-xs font-semibold bg-gray-800 px-2 py-1 rounded-lg">{{ str_replace('_', ' ', $order->payment_method) }}</span>
                    </div>
                    @if($order->notes)
                    <div class="pt-2 border-t border-gray-800">
                        <p class="text-gray-500 text-xs">📝 {{ $order->notes }}</p>
                    </div>
                    @endif
                </div>
                <div class="px-6 pb-5 space-y-2 text-sm border-t border-gray-800 pt-4">
                    <div class="flex justify-between">
                        <span class="text-gray-400">Subtotal</span>
                        <span class="text-white">৳{{ number_format($order->total_amount) }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-400">Shipping</span>
                        @if($order->shipping_charge == 0)
                            <span class="text-emerald-400 font-semibold">Free</span>
                        @else
                            <span class="text-white">৳{{ number_format($order->shipping_charge) }}</span>
                        @endif
                    </div>
                    @if($order->discount > 0)
                    <div class="flex justify-between">
                        <span class="text-gray-400">Discount</span>
                        <span class="text-emerald-400">−৳{{ number_format($order->discount) }}</span>
                    </div>
                    @endif
                    <div class="flex justify-between font-bold text-base pt-3 border-t border-gray-800 mt-2">
                        <span class="text-white">Total</span>
                        <span class="text-indigo-400">৳{{ number_format($order->final_amount) }}</span>
                    </div>
                </div>
            </div>

            {{-- Help box --}}
            <div class="bg-gray-900 border border-gray-800 rounded-2xl p-5 text-center">
                <p class="text-sm font-semibold text-gray-300 mb-1">Need help?</p>
                <p class="text-xs text-gray-500 mb-3">Have a question about this order? Contact our support team.</p>
                <a href="mailto:support@clothstore.com"
                   class="inline-flex items-center gap-2 text-xs text-indigo-400 hover:text-indigo-300 transition-colors font-medium">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                    support@clothstore.com
                </a>
            </div>

            {{-- Continue shopping --}}
            <a href="{{ route('products.index') }}"
               class="btn-outline w-full py-3 flex items-center justify-center gap-2 text-sm">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 16H4L5 9z"/></svg>
                Continue Shopping
            </a>
        </div>

    </div>
</div>

<style>
/* Pulsing ring animation for the active tracking step */
@keyframes pulse-ring {
    0%, 100% { box-shadow: 0 0 0 0 rgba(99, 102, 241, 0.5); }
    50%       { box-shadow: 0 0 0 10px rgba(99, 102, 241, 0); }
}
</style>
@endsection
