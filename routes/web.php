<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Web\HomeController;
use App\Http\Controllers\Web\ProductController;
use App\Http\Controllers\Web\CartController;
use App\Http\Controllers\Web\CheckoutController;
use App\Http\Controllers\Web\AuthController;
use App\Http\Controllers\Web\OrderController;
use App\Http\Controllers\Web\AdminController;
use App\Http\Controllers\Web\SslcommerzController;
use App\Http\Controllers\Web\WishlistController;
use App\Http\Controllers\Web\ReviewController;
use App\Http\Controllers\Web\CouponController;
use App\Http\Controllers\Web\AdminCouponController;
use App\Http\Controllers\Web\LoyaltyController;
use App\Http\Controllers\Web\AdminFlashSaleController;
use App\Http\Controllers\Web\FlashSaleController;
use App\Http\Controllers\Web\AdminDeliveryZoneController;
use App\Http\Controllers\Web\AdminAnalyticsController;
use App\Http\Controllers\Web\AdminInventoryReportController;
use App\Http\Controllers\Web\AdminSettingsController;

// ─── Public Routes ────────────────────────────────────────────────────────────

Route::get('/', [HomeController::class, 'index'])->name('home');

// Products
Route::get('/products', [ProductController::class, 'index'])->name('products.index');
Route::get('/products/{id}', [ProductController::class, 'show'])->name('products.show');

// Flash Sales
Route::get('/flash-sale', [FlashSaleController::class, 'index'])->name('flash-sale.index');

// Live search suggestions (public, returns JSON)
Route::get('/search/suggestions', [ProductController::class, 'searchSuggestions'])->name('products.search.suggestions');

// Coupon validation (public — guests can apply coupons too)
Route::post('/coupon/apply', [CouponController::class, 'apply'])->name('coupon.apply');

// Category shortcut (redirects to products with filter)
Route::get('/products/category/{category}', function ($category) {
    return redirect()->route('products.index', ['category' => $category]);
})->name('products.category');

// Cart
Route::get('/cart', [CartController::class, 'index'])->name('cart.index');
Route::post('/cart/add', [CartController::class, 'add'])->name('cart.add');
Route::post('/cart/update', [CartController::class, 'update'])->name('cart.update');
Route::post('/cart/remove', [CartController::class, 'remove'])->name('cart.remove');
Route::post('/cart/clear', [CartController::class, 'clear'])->name('cart.clear');

