@extends('layouts.admin')
@section('title', 'Orders Management')
@section('page-title', 'Orders')

@section('content')

<div x-data="orderManager()" x-init="init()" class="space-y-6">

    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-8">
        <div>
            <h2 class="text-xs font-bold uppercase tracking-widest text-[#1A1A1A] border-l-2 border-[#C9A84C] pl-3">Order History</h2>
            <p class="text-[10px] text-gray-400 mt-1 uppercase tracking-widest ml-3">Manage and track all customer orders</p>
        </div>
        
        <div class="flex items-center gap-3">
            <a href="{{ route('admin.orders.export') }}" id="export-btn" class="bg-white border border-gray-200 text-[#1A1A1A] hover:border-[#C9A84C] hover:text-[#C9A84C] text-[10px] font-bold uppercase tracking-widest px-6 py-2.5 transition-colors shadow-sm flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                Export All
            </a>
        </div>
    </div>

    {{-- Advanced Filters Panel --}}
    <div class="bg-white border border-gray-100 shadow-sm p-5 sm:p-6 mb-6">
        <form @submit.prevent="fetchOrders" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
            {{-- Search --}}
            <div class="lg:col-span-2">
                <label class="text-[10px] font-bold uppercase tracking-widest text-gray-500 mb-2 block">Search Orders</label>
                <div class="relative">
                    <input type="text" x-model="filters.search" @input.debounce.500ms="fetchOrders" placeholder="Order ID, Customer Name, Phone..."
                           class="w-full bg-white border border-gray-200 pl-10 pr-4 py-2 text-sm focus:outline-none focus:border-[#C9A84C] focus:ring-1 focus:ring-[#C9A84C]">
                    <svg class="w-4 h-4 text-gray-400 absolute left-3 top-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                </div>
            </div>

            {{-- Status --}}
            <div>
                <label class="text-[10px] font-bold uppercase tracking-widest text-gray-500 mb-2 block">Status</label>
                <select x-model="filters.status" @change="fetchOrders" class="w-full bg-white border border-gray-200 px-4 py-2 text-[11px] font-bold uppercase tracking-widest text-[#1A1A1A] focus:outline-none focus:border-[#C9A84C] focus:ring-1 focus:ring-[#C9A84C]">
                    <option value="">All Statuses</option>
                    <option value="pending">Pending</option>
                    <option value="processing">Processing</option>
                    <option value="shipped">Shipped</option>
                    <option value="delivered">Delivered</option>
                    <option value="cancelled">Cancelled</option>
                </select>
            </div>

            {{-- Payment Method --}}
            <div>
                <label class="text-[10px] font-bold uppercase tracking-widest text-gray-500 mb-2 block">Payment</label>
                <select x-model="filters.payment_method" @change="fetchOrders" class="w-full bg-white border border-gray-200 px-4 py-2 text-[11px] font-bold uppercase tracking-widest text-[#1A1A1A] focus:outline-none focus:border-[#C9A84C] focus:ring-1 focus:ring-[#C9A84C]">
                    <option value="">All Methods</option>
                    <option value="cod">COD</option>
                    <option value="sslcommerz">Digital Payment</option>
                </select>
            </div>

            {{-- Date From --}}
            <div>
                <label class="text-[10px] font-bold uppercase tracking-widest text-gray-500 mb-2 block">Date From</label>
                <input type="date" x-model="filters.date_from" @change="fetchOrders" class="w-full bg-white border border-gray-200 px-4 py-2 text-[11px] font-bold uppercase tracking-widest text-[#1A1A1A] focus:outline-none focus:border-[#C9A84C] focus:ring-1 focus:ring-[#C9A84C]">
            </div>

            {{-- Date To --}}
            <div>
                <label class="text-[10px] font-bold uppercase tracking-widest text-gray-500 mb-2 block">Date To</label>
                <input type="date" x-model="filters.date_to" @change="fetchOrders" class="w-full bg-white border border-gray-200 px-4 py-2 text-[11px] font-bold uppercase tracking-widest text-[#1A1A1A] focus:outline-none focus:border-[#C9A84C] focus:ring-1 focus:ring-[#C9A84C]">
            </div>

            {{-- Location --}}
            <div class="lg:col-span-2">
                <label class="text-[10px] font-bold uppercase tracking-widest text-gray-500 mb-2 block">District</label>
                <input type="text" x-model="filters.district" @input.debounce.500ms="fetchOrders" placeholder="e.g. Dhaka, Chittagong..." class="w-full bg-white border border-gray-200 px-4 py-2 text-sm focus:outline-none focus:border-[#C9A84C] focus:ring-1 focus:ring-[#C9A84C]">
            </div>
            
            <div class="lg:col-span-4 flex justify-between items-center pt-2">
                <button type="button" @click="resetFilters" class="text-[10px] font-bold uppercase tracking-widest text-red-500 hover:text-red-700 underline underline-offset-4 decoration-red-200 hover:decoration-red-500 transition-colors">
                    Clear Filters
                </button>
            </div>
        </form>
    </div>

    {{-- Bulk Actions Bar (Appears when items are selected) --}}
    <div x-show="selectedOrders.length > 0" x-transition.opacity style="display: none;" class="bg-[#1A1A1A] text-white p-4 shadow-xl flex items-center justify-between sticky top-0 z-40 rounded-sm">
        <div class="flex items-center gap-4">
            <span class="text-xs font-bold tracking-widest bg-[#C9A84C] px-3 py-1 rounded-sm"><span x-text="selectedOrders.length"></span> Selected</span>
        </div>
        
        <form method="POST" action="{{ route('admin.orders.bulkStatus') }}" class="flex items-center gap-3">
            @csrf
            <template x-for="id in selectedOrders">
                <input type="hidden" name="order_ids[]" :value="id">
            </template>
            <select name="status" class="bg-gray-800 border border-gray-700 text-white text-[10px] font-bold uppercase tracking-widest px-4 py-2 focus:outline-none focus:border-[#C9A84C]">
                <option value="">-- Change Status --</option>
                <option value="pending">Pending</option>
                <option value="processing">Processing</option>
                <option value="shipped">Shipped</option>
                <option value="delivered">Delivered</option>
                <option value="cancelled">Cancelled</option>
            </select>
            <button type="submit" class="bg-[#C9A84C] hover:bg-[#b08a38] text-white text-[10px] font-bold uppercase tracking-[0.1em] px-6 py-2 transition-colors">
                Apply Action
            </button>
        </form>
    </div>

    {{-- Orders Table Container --}}
    <div class="bg-white border border-gray-100 shadow-sm relative">
        <div x-show="loading" class="absolute inset-0 bg-white/50 backdrop-blur-sm z-20 flex items-center justify-center">
            <svg class="animate-spin h-8 w-8 text-[#C9A84C]" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
        </div>
        <div id="table-container" x-html="tableHtml">
            @include('admin.orders.partials.table', ['orders' => $orders])
        </div>
    </div>

