<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Mail\NewOrderMail;
use App\Models\Coupon;
use App\Models\Order;
use App\Models\OrderItem;
use App\Services\SmsService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Raziul\Sslcommerz\Facades\Sslcommerz;

class CheckoutController extends Controller
{
    public function index()
    {
        $cart = session('cart', []);

        if (empty($cart)) {
            return redirect()->route('cart.index')->with('error', 'Your cart is empty.');
        }

        $subtotal        = collect($cart)->sum(fn($i) => $i['price'] * $i['quantity']);
        $dhakaShipping   = 60;
        $outsideShipping = 120;
        // Default shipping shown before district selected (controller won't know, Alpine handles it)
        $shipping        = $outsideShipping;
        $total           = $subtotal + $shipping;

        return view('checkout.index', compact('cart', 'subtotal', 'shipping', 'total', 'dhakaShipping', 'outsideShipping'));
    }

    public function place(Request $request)
    {
        $request->validate([
            'name'           => 'required|string|max:100',
            'phone'          => ['required', 'regex:/^01[3-9]\d{8}$/'],
            'address'        => 'required|string|max:255',
            'district'       => 'required|string|max:100',
            'thana'          => 'required|string|max:100',
            'city'           => 'nullable|string|max:100',
            'payment_method' => 'required|in:cod,bkash,nagad,sslcommerz',
        ]);

        $cart = session('cart', []);

        if (empty($cart)) {
            return redirect()->route('cart.index')->with('error', 'Your cart is empty.');
        }

        $subtotal        = collect($cart)->sum(fn($i) => $i['price'] * $i['quantity']);
        $district        = $request->district;
        $shipping        = strtolower($district) === 'dhaka' ? 60 : 120;

        // Trust the client-computed shipping only as a fallback sanity — server recalculates

        // ── Coupon processing ────────────────────────────────────────────────────
        $couponCode     = null;
        $couponDiscount = 0;
        $coupon         = null;

        if ($request->filled('coupon_code')) {
            $coupon = Coupon::where('code', strtoupper(trim($request->coupon_code)))->first();
            if ($coupon && $coupon->isValid() && $subtotal >= $coupon->min_order) {
                $couponCode     = $coupon->code;
                $couponDiscount = $coupon->calculateDiscount($subtotal);
            }
        }

        $total = $subtotal + $shipping - $couponDiscount;

        $order = Order::create([
            'order_number'     => 'ORD-' . strtoupper(Str::random(8)),
            'user_id'          => auth()->id(),
            'total_amount'     => $subtotal,
            'shipping_charge'  => $shipping,
            'discount'         => 0,
            'coupon_code'      => $couponCode,
            'coupon_discount'  => $couponDiscount,
            'final_amount'     => max(0, $total),
            'payment_method'   => $request->payment_method,
            'payment_status'   => 'pending',
            'status'           => 'pending',
            'delivery_address' => [
                'name'        => $request->name,
                'phone'       => $request->phone,
                'email'       => $request->email,
                'address'     => $request->address,
                'thana'       => $request->thana,
                'district'    => $request->district,
                'city'        => $request->city,
                'postal_code' => $request->postal_code,
                'country'     => 'Bangladesh',
            ],
            'notes' => $request->notes,
        ]);

        foreach ($cart as $item) {
            OrderItem::create([
                'order_id'   => $order->id,
                'product_id' => $item['product_id'],
                'name'       => $item['name'],                 // snapshot product name
                'image'      => $item['image'] ?? null,        // snapshot first image
                'quantity'   => $item['quantity'],
                'price'      => $item['price'],
                'size'       => $item['size'] ?? null,
                'color'      => $item['color'] ?? null,
            ]);
        }

        session()->forget('cart');

        // Increment coupon usage
        if ($coupon) {
            $coupon->incrementUsage();
        }

        // ── Route to SSLCommerz for online payments ─────────────────────────────
        if ($request->payment_method === 'sslcommerz') {
            return $this->initiateSSLCommerz($order);
        }

        // ── COD / offline: send SMS + email then redirect ───────────────────────
        $this->sendCustomerSms($order, $request->phone, $request->name);
        $this->sendAdminEmail($order);

        return redirect()->route('checkout.success', $order)->with('success', 'Order placed successfully!');
    }

    /**
     * Initiate SSLCommerz payment session and redirect the customer to the gateway.
     * Called internally from place() when payment_method === 'sslcommerz'.
     */
    public function initiateSSLCommerz(Order $order)
    {
        /** @var \App\Models\User $user */
        $user    = auth()->user();
        $address = $order->delivery_address ?? [];

        $name  = $address['name']  ?? $user->name;
        $email = $address['email'] ?? $user->email;
        $phone = $address['phone'] ?? '01700000000';
        $city  = $address['city']  ?? 'Dhaka';

        $response = Sslcommerz::setOrder(
                $order->final_amount,
                $order->order_number,  // used as transaction ID by SSLCommerz
                'ClothStore Order'
            )
            ->setCustomer($name, $email, $phone)
            ->setShippingInfo(
                $order->items()->count(),
                $city . ', Bangladesh'
            )
            ->makePayment(['value_a' => $order->id]);
            // value_a carries the order ID through to the callbacks

        if ($response->success()) {
            return redirect($response->gatewayPageURL());
        }

        // Could not initiate — cancel the pending order and fall back to checkout
        $order->update(['payment_status' => 'failed', 'status' => 'cancelled']);

        Log::error('[SSLCommerz] Could not initiate payment session.', [
            'order_id' => $order->id,
        ]);

        return redirect()->route('checkout.index')
            ->with('error', 'Could not connect to payment gateway. Please choose Cash on Delivery or try again.');
    }

    public function success(Order $order)
    {
        return view('checkout.success', compact('order'));
    }

    // ──────────────────────────────────────────────────────────────────────────
    // Private helpers
    // ──────────────────────────────────────────────────────────────────────────

    private function sendCustomerSms(Order $order, string $phone, string $name): void
    {
        $msg = "Dear {$name}, your order #{$order->order_number} has been placed successfully! "
             . "Total: Tk {$order->final_amount}. We will notify you once it's shipped. "
             . "Thank you for shopping with ClothStore! - ClothStore BD";

        app(SmsService::class)->send($phone, $msg);
    }

    private function sendAdminEmail(Order $order): void
    {
        $adminEmail = config('services.admin.email');
        if (! $adminEmail) return;

        try {
            $order->load('items.product');
            Mail::to($adminEmail)->send(new NewOrderMail($order));
        } catch (\Throwable $e) {
            Log::error('[NewOrderMail] Failed: ' . $e->getMessage());
        }
    }
}
