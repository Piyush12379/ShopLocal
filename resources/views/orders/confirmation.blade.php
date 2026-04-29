@extends('layouts.app')
@section('title', 'Order Confirmed — ShopLocal')

@section('content')
<div style="max-width:640px;margin:60px auto;padding:0 24px;text-align:center">

    {{-- Success animation --}}
    <div style="font-size:72px;margin-bottom:16px;animation:popIn .5s ease">🎉</div>

    <h1 style="font-family:var(--ff-head);font-size:42px;font-weight:700;margin-bottom:8px">
        Order confirmed!
    </h1>
    <p style="color:var(--muted);font-size:16px;margin-bottom:32px">
        Thank you for your purchase. Your order has been placed successfully.
    </p>

    {{-- Order ID card --}}
    <div style="background:#fff;border:1px solid var(--border);border-radius:16px;padding:24px;margin-bottom:24px;text-align:left">

        <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:20px">
            <div>
                <div style="font-size:12px;color:var(--muted);text-transform:uppercase;letter-spacing:.06em">Order ID</div>
                <div style="font-family:var(--ff-head);font-size:24px;font-weight:700">#{{ $order->id }}</div>
            </div>
            <div style="background:var(--cream);padding:8px 16px;border-radius:20px;font-size:13px;font-weight:500">
                {{ $order->statusEmoji() }} {{ ucfirst($order->status) }}
            </div>
        </div>

        {{-- Order items --}}
        <div style="border-top:1px solid var(--border);padding-top:16px;margin-bottom:16px">
            @foreach($order->items as $item)
            <div style="display:flex;align-items:center;gap:12px;margin-bottom:12px">
                <div style="width:40px;height:40px;background:var(--cream);border-radius:8px;display:flex;align-items:center;justify-content:center;font-size:20px;flex-shrink:0">
                    {{ $item->product->emoji ?? '📦' }}
                </div>
                <div style="flex:1">
                    <div style="font-size:14px;font-weight:500">{{ $item->product->name ?? 'Product' }}</div>
                    <div style="font-size:12px;color:var(--muted)">Qty: {{ $item->quantity }}</div>
                </div>
                <div style="font-size:14px;font-weight:600">
                    ₹{{ number_format($item->subtotal()) }}
                </div>
            </div>
            @endforeach
        </div>

        {{-- Order details --}}
        <div style="border-top:1px solid var(--border);padding-top:16px">
            <div style="display:flex;justify-content:space-between;font-size:14px;margin-bottom:8px">
                <span style="color:var(--muted)">Payment method</span>
                <span style="font-weight:500;text-transform:capitalize">
                    {{ $order->payment_method === 'cod' ? 'Cash on delivery' : strtoupper($order->payment_method) }}
                </span>
            </div>
            <div style="display:flex;justify-content:space-between;font-size:14px;margin-bottom:8px">
                <span style="color:var(--muted)">Delivery address</span>
                <span style="font-weight:500;max-width:280px;text-align:right">{{ $order->address }}</span>
            </div>
            <div style="display:flex;justify-content:space-between;font-size:16px;font-weight:700;border-top:1px solid var(--border);padding-top:12px;margin-top:8px">
                <span>Total paid</span>
                <span>₹{{ number_format($order->total_amount) }}</span>
            </div>
        </div>
    </div>

    {{-- What happens next --}}
    <div style="background:var(--cream);border-radius:14px;padding:20px;margin-bottom:24px;text-align:left">
        <div style="font-size:13px;font-weight:600;margin-bottom:12px;text-transform:uppercase;letter-spacing:.06em;color:var(--muted)">
            What happens next?
        </div>
        <div style="display:flex;flex-direction:column;gap:10px">
            <div style="display:flex;gap:12px;align-items:flex-start;font-size:13px">
                <span>📦</span><span><strong>Processing</strong> — We notify the vendor to prepare your order</span>
            </div>
            <div style="display:flex;gap:12px;align-items:flex-start;font-size:13px">
                <span>🚚</span><span><strong>Shipping</strong> — Your order is dispatched within 2-3 days</span>
            </div>
            <div style="display:flex;gap:12px;align-items:flex-start;font-size:13px">
                <span>✅</span><span><strong>Delivery</strong> — Expected in 5-7 working days</span>
            </div>
        </div>
    </div>

    {{-- Action buttons --}}
    <div style="display:flex;gap:12px;justify-content:center;flex-wrap:wrap">
        <a href="{{ route('orders.index') }}" class="btn btn-primary">
            View my orders
        </a>
        <a href="{{ route('home') }}" class="btn btn-outline">
            Continue shopping
        </a>
    </div>

</div>

<style>
@keyframes popIn {
    0%   { transform: scale(0); opacity: 0; }
    70%  { transform: scale(1.2); }
    100% { transform: scale(1); opacity: 1; }
}
</style>
@endsection