@extends('layouts.admin')

@section('title', 'Manage Loyalty')
@section('page-title', 'Loyalty Points')

@section('content')
<div class="bg-white border border-gray-100 shadow-sm flex flex-col mb-8">
    <div class="flex flex-col sm:flex-row items-center justify-between px-6 py-5 border-b border-gray-100 gap-4">
        <div class="flex items-center gap-2">
            <h2 class="text-xs font-bold uppercase tracking-widest text-[#1A1A1A]">Customer Balances</h2>
            <span class="bg-[#C9A84C]/10 text-[#C9A84C] text-[10px] font-bold px-2 py-0.5 rounded-full">{{ $users->total() }}</span>
        </div>
        <form method="GET" action="{{ route('admin.loyalty.index') }}" class="flex items-center gap-3">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Search Customer..." class="border border-gray-200 px-3 py-1.5 text-[11px] focus:outline-none focus:border-[#C9A84C] w-64 bg-white text-[#1A1A1A]">
            <button type="submit" class="bg-white border border-gray-200 text-[#1A1A1A] hover:bg-gray-50 text-[10px] uppercase font-bold tracking-widest py-1.5 px-4 transition-colors">Search</button>
            @if(request('search'))
                <a href="{{ route('admin.loyalty.index') }}" class="text-[10px] uppercase tracking-widest text-red-500 hover:underline border border-transparent px-2">Clear</a>
            @endif
        </form>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr>
                    <th class="py-4 px-6 text-[10px] font-bold uppercase tracking-widest text-[#1A1A1A] bg-[#F5F5F5] border-b border-[#C9A84C]">Customer</th>
                    <th class="py-4 px-6 text-[10px] font-bold uppercase tracking-widest text-[#1A1A1A] bg-[#F5F5F5] border-b border-[#C9A84C]">Email</th>
                    <th class="py-4 px-6 text-[10px] font-bold uppercase tracking-widest text-[#1A1A1A] bg-[#F5F5F5] border-b border-[#C9A84C] text-right">Points Balance</th>
                    <th class="py-4 px-6 text-[10px] font-bold uppercase tracking-widest text-[#1A1A1A] bg-[#F5F5F5] border-b border-[#C9A84C] w-1/3">Modify Points</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($users as $user)
                    <tr class="bg-white hover:bg-[#FFFBF0] transition-colors duration-200">
                        <td class="py-4 px-6 text-sm font-bold text-[#1A1A1A]">{{ $user->name }}</td>
                        <td class="py-4 px-6 text-sm text-gray-500">{{ $user->email }}</td>
                        <td class="py-4 px-6 text-right">
                            <span class="text-xl font-bold {{ $user->total_points > 0 ? 'text-[#C9A84C]' : 'text-gray-400' }}">{{ number_format($user->total_points) }}</span>
                        </td>
                        <td class="py-4 px-6">
                            <form method="POST" action="{{ route('admin.loyalty.store', $user->id) }}" class="flex gap-2">
                                @csrf
                                <input type="number" name="points" placeholder="+/- Pts" class="w-24 text-xs bg-white border border-gray-200 px-2 py-1.5 text-[#1A1A1A] focus:outline-none focus:border-[#C9A84C]" required>
                                <input type="text" name="description" placeholder="Reason (e.g. Refund)..." class="flex-1 text-xs bg-white border border-gray-200 px-2 py-1.5 text-[#1A1A1A] focus:outline-none focus:border-[#C9A84C]" required>
                                <button type="submit" class="bg-[#C9A84C] hover:bg-[#b08a38] text-white text-[10px] font-bold uppercase tracking-widest py-1.5 px-4 transition-colors">Apply</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="py-16 text-center">
                            <span class="text-4xl mb-3 grayscale opacity-60">🎫</span>
                            <p class="text-xs text-gray-400 font-bold uppercase tracking-widest">No customers found</p>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="p-6 border-t border-gray-100">
        {{ $users->withQueryString()->links() }}
    </div>
</div>

<style>
/* Minimal Laravel paginator */
nav[role="navigation"] div.hidden.sm\:flex-1.sm\:flex.sm\:items-center.sm\:justify-between { display: flex; flex-direction: column; gap: 1rem; align-items: center; justify-content: center; }
nav[role="navigation"] span.relative.z-0.inline-flex.shadow-sm.rounded-md { box-shadow: none; display: flex; gap: 0.25rem; }
nav[role="navigation"] span.relative.z-0.inline-flex.shadow-sm.rounded-md > span[aria-current="page"] > span { border: 1px solid #C9A84C; background-color: #C9A84C; color: white; border-radius: 0; }
nav[role="navigation"] span.relative.z-0.inline-flex.shadow-sm.rounded-md > a,
nav[role="navigation"] span.relative.z-0.inline-flex.shadow-sm.rounded-md > span { border: 1px solid #e5e7eb; background-color: white; color: #1A1A1A; border-radius: 0; }
nav[role="navigation"] span.relative.z-0.inline-flex.shadow-sm.rounded-md > a:hover { background-color: #F8F8F8; }
</style>
@endsection
