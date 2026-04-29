@extends('layouts.app')
@section('title', 'My Orders — ShopLocal Vendor')

@section('content')
<div class="dash-layout">
    <div class="dash-sidebar">
        <div class="dash-brand">
            <div class="dash-brand-name">{{ auth()->user()->name }}</div>
            <div class="dash-brand-role">Shopkeeper</div>
        </div>
        <a href="{{ route('vendor.dashboard') }}" class="dash-nav-item">📊 Dashboard</a>
        <a href="{{ route('vendor.products') }}"  class="dash-nav-item">📦 My products</a>
        <a href="{{ route('vendor.orders') }}"    class="dash-nav-item active">🛍️ Orders</a>
        <a href="{{ route('home') }}"             class="dash-nav-item">🏠 View store</a>
    </div>

    <div class="dash-main">
        <div class="dash-header">
            <div>
                <div class="dash-title">Orders</div>
                <div class="dash-sub">Orders containing your products</div>
            </div>
        </div>

        @if($orderItems->isEmpty())
            <div class="empty-state">
                <div style="font-size:48px;margin-bottom:16px">🛍️</div>
                <h3>No orders yet</h3>
                <p>When customers order your products, they will appear here</p>
            </div>
        @else
            <div class="dash-card">
                <table>
                    <thead>
                        <tr>
                            <th>Order ID</th>
                            <th>Customer</th>
                            <th>Products</th>
                            <th>Total</th>
                            <th>Date</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($orderItems as $orderId => $items)
                        <tr>
                            <td><strong>#{{ $orderId }}</strong></td>
                            <td>{{ $items->first()->order->user->name }}</td>
                            <td>
                                @foreach($items as $item)
                                    <div style="font-size:13px">
                                        {{ $item->product->emoji }} {{ $item->product->name }}
                                        <span style="color:var(--muted)">× {{ $item->quantity }}</span>
                                    </div>
                                @endforeach
                            </td>
                            <td>
                                ₹{{ number_format($items->sum(fn($i) => $i->unit_price * $i->quantity)) }}
                            </td>
                            <td style="font-size:13px;color:var(--muted)">
                                {{ $items->first()->order->created_at->format('d M Y') }}
                            </td>
                            <td>
                                <span class="status-pill
                                    {{ $items->first()->order->status === 'delivered'  ? 'status-active'  : '' }}
                                    {{ $items->first()->order->status === 'pending'    ? 'status-pending' : '' }}">
                                    {{ $items->first()->order->statusEmoji() }}
                                    {{ ucfirst($items->first()->order->status) }}
                                </span>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
</div>
@endsection