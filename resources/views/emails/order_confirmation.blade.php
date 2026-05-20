<h2>🎉 Order Confirmed</h2>

<p>Hi {{ $order->user->name }},</p>

<p>Your order <b>#{{ $order->id }}</b> has been successfully placed.</p>

<hr>

<h3>🧾 Order Summary</h3>

<ul>
    <li><b>Order ID:</b> #{{ $order->id }}</li>
    <li><b>Payment Method:</b> {{ strtoupper($order->payment_method) }}</li>
    <li><b>Total Amount:</b> ₹{{ $order->total_amount }}</li>
    <li><b>Status:</b> {{ ucfirst($order->status) }}</li>
</ul>

<hr>

<h3>🛒 Items Ordered</h3>

<table border="1" cellpadding="8" cellspacing="0" width="100%">
    <thead>
        <tr>
            <th>Product</th>
            <th>Qty</th>
            <th>Unit Price</th>
            <th>Subtotal</th>
        </tr>
    </thead>

    <tbody>
        @foreach($order->items as $item)
            <tr>
                <td>{{ $item->product->name }}</td>
                <td>{{ $item->quantity }}</td>
                <td>₹{{ $item->unit_price }}</td>
                <td>₹{{ $item->unit_price * $item->quantity }}</td>
            </tr>
        @endforeach
    </tbody>
</table>

<hr>

<h3>📦 Delivery Address</h3>
<p>{{ $order->address }}</p>

<hr>

<p>We’ll notify you once your order is shipped 🚚</p>

<p>Thank you for shopping with <b>ShopLocal 🛍️</b></p>