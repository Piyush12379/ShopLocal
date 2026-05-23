<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8"/>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 13px;
            color: #0F1117;
            background: #fff;
            padding: 40px;
        }

        /* Header */
        .header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 40px;
            padding-bottom: 20px;
            border-bottom: 2px solid #0F1117;
        }
        .logo {
            font-size: 28px;
            font-weight: 700;
            letter-spacing: -0.5px;
        }
        .logo span { color: #D4A853; }
        .invoice-label {
            text-align: right;
        }
        .invoice-label h2 {
            font-size: 22px;
            font-weight: 700;
            color: #D4A853;
            margin-bottom: 4px;
        }
        .invoice-label p {
            font-size: 12px;
            color: #7A7060;
        }

        /* Info grid */
        .info-grid {
            display: flex;
            justify-content: space-between;
            margin-bottom: 32px;
            gap: 20px;
        }
        .info-box {
            flex: 1;
        }
        .info-box h4 {
            font-size: 10px;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            color: #7A7060;
            margin-bottom: 8px;
            font-weight: 600;
        }
        .info-box p {
            font-size: 13px;
            line-height: 1.7;
            color: #0F1117;
        }

        /* Status badge */
        .status-badge {
            display: inline-block;
            padding: 3px 10px;
            border-radius: 20px;
            font-size: 11px;
            font-weight: 600;
            background: #E1F5EE;
            color: #085041;
        }

        /* Items table */
        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 24px;
        }
        .items-table th {
            background: #0F1117;
            color: #F8F5EF;
            padding: 10px 14px;
            text-align: left;
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: 0.07em;
            font-weight: 600;
        }
        .items-table th:last-child,
        .items-table td:last-child {
            text-align: right;
        }
        .items-table td {
            padding: 11px 14px;
            border-bottom: 1px solid #EEE9DF;
            font-size: 13px;
            vertical-align: middle;
        }
        .items-table tr:nth-child(even) td {
            background: #FAFAF8;
        }

        /* Totals */
        .totals {
            width: 260px;
            margin-left: auto;
            margin-bottom: 32px;
        }
        .totals-row {
            display: flex;
            justify-content: space-between;
            padding: 7px 0;
            font-size: 13px;
            border-bottom: 1px solid #EEE9DF;
        }
        .totals-row:last-child {
            border: none;
            padding-top: 10px;
            font-weight: 700;
            font-size: 15px;
        }
        .totals-row .label { color: #7A7060; }

        /* Footer */
        .footer {
            margin-top: 40px;
            padding-top: 20px;
            border-top: 1px solid #EEE9DF;
            display: flex;
            justify-content: space-between;
            font-size: 11px;
            color: #7A7060;
        }
        .thank-you {
            text-align: center;
            margin: 24px 0;
            font-size: 14px;
            color: #7A7060;
        }
        .thank-you strong { color: #0F1117; }
    </style>
</head>
<body>

    <div class="header">
        <div>
            <div class="logo">Shop<span>Local</span></div>
            <div style="font-size:12px;color:#7A7060;margin-top:4px">
                hello@shoplocal.in | shoplocal.com
            </div>
        </div>
        <div class="invoice-label">
            <h2>INVOICE</h2>
            <p>Invoice #INV-{{ str_pad($order->id, 5, '0', STR_PAD_LEFT) }}</p>
            <p>Date: {{ $order->created_at->format('d M Y') }}</p>
        </div>
    </div>

    <div class="info-grid">
        <div class="info-box">
            <h4>Billed to</h4>
            <p>
                <strong>{{ $order->user->name }}</strong><br/>
                {{ $order->user->email }}<br/>
                {{ $order->address }}
            </p>
        </div>
        <div class="info-box">
            <h4>Order info</h4>
            <p>
                <strong>Order ID:</strong> #{{ $order->id }}<br/>
                <strong>Date:</strong> {{ $order->created_at->format('d M Y, h:i A') }}<br/>
                <strong>Payment:</strong> {{ $order->payment_method === 'cod' ? 'Cash on delivery' : strtoupper($order->payment_method) }}<br/>
                <strong>Status:</strong>
                <span class="status-badge">{{ ucfirst($order->status) }}</span>
            </p>
        </div>
        <div class="info-box">
            <h4>From</h4>
            <p>
                <strong>ShopLocal Platform</strong><br/>
                123 Artisan Lane<br/>
                Jaipur, Rajasthan — 302001<br/>
                India
            </p>
        </div>
    </div>

    <table class="items-table">
        <thead>
            <tr>
                <th>#</th>
                <th>Product</th>
                <th>Vendor</th>
                <th>Qty</th>
                <th>Unit price</th>
                <th>Subtotal</th>
            </tr>
        </thead>
        <tbody>
            @foreach($order->items as $index => $item)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td>
                    {{ $item->product->emoji ?? '📦' }}
                    {{ $item->product->name ?? 'Product' }}
                </td>
                <td>{{ $item->product->vendor->name ?? '—' }}</td>
                <td>{{ $item->quantity }}</td>
                {{-- CHANGED TO Rs. --}}
                <td>Rs. {{ number_format($item->unit_price) }}</td>
                <td>Rs. {{ number_format($item->unit_price * $item->quantity) }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="totals">
        @php
            $subtotal = $order->items->sum(fn($i) => $i->unit_price * $i->quantity);
            // Delivery is the grand total, plus the discount, minus the subtotal
            $delivery = $order->total_amount + $order->discount - $subtotal;
        @endphp
        
        <div class="totals-row">
            <span class="label">Subtotal</span>
            <span>Rs. {{ number_format($subtotal) }}</span>
        </div>

        @if($order->discount > 0)
        <div class="totals-row">
            <span class="label">Discount ({{ $order->coupon_code }})</span>
            <span style="color: #1D9E75; font-weight: 600;">-Rs. {{ number_format($order->discount) }}</span>
        </div>
        @endif

        <div class="totals-row">
            <span class="label">Delivery</span>
            <span>{{ $delivery <= 0 ? 'Free' : 'Rs. ' . number_format($delivery) }}</span>
        </div>
        
        <div class="totals-row">
            <span class="label">Grand Total</span>
            <span>Rs. {{ number_format($order->total_amount) }}</span>
        </div>
    </div>

    <div class="thank-you">
        <strong>Thank you for shopping with ShopLocal!</strong><br/>
        Every purchase supports a local artisan. ❤️
    </div>

    <div class="footer">
        <span>ShopLocal © {{ date('Y') }}</span>
        <span>This is a computer-generated invoice and does not require a signature.</span>
        <span>hello@shoplocal.in</span>
    </div>

</body>
</html>