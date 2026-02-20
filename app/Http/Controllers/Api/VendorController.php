<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class VendorController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return \App\Models\Vendor::all();
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string',
            'contact_person' => 'required|string',
            'phone' => 'required|string',
            'email' => 'nullable|email',
            'address' => 'required|string',
            'categories' => 'nullable|array',
            'commission_rate' => 'numeric',
            'is_active' => 'boolean'
        ]);

        return \App\Models\Vendor::create($validated);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        return \App\Models\Vendor::findOrFail($id);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $vendor = \App\Models\Vendor::findOrFail($id);
        
        $validated = $request->validate([
            'name' => 'string',
            'contact_person' => 'string',
            'phone' => 'string',
            'email' => 'nullable|email',
            'address' => 'string',
            'categories' => 'nullable|array',
            'commission_rate' => 'numeric',
            'is_active' => 'boolean'
        ]);
        
        $vendor->update($validated);
        return $vendor;
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        \App\Models\Vendor::destroy($id);
        return response()->noContent();
    }
}
