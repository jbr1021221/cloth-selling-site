<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Illuminate\Http\Request;
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
        $order->update(['status' => $request->status]);
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
        $data = $request->validate([
            'name'           => 'required|string|max:255',
            'description'    => 'required|string',
            'category'       => 'required|string',
            'price'          => 'required|numeric|min:0',
            'discount_price' => 'nullable|numeric|min:0',
            'stock'          => 'required|integer|min:0',
            'sku'            => 'nullable|string|max:100',
            'is_active'      => 'required|boolean',
            'sizes'          => 'nullable|string',
            'colors'         => 'nullable|string',
            'images'         => 'nullable|string',
        ]);

        $data['sizes']  = $this->parseCommaList($request->sizes);
        $data['colors'] = $this->parseCommaList($request->colors);
        $data['images'] = $this->parseCommaList($request->images);
        $data['sku']    = $data['sku'] ?: strtoupper(Str::random(8));

        Product::create($data);

        return redirect()->route('admin.products')->with('success', 'Product created successfully!');
    }

    public function productEdit(Product $product)
    {
        return view('admin.products.form', compact('product'));
    }

    public function productUpdate(Request $request, Product $product)
    {
        $data = $request->validate([
            'name'           => 'required|string|max:255',
            'description'    => 'required|string',
            'category'       => 'required|string',
            'price'          => 'required|numeric|min:0',
            'discount_price' => 'nullable|numeric|min:0',
            'stock'          => 'required|integer|min:0',
            'sku'            => 'nullable|string|max:100',
            'is_active'      => 'required|boolean',
            'sizes'          => 'nullable|string',
            'colors'         => 'nullable|string',
            'images'         => 'nullable|string',
        ]);

        $data['sizes']  = $this->parseCommaList($request->sizes);
        $data['colors'] = $this->parseCommaList($request->colors);
        $data['images'] = $this->parseCommaList($request->images);

        $product->update($data);

        return redirect()->route('admin.products')->with('success', 'Product updated successfully!');
    }

    public function productDestroy(Product $product)
    {
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
