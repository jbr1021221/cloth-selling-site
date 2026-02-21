<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\FlashSale;

class FlashSaleController extends Controller
{
    public function index()
    {
        $flashSales = FlashSale::with('product')
            ->active()
            ->get();

        return view('flash-sales.index', compact('flashSales'));
    }
}
