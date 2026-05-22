<!DOCTYPE html>
<html>
<head>
    <style>
        body { font-family: Arial, sans-serif; background-color: #f4f4f4; padding: 20px; }
        .container { background-color: #ffffff; padding: 30px; border-radius: 8px; max-width: 600px; margin: 0 auto; box-shadow: 0 4px 8px rgba(0,0,0,0.1); }
        .header { text-align: center; border-bottom: 2px solid #eee; padding-bottom: 20px; margin-bottom: 20px; }
        .header h1 { color: #2c3e50; }
        .table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        .table th, .table td { padding: 12px; border: 1px solid #ddd; text-align: left; }
        .table th { background-color: #f8f9fa; }
        .btn { display: inline-block; padding: 12px 24px; background-color: #007bff; color: #ffffff; text-decoration: none; border-radius: 5px; margin-top: 20px; font-weight: bold; }
        .footer { margin-top: 30px; text-align: center; color: #888; font-size: 12px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>🛍️ New Order Received!</h1>
        </div>

        <p>Hello <strong>{{ $vendor->name }}</strong>,</p>
        <p>Great news! A customer just purchased items from your shop. Here are the details for Order <strong>#{{ $order->id }}</strong>:</p>

        <table class="table">
            <thead>
                <tr>
                    <th>Product</th>
                    <th>Quantity</th>
                    <th>Price</th>
                </tr>
            </thead>
            <tbody>
                @foreach($order->items as $item)
                    @if($item->product->vendor_id == $vendor->id)
                    <tr>
                        <td>{{ $item->product->name }}</td>
                        <td>{{ $item->quantity }}</td>
                        <td>₹{{ number_format($item->unit_price, 2) }}</td>
                    </tr>
                    @endif
                @endforeach
            </tbody>
        </table>

        <div style="text-align: center;">
            <a href="{{ route('vendor.orders') }}" class="btn">View My Orders</a>
        </div>

        <div class="footer">
            <p>Thank you for selling with ShopLocal!</p>
        </div>
    </div>
</body>
</html>