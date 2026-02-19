<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = \App\Models\Product::with('vendor');

        if ($request->has('category')) {
            $query->where('category', $request->category);
        }

        if ($request->has('subcategory')) {
            $query->where('subcategory', $request->subcategory);
        }
        
        if ($request->has('search')) {
             $search = $request->search;
             $query->where(function($q) use ($search) {
                 $q->where('name', 'like', "%{$search}%")
                   ->orWhere('description', 'like', "%{$search}%");
             });
        }

        return $query->get();
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string',
            'description' => 'required|string',
            'category' => 'required|string',
            'subcategory' => 'nullable|string',
            'price' => 'required|numeric',
            'discount_price' => 'nullable|numeric',
            'images' => 'nullable|array',
            'sizes' => 'nullable|array',
            'colors' => 'nullable|array',
            'stock' => 'required|integer',
            'sku' => 'nullable|string|unique:products',
            'vendor_id' => 'nullable|exists:vendors,id',
            'is_active' => 'boolean'
        ]);

        return \App\Models\Product::create($validated);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        return \App\Models\Product::with('vendor')->findOrFail($id);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $product = \App\Models\Product::findOrFail($id);
        
        $validated = $request->validate([
            'name' => 'string',
            'description' => 'string',
            'category' => 'string',
            'subcategory' => 'nullable|string',
            'price' => 'numeric',
            'discount_price' => 'nullable|numeric',
            'images' => 'nullable|array',
            'sizes' => 'nullable|array',
            'colors' => 'nullable|array',
            'stock' => 'integer',
            'sku' => 'nullable|string|unique:products,sku,'.$product->id,
            'vendor_id' => 'nullable|exists:vendors,id',
            'is_active' => 'boolean'
        ]);

        $product->update($validated);
        return $product;
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        \App\Models\Product::destroy($id);
        return response()->noContent();
    }
}
