@extends('layouts.admin')
@section('title', 'Inventory Report')
@section('page-title', 'Inventory Report')

@section('content')
<div class="space-y-8 max-w-7xl mx-auto pb-8">
    
    {{-- Top Action Bar --}}
    <div class="flex items-center justify-between bg-white border border-gray-100 shadow-sm p-5 sm:p-6 mb-6">
        <div>
            <h2 class="text-xs font-bold uppercase tracking-widest text-[#1A1A1A] border-l-2 border-[#C9A84C] pl-3">Inventory Health</h2>
            <p class="text-[10px] text-gray-400 mt-1 uppercase tracking-widest ml-3">Real-time stock monitoring</p>
        </div>
        <div>
            <a href="{{ route('admin.reports.inventory.export') }}" class="btn-admin-primary px-8 flex items-center gap-2" target="_blank">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                Export Excel/CSV
            </a>
        </div>
    </div>

    {{-- Validation msg --}}
    @if(session('success'))
        <div class="bg-green-50 text-green-600 border border-green-200 text-xs font-bold uppercase tracking-widest p-4">
            {{ session('success') }}
        </div>
    @endif
    @error('stock')
        <div class="bg-red-50 text-red-600 border border-red-200 text-xs font-bold uppercase tracking-widest p-4">
            {{ $message }}
        </div>
    @enderror

    {{-- 1. OVERVIEW CARDS --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
        {{-- Total Products --}}
        <div class="bg-white border border-gray-100 shadow-sm p-6 relative overflow-hidden">
            <h3 class="text-[10px] font-bold uppercase tracking-widest text-gray-500 mb-1">Total Items Catalogued</h3>
            <p class="text-3xl font-bold text-[#1A1A1A] playfair">{{ number_format($totalProducts) }}</p>
        </div>
        
        {{-- In Stock healthy --}}
        <div class="bg-white border border-gray-100 shadow-sm p-6 relative overflow-hidden border-b-2 border-b-green-500">
            <h3 class="text-[10px] font-bold uppercase tracking-widest text-green-600 mb-1">Healthy Stock (10+)</h3>
            <p class="text-3xl font-bold text-[#1A1A1A] playfair">{{ number_format($inStock) }}</p>
        </div>

        {{-- Low Stock --}}
        <div class="bg-white border border-gray-100 shadow-sm p-6 relative overflow-hidden border-b-2 border-b-yellow-500">
            <h3 class="text-[10px] font-bold uppercase tracking-widest text-yellow-600 mb-1">Low Stock (< 10)</h3>
            <p class="text-3xl font-bold text-[#1A1A1A] playfair">{{ number_format($lowStockCount) }}</p>
        </div>

        {{-- Out of Stock --}}
        <div class="bg-[#FFFBF0] border border-[#C9A84C]/50 shadow-sm p-6 relative overflow-hidden border-b-2 border-b-red-500">
            <h3 class="text-[10px] font-bold uppercase tracking-widest text-red-500 mb-1">Out of Stock (= 0)</h3>
            <p class="text-3xl font-bold text-red-600 playfair">{{ number_format($outOfStockCount) }}</p>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        
        {{-- 4. STOCK VALUE (Moving to left col for layout) --}}
        <div class="bg-[#1A1A1A] border border-[#333] shadow-sm p-6 lg:col-span-1 h-fit text-white">
            <h2 class="text-xs font-bold uppercase tracking-widest text-gray-400 border-l-2 border-[#C9A84C] pl-3 mb-6">Valuation By Category</h2>
            
            <div class="mb-8">
                <p class="text-[10px] font-bold uppercase tracking-widest text-gray-500 mb-1">Total Inventory Value</p>
                <p class="text-3xl font-bold text-[#C9A84C] playfair">৳{{ number_format($totalStockValue) }}</p>
            </div>

            <div class="space-y-4">
                @foreach($stockValuesRaw as $val)
                <div class="flex justify-between items-end border-b border-[#333] pb-2">
                    <div>
                        <p class="text-[11px] font-bold uppercase tracking-widest text-gray-300">{{ $val->category }}</p>
                        <p class="text-[9px] text-gray-500 uppercase tracking-widest">{{ $val->total_items }} Items</p>
                    </div>
                    <div>
                        <p class="text-sm font-bold text-[#C9A84C]">৳{{ number_format($val->total_value) }}</p>
                    </div>
                </div>
                @endforeach
            </div>
        </div>

        {{-- Tables Column --}}
        <div class="lg:col-span-2 space-y-8">
            
            {{-- 3. OUT OF STOCK TABLE --}}
            <div class="bg-white border border-red-200 shadow-sm p-6 relative">
                <div class="absolute top-0 right-0 px-4 py-1 bg-red-500 text-white text-[9px] font-bold uppercase tracking-widest shadow-sm">Critical Attention</div>
                <h2 class="text-xs font-bold uppercase tracking-widest text-[#1A1A1A] border-l-2 border-red-500 pl-3 mb-6 mt-2">Out of Stock</h2>
                
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr>
                                <th class="py-3 px-2 text-[9px] font-bold uppercase tracking-widest text-gray-500 border-b border-gray-200">Product</th>
                                <th class="py-3 px-2 text-[9px] font-bold uppercase tracking-widest text-gray-500 border-b border-gray-200">Category</th>
                                <th class="py-3 px-2 text-[9px] font-bold uppercase tracking-widest text-gray-500 border-b border-gray-200 text-right">Quick Restock</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @forelse($outOfStockProducts as $p)
                            <tr class="hover:bg-red-50/30 transition-colors">
                                <td class="py-3 px-2">
                                    <div class="flex items-center gap-3">
                                        <div class="w-8 h-10 bg-gray-50 border border-gray-200 flex-shrink-0">
                                            @if(is_array($p->images) && count($p->images) > 0)
                                                <img src="{{ $p->images[0] }}" class="w-full h-full object-cover grayscale opacity-60">
                                            @endif
                                        </div>
                                        <div class="flex flex-col">
                                            <a href="{{ route('admin.products.edit', $p->id) }}" class="text-[11px] font-bold text-[#1A1A1A] uppercase tracking-widest hover:text-[#C9A84C] transition-colors line-clamp-1 w-48">{{ $p->name }}</a>
                                            <span class="text-[9px] text-gray-400">SKU: {{ $p->sku ?: '---' }}</span>
                                        </div>
                                    </div>
                                </td>
                                <td class="py-3 px-2 text-[10px] font-bold text-gray-600 uppercase tracking-widest">{{ $p->category }}</td>
                                <td class="py-3 px-2 text-right">
                                    <form method="POST" action="{{ route('admin.reports.inventory.updateStock', $p->id) }}" class="flex items-center justify-end gap-2">
                                        @csrf
                                        <input type="number" name="stock" value="0" min="1" required class="w-16 bg-white border border-gray-200 px-2 py-1 text-xs focus:outline-none focus:border-[#C9A84C] text-center font-bold">
                                        <button type="submit" class="bg-[#1A1A1A] hover:bg-[#333] text-white text-[9px] font-bold uppercase tracking-widest px-3 py-1.5 transition-colors">Apply</button>
                                    </form>
                                </td>
                            </tr>
                            @empty
                            <tr><td colspan="3" class="py-6 text-center text-[10px] text-gray-400 font-bold uppercase tracking-widest">No products are currently out of stock. ✅</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- 2. LOW STOCK TABLE --}}
            <div class="bg-white border border-yellow-200 shadow-sm p-6 relative">
                <div class="absolute top-0 right-0 px-4 py-1 bg-yellow-400 text-[#1A1A1A] text-[9px] font-bold uppercase tracking-widest shadow-sm">Warning Queue</div>
                <h2 class="text-xs font-bold uppercase tracking-widest text-[#1A1A1A] border-l-2 border-yellow-400 pl-3 mb-6 mt-2">Low Stock Items (< 10)</h2>
                
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr>
                                <th class="py-3 px-2 text-[9px] font-bold uppercase tracking-widest text-gray-500 border-b border-gray-200">Product</th>
                                <th class="py-3 px-2 text-[9px] font-bold uppercase tracking-widest text-gray-500 border-b border-gray-200 text-center">Current</th>
                                <th class="py-3 px-2 text-[9px] font-bold uppercase tracking-widest text-gray-500 border-b border-gray-200 text-center">Last Sold</th>
                                <th class="py-3 px-2 text-[9px] font-bold uppercase tracking-widest text-gray-500 border-b border-gray-200 text-right">Quick Restock</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @forelse($lowStockProducts as $p)
                            <tr class="hover:bg-yellow-50/30 transition-colors">
                                <td class="py-3 px-2">
                                    <div class="flex items-center gap-3">
                                        <div class="w-8 h-10 bg-gray-50 border border-gray-200 flex-shrink-0">
                                            @if(is_array($p->images) && count($p->images) > 0)
                                                <img src="{{ $p->images[0] }}" class="w-full h-full object-cover">
                                            @endif
                                        </div>
                                        <div class="flex flex-col">
                                            <a href="{{ route('admin.products.edit', $p->id) }}" class="text-[11px] font-bold text-[#1A1A1A] uppercase tracking-widest hover:text-[#C9A84C] transition-colors line-clamp-1 w-40">{{ $p->name }}</a>
                                            <span class="text-[9px] text-gray-400">{{ $p->category }}</span>
                                        </div>
                                    </div>
                                </td>
                                <td class="py-3 px-2 text-center">
                                    <span class="text-sm font-bold {{ $p->stock <= 3 ? 'text-red-500' : 'text-yellow-600' }}">{{ $p->stock }}</span>
                                </td>
                                <td class="py-3 px-2 text-[9px] font-bold text-gray-500 uppercase tracking-widest text-center">
                                    {{ $p->last_sold_date ? \Carbon\Carbon::parse($p->last_sold_date)->diffForHumans() : 'Never' }}
                                </td>
                                <td class="py-3 px-2 text-right">
                                    <form method="POST" action="{{ route('admin.reports.inventory.updateStock', $p->id) }}" class="flex items-center justify-end gap-2">
                                        @csrf
                                        <input type="number" name="stock" value="{{ $p->stock }}" min="0" required class="w-16 bg-white border border-gray-200 px-2 py-1 text-xs focus:outline-none focus:border-[#C9A84C] text-center font-bold">
                                        <button type="submit" class="bg-[#1A1A1A] hover:bg-[#333] text-white text-[9px] font-bold uppercase tracking-widest px-3 py-1.5 transition-colors">Apply</button>
                                    </form>
                                </td>
                            </tr>
                            @empty
                            <tr><td colspan="4" class="py-6 text-center text-[10px] text-gray-400 font-bold uppercase tracking-widest">No products are currently low in stock. ✅</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </div>
</div>
@endsection
