@extends('layouts.app')
@section('title', 'Vendor Dashboard — ShopLocal')

@section('content')
<div class="dash-layout">

    {{-- SIDEBAR --}}
    <div class="dash-sidebar">
        <div class="dash-brand">
            <div class="dash-brand-name">{{ auth()->user()->name }}</div>
            <div class="dash-brand-role">Shopkeeper</div>
        </div>
        <a href="{{ route('vendor.dashboard') }}"
           class="dash-nav-item {{ request()->routeIs('vendor.dashboard') ? 'active' : '' }}">
            📊 Dashboard
        </a>
        <a href="{{ route('vendor.products') }}"
           class="dash-nav-item {{ request()->routeIs('vendor.products*') ? 'active' : '' }}">
            📦 My products
        </a>
        <a href="{{ route('vendor.orders') }}"
           class="dash-nav-item {{ request()->routeIs('vendor.orders') ? 'active' : '' }}">
            🛍️ Orders
        </a>
        <a href="{{ route('home') }}" class="dash-nav-item">
            🏠 View store
        </a>
        <form method="POST" action="{{ route('logout') }}" style="margin-top:auto;padding:20px">
            @csrf
            <button type="submit" class="btn btn-outline btn-sm" style="width:100%;justify-content:center">
                Logout
            </button>
        </form>
    </div>

    {{-- MAIN CONTENT --}}
    <div class="dash-main">

        {{-- Header --}}
        <div class="dash-header">
            <div>
                <div class="dash-title">Welcome back 👋</div>
                <div class="dash-sub">Here is how your store is performing</div>
            </div>
            <a href="{{ route('vendor.products.create') }}" class="btn btn-warm">
                + Add product
            </a>
        </div>

        {{-- Flash messages --}}
        @if(session('success'))
            <div class="alert alert-success" style="margin:0 0 20px">{{ session('success') }}</div>
        @endif

        {{-- Stats --}}
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-label">Total revenue</div>
                <div class="stat-num">₹{{ number_format($totalRevenue) }}</div>
                <div class="stat-delta delta-up">From confirmed orders</div>
            </div>
            <div class="stat-card">
                <div class="stat-label">Products</div>
                <div class="stat-num">{{ $totalProducts }}</div>
                <div class="stat-delta" style="color:var(--muted)">In your store</div>
            </div>
            <div class="stat-card">
                <div class="stat-label">Total orders</div>
                <div class="stat-num">{{ $totalOrders }}</div>
                <div class="stat-delta" style="color:var(--muted)">Containing your products</div>
            </div>
            <div class="stat-card">
                <div class="stat-label">Active since</div>
                <div class="stat-num" style="font-size:20px">{{ auth()->user()->created_at->format('M Y') }}</div>
                <div class="stat-delta" style="color:var(--muted)">Member</div>
            </div>
        </div>

        {{-- Recent products --}}
        <div class="dash-card">
            <div class="dash-card-header">
                <div class="dash-card-title">Recent products</div>
                <a href="{{ route('vendor.products') }}" class="btn btn-sm btn-outline">View all</a>
            </div>
            @if($products->isEmpty())
                <div style="padding:32px;text-align:center;color:var(--muted)">
                    No products yet.
                    <a href="{{ route('vendor.products.create') }}" style="color:var(--warm)">Add your first product →</a>
                </div>
            @else
            <table>
                <thead>
                    <tr>
                        <th>Product</th>
                        <th>Category</th>
                        <th>Price</th>
                        <th>Stock</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($products as $product)
                    <tr>
                        <td>
                            <span style="font-size:20px;margin-right:8px">{{ $product->emoji }}</span>
                            <strong>{{ $product->name }}</strong>
                        </td>
                        <td>{{ $product->category->name }}</td>
                        <td>₹{{ number_format($product->price) }}</td>
                        <td>{{ $product->stock }}</td>
                        <td>
                            @if(!$product->is_active)
                                <span class="status-pill status-out">Inactive</span>
                            @elseif($product->stock == 0)
                                <span class="status-pill status-out">Out of stock</span>
                            @elseif($product->stock < 5)
                                <span class="status-pill status-pending">Low stock</span>
                            @else
                                <span class="status-pill status-active">Active</span>
                            @endif
                        </td>
                        <td>
                            <a href="{{ route('vendor.products.edit', $product) }}"
                               class="action-btn edit-btn">Edit</a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            @endif
        </div>

        {{-- Recent orders --}}
        <div class="dash-card">
            <div class="dash-card-header">
                <div class="dash-card-title">Recent orders</div>
                <a href="{{ route('vendor.orders') }}" class="btn btn-sm btn-outline">View all</a>
            </div>
            @if($recentOrders->isEmpty())
                <div style="padding:32px;text-align:center;color:var(--muted)">
                    No orders yet. Share your store link to get started!
                </div>
            @else
            <table>
                <thead>
                    <tr>
                        <th>Order ID</th>
                        <th>Customer</th>
                        <th>Product</th>
                        <th>Qty</th>
                        <th>Amount</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($recentOrders as $item)
                    <tr>
                        <td><strong>#{{ $item->order->id }}</strong></td>
                        <td>{{ $item->order->user->name }}</td>
                        <td>{{ $item->product->name }}</td>
                        <td>{{ $item->quantity }}</td>
                        <td>₹{{ number_format($item->unit_price * $item->quantity) }}</td>
                        <td>
                            <span class="status-pill
                                {{ $item->order->status === 'delivered'  ? 'status-active'  : '' }}
                                {{ $item->order->status === 'pending'    ? 'status-pending' : '' }}
                                {{ $item->order->status === 'processing' ? 'status-pending' : '' }}
                                {{ $item->order->status === 'shipped'    ? 'status-active'  : '' }}">
                                {{ $item->order->statusEmoji() }} {{ ucfirst($item->order->status) }}
                            </span>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            @endif
        </div>

    </div>
</div>
@endsection