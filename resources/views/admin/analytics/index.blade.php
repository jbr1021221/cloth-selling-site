@extends('layouts.admin')
@section('title', 'Sales Analytics')
@section('page-title', 'Analytics')

@section('content')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<div x-data="analyticsApp()" x-init="initData()" class="space-y-6 max-w-7xl mx-auto pb-8">

    {{-- Header / Filters --}}
    <div class="bg-white border border-gray-100 shadow-sm p-5 sm:p-6 flex flex-col md:flex-row items-center justify-between gap-4">
        <div>
            <h2 class="text-xs font-bold uppercase tracking-widest text-[#1A1A1A] border-l-2 border-[#C9A84C] pl-3">Sales Overview</h2>
            <p class="text-[10px] text-gray-400 mt-1 uppercase tracking-widest ml-3">Interactive business performance metrics</p>
        </div>
        
        <div class="flex items-center gap-3">
            <select x-model="range" @change="fetchData()" class="bg-white border border-gray-200 text-[#1A1A1A] text-xs font-bold uppercase tracking-widest px-4 py-2 focus:outline-none focus:border-[#C9A84C]">
                <option value="today">Today</option>
                <option value="this_week">This Week</option>
                <option value="this_month">This Month</option>
                <option value="custom">Custom Range</option>
            </select>

            <template x-if="range === 'custom'">
                <div class="flex items-center gap-2">
                    <input type="date" x-model="startDate" @change="fetchData()" class="bg-white border border-gray-200 text-[#1A1A1A] text-xs font-bold uppercase tracking-widest px-3 py-2 focus:outline-none focus:border-[#C9A84C]">
                    <span class="text-gray-400 text-xs">to</span>
                    <input type="date" x-model="endDate" @change="fetchData()" class="bg-white border border-gray-200 text-[#1A1A1A] text-xs font-bold uppercase tracking-widest px-3 py-2 focus:outline-none focus:border-[#C9A84C]">
                </div>
            </template>
        </div>
    </div>

    {{-- Loading Overlay Logic embedded inside components basically with opacity transitions --}}
    <div :class="{ 'opacity-50 pointer-events-none': loading }" class="transition-opacity duration-300 space-y-6">

        {{-- 1. Top Stats Row --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
            {{-- Today --}}
            <div class="bg-white border border-gray-100 shadow-sm p-6 relative overflow-hidden group">
                <div class="absolute right-0 top-0 w-16 h-16 bg-gray-50 rounded-bl-full -mr-8 -mt-8 transition-transform group-hover:scale-110"></div>
                <h3 class="text-[10px] font-bold uppercase tracking-widest text-gray-500 mb-1">Today's Revenue</h3>
                <p class="text-2xl font-bold text-[#1A1A1A] playfair">৳<span x-text="stats.today.revenue"></span></p>
                <div class="mt-4 flex items-center gap-1.5 text-[10px] font-bold uppercase tracking-widest" :class="stats.today.change >= 0 ? 'text-green-600' : 'text-red-500'">
                    <span x-text="stats.today.change >= 0 ? '▲' : '▼'"></span>
                    <span x-text="Math.abs(stats.today.change) + '%'"></span>
                    <span class="text-gray-400 font-normal">vs Prev Day</span>
                </div>
            </div>
            
            {{-- This Week --}}
            <div class="bg-white border border-gray-100 shadow-sm p-6 relative overflow-hidden group">
                <div class="absolute right-0 top-0 w-16 h-16 bg-gray-50 rounded-bl-full -mr-8 -mt-8 transition-transform group-hover:scale-110"></div>
                <h3 class="text-[10px] font-bold uppercase tracking-widest text-gray-500 mb-1">This Week</h3>
                <p class="text-2xl font-bold text-[#1A1A1A] playfair">৳<span x-text="stats.week.revenue"></span></p>
                <div class="mt-4 flex items-center gap-1.5 text-[10px] font-bold uppercase tracking-widest" :class="stats.week.change >= 0 ? 'text-green-600' : 'text-red-500'">
                    <span x-text="stats.week.change >= 0 ? '▲' : '▼'"></span>
                    <span x-text="Math.abs(stats.week.change) + '%'"></span>
                    <span class="text-gray-400 font-normal">vs Prev Week</span>
                </div>
            </div>

            {{-- This Month --}}
            <div class="bg-white border border-gray-100 shadow-sm p-6 relative overflow-hidden group">
                <div class="absolute right-0 top-0 w-16 h-16 bg-[#FFFBF0] rounded-bl-full -mr-8 -mt-8 transition-transform group-hover:scale-110"></div>
                <h3 class="text-[10px] font-bold uppercase tracking-widest text-[#C9A84C] mb-1">This Month</h3>
                <p class="text-2xl font-bold text-[#1A1A1A] playfair">৳<span x-text="stats.month.revenue"></span></p>
                <div class="mt-4 flex items-center gap-1.5 text-[10px] font-bold uppercase tracking-widest" :class="stats.month.change >= 0 ? 'text-green-600' : 'text-red-500'">
                    <span x-text="stats.month.change >= 0 ? '▲' : '▼'"></span>
                    <span x-text="Math.abs(stats.month.change) + '%'"></span>
                    <span class="text-gray-400 font-normal">vs Prev Month</span>
                </div>
            </div>

            {{-- All Time --}}
            <div class="bg-[#1A1A1A] border border-[#333] shadow-sm p-6 relative overflow-hidden group">
                <div class="absolute right-0 top-0 w-16 h-16 bg-[#222] rounded-bl-full -mr-8 -mt-8 transition-transform group-hover:scale-110"></div>
                <h3 class="text-[10px] font-bold uppercase tracking-widest text-gray-400 mb-1">All Time Revenue</h3>
                <p class="text-2xl font-bold text-[#C9A84C] playfair">৳<span x-text="stats.all.revenue"></span></p>
                <div class="mt-4 flex items-center gap-1.5 text-[10px] font-bold uppercase tracking-widest text-[#C9A84C]">
                    <span>Lifetime Gross</span>
                </div>
            </div>
        </div>

        {{-- 2. Revenue Line Chart --}}
        <div class="bg-white border border-gray-100 shadow-sm p-6">
            <h2 class="text-xs font-bold uppercase tracking-widest text-[#1A1A1A] mb-4">Revenue Trends <span class="text-gray-400 font-normal ml-2 tracking-normal normal-case">(Selected Period)</span></h2>
            <div class="w-full h-80 relative">
                <canvas id="revenueChart"></canvas>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            {{-- 3. Top Selling Products Table --}}
            <div class="lg:col-span-2 bg-white border border-gray-100 shadow-sm p-6">
                <h2 class="text-xs font-bold uppercase tracking-widest text-[#1A1A1A] mb-4">Top 10 Products <span class="text-gray-400 font-normal ml-2 tracking-normal normal-case">(By Volume)</span></h2>
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr>
                                <th class="py-3 px-2 text-[9px] font-bold uppercase tracking-widest text-gray-500 border-b border-gray-200">Product</th>
                                <th class="py-3 px-2 text-[9px] font-bold uppercase tracking-widest text-gray-500 border-b border-gray-200 text-center">Units Sold</th>
                                <th class="py-3 px-2 text-[9px] font-bold uppercase tracking-widest text-gray-500 border-b border-gray-200 text-right">Revenue</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            <template x-for="prod in topProducts" :key="prod.name">
                                <tr>
                                    <td class="py-3 px-2">
                                        <div class="flex items-center gap-3">
                                            <div class="w-8 h-10 bg-gray-50 border border-gray-200 flex-shrink-0">
                                                <template x-if="prod.image">
                                                    <img :src="prod.image" class="w-full h-full object-cover">
                                                </template>
                                            </div>
                                            <span class="text-xs font-bold text-[#1A1A1A] uppercase tracking-widest" x-text="prod.name"></span>
                                        </div>
                                    </td>
                                    <td class="py-3 px-2 text-center text-[11px] font-bold text-gray-600" x-text="prod.total_units"></td>
                                    <td class="py-3 px-2 text-right text-xs font-bold text-[#C9A84C]">৳<span x-text="Number(prod.total_revenue).toLocaleString()"></span></td>
                                </tr>
                            </template>
                            <template x-if="topProducts.length === 0">
                                <tr><td colspan="3" class="py-6 text-center text-[10px] text-gray-400 font-bold uppercase tracking-widest">No product sales in this period.</td></tr>
                            </template>
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- 4. Sales By Category Donut --}}
            <div class="bg-white border border-gray-100 shadow-sm p-6">
                <h2 class="text-xs font-bold uppercase tracking-widest text-[#1A1A1A] mb-4">Category Split</h2>
                <div class="w-full aspect-square relative flex items-center justify-center">
                    <canvas id="categoryChart"></canvas>
                    <div x-show="salesByCategory.data && salesByCategory.data.length === 0" class="absolute inset-0 flex items-center justify-center text-[10px] text-gray-400 font-bold uppercase tracking-widest">No Data</div>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            {{-- 5. Customer Analytics --}}
            <div class="bg-white border border-gray-100 shadow-sm p-6 flex flex-col justify-between">
                <div>
                    <h2 class="text-xs font-bold uppercase tracking-widest text-[#1A1A1A] mb-6">Audience Growth</h2>
                    
                    <div class="flex items-center justify-between mb-8 pb-8 border-b border-gray-100">
                        <div>
                            <p class="text-[10px] font-bold uppercase tracking-widest text-gray-500 mb-1">New Registrations</p>
                            <p class="text-3xl font-bold text-[#1A1A1A] playfair" x-text="customers.new"></p>
                        </div>
                        <div class="text-right">
                            <p class="text-[10px] font-bold uppercase tracking-widest text-gray-500 mb-1">Return Rate</p>
                            <p class="text-3xl font-bold text-[#C9A84C] playfair" x-text="customers.returning_percent + '%'"></p>
                        </div>
                    </div>

                    <h3 class="text-[10px] font-bold uppercase tracking-widest text-[#1A1A1A] mb-3">Top 5 Customers (Value)</h3>
                    <div class="space-y-3">
                        <template x-for="(cus, i) in customers.top" :key="i">
                            <div class="flex items-center justify-between">
                                <span class="text-[11px] font-bold uppercase tracking-widest text-gray-600 truncate mr-2"><span class="text-[#C9A84C] mr-2" x-text="i+1 + '.'"></span><span x-text="cus.name"></span></span>
                                <span class="text-[11px] font-bold text-[#1A1A1A]">৳<span x-text="Number(cus.total_spent).toLocaleString()"></span></span>
                            </div>
                        </template>
                        <template x-if="customers.top && customers.top.length === 0">
                            <p class="text-[10px] text-gray-400 uppercase tracking-widest font-bold">No active customers</p>
                        </template>
                    </div>
                </div>
            </div>

            {{-- 6. Order Status Breakdown Bar Chart --}}
            <div class="bg-white border border-gray-100 shadow-sm p-6">
                <h2 class="text-xs font-bold uppercase tracking-widest text-[#1A1A1A] mb-4">Pipeline Status</h2>
                <div class="w-full h-64 relative">
                    <canvas id="statusChart"></canvas>
                </div>
            </div>

            {{-- 7. District Wise Orders --}}
            <div class="bg-white border border-gray-100 shadow-sm p-6">
                <h2 class="text-xs font-bold uppercase tracking-widest text-[#1A1A1A] mb-4">Top Regions</h2>
                <div class="overflow-y-auto max-h-[300px] pr-2 custom-scrollbar">
                    <table class="w-full text-left border-collapse">
                        <tbody class="divide-y divide-gray-100">
                            <template x-for="dw in districtWise" :key="dw.district">
                                <tr>
                                    <td class="py-2.5 px-1">
                                        <span class="text-[10px] font-bold text-[#1A1A1A] uppercase tracking-widest" x-text="dw.district"></span>
                                    </td>
                                    <td class="py-2.5 px-1 text-center">
                                        <span class="text-[10px] font-bold text-gray-500" x-text="dw.count + ' Orders'"></span>
                                    </td>
                                    <td class="py-2.5 px-1 text-right">
                                        <span class="text-[10px] font-bold text-[#C9A84C]">৳<span x-text="Number(dw.revenue).toLocaleString()"></span></span>
                                    </td>
                                </tr>
                            </template>
                            <template x-if="districtWise.length === 0">
                                <tr><td colspan="3" class="py-6 text-center text-[10px] text-gray-400 font-bold uppercase tracking-widest">No location data.</td></tr>
                            </template>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
