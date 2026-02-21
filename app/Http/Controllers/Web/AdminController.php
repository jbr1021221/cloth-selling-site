<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use App\Services\SmsService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class AdminController extends Controller
{

    // ─── Dashboard ────────────────────────────────────────────────────────────

    public function dashboard()
    {
        $totalOrders    = Order::count();
        $totalRevenue   = Order::where('payment_status', 'paid')->sum('final_amount');
        $totalProducts  = Product::count();
        $totalCustomers = User::where('role', 'customer')->count();
        $recentOrders   = Order::with('user')->latest()->take(10)->get();
        $topProducts    = Product::latest()->take(5)->get();

        // Loyalty Stats
        $totalPointsIssued   = \App\Models\LoyaltyPoint::where('type', 'earned')->sum('points');
        $totalPointsRedeemed = \App\Models\LoyaltyPoint::where('type', 'redeemed')->sum('points');

        return view('admin.dashboard', compact(
            'totalOrders', 'totalRevenue', 'totalProducts',
            'totalCustomers', 'recentOrders', 'topProducts',
            'totalPointsIssued', 'totalPointsRedeemed'
        ));
    }

    // ─── Orders ───────────────────────────────────────────────────────────────

    public function orders(Request $request)
    {
        $query = Order::with('user')->latest();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('order_number', 'LIKE', "%{$search}%")
                  ->orWhere('delivery_address->name', 'LIKE', "%{$search}%")
                  ->orWhere('delivery_address->phone', 'LIKE', "%{$search}%");
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('payment_method')) {
            $query->where('payment_method', $request->payment_method);
        }

        if ($request->filled('district')) {
            $query->where('delivery_address->district', 'LIKE', "%{$request->district}%");
        }

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        if ($request->filled('min_amount')) {
            $query->where('final_amount', '>=', $request->min_amount);
        }

        if ($request->filled('max_amount')) {
            $query->where('final_amount', '<=', $request->max_amount);
        }

        $orders = $query->paginate(20);

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'html' => view('admin.orders.partials.table', compact('orders'))->render(),
            ]);
        }

        return view('admin.orders.index', compact('orders'));
    }

    public function orderShow(Order $order)
    {
        $order->load(['items.product', 'user', 'statusHistories.user']);
        
        $customerOrderCount = 0;
        if ($order->user_id) {
            $customerOrderCount = Order::where('user_id', $order->user_id)->where('id', '!=', $order->id)->count();
        } elseif (!empty($order->delivery_address['phone'])) {
            $customerOrderCount = Order::where('delivery_address->phone', $order->delivery_address['phone'])->where('id', '!=', $order->id)->count();
        }

        return view('admin.orders.show', compact('order', 'customerOrderCount'));
    }

    public function orderUpdateStatus(Request $request, Order $order)
    {
        $request->validate(['status' => 'required|in:pending,processing,shipped,delivered,cancelled']);

        $previousStatus = $order->status;
        $order->update(['status' => $request->status]);

        if ($previousStatus !== $request->status) {
            $order->statusHistories()->create([
                'user_id' => \Illuminate\Support\Facades\Auth::id(),
                'status'  => $request->status,
            ]);
        }

        // ── Send "Shipped" SMS to customer ───────────────────────────────────
        if ($request->status === 'shipped' && $previousStatus !== 'shipped') {
            $address       = $order->delivery_address ?? [];
            $customerPhone = $address['phone'] ?? null;
            $customerName  = $address['name']  ?? 'Customer';

            if ($customerPhone) {
                $smsMessage = "Dear {$customerName}, great news! Your order #{$order->order_number} "
                            . "has been shipped and is on its way to you. "
                            . "Expected delivery: Dhaka 1-2 days | Outside Dhaka 3-5 days. "
                            . "Thank you for shopping with ClothStore! - ClothStore BD";

                app(SmsService::class)->send($customerPhone, $smsMessage);
            }
        }
        // ─────────────────────────────────────────────────────────────────────

        // ── Tier Calculation ─────────────────────────────────────────────────
        if ($order->user) {
            if ($request->status === 'delivered' && $previousStatus !== 'delivered') {
                $order->user->increment('total_spent', $order->final_amount);
                $order->user->updateTier();
            } elseif ($previousStatus === 'delivered' && $request->status !== 'delivered') {
                $order->user->decrement('total_spent', $order->final_amount);
                $order->user->updateTier();
            }
        }
        // ─────────────────────────────────────────────────────────────────────

        return back()->with('success', 'Order status updated.');
    }

    public function orderUpdateNotes(Request $request, Order $order)
    {
        $request->validate(['admin_notes' => 'nullable|string']);
        $order->update(['admin_notes' => $request->admin_notes]);
        return back()->with('success', 'Admin notes updated.');
    }

    public function orderBulkStatus(Request $request)
    {
        $request->validate([
            'order_ids' => 'required|array',
            'order_ids.*' => 'exists:orders,id',
            'status' => 'required|in:pending,processing,shipped,delivered,cancelled'
        ]);

        $orders = Order::whereIn('id', $request->order_ids)->get();
        foreach ($orders as $order) {
            $previousStatus = $order->status;
            $order->update(['status' => $request->status]);
            
            if ($previousStatus !== $request->status) {
                $order->statusHistories()->create([
                    'user_id' => \Illuminate\Support\Facades\Auth::id(),
                    'status'  => $request->status,
                ]);
            }

            // Could also add tier/sms calculation here if needed...
        }

        return back()->with('success', 'Selected orders updated successfully.');
    }

    public function orderInvoice(Order $order)
    {
        $order->load(['items.product', 'user']);
        return view('admin.orders.invoice', compact('order'));
    }

    public function orderExport(Request $request)
    {
        $query = Order::with('items')->latest();

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $orders = $query->get();

        $filename = "orders_export_" . date('Y-m-d_H-i') . ".csv";
        $headers = [
            "Content-type"        => "text/csv",
            "Content-Disposition" => "attachment; filename=$filename",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        ];

        $columns = ['Order ID', 'Date', 'Customer', 'Phone', 'Address', 'District', 'Items (Qty)', 'Status', 'Payment Method', 'Payment Status', 'Total (BDT)'];

        $callback = function () use ($orders, $columns) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);

            foreach ($orders as $order) {
                $address = $order->delivery_address ?? [];
                $itemsStr = $order->items->map(function ($item) {
                    return "{$item->name} (x{$item->quantity})";
                })->implode('; ');

                fputcsv($file, [
                    $order->order_number,
                    $order->created_at->format('Y-m-d H:i:s'),
                    $address['name'] ?? 'N/A',
                    $address['phone'] ?? 'N/A',
                    $address['address'] ?? 'N/A',
                    $address['district'] ?? 'N/A',
                    $itemsStr,
                    ucfirst($order->status),
                    strtoupper($order->payment_method),
                    ucfirst($order->payment_status),
                    $order->final_amount
                ]);
            }
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    // ─── Products ─────────────────────────────────────────────────────────────

    public function products(Request $request)
    {
        $query = Product::latest();

        if ($request->filled('search')) {
            $query->where('name', 'LIKE', '%' . $request->search . '%');
        }

        $products = $query->paginate(20);

        return view('admin.products.index', compact('products'));
    }

    public function productCreate()
    {
        return view('admin.products.form');
    }

    public function productStore(Request $request)
    {
        $request->validate([
            'name'           => 'required|string|max:255',
            'description'    => 'required|string',
            'category'       => 'required|string',
            'price'          => 'required|numeric|min:0',
            'discount_price' => 'nullable|numeric|min:0',
            'stock'          => 'required|integer|min:0',
            'sku'            => 'nullable|string|max:100',
            'is_active'      => 'required|in:0,1',
            'sizes'          => 'nullable|string',
            'colors'         => 'nullable|string',
            'images'         => 'nullable|array',
            'images.*'       => 'image|mimes:jpeg,png,jpg,webp,gif|max:4096',
        ]);

        // Upload images
        $imagePaths = [];
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $file) {
                $path = $file->store('products', 'public');
                $imagePaths[] = Storage::url($path);  // /storage/products/filename.jpg
            }
        }

        $product = Product::create([
            'name'           => $request->name,
            'description'    => $request->description,
            'category'       => $request->category,
            'price'          => $request->price,
            'discount_price' => $request->discount_price,
            'stock'          => $request->stock,
            'sku'            => $request->sku ?: strtoupper(Str::random(8)),
            'is_active'      => (bool) $request->is_active,
            'sizes'          => $request->variants ? array_values(array_unique(array_filter(array_column($request->variants, 'size')))) : [],
            'colors'         => $request->variants ? array_values(array_unique(array_filter(array_column($request->variants, 'color')))) : [],
            'images'         => $imagePaths,
        ]);

        if ($request->has('variants')) {
            foreach ($request->variants as $variant) {
                if (isset($variant['size']) || isset($variant['color'])) {
                    $product->variants()->create([
                        'size' => $variant['size'] ?? null,
                        'color' => $variant['color'] ?? null,
                        'stock' => $variant['stock'] ?? 0,
                        'sku' => $variant['sku'] ?? null,
                        'price_modifier' => $variant['price_modifier'] ?? 0,
                    ]);
                }
            }
        }

        return redirect()->route('admin.products')->with('success', 'Product created successfully!');
    }

    public function productEdit(Product $product)
    {
        return view('admin.products.form', compact('product'));
    }

    public function productUpdate(Request $request, Product $product)
    {
        $request->validate([
            'name'           => 'required|string|max:255',
            'description'    => 'required|string',
            'category'       => 'required|string',
            'price'          => 'required|numeric|min:0',
            'discount_price' => 'nullable|numeric|min:0',
            'stock'          => 'required|integer|min:0',
            'sku'            => 'nullable|string|max:100',
            'is_active'      => 'required|in:0,1',
            'sizes'          => 'nullable|string',
            'colors'         => 'nullable|string',
            'images'         => 'nullable|array',
            'images.*'       => 'image|mimes:jpeg,png,jpg,webp,gif|max:4096',
            'delete_images'  => 'nullable|array',  // array of image URLs to remove
            'delete_images.*'=> 'nullable|string',
        ]);

        // Start with existing images, remove any flagged for deletion
        $existingImages = is_array($product->images) ? $product->images : [];

        if ($request->filled('delete_images')) {
            foreach ($request->delete_images as $urlToDelete) {
                // Convert public URL back to storage path and delete file
                $storagePath = str_replace('/storage/', 'public/', $urlToDelete);
                Storage::delete($storagePath);
                $existingImages = array_filter($existingImages, fn($img) => $img !== $urlToDelete);
            }
        }

        // Upload new images and append
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $file) {
                $path = $file->store('products', 'public');
                $existingImages[] = Storage::url($path);
            }
        }

        $product->update([
            'name'           => $request->name,
            'description'    => $request->description,
            'category'       => $request->category,
            'price'          => $request->price,
            'discount_price' => $request->discount_price,
            'stock'          => $request->stock,
            'sku'            => $request->sku,
            'is_active'      => (bool) $request->is_active,
            'sizes'          => $request->variants ? array_values(array_unique(array_filter(array_column($request->variants, 'size')))) : [],
            'colors'         => $request->variants ? array_values(array_unique(array_filter(array_column($request->variants, 'color')))) : [],
            'images'         => array_values($existingImages),
        ]);

        if ($request->has('variants')) {
            $product->variants()->delete();
            foreach ($request->variants as $variant) {
                if (isset($variant['size']) || isset($variant['color'])) {
                    $product->variants()->create([
                        'size' => $variant['size'] ?? null,
                        'color' => $variant['color'] ?? null,
                        'stock' => $variant['stock'] ?? 0,
                        'sku' => $variant['sku'] ?? null,
                        'price_modifier' => $variant['price_modifier'] ?? 0,
                    ]);
                }
            }
        }

        return redirect()->route('admin.products')->with('success', 'Product updated successfully!');
    }

    public function productDestroy(Product $product)
    {
        // Delete all uploaded images from storage
        if (is_array($product->images)) {
            foreach ($product->images as $url) {
                $storagePath = str_replace('/storage/', 'public/', $url);
                Storage::delete($storagePath);
            }
        }

        $product->delete();
        return back()->with('success', 'Product deleted.');
    }

    // ─── Users ────────────────────────────────────────────────────────────────

    public function users(Request $request)
    {
        $query = User::withCount('orders')->latest();

        if ($request->filled('search')) {
            $query->where(function($q) use ($request) {
                $q->where('name', 'LIKE', '%' . $request->search . '%')
                  ->orWhere('email', 'LIKE', '%' . $request->search . '%');
            });
        }

        if ($request->filled('tier')) {
            $query->where('tier', $request->tier);
        }

        $users = $query->paginate(20);

        return view('admin.users.index', compact('users'));
    }

    // ─── Vendors ──────────────────────────────────────────────────────────────

    public function vendors()
    {
        $vendors = \App\Models\Vendor::latest()->paginate(20);
        return view('admin.vendors.index', compact('vendors'));
    }

    // ─── Helper ───────────────────────────────────────────────────────────────

    private function parseCommaList(?string $value): array
    {
        if (!$value) return [];
        return array_map('trim', explode(',', $value));
    }
}
