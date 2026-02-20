@extends('layouts.app')

@section('title', 'Order Confirmed!')

@section('content')
<div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8 py-20 text-center">
    <div class="card p-10">
        <div class="w-20 h-20 bg-emerald-600/20 border border-emerald-500/30 rounded-full flex items-center justify-center mx-auto mb-6">
            <svg class="w-10 h-10 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
            </svg>
        </div>
        <h1 class="text-3xl font-bold text-white mb-3">Order Confirmed! 🎉</h1>
        <p class="text-gray-400 mb-2">Thank you for your purchase.</p>
        <p class="text-gray-500 text-sm mb-8">Order <span class="text-indigo-400 font-mono font-semibold">#{{ $order->order_number }}</span> has been placed successfully.</p>

        <div class="bg-gray-800 rounded-xl p-5 text-left mb-8 space-y-3">
            <div class="flex justify-between text-sm">
                <span class="text-gray-400">Payment Method</span>
                <span class="text-white font-medium">{{ strtoupper($order->payment_method) }}</span>
            </div>
            <div class="flex justify-between text-sm">
                <span class="text-gray-400">Total Amount</span>
                <span class="text-indigo-400 font-bold">৳{{ number_format($order->final_amount) }}</span>
            </div>
            <div class="flex justify-between text-sm">
                <span class="text-gray-400">Status</span>
                <span class="badge bg-yellow-500/20 text-yellow-400 border border-yellow-500/30">Processing</span>
            </div>
        </div>

        <div class="flex gap-4">
            <a href="{{ route('orders.show', $order->id) }}" class="btn-primary flex-1 py-3">View Order</a>
            <a href="{{ route('products.index') }}" class="btn-outline flex-1 py-3">Shop More</a>
        </div>
    </div>
</div>
@endsection
