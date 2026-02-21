<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Wishlist;
use Illuminate\Http\Request;

class WishlistController extends Controller
{
    /**
     * Show the authenticated user's wishlist page.
     */
    public function index()
    {
        $wishlistItems = Wishlist::with('product')
            ->where('user_id', auth()->id())
            ->latest()
            ->get();

        // Filter out any orphaned wishlist items (product deleted)
        $wishlistItems = $wishlistItems->filter(fn($w) => $w->product && $w->product->is_active);

        return view('wishlist.index', compact('wishlistItems'));
    }

    /**
     * Toggle a product's wishlist status for the authenticated user.
     * Called via fetch() from Alpine.js — always returns JSON.
     *
     * POST /wishlist/{product}/toggle
     * Returns: { wishlisted: bool, count: int }
     */
    public function toggle(Product $product)
    {
        $userId    = auth()->id();
        $existing  = Wishlist::where('user_id', $userId)
                             ->where('product_id', $product->id)
                             ->first();

        if ($existing) {
            $existing->delete();
            $wishlisted = false;
        } else {
            Wishlist::create([
                'user_id'    => $userId,
                'product_id' => $product->id,
            ]);
            $wishlisted = true;
        }

        $count = Wishlist::where('user_id', $userId)->count();

        return response()->json([
            'wishlisted' => $wishlisted,
            'count'      => $count,
        ]);
    }
}
