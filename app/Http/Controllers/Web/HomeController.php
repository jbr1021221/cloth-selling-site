<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\FlashSale;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index()
    {
        $featuredProducts = Product::where('is_active', true)
            ->latest()
            ->take(8)
            ->get();

        $flashSales = FlashSale::with('product')
            ->active()
            ->latest('starts_at')
            ->take(4)
            ->get();

        return view('home', compact('featuredProducts', 'flashSales'));
    }
}
