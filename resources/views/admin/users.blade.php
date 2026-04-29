@extends('layouts.app')
@section('title', 'Users — Admin')

@section('content')
<div class="dash-layout">
    <div class="dash-sidebar">
        <div class="dash-brand">
            <div class="dash-brand-name">ShopLocal</div>
            <div class="dash-brand-role">Administrator</div>
        </div>
        <a href="{{ route('admin.dashboard') }}"  class="dash-nav-item">📊 Overview</a>
        <a href="{{ route('admin.vendors') }}"    class="dash-nav-item">🏪 Vendors</a>
        <a href="{{ route('admin.users') }}"      class="dash-nav-item active">👥 Users</a>
        <a href="{{ route('admin.orders') }}"     class="dash-nav-item">🛍️ Orders</a>
        <a href="{{ route('admin.products') }}"   class="dash-nav-item">📦 Products</a>
        <a href="{{ route('admin.categories') }}" class="dash-nav-item">🏷️ Categories</a>
        <a href="{{ route('home') }}"             class="dash-nav-item">🏠 View store</a>
    </div>

    <div class="dash-main">
        <div class="dash-header">
            <div>
                <div class="dash-title">All users</div>
                <div class="dash-sub">{{ $users->count() }} total users on the platform</div>
            </div>
        </div>

        @if(session('success'))
            <div class="alert alert-success" style="margin:0 0 20px">{{ session('success') }}</div>
        @endif
        @if(session('error'))
            <div class="alert alert-error" style="margin:0 0 20px">{{ session('error') }}</div>
        @endif

        <div class="dash-card">
            <table>
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Role</th>
                        <th>Status</th>
                        <th>Joined</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($users as $user)
                    <tr>
                        <td>
                            <strong>{{ $user->name }}</strong>
                            @if($user->id === auth()->id())
                                <span style="font-size:10px;color:var(--muted)">(you)</span>
                            @endif
                        </td>
                        <td>{{ $user->email }}</td>
                        <td>
                            <span class="status-pill
                                {{ $user->role === 'admin'      ? 'status-active'  : '' }}
                                {{ $user->role === 'shopkeeper' ? 'status-pending' : '' }}
                                {{ $user->role === 'customer'   ? 'status-out'     : '' }}">
                                {{ ucfirst($user->role) }}
                            </span>
                        </td>
                        <td>
                            @if($user->role === 'shopkeeper')
                                @if($user->is_approved)
                                    <span class="status-pill status-active">Approved</span>
                                @else
                                    <span class="status-pill status-pending">Pending</span>
                                @endif
                            @else
                                <span style="font-size:13px;color:var(--muted)">—</span>
                            @endif
                        </td>
                        <td style="font-size:13px;color:var(--muted)">
                            {{ $user->created_at->format('d M Y') }}
                        </td>
                        <td>
                            @if($user->id !== auth()->id())
                            <form method="POST"
                                  action="{{ route('admin.users.delete', $user) }}"
                                  onsubmit="return confirm('Delete {{ $user->name }}? This cannot be undone.')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="action-btn del-btn">Delete</button>
                            </form>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection