<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\DeliveryZone;
use Illuminate\Http\Request;

class AdminDeliveryZoneController extends Controller
{
    public function index()
    {
        $zones = DeliveryZone::orderBy('district_name', 'asc')->get();
        return view('admin.delivery-zones.index', compact('zones'));
    }

    public function update(Request $request, DeliveryZone $zone)
    {
        $request->validate([
            'delivery_charge' => 'required|numeric|min:0',
            'estimated_days'  => 'required|string|max:255',
            'is_active'       => 'boolean',
        ]);

        $zone->update([
            'delivery_charge' => $request->delivery_charge,
            'estimated_days'  => $request->estimated_days,
            'is_active'       => $request->has('is_active'),
        ]);

        return back()->with('success', 'Delivery zone updated successfully!');
    }
}
