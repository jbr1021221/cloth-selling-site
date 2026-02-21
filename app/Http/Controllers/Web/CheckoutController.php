<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Mail\NewOrderMail;
use App\Models\Coupon;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\LoyaltyPoint;
use App\Models\User;
use App\Models\DeliveryZone;
use App\Services\SmsService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
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

        $subtotal = collect($cart)->sum(fn($i) => $i['price'] * $i['quantity']);
        $tierDiscountPercentage = 0;
        
        $deliveryZones = DeliveryZone::where('is_active', true)->orderBy('district_name')->get();

        /** @var \App\Models\User|null $user */
        $user = Auth::user();
        if ($user) {
            if ($user->tier === 'diamond') {
                $tierDiscountPercentage = 0.10;
            } elseif ($user->tier === 'gold') {
                $tierDiscountPercentage = 0.05;
            }
        }

        $tierDiscount = $subtotal * $tierDiscountPercentage;
        
        // Initial defaults (overridden in frontend by Alpine.js based on zone selection)
        $shipping = count($deliveryZones) > 0 ? $deliveryZones->first()->delivery_charge : 0;
        if (\App\Models\Setting::get('free_shipping_enabled', '0') == '1' && $subtotal >= (int)\App\Models\Setting::get('free_shipping_min_order', '999')) {
            $shipping = 0;
        }
        
        // Quick override for tier
        if ($user && ($user->tier === 'diamond' || ($user->tier === 'gold' && $subtotal > 500))) {
            $shipping = 0;
        }

        $total = $subtotal + $shipping - $tierDiscount;

        return view('checkout.index', compact('cart', 'subtotal', 'shipping', 'total', 'tierDiscount', 'tierDiscountPercentage', 'deliveryZones'));
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
        $subtotal = collect($cart)->sum(fn($i) => $i['price'] * $i['quantity']);

        if ($request->payment_method === 'cod' && (\App\Models\Setting::get('cod_enabled', '1') != '1' || $subtotal < (int)\App\Models\Setting::get('cod_min_order', 0))) {
            return back()->withInput()->with('error', 'Cash on Delivery is not available for this order.');
        }

        if ($request->payment_method === 'sslcommerz' && \App\Models\Setting::get('sslcommerz_enabled', '1') != '1') {
            return back()->withInput()->with('error', 'Online payment is currently unavailable.');
        }

        if (empty($cart)) {
            return redirect()->route('cart.index')->with('error', 'Your cart is empty.');
        }

        $district = $request->district;

        $zone = DeliveryZone::where('district_name', $district)->where('is_active', true)->first();
        if (!$zone) {
            return back()->withInput()->with('error', 'We currently do not deliver to the selected district.');
        }

        $shipping = $zone->delivery_charge;

        if (\App\Models\Setting::get('free_shipping_enabled', '0') == '1' && $subtotal >= (int)\App\Models\Setting::get('free_shipping_min_order', '999')) {
            $shipping = 0;
        }
        $tierDiscount = 0;

        /** @var \App\Models\User|null $user */
        $user = Auth::user();

        if ($user) {
            if ($user->tier === 'diamond') {
                $shipping = 0;
                $tierDiscount = $subtotal * 0.10;
            } elseif ($user->tier === 'gold') {
                if ($subtotal > 500) {
                    $shipping = 0;
                }
                $tierDiscount = $subtotal * 0.05;
            }
        }

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

        // ── Points processing ────────────────────────────────────────────────────
        $pointsApplied  = 0;
        $pointsDiscount = 0;

        if ($user && $request->has('use_points') && $request->use_points == '1' && $user->total_points > 0) {
            // Calculate how many points can be used (assuming max points usage covering subtotal only, or just using all available)
            // 100 points = 10 discount. Wait, the ratio is requested as: 100 points = ৳10 -> meaning 1 point = ৳0.1
            $availableDiscountValue = $user->total_points * 0.1;
            
            // Cannot discount more than the subtotal minus coupon.
            $maxDiscountable = max(0, $subtotal - $couponDiscount);
            
            if ($availableDiscountValue > $maxDiscountable) {
                // If they have more points than needed
                $pointsDiscount = $maxDiscountable;
                $pointsApplied = (int)($maxDiscountable / 0.1); 
            } else {
                // Apply all points
                $pointsDiscount = $availableDiscountValue;
                $pointsApplied = $user->total_points;
            }
        }

        $total = $subtotal + $shipping - $couponDiscount - $pointsDiscount - $tierDiscount;

        $order = Order::create([
            'order_number'     => 'ORD-' . strtoupper(Str::random(8)),
            'user_id'          => \Illuminate\Support\Facades\Auth::id(),
            'total_amount'     => $subtotal,
            'shipping_charge'  => $shipping,
            'discount'         => 0,
            'coupon_code'      => $couponCode,
            'coupon_discount'  => $couponDiscount,
            'tier_discount'    => $tierDiscount,
            'points_applied'   => $pointsApplied,
            'points_discount'  => $pointsDiscount,
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
                'order_id'           => $order->id,
                'product_id'         => $item['product_id'],
                'product_variant_id' => $item['variant_id'] ?? null,
                'name'               => $item['name'],                 // snapshot product name
                'image'              => $item['image'] ?? null,        // snapshot first image
                'quantity'           => $item['quantity'],
                'price'              => $item['price'],
                'size'               => $item['size'] ?? null,
                'color'              => $item['color'] ?? null,
            ]);

            // Deduct stock conditionally
            if (isset($item['variant_id']) && $item['variant_id']) {
                \App\Models\ProductVariant::where('id', $item['variant_id'])->decrement('stock', $item['quantity']);
            } else {
                \App\Models\Product::where('id', $item['product_id'])->decrement('stock', $item['quantity']);
            }

            // Increment FlashSale count if sold via FlashSale
            if (isset($item['flash_sale_id']) && $item['flash_sale_id']) {
                \App\Models\FlashSale::where('id', $item['flash_sale_id'])->increment('sold_count', $item['quantity']);
            }
        }

        session()->forget('cart');

        // Increment coupon usage
        if ($coupon) {
            $coupon->incrementUsage();
        }

        // Deduct loyalty points if used
        if ($pointsApplied > 0 && $user) {
            $user->addPoints(-$pointsApplied, 'redeemed', 'Redeemed for order #' . $order->order_number, $order->id);
        }

        // Award points for this purchase (1 point per ৳10 spent) based on final_amount
        if ($user && $order->final_amount > 0) {
            $pointsEarned = floor($order->final_amount / 10);
            if ($pointsEarned > 0) {
                $user->addPoints($pointsEarned, 'earned', 'Earned from order #' . $order->order_number, $order->id);
            }
            
            // Check for first order bonus
            if ($user->orders()->count() === 1) { // 1 because this order was just created
                $user->addPoints(50, 'earned', 'First order bonus!', $order->id);
                
                // Reward referring user if exists
                if ($user->referred_by_id) {
                    $referrer = \App\Models\User::find($user->referred_by_id);
                    if ($referrer) {
                        $referrer->addPoints(75, 'earned', "Referral bonus for user {$user->name}'s first order", $order->id);
                    }
                }
            }
        }

        // Share & Earn bonus via link referral
        if (session()->has('referred_by')) {
            $sharerId = session('referred_by');
            if ($sharerId && (!$user || $sharerId != $user->id)) {
                $sharer = \App\Models\User::find($sharerId);
                if ($sharer) {
                    $sharer->addPoints(5, 'earned', 'Product share bonus from successful order #' . $order->order_number, $order->id);
                }
            }
            session()->forget('referred_by');
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
        $user    = Auth::user();
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
        if (\App\Models\Setting::get('sms_on_new_order_customer', '1') == '1') {
            $msg = "Dear {$name}, your order #{$order->order_number} has been placed successfully! "
                 . "Total: Tk {$order->final_amount}. We will notify you once it's shipped. "
                 . "Thank you for shopping with " . \App\Models\Setting::get('store_name', 'ClothStore') . "!";

            app(SmsService::class)->send($phone, $msg);
        }

        if (\App\Models\Setting::get('sms_on_new_order_admin', '0') == '1') {
            $adminPhone = \App\Models\Setting::get('admin_phone_number');
            if (!empty($adminPhone)) {
                $msgAdmin = "New Order #{$order->order_number} received from {$name} for Tk {$order->final_amount}.";
                app(SmsService::class)->send($adminPhone, $msgAdmin);
            }
        }
    }

    private function sendAdminEmail(Order $order): void
    {
        if (\App\Models\Setting::get('email_on_new_order_admin', '0') != '1') return;

        $adminEmail = \App\Models\Setting::get('admin_notification_email', config('services.admin.email'));
        if (! $adminEmail) return;

        try {
            $order->load('items.product');
            Mail::to($adminEmail)->send(new NewOrderMail($order));
        } catch (\Throwable $e) {
            Log::error('[NewOrderMail] Failed: ' . $e->getMessage());
        }
    }
}
