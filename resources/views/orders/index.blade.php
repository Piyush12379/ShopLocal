@extends('layouts.app')
@section('title', 'My Orders — ShopLocal')

@section('content')
<div style="max-width:800px;margin:0 auto;padding:48px 24px">

    <div style="margin-bottom:32px">
        <h1 style="font-family:var(--ff-head);font-size:40px;font-weight:700;margin-bottom:6px">
            My orders
        </h1>
        <p style="color:var(--muted)">{{ $orders->count() }} {{ Str::plural('order', $orders->count()) }} placed</p>
    </div>

    @if($orders->isEmpty())
        <div class="empty-state">
            <div style="font-size:56px;margin-bottom:16px">📦</div>
            <h3>No orders yet</h3>
            <p style="margin-top:8px">When you place an order it will appear here</p>
            <a href="{{ route('home') }}" class="btn btn-primary" style="margin-top:24px">
                Start shopping
            </a>
        </div>
    @else
        <div style="display:flex;flex-direction:column;gap:16px">
            @foreach($orders as $order)
            <div style="background:#fff;border:1px solid var(--border);border-radius:14px;padding:20px">

                {{-- Order header --}}
                <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:16px;flex-wrap:wrap;gap:10px">
                    <div>
                        <div style="font-size:12px;color:var(--muted)">Order ID</div>
                        <div style="font-family:var(--ff-head);font-size:20px;font-weight:700">#{{ $order->id }}</div>
                    </div>
                    <div style="text-align:right">
                        <div style="font-size:12px;color:var(--muted)">Placed on</div>
                        <div style="font-size:13px;font-weight:500">{{ $order->created_at->format('d M Y') }}</div>
                    </div>
                    <div style="background:var(--cream);padding:6px 14px;border-radius:20px;font-size:13px;font-weight:500">
                        {{ $order->statusEmoji() }} {{ ucfirst($order->status) }}
                    </div>
                    <div style="font-family:var(--ff-head);font-size:22px;font-weight:700;color:var(--accent)">
                        ₹{{ number_format($order->total_amount) }}
                    </div>
                </div>

                {{-- Order items --}}
                <div style="border-top:1px solid var(--border);padding-top:14px;display:flex;flex-wrap:wrap;gap:10px">
                    @foreach($order->items as $item)
                    <div style="display:flex;align-items:center;gap:8px;background:var(--cream);border-radius:8px;padding:8px 12px">
                        <span style="font-size:18px">{{ $item->product->emoji ?? '📦' }}</span>
                        <div>
                            <div style="font-size:12px;font-weight:500">{{ $item->product->name ?? 'Product' }}</div>
                            <div style="font-size:11px;color:var(--muted)">Qty: {{ $item->quantity }} · ₹{{ number_format($item->unit_price) }} each</div>
                        </div>
                    </div>
                    @endforeach
                </div>

                {{-- View details link --}}
    <div style="margin-top:14px;padding-top:14px;border-top:1px solid var(--border);
            display:flex;align-items:center;justify-content:space-between">
            <a href="{{ route('orders.confirmation', $order->id) }}"
           style="font-size:13px;color:var(--warm);font-weight:500;text-decoration:none">
                View full details →
            </a>
            <a href="{{ route('orders.invoice', $order->id) }}"
              style="font-size:13px;color:var(--muted);text-decoration:none;
              display:flex;align-items:center;gap:5px;padding:5px 10px;
              border:1px solid var(--border);border-radius:var(--r)">
             📄 Download invoice
            </a>
     </div>

            </div>
            @endforeach
        </div>
    @endif

</div>
@endsection