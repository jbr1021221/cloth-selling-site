<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Coupon;
use Illuminate\Http\Request;

class CouponController extends Controller
{
    /**
     * Validate a coupon code against the current cart subtotal.
     * Called via fetch() from Alpine.js on the checkout page.
     *
     * POST /coupon/apply
     * Body: { code, subtotal }
     * Returns JSON: { valid, discount, message, type, value }
     */
    public function apply(Request $request)
    {
        $request->validate([
            'code'     => 'required|string|max:50',
            'subtotal' => 'required|numeric|min:0',
        ]);

        $coupon = Coupon::where('code', strtoupper(trim($request->code)))->first();

        if (! $coupon) {
            return response()->json([
                'valid'    => false,
                'message'  => 'Coupon code not found.',
                'discount' => 0,
            ]);
        }

        if (! $coupon->isValid()) {
            $reason = match(true) {
                ! $coupon->is_active            => 'This coupon is no longer active.',
                $coupon->expires_at?->isPast()  => 'This coupon has expired.',
                default                          => 'This coupon has reached its usage limit.',
            };
            return response()->json([
                'valid'    => false,
                'message'  => $reason,
                'discount' => 0,
            ]);
        }

        if ($request->subtotal < $coupon->min_order) {
            return response()->json([
                'valid'    => false,
                'message'  => "Minimum order of ৳" . number_format($coupon->min_order) . " required for this coupon.",
                'discount' => 0,
            ]);
        }

        $discount = $coupon->calculateDiscount((float) $request->subtotal);

        $label = $coupon->type === 'fixed'
            ? "৳" . number_format($coupon->value) . " off"
            : $coupon->value . "% off" . ($coupon->max_discount ? " (max ৳" . number_format($coupon->max_discount) . ")" : "");

        return response()->json([
            'valid'    => true,
            'discount' => $discount,
            'message'  => "🎉 Coupon applied! {$label}",
            'code'     => $coupon->code,
            'type'     => $coupon->type,
            'value'    => $coupon->value,
        ]);
    }
}
