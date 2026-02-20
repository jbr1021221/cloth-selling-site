<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CheckoutController extends Controller
{
    public function index()
    {
        $cart = session('cart', []);

        if (empty($cart)) {
            return redirect()->route('cart.index')->with('error', 'Your cart is empty.');
        }

        $subtotal = collect($cart)->sum(fn($i) => $i['price'] * $i['quantity']);
        $shipping = $subtotal >= 2000 ? 0 : 100;
        $total    = $subtotal + $shipping;

        return view('checkout.index', compact('cart', 'subtotal', 'shipping', 'total'));
    }

    public function place(Request $request)
    {
        $request->validate([
            'name'           => 'required|string|max:100',
            'phone'          => 'required|string|max:20',
            'address'        => 'required|string|max:255',
            'city'           => 'required|string|max:100',
            'payment_method' => 'required|in:cod,bkash,nagad',
        ]);

        $cart = session('cart', []);

        if (empty($cart)) {
            return redirect()->route('cart.index')->with('error', 'Your cart is empty.');
        }

        $subtotal = collect($cart)->sum(fn($i) => $i['price'] * $i['quantity']);
        $shipping = $subtotal >= 2000 ? 0 : 100;
        $total    = $subtotal + $shipping;

        $order = Order::create([
            'order_number'     => 'ORD-' . strtoupper(Str::random(8)),
            'user_id'          => auth()->id(),
            'total_amount'     => $subtotal,
            'shipping_charge'  => $shipping,
            'discount'         => 0,
            'final_amount'     => $total,
            'payment_method'   => $request->payment_method,
            'payment_status'   => 'pending',
            'status'           => 'pending',
            'delivery_address' => [
                'name'        => $request->name,
                'phone'       => $request->phone,
                'email'       => $request->email,
                'address'     => $request->address,
                'city'        => $request->city,
                'district'    => $request->district,
                'postal_code' => $request->postal_code,
                'country'     => $request->country ?? 'Bangladesh',
            ],
            'notes' => $request->notes,
        ]);

        foreach ($cart as $item) {
            OrderItem::create([
                'order_id'   => $order->id,
                'product_id' => $item['product_id'],
                'quantity'   => $item['quantity'],
                'price'      => $item['price'],
                'size'       => $item['size'],
                'color'      => $item['color'],
            ]);
        }

        session()->forget('cart');

        return redirect()->route('checkout.success', $order)->with('success', 'Order placed successfully!');
    }

    public function success(Order $order)
    {
        return view('checkout.success', compact('order'));
    }
}
