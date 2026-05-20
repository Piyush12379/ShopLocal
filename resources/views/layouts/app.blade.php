<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'ShopLocal')</title>
    <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,400;0,600;0,700;1,400&family=Outfit:wght@300;400;500;600&display=swap" rel="stylesheet"/>
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    @stack('styles')
</head>
<body>

{{-- NAVIGATION --}}
<nav>
    <div class="nav-logo">
        <a href="{{ route('home') }}">Shop<span>Local</span></a>
    </div>

<div class="nav-links">
    <a href="{{ route('home') }}"
       class="{{ request()->routeIs('home') ? 'active-nav' : '' }}">Home</a>
    <a href="{{ route('home') }}#products"
       class="{{ request()->routeIs('products*') ? 'active-nav' : '' }}">Shop</a>
    <a href="{{ route('about') }}"
       class="{{ request()->routeIs('about') ? 'active-nav' : '' }}">About</a>
    <a href="{{ route('contact') }}"
       class="{{ request()->routeIs('contact') ? 'active-nav' : '' }}">Contact</a>
</div>

    <div class="nav-actions">
        @auth
            {{-- Cart icon — only for customers --}}
@if(auth()->user()->isCustomer())

    {{-- Wishlist link --}}
    <a href="{{ route('wishlist.index') }}"
       class="nav-icon-btn"
       title="My wishlist"
       style="font-size:16px">

        ♡

        @php
            $wCount = \App\Models\Wishlist::where('user_id', auth()->id())->count();
        @endphp

        @if($wCount > 0)
            <span class="cart-badge" style="background:#C85A3A">
                {{ $wCount }}
            </span>
        @endif
    </a>

    {{-- Cart icon --}}
    <a href="{{ route('cart.index') }}" class="nav-icon-btn">
        🛒
        <span class="cart-badge" id="cartBadge">
            {{ count(session('cart', [])) }}
        </span>
    </a>

    {{-- Orders --}}
    <a href="{{ route('orders.index') }}"
       class="btn btn-outline btn-sm">
        My orders
      </a>

         @endif

            {{-- Dashboard link based on role --}}
            @if(auth()->user()->isAdmin())
                <a href="{{ route('admin.dashboard') }}" class="btn btn-primary btn-sm">Admin panel</a>
            @elseif(auth()->user()->isShopkeeper())
                <a href="{{ route('vendor.dashboard') }}" class="btn btn-primary btn-sm">My store</a>
            @endif

            {{-- Logout --}}
            <form method="POST" action="{{ route('logout') }}" style="display:inline">
                @csrf
                <button type="submit" class="btn btn-outline btn-sm">Logout</button>
            </form>

        @else
            <a href="{{ route('login') }}"    class="btn btn-outline btn-sm">Login</a>
            <a href="{{ route('register') }}" class="btn btn-primary btn-sm">Register</a>
        @endauth
    </div>
</nav>

{{-- TOAST notification (used by cart JS) --}}
<div class="toast" id="toast">
    <span class="toast-icon" id="toastIcon">🛒</span>
    <span id="toastMsg">Added to cart!</span>
</div>

{{-- PAGE CONTENT --}}
<main>
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="alert alert-error">{{ session('error') }}</div>
    @endif

    @yield('content')
</main>

<footer>
    <div class="footer-grid">
        <div class="footer-brand">
            <div style="font-family:var(--ff-head);font-size:28px;font-weight:700;color:var(--warm)">ShopLocal</div>
            <p>Connecting artisans with customers. Every purchase supports a local maker.</p>
        </div>
        <div class="footer-col">
            <h4>Shop</h4>
            <a href="{{ route('home') }}">All products</a>
        </div>
        <div class="footer-col">
            <h4>Company</h4>
            <a href="{{ route('about') }}">About us</a>
            <a href="{{ route('contact') }}">Contact</a>
        </div>
        <div class="footer-col">
            <h4>Account</h4>
            @auth
                <form id="logout-form-footer" action="{{ route('logout') }}" method="POST" style="display:none">@csrf</form>
                <a href="#" onclick="document.getElementById('logout-form-footer').submit()">Logout</a>
            @else
                <a href="{{ route('login') }}">Login</a>
                <a href="{{ route('register') }}">Register</a>
            @endauth
        </div>
    </div>
    <div class="footer-bottom">
        <span>© 2026 ShopLocal. All rights reserved.</span>
        <span>Made with ❤️ in India</span>
    </div>
</footer>

{{-- Global toast JS --}}
<script>
function showToast(msg, icon) {
    const toast = document.getElementById('toast');
    document.getElementById('toastMsg').textContent  = msg  || 'Done!';
    document.getElementById('toastIcon').textContent = icon || '✓';
    toast.classList.add('show');
    setTimeout(() => toast.classList.remove('show'), 2800);
}
</script>

@stack('scripts')
</body>
</html>