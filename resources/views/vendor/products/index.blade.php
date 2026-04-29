@extends('layouts.app')
@section('title', 'My Products — ShopLocal')

@section('content')
<div class="dash-layout">
    <div class="dash-sidebar">
        <div class="dash-brand">
            <div class="dash-brand-name">{{ auth()->user()->name }}</div>
            <div class="dash-brand-role">Shopkeeper</div>
        </div>
        <a href="{{ route('vendor.dashboard') }}" class="dash-nav-item">📊 Dashboard</a>
        <a href="{{ route('vendor.products') }}" class="dash-nav-item active">📦 My products</a>
        <a href="{{ route('vendor.orders') }}"   class="dash-nav-item">🛍️ Orders</a>
        <a href="{{ route('home') }}"            class="dash-nav-item">🏠 View store</a>
    </div>

    <div class="dash-main">
        <div class="dash-header">
            <div>
                <div class="dash-title">My products</div>
                <div class="dash-sub">{{ $products->count() }} products in your store</div>
            </div>
            <a href="{{ route('vendor.products.create') }}" class="btn btn-warm">+ Add product</a>
        </div>

        @if(session('success'))
            <div class="alert alert-success" style="margin:0 0 20px">{{ session('success') }}</div>
        @endif

        @if($products->isEmpty())
            <div class="empty-state">
                <div style="font-size:48px;margin-bottom:16px">📦</div>
                <h3>No products yet</h3>
                <p style="margin-top:8px">Add your first product to start selling</p>
                <a href="{{ route('vendor.products.create') }}" class="btn btn-warm" style="margin-top:24px">
                    + Add first product
                </a>
            </div>
        @else
            <div class="dash-card">
                <table>
                    <thead>
                        <tr>
                            <th>Product</th>
                            <th>Category</th>
                            <th>Price</th>
                            <th>Stock</th>
                            <th>Status</th>
                            <th>Actions</th>
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
                            <td>
                                ₹{{ number_format($product->price) }}
                                @if($product->old_price)
                                    <span style="font-size:11px;color:var(--muted);text-decoration:line-through;margin-left:4px">
                                        ₹{{ number_format($product->old_price) }}
                                    </span>
                                @endif
                            </td>
                            <td>
                                <span style="{{ $product->stock < 5 ? 'color:#F57F17;font-weight:600' : '' }}">
                                    {{ $product->stock }}
                                </span>
                            </td>
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
            </div>
        @endif
    </div>
</div>
@endsection