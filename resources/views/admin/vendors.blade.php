@extends('layouts.app')
@section('title', 'Vendors — Admin')

@section('content')
<div class="dash-layout">
    <div class="dash-sidebar">
        <div class="dash-brand">
            <div class="dash-brand-name">ShopLocal</div>
            <div class="dash-brand-role">Administrator</div>
        </div>
        <a href="{{ route('admin.dashboard') }}"  class="dash-nav-item">📊 Overview</a>
        <a href="{{ route('admin.vendors') }}"    class="dash-nav-item active">🏪 Vendors</a>
        <a href="{{ route('admin.users') }}"      class="dash-nav-item">👥 Users</a>
        <a href="{{ route('admin.orders') }}"     class="dash-nav-item">🛍️ Orders</a>
        <a href="{{ route('admin.products') }}"   class="dash-nav-item">📦 Products</a>
        <a href="{{ route('admin.categories') }}" class="dash-nav-item">🏷️ Categories</a>
        <a href="{{ route('admin.coupons') }}" class="dash-nav-item {{ request()->routeIs('admin.coupons') ? 'active' : '' }}">🎟️ Coupons</a>
        <a href="{{ route('home') }}"             class="dash-nav-item">🏠 View store</a>
    </div>

    <div class="dash-main">
        <div class="dash-header">
            <div>
                <div class="dash-title">Vendors</div>
                <div class="dash-sub">Manage vendor applications and approved sellers</div>
            </div>
        </div>

        @if(session('success'))
            <div class="alert alert-success" style="margin:0 0 20px">{{ session('success') }}</div>
        @endif
        @if(session('error'))
            <div class="alert alert-error" style="margin:0 0 20px">{{ session('error') }}</div>
        @endif

        {{-- Pending vendors --}}
        <div class="dash-card" style="margin-bottom:24px">
            <div class="dash-card-header">
                <div class="dash-card-title">
                    Pending applications
                    <span style="background:var(--accent);color:#fff;font-size:11px;padding:2px 8px;border-radius:10px;margin-left:8px">
                        {{ $pendingVendors->count() }}
                    </span>
                </div>
            </div>
            @if($pendingVendors->isEmpty())
                <div style="padding:32px;text-align:center;color:var(--muted)">
                    ✅ No pending applications
                </div>
            @else
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
                    @foreach($pendingVendors as $vendor)
                    <tr>
                        <td><strong>{{ $vendor->name }}</strong></td>
                        <td>{{ $vendor->email }}</td>
                        <td>{{ $vendor->created_at->format('d M Y') }}</td>
                        <td>
                            <div style="display:flex;gap:8px">
                                <form method="POST" action="{{ route('admin.vendors.approve', $vendor) }}">
                                    @csrf
                                    <button type="submit"
                                            class="action-btn"
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
            @endif
        </div>

        {{-- Approved vendors --}}
        <div class="dash-card">
            <div class="dash-card-header">
                <div class="dash-card-title">
                    Approved vendors ({{ $approvedVendors->count() }})
                </div>
            </div>
            @if($approvedVendors->isEmpty())
                <div style="padding:32px;text-align:center;color:var(--muted)">No approved vendors yet.</div>
            @else
            <table>
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Products</th>
                        <th>Joined</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($approvedVendors as $vendor)
                    <tr>
                        <td><strong>{{ $vendor->name }}</strong></td>
                        <td>{{ $vendor->email }}</td>
                        <td>{{ $vendor->products_count }} products</td>
                        <td>{{ $vendor->created_at->format('d M Y') }}</td>
                        <td>
                            <form method="POST" action="{{ route('admin.vendors.reject', $vendor) }}"
                                  onsubmit="return confirm('Suspend this vendor?')">
                                @csrf
                                <button type="submit" class="action-btn del-btn">Suspend</button>
                            </form>
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