@extends('layouts.admin')

@section('title', 'Dashboard')
@section('page-title', 'Dashboard')

@section('content')
{{-- Stats Cards --}}
<div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
    @php
        $stats = [
            ['label' => 'Total Orders',    'value' => $totalOrders,    'icon' => '📦', 'color' => 'indigo', 'link' => route('admin.orders')],
            ['label' => 'Total Revenue',   'value' => '৳' . number_format($totalRevenue), 'icon' => '💰', 'color' => 'emerald', 'link' => route('admin.orders')],
            ['label' => 'Total Products',  'value' => $totalProducts,  'icon' => '👔', 'color' => 'purple', 'link' => route('admin.products')],
            ['label' => 'Total Customers', 'value' => $totalCustomers, 'icon' => '👤', 'color' => 'blue',   'link' => route('admin.users')],
            ['label' => 'Active Coupons',  'value' => \App\Models\Coupon::where('is_active', true)->count(), 'icon' => '🎟️', 'color' => 'amber', 'link' => route('admin.coupons.index')],
        ];
    @endphp
    @foreach($stats as $stat)
        <div class="bg-gray-900 border border-gray-800 rounded-2xl p-5 hover:border-indigo-500/30 transition-colors">
            <div class="flex items-center justify-between mb-3">
                <span class="text-2xl">{{ $stat['icon'] }}</span>
            </div>
            <p class="text-2xl font-bold text-white mb-1">{{ $stat['value'] }}</p>
            <p class="text-sm text-gray-500">{{ $stat['label'] }}</p>
        </div>
    @endforeach
</div>

<div class="grid lg:grid-cols-2 gap-6">
    {{-- Recent Orders --}}
    <div class="bg-gray-900 border border-gray-800 rounded-2xl overflow-hidden">
        <div class="flex items-center justify-between p-5 border-b border-gray-800">
            <h2 class="font-semibold text-white">Recent Orders</h2>
            <a href="{{ route('admin.orders') }}" class="text-sm text-indigo-400 hover:underline">View All</a>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr>
                        <th class="table-head">Order</th>
                        <th class="table-head">Amount</th>
                        <th class="table-head">Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($recentOrders as $order)
                        <tr class="hover:bg-gray-800/50 transition-colors">
                            <td class="table-cell">
                                <a href="{{ route('admin.orders.show', $order->id) }}" class="font-mono text-indigo-400 hover:underline">#{{ $order->order_number }}</a>
                                <p class="text-xs text-gray-600">{{ $order->created_at->diffForHumans() }}</p>
                            </td>
                            <td class="table-cell font-semibold text-white">৳{{ number_format($order->final_amount) }}</td>
                            <td class="table-cell">
                                @php
                                    $c = ['pending'=>'yellow','processing'=>'blue','shipped'=>'purple','delivered'=>'emerald','cancelled'=>'red'];
                                    $col = $c[$order->status] ?? 'gray';
                                @endphp
                                <span class="badge bg-{{ $col }}-500/20 text-{{ $col }}-400 border border-{{ $col }}-500/30">{{ ucfirst($order->status) }}</span>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="3" class="table-cell text-center text-gray-600 py-8">No orders yet</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Top Products --}}
    <div class="bg-gray-900 border border-gray-800 rounded-2xl overflow-hidden">
        <div class="flex items-center justify-between p-5 border-b border-gray-800">
            <h2 class="font-semibold text-white">Top Products</h2>
            <a href="{{ route('admin.products') }}" class="text-sm text-indigo-400 hover:underline">View All</a>
        </div>
        <div class="p-5 space-y-4">
            @forelse($topProducts as $product)
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 bg-gray-800 rounded-xl overflow-hidden flex-shrink-0">
                        @if(is_array($product->images) && count($product->images) > 0)
                            <img src="{{ $product->images[0] }}" alt="{{ $product->name }}" class="w-full h-full object-cover">
                        @else
                            <div class="w-full h-full flex items-center justify-center text-xl">👔</div>
                        @endif
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-medium text-white truncate">{{ $product->name }}</p>
                        <p class="text-xs text-gray-500">{{ $product->category }} • Stock: {{ $product->stock }}</p>
                    </div>
                    <p class="text-sm font-bold text-indigo-400 flex-shrink-0">৳{{ number_format($product->price) }}</p>
                </div>
            @empty
                <p class="text-gray-600 text-sm text-center py-8">No products yet</p>
            @endforelse
        </div>
    </div>
</div>
@endsection
