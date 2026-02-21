<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Coupon;
use Illuminate\Http\Request;

class AdminCouponController extends Controller
{
    public function index()
    {
        $coupons = Coupon::latest()->paginate(20);
        return view('admin.coupons.index', compact('coupons'));
    }

    public function create()
    {
        return view('admin.coupons.form');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'code'         => 'required|string|max:50|unique:coupons,code',
            'type'         => 'required|in:fixed,percentage',
            'value'        => 'required|numeric|min:0.01',
            'max_discount' => 'nullable|numeric|min:0',
            'min_order'    => 'nullable|numeric|min:0',
            'max_uses'     => 'nullable|integer|min:1',
            'expires_at'   => 'nullable|date|after:now',
            'is_active'    => 'nullable|boolean',
        ]);

        $data['code']      = strtoupper(trim($data['code']));
        $data['is_active'] = $request->boolean('is_active');

        Coupon::create($data);

        return redirect()->route('admin.coupons.index')
                         ->with('success', "Coupon {$data['code']} created successfully.");
    }

    public function edit(Coupon $coupon)
    {
        return view('admin.coupons.form', compact('coupon'));
    }

    public function update(Request $request, Coupon $coupon)
    {
        $data = $request->validate([
            'code'         => 'required|string|max:50|unique:coupons,code,' . $coupon->id,
            'type'         => 'required|in:fixed,percentage',
            'value'        => 'required|numeric|min:0.01',
            'max_discount' => 'nullable|numeric|min:0',
            'min_order'    => 'nullable|numeric|min:0',
            'max_uses'     => 'nullable|integer|min:1',
            'expires_at'   => 'nullable|date',
            'is_active'    => 'nullable|boolean',
        ]);

        $data['code']      = strtoupper(trim($data['code']));
        $data['is_active'] = $request->boolean('is_active');

        $coupon->update($data);

        return redirect()->route('admin.coupons.index')
                         ->with('success', "Coupon {$coupon->code} updated.");
    }

    public function destroy(Coupon $coupon)
    {
        $code = $coupon->code;
        $coupon->delete();

        return redirect()->route('admin.coupons.index')
                         ->with('success', "Coupon {$code} deleted.");
    }
}
