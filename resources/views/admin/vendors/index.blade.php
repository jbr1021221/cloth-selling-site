@extends('layouts.admin')

@section('title', 'Vendors')
@section('page-title', 'Vendors')

@section('content')
<div x-data="{ tableSearch: '' }" class="bg-white border border-gray-100 shadow-sm flex flex-col mb-8">
    {{-- TABLE HEADER BAR --}}
    <div class="flex flex-col sm:flex-row items-center justify-between px-6 py-5 border-b border-gray-100 gap-4">
        <div class="flex items-center gap-2">
            <h2 class="text-xs font-bold uppercase tracking-widest text-[#1A1A1A]">All Vendors</h2>
            <span class="bg-[#C9A84C]/10 text-[#C9A84C] text-[10px] font-bold px-2 py-0.5 rounded-full">{{ $vendors->total() }}</span>
        </div>
        <div class="flex items-center gap-3 w-full sm:w-auto">
            <div class="relative w-full sm:w-64">
                <svg class="w-4 h-4 absolute left-3 top-1/2 -translate-y-1/2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                <input type="text" x-model="tableSearch" placeholder="Filter visible rows..." class="w-full pl-9 pr-4 py-2 text-xs border border-gray-200 focus:outline-none focus:border-[#C9A84C] text-[#1A1A1A] placeholder-gray-400 transition-colors">
            </div>
            <!-- Vendor addition logic normally goes here, leaving space per specification. -->
        </div>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr>
                    <th class="py-4 px-6 text-[10px] font-bold uppercase tracking-widest text-[#1A1A1A] bg-[#F5F5F5] border-b border-[#C9A84C]">Name</th>
                    <th class="py-4 px-6 text-[10px] font-bold uppercase tracking-widest text-[#1A1A1A] bg-[#F5F5F5] border-b border-[#C9A84C]">Contact</th>
                    <th class="py-4 px-6 text-[10px] font-bold uppercase tracking-widest text-[#1A1A1A] bg-[#F5F5F5] border-b border-[#C9A84C]">Phone</th>
                    <th class="py-4 px-6 text-[10px] font-bold uppercase tracking-widest text-[#1A1A1A] bg-[#F5F5F5] border-b border-[#C9A84C]">Categories</th>
                    <th class="py-4 px-6 text-[10px] font-bold uppercase tracking-widest text-[#1A1A1A] bg-[#F5F5F5] border-b border-[#C9A84C]">Commission</th>
                    <th class="py-4 px-6 text-[10px] font-bold uppercase tracking-widest text-[#1A1A1A] bg-[#F5F5F5] border-b border-[#C9A84C]">Status</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($vendors as $vendor)
                    <tr class="bg-white hover:bg-[#FFFBF0] transition-colors duration-200" x-show="tableSearch === '' || $el.innerText.toLowerCase().includes(tableSearch.toLowerCase())">
                        <td class="py-4 px-6 text-sm font-bold text-[#1A1A1A]">{{ $vendor->name }}</td>
                        <td class="py-4 px-6 text-sm text-gray-500">{{ $vendor->contact_person }}</td>
                        <td class="py-4 px-6 text-sm text-gray-500">{{ $vendor->phone }}</td>
                        <td class="py-4 px-6">
                            <div class="flex flex-wrap gap-1">
                                @if(is_array($vendor->categories))
                                    @foreach($vendor->categories as $cat)
                                        <span class="inline-flex items-center px-1.5 py-0.5 border border-[#C9A84C]/20 bg-[#C9A84C]/5 text-[#1A1A1A] text-[9px] font-bold uppercase tracking-widest">{{ $cat }}</span>
                                    @endforeach
                                @endif
                            </div>
                        </td>
                        <td class="py-4 px-6 text-sm font-bold text-[#C9A84C]">{{ $vendor->commission_rate }}%</td>
                        <td class="py-4 px-6">
                            <span class="inline-flex items-center px-2 py-1 rounded-full text-[9px] font-bold uppercase tracking-widest {{ $vendor->is_active ? 'bg-green-50 text-green-600' : 'bg-red-50 text-red-600' }}">
                                {{ $vendor->is_active ? 'Active' : 'Inactive' }}
                            </span>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="py-16 text-center">
                            <div class="flex flex-col items-center justify-center">
                                <span class="text-4xl mb-3 grayscale opacity-60">🏭</span>
                                <p class="text-xs text-gray-400 font-bold uppercase tracking-widest">No vendors found</p>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="p-6 border-t border-gray-100">
        {{ $vendors->links() }}
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
