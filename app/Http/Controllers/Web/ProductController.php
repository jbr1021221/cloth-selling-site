<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $query = Product::where('is_active', true);

        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }

        if ($request->filled('search')) {
            $query->where('name', 'LIKE', '%' . $request->search . '%');
        }

        if ($request->filled('min_price')) {
            $query->where('price', '>=', $request->min_price);
        }

        if ($request->filled('max_price')) {
            $query->where('price', '<=', $request->max_price);
        }

        if ($request->filled('size')) {
            $query->whereJsonContains('sizes', $request->size);
        }

        switch ($request->sort) {
            case 'price_asc':  $query->orderBy('price', 'asc'); break;
            case 'price_desc': $query->orderBy('price', 'desc'); break;
            default:           $query->latest(); break;
        }

        $products = $query->paginate(12);

        return view('products.index', compact('products'));
    }

    public function show($id)
    {
        if (request()->has('ref') && request('ref') !== (string)auth()->id()) {
            session(['referred_by' => request('ref')]);
        }

        $product = Product::with(['variants', 'activeFlashSale'])->where('is_active', true)->findOrFail($id);

        $relatedProducts = Product::where('category', $product->category)
            ->where('id', '!=', $product->id)
            ->where('is_active', true)
            ->take(4)
            ->get();

        // Reviews data
        $reviews    = $product->reviews()->with('user')->get();
        $avgRating  = $reviews->avg('rating') ?? 0;
        $ratingCount = $reviews->count();
        $userReview = \Illuminate\Support\Facades\Auth::check()
            ? $reviews->firstWhere('user_id', \Illuminate\Support\Facades\Auth::id())
            : null;

        return view('products.show', compact('product', 'relatedProducts', 'reviews', 'avgRating', 'ratingCount', 'userReview'));
    }

    /**
     * Live search suggestions endpoint.
     * GET /search/suggestions?q=keyword
     * Returns up to 8 matching active products as JSON.
     */
    public function searchSuggestions(Request $request)
    {
        $query = trim($request->get('q', ''));

        if (strlen($query) < 2) {
            return response()->json([]);
        }

        $products = Product::where('is_active', true)
            ->where(function ($q) use ($query) {
                $q->where('name',        'LIKE', "%{$query}%")
                  ->orWhere('category',  'LIKE', "%{$query}%")
                  ->orWhere('description', 'LIKE', "%{$query}%");
            })
            ->orderByRaw("CASE WHEN name LIKE ? THEN 0 ELSE 1 END", ["{$query}%"]) // exact-prefix first
            ->take(8)
            ->get(['id', 'name', 'category', 'price', 'discount_price', 'images', 'stock']);

        $fallbackImage = 'https://images.unsplash.com/photo-1523381210434-271e8be1f52b?q=80&w=120&auto=format&fit=crop';

        return response()->json(
            $products->map(fn($p) => [
                'id'          => $p->id,
                'name'        => $p->name,
                'category'    => $p->category,
                'price'       => $p->getCurrentPrice(),
                'original'    => $p->price,
                'has_discount'=> $p->getHasDiscount(),
                'image'       => is_array($p->images) && count($p->images) > 0
                                     ? $p->images[0]
                                     : $fallbackImage,
                'stock'       => $p->stock,
                'url'         => route('products.show', $p->id),
            ])
        );
    }
}
