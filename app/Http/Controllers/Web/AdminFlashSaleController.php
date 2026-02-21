<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\FlashSale;
use App\Models\Product;
use Illuminate\Http\Request;

class AdminFlashSaleController extends Controller
{
    public function index()
    {
        $flashSales = FlashSale::with('product')->latest()->get();
        // Get products that don't have an active flash sale
        $products = Product::where('is_active', true)->orderBy('name')->get();
        return view('admin.flash-sales.index', compact('flashSales', 'products'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'product_id'   => 'required|exists:products,id',
            'sale_price'   => 'required|numeric|min:0',
            'starts_at'    => 'required|date',
            'ends_at'      => 'required|date|after:starts_at',
            'max_quantity' => 'required|integer|min:1',
        ]);

        FlashSale::create([
            'product_id'   => $request->product_id,
            'sale_price'   => $request->sale_price,
            'starts_at'    => $request->starts_at,
            'ends_at'      => $request->ends_at,
            'max_quantity' => $request->max_quantity,
            'is_active'    => true,
            'sold_count'   => 0
        ]);

        return redirect()->route('admin.flash-sales.index')->with('success', 'Flash Sale created successfully!');
    }

    public function destroy(FlashSale $flashSale)
    {
        $flashSale->delete();
        return redirect()->route('admin.flash-sales.index')->with('success', 'Flash Sale deleted successfully!');
    }
}
