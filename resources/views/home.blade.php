@extends('layouts.app')
@section('title', 'ShopLocal — Handcrafted Goods')

@section('content')

{{-- ═══════════════════════════════════════
     HERO SECTION
═══════════════════════════════════════ --}}
<section class="hero">
    <div class="hero-content">
        <div class="hero-text">
            <div class="hero-eyebrow">
                <span class="hero-dot"></span>
                100+ local artisans
            </div>
            <h1 class="hero-title">
                Discover<br>
                <em>handcrafted</em><br>
                treasures
            </h1>
            <p class="hero-subtitle">
                Support small businesses and find unique handmade
                products from artisans across India.
            </p>
            <div class="hero-actions">
                <a href="#products" class="btn btn-primary">Shop now</a>
                <a href="#categories" class="btn btn-outline">Browse categories</a>
            </div>
        </div>

        {{-- Featured product card in hero --}}
        @if($featuredProduct)
        <div class="hero-visual">
            <div class="hero-product-card">
                <div class="hero-product-img">{{ $featuredProduct->emoji }}</div>
                <div class="hero-product-tag">{{ $featuredProduct->category->name }}</div>
                <div class="hero-product-name">{{ $featuredProduct->name }}</div>
                <div class="hero-product-price">₹{{ number_format($featuredProduct->price) }}</div>
            </div>
        </div>
        @endif
    </div>
</section>

{{-- ═══════════════════════════════════════
     TRUST BAR
═══════════════════════════════════════ --}}
<div class="trust-bar">
    <div class="trust-item"><span>🚚</span><div><strong>Free delivery</strong><small>Orders over ₹500</small></div></div>
    <div class="trust-item"><span>🔒</span><div><strong>Secure payment</strong><small>100% protected</small></div></div>
    <div class="trust-item"><span>↩️</span><div><strong>Easy returns</strong><small>7-day policy</small></div></div>
    <div class="trust-item"><span>🤝</span><div><strong>Support artisans</strong><small>Direct from makers</small></div></div>
</div>

{{-- ═══════════════════════════════════════
     CATEGORY FILTER
═══════════════════════════════════════ --}}
<section class="section" id="categories">
    <div class="section-header">
        <div class="section-label">Browse by category</div>
        <h2 class="section-title">Shop by category</h2>
        <p class="section-sub">Find exactly what you're looking for</p>
    </div>

    <div class="categories-grid">
        {{-- "All" button --}}
        <a href="{{ route('home') }}"
           class="category-card {{ !request('category') ? 'category-active' : '' }}">
            <div class="category-emoji">🛍️</div>
            <div class="category-name">All</div>
            <div class="category-count">{{ $products->count() }} items</div>
        </a>

        {{-- Dynamic categories from DB --}}
        @foreach($categories as $category)
            @if($category->products_count > 0)
            <a href="{{ route('home', ['category' => $category->slug]) }}"
               class="category-card {{ request('category') == $category->slug ? 'category-active' : '' }}">
                <div class="category-emoji">{{ $category->emoji }}</div>
                <div class="category-name">{{ $category->name }}</div>
                <div class="category-count">{{ $category->products_count }} items</div>
            </a>
            @endif
        @endforeach
    </div>
</section>

