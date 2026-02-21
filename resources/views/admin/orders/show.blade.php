@extends('layouts.admin')
@section('title', 'Order #' . $order->order_number)
@section('page-title', 'Order Details')

@section('content')
<div class="space-y-6">

    {{-- Top Bar Actions --}}
    <div class="flex items-center justify-between">
        <a href="{{ route('admin.orders') }}" class="text-[10px] font-bold uppercase tracking-widest text-gray-500 hover:text-[#C9A84C] flex items-center gap-2 transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            Back to Orders
        </a>
        <div class="flex gap-3">
            <a href="{{ route('admin.orders.invoice', $order->id) }}" target="_blank" class="bg-white border border-gray-200 text-[#1A1A1A] hover:border-[#C9A84C] hover:text-[#C9A84C] text-[10px] font-bold uppercase tracking-widest px-6 py-2.5 transition-colors shadow-sm flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                Print Invoice
            </a>
        </div>
    </div>

    {{-- Grid Layout --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 relative items-start">
        
        {{-- LEFT COLUMN: Details --}}
        <div class="lg:col-span-2 space-y-6">
            
            {{-- Order Status Block --}}
            <div class="bg-white border border-gray-100 shadow-sm p-6">
                <div class="flex flex-col sm:flex-row justify-between sm:items-center gap-4 border-b border-gray-100 pb-6 mb-6">
                    <div>
                        <h2 class="text-xl font-bold uppercase tracking-widest text-[#1A1A1A]">Order #{{ $order->order_number }}</h2>
                        <p class="text-[11px] font-bold uppercase tracking-widest text-gray-400 mt-1 flex items-center gap-2">
                            <svg class="w-4 h-4 text-[#C9A84C]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                            {{ $order->created_at->format('M d, Y • h:i A') }}
                        </p>
                    </div>
                    <form method="POST" action="{{ route('admin.orders.updateStatus', $order->id) }}" class="flex items-center gap-3">
                        @csrf @method('PATCH')
                        <span class="text-[10px] font-bold uppercase tracking-widest text-gray-500">Update Status:</span>
                        <select name="status" onchange="this.form.submit()" class="bg-[#F8F8F8] border border-gray-200 text-[#1A1A1A] text-[10px] font-bold uppercase tracking-widest px-4 py-2 focus:outline-none focus:border-[#C9A84C]">
                            @foreach(['pending', 'processing', 'shipped', 'delivered', 'cancelled'] as $st)
                                <option value="{{ $st }}" {{ $order->status === $st ? 'selected' : '' }}>{{ ucfirst($st) }}</option>
                            @endforeach
                        </select>
                    </form>
                </div>
                
                {{-- Payment Details Summary --}}
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                    <div class="p-4 bg-gray-50 border border-gray-100">
                        <p class="text-[9px] font-bold uppercase tracking-widest text-gray-500 mb-1">Payment Method</p>
                        <p class="text-[11px] font-bold text-[#1A1A1A] tracking-wider uppercase">{{ $order->payment_method }}</p>
                    </div>
                    <div class="p-4 bg-gray-50 border border-gray-100">
                        <p class="text-[9px] font-bold uppercase tracking-widest text-gray-500 mb-1">Status</p>
                        <p class="text-[11px] font-bold tracking-wider uppercase {{ $order->payment_status === 'paid' ? 'text-green-600' : 'text-red-500' }}">{{ $order->payment_status }}</p>
                    </div>
                    <div class="p-4 bg-gray-50 border border-gray-100">
                        <p class="text-[9px] font-bold uppercase tracking-widest text-gray-500 mb-1">Txn ID</p>
                        <p class="text-[11px] font-bold text-[#1A1A1A] tracking-wider truncate">{{ $order->transaction_id ?? 'N/A' }}</p>
                    </div>
                    <div class="p-4 bg-gray-50 border border-gray-100">
                        <p class="text-[9px] font-bold uppercase tracking-widest text-gray-500 mb-1">Total Amount</p>
                        <p class="text-xs font-bold text-[#C9A84C] tracking-wide">৳{{ number_format($order->final_amount) }}</p>
                    </div>
                </div>
            </div>

            {{-- Items List --}}
            <div class="bg-white border border-gray-100 shadow-sm p-6">
                <h3 class="text-xs font-bold uppercase tracking-widest text-[#1A1A1A] border-l-2 border-[#C9A84C] pl-3 mb-6">Order Items</h3>
                <div class="border border-gray-100 divide-y divide-gray-100">
                    @foreach($order->items as $item)
                        <div class="p-4 flex gap-4 items-center">
                            <div class="w-16 h-20 bg-gray-50 border border-gray-200 flex-shrink-0">
                                @if($item->image)
                                    <img src="{{ $item->image }}" alt="{{ $item->name }}" class="w-full h-full object-cover">
                                @endif
                            </div>
                            <div class="flex-1 min-w-0">
                                <a href="{{ route('products.show', $item->product_id) }}" target="_blank" class="block text-sm font-bold text-[#1A1A1A] uppercase tracking-widest hover:text-[#C9A84C] truncate">{{ $item->name }}</a>
                                <div class="mt-1 flex flex-col gap-0.5 text-[10px] text-gray-500 tracking-wider">
                                    @if($item->size)<span>Size: {{ $item->size }}</span>@endif
                                    @if($item->color)<span>Color: {{ $item->color }}</span>@endif
                                    <span>Qty: {{ $item->quantity }}</span>
                                </div>
                            </div>
                            <div class="text-right">
                                <p class="text-lg font-bold text-[#C9A84C]">৳{{ number_format($item->price) }}</p>
                                @if($item->quantity > 1)
                                    <p class="text-[10px] text-gray-400 mt-1">৳{{ number_format($item->price * $item->quantity) }} Total</p>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            {{-- Status Timeline --}}
            <div class="bg-white border border-gray-100 shadow-sm p-6">
                <h3 class="text-xs font-bold uppercase tracking-widest text-[#1A1A1A] border-l-2 border-[#C9A84C] pl-3 mb-6">Execution Timeline</h3>
                
                <div class="relative pl-6 border-l border-gray-200 space-y-8 ml-3">
                    @foreach($order->statusHistories as $idx => $history)
                        <div class="relative">
                            {{-- Dot --}}
                            <div class="absolute -left-[31px] w-3 h-3 rounded-full {{ $idx === 0 ? 'bg-[#C9A84C] border-[3px] border-white ring-1 ring-[#C9A84C]' : 'bg-gray-200 border-2 border-white' }}"></div>
                            
                            <p class="text-[11px] font-bold text-[#1A1A1A] uppercase tracking-widest">
                                Status changed to: <span class="{{ cloneStatusColor($history->status) }}">{{ $history->status }}</span>
                            </p>
                            <p class="text-[10px] text-gray-500 mt-1">
                                {{ $history->created_at->format('d M y, h:i A') }} • By: <span class="font-bold text-[#1A1A1A]">{{ $history->user->name ?? 'System' }}</span>
                            </p>
                        </div>
                    @endforeach
                    
                    {{-- Original --}}
                    <div class="relative">
                        <div class="absolute -left-[31px] w-3 h-3 rounded-full bg-gray-200 border-2 border-white"></div>
                        <p class="text-[11px] font-bold text-[#1A1A1A] uppercase tracking-widest">Order Placed</p>
                        <p class="text-[10px] text-gray-500 mt-1">{{ $order->created_at->format('d M y, h:i A') }}</p>
                    </div>
                </div>
            </div>

        </div>

        {{-- RIGHT COLUMN: Customer & Tools --}}
        <div class="space-y-6">
            
            {{-- Customer Card --}}
            @php $address = $order->delivery_address ?? []; @endphp
            <div class="bg-white border border-gray-100 shadow-sm p-6">
                <h3 class="text-xs font-bold uppercase tracking-widest text-[#1A1A1A] border-l-2 border-[#C9A84C] pl-3 mb-6">Customer Detials</h3>
                
                <div class="flex items-center gap-4 mb-6">
                    <div class="w-12 h-12 bg-[#F8F8F8] border border-gray-200 flex items-center justify-center font-bold text-[#1A1A1A] uppercase text-lg">
                        {{ substr($address['name'] ?? 'U', 0, 1) }}
                    </div>
                    <div>
                        <h4 class="text-sm font-bold text-[#1A1A1A] uppercase tracking-widest">{{ $address['name'] ?? 'N/A' }}</h4>
                        <p class="text-[10px] text-gray-500">
                            @if($order->user_id) 
                                Registered Member ({{ $order->user->tier ?? 'Bronze' }}) 
                            @else 
                                Guest Checkout 
                            @endif
                        </p>
                    </div>
                </div>

                <div class="space-y-4">
                    <div class="flex justify-between items-center py-2 border-t border-gray-50">
                        <span class="text-[10px] font-bold uppercase tracking-widest text-gray-500">Past Orders</span>
                        <span class="text-xs font-bold text-[#C9A84C]">{{ $customerOrderCount }}</span>
                    </div>
                    
                    <div class="py-2 border-t border-gray-50">
                        <span class="text-[10px] font-bold uppercase tracking-widest text-gray-500 block mb-1">Contact Details</span>
                        <a href="tel:{{ $address['phone'] ?? '' }}" class="text-xs font-bold text-[#1A1A1A] hover:text-[#C9A84C] block transition-colors">{{ $address['phone'] ?? 'N/A' }}</a>
                        <p class="text-[11px] text-gray-500 mt-1">{{ $address['email'] ?? 'N/A' }}</p>
                    </div>

                    <div class="py-2 border-t border-gray-50" x-data="{
                        copyAddr() {
                            const addr = '{{ addslashes($address['name'] ?? '') }}\n{{ addslashes($address['phone'] ?? '') }}\n{{ addslashes($address['address'] ?? '') }}\n{{ addslashes($address['district'] ?? '') }}';
                            navigator.clipboard.writeText(addr);
                            alert('Copied to clipboard!');
                        }
                    }">
                        <div class="flex items-end justify-between mb-2">
                            <span class="text-[10px] font-bold uppercase tracking-widest text-gray-500">Delivery Address</span>
                            <button @click="copyAddr" class="text-[9px] font-bold uppercase tracking-widest text-[#C9A84C] hover:text-[#b08a38] transition-colors flex items-center gap-1">
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                                Copy
                            </button>
                        </div>
                        <p class="text-xs leading-relaxed text-[#1A1A1A]">
                            {{ $address['address'] ?? 'N/A' }}<br>
                            <span class="font-bold border-b border-dashed border-gray-400 pb-0.5">{{ $address['district'] ?? 'N/A' }}</span>
                        </p>
                    </div>
                </div>
            </div>

            {{-- Summary Breakdown --}}
            <div class="bg-white border border-gray-100 shadow-sm p-6">
                <h3 class="text-xs font-bold uppercase tracking-widest text-[#1A1A1A] border-l-2 border-[#C9A84C] pl-3 mb-6">Financials</h3>
                
                <div class="space-y-3 text-[11px] font-bold uppercase tracking-widest text-gray-500">
                    <div class="flex justify-between items-center">
                        <span>Subtotal</span>
                        <span class="text-[#1A1A1A]">৳{{ number_format($order->total_amount) }}</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span>Shipping</span>
                        <span class="text-[#1A1A1A]">৳{{ number_format($order->shipping_charge) }}</span>
                    </div>
                    @if($order->discount > 0)
                    <div class="flex justify-between items-center text-red-500">
                        <span>Discount</span>
                        <span>-৳{{ number_format($order->discount) }}</span>
                    </div>
                    @endif
                    @if($order->coupon_discount > 0)
                    <div class="flex justify-between items-center text-red-500">
                        <span>Coupon ({{ $order->coupon_code }})</span>
                        <span>-৳{{ number_format($order->coupon_discount) }}</span>
                    </div>
                    @endif
                </div>

                <div class="mt-4 pt-4 border-t border-gray-100 flex justify-between items-end">
                    <span class="text-[10px] font-bold uppercase tracking-widest text-gray-500">Total Charged</span>
                    <span class="text-xl font-extrabold text-[#C9A84C]">৳{{ number_format($order->final_amount) }}</span>
                </div>
            </div>

            {{-- Internal Notes Block --}}
            <div class="bg-[#FAFAF8] border border-[#C9A84C]/20 shadow-sm p-6">
                <h3 class="flex justify-between items-center text-xs font-bold uppercase tracking-widest text-[#1A1A1A] border-l-2 border-[#C9A84C] pl-3 mb-4">
                    Admin Notes
                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                </h3>
                <form method="POST" action="{{ route('admin.orders.updateNotes', $order->id) }}">
                    @csrf @method('PATCH')
                    <textarea name="admin_notes" rows="4" placeholder="Private notes visible only to admins..." class="w-full bg-white border border-gray-200 p-3 text-xs focus:border-[#C9A84C] focus:ring-0 mb-3 resize-y">{{ $order->admin_notes }}</textarea>
                    <button type="submit" class="w-full bg-[#1A1A1A] hover:bg-[#333] text-white text-[10px] font-bold uppercase tracking-widest py-2.5 transition-colors">
                        Save Private Note
                    </button>
                </form>
                @if($order->notes)
                    <div class="mt-4 pt-4 border-t border-gray-200">
                        <span class="text-[9px] font-bold uppercase tracking-widest text-[#C9A84C] block mb-1">Customer Note:</span>
                        <p class="text-xs text-gray-500 italic bg-white p-3 border border-gray-100">"{{ $order->notes }}"</p>
                    </div>
                @endif
            </div>

        </div>
    </div>
</div>

@php
function cloneStatusColor($status) {
    return match($status) {
        'pending' => 'text-yellow-600',
        'processing' => 'text-blue-600',
        'shipped' => 'text-purple-600',
        'delivered' => 'text-green-600',
        'cancelled' => 'text-red-500',
        default => 'text-gray-600'
    };
}
@endphp
@endsection