// Auth
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
Route::post('/register', [AuthController::class, 'register']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// ─── Auth-Required Routes ─────────────────────────────────────────────────────

Route::middleware('auth')->group(function () {
    // Checkout
    Route::get('/checkout', [CheckoutController::class, 'index'])->name('checkout.index');
    Route::post('/checkout', [CheckoutController::class, 'place'])->name('checkout.place');
    Route::get('/checkout/success/{order}', [CheckoutController::class, 'success'])->name('checkout.success');

    // SSLCommerz – initiate payment (auth required)
    Route::post('/checkout/sslcommerz/initiate', [CheckoutController::class, 'initiateSSLCommerz'])
        ->name('checkout.sslcommerz.initiate');

    // Orders (customer)
    Route::get('/orders', [OrderController::class, 'index'])->name('orders.index');
    Route::get('/orders/{order}', [OrderController::class, 'show'])->name('orders.show');

    // Wishlist
    Route::get('/wishlist', [WishlistController::class, 'index'])->name('wishlist.index');
    Route::post('/wishlist/{product}/toggle', [WishlistController::class, 'toggle'])->name('wishlist.toggle');

    // Reviews
    Route::post('/products/{product}/reviews', [ReviewController::class, 'store'])->name('reviews.store');
    Route::delete('/reviews/{review}', [ReviewController::class, 'destroy'])->name('reviews.destroy');

    // Loyalty / Rewards
    Route::get('/loyalty', [LoyaltyController::class, 'index'])->name('loyalty.index');
});

// ─── SSLCommerz Callbacks (no CSRF — SSLCommerz POSTs from their servers) ─────
// These are excluded from CSRF in bootstrap/app.php (see below)
Route::controller(SslcommerzController::class)
    ->prefix('sslcommerz')
    ->name('sslc.')
    ->group(function () {
        Route::post('success', 'success')->name('success');
        Route::post('failure', 'failure')->name('failure');
        Route::post('cancel',  'cancel')->name('cancel');
        Route::post('ipn',     'ipn')->name('ipn');
    });

// ─── Admin Routes ─────────────────────────────────────────────────────────────

Route::prefix('admin')->name('admin.')->middleware(['auth', 'admin'])->group(function () {
    Route::get('/', [AdminController::class, 'dashboard'])->name('dashboard');

    // Orders
    Route::get('/orders', [AdminController::class, 'orders'])->name('orders');
    Route::get('/orders/export', [AdminController::class, 'orderExport'])->name('orders.export');
    Route::post('/orders/bulk-status', [AdminController::class, 'orderBulkStatus'])->name('orders.bulkStatus');
    Route::get('/orders/{order}', [AdminController::class, 'orderShow'])->name('orders.show');
    Route::patch('/orders/{order}/status', [AdminController::class, 'orderUpdateStatus'])->name('orders.updateStatus');
    Route::patch('/orders/{order}/notes', [AdminController::class, 'orderUpdateNotes'])->name('orders.updateNotes');
    Route::get('/orders/{order}/invoice', [AdminController::class, 'orderInvoice'])->name('orders.invoice');

    // Products
    Route::get('/products', [AdminController::class, 'products'])->name('products');
    Route::get('/products/create', [AdminController::class, 'productCreate'])->name('products.create');
    Route::post('/products', [AdminController::class, 'productStore'])->name('products.store');
    Route::get('/products/{product}/edit', [AdminController::class, 'productEdit'])->name('products.edit');
    Route::put('/products/{product}', [AdminController::class, 'productUpdate'])->name('products.update');
    Route::delete('/products/{product}', [AdminController::class, 'productDestroy'])->name('products.destroy');

    // Users
    Route::get('/users', [AdminController::class, 'users'])->name('users');

    // Vendors
    Route::get('/vendors', [AdminController::class, 'vendors'])->name('vendors');

    // Coupons
    Route::get('/coupons', [AdminCouponController::class, 'index'])->name('coupons.index');
    Route::get('/coupons/create', [AdminCouponController::class, 'create'])->name('coupons.create');
    Route::post('/coupons', [AdminCouponController::class, 'store'])->name('coupons.store');
    Route::get('/coupons/{coupon}/edit', [AdminCouponController::class, 'edit'])->name('coupons.edit');
    Route::put('/coupons/{coupon}', [AdminCouponController::class, 'update'])->name('coupons.update');
    Route::delete('/coupons/{coupon}', [AdminCouponController::class, 'destroy'])->name('coupons.destroy');

    // Loyalty Management
    Route::get('/loyalty', [LoyaltyController::class, 'adminIndex'])->name('loyalty.index');
    Route::post('/loyalty/{user}/modify', [LoyaltyController::class, 'adminStore'])->name('loyalty.store');

    // Flash Sales
    Route::get('/flash-sales', [AdminFlashSaleController::class, 'index'])->name('flash-sales.index');
    Route::post('/flash-sales', [AdminFlashSaleController::class, 'store'])->name('flash-sales.store');
    Route::delete('/flash-sales/{flashSale}', [AdminFlashSaleController::class, 'destroy'])->name('flash-sales.destroy');

    // Delivery Zones
    Route::get('/delivery-zones', [AdminDeliveryZoneController::class, 'index'])->name('delivery-zones.index');
    Route::put('/delivery-zones/{zone}', [AdminDeliveryZoneController::class, 'update'])->name('delivery-zones.update');

    // Analytics
    Route::get('/analytics', [AdminAnalyticsController::class, 'index'])->name('analytics');

    // Reports
    Route::get('/reports/inventory', [AdminInventoryReportController::class, 'index'])->name('reports.inventory.index');
    Route::get('/reports/inventory/export', [AdminInventoryReportController::class, 'exportCsv'])->name('reports.inventory.export');
    Route::post('/reports/inventory/{product}', [AdminInventoryReportController::class, 'updateStock'])->name('reports.inventory.updateStock');

    // Settings
    Route::get('/settings', [AdminSettingsController::class, 'index'])->name('settings.index');
    Route::post('/settings/{group}', [AdminSettingsController::class, 'update'])->name('settings.update');
});
