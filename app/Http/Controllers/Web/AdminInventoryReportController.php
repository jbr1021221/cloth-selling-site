<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdminInventoryReportController extends Controller
{
    public function index()
    {
        // 1. Overview Cards
        $totalProducts = Product::count();
        $inStock = Product::where('stock', '>=', 10)->count();
        $lowStockCount = Product::whereBetween('stock', [1, 9])->count();
        $outOfStockCount = Product::where('stock', '<=', 0)->count();

        // 2. Low Stock Table
        $lowStockProducts = Product::whereBetween('stock', [1, 9])
            ->orderBy('stock', 'asc')
            ->get();

        // Need last sold date: subquery to find MAX(created_at) from order_items joining orders where status != cancelled
        foreach ($lowStockProducts as $p) {
            $lastSold = DB::table('order_items')
                ->join('orders', 'order_items.order_id', '=', 'orders.id')
                ->where('order_items.product_id', $p->id)
                ->where('orders.status', '!=', 'cancelled')
                ->max('orders.created_at');
            $p->last_sold_date = $lastSold;
        }

        // 3. Out of Stock Table
        $outOfStockProducts = Product::where('stock', '<=', 0)
            ->orderBy('updated_at', 'desc')
            ->get();

        // 4. Stock Value
        $stockValuesRaw = Product::select(
            'category',
            DB::raw('SUM(price * stock) as total_value'),
            DB::raw('SUM(stock) as total_items')
        )
        ->groupBy('category')
        ->orderBy('total_value', 'desc')
        ->get();

        $totalStockValue = $stockValuesRaw->sum('total_value');

        return view('admin.reports.inventory.index', compact(
            'totalProducts', 'inStock', 'lowStockCount', 'outOfStockCount',
            'lowStockProducts', 'outOfStockProducts', 'stockValuesRaw', 'totalStockValue'
        ));
    }

    public function updateStock(Request $request, Product $product)
    {
        $request->validate([
            'stock' => 'required|integer|min:0'
        ]);

        $product->update([
            'stock' => $request->stock
        ]);

        return back()->with('success', 'Stock updated successfully!');
    }

    public function exportCsv()
    {
        // Simple CSV Export directly
        $filename = "inventory_report_" . date('Y-m-d') . ".csv";
        $headers = array(
            "Content-type"        => "text/csv",
            "Content-Disposition" => "attachment; filename=$filename",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        );

        $products = Product::select('id', 'name', 'category', 'sku', 'price', 'stock')->orderBy('stock', 'asc')->get();

        $columns = array('ID', 'Name', 'Category', 'SKU', 'Price', 'Current Stock', 'Total Value');

        $callback = function() use($products, $columns) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);

            foreach ($products as $p) {
                $row['ID'] = $p->id;
                $row['Name'] = $p->name;
                $row['Category'] = $p->category;
                $row['SKU'] = $p->sku ?: 'N/A';
                $row['Price'] = $p->price;
                $row['Current Stock'] = $p->stock;
                $row['Total Value'] = $p->stock * $p->price;

                fputcsv($file, array($row['ID'], $row['Name'], $row['Category'], $row['SKU'], $row['Price'], $row['Current Stock'], $row['Total Value']));
            }
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
