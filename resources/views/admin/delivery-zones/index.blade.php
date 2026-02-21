@extends('layouts.admin')
@section('title', 'Delivery Zones')
@section('page-title', 'Delivery Zones')

@section('content')
<div class="space-y-6 max-w-7xl mx-auto">
    <div class="bg-white border border-gray-100 shadow-sm p-6 sm:p-8">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-8 border-b border-gray-100 pb-6">
            <div>
                <h2 class="text-xs font-bold uppercase tracking-widest text-[#1A1A1A] border-l-2 border-[#C9A84C] pl-3">District Management</h2>
                <p class="text-[10px] text-gray-400 mt-1 uppercase tracking-widest ml-3">Configure shipping fees and ETA per district</p>
            </div>
            <div class="text-[10px] font-bold text-gray-500 uppercase tracking-widest">
                {{ $zones->count() }} Total Districts
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr>
                        <th class="py-4 px-4 text-[9px] font-bold uppercase tracking-widest text-gray-500 bg-gray-50 border-b border-gray-200">District Name</th>
                        <th class="py-4 px-4 text-[9px] font-bold uppercase tracking-widest text-gray-500 bg-gray-50 border-b border-gray-200">Delivery Charge (৳)</th>
                        <th class="py-4 px-4 text-[9px] font-bold uppercase tracking-widest text-gray-500 bg-gray-50 border-b border-gray-200">Estimated Delivery</th>
                        <th class="py-4 px-4 text-[9px] font-bold uppercase tracking-widest text-gray-500 bg-gray-50 border-b border-gray-200">Status</th>
                        <th class="py-4 px-4 text-[9px] font-bold uppercase tracking-widest text-gray-500 bg-gray-50 border-b border-gray-200 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 bg-white">
                    @foreach($zones as $zone)
                        <tr class="hover:bg-gray-50 transition-colors" x-data="{ editing: false }">
                            
                            {{-- View Mode --}}
                            <template x-if="!editing">
                                <>
                                    <td class="py-4 px-4 text-[11px] font-bold text-[#1A1A1A] tracking-wider uppercase">
                                        {{ $zone->district_name }}
                                        @if($zone->district_name == 'Dhaka' || $zone->district_name == 'Chittagong')
                                            <span class="ml-2 text-[8px] bg-blue-50 text-blue-600 px-1 border border-blue-100 uppercase">Core</span>
                                        @endif
                                    </td>
                                    <td class="py-4 px-4 text-xs font-bold text-[#C9A84C]">৳{{ number_format($zone->delivery_charge) }}</td>
                                    <td class="py-4 px-4 text-[10px] text-gray-500 tracking-wider uppercase font-bold">{{ $zone->estimated_days }}</td>
                                    <td class="py-4 px-4">
                                        @if($zone->is_active)
                                            <span class="inline-flex px-2 py-0.5 text-[9px] font-bold uppercase tracking-widest rounded-full bg-green-50 text-green-600 border border-green-200">Active</span>
                                        @else
                                            <span class="inline-flex px-2 py-0.5 text-[9px] font-bold uppercase tracking-widest rounded-full bg-red-50 text-red-600 border border-red-200">Disabled</span>
                                        @endif
                                    </td>
                                    <td class="py-4 px-4 text-right">
                                        <button @click="editing = true" class="text-[9px] font-bold uppercase tracking-widest text-[#1A1A1A] hover:text-[#C9A84C] underline underline-offset-4 decoration-gray-200 hover:decoration-[#C9A84C] transition-colors">
                                            Edit
                                        </button>
                                    </td>
                                </>
                            </template>

                            {{-- Edit Mode --}}
                            <template x-if="editing">
                                <td colspan="5" class="p-0 bg-[#FAFAF8] shadow-[inset_0_2px_4px_rgba(0,0,0,0.02)] border-b-2 border-[#1A1A1A]">
                                    <form method="POST" action="{{ route('admin.delivery-zones.update', $zone->id) }}" class="p-4 grid grid-cols-1 md:grid-cols-5 gap-4 items-end">
                                        @csrf
                                        @method('PUT')
                                        <div>
                                            <label class="text-[9px] font-bold uppercase tracking-widest text-gray-400 block mb-1">District</label>
                                            <input type="text" value="{{ $zone->district_name }}" disabled class="w-full bg-gray-100 border border-gray-200 px-3 py-2 text-xs text-gray-500 cursor-not-allowed uppercase font-bold tracking-widest">
                                        </div>
                                        <div>
                                            <label class="text-[9px] font-bold uppercase tracking-widest text-gray-400 block mb-1">Charge (৳)</label>
                                            <input type="number" name="delivery_charge" value="{{ $zone->delivery_charge }}" required min="0" step="0.01" class="w-full bg-white border border-[#C9A84C]/50 focus:border-[#C9A84C] px-3 py-2 text-xs text-[#1A1A1A] focus:outline-none">
                                        </div>
                                        <div>
                                            <label class="text-[9px] font-bold uppercase tracking-widest text-gray-400 block mb-1">Duration text</label>
                                            <input type="text" name="estimated_days" value="{{ $zone->estimated_days }}" required class="w-full bg-white border border-[#C9A84C]/50 focus:border-[#C9A84C] px-3 py-2 text-xs text-[#1A1A1A] focus:outline-none">
                                        </div>
                                        <div class="flex items-center h-[34px] px-2 text-[10px] font-bold uppercase tracking-widest text-gray-600">
                                            <label class="flex items-center gap-2 cursor-pointer">
                                                <input type="checkbox" name="is_active" value="1" {{ $zone->is_active ? 'checked' : '' }} class="w-4 h-4 text-[#C9A84C] focus:ring-[#C9A84C] border-gray-300 rounded">
                                                Enable Delivery
                                            </label>
                                        </div>
                                        <div class="flex justify-end gap-2">
                                            <button type="button" @click="editing = false" class="bg-white border border-gray-200 text-[#1A1A1A] hover:bg-gray-50 text-[9px] font-bold uppercase tracking-widest px-4 py-2 transition-colors">
                                                Cancel
                                            </button>
                                            <button type="submit" class="bg-[#C9A84C] hover:bg-[#b08a38] text-white text-[9px] font-bold uppercase tracking-widest px-4 py-2 transition-colors shadow-sm">
                                                Save
                                            </button>
                                        </div>
                                    </form>
                                </td>
                            </template>

                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
