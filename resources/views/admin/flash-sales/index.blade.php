@extends('layouts.admin')
@section('title', 'Flash Sales — Admin')
@section('page-title', 'Flash Sales')

@section('content')
<div class="space-y-8 max-w-7xl mx-auto">
    
    {{-- Create New Flash Sale --}}
    <div class="bg-white border border-gray-100 shadow-sm p-6 sm:p-8">
        <h2 class="text-xs font-bold uppercase tracking-widest text-[#1A1A1A] border-l-2 border-[#C9A84C] pl-3 mb-6">Create Flash Sale</h2>
        
        <form method="POST" action="{{ route('admin.flash-sales.store') }}" class="grid grid-cols-1 md:grid-cols-2 gap-6">
            @csrf
            
            <div class="md:col-span-2">
                <label class="text-[10px] font-bold uppercase tracking-widest text-[#1A1A1A] mb-2 block">Select Product <span class="text-[#C9A84C]">*</span></label>
                <select name="product_id" required class="w-full bg-white border border-gray-200 px-4 py-2.5 text-sm text-[#1A1A1A] focus:outline-none focus:border-[#C9A84C] transition-colors">
                    <option value="">-- Choose Product --</option>
                    @foreach($products as $product)
                        <option value="{{ $product->id }}">{{ $product->name }} (৳{{ $product->price }})</option>
                    @endforeach
                </select>
                @error('product_id') <p class="text-red-500 text-xs mt-1 font-bold uppercase tracking-widest">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="text-[10px] font-bold uppercase tracking-widest text-[#1A1A1A] mb-2 block">Flash Sale Price (৳) <span class="text-[#C9A84C]">*</span></label>
                <input type="number" step="0.01" name="sale_price" required min="1" value="{{ old('sale_price') }}"
                       class="w-full bg-white border border-gray-200 px-4 py-2.5 text-sm text-[#1A1A1A] focus:outline-none focus:border-[#C9A84C] transition-colors" placeholder="e.g. 500">
                @error('sale_price') <p class="text-red-500 text-xs mt-1 font-bold uppercase tracking-widest">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="text-[10px] font-bold uppercase tracking-widest text-[#1A1A1A] mb-2 block">Max Quantity <span class="text-[#C9A84C]">*</span></label>
                <input type="number" name="max_quantity" required min="1" value="{{ old('max_quantity', 100) }}"
                       class="w-full bg-white border border-gray-200 px-4 py-2.5 text-sm text-[#1A1A1A] focus:outline-none focus:border-[#C9A84C] transition-colors">
                @error('max_quantity') <p class="text-red-500 text-xs mt-1 font-bold uppercase tracking-widest">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="text-[10px] font-bold uppercase tracking-widest text-[#1A1A1A] mb-2 block">Starts At <span class="text-[#C9A84C]">*</span></label>
                <input type="datetime-local" name="starts_at" required value="{{ old('starts_at') }}"
                       class="w-full bg-white border border-gray-200 px-4 py-2.5 text-sm text-[#1A1A1A] focus:outline-none focus:border-[#C9A84C] transition-colors">
                @error('starts_at') <p class="text-red-500 text-xs mt-1 font-bold uppercase tracking-widest">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="text-[10px] font-bold uppercase tracking-widest text-[#1A1A1A] mb-2 block">Ends At <span class="text-[#C9A84C]">*</span></label>
                <input type="datetime-local" name="ends_at" required value="{{ old('ends_at') }}"
                       class="w-full bg-white border border-gray-200 px-4 py-2.5 text-sm text-[#1A1A1A] focus:outline-none focus:border-[#C9A84C] transition-colors">
                @error('ends_at') <p class="text-red-500 text-xs mt-1 font-bold uppercase tracking-widest">{{ $message }}</p> @enderror
            </div>

            <div class="md:col-span-2 pt-2 border-t border-gray-100">
                <button type="submit" class="bg-[#C9A84C] hover:bg-[#b08a38] text-white text-[11px] font-bold uppercase tracking-[0.2em] px-8 py-3 transition-colors shadow-sm">
                    Create Flash Sale ⚡
                </button>
            </div>
        </form>
    </div>

    {{-- Active & Past Flash Sales --}}
    <div class="bg-white border border-gray-100 shadow-sm p-6 sm:p-8">
        <h2 class="text-xs font-bold uppercase tracking-widest text-[#1A1A1A] border-l-2 border-[#C9A84C] pl-3 mb-6">Flash Sale Records</h2>
        
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr>
                        <th class="py-4 px-4 text-[9px] font-bold uppercase tracking-widest text-gray-500 bg-gray-50 border-b border-gray-200">Product</th>
                        <th class="py-4 px-4 text-[9px] font-bold uppercase tracking-widest text-gray-500 bg-gray-50 border-b border-gray-200">Status</th>
                        <th class="py-4 px-4 text-[9px] font-bold uppercase tracking-widest text-gray-500 bg-gray-50 border-b border-gray-200">Price</th>
                        <th class="py-4 px-4 text-[9px] font-bold uppercase tracking-widest text-gray-500 bg-gray-50 border-b border-gray-200">Progress</th>
                        <th class="py-4 px-4 text-[9px] font-bold uppercase tracking-widest text-gray-500 bg-gray-50 border-b border-gray-200">Ends In</th>
                        <th class="py-4 px-4 text-[9px] font-bold uppercase tracking-widest text-gray-500 bg-gray-50 border-b border-gray-200 text-right">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 bg-white">
                    @forelse($flashSales as $sale)
                        @php
                            $now = now();
                            if (!$sale->is_active || $sale->sold_count >= $sale->max_quantity || $sale->ends_at <= $now) {
                                $status = 'Ended';
                                $statusColor = 'bg-gray-100 text-gray-500';
                            } elseif ($sale->starts_at > $now) {
                                $status = 'Upcoming';
                                $statusColor = 'bg-blue-50 text-blue-600 border border-blue-200';
                            } else {
                                $status = 'Active';
                                $statusColor = 'bg-[#FFFBF0] text-[#C9A84C] border border-[#C9A84C]/30';
                            }
                        @endphp
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="py-4 px-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 bg-gray-100 border border-gray-200 overflow-hidden flex-shrink-0">
                                        @if(is_array($sale->product->images) && count($sale->product->images) > 0)
                                            <img src="{{ $sale->product->images[0] }}" class="w-full h-full object-cover">
                                        @endif
                                    </div>
                                    <a href="{{ route('products.show', $sale->product_id) }}" target="_blank" class="text-sm font-bold text-[#1A1A1A] hover:text-[#C9A84C] transition-colors truncate max-w-[200px]">{{ $sale->product->name }}</a>
                                </div>
                            </td>
                            <td class="py-4 px-4">
                                <span class="inline-flex px-2 py-0.5 text-[9px] font-bold uppercase tracking-widest rounded-full {{ $statusColor }}">
                                    {{ $status }}
                                </span>
                            </td>
                            <td class="py-4 px-4">
                                <span class="text-sm font-bold text-[#C9A84C]">৳{{ number_format($sale->sale_price) }}</span>
                                <span class="text-[10px] text-gray-400 line-through ml-1 block">৳{{ number_format($sale->product->price) }}</span>
                            </td>
                            <td class="py-4 px-4">
                                <div class="w-24 bg-gray-100 h-1.5 mb-1 relative overflow-hidden">
                                    <div class="absolute left-0 top-0 h-full bg-[#1A1A1A]" style="width: {{ min(100, ($sale->sold_count / $sale->max_quantity) * 100) }}%"></div>
                                </div>
                                <span class="text-[9px] text-gray-500 uppercase tracking-widest font-bold">{{ $sale->sold_count }} / {{ $sale->max_quantity }} Sold</span>
                            </td>
                            <td class="py-4 px-4" x-data="{
                                endsAt: new Date('{{ $sale->ends_at->toIso8601String() }}').getTime(),
                                startsAt: new Date('{{ $sale->starts_at->toIso8601String() }}').getTime(),
                                now: new Date().getTime(),
                                timeRemaining: '',
                                init() {
                                    if(this.now > this.endsAt) {
                                        this.timeRemaining = 'Expired';
                                        return;
                                    }
                                    if(this.now < this.startsAt) {
                                        this.timeRemaining = 'Starts soon';
                                        return;
                                    }
                                    this.updateTimer();
                                    setInterval(() => { this.updateTimer() }, 1000);
                                },
                                updateTimer() {
                                    const distance = this.endsAt - new Date().getTime();
                                    if (distance < 0) {
                                        this.timeRemaining = 'Expired';
                                        return;
                                    }
                                    const hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
                                    const minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
                                    const seconds = Math.floor((distance % (1000 * 60)) / 1000);
                                    this.timeRemaining = hours + 'h ' + minutes + 'm ' + seconds + 's';
                                }
                            }">
                                <span x-text="timeRemaining" class="text-[10px] font-mono font-bold text-red-500 uppercase tracking-widest"></span>
                            </td>
                            <td class="py-4 px-4 text-right">
                                <form method="POST" action="{{ route('admin.flash-sales.destroy', $sale->id) }}" onsubmit="return confirm('Delete this flash sale?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-[9px] font-bold uppercase tracking-widest text-red-500 hover:text-red-700 underline underline-offset-4 decoration-red-200 hover:decoration-red-500 transition-colors">Delete</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="py-8 px-4 text-center text-xs text-gray-500 font-bold uppercase tracking-widest">No flash sales found.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
