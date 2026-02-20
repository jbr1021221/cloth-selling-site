@extends('layouts.app')

@section('title', 'My Orders')

@section('content')
<div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
    <h1 class="text-3xl font-bold text-white mb-8">My Orders</h1>

    @if($orders->count() > 0)
        <div class="space-y-4">
            @foreach($orders as $order)
                <div class="card p-6">
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                        <div>
                            <div class="flex items-center gap-3 mb-1">
                                <p class="font-semibold text-white font-mono">#{{ $order->order_number }}</p>
                                @php
                                    $statusColors = [
                                        'pending'    => 'bg-yellow-500/20 text-yellow-400 border-yellow-500/30',
                                        'processing' => 'bg-blue-500/20 text-blue-400 border-blue-500/30',
                                        'shipped'    => 'bg-purple-500/20 text-purple-400 border-purple-500/30',
                                        'delivered'  => 'bg-emerald-500/20 text-emerald-400 border-emerald-500/30',
                                        'cancelled'  => 'bg-red-500/20 text-red-400 border-red-500/30',
                                    ];
                                    $statusClass = $statusColors[$order->status] ?? 'bg-gray-500/20 text-gray-400 border-gray-500/30';
                                @endphp
                                <span class="badge border {{ $statusClass }}">{{ ucfirst($order->status) }}</span>
                            </div>
                            <p class="text-sm text-gray-500">{{ $order->created_at->format('d M Y, h:i A') }} • {{ $order->items->count() }} item(s)</p>
                        </div>
                        <div class="text-right">
                            <p class="text-xl font-bold text-indigo-400">৳{{ number_format($order->final_amount) }}</p>
                            <p class="text-xs text-gray-500 uppercase">{{ $order->payment_method }} • {{ $order->payment_status }}</p>
                        </div>
                        <a href="{{ route('orders.show', $order->id) }}" class="btn-outline text-sm px-4 py-2">View Details</a>
                    </div>
                </div>
            @endforeach
        </div>
        <div class="mt-8">{{ $orders->links() }}</div>
    @else
        <div class="text-center py-24">
            <div class="text-8xl mb-6">📦</div>
            <h2 class="text-2xl font-bold text-white mb-3">No orders yet</h2>
            <p class="text-gray-500 mb-8">When you make a purchase, your orders will appear here.</p>
            <a href="{{ route('products.index') }}" class="btn-primary px-8 py-3.5">Start Shopping</a>
        </div>
    @endif
</div>
@endsection
