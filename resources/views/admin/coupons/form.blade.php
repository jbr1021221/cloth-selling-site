@extends('layouts.admin')
@section('title', isset($coupon) ? 'Edit Coupon — Admin' : 'Create Coupon — Admin')
@section('page-title', isset($coupon) ? 'Edit Coupon' : 'Create Coupon')

@section('content')
<div class="max-w-3xl">

    <div class="mb-8 hidden">
        <h1 class="text-3xl font-bold text-[#1A1A1A] playfair uppercase tracking-widest">{{ isset($coupon) ? 'Edit Coupon' : 'Create Coupon' }}</h1>
    </div>

    <form method="POST"
          action="{{ isset($coupon) ? route('admin.coupons.update', $coupon->id) : route('admin.coupons.store') }}"
          class="bg-white border border-gray-100 shadow-sm p-6 sm:p-8 space-y-6"
          x-data="{ type: '{{ old('type', $coupon->type ?? 'fixed') }}' }">
        @csrf
        @if(isset($coupon)) @method('PUT') @endif
        
        <h2 class="text-xs font-bold uppercase tracking-widest text-[#1A1A1A] border-l-2 border-[#C9A84C] pl-3 mb-6">Coupon Configuration</h2>

        {{-- Code --}}
        <div>
            <label class="text-[10px] font-bold uppercase tracking-widest text-[#1A1A1A] mb-2 block">Coupon Code <span class="text-[#C9A84C]">*</span></label>
            <input type="text" name="code"
                   value="{{ old('code', $coupon->code ?? '') }}"
                   class="w-full bg-white border border-gray-200 px-4 py-2.5 text-sm text-[#1A1A1A] focus:outline-none focus:border-[#C9A84C] transition-colors mt-1.5 uppercase font-mono tracking-widest"
                   placeholder="e.g. SAVE20, WELCOME10"
                   maxlength="50" required
                   style="text-transform:uppercase">
            <p class="text-[10px] text-gray-500 mt-1 uppercase tracking-widest">Customers will enter this exactly as shown (case-insensitive).</p>
            @error('code') <p class="text-red-500 text-xs mt-1 font-bold uppercase tracking-widest">{{ $message }}</p> @enderror
        </div>

        {{-- Type --}}
        <div>
            <label class="text-[10px] font-bold uppercase tracking-widest text-[#1A1A1A] mb-2 block">Discount Type <span class="text-[#C9A84C]">*</span></label>
            <div class="flex gap-3 mt-1.5">
                <label class="flex-1 cursor-pointer">
                    <input type="radio" name="type" value="fixed" class="sr-only peer" @change="type='fixed'"
                           {{ old('type', $coupon->type ?? 'fixed') === 'fixed' ? 'checked' : '' }}>
                    <div class="border border-gray-200 bg-white peer-checked:border-[#C9A84C] flex flex-col items-center justify-center p-4 text-center transition-all shadow-sm text-gray-400 peer-checked:text-[#C9A84C]">
                        <p class="text-lg font-bold text-inherit">৳</p>
                        <p class="text-[10px] font-bold uppercase tracking-widest text-gray-500 peer-checked:text-[#C9A84C]">Fixed Amount</p>
                    </div>
                </label>
                <label class="flex-1 cursor-pointer">
                    <input type="radio" name="type" value="percentage" class="sr-only peer" @change="type='percentage'"
                           {{ old('type', $coupon->type ?? '') === 'percentage' ? 'checked' : '' }}>
                    <div class="border border-gray-200 bg-white peer-checked:border-[#C9A84C] flex flex-col items-center justify-center p-4 text-center transition-all shadow-sm text-gray-400 peer-checked:text-[#C9A84C]">
                        <p class="text-lg font-bold text-inherit">%</p>
                        <p class="text-[10px] font-bold uppercase tracking-widest text-gray-500 peer-checked:text-[#C9A84C]">Percentage</p>
                    </div>
                </label>
            </div>
        </div>

        {{-- Value --}}
        <div>
            <label class="text-[10px] font-bold uppercase tracking-widest text-[#1A1A1A] mb-2 block">
                <span x-text="type === 'fixed' ? 'Discount Amount (৳)' : 'Discount Percentage (%)'"></span>
                <span class="text-[#C9A84C]">*</span>
            </label>
            <input type="number" name="value" step="0.01" min="0.01"
                   value="{{ old('value', $coupon->value ?? '') }}"
                   class="w-full bg-white border border-gray-200 px-4 py-2.5 text-sm text-[#1A1A1A] focus:outline-none focus:border-[#C9A84C] transition-colors mt-1.5" required
                   :placeholder="type === 'fixed' ? '100' : '20'">
            @error('value') <p class="text-red-500 text-xs mt-1 font-bold uppercase tracking-widest">{{ $message }}</p> @enderror
        </div>

        {{-- Max discount cap (percentage only) --}}
        <div x-show="type === 'percentage'" x-transition>
            <label class="text-[10px] font-bold uppercase tracking-widest text-[#1A1A1A] mb-2 block">Max Discount Cap (৳) <span class="text-gray-400 font-normal tracking-normal normal-case">optional</span></label>
            <input type="number" name="max_discount" step="0.01" min="0"
                   value="{{ old('max_discount', $coupon->max_discount ?? '') }}"
                   class="w-full bg-white border border-gray-200 px-4 py-2.5 text-sm text-[#1A1A1A] focus:outline-none focus:border-[#C9A84C] transition-colors mt-1.5"
                   placeholder="e.g. 500 — discount won't exceed this">
            <p class="text-[10px] text-gray-500 mt-1 uppercase tracking-widest">Leave blank for no cap.</p>
            @error('max_discount') <p class="text-red-500 text-xs mt-1 font-bold uppercase tracking-widest">{{ $message }}</p> @enderror
        </div>

        {{-- Min order --}}
        <div>
            <label class="text-[10px] font-bold uppercase tracking-widest text-[#1A1A1A] mb-2 block">Minimum Order Amount (৳) <span class="text-gray-400 font-normal tracking-normal normal-case">optional</span></label>
            <input type="number" name="min_order" step="0.01" min="0"
                   value="{{ old('min_order', $coupon->min_order ?? 0) }}"
                   class="w-full bg-white border border-gray-200 px-4 py-2.5 text-sm text-[#1A1A1A] focus:outline-none focus:border-[#C9A84C] transition-colors mt-1.5"
                   placeholder="0 = no minimum">
            @error('min_order') <p class="text-red-500 text-xs mt-1 font-bold uppercase tracking-widest">{{ $message }}</p> @enderror
        </div>

        {{-- Max uses --}}
        <div>
            <label class="text-[10px] font-bold uppercase tracking-widest text-[#1A1A1A] mb-2 block">Max Uses <span class="text-gray-400 font-normal tracking-normal normal-case">optional</span></label>
            <input type="number" name="max_uses" min="1"
                   value="{{ old('max_uses', $coupon->max_uses ?? '') }}"
                   class="w-full bg-white border border-gray-200 px-4 py-2.5 text-sm text-[#1A1A1A] focus:outline-none focus:border-[#C9A84C] transition-colors mt-1.5"
                   placeholder="Leave blank for unlimited">
            @error('max_uses') <p class="text-red-500 text-xs mt-1 font-bold uppercase tracking-widest">{{ $message }}</p> @enderror
        </div>

        {{-- Expires at --}}
        <div>
            <label class="text-[10px] font-bold uppercase tracking-widest text-[#1A1A1A] mb-2 block">Expiry Date <span class="text-gray-400 font-normal tracking-normal normal-case">optional</span></label>
            <input type="datetime-local" name="expires_at"
                   value="{{ old('expires_at', isset($coupon->expires_at) ? $coupon->expires_at->format('Y-m-d\TH:i') : '') }}"
                   class="w-full bg-white border border-gray-200 px-4 py-2.5 text-sm text-[#1A1A1A] focus:outline-none focus:border-[#C9A84C] transition-colors mt-1.5">
            <p class="text-[10px] text-gray-500 mt-1 uppercase tracking-widest">Leave blank for no expiry.</p>
            @error('expires_at') <p class="text-red-500 text-xs mt-1 font-bold uppercase tracking-widest">{{ $message }}</p> @enderror
        </div>

        {{-- Active toggle --}}
        <div class="flex items-center gap-3 pt-2">
            <input type="checkbox" id="is_active" name="is_active" value="1"
                   class="w-4 h-4 text-[#C9A84C] focus:ring-[#C9A84C] border-gray-300 rounded"
                   {{ old('is_active', $coupon->is_active ?? true) ? 'checked' : '' }}>
            <label for="is_active" class="text-[#1A1A1A] text-sm font-medium cursor-pointer">Active (coupon is usable)</label>
        </div>

        {{-- Submit --}}
        <div class="flex gap-3 pt-4 border-t border-gray-100">
            <button type="submit" class="bg-[#C9A84C] hover:bg-[#b08a38] text-white text-[11px] font-bold uppercase tracking-[0.2em] px-8 py-3 transition-colors shadow-sm">
                {{ isset($coupon) ? 'Update Coupon' : 'Create Coupon 🎟️' }}
            </button>
            <a href="{{ route('admin.coupons.index') }}" class="bg-white border border-gray-200 text-[#1A1A1A] hover:bg-gray-50 text-[11px] font-bold uppercase tracking-[0.2em] px-8 py-3 transition-colors text-center shadow-sm flex items-center">Cancel</a>
        </div>
    </form>
</div>
@endsection
