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

        return view('admin.dashboard', compact(
            'totalOrders', 'totalRevenue', 'totalProducts',
            'totalCustomers', 'recentOrders', 'topProducts'
        ));
    }

    // ─── Orders ───────────────────────────────────────────────────────────────

    public function orders(Request $request)
    {
        $query = Order::with('user')->latest();

        if ($request->filled('search')) {
            $query->where('order_number', 'LIKE', '%' . $request->search . '%');
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $orders = $query->paginate(20);

        return view('admin.orders.index', compact('orders'));
    }

    public function orderShow(Order $order)
    {
        $order->load('items.product', 'user');
        return view('orders.show', compact('order'));
    }

    public function orderUpdateStatus(Request $request, Order $order)
    {
        $request->validate(['status' => 'required|in:pending,processing,shipped,delivered,cancelled']);

        $previousStatus = $order->status;
        $order->update(['status' => $request->status]);

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

        return back()->with('success', 'Order status updated.');
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

        Product::create([
            'name'           => $request->name,
            'description'    => $request->description,
            'category'       => $request->category,
            'price'          => $request->price,
            'discount_price' => $request->discount_price,
            'stock'          => $request->stock,
            'sku'            => $request->sku ?: strtoupper(Str::random(8)),
            'is_active'      => (bool) $request->is_active,
            'sizes'          => $this->parseCommaList($request->sizes),
            'colors'         => $this->parseCommaList($request->colors),
            'images'         => $imagePaths,
        ]);

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
            'sizes'          => $this->parseCommaList($request->sizes),
            'colors'         => $this->parseCommaList($request->colors),
            'images'         => array_values($existingImages),
        ]);

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
            $query->where('name', 'LIKE', '%' . $request->search . '%')
                  ->orWhere('email', 'LIKE', '%' . $request->search . '%');
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
