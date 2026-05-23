@extends('layouts.app')
@section('title', 'Orders — Admin')

@section('content')
<div class="dash-layout">
    <div class="dash-sidebar">
        <div class="dash-brand">
            <div class="dash-brand-name">ShopLocal</div>
            <div class="dash-brand-role">Administrator</div>
        </div>
        <a href="{{ route('admin.dashboard') }}"  class="dash-nav-item">📊 Overview</a>
        <a href="{{ route('admin.vendors') }}"    class="dash-nav-item">🏪 Vendors</a>
        <a href="{{ route('admin.users') }}"      class="dash-nav-item">👥 Users</a>
        <a href="{{ route('admin.orders') }}"     class="dash-nav-item active">🛍️ Orders</a>
        <a href="{{ route('admin.products') }}"   class="dash-nav-item">📦 Products</a>
        <a href="{{ route('admin.categories') }}" class="dash-nav-item">🏷️ Categories</a>
        <a href="{{ route('admin.coupons') }}" class="dash-nav-item {{ request()->routeIs('admin.coupons') ? 'active' : '' }}">🎟️ Coupons</a>
        <a href="{{ route('home') }}"             class="dash-nav-item">🏠 View store</a>
    </div>

    <div class="dash-main">
        <div class="dash-header">
            <div>
                <div class="dash-title">All orders</div>
                <div class="dash-sub">{{ $orders->count() }} total orders</div>
            </div>
        </div>

        @if(session('success'))
            <div class="alert alert-success" style="margin:0 0 20px">{{ session('success') }}</div>
        @endif

        <div class="dash-card">
            <table>
                <thead>
                    <tr>
                        <th>Order ID</th>
                        <th>Customer</th>
                        <th>Amount</th>
                        <th>Payment</th>
                        <th>Date</th>
                        <th>Status</th>
                        <th>Update status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($orders as $order)
                    <tr>
                        <td><strong>#{{ $order->id }}</strong></td>
                        <td>{{ $order->user->name }}</td>
                        <td>₹{{ number_format($order->total_amount) }}</td>
                        <td style="text-transform:uppercase;font-size:12px">
                            {{ $order->payment_method }}
                        </td>
                        <td style="font-size:13px;color:var(--muted)">
                            {{ $order->created_at->format('d M Y') }}
                        </td>
                        <td>
                            <span class="status-pill
                                {{ $order->status === 'delivered'  ? 'status-active'  : '' }}
                                {{ $order->status === 'pending'    ? 'status-pending' : '' }}
                                {{ $order->status === 'processing' ? 'status-pending' : '' }}
                                {{ $order->status === 'shipped'    ? 'status-active'  : '' }}">
                                {{ $order->statusEmoji() }} {{ ucfirst($order->status) }}
                            </span>
                        </td>
                        <td>
                            {{-- Status update form --}}
                            <form method="POST"
                                  action="{{ route('admin.orders.status', $order) }}"
                                  style="display:flex;gap:6px;align-items:center">
                                @csrf
                                <select name="status"
                                        style="padding:4px 8px;border:1px solid var(--border);border-radius:6px;font-size:12px;font-family:var(--ff-body)">
                                    <option value="pending"    {{ $order->status === 'pending'    ? 'selected' : '' }}>Pending</option>
                                    <option value="processing" {{ $order->status === 'processing' ? 'selected' : '' }}>Processing</option>
                                    <option value="shipped"    {{ $order->status === 'shipped'    ? 'selected' : '' }}>Shipped</option>
                                    <option value="delivered"  {{ $order->status === 'delivered'  ? 'selected' : '' }}>Delivered</option>
                                </select>
                                <button type="submit" class="action-btn edit-btn">Update</button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" style="text-align:center;color:var(--muted);padding:32px">
                            No orders yet.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection