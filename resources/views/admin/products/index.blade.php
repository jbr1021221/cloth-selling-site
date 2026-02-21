@extends('layouts.admin')

@section('title', 'Products')
@section('page-title', 'Products')

@section('content')
<div x-data="{ tableSearch: '' }" class="bg-white border border-gray-100 shadow-sm flex flex-col mb-8">
    {{-- TABLE HEADER BAR --}}
    <div class="flex flex-col sm:flex-row items-center justify-between px-6 py-5 border-b border-gray-100 gap-4">
        <div class="flex items-center gap-2">
            <h2 class="text-xs font-bold uppercase tracking-widest text-[#1A1A1A]">All Products</h2>
            <span class="bg-[#C9A84C]/10 text-[#C9A84C] text-[10px] font-bold px-2 py-0.5 rounded-full">{{ $products->total() }}</span>
        </div>
        <div class="flex flex-wrap items-center gap-3 w-full sm:w-auto">
            <div class="relative flex-1 sm:w-64">
                <svg class="w-4 h-4 absolute left-3 top-1/2 -translate-y-1/2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                <input type="text" x-model="tableSearch" placeholder="Filter visible rows..." class="w-full pl-9 pr-4 py-2 text-xs border border-gray-200 focus:outline-none focus:border-[#C9A84C] text-[#1A1A1A] placeholder-gray-400 transition-colors">
            </div>
            <a href="{{ route('admin.products.create') }}" class="btn-admin-primary whitespace-nowrap">+ Add New</a>
        </div>
    </div>

    {{-- Server Filter --}}
    <div class="px-6 py-4 border-b border-gray-100 bg-[#F8F8F8]">
        <form method="GET" action="{{ route('admin.products') }}" class="flex flex-wrap items-center gap-3">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Search Database..." class="bg-white border border-gray-200 px-3 py-1.5 text-[11px] focus:outline-none focus:border-[#C9A84C] w-48">
            <button type="submit" class="btn-admin-secondary py-1.5 px-3">Filter Server</button>
            @if(request('search'))
                <a href="{{ route('admin.products') }}" class="text-[10px] uppercase tracking-widest text-red-500 hover:underline border border-transparent px-2">Clear</a>
            @endif
        </form>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr>
                    <th class="py-4 px-6 text-[10px] font-bold uppercase tracking-widest text-[#1A1A1A] bg-[#F5F5F5] border-b border-[#C9A84C]">Product</th>
                    <th class="py-4 px-6 text-[10px] font-bold uppercase tracking-widest text-[#1A1A1A] bg-[#F5F5F5] border-b border-[#C9A84C]">Category</th>
                    <th class="py-4 px-6 text-[10px] font-bold uppercase tracking-widest text-[#1A1A1A] bg-[#F5F5F5] border-b border-[#C9A84C]">Price</th>
                    <th class="py-4 px-6 text-[10px] font-bold uppercase tracking-widest text-[#1A1A1A] bg-[#F5F5F5] border-b border-[#C9A84C]">Stock</th>
                    <th class="py-4 px-6 text-[10px] font-bold uppercase tracking-widest text-[#1A1A1A] bg-[#F5F5F5] border-b border-[#C9A84C]">Status</th>
                    <th class="py-4 px-6 text-[10px] font-bold uppercase tracking-widest text-[#1A1A1A] bg-[#F5F5F5] border-b border-[#C9A84C] text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($products as $product)
                    <tr class="bg-white hover:bg-[#FFFBF0] transition-colors duration-200" x-show="tableSearch === '' || $el.innerText.toLowerCase().includes(tableSearch.toLowerCase())">
                        <td class="py-4 px-6">
                            <div class="flex items-center gap-4">
                                <div class="w-12 h-16 bg-[#F8F8F8] border border-gray-100 flex-shrink-0 flex items-center justify-center overflow-hidden">
                                    @if(is_array($product->images) && count($product->images) > 0)
                                        <img src="{{ $product->images[0] }}" alt="{{ $product->name }}" class="w-full h-full object-cover">
                                    @else
                                        <div class="text-[9px] text-gray-400 uppercase tracking-widest">No Img</div>
                                    @endif
                                </div>
                                <div class="min-w-0">
                                    <p class="text-xs font-bold text-[#1A1A1A] uppercase tracking-widest truncate">{{ Str::limit($product->name, 35) }}</p>
                                    <p class="text-[10px] text-gray-500 uppercase tracking-widest mt-1">SKU: {{ $product->sku ?? 'N/A' }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="py-4 px-6 text-[10px] font-bold uppercase tracking-widest text-gray-600">{{ $product->category }}</td>
                        <td class="py-4 px-6">
                            <p class="text-sm font-bold text-[#1A1A1A]">৳{{ number_format($product->price) }}</p>
                            @if($product->discount_price)
                                <p class="text-[10px] font-bold text-[#C9A84C] uppercase tracking-widest mt-0.5">Sale: ৳{{ number_format($product->discount_price) }}</p>
                            @endif
                        </td>
                        <td class="py-4 px-6">
                            <span class="{{ $product->stock <= 5 ? 'text-red-500 font-bold' : 'text-gray-600 font-medium' }} text-sm">{{ $product->stock }}</span>
                        </td>
                        <td class="py-4 px-6">
                            <span class="inline-flex items-center px-2 py-1 rounded-full text-[9px] font-bold uppercase tracking-widest {{ $product->is_active ? 'bg-green-50 text-green-600' : 'bg-red-50 text-red-600' }}">
                                {{ $product->is_active ? 'Active' : 'Inactive' }}
                            </span>
                        </td>
                        <td class="py-4 px-6 text-right">
                            <div class="flex items-center justify-end gap-2">
                                <a href="{{ route('admin.products.edit', $product->id) }}" class="inline-block px-3 py-1.5 text-[10px] uppercase tracking-widest font-bold border border-gray-200 text-[#1A1A1A] hover:bg-gray-50 transition-colors">Edit</a>
                                <form method="POST" action="{{ route('admin.products.destroy', $product->id) }}" onsubmit="return confirm('Delete this product?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="inline-block px-3 py-1.5 text-[10px] uppercase tracking-widest font-bold border border-red-200 text-red-500 hover:bg-red-50 transition-colors">Delete</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="py-16 text-center">
                            <div class="flex flex-col items-center justify-center">
                                <span class="text-4xl mb-3 grayscale opacity-60">👔</span>
                                <p class="text-xs text-gray-400 font-bold uppercase tracking-widest">No products found</p>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="p-6 border-t border-gray-100">
        {{ $products->withQueryString()->links() }}
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
