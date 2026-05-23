@extends('layouts.app')
@section('title', 'Admin Dashboard — ShopLocal')

@section('content')
<div class="dash-layout">

    {{-- SIDEBAR --}}
    <div class="dash-sidebar">
        <div class="dash-brand">
            <div class="dash-brand-name">ShopLocal</div>
            <div class="dash-brand-role">Administrator</div>
        </div>
        <a href="{{ route('admin.dashboard') }}"
           class="dash-nav-item {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
            📊 Overview
        </a>
        <a href="{{ route('admin.vendors') }}"
           class="dash-nav-item {{ request()->routeIs('admin.vendors') ? 'active' : '' }}">
            🏪 Vendors
            @if($pendingVendors > 0)
                <span style="background:var(--accent);color:#fff;font-size:10px;padding:1px 6px;border-radius:10px;margin-left:auto">
                    {{ $pendingVendors }}
                </span>
            @endif
        </a>
        <a href="{{ route('admin.users') }}"
           class="dash-nav-item {{ request()->routeIs('admin.users') ? 'active' : '' }}">
            👥 Users
        </a>
        <a href="{{ route('admin.orders') }}"
           class="dash-nav-item {{ request()->routeIs('admin.orders') ? 'active' : '' }}">
            🛍️ Orders
        </a>
        <a href="{{ route('admin.products') }}"
           class="dash-nav-item {{ request()->routeIs('admin.products') ? 'active' : '' }}">
            📦 Products
        </a>
        <a href="{{ route('admin.categories') }}"
           class="dash-nav-item {{ request()->routeIs('admin.categories') ? 'active' : '' }}">
            🏷️ Categories
        </a>
         <a href="{{ route('admin.coupons') }}"
           class="dash-nav-item {{ request()->routeIs('admin.coupons') ? 'active' : '' }}">
            🎟️ Coupons
        </a>


        <a href="{{ route('home') }}" class="dash-nav-item">🏠 View store</a>
        <form method="POST" action="{{ route('logout') }}" style="padding:20px">
            @csrf
            <button type="submit" class="btn btn-outline btn-sm" style="width:100%;justify-content:center">
                Logout
            </button>
        </form>
    </div>

    {{-- MAIN --}}
    <div class="dash-main">
        <div class="dash-header">
            <div>
                <div class="dash-title">Platform overview</div>
                <div class="dash-sub">{{ now()->format('l, d F Y') }}</div>
            </div>
        </div>

        @if(session('success'))
            <div class="alert alert-success" style="margin:0 0 20px">{{ session('success') }}</div>
        @endif

        {{-- Stats --}}
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-label">Total revenue</div>
                <div class="stat-num">₹{{ number_format($totalRevenue) }}</div>
                <div class="stat-delta delta-up">From all orders</div>
            </div>
            <div class="stat-card">
                <div class="stat-label">Total orders</div>
                <div class="stat-num">{{ $totalOrders }}</div>
                <div class="stat-delta" style="color:var(--muted)">Platform wide</div>
            </div>
            <div class="stat-card">
                <div class="stat-label">Customers</div>
                <div class="stat-num">{{ $totalCustomers }}</div>
                <div class="stat-delta" style="color:var(--muted)">Registered</div>
            </div>
            <div class="stat-card">
                <div class="stat-label">Active vendors</div>
                <div class="stat-num">{{ $totalVendors }}</div>
                @if($pendingVendors > 0)
                    <div class="stat-delta" style="color:var(--accent)">
                        {{ $pendingVendors }} pending approval
                    </div>
                @else
                    <div class="stat-delta" style="color:var(--muted)">All approved</div>
                @endif
            </div>
        </div>

        {{-- Pending vendor approvals --}}
        @if($pendingVendorList->isNotEmpty())
        <div class="dash-card" style="border:1.5px solid var(--accent);margin-bottom:20px">
            <div class="dash-card-header">
                <div class="dash-card-title">
                    ⚠️ Pending vendor approvals
                    <span style="background:var(--accent);color:#fff;font-size:11px;padding:2px 8px;border-radius:10px;margin-left:8px">
                        {{ $pendingVendorList->count() }}
                    </span>
                </div>
                <a href="{{ route('admin.vendors') }}" class="btn btn-sm btn-outline">View all</a>
            </div>
            <table>
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Applied on</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($pendingVendorList as $vendor)
                    <tr>
                        <td><strong>{{ $vendor->name }}</strong></td>
                        <td>{{ $vendor->email }}</td>
                        <td>{{ $vendor->created_at->format('d M Y') }}</td>
                        <td>
                            <div style="display:flex;gap:8px">
                                <form method="POST" action="{{ route('admin.vendors.approve', $vendor) }}">
                                    @csrf
                                    <button type="submit" class="action-btn"
                                            style="background:#e8f5e9;color:#2e7d32">
                                        ✓ Approve
                                    </button>
                                </form>
                                <form method="POST" action="{{ route('admin.vendors.reject', $vendor) }}">
                                    @csrf
                                    <button type="submit" class="action-btn del-btn">
                                        ✗ Reject
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @endif

        {{-- Recent orders --}}
        <div class="dash-card">
            <div class="dash-card-header">
                <div class="dash-card-title">Recent orders</div>
                <a href="{{ route('admin.orders') }}" class="btn btn-sm btn-outline">View all</a>
            </div>
            @if($recentOrders->isEmpty())
                <div style="padding:32px;text-align:center;color:var(--muted)">No orders yet.</div>
            @else
            <table>
                <thead>
                    <tr>
                        <th>Order ID</th>
                        <th>Customer</th>
                        <th>Amount</th>
                        <th>Payment</th>
                        <th>Date</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($recentOrders as $order)
                    <tr>
                        <td><strong>#{{ $order->id }}</strong></td>
                        <td>{{ $order->user->name }}</td>
                        <td>₹{{ number_format($order->total_amount) }}</td>
                        <td style="text-transform:uppercase;font-size:12px">{{ $order->payment_method }}</td>
                        <td style="font-size:13px;color:var(--muted)">{{ $order->created_at->format('d M Y') }}</td>
                        <td>
                            <span class="status-pill
                                {{ $order->status === 'delivered'  ? 'status-active'  : '' }}
                                {{ $order->status === 'pending'    ? 'status-pending' : '' }}
                                {{ $order->status === 'processing' ? 'status-pending' : '' }}
                                {{ $order->status === 'shipped'    ? 'status-active'  : '' }}">
                                {{ $order->statusEmoji() }} {{ ucfirst($order->status) }}
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