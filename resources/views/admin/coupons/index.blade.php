@extends('layouts.admin')

@section('title', 'Coupons')
@section('page-title', 'Coupons')

@section('content')
<div x-data="{ tableSearch: '' }" class="bg-white border border-gray-100 shadow-sm flex flex-col mb-8">
    {{-- TABLE HEADER BAR --}}
    <div class="flex flex-col sm:flex-row items-center justify-between px-6 py-5 border-b border-gray-100 gap-4">
        <div class="flex items-center gap-2">
            <h2 class="text-xs font-bold uppercase tracking-widest text-[#1A1A1A]">All Coupons</h2>
            <span class="bg-[#C9A84C]/10 text-[#C9A84C] text-[10px] font-bold px-2 py-0.5 rounded-full">{{ count($coupons) }}</span>
        </div>
        <div class="flex flex-wrap items-center gap-3 w-full sm:w-auto">
            <div class="relative flex-1 sm:w-64">
                <svg class="w-4 h-4 absolute left-3 top-1/2 -translate-y-1/2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                <input type="text" x-model="tableSearch" placeholder="Filter locally..." class="w-full pl-9 pr-4 py-2 text-xs border border-gray-200 focus:outline-none focus:border-[#C9A84C] text-[#1A1A1A] placeholder-gray-400 transition-colors">
            </div>
            <a href="{{ route('admin.coupons.create') }}" class="btn-admin-primary whitespace-nowrap">+ Add New</a>
        </div>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr>
                    <th class="py-4 px-6 text-[10px] font-bold uppercase tracking-widest text-[#1A1A1A] bg-[#F5F5F5] border-b border-[#C9A84C]">Code</th>
                    <th class="py-4 px-6 text-[10px] font-bold uppercase tracking-widest text-[#1A1A1A] bg-[#F5F5F5] border-b border-[#C9A84C]">Type / Value</th>
                    <th class="py-4 px-6 text-[10px] font-bold uppercase tracking-widest text-[#1A1A1A] bg-[#F5F5F5] border-b border-[#C9A84C]">Min Order</th>
                    <th class="py-4 px-6 text-[10px] font-bold uppercase tracking-widest text-[#1A1A1A] bg-[#F5F5F5] border-b border-[#C9A84C]">Uses</th>
                    <th class="py-4 px-6 text-[10px] font-bold uppercase tracking-widest text-[#1A1A1A] bg-[#F5F5F5] border-b border-[#C9A84C]">Expires</th>
                    <th class="py-4 px-6 text-[10px] font-bold uppercase tracking-widest text-[#1A1A1A] bg-[#F5F5F5] border-b border-[#C9A84C]">Status</th>
                    <th class="py-4 px-6 text-[10px] font-bold uppercase tracking-widest text-[#1A1A1A] bg-[#F5F5F5] border-b border-[#C9A84C] text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($coupons as $coupon)
                @php
                    $valid   = $coupon->isValid();
                    $expired = $coupon->expires_at && $coupon->expires_at->isPast();
                    $maxed   = $coupon->max_uses !== null && $coupon->times_used >= $coupon->max_uses;
                @endphp
                    <tr class="bg-white hover:bg-[#FFFBF0] transition-colors duration-200" x-show="tableSearch === '' || $el.innerText.toLowerCase().includes(tableSearch.toLowerCase())">
                        <td class="py-4 px-6">
                            <span class="inline-block border border-gray-200 bg-[#F8F8F8] px-2 py-1 text-xs font-bold text-[#1A1A1A] uppercase tracking-widest">{{ $coupon->code }}</span>
                        </td>
                        <td class="py-4 px-6">
                            @if($coupon->type === 'fixed')
                                <span class="text-sm font-bold text-[#1A1A1A]">৳{{ number_format($coupon->value) }} OFF</span>
                            @else
                                <span class="text-sm font-bold text-[#1A1A1A]">{{ $coupon->value }}% OFF</span>
                                @if($coupon->max_discount)
                                    <span class="block text-[9px] text-gray-400 uppercase tracking-widest mt-1">MAX ৳{{ number_format($coupon->max_discount) }}</span>
                                @endif
                            @endif
                        </td>
                        <td class="py-4 px-6">
                            <span class="text-xs text-gray-500 font-medium">
                                {{ $coupon->min_order > 0 ? '৳' . number_format($coupon->min_order) : '—' }}
                            </span>
                        </td>
                        <td class="py-4 px-6 text-sm font-bold text-[#1A1A1A]">
                            {{ $coupon->times_used }}
                            <span class="text-[10px] text-gray-400 font-normal"> / {{ $coupon->max_uses !== null ? $coupon->max_uses : '∞' }}</span>
                        </td>
                        <td class="py-4 px-6">
                            @if($coupon->expires_at)
                                <span class="text-xs font-medium {{ $expired ? 'text-red-500' : 'text-gray-500' }}">
                                    {{ $coupon->expires_at->format('M d, Y') }}
                                </span>
                            @else
                                <span class="text-xs font-medium text-gray-400 uppercase tracking-widest">Never</span>
                            @endif
                        </td>
                        <td class="py-4 px-6">
                            @if(!$coupon->is_active)
                                <span class="inline-flex items-center px-2 py-1 rounded-full text-[9px] font-bold uppercase tracking-widest bg-gray-100 text-gray-500">Inactive</span>
                            @elseif($expired)
                                <span class="inline-flex items-center px-2 py-1 rounded-full text-[9px] font-bold uppercase tracking-widest bg-red-50 text-red-600">Expired</span>
                            @elseif($maxed)
                                <span class="inline-flex items-center px-2 py-1 rounded-full text-[9px] font-bold uppercase tracking-widest bg-orange-50 text-orange-600">Maxed</span>
                            @else
                                <span class="inline-flex items-center px-2 py-1 rounded-full text-[9px] font-bold uppercase tracking-widest bg-green-50 text-green-600">Active</span>
                            @endif
                        </td>
                        <td class="py-4 px-6 text-right">
                            <div class="flex items-center justify-end gap-2">
                                <a href="{{ route('admin.coupons.edit', $coupon->id) }}" class="inline-block px-3 py-1.5 text-[10px] uppercase tracking-widest font-bold border border-gray-200 text-[#1A1A1A] hover:bg-gray-50 transition-colors">Edit</a>
                                <form method="POST" action="{{ route('admin.coupons.destroy', $coupon->id) }}" onsubmit="return confirm('Delete coupon {{ $coupon->code }}?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="inline-block px-3 py-1.5 text-[10px] uppercase tracking-widest font-bold border border-red-200 text-red-500 hover:bg-red-50 transition-colors">Delete</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="py-16 text-center">
                            <div class="flex flex-col items-center justify-center">
                                <span class="text-4xl mb-3 grayscale opacity-60">🎟️</span>
                                <p class="text-xs text-gray-400 font-bold uppercase tracking-widest">No coupons found</p>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="p-6 border-t border-gray-100 text-center">
        <!-- Assume standard pagination output or just list end padding for short tables -->
        @if(method_exists($coupons, 'links'))
            {{ $coupons->links() }}
        @endif
    </div>
</div>

<style>
/* Clean up default Laravel pagination to match minimal style */
nav[role="navigation"] div.hidden.sm\:flex-1.sm\:flex.sm\:items-center.sm\:justify-between { display: flex; flex-direction: column; gap: 1rem; align-items: center; justify-content: center; }
nav[role="navigation"] span.relative.z-0.inline-flex.shadow-sm.rounded-md { box-shadow: none; display: flex; gap: 0.25rem; }
nav[role="navigation"] span.relative.z-0.inline-flex.shadow-sm.rounded-md > span[aria-current="page"] > span { border: 1px solid #C9A84C; background-color: #C9A84C; color: white; border-radius: 0; }
nav[role="navigation"] span.relative.z-0.inline-flex.shadow-sm.rounded-md > a,
nav[role="navigation"] span.relative.z-0.inline-flex.shadow-sm.rounded-md > span { border: 1px solid #e5e7eb; background-color: white; color: #1A1A1A; border-radius: 0; }
nav[role="navigation"] span.relative.z-0.inline-flex.shadow-sm.rounded-md > a:hover { background-color: #F8F8F8; }
</style>
@endsection
