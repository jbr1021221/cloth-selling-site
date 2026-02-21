<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Review;
use Illuminate\Http\Request;

class ReviewController extends Controller
{
    /**
     * Store a new review for a product.
     * One review per user per product — enforced by DB unique constraint + validation here.
     */
    public function store(Request $request, Product $product)
    {
        $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'title'  => 'nullable|string|max:120',
            'body'   => 'nullable|string|max:2000',
        ]);

        $userId = auth()->id();

        // Prevent duplicate review (belt-and-suspenders alongside DB constraint)
        $already = Review::where('user_id', $userId)
                         ->where('product_id', $product->id)
                         ->exists();

        if ($already) {
            return back()->with('error', 'You have already reviewed this product.');
        }

        Review::create([
            'user_id'    => $userId,
            'product_id' => $product->id,
            'rating'     => $request->rating,
            'title'      => $request->title,
            'body'       => $request->body,
            'approved'   => true,
        ]);

        return back()->with('success', 'Thank you for your review!');
    }

    /**
     * Delete the authenticated user's own review.
     */
    public function destroy(Review $review)
    {
        if ($review->user_id !== auth()->id()) {
            abort(403, 'You can only delete your own reviews.');
        }

        $review->delete();

        return back()->with('success', 'Review deleted.');
    }
}