{{-- ═══════════════════════════════════════
     PRODUCTS GRID
═══════════════════════════════════════ --}}
<section class="section" id="products">
    <div class="section-header">
        <div class="section-label">
            {{ request('category') ? $categories->where('slug', request('category'))->first()?->name : 'All products' }}
        </div>
        <h2 class="section-title">
            @if(request('search'))
                Results for "{{ request('search') }}"
            @else
                Featured products
            @endif
        </h2>
        <p class="section-sub">{{ $products->count() }} products found</p>
    </div>

    {{-- Search bar --}}
    <form method="GET" action="{{ route('home') }}" class="search-form">
        @if(request('category'))
            <input type="hidden" name="category" value="{{ request('category') }}">
        @endif
        <input
            type="text"
            name="search"
            value="{{ request('search') }}"
            placeholder="Search products..."
            class="search-input"
        />
        <button type="submit" class="btn btn-primary">Search</button>
        @if(request('search'))
            <a href="{{ route('home', request('category') ? ['category' => request('category')] : []) }}"
               class="btn btn-outline">Clear</a>
        @endif
    </form>

    {{-- Products --}}
    @if($products->isEmpty())
        <div class="empty-state">
            <div style="font-size:48px; margin-bottom:16px;">🔍</div>
            <h3>No products found</h3>
            <p>Try a different category or search term</p>
            <a href="{{ route('home') }}" class="btn btn-primary" style="margin-top:16px;">View all products</a>
        </div>
    @else
        <div class="products-grid">
            @foreach($products as $product)
            <div class="product-card">
                {{-- Product Image / Emoji --}}
                <div class="product-img">
                    @if($product->image)
                        <img src="{{ asset('storage/' . $product->image) }}" alt="{{ $product->name }}"/>
                    @else
                        <span class="product-emoji">{{ $product->emoji }}</span>
                    @endif

                    {{-- Badges --}}
                    @if($product->isOnSale())
                        <span class="badge badge-sale">{{ $product->discountPercent() }}% off</span>
                    @elseif($product->created_at->diffInDays() < 7)
                        <span class="badge badge-new">New</span>
                    @elseif($product->stock < 5 && $product->stock > 0)
                        <span class="badge badge-hot">Low stock</span>
                    @endif

                    {{-- Out of stock overlay --}}
                    @if(!$product->inStock())
                        <div class="out-of-stock-overlay">Out of stock</div>
                    @endif
                </div>

                {{-- Product Info --}}
                <div class="product-body">
                    <div class="product-category">{{ $product->category->name }}</div>
                    <h3 class="product-name">
                        <a href="{{ route('products.show', $product) }}">{{ $product->name }}</a>
                    </h3>
                    <p class="product-desc">{{ Str::limit($product->description, 60) }}</p>

                    <div class="product-footer">
                        <div class="product-price">
                            @if($product->isOnSale())
                                <span class="price-old">₹{{ number_format($product->old_price) }}</span>
                            @endif
                            <span class="price-current">₹{{ number_format($product->price) }}</span>
                        </div>

                        {{-- Add to cart button --}}
                        @auth
                            @if(auth()->user()->isCustomer() && $product->inStock())
                                <button
                                 class="add-to-cart-btn"
                                    data-id="{{ $product->id }}"
                                   onclick="addToCart(this)"
                                   title="Add to cart">
                                    +
                                </button>
                            @endif
                        @else
                            <a href="{{ route('login') }}" class="add-to-cart-btn" title="Login to buy">+</a>
                        @endauth
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    @endif
</section>

@endsection

@push('scripts')
<script>
function addToCart(btn) {
    const productId = btn.getAttribute('data-id');

    // Disable button while request runs
    btn.disabled    = true;
    btn.textContent = '...';

    fetch('{{ route("cart.add") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Accept':       'application/json',
        },
        body: JSON.stringify({ product_id: productId }),
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            // ✅ Green tick feedback on the button
            btn.textContent      = '✓';
            btn.style.background = '#1D9E75';

            // ✅ Update cart badge number in nav
            const badge = document.getElementById('cartBadge');
            if (badge) badge.textContent = data.cart_count;

            // ✅ Show toast notification (defined in app.blade.php)
            if (typeof showToast === 'function') {
                showToast(data.message, '🛒');
            }

            // Reset button after 1.5 seconds
            setTimeout(() => {
                btn.textContent      = '+';
                btn.style.background = '';
                btn.disabled         = false;
            }, 1500);

        } else {
            // ❌ Error — show red ! on button
            btn.textContent      = '!';
            btn.style.background = '#C85A3A';

            if (typeof showToast === 'function') {
                showToast(data.message, '⚠️');
            }

            setTimeout(() => {
                btn.textContent      = '+';
                btn.style.background = '';
                btn.disabled         = false;
            }, 2000);
        }
    })
    .catch(err => {
        // Network error fallback
        console.error('Cart error:', err);
        btn.textContent      = '+';
        btn.style.background = '';
        btn.disabled         = false;
    });
}
</script>
@endpush