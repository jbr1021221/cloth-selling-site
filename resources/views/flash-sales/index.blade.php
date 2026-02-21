@extends('layouts.app')
@section('title', 'Flash Sales')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12 space-y-10 border-t border-[#C9A84C]/20 shadow-sm mt-4">
    
    <div class="text-center mb-16">
        <h1 class="text-3xl md:text-5xl font-extrabold uppercase tracking-[0.2em] text-[#1A1A1A] mb-4">Flash Sales ⚡</h1>
        <p class="text-xs uppercase tracking-widest text-[#C9A84C] font-bold">Incredible deals. Limited Time. Act Fast.</p>
        <div class="h-px w-24 bg-[#C9A84C] mx-auto mt-6"></div>
    </div>

    @if($flashSales->count() > 0)
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-8">
            @foreach($flashSales as $sale)
                <div class="bg-white border border-gray-100 shadow-sm relative group overflow-hidden">
                    {{-- Flash Badge --}}
                    <div class="absolute top-2 left-2 z-10 bg-[#1A1A1A] text-[#C9A84C] text-[9px] font-bold uppercase tracking-widest px-3 py-1 shadow-sm border border-[#C9A84C]/30 flex items-center gap-1">
                        <svg class="w-3 h-3 text-[#C9A84C]" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M11.3 1.046A1 1 0 0112 2v5h4a1 1 0 01.82 1.573l-7 10A1 1 0 018 18v-5H4a1 1 0 01-.82-1.573l7-10a1 1 0 011.12-.38z" clip-rule="evenodd"></path></svg>
                        Sale
                    </div>

                    {{-- Image --}}
                    <div class="relative aspect-[4/5] bg-gray-50 flex items-center justify-center overflow-hidden">
                        <a href="{{ route('products.show', $sale->product_id) }}" class="block w-full h-full">
                            @if(is_array($sale->product->images) && count($sale->product->images) > 0)
                                <img src="{{ $sale->product->images[0] }}" alt="{{ $sale->product->name }}" class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-105">
                            @else
                                <img src="https://images.unsplash.com/photo-1523381210434-271e8be1f52b?q=80&w=600" class="w-full h-full object-cover">
                            @endif
                        </a>

                        {{-- Hover Action --}}
                        <div class="absolute bottom-0 left-0 right-0 p-4 translate-y-full group-hover:translate-y-0 transition-transform duration-300">
                            <a href="{{ route('products.show', $sale->product_id) }}" class="block w-full text-center bg-[#C9A84C] hover:bg-[#b08a38] text-white text-[10px] uppercase font-bold tracking-widest py-3 transition-colors shadow-sm">
                                View Deal
                            </a>
                        </div>
                    </div>

                    {{-- Info --}}
                    <div class="p-5 flex flex-col items-center">
                        <p class="text-[9px] uppercase tracking-widest text-gray-400 font-bold mb-1">{{ $sale->product->category }}</p>
                        <h3 class="text-xs font-bold uppercase tracking-widest text-[#1A1A1A] mb-3 truncate w-full text-center">{{ $sale->product->name }}</h3>
                        
                        <div class="flex items-center gap-2 mb-4">
                            <span class="text-sm font-extrabold text-[#C9A84C] tracking-wide">৳{{ number_format($sale->sale_price) }}</span>
                            <span class="text-[10px] text-gray-400 line-through tracking-wide">৳{{ number_format($sale->product->price) }}</span>
                        </div>

                        {{-- Progress --}}
                        <div class="w-full mb-4">
                            <div class="flex justify-between text-[9px] text-[#1A1A1A] font-bold uppercase tracking-widest mb-1.5">
                                <span>Sold: {{ $sale->sold_count }}</span>
                                <span>Total: {{ $sale->max_quantity }}</span>
                            </div>
                            <div class="w-full bg-gray-100 h-1 relative overflow-hidden">
                                <div class="absolute left-0 top-0 h-full bg-[#C9A84C]" style="width: {{ min(100, ($sale->sold_count / $sale->max_quantity) * 100) }}%"></div>
                            </div>
                        </div>

                        {{-- Timer --}}
                        <div class="w-full flex items-center justify-center border-t border-gray-100 pt-3" x-data="{
                            endsAt: new Date('{{ $sale->ends_at->toIso8601String() }}').getTime(),
                            now: new Date().getTime(),
                            h: '00', m: '00', s: '00',
                            init() {
                                this.updateTimer();
                                setInterval(() => { this.updateTimer() }, 1000);
                            },
                            updateTimer() {
                                const distance = this.endsAt - new Date().getTime();
                                if (distance < 0) {
                                    this.h = '00'; this.m = '00'; this.s = '00';
                                    return;
                                }
                                this.h = String(Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60))).padStart(2, '0');
                                this.m = String(Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60))).padStart(2, '0');
                                this.s = String(Math.floor((distance % (1000 * 60)) / 1000)).padStart(2, '0');
                            }
                        }">
                            <div class="flex items-center gap-2 text-[#1A1A1A] font-mono text-[10px] font-bold uppercase tracking-widest">
                                <span>Ends In:</span>
                                <span class="bg-red-50 text-red-600 border border-red-100 px-1.5 py-0.5"><span x-text="h"></span>H</span>
                                <span class="text-gray-300">:</span>
                                <span class="bg-red-50 text-red-600 border border-red-100 px-1.5 py-0.5"><span x-text="m"></span>M</span>
                                <span class="text-gray-300">:</span>
                                <span class="bg-red-50 text-red-600 border border-red-100 px-1.5 py-0.5"><span x-text="s"></span>S</span>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <div class="py-20 text-center border-t border-b border-gray-100 bg-gray-50/50">
            <svg class="w-12 h-12 text-[#C9A84C] mx-auto mb-4 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
            <h2 class="text-sm font-bold uppercase tracking-widest text-[#1A1A1A] mb-2">No Active Flash Sales</h2>
            <p class="text-[10px] uppercase tracking-widest text-gray-500">Check back later for incredible new deals.</p>
        </div>
    @endif
</div>
@endsection