</div>

@push('scripts')
<script>
    function orderManager() {
        return {
            loading: false,
            allSelected: false,
            selectedOrders: [],
            filters: {
                search: '',
                status: '',
                payment_method: '',
                date_from: '',
                date_to: '',
                district: '',
                min_amount: '',
                max_amount: ''
            },
            tableHtml: '',
            
            init() {
                // Intercept pagination clicks
                document.addEventListener('click', e => {
                    const el = e.target.closest('a');
                    if(el && el.href && el.href.includes('orders') && el.href.includes('page=')) {
                        e.preventDefault();
                        this.fetchOrders(el.href);
                    }
                });
                
                // Keep export button link updated with active filters
                this.$watch('filters', (newVal) => {
                    let url = new URL('{{ route('admin.orders.export') }}');
                    Object.keys(newVal).forEach(key => {
                        if(newVal[key]) url.searchParams.set(key, newVal[key]);
                    });
                    document.getElementById('export-btn').href = url.toString();
                }, { deep: true });
            },

            fetchOrders(url = null) {
                this.loading = true;
                this.selectedOrders = []; // Note: bulk selections are reset per page load to ensure accuracy

                let fetchUrl = typeof url === 'string' ? url : '{{ route('admin.orders') }}';
                let params = new URLSearchParams();
                
                Object.keys(this.filters).forEach(key => {
                    if (this.filters[key]) params.append(key, this.filters[key]);
                });

                if (typeof url !== 'string' || !url.includes('?')) {
                    fetchUrl += '?' + params.toString();
                } else if(params.toString()) {
                    fetchUrl += '&' + params.toString();
                }

                fetch(fetchUrl, {
                    headers: { 'X-Requested-With': 'XMLHttpRequest' }
                })
                .then(res => res.json())
                .then(data => {
                    this.tableHtml = data.html;
                    this.loading = false;
                    this.allSelected = false;
                });
            },

            resetFilters() {
                this.filters = {search: '', status: '', payment_method: '', date_from: '', date_to: '', district: '', min_amount: '', max_amount: ''};
                this.fetchOrders();
            },

            toggleAll(e) {
                this.allSelected = e.target.checked;
                if(this.allSelected) {
                    const checkboxes = document.querySelectorAll('input[x-model="selectedOrders"]');
                    this.selectedOrders = Array.from(checkboxes).map(cb => cb.value);
                } else {
                    this.selectedOrders = [];
                }
            }
        }
    }
</script>
@endpush
@endsection
