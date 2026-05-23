@extends('layouts.app')
@section('title', 'Products — Admin')

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
        <a href="{{ route('admin.orders') }}"     class="dash-nav-item">🛍️ Orders</a>
        <a href="{{ route('admin.products') }}"   class="dash-nav-item active">📦 Products</a>
        <a href="{{ route('admin.categories') }}" class="dash-nav-item">🏷️ Categories</a>
        <a href="{{ route('admin.coupons') }}" class="dash-nav-item {{ request()->routeIs('admin.coupons') ? 'active' : '' }}">🎟️ Coupons</a>
        <a href="{{ route('home') }}"             class="dash-nav-item">🏠 View store</a>
    </div>
    <div class="dash-main">
        <div class="dash-header">
            <div>
                <div class="dash-title">All products</div>
                <div class="dash-sub">{{ $products->count() }} products across all vendors</div>
            </div>
        </div>
        <div class="dash-card">
            <table>
                <thead>
                    <tr>
                        <th>Product</th>
                        <th>Vendor</th>
                        <th>Category</th>
                        <th>Price</th>
                        <th>Stock</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($products as $product)
                    <tr>
                        <td>
                            <span style="font-size:18px;margin-right:8px">{{ $product->emoji }}</span>
                            <strong>{{ $product->name }}</strong>
                        </td>
                        <td>{{ $product->vendor->name }}</td>
                        <td>{{ $product->category->name }}</td>
                        <td>₹{{ number_format($product->price) }}</td>
                        <td>{{ $product->stock }}</td>
                        <td>
                            @if(!$product->is_active)
                                <span class="status-pill status-out">Inactive</span>
                            @elseif($product->stock == 0)
                                <span class="status-pill status-out">Out of stock</span>
                            @else
                                <span class="status-pill status-active">Active</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" style="text-align:center;color:var(--muted);padding:32px">
                            No products yet.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection