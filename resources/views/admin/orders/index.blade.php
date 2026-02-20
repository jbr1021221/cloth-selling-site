@extends('layouts.admin')

@section('title', 'Orders')
@section('page-title', 'All Orders')

@section('content')
<div class="bg-gray-900 border border-gray-800 rounded-2xl overflow-hidden">
    {{-- Filters --}}
    <div class="p-5 border-b border-gray-800 flex flex-wrap gap-3">
        <form method="GET" action="{{ route('admin.orders') }}" class="flex flex-wrap gap-3">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Search order # or name..."
                   class="input text-sm w-56">
            <select name="status" class="input text-sm w-40" onchange="this.form.submit()">
                <option value="">All Status</option>
                @foreach(['pending','processing','shipped','delivered','cancelled'] as $s)
                    <option value="{{ $s }}" {{ request('status') === $s ? 'selected' : '' }}>{{ ucfirst($s) }}</option>
                @endforeach
            </select>
            <button type="submit" class="btn-primary text-sm px-4">Filter</button>
            @if(request()->hasAny(['search','status']))
                <a href="{{ route('admin.orders') }}" class="btn-outline text-sm px-4">Clear</a>
            @endif
        </form>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full">
            <thead>
                <tr>
                    <th class="table-head">Order</th>
                    <th class="table-head">Customer</th>
                    <th class="table-head">Amount</th>
                    <th class="table-head">Payment</th>
                    <th class="table-head">Status</th>
                    <th class="table-head">Date</th>
                    <th class="table-head">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($orders as $order)
                    <tr class="hover:bg-gray-800/30 transition-colors">
                        <td class="table-cell">
                            <a href="{{ route('admin.orders.show', $order->id) }}" class="font-mono text-indigo-400 hover:underline">#{{ $order->order_number }}</a>
                        </td>
                        <td class="table-cell">{{ $order->user?->name ?? 'Guest' }}</td>
                        <td class="table-cell font-semibold text-white">৳{{ number_format($order->final_amount) }}</td>
                        <td class="table-cell">
                            <span class="text-xs text-gray-400 uppercase">{{ $order->payment_method }}</span>
                            <br>
                            <span class="{{ $order->payment_status === 'paid' ? 'text-emerald-400' : 'text-yellow-400' }} text-xs">{{ ucfirst($order->payment_status) }}</span>
                        </td>
                        <td class="table-cell">
                            <form method="POST" action="{{ route('admin.orders.updateStatus', $order->id) }}">
                                @csrf @method('PATCH')
                                <select name="status" onchange="this.form.submit()"
                                        class="bg-gray-800 border border-gray-700 text-gray-300 text-xs rounded-lg px-2 py-1 focus:outline-none focus:border-indigo-500">
                                    @foreach(['pending','processing','shipped','delivered','cancelled'] as $s)
                                        <option value="{{ $s }}" {{ $order->status === $s ? 'selected' : '' }}>{{ ucfirst($s) }}</option>
                                    @endforeach
                                </select>
                            </form>
                        </td>
                        <td class="table-cell text-gray-500 text-xs">{{ $order->created_at->format('d M Y') }}</td>
                        <td class="table-cell">
                            <a href="{{ route('admin.orders.show', $order->id) }}" class="text-indigo-400 hover:underline text-sm">View</a>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="7" class="table-cell text-center text-gray-600 py-12">No orders found</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="p-5 border-t border-gray-800">
        {{ $orders->withQueryString()->links() }}
    </div>
</div>
@endsection
