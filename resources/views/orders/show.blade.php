@extends('layouts.app')

@section('title', 'Order #' . $order->order_number)

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
    <div class="flex items-center gap-4 mb-8">
        <a href="{{ route('orders.index') }}" class="text-gray-500 hover:text-indigo-400 transition-colors">
            ← Back to Orders
        </a>
    </div>

    <div class="grid md:grid-cols-3 gap-6">

        {{-- Order Items --}}
        <div class="md:col-span-2 space-y-4">
            <div class="card p-6">
                <h2 class="font-semibold text-white mb-4">Order Items</h2>
                <div class="space-y-4">
                    @foreach($order->items as $item)
                        <div class="flex items-center gap-4 border-t border-gray-800 pt-4 first:border-0 first:pt-0">
                            <div class="w-14 h-14 bg-gray-800 rounded-xl flex items-center justify-center flex-shrink-0 text-xl">👔</div>
                            <div class="flex-1">
                                <p class="text-sm font-medium text-white">{{ $item->product->name ?? 'Product #' . $item->product_id }}</p>
                                <p class="text-xs text-gray-500">
                                    Qty: {{ $item->quantity }}
                                    @if($item->size) | Size: {{ $item->size }} @endif
                                    @if($item->color) | Color: {{ $item->color }} @endif
                                </p>
                            </div>
                            <p class="text-sm font-semibold text-white">৳{{ number_format($item->price * $item->quantity) }}</p>
                        </div>
                    @endforeach
                </div>
            </div>

            {{-- Shipping Address --}}
            <div class="card p-6">
                <h2 class="font-semibold text-white mb-4">Shipping Address</h2>
                @if($order->delivery_address)
                    @php $addr = $order->delivery_address; @endphp
                    <div class="text-sm text-gray-400 space-y-1">
                        <p class="text-white font-medium">{{ $addr['name'] ?? '' }}</p>
                        <p>{{ $addr['address'] ?? '' }}</p>
                        <p>{{ $addr['city'] ?? '' }} {{ $addr['district'] ?? '' }} {{ $addr['postal_code'] ?? '' }}</p>
                        <p>{{ $addr['country'] ?? 'Bangladesh' }}</p>
                        @if(isset($addr['phone'])) <p>📞 {{ $addr['phone'] }}</p> @endif
                    </div>
                @else
                    <p class="text-gray-500 text-sm">No address on file.</p>
                @endif
            </div>
        </div>

        {{-- Summary --}}
        <div>
            <div class="card p-6">
                <h2 class="font-semibold text-white mb-5">Order Summary</h2>
                <div class="space-y-3 text-sm mb-5">
                    <div class="flex justify-between">
                        <span class="text-gray-400">Order #</span>
                        <span class="text-white font-mono">{{ $order->order_number }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-400">Date</span>
                        <span class="text-white">{{ $order->created_at->format('d M Y') }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-400">Status</span>
                        @php
                            $statusColors = ['pending' => 'text-yellow-400', 'processing' => 'text-blue-400', 'shipped' => 'text-purple-400', 'delivered' => 'text-emerald-400', 'cancelled' => 'text-red-400'];
                        @endphp
                        <span class="{{ $statusColors[$order->status] ?? 'text-gray-400' }} font-medium">{{ ucfirst($order->status) }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-400">Payment</span>
                        <span class="text-white uppercase">{{ $order->payment_method }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-400">Payment Status</span>
                        <span class="{{ $order->payment_status === 'paid' ? 'text-emerald-400' : 'text-yellow-400' }} font-medium">{{ ucfirst($order->payment_status) }}</span>
                    </div>
                </div>
                <div class="border-t border-gray-800 pt-4 space-y-2">
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-400">Subtotal</span>
                        <span class="text-white">৳{{ number_format($order->total_amount) }}</span>
                    </div>
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-400">Shipping</span>
                        <span class="text-white">৳{{ number_format($order->shipping_charge) }}</span>
                    </div>
                    <div class="flex justify-between font-bold text-base pt-1">
                        <span class="text-white">Total</span>
                        <span class="text-indigo-400">৳{{ number_format($order->final_amount) }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
