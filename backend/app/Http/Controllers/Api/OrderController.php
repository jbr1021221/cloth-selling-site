<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return \App\Models\Order::with(['items', 'user', 'vendor'])->get();
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // 1. Handle User (Guest or Existing)
        $userId = $request->user_id;
        
        if (!$userId) {
            $email = $request->input('email');
            if (!$email && is_array($request->delivery_address)) {
                 $email = $request->delivery_address['email'] ?? null;
            }

            if ($email) {
                $user = \App\Models\User::firstOrCreate(
                    ['email' => $email],
                    [
                        'name' => $request->input('name') ?? ($request->delivery_address['name'] ?? 'Guest'),
                        'password' => \Illuminate\Support\Facades\Hash::make(\Illuminate\Support\Str::random(12)),
                        'phone' => $request->input('phone') ?? ($request->delivery_address['phone'] ?? null),
                        'role' => 'customer',
                    ]
                );
                $userId = $user->id;
            } else {
                // Return error if no email provided for guest
                return response()->json(['message' => 'Email is required for checkout.'], 422);
            }
        }

        $request->merge(['user_id' => $userId]);

        // 2. Generate Order Number
        $orderNumber = 'ORD-' . strtoupper(uniqid());
        $request->merge(['order_number' => $orderNumber]);

        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'vendor_id' => 'nullable|exists:vendors,id',
            'order_number' => 'required|string|unique:orders',
            'total_amount' => 'required|numeric',
            'shipping_charge' => 'numeric',
            'discount' => 'numeric',
            'final_amount' => 'required|numeric',
            'payment_method' => 'required|string',
            'payment_status' => 'in:Pending,Paid,Failed',
            'delivery_address' => 'required|array',
            'status' => 'in:Pending,Processing,Shipped,Delivered,Cancelled',
            'notes' => 'nullable|string',
            'items' => 'required|array',
            'items.*.product_id' => 'nullable|exists:products,id',
            'items.*.name' => 'required|string',
            'items.*.price' => 'required|numeric',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.size' => 'nullable|string',
            'items.*.color' => 'nullable|string',
            'items.*.image' => 'nullable|string',
        ]);

        return \Illuminate\Support\Facades\DB::transaction(function () use ($validated, $request) {
            $orderData = collect($validated)->except('items')->toArray();
            $order = \App\Models\Order::create($orderData);

            foreach ($request->items as $item) {
                $order->items()->create($item);
            }

            return $order->load('items');
        });
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        return \App\Models\Order::with(['items', 'user', 'vendor'])->findOrFail($id);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        return \Illuminate\Support\Facades\DB::transaction(function () use ($request, $id) {
            $order = \App\Models\Order::findOrFail($id);
            
            // Only allow updating order status/details, usually items shouldn't be changed easily without complex logic
            // Assuming basic update for now
            $validated = $request->validate([
               'status' => 'in:Pending,Processing,Shipped,Delivered,Cancelled',
               'payment_status' => 'in:Pending,Paid,Failed',
               // Add other fields as necessary
            ]);

            $order->update($validated);
            return $order;
        });
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
         \App\Models\Order::destroy($id);
         return response()->noContent();
    }
}
