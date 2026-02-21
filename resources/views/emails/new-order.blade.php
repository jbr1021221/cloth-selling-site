<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>New Order — ClothStore</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
            background-color: #0f0f1a;
            color: #e2e8f0;
            padding: 32px 16px;
        }
        .wrapper {
            max-width: 620px;
            margin: 0 auto;
        }
        /* Header */
        .header {
            background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%);
            border-radius: 16px 16px 0 0;
            padding: 32px 36px;
            text-align: center;
        }
        .header .logo {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 16px;
        }
        .logo-icon {
            width: 36px; height: 36px;
            background: rgba(255,255,255,0.2);
            border-radius: 10px;
            display: inline-flex; align-items: center; justify-content: center;
            font-size: 18px; font-weight: 800; color: #fff;
        }
        .logo-text { font-size: 22px; font-weight: 800; color: #fff; }
        .header h1 { font-size: 20px; color: #c7d2fe; font-weight: 500; }
        .header .order-num {
            display: inline-block;
            margin-top: 8px;
            background: rgba(255,255,255,0.15);
            color: #fff;
            padding: 6px 18px;
            border-radius: 50px;
            font-size: 15px;
            font-weight: 700;
            letter-spacing: 0.5px;
        }
        /* Body */
        .body {
            background: #131325;
            padding: 32px 36px;
        }
        .alert-banner {
            background: #4f46e5;
            border-radius: 12px;
            padding: 16px 20px;
            display: flex;
            align-items: center;
            gap: 12px;
            margin-bottom: 28px;
        }
        .alert-banner .icon { font-size: 24px; }
        .alert-banner p { color: #e0e7ff; font-size: 14px; line-height: 1.5; }
        .alert-banner strong { color: #fff; }
        /* Section heading */
        .section-title {
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: #6366f1;
            font-weight: 700;
            margin-bottom: 12px;
            padding-bottom: 8px;
            border-bottom: 1px solid #1e1e3a;
        }
        /* Info grid */
        .info-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 12px;
            margin-bottom: 28px;
        }
        .info-card {
            background: #1a1a2e;
            border: 1px solid #2d2d4e;
            border-radius: 12px;
            padding: 14px 16px;
        }
        .info-card .label { font-size: 11px; color: #6b7280; text-transform: uppercase; letter-spacing: 0.5px; }
        .info-card .value { font-size: 15px; color: #e2e8f0; font-weight: 600; margin-top: 3px; }
        .info-card .value.accent { color: #818cf8; }
        .info-card .value.success { color: #34d399; }
        /* Items table */
        .items-table-wrap { margin-bottom: 28px; }
        table { width: 100%; border-collapse: collapse; }
        thead th {
            background: #1a1a2e;
            color: #6b7280;
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            padding: 10px 14px;
            text-align: left;
            font-weight: 600;
        }
        thead th:last-child { text-align: right; }
        tbody tr { border-bottom: 1px solid #1e1e3a; }
        tbody td { padding: 12px 14px; font-size: 14px; color: #cbd5e1; vertical-align: top; }
        tbody td:last-child { text-align: right; color: #e2e8f0; font-weight: 600; }
        .item-meta { font-size: 12px; color: #4b5563; margin-top: 2px; }
        /* Totals */
        .totals { margin-bottom: 28px; }
        .total-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 8px 0;
            font-size: 14px;
            color: #9ca3af;
            border-bottom: 1px solid #1e1e3a;
        }
        .total-row.grand {
            color: #e2e8f0;
            font-size: 18px;
            font-weight: 700;
            border-bottom: none;
            padding-top: 12px;
        }
        .total-row.grand span:last-child { color: #818cf8; }
        /* Delivery address */
        .address-box {
            background: #1a1a2e;
            border: 1px solid #2d2d4e;
            border-radius: 12px;
            padding: 16px 20px;
            margin-bottom: 28px;
            font-size: 14px;
            line-height: 1.8;
            color: #cbd5e1;
        }
        /* CTA */
        .cta-wrap { text-align: center; margin-bottom: 28px; }
        .cta-btn {
            display: inline-block;
            background: linear-gradient(135deg, #4f46e5, #7c3aed);
            color: #fff !important;
            text-decoration: none;
            padding: 14px 36px;
            border-radius: 12px;
            font-weight: 700;
            font-size: 15px;
        }
        /* Footer */
        .footer {
            background: #0d0d1f;
            border-radius: 0 0 16px 16px;
            padding: 20px 36px;
            text-align: center;
            font-size: 12px;
            color: #4b5563;
        }
        .footer a { color: #6366f1; text-decoration: none; }
    </style>
</head>
<body>
<div class="wrapper">

    {{-- ── Header ── --}}
    <div class="header">
        <div class="logo">
            <span class="logo-icon">C</span>
            <span class="logo-text">ClothStore</span>
        </div>
        <h1>🛒 New Order Received!</h1>
        <div class="order-num">{{ $order->order_number }}</div>
    </div>

    {{-- ── Body ── --}}
    <div class="body">

        {{-- Alert --}}
        <div class="alert-banner">
            <span class="icon">⚡</span>
            <p>A new order has just been placed on <strong>ClothStore</strong>.
               Please review the details below and start processing.</p>
        </div>

        {{-- Key Stats --}}
        <p class="section-title">Order Overview</p>
        <div class="info-grid">
            <div class="info-card">
                <div class="label">Order Number</div>
                <div class="value accent">{{ $order->order_number }}</div>
            </div>
            <div class="info-card">
                <div class="label">Placed At</div>
                <div class="value">{{ $order->created_at->format('d M Y, h:i A') }}</div>
            </div>
            <div class="info-card">
                <div class="label">Payment Method</div>
                <div class="value">{{ strtoupper($order->payment_method) }}</div>
            </div>
            <div class="info-card">
                <div class="label">Order Total</div>
                <div class="value success">৳{{ number_format($order->final_amount) }}</div>
            </div>
        </div>

        {{-- Order Items --}}
        <p class="section-title">Items Ordered</p>
        <div class="items-table-wrap">
            <table>
                <thead>
                    <tr>
                        <th>Product</th>
                        <th style="text-align:center">Qty</th>
                        <th>Subtotal</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($order->items as $item)
                    <tr>
                        <td>
                            {{ $item->product->name ?? 'Product #'.$item->product_id }}
                            @if($item->size || $item->color)
                                <div class="item-meta">
                                    {{ $item->size ? 'Size: '.$item->size : '' }}
                                    {{ $item->size && $item->color ? ' · ' : '' }}
                                    {{ $item->color ? 'Colour: '.$item->color : '' }}
                                </div>
                            @endif
                        </td>
                        <td style="text-align:center">{{ $item->quantity }}</td>
                        <td>৳{{ number_format($item->price * $item->quantity) }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        {{-- Totals --}}
        <div class="totals">
            <div class="total-row">
                <span>Subtotal</span>
                <span>৳{{ number_format($order->total_amount) }}</span>
            </div>
            <div class="total-row">
                <span>Shipping</span>
                <span>{{ $order->shipping_charge == 0 ? 'Free' : '৳'.number_format($order->shipping_charge) }}</span>
            </div>
            @if($order->discount > 0)
            <div class="total-row">
                <span>Discount</span>
                <span style="color:#f87171">−৳{{ number_format($order->discount) }}</span>
            </div>
            @endif
            <div class="total-row grand">
                <span>Grand Total</span>
                <span>৳{{ number_format($order->final_amount) }}</span>
            </div>
        </div>

        {{-- Delivery Address --}}
        <p class="section-title">Shipping Address</p>
        @php $addr = $order->delivery_address ?? []; @endphp
        <div class="address-box">
            📦 <strong>{{ $addr['name'] ?? '—' }}</strong><br>
            📞 {{ $addr['phone'] ?? '—' }}<br>
            📧 {{ $addr['email'] ?? '—' }}<br>
            📍 {{ $addr['address'] ?? '—' }},
               {{ $addr['city'] ?? '' }}
               {{ $addr['district'] ? ', '.$addr['district'] : '' }}
               {{ $addr['postal_code'] ? ' - '.$addr['postal_code'] : '' }},
               {{ $addr['country'] ?? 'Bangladesh' }}
            @if($order->notes)
                <br>📝 Note: {{ $order->notes }}
            @endif
        </div>

        {{-- CTA --}}
        <div class="cta-wrap">
            <a href="{{ url('/admin/orders/'.$order->id) }}" class="cta-btn">
                View &amp; Manage Order →
            </a>
        </div>

    </div>

    {{-- ── Footer ── --}}
    <div class="footer">
        <p>This is an automated notification from <strong>ClothStore</strong> admin system.</p>
        <p style="margin-top:6px;">© {{ date('Y') }} ClothStore Bangladesh. All rights reserved.</p>
    </div>

</div>
</body>
</html>
