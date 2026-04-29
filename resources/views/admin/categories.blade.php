@extends('layouts.app')
@section('title', 'Categories — Admin')

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
        <a href="{{ route('admin.categories') }}" class="dash-nav-item active">🏷️ Categories</a>
        <a href="{{ route('home') }}"             class="dash-nav-item">🏠 View store</a>
    </div>
    <div class="dash-main">
        <div class="dash-header">
            <div>
                <div class="dash-title">Categories</div>
                <div class="dash-sub">{{ $categories->count() }} categories</div>
            </div>
        </div>

        @if(session('success'))
            <div class="alert alert-success" style="margin:0 0 20px">{{ session('success') }}</div>
        @endif
        @if(session('error'))
            <div class="alert alert-error" style="margin:0 0 20px">{{ session('error') }}</div>
        @endif

        {{-- Add category form --}}
        <div class="dash-card" style="margin-bottom:20px">
            <div class="dash-card-header">
                <div class="dash-card-title">Add new category</div>
            </div>
            <div style="padding:20px">
                <form method="POST"
                      action="{{ route('admin.categories.store') }}"
                      style="display:flex;gap:12px;align-items:flex-end;flex-wrap:wrap">
                    @csrf
                    <div class="form-group">
                        <label>Category name *</label>
                        <input type="text" name="name"
                               value="{{ old('name') }}"
                               required placeholder="e.g. Pottery"
                               style="min-width:200px"/>
                    </div>
                    <div class="form-group">
                        <label>Emoji *</label>
                        <input type="text" name="emoji"
                               value="{{ old('emoji') }}"
                               required placeholder="🏺" maxlength="10"
                               style="width:80px"/>
                    </div>
                    <button type="submit" class="btn btn-warm">Add category</button>
                </form>
            </div>
        </div>

        {{-- Categories list --}}
        <div class="dash-card">
            <table>
                <thead>
                    <tr>
                        <th>Emoji</th>
                        <th>Name</th>
                        <th>Slug</th>
                        <th>Products</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($categories as $category)
                    <tr>
                        <td style="font-size:24px">{{ $category->emoji }}</td>
                        <td><strong>{{ $category->name }}</strong></td>
                        <td style="font-family:monospace;font-size:12px;color:var(--muted)">
                            {{ $category->slug }}
                        </td>
                        <td>{{ $category->products_count }}</td>
                        <td>
                            @if($category->products_count == 0)
                            <form method="POST"
                                  action="{{ route('admin.categories.delete', $category) }}"
                                  onsubmit="return confirm('Delete this category?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="action-btn del-btn">Delete</button>
                            </form>
                            @else
                                <span style="font-size:12px;color:var(--muted)">Has products</span>
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