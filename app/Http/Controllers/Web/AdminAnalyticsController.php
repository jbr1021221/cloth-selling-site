<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\User;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class AdminAnalyticsController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax() || $request->wantsJson()) {
            return $this->getAnalyticsData($request);
        }
        return view('admin.analytics.index');
    }

    private function getAnalyticsData(Request $request)
    {
        $range = $request->get('range', 'this_month');
        $start = null;
        $end = Carbon::now();
        $prevStart = null;
        $prevEnd = null;

        if ($range === 'today') {
            $start = Carbon::today();
            $prevStart = Carbon::yesterday();
            $prevEnd = Carbon::yesterday()->endOfDay();
        } elseif ($range === 'this_week') {
            $start = Carbon::now()->startOfWeek();
            $prevStart = clone $start; $prevStart->subWeek();
            $prevEnd = clone $start; $prevEnd->subSecond();
        } elseif ($range === 'this_month') {
            $start = Carbon::now()->startOfMonth();
            $prevStart = clone $start; $prevStart->subMonth();
            $prevEnd = clone $start; $prevEnd->subSecond();
        } elseif ($range === 'custom') {
            $start = $request->filled('start_date') ? Carbon::parse($request->start_date)->startOfDay() : Carbon::now()->startOfMonth();
            $end = $request->filled('end_date') ? Carbon::parse($request->end_date)->endOfDay() : Carbon::now();
            $diffDays = $start->diffInDays($end) + 1;
            $prevStart = clone $start; $prevStart->subDays($diffDays);
            $prevEnd = clone $end; $prevEnd->subDays($diffDays);
        } else {
            $start = Carbon::now()->startOfMonth();
            $prevStart = clone $start; $prevStart->subMonth();
            $prevEnd = clone $start; $prevEnd->subSecond();
        }

        $allTimeStatusCond = ['pending', 'processing', 'shipped', 'delivered'];
        
        $calcChange = function($current, $prev) {
            if ($prev == 0) return $current > 0 ? 100 : 0;
            return round((($current - $prev) / $prev) * 100, 1);
        };

        $todayRevenue = Order::whereIn('status', $allTimeStatusCond)->whereDate('created_at', Carbon::today())->sum('final_amount');
        $prevTodayRevenue = Order::whereIn('status', $allTimeStatusCond)->whereDate('created_at', Carbon::yesterday())->sum('final_amount');
        
        $weekRevenue = Order::whereIn('status', $allTimeStatusCond)->whereBetween('created_at', [Carbon::now()->startOfWeek(), Carbon::now()])->sum('final_amount');
        $prevWeekRevenue = Order::whereIn('status', $allTimeStatusCond)->whereBetween('created_at', [Carbon::now()->startOfWeek()->subWeek(), Carbon::now()->startOfWeek()->subSecond()])->sum('final_amount');
        
        $monthRevenue = Order::whereIn('status', $allTimeStatusCond)->whereBetween('created_at', [Carbon::now()->startOfMonth(), Carbon::now()])->sum('final_amount');
        $prevMonthRevenue = Order::whereIn('status', $allTimeStatusCond)->whereBetween('created_at', [Carbon::now()->startOfMonth()->subMonth(), Carbon::now()->startOfMonth()->subSecond()])->sum('final_amount');
        
        $allTimeRevenue = Order::whereIn('status', $allTimeStatusCond)->sum('final_amount');

        // Line Chart (Daily revenue within Start & End)
        $dates = [];
        $revenues = [];
        // Cap to avoid huge loops if custom range is too large
        $loopStart = clone $start;
        $loopEnd = clone $end;
        if ($loopStart->diffInDays($loopEnd) > 90) {
            $loopStart = clone $loopEnd;
            $loopStart->subDays(90);
        }
        for ($date = clone $loopStart; $date <= $loopEnd; $date->addDay()) {
            $dates[] = $date->format('M d');
            $rev = Order::whereIn('status', $allTimeStatusCond)->whereDate('created_at', $date)->sum('final_amount');
            $revenues[] = $rev;
        }

        // Top Selling Products
        $topProducts = DB::table('order_items')
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->join('products', 'order_items.product_id', '=', 'products.id')
            ->whereIn('orders.status', $allTimeStatusCond)
            ->whereBetween('orders.created_at', [$start, $end])
            ->select('products.id', 'products.name', DB::raw('SUM(order_items.quantity) as total_units'), DB::raw('SUM(order_items.price * order_items.quantity) as total_revenue'))
            ->groupBy('products.id', 'products.name')
            ->orderByDesc('total_units')
            ->limit(10)
            ->get();
            
        $topProductsFormatted = [];
        foreach($topProducts as $tp) {
            $prod = Product::find($tp->id);
            $img = $prod && is_array($prod->images) && count($prod->images) > 0 ? $prod->images[0] : null;
            $topProductsFormatted[] = [
                'name' => $tp->name,
                'image' => $img,
                'total_units' => (int)$tp->total_units,
                'total_revenue' => (float)$tp->total_revenue
            ];
        }

        // Sales Category
        $salesByCategory = DB::table('order_items')
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->join('products', 'order_items.product_id', '=', 'products.id')
            ->whereIn('orders.status', $allTimeStatusCond)
            ->whereBetween('orders.created_at', [$start, $end])
            ->select('products.category', DB::raw('SUM(order_items.price * order_items.quantity) as category_revenue'))
            ->groupBy('products.category')
            ->orderByDesc('category_revenue')
            ->get();

        // Customer Analytics
        $newCustomers = User::where('role', 'user')->whereBetween('created_at', [$start, $end])->count();
        $uniqueCustomersInRange = Order::whereBetween('created_at', [$start, $end])->whereNotNull('user_id')->distinct('user_id')->pluck('user_id');
        $returningCustomers = 0;
        if ($uniqueCustomersInRange->count() > 0) {
            $returningCustomers = Order::whereIn('user_id', $uniqueCustomersInRange)->where('created_at', '<', $start)->distinct('user_id')->count();
        }
        $returningPercent = $uniqueCustomersInRange->count() > 0 ? round(($returningCustomers / $uniqueCustomersInRange->count()) * 100, 1) : 0;

        $topCustomers = DB::table('orders')
            ->join('users', 'orders.user_id', '=', 'users.id')
            ->whereIn('orders.status', $allTimeStatusCond)
            ->whereBetween('orders.created_at', [$start, $end])
            ->select('users.name', DB::raw('SUM(orders.final_amount) as total_spent'))
            ->groupBy('users.id', 'users.name')
            ->orderByDesc('total_spent')
            ->limit(5)
            ->get();

        // Order Status Breakdown
        $statusBreakdown = DB::table('orders')
            ->whereBetween('created_at', [$start, $end])
            ->select('status', DB::raw('COUNT(*) as count'))
            ->groupBy('status')
            ->pluck('count', 'status')->toArray();
        $statuses = ['pending', 'processing', 'shipped', 'delivered', 'cancelled'];
        $statusData = [];
        foreach($statuses as $st) {
            $statusData[] = $statusBreakdown[$st] ?? 0;
        }

        // District Wise
        $districtWiseRaw = DB::table('orders')
            ->whereBetween('created_at', [$start, $end])
            ->select('delivery_address', 'final_amount')
            ->get();
        $districtAgg = [];
        foreach($districtWiseRaw as $o) {
            $addr = json_decode($o->delivery_address, true);
            $dist = $addr['district'] ?? 'Unknown';
            if (!isset($districtAgg[$dist])) $districtAgg[$dist] = ['count' => 0, 'revenue' => 0];
            $districtAgg[$dist]['count']++;
            $districtAgg[$dist]['revenue'] += $o->final_amount;
        }
        uasort($districtAgg, fn($a, $b) => $b['count'] <=> $a['count']);
        $districtWise = [];
        $dC = 0;
        foreach($districtAgg as $dist => $data) {
            if ($dC++ >= 10) break;
            $districtWise[] = [
                'district' => $dist,
                'count' => $data['count'],
                'revenue' => $data['revenue']
            ];
        }

        return response()->json([
            'stats' => [
                'today' => ['revenue' => $todayRevenue, 'change' => $calcChange($todayRevenue, $prevTodayRevenue)],
                'week'  => ['revenue' => $weekRevenue, 'change' => $calcChange($weekRevenue, $prevWeekRevenue)],
                'month' => ['revenue' => $monthRevenue, 'change' => $calcChange($monthRevenue, $prevMonthRevenue)],
                'all'   => ['revenue' => $allTimeRevenue, 'change' => 0],
            ],
            'chart' => [
                'labels' => $dates,
                'data' => $revenues,
            ],
            'topProducts' => $topProductsFormatted,
            'salesByCategory' => [
                'labels' => $salesByCategory->pluck('category')->toArray(),
                'data' => $salesByCategory->pluck('category_revenue')->toArray(),
            ],
            'customers' => [
                'new' => $newCustomers,
                'returning_percent' => $returningPercent,
                'top' => $topCustomers->toArray()
            ],
            'statusBreakdown' => [
                'labels' => array_map('ucfirst', $statuses),
                'data' => $statusData
            ],
            'districtWise' => $districtWise
        ]);
    }
}