/* Thin scrollbar for the district list */
.custom-scrollbar::-webkit-scrollbar { width: 4px; }
.custom-scrollbar::-webkit-scrollbar-track { background: #F8F8F8; }
.custom-scrollbar::-webkit-scrollbar-thumb { background: #D1D5DB; border-radius: 4px; }
.custom-scrollbar::-webkit-scrollbar-thumb:hover { background: #9CA3AF; }
</style>

<script>
// Chart instance trackers so we can destroy them on reload
let revenueChartInst = null;
let categoryChartInst = null;
let statusChartInst = null;

// The global config for chart.js matching our theme
Chart.defaults.font.family = "'Inter', sans-serif";
Chart.defaults.color = '#9CA3AF'; // text-gray-400

function analyticsApp() {
    return {
        range: 'this_month',
        startDate: '',
        endDate: '',
        loading: true,
        
        // Data stubs
        stats: { today: {revenue:0, change:0}, week: {revenue:0, change:0}, month: {revenue:0, change:0}, all: {revenue:0, change:0} },
        topProducts: [],
        salesByCategory: { labels: [], data: [] },
        customers: { new: 0, returning_percent: 0, top: [] },
        statusBreakdown: { labels: [], data: [] },
        districtWise: [],

        initData() {
            this.fetchData();
        },

        async fetchData() {
            this.loading = true;
            try {
                // Build query params
                let query = `?range=${this.range}`;
                if (this.range === 'custom' && this.startDate && this.endDate) {
                    query += `&start_date=${this.startDate}&end_date=${this.endDate}`;
                }

                const res = await fetch(`{{ route('admin.analytics') }}${query}`, {
                    headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
                });
                const data = await res.json();
                
                // Map Alpine state
                this.stats = data.stats;
                this.topProducts = data.topProducts;
                this.salesByCategory = data.salesByCategory;
                this.customers = data.customers;
                this.statusBreakdown = data.statusBreakdown;
                this.districtWise = data.districtWise;

                // Render Charts
                this.$nextTick(() => {
                    this.renderRevenueChart(data.chart);
                    this.renderCategoryChart(data.salesByCategory);
                    this.renderStatusChart(data.statusBreakdown);
                });

            } catch(e) {
                console.error("Failed to load analytics", e);
            } finally {
                this.loading = false;
            }
        },

        renderRevenueChart(chartData) {
            const ctx = document.getElementById('revenueChart');
            if(revenueChartInst) revenueChartInst.destroy();
            
            revenueChartInst = new Chart(ctx, {
                type: 'line',
                data: {
                    labels: chartData.labels,
                    datasets: [{
                        label: 'Gross Revenue (৳)',
                        data: chartData.data,
                        borderColor: '#C9A84C',
                        backgroundColor: 'rgba(201, 168, 76, 0.1)',
                        borderWidth: 2,
                        tension: 0.3,
                        fill: true,
                        pointBackgroundColor: '#FFFFFF',
                        pointBorderColor: '#C9A84C',
                        pointRadius: 3,
                        pointHoverRadius: 5
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { display: false },
                        tooltip: {
                            backgroundColor: '#1A1A1A',
                            titleFont: { size: 10, weight: 'bold' },
                            bodyFont: { size: 12, weight: 'bold' },
                            padding: 10,
                            displayColors: false,
                            callbacks: {
                                label: function(c) { return '৳' + Number(c.raw).toLocaleString(); }
                            }
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            grid: { color: '#F3F4F6', drawBorder: false },
                            ticks: { callback: function(value) { return '৳'+value; }, font: { size: 10 } }
                        },
                        x: {
                            grid: { display: false, drawBorder: false },
                            ticks: { font: { size: 10 } }
                        }
                    }
                }
            });
        },

        renderCategoryChart(categoryData) {
            const ctx = document.getElementById('categoryChart');
            if(categoryChartInst) categoryChartInst.destroy();

            // Minimalist Gold scale gradient
            const goldPalette = ['#1A1A1A', '#C9A84C', '#E2CA76', '#F5E6B3', '#D1D5DB', '#9CA3AF'];

            categoryChartInst = new Chart(ctx, {
                type: 'doughnut',
                data: {
                    labels: categoryData.labels,
                    datasets: [{
                        data: categoryData.data,
                        backgroundColor: goldPalette.slice(0, categoryData.labels.length),
                        borderWidth: 2,
                        borderColor: '#FFFFFF'
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    cutout: '70%',
                    plugins: {
                        legend: { position: 'bottom', labels: { boxWidth: 10, font: { size: 10, weight: 'bold' } } },
                        tooltip: {
                            backgroundColor: '#1A1A1A',
                            callbacks: { label: function(c) { return ' ' + c.label + ': ৳' + Number(c.raw).toLocaleString(); } }
                        }
                    }
                }
            });
        },

        renderStatusChart(statusData) {
            const ctx = document.getElementById('statusChart');
            if(statusChartInst) statusChartInst.destroy();

            statusChartInst = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: statusData.labels,
                    datasets: [{
                        label: 'Orders',
                        data: statusData.data,
                        backgroundColor: '#C9A84C',
                        borderRadius: 2,
                        barThickness: 20
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { display: false },
                        tooltip: { backgroundColor: '#1A1A1A' }
                    },
                    scales: {
                        y: { beginAtZero: true, grid: { color: '#F3F4F6', drawBorder: false }, ticks: { stepSize: 1, font: { size: 10 } } },
                        x: { grid: { display: false, drawBorder: false }, ticks: { font: { size: 10, weight: 'bold' } } }
                    }
                }
            });
        }
    };
}
</script>
@endsection
