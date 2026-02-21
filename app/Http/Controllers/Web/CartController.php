<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;

class CartController extends Controller
{
    public function index()
    {
        $cart     = session('cart', []);
        $subtotal = collect($cart)->sum(fn($i) => $i['price'] * $i['quantity']);
        $shipping = $subtotal >= 2000 ? 0 : 100;
        $total    = $subtotal + $shipping;

        return view('cart.index', compact('cart', 'subtotal', 'shipping', 'total'));
    }

    public function add(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity'   => 'required|integer|min:1',
            'variant_id' => 'nullable|exists:product_variants,id',
        ]);

        $product = Product::with('activeFlashSale')->findOrFail($request->product_id);
        $variant = null;
        $price = $product->getCurrentPrice();

        if ($request->filled('variant_id')) {
            $variant = \App\Models\ProductVariant::find($request->variant_id);
            if ($variant) {
                $price += $variant->price_modifier;
                $request->merge([
                    'size' => $variant->size,
                    'color' => $variant->color,
                ]);
            }
        }

        $cart    = session('cart', []);
        $key     = $product->id . '-' . ($variant ? $variant->id : ($request->size ?? 'none') . '-' . ($request->color ?? 'none'));

        $activeFlashSaleId = $product->activeFlashSale ? $product->activeFlashSale->id : null;

        if (isset($cart[$key])) {
            $cart[$key]['quantity'] += $request->quantity;
            $cart[$key]['flash_sale_id'] = $activeFlashSaleId;
        } else {
            $cart[$key] = [
                'product_id' => $product->id,
                'variant_id' => $variant ? $variant->id : null,
                'flash_sale_id' => $activeFlashSaleId,
                'name'       => $product->name,
                'price'      => $price,
                'quantity'   => $request->quantity,
                'size'       => $request->size,
                'color'      => $request->color,
                'image'      => is_array($product->images) && count($product->images) > 0
                    ? $product->images[0]
                    : 'https://images.unsplash.com/photo-1523381210434-271e8be1f52b?q=80&w=300',
            ];
        }

        session(['cart' => $cart]);

        // BUY NOW: redirect straight to cart page
        if ($request->input('buy_now') == '1') {
            return redirect()->route('cart.index')
                ->with('success', '"' . $product->name . '" added! Ready to checkout.');
        }

        return back()->with('success', '"' . $product->name . '" added to cart! ✔');
    }

    public function update(Request $request)
    {
        $cart = session('cart', []);
        $key  = $request->key;

        if (isset($cart[$key])) {
            if ($request->action === 'inc') {
                $cart[$key]['quantity']++;
            } elseif ($request->action === 'dec') {
                $cart[$key]['quantity']--;
                if ($cart[$key]['quantity'] <= 0) {
                    unset($cart[$key]);
                }
            }
        }

        session(['cart' => $cart]);
        return back();
    }

    public function remove(Request $request)
    {
        $cart = session('cart', []);
        unset($cart[$request->key]);
        session(['cart' => $cart]);
        return back()->with('success', 'Item removed from cart.');
    }

    public function clear()
    {
        session()->forget('cart');
        return back()->with('success', 'Cart cleared.');
    }
}
