@extends('layouts.app')
@section('title', 'My Orders')

@section('content')
<div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-10">

    {{-- Header --}}
    <div class="flex items-center justify-between mb-8">
        <div>
            <h1 class="text-3xl font-bold text-white">My Orders</h1>
            <p class="text-gray-400 mt-1">
                @if($orders->total() > 0)
                    {{ $orders->total() }} {{ Str::plural('order', $orders->total()) }} total
                @else
                    No orders yet
                @endif
            </p>
        </div>
        <a href="{{ route('products.index') }}" class="btn-outline text-sm flex items-center gap-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 16H4L5 9z"/></svg>
            Shop
        </a>
    </div>

    @if($orders->count() > 0)
        <div class="space-y-4">
            @foreach($orders as $order)
            @php
                $statusConfig = [
                    'pending'    => ['color' => 'amber',   'icon' => '📋'],
                    'processing' => ['color' => 'blue',    'icon' => '⚙️'],
                    'shipped'    => ['color' => 'purple',  'icon' => '🚚'],
                    'delivered'  => ['color' => 'emerald', 'icon' => '✅'],
                    'cancelled'  => ['color' => 'red',     'icon' => '❌'],
                ];
                $sc        = $statusConfig[$order->status] ?? ['color' => 'gray', 'icon' => '📦'];
                $c         = $sc['color'];
                $trackSteps = ['pending', 'processing', 'shipped', 'delivered'];
                $stepIdx   = array_search($order->status, $trackSteps);
                $isCancelled = $order->status === 'cancelled';
                $pct       = $isCancelled ? 0 : ($stepIdx === false ? 0 : ($stepIdx / 3) * 100);
            @endphp
            <div class="bg-gray-900 border border-gray-800 rounded-2xl overflow-hidden hover:border-gray-700 transition-colors">

                {{-- Top row --}}
                <div class="px-6 py-5 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                    <div class="flex-1 min-w-0">
                        <div class="flex flex-wrap items-center gap-2 mb-1">
                            <span class="text-base font-bold text-white font-mono">#{{ $order->order_number }}</span>
                            <span class="text-lg">{{ $sc['icon'] }}</span>
                            <span class="badge border
                                @if($c === 'amber')   bg-amber-500/20   text-amber-400   border-amber-500/30
                                @elseif($c === 'blue')    bg-blue-500/20    text-blue-400    border-blue-500/30
                                @elseif($c === 'purple')  bg-purple-500/20  text-purple-400  border-purple-500/30
                                @elseif($c === 'emerald') bg-emerald-500/20 text-emerald-400 border-emerald-500/30
                                @elseif($c === 'red')     bg-red-500/20     text-red-400     border-red-500/30
                                @else                     bg-gray-500/20    text-gray-400    border-gray-500/30
                                @endif">
                                {{ ucfirst($order->status) }}
                            </span>
                            @if($order->payment_status === 'paid')
                                <span class="badge border bg-emerald-500/20 text-emerald-400 border-emerald-500/30">💳 Paid</span>
                            @endif
                        </div>
                        <p class="text-sm text-gray-500">
                            {{ $order->created_at->format('d M Y, h:i A') }}
                            &bull;
                            {{ $order->items->count() }} {{ Str::plural('item', $order->items->count()) }}
                            &bull;
                            <span class="uppercase text-xs">{{ str_replace('_', ' ', $order->payment_method) }}</span>
                        </p>
                    </div>
                    <div class="flex flex-col sm:items-end gap-2 flex-shrink-0">
                        <p class="text-2xl font-extrabold text-indigo-400">৳{{ number_format($order->final_amount) }}</p>
                        <a href="{{ route('orders.show', $order->id) }}"
                           class="btn-outline text-sm px-4 py-2 flex items-center gap-1.5">
                            Track Order
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                        </a>
                    </div>
                </div>

                {{-- Mini tracking progress bar --}}
                @if(!$isCancelled)
                <div class="px-6 pb-5">
                    <div class="flex items-center gap-2 mb-2">
                        @foreach($trackSteps as $i => $step)
                            @php $done = $stepIdx !== false && $i <= $stepIdx; @endphp
                            <div class="flex-1 h-1.5 rounded-full {{ $done ? 'bg-indigo-500' : 'bg-gray-800' }} transition-all"></div>
                            @if($i < 3)
                                <div class="w-2 h-2 rounded-full {{ $done && ($stepIdx > $i) ? 'bg-indigo-400' : 'bg-gray-800' }}"></div>
                            @endif
                        @endforeach
                    </div>
                    <div class="flex justify-between">
                        @foreach($trackSteps as $step)
                            @php $done = $stepIdx !== false && array_search($step, $trackSteps) <= $stepIdx; @endphp
                            <span class="text-xs {{ $done ? 'text-gray-400' : 'text-gray-700' }}">{{ ucfirst($step) }}</span>
                        @endforeach
                    </div>
                </div>
                @else
                <div class="px-6 pb-4">
                    <div class="h-1.5 rounded-full bg-red-500/30"></div>
                    <p class="text-xs text-red-400 mt-1">Order cancelled</p>
                </div>
                @endif

            </div>
            @endforeach
        </div>

        {{-- Pagination --}}
        <div class="mt-8">{{ $orders->links() }}</div>

    @else
        {{-- Empty state --}}
        <div class="text-center py-24">
            <div class="w-24 h-24 bg-gray-900 rounded-full flex items-center justify-center mx-auto mb-6 border border-gray-800">
                <svg class="w-12 h-12 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 16H4L5 9z"/>
                </svg>
            </div>
            <h2 class="text-2xl font-bold text-white mb-3">No orders yet</h2>
            <p class="text-gray-400 max-w-sm mx-auto mb-8">When you make a purchase, your orders will appear here with live tracking.</p>
            <a href="{{ route('products.index') }}" class="btn-primary inline-flex items-center gap-2 px-8 py-3.5">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 16H4L5 9z"/></svg>
                Start Shopping
            </a>
        </div>
    @endif
</div>
@endsection
