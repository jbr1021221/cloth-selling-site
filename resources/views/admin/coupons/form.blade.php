@extends('layouts.app')
@section('title', isset($coupon) ? 'Edit Coupon — Admin' : 'Create Coupon — Admin')

@section('content')
<div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8 py-10">

    <div class="mb-8">
        <a href="{{ route('admin.coupons.index') }}" class="inline-flex items-center gap-2 text-sm text-gray-500 hover:text-indigo-400 transition-colors mb-3">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            All Coupons
        </a>
        <h1 class="text-3xl font-bold text-white">{{ isset($coupon) ? 'Edit Coupon' : 'Create Coupon' }}</h1>
    </div>

    <form method="POST"
          action="{{ isset($coupon) ? route('admin.coupons.update', $coupon->id) : route('admin.coupons.store') }}"
          class="bg-gray-900 border border-gray-800 rounded-2xl p-7 space-y-5"
          x-data="{ type: '{{ old('type', $coupon->type ?? 'fixed') }}' }">
        @csrf
        @if(isset($coupon)) @method('PUT') @endif

        {{-- Code --}}
        <div>
            <label class="label">Coupon Code <span class="text-red-400">*</span></label>
            <input type="text" name="code"
                   value="{{ old('code', $coupon->code ?? '') }}"
                   class="input w-full mt-1.5 uppercase font-mono tracking-widest"
                   placeholder="e.g. SAVE20, WELCOME10"
                   maxlength="50" required
                   style="text-transform:uppercase">
            <p class="text-xs text-gray-600 mt-1">Customers will enter this exactly as shown (case-insensitive).</p>
            @error('code') <p class="text-red-400 text-xs mt-1">{{ $message }}</p> @enderror
        </div>

        {{-- Type --}}
        <div>
            <label class="label">Discount Type <span class="text-red-400">*</span></label>
            <div class="flex gap-3 mt-1.5">
                <label class="flex-1 cursor-pointer">
                    <input type="radio" name="type" value="fixed" class="sr-only peer" @change="type='fixed'"
                           {{ old('type', $coupon->type ?? 'fixed') === 'fixed' ? 'checked' : '' }}>
                    <div class="border border-gray-700 peer-checked:border-indigo-500 peer-checked:bg-indigo-600/15 rounded-xl p-4 text-center transition-all">
                        <p class="text-lg font-bold text-white">৳</p>
                        <p class="text-sm text-gray-400">Fixed Amount</p>
                    </div>
                </label>
                <label class="flex-1 cursor-pointer">
                    <input type="radio" name="type" value="percentage" class="sr-only peer" @change="type='percentage'"
                           {{ old('type', $coupon->type ?? '') === 'percentage' ? 'checked' : '' }}>
                    <div class="border border-gray-700 peer-checked:border-indigo-500 peer-checked:bg-indigo-600/15 rounded-xl p-4 text-center transition-all">
                        <p class="text-lg font-bold text-white">%</p>
                        <p class="text-sm text-gray-400">Percentage</p>
                    </div>
                </label>
            </div>
        </div>

        {{-- Value --}}
        <div>
            <label class="label">
                <span x-text="type === 'fixed' ? 'Discount Amount (৳)' : 'Discount Percentage (%)'"></span>
                <span class="text-red-400">*</span>
            </label>
            <input type="number" name="value" step="0.01" min="0.01"
                   value="{{ old('value', $coupon->value ?? '') }}"
                   class="input w-full mt-1.5" required
                   :placeholder="type === 'fixed' ? '100' : '20'">
            @error('value') <p class="text-red-400 text-xs mt-1">{{ $message }}</p> @enderror
        </div>

        {{-- Max discount cap (percentage only) --}}
        <div x-show="type === 'percentage'" x-transition>
            <label class="label">Max Discount Cap (৳) <span class="text-gray-500 font-normal">optional</span></label>
            <input type="number" name="max_discount" step="0.01" min="0"
                   value="{{ old('max_discount', $coupon->max_discount ?? '') }}"
                   class="input w-full mt-1.5"
                   placeholder="e.g. 500 — discount won't exceed this">
            <p class="text-xs text-gray-600 mt-1">Leave blank for no cap.</p>
            @error('max_discount') <p class="text-red-400 text-xs mt-1">{{ $message }}</p> @enderror
        </div>

        {{-- Min order --}}
        <div>
            <label class="label">Minimum Order Amount (৳) <span class="text-gray-500 font-normal">optional</span></label>
            <input type="number" name="min_order" step="0.01" min="0"
                   value="{{ old('min_order', $coupon->min_order ?? 0) }}"
                   class="input w-full mt-1.5"
                   placeholder="0 = no minimum">
            @error('min_order') <p class="text-red-400 text-xs mt-1">{{ $message }}</p> @enderror
        </div>

        {{-- Max uses --}}
        <div>
            <label class="label">Max Uses <span class="text-gray-500 font-normal">optional</span></label>
            <input type="number" name="max_uses" min="1"
                   value="{{ old('max_uses', $coupon->max_uses ?? '') }}"
                   class="input w-full mt-1.5"
                   placeholder="Leave blank for unlimited">
            @error('max_uses') <p class="text-red-400 text-xs mt-1">{{ $message }}</p> @enderror
        </div>

        {{-- Expires at --}}
        <div>
            <label class="label">Expiry Date <span class="text-gray-500 font-normal">optional</span></label>
            <input type="datetime-local" name="expires_at"
                   value="{{ old('expires_at', isset($coupon->expires_at) ? $coupon->expires_at->format('Y-m-d\TH:i') : '') }}"
                   class="input w-full mt-1.5">
            <p class="text-xs text-gray-600 mt-1">Leave blank for no expiry.</p>
            @error('expires_at') <p class="text-red-400 text-xs mt-1">{{ $message }}</p> @enderror
        </div>

        {{-- Active toggle --}}
        <div class="flex items-center gap-3 pt-2">
            <input type="checkbox" id="is_active" name="is_active" value="1"
                   class="w-4 h-4 accent-indigo-500"
                   {{ old('is_active', $coupon->is_active ?? true) ? 'checked' : '' }}>
            <label for="is_active" class="text-gray-300 text-sm font-medium cursor-pointer">Active (coupon is usable)</label>
        </div>

        {{-- Submit --}}
        <div class="flex gap-3 pt-2 border-t border-gray-800">
            <button type="submit" class="btn-primary flex-1 py-3">
                {{ isset($coupon) ? 'Update Coupon' : 'Create Coupon 🎟️' }}
            </button>
            <a href="{{ route('admin.coupons.index') }}" class="btn-outline px-6 py-3">Cancel</a>
        </div>
    </form>
</div>
@endsection
