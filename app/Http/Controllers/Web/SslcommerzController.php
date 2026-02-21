<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Mail\NewOrderMail;
use App\Models\Order;
use App\Services\SmsService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Raziul\Sslcommerz\Facades\Sslcommerz;

/**
 * Handles SSLCommerz payment gateway callbacks.
 *
 * Routes (in web.php):
 *   POST /sslcommerz/success  → success()
 *   POST /sslcommerz/failure  → failure()
 *   POST /sslcommerz/cancel   → cancel()
 *   POST /sslcommerz/ipn      → ipn()
 */
class SslcommerzController extends Controller
{
    /**
     * Payment success callback from SSLCommerz.
     * Validates the transaction and marks the order as paid.
     */
    public function success(Request $request)
    {
        $order = $this->findOrder($request->value_a);

        if (! $order) {
            return redirect()->route('home')->with('error', 'Order not found.');
        }

        // Validate the payment with SSLCommerz
        $isValid = Sslcommerz::validatePayment(
            $request->all(),
            $request->tran_id,
            $order->final_amount
        );

        if ($isValid) {
            $order->update([
                'payment_status' => 'paid',
                'status'         => 'processing',
                'transaction_id' => $request->bank_tran_id ?? $request->tran_id,
            ]);

            // Send confirmation SMS to customer
            $this->sendOrderConfirmationSms($order);

            // Send admin email notification
            $this->sendAdminEmailNotification($order);

            return redirect()
                ->route('checkout.success', $order)
                ->with('success', 'Payment successful! Your order has been placed.');
        }

        // Validation failed — mark as failed and show error
        $order->update(['payment_status' => 'failed', 'status' => 'cancelled']);

        Log::warning('[SSLCommerz] Payment validation failed.', [
            'order_id' => $order->id,
            'tran_id'  => $request->tran_id,
        ]);

        return redirect()
            ->route('checkout.index')
            ->with('error', 'Payment validation failed. Please try again or contact support.');
    }

    /**
     * Payment failure callback from SSLCommerz.
     */
    public function failure(Request $request)
    {
        $order = $this->findOrder($request->value_a);

        if ($order) {
            $order->update(['payment_status' => 'failed', 'status' => 'cancelled']);
        }

        Log::warning('[SSLCommerz] Payment failed.', ['order_id' => $order?->id]);

        return redirect()
            ->route('checkout.index')
            ->with('error', 'Payment failed. Please try again or choose Cash on Delivery.');
    }

    /**
     * Payment cancel callback from SSLCommerz.
     */
    public function cancel(Request $request)
    {
        $order = $this->findOrder($request->value_a);

        if ($order) {
            $order->update(['payment_status' => 'failed', 'status' => 'cancelled']);
        }

        return redirect()
            ->route('checkout.index')
            ->with('error', 'Payment was cancelled. You can try again or choose Cash on Delivery.');
    }

    /**
     * IPN (Instant Payment Notification) callback.
     * SSLCommerz sends this independently to confirm payment.
     */
    public function ipn(Request $request)
    {
        $order = $this->findOrder($request->value_a);

        if (! $order) {
            Log::error('[SSLCommerz IPN] Order not found.', ['value_a' => $request->value_a]);
            return response()->json(['message' => 'Order not found'], 404);
        }

        $isValid = Sslcommerz::validatePayment(
            $request->all(),
            $request->tran_id,
            $order->final_amount
        );

        if ($isValid && $order->payment_status !== 'paid') {
            $order->update([
                'payment_status' => 'paid',
                'status'         => 'processing',
                'transaction_id' => $request->bank_tran_id ?? $request->tran_id,
            ]);

            Log::info('[SSLCommerz IPN] Order paid via IPN.', ['order_id' => $order->id]);
        }

        return response()->json(['message' => 'IPN processed']);
    }

    // ──────────────────────────────────────────────────────────────────────────
    // Helpers
    // ──────────────────────────────────────────────────────────────────────────

    private function findOrder(?string $orderId): ?Order
    {
        if (! $orderId) return null;
        return Order::find($orderId);
    }

    private function sendOrderConfirmationSms(Order $order): void
    {
        $address  = $order->delivery_address ?? [];
        $phone    = $address['phone'] ?? null;
        $name     = $address['name']  ?? 'Customer';

        if ($phone) {
            $msg = "Dear {$name}, your payment for order #{$order->order_number} was successful! "
                 . "Total: Tk {$order->final_amount}. We will notify you once shipped. "
                 . "Thank you for shopping with ClothStore! - ClothStore BD";

            app(SmsService::class)->send($phone, $msg);
        }
    }

    private function sendAdminEmailNotification(Order $order): void
    {
        $adminEmail = config('services.admin.email');
        if (! $adminEmail) return;

        try {
            $order->load('items.product');
            Mail::to($adminEmail)->send(new NewOrderMail($order));
        } catch (\Throwable $e) {
            Log::error('[SSLCommerz] Admin email failed: ' . $e->getMessage());
        }
    }
}
