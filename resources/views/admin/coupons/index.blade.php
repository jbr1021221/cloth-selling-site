@extends('layouts.app')
@section('title', 'Manage Coupons — Admin')

@section('content')
<div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-10">

    {{-- Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-8">
        <div>
            <h1 class="text-3xl font-bold text-white">Coupon Codes</h1>
            <p class="text-gray-400 mt-1">Create and manage discount coupons</p>
        </div>
        <a href="{{ route('admin.coupons.create') }}" class="btn-primary flex items-center gap-2">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            Create Coupon
        </a>
    </div>

    {{-- Flash messages --}}
    @if(session('success'))
        <div class="mb-6 p-4 bg-emerald-500/15 border border-emerald-500/30 rounded-xl text-emerald-400 text-sm">
            ✅ {{ session('success') }}
        </div>
    @endif

    {{-- Table --}}
    @if($coupons->count() > 0)
    <div class="bg-gray-900 border border-gray-800 rounded-2xl overflow-hidden">
        <table class="w-full text-sm">
            <thead>
                <tr class="border-b border-gray-800 text-xs text-gray-500 uppercase tracking-wider">
                    <th class="px-6 py-4 text-left">Code</th>
                    <th class="px-6 py-4 text-left">Type / Value</th>
                    <th class="px-6 py-4 text-left">Min Order</th>
                    <th class="px-6 py-4 text-left">Uses</th>
                    <th class="px-6 py-4 text-left">Expires</th>
                    <th class="px-6 py-4 text-left">Status</th>
                    <th class="px-6 py-4 text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-800">
                @foreach($coupons as $coupon)
                @php
                    $valid   = $coupon->isValid();
                    $expired = $coupon->expires_at && $coupon->expires_at->isPast();
                    $maxed   = $coupon->max_uses !== null && $coupon->times_used >= $coupon->max_uses;
                @endphp
                <tr class="hover:bg-gray-800/50 transition-colors">
                    <td class="px-6 py-4">
                        <span class="font-mono font-bold text-white bg-gray-800 px-2 py-1 rounded-lg text-sm">{{ $coupon->code }}</span>
                    </td>
                    <td class="px-6 py-4">
                        @if($coupon->type === 'fixed')
                            <span class="text-indigo-400 font-semibold">৳{{ number_format($coupon->value) }} off</span>
                        @else
                            <span class="text-indigo-400 font-semibold">{{ $coupon->value }}% off</span>
                            @if($coupon->max_discount)
                                <span class="text-gray-600 text-xs ml-1">(max ৳{{ number_format($coupon->max_discount) }})</span>
                            @endif
                        @endif
                    </td>
                    <td class="px-6 py-4">
                        <span class="text-gray-400">
                            {{ $coupon->min_order > 0 ? '৳' . number_format($coupon->min_order) : '—' }}
                        </span>
                    </td>
                    <td class="px-6 py-4">
                        <span class="text-gray-300">{{ $coupon->times_used }}</span>
                        @if($coupon->max_uses !== null)
                            <span class="text-gray-600"> / {{ $coupon->max_uses }}</span>
                        @else
                            <span class="text-gray-600"> / ∞</span>
                        @endif
                    </td>
                    <td class="px-6 py-4">
                        @if($coupon->expires_at)
                            <span class="{{ $expired ? 'text-red-400' : 'text-gray-400' }}">
                                {{ $coupon->expires_at->format('d M Y') }}
                            </span>
                        @else
                            <span class="text-gray-600">Never</span>
                        @endif
                    </td>
                    <td class="px-6 py-4">
                        @if(!$coupon->is_active)
                            <span class="badge bg-gray-700 text-gray-400 border-gray-600">Inactive</span>
                        @elseif($expired)
                            <span class="badge bg-red-500/20 text-red-400 border-red-500/30">Expired</span>
                        @elseif($maxed)
                            <span class="badge bg-amber-500/20 text-amber-400 border-amber-500/30">Maxed</span>
                        @else
                            <span class="badge bg-emerald-500/20 text-emerald-400 border-emerald-500/30">Active</span>
                        @endif
                    </td>
                    <td class="px-6 py-4">
                        <div class="flex items-center justify-end gap-2">
                            <a href="{{ route('admin.coupons.edit', $coupon->id) }}"
                               class="text-xs text-indigo-400 hover:text-indigo-300 transition-colors font-medium px-3 py-1.5 rounded-lg border border-indigo-500/30 hover:border-indigo-500/60">
                                Edit
                            </a>
                            <form method="POST" action="{{ route('admin.coupons.destroy', $coupon->id) }}"
                                  onsubmit="return confirm('Delete coupon {{ $coupon->code }}?')">
                                @csrf @method('DELETE')
                                <button type="submit"
                                        class="text-xs text-red-400 hover:text-red-300 transition-colors font-medium px-3 py-1.5 rounded-lg border border-red-500/30 hover:border-red-500/60">
                                    Delete
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <div class="mt-6">{{ $coupons->links() }}</div>

    @else
    <div class="bg-gray-900 border border-gray-800 rounded-2xl p-16 text-center">
        <div class="text-5xl mb-4">🎟️</div>
        <h2 class="text-xl font-bold text-white mb-2">No coupons yet</h2>
        <p class="text-gray-500 mb-6">Create your first promo code to offer discounts to customers.</p>
        <a href="{{ route('admin.coupons.create') }}" class="btn-primary inline-flex items-center gap-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            Create First Coupon
        </a>
    </div>
    @endif

</div>
@endsection
