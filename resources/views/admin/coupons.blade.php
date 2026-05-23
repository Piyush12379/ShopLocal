@extends('layouts.app')
@section('title', 'Coupons — Admin')

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
        <a href="{{ route('admin.products') }}"   class="dash-nav-item">📦 Products</a>
        <a href="{{ route('admin.categories') }}" class="dash-nav-item">🏷️ Categories</a>
        <a href="{{ route('admin.coupons') }}"    class="dash-nav-item active">🎟️ Coupons</a>
        <a href="{{ route('home') }}"             class="dash-nav-item">🏠 View store</a>
    </div>

    <div class="dash-main">
        <div class="dash-header">
            <div>
                <div class="dash-title">Coupon codes</div>
                <div class="dash-sub">Create and manage discount codes</div>
            </div>
        </div>

        @if(session('success'))
            <div class="alert alert-success" style="margin:0 0 20px">{{ session('success') }}</div>
        @endif
        @if(session('error'))
            <div class="alert alert-error" style="margin:0 0 20px">{{ session('error') }}</div>
        @endif

        {{-- Create coupon form --}}
        <div class="dash-card" style="margin-bottom:20px">
            <div class="dash-card-header">
                <div class="dash-card-title">Create new coupon</div>
            </div>
            <div style="padding:20px">
                <form method="POST" action="{{ route('admin.coupons.store') }}">
                    @csrf
                    <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(160px,1fr));gap:14px;margin-bottom:16px">

                        <div class="form-group">
                            <label>Code * <span style="color:var(--muted);font-size:11px">(e.g. SAVE20)</span></label>
                            <input type="text" name="code" required
                                   placeholder="SAVE20" maxlength="20"
                                   style="text-transform:uppercase"/>
                        </div>

                        <div class="form-group">
                            <label>Discount type *</label>
                            <select name="type" required>
                                <option value="percent">Percentage (e.g. 20%)</option>
                                <option value="flat">Flat amount (e.g. ₹100)</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label>Discount value * <span style="color:var(--muted);font-size:11px">(% or ₹)</span></label>
                            <input type="number" name="value" required min="1" placeholder="20"/>
                        </div>

                        <div class="form-group">
                            <label>Min order amount (₹)</label>
                            <input type="number" name="min_order" min="0" placeholder="0"/>
                        </div>

                        <div class="form-group">
                            <label>Max uses</label>
                            <input type="number" name="max_uses" min="1" placeholder="100"/>
                        </div>

                        <div class="form-group">
                            <label>Expires on</label>
                            <input type="date" name="expires_at"/>
                        </div>

                    </div>
                    <button type="submit" class="btn btn-warm">Create coupon</button>
                </form>
            </div>
        </div>

        {{-- Coupons list --}}
        <div class="dash-card">
            <table>
                <thead>
                    <tr>
                        <th>Code</th>
                        <th>Type</th>
                        <th>Value</th>
                        <th>Min order</th>
                        <th>Used</th>
                        <th>Expires</th>
                        <th>Status</th>
                        <th>Delete</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($coupons as $coupon)
                    <tr>
                        <td><strong style="font-family:monospace">{{ $coupon->code }}</strong></td>
                        <td>{{ $coupon->type === 'percent' ? 'Percentage' : 'Flat' }}</td>
                        <td>
                            {{ $coupon->type === 'percent'
                                ? $coupon->value . '%'
                                : '₹' . number_format($coupon->value) }}
                        </td>
                        <td>₹{{ number_format($coupon->min_order) }}</td>
                        <td>{{ $coupon->used_count }} / {{ $coupon->max_uses }}</td>
                        <td style="font-size:13px;color:var(--muted)">
                            {{ $coupon->expires_at ? $coupon->expires_at->format('d M Y') : 'Never' }}
                        </td>
                        <td>
                            @if($coupon->isValid())
                                <span class="status-pill status-active">Active</span>
                            @else
                                <span class="status-pill status-out">Expired</span>
                            @endif
                        </td>
                        <td>
                            <form method="POST"
                                  action="{{ route('admin.coupons.delete', $coupon) }}"
                                  onsubmit="return confirm('Delete coupon {{ $coupon->code }}?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="action-btn del-btn">Delete</button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" style="text-align:center;color:var(--muted);padding:32px">
                            No coupons yet. Create your first one above.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection