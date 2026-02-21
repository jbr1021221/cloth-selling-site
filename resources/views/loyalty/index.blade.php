@extends('layouts.app')

@section('title', 'My Loyalty Points')

@section('content')
<div class="max-w-4xl mx-auto px-4 py-16">
    <div class="text-center mb-12">
        <h1 class="playfair text-3xl font-bold uppercase tracking-widest text-[#1A1A1A] mb-4">Loyalty Rewards</h1>
        <p class="text-xs uppercase tracking-widest text-gray-500 font-bold">Earn points, unlock exclusives.</p>
    </div>

    {{-- Balance Card --}}
    <div class="border flex flex-col items-center justify-center p-12 bg-white text-center mb-12 relative overflow-hidden group hover:border-[#C9A84C] transition-colors shadow-sm {{ $user->total_points > 0 ? 'border-[#C9A84C]' : 'border-gray-200' }}">
        @if($user->total_points > 0)
        <div class="absolute -top-16 -right-16 w-32 h-32 bg-[#C9A84C]/5 rounded-full blur-2xl group-hover:bg-[#C9A84C]/10 transition-colors"></div>
        @endif
        
        <span class="text-[10px] font-bold uppercase tracking-[0.2em] text-gray-400 mb-6">Current Balance</span>
        <div class="flex items-baseline justify-center gap-2 mb-4">
            <span class="text-6xl sm:text-7xl font-bold text-[#C9A84C] tracking-tighter">{{ number_format($user->total_points) }}</span>
            <span class="text-sm font-bold uppercase tracking-widest text-[#1A1A1A]">PTS</span>
        </div>
        
        <p class="text-[11px] font-bold uppercase tracking-widest text-[#1A1A1A] bg-[#F8F8F8] px-6 py-2 border border-gray-100">
            Equals <span class="text-[#C9A84C]">৳{{ number_format($user->total_points * 0.1, 2) }}</span> discount value
        </p>
    </div>

    {{-- How to earn --}}
    <div class="grid sm:grid-cols-3 gap-6 mb-16">
        <div class="border border-gray-100 bg-white p-6 text-center shadow-sm">
            <div class="w-12 h-12 bg-[#F8F8F8] flex items-center justify-center mx-auto mb-4 text-xl">🛍️</div>
            <h3 class="text-[11px] font-bold uppercase tracking-widest text-[#1A1A1A] mb-2">Shop</h3>
            <p class="text-xs text-gray-500">1 Point per ৳10 spent</p>
        </div>
        <div class="border border-gray-100 bg-white p-6 text-center shadow-sm">
            <div class="w-12 h-12 bg-[#F8F8F8] flex items-center justify-center mx-auto mb-4 text-xl">⭐</div>
            <h3 class="text-[11px] font-bold uppercase tracking-widest text-[#1A1A1A] mb-2">Review</h3>
            <p class="text-xs text-gray-500">10 Points per review</p>
        </div>
        <div class="border border-gray-100 bg-white p-6 text-center shadow-sm">
            <div class="w-12 h-12 bg-[#F8F8F8] flex items-center justify-center mx-auto mb-4 text-xl">🎂</div>
            <h3 class="text-[11px] font-bold uppercase tracking-widest text-[#1A1A1A] mb-2">Birthday</h3>
            <p class="text-xs text-gray-500">100 Points bonus</p>
        </div>
    </div>

    {{-- History --}}
    <div class="bg-white border border-gray-100 shadow-sm">
        <div class="px-6 py-5 border-b border-gray-100">
            <h2 class="text-[11px] font-bold uppercase tracking-widest text-[#1A1A1A]">Points History</h2>
        </div>
        
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr>
                        <th class="py-4 px-6 text-[10px] font-bold uppercase tracking-widest text-[#1A1A1A] bg-[#F8F8F8] border-b border-[#C9A84C]">Date</th>
                        <th class="py-4 px-6 text-[10px] font-bold uppercase tracking-widest text-[#1A1A1A] bg-[#F8F8F8] border-b border-[#C9A84C]">Activity</th>
                        <th class="py-4 px-6 text-[10px] font-bold uppercase tracking-widest text-[#1A1A1A] bg-[#F8F8F8] border-b border-[#C9A84C] text-right">Points</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($points as $point)
                    <tr class="hover:bg-[#FFFBF0] transition-colors duration-200">
                        <td class="py-4 px-6 text-xs text-gray-500">{{ $point->created_at->format('M d, Y') }}</td>
                        <td class="py-4 px-6 text-sm font-bold text-[#1A1A1A]">{{ $point->description }}</td>
                        <td class="py-4 px-6 text-right">
                            @if($point->points > 0)
                                <span class="text-green-600 font-bold text-sm">+{{ $point->points }}</span>
                            @else
                                <span class="text-[#C9A84C] font-bold text-sm">{{ $point->points }}</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="3" class="py-16 text-center">
                            <span class="text-3xl mb-3 grayscale opacity-60">🎫</span>
                            <p class="text-[10px] font-bold uppercase tracking-widest text-gray-400">No history available</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($points->hasPages())
        <div class="p-4 border-t border-gray-100 text-center">
            {{ $points->links() }}
        </div>
        @endif
    </div>
</div>
@endsection
