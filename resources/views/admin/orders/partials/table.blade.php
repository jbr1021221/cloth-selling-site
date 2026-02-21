<div class="overflow-x-auto">
    <table class="w-full text-left border-collapse">
        <thead>
            <tr>
                <th class="py-4 px-4 bg-gray-50 border-b border-gray-200 w-12">
                    <input type="checkbox" @change="toggleAll" :checked="allSelected" class="rounded border-gray-300 text-[#C9A84C] focus:ring-[#C9A84C]">
                </th>
                <th class="py-4 px-4 text-[9px] font-bold uppercase tracking-widest text-gray-500 bg-gray-50 border-b border-gray-200">Order ID</th>
                <th class="py-4 px-4 text-[9px] font-bold uppercase tracking-widest text-gray-500 bg-gray-50 border-b border-gray-200">Date</th>
                <th class="py-4 px-4 text-[9px] font-bold uppercase tracking-widest text-gray-500 bg-gray-50 border-b border-gray-200">Customer</th>
                <th class="py-4 px-4 text-[9px] font-bold uppercase tracking-widest text-gray-500 bg-gray-50 border-b border-gray-200">Amount</th>
                <th class="py-4 px-4 text-[9px] font-bold uppercase tracking-widest text-gray-500 bg-gray-50 border-b border-gray-200">Payment</th>
                <th class="py-4 px-4 text-[9px] font-bold uppercase tracking-widest text-gray-500 bg-gray-50 border-b border-gray-200">Status</th>
                <th class="py-4 px-4 text-[9px] font-bold uppercase tracking-widest text-gray-500 bg-gray-50 border-b border-gray-200 text-right">Actions</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100 bg-white" id="orders-tbody">
            @forelse($orders as $order)
                @php
                    $address = $order->delivery_address ?? [];
                    $statusColors = [
                        'pending'    => 'bg-yellow-50 text-yellow-600 border border-yellow-200',
                        'processing' => 'bg-blue-50 text-blue-600 border border-blue-200',
                        'shipped'    => 'bg-purple-50 text-purple-600 border border-purple-200',
                        'delivered'  => 'bg-green-50 text-green-600 border border-green-200',
                        'cancelled'  => 'bg-red-50 text-red-600 border border-red-200',
                    ];
                    $badgeClass = $statusColors[$order->status] ?? 'bg-gray-50 text-gray-600 border border-gray-200';
                @endphp
                <tr class="hover:bg-gray-50 transition-colors">
                    <td class="py-4 px-4">
                        <input type="checkbox" value="{{ $order->id }}" x-model="selectedOrders" class="rounded border-gray-300 text-[#C9A84C] focus:ring-[#C9A84C]">
                    </td>
                    <td class="py-4 px-4">
                        <a href="{{ route('admin.orders.show', $order->id) }}" class="text-xs font-bold text-[#1A1A1A] hover:text-[#C9A84C] transition-colors">
                            #{{ $order->order_number }}
                        </a>
                    </td>
                    <td class="py-4 px-4 text-[11px] text-gray-500">
                        {{ $order->created_at->format('M d, Y h:i A') }}
                    </td>
                    <td class="py-4 px-4">
                        <p class="text-[11px] font-bold text-[#1A1A1A] mb-0.5">{{ $address['name'] ?? 'N/A' }}</p>
                        <p class="text-[10px] text-gray-500">{{ $address['phone'] ?? 'N/A' }}</p>
                    </td>
                    <td class="py-4 px-4 text-xs font-bold text-[#C9A84C]">
                        ৳{{ number_format($order->final_amount) }}
                    </td>
                    <td class="py-4 px-4">
                        <span class="text-[9px] uppercase tracking-widest font-bold {{ $order->payment_status === 'paid' ? 'text-green-600' : 'text-gray-500' }}">
                            {{ $order->payment_method }} ({{ $order->payment_status }})
                        </span>
                    </td>
                    <td class="py-4 px-4">
                        <span class="inline-flex px-2 py-0.5 text-[9px] font-bold uppercase tracking-widest rounded-full {{ $badgeClass }}">
                            {{ $order->status }}
                        </span>
                    </td>
                    <td class="py-4 px-4 text-right">
                        <a href="{{ route('admin.orders.show', $order->id) }}" class="text-[9px] font-bold uppercase tracking-widest text-[#1A1A1A] hover:text-[#C9A84C] underline underline-offset-4 decoration-gray-200 hover:decoration-[#C9A84C] transition-colors mr-3">View</a>
                        <form method="POST" action="{{ route('admin.orders.updateStatus', $order->id) }}" class="inline-block relative" x-data="{ open: false }" @click.away="open = false">
                            @csrf
                            @method('PATCH')
                            <button type="button" @click="open = !open" class="text-[9px] font-bold uppercase tracking-widest text-[#1A1A1A] hover:text-[#C9A84C] underline underline-offset-4 decoration-gray-200 hover:decoration-[#C9A84C] transition-colors">
                                Status
                            </button>
                            <div x-show="open" class="absolute right-0 mt-1 w-32 bg-white border border-gray-100 shadow-lg z-10 text-left" style="display: none;">
                                @foreach(['pending', 'processing', 'shipped', 'delivered', 'cancelled'] as $st)
                                    <button type="submit" name="status" value="{{ $st }}" class="block w-full text-left px-4 py-2 text-[10px] uppercase tracking-widest {{ $order->status === $st ? 'bg-[#C9A84C] text-white font-bold' : 'text-gray-600 hover:bg-gray-50 hover:text-[#1A1A1A]' }}">
                                        {{ ucfirst($st) }}
                                    </button>
                                @endforeach
                            </div>
                        </form>
                    </td>
                </tr>
            @empty
                <tr><td colspan="8" class="py-12 px-4 text-center text-xs text-gray-500 font-bold uppercase tracking-widest">No orders found matching criteria.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>

@if($orders->hasPages())
<div class="p-4 border-t border-gray-100 bg-gray-50">
    {{ $orders->links('pagination::tailwind') }}
</div>
@endif
