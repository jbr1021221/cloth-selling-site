@extends('layouts.admin')

@section('title', 'Dashboard')
@section('page-title', 'Overview')

@section('content')

{{-- Stats Cards --}}
<div class="grid grid-cols-2 lg:grid-cols-3 xl:grid-cols-6 gap-6 mb-10">
    @php
        $stats = [
            ['label' => 'Total Orders',    'value' => $totalOrders,    'icon' => '📦', 'link' => route('admin.orders')],
            ['label' => 'Total Revenue',   'value' => '৳' . number_format($totalRevenue), 'icon' => '💰', 'link' => route('admin.orders')],
            ['label' => 'Total Products',  'value' => $totalProducts,  'icon' => '👔', 'link' => route('admin.products')],
            ['label' => 'Total Customers', 'value' => $totalCustomers, 'icon' => '👤', 'link' => route('admin.users')],
            ['label' => 'Points Issued',   'value' => number_format($totalPointsIssued), 'icon' => '⭐', 'link' => route('admin.loyalty.index')],
            ['label' => 'Points Redeemed', 'value' => number_format($totalPointsRedeemed), 'icon' => '🎫', 'link' => route('admin.loyalty.index')],
        ];
    @endphp
    
    @foreach($stats as $stat)
        <a href="{{ $stat['link'] }}" class="bg-white border border-gray-100 p-6 flex flex-col hover:border-[#C9A84C] transition-colors shadow-sm group">
            <div class="flex justify-between items-start mb-4">
                <span class="text-[10px] font-bold uppercase tracking-widest text-gray-400 group-hover:text-[#1A1A1A] transition-colors">{{ $stat['label'] }}</span>
                <span class="text-[#C9A84C] text-xl grayscale group-hover:grayscale-0 transition-all">{{ $stat['icon'] }}</span>
            </div>
            <p class="text-3xl font-bold text-[#1A1A1A] mt-auto">{{ $stat['value'] }}</p>
        </a>
    @endforeach
</div>


<div class="grid lg:grid-cols-3 gap-8">
    
    {{-- Recent Orders Table --}}
    <div class="lg:col-span-2 bg-white border border-gray-100 shadow-sm flex flex-col">
        <div class="flex items-center justify-between px-6 py-5 border-b border-gray-100">
            <h2 class="text-xs font-bold uppercase tracking-widest text-[#1A1A1A]">Recent Orders</h2>
            <a href="{{ route('admin.orders') }}" class="text-[10px] font-bold uppercase tracking-widest text-[#C9A84C] hover:text-[#1A1A1A] transition-colors">View All &rarr;</a>
        </div>
        
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-[#F8F8F8] border-b border-gray-100">
                        <th class="py-3 px-6 text-[10px] font-bold uppercase tracking-widest text-gray-500">Order</th>
                        <th class="py-3 px-6 text-[10px] font-bold uppercase tracking-widest text-gray-500">Date</th>
                        <th class="py-3 px-6 text-[10px] font-bold uppercase tracking-widest text-gray-500">Amount</th>
                        <th class="py-3 px-6 text-[10px] font-bold uppercase tracking-widest text-gray-500">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($recentOrders as $order)
                        <tr class="hover:bg-[#FFF9EC] transition-colors">
                            <td class="py-4 px-6">
                                <a href="{{ route('admin.orders.show', $order->id) }}" class="text-sm font-bold text-[#1A1A1A] hover:text-[#C9A84C] transition-colors">
                                    #{{ $order->order_number }}
                                </a>
                            </td>
                            <td class="py-4 px-6 text-xs text-gray-500 truncate">
                                {{ $order->created_at->format('M d, Y') }}
                            </td>
                            <td class="py-4 px-6 text-sm font-bold text-[#1A1A1A]">
                                ৳{{ number_format($order->final_amount) }}
                            </td>
                            <td class="py-4 px-6">
                                <span class="inline-flex items-center px-2 py-1 border border-[#C9A84C]/20 bg-[#C9A84C]/10 text-[#C9A84C] text-[9px] font-bold uppercase tracking-widest">
                                    {{ $order->status }}
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="py-10 text-center text-xs text-gray-500 uppercase tracking-widest">No recent orders</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>


    {{-- Top Products List --}}
    <div class="bg-white border border-gray-100 shadow-sm flex flex-col">
        <div class="flex items-center justify-between px-6 py-5 border-b border-gray-100">
            <h2 class="text-xs font-bold uppercase tracking-widest text-[#1A1A1A]">Top Products</h2>
            <a href="{{ route('admin.products') }}" class="text-[10px] font-bold uppercase tracking-widest text-[#C9A84C] hover:text-[#1A1A1A] transition-colors">View All</a>
        </div>
        
        <div class="p-6 space-y-6">
            @forelse($topProducts as $product)
                <div class="flex items-center gap-4 group">
                    <div class="w-16 h-20 bg-[#F8F8F8] border border-gray-100 shrink-0 group-hover:border-[#C9A84C] transition-colors">
                        @if(is_array($product->images) && count($product->images) > 0)
                            <img src="{{ $product->images[0] }}" alt="{{ $product->name }}" class="w-full h-full object-cover">
                        @else
                            <div class="w-full h-full flex items-center justify-center text-gray-400 text-xs">NO IMG</div>
                        @endif
                    </div>
                    
                    <div class="flex-1 min-w-0">
                        <a href="{{ route('admin.products.edit', $product->id) }}" class="block">
                            <h3 class="text-xs font-bold uppercase tracking-widest text-[#1A1A1A] line-clamp-1 group-hover:text-[#C9A84C] transition-colors">{{ $product->name }}</h3>
                        </a>
                        <p class="text-[9px] font-bold uppercase tracking-widest text-gray-400 mt-1 mb-2">{{ $product->category }}</p>
                        <div class="flex items-center justify-between">
                            <span class="text-sm font-bold text-[#1A1A1A]">৳{{ number_format($product->price) }}</span>
                            <span class="text-[9px] font-bold uppercase tracking-widest {{ $product->stock > 0 ? 'text-green-600' : 'text-red-500' }}">
                                Stock: {{ $product->stock }}
                            </span>
                        </div>
                    </div>
                </div>
            @empty
                <div class="py-10 text-center text-xs text-gray-500 uppercase tracking-widest">No products available</div>
            @endforelse
        </div>
    </div>

</div>

@endsection
