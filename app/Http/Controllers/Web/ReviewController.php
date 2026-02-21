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
            'images' => 'array|max:3',
            'images.*' => 'image|mimes:jpeg,png,jpg,webp|max:4096'
        ]);

        $userId = auth()->id();

        // Prevent duplicate review (belt-and-suspenders alongside DB constraint)
        $already = Review::where('user_id', $userId)
                         ->where('product_id', $product->id)
                         ->exists();

        if ($already) {
            return back()->with('error', 'You have already reviewed this product.');
        }

        $imageUrls = [];
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $file) {
                // Using simple local storage directly to public output mirroring earlier controllers in project
                $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
                $file->move(public_path('uploads/reviews'), $filename);
                $imageUrls[] = '/uploads/reviews/' . $filename;
            }
        }

        $review = Review::create([
            'user_id'    => $userId,
            'product_id' => $product->id,
            'rating'     => $request->rating,
            'title'      => $request->title,
            'body'       => $request->body,
            'images'     => count($imageUrls) > 0 ? $imageUrls : null,
            'approved'   => true,
        ]);

        if (auth()->user()) {
            auth()->user()->addPoints(10, 'earned', "Reviewed product: " . $product->name);
        }

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
